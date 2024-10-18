<? //
if (!defined('true_enter')) die ("Direct access not allowed!");

class CC_AB extends DB
{
    var $avto = array();
    var $type_arr = array();
    var $avto_id = 0;
    var $brand_id = 0;
    var $brand_img = '';
    var $tree = array('vendors' => array(), 'models' => array(), 'years' => array(), 'modifs' => array());
    var $fname = ''; // полное название авто
    var $spath = '';
    var $snameArr = array();

    function __construct()
    {
        parent::__construct();
    }

    function ab_connect($cdb_host = '', $cdb_user = '', $cdb_pass = '', $cdb_db = '')
    {
        if ($cdb_db == '') {
            $res = $this->sql_connect();
        } else {
            $this->sql_host = $cdb_host;
            $this->sql_user = $cdb_user;
            $this->sql_pass = $cdb_pass;
            $this->sql_db = $cdb_db;
            $res = $this->sql_connect();
        }
        return $res;
    }


    function que($qname, $cond1 = '', $cond2 = '', $cond3 = '', $cond4 = '')
    {
        switch ($qname) {
            case 'avto_marks':
                if (Cfg::get('avto_bd_ver') == 2) $res = $this->query("SELECT * FROM ab_avto WHERE (vendor_id=0)AND(NOT H) ORDER BY name"); else {
                    $res = $this->query("SELECT * FROM cc_avto WHERE (parent_id=0)AND(LD<>1) ORDER BY name");
                }
                break;
            case 'avto_years':
            case 'avto_year':
                $cond1 = intval($cond1);
                if (Cfg::get('avto_bd_ver') == 2) $res = $this->query("SELECT * FROM ab_avto WHERE (model_id='$cond1')AND(year_id=0)AND(NOT H) ORDER BY till DESC, name");
                break;
            case 'avto_models':
                $cond1 = intval($cond1);
                $cond2 = Tools::esc($cond2);
                if (Cfg::get('avto_bd_ver') == 2) if ($cond2 == '') $res = $this->query("SELECT * FROM ab_avto WHERE  vendor_id='$cond1' AND model_id=0 AND NOT H ORDER BY name"); else $res = $this->query("SELECT * FROM ab_avto WHERE  sname='$cond2' AND model_id=0 AND vendor_id!=0 AND NOT H  ORDER BY name"); else
                    $res = $this->query("SELECT * FROM cc_avto WHERE  parent_id='$cond1' AND LD<>1 ORDER BY name");
                break;
            case 'avto_modifs':
            case 'avto_modif':
                $cond1 = intval($cond1);
                if (Cfg::get('avto_bd_ver') == 2) $res = $this->query("SELECT * FROM ab_avto WHERE  year_id='$cond1' AND NOT H ORDER BY name");
                break;
            case 'avto_by_id':
                $cond1 = intval($cond1);
                if (Cfg::get('avto_bd_ver') == 2) $res = $this->query("SELECT * FROM ab_avto WHERE  avto_id='$cond1' AND NOT H $cond2"); else $res = $this->query("SELECT * FROM cc_avto WHERE  avto_id='$cond1' $cond2");
                $this->next();
                break;
            case 'avto_by_sname':
                $cond1 = Tools::esc($cond1);
                $cond2 = Tools::esc($cond2);
                $res = $this->query("SELECT * FROM ab_avto WHERE (BINARY sname='$cond1')AND(NOT H)$cond2");
                $this->next();
                break;
            case 'avto_sh':
                $cond1 = intval($cond1);
                $cond2 = intval($cond2);
                if (Cfg::get('avto_bd_ver') == 2) $res = $this->query("SELECT avtosh_id, avto_id, P1+'0' AS P1, P2+'0' AS P2, P3+'0' AS P3, P4+'0' AS P4, P5+'0' AS P5, P6+'0' AS P6, avto_type_id, rel_id, _upd FROM ab_avtosh WHERE  avto_type_id='$cond2' AND avto_id='$cond1' AND gr=1  ORDER BY P1,P3,P2"); else
                    $res = $this->query("SELECT avtosh_id, avto_id, P1+'0' AS P1, P2+'0' AS P2, P3+'0' AS P3, P4+'0' AS P4, P5+'0' AS P5, P6+'0' AS P6, avto_type_id, rel_id, _upd FROM cc_avtosh WHERE  avto_id='$cond1' AND gr=1 AND LD<>1  ORDER BY P1,P3,P2");
                break;
            case 'avto_di':
                $cond1 = intval($cond1);
                $cond2 = intval($cond2);
                if (Cfg::get('avto_bd_ver') == 2) $res = $this->query("SELECT avtosh_id, avto_id, P1+'0' AS P1, P2+'0' AS P2, P3+'0' AS P3, P4, P5+'0' AS P5, P6+'0' AS P6, avto_type_id, rel_id, _upd FROM ab_avtosh WHERE avto_type_id='$cond2' AND avto_id='$cond1' AND gr=2  ORDER BY P2,P5,P1"); else
                    $res = $this->query("SELECT avtosh_id, avto_id, P1+'0' AS P1, P2+'0' AS P2, P3+'0' AS P3, P4, P5+'0' AS P5, P6+'0' AS P6, avto_type_id, rel_id, _upd FROM cc_avtosh WHERE avto_id='$cond1' AND gr=2 AND LD<>1 ORDER BY P5,P1,P2");
                break;
            default:
                echo 'BAD CASE ' . $qname;
                $res = false;
        }
        return ($res);
    }

