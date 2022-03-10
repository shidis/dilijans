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
<title><?=Cfg::get('site_name')?> :: Обновление БД</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="../css/style.css" rel="stylesheet" type="text/css">
</head>
<body>
<? if(@$_POST['act']=='ok'){?>
<p>Дождитесь появления кнопки ЗАКРЫТЬ ОКНО...</p>
<?
$cc->update_base();
?>
<br><strong>Базы обновлены.</strong>
<br>
<br>
<input type="button" value="Закрыть окно" onClick="window.close();">
<? }else{?>
<div class="rama">
<p id="bold" align="center">Нажав кнопку ДА, будет произведен пересчет всех фильтров и цен, с учетом всевозможных наценок и курсов валют. Эта процедура может занять некоторое время.</p> <p align="center" id="bold">Выполнять пересчет следует после правки непосредственно базы данных. В остальных случаях пересчет цен выполняется автоматически по расписанию (раз в 15 минут).</p>
</div>
<p id="subh" align="center">Выполнить обновление?</p>
<form method="post" name="form1">
<input type="hidden" name="act" value="no">
<table width="100%" border="0" cellspacing="0" cellpadding="10">
  <tr>
    <td align="center"><input type="submit" value="ДА" onClick="document.forms['form1'].act.value='ok'"></td>
    <td align="center"><input type="submit" value="НЕТ" onClick="window.close()"></td>
  </tr>
</table>

</form>
<? }?>
</body>
</html>