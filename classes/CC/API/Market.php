<?

class CC_API_Market extends DB
{
    public $accLogin, $accPW, $appID, $appPW, $authDataPass=false;
    public $lastHeader, $lastBody, $tmpPath, $todayD;
    public $campaignId = 0, $feedId = 0, $regionId=213;
    public $logs = [];


    // как есть ришется в MC::YandexMarketOpt
    public $opt = [
        'token_type' => '',
        'access_token' => '',
        'expires_in' => 0,
        'uid' => 0,
        'expires_at'=>0,
        'expires_dt'=>'',
        'campaignOffersTotal'=>[],
        'campaignZeroModelIdOffers'=>[],
        'queries'=>[],
        'balance'=>[
            'v'=>'',
            'daysLeft'=>'',
            'dtCheck'=>''
        ],
        'limitsExceeded'=>false
    ];

    function __construct()
    {
        parent::__construct();
        $this->tmpPath = Cfg::_get('root_path') . '/tmp/';
        $this->logFile = Cfg::_get('root_path') . '/assets/logs/ym-parser.log';
        $this->todayD=date("d-m-Y");

        if (!MC::chk()) {
            $this->log("Constructor: {ERROR} Memcached не работает. Выход.");
            throw new Exception('MC not working');
        }
        $this->optInit();
    }

    function optInit()
    {
        $opt = MC::sget('YandexMarketOpt');
        if (!empty($opt)) $this->opt = array_merge($this->opt,$opt);

        if(!isset($this->opt['queries'][$this->todayD])) {
            if(empty($this->opt['queries']) || !is_array($this->opt['queries'])) $this->opt['queries']=[];
            $this->opt['queries'][$this->todayD]=0;
        }
        if(!isset($this->opt['campaignOffersTotal'][$this->todayD])) {
            if(empty($this->opt['campaignOffersTotal']) || !is_array($this->opt['campaignOffersTotal'])) $this->opt['campaignOffersTotal']=[];
            $this->opt['campaignOffersTotal'][$this->todayD]='';
        }
        if(!isset($this->opt['campaignZeroModelIdOffers'][$this->todayD])) {
            if(empty($this->opt['campaignZeroModelIdOffers']) || !is_array($this->opt['campaignZeroModelIdOffers'])) $this->opt['campaignZeroModelIdOffers']=[];
            $this->opt['campaignZeroModelIdOffers'][$this->todayD]='';
        }
    }

    function setAuthData($data = [])
    {
        if (empty($data)) {
            $this->accLogin = Data::get('yandex_login');
            $this->accPW = Data::get('yandex_pw');
            $this->appID = Data::get('yandex_market_id');
            $this->appPW = Data::get('yandex_market_pw');
            $this->campaignId = Data::get('yandex_market_campaign');
            $this->feedId = Data::get('yandex_market_feed');
            $this->regionId = Data::get('yandex_market_regionId');
        }

        if(!empty($this->accLogin) && !empty($this->accPW) && !empty($this->appID) && !empty($this->campaignId)) $this->authDataPass=true;

        return $this;
    }

    function saveOpt()
    {
        MC::sset('YandexMarketOpt', $this->opt);
    }

    function q($url, $postdata = [])
    {
        $ch = curl_init($url);
        // http://php.net/manual/ru/function.curl-setopt.php
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        //curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_CERTINFO, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);

        $httpheader=[];

        /*
         * p[ostdata: или строка или массив, если строка то добавляем Content-Type: application/json
         */
        if (!empty($postdata)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($postdata)?http_build_query($postdata):$postdata);

            if(!is_array($postdata)) $httpheader[]='Content-Type: application/json';
        }


        if (!empty($this->opt['access_token'])) {

            $httpheader[]="Authorization: OAuth oauth_token=\"{$this->opt['access_token']}\", oauth_client_id=\"{$this->appID}\", oauth_login=\"{$this->accLogin}\"";

        }

