<?
require_once dirname(__FILE__).'/../auth.php';

$output=trim(@$_REQUEST['output']);
$order_id=@$_REQUEST['orderId'];

$os=new App_Orders();

$res=$os->openOrderFileByHash(@$_REQUEST['hash']);
echo $os->strMsg();