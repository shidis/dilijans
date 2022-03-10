<?
if (!defined('true_enter')) die ("Direct access not allowed!");

// Ver 4.2.

class App_CC_CII2d extends App_CC_CII2Base
{

    function recognize($file_id, $iter, $limit, $opt, $ciSID)
    {
        $this->r=(object)[];
        $this->r->finish = false;
        $this->r->fres = true;
        $this->r->fres_msg = '';
        $this->is=(object)[];
        $this->is->ciSID = $ciSID;
        $this->is->file_id = $file_id;
        $this->cc = new CC_Ctrl();
        $this->r->logs = array(); // массив для лог-данных
        $this->db = new DB;
        if ($iter == 0) {

            if (!$this->iter1($file_id, $ciSID, $opt)) return $this->r;
            if ($this->is->gr != 2) {
                $this->r->fres = $this->putMsg(false, '[CC_CII2d]: Не правильная группа для файла id=' . $file_id);
                $this->r->fres_msg = $this->fres_msg;
                return $this->r;
            }

            if (empty($this->is->config['diaMerge'])) $this->is->diaMerge = array(); else $this->is->diaMerge = $this->is->config['diaMerge'];
            if (empty($this->is->config['svMerge'])) $this->is->svMerge = array(); else $this->is->svMerge = $this->is->config['svMerge'];

            // загружаем матрицу цветов дисков
            $this->cc->loadSMatrix(2);
            $this->is->sMatrix = $this->cc->sMatrix;
            unset($this->cc->sMatrix);


            $this->cc->load_sup(0);
            $this->is->sup = $this->cc->sup_arr;
            unset($this->cc->sup_arr);

            $this->is->blist = array(); // список брендов в файле

            $this->query("UPDATE cii_item SET cstatus=0, mstatus=0, cstatus=0 WHERE file_id='$file_id'");
            $this->query("UPDATE cii_file SET SID='{$this->is->ciSID}' WHERE file_id='$file_id'");
            if (!$this->is->opt['test']) {
                $this->query("UPDATE cc_cat SET upd_id=0 WHERE gr='{$this->is->gr}'");
                // cc_cat.upd_id для cat_id будет равен CSID если размер подвергся обработке и cc_cat_sc обновился
            }

            // поставщики
            $this->query("SELECT * FROM cc_suplr ORDER BY name");
            $this->is->suplrs = array();
            if ($this->qnum()) while ($this->next() !== false) {
                $this->is->suplrs[$this->qrow['suplr_id']] = Tools::unesc($this->qrow['name']);
            }

            $this->is->cat_id = 0;

        } else {
            if (!$this->loadSession()) return $this->r;
        }


        $this->bm = array();

        $this->r->brandName = '';

        $limit = (int)$limit;

        // USE INDEX (file_id_cstatus)
        $this->query("SELECT item_id, bstatus, mstatus, cstatus, brand, model, full_name, company, P1+'0' AS P1, P2+'0' AS P2, P3+'0' AS P3, P4+'0' AS P4, P5+'0' AS P5, P6+'0' AS P6, P7, P7_1, MP1, suffix, sklad, price1,price2,price3, cat_id, replica, sup_id FROM cii_item WHERE file_id='$file_id' AND cstatus=0 ORDER BY brand, model, P5, P2, P4, P6, P1, P3, suffix  LIMIT 0, $limit");

        if ($this->qnum()) {

            while ($this->next() !== false) {

                $this->replica = $this->cstatus = $this->mstatus = $this->bstatus = $this->brand_id = $this->model_id = 0;
                $this->tipoInserted = false;
                $this->transform = array(); // поле таблицы tSuffix - трансформации цвета и размера. Записываемые в базу значения - в ключах массива

                $brand = Tools::unesc($this->qrow['brand']);
                $model = Tools::unesc($this->qrow['model']);
                if ($this->qrow['replica']) $this->replica = 1;
                $this->sup_id = $this->qrow['sup_id'];
                $this->prepareModel(trim($brand), trim($model), $this->is->opt['replicaBrand']);

                // хак для бренда FR
                /*
                if($this->replica && $this->bm['brand']=='FR'){
                    $this->bm['brand']='FR Design';
                    $this->replica=0;
                }*/

                //$this->sup_id=$this->getSup_id();

                $this->suplr = Tools::unesc($this->qrow['company']);

                // $this->suffix - массив если шины и строка если диски
                $this->suffix = trim(Tools::cutDoubleSpaces($this->qrow['suffix']));

                if (!$this->brandExists()) return $this->r;

                if ($this->brand_id && @$this->is->brandsCfg[$this->brand_id]['priceNoUpd']) {
                    $this->cstatus=23;
                    $this->mstatus=23;
                    $this->bstatus=23;
                    $this->changeCatId(0);
                }else{

                    // находим аналоги цветов $this->iSuffixes по матрице
                    $this->iSuffixes = array($this->suffix);
                    if ($this->suffix != '') {
                        $bs = $nbs = array();
                        if (!empty($this->is->sMatrix[2]))
                            foreach ($this->is->sMatrix[2] as $sk => $sv) {
                                if (!empty($sv[$this->brand_id]['iSuffixes']) && Tools::mb_array_search($this->suffix, $sv[$this->brand_id]['iSuffixes']) !== false) $bs[] = $sk;
                                if (!empty($sv[0]['iSuffixes']) && Tools::mb_array_search($this->suffix, $sv[0]['iSuffixes']) !== false) $nbs[] = $sk;
                            }
                        // приоритет для суффиксов с указанным брендом. В идеале iSuffixes должен содержать одно значение
                        $nbs = array_diff($nbs, $bs);
                        $this->iSuffixes = array_merge($this->iSuffixes, $bs, $nbs);
                    }

                    // хак для КИК
                    // для дополнительного параметра. В базе должно быть поле cc_cat.code
                    /*			if($this->brand_id==18 && isset(App_TFields::$fields['cc_cat']['code'])){
                                if(mb_strpos($this->bm['model'],'##')!==false){
                                    $m=explode('##',$this->bm['model']);
                                    $this->bm['model']=trim($m[0]);
                                    $this->bm['code']=trim(@$m[1]);
                                }elseif(preg_match("/([^\(]+)\([КСKC]{2}([0-9]+)\)(.*)/iu",$this->bm['model'],$m)){
                                    $this->bm['model']=trim($m[1].' '.$m[3]);
                                    $this->bm['code']=trim('КС'.$m[2]);
                                }
                            }
                */
                    // хак для реплики
                    // для дополнительного параметра. В базе должно быть поле cc_cat.app
                    if ($this->replica && isset(App_TFields::$fields['cc_cat']['app'])) {
                        if (mb_strpos($this->bm['model'], '@@') !== false) {
                            $m = explode('@@', $this->bm['model']);
                            $this->bm['model'] = trim($m[0]);
                            $this->bm['app'] = trim(@$m[1]);
                        }
                    }

                    $dia=$this->qrow['P3'].'';
                    if(isset($this->is->diaMerge[$dia])){
                        $this->transform["Dia {$this->is->diaMerge[$dia]}"] = 1;
                        $this->qrow['P3']=$dia*1;
                    }

                    $sv = $this->qrow['P4'] . '*' . $this->qrow['P6'];
                    if(isset($this->is->svMerge[$sv])){
                        $this->transform["SV {$this->is->svMerge[$sv][0]}*{$this->is->svMerge[$sv][1]}"] = 1;
                        $this->qrow['P4']=$this->is->svMerge[$sv][0]*1;
                        $this->qrow['P6']=$this->is->svMerge[$sv][1]*1;
                    }

                    if (!$this->modelExists()) return $this->r;
                }

                $this->is->blist[$this->brand_id] = 1;


                // обновляем cii_item
                $aq = array();
                $aq['brand'] = Tools::esc($this->bm['brand']);
                $aq['model'] = Tools::esc($this->bm['model']);
//			if(!empty($this->bm['code'])) $aq['model'].=" ##{$this->bm['code']}";
                if (!empty($this->bm['app'])) $aq['model'] .= " @@{$this->bm['app']}";
                $aq['replica'] = $this->replica;
                $aq['sup_id'] = $this->sup_id;
                $aq['tSuffix'] = Tools::esc(implode(' ', array_keys($this->transform))); // трансформации

                if (!$this->is->opt['test']) {
                    $aq['brand_id'] = $this->brand_id;
                    $aq['model_id'] = $this->model_id;
                    $aq['cat_id'] = $this->is->cat_id;
                }

                $aq['cstatus'] = $this->cstatus;
                $aq['bstatus'] = $this->bstatus;
                $aq['mstatus'] = $this->mstatus;

                if (count($aq)) $this->db->update('cii_item', $aq, "item_id='{$this->qrow['item_id']}'");

            }
        } else $this->r->finish = true;

        if (!$this->saveSession()) return $this->r;

        if ($this->r->finish) {

            $this->changeCatId(0); // сбрасываем в базу данные по последнему типоразмеру

            unset($this->is->models, $this->is->tipos);

            if (!$this->is->opt['test']) {

                $this->finishCat();
                $this->status = 1;
                $this->cc->addCacheTask('brands pricesNoIntPrice sizes modAll', $this->is->gr);

            } else {
                $this->status = 0;
            }

            $this->finish();
        }


        return $this->r;
    }

