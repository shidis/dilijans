<?
class Geo extends DB
{
    public $_db; // geo_ip dbase
    public $quiet;
    public $mainCityId=-77;
    public $mainCityName='Москва';


    function __construct($quiet = false)
    {
        parent::__construct();

        try {
            require_once(Cfg::get('root_path') . '/config/geo.php');
            $this->quiet = $quiet;
            $this->_db = new DB();
            $this->_db->setCharset('utf8');
            $this->_db->set_db(GEOBASE_HOST, GEOBASE_DB, GEOBASE_USER, GEOBASE_PW);
            if (!$this->_db->sql_connect()) {
                if ($quiet) return Msg::put(false, "Не возможно подключиться к геобазе.");
                else  die('GEO_DB connection fault');
            }

        } catch (DBException $e) {
            if (!$quiet) $e->getError(); else return Msg::put(false, "[Geo]: Не возможно выбрать геобазу.");
        }
    }

    function resolveWithMapsTable($ip)
    {
        if (!preg_match("/^([0-9]+)\.([0-9]+)\.([0-9]+)\.([0-9]+)$/u", $ip)) {
            return false;
        }

        try {

            $r = array(
                'mm_country_code' => '',
                'mm_region' => '',
                'mm_city' => '',
                'gip_country_code' => '',
                'gip_region' => '',
                'gip_city' => '',
                'sx_country_code'=>'',
                'sx_region'=>'',
                'sx_city'=>'',
                'cityId' => 0,
                'cityName'=> ''
            );

            /* пробиваем по SxGEO  */
            include_once Cfg::get('root_path') . "/inc/geoIP/SxGeo.php";

            $SxGeo=@new SxGeo(Cfg::get('root_path') . "/inc/geoIP/data/SxGeoCity.dat", SXGEO_FILE);
            $d=false;
            $d=@$SxGeo->getCityFull($ip); // возвращает полную информацию о городе и регионе
            if($d===false) {
                $err="[Geo.SxGeo.getCityFull]: ошибка";
                if (!$this->quiet) echo $err; else return Msg::put(false, $err);
            }
            //print_r($d);
            //$city = $SxGeo->get($ip); // выполняет getCountry либо getCity в зависимости от типа базы
            //print_r($city);
            $r['sx_country_code']=@$d['country']['iso'];
            $r['sx_region']=@$d['region']['name_ru'];
            $r['sx_city']=@$d['city']['name_ru'];
            //$r['sxData']=$d;
            // находим город транспортной компании
            if(!empty($r['sx_city'])){
                if(Tools::tolow($r['sx_city']) == Tools::tolow($this->mainCityName)) {
                    $r['cityId'] = $this->mainCityId;
                    $r['cityName'] = $this->mainCityName;
                }else{
                    $city=Tools::esc($r['sx_city']);
                    $d = $this->getOne("SELECT city_id FROM tc_city WHERE name LIKE '{$city}' LIMIT 1");
                    if ($d !== 0) {
                        $r['cityId'] = $d[0];
                        $r['cityName'] = $city;
                    }
                }
            }
            unset($SxGeo);


//            $long_ip = ip2long($ip);
//            if ($long_ip < 0) $long_ip += pow(2,32);

            // пробиваем по русской базе geoip_*
            $d = $this->_db->getOne("SELECT * FROM geoip_base JOIN geoip_cities USING (city_id) WHERE INET_ATON('$ip')>=_ip1 AND INET_ATON('$ip')<=_ip2 LIMIT 1");
            if ($d !== 0) {
                $r['gip_country_code'] = $d['country'];
                $r['gip_region'] = trim(Tools::unesc($d['region']));
                $r['gip_city'] = trim(Tools::unesc($d['city']));

                // находим город транспортной компании
                if(empty($r['cityId']) && !empty($r['gip_city'])){
                    if(Tools::tolow($r['gip_city']) == Tools::tolow($this->mainCityName)) {
                        $r['cityId'] = $this->mainCityId;
                        $r['cityName'] = $this->mainCityName;
                    }else{
                        $city=Tools::esc($r['gip_city']);
                        $d = $this->getOne("SELECT city_id FROM tc_city WHERE name LIKE '{$city}' LIMIT 1");
                        if ($d !== 0) {
                            $r['cityId'] = $d[0];
                            $r['cityName'] = $city;
                        }
                    }
                }
            }

            // пробиваем по MaxMind
            include_once Cfg::get('root_path') . "/inc/geoIP/geoipcity.inc";
            include_once Cfg::get('root_path') . "/inc/geoIP/geoipregionvars.php";

            if (!function_exists('geoip_open')) {
                if ($this->quiet)
                    Msg::put(false, '[Geo.resolveWithMapsTable]: geoip_open() not exist');
                else {
                    echo "[Geo.resolveWithMapsTable]: geoip_open() not exist";
                    return false;
                }
            }

            $gi = geoip_open(Cfg::get('root_path') . "/inc/geoIP/data/GeoLiteCity.dat", GEOIP_STANDARD);

            $record = geoip_record_by_addr($gi, $ip);

            if (!empty($record)) {
                $region = iconv('ISO-8859-1', 'utf-8', @$GEOIP_REGION_NAME[$record->country_code][$record->region]);
                $r['mm_country_code'] = $record->country_code === 0 || $record->country_code == NULL ? '' : $record->country_code;
                $r['mm_region'] = $region === 0 || $region == NULL ? '' : $region;
                $r['mm_city'] = $record->city === 0 || $record->city == NULL ? '' : iconv('ISO-8859-1', 'utf-8', $record->city);
            }

            geoip_close($gi);



        } catch (DBException $e) {
            if (!$this->quiet) $e->getError(); else Msg::put(false, '[Geo.resolveWithMapsTable]:Ошибка БД');
            return false;
        }
        if (!$this->quiet) {
            Stat::finish();
            echo Stat::execTime();
        }

        return $r;

    }

