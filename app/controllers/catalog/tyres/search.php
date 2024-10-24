<?
class App_Catalog_Tyres_Search_Controller extends App_Catalog_Tyres_Common_Controller
{
    public
    // главные параметры
    $M1=array(), // GET['mp1'] || route['M1'] as integer
    $M2=array(), // GET['at'] || route['M2'] as integer  -тип авто
    $M3='', // GET['mp3'] || route['M3'] as integer  -шип
    $P1=array(), // GET['p1'] || route['P1'] as decimal
    $P2=array(), // GET['p2'] || route['P2'] as decimal
    $P3=array(), // GET['p3'] || route['P3'] as decimal
    $brands=array(), // GET['vendor']
    $runflat='', // технология ранфлет
    $c_index='', // индекс С
    // главные спарочные
    $sP1=array(), // GET['p1_']
    $sP2=array(), // GET['p2_']
    $sP3=array(), // GET['p3_']
    $sMode=false, // ==true - спарка-режим

    // уточняющие параметры
    $_M1=array(), // GET['_mp1']
    $_M2=array(), // GET['_at']
    $_M3='', // GET['_mp3']
    $_P1=array(), // GET['_p1']
    $_P2=array(), // GET['_p2']
    $_P3=array(), // GET['_p3']
    $_brands=array(), // GET['_bids']
    $_runflat='',
    $_c_index='',

    // сумма параметров
    $M1_=array(),
    $M2_=array(),
    $M3_='',
    $P1_=array(),
    $P2_=array(),
    $P3_=array(),
    $runflat_='',
    $c_index_='',
    $brands_=array();

    private $breadcrumbs = '';


    /*
    * ЗАПОЛНЕНИЕ ПЕРЕМЕННЫХ ГЕТ ПАРАМЕТРАМИ ДЛЯ ПОИСКА
    */
    private function _routeParam()
    {
        if(!empty(App_Route::$param['M1'])) $this->M1[]=App_Route::$param['M1'];
        elseif(@is_array(Url::$sq['mp1'])) $this->M1=array_keys(Url::$sq['mp1']);
        elseif(Tools::typeOf(@Url::$sq['mp1'])=='integer') $this->M1=array(Url::$sq['mp1']);

        if(!empty(App_Route::$param['M2'])) $this->M2[]=App_Route::$param['M2'];
        elseif(@is_array(Url::$sq['at'])) $this->M2=array_keys(Url::$sq['at']);
        elseif(Tools::typeOf(@Url::$sq['at'])=='integer') $this->M2=array(Url::$sq['at']);

        if(isset(App_Route::$param['M3']) && @App_Route::$param['M3']!=='') $this->M3=(string)@App_Route::$param['M3'];
        elseif(@is_array(Url::$sq['mp3'])) $this->M3=array_pop(array_keys(Url::$sq['mp3']));
        elseif(Tools::typeOf(@Url::$sq['mp3'])=='integer') $this->M3=Url::$sq['mp3'];

        if(!empty(App_Route::$param['P1'])) $this->P1[]=App_Route::$param['P1'];
        elseif(@is_array(Url::$sq['p1'])) $this->P1=array_keys(Url::$sq['p1']);
        elseif(in_array(Tools::typeOf(@Url::$sq['p1']),array('float','integer'))) $this->P1=array(Url::$sq['p1']);

        if(isset(App_Route::$param['P2']) && App_Route::$param['P2']!=='') $this->P2[]=App_Route::$param['P2'];
        elseif(@is_array(Url::$sq['p2'])) $this->P2=array_keys(Url::$sq['p2']);
        elseif(in_array(Tools::typeOf(@Url::$sq['p2']),array('float','integer'))) $this->P2=array(Url::$sq['p2']);

        if(!empty(App_Route::$param['P3'])) $this->P3[]=App_Route::$param['P3'];
        elseif(@is_array(Url::$sq['p3'])) $this->P3=array_keys(Url::$sq['p3']);
        elseif(in_array(Tools::typeOf(@Url::$sq['p3']),array('float','integer'))) $this->P3=array(Url::$sq['p3']);

        if(!empty(Url::$sq['vendor'])){
            $this->cc->que('brand_by_sname',Url::$sq['vendor'],1,1);
            if(!$this->cc->qnum()) return App_Route::redir404();
            $this->cc->next();
            $this->brand_id=$this->cc->qrow['brand_id'];
            $this->bname=Tools::unesc($this->cc->qrow['name']);
            $this->balt=Tools::unesc($this->cc->qrow['alt']!=''?$this->cc->qrow['alt']:$this->cc->qrow['name']);
            $this->balt1=$this->firstS($this->balt);
            $this->baltOther=$this->otherS($this->balt);
            $this->brand_sname=$this->cc->qrow['sname'];
        }

        if(empty($this->brand_id)) {
            if(@is_array(Url::$sq['bids'])) $this->brands=array_keys(Url::$sq['bids']);
            elseif(Tools::typeOf(@Url::$sq['bids'])=='integer') $this->brands=array(Url::$sq['bids']);
        } else $this->brands=array($this->brand_id);

        if (!$this->c_index)
        {
            if(isset(App_Route::$param['runflat']) && @App_Route::$param['runflat']!=='') $this->runflat=(string)@App_Route::$param['runflat'];
            elseif(Tools::typeOf(@Url::$sq['runflat'])=='integer') $this->runflat=Url::$sq['runflat'];
        }
        if (!$this->runflat)
        {
            // ************************ new field: c_index *************************
            if(isset(App_Route::$param['c_index']) && @App_Route::$param['c_index']!=='') $this->c_index=(string)@App_Route::$param['c_index'];
            elseif(Tools::typeOf(@Url::$sq['c_index'])=='integer') $this->c_index=Url::$sq['c_index'];
            // ************************ /new field: c_index *************************
        }
        // спарки
        $this->sMode=false;                    
        /*
        * диаметр у второй оси можно не передавать - он будет равен переднему
        */
        if(count($this->P1)==1 && count($this->P2)==1 && count($this->P3)==1 && count($this->P1)==1){
            if(in_array(Tools::typeOf(@Url::$sq['p1_']),array('float','integer'))) $this->sP1=array(Url::$sq['p1_']);
            if(in_array(Tools::typeOf(@Url::$sq['p2_']),array('float','integer'))) $this->sP2=array(Url::$sq['p2_']);
            if(in_array(Tools::typeOf(@Url::$sq['p3_']),array('float','integer'))) $this->sP3=array(Url::$sq['p3_']);
            if(!empty($this->sP2) && !empty($this->sP3)){
                if(empty($this->sP1)) $this->sP1=$this->P1;
                $this->sMode=true;
            }
        }

        //уточняющие параметры
        if(@is_array(Url::$sq['_mp1'])) $this->_M1=array_keys(Url::$sq['_mp1']);
        elseif(Tools::typeOf(@Url::$sq['_mp1'])=='integer') $this->_M1=array(Url::$sq['_mp1']);

        if(@is_array(Url::$sq['_at'])) $this->_M2=array_keys(Url::$sq['_at']);
        elseif(Tools::typeOf(@Url::$sq['_at'])=='integer') $this->_M2=array(Url::$sq['_at']);

        if(Tools::typeOf(@Url::$sq['_mp3'])=='integer') $this->_M3=(string)Url::$sq['_mp3'];
        elseif(@is_array(Url::$sq['_mp3'])) {
            $a=array_keys(Url::$sq['_mp3']);
            if (count($a) == 2){
                $this->_M3='';
            }else $this->_M3=(string)array_pop($a);
        }

        if(@is_array(Url::$sq['_p1'])) $this->_P1=array_keys(Url::$sq['_p1']);
        elseif(in_array(Tools::typeOf(@Url::$sq['_p1']),array('float','integer'))) $this->_P1=array(Url::$sq['_p1']);

        if(@is_array(Url::$sq['_p3'])) $this->_P3=array_keys(Url::$sq['_p3']);
        elseif(in_array(Tools::typeOf(@Url::$sq['_p3']),array('float','integer'))) $this->_P3=array(Url::$sq['_p3']);

        if(@is_array(Url::$sq['_p2'])) $this->_P2=array_keys(Url::$sq['_p2']);
        elseif(in_array(Tools::typeOf(@Url::$sq['_p2']),array('float','integer'))) $this->_P2=array(Url::$sq['_p2']);

        if(!empty(Url::$sq['_vendor'])){
            $this->cc->que('brand_by_sname',Url::$sq['_vendor'],1,1);
            if($this->cc->qnum()) {
                $this->cc->next();
                $this->_brand_id=$this->cc->qrow['brand_id'];
            }
        }
        if(empty($this->_brand_id)) {
            if(@is_array(Url::$sq['_bids'])) $this->_brands=array_keys(Url::$sq['_bids']);
            elseif(Tools::typeOf(@Url::$sq['_bids'])=='integer') $this->_brands=array(Url::$sq['_bids']);
        }else $this->_brands=array($this->_brand_id);

        if(Tools::typeOf(@Url::$sq['_runflat'])=='integer') $this->_runflat=(string)Url::$sq['_runflat'];
        if(Tools::typeOf(@Url::$sq['_c_index'])=='integer') $this->_c_index=(string)Url::$sq['_c_index'];

        // все параметры в кучу
        $this->P1_=array_unique(array_merge($this->P1,$this->_P1));
        $this->P2_=array_unique(array_merge($this->P2,$this->_P2));
        $this->P3_=array_unique(array_merge($this->P3,$this->_P3));
        $this->M1_=array_unique(array_merge($this->M1,$this->_M1));
        $this->M2_=array_unique(array_merge($this->M2,$this->_M2));
        $this->brands_=array_unique(array_merge($this->brands,$this->_brands));

        if($this->_M3==='' && $this->M3==='') $this->M3_='';
        else $this->M3_=(string)max((int)$this->_M3,(int)$this->M3);

        if (!$this->c_index_)
        {
            if($this->_runflat=='' && $this->runflat==='') $this->runflat_='';
            else $this->runflat_=(string)max((int)$this->_runflat,(int)$this->runflat);
        } 
        if (!$this->runflat_) 
        {
            // ************************ new field: c_index *************************
            if($this->_c_index=='' && $this->c_index==='') $this->c_index='';
            else $this->c_index_=(string)max((int)$this->_c_index,(int)$this->c_index);
            // ************************ /new field: c_index *************************
        }
    }

