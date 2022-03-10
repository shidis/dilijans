<?
class App_PodborDiskov_Controller extends App_Common_Controller
{
    private
    $valid_radiuses = array(),
    $valid_brands = array(),
    // главные параметры
    $P1=array(), // GET['p1'] || route['P1'] as float    ET
    $P2=array(), // GET['p2'] || route['P2'] as float   J
    $P3=array(), // GET['p3'] || route['P3'] as float    DIA
    $P4=array(), // GET['p4'] || route['P4'] as decimal   PCD
    $P5=array(), // GET['p5'] || route['P5'] as float    RADIUS
    $P6=array(), // GET['p6'] || route['P6'] as float   DCO
    $P46=array(), // GET['sv']  сверловка   SV
    $brands=array(), // GET['vendor']
    $replica, // _GET['replica']=1 || route['replica']=1
    // главные спарочные
    $sP1=array(), // GET['p1_']
    $sP2=array(), // GET['p2_']
    $sP3=array(), // GET['p3_']
    $sP4=array(), // GET['p4_']
    $sP5=array(), // GET['p5_']
    $sP6=array(), // GET['p6_']
    $sMode=false, // ==true - спарка-режим

    // уточняющие параметры
    $_P1=array(), // GET['_p1']
    $_P2=array(), // GET['_p2']
    $_P3=array(), // GET['_p3']
    $_P4=array(), // GET['_p4']
    $_P5=array(), // GET['_p5']
    $_P6=array(), // GET['_p6']
    $_P46=array(), // GET['_sv']
    $_brands=array(), // GET['_bids']
    // сумма параметров
    $P1_=array(),
    $P2_=array(),
    $P3_=array(),
    $P4_=array(),
    $P5_=array(),
    $P6_=array(),
    $P46_=array(),
    $brands_=array(),
    $noimg2='/app/images/noimg2-m.jpg';
    // ******************* Включена по-умолчанию для подбора по маркам авто
    private
    $apMode = 1;  // _GET['ap']==1  включает delta_et  и delta_dia         
    // ********************************************************************
    private function _initLists()
    {
        $this->mark = (@$_REQUEST['mark']);
        $this->model = (@$_REQUEST['model']);
        $this->year = (@$_REQUEST['year']);
        $this->modif = (@$_REQUEST['modif']);

        $this->ab = new CC_AB();

        $this->ab->getTree(array('svendor' => $this->mark, 'smodel' => $this->model, 'syear' => $this->year, 'smodif' => $this->modif));
    }


    public function index()
    {
        $this->view('podbordiskov/index');

        $this->apInited = true;
        $this->ab->getTree($this->abCookie);

        if (!count($this->ab->tree['vendors'])) return App_Route::redir404();
        $this->marks = array();
        foreach ($this->ab->tree['vendors'] as $v) {
            $this->marks[] = array(
                'anc' => Tools::unesc($v['name']),
                'title' => 'шины и диски для ' . Tools::unesc($v['alt'] != '' ? $v['alt'] : $v['name']),
                'url' => '/' . App_Route::_getUrl('avtoPodborDiskov') . '/' . Tools::unesc($v['sname']) . '.html',
                'sname' => Tools::unesc($v['sname'])
            );
        }      
        $this->title = 'Подбор дисков по марке автомобиля позволяет подобрать колесные диски по марке авто онлайн!';
        $this->description = 'Сервис позволяет подобрать диски по марке авто. Просто введите марку, модель, год, двигатель и вы получите подбор колесных дисков по вашему автомобилю. Подобрать и купить литые диски в интернет-магазине «Дилижанс».';
        $this->keywords = 'Подобрать диски по марке, подбор дисков по автомобилю, подобрать диски по марке автомобиля, подбор дисков по марке автомобиля, онлайн подбор дисков по авто, литые диски, подбор колесных дисков, литых';

        $this->breadcrumbs['Подбор дисков по марке авто'] = '';
        $this->_title = 'Подбор колесных дисков по марке автомобиля';
        // *** Очистка кук
        $this->abCookie = array('svendor' => '', 'smodel' => '', 'syear' => '', 'smodif' => '', 'apMode' => 0);
        $this->setCookie('apData', base64_encode(serialize($this->abCookie)));
        // Очистка ненужных параметров (фикс для чекбоксов)
        unset($this->ab->tree['models'], $this->ab->tree['years'], $this->ab->tree['modifs']);
        $this->relink_href = '/' . App_Route::_getUrl('avtoPodborShin') . '.html';
    }

    public function models()
    {
        $this->apInited = true;
        $this->ab->getTree($this->abCookie);

        if (!count($this->ab->tree['models'])) return App_Route::redir404();
        $this->view('podbordiskov/models');
        $this->mark = $this->ab->tree['vendor_name'];
        $this->mark2 = $this->mark_alt = $this->ab->tree['vendor_alt'] != '' ? $this->ab->tree['vendor_alt'] : $this->ab->tree['vendor_name'];
        $this->fname = $this->ab->fname;

        $this->bottomText = $this->ss->getDoc('avtoPodborDiskov_models$10');
        if (mb_strlen(trim(Tools::striptags($this->bottomText))) < 20) $this->bottomText = '';

        $this->description = $this->ss->meta['description'];
        $this->keywords = $this->ss->meta['keywords'];

        $this->bottomText = ($this->ab->tree['vendor_text1']);

        $this->models = array();
        foreach ($this->ab->tree['models'] as $v) {
            $this->models[] = array(
                'anc' => Tools::unesc($this->mark . ' ' . $v['name']),
                'title' => 'шины и диски для ' . Tools::unesc($v['alt'] != '' ? "{$this->mark_alt} {$v['alt']}" : "{$this->mark_alt} {$v['name']}"),
                'url' => '/' . App_Route::_getUrl('avtoPodborDiskov') . '/' . App_Route::$param['ap'][0] . '--' . Tools::unesc($v['sname']) . '.html',
                'sname' => Tools::unesc($v['sname'])
            );
        }
        $this->title = "Диски {$this->mark} с оригинальными параметрами! Подобрать колесные диски на {$this->mark_alt} онлайн!";
        // Закомментить, когда сделается нормально через админку
        $this->description = "Сервис позволяет подобрать диски для {$this->mark}. Просто введите модель, год, двигатель и вы получите подбор колесных дисков на {$this->mark_alt}. Подобрать и купить литые диски в интернет-магазине «Дилижанс».";
        $this->keywords = "Диски {$this->mark}, подбор дисков {$this->mark}, подобрать диски для {$this->mark}, Подбор дисков для {$this->mark}, Диски {$this->mark_alt}, литые диски {$this->mark_alt}, колесные, литые";
        //***
        $this->_title = "Подобрать диски для {$this->mark}.";

        $this->breadcrumbs['Подбор дисков по марке авто'] = array('/' . App_Route::_getUrl('avtoPodborDiskovIndex') . '.html', 'подобрать шины по марке авто');
        $this->breadcrumbs[$this->mark] = '';

        $d=$this->ab->getOne("SELECT cb.sname FROM cc_brand cb JOIN ab_avto ab USING (avto_id) WHERE ab.avto_id={$this->ab->tree['vendor_id']}");
        if($d!==0 && !empty($d['sname'])){
            $this->cc->brands(array(
                'gr'=>2,
                'where'=>'cc_brand.replica=1 AND cc_brand.sname=\''.$d['sname'].'\'',
                'qSelect'=>array(
                    'modelsNum'=>array()
                ),
                'whereCat'=>$this->minQtyRadiusSQL,
                'having'=>'modelsNum>0'
            ));
            $d_ex=$this->cc->fetchAll('',MYSQL_ASSOC);
            if (!empty($d_ex)) {
                $this->replicaCross = array(
                    'url' => '/' . App_Route::_getUrl('dCat') . '/' . $d['sname'] . '.html',
                    'title' => "Диски replica {$this->mark}",
                    'anc' => "Диски реплика для {$this->mark_alt}"
                );
            }
        }
        // *** Установка кук
        $this->abCookie = array(
            'svendor' => @$this->ab->tree['vendor_sname'] ? $this->ab->tree['vendor_sname'] : '', 
            'smodel'  => @$this->ab->tree['model_sname'] ? $this->ab->tree['model_sname'] : '', 
            'syear'   => @$this->ab->tree['year_sname'] ? $this->ab->tree['year_sname'] : '', 
            'smodif'  => @$this->ab->tree['modif_sname'] ? $this->ab->tree['modif_sname'] : ''
        );  
        $this->setCookie('apData', base64_encode(serialize($this->abCookie)));
        //***  
        // Хак для картинки бренда
        $this->ab->avto_sh_array(@$this->ab->tree['vendor_id'], array_keys($this->ab->tree['models'])[0], null, Array(2));
        //*** Переназначаем мета-теги и описаия ***//
        $page_meta_info = $this->ab->getOne("SELECT * FROM `ab_podbor_meta` WHERE LD = 0 AND H=0 AND gr='2'
            ".(!empty($this->ab->tree['vendor_id']) ? " AND vendor_id='".$this->ab->tree['vendor_id']."'" : '')."
            ".(!empty($this->ab->tree['model_id']) ? " AND model_id='".$this->ab->tree['model_id']."'" : ' AND model_id=0')."
            ".(!empty($this->ab->tree['year_id']) ? " AND year_id='".$this->ab->tree['year_id']."'" : ' AND year_id=0')."
            ".(!empty($this->ab->tree['modif_id']) ? " AND modif_id='".$this->ab->tree['modif_id']."'" : ' AND modif_id=0')
            ,MYSQL_ASSOC);
        if(!empty($page_meta_info))
        {
            $this->title = $page_meta_info['seo_title'];
            $this->description = $page_meta_info['seo_desc'];
            $this->keywords = $page_meta_info['seo_key'];
            //***
            $this->_title = $page_meta_info['seo_h1'];
            $this->upText = $page_meta_info['text1'];
            $this->h2     = $page_meta_info['seo_h2'];
            $this->dwText = $page_meta_info['text2'];
        }
        //*****************************************//
        $this->relink_href = '/' . App_Route::_getUrl('avtoPodborShin') . '/' . App_Route::$param['ap'][0] . '.html';
    }

    public function years()
    {
        $this->apInited = true;
        $this->ab->getTree($this->abCookie);

        // собираем все id машинок с такими параметрами
        $this->ab->avto_sh_array(@$this->ab->tree['vendor_id'], @$this->ab->tree['model_id'], @$this->ab->tree['year_id'], Array(2));
        //
        if (!count($this->ab->tree['years'])) return App_Route::redir404();
        $this->view('podbordiskov/years');
        $this->mark = $this->ab->tree['vendor_name'];
        $this->mark2 = $this->mark_alt = $this->ab->tree['vendor_alt'] != '' ? $this->ab->tree['vendor_alt'] : $this->ab->tree['vendor_name'];
        $this->model = $this->ab->tree['model_name'];
        $this->model2 = $this->model_alt = $this->ab->tree['model_alt'] != '' ? $this->ab->tree['model_alt'] : $this->ab->tree['model_name'];
        $this->fname = $this->ab->fname;

        //$this->bottomText = $this->ss->getDoc('avtoPodborDiskov_years$10');
        if (mb_strlen(trim(Tools::striptags($this->bottomText))) < 20) $this->bottomText = '';

        $this->description = $this->ss->meta['description'];
        $this->keywords = $this->ss->meta['keywords'];

        /*$s = ($this->ab->tree['model_text1']);
        if (!empty($s)) $this->bottomText = $s;    */

        $this->years = array();
        foreach ($this->ab->tree['years'] as $v) {
            $this->years[] = array(
                'anc' => Tools::unesc($v['name']),
                'url' => '/' . App_Route::_getUrl('avtoPodborDiskov') . '/' . App_Route::$param['ap'][0] . '--' . App_Route::$param['ap'][1] . '--' . Tools::unesc($v['sname']) . '.html',
                'sname' => Tools::unesc($v['sname'])

            );
        }

        $this->breadcrumbs['Подбор дисков по марке авто'] = array('/' . App_Route::_getUrl('avtoPodborDiskovIndex') . '.html', 'подобрать шины и диски по марке авто');
        $this->breadcrumbs[$this->mark] = array('/' . App_Route::_getUrl('avtoPodborDiskov') . '/' . App_Route::$param['ap'][0] . '.html', '');
        $this->breadcrumbs[$this->model] = '';
        // *** добавление фильтра
        $this->abc = $this->ab->getCommons(@$this->ab->tree['vendor_id'], @$this->ab->tree['model_id'], @$this->ab->tree['year_id']);
        //$this->brands = array_unique(array_merge($this->ab->getBrandsIds(array_keys($this->ab->tree['vendors'])), Array($this->ab->brand_id)));
        $this->brands = array_unique(array_merge($this->ab->getBrandsIds(), Array($this->ab->brand_id)));   
        // ***  Добавляем параметры в класс, как будто их выбрал пользователь
        $this->setClassParams();
        // *** 
        @ksort($this->ab->avto[2], SORT_STRING);       
        $this->sz2=array();
        ksort($this->ab->avto[2]);
        foreach($this->ab->avto[2] as $c=>$v){
            foreach($v as $vv){
                if($c==20 || $c==25){
                    if(!isset($this->sz2[$vv['P5']])) $this->sz2[$vv['P5']]=array();
                    $this->sz2[$vv['P5']]['OEM'][][1]=$vv;
                }else{
                    $vvv=current($vv);
                    $this->sz2[$vvv['P5']]['Тюннинг'][]=$vv;
                }
            }
        }
        ksort($this->sz2);
        $native_sz = $this->sz2;
        $this->dSearchUrl='/'.App_Route::_getUrl('dSearch').'.html';
        //***  
        $this->brandsFilter(); 
        //***
        $this->diaMore = 1;
        $this->search();
        // Возвращаем $this->sz2 и добавляем в него подсчет из _cat()
        foreach ($native_sz as $i1 => $r){
            foreach ($r as $i2 => $type){
                foreach ($type as $i3 => $elem){
                    if (count($elem) > 1){
                        $native_sz[$i1][$i2][$i3]['exnum'] = $this->sz2[$i1][$i2][$i3]['exnum'];
                    }else{
                        $native_sz[$i1][$i2][$i3][1]['exnum'] = $this->sz2[$i1][$i2][$i3][1]['exnum'];
                    }
                }
            }
        }
        $this->sz2 = $native_sz;
        //
        $this->title = "Диски на {$this->mark} {$this->model} с оригинальными параметрами! Подобрать колесные диски для {$this->mark_alt} ".Tools::replaceMetaInCatalog($this->model)." онлайн. Типоразмеры R".implode(', R', array_keys($this->sz2)).".";
        // Закомментить, когда сделается нормально через админку
        $this->description = "Колесные диски для {$this->mark} {$this->model}. Подобрать и купить литые диски на {$this->mark} {$this->model} в интернет-магазине «Дилижанс». Широкий выбор типоразмеров на {$this->mark_alt} ".Tools::replaceMetaInCatalog($this->model).": R".implode(', R', array_keys($this->sz2)).".";
        $this->keywords = "Диски {$this->mark} {$this->model}, подбор дисков {$this->mark} {$this->model}, подобрать диски для {$this->mark} {$this->model}, Подбор дисков для {$this->mark} {$this->model}, Диски {$this->mark_alt} ".Tools::replaceMetaInCatalog($this->model).", литые, колесные";
        //***
        $this->_title = "Диски для {$this->mark} {$this->model}";
        // *** Установка кук
        $this->abCookie = array(
            'svendor' => @$this->ab->tree['vendor_sname'] ? $this->ab->tree['vendor_sname'] : '', 
            'smodel'  => @$this->ab->tree['model_sname'] ? $this->ab->tree['model_sname'] : '', 
            'syear'   => @$this->ab->tree['year_sname'] ? $this->ab->tree['year_sname'] : '', 
            'smodif'  => @$this->ab->tree['modif_sname'] ? $this->ab->tree['modif_sname'] : ''
        );  
        $this->setCookie('apData', base64_encode(serialize($this->abCookie)));
        //*** Переназначаем мета-теги и описаия ***//
        $page_meta_info = $this->ab->getOne("SELECT * FROM `ab_podbor_meta` WHERE LD = 0 AND H=0 AND gr='2'
            ".(!empty($this->ab->tree['vendor_id']) ? " AND vendor_id='".$this->ab->tree['vendor_id']."'" : '')."
            ".(!empty($this->ab->tree['model_id']) ? " AND model_id='".$this->ab->tree['model_id']."'" : ' AND model_id=0')."
            ".(!empty($this->ab->tree['year_id']) ? " AND year_id='".$this->ab->tree['year_id']."'" : ' AND year_id=0')."
            ".(!empty($this->ab->tree['modif_id']) ? " AND modif_id='".$this->ab->tree['modif_id']."'" : ' AND modif_id=0')
            ,MYSQL_ASSOC);
        if(!empty($page_meta_info))
        {
            $this->title = $page_meta_info['seo_title'];
            $this->description = $page_meta_info['seo_desc'];
            $this->keywords = $page_meta_info['seo_key'];
            //***
            $this->_title = $page_meta_info['seo_h1'];
            $this->upText = $page_meta_info['text1'];
            $this->h2     = $page_meta_info['seo_h2'];
            $this->dwText = $page_meta_info['text2'];
        }
        $this->show_rating = $this->ab->tree['ext_avto_info']['show_rating'];
        $this->avto_image  = !empty($this->ab->tree['ext_avto_info']['avto_image']) ? '/'.Cfg::get('cc_upload_dir').'/'.$this->ab->tree['ext_avto_info']['avto_image'] : '';
        //*****************************************//
        $this->relink_href = '/' . App_Route::_getUrl('avtoPodborShin') . '/' . App_Route::$param['ap'][0] . '--' . App_Route::$param['ap'][1] . '.html';
    }