    private function getSup_id()
    {

        // опредеяем sup_id
        // "H-" - replica H, "R[цифры]" - replica WSP, "[Буква][цифры]" - replica LS, "только цифры и слеш" - replica FR
        // идентификаторы sup_id правятся в этом коде

        return 0; // пока выключено

        if (!$this->replica) return 0;
        if (preg_match("/^H\-.*$/u", $this->bm['model'])) return 25;
        if (preg_match("/^R[0-9]+.*$/u", $this->bm['model'])) return 8;
        if (preg_match("/^[a-z]{1}[0-9]+$/iu", $this->bm['model'])) return 9;
        if (preg_match("/^[0-9\/]+$/u", $this->bm['model'])) {
            $this->bm['model'] = 'MM/u' . $this->bm['model'];
            return 11;
        }
        if (preg_match("/^FR[0-9\/]+$/u", $this->bm['model'])) return 11;
        return 0;
    }

    private function brandExists()
    {
        if (empty($this->is->brands)) {
            // кешируем бренды
            $this->cc->que('brands', $this->is->gr);
            $this->is->brands = array(0 => array(), 1 => array());
            if ($this->cc->qnum()) while ($this->cc->next() !== false) {
                $this->is->brands[$this->cc->qrow['replica'] == 0 ? 0 : 1][trim(Tools::unesc($this->cc->qrow['name']))] = $this->cc->qrow['brand_id'];
            }
        }

        foreach ($this->is->brands[$this->replica] as $kb => &$vb) {
            if (Tools::mb_strcasecmp($kb, $this->bm['brand']) == 0) {
                $this->brand_id = $vb;
                break;
            }
        }
        $dt = date("Y-m-d H:i:s");
        if ($this->brand_id) {
            if ($this->is->opt['test']) $this->bstatus = 21; else $this->bstatus = 1;
        } else {
            if (!$this->is->opt['test']) {
                $b = Tools::esc($this->bm['brand']);
                $this->db->query("INSERT INTO cc_brand (gr,name,replica,dt_added) VALUES('{$this->is->gr}','{$b}',{$this->replica},'$dt')");
                $this->brand_id = $this->db->lastId();
                if (!$this->brand_id) {
                    $this->r->fres = $this->putMsg(false, '[brandExists()]: Бренд не записан в БД');
                    $this->r->fres_msg = $this->fres_msg;
                    return false;
                }
                $this->cc->sname_brand($this->brand_id);
                $this->bstatus = 2;
                return $this->brand_id = $this->is->brands[$this->replica][$this->bm['brand']] = $this->brand_id;
            } else {
                $this->bstatus = 22;
            }
        }
        $this->r->brandName = $this->bm['brand'];
        return true;
    }

