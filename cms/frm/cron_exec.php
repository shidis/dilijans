<? require_once '../auth.php'?>
<?
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
?><?
@define (true_enter,1);
require_once ($_SERVER['DOCUMENT_ROOT'].'/config/init.php');

require_once($_SERVER['DOCUMENT_ROOT'].'/cms/inc/utils.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?=Cfg::get('site_name')?> :: Запуск крон</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="../css/style.css" rel="stylesheet" type="text/css">
</head>
<body>
<p>Это действие выполняется автоматически каждые 15 минут по заданию CRONTAB. Используйте эту функцию если нет возможности подождать.</p>
<p>Дождитесь появления кнопки ЗАКРЫТЬ ОКНО...</p>
<?
flu();
$cc->execCacheTasks();
?>
<br>
<br>
<input type="button" value="Закрыть окно" onClick="window.close();">
</body>
</html>