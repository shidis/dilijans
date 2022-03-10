<?
include_once ('ajx_loader.php');

$cp->setFN('order_discount');
$cp->checkPermissions();

//sleep(1);


$os=new DB();

$r->fres=true;
$r->fres_msg='';

$page = @$_REQUEST['page']; // get the requested page
$lim = @$_REQUEST['rows']; // get how many rows we want to have into the grid
$sidx = @$_REQUEST['sidx']; // get index row - i.e. user click to sort
$sord = @$_REQUEST['sord']; // get the direction
if(!$sidx) $sidx =1; 
$act=Tools::esc(@$_REQUEST['act']);
if(@$_REQUEST['oper']=='add') $act='add';
if(@$_REQUEST['oper']=='edit') $act='edit';
if(@$_REQUEST['oper']=='del') $act='del';

switch ($act){

case 'list': 
	$d=$os->fetchAll("SELECT * FROM os_limit ORDER BY type,lim");
	$r->records=count($d);
	if( $r->records ) $total_pages = ceil($r->records/$lim); else $total_pages = 0;
	if ($page > $total_pages) $page=$total_pages;
	$start = $lim*$page - $lim; // do not put $lim*($page - 1)
	if($start<0) $start=0;
	$r->page = $page;
	$r->total = $total_pages;
	$i=0;
	$r->rows=array();
//	$r->sql=$cc->sql_query;
	foreach($d as $v){
		$r->rows[$i]['id']=$v['limit_id'];
    	$r->rows[$i]['cell']=array($v['type']==1?'При кол-ве заказов от':'При общей сумме заказов от (руб)',$v['lim'],$v['value']);
    	$i++;
	}
break;

case 'edit':
	$id=intval(@$_REQUEST['id']);
	$type=intval(@$_REQUEST['type']);
	$lim=floatval(@$_REQUEST['lim']);
	$value=floatval(@$_REQUEST['value']);
	$os->query("UPDATE os_limit SET type='$type', lim='$lim', value='$value' WHERE limit_id='$id'");
	echo '0';
	exit;
	break;
		
case 'add':
	$type=intval(@$_REQUEST['type']);
	$lim=floatval(@$_REQUEST['lim']);
	$value=floatval(@$_REQUEST['value']);
	$os->query("INSERT INTO os_limit (type,lim,value) VALUES('$type','$lim','$value')");
	echo '0';
	exit;
	break;

case 'del':
	$id=intval(@$_REQUEST['id']);
	$os->query("DELETE FROM os_limit WHERE limit_id='$id'");
	echo '0';
	exit;
	break;
	
default: $r->fres=false; $r->fres_msg='BAD ACT ID '.$act;
}
ajxEnd();