    private function modelExists()
    {

        if (empty($this->is->models) || $this->is->modelsBrand != $this->bm['brand']) {
            // кешируем модели и размеры внутри выбранного бренда
            $this->is->modelsBrand = $this->bm['brand'];
            $sql = "SELECT  cc_model.name, cc_model.model_id, cc_model.suffix AS msuffix FROM cc_model WHERE cc_model.gr='{$this->is->gr}' AND cc_model.brand_id='{$this->brand_id}' AND NOT cc_model.LD";
            $this->db->query($sql);
            $this->is->models = array();
            if ($this->db->qnum()) while ($this->db->next() !== false) {
                $m = trim(Tools::unesc($this->db->qrow['name']));
                $msuffix = Tools::cutDoubleSpaces(trim(Tools::unesc($this->db->qrow['msuffix'])));
                $this->is->models[$this->db->qrow['model_id']] = array('m' => $m, 's' => $msuffix);
            }
            // размеры
            $sql = "SELECT SQL_NO_CACHE cc_cat.ignoreUpdate, cc_cat.cat_id, cc_cat.upd_id, cc_cat.sc, cc_model.sup_id, cc_model.name, cc_model.P1 AS M1, cc_model.P2 AS M2, cc_model.P3 AS M3, cc_model.model_id, cc_model.suffix AS msuffix, cc_cat.suffix AS csuffix, cc_cat.cat_id, cc_cat.P1+'0' AS P1, cc_cat.P2+'0' AS P2, cc_cat.P3+'0' AS P3, cc_cat.P4+'0' AS P4, cc_cat.P5+'0' AS P5, cc_cat.P6+'0' AS P6, cc_cat.P7, cc_cat.bprice, cc_cat.cur_id FROM cc_cat INNER JOIN cc_model ON cc_cat.model_id = cc_model.model_id WHERE cc_cat.gr='{$this->is->gr}' AND cc_model.brand_id='{$this->brand_id}' AND NOT cc_cat.LD AND NOT cc_model.LD";
            $this->db->query($sql);
            $this->is->tipos = array();
            if ($this->db->qnum()) while ($this->db->next() !== false) {
                $csuffix = Tools::cutDoubleSpaces(trim(Tools::unesc($this->db->qrow['csuffix'])));
                $this->is->tipos[$this->db->qrow['model_id']][$this->db->qrow['cat_id']] = array(
                    'P1' => $this->db->qrow['P1'], //ET
                    'P2' => $this->db->qrow['P2'], //J
                    'P3' => $this->db->qrow['P3'], // DIA
                    'P4' => $this->db->qrow['P4'], // дырки
                    'P5' => $this->db->qrow['P5'], // R
                    'P6' => $this->db->qrow['P6'], // DCO
                    's' => $csuffix
                );
                // уже обработанный размер
                if ($this->db->qrow['upd_id'] == $this->is->ciSID) $this->is->tipos[$this->db->qrow['model_id']][$this->db->qrow['cat_id']]['u'] = true;
                if ($this->db->qrow['ignoreUpdate']) $this->is->tipos[$this->db->qrow['model_id']][$this->db->qrow['cat_id']]['iu'] = true;
            }
        }

        $model_ids = array();
        $mss = array();

        $dt = date("Y-m-d H:i:s");

        // 1. проверяем сначала наличие модель+суффикс, если есть, то провеяем в ней размер+суффикс, если нет размера+суффикс то помещаем в модель+суффикс размер+суффикс, если нет модель+суффикс, то п.2
        // 2. если нет модель+суффикс, то проверяем наличие модель-без-суффикса и в ней размер+суффикс, если нет размер+суффикс, то добавляем размер+суффикс, если нет модели-без-суффикса, то добавляем модель и в нее добавляем размер+суффикс
        // для сравнения DIA в размерах используется таблица $this->is->diam для поиска аналогов
        // параметры Не учитывающиесмя при сравнении: тип диска


        // ищем модель+суффикс
        foreach ($this->is->models as $km => &$vm) {
            if (strcasecmp($vm['m'], $this->bm['model']) == 0 && ($im = Tools::mb_array_search($vm['s'], $this->iSuffixes)) !== false) {
                $model_ids[] = $km; // ищем все подходящие модели
                $mss[] = $im;
            }
        }

        // ищем размер
        if (!empty($model_ids)) { // есть модель(и)

            if ($this->is->opt['test']) $this->mstatus = 21; else $this->mstatus = 1;

            foreach ($model_ids as $kmid => $mid) {

                if (!empty($this->is->tipos[$mid])) foreach ($this->is->tipos[$mid] as $kt => &$vt) {
                    $eq = 1;

                    $eq1 = 0;
                    // поиск по матрице цветов включая ситуацию когда vs['s']=='' && $this->suffix==''
                    if (($im = Tools::mb_array_search($vt['s'], $this->iSuffixes)) !== false) {
                        $eq1 = 1;
                        //в нулевой позиции iSuffixes стоит базовый суффикс
                        if ($im > 0) $this->transform[$this->iSuffixes[$im]] = 1; else $this->transform['T:DIRECT'] = 1;
                    }
                    if (!$eq1) $eq = 0;

                    if ($vt['P1'] != $this->qrow['P1']) $eq = 0;
                    if ($vt['P2'] != $this->qrow['P2']) $eq = 0;
                    if ($vt['P3'] != $this->qrow['P3']) $eq = 0;
                    if ($vt['P5'] !== $this->qrow['P5']) $eq = 0;
                    if ($vt['P4'] != $this->qrow['P4']) $eq = 0;
                    if ($vt['P6'] != $this->qrow['P6']) $eq = 0;

                    if ($eq) { // размеры равны
                        if ($mss[$kmid] > 0) $this->transform['M:' . $this->iSuffixes[$mss[$kmid]]] = 1; else $this->transform['M:DIRECT'] = 1;
                        $this->model_id = $mid;
                        $this->changeCatId($kt);
                        $this->pushSCT();
                        if (empty($vt['iu'])) {
                            if ($this->is->opt['test']) $this->cstatus = 21; else $this->cstatus = 1;
                        } else {
                            if ($this->is->opt['test']) $this->cstatus = 23; else $this->cstatus = 3;
                        }
                        break 2;
                    }
                }
            }
            if ($this->model_id == 0) { // нет размера
                $this->model_id = array_shift($model_ids); // берем первую подходящую модель
                if ($mss[$kmid] > 0) $this->transform['M:' . $this->iSuffixes[$mss[$kmid]]] = 1; else $this->transform['M:DIRECT'] = 1;
                if (!$this->is->opt['test']) {
                    // добавляем размер
                    $cid = $this->tipoInsert();
                    if ($cid === false) return false;
                    $this->changeCatId($cid);
                    $this->cstatus = 2;
                    $this->pushSCT();
                } else {
                    $this->cstatus = 22;
                    $this->makeTSuffix();
                }
            }

        } else { // нет модели+суффикс
            // ищем модель без суффикса
            foreach ($this->is->models as $km => &$vm) {
                if (strcasecmp($vm['m'], $this->bm['model']) == 0 && $vm['s'] == '') {
                    $model_ids[] = $km; // ищем все подходящие модели
                }
            }
            if (!empty($model_ids)) { // есть модель(и)


                if ($this->is->opt['test']) $this->mstatus = 21; else $this->mstatus = 1;

                foreach ($model_ids as $mid) {

                    if (!empty($this->is->tipos[$mid])) foreach ($this->is->tipos[$mid] as $kt => &$vt) {
                        $eq = 1;

                        $eq1 = 0;
                        // поиск по матрице цветов включая ситуацию когда vs['s']=='' && $this->suffix==''
                        if (($im = Tools::mb_array_search($vt['s'], $this->iSuffixes)) !== false) {
                            $eq1 = 1;
                            //в нулевой позиции iSuffixes стоит базовый суффикс
                            if ($im > 0) $this->transform['T:' . $this->iSuffixes[$im]] = 1; else $this->transform['T:DIRECT'] = 1;
                        }
                        if (!$eq1) $eq = 0;

                        if ($vt['P1'] != $this->qrow['P1']) $eq = 0;
                        if ($vt['P2'] != $this->qrow['P2']) $eq = 0;
                        if ($vt['P3'] != $this->qrow['P3']) $eq = 0;
                        if ($vt['P5'] !== $this->qrow['P5']) $eq = 0;
                        if ($vt['P4'] != $this->qrow['P4']) $eq = 0;
                        if ($vt['P6'] != $this->qrow['P6']) $eq = 0;


                        if ($eq) { // размеры равны
                            $this->model_id = $mid;
                            $this->changeCatId($kt);
                            $this->pushSCT();
                            if (empty($vt['iu'])) {
                                if ($this->is->opt['test']) $this->cstatus = 21; else $this->cstatus = 1;
                            } else {
                                if ($this->is->opt['test']) $this->cstatus = 23; else $this->cstatus = 3;
                            }
                            break 2;
                        }
                    }
                }
                if ($this->model_id == 0) { // нет размера
                    $this->model_id = array_shift($model_ids); // берем первую подходящую модель
                    if (!$this->is->opt['test']) {
                        // добавляем размер
                        $cid = $this->tipoInsert();
                        if ($cid === false) return false;
                        $this->changeCatId($cid);
                        $this->cstatus = 2;
                        $this->pushSCT();
                    } else {
                        $this->cstatus = 22;
                        $this->makeTSuffix();
                    }
                }


            } else { // нет модели без суффикса
                if (!$this->is->opt['test']) {
                    $this->mstatus = 2;
                    $this->cstatus = 2;
                    // добавляем модель
                    $this->db->insert('cc_model', array(
                        'brand_id' => $this->brand_id,
                        'gr' => 2,
                        'sup_id' => $this->sup_id,
                        'name' => Tools::esc($this->bm['model']),
                        'P1' => $this->qrow['MP1'],
                        'dt_added' => $dt
                    ));
                    $this->model_id = $this->db->lastId();
                    if (!$this->model_id) return false;
                    $this->cc->sname_model($this->model_id);
                    $this->is->models[$this->model_id] = array('m' => $this->bm['model'], 's' => '');
                    // добавляем размер
                    $cid = $this->tipoInsert();
                    if ($cid === false) return false;
                    $this->changeCatId($cid);
                    $this->pushSCT();
                } else {
                    $this->mstatus = 22;
                    $this->cstatus = 22;
                    $this->makeTSuffix();
                }
            }

        }


        return true;

    }

