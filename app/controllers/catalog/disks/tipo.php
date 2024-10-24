<?
class App_Catalog_Disks_Tipo_Controller extends App_Catalog_Disks_Common_Controller {

    //бренды дисков
    public function index() {

        $this->view('catalog/disks/tipo');

        // тип отображения результатов поиска
        if(@$_COOKIE['stype']=='lenta') {
            $this->altTpl='catalog/disks/searchLentaTable';
            $this->altViewMode=[
                'setBlockMode',
                'active'
            ];
        } else {
            $this->altTpl='catalog/disks/searchBlockTable';
            $this->altViewMode=[
                'active',
                'setLentaMode'
            ];
        }

        if(empty($this->cat_id)) $this->cc->que('cat_by_sname',Url::$spath[2],1,'','',2); else $this->cc->que('cat_by_id',$this->cat_id);
        if(!$this->cc->qnum()) return App_Route::redir404();
        $this->cc->next();
        $this->bname=Tools::unesc($this->cc->qrow['bname']);
        $this->brand_sname=$this->cc->qrow['brand_sname'];
        $this->model_sname=$this->cc->qrow['model_sname'];
        $this->brand_id=$this->cc->qrow['brand_id'];
        $this->mname=Tools::unesc($this->cc->qrow['name']);
        $this->color=Tools::unesc($this->cc->qrow['suffix']);
        $this->balt=$this->cc->qrow['balt']!=''?Tools::unesc($this->cc->qrow['balt']):$this->bname;
        $this->malt=$this->cc->qrow['alt']!=''?Tools::unesc($this->cc->qrow['alt']):$this->mname;
        $this->balt1=$this->firstS($this->balt);
        $this->baltOther=$this->otherS($this->balt);
        $this->malt1=$this->firstS($this->malt);
        $this->maltOther=$this->otherS($this->malt);
        $this->model_id=$this->cc->qrow['model_id'];
        $this->replica=$this->cc->qrow['replica'];
        $this->burl='/'.App_Route::_getUrl('dCat').'/'.$this->brand_sname.'.html';
        $this->self_url='/'.App_Route::_getUrl('dTipo').'/'.$this->cc->qrow['sname'].'.html';
        $this->cprice=Tools::nn($this->cc->qrow['cprice']);
        $this->width=$this->cc->qrow['P2'];
        $this->radius=$this->cc->qrow['P5'];
        $this->dia_val=$this->cc->qrow['P3'];
        $this->pcd=$this->cc->qrow['P6'];
        $this->dirok=$this->cc->qrow['P4'];
        $this->size="{$this->cc->qrow['P2']}x{$this->cc->qrow['P5']}";
        $this->size1="{$this->cc->qrow['P2']} x {$this->cc->qrow['P5']}";
        $this->et=$this->cc->qrow['P1'];
        $d_type = $this->cc->qrow['MP1']; // 2-литые
        if($this->cc->qrow['P4'] && $this->cc->qrow['P6']) $this->sverlovka="{$this->cc->qrow['P4']}/{$this->cc->qrow['P6']}"; else $this->sverlovka='';
        if($this->cc->qrow['P4'] && $this->cc->qrow['P6']) $this->sverlovka1="{$this->cc->qrow['P4']} / {$this->cc->qrow['P6']}"; else $this->sverlovka1='';
        if($this->cc->qrow['P3']) $this->dia=$this->cc->qrow['P3']; else $this->dia='';
        $this->fullSize=trim($this->size.' '.$this->sverlovka.($this->et!=''?" ET{$this->et}":'').' d'.$this->dia);
        $this->fullSize1=trim($this->size1.' '.$this->sverlovka1.($this->et!=''?" ET {$this->et}":'').' '.$this->dia);
        $this->video_link=$this->cc->qrow['video_link'];

        $this->color=Tools::unesc($this->cc->qrow['suffix']);
        if(!empty($this->color)){
            $this->colorUrl=$this->dict_url($cex=$this->cc->dict_search_key($this->cc->qrow['csuffix'],$this->cc->qrow['gr'],$this->cc->qrow['brand_id']));
            if(!empty($cex)) $this->colorExplain=trim(Tools::stripTags($this->cc->getDictByIds(current($cex))));
        }else $this->colorUrl='';

        if($this->cc->qrow['cprice'])  $this->priceText=Tools::nn($this->cc->qrow['cprice']).'&nbsp;руб.';
        else $this->priceText='звоните';

        $this->qty=(($this->cc->qrow['P5'] >= $this->minQtyRadius && $this->cc->qrow['sc'] >= 2) || ($this->cc->qrow['P5'] < $this->minQtyRadius && $this->cc->qrow['sc'] >= 4) ? (int)$this->cc->qrow['sc'] : 0);
        $this->scText=$this->qty==0?"<noindex>нет в наличии</noindex>":"есть на складе";
        $this->qtyText= $this->qty>12?"&gt;&nbsp;12&nbsp;шт":(!$this->qty?'отсутствует':$this->qty."&nbsp;шт");
        $this->defQty=$this->qty>4 || $this->qty==0?4:$this->cc->qrow['sc'];
        $this->maxQty=$this->cc->qrow['sc'];
        $this->minQty=$this->minQty($this->radius,2);

        $this->cat_id=$this->cc->qrow['cat_id'];

        $this->img1=($this->cc->make_img_path($this->cc->qrow['img1'])).($this->cc->make_img_path($this->cc->qrow['img1']) ? '?v='.ExLib::loadImagesId() : '');
        $this->img2=($this->cc->make_img_path($this->cc->qrow['img2'])).($this->cc->make_img_path($this->cc->qrow['img2']) ? '?v='.ExLib::loadImagesId() : '');
        $this->imgAlt='Фото диска '.Tools::cutDoubleSpaces($this->bname.' '.$this->mname.' '.$this->fullSize.' '.$this->color);
        $this->imgTitle='Диски '.Tools::cutDoubleSpaces($this->bname.' '.$this->mname.' '.$this->fullSize1.' '.$this->color);

        $this->bimg1=$this->cc->make_img_path($this->cc->qrow['brand_img1']);

        $this->brand_alt=$this->brand.($this->balt!=''?" ({$this->balt})":'');

        $this->mtext=$this->ss->parseText(Tools::unesc($this->cc->qrow['text']));

        if($this->cc->qrow['mspez_id']==2) $this->new=true; else $this->new=false;

        $this->middleText=$this->parse($this->ss->getDoc('disk_tipo_rekomend$6'));

        $this->title='Диски '.Tools::cutDoubleSpaces($this->bname.' '.$this->mname.' '.$this->fullSize1.' '.$this->color).' - купить, цена, наличие';
        $this->_title=Tools::cutDoubleSpaces($this->bname.' '.$this->mname.' '.$this->fullSize.' '.$this->color);

        if($this->replica){
            $this->breadcrumbs['диски реплика']=array('/'.App_Route::_getUrl('replicaCat').'.html','каталог дисков replica');
            $this->breadcrumbs["replica {$this->balt}"]=array('/'.App_Route::_getUrl('dCat').'/'.$this->brand_sname.'.html',"литые диски реплика {$this->balt1}");
            $this->breadcrumbs["{$this->balt} {$this->malt}"]=array('/'.App_Route::_getUrl('dModel').'/'.$this->model_sname.'.html',"литые диски реплика {$this->balt1} {$this->malt}");
        }else{
            $this->breadcrumbs['диски']=array('/'.App_Route::_getUrl('dCat').'.html','каталог дисков');
            if($d_type == 2) { // Литые
                $this->breadcrumbs["диски {$this->balt}"]=array('/'.App_Route::_getUrl('dCat').'/'.$this->brand_sname.'.html',"литые диски {$this->balt1}");
            } else $this->breadcrumbs["диски {$this->balt}"]=array('/'.App_Route::_getUrl('dSearch').'?vendor='.$this->brand_sname,"литые диски {$this->balt1}");
            $this->breadcrumbs["{$this->balt} {$this->malt}"]=array('/'.App_Route::_getUrl('dModel').'/'.$this->model_sname.'.html',"литые диски {$this->balt1} {$this->malt}");
        }
        $this->breadcrumbs["{$this->bname} {$this->mname} {$this->fullSize}"]='';

        $this->keywords="колесные диски {$this->fullSize} купить цена";

        //
        if (!empty($this->cc->qrow['seo_title']))
            $this->title = $this->cc->qrow['seo_title'];
        if (!empty($this->cc->qrow['seo_keywords']))
            $this->keywords = $this->cc->qrow['seo_keywords'];
        if (!empty($this->cc->qrow['seo_description']))
            $this->description = $this->cc->qrow['seo_description'];

        $this->adv_text = $this->ss->parseText(Tools::unesc($this->cc->qrow['adv_text']));
        //
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

        $this->deliveryCost=Data::get('delivery_cost');
        $this->deliveryCalcUrl='/i/dostavka.html';


        // галерея
        $gl=new CC_Gallery();
        $this->gallery = Tools::sortImagesBySize($gl->glist($this->model_id), 185);

        // сертификаты
        $cert=new CC_Certificates();
        $this->certificates = Tools::sortImagesBySize($cert->glist($this->model_id), 450);

        $burl='/'.App_Route::_getUrl('tTipo').'/';

        //альтернативные размеры
        $this->limits=Data::get('cc_tipoNumList');
        if(!empty($this->limits)) $this->limits=explode(',',$this->limits); else $this->limits=[];
        $this->limit=0;
        if(!empty($_GET['num'])){
            $this->limit=(int)$_GET['num'];
        }
        if(empty($this->limit)) $this->limit=20;
        $this->page=@(int)Url::$sq['page'];
        $r['start']=max(0,$this->page*$this->limit-$this->limit);
        $r['lines']=$this->limit;

        $this->tipoNum=intval(@Url::$sq['num'])>0?intval(Url::$sq['num']):20;
        $this->cat=array();
        $this->num=$this->cc->cat_view(array(
            'P1'=>$this->et,
            'P2'=>$this->width,
            'P3'=>array('from'=>$this->cc->qrow['P3'] + $this->_deltaDiaMin,'to'=>$this->cc->qrow['P3'] + $this->_deltaDiaMax),
            'P4'=>$this->dirok,
            'P5'=>$this->radius,
            'P6'=>$this->pcd,
            'gr'=>2,
            'brand_id'=>(@Url::$sq['bid']&&@Url::$sq['bid']>0)?@Url::$sq['bid']:'',
            'start'=>	intval((@Url::$sq['page']?(Url::$sq['page']-1):0)*$this->tipoNum),
            'lines'=>	$this->tipoNum,
            'order'=>' cc_brand.replica DESC, cc_brand.pos DESC, m_pos ASC, cc_brand.name,cc_model.name,cc_cat.P1',
            'where'=>"cc_cat.cat_id!='{$this->cat_id}'".($this->hideDSCZero?" AND $this->minQtyRadiusSQL":'')
        ));
        $d=$this->cc->fetchAll();
        if($this->num) {
            $this->paginator=$this->cc->paginator(Url::$path,Url::$sq,@Url::$sq['page'],$this->num,$this->limit,'page',array(
                'active'=>	'<li class="active">{page}</li>',
                'noActive'=>'<li><a href="{url}">{page}</a></li>',
                'dots'=>	'<li>...</li>'
            ));
        }
        $this->cat=array();
        $burl='/'.App_Route::_getUrl('dTipo').'/';
        foreach($d as $v){
            $this->cat[]=$this->catRow($v,$burl);
        }
        // Фильтр по брендам
        $this->cc->cat_view(array(
            'P1'=>$this->et,
            'P2'=>$this->width,
            'P3'=>array('from'=>$this->dia_val + $this->_deltaDiaMin,'to'=>$this->dia_val + $this->_deltaDiaMax),
            'P4'=>$this->dirok,
            'P5'=>$this->radius,
            'P6'=>$this->pcd,
            'gr'=>2,
            'brand_id'=>'',
            'order'=>'cc_brand.replica DESC,cc_brand.name,cc_model.name,cc_cat.P1',
            'where'=>"cc_cat.cat_id!='{$this->cat_id}'".($this->hideDSCZero?" AND $this->minQtyRadiusSQL":'')
        ));
        $bfres=$this->cc->fetchAll();
        $this->alt_brands = Array();
        foreach($bfres as $v){
            $this->alt_brands[$v['brand_id']] = $v['bname'];
        }

        $this->_sidebar();

        // ************* Применяемость *************
        $AB = new CC_AB();
        $this->suitable = $AB->getAvtoArrayByTipo(Array(
            'P1' => array('_from'=>$this->_deltaET, '_to'=> $this->deltaET_, 'ex' => $this->et),
            'P2' => $this->width,
            'P3' => array('from'=>0, 'to'=> $this->dia_val + $this->_deltaDiaMax),
            'P4' => $this->dirok,
            'P5' => $this->radius,
            'P6' => $this->pcd
        ), 2);
        // *****************************************
    }

