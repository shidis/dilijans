<?
if (!defined('true_enter')) die ("Direct access not allowed!");

class CC_Ctrl extends CC_Base
{

    var $min_extra_arr = false; // глобальные сезонные минимальные наценки на радиус
    var $extra_arr = array(); // наценки для брендов
    var $cur_name; // массив с валютами и курсами
    var $sup_arr = array();
    var $sezon_extra; // глобальные наценки на сезон

    function __construct($r = array())
    {
        parent::__construct($r);
    }


    function load_cur()
    {
        if (!empty($this->cur_name)) return;
        $cc = new DB;
        $this->cur_name[0][0] = 'руб';
        $this->cur_name[0][1] = '0';
        $cc->query("SELECT cc_cur.* FROM cc_cur ORDER BY cur_id");
        while ($cc->next() != false) {
            $this->cur_name[$cc->qrow['cur_id']][0] = $cc->qrow['name'];
            $this->cur_name[$cc->qrow['cur_id']][1] = $cc->qrow['V'];
        }
        $this->cur_loaded = true;
        unset($cc);
    }


    function update_price($cat_id, $value, $cur_id)
    {
        $c = new DB;
        $value = str_replace(',', '.', $value);
        $value = str_replace(' ', '', $value);
        $value = floatval($value);
        $res = $c->query("UPDATE cc_cat SET bprice='$value', cur_id='$cur_id' WHERE cat_id='$cat_id'");
        unset($c);
        return ($res);
    }

    function hide_switch($table, $id_name, $id)
    {
        $c = new DB();
        if ($table == 'cc_model' || $table == 'cc_cat') $c->query("SELECT H,gr,model_id FROM $table WHERE $id_name='$id'");
        elseif ($table == 'cc_brand') $c->query("SELECT H,gr FROM $table WHERE $id_name='$id'");
        else $c->query("SELECT H FROM $table WHERE $id_name='$id'");
        $c->next();
        if ($table == 'cc_model' || $table == 'cc_cat') {
            $gr = $c->qrow['gr'];
            $model_id = $c->qrow['model_id'];
        } elseif ($table == 'cc_brand') $gr = $c->qrow['gr'];
        $res = !(boolean)$c->qrow['H'];
        if ($c->qrow['H'] != '1')
            $c->query("UPDATE $table SET H='1' WHERE $id_name='$id'");
        else $c->query("UPDATE $table SET H='0' WHERE $id_name='$id'");
        if ($table == 'cc_cat') {
            if ($c->qrow['gr'] == 1 && Cfg::get('INIS_S1S2')) CC_inis::modelUpdate($model_id);
            if ($c->qrow['gr'] == 2 && isset($this->RDisk)) $this->RDisk->modelUpdate($model_id);
            if ($c->qrow['gr'] == 1 && isset($this->RTyre)) $this->RTyre->modelUpdate($model_id);
            if (isset($this->intPrice)) $this->intPrice->modelUpdate($model_id);
            if (Cfg::get('model_SC')) CC_ModelSC::modelUpdate($model_id);
        }
        if ($table == 'cc_brand') $this->addCacheTask('brands', $gr);
        if ($table == 'cc_cat') $this->addCacheTask('sizes', $gr);
        if ($table == 'cc_model' && $gr) $this->addCacheTask('sizes', $gr);
        unset($c);
        return ($res);
    }

    function ld($table, $id_name, $id, $gr = 0)
    {
        $c = new CC_Ctrl;
        if ($table == 'cc_cat') {
            $c->que('cat_by_id', $id);
            $c->next();
            $model_id = $c->qrow['model_id'];
            $gr = $c->qrow['gr'];
        }
        if ($table == 'cc_cat' || $table == 'cc_model') {
            $d = $c->getOne("SELECT H,gr FROM $table WHERE $id_name='$id'");
            $gr = @$d['gr'];
        }
        $res = $c->query("UPDATE $table SET LD='1' WHERE $id_name='$id'");
        if ($table == 'cc_cat') {
            if ($gr == 1 && Cfg::get('INIS_S1S2')) CC_inis::modelUpdate($model_id);
            if ($gr == 2 && isset($this->RDisk)) $this->RDisk->modelUpdate($model_id);
            if ($gr == 1 && isset($this->RTyre)) $this->RTyre->modelUpdate($model_id);
            if (isset($this->intPrice)) $this->intPrice->modelUpdate($model_id);
            if (Cfg::get('model_SC')) CC_ModelSC::modelUpdate($model_id);
        }
        if ($table == 'cc_brand' && $gr) {
            $this->addCacheTask('brands', $gr);
        }
        if ($table == 'cc_cat' && $gr) $this->addCacheTask('sizes', $gr);
        if ($table == 'cc_model' && $gr) {
            $this->addCacheTask('sizes', $gr);
        }
        if($table == 'cc_brand'){
            $this->imgDelete('cc_brand',$id_name, $id,'img1');
            $this->imgDelete('cc_brand',$id_name, $id,'img2');
        }
        if($table == 'cc_model'){
            $this->imgDelete('cc_model',$id_name, $id,'img1');
            $this->imgDelete('cc_model',$id_name, $id,'img2');
            $this->imgDelete('cc_model',$id_name, $id,'img3');
        }
        unset($c);
        return ($res);
    }

    function get_curval($cur_id)
    {
        $c = new DB;
        $c->query("SELECT V FROM cc_cur WHERE cur_id='$cur_id'");
        if ($c->qnum()) {
            $c->next();
            $res = $c->qrow['V'];
        } else $res = 0;
        unset($c);
        return ($res);
    }

    function load_extra()
    {
        if (!empty($this->extra_arr)) return;
        $this->extra_arr = array();
        $cc = new DB;
        $cc->query("SELECT * FROM cc_extra");
        if ($cc->qnum()) while ($cc->next() !== false) {
            switch ($cc->qrow['extra_group']) {
                case 1:
                case 2:
                    $this->extra_arr[$cc->qrow['extra_group']][$cc->qrow['brand_id']][($cc->qrow['P_value'] * 1) . ''] = array(
                        'extra' => $cc->qrow['extra'] * 1,
                        'minExtra' => $cc->qrow['minExtra'] * 1
                    );
                    break;
                case 3:
                    $this->extra_arr
                    [$cc->qrow['extra_group']]
                    [$cc->qrow['brand_id']]
                    [$cc->qrow['S_value']]
                    [($cc->qrow['P_value'] * 1) . ''] = array(
                        'extra' => $cc->qrow['extra'] * 1,
                        'minExtra' => $cc->qrow['minExtra'] * 1
                    );
                    break;
            }
        }
        $this->min_extra_arr = array();
        $d = $cc->fetchAll("SELECT * FROM cc_min_extra");
        foreach ($d as $v) {
            $this->min_extra_arr[$v['gr']][($v['P'] * 1) . ''][($v['PVal'] * 1) . ''] = $v['extra'] * 1;
        }
        $this->global_d_extra = (int)Data::get('global_d_extra');
        $this->global_t_extra = (int)Data::get('global_t_extra');
        $this->sezon_extra = array();
        $this->sezon_extra[0] = 0;
        $this->sezon_extra[1] = Data::get('sezon_extra_1');
        $this->sezon_extra[2] = Data::get('sezon_extra_2');;
        $this->sezon_extra[3] = Data::get('sezon_extra_3');;
        unset($cc);
        $this->roundPriceUpTo = abs((int)Data::get('roundPriceUpTo'));
        if ($this->roundPriceUpTo == 0) $this->roundPriceUpTo = 1;
    }

    function extra_price_update($cat_id)
    {
        $cc = new DB;
        $cat_id = (int)$cat_id;
        $cc->query("SELECT cc_cat.sc, cc_cat.scprice, cc_cat.cprice, cc_brand.extra_b, cc_brand.brand_id, cc_cat.cat_id, cc_cat.P1, cc_cat.P5, cc_cat.gr, cc_cat.bprice, cc_cat.cur_id, cc_model.sup_id, cc_model.P1 AS MP1, cc_model.P3 AS MP3, cc_model.P2 AS MP2 FROM cc_brand RIGHT JOIN (cc_cat LEFT JOIN cc_model ON cc_cat.model_id = cc_model.model_id) ON cc_brand.brand_id = cc_model.brand_id WHERE cc_cat.cat_id='$cat_id' AND NOT cc_cat.fixPrice");
        if ($cc->qnum()) {
            $cc->next();
            if ($cc->qrow['gr'] == 1)
                $pb = $this->extra_price($cc->qrow['bprice'], $cc->qrow['cur_id'], $cc->qrow['P1'], $cc->qrow['sup_id'], $cc->qrow['brand_id'], $cc->qrow['extra_b'], $cc->qrow['gr'], $cc->qrow['MP1']);
            elseif ($cc->qrow['gr'] == 2)
                $pb = $this->extra_price($cc->qrow['bprice'], $cc->qrow['cur_id'], $cc->qrow['P5'], $cc->qrow['sup_id'], $cc->qrow['brand_id'], $cc->qrow['extra_b'], $cc->qrow['gr']);
            if ($pb != $cc->qrow['cprice'])
                if (!$cc->query("UPDATE cc_cat SET cprice='$pb' WHERE cat_id='{$cat_id}'")) die('[extra_price()]: Ошибка БД');
        } else $pb = false;
        unset($cc);
        return $pb;
    }

