<?
if (!defined('true_enter')) die ("Direct access not allowed!");

// ver.1.0    CAT_IMPORT_MODE==1

class App_CC_CIBase extends DB{

var
	$sheets,
	$fname,
	$ftype,
	$param=array(), // param в таблице ci_file
	$colModel=array(), // в таблице ci_file
	$CM=array(), // colModel подготовленная  для вывода таблицы
	$gr,
	$name, // имя файла в таблице ci_file
	$dt_add, // дата файла в таблице ci_file
	$replica=false,  // массив для реплики
	$file_id;
var $CMT=array(
	1=>array('Код TyreIndex'=>'Системный код','Код бренда'=>'IDBRAND','Код модели'=>'IDMODEL','Бренд'=>'Бренд','Модель'=>'Модель','Полный размер'=>'Название','Ширина'=>'Ш','Высота'=>'П','Диаметр'=>'Д','Индекс нагрузки'=>'ИН','Индекс скорости'=>'ИС','Суффиксы'=>'Усил.','Шипы'=>'Шип','Сезонность'=>'Сезон','Тип ТС'=>'Тип ТС','Склад'=>'Склад','Розница'=>'Розница'),
	2=>array('Код TyreIndex'=>'Системный код','Код бренда'=>'IDBRAND','Код модели'=>'IDMODEL','Бренд'=>'Бренд','Модель'=>'Модель','Полный размер'=>'Название','Ширина'=>'Ш','Диаметр'=>'Д','Крепеж'=>'Крепеж','PCD'=>'PCD','PCD (двойной)'=>'PCD(двойной)','ET'=>'ET','DIA'=>'Dia','Цвет'=>'Цвет','Тип диска'=>'Тип диска','Склад'=>'Склад','Розница'=>'Розница')
);

var $CMI=array(
	1=>array(
		'Код TyreIndex'=>array('item_field'=>'sys_code','type'=>'integer','list'=>array())
		,'Код бренда'=>array('item_field'=>'IDBRAND','type'=>'integer','list'=>array())
		,'Код модели'=>array('item_field'=>'IDMODEL','type'=>'integer','list'=>array())
		,'Бренд'=>array('item_field'=>'brand','type'=>'string','list'=>array())
		,'Модель'=>array('item_field'=>'model','type'=>'string','list'=>array())
		,'Полный размер'=>array('item_field'=>'full_name','type'=>'string','list'=>array())
		,'Ширина'=>array('item_field'=>'P3','type'=>'float','list'=>array())
		,'Высота'=>array('item_field'=>'P2','type'=>'float','list'=>array())
		,'Диаметр'=>array('item_field'=>'P1','type'=>'float','list'=>array())
		,'Индекс нагрузки'=>array('item_field'=>'P7','type'=>'string','list'=>array())
		,'Индекс скорости'=>array('item_field'=>'P7_1','type'=>'string','list'=>array())
		,'Суффиксы'=>array('item_field'=>'suffix','type'=>'string','list'=>array())
		,'Шипы'=>array('item_field'=>'MP3','type'=>'id','list'=>array('шип'=>1))
		,'Сезонность'=>array('item_field'=>'MP1','type'=>'id','list'=>array('летняя'=>1,'зимняя'=>2,'всесезонная'=>3))
		,'Тип ТС'=>array('item_field'=>'MP2','type'=>'id','list'=>array('легковой'=>1,'внедорожник'=>2,'микроавтобус'=>3))
		,'Склад'=>array('item_field'=>'sklad','type'=>'integer','list'=>array())
		,'Розница'=>array('item_field'=>'price','type'=>'price','list'=>array(),'cur_id'=>'1')
	),
	2=>array(
		'Код TyreIndex'=>array('item_field'=>'sys_code','type'=>'integer','list'=>array())
		,'Код бренда'=>array('item_field'=>'IDBRAND','type'=>'integer','list'=>array())
		,'Код модели'=>array('item_field'=>'IDMODEL','type'=>'integer','list'=>array())
		,'Бренд'=>array('item_field'=>'brand','type'=>'string','list'=>array())
		,'Модель'=>array('item_field'=>'model','type'=>'string','list'=>array())
		,'Полный размер'=>array('item_field'=>'full_name','type'=>'string','list'=>array())
		,'Ширина'=>array('item_field'=>'P2','type'=>'float','list'=>array())
		,'Диаметр'=>array('item_field'=>'P5','type'=>'float','list'=>array())
		,'Крепеж'=>array('item_field'=>'P4','type'=>'float','list'=>array())
		,'PCD'=>array('item_field'=>'P6','type'=>'float','list'=>array())
		,'PCD (двойной)'=>array('item_field'=>'P4_1','type'=>'float','list'=>array())
		,'ET'=>array('item_field'=>'P1','type'=>'float','list'=>array())
		,'DIA'=>array('item_field'=>'P3','type'=>'float','list'=>array())
		,'Цвет'=>array('item_field'=>'suffix','type'=>'string','list'=>array())
		,'Тип диска'=>array('item_field'=>'MP1','type'=>'id','list'=>array('кованый'=>1,'литой'=>2,'стальной'=>3))
		,'Склад'=>array('item_field'=>'sklad','type'=>'integer','list'=>array())
		,'Розница'=>array('item_field'=>'price','type'=>'price','list'=>array(),'cur_id'=>'1')
	)
);

var $status=array(
	0=>'не обработан',	
	1=>'связано',			
	2=>'добавлено',				
	3=>'обновлен (изм.рубрика)',			
	4=>'обновлен',	
	5=>'пропущен', // установлен статус размера "не обновлять цены и склад" 
	6=>'проблема импорта', //неизвестная проблема в процессе импорта
	//+20 = check file
	21=>'будет связано',		
	22=>'будет добавлено',		
	23=>'будет обновлено (изм. рубрика)',		
	24=>'есть',	
	25=>'будет пропущен', // установлен статус размера "не обновлять цены и склад" 
	26=>'проблема импорта',//неизвестная проблема в процессе импорта
	40=>'пропустить' // принудительный пропуск. устанавливается на этапе preParse()
);  
	

function recognize($file_id,$start,$limit,$opt)
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
    if ($this->qnum()) while ($this->next() !== false) {
        $ft = unserialize(Tools::unesc($this->qrow['ft']));
        $sys_code = (int)$ft[$this->CM['Код TyreIndex']];
        $IDBRAND = (int)$ft[$this->CM['Код бренда']];
        $IDMODEL = (int)$ft[$this->CM['Код модели']];
        if ($sys_code) {
            if ($param['gr'] == 2) $pa[$this->qrow['item_id']] = array('replica' => $this->isReplica(@$ft[$this->CM['Бренд']], @$ft[$this->CM['Модель']], @$opt['replicaBrand']));
            else $pa[$this->qrow['item_id']] = array();
            if ($this->replica !== false) $pa[$this->qrow['item_id']]['replica'] = $this->replica;
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
        foreach ($tyresSuffixes as $k => $suf) {
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
            // проверка на соответствие кода модели и бренда и если нет его,  то предупреждение и обновдение цен и остатков
            if ($v['IDBRAND'] != $IDBRAND) $mstatus = @$opt['check'] ? 23 : 3;
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
                if ($param['gr'] != 2 || @$cell['replica'] === false)
                    foreach ($d as $v)
                        if ($cell['cell']['Код бренда'] == $v['ti_id']) {
                            $brand_id = $v['brand_id'];
                            $bstatus = 4; // обновление
                            break;
                        }
                if (!$brand_id)
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
                if (!@$opt['check'] && $bstatus == 4 && !isset($bupd[$brand_id])) { // для реплики условие не сработает никогда
                    $this->update('cc_brand', array('ti_file_id' => $file_id, 'H' => 0), "brand_id='$brand_id'");
                    $bupd[$brand_id] = 1;
                }
                // ищем модель внутри бренда
                if ($brand_id) {
                    $mc = Tools::like($cell['cell']['Модель']);
                    if ($param['gr'] == 2 && $cell['replica'] !== false) {
                        $mc = $cell['replica']['model'];
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
                    $d = $this->getOne("SELECT DISTINCT cc_cat.cat_id FROM (cc_cat INNER JOIN cc_model ON cc_cat.model_id = cc_model.model_id) INNER JOIN cc_brand ON cc_model.brand_id = cc_brand.brand_id WHERE (cc_cat.gr='{$file['gr']}')AND(NOT cc_model.LD)AND(NOT cc_brand.LD)AND(NOT cc_cat.LD)AND(cc_cat.ti_id=0)AND($w)");
                    if ($d !== 0) {
                        $cat_id = $d['cat_id'];
                        if ($d['ignoreUpdate']) $cstatus = 5; // пропуск
                        else $cstatus = 1; //привязка
                    } else {
                        $cstatus = 2; //добавление
//							echo $this->sql_query;
//							return;
                    }
                } else $cstatus = 2;
                // запись данных
                if (@$opt['check']) {
                    $cstatus += 20;
                    $mstatus += 20;
                    $bstatus += 20;
                } else {
                    // делаем бренд
                    if (!isset($bupd[$brand_id]) || !$brand_id) {
                        $a = array('ti_id' => $tiBrands[$item_id], 'ti_file_id' => $file_id);
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
                // неизвестная проблема
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
        if (Tools::mb_strcasecmp($replicaBrand, trim($brand)) == 0) {
            preg_match("/([^\(]+)\((.+?)\)(.*)/u", $model, $m);
            if (count($m) == 4) {
                $this->replica = array('brand' => Tools::cutDoubleSpaces(trim($m[1])), 'model' => Tools::cutDoubleSpaces(trim($m[2] . $m[3])));
//			print_r($this->replica);
                return true;
            } else {
                $this->replica = array('brand' => $replicaBrand, 'model' => $model);
                return true;
            }
        }
        $this->replica = false;
        return false;
    }

    function makeStat($file_id, $opt)
    {
        $r=(object)[];
        $cc = new CC_Ctrl();
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
        $d = $this->getOne("SELECT count(item_id) FROM ci_item WHERE (file_id='$file_id')AND(item_id!='{$param['header_item_id']}')AND(cstatus=40 OR mstatus=40 OR bstatus=40)");
        $r->c40 = $c40 = $d[0];
        $d = $this->getOne("SELECT count(item_id) FROM ci_item WHERE (file_id='$file_id')AND(item_id!='{$param['header_item_id']}')AND cstatus>0 AND cstatus<20 AND cstatus!=40 AND mstatus!=40 AND bstatus!=40");
        if (($d[0] + $c40) == ($param['numRows'] - 1)) $param['status'] = 2; // ИМПОРТИРОВАН
        elseif ($d[0]) $param['status'] = -2; //ЧАСТИЧНО ИМПОРТИРОВАН
        else {
            $d = $this->getOne("SELECT count(item_id) FROM ci_item WHERE (file_id='$file_id')AND(item_id!='{$param['header_item_id']}') AND cstatus>=20 AND cstatus!=40 AND mstatus!=40 AND bstatus!=40");
            if (($d[0] + $c40) == ($param['numRows'] - 1)) $param['status'] = 1; // ОБРАБОТАН БЕЗ ЗАПИСИ
            elseif ($d[0]) $param['status'] = -1; //ЧАСТИЧНО ОБРАБОТАН БЕЗ ЗАПИСИ
        }
        $d = $this->getOne("SELECT count(item_id) FROM ci_item WHERE (file_id='$file_id')AND(item_id!='{$param['header_item_id']}')AND(cstatus=1)");
        if ($d !== 0) $param['relTipos'] = $d[0];
        $d = $this->getOne("SELECT count(item_id) FROM ci_item WHERE (file_id='$file_id')AND(item_id!='{$param['header_item_id']}')AND(mstatus=1)");
        if ($d !== 0) $param['relModels'] = $d[0];
        $d = $this->getOne("SELECT count(item_id) FROM ci_item WHERE (file_id='$file_id')AND(item_id!='{$param['header_item_id']}')AND(bstatus=1)");
        if ($d !== 0) $param['relBrands'] = $d[0];


        $d = $this->getOne("SELECT count(item_id) FROM ci_item WHERE (file_id='$file_id')AND(item_id!='{$param['header_item_id']}')AND(cstatus=5 OR cstatus=25)");
        if ($d !== 0) $param['c_code5'] = $d[0];

        $d = $this->getOne("SELECT count(item_id) FROM ci_item WHERE (file_id='$file_id')AND(item_id!='{$param['header_item_id']}')AND(bstatus=6 OR bstatus=26)");
        if ($d !== 0) $param['b_code6'] = $d[0];
        $d = $this->getOne("SELECT count(item_id) FROM ci_item WHERE (file_id='$file_id')AND(item_id!='{$param['header_item_id']}')AND(mstatus=6 OR mstatus=26)");
        if ($d !== 0) $param['m_code6'] = $d[0];
        $d = $this->getOne("SELECT count(item_id) FROM ci_item WHERE (file_id='$file_id')AND(item_id!='{$param['header_item_id']}')AND(cstatus=6 OR cstatus=26)");
        if ($d !== 0) $param['c_code6'] = $d[0];

        $d = $this->getOne("SELECT count(item_id) FROM ci_item WHERE (file_id='$file_id')AND(item_id!='{$param['header_item_id']}')AND(cstatus=2)");
        if ($d !== 0) $param['newTipos'] = $d[0];
        $d = $this->getOne("SELECT count(item_id) FROM ci_item WHERE (file_id='$file_id')AND(item_id!='{$param['header_item_id']}')AND(mstatus=2)");
        if ($d !== 0) $param['newModels'] = $d[0];
        $d = $this->getOne("SELECT count(item_id) FROM ci_item WHERE (file_id='$file_id')AND(item_id!='{$param['header_item_id']}')AND(bstatus=2)");
        if ($d !== 0) $param['newBrands'] = $d[0];

        $d = $this->getOne("SELECT count(item_id) FROM ci_item WHERE (file_id='$file_id')AND(item_id!='{$param['header_item_id']}')AND(cstatus=4)");
        if ($d !== 0) $param['refreshTipos'] = $d[0];
        $d = $this->getOne("SELECT count(item_id) FROM ci_item WHERE (file_id='$file_id')AND(item_id!='{$param['header_item_id']}')AND(mstatus=4)");
        if ($d !== 0) $param['refreshModels'] = $d[0];
        $d = $this->getOne("SELECT count(item_id) FROM ci_item WHERE (file_id='$file_id')AND(item_id!='{$param['header_item_id']}')AND(bstatus=4)");
        if ($d !== 0) $param['refreshBrands'] = $d[0];

        $d = $this->getOne("SELECT count(item_id) FROM ci_item WHERE (file_id='$file_id')AND(item_id!='{$param['header_item_id']}')AND(cstatus=3)");
        if ($d !== 0) $param['moveTipos'] = $d[0];
        $d = $this->getOne("SELECT count(item_id) FROM ci_item WHERE (file_id='$file_id')AND(item_id!='{$param['header_item_id']}')AND(mstatus=3)");
        if ($d !== 0) $param['moveModels'] = $d[0];
        $d = $this->getOne("SELECT count(item_id) FROM ci_item WHERE (file_id='$file_id')AND(item_id!='{$param['header_item_id']}')AND(bstatus=3)");
        if ($d !== 0) $param['moveBrands'] = $d[0];

        $d = $this->getOne("SELECT count(item_id) FROM ci_item WHERE (file_id='$file_id')AND(item_id!='{$param['header_item_id']}')AND(price>0)");
        if ($d !== 0) $param['notZeroPriceNum'] = $d[0];
        $d = $this->getOne("SELECT count(item_id) FROM ci_item WHERE (file_id='$file_id')AND(item_id!='{$param['header_item_id']}')AND(sklad>0)");
        if ($d !== 0) $param['notZeroSkladNum'] = $d[0];

        if ($param['status'] == 2) {
            if (@$opt['delBrandsAbsent'])
                $this->update('cc_brand', array('LD' => 1), "(ti_file_id!='$file_id')AND(gr='{$param['gr']}')");
            if (@$opt['delModelsAbsent'])
                $this->update('cc_model', array('LD' => 1), "(ti_file_id!='$file_id')AND(gr='{$param['gr']}')");
            if (@$opt['delTiposAbsent'])
                $this->update('cc_cat', array('LD' => 1), "(ti_file_id!='$file_id')AND(gr='{$param['gr']}')AND(NOT ignoreUpdate)");
            elseif (@$opt['resetAbsent']) { // Обнулять складской остаток у отстуствующих в файле размеров
                $this->update('cc_cat', array('sc' => 0), "(ti_file_id!='$file_id')AND(sc>0)AND(gr='{$param['gr']}')AND(NOT ignoreUpdate)");
                $r->un = $this->updatedNum();
            }
            if (@$opt['hideZeroTipo']) { // Скрывать с сайта типоразмеры, отсутсвующие на складе
                $this->update('cc_cat', array('H' => 1), "(sc=0)AND(gr='{$param['gr']}')AND(NOT ignoreUpdate)");
            }
            if (@$opt['hideZero']) { // Скрывать с сайта типоразмеры, модели и бренды, отстутсвующие на складе
                // скрывае все типоразмеры sc==0
                $this->update('cc_cat', array('H' => 1), "(sc=0)AND(gr='{$param['gr']}')AND(NOT ignoreUpdate)");
                // создаем список моделей которые должны быть открыты
                $d = $this->fetchAll("SELECT DISTINCT cc_model.model_id FROM cc_model INNER JOIN cc_cat ON cc_model.model_id = cc_cat.model_id WHERE (cc_model.gr='{$param['gr']}')AND(NOT cc_model.LD)AND(NOT cc_cat.LD)AND(cc_cat.sc>0 OR cc_cat.ignoreUpdate)");
                $ma = array();
                if (count($d)) foreach ($d as $v) $ma[] = $v['model_id'];
                $r->show_model = $ma;
                // скрывае все модели
                $this->update('cc_model', array('H' => 1), "(NOT LD)AND(gr='{$param['gr']}')");
                // показываем все можели из списка ma
                if (count($ma)) $this->update('cc_model', array('H' => 0), "model_id IN (" . join(',', $ma) . ")");
                // аналогично для брендов
                $d = $this->fetchAll("SELECT DISTINCT cc_brand.brand_id FROM cc_brand INNER JOIN cc_model ON cc_brand.brand_id = cc_model.brand_id WHERE (cc_brand.gr='{$file['gr']}')AND(NOT cc_model.LD)AND(NOT cc_brand.LD)AND(NOT cc_model.H)");
                $ma = array();
                // создаем список брендов которые должны быть открыты
                if (count($d)) foreach ($d as $v) $ma[] = $v['brand_id'];
                $r->show_brand = $ma;
                // скрывае все бренды
                $this->update('cc_brand', array('H' => 1), "(NOT LD)AND(gr='{$param['gr']}')");
                // показываем все бренды из списка ma
                if (count($ma)) $this->update('cc_brand', array('H' => 0), "brand_id IN (" . join(',', $ma) . ")");
            }
            // Автоматически скрывать с сайта типоразмеры, имеющие статус "Пропустить"
            if (@$opt['hideMiss']) {
                // определям cc_cat.ti_id всех размеров со статусом ПРОПУСТИТЬ
                $d = $this->fetchAll("SELECT cc_cat.cat_id FROM cc_cat INNER JOIN ci_item ON cc_cat.ti_id=ci_item.sys_code WHERE (ci_item.file_id='$file_id') AND(ci_item.cstatus=40 OR ci_item.mstatus=40 OR ci_item.bstatus=40)");
                $dd = array();
                foreach ($d as $v) $dd[] = $v[0];
                if (count($dd)) $this->query("UPDATE cc_cat SET H=1 WHERE cat_id IN(" . implode(',', $dd) . ")");
            }

        }
        unset($opt['check']);
        $this->setConfig(array('uploadFrm' => $opt));
        $this->update('ci_file', array('param' => Tools::esc(serialize($param)), 'status' => $param['status']), "file_id='$file_id'");
        if ($param['status'] == 2 || $param['status'] == -2) $cc->addCacheTask('brands pricesNoIntPrice sizes modAll', $file['gr']);
        $r->fres = $this->fres;
        $r->fres_msg = $this->fres_msg;
        $r->param = $param;
        $r->gr = $file['gr'];
        return $r;
    }

    function defConfig(&$d)
    {
        $d['uploadFrm']['delBrandsAbsent'] = 0;
        $d['uploadFrm']['delModelsAbsent'] = 0;
        $d['uploadFrm']['delTiposAbsent'] = 0;
        $d['uploadFrm']['resetAbsent'] = 0;
        $d['uploadFrm']['hideZero'] = 0;
        $d['uploadFrm']['hideZeroTipo'] = 0;
        $d['uploadFrm']['hideOff'] = 0;
        $d['uploadFrm']['updatePrices'] = 0;
        $d['uploadFrm']['updateStock'] = 0;
        $d['uploadFrm']['hideMiss'] = 0;
        $d['uploadFrm']['updateTyresSuffix'] = 0;
        $d['uploadFrm']['replicaBrand'] = 'Replica';
        $d['uploadFrm']['maxFileList'] = '7';
    }

    function getConfig()
    {
        $d = array();
        $this->defConfig($d);
        $dd = Data::get('ci_config');
        if ($dd != '') {
            $dd = unserialize($dd);
            $dd['uploadFrm'] = array_merge($d['uploadFrm'], $dd['uploadFrm']);
            $d = array_merge($d, $dd);
//		print_r($d);
        } else {
            Data::set('ci_config', serialize($d));
        }
        return $d;
    }

    function setConfig($alldata)
    {
        $d = array();
        $this->defConfig($d);
        $d = array_merge($d, $alldata);
        Data::set('ci_config', serialize($d));
    }

    function view($file_id, $page, $limit, $sidx, $sord)
    {
        $r=(object)[];
        $d = $this->getOne("SELECT gr,col_model,param FROM ci_file WHERE file_id='$file_id'");
        if ($d === 0) {
            return $this->putMsg(false, 'Не найден файл id=' . $file_id);
        }
        if (@$d['gr'] == 0) {
            return $this->putMsg(false, 'Не присвоена группа для файла id=' . $file_id);
        }
        $param = unserialize(Tools::unesc($d['param']));
        if (!isset($param['CM'])) return $this->putMsg(false, 'Не распознанная структура файла.');
        else $this->CM = $param['CM'];
//	Tools::prn($this->CM);
        $r->status = @$param['status'];
        $s = array();
        foreach ($this->CMI[$param['gr']] as $v) {
            if (isset($_GET[$v['item_field']])) {
                switch ($v['type']) {
                    case 'id':
                        /*					if(count($v['list'])) {
                                                $a=array_flip($v['list']);
                                                $s[]="{$v['item_field']}='".@$a[intval($_GET[$v['item_field']])]."'";
                                                break;
                                            }*/
                    case 'id':
                    case 'integer':
                        $s[] = "{$v['item_field']}='" . intval($_GET[$v['item_field']]) . "'";
                        break;
                    case 'float':
                    case 'price':
                        $s[] = "{$v['item_field']} LIKE '" . floatval($_GET[$v['item_field']]) . "'";
                        break;
                    default:
                        $s[] = "{$v['item_field']} LIKE '%" . Tools::esc(($_GET[$v['item_field']])) . "%'";
                        break;
                }
            }
        }
        if (isset($_GET['item_id'])) $s[] = "item_id='" . intval($_GET['item_id']) . "'";
        if (@$param['status'] == 2 || @$param['status'] == -2) {
            if (isset($_GET['cstatus'])) $s[] = "cstatus='" . intval($_GET['cstatus']) . "'";
            if (isset($_GET['mstatus'])) $s[] = "mstatus='" . intval($_GET['mstatus']) . "'";
            if (isset($_GET['bstatus'])) $s[] = "bstatus='" . intval($_GET['bstatus']) . "'";
        } elseif (@$param['status'] == 1 || @$param['status'] == -1) {
            if (isset($_GET['cstatus'])) $s[] = "cstatus='" . intval($_GET['cstatus'] + ($_GET['cstatus'] != 40 ? 20 : 0)) . "'";
            if (isset($_GET['mstatus'])) $s[] = "mstatus='" . intval($_GET['mstatus'] + ($_GET['mstatus'] != 40 ? 20 : 0)) . "'";
            if (isset($_GET['bstatus'])) $s[] = "bstatus='" . intval($_GET['bstatus'] + ($_GET['bstatus'] != 40 ? 20 : 0)) . "'";
        }
        $s = implode(' AND ', $s);
        if ($s != '') $s = ' AND ' . $s;
        $r->sql = $s;

        $d = $this->getOne("SELECT count(item_id) FROM ci_item WHERE (file_id='$file_id')AND(item_id!='{$param['header_item_id']}')$s");
        $count = $d[0];
        $total_pages = ceil($count / $limit);
        if ($page > $total_pages) $page = $total_pages;
        $start = $limit * $page - $limit;
        if ($start < 0) $start = 0;
        if ($sidx == '') $sidx = 'item_id';
        $r->page = $page;
        $r->total = $total_pages;
        $r->records = $count;

        $this->query("SELECT * FROM ci_item WHERE (file_id='$file_id')AND(item_id!='{$param['header_item_id']}')$s ORDER BY $sidx $sord LIMIT $start,$limit", MYSQL_ASSOC);
        $i = 0;
        if ($this->qnum()) while ($this->next() !== false) {
            $ft = unserialize(Tools::unesc($this->qrow['ft']));
//		Tools::prn($ft);
            $r->rows[$i]['id'] = $this->qrow['item_id'];
            $r->rows[$i]['cell'][] = '';
            $r->rows[$i]['cell'][] = $this->qrow['item_id'];
            if (isset($this->status[$this->qrow['cstatus']])) $r->rows[$i]['cell'][] = $this->status[$this->qrow['cstatus']]; else $r->rows[$i]['cell'][] = '???';
            if (isset($this->status[$this->qrow['mstatus']])) $r->rows[$i]['cell'][] = $this->status[$this->qrow['mstatus']]; else $r->rows[$i]['cell'][] = '???';
            if (isset($this->status[$this->qrow['bstatus']])) $r->rows[$i]['cell'][] = $this->status[$this->qrow['bstatus']]; else $r->rows[$i]['cell'][] = '???';
            if (@$param['status'] != 0) {
                foreach ($this->CMI[$param['gr']] as $k => $v) {
                    if ($this->qrow['bstatus'] == 40 || $this->qrow['mstatus'] == 40 || $this->qrow['cstatus'] == 40) {
                        $r->rows[$i]['cell'][] = isset($ft[$this->CM[$k]]) ? Tools::html($ft[$this->CM[$k]], true) : '';
                    } else
                        if (count($v['list'])) {
                            $a = array_flip($v['list']);
                            $r->rows[$i]['cell'][] = @$a[$this->qrow[$v['item_field']]];
                        } else {
                            if (in_array($v['item_field'], array('P1', 'P2', 'P3', 'P4', 'P5', 'P6'))) $this->qrow[$v['item_field']] = $this->qrow[$v['item_field']] * 1;
                            $r->rows[$i]['cell'][] = $this->qrow[$v['item_field']];
                        }
                }
            } else {
                foreach ($this->CM as $k => $v) $r->rows[$i]['cell'][] = isset($ft[$v]) ? Tools::html($ft[$v], true) : '';
            }
            $i++;
        }
        return $r;
    }


    function __construct()
    {
        parent::__construct();
        include Cfg::_get('root_path') . '/inc/excel/reader.php';
    }


    function readCSV($fname, $sheetNo = 1)
    {
//	error_reporting(0);
        $this->fname = $fname;
        if (!is_file($fname)) {
            return $this->putMsg(false, "Файл $fname не найден");
        }
        $f = fopen($fname, 'r');
        $row = $cols = 0;
        $this->sheets = array();
        while (($data = fgetcsv($f, 0, ";")) !== FALSE) {
            $row++;
            $cols = $cols < count($data) ? count($data) : $cols;
            foreach ($data as $k => &$v) $v = iconv('cp1251', 'UTF-8//IGNORE', $v);
            $this->sheets[$sheetNo - 1]['cells'][] = $data;
        }
        fclose($f);
        if (!$row || !$cols) {
            return $this->putMsg(false, 'Нет строк в файле');
        }
        $this->sheets[$sheetNo - 1]['numRows'] = $row;
        $this->sheets[$sheetNo - 1]['numCols'] = $cols;
        return $this->putMsg(true);
    }

    function readExcel($fname, $sheetNo = 'all')
    {
//	echo memory_get_usage() ;
//	echo '-----';
        $this->fname = $fname;
        if (!is_file($fname)) {
            return $this->putMsg(false, "Файл $fname не найден");
        }
        try {
            $excel = new Spreadsheet_Excel_Reader();
            $excel->setOutputEncoding("UTF-8");
            if (($res = $excel->read($fname) !== true)) {
                $this->putMsg(false, "Ошибка во время обработки EXCEL файла");
                unset($excel);
                return false;
            }
            /*	for ($x=0; $x<sizeof ($excel->sheets); $x++) {
                    echo "Number of rows in sheet " . ($x+1) . ": " . $excel->sheets[$x]["numRows"] . "\n";
                    echo "Number of columns in sheet " . ($x+1) . ": " . $excel->sheets[$x]["numCols"] . "\n";
                    $this->echoSheet($excel->sheets[$x]);
                }*/
            if (!count($excel->sheets)) {
                $this->putMsg(false, "Нет доступных листов после обработки EXCEL файла");
                unset($excel);
                return false;
            }
            /*	echo memory_get_usage() ;
                echo '-----';*/
            if ($sheetNo == 'all') $this->sheets = $excel->sheets;
            else {
                if (isset($excel->sheets[$sheetNo - 1])) $this->sheets[$sheetNo - 1] = $excel->sheets[$sheetNo - 1];
                else {
                    $this->putMsg(false, "Нет доступных данных на листе " . $sheetNo);
                    unset($excel);
                    return false;
                }
            }
//	echo memory_get_usage() ;
//	echo '-----';
            unset($excel);
        } catch (Exception $e) {
            return $this->putMsg(false, $e->getMessage());
        }
//	echo memory_get_usage() ;
//	echo '---';
        return $this->putMsg(true);
    }

    function echoSheet($sheet)
    {
        ?>
        <table><?
        $x = 1;
        while ($x <= $sheet['numRows']) {
            echo "<tr><td>$x</td>";
            $y = 1;
            while ($y <= $sheet['numCols']) {
                $cell = isset($sheet['cells'][$x][$y]) ? $sheet['cells'][$x][$y] : '';
                echo "<td>$cell</td>";
                $y++;
            }
            echo "</tr>";
            $x++;
        }
        ?></table><?
    }

    function parse($fname)
    {
        $pi = pathinfo($this->fname = $fname);
        $this->param = array();
        $this->param['sheetNo'] = 1; // нумерация с 1 при отправке в парвер и для записи в таблицу param файла. Но Парсер нумерует с нуля
        $this->ftype = mb_strtolower(@$pi['extension']);
        if ($this->ftype == 'xls') $this->readExcel($fname, $this->param['sheetNo']);
        elseif ($this->ftype == 'csv') $this->readCSV($fname, $this->param['sheetNo']);
        else $this->putMsg(false, 'Неверный формат файла ' . $this->ftype);
        @unlink($fname);
        if (!$this->fres) return false;
        $this->param['extension'] = $this->ftype;
        $this->param['fname'] = $pi['basename'];
        $this->param['numRows'] = $this->sheets[$this->param['sheetNo'] - 1]['numRows'];
        $this->param['numCols'] = $this->sheets[$this->param['sheetNo'] - 1]['numCols'];
        $this->gr = 0;
        $this->name = date("d/m/Y H:i") . ', ' . $pi['basename'];
        $this->dt_add = date("Y-m-d H:i:s");
        $col_model = serialize(array());
        $this->query("INSERT INTO ci_file (name,gr,dt_add,param,col_model) VALUES('{$this->name}','{$this->gr}','{$this->dt_add}','" . Tools::esc(serialize($this->param)) . "','$col_model')");
        $this->file_id = $this->lastId();
        $line = 0;
        $res = false;
        if (count(@$this->sheets[$this->param['sheetNo'] - 1]['cells']))
            foreach (@$this->sheets[$this->param['sheetNo'] - 1]['cells'] as $cell) {
                $t = false;
                for ($i = 1; $i <= $this->param['numCols']; $i++)
                    if (trim(@$cell[$i]) != '') {
                        $t = true;
                        break;
                    } // перопускаем пустые строки
                if ($t) {
                    $line++;
                    if ($res) {
                        // сразу разносим коды ТИ по полям БД
                        $idmodel = @$cell[$this->CM['Код модели']];
                        $idbrand = @$cell[$this->CM['Код бренда']];
                        $syscode = @$cell[$this->CM['Код TyreIndex']];
                        $this->query("INSERT INTO ci_item (file_id,ft,IDBRAND,IDMODEL,sys_code) VALUES('{$this->file_id}','" . Tools::esc(serialize($cell)) . "','{$idbrand}','{$idmodel}','$syscode')");
                    } else $this->query("INSERT INTO ci_item (file_id,ft) VALUES('{$this->file_id}','" . Tools::esc(serialize($cell)) . "')");
                    if ($line == 1) $res = $this->checkStructure($this->file_id); // заголовок должен быть в первой записи!!!
                }
            }
        return $this->fres;
    }

    function checkStructure($file_id)
    {
        $this->CM = array(); // CM: [{поле:номер колонки}]
        $this->colModel = array();
        if (!$file_id) return $this->putMsg(false, 'Не задан файл для анализа', true);
        $d = $this->getOne("SELECT name,col_model, gr,param FROM ci_file WHERE file_id='$file_id'");
        if ($d !== 0) {
            $this->colModel = unserialize(Tools::unesc($d['col_model']));
            $this->param = unserialize(Tools::unesc($d['param']));
            $this->name = Tools::unesc($d['name']);
            $this->oldgr = @$this->param['gr'];
        } else return $this->putMsg(false, 'файл не существует', true);
        if (!count($this->colModel)) {
            $this->colModel = unserialize(Data::get('ci_cm_default'));
            $this->update('ci_file', array('col_model' => Tools::esc(serialize($this->colModel))), "file_id='$file_id'");
            unset($ss);
        }
        if (!count($this->colModel)) return $this->putMsg(true, 'Нет модели полей для анализа', true);
        $d = $this->getOne("SELECT * FROM ci_item WHERE file_id='$file_id' ORDER BY item_id"); // заголовок должен быть в первой записи!!!
        if ($d === 0) return $this->putMsg(false, 'Файл пустой', true);
        $ft = unserialize(Tools::unesc($d['ft']));
        $this->param['header_item_id'] = $d['item_id'];
        $this->gr = 0;
        for ($gr = 1; $gr <= 2; $gr++) if ($this->colModel[$gr]['rows']) {
            $this->CM = array();
            $c = array();
            for ($i = 0; $i < count($this->colModel[$gr]['rows']); $i++) $c[$this->colModel[$gr]['rows'][$i]['cell'][0]] = mb_strtolower($this->colModel[$gr]['rows'][$i]['cell'][1]);
            foreach ($ft as $k => &$v) {
                if (($cn = array_search(mb_strtolower($v), $c)) !== false) $this->CM[$cn] = $k;
            }
            if (count($this->CM) == count($this->colModel[$gr]['rows'])) {
                $this->gr = $gr;
                break;
            }
        }
        if (!count($this->CM)) return $this->putMsg(true, 'Структура файла не распознана. Проверьте настройки полей', true);
        $this->header = $ft;
        if (!$this->gr || count($this->CM) != count($this->colModel[$this->gr]['rows'])) return $this->putMsg(true, 'Не все столбцы файла распознаны. Проверьте настройки', true);
        $this->param['CM'] = $this->CM;
        $this->param['gr'] = $this->gr;
        if ($file_id) $this->update('ci_file', array('gr' => $this->gr, 'param' => Tools::esc(serialize($this->param))), "file_id='$file_id'");
        return $this->putMsg(true);
    }

    function clearFileList($maxFiles)
    {
        $mf = (int)$maxFiles;
        if (empty($mf)) $mf = 50;
        $d = $this->fetchAll("SELECT file_id FROM ci_file ORDER BY dt_add DESC");
        for ($i = $mf; $i < count($d); $i++) {
            $this->del('ci_item', 'file_id', $d[$i]['file_id']);
            $this->del('ci_file', 'file_id', $d[$i]['file_id']);
        }
        $this->query("OPTIMIZE TABLE ci_item");
    }

}?>