<?
class App_Search extends Common {

    use TextParser;

    var
        $info='',
        $inis=array(),
        $grDict=array(
        1=>array('шин','резин','автошин','покрышк'),
        2=>array('колес','диски','дисков','колесн','автодиск')
    ),
        $excludeParts=array(
        'автомоб','радиус','размер'
    ),
        $excludeWords=array(
        'r','et','dia','pcd','dia','dco','для','по','автом','на','.',',','/','j','jx','x','диск'
    ),
        $excludePartsRegEx=array(
        '^r([0-9\.,]{2,})$','^et([0-9\.,\-]{2,})$','^dia([0-9\.,]{2,})$','^pcd([0-9]{1})$','^dia([0-9\.,]{2,})$','^dco([0-9\.,]{2,})$','^(.*)[\.,]$'
    ),
        $dType=array(
        1=>array('кован'),
        2=>array('литой','литые'),
        3=>array('штампов','стальн')
    ),
        $aType=array(
        1=>array('легков'),
        2=>array('внедорож','джип'),
        3=>array('микроавтоб')
    ),
        $sezon=array(
        1=>array('летн','лето'),
        2=>array('зима','зимн'),
        3=>array('всесез')
    ),
        $ship=array('шип'),
        $replicaParts=array('реплик','replica');

    function init()
    {
        $this->cc=new CC_Base();
        $this->cc->load_filter('P7_1');
        for($i=0;$i<count($this->cc->s_arr['P7_1']);$i++)
            if(mb_strpos($this->cc->s_arr['P7_1'][$i],'/')!==false || preg_match("/[0-9]+[A-Z]{1}/u",$this->cc->s_arr['P7_1'][$i])) $this->inis[]=$this->cc->s_arr['P7_1'][$i];

        unset($this->cc->s_arr['P7_1']);

        $d=$this->cc->getOne("SELECT min(model_id) FROM cc_model WHERE NOT LD");
        $this->minMID=$d[0];
        $d=$this->cc->getOne("SELECT min(cat_id) FROM cc_cat WHERE NOT LD");
        $this->minCID=$d[0];

        $r=$this->cc->fetchAll("SELECT * FROM cc_suffix ORDER BY cSuffix",MYSQL_ASSOC);
        $this->suf=array();
        foreach($r as $v) if($v['suffix1']!='') $this->suf[Tools::unesc($v['cSuffix'])]=Tools::unesc($v['suffix1']);
        unset($r);

    }