    function extra_price_update_for_model($model_id)
    {
        $cc = new DB;
        $ccc = new DB;
        $model_id = (int)$model_id;
        $cc->query("SELECT cc_cat.sc, cc_cat.scprice, cc_cat.cprice, cc_brand.extra_b, cc_brand.brand_id, cc_cat.cat_id, cc_cat.P1, cc_cat.P5, cc_cat.gr, cc_cat.bprice, cc_cat.cur_id, cc_model.sup_id, cc_model.P1 AS MP1, cc_model.P3 AS MP3, cc_model.P2 AS MP2 FROM cc_brand RIGHT JOIN (cc_cat LEFT JOIN cc_model ON cc_cat.model_id = cc_model.model_id) ON cc_brand.brand_id = cc_model.brand_id WHERE (cc_model.model_id='$model_id')AND(NOT cc_cat.LD)AND(NOT cc_brand.LD) AND NOT cc_cat.fixPrice");
        if ($cc->qnum()) while ($cc->next() !== false) {
            if ($cc->qrow['gr'] == 1)
                $pb = $this->extra_price($cc->qrow['bprice'], $cc->qrow['cur_id'], $cc->qrow['P1'], $cc->qrow['sup_id'], $cc->qrow['brand_id'], $cc->qrow['extra_b'], $cc->qrow['gr'], $cc->qrow['MP1']);
            elseif ($cc->qrow['gr'] == 2)
                $pb = $this->extra_price($cc->qrow['bprice'], $cc->qrow['cur_id'], $cc->qrow['P5'], $cc->qrow['sup_id'], $cc->qrow['brand_id'], $cc->qrow['extra_b'], $cc->qrow['gr']);

            if ($pb != $cc->qrow['cprice'])
                if (!$ccc->query("UPDATE cc_cat SET cprice='$pb' WHERE cat_id='{$cc->qrow['cat_id']}'")) die('[extra_price()]: Ошибка БД');
        }
        unset($cc);
        unset($ccc);
    }

    function getMinExtra($gr, $P_name, $P_value)
    {
        $this->load_extra();
        switch ($P_name) {
            case 'radius':
                if ($gr == 1) $P = 1; else $P = 5;
                break;
        }
        return @$this->min_extra_arr[$gr]["$P"]["$P_value"];
    }

    function extra_price($base_price, $cur_id, $Rad, $sup_id, $brand_id, $extra_b, $gr, $sezon = 0)
    {
        $this->load_cur();
        $this->load_extra();
        $basePrice = $base_price * $this->cur_name[$cur_id][1];
        $extra = $extra_b + @$this->extra_arr[2][$brand_id][$sup_id]['extra'] + @$this->extra_arr[1][$brand_id][$Rad]['extra'];
        if ($gr == 1) {
            $extra += $this->global_t_extra;
            $extra += @$this->sezon_extra[(int)$sezon];
            $extra += @$this->extra_arr[3][$brand_id][$sezon]["$Rad"]['extra'];
        } elseif ($gr == 2) {
            $extra += $this->global_d_extra;
        }
        // контроль абсолютных наценок
        if ($base_price > 0) {
            if ($gr == 1) $me = max($this->getMinExtra($gr, 'radius', $Rad), @$this->extra_arr[3][$brand_id][$sezon]["$Rad"]['minExtra'], @$this->extra_arr[1][$brand_id]["$Rad"]['minExtra'], @$this->extra_arr[2][$brand_id][$sup_id]['minExtra']);
            elseif ($gr == 2) $me = max($this->getMinExtra($gr, 'radius', $Rad), @$this->extra_arr[1][$brand_id]["$Rad"]['minExtra'], @$this->extra_arr[2][$brand_id][$sup_id]['minExtra']);
        } else $me = 0;

        if ($me != 0 && $basePrice * $extra / 100 < $me) $res = $basePrice + $me;
        else $res = $basePrice + $basePrice * $extra / 100;

        return Tools::roundUpTo($res, $this->roundPriceUpTo);
    }

    function addCacheTask($task, $gr = 0)
    {
        $o = Data::get('cc_cache_tasks');
        if ($o == '') $o = array('tasks' => array()); else $o = unserialize($o);
        $task = explode(' ', $task);
        foreach ($task as $v) {
            if (!$gr) {
                $o['tasks'][$v] = array(0 => 1);
            } elseif (!@$o['tasks'][$v][0]) {
                $o['tasks'][$v][$gr] = 1;
            }
        }
        Data::set('cc_cache_tasks', serialize($o));
    }

    function clearCacheTask()
    {
        $o = array('tasks' => array());
        Data::set('cc_cache_tasks', serialize($o));
    }

    private function updateCacheSizes($gr = 0)
    {
        if ($gr == 1 || $gr == 0) {
            $this->que('filter', 'P1', 1, 1, Cfg::get('cc_filters_sc_not_zero'));
            $this->write_res('P1', 1);

            $this->que('filter', 'P2', 1, 1, Cfg::get('cc_filters_sc_not_zero'));
            $this->write_res('P2', 1);

            $this->que('filter', 'P3', 1, 1, Cfg::get('cc_filters_sc_not_zero'));
            $this->write_res('P3', 1);

            $this->que('filter', 'P7', 1, 1, Cfg::get('cc_filters_sc_not_zero'));
            $this->write_res('P7', 1);

            echo ' (gr=1 ok)';
        }
        if ($gr == 2 || $gr == 0) {
            $this->que('filter', 'P1', 2, 1, Cfg::get('cc_filters_sc_not_zero'));
            $this->write_res('P1', 2);
            $this->que('filter', 'P1', 2, 1, Cfg::get('cc_filters_sc_not_zero'), 1);
            $this->write_res('r_P1', 2);
            $this->que('filter', 'P1', 2, 1, Cfg::get('cc_filters_sc_not_zero'), 0);
            $this->write_res('nor_P1', 2);

            $this->que('filter', 'P2', 2, 1, Cfg::get('cc_filters_sc_not_zero'));
            $this->write_res('P2', 2);
            $this->que('filter', 'P2', 2, 1, Cfg::get('cc_filters_sc_not_zero'), 1);
            $this->write_res('r_P2', 2);
            $this->que('filter', 'P2', 2, 1, Cfg::get('cc_filters_sc_not_zero'), 0);
            $this->write_res('nor_P2', 2);

            $this->que('filter', 'P3', 2, 1, Cfg::get('cc_filters_sc_not_zero'));
            $this->write_res('P3', 2);
            $this->que('filter', 'P3', 2, 1, Cfg::get('cc_filters_sc_not_zero'), 1);
            $this->write_res('r_P3', 2);
            $this->que('filter', 'P3', 2, 1, Cfg::get('cc_filters_sc_not_zero'), 0);
            $this->write_res('nor_P3', 2);

            $this->que('filter', 'P4', 2, 1, Cfg::get('cc_filters_sc_not_zero'));
            $this->write_res('P4', 2);
            $this->que('filter', 'P4', 2, 1, Cfg::get('cc_filters_sc_not_zero'), 1);
            $this->write_res('r_P4', 2);
            $this->que('filter', 'P4', 2, 1, Cfg::get('cc_filters_sc_not_zero'), 0);
            $this->write_res('nor_P4', 2);

            $this->que('filter', 'P5', 2, 1, Cfg::get('cc_filters_sc_not_zero'));
            $this->write_res('P5', 2);
            $this->que('filter', 'P5', 2, 1, Cfg::get('cc_filters_sc_not_zero'), 1);
            $this->write_res('r_P5', 2);
            $this->que('filter', 'P5', 2, 1, Cfg::get('cc_filters_sc_not_zero'), 0);
            $this->write_res('nor_P5', 2);

            $this->que('filter', 'P6', 2, 1, Cfg::get('cc_filters_sc_not_zero'));
            $this->write_res('P6', 2);
            $this->que('filter', 'P6', 2, 1, Cfg::get('cc_filters_sc_not_zero'), 1);
            $this->write_res('r_P6', 2);
            $this->que('filter', 'P6', 2, 1, Cfg::get('cc_filters_sc_not_zero'), 0);
            $this->write_res('nor_P6', 2);

            $this->que('P46concat', 1, Cfg::get('cc_filters_sc_not_zero'), 0, Data::get('cc_LZexclude'));
            $this->write_res('P4x6', 2);
            $this->que('P46concat', 1, Cfg::get('cc_filters_sc_not_zero'), 1, Data::get('cc_LZexclude'));
            $this->write_res('r_P4x6', 2);
            $this->que('P46concat', 1, Cfg::get('cc_filters_sc_not_zero'), 0, Data::get('cc_LZexclude'));
            $this->write_res('nor_P4x6', 2);

            echo ' (gr=2 ok)';
        }
    }