    public function modifs()
    {
        $this->apInited = true;
        $this->ab->getTree($this->abCookie);
        // собираем все id машинок с такими параметрами
        $this->ab->avto_sh_array(@$this->ab->tree['vendor_id'], @$this->ab->tree['model_id'], @$this->ab->tree['year_id'], Array(2));
        //
        if (!count($this->ab->tree['modifs'])) return App_Route::redir404();
        $this->view('podbordiskov/modifs');
        $this->mark = $this->ab->tree['vendor_name'];
        $this->mark2 = $this->mark_alt = $this->ab->tree['vendor_alt'] != '' ? $this->ab->tree['vendor_alt'] : $this->ab->tree['vendor_name'];
        $this->model = $this->ab->tree['model_name'];
        $this->model2 = $this->model_alt = $this->ab->tree['model_alt'] != '' ? $this->ab->tree['model_alt'] : $this->ab->tree['model_name'];
        $this->year = $this->ab->tree['year_name'];
        $this->fname = $this->ab->fname;

        $this->ss->getDoc('avtoPodborDiskov_modifs$10');
        $this->description = $this->ss->meta['description'];
        $this->keywords = $this->ss->meta['keywords'];
        $this->introText = array(1 => $this->parse($this->ss->cnt_intro), 2 => $this->parse($this->ss->cnt_text));

        $this->bottomText = ($this->ab->tree['year_text1']);

        $this->modifs = array();
        foreach ($this->ab->tree['modifs'] as $v) {
            $this->modifs[] = array(
                'anc' => Tools::unesc($this->mark . ' ' . $this->model . ' ' . $v['name']),
                'url' => '/' . App_Route::_getUrl('avtoPodborDiskov') . '/' . App_Route::$param['ap'][0] . '--' . App_Route::$param['ap'][1] . '--' . App_Route::$param['ap'][2] . '--' . Tools::unesc($v['sname']) . '.html',
                'sname' => Tools::unesc($v['sname'])
            );
        }

        $this->breadcrumbs['Подбор дисков по марке авто'] = array('/' . App_Route::_getUrl('avtoPodborDiskovIndex') . '.html', 'подобрать дисков по машине');
        $this->breadcrumbs[$this->mark_alt] = array('/' . App_Route::_getUrl('avtoPodborDiskov') . '/' . App_Route::$param['ap'][0] . '.html', "размеры шин и дисков для {$this->mark}");
        $this->breadcrumbs[$this->model_alt] = array('/' . App_Route::_getUrl('avtoPodborDiskov') . '/' . App_Route::$param['ap'][0] . '--' . App_Route::$param['ap'][1] . '.html', "размеры шин и дисков для {$this->mark} {$this->model}");
        $this->breadcrumbs[$this->year . ' года'] = '';
        // *** добавление фильтра
        $this->abc = $this->ab->getCommons(@$this->ab->tree['vendor_id'], @$this->ab->tree['model_id'], @$this->ab->tree['year_id']);
        //$this->brands = array_unique(array_merge($this->ab->getBrandsIds(array_keys($this->ab->tree['vendors'])), Array($this->ab->brand_id))); 
        $this->brands = array_unique(array_merge($this->ab->getBrandsIds(), Array($this->ab->brand_id)));
        // ***  Добавляем параметры в класс, как будто их выбрал пользователь
        $this->setClassParams();
        // ***
        $this->sz2=array();
        @ksort($this->ab->avto[2]);
        foreach($this->ab->avto[2] as $c=>$v){
            foreach($v as $vv){
                if($c==20 || $c==25){
                    if(!isset($this->sz2[$vv['P5']])) $this->sz2[$vv['P5']]=array();
                    $this->sz2[$vv['P5']]['OEM'][][1]=$vv;
                }else{
                    $vvv=current($vv);
                    $this->sz2[$vvv['P5']]['Тюннинг'][]=$vv;
                }
            }
        }
        ksort($this->sz2);
        $native_sz = $this->sz2;
        $this->dSearchUrl='/'.App_Route::_getUrl('dSearch').'.html';
        //***  
        $this->brandsFilter(); 
        //***
        $this->diaMore = 1;
        $this->search();
        // Возвращаем $this->sz2 и добавляем в него подсчет из _cat()
        foreach ($native_sz as $i1 => $r){
            foreach ($r as $i2 => $type){
                foreach ($type as $i3 => $elem){
                    if (count($elem) > 1){
                        $native_sz[$i1][$i2][$i3]['exnum'] = $this->sz2[$i1][$i2][$i3]['exnum'];
                    }else{
                        $native_sz[$i1][$i2][$i3][1]['exnum'] = $this->sz2[$i1][$i2][$i3][1]['exnum'];
                    }
                }
            }
        }
        $this->sz2 = $native_sz;
        //
        $this->title = "Диски на {$this->mark} {$this->model} {$this->year} г/в. Подобрать колесные диски для {$this->mark_alt} ".Tools::replaceMetaInCatalog($this->model)." {$this->year} г/в онлайн. Типоразмеры R".implode(', R', array_keys($this->sz2)).".";
        // Закомментить, когда сделается нормально через админку
        $this->description = "Диски на {$this->mark} {$this->model} {$this->year} г/в. Подобрать и купить колесные диски на {$this->mark_alt} ".Tools::replaceMetaInCatalog($this->model)." {$this->year} г/в в интернет-магазине «Дилижанс».  Литые диски {$this->mark} {$this->model} {$this->year} г/в все типоразмеры: R".implode(', R', array_keys($this->sz2)).".";
        $this->keywords = "Диски {$this->mark} {$this->model} {$this->year} г/в, подбор дисков {$this->mark} {$this->model} {$this->year} г/в, подобрать диски для {$this->mark} {$this->model} {$this->year} г/в, Подбор дисков для {$this->mark} {$this->model} {$this->year} г/в, Диски {$this->mark} {$this->model} {$this->year} г/в, литые, колесные";
        //***
        $this->_title = 'Диски для ' . $this->mark.' '.$this->model.' '.$this->year.' г/в';
        // *** Установка кук
        $this->abCookie = array(
            'svendor' => @$this->ab->tree['vendor_sname'] ? $this->ab->tree['vendor_sname'] : '', 
            'smodel'  => @$this->ab->tree['model_sname'] ? $this->ab->tree['model_sname'] : '', 
            'syear'   => @$this->ab->tree['year_sname'] ? $this->ab->tree['year_sname'] : '', 
            'smodif'  => @$this->ab->tree['modif_sname'] ? $this->ab->tree['modif_sname'] : ''
        );  
        $this->setCookie('apData', base64_encode(serialize($this->abCookie)));
        //***
        //*** Переназначаем мета-теги и описаия ***//
        $page_meta_info = $this->ab->getOne("SELECT * FROM `ab_podbor_meta` WHERE LD = 0 AND H=0 AND gr='2'
            ".(!empty($this->ab->tree['vendor_id']) ? " AND vendor_id='".$this->ab->tree['vendor_id']."'" : '')."
            ".(!empty($this->ab->tree['model_id']) ? " AND model_id='".$this->ab->tree['model_id']."'" : ' AND model_id=0')."
            ".(!empty($this->ab->tree['year_id']) ? " AND year_id='".$this->ab->tree['year_id']."'" : ' AND year_id=0')."
            ".(!empty($this->ab->tree['modif_id']) ? " AND modif_id='".$this->ab->tree['modif_id']."'" : ' AND modif_id=0')
            ,MYSQL_ASSOC);
        if(!empty($page_meta_info))
        {
            $this->title = $page_meta_info['seo_title'];
            $this->description = $page_meta_info['seo_desc'];
            $this->keywords = $page_meta_info['seo_key'];
            //***
            $this->_title = $page_meta_info['seo_h1'];
            $this->upText = $page_meta_info['text1'];
            $this->h2     = $page_meta_info['seo_h2'];
            $this->dwText = $page_meta_info['text2'];
        }
        $this->show_rating = $this->ab->tree['ext_avto_info']['show_rating'];
        $this->avto_image  = !empty($this->ab->tree['ext_avto_info']['avto_image']) ? '/'.Cfg::get('cc_upload_dir').'/'.$this->ab->tree['ext_avto_info']['avto_image'] : '';
        //*****************************************//
        $this->relink_href = '/' . App_Route::_getUrl('avtoPodborShin') . '/' . App_Route::$param['ap'][0] . '--' . App_Route::$param['ap'][1] . '--' . App_Route::$param['ap'][2] . '.html';
    }


