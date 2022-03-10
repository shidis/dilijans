<?
ini_set('max_execution_time', 200);

@define (true_enter,1);
require_once ($_SERVER['DOCUMENT_ROOT'].'/config/init.php');

$cc=new CC_Base();

$cc->query("SELECT cc_suplr.name AS suplrName, cc_cat_sc.sc AS sc, cc_cat_sc.price1,cc_cat_sc.price2,cc_cat_sc.price3, cc_brand.brand_id, cc_brand.name AS bname, cc_model.name AS mname, cc_model.P1 AS MP1, cc_model.P2 AS MP2, cc_model.P3 AS MP3, cc_model.suffix AS msuffix, cc_cat.suffix AS csuffix, cc_cat.P1+'0' AS P1, cc_cat.P2+'0' AS P2, cc_cat.P3+'0' AS P3, cc_cat.P4+'0' AS P4, cc_cat.P5+'0' AS P5, cc_cat.P6+'0' AS P6, cc_cat.P7, cc_brand.replica, cc_brand.sup_id AS bsup_id, cc_model.sup_id AS msup_id, cc_brand.sname AS brand_sname  FROM cc_suplr JOIN cc_cat_sc USING (suplr_id) JOIN cc_cat USING (cat_id) JOIN cc_model USING (model_id) JOIN cc_brand USING(brand_id) WHERE NOT cc_cat.LD AND NOT cc_model.LD AND NOT cc_brand.LD AND(cc_cat.gr=1)AND(cc_cat_sc.sc>0) ORDER BY cc_brand.name,cc_model.name, cc_cat.P3, cc_cat.P2, cc_cat.P1");

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
	$suplr=csv($cc->qrow['suplrName']);
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
	
	$sc=(int)$cc->qrow['sc'];
	$price1=Tools::n($cc->qrow['price1']);
	$price2=Tools::n($cc->qrow['price2']);
	$price3=Tools::n($cc->qrow['price3']);
	echo Tools::cp1251("$b;$m;$fname;$suplr;$w;$h;$r;$in;$is;$suf;$ship;$sez;$at;$sc;$price1;$price2;$price3").$e;
}



