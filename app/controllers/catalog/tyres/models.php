<?
class App_Catalog_Tyres_Models_Controller extends App_Catalog_Tyres_Common_Controller
{

    public $radius,
    $sezonId,
    $atId,
    $shipId,
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
    // модели без сезона
    public function index()
    {
        $this->view('catalog/tyres/models');

        $this->cc->que('brand_by_sname',Url::$spath[2],1,1);
        if(!$this->cc->qnum()) return Route::redir404();
        $this->cc->next();
        $this->bname=Tools::unesc($this->cc->qrow['name']);
        $this->balt=Tools::unesc($this->cc->qrow['alt']!=''?$this->cc->qrow['alt']:$this->cc->qrow['name']);
        $this->balt1=$this->firstS($this->balt);
        $this->baltOther=$this->otherS($this->balt);
        $this->brand_id=$this->cc->qrow['brand_id'];
        $this->brand_sname=$this->cc->qrow['sname'];
        $this->img1=($this->cc->makeImgPath($this->cc->qrow['img1'])).'?v='.ExLib::loadImagesId();

        if(!empty(App_Route::$param['radius'])) $this->radius=App_Route::$param['radius']; else $this->radius=0;
        if(!empty(App_Route::$param['M1'])) $this->sezonId=App_Route::$param['M1']; else $this->sezonId=0;
        if(!empty(App_Route::$param['M2'])) $this->atId=App_Route::$param['M2']; else $this->atId=0;
        if(isset(App_Route::$param['M3']) && @App_Route::$param['M3']!=='') $this->shipId=App_Route::$param['M3']; else $this->shipId='';

        switch($this->atId){
            case 1: // легковые
                if(!empty($this->radius)){
                    $this->title="Легковые {$this->sezonNames4[$this->sezonId]} ".($this->shipId?'шипованные ':($this->shipId===0?'нешипованные ':''))."шины {$this->bname} R{$this->radius}. Купить {$this->sezonNames4[$this->sezonId]} ".($this->shipId?'шипованные ':($this->shipId===0?'нешипованные ':''))."автошины {$this->balt1} R{$this->radius} для легковых авто. ".Tools::mb_ucfirst(trim($this->sezonNames3[$this->sezonId]).' резина')." {$this->balt} с доставкой по Москве";
                    $this->_title="Легковые ".($this->shipId?'шипованные ':($this->shipId===0?'нешипованные ':''))."шины {$this->bname} R{$this->radius}";

                    $this->breadcrumbs['Шины']='/'.App_Route::_getUrl('tCat').'.html';
                    $this->breadcrumbs["легковые шины {$this->balt1}"]='/'.App_Route::_getUrl('tLight').'/'.$this->brand_sname.'.html';
                    $this->breadcrumbs[]=$this->bname.' R'.$this->radius;

                }else{
                    $this->title="Легковые {$this->sezonNames4[$this->sezonId]} ".($this->shipId?'шипованные ':($this->shipId===0?'нешипованные ':''))."шины {$this->bname}. Купить {$this->sezonNames4[$this->sezonId]} ".($this->shipId?'шипованные ':($this->shipId===0?'нешипованные ':''))."автошины {$this->balt1} для легковых авто. ".Tools::mb_ucfirst(trim($this->sezonNames3[$this->sezonId]).' резина')." {$this->balt}  с доставкой по Москве";
                    $this->_title="Легковые ".($this->shipId?'шипованные ':($this->shipId===0?'нешипованные ':''))."шины {$this->bname}";

                    $this->breadcrumbs['Шины']='/'.App_Route::_getUrl('tCat').'.html';
                    $this->breadcrumbs[]="легковые шины {$this->balt}";
                }
                $this->backUrl='javascript:goBack();';
                break;
            case 2:  // SUV
                if(!empty($this->radius)){
                    $this->title="{$this->sezonNames[$this->sezonId]} ".($this->shipId?'шипованные ':($this->shipId===0?'нешипованные ':''))." для внедорожников {$this->bname} R{$this->radius}. Купить  {$this->sezonNames8[$this->sezonId]} ".($this->shipId?'шипованную ':($this->shipId===0?'нешипованную ':''))."внедорожную резину {$this->balt} R{$this->radius} для джипов. Грязевые шины {$this->bname} R{$this->radius} по низким ценам.";

                    $this->_title="{$this->sezonNames[$this->sezonId]} ".($this->shipId?'шипованные ':($this->shipId===0?'нешипованные ':''))." {$this->bname} R{$this->radius} для внедорожников. Купить {$this->sezonNames8[$this->sezonId]} ".($this->shipId?'шипованную ':($this->shipId===0?'нешипованную ':''))."грязевую резину {$this->balt} R{$this->radius} для джипа.";

                    $this->breadcrumbs['Шины']='/'.App_Route::_getUrl('tCat').'.html';
                    $this->breadcrumbs['Для внедорожников']='/'.App_Route::_getUrl('tSUV').'.html';
                    if($this->sezonId)
                        $this->breadcrumbs[$this->sezonNames5[$this->sezonId]]=$this->tSezAtRoute["{$this->sezonId}{$this->sezonId}"].'/'.$this->brand_sname.'.html';
                    $this->breadcrumbs[]='R'.$this->radius;

                    $this->description="Большой выбор {$this->sezonNames6[$this->sezonId]} ".($this->shipId?'шипованных ':($this->shipId===0?'нешипованных ':''))."шин {$this->bname} R{$this->radius} для внедорожников. Вы можете купить {$this->sezonNames8[$this->sezonId]} ".($this->shipId?'шипованную ':($this->shipId===0?'нешипованную ':''))."грязевую резину {$this->balt1} R{$this->radius} для вашего джипа по привликательной цене в интернет магазине шин и дисков Dilijans. Доставка по москве и России.";

                }else{

                    $this->title="{$this->sezonNames[$this->sezonId]} ".($this->shipId?'шипованные ':($this->shipId===0?'нешипованные ':''))." для внедорожников {$this->bname}. Купить  {$this->sezonNames8[$this->sezonId]} ".($this->shipId?'шипованную ':($this->shipId===0?'нешипованную ':''))."внедорожную резину {$this->balt} для джипов. Грязевые шины {$this->bname} по низким ценам.";

                    $this->_title="{$this->sezonNames[$this->sezonId]} ".($this->shipId?'шипованные ':($this->shipId===0?'нешипованные ':''))." {$this->bname} для внедорожников. Купить {$this->sezonNames8[$this->sezonId]} ".($this->shipId?'шипованную ':($this->shipId===0?'нешипованную ':''))."грязевую резину {$this->balt} для джипа.";

                    $this->breadcrumbs['Шины']='/'.App_Route::_getUrl('tCat').'.html';
                    $this->breadcrumbs['Для внедорожников']='/'.App_Route::_getUrl('tSUV').'.html';
                    if($this->sezonId)
                        $this->breadcrumbs[$this->sezonNames5[$this->sezonId]]=$this->tSezAtRoute["{$this->sezonId}{$this->sezonId}"].'/'.$this->brand_sname.'.html';
                    $this->breadcrumbs[]="{$this->bname}";

                    $this->description="Большой выбор {$this->sezonNames6[$this->sezonId]} ".($this->shipId?'шипованных ':($this->shipId===0?'нешипованных ':''))."шин {$this->bname} для внедорожников. Вы можете купить {$this->sezonNames8[$this->sezonId]} ".($this->shipId?'шипованную ':($this->shipId===0?'нешипованную ':''))."грязевую резину {$this->balt1} для вашего джипа по привликательной цене в интернет магазине шин и дисков Dilijans. Доставка по москве и России.";
                }
                $this->backUrl='javascript:goBack();';
                break;
            case 3: // Усиленные
                if(!empty($this->radius)){
                    $this->title="{$this->sezonNames4[$this->sezonId]} ".($this->shipId?'шипованные ':($this->shipId===0?'нешипованные ':''))."шины для микроавтобусов {$this->bname} R{$this->radius}. Купить {$this->sezonNames4[$this->sezonId]} ".($this->shipId?'шипованные ':($this->shipId===0?'нешипованные ':''))."автошины {$this->balt1} R{$this->radius} для микроавтобусов. ".Tools::mb_ucfirst(trim($this->sezonNames3[$this->sezonId]).' резина')." {$this->balt} с доставкой по Москве";
                    $this->_title="{$this->sezonNames5[$this->sezonId]} ".($this->shipId?'шипованные ':($this->shipId===0?'нешипованные ':''))."шины для микроавтобусов {$this->bname} R{$this->radius}";

                    $this->breadcrumbs['Шины']='/'.App_Route::_getUrl('tCat').'.html';
                    $this->breadcrumbs["шины для микроавтобусов {$this->balt1}"]='/'.App_Route::_getUrl('tStrong').'/'.$this->brand_sname.'.html';
                    $this->breadcrumbs[]=$this->bname.' R'.$this->radius;

                }else{
                    $this->title="{$this->sezonNames[$this->sezonId]} {$this->balt1} ".($this->shipId?'шипованные ':($this->shipId===0?'нешипованные ':''))."для микроавтобусов, купить легкогрузовые ".($this->shipId?'шипованные ':($this->shipId===0?'нешипованные ':''))."шины {$this->bname} - цены, фото, актуальное наличие {$this->baltOther}";
                    $this->_title="{$this->sezonNames5[$this->sezonId]} ".($this->shipId?'шипованные ':($this->shipId===0?'нешипованные ':''))."шины для микроавтобусов {$this->bname}";

                    $this->breadcrumbs['Шины']='/'.App_Route::_getUrl('tCat').'.html';
                    $this->breadcrumbs[]="шины для микроавтобусов {$this->balt}";
                }
                $this->backUrl='javascript:goBack();';
                break;
            default:
                // без сезона
                if(empty($this->sezonId))
                    if(!empty($this->radius)){
                        $this->title="Продажа шин {$this->bname} R{$this->radius}. Купить резину {$this->balt1} R{$this->radius} по низкой цене в Москве. Каталог летних и зимних шин {$this->balt} R {$this->radius} с доставкой по Москве. Каталог летних и зимних шин {$this->bname} R{$this->radius} со скидкой.";
                        $this->_title="Шины {$this->bname} R{$this->radius}. Купить резину {$this->balt1} R{$this->radius} по каталогу.";

                        $this->breadcrumbs['Шины']='/'.App_Route::_getUrl('tCat').'.html';
                        $this->breadcrumbs[$this->balt1]='/'.App_Route::_getUrl('tCat').'/'.$this->brand_sname.'.html';
                        $this->breadcrumbs[]=$this->bname.' R'.$this->radius;

                        $this->description="Самый большой выбор шин {$this->bname} R{$this->radius} в интернет магазине Dilijans. В нашем каталоге шин {$this->balt1} R{$this->radius} Вы найдете лучшую резину для своего автомобиля. Зимняя и летняя резина {$this->balt1} R{$this->radius} по привлекательным ценам. Мы доставим Вашу резину {$this->bname} R{$this->radius} в любую точку Москвы или любой город России.";

                    }else{
                        $this->title="Продажа шин {$this->bname}. Купить резину {$this->balt1} по низкой цене в Москве. Каталог летних и зимних шин {$this->balt} с доставкой по Москве. Каталог летних и зимних шин {$this->bname} со скидкой.";
                        $this->_title="Шины {$this->bname}. Купить резину {$this->balt1} по каталогу.";

                        $this->breadcrumbs['Шины']='/'.App_Route::_getUrl('tCat').'.html';
                        $this->breadcrumbs[]=$this->bname;

                        $this->description="Самый большой выбор шин {$this->bname} в интернет магазине Dilijans. В нашем каталоге шин {$this->balt1} Вы найдете лучшую резину для своего автомобиля. Зимняя и летняя резина {$this->balt1} по привлекательным ценам. Мы доставим Вашу резину {$this->bname} в любую точку Москвы или любой город России.";

                        $this->bottomText=($this->cc->qrow['text']);
                        if(!empty($this->bottomText)){
                            $s='<h3>О бренде '.$this->balt1.'</h3>';
                            if(!empty($this->img1)){
                                $s.=
                                '<div class="box-logo-brends">'
                                .'<table>'
                                .'<tr>'
                                .'<td><img src="'.$this->img1.'" alt="шины '.$this->balt.'"></td>'
                                .'</tr>'
                                .'</table>';

                            }
                            $this->bottomText=$s.'</div>'.$this->bottomText;
                        }
                }
                else
                    // с сезоном
                    if(!empty($this->radius)){
                        $this->title="{$this->sezonNames5[$this->sezonId]} ".($this->shipId?'шипованные ':($this->shipId===0?'нешипованные ':''))."шины {$this->bname} R{$this->radius}. Купить {$this->sezonNames8[$this->sezonId]} ".($this->shipId?'шипованную ':($this->shipId===0?'нешипованную ':''))."резину {$this->bal1} R{$this->radius} по привлекательной цене в Москве. Каталог  {$this->sezonNames6[$this->sezonId]} шин {$this->balt} R{$this->radius} со скидкой.";

                        $this->_title="{$this->sezonNames5[$this->sezonId]} ".($this->shipId?'шипованные ':($this->shipId===0?'нешипованные ':''))." шины {$this->bname} R{$this->radius}. Купить {$this->sezonNames8[$this->sezonId]} ".($this->shipId?'шипованную ':($this->shipId===0?'нешипованную ':''))."резину {$this->balt} R{$this->radius} по каталогу.";

                        $this->breadcrumbs['Шины']='/'.App_Route::_getUrl('tCat').'.html';
                        $this->breadcrumbs[$this->sezonNames5[$this->sezonId]]=$this->tRoute[$this->sezonId].'.html';
                        if($this->shipId) $this->breadcrumbs['Шипованные']=$this->tRoute['21'].'.html';
                        elseif($this->shipId===0) $this->breadcrumbs['Нешипованные']=$this->tRoute['20'].'.html';
                        $this->breadcrumbs[]=$this->bname.' R'.$this->radius;

                    $this->description="Самый большой выбор {$this->sezonNames6[$this->sezonId]} ".($this->shipId?'шипованных ':($this->shipId===0?'нешипованных ':''))."шин {$this->bname} R{$this->radius} в интернет магазине Dilijans. В нашем каталоге {$this->sezonNames6[$this->sezonId]} ".($this->shipId?'шипованных ':($this->shipId===0?'':''))."шин {$this->balt} Вы найдете лучшую {$this->sezonNames8[$this->sezonId]} резину ".($this->shipId?'на шипах ':($this->shipId===0?'':''))."диметром R{$this->radius} для своего автомобиля. {$this->sezonNames5[$this->sezonId]} шины ".($this->shipId?'с шипами ':($this->shipId===0?' ':''))." шины {$this->balt} R{$this->radius} по привлекательным ценам. Мы доставим Ваши шины {$this->bname} в любую точку Москвы или любой город России.";

                }else{
                    $this->title="{$this->sezonNames5[$this->sezonId]} ".($this->shipId?'шипованные ':($this->shipId===0?'нешипованные ':''))."шины {$this->bname} . Купить {$this->sezonNames8[$this->sezonId]} ".($this->shipId?'шипованную ':($this->shipId===0?'нешипованную ':''))."резину {$this->bal1} по привлекательной цене в Москве. Каталог  {$this->sezonNames6[$this->sezonId]} шин {$this->balt} со скидкой.";

                    $this->_title="{$this->sezonNames5[$this->sezonId]} ".($this->shipId?'шипованные ':($this->shipId===0?'нешипованные ':''))." шины {$this->bname}. Купить {$this->sezonNames8[$this->sezonId]} ".($this->shipId?'шипованную ':($this->shipId===0?'нешипованную ':''))."резину {$this->balt} по каталогу.";

                    $this->breadcrumbs['Шины']='/'.App_Route::_getUrl('tCat').'.html';
                    $this->breadcrumbs[$this->sezonNames5[$this->sezonId]]=$this->tRoute[$this->sezonId].'.html';
                    if($this->shipId) $this->breadcrumbs['Шипованные']=$this->tRoute['21'].'.html';
                    elseif($this->shipId===0) $this->breadcrumbs['Нешипованные']=$this->tRoute['20'].'.html';
                    $this->breadcrumbs[]=$this->bname;

                    $this->description="Самый большой выбор {$this->sezonNames6[$this->sezonId]} ".($this->shipId?'шипованных ':($this->shipId===0?'нешипованных ':''))."шин {$this->bname} в интернет магазине Dilijans. В нашем каталоге {$this->sezonNames6[$this->sezonId]} ".($this->shipId?'шипованных ':($this->shipId===0?'':''))."шин {$this->balt} Вы найдете лучшую {$this->sezonNames8[$this->sezonId]} резину ".($this->shipId?'на шипах ':($this->shipId===0?'':''))."для своего автомобиля. {$this->sezonNames5[$this->sezonId]} шины ".($this->shipId?'с шипами ':($this->shipId===0?' ':''))." шины {$this->balt} по привлекательным ценам. Мы доставим Ваши шины {$this->bname} в любую точку Москвы или любой город России.";
                }


                $this->backUrl='javascript:goBack();';
        }



        $this->_sidebar();

        if(!$this->_filter()) return;

        if($this->sezonId==2 && $this->shipId==='') $this->_modelsSplitByShip();
        elseif($this->sezonId==0 && $this->atId==0) $this->_modelsSplitBySezon();
        else $this->_models();

    }