        if(!empty($httpheader)) curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);

        /*
        curl_setopt($ch, CURLOPT_COOKIEJAR, "{$this->tmpPath}cookie_mango.txt");
        curl_setopt($ch, CURLOPT_COOKIEFILE, "{$this->tmpPath}cookie_mango.txt");
*/
        $content = curl_exec($ch);
        $err = curl_errno($ch);
        $errmsg = curl_error($ch);
        $header = curl_getinfo($ch);
        curl_close($ch);

        $this->lastHeader = [
            'errno' => $err,
            'errmsg' => $errmsg,
            'HEADER' => $header
        ];
        $this->lastBody = $content;

        if ($err) {
            $this->log("Query() ERROR: ($err) $errmsg [{$header['url']}]");
            return false;
        } elseif ($header['http_code'] != 200) {
            $this->log("Query() ERORR: code={$header['http_code']}, [{$header['url']}]");
            return false;
        }

        return true;
    }

    function getToken()
    {
        $url = "https://oauth.yandex.ru/token";
        $postdata = [
            'grant_type' => 'password',
            'username' => $this->accLogin,
            'password' => $this->accPW,
            'client_id' => $this->appID,
            'client_secret' => $this->appPW
        ];

        if ($this->q($url, $postdata)) {
            $this->opt = array_merge($this->opt, json_decode($this->lastBody, true));
            $this->opt['expires_at'] = time() + $this->opt['expires_in'];
            $this->opt['expires_dt'] = date("Y-m-d H:i:s", $this->opt['expires_at']);
            $this->saveOpt();

            return true;
        }

        return false;
    }

    function checkToken()
    {
        if (!empty($this->opt['access_token']) && time() > $this->opt['expires_at'] || empty($this->opt['access_token'])) {
            return $this->getToken();
        }

        return true;
    }

    function log($msg)
    {
        $this->logs[] = $msg;
        file_put_contents($this->logFile, Tools::dt() . " - " . $msg . "\n", FILE_APPEND);
    }

    function getCampaigns()
    {

        if ($this->q("https://api.partner.market.yandex.ru/v2/campaigns.json")) {
            return json_decode($this->lastBody, true);
        }

        return false;
    }

    function getCompaignCats($campaignId)
    {
        if ($this->q("https://api.partner.market.yandex.ru/v2/campaigns/$campaignId/feeds/categories.json")) {
            return json_decode($this->lastBody, true);
        }

        return false;
    }

    function getFeeds($campaignId)
    {
        if ($this->q("https://api.partner.market.yandex.ru/v2/campaigns/$campaignId/feeds.json")) {
            return json_decode($this->lastBody, true);
        }

        return false;
    }

    function getRegions($name)
    {

        if ($this->q("https://api.partner.market.yandex.ru/v2/regions.json?name=".$name)) {
            return json_decode($this->lastBody, true);
        }

        return false;
    }

    function getBalance($campaignId='')
    {
        if(empty($campaignId)) $campaignId=$this->campaignId;
        if ($this->q("https://api.partner.market.yandex.ru/v2/campaigns/$campaignId/balance.json")) {
            $r=json_decode($this->lastBody);
            $this->opt=array_merge($this->opt, ['balance'=>[
                'v'=>$r->balance->balance,
                'daysLeft'=>$r->balance->daysLeft,
                'dtCheck'=>Tools::dt()
            ]]);
            $this->saveOpt();
            return $r;
        }

        return false;
    }

    /*
     * свободный поиск (без привязки к компании по)
     * query
     * page  max 50
     * regionId
     *
     * strict  def==1 - искать в кавычках
     */
    function searchModifs($r)
    {
        // http://api.yandex.com/market/partner/doc/dg/reference/get-models.xml

        if(empty($r['currency'])) $r['currency']='RUR';
        if(empty($r['pageSize'])) $r['pageSize']=100;
        if(empty($r['page'])) $r['page']=1;
        if(!isset($r['regionId'])) $r['regionId']=$this->regionId;

        if(!isset($r['strict'])) $strict=1; else $strict=$r['strict'];
        unset($r['strict']);

        if($strict && !empty($r['query'])) $r['query']="\"{$r['query']}\"";

        if ($this->q("https://api.partner.market.yandex.ru/v2/models.json?".http_build_query($r))) {
            $this->opt['queries'][$this->todayD]++;
            $this->saveOpt();
            return json_decode($this->lastBody, true);
        }

        return false;
    }

    /*
     *  поиск внутри компании
     * query
     * pageSize   max 1000 offers
     * page   max 50
     * regionId
     * campaignId    req
     * shopCategoryId
     * matched
     *
     * strict def==1 - искать в кавычках
     */
    /*
     * на выходе
     *  (
                    [feedId] => 358212
                    [id] => 423127  - наш id
                    [modelId] => 10976753
                    [price] => 23400
                    [currency] => RUR
                    [bid] => 0.1
                    [cbid] => 0.1
                    [url] => http://www.megatrack.ru/diski/american-racing/ar890/8.5-j-20-5-127-et35-d78.1-chrome?utm_source=ym&utm_medium=cpc&utm_campaign=general&utm_content=disks
                    [name] => Диски колесные American Racing AR890 8,5x20 5x127 ET35 Dia 78,1
                    [shopCategoryId] => 42
                )
     */
    function searchCampaignOffers($r=[])
    {
        // http://api.yandex.com/market/partner/doc/dg/reference/get-campaigns-id-offers.xml

        if(empty($r['currency'])) $r['currency']='RUR';
        if(empty($r['pageSize'])) $r['pageSize']=1000;
        if(empty($r['page'])) $r['page']=1;
        if(!isset($r['regionId'])) $r['regionId']=$this->regionId;
        if(empty($r['campaignId'])) $campaignId=$this->campaignId; else $campaignId=$r['campaignId'];
        unset($r['campaignId']);
        if(!empty($r['shopCategoryId'])){
            if(!isset($r['feedId'])) $r['feedId']=$this->feedId;
        }else{
            unset($r['feedId']);
        }
        if(!isset($r['strict'])) $strict=1; else $strict=$r['strict'];
        unset($r['strict']);

        if($strict && !empty($r['query'])) $r['query']="\"{$r['query']}\"";

        if ($this->q("https://api.partner.market.yandex.ru/v2/campaigns/{$campaignId}/offers.json?".http_build_query($r))) {
            $this->opt['queries'][$this->todayD]++;
            $this->saveOpt();
            return json_decode($this->lastBody, true);
        }

        return false;
    }

    /*
     * postdata.modifs - max 100
     * выдает только 10 офферов для товара
     */
    function getOffersForModifs($r,$modifs)
    {

        if(empty($r['currency'])) $r['currency']='RUR';
        if(!isset($r['regionId'])) $r['regionId']=$this->regionId;

        $postdata=[
            'models'=>$modifs
        ];

        if ($this->q("https://api.partner.market.yandex.ru/v2/models/offers.json?".http_build_query($r), json_encode($postdata))) {
            $this->opt['queries'][$this->todayD]++;
            $this->saveOpt();
            return json_decode($this->lastBody, true);
        }

        return false;
    }

    function getInfoForModifs($r,$modifs)
    {

        if(empty($r['currency'])) $r['currency']='RUR';
        if(!isset($r['regionId'])) $r['regionId']=$this->regionId;

        $postdata=[
            'models'=>$modifs
        ];

        if ($this->q("https://api.partner.market.yandex.ru/v2/models.json?".http_build_query($r), json_encode($postdata))) {
            $this->opt['queries'][$this->todayD]++;
            $this->saveOpt();
            return json_decode($this->lastBody, true);
        }

        return false;
    }

    function task()
    {
        $this->log("task(): запуск...");
        if(!$this->setAuthData()->checkToken()) return false;

        if(!empty($this->opt['access_token']) && $this->opt['limitsExceeded']) {
            $this->log("task() ERROR: limitsExceeded");
            return false;
        }

        /*
         * для принудительного запуска процесса обновления ставим MC::YandexMarketRunUpdate=1
         * иначе регулярная проверка: если max(ym_cat.dtCheck) != сегодня. Т.е повторное снятие ассортимента запускаем каждыые 24 часа по одному разу.
         */

        // товары компании имеют высший приоритет - делаем сначала их
        $runUpdate=false;
        if(!($mcv=MC::sget('YandexMarketRunCampaignUpdate'))){

            $d=$this->getOne("SELECT count(*), UNIX_TIMESTAMP(MAX(dtCheck)), UNIX_TIMESTAMP(MIN(dtCheck)) FROM ym_cat WHERE campaign='1'");
            if($d!==0){
                $lastDTCheck=$d[1];
                if($lastDTCheck!=0 &&  $lastDTCheck < (time()-24*60*60)) {
                    $this->log("task(campaign): Последняя проверка ym_cat была ".date("Y-m-d H:i:s", $lastDTCheck)." -> требуется обновление ассортимента");
                    $runUpdate=1;
                }
                if($d[0]==0){
                    $this->log("task(campaign): Пустая таблица ym_cat -> требуется обновление ассортимента");
                    $runUpdate=3;
                }
            }
        }elseif($mcv==1) {
            $this->log("task(): Получен сигнал MC::YandexMarketRunUpdate -> требуется обновление");
            $runUpdate=2;
        }

        if($runUpdate){
            $zeroModelIds=0;

            $this->log("task(campaign): Загрузка ассортимента компании в ym_cat...");

            // если маркет отдаст ошибку далее, весь ассортимент будет с NE==1
            $this->update('ym_cat', ['NE'=>'1'], "campaign='1'");

            $page=0;
            do {

                /*
                 * pageSize     max 1000 offers
                 * page         max 50
                 */
                $iter=0;
                do {
                    $iter++;
                    $res = $this->searchCampaignOffers(['page'=>$page]);
                    if(!$res && $iter<3){
                        $this->log("task(campaign): таймаут и попробуем еще раз ($iter)");
                        sleep(5);
                    }
                } while (!$res && $iter<3);

                if (!$res) return false;

                $this->log("task(searchCampaignOffers): total={$res['pager']['total']}, currentPage={$res['pager']['currentPage']}, pagesCount={$res['pager']['pagesCount']}, ");

                if(!empty($res['offers'])){
                    foreach ($res['offers'] as $offer_k=>$offer_v) {

                        if($this->feedId != $offer_v['feedId']){
                            $this->log("task(): feedId != {$this->feedId} -> пропускаю ".http_build_query($offer_v));
                            continue;
                        }
                        if($offer_v['modelId']==0) $zeroModelIds++;

                        $d=$this->getOne("SELECT id FROM ym_cat WHERE cat_id='{$offer_v['id']}'");

                        $row=[
                            'cat_id'=>$offer_v['id'],
                            'modelId'=>$offer_v['modelId'],
                            'price'=>$offer_v['price'],
                            'bid'=>$offer_v['bid'],
                            'cbid'=>$offer_v['cbid'],
                            'url'=>Tools::esc($offer_v['url']),
                            'name'=>Tools::esc($offer_v['name']),
                            'shopCategoryId'=>$offer_v['shopCategoryId'],
                            'NE'=>'0',
                            'E'=>'0', // убираем статус ошибки
                            'dtAdd'=>$dt=Tools::dt(),
                            'inQueue'=>'1',
                            'dtInQueue'=>$dt,
                            'campaign'=>'1'
                        ];

                        if($d===0){
                            //добавляем
                            $row1=$row;
                            $this->insert('ym_cat', $row);
                            $this->log("task(campaign): Добавлен в ym_cat: cat_id={$offer_v['id']}, modelId={$offer_v['modelId']}, name={$offer_v['name']}");
                        }else{
                            // обновление
                            unset($row['cat_id'], $row['dtAdd']);

                            if($offer_v['modelId']==0 && $row['modelId']!=0) unset($row['modelId']);

                            $this->update('ym_cat', $row, "id={$d['id']}");
                            $this->log("task(campaign): Обновлен ym_cat[id={$d['id']}]: cat_id={$offer_v['id']}, modelId={$offer_v['modelId']}, name={$offer_v['name']}");
                        }
                    }
                }


                $page++;

            } while(@$res['pager']['pagesCount'] > @$res['pager']['currentPage']);

            $this->opt['campaignOffersTotal'][$this->todayD]=$res['pager']['total'];
            $this->opt['campaignZeroModelIdOffers'][$this->todayD]=$zeroModelIds;

            $this->saveOpt();
            if($res['pager']['total']>50000) $this->log('WARNING! Количество товаров в ассортименте превышает 50000.');
            $this->log("task(campaign): Конец загрузки ассортимента в ym_cat: Всего {$res['pager']['total']}, из них с нулевой modelId: $zeroModelIds");

        }

        if($runUpdate==2) MC::sdel('YandexMarketRunCampaignUpdate');

        /*
         *
         *   Работаем с офферами
         *
         */

        // выбираем все недоделанные офферы по inQueue
        $limit=100;

        do{

            $continue=false;

            // выбираем ассортимент из очереди и только который был найден на маркете (NE==0). Строки с ошибками пропускаем (их обработка внизу цикла do
            // NE==1 - признак что товар не был выгружен на маркет - они остаются ссо статусом ошибки с последней проверке и не имеют inQueue
            //
            $d=$this->fetchAll("SELECT modelId FROM ym_cat WHERE inQueue='1' AND modelId!=0 AND E='0' AND NE='0' GROUP BY modelId LIMIT $limit", MYSQL_ASSOC);

            $modifsIds = [];

            if(!empty($d)) {

                foreach ($d as $v) {
                    $modifsIds[] = (int)$v['modelId'];
                }

                // $d больше не нужен

                $ids=implode(',',$modifsIds);

                $iter=0;
                do {
                    $iter++;
                    $offers = $this->getOffersForModifs([], $modifsIds);
                    if(!$offers && $iter<3 && $limit!=1){
                        $this->log("task(getOffersForModifs): итерация modelIds = [$ids] -> таймаут и попробуем еще раз ($iter)");
                        sleep(5);
                    }
                } while (!$offers && ($iter<3 && $limit!=1));


                if(!$offers) {
                    // что то не то с модификациями. Ставим E=1 и идем дальше
                    $this->log("task(getOffersForModifs) ERROR, modelsIds = [ ".implode(',',$modifsIds).' ]');
                    $u=['E'=>'1'];
                    if($limit==1) $u['inQueue']='0'; // если повторно гоняем строку, то убираем ее уже из очереди
                    $this->update('ym_cat', $u, "modelId IN ($ids)");
                    $this->log("task() Пропускаем текущий набор в $limit штук и идем дальше");
                    continue;
                }

                // получаем инфу о модификациях avg, min, max
                $iter=0;
                do {
                    $iter++;
                    $_infos = $this->getInfoForModifs([], $modifsIds);
                    if(!$_infos && $iter<3 && $limit!=1){
                        $this->log("task(getInfoForModifs): итерация modelIds = [$ids] -> таймаут и попробуем еще раз ($iter)");
                        sleep(5);
                    }
                } while (!$_infos && ($iter<3 && $limit!=1));

                // перераспредяем данные в массиве
                $infos=[];
                if($_infos){
                    if(!empty($_infos['models'])){
                        foreach($_infos['models'] as $k=>$v){
                            if(!empty($v['id']) && !empty($v['prices'])){
                                $infos[$v['id']]=$v['prices'];
                            }
                        }
                    }
                }
                unset($_infos);

                // в ассортименте ym_cat снимаем галку inQueue для всей пачки пачки в 100 штук
                if(!empty($modifsIds)) {
                    $this->update("ym_cat", ['inQueue' => '0'], "modelId IN ($ids)");
                }

                $this->log("task(): Выбран ассортимент ym_cat(".count($d).')');

                if(!empty($offers['models'])){

                    foreach($offers['models'] as $model_k=>$model_v) {  // выбрали офферы для одной модификации из json

                        $modelId=$model_v['id'];

                        $do = $this->fetchAll("SELECT * FROM ym_offers WHERE modelId=$modelId", MYSQL_ASSOC);

                        $do_updIds=[]; // обновленные строки
                        $updated=$inserted=0;

                        // перебираем офферы
                        foreach($model_v['offers'] as $offer_k=>$offer_v) {

                            $noOfferInDB=true;

                            foreach ($do as $k=>$v) { // цикл по все офферам в нашей базе для текущего типоразмера

                                if (Tools::mb_strcasecmp(Tools::unesc($v['shopName']),$offer_v['shopName'])==0) {

                                    $do_updIds[] = $v['id'];

                                    $this->update('ym_offers', $row= [
                                        'dtCheck' => Tools::dt(),
                                        'NE' => '0',
                                        'pos' => (int)@$offer_v['pos'],
                                        'name' => Tools::esc(@$offer_v['name']),
                                        'price' => (int)@$offer_v['price'],
                                        'regionId' => (int)@$offer_v['regionId'],
                                        'inStock' => (int)@$offer_v['inStock'],
                                        'shopRating' => (int)@$offer_v['shopRating'],
                                        'shippingCost' => (int)@$offer_v['shippingCost'],
                                    ], "id={$v['id']}");

                                    $updated++;

                                    // обновляем строку с оффером в массиве
                                    $do[$k]=array_merge($do[$k], $row);

                                    //$this->log("task(): Обновлен оффер ym_offer.id={$v['id']}");

                                    $noOfferInDB=false;

                                    break 1;
                                }
                            }

                            // если нет оффера в нащей базе - добавляем
                            if($noOfferInDB){
                                $this->insert('ym_offers', $row = [
                                    'modelId'=>$modelId,
                                    'dtAdd'=>$dt=Tools::dt(),
                                    'dtCheck'=>$dt,
                                    'NE'=>'0',
                                    'pos' => (int)@$offer_v['pos'],
                                    'name' => Tools::esc(@$offer_v['name']),
                                    'price' => (int)@$offer_v['price'],
                                    'regionId' => (int)@$offer_v['regionId'],
                                    'shopName' => Tools::esc(@$offer_v['shopName']),
                                    'inStock' => (int)@$offer_v['inStock'],
                                    'shopRating' => (int)@$offer_v['shopRating'],
                                    'shippingCost' => (int)@$offer_v['shippingCost']
                                ]);

                                $inserted++;
                                $do_updIds[]=$this->lastId();
                                $row['id']=$this->last_id;
                                $do[]=$row;
                                //$this->log("task(): Добавлен оффер ym_offer.id={$this->last_id}");
                            }
                        }

                        // считаем агрегаторы для одной текущей модификации
                        $avg=$min=$max=$rowi=0;
                        $sum=0;
                        $modInfo=0;
                        if(!empty($infos[$modelId])){
                            $avg=$infos[$modelId]['avg'];
                            $min=$infos[$modelId]['min'];
                            $max=$infos[$modelId]['max'];
                            $modInfo=1;
                        }
                        // если нет инфы с маркета, то счиатем сами
                        if(empty($avg) && !empty($do)) {
                            foreach ($do as $k => $v) {
                                if ($v['price'] > 0) {
                                    $rowi++;
                                    $sum += $v['price'];
                                    if ($v['price'] > $max) $max = $v['price'];
                                    if ($min == 0 || $min > $v['price']) $min = $v['price'];
                                }
                            }
                            $avg = ceil($sum / $rowi);
                            $modeInfo=2;
                        }
                        $this->update('ym_cat', [
                            'dtCheck'=>Tools::dt(),
                            'min'=>$min,
                            'max'=>$max,
                            'avg'=>$avg,
                            'E'=>'0',
                            'onlineOffers'=>@$model_v['onlineOffers'],
                            'offlineOffers'=>@$model_v['offlineOffers']
                        ], "modelId=$modelId");

                        if($modInfo==1)
                            $this->log("task(): Обновлен ym_cat[modelId=$modelId]: обновленных ym_offers = $updated, добавленных ym_offers = $inserted, дапазоны с маркета: avg=$avg, min=$min, max=$max");
                        elseif($modInfo==2)
                            $this->log("task(): Обновлен ym_cat[modelId=$modelId]: обновленных ym_offers = $updated, добавленных ym_offers = $inserted, дапазоны по оферам: avg=$avg, min=$min, max=$max");
                        else
                            $this->log("task(): Обновлен ym_cat[modelId=$modelId]: обновленных ym_offers = $updated, добавленных ym_offers = $inserted, дапазоны НЕ ОПРЕДЕЛНЫ");

                        // в оферах ставим NE = 1 для все строк которые не обновили в пределах modelId
                        if(!empty($do_updIds)) $w="AND id NOT IN(".implode(',', $do_updIds).')'; else $w='';
                        $this->update('ym_offers', ['NE'=>'1', 'dtCheck'=>Tools::dt()], "modelId=$modelId $w");
                        if($unum=$this->unum())
                            $this->log("task(): NE=1 для $unum строк (modelId=$modelId $w)");
                    }
                }



            } else {

                $this->log("task(): конец очереди inQueue");

                if($limit!=1) {
                    // проверяем есть ли E==1, уменьшаем limit до 1 и перепроверяем
                    $d = $this->getOne("SELECT count(*) FROM ym_cat WHERE E='1' AND inQueue='1' AND NE='0' AND modelId!=0");
                    if (@$d[0]) {
                        $limit = 1;
                        $this->update("ym_cat", ['E'=>'0'], "E='1' AND inQueue='1' AND NE='0' AND modelId!=0");
                        $continue=true;
                        $u=$this->updatedNum();
                        $this->log("task(): Найдены ym_cat.E==1 ({$u} строк) -> limit=1 -> E=0 -> продолжаем итерации");
                    }
                }
            }


        } while(!empty($modifsIds) || $continue);


        $this->log("task(): успешное завершение.");

        return true;
    }


    /*
     * mode ==1 - обновление ассортимента компании
     * mode ==2 - обновление свободного ассортимнета
     */
    function forceUpdate($mode)
    {
        if($mode==1)
            MC::sset('YandexMarketRunCampaignUpdate',1);
        else
            MC::sset('YandexMarketRunUpdate',1);
    }

}