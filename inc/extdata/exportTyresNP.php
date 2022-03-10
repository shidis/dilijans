<?
ini_set('max_execution_time', 200);

@define (true_enter,1);
require_once ($_SERVER['DOCUMENT_ROOT'].'/config/init.php');

$cc=new CC_Base();

$n=$cc->cat_view(array(
	'gr'=>1,
	'notH'=>0,
	'add_query'=>'sc>0',
	'nolimits'=>1,
	'select'=>'cc_cat.fixPrice,bprice'
));

function csv($s,$strip=true)
{
	if($strip) $s=stripslashes($s);
	$s=str_replace('"','""',$s);
	if(mb_strpos($s,'"')!==false) $s='"'.$s.'"';
	return $s;
}

$fname='exportTyres.csv';

header('Content-type: text/csv');
header("Content-disposition: attachment; filename=$fname");
header('Pragma: public');

$e="\n";
echo Tools::cp1251("Бренд;Модель;Название;Компания;Ш;П;Д;ИН;ИС;Усил.;Шип;Сезон;Тип ТС;Склад;Опт;Розница;Собственная розница").$e;
while($cc->next()!==false){
	$sup=Cfg::$config['site_name'];
	$b=csv($cc->qrow['bname']);
	$m=csv($cc->qrow['mname']);
	$w=Tools::n($cc->qrow['P3']);
	$h=Tools::n($cc->qrow['P2']);
	$r=Tools::n($cc->qrow['P1']);
	$inis=$cc->qrow['P7'];
	if(preg_match("~^([0-9\/]*)([a-zA-Z]*)$~",$inis,$mm)){
		$in=$mm[1];
		$is=$mm[2];
	}else {
		$is='';
		$in='';
	}
	$suf=trim(csv($cc->qrow['csuffix']));
	$fname=trim("$b $m $w/$h ".($cc->qrow['P6']?'Z':'')."R$r $inis $suf");
	switch($cc->qrow['MP1']){
		case 1: $sez='летняя'; break;
		case 2: $sez='зимняя'; break;
		case 3: $sez='всесезонная'; break;
		default: $sez='';
	}
	if($cc->qrow['MP3']) $ship='шип'; else $ship='нешип';
	switch($cc->qrow['MP2']){
		case 1: $at='легковой'; break;
		case 2: $at='внедорожник'; break;
		case 3: $at='микроавтобус'; break;
		default: $at='';
	}
	$sc=$cc->qrow['sc'];
	$price3=0;
	if($cc->qrow['fixPrice']){
		$price1=(int)$cc->qrow['bprice'];
		$price2=(int)$cc->qrow['cprice'];
	}else{
		$price1=(int)$cc->qrow['bprice'];
		$price2=0;
	}
	echo Tools::cp1251("$b;$m;$fname;$sup;$w;$h;$r;$in;$is;$suf;$ship;$sez;$at;$sc;$price1;$price2;$price3").$e;
}