    // список моделей
    private function _models()
    {
        $this->doubleDimension=false;

        $this->mLimit=(int)abs(Data::get('t_models_per_page'));
        $page=(int)abs(@Url::$sq['page']);
        if(!$page) $page=1;

        $r=array(
            'gr'=>1,
            'brand_id'=>$this->brand_id,
            'start'=>abs(($page-1)*$this->mLimit),
            'lines'=>$this->mLimit,
            'qSelect'=>array(
                'scDiv'=>array()
            ),
            'whereCat'=>array($this->minQtyRadiusSQL),
            'order'=>"scDiv DESC, cc_model.name ASC"
        );
        if(!empty($this->radius)) $r['whereCat'][]="cc_cat.P1 = '{$this->radius}'";
        if(!empty($this->sezonId)) $r['P1']=$this->sezonId;
        if($this->shipId!=='') $r['P3']=$this->shipId;
        if(!empty($this->atId)) {
            if($this->atId==1) $r['P2']=array('list'=>array(1,4));
            else if($this->atId==2) $r['P2']=array('list'=>array(2,4));
                else $r['P2']=3;
        }

        if(empty($this->sezonId)){
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
                $r['order']='scDiv DESC, sezOrd, cc_model.name ASC';
        }

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

        $burl='/'.App_Route::_getUrl('tModel').'/';

        foreach($d as $v){
            // *****************************************************************
            $v['catalog_items'] = array('gt0'=>array(), 0=>array());
            $this->cc->cat_view(array(
                'model_id' => $v['model_id'],
                'gr'=>1,
                'scDiv'=>1,
                'nolimits'=>true,
                'order'=>'scDiv DESC, cc_cat.P1, cc_cat.P3,cc_cat.P2'
            ));
            $desc=$this->cc->fetchAll();
            $v['item_rads']=array();    
            $prices = Array();
            foreach($desc as $item)
            {
                if(empty($this->diametr) || $this->diametr==$item['P5']) 
                {
                    if($item['sc'] && $item['cprice'] > 0) 
                    {
                        $prices[] = $item['cprice'];
                    }
                }
                if($item['sc']) 
                {
                    $v['item_rads'][$item['P1']] = ($this->diametr == $item['P1']) ? true : false;
                }
            }
            // *****************************************************************
            $malt=Tools::unesc($v['alt']!=''?$v['alt']:$v['name']);
            $malt1=$this->firstS($malt);
            $mname=Tools::unesc($v['name']);
            $v['suffix']=Tools::unesc($v['suffix']);
            $vi=array(
                'anc'=>"{$this->bname} {$mname} {$v['suffix']}",
                'alt'=>"резина {$this->balt} {$malt1}",
                'url'=>$burl.$v['sname'].'.html',
                'img'=>$this->cc->makeImgPath($v['img1']),
                'scDiv'=>$v['scDiv'],
                'sez'=>$v['P1'],
                'ship'=>$v['P3'],
                'spezId'=>$v['mspez_id']==1?true:false,
                'video_link' => $v['video_link']
            );
            if($vi['img']=='') $vi['img']=$this->noimg1;
            //****
            $vi['prices'] = $prices;
            $vi['radiuses'] = $v['item_rads'];
            $vi['reviews']  = $this->_reviewsList([
                'modelId'=> $v['model_id']
            ]);
            //****
            $this->models[]=$vi;
        }

        return true;
    }

