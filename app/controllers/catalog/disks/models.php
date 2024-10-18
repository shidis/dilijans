<?
class App_Catalog_Disks_Models_Controller extends App_Catalog_Disks_Common_Controller
{
    public $replica,
    $bname,
    $balt,
    $balt1,
    $baltOther,
    $brand_sname,
    $brand_id,
    $backUrl,
    $num,
    $exnum,
    $filter,
    $models,
    $qmodels,
    $qbrands,
    $noResults;

    public $yandex_social_share = '
        <script type="text/javascript" src="//yastatic.net/es5-shims/0.0.2/es5-shims.min.js" charset="utf-8"></script>
        <script type="text/javascript" src="//yastatic.net/share2/share.js" charset="utf-8"></script>
        <div class="ya-share2" data-services="vkontakte,facebook,odnoklassniki,gplus" data-counter=""></div>
    ';

    public function index()
    {
        $this->view('catalog/disks/models');
        $this->cc->que('brand_by_sname', Url::$spath[2], 1, 2);
        if (!$this->cc->qnum()) return Route::redir404();
        $this->cc->next();
        $this->bname = Tools::unesc($this->cc->qrow['name']);
        $this->balt = Tools::unesc($this->cc->qrow['alt'] != '' ? $this->cc->qrow['alt'] : $this->cc->qrow['name']);
        $this->balt1 = $this->firstS($this->balt);
        $this->baltOther = $this->otherS($this->balt);
        $this->brand_id = $this->cc->qrow['brand_id'];
        $this->brand_sname = $this->cc->qrow['sname'];
        $this->bottomText = ($this->cc->qrow['text']);
        $this->img1 = $this->cc->makeImgPath($this->cc->qrow['img1']);
        $this->seo_img = $this->cc->makeImgPath($this->cc->qrow['seo_img']);
        $this->replica = $this->cc->qrow['replica'];

        if (!empty($this->bottomText)) {
            if (!empty($this->cc->qrow['seo_h2'])) {
                $s = '<h3>' . $this->cc->qrow['seo_h2'] . '</h3>';
            } else $s = '<h3>О ' . ($this->replica ? 'дисках реплика ' : 'бренде ') . $this->balt1 . '</h3>';
            if (!empty($this->img1)) {
                $s .=
                    '<div class="box-logo-brends">'
                    . '<table>'
                    . '<tr>'
                    . '<td><img src="' . $this->img1 . '" alt="реплика ' . $this->balt . '"></td>'
                    . '</tr>'
                    . '</table>';

            }
            $this->bottomText = $s . '</div>' . $this->bottomText;
        }

        if($this->cc->qrow['replica']) $this->replica=1; else $this->replica=0;

        if($this->replica){
            $this->title=(!empty($this->cc->qrow['seo_title'])) ? $this->cc->qrow['seo_title'] : "Литые диски Replica {$this->bname}. Купить колесные диски для {$this->bname}.";
            $this->_title=(!empty($this->cc->qrow['seo_h1'])) ? $this->cc->qrow['seo_h1'] : "Литые диски Replica для {$this->bname}";
            $this->breadcrumbs['Диски реплика']='/'.App_Route::_getUrl('replicaCat').'.html';
            $this->breadcrumbs[]=$this->bname;
            $this->description=(!empty($this->cc->qrow['seo_desc'])) ? $this->cc->qrow['seo_desc'] : "Каталог дисков для автомобиля {$this->bname} по низким ценам в интернет магазине Дилижанс. Большой выбор дисков реплика для авто {$this->bname}. Доставка литых дисков {$this->bname} по все территории России:  Москва, Санкт-Петербург, Екатеринбург, Уфа, Воронеж и т.д.";
            $this->keywords=(!empty($this->cc->qrow['seo_key'])) ? $this->cc->qrow['seo_key'] : "диски реплика {$this->balt}, каталог replica {$this->bname}, купить диски реплика {$this->balt}";

            $d=$this->ab->getOne("SELECT ab.sname, ab.name FROM ab_avto ab JOIN cc_brand cb USING (avto_id) WHERE cb.avto_id={$this->cc->qrow['avto_id']}");
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
                $d_ex=$this->cc->fetchAll('',MYSQLI_ASSOC);
                if (!empty($d_ex)) {
                    $this->replicaCross = array(
                        'url' => '/' . App_Route::_getUrl('avtoPodborDiskov') . '/' . $d['sname'] . '.html',
                        'title' => "Резина {$this->bname}",
                        'anc' => "Шины для {$this->balt}"
                    );
                }

                $this->abCookie=array('svendor'=>$d['sname'], 'smodel' => '', 'syear' => '', 'smodif' => '', 'apMode' => 0);
                $this->ab->getTree($this->abCookie);

                $this->abModels=array();
                foreach ($this->ab->tree['models'] as $v) {
                    $this->abModels[] = array(
                        'anc' => Tools::unesc($d['name'] . ' ' . $v['name']),
                        'title' => 'шины и диски для ' . Tools::unesc($v['alt'] != '' ? "{$this->mark_alt} {$v['alt']}" : "{$this->mark_alt} {$v['name']}"),
                        'url' => '/' . App_Route::_getUrl('avtoPodborDiskov') . '/' . $d['sname'] . '--' . Tools::unesc($v['sname']) . '.html',
                        'sname' => Tools::unesc($v['sname'])
                    );
                }

                // по радиусам
                $cnum=$this->cc->cat(array(
                    'gr'=>2,
                    'where'=>"$this->minQtyRadiusSQL AND cc_brand.replica=1 AND cc_model.P1=".(int)App_Route::$param['d_type'],
                    'fields'=>"cc_cat.model_id, cc_cat.P5+'0' AS P5",
                    'ex'=>1,
                    'nolimits'=>1,
                    'exFields'=>array('P5'=>array())
                ));
                $ex=$this->cc->ex_arr;
                unset($ex['P5'][0],$this->cc->ex_arr);
                $this->rlinks=array();
                $burl='/'.App_Route::_getUrl('dSearch').'.html?replica=1&p5=';
                ksort($ex['P5']);
                foreach(array_keys($ex['P5']) as $v){
                    $this->rlinks[]=array(
                        'url'=>$burl.$v,
                        'title'=>'купить диски реплика r'.$v,
                        'anc'=>"Replica R{$v}"
                    );
                }
            }
        }else{
            $this->title=(!empty($this->cc->qrow['seo_title'])) ? $this->cc->qrow['seo_title'] : "Литые диски {$this->bname} в интернет магазине по привлекательным ценам.";
            $this->_title=(!empty($this->cc->qrow['seo_h1'])) ? $this->cc->qrow['seo_h1'] :"Каталог литых дисков {$this->bname}";
            $this->breadcrumbs['Диски']='/'.App_Route::_getUrl('dCat').'.html';
            $this->breadcrumbs[]=$this->bname;
            $this->description=(!empty($this->cc->qrow['seo_desc'])) ? $this->cc->qrow['seo_desc'] :"Каталог литых дисков {$this->bname} в интернет магазине по привлекательным ценам. Богатый выбор дисков для вашего авто от производителя {$this->bname}. Заказывайте колесные диски с доставкой по Москве и России.";
            $this->keywords=(!empty($this->cc->qrow['seo_key'])) ? $this->cc->qrow['seo_key'] :"литые диски {$this->bname}, каталог {$this->balt}, купить диски {$this->balt}";
        }

