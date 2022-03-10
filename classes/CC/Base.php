<? //

/*  ЗНАЧЕНИЯ ПАРАМЕТРОВ ТАБЛИЦ С МОДЕЛЯМИ И РАЗМЕРАМИ:

	Шины - размеры
	
		P1	радиус DECIMAL (5,2)
		P2 	высота  UNSIGNED DECIMAL (5,2)
		P3	ширина  UNSIGNED DECIMAL (5,2)
		P4	индекс C   DECIMAL (2,0) UNSIGNED -- в суффиксе может быть С
		P5	-- резерв--   UNSIGNED DECIMAL (5,2)
		P6	Скоростной ZR   UNSIGNED DECIMAL (5,2)  -- в суффиксе не должно быть ZR
		P7	ис/ин   VARCHAR(50)
		
	Диски размеры
	
		P1	ET  DECIMAL (5,2)
		P2	J   UNSIGNED DECIMAL (5,2)
		P3	Dia  UNSIGNED DECIMAL (5,2)
		P4	дырки  DECIMAL (2,0) UNSIGNED
		P5	xR  UNSIGNED DECIMAL (5,2)
		P6	ДЦО   UNSIGNED DECIMAL (5,2)
		P7	--Резерв-- (раньше был Тип диска (1-кованый 2-литой 3-штампованый)   VARCHAR
		
		формат размера: 
			{P2}xJ{P5} {P4}/{P6} ET{P1} DIA {P3}
			{$v['P2']}xJ{$v['P5']} {$v['P4']}/{$v['P6']} ET{$v['P1']} DIA {$v['P3']}
		
	Шины модели
	
		P1	Сезон: 1-лето 2-зима 3-всесезон
		P2	Тип авто: 1-легковой 2-внедорожник 3-микроавтобус/легкий грузовик
		P3	1-Шипы 0-нешипы
		
	Диски модели
	
		P1	Тип диска: 1-кованый 2-литой 3-штамп
		P2	--резерв--
		P3	--резерв--
		
*/

if (!defined('true_enter')) die ("Direct access not allowed!");

class CC_Base extends DB
{
    var $cc_conn=true;
    var $img_path; // последнее возвращенное значение из make(get)_img_path()
    var $mspez_arr=array(); // массив cc_mspez
    var $class_arr=array(); // массив cc_class
    var $s_arr; // массив из файловых кешей load_filters()
    var $dict_keys=array();
    var $ex_arr=array();  // экстрагированные значения при ex==1 при выполеннии cat_view() и cat(), models()
    var $filters_coo=array(); // данные кук для сохранения состояния фильтров на сайте
    var $global_t_extra; // глобальная наценка
    var $global_d_extra;
    var $discount_arr=array(); // discount_price() $t_discount, $d_discount краснеая цена
    var $sMatrix=array(); // матрица суффиксов из cc_suffix

    function __construct($r=array())
    {
        parent::__construct();

        if(Cfg::get('RDisk_S1')) $this->RDisk=new CC_RDisk();
        if(Cfg::get('RTyre_R1')) $this->RTyre=new CC_RTyre();
        if(Cfg::get('int_price_F1F2')) $this->intPrice=new CC_IntPrice();
    }

    function cc_connect($cdb_host='',$cdb_user='',$cdb_pass='',$cdb_db='')
    {
        if ($cdb_db==''){
            $res= $this->sql_connect();
        }else{
            $this->sql_host=$cdb_host;
            $this->sql_user=$cdb_user;
            $this->sql_pass=$cdb_pass;
            $this->sql_db=$cdb_db;
            $res= $this->sql_connect();
        }
        $this->cc_conn=$res;
        return $res;
    }

    public function loadSMatrix($gr=2,$infoInclude=false){

        if(!empty($this->sMatrix)) return true;
        $this->sMatrix=array($gr=>array());
        $db=new DB();
        $db->query("SELECT * FROM cc_suffix ORDER BY cSuffix");
        if($db->qnum()) while($db->next()!==false){
            $is=trim(Tools::unesc($db->qrow['iSuffixes']));
            $is=explode(',',$is);
            $is=array_unique($is);
            foreach($is as $k=>&$v) {
                $v=trim($v);
                if(empty($v)) unset($is[$k]);
            }
            $cs=Tools::unesc($db->qrow['cSuffix']);
            $this->sMatrix[$gr][$cs][$db->qrow['brand_id']]=array(
                'iSuffixes'=>$is,
                'tag'=>Tools::unesc($db->qrow['tag']),
                'suffix1'=>Tools::unesc($db->qrow['suffix1']),
                'suffix2'=>Tools::unesc($db->qrow['suffix2'])
            );
            if($infoInclude) {
                $this->sMatrix[$gr][$cs][$db->qrow['brand_id']]['dt_added']=Tools::sdate($db->qrow['dt_added']);
                $this->sMatrix[$gr][$cs][$db->qrow['brand_id']]['dt_added_ts']=$db->qrow['dt_added'];
                $this->sMatrix[$gr][$cs][$db->qrow['brand_id']]['id']=$db->qrow['id'];
            }

        }
        unset($db);
    }

// возвращает suffix1 если есть, иначе suffix
    public function getSuffix1($suffix,$brand_id=0,$gr=2){
        $this->loadSMatrix($gr);
        foreach($this->sMatrix[$gr] as $s=>$v)
            if(Tools::mb_strcasecmp($s,$suffix)==0)
                if(@$v[$brand_id]['suffix1']!='')
                    return $v[$brand_id]['suffix1'];
                else
                    if(@$v[0]['suffix1']!='') return $v[0]['suffix1'];
                    else return $suffix;
        return $suffix;
    }

// возвращает suffix2 если есть, иначе suffix1 ,ели есть, иначе suffix
    public function getSuffix2($suffix,$brand_id=0,$gr=2){
        $this->loadSMatrix($gr);
        foreach($this->sMatrix[$gr] as $s=>$v)
            if(Tools::mb_strcasecmp($s,$suffix)==0)
                if(@$v[$brand_id]['suffix2']!='') return $v[$brand_id]['suffix2']; elseif(@$v[0]['suffix2']!='') return $v[0]['suffix2'];
                else
                    if(@$v[$brand_id]['suffix1']!='') return $v[$brand_id]['suffix1']; elseif(@$v[0]['suffix1']!='') return $v[0]['suffix1'];
                    else return $suffix;
        return $suffix;
    }

// возвращает массив suffix1,suffix2 если есть suffix1||suffix2, иначе строку suffix
    public function getSuffix12($suffix,$brand_id=0,$gr=2){
        $this->loadSMatrix($gr);
        foreach($this->sMatrix[$gr] as $s=>$v)
            if(Tools::mb_strcasecmp($s,$suffix)==0)
                if(@$v[$brand_id]['suffix1']!='' || @$v[$brand_id]['suffix2']!='')
                    return array($v[$brand_id]['suffix1'],$v[$brand_id]['suffix2']);
                elseif(@$v[0]['suffix1']!='' || @$v[0]['suffix2']!='')
                    return array($v[0]['suffix1'],$v[0]['suffix2']);
                else return $suffix;

        return $suffix;
    }


