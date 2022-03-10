<?
class App_Catalog_Tyres_Model_Controller extends App_Catalog_Tyres_Common_Controller {

    //бренды дисков
    public function index() {

        $this->view('catalog/tyres/model');
        if(empty($this->model_id)) $this->cc->que('model_by_sname',Url::$spath[2],1,'',1); else $this->cc->que('model_by_id',$this->model_id);
        //$this->cc->que('model_by_sname',Url::$spath[2],1,'',1);
        if(!$this->cc->qnum()) return App_Route::redir404();
        $this->cc->next();

        $this->brand_sname=$this->cc->qrow['brand_sname'];
        $this->bname=Tools::unesc($this->cc->qrow['bname']);
        $this->mname=Tools::unesc($this->cc->qrow['name']);
        $this->balt=Tools::unesc($this->cc->qrow['balt']!=''?$this->cc->qrow['balt']:$this->cc->qrow['bname']);
        $this->malt=Tools::unesc($this->cc->qrow['alt']!=''?$this->cc->qrow['alt']:$this->cc->qrow['name']);
        $this->balt1=$this->firstS($this->balt);
        $this->baltOther=$this->otherS($this->balt);
        $this->bimg1=$this->cc->make_img_path($this->cc->qrow['brand_img1']);
        $this->bAnc="шины {$this->bname}";
        $this->burl= $this->tRoute[0].'/'.$this->brand_sname.'.html';
        $this->malt1=$this->firstS($this->malt);
        $this->maltOther=$this->otherS($this->malt);
        $this->brand_id=$this->cc->qrow['brand_id'];
        $this->model_id=$this->cc->qrow['model_id'];
        $this->img1=($this->cc->make_img_path($this->cc->qrow['img1'])).'?v='.ExLib::loadImagesId();
        $this->img2=($this->cc->make_img_path($this->cc->qrow['img2'])).'?v='.ExLib::loadImagesId();
        $this->msuffix=Tools::unesc($this->cc->qrow['suffix']);
        if($this->cc->qrow['mspez_id']==1) $this->new=true; else $this->new=false;
        $this->sezonId=$this->cc->qrow['P1'];
        $this->sezon=@$this->sezonNames4[$this->sezonId];
        $this->shipId=$this->cc->qrow['P3'];
        $this->atId=$this->cc->qrow['P2'];
        $this->sezTitle=$this->sezonNames[$this->sezonId];
        $this->diametr=(int)@$_GET['diametr'];
        $this->modelUrl=App_SUrl::tModel(0,$this->cc->qrow);
        $this->mspezId=$this->cc->qrow['mspez_id'];
        $this->classId=$this->cc->qrow['class_id'];
        $this->video_link=$this->cc->qrow['video_link'];

        $this->imgAlt="Фото шины {$this->balt1} {$this->malt1} {$this->msuffix}";
        $this->imgTitle="автошины {$this->bname} {$this->mname}  {$this->msuffix}";

        $this->shipIco='';

        switch($this->sezonId){
            case 1:
                $this->sezIco='<img src="/app/images/sun.png" title="Летние шины" class="nttip">';
                break;
            case 2:
                $this->sezIco='<img src="/app/images/snow.png" title="Зимние шины" class="nttip">';
                if($this->shipId) {
                    $this->shipIco='<img src="/app/images/ship.png" title="Шипованные шины" class="nttip">';
                }
                break;
            case 3:
                $this->sezIco='<img src="/app/images/sunsnow.png" title="Всесезонные шины" class="nttip">';
                break;
        }

        $this->bcatUrl='/'.App_Route::_getUrl('tCat').'/'.$this->brand_sname.'.html';

        switch($this->sezonId) {
            case 1:
                $this->title="Шины {$this->bname} {$this->mname} {$this->msuffix}. Купить {$this->balt} {$this->malt}, цена, фото, доставка";
                $this->_title="Летняя шина {$this->bname} {$this->mname} {$this->msuffix}";
                $this->breadcrumbs['летние шины']=array('/'.App_Route::_getUrl('tSummer').'.html','летняя резина');
                $this->breadcrumbs["летние шины {$this->balt1}"]=array('/'.App_Route::_getUrl('tSummer').'/'.$this->brand_sname.'.html',"летняя резина {$this->balt}");
                $this->breadcrumbs[]=$this->balt1.' '.$this->malt1;
                $this->description="Цены на летние авто шины {$this->bname} {$this->mname}. Актуальное наличие, фото резины {$this->balt1} {$this->malt}";
                $this->keywords="{$this->bname} {$this->mname}, купить летние шины {$this->balt1} {$this->malt}";
                break;
            case 2:
                $this->title="Шины {$this->bname} {$this->mname} {$this->msuffix}. Купить {$this->balt} {$this->malt}, цена, фото, доставка";
                $this->_title="Зимняя шина {$this->bname} {$this->mname} {$this->msuffix}";
                $this->breadcrumbs['зимние шины']=array('/'.App_Route::_getUrl('tWinter').'.html','зимняя резина');
                $this->breadcrumbs["зимние шины {$this->balt1}"]=array('/'.App_Route::_getUrl('tWinter').'/'.$this->brand_sname.'.html',"зимняя резина {$this->balt}");
                $this->breadcrumbs[]=$this->balt1.' '.$this->malt1;
                $this->description="Цены на зимние авто шины {$this->bname} {$this->mname}. Актуальное наличие, фото резины {$this->balt1} {$this->malt}";
                $this->keywords="{$this->bname} {$this->mname}, купить зимние шины {$this->balt1} {$this->malt}";
                break;
            case 3:
                $this->title="Шины {$this->bname} {$this->mname} {$this->msuffix}. Купить {$this->balt} {$this->malt}, цена, фото, доставка";
                $this->_title="Всесезонная шина {$this->bname} {$this->mname} {$this->msuffix}";
                $this->breadcrumbs['всесезонные шины']=array('/'.App_Route::_getUrl('tAllW').'.html','всесезонная резина');
                $this->breadcrumbs["всесезонные шины {$this->balt1}"]=array('/'.App_Route::_getUrl('tAllW').'/'.$this->brand_sname.'.html',"всесезонная резина {$this->balt}");
                $this->breadcrumbs[]=$this->balt1.' '.$this->malt1;
                $this->description="Цены на всесезонные авто шины {$this->bname} {$this->mname}. Актуальное наличие, фото резины {$this->balt1} {$this->malt}";
                $this->keywords="{$this->bname} {$this->mname}, купить всесезонные шины {$this->balt1} {$this->malt}";
                break;
            default:
                $this->title="Шины {$this->bname} {$this->mname} {$this->msuffix}. Купить {$this->balt} {$this->malt}, цена, фото, доставка";
                $this->_title="{$this->bname} {$this->mname} {$this->msuffix}";
                $this->breadcrumbs['шины']=array('/'.App_Route::_getUrl('tCat').'.html','резина');
                $this->breadcrumbs["шины {$this->balt1}"]=array('/'.App_Route::_getUrl('tCat').'/'.$this->brand_sname.'.html',"резина {$this->balt}");
                $this->breadcrumbs[]=$this->balt1.' '.$this->malt1;
                $this->description="Цены на шины {$this->bname} {$this->mname}. Актуальное наличие, фото резины {$this->balt1} {$this->malt}";
                $this->keywords="{$this->bname} {$this->mname}, купить шины {$this->balt1} {$this->malt}";
                break;
        }

//

        if (!empty($this->cc->qrow['is_seo']))
            $this->is_seo = $this->cc->qrow['is_seo'];

        if (!empty($this->cc->qrow['seo_title']))
            $this->title = $this->cc->qrow['seo_title'];
        if (!empty($this->cc->qrow['seo_h1']))
            $this->_title = $this->cc->qrow['seo_h1'];
        if (!empty($this->cc->qrow['seo_keywords']))
            $this->keywords = $this->cc->qrow['seo_keywords'];
        if (!empty($this->cc->qrow['seo_description']))
            $this->description = $this->cc->qrow['seo_description'];
//

        $this->mtext=$this->ss->parseText(Tools::unesc($this->cc->qrow['text']));
        if(trim(Tools::stripTags($this->mtext))!=''){
                $this->mtext=$this->ss->splitText($this->mtext,2);
        }else{
            $this->mtext=$this->ss->splitText($this->parse($this->ss->getDoc('tpl_tmodel$10')),2);
        }
        if (count($this->mtext)==1) {
            $this->mtext = $this->mtext[0];
        }

        // галерея
        $gl=new CC_Gallery();
        $this->gallery = Tools::sortImagesBySize($gl->glist($this->model_id), 185);

        // размеры
        $this->rads=array();
        $this->cat=array('gt0'=>array(), 0=>array());
        $this->num=$this->cc->cat_view(array(
            'model_id'=>$this->model_id,
            'gr'=>1,
            'scDiv'=>1,
            /*'where'=>array($this->minQtyRadiusSQL),   Скроет блок "Позиции отсутствующие на складе"*/
            'nolimits'=>true,
            'order'=>'scDiv DESC, cc_cat.P1, cc_cat.P3,cc_cat.P2'
        ));
        $d=$this->cc->fetchAll();
        $burl='/'.App_Route::_getUrl('tTipo').'/';
        $_altt='/'.App_Route::_getUrl('tSearch').'.html?';
        foreach($d as $v){
            if(empty($this->diametr) || $this->diametr==$v['P1']) {
                $vi=array(
                    'video_link'=>  $v['video_link'],
                    'url'=>			$burl.$v['cat_sname'].'.html',
                    'suffixUrl'=>    $this->dict_url($this->cc->dict_search_key($v['csuffix'],$v['gr'],$this->brand_id)),
                    'anc'=>	        Tools::unesc($v['bname'].' '.$v['mname']),
                    'title'=>	    "резина ".Tools::html($v['bname'].' '.$v['mname']." {$v['P3']}/{$v['P2']} R{$v['P1']}".($v['csuffix']!=''?" {$v['csuffix']}":'')),
                    'qtyText'=>		$v['sc']>12?"&gt;&nbsp;12&nbsp;шт":(!$v['sc']?'-':"{$v['sc']}&nbsp;шт"),
                    'maxQty'=>		$v['sc'],
                    'defQty'=>		$v['sc']>4 || $v['sc']==0?4:$v['sc'],
                    'priceText'=>	$v['cprice']?(Tools::nn($v['cprice'])." р"):'звоните',
                    'cprice' =>    $v['cprice']?(Tools::nn($v['cprice'])):'0',
                    'cat_id'=>		$v['cat_id'],
                    'razmer'=>		"{$v['P3']}/{$v['P2']}&nbsp;R{$v['P1']}",
                    'INIS'=>        "{$v['P7']}",
                    'shipIco'=>     '',
                    'sezIco'=>      '',
                    'inisUrl'=>     $v['P7']!=''?('<a href="#" rel="/ax/explain/inis?v='.$v['P7'].'" title="Что означает '.$v['P7'].'?" class="atip gr">'.$v['P7'].'</a>'):'',
                    // http://www.dilijans.org/t_filter.html?p3=235&p2=60&p1=18&mp1=2
                    'altt'=>        urlencode($_altt."p3={$v['P3']}&p2={$v['P2']}&p1={$v['P1']}&mp1={$v['MP1']}")
                );
                switch($v['MP1']){
                    case 1:
                        $vi['sezIco']='<img src="/app/images/sun.png" title="Летние шины" class="nttip">';
                        break;
                    case 2:
                        $vi['sezIco']='<img src="/app/images/snow.png" title="Зимние шины" class="nttip">';
                        if($v['MP3']) {
                            $vi['shipIco']='<img src="/app/images/ship.png" title="Шипованные шины" class="nttip">';
                        }
                        break;
                    case 3:
                        $vi['sezIco']='<img src="/app/images/sunsnow.png" title="Всесезонные шины" class="nttip">';
                        break;
                }
                if(($v['P1'] >= $this->minQtyRadius && $v['sc'] >= 2) || ($v['P1'] < $this->minQtyRadius && $v['sc'] >= 4)) {
                    $this->cat['gt0'][$v['P1']][]=$vi;
                } else $this->cat[0][$v['P1']][]=$vi;
            }
            if(($v['P1'] >= $this->minQtyRadius && $v['sc'] >= 2) || ($v['P1'] < $this->minQtyRadius && $v['sc'] >= 4)) $this->rads[$v['P1']]=$this->diametr==$v['P1']?true:false;
        }

        $this->rvws=$this->_reviewsList([
            'modelId'=>$this->model_id
        ]);
        //print_r($this->rvws);


        $this->_sidebar();

    }

}