    function go()
    {
        if(!$this->checkQ()) return false;
        $this->init();
        $this->articleMode=Request::$ajax?false:true;
        $nParam=array();
        $strParam=array();
        $this->_where=array();
        $this->_whereCat=array();
        $this->_having=array();
        $this->_select='';
        $this->gr=0;
        $this->replica=0;
        $this->M1=$this->M2=$this->M3=$this->P7=$this->P1=$this->P2=$this->P3=$this->P4=$this->P5=$this->P6='';
        $suffix=array();

        // пытаемся делать поиск по артиклю
        $qq=preg_split("/[\s\,;]{1}/",$this->q);
        $mids=array();
        $cids=array();
        foreach($qq as $v){
            $v=trim($v);
            $mi=$ci=true;
            if($v!=''){
                if(preg_match("~^([0-9]+)$~iu",$v,$m) && $m[1]>=$this->minMID) $mids[]=$m[1]; else $mi=false;
                if(preg_match("~^([0-9]+)$~",$v,$m) && $m[1]>=$this->minCID) $cids[]=$m[1]; else $ci=false;
                if(!$mi && !$ci) {
                    $this->articleMode=false;
                    break 1;
                }
            }
        }
        if($this->articleMode){
            $this->ss=new Content();
            if(!empty($mids)){
                $this->info="Выполнен поиск по артикулу модели:  ".implode(', ',$mids);
                if(count($mids)==1){
                    $d=$this->cc->getOne("SELECT cc_model.gr,cc_model.model_id FROM cc_model JOIN cc_brand USING (brand_id) WHERE cc_model.model_id='".$mids[0]."' AND NOT cc_brand.LD AND NOT cc_model.LD");
                    //if($d===0) return $this->putMsg(false, $this->parse($this->ss->getDoc('noresult_search')));
                    //else {
                    if(!empty($d)){
                        $this->model_id=$this->cc->qrow['model_id'];
                        if($this->cc->qrow['gr']==1)
                            return $this->putMsg(true,'catalog/tyres/model');
                        else return $this->putMsg(true,'catalog/disks/model');
                    }
                }
                if(empty($this->VMode)) $this->VMode=2;
                $this->_where[]="cc_model.model_id IN(".implode(',',$mids).")";
                //return $this->putMsg(true,'catalog/tyres/qsearch');
            }
            if(!empty($cids)){
                $this->info="Выполнен поиск по артикулу типоразмера:  ".implode(', ',$cids);
                if(count($cids)==1){
                    $d=$this->cc->getOne("SELECT cc_cat.gr, cc_cat.cat_id FROM cc_cat JOIN cc_model USING (model_id) JOIN cc_brand USING (brand_id) WHERE cc_cat.cat_id='".$cids[0]."' AND NOT cc_brand.LD AND NOT cc_model.LD AND NOT cc_model.H AND NOT cc_cat.H AND NOT cc_cat.LD");
                    //if($d===0) return $this->putMsg(false, $this->parse($this->ss->getDoc('noresult_search')));
                    //else {
                    if(!empty($d)){
                        $this->cat_id=$this->cc->qrow['cat_id'];
                        if($this->cc->qrow['gr']==1)
                            return $this->putMsg(true,'catalog/tyres/tipo');
                        else return $this->putMsg(true,'catalog/disks/tipo');
                    }
                }
                if(empty($this->VMode)) $this->VMode=1;
                $this->_whereCat[]="cc_cat.cat_id IN(".implode(',',$cids).")";
                //return $this->putMsg(true,'catalog/tyres/qsearch');
            }
        }

        $qq=$q=explode(" ",$this->q);

        //разбиваем известные комбинации параметров
        foreach($q as $k=>$part){
            //225[/*x]65[/*rx]16
            if(preg_match("~^([0-9\.,]{2,})[\/\*x]{1}([0-9\.,]{2,})[\/\*xr]{1}([0-9\.,]{2,})$~iu",$part,$m)){
                unset($qq[$k]);
                $qq[]=$m[1];
                $qq[]=$m[2];
                $qq[]=$m[3];
                $this->gr=1;
            }
            //225[/*]65
            //5[x*/]110
            //5jx110.1
            elseif(preg_match("~^([0-9\.,]{1,})(jx|xj|\/|\*|x|х)([0-9\.,]{2,})$~iu",$part,$m)){
                unset($qq[$k]);
                $qq[]=$m[1];
                $qq[]=$m[3];
            }
        }
        if(preg_match("~(^|\s)et[\s]*([0-9]+)(\s|$)~iu",$this->q,$m)) {
            $this->gr=2;
            $this->_where[]="cc_cat.P1 = '{$m[2]}'";
        }
        if(preg_match("~(^|\s)(dia|d)[\s]*([0-9\.,]{2,})(\s|$)~iu",$this->q,$m)) {
            $this->gr=2;
            $m[3]=str_replace(',','.',$m[3]);
            $this->_where[]="cc_cat.P3 = '{$m[3]}'";
        }

        //убираем стоп слова
        $q=$qq;

        foreach($q as $k=>$part)
            foreach($this->excludeParts as $ew)
                if(mb_stripos($part,$ew)!==false) unset($qq[$k]);

        $q=$qq;
        foreach($q as $k=>$part)
            foreach($this->excludePartsRegEx as $ew)
                $q[$k]=preg_replace("/$ew/iu","\$1",$q[$k]);

        $qq=$q;
        foreach($q as $k=>$part)
            foreach($this->excludeWords as $ew)
                if(Tools::mb_strcasecmp($part,$ew)===0) unset($qq[$k]);

        $q=$qq;
        // определяем группу
        foreach($q as $k=>$part)
            foreach($this->grDict as $gr=>$gri)
                foreach($gri as $w)
                    if(mb_stripos($part,$w)!==false) {
                        $this->gr=$gr;
                        unset($qq[$k]);
                    }
        // реплика
        $q=$qq;
        foreach($q as $k=>$part)
            foreach($this->replicaParts as $w)
                if(mb_stripos($part,$w)!==false) {
                    $this->replica=1;
                    $this->gr=2;
                    $this->_where[]="cc_brand.replica={$this->replica}";
                    unset($qq[$k]);
                }
        // сезон
        $q=$qq;
        foreach($q as $k=>$part)
            foreach($this->sezon as $i=>$v)
                foreach($v as $w)
                    if(mb_stripos($part,$w)!==false) {
                        $this->M1=$i;
                        $this->gr=1;
                        $this->_where[]="cc_model.P1=$i";
                        unset($qq[$k]);
                    }
        // шипы
        $q=$qq;
        foreach($q as $k=>$part)
            foreach($this->ship as $w)
                if(mb_stripos($part,$w)!==false) {
                    $this->M3=1;
                    $this->gr=1;
                    $this->_where[]="cc_model.P3=1";
                    unset($qq[$k]);
                }
        // тип шины
        $q=$qq;
        foreach($q as $k=>$part)
            foreach($this->aType as $i=>$v)
                foreach($v as $w)
                    if(mb_stripos($part,$w)!==false) {
                        $this->M2=$i;
                        $this->gr=1;
                        $this->_where[]="cc_model.P2=$i";
                        unset($qq[$k]);
                    }
        // тип диска
        $q=$qq;
        foreach($q as $k=>$part)
            foreach($this->dType as $i=>$v)
                foreach($v as $w)
                    if(mb_stripos($part,$w)!==false) {
                        $this->M1=$i;
                        $this->gr=2;
                        $this->_where[]="cc_model.P1=$i";
                        unset($qq[$k]);
                    }
        // инис
        $q=$qq;
        foreach($q as $k=>$part)
            foreach($this->inis as $i=>$v)
                if(Tools::mb_strcasecmp($part,$v)===0) {
                    $this->P7=addslashes($v);
                    $this->gr=1;
                    $this->_whereCat[]="cc_cat.P7 LIKE '{$this->P7}'";
                    unset($qq[$k]);
                    if(empty($this->VMode)) $this->VMode=1;
                }

        //  цвет
        $q=$qq;
        foreach($q as $k=>$part)
//		if(($i=Tools::mb_array_search($part,$this->suf))!==false) $qq[]=str_replace(' ','_',$i);

        if($this->gr==1){
            // ZR
            $q=$qq;
            foreach($q as $k=>$part)
                if(Tools::mb_strcasecmp('zr',$part)===0) {
                    $this->P6=1;
                    $this->_whereCat[]="cc_cat.P6=1";
                    unset($qq[$k]);
                    if(empty($this->VMode)) $this->VMode=1;
                }

        }

        $q=array_diff($qq,array(''));
//	print_r($q);
        // собираем части запроса
        foreach($q as $k=>$v){
            $p=Tools::typeOf($pp=str_replace(',','.',$v));
            if(in_array($p,array('float','integer'))) $v=$pp;
            $v=Tools::esc($v);
            $s="(cc_brand.name LIKE '%$v%'"
                ." OR cc_brand.alt LIKE '%$v%'"
                ." OR cc_model.name LIKE '%$v%'"
                ." OR cc_model.alt LIKE '%$v%'";

            if(Tools::typeOf($v)=='string') $s.=" OR cc_cat.P7 LIKE '$v' OR cc_cat.suffix LIKE '%$v%'";
            else $s.=" OR cc_cat.P1 = '$v' OR cc_cat.P2 = '$v' OR cc_cat.P3 = '$v' OR cc_cat.P4 = '$v' OR cc_cat.P5 = '$v' OR cc_cat.P6 = '$v' ";
            $s.=")";

            $this->_whereCat[]=$s;
        }

        $this->VMode=1;

        if($this->gr==2) return $this->putMsg(true,'catalog/disks/qsearch'); else $this->putMsg(true,'catalog/tyres/qsearch');
    }

    function getQ($q)
    {
        $this->q='';
        if(preg_match("/[<>`]/",$q)) return '';
        $q=str_replace(chr(9),' ',$q);
        return $this->q=Tools::cutDoubleSpaces(Tools::stripTags(trim($q)));
    }

    function checkQ()
    {
        $s=explode(' ',$this->q);
        if(mb_strlen($this->q)>100 || count($s)>15) {
            $this->q=mb_substr($this->q,0,100);
            return $this->putMsg(false,'Слишком длинная строка поиска. Оптимизируйте запрос, пожалуйста');
        }
        if($this->q=='') return $this->putMsg(false,'Пустая строка поиска. Нечего искать!');
        if(mb_strlen($this->q)<2) return $this->putMsg(false,'Слишком короткая строка запрос поиска. Пожалуйста, уточните ваш вопрос');
        return true;
    }

    function __construct($param=array())
    {
        $this->getQ(@$param['q']);
        $this->VMode=@$param['VMode'];
    }

}