    public function result()
    {

        if (!@$this->ab->tree['modif_id']) return App_Route::redir404();

        //$this->noSidebar = true;
        // собираем все id машинок с такими параметрами
        $this->ab->avto_sh_array(@$this->ab->tree['vendor_id'], @$this->ab->tree['model_id'], @$this->ab->tree['year_id'], Array(2), $this->ab->tree['modif_id']); 
        //
        $this->view('podbordiskov/results');

        $this->mark = $this->ab->tree['vendor_name'];
        $this->mark_alt = $this->ab->tree['vendor_alt'] != '' ? $this->ab->tree['vendor_alt'] : $this->ab->tree['vendor_name'];
        $this->model = $this->ab->tree['model_name'];
        $this->model_alt = $this->ab->tree['model_alt'] != '' ? $this->ab->tree['model_alt'] : $this->ab->tree['model_name'];
        $this->year = $this->ab->tree['year_name'];
        $this->modif = $this->ab->tree['modif_name'];

        $this->aname = $this->ab->tree['vendor_name'] . ' ' . $this->ab->tree['model_name'];
        $this->fname = $this->ab->fname;

        $this->ss->getDoc('avtoPodborDiskov_results$10');
        $this->description = $this->ss->meta['description'];
        $this->keywords = $this->ss->meta['keywords'];
        $this->introText = array(1 => $this->parseText($this->ss->cnt_intro), 2 => $this->parseText($this->ss->cnt_text));

        //$this->ab->avto_sh($this->ab->tree['modif_id']);
        $this->abc = $this->ab->getCommon($this->ab->tree['modif_id']);

        //$this->apText = $this->parse($this->ss->getDoc('avtoPodborDiskov_result$6'));

        $this->breadcrumbs['Подбор дисков по марке авто'] = array('/' . App_Route::_getUrl('avtoPodborDiskovIndex') . '.html', 'подобрать дисков по машине');
        $this->breadcrumbs[$this->mark_alt] = array('/' . App_Route::_getUrl('avtoPodborDiskov') . '/' . App_Route::$param['ap'][0] . '.html', "размеры шин и дисков для {$this->mark}");
        $this->breadcrumbs[$this->model_alt] = array('/' . App_Route::_getUrl('avtoPodborDiskov') . '/' . App_Route::$param['ap'][0] . '--' . App_Route::$param['ap'][1] . '.html', "размеры шин и дисков для {$this->mark} {$this->model}");
        $this->breadcrumbs[$this->year] = array($this->prevUrl = '/' . App_Route::_getUrl('avtoPodborDiskov') . '/' . App_Route::$param['ap'][0] . '--' . App_Route::$param['ap'][1] . '--' . App_Route::$param['ap'][2] . '.html', "размеры шин и дисков для {$this->mark} {$this->model} {$this->year}");
        $this->breadcrumbs[$this->modif] = '';

        @ksort($this->ab->avto[2], SORT_STRING);
        $this->sz2=array();
        ksort($this->ab->avto[2]);
        foreach($this->ab->avto[2] as $c=>$v){
            foreach($v as $vv){
                if($c==20 || $c==25){
                    if(!isset($this->sz2[$vv['P5']])) $this->sz2[$vv['P5']]=array();
                    $this->sz2[$vv['P5']]['OEM'][][1]=$vv;
                }else{
                    $vvv=current($vv);
                    $this->sz2[$vvv['P5']]['Тюннинг'][]=$vv;
                }
            }
        }
        ksort($this->sz2);
        $native_sz = $this->sz2;
        // *** добавление фильтра
        //$this->brands = array_unique(array_merge($this->ab->getBrandsIds(array_keys($this->ab->tree['vendors'])), Array($this->ab->brand_id))); 
        $this->brands = array_unique(array_merge($this->ab->getBrandsIds(), Array($this->ab->brand_id)));
        // ***  Добавляем параметры в класс, как будто их выбрал пользователь
        $this->setClassParams();
        // *** 
        $this->dSearchUrl='/'.App_Route::_getUrl('dSearch').'.html';
        //***  
        $this->brandsFilter(); 
        //***
        $this->diaMore = 1;
        $this->search();
        // Возвращаем $this->sz2 и добавляем в него подсчет из _cat()
        foreach ($native_sz as $i1 => $r){
            foreach ($r as $i2 => $type){
                foreach ($type as $i3 => $elem){
                   if (count($elem) > 1){
                       $native_sz[$i1][$i2][$i3]['exnum'] = $this->sz2[$i1][$i2][$i3]['exnum'];
                   }else{
                       $native_sz[$i1][$i2][$i3][1]['exnum'] = $this->sz2[$i1][$i2][$i3][1]['exnum'];
                   }
                }
            }
        }
        $this->sz2 = $native_sz;
        //
        $this->title = "Диски на {$this->mark} {$this->model} {$this->year} г/в с двигателем {$this->modif}! Подобрать диски онлайн. Типоразмеры R".implode(', R', array_keys($this->sz2)).".";
        // Закомментить, когда сделается нормально через админку
        $this->description = "Диски на {$this->mark} {$this->model} {$this->year} г/в с двигателем {$this->modif}. Подобрать и купить колесные диски в интернет-магазине «Дилижанс». Литые  диски для {$this->mark_alt} ".Tools::replaceMetaInCatalog($this->model)." {$this->year} г/в {$this->modif}. Все типоразмеры: R".implode(', R', array_keys($this->sz2)).".";
        $this->keywords = "Диски {$this->mark} {$this->model} {$this->year} г/в {$this->modif}, подбор дисков {$this->mark} {$this->model} {$this->year} г/в {$this->modif}, подобрать диски для {$this->mark} {$this->model} {$this->year} г/в {$this->modif}, Подбор дисков для {$this->mark} {$this->model} {$this->year} г/в {$this->modif}, Диски {$this->mark} {$this->model} {$this->year} г/в {$this->modif}, литые, колесные";
        //***
        $this->_title = 'Диски для '.$this->mark.' '.$this->model.' '.$this->year.' г/в '.$this->modif.'.';

        $this->extra_meta = '<meta name="robots" content="noindex, follow"/>';
        // *** Установка кук
        $this->abCookie = array(
            'svendor' => @$this->ab->tree['vendor_sname'] ? $this->ab->tree['vendor_sname'] : '', 
            'smodel'  => @$this->ab->tree['model_sname'] ? $this->ab->tree['model_sname'] : '', 
            'syear'   => @$this->ab->tree['year_sname'] ? $this->ab->tree['year_sname'] : '', 
            'smodif'  => @$this->ab->tree['modif_sname'] ? $this->ab->tree['modif_sname'] : ''
        );  
        $this->setCookie('apData', base64_encode(serialize($this->abCookie)));
        //***
        //*** Переназначаем мета-теги и описаия ***//
        $page_meta_info = $this->ab->getOne("SELECT * FROM `ab_podbor_meta` WHERE LD = 0 AND H=0 AND gr='2'
            ".(!empty($this->ab->tree['vendor_id']) ? " AND vendor_id='".$this->ab->tree['vendor_id']."'" : '')."
            ".(!empty($this->ab->tree['model_id']) ? " AND model_id='".$this->ab->tree['model_id']."'" : ' AND model_id=0')."
            ".(!empty($this->ab->tree['year_id']) ? " AND year_id='".$this->ab->tree['year_id']."'" : ' AND year_id=0')."
            ".(!empty($this->ab->tree['modif_id']) ? " AND modif_id='".$this->ab->tree['modif_id']."'" : ' AND modif_id=0')
            ,MYSQL_ASSOC);
                if(!empty($page_meta_info))
                {
                    $this->title = $page_meta_info['seo_title'];
                    $this->description = $page_meta_info['seo_desc'];
                    $this->keywords = $page_meta_info['seo_key'];
                    //***
                    $this->_title = $page_meta_info['seo_h1'];
                    $this->upText = $page_meta_info['text1'];
                    $this->h2     = $page_meta_info['seo_h2'];
                    $this->dwText = $page_meta_info['text2'];
                }
                $this->show_rating = $this->ab->tree['ext_avto_info']['show_rating'];
                $this->avto_image  = !empty($this->ab->tree['ext_avto_info']['avto_image']) ? '/'.Cfg::get('cc_upload_dir').'/'.$this->ab->tree['ext_avto_info']['avto_image'] : '';
        //*****************************************//
        $this->relink_href = '/' . App_Route::_getUrl('avtoPodborShin') . '/' . App_Route::$param['ap'][0] . '--' . App_Route::$param['ap'][1] . '--' . App_Route::$param['ap'][2] . '--' . App_Route::$param['ap'][3] . '.html';
    }


    // ********************************************************************** МЕТОДЫ ДЛЯ ФИЛЬТРА ******************************************************************************
    public function search()
    {
        $this->_routeParam();

        $this->ab->getTree($this->abCookie);
        $this->ab->avto_sh_array(@$this->ab->tree['vendor_id'], @$this->ab->tree['model_id'], @$this->ab->tree['year_id'], Array(2));

        // тип отображения результатов поиска
        if(@$_COOKIE['stype']=='lenta') $this->searchTpl='catalog/disks/searchLenta'; else $this->searchTpl='catalog/disks/searchBlock';

        if(true!==($res=$this->_cat())) return $res;

        reset($this->P46);
        reset($this->P1);
        reset($this->P2);
        reset($this->P3);
        reset($this->P4);
        reset($this->P5);
        reset($this->P6);
        reset($this->brands);

        $this->size='';

        if(count($this->P2)==1 && count($this->P5)==1)
            $this->size.=' '.current($this->P2).'x'.current($this->P5);
        elseif(count($this->P5)==1)
            $this->size.=' R'.current($this->P5);

        if(count($this->P46)==1) {
            $sv=current($this->P46);
            $this->size.=' '.$sv[0].'x'.$sv[1];
        }else
            if(count($this->P4)==1 && count($this->P6)==1) $this->size.=' '.current($this->P4).'x'.current($this->P6);

            if(!empty($this->P1)) $this->size.=' ET '.implode(', ',$this->P1);

        if(!empty($this->P3)) $this->size.=' DIA '.implode(', ',$this->P3);

        $this->size=trim($this->size);

        $parama=''; // replica,brand,R,sv
        if(empty($this->P1) && empty($this->P2) && empty($this->P3)){
            if(!empty($this->replica)) $parama.='1'; else $parama.='0';
            if(!empty($this->brand_id)) $parama.='1'; else $parama.='0';
            if(count($this->P5)==1) {
                $parama.='1';
                $P5=current($this->P5);
            } else $parama.='0';
            if(!empty($this->P4) && !empty($this->P6) || count($this->P46)==1) {
                $parama.='1';
                if(!empty($this->P4) && !empty($this->P6)){
                    $sv1=current($this->P4).'x'.Tools::n(current($this->P6));
                    $sv2=current($this->P4).'*'.Tools::n(current($this->P6));
                }else{
                    $sv=current($this->P46);
                    $sv1=$sv[0].'x'.$sv[1];
                    $sv2=$sv[0].'*'.$sv[1];
                }
            } else $parama.='0';
        }

        if($this->replica){

            if($parama==='1110'){

                $this->title=Tools::cutDoubleSpaces("Литые диски Replica {$this->bname} R$P5. Каталог дисков $P5 радиус для {$this->bname} в интернет магазине. ");
                $this->_title=Tools::cutDoubleSpaces("Литые диски Replica R$P5 для {$this->bname}.");
                $this->keywords=Tools::cutDoubleSpaces("литые диски {$this->bname} R$P5");
                $this->description=Tools::cutDoubleSpaces("Диски для автомобиля {$this->bname} $P5 радиус по низким ценам в интернет магазине Дилижанс. Широкий ассортимент дисков R$P5 реплика для авто {$this->bname}. Доставка литых дисков {$this->bname} $P5 радиус по все территории России:  Москва, Санкт-Петербург, Екатеринбург, Уфа, Воронеж и т.д.");

            }else{
                $this->title=Tools::cutDoubleSpaces("Диски реплика {$this->bname} {$this->size}");
                $this->_title=Tools::cutDoubleSpaces("Результаты поиска: диски Replica {$this->bname} {$this->size}");
                $this->keywords=Tools::cutDoubleSpaces("колесные диски реплика replica {$this->bname} {$this->size}");
                $this->description=Tools::cutDoubleSpaces("колесные литые диски реплика (replica) {$this->bname} {$this->size}, здесь вы можете выбрать и купить диски реплика {$this->balt1} {$this->size}");


            }

            $this->breadcrumbs['Диски реплика']=array('/'.App_Route::_getUrl('replicaCat').'.html','');
            if($this->brand_id)
                $this->breadcrumbs[$this->bname]=array('/'.App_Route::_getUrl('dCat').'/'.$this->brand_sname.'.html','');

        }else{

            //$this->breadcrumbs['диски']=array('/'.App_Route::_getUrl('dCat').'.html','купить литые диски');

            if(!empty($this->ab->fname) && !empty(Url::$sq['ap'])){
                $this->title=Tools::cutDoubleSpaces("Диски {$this->bname} {$this->size} для {$this->ab->fname}");
                $this->_title=Tools::cutDoubleSpaces("Диски {$this->bname} {$this->size} для {$this->ab->fname}");

                $this->keywords=Tools::cutDoubleSpaces("литые диски replica {$this->bname} {$this->size}");
                $this->description=Tools::cutDoubleSpaces("литые диски {$this->bname} {$this->size}, здесь вы можете выбрать и купить диски {$this->balt1} {$this->size}");

            }else{

                // радиус
                if($parama==='0010'){

                    $this->title=Tools::cutDoubleSpaces("Литые диски R$P5 по низким ценам. Продажа колесных дисков $P5 радиус в интернет магазине Дилижанс.");
                    $this->_title=Tools::cutDoubleSpaces("Каталог литых дисков $P5 радиуса - R$P5: литые, кованные и штампованные диски.");
                    $this->keywords=Tools::cutDoubleSpaces("литые диски R$P5");
                    $this->description=Tools::cutDoubleSpaces("Широкий выбор литых дисков R$P5 в интернет магазине по привлекательным ценам. Огромный ассортимент литых дисков $P5 радиуса от разных мировых производителей. Заказывайте колесные диски: литые R$P5, кованные R$P5, штампованные R $P5 -  доставка по России в города: Москва, Санкт-Петербург, Воронеж, Екатеринбург, Нижний Новгород, Уфа, Казань и др.");

                }elseif($parama==='0001'){

                    $this->title=Tools::cutDoubleSpaces("Диски литые размера $sv1 по низкой цене. Продажа колесных дисков сверловка $sv1 в интернет магазине Дилижанс.");
                    $this->_title=Tools::cutDoubleSpaces("Каталог литых дисков со сверловкой $sv1 (размер)");
                    $this->keywords=Tools::cutDoubleSpaces("литые диски $sv1");
                    $this->description=Tools::cutDoubleSpaces("Огромный выбор литых дисков размера $sv1 в интернет магазине Дилижанс по привлекательным ценам.  Вы можете заказать колесные диски со сверловкой $sv1, доставка осуществляется по всем городам России: Москва, Санкт-Петербург, Екатеринбург, Уфа, Воронеж и т.д.");

                }elseif($parama==='0110'){

                    $this->title=Tools::cutDoubleSpaces("Диски литые {$this->bname} R $P5 по привлекательным ценам. Колесные диски {$this->bname} $P5 радиус в интернет магазине Дилижанс.");
                    $this->_title=Tools::cutDoubleSpaces("Каталог литых дисков {$this->bname} $P5 радиус - R$P5");
                    $this->keywords=Tools::cutDoubleSpaces("литые диски {$this->bname} {$this->size}");
                    $this->description=Tools::cutDoubleSpaces("Выбирайте лучшие диски по параметрам, производитель {$this->bname} радиус колеса R$P5. Каталог литых дисков {$this->bname} R$P5, доставка осуществляется по всей территории России и страны СНГ: Москва, Минск, Киев, Санкт-Петербург, и многие другие города.");

                }elseif($parama==='0101'){

                    $this->title=Tools::cutDoubleSpaces("Литые диски {$this->bname} $sv1 по низкой цене. Колесные диски {$this->bname} сверловка $sv1 в интернет магазине Дилижанс.");
                    $this->_title=Tools::cutDoubleSpaces("Каталог литых дисков {$this->bname} размер $sv1");
                    $this->keywords=Tools::cutDoubleSpaces("литые диски {$this->bname} {$this->size}");
                    $this->description=Tools::cutDoubleSpaces("Выбирайте лучшие диски по параметрам, производитель {$this->bname} размер колеса $sv1. Каталог литых дисков {$this->bname} сверловка $sv1, доставка осуществляется по всей территории России и страны СНГ: Москва, Минск, Киев, Санкт-Петербург, и многие другие города.");

                }elseif($parama==='0011'){

                    $this->title=Tools::cutDoubleSpaces("Литые диски R$P5 размер $sv1 по низкой цене. Колесные диски $P5 радиус и сверловка $sv1 в интернет магазине Дилижанс.");
                    $this->_title=Tools::cutDoubleSpaces("Каталог литых дисков $P5 радиус и размер $sv1");
                    $this->keywords=Tools::cutDoubleSpaces("литые диски $P5 $sv1");
                    $this->description=Tools::cutDoubleSpaces("Каталог дисков по параметрам $P5 радиус и размер $sv1 от лучших мировых производителей. Доставка литых дисков осуществляется по всей территории России и страны СНГ: Москва, Минск, Киев, Санкт-Петербург, и многие другие города.");

                }elseif($parama==='0111'){

                    $this->title=Tools::cutDoubleSpaces("Литые диски {$this->bname} R$P5 размер $sv1. Колесные диски {$this->bname} $P5 радиус и сверловка $sv1 в интернет магазине Дилижанс.");
                    $this->_title=Tools::cutDoubleSpaces("Литые диски {$this->bname} $P5 радиус и размер $sv1");
                    $this->keywords=Tools::cutDoubleSpaces("литые диски {$this->bname} $P5 $sv1");
                    $this->description=Tools::cutDoubleSpaces("Каталог дисков по параметрам производитель {$this->bname}, $P5 радиус и размер $sv1 от лучших мировых производителей. Доставка литых дисков осуществляется по всей территории России и страны СНГ: Москва, Минск, Киев, Санкт-Петербург, и многие другие города.");

                    // все остальное
                }else{
                    $this->title=Tools::cutDoubleSpaces("Диски {$this->bname} {$this->size}");
                    $this->_title=Tools::cutDoubleSpaces("Диски {$this->bname} {$this->size}");
                    $this->keywords=Tools::cutDoubleSpaces("литые диски {$this->bname} {$this->size}");
                    $this->description=Tools::cutDoubleSpaces("литые диски {$this->bname} {$this->size}, здесь вы можете выбрать и купить диски {$this->balt1} {$this->size}");
                }

            }


            if($this->brand_id)
                $this->breadcrumbs[$this->bname]=array('/'.App_Route::_getUrl('dCat').'/'.$this->brand_sname.'.html','литые диски '.$this->balt);
        }

        if($parama==='0011'){
            $this->breadcrumbs["R$P5"]=array('/'.App_Route::_getUrl('dSearch').'?p5='.$P5, '');
            $this->breadcrumbs["$sv1"]='';
        }else
        {
            // Добавить параметры к хлебным крошкам
            //$this->breadcrumbs["{$this->size}"]='';
        }
        if(empty($this->cat)) $this->qtext="Дисков  {$this->bname} {$this->size} в данный момент нет в наличии";


        $this->_sidebar();
    }


