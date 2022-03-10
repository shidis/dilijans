<?
if (!defined('true_enter')) die ("Direct access not allowed!");

// ver.1.0    CAT_IMPORT_MODE==1

class App_CC_CI extends App_CC_CIBase
{

    function linkTipo($item_id, $linkItems)
    { // linkItems цепляем к item_id
        $r = (object)[];
        $r->fres = true;
        $r->fres_msg = '';
        $item_id = (int)$item_id;
        foreach ($linkItems as $k => &$v) $v = (int)$v;
        $file_id = $this->getOne("SELECT file_id,ft FROM ci_item WHERE item_id='$item_id'");
        if ($file_id == 0) {
            $r->fres = $this->putMsg(false, 'Не найден файл');
            $r->fres_msg = $this->fres_msg;
            return $r;
        }
        $ft_to = unserialize(Tools::unesc($file_id['ft']));
        $this->file_id = $file_id = $file_id['file_id'];
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
        $this->gr = $file['gr'];
        $param = unserialize(Tools::unesc($file['param']));
        if (!isset($param['CM'])) {
            $r->fres = $this->putMsg(false, 'Не распознанная структура файла.');
            $r->fres_msg = $this->fres_msg;
            return $r;
        } else $this->CM = $param['CM'];
        $sys_code_to = $ft_to[$this->CM['Код TyreIndex']];
        $name_to = Tools::esc($ft_to[$this->CM['Код TyreIndex']] . ': ' . $ft_to[$this->CM['Полный размер']]);
        $c = $this->getOne("SELECT count(tipo_id) FROM ci_tipo WHERE sys_code='$sys_code_to'");
        if ($c[0]) {
            $r->fres = $this->putMsg(false, 'ID=' . $item_id . ' уже есть в таблице связей');
            $r->fres_msg = $this->fres_msg;
            return $r;
        }
        $d = $this->fetchAll("SELECT ft,item_id FROM ci_item WHERE item_id IN (" . implode(',', $linkItems) . ")");
        foreach ($d as $k => &$v) {
            $ft = unserialize(Tools::unesc($v['ft']));
            $sys_code = $ft[$this->CM['Код TyreIndex']];
            $c = $this->getOne("SELECT count(tipo_id) FROM ci_tipo WHERE sys_code='$sys_code' OR sys_code_to='$sys_code'");
            if ($c[0]) $r->fres = $this->putMsg(true, 'ID=' . $v['item_id'] . ' уже есть в таблице связей', true); elseif ($sys_code == $sys_code_to) $r->fres = $this->putMsg(true, 'ID=' . $v['item_id'] . ' нельзя объединять размер с самим собой', true);
            else $this->insert('ci_tipo', array(
                'gr' => $file['gr'],
                'sys_code' => $sys_code,
                'sys_code_to' => $sys_code_to,
                'name_to' => $name_to,
                'name' => Tools::esc($ft[$this->CM['Код TyreIndex']] . ': ' . $ft[$this->CM['Полный размер']]),
                'param' => Tools::esc(serialize(array(
                            'ID_BRAND' => $ft[$this->CM['Код бренда']],
                            'ID_MODEL' => $ft[$this->CM['Код модели']]
                        )))
            ));
        }
        $r->fres = true;
        $r->fres_msg = $this->fres_msg;
        $r = $this->preParse($file_id, 0, $sys_code_to);
        return $r;
    }