    private function _modelsSplitBySezon()
    {
        $this->doubleDimension=true;
        $sezOrder=Data::get('ccSezonOrder');
        if($sezOrder==1){
            // сначала зиму
            $this->models=array(2=>array(),3=>array(),1=>array());
        }elseif($sezOrder==2){
            // сначала лето
            $this->models=array(1=>array(),3=>array(),2=>array());
        }else {
            $sezOrder=0;
            $this->models=array(1=>array(),2=>array(),3=>array());
        }

        if(empty($this->atId)){
            $this->h2=array(
                1=>array(
                    'img'=>"/app/images/title-sez1.png",
                    'title'=>'Летняя резина '.$this->bname,
                    'url'=>$this->tRoute[1].'/'.$this->brand_sname.'.html'
                ),
                2=>array(
                    'img'=>"/app/images/title-sez2.png",
                    'title'=>'Зимняя резина '.$this->bname,
                    'url'=>$this->tRoute[2].'/'.$this->brand_sname.'.html'
                ),
                3=>array(
                    'img'=>"/app/images/title-sez3.png",
                    'title'=>'Всесезонная резина '.$this->bname,
                    'url'=>$this->tRoute[3].'/'.$this->brand_sname.'.html'
                )
            );
        }else{
            $this->h2=array(
                1=>array(
                    'img'=>"/app/images/title-sez1.png",
                    'title'=>'Летняя резина '.$this->bname,
                    'url'=>''
                ),
                2=>array(
                    'img'=>"/app/images/title-sez2.png",
                    'title'=>'Зимняя резина'.$this->bname,
                    'url'=>''
                ),
                3=>array(
                    'img'=>"/app/images/title-sez3.png",
                    'title'=>'Всесезонная резина'.$this->bname,
                    'url'=>''
                )
            );

        }
        $this->num=0;

        for($i=1;$i<=3;$i++){
            $r=array(
                'gr'=>1,
                'brand_id'=>$this->brand_id,
                'qSelect'=>array(
                    'scDiv'=>array()
                ),
                'whereCat'=>array($this->minQtyRadiusSQL),
                'order'=>"scDiv DESC, cc_model.name ASC"
            );
            if(!empty($this->radius)) $r['whereCat'][]="cc_cat.P1 = '{$this->radius}'";
            $r['P1']=$i;
            if(!empty($this->atId)) {
                if($this->atId==1) $r['P2']=array('list'=>array(1,4));
                else if($this->atId==2) $r['P2']=array('list'=>array(2,4));
                    else $r['P2']=3;
            }


            $this->num+=$this->cc->models($r);

            $d=$this->cc->fetchAll('', MYSQLI_ASSOC);

            $burl='/'.App_Route::_getUrl('tModel').'/';

            foreach($d as $v){
                // *****************************************************************
                $v['catalog_items'] = array('gt0'=>array(), 0=>array());
                $this->cc->cat_view(array(
                    'model_id' => $v['model_id'],
                    'gr'=>1,
                    'scDiv'=>1,
                    'nolimits'=>true,
                    'order'=>'scDiv DESC, cc_cat.P1, cc_cat.P3,cc_cat.P2'
                ));
                $desc=$this->cc->fetchAll();
                $v['item_rads']=array();    
                $prices = Array();
                foreach($desc as $item)
                {
                    if(empty($this->diametr) || $this->diametr==$item['P5']) 
                    {
                        if($item['sc'] && $item['cprice'] > 0) 
                        {
                            $prices[] = $item['cprice'];
                        }
                    }
                    if($item['sc']) 
                    {
                        $v['item_rads'][$item['P1']] = ($this->diametr == $item['P1']) ? true : false;
                    }
                }
                // *****************************************************************
                $malt=Tools::unesc($v['alt']!=''?$v['alt']:$v['name']);
                $malt1=$this->firstS($malt);
                $mname=Tools::unesc($v['name']);
                $v['suffix']=Tools::unesc($v['suffix']);
                $vi=array(
                    'anc'=>"{$this->bname} {$mname} {$v['suffix']}",
                    'alt'=>"резина {$this->balt} {$malt1}",
                    'url'=>$burl.$v['sname'].'.html',
                    'img'=>$this->cc->makeImgPath($v['img1']),
                    'scDiv'=>$v['scDiv'],
                    'sez'=>$v['P1'],
                    'ship'=>$v['P3'],
                    'spezId'=>$v['mspez_id']==1?true:false,
                    'video_link' => $v['video_link']
                );
                if($vi['img']=='') $vi['img']=$this->noimg1;
                //****
                $vi['prices'] = $prices;
                $vi['radiuses'] = $v['item_rads'];
                $vi['reviews']  = $this->_reviewsList([
                    'modelId'=> $v['model_id']
                ]);
                //****
                $this->models[$i][]=$vi;
            }

        }


        if(!$this->num) {
            $this->bottomTextTitle=$this->topText=$this->bottomText='';
            $this->noResults=$this->parse($this->ss->getDoc('t_models_nr_sub$6'));
            return false;
        }

        return true;

    }