    private function tipoInsert()
    {

        $dt = date("Y-m-d H:i:s");

        $suf = $this->makeTSuffix();

        $this->db->insert('cc_cat', array(
            'model_id' => $this->model_id,
            'gr' => 2,
            'P1' => $this->qrow['P1'],
            'P2' => $this->qrow['P2'],
            'P3' => $this->qrow['P3'],
            'P4' => $this->qrow['P4'],
            'P5' => $this->qrow['P5'],
            'P6' => $this->qrow['P6'],
            'suffix' => Tools::esc($suf),
            'upd_id' => $this->is->ciSID,
            'dt_added' => $dt
        ));
        $cat_id = $this->db->lastId();
        $this->tipoInserted = true;
        if (!$cat_id) {
            $this->r->fres = $this->putMsg(false, '[tipoInsert()]: Размер не записан в БД');
            $this->r->fres_msg = $this->fres_msg;
            return false;
        }
        $this->cc->sname_cat($cat_id);
        $this->is->tipos[$this->model_id][$cat_id] = array(
            'P1' => $this->qrow['P1'],
            'P2' => $this->qrow['P2'],
            'P3' => $this->qrow['P3'],
            'P4' => $this->qrow['P4'],
            'P5' => $this->qrow['P5'],
            'P6' => $this->qrow['P6'],
            's' => $suf,
            'u' => true
        );
        return $cat_id;
    }