    function linkModel($item_id, $linkItems)
    { // linkItems цепляем к item_id
        $r = (object)[];
        $r->fres = true;
        $r->fres_msg = '';
        $item_id = (int)$item_id;
        foreach ($linkItems as $k => &$v) $v = (int)$v;
        $file_id = $this->getOne("SELECT file_id,ft FROM ci_item WHERE item_id='$item_id'");
        if ($file_id == 0) {
            $r->fres = $this->putMsg(false, 'Не найден файл');
            $r->fres_msg = $this->fres_msg;
            return $r;
        }
        $ft_to = unserialize(Tools::unesc($file_id['ft']));
        $this->file_id = $file_id = $file_id['file_id'];
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
        $this->gr = $file['gr'];
        $param = unserialize(Tools::unesc($file['param']));
        if (!isset($param['CM'])) {
            $r->fres = $this->putMsg(false, 'Не распознанная структура файла.');
            $r->fres_msg = $this->fres_msg;
            return $r;
        } else $this->CM = $param['CM'];
        $ID_MODEL_to = $ft_to[$this->CM['Код модели']];
        $name_to = Tools::esc($ft_to[$this->CM['Код модели']] . ': ' . $ft_to[$this->CM['Бренд']] . ' ' . $ft_to[$this->CM['Модель']]);
        $c = $this->getOne("SELECT count(model_id) FROM ci_model WHERE ID_MODEL='$ID_MODEL_to'");
        if ($c[0]) {
            $r->fres = $this->putMsg(false, 'ID=' . $item_id . ' уже есть в таблице связей');
            $r->fres_msg = $this->fres_msg;
            return $r;
        }
        $d = $this->fetchAll("SELECT ft,item_id FROM ci_item WHERE item_id IN (" . implode(',', $linkItems) . ")");
        foreach ($d as $k => &$v) {
            $ft = unserialize(Tools::unesc($v['ft']));
            $ID_MODEL = $ft[$this->CM['Код модели']];
            $c = $this->getOne("SELECT count(model_id) FROM ci_model WHERE ID_MODEL='$ID_MODEL' OR ID_MODEL_to='$ID_MODEL'");
            if ($c[0]) $r->fres = $this->putMsg(true, 'ID=' . $v['item_id'] . ' уже есть в таблице связей', true); elseif ($ID_MODEL == $ID_MODEL_to) $r->fres = $this->putMsg(true, 'ID=' . $v['item_id'] . ' нельзя сливать модель саму в себя', true);
            else $this->insert('ci_model', array(
                'gr' => $file['gr'],
                'ID_MODEL' => $ID_MODEL,
                'ID_MODEL_to' => $ID_MODEL_to,
                'name_to' => $name_to,
                'name' => Tools::esc($ft[$this->CM['Код модели']] . ': ' . $ft[$this->CM['Бренд']] . ' ' . $ft[$this->CM['Модель']]),
                'param' => Tools::esc(serialize(array('ID_BRAND' => $ft[$this->CM['Код бренда']])))
            ));
        }
        $r->fres = true;
        $r->fres_msg = $this->fres_msg;
        $r = $this->preParse($file_id, $ID_MODEL_to);
        return $r;
    }