    /*
    * получение массива с размерами
    */
    private function _cat()
    {
        $this->cat=array();
        $this->num=0;

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
        if($this->M3==='') $r['exFields']['MP3']=array(); else $r['M3']=$this->M3; // шип
        if($this->runflat!=='') $r['rf']=$this->runflat;
        if($this->c_index!=='') $r['c_index']=$this->c_index;

        if(!empty($this->_where)) $r['where']=array_merge($r['where'],$this->_where);
        if(!empty($this->_whereCat)) $r['where']=array_merge($r['where'],$this->_whereCat);
        if(!empty($this->_having)) $r['having']=array_merge($r['having'],$this->_having);

        if($this->hideTSCZero) $r['where'][]=$this->minQtyRadiusSQL;

        $r['sqlReturn']=0;
        $r['nolimits']=1;
        $r['ex']=1;

        //Tools::prn($r['catOrGroups']);
        $this->exnum=$this->cc->cat_view($r);
        //echo $this->cc->sql_query;
        //print_r($this->cc->ex_arr['brand']);

        $this->cc->sqlFree();
        $this->ex=$this->cc->ex_arr;
        unset($this->cc->ex_arr,$this->ex['MP2'][0],$this->ex['P1'][0],$this->ex['MP1'][0]);

        if(!$this->exnum) {
            GA::_event('Other','searchTyresNoResult',ltrim(@$_SERVER['REQUEST_URI'],'/'),'',true);
            return true;
        }

        //генерим живую форму уточняющего фильтра

        // пока не выбран полный размер x/x Rx выводим фильтр с выбором размера
        if(!empty($this->ex['P1']) || !empty($this->ex['P2']) || !empty($this->ex['P3'])){

            $this->filterHF=[];
            $this->filter=[
                'P1'=>[],
                'P2'=>[],
                'P3'=>[]
            ];
            $this->lf=array();
            $this->lfi=0;

            if(empty($this->ex_arr['P1']) && !empty($this->P1_)){
                $a=$this->P1_;
                $this->filter['P1'][]=array_shift($a);
            }elseif(!empty($this->ex['P1'])){
                $this->filter['P1']=array_keys($this->ex['P1']);
            }

            if(empty($this->ex_arr['P2']) && !empty($this->P2_)){
                $a=$this->P2_;
                $this->filter['P2'][]=array_shift($a);
            }elseif(!empty($this->ex['P2'])){
                $this->filter['P2']=array_keys($this->ex['P2']);
            }

            if(empty($this->ex_arr['P3']) && !empty($this->P3_)){
                $a=$this->P3_;
                $this->filter['P3'][]=array_shift($a);
            }elseif(!empty($this->ex['P3'])){
                $this->filter['P3']=array_keys($this->ex['P3']);
            }

            if (count(@$this->ex['MP1']) > 1) {
                $this->lfi++;
                ksort($this->ex['MP1']);
                $this->lf['mp1'] = array();
                $si = App_Route::_getUrl('tSearch') . '?';
                if (!empty($this->ex['MP1'][2])) $this->lf['mp1'][2] = array(
                    'chk' => in_array(2, $this->M1_) ? true : false,
                    'anc' => @$this->sezonNames5[2] . @$this->sezonIcos[2],
                    'id' => '_mp12',
                    'url' => $si . 'mp1=2'
                    );
                if (!empty($this->ex['MP1'][1])) $this->lf['mp1'][1] = array(
                    'chk' => in_array(1, $this->M1_) ? true : false,
                    'anc' => @$this->sezonNames5[1] . @$this->sezonIcos[1],
                    'id' => '_mp11',
                    'url' => $si . 'mp1=1'
                    );
                if (!empty($this->ex['MP1'][3])) $this->lf['mp1'][3] = array(
                    'chk' => in_array(3, $this->M1_) ? true : false,
                    'anc' => @$this->sezonNames5[3] . @$this->sezonIcos[3],
                    'id' => '_mp13',
                    'url' => $si . 'mp1=3'
                    );
            }

            if (count(@$this->ex['MP3']) > 1) {
                $this->lfi++;
                ksort($this->ex['MP3']);
                $this->lf['mp3'] = array();
                $si = App_Route::_getUrl('tSearch') . '?';
                if (!empty($this->ex['MP3'][0])) $this->lf['mp3'][0] = array(
                    'chk' => $this->M3_ === '0' ? true : false,
                    'anc' => "Нешипованные",
                    'id' => '_mp30',
                    'url' => $si . 'mp3=0'
                    );
                if (!empty($this->ex['MP3'][1])) $this->lf['mp3'][1] = array(
                    'chk' => $this->_M3 === '1' ? true : false,
                    'anc' => 'Шипованные <img src="/app/images/ship.png" alt="шипованные шины">',
                    'id' => '_mp31',
                    'url' => $si . 'mp3=1'
                    );
            }

            sort($this->filter['P1'],SORT_NUMERIC );
            sort($this->filter['P2'],SORT_NUMERIC );
            sort($this->filter['P3'],SORT_NUMERIC );

            // добавляем гет параметры в форму как hidden
            foreach (Url::$sq as $k => $v)
                if (in_array($k, array(
                    'at',
                    'vendor',
                    'mp1',
                    'mp2',
                    'mp3',
                    'q',
                    'ap'
                    )))
                    if (is_array($v)) {
                        foreach ($v as $k1 => $v1) $this->filterHF["{$k}[{$k1}]"] = $v1;
                    } else  $this->filterHF[$k] = $v;


        }else {

            // фильтр для полного размера

            $this->lf=array();
            $this->lfi=0;
            $this->lfh=array();


            if (count(@$this->ex['brand'][0]) > 1) {
                $this->lfi++;
                $this->lf['_bids'] = array();
                if (in_array(1, $this->M1)) $si = $this->tRoute[1]; elseif (in_array(2, $this->M1)) $si = $this->tRoute[2];
                elseif (in_array(3, $this->M1)) $si = $this->tRoute[3];
                else  $si = $this->tRoute[-1];
                foreach ($this->ex['brand'][0] as $k => $v) {
                    $this->lf['_bids'][$k] = array(
                        'chk' => in_array($k, $this->_brands) ? true : false,
                        'anc' => $v['name'],
                        'id' => '_bids' . $k,
                        'url' => $si . "/" . $v['sname'] . '.html'
                    );
                }
            }
            /*
            if(count(@$this->ex['P1'])>1){
            $this->lfi++;
            ksort($this->ex['P1']);
            $this->lf['_p1']=array();
            $si=App_Route::_getUrl('tSearch').'?';
            foreach($this->ex['P1'] as $k=>$v){
            $this->lf['_p1'][$k]=array(
            'chk'=>in_array($k,$this->_P1)?true:false,
            'anc'=>"R$k",
            'id'=>'_p1'.$this->makeId($k),
            'url'=>$si.'p1='.$k
            );
            }
            }
            */
            if (count(@$this->ex['MP1']) > 1) {
                $this->lfi++;
                ksort($this->ex['MP1']);
                $this->lf['_mp1'] = array();
                $si = App_Route::_getUrl('tSearch') . '?';
                if (!empty($this->ex['MP1'][2])) $this->lf['_mp1'][2] = array(
                    'chk' => in_array(2, $this->_M1) ? true : false,
                    'anc' => @$this->sezonNames5[2] . $this->sezonIcos[2],
                    'id' => '_mp12',
                    'url' => $si . 'mp1=2'
                    );
                if (!empty($this->ex['MP1'][1])) $this->lf['_mp1'][1] = array(
                    'chk' => in_array(1, $this->_M1) ? true : false,
                    'anc' => @$this->sezonNames5[1] . $this->sezonIcos[1],
                    'id' => '_mp11',
                    'url' => $si . 'mp1=1'
                    );
                if (!empty($this->ex['MP1'][3])) $this->lf['_mp1'][3] = array(
                    'chk' => in_array(3, $this->_M1) ? true : false,
                    'anc' => @$this->sezonNames5[3] . $this->sezonIcos[3],
                    'id' => '_mp13',
                    'url' => $si . 'mp1=3'
                    );
            }

            if (count(@$this->ex['MP3']) > 1) {
                $this->lfi++;
                ksort($this->ex['MP3']);
                $this->lf['_mp3'] = array();
                $si = App_Route::_getUrl('tSearch') . '?';
                if (!empty($this->ex['MP3'][0])) $this->lf['_mp3'][0] = array(
                    'chk' => $this->_M3 === '0' ? true : false,
                    'anc' => "Нешипованные",
                    'id' => '_mp30',
                    'url' => $si . 'mp3=0'
                    );
                if (!empty($this->ex['MP3'][1])) $this->lf['_mp3'][1] = array(
                    'chk' => $this->_M3 === '1' ? true : false,
                    'anc' => 'Шипованные <img src="/app/images/ship.png" alt="шипованные шины">',
                    'id' => '_mp31',
                    'url' => $si . 'mp3=1'
                    );
            }
            /*
            if (empty(Url::$sq['ap'])) {
            if (!empty($this->ex['MP2'][4]) && (!empty($this->ex['MP2'][1]) || !empty($this->ex['MP2'][2]))) {
            $this->ex['MP2'][1] = 1;
            $this->ex['MP2'][2] = 1;
            }
            unset($this->ex['MP2'][4]);
            if (count(@$this->ex['MP2']) > 1) {
            $this->lfi++;
            ksort($this->ex['MP2']);
            $this->lf['_mp2'] = array();
            $si = App_Route::_getUrl('tSearch') . '?';
            foreach ($this->ex['MP2'] as $k => $v) if ($k > 0) {
            $this->lf['_mp2'][$k] = array(
            'chk' => in_array($k, $this->_M2) ? true : false,
            'anc' => @$this->atNames3[$k] . " <span class=\"atype-ico$k\"></span>",
            'id' => '_at' . $k,
            'url' => $si . 'at=' . $k
            );
            }
            }
            }
            */
            // добавляем гет параметры в форму как hidden
            foreach (Url::$sq as $k => $v)
                if (in_array($k, array(
                    'p1',
                    'p2',
                    'p3',
                    'at',
                    'vendor',
                    'mp1',
                    'mp2',
                    'mp3',
                    'q',
                    'ap'
                    )))
                    if (is_array($v)) {
                        foreach ($v as $k1 => $v1) $this->lfh["{$k}[{$k1}]"] = $v1;
                    } else  $this->lfh[$k] = $v;

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

        if(!empty($this->_where)) $r['where']=array_merge($r['where'],$this->_where);
        if(!empty($this->_whereCat)) $r['where']=array_merge($r['where'],$this->_whereCat);
        if(!empty($this->_having)) $r['having']=array_merge($r['having'],$this->_having);

        if($this->hideTSCZero) $r['where'][]=$this->minQtyRadiusSQL;

        //добавляем к запросу все параметры
        if(!empty($this->P1_)) $r['P1']=array('list'=>$this->P1_);
        if(!empty($this->P2_)) $r['P2']=array('list'=>$this->P2_);
        if(!empty($this->P3_)) $r['P3']=array('list'=>$this->P3_);
        if(!empty($this->M1_)) $r['M1']=array('list'=>$this->M1_);
        $r['M3']=$this->M3_;
        if(!empty($this->M2_)) {
            if(in_array(1,$this->M2_) || in_array(2,$this->M2_)) $this->M2_[]=4;
            $r['M2']=array('list'=>$this->M2_);
        }
        if(!empty($this->brands_)) $r['brand_id']=array('list'=>$this->brands_);

        if($this->runflat_!=='') $r['rf']=$this->runflat_;
        if($this->c_index_!=='') $r['c_index']=$this->c_index_;

        $this->num=$this->cc->cat_view($r);
        $d=$this->cc->fetchAll();
        if($this->num) {
            $this->paginator=$this->cc->paginator(Url::$path,Url::$sq,@Url::$sq['page'],$this->num,$this->limit,'page',array(
                'active'=>	'<li class="active">{page}</li>',
                'noActive'=>'<li><a href="{url}">{page}</a></li>',
                'dots'=>	'<li>...</li>'
                ),5);

            if(!empty($this->filter)){
                $this->markir=[
                    'text'=>""
                ];
                if($this->num>100) {
                    $this->markir['text'] = "По Вашему запросу найдено слишком большое количество типоразмеров ({$this->num} шт.). Для более эффективного поиска уточните недостающие параметры шин: <b>";
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

    private function _catDouble()
    {
        $this->cat=array();
        $this->num=0;
        $this->lf=array();
        $this->lfi=0;
        $this->lfh=array();

        // первый запрос
        $r=array(
            'gr'=>1,
            'notH'=>1,
            'where'=>array(),
            'nolimits'=>1
        );

        if(!empty($this->brands_))   $r['brand_id']=array('list'=>$this->brands_); // бренд
        if(!empty($this->M1_))       $r['M1']=array('list'=>$this->M1_); // сезон
        if(!empty($this->M2_)) {
            if(in_array(1,$this->M2_) || in_array(2,$this->M2_)) $this->M2_[]=4;
            $r['M2']=array('list'=>$this->M2_);
        } // автотип
        if($this->M3_!=='')         $r['M3']=$this->M3_; // шип
        if(!empty($this->P1))       $r['P1']=array('list'=>$this->P1); // радиус
        if(!empty($this->P2))       $r['P2']=array('list'=>$this->P2);
        if(!empty($this->P3))       $r['P3']=array('list'=>$this->P3);
        if($this->runflat_!=='')     $r['rf']=$this->runflat;
        if($this->c_index_!=='')     $r['c_index']=$this->c_index;

        if($this->hideTSCZero) $r['where'][]=$this->minQtyRadiusSQL;

        $this->exnum1=$this->cc->cat_view($r);
        $r1=$this->cc->fetchAll('',MYSQLI_ASSOC);

        if(!$this->exnum1) return true; // если ничего не найдено, то спарок не будет - уходим от сюда

        // второй запрос
        $r=array(
            'gr'=>1,
            'notH'=>1,
            'where'=>array(),
            'nolimits'=>1
        );

        if(!empty($this->brands_))   $r['brand_id']=array('list'=>$this->brands_); // бренд
        if(!empty($this->M1_))       $r['M1']=array('list'=>$this->M1_); // сезон
        if(!empty($this->M2_))       $r['M2']=array('list'=>$this->M2_); // автотип
        if($this->M3_!=='')          $r['M3']=$this->M3_; // шип
        if(!empty($this->sP1))      $r['P1']=array('list'=>$this->sP1);
        if(!empty($this->sP2))      $r['P2']=array('list'=>$this->sP2);
        if(!empty($this->sP3))      $r['P3']=array('list'=>$this->sP3);
        if($this->runflat_!=='')     $r['rf']=$this->runflat;
        if($this->c_index_!=='')     $r['c_index']=$this->c_index;

        if($this->hideTSCZero) $r['where'][]=$this->minQtyRadiusSQL;

        $this->exnum2=$this->cc->cat_view($r);
        $r2=$this->cc->fetchAll('',MYSQLI_ASSOC);

        /* сливаем результаты
        группируем по размеру, ранфлету и XL, бренду, модели
        */
        $this->gsuf=array(); // эти суффиксы должны присуствовать в обеих типоразмерах
        $s=Data::get('cc_runflat_suffix');
        $this->gsuf=explode(';',$s);
        $this->gsuf[]='XL';

        $burl='/'.App_Route::_getUrl('tTipo').'/';

        $this->ex=array('brand'=>array(0=>array()),'MP1'=>array(),'MP2'=>array(),'MP3'=>array());

        foreach($r1 as $v1){
            foreach($r2 as $v2){
                if($v1['brand_id']==$v2['brand_id'])
                    if($v1['model_id']==$v2['model_id'])
                        if($this->checkSuffixes(Tools::unesc($v1['csuffix']), Tools::unesc($v2['csuffix']))){

                            // считаем что нашли спарку

                            $this->ex['brand'][0][$v1['brand_id']]=array('name'=>Tools::unesc($v1['bname']), 'sname'=>Tools::unesc($v1['brand_sname']));
                            $this->ex['MP1'][$v1['MP1']]=1;
                            $this->ex['MP2'][$v1['MP2']]=1;
                            $this->ex['MP3'][$v1['MP3']]=1;

                            if(empty($this->_brands) || in_array($v1['brand_id'],$this->_brands)) // проверка по уточняющим фильтрам
                                if(empty($this->_M1) || in_array($v1['MP1'],$this->_M1)){

                                    $this->cat[]=array(
                                        0=>$this->catRow($v1,$burl),
                                        1=>$this->catRow($v2,$burl)
                                    );
                                }
                        }
            }
        }

        $this->exnum=count($this->cat);

        //генерим живую форму уточняющего фильтра
        if(count(@$this->ex['brand'][0])>1){
            $this->lfi++;
            $this->lf['_bids']=array();
            if(in_array(1,$this->M1)) $si=$this->tRoute[1];
            elseif(in_array(2,$this->M1)) $si=$this->tRoute[2];
            elseif(in_array(3,$this->M1)) $si=$this->tRoute[3];
            else  $si=$this->tRoute[-1];
            foreach($this->ex['brand'][0] as $k=>$v){
                $this->lf['_bids'][$k]=array(
                    'chk'=>in_array($k,$this->_brands)?true:false,
                    'anc'=>$v['name'],
                    'id'=>'_bids'.$k,
                    'url'=>$si."/".$v['sname'].'.html'
                );
            }
        }
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
                    'chk'=>$this->_M3==='0'?true:false,
                    'anc'=>"Нешипованные",
                    'id'=>'_mp30',
                    'url'=>$si.'mp3=0'
                );
            if(!empty($this->ex['MP3'][1]))
                $this->lf['_mp3'][1]=array(
                    'chk'=>$this->_M3==='1'?true:false,
                    'anc'=>'Шипованные <img src="/app/images/ship.png" alt="шипованные шины">',
                    'id'=>'_mp31',
                    'url'=>$si.'mp3=1'
                );
        }

        if(empty(Url::$sq['ap'])){
            if(!empty($this->ex['MP2'][4]) && (!empty($this->ex['MP2'][1]) || !empty($this->ex['MP2'][2]))) {
                $this->ex['MP2'][1]=1;
                $this->ex['MP2'][2]=1;
            }
            unset($this->ex['MP2'][4]);
            if(count(@$this->ex['MP2'])>1){
                $this->lfi++;
                ksort($this->ex['MP2']);
                $this->lf['_mp2']=array();
                $si=App_Route::_getUrl('tSearch').'?';
                foreach($this->ex['MP2'] as $k=>$v) if($k>0) {
                    $this->lf['_mp2'][$k]=array(
                        'chk'=>in_array($k,$this->_M2)?true:false,
                        'anc'=>@$this->atNames3[$k]." <span class=\"atype-ico$k\"></span>",
                        'id'=>'_at'.$k,
                        'url'=>$si.'at='.$k
                    );
                }
            }
        }
        // добавляем гет параметры в форму как hidden
        foreach(Url::$sq as $k=>$v)
            if(in_array($k,array('p1','p2','p3','at','vendor','mp1','mp2','mp3','q','ap','p1_','p2_','p3_')))
                if(is_array($v)){
                    foreach($v as $k1=>$v1) $this->lfh["{$k}[{$k1}]"]=$v1;
                }else  $this->lfh[$k]=$v;



            return true;
    }

    /*
    * основной вход в поиск
    */
    public function search()
    {

        $this->_routeParam();

        $this->view('catalog/tyres/search');

        if($this->sMode) return $this->doubleSearch();

        // тип отображения результатов поиска
        if(@$_COOKIE['stype']=='lenta') $this->searchTpl='catalog/tyres/searchLenta'; else $this->searchTpl='catalog/tyres/searchBlock';

        if(true!==($res=$this->_cat())) return $res;

        reset($this->M1);
        reset($this->M2);
        reset($this->P1);
        reset($this->P2);
        reset($this->P3);
        reset($this->brands);

        $this->size1=$this->size2=$this->radius='';

        if(count($this->P3)==1) {
            $this->size1.=current($this->P3);
            $this->size2.=current($this->P3);
        }
        if(count($this->P2)==1) {
            $this->size1.='/'.current($this->P2);
            $this->size2.=' '.current($this->P2);
        }
        if(count($this->P1)==1) {
            $this->size1.=" R".current($this->P1);
            $this->size2.=' R'.current($this->P1);
        }
        $this->size1=trim($this->size1);
        $this->size2=trim($this->size2);

        $this->sezat1=$this->sezat2=$this->sezat3=$this->sezat4=$this->sezat5='';
        if(count($this->M1)==1) $this->sezat1=@$this->sezonNames4[current($this->M1)];
        if($this->M3) $this->sezat1.=' шипованные шины'; else $this->sezat1.=' шины';
        if(!empty($this->M2)) $this->sezat1.=' '.@$this->atNames1[current($this->M2)];
        $this->sezat1=trim(Tools::cutDoubleSpaces($this->sezat1));

        if(count($this->M1)==1) $this->sezat2=@$this->sezonNames3[current($this->M1)];
        if($this->M3) $this->sezat2.=' шипованнная резина'; else $this->sezat2.=' резина';
        if(!empty($this->M2)) $this->sezat2.=' '.@$this->atNames1[current($this->M2)];
        $this->sezat2=trim(Tools::cutDoubleSpaces($this->sezat2));

        if(count($this->M1)==1) $this->sezat3=@$this->sezonNames8[current($this->M1)];
        if($this->M3) $this->sezat3.=' шипованнную резину'; else $this->sezat3.=' резину';
        if(!empty($this->M2)) $this->sezat3.=' '.@$this->atNames1[current($this->M2)];
        $this->sezat3=trim(Tools::cutDoubleSpaces($this->sezat3));

        if(count($this->M1)==1) $this->sezat4=@$this->sezonNames6[current($this->M1)];
        if($this->M3) $this->sezat4.=' шипованнных шин'; else $this->sezat4.=' шин';
        if(!empty($this->M2)) $this->sezat4.=' '.@$this->atNames1[current($this->M2)];
        $this->sezat4=trim(Tools::cutDoubleSpaces($this->sezat4));

        if(count($this->M1)==1) $this->sezat5=@$this->sezonNames7[current($this->M1)];
        if($this->M3) $this->sezat5.=' шипованнной резины'; else $this->sezat5.=' резины';
        if(!empty($this->M2)) $this->sezat5.=' '.@$this->atNames1[current($this->M2)];
        $this->sezat5=trim(Tools::cutDoubleSpaces($this->sezat5));

        if($this->runflat_) {
            $this->runflat1="Runflat";
            $this->runflat2="Ранфлэт";
        }

        if($this->c_index_) {
            $this->c_index1="индекс С";
            $this->c_index2="легкогрузовая шина (индекс C)";
        }

        $this->width=@current($this->P3);
        $this->profile=@current($this->P2);
        $this->diametr=@current($this->P1);
        $sezonId=@current($this->M1);
        $this->sezonName3=@$this->sezonNames3[$sezonId];
        $this->sezonName4=@$this->sezonNames4[$sezonId];
        $this->sezonName6=@$this->sezonNames6[$sezonId];
        $this->sezonName7=@$this->sezonNames7[$sezonId];
        $this->sezonName8=@$this->sezonNames8[$sezonId];

        if(!empty($this->ab->fname) && !empty(Url::$sq['ap'])){
            $this->title=$this->ab->fname.': '.$this->sezat1." {$this->balt1} {$this->size1} {$this->runflat1} {$this->c_index1}, наличие, цена. ".Tools::mb_ucfirst($this->sezat2)." {$this->bname}  {$this->size2} {$this->runflat2} с доставкой";
            $this->_title="{$this->sezat1} {$this->bname} {$this->size1} {$this->runflat1} {$this->c_index1} для {$this->ab->fname}";
        }else{

            if(empty($this->P2) && empty($this->P3) && !empty($this->P1)) {
                // радиус [+ сезон]
                $this->title="{$this->sezat1} {$this->balt1} {$this->runflat1} {$this->c_index1} R{$this->diametr} - купить {$this->sezat3} {$this->balt1} {$this->runflat1} {$this->c_index1} {$this->diametr} радиуса по выгодной цене в Москве.";
                $this->_title="{$this->sezat1} {$this->bname} {$this->size1} {$this->runflat1} {$this->c_index1}";
                $this->description="Продажа {$this->sezat4} {$this->balt1} R {$this->diametr} в интернет магазине Дилижанс по лучшим ценам в Москве и Санкт-Петербурге. Огромный выбор {$this->sezat5} {$this->balt1} {$this->diametr} радиусом от лучших производителей.";
                $this->keywords="{$this->sezat2} {$this->balt} {$this->size2} {$this->runflat1} {$this->c_index1}, {$this->sezat1} {$this->bname} {$this->size1} {$this->runflat2}  купить наличие цена продажа фото";

            }elseif(!empty($this->P2) && !empty($this->P3) && !empty($this->P1)) {
                //размер [+ сезон]
                $this->title="{$this->sezat1} {$this->bname} {$this->size1} {$this->runflat1} {$this->c_index1} - купить {$this->sezat3} {$this->balt1} {$this->size1} {$this->runflat1} {$this->c_index1} по выгодной цене в Москве.";
                $this->_title="{$this->sezat1} {$this->bname} {$this->size1} {$this->runflat1} {$this->c_index1}";
                $this->description="Продажа {$this->sezat4} {$this->bname} {$this->size1} {$this->runflat1} {$this->c_index1} в интернет магазине Дилижанс по лучшим ценам в Москве и Санкт-Петербурге. Огромный выбор {$this->sezat5} {$this->balt1} {$this->size1} {$this->runflat1} {$this->c_index1} от лучших производителей.";
                $this->keywords="{$this->sezat2} {$this->balt} {$this->size2} {$this->runflat1} {$this->c_index1}, {$this->sezat1} {$this->bname} {$this->size1} {$this->runflat2}  купить наличие цена продажа фото";

            } else {
                //все остальное
                $this->title = 'Купить ' . $this->sezat1 . " {$this->balt1} {$this->size1} {$this->runflat1} {$this->c_index1}, наличие, цена. " . Tools::mb_ucfirst($this->sezat2) . " {$this->bname}  {$this->size2} {$this->runflat2} с доставкой";
                $this->keywords="{$this->sezat2} {$this->balt} {$this->size2} {$this->runflat1} {$this->c_index1}, {$this->sezat1} {$this->bname} {$this->size1} {$this->runflat2}  купить наличие цена продажа фото";
                $this->description="Здесь в каталоге вы найдете  {$this->sezat2} {$this->balt} {$this->size2} {$this->runflat2}. Актуальное наличие на складе, фото каталог, подбор по размеру, купить {$this->sezat1} {$this->bname} {$this->size1} {$this->runflat1} {$this->c_index1} с доставкой по Москве и регионам России";
                $this->_title="{$this->sezat1} {$this->bname} {$this->size1} {$this->runflat1} {$this->c_index1}";
            }

        }

        $this->title=Tools::mb_ucfirst($this->title);
        $this->_title=Tools::mb_ucfirst($this->_title);
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
            $this->s_info_str = '<div class="search_q_info_str" style="margin-top: 15px;">';
            $this->s_info_str .= trim("По вашему запросу: ".$this->_title);
            $this->s_info_str .= '</div>';
        }
        // *****
        if(empty($this->cat)) $this->qtext=Tools::mb_ucfirst(trim(@$this->sezonNames6[current($this->M1)].($this->M3?' шипованных ':'')." шин {$this->bname} {$this->size1}  в данный момент нет в наличии"));


        $this->breadcrumbs['шины']=array('/'.App_Route::_getUrl('tCat').'.html','купить шины');

        if($sezonId) $this->breadcrumbs[$this->sezonNames4[$sezonId].' шины']=array($this->tRoute[$sezonId].'.html', 'каталог '.$this->sezonNames6[$sezonId].' шин');
        if($this->brand_id) $this->breadcrumbs[$this->sezonNames4[$sezonId].' шины '.$this->balt1]=array($this->tRoute[$sezonId].'/'.$this->brand_sname.'.html', 'каталог '.$this->sezonNames6[$sezonId]." шин {$this->balt}");
        $this->breadcrumbs["{$this->sezat2} {$this->balt1} {$this->size1} {$this->runflat2}"]='';

        // сео шаблоны

        //  [бренд] [ширина]/[высота]R[радиус] (с сезоном и без) (с типом авто и без) (с шипами или без)
        if($this->brand_id && count($this->P3)==1 && count($this->P2)==1 && count($this->P1)==1){
            $this->bottomText=$this->ss->getDoc('tpl_tyres_brand_full_size$10');
        }else
            //  [ширина]/[высота]R[радиус] (с сезоном и без) (с типом авто и без) (с шипами или без)
            if(empty($this->brand_id) && count($this->P3)==1 && count($this->P2)==1 && count($this->P1)==1){
                $this->bottomText=$this->ss->getDoc('tpl_tyres_full_size$10');
            }else
                //  [R[радиус] (с сезоном и без) (с типом авто и без) (с шипами или без)
                if(empty($this->brand_id) && empty($this->P3)==1 && empty($this->P2)==1 && count($this->P1)==1){
                    $this->bottomText=$this->ss->getDoc('tpl_tyres_radius$10');
                }

                //перелинковка
                $this->rlinks=[];
        if(count(@$this->ex['MP1'])>1){
            // сезон + рад
            $dd=[];
            foreach($this->ex['MP1'] as $k=>$v){
                switch($k) {
                    case 1:
                        $dd[] = [
                            'anc' => "Летние шины R{$this->diametr}",
                            'title' => "купить летние шины R{$this->diametr}",
                            'url' =>'/'.App_Route::_getUrl('tSearch').".html?p1={$this->diametr}&mp1=1"
                        ];
                        break;
                    case 2:
                        $dd[] = [
                            'anc' => "Зимние шины R{$this->diametr}",
                            'title' => "купить зимние шины R{$this->diametr}",
                            'url' =>'/'.App_Route::_getUrl('tSearch').".html?p1={$this->diametr}&mp1=2"
                        ];
                        break;
                    case 3:
                        $dd[] = [
                            'anc' => "Всесезонные шины R{$this->diametr}",
                            'title' => "купить всесезонные шины R{$this->diametr}",
                            'url' =>'/'.App_Route::_getUrl('tSearch').".html?p1={$this->diametr}&mp1=3"
                        ];
                        break;
                }
            }
            $this->rlinks[]=['label'=>'Быстрый переход в каталог шин по сезону', 'listyle'=>"width:150px", 'data'=>$dd];
        }

        if(count(@$this->ex['P123'])>1 && count($this->ex['P123'])<50){
            // размер
            $dd=[];
            foreach($this->ex['P123'] as $k=>$v){
                if(preg_match("~([0-9]{2,})-([0-9]{2,})-([0-9]{3})~i",$k, $ex))
                    $dd[] = [
                        'anc' => "{$ex[3]}/{$ex[2]} r{$ex[1]}",
                        'title' => "купить шины {$ex[3]}/{$ex[2]} r{$ex[1]}",
                        'url' =>'/'.App_Route::_getUrl('tSearch').".html?p3={$ex[3]}&p2={$ex[2]}&p1={$ex[1]}"
                    ];
            }
            if(!empty($dd))  $this->rlinks[]=['label'=>'Быстрый переход в каталог шин по типоразмеру', 'listyle'=>"", 'data'=>$dd];
        }
        //Tools::prn($this->ex);

        if(count(@$this->ex['brand'][0])>1 && !empty($this->profile) && !empty($this->width) && !empty($this->diametr)){
            //бренды
            $dd=[];
            foreach($this->ex['brand'][0] as $k=>$v){
                $dd[] = [
                    'anc' => "шины {$v['name']}",
                    'title' => "купить шины {$v['name']}",
                    'url' =>$this->tRoute[0]."/{$v['sname']}.html"
                ];
            }
            $this->rlinks[]=['label'=>'Быстрый переход в каталог шин по производителю', 'listyle'=>"width:120px", 'data'=>$dd];
        }

        $this->_sidebar();
    }

    private function doubleSearch()
    {
        $this->searchTpl='catalog/tyres/searchDouble';

        if(true!==($res=$this->_catDouble())) return $res;

        reset($this->M1);
        reset($this->M2);
        reset($this->P1);
        reset($this->P2);
        reset($this->P3);
        reset($this->sP1);
        reset($this->sP2);
        reset($this->sP3);
        reset($this->brands);

        $this->size1=$this->size2='';
        $this->size1_=$this->size2_='';

        if(count($this->P3)==1) {
            $this->size1.=current($this->P3);
            $this->size2.=current($this->P3);
            $this->size1_.=current($this->sP3);
            $this->size2_.=current($this->sP3);
        }
        if(count($this->P2)==1) {
            $this->size1.='/'.current($this->P2);
            $this->size2.=' '.current($this->P2);
            $this->size1_.='/'.current($this->sP2);
            $this->size2_.=' '.current($this->sP2);
        }
        if(count($this->P1)==1) {
            $this->size1.=" R".current($this->P1);
            $this->size2.=' R '.current($this->P1);
            $this->size1_.=" R".current($this->sP1);
            $this->size2_.=' R '.current($this->sP1);
        }
        $this->size1=trim($this->size1);
        $this->size2=trim($this->size2);
        $this->size1_=trim($this->size1_);
        $this->size2_=trim($this->size2_);

        $this->sezat1='';
        if(count($this->M1)==1) $this->sezat1=@$this->sezonNames4[current($this->M1)];
        if($this->M3) $this->sezat1.=' шипованные шины'; else $this->sezat1.=' шины';
        if(count($this->M2)==1) $this->sezat1.=' '.@$this->atNames1[current($this->M2)];
        $this->sezat1=trim(Tools::cutDoubleSpaces($this->sezat1));

        $this->sezat2='';
        if(count($this->M1)==1) $this->sezat2=@$this->sezonNames3[current($this->M1)];
        if($this->M3) $this->sezat2.=' шипованнная резина'; else $this->sezat2.=' резина';
        if(count($this->M2)==1) $this->sezat2.=' '.@$this->atNames1[current($this->M2)];
        $this->sezat2=trim(Tools::cutDoubleSpaces($this->sezat2));

        if($this->runflat_) {
            $this->runflat1="Runflat";
            $this->runflat2="Ранфлэт";
        }

        if($this->c_index_) {
            $this->c_index1="индекс C";
            $this->c_index2="легкогрузовая шина (индекс C)";
        }

        if(!empty($this->ab->fname) && !empty(Url::$sq['ap'])){
            $this->title=$this->ab->fname.': '.$this->sezat1." {$this->balt1} {$this->runflat1} {$this->c_index1} {$this->size1} - {$this->size1_}, наличие, цена. ".Tools::mb_ucfirst($this->sezat2)." {$this->bname} {$this->size2} - {$this->size2_} {$this->runflat2} с доставкой";
            $this->_title=Tools::mb_ucfirst("{$this->sezat1} {$this->bname} {$this->runflat1} {$this->c_index1} {$this->size1} в спарке с {$this->size1_} для {$this->ab->fname}");
        }else{
            $this->title='Купить '.$this->sezat1." {$this->balt1} {$this->runflat1} {$this->c_index1} {$this->size1} - {$this->size1_}, наличие, цена. ".Tools::mb_ucfirst($this->sezat2)." {$this->bname} {$this->size2} - {$this->size2_} {$this->runflat2} с доставкой";
            $this->_title="Результат поиска: {$this->sezat1} {$this->bname} {$this->runflat1} {$this->c_index1} {$this->size1} в спарке с {$this->size1_}";
        }

        if(empty($this->cat)) {
            $this->qtext="К сожалению, мы не можем сейчас вам предложить спаренные размеры {$this->size1} с {$this->size1_} ".trim(@$this->sezonNames6[current($this->M1)].($this->M3?' шипованных ':'')." шин {$this->bname}.");
        }

        $this->width=@current($this->P3);
        $this->profile=@current($this->P2);
        $this->diametr=@current($this->P1);
        $sezonId=@current($this->M1);
        $this->sezonName3=@$this->sezonNames3[$sezonId];
        $this->sezonName4=@$this->sezonNames4[$sezonId];
        $this->sezonName6=@$this->sezonNames6[$sezonId];
        $this->sezonName7=@$this->sezonNames7[$sezonId];
        $this->sezonName8=@$this->sezonNames8[$sezonId];

        $this->keywords="{$this->sezat2} {$this->balt} {$this->size2} {$this->size2_}, {$this->sezat1} {$this->bname} {$this->size1} {$this->size1_} {$this->runflat1} {$this->c_index1} купить наличие цена продажа фото";
        $this->description="Здесь в каталоге вы найдете {$this->sezat2} {$this->balt} {$this->runflat1} {$this->c_index1} {$this->size2} в спарке с {$this->size2_}. Актуальное наличие на складе, фото каталог, подбор по размеру, купить {$this->sezat1} {$this->bname} {$this->size1} с {$this->size1_} с доставкой по Москве и регионам России";

        $this->breadcrumbs['шины']=array('/'.App_Route::_getUrl('tCat').'.html','купить шины');

        if($sezonId) $this->breadcrumbs[$this->sezonNames4[$sezonId].' шины']=array($this->tRoute[$sezonId].'.html', 'каталог '.$this->sezonNames6[$sezonId].' шин');
        if($this->brand_id) $this->breadcrumbs[$this->sezonNames4[$sezonId].' шины '.$this->balt1]=array($this->tRoute[$sezonId].'/'.$this->brand_sname.'.html', 'каталог '.$this->sezonNames6[$sezonId]." шин {$this->balt}");
        $this->breadcrumbs["{$this->sezat2} {$this->balt1} {$this->runflat1} {$this->c_index1} {$this->size1} - {$this->size1_}"]='';

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

    /*
    * поиск для живого фильтра
    */
    public function axSearch()
    {
        //sleep(2);

        $changeVars=(int)@$_REQUEST['chVars'];

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
        }

        $this->_routeParam();

        if($this->sMode) return $this->axSearchDouble();

        $time1=Tools::getMicroTime();

        $groups=@$_REQUEST['groups'];
        
        // сначала получаем кол-во размеров применяя все параметры

        $r=array(
            'gr'=>1,
            'notH'=>1,
            'nolimits'=>1,
            'count'=>1,
            'where'=>array(),
            'having'=>array());

        if(!empty($this->P2_)) $r['P2']=array('list'=>$this->P2_);
        if(!empty($this->P3_)) $r['P3']=array('list'=>$this->P3_);
        if(!empty($this->M1_)) $r['M1']=array('list'=>$this->M1_); // сезон
        if(!empty($this->M2_)) {
            if(in_array(1,$this->M2_) || in_array(2,$this->M2_)) $this->M2_[]=4;
            $r['M2']=array('list'=>$this->M2_);
        } // автотип
        if(!empty($this->P1_)) $r['P1']=array('list'=>$this->P1_); // радиус
        if(!empty($this->brands_)) $r['brand_id']=array('list'=>$this->brands_); // бренд
        if($this->M3_!=='') $r['M3']=$this->M3_; // шип
        if($this->runflat_!=='') $r['rf']=$this->runflat_;
        if($this->c_index_!=='') $r['c_index']=$this->c_index_;

        // where
        if($this->hideTSCZero) $r['where'][]=$this->minQtyRadiusSQL;
        if(!empty($this->_where)) $r['where']=array_merge($r['where'],$this->_where);
        if(!empty($this->_whereCat)) $r['where']=array_merge($r['where'],$this->_whereCat);

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
                    if($this->hideTSCZero) $r['where'][]=$this->minQtyRadiusSQL;
                    if(!empty($this->_where)) $r['where']=array_merge($r['where'],$this->_where);
                    if(!empty($this->_whereCat)) $r['where']=array_merge($r['where'],$this->_whereCat);

                    // добавляем все жесткие параметры (1)
                    if(!empty($this->M1)) $r['M1']=array('list'=>$this->M1); else unset($r['M1']); // сезон
                    if(!empty($this->M2)) $r['M2']=array('list'=>$this->M2); else unset($r['M2']); // автотип
                    if(!empty($this->P1)) $r['P1']=array('list'=>$this->P1); else unset($r['P1']); // радиус
                    if(!empty($this->P2)) $r['P2']=array('list'=>$this->P2); else unset($r['P2']); // высота
                    if(!empty($this->P3)) $r['P3']=array('list'=>$this->P3); else unset($r['P3']); // ширина

                    if(!empty($this->brands)) $r['brand_id']=array('list'=>$this->brands); else unset($r['brand_id']); // бренд

                    if($this->M3!=='') $r['M3']=$this->M3;  else unset($r['M3']);// шип

                    if($this->runflat!=='') $r['rf']=$this->runflat; else unset($r['rf']);
                    if($this->c_index!=='') $r['c_index']=$this->c_index; else unset($r['c_index']);

                    // добавляем уточняющие параметры из того же состава что и жесткие (1)
                    $r['groupby']='';
                    $n=0;
                    switch($group){

                        case '_p3':
                            if(!empty($this->_P1))  $r['P1']=array('list'=>$this->P1_); // радиус
                            if(!empty($this->_P2))  $r['P2']=array('list'=>$this->P2_); // высота
                            if(!empty($this->_M1))  $r['M1']=array('list'=>$this->M1_); // сезон
                            if(!empty($this->_M2))  $r['M2']=array('list'=>$this->M2_); // автотип
                            if(!empty($this->_brands))  $r['brand_id']=array('list'=>$this->brands_); // бренд
                            if($this->_M3!=='') $r['M3']=$this->M3_;  // шип
                            if($this->_runflat!=='') $r['rf']=$this->runflat_;
                            if($this->_c_index!=='') $r['c_index']=$this->c_index_;
                            $r['fields']="cc_cat.P3+'0' AS FF";
                            $r['groupby']='FF';
                            break;

                        case '_p2':
                            if(!empty($this->_P1))  $r['P1']=array('list'=>$this->P1_); // радиус
                            if(!empty($this->_P3))  $r['P3']=array('list'=>$this->P3_); // ширина
                            if(!empty($this->_M1))  $r['M1']=array('list'=>$this->M1_); // сезон
                            if(!empty($this->_M2))  $r['M2']=array('list'=>$this->M2_); // автотип
                            if(!empty($this->_brands))  $r['brand_id']=array('list'=>$this->brands_); // бренд
                            if($this->_M3!=='') $r['M3']=$this->M3_;  // шип
                            if($this->_runflat!=='') $r['rf']=$this->runflat_;
                            if($this->_c_index!=='') $r['c_index']=$this->c_index_;
                            $r['fields']="cc_cat.P2+'0' AS FF";
                            $r['groupby']='FF';
                            break;

                        case '_p1':
                            if(!empty($this->_P2))  $r['P2']=array('list'=>$this->P2_); // высота
                            if(!empty($this->_P3))  $r['P3']=array('list'=>$this->P3_); // ширина
                            if(!empty($this->_M1))  $r['M1']=array('list'=>$this->M1_); // сезон
                            if(!empty($this->_M2))  $r['M2']=array('list'=>$this->M2_); // автотип
                            if(!empty($this->_brands))  $r['brand_id']=array('list'=>$this->brands_); // бренд
                            if($this->_M3!=='') $r['M3']=$this->M3_;  // шип
                            if($this->_runflat!=='') $r['rf']=$this->runflat_;
                            if($this->_c_index!=='') $r['c_index']=$this->c_index_;
                            $r['fields']="cc_cat.P1+'0' AS FF";
                            $r['groupby']='FF';
                            break;

                        case '_mp1':
                            if(!empty($this->_P1))  $r['P1']=array('list'=>$this->P1_); // радиус
                            if(!empty($this->_P2))  $r['P2']=array('list'=>$this->P2_); // высота
                            if(!empty($this->_P3))  $r['P3']=array('list'=>$this->P3_); // ширина
                            if(!empty($this->_M2))  $r['M2']=array('list'=>$this->M2_); // автотип
                            if(!empty($this->_brands))  $r['brand_id']=array('list'=>$this->brands_); // бренд
                            if($this->_M3!=='') $r['M3']=$this->M3_;  // шип
                            if($this->_runflat!=='') $r['rf']=$this->runflat_;
                            if($this->_c_index!=='') $r['c_index']=$this->c_index_;
                            $r['fields']='cc_model.P1 AS FF';
                            $r['groupby']='FF';
                            break;

                        case '_mp3':
                            if(!empty($this->_P1))  $r['P1']=array('list'=>$this->P1_); // радиус
                            if(!empty($this->_P2))  $r['P2']=array('list'=>$this->P2_); // высота
                            if(!empty($this->_P3))  $r['P3']=array('list'=>$this->P3_); // ширина
                            if(!empty($this->_M1))  $r['M1']=array('list'=>$this->M1_); // сезон
                            if(!empty($this->_M2))  $r['M2']=array('list'=>$this->M2_); // автотип
                            if(!empty($this->_brands))  $r['brand_id']=array('list'=>$this->brands_); // бренд
                            if($this->_runflat!=='') $r['rf']=$this->runflat_;
                            if($this->_c_index!=='') $r['c_index']=$this->c_index_;
                            $r['fields']='cc_model.P3 AS FF';
                            $r['groupby']='FF';
                            break;

                        case '_vendor':
                        case '_bids':
                            if(!empty($this->_P1))  $r['P1']=array('list'=>$this->P1_); // радиус
                            if(!empty($this->_P2))  $r['P2']=array('list'=>$this->P2_); // высота
                            if(!empty($this->_P3))  $r['P3']=array('list'=>$this->P3_); // ширина
                            if(!empty($this->_M1))  $r['M1']=array('list'=>$this->M1_); // сезон
                            if(!empty($this->_M2))  $r['M2']=array('list'=>$this->M2_); // автотип
                            if($this->_M3!=='') $r['M3']=$this->M3_;  // шип
                            if($this->_runflat!=='') $r['rf']=$this->runflat_;
                            if($this->_c_index!=='') $r['c_index']=$this->c_index_;
                            $r['fields']='cc_brand.brand_id AS FF';
                            $r['groupby']='FF';
                            break;

                        case '_at':
                            if(!empty($this->_P1))  $r['P1']=array('list'=>$this->P1_); // радиус
                            if(!empty($this->_P2))  $r['P2']=array('list'=>$this->P2_); // высота
                            if(!empty($this->_P3))  $r['P3']=array('list'=>$this->P3_); // ширина
                            if(!empty($this->_M1))  $r['M1']=array('list'=>$this->M1_); // сезон
                            if(!empty($this->_brands))  $r['brand_id']=array('list'=>$this->brands_); // бренд
                            if($this->_M3!=='') $r['M3']=$this->M3_;  // шип
                            if($this->_runflat!=='') $r['rf']=$this->runflat_;
                            if($this->_c_index!=='') $r['c_index']=$this->c_index_;
                            $r['fields']='cc_model.P2 AS FF';
                            $r['groupby']='FF';
                            break;

                        case '_runflat': 
                            if($this->_c_index==='') 
                            {
                                $r['rf']=1;
                                $r['c_index']=''; // взаимоисключающие
                                if(!empty($this->_P1))  $r['P1']=array('list'=>$this->P1_); // радиус
                                if(!empty($this->_P2))  $r['P2']=array('list'=>$this->P2_); // высота
                                if(!empty($this->_P3))  $r['P3']=array('list'=>$this->P3_); // ширина
                                if(!empty($this->_M1))  $r['M1']=array('list'=>$this->M1_); // сезон
                                if(!empty($this->_M2))  $r['M2']=array('list'=>$this->M2_); // автотип
                                if(!empty($this->_brands))  $r['brand_id']=array('list'=>$this->brands_); // бренд
                                if($this->_M3!=='') $r['M3']=$this->M3_;  // шип
                                $r['count']=1;
                                $nn=$this->cc->cat_view($r);
                                $this->r['sql']=$this->cc->sql_query;
                                if($nn) $this->r['formdata']["{$group}"]=1; 
                            }
                            break;
                        case '_c_index':  
                            if($this->_runflat==='')
                            {
                                $r['c_index']=1;
                                $r['rf']=''; // взаимоисключающие
                                if(!empty($this->_P1))  $r['P1']=array('list'=>$this->P1_); // радиус
                                if(!empty($this->_P2))  $r['P2']=array('list'=>$this->P2_); // высота
                                if(!empty($this->_P3))  $r['P3']=array('list'=>$this->P3_); // ширина
                                if(!empty($this->_M1))  $r['M1']=array('list'=>$this->M1_); // сезон
                                if(!empty($this->_M2))  $r['M2']=array('list'=>$this->M2_); // автотип
                                if(!empty($this->_brands))  $r['brand_id']=array('list'=>$this->brands_); // бренд
                                if($this->_M3!=='') $r['M3']=$this->M3_;  // шип
                                $r['count']=1;
                                $nn=$this->cc->cat_view($r);
                                $this->r['sql']=$this->cc->sql_query;
                                if($nn) $this->r['formdata']["{$group}"]=1;  
                            }
                            break;
                    }
                    if(!empty($r['groupby']) && empty($r['count'])) {
                        $n=$this->cc->cat_view($r);
                        //$this->r['sql']=$this->cc->sql_query;
                    }     
                    if($n) {
                        while($this->cc->next()!==false)
                            if($group=='_mp3' && !in_array(2,$this->M1_) && (in_array(1,$this->M1_) || in_array(3,$this->M1_))){
                                //блокируем "нешип" при выборе  лета и/или всесезонки
                            }
                            elseif($group!='_at')
                                $this->r['formdata'][$group.$this->makeId($this->cc->qrow['FF'])]=1;
                            else
                                if($this->cc->qrow['FF']==4){
                                    $this->r['formdata']["{$group}1"]=1;
                                    $this->r['formdata']["{$group}2"]=1;
                                }else
                                    $this->r['formdata'][$group.$this->makeId($this->cc->qrow['FF'])]=1;
                    }


            }
        }

        $this->r['queryTime']=Tools::getMicroTime()-$time1;

    }

