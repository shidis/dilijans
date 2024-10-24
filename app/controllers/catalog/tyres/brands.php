<?
class App_Catalog_Tyres_Brands_Controller extends App_Common_Controller {
    public $yandex_social_share = '
        <div style="margin-right: 270px;">
            <script type="text/javascript" src="//yastatic.net/es5-shims/0.0.2/es5-shims.min.js" charset="utf-8"></script>
            <script type="text/javascript" src="//yastatic.net/share2/share.js" charset="utf-8"></script>
            <div class="ya-share2" data-services="vkontakte,facebook,odnoklassniki,gplus" data-counter=""></div>
        </div>
    ';

    //бренды шин
    public function index() {

        $this->view('catalog/tyres/brands');

        $this->title='Автомобильные шины | каталог и цены на легковые автошины';
        $this->_title='Автомобильные шины';

        $this->breadcrumbs[]='шины';

        $this->h=$this->cc->maxImgBH(1,1);
        $this->doubleDimension=false;

        $this->brands=array();
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
            'having'=>'modelsNum>0'
        ));
        $d=$this->cc->fetchAll('',MYSQLI_ASSOC);
        foreach($d as $v){
            $this->brands[$v['is_popular']][]=array(
                'img1'=>$this->cc->make_img_path($v['img1']),
                'url'=>$this->tRoute[0].'/'.Tools::unesc($v['sname']).'.html',
                'title'=>'Купить шины '.($v['alt']!=''?$v['alt']:$v['name']),
                'alt'=>'шины '.Tools::html($v['name']),
                'name'=>Tools::unesc($v['name'])
            );
        }

        $this->filter=array(
            'P3'=>$this->cc->s_arr['P3_1'],
            'P2'=>$this->cc->s_arr['P2_1'],
            'P1'=>$this->cc->s_arr['P1_1']
        );

        $this->filterHF=array();

        // быстрый выбор бренда
        $r=$this->cc->brands(array(
            'gr'=>1,
            'qSelect'=>array(
                'modelsNum'=>array()
            ),
            'select'=>array(
                'cc_brand.name'=>'name',
                'cc_brand.sname'=>'sname'
            ),
            'having'=>'modelsNum>0'
        ));

        $this->qbrands=array(0=>array());
        foreach($d as $v){
            $this->qbrands[0][]=array(
                'name'=>Tools::unesc($v['name']),
                'sname'=>$this->tRoute[0].'/'.Tools::unesc($v['sname']).'.html'
            );
        }

        // ссылки по радиусам
        $this->rlinks=array();
        if(!empty($this->filter['P1'])){
            $burl='/'.App_Route::_getUrl('tSearch').'.html?p1=';
            foreach($this->filter['P1'] as $v){
                $this->rlinks[]=array(
                    'url'=>$burl.$v,
                    'title'=>'купить шины r'.$v,
                    'anc'=>"Шины R{$v}"
                );
            }
        }

        // фильтры
        $this->lf=array();
        $this->lfi=0;

        $this->lf['mp3']=array();
        $si=App_Route::_getUrl('tSearch').'?';
        $this->lf['mp3'][1]=array(
            'chk'=>false,
            'anc'=>'Шип <img src="/app/images/ship.png" alt="шипованные шины">',
            'id'=>'_mp31',
            'url'=>$si.'mp3=1'
        );
        $this->lf['mp3'][0]=array(
            'chk'=>false,
            'anc'=>"Нешип",
            'id'=>'_mp30',
            'url'=>$si.'mp3=0'
        );

        $this->lf['mp1']=array();
        $this->lf['mp1'][2]=array(
            'chk'=>false,
            'anc'=>@$this->sezonNames5[2].$this->sezonIcos[2],
            'id'=>'_mp12',
            'url'=>$si.'mp1=2'
        );
        $this->lf['mp1'][1]=array(
            'chk'=>false,
            'anc'=>@$this->sezonNames5[1].$this->sezonIcos[1],
            'id'=>'_mp11',
            'url'=>$si.'mp1=1'
        );
        $this->lf['mp1'][3]=array(
            'chk'=>false,
            'anc'=>@$this->sezonNames5[3].$this->sezonIcos[3],
            'id'=>'_mp13',
            'url'=>$si.'mp1=3'
        );

    }

    //бренды шин с разбивкой по сезонам - не используется
    public function splitBySezon() {

        $this->view('catalog/tyres/brands');

        $this->title='Автомобильные шины | каталог и цены на легковые автошины';
        $this->_title='Автомобильные шины';

        $this->breadcrumbs[]='шины';

        $this->h=$this->cc->maxImgBH(1,1);

        $this->doubleDimension=true;
        $sezOrder=Data::get('ccSezonOrder');
        if($sezOrder==1){
            // сначала зиму
            $this->brands=array(2=>array(),3=>array(),1=>array());
        }elseif($sezOrder==2){
            // сначала лето
            $this->brands=array(1=>array(),3=>array(),2=>array());
        }else {
            $sezOrder=0;
            $this->brands=array(1=>array(),2=>array(),3=>array());
        }

        $this->h2=array(
            1=>array(
                'img'=>"/app/images/title-sez1.png",
                'title'=>'Летняя резина',
                'url'=>$this->tRoute[1].'.html'
            ),
            2=>array(
                'img'=>"/app/images/title-sez2.png",
                'title'=>'Зимняя резина',
                'url'=>$this->tRoute[2].'.html'
            ),
            3=>array(
                'img'=>"/app/images/title-sez3.png",
                'title'=>'Всесезонная резина',
                'url'=>$this->tRoute[3].'.html'
            )
        );

        for($i=1;$i<=3;$i++){
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
                    'cc_brand.img1'=>'img1'
                ),
                'whereModel'=>"cc_model.P1=$i",
                'whereCat'=>$this->minQtyRadiusSQL,
                'having'=>'modelsNum>0'
            ));
            $d=$this->cc->fetchAll('',MYSQLI_ASSOC);
            foreach($d as $v){
                $this->brands[$i][]=array(
                    'img1'=>$this->cc->make_img_path($v['img1']),
                    'url'=>$this->tRoute[$i].'/'.Tools::unesc($v['sname']).'.html',
                    'title'=>'Купить шины '.$this->sezonNames4[$i].' '.($v['alt']!=''?$v['alt']:$v['name']),
                    'alt'=>$this->sezonNames4[$i].' шины '.Tools::html($v['name']),
                    'name'=>Tools::unesc($v['name'])
                );
            }
        }

        // для фильтров
        $this->filter=array(
            'P3'=>$this->cc->s_arr['P3_1'],
            'P2'=>$this->cc->s_arr['P2_1'],
            'P1'=>$this->cc->s_arr['P1_1']
        );

        $this->filterHF=array();

        // быстрый выбор бренда
        $r=$this->cc->brands(array(
            'gr'=>1,
            'qSelect'=>array(
                'modelsNum'=>array()
            ),
            'select'=>array(
                'cc_brand.name'=>'name',
                'cc_brand.sname'=>'sname'
            ),
            'having'=>'modelsNum>0'
        ));

        $this->qbrands=array(0=>array());
        foreach($d as $v){
            $this->qbrands[0][]=array(
                'name'=>Tools::unesc($v['name']),
                'sname'=>$this->tRoute[0].'/'.Tools::unesc($v['sname']).'.html'
            );
        }

        // ссылки по радиусам
        $this->rlinks=array();
        if(!empty($this->filter['P1'])){
            $burl='/'.App_Route::_getUrl('tSearch').'.html?p1=';
            foreach($this->filter['P1'] as $v){
                $this->rlinks[]=array(
                    'url'=>$burl.$v,
                    'title'=>'купить шины r'.$v,
                    'anc'=>"Шины R{$v}"
                );
            }
        }

        // фильтры
        $this->lf=array();
        $this->lfi=0;

        $this->lf['mp3']=array();
        $si=App_Route::_getUrl('tSearch').'?';
        $this->lf['mp3'][1]=array(
            'chk'=>false,
            'anc'=>'Шип <img src="/app/images/ship.png" alt="шипованные шины">',
            'id'=>'_mp31',
            'url'=>$si.'mp3=1'
        );
        $this->lf['mp3'][0]=array(
            'chk'=>false,
            'anc'=>"Нешип",
            'id'=>'_mp30',
            'url'=>$si.'mp3=0'
        );

        $this->lf['mp1']=array();
        $this->lf['mp1'][2]=array(
            'chk'=>false,
            'anc'=>@$this->sezonNames5[2].$this->sezonIcos[2],
            'id'=>'_mp12',
            'url'=>$si.'mp1=2'
        );
        $this->lf['mp1'][1]=array(
            'chk'=>false,
            'anc'=>@$this->sezonNames5[1].$this->sezonIcos[1],
            'id'=>'_mp11',
            'url'=>$si.'mp1=1'
        );
        $this->lf['mp1'][3]=array(
            'chk'=>false,
            'anc'=>@$this->sezonNames5[3].$this->sezonIcos[3],
            'id'=>'_mp13',
            'url'=>$si.'mp1=3'
        );

    }

    // бренды по сезону
    public function bySezon() {

        if(!in_array(App_Route::$param['M1'],array(1,2,3))) return App_Route::redir404();

        $this->view('catalog/tyres/brands');

        $this->title="{$this->sezonNames[App_Route::$param['M1']]} для внедорожников. Купить внедорожную резину для джипов. Шипованые шины и липучка по низким ценам.";

        $this->_title='Каталог '.$this->sezonNames6[App_Route::$param['M1']].' шин';

        $this->description="";

        $this->breadcrumbs['шины']='/'.App_Route::_getUrl('tCat').'.html';
        $this->breadcrumbs[]=$this->sezonNames4[App_Route::$param['M1']].' шины';

        $this->h=$this->cc->maxImgBH(1,1);

        if(App_Route::$param['M1']==2 && 0) {// отключили разбиение
            $this->doubleDimension=true;
            $this->brands=array(0=>array(),1=>array());
            $this->h2=array(
                0=>array(
                    'img'=>"/app/images/img-nav-filter-05.png",
                    'title'=>'Зимняя нешипованая резина (липучка)',
                    'url'=>$this->tRoute['20'].'.html'
                ),
                1=>array(
                    'img'=>"/app/images/title-ship.png",
                    'title'=>'Зимняя шипованая резина',
                    'url'=>$this->tRoute['21'].'.html'
                )
            );
        } else {
            $this->doubleDimension=false;
            $this->brands=array();
        }

        // зима
        if($this->doubleDimension){
            for($i=0;$i<=1;$i++){
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
                        'cc_brand.img1'=>'img1'
                    ),
                    'whereModel'=>'cc_model.P1='.App_Route::$param['M1']." AND cc_model.P3=$i",
                    'whereCat'=>$this->minQtyRadiusSQL,
                    'having'=>'modelsNum>0'
                ));
                $d=$this->cc->fetchAll('',MYSQLI_ASSOC);
                foreach($d as $v){
                    $this->brands[$i][]=array(
                        'img1'=>$this->cc->make_img_path($v['img1']),
                        'url'=>$this->tRoute[App_Route::$param['M1'].''.$i].'/'.Tools::unesc($v['sname']).'.html',
                        'title'=>$this->sezonNames5[App_Route::$param['M1']].($i==1?' шипованные':'').' шины '.($v['alt']!=''?$v['alt']:$v['name']),
                        'alt'=>$this->sezonNames5[App_Route::$param['M1']].($i==1?' шипованные':'').' шины '.Tools::html($v['name']),
                        'name'=>Tools::unesc($v['name'])
                    );

                }
            }
        }else{
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
                'whereModel'=>'cc_model.P1='.App_Route::$param['M1'],
                'whereCat'=>$this->minQtyRadiusSQL,
                'having'=>'modelsNum>0'
            ));
            $d=$this->cc->fetchAll('',MYSQLI_ASSOC);
            foreach($d as $v){
                $this->brands[$v['is_popular']][]=array(
                    'img1'=>$this->cc->make_img_path($v['img1']),
                    'url'=>$this->tRoute[App_Route::$param['M1']].'/'.Tools::unesc($v['sname']).'.html',
                    'title'=>'Купить '.$this->sezonNames4[App_Route::$param['M1']].' шины '.($v['alt']!=''?$v['alt']:$v['name']),
                    'alt'=>$this->sezonNames5[App_Route::$param['M1']].' шины '.Tools::html($v['name']),
                    'name'=>Tools::unesc($v['name'])
                );
            }
        }


        // фильтры
        $this->cc->cat(array(
            'gr'=>1,
            'M1'=>App_Route::$param['M1'],
            'where'=>$this->minQtyRadiusSQL,
            'fields'=>"cc_model.model_id,cc_cat.P1+'0' AS P1,cc_cat.P2+'0' AS P2,cc_cat.P3+'0' AS P3,cc_model.P1 AS MP1,cc_model.P3 AS MP3",
            'ex'=>1,
            'nolimits'=>1,
            'exFields'=>array('P1'=>array(),'P2'=>array(),'P3'=>array(),'MP3'=>array())
        ));

        ksort($this->cc->ex_arr['P1']);
        ksort($this->cc->ex_arr['P2']);
        ksort($this->cc->ex_arr['P3']);
        ksort($this->cc->ex_arr['MP3']);
        unset($this->cc->ex_arr['P1'][0],$this->cc->ex_arr['P2'][0],$this->cc->ex_arr['P3'][0]);
        $this->filter=array(
            'P1'=>array_keys($this->cc->ex_arr['P1']),
            'P2'=>array_keys($this->cc->ex_arr['P2']),
            'P3'=>array_keys($this->cc->ex_arr['P3'])
        );

        $this->filterHF=array(
            'mp1'=>App_Route::$param['M1']
        );

        $this->qbrands=array(0=>array());
        foreach($d as $v){
            $this->qbrands[0][]=array(
                'name'=>Tools::unesc($v['name']),
                'sname'=>$this->tRoute[App_Route::$param['M1']].'/'.Tools::unesc($v['sname']).'.html'
            );
        }

        // ссылки по радиусам
        $this->rlinks=array();
        if(!empty($this->filter['P1'])){
            $burl='/'.App_Route::_getUrl('tSearch').'.html?p1=';
            foreach($this->filter['P1'] as $v){
                $this->rlinks[]=array(
                    'url'=>$burl.$v.'&mp1='.App_Route::$param['M1'],
                    'title'=>'купить шины r'.$v,
                    'anc'=>"Шины R{$v}"
                );
            }
        }

        // фильтры
        $this->lf=array();
        $this->lfi=0;

        $this->lf['mp3']=array();
        $si=App_Route::_getUrl('tSearch').'?';
        if(count(@$this->cc->ex_arr['MP3'])>1){
            $this->lf['mp3'][1]=array(
                'chk'=>false,
                'anc'=>'Шип <img src="/app/images/ship.png" alt="шипованные шины">',
                'id'=>'_mp31',
                'url'=>$si.'mp3=1'
            );
            $this->lf['mp3'][0]=array(
                'chk'=>false,
                'anc'=>"Нешип",
                'id'=>'_mp30',
                'url'=>$si.'mp3=0'
            );
        }
    }

    // бренды зимние шипы/нешипы
    public function byShip() {

        if(!in_array(App_Route::$param['M1'],array(2)) || !in_array(App_Route::$param['M3'],array(0,1))) return App_Route::redir404();

        $this->view('catalog/tyres/brands');

        $this->title='Зимняя '.(App_Route::$param['M3']?'шипованная':'нешипованная').', выбор зимних'.(App_Route::$param['M3']?'шипованных':'нешипованных').' шин';
        $this->_title='Каталог зимних '.(App_Route::$param['M3']?'шипованных':'нешипованных').' шин';

        $this->breadcrumbs['шины']='/'.App_Route::_getUrl('tCat').'.html';
        $this->breadcrumbs['зимние шины']=$this->tRoute[2].'.html';
        $this->breadcrumbs[]='зимние '.(App_Route::$param['M3']?'шипованные':'нешипованные').' шины';

        $this->h=$this->cc->maxImgBH(1,1);


        $this->doubleDimension=false;
        $this->brands=array();

        // зима

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
                'cc_brand.is_popular'=>'is_popular',
            ),
            'whereModel'=>"cc_model.P1=2 AND cc_model.P3=".App_Route::$param['M3'],
            'whereCat'=>$this->minQtyRadiusSQL,
            'having'=>'modelsNum>0'
        ));
        $d=$this->cc->fetchAll('',MYSQLI_ASSOC);
        foreach($d as $v){
            $this->brands[$v['is_popular']][]=array(
                'img1'=>$this->cc->make_img_path($v['img1']),
                'url'=>$this->tRoute['2'.App_Route::$param['M3']].'/'.Tools::unesc($v['sname']).'.html',
                'title'=>'Купить зимние '.(App_Route::$param['M3']?'шипованные':'нешипованные').' шины '.($v['alt']!=''?$v['alt']:$v['name']),
                'alt'=>'Зимняя '.(App_Route::$param['M3']?'шипованная':'нешипованная').' резина '.Tools::html($v['name']),
                'name'=>Tools::unesc($v['name'])
            );
        }


        // фильтры
        $this->cc->cat(array(
            'gr'=>1,
            'M1'=>App_Route::$param['M1'],
            'where'=>$this->minQtyRadiusSQL,
            'fields'=>"cc_model.model_id,cc_cat.P1+'0' AS P1,cc_cat.P2+'0' AS P2,cc_cat.P3+'0' AS P3,cc_model.P1 AS MP1,cc_model.P3 AS MP3",
            'ex'=>1,
            'nolimits'=>1,
            'exFields'=>array('P1'=>array(),'P2'=>array(),'P3'=>array())
        ));

        ksort($this->cc->ex_arr['P1']);
        ksort($this->cc->ex_arr['P2']);
        ksort($this->cc->ex_arr['P3']);
        unset($this->cc->ex_arr['P1'][0],$this->cc->ex_arr['P2'][0],$this->cc->ex_arr['P3'][0]);
        $this->filter=array(
            'P1'=>array_keys($this->cc->ex_arr['P1']),
            'P2'=>array_keys($this->cc->ex_arr['P2']),
            'P3'=>array_keys($this->cc->ex_arr['P3'])
        );

        $this->filterHF=array(
            'mp1'=>App_Route::$param['M1'],
            'mp3'=>App_Route::$param['M3']
        );

        $this->qbrands=array(0=>array());
        foreach($d as $v){
            $this->qbrands[0][]=array(
                'name'=>Tools::unesc($v['name']),
                'sname'=>$this->tRoute['2'.App_Route::$param['M3']].'/'.Tools::unesc($v['sname']).'.html'
            );
        }

        // ссылки по радиусам
        $this->rlinks=array();
        if(!empty($this->filter['P1'])){
            $burl='/'.App_Route::_getUrl('tSearch').'.html?p1=';
            foreach($this->filter['P1'] as $v){
                $this->rlinks[]=array(
                    'url'=>$burl.$v.'&mp1=2&mp3='.App_Route::$param['M3'],
                    'title'=>'купить зимние '.(App_Route::$param['M3']?'шипованные':'нешипованные').' шины r'.$v,
                    'anc'=>"R{$v}"
                );
            }
        }

        // фильтры
        $this->lf=array();
        $this->lfi=0;
    }

    //бренды шин для внедор.
    public function bySUV() {

        $this->view('catalog/tyres/brands');

        $this->title='Шины для внедорожников. Купить внедорожную резину для джипов. Летние и зимние шины по низким ценам.';
        $this->_title='Шины для внедорожников. Купить грязевую резину для джипа.';
        $this->description="Каталог внедорожных шин для джипов. Большой выбор грязевых шин по низким ценам в интернет магазине Dilijans. Мы доставим шины для вашего джипа в любую точку Москвы и город России.";

        $this->breadcrumbs['шины']='/'.App_Route::_getUrl('tCat').'.html';
        $this->breadcrumbs[]='для внедорожников';

        $this->h=$this->cc->maxImgBH(1,1);

        $this->doubleDimension=true;
        $sezOrder=Data::get('ccSezonOrder');
        if($sezOrder==1){
            // сначала зиму
            $this->brands=array(2=>array(),3=>array(),1=>array());
        }elseif($sezOrder==2){
            // сначала лето
            $this->brands=array(1=>array(),3=>array(),2=>array());
        }else {
            $sezOrder=0;
            $this->brands=array(1=>array(),2=>array(),3=>array());
        }

        $this->h2=array(
            1=>array(
                'img'=>"/app/images/title-sez1.png",
                'title'=>'Летняя резина для внедорожников',
                'url'=>$this->tSezAtRoute['12'].'.html'
            ),
            2=>array(
                'img'=>"/app/images/title-sez2.png",
                'title'=>'Зимняя резина для внедорожников',
                'url'=>$this->tSezAtRoute['22'].'.html'
            ),
            3=>array(
                'img'=>"/app/images/title-sez3.png",
                'title'=>'Всесезонная резина для внедорожников',
                'url'=>$this->tSezAtRoute['32'].'.html'
            )
        );

        for($i=1;$i<=3;$i++){
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
                    'cc_brand.img1'=>'img1'
                ),
                'whereModel'=>'cc_model.P2=2 AND cc_model.P1='.$i,
                'whereCat'=>$this->minQtyRadiusSQL,
                'having'=>'modelsNum>0'
            ));
            $d=$this->cc->fetchAll('',MYSQLI_ASSOC);
            foreach($d as $v){
                $this->brands[$i][]=array(
                    'img1'=>$this->cc->make_img_path($v['img1']),
                    'url'=>$this->tSezAtRoute[$i.'2'].'/'.Tools::unesc($v['sname']).'.html',
                    'title'=>'Купить '.$this->sezonNames4[$i].' шины '.($v['alt']!=''?$v['alt']:$v['name']).' для внедорожников',
                    'alt'=>$this->sezonNames4[$i].' шины внедорожные '.Tools::html($v['name']),
                    'name'=>Tools::unesc($v['name'])
                );
            }

        }

        // фильтр
        $this->cc->cat(array(
            'gr'=>1,
            'M2'=>2,
            'where'=>$this->minQtyRadiusSQL,
            'fields'=>"cc_model.model_id,cc_cat.P1+'0' AS P1,cc_cat.P2+'0' AS P2,cc_cat.P3+'0' AS P3,cc_model.P1 AS MP1,cc_model.P3 AS MP3",
            'ex'=>1,
            'nolimits'=>1,
            'exFields'=>array('P1'=>array(),'P2'=>array(),'P3'=>array(),'MP1'=>array(),'MP3'=>array())
        ));
        ksort($this->cc->ex_arr['P1']);
        ksort($this->cc->ex_arr['P2']);
        ksort($this->cc->ex_arr['P3']);
        ksort($this->cc->ex_arr['MP1']);
        ksort($this->cc->ex_arr['MP3']);
        unset($this->cc->ex_arr['P1'][0],$this->cc->ex_arr['P2'][0],$this->cc->ex_arr['P3'][0],$this->cc->ex_arr['MP1'][0]);
        $this->filter=array(
            'P1'=>array_keys($this->cc->ex_arr['P1']),
            'P2'=>array_keys($this->cc->ex_arr['P2']),
            'P3'=>array_keys($this->cc->ex_arr['P3'])
        );

        $this->filterHF=array(
            'at'=>2
        );


        $this->qbrands=array(0=>array());
        foreach($d as $v){
            $this->qbrands[0][]=array(
                'name'=>Tools::unesc($v['name']),
                'sname'=>'/'.App_Route::_getUrl('tSUV').'/'.Tools::unesc($v['sname']).'.html'
            );
        }

        // ссылки по радиусам
        $this->rlinks=array();
        if(!empty($this->filter['P1'])){
            $burl='/'.App_Route::_getUrl('tSearch').'.html?p1=';
            foreach($this->filter['P1'] as $v){
                $this->rlinks[]=array(
                    'url'=>$burl.$v.'&at=2',
                    'title'=>'купить шины r'.$v,
                    'anc'=>"Шины R{$v}"
                );
            }
        }
        // фильтры
        $this->lf=array();
        $this->lfi=0;

        $this->lf['mp3']=array();
        $si=App_Route::_getUrl('tSearch').'?';
        if(count(@$this->cc->ex_arr['MP3'])>1){
            $this->lf['mp3'][1]=array(
                'chk'=>false,
                'anc'=>'Шип <img src="/app/images/ship.png" alt="шипованные шины">',
                'id'=>'_mp31',
                'url'=>$si.'mp3=1'
            );
            $this->lf['mp3'][0]=array(
                'chk'=>false,
                'anc'=>"Нешип",
                'id'=>'_mp30',
                'url'=>$si.'mp3=0'
            );
        }
        if(count(@$this->cc->ex_arr['MP1'])>1){
            $this->lf['mp1']=array();
            if(!empty($this->cc->ex_arr['MP1'][2]))
                $this->lf['mp1'][2]=array(
                    'chk'=>false,
                    'anc'=>@$this->sezonNames5[2].$this->sezonIcos[2],
                    'id'=>'_mp12',
                    'url'=>$si.'mp1=2'
                );
            if(!empty($this->cc->ex_arr['MP1'][1]))
                $this->lf['mp1'][1]=array(
                    'chk'=>false,
                    'anc'=>@$this->sezonNames5[1].$this->sezonIcos[1],
                    'id'=>'_mp11',
                    'url'=>$si.'mp1=1'
                );
            if(!empty($this->cc->ex_arr['MP1'][3]))
                $this->lf['mp1'][3]=array(
                    'chk'=>false,
                    'anc'=>@$this->sezonNames5[3].$this->sezonIcos[3],
                    'id'=>'_mp13',
                    'url'=>$si.'mp1=3'
                );
        }
    }

    // бренды для внедорож. и сезону
    public function bySezonSUV()
    {

        if(!in_array(App_Route::$param['M1'],array(1,2,3))) return App_Route::redir404();

        $this->view('catalog/tyres/brands');

        $sezonId=App_Route::$param['M1'];

        if($sezonId==2){
            $this->title="Зимние шины для внедорожников. Купить внедорожную резину для джипов. Шипованые шины и липучка по низким ценам.";
            $this->_title="Зимние шины для внедорожников. Купить зимнюю  резину для джипа.";

            $this->description="Большой выбор зимних шин для внедорожников. Вы можете купить зимнюю резину для вашего джипа по привликательной цене в интернет магазине шин и дисков Dilijans. Шипованые шины и липцчки на выбор. Доставка по москве и России";

        }else{
            $this->title="{$this->sezonNames5[$sezonId]} шины для внедорожников. Купить внедорожную резину для джипов. {$this->sezonNames5[$sezonId]} грязевые шины по низким ценам.";
            $this->_title="{$this->sezonNames5[$sezonId]} шины для внедорожников. Купить {$this->sezonNames8[$sezonId]} грязевую резину для джипа.";

            $this->description="Большой выбор {$this->sezonNames6[$sezonId]} шин для внедорожников. Вы можете купить {$this->sezonNames8[$sezonId]} резину для вашего джипа по привликательной цене в интернет магазине шин и дисков Dilijans. Грязевые {$this->sezonNames9[$sezonId]} шины на выбор. Доставка по москве и России";
        }

        $this->breadcrumbs['шины']='/'.App_Route::_getUrl('tCat').'.html';
        $this->breadcrumbs['для внедорожников']='/'.App_Route::_getUrl('tSUV').'.html';
        $this->breadcrumbs[]=$this->sezonNames4[App_Route::$param['M1']];

        $this->h=$this->cc->maxImgBH(1,1);

        $burl=array(
            1=>'/'.App_Route::_getUrl('tSummerSUV').'/',
            2=>'/'.App_Route::_getUrl('tWinterSUV').'/',
            3=>'/'.App_Route::_getUrl('tAllWSUV').'/'
        );

        if($sezonId==2) {
            $this->doubleDimension=true;
            $this->brands=array(0=>array(),1=>array());
            $this->h2=array(
                0=>array(
                    'img'=>"/app/images/img-nav-filter-05.png",
                    'title'=>'Зимняя нешипованая резина (липучка) для внедорожников',
                    'url'=>''
                ),
                1=>array(
                    'img'=>"/app/images/title-ship.png",
                    'title'=>'Зимняя шипованая резина для внедорожников',
                    'url'=>''
                )
            );

            for($i=0;$i<=1;$i++){
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
                        'cc_brand.img1'=>'img1'
                    ),
                    'whereModel'=>array(
                        'cc_model.P1='.$sezonId,
                        'cc_model.P2=2',
                        'cc_model.P3='.$i
                    ),
                    'whereCat'=>$this->minQtyRadiusSQL,
                    'having'=>'modelsNum>0'
                ));
                $d=$this->cc->fetchAll('',MYSQLI_ASSOC);
                $burlShip=array(
                    0=>'/'.App_Route::_getUrl('tSUVNeShip').'/',
                    1=>'/'.App_Route::_getUrl('tSUVShip').'/'
                );
                foreach($d as $v){
                    $this->brands[$i][]=array(
                        'img1'=>$this->cc->make_img_path($v['img1']),
                        'url'=>$burlShip[$i].Tools::unesc($v['sname']).'.html',
                        'title'=>'Купить зимние '.($i?'шипованные':'нешипованные').' шины для внедорожников '.($v['alt']!=''?$v['alt']:$v['name']),
                        'alt'=>'Зимние '.($i?'шипованные':'нешипованные').' шины для внедорожников '.Tools::html($v['name']),
                        'name'=>Tools::unesc($v['name'])
                    );
                }
            }

        } else {
            $this->doubleDimension=false;
            $this->brands=array();
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
                        'cc_brand.is_popular'=>'is_popular',
                    ),
                    'whereModel'=>array(
                        'cc_model.P1='.$sezonId,
                        'cc_model.P2=2'
                    ),
                    'whereCat'=>$this->minQtyRadiusSQL,
                    'having'=>'modelsNum>0'
                ));
                $d=$this->cc->fetchAll('',MYSQLI_ASSOC);
                foreach($d as $v){
                    $this->brands[$v['is_popular']][]=array(
                        'img1'=>$this->cc->make_img_path($v['img1']),
                        'url'=>$burl[App_Route::$param['M1']].Tools::unesc($v['sname']).'.html',
                        'title'=>'Купить '.$this->sezonNames4[App_Route::$param['M1']].' шины для внедорожников '.($v['alt']!=''?$v['alt']:$v['name']),
                        'alt'=>$this->sezonNames5[App_Route::$param['M1']].' шины для внедорожников '.Tools::html($v['name']),
                        'name'=>Tools::unesc($v['name'])
                    );
                }
        }

        $this->cc->cat(array(
            'gr'=>1,
            'M1'=>$sezonId,
            'M2'=>2,
            'where'=>$this->minQtyRadiusSQL,
            'fields'=>"cc_model.model_id,cc_cat.P1+'0' AS P1,cc_cat.P2+'0' AS P2,cc_cat.P3+'0' AS P3,cc_model.P1 AS MP1,cc_model.P3 AS MP3",
            'ex'=>1,
            'nolimits'=>1,
            'exFields'=>array('P1'=>array(),'P2'=>array(),'P3'=>array(),'MP1'=>array(),'MP3'=>array())
        ));
        ksort($this->cc->ex_arr['P1']);
        ksort($this->cc->ex_arr['P2']);
        ksort($this->cc->ex_arr['P3']);
        ksort($this->cc->ex_arr['MP1']);
        ksort($this->cc->ex_arr['MP3']);
        unset($this->cc->ex_arr['P1'][0],$this->cc->ex_arr['P2'][0],$this->cc->ex_arr['P3'][0],$this->cc->ex_arr['MP1'][0]);
        $this->filter=array(
            'P1'=>array_keys($this->cc->ex_arr['P1']),
            'P2'=>array_keys($this->cc->ex_arr['P2']),
            'P3'=>array_keys($this->cc->ex_arr['P3'])
        );

        $this->filterHF=array(
            'at'=>2,
            'mp1'=>App_Route::$param['M1']
        );

        $this->qbrands=array(0=>array());
        foreach($d as $v){
            $this->qbrands[0][]=array(
                'name'=>Tools::unesc($v['name']),
                'sname'=>$burl[App_Route::$param['M1']].Tools::unesc($v['sname']).'.html'
            );
        }

        // ссылки по радиусам
        $this->rlinks=array();
        if(!empty($this->filter['P1'])){
            $burl='/'.App_Route::_getUrl('tSearch').'.html?p1=';
            foreach($this->filter['P1'] as $v){
                $this->rlinks[]=array(
                    'url'=>$burl.$v.'&mp1='.App_Route::$param['M1'].'&at=2',
                    'title'=>'купить шины r'.$v,
                    'anc'=>"Шины R{$v}"
                );
            }
        }

        // фильтры
        $this->lf=array();
        $this->lfi=0;

        $this->lf['mp3']=array();
        $si=App_Route::_getUrl('tSearch').'?';
        if(count(@$this->cc->ex_arr['MP3'])>1){
            $this->lf['mp3'][1]=array(
                'chk'=>false,
                'anc'=>'Шип <img src="/app/images/ship.png" alt="шипованные шины">',
                'id'=>'_mp31',
                'url'=>$si.'mp3=1'
            );
            $this->lf['mp3'][0]=array(
                'chk'=>false,
                'anc'=>"Нешип",
                'id'=>'_mp30',
                'url'=>$si.'mp3=0'
            );
        }
        if(count(@$this->cc->ex_arr['MP1'])>1){
            $this->lf['mp1']=array();
            for($k=1; $k<=3; $k++) {
                $this->lf['mp1'][$k]=array(
                    'chk'=>false,
                    'anc'=>@$this->sezonNames5[$k].$this->sezonIcos[$k],
                    'id'=>'_mp1'.$k,
                    'url'=>$si.'mp1='.$k
                );
            }
        }
    }


    //бренды шин для легковушек.
    public function byLight() {

        $this->view('catalog/tyres/brands');

        $this->title='Легковые шины | резина для легковых автомобилей';
        $this->_title='Легковые шины';

        $this->breadcrumbs['шины']='/'.App_Route::_getUrl('tCat').'.html';
        $this->breadcrumbs[]='легковые шины';

        $this->h=$this->cc->maxImgBH(1,1);

        $this->brands=array();
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
            'whereModel'=>'cc_model.P2=1',
            'whereCat'=>$this->minQtyRadiusSQL,
            'having'=>'modelsNum>0'
        ));
        $d=$this->cc->fetchAll('',MYSQLI_ASSOC);
        foreach($d as $v){
            $this->brands[$v['is_popular']][]=array(
                'img1'=>$this->cc->make_img_path($v['img1']),
                'url'=>'/'.App_Route::_getUrl('tLight').'/'.Tools::unesc($v['sname']).'.html',
                'title'=>'Купить легковые шины '.($v['alt']!=''?$v['alt']:$v['name']),
                'alt'=>'шины для легковых авто '.Tools::html($v['name']),
                'name'=>Tools::unesc($v['name'])
            );
        }

        $this->cc->cat(array(
            'gr'=>1,
            'M2'=>1,
            'where'=>$this->minQtyRadiusSQL,
            'fields'=>"cc_model.model_id,cc_cat.P1+'0' AS P1,cc_cat.P2+'0' AS P2,cc_cat.P3+'0' AS P3,cc_model.P1 AS MP1,cc_model.P3 AS MP3",
            'ex'=>1,
            'nolimits'=>1,
            'exFields'=>array('P1'=>array(),'P2'=>array(),'P3'=>array(),'MP1'=>array(),'MP3'=>array())
        ));
        ksort($this->cc->ex_arr['P1']);
        ksort($this->cc->ex_arr['P2']);
        ksort($this->cc->ex_arr['P3']);
        ksort($this->cc->ex_arr['MP1']);
        ksort($this->cc->ex_arr['MP3']);
        unset($this->cc->ex_arr['P1'][0],$this->cc->ex_arr['P2'][0],$this->cc->ex_arr['P3'][0],$this->cc->ex_arr['MP1'][0]);
        $this->filter=array(
            'P1'=>array_keys($this->cc->ex_arr['P1']),
            'P2'=>array_keys($this->cc->ex_arr['P2']),
            'P3'=>array_keys($this->cc->ex_arr['P3'])
        );

        $this->filterHF=array(
            'at'=>1
        );

        $this->qbrands=array(0=>array());
        foreach($d as $v){
            $this->qbrands[0][]=array(
                'name'=>Tools::unesc($v['name']),
                'sname'=>'/'.App_Route::_getUrl('tLight').'/'.Tools::unesc($v['sname']).'.html'
            );
        }

        // ссылки по радиусам
        $this->rlinks=array();
        if(!empty($this->filter['P1'])){
            $burl='/'.App_Route::_getUrl('tSearch').'.html?p1=';
            foreach($this->filter['P1'] as $v){
                $this->rlinks[]=array(
                    'url'=>$burl.$v.'&at=1',
                    'title'=>'купить шины r'.$v,
                    'anc'=>"Шины R{$v}"
                );
            }
        }
        // фильтры
        $this->lf=array();
        $this->lfi=0;

        $this->lf['mp3']=array();
        $si=App_Route::_getUrl('tSearch').'?';
        if(count(@$this->cc->ex_arr['MP3'])>1){
            $this->lf['mp3'][1]=array(
                'chk'=>false,
                'anc'=>'Шип <img src="/app/images/ship.png" alt="шипованные шины">',
                'id'=>'_mp31',
                'url'=>$si.'mp3=1'
            );
            $this->lf['mp3'][0]=array(
                'chk'=>false,
                'anc'=>"Нешип",
                'id'=>'_mp30',
                'url'=>$si.'mp3=0'
            );
        }
        if(count(@$this->cc->ex_arr['MP1'])>1){
            $this->lf['mp1']=array();
            if(!empty($this->cc->ex_arr['MP1'][2]))
                $this->lf['mp1'][2]=array(
                    'chk'=>false,
                    'anc'=>@$this->sezonNames5[2].$this->sezonIcos[2],
                    'id'=>'_mp12',
                    'url'=>$si.'mp1=2'
                );
            if(!empty($this->cc->ex_arr['MP1'][1]))
                $this->lf['mp1'][1]=array(
                    'chk'=>false,
                    'anc'=>@$this->sezonNames5[1].$this->sezonIcos[1],
                    'id'=>'_mp11',
                    'url'=>$si.'mp1=1'
                );
            if(!empty($this->cc->ex_arr['MP1'][3]))
                $this->lf['mp1'][3]=array(
                    'chk'=>false,
                    'anc'=>@$this->sezonNames5[3].$this->sezonIcos[3],
                    'id'=>'_mp13',
                    'url'=>$si.'mp1=3'
                );
        }

    }

    // бренды для легковых шин и сезону
    public function bySezonLight() {

        if(!in_array(App_Route::$param['M1'],array(1,2,3))) return App_Route::redir404();

        $this->view('catalog/tyres/brands');

        $this->title=$this->sezonNames1[App_Route::$param['M1']].' для легковых авто, выбор '.$this->sezonNames6[App_Route::$param['M1']].' шин для легковых автомобилей';
        $this->_title='Каталог '.$this->sezonNames6[App_Route::$param['M1']].' шин для легковых автомобилей';

        $this->breadcrumbs['шины']='/'.App_Route::_getUrl('tCat').'.html';
        $this->breadcrumbs['легковые шины']='/'.App_Route::_getUrl('tLight').'.html';
        $this->breadcrumbs[]=$this->sezonNames4[App_Route::$param['M1']].' легковые шины';

        $this->h=$this->cc->maxImgBH(1,1);

        $this->brands=array();
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
            'whereModel'=>array(
                'cc_model.P1='.App_Route::$param['M1'],
                'cc_model.P2=1'
            ),
            'whereCat'=>$this->minQtyRadiusSQL,
            'having'=>'modelsNum>0'
        ));
        $d=$this->cc->fetchAll('',MYSQLI_ASSOC);
        $burl=array(
            1=>'/'.App_Route::_getUrl('tSummerLight').'/',
            2=>'/'.App_Route::_getUrl('tWinterLight').'/',
            3=>'/'.App_Route::_getUrl('tAllWLight').'/'
        );
        foreach($d as $v){
            $this->brands[$v['is_popular']][]=array(
                'img1'=>$this->cc->make_img_path($v['img1']),
                'url'=>$burl[App_Route::$param['M1']].Tools::unesc($v['sname']).'.html',
                'title'=>'Купить '.$this->sezonNames4[App_Route::$param['M1']].' шины для легковых авто '.($v['alt']!=''?$v['alt']:$v['name']),
                'alt'=>$this->sezonNames5[App_Route::$param['M1']].' шины легковых авто '.Tools::html($v['name']),
                'name'=>Tools::unesc($v['name'])
            );
        }

        $this->cc->cat(array(
            'gr'=>1,
            'M1'=>App_Route::$param['M1'],
            'M2'=>1,
            'where'=>$this->minQtyRadiusSQL,
            'fields'=>"cc_model.model_id,cc_cat.P1+'0' AS P1,cc_cat.P2+'0' AS P2,cc_cat.P3+'0' AS P3,cc_model.P1 AS MP1,cc_model.P3 AS MP3",
            'ex'=>1,
            'nolimits'=>1,
            'exFields'=>array('P1'=>array(),'P2'=>array(),'P3'=>array(),'MP1'=>array(),'MP3'=>array())
        ));
        ksort($this->cc->ex_arr['P1']);
        ksort($this->cc->ex_arr['P2']);
        ksort($this->cc->ex_arr['P3']);
        ksort($this->cc->ex_arr['MP1']);
        ksort($this->cc->ex_arr['MP3']);
        unset($this->cc->ex_arr['P1'][0],$this->cc->ex_arr['P2'][0],$this->cc->ex_arr['P3'][0],$this->cc->ex_arr['MP1'][0]);
        $this->filter=array(
            'P1'=>array_keys($this->cc->ex_arr['P1']),
            'P2'=>array_keys($this->cc->ex_arr['P2']),
            'P3'=>array_keys($this->cc->ex_arr['P3'])
        );

        $this->filterHF=array(
            'at'=>1,
            'mp1'=>App_Route::$param['M1']
        );

        $this->qbrands=array(0=>array());
        foreach($d as $v){
            $this->qbrands[0][]=array(
                'name'=>Tools::unesc($v['name']),
                'sname'=>$burl[App_Route::$param['M1']].Tools::unesc($v['sname']).'.html'
            );
        }

        // ссылки по радиусам
        $this->rlinks=array();
        if(!empty($this->filter['P1'])){
            $burl='/'.App_Route::_getUrl('tSearch').'.html?p1=';
            foreach($this->filter['P1'] as $v){
                $this->rlinks[]=array(
                    'url'=>$burl.$v.'&mp1='.App_Route::$param['M1'].'&at=1',
                    'title'=>'купить шины r'.$v,
                    'anc'=>"Шины R{$v}"
                );
            }
        }

        // фильтры
        $this->lf=array();
        $this->lfi=0;

        $this->lf['mp3']=array();
        $si=App_Route::_getUrl('tSearch').'?';
        if(count(@$this->cc->ex_arr['MP3'])>1){
            $this->lf['mp3'][1]=array(
                'chk'=>false,
                'anc'=>'Шип <img src="/app/images/ship.png" alt="шипованные шины">',
                'id'=>'_mp31',
                'url'=>$si.'mp3=1'
            );
            $this->lf['mp3'][0]=array(
                'chk'=>false,
                'anc'=>"Нешип",
                'id'=>'_mp30',
                'url'=>$si.'mp3=0'
            );
        }
        if(count(@$this->cc->ex_arr['MP1'])>1){
            $this->lf['mp1']=array();
            for($k=1; $k<=3; $k++) {
                $this->lf['mp1'][$k]=array(
                    'chk'=>false,
                    'anc'=>@$this->sezonNames5[$k].$this->sezonIcos[$k],
                    'id'=>'_mp1'.$k,
                    'url'=>$si.'mp1='.$k
                );
            }
        }
    }


    //бренды усиленных шин.
    public function byStrong() {

        $this->view('catalog/tyres/brands');

        $this->title='Шины для микроавтобусов | усиленные шины';
        $this->_title='Шины для микроавтобусов';

        $this->breadcrumbs['шины']='/'.App_Route::_getUrl('tCat').'.html';
        $this->breadcrumbs[]='шины для микроавтобусов';

        $this->h=$this->cc->maxImgBH(1,1);

        $this->brands=array();
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
                'cc_brand.is_popular'=>'is_popular',
            ),
            'whereModel'=>'cc_model.P2=3',
            'whereCat'=>$this->minQtyRadiusSQL,
            'having'=>'modelsNum>0'
        ));
        $d=$this->cc->fetchAll('',MYSQLI_ASSOC);
        foreach($d as $v){
            $this->brands[$v['is_popular']][]=array(
                'img1'=>$this->cc->make_img_path($v['img1']),
                'url'=>'/'.App_Route::_getUrl('tStrong').'/'.Tools::unesc($v['sname']).'.html',
                'title'=>'Купить шины для микроавтобусов '.($v['alt']!=''?$v['alt']:$v['name']),
                'alt'=>'шины для легких грузовиков и микроавтобусов '.Tools::html($v['name']),
                'name'=>Tools::unesc($v['name'])
            );
        }

        $this->cc->cat(array(
            'gr'=>1,
            'M2'=>3,
            'where'=>$this->minQtyRadiusSQL,
            'fields'=>"cc_model.model_id,cc_cat.P1+'0' AS P1,cc_cat.P2+'0' AS P2,cc_cat.P3+'0' AS P3,cc_model.P1 AS MP1,cc_model.P3 AS MP3",
            'ex'=>1,
            'nolimits'=>1,
            'exFields'=>array('P1'=>array(),'P2'=>array(),'P3'=>array(),'MP1'=>array(),'MP3'=>array())
        ));
        ksort($this->cc->ex_arr['P1']);
        ksort($this->cc->ex_arr['P2']);
        ksort($this->cc->ex_arr['P3']);
        ksort($this->cc->ex_arr['MP1']);
        ksort($this->cc->ex_arr['MP3']);
        unset($this->cc->ex_arr['P1'][0],$this->cc->ex_arr['P2'][0],$this->cc->ex_arr['P3'][0],$this->cc->ex_arr['MP1'][0]);
        $this->filter=array(
            'P1'=>array_keys($this->cc->ex_arr['P1']),
            'P2'=>array_keys($this->cc->ex_arr['P2']),
            'P3'=>array_keys($this->cc->ex_arr['P3'])
        );

        $this->filterHF=array(
            'at'=>3
        );

        $this->qbrands=array(0=>array());
        foreach($d as $v){
            $this->qbrands[0][]=array(
                'name'=>Tools::unesc($v['name']),
                'sname'=>'/'.App_Route::_getUrl('tStrong').'/'.Tools::unesc($v['sname']).'.html'
            );
        }

        // ссылки по радиусам
        $this->rlinks=array();
        if(!empty($this->filter['P1'])){
            $burl='/'.App_Route::_getUrl('tSearch').'.html?p1=';
            foreach($this->filter['P1'] as $v){
                $this->rlinks[]=array(
                    'url'=>$burl.$v.'&at=3',
                    'title'=>'купить шины r'.$v,
                    'anc'=>"Шины R{$v}"
                );
            }
        }

        // фильтры
        $this->lf=array();
        $this->lfi=0;

        $this->lf['mp3']=array();
        $si=App_Route::_getUrl('tSearch').'?';
        if(count(@$this->cc->ex_arr['MP3'])>1){
            $this->lf['mp3'][1]=array(
                'chk'=>false,
                'anc'=>'Шип <img src="/app/images/ship.png" alt="шипованные шины">',
                'id'=>'_mp31',
                'url'=>$si.'mp3=1'
            );
            $this->lf['mp3'][0]=array(
                'chk'=>false,
                'anc'=>"Нешип",
                'id'=>'_mp30',
                'url'=>$si.'mp3=0'
            );
        }
        if(count(@$this->cc->ex_arr['MP1'])>1){
            $this->lf['mp1']=array();
            if(!empty($this->cc->ex_arr['MP1'][2]))
                $this->lf['mp1'][2]=array(
                    'chk'=>false,
                    'anc'=>@$this->sezonNames5[2].$this->sezonIcos[2],
                    'id'=>'_mp12',
                    'url'=>$si.'mp1=2'
                );
            if(!empty($this->cc->ex_arr['MP1'][1]))
                $this->lf['mp1'][1]=array(
                    'chk'=>false,
                    'anc'=>@$this->sezonNames5[1].$this->sezonIcos[1],
                    'id'=>'_mp11',
                    'url'=>$si.'mp1=1'
                );
            if(!empty($this->cc->ex_arr['MP1'][3]))
                $this->lf['mp1'][3]=array(
                    'chk'=>false,
                    'anc'=>@$this->sezonNames5[3].$this->sezonIcos[3],
                    'id'=>'_mp13',
                    'url'=>$si.'mp1=3'
                );
        }
    }

    // бренды усиленных шин и сезону
    public function bySezonStrong() {

        if(!in_array(App_Route::$param['M1'],array(1,2,3))) return App_Route::redir404();

        $this->view('catalog/tyres/brands');

        $this->title=$this->sezonNames1[App_Route::$param['M1']].' для микроавтобусв, выбор '.$this->sezonNames6[App_Route::$param['M1']].' шин для микроавтобусов и легких грузовиков';
        $this->_title='Каталог '.$this->sezonNames6[App_Route::$param['M1']].' шин для микроавтобусов и легких грузовиков';

        $this->breadcrumbs['шины']='/'.App_Route::_getUrl('tCat').'.html';
        $this->breadcrumbs['шины для микроавтобусов']='/'.App_Route::_getUrl('tStrong').'.html';
        $this->breadcrumbs[]=$this->sezonNames4[App_Route::$param['M1']].' шины для микроавтобусов';

        $this->h=$this->cc->maxImgBH(1,1);

        $this->brands=array();
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
            'whereModel'=>array(
                'cc_model.P1='.App_Route::$param['M1'],
                'cc_model.P2=3'
            ),
            'whereCat'=>$this->minQtyRadiusSQL,
            'having'=>'modelsNum>0'
        ));
        $d=$this->cc->fetchAll('',MYSQLI_ASSOC);
        $burl=array(
            1=>'/'.App_Route::_getUrl('tSummerStrong').'/',
            2=>'/'.App_Route::_getUrl('tWinterStrong').'/',
            3=>'/'.App_Route::_getUrl('tAllWStrong').'/'
        );
        foreach($d as $v){
            $this->brands[$v['is_popular']][]=array(
                'img1'=>$this->cc->make_img_path($v['img1']),
                'url'=>$burl[App_Route::$param['M1']].Tools::unesc($v['sname']).'.html',
                'title'=>'Купить '.$this->sezonNames4[App_Route::$param['M1']].' шины для микроавтобусов '.($v['alt']!=''?$v['alt']:$v['name']),
                'alt'=>$this->sezonNames5[App_Route::$param['M1']].' шины '.Tools::html($v['name']).' для микроавтобусов',
                'name'=>Tools::unesc($v['name'])
            );
        }

        $this->cc->cat(array(
            'gr'=>1,
            'M1'=>App_Route::$param['M1'],
            'M2'=>3,
            'where'=>$this->minQtyRadiusSQL,
            'fields'=>"cc_model.model_id,cc_cat.P1+'0' AS P1,cc_cat.P2+'0' AS P2,cc_cat.P3+'0' AS P3,cc_model.P1 AS MP1,cc_model.P3 AS MP3",
            'ex'=>1,
            'nolimits'=>1,
            'exFields'=>array('P1'=>array(),'P2'=>array(),'P3'=>array(),'MP1'=>array(),'MP3'=>array())
        ));
        ksort($this->cc->ex_arr['P1']);
        ksort($this->cc->ex_arr['P2']);
        ksort($this->cc->ex_arr['P3']);
        ksort($this->cc->ex_arr['MP1']);
        ksort($this->cc->ex_arr['MP3']);
        unset($this->cc->ex_arr['P1'][0],$this->cc->ex_arr['P2'][0],$this->cc->ex_arr['P3'][0],$this->cc->ex_arr['MP1'][0]);
        $this->filter=array(
            'P1'=>array_keys($this->cc->ex_arr['P1']),
            'P2'=>array_keys($this->cc->ex_arr['P2']),
            'P3'=>array_keys($this->cc->ex_arr['P3'])
        );

        $this->filterHF=array(
            'at'=>3,
            'mp1'=>App_Route::$param['M1']
        );

        $this->qbrands=array(0=>array());
        foreach($d as $v){
            $this->qbrands[0][]=array(
                'name'=>Tools::unesc($v['name']),
                'sname'=>$burl[App_Route::$param['M1']].Tools::unesc($v['sname']).'.html'
            );
        }

        // ссылки по радиусам
        $this->rlinks=array();
        if(!empty($this->filter['P1'])){
            $burl='/'.App_Route::_getUrl('tSearch').'.html?p1=';
            foreach($this->filter['P1'] as $v){
                $this->rlinks[]=array(
                    'url'=>$burl.$v.'&mp1='.App_Route::$param['M1'].'&at=3',
                    'title'=>'купить шины r'.$v,
                    'anc'=>"Шины R{$v}"
                );
            }

        }

        // фильтры
        $this->lf=array();
        $this->lfi=0;

        $this->lf['mp3']=array();
        $si=App_Route::_getUrl('tSearch').'?';
        if(count(@$this->cc->ex_arr['MP3'])>1){
            $this->lf['mp3'][1]=array(
                'chk'=>false,
                'anc'=>'Шип <img src="/app/images/ship.png" alt="шипованные шины">',
                'id'=>'_mp31',
                'url'=>$si.'mp3=1'
            );
            $this->lf['mp3'][0]=array(
                'chk'=>false,
                'anc'=>"Нешип",
                'id'=>'_mp30',
                'url'=>$si.'mp3=0'
            );
        }
        if(count(@$this->cc->ex_arr['MP1'])>1){
            $this->lf['mp1']=array();
            for($k=1; $k<=3; $k++) {
                $this->lf['mp1'][$k]=array(
                    'chk'=>false,
                    'anc'=>@$this->sezonNames5[$k].$this->sezonIcos[$k],
                    'id'=>'_mp1'.$k,
                    'url'=>$si.'mp1='.$k
                );
            }
        }
    }




}