    /*
    * ЗАПОЛНЕНИЕ ПЕРЕМЕННЫХ ГЕТ ПАРАМЕТРАМИ ДЛЯ ПОИСКА
    */
    private function _routeParam()
    {
        if(!empty(App_Route::$param['P1'])) $this->P1[]=App_Route::$param['P1'];
        elseif(@is_array(Url::$sq['p1'])) $this->P1=array_keys(Url::$sq['p1']);
        elseif(in_array(Tools::typeOf(@Url::$sq['p1']),array('float','integer'))) $this->P1=array(Url::$sq['p1']);

        if(isset(App_Route::$param['P2']) && App_Route::$param['P2']!=='') $this->P2[]=App_Route::$param['P2'];
        elseif(@is_array(Url::$sq['p2'])) $this->P2=array_keys(Url::$sq['p2']);
        elseif(in_array(Tools::typeOf(@Url::$sq['p2']),array('float','integer'))) $this->P2=array(Url::$sq['p2']);

        if(!empty(App_Route::$param['P3'])) $this->P3[]=App_Route::$param['P3'];
        elseif(@is_array(Url::$sq['p3'])) $this->P3=array_keys(Url::$sq['p3']);
        elseif(in_array(Tools::typeOf(@Url::$sq['p3']),array('float','integer'))) $this->P3=array(Url::$sq['p3']);

        if(!empty(App_Route::$param['P4'])) $this->P4[]=App_Route::$param['P4'];
        elseif(@is_array(Url::$sq['p4'])) $this->P4=array_keys(Url::$sq['p4']);
        elseif(in_array(Tools::typeOf(@Url::$sq['p4']),array('float','integer'))) $this->P4=array(Url::$sq['p4']);

        if(!empty(App_Route::$param['P5'])) $this->P5[]=App_Route::$param['P5'];
        elseif(@is_array(Url::$sq['p5'])) $this->P5=array_keys(Url::$sq['p5']);
        elseif(in_array(Tools::typeOf(@Url::$sq['p5']),array('float','integer'))) $this->P5=array(Url::$sq['p5']);

        if(!empty(App_Route::$param['P6'])) $this->P6[]=App_Route::$param['P6'];
        elseif(@is_array(Url::$sq['p6'])) $this->P6=array_keys(Url::$sq['p6']);
        elseif(in_array(Tools::typeOf(@Url::$sq['p6']),array('float','integer'))) $this->P6=array(Url::$sq['p6']);

        if(@is_array(Url::$sq['sv'])){
            foreach(Url::$sq['sv'] as $k=>$v){
                if(preg_match("/([0-9]+)[\*xX]{1}([0-9\.]+)/",$k,$m)){
                    $this->P46[]=array($m[1],$m[2]);
                }
            }
        }elseif(@Url::$sq['sv']!='') {
            if(preg_match("/([0-9]+)[\*xX]{1}([0-9\.]+)/",Url::$sq['sv'],$m)){
                $this->P46[]=array($m[1],$m[2]);
            }
        }

        if(!empty(Url::$sq['vendor'])){
            $this->cc->que('brand_by_sname',Url::$sq['vendor'],1,2);
            if(!$this->cc->qnum()) return App_Route::redir404();
            $this->cc->next();
            $this->brand_id=$this->cc->qrow['brand_id'];
            $this->bname=Tools::unesc($this->cc->qrow['name']);
            $this->balt=Tools::unesc($this->cc->qrow['alt']!=''?$this->cc->qrow['alt']:$this->cc->qrow['name']);
            $this->balt1=$this->firstS($this->balt);
            $this->baltOther=$this->otherS($this->balt);
            $this->brand_sname=$this->cc->qrow['sname'];
            $this->replica=$this->cc->qrow['replica'];
        }

        if(empty($this->brand_id)) {
            if(@is_array(Url::$sq['bids'])) $this->brands=array_keys(Url::$sq['bids']);
            elseif(Tools::typeOf(@Url::$sq['bids'])=='integer') $this->brands=array(Url::$sq['bids']);
        } else $this->brands=array($this->brand_id);

        if(@Url::$sq['replica']) $this->replica=1;
        if(@Url::$sq['ap']) $this->apMode=1;

        // спарки
        $this->sMode=false;
        /*
        * долдны быть явно заданы: R, J
        * если не передан диаметр, сверловка, dia,  задней оси  - они будет равны предним значениям
        * TODO с вылетом не понятно
        */
        //        if(count($this->P2)==1 && count($this->P3)==1 && (count($this->P4)==1  && count($this->P6)==1 || count($this->P46)==1) && count($this->P5)==1){
        if(count($this->P2)==1 && count($this->P5)==1){
            if(in_array(Tools::typeOf(@Url::$sq['p1_']),array('float','integer'))) $this->sP1=array(Url::$sq['p1_']);
            if(in_array(Tools::typeOf(@Url::$sq['p2_']),array('float','integer'))) $this->sP2=array(Url::$sq['p2_']);
            if(in_array(Tools::typeOf(@Url::$sq['p3_']),array('float','integer'))) $this->sP3=array(Url::$sq['p3_']);
            if(in_array(Tools::typeOf(@Url::$sq['p4_']),array('float','integer'))) $this->sP4=array(Url::$sq['p4_']);
            if(in_array(Tools::typeOf(@Url::$sq['p5_']),array('float','integer'))) $this->sP5=array(Url::$sq['p5_']);
            if(in_array(Tools::typeOf(@Url::$sq['p6_']),array('float','integer'))) $this->sP6=array(Url::$sq['p6_']);
            if(!empty($this->sP2)){
                if(!empty($this->P46)){
                    $a=array_shift($this->P46);
                    $this->P4=array($a[0]);
                    $this->P6=array($a[1]);
                    $this->sP4=$this->P4;
                    $this->sP6=$this->P6;
                    $this->P46=array();
                }
                if(empty($this->sP5)) $this->sP5=$this->P5;
                if(empty($this->sP4)) $this->sP4=$this->P4;
                if(empty($this->sP6)) $this->sP6=$this->P6;
                if(empty($this->sP3)) $this->sP3=$this->P3;

                $this->sMode=true;
            }
        }

        //уточняющие параметры
        if(@is_array(Url::$sq['_p1'])) $this->_P1=array_keys(Url::$sq['_p1']);
        elseif(in_array(Tools::typeOf(@Url::$sq['_p1']),array('float','integer'))) $this->_P1=array(Url::$sq['_p1']);

        if(@is_array(Url::$sq['_p2'])) $this->_P2=array_keys(Url::$sq['_p2']);
        elseif(in_array(Tools::typeOf(@Url::$sq['_p2']),array('float','integer'))) $this->_P2=array(Url::$sq['_p2']);

        if(@is_array(Url::$sq['_p3'])) $this->_P3=array_keys(Url::$sq['_p3']);
        elseif(in_array(Tools::typeOf(@Url::$sq['_p3']),array('float','integer'))) $this->_P3=array(Url::$sq['_p3']);

        if(@is_array(Url::$sq['_p4'])) $this->_P4=array_keys(Url::$sq['_p4']);
        elseif(in_array(Tools::typeOf(@Url::$sq['_p4']),array('float','integer'))) $this->_P4=array(Url::$sq['_p4']);

        if(@is_array(Url::$sq['_p5'])) $this->_P5=array_keys(Url::$sq['_p5']);
        elseif(in_array(Tools::typeOf(@Url::$sq['_p5']),array('float','integer'))) $this->_P5=array(Url::$sq['_p5']);

        if(@is_array(Url::$sq['_p6'])) $this->_P3=array_keys(Url::$sq['_p6']);
        elseif(in_array(Tools::typeOf(@Url::$sq['_p6']),array('float','integer'))) $this->_P6=array(Url::$sq['_p6']);

        if(@is_array(Url::$sq['_bids'])) $this->_brands=array_keys(Url::$sq['_bids']);
        elseif(Tools::typeOf(@Url::$sq['_bids'])=='integer') $this->_brands=array(Url::$sq['_bids']);


        if(@is_array(Url::$sq['_sv'])){
            foreach(Url::$sq['_sv'] as $k=>$v){
                if(preg_match("/([0-9]+)[\*x]{1}([0-9\.]+)/",$k,$m)){
                    $this->_P46[]=array($m[1],$m[2]);
                }
            }
        }elseif(@Url::$sq['_sv']!='') {
            if(preg_match("/([0-9]+)[\*x]{1}([0-9\.]+)/",Url::$sq['_sv'],$m)){
                $this->_P46[]=array($m[1],$m[2]);
            }
        }

        if(!empty(Url::$sq['_vendor'])){
            $this->cc->que('brand_by_sname',Url::$sq['_vendor'],1,2);
            if($this->cc->qnum()) {
                $this->cc->next();
                $this->_brand_id=$this->cc->qrow['brand_id'];
            }
        }
        if(empty($this->_brand_id)) {
            if(@is_array(Url::$sq['_bids'])) $this->_brands=array_keys(Url::$sq['_bids']);
            elseif(Tools::typeOf(@Url::$sq['_bids'])=='integer') $this->_brands=array(Url::$sq['_bids']);
        }else $this->_brands=array($this->_brand_id);

        // Хак для доступности поиска  по размерам
        //$this->valid_radiuses = array_unique($this->P5);
        if (!empty($this->_P5)) 
        {     
            $this->P5 = array_intersect($this->P5,$this->_P5);
        }
        if (!empty($this->_brands)) 
        {     
            $this->brands = array_intersect($this->brands, $this->_brands);
        }
        // все параметры в кучу
        $this->P1_=array_unique(array_merge($this->P1,$this->_P1));
        $this->P2_=array_unique(array_merge($this->P2,$this->_P2));
        $this->P3_=array_unique(array_merge($this->P3,$this->_P3));
        $this->P4_=array_unique(array_merge($this->P4,$this->_P4));
        $this->P5_=array_unique(array_merge($this->P5,$this->_P5));
        $this->P6_=array_unique(array_merge($this->P6,$this->_P6));
        $this->P46_=(array_merge($this->P46,$this->_P46));
        $this->brands_=array_unique(array_merge($this->brands,$this->_brands));

    }