    function execCacheTasks()
    {
        echo "[CC_Ctrl.execCacheTasks()]:: started...\n";
        MC::set('exec_cc_tasks'.MC::uid(),time(), 2400);
        $o = Data::get('cc_cache_tasks');
        if ($o == '') return; else $o = unserialize($o);
        $no = $o;
        //Tools::prn($o,'Tasks');
        $upd=false;
        ob_start();
        foreach ($o['tasks'] as $task => $v) {
            unset($no['tasks'][$task]);
            switch ($task) {
                case 'waitList':
                    if (!Cfg::get('waitList')) break;
                    echo '[WaitList]: ';
                    foreach ($v as $k1 => $v1) {
                        Orders_WaitList::check($k1);
                        echo "gr=$k1 ok  \n";
                    }
                    $upd=true;
                    break;
                case 'sizes':
                    echo '[SIZES]: ';
                    if(class_exists('App_App')){
                        $app=new App_CC_Actions();
                        if(method_exists($app, 'cacheTask_updateSizes')){
                            echo '[CC_Actions.cacheTask_updateSizes]: ';
                            $res=call_user_func([$app, 'cacheTask_updateSizes'], $v);
                            echo implode(', ',$res);
                            echo ".\n";
                        }
                    }
                    foreach ($v as $k1 => $v1) $this->updateCacheSizes($k1);
                    echo "\n";
                    $upd=true;
                    break;
                case 'brands':
                    echo '[BRANDS]: ';
                    if(class_exists('App_App')){
                        $app=new App_CC_Actions();
                        if(method_exists($app, 'cacheTask_updateBrands')){
                            echo '[CC_Actions.cacheTask_updateBrands]: ';
                            $res=call_user_func([$app, 'cacheTask_updateBrands'], $v);
                            echo implode(', ',$res);
                            echo ".\n";
                        }
                    }
                    if (isset($v[1]) || isset($v[0])) {
                        $this->que('brands_join', 1, 1, '', '', Cfg::get('cc_filters_sc_not_zero') ? true : false);
                        $this->write_res('brands', 1, 1);
                        echo " (gr=1 ok) ";
                    }
                    if (isset($v[2]) || isset($v[0])) {
                        $replica_title = Data::get('cc_replica_title');
                        if ($replica_title == '') $replica_title = 'Replica';
                        $this->que('brands_join', 2, 1, '', '', Cfg::get('cc_filters_sc_not_zero') ? true : false);
                        $this->write_res('brands', 2, $replica_title);
                        $this->que('brands_join', 2, 1, "AND(cc_brand.replica='1')", '', Cfg::get('cc_filters_sc_not_zero') ? true : false);
                        $this->write_res('r_brands', 2, 1);
                        $this->que('brands_join', 2, 1, "AND(cc_brand.replica<>'1')", '', Cfg::get('cc_filters_sc_not_zero') ? true : false);
                        $this->write_res('nor_brands', 2, $replica_title);
                        echo ' (gr=2 ok) ';
                    }
                    echo "\n";
                    $upd=true;
                    break;
                case 'prices':
                case 'pricesNoIntPrice':
                    $upd=true;
                    $gr = -1;
                    echo '[PRICES]: ';
                    if(class_exists('App_App')){
                        $app=new App_CC_Actions();
                        if(method_exists($app, 'cacheTask_updatePrices')){
                            echo '[CC_Actions.cacheTask_updatePrices]: ';
                            $res=call_user_func([$app, 'cacheTask_updatePrices'], $v);
                            echo implode(', ',$res);
                            echo ".\n";
                        }
                    }
                    if (isset($v[1]) && isset($v[2]) || isset($v[0])) $gr = 0; elseif (isset($v[1])) $gr = 1;
                    elseif (isset($v[2])) $gr = 2;
                    if ($gr < 0) break;
                    $this->query("SELECT cc_cat.sc, cc_model.P1 AS MP1, cc_model.P3 AS MP3, cc_model.P2 AS MP2, cc_cat.scprice, cc_cat.cprice, cc_brand.extra_b, cc_brand.brand_id, cc_brand.name AS bname, cc_model.name, cc_model.class_id, cc_model.mspez_id, cc_model.model_id, cc_model.suffix AS msuffix, cc_cat.suffix AS csuffix, cc_cat.cat_id, cc_cat.P1+'0' AS P1, cc_cat.P2+'0' AS P2, cc_cat.P3+'0' AS P3, cc_cat.P4, cc_cat.P5+'0' AS P5, cc_cat.P6+'0' AS P6, cc_cat.P7, cc_cat.H, cc_cat.gr, cc_cat.bprice, cc_cat.cur_id, cc_cat.fixPrice, cc_cat.recomend, cc_model.sup_id FROM cc_brand RIGHT JOIN (cc_cat LEFT JOIN cc_model ON cc_cat.model_id = cc_model.model_id) ON cc_brand.brand_id = cc_model.brand_id WHERE (NOT cc_model.LD)AND(NOT cc_cat.LD)AND(NOT cc_brand.LD) AND cc_cat.fixPrice = 0 " . ($gr > 0 ? "AND(cc_cat.gr='$gr')" : ''));
                    $u = 0;
                    $zero = 0;
                    $me = array();
                    $fi = 0;
                    $cc = new DB;
                    if ($this->qnum()) while ($this->next() != false) {
                        $fi++;
                        if ($this->qrow['gr'] == 1) {
                            $pb = $this->extra_price($this->qrow['bprice'], $this->qrow['cur_id'], $this->qrow['P1'], $this->qrow['sup_id'], $this->qrow['brand_id'], $this->qrow['extra_b'], $this->qrow['gr'], $this->qrow['MP1']);
                            if ($pb == 0) $zero++;
                            if ($pb != $this->qrow['cprice'] && !$this->qrow['recomend']) if (!$cc->query("UPDATE cc_cat SET cprice='$pb' WHERE cat_id='{$this->qrow['cat_id']}'"))
                                die('Ошибка обновления прайсов');
                            else{
                                $u++;
                                $a = $this->qrow['model_id'];
                                $me[$a] = 1;
                            }
                        } else {
                            $pb = $this->extra_price($this->qrow['bprice'], $this->qrow['cur_id'], $this->qrow['P5'], $this->qrow['sup_id'], $this->qrow['brand_id'], $this->qrow['extra_b'], $this->qrow['gr']);
                            if ($pb == 0) $zero++;
                            if ($pb != $this->qrow['cprice'] && !$this->qrow['recomend']) if (!$cc->query("UPDATE cc_cat SET cprice='$pb' WHERE cat_id='{$this->qrow['cat_id']}'"))
                                die('Ошибка обновления прайсов');
                            else {
                                $u++;
                                $a = $this->qrow['model_id'];
                                $me[$a] = 1;
                            }
                        }
                    }
                    echo " (gr=$gr. Всего $fi. Обновлено $u. Из них $zero нулевых) ";
                    unset($cc);
                    if ($task != 'pricesNoIntPrice') break;
                    if (isset($this->intPrice)) {
                        $fi = 0;
                        if (count($me)) {
                            foreach ($me as $k => $v) {
                                $fi++;
                                $this->intPrice->modelUpdate($k);
                            }
                            echo " (Обновлено $fi интервалов цен)";
                        }
                    }
                    echo "\n";
                    break;
                case 'modAll':
                    $upd=true;
                    echo '[modAll]: ';
                    if (isset($v[1]) && isset($v[2]) || isset($v[0])) $gr = 0; elseif (isset($v[1])) $gr = 1;
                    elseif (isset($v[2])) $gr = 2;
                    if (isset($this->RDisk) || isset($this->RTyre) || isset($this->intPrice) || Cfg::get('INIS_S1S2') || Cfg::get('model_SC')) {
                        $d = $this->fetchAll("SELECT model_id,gr FROM cc_model WHERE (NOT LD)" . ($gr > 0 ? "AND(gr='$gr')" : ''), MYSQLI_ASSOC);
                        $fi = 0;
                        foreach ($d as $v1) {
                            $fi++;
                            if (isset($this->intPrice)) $this->intPrice->modelUpdate($v1['model_id']);
                            if (isset($this->RDisk) && $v1['gr'] == 2) $this->RDisk->modelUpdate($v1['model_id']);
                            if (isset($this->RTyre) && $v1['gr'] == 1) $this->RTyre->modelUpdate($v1['model_id']);
                            if ($v1['gr'] == 1 && Cfg::get('INIS_S1S2')) CC_inis::modelUpdate($v1['model_id']);
                            if (Cfg::get('model_SC')) CC_ModelSC::modelUpdate($v1['model_id']);
                        }
                        echo " (gr= $gr. Обновлено $fi интервалов, цен, радиусов)";
                    }
                    echo "\n";
                    break;
                case 'modelsSC':
                    $upd=true;
                    echo '[modelsSC]: ';
                    if(class_exists('App_App')){
                        $app=new App_CC_Actions();
                        if(method_exists($app, 'cacheTask_updateModelSC')){
                            echo '[CC_Actions.cacheTask_updateBrands]: ';
                            $res=call_user_func([$app, 'cacheTask_updateModelSC'], $v);
                            echo implode(', ',$res);
                            echo ".\n";
                        }
                    }
                    if (isset($v[1]) && isset($v[2]) || isset($v[0])) $gr = 0; elseif (isset($v[1])) $gr = 1;
                    elseif (isset($v[2])) $gr = 2;
                    if (Cfg::get('model_SC')) {
                        $d = $this->fetchAll("SELECT model_id,gr FROM cc_model WHERE (NOT LD)" . ($gr > 0 ? "AND(gr='$gr')" : ''), MYSQLI_ASSOC);
                        $fi = 0;
                        foreach ($d as $v1) {
                            $fi++;
                            CC_ModelSC::modelUpdate($v1['model_id']);
                        }
                        echo " (gr=$gr. Обновлено $fi моделей)";
                    }
                    echo "\n";
                    break;
            }
        }
        echo "[CC_Ctrl.execCacheTasks()]:: done.\n";
        $s = ob_get_contents();
        ob_flush();
        if($upd){
            $no['dt_exec'] = time();
            Data::set('cc_cache_tasks', serialize($no));
        }
    }