    private function _modelsSplitByShip()
    {
        $this->doubleDimension=true;
        $sezOrder=Data::get('ccSezonOrder');
        $this->models=array(0=>array(),1=>array());

        $this->h2=array(
            0=>array(
                'img'=>"/app/images/img-nav-filter-05.png",
                'title'=>'Зимняя нешипованая резина (липучка) '.$this->bname,
                'url'=>$this->tRoute['20'].'/'.$this->brand_sname.'.html'
            ),
            1=>array(
                'img'=>"/app/images/title-ship.png",
                'title'=>'Зимняя шипованая резина '.$this->bname,
                'url'=>$this->tRoute['21'].'/'.$this->brand_sname.'.html'
            )
        );
        $this->num=0;

        for($i=0;$i<=1;$i++){
            $r=array(
                'gr'=>1,
                'brand_id'=>$this->brand_id,
                'qSelect'=>array(
                    'scDiv'=>array()
                ),
                'whereCat'=>array($this->minQtyRadiusSQL),
                'order'=>"scDiv DESC, cc_model.name ASC"
            );
            if(!empty($this->radius)) $r['whereCat'][]="cc_cat.P1 = '{$this->radius}'";
            $r['P3']=$i;
            $r['P1']=2;
            if(!empty($this->atId)) {
                if($this->atId==1) $r['P2']=array('list'=>array(1,4));
                else if($this->atId==2) $r['P2']=array('list'=>array(2,4));
                    else $r['P2']=3;
            }

            $this->num+=$this->cc->models($r);

            $d=$this->cc->fetchAll('', MYSQLI_ASSOC);

            $burl='/'.App_Route::_getUrl('tModel').'/';

            foreach($d as $v){
                // *****************************************************************
                $v['catalog_items'] = array('gt0'=>array(), 0=>array());
                $this->cc->cat_view(array(
                    'model_id' => $v['model_id'],
                    'gr'=>1,
                    'scDiv'=>1,
                    'nolimits'=>true,
                    'order'=>'scDiv DESC, cc_cat.P1, cc_cat.P3,cc_cat.P2'
                ));
                $desc=$this->cc->fetchAll();
                $v['item_rads']=array();    
                $prices = Array();
                foreach($desc as $item)
                {
                    if(empty($this->diametr) || $this->diametr==$item['P5']) 
                    {
                        if($item['sc'] && $item['cprice'] > 0) 
                        {
                            $prices[] = $item['cprice'];
                        }
                    }
                    if($item['sc']) 
                    {
                        $v['item_rads'][$item['P1']] = ($this->diametr == $item['P1']) ? true : false;
                    }
                }
                // *****************************************************************
                $malt=Tools::unesc($v['alt']!=''?$v['alt']:$v['name']);
                $malt1=$this->firstS($malt);
                $mname=Tools::unesc($v['name']);
                $v['suffix']=Tools::unesc($v['suffix']);
                $vi=array(
                    'anc'=>"{$this->bname} {$mname} {$v['suffix']}",
                    'alt'=>"резина {$this->balt} {$malt1}",
                    'url'=>$burl.$v['sname'].'.html',
                    'img'=>$this->cc->makeImgPath($v['img1']),
                    'scDiv'=>$v['scDiv'],
                    'sez'=>$v['P1'],
                    'ship'=>$v['P3'],
                    'spezId'=>$v['mspez_id']==1?true:false,
                    'video_link' => $v['video_link']
                );
                if($vi['img']=='') $vi['img']=$this->noimg1;
                //****
                $vi['prices'] = $prices;
                $vi['radiuses'] = $v['item_rads'];
                $vi['reviews']  = $this->_reviewsList([
                    'modelId'=> $v['model_id']
                ]);
                //****
                $this->models[$i][]=$vi;
            }

        }


        if(!$this->num) {
            $this->bottomTextTitle=$this->topText=$this->bottomText='';
            $this->noResults=$this->parse($this->ss->getDoc('t_models_nr_sub$6'));
            return false;
        }

        return true;


    }