    /*
    * $s=array(svendor:'',smodel:'',syear:'',smodif:'') ИЛИ modifId (int)
    * если byId то array(svendor:vendorId,smodel:modelId,syear:yearId,smodif:modiId)
    */
    function getTree($s, $byId = false)
    { //
        $ab = new CC_AB;
        $this->tree = array('vendors' => array(), 'models' => array(), 'years' => array(), 'modifs' => array());
        $this->fname = '';
        $this->spath = '';
        $this->snameArr = array();
        if (is_array($s)) {
            foreach ($s as $k => $v) if (isset($this->tree[$k]) && $v != '') $this->snameArr[] = $v;
        } else {
            $modif_id = (int)$s;
            $byId = false;
            if ($modif_id > 0) {
                $s = $ab->getOne("SELECT vendor_id,model_id,year_id FROM ab_avto WHERE avto_id='$modif_id' AND year_id>0 AND model_id>0 AND vendor_id>0 AND NOT H");
                if ($s !== 0) {
                    $byId = true;
                    $s = array(
                        'svendor' => $s['vendor_id'],
                        'smodel' => $s['model_id'],
                        'syear' => $s['year_id'],
                        'smodif' => $modif_id
                    );
                }
            }
            if (!$byId) {
                unset($ab);
                return false;
            }
        }
        $ab->query("SELECT * FROM ab_avto WHERE (vendor_id=0)AND(NOT H) ORDER BY name");
        if ($ab->qnum()) {
            while ($ab->next() !== false) {
                if (!$byId && $ab->qrow['sname'] == @$s['svendor'] || $byId && $ab->qrow['avto_id'] == @$s['svendor']) {
                    $this->tree['vendor_id'] = $ab->qrow['avto_id'];
                    $this->tree['vendor_name'] = Tools::unesc($ab->qrow['name']);
                    $this->tree['vendor_sname'] = Tools::unesc($ab->qrow['sname']);
                    $this->tree['vendor_alt'] = Tools::unesc($ab->qrow['alt']);
                    $this->tree['vendor_img1'] = Tools::unesc($ab->qrow['img1']);
                    $this->tree['vendor_img2'] = Tools::unesc($ab->qrow['img2']);
                    $this->tree['vendor_text1'] = Tools::unesc($ab->qrow['text1']);
                    $this->tree['vendor_text2'] = Tools::unesc($ab->qrow['text2']);
                    $this->fname = Tools::unesc($ab->qrow['name']);
                    $this->spath = $ab->qrow['sname'];
                    $this->tree['avto_id'] = $ab->qrow['avto_id'];
                    $this->tree['ext_avto_info']['avto_image'] = null;
                    $this->tree['ext_avto_info']['show_rating'] = $ab->qrow['show_rating'];
                }
                $this->tree['vendors'][$ab->qrow['avto_id']] = array(
                    'sname' => $ab->qrow['sname'],
                    'name' => Tools::unesc($ab->qrow['name']),
                    'alt' => Tools::unesc($ab->qrow['alt']),
                    'img1' => Tools::unesc($ab->qrow['img1']),
                    'img2' => Tools::unesc($ab->qrow['img2'])
                );
            }
            if (@$this->tree['vendor_id']) {
                $ab->query("SELECT * FROM ab_avto WHERE (vendor_id='{$this->tree['vendor_id']}')AND(model_id=0)AND(NOT H) ORDER BY name");
                if ($ab->qnum()) {
                    while ($ab->next() !== false) {
                        if (!$byId && $ab->qrow['sname'] == @$s['smodel'] || $byId && $ab->qrow['avto_id'] == @$s['smodel']) {
                            $this->tree['model_id'] = $ab->qrow['avto_id'];
                            $this->tree['model_name'] = Tools::unesc($ab->qrow['name']);
                            $this->tree['model_sname'] = Tools::unesc($ab->qrow['sname']);
                            $this->tree['model_alt'] = Tools::unesc($ab->qrow['alt']);
                            $this->tree['model_img1'] = Tools::unesc($ab->qrow['img1']);
                            $this->tree['model_img2'] = Tools::unesc($ab->qrow['img2']);
                            $this->tree['model_text1'] = Tools::unesc($ab->qrow['text1']);
                            $this->tree['model_text2'] = Tools::unesc($ab->qrow['text2']);
                            $this->fname .= ' ' . Tools::unesc($ab->qrow['name']);
                            $this->spath .= '/' . $ab->qrow['sname'];
                            $this->tree['avto_id'] = $ab->qrow['avto_id'];
                            if (!empty($ab->qrow['avto_image'])) {
                                $this->tree['ext_avto_info']['avto_image'] = $ab->qrow['avto_image'];
                            }
                            $this->tree['ext_avto_info']['show_rating'] = $this->tree['ext_avto_info']['show_rating'] ? 1 : $ab->qrow['show_rating'];
                        }
                        $this->tree['models'][$ab->qrow['avto_id']] = array(
                            'sname' => $ab->qrow['sname'],
                            'name' => Tools::unesc($ab->qrow['name']),
                            'alt' => Tools::unesc($ab->qrow['alt']),
                            'img1' => Tools::unesc($ab->qrow['img1']),
                            'img2' => Tools::unesc($ab->qrow['img2'])
                        );
                    }
                }
                if (@$this->tree['model_id']) {
                    $ab->query("SELECT * FROM ab_avto WHERE (model_id='{$this->tree['model_id']}')AND(year_id=0)AND(NOT H) ORDER BY name");
                    if ($ab->qnum()) {
                        while ($ab->next() !== false) {
                            if (!$byId && $ab->qrow['sname'] == @$s['syear'] || $byId && $ab->qrow['avto_id'] == @$s['syear']) {
                                $this->tree['year_id'] = $ab->qrow['avto_id'];
                                $this->tree['year_name'] = Tools::unesc($ab->qrow['name']);
                                $this->tree['year_sname'] = Tools::unesc($ab->qrow['sname']);
                                $this->tree['year_alt'] = Tools::unesc($ab->qrow['alt']);
                                $this->tree['year_img1'] = Tools::unesc($ab->qrow['img1']);
                                $this->tree['year_img2'] = Tools::unesc($ab->qrow['img2']);
                                $this->tree['year_text1'] = Tools::unesc($ab->qrow['text1']);
                                $this->tree['year_text2'] = Tools::unesc($ab->qrow['text2']);
                                $y = Tools::unesc($ab->qrow['name']) . ' г/в';
                                $fname = $this->fname;
                                $this->fname .= ' ' . $y;
                                $this->spath .= '/' . $ab->qrow['sname'];
                                $this->tree['avto_id'] = $ab->qrow['avto_id'];
                                if (!empty($ab->qrow['avto_image'])) {
                                    $this->tree['ext_avto_info']['avto_image'] = $ab->qrow['avto_image'];
                                }
                                $this->tree['ext_avto_info']['show_rating'] = $this->tree['ext_avto_info']['show_rating'] ? 1 : $ab->qrow['show_rating'];
                            }
                            $this->tree['years'][$ab->qrow['avto_id']] = array(
                                'sname' => $ab->qrow['sname'],
                                'name' => Tools::unesc($ab->qrow['name']),
                                'alt' => Tools::unesc($ab->qrow['alt']),
                                'img1' => Tools::unesc($ab->qrow['img1']),
                                'img2' => Tools::unesc($ab->qrow['img2'])
                            );
                        }
                    }
                    if (@$this->tree['year_id']) {
                        $ab->query("SELECT * FROM ab_avto WHERE (year_id='{$this->tree['year_id']}')AND(NOT H) ORDER BY name");
                        if ($ab->qnum()) {
                            while ($ab->next() !== false) {
                                if (!$byId && $ab->qrow['sname'] == @$s['smodif'] || $byId && $ab->qrow['avto_id'] == @$s['smodif']) {
                                    $this->tree['modif_id'] = $ab->qrow['avto_id'];
                                    $this->tree['modif_name'] = Tools::unesc($ab->qrow['name']);
                                    $this->tree['modif_sname'] = Tools::unesc($ab->qrow['sname']);
                                    $this->tree['modif_alt'] = Tools::unesc($ab->qrow['alt']);
                                    $this->tree['modif_img1'] = Tools::unesc($ab->qrow['img1']);
                                    $this->tree['modif_img2'] = Tools::unesc($ab->qrow['img2']);
                                    $this->tree['modif_text1'] = Tools::unesc($ab->qrow['text1']);
                                    $this->tree['modif_text2'] = Tools::unesc($ab->qrow['text2']);
                                    $this->fname = $fname . ' ' . Tools::unesc($ab->qrow['name']) . ' ' . $y;
                                    $this->spath .= '/' . $ab->qrow['sname'];
                                    $this->tree['avto_id'] = $ab->qrow['avto_id'];
                                    if (!empty($ab->qrow['avto_image'])) {
                                        $this->tree['ext_avto_info']['avto_image'] = $ab->qrow['avto_image'];
                                    }
                                    $this->tree['ext_avto_info']['show_rating'] = $this->tree['ext_avto_info']['show_rating'] ? 1 : $ab->qrow['show_rating'];
                                }
                                $this->tree['modifs'][$ab->qrow['avto_id']] = array(
                                    'sname' => $ab->qrow['sname'],
                                    'name' => Tools::unesc($ab->qrow['name']),
                                    'alt' => Tools::unesc($ab->qrow['alt']),
                                    'img1' => Tools::unesc($ab->qrow['img1']),
                                    'img2' => Tools::unesc($ab->qrow['img2'])
                                );
                            }
                        }
                    }
                }
            }
            uasort($this->tree['years'], function ($a, $b) {
                return $a['name'] > $b['name'] ? -1 : 1;
            });
        }
        unset($ab);
    }