    function write_res($field, $gr, $replica_title = '')
    {
        if (!isset($this->tWidth82rewrite)) $this->tWidth82rewrite = Data::get('tWidth82rewrite');
        $ff = @fopen(Cfg::_get('root_path') . '/' . Cfg::get('res_dir') . '/' . $field . '_' . $gr, 'wt');
        $s = array();
        if ($ff === false) return false;
        $asort = false;
        $field0 = $field;
        $field = preg_replace("/(r_|nor_)(P[0-9]{1})/u", "$2", $field);
        if ($this->qnum())
            while ($this->next() !== false) {
                switch ($field) {
                    case 'brands':
                    case 'r_brands':
                    case 'nor_brands':
                        if ($this->qrow['name'] != '') $s[$this->qrow['brand_id']] = ($field == 'brands' && $this->qrow['replica'] == 1 ? ($replica_title . ' ') : '') . Tools::unesc($this->qrow['name']);
                        break;
                    case 'brands_sname':
                    case 'r_brands_sname':
                    case 'nor_brands_sname':
                        if ($this->qrow['name'] != '')
                            $s[$this->qrow['brand_id']] = array(
                                'name' => ($field == 'brands_sname' && $this->qrow['replica'] == 1 ? ($replica_title . ' ') : '') . Tools::unesc($this->qrow['name']),
                                'sname' => Tools::unesc($this->qrow['sname']),
                                'pos' => $this->qrow['pos']
                            );
                        break;
                    case 'P4x6':
                        $s[] = Tools::unesc($this->qrow[$field]);
                        break;
                    case 'P7':
                        if ($gr == 1 && $this->qrow[$field] != '') $s[] = Tools::unesc($this->qrow[$field]); elseif ($gr == 2) $s[] = Tools::unesc($this->qrow[$field]);
                        break;
                    case 'P2':
                        if ($gr == 1)
                            if ($this->qrow[$field] == 0 && !empty($this->tWidth82rewrite)) $s[] = $this->tWidth82rewrite;
                            elseif ($this->qrow[$field] != 0) $s[] = $this->qrow[$field];
                            else;
                        elseif ($gr == 2) $s[] = $this->qrow[$field];
                        $asort = true;
                        break;
                    default:
                        if (($this->qrow[$field] != 0 || $field == 'P1' && $gr = 1) && $this->qrow[$field] != '') $s[] = Tools::unesc($this->qrow[$field]);
                        $asort = true;
                        break;
                }
            }
        if ($asort) asort($s);
        if (fwrite($ff, serialize($s)) === false) echo '[CC_Ctrl.write_res()]:: Ошибка записи в файл. Обратитесь к администратору!';
        fclose($ff);
        return true;
    }


    function update_base($do = '')
    {
        if(class_exists('App_App')){
            $app=new App_CC_Actions();
            if(method_exists($app, 'cacheTask_updateSizes')){
                echo '[CC_Actions.cacheTask_updateSizes]: ';
                $res=call_user_func([$app, 'cacheTask_updateSizes'], [0=>1]);
                echo implode(', ',$res);
                echo ".<br>";
            }
            if(method_exists($app, 'cacheTask_updatePrices')){
                echo '[CC_Actions.cacheTask_updatePrices]: ';
                $res=call_user_func([$app, 'cacheTask_updatePrices'], [0=>1]);
                echo implode(', ',$res);
                echo ".<br>";
            }
            if(method_exists($app, 'cacheTask_updateBrands')){
                echo '[CC_Actions.cacheTask_updateBrands]: ';
                $res=call_user_func([$app, 'cacheTask_updateBrands'], [0=>1]);
                echo implode(', ',$res);
                echo ".<br>";
            }
            if(method_exists($app, 'cacheTask_updateModelSC')){
                echo '[CC_Actions.cacheTask_updateBrands]: ';
                $res=call_user_func([$app, 'cacheTask_updateModelSC'], [0=>1]);
                echo implode(', ',$res);
                echo ".<br>";
            }
        }
        if(class_exists('App_App')){
            $app=new App_CC_Actions();
        }

        $replica_title = Data::get('cc_replica_title');
        if ($replica_title == '') $replica_title = 'Replica';
        echo 'Обновление фильтров ';
        $this->que('filter', 'P1', 1, 1, Cfg::get('cc_filters_sc_not_zero'));
        $this->write_res('P1', 1, 1);
        echo '.';
        Tools::flu();
        $this->que('filter', 'P2', 1, 1, Cfg::get('cc_filters_sc_not_zero'));
        $this->write_res('P2', 1, 1);
        echo '.';
        Tools::flu();
        $this->que('filter', 'P3', 1, 1, Cfg::get('cc_filters_sc_not_zero'));
        $this->write_res('P3', 1, 1);
        echo '.';
        Tools::flu();
        $this->que('filter', 'P7', 1, 1, Cfg::get('cc_filters_sc_not_zero'));
        $this->write_res('P7', 1, 1);
        echo '.';
        Tools::flu();
        $this->que('brands_join', 1, 1, '', '', Cfg::get('cc_filters_sc_not_zero'));
        $this->write_res('brands', 1, 1);
        $this->first();
        $this->write_res('brands_sname', 1, 1);
        echo '.';
        Tools::flu();
        $this->que('filter', 'P1', 2, 1, Cfg::get('cc_filters_sc_not_zero'));
        $this->write_res('P1', 2);
        echo '.';
        Tools::flu();
        $this->que('filter', 'P1', 2, 1, Cfg::get('cc_filters_sc_not_zero'), 1);
        $this->write_res('r_P1', 2, 1);
        echo '.';
        Tools::flu();
        $this->que('filter', 'P1', 2, 1, Cfg::get('cc_filters_sc_not_zero'), 0);
        $this->write_res('nor_P1', 2, 1);
        echo '.';
        Tools::flu();
        $this->que('filter', 'P2', 2, 1, Cfg::get('cc_filters_sc_not_zero'));
        $this->write_res('P2', 2, 1);
        echo '.';
        Tools::flu();
        $this->que('filter', 'P2', 2, 1, Cfg::get('cc_filters_sc_not_zero'), 1);
        $this->write_res('r_P2', 2, 1);
        echo '.';
        Tools::flu();
        $this->que('filter', 'P2', 2, 1, Cfg::get('cc_filters_sc_not_zero'), 0);
        $this->write_res('nor_P2', 2, 1);
        echo '.';
        Tools::flu();
        $this->que('filter', 'P3', 2, 1, Cfg::get('cc_filters_sc_not_zero'));
        $this->write_res('P3', 2, 1);
        echo '.';
        Tools::flu();
        $this->que('filter', 'P3', 2, 1, Cfg::get('cc_filters_sc_not_zero'), 1);
        $this->write_res('r_P3', 2, 1);
        echo '.';
        Tools::flu();
        $this->que('filter', 'P3', 2, 1, Cfg::get('cc_filters_sc_not_zero'), 0);
        $this->write_res('nor_P3', 2, 1);
        echo '.';
        Tools::flu();
        $this->que('filter', 'P4', 2, 1, Cfg::get('cc_filters_sc_not_zero'));
        $this->write_res('P4', 2, 1);
        echo '.';
        Tools::flu();
        $this->que('filter', 'P4', 2, 1, Cfg::get('cc_filters_sc_not_zero'), 1);
        $this->write_res('r_P4', 2, 1);
        echo '.';
        Tools::flu();
        $this->que('filter', 'P4', 2, 1, Cfg::get('cc_filters_sc_not_zero'), 0);
        $this->write_res('nor_P4', 2, 1);
        echo '.';
        Tools::flu();
        $this->que('filter', 'P5', 2, 1, Cfg::get('cc_filters_sc_not_zero'));
        $this->write_res('P5', 2, 1);
        echo '.';
        Tools::flu();
        $this->que('filter', 'P5', 2, 1, Cfg::get('cc_filters_sc_not_zero'), 1);
        $this->write_res('r_P5', 2, 1);
        echo '.';
        Tools::flu();
        $this->que('filter', 'P5', 2, 1, Cfg::get('cc_filters_sc_not_zero'), 0);
        $this->write_res('nor_P5', 2, 1);
        echo '.';
        Tools::flu();
        $this->que('filter', 'P6', 2, 1, Cfg::get('cc_filters_sc_not_zero'));
        $this->write_res('P6', 2, 1);
        echo '.';
        Tools::flu();
        $this->que('filter', 'P6', 2, 1, Cfg::get('cc_filters_sc_not_zero'), 1);
        $this->write_res('r_P6', 2, 1);
        echo '.';
        Tools::flu();
        $this->que('filter', 'P6', 2, 1, Cfg::get('cc_filters_sc_not_zero'), 0);
        $this->write_res('nor_P6', 2, 1);
        echo '.';
        Tools::flu();

        $this->que('P46concat', 1, Cfg::get('cc_filters_sc_not_zero'), 0, Data::get('cc_LZexclude'));
        $this->write_res('P4x6', 2);
        echo '.';
        Tools::flu();
        $this->que('P46concat', 1, Cfg::get('cc_filters_sc_not_zero'), 1, Data::get('cc_LZexclude'));
        $this->write_res('r_P4x6', 2);
        echo '.';
        Tools::flu();
        $this->que('P46concat', 1, Cfg::get('cc_filters_sc_not_zero'), 0, Data::get('cc_LZexclude'));
        $this->write_res('nor_P4x6', 2);
        echo '.';
        Tools::flu();

        $this->que('brands_join', 2, 1, '', '', Cfg::get('cc_filters_sc_not_zero'));
        $this->write_res('brands', 2, $replica_title);
        $this->first();
        $this->write_res('brands_sname', 2, $replica_title);
        $this->que('brands_join', 2, 1, "AND(cc_brand.replica=1)", '', Cfg::get('cc_filters_sc_not_zero'));
        $this->write_res('r_brands', 2, 1, $replica_title);
        $this->first();
        $this->write_res('r_brands_sname', 2, 1, $replica_title);
        $this->que('brands_join', 2, 1, "AND(cc_brand.replica!=1)", '', Cfg::get('cc_filters_sc_not_zero'));
        $this->write_res('nor_brands', 2, $replica_title);
        $this->first();
        $this->write_res('nor_brands_sname', 2, $replica_title);
        echo 'ОК<br>';

        $cc1 = new CC_Ctrl;
        $cc = new CC_Ctrl;
        $cc->query("SELECT cc_cat.sc, cc_model.P1 AS MP1, cc_model.P3 AS MP3, cc_model.P2 AS MP2, cc_cat.scprice, cc_cat.cprice, cc_brand.extra_b, cc_brand.brand_id, cc_brand.name AS bname, cc_model.name, cc_model.class_id, cc_model.mspez_id, cc_model.model_id, cc_model.suffix AS msuffix, cc_cat.suffix AS csuffix, cc_model.text, cc_model.img1, cc_model.img2, cc_cat.cat_id, cc_cat.P1+'0' AS P1, cc_cat.P2+'0' AS P2, cc_cat.P3+'0' AS P3, cc_cat.P4, cc_cat.P5+'0' AS P5, cc_cat.P6+'0' AS P6, cc_cat.P7, cc_cat.H, cc_cat.gr, cc_cat.bprice, cc_cat.cur_id, cc_model.sup_id FROM cc_brand RIGHT JOIN (cc_cat LEFT JOIN cc_model ON cc_cat.model_id = cc_model.model_id) ON cc_brand.brand_id = cc_model.brand_id WHERE (NOT cc_model.LD)AND(NOT cc_cat.LD)AND(NOT cc_brand.LD) AND NOT cc_cat.fixPrice");
        $u = 0;
        $zero = 0;
        $me = array();
        $fi = 0;
        echo 'Обновляем цены...<span id=p>0</span>%';
        Tools::flu();
        $ppp = 0;
        if ($cc->qnum()) while ($cc->next() != false) {
            $fi++;
            $pp = round($fi * 100 / $cc->qnum);
            if (($pp / 10) == round($pp / 10) && $pp != $ppp) {
                echo "<script>document.getElementById('p').innerHTML='$pp';</script>";
                $ppp = $pp;
                Tools::flu();
            }
            if ($cc->qrow['gr'] == 1) {
                $pb = $cc->extra_price($cc->qrow['bprice'], $cc->qrow['cur_id'], $cc->qrow['P1'], $cc->qrow['sup_id'], $cc->qrow['brand_id'], $cc->qrow['extra_b'], $cc->qrow['gr'], $cc->qrow['MP1']);
                if ($pb == 0) $zero++;
                if ($pb != $cc->qrow['cprice']) if (!$cc1->query("UPDATE cc_cat SET cprice='$pb' WHERE cat_id='{$cc->qrow['cat_id']}'")) die('Ошибка обновления прайсов');
                else {
                    $u++;
                    $me[$cc->qrow['model_id']] = 1;
                }
            } else {
                $pb = $cc->extra_price($cc->qrow['bprice'], $cc->qrow['cur_id'], $cc->qrow['P5'], $cc->qrow['sup_id'], $cc->qrow['brand_id'], $cc->qrow['extra_b'], $cc->qrow['gr']);
                if ($pb == 0) $zero++;
                if ($pb != $cc->qrow['cprice']) if (!$cc1->query("UPDATE cc_cat SET cprice='$pb' WHERE cat_id='{$cc->qrow['cat_id']}'")) die('Ошибка обновления прайсов');
                else {
                    $u++;
                    $me[$cc->qrow['model_id']] = 1;
                }
            }
        }
        echo ' ОК: ' . $u . ' цен. Из них ' . $zero . ' нулевых.<br>';
        if (isset($this->intPrice)) {
            $fi = 0;
            if (count($me)) {
                echo 'Обновляем интевалы цен (' . count($me) . ' моделей)...<span id=it>0</span>% ';
                Tools::flu();
                $ppp = 0;
                foreach ($me as $k => $v) {
                    $fi++;
                    $pp = round($fi * 100 / count($me));
                    if (($pp / 10) == round($pp / 10) && $pp != $ppp) {
                        echo "<script>document.getElementById('it').innerHTML='$pp';</script>";
                        $ppp = $pp;
                        Tools::flu();
                    }
                    $this->intPrice->modelUpdate($k);
                }
                echo "OK. <br>";
            }
        }



        return (true);
    }

