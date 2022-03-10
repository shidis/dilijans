<?

abstract class App_Common_Controller extends Controller
{
    use TextParser;

    public function minQty($diametr,$gr)
    {
        if(!(int)Data::get('cc_QtyLimits')) return 1;
        if($gr==1){
            if($diametr<=16) return 4;
            elseif($diametr>=17 && $diametr<=19) return 2;
            elseif($diametr>=20) return 1;
        }else{
            if($diametr<=16) return 4;
            elseif($diametr>=17) return 2;
        }
        return 1;
    }


    public function setCookie($name, $value)
    {
        @setcookie($name, $value, time() + 8640000000, '/', Url::trimWWW(Cfg::get('site_url')));
        $_COOKIE[$name]=$value;
    }

    public function delCookie($name)
    {
        setcookie($name, '', time() - 3600, '/', Url::trimWWW(Cfg::get('site_url')));
        unset($_COOKIE[$name]);
    }

    public function init()
    {

        BotLog::detect();

        if (CU::isLogged()) $this->adminLogged = true; else $this->adminLogged = false;
        GA::attrEnhance(true);
        GA::adminCounter(false);
        GA::doubleClick(true);

        // скрывать в поиске и в альт размерах то что нет на складе
        $this->hideTSCZero = 1;
        $this->hideDSCZero = 1;

        // для ap=1
        $this->_deltaDia = -0.1;
        $this->_deltaET = -5;
        $this->deltaET_ = 3;
        $this->minQtyRadius = (int)Data::get('cc_border_radius');
        $this->minQtyRadiusSQL = "IF(cc_cat.gr=1, IF(cc_cat.P1<{$this->minQtyRadius}, cc_cat.sc>=4 , cc_cat.sc>=2), IF(cc_cat.P5<{$this->minQtyRadius}, cc_cat.sc>=4 , cc_cat.sc>=2))";

        $this->cc = new CC_Base();
        $this->os = new App_Orders();
        $this->ss = new Content();
        $this->ab = new CC_AB();
        $this->ens = new Entrysection();
        Cart::check_load();
        //$this->search=new App_Search(array('q'=>@Url::$sq['q'],'VMode'=>$this->VMode));
        //if($this->search->checkQ()) $this->searchQ=$this->search->q; else $this->searchQ='';

        $this->abCookie = array('svendor' => '', 'smodel' => '', 'syear' => '', 'smodif' => '', 'apMode' => 0);

        if (isset(App_Route::$param['ap'])) {
            $this->abCookie = array('svendor' => @App_Route::$param['ap'][0], 'smodel' => @App_Route::$param['ap'][1], 'syear' => @App_Route::$param['ap'][2], 'smodif' => @App_Route::$param['ap'][3]);

            if (!empty(App_Route::$param['ap'][0]) && !empty(App_Route::$param['ap'][1]) && !empty(App_Route::$param['ap'][2]) && !empty(App_Route::$param['ap'][3])) {
                $this->setCookie('apData', base64_encode(serialize($this->abCookie)));
            }

        } else {
            $d = @$_COOKIE['apData'];
            if (!empty($d)) $this->abCookie = @unserialize(base64_decode($d));
        }
        $this->ab->getTree($this->abCookie);

        if (empty($_COOKIE['cmp'])) $this->cmpData = array('t' => array(), 'd' => array()); else {
            $this->cmpData = @unserialize(base64_decode($_COOKIE['cmp']));
            if (!is_array($this->cmpData)) $this->cmpData = array('t' => array(), 'd' => array());
        }

        $this->cityId = '';
        $this->cityName = '';
        if (@$_COOKIE['cityId'] == '-77') {
            $this->cityId = -77;
            $this->cityName = 'Москва';
        } elseif (@$_COOKIE['cityId'] != '') {
            $this->cityId = (int)@$_COOKIE['cityId'];
            if (isset($this->cities[$this->cityId])) {
                $this->cityName = $this->cities[$this->cityId]['city'];
            }
        }
        // Бренды шин и дисков для меню
        $this->menu_brands=array();
        $this->cc->brands(array(
            'gr'=>1,
            'sqlReturn'=>0,
            'qSelect'=>array(
                'modelsNum'=>array()
            ),
            'select'=>array(
                'cc_brand.name'=>'name',
                'cc_brand.alt'=>'alt',
                'cc_brand.sname'=>'sname',
                'cc_brand.img1'=>'img1',
                'cc_brand.is_popular'=>'is_popular'
            ),
            'whereCat'=>$this->minQtyRadiusSQL,
            'having'=>'modelsNum>0',
            'order'=>'cc_brand.pos DESC'
        ));
        $d=$this->cc->fetchAll('',MYSQL_ASSOC);
        foreach($d as $v){
            $this->menu_brands[1][$v['is_popular']][]=array(
                'img1'=>$this->cc->make_img_path($v['img1']),
                'url'=>'/'.App_Route::_getUrl('tCat').'/'.Tools::unesc($v['sname']).'.html',
                'title'=>'Купить шины '.($v['alt']!=''?$v['alt']:$v['name']),
                'alt'=>'шины '.Tools::html($v['name']),
                'name'=>Tools::unesc($v['name'])
            );
        }
        //krsort($this->menu_brands[1]);
        $this->cc->brands(array(
            'gr'=>2,
            'sqlReturn'=>0,
            'qSelect'=>array(
                'modelsNum'=>array()
            ),
            'select'=>array(
                'cc_brand.name'=>'name',
                'cc_brand.alt'=>'alt',
                'cc_brand.sname'=>'sname',
                'cc_brand.img1'=>'img1',
                'cc_brand.is_popular'=>'is_popular'
            ),
            'whereCat'=>$this->minQtyRadiusSQL,
            'having'=>'modelsNum>0',
            'order'=>'cc_brand.pos DESC'
        ));
        $d=$this->cc->fetchAll('',MYSQL_ASSOC);
        foreach($d as $v){
            $this->menu_brands[2][$v['is_popular']][]=array(
                'img1'=>$this->cc->make_img_path($v['img1']),
                'url'=>'/'.App_Route::_getUrl('dCat').'/'.Tools::unesc($v['sname']).'.html',
                'title'=>'Купить шины '.($v['alt']!=''?$v['alt']:$v['name']),
                'alt'=>'шины '.Tools::html($v['name']),
                'name'=>Tools::unesc($v['name'])
            );
        }
        //krsort($this->menu_brands[2]);
        //
        $this->notAjaxInit();

    }

