<?
@define (true_enter,1);
if(!defined('FROM_CMS')) define('FROM_CMS',1);

error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);

if(@$_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') die('HALT(0)');

require_once ($_SERVER['DOCUMENT_ROOT'].'/config/init.php');

header('Content-Type: text/html; charset=utf-8');

$login=@$_REQUEST['login'];
$pw=@$_REQUEST['pw'];

$r=array();



if(CU::login($login,$pw)){
	$r['pass']=1;
}else{
	$r['pass']=0;
	sleep(2);
}



echo json_encode($r);