<?
class App_Catalog_Tyres_Tipo_Controller extends App_Catalog_Tyres_Common_Controller {

    //бренды дисков
    public function index() {

        $this->view('catalog/tyres/tipo');

        // тип отображения результатов поиска
        if(@$_COOKIE['stype']=='lenta') {
            $this->altTpl='catalog/tyres/searchLentaTable';
            $this->altViewMode=[
                'setBlockMode',
                'active'
            ];
        } else {
            $this->altTpl='catalog/tyres/searchBlockTable';
            $this->altViewMode=[
                'active',
                'setLentaMode'
            ];
        }

        if(empty($this->cat_id)) $this->cc->que('cat_by_sname',Url::$spath[2],1,'','',1); else $this->cc->que('cat_by_id',$this->cat_id);
        if(!$this->cc->qnum()) return App_Route::redir404();
        $this->cc->next();

        $this->bname=Tools::unesc($this->cc->qrow['bname']);
        $this->brand_sname=$this->cc->qrow['brand_sname'];
        $this->model_sname=$this->cc->qrow['model_sname'];
        $this->brand_id=$this->cc->qrow['brand_id'];
        $this->mname=Tools::unesc($this->cc->qrow['name']);
        $this->suffix=Tools::unesc($this->cc->qrow['suffix']);
        $this->balt=$this->cc->qrow['balt']!=''?Tools::unesc($this->cc->qrow['balt']):$this->bname;
        $this->malt=$this->cc->qrow['alt']!=''?Tools::unesc($this->cc->qrow['alt']):$this->mname;
        $this->balt1=$this->firstS($this->balt);
        $this->baltOther=$this->otherS($this->balt);
        $this->malt1=$this->firstS($this->malt);
        $this->maltOther=$this->otherS($this->malt);
        $this->model_id=$this->cc->qrow['model_id'];
        $this->burl='/'.App_Route::_getUrl('tCat').'/'.$this->brand_sname.'.html';
        $this->self_url='/'.App_Route::_getUrl('tTipo').'/'.$this->cc->qrow['sname'].'.html';
        $this->cprice=Tools::nn($this->cc->qrow['cprice']);
        $this->width=$this->cc->qrow['P3'];
        $this->height=$this->cc->qrow['P2'];
        $this->radius=$this->cc->qrow['P1'];
        $this->svr=$this->cc->qrow['P6'];
        $this->size="{$this->cc->qrow['P3']}/{$this->cc->qrow['P2']} ".($this->cc->qrow['P6']?'Z':'')."R{$this->cc->qrow['P1']}";
        $this->fullSize=$this->size.' '.$this->inis.' '.$this->suffix;
        $this->video_link=$this->cc->qrow['video_link'];
        $this->INIS=$this->cc->qrow['P7'];
        $this->inisUrl=$this->INIS!=''?('<a href="#" rel="/ax/explain/inis?v='.$this->INIS.'" title="Что означает '.$this->INIS.'?" class="atip gr">'.$this->INIS.'</a>'):$this->INIS;

        if($this->cc->qrow['cprice'])  $this->priceText=Tools::nn($this->cc->qrow['cprice']).'&nbsp;руб.';
        else $this->priceText='звоните';

        $this->qty=(($this->cc->qrow['P1'] >= $this->minQtyRadius && $this->cc->qrow['sc'] >= 2) || ($this->cc->qrow['P1'] < $this->minQtyRadius && $this->cc->qrow['sc'] >= 4) ? (int)$this->cc->qrow['sc'] : 0);
        $this->scText=$this->qty==0?"<noindex>нет в наличии</noindex>":"есть на складе";
        $this->qtyText=$this->qty>12?"&gt;&nbsp;12&nbsp;шт":(!$this->qty?'отсутствует':$this->qty."&nbsp;шт");
        $this->defQty=$this->qty>4 || $this->qty==0?4:$this->cc->qrow['sc'];
        $this->maxQty=$this->cc->qrow['sc'];
        $this->minQty=$this->minQty($this->radius,1);

        $this->cat_id=$this->cc->qrow['cat_id'];

        $this->img1=($this->cc->make_img_path($this->cc->qrow['img1'])).'?v='.ExLib::loadImagesId();
        $this->img2=($this->cc->make_img_path($this->cc->qrow['img2'])).'?v='.ExLib::loadImagesId();
        $this->imgAlt='Фото шины '.Tools::cutDoubleSpaces($this->bname.' '.$this->mname.' '.$this->size.' '.$this->inis.' '.$this->suffix);
        $this->imgTitle='Шина '.Tools::cutDoubleSpaces($this->bname.' '.$this->mname.' '.$this->size.' '.$this->inis.' '.$this->suffix);

        $this->bimg1=$this->cc->make_img_path($this->cc->qrow['brand_img1']);

        $this->brand_alt=$this->brand.($this->balt!=''?" ({$this->balt})":'');

        $this->sezonId=$this->cc->qrow['MP1'];
        $this->shipId=$this->cc->qrow['MP3'];
        $this->atId=$this->cc->qrow['MP2'];

        $this->mtext=$this->ss->parseText(Tools::unesc($this->cc->qrow['text']));


        if(trim(Tools::stripTags($this->mtext))!=''){
            $this->mtext=$this->ss->splitText($this->mtext,2);
        }else{
            $this->mtext=$this->ss->splitText($this->parse($this->ss->getDoc('tpl_tmodel$10')),2);
        }

        if($this->cc->qrow['mspez_id']==1) $this->new=true; else $this->new=false;

        $this->title='Шины '.Tools::cutDoubleSpaces($this->bname.' '.$this->mname.' '.$this->size.' '.$this->inis.' '.$this->suffix).' - купить, цена, наличие';
        $this->_title=Tools::cutDoubleSpaces($this->bname.' '.$this->mname.' '.$this->size.' '.$this->inis.' '.$this->suffix);

        $this->breadcrumbs['шины']=array('/'.App_Route::_getUrl('tCat').'.html','каталог шин');

        switch($this->sezonId){
            case 1:
                $this->sezIco='<img src="/app/images/sun.png" title="Летние шины" class="nttip">';
                $this->breadcrumbs['летние шины']=array('/'.App_Route::_getUrl('tSummer').'.html','каталог летней резины');
                $this->breadcrumbs["летние шины {$this->balt1}"]=array('/'.App_Route::_getUrl('tSummer').'/'.$this->brand_sname.'.html',"летняя резина {$this->balt}");
                $this->breadcrumbs["{$this->balt1} {$this->malt1}"]=array('/'.App_Route::_getUrl('tModel').'/'.$this->model_sname.'.html',"летняя резина {$this->balt1} {$this->malt}");
                $this->description="Купить летние шины {$this->bname} {$this->mname} {$this->fullSize} ({$this->balt1}), автомобильные летние шины, купить, цена, продажа, доставка по Москве и России";
                break;
            case 2:
                $this->sezIco='<img src="/app/images/snow.png" title="Зимние шины" class="nttip">';
                if($this->cc->qrow['MP3']) $this->sezIco.='<img src="/app/images/ship.png" title="Шипованные шины" class="nttip">';
                $this->breadcrumbs['зимние шины']=array('/'.App_Route::_getUrl('tWinter').'.html','каталог зимней резины');
                $this->breadcrumbs["зимние шины {$this->balt1}"]=array('/'.App_Route::_getUrl('tWinter').'/'.$this->brand_sname.'.html',"зимняя резина {$this->balt}");
                $this->breadcrumbs["{$this->balt1} {$this->malt1}"]=array('/'.App_Route::_getUrl('tModel').'/'.$this->model_sname.'.html',"зимняя резина {$this->balt1} {$this->malt}");
                $this->description="Купить зимние шины {$this->bname} {$this->mname} {$this->fullSize} ({$this->balt1}), автомобильные зимние шины, купить, цена, продажа, доставка по Москве и России";
                break;
            case 3:
                $this->sezIco='<img src="/app/images/sunsnow.png" title="Всесезонные шины" class="nttip">';
                $this->breadcrumbs['всесезонные шины']=array('/'.App_Route::_getUrl('tAllW').'.html','каталог всесезонной резины');
                $this->breadcrumbs["всесезонные шины {$this->balt1}"]=array('/'.App_Route::_getUrl('tAllW').'/'.$this->brand_sname.'.html',"всесезонная резина {$this->balt}");
                $this->breadcrumbs["{$this->balt1} {$this->malt1}"]=array('/'.App_Route::_getUrl('tModel').'/'.$this->model_sname.'.html',"всесезонная резина {$this->balt1} {$this->malt}");
                $this->description="Купить всесезонные шины {$this->bname} {$this->mname} {$this->fullSize} ({$this->balt1}), автомобильные всесезонные шины, купить, цена, продажа, доставка по Москве и России";
                break;
        }
        $this->breadcrumbs["{$this->bname} {$this->mname} {$this->fullSize}"]='';

        $this->keywords="шины {$this->bname} {$this->mname} {$this->fullSize} купить цена";

//
        if (!empty($this->cc->qrow['seo_title']))
            $this->title = $this->cc->qrow['seo_title'];
        if (!empty($this->cc->qrow['seo_keywords']))
            $this->keywords = $this->cc->qrow['seo_keywords'];
        if (!empty($this->cc->qrow['seo_description']))
            $this->description = $this->cc->qrow['seo_description'];

        $this->adv_text = $this->ss->parseText(Tools::unesc($this->cc->qrow['adv_text']));
//


        // галерея
        $gl=new CC_Gallery();
        $this->gallery = Tools::sortImagesBySize($gl->glist($this->model_id), 185);

        $this->deliveryCost=Data::get('delivery_cost');

        $this->deliveryCalcUrl='/i/dostavka.html';


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
            'P1'=>$this->radius,
            'P2'=>$this->height,
            'P3'=>$this->width,
            'P6'=>$this->cc->qrow['P6'],
            'M1'=>$this->sezonId,
            'M3'=>$this->shipId,
            'brand_id'=>(@Url::$sq['bid']&&@Url::$sq['bid']>0)?@Url::$sq['bid']:'',
            'gr'=>1,
            'start'=>	intval((@Url::$sq['page']?(Url::$sq['page']-1):0)*$this->tipoNum),
            'lines'=>	$this->tipoNum,
            'order'=>'cc_brand.name,cc_model.name,cc_cat.P7',
            'where'=>"cc_cat.cat_id!='{$this->cat_id}'".($this->hideTSCZero?" AND $this->minQtyRadiusSQL":'')
        ));
        $d=$this->cc->fetchAll();
        if($this->num) {
            $this->paginator=$this->cc->paginator(Url::$path,Url::$sq,@Url::$sq['page'],$this->num,$this->limit,'page',array(
                'active'=>	'<li class="active">{page}</li>',
                'noActive'=>'<li><a href="{url}">{page}</a></li>',
                'dots'=>	'<li>...</li>'
            ));
        }

        $burl='/'.App_Route::_getUrl('tTipo').'/';

        $this->cat=array();
        foreach($d as $v){
            $this->cat[]=$this->catRow($v,$burl);
        }

        // Фильтр по брендам
        $this->cc->cat_view(array(
            'P1'=>$this->radius,
            'P2'=>$this->height,
            'P3'=>$this->width,
            'P6'=>$this->svr,
            'M1'=>$this->sezonId,
            'M3'=>$this->shipId,
            'gr'=>1,
            'brand_id'=>'',
            'order'=>'cc_brand.name,cc_model.name,cc_cat.P7',
            'where'=>"cc_cat.cat_id!='{$this->cat_id}'".($this->hideDSCZero?" AND $this->minQtyRadiusSQL":'')
        ));
        $bfres=$this->cc->fetchAll();
        $this->alt_brands = Array();
        foreach($bfres as $v){
            $this->alt_brands[$v['brand_id']] = $v['bname'];
        }

        $this->rvws=$this->_reviewsList([
            'modelId'=>$this->model_id,
            'noList'=>true
        ]);

        /*
         * SEO ссылки
            Шины r16 +
            Шины 235 65 r16 +
            Шины Нокиан 235/65 R16  +
            Шины Nokian R16 +
            Каталог летней резины +
            Летняя резина радиус 16  +
            летние шины 235/65 R16  +
            летние шины Нокиан +
            летние шины Нокиан r16  +
            летние шины Нокиан 235/65 R16  +
            Шины для микроавтобусов и легких грузовиков +
            Шины Нокиан для микроавтобусов и легких грузовиков +
            Летние шины для микроавтобусов и легких грузовиков +
            Летние шины Нокиан для микроавтобусов и легких грузовиков +
         */
        $this->seoLinks=array();
        $this->seoLinks[]=array(
            'anc'=>"Шины R{$this->radius}",
            'title'=>"Резина R{$this->radius}",
            'url'=>'/'.App_Route::_getUrl('tSearch').".html?p1={$this->radius}"
        );
        $this->seoLinks[]=array(
            'anc'=>"Шины {$this->width}/{$this->height} R{$this->radius}",
            'title'=>"Резина {$this->width} {$this->height} r {$this->radius}",
            'url'=>'/'.App_Route::_getUrl('tSearch').".html?p3={$this->width}&p2={$this->height}&p1={$this->radius}"
        );
        $this->seoLinks[]=array(
            'anc'=>"Шины {$this->balt1} R{$this->radius}",
            'title'=>"купить резину {$this->balt1} r{$this->radius}",
            'url'=>'/'.App_Route::_getUrl('tSearch').".html?vendor={$this->brand_sname}&p1={$this->radius}"
        );
        $this->seoLinks[]=array(
            'anc'=>"Шины {$this->balt1} {$this->width}/{$this->height} R{$this->radius}",
            'title'=>"купить резину {$this->balt1} {$this->width} {$this->height} r {$this->radius}",
            'url'=>'/'.App_Route::_getUrl('tSearch').".html?vendor={$this->brand_sname}&p3={$this->width}&p2={$this->height}&p1={$this->radius}"
        );

        switch($this->sezonId){
            case 1:
                $this->seoLinks[]=array(
                    'anc'=>"Каталог летней резины",
                    'title'=>"купить летние шины",
                    'url'=>'/'.App_Route::_getUrl('tSummer').'.html'
                );
                $this->seoLinks[]=array(
                    'anc'=>"Летние шины R{$this->radius}",
                    'title'=>"купить летнюю резину  r{$this->radius}",
                    'url'=>'/'.App_Route::_getUrl('tSearch').".html?p1={$this->radius}&mp1=1"
                );
                $this->seoLinks[]=array(
                    'anc'=>"Летние шины {$this->balt1}",
                    'title'=>"купить летнюю резину {$this->balt1}",
                    'url'=>'/'.App_Route::_getUrl('tSummer').'/'.$this->brand_sname.'.html'
                );
                $this->seoLinks[]=array(
                    'anc'=>"Летние шины {$this->width}/{$this->height} R{$this->radius}",
                    'title'=>"купить летнюю резину {$this->width} {$this->height} r {$this->radius}",
                    'url'=>'/'.App_Route::_getUrl('tSearch').".html?p3={$this->width}&p2={$this->height}&p1={$this->radius}&mp1=1"
                );
                $this->seoLinks[]=array(
                    'anc'=>"Летние шины {$this->balt1} R{$this->radius}",
                    'title'=>"купить летние шины {$this->balt1} r {$this->radius}",
                    'url'=>'/'.App_Route::_getUrl('tSummer').'/'.$this->brand_sname.'/R'.$this->radius.'.html'
                );
                $this->seoLinks[]=array(
                    'anc'=>"Летние шины {$this->balt1} {$this->width}/{$this->height} R{$this->radius}",
                    'title'=>"купить летнюю резину {$this->balt1} {$this->width} {$this->height} r {$this->radius}",
                    'url'=>'/'.App_Route::_getUrl('tSearch').".html?vendor={$this->brand_sname}&p3={$this->width}&p2={$this->height}&p1={$this->radius}&mp1=1"
                );
                break;
            case 2:
                $this->seoLinks[]=array(
                    'anc'=>"Каталог зимней резины",
                    'title'=>"купить зимние шины",
                    'url'=>'/'.App_Route::_getUrl('tWinter').'.html'
                );
                $this->seoLinks[]=array(
                    'anc'=>"Зимние шины R{$this->radius}",
                    'title'=>"купить зимнюю резину  r{$this->radius}",
                    'url'=>'/'.App_Route::_getUrl('tSearch').".html?p1={$this->radius}&mp1=2"
                );
                $this->seoLinks[]=array(
                    'anc'=>"Зимние шины {$this->balt1}",
                    'title'=>"купить зимнюю резину {$this->balt1}",
                    'url'=>'/'.App_Route::_getUrl('tWinter').'/'.$this->brand_sname.'.html'
                );
                $this->seoLinks[]=array(
                    'anc'=>"Зимние шины {$this->width}/{$this->height} R{$this->radius}",
                    'title'=>"купить зимнюю резину {$this->width} {$this->height} r {$this->radius}",
                    'url'=>'/'.App_Route::_getUrl('tSearch').".html?p3={$this->width}&p2={$this->height}&p1={$this->radius}&mp1=2"
                );
                $this->seoLinks[]=array(
                    'anc'=>"Зимние шины {$this->balt1} R{$this->radius}",
                    'title'=>"купить зимнюю резину {$this->balt1} r {$this->radius}",
                    'url'=>'/'.App_Route::_getUrl('tWinter').'/'.$this->brand_sname.'/R'.$this->radius.'.html'
                );
                $this->seoLinks[]=array(
                    'anc'=>"Зимние шины {$this->balt1} {$this->width}/{$this->height} R{$this->radius}",
                    'title'=>"купить зимнюю резину {$this->balt1} {$this->width} {$this->height} r {$this->radius}",
                    'url'=>'/'.App_Route::_getUrl('tSearch').".html?vendor={$this->brand_sname}&p3={$this->width}&p2={$this->height}&p1={$this->radius}&mp1=2"
                );
                break;
            case 3:
                $this->seoLinks[]=array(
                    'anc'=>"Каталог вссезонной резины",
                    'title'=>"купить всесезонные шины",
                    'url'=>'/'.App_Route::_getUrl('tAllW').'.html'
                );
                $this->seoLinks[]=array(
                    'anc'=>"Всесезонные шины R{$this->radius}",
                    'title'=>"купить всесезонную резину  r{$this->radius}",
                    'url'=>'/'.App_Route::_getUrl('tSearch').".html?p1={$this->radius}&mp1=3"
                );
                $this->seoLinks[]=array(
                    'anc'=>"Летние шины {$this->balt1}",
                    'title'=>"купить всесезонную резину {$this->balt1}",
                    'url'=>'/'.App_Route::_getUrl('tAllW').'/'.$this->brand_sname.'.html'
                );
                $this->seoLinks[]=array(
                    'anc'=>"Всесезонные шины {$this->width}/{$this->height} R{$this->radius}",
                    'title'=>"купить всесезонную резину {$this->width} {$this->height} r {$this->radius}",
                    'url'=>'/'.App_Route::_getUrl('tSearch').".html?p3={$this->width}&p2={$this->height}&p1={$this->radius}&mp1=3"
                );
                $this->seoLinks[]=array(
                    'anc'=>"Всесезонные шины {$this->balt1} R{$this->radius}",
                    'title'=>"купить всесезонную резину {$this->balt1} r {$this->radius}",
                    'url'=>'/'.App_Route::_getUrl('tAllW').'/'.$this->brand_sname.'/R'.$this->radius.'.html'
                );
                $this->seoLinks[]=array(
                    'anc'=>"Всесезонные шины {$this->balt1} {$this->width}/{$this->height} R{$this->radius}",
                    'title'=>"купить всесезонную резину {$this->balt1} {$this->width} {$this->height} r {$this->radius}",
                    'url'=>'/'.App_Route::_getUrl('tSearch').".html?vendor={$this->brand_sname}&p3={$this->width}&p2={$this->height}&p1={$this->radius}&mp1=3"
                );
                break;
        }
        if($this->atId && isset($this->tAtRoute[$this->atId])){
            $this->seoLinks[]=array(
                'anc'=>"Резина {$this->atNames1[$this->atId]}",
                'title'=>"купить шины {$this->atNames1[$this->atId]}",
                'url'=>$this->tAtRoute[$this->atId].'.html'
            );
            $this->seoLinks[]=array(
                'anc'=>"Шины {$this->balt1} {$this->atNames1[$this->atId]}",
                'title'=>"купить летнюю резину {$this->balt1} {$this->atNames1[$this->atId]}",
                'url'=>$this->tAtRoute[$this->atId].'/'.$this->brand_sname.'.html'
            );
            if($this->sezonId){
                $this->seoLinks[]=array(
                    'anc'=>"{$this->sezonNames[$this->sezonId]} {$this->atNames1[$this->atId]}",
                    'title'=>"купить {$this->sezonNames[$this->sezonId]} {$this->atNames1[$this->atId]}",
                    'url'=>$this->tSezAtRoute[$this->sezonId.$this->atId].'.html'
                );
                $this->seoLinks[]=array(
                    'anc'=>"{$this->sezonNames[$this->sezonId]} {$this->balt1} {$this->atNames1[$this->atId]}",
                    'title'=>"купить {$this->sezonNames[$this->sezonId]} {$this->balt1} {$this->atNames1[$this->atId]}",
                    'url'=>$this->tSezAtRoute[$this->sezonId.$this->atId].'/'.$this->brand_sname.'.html'
                );

            }
        }


        $this->_sidebar();

        // ************* Применяемость *************
        $AB = new CC_AB();
        $this->suitable = $AB->getAvtoArrayByTipo(Array(
            'P1' => $this->radius,
            'P2' => $this->height,
            'P3' => $this->width,
        ), 1);
        // *****************************************
    }

    /*function axSearch()
    {
        // Поля REQUEST: bid,
        $bid = @$_REQUEST['bid'];
        @Url::$sq['bid'] = $bid;

        // тип отображения результатов поиска
        if(@$_COOKIE['stype']=='lenta') {
            $this->altTpl='catalog/tyres/searchLentaTable';
            $this->altViewMode=[
                'setBlockMode',
                'active'
            ];
        } else {
            $this->altTpl='catalog/tyres/searchBlockTable';
            $this->altViewMode=[
                'active',
                'setLentaMode'
            ];
        }

        if(empty($this->cat_id)) $this->cc->que('cat_by_sname',Url::$spath[2],1,'','',1); else $this->cc->que('cat_by_id',$this->cat_id);
        if(!$this->cc->qnum()) return App_Route::redir404();
        $this->cc->next();
        $this->width=$this->cc->qrow['P3'];
        $this->height=$this->cc->qrow['P2'];
        $this->radius=$this->cc->qrow['P1'];
        $this->sezonId=$this->cc->qrow['MP1'];
        $this->shipId=$this->cc->qrow['MP3'];
        $this->atId=$this->cc->qrow['MP2'];
        $this->fullSize=$this->size.' '.$this->inis.' '.$this->suffix;

        //альтернативные размеры
        $this->tipoNum= 20;
        $this->cat=array();
        $this->num=$this->cc->cat_view(array(
            'P1'=>$this->radius,
            'P2'=>$this->height,
            'P3'=>$this->width,
            'P6'=>$this->cc->qrow['P6'],
            'M1'=>$this->sezonId,
            'M3'=>$this->shipId,
            'gr'=>1,
            'brand_id'=> ($bid > 0) ? $bid : '',
            'order'=>'cc_brand.name,cc_model.name,cc_cat.P7',
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

        $burl='/'.App_Route::_getUrl('tTipo').'/';

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

                    <? if(!empty($this->paginator)){
                        ?><div class="paginator" style="float: left"><?
                        ?><ul><?
                        foreach($this->paginator as $v) echo $v;
                        ?></ul><?
                        ?></div><?
                    }?>

                    <div class="vids">

                        <? foreach($this->altViewMode as $v){
                            ?><a href="#" class="<?=$v?>"></a><?
                        }?>

                    </div>
                </div>

            </div>


            <div class="">

                <?
                if(class_exists('App_App')) {
                    $app = new App_App();
                    echo $app->incView($this->altTpl, false, Array('cat' => $this->cat));
                }
                ?>

                <div class="box-padding">

                    <div class="vids">

                        <? foreach($this->altViewMode as $v){
                            ?><a href="#" class="<?=$v?>"></a><?
                        }?>

                    </div>

                    <? if(!empty($this->paginator)){
                        ?><div class="paginator" style="float: left"><?
                        ?><ul><?
                        foreach($this->paginator as $v) echo $v;
                        ?></ul><?
                        ?></div><?
                    }?>

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
            $this->altTpl='catalog/tyres/searchLentaTypo';
            $this->altViewMode=[
                'setBlockMode',
                'active'
            ];
        } else {
            $this->altTpl='catalog/tyres/searchBlockTypo';
            $this->altViewMode=[
                'active',
                'setLentaMode'
            ];
        }

        if(empty($this->cat_id)) $this->cc->que('cat_by_sname',Url::$spath[2],1,'','',1); else $this->cc->que('cat_by_id',$this->cat_id);
        if(!$this->cc->qnum()) return App_Route::redir404();
        $this->cc->next();
        $this->width=$this->cc->qrow['P3'];
        $this->height=$this->cc->qrow['P2'];
        $this->radius=$this->cc->qrow['P1'];
        $this->sezonId=$this->cc->qrow['MP1'];
        $this->shipId=$this->cc->qrow['MP3'];
        $this->atId=$this->cc->qrow['MP2'];
        $this->fullSize=$this->size.' '.$this->inis.' '.$this->suffix;
        $cc = new CC_Ctrl();
        $this->ex_num=$cc->cat_view(array(
            'P1'=>$this->radius,
            'P2'=>$this->height,
            'P3'=>$this->width,
            'P6'=>$this->cc->qrow['P6'],
            'M1'=>$this->sezonId,
            'M3'=>$this->shipId,
            'gr'=>1,
            'brand_id'=> ($bid > 0) ? $bid : '',
            'order'=>'cc_brand.name,cc_model.name,cc_cat.P7',
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

        //$this->tipoNum= 20;
        $this->cat=array();
        $this->num=$this->cc->cat_view(array(
            'P1'=>$this->radius,
            'P2'=>$this->height,
            'P3'=>$this->width,
            'P6'=>$this->cc->qrow['P6'],
            'M1'=>$this->sezonId,
            'M3'=>$this->shipId,
            'gr'=>1,
            'brand_id'=> ($bid > 0) ? $bid : '',
            'order'=>'cc_brand.name,cc_model.name,cc_cat.P7',
            'start'=>max(0,$this->page*$this->limit-$this->limit),
            'lines'=>$this->limit,
            'where'=>"cc_cat.cat_id!='{$this->cat_id}'".($this->hideDSCZero?" AND $this->minQtyRadiusSQL":'')
        ));
        $d=$this->cc->fetchAll();
        if($this->num) {
            $this->paginator=$this->cc->paginator(Url::$path, Url::$sq, @Url::$sq['page'],$this->num,$this->limit,'page',array(
                'active'=>	'<li class="active">{page}</li>',
                'noActive'=>'<li><a href="{url}">{page}</a></li>',
                'dots'=>	'<li>...</li>'
            ));
        }

        $burl='/'.App_Route::_getUrl('tTipo').'/';

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