    function makeCanonicalUrl()
    {
        $this->canonical='';
        if(App_Route::_getAction()=='tSearch'){
            $sq=[];
            if(!empty(Url::$sq['vendor'])) $sq['vendor']=Url::$sq['vendor'];
            if(!empty(Url::$sq['p3'])) $sq['p3']=Url::$sq['p3'];
            if(!empty(Url::$sq['p2'])) $sq['p2']=Url::$sq['p2'];
            if(!empty(Url::$sq['p1'])) $sq['p1']=Url::$sq['p1'];
            if(!empty(Url::$sq['mp1'])) $sq['mp1']=Url::$sq['mp1'];
            if(!empty(Url::$sq['mp3'])) $sq['mp3']=Url::$sq['mp3'];
            if(!empty($sq)) $this->canonical='https://'.Cfg::_get('site_url').'/'.App_Route::_getUrl('tSearch').'.html?'.http_build_query($sq);
        }
    }

    private function notAjaxInit()
    {
        if (Request::$ajax) return;

        $this->seoJS = array();
        $this->seoJSW = array();
        $this->VJS=array();


        $this->initApData();

        $this->cc->load_filters_coo();

        $this->breadcrumbs = array();

        if(in_array(App_Route::_getAction(), ['avtoPodborShin', 'avtoPodborDiskov']))
            //if (strstr(App_Route::$controller, 'podborshin/') || strstr(App_Route::$controller, 'podbordiskov/'))
        {
            $this->template('podborPage');
        }
        elseif (App_Route::$controller != 'home/index') {
            $this->template('innerPage');
        }

        $this->makeCanonicalUrl();


        $this->tel = Data::get('tel');
        $this->tel2 = Data::get('tel2');

        $this->telHeader = '+7' . ltrim(preg_replace("~\(([0-9]{3})\)[\s]*([0-9\-]+)~i", " <i>($1)</i> <b>$2</b>", $this->tel), '+7');
        $this->mtel = '+7' . ltrim(ltrim(preg_replace("/[\(\)\-\s]/", '', trim(preg_replace("/(<\/strong>|<strong>)/i", '', $this->tel))), '+7'), '+');

        $this->tel2Header = '8'. ltrim(preg_replace("~\(([0-9]{3})\)[\s]*([0-9\-]+)~i", " <i>($1)</i> <b>$2</b>", $this->tel2), '+7');
        $this->mtel2 = '+7' . ltrim(ltrim(preg_replace("/[\(\)\-\s]/", '', trim(preg_replace("/(<\/strong>|<strong>)/i", '', $this->tel2))), '+7'), '+');

        $this->vr = Data::get('vr');
        $this->vrHeader = "Работаем {$this->vr}";
        $this->email = Data::get('mail_info');
        $this->officeAddr = Data::get('officeAddr');
        $this->officeSchema = Data::get('officeSchema');
        $this->isMobile = Tools::isMobile();
        $this->footerMenu = $this->parse($this->ss->getDoc('footer_menu$6'));
        $this->counters = $this->parse($this->ss->getDoc('counters$6'));
        $this->offertaFooter = $this->parse($this->ss->getDoc('offerta_footer$6'));
        Cart::make_back_url();
        $this->backUrl = Cart::$back_url_nosid;

        if(@$_COOKIE['region']!=-7750 && !$this->adminLogged) $this->show800=false; else $this->show800=true;

        $this->noimg1 = '/app/images/noimg1-m.jpg';
        $this->noimg2 = '/app/images/noimg2-m.jpg';
        $this->noimg1m = '/app/images/noimg1-m.jpg';
        $this->noimg2m = '/app/images/noimg2-m.jpg';

        $this->cities = App_Dostavka::cities();
        $this->cities = array(-77 => array('city' => 'Москва')) + $this->cities;

//
        $nonactive = false;
        $curr_url = $_SERVER['REQUEST_URI'];
        if (strcmp($curr_url, '/cart.html') == 0 )
            $nonactive = true;
//

        $this->putJSD('header .vk-c', '<a class="button vk" target="_blank" href="https://vk.com/dilijans">Мы</a>');
        $this->putJSDW('.liveinternet', '<a href="https://www.liveinternet.ru/stat/dilijans.org/" target="_blank"></a>');
        if (!empty(Cart::$b_count)) {
            if ($nonactive){
                $this->putJSD('.box-logo .basket, .mobile-header__basket .basket', '<i>' . Cart::$b_count . '</i><p><a style="pointer-events: none;cursor: default;border-bottom:0px;" rel="nofollow">Корзина:</a><b>' . Tools::nn(Cart::$asum) . ' руб.</b></p>');
            }
            else $this->putJSD('.box-logo .basket, .mobile-header__basket .basket', '<i>' . Cart::$b_count . '</i><p><a href="/cart.html">Корзина:</a><b>' . Tools::nn(Cart::$asum) . ' руб.</b></p><a href="/cart.html" class="buy" title="Перейти к оформлению заказа">Оформить<i></i></a>');
        }
        else{
            $yam_show_id = Data::get('yam_raiting');
            if ($yam_show_id == 1 || $yam_show_id == 3) {
                $this->putJSD('.box-logo .basket', '<div class="ya_reviews_wrap empty_basket_title"><p>Рейтинг магазина</p><img class="ya_reviews" src="http://grade.market.yandex.ru/?id=98540&action=image&size=0" border="0" width="88" height="31" alt="Читайте отзывы покупателей и оценивайте качество магазина на Яндекс.Маркете" /></div>');
            }else $this->putJSD('.box-logo .basket', '<div class="empty_basket_title">Корзина<br> пуста</div>');
        }
        // статьи
        $this->ss->que('cnt_list', 7, Data::get('sidebar_pubs'), 'dt_added DESC');
        $d = $this->ss->fetchAll();
        $this->articlesSB = array();
        $this->allArticlesUrl = ($u = '/' . App_Route::_getUrl('articles')) . '.html';
        foreach ($d as $v) {
            $this->articlesSB[] = array(
                'title' => Tools::unesc($v['title']),
                'url' => $u . '/' . Tools::unesc($v['sname']) . '.html'
            );
        }

        // Категории записей (выводятся в сайдбаре)
        /*$this->entry = new Entry();
        $sectionId = $this->entrysection->qrow['entry_section_id'];

        $this->entry->que('entry_list_by_section_id', "entry_section_id=" . $sectionId);*/


        $this->ens->que('entry_section');
        $this->arSectionList = array();

        if ($this->ens->qnum()) {
            while($this->ens->next()!=false){
                $this->arSectionList[] = array(
                'title' => Tools::html($this->ens->qrow['title']),
                'url' => '/'.App_Route::_getUrl('entrysection').'/'.$this->ens->qrow['sname'].'.html',
                'count' => $this->ens->qnum()
                );
            }
        }

        $this->metaCSS = App_ExLib::getMetaCSS();
        $this->CSSPush('/app/css/custom.css');

        $this->metaJS = App_ExLib::getMetaJS();

        if ($this->adminLogged) {
            $this->JSPush('/app/js/lib/jquery.cluetip.min.js');
            $this->JSPush('/app/js/admin.js');
            $this->CSSPush('/app/css/cluetip.css');
            $this->CSSPush('/app/css/admin.css');
        }

        $this->cc->load_filter('brands_sname_1');
        $this->cc->load_filter('P1_1');
        $this->cc->load_filter('P2_1');
        $this->cc->load_filter('P3_1');

        $this->cc->load_filter('nor_brands_sname_2');
        $this->cc->load_filter('r_brands_sname_2');
        $this->cc->load_filter('P1_2');
        $this->cc->load_filter('P2_2');
        $this->cc->load_filter('P3_2');
        $this->cc->load_filter('P5_2');
        $this->cc->load_filter('P4x6_2');

        $this->cc->load_filters_coo();

        $this->tRoute = array(
            -1 => '/' . App_Route::_getUrl('tCat'),
            0 => '/' . App_Route::_getUrl('tCat'),
            1 => '/' . App_Route::_getUrl('tSummer'),
            2 => '/' . App_Route::_getUrl('tWinter'),
            3 => '/' . App_Route::_getUrl('tAllW'),
            '20' => '/' . App_Route::_getUrl('tNeShip'),
            '21' => '/' . App_Route::_getUrl('tShip')
        );

        $this->tAtRoute = array(
            0 => '/' . App_Route::_getUrl('tCat'),
            1 => '/' . App_Route::_getUrl('tLight'),
            2 => '/' . App_Route::_getUrl('tSUV'),
            3 => '/' . App_Route::_getUrl('tStrong')
        );

        $this->tSezAtRoute = array(
            '00' => '/' . App_Route::_getUrl('tCat'),
            '01' => '/' . App_Route::_getUrl('tLight'),
            '02' => '/' . App_Route::_getUrl('tSUV'),
            '03' => '/' . App_Route::_getUrl('tStrong'),
            '10' => '/' . App_Route::_getUrl('tSummer'),
            '11' => '/' . App_Route::_getUrl('tSummerLight'),
            '12' => '/' . App_Route::_getUrl('tSummerSUV'),
            '13' => '/' . App_Route::_getUrl('tSummerStrong'),
            '20' => '/' . App_Route::_getUrl('tWinter'),
            '21' => '/' . App_Route::_getUrl('tWinterLight'),
            '22' => '/' . App_Route::_getUrl('tWinterSUV'),
            '23' => '/' . App_Route::_getUrl('tWinterStrong'),
            '30' => '/' . App_Route::_getUrl('tAllW'),
            '31' => '/' . App_Route::_getUrl('tAllWLight'),
            '32' => '/' . App_Route::_getUrl('tAllWSUV'),
            '33' => '/' . App_Route::_getUrl('tAllWStrong')
        );


        $this->sezonNames = array(
            0 => 'Шины',
            1 => 'Летние шины',
            2 => 'Зимние шины',
            3 => 'Всесезонные шины',
            '20' => 'Зимние нешипованная шины',
            '21' => 'Зимние шипованная шины'
        );

        $this->sezonNames1 = array(
            0 => 'Резина',
            1 => 'Летняя резина',
            2 => 'Зимняя резина',
            3 => 'Всесезонная резина',
            '20' => 'Зимняя нешипованная резина',
            '21' => 'Зимняя шипованная резина'
        );

        $this->sezonNames2 = array(
            0 => 'Шина',
            1 => 'Летняя шина',
            2 => 'Зимняя шина',
            3 => 'Всесезонная шина',
            '20' => 'Зимняя нешипованная шина',
            '21' => 'Зимняя шипованная шина'
        );

        $this->sezonNames3 = array(
            0 => '',
            1 => 'летняя',
            2 => 'зимняя',
            3 => 'всесезонная'
        );

        $this->sezonNames4 = array(
            0 => '',
            1 => 'летние',
            2 => 'зимние',
            3 => 'всесезонные',
            '20' => 'зимние нешипованные',
            '21' => 'зимние шипованные'
        );

        $this->sezonNames5 = array(
            0 => '',
            1 => 'Летние',
            2 => 'Зимние',
            3 => 'Всесезонные'
        );

        $this->sezonNames6 = array(
            0 => '',
            1 => 'летних',
            2 => 'зимних',
            3 => 'всесезонных',
            '20' => 'зимних нешипованных',
            '21' => 'зимних шипованных'
        );

        $this->sezonNames7 = array(
            0 => '',
            1 => 'летней',
            2 => 'зимней',
            3 => 'всесезонной'
        );

        $this->sezonNames8 = array(
            0 => '',
            1 => 'летнюю',
            2 => 'зимнюю',
            3 => 'всесезонную'
        );

        $this->sezonNames9 = array(
            0 => '',
            1 => 'летние',
            2 => 'зимние',
            3 => 'всесезонные'
        );

        $this->sezonIcos = array(
            0 => '',
            1 => '<img src="/app/images/sun.png" alt="летние шины">',
            2 => '<img src="/app/images/snow.png" alt="зимние шины">',
            3 => '<img src="/app/images/sunsnow.png" alt="всесезонные шины">',
        );

        $this->atIcos = array(
            0 => '',
            1 => '<img src="/app/images/sun.png" alt="летние шины">',
            2 => '<img src="/app/images/snow.png" alt="зимние шины">',
            3 => '<img src="/app/images/sunsnow.png" alt="всесезонные шины">',
        );

        $this->atNames1 = array(
            1 => 'для легковых авто',
            2 => 'для внедорожников',
            3 => 'для микроавтобусов',
            4 => 'для легковых авто и внедорожников'
        );

        $this->atNames2 = array(
            1 => 'для легковых авто',
            2 => 'для джипов',
            3 => 'для микроавтобусов',
            4 => 'для легких грузовиков'
        );

        $this->atNames3 = array(
            1 => 'легковые',
            2 => 'внедорожные',
            3 => 'усиленные',
            4 => 'легкогрузовые'
        );


        $this->diskTypes = array(
            0 => 'Диски',
            1 => 'Кованые диски',
            2 => 'Литые диски',
            3 => 'Штампованные диски'
        );

        $this->diskTypes1 = array(
            0 => '',
            1 => 'кованые',
            2 => 'литые',
            3 => 'штампованные'
        );

        $this->diskTypes2 = array(
            0 => '',
            1 => 'кованый',
            2 => 'литой',
            3 => 'штампованный'
        );

    }