    function load_type_arr()
    {
        $this->type_arr = array(
            1 => array(
                10 => 'Заводская комплектация',
                12 => 'Заводская комплектация - передняя ось',
                13 => 'Заводская комплектация - задняя ось',
                15 => 'Варианты замены',
                18 => 'Для тюнинга: передняя ось',
                19 => 'Для тюнинга: задняя ось'
            ),
            2 => array(
                20 => 'Заводская комплектация',
                22 => 'Заводская комплектация - передняя ось',
                23 => 'Заводская комплектация - задняя ось',
                25 => 'Варианты замены',
                28 => 'Для тюнинга: передняя ось',
                29 => 'Для тюнинга: задняя ось'
            )
        );
        return true;
    }

    function avto_sh($avto_id, $grs = array(1, 2))
    {
        //$this->load_type_arr();
        $avto_id = (int)$avto_id;
        if ($this->avto_id == $avto_id) return $this->avto;
        $this->avto_id = $avto_id;
        $this->avto = array(
            1 => array(
                10 => array(), // Заводская комплектация
                1213 => array(), // Заводская комплектация спарка
                15 => array(),  // Варианты замены
                1819 => array() // для тюнинга - спарка
            ),
            2 => array(
                20 => array(),  // Заводская комплектация
                2223 => array(),  // Заводская комплектация спарка
                25 => array(),   // Варианты замены
                2829 => array()   // для тюнинга - спарка
            )
        );

        if (empty($avto_id)) return $this->avto;

        foreach ($grs as $gr) {
            if ($gr == 1) {
                $r = $this->fetchAll("SELECT gr, avtosh_id, avto_id, P1+'0' AS P1, P2+'0' AS P2, P3+'0' AS P3, P4+'0' AS P4, P5+'0' AS P5, P6+'0' AS P6, avto_type_id, rel_id, _upd FROM ab_avtosh WHERE avto_id='$avto_id' AND gr='$gr' ORDER BY avto_type_id, P1, P3, P2", MYSQLI_ASSOC);
            } else {
                $r = $this->fetchAll("SELECT gr, avtosh_id, avto_id, P1+'0' AS P1, P2+'0' AS P2, P3+'0' AS P3, P4+'0' AS P4, P5+'0' AS P5, P6+'0' AS P6, avto_type_id, rel_id, _upd FROM ab_avtosh WHERE avto_id='$avto_id' AND gr='$gr' ORDER BY avto_type_id, P2, P5, P1", MYSQLI_ASSOC);
            }
            if (count($r)) foreach ($r as $k => $v) {
                $rel_id = $v['rel_id'];
                $avtosh_id = $v['avtosh_id'];
                $avto_type_id = $v['avto_type_id'];
                $gr = $v['gr'];
                if ($gr == 1) {
                    foreach ($v as $k1 => $v1) if (!in_array($k1, array('P1', 'P2', 'P3'))) unset($v[$k1]);
                } elseif ($gr == 2) {
                    foreach ($v as $k1 => $v1) if (!in_array($k1, array(
                        'P1',
                        'P2',
                        'P3',
                        'P4',
                        'P5',
                        'P6'
                        ))
                        ) unset($v[$k1]);
                }
                if (array_search($avto_type_id, array(0, 10, 15, 20, 25))) {
                    $this->avto[$gr][$avto_type_id][$avtosh_id] = $v;
                } elseif (array_search($avto_type_id, array(0, 12, 18, 22, 28))) {
                    $part = $avto_type_id . ($avto_type_id + 1);
                    $this->avto[$gr][$part][$avtosh_id] = array(1 => $v, 2 => array());
                } elseif (array_search($avto_type_id, array(0, 13, 19, 23, 29))) {
                    $part = ($avto_type_id - 1) . $avto_type_id;
                    $this->avto[$gr][$part][$rel_id][2] = $v;
                }
            }
        }
        return $this->avto;
    }

