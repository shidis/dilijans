<? require_once '../auth.php';

include('../struct.php');

$cp->frm['name']='export_price';

$cp->checkPermissions();


header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");

require_once($_SERVER['DOCUMENT_ROOT'].'/cms/inc/utils.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Экспорт прайс-листов</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="../css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">
.d1{float:left; height:17px; width:290px; overflow:auto; margin-bottom:5px}
.d2{height:17px; width:110px; overflow:hidden; margin-bottom:5px; float:none}
</style>
</head>
<body>
<?
$cc->load_cur();
if(($f=fopen ('../../assets/download/tyres.csv','w'))===false) die('ОШИБКА ЗАПИСИ!');

$len=0;
$lines=0;
$cc->cat_view(array('gr'=>1,'select'=>'cc_model.hit_quant,cc_cat.bprice,cc_cat.cur_id'));

$hq=Cfg::get('cmsShowHitQuant');

if(!$cc->qnum()) die('Проблема с таблицами. Остановлен!');
echo '<p>ВНИМАНИЕ! Экспортируются типоразмеры имеющий статус "Видимый". Розничная цена рассчитывается с учетом всех скидок/наценок.</p>Дождитесь появления кнопки ЗАКРЫТЬ!<br><br>';
echo'<div class=d1>Создание прайса-шины...</div>';
$s='Марка;Название;Ширина;Высота;Радиус;Ин/Ис;Сезон;Шипы;Закупка;Валюта закупки;Розница, руб;На складе;ID поставщика;Тип авто;Суффикс;Бренд+модель;Полное название размера;Урл страницы модели;Урл страницы размера'.($hq?';Популярность модели':'')."\r\n";
$len+=fwrite($f,Tools::cp1251($s));
while ($cc->next()!==false) {
	switch ($cc->qrow['MP2']){
		case 1:$ta='легковой';break;
		case 4:$ta='внедорожник';break;
		case 3:$ta='микроавтобус';break;
		default: $ta='';
	}
	$url='http://'.Cfg::get('site_url').'/'.App_Route::_getUrl('tCat').'/'.$cc->qrow['brand_sname'].'/'.$cc->qrow['model_sname'].'/'.$cc->qrow['cat_sname'];
	$murl='http://'.Cfg::get('site_url').'/'.App_Route::_getUrl('tCat').'/'.$cc->qrow['brand_sname'].'/'.$cc->qrow['model_sname'];
	$fname=Tools::cutDoubleSpaces(trim(Tools::unesc("{$cc->qrow['bname']} {$cc->qrow['mname']} {$cc->qrow['P3']}/{$cc->qrow['P2']} ".($cc->qrow['P6']==1?'ZR':'R')."{$cc->qrow['P1']} {$cc->qrow['P7']} {$cc->qrow['csuffix']}")));
	$mname=Tools::cutDoubleSpaces(trim(Tools::unesc("{$cc->qrow['bname']} {$cc->qrow['mname']}")));
	$s="{$cc->qrow['bname']};{$cc->qrow['mname']}".($cc->qrow['msuffix']!=''?(' '.$cc->qrow['msuffix']):'').";".str_replace('.',',',$cc->qrow['P3']).";".str_replace('.',',',$cc->qrow['P2']).";".str_replace('.',',',($cc->qrow['P6']==1?'ZR':'R')."{$cc->qrow['P1']};{$cc->qrow['P7']};").($cc->qrow['MP1']==1?'лето':($cc->qrow['MP1']==2?'зима':'всесезон.')).";".($cc->qrow['MP3']?'ш':'').";".str_replace('.',',',$cc->qrow['bprice']).";".str_replace('.',',',$cc->cur_name[$cc->qrow['cur_id']][0]).";".str_replace('.',',',$cc->discount_price($cc->qrow['gr'],$cc->qrow['cprice'])).";{$cc->qrow['sc']};".$cc->qrow['sup_id'].";$ta;{$cc->qrow['csuffix']};{$mname};{$fname};{$murl};{$url}".($hq?";{$cc->qrow['hit_quant']}":'')."\r\n";
	$len+=fwrite($f,Tools::cp1251($s));
	$lines++;
	echo' ';flu();
}
fclose($f);
echo'<div class=d2><a target=_blank href="/assets/download/tyres.csv">Скачать ('.$lines.')</a></div>';

if(($f=fopen ('../../assets/download/disks.csv','w'))===false) die('ОШИБКА ЗАПИСИ!');
$len=0;
$lines=0;
echo'<div class=d1>Создание прайса-диски (без реплики)...</div>';
$cc->cat_view(array('gr'=>2,'add_query'=>'NOT cc_brand.replica','select'=>'cc_model.hit_quant,cc_cat.bprice,cc_cat.cur_id'));
$s='Марка;Название;Тип диска;Радиус;PCD;ДЦО;Вылет;Ширина;Диаметр;Цвет;Закупка;Валюта закупки;Розница, руб;На складе;ID поставщика;Бренд+модель;Полное название размера;Урл страницы модели;Урл страницы размера'.($hq?';Популярность модели':'')."\r\n";
$len+=fwrite($f,Tools::cp1251($s));
while ($cc->next()!==false) {
	switch ($cc->qrow['MP1']){
		case '1': $mp1='кованый';break;
		case '2': $mp1='литой';break;
		case '3': $mp1='штамп.';break;
		default: $mp1='';
	}
	$url='http://'.Cfg::get('site_url').'/'.App_Route::_getUrl('dCat').'/'.$cc->qrow['brand_sname'].'/'.$cc->qrow['model_sname'].'/'.$cc->qrow['cat_sname'];
	$murl='http://'.Cfg::get('site_url').'/'.App_Route::_getUrl('dCat').'/'.$cc->qrow['brand_sname'].'/'.$cc->qrow['model_sname'];

	$size="{$cc->qrow['P2']}Jx{$cc->qrow['P5']}";
	if($cc->qrow['P4']!='' && $cc->qrow['P6']!='') $sverlovka="{$cc->qrow['P4']}/{$cc->qrow['P6']}"; else $sverlovka='';
	$fname=Tools::cutDoubleSpaces(trim(Tools::unesc($cc->qrow['bname'].' '.$cc->qrow['mname'].' '.$size.($cc->qrow['P1']!=''?" ET{$cc->qrow['P1']}":'').' '.$sverlovka.' '.$cc->qrow['csuffix'])));
	
	$mname=Tools::cutDoubleSpaces(trim(Tools::unesc("{$cc->qrow['bname']} {$cc->qrow['mname']}")));
	$cprice=$cc->discount_price($cc->qrow['gr'],$cc->qrow['cprice']);
	$s="{$cc->qrow['bname']};{$cc->qrow['mname']}".($cc->qrow['msuffix']!=''?(' '.$cc->qrow['msuffix']):'').";$mp1;".str_replace('.',',',$cc->qrow['P5']).";".str_replace('.',',',$cc->qrow['P4']).";".str_replace('.',',',$cc->qrow['P6']).";".str_replace('.',',',$cc->qrow['P1']).";".str_replace('.',',',$cc->qrow['P2']).";".str_replace('.',',',$cc->qrow['P3']).";{$cc->qrow['csuffix']};".str_replace('.',',',$cc->qrow['bprice']).";".$cc->cur_name[$cc->qrow['cur_id']][0].";".str_replace('.',',',$cprice).";{$cc->qrow['sc']};".$cc->qrow['sup_id'].";{$mname};{$fname};{$murl};{$url}".($hq?";{$cc->qrow['hit_quant']}":'')."\r\n";
	$len+=fwrite($f,Tools::cp1251($s));
	$lines++;
	echo' ';flu();
}
fclose($f);
echo'<div class=d2><a target=_blank href="/assets/download/disks.csv">Скачать ('.$lines.')</a></div>';

if(($f=fopen ('../../assets/download/replica.csv','w'))===false) die('ОШИБКА ЗАПИСИ!');
$len=0;
$lines=0;
echo'<div class=d1>Создание прайса-диски реплика...</div>';
$cc->cat_view(array('gr'=>2,'cc_brand.replica'=>1,'select'=>'cc_model.hit_quant,cc_cat.bprice,cc_cat.cur_id'));
$s='Марка;Название;Тип диска;Радиус;PCD;ДЦО;Вылет;Ширина;Диаметр;Цвет;Закупка;Валюта закупки;Розница, руб;На скдладе;ID поставщика;Бренд+модель;Полное название размера;Урл страницы модели;Урл страницы размера'.($hq?';Популярность модели':'')."\r\n";
$len+=fwrite($f,Tools::cp1251($s));
while ($cc->next()!==false) {
	switch ($cc->qrow['MP1']){
		case '1': $mp1='кованый';break;
		case '2': $mp1='литой';break;
		case '3': $mp1='штамп.';break;
		default: $mp7='';
	}
	$url='http://'.Cfg::get('site_url').'/'.App_Route::_getUrl('dCat').'/'.$cc->qrow['brand_sname'].'/'.$cc->qrow['model_sname'].'/'.$cc->qrow['cat_sname'];
	$murl='http://'.Cfg::get('site_url').'/'.App_Route::_getUrl('dCat').'/'.$cc->qrow['brand_sname'].'/'.$cc->qrow['model_sname'];

	$size="{$cc->qrow['P2']}Jx{$cc->qrow['P5']}";
	if($cc->qrow['P4']!='' && $cc->qrow['P6']!='') $sverlovka="{$cc->qrow['P4']}/{$cc->qrow['P6']}"; else $sverlovka='';
	$fname=Tools::cutDoubleSpaces(trim(Tools::unesc($cc->qrow['bname'].' '.$cc->qrow['mname'].' '.$size.($cc->qrow['P1']!=''?" ET{$cc->qrow['P1']}":'').' '.$sverlovka.' '.$cc->qrow['csuffix'])));
	
	$mname=Tools::cutDoubleSpaces(trim(Tools::unesc("{$cc->qrow['bname']} {$cc->qrow['mname']}")));
	$cprice=$cc->discount_price($cc->qrow['gr'],$cc->qrow['cprice']);
	$s="{$cc->qrow['bname']};{$cc->qrow['mname']}".($cc->qrow['msuffix']!=''?(' '.$cc->qrow['msuffix']):'').";$mp1;".str_replace('.',',',$cc->qrow['P5']).";".str_replace('.',',',$cc->qrow['P4']).";".str_replace('.',',',$cc->qrow['P6']).";".str_replace('.',',',$cc->qrow['P1']).";".str_replace('.',',',$cc->qrow['P2']).";".str_replace('.',',',$cc->qrow['P3']).";{$cc->qrow['csuffix']};".str_replace('.',',',$cc->qrow['bprice']).";".$cc->cur_name[$cc->qrow['cur_id']][0].";".str_replace('.',',',$cprice).";{$cc->qrow['sc']};".$cc->qrow['sup_id'].";{$mname};{$fname};{$murl};{$url}".($hq?";{$cc->qrow['hit_quant']}":'')."\r\n";
	$len+=fwrite($f,Tools::cp1251($s));
	$lines++;
	echo' ';flu();
}
fclose($f);
echo'<div class=d2><a target=_blank href="/assets/download/replica.csv">Скачать ('.$lines.')</a></div>';

echo '<p><strong>Диски по поставщикам:</strong></p>';

$ccc=new CC_Ctrl;
$ccc->query("SELECT DISTINCTROW cc_sup.name, cc_sup.sup_id FROM cc_brand INNER JOIN ((cc_cat INNER JOIN cc_model ON cc_cat.model_id = cc_model.model_id) INNER JOIN cc_sup ON cc_model.sup_id = cc_sup.sup_id) ON cc_brand.brand_id = cc_model.brand_id WHERE (cc_model.gr=2) AND ((cc_cat.LD)<>'1') AND (cc_model.LD<>'1') AND (cc_brand.LD<>'1') AND (cc_brand.H<>'1')AND(cc_model.H<>'1')AND (cc_cat.H<>'1') ORDER BY cc_sup.name");
if($ccc->qnum()) while($ccc->next()!==false){
if(($f=fopen ('../../assets/download/disks_'.Tools::str2iso($ccc->qrow['name']).'.csv','w'))===false) die('ОШИБКА ЗАПИСИ!');
$len=0;
$lines=0;
echo'<div class=d1>прайс-диски '.$ccc->qrow['name'].'...</div>';
$cc->cat_view(array('gr'=>2,'cc_model.sup_id'=>$ccc->qrow['sup_id'],'select'=>'cc_model.hit_quant,cc_cat.bprice,cc_cat.cur_id'));
$s='Марка;Название;Тип диска;Радиус;PCD;ДЦО;Вылет;Ширина;Диаметр;Цвет;Закупка;Валюта закупки;Розница, руб;ID поставщика'.($hq?';Популярность модели':'')."\r\n";
$len+=fwrite($f,Tools::cp1251($s));
while ($cc->next()!==false) {
	switch ($cc->qrow['MP1']){
		case '1': $mp1='кованый';break;
		case '2': $mp1='литой';break;
		case '3': $mp1='штамп.';break;
		default: $mp1='';
	}
	$cprice=$cc->discount_price($cc->qrow['gr'],$cc->qrow['cprice']);
	$s="{$cc->qrow['bname']};{$cc->qrow['mname']}".($cc->qrow['msuffix']!=''?(' '.$cc->qrow['msuffix']):'').";$mp1;".str_replace('.',',',$cc->qrow['P5']).";".str_replace('.',',',$cc->qrow['P4']).";".str_replace('.',',',$cc->qrow['P6']).";".str_replace('.',',',$cc->qrow['P1']).";".str_replace('.',',',$cc->qrow['P2']).";".str_replace('.',',',$cc->qrow['P3']).";{$cc->qrow['csuffix']};".str_replace('.',',',$cc->qrow['bprice']).";".$cc->cur_name[$cc->qrow['cur_id']][0].";".str_replace('.',',',$cprice).";".$cc->qrow['sup_id']."\r\n";
	$s=str_replace('.',',',$s);
	$len+=fwrite($f,Tools::cp1251($s));
	$lines++;
	echo' ';flu();
}
fclose($f);
echo'<div class=d2><a target=_blank href="/assets/download/disks_'.Tools::str2iso($ccc->qrow['name']).'.csv">Скачать ('.$lines.')</a></div>';
}
?>
<br><br><input type="button" value="Закрыть окно" onclick="window.close()" />

</body>
</html>