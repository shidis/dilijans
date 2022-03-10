<?
class App_Catalog_Disks_Skmodels_Controller extends App_Catalog_Disks_Common_Controller
{
    public
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
        $brands_=array();

    public
        $apMode;  // _GET['ap']==1  включает delta_et  и delta_dia


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

        if(empty($this->P5)) $r['exFields']['P5']=array(); else $r['P5']=array('list'=>$this->P5); // радиус
        if(empty($this->brands)) $r['exFields']['brand']=array(); else $r['brand_id']=array('list'=>$this->brands); // бренд
        if(!empty($this->P2)) $r['P2']=array('list'=>$this->P2);
        if(!empty($this->P4)) $r['P4']=array('list'=>$this->P4);
        if(!empty($this->P6)) $r['P6']=array('list'=>$this->P6);

        if(!empty($this->P3))
            if($this->apMode){
                $r['P3']=array('from'=>(float)min($this->P3)+$this->_deltaDia,'to'=>'');
            } else $r['P3']=array('list'=>$this->P3);

        if(!empty($this->P1))
            if($this->apMode){
                $r['P1']=array('from'=>(float)min($this->P1)+$this->_deltaET,'to'=>(float)max($this->P1)+$this->deltaET_);
            }
            else $r['P1']=array('list'=>$this->P1);

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
        if(!empty(App_Route::$param['d_type'])) $r['where'][]="cc_model.P1='".((int)App_Route::$param['d_type'])."'";

        if(!empty($this->_where)) $r['where']=array_merge($r['where'],$this->_where);
        if(!empty($this->_whereCat)) $r['where']=array_merge($r['where'],$this->_whereCat);
        if(!empty($this->_having)) $r['having']=array_merge($r['having'],$this->_having);

        $r['sqlReturn']=0;
        $r['nolimits']=1;
        $r['ex']=1;

        //Tools::prn($r['catOrGroups']);
        $this->exnum=$this->cc->cat_view($r);
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
        /*if((count(@$this->ex['brand'][0])+count(@$this->ex['brand']['replica']))>1){
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
                foreach($this->ex['brand']['replica'] as $k=>$v){
                    $this->lf['_rbids'][$k]=array(
                        'chk'=>in_array($k,$this->_brands)?true:false,
                        'anc'=>$v['name'],
                        'id'=>'_bids'.$k,
                        'url'=>$si."/".$v['sname'].'.html'
                    );
                }
            }
        }*/
        if(count(@$this->ex['P5'])>1){
            $this->lfi++;
            ksort($this->ex['P5']);
            $this->lf['_p5']=array();
            $si=App_Route::_getUrl('dSearch').'?';
            foreach($this->ex['P5'] as $k=>$v){
                $this->lf['_p5'][$k]=array(
                    'chk'=>in_array($k,$this->_P5)?true:false,
                    'anc'=>"R$k",
                    'id'=>'_p5'.$this->makeId($k),
                    'url'=>$si.'p5='.$k
                );
            }
        }
        if(count(@$this->ex['P46'])>1){
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
            //if(!in_array($this->limit,$this->limits)) $this->limit=0;
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
                $r['order'][]='cc_brand.name,cc_model.name,cc_cat.P5';
                break;
            case 1:
                $r['order'][]='cc_brand.name,cc_model.name,cc_cat.P5';
                break;
            case -1:
                $r['order'][]='cc_brand.name DESC,cc_model.name DESC,cc_cat.P5';
                break;
            case 2:
                $r['order'][]='cc_cat.cprice ASC, cc_brand.name,cc_model.name,cc_cat.P5';
                break;
            case -2:
                $r['order'][]='cc_cat.cprice DESC, cc_brand.name,cc_model.name,cc_cat.P5';
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


        if(!empty($this->_where)) $r['where']=array_merge($r['where'],$this->_where);
        if(!empty($this->_whereCat)) $r['where']=array_merge($r['where'],$this->_whereCat);
        if(!empty($this->_having)) $r['having']=array_merge($r['having'],$this->_having);

        if($this->hideDSCZero) $r['where'][]=$this->minQtyRadiusSQL;
        if(!empty(App_Route::$param['d_type'])) $r['where'][]="cc_model.P1='".((int)App_Route::$param['d_type'])."'";

        //добавляем к запросу все параметры
        if(!empty($this->P5_)) $r['P5']=array('list'=>$this->P5_); // радиус
        if(!empty($this->brands_)) $r['brand_id']=array('list'=>$this->brands_); // бренд
        if(!empty($this->P2_)) $r['P2']=array('list'=>$this->P2_);
        if(!empty($this->P4_)) $r['P4']=array('list'=>$this->P4_);
        if(!empty($this->P6_)) $r['P6']=array('list'=>$this->P6_);

        if(!empty($this->P3_))
            if($this->apMode){
                $r['P3']=array('from'=>(float)min($this->P3_)+$this->_deltaDia,'to'=>'');
            } else $r['P3']=array('list'=>$this->P3_);

        if(!empty($this->P1_))
            if($this->apMode){
                $r['P1']=array('from'=>(float)min($this->P1_)+$this->_deltaET,'to'=>(float)max($this->P1_)+$this->deltaET_);
            }
            else $r['P1']=array('list'=>$this->P1_);

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
        $d=$this->cc->fetchAll();
        if($this->num) {
            $this->paginator=$this->cc->paginator(Url::$path,Url::$sq,@Url::$sq['page'],$this->num,$this->limit,'page',array(
                'active'=>	'<li class="active">{page}</li>',
                'noActive'=>'<li><a href="{url}">{page}</a></li>',
                'dots'=>	'<li>...</li>'
            ),5);
        }
        $burl='/'.App_Route::_getUrl('dTipo').'/';

        foreach($d as $v){
            $this->cat[]=$this->catRow($v,$burl);
        }

        return true;

    }