    function checkMapsTable()
    {
        try {
            echo "<br>Проверка соответствия базы городов на сайте с базой RIPE geoIP:<br>";
            $maps = $this->fetchAll("SELECT city FROM maps ORDER BY city", MYSQLI_ASSOC);
            $this->sql_close();
            $this->_db->sql_close();
            foreach ($maps as $v) {
                $d = $this->_db->fetchAll("SELECT city, region FROM geoip_cities WHERE city LIKE '{$v['city']}'");
                $s = array();
                foreach ($d as $vv) $s[] = $vv['region'] . ' ' . $vv['city'];
                $s = implode(', ', $s);
                echo "{$v['city']} -> " . $s;
                if (empty($s)) echo '<b>НЕТ СООТВЕТСТВИЯ</b>';
                echo '<br>';
            }
            echo 'DONE';
        } catch (DBException $e) {
            $e->getError();
        }
    }

    //импорт базы MaxMind. НЕ ЗАБЫТЬ ОБНОВИТЬ ФАЙЛ  /inc/geoIP/geoipregionvars.php
    function importMMLiteV4Base()
    {
        if (($handle = fopen("base/GeoLiteCity-Blocks.csv", "r")) !== FALSE) {
            echo "Импорт City-Blocks ...";
            Tools::flu();

            $this->_db->query("TRUNCATE TABLE `mml_blocks`");
            $i = 0;
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if ($data[0] != 0) {
                    $i++;
                    //startIpNum,endIpNum,locId
                    $_ip1 = $data[0];
                    $_ip2 = $data[1];
                    $ip1 = long2ip($data[0]);
                    $ip2 = long2ip($data[1]);
                    $locId = $data[2];
                    $this->_db->query("INSERT INTO mml_blocks (_ip1,_ip2,ip1,ip2,locId) VALUES('$_ip1','$_ip2','$ip1','$ip2','$locId')");
                    if (!($i % 1000)) {
                        echo '. ';
                        Tools::flu();
                    }
                }
            }
            fclose($handle);
            echo "$i OK<br>";
        }