    function preParse($file_id, $_ID_MODEL_to = 0, $_sys_code_to = 0)
    { // ИД назначения
        $r = (object)[];
        $r->fres = true;
        $r->fres_msg = $this->fres_msg;
        $ID_MODEL_to = (int)$_ID_MODEL_to;
        $sys_code_to = (int)$_sys_code_to;
        $file_id = (int)$file_id;
        if (!$this->gr) {
            $file = $this->getOne("SELECT gr,col_model,param FROM ci_file WHERE file_id='$file_id'");
            if ($file === 0) {
                $r->fres = $this->putMsg(false, '[PP]: Не найден файл id=' . $file_id, true);
                $r->fres_msg = $this->fres_msg;
                return $r;
            }
            $this->gr = @$file['gr'];
            if ($this->gr == 0) {
                $r->fres = $this->putMsg(false, '[PP]: Не присвоена группа для файла id=' . $file_id, true);
                $r->fres_msg = $this->fres_msg;
                return $r;
            }
        }
        if (!count($this->CM)) {
            $param = unserialize(Tools::unesc($file['param']));
            if (!isset($param['CM'])) {
                $r->fres = $this->putMsg(false, '[PP]: Не распознанная структура файла.', true);
                $r->fres_msg = $this->fres_msg;
                return $r;
            } else $this->CM = $param['CM'];
        }
        if ($_ID_MODEL_to || !$_ID_MODEL_to && !$_sys_code_to) {
            $d = $this->fetchAll("SELECT * FROM ci_model WHERE gr='{$this->gr}' " . ($ID_MODEL_to ? "AND ID_MODEL_to='$ID_MODEL_to'" : '') . " ORDER BY ID_MODEL_to");
            $ID_MODEL_to = 0;
            foreach ($d as $v) {  // цикл по ИД модей источникам
                if ($ID_MODEL_to != $v['ID_MODEL_to']) {
                    $ID_MODEL_to = $v['ID_MODEL_to'];
                    $c2 = $this->fetchAll("SELECT * FROM ci_item WHERE file_id='$file_id' AND IDMODEL='$ID_MODEL_to'"); // выбираем размеры из модели назначения
                }
                $ID_MODEL = $v['ID_MODEL'];
                $c1 = $this->fetchAll("SELECT * FROM ci_item WHERE file_id='$file_id' AND IDMODEL='$ID_MODEL' AND mstatus!=40");
                foreach ($c1 as $c1v) { // цикл по всем размерам в моделе источнике
                    $ft0 = $ft = unserialize(Tools::unesc($c1v['ft']));
                    unset($ft[$this->CM['Код TyreIndex']], $ft[$this->CM['Розница']], $ft[$this->CM['Склад']], $ft[$this->CM['Код бренда']], $ft[$this->CM['Код модели']], $ft[$this->CM['Бренд']], $ft[$this->CM['Модель']], $ft[$this->CM['Полный размер']]);
                    $price = $ft0[$this->CM['Розница']];
                    $sc = $ft0[$this->CM['Склад']];
                    foreach ($c2 as $c2v) { // цикл по всем размерам назначения
                        //$this->putMsg(true,"{$ID_MODEL}->{$ID_MODEL_to}: {$c2v['item_id']}<=>{$c1v['item_id']}",true);
                        $model0 = $c2v[$this->CMI[$this->gr]['Модель']['item_field']];
                        $brand0 = $c2v[$this->CMI[$this->gr]['Бренд']['item_field']];
                        $ft_to0 = $ft_to = unserialize(Tools::unesc($c2v['ft']));
                        $price_to = $ft_to[$this->CM['Розница']];
                        $sc_to = $ft_to[$this->CM['Склад']];
                        unset($ft_to[$this->CM['Код TyreIndex']], $ft_to[$this->CM['Розница']], $ft_to[$this->CM['Склад']], $ft_to[$this->CM['Код бренда']], $ft_to[$this->CM['Код модели']], $ft_to[$this->CM['Бренд']], $ft_to[$this->CM['Модель']], $ft_to[$this->CM['Полный размер']]);
                        //					$r->fres_msg.=print_r($ft,true).'------>'.print_r($ft_to,true).' ======>'.print_r(array_diff($ft,$ft_to),true).'......';
                        if (!count(array_diff($ft, $ft_to))) {
                            // есть одинаковые
                            $ft_to0[$this->CM['Склад']] += $sc;
                            if (min($price, $price_to) > 0) $ft_to0[$this->CM['Розница']] = min($price, $price_to); else $ft_to0[$this->CM['Розница']] = max($price, $price_to);
                            $s = Tools::esc(serialize($ft_to0));
                            if ($this->gr > 0) $this->update('ci_item', array(
                                    'ft' => $s,
                                    $this->CMI[$this->gr]['Розница']['item_field'] => $ft_to0[$this->CM['Розница']],
                                    $this->CMI[$this->gr]['Склад']['item_field'] => $ft_to0[$this->CM['Склад']]
                                ), "item_id='{$c2v['item_id']}'"); else $this->update('ci_item', array('ft' => $s), "item_id='{$c2v['item_id']}'");
                            $this->update('ci_item', array('mstatus' => 40), "item_id='{$c1v['item_id']}'");

                            break;
                        } else {
                            // нет совпадения
                            $ft0[$this->CM['Код модели']] = $ft_to0[$this->CM['Код модели']];
                            $ft0[$this->CM['Код бренда']] = $ft_to0[$this->CM['Код бренда']];
                            $ft0[$this->CM['Модель']] = $ft_to0[$this->CM['Модель']];
                            $ft0[$this->CM['Бренд']] = $ft_to0[$this->CM['Бренд']];
                            $s = Tools::esc(serialize($ft0));
                            if ($this->gr > 0) $this->update('ci_item', array(
                                    'ft' => $s,
                                    $this->CMI[$this->gr]['Код модели']['item_field'] => $ft0[$this->CM['Код модели']],
                                    $this->CMI[$this->gr]['Код бренда']['item_field'] => $ft0[$this->CM['Код бренда']],
                                    $this->CMI[$this->gr]['Модель']['item_field'] => $model0,
                                    $this->CMI[$this->gr]['Бренд']['item_field'] => $brand0
                                ), "item_id='{$c1v['item_id']}'"); else $this->update('ci_item', array('ft' => $s), "item_id='{$c1v['item_id']}'");
                            break;
                        }
                    }
                }
            }
        }
        if ($_sys_code_to || !$_sys_code_to && !$_ID_MODEL_to) {
            $d = $this->fetchAll("SELECT * FROM ci_tipo WHERE gr='{$this->gr}' " . ($sys_code_to ? "AND sys_code_to='$sys_code_to'" : '') . " ORDER BY sys_code_to");
            $delIds = array();
            $i = 0;
            if (count($d)) {
                $sys_code_to = $d[$i]['sys_code_to'];
                $c2 = $this->getOne("SELECT * FROM ci_item WHERE file_id='$file_id' AND sys_code='$sys_code_to'");
                $sys_code = $d[$i]['sys_code'];
                $ft_to = unserialize(Tools::unesc($c2['ft']));
                $price_to = $ft_to[$this->CM['Розница']];
                $sc_to = $ft_to[$this->CM['Склад']];
                do {
                    if ($c2 !== 0) {
                        $c1 = $this->getOne("SELECT * FROM ci_item WHERE file_id='$file_id' AND sys_code='$sys_code' AND cstatus!=40");
                        if ($c1 !== 0) {
                            $ft = unserialize(Tools::unesc($c1['ft']));
                            $price = $ft[$this->CM['Розница']];
                            $sc = $ft[$this->CM['Склад']];
                            $delIds[] = $c1['item_id'];
                            if (min($price_to, $price)) $price_to = min($price_to, $price); else $price_to = max($price_to, $price);
                            $sc_to += $sc;
                            if ($i == (count($d) - 1) || $sys_code_to != $d[$i + 1]['sys_code_to']) {
                                $ft_to[$this->CM['Розница']] = $price_to;
                                $ft_to[$this->CM['Склад']] = $sc_to;
                                if ($this->gr > 0) $this->update('ci_item', array(
                                        'ft' => Tools::esc(serialize($ft_to)),
                                        $this->CMI[$this->gr]['Розница']['item_field'] => $price_to,
                                        $this->CMI[$this->gr]['Склад']['item_field'] => $sc_to
                                    ), "item_id='{$c2['item_id']}'"); else $this->update('ci_item', array('ft' => Tools::esc(serialize($ft_to))), "item_id='{$c2['item_id']}'");
                            }
                        }
                    }
                    $i++;
                    if ($i < count($d) && $sys_code_to != $d[$i]['sys_code_to']) {
                        $sys_code_to = $d[$i]['sys_code_to'];
                        $c2 = $this->getOne("SELECT * FROM ci_item WHERE file_id='$file_id' AND sys_code='$sys_code_to'");
                        if ($c2 !== 0) {
                            $ft_to = unserialize(Tools::unesc($c2['ft']));
                            $price_to = $ft_to[$this->CM['Розница']];
                            $sc_to = $ft_to[$this->CM['Склад']];
                        }
                    }
                    if ($i < count($d)) $sys_code = $d[$i]['sys_code'];
                } while ($i < count($d));
                if (count($delIds)) $this->update('ci_item', array('cstatus' => 40), "item_id IN (" . implode(',', $delIds) . ")");
            }
        }
        return $r;
    }