    /*
    * получение массива с размерами и фильтров
    */
    private function _cat()
    {
        $this->cat=array();
        $this->num=0;
        $this->lf=array();
        $this->lfi=0;
        $this->lfh=array();

        $r=array(
            'gr'=>2,
            'notH'=>1,
            'where'=>array(),
            'exFields'=>array(),
            'select'=>'',
            'having'=>array()
        );

        if(empty($this->P5) || true) $r['exFields']['P5']=array(); else $r['P5']=array('list'=>$this->P5); // радиус
        if(empty($this->brands) || true) $r['exFields']['brand']=array(); else $r['brand_id']=array('list'=>$this->brands); // бренд
        if(!empty($this->P2)) $r['P2']=array('list'=>$this->P2);
        if(!empty($this->P4)) $r['P4']=array('list'=>$this->P4);
        if(!empty($this->P6)) $r['P6']=array('list'=>$this->P6);
        // ***** сообщение запроса
        if (!empty($_REQUEST['submited']) || Tools::getCookie('search_q_info_str')) {
            if (!Tools::getCookie('search_q_info_str')) {
                Tools::setCookie('search_q_info_str', 1);
                header('Location: ' . preg_replace('/\??\&?submited\=1/i', '',$_SERVER['REQUEST_URI']));
                exit(200);
            }
            else{
                Tools::delCookie('search_q_info_str');
            }
            $this->s_info_str = '<div class="search_q_info_str">';
            $this->s_info_str .= trim("По вашему запросу: Диски для {$this->mark} {$this->model} " . ($this->year ? $this->year . ' г/в' : '') . " {$this->modif}");
            $this->s_info_str .= (!empty($this->_P5) ? ', диаметр - '.implode(', ', $this->_P5) : '');
            if (!empty($this->_brands)) {
                $sel_b_data = Array();
                $f_brands = array_filter($this->_brands, function ($val) {
                    return !empty($val);
                });
                $sel_b = $this->cc->fetchAll("SELECT name FROM cc_brand WHERE brand_id IN (".implode(',', $f_brands).") LIMIT 0, 5", MYSQL_ASSOC);
                foreach ($sel_b as $sb){
                    $sel_b_data[] = $sb['name'];
                }
                $this->s_info_str .= (!empty($sel_b_data) ? ', бренды - ' . implode(', ', $sel_b_data) : '');
            }
            $this->s_info_str .= '</div>';
        }
        // *****
        if(!empty($this->P3)) 
            if($this->apMode){     
                $r['P3']=array('from'=>(float)min($this->P3)+$this->_deltaDia,'to'=>'', 'ext_or_eq' => 0);
            } else $r['P3']=array('list'=>$this->P3);

        //  Костыль для вывода вылета (ET) с учетом радиусов // avcode
        @ksort($this->ab->avto[2], SORT_STRING);
        $this->sz2=array();

        $r['sqlReturn']=0;
        $r['nolimits']=1;
        $r['ex']=1;

        if($this->replica==1) $r['where'][]="cc_brand.replica=1";
        if($this->hideDSCZero) $r['where'][]=$this->minQtyRadiusSQL;
        $apMode = $this->apMode;
        $this->apMode = 0; // Сохраняем и отключаем apMode для точного подбора по параметрам
        $CC_BASE = new CC_Base();
        ksort($this->ab->avto[2]);
        foreach($this->ab->avto[2] as $c=>$v){
            foreach($v as $vv){
                if($c==20 || $c==25){

                    $rr=array(
                        'gr'=>2,
                        'notH'=>1,
                        'where'=>array(),
                        'exFields'=>array(),
                        'select'=>'',
                        'having'=>array()
                    );

                    if(empty($this->brands) || true) $rr['exFields']['brand']=array(); else $rr['brand_id']=array('list'=>(array)$this->brands); // бренд
                    if(!empty($vv['P1']))
                        if($this->apMode){
                            $rr['P1']=array('from'=>(float)$vv['P1']+$this->_deltaET,'to'=>(float)$vv['P1']+$this->deltaET_);
                        }
                        else $rr['P1']=array('list'=>(array)$vv['P1']);
                    if(!empty($vv['P2'])) $rr['P2']=array('list'=>(array)$vv['P2']);
                    if(!empty($vv['P3']))
                        if($this->apMode || @$this->diaMore){
                            $rr['P3']=array('from'=>(float)$vv['P3']+$this->_deltaDia,'to'=>'');
                        } else $rr['P3']=array('list'=>(array)$vv['P3']);
                    if(!empty($vv['P4'])) $rr['P4']=array('list'=>(array)$vv['P4']);
                    if(!empty($vv['P5'])) $rr['P5']=array('list'=>(array)$vv['P5']); // радиус
                    if(!empty($vv['P6'])) $rr['P6']=array('list'=>(array)$vv['P6']);

                    $rr['sqlReturn']=0;
                    $rr['nolimits']=1;
                    $rr['ex']=1;

                    if($this->replica==1) $rr['where'][]="cc_brand.replica=1";
                    if($this->hideDSCZero) $rr['where'][]=$this->minQtyRadiusSQL;

                    $exnum=$CC_BASE->cat_view($rr);
                    if(!isset($this->sz2[$vv['P5']])) $this->sz2[$vv['P5']]=array();
                    $vv['exnum'] = $exnum;
                    $this->sz2[$vv['P5']]['OEM'][][1]=$vv;
                }else{

                    $rr=array(
                        'gr'=>2,
                        'notH'=>1,
                        'where'=>array(),
                        'exFields'=>array(),
                        'select'=>'',
                        'having'=>array()
                    );

                    if(!empty($this->brands))   $rr['brand_id']=array('list'=>(array)$this->brands);
                    if(!empty($vv[1]['P1']))
                        if($this->apMode){
                            $rr['P1']=array('from'=>(float)$vv[1]['P1']+$this->_deltaET,'to'=>(float)$vv[1]['P1']+$this->deltaET_);
                        }
                        else $rr['P1']=array('list'=>(array)$vv[1]['P1']);
                    $rr['P2'] = array('list'=>(array)$vv[1]['P2']);
                    if(!empty($vv[1]['P3']))
                        if($this->apMode){
                            $rr['P3']=array('from'=>(float)$vv[1]['P3']+$this->_deltaDia,'to'=>'');
                        } else $rr['P3']=array('list'=>(array)$vv[1]['P3']);
                    $rr['P4'] = array('list'=>(array)$vv[1]['P4']);
                    $rr['P5'] = array('list'=>(array)$vv[1]['P5']);
                    $rr['P6'] = array('list'=>(array)$vv[1]['P6']);

                    $rr['sqlReturn']=0;
                    $rr['nolimits']=1;
                    //$rr['ex']=1;

                    if($this->hideDSCZero) $rr['where'][]=$this->minQtyRadiusSQL;

                    $exnum1=$CC_BASE->cat_view($rr);
                    $r1=$CC_BASE->fetchAll('',MYSQL_ASSOC);



                    $rr=array(
                        'gr'=>2,
                        'notH'=>1,
                        'where'=>array(),
                        'exFields'=>array(),
                        'select'=>'',
                        'having'=>array()
                    );

                    if(!empty($this->brands))   $rr['brand_id']=array('list'=>(array)$this->brands);
                    if(!empty($vv[2]['P1']))
                        if($this->apMode){
                            $rr['P1']=array('from'=>(float)$vv[2]['P1']+$this->_deltaET,'to'=>(float)$vv[2]['P1']+$this->deltaET_);
                        }
                        else $rr['P1']=array('list'=>(array)$vv[2]['P1']);
                    $rr['P2'] = array('list'=>(array)$vv[2]['P2']);
                    if(!empty($vv[2]['P3']))
                        if($this->apMode){
                            $rr['P3']=array('from'=>(float)$vv[2]['P3']+$this->_deltaDia,'to'=>'');
                        } else $rr['P3']=array('list'=>(array)$vv[2]['P3']);
                    $rr['P4'] = array('list'=>(array)$vv[2]['P4']);
                    $rr['P5'] = array('list'=>(array)$vv[2]['P5']);
                    $rr['P6'] = array('list'=>(array)$vv[2]['P6']);

                    $rr['sqlReturn']=0;
                    $rr['nolimits']=1;
                    //$rr['ex']=1;

                    if($this->hideDSCZero) $rr['where'][]=$this->minQtyRadiusSQL;

                    $exnum2=$CC_BASE->cat_view($rr);
                    $r2=$CC_BASE->fetchAll('',MYSQL_ASSOC);
                    $cat = array();
                    foreach($r1 as $v1){
                        foreach($r2 as $v2){
                            if($v1['brand_id']==$v2['brand_id'])
                                if($v1['model_id']==$v2['model_id'])
                                    if($v1['P3']==$v2['P3'])
                                        if($v1['P4']==$v2['P4'])
                                            if($v1['P6']==$v2['P6'])
                                                if(Tools::mb_strcasecmp(Tools::unesc($v1['csuffix']), Tools::unesc($v2['csuffix']))===0){
                                                    $cat[]=1;
                                                }
                        }
                    }
                    $exnum=count($cat);
                    $vvv=current($vv);
                    $vv['exnum'] = $exnum;
                    $this->sz2[$vvv['P5']]['Тюннинг'][]=$vv;
                }
            }
        }
        unset($CC_BASE);
        $this->apMode = $apMode; // Возвращаем apMode на всякий случай
        ksort($this->sz2);
        if(!$this->is_unic_p46())
        {     
            $this->generateRestrictions ($r);
        }
        // *************************************************
        if(!empty($this->P46)) {
            $a=array();
            foreach($this->P46 as $v) {
                $v=array(floatval($v[0]),floatval($v[1]));
                $a[]="P4 = '{$v[0]}' AND P6 = '{$v[1]}'";
            }
            if(!empty($a)) $r['where'][]='('.implode(' OR ',$a).')';
        }else $r['exFields']['P46']=array();

        if($this->replica==1) $r['where'][]="cc_brand.replica=1";
        if($this->hideDSCZero) $r['where'][]=$this->minQtyRadiusSQL;

        if(!empty($this->_where)) $r['where']=array_merge($r['where'],$this->_where);
        if(!empty($this->_whereCat)) $r['where']=array_merge($r['where'],$this->_whereCat);
        if(!empty($this->_having)) $r['having']=array_merge($r['having'],$this->_having);

        $r['order']='cc_brand.pos DESC';
        $r['sqlReturn']=0;
        $r['nolimits']=1;
        $r['ex']=1;

        $this->exnum = $this->cc->cat_view($r);
        //echo $this->cc->sql_query;
        //print_r($this->cc->ex_arr['brand']);

        $this->cc->sqlFree();
        $this->ex=$this->cc->ex_arr;
        unset($this->cc->ex_arr,$this->ex['P5'][0],$this->ex['P1'][0],$this->ex['P46']["0-0"]);

        if(!$this->exnum) {
            GA::_event('Other','searchDisksNoResult',ltrim(@$_SERVER['REQUEST_URI'],'/'),'',true);
            return true;
        }

        //генерим живую форму уточняющего фильтра  
        $this->lf['valid_brands'] = $this->valid_brands;
        if((count(@$this->ex['brand'][0])+count(@$this->ex['brand']['replica']))>1){
            if(count(@$this->ex['brand'][0])){
                $this->lfi++;
                $this->lf['_bids']=array();
                $si='/'.App_Route::_getUrl('dCat');
                foreach($this->ex['brand'][0] as $k=>$v){
                    $this->lf['_bids'][$k]=array(
                        'chk'=>in_array($k,$this->_brands)?true:false,
                        'anc'=>$v['name'],
                        'id'=>'_bids'.$k,
                        'url'=>$si."/".$v['sname'].'.html'
                    );
                }
            }
            if(count(@$this->ex['brand']['replica'])){
                $this->lfi++;
                if(!isset($this->lf['_rbids'])) $this->lf['_rbids']=array();
                $si='/'.App_Route::_getUrl('dCat');
                foreach($this->ex['brand']['replica'] as $k=>$v)
                {
                    if ($this->ab->brand_id == $k)
                    {
                        $this->lf['_rbids'][$k]=array(
                            'chk'=>in_array($k,$this->_brands)?true:false,
                            'anc'=>$v['name'],
                            'id'=>'_bids'.$k,
                            'url'=>$si."/".$v['sname'].'.html'
                        );
                    }
                }
            }
        }
        if(count(@$this->ex['P5'])>0){
            $this->lfi++;
            ksort($this->ex['P5']);
            $this->lf['_p5']=array();
            $si=App_Route::_getUrl('dSearch').'?';
            foreach($this->ex['P5'] as $k=>$v)
            {
                if (in_array($this->makeId($k), $this->valid_radiuses))
                {
                    $this->lf['_p5'][$k]=array(
                        'chk'=>in_array($k,$this->_P5)?true:false,
                        'anc'=>"R$k",
                        'id'=>'_p5'.$this->makeId($k),
                        'url'=>$si.'p5='.$k
                    );  
                } 
            }
        }

        if(count(@$this->ex['P46'])>1)
        {
            $this->lfi++;
            uksort($this->ex['P46'], array($this,'usortSVfoo'));
            $this->lf['_sv']=array();
            $si=App_Route::_getUrl('dSearch').'?';
            $p46=array();
            foreach($this->_P46 as $k=>$v) $p46[]="{$v[0]}*{$v[1]}";
            foreach($this->ex['P46'] as $k=>$v) if($k>0) {              
                $this->lf['_sv'][$k]=array(
                    'chk'=>in_array($k,$p46)?true:false,
                    'anc'=>$k,
                    'id'=>'_sv'.$this->makeId($k),
                    'url'=>$si.'sv='.$k
                );    
            }
        }

        // добавляем гет параметры в форму как hidden
        foreach(Url::$sq as $k=>$v)
            if(in_array($k,array('p1','p2','p3','p4','p5','p6','sv','ap','replica','vendor','q')))
                if(is_array($v)){
                    foreach($v as $k1=>$v1) $this->lfh["{$k}[{$k1}]"]=$v1;
                }else  $this->lfh[$k]=$v;


            $r=array(
            'gr'=>2,
            'notH'=>1,
            'where'=>array(),
            'order'=>array(),
            'having'=>array(),
            'scDiv'=>1,
            'cpriceDiv'=>1
        );

        $this->limits=Data::get('cc_tipoNumList');
        if(!empty($this->limits)) $this->limits=explode(',',$this->limits); else $this->limits=[];
        $this->limit=0;
        /*if(!empty($_GET['num'])){
            $this->setCookie('lim2', (int)$_GET['num']);
        }
        if(!empty($_COOKIE['lim2'])){
            $this->limit=(int)$_COOKIE['lim2'];
            if(!in_array($this->limit,$this->limits)) $this->limit=0;
        }*/
        if(!empty($_GET['num'])){
            $this->limit=(int)$_GET['num'];
        }
        if(empty($this->limit)) $this->limit=(int)Data::get('cc_tipoNum');

        $this->page=@(int)Url::$sq['page'];
        $r['start']=max(0,$this->page*$this->limit-$this->limit);
        $r['lines']=$this->limit;

        $r['order'][]='scDiv DESC';
        $r['order'][]='cpriceDiv DESC';

        if(isset($_GET['ord'])){
            $this->setCookie('ord2', (int)$_GET['ord']);
        }
        if(isset($_COOKIE['ord2'])) $this->sortBy=$_COOKIE['ord2']; else  $this->sortBy=0;

        switch($this->sortBy){
            default:
                //$r['order'][]='cc_brand.name,cc_model.name,cc_cat.P5';
                $r['order'][]='m_pos ASC, cc_brand.replica DESC, cc_brand.pos DESC';
                break;
            case 1:
                $r['order'][]='cc_brand.name, cc_model.name,cc_cat.P5';
                break;
            case -1:
                $r['order'][]='cc_brand.name DESC, cc_model.name DESC,cc_cat.P5';
                break;
            case 2:
                $r['order'][]='cc_cat.cprice ASC, cc_brand.pos DESC, m_pos ASC, cc_model.name,cc_cat.P5';
                break;
            case -2:
                $r['order'][]='cc_cat.cprice DESC, cc_brand.pos DESC, m_pos ASC, cc_model.name,cc_cat.P5';
                break;
            case 3:
                $sezOrder=Data::get('ccSezonOrder');
                if($sezOrder==1){
                    // сначала зиму
                    //sezOrd= {1,3,5}
                    $r['select']="IF(cc_model.P1=1, 5, IF(cc_model.P1=3, 3, 1)) AS sezOrd";
                }elseif($sezOrder==2){
                    // сначала лето
                    //sezOrd= {1,3,5}
                    $r['select']="IF(cc_model.P1=2, 5, IF(cc_model.P1=3, 3, 1)) AS sezOrd";
                }else $sezOrder=0;
                if(!empty($sezOrder))
                    $r['order'][]='cc_model.mspez_id DESC, sezOrd, cc_brand.pos DESC, m_pos ASC, cc_brand.name,cc_model.name,cc_cat.P7';
                else
                    $r['order'][]='cc_model.mspez_id DESC, cc_brand.pos DESC, m_pos ASC, cc_brand.name,cc_model.name,cc_cat.P7';
                break;
        }


        if(!empty($this->_where)) $r['where']=array_merge($r['where'],$this->_where);
        if(!empty($this->_whereCat)) $r['where']=array_merge($r['where'],$this->_whereCat);
        if(!empty($this->_having)) $r['having']=array_merge($r['having'],$this->_having);

        if($this->hideDSCZero) $r['where'][]=$this->minQtyRadiusSQL;

        //добавляем к запросу все параметры
        if(!empty($this->P5_)) $r['P5']=array('list'=>$this->P5_); // радиус
        if(!empty($this->brands_)) $r['brand_id']=array('list'=>$this->brands_); // бренд
        if(!empty($this->P2_)) $r['P2']=array('list'=>$this->P2_);
        if(!empty($this->P4_)) $r['P4']=array('list'=>$this->P4_);
        if(!empty($this->P6_)) $r['P6']=array('list'=>$this->P6_);

        if(!empty($this->P3_))
            if($this->apMode){
                $r['P3']=array('from'=>(float)min($this->P3_)+$this->_deltaDia,'to'=>'', 'ext_or_eq' => 0);
            } else $r['P3']=array('list'=>$this->P3_);

        /*if(!empty($this->P1_))
        if($this->apMode){
        $r['P1']=array('from'=>(float)min($this->P1_)+$this->_deltaET,'to'=>(float)max($this->P1_)+$this->deltaET_);
        }
        else $r['P1']=array('list'=>$this->P1_);        */

        //  Костыль для вывода вылета (ET) с учетом радиусов // avcode
        if(!$this->is_unic_p46())
        {     
            $this->generateRestrictions ($r);
        }
        // *************************************************

        if(!empty($this->P46_)) {
            $a=array();
            foreach($this->P46_ as $v) {
                $v=array(floatval($v[0]),floatval($v[1]));
                $a[]="P4 = '{$v[0]}' AND P6 = '{$v[1]}'";
            }
            if(!empty($a)) $r['where'][]='('.implode(' OR ',$a).')';
        }
        if($this->replica==1) $r['where'][]="cc_brand.replica=1";
        
        $this->num=$this->cc->cat_view($r);
        $this->ext_filter=true;

        $d=$this->cc->fetchAll();
        if($this->num) {
            $this->paginator=$this->cc->paginator(Url::$path,Url::$sq,@Url::$sq['page'],$this->num,$this->limit,'page',array(
                'active'=>    '<li class="active">{page}</li>',
                'noActive'=>'<li><a href="{url}">{page}</a></li>',
                'dots'=>    '<li>...</li>'
                ),5);
        }
        $burl='/'.App_Route::_getUrl('dTipo').'/';

        foreach($d as $v){
            $this->cat[]=$this->catRow($v,$burl);
        }

        return true;

    }