    /*function axSearch()
    {
        // Поля REQUEST: bid,
        $bid = @$_REQUEST['bid'];
        @Url::$sq['bid'] = $bid;

        // тип отображения результатов поиска
        if(@$_COOKIE['stype']=='lenta') {
            $this->altTpl='catalog/disks/searchLentaTable';
            $this->altViewMode=[
                'setBlockMode',
                'active'
            ];
        } else {
            $this->altTpl='catalog/disks/searchBlockTable';
            $this->altViewMode=[
                'active',
                'setLentaMode'
            ];
        }

        if(empty($this->cat_id)) $this->cc->que('cat_by_sname',Url::$spath[2],1,'','',2); else $this->cc->que('cat_by_id',$this->cat_id);
        if(!$this->cc->qnum()) return App_Route::redir404();
        $this->cc->next();
        $this->width=$this->cc->qrow['P2'];
        $this->radius=$this->cc->qrow['P5'];
        $this->pcd=$this->cc->qrow['P6'];
        $this->dirok=$this->cc->qrow['P4'];
        $this->size="{$this->cc->qrow['P2']}x{$this->cc->qrow['P5']}";
        $this->size1="{$this->cc->qrow['P2']} x {$this->cc->qrow['P5']}";
        $this->et=$this->cc->qrow['P1'];
        $this->cat_id = $this->cc->qrow['cat_id'];

        //альтернативные размеры
        $this->tipoNum= 20;
        $this->cat=array();
        $this->num=$this->cc->cat_view(array(
            'P1'=>$this->et,
            'P2'=>$this->width,
            'P3'=>array('from'=>$this->cc->qrow['P3'] + $this->_deltaDiaMin,'to'=>$this->cc->qrow['P3'] + $this->_deltaDiaMax),
            'P4'=>$this->dirok,
            'P5'=>$this->radius,
            'P6'=>$this->pcd,
            'gr'=>2,
            'brand_id'=> ($bid > 0) ? $bid : '',
            'order'=>'cc_brand.name,cc_model.name,cc_cat.P1',
            'where'=>"cc_cat.cat_id!='{$this->cat_id}'".($this->hideDSCZero?" AND $this->minQtyRadiusSQL":'')
        ));
        $d=$this->cc->fetchAll();
        if($this->num) {
            $this->paginator=$this->cc->paginator(Url::$path, Url::$sq, 0,$this->num,$this->tipoNum,'page',array(
                'active'=>	'<li class="active">{page}</li>',
                'noActive'=>'<li><a href="{url}">{page}</a></li>',
                'dots'=>	'<li>...</li>'
            ));
        }

        $burl='/'.App_Route::_getUrl('dTipo').'/';

        $this->cat=array();
        $this->alt_brands = Array();
        foreach($d as $v){
            //$this->alt_brands[$v['brand_id']] = $v['bname'];
            $this->cat[]=$this->catRow($v,$burl);
        }
        // ****************** Вывод и выход ******************
        if (!empty($this->cat)) {
            @ob_clean();
            @ob_start();
            ?>
            <div class="box-padding">
                <div class="box-rez">

                    <? if (!empty($this->paginator)) {
                        ?>
                        <div class="paginator" style="float: left"><?
                        ?>
                        <ul><?
                        foreach ($this->paginator as $v) echo $v;
                        ?></ul><?
                        ?></div><?
                    } ?>

                    <div class="vids">

                        <? foreach ($this->altViewMode as $v) {
                            ?><a href="#" class="<?= $v ?>"></a><?
                        } ?>

                    </div>
                </div>

            </div>


            <div class="box-shadow">

                <?
                if(class_exists('App_App')) {
                    $app = new App_App();
                    echo $app->incView($this->altTpl, false, Array('cat' => $this->cat));
                }
                ?>

                <div class="box-padding">

                    <div class="vids">

                        <? foreach ($this->altViewMode as $v) {
                            ?><a href="#" class="<?= $v ?>"></a><?
                        } ?>

                    </div>

                    <? if (!empty($this->paginator)) {
                        ?>
                        <div class="paginator" style="float: left"><?
                        ?>
                        <ul><?
                        foreach ($this->paginator as $v) echo $v;
                        ?></ul><?
                        ?></div><?
                    } ?>

                </div>

            </div>
            <?
        }
        else
        {
            echo 'Результаты не найдены';
        }
        $this->r['data'] = ob_get_contents();
        @ob_end_clean();
    }*/