    function load_sup($gr = 0)
    {
        $cc = new DB;
        if (!$gr) $cc->query("SELECT * FROM cc_sup ORDER BY name");
        else $cc->QUERY("SELECT DISTINCT cc_sup.name, cc_sup.sup_id FROM cc_model RIGHT JOIN cc_sup ON cc_model.sup_id = cc_sup.sup_id WHERE (cc_model.gr='$gr')AND(cc_model.LD<>1) ORDER BY cc_sup.name");
        $this->sup_arr = array();
        $this->sup_arr[0] = '';
        if ($cc->qnum()) while ($cc->next() !== false) $this->sup_arr[$cc->qrow['sup_id']] = Tools::unesc($cc->qrow['name']);
        unset($cc);
    }

    function sname_cat($cat_id = 0, $sname = '', $snameForceUpdate = false)
    {
        /* 
            Если cat_id==0 используется текущий $this->qrow
            Если sname=='' 
                Если sname_в_базе!='' 
                    Если snameForceUpdate==true то пересчет sname в размере
                    Если snameForceUpdate==false просто прверяем на дубли
                Если sname_в_базе==''	- автоматически формируется
            Если sname!='' то
                Если snameForceUpdate==true то пересчет sname в размере
                Если snameForceUpdate==false просто прверяем на дубли
            Если sname==''	- автоматически формируется
        */


        if (!$cat_id && (!@$this->qrow['gr'] || !@$this->qrow['cat_id'] || !@$this->qrow['model_id'] || !isset($this->qrow['bname']) || !isset($this->qrow['name']) || !isset($this->qrow['sname']) || !isset($this->qrow['P1']) || !isset($this->qrow['P2']) || !isset($this->qrow['P3']) || !isset($this->qrow['P4']) || !isset($this->qrow['P5']) || !isset($this->qrow['P6']) || !isset($this->qrow['P7']) || !isset($this->qrow['suffix']))) {
            echo '[sname_cat()]: Не задан cat_id. ';
            return false;
        }
        $cc = new CC_Ctrl;
        if (!$cat_id) $cc->qrow = $this->qrow;
        if (!$cat_id) {
            $cc->qrow = $this->qrow;
            $sname = $cc->qrow['sname'];
        }
        $res = true;
        $sname = trim($sname);
        if ($cat_id) {
            $cc->query("SELECT cc_cat.*, cc_brand.name AS bname, cc_model.name, cc_model.P1 AS MP1, cc_model.P3 AS MP3, cc_model.P2 AS MP2 FROM cc_cat INNER JOIN (cc_model INNER JOIN cc_brand ON cc_model.brand_id = cc_brand.brand_id) ON cc_cat.model_id=cc_model.model_id WHERE (cc_cat.cat_id='$cat_id')");
            $cc->next();
        }

        if (!$cat_id || $cc->qnum()) {
            if ($sname == '' || $snameForceUpdate) {
                if ($cc->qrow['gr'] == 1) $tpl = Cfg::get('SNAME_CAT_TPL1') != '' ? Cfg::get('SNAME_CAT_TPL1') : '$C'; else $tpl = Cfg::get('SNAME_CAT_TPL2') != '' ? Cfg::get('SNAME_CAT_TPL2') : '$C';
                $tpl = $tpl . (Cfg::get('SNAME_CAT_SUFFIX') ? "-{$cc->qrow['suffix']}" : '');
                $tpl = str_replace('#B', Tools::unesc($cc->qrow['bname']), $tpl);
                $tpl = str_replace('#M', Tools::unesc($cc->qrow['name']), $tpl);
                $tpl = str_replace('#P1', $cc->qrow['P1'] * 1, $tpl);
                $tpl = str_replace('#P2', $cc->qrow['P2'] * 1, $tpl);
                $tpl = str_replace('#P3', $cc->qrow['P3'] * 1, $tpl);
                $tpl = str_replace('#P4', $cc->qrow['P4'], $tpl);
                $tpl = str_replace('#P5', $cc->qrow['P5'] * 1, $tpl);
                $tpl = str_replace('#Z', ($cc->qrow['P6'] * 1) == 1 ? 'Z' : '', $tpl);
                $tpl = str_replace('#P6', $cc->qrow['P6'] * 1, $tpl);
                $tpl = str_replace('#P7', $cc->qrow['P7'], $tpl);
                $tpl = str_replace('#C', sprintf("%u\n", crc32("{$cc->qrow['P1']}{$cc->qrow['P2']}{$cc->qrow['P3']}{$cc->qrow['P4']}{$cc->qrow['P5']}{$cc->qrow['P6']}{$cc->qrow['P7']}{$cc->qrow['suffix']}")), $tpl);
                $af = App_TFields::get('cc_cat');
                foreach ($af as $k => &$v) $tpl = str_replace('#' . $k, $cc->qrow[$k], $tpl);
                $sname = Tools::str2iso($tpl, Cfg::get('SNAME_CAT_LEN'), Cfg::get('SNAME_CAT_REG'));
            } else
                $sname = Tools::str2iso($sname, Cfg::get('SNAME_CAT_LEN'), Cfg::get('SNAME_CAT_REG'));

            $is = '';
            $sname0 = $sname;
            $gr = $cc->qrow['gr'];
            $cat_id = $cc->qrow['cat_id'];
            $model_id = $cc->qrow['model_id'];

            if (Cfg::get('SNAME_CAT_DAREA'))
                do {
                    $sname = Tools::esc($sname0 . ($is != '' ? "_$is" : ''));
                    $sname1 = Tools::like($sname);
                    $cc->query("SELECT count(cat_id) FROM cc_cat JOIN cc_model USING (model_id) JOIN cc_brand USING (brand_id) WHERE cc_cat.gr='{$gr}' AND cc_cat.sname LIKE '$sname1' AND NOT cc_model.LD AND NOT cc_brand.LD AND NOT cc_cat.LD AND cat_id!='{$cat_id}'" . (Cfg::get('SNAME_CAT_DAREA') == 2 ? " AND model_id='{$model_id}'" : ''));
                    $cc->next(MYSQL_NUM);
                    if ($cc->qrow[0]) $is++;
                } while ($cc->qrow[0]);

            if (!$cc->query("UPDATE cc_cat SET sname='$sname' WHERE cat_id='{$cat_id}'")) {
                $res = false;
                $fres = 'Ошибка записи';
            }

        } else {
            $res = false;
            $fres = 'Не найдена модель';
        }
        unset($cc, $ss);
        if (!$res) echo '[sname_cat()]: ' . $fres . '. ';
        return !$res ? $res : $sname;
    }

