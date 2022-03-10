<?
require_once dirname(__FILE__).'/../auth.php';

// http://mt.s/cms/ext/ordersDoc.php?do=pdf.orderDetail&output=file&orderId=609

$do=@$_REQUEST['do'];
$do=explode('.',$do);
$output=trim(@$_REQUEST['output']);
$order_id=@$_REQUEST['orderId'];

$os=new App_Orders();

if($do[0]=='pdf')
    call_user_func(array($os, 'exportPDF'), $order_id, @$do[1], $output);
else die('Wrong parameter. Halted.');

