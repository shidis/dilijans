<?
class App_PodborShin_Controller extends App_Common_Controller
{
    // главные параметры
    private $M1=array(), // GET['mp1'] || route['M1'] as integer
    $M2=array(), // GET['at'] || route['M2'] as integer  -тип авто
    $M3='', // GET['mp3'] || route['M3'] as integer  -шип
    $P1=array(), // GET['p1'] || route['P1'] as decimal
    $P2=array(), // GET['p2'] || route['P2'] as decimal
    $P3=array(), // GET['p3'] || route['P3'] as decimal
    $brands=array(), // GET['vendor']
    $runflat='', // технология ранфлет
    $c_index=''; // индекс С

    // сумма параметров
    private $M1_=array(),
    $M2_=array(),
    $M3_='',
    $P1_=array(),
    $P2_=array(),
    $P3_=array(),
    $runflat_='',
    $c_index_='',
    $brands_=array();

    private $valid_radiuses = array(), $valid_brands = array();

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
        $this->view('podborshin/index');

        $this->apInited = true;
        $this->ab->getTree($this->abCookie);

        if (!count($this->ab->tree['vendors'])) return App_Route::redir404();
        $this->marks = array();
        foreach ($this->ab->tree['vendors'] as $v) {
            $this->marks[] = array(
                'anc' => Tools::unesc($v['name']),
                'title' => 'шины и диски для ' . Tools::unesc($v['alt'] != '' ? $v['alt'] : $v['name']),
                'url' => '/' . App_Route::_getUrl('avtoPodborShin') . '/' . Tools::unesc($v['sname']) . '.html',
                'sname' => Tools::unesc($v['sname'])
            );
        }
        // Фильтр
        $this->lf = array();
        $this->lf['mp3']=array();
        $si=App_Route::_getUrl('tSearch').'?';
        $this->lf['mp3'][1]=array(
            'chk'=>@in_array(1, $this->M3_) ? true : false,
            'anc'=>'Шип <img src="/app/images/ship.png" alt="шипованные шины">',
            'id'=>'_mp31',
            'url'=>$si.'mp3=1'
        );
        $this->lf['mp3'][0]=array(
            'chk'=>@in_array(0, $this->M3_) ? true : false,
            'anc'=>"Нешип",
            'id'=>'_mp30',
            'url'=>$si.'mp3=0'
        );