    function sname_model($model_id = 0, $sname = '', $snameForceUpdate = false)
    {
        /* 
            Если model_id==0 используется текущий $this->qrow
            Если sname=='' 
                Если sname_в_базе!='' 
                    Если snameForceUpdate==true то пересчет sname в модели
                    Если snameForceUpdate==false просто прверяем на дубли и корректность
                Если sname_в_базе==''	- автоматически формируется
            Если sname!='' то
                Если snameForceUpdate==true то пересчет sname в модели 
                Если snameForceUpdate==false просто прверяем на дубли и корректность
            Если sname==''	- автоматически формируется
        */

        if (!$model_id && (!@$this->qrow['gr'] || !@$this->qrow['model_id'] || !isset($this->qrow['bname']) || !isset($this->qrow['name']) || !isset($this->qrow['sname']))) {
            echo '[sname_model()]: Не задан model_id. ';
            return false;
        }
        $cc = new CC_Ctrl;
        if (!$model_id) {
            $cc->qrow = $this->qrow;
            $sname = $cc->qrow['sname'];
        }
        $res = true;
        $sname = trim($sname);
        if ($model_id) {
            $cc->query("SELECT cc_model.*, cc_brand.name AS bname, cc_brand.replica FROM cc_model INNER JOIN cc_brand ON cc_model.brand_id = cc_brand.brand_id WHERE (cc_model.model_id='$model_id') LIMIT 1");
            $cc->next();
        }
        if (!$model_id || $cc->qnum()) {
            if ($sname == '' || $snameForceUpdate) {
                $B = Tools::unesc($cc->qrow['bname']);
                $M = Tools::unesc($cc->qrow['name']);
                $tpl = Cfg::get('SNAME_MODEL_TPL') != '' ? Cfg::get('SNAME_MODEL_TPL') : '$M';
                $tpl = $tpl . (Cfg::get('SNAME_MODEL_SUFFIX') ? "-{$cc->qrow['suffix']}" : '');
                $sname = Tools::str2iso(str_replace('#B', $B, str_replace('#M', $M, $tpl)), Cfg::get('SNAME_MODEL_LEN'), Cfg::get('SNAME_MODEL_REG'));
            } else
                $sname = Tools::str2iso($sname, Cfg::get('SNAME_MODEL_LEN'), Cfg::get('SNAME_MODEL_REG'));

            $is = '';
            $sname0 = $sname;
            $gr = $cc->qrow['gr'];
            $model_id = $cc->qrow['model_id'];
            $brand_id = $cc->qrow['brand_id'];

            if (Cfg::get('SNAME_MODEL_DAREA'))
                do {
                    $sname = Tools::esc($sname0 . ($is != '' ? "_$is" : ''));
                    $sname1 = Tools::like_($sname);
                    $cc->query("SELECT count(model_id) FROM cc_model JOIN cc_brand USING (brand_id) WHERE cc_model.gr='{$gr}' AND cc_model.sname LIKE '$sname1' AND NOT cc_model.LD AND NOT cc_brand.LD AND model_id!='{$model_id}'" . (Cfg::get('SNAME_MODEL_DAREA') == 2 ? " AND cc_model.brand_id='{$brand_id}'" : ''));
                    $cc->next(MYSQL_NUM);
                    if ($cc->qrow[0]) $is++;
                } while ($cc->qrow[0]);

            if (!$cc->query("UPDATE cc_model SET sname='$sname' WHERE model_id='{$model_id}'")) {
                $res = false;
                $fres = 'Ошибка записи';
            }

            if ($res && $cc->updatedNum()) {
                if (mb_strpos(Cfg::get('SNAME_CAT_TPL1'), '#M') !== false || mb_strpos(Cfg::get('SNAME_CAT_TPL2'), '#M') !== false) {

                    $cc->query("SELECT cc_cat.*, cc_brand.name AS bname, cc_model.name FROM cc_cat INNER JOIN (cc_model INNER JOIN cc_brand ON cc_model.brand_id = cc_brand.brand_id) ON cc_cat.model_id=cc_model.model_id WHERE (NOT cc_cat.LD) AND (cc_cat.model_id='{$model_id}')");

                    if ($cc->qnum())
                        while ($cc->next() !== false) {
                            $cc->sname_cat($cc->qrow['cat_id'], '', true);
                        }
                }
            }
        } else {
            $res = false;
            $fres = 'Не найдена модель';
        }
        unset($cc);
        if (!$res) echo '[sname_model()]: ' . $fres . '. ';
        return !$res ? $res : $sname;
    }

    function sname_brand($brand_id = 0, $sname = '', $snameForceUpdate = false)
    {
        /*
            Если brand_id==0 используется текущий $this->qrow
            Если sname==''
                Если sname_в_базе!=''
                    Если snameForceUpdate==true то пересчет sname в бренде
                    Если snameForceUpdate==false просто прверяем на дубли
                Если sname_в_базе==''	- автоматически формируется
            Если sname!='' то
                Если snameForceUpdate==true то пересчет sname в бренде
                Если snameForceUpdate==false просто прверяем на дубли
            Если sname==''	- автоматически формируется
        */

        if (!$brand_id && (!@$this->qrow['gr'] || !@$this->qrow['brand_id'] || !isset($this->qrow['name']))) {
            echo '[sname_brand()]: Не задан brand_id. ';
            return false;
        }
        $cc = new CC_Ctrl;
        if (!$brand_id) $cc->qrow = $this->qrow;
        if (!$brand_id) {
            $cc->qrow = $this->qrow;
            $sname = $cc->qrow['sname'];
        }
        $res = true;
        $sname = trim($sname);
        if ($brand_id) {
            $cc->query("SELECT cc_brand.* FROM cc_brand WHERE (cc_brand.brand_id='$brand_id')");
            $cc->next();
        }
        if (!$brand_id || $cc->qnum()) {
            if ($sname == '' || $snameForceUpdate) {
                $B = Tools::unesc($cc->qrow['name']);
                $tpl = Cfg::get('SNAME_BRAND_TPL') != '' ? Cfg::get('SNAME_BRAND_TPL') : '$B';
                $sname = Tools::str2iso(str_replace('#B', $B, $tpl), Cfg::get('SNAME_BRAND_LEN'), Cfg::get('SNAME_BRAND_REG'));
            } else
                $sname = Tools::str2iso($sname, Cfg::get('SNAME_BRAND_LEN'), Cfg::get('SNAME_BRAND_REG'));

            $brand_id = $cc->qrow['brand_id'];
            $is = '';
            $sname0 = $sname;
            $gr = $cc->qrow['gr'];

            if (Cfg::get('SNAME_BRAND_DAREA'))
                do {
                    $sname = Tools::esc($sname0 . ($is != '' ? "_$is" : ''));
                    $sname1 = Tools::like_($sname);
                    $cc->query("SELECT count(brand_id) FROM cc_brand WHERE (gr='$gr')AND(sname LIKE '$sname1')AND(NOT LD)AND(brand_id!='{$brand_id}')");
                    $cc->next(MYSQL_NUM);
                    if ($cc->qrow[0]) $is++;
                } while ($cc->qrow[0]);

            if (!$cc->query("UPDATE cc_brand SET sname='$sname' WHERE brand_id='{$brand_id}'")) {
                $res = false;
                $fres = 'Ошибка записи';
            }

            if ($res && $cc->updatedNum()) {
                if (mb_strpos(Cfg::get('SNAME_MODEL_TPL'), '#B') !== false) {
                    $cc->query("SELECT cc_model.*, cc_brand.brand_id, cc_brand.name AS bname, cc_brand.replica, cc_brand.sname AS brand_sname FROM cc_model INNER JOIN cc_brand ON cc_model.brand_id = cc_brand.brand_id WHERE (NOT cc_model.LD) AND (cc_model.brand_id='{$brand_id}')");
                    if ($cc->qnum()) while ($cc->next() !== false) $cc->sname_model($cc->qrow['model_id'], '', true);
                }
                if (mb_strpos(Cfg::get('SNAME_CAT_TPL1'), '#M') === false && mb_strpos(Cfg::get('SNAME_CAT_TPL1'), '#B') !== false || mb_strpos(Cfg::get('SNAME_CAT_TPL2'), '#M') === false && mb_strpos(Cfg::get('SNAME_CAT_TPL2'), '#B') !== false) {

                    $cc->query("SELECT cc_cat.*, cc_brand.name AS bname, cc_model.name FROM cc_cat INNER JOIN (cc_model INNER JOIN cc_brand ON cc_model.brand_id = cc_brand.brand_id) ON cc_cat.model_id=cc_model.model_id WHERE (NOT cc_cat.LD) AND (cc_model.brand_id='{$brand_id}')");

                    if ($cc->qnum())
                        while ($cc->next() !== false)
                            $cc->sname_cat($cc->qrow['cat_id'], '', true);
                }
            }

        } else {
            $res = false;
            $fres = 'Не найден бренд';
        }
        unset($cc, $ss);
        if (!$res) echo '[sname_brand()]: ' . $fres . '. ';
        return !$res ? $res : $sname;
    }

