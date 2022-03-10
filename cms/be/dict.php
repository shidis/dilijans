<?
include_once ('ajx_loader.php');

$cc=new CC_Ctrl();
//sleep(1);

$cp->setFN('dict');
$cp->checkPermissions();

$r->fres=true;
$r->fres_msg='';

$act=Tools::esc($_REQUEST['act']);

switch ($act){
case 'delTermById':
	$dict_id=intval($_REQUEST['dict_id']);
	$cc->query("DELETE FROM cc_dict WHERE dict_id='$dict_id'");
	break;
case 'termById':
	$dict_id=intval($_REQUEST['dict_id']);
	$d=$cc->getOne("SELECT name,text FROM cc_dict WHERE dict_id='$dict_id'");
	$r->text=$d['text'];
	break;
case 'loadBrands':
	$gr=intval($_REQUEST['gr']);
	$cc->que('brands',$gr);
	while($cc->next()!==false) {
		$r->brands[]=array('id'=>$cc->qrow['brand_id'],'name'=>Tools::html($cc->qrow['name']));
	}
	$d=$cc->fetchAll("SELECT dict_id,name FROM cc_dict WHERE gr='$gr' AND brand_id=0 ORDER BY name");
	if($d!==0)
		foreach($d as &$v) $r->items[]=array('dict_id'=>$v['dict_id'],'name'=>$v['name']);
	else $r->items=array();
	break;
case 'selectBrand':
	$brand_id=intval($_REQUEST['brand_id']);
	$gr=intval($_REQUEST['gr']);
	$d=$cc->fetchAll("SELECT dict_id,name FROM cc_dict WHERE gr='$gr' AND brand_id='$brand_id' ORDER BY name");
	if($d!==0)
		foreach($d as &$v) $r->items[]=array('dict_id'=>$v['dict_id'],'name'=>$v['name']);
	else $r->items=array();
	break;
case 'saveTerm':
	if(trim(@$_REQUEST['name'])=='') {
		$r->fres=false;
		$r->fres_msg='Необходимо задать термин. Не записано.';
		break;
	}
	if(trim(Tools::stripTags(@$_REQUEST['text']))=='') {
		$r->fres=false;
		$r->fres_msg='Значение отсутсвует. Не записано.';
		break;
	}
	$text=Tools::esc(trim(($_REQUEST['text'])));
	$name=Tools::esc(trim($_REQUEST['name']));
	$gr=intval($_REQUEST['gr']);
	$brand_id=intval($_REQUEST['brand_id']);
	$name1=Tools::like_($name);
	$d=$cc->getOne("SELECT dict_id,name FROM cc_dict WHERE  (name LIKE '$name1') AND (gr='$gr') AND (brand_id='$brand_id')");
	if($d!==0){
		if($cc->query("UPDATE cc_dict SET text='$text' WHERE dict_id='{$d['dict_id']}'")) {
			$r->fres_msg='Обновлено';
			$r->dict_id=$d['dict_id'];
		}
	}else{
		if($cc->query("INSERT INTO cc_dict (name,text,gr,brand_id) VALUES('$name','$text','$gr','$brand_id')")) {
			$r->fres_msg='Добавлено';
			$r->dict_id=$cc->lastId();
		}
	}
	break;
case 'termByStr':
	if(trim(@$_REQUEST['name'])=='') {
		$r->fres=false;
		$r->fres_msg='Необходимо задать термин.';
		break;
	}
	$name=Tools::esc(trim($_REQUEST['name']));
	$gr=intval($_REQUEST['gr']);
	$brand_id=intval($_REQUEST['brand_id']);
	$name1=Tools::like_($name);
	$d=$cc->getOne("SELECT dict_id,name,text FROM cc_dict WHERE  (name LIKE '$name1') AND (gr='$gr') AND (brand_id='$brand_id')");
	$r->dict_id=0;
	$r->text='';
	if($d!==0){
		$r->dict_id=$d['dict_id'];
		$r->name=Tools::unesc($d['name']);
		$r->text=Tools::unesc($d['text']);
	}
	break;
default: echo 'BAD ACT ID '.$act;
}

ajxEnd();