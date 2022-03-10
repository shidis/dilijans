<?

class Orders_Mango extends DB
{
    private $tmpPath, $cooPath, $login, $pw, $maxTS;
    public $logged = false;
    public $MCData; // ячейка MongoOffice_Logged MC с оперативными данными и балансом
    public $conf; // Data::get('os_log_calls') - тут храним время последнего звонка снятого в базу звонков сайта
    public $baseUrl = 'https://lk.mango-office.ru';
    private $logFile;
    private $missTimeout;

    /*
     * поля LastResult:
     * content - html ответа
     * url - урл после редиректов
     *
     */
    private $lastResult;

    function __construct($login = '', $pw = '')
    {
        parent::__construct();
        if (empty($login)) $login = Data::get('mangoOfficeLogin');
        if (empty($pw)) $pw = Data::get('mangoOfficePw');
        if (empty($login) || empty($pw)) throw new Exception('conf Off.');
        $this->logFile = Cfg::_get('root_path') . '/assets/logs/mango-parser.log';
        if (!MC::chk())
        {
            $this->log("Constructor: {ERROR} Memcached не работает. Выход.");
            throw new Exception('MC not working');
        }
        $this->tmpPath = Cfg::_get('root_path') . '/tmp/';
        require_once Cfg::_get('root_path') . '/inc/phpQuery/phpQuery.php';
        $this->cooPath = "{$this->tmpPath}cookie_mango.txt";
        $this->login = $login;
        $this->pw = $pw;
        if (($m = MC::get('MongoOffice_Logged' . MC::uid())) !== false)
        {
            $this->MCData = json_decode($m);
            if (!empty($this->MCData->csvUrl_1) && !empty($this->MCData->csvUrl_2))
            {
                $this->logged = true;
                $this->log("Constructor: [IS_LOGGED].");
            }
            else
            {
                MC::del('MongoOffice_Logged' . MC::uid());
                $this->log("Constructor: [NOT_LOGGED].");
            }
        }
        else
        {
            $this->log("Constructor: [NOT_LOGGED].");
        }
        $this->conf = Data::get('os_log_calls');
        if (!empty($this->conf))
        {
            $this->conf = Tools::DB_unserialize($this->conf);
            /*
             * формат $conf:
             * tsLastRun - time() последнего звонка попавшего в базу сайта
             * balance - баланс
             * tsBalance - время опроса баланса
             */
        }
        else
        {
            $this->conf = [];
        }
        $this->missTimeout = (int)Data::get('mangoOffice_missTimeout');
    }

    private function log($msg)
    {
        file_put_contents($this->logFile, Tools::dt() . " - " . $msg . "\n", FILE_APPEND);
    }

