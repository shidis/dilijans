<? require_once '../auth.php'?>
<?
if (!defined('true_enter')) die ("Direct access not allowed!");

error_reporting(E_ALL);
ini_set('log_errors', true);

require_once ($_SERVER['DOCUMENT_ROOT'].'/config/init.php');

require_once($_SERVER['DOCUMENT_ROOT'].'/cms/inc/global.php');

$cp=new App_CP();

$JsHttpRequest =new JsHttpRequest("utf-8");

function debug(){
    global $JsHttpRequest,$_RESULT;
    $_RESULT['debug']='';
    if (!$JsHttpRequest->ID) $_RESULT['debug'].="Zero loading ID: yes. \n";
    $_RESULT['debug'].="QUERY_STRING: {$_SERVER['QUERY_STRING']}. \n";
    $_RESULT['debug'].="Request method: {$_SERVER['REQUEST_METHOD']}. \n";
    $_RESULT['debug'].="Loader used: {$JsHttpRequest->LOADER}. \n";
    $_RESULT['debug'].="Uploaded file size: ".@$_FILES['file']['size']." . \n";
    $_RESULT['debug'].="_GET: ".print_r($_GET, 1);
    $_RESULT['debug'].="_POST: ".print_r($_POST, 1);
    $_RESULT['debug'].="_FILES: ".preg_replace('/(\[(name|size|tmp_name|type)\].*?)(\S+)$/m', '$1***', print_r($_FILES, 1));
    //$_RESULT['debug'].="_REQUEST: ".print_r($_REQUEST, 1);
    $_RESULT['debug']=nl2br($_RESULT['debug']);
}?>