    public function search()
    {
        $this->_routeParam();

        $this->view('catalog/disks/sksearch');

        $meta_cat = $meta_cat_s = '';
        switch (App_Route::$param['d_type'])
        {
            case 1:
                    $meta_cat = 'Кованые диски';
                    $meta_cat_s = 'кованые диски';
                break;
            case 3:
                    $meta_cat = 'Штампованные диски';
                    $meta_cat_s = 'штампованные диски';
                break;
            default:
                    $meta_cat = 'Диски';
                    $meta_cat_s = 'диски';
                break;
        }

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

                $this->title=Tools::cutDoubleSpaces("$meta_cat Replica {$this->bname} R$P5. Каталог дисков $P5 радиус для {$this->bname} в интернет магазине. ");
                $this->_title=Tools::cutDoubleSpaces("$meta_cat Replica R$P5 для {$this->bname}.");
                $this->keywords=Tools::cutDoubleSpaces("$meta_cat_s {$this->bname} R$P5");
                $this->description=Tools::cutDoubleSpaces("Диски для автомобиля {$this->bname} $P5 радиус по низким ценам в интернет магазине Дилижанс. Широкий ассортимент дисков R$P5 реплика для авто {$this->bname}. Доставка литых дисков {$this->bname} $P5 радиус по все территории России:  Москва, Санкт-Петербург, Екатеринбург, Уфа, Воронеж и т.д.");

            }else{
                $this->title=Tools::cutDoubleSpaces("Диски реплика {$this->bname} {$this->size}");
                $this->_title=Tools::cutDoubleSpaces("Результаты поиска: диски Replica {$this->bname} {$this->size}");
                $this->keywords=Tools::cutDoubleSpaces("колесные диски реплика replica {$this->bname} {$this->size}");
                $this->description=Tools::cutDoubleSpaces("колесные $meta_cat_s реплика (replica) {$this->bname} {$this->size}, здесь вы можете выбрать и купить диски реплика {$this->balt1} {$this->size}");


            }