    public function arr2url($aName, $a, $value = 1)
    {
        if (is_array($a)) {
            $s = array();
            foreach ($a as $v) $s[] = "{$aName}[{$v}]={$value}";
            return implode('&', $s);
        } else return "{$aName}={$a}";
    }

    // возвращает первую часть строки до первого разделителя
    public function firstS($str, $delimeter = ',')
    {
        $s = explode($delimeter, $str);
        return $s[0];
    }

    // возвращает всю подстроку после первого разделителя
    public function otherS($str, $delimeter = ',')
    {
        $s = mb_substr($str, 0, (mb_strlen($str) - mb_strpos($str, $delimeter)) * -1);
        return $s != '' ? $s : $str;
    }

    public function price($price)
    {
        return Tools::nn($price);
    }

    public function putJSD($selector, $data)
    {
        $this->seoJS[$selector] = base64_encode($data);
    }

    public function putJSDW($selector, $data)
    {
        $this->seoJSW[$selector] = base64_encode($data);
    }

    function e404($label)
    {
        GA::_event('Other', 'e404', ltrim(@$_SERVER['REQUEST_URI'], '/'), '', true);
        return App_Route::redir404();
    }

    public function month($month, $len)
    {
        $a = array('января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');
        if ($len) return mb_substr(@$a[$month - 1], 0, $len); else return @$a[$month - 1];
    }

