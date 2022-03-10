<? require_once '../auth.php'?>
<?
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="../css/style.css" rel="stylesheet" type="text/css">
</head>
<body>
<?
@define (true_enter,1);
require_once ($_SERVER['DOCUMENT_ROOT'].'/config/init.php');

require_once($_SERVER['DOCUMENT_ROOT'].'/cms/inc/utils.php');

if(@$_GET['inis']==''){?>
Не задан параметр ['inis']
<? }else{
$r=CC_inis::explain_1($_GET['inis']);
if($r[0])echo "<b>{$r[1]}</b> - {$r[2]}"; else echo 'Не получилось получить расшифровку';?>
<p>CC_inis::check()=<? $ic=CC_inis::check($_GET['inis']); if($ic) echo 'true'; else echo 'false';?></p>
<script>document.title='ИС/ИН :: <?=$_GET['inis']?>'</script>
<? }?>
<br><br><br><form><input type="button" value="Закрыть окно" onClick="window.close()"></form>
</body>
</html>
