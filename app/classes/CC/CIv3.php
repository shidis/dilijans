<?
if (!defined('true_enter')) die ("Direct access not allowed!");

// ver 3.0  CAT_IMPORT_MODE==1

class App_CC_CIv3 extends App_CC_CI
{

    function recognize($file_id, $start, $limit, $opt)
    {
        $r=(object)[];
        $r->finish = false;
        $r->fres = true;
        $r->fres_msg = '';
        $file = $this->getOne("SELECT gr,col_model,param FROM ci_file WHERE file_id='$file_id'");
        if ($file === 0) {
            $r->fres = $this->putMsg(false, 'Не найден файл id=' . $file_id);
            $r->fres_msg = $this->fres_msg;
            return $r;
        }
        if (@$file['gr'] == 0) {
            $r->fres = $this->putMsg(false, 'Не присвоена группа для файла id=' . $file_id);
            $r->fres_msg = $this->fres_msg;
            return $r;
        }
        $param = unserialize(Tools::unesc($file['param']));
        if (!isset($param['CM'])) {
            $r->fres = $this->putMsg(false, 'Не распознанная структура файла.');
            $r->fres_msg = $this->fres_msg;
            return $r;
        } else $this->CM = $param['CM'];
        if (@$opt['check']) $r->start = $start; else $start = $r->start = 0;
        $r->limit = $limit;
        $this->query("SELECT item_id,ft FROM ci_item WHERE file_id='$file_id' AND (cstatus=0 OR cstatus>=20) AND item_id!='{$param['header_item_id']}' AND cstatus!=40 AND mstatus!=40 AND bstatus!=40 ORDER BY item_id LIMIT $start,$limit", MYSQL_ASSOC);
        // весь список на анализ
        $pa = $tiBrands = $tiModels = $tiCat = array();
        if ($this->qnum())
            while ($this->next() !== false) {
                $ft = unserialize(Tools::unesc($this->qrow['ft']));
                $sys_code = (int)$ft[$this->CM['Код TyreIndex']];
                $IDBRAND = (int)$ft[$this->CM['Код бренда']];
                $IDMODEL = (int)$ft[$this->CM['Код модели']];
                if ($sys_code) {
                    if ($param['gr'] == 2) $pa[$this->qrow['item_id']] = array('replica' => $this->isReplica(@$ft[$this->CM['Бренд']], @$ft[$this->CM['Модель']], @$opt['replicaBrand']));
                    else $pa[$this->qrow['item_id']] = array();
                    if ($this->replica !== false) $pa[$this->qrow['item_id']]['replica'] = $this->replica;
                    /*
                                if($this->replica===false)
                                    $tiBrandsN[$this->qrow['item_id']]=$ft[$this->CM['Код бренда']];
                                else
                                    $tiBrandsN[$this->qrow['item_id']]=$this->replica['brand'];
                    */
                    $tiBrands[$this->qrow['item_id']] = $IDBRAND;
                    $tiModels[$this->qrow['item_id']] = $IDMODEL;
                    $tiCat[$this->qrow['item_id']] = $sys_code;
                    foreach ($this->CM as $k => $v) {
                        $pa[$this->qrow['item_id']]['cell'][$k] = isset($ft[$v]) ? trim(Tools::esc($ft[$v])) : '';
                    }
                }
            } else {
            $r->finish = true;
            $r->fres = $this->putMsg(true, 'Больше нет строк в файле.');
            $r->fres_msg = $this->fres_msg;
            return $r;
        }
        $cc = new CC_Ctrl();
        $paOk = array();
        $bupd = $mupd = array();

        if (!empty($opt['tyresSuffixes'])) {
            $tyresSuffixes = preg_split("/[,;]/u", $opt['tyresSuffixes']);
            foreach ($tyresSuffixes as $k => &$suf) {
                $suf = trim($suf);
                $suf = str_replace('/', '\/', $suf);
                $suf = str_replace("\\", "\\\\", $suf);
            }
        }

        if (count($pa)) {
            $d = $this->fetchAll("SELECT DISTINCTROW cc_cat.ti_id, cc_cat.cat_id, cc_model.model_id, cc_brand.brand_id,  cc_cat.sc, cc_cat.cprice, cc_model.name AS mname, cc_brand.name AS bname, cc_model.ti_id AS IDMODEL, cc_brand.ti_id AS IDBRAND  FROM (cc_cat INNER JOIN cc_model ON cc_cat.model_id = cc_model.model_id) INNER JOIN cc_brand ON cc_model.brand_id = cc_brand.brand_id WHERE (cc_cat.gr='{$file['gr']}')AND(NOT cc_model.LD)AND(NOT cc_brand.LD)AND(NOT cc_cat.LD)AND(cc_cat.ti_id IN (" . join(',', array_values($tiCat)) . "))");
            foreach ($d as $v) {
                $cstatus = $bstatus = $mstatus = 0;
                $item_id = array_search($v['ti_id'], $tiCat);
                $IDBRAND = $tiBrands[$item_id];
                $IDMODEL = $tiModels[$item_id];
                // проверка на соответствет кода модели и бренда и если нет его,  то предупреждение и обновдение цен иостатков
                //if($v['IDBRAND']!=$IDBRAND) $mstatus=@$opt['check']?23:3;
                if ($v['IDMODEL'] != $IDMODEL) $cstatus = @$opt['check'] ? 23 : 3;
                if ($bstatus == 0) $bstatus = @$opt['check'] ? 24 : 4;
                if ($mstatus == 0) $mstatus = @$opt['check'] ? 24 : 4;
                if ($cstatus == 0) $cstatus = @$opt['check'] ? 24 : 4;
                // подготавливаем данные для записи в ci_item
                $brand_id = $v['brand_id'];
                $model_id = $v['model_id'];
                $cat_id = $v['cat_id'];
                $iv = array();
                foreach ($this->CMI[$file['gr']] as $k => $v1) if (isset($pa[$item_id]['cell'][$k])) {
                    switch ($v1['type']) {
                        case 'integer':
                            $iv[$v1['item_field']] = intval(str_replace(',', '.', $pa[$item_id]['cell'][$k]));
                            break;
                        case 'float':
                        case 'price':
                            $iv[$v1['item_field']] = floatval(str_replace(',', '.', $pa[$item_id]['cell'][$k]));
                            break;
                        case 'id':
                            $iv[$v1['item_field']] = @$v1['list'][$pa[$item_id]['cell'][$k]];
                            break;
                        case 'string':
                        default:
                            $iv[$v1['item_field']] = $pa[$item_id]['cell'][$k];
                            break;
                    }
                }
                if ($param['gr'] == 2 && @$pa[$item_id]['replica'] !== false) {
                    $iv['brand'] = $pa[$item_id]['replica']['brand'];
                    $iv['model'] = $pa[$item_id]['replica']['model'];
                    $iv['replica'] = 1;
                }
                if (!empty($tyresSuffixes) && isset($iv['full_name'])) {
                    $sr = array();
                    foreach ($tyresSuffixes as $suf) {
                        $suf = trim($suf);
                        //if($suf!='' && (mb_stripos($iv['full_name']," $suf")!==false || mb_stripos($iv['full_name']," $suf ")!==false)) $sr[]=$suf;
                        if ($suf != '' && preg_match("/\w\s{$suf}(\z|\s)/iu", $iv['full_name']) === 1) $sr[] = $suf;
                    }
                    $iv['suffix'] = trim($iv['suffix'] . ' ' . implode(' ', $sr));
                }
                if (!@$opt['check']) {
                    $a = array('ti_file_id' => $file_id);
                    if (@$opt['hideOff']) $a['H'] = 0;
                    if (@$opt['updatePrices'] && isset($iv['price'])) {
                        $a['bprice'] = $iv['price'];
                        $a['cur_id'] = 1;
                    }
                    if (@$opt['updateStock'] && isset($iv['sklad'])) $a['sc'] = $iv['sklad'];
                    if ($param['gr'] == 1 && @$opt['updateTyresSuffix'] && isset($iv['suffix'])) {
                        $a['suffix'] = $iv['suffix'];
                    }
                    $this->update('cc_cat', $a, "cat_id='$cat_id'");
                    if (!isset($bupd[$brand_id])) {
                        $a = array('ti_file_id' => $file_id);
                        if (@$opt['hideOff']) $a['H'] = 0;
                        $this->update('cc_brand', $a, "brand_id='$brand_id'");
                        $bupd[$brand_id] = 1;
                    }
                    if (!isset($mupd[$model_id])) {
                        $a = array('ti_file_id' => $file_id);
                        if (@$opt['hideOff']) $a['H'] = 0;
                        $this->update('cc_model', $a, "model_id='$model_id'");
                        $mupd[$model_id] = 1;
                    }
                } // check==1
                $iv = array_merge($iv, array('cstatus' => $cstatus, 'bstatus' => $bstatus, 'mstatus' => $mstatus, 'cat_id' => $cat_id, 'brand_id' => $brand_id, 'model_id' => $model_id));
                $this->update('ci_item', $iv, "item_id='$item_id'");
                $paOk[$item_id] = '';
            }
            // вторая часть! Парсинг оставшихся данных (не связанных размеры)
            $pa = array_diff_key($pa, $paOk);
            if (count($pa)) {
                foreach ($pa as $item_id => &$cell) {
                    $cstatus = $bstatus = $mstatus = 0;
                    $cv = $mv = $bv = array(); // массивы сетов для cc_cat & cc_model && cc_brand
                    // подготавливаем данные сетов для ci_item
                    $iv = array();
                    foreach ($this->CMI[$file['gr']] as $k => $v) if (isset($pa[$item_id]['cell'][$k])) {
                        switch ($v['type']) {
                            case 'integer':
                                $iv[$v['item_field']] = intval(str_replace(',', '.', $cell['cell'][$k]));
                                break;
                            case 'float':
                            case 'price':
                                $iv[$v['item_field']] = floatval(str_replace(',', '.', $cell['cell'][$k]));
                                break;
                            case 'id':
                                $iv[$v['item_field']] = @$v['list'][$cell['cell'][$k]];
                                break;
                            case 'string':
                            default:
                                $iv[$v['item_field']] = $cell['cell'][$k];
                                break;
                        }
                    }
                    if ($param['gr'] == 2 && $cell['replica'] !== false) {
                        $iv['brand'] = $cell['replica']['brand'];
                        $iv['model'] = $cell['replica']['model'];
                        $iv['replica'] = 1;
                    }

                    // ищем бренд
                    $brand_id = 0;
                    $cat_id = 0;
                    $model_id = 0;
                    $d = $this->fetchAll("SELECT name, brand_id, ti_id,alt FROM cc_brand WHERE (NOT LD)AND(gr='{$file['gr']}')" . ($param['gr'] == 2 && $cell['replica'] !== false ? "AND(replica=1)" : ''));
                    foreach ($d as $v)
                        if (($param['gr'] != 2 || @$cell['replica'] === false) &&
                            (Tools::mb_strcasecmp($v['name'], $cell['cell']['Бренд']) == 0 || Tools::mb_strcasecmp($v['alt'], $cell['cell']['Бренд']) == 0)
                            ||
                            ($param['gr'] == 2 && $cell['replica'] !== false) &&
                            (Tools::mb_strcasecmp($v['name'], $cell['replica']['brand']) == 0 || Tools::mb_strcasecmp($v['alt'], $cell['replica']['brand']) == 0)
                        ) {
                            $brand_id = $v['brand_id'];
                            $bstatus = 1; // привязка
                            break;
                        }
                    if (!$brand_id) { // добавляем бренд
                        $bstatus = 2;
                    }
                    /*
                    if(!@$opt['check'] && $bstatus==4 && !isset($bupd[$brand_id])){  // для реплики условие не сработает никогда
                        $this->update('cc_brand',array('ti_file_id'=>$file_id,'H'=>0),"brand_id='$brand_id'");
                        $bupd[$brand_id]=1;
                    }
                    */
                    // ищем модель внутри бренда
                    if ($brand_id) {
                        $mc = Tools::like($cell['cell']['Модель']);
                        if ($param['gr'] == 2 && $cell['replica'] !== false) {
                            $mc = Tools::like($cell['replica']['model']);
                        }
                        $d = $this->fetchAll("SELECT DISTINCT cc_model.name, cc_model.ti_id, cc_model.alt, cc_model.model_id, cc_model.brand_id  FROM cc_model INNER JOIN cc_brand ON cc_model.brand_id = cc_brand.brand_id WHERE (NOT cc_model.LD)AND(NOT cc_brand.LD)AND(cc_model.gr='{$file['gr']}')AND((cc_model.ti_id='{$cell['cell']['Код модели']}')OR(cc_model.name LIKE '{$mc}' OR cc_model.alt LIKE '{$mc}'))AND(cc_model.brand_id='$brand_id')");
                        foreach ($d as $v)
                            if ($cell['cell']['Код модели'] == $v['ti_id'])
                                if ($v['brand_id'] == $brand_id && $brand_id) {
                                    $model_id = $v['model_id'];
                                    $mstatus = 4; // обновление модели
                                    break;
                                } elseif ($brand_id) {
                                    $mstatus = 3; // возможно перемещение модели в другой бренд
                                    $model_id = $v['model_id'];
                                }
                        if (!$model_id)
                            foreach ($d as $v)
                                if (($param['gr'] != 2 || @$cell['replica'] === false) &&
                                    Tools::mb_strcasecmp($v['name'], $cell['cell']['Модель']) == 0 || Tools::mb_strcasecmp($v['alt'], $cell['cell']['Модель']) == 0
                                    ||
                                    $param['gr'] == 2 && $cell['replica'] !== false &&
                                    Tools::mb_strcasecmp($v['name'], $cell['replica']['model']) == 0 || Tools::mb_strcasecmp($v['alt'], $cell['replica']['model']) == 0
                                ) {
                                    $model_id = $v['model_id'];
                                    $mstatus = 1; // привязка модели
                                    break;
                                }
                    }
                    if (!$model_id) {
                        $mstatus = 2; //добавляем модель
                    }
                    if (!@$opt['check'] && $mstatus == 4 && !isset($mupd[$model_id])) {
                        $this->update('cc_model', array('ti_file_id' => $file_id, 'H' => 0), "model_id='$model_id'");
                        $mupd[$model_id] = 1;
                    }
                    foreach ($this->CMI[$file['gr']] as $k => $v) if (isset($cell['cell'][$k])) {
                        switch ($v['type']) {
                            case 'integer':
                                $vv = intval(str_replace(',', '.', $cell['cell'][$k]));
                                break;
                            case 'float':
                            case 'price':
                                $vv = floatval(str_replace(',', '.', $cell['cell'][$k]));
                                break;
                            case 'id':
                                $vv = intval(@$v['list'][$cell['cell'][$k]]);
                                break;
                            case 'string':
                            default:
                                $vv = Tools::cutDoubleSpaces(trim($cell['cell'][$k]));
                                break;
                        }
                        switch ($file['gr']) {
                            case '1':
                                switch ($v['item_field']) {
                                    case 'sys_code':
                                        $cv['ti_id'] = $vv;
                                        break;
                                    case 'IDBRAND':
                                        $bv['ti_id'] = $vv;
                                        break;
                                    case 'IDMODEL':
                                        $mv['ti_id'] = $vv;
                                        break;
                                    case 'brand':
                                        $bv['name'] = $vv;
                                        break;
                                    case 'model':
                                        $mv['name'] = $vv;
                                        break;
                                    case 'P3':
                                        $cv['P3'] = $vv;
                                        break;
                                    case 'P2':
                                        $cv['P2'] = $vv;
                                        break;
                                    case 'P1':
                                        $cv['P1'] = $vv;
                                        break;
                                    case 'P7':
                                        $cv['P7'] = $vv;
                                        break;
                                    case 'P7_1':
                                        if ($vv == 'ZR') $cv['P6'] = 1; else $cv['P7'] .= $vv;
                                        break;
                                    case 'suffix':
                                        $cv['suffix'] = $vv;
                                        break;
                                    case 'MP1':
                                        $mv['P1'] = $vv;
                                        break;
                                    case 'MP3':
                                        $mv['P3'] = $vv;
                                        break;
                                    case 'MP2':
                                        $mv['P2'] = $vv;
                                        break;
                                    case 'sklad':
                                        $cv['sc'] = $vv;
                                        break;
                                    case 'price':
                                        $cv['bprice'] = $vv;
                                        break;
                                }
                                break;
                            case '2':
                                switch ($v['item_field']) {
                                    case 'sys_code':
                                        $cv['ti_id'] = $vv;
                                        break;
                                    case 'IDBRAND':
                                        $bv['ti_id'] = $vv;
                                        break;
                                    case 'IDMODEL':
                                        $mv['ti_id'] = $vv;
                                        break;
                                    case 'brand':
                                        if ($param['gr'] == 2)
                                            if ($cell['replica'] === false) $bv['name'] = $vv;
                                            else {
                                                $bv['name'] = $cell['replica']['brand'];
                                                $bv['replica'] = 1;
                                            }
                                        else $bv['name'] = $vv;
                                        break;
                                    case 'model':
                                        if ($param['gr'] == 2)
                                            if ($cell['replica'] === false) $mv['name'] = $vv;
                                            else $mv['name'] = $cell['replica']['model'];
                                        else $mv['name'] = $vv;
                                        break;
                                    case 'P2':
                                        $cv['P2'] = $vv;
                                        break;
                                    case 'P5':
                                        $cv['P5'] = $vv;
                                        break;
                                    case 'P4':
                                        $cv['P4'] = $vv;
                                        break;
                                    case 'P4_1': /*----PCD double----*/
                                        break;
                                    case 'P6':
                                        $cv['P6'] = $vv;
                                        break;
                                    case 'P1':
                                        $cv['P1'] = $vv;
                                        break;
                                    case 'P3':
                                        $cv['P3'] = $vv;
                                        break;
                                    case 'suffix':
                                        $cv['suffix'] = $vv;
                                        break;
                                    case 'MP1':
                                        $mv['P1'] = $vv;
                                        break;
                                    case 'sklad':
                                        $cv['sc'] = $vv;
                                        break;
                                    case 'price':
                                        $cv['bprice'] = $vv;
                                        break;
                                }
                        }
                    }
                    if ($file['gr'] == 1) {
                        $cv['P4'] = $cc->isCinSuffix($cv['P7']);
                    }
                    $cv['gr'] = $mv['gr'] = $bv['gr'] = $file['gr'];
                    if ($model_id) {
                        $mv['brand_id'] = $brand_id;
                        $cv['model_id'] = $model_id;
                    }
                    $suffix = array();
                    if (!empty($tyresSuffixes) && isset($iv['full_name'])) {
                        foreach ($tyresSuffixes as $suf) {
                            if ($suf != '' && preg_match("/\w\s{$suf}(\z|\s)/iu", $iv['full_name']) === 1) $suffix[] = $suf;
                        }
                        $iv['suffix'] = trim($iv['suffix'] . ' ' . implode(' ', $suffix));
                    }
                    //print_r($suffix);
                    // проверка наличия размера
                    if ($brand_id && $model_id) {
                        $w = array();
                        if (@$cv['suffix'] != '') $suffix = array_merge($suffix, explode(' ', $cv['suffix']));
                        foreach ($suffix as $v) {
                            $v = Tools::like($v);
                            $w[] = "((cc_cat.suffix LIKE '$v')OR(cc_cat.suffix LIKE '$v %')OR(cc_cat.suffix LIKE '% $v %')OR(cc_cat.suffix LIKE '% $v'))";
                        }
                        foreach (array_diff_key($cv, array('ti_id' => '', 'gr' => '', 'sc' => '', 'bprice' => '', 'suffix' => '')) as $k => $v) {
                            $v = Tools::like($v);
                            $w[] = "(cc_cat.$k = '{$v}')";
                        }
                        $w = join('AND', $w);
                        $d = $this->getOne("SELECT DISTINCT cc_cat.cat_id,cc_cat.ignoreUpdate FROM (cc_cat INNER JOIN cc_model ON cc_cat.model_id = cc_model.model_id) INNER JOIN cc_brand ON cc_model.brand_id = cc_brand.brand_id WHERE (cc_cat.gr='{$file['gr']}')AND(NOT cc_model.LD)AND(NOT cc_brand.LD)AND(NOT cc_cat.LD)AND(cc_cat.ti_id=0)AND($w)");
                        if ($d !== 0) {
                            $cat_id = $d['cat_id'];
                            if ($d['ignoreUpdate']) $cstatus = 5; // пропуск
                            else $cstatus = 1; //привязка
                        } else {
                            $cstatus = 2; //добавление
//							echo $this->sql_query;
//							return;
                        }
                    } else $cstatus = 2; // добавление
                    // запись данных
                    if (@$opt['check']) {
                        $cstatus += 20;
                        $mstatus += 20;
                        $bstatus += 20;
                    } else {
                        // делаем бренд
                        if (!isset($bupd[$brand_id]) || !$brand_id) {
                            //$a=array('ti_id'=>$tiBrands[$item_id],'ti_file_id'=>$file_id);
                            $a = array('ti_file_id' => $file_id);
                            if (@$opt['hideOff']) $a['H'] = 0;
                            $bv = array_merge($bv, $a);
                            if ($bstatus == 1 || $bstatus == 4) $this->update('cc_brand', $a, "brand_id='$brand_id'");
                            elseif ($bstatus == 2) {
                                $this->insert('cc_brand', $bv);
                                $brand_id = $this->lastId();
                                $cc->sname_brand($brand_id, '', false);
                            }
                        }
                        if ($brand_id) {
                            $mv['brand_id'] = $brand_id;
                            $bupd[$brand_id] = 1;
                        }
                        // делаем модель
                        if (!isset($mupd[$model_id]) || !$model_id) {
                            $a = array('ti_id' => $tiModels[$item_id], 'ti_file_id' => $file_id);
                            if (@$opt['hideOff']) $a['H'] = 0;
                            $mv = array_merge($mv, $a);
                            if ($mstatus == 1 || $mstatus == 4 || $mstatus == 3) $this->update('cc_model', $a, "model_id='$model_id'");
                            elseif ($mstatus == 2) {
                                $this->insert('cc_model', $mv);
                                $model_id = $this->lastId();
                                $cc->sname_model($model_id, '', false);
                            }
                        }
                        if ($model_id) {
                            $cv['model_id'] = $model_id;
                            $mupd[$model_id] = 1;
                        }
                        // делаем размер
                        $a = array('ti_id' => $tiCat[$item_id], 'ti_file_id' => $file_id);
                        if ($cstatus != 5) {
                            if (@$opt['hideOff']) $a['H'] = 0;
                            if (@$opt['updatePrices'] && isset($cv['bprice'])) {
                                $a['bprice'] = $cv['bprice'];
                                $a['cur_id'] = 1;
                            }
                            if (@$opt['updateStock'] && isset($cv['sc'])) $a['sc'] = $cv['sc'];
                            $cv = array_merge($cv, $a);
                        }
                        if ($cstatus == 1 || $cstatus == 4 || $cstatus == 5) $this->update('cc_cat', $a, "cat_id='$cat_id'");
                        elseif ($cstatus == 2) {
                            $this->insert('cc_cat', array_merge($cv, $a));
                            $cat_id = $this->lastId();
                            $cc->sname_cat($cat_id);
                        }
                    }
                    //неизвестная проблема
                    if ($cstatus == 0) $cstatus = @$opt['check'] ? 26 : 6;
                    if ($mstatus == 0) $mstatus = @$opt['check'] ? 26 : 6;
                    if ($bstatus == 0) $bstatus = @$opt['check'] ? 26 : 6;

                    $iv = array_merge($iv, array('cstatus' => $cstatus, 'bstatus' => $bstatus, 'mstatus' => $mstatus, 'cat_id' => $cat_id, 'brand_id' => $brand_id, 'model_id' => $model_id));
                    $this->update('ci_item', $iv, "item_id='$item_id'");

                } // end $pa iteration
            } // конец второй части
        } else { // else все строки без кода тайриндекс
            $r->finish = true;
            $r->fres = $this->putMsg(true, 'Больше нет строк с кодом');
            $r->fres_msg = $this->fres_msg;
            return $r;
        }
        $r->fres = $this->fres;
        $r->fres_msg = $this->fres_msg;
        return $r;
    }

    private function isReplica($brand, $model, $replicaBrand = 'Replica')
    {
        if ($replicaBrand == '') return false;
        $ex = preg_split("[;,]", $replicaBrand);
        foreach ($ex as $rb) {
            $rb = trim(Tools::cutDoubleSpaces($rb));
            if ($rb != '') {
                if (Tools::mb_strcasecmp($rb, trim($brand)) == 0) {
                    preg_match("/([^\(]+)\((.+?)\)(.*)/u", $model, $m);
                    if (count($m) == 4) {
                        $this->replica = array('brand' => Tools::cutDoubleSpaces(trim($m[1])), 'model' => Tools::cutDoubleSpaces(trim($m[2] . $m[3])));
                        return true;
                    }
                }
            }
        }
        $this->replica = false;
        return false;
    }

    function __construct()
    {
        parent::__construct();
    }


}

?>