        $this->lf['mp1']=array();
        $this->lf['mp1'][2]=array(
            'chk'=>@in_array(2, $this->M1_)? true : false,
            'anc'=>@$this->sezonNames5[2].$this->sezonIcos[2],
            'id'=>'_mp12',
            'url'=>$si.'mp1=2'
        );
        $this->lf['mp1'][1]=array(
            'chk'=>@in_array(1, $this->M1_) ? true : false,
            'anc'=>@$this->sezonNames5[1].$this->sezonIcos[1],
            'id'=>'_mp11',
            'url'=>$si.'mp1=1'
        );
        $this->lf['mp1'][3]=array(
            'chk'=>@in_array(3, $this->M1_) ? true : false,
            'anc'=>@$this->sezonNames5[3].$this->sezonIcos[3],
            'id'=>'_mp13',
            'url'=>$si.'mp1=3'
        );
        // ***
        $this->breadcrumbs['Подбор шин по марке авто'] = '';
        $this->title = 'Подбор шин по марке автомобиля онлайн. Виртуальный подбор резины на ваш авто!';
        $this->description = 'Наш сервис позволяет совершить удобный виртуальный подбор шин по марке автомобиля, с большим количеством параметров, которые позволят сделать максимально точный подбор резины к вашему автомобилю.';
        $this->keywords = 'подбор шин и дисков по марке автомобиля, подбор зимних шин по марке автомобиля, подбор летних шин по марке автомобиля, подобрать шины по марке автомобиля виртуальный подбор, подбор резины по марке автомобиля онлайн';
        $this->_title = 'Подбор шин по марке автомобиля';
        // *** Очистка кук
        $this->abCookie = array('svendor' => '', 'smodel' => '', 'syear' => '', 'smodif' => '', 'apMode' => 0);
        $this->setCookie('apData', base64_encode(serialize($this->abCookie)));
        // Очистка ненужных параметров (фикс для чекбоксов)
        unset($this->ab->tree['models'], $this->ab->tree['years'], $this->ab->tree['modifs']);
        $this->relink_href = '/' . App_Route::_getUrl('avtoPodborDiskovIndex') . '.html';
    }

    public function models()
    {
        $this->apInited = true;
        $this->ab->getTree($this->abCookie);
        
        
        if (!count($this->ab->tree['models'])) return App_Route::redir404();
        $this->view('podborshin/models');
        $this->mark = $this->ab->tree['vendor_name'];
        $this->mark2 = $this->mark_alt = $this->ab->tree['vendor_alt'] != '' ? $this->ab->tree['vendor_alt'] : $this->ab->tree['vendor_name'];
        $this->fname = $this->ab->fname;

        //$this->bottomText = $this->ss->getDoc('avtoPodborShin_models$10');
        //if (mb_strlen(trim(Tools::striptags($this->bottomText))) < 20) $this->bottomText = '';

        $this->description = $this->ss->meta['description'];
        $this->keywords = $this->ss->meta['keywords'];

        $this->bottomText = ($this->ab->tree['vendor_text1']);

        $this->models = array();
        foreach ($this->ab->tree['models'] as $v) {
            $this->models[] = array(
                'anc' => Tools::unesc($this->mark . ' ' . $v['name']),
                'title' => 'шины и диски для ' . Tools::unesc($v['alt'] != '' ? "{$this->mark_alt} {$v['alt']}" : "{$this->mark_alt} {$v['name']}"),
                'url' => '/' . App_Route::_getUrl('avtoPodborShin') . '/' . App_Route::$param['ap'][0] . '--' . Tools::unesc($v['sname']) . '.html',
                'sname' => Tools::unesc($v['sname'])
            );
        }
        $this->title = "Подобрать шины для {$this->mark}. Онлайн подбор резины для {$this->mark_alt} все типоразмеры!";
        // Закомментить, когда сделается нормально через админку
        $this->description = "Точный подбор шин для {$this->mark} с учетом всех характеристик автомобиля, а именно модель, год, двигатель. Наш онлайн сервис позволяет быстро подобрать оригинальный размер резины для {$this->mark_alt}, а так же предложит варианты замены.";
        $this->keywords = "Шины {$this->mark}, шины {$this->mark_alt}, подбор шин {$this->mark}, резина {$this->mark}, покрышки {$this->mark}, колеса {$this->mark}, онлайн подбор шин {$this->mark}";
        //***
        $this->_title = "Подобрать шины для {$this->mark}";

        $this->breadcrumbs['Подбор шин по марке авто'] = array('/' . App_Route::_getUrl('avtoPodborShin') . '.html', 'подобрать шины по марке авто');
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
        $this->lf = array();
        $this->lf['mp3']=array();
        $si=App_Route::_getUrl('tSearch').'?';
        $this->lf['mp3'][1]=array(
            'chk'=>@in_array(1, $this->M3_) ? true : false,
            'anc'=>'Шип <img src="/app/images/ship.png" alt="шипованные шины">',
            'id'=>'_mp31',
            'url'=>$si.'mp3=1'
        );
        $this->lf['mp3'][0]=array(
            'chk'=>@in_array(0, $this->M3_) ? true : false,
            'anc'=>"Нешип",
            'id'=>'_mp30',
            'url'=>$si.'mp3=0'
        );

        $this->lf['mp1']=array();
        $this->lf['mp1'][2]=array(
            'chk'=>@in_array(2, $this->M1_)? true : false,
            'anc'=>@$this->sezonNames5[2].$this->sezonIcos[2],
            'id'=>'_mp12',
            'url'=>$si.'mp1=2'
        );
        $this->lf['mp1'][1]=array(
            'chk'=>@in_array(1, $this->M1_) ? true : false,
            'anc'=>@$this->sezonNames5[1].$this->sezonIcos[1],
            'id'=>'_mp11',
            'url'=>$si.'mp1=1'
        );
        $this->lf['mp1'][3]=array(
            'chk'=>@in_array(3, $this->M1_) ? true : false,
            'anc'=>@$this->sezonNames5[3].$this->sezonIcos[3],
            'id'=>'_mp13',
            'url'=>$si.'mp1=3'
        );
        // Хак для картинки бренда
        $this->ab->avto_sh_array(@$this->ab->tree['vendor_id'], array_keys($this->ab->tree['models'])[0], null, Array(1));
        //*** Переназначаем мета-теги и описаия ***//
        $page_meta_info = $this->ab->getOne("SELECT * FROM `ab_podbor_meta` WHERE LD = 0 AND H=0 AND gr='1'
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
        $this->relink_href = '/' . App_Route::_getUrl('avtoPodborDiskov') . '/' . App_Route::$param['ap'][0] . '.html';
    }

    public function years()
    {
        $this->apInited = true;
        $this->ab->getTree($this->abCookie);
        // *************
        // собираем все id машинок с такими параметрами
        $this->ab->avto_sh_array(@$this->ab->tree['vendor_id'], @$this->ab->tree['model_id'], @$this->ab->tree['year_id'], Array(1));
        $this->brands = $this->brands_ = array_unique(array_merge($this->ab->getBrandsIds(null, 1), Array($this->ab->brand_id)));
        //
        @ksort($this->ab->avto[1], SORT_STRING);
        $this->sz1=array();
        foreach($this->ab->avto[1] as $c=>$v){
            foreach($v as $vv){
                if($c==10 || $c==15){
                    if(!isset($this->sz1[$vv['P1']])) $this->sz1[$vv['P1']]=array();
                    $this->sz1[$vv['P1']]['OEM'][][1]=$vv;
                }else{
                    $vvv=current($vv);
                    $this->sz1[$vvv['P1']]['Тюннинг'][]=$vv;
                }
            }
        }
        ksort($this->sz1);
        $this->tSearchUrl='/'.App_Route::_getUrl('tSearch').'.html';
        // ****************
        if (!count($this->ab->tree['years'])) return App_Route::redir404();
        $this->view('podborshin/years');

        // тип отображения результатов поиска
        if(@$_COOKIE['stype']=='lenta') $this->searchTpl='catalog/tyres/searchLenta'; else $this->searchTpl='catalog/tyres/searchBlock';

        $this->mark = $this->ab->tree['vendor_name'];
        $this->mark2 = $this->mark_alt = $this->ab->tree['vendor_alt'] != '' ? $this->ab->tree['vendor_alt'] : $this->ab->tree['vendor_name'];
        $this->model = $this->ab->tree['model_name'];
        $this->model2 = $this->model_alt = $this->ab->tree['model_alt'] != '' ? $this->ab->tree['model_alt'] : $this->ab->tree['model_name'];
        $this->fname = $this->ab->fname;

        if(true!==($res=$this->_cat())) return $res;

        //$this->bottomText = $this->ss->getDoc('avtoPodborShin_years$10');
        //if (mb_strlen(trim(Tools::striptags($this->bottomText))) < 20) $this->bottomText = '';

        $this->description = $this->ss->meta['description'];
        $this->keywords = $this->ss->meta['keywords'];

        //$s = ($this->ab->tree['model_text1']);
        //if (!empty($s)) $this->bottomText = $s;

        $this->years = array();
        foreach ($this->ab->tree['years'] as $v) {
            $this->years[] = array(
                'anc' => Tools::unesc($v['name']),
                'url' => '/' . App_Route::_getUrl('avtoPodborShin') . '/' . App_Route::$param['ap'][0] . '--' . App_Route::$param['ap'][1] . '--' . Tools::unesc($v['sname']) . '.html',
                'sname' => Tools::unesc($v['sname'])

            );
        }

        $this->title = "Шины {$this->mark} {$this->model}.  Онлайн подбор шин для {$this->mark_alt} ".Tools::replaceMetaInCatalog($this->model).".";
        // Закомментить, когда сделается нормально через админку
        $this->description = "Удобный подбор шин  для {$this->mark} {$this->model} с учетом всех характеристик автомобиля. Наш онлайн сервис позволяет быстро подобрать оригинальный размер резины   для {$this->mark_alt} ".Tools::replaceMetaInCatalog($this->model).". Широкий выбор типоразмеров для {$this->mark} {$this->model}.";
        $this->keywords = "Шины {$this->mark} {$this->model}, шины {$this->mark_alt} ".Tools::replaceMetaInCatalog($this->model).", подбор шин {$this->mark} {$this->model}, резина {$this->mark} {$this->model}, покрышки {$this->mark} {$this->model}, колеса {$this->mark} {$this->model}, онлайн подбор шин {$this->mark} {$this->model}";
        //***
        $this->_title = "Шины для {$this->mark} {$this->model}";

        $this->breadcrumbs['Подбор шин по марке авто'] = array('/' . App_Route::_getUrl('avtoPodborShin') . '.html', 'подобрать шины и диски по марке авто');
        $this->breadcrumbs[$this->mark] = array('/' . App_Route::_getUrl('avtoPodborShin') . '/' . App_Route::$param['ap'][0] . '.html', '');
        $this->breadcrumbs[$this->model] = '';  
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
        $page_meta_info = $this->ab->getOne("SELECT * FROM `ab_podbor_meta` WHERE LD = 0 AND H=0 AND gr='1'
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
        $this->show_rating = $this->ab->tree['ext_avto_info']['show_rating'];
        $this->avto_image  = !empty($this->ab->tree['ext_avto_info']['avto_image']) ? '/'.Cfg::get('cc_upload_dir').'/'.$this->ab->tree['ext_avto_info']['avto_image'] : '';
        $this->relink_href = '/' . App_Route::_getUrl('avtoPodborDiskov') . '/' . App_Route::$param['ap'][0] . '--' . App_Route::$param['ap'][1] . '.html';
    }

    public function modifs()
    {

        $this->apInited = true;
        $this->ab->getTree($this->abCookie);
        // *************
        // собираем все id машинок с такими параметрами
        $this->ab->avto_sh_array(@$this->ab->tree['vendor_id'], @$this->ab->tree['model_id'], @$this->ab->tree['year_id'], Array(1));
        //
        @ksort($this->ab->avto[1], SORT_STRING);
        $this->sz1=array();
        foreach($this->ab->avto[1] as $c=>$v){
            foreach($v as $vv){
                if($c==10 || $c==15){
                    if(!isset($this->sz1[$vv['P1']])) $this->sz1[$vv['P1']]=array();
                    $this->sz1[$vv['P1']]['OEM'][][1]=$vv;
                }else{
                    $vvv=current($vv);
                    $this->sz1[$vvv['P1']]['Тюннинг'][]=$vv;
                }
            }
        }
        ksort($this->sz1);
        $this->tSearchUrl='/'.App_Route::_getUrl('tSearch').'.html';
        // ****************
        if (!count($this->ab->tree['modifs'])) return App_Route::redir404();
        $this->view('podborshin/modifs');

        // тип отображения результатов поиска
        if(@$_COOKIE['stype']=='lenta') $this->searchTpl='catalog/tyres/searchLenta'; else $this->searchTpl='catalog/tyres/searchBlock';

        $this->mark = $this->ab->tree['vendor_name'];
        $this->mark2 = $this->mark_alt = $this->ab->tree['vendor_alt'] != '' ? $this->ab->tree['vendor_alt'] : $this->ab->tree['vendor_name'];
        $this->model = $this->ab->tree['model_name'];
        $this->model2 = $this->model_alt = $this->ab->tree['model_alt'] != '' ? $this->ab->tree['model_alt'] : $this->ab->tree['model_name'];
        $this->year = $this->ab->tree['year_name'];
        $this->fname = $this->ab->fname;

        if(true!==($res=$this->_cat())) return $res;

        $this->ss->getDoc('avtoPodborShin_modifs$10');
        $this->description = $this->ss->meta['description'];
        $this->keywords = $this->ss->meta['keywords'];
        $this->introText = array(1 => $this->parse($this->ss->cnt_intro), 2 => $this->parse($this->ss->cnt_text));

        //$this->bottomText = ($this->ab->tree['year_text1']);

        $this->modifs = array();
        foreach ($this->ab->tree['modifs'] as $v) {
            $this->modifs[] = array(
                'anc' => Tools::unesc($this->mark . ' ' . $this->model . ' ' . $v['name']),
                'url' => '/' . App_Route::_getUrl('avtoPodborShin') . '/' . App_Route::$param['ap'][0] . '--' . App_Route::$param['ap'][1] . '--' . App_Route::$param['ap'][2] . '--' . Tools::unesc($v['sname']) . '.html',
                'sname' => Tools::unesc($v['sname'])
            );
        }
        $this->title = 'Резина ' . $this->mark.' '.$this->model.' '.$this->year.' г/в.  Онлайн подбор шин для ' . $this->mark_alt.' '.Tools::replaceMetaInCatalog($this->model).' '.$this->year.' г/в.';
        // Закомментить, когда сделается нормально через админку
        $this->description = 'Подбор шин для ' . $this->mark.' '.$this->model.' '.$this->year.' г/в с учетом года выпуска и других характеристик автомобиля. Удобный онлайн сервис позволяет подобрать оригинальный размер резины для ' . $this->mark_alt.' '.Tools::replaceMetaInCatalog($this->model).' '.$this->year.' г/в, а так же варианты замены для данной модели '.$this->year.' года. Широкий выбор типоразмеров!';
        $this->keywords = 'Шины ' . $this->mark.' '.$this->model.' '.$this->year.' г/в, шины ' . $this->mark_alt.' '.Tools::replaceMetaInCatalog($this->model).' '.$this->year.' г/в, подбор шин ' . $this->mark.' '.$this->model.' '.$this->year.' г/в, резина ' . $this->mark.' '.$this->model.' '.$this->year.' г/в, покрышки ' . $this->mark.' '.$this->model.' '.$this->year.' г/в, колеса ' . $this->mark.' '.$this->model.' '.$this->year.' г/в, онлайн подбор шин ' . $this->mark.' '.$this->model.' '.$this->year.' г/в.';
        //***
        $this->_title = 'Шины для ' . $this->mark.' '.$this->model.' '.$this->year.' г/в.';

        $this->breadcrumbs['Подбор шин по марке авто'] = array('/' . App_Route::_getUrl('avtoPodborShin') . '.html', 'подобрать дисков по машине');
        $this->breadcrumbs[$this->mark_alt] = array('/' . App_Route::_getUrl('avtoPodborShin') . '/' . App_Route::$param['ap'][0] . '.html', "размеры шин и дисков для {$this->mark}");
        $this->breadcrumbs[$this->model_alt] = array('/' . App_Route::_getUrl('avtoPodborShin') . '/' . App_Route::$param['ap'][0] . '--' . App_Route::$param['ap'][1] . '.html', "размеры шин и дисков для {$this->mark} {$this->model}");
        $this->breadcrumbs[$this->year . ' года'] = '';
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
        $page_meta_info = $this->ab->getOne("SELECT * FROM `ab_podbor_meta` WHERE LD = 0 AND H=0 AND gr='1'
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
        $this->show_rating = $this->ab->tree['ext_avto_info']['show_rating'];
        $this->avto_image  = !empty($this->ab->tree['ext_avto_info']['avto_image']) ? '/'.Cfg::get('cc_upload_dir').'/'.$this->ab->tree['ext_avto_info']['avto_image'] : '';
        $this->relink_href = '/' . App_Route::_getUrl('avtoPodborDiskov') . '/' . App_Route::$param['ap'][0] . '--' . App_Route::$param['ap'][1] . '--' . App_Route::$param['ap'][2] . '.html';
    }


    public function result()
    {

        if (!@$this->ab->tree['modif_id']) return App_Route::redir404();

        //$this->noSidebar = true;
        $this->ab->avto_sh_array(@$this->ab->tree['vendor_id'], @$this->ab->tree['model_id'], @$this->ab->tree['year_id'], Array(1), $this->ab->tree['modif_id']); 
        $this->view('podborshin/results');

        $this->mark = $this->ab->tree['vendor_name'];
        $this->mark_alt = $this->ab->tree['vendor_alt'] != '' ? $this->ab->tree['vendor_alt'] : $this->ab->tree['vendor_name'];
        $this->model = $this->ab->tree['model_name'];
        $this->model_alt = $this->ab->tree['model_alt'] != '' ? $this->ab->tree['model_alt'] : $this->ab->tree['model_name'];
        $this->year = $this->ab->tree['year_name'];
        $this->modif = $this->ab->tree['modif_name'];

        $this->aname = $this->ab->tree['vendor_name'] . ' ' . $this->ab->tree['model_name'];
        $this->fname = $this->ab->fname;

        $this->ss->getDoc('avtoPodborShin_results$10');
        $this->description = $this->ss->meta['description'];
        $this->keywords = $this->ss->meta['keywords'];
        $this->introText = array(1 => $this->parseText($this->ss->cnt_intro), 2 => $this->parseText($this->ss->cnt_text));

        //$this->ab->avto_sh($this->ab->tree['modif_id']);
        $this->abc = $this->ab->getCommon($this->ab->tree['modif_id']);

        $this->title = 'Шины '.$this->mark.' '.$this->model.' '.$this->year.' г/в '.$this->modif.'.  Онлайн подбор шин для '.$this->mark_alt.' '.Tools::replaceMetaInCatalog($this->model).' '.$this->year.' г/в '.$this->modif.'.';
        // Закомментить, когда сделается нормально через админку
        $this->description = 'Подбор шин для '.$this->mark.' '.$this->model.' '.$this->year.' г/в с двигателем '.$this->modif.'. Виртуальный онлайн сервис позволяет подобрать оригинальный размер резины для '.$this->mark_alt.' '.Tools::replaceMetaInCatalog($this->model).' '.$this->year.' г/в '.$this->modif.', а так же  варианты замены  для данной модели с двигателем '.$this->modif.'.';
        $this->keywords = 'Шины '.$this->mark.' '.$this->model.' '.$this->year.' '.$this->modif.', шины '.$this->mark_alt.' '.Tools::replaceMetaInCatalog($this->model).' '.$this->year.' '.$this->modif.', подбор шин '.$this->mark.' '.$this->model.' '.$this->year.' '.$this->modif.', резина '.$this->mark.' '.$this->model.' '.$this->year.' '.$this->modif.', покрышки '.$this->mark.' '.$this->model.' '.$this->year.' '.$this->modif.', колеса '.$this->mark.' '.$this->model.' '.$this->year.' '.$this->modif.', онлайн подбор шин '.$this->mark.' '.$this->model.' '.$this->year.' '.$this->modif;
        //***
        $this->_title = 'Шины для '.$this->mark.' '.$this->model.' '.$this->year.' г/в '.$this->modif;
        $this->apText = $this->parse($this->ss->getDoc('avtoPodborShin_result$6'));

        $this->breadcrumbs['Подбор шин по марке авто'] = array('/' . App_Route::_getUrl('avtoPodborShin') . '.html', 'подобрать дисков по машине');
        $this->breadcrumbs[$this->mark_alt] = array('/' . App_Route::_getUrl('avtoPodborShin') . '/' . App_Route::$param['ap'][0] . '.html', "размеры шин и дисков для {$this->mark}");
        $this->breadcrumbs[$this->model_alt] = array('/' . App_Route::_getUrl('avtoPodborShin') . '/' . App_Route::$param['ap'][0] . '--' . App_Route::$param['ap'][1] . '.html', "размеры шин и дисков для {$this->mark} {$this->model}");
        $this->breadcrumbs[$this->year] = array($this->prevUrl = '/' . App_Route::_getUrl('avtoPodborShin') . '/' . App_Route::$param['ap'][0] . '--' . App_Route::$param['ap'][1] . '--' . App_Route::$param['ap'][2] . '.html', "размеры шин и дисков для {$this->mark} {$this->model} {$this->year}");
        $this->breadcrumbs[$this->modif] = '';

        @ksort($this->ab->avto[1], SORT_STRING);

        //print_r($this->ab->avto[2]);

        $this->sz1=array();

        ksort($this->ab->avto[1]);
        foreach($this->ab->avto[1] as $c=>$v){
            foreach($v as $vv){
                if($c==10 || $c==15){
                    if(!isset($this->sz1[$vv['P1']])) $this->sz1[$vv['P1']]=array();
                    $this->sz1[$vv['P1']]['OEM'][][1]=$vv;
                }else{
                    $vvv=current($vv);
                    $this->sz1[$vvv['P1']]['Тюннинг'][]=$vv;
                }
            }
        }

        ksort($this->sz1);

        // тип отображения результатов поиска
        if(@$_COOKIE['stype']=='lenta') $this->searchTpl='catalog/tyres/searchLenta'; else $this->searchTpl='catalog/tyres/searchBlock';

        if(true!==($res=$this->_cat())) return $res;

        $this->tSearchUrl='/'.App_Route::_getUrl('tSearch').'.html';
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
        $page_meta_info = $this->ab->getOne("SELECT * FROM `ab_podbor_meta` WHERE LD = 0 AND H=0 AND gr='1'
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
        $this->show_rating = $this->ab->tree['ext_avto_info']['show_rating'];
        $this->avto_image  = !empty($this->ab->tree['ext_avto_info']['avto_image']) ? '/'.Cfg::get('cc_upload_dir').'/'.$this->ab->tree['ext_avto_info']['avto_image'] : '';
        $this->relink_href = '/' . App_Route::_getUrl('avtoPodborDiskov') . '/' . App_Route::$param['ap'][0] . '--' . App_Route::$param['ap'][1] . '--' . App_Route::$param['ap'][2] . '--' . App_Route::$param['ap'][3] . '.html';
    }
/************************ Вывод товаров в подборе ***********************/
    /*
        * получение массива с размерами
        */
    private function _cat()
    {
        // Переносим параметры запроса
        if(!empty($_REQUEST['_p1'])) $this->P1_ = $this->P1 =  @array_keys($_REQUEST['_p1']);
        if(!empty($_REQUEST['_bids'])) $this->brands_ = $this->brands =  @array_keys($_REQUEST['_bids']);
        if(!empty($_REQUEST['mp1'])) $this->M1_ = $this->M1 = @array_keys($_REQUEST['mp1']); // сезон
        if(!empty($_REQUEST['_mp3'][1])) $this->M3_ = $this->M3 = 1; elseif(!empty($_REQUEST['_mp3'][0])) $this->M3_ = $this->M3 = 0;// шипы
        if(!empty($_REQUEST['c_index'])) $this->c_index_ = $this->c_index = $_REQUEST['c_index']; else $this->c_index_ = $this->c_index = ''; // c index
        if(!empty($_REQUEST['runflat'])) $this->runflat_ = $this->runflat = $_REQUEST['runflat']; else $this->runflat_ = $this->runflat = ''; // runflat
        //
        $this->cat=array();
        $this->num=0;
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
            $this->s_info_str .= trim("По вашему запросу: Шины для {$this->mark} {$this->model} " . ($this->year ? $this->year . ' г/в' : '') . " {$this->modif}");
            switch(@$this->M1[0]){
                case 1:
                    $this->s_info_str .= ', летние';
                    break;
                case 2:
                    $this->s_info_str .= ', зимние';
                    break;
                case 3:
                    $this->s_info_str .= ', всесезонные';
                    break;
                default:
                    break;
            }
            $this->s_info_str .= (!empty($this->M3) ? ', шипованные' : '');
            $this->s_info_str .= (!empty($this->c_index) ? ', легкогрузовые' : '');
            $this->s_info_str .= (!empty($this->runflat) ? ', с технологией RunFlat' : '');
            $this->s_info_str .= (!empty($this->P1) ? ', диаметр - '.implode(', ', $this->P1) : '');
            if (!empty($this->brands)) {
                $sel_b_data = Array();
                $f_brands = array_filter($this->brands, function ($val) {
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
        $r=array(
            'gr'=>1,
            'notH'=>1,
            'where'=>array(),
            'exFields'=>array(),
            'select'=>'',
            'having'=>array()
        );

        if(empty($this->M1)) $r['exFields']['MP1']=array(); else $r['M1']=array('list'=>$this->M1); // сезон
        if(empty($this->M2)) $r['exFields']['MP2']=array();
        else {
            if(in_array(1,$this->M2) || in_array(2,$this->M2)) $this->M2[]=4;
            $r['M2']=array('list'=>$this->M2);
        } // автотип
        if(empty($this->P1)) $r['exFields']['P1']=array(); else $r['P1']=array('list'=>$this->P1); // радиус
        if(empty($this->P2)) $r['exFields']['P2']=array(); else $r['P2']=array('list'=>$this->P2); // профиль
        if(empty($this->P3)) $r['exFields']['P3']=array(); else $r['P3']=array('list'=>$this->P3); // ширина
        if(!empty($this->P1) || !empty($this->P2) || !empty($this->P3)) $r['exFields']['P123']=[];
        if(empty($this->brands)) $r['exFields']['brand']=array(); else $r['brand_id']=array('list'=>$this->brands); // бренд
        if($this->M3 === 1) $r['M3']=1; elseif ($this->M3 === 0) $r['M3']=0; // шип
        if($this->runflat!=='') $r['rf']=$this->runflat;
        if($this->c_index!=='') $r['c_index']=$this->c_index;
        // Ограничения для точного показа параметров
        $this->generateRestrictions ($r);
        //
        if(!empty($this->_where)) $r['where']=array_merge($r['where'],$this->_where);
        if(!empty($this->_whereCat)) $r['where']=array_merge($r['where'],$this->_whereCat);
        if(!empty($this->_having)) $r['having']=array_merge($r['having'],$this->_having);

        if($this->hideTSCZero) $r['where'][]=$this->minQtyRadiusSQL;

        @ksort($this->ab->avto[1], SORT_STRING);
        $this->sz1=array();

        $r['sqlReturn']=0;
        $r['nolimits']=1;
        $r['ex']=1;

        $CC_BASE = new CC_Base();
        ksort($this->ab->avto[1]);
        foreach($this->ab->avto[1] as $c=>$v){
            foreach($v as $vv){
                if($c==10 || $c==15){

                    $rr=array(
                        'gr'=>1,
                        'notH'=>1,
                        'where'=>array(),
                        'exFields'=>array(),
                        'select'=>'',
                        'having'=>array()
                    );

                    $rr['sqlReturn']=0;
                    $rr['nolimits']=1;
                    $rr['ex']=1;

                    if(empty($this->M1)) $rr['exFields']['MP1']=array(); else $rr['M1']=array('list'=>$this->M1); // сезон
                    if(empty($this->M2)) $rr['exFields']['MP2']=array();
                    else {
                        if(in_array(1,$this->M2) || in_array(2,$this->M2)) $this->M2[]=4;
                        $rr['M2']=array('list'=>$this->M2);
                    } // автотип

                    if(!empty($vv['P1']) || !empty($vv['P2']) || !empty($vv['P3'])) $rr['exFields']['P123']=[];
                    if(empty($this->brands)) $rr['exFields']['brand']=array(); else $rr['brand_id']=array('list'=>$this->brands); // бренд
                    if($this->M3==='') $rr['exFields']['MP3']=array(); else $rr['M3']=$this->M3; // шип
                    if($this->runflat!=='') $rr['rf']=$this->runflat;
                    if($this->c_index!=='') $rr['c_index']=$this->c_index;

                    if(!empty($vv['P1'])) $rr['P1']=$vv['P1'];
                    if(!empty($vv['P2'])) $rr['P2']=$vv['P2'];
                    if(!empty($vv['P3'])) $rr['P3']=$vv['P3'];

                    $rr['where']=array_merge($rr['where'],$r['where']);

                    $exnum=$CC_BASE->cat_view($rr);

                    if(!isset($this->sz1[$vv['P1']])) $this->sz1[$vv['P1']]=array();
                    $vv['exnum'] = $exnum;
                    $this->sz1[$vv['P1']]['OEM'][][1]=$vv;
                }else{

                    $rr=array(
                        'gr'=>1,
                        'notH'=>1,
                        'where'=>array(),
                        'nolimits'=>1
                    );
                    if(!empty($this->brands_))   $rr['brand_id']=array('list'=>$this->brands_); // бренд
                    if(!empty($this->M1_))       $rr['M1']=array('list'=>$this->M1_); // сезон
                    if(!empty($this->M2_)) {
                        if(in_array(1,$this->M2_) || in_array(2,$this->M2_)) $this->M2_[]=4;
                        $rr['M2']=array('list'=>$this->M2_);
                    } // автотип
                    if($this->M3_!=='')         $rr['M3']=$this->M3_; // шип
                    if(!empty($vv[1]['P1']))       $rr['P1']=array('list'=>$vv[1]['P1']); // радиус
                    if(!empty($vv[1]['P2']))       $rr['P2']=array('list'=>$vv[1]['P2']);
                    if(!empty($vv[1]['P3']))       $rr['P3']=array('list'=>$vv[1]['P3']);
                    if($this->runflat_!=='')     $rr['rf']=$this->runflat;
                    if($this->c_index_!=='')     $rr['c_index']=$this->c_index;
                    if($this->hideTSCZero) $rr['where'][]=$this->minQtyRadiusSQL;
                    $this->exnum1=$this->cc->cat_view($rr);
                    $r1=$this->cc->fetchAll('',MYSQL_ASSOC);

                    $rr=array(
                        'gr'=>1,
                        'notH'=>1,
                        'where'=>array(),
                        'nolimits'=>1
                    );
                    if(!empty($this->brands_))   $rr['brand_id']=array('list'=>$this->brands_); // бренд
                    if(!empty($this->M1_))       $rr['M1']=array('list'=>$this->M1_); // сезон
                    if(!empty($this->M2_)) {
                        if(in_array(1,$this->M2_) || in_array(2,$this->M2_)) $this->M2_[]=4;
                        $rr['M2']=array('list'=>$this->M2_);
                    } // автотип
                    if($this->M3_!=='')         $rr['M3']=$this->M3_; // шип
                    if(!empty($vv[2]['P1']))       $rr['P1']=array('list'=>$vv[2]['P1']); // радиус
                    if(!empty($vv[2]['P2']))       $rr['P2']=array('list'=>$vv[2]['P2']);
                    if(!empty($vv[2]['P3']))       $rr['P3']=array('list'=>$vv[2]['P3']);
                    if($this->runflat_!=='')     $rr['rf']=$this->runflat;
                    if($this->c_index_!=='')     $rr['c_index']=$this->c_index;
                    if($this->hideTSCZero) $rr['where'][]=$this->minQtyRadiusSQL;
                    $exnum2=$CC_BASE->cat_view($rr);
                    $r2=$CC_BASE->fetchAll('',MYSQL_ASSOC);

                    $this->gsuf=array(); // эти суффиксы должны присуствовать в обеих типоразмерах
                    $s=Data::get('cc_runflat_suffix');
                    $this->gsuf=explode(';',$s);
                    $this->gsuf[]='XL';

                    $cat = array();
                    $ex = array();
                    foreach($r1 as $v1){
                        foreach($r2 as $v2){
                            if($v1['brand_id']==$v2['brand_id'])
                                if($v1['model_id']==$v2['model_id'])
                                    if($this->checkSuffixes(Tools::unesc($v1['csuffix']), Tools::unesc($v2['csuffix']))){

                                        // считаем что нашли спарку

                                        $ex['brand'][0][$v1['brand_id']]=array('name'=>Tools::unesc($v1['bname']), 'sname'=>Tools::unesc($v1['brand_sname']));
                                        $ex['MP1'][$v1['MP1']]=1;
                                        $ex['MP2'][$v1['MP2']]=1;
                                        $ex['MP3'][$v1['MP3']]=1;

                                        if(empty($this->_brands) || in_array($v1['brand_id'],$this->_brands)) // проверка по уточняющим фильтрам
                                            if(empty($this->_M1) || in_array($v1['MP1'],$this->_M1)){
                                                $cat[]=1;
                                            }
                                    }
                        }
                    }

                    $exnum=count($cat);
                    $vvv=current($vv);
                    $vv['exnum'] = $exnum;
                    $this->sz1[$vvv['P1']]['Тюннинг'][]=$vv;
                }
            }
        }
        unset($CC_BASE);
        ksort($this->sz1);

        $this->num=$this->cc->cat_view($r);

        // ***   генерим живую форму уточняющего фильтра  *****
        $d=$this->cc->fetchAll();
        //Tools::p($d);
        $this->lf = Array();
        // Получаем все бренды и радиусы
        $r=array(
            'gr'=>1,
            'notH'=>1,
            'where'=>array(),
            'exFields'=>array(),
            'select'=>'',
            'having'=>array()
        );

        // Ограничения для точного показа параметров
        $this->generateRestrictions ($r);
        //
        if(!empty($this->_where)) $r['where']=array_merge($r['where'],$this->_where);
        if(!empty($this->_whereCat)) $r['where']=array_merge($r['where'],$this->_whereCat);
        if(!empty($this->_having)) $r['having']=array_merge($r['having'],$this->_having);

        if($this->hideTSCZero) $r['where'][]=$this->minQtyRadiusSQL;

        $r['order']='cc_brand.pos DESC';
        $r['sqlReturn']=0;
        $r['nolimits']=1;
        $r['ex']=1;

        $this->cc->cat_view($r);
        $d=$this->cc->fetchAll();
        $this->lf = Array();
        foreach($d as $v){
            $si=App_Route::_getUrl('tSearch').'?';
            $P1[$v['P1']]=array(
                'chk'=>@in_array($v['P1'], @array_keys($_REQUEST['_p1']))?true:false,
                'anc'=>"R".$v['P1'],
                'id'=>'_p1'.$this->makeId($v['P1']),
                'url'=>$si.'p1='.$v['P1']
            );
            $si='/'.App_Route::_getUrl('tCat');
            $brands[$v['brand_id']] = array(
                'chk'=>@in_array($v['brand_id'], @array_keys($_REQUEST['_bids']))?true:false,
                'anc'=>$v['bname'],
                'id'=>'_bids'.$v['brand_id'],
                'url'=>$si."/".$v['cat_sname'].'.html'
            );
        }
        @ksort($P1);
        // **************************************************************
        $this->lf['mp3']=array();
        $si=App_Route::_getUrl('tSearch').'?';
        $this->lf['mp3'][1]=array(
            'chk'=>@$this->M3_ === 1 ? true : false,
            'anc'=>'Шип <img src="/app/images/ship.png" alt="шипованные шины">',
            'id'=>'_mp31',
            'url'=>$si.'mp3=1'
        );
        $this->lf['mp3'][0]=array(
            'chk'=>@$this->M3_ === 0 ? true : false,
            'anc'=>"Нешип",
            'id'=>'_mp30',
            'url'=>$si.'mp3=0'
        );

        $this->lf['mp1']=array();
        $this->lf['mp1'][2]=array(
            'chk'=>@in_array(2, $this->M1_)? true : false,
            'anc'=>@$this->sezonNames5[2].@$this->sezonIcos[2],
            'id'=>'_mp12',
            'url'=>$si.'mp1=2'
        );
        $this->lf['mp1'][1]=array(
            'chk'=>@in_array(1, $this->M1_) ? true : false,
            'anc'=>@$this->sezonNames5[1].@$this->sezonIcos[1],
            'id'=>'_mp11',
            'url'=>$si.'mp1=1'
        );
        $this->lf['mp1'][3]=array(
            'chk'=>@in_array(3, $this->M1_) ? true : false,
            'anc'=>@$this->sezonNames5[3].@$this->sezonIcos[3],
            'id'=>'_mp13',
            'url'=>$si.'mp1=3'
        );

        $this->lf['c_index']=array(
            'chk'=>@$this->c_index ? true : false,
        );

        $this->lf['runflat']=array(
            'chk'=>@$this->runflat ? true : false,
        );
        $this->lf['_bids'] = $brands;
        $this->lf['_p1'] = $P1;
        // ****************************************************
        //Tools::p($this->cc->sql_query);

        $this->cc->sqlFree();
        $this->ex=$this->cc->ex_arr;

        unset($this->cc->ex_arr,$this->ex['MP2'][0],$this->ex['P1'][0],$this->ex['MP1'][0]);

        if(!$this->num) {
            GA::_event('Other','searchTyresNoResult',ltrim(@$_SERVER['REQUEST_URI'],'/'),'',true);
            return true;
        }

        $r=array(
            'gr'=>1,
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
            $this->setCookie('lim1', (int)$_GET['num']);
        }
        if(!empty($_COOKIE['lim1'])){
            $this->limit=(int)$_COOKIE['lim1'];
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
            $this->setCookie('ord1', (int)$_GET['ord']);
        }
        if(isset($_COOKIE['ord1'])) $this->sortBy=$_COOKIE['ord1']; else  $this->sortBy=0;

        switch($this->sortBy){
            default: //==0
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
                    $r['order'][]='sezOrd, cc_brand.pos DESC, cc_brand.name,cc_model.name,cc_cat.P7';
                else
                    $r['order'][]='cc_brand.pos DESC, cc_brand.name,cc_model.name,cc_cat.P7';
                break;
            case 1:
                $r['order'][]='cc_brand.name,cc_model.name,cc_cat.P7';
                break;
            case -1:
                $r['order'][]='cc_brand.name DESC,cc_model.name DESC,cc_cat.P5';
                break;
            case 2:
                $r['order'][]='cc_cat.cprice ASC, cc_brand.name,cc_model.name,cc_cat.P7';
                break;
            case -2:
                $r['order'][]='cc_cat.cprice DESC, cc_brand.name,cc_model.name,cc_cat.P7';
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
                    $r['order'][]='cc_model.mspez_id DESC, sezOrd, cc_brand.pos DESC, cc_brand.name,cc_model.name,cc_cat.P7';
                else
                    $r['order'][]='cc_model.mspez_id DESC, cc_brand.pos DESC, cc_brand.name,cc_model.name,cc_cat.P7';
                break;
        }

        // Ограничения для точного показа параметров
        $this->generateRestrictions ($r);
        //
        if(!empty($this->_where)) $r['where']=array_merge($r['where'],$this->_where);
        if(!empty($this->_whereCat)) $r['where']=array_merge($r['where'],$this->_whereCat);
        if(!empty($this->_having)) $r['having']=array_merge($r['having'],$this->_having);

        if($this->hideTSCZero) $r['where'][]=$this->minQtyRadiusSQL;

        //добавляем к запросу все параметры
        if(!empty($this->P1_)) $r['P1']=array('list'=>$this->P1_);
        if(!empty($this->P2_)) $r['P2']=array('list'=>$this->P2_);
        if(!empty($this->P3_)) $r['P3']=array('list'=>$this->P3_);
        if(!empty($this->M1_)) $r['M1']=array('list'=>$this->M1_);
        if($this->M3_ === 1) $r['M3']=1; elseif ($this->M3_ === 0) $r['M3']=0; // шип
        if(!empty($this->M2_)) {
            if(in_array(1,$this->M2_) || in_array(2,$this->M2_)) $this->M2_[]=4;
            $r['M2']=array('list'=>$this->M2_);
        }
        if(!empty($this->brands_)) $r['brand_id']=array('list'=>$this->brands_);

        if($this->runflat_!=='') $r['rf']=$this->runflat_;
        if($this->c_index_!=='') $r['c_index']=$this->c_index_;

        $num=$this->cc->cat_view($r);
        $d=$this->cc->fetchAll();
        if($num) {
            $this->paginator=$this->cc->paginator(Url::$path,Url::$sq,@Url::$sq['page'],$num,$this->limit,'page',array(
                'active'=>	'<li class="active">{page}</li>',
                'noActive'=>'<li><a href="{url}">{page}</a></li>',
                'dots'=>	'<li>...</li>'
            ),5);

            if(!empty($this->filter)){
                $this->markir=[
                    'text'=>""
                ];
                if($num>100) {
                    $this->markir['text'] = "По Вашему запросу найдено слишком большое количество типоразмеров ({$num} шт.). Для более эффективного поиска уточните недостающие параметры шин: <b>";
                }else {
                    $this->markir['text'] = "Для более эффективного поиска уточните недостающие параметры шин: <b>";
                }
                $a=[];
                if(empty($this->P1_)) $a[]="диаметр";
                if(empty($this->P2_)) $a[]="высоту";
                if(empty($this->P3_)) $a[]="ширину";
                $this->markir['text'].=implode(', ',$a);
                if(empty($this->P3_) || empty($this->P2_)) $this->markir['text'].=" профиля";
                $this->markir['text'].='.</b>';
            }
        }
        $burl='/'.App_Route::_getUrl('tTipo').'/';
        foreach($d as $v){
            $this->cat[]=$this->catRow($v,$burl);
        }

        return true;

    }

    function catRow($v,$burl)
    {
        $fullSize="{$v['P3']}/{$v['P2']} R{$v['P1']}".($v['csuffix']!=''?" {$v['csuffix']}":'');
        $vi=array(
            'video_link'=>  $v['video_link'],
            'img3'=>		$this->cc->make_img_path($v['img3']),
            'img2'=>		$this->cc->make_img_path($v['img2']),
            'img1'=>		$this->cc->make_img_path($v['img1']),
            'img1Blk'=>     $this->cc->img_path==''?$this->noimg1:$this->cc->img_path,
            'url'=>			$burl.$v['cat_sname'].'.html',
            'bname'=>       Tools::html($v['bname']),
            'mname'=>       Tools::html($v['mname'].' '.$v['msuffix']),
            'imgAlt'=>      'Фото шины '.Tools::html($v['bname'].' '.$v['mname'].' '.$fullSize),
            'suffixUrl'=>    $this->dict_url($this->cc->dict_search_key($v['csuffix'],$v['gr'],$this->brand_id)),
            'anc'=>	        Tools::unesc($v['bname'].' '.$v['mname']),
            'ancBlk'=>	    Tools::unesc($v['bname'].' '.$v['mname'].' '.$v['msuffix']),
            'title'=>	    "резина ".Tools::html($v['bname'].' '.$v['mname'].' '.$fullSize),
            'qtyText'=>		$v['sc']>12?"&gt;&nbsp;12&nbsp;шт":(!$v['sc']?'-':"{$v['sc']}&nbsp;шт"),
            'scText'=>      $v['sc']?("<span class=\"nal\">на&nbsp;складе&nbsp;(".($v['sc']>12?'&gt;12':$v['sc'])."&nbsp;шт.)</span>"):"<span class=\"nnal\">нет&nbsp;на&nbsp;складе</span>",
            'maxQty'=>		$v['sc'],
            'defQty'=>		$v['sc']>4 || $v['sc']==0?4:$v['sc'],
            'priceText'=>	$v['cprice']?(Tools::nn($v['cprice'])."&nbsp;р."):'звоните',
            'cprice' =>    $v['cprice']?(Tools::nn($v['cprice'])):'0',
            'priceTextBlk'=>$v['cprice']?('<span class="price scl" cat_id="'.$v['cat_id'].'">'.Tools::nn($v['cprice'])."<span class='cur'>&nbsp;руб. за шину</span></span>"):'<span class="price">-<span class="cur">&nbsp;руб. за шину</span></span>',
            'cat_id'=>		$v['cat_id'],
            'razmer'=>		"{$v['P3']}/{$v['P2']}&nbsp;R{$v['P1']}",
            'INIS'=>        "{$v['P7']}",
            'shipIco'=>     '',
            'sezIco'=>      '',
            'inisUrl'=>     $v['P7']!=''?('<a href="#" rel="/ax/explain/inis?v='.$v['P7'].'" title="Что означает '.$v['P7'].'?" class="atip gr">'.$v['P7'].'</a>'):'',
            'newBlk'=>         ($v['mspez_id']==1?'<i></i>':''),
            'newTbl'=>         ($v['mspez_id']==1?'<div class="new">новинка</div>':''),
            'brand_img1'=>  $this->cc->make_img_path($v['brand_img1']),
            'brand_img2'=>  $this->cc->make_img_path($v['brand_img2'])
        );
        if($this->sMode){
            if($v['sc']>=2 || $v['sc']==0) $vi['defQty']=2;
        }

        switch($v['MP1']){
            case 1:
                $vi['sezIcoBlk']='<u class="sun nttip" title="Летние шины"></u>';
                $vi['sezIco']='<img src="/app/images/sun.png" title="Летние шины" class="nttip">';
                break;
            case 2:
                $vi['sezIco']='<img src="/app/images/snow.png" title="Зимние шины" class="nttip">';
                $vi['sezIcoBlk']='<u class="snow nttip" title="Зимние шины" class="nttip"></u>';                
                if($v['MP3']) {
                    $vi['shipIco']='<img src="/app/images/ship.png" title="Шипованные шины" class="nttip">';
                    $vi['sezIcoBlk'].='<em title="Шипованные шины" class="nttip"></em>';
                }
                break;
            case 3:
                $vi['sezIco']='<img src="/app/images/sunsnow.png" title="Всесезонные шины" class="nttip">';
                $vi['sezIcoBlk']='<u class="sun-snow nttip" title="Всесезонные шины"></u>';
                break;
        }

        return $vi;
    }


    /*
    * поиск для живого фильтра
    */
    public function axSearch()
    {
        //sleep(2);
        /*        $changeVars=(int)@$_REQUEST['chVars'];

        if($changeVars){
            $sq=array();
            // таблица соответсвий параметров для подмены
            $tt=array(
                'bids'=>'_bids',
                'mp1'=>'_mp1',
                'mp3'=>'_mp3',
                'at'=>'_at',
                'p1'=>'_p1',
                'p2'=>'_p2',
                'p3'=>'_p3',
                'runflat'=>'_runflat',
                'c_index'=>'_c_index',
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
            $this->r['newParam']=Url::$sq; // для отладки
        }*/
        // Переносим параметры запроса **********************************
        if(!empty($_REQUEST['_p1'])) $this->P1 = $this->P1_ = @array_keys($_REQUEST['_p1']); // радиус
        if(!empty($_REQUEST['_bids'])) $this->brands = $this->brands_ = @array_keys($_REQUEST['_bids']); // бренд
        if(!empty($_REQUEST['mp1'])) $this->M1 = $this->M1_ = @array_keys($_REQUEST['mp1']); // сезон
        if(!empty($_REQUEST['_mp3'][1])) $this->M3 = $this->M3_ = 1; elseif(!empty($_REQUEST['_mp3'][0])) $this->M3 = $this->M3_ = 0;// шипы
        if(!empty($_REQUEST['c_index'])) $this->c_index = $this->c_index_ = $_REQUEST['c_index']; else $this->c_index = $this->c_index_ = ''; // c index
        if(!empty($_REQUEST['runflat'])) $this->runflat = $this->runflat_ = $_REQUEST['runflat']; else $this->runflat = $this->runflat_ = ''; // runflat

        $this->apInited = true;
        $this->ab->getTree($this->abCookie);

        $this->ab->avto_sh_array(@$this->ab->tree['vendor_id'], @$this->ab->tree['model_id'], @$this->ab->tree['year_id'], Array(1), @$this->ab->tree['modif_id']);

        @ksort($this->ab->avto[1], SORT_STRING);
        $this->sz1=array();

        ksort($this->ab->avto[1]);
        foreach($this->ab->avto[1] as $c=>$v){
            foreach($v as $vv){
                if($c==10 || $c==15){
                    if(!isset($this->sz1[$vv['P1']])) $this->sz1[$vv['P1']]=array();
                    $this->sz1[$vv['P1']]['OEM'][][1]=$vv;
                }else{
                    $vvv=current($vv);
                    $this->sz1[$vvv['P1']]['Тюннинг'][]=$vv;
                }
            }
        }

        ksort($this->sz1);
        $time1=Tools::getMicroTime();

        $groups=@$_REQUEST['groups'];

        // Получаем все бренды и радиусы
        /*$all_brands = Array();
        $all_radiuses = Array();
        $r=array(
            'gr'=>1,
            'notH'=>1,
            'where'=>array(),
            'exFields'=>array(),
            'select'=>'',
            'having'=>array()
        );

        // Ограничения для точного показа параметров
        $this->generateRestrictions ($r);
        //
        if(!empty($this->_where)) $r['where']=array_merge($r['where'],$this->_where);
        if(!empty($this->_whereCat)) $r['where']=array_merge($r['where'],$this->_whereCat);
        if(!empty($this->_having)) $r['having']=array_merge($r['having'],$this->_having);

        if($this->hideTSCZero) $r['where'][]='cc_cat.sc>0';

        $r['sqlReturn']=0;
        $r['nolimits']=1;
        $r['ex']=1;

        $this->cc->cat_view($r);
        // ***   генерим живую форму уточняющего фильтра  *****
        $d=$this->cc->fetchAll();
        $this->lf = Array();
        foreach($d as $v){
            $all_radiuses[$v['P1']]=$this->makeId($v['P1']);
            $all_brands[$v['brand_id']] = $v['brand_id'];
        }*/
        // **************************************************************

        // сначала получаем кол-во размеров применяя все параметры

        $r=array(
            'gr'=>1,
            'notH'=>1,
            'nolimits'=>1,
            'count'=>1,
            'where'=>array(),
            'having'=>array());

        if(!empty($this->M1_)) $r['M1']=array('list'=>$this->M1_); // сезон
        if(!empty($this->M2_)) {
            if(in_array(1,$this->M2_) || in_array(2,$this->M2_)) $this->M2_[]=4;
            $r['M2']=array('list'=>$this->M2_);
        } // автотип
        if(!empty($this->P1_)) $r['P1']=array('list'=>$this->P1_); // радиус
        if(!empty($this->brands_)) $r['brand_id']=array('list'=>$this->brands_); // бренд
        if($this->M3_ === 1) $r['M3']=1; elseif ($this->M3_ === 0) $r['M3']=0; // шип
        if($this->runflat_!=='') $r['rf']=$this->runflat_;
        if($this->c_index_!=='') $r['c_index']=$this->c_index_;

        // where
        if($this->hideTSCZero) $r['where'][]=$this->minQtyRadiusSQL;
        if(!empty($this->_where)) $r['where']=array_merge($r['where'],$this->_where);
        if(!empty($this->_whereCat)) $r['where']=array_merge($r['where'],$this->_whereCat);

        //--
        $this->generateRestrictions($r);
        $r['sqlReturn']=0;

        $exnum=$this->cc->cat_view($r);
        //Tools::p($this->M3_);
        $this->r['tn']=$exnum;
        $this->r['formdata']=array();

        if($exnum){
            // теперь получаем значения для каждой группы параметров
            $r['sqlReturn']=0;
            $r['order']='';
            if(@is_array($groups))
                foreach($groups as $group){

                   /* $r['where']=array();
                    if($this->hideTSCZero) $r['where'][]='cc_cat.sc>0';
                    if(!empty($this->_where)) $r['where']=array_merge($r['where'],$this->_where);
                    if(!empty($this->_whereCat)) $r['where']=array_merge($r['where'],$this->_whereCat);*/

                    /*// добавляем все жесткие параметры (1)
                    if(!empty($this->M1)) $r['M1']=array('list'=>$this->M1); else unset($r['M1']); // сезон
                    if(!empty($this->M2)) $r['M2']=array('list'=>$this->M2); else unset($r['M2']); // автотип
                    if(!empty($this->P1)) $r['P1']=array('list'=>$this->P1); else unset($r['P1']); // радиус
                    if(!empty($this->P2)) $r['P2']=array('list'=>$this->P2); else unset($r['P2']); // высота
                    if(!empty($this->P3)) $r['P3']=array('list'=>$this->P3); else unset($r['P3']); // ширина

                    if(!empty($this->brands)) $r['brand_id']=array('list'=>$this->brands); else unset($r['brand_id']); // бренд

                    if($this->M3!=='') $r['M3']=$this->M3;  else unset($r['M3']);// шип

                    if($this->runflat!=='') $r['rf']=$this->runflat; else unset($r['rf']);
                    if($this->c_index!=='') $r['c_index']=$this->c_index; else unset($r['c_index']);*/

                    // добавляем уточняющие параметры из того же состава что и жесткие (1)
                    $r['groupby']='';
                    $r['count']=0;
                    unset($r['rf'], $r['c_index']);
                    $n=0;
                    switch($group){
                        case '_p1':
                            $r['P1'] = null;
                            if(!empty($this->M1))  $r['M1']=array('list'=>$this->M1_); // сезон
                            if(!empty($this->M2))  $r['M2']=array('list'=>$this->M2_); // автотип
                            if(!empty($this->brands))  $r['brand_id']=array('list'=>$this->brands); // бренд
                            if($this->M3 === 1) $r['M3']=1; elseif ($this->M3 === 0) $r['M3']=0; // шип
                            if($this->runflat!=='') $r['rf']=$this->runflat_;
                            if($this->c_index!=='') $r['c_index']=$this->c_index_;
                            $r['fields']="cc_cat.P1+'0' AS FF";
                            $r['groupby']='FF';
                            break;

                        case '_mp1':
                            if(!empty($this->P1))  $r['P1']=array('list'=>$this->P1_); // радиус
                            if(!empty($this->M2))  $r['M2']=array('list'=>$this->M2_); // автотип
                            if(!empty($this->brands))  $r['brand_id']=array('list'=>$this->brands); // бренд
                            if($this->M3 === 1) $r['M3']=1; elseif ($this->M3 === 0) $r['M3']=0; // шип
                            if($this->runflat!=='') $r['rf']=$this->runflat_;
                            if($this->c_index!=='') $r['c_index']=$this->c_index_;
                            $r['fields']='cc_model.P1 AS FF';
                            $r['groupby']='FF';
                            break;

                        case '_mp3':
                            if(!empty($this->P1))  $r['P1']=array('list'=>$this->P1_); // радиус
                            if(!empty($this->M1))  $r['M1']=array('list'=>$this->M1_); // сезон
                            if(!empty($this->M2))  $r['M2']=array('list'=>$this->M2_); // автотип
                            if(!empty($this->brands))  $r['brand_id']=array('list'=>$this->brands); // бренд
                            if($this->runflat!=='') $r['rf']=$this->runflat_;
                            if($this->c_index!=='') $r['c_index']=$this->c_index_;
                            $r['fields']='cc_model.P3 AS FF';
                            $r['groupby']='FF';
                            break;

                        case '_vendor':
                        case '_bids':
                            $r['brand_id'] = null;
                            if(!empty($this->P1))  $r['P1']=array('list'=>$this->P1_); // радиус
                            if(!empty($this->M1))  $r['M1']=array('list'=>$this->M1_); // сезон
                            if(!empty($this->M2))  $r['M2']=array('list'=>$this->M2_); // автотип
                            if($this->M3 === 1) $r['M3']=1; elseif ($this->M3 === 0) $r['M3']=0; // шип
                            if($this->runflat!=='') $r['rf']=$this->runflat_;
                            if($this->c_index!=='') $r['c_index']=$this->c_index_;
                            $r['fields']='cc_brand.brand_id AS FF';
                            $r['groupby']='FF';
                            break;
                        case '_runflat':
                            if($this->c_index==='')
                            {
                                $r['rf']=1;
                                $r['c_index']=''; // взаимоисключающие
                                if(!empty($this->P1))  $r['P1']=array('list'=>$this->P1_); // радиус
                                if(!empty($this->M1))  $r['M1']=array('list'=>$this->M1_); // сезон
                                if(!empty($this->M2))  $r['M2']=array('list'=>$this->M2_); // автотип
                                if(!empty($this->brands))  $r['brand_id']=array('list'=>$this->brands); // бренд
                                if($this->M3 === 1) $r['M3']=1; elseif ($this->M3 === 0) $r['M3']=0; // шип
                                $r['count']=1;
                                $nn=$this->cc->cat_view($r);
                                //$this->r['sql']=$this->cc->sql_query;
                                if($nn) $this->r['formdata']["{$group}"]=1;
                            }
                            break;
                        case '_c_index':
                            if($this->runflat==='')
                            {
                                $r['c_index']=1;
                                $r['rf']=''; // взаимоисключающие
                                if(!empty($this->P1))  $r['P1']=array('list'=>$this->P1_); // радиус
                                if(!empty($this->M1))  $r['M1']=array('list'=>$this->M1_); // сезон
                                if(!empty($this->M2))  $r['M2']=array('list'=>$this->M2_); // автотип
                                if(!empty($this->brands))  $r['brand_id']=array('list'=>$this->brands); // бренд
                                if($this->M3 === 1) $r['M3']=1; elseif ($this->M3 === 0) $r['M3']=0; // шип
                                $r['count']=1;
                                $nn=$this->cc->cat_view($r);
                                //$this->r['sql']=$this->cc->sql_query;
                                if($nn) $this->r['formdata']["{$group}"]=1;
                            }
                            break;
                    }
                    if(!empty($r['groupby']) && empty($r['count'])) {
                        //$this->generateRestrictions ($r);
                        //Tools::p($r, false);
                        $n=$this->cc->cat_view($r);
                        //Tools::p($this->cc->sql_query, false);
                    }
                    if($n) {
                        while($this->cc->next()!==false) {
                            if ($group == '_mp3' && !in_array(2, $this->M1_) && (in_array(1, $this->M1_) || in_array(3, $this->M1_))) {
                                //блокируем "нешип" при выборе  лета и/или всесезонки
                            }
                            elseif ($group == '_mp3' && !isset($this->r['formdata']['_mp12']) && !isset($this->r['formdata']['_mp13'])) {
                                //блокируем "нешип" если есть только лето
                            } elseif ($group != '_at') {
                                $this->r['formdata'][$group . $this->makeId($this->cc->qrow['FF'])] = 1;
                            }
                            else {
                                if ($this->cc->qrow['FF'] == 4) {
                                    $this->r['formdata']["{$group}1"] = 1;
                                    $this->r['formdata']["{$group}2"] = 1;
                                } else {
                                    $this->r['formdata'][$group . $this->makeId($this->cc->qrow['FF'])] = 1;
                                }
                            }
                        }
                    }
                }
        }

        $this->r['queryTime']=Tools::getMicroTime()-$time1;

    }

    /*
    * функция для спарок
    * проверяет наличие какиех то значений из $gsuf в обоих строках $s1 и $s2 одновременно или полное остутсвие наличия, возвращая при этом true
    */
    private function checkSuffixes($s1,$s2)
    {
        $s1=" $s1 ";
        $s2=" $s2 ";
        foreach($this->gsuf as $v){
            if(mb_stripos($s1," $v ")!==false) // есть в первом
                if(mb_stripos($s2," $v ")===false) return false;// нет во втором - не подходит

        }
        return true;

    }

    function dict_url($r)
    {
        $s='';
        foreach($r as $k=>$v) if($v)
            $s.=($s!=''?'&nbsp;&nbsp;':'')."<a href=\"#\" rel=\"/ax/explain/suf?v=$v\" title=\"Что значит $k?\" class=\"atip gr\">$k</a>";
        else $s.=($s!=''?'&nbsp;&nbsp;':'').$k;
        return trim($s);
    }

    public function makeId($v)
    {
        return preg_replace("~[^a-z0-9_-]~iu",'_',$v);
    }

    private function generateRestrictions (&$r)
    {
        if(!empty($this->sz1))
        {
            $cond_array = Array();
            foreach($this->sz1 as $rad=>$v)
            {
                foreach($v as $type=>$vv)
                {
                    foreach($vv as $row)
                    {
                        foreach ($row as $typo)
                        {
                            $cond_array[]  = '(cc_cat.P1 = \''.$rad.'\' AND cc_cat.P2 = \''.((float)$typo['P2']).'\' AND cc_cat.P3 = \''.(float)$typo['P3'].'\')';
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
    // *************************** Вывод для AJAX *******************************
    public function ax_years()
    {
        $this->apInited = true;
        $this->ab->getTree($this->abCookie);
        // *************
        // собираем все id машинок с такими параметрами
        $this->ab->avto_sh_array(@$this->ab->tree['vendor_id'], @$this->ab->tree['model_id'], @$this->ab->tree['year_id'], Array(1));
        $this->brands = $this->brands_ = array_unique(array_merge($this->ab->getBrandsIds(null, 1), Array($this->ab->brand_id)));
        //
        @ksort($this->ab->avto[1], SORT_STRING);
        $this->sz1=array();
        foreach($this->ab->avto[1] as $c=>$v){
            foreach($v as $vv){
                if($c==10 || $c==15){
                    if(!isset($this->sz1[$vv['P1']])) $this->sz1[$vv['P1']]=array();
                    $this->sz1[$vv['P1']]['OEM'][][1]=$vv;
                }else{
                    $vvv=current($vv);
                    $this->sz1[$vvv['P1']]['Тюннинг'][]=$vv;
                }
            }
        }
        ksort($this->sz1);
        $this->tSearchUrl='/'.App_Route::_getUrl('tSearch').'.html';
        // ****************
        if (!count($this->ab->tree['years'])) return App_Route::redir404();

        // тип отображения результатов поиска
        if(@$_COOKIE['stype']=='lenta') $this->searchTpl='catalog/tyres/searchLenta'; else $this->searchTpl='catalog/tyres/searchBlock';

        $this->mark = $this->ab->tree['vendor_name'];
        $this->mark2 = $this->mark_alt = $this->ab->tree['vendor_alt'] != '' ? $this->ab->tree['vendor_alt'] : $this->ab->tree['vendor_name'];
        $this->model = $this->ab->tree['model_name'];
        $this->model2 = $this->model_alt = $this->ab->tree['model_alt'] != '' ? $this->ab->tree['model_alt'] : $this->ab->tree['model_name'];
        $this->fname = $this->ab->fname;

        if(true!==($res=$this->_cat())) return $res;

        $this->years = array();
        foreach ($this->ab->tree['years'] as $v) {
            $this->years[] = array(
                'anc' => Tools::unesc($v['name']),
                'url' => '/' . App_Route::_getUrl('avtoPodborShin') . '/' . App_Route::$param['ap'][0] . '--' . App_Route::$param['ap'][1] . '--' . Tools::unesc($v['sname']) . '.html',
                'sname' => Tools::unesc($v['sname'])

            );
        }
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
        // *************
        // собираем все id машинок с такими параметрами
        $this->ab->avto_sh_array(@$this->ab->tree['vendor_id'], @$this->ab->tree['model_id'], @$this->ab->tree['year_id'], Array(1));
        //
        @ksort($this->ab->avto[1], SORT_STRING);
        $this->sz1=array();
        foreach($this->ab->avto[1] as $c=>$v){
            foreach($v as $vv){
                if($c==10 || $c==15){
                    if(!isset($this->sz1[$vv['P1']])) $this->sz1[$vv['P1']]=array();
                    $this->sz1[$vv['P1']]['OEM'][][1]=$vv;
                }else{
                    $vvv=current($vv);
                    $this->sz1[$vvv['P1']]['Тюннинг'][]=$vv;
                }
            }
        }
        ksort($this->sz1);
        $this->tSearchUrl='/'.App_Route::_getUrl('tSearch').'.html';
        // ****************
        if (!count($this->ab->tree['modifs'])) return App_Route::redir404();

        // тип отображения результатов поиска
        if(@$_COOKIE['stype']=='lenta') $this->searchTpl='catalog/tyres/searchLenta'; else $this->searchTpl='catalog/tyres/searchBlock';

        $this->mark = $this->ab->tree['vendor_name'];
        $this->mark2 = $this->mark_alt = $this->ab->tree['vendor_alt'] != '' ? $this->ab->tree['vendor_alt'] : $this->ab->tree['vendor_name'];
        $this->model = $this->ab->tree['model_name'];
        $this->model2 = $this->model_alt = $this->ab->tree['model_alt'] != '' ? $this->ab->tree['model_alt'] : $this->ab->tree['model_name'];
        $this->year = $this->ab->tree['year_name'];
        $this->fname = $this->ab->fname;

        if(true!==($res=$this->_cat())) return $res;

        $this->modifs = array();
        foreach ($this->ab->tree['modifs'] as $v) {
            $this->modifs[] = array(
                'anc' => Tools::unesc($this->mark . ' ' . $this->model . ' ' . $v['name']),
                'url' => '/' . App_Route::_getUrl('avtoPodborShin') . '/' . App_Route::$param['ap'][0] . '--' . App_Route::$param['ap'][1] . '--' . App_Route::$param['ap'][2] . '--' . Tools::unesc($v['sname']) . '.html',
                'sname' => Tools::unesc($v['sname'])
            );
        }
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

        $this->ab->avto_sh_array(@$this->ab->tree['vendor_id'], @$this->ab->tree['model_id'], @$this->ab->tree['year_id'], Array(1), $this->ab->tree['modif_id']);
        $this->view('podborshin/results');

        $this->mark = $this->ab->tree['vendor_name'];
        $this->mark_alt = $this->ab->tree['vendor_alt'] != '' ? $this->ab->tree['vendor_alt'] : $this->ab->tree['vendor_name'];
        $this->model = $this->ab->tree['model_name'];
        $this->model_alt = $this->ab->tree['model_alt'] != '' ? $this->ab->tree['model_alt'] : $this->ab->tree['model_name'];
        $this->year = $this->ab->tree['year_name'];
        $this->modif = $this->ab->tree['modif_name'];

        $this->aname = $this->ab->tree['vendor_name'] . ' ' . $this->ab->tree['model_name'];
        $this->fname = $this->ab->fname;

        $this->abc = $this->ab->getCommon($this->ab->tree['modif_id']);

        @ksort($this->ab->avto[1], SORT_STRING);

        $this->sz1=array();

        ksort($this->ab->avto[1]);
        foreach($this->ab->avto[1] as $c=>$v){
            foreach($v as $vv){
                if($c==10 || $c==15){
                    if(!isset($this->sz1[$vv['P1']])) $this->sz1[$vv['P1']]=array();
                    $this->sz1[$vv['P1']]['OEM'][][1]=$vv;
                }else{
                    $vvv=current($vv);
                    $this->sz1[$vvv['P1']]['Тюннинг'][]=$vv;
                }
            }
        }
        ksort($this->sz1);

        // тип отображения результатов поиска
        if(@$_COOKIE['stype']=='lenta') $this->searchTpl='catalog/tyres/searchLenta'; else $this->searchTpl='catalog/tyres/searchBlock';

        if(true!==($res=$this->_cat())) return $res;

        $this->tSearchUrl='/'.App_Route::_getUrl('tSearch').'.html';
        $this->extra_meta = '<meta name="robots" content="noindex, follow"/>';
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