    private function dict_url($r)
    {
        $s='';
        foreach($r as $k=>$v) if($v)
            $s.=($s!=''?'&nbsp;&nbsp;':'')."<a href=\"#\" rel=\"/ax/explain/color?v=$v\" title=\"Что значит $k?\" class=\"atip gr\">$k</a>";
            else $s.=($s!=''?'&nbsp;&nbsp;':'').$k;
        return trim($s);
    }

    private function makeId($v)
    {
        return preg_replace("~[^a-z0-9_-]~iu",'_',str_replace('*','x',$v));
    }

    private function usortSVfoo($a,$b)
    {
        $a=explode('*',$a);
        $b=explode('*',$b);
        if($a[0]<$b[0]) return -1;
        if($a[0]>$b[0]) return 1;
        if($a[0]==$b[0] && $a[1]<$b[1]) return -1;
        if($a[0]==$b[0] && $a[1]>$b[1]) return 1;
        return 0;
    }

    private function catRow($v,$burl)
    {
        $fullSize=trim("{$v['P2']}x{$v['P5']} {$v['P4']}/{$v['P6']} ET{$v['P1']}".' '.($v['P3']!=0?"DIA {$v['P3']}":''));
        // Стикеры
        if (!empty($v['sticker_id'])) {
            $CC_Ctrl = new CC_Ctrl();
            $stickers_list = $CC_Ctrl::getStickersList();
            $m_sticker = $CC_Ctrl->getModelSticker($v['model_id']);
            if (!empty($m_sticker)) {
                $v['m_sticker'] = array_merge($m_sticker, $stickers_list[$m_sticker['sticker_type']]);
            }
            unset($CC_Ctrl);
        }
        else $v['m_sticker'] = array();
        //
        $vi=array(
            'video_link'=>  $v['video_link'],
            //'img3'=>        $this->cc->make_img_path($v['img3']),
            'img2'=>        !empty($v['suffix_img2'])?$this->cc->make_img_path($v['suffix_img2']):(!empty($v['img2'])?$this->cc->make_img_path($v['img2']):$this->noimg2),
            'img1'=>        !empty($v['suffix_img1'])?$this->cc->make_img_path($v['suffix_img1']):(!empty($v['img1'])?$this->cc->make_img_path($v['img1']):$this->noimg2),
            'img1Blk'=>     !empty($v['suffix_img1'])?$this->cc->make_img_path($v['suffix_img1']):(!empty($v['img1'])?$this->cc->make_img_path($v['img1']):$this->noimg2),
            'url'=>         $burl.$v['cat_sname'].'.html',
            'bname'=>       Tools::html($v['bname']),
            'mname'=>       Tools::html($v['mname'].' '.$v['msuffix']),
            'imgAlt'=>      'Фото диска '.Tools::html($v['bname'].' '.$v['mname'].' '.$fullSize.' '.($v['csuffix'] != 'nocolor' ? $v['csuffix'] : '')),
            'anc'=>            Tools::unesc($v['bname'].' '.$v['mname']),
            'ancBlk'=>        Tools::unesc($v['bname'].' '.$v['mname']),
            'title'=>        "резина ".Tools::html($v['bname'].' '.$v['mname'].' '.$fullSize.' '.($v['csuffix'] != 'nocolor' ? $v['csuffix'] : '')),
            'qtyText'=>        $v['sc']>12?"&gt;&nbsp;12&nbsp;шт":(!$v['sc']?'-':"{$v['sc']}&nbsp;шт"),
            'scText'=>      $v['sc']?("<span class=\"nal\">на&nbsp;складе&nbsp;(".($v['sc']>12?'&gt;12':$v['sc'])."&nbsp;шт.)</span>"):"<span class=\"nnal\">нет&nbsp;на&nbsp;складе</span>",
            'maxQty'=>        $v['sc'],
            'defQty'=>        $v['sc']>4 || $v['sc']==0?4:$v['sc'],
            'priceText'=>    $v['cprice']?(Tools::nn($v['cprice'])."&nbsp;р."):'звоните',
            'cprice' =>    $v['cprice']?(Tools::nn($v['cprice'])):'0',
            'priceTextBlk'=> $v['cprice']?('<span class="price scl" cat_id="'.$v['cat_id'].'">'.Tools::nn($v['cprice'])."<span class='cur'>&nbsp;руб. за диск</span></span>"):'<span class="price">-<span class="cur">&nbsp;руб. за диск</span></span>',
            'cat_id'=>        $v['cat_id'],
            'razmer'=>        "{$v['P2']} x {$v['P5']}",
            'sverlovka'=>    "{$v['P4']} x {$v['P6']}",
            'sverlovka1'=>    "{$v['P4']}/{$v['P6']}",
            'dia'=>            $v['P3']!=0?$v['P3']:'',
            'et'=>            $v['P1'],
            'color'=>       ($v['csuffix'] != 'nocolor' ? $v['csuffix'] : ''),
            'fullName'=>    $fullSize,
            'colorUrl'=>     $this->dict_url($this->cc->dict_search_key(($v['csuffix'] != 'nocolor' ? $v['csuffix'] : ''),$v['gr'],$v['brand_id'])),
            'newBlk'=>         ($v['mspez_id']==2?'<i></i>':''),
            'newTbl'=>         ($v['mspez_id']==2?'<div class="new">новинка</div>':''),
            'm_sticker'=>   @$v['m_sticker'],
            'brand_img1'=>  $this->cc->make_img_path($v['brand_img1']),
            'brand_img2'=>  $this->cc->make_img_path($v['brand_img2'])
        );

        if($this->sMode){
            if($v['sc']>=2 || $v['sc']==0) $vi['defQty']=2;
        }

        return $vi;
    }

    private function _sidebar()
    {
        // быстрые бренды
        $burl='/'.App_Route::_getUrl('dCat').'/';
        $this->qbrands=array(0=>array());
        $r=array(
            'gr'=>2,
            'whereCat'=>$this->minQtyRadiusSQL,
            'qSelect'=>array(
                'modelsNum'=>array()
            ),
            'select'=>array(
                'cc_brand.replica'=>'replica',
                'cc_brand.name'=>'name',
                'cc_brand.sname'=>'sname'
            )
        );
        $r=$this->cc->brands($r);
        if($r){
            $this->qbrands=array();
            while($this->cc->next()!==false){
                $this->qbrands[$this->cc->qrow['replica']?'Реплика':0][]=array(
                    'name'=>Tools::unesc($this->cc->qrow['name']),
                    'sname'=>$burl.$this->cc->qrow['sname'].'.html'
                );
            }
        }

        if(!empty($this->brand_id)){
            // модели в сайдбаре. bname, brand_id должно быть определено выше
            $r=array(
                'gr'=>2,
                'brand_id'=>$this->brand_id,
                'nolimits'=>1,
                'qSelect'=>array(
                    'scDiv'=>array('where'=>$this->minQtyRadiusSQL)
                ),
                'order'=>"m_pos ASC, cc_model.name"
            );
            $this->cc->models($r);
            $d=$this->cc->fetchAll('', MYSQL_ASSOC);
            $this->qmodels=array();
            $burl='/'.App_Route::_getUrl('dModel').'/';
            foreach($d as $v){
                $mname=Tools::unesc($v['name']);
                $v['suffix']=Tools::unesc($v['suffix']);
                $vi=array(
                    'anc'=>"{$this->bname} {$mname} {$v['suffix']}",
                    'url'=>$burl.$v['sname'].'.html',
                    'scDiv'=>$v['scDiv']
                );
                $this->qmodels[]=$vi;
            }

        }
    }

