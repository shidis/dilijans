<?
include_once ('ajx_loader.php');

$cp->setFN('models');
$cp->checkPermissions();

//sleep(1);


$r->fres=true;
$r->fres_msg='';

$act=Tools::esc(@$_REQUEST['act']);
$gr=@$_REQUEST['gr'];

$db=new DB();
$cc=new CC_Ctrl();

switch ($act){
	case 'saveModelAlt':
		$id=(int)$_REQUEST['id'];
		if(!$id) {$r->fres=false; $r->fres_msg='Нет ID';break;}
		$v=Tools::esc(@$_REQUEST['v']);
		$db->query("UPDATE cc_model SET alt='$v' WHERE model_id='$id'");
		$r->v=Tools::unesc($v);
		break;

	case 'hSwitch':
		$id=(int)$_REQUEST['id'];
		if(!$id) {$r->fres=false; $r->fres_msg='Нет ID';break;}
		$r->h=$cc->hide_switch('cc_model','model_id',$id);
		$r->v="<a href=\"#\" class=\"h-sw\">";
		if($r->h) $r->v.='отобразить'; else $r->v.='скрыть';
		$r->v.='</a>';
		break;
	
	case 'change_atype':
		$id=(int)$_REQUEST['id'];
		if(!$id) {$r->fres=false; $r->fres_msg='Нет ID';break;}
		$cc->update('cc_model',array('P2'=>(int)$_REQUEST['atype']),"model_id='$id'");
		break;

	case 'change_dtype':
		$id=(int)$_REQUEST['id'];
		if(!$id) {$r->fres=false; $r->fres_msg='Нет ID';break;}
		$cc->update('cc_model',array('P1'=>(int)$_REQUEST['dtype']),"model_id='$id'");
		break;

    case 'suplrList':
        $model_id=(int)@$_REQUEST['model_id'];
        if(empty($model_id)) {$r->fres=false; $r->fres_msg='Нет MODEL_ID';break;}
        $d=$cc->fetchAll("SELECT cc_suplr.suplr_id, cc_suplr.name FROM cc_suplr JOIN cc_cat_sc USING (suplr_id) JOIN cc_cat USING (cat_id) WHERE NOT cc_cat.LD AND NOT cc_suplr.LD AND cc_cat.model_id=$model_id GROUP BY cc_suplr.suplr_id ORDER BY cc_suplr.name");
        $r->suplrs=array();
        foreach($d as $v){
            $r->suplrs[$v['suplr_id']]=Tools::html($v['name']);
        }
        $cc->que("model_by_id",$model_id);
        $cc->next();
        $r->modelName=Tools::html($cc->qrow['bname'].' '.$cc->qrow['name']);
        break;



        default: $r->fres=false; $r->fres_msg='BAD ACT_CASE '.$act;
}

ajxEnd();