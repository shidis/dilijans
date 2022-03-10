<?
include_once ('ajx_loader.php');

//sleep(1);

$cp->setFN('extra');
$cp->checkPermissions();

$cc=new CC_Ctrl();
$cc->load_extra();

$r->fres=true;
$r->fres_msg='';

$page = @$_REQUEST['page']; // get the requested page
$limit = @$_REQUEST['rows']; // get how many rows we want to have into the grid
$sidx = @$_REQUEST['sidx']; // get index row - i.e. user click to sort
$sord = @$_REQUEST['sord']; // get the direction
if(!$sidx) $sidx =1; 
$act=Tools::esc($_REQUEST['act']);
$gr=@$_REQUEST['gr'];
$oper=@$_REQUEST['oper'];
$brand_id=@$_REQUEST['brand_id'];

switch ($act){
case 'list_r': 
	if(!$gr || $gr>2) {
		$r->fres_msg='gr not set';
		$r->fres=false;
		break;
	}
	if(!$brand_id) {
		$r->fres_msg='brand_id not set';
		$r->fres=false;
		break;
	}
	if ($gr==1) $d=$cc->fetchAll("SELECT cc_cat.P1+'0' AS R FROM cc_cat LEFT JOIN cc_model ON cc_cat.model_id = cc_model.model_id WHERE cc_model.brand_id='{$brand_id}' GROUP BY cc_cat.P1 ORDER BY R");
		elseif ($gr==2) $d=$cc->fetchAll("SELECT cc_cat.P5+'0' AS R FROM cc_cat LEFT JOIN cc_model ON cc_cat.model_id = cc_model.model_id WHERE cc_model.brand_id='{$brand_id}'  GROUP BY cc_cat.P5 ORDER BY R");
	$r->records=count($d);
	if( $r->records ) $total_pages = ceil($r->records/$limit); else $total_pages = 0;
	if ($page > $total_pages) $page=$total_pages;
	$start = $limit*$page - $limit; // do not put $limit*($page - 1)
	if($start<0) $start=0;
	$r->page = $page;
	$r->total = $total_pages;
	$i=0;
//	$r->sql=$cc->sql_query;
	foreach($d as $v){
		$r->rows[$i]['id']=$v['R'];
    	$r->rows[$i]['cell']=array($v['R'],@$cc->extra_arr[1][$brand_id][$v['R']]['extra'],@$cc->extra_arr[1][$brand_id][$v['R']]['minExtra']);
    	$i++;
	}
break;


case 'list_sup': 
	if(!$gr || $gr>2) {
		$r->fres_msg='gr not set';
		$r->fres=false;
		break;
	}
	if(!$brand_id) {
		$r->fres_msg='brand_id not set';
		$r->fres=false;
		break;
	}
	$d=$cc->fetchAll("SELECT cc_sup.name, cc_sup.sup_id FROM cc_model RIGHT JOIN cc_sup ON cc_model.sup_id = cc_sup.sup_id WHERE cc_model.brand_id='{$brand_id}' GROUP BY cc_sup.name");
	$r->records=count($d);
	if( $r->records ) $total_pages = ceil($r->records/$limit); else $total_pages = 0;
	if ($page > $total_pages) $page=$total_pages;
	$start = $limit*$page - $limit; // do not put $limit*($page - 1)
	if($start<0) $start=0;
	$r->page = $page;
	$r->total = $total_pages;
	$i=0;
//	$r->sql=$cc->sql_query;

	foreach($d as $v){
		$r->rows[$i]['id']=$v['sup_id'];
    	$r->rows[$i]['cell']=array(Tools::unesc($v['name']),@$cc->extra_arr[2][$brand_id][$v['sup_id']]['extra'],@$cc->extra_arr[2][$brand_id][$v['sup_id']]['minExtra']);
    	$i++;
	}
break;


case 'update':
	if($oper!='edit') return;
	if(!$gr || $gr>2) {
		$r->fres_msg='gr not set';
		$r->fres=false;
		break;
	}
	if(!$brand_id) {
		$r->fres_msg='brand_id not set';
		$r->fres=false;
		break;
	}
	$extra_group=(int)$_REQUEST['extra_group'];
	if(!$extra_group) {
		$r->fres_msg='extra_group not set';
		$r->fres=false;
		break;
	}
	$id=(float)str_replace(',','.',@$_REQUEST['id']);
	$oe=trim(@$_REQUEST['extra']);
	$extra=(float)str_replace(',','.',@$_REQUEST['extra']);
	$minExtra=(float)str_replace(',','.',@$_REQUEST['minExtra']);
	
	if (!isset($cc->extra_arr[$extra_group][$brand_id]["$id"])){
		$cc->query("INSERT INTO cc_extra (brand_id, P_value, extra, extra_group,minExtra) VALUES('{$brand_id}','{$id}','$extra','$extra_group','$minExtra')");
	}else {
		$cc->query($sss="UPDATE cc_extra SET extra='$extra', minExtra='$minExtra' WHERE brand_id='{$brand_id}' AND P_value = '$id' AND extra_group='$extra_group'");
	}

	if(strcmp($oe,$extra)!==0) echo '0'; else echo '1';
	$cc->addCacheTask('prices',$gr);
	exit();
	break;

case 'list_sez': 
	$sez=(int)@$_REQUEST['S_value'];
	if(!$sez) {
		$r->fres_msg='sezon must be set';
		$r->fres=false;
		break;
	}
	if($gr!=1) {
		$r->fres_msg='gr must be eq 1';
		$r->fres=false;
		break;
	}
	if(!$brand_id) {
		$r->fres_msg='brand_id not set';
		$r->fres=false;
		break;
	}
	$d=$cc->fetchAll("SELECT cc_cat.P1+'0' AS R, cc_model.P1 AS sez FROM cc_cat LEFT JOIN cc_model ON cc_cat.model_id = cc_model.model_id WHERE cc_model.brand_id='{$brand_id}' AND cc_model.P1='$sez' GROUP BY cc_cat.P1 ORDER BY sez,R");
	$r->records=count($d);
	if( $r->records ) $total_pages = ceil($r->records/$limit); else $total_pages = 0;
	if ($page > $total_pages) $page=$total_pages;
	$start = $limit*$page - $limit; // do not put $limit*($page - 1)
	if($start<0) $start=0;
	$r->page = $page;
	$r->total = $total_pages;
	$i=0;
//	$r->sql=$cc->sql_query;
	foreach($d as $v){
		$r->rows[$i]['id']=$v['R'];
    	$r->rows[$i]['cell']=array($v['R'],@$cc->extra_arr[3][$brand_id][$v['sez']][$v['R']]['extra'].'',@$cc->extra_arr[3][$brand_id][$v['sez']][$v['R']]['minExtra'].'');
    	$i++;
	}
break;

case 'update_sez':
	if($oper!='edit') return;
	$sez=(int)@$_REQUEST['S_value'];
	if(!$sez) {
		$r->fres_msg='sezon must be set';
		$r->fres=false;
		break;
	}
	if($gr!=1) {
		$r->fres_msg='gr must be eq 1';
		$r->fres=false;
		break;
	}
	if(!$brand_id) {
		$r->fres_msg='brand_id not set';
		$r->fres=false;
		break;
	}
	$extra_group=(int)$_REQUEST['extra_group'];
	if(!$extra_group) {
		$r->fres_msg='extra_group not set';
		$r->fres=false;
		break;
	}
	$id=(float)str_replace(',','.',@$_REQUEST['id']);
	$oe=trim(@$_REQUEST['extra']);
	$extra=(float)str_replace(',','.',@$_REQUEST['extra']);
	$oe1=trim(@$_REQUEST['minExtra']);
	$minExtra=(float)str_replace(',','.',@$_REQUEST['minExtra']);
	
	if (!isset($cc->extra_arr[$extra_group][$brand_id][$sez]["$id"]))
		$cc->query("INSERT INTO cc_extra (brand_id, P_value, S_value, extra, minExtra, extra_group) VALUES('{$brand_id}','{$id}','$sez','$extra','$minExtra','$extra_group')");
	else 
		$cc->query("UPDATE cc_extra SET extra='$extra', minExtra='$minExtra' WHERE brand_id='{$brand_id}' AND P_value='{$id}' AND S_value='$sez' AND extra_group='$extra_group'");
	
	if(strcmp($oe,$extra)!==0 || strcmp($oe1,$minExtra)!==0) echo '0'; else echo '1';
	$cc->addCacheTask('prices',$gr);
	exit();
	break;

default: $r->fres=false; $r->fres_msg='BAD ACT ID '.$act;
}

ajxEnd();