    public function _sidebar()
    {
        // быстрые бренды
        $burl='/'.App_Route::_getUrl('tCat').'/';
        $this->qbrands=array(0=>array());
        $r=$this->cc->brands(array(
            'gr'=>1,
            'whereCat'=>$this->minQtyRadiusSQL,
            'qSelect'=>array(
                'modelsNum'=>array()
            ),
            'select'=>array(
                'cc_brand.name'=>'name',
                'cc_brand.sname'=>'sname'
            )
        ));
        if($r){
            $this->qbrands=array();
            while($this->cc->next()!==false){
                $this->qbrands[0][]=array(
                    'name'=>Tools::unesc($this->cc->qrow['name']),
                    'sname'=>$burl.$this->cc->qrow['sname'].'.html'
                );
            }
        }

        // модели в сайдбаре. bname должно быть определено выше
        $r=array(
            'gr'=>1,
            'brand_id'=>$this->brand_id,
            'nolimits'=>1,
            'qSelect'=>array(
                'scDiv'=>array('where'=>$this->minQtyRadiusSQL)
            ),
            'order'=>"cc_model.P1, cc_model.name"
        );

        $this->cc->models($r);
        $d=$this->cc->fetchAll('', MYSQLI_ASSOC);
        $this->qmodels=array();
        $burl='/'.App_Route::_getUrl('tModel').'/';
        foreach($d as $v){
            $mname=Tools::unesc($v['name']);
            $v['suffix']=Tools::unesc($v['suffix']);
            $vi=array(
                'anc'=>"{$this->bname} {$mname} {$v['suffix']}",
                'url'=>$burl.$v['sname'].'.html',
                'scDiv'=>$v['scDiv']
            );
            $this->qmodels[$v['P1']][]=$vi;
        }

        // активная вкладка в списке моделей
        $this->qmodels['active']=@App_Route::$param['M1'];


    }