    private function makeTSuffix()
    {

        if ($this->suffix != '') {
            // добавляем размер с суффиксом со вторым  значением из матрицы цветов
            if (!empty($this->iSuffixes[1])) {
                $suf = $this->iSuffixes[1];
                $this->transform['T:' . $this->iSuffixes[1]] = 1;
            } else {
                $suf = $this->suffix;
            }
        } else $suf = '';

        return $suf;
    }

    private function prepareModel($brand, $model, $replicaBrand = 'Replica')
    {
        $this->bm = array();
        if ($replicaBrand == '' || $this->replica) return $this->bm = array('brand' => $brand, 'model' => $model);
        $ex = preg_split("~[;,]~u", $replicaBrand);
        foreach ($ex as $rb) {
            $rb = trim(Tools::cutDoubleSpaces($rb));
            if ($rb != '') {
                if (Tools::mb_strcasecmp($rb, trim($brand)) == 0) {
                    preg_match("/([^\(]+)\((.+)\)(.*)/u", $model, $m);
                    if (count($m) == 4) {
                        $this->replica = 1;
                        $this->bm = array('brand' => Tools::cutDoubleSpaces(trim($m[1])), 'model' => Tools::cutDoubleSpaces(trim($m[2])));
                        if (isset(App_TFields::$fields['cc_cat']['app']))
                            $this->bm['app'] = Tools::cutDoubleSpaces(trim($m[3]));
                        else $this->bm['model'] .= ' ' . Tools::cutDoubleSpaces(trim($m[3]));

                        // определяем поставщика для брендовой реплики
                        foreach ($this->is->sup as $k => $v)
                            if (Tools::mb_strcasecmp($brand, $v) == 0) {
                                $this->sup_id = $k;
                                break 1;
                            }

                        return 1;
                    }
                }
            }
        }
        return $this->bm = array('brand' => $brand, 'model' => $model);
    }

    function __construct()
    {
        parent::__construct();
    }


}