    // не используется пока
    public function initApData()
    {
        if ($this->apInited) return true;
        $this->apAllUrl = $this->apUrl = '';
        $this->apUrl = '/' . App_Route::_getUrl('avtoPodbor');
        $this->apParam = array();
        $this->apInited = true;
        $this->ab->getTree($this->abCookie);
        if (empty($this->abCookie['smodif'])) {
            return true;
        }
        if (empty($this->ab->tree['modif_id'])) {
//			setcookie('apData','',time()-3600,'/');
//			$this->abCookie=array('svendor'=>'','smodel'=>'','syear'=>'','smodif'=>'');
            return false;
        }
        $this->avtoName = $this->ab->fname;
        $this->avtoId = $this->ab->tree['modif_id'];
        $this->ab->avto_sh($this->ab->tree['modif_id']);
        $this->apAllUrl .= '/' . str_replace('/', '--', $this->ab->spath);
//		$this->abc=$this->ab->getCommon($this->ab->tree['modif_id']);
        // список размеров в кучу | значения массива напрямую посылаются в catOrGroups
        //шины
        $this->apSizes = array(1 => array(), 2 => array());
        foreach ($this->ab->avto[1] as $k => $v) {
            foreach ($v as $k1 => $v1) {
                if (is_array(@$v1[1]) || is_array(@$v1[2])) {
                    foreach ($v1 as $k2 => $v2) { // спарки
                        $v2['P2'] = (float)$v2['P2'];
                        $this->apSizes[1]["{$v2['P1']}-{$v2['P2']}-{$v2['P3']}"] = array('P1' => $v2['P1'], 'P2' => $v2['P2'], 'P3' => $v2['P3']);
                    }
                } else {
                    $v1['P2'] = (float)$v1['P2'];
                    $this->apSizes[1]["{$v1['P1']}-{$v1['P2']}-{$v1['P3']}"] = array('P1' => $v1['P1'], 'P2' => $v1['P2'], 'P3' => $v1['P3']);
                }
            }
        }
        $this->apSizes['q1'] = array_values($this->apSizes[1]);
        // disks
        // DIA >=значения
        $s2 = array();
        foreach ($this->ab->avto[2] as $k => $v) {
            foreach ($v as $k1 => $v1) {
                if (is_array(@$v1[1]) || is_array(@$v1[2])) {
                    foreach ($v1 as $k2 => $v2) { // спарки
                        $dia = $v2['P3'] + $this->_deltaDia;
                        $et1 = $v2['P1'] + $this->_deltaET;
                        $et2 = $v2['P1'] + $this->deltaET_;
                        $this->apSizes[2]["{$v2['P1']}-{$v2['P2']}-{$v2['P3']}-{$v2['P4']}-{$v2['P5']}-{$v2['P6']}"] = array('P1' => $v2['P1'], 'P2' => $v2['P2'], 'P3' => $v2['P3'], 'P4' => $v2['P4'], 'P5' => $v2['P5'], 'P6' => $v2['P6']);
                        $s2["{$v2['P1']}-{$v2['P2']}-{$v2['P3']}-{$v2['P4']}-{$v2['P5']}-{$v2['P6']}"] = array("(cc_cat.P1 LIKE '$et1' OR cc_cat.P1 LIKE '$et2' OR cc_cat.P1>='$et1' AND cc_cat.P1<='$et2')", 'P2' => $v2['P2'], "(cc_cat.P3 LIKE '{$dia}' OR cc_cat.P3>='{$dia}')", 'P4' => $v2['P4'], 'P5' => $v2['P5'], 'P6' => $v2['P6']);
                    }
                } else {
                    $dia = $v1['P3'] + $this->_deltaDia;
                    $et1 = $v1['P1'] + $this->_deltaET;
                    $et2 = $v1['P1'] + $this->deltaET_;
                    $this->apSizes[2]["{$v1['P1']}-{$v1['P2']}-{$v1['P3']}-{$v1['P4']}-{$v1['P5']}-{$v1['P6']}"] = array('P1' => $v1['P1'], 'P2' => $v1['P2'], 'P3' => $v1['P3'], 'P4' => $v1['P4'], 'P5' => $v1['P5'], 'P6' => $v1['P6']);
                    $s2["{$v1['P1']}-{$v1['P2']}-{$v1['P3']}-{$v1['P4']}-{$v1['P5']}-{$v1['P6']}"] = array("(cc_cat.P1 LIKE '$et1' OR cc_cat.P1 LIKE '$et2' OR cc_cat.P1>='$et1' AND cc_cat.P1<='$et2')", 'P2' => $v1['P2'], "(cc_cat.P3 LIKE '{$dia}' OR cc_cat.P3>='{$dia}')", 'P4' => $v1['P4'], 'P5' => $v1['P5'], 'P6' => $v1['P6']);
                }
            }
        }
        $this->apSizes['q2'] = array_values($s2);
        unset($s2);


//		Tools::prn($this->apSizes[2]);
//		Tools::prn($this->ab->avto[2]);
        return true;
    }

