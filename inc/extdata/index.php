<?
$fname=@$_REQUEST['__q'];

//* у файла должно быть расширение. sname получаем, отбрасывая его
$fname=explode('.',$fname);
$ext=array_pop($fname);
$fname=join('.',$fname);

if($fname=='') exit;


@define (true_enter,1);
require_once ($_SERVER['DOCUMENT_ROOT'].'/config/init.php');

BotLog::detect();

$dsb=new CC_Dataset();

if(!is_object($ds=$dsb->initDatasetBySname($fname))) {
	echo 'Набор не инициализирован. ErrorCode='.$ds;
	exit;
}

// $ds->dataset == $dsb->c[$ds->dataset['class']];

$ds->export(isset($_GET['debug'])?true:false);