    function que($qname,$cond1='',$cond2='',$cond3='',$cond4='',$cond5='')
    {
        switch ($qname)
        {
            case 'filter':
                $cond1=Tools::esc($cond1);  // P
                $cond2=intval($cond2);   // gr
                $cond3=intval($cond3);   // NOT H
                $cond4=intval($cond4);   // sc>0
                $cond5=($cond5);   // replica
                if(in_array($cond1,array('P1','P2','P3','P5','P6'))) $p0="+'0' AS $cond1"; else $p0='';
                $s='';
                if($cond5===0) $s="AND cc_brand.replica!=1";
                elseif($cond5===1) $s="AND cc_brand.replica=1";
                $res=$this->query("SELECT cc_cat.{$cond1}{$p0} FROM (cc_cat INNER JOIN cc_model ON cc_cat.model_id = cc_model.model_id) INNER JOIN cc_brand ON cc_model.brand_id = cc_brand.brand_id WHERE (cc_cat.gr='$cond2')AND(NOT cc_model.LD)AND(NOT cc_brand.LD)AND(NOT cc_cat.LD)".($cond4?' AND cc_cat.sc>0':'').($cond3?" AND (NOT cc_cat.H) AND (NOT cc_model.H) AND (NOT cc_brand.H)":'')." $s GROUP BY cc_cat.$cond1 ORDER BY cc_cat.$cond1");
                break;
            case 'P46concat':
                $cond1=intval($cond1);   // NOT H
                $cond2=intval($cond2);   // sc>0
                $cond3=($cond3);   // replica
                $s='';
                if($cond3===0) $s="AND cc_brand.replica=0";
                elseif($cond3===1) $s="AND cc_brand.replica!=0";
                $cond4=explode(',',$cond4);
                $ex=array();
                foreach($cond4 as $v) $ex[]=(float)$v;
                $ex=implode(',',$ex);
                $res=$this->query("SELECT CONCAT(cc_cat.P4,'x',cc_cat.P6+'0') as P4x6 FROM (cc_cat INNER JOIN cc_model ON cc_cat.model_id = cc_model.model_id) INNER JOIN cc_brand ON cc_model.brand_id = cc_brand.brand_id WHERE (cc_cat.gr=2)AND(NOT cc_model.LD)AND(NOT cc_brand.LD)AND(NOT cc_cat.LD)".($cond2?' AND cc_cat.sc>0':'').($cond1?" AND (NOT cc_cat.H) AND (NOT cc_model.H) AND (NOT cc_brand.H)":'')." AND cc_cat.P4 NOT IN ($ex) $s GROUP BY P4x6 ORDER BY cc_cat.P4,cc_cat.P6");
                break;
            case 'brands':
                $cond1=intval($cond1);  //gr
                $cond2=intval($cond2);  // NOT H
                $cond3=$cond3;  // where
                $cond4=$cond4; // order
                if($cond4=='' && $cond1==2) $cond4="bsupDiv ASC, replica ASC, name ASC"; elseif($cond4=='') $cond4="name ASC";
                $res=$this->query("SELECT *, (sup_id DIV sup_id) AS bsupDiv FROM cc_brand WHERE (gr='$cond1')AND(NOT LD)".($cond2?" AND (NOT H)":'')." $cond3 ORDER BY $cond4");
                break;
            case 'brands_join':
                $cond1=intval($cond1);  //gr
                $cond2=intval($cond2);  // NOT H
                $cond3=$cond3;  // where
                if($cond4=='' && $cond1==2) $cond4="cc_brand.replica DESC, cc_brand.name ASC"; elseif($cond4=='') $cond4="cc_brand.name ASC";
                $cond5=intval($cond5);   // sc>0
                $res=$this->query("SELECT cc_brand.* FROM (cc_cat INNER JOIN cc_model ON cc_cat.model_id = cc_model.model_id) INNER JOIN cc_brand ON cc_model.brand_id = cc_brand.brand_id WHERE (cc_cat.gr='$cond1')AND(NOT cc_model.LD)AND(NOT cc_brand.LD)AND(NOT cc_cat.LD) $cond3 ".($cond5?' AND cc_cat.sc>0':'').($cond2?" AND (NOT cc_cat.H) AND (NOT cc_model.H) AND (NOT cc_brand.H)":'')." GROUP BY cc_brand.name ORDER BY $cond4");

                break;
            case 'brand_by_id':
                $cond1=intval($cond1);  // brand_id
                $cond2=intval($cond2);  // gr
                $cond3=intval($cond3);  // NOT H
                $res=$this->query("SELECT * FROM cc_brand WHERE (brand_id='$cond1')".($cond2?" AND(gr=$cond2)":'').($cond3?"AND(NOT cc_brand.H)":''));
                break;
            case 'brand_by_name':
                $cond1=Tools::like($cond1);
                $cond2=intval($cond2);
                $cond3=intval($cond3);
                $res=$this->query("SELECT * FROM cc_brand WHERE (name LIKE '$cond1')AND(NOT cc_brand.LD)".($cond2?" AND(gr=$cond2)":'').($cond3?"AND(NOT cc_brand.H)":''));
                break;
            case 'brand_by_sname':
                $cond1=Tools::like($cond1); // brand_sname
                $cond2=intval($cond2); // not H
                $cond3=intval($cond3); // gr
                $res=$this->query("SELECT * FROM cc_brand WHERE (BINARY sname = '$cond1')AND(NOT cc_brand.LD)".($cond3?" AND(gr=$cond3)":'').($cond2?"AND(NOT cc_brand.H)":''));
                break;
            case 'model_by_id':
                $cond1=intval($cond1); // model_id
                $cond2=intval($cond2); // NOT H
                $af_brand=App_TFields::DBselect('cc_brand','all');
                $res=$this->query("SELECT cc_model.*, cc_brand.brand_id, cc_brand.name AS bname, cc_brand.text AS btext, cc_brand.alt AS balt, cc_brand.img1 AS brand_img1, cc_brand.img2 AS brand_img2, cc_brand.replica, cc_brand.sname AS brand_sname{$af_brand} FROM cc_model INNER JOIN cc_brand ON cc_model.brand_id = cc_brand.brand_id WHERE (model_id='$cond1')AND(NOT cc_model.LD)AND(NOT cc_brand.LD)".($cond2?"AND(NOT cc_model.H)AND(NOT cc_brand.H)":''));
                break;
            case 'model_by_sname':
                $cond1=Tools::like($cond1, false);  // model.sname
                $cond2=intval($cond2);  // H
                $cond3=Tools::like($cond3);   // brand.sname
                $cond4=(int)$cond4; // gr
                $af_brand=App_TFields::DBselect('cc_brand','all');
                $res=$this->query("SELECT cc_model.*, cc_brand.brand_id, cc_brand.name AS bname, cc_brand.text AS btext, cc_brand.alt AS balt, cc_brand.img1 AS brand_img1, cc_brand.img2 AS brand_img2, cc_brand.replica, cc_brand.sname AS brand_sname{$af_brand}, cc_model.P1 as MP1 FROM cc_model INNER JOIN cc_brand ON cc_model.brand_id = cc_brand.brand_id WHERE (NOT cc_model.LD)AND(NOT cc_brand.LD)AND(BINARY cc_model.sname = '$cond1')".($cond2?"AND(NOT cc_model.H)AND(NOT cc_brand.H)":'').($cond3!=''?" AND(cc_brand.sname LIKE '$cond3')":'').($cond4?" AND(cc_brand.gr = '$cond4')":''));
                break;
            case 'model_list':
                $cond1=intval($cond1);  // gr
                $cond2=intval($cond2); // brand_id
                $cond3=intval($cond3);  // not H
                $cond4=$cond4;  // where
                $af_brand=App_TFields::DBselect('cc_brand','all');
                $res=$this->query("SELECT cc_model.*, cc_brand.name AS bname, cc_brand.text AS btext, cc_brand.replica{$af_brand}
				FROM cc_model LEFT JOIN cc_brand ON cc_model.brand_id = cc_brand.brand_id
				WHERE (cc_model.gr='$cond1') AND (NOT cc_model.LD) AND (NOT cc_brand.LD)".($cond2>0?"AND (cc_model.brand_id='$cond2')":'')
                .($cond3?'AND(NOT cc_model.H)':'').($cond4!=''?"AND({$cond4})":'')." ORDER BY cc_brand.name, cc_model.name");
                break;
            case 'model_by_brand':
                $cond1=intval($cond1);  //brand_id
                $cond2=intval($cond2);  //page
                $cond3=intval($cond3);  //per page
                $cond4=intval($cond4); // NOT H
                $af_brand=App_TFields::DBselect('cc_brand','all');
                $s="SELECT cc_model.*, cc_brand.name AS bname, cc_brand.text AS btext, cc_brand.replica{$af_brand} FROM cc_model LEFT JOIN cc_brand ON cc_model.brand_id = cc_brand.brand_id WHERE (cc_model.brand_id='$cond1')AND(NOT cc_model.LD)AND(NOT cc_brand.LD)".($cond4?'AND(NOT cc_model.H)AND(NOT cc_brand.H)':'')." ORDER BY cc_model.name";
                if ($cond3>0) 	$s=$s." LIMIT ".$cond2*$cond3.",$cond3";
                $res=$this->query($s);
                break;
            case 'cat_by_model':
                $cond1=intval($cond1); // model_id
                $cond2=Tools::esc($cond2); // ==1 => NOT cc_cat.H
                $cond3=Tools::esc($cond3); // ORDER
                $af_model=App_TFields::DBselect('cc_model','all');
                $af_cat=App_TFields::DBselect('cc_cat','all');
                $res=$this->query("SELECT cc_cat.cat_id, cc_cat.model_id, cc_cat.gr,
cc_cat.is_seo,cc_cat.seo_title, cc_cat.seo_keywords, cc_cat.seo_description, cc_cat.adv_text,
cc_cat.sname, cc_cat.suffix, cc_cat.P1+'0' AS P1, cc_cat.P2+'0' AS P2, cc_cat.P3+'0' AS P3, cc_cat.P4, cc_cat.P5+'0' AS P5, cc_cat.P6+'0' AS P6, cc_cat.bprice, cc_cat.cprice, cc_cat.fixPrice, cc_cat.fixSc, cc_cat.cur_id, cc_cat.upd_id, cc_cat.ft, cc_cat.H, cc_cat.hit_quant, cc_cat.sc, cc_cat.scprice, cc_cat.ti_id, cc_cat.ti_file_id, cc_cat.dt_added, cc_cat.dt_upd, cc_cat.ignoreUpdate, cc_model.name, cc_model.P1 AS MP1, cc_model.P2 AS MP2, cc_model.P3 AS MP3, cc_model.img1, cc_model.img2, cc_model.img3, cc_model.alt, cc_model.suffix AS msuffix{$af_model}{$af_cat} FROM cc_cat INNER JOIN cc_model ON cc_cat.model_id=cc_model.model_id WHERE cc_cat.model_id='$cond1' AND NOT cc_cat.LD AND NOT cc_model.LD".($cond2=='1'?" AND NOT cc_cat.H":'').($cond3!=''?" ORDER BY $cond3":''));
                break;
            case 'cat_by_id':
                $cond1=intval($cond1); // cat_id
                $cond2=intval($cond2); // NOT H
                $af_brand=App_TFields::DBselect('cc_brand','all');
                $af_model=App_TFields::DBselect('cc_model','all');
                $af_cat=App_TFields::DBselect('cc_cat','all');
                $res=$this->query("SELECT cc_cat.cat_id, cc_cat.model_id, cc_cat.gr,
cc_cat.is_seo,cc_cat.seo_title, cc_cat.seo_keywords, cc_cat.seo_description, cc_cat.adv_text,
cc_cat.sname, cc_cat.suffix, cc_cat.P1+'0' AS P1, cc_cat.P2+'0' AS P2, cc_cat.P3+'0' AS P3, cc_cat.P4, cc_cat.P5+'0' AS P5, cc_cat.P6+'0' AS P6, cc_cat.P7, cc_cat.bprice, cc_cat.cprice, cc_cat.fixPrice, cc_cat.fixSc, cc_cat.cur_id, cc_cat.upd_id, cc_cat.ft, cc_cat.H, cc_cat.hit_quant, cc_cat.sc, cc_cat.scprice, cc_cat.ti_id, cc_cat.ti_file_id, cc_cat.dt_added, cc_cat.dt_upd, cc_cat.ignoreUpdate, cc_model.model_id, cc_model.brand_id, cc_model.P1 AS MP1, cc_model.P2 AS MP2, cc_model.P3 AS MP3, cc_model.text, cc_model.class_id, cc_model.mspez_id, cc_model.sticker_id, cc_model.video_link, cc_brand.text AS btext, cc_brand.replica,  cc_brand.img1 AS brand_img1, cc_brand.img2 AS brand_img2, cc_model.name, cc_model.img1, cc_model.img2, cc_model.img3, cc_brand.name AS bname, cc_brand.extra_b AS extra_b, cc_model.alt, cc_brand.alt AS balt, cc_model.suffix AS msuffix, cc_cat.suffix AS csuffix, cc_brand.sname AS brand_sname, cc_model.sname AS model_sname{$af_cat}{$af_model}{$af_brand} FROM cc_brand RIGHT JOIN (cc_cat LEFT JOIN cc_model ON cc_cat.model_id = cc_model.model_id) ON cc_brand.brand_id = cc_model.brand_id WHERE (cat_id='$cond1')AND(NOT cc_model.LD)AND(NOT cc_cat.LD)AND(NOT cc_brand.LD)".($cond2?"AND(NOT cc_model.H)AND(NOT cc_cat.H)AND(NOT cc_brand.H)":''));

                break;
            case 'cat_by_sname':
                $cond1=Tools::like($cond1,true,true,true);  // sname
                $cond2=intval($cond2);  // not H
                $cond3=Tools::like($cond3);   // brand sname
                $cond4=Tools::like($cond4);   // model sname
                $cond5=(int)$cond5;   // gr
                $af_brand=App_TFields::DBselect('cc_brand','all');
                $af_model=App_TFields::DBselect('cc_model','all');
                $af_cat=App_TFields::DBselect('cc_cat','all');
                if(@Cfg::$config['ccTags']['enabled']) $tags=', cc_model.tags AS mTags'; else $tags='';
                $res=$this->query("SELECT cc_cat.cat_id, cc_cat.model_id, cc_cat.gr,
cc_cat.is_seo,cc_cat.seo_title, cc_cat.seo_keywords, cc_cat.seo_description, cc_cat.adv_text,
cc_cat.sname, cc_cat.suffix, cc_cat.P1+'0' AS P1, cc_cat.P2+'0' AS P2, cc_cat.P3+'0' AS P3, cc_cat.P4, cc_cat.P5+'0' AS P5, cc_cat.P6+'0' AS P6, cc_cat.P7, cc_cat.bprice, cc_cat.cprice, cc_cat.fixPrice, cc_cat.fixSc, cc_cat.cur_id, cc_cat.upd_id, cc_cat.ft, cc_cat.H, cc_cat.hit_quant, cc_cat.sc, cc_cat.scprice, cc_cat.ti_id, cc_cat.ti_file_id, cc_cat.dt_added, cc_cat.dt_upd, cc_cat.ignoreUpdate, cc_model.model_id, cc_model.brand_id, cc_model.P1 AS MP1, cc_model.P2 AS MP2, cc_model.P3 AS MP3, cc_model.text, cc_model.class_id, cc_model.mspez_id,  cc_model.video_link, cc_model.sticker_id, cc_brand.text AS btext, cc_brand.replica,  cc_brand.img1 AS brand_img1, cc_brand.img2 AS brand_img2, cc_model.name, cc_model.img1, cc_model.img2, cc_model.img3, cc_brand.name AS bname, cc_model.alt, cc_brand.alt AS balt, cc_model.suffix AS msuffix, cc_cat.suffix AS csuffix, cc_brand.sname AS brand_sname, cc_model.sname AS model_sname{$tags}{$af_cat}{$af_model}{$af_brand} FROM cc_brand RIGHT JOIN (cc_cat LEFT JOIN cc_model ON cc_cat.model_id = cc_model.model_id) ON cc_brand.brand_id = cc_model.brand_id WHERE (BINARY cc_cat.sname = '$cond1')AND(NOT cc_model.LD)AND(NOT cc_cat.LD)AND(NOT cc_brand.LD)".($cond2?"AND(NOT cc_model.H)AND(NOT cc_cat.H)AND(NOT cc_brand.H)":'').($cond3!=''?" AND(cc_brand.sname LIKE '$cond3')":'').($cond4!=''?" AND(cc_model.sname LIKE '$cond4')":'').($cond5?" AND(cc_brand.gr = '$cond5')":''));
                break;
            case 'cur':
                $res=$this->query("SELECT * FROM cc_cur ORDER BY cur_id");
                break;
            case 'hit_list':
                $cond1=intval($cond1);  // gr
                $cond2=intval($cond2);  // limit
                $res=$this->query("SELECT cc_model.*, cc_brand.name AS bname, cc_brand.replica, cc_brand.text AS btext FROM cc_model LEFT JOIN cc_brand ON cc_model.brand_id = cc_brand.brand_id
				WHERE cc_model.gr='$cond1' ORDER BY cc_model.hit_quant DESC LIMIT 0,$cond2");
                break;
            case 'min_price':
                $cond1=intval($cond1);  // gr
                $cond2=intval($cond2);  // model_id
                $res=$this->query("SELECT min(cprice) FROM cc_cat WHERE (gr='$cond1')AND(NOT LD)AND(model_id='$cond2')AND(cprice>0)");
                $this->next(MYSQL_NUM);
                break;
            case 'max_price':
                $cond1=intval($cond1);  // gr
                $cond2=intval($cond2);
                $res=$this->query("SELECT max(cprice) FROM cc_cat WHERE (gr='$cond1')AND(NOT LD)AND(model_id='$cond2')AND(cprice>0)");
                $this->next(MYSQL_NUM);
                break;
            case 'class_list':
                $cond1=intval($cond1);  // gr
                $res=$this->query("SELECT * FROM cc_class WHERE ".($cond1?"(gr='$cond1')AND":'')."(NOT LD)AND(NOT H) ORDER BY gr ASC,pos ASC, name ASC");
                break;
            case 'dict_by_name':
                $cond1=Tools::like($cond1);
                $res=$this->query("SELECT * FROM cc_dict WHERE name LIKE '$cond1'");
                //AND((name LIKE '$cond1 %')OR(name LIKE '% $cond1 %')OR(name LIKE '% $cond1')OR(name LIKE '$cond1'))
                break;
            case 'dict_by_id':
                $cond1=intval($cond1);
                $res=$this->query("SELECT * FROM cc_dict WHERE dict_id='$cond1'");
                break;
            case 'acc_by_id':
                $cond1=intval($cond1);
                $res=$this->query("SELECT * FROM cc_accessories WHERE acc_id='$cond1'");
                break;
            case 'accessories_bind':
                $cond1=intval($cond1);
                $res=$this->query("SELECT * FROM cc_accessories_bind WHERE brand_id='$cond1' AND gr='$cond2'");
                break;

            default: echo 'BAD CASE '.$qname; $res=false;
        }
        return($res);
    }

    /*
     * на вход строку с тегами
     * на выходе массив
     */
    function getTags($tags)
    {
        $tags=explode('.',trim($tags,'.'));
        $r=array();
        foreach($tags as $v){
            if($v!=0) $r[$v]=array();
        }
        return $r;
    }

    function brands($r)
    {
        /*
            ПОСТУПАЕМЫЕ ДАННЫЕ ДОЛЖНЫ БЫТЬ УЖЕ ЭКРАНИРОВАНЫ С ПОМОЩЬЮ Tools::like_() но не экранированы через addslashes()
            Все входные значения экранируются КРОМЕ where,order,select
            Параметры :
                gr - единственный обязательный параметр
                notH - если ==1 то не показывать скрытые модели и бренды, по умолчанию =1
                where - array() || строка доп условие отбора. массив, который будет объединен в строку через AND. Здесь только условия для cc_brand
                having - array() || строка  - условие HAVING для главного запроса (например modelsNum>0) (не совместимо с count)
                whereModel - array() || string доп условие отбора с указанием таблицы cc_model. массив, который будет объединен в строку через AND. Здесь только условия для cc_model - подключается таблица cc_model
                whereCat - array() string - условия для cc_cat, которая будет подключена в случае указания занчения этого поля (если array() то подразумевается перечисление через AND )
                apMode  array(markId=>int)   режим подбора по марке авто: реплика только в связке с avtoId
                mTags - array(id1,id2,...) - список тегов для моделей
                catNotH = {0|1}  - если ==1 то не считать скрытые размеры и модели в случае подключения через JOIN к запросу таблицы cc_cat, по умолчанию ==$r['notH']
                modelNotH = {0|1}  - если ==1 то не считать скрытые модели в случае подключения через JOIN к запросу таблицы cc_model, по умолчанию ==$r['notH']
                 // нюанс - если в запросе будет условия для cc_cat то модели без типоразмеров не включатся в результат запроса
                select - array( array(поле=>алиас))  (можно без указания таблицы)
                order - array() || string -  условия ORDER BY  (можно без указания таблицы) (если array() то перечислятся через запятую
                bsupDiv {false|true}- добавлять в селект (sup_id DIV sup_id) AS bsubDiv  (для gr==2)
                afInclude - включать  в select Tfields для брендов
                count - {0|1}вычислить только кол-во записей (не совместимо с qSelect)

                catOrGroups => array() - 	массив масивов, по которым будет ограничен поиск для таблицы cc_cat. Например
                                catOrGroups=>array(
                                    array(
                                        'P3>15 AND P3<21',
                                        'P1'=>16,
                                        'P2'=>65
                                    ),
                                    array(
                                        'P3'=>17
                                    )
                                )
                                добавит в WHERE запроса условие ... AND (   (P3>15 AND P3<21) AND (P1=16) AND (P2=65) OR (P3=17)  ) ...

                режимы линковки таблиц
                qSelect - array(    дополнительные подзапросы для SELECT
                    кол-во моделей в брендах
                    modelsNum => array(
                        notH=>{0|1}    // по умолчанию ==$r['notH']
                    )
                    кол-во размеров в моделях
                    // в WHERE подзапроса будет также подставлен whereModel и catOrGroups
                    catNum => array(
                        notH=>{0|1}    // по умолчанию ==$r['notH']
                        'where'=>строка с условиями
                    )
                    сумма склада в размерах моделей
                    scSum =>  => array(
                        notH=>{0|1}  // по умолчанию ==$r['notH']
                        'where'=>строка с условиями
                    )
                    список значений параметра
                    // например для выборки всех сезонов моделей для каждого
                    modelConcatGroup => array of array(
                        'field'=>{поле таблицы} без названия таблицы
                        'as'=>{as поля}
                        'notH'=>{0|1}     // по умолчанию ==$r['notH']
                        'separator'=>{строка} // по умолчанию {,}

                    список значений параметра
                    // например для выборки всех радиусов размеров для каждого бренда JOIN моделей
                    // в WHERE подзапроса будет также подставлен whereModel и catOrGroups
                    catConcatGroup => array of array(
                        'field'=>{поле таблицы}   без названия таблицы
                        'as'=>{as поля}
                        'notH'=>{0|1}     // по умолчанию ==$r['notH']
                        'separator'=>{строка} // по умолчанию {,}
                        'where'=>строка с условиями


        */

        $t1=Tools::getMicroTime();

        if(empty($r['gr'])) return $this->putMsg(false,'Не задана категория [gr]');
        if(empty($r['select'])) $r['select']=array(
            'cc_brand.name'=>'name',
            'cc_brand.alt'=>'alt',
            'cc_brand.sname'=>'sname',
            'cc_brand.text'=>'text',
            'cc_brand.img1'=>'img1',
            'cc_brand.img2'=>'img2',
            'cc_brand.pos'=>'pos',
            'cc_brand.replica'=>'replica',
            'cc_brand.sup_id'=>'bsup_id',
            'cc_brand.avto_id'=>'avto_id'
        );

        if(empty($r['order']))
        {
            if($r['gr']==1) $r['order']=array('cc_brand.name ASC');
            elseif($r['gr']==2) {
                $r['order']=array('cc_brand.replica ASC','cc_brand.name ASC');
                if(!empty($r['bsupDiv'])) array_unshift($r['order'],'bsupDiv ASC');
            }
        }else $r['order']=$r['order'];

        $w=array('NOT cc_brand.LD',"gr={$r['gr']}");

        if(!empty($r['apMode']['markId'])) $w[]="(cc_brand.avto_id='".(int)$r['apMode']['markId']."' AND cc_brand.replica=1  OR cc_brand.avto_id=0)";

        $wm=$wc='';

        if(!empty($r['where']))  // WHERE
            if(is_array($r['where'])) $w=array_merge($w,$r['where']);
            else array_push($w,"({$r['where']})");

        if(!empty($r['whereModel']))// WHERE для подзапросов с cc_model без cc_cat
            if(is_array($r['whereModel'])) $wm=implode(' AND ',$r['whereModel']);
            else $wm="({$r['whereModel']})";

        if(!empty($r['mTags'])){
            if(!is_array($r['mTags'])) $v=array((int)$r['mTags']); else $v=$r['mTags'];
            $qt=array();
            if(!empty($v)) foreach($v as $xv){
                $xv=(int)$xv;
                $qt[]="(cc_model.tags LIKE '.$xv.%' OR cc_model.tags LIKE '.%.$xv.%')";
            }
            if(!empty($qt)) {
                $qt= ' ('.implode(' OR ',$qt).') ';
                if(!empty($wm)) $wm.=" AND $qt"; else $wm=$qt;
            }
        }


        if(!empty($r['whereCat']))// WHERE для подзапросов с cc_cat
            if(is_array($r['whereCat'])) $wc=implode(' AND ',$r['whereCat']);
            else $wc="({$r['whereCat']})";

        if(!empty($r['having']))
            if(is_array($r['having'])) $having=implode(' AND ',$r['having']);
            else $having=$r['having'];

        if(!empty($having)) $having=" HAVING $having"; else $having='';


        if(!isset($r['notH'])) $r['notH']=1;
        if(!empty($r['notH'])) array_push($w,'NOT cc_brand.H');
        if(!empty($r['afInclude'])) $af_brand=App_TFields::Dbselect('cc_brand',$r['gr']); else $af_brand='';

        if(!empty($r['order']))// ORDER BY
            if(is_array($r['order'])) $order=implode(', ',$r['order']);
            else $order=$r['order'];

        if(!empty($r['bsupDiv'])) $r['select']['IFNULL((cc_brand.sup_id DIV cc_brand.sup_id),0)']='bsupDiv';

        $select=array();
        foreach($r['select'] as $k=>$v) $select[]="$k AS $v";
        $select=implode(', ',$select).$af_brand; // поля SELECT

        $catOrGroups=array();
        if(!empty($r['catOrGroups'])){
            foreach($r['catOrGroups'] as $gv){
                if(is_array($gv)){
                    $row=array();
                    foreach($gv as $k=>$v){
                        if(is_string($k)){
                            if(strpos($k,'cc_')===false) $k="cc_cat.$k";
                            $k=Tools::esc($k);
                            if(is_numeric($v)) $row[]="$k = '$v'";
                            elseif($v!=='') $row[]="$k LIKE '$v'";
                        }else{
                            $row[]="($v)";
                        }

                    }
                    if(!empty($row)) $catOrGroups[]=implode(' AND ',$row);
                }
            }
        }

        $catOrGroups=implode(' OR ',$catOrGroups);
        if(!empty($catOrGroups)) $catOrGroups="( $catOrGroups )";

        if(empty($r['count'])){
            if(!empty($r['qSelect'])){
                foreach($r['qSelect'] as $type=>$data){
                    switch($type){
                        case 'catNum':
                            if(!isset($data['notH'])) $data['notH']=$r['notH'];
                            $select.=','.
                                "(SELECT COUNT(cc_cat.cat_id) FROM cc_model JOIN cc_cat USING (model_id) WHERE cc_model.brand_id=cc_brand.brand_id"
                                .(!empty($data['notH'])?" AND NOT cc_cat.H AND NOT cc_model.H":'')
                                .(!empty($wm)?" AND $wm":'')
                                .(!empty($data['where'])?" AND {$data['where']}":'')
                                .(!empty($catOrGroups)?" AND $catOrGroups":'')
                                ." AND NOT cc_cat.LD AND NOT cc_model.LD)  AS catNum";
                            break;
                        case 'scSum':
                            if(!isset($data['notH'])) $data['notH']=$r['notH'];
                            $select.=','.
                                "(SELECT SUM(sc) FROM cc_model JOIN cc_cat USING (model_id) WHERE cc_model.brand_id=cc_brand.brand_id WHERE NOT cc_model.LD AND NOT cc_cat.LD"
                                .(!empty($data['notH'])?" AND NOT cc_cat.H AND NOT cc_model.H":'')
                                .(!empty($wm)?" AND $wm":'')
                                .(!empty($data['where'])?" AND {$data['where']}":'')
                                .(!empty($catOrGroups)?" AND $catOrGroups":'')
                                .")  AS scSum";
                            break;
                        case 'catConcatGroup':
                            foreach($data as $g)
                                if(is_array($g) && !empty($g['as']) && !empty($g['field'])){
                                    if(!isset($g['notH'])) $g['notH']=$r['notH'];
                                    if(empty($g['separator'])) $g['separator']=',';
                                    if(in_array($g['field'],array('P1','P2','P3','P5','P6'))) $p0="+'0'"; else $p0='';
                                    $select.=','.
                                        "(SELECT GROUP_CONCAT(DISTINCT cc_cat.{$g['field']}{$p0} SEPARATOR '{$g['separator']}') FROM cc_model JOIN cc_cat USING (model_id) WHERE cc_model.brand_id=cc_brand.brand_id"
                                        .(!empty($g['notH'])?" AND NOT H":'')
                                        .(!empty($wm)?" AND $wm":'')
                                        .(!empty($wc)?" AND $wc":'')
                                        .(!empty($data['where'])?" AND {$data['where']}":'')
                                        .(!empty($catOrGroups)?" AND $catOrGroups":'')
                                        ." AND NOT cc_model.LD AND NOT cc_cat.LD) AS {$g['as']}";
                                }
                            break;
                        case 'modelConcatGroup':
                            foreach($data as $g)
                                if(is_array($g) && !empty($g['as']) && !empty($g['field'])){
                                    if(!isset($g['notH'])) $g['notH']=$r['notH'];
                                    if(empty($g['separator'])) $g['separator']=',';
                                    if(!empty($wc) || !empty($catOrGroups))
                                        $select.=','.
                                            "(SELECT GROUP_CONCAT(DISTINCT {$g['field']} SEPARATOR '{$g['separator']}') FROM "
                                            ."(SELECT model_id FROM cc_cat WHERE NOT LD "
                                            .(!empty($data['notH'])?" AND NOT cc_cat.H":'')
                                            .(!empty($wc)?" AND $wc":'')
                                            .(!empty($catOrGroups)?" AND $catOrGroups":'')
                                            ." GROUP BY model_id) cii JOIN cc_model USING (model_id)"

                                            ." WHERE cc_model.brand_id=cc_brand.brand_id AND NOT cc_model.LD"
                                            .(!empty($data['notH'])?" AND NOT cc_model.H":'')
                                            .(!empty($wm)?" AND $wm":'')
                                            .") AS {$g['as']}";
                                    else
                                        $select.=','.
                                            "(SELECT GROUP_CONCAT(DISTINCT {$g['field']} SEPARATOR '{$g['separator']}') FROM cc_model WHERE NOT LD AND cc_model.brand_id=cc_brand.brand_id"
                                            .(!empty($data['notH'])?" AND NOT H":'')
                                            .(!empty($wm)?" AND $wm":'')
                                            .") AS {$g['as']}";
                                }
                            break;
                        case 'modelsNum':
                            if(!isset($data['notH'])) $data['notH']=$r['notH'];
                            if(!empty($wc) || !empty($catOrGroups))
                                $select.=','.
                                    "(SELECT COUNT(model_id) FROM "
                                    ."(SELECT model_id FROM cc_cat WHERE NOT LD "
                                    .(!empty($data['notH'])?" AND NOT cc_cat.H":'')
                                    .(!empty($wc)?" AND $wc":'')
                                    .(!empty($catOrGroups)?" AND $catOrGroups":'')
                                    ." GROUP BY model_id) cii JOIN cc_model USING (model_id)"

                                    ." WHERE cc_model.brand_id=cc_brand.brand_id AND NOT cc_model.LD"
                                    .(!empty($data['notH'])?" AND NOT cc_model.H":'')
                                    .(!empty($wm)?" AND $wm":'')
                                    .") AS modelsNum";
                            else
                                $select.=','.
                                    "(SELECT COUNT(model_id) FROM cc_model WHERE NOT LD AND cc_model.brand_id=cc_brand.brand_id"
                                    .(!empty($data['notH'])?" AND NOT H":'')
                                    .(!empty($wm)?" AND $wm":'')
                                    .") AS modelsNum";
                            break;
                        case 'bsupModelsNum':
                            if(!isset($data['notH'])) $data['notH']=$r['notH'];
                            if(!empty($wc) || !empty($catOrGroups))
                                $select.=','.
                                    "(IF(cc_brand.sup_id>0,(SELECT COUNT(model_id) FROM "
                                    ."(SELECT model_id FROM cc_cat WHERE NOT LD "
                                    .(!empty($data['notH'])?" AND NOT cc_cat.H":'')
                                    .(!empty($wc)?" AND $wc":'')
                                    .(!empty($catOrGroups)?" AND $catOrGroups":'')
                                    ." GROUP BY model_id) cmii JOIN cc_model USING (model_id)"

                                    ." WHERE cc_model.sup_id=cc_brand.sup_id AND NOT cc_model.LD"
                                    .(!empty($data['notH'])?" AND NOT cc_model.H":'')
                                    .(!empty($wm)?" AND $wm":'')
                                    ."),0)) AS bsupModelsNum";
                            else
                                $select.=','.
                                    "(IF(cc_brand.sup_id>0,(SELECT COUNT(model_id) FROM cc_model WHERE NOT LD AND cc_model.sup_id=cc_brand.sup_id"
                                    .(!empty($data['notH'])?" AND NOT H":'')
                                    .(!empty($wm)?" AND $wm":'')
                                    ."),0)) AS bsupModelsNum";
                            break;
                    }
                }
            }


        }
        /*
        SELECT cc_brand.name, sup_id, IFNULL((sup_id DIV sup_id),0) AS bsupDiv,

        (SELECT COUNT(cc_model.model_id) FROM cc_model JOIN cc_cat USING (model_id) WHERE cc_model.brand_id=cc_brand.brand_id AND NOT cc_model.H AND NOT cc_model.LD AND NOT cc_cat.LD AND NOT cc_cat.H  AND cc_cat.P5=13 AND cc_cat.sc>0) AS modelsNum

        FROM
        ( SELECT brand_id FROM cc_model WHERE NOT LD AND NOT H  GROUP BY brand_id ) AS cc_model JOIN cc_brand USING (brand_id)
        или
        ( SELECT cc_model.brand_id FROM cc_model JOIN cc_cat USING (model_id) WHERE NOT cc_model.LD AND NOT cc_model.H AND NOT cc_cat.H AND NOT cc_cat.LD AND cc_cat.P5=13 AND cc_cat.sc>0  GROUP BY cc_model.brand_id ) AS mc JOIN cc_brand USING (brand_id)
        или
        ( SELECT brand_id FROM
             ( SELECT model_id FROM cc_cat WHERE NOT LD AND NOT H GROUP BY cc_cat.model_id ) AS ccc
             JOIN cc_model USING (model_id)
        WHERE NOT LD AND NOT H AND P1=2 GROUP BY cc_model.brand_id ) AS mc
        JOIN cc_brand USING (brand_id)

        WHERE cc_brand.gr=2  AND NOT cc_brand.LD AND NOT cc_brand.H

        ORDER BY bsupDiv DESC, replica DESC, cc_brand.name
        */

        if(!isset($r['catNotH'])) $r['catNotH']=$r['notH'];
        if(!isset($r['modelNotH'])) $r['modelNotH']=$r['notH'];


        if(!empty($catOrGroups) || !empty($wc)) {
            $wc=Tools::uJoin(' AND ',array($catOrGroups,$wm,$wc));
            $ww="(SELECT cc_model.brand_id FROM cc_model JOIN cc_cat USING (model_id) WHERE NOT cc_model.LD  AND NOT cc_cat.LD ".
                (!empty($r['catNotH'])?"AND NOT cc_cat.H ":'').
                (!empty($r['modelNotH'])?"AND NOT cc_model.H ":'').
                (!empty($r['d_type'])?"AND cc_model.P1 = '{$r['d_type']}'":'').
                "AND $wc GROUP BY cc_model.brand_id) mi  JOIN cc_brand USING (brand_id) ";

        } elseif(!empty($wm)) {
            $ww="( SELECT brand_id FROM cc_model WHERE NOT LD AND $wm".(!empty($r['modelNotH'])?"AND NOT H ":'')." GROUP BY brand_id ) AS cc_model JOIN cc_brand USING (brand_id) ";
        } else {
            $ww="cc_brand ";
        }

        $w=implode(' AND ',$w); // WHERE

        $q1="SELECT $select FROM $ww WHERE $w $having".(!empty($order)?" ORDER BY $order":'');
        $q2="SELECT Count(cc_model.model_id) FROM $ww WHERE $w";

        $this->rt=$t1-Tools::getMicroTime();

        if(!empty($r['sqlReturn']))
            if(!empty($r['count'])) return $q2; else return $q1;

        if(!empty($r['count'])){
            $n=$this->getOne($q2);
            $n=$n[0];
            $this->rt=$t1-Tools::getMicroTime();
            return $n;
        }else{
            $this->query($q1);
            $n=$this->qnum();
            $this->rt=$t1-Tools::getMicroTime();
            return $n;
        }

    }

    function models($r)
    {
        return $this->model_view($r);
    }

    function model_view($r)
    {

        /*
            ПОСТУПАЕМЫЕ на первом уровне массива $r, ДАННЫЕ ДОЛЖНЫ БЫТЬ УЖЕ ЭКРАНИРОВАНЫ С ПОМОЩЬЮ Tools::like_() но не экранированы через addslashes()
            Все нижеперечисленные параметры не экранируются!
            Параметры :
                gr - единственный обязательный параметр
                start,lines,
                seekMode
                rf - 0|1  искать шины ранфлет. если ==0 то исключить ранфлет из поиска
                H/notH - если ==1 то не показывать скрытые модели и бренды, по умолчанию =1
                (упразднили) - sea_name - поиск по сроке в моделях
                where/add_query - array()||string - дополнительные условия WHERE только для cc_brand и cc_model
                whereCat - array() string - условия для cc_cat, которая будет подключена в случае указания занчения этого поля (если array() то подразумевается перечисление через AND )  Здесь можно указывать условия для cc_model & cc_brand - тогда эти таблицы будут тоже слинкованы в подзапрос
                having - array() || строка  - условие HAVING
                catNotH = {0|1}  - если ==1 то не показывать скрытые размеры в случае подключения к запросу таблицы cc_cat, по умолчанию ==$r['notH']
                order array()||string - дополнительное условия для ORDER BY
                nolimits - не вычислять общее кол-во записей (-1 запрос)
                select - string   -добавление строки к полям выбора полей в запросе, т.е. после SELECT. Надо учитывать, что в запросе count() этот параметр не участвует.
                'dataset_id','datasetTo' - только данные из набора.
                ex => {1|0} - екстракция значений полей
                exFields =>array() список полей для экстракции
                count - {0|1}вычислить только кол-во записей (не совместимо с qSelect)
                mTags - array(id1,id2,...) - список тегов для моделей
                imgLenDiv  (0|1) добавить к селекту ((@len:=LENGTH (cc_model.img1)) DIV @len) AS imgLenDiv    (к SELECT Count()  не добалвяются)
                apMode  array(markId=>int)   режим подбора по марке авто: реплика только в связке с avtoId

                catOrGroups => array() - 	массив масивов, по которым будет ограничен поиск для таблицы cc_cat. Например
                                catOrGroups=>array(
                                    array(
                                        'P3>15 AND P3<21',
                                        'P1'=>16,
                                        'P2'=>65
                                    ),
                                    array(
                                        'P3'=>17
                                    )
                                )
                                добавит в WHERE запроса условие ... AND (   (P3>15 AND P3<21) AND (P1=16) AND (P2=65) OR (P3=17)  ) ...


                дополнительные подзапросы для SELECT, для таблицы cc_cat (cc_cat будет линковаться с основным запросом) // в WHERE подзапроса будет также подставлен catOrGroups
                qSelect - array(
                    кол-во размеров в моделях
                    catNum => array(
                        notH=>{0|1}    // по умолчанию ==$r['notH']
                        where=>строка с условиями
                        countSelect=>{0|1} // добавить к запросу SELECT count()... | по умолчанию 0
                    )
                    сумма склада в размерах моделей
                    scSum =>  => array(
                        notH=>{0|1}  // по умолчанию ==$r['notH']
                        where=>строка с условиями
                        countSelect=>{0|1} // добавить к запросу SELECT count()... | по умолчанию 0
                    )
                    0 | 1 - есть нет на складе хотябы один типоразмер
                    scDiv =>  => array(
                        notH=>{0|1}  // по умолчанию ==$r['notH']
                        where=>строка с условиями
                        countSelect=>{0|1} // добавить к запросу SELECT count()... | по умолчанию 0
                    )
                    0 | 1 - колонка для сортировке по возрастающей цене
                    cpriceDiv =>  => array(
                        notH=>{0|1}  // по умолчанию ==$r['notH']
                        where=>строка с условиями
                        countSelect=>{0|1} // добавить к запросу SELECT count()... | по умолчанию 0
                    )
                    мин цены в  моделях
                    minPrice =>  => array(
                        notH=>{0|1}  // по умолчанию ==$r['notH']
                        where=>строка с условиями
                        countSelect=>{0|1} // добавить к запросу SELECT count()... | по умолчанию 0
                    )
                    мин/макс цены в  моделях
                    minMaxPrices =>  => array(
                        notH=>{0|1}  // по умолчанию ==$r['notH']
                        where=>строка с условиями
                        countSelect=>{0|1} // добавить к запросу SELECT count()... | по умолчанию 0
                    )
                    список значений параметра // например для выборки всех радиусов размеров для каждой модели
                    concatGroup => array of array(
                        'field'=>{поле таблицы}
                        'as'=>{as поля}
                        'notH'=>{0|1}     // по умолчанию ==$r['notH']
                        'separator'=>{строка} // по умолчанию {,}
                        where=>строка с условиями
                        countSelect=>{0|1} // добавить к запросу SELECT count()... | по умолчанию 0


            *** конец параметрам

            С указанием таблиц cc_brand. можно передавать любые ее поля
            Без указания cc_brand. можно передавать:
                replica

            Элементы $r не распознанные как параметры и как явно указанные поля таблиц, дополняются cc_model.

            Для каждого параметра возможны диапазоны  и перечисления array('from'=>..,'to'=>..., 'list'=>..;.., 'like'=>...;..

        */
        $t1=Tools::getMicroTime();

        if(!@$r['gr']) return false;

        $s='NOT cc_model.LD  AND NOT cc_brand.LD ';  // WHERE

        foreach($r as $k=>$v) if($v!==''){
            $k=trim($k);
            if($k=='mTags'){
                if(!is_array($v)) $v=array((int)$v);
                $sq=array();
                if(!empty($v)) foreach($v as $xv){
                    $xv=(int)$xv;
                    $sq[]="(cc_model.tags LIKE '.$xv.%' OR cc_model.tags LIKE '.%.$xv.%')";
                }
                if(!empty($sq)) $s.= ' AND ('.implode(' OR ',$sq).') ';
            }else{
                if(!in_array($k,array('start','lines','H','notH','where','add_query','having','order','nolimits','select','dataset_id','datasetTo','count','catOrGroups','qSelect','catNotH','sqlReturn','ex','exFields','whereCat','seekMode','rf','countSelect','imgLenDiv','apMode')))
                {
                    if($k=='replica') $k='cc_brand.replica';
                    elseif(mb_strpos($k,'cc_')===false) $k='cc_model.'.$k; // если явно не указан префикс таблицы cc_ то добавляем cc_model

                    $k=Tools::esc($k);

                    if(is_array($v)){
                        if(isset($v['from'])) {
                            $v['from']=Tools::esc($v['from']);
                            if($v['from']!=='') $s=$s." AND ($k >= '{$v['from']}')";
                        }
                        if(isset($v['to'])) {
                            $v['to']=Tools::esc($v['to']);
                            if($v['to']!=='') $s=$s." AND ($k <= '{$v['to']}')";
                        }
                        if(isset($v['list'])) {
                            if(is_array($v['list'])) $x=$v['list']; else $x=explode(';',$v['list']);
                            $aa=array();
                            foreach($x as $xv){
                                if(is_numeric($xv)) {
                                    $xv=(float)$xv;
                                    $aa[]="$k = '$xv'";
                                }else{
                                    $xv=Tools::esc($xv);
                                    $aa[]="$k LIKE '$xv'";
                                }
                            }
                            if(count($aa)) {
                                $s.=" AND (".implode(' OR ',$aa).")";
                            }
                        }
                        if(isset($v['like'])) {
                            if(is_array($v['like'])) $x=$v['like']; else $x=explode(';',$v['like']);

                            $aa=array();
                            foreach($x as $xv)
                                if($xv!=''){
                                    $xv=Tools::esc($xv);
                                    $aa[]="$k LIKE '%$xv%'";
                                }
                            if(count($aa)) {
                                $s.=" AND (".implode(' OR ',$aa).")";
                            }
                        }
                    }else {
                        if(is_numeric($v)) {
                            $v=(float)$v;
                            $s.="AND ($k = '$v')";
                        }
                        elseif($v!=''){
                            $v=Tools::esc($v);
                            $s.="AND ($k LIKE '$v')";
                        }
                    }
                }
            }
        }

        if(!empty($r['apMode']['markId'])) $s.=" AND (cc_brand.avto_id='".(int)$r['apMode']['markId']."' AND cc_brand.replica=1  OR cc_brand.avto_id=0)";

        if(empty($r['exFields'])) $r['ex']=0;

        if(!isset($r['H']) && !isset($r['notH']) || !empty($r['notH']) || !empty($r['H'])) $r['notH']=true; else $r['notH']=false;

        if (empty($r['where'])) $r['where']=@$r['add_query'];


        if(!empty($r['where']))// WHERE основного запроса
            if(is_array($r['where'])) $s.=' AND '.implode(' AND ',$r['where']);
            else $s.=" AND ({$r['where']})";


        if(!empty($r['whereCat']))// WHERE для подзапросов с cc_cat
            if(is_array($r['whereCat'])) $wc=implode(' AND ',$r['whereCat']);
            else $wc="({$r['whereCat']})";

        if(!empty($r['select'])) $select=','.$r['select']; else $select='';

        if(!empty($r['having']))
            if(is_array($r['having'])) $having=implode(' AND ',$r['having']);
            else $having=$r['having'];

        if(!empty($having)) $having=" HAVING $having"; else $having='';

        if(!empty($r['order']))// ORDER BY
            if(is_array($r['order'])) $order=implode(', ',$r['order']);
            else $order=$r['order'];

        $af_brand=App_TFields::DBselect('cc_brand',$r['gr']);
        $af_model=App_TFields::DBselect('cc_model',$r['gr']);

        if(@$r['datasetTo']!='' && @$r['dataset_id']>0){
            switch($r['datasetTo']){
                case 'model': $dsModel=true; break;
            }
        }

        if(isset($r['rf']) && $r['gr']==1){
            $rfs=trim(Data::get('сс_runflat_suffix'));
            if($rfs=='') $rfs=array('RunFlat','Run Flat'); else $rfs=preg_split("/[;,]/",$rfs);
            foreach($rfs as $k1=>$v1) $rfs[$k1]=trim($v1);
            $rfs=Tools::usortStr($rfs,'DESC');
            if(count($rfs)){
                if($r['rf']){
                    foreach($rfs as $k1=>$v1) {
                        $rfs[$k1]="cc_cat.suffix LIKE '$v1' OR cc_cat.suffix LIKE '% $v1' OR cc_cat.suffix LIKE '$v1 %' OR cc_cat.suffix LIKE '% $v1 %'";
                    }
                    if(!empty($wc)) $wc.=" AND (".implode(' OR ',$rfs).")"; else $wc='('.implode(' OR ',$rfs).')';
                }else{
                    foreach($rfs as $k1=>$v1) {
                        $rfs[$k1]="cc_cat.suffix NOT LIKE '$v1' AND cc_cat.suffix NOT LIKE '% $v1' AND cc_cat.suffix NOT LIKE '$v1 %' AND cc_cat.suffix NOT LIKE '% $v1 %'";
                    }
                    if(!empty($wc)) $wc.=" AND ".implode(' AND ',$rfs); else $wc=implode(' AND ',$rfs);
                }

            }
        }
        
        $catOrGroups=array();
        if(!empty($r['catOrGroups'])){
            foreach($r['catOrGroups'] as $gv){
                if(is_array($gv)){
                    $row=array();
                    foreach($gv as $k=>$v){
                        if(is_string($k)){
                            if(strpos($k,'cc_')===false) $k="cc_cat.$k";
                            $k=Tools::esc($k);
                            if(is_numeric($v)) $row[]="$k = '$v'";
                            elseif($v!=='') $row[]="$k LIKE '$v'";
                        }else{
                            $row[]="($v)";
                        }

                    }
                    if(!empty($row)) $catOrGroups[]=implode(' AND ',$row);
                }
            }
        }


        $catOrGroups=implode(' OR ',$catOrGroups);
        if(!empty($catOrGroups)) $catOrGroups="( $catOrGroups )";

        $countSelect='Count(cc_model.model_id)';
        if(!empty($r['countSelect'])) $countSelect.=','.$r['countSelect'];

        if(!empty($r['qSelect'])){

            foreach($r['qSelect'] as $type=>$data){
                switch($type){
                    case 'catNum':
                        if(!isset($data['notH'])) $data['notH']=$r['notH'];
                        $select.=$ss=','.
                            "(SELECT COUNT(cat_id) FROM cc_cat WHERE cc_cat.model_id=cc_model.model_id AND NOT LD"
                            .(!empty($data['notH'])?" AND NOT H":'')
                            .(!empty($data['where'])?" AND {$data['where']}":'')
                            .(!empty($catOrGroups)?" AND $catOrGroups":'')
                            .(!empty($wc)?" AND $wc":'')
                            ." ) AS catNum";
                        if(!empty($data['countSelect'])) $countSelect.=$ss;
                        break;
                    case 'catSname1':
                        if(!isset($data['notH'])) $data['notH']=$r['notH'];
                        $select.=$ss=','.
                            "(SELECT sname FROM cc_cat WHERE cc_cat.model_id=cc_model.model_id AND NOT LD"
                            .(!empty($data['notH'])?" AND NOT H":'')
                            .(!empty($data['where'])?" AND {$data['where']}":'')
                            .(!empty($catOrGroups)?" AND $catOrGroups":'')
                            .(!empty($wc)?" AND $wc":'')
                            ." LIMIT 1"
                            .") AS catSname1";
                        if(!empty($data['countSelect'])) $countSelect.=$ss;
                        break;
                    case 'scSum':
                        if(!isset($data['notH'])) $data['notH']=$r['notH'];
                        $select.=$ss=','.
                            "(SELECT SUM(sc) FROM cc_cat WHERE cc_cat.model_id=cc_model.model_id AND NOT LD"
                            .(!empty($data['notH'])?" AND NOT H":'')
                            .(!empty($data['where'])?" AND {$data['where']}":'')
                            .(!empty($catOrGroups)?" AND $catOrGroups":'')
                            .(!empty($wc)?" AND $wc":'')
                            .") AS scSum";
                        if(!empty($data['countSelect'])) $countSelect.=$ss;
                        break;
                    case 'scDiv':
                        if(!isset($data['notH'])) @$data['notH']=$r['notH'];
                        $select.=$ss=','.
                            "(SELECT IFNULL((SUM(sc) DIV SUM(sc)), 0) FROM cc_cat WHERE cc_cat.model_id=cc_model.model_id AND NOT LD"
                            .(!empty($data['notH'])?" AND NOT H":'')
                            .(!empty($data['where'])?" AND {$data['where']}":'')
                            .(!empty($catOrGroups)?" AND $catOrGroups":'')
                            .(!empty($wc)?" AND $wc":'')
                            .") AS scDiv";
                        if(!empty($data['countSelect'])) $countSelect.=$ss;
                        break;
                    case 'cpriceDiv':
                        if(!isset($data['notH'])) @$data['notH']=$r['notH'];
                        $select.=$ss=','.
                            "(SELECT IFNULL((MAX(cprice) DIV MAX(cprice)), 0) FROM cc_cat WHERE cc_cat.model_id=cc_model.model_id AND NOT LD"
                            .(!empty($data['notH'])?" AND NOT H":'')
                            .(!empty($data['where'])?" AND {$data['where']}":'')
                            .(!empty($catOrGroups)?" AND $catOrGroups":'')
                            .(!empty($wc)?" AND $wc":'')
                            .") AS cpriceDiv";
                        if(!empty($data['countSelect'])) $countSelect.=$ss;
                        break;
                    case 'minPrice':
                        if(!isset($data['notH'])) $data['notH']=$r['notH'];
                        $select.=$ss=','.
                            "(SELECT MIN(cprice) FROM cc_cat WHERE cc_cat.model_id=cc_model.model_id AND NOT LD AND cprice>0"
                            .(!empty($data['notH'])?" AND NOT H":'')
                            .(!empty($data['where'])?" AND {$data['where']}":'')
                            .(!empty($catOrGroups)?" AND $catOrGroups":'')
                            .(!empty($wc)?" AND $wc":'')
                            .") AS minPrice";
                        if(!empty($data['countSelect'])) $countSelect.=$ss;
                        break;
                    case 'minMaxPrices':
                        if(!isset($data['notH'])) $data['notH']=$r['notH'];
                        $select.=$ss=','.
                            "(SELECT CONCAT(MIN(cprice),'-',MAX(cprice)) FROM cc_cat WHERE cc_cat.model_id=cc_model.model_id AND NOT LD AND cprice>0"
                            .(!empty($data['notH'])?" AND NOT H":'')
                            .(!empty($data['where'])?" AND {$data['where']}":'')
                            .(!empty($catOrGroups)?" AND $catOrGroups":'')
                            .(!empty($wc)?" AND $wc":'')
                            .") AS minMaxPrices";
                        if(!empty($data['countSelect'])) $countSelect.=$ss;
                        break;
                    case 'concatGroup':
                        foreach($data as $g)
                            if(is_array($g) && !empty($g['as']) && !empty($g['field'])){
                                if(!isset($g['notH'])) $g['notH']=$r['notH'];
                                if(empty($g['separator'])) $g['separator']=',';
                                if(in_array($g['field'],array('P1','P2','P3','P5','P6'))) $p0="+'0'"; else $p0='';
                                $select.=$ss=','.
                                    "(SELECT GROUP_CONCAT(DISTINCT {$g['field']}{$p0} ORDER BY {$g['field']} SEPARATOR '{$g['separator']}') FROM cc_cat WHERE cc_cat.model_id=cc_model.model_id AND NOT LD"
                                    .(!empty($g['notH'])?" AND NOT H":'')
                                    .(!empty($data['where'])?" AND {$data['where']}":'')
                                    .(!empty($catOrGroups)?" AND $catOrGroups":'')
                                    .(!empty($wc)?" AND $wc":'')
                                    .") AS {$g['as']}";
                            }
                        if(!empty($data['countSelect'])) $countSelect.=$ss;
                        break;
                }
            }
        }


        /*
        SELECT cc_brand.name,cc_model.name
        ,
        (SELECT SUM(cc_cat.sc)  FROM cc_cat WHERE cc_cat.model_id=cc_model.model_id AND NOT cc_cat.H AND NOT cc_cat.LD AND P2='65') AS catSC
        ,
        (SELECT COUNT(cc_cat.cat_id)  FROM cc_cat WHERE cc_cat.model_id=cc_model.model_id AND NOT cc_cat.H AND NOT cc_cat.LD AND  P2='65') AS catNum
        ,
        (SELECT CONCAT(MIN(cc_cat.cprice),'-',MAX(cc_cat.cprice))  FROM cc_cat WHERE cc_cat.model_id=cc_model.model_id AND NOT cc_cat.H AND NOT cc_cat.LD AND  P2='65') AS cpriceDia
        ,
        (SELECT GROUP_CONCAT(DISTINCT cc_cat.P1 SEPARATOR ',')  FROM cc_cat WHERE cc_cat.model_id=cc_model.model_id AND NOT cc_cat.H AND NOT cc_cat.LD AND  P2='65') AS R

        FROM  (SELECT model_id  FROM cc_cat WHERE  P2='65' AND  NOT LD AND NOT H GROUP BY model_id) AS mi

        JOIN cc_model USING (model_id) JOIN cc_brand USING(brand_id) WHERE

        cc_brand.gr=1  AND NOT cc_model.LD AND NOT cc_brand.LD  AND NOT cc_brand.H AND NOT cc_model.H

        ORDER BY cc_brand.name, cc_model.name


        */

        if(!empty($catOrGroups) || !empty($wc)){
            if(!isset($r['catNotH'])) $r['catNotH']=$r['notH'];

            $joinMB=$hideMB='';
            if(mb_strpos(@$wc,'cc_brand.')) {
                $joinMB=" JOIN cc_model USING (model_id) JOIN cc_brand USING (brand_id) ";
                $hideMB=" AND NOT cc_brand.H AND NOT cc_brand.LD AND NOT cc_model.H AND NOT cc_model.LD ";
            }
            else if(mb_strpos(@$wc,'cc_model.')) {
                $joinMB=" JOIN cc_model USING (model_id) ";
                $hideMB=" AND NOT cc_model.H AND NOT cc_model.LD ";
            }

            if(!empty($wc)) $wc=Tools::uJoin(' AND ',array($catOrGroups,$wc)); else $wc=Tools::uJoin(' AND ',array($catOrGroups));

            if(@$dsModel){

                $ww="(SELECT model_id $joinMB FROM cc_cat WHERE  NOT cc_cat.LD ".
                    (!empty($r['catNotH'])?"AND cc_cat.NOT H ":'').
                    " AND $wc ".
                    ($r['notH']?$hideMB:'').
                    " GROUP BY cc_cat.model_id) mi JOIN cc_dataset_model USING (model_id) JOIN cc_model USING (model_id) JOIN cc_brand ON cc_model.brand_id=cc_brand.brand_id ".
                    "WHERE $s AND dataset_id='{$r['dataset_id']}'".
                    ($r['notH']?" AND NOT cc_model.H AND NOT cc_brand.H":'');

            }else{
                $ww="(SELECT model_id FROM cc_cat $joinMB WHERE NOT cc_cat.LD ".
                    (!empty($r['catNotH'])?"AND NOT cc_cat.H ":'').
                    " AND $wc ".
                    ($r['notH']?$hideMB:'').
                    " GROUP BY cc_cat.model_id) mi JOIN cc_model USING (model_id) JOIN cc_brand USING (brand_id) ".
                    " WHERE $s".
                    ($r['notH']?" AND NOT cc_model.H AND NOT cc_brand.H":'');
            }
        } else {

            if(@$dsModel){

                $ww="cc_dataset_model JOIN cc_model USING (model_id) JOIN cc_brand ON cc_model.brand_id=cc_brand.brand_id ".
                    "WHERE $s AND dataset_id='{$r['dataset_id']}'".
                    ($r['notH']?" AND NOT cc_model.H AND NOT cc_brand.H":'');

            }else{
                $ww="cc_model JOIN cc_brand USING(brand_id) ".
                    "WHERE $s".
                    ($r['notH']?" AND NOT cc_model.H AND NOT cc_brand.H":'');
            }
        }

        if(!empty($r['imgLenDiv'])) $select.=($select!=''?', ':'').'((@imgLen:=LENGTH (cc_model.img1)) DIV @imgLen) AS imgLenDiv';

        $s1="SELECT cc_model.model_id,
		cc_model.sname, 
		cc_model.name, 
		cc_model.alt, 
		cc_model.suffix, 
		cc_model.img1, 
		cc_model.img2,
		cc_model.img3, 
		cc_model.sup_id, 
		cc_model.mspez_id, 
		cc_model.class_id, 
		cc_model.P1, 
		cc_model.P2, 
		cc_model.P3, 
		IF(cc_model.pos <= 0, (9999999 + (cc_model.pos * -1)), cc_model.pos) as m_pos,
		cc_model.pos,
		cc_model.sticker_id,
		cc_model.video_link,
		cc_brand.name AS bname,
		cc_brand.alt AS balt, 
		cc_brand.replica, 
		cc_brand.sname AS brand_sname,
		cc_brand.sup_id AS bsup_id,
		cc_brand.H AS BH,
		cc_brand.brand_id
		{$select}{$af_brand}{$af_model} FROM $ww {$having}";

        // TODO подзапросы в SELECT с условиями в HAVING возврщают ошибку если делать COUNT
        $s2="SELECT {$countSelect} FROM $ww {$having}";

        $start=abs(intval(@$r['start']));
        $lines=abs(intval(@$r['lines']));
        if(empty($order))	$s0=$s1." ORDER BY cc_brand.name, cc_model.name";
        else $s0=$s1." ORDER BY ".$order;

        if(!@$r['seekMode']) $s00=$s0.($lines?" LIMIT $start, $lines":''); else $s00=$s0;

        if(@$r['sqlReturn']) {
            $this->rt=$t1-Tools::getMicroTime();
            if(@$r['count']) return $s2;
            elseif(@$r['nolimits']) return $s0;
            else return $s00;
        }

        if(!@$r['seekMode'] && !@$r['nolimits'] && !@$r['ex'] || @$r['count']){
            $this->getOne($s2);
            $n=$this->qrow[0];
            //        echo $n;
            //       echo "-->";
            //        echo $this->sql_query;
            //        echo '<br>';
        }else $n=true;

        $this->rt=$t1-Tools::getMicroTime();

        if(!empty($r['count'])) return $n;


        if($n)
            if(@$r['seekMode'] || @$r['nolimits'] || @$r['ex']) {
                $this->query($s0);
                $n=$this->qnum();
            }else $this->query($s00);

        if($n && @$r['ex']){
            if(empty($r['exFields']))
                $this->ex_arr=array('sbrand'=>array(),'brand'=>array(),'P1'=>array(),'P2'=>array(),'P3'=>array(),'sup_id'=>array());
            else {
                $this->ex_arr=array();
                foreach($r['exFields'] as $k=>$v)
                    $this->ex_arr[$k]=array();
            }

            while($this->next(MYSQL_ASSOC)!==false){
                if(isset($this->ex_arr['sbrand']) and $this->qrow['bsup_id'])
                    $this->ex_arr['sbrand'][$this->qrow['brand_id']]=array(
                        'replica'=>(int)$this->qrow['replica']?1:0,
                        'sup_id'=>$this->qrow['bsup_id'],
                        'name'=>Tools::unesc($this->qrow['bname']),
                        'alt'=>Tools::unesc($this->qrow['balt']),
                        'H'=>$this->qrow['BH'],
                        'sname'=>$this->qrow['brand_sname'],
                        'amount'=>@$this->ex_arr['brand'][(int)$this->qrow['replica']?'replica':0][$this->qrow['brand_id']]['amount']+1
                    );
                elseif(isset($this->ex_arr['brand']))
                    $this->ex_arr['brand'][(int)$this->qrow['replica']?'replica':0][$this->qrow['brand_id']]=array(
                        'name'=>Tools::unesc($this->qrow['bname']),
                        'alt'=>Tools::unesc($this->qrow['balt']),
                        'H'=>$this->qrow['BH'],
                        'sname'=>$this->qrow['brand_sname'],
                        'amount'=>@$this->ex_arr['brand'][(int)$this->qrow['replica']?'replica':0][$this->qrow['brand_id']]['amount']+1
                    );

                for($i=1;$i<=3;$i++)
                    if(isset($this->ex_arr['P'.$i]))
                        if(isset($this->ex_arr['P'.$i][$this->qrow['P'.$i]]))
                            $this->ex_arr['P'.$i][$this->qrow['P'.$i]]++;
                        else $this->ex_arr['P'.$i][$this->qrow['P'.$i]]=1;

                if(isset($this->ex_arr['sup_id']))
                    if(isset($this->ex_arr['sup_id'][$this->qrow['sup_id']]))
                        $this->ex_arr['sup_id'][$this->qrow['sup_id']]++;
                    else $this->ex_arr['sup_id'][$this->qrow['sup_id']]=1;
            }
        }

        if(@$r['seekMode'] && $n) $this->seek((int)@$r['start']);

        if(!@$r['seekMode'] && @$r['ex'] && !@$r['nolimits'] && $n) $this->query($s00);

        return $n;
    }

    function cat($r)
    {
        return $this->cat_view($r);
    }

    function cat_view($r) {
        /*
            ПОСТУПАЕМЫЕ ДАННЫЕ ДОЛЖНЫ БЫТЬ УЖЕ ЭКРАНИРОВАНЫ С ПОМОЩЬЮ Tools::like_() но не экранированы через addslashes()

            Параметры (параметры надо экранировать (если надо) до вызова функции):
                gr - единственный обязательный параметр = {1,2,all}
                start,lines,
                H/notH,
                byprice,
                exFields
                ex,
                apMode  array(markId=>int)   режим подбора по марке авто: реплика только в связке с avtoId
                exSelect -  0|1, - в SELECT включаться только поля необходимые для расчета массива ex
                rf, - 0|1 - искать только шины ранфлет, если ==0 то искобчить шины ранфлет
                sea_name, - упразднили
                add_query/where,  string || array
                apb,
                order,   string || array
                sqlReturn,
                nolimits,
                select,    string    - добавление строки к полям выбора полей в запросе, т.е. после SELECT.
                fields		string|array(поле=>алиас)   - замена полей в SELECT этим списком
                countSelect string   - отдельнор задаются дополнительные поля для SELECT count(),...
                scDiv (0|1) - добавить к селекту (cc_cat.sc DIV cc_cat.sc) AS scDiv    (к SELECT Count()  не добалвяются)
                imgLenDiv  (0|1) добавить к селекту ((@len:=LENGTH (cc_model.img1)) DIV @len) AS imgLenDiv    (к SELECT Count()  не добалвяются)
                cpriceDiv (0|1) - добавить к селекту (cc_cat.cprice DIV cc_cat.cprice) AS scDiv    (к SELECT Count()  не добалвяются)
                imgLenDiv  (0|1) добавить к селекту ((@len:=LENGTH (cc_model.img1)) DIV @len) AS imgLenDiv    (к SELECT Count()  не добалвяются)
                having - array() || строка  - условие HAVING
                groupby - string - GROUP BY
                dataset_id,
                datasetTo,
                count   0|1  - SELECT count(*),
                seekMode (0|1),
                mTags - array(id1,id2,...) - список тегов для моделей
            *** конец параметрам

            С указанием таблиц cc_model., cc_brand. можно передавать любые их поля

            Без указания модельной таблицы (cc_model.) можно передавать поля таблицы:
                Для указания значений параметра cc_model.P{1,2,3...} надо передавать M{1,2,3...} - на выходе MP{1,2,3...},
                brand_id,
                sup_id,
                model_id
                replica

            Элементы $r не распознанные как параметры и как явно указанные поля таблиц, дополняются cc_cat.

            Для каждого параметра возможны диапазоны  и перечисления array('from'=>..,'to'=>..., 'list'=>..;.., 'like'=>...;..

            datasetTo: string of {cat,model,brand}
            apb и dataset - взаимоисключающие режимы

            в режиме seekMode указатель перемещается на start, lines не используется в этой функции (лимит надо контролировать в скрипте). также кол-во строк ($n) бедет соответсвовать запросу без LIMIT start,lines

              catOrGroups => array() - 	массив масивов, по которым будет ограничен поиск для таблицы cc_cat. Например
                  catOrGroups=>array(
                      array(
                          'P3>15 AND P3<21',
                          'P1'=>16,
                          'P2'=>65
                      ),
                      array(
                          'P3'=>17
                      )
                  )
                  добавит в WHERE запроса условие ... AND (   (P3>15 AND P3<21) AND (P1=16) AND (P2=65) OR (P3=17)  ) ...
                  если таблица не указана, будет прибавлено к полю cc_cat.

        */

        $t1=Tools::getMicroTime();
        if(empty($r['gr'])) return false;
        $s=''; // FROM $ww WHERE $s
        if($r['gr']!='all') {
            $r['gr']=(int)$r['gr'];
            $s.="AND (cc_cat.gr={$r['gr']})";
        }
        foreach($r as $k=>$v) if($v!==''){
            $k=trim($k);
            if($k=='mTags'){
                if(!is_array($v)) $v=array((int)$v);
                $sq=array();
                if(!empty($v)) foreach($v as $xv){
                    $xv=(int)$xv;
                    $sq[]="(cc_model.tags LIKE '.$xv.%' OR cc_model.tags LIKE '.%.$xv.%')";
                }
                if(!empty($sq)) $s.= ' AND ('.implode(' OR ',$sq).') ';
            }else{
                if(!in_array($k,array('gr','start','lines','H','notH','byprice','ex','rf', 'c_index','add_query','where','apb','order','sqlReturn','nolimits','select','dataset_id','datasetTo','count','seekMode','exFields','catOrGroups','exSelect','scDiv','cpriceDiv','imgLenDiv','countSelect','fields','groupby','apMode')))
                {
                    if($k=='brand_id' || $k=='sup_id' || $k=='model_id' ||  mb_substr($k,0,1)=='M' && mb_substr($k,1,1)>0) $k='cc_model.'.str_replace('M','P',$k);
                    elseif($k=='replica') $k='cc_brand.replica';
                    elseif(mb_strpos($k,'cc_')===false) $k='cc_cat.'.$k;

                    $k=Tools::esc($k);

                    if(is_array($v)){
                        if(isset($v['from'])) {
                            $v['from']=Tools::esc($v['from']);
                            if($v['from']!=='') $s=$s." AND ($k >= '{$v['from']}'";    
                            // Заплатка для добавления 0-го значения DIA
                            if(isset($v['ext_or_eq'])) {
                                $v['ext_or_eq']=Tools::esc($v['ext_or_eq']);
                                if($v['ext_or_eq']!=='') $s=$s." OR $k = '{$v['ext_or_eq']}'";
                            }     
                            // ***           
                            $s = $s.")";
                        }
                        if(isset($v['to'])) {
                            $v['to']=Tools::esc($v['to']);
                            if($v['to']!=='') $s=$s." AND ($k <= '{$v['to']}')";
                        }
                        if(isset($v['list'])) {
                            if(is_array($v['list'])) $x=$v['list']; else $x=explode(';',$v['list']);
                            $aa=array();
                            foreach($x as $xv){
                                if(is_numeric($xv)) {
                                    $xv=(float)$xv;
                                    $aa[]="$k = '$xv'";
                                }else{
                                    $xv=Tools::esc($xv);
                                    $aa[]="$k LIKE '$xv'";
                                }
                            }
                            if(count($aa)) {
                                $s.=" AND (".implode(' OR ',$aa).")";
                            }
                        }
                        if(isset($v['like'])) {
                            if(is_array($v['like'])) $x=$v['like']; else $x=explode(';',$v['like']);

                            $aa=array();
                            foreach($x as $xv)
                                if($xv!=''){
                                    $xv=Tools::esc($xv);
                                    $aa[]="$k LIKE '%$xv%'";
                                }
                            if(count($aa)) {
                                $s.=" AND (".implode(' OR ',$aa).")";
                            }
                        }
                    }else {
                        if(is_numeric($v)) {
                            $v=(float)$v;
                            $s.="AND ($k = '$v')";
                        }
                        elseif($v!=''){
                            $v=Tools::esc($v);
                            $s.="AND ($k LIKE '$v')";
                        }
                    }
                }
            }
        }
        $catOrGroups=array();
        if(!empty($r['catOrGroups'])){
            foreach($r['catOrGroups'] as $gv){
                if(is_array($gv)){
                    $row=array();
                    foreach($gv as $k=>$v){
                        if(is_string($k)){
                            if(strpos($k,'cc_')===false) $k="cc_cat.$k";
                            $k=Tools::esc($k);
                            if(is_numeric($v)) $row[]="$k = '$v'";
                            elseif($v!=='') $row[]="$k LIKE '$v'";
                        }else{
                            $row[]="($v)";
                        }

                    }
                    if(!empty($row)) $catOrGroups[]=implode(' AND ',$row);
                }
            }
        }

        if(!empty($r['apMode']['markId'])) $s.=" AND (cc_brand.avto_id='".(int)$r['apMode']['markId']."' AND cc_brand.replica=1  OR cc_brand.avto_id=0)";

        $catOrGroups=implode(' OR ',$catOrGroups);
        if(!empty($catOrGroups)){
            $s.=" AND ( $catOrGroups )";
        }

        if(!isset($r['H']) && !isset($r['notH']) || !empty($r['notH']) || !empty($r['H'])) $r['notH']=true; else $r['notH']=false;

        if (@$r['avto_mark']!='') {
            $avto_mark=$r['avto_mark'];
            $s.="AND (cc_brand.replica AND cc_brand.name LIKE '%{$avto_mark}%' OR NOT cc_brand.replica)";
        }
        if(isset($r['rf']) && $r['gr']==1){
            $rfs=trim(Data::get('сс_runflat_suffix'));
            if($rfs=='') $rfs=array('RunFlat','Run Flat'); else $rfs=preg_split("/[;,]/",$rfs);
            foreach($rfs as $k1=>$v1) $rfs[$k1]=trim($v1);
            $rfs=Tools::usortStr($rfs,'DESC');
            if(count($rfs)){
                if($r['rf']){
                    foreach($rfs as $k1=>$v1) {
                        $rfs[$k1]="cc_cat.suffix LIKE '$v1' OR cc_cat.suffix LIKE '% $v1' OR cc_cat.suffix LIKE '$v1 %' OR cc_cat.suffix LIKE '% $v1 %'";
                    }
                    $s.=" AND (".implode(' OR ',$rfs).")";
                }else{
                    foreach($rfs as $k1=>$v1) {
                        $rfs[$k1]="cc_cat.suffix NOT LIKE '$v1' AND cc_cat.suffix NOT LIKE '% $v1' AND cc_cat.suffix NOT LIKE '$v1 %' AND cc_cat.suffix NOT LIKE '% $v1 %'";
                    }
                    $s.=" AND ".implode(' AND ',$rfs);
                }

            }
        }
        
        // ************************ new field: c_index *************************
        if((isset($r['c_index']) && $r['gr']==1) && !(isset($r['rf']) && $r['rf']) ){            
                $s .= " AND (cc_cat.suffix LIKE 'C' or cc_cat.suffix LIKE 'С') ";   
        }
        // ************************ /new field: c_index *************************
        
        if(@Cfg::get('apb_enabled') && (is_array(@$r['apb']))) $apb_en=true; else $apb_en=false;
        if($apb_en)
            $s.=(@$r['apb']['bid']?"AND(apb_cat.brand_id='{$r['apb']['bid']}')":'')
                .(@$r['apb']['avto_id']?"AND(apb_cat.avto_id='{$r['apb']['avto_id']}')":'')
                .(@$r['apb']['model_id']?"AND(apb_cat.model_id='{$r['apb']['model_id']}')":'')
                .(@$r['apb']['P6']?"AND(apb_cat.P6 = '{$r['apb']['P6']}')":'')
                .(@$r['apb']['P1']?"AND(apb_cat.P1 = '{$r['apb']['P1']}')":'')
                .(@$r['apb']['P2']?"AND(apb_cat.P2 = '{$r['apb']['P2']}')":'')
                .(@$r['apb']['P3']?"AND(apb_cat.P3 = '{$r['apb']['P3']}')":'')
                .(@$r['apb']['P4']?"AND(apb_cat.P4 = '{$r['apb']['P4']}')":'')
                .(@$r['apb']['P5']?"AND(apb_cat.P5 = '{$r['apb']['P5']}')":'');
        elseif(@$r['datasetTo']!='' && @$r['dataset_id']>0){
            switch($r['datasetTo']){
                case 'cat': $dsCat=true; break;
                case 'model': $dsModel=true; break;
                case 'brand': break; // пока не нужно
            }
        }
        $af_brand=App_TFields::Dbselect('cc_brand',$r['gr']);
        $af_cat=App_TFields::DBselect('cc_cat',$r['gr']);
        $af_model=App_TFields::DBselect('cc_model',$r['gr']);

        if(empty($r['fields'])) {
            $fields=
            "cc_cat.gr,cc_cat.dt_added,
            cc_brand.brand_id,
            cc_brand.H AS BH,
            cc_brand.name AS bname,
            cc_brand.sname AS brand_sname,
            cc_brand.replica,
            cc_brand.pos,
            cc_brand.img1 AS brand_img1,
            cc_brand.img2 AS brand_img2,
            cc_brand.sup_id AS bsup_id,
            cc_model.name AS mname,
            cc_model.alt AS malt,
            cc_brand.alt AS balt,
            cc_model.model_id,
            cc_model.sname AS model_sname,
            cc_model.P1 AS MP1,
            cc_model.P2 AS MP2,
            cc_model.P3 AS MP3,
            cc_model.class_id,
            cc_model.mspez_id,
            cc_model.suffix AS msuffix,
            cc_model.sticker_id,
            cc_cat.suffix AS csuffix,
            cc_model.sup_id,
            cc_model.img1,
            cc_model.img2,
            cc_model.img3,
            IF(cc_model.pos <= 0, (9999999 + (cc_model.pos * -1)), cc_model.pos) as m_pos,
            cc_model.pos,
            cc_model.video_link,
            cc_cat.cat_id,
            cc_cat.sc,
            cc_cat.scprice,
            cc_cat.cprice,
            cc_cat.P1+'0' AS P1,
            cc_cat.P2+'0' AS P2,
            cc_cat.P3+'0' AS P3,
            cc_cat.P4,
            cc_cat.P5+'0' AS P5,
            cc_cat.P6+'0' AS P6,
            cc_cat.P7,
            cc_cat.app,
            cc_cat.sname AS cat_sname{$af_brand}{$af_model}{$af_cat}";
            if(Cfg::get('ccTags')) $fields.=', cc_model.tags AS mTags';
        }
        elseif(is_array($r['fields'])){
            $a=array();
            foreach($r['fields'] as $k=>$v) $a[]="$k AS $v";
            $fields=implode(', ',$a);
        }else $fields=$r['fields'];

        $r['select']=@$r['select'];

        if(!empty($r['scDiv'])) $r['select'].=($r['select']!=''?',':'').'IFNULL((cc_cat.sc DIV cc_cat.sc), 0) AS scDiv';
        if(!empty($r['imgLenDiv'])) $r['select'].=($r['select']!=''?',':'').'((@len:=LENGTH (cc_model.img1)) DIV @len) AS imgLenDiv';
        if(!empty($r['cpriceDiv'])) $r['select'].=($r['select']!=''?',':'').'IFNULL((cprice DIV cprice), 0) AS cpriceDiv';

        if(!empty($r['select'])) $fields.=!empty($r['select'])?", {$r['select']}":'';


        if (empty($r['where'])) $r['where']=@$r['add_query'];

        if(!empty($r['where']))// WHERE основного запроса
        if(is_array($r['where'])) $s.=' AND '.implode(' AND ',$r['where']);
        else $s.=" AND ({$r['where']})";


        $ww=''; // FROM $ww
        if(!@$dsModel && !@$dsCat){
            $ww=($apb_en?'apb_cat,':'').
                "cc_cat ".
                "INNER JOIN (".
                "cc_model ".
                "JOIN cc_brand ON cc_model.brand_id = cc_brand.brand_id".
                ") ON cc_cat.model_id = cc_model.model_id ".
                "WHERE NOT cc_cat.LD AND NOT cc_model.LD AND NOT cc_brand.LD ".
                ($r['notH']?" AND NOT cc_cat.H AND NOT cc_brand.H AND NOT cc_model.H":'').
                ' '.$s.
                ($apb_en?" AND cc_cat.P1 = apb_cat.P1 AND cc_cat.P2 = apb_cat.P2 AND cc_cat.P4 = apb_cat.P4 AND cc_cat.P5 = apb_cat.P5 AND cc_cat.P6 = apb_cat.P6 AND cc_model.name LIKE apb_cat.name GROUP BY cc_cat.cat_id ":'');
        }elseif(@$dsModel){
            $ww="cc_dataset_model ".
                "JOIN  (".
                "cc_cat ".
                "JOIN (".
                "cc_model JOIN cc_brand ON cc_model.brand_id = cc_brand.brand_id".
                ") ON cc_cat.model_id = cc_model.model_id ".
                ") ON cc_dataset_model.model_id = cc_cat.model_id ".
                "WHERE NOT cc_cat.LD AND NOT cc_model.LD AND NOT cc_brand.LD ".
                ($r['notH']?" AND NOT cc_cat.H AND NOT cc_brand.H AND NOT cc_model.H":'').
                ' '.$s." AND cc_dataset_cat.dataset_id='{$r['dataset_id']}'";
        }elseif(@$dsCat){
            $ww="cc_dataset_cat ".
                "JOIN  (".
                "cc_cat ".
                "JOIN (".
                "cc_model JOIN cc_brand ON cc_model.brand_id = cc_brand.brand_id".
                ") ON cc_cat.model_id = cc_model.model_id ".
                ") ON cc_dataset_cat.cat_id = cc_cat.cat_id ".
                "WHERE NOT cc_cat.LD AND NOT cc_model.LD AND NOT cc_brand.LD ".
                ($r['notH']?" AND NOT cc_cat.H AND NOT cc_brand.H AND NOT cc_model.H":'').
                ' '.$s." AND cc_dataset_cat.dataset_id='{$r['dataset_id']}'";
        }


        if(!empty($r['having']))
            if(is_array($r['having'])) $having=implode(' AND ',$r['having']);
            else $having=$r['having'];

        if(!empty($having)) $having=" HAVING $having"; else $having='';

        if(empty($r['countSelect'])) $r['countSelect']=''; else $r['countSelect']=','.$r['countSelect'];

        $groupby=!empty($r['groupby'])?"GROUP BY {$r['groupby']}":'';

        $s1="SELECT $fields ".
            "FROM $ww $having $groupby";

        $s2="SELECT Count(".($apb_en?'DISTINCT ':'')."cc_cat.cat_id) AS nc {$r['countSelect']} FROM $ww $having $groupby";

//        print_r($r);
//        echo "<br><br>S1=$s1<br><br>S2=$s2<br><br>";

        $this->rt=$t1-Tools::getMicroTime();

        if(!empty($r['count'])) {
            if(@$r['sqlReturn']) return $s2;
            if(!$apb_en){
                $this->getOne($s2);
                $n=$this->qrow['nc'];
            }else{
                $this->query($s2);
                $n=0;
                while($this->next()!==false) $n+=$this->qrow['nc'];
            }
            $this->rt=$t1-Tools::getMicroTime();
            return $n;
        }

        if(!empty($r['order']))// ORDER BY
        if(is_array($r['order'])) $order=implode(', ',$r['order']);
        else $order=$r['order'];

        if(!isset($r['order'])){
            if ($r['gr']==1) $s1.=" ORDER BY ".(@$r['byprice']==0?"cc_brand.name,cc_model.name, cc_cat.P1, cc_cat.P3, cc_cat.P2":"cprice ASC, cc_brand.name, cc_model.name, cc_cat.P1, cc_cat.P3, cc_cat.P2");
            else $s1.=" ORDER BY ".(@$r['byprice']==0?"cc_brand.replica DESC, cc_brand.name,cc_model.name, cc_cat.P5, cc_cat.P1, cc_cat.P2":"cprice ASC, cc_brand.name, 	cc_model.name, cc_cat.P1, cc_cat.P3, cc_cat.P2");
        }elseif(!empty($order)) $s1.=" ORDER BY $order";

        if(isset($r['exFields']) && empty($r['exFields'])) $r['ex']=0;

        if(@$r['ex']){
            if(empty($r['exFields']))
                $this->ex_arr=array('sbrand'=>array(),'brand'=>array(),'P1'=>array(),'P2'=>array(),'P3'=>array(),'P4'=>array(),'P5'=>array(),'P6'=>array(),'P7'=>array(),'MP1'=>array(),'MP2'=>array(),'MP3'=>array(),'sup_id'=>array());
            else {
                $this->ex_arr=array();
                foreach($r['exFields'] as $k=>$v)
                    if(is_array($v)) $this->ex_arr[$k]=array(); else $this->ex_arr[$v]=array();
            }
        }

        $this->models_ids=array();

        $this->rt=$t1-Tools::getMicroTime();

        if(@$r['sqlReturn']) return $s1;

        if(@$r['seekMode']){
            $this->query($s1);
            $n=$this->qnum();
        }else{
            if(@$r['nolimits']){
                $this->query($s1);
                $n=$this->qnum();
            } else {
                $this->query($s2);
                if(!$apb_en){
                    $this->next();
                    $n=$this->qrow['nc'];
                }else{
                    $n=0;
                    while($this->next()!==false) $n+=$this->qrow['nc'];
                }
                if($n && @$r['ex']) {
                    $this->query($s1);
                }
            }
        }

        if($n && @$r['ex']){

            if(isset($this->ex_arr['P46'])) {
                $LZex=str_replace(' ','',Tools::cutDoubleSpaces(Data::get('cc_LZexclude')));
                $LZex=explode(',',$LZex);
            }

            if((!empty($r['ex_hrom']) or isset($this->ex_arr['ex_hrom'])) && $r['gr']==2 && ($hs=trim(Data::get('cc_hrom_str')))!='') {
                $this->ex_arr['hrom']=array();
                $hs=explode(';',$hs);
            } else $hs=false;

            if((isset($this->ex_arr['runflat'])) && $r['gr']==1) {
                $this->ex_arr['runflat']=array();
                $rf=trim(Data::get('сс_runflat_suffix'));
                if(!empty($rf)) $rf=preg_split("/[;,]/",$rf); else $rf=array('RunFlat','Run Flat');
            } else $rf=false;

            if(isset($this->ex_arr['minMaxPrice'])){
                $this->ex_arr['minMaxPrice']['min']=$this->ex_arr['minMaxPrice']['max']=0;
            }

            if($n) while($this->next(MYSQL_ASSOC)!==false){

                if(isset($this->models_ids[$this->qrow['model_id']])) $this->models_ids[$this->qrow['model_id']]++; else $this->models_ids[$this->qrow['model_id']]=1;

                if(isset($this->ex_arr['sbrand']) and $this->qrow['bsup_id'])
                    $this->ex_arr['sbrand'][$this->qrow['brand_id']]=array(
                        'replica'=>(int)$this->qrow['replica']?1:0,
                        'sup_id'=>$this->qrow['bsup_id'],
                        'name'=>stripslashes($this->qrow['bname']),
                        'alt'=>stripslashes($this->qrow['balt']),
                        'gr'=>$this->qrow['gr'],
                        'H'=>$this->qrow['BH'],
                        'sname'=>$this->qrow['brand_sname'],
                        'amount'=>@$this->ex_arr['brand'][(int)$this->qrow['replica']?'replica':0][$this->qrow['brand_id']]['amount']+1
                    );
                elseif(isset($this->ex_arr['brand']))
                    $this->ex_arr['brand'][(int)$this->qrow['replica']?'replica':0][$this->qrow['brand_id']]=array(
                        'name'=>stripslashes($this->qrow['bname']),
                        'alt'=>stripslashes($this->qrow['balt']),
                        'gr'=>$this->qrow['gr'],
                        'H'=>$this->qrow['BH'],
                        'sname'=>$this->qrow['brand_sname'],
                        'amount'=>@$this->ex_arr['brand'][(int)$this->qrow['replica']?'replica':0][$this->qrow['brand_id']]['amount']+1
                    );

                if(isset($this->ex_arr['csuffix'])){
                    $suf=stripslashes($this->qrow['csuffix']);
                    if(isset($this->ex_arr['csuffix'][$suf]))
                        $this->ex_arr['csuffix'][$suf]++;
                    else
                        $this->ex_arr['csuffix'][$suf]=1;
                }

                if(isset($this->ex_arr['P123'])){
                    $pp=$this->qrow['P1'].'-'.$this->qrow['P2'].'-'.$this->qrow['P3'];
                    if(isset($this->ex_arr['P123'][$pp]))
                        $this->ex_arr['P123'][$pp]++;
                    else
                        $this->ex_arr['P123'][$pp]=1;
                }

                if(isset($this->ex_arr['P46']) && !in_array($this->qrow['P4'],$LZex)){
                    $pp=$this->qrow['P4'].'*'.$this->qrow['P6'];
                    if(isset($this->ex_arr['P46'][$pp]))
                        $this->ex_arr['P46'][$pp]++;
                    else
                        $this->ex_arr['P46'][$pp]=1;
                }

                if(isset($this->ex_arr['P123456'])){
                    $pp=$this->qrow['P1'].'-'.$this->qrow['P2'].'-'.$this->qrow['P3'].'-'.$this->qrow['P4'].'-'.$this->qrow['P5'].'-'.$this->qrow['P6'];
                    if(isset($this->ex_arr['P123456'][$pp]))
                        $this->ex_arr['P123456'][$pp]++;
                    else
                        $this->ex_arr['P123456'][$pp]=1;
                }

                if(isset($this->ex_arr['minMaxPrice'])){
                    if(@$this->ex_arr['minMaxPrice']['look_scprice']) $price=$this->qrow['scprice']>0?$this->qrow['scprice']:$this->qrow['cprice']; else $price=$this->qrow['cprice'];
                    if($price>$this->ex_arr['minMaxPrice']['max']) $this->ex_arr['minMaxPrice']['max']=$price;
                    if($price>0)
                        if($this->ex_arr['minMaxPrice']['min']==0) $this->ex_arr['minMaxPrice']['min']=$price;
                        elseif($price<$this->ex_arr['minMaxPrice']['min']) $this->ex_arr['minMaxPrice']['min']=$price;
                }

                for($i=1;$i<=7;$i++)
                    if(isset($this->ex_arr['P'.$i]))
                        if(isset($this->ex_arr['P'.$i][$this->qrow['P'.$i]]))
                            $this->ex_arr['P'.$i][$this->qrow['P'.$i]]++;
                        else
                            $this->ex_arr['P'.$i][$this->qrow['P'.$i]]=1;

                for($i=1;$i<=3;$i++)
                    if(isset($this->ex_arr['MP'.$i]))
                        if(isset($this->ex_arr['MP'.$i][$this->qrow['MP'.$i]]))
                            $this->ex_arr['MP'.$i][$this->qrow['MP'.$i]]++;
                        else
                            $this->ex_arr['MP'.$i][$this->qrow['MP'.$i]]=1;

                if(isset($this->ex_arr['sup_id']))
                    if(isset($this->ex_arr['sup_id'][$this->qrow['sup_id']]))
                        $this->ex_arr['sup_id'][$this->qrow['sup_id']]++;
                    else
                        $this->ex_arr['sup_id'][$this->qrow['sup_id']]=1;

                if($hs!==false){
                    foreach($hs as $v1){
                        if(mb_stripos($this->qrow['mname'],$v1)!==false || mb_stripos($this->qrow['msuffix'],$v1)!==false || mb_stripos($this->qrow['csuffix'],$v1)!==false)
                            $this->ex_arr['hrom'][$this->qrow['brand_id']][(int)$this->qrow['replica']?'replica':0]=array(
                                'bname'=>stripslashes($this->qrow['bname']),
                                'amount'=>@$this->ex_arr['hrom'][$this->qrow['brand_id']][(int)$this->qrow['replica']?'replica':0]['amount']++
                            );
                    }
                }

                if($rf!==false){
                    foreach($rf as $v1){
                        if(preg_match("/(\s|^){$v1}(\s|$)/iu",$this->qrow['csuffix']))
                            if(isset($this->ex_arr['runflat'][$v1]))
                                $this->ex_arr['runflat'][$v1]++;
                            else
                                $this->ex_arr['runflat'][$v1]=1;
                    }
                }

                if(isset($this->ex_arr['mTags']) && $this->qrow['mTags']!='') {
                    $tags=explode('.',trim($this->qrow['mTags'],'.'));
                    for ($i=0; $i<count($tags); $i++)
                        if($tags[$i]!='')
                            if(isset($this->ex_arr['mTags'][$tags[$i]]))
                                $this->ex_arr['mTags'][$tags[$i]]++;
                            else
                                $this->ex_arr['mTags'][$tags[$i]]=1;
                }


            }
        }

        $start=abs(intval(@$r['start']));
        $lines=abs(intval(@$r['lines']));

        if(@$r['nolimits'] || @$r['seekMode'] && $start==0){
            if($this->qnum()) $this->first();
            $this->rt=$t1-Tools::getMicroTime();
            return $n;
        }elseif(@$r['seekMode']) $this->seek($start);

        if(!@$r['seekMode'] && $n){
            $s1.=($lines?" LIMIT $start, $lines":'');
            $this->query($s1);
        }
        $this->rt=$t1-Tools::getMicroTime();
        return($n);
    }


    function paginator($baseUrl, $urlParam, $page, $num, $limit, $pageVar='page', $tpl=array('active'=>'','noActive'=>'','dots'=>''), $itemNum=15)
    {
        // Первая страница передается с номером 0, вторая  с номер два  и т.д
        // page - выбранная страница
        // limit - записей на странице
        // num - всего записей на всех страницах
        // itemNum - кол-во непрерывной нумерации
        $page=abs($page);
        $num=abs($num);
        $pages=ceil($num/$limit);
        if($pages<=1) return array();
        $q=Tools::arrayKeyDiff($urlParam,$pageVar);
        $r=array();
        $a=intval($itemNum/2);
        $from=$page-$a;
        if ($from<1) {
            $d=abs($from);
            $to=$page+$a+$d;
            $from=1;
            if($to>$pages) $to=$pages;
        } else {
            $from=$from+1;
            $to=$page+$a-1;
            if($to>$pages) {
                $d=abs($pages-$to);
                $to=$pages;
                $from=$from-$d;
                if($from<1) $from=1;
            }
        }
        if ($from>1) {
            $r[]=str_replace('{url}',$baseUrl.Tools::imp($q),str_replace('{page}',1,$tpl['noActive']));
            if($from>2) $r[]=$tpl['dots'];
        }
        for($i=$from;$i<=$to;$i++){
            $r[]=str_replace('{url}',$baseUrl.Tools::imp($i==1?$q:array_merge($q,array("$pageVar"=>$i))),str_replace('{page}',$i,$i==$page || ($i==1 && $page==0)?$tpl['active']:$tpl['noActive']));
        }
        if ($to<$pages) {
            if($pages-$to>1) $r[]=$tpl['dots'];
            $r[]=str_replace('{url}',$baseUrl.Tools::imp(array_merge($q,array("$pageVar"=>$pages))),str_replace('{page}',$pages,$tpl['noActive']));
        }

        return $r;
    }


    function load_class($gr,$check=false)
    {
        if($check && count($this->class_arr)) return;
        $cc=new CC_Ctrl;
        $this->class_arr=array();
        $cc->que('class_list',$gr);
        while($cc->next()!==false)
            $this->class_arr[$cc->qrow['class_id']]=Tools::unesc($cc->qrow['name']);
        unset($cc);
    }

    function load_mspez($gr)
    {
        $cc=new CC_Ctrl;
        $this->mzpez_arr=array();
        $cc->query("SELECT * FROM cc_mspez WHERE gr='$gr' ORDER BY name");
        while($cc->next()!==false)
            $this->mspez_arr[$cc->qrow['mspez_id']]=Tools::unesc($cc->qrow['name']);
        unset($cc);
    }

    function http_upath($file,$serv=false, $replace_protocol = true)
    {
        if(defined('FROM_CMS'))
            if($serv) $CC_UPLOAD_DIR=Cfg::_get('root_path').'/'.Cfg::get('cc_upload_dir'); else $CC_UPLOAD_DIR=Cfg::get('cc_upload_dir');
        else
            if($serv) {
                $d=Cfg::_get('root_path').'/'.Cfg::get('cc_cache_images_dir');
                $CC_UPLOAD_DIR=is_file($d)?$d:Cfg::_get('root_path').'/'.Cfg::get('cc_upload_dir');
            } else $CC_UPLOAD_DIR=Cfg::get('cc_images_dir');

        if(mb_strpos($CC_UPLOAD_DIR,'http')===false)
            if($serv) return $CC_UPLOAD_DIR.'/'.$file;
            else
            {
                if ($replace_protocol) {
                    return '//'.$_SERVER['HTTP_HOST'].'/'.$CC_UPLOAD_DIR.'/'.$file;
                }
                else {
                    return 'http://'.$_SERVER['HTTP_HOST'].'/'.$CC_UPLOAD_DIR.'/'.$file;
                }
            }
        else return $CC_UPLOAD_DIR.'/'.$file;
    }

    function get_img_path($table,$key,$id,$field,$serv=false)
    {
        $c=new CC_Ctrl;
        $c->query("SELECT $field FROM $table WHERE $key='$id'");
        if ($c->qnum()){
            $c->next();
            if ($c->qrow[$field]!='') $this->img_path=$this->http_upath($c->qrow[$field],$serv); else $this->img_path='';
        } else $this->img_path='';
        unset($c);
        return($this->img_path);
    }

    function makeImgPath($img,$serv=false){
        return $this->make_img_path($img,$serv);
    }

    function make_img_path($img, $serv=false, $replace_protocol=true)
    {
        if(is_numeric($img)) $img=@$this->qrow['img'.$img];
        if (trim($img)!='') $this->img_path=$this->http_upath($img, $serv, $replace_protocol);
        else $this->img_path='';
        return($this->img_path);
    }

    function load_filter($file)
    {
        if(is_array(@$this->s_arr[$file])) return;
        $this->s_arr[$file]=array();
        $fn=Cfg::_get('root_path').'/'.Cfg::get('res_dir').'/'.$file;
        $f=@fopen($fn,'r');
        if($f===false) return false;
        $s=unserialize(fread($f,filesize($fn)));
        fclose($f);
        $this->s_arr[$file]=$s;
    }

    /*
     * TODO убарть функцию в TextParser
     */
    function parse_text($t,$len=0)
    {
        $t=trim($t);
        if(mb_strpos($t,'</')!==false) $t=Tools::unesc($t); else $t=nl2br(Tools::unesc($t));
        if(mb_strpos($t,'<notrim>')!==false) {
            $t=str_replace('<notrim>','',$t);
            return $t;
        }
        $s=$t;
        if($len && $s!=''){
            $t=preg_split("[.,;]",Tools::unesc(Tools::stripTags($t)));
            $s='';
            foreach($t as $k=>$v) if(mb_strlen($s)<$len) $s.=$v.'. ';
        }
        return $s;
    }

    function dict_load($gr)
    {
        if(!empty($this->dictArr)) return;
        $this->dictArr=array();
        $cc=new DB;
        $cc->query("SELECT dict_id,name,brand_id,gr FROM cc_dict WHERE gr='$gr' ORDER BY brand_id");
        while($cc->next()!==false) $this->dictArr[$cc->qrow['gr']][$cc->qrow['brand_id']][stripslashes($cc->qrow['name'])]=$cc->qrow['dict_id'];
        if(is_array(@$this->dictArr[$gr]))
            foreach($this->dictArr[$gr] as $k=>&$v) $v=Tools::uksortStr($v,'DESC');
        unset($cc);
    }

    function dict_search_key($s,$gr,$brand_id=0)
    {
        $s0=' '.trim($s).' ';
        $this->dict_load($gr);
        $r=array();
        if($brand_id && isset($this->dictArr[$gr][$brand_id]))
            foreach($this->dictArr[$gr][$brand_id] as $k=>$v) {
                $sk=mb_stripos($s0,' '.($k).' ');
                if($sk!==false) {
                    $r[$k]=$v;
                    $s0=Tools::_substr_replace($s0," ",$sk,mb_strlen($k)+2);
                }
            }
        if(isset($this->dictArr[$gr]))
            foreach($this->dictArr[$gr][0] as $k=>$v) {
                $sk=mb_stripos($s0,' '.($k).' ');
                if($sk!==false) {
                    $r[$k]=$v;
                    $s0=Tools::_substr_replace($s0," ",$sk,mb_strlen($k)+2);
                }
            }
        $s0=trim($s0);
        if($s0!='' && !in_array($s0,array_keys($r))) $r[$s0]=0; // добавляем отсутвующие ключи как dist_id=0
        krsort($r);
        return $r;
    }

    /*
     * возвращает текстовку словаря по задданным ids
     * если ids:int то вернет string
     * если массив то вернет array(id=>array(name=>text))
     */
    function getDictByIds($dict_ids, $opts=array())
    {
        $db=new CC_Base();
        $res='';
        if(is_scalar($dict_ids)){
            $dict_ids=(int)$dict_ids;
            $d=$db->getOne("SELECT text FROM cc_dict WHERE dict_id=$dict_ids");
            if($d!==0)
                if(!empty($opts['notEmpty']) && trim(Tools::stripTags($d['text']))!='')
                    $res=Tools::unesc($d['text']);
                else
                    $res=Tools::unesc($d['text']);
        }else{
            $res=array();
            if (!empty($dict_ids)) {
                foreach ($dict_ids as &$v) $v = (int)$v;
                $ids = implode(',', $dict_ids);
                if (!empty($ids)) {
                    $d = $db->fetchAll("SELECT name, dict_id, text FROM cc_dict WHERE dict_id IN($ids)");
                    foreach ($d as $v) {
                        if (!empty($opts['notEmpty']) && trim(Tools::stripTags($v['text'])) != '')
                            $res[$v['dict_id']] = array(
                                'name' => $v['name'],
                                'text' => Tools::unesc($v['text'])
                            );
                    }
                }
            }
        }
        unset($db);
        return $res;
    }

    /*
     * определяет есть ли в суффиксах ранфлет и возвращает array(array:найденные суффиксы ранфлет, string:строка без найденных суффиксов) или false если не найдено
     */
    function isRunFlat($suffs)
    {
        if(empty($this->runflatSuffs)) {
            $this->runflatSuffs=array();
            $d = Data::get('сс_runflat_suffix');
            if (!empty($d)) {
                $d = explode(',', $d);
                foreach ($d as $v) {
                    $v = trim(Tools::cutDoubleSpaces($v));
                    if (!empty($v)) $this->runflatSuffs[] = $v;
                }
            }
            $this->runflatSuffs=Tools::usortStr($this->runflatSuffs,'DESC');
        }
        $RF=array();
        $resultSuffs=" $suffs ";
        foreach ($this->runflatSuffs as $v)
            if(false!==($pos=mb_stripos($resultSuffs," $v "))){
                $RF[]=$v;
                $resultSuffs=str_ireplace($v,' ',$resultSuffs);
            }

        if(!empty($RF)) return array($RF,trim(Tools::cutDoubleSpaces($resultSuffs))); else return false;
    }

    function load_filters_coo()
    {
        if(isset($_COOKIE['tsf1']))	{
            $c=$_COOKIE['tsf1'];
            if(preg_match("~[%&]~u",$c)){
                $this->filters_coo[1]=Tools::parseStr(urldecode($_COOKIE['tsf1']));
            }else{
                $this->filters_coo[1]=Tools::parseStr(base64_decode($_COOKIE['tsf1']));
            }
        }
        if(isset($_COOKIE['tsf2']))	{
            $c=$_COOKIE['tsf2'];
            if(preg_match("~[%&]~u",$c)){
                $this->filters_coo[2]=Tools::parseStr(urldecode($_COOKIE['tsf2']));
            }else{
                $this->filters_coo[2]=Tools::parseStr(base64_decode($_COOKIE['tsf2']));
            }
        }

    }

    function discount_price($gr,$price){  // красная цена
        $cc=new CC_Base();
        if(!@Cfg::get('td_discount')) {$this->discount_arr=array('t_discount'=>0,'d_discount'=>0); return $price;}
        if(!$gr) return false;
        if(!isset($this->discount_arr[$gr][($gr==1?'t':'d').'_discount'])) {
            $this->discount_arr[$gr][($gr==1?'t':'d').'_discount']=(float)Data::get(($gr==1?'t':'d').'_discount');
        }
        unset($cc);
        return round($price-$price*$this->discount_arr[$gr][($gr==1?'t':'d').'_discount']/100);
    }

    function isCinSuffix($suffix)
    {
        if(preg_match("~^C\s|\sC\s|\sC$|^C$~u",trim($suffix))) return 1; else return 0;
    }

    function removeCfromSuffix($suffix)
    {
        return trim(Tools::cutDoubleSpaces(preg_replace("~^C\s|\sC\s|\sC$|^C$~u",' ',trim($suffix))));
    }


    function suplrList($r=array())
    {
        $d=$this->fetchAll("SELECT suplr_id, name, dt_added FROM cc_suplr WHERE NOT LD ORDER BY name");
        $rr=array();
        foreach($d as $v){
            $rr[$v['suplr_id']]=array(
                'name'=>Tools::unesc($v['name']),
                'dt_added'=>$v['dt_added']
            );
        }
        unset($d);
        return $rr;
    }

    static function maxImgBW($gr, $imgNum)
    {
        return (int)@Cfg::$config['cc_brand_img'][$gr][$imgNum]['size']['w'];
    }

    static function maxImgBH($gr, $imgNum)
    {
        return (int)@Cfg::$config['cc_brand_img'][$gr][$imgNum]['size']['h'];
    }

    static function maxImgMW($gr, $imgNum)
    {
        return (int)@Cfg::$config['cc_model_img'][$gr][$imgNum]['size']['w'];
    }

    static function maxImgMH($gr, $imgNum)
    {
        return (int)@Cfg::$config['cc_model_img'][$gr][$imgNum]['size']['h'];
    }

    function dopById($id)
    {
        $id=(int)$id;
        $d=$this->getOne("SELECT * FROM cc_dop WHERE dop_id=$id",MYSQL_ASSOC);
        return $d;
    }
}