    function axView()
    {
        // Поля REQUEST: bid,
        $bid = @$_REQUEST['bid'];
        @Url::$sq['bid'] = $bid;

        // тип отображения результатов поиска
        if(@$_COOKIE['stype']=='lenta') {
            $this->altTpl='catalog/disks/searchLentaTypo';
            $this->altViewMode=[
                'setBlockMode',
                'active'
            ];
        } else {
            $this->altTpl='catalog/disks/searchBlockTypo';
            $this->altViewMode=[
                'active',
                'setLentaMode'
            ];
        }

        if(empty($this->cat_id)) $this->cc->que('cat_by_sname',Url::$spath[2],1,'','',2); else $this->cc->que('cat_by_id',$this->cat_id);
        if(!$this->cc->qnum()) return App_Route::redir404();
        $this->cc->next();
        $this->width=$this->cc->qrow['P2'];
        $this->radius=$this->cc->qrow['P5'];
        $this->pcd=$this->cc->qrow['P6'];
        $this->dirok=$this->cc->qrow['P4'];
        $this->size="{$this->cc->qrow['P2']}x{$this->cc->qrow['P5']}";
        $this->size1="{$this->cc->qrow['P2']} x {$this->cc->qrow['P5']}";
        $this->et=$this->cc->qrow['P1'];
        $this->cat_id = $this->cc->qrow['cat_id'];
        $cc = new CC_Ctrl();
        $this->ex_num=$cc->cat_view(array(
            'P1'=>$this->et,
            'P2'=>$this->width,
            'P3'=>array('from'=>$this->cc->qrow['P3'] + $this->_deltaDiaMin,'to'=>$this->cc->qrow['P3'] + $this->_deltaDiaMax),
            'P4'=>$this->dirok,
            'P5'=>$this->radius,
            'P6'=>$this->pcd,
            'gr'=>2,
            'brand_id'=> ($bid > 0) ? $bid : '',
            'order'=>'cc_brand.name,cc_model.name,cc_cat.P1',
            'nolimits' => true,
            'where'=>"cc_cat.cat_id!='{$this->cat_id}'".($this->hideDSCZero?" AND $this->minQtyRadiusSQL":'')
        ));
        //альтернативные размеры
        $this->limits=Data::get('cc_tipoNumList');
        if(!empty($this->limits)) $this->limits=explode(',',$this->limits); else $this->limits=[];
        $this->limit=0;
        if(!empty($_GET['num'])){
            $this->limit=(int)$_GET['num'];
        }
        if(empty($this->limit)) $this->limit=(int)Data::get('cc_tipoNum');
        $this->page=@(int)Url::$sq['page'] ? @(int)Url::$sq['page'] : 1;

        $this->cat=array();
        $this->num=$this->cc->cat_view(array(
            'P1'=>$this->et,
            'P2'=>$this->width,
            'P3'=>array('from'=>$this->cc->qrow['P3'] + $this->_deltaDiaMin,'to'=>$this->cc->qrow['P3'] + $this->_deltaDiaMax),
            'P4'=>$this->dirok,
            'P5'=>$this->radius,
            'P6'=>$this->pcd,
            'gr'=>2,
            'brand_id'=> ($bid > 0) ? $bid : '',
            'order'=>'cc_brand.name,cc_model.name,cc_cat.P1',
            'start'=>max(0,$this->page*$this->limit-$this->limit),
            'lines'=>$this->limit,
            'where'=>"cc_cat.cat_id!='{$this->cat_id}'".($this->hideDSCZero?" AND $this->minQtyRadiusSQL":'')
        ));
        $d=$this->cc->fetchAll();
        if($this->num) {
            $this->paginator=$this->cc->paginator(Url::$path,Url::$sq,@Url::$sq['page'],$this->num,$this->limit,'page',array(
                'active'=>	'<li class="active">{page}</li>',
                'noActive'=>'<li><a href="{url}">{page}</a></li>',
                'dots'=>	'<li>...</li>'
            ));
        }

        $burl='/'.App_Route::_getUrl('dTipo').'/';

        $this->cat=array();
        $this->alt_brands = Array();
        foreach($d as $v){
            //$this->alt_brands[$v['brand_id']] = $v['bname'];
            $this->cat[]=$this->catRow($v,$burl);
        }
        // ****************** Вывод и выход ******************
        global $app;
        if (is_file($app->namespace . '/view/'.$this->altTpl.'.php')) {
            extract((array)$app->controllerInstance, EXTR_OVERWRITE);
            extract($app->controllerInstance->_data, EXTR_OVERWRITE);
            include $app->namespace . '/view/' .$this->altTpl . '.php';
        } else
            throw new AppException ('[App::output()]: ' . $app->namespace . '/view/' . $this->altTpl . ' open fault.');
        exit(200);
    }


}