            $this->breadcrumbs['Диски реплика']=array('/'.App_Route::_getUrl('replicaCat').'.html','');
            if($this->brand_id)
                $this->breadcrumbs[$this->bname]=array('/'.App_Route::_getUrl('dCat').'/'.$this->brand_sname.'.html','');

        }else{

            $this->breadcrumbs[$meta_cat_s]=array('/'.App_Route::_getUrl('dCat').'.html',"купить $meta_cat_s");

            if(!empty($this->ab->fname) && !empty(Url::$sq['ap'])){
                $this->title=Tools::cutDoubleSpaces("Диски {$this->bname} {$this->size} для {$this->ab->fname}");
                $this->_title=Tools::cutDoubleSpaces("Диски {$this->bname} {$this->size} для {$this->ab->fname}");

                $this->keywords=Tools::cutDoubleSpaces("$meta_cat_s replica {$this->bname} {$this->size}");
                $this->description=Tools::cutDoubleSpaces("$meta_cat_s {$this->bname} {$this->size}, здесь вы можете выбрать и купить диски {$this->balt1} {$this->size}");

            }else{

                // радиус
                if($parama==='0010'){

                    $this->title=Tools::cutDoubleSpaces("$meta_cat R$P5 по низким ценам. Продажа колесных дисков $P5 радиус в интернет магазине Дилижанс.");
                    $this->_title=Tools::cutDoubleSpaces("Каталог литых дисков $P5 радиуса - R$P5: литые, кованные и штампованные диски.");
                    $this->keywords=Tools::cutDoubleSpaces("$meta_cat_s R$P5");
                    $this->description=Tools::cutDoubleSpaces("Широкий выбор литых дисков R$P5 в интернет магазине по привлекательным ценам. Огромный ассортимент литых дисков $P5 радиуса от разных мировых производителей. Заказывайте колесные диски: литые R$P5, кованные R$P5, штампованные R $P5 -  доставка по России в города: Москва, Санкт-Петербург, Воронеж, Екатеринбург, Нижний Новгород, Уфа, Казань и др.");

                }elseif($parama==='0001'){

                    $this->title=Tools::cutDoubleSpaces("Диски литые размера $sv1 по низкой цене. Продажа колесных дисков сверловка $sv1 в интернет магазине Дилижанс.");
                    $this->_title=Tools::cutDoubleSpaces("Каталог литых дисков со сверловкой $sv1 (размер)");
                    $this->keywords=Tools::cutDoubleSpaces("$meta_cat_s $sv1");
                    $this->description=Tools::cutDoubleSpaces("Огромный выбор литых дисков размера $sv1 в интернет магазине Дилижанс по привлекательным ценам.  Вы можете заказать колесные диски со сверловкой $sv1, доставка осуществляется по всем городам России: Москва, Санкт-Петербург, Екатеринбург, Уфа, Воронеж и т.д.");

                }elseif($parama==='0110'){

                    $this->title=Tools::cutDoubleSpaces("Диски литые {$this->bname} R $P5 по привлекательным ценам. Колесные диски {$this->bname} $P5 радиус в интернет магазине Дилижанс.");
                    $this->_title=Tools::cutDoubleSpaces("Каталог литых дисков {$this->bname} $P5 радиус - R$P5");
                    $this->keywords=Tools::cutDoubleSpaces("$meta_cat_s {$this->bname} {$this->size}");
                    $this->description=Tools::cutDoubleSpaces("Выбирайте лучшие диски по параметрам, производитель {$this->bname} радиус колеса R$P5. Каталог литых дисков {$this->bname} R$P5, доставка осуществляется по всей территории России и страны СНГ: Москва, Минск, Киев, Санкт-Петербург, и многие другие города.");

                }elseif($parama==='0101'){

                    $this->title=Tools::cutDoubleSpaces("$meta_cat {$this->bname} $sv1 по низкой цене. Колесные диски {$this->bname} сверловка $sv1 в интернет магазине Дилижанс.");
                    $this->_title=Tools::cutDoubleSpaces("Каталог литых дисков {$this->bname} размер $sv1");
                    $this->keywords=Tools::cutDoubleSpaces("$meta_cat_s {$this->bname} {$this->size}");
                    $this->description=Tools::cutDoubleSpaces("Выбирайте лучшие диски по параметрам, производитель {$this->bname} размер колеса $sv1. Каталог литых дисков {$this->bname} сверловка $sv1, доставка осуществляется по всей территории России и страны СНГ: Москва, Минск, Киев, Санкт-Петербург, и многие другие города.");

                }elseif($parama==='0011'){

                    $this->title=Tools::cutDoubleSpaces("$meta_cat R$P5 размер $sv1 по низкой цене. Колесные диски $P5 радиус и сверловка $sv1 в интернет магазине Дилижанс.");
                    $this->_title=Tools::cutDoubleSpaces("Каталог литых дисков $P5 радиус и размер $sv1");
                    $this->keywords=Tools::cutDoubleSpaces("$meta_cat_s $P5 $sv1");
                    $this->description=Tools::cutDoubleSpaces("Каталог дисков по параметрам $P5 радиус и размер $sv1 от лучших мировых производителей. Доставка литых дисков осуществляется по всей территории России и страны СНГ: Москва, Минск, Киев, Санкт-Петербург, и многие другие города.");

                }elseif($parama==='0111'){

                    $this->title=Tools::cutDoubleSpaces("$meta_cat {$this->bname} R$P5 размер $sv1. Колесные диски {$this->bname} $P5 радиус и сверловка $sv1 в интернет магазине Дилижанс.");
                    $this->_title=Tools::cutDoubleSpaces("$meta_cat {$this->bname} $P5 радиус и размер $sv1");
                    $this->keywords=Tools::cutDoubleSpaces("$meta_cat_s {$this->bname} $P5 $sv1");
                    $this->description=Tools::cutDoubleSpaces("Каталог дисков по параметрам производитель {$this->bname}, $P5 радиус и размер $sv1 от лучших мировых производителей. Доставка литых дисков осуществляется по всей территории России и страны СНГ: Москва, Минск, Киев, Санкт-Петербург, и многие другие города.");

                    // все остальное
                }else{
                    $this->title=Tools::cutDoubleSpaces("$meta_cat {$this->bname} {$this->size}");
                    $this->_title=Tools::cutDoubleSpaces("$meta_cat {$this->bname} {$this->size}");
                    $this->keywords=Tools::cutDoubleSpaces("$meta_cat_s {$this->bname} {$this->size}");
                    $this->description=Tools::cutDoubleSpaces("$meta_cat_s {$this->bname} {$this->size}, здесь вы можете выбрать и купить диски {$this->balt1} {$this->size}");
                }

            }
            if($this->brand_id)
                $this->breadcrumbs[$this->bname]=array('/'.App_Route::_getUrl('dCat').'/'.$this->brand_sname.'.html', $meta_cat_s.' '.$this->balt);
        }

        if($parama==='0011'){
            $this->breadcrumbs["R$P5"]=array('/'.App_Route::_getUrl('dSearch').'?p5='.$P5, '');
            $this->breadcrumbs["$sv1"]='';
        }else
            $this->breadcrumbs["{$this->size}"]='';

        if(empty($this->cat)) $this->qtext="Дисков {$this->bname} {$this->size} в данный момент нет в наличии";


        $this->_sidebar();
    }

    function axModels()
    {
        $this->_routeParam();
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
        $this->noResults = '';
        if(true!==($res=$this->_cat())) return $res;
        $this->models = $this->cat;
        // ****************** Вывод и выход ******************
        global $app;
        if (is_file($app->namespace . '/view/catalog/disks/axSksearch.php')) {
            extract((array)$app->controllerInstance, EXTR_OVERWRITE);
            extract($app->controllerInstance->_data, EXTR_OVERWRITE);
            include $app->namespace . '/view/catalog/disks/axSksearch.php';
        } else
            throw new AppException ('[App::output()]: ' . $app->namespace . '/view/catalog/disks/axSksearch open fault.');
        exit(200);
    }
    /*
     * поиск для живого фильтра
     */
    public function axSearch()
    {
        //sleep(2);
        $time1=Tools::getMicroTime();

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
        if(!empty(App_Route::$param['d_type'])) $r['where'][]="cc_model.P1='".((int)App_Route::$param['d_type'])."'"; // Тип диска
        if(!empty($this->_where)) $r['where']=array_merge($r['where'],$this->_where);
        if(!empty($this->_whereCat)) $r['where']=array_merge($r['where'],$this->_whereCat);

        // DIA
        if(!empty($this->P3_))
            if($this->apMode){
                $r['P3']=array('from'=>(float)min($this->P3_)+$this->_deltaDia,'to'=>'');
            } else $r['P3']=array('list'=>$this->P3_);

        // ET
        if(!empty($this->P1_))
            if($this->apMode){
                $r['P1']=array('from'=>(float)min($this->P1_)+$this->_deltaET,'to'=>(float)max($this->P1_)+$this->deltaET_);
            }
            else $r['P1']=array('list'=>$this->P1_);

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

        $this->exnum=$this->cc->cat_view($r);

        $this->r['tn']=$this->exnum;
        $this->r['formdata']=array();

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
                    if(!empty($this->brands)) $r['brand_id']=array('list'=>$this->brands); else unset($r['brand_id']);
                    if(!empty($this->P5)) $r['P5']=array('list'=>$this->P5); else unset($r['P5']);  // R
                    if(!empty($this->P2)) $r['P2']=array('list'=>$this->P2); else unset($r['P2']);  // J

                    // DIA
                    if(!empty($this->P3))
                        if($this->apMode){
                            $r['P3']=array('from'=>(float)min($this->P3)+$this->_deltaDia,'to'=>'');
                        } else $r['P3']=array('list'=>$this->P3);
                    else unset($r['P3']);

                    // ET
                    if(!empty($this->P1))
                        if($this->apMode){
                            $r['P1']=array('from'=>(float)min($this->P1)+$this->_deltaET,'to'=>(float)max($this->P1)+$this->deltaET_);
                        }
                        else $r['P1']=array('list'=>$this->P1);
                    else unset($r['P1']);

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
                            if(!empty($this->_brands))  $r['brand_id']=array('list'=>$this->brands_); // бренд
                            if(!empty($this->_P5))  $r['P5']=array('list'=>$this->P5_); // радиус

                            // DIA
                            if(!empty($this->_P3))
                                if($this->apMode){
                                    $r['P3']=array('from'=>(float)min($this->P3_)+$this->_deltaDia,'to'=>'');
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
                            if(!empty($this->_brands))  $r['brand_id']=array('list'=>$this->brands_); // бренд
                            if(!empty($this->_P2))  $r['P2']=array('list'=>$this->P2_); // J
                            if(!empty($this->_P5))  $r['P5']=array('list'=>$this->P5_); // радиус

                            // DIA
                            if(!empty($this->_P3))
                                if($this->apMode){
                                    $r['P3']=array('from'=>(float)min($this->P3_)+$this->_deltaDia,'to'=>'');
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
                            if(!empty($this->_brands))  $r['brand_id']=array('list'=>$this->brands_); // бренд
                            if(!empty($this->_P2))  $r['P2']=array('list'=>$this->P2_); // J

                            // DIA
                            if(!empty($this->_P3))
                                if($this->apMode){
                                    $r['P3']=array('from'=>(float)min($this->P3_)+$this->_deltaDia,'to'=>'');
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
                            if(!empty($this->_P2))  $r['P2']=array('list'=>$this->P2_); // J
                            if(!empty($this->_P5))  $r['P5']=array('list'=>$this->P5_); // Rad

                            // DIA
                            if(!empty($this->_P3))
                                if($this->apMode){
                                    $r['P3']=array('from'=>(float)min($this->P3_)+$this->_deltaDia,'to'=>'');
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
                                    $r['P3']=array('from'=>(float)min($this->P3_)+$this->_deltaDia,'to'=>'');
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

                    if(!empty(App_Route::$param['d_type'])) $r['where'][]="cc_model.P1='".((int)App_Route::$param['d_type'])."'"; // Тип диска

                    if(!empty($r['groupby'])) {
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

    public function qsearch()
    {
        $this->search();
        return 'search/stage3';
    }

    function _sidebar()
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

        if(!empty($this->brand_id)){
            // модели в сайдбаре. bname, brand_id должно быть определено выше
            $r=array(
                'gr'=>2,
                'P1' => (int)@App_Route::$param['d_type'],
                'brand_id'=>$this->brand_id,
                'nolimits'=>1,
                'qSelect'=>array(
                    'scDiv'=>array()
                ),
                'order'=>"cc_model.name"
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
        // Все убираем, т.к. нет таких страниц
        $this->qbrands = Array();
        $this->qmodels = Array();
    }

}
