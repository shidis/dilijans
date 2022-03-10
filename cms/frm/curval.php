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
<title><?=Cfg::get('site_name')?> :: Кросс-курсы валют к рублю</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="../css/style.css" rel="stylesheet" type="text/css">
</head>
<body>
<?
if (isset($_POST['post'])){
	foreach ($_POST as $key=>$value) {
      $value=trim(Tools::esc($value));
	  $key=Tools::esc($key);
	  if($key=='newcur' && $value!=''){
		  	$cc->query("SELECT * FROM cc_cur WHERE name LIKE '$value'");
			if($cc->qnum()){
				echo "<p>ОШИБКА! Валюта с названием $key уже есть!</p>";
			}else{
				$cc->query("INSERT INTO cc_cur (name) VALUES ('$value')");
				$v=floatval($_POST['newval']);
				$key=mysql_insert_id();
				if (!$cc->query("INSERT INTO cc_curval (cur_id,value) VALUES('$key','$v')")) die ('Ошибка записи.');
			}
	  }elseif($key!='newval'){
	  	$v=floatval($value);
	  	if($v) {
			if (!$cc->get_curval($key))
				if (!$cc->query("INSERT INTO cc_curval (cur_id,value) VALUES('$key','$v')")) die ('Ошибка записи.');else;
			else
				if (!$cc->query("UPDATE cc_curval SET value='$v' WHERE (cur_id='$key')")) die ('Ошибка записи.');else;
	  	}
		}
	}
}
	
echo "<h3>Курсы валют</h3>";
?>
<p>Функция перенсена в раздел настроек сайта (Вкладка "курсы валют")</p>

</body>
</html>