    /*
    * поиск для живого фильтра
    */
    public function axSearch()
    {
        $time1=Tools::getMicroTime();

        //************************************
        //die(print_r($_SERVER['REQUEST_URI'], true));
        // собираем все id машинок с такими параметрами    
        $this->ab->getTree($this->abCookie);
        $this->ab->avto_sh_array(@$this->ab->tree['vendor_id'], @$this->ab->tree['model_id'], @$this->ab->tree['year_id'], Array(2));
        //
        $this->brands = array_unique(array_merge($this->ab->getBrandsIds(), Array($this->ab->brand_id)));
        // ***  Добавляем параметры в класс, как будто их выбрал пользователь      
        $this->setClassParams();     
        //************************************
        // для дисков группы
        $groups=@$_REQUEST['groups'];

        $changeVars=(int)@$_REQUEST['chVars'];
        if($changeVars){
            $sq=array();
            // таблица соответсвий параметров для подмены
            $tt=array(
                'bids'=>'_bids',
                'p5'=>'_p5',
                'p2'=>'_p2',
                'sv'=>'_sv',
                'p1'=>'_p1',
                'p3'=>'_p3',
                'vendor'=>'_vendor'
            );
            // изменяем названия _GET переменных параметров в уточняющий тип для запросов из всплывающих форм (chVars=1)
            foreach($tt as $tk=>$tv) {
                if(isset(Url::$sq[$tk]) && !isset(Url::$sq[$tv])) {
                    if(is_array(Url::$sq[$tk])) foreach(Url::$sq[$tk] as $k=>$v){
                        if(!isset($sq[$tv])) $sq[$tv]=array();
                        $sq[$tv][$k] = $v;
                    }
                    else $sq[$tv]=Url::$sq[$tk];
                    unset(Url::$sq[$tk]);
                    Url::$sq[$tv]=$sq[$tv];
                }
            }
            $this->r['newParam']=$sq; // для отладки
        }
        $all_brands = $this->brands;
        $this->_routeParam();

        // сначала получаем кол-во размеров применяя все параметры

        $r=array(
            'gr'=>2,
            'notH'=>1,
            'nolimits'=>1,
            'count'=>1,
            'where'=>array(),
            'having'=>array());

        if(!empty($this->P2_)) $r['P2']=array('list'=>$this->P2_); // J
        if(!empty($this->P4_)) $r['P4']=array('list'=>$this->P4_); // дырки
        if(!empty($this->P5_))  $r['P5']=array('list'=>$this->P5_); // радиус
        if(!empty($this->P6_)) $r['P6']=array('list'=>$this->P6_); // ДЦО
        if(!empty($this->brands_))  $r['brand_id']=array('list'=>$this->brands_); // бренд
        $this->r['brands']=$this->_brands;

        // where
        if($this->replica==1) $r['where'][]="cc_brand.replica=1";
        if($this->hideDSCZero) $r['where'][]=$this->minQtyRadiusSQL;
        if(!empty($this->_where)) $r['where']=array_merge($r['where'],$this->_where);
        if(!empty($this->_whereCat)) $r['where']=array_merge($r['where'],$this->_whereCat);

        // DIA
        if(!empty($this->P3_))
            if($this->apMode){
                $r['P3']=array('from'=>(float)min($this->P3_)+$this->_deltaDia,'to'=>'', 'ext_or_eq' => 0);
            } else $r['P3']=array('list'=>$this->P3_);

        // ET
        /*if(!empty($this->P1_))
        if($this->apMode){
        $r['P1']=array('from'=>(float)min($this->P1_)+$this->_deltaET,'to'=>(float)max($this->P1_)+$this->deltaET_);
        }
        else $r['P1']=array('list'=>$this->P1_);
        */
        if(!empty($this->P46_)) {
            $a=array();
            foreach($this->P46_ as $v) {
                $v=array(floatval($v[0]),floatval($v[1]));
                $a[]="P4 = '{$v[0]}' AND P6 = '{$v[1]}'";
            }
            if(!empty($a)) $r['where'][]='('.implode(' OR ',$a).')';
        }
        //--

        $r['sqlReturn']=0;
        //  Костыль для вывода вылета (ET) с учетом радиусов // avcode
        @ksort($this->ab->avto[2], SORT_STRING);
        $this->sz2=array(); 
        ksort($this->ab->avto[2]);
        foreach($this->ab->avto[2] as $c=>$v){
            foreach($v as $vv){
                if($c==20 || $c==25){
                    if(!isset($this->sz2[$vv['P5']])) $this->sz2[$vv['P5']]=array();
                    $this->sz2[$vv['P5']]['OEM'][][1]=$vv;
                }else{
                    $vvv=current($vv);
                    $this->sz2[$vvv['P5']]['Тюннинг'][]=$vv;
                }
            }
        }
        ksort($this->sz2);
        $this->brandsFilter();
        // ***
        if(!$this->is_unic_p46())
        {     
            $this->generateRestrictions ($r);
        }
        // *************************************************
        $this->exnum=$this->cc->cat_view($r);  
        $this->r['tn']=$this->exnum;  
        $this->r['formdata'] = null;

        if($this->exnum){
            // теперь получаем значения для каждой группы параметров
            $r['count']=0;
            $r['sqlReturn']=0;
            $r['order']='';

            if(@is_array($groups))
                foreach($groups as $group){

                    $r['where']=array();
                    if($this->replica==1) $r['where'][]="cc_brand.replica=1";
                    if($this->hideDSCZero) $r['where'][]=$this->minQtyRadiusSQL;
                    if(!empty($this->_where)) $r['where']=array_merge($r['where'],$this->_where);
                    if(!empty($this->_whereCat)) $r['where']=array_merge($r['where'],$this->_whereCat);

                    // добавляем все жесткие параметры (1)     
                    if(!empty($all_brands)) $r['brand_id']=array('list'=>$all_brands); else unset($r['brand_id']);
                    if(!empty($this->valid_radiuses)) $r['P5']=array('list'=>$this->valid_radiuses); else unset($r['P5']);  // R
                    if(!empty($this->P2)) $r['P2']=array('list'=>$this->P2); else unset($r['P2']);  // J

                    // DIA
                    if(!empty($this->P3))
                        if($this->apMode){
                            $r['P3']=array('from'=>(float)min($this->P3)+$this->_deltaDia,'to'=>'', 'ext_or_eq' => 0);
                        } else $r['P3']=array('list'=>$this->P3);
                    else unset($r['P3']);

                    // ET
                    /*if(!empty($this->P1))
                    if($this->apMode){
                    $r['P1']=array('from'=>(float)min($this->P1)+$this->_deltaET,'to'=>(float)max($this->P1)+$this->deltaET_);
                    }
                    else $r['P1']=array('list'=>$this->P1);
                    else unset($r['P1']);*/

                    // SV
                    if(!empty($this->P46)) {
                        $a=array();
                        foreach($this->P46 as $v) {
                            $v=array(floatval($v[0]),floatval($v[1]));
                            $a[]="P4 = '{$v[0]}' AND P6 = '{$v[1]}'";
                        }
                        if(!empty($a)) $r['where'][]='('.implode(' OR ',$a).')';
                    }

                    // добавляем уточняющие параметры из того же состава что и жесткие (1)
                    $r['groupby']='';
                    $n=0;
                    switch($group){

                        case '_p2':
                            $r['P2'] = null;
                            if(!empty($this->_brands))  $r['brand_id']=array('list'=>$this->brands_); // бренд
                            if(!empty($this->_P5))  $r['P5']=array('list'=>$this->P5_); // радиус

                            // DIA
                            if(!empty($this->_P3))
                                if($this->apMode){
                                    $r['P3']=array('from'=>(float)min($this->P3_)+$this->_deltaDia,'to'=>'', 'ext_or_eq' => 0);
                                } else $r['P3']=array('list'=>$this->P3_);

                            // ET
                            if(!empty($this->_P1))
                                if($this->apMode){
                                    $r['P1']=array('from'=>(float)min($this->P1_)+$this->_deltaET,'to'=>(float)max($this->P1_)+$this->deltaET_);
                                }
                                else $r['P1']=array('list'=>$this->P1_);

                            if(!empty($this->_P46)) {
                                $a=array();
                                foreach($this->P46_ as $v) {
                                    $v=array(floatval($v[0]),floatval($v[1]));
                                    $a[]="P4 = '{$v[0]}' AND P6 = '{$v[1]}'";
                                }
                                if(!empty($a)) $r['where'][]='('.implode(' OR ',$a).')';
                            }

                            $r['fields']="cc_cat.P2+'0' AS FF";
                            $r['groupby']='FF';
                            break;

                        case '_p1':
                            $r['P1'] = null;
                            if(!empty($this->_brands))  $r['brand_id']=array('list'=>$this->brands_); // бренд
                            if(!empty($this->_P2))  $r['P2']=array('list'=>$this->P2_); // J
                            if(!empty($this->_P5))  $r['P5']=array('list'=>$this->P5_); // радиус

                            // DIA
                            if(!empty($this->_P3))
                                if($this->apMode){
                                    $r['P3']=array('from'=>(float)min($this->P3_)+$this->_deltaDia,'to'=>'', 'ext_or_eq' => 0);
                                } else $r['P3']=array('list'=>$this->P3_);

                            if(!empty($this->_P46)) {
                                $a=array();
                                foreach($this->P46_ as $v) {
                                    $v=array(floatval($v[0]),floatval($v[1]));
                                    $a[]="P4 = '{$v[0]}' AND P6 = '{$v[1]}'";
                                }
                                if(!empty($a)) $r['where'][]='('.implode(' OR ',$a).')';
                            }

                            $r['fields']="cc_cat.P1+'0' AS FF";
                            $r['groupby']='FF';
                            break;

                        case '_p3':
                            $r['P3'] = null;
                            if(!empty($this->_brands))  $r['brand_id']=array('list'=>$this->brands_); // бренд
                            if(!empty($this->_P2))  $r['P2']=array('list'=>$this->P2_); // J
                            if(!empty($this->_P5))  $r['P5']=array('list'=>$this->P5_); // радиус

                            // ET
                            if(!empty($this->_P1))
                                if($this->apMode){
                                    $r['P1']=array('from'=>(float)min($this->P1_)+$this->_deltaET,'to'=>(float)max($this->P1_)+$this->deltaET_);
                                }
                                else $r['P1']=array('list'=>$this->P1_);

                            if(!empty($this->_P46)) {
                                $a=array();
                                foreach($this->P46_ as $v) {
                                    $v=array(floatval($v[0]),floatval($v[1]));
                                    $a[]="P4 = '{$v[0]}' AND P6 = '{$v[1]}'";
                                }
                                if(!empty($a)) $r['where'][]='('.implode(' OR ',$a).')';
                            }

                            $r['fields']="cc_cat.P3+'0' AS FF";
                            $r['groupby']='FF';
                            break;


                        case '_p5':
                            $r['P5'] = null;
                            if(!empty($this->_brands))  $r['brand_id']=array('list'=>$this->brands_); // бренд
                            if(!empty($this->_P2))  $r['P2']=array('list'=>$this->P2_); // J

                            // DIA
                            if(!empty($this->_P3))
                                if($this->apMode){
                                    $r['P3']=array('from'=>(float)min($this->P3_)+$this->_deltaDia,'to'=>'', 'ext_or_eq' => 0);
                                } else $r['P3']=array('list'=>$this->P3);

                            // ET
                            if(!empty($this->_P1))
                                if($this->apMode){
                                    $r['P1']=array('from'=>(float)min($this->P1_)+$this->_deltaET,'to'=>(float)max($this->P1_)+$this->deltaET_);
                                }
                                else $r['P1']=array('list'=>$this->P1_);


                            if(!empty($this->_P46)) {
                                $a=array();
                                foreach($this->P46_ as $v) {
                                    $v=array(floatval($v[0]),floatval($v[1]));
                                    $a[]="P4 = '{$v[0]}' AND P6 = '{$v[1]}'";
                                }
                                if(!empty($a)) $r['where'][]='('.implode(' OR ',$a).')';
                            }

                            $r['fields']="cc_cat.P5+'0' AS FF";
                            $r['groupby']='FF';
                            break;

                        case '_vendor':
                        case '_bids':
                            $r['brand_id'] = null;
                            if(!empty($this->_P2))  $r['P2']=array('list'=>$this->P2_); // J
                            if(!empty($this->P5_))  $r['P5']=array('list'=>$this->P5_); // Rad

                            // DIA
                            if(!empty($this->_P3))
                                if($this->apMode){
                                    $r['P3']=array('from'=>(float)min($this->P3_)+$this->_deltaDia,'to'=>'', 'ext_or_eq' => 0);
                                } else $r['P3']=array('list'=>$this->P3_);

                            // ET
                            if(!empty($this->_P1))
                                if($this->apMode){
                                    $r['P1']=array('from'=>(float)min($this->P1_)+$this->_deltaET,'to'=>(float)max($this->P1_)+$this->deltaET_);
                                }
                                else $r['P1']=array('list'=>$this->P1_);


                            if(!empty($this->_P46)) {
                                $a=array();
                                foreach($this->P46_ as $v) {
                                    $v=array(floatval($v[0]),floatval($v[1]));
                                    $a[]="P4 = '{$v[0]}' AND P6 = '{$v[1]}'";
                                }
                                if(!empty($a)) $r['where'][]='('.implode(' OR ',$a).')';
                            }

                            $r['fields']='cc_brand.brand_id AS FF';
                            $r['groupby']='FF';
                            break;

                        case '_sv':
                            if(!empty($this->_P5))  $r['P5']=array('list'=>$this->P5_); // радиус
                            if(!empty($this->_brands))  $r['brand_id']=array('list'=>$this->brands_); // бренд
                            if(!empty($this->_P2))  $r['P2']=array('list'=>$this->P2_); // J

                            // DIA
                            if(!empty($this->_P3))
                                if($this->apMode){
                                    $r['P3']=array('from'=>(float)min($this->P3_)+$this->_deltaDia,'to'=>'', 'ext_or_eq' => 0);
                                } else $r['P3']=array('list'=>$this->P3_);

                            // ET
                            if(!empty($this->_P1))
                                if($this->apMode){
                                    $r['P1']=array('from'=>(float)min($this->P1_)+$this->_deltaET,'to'=>(float)max($this->P1_)+$this->deltaET_);
                                }
                                else $r['P1']=array('list'=>$this->P1_);


                            $r['fields']="CONCAT(cc_cat.P4+'0','*',cc_cat.P6+'0') AS FF";
                            $r['groupby']='FF';
                            break;
                    }
                    if(!empty($r['groupby'])) {
                        //  Костыль для вывода вылета (ET) с учетом радиусов // avcode
                        if(!$this->is_unic_p46())
                        {     
                            $this->generateRestrictions ($r);
                        }
                        // *************************************************
                        $n=$this->cc->cat_view($r);
                        //$this->r['sql']=$this->cc->sql_query;
                    }

                    if($n) {
                        while($this->cc->next()!==false)
                            $this->r['formdata'][$group.$this->makeId($this->cc->qrow['FF'])]=1;
                    }
            }
        }  
        $this->r['queryTime']=Tools::getMicroTime()-$time1;

    }  

    /*
    * фильтр брендов для живого фильтра
    */
    public function brandsFilter()
    {
        $r=array(
            'gr'=>2,
            'notH'=>1,
            'nolimits'=>1,
            'count'=>1,
            'where'=>array(),
            'having'=>array());

        $all_brands = $this->brands;
        $all_radiuses = array_unique($this->P5);

        // теперь получаем значения для каждой группы параметров
        $r['count']=0;
        $r['sqlReturn']=0;
        $r['order']='';
        //
        $r['where']=array();
        if($this->replica==1) $r['where'][]="cc_brand.replica=1";
        if($this->hideDSCZero) $r['where'][]=$this->minQtyRadiusSQL;
        if(!empty($this->_where)) $r['where']=array_merge($r['where'],$this->_where);
        if(!empty($this->_whereCat)) $r['where']=array_merge($r['where'],$this->_whereCat);

        // добавляем все жесткие параметры (1)

        if(!empty($all_brands)) $r['brand_id']=array('list'=>$all_brands); else unset($r['brand_id']);
        if(!empty($all_radiuses)) $r['P5']=array('list'=> $all_radiuses); else unset($r['P5']);  // R
        if(!empty($this->P2)) $r['P2']=array('list'=>$this->P2); else unset($r['P2']);  // J

        // DIA
        if(!empty($this->P3))
            if($this->apMode){
                $r['P3']=array('from'=>(float)min($this->P3)+$this->_deltaDia,'to'=>'', 'ext_or_eq' => 0);
            } else $r['P3']=array('list'=>$this->P3);
        else unset($r['P3']);

        // ET
        /*if(!empty($this->P1))
        if($this->apMode){
        $r['P1']=array('from'=>(float)min($this->P1)+$this->_deltaET,'to'=>(float)max($this->P1)+$this->deltaET_);
        }
        else $r['P1']=array('list'=>$this->P1);
        else unset($r['P1']);*/

        // SV
        if(!empty($this->P46)) {
            $a=array();
            foreach($this->P46 as $v) {
                $v=array(floatval($v[0]),floatval($v[1]));
                $a[]="P4 = '{$v[0]}' AND P6 = '{$v[1]}'";
            }
            if(!empty($a)) $r['where'][]='('.implode(' OR ',$a).')';
        }

        $r['groupby']='';
        $n=0;

        // DIA
        if(!empty($this->_P3))
            if($this->apMode){
                $r['P3']=array('from'=>(float)min($this->P3_)+$this->_deltaDia,'to'=>'', 'ext_or_eq' => 0);
            } else $r['P3']=array('list'=>$this->P3_);

        // ET
        if(!empty($this->_P1))
            if($this->apMode){
                $r['P1']=array('from'=>(float)min($this->P1_)+$this->_deltaET,'to'=>(float)max($this->P1_)+$this->deltaET_);
            }
            else $r['P1']=array('list'=>$this->P1_);


        if(!empty($this->_P46)) {
            $a=array();
            foreach($this->P46_ as $v) {
                $v=array(floatval($v[0]),floatval($v[1]));
                $a[]="P4 = '{$v[0]}' AND P6 = '{$v[1]}'";
            }
            if(!empty($a)) $r['where'][]='('.implode(' OR ',$a).')';
        }

        $r['fields']='cc_brand.brand_id AS FF, cc_cat.P5+\'0\' AS P5';
        $r['groupby']='FF, P5';
        //  Костыль для вывода вылета (ET) с учетом радиусов // avcode
        if(!$this->is_unic_p46())
        {     
           $this->generateRestrictions ($r);
        }
        // *************************************************
        if(!empty($r['groupby'])) {
            $n=$this->cc->cat_view($r);
        }

        if($n) {
            while($this->cc->next()!==false)
            {
                $this->valid_brands[]=$this->makeId($this->cc->qrow['FF']);
                $this->valid_radiuses[]=$this->makeId($this->cc->qrow['P5']);
            }
        }
        $this->valid_brands = array_unique($this->valid_brands);    
        $this->valid_radiuses = array_unique($this->valid_radiuses);    
    }        