        $this->_sidebar();

        if(!$this->_filter()) return;

        $this->_models();

        if($this->replica) $this->backUrl='/'.App_Route::_getUrl('replicaCat').'.html'; else $this->backUrl='/'.App_Route::_getUrl('dCat').'.html';


    }


    // список моделей
    private function _models()
    {
        $this->mLimit=(int)abs(Data::get('d_models_per_page'));
        $page=(int)abs(@Url::$sq['page']);
        if(!$page) $page=1;

        $r=array(
            'gr'=>2,
            'P1' => (int)@App_Route::$param['d_type'],
            'brand_id'=>$this->brand_id,
            'start'=>abs(($page-1)*$this->mLimit),
            'lines'=>$this->mLimit,
            'qSelect'=>array(
                'scDiv'=>array()
            ),
            'whereCat'=>array($this->minQtyRadiusSQL),
            'order'=>"scDiv DESC, m_pos ASC, cc_model.name ASC"
        );

        $this->num=$this->cc->models($r);

        if(!$this->num) {
            $this->bottomTextTitle=$this->topText=$this->bottomText='';
            $this->noResults=$this->parse($this->ss->getDoc('t_models_nr_sub$6'));
            return false;
        }

        $d=$this->cc->fetchAll('', MYSQLI_ASSOC);

        $this->paginator=Tools::paginator(Url::$path,Url::$sq,$page,$this->num,$this->mLimit,'page',array(
            'active'=>	'<li class="active">{page}</li>',
            'noActive'=>'<li><a href="{url}">{page}</a></li>',
            'dots'=>	'<li>...</li>'
            ),21);
        $s='';
        foreach($this->paginator as $vv) $s.=$vv;
        $this->paginator=$s;

        $this->models=array();

        $burl='/'.App_Route::_getUrl('dModel').'/';
        $CC_Ctrl = new CC_Ctrl();
        $stickers_list = $CC_Ctrl::getStickersList();
        foreach($d as $v)
        {
            // *****************************************************************
            $v['catalog_items'] = array('gt0'=>array(), 0=>array());
            $this->cc->cat_view(array(
                'model_id' => $v['model_id'],
                'gr'=>2,
                'scDiv'=>1,
                'nolimits'=>true,
                'order'=>'scDiv DESC, cc_cat.P5, cc_cat.P4,cc_cat.P6, cc_cat.P1, cc_cat.P2, cc_cat.P3'
            ));
            $desc=$this->cc->fetchAll();
            $v['item_rads']=array();    
            $prices = Array();
            $colors_url = Array();
            foreach($desc as $item)
            {
                if(empty($this->diametr) || $this->diametr==$item['P5']) 
                {
                    if($item['sc']) // Убрать, если нужны будут и те, которых нет на складе
                    {
                        $colors_url[] = $this->dict_url($this->cc->dict_search_key($item['csuffix'],$item['gr'],$this->brand_id));
                        if ($item['cprice'] > 0)
                        {
                            $prices[] = $item['cprice'];
                        }
                    }
                }
                if($item['sc']) 
                {
                    $v['item_rads'][$item['P5']] = ($this->diametr==$item['P5']) ? true : false;
                }
            }
            // *****************************************************************
            $malt=Tools::unesc($v['alt']!=''?$v['alt']:$v['name']);
            $malt1=$this->firstS($malt);
            $mname=Tools::unesc($v['name']);
            $v['suffix']=Tools::unesc($v['suffix']);
            // Стикеры
            if (!empty($v['sticker_id'])) {
                $m_sticker = $CC_Ctrl->getModelSticker($v['model_id']);
                if (!empty($m_sticker)) {
                    @$m_sticker = array_merge($m_sticker, $stickers_list[$m_sticker['sticker_type']]);
                }
            }
            else $m_sticker = array();
            //
            $vi=array(
                'anc'=>"{$this->bname} {$mname} {$v['suffix']}",
                'alt'=>"диски {$this->balt} {$malt1}",
                'url'=>$burl.$v['sname'].'.html',
                'img'=>($this->cc->makeImgPath($v['img1'])).($this->cc->makeImgPath($v['img1']) ? '?v='.ExLib::loadImagesId() : ''),
                'scDiv'=>$v['scDiv'],
                'spezId'=>$v['mspez_id']==2?true:false,
                'model_sticker' => $m_sticker,
                'video_link' => $v['video_link']
            );
            if($vi['img']=='') $vi['img']=$this->noimg2;
            $vi['colors'] = array_unique($colors_url, SORT_STRING);
            $vi['prices'] = $prices;
            $vi['radiuses'] = $v['item_rads'];
            $this->models[]= $vi;
        }
        unset($CC_Ctrl);
        return true;
    }

    function axModels()
    {
        $this->view('catalog/disks/models');
        $this->cc->que('brand_by_sname', Url::$spath[2], 1, 2);
        if (!$this->cc->qnum()) return Route::redir404();
        $this->cc->next();
        $this->brand_id = $this->cc->qrow['brand_id'];
        $this->brand_sname = $this->cc->qrow['sname'];
        if($this->cc->qrow['replica']) $this->replica=1; else $this->replica=0;

        if(!empty($_GET['num'])){
            $this->limit=intval(@Url::$sq['num'])>0?intval(Url::$sq['num']):20;
        }
        if(empty($this->limit)) $this->limit=(int)abs(Data::get('d_models_per_page'));
        $page=@(int)Url::$sq['page'] ? @(int)Url::$sq['page'] : 1;

        $cc = new CC_Ctrl();
        $r=array(
            'gr'=>2,
            'P1' => (int)@App_Route::$param['d_type'],
            'brand_id'=>$this->brand_id,
            'qSelect'=>array(
                'scDiv'=>array()
            ),
            'nolimits' => true,
            'whereCat'=>array($this->minQtyRadiusSQL),
            'order'=>"scDiv DESC, m_pos ASC, cc_model.name ASC"
        );
        $this->num=$cc->models($r);

        $r=array(
            'gr'=>2,
            'P1' => (int)@App_Route::$param['d_type'],
            'brand_id'=>$this->brand_id,
            'start'=>abs(($page-1)*$this->limit),
            'lines'=>$this->limit,
            'qSelect'=>array(
                'scDiv'=>array()
            ),
            'whereCat'=>array($this->minQtyRadiusSQL),
            'order'=>"scDiv DESC, m_pos ASC, cc_model.name ASC"
        );
        $this->ex_num=$this->cc->models($r);
        $d=$this->cc->fetchAll('', MYSQLI_ASSOC);

        $this->paginator=Tools::paginator(Url::$path,Url::$sq,$page,$this->num,$this->limit,'page',array(
            'active'=>	'<li class="active">{page}</li>',
            'noActive'=>'<li><a href="{url}">{page}</a></li>',
            'dots'=>	'<li>...</li>'
        ),21);
        $s='';
        foreach($this->paginator as $vv) $s.=$vv;
        $this->paginator=$s;

        $this->models=array();

        $burl='/'.App_Route::_getUrl('dModel').'/';
        $CC_Ctrl = new CC_Ctrl();
        $stickers_list = $CC_Ctrl::getStickersList();
        foreach($d as $v)
        {
            // *****************************************************************
            $v['catalog_items'] = array('gt0'=>array(), 0=>array());
            $this->cc->cat_view(array(
                'model_id' => $v['model_id'],
                'gr'=>2,
                'scDiv'=>1,
                'nolimits'=>true,
                'order'=>'scDiv DESC, cc_cat.P5, cc_cat.P4,cc_cat.P6, cc_cat.P1, cc_cat.P2, cc_cat.P3'
            ));
            $desc=$this->cc->fetchAll();
            $v['item_rads']=array();
            $prices = Array();
            $colors_url = Array();
            foreach($desc as $item)
            {
                if(empty($this->diametr) || $this->diametr==$item['P5'])
                {
                    if($item['sc']) // Убрать, если нужны будут и те, которых нет на складе
                    {
                        $colors_url[] = $this->dict_url($this->cc->dict_search_key($item['csuffix'],$item['gr'],$this->brand_id));
                        if ($item['cprice'] > 0)
                        {
                            $prices[] = $item['cprice'];
                        }
                    }
                }
                if($item['sc'])
                {
                    $v['item_rads'][$item['P5']] = ($this->diametr==$item['P5']) ? true : false;
                }
            }
            // *****************************************************************
            $malt=Tools::unesc($v['alt']!=''?$v['alt']:$v['name']);
            $malt1=$this->firstS($malt);
            $mname=Tools::unesc($v['name']);
            $bname=Tools::unesc($v['bname']);
            $v['suffix']=Tools::unesc($v['suffix']);
            // Стикеры
            if (!empty($v['sticker_id'])) {
                $m_sticker = $CC_Ctrl->getModelSticker($v['model_id']);
                if (!empty($m_sticker)) {
                    @$m_sticker = array_merge($m_sticker, $stickers_list[$m_sticker['sticker_type']]);
                }
            }
            else $m_sticker = array();
            //
            $vi=array(
                'anc'=>"{$bname} {$mname} {$v['suffix']}",
                'alt'=>"диски {$this->balt} {$malt1}",
                'url'=>$burl.$v['sname'].'.html',
                'img'=>$this->cc->makeImgPath($v['img1']),
                'scDiv'=>$v['scDiv'],
                'spezId'=>$v['mspez_id']==2?true:false,
                'model_sticker' => $m_sticker,
                'video_link' => $v['video_link']
            );
            if($vi['img']=='') $vi['img']=$this->noimg2;
            $vi['colors'] = array_unique($colors_url, SORT_STRING);
            $vi['prices'] = $prices;
            $vi['radiuses'] = $v['item_rads'];
            $this->models[]= $vi;
        }
        unset($CC_Ctrl);

        // ****************** Вывод и выход ******************
        global $app;
        if (is_file($app->namespace . '/view/catalog/disks/axModels.php')) {
            extract((array)$app->controllerInstance, EXTR_OVERWRITE);
            extract($app->controllerInstance->_data, EXTR_OVERWRITE);
            include $app->namespace . '/view/catalog/disks/axModels.php';
        } else
            throw new AppException ('[App::output()]: ' . $app->namespace . '/view/catalog/disks/axModels open fault.');
        exit(200);
    }


    public function _sidebar()
    {
        // быстрые бренды
        $burl='/'.App_Route::_getUrl('dCat').'/';
        $this->qbrands=array(0=>array());
        $r=array(
            'gr'=>2,
            'd_type'=>(int)@App_Route::$param['d_type'],
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

        // модели в сайдбаре. bname должно быть определено выше
        $r=array(
            'gr'=>2,
            'P1' => (int)@App_Route::$param['d_type'],
            'brand_id'=>$this->brand_id,
            'nolimits'=>1,
            'qSelect'=>array(
                'scDiv'=>array('where'=>$this->minQtyRadiusSQL)
            ),
            'order'=>"m_pos ASC, cc_model.name"
        );
        $this->cc->models($r);
        $d=$this->cc->fetchAll('', MYSQLI_ASSOC);
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

    public function _filter()
    {
        //фильтр
        $r=array(
            'gr'=>2,
            'brand_id'=>$this->brand_id,
            'where'=>array($this->minQtyRadiusSQL, "cc_model.P1='".(int)@App_Route::$param['d_type']."'"),
            'fields'=>"cc_model.model_id, cc_cat.P5+'0' AS P5, cc_cat.P4+'0' AS P4, cc_cat.P6+'0' AS P6",
            'ex'=>1,
            'nolimits'=>1,
            'exFields'=>array('P5'=>array(),'P46'=>array())
        );

        $this->exnum=$this->cc->cat($r);

        $this->ex=$this->cc->ex_arr;
        unset($this->ex['P5'][0],$this->cc->ex_arr);

        $this->lf=array();
        $this->lfi=0;
        $this->lfh=array();

        if(count(@$this->ex['P5'])>1){
            $this->lfi++;
            ksort($this->ex['P5']);
            $this->lf['p5']=array();
            $si=App_Route::_getUrl('dSearch').'?';
            foreach($this->ex['P5'] as $k=>$v){
                $this->lf['p5'][$k]=array(
                    'chk'=>false,
                    'anc'=>"R$k",
                    'id'=>'_p5'.$this->makeId($k),
                    'url'=>$si.'p5='.$k
                );
            }
        }
        if(count(@$this->ex['P46'])>1){
            $this->lfi++;
            uksort($this->ex['P46'], array($this,'usortSVfoo'));
            $this->lf['sv']=array();
            $si=App_Route::_getUrl('dSearch').'?';
            foreach($this->ex['P46'] as $k=>$v) if($k>0) {
                $this->lf['sv'][$k]=array(
                    'chk'=>false,
                    'anc'=>$k,
                    'id'=>'_sv'.$this->makeId($k),
                    'url'=>$si.'sv='.$k
                );
            }
        }



        if(@$this->replica) $this->lfh['replica']=@$this->replica;
        $this->lfh['vendor']=$this->brand_sname;


        return true;

    }

}