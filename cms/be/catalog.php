<?
include_once ('ajx_loader.php');

sleep(0);


$cp->setFN('catalog');
$cp->checkPermissions();

$r->fres=true;
$r->fres_msg='';

$act=Tools::esc(@$_REQUEST['act']);
$gr=@$_REQUEST['gr'];

$cc=new CC_Ctrl();


switch ($act){
	case 'hSwitch':
		$id=(int)$_REQUEST['id'];
		if(!$id) {$r->fres=false; $r->fres_msg='Нет ID';}
		$r->h=$cc->hide_switch('cc_cat','cat_id',$id);
		$r->v="<a href=\"#\" class=\"chide\">";
		if($r->h) $r->v.='отобразить'; else $r->v.='скрыть';
		$r->v.='</a>';
		break;

	case 'fixPriceSwitch':
		$id=(int)$_REQUEST['id'];
		$cprice=(int)$_REQUEST['cprice'];
		if(!$id) {$r->fres=false; $r->fres_msg='Нет ID';}
		
		$d=$cc->getOne("SELECT cprice,fixPrice,gr FROM cc_cat WHERE cat_id='$id'");
		$gr=$d['gr'];
		$r->v="<a href=\"#\" class=\"fixPrice\">";
		if($d['fixPrice']) {
			$v=0;
			$r->v.='нет';
		}else{
			$v=1;
			$r->v.='да';
		}
		$cc->query("UPDATE cc_cat SET fixPrice='$v' WHERE cat_id='$id'");
		//
		//if(!$v) $cc->extra_price_update($id);
		if($v) $cc->query("UPDATE cc_cat SET cprice='$cprice' WHERE cat_id='$id'");
		//
		$r->v.='</a>';
		break;
	case 'fixScSwitch':
		$id=(int)$_REQUEST['id'];
		if(!$id) {$r->fres=false; $r->fres_msg='Нет ID';}

		$d=$cc->getOne("SELECT fixSc,gr FROM cc_cat WHERE cat_id='$id'");
		$gr=$d['gr'];
		$r->v="<a href=\"#\" class=\"fixPrice\">";
		if($d['fixSc']) {
			$v=0;
			$r->v.='нет';
		}else{
			$v=1;
			$r->v.='да';
		}
		$cc->query("UPDATE cc_cat SET fixSc='$v' WHERE cat_id='$id'");
		//
		$r->v.='</a>';
		break;

	case 'ignoreUpdateSwitch':
		$id=(int)$_REQUEST['id'];
		if(!$id) {$r->fres=false; $r->fres_msg='Нет ID';}
		
		$d=$cc->getOne("SELECT cprice,ignoreUpdate,gr FROM cc_cat WHERE cat_id='$id'");
		$gr=$d['gr'];
		$r->v="<a href=\"#\" class=\"ignoreUpdate\">";
		if($d['ignoreUpdate']) {
			$v=0;
			$r->v.='нет';
			$r->fres_msg='Включено. Привязки ТИ для типоразмера удалены';
		}else{
			$v=1;
			$r->v.='да';
			$r->fres_msg='Выключено';
		}
		$cc->query("UPDATE cc_cat SET ignoreUpdate='$v', ti_id=0, ti_file_id=0 WHERE cat_id='$id'");
		$r->v.='</a>';
		break;

	case 'getSclByCatId':
		$cat_id=(int)@$_REQUEST['cat_id'];
		if(empty($cat_id)) {
			$r->fres=false;
			$r->fres_msg='ошибка - не передан cat_id';
			break;
		}
		if(in_array(Cfg::get('CAT_IMPORT_MODE'),array(2,3)))
			$r->scl=$cc->fetchAll("SELECT cc_cat_sc.sc,cc_cat_sc.price1,cc_cat_sc.price2,cc_cat_sc.price3,cc_suplr.name,cc_cat_sc.dt_added,cc_cat_sc.dt_upd, cc_cat_sc.ignored FROM cc_cat_sc INNER JOIN cc_suplr ON cc_cat_sc.suplr_id=cc_suplr.suplr_id WHERE cat_id='{$cat_id}' ORDER BY cc_suplr.name",MYSQL_ASSOC);

        $cc->que('cat_by_id',$cat_id);
        if($cc->qnum()){
            $cc->next();
            $r->dt_added=Tools::sDateTime($cc->qrow['dt_added']);
            $r->dt_upd=Tools::sDateTime($cc->qrow['dt_upd']);
            $r->upd_id=$cc->qrow['upd_id'];
        }
		break;
			
default: $r->fres=false; $r->fres_msg='BAD ACT_CASE '.$act;
}

ajxEnd();