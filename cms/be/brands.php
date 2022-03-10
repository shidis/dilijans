<?
include_once ('ajx_loader.php');

//sleep(1);

$cp->setFN('brands');
$cp->checkPermissions();

$r->fres=true;
$r->fres_msg='';

$act=Tools::esc(@$_REQUEST['act']);
$gr=@$_REQUEST['gr'];

$db=new DB();
$cc=new CC_Ctrl();

switch ($act){
	case 'saveModelAlt':
		$id=(int)$_REQUEST['id'];
		if(!$id) {$r->fres=false; $r->fres_msg='Нет ID';}
		$v=Tools::esc(@$_REQUEST['v']);
		$db->query("UPDATE cc_brand SET alt='$v' WHERE brand_id='$id'");
		$r->v=Tools::unesc($v);
		break;

	case 'hSwitch':
		$id=(int)$_REQUEST['id'];
		if(!$id) {$r->fres=false; $r->fres_msg='Нет ID';}
		$r->h=$cc->hide_switch('cc_brand','brand_id',$id);
		$r->v="<a href=\"#\" class=\"h-sw\">";
		if($r->h) $r->v.='отобразить'; else $r->v.='скрыть';
		$r->v.='</a>';
		break;

			
default: $r->fres=false; $r->fres_msg='BAD ACT_CASE '.$act;
}

ajxEnd();