    public function _filter()
    {
        //фильтр
        $r=array(
            'gr'=>1,
            'brand_id'=>$this->brand_id,
            'where'=>array($this->minQtyRadiusSQL),
            'fields'=>"cc_model.model_id, cc_cat.P1+'0' AS P1, cc_cat.P2+'0' AS P2, cc_cat.P3+'0' AS P3 , cc_model.P1 AS MP1, cc_model.P2 AS MP2, cc_model.P3 AS MP3",
            'ex'=>1,
            'nolimits'=>1,
            'exFields'=>array('P1'=>array(),'P2'=>array(),'P3'=>array(),'MP1'=>array(),'MP2'=>array(),'MP3'=>array())
        );

        if(!empty($this->radius)) $r['P1']=$this->radius;
        if(!empty($this->sezonId)) $r['M1']=$this->sezonId;
        if($this->shipId!=='') $r['M3']=$this->shipId;
        if(!empty($this->atId)) {
            if($this->atId==1) $r['M2']=array('list'=>array(1,4));
            else if($this->atId==2) $r['M2']=array('list'=>array(2,4));
                else $r['M2']=3;
        }
        $this->exnum=$this->cc->cat($r);

        ksort($this->cc->ex_arr['P1']);
        ksort($this->cc->ex_arr['P2']);
        ksort($this->cc->ex_arr['P3']);
        ksort($this->cc->ex_arr['MP1']);
        ksort($this->cc->ex_arr['MP2']);
        ksort($this->cc->ex_arr['MP3']);

        $this->ex=$this->cc->ex_arr;
        unset($this->ex['P1'][0],$this->ex['P2'][0],$this->ex['P3'][0], $this->ex['MP1'][0], $this->ex['MP2'][0], $this->cc->ex_arr);

        $this->lf=array();
        $this->lfi=0;
        $this->lfh=array();

        $this->filter=array(
            'P1'=>array_keys($this->ex['P1']),
            'P2'=>array_keys($this->ex['P2']),
            'P3'=>array_keys($this->ex['P3'])
        );

        if(count(@$this->ex['MP1'])>1){
            $this->lfi++;
            ksort($this->ex['MP1']);
            $this->lf['_mp1']=array();
            $si=App_Route::_getUrl('tSearch').'?';
            if(!empty($this->ex['MP1'][2]))
                $this->lf['_mp1'][2]=array(
                    'chk'=>false,
                    'anc'=>@$this->sezonNames5[2].$this->sezonIcos[2],
                    'id'=>'_mp12',
                    'url'=>$si.'mp1=2'
                );
            if(!empty($this->ex['MP1'][1]))
                $this->lf['_mp1'][1]=array(
                    'chk'=>false,
                    'anc'=>@$this->sezonNames5[1].$this->sezonIcos[1],
                    'id'=>'_mp11',
                    'url'=>$si.'mp1=1'
                );
            if(!empty($this->ex['MP1'][3]))
                $this->lf['_mp1'][3]=array(
                    'chk'=>false,
                    'anc'=>@$this->sezonNames5[3].$this->sezonIcos[3],
                    'id'=>'_mp13',
                    'url'=>$si.'mp1=3'
                );
        }
        if(count(@$this->ex['MP3'])>1){
            $this->lfi++;
            ksort($this->ex['MP3']);
            $this->lf['_mp3']=array();
            $si=App_Route::_getUrl('tSearch').'?';
            if(!empty($this->ex['MP3'][0]))
                $this->lf['_mp3'][0]=array(
                    'chk'=>false,
                    'anc'=>"Нешипованные",
                    'id'=>'_mp30',
                    'url'=>$si.'mp3=0'
                );
            if(!empty($this->ex['MP3'][1]))
                $this->lf['_mp3'][1]=array(
                    'chk'=>false,
                    'anc'=>'Шипованные <img src="/app/images/ship.png" alt="шипованные шины">',
                    'id'=>'_mp31',
                    'url'=>$si.'mp3=1'
                );
        }
        /*
        if(!empty($this->ex['MP2'][4]) && (!empty($this->ex['MP2'][1]) || !empty($this->ex['MP2'][2]))) {
        $this->ex['MP2'][1]=1;
        $this->ex['MP2'][2]=1;
        }
        unset($this->ex['MP2'][4]);
        if(count(@$this->ex['MP2'])>1){
        $this->lfi++;
        ksort($this->ex['MP2']);
        $this->lf['_at']=array();
        $si=App_Route::_getUrl('tSearch').'?';
        foreach($this->ex['MP2'] as $k=>$v) if($k>0) {
        $this->lf['_at'][$k]=array(
        'chk'=>false,
        'anc'=>@$this->atNames3[$k]." <span class=\"atype-ico$k\"></span>",
        'id'=>'_at'.$k,
        'url'=>$si.'at='.$k
        );
        }
        }
        */
        $this->lfh['vendor']=$this->brand_sname;
        if(!empty($this->radius)) $this->lfh['p1']=$this->radius;
        if(!empty($this->sezonId)) $this->lfh['mp1']=$this->sezonId;
        if(!empty($this->atId)) $this->lfh['at']=$this->atId;
        if($this->shipId!=='') $this->lfh['mp3']=$this->shipId;


        return true;

    }