    function parse($fname)
    {
        $r = parent::parse($fname);
        if ($this->gr > 0) {
            $res = $this->preParse($this->file_id);
        }
        return $r;
    }

    function checkStructure($file_id, $preParse = false)
    {
        $r = parent::checkStructure($file_id);
        if ($r && $this->oldgr != $this->gr && $this->gr > 0 && $preParse) {
            $res = $this->preParse($file_id);
        }
        //	$this->putMsg(true,'preParse:'."r=$r && oldgr={$this->oldgr} gr={$this->gr} && gr={$this->gr}>0 && preParse={$preParse}",true);
        return $r;
    }

    function getLinkArray()
    {
        $a = array(
            'tipo' => array('src' => array(), 'dist' => array()),
            'model' => array('src' => array(), 'dist' => array())
        );
        $LA = array(1 => $a, 2 => $a); // LinkArray по группам
        $d = $this->fetchAll("SELECT * FROM ci_tipo ORDER BY gr,sys_code_to,sys_code", MYSQL_ASSOC);
        foreach ($d as $k => &$v) {
            $LA[$v['gr']]['tipo']['src'][$v['sys_code']] = $v['sys_code_to'];
            $LA[$v['gr']]['tipo']['dist'][$v['sys_code_to']] = $v['sys_code'];
        }
        $d = $this->fetchAll("SELECT * FROM ci_model ORDER BY gr,ID_MODEL_to,ID_MODEL", MYSQL_ASSOC);
        foreach ($d as $k => &$v) {
            $LA[$v['gr']]['model']['src'][$v['ID_MODEL']] = $v['ID_MODEL_to'];
            $LA[$v['gr']]['model']['dist'][$v['ID_MODEL_to']] = $v['ID_MODEL'];
        }
        return $LA;
    }

    function getConfig($noLA = false)
    {
        $cfg = parent::getConfig();
        if (!$noLA) $cfg['LA'] = $this->getLinkArray();
        return $cfg;
    }

    function __construct()
    {
        parent::__construct();
    }


}

?>