    /**
    * Метод устанавливет параметры для поиска дисков, как если бы их выбрал пользователь
    * 
    */
    private function setClassParams()
    {
        foreach($this->ab->avto[2] as $rad=>$v)
        {
            foreach($v as $type=>$vv)
            {    
                if (isset($vv[1]))
                {     
                    foreach ($vv as $vvv){
                        if ($this->ab->tree['model_id'] != 11546 && $this->ab->tree['model_id'] != 3675) { // особые условия для Jeep Grand Cherokee и Cadillac SRX
                            $this->P1[] = $vvv['P1']; // добавляем вылет (ET)
                            $this->P2[] = $vvv['P2']; // добавляем ширину
                        }
                        $this->P3[] = $vvv['P3']; // добавляем DIA
                        $this->P5[] = $vvv['P5'];
                        if (!empty($vvv['P4']) && !empty($vvv['P6']))  $this->P46[] = Array($vvv['P4'], $vvv['P6']);
                    }
                }
                else
                {
                    if ($this->ab->tree['model_id'] != 11546 && $this->ab->tree['model_id'] != 3675) { // особые условия для Jeep Grand Cherokee и Cadillac SRX
                        $this->P1[] = $vv['P1']; // добавляем вылет (ET)
                        $this->P2[] = $vv['P2']; // добавляем ширину
                    }
                    $this->P3[] = $vv['P3']; // добавляем DIA
                    $this->P5[] = $vv['P5'];
                    if (!empty($vv['P4']) && !empty($vv['P6'])) $this->P46[] = Array($vv['P4'], $vv['P6']);
                }
            }
        }
        $this->P1 = array_unique($this->P1);
        $this->P2 = array_unique($this->P2);
        $this->P3 = array_unique($this->P3);
        $this->P5 = array_unique($this->P5);
    }

    private function generateRestrictions (&$r)
    {   
        if(!empty($this->sz2))
        {
            $cond_array = Array();
            foreach($this->sz2 as $rad=>$v)
            {
                foreach($v as $type=>$vv)
                {
                    foreach($vv as $row)
                    {
                        foreach ($row as $typo)
                        {
                            $cond_array[]  = '(cc_cat.P5 = \''.$rad.'\' AND cc_cat.P1 >= \''.((float)$typo['P1']+$this->_deltaET).'\' AND cc_cat.P1 <= \''.((float)$typo['P1']+$this->deltaET_).
                            '\' AND cc_cat.P2 = \''.(float)$typo['P2'].'\')';  
                        }
                    }

                }
            }
            $cond_array = array_unique($cond_array);
            //
            $r['where'][] = '('.implode(' OR ', $cond_array).')';
        }
        return $r;
    }


    private function is_unic_p46()
    {
        $p46_array = @array_unique($this->P46);
        if (!empty($p46_array))
        {
            foreach ($p46_array as $p46)
            {
                if (
                ((float)$p46[0] == 5 and (float)$p46[1] == 150) or
                ((float)$p46[0] == 6 and (float)$p46[1] == 139.7) or
                ((float)$p46[0] == 6 and (float)$p46[1] == 114.3) or
                ((float)$p46[0] == 5 and (float)$p46[1] == 127) or
                ((float)$p46[0] == 5 and (float)$p46[1] == 139.7) or
                ((float)$p46[0] == 6 and (float)$p46[1] == 115) or
                ((float)$p46[0] == 8 and (float)$p46[1] == 170) or
                ((float)$p46[0] == 6 and (float)$p46[1] == 135) 
                )
                {   
                    return true;
                }
            }
        }
        return false;
    }

    // *************************** Вывод для AJAX *******************************
    public function ax_years()
    {
        $this->apInited = true;
        $this->ab->getTree($this->abCookie);

        // собираем все id машинок с такими параметрами
        $this->ab->avto_sh_array(@$this->ab->tree['vendor_id'], @$this->ab->tree['model_id'], @$this->ab->tree['year_id'], Array(2));
        //
        if (!count($this->ab->tree['years'])) return App_Route::redir404();
        $this->mark = $this->ab->tree['vendor_name'];
        $this->mark2 = $this->mark_alt = $this->ab->tree['vendor_alt'] != '' ? $this->ab->tree['vendor_alt'] : $this->ab->tree['vendor_name'];
        $this->model = $this->ab->tree['model_name'];
        $this->model2 = $this->model_alt = $this->ab->tree['model_alt'] != '' ? $this->ab->tree['model_alt'] : $this->ab->tree['model_name'];
        $this->fname = $this->ab->fname;
        $this->years = array();
        foreach ($this->ab->tree['years'] as $v) {
            $this->years[] = array(
                'anc' => Tools::unesc($v['name']),
                'url' => '/' . App_Route::_getUrl('avtoPodborDiskov') . '/' . App_Route::$param['ap'][0] . '--' . App_Route::$param['ap'][1] . '--' . Tools::unesc($v['sname']) . '.html',
                'sname' => Tools::unesc($v['sname'])

            );
        }
        // *** добавление фильтра
        $this->abc = $this->ab->getCommons(@$this->ab->tree['vendor_id'], @$this->ab->tree['model_id'], @$this->ab->tree['year_id']);
        //$this->brands = array_unique(array_merge($this->ab->getBrandsIds(array_keys($this->ab->tree['vendors'])), Array($this->ab->brand_id)));
        $this->brands = array_unique(array_merge($this->ab->getBrandsIds(), Array($this->ab->brand_id)));
        // ***  Добавляем параметры в класс, как будто их выбрал пользователь
        $this->setClassParams();
        // ***
        @ksort($this->ab->avto[2], SORT_STRING);
        $this->sz2=array();
        ksort($this->ab->avto[2]);
        foreach($this->ab->avto[2] as $c=>$v){
            foreach($v as $vv){
                if($c==20 || $c==25){
                    if(!isset($this->sz2[$vv['P5']])) $this->sz2[$vv['P5']]=array();
                    $this->sz2[$vv['P5']]['OEM'][][1]=$vv;
                }else{
                    $vvv=current($vv);
                    $this->sz2[$vvv['P5']]['Тюннинг'][]=$vv;
                }
            }
        }
        ksort($this->sz2);
        $this->dSearchUrl='/'.App_Route::_getUrl('dSearch').'.html';
        //***
        $this->brandsFilter();
        //***
        $this->search();
        // *** Установка кук
        $this->abCookie = array(
            'svendor' => @$this->ab->tree['vendor_sname'] ? $this->ab->tree['vendor_sname'] : '',
            'smodel'  => @$this->ab->tree['model_sname'] ? $this->ab->tree['model_sname'] : '',
            'syear'   => @$this->ab->tree['year_sname'] ? $this->ab->tree['year_sname'] : '',
            'smodif'  => @$this->ab->tree['modif_sname'] ? $this->ab->tree['modif_sname'] : ''
        );
        $this->setCookie('apData', base64_encode(serialize($this->abCookie)));

        // *************************************************************************************************************

        global $app;
        if (is_file($app->namespace . '/view/'.$this->searchTpl.'.php')) {
            extract((array)$app->controllerInstance, EXTR_OVERWRITE);
            extract($app->controllerInstance->_data, EXTR_OVERWRITE);
            include $app->namespace . '/view/' .$this->searchTpl . '.php';
        } else
            throw new AppException ('[App::output()]: ' . $app->namespace . '/view/' . $this->searchTpl . ' open fault.');
        exit(200);
    }

    public function ax_modifs()
    {

        $this->apInited = true;
        $this->ab->getTree($this->abCookie);
        // собираем все id машинок с такими параметрами
        $this->ab->avto_sh_array(@$this->ab->tree['vendor_id'], @$this->ab->tree['model_id'], @$this->ab->tree['year_id'], Array(2));
        //
        if (!count($this->ab->tree['modifs'])) return App_Route::redir404();
        $this->mark = $this->ab->tree['vendor_name'];
        $this->mark2 = $this->mark_alt = $this->ab->tree['vendor_alt'] != '' ? $this->ab->tree['vendor_alt'] : $this->ab->tree['vendor_name'];
        $this->model = $this->ab->tree['model_name'];
        $this->model2 = $this->model_alt = $this->ab->tree['model_alt'] != '' ? $this->ab->tree['model_alt'] : $this->ab->tree['model_name'];
        $this->year = $this->ab->tree['year_name'];
        $this->fname = $this->ab->fname;

        $this->bottomText = ($this->ab->tree['year_text1']);

        $this->modifs = array();
        foreach ($this->ab->tree['modifs'] as $v) {
            $this->modifs[] = array(
                'anc' => Tools::unesc($this->mark . ' ' . $this->model . ' ' . $v['name']),
                'url' => '/' . App_Route::_getUrl('avtoPodborDiskov') . '/' . App_Route::$param['ap'][0] . '--' . App_Route::$param['ap'][1] . '--' . App_Route::$param['ap'][2] . '--' . Tools::unesc($v['sname']) . '.html',
                'sname' => Tools::unesc($v['sname'])
            );
        }
        // *** добавление фильтра
        $this->abc = $this->ab->getCommons(@$this->ab->tree['vendor_id'], @$this->ab->tree['model_id'], @$this->ab->tree['year_id']);
        //$this->brands = array_unique(array_merge($this->ab->getBrandsIds(array_keys($this->ab->tree['vendors'])), Array($this->ab->brand_id)));
        $this->brands = array_unique(array_merge($this->ab->getBrandsIds(), Array($this->ab->brand_id)));
        // ***  Добавляем параметры в класс, как будто их выбрал пользователь
        $this->setClassParams();
        // ***
        $this->sz2=array();
        @ksort($this->ab->avto[2]);
        foreach($this->ab->avto[2] as $c=>$v){
            foreach($v as $vv){
                if($c==20 || $c==25){
                    if(!isset($this->sz2[$vv['P5']])) $this->sz2[$vv['P5']]=array();
                    $this->sz2[$vv['P5']]['OEM'][][1]=$vv;
                }else{
                    $vvv=current($vv);
                    $this->sz2[$vvv['P5']]['Тюннинг'][]=$vv;
                }
            }
        }
        ksort($this->sz2);
        $this->dSearchUrl='/'.App_Route::_getUrl('dSearch').'.html';
        //***
        $this->brandsFilter();
        //***
        $this->search();
        // *** Установка кук
        $this->abCookie = array(
            'svendor' => @$this->ab->tree['vendor_sname'] ? $this->ab->tree['vendor_sname'] : '',
            'smodel'  => @$this->ab->tree['model_sname'] ? $this->ab->tree['model_sname'] : '',
            'syear'   => @$this->ab->tree['year_sname'] ? $this->ab->tree['year_sname'] : '',
            'smodif'  => @$this->ab->tree['modif_sname'] ? $this->ab->tree['modif_sname'] : ''
        );
        $this->setCookie('apData', base64_encode(serialize($this->abCookie)));

        // *************************************************************************************************************

        global $app;
        if (is_file($app->namespace . '/view/'.$this->searchTpl.'.php')) {
            extract((array)$app->controllerInstance, EXTR_OVERWRITE);
            extract($app->controllerInstance->_data, EXTR_OVERWRITE);
            include $app->namespace . '/view/' .$this->searchTpl . '.php';
        } else
            throw new AppException ('[App::output()]: ' . $app->namespace . '/view/' . $this->searchTpl . ' open fault.');
        exit(200);
    }


    public function ax_result()
    {
        $this->apInited = true;
        $this->ab->getTree($this->abCookie);

        if (!@$this->ab->tree['modif_id']) return App_Route::redir404();

        //$this->noSidebar = true;
        // собираем все id машинок с такими параметрами
        $this->ab->avto_sh_array(@$this->ab->tree['vendor_id'], @$this->ab->tree['model_id'], @$this->ab->tree['year_id'], Array(2), $this->ab->tree['modif_id']);
        //
        $this->mark = $this->ab->tree['vendor_name'];
        $this->mark_alt = $this->ab->tree['vendor_alt'] != '' ? $this->ab->tree['vendor_alt'] : $this->ab->tree['vendor_name'];
        $this->model = $this->ab->tree['model_name'];
        $this->model_alt = $this->ab->tree['model_alt'] != '' ? $this->ab->tree['model_alt'] : $this->ab->tree['model_name'];
        $this->year = $this->ab->tree['year_name'];
        $this->modif = $this->ab->tree['modif_name'];

        $this->aname = $this->ab->tree['vendor_name'] . ' ' . $this->ab->tree['model_name'];
        $this->fname = $this->ab->fname;

        $this->abc = $this->ab->getCommon($this->ab->tree['modif_id']);

        @ksort($this->ab->avto[2], SORT_STRING);
        $this->sz2=array();
        ksort($this->ab->avto[2]);
        foreach($this->ab->avto[2] as $c=>$v){
            foreach($v as $vv){
                if($c==20 || $c==25){
                    if(!isset($this->sz2[$vv['P5']])) $this->sz2[$vv['P5']]=array();
                    $this->sz2[$vv['P5']]['OEM'][][1]=$vv;
                }else{
                    $vvv=current($vv);
                    $this->sz2[$vvv['P5']]['Тюннинг'][]=$vv;
                }
            }
        }
        ksort($this->sz2);
        // *** добавление фильтра
        //$this->brands = array_unique(array_merge($this->ab->getBrandsIds(array_keys($this->ab->tree['vendors'])), Array($this->ab->brand_id)));
        $this->brands = array_unique(array_merge($this->ab->getBrandsIds(), Array($this->ab->brand_id)));
        // ***  Добавляем параметры в класс, как будто их выбрал пользователь
        $this->setClassParams();
        // ***
        $this->dSearchUrl='/'.App_Route::_getUrl('dSearch').'.html';
        //***
        $this->brandsFilter();
        //***
        $this->search();
        // *** Установка кук
        $this->abCookie = array(
            'svendor' => @$this->ab->tree['vendor_sname'] ? $this->ab->tree['vendor_sname'] : '',
            'smodel'  => @$this->ab->tree['model_sname'] ? $this->ab->tree['model_sname'] : '',
            'syear'   => @$this->ab->tree['year_sname'] ? $this->ab->tree['year_sname'] : '',
            'smodif'  => @$this->ab->tree['modif_sname'] ? $this->ab->tree['modif_sname'] : ''
        );
        $this->setCookie('apData', base64_encode(serialize($this->abCookie)));

        // *************************************************************************************************************

        global $app;
        if (is_file($app->namespace . '/view/'.$this->searchTpl.'.php')) {
            extract((array)$app->controllerInstance, EXTR_OVERWRITE);
            extract($app->controllerInstance->_data, EXTR_OVERWRITE);
            include $app->namespace . '/view/' .$this->searchTpl . '.php';
        } else
            throw new AppException ('[App::output()]: ' . $app->namespace . '/view/' . $this->searchTpl . ' open fault.');
        exit(200);
    }
}