    function model_ae($mode, $r)
    {
        $uf = array();
        if (isset($r['gr'])) $uf['gr'] = (int)$r['gr'];
        if (isset($r['model_id'])) $uf['model_id'] = (int)$r['model_id'];
        if (isset($r['brand_id'])) $uf['brand_id'] = (int)$r['brand_id'];
        if (isset($r['sup_id'])) $uf['sup_id'] = (int)$r['sup_id'];
        if (isset($r['mspez_id'])) $uf['mspez_id'] = (int)$r['mspez_id'];
        if (isset($r['class_id'])) $uf['class_id'] = (int)$r['class_id'];
        if (isset($r['hit_quant'])) $uf['hit_quant'] = (int)$r['hit_quant'];
        if (isset($r['name'])) $uf['name'] = Tools::esc($r['name']);
        if (isset($r['sname'])) $uf['sname'] = Tools::esc($r['sname']);
        if (isset($r['alt'])) $uf['alt'] = Tools::esc($r['alt']);
        if (isset($r['P1'])) $uf['P1'] = Tools::esc($r['P1']);
        if (isset($r['P2'])) $uf['P2'] = Tools::esc($r['P2']);
        if (isset($r['P3'])) $uf['P3'] = Tools::esc($r['P3']);
        if (isset($r['text'])) $uf['text'] = Tools::untaria($r['text']);
        if (isset($r['suffix'])) $uf['suffix'] = Tools::esc($r['suffix']);
        if (isset($r['sticker_id'])) $uf['sticker_id'] = Tools::esc($r['sticker_id']);
        if (isset($r['video_link'])) $uf['video_link'] = Tools::esc($r['video_link']);

        if (isset($r['is_seo'])) $uf['is_seo'] = (int)($r['is_seo']);
        if (isset($r['seo_h1'])) $uf['seo_h1'] = Tools::esc($r['seo_h1']);
        if (isset($r['seo_title'])) $uf['seo_title'] = Tools::esc($r['seo_title']);
        if (isset($r['seo_keywords'])) $uf['seo_keywords'] = Tools::esc($r['seo_keywords']);
        if (isset($r['seo_description'])) $uf['seo_description'] = Tools::esc($r['seo_description']);


        $gr = (int)@$r['gr'];
        $model_id = (int)@$r['model_id'];
        $res = false;
        $this->fres_msg = '';
        $brand_id0 = 0;

        if ($mode == 'add' && !$gr) return $this->putMsg(false, '[CC_ctrl.model_ae]: не указана группа - необходимо для добавления модели');
        if ($mode == 'edit' && empty($model_id)) return $this->putMsg(false, '[CC_ctrl.model_ae]: не указано ID модели - необходимо для редактирования модели');

        if ($mode == 'edit') {
            $d = $this->getOne("SELECT model_id, brand_id, gr FROM cc_model WHERE model_id='$model_id'");
            if (@$d['model_id'] == $model_id) {
                $gr=$d['gr'];
                $brand_id0 = $d['brand_id'];
                $s = array();
                foreach ($uf as $k => $v) $s[] = "$k='$v'";
                $s = implode(',', $s);
                $a = App_TFields::DBupdate('cc_model', @$r['af'], 'all', 1, $s != '' ? ',' : '', 0);
                if ($res = $this->query("UPDATE cc_model SET {$s}{$a} WHERE model_id='$model_id'")) {
                    if ($this->updatedNum()) {
                        if (isset($r['brand_id']) && $r['brand_id'] != $brand_id0) {
                            $this->extra_price_update_for_model($model_id);
                            if (isset($this->intPrice)) $this->intPrice->modelUpdate($model_id);
                        }
                    }
                } else $this->fres_msg = '[CC_Ctrl.model_ae]: ошибка DB update';
            } else $this->fres_msg = '[CC_Ctrl.model_ae]: модель не найдена';

        } elseif ($mode == 'add') {
            if(empty($gr)){
                $this->fres_msg = '[CC_Ctrl.model_ae]: Не передана группа товарная gr';
            }else{
                $uf['dt_added']=Tools::dt();
                $vals = array();
                foreach ($uf as $k => $v) $vals[] = "'$v'";
                $vals = implode(',', $vals);
                $fields = implode(',', array_keys($uf));
                $a = App_TFields::DBinsert('cc_model', @$r['af'], $gr, 1, $vals != '' ? ',' : '', 0);
                if ($res = $this->query("INSERT INTO cc_model ({$fields}{$a[0]}) VALUES({$vals}{$a[1]})")) $model_id = $this->lastId();
                else $this->fres_msg = '[CC_Ctrl.model_ae]: ошибка DB insert';
            }
        }

        if ($res) {
            $this->sname_model($model_id, @$r['sname'], (isset($r['brand_id']) && $r['brand_id'] != $brand_id0) ? true : false);

            if(!empty($r['imgFileFileld']) && !empty($_FILES[$r['imgFileFileld']]['tmp_name'])){
                $uploader=new Uploader();
                if(!$uploader->upload($r['imgFileFileld'], Uploader::$EXT_GRAPHICS)){
                    $res=false;
                    $this->fres_msg=$uploader->strMsg();
                }
            }elseif(!empty($r['spyUrl'])){
                $uploader=new Uploader();
                if(!$uploader->spyUrl($r['spyUrl'], Uploader::$EXT_GRAPHICS)){
                    $res=false;
                    $this->fres_msg=$uploader->strMsg();
                }
            } elseif(!empty($r['delImg'])){
                if(!$this->imgDelete('cc_model','model_id',$model_id, 'img2')){
                    $res=false;
                }elseif(!$this->imgDelete('cc_model','model_id',$model_id, 'img1')){
                    $res=false;
                }elseif(!$this->imgDelete('cc_model','model_id',$model_id, 'img3')){
                    $res=false;
                }
            }

            if($res && isset($uploader)){
                if(!$this->imgUpload('cc_model', $model_id, $gr, 1, $uploader->sfile)){
                    $res=false;
                }elseif(!$this->imgUpload('cc_model', $model_id, $gr, 2, $uploader->sfile)){
                    $res=false;
                }elseif(!$this->imgUpload('cc_model', $model_id, $gr, 3, $uploader->sfile)){
                    $res=false;
                }
            }

            if(isset($uploader)){
                $uploader->del();
                unset($uploader);
            }
        }
        $this->fres = $res;
        return $res;
    }

