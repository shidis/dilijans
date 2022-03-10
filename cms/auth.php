<?
//ob_start();

@define ('true_enter',1);
define('FROM_CMS',1);
require_once ($_SERVER['DOCUMENT_ROOT'].'/config/init.php');


error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);

ini_set('max_execution_time', 600);
ini_set('upload_max_filesize', (round(Cfg::get('max_file_size')/1000000)).'M');
ini_set('max_file_uploads','20');
ini_set('post_max_size', (round(Cfg::get('max_file_size')/1000000)).'M');

Request::checkDomain();

/*
echo 'POST'; print_r($_POST);
echo 'GET'; print_r($_GET);
echo 'REQUEST'; print_r($_REQUEST);
echo 'COOKIE'; print_r($_COOKIE);
echo 'SESSION'; print_r($_SESSION);
echo session_name().' = ';
echo session_id()."\r\n";
$buf=ob_get_contents();
ob_end_clean();
logs($buf);
*/

function logs($buf){
	$f=fopen($_SERVER['DOCUMENT_ROOT'].'/tmp/sessions.log','w');
	fwrite($f,date("Y-m-d H:i:s").':::	'.$buf."\r\n");
	fclose($f);
}

if(!CU::isLogged()){

	include 'login.php';
	
} else
    if(!defined('CMS_LEVEL_ACCESS')) define('CMS_LEVEL_ACCESS',CU::$roleId);

if(!defined('CMS_LEVEL_ACCESS')) die();

$_SESSION['CANED']=1;

@header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
@header("Pragma: no-cache");
@header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");	
@header("Cache-Control: no-store, no-cache, must-revalidate");
@header("Cache-Control: post-check=0, pre-check=0", false);

$cp=new App_CP;

