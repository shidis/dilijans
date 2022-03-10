<?
include_once ('ajx_loader.php');

//sleep(1);

$cp->setFN('datasets');
$cp->checkPermissions();

$r->fres=true;
$r->fres_msg='';

$act=Tools::esc(@$_REQUEST['act']);

$ds=new CC_Dataset();

switch ($act){
	case 'saveDataSet':
		$f=Tools::parseStr(@$_REQUEST['f']);
		$r->ddd=$f;
		$dt_added=date("Y-m-d H:i:s");
		$method=1;
		$dataset_id=(int)@$f['dataset_id'];
		if($dataset_id){
			$d=$ds->getOne("SELECT * FROM cc_dataset WHERE dataset_id='$dataset_id'");
			if($d['dataset_id']!=$dataset_id) {$r->fres=false; $r->fres_msg='Ошибка выборки';break;}
			$class=$d['class'];
		}else{
			if(empty($_REQUEST['class'])) {$r->fres=false; $r->fres_msg='ОШИБКА! Не передано имя класса';break;} else $class=Tools::esc($_REQUEST['class']);
		}
		$name=trim(@$f['name']);
		if(empty($name)) {$r->fres=false; $r->fres_msg='Не задано имя набора';break;}
		$sname=trim(@$f['sname']);
		if($ds->classExists($class)) $c=$ds->classInstance($class); else {$r->fres=false; $r->fres_msg='Класс набора '.$class.' отсутствует';break;}
		if(empty($sname)) $sname=Tools::tolow($class).'-'.date("d-m-Y-H-i-s"); else $sname=Tools::str2iso($sname);
		$sname1=Tools::like($sname);
		$d=$ds->getOne("SELECT count(dataset_id) FROM cc_dataset WHERE sname LIKE '$sname1' AND dataset_id!='$dataset_id'");
		if($d[0]) {$r->fres=false; $r->fres_msg='Дубликат системного имени.';break;}
		$name1=Tools::like($name);
		$d=$ds->getOne("SELECT count(dataset_id) FROM cc_dataset WHERE name LIKE '$name1' AND dataset_id!='$dataset_id'");
		if($d[0]) {$r->fres=false; $r->fres_msg='Дубликат имени набора.';break;}
		
		$data=array();
		foreach($c->dataFields as $k=>$v){
			if($v['require']){
				if(is_array($f[$k]) && (empty($f[$k][1]) || empty($f[$k][2])) 
				|| !is_array($f[$k]) && empty($f[$k])) {
					$r->fres=false; 
					$r->fres_msg='Не задано значение &quot;'.$v['info'].'&quot;'; 
					break;
				}
			} 
			$data[$k]=@$f[$k];
		}
		if(!$r->fres) break;
		$r->data=$data;
		$r->aaaa=$data=Tools::DB_serialize($data);
		if($dataset_id) {
			if(!$ds->query("UPDATE cc_dataset SET name='$name',sname='$sname',data='$data' WHERE dataset_id='$dataset_id'")) {
				$r->fres=false; $r->fres_msg='Ошибка записи БД';break;
			}
		}elseif(!$ds->query("INSERT INTO cc_dataset (method,class,name,sname,dt_added,data) VALUES('$method','$class','$name','$sname','$dt_added','$data')")) {
			$r->fres=false; $r->fres_msg='Ошибка записи БД';break;
		}
		break;
		
	case 'loadDataSet':
		$dataset_id=(int)@$_REQUEST['dataset_id'];
		if(empty($dataset_id)) {$r->fres=false; $r->fres_msg='Пропущен ID';break;}
		$d=$ds->getOne("SELECT * FROM cc_dataset WHERE dataset_id='$dataset_id'");
		if(mb_strpos($d['data'],':')!==false) $data=unserialize(stripslashes($d['data'])); else $data=Tools::DB_unserialize($d['data']);
		$r->data['name']=Tools::unesc($d['name']);
		$r->data['sname']=Tools::unesc($d['sname']);
		$r->data['class']=$class=$d['class'];
		if($ds->classExists($class)) $c=$ds->classInstance($class); else {$r->fres=false; $r->fres_msg='Класс набора '.$class.' отсутствует';break;}
		foreach($c->dataFields as $k=>$v){
			$r->data[$k]=@$data[$k];
		}
		break;

    case 'delDataSet':
        $dataset_id=(int)@$_REQUEST['dataset_id'];
        if(empty($dataset_id)) {$r->fres=false; $r->fres_msg='Пропущен ID';break;}
        $ds->query("DELETE FROM cc_dataset_cat WHERE dataset_id='$dataset_id'");
        $ds->query("DELETE FROM cc_dataset_model WHERE dataset_id='$dataset_id'");
        $ds->query("DELETE FROM cc_dataset_brand WHERE dataset_id='$dataset_id'");
        $ds->query("DELETE FROM cc_dataset WHERE dataset_id='$dataset_id'");
        break;

    case 'clearDataSet':
        $dataset_id=(int)@$_REQUEST['dataset_id'];
        if(empty($dataset_id)) {$r->fres=false; $r->fres_msg='Пропущен ID';break;}
        $ds->query("DELETE FROM cc_dataset_cat WHERE dataset_id='$dataset_id'");
        $ds->query("DELETE FROM cc_dataset_model WHERE dataset_id='$dataset_id'");
        $ds->query("DELETE FROM cc_dataset_brand WHERE dataset_id='$dataset_id'");
        break;

    default: $r->fres=false; $r->fres_msg='BAD ACT_CASE '.$act;
}

ajxEnd();