    function imgUpload($table, $id, $gr, $imgNum, $sfile, $img_field_name='')
    {
        if (Cfg::get('cc_upload_dir') == '') return $this->putMsg(false, '[CC_Ctrl.imgUpload]: cc_upload_dir not defined');
        if (!$imgNum) $this->putMsg(false, '[CC_Ctrl.imgUpload]: Номер изображения не задан');

        $sfinfo=pathinfo($sfile);
        $id=(int)$id;

        // трансформация
        $fn=Cfg::$config['root_path'].'/tmp/CC_Ctrl_imgUpload__'.$sfinfo['basename'];
        if(!@copy($sfile,$fn)){
            return $this->putMsg(false, "[CC_Ctrl.imgUpload]: Ошибка копирования ($fn)");
        }
        
        $transforms=@Cfg::$config[$table.'_img'][$gr][$imgNum]['transform'];
        if(empty($transforms)){
            return $this->putMsg(false, "[CC_Ctrl.imgUpload]: Параметры преобразования не определены ([{$table}_img'][$gr][$imgNum])");
        }

        $res=true;
        foreach($transforms as $tdata){
            switch (@$tdata['action']) {
                case 'crop':
                    if(false===($res=GD::crop($fn, $tdata['x1'], $tdata['y1'], $tdata['x2'], $tdata['y2'], @$tdata['outputFormat'], @$tdata['quality']))) break 2;
                    $fn=$res; // outputFormat может менять имя файла
                    break;
                case 'resize':
                    if(false===($res=GD::resize($tdata['method'], $fn, $tdata['w'], $tdata['h'], @$tdata['outputFormat'], @$tdata['quality']))) break 2;
                    $fn=$res;
                    break;
                default:
                    $this->putMsg(false, "[CC_Ctrl.imgUpload]: Не найден режим трансформации {$tdata['action']}");
                    @unlink($fn);
                    return false;
            }
        }
        
        if($res===false){
            $this->putMsg(false, "[CC_Ctrl.imgUpload]: Ошибка трансформации ([{$table}_img'][$gr][$imgNum][{$tdata['action']})");
            $this->putMsg(false, GD::$fres_msg, true);
            @unlink($fn);
            return false;
        }

       $sfinfo=pathinfo($fn); // здесь трансормированный файл с полным путем

        // получаем относительный путь+имя файла конечного местоположения картинки $fname  для записи этого значения в БД
        switch ($table) {
            case 'cc_brand':
                $idField='brand_id';
                if (!empty($img_field_name))
                {
                    $d = $this->getOne("SELECT brand_id AS id, name, gr, $img_field_name FROM cc_brand WHERE brand_id='$id'");
                }
                else $d = $this->getOne("SELECT brand_id AS id, name, gr, img{$imgNum} FROM cc_brand WHERE brand_id='$id'");
                if ($d === 0) {
                    @unlink($fn);
                    return $this->putMsg(false, '[CC_Ctrl.imgUpload]: Не найден бренд');
                }
                $s = Cfg::get('SNAME_BRAND_IMG_TPL');
                $s = str_replace('#B', Tools::unesc($d['name']), $s);
                $s = str_replace('#ID', Tools::unesc($d['id']), $s);
                if (!empty($img_field_name)) {
                    $s .= '_'.$img_field_name;
                }
                $s = Tools::str2iso($s, Cfg::get('SNAME_BRAND_IMG_LEN'), Cfg::get('SNAME_BRAND_IMG_REG'));
                if ($d['gr'] == 1)
                    $grs = Cfg::get('cc_tyres_subdir') . '/'; elseif ($d['gr'] == 2) $grs = Cfg::get('cc_wheels_subdir') . '/';
                else
                    $grs = 'other/';
                $fname = $grs . Cfg::get('cc_brand_subdir') . '/' . $imgNum . '/' . $s . '.' . $sfinfo['extension'];
                break;

            case 'cc_model':
                $idField='model_id';
                if (!empty($img_field_name))
                {
                    $d = $this->getOne("SELECT cc_model.model_id AS id, cc_model.name, cc_brand.name AS bname, cc_model.gr, cc_model.$img_field_name FROM cc_model INNER JOIN cc_brand ON cc_model.brand_id=cc_brand.brand_id WHERE cc_model.model_id='$id'");

                }
                else $d = $this->getOne("SELECT cc_model.model_id AS id, cc_model.name, cc_brand.name AS bname, cc_model.gr, cc_model.img{$imgNum} FROM cc_model INNER JOIN cc_brand ON cc_model.brand_id=cc_brand.brand_id WHERE cc_model.model_id='$id'");
                if ($d === 0) {
                    @unlink($fn);
                    return $this->putMsg(false, '[CC_Ctrl.imgUpload]: Не найдена модель');
                }
                $s = Cfg::get('SNAME_MODEL_IMG_TPL');
                $s = str_replace('#B', Tools::unesc($d['bname']), $s);
                $s = str_replace('#M', Tools::unesc($d['name']), $s);
                $s = str_replace('#ID', Tools::unesc($d['id']), $s);
                if (!empty($img_field_name)) {
                    $s .= '_'.$img_field_name;
                }
                $s = Tools::str2iso($s, Cfg::get('SNAME_MODEL_IMG_LEN'), Cfg::get('SNAME_MODEL_IMG_REG'));
                if ($d['gr'] == 1)
                    $grs = Cfg::get('cc_tyres_subdir') . '/'; elseif ($d['gr'] == 2) $grs = Cfg::get('cc_wheels_subdir') . '/';
                else
                    $grs = 'other/';
                $fname = $grs . Cfg::get('cc_model_subdir') . '/' . $imgNum . '/' . $s . '.' . $sfinfo['extension'];
                break;

            case 'ab_avto':
                $idField='avto_id';
                $d = $this->getOne("SELECT avto_image, vendor_id, year_id, model_id, avto_id FROM ab_avto WHERE avto_id='$id'", MYSQLI_ASSOC);
                if ($d === 0) {
                    @unlink($fn);
                    return $this->putMsg(false, '[CC_Ctrl.imgUpload]: Не найдена запись');
                }
                $d["img{$imgNum}"] = $d['avto_image']; // Костыль для удаления старых фото
                $vendor_sname = $this->getOne("SELECT sname FROM ab_avto WHERE avto_id='{$d['vendor_id']}'", MYSQLI_ASSOC);
                $year_sname   = $this->getOne("SELECT sname FROM ab_avto WHERE avto_id='{$d['year_id']}'", MYSQLI_ASSOC);
                $model_sname  = $this->getOne("SELECT sname FROM ab_avto WHERE avto_id='{$d['model_id']}'", MYSQLI_ASSOC);
                $modif_sname  = $this->getOne("SELECT sname FROM ab_avto WHERE avto_id='{$d['avto_id']}'", MYSQLI_ASSOC);
                $s = Cfg::get('SNAME_AVTO_IMG_TPL');
                $s = str_replace('#V', Tools::unesc(@$vendor_sname['sname']), $s);
                $s = str_replace('#Y', Tools::unesc(@$year_sname['sname']), $s);
                $s = str_replace('#MD', Tools::unesc(@$model_sname['sname']), $s);
                $s = str_replace('#MI', Tools::unesc(@$modif_sname['sname']), $s);
                $s = Tools::str2iso($s, Cfg::get('SNAME_AVTO_IMG_REG'), Cfg::get('SNAME_AVTO_IMG_LEN'));
                $fname =  Cfg::get('cc_avto_subdir') . '/' . $s . '.' . $sfinfo['extension'];
                break;

            default:
                @unlink($fn);
                return $this->putMsg(false, '[CC_Ctrl.imgUpload]: Неверное имя таблицы');
        }

        // перемещаем в конечную позицию
        Tools::tree_mkdir(Cfg::get('root_path') . '/' . Cfg::get('cc_upload_dir') . '/' . $fname);

        // удаляю старую
        if(!empty($d["img{$imgNum}"])) {
            @unlink(Cfg::_get('root_path') . '/' . Cfg::get('cc_upload_dir') . '/' . $d["img{$imgNum}"]);
            @unlink(Cfg::_get('root_path') . '/' . Cfg::get('cc_cache_images_dir') . '/' . $d["img{$imgNum}"]);
        }

        if(!@copy($fn, Cfg::_get('root_path') . '/' . Cfg::get('cc_upload_dir') . '/' . $fname)){
            @unlink($fn);
            return $this->putMsg(false, "[CC_Ctrl.imgUpload]: Не могу переместить временный файл в папку назначения $fn -> $fname");
        }

        // запись в базу
        if (!empty($img_field_name))
        {
            if (!$this->query("UPDATE $table SET $img_field_name='$fname' WHERE $idField='$id'")) {
                @unlink($fn);
                @unlink(Cfg::_get('root_path') . '/' . Cfg::get('cc_upload_dir') . '/' . $fname);
                return $this->putMsg(false, '[CC_Ctrl.imgUpload]: Ошибка записи в БД');
            }
        }
        else {
            if (!$this->query("UPDATE $table SET img{$imgNum}='$fname' WHERE $idField='$id'")) {
                @unlink($fn);
                @unlink(Cfg::_get('root_path') . '/' . Cfg::get('cc_upload_dir') . '/' . $fname);
                return $this->putMsg(false, '[CC_Ctrl.imgUpload]: Ошибка записи в БД');
            }
        }
        @unlink($fn);
        
        return true;
    }


    function imgDelete($table, $id, $value, $field = 'img') //удаление файла и обнуление поля
    {
        if (Cfg::get('cc_upload_dir') == '')  return $this->putMsg(false, '[CC_Ctrl.imgDelete]: cc_upload_dir not defined');

        $d=$this->getOne("SELECT $field FROM $table WHERE $id='$value'");

        if ($d!==0) {
            if ($d[$field] != ''){
                $fn=Cfg::_get('root_path') . '/' . Cfg::get('cc_cache_images_dir') . '/' . $d[$field];

                if(is_file($fn) && !@unlink($fn)){
                    return $this->putMsg(false, '[CC.imgDelete]: Файл кеша не удаляется. Запись с адресом файла в БД не очищена');
                }

                $fn=Cfg::_get('root_path') . '/' . Cfg::get('cc_upload_dir') . '/' . $d[$field];
                if (is_file($fn) && !@unlink($fn)) {
                    return $this->putMsg(false, '[CC.imgDelete]: Файл не удаляется. Запись с адресом файла в БД не очищена');
                }

                if (!$this->query("UPDATE $table SET $field='' WHERE $id='$value'")){
                    return $this->putMsg(false, '[CC.imgDelete]: Ошибка БД: Файл удален, но запись в БД о нем осталась');
                }
            }
        }

        return true;
    }

    public static function getStickersList()
    {
        $stickers = Array(
            1 => Array('desc' => 'Синий с надписью',    'img' => '/assets/images/stickers/stiker01.png', 'allow_text' => true),
            2 => Array('desc' => 'Зеленый с надписью',  'img' => '/assets/images/stickers/stiker02.png', 'allow_text' => true),
            3 => Array('desc' => 'Made in Germany',     'img' => '/assets/images/stickers/stiker03.png', 'allow_text' => false),
            4 => Array('desc' => 'Made in Italy',       'img' => '/assets/images/stickers/stiker04.png', 'allow_text' => false)
        );
        return $stickers;
    }

    public function getModelSticker($model_id)
    {
        $sticker = $this->getOne("SELECT cc_model_stickers.* FROM cc_model JOIN cc_model_stickers USING(sticker_id) WHERE model_id='$model_id'", MYSQLI_ASSOC);
        return $sticker;
    }

}