    public function q($path, $fileGetting = false)
    {
        if (!$this->logged)
        {
            $notLogged = true;
            $url = "{$this->baseUrl}/stats";
            $postdata = [
                //'request-uri' => '/',
                'auth-type' => 'mo',
                'username'  => $this->login,
                'password'  => $this->pw,
            ];
            $this->log("Query($path, " . (int)$fileGetting . "): [NOT_LOGGED]");
        }
        else
        {
            $url = "{$this->baseUrl}$path";
            $this->log("Query($path, " . (int)$fileGetting . "): [IS_LOGGED]");
        }

        $this->log("Query(): обработка url = $url");

        $ch = curl_init($url);
        // http://php.net/manual/ru/function.curl-setopt.php
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        //curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_CERTINFO, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        if (!empty($postdata))
        {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata));
        }
        curl_setopt($ch, CURLOPT_COOKIEJAR, "{$this->tmpPath}cookie_mango.txt");
        curl_setopt($ch, CURLOPT_COOKIEFILE, "{$this->tmpPath}cookie_mango.txt");

        $content = curl_exec($ch);
        $err = curl_errno($ch);
        $errmsg = curl_error($ch);
        $header = curl_getinfo($ch);
        curl_close($ch);

        $header['errno'] = $err;
        $header['errmsg'] = $errmsg;
        $header['content'] = $content;
        $this->lastResult = $header;
        file_put_contents("{$this->tmpPath}mango_dump.txt", print_r($header, true));
        if ($err)
        {
            $this->log("Query(): [$errmsg] url = {$header['url']}");

            return false;
        }
        elseif ($header['http_code'] != 200)
        {
            $this->log("Query(): [http_code={$header['http_code']}] url = {$header['url']}");

            return false;
        }
        if (strpos($header['content'], 'auth-form') === false)
        {
            $this->logged = true;
            if (!empty($notLogged))
            {
                $this->log("Query(): [LOGGED_OK] url = {$header['url']}");
                // разбираем урл полученный и выделяем переменные нужные
                // Ics.accountId = '300270706';
                preg_match("~Ics.accountId = '([0-9]+)'~iu", $this->lastResult['content'], $m);
                $accountId = @$m[1];
                // Ics.productId = '300287426';
                preg_match("~Ics.productId = '([0-9]+)'~iu", $this->lastResult['content'], $m);
                $productId = @$m[1];
                // $.ics__period2({"minDate":"16.08.2012"});
                //$md = preg_match("~\"minDate\":\"([0-9]+)\.([0-9]+)\.([0-9]+)\"~iu", $this->lastResult['content'], $m);
                //$minDate = @$m[3] . '-' . @$m[2] . '-' . @$m[1];
                $minDate = '2013-09-01';
                if (empty($accountId) || empty($productId))
                {
                    $this->log("Query(): [LOGGED_OK] Ошибка генерации csvUrl  accountId=$accountId, productId=$productId, minDate=" . ($minDate) . ", Query(): url = {$header['url']}");

                    return false;
                }
                // генерим урл
                // "/300270706/300287426/stats/successed-calls/stat.csv?filter-period=since_previous_month&filter-start-date=2014-09-01&filter-end-date=2014-10-06&filter-call-type=3&filter-accountid=300270706&filter-productid=300287426&filter-offset=240&filter-timezoneoffset=240"
                MC::set('MongoOffice_Logged' . MC::uid(), json_encode($this->MCData = (object)[
                    'tsLogged'  => time(),
                    'accountId' => $accountId,
                    'productId' => $productId,
                    'minDate'   => $minDate,
                    'csvUrl_1'  => "/$accountId/$productId/stats/successed-calls/stat.csv?filter-period=arbitrary&filter-start-date=#date1#&filter-end-date=#date2#&filter-call-type=3&filter-accountid=$accountId&filter-productid=$productId&filter-offset=240&filter-timezoneoffset=240",
                    'csvUrl_2'  => "/$accountId/$productId/stats/incomplete-calls/stat.csv?filter-period=arbitrary&filter-start-date=#date1#&filter-end-date=#date2#&filter-call-type=3&filter-accountid=$accountId&filter-productid=$productId&filter-offset=240&filter-timezoneoffset=240",
                ]));

                return $this->q($path, $fileGetting);
            }
            $this->log("Query(): OK");

            return true;
        }
        else
        {
            $this->log("Query(): [NOT_LOGGED_200] url = {$header['url']}");
            MC::del('MongoOffice_Logged' . MC::uid());

            return false;
        }

    }

    public function getInfo()
    {
        if ($this->q('/news'))
        {
            $doc = phpQuery::newDocumentHTML($this->lastResult['content']);
            $s = $doc->find(".account-info .balance .value")->html();
            $b = str_replace(',', '.', preg_replace("~[^0-9,]~u", '', $s));
            $res = [
                'balance' => $b,
                'tsCheck' => $t = time(),
            ];
            $this->conf['balance'] = $b;
            $this->conf['tsBalance'] = $t;
            Data::set('os_log_calls', Tools::DB_serialize($this->conf));
            $this->log("getBalance(): balance=$b");

            return $res;
        }
        else
        {
            $this->log("getBalance(): [ERROR] query() вернул false");

            return false;
        }
    }

    public function getCallHistory()
    {
        if (!$this->logged && !$this->q('/stats')) return false;

        $date2 = date("Y-m-d");

        $res = [];

        // получаем лог успешных звонков (входящие/исходящие, по всем линиям)

        $d = $this->getOne("SELECT count(*) FROM os_log_calls WHERE type=1 OR type=2");

        $this->maxTS = [1 => 0, 2 => 0];

        if (!$d[0])
        {
            // первый запуск. В логах ничего нет.
            $date1 = $this->MCData->minDate;
            $mts = 0;
        }
        else
        {
            // обновление
            $date1 = date("Y-m-d", $this->conf['tsLastRun_1']);
            $d = $this->getOne("SELECT UNIX_TIMESTAMP(MAX(dt)) FROM os_log_calls WHERE type=1");
            $this->maxTS[1] = $d[0];
            $mts = date("d-m-Y H:i:s", $d[0]);
            $d = $this->getOne("SELECT UNIX_TIMESTAMP(MAX(dt)) FROM os_log_calls WHERE type=2");
            $this->maxTS[2] = $d[0];
        }

        $this->log("getCallHistory(Successed_Calls): $date1 -> $date2, maxDT[1] = $mts");

        $csvUrl = $this->MCData->csvUrl_1;
        $csvUrl = str_replace('#date1#', $date1, $csvUrl);
        $csvUrl = str_replace('#date2#', $date2, $csvUrl);
        if ($this->q($csvUrl, true))
        {

            $this->conf['tsLastRun_1'] = time();
            Data::set('os_log_calls', Tools::DB_serialize($this->conf));

            // парсим csv
            $res['successedCalls'] = $this->parseCSV(0);

        }
        else
        {
            $this->log("getCallHistory(Successed_Calls): [ERROR] query() вернул false");

            return false;
        }


        // получаем лог пропущенных звонков (входящие/исходящие, по всем линиям)
        $d = $this->getOne("SELECT count(*) FROM os_log_calls WHERE type=11 OR type=12");

        $this->maxTS = [11 => 0, 12 => 0];

        if (!$d[0])
        {
            // первый запуск. В логах ничего нет.
            $date1 = $this->MCData->minDate;
            $mts = 0;
        }
        else
        {
            // обновление
            $date1 = date("Y-m-d", $this->conf['tsLastRun_2']);
            $d = $this->getOne("SELECT UNIX_TIMESTAMP(MAX(dt)) FROM os_log_calls WHERE type=11");
            $this->maxTS[11] = $d[0];
            $mts = date("d-m-Y H:i:s", $d[0]);
            $d = $this->getOne("SELECT UNIX_TIMESTAMP(MAX(dt)) FROM os_log_calls WHERE type=12");
            $this->maxTS[12] = $d[0];
        }

        $this->log("getCallHistory(Missed_Calls): $date1 -> $date2, maxDT[11] = $mts");

        $csvUrl = $this->MCData->csvUrl_2;
        $csvUrl = str_replace('#date1#', $date1, $csvUrl);
        $csvUrl = str_replace('#date2#', $date2, $csvUrl);
        if ($this->q($csvUrl, true))
        {

            $this->conf['tsLastRun_2'] = time();
            Data::set('os_log_calls', Tools::DB_serialize($this->conf));

            // парсим csv
            $res['missedCalls'] = $this->parseCSV(10);

        }
        else
        {
            $this->log("getCallHistory(Missed_Calls): [ERROR] query() вернул false");

            return false;
        }

        return $res;
    }

    private function parseCSV($typeInc = 0)
    {

        $months = [
            'янв.' => '01',
            'фев.' => '02',
            'мар.' => '03',
            'апр.' => '04',
            'май.' => '05',
            'июн.' => '06',
            'июл.' => '07',
            'авг.' => '08',
            'сен.' => '09',
            'окт.' => '10',
            'ноя.' => '11',
            'дек.' => '12',
        ];

        $buf = explode("\n", $this->lastResult['content']);
        $i = 0;
        $inserted = 0;
        $maxTS = 0;
        $ts0 = time();
        //Дата;Тип;Сотрудник;"От кого";Кому;Длительность
        foreach ($buf as $v)
        {
            if (mb_strpos($v, ';') !== false)
            {
                $i++;
                $row = Tools::utf($v);
                $a = str_getcsv($row, ';', '"', '\\');
                if ($i == 1)
                {
                    if ($a[0] != 'Дата' || $a[1] != 'Тип' || $a[2] != 'Сотрудник' || $a[3] != 'От кого' || $a[4] != 'Кому' || $a[5] != 'Длительность')
                    {
                        $this->log("parseCSV($typeInc): [ERROR] строка $i: состав колонок в таблице неверное, возможно в диапазоне нет ни одного звонка. Останов");

                        return false;
                    }
                    continue;
                }
                if (count($a) != 6)
                {
                    $this->log("parseCSV($typeInc): [ERROR] строка $i: количество колонок в записи неверное - пропускаю. Строка /$row/");
                    continue;
                }
                $r = [
                    'dt_added' => Tools::dt(),
                ];

                if (false === mb_strpos($a[1], 'сходящий')) $r['type'] = $typeInc + 1;
                else $r['type'] = $typeInc + 2;
                $r['co'] = Tools::esc(@$a[2]);
                $r['source'] = Tools::esc(@$a[3]);
                $r['dest'] = Tools::esc(@$a[4]);
                $r['duration'] = Tools::esc(@$a[5]);


                //"05 окт. 2014 19:06:16"
                if (preg_match("~([0-9]+) ([^\s]+) ([0-9]+) ([0-9]+):([0-9]+):([0-9]+)~u", $a[0], $m))
                {
                    $r['dt'] = "{$m[3]}-{$months[$m[2]]}-{$m[1]} {$m[4]}:{$m[5]}:{$m[6]}";
                    // mktime ($hour = null, $minute = null, $second = null, $month = null, $day = null, $year = null, $is_dst = null) {}
                    $ts = mktime($m[4], $m[5], $m[6], $months[$m[2]], $m[1], $m[3]);
                    // echo "ts= {$r['dt']} = $ts must be >  {$this->maxTS} = ".date("d-m-Y H:i:s",$this->maxTS)."<br>";
                    if ($ts <= $this->maxTS[$r['type']] || ($dts = $ts0 - $ts) < $this->missTimeout)
                    {
                        //if($dts < $this->missTimeout) $this->log("parseCSV($typeInc): dTS= ($ts0 - $ts = $dts) < {$this->missTimeout} -> [SKIPPED] row[$i]:\t\t /$row/");
                        continue;
                    }

                    $this->insert('os_log_calls', $r);
                    $inserted++;
                    $this->log("parseCSV($typeInc): row[$i]:\t\t /$row/");

                }
                else
                {
                    $this->log("parseCSV($typeInc): [ERROR] строка $i: поле даты /{$a[0]}/ не распознано - пропускаю. Строка /$row/");
                    continue;
                }


            }
        }


        $this->log("parseCSV($typeInc): [Успешно] добавлено $inserted звонков в БД");

        return [
            'inserted' => $inserted,
            'csvRows'  => $i,
        ];
    }

    /*
     * сюда пускаем телефон из заказов
     * убирает все кроме цифр
     * возвращает несколько написаний телефонов в иде массива:
     * только с убранными нецифрами
     * с приведенной семеркой вначале
     * без семерки вначале - номер неизменный
     * восьмерка вначале заменена на 7ку
     */
    public function parseTelNumber($tel)
    {
        $s = trim(preg_replace("~[^0-9]~u", '', trim($tel)));
        if (empty($s)) return [];
        $res = [$s];
        if ($s{0} == '8')
        {
            $_s = $s;
            $_s{0} = 7;
            $res[] = $_s;
        }
        else
        {
            $res[] = "8{$s}";
        }
        if ($s{0} != '7') $res[] = "7{$s}";

        return $res;
    }

    /*
     * получаем лог звонков для type=1 source=tel для заказа
     */
    public function orderCallLog($order_id)
    {
        $order_id = (int)$order_id;
        if (empty($order_id)) return [];
        $telf = [];
        if (isset(App_TFields::$fields['os_order']['tel1'])) $telf[] = App_TFields::$fields['os_order']['tel1']['as'];
        if (isset(App_TFields::$fields['os_order']['tel2'])) $telf[] = App_TFields::$fields['os_order']['tel2']['as'];
        if (isset(App_TFields::$fields['os_order']['SMSTel'])) $telf[] = App_TFields::$fields['os_order']['SMSTel']['as'];
        if (empty($telf)) return [];
        $telf = implode(', ', $telf);
        $d = $this->getOne("SELECT dt_add, UNIX_TIMESTAMP(dt_add) AS dt_add_ts, $telf FROM os_order WHERE order_id=$order_id", MYSQL_ASSOC);
        if ($d === 0) return [];
        $tels = [];
        foreach ($d as $k => $v)
        {
            if ($k != 'dt_add' && $k != 'dt_add_ts' && trim($v) != '') $tels += $this->parseTelNumber(Tools::esc($v));
        }
        foreach ($tels as $k => $v)
        {
            $v = Tools::esc($v);
            $tels[$k] = "'$v'";
        }
        $tels = implode(',', $tels);
        $d1 = $this->fetchAll("SELECT *, UNIX_TIMESTAMP(dt) AS dt_ts  FROM os_log_calls WHERE source IN ($tels) OR dest IN ($tels) ORDER BY dt ASC", MYSQL_ASSOC);
        foreach ($d1 as $k => $v)
        {
            $d1[$k]['dt'] = Tools::sDateTime($v['dt']);
            $d1[$k]['deltaDTAddMinutes'] = ceil(($v['dt_ts'] - $d['dt_add_ts']) / 60);
            if ($d1[$k]['type'] == 11 || $d1[$k]['type'] == 1 && $d1[$k]['co'] == $d1[$k]['dest']) $d1[$k]['_type'] = 'вх. пропущ.';
            elseif ($d1[$k]['type'] == 12) $d1[$k]['_type'] = 'исх. пропущ.';
            elseif ($d1[$k]['type'] == 1 && $d1[$k]['co'] != $d1[$k]['dest']) $d1[$k]['_type'] = 'вход.';
            elseif ($d1[$k]['type'] == 2) $d1[$k]['_type'] = 'исх.';
        }

        return ['data' => $d1, 'tels' => $tels, 'd' => $d];
    }
}