    /**
    * Возвращает массив id-ков производителей из cc_brand по массиву id-ков производителей из ab_avto
    * 
    * @param mixed $vendors_array - Массив id брендов
    * @param int $gr - группа (шины/диски). По умолчанию - диски
    */
    function getBrandsIds ($vendors_array = null, $gr = 2)
    {
        $result = Array();

        // В классах моделей всегда экранируем входные параметры
        if(is_array($vendors_array)) foreach($vendors_array as $k=>&$v) $v=(int)$v; else $vendors_array=(int)$vendors_array;

        if (!empty($vendors_array))
        {
            $brands_ids  = $this->fetchAll("SELECT brand_id FROM cc_brand WHERE avto_id IN (".implode(',', $vendors_array).") AND gr = '$gr';", MYSQLI_ASSOC);
        }
        else
        {
            $brands_ids  = $this->fetchAll("SELECT brand_id FROM cc_brand WHERE replica = 0 AND gr = '$gr';", MYSQLI_ASSOC);
        }
        if (!empty($brands_ids))
        {
            foreach ($brands_ids as $bid)
            {
                $result[] = $bid['brand_id'];
            }
        }
        return $result;
    }

    /**
    * Возвращает все модели (по массиву avto_id, который строится на основании параметров) из таблицы  ab_avto по неполному набору параметров
    * 
    * @param mixed $vendors_id -  id марки
    * @param mixed $models_id -  id модели
    * @param mixed $years_id  -  id года
    * @param mixed $grs - шины и/или диски
    * @param mixed $modif_id - уточнение конкретной модели (нужно для результата)
    */
    function avto_sh_array($vendors_id, $models_id, $years_id = null, $grs = array(1, 2), $modif_id = null)
    {
        // В классах моделей всегда экранируем входные параметры
        $vendors_id = (int)$vendors_id;
        $models_id = (int)$models_id;

        if (!empty($vendors_id) && !empty($models_id))
        {
            if (empty($modif_id)) // Если не задана конкретная модификация
            {
                $sql = "SELECT DISTINCT `avto_id` FROM `ab_avto` WHERE";
                if (!empty($years_id))
                {
                    $years_id = (int)$years_id;
                    $sql .= " `year_id` = ".$years_id;  
                }
                else 
                {
                    $sql .= " `year_id` != 0";   
                }
                $sql .= "     
                AND `model_id` = '$models_id'
                AND `vendor_id` = '$vendors_id'";
                //
                $avto_ids  = $this->fetchAll($sql, MYSQLI_ASSOC);
            }
            else  $avto_ids = Array(Array('avto_id' => $modif_id));  // Если задана конкретная модификация
            //
            if (!empty($avto_ids))
            {
                $auto_ids_for_implode = Array();  
                foreach ($avto_ids as $aid)
                {
                    $auto_ids_for_implode[] = $aid['avto_id'];
                }
                $this->avto = array(
                    1 => array(
                        10 => array(), // Заводская комплектация
                        1213 => array(), // Заводская комплектация спарка
                        15 => array(),  // Варианты замены
                        1819 => array() // для тюнинга - спарка
                    ),
                    2 => array(
                        20 => array(),  // Заводская комплектация
                        2223 => array(),  // Заводская комплектация спарка
                        25 => array(),   // Варианты замены
                        2829 => array()   // для тюнинга - спарка
                    )
                );             
                foreach ($grs as $gr) 
                {
                    $r = $this->fetchAll("SELECT cc_brand.brand_id, cc_brand.img1 as 'brand_img', ab_avtosh.gr, ab_avtosh.avtosh_id, ab_avtosh.avto_id, ab_avtosh.P1+'0' AS P1, ab_avtosh.P2+'0' AS P2, 
                        ab_avtosh.P3+'0' AS P3, ab_avtosh.P4+'0' AS P4, ab_avtosh.P5+'0' AS P5, ab_avtosh.P6+'0' AS P6, ab_avtosh.avto_type_id, ab_avtosh.rel_id, ab_avtosh._upd 
                        FROM ab_avtosh 
                        LEFT OUTER JOIN cc_brand ON cc_brand.avto_id = '$vendors_id'
                        WHERE ab_avtosh.avto_id IN (".implode(',', $auto_ids_for_implode).") AND ab_avtosh.gr='$gr' 
                        ORDER BY ab_avtosh.avto_type_id, ab_avtosh.P1, ab_avtosh.P3, ab_avtosh.P2", MYSQLI_ASSOC);
                    $brand_id = null;
                    if (count($r))
                    { 
                        foreach ($r as $k => $v) 
                        {
                            if (empty($brand_id))
                            {
                                $brand_id = $v['brand_id'];
                                $brand_img = $v['brand_img'];
                            }
                            $rel_id = $v['rel_id'];
                            $avtosh_id = $v['avtosh_id'];
                            $avto_type_id = $v['avto_type_id'];
                            $gr = $v['gr'];
                            if ($gr == 1) 
                            {
                                foreach ($v as $k1 => $v1) 
                                {
                                    if (!in_array($k1, array('P1', 'P2', 'P3'))) 
                                    {
                                        unset($v[$k1]);
                                    }
                                }
                            } 
                            elseif ($gr == 2) 
                            {
                                foreach ($v as $k1 => $v1) if (!in_array($k1, array(
                                    'P1',
                                    'P2',
                                    'P3',
                                    'P4',
                                    'P5',
                                    'P6'
                                    ))
                                    ) unset($v[$k1]);
                            }

                            if (array_search($avto_type_id, array(0, 10, 15, 20, 25))) 
                            {
                                $this->avto[$gr][$avto_type_id][$avtosh_id] = $v;   
                            } 
                            elseif (array_search($avto_type_id, array(0, 12, 18, 22, 28))) 
                            {
                                $part = $avto_type_id . ($avto_type_id + 1);
                                $this->avto[$gr][$part][$avtosh_id] = array(1 => $v, 2 => array());     
                            } 
                            elseif (array_search($avto_type_id, array(0, 13, 19, 23, 29))) 
                            {
                                $part = ($avto_type_id - 1) . $avto_type_id;
                                $this->avto[$gr][$part][$rel_id][2] = $v;
                            }
                        }
                    }
                }
                // Убираем совпадения   
                foreach($this->avto as $rad=>$v)
                {
                    $checked_vals = Array();
                    foreach($v as $type=>$vv)
                    {
                        foreach($vv as $i=>$row)
                        {     
                            $val_checked = false;
                            foreach ($checked_vals as $ch_val)
                            {
                                if (serialize($row) == serialize($ch_val))
                                {
                                    $val_checked = true;
                                }
                            }
                            if ($val_checked)
                            {
                                unset($this->avto[$rad][$type][$i]);
                            }
                            else
                            {
                                $checked_vals[] = $row;
                            }
                        }
                    }
                }
                $this->brand_id = @$brand_id;// Устанавливаем id бренда
                $this->brand_img = @$brand_img;// Устанавливаем img бренда
                ///
                return $this->avto;
            }
        }
    }

    function avto_sh0($avto_id)
    {
        $this->load_type_arr();
        $avto_id = (int)$avto_id;
        //	if($this->avto_id==$avto_id) return $this->avto;
        $this->avto = array(1 => array(), 2 => array()); //gr
        foreach ($this->type_arr as $gr => $gr_v) {
            foreach ($gr_v as $type_id => $type_v) {
                if ($gr == 1) {
                    $this->query("SELECT avtosh_id, avto_id, P1+'0' AS P1, P2+'0' AS P2, P3+'0' AS P3, P4+'0' AS P4, P5+'0' AS P5, P6+'0' AS P6, avto_type_id, rel_id, _upd, manual_insert, IF(pos=0,9999,pos) as pos FROM ab_avtosh WHERE avto_type_id='$type_id' AND avto_id='$avto_id' ORDER BY pos, P1, P3, P2");
                } else {
                    $this->query("SELECT avtosh_id, avto_id, P1+'0' AS P1, P2+'0' AS P2, P3+'0' AS P3, P4+'0' AS P4, P5+'0' AS P5, P6+'0' AS P6, avto_type_id, rel_id, _upd, manual_insert, IF(pos=0,9999,pos) as pos FROM ab_avtosh WHERE avto_type_id='$type_id' AND avto_id='$avto_id'  ORDER BY pos, P2, P5, P1");
                }
                $this->avto[$gr][$type_id] = array();
                if ($this->qnum()) {
                    $r = array();
                    while ($this->next(MYSQLI_ASSOC) !== false) {
                        if (!array_search($type_id, array(0, 13, 19, 23, 29))) {
                            //удаляем лишние поля
                            $r = $this->qrow;
                            unset($r['avto_id'], $r['avto_type_id'], $r['gr'], $r['rel_id']);
                            if($r['pos'] == 9999) unset($r['pos']);
                            foreach ($r as $k => $v) $r[$k] = $r[$k] == 0 ? '' : $r[$k];
                            $this->avto[$gr][$type_id][] = $r;
                        } else {
                            $r[$this->qrow['rel_id']] = $this->qrow;
                        }
                    }
                    if (array_search($type_id, array(0, 13, 19, 23, 29))) {
                        foreach ($this->avto[$gr][$type_id - 1] as $k => $v) {
                            $rr = $r[$v['avtosh_id']];
                            unset($rr['avto_id'], $rr['avto_type_id'], $rr['gr'], $rr['rel_id']);
                            if($r['pos'] == 9999) unset($r['pos']);
                            foreach ($rr as $k => $v) $rr[$k] = $rr[$k] == 0 ? '' : $rr[$k];
                            $this->avto[$gr][$type_id][] = $rr;
                        }
                    }
                }
            }
        }
        $this->avto_id = $avto_id;
        return $this->avto;
    }

    function getCommon($avto_id)
    {
        $avto_id = (int)$avto_id;
        if (empty($avto_id)) return false;
        $c = new DB();
        $d = $c->getOne("SELECT * FROM ab_common WHERE avto_id='$avto_id'");
        if ($d !== 0) {
            $r = array(
                'common_id' => $d['common_id'],
                'pcd' => Tools::unesc($d['pcd']),
                'dia' => Tools::unesc($d['dia']),
                'bolt' => Tools::unesc($d['bolt']),
                'gaika' => Tools::unesc($d['gaika'])
            );
        } else $r = false;
        unset($c);
        return $r;
    }

    function setCommon($avto_id, $data)
    {
        $avto_id = (int)$avto_id;
        if (empty($avto_id)) return false;
        $c = new DB();
        if (!empty($data['common_id'])){
            $cid = $data['common_id'];
            unset($data['common_id']);
            $d = $c->update('ab_common', $data, " common_id = '{$cid}' AND avto_id = '{$avto_id}'");
        }else{
            unset($data['common_id']);
            $d = $c->insert('ab_common', array_merge(Array('avto_id' => $avto_id), $data));
        }
        if ($d !== 0) {
            $r =true;
        } else $r = false;
        unset($c);
        return $r;
    }

    function delCommon($common_id)
    {
        $common_id = (int)$common_id;
        if (empty($common_id)) return false;
        $c = new DB();
        $d = $c->del('ab_common', 'common_id', $common_id);
        if ($d !== 0) {
            $r =true;
        } else $r = false;
        unset($c);
        return $r;
    }

    function getCommons($vendors_id, $models_id, $years_id = null)
    {
        // В классах моделей всегда экранируем входные параметры
        $vendors_id = (int)$vendors_id;
        $models_id = (int)$models_id;

        $sql = "SELECT DISTINCT `avto_id` FROM `ab_avto` WHERE";
        if (!empty($years_id))
        {
            $years_id = (int)$years_id;
            $sql .= " `year_id` = ".$years_id;
        }
        else
        {
            $sql .= " `year_id` != 0";
        }
        $sql .= "
        AND `model_id` = '$models_id'
        AND `vendor_id` = '$vendors_id'";
        //
        $avto_ids  = $this->fetchAll($sql, MYSQLI_ASSOC);
        $r = false;
        if (!empty($avto_ids))
        {
            $auto_ids_for_implode = Array();
            foreach ($avto_ids as $aid)
            {
                $auto_ids_for_implode[] = $aid['avto_id'];
            }
            $d = $this->fetchAll("SELECT * FROM ab_common WHERE avto_id IN ('".implode(',', $auto_ids_for_implode)."')");
            if ($d !== 0)
            {
                foreach ($d as $item)
                {
                    $r[] = array(
                        'common_id' => $item['common_id'],
                        'pcd' => Tools::unesc($item['pcd']),
                        'dia' => Tools::unesc($item['dia']),
                        'bolt' => Tools::unesc($item['bolt']),
                        'gaika' => Tools::unesc($item['gaika'])
                    );
                }
            }
            return $r;
        }
    }

    function getCommonsList ($avto_id)
    {

        if (!empty($avto_id)) {
            $d = $this->fetchAll("SELECT * FROM `ab_common` WHERE `avto_id` = " . $avto_id);
        } else {
            $d = $this->fetchAll("SELECT * FROM `ab_common`");
        }

        if ($d !== 0)
        {
            foreach ($d as $item)
            {
                $r[] = array(
                    'common_id' => $item['common_id'],
                    'pcd' => Tools::unesc($item['pcd']),
                    'dia' => Tools::unesc($item['dia']),
                    'bolt' => Tools::unesc($item['bolt']),
                    'gaika' => Tools::unesc($item['gaika'])
                );
            }
        }
        return $r;
    }

    public function parseText($text, $pattern, $separator)
    {
        $lex_array = Array();   
        $text = str_replace(array("\n", "\r", "\t"), Array('', '', ''), $text);
        preg_match_all($pattern, $text, $preg_lex_array);
        // Чистим данные и формируем структуру
        if(!empty($preg_lex_array[1]))
        {
            foreach ($preg_lex_array[1] as $i=>$lex)
            {
                $vars = explode($separator, $lex);
                $lex_array[$i] = Array('var' => $vars, 'last_index' => count($vars) - 1);
            }
            // Формируем новый случайный текст
            if (!empty($preg_lex_array[0]))
            {
                foreach ($preg_lex_array[0] as $i=>$f_lex) 
                {     
                    $text = str_replace(
                        $f_lex, 
                        $lex_array[$i]['var'][rand(0, $lex_array[$i]['last_index'])], 
                        $text
                    );
                }
            }
        }
        return $text;
    }

    /**
     * @param $params - Для шин [P1,P2,P3], для дисков [P1,P2,P3,P4,P5,P6]
     * @param $gr - шины(1)/диски(2)
     * @return mixed
     */
    public function getAvtoArrayByTipo($params, $gr)
    {
        $result = Array();
        if ($gr == 1) // шины
        {
            $dbRes = $this->fetchAll("SELECT a.*, (SELECT name FROM ab_avto WHERE avto_id = a.vendor_id) as v_name,
                                      (SELECT name FROM ab_avto WHERE avto_id = a.year_id) as y_name,
                                      (SELECT name FROM ab_avto WHERE avto_id = a.model_id) as m_name
                                      FROM ab_avtosh
                                      JOIN ab_avto a USING (avto_id)
                                      WHERE ab_avtosh.gr = 1 AND P1='{$params['P1']}' AND P2='{$params['P2']}' AND P3='{$params['P3']}';", MYSQLI_ASSOC);
            if (!empty($dbRes))
            {
                foreach($dbRes as $row)
                {
                    $result[$row['v_name']][$row['m_name']][$row['name']][] = Array(
                        'modif' => $row['name'],
                        'year'  => $row['y_name'],
                        'brand' => $row['v_name'],
                        'model' => $row['m_name']
                    );
                }
            }
        }
        elseif($gr == 2) // диски
        {
            $is_uniq = false;
            if ( // Если такая сверловка - ограничения только по ней
                ((float)$params['P4'] == 5 and (float)$params['P6'] == 150) or
                ((float)$params['P4'] == 6 and (float)$params['P6'] == 139.7) or
                ((float)$params['P4'] == 6 and (float)$params['P6'] == 114.3) or
                ((float)$params['P4'] == 5 and (float)$params['P6'] == 127) or
                ((float)$params['P4'] == 5 and (float)$params['P6'] == 139.7) or
                ((float)$params['P4'] == 6 and (float)$params['P6'] == 115) or
                ((float)$params['P4'] == 8 and (float)$params['P6'] == 170) or
                ((float)$params['P4'] == 6 and (float)$params['P6'] == 135)
            )
            {
                $is_uniq = true;
            }
            $sql = "SELECT a.*, ab_avtosh.P1,
                    (SELECT name FROM ab_avto WHERE avto_id = a.vendor_id) as v_name,
                    (SELECT name FROM ab_avto WHERE avto_id = a.year_id) as y_name,
                    (SELECT name FROM ab_avto WHERE avto_id = a.model_id) as m_name
                    FROM ab_avtosh
                    JOIN ab_avto a USING (avto_id)
                    WHERE ab_avtosh.gr = 2";
            if (!$is_uniq)
            {
                //AND ".(is_array($params['P1']) ? "(P1>='{$params['P1']['from']}' AND P1<='{$params['P1']['to']}')" : "P1='{$params['P1']}'")."
                $sql .= "
                    AND P2='{$params['P2']}' ".(is_array($params['P3']) ? "AND (P3>='{$params['P3']['from']}' AND P3<='{$params['P3']['to']}')" : "AND P3='{$params['P3']}'")."
                    AND P4='{$params['P4']}' AND P5='{$params['P5']}' AND P6='{$params['P6']}';
                ";
            }
            else
            {
                $sql .= "
                    AND P4='{$params['P4']}' AND P6='{$params['P6']}' AND P5='{$params['P5']}';
                ";
            }
            $dbRes = $this->fetchAll($sql);
            //Tools::p($sql, false);
            if (!empty($dbRes))
            {
                foreach($dbRes as $row)
                {
                    if (
                    ((float)$params['P1']['ex'] >= (float)$row['P1'] + (float)$params['P1']['_from']  && (float)$params['P1']['ex'] <= (float)$row['P1']  + (float)$params['P1']['_to'])
                    or $is_uniq
                    ){
                        $result[$row['v_name']][$row['m_name']][$row['name']][] = Array(
                            'modif' => $row['name'],
                            'year' => $row['y_name'],
                            'brand' => $row['v_name'],
                            'model' => $row['m_name']
                        );
                    }
                }
            }
        }
        return $result;
    }
}