    private function axSearchDouble()
    {
        $time1=Tools::getMicroTime();

        $groups=@$_REQUEST['groups'];

        /*
        * делаем выборки с уточняющими параметрами для получения tn
        */

        $this->r['tn']=0;
        $this->r['formdata']=array();
        $this->r['sMode']=1;

        // первый запрос
        $r=array(
            'gr'=>1,
            'fields'=>'cc_brand.brand_id, cc_model.model_id, cc_cat.suffix AS csuffix',
            'notH'=>1,
            'where'=>array(),
            'nolimits'=>1
        );

        if(!empty($this->brands_))   $r['brand_id']=array('list'=>$this->brands_); // бренд
        if(!empty($this->M1_))       $r['M1']=array('list'=>$this->M1_); // сезон
        if(!empty($this->M2_)) {
            if(in_array(1,$this->M2_) || in_array(2,$this->M2_)) $this->M2_[]=4;
            $r['M2']=array('list'=>$this->M2_);
        } // автотип
        if($this->M3_!=='')          $r['M3']=$this->M3_; // шип
        if(!empty($this->P1))       $r['P1']=array('list'=>$this->P1); // радиус
        if(!empty($this->P2))       $r['P2']=array('list'=>$this->P2);
        if(!empty($this->P3))       $r['P3']=array('list'=>$this->P3);
        if($this->runflat_!=='')    $r['rf']=$this->runflat_;
        if($this->c_index_!=='')    $r['rf']=$this->c_index_;

        if($this->hideTSCZero) $r['where'][]=$this->minQtyRadiusSQL;

        $exnum1=$this->cc->cat_view($r);
        $r1=$this->cc->fetchAll('',MYSQLI_ASSOC);

        if($exnum1) {

            // второй запрос
            $r=array(
                'gr'=>1,
                'fields'=>'cc_brand.brand_id, cc_model.model_id, cc_cat.suffix AS csuffix',
                'notH'=>1,
                'where'=>array(),
                'groupBy'=>'',
                'nolimits'=>1
            );

            if(!empty($this->brands_))   $r['brand_id']=array('list'=>$this->brands_); // бренд
            if(!empty($this->M1_))       $r['M1']=array('list'=>$this->M1_); // сезон
            if(!empty($this->M2_)) {
                if(in_array(1,$this->M2_) || in_array(2,$this->M2_)) $this->M2_[]=4;
                $r['M2']=array('list'=>$this->M2_);
            } // автотип
            if($this->M3_!=='')         $r['M3']=$this->M3_; // шип
            if(!empty($this->sP1))      $r['P1']=array('list'=>$this->sP1);
            if(!empty($this->sP2))      $r['P2']=array('list'=>$this->sP2);
            if(!empty($this->sP3))      $r['P3']=array('list'=>$this->sP3);
            if($this->runflat_!=='')    $r['rf']=$this->runflat_;
            if($this->c_index_!=='')    $r['rf']=$this->c_index_;

            if($this->hideTSCZero) $r['where'][]=$this->minQtyRadiusSQL;

            $exnum2=$this->cc->cat_view($r);
            $r2=$this->cc->fetchAll('',MYSQLI_ASSOC);

            /* сливаем результаты
            группируем по размеру, ранфлету и XL, бренду, модели
            */
            $this->gsuf=array(); // эти суффиксы должны присуствовать в обеих типоразмерах
            $s=Data::get('cc_runflat_suffix');
            $this->gsuf=explode(';',$s);
            $this->gsuf[]='XL';

            foreach($r1 as $v1)
                foreach($r2 as $v2)
                    if($v1['brand_id']==$v2['brand_id'])
                        if($v1['model_id']==$v2['model_id'])
                            if($this->checkSuffixes(Tools::unesc($v1['csuffix']), Tools::unesc($v2['csuffix']))){

                                $this->r['tn']++;

                            }
        }

        /*
        * второй этап: получение групп параметров
        */

        if(@is_array($groups))
            foreach($groups as $group){

                $r1=array(
                    'gr'=>1,
                    'fields'=>'cc_brand.brand_id, cc_model.model_id, cc_cat.suffix AS csuffix, cc_model.P1 AS MP1, cc_model.P2 AS MP2, cc_model.P3 AS MP3',
                    'notH'=>1,
                    'where'=>array(),
                    'nolimits'=>1
                );

                // добавляем все жесткие параметры (1)
                if(!empty($this->brands))   $r1['brand_id']=array('list'=>$this->brands); // бренд
                if(!empty($this->M1))       $r1['M1']=array('list'=>$this->M1); // сезон
                if(!empty($this->M2))       {
                    if(in_array(1,$this->M2) || in_array(2,$this->M2)) $this->M2[]=4;
                    $r1['M2']=array('list'=>$this->M2);
                } // автотип
                if($this->M3!=='')          $r1['M3']=$this->M3; // шип
                if(!empty($this->P1))       $r1['P1']=array('list'=>$this->P1); // радиус
                if(!empty($this->P2))       $r1['P2']=array('list'=>$this->P2);
                if(!empty($this->P3))       $r1['P3']=array('list'=>$this->P3);
                if($this->runflat!=='')    $r1['rf']=$this->runflat;
                if($this->c_index!=='')    $r1['c_index']=$this->c_index;

                if($this->hideTSCZero) $r1['where'][]=$this->minQtyRadiusSQL;

                $r2=array(
                    'gr'=>1,
                    'fields'=>'cc_brand.brand_id, cc_model.model_id, cc_cat.suffix AS csuffix, cc_model.P1 AS MP1, cc_model.P2 AS MP2, cc_model.P3 AS MP3',
                    'notH'=>1,
                    'where'=>array(),
                    'groupBy'=>'',
                    'nolimits'=>1
                );

                // добавляем все жесткие параметры (2)
                if(!empty($this->brands))   $r2['brand_id']=array('list'=>$this->brands); // бренд
                if(!empty($this->M1))       $r2['M1']=array('list'=>$this->M1); // сезон
                if(!empty($this->M2))       {
                    if(in_array(1,$this->M2) || in_array(2,$this->M2)) $this->M2[]=4;
                    $r2['M2']=array('list'=>$this->M2);
                } // автотип
                if($this->M3!=='')          $r2['M3']=$this->M3; // шип
                if(!empty($this->sP1))      $r2['P1']=array('list'=>$this->sP1); // радиус
                if(!empty($this->sP2))      $r2['P2']=array('list'=>$this->sP2);
                if(!empty($this->sP3))      $r2['P3']=array('list'=>$this->sP3);
                if($this->runflat!=='')     $r2['rf']=$this->runflat;
                if($this->c_index!=='')     $r2['c_index']=$this->c_index;

                if($this->hideTSCZero) $r2['where'][]=$this->minQtyRadiusSQL;

                // добавляем уточняющие параметры, по группам
                switch($group){

                    case '_bids':
                        if(!empty($this->_M1))       $r1['M1']=$r2['M1']=array('list'=>$this->M1_); else unset($r1['M1'], $r2['M1']);// сезон
                        if(!empty($this->_M2))       {
                            if(in_array(1,$this->M2_) || in_array(2,$this->M2_)) $this->M2_[]=4;
                            $r1['M2']=$r2['M2']=array('list'=>$this->M2_);
                        } else unset($r1['M2'], $r2['M2']);  // автотип
                        if(!empty($this->_M3))       $r1['M3']=$r2['M3']=array('list'=>$this->M3_); else unset($r1['M3'], $r2['M3']);// шип

                        $exnum1=$this->cc->cat_view($r1);
                        $r1=$this->cc->fetchAll('',MYSQLI_ASSOC);

                        if($exnum1) {

                            $exnum2=$this->cc->cat_view($r2);
                            $r2=$this->cc->fetchAll('',MYSQLI_ASSOC);

                            /* сливаем результаты
                            группируем по размеру, ранфлету и XL, бренду, модели
                            */

                            foreach($r1 as $v1)
                                foreach($r2 as $v2)
                                    if($v1['brand_id']==$v2['brand_id'])
                                        if($v1['model_id']==$v2['model_id'])
                                            if($this->checkSuffixes(Tools::unesc($v1['csuffix']), Tools::unesc($v2['csuffix']))){

                                                $this->r['formdata']["_bids{$v1['brand_id']}"]=1;

                                            }
                        }

                        break;

                    case '_mp1':

                        if(!empty($this->_brands))   $r1['brand_id']=$r2['brand_id']=array('list'=>$this->brands_); else unset ($r1['brand_id'],$r2['brand_id']); // бренд
                        if(!empty($this->_M2))       {
                            if(in_array(1,$this->M2_) || in_array(2,$this->M2_)) $this->M2_[]=4;
                            $r1['M2']=$r2['M2']=array('list'=>$this->M2_);
                        } else unset($r1['M2'], $r2['M2']);  // автотип
                        if(!empty($this->_M3))       $r1['M3']=$r2['M3']=array('list'=>$this->M3_); else unset($r1['M3'], $r2['M3']);// шип

                        $exnum1=$this->cc->cat_view($r1);
                        $r1=$this->cc->fetchAll('',MYSQLI_ASSOC);

                        if($exnum1) {

                            $exnum2=$this->cc->cat_view($r2);
                            $r2=$this->cc->fetchAll('',MYSQLI_ASSOC);

                            /* сливаем результаты
                            группируем по размеру, ранфлету и XL, бренду, модели
                            */

                            foreach($r1 as $v1)
                                foreach($r2 as $v2)
                                    if($v1['brand_id']==$v2['brand_id'])
                                        if($v1['model_id']==$v2['model_id'])
                                            if($this->checkSuffixes(Tools::unesc($v1['csuffix']), Tools::unesc($v2['csuffix']))){

                                                $this->r['formdata']["_mp1{$v1['MP1']}"]=1;

                                            }
                        }

                        break;

                    case '_at':

                        if(!empty($this->_brands))   $r1['brand_id']=$r2['brand_id']=array('list'=>$this->brands_); else unset ($r1['brand_id'],$r2['brand_id']); // бренд
                        if(!empty($this->_M1))       $r1['M1']=$r2['M1']=array('list'=>$this->M1_); else unset($r1['M1'], $r2['M1']);// сезон
                        if(!empty($this->_M3))       $r1['M3']=$r2['M3']=array('list'=>$this->M3_); else unset($r1['M3'], $r2['M3']);// шип

                        $exnum1=$this->cc->cat_view($r1);
                        $r1=$this->cc->fetchAll('',MYSQLI_ASSOC);

                        if($exnum1) {

                            $exnum2=$this->cc->cat_view($r2);
                            $r2=$this->cc->fetchAll('',MYSQLI_ASSOC);

                            /* сливаем результаты
                            группируем по размеру, ранфлету и XL, бренду, модели
                            */

                            foreach($r1 as $v1)
                                foreach($r2 as $v2)
                                    if($v1['brand_id']==$v2['brand_id'])
                                        if($v1['model_id']==$v2['model_id'])
                                            if($this->checkSuffixes(Tools::unesc($v1['csuffix']), Tools::unesc($v2['csuffix']))){

                                                $this->r['formdata']["_at{$v1['MP2']}"]=1;

                                            }
                        }

                        if(!empty($this->r['formdata']["_at4"])) {
                            $this->r['formdata']["_at1"]=1;
                            $this->r['formdata']["_at2"]=1;
                        }

                        break;

                    case '_mp3':

                        if(!empty($this->_brands))   $r1['brand_id']=$r2['brand_id']=array('list'=>$this->brands_); else unset ($r1['brand_id'],$r2['brand_id']); // бренд
                        if(!empty($this->_M1))       $r1['M1']=$r2['M1']=array('list'=>$this->M1_); else unset($r1['M1'], $r2['M1']);// сезон
                        if(!empty($this->_M2))       {
                            if(in_array(1,$this->M2_) || in_array(2,$this->M2_)) $this->M2_[]=4;
                            $r1['M2']=$r2['M2']=array('list'=>$this->M2_);
                        } else unset($r1['M2'], $r2['M2']);  // автотип

                        $exnum1=$this->cc->cat_view($r1);
                        $r1=$this->cc->fetchAll('',MYSQLI_ASSOC);

                        if($exnum1) {

                            $exnum2=$this->cc->cat_view($r2);
                            $r2=$this->cc->fetchAll('',MYSQLI_ASSOC);

                            /* сливаем результаты
                            группируем по размеру, ранфлету и XL, бренду, модели
                            */

                            foreach($r1 as $v1)
                                foreach($r2 as $v2)
                                    if($v1['brand_id']==$v2['brand_id'])
                                        if($v1['model_id']==$v2['model_id'])
                                            if($this->checkSuffixes(Tools::unesc($v1['csuffix']), Tools::unesc($v2['csuffix']))){

                                                $this->r['formdata']["_mp3{$v1['MP3']}"]=1;

                                            }
                        }

                        break;

                }
        }

        $this->r['queryTime']=Tools::getMicroTime()-$time1;

    }


    public function qsearch()
    {
        $this->search();
        return 'search/stage2';
    }

    public function axView(){
        $this->_routeParam();

        if($this->sMode) return $this->doubleSearch();

        // тип отображения результатов поиска
        if(@$_COOKIE['stype']=='lenta') $this->searchTpl='catalog/tyres/searchLenta'; else $this->searchTpl='catalog/tyres/searchBlock';

        if(true!==($res=$this->_cat())) return $res;

        if(empty($this->cat)) $this->qtext="Шин в данный момент нет в наличии";

        // Вывод
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