    public function preRender()
    {

        if (Request::$ajax) return;

        $this->JS = $this->getJS();
        $this->CSS = $this->getCSS();

        $this->pages = new App_Pages();
        $this->pages->setChainMode(true);

        /*
            $title - тег <title></title>
            $_title - тег H1
            $topTextTitle - тег H1
            $topText - текст под H1
            $bottomTextTitle - тег H2 для блока внизу страницы после вывода $content
            $bottomText - текст для блока внизу страницы после вывода $content
            киворды и дескрипшены  в верхнем блоке имеют высший приоритет
        */

        $title1 = $this->pages->title(2); // верхний блок
        $title2 = $this->pages->title(1); // нижний блок
        if ($title1 != '') $this->title = $title1; elseif ($title2 != '') $this->title = $title2; // тайтл из верхнего блока имеет больший приоритет.
        $this->title = Tools::stripTags($this->title);

        $t1 = $this->pages->block(2); // верхний блок
        $t2 = $this->pages->block(1); // нижний блок
        if (Tools::stripTags($t1) != '') $this->topText = ($t1); else $this->topText = @$this->topText;
        if (Tools::stripTags($t2) != '') $this->bottomText = ($t2); else $this->bottomText = @$this->bottomText;

        $this->topTextTitle = $this->pages->header(2);
        $this->bottomTextTitle = $this->pages->header(1);

        if (!empty($this->topTextTitle)) $this->_title = $this->topTextTitle;

        $kd = $this->pages->kd(array(1, 2)); // (1,2)  - приоритет имеет KD из верхнего влока
        if (!empty($kd['description'])) $this->description = $kd['description'];
        if (!empty($kd['keywords'])) $this->keywords = $kd['keywords'];

        $this->topText=$this->parseText($this->topText);
        $this->bottomText=$this->parseText($this->bottomText);
        $this->keywords=$this->parseText($this->keywords);
        $this->description=$this->parseText($this->description);

        $this->description=htmlspecialchars($this->description,ENT_QUOTES);
        $this->keywords=htmlspecialchars($this->keywords,ENT_QUOTES);

        // переменные для вывода в хтмл - их дублируем в пространство $this
        $this->_data=array_merge($this->VJS,$this->_data);
    }


}