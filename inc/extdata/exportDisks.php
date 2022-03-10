<?
ini_set('max_execution_time', 200);

@define (true_enter,1);
require_once ($_SERVER['DOCUMENT_ROOT'].'/config/init.php');

$cc=new CC_Ctrl();

$cc->query("SELECT ".(isset(TFields::$fields['cc_cat']['code'])?' cc_cat.code,':'').(isset(TFields::$fields['cc_cat']['app'])?' cc_cat.app,':'')." cc_suplr.name AS suplrName, cc_cat_sc.sc AS sc, cc_cat_sc.price1,cc_cat_sc.price2,cc_cat_sc.price3, cc_brand.brand_id, cc_brand.name AS bname, cc_model.name AS mname, cc_model.P1 AS MP1, cc_model.suffix AS msuffix, cc_cat.suffix AS csuffix, cc_cat.P1+'0' AS P1, cc_cat.P2+'0' AS P2, cc_cat.P3+'0' AS P3, cc_cat.P4+'0' AS P4, cc_cat.P5+'0' AS P5, cc_cat.P6+'0' AS P6, cc_cat.P7, cc_brand.replica, cc_brand.sup_id AS bsup_id, cc_model.sup_id AS msup_id, cc_brand.sname AS brand_sname  FROM cc_suplr JOIN cc_cat_sc USING (suplr_id) JOIN cc_cat USING (cat_id) JOIN cc_model USING (model_id) JOIN cc_brand USING(brand_id) WHERE NOT cc_cat.LD AND NOT cc_model.LD AND NOT cc_brand.LD AND(cc_cat.gr='2')AND(cc_cat_sc.sc>0) ORDER BY cc_brand.name,cc_model.name, cc_cat.P5, cc_cat.P4, cc_cat.P6");

function csv($s,$strip=true)
{
	if($strip) $s=stripslashes($s);
	$s=str_replace('"','""',$s);
	if(mb_strpos($s,'"')!==false) $s='"'.$s.'"';
	return $s;
}

$fname='exportDisks.csv';

header('Content-type: text/csv');
header("Content-disposition: attachment; filename=$fname");
header('Pragma: public');

$e="\r\n";

$cc->load_sup(0);

echo Tools::cp1251("Бренд;Модель;Название;Компания;Ш;Д;Крепеж;PCD;PCD(двойной);ET;Dia;Цвет;Тип диска;Склад;Опт;Розница;Собственная розница").$e;

while($cc->next()!==false){
	$suplr=csv($cc->qrow['suplrName']);
	$replica=(int)$cc->qrow['replica'];
	if($replica && $cc->qrow['msup_id']) $b=@$cc->sup_arr[$cc->qrow['msup_id']]; else $b='';
	if(empty($b)) 
		if($replica) $b='Replica';
		else $b=csv($cc->qrow['bname']);
	if($cc->qrow['brand_id']==18 && isset(TFields::$fields['cc_cat']['code']) && $cc->qrow['code']!='') $code=' ('.$cc->qrow['code'].')'; else $code='';
	if($replica) {
		if(isset(TFields::$fields['cc_cat']['app']) && $cc->qrow['app']!='') $app=' '.$cc->qrow['app']; else $app='';
		$m=csv($cc->qrow['bname'].' '.'('.$cc->qrow['mname'].')'.$app);
	}else $m=csv($cc->qrow['mname'].$code);
	$j=Tools::n($cc->qrow['P2']);
	$rad=Tools::n($cc->qrow['P5']);
	$pcd=Tools::n($cc->qrow['P4']);
	$dco=Tools::n($cc->qrow['P6']);
	$dia=Tools::n($cc->qrow['P3']);
	$et=Tools::n($cc->qrow['P1']);
	$suf=trim(csv($cc->qrow['csuffix']));
	
//	BSA 235 7,5x16 5x112 ET 37 Dia 66,6 (VSC/A)
	$fname=trim("$b $m {$j}x{$rad} {$pcd}x{$dco} ET {$et}".($dia!=0?" Dia $dia":'').($suf!=''?" ($suf)":''));
	switch($cc->qrow['MP1']){
		case 2: $dt='литой'; break;
		case 1: $dt='кованый'; break;
		case 3: $dt='стальной'; break;
		default: $dt='';
	}
	$sc=(int)$cc->qrow['sc'];
	$price1=Tools::n($cc->qrow['price1']);
	$price2=Tools::n($cc->qrow['price2']);
	$price3=Tools::n($cc->qrow['price3']);
	echo Tools::cp1251("$b;$m;$fname;$suplr;$j;$rad;$pcd;$dco;0;$et;$dia;$suf;$dt;$sc;$price1;$price2;$price3").$e;
}