        if (($handle = fopen("base/GeoLiteCity-Location.csv", "r")) !== FALSE) {
            echo "Импорт City-Location ...";
            Tools::flu();

            $this->_db->query("TRUNCATE TABLE `mml_locs`");

            $i = 0;
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if ($data[0] != 0) {
                    $i++;
                    //locId,country,region,city,postalCode,latitude,longitude,metroCode,areaCode
                    $locId = $data[0];
                    $country = addslashes(iconv('ISO-8859-1', 'utf-8', $data[1]));
                    $region = addslashes(iconv('ISO-8859-1', 'utf-8', $data[2]));
                    $city = addslashes(iconv('ISO-8859-1', 'utf-8', $data[3]));
                    $postalCode = addslashes($data[4]);
                    $lat = $data[5];
                    $lng = $data[6];
                    $this->_db->query("INSERT INTO mml_locs (locId,country,region,city,postalCode,lat,lng) VALUES('$locId','$country','$region','$city','$postalCode','$lat','$lng')");
                    if (!($i % 1000)) {
                        echo '. ';
                        Tools::flu();
                    }
                }
            }
            fclose($handle);
            echo "$i OK<br>";
        }

    }

    // импорт русской базы
    function importGeoIPBase()
    {
        echo "Запускаемся...<br>";
        Tools::flu();
        // проверяем наличие файла cities.txt в папке рядом с этим скриптом
        echo "Импорт городов...";
        Tools::flu();
        $i = 0;
        if (file_exists('base/cities.txt')) {
            $this->_db->query("TRUNCATE TABLE `geoip_cities`"); // очищаем таблицу перед импортом актуальных данных
            $file = file('base/cities.txt');
            $pattern = '#(\d+)\s+(.*?)\t+(.*?)\t+(.*?)\t+(.*?)\s+(.*)#';
            foreach ($file as $row) {
                $row = iconv('windows-1251', 'utf-8', $row);
                if (preg_match($pattern, $row, $out)) {
                    $this->_db->query("INSERT INTO `geoip_cities` (`city_id`, `city`, `region`, `district`, `lat`, `lng`, `coords`) VALUES('$out[1]', '$out[2]', '$out[3]', '$out[4]', '$out[5]', '$out[6]', POINT('$out[5]','$out[6]'))");
                    $i++;
                }
            }
        } else {
            die('Ошибка! Файл cities.txt не найден');
        }
        echo "$i OK<br>";

        echo "Импорт базы RIPE...";
        Tools::flu();

        // проверяем наличие файла cidr_optim.txt в папке рядом с этим скриптом
        if (file_exists('base/cidr_optim.txt')) {
            $this->_db->query("TRUNCATE TABLE `geoip_base`"); // очищаем таблицу перед импортом актуальных данных

            $file = file('base/cidr_optim.txt');
            $pattern = '#(\d+)\s+(\d+)\s+(\d+\.\d+\.\d+\.\d+)\s+-\s+(\d+\.\d+\.\d+\.\d+)\s+(\w+)\s+(\d+|-)#';
            $i++;
            foreach ($file as $row) {
                if (preg_match($pattern, $row, $out)) {
                    $this->_db->query("INSERT INTO `geoip_base` (`ip1`, `ip2`, `_ip1`, `_ip2`,  `country`, `city_id`) VALUES('$out[3]', '$out[4]', INET_ATON('{$out[3]}'), INET_ATON('{$out[4]}'), '$out[5]', '$out[6]')");
                    $i++;
                    if (!($i % 1000)) {
                        echo '. ';
                        Tools::flu();
                    }
                }
            }
        } else {
            die('Ошибка! Файл cidr_optim.txt не найден');
        }
        echo "$i OK<br>";
        echo 'DONE';

    }

    public function importFIAS()
    {
        // открываем базу в режиме чтения
        $db = dbase_open('base/fias.dbf', 0);

        if ($db) {
            $record_numbers = dbase_numrecords($db);
            for ($i = 1; $i <= $record_numbers; $i++) {
                $row = dbase_get_record_with_names($db, $i);
                if ($row['ismember'] == 1) {
                    echo "Member #$i: " . trim($row['name']) . "\n";
                }
            }
        }
// Прим. пер. -
// к полученным с помощью dbase_get_record_with_names значениям записи
// обращаемся по имени - $row['ismember'],
// а в случае с dbase_get_record к значениям записи
// обращаемся по номеру - $row[4]
    }

}