    public function _filter_bak()
    {
        // уточняющий фильтр
        $r=array(
            'gr'=>1,
            'brand_id'=>$this->brand_id,
            'nolimits'=>true,
            'ex'=>1,
            'fields'=>array('cc_model.model_id'=>'model_id',"cc_model.P1"=>'MP1',"cc_cat.P1+'0'"=>'P1','cc_model.P2'=>'MP2'),
            'exFields'=>array('MP1'=>array(),'P1'=>array(),'MP2'=>array())
        );

        $this->exnum=$this->cc->cat($r);

        if(!$this->exnum) {
            $this->bottomTextTitle=$this->topText=$this->bottomText='';
            $this->noResults=$this->parse($this->ss->getDoc('t_models_nr$6'));
            return false;
        }

        unset($this->cc->ex_arr['MP1'][0],$this->cc->ex_arr['P1'][0],$this->cc->ex_arr['MP2'][0]);


        if(!empty($this->sezonId) || !empty($this->atId)){
            $this->cc->ex_arr['P1']=array();
            while($this->cc->next()!==false){
                if(($this->sezonId==$this->cc->qrow['MP1'] || !$this->sezonId) && ($this->atId==$this->cc->qrow['MP2'] || !$this->atId) && $this->cc->qrow['P1']>0)
                    $this->cc->ex_arr['P1'][$this->cc->qrow['P1']]=1;
            }
        }

        ksort($this->cc->ex_arr['P1']);
        ksort($this->cc->ex_arr['MP1']);
        ksort($this->cc->ex_arr['MP2']);

        $this->filter=array();


        if(count($this->cc->ex_arr['MP1'])>1){
            $this->filter['sezon']=array();

            $this->filter['sezon'][0]=array(
                'anc'=>'Все',
                'ico'=>'',
                'active'=>false
            );
            switch($this->atId){
                case 1: $this->filter['sezon'][0]['url']='/'.App_Route::_getUrl('tLight'); break;
                case 2: $this->filter['sezon'][0]['url']='/'.App_Route::_getUrl('tSUV'); break;
                case 3: $this->filter['sezon'][0]['url']='/'.App_Route::_getUrl('tStrong'); break;
                default: $this->filter['sezon'][0]['url']='/'.App_Route::_getUrl('tCat'); break;
            }
            $this->filter['sezon'][0]['url'].='/'.$this->brand_sname.'.html';

            $p=array(
                1=>'tSummer',
                2=>'tWinter',
                3=>'tAllW'
            );
            foreach($this->cc->ex_arr['MP1'] as $k=>$v){
                $this->filter['sezon'][$k]=array(
                    'anc'=>$this->sezonNames5[$k],
                    'active'=>false
                );
                switch($k){
                    case 1:
                        $this->filter['sezon'][$k]['ico']='/app/images/sun.png';
                        break;
                    case 2:
                        $this->filter['sezon'][$k]['ico']='/app/images/snow.png';
                        break;
                    case 3:
                    $this->filter['sezon'][$k]['ico']='/app/images/sunsnow.png';
                    break;
                }
                switch($this->atId){
                    case 1: $this->filter['sezon'][$k]['url']='/'.App_Route::_getUrl($p[$k].'Light'); break;
                    case 2: $this->filter['sezon'][$k]['url']='/'.App_Route::_getUrl($p[$k].'SUV'); break;
                    case 3: $this->filter['sezon'][$k]['url']='/'.App_Route::_getUrl($p[$k].'Strong'); break;
                    default: $this->filter['sezon'][$k]['url']=$this->tRoute[$k]; break;
                }
                $this->filter['sezon'][$k]['url'].='/'.$this->brand_sname.'.html';
            }
            if(empty($this->sezonId)) $this->filter['sezon'][0]['active']=true; else $this->filter['sezon'][$this->sezonId]['active']=true;

        }

        if(count($this->cc->ex_arr['MP2'])>1){
            $this->filter['at']=array();

            $this->filter['at'][0]=array(
                'anc'=>'Все',
                'ico'=>'',
                'ico_active'=>'',
                'active'=>false
            );
            switch($this->sezonId){
                case 1: $this->filter['at'][0]['url']='/'.App_Route::_getUrl('tSummer'); break;
                case 2: $this->filter['at'][0]['url']='/'.App_Route::_getUrl('tWinter'); break;
                case 3: $this->filter['at'][0]['url']='/'.App_Route::_getUrl('tAllW'); break;
                default: $this->filter['at'][0]['url']='/'.App_Route::_getUrl('tCat'); break;
            }
            $this->filter['at'][0]['url'].='/'.$this->brand_sname.'.html';

            if(isset($this->cc->ex_arr['MP2'][4])){
                $this->cc->ex_arr['MP2'][1]=1;
                $this->cc->ex_arr['MP2'][2]=1;
                unset($this->cc->ex_arr['MP2'][4]);
            }
            foreach($this->cc->ex_arr['MP2'] as $k=>$v){
                $this->filter['at'][$k]=array(
                    'active'=>false
                );
                $p=array(
                    0=>'',
                    1=>'Summer',
                    2=>'Winter',
                    3=>'AllW'
                );
                switch($k){
                    case 1:
                        $this->filter['at'][$k]['ico']='/app/images/icon-car-light.png';
                        $this->filter['at'][$k]['ico_active']='/app/images/icon-car-light-active.png';
                        $this->filter['at'][$k]['anc']='Легковые';
                        $this->filter['at'][$k]['url']='/'.App_Route::_getUrl('t'.$p[(int)$this->sezonId].'Light').'/'.$this->brand_sname.'.html';
                        break;
                    case 2:
                        $this->filter['at'][$k]['ico']='/app/images/icon-car-vnedor.png';
                        $this->filter['at'][$k]['ico_active']='/app/images/icon-car-vnedor-active.png';
                        $this->filter['at'][$k]['anc']='Внедорожные';
                        $this->filter['at'][$k]['url']='/'.App_Route::_getUrl('t'.$p[(int)$this->sezonId].'SUV').'/'.$this->brand_sname.'.html';
                        break;
                    case 3:
                    case 4: //TODO убрать из базы 4-й тип или дополнить здесь его
                        $this->filter['at'][3]['ico']='/app/images/icon-car-gruz.png';
                        $this->filter['at'][3]['ico_active']='/app/images/icon-car-gruz-active.png';
                        $this->filter['at'][3]['anc']='Усиленные';
                        $this->filter['at'][3]['url']='/'.App_Route::_getUrl('t'.$p[(int)$this->sezonId].'Strong').'/'.$this->brand_sname.'.html';
                        break;
                }
            }
            if(empty($this->atId)) $this->filter['at'][0]['active']=true; else $this->filter['at'][$this->atId]['active']=true;
        }

        if(count($this->cc->ex_arr['P1'])>1){
            $this->filter['rads']=array();
            $this->filter['rads'][0]=array(
                'url'=>'/'.App_Route::_getUrl('tCat').'/'.$this->brand_sname.'.html',
                'anc'=>'Все',
                'active'=>false
            );
            $this->filter['rads'][0]['url']=$this->tSezAtRoute[(int)$this->sezonId.(int)$this->atId].'/'.$this->brand_sname.'.html';
            foreach($this->cc->ex_arr['P1'] as $k=>$v){
                $this->filter['rads'][$k]=array(
                    'anc'=>"R{$k}",
                    'active'=>false
                );
                $this->filter['rads'][$k]['url']=$this->tSezAtRoute[(int)$this->sezonId.(int)$this->atId].'/'.$this->brand_sname.'/R'.$k.'.html';
            }
            if(empty($this->radius)) $this->filter['rads'][0]['active']=true; else $this->filter['rads'][$this->radius]['active']=true;
        }

        return true;

    }
}