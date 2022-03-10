<?
class App_Catalog_Disks_Model_Controller extends App_Catalog_Disks_Common_Controller {

    //бренды дисков
    public function index() {

        $this->view('catalog/disks/model');
        if(empty($this->model_id)) $this->cc->que('model_by_sname',Url::$spath[2],1,'',2); else $this->cc->que('model_by_id',$this->model_id);
        //$this->cc->que('model_by_sname',Url::$spath[2],1,'',2);
        if(!$this->cc->qnum()) return App_Route::redir404();
        $this->cc->next();
        $this->replica=$this->cc->qrow['replica'];
        $this->brand_sname=$this->cc->qrow['brand_sname'];
        $this->bname=Tools::unesc($this->cc->qrow['bname']);
        $this->mname=Tools::unesc($this->cc->qrow['name']);
        $this->balt=Tools::unesc($this->cc->qrow['balt']!=''?$this->cc->qrow['balt']:$this->cc->qrow['bname']);
        $this->malt=Tools::unesc($this->cc->qrow['alt']!=''?$this->cc->qrow['alt']:$this->cc->qrow['name']);
        $this->balt1=$this->firstS($this->balt);
        $this->baltOther=$this->otherS($this->balt);
        $this->bimg1=$this->cc->make_img_path($this->cc->qrow['brand_img1']);
        $this->bAnc=$this->replica?"Replica {$this->bname}":$this->bname;
        $this->malt1=$this->firstS($this->malt);
        $this->maltOther=$this->otherS($this->malt);
        $this->brand_id=$this->cc->qrow['brand_id'];
        $this->model_id=$this->cc->qrow['model_id'];
        $this->img1=($this->cc->make_img_path($this->cc->qrow['img1'])).($this->cc->make_img_path($this->cc->qrow['img1']) ? '?v='.ExLib::loadImagesId() : '');
        $this->img2=($this->cc->make_img_path($this->cc->qrow['img2'])).($this->cc->make_img_path($this->cc->qrow['img2']) ? '?v='.ExLib::loadImagesId() : '');
        $this->msuffix=Tools::unesc($this->cc->qrow['suffix']);
        if($this->cc->qrow['mspez_id']==2) $this->new=true; else $this->new=0;
        $this->diametr=(int)@$_GET['diametr'];
        $this->dType=@$this->diskTypes2[$this->cc->qrow['P1']];
        $this->burl='/'. App_Route::_getUrl($this->replica?'dCat':'dCat').'/'.$this->brand_sname.'.html';
        $this->video_link=$this->cc->qrow['video_link'];
        $d_type = @$this->cc->qrow['MP1']; // 2-литые
        // Стикеры
        if (!empty($this->cc->qrow['sticker_id'])) {
            $CC_Ctrl = new CC_Ctrl();
            $stickers_list = $CC_Ctrl::getStickersList();
            $m_sticker = $CC_Ctrl->getModelSticker($this->cc->qrow['model_id']);
            if (!empty($m_sticker)) {
                @$this->m_sticker = array_merge($m_sticker, $stickers_list[$m_sticker['sticker_type']]);
            }
            unset($CC_Ctrl);
        }
        else $this->m_sticker = array();
        //
        // Цвет
        @$color_info = $this->cc->dict_search_key($this->cc->qrow['default_color'], $this->cc->qrow['gr'], $this->brand_id);
        @$this->default_color = $this->cc->qrow['default_color'];
        $this->extcolor = $this->cc->getDictByIds(@$color_info[@$this->cc->qrow['default_color']]);
        //
        $this->imgAlt="Фото диска {$this->bname} {$this->mname} {$this->msuffix}";
        $this->imgTitle="Литые диски {$this->bname} {$this->mname}  {$this->msuffix}";

        $this->bcatUrl='/'.App_Route::_getUrl('dCat').'/'.$this->brand_sname.'.html';

        if($this->replica==1) {
            $this->_title="Replica {$this->bname} {$this->mname} {$this->msuffix}";
            $this->title="Replica {$this->bname} {$this->mname} {$this->msuffix} - цены, фото, купить";
            $this->breadcrumbs['диски реплика']=array('/'.App_Route::_getUrl('replicaCat').'.html','перейти в каталог дисков реплика');
            $this->breadcrumbs[$this->bname]=array('/'.App_Route::_getUrl('dCat').'/'.$this->brand_sname.'.html',"купить диски реплика {$this->balt}");
            $this->breadcrumbs[]=$this->bname.' '.$this->mname;
            $this->description="Все размеры и цены дисков replica {$this->bname} {$this->mname}. Актуальное наличие, фото и цены на литые диски реплика {$this->balt1} {$this->malt}";
            $this->keywords="{$this->bname} {$this->mname}, купить диски {$this->balt1} {$this->malt}";
        }else{
            $this->_title="{$this->bname} {$this->mname} {$this->msuffix}";
            $this->title="{$this->bname} {$this->mname} {$this->msuffix} - цены, фото, купить";
            $this->breadcrumbs=array('диски'=>array('/'.App_Route::_getUrl('dCat').'.html','перейти в каталог дисков'));
            if($d_type == 2) { // Литые
                $this->breadcrumbs[$this->bname]=array('/'.App_Route::_getUrl('dCat').'/'.$this->brand_sname.'.html',"купить диски {$this->balt}");
            } else $this->breadcrumbs[$this->bname]=array('/'.App_Route::_getUrl('dSearch').'?vendor='.$this->brand_sname, "купить диски {$this->balt}");
            $this->breadcrumbs[]=$this->bname.' '.$this->mname;
            $this->description="Все размеры и цены дисков {$this->bname} {$this->mname}. Актуальное наличие, фото и цены на литые диски {$this->balt1} {$this->malt}";
            $this->keywords="{$this->bname} {$this->mname}, купить диски {$this->balt1} {$this->malt}";
        }

//
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

        // алтернативные модели
        $this->relModels=array();
        $r=array(
            'gr'=>2,
            'brand_id'=>$this->brand_id,
            'nolimits'=>1,
            'where'=>"cc_model.name LIKE '".Tools::esc($this->mname)."' AND cc_model.model_id!={$this->model_id} AND cc_model.img3!=''",
            'order'=>"m_pos ASC"
        );
        $this->cc->models($r);
        $d=$this->cc->fetchAll('', MYSQL_ASSOC);
        foreach($d as $v){
            $this->relModels[]=array(
                'img'=>$this->cc->makeImgPath($v['img3']),
                'anc'=>$this->bname.' '.$this->mname.' '.Tools::unesc($v['suffix']),
                'url'=>'/'.App_Route::_getUrl('dModel').'/'.$v['sname'].'.html'
            );
        }

        // галерея
        $gl=new CC_Gallery();
        $this->gallery = Tools::sortImagesBySize($gl->glist($this->model_id), 185);

        // сертификаты
        $cert=new CC_Certificates();
        $this->certificates = Tools::sortImagesBySize($cert->glist($this->model_id), 450);

        // размеры
        $this->cat=array('gt0'=>array(), 0=>array());
        $this->num=$this->cc->cat_view(array(
            'model_id'=>$this->model_id,
            'gr'=>2,
            'scDiv'=>1,
            /*'where'=>array($this->minQtyRadiusSQL),   Скроет блок "Позиции отсутствующие на складе"*/
            'nolimits'=>true,
            'order'=>'scDiv DESC, cc_cat.P5, cc_cat.P4,cc_cat.P6, cc_cat.P1, cc_cat.P2, cc_cat.P3'
        ));
        $d=$this->cc->fetchAll();
        $burl='/'.App_Route::_getUrl('dTipo').'/';
        $_altt='/'.App_Route::_getUrl('dSearch').'.html?';
        $this->rads=array();
        foreach($d as $v){
            if(empty($this->diametr) || $this->diametr==$v['P5']) {
                $vi=array(
                    'video_link'=>  $v['video_link'],
                    'url'=>			$burl.$v['cat_sname'].'.html',
                    'modelName'=>	Tools::html($v['bname'].' '.$v['mname']),
                    'colorUrl'=>	$this->dict_url($this->cc->dict_search_key($v['csuffix'],$v['gr'],$this->brand_id)),
                    'anc'=>	Tools::unesc($v['bname'].' '.$v['mname']),
                    'title'=>	    "диски ".Tools::html($v['bname'].' '.$v['mname']." {$v['P2']}x{$v['P5']}"." {$v['P4']}/{$v['P6']}"." ET{$v['P1']}".($v['P3']!=0?" DIA{$v['P3']}":'').($v['csuffix']!=''?" {$v['csuffix']}":'')),
                    'qtyText'=>		$v['sc']>12?"&gt;&nbsp;12&nbsp;шт":(!$v['sc']?'-':"{$v['sc']}&nbsp;шт"),
                    'maxQty'=>		$v['sc'],
                    'defQty'=>		$v['sc']>4 || $v['sc']==0?4:$v['sc'],
                    'priceText'=>	$v['cprice']?(Tools::nn($v['cprice'])." р"):'звоните',
                    'cprice' =>    $v['cprice']?(Tools::nn($v['cprice'])):'0',
                    'cat_id'=>		$v['cat_id'],
                    'LZ'=>          $v['P4'],
                    'razmer'=>		"{$v['P2']} x {$v['P5']}",
                    'sverlovka'=>	"{$v['P4']} x {$v['P6']}",
                    'sverlovka1'=>	"{$v['P4']}/{$v['P6']}",
                    'DIA'=>			$v['P3']!=0?$v['P3']:'',
                    'ET'=>			$v['P1'],
                    // http://www.dilijans.org/d_filter.html?ap=1&p2=8.5&p5=18&p1=46&p4=5&p6=120&p3=74.1
                    'altt'=>        urlencode($_altt."p2={$v['P2']}&p5={$v['P5']}&p1={$v['P1']}&p4={$v['P4']}&p6={$v['P6']}&p3={$v['P3']}")
                );

                if(($v['P5'] >= $this->minQtyRadius && $v['sc'] >= 2) || ($v['P5'] < $this->minQtyRadius && $v['sc'] >= 4))
                    $this->cat['gt0'][$v['P5']][]=$vi;
                else
                    $this->cat[0][$v['P5']][]=$vi;
            }

            if(($v['P5'] >= $this->minQtyRadius && $v['sc'] >= 2) || ($v['P5'] < $this->minQtyRadius && $v['sc'] >= 4))  $this->rads[$v['P5']]=$this->diametr==$v['P5']?true:false;
        }
        $this->_sidebar();

    }
}
