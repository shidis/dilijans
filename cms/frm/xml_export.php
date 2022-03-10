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
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="../css/style.css" rel="stylesheet" type="text/css">
</head>
<body >
<? 
foreach ($_GET as $key=>$value) $$key=$value;
if(!is_dir(Cfg::_get('root_path').'/assets/download/') && !mkdir(Cfg::_get('root_path').'/assets/download/')) die('Невозможно создать диррикторию для загрузки каталога!');
if(!count(@$_POST)){
if(!isset($sea_name)) $sea_name=''; else echo $sea_name=($sea_name);
if(@$only_sc!='') $sc='sc>0'; else $sc='';
if (isset($di)){
	if (!isset($check_ET)) $check_ET=0;
	$num=$cc->cat_view(array('gr'=>$gr,'brand_id'=>$brand_id_2,'delta_ET'=>$check_ET, 'P1'=>$P1_2,'P2'=>$P2_2, 'P3'=>$P3_2,'P4'=>$P4_2, 'P5'=>$P5_2, 'P6'=>$P6_2, 'M1'=>$MP1_2,'add_query'=>$sc,'H'=>0,'sea_name'=>$sea_name,'sup_id'=>$sup_id_2,'cc_brand.replica'=>@$replica));
 } elseif (isset($sh)){
	if (!isset($MP1_1)) $MP1_1='';
	if (!isset($MP3_1)) $MP3_1='';
	if (!isset($P6_1)) $P6_1='';
	$num=$cc->cat_view(array('gr'=>$gr,'brand_id'=>$brand_id_1, 'P1'=>$P1_1,'P2'=>$P2_1, 'P3'=>$P3_1,'M1'=>$MP1_1, 'M3'=>$MP3_1, 'P6'=>$P6_1, 'P7'=>$P7_1,'add_query'=>$sc,'H'=>0,'sea_name'=>$sea_name,'sup_id'=>$sup_id_1));
}else{
	$num=$cc->cat_view(array('gr'=>$gr,'H'=>0));
}
echo"<p>Найдено <strong>$num</strong> типразмеров</p><br>";
if(!$num) exit();
$cc->load_mspez($gr);
$cc->load_sup();
$cc->load_class($gr);
$cc->load_cur();
$brand_l=array();
$mspez_l=array();
$sup_l=array();
$class_l=array();
$cur_l=array();
$model_l=array();




echo '<p>наценки НЕ экспортируются.</p>Складываем в XML файл...';flu();
$dom = new DOMDocument('1.0', 'UTF-8');

$root = $dom->createElement('ROOT');
$root->SetAttribute('gr',$gr);
$dom->appendChild($root);
$brands = $dom->createElement('BRANDS');
$root->appendChild($brands);
$cur = $dom->createElement('CUR');
$root->appendChild($cur);
$class = $dom->createElement('CLASS');
$root->appendChild($class);
$mspez = $dom->createElement('MSPEZ');
$root->appendChild($mspez);
$sup = $dom->createElement('SUP');
$root->appendChild($sup);
$models = $dom->createElement('MODELS');
$root->appendChild($models);
$tipos=$dom->createElement('TIPOS');
$root->appendChild($tipos);

$afbrand=App_TFields::get('cc_brand','all',$gr);
$afmodel=App_TFields::get('cc_model','all',$gr);
$afcat=App_TFields::get('cc_cat','all',$gr);

while($cc->next()!==false){
	$tipo = $dom->createElement('t');
	$tipo->SetAttribute('cat_id',$cc->qrow['cat_id']);
	$tipo->SetAttribute('model_id',$cc->qrow['model_id']);
	$tipo->SetAttribute('cur_id',$cc->qrow['cur_id']);
	$tipo->SetAttribute('H',$cc->qrow['CH']);
	$n = $dom->createElement('csuffix');
	$n->appendChild($dom->createCDATASection(Tools::utf($cc->qrow['csuffix'])));
	$tipo->appendChild($n);
	$n = $dom->createElement('P1',Tools::utf($cc->qrow['P1']));
	$tipo->appendChild($n);
	$n = $dom->createElement('P2',Tools::utf($cc->qrow['P2']));
	$tipo->appendChild($n);
	$n = $dom->createElement('P3',Tools::utf($cc->qrow['P3']));
	$tipo->appendChild($n);
	$n = $dom->createElement('P4',Tools::utf($cc->qrow['P4']));
	$tipo->appendChild($n);
	$n = $dom->createElement('P5',Tools::utf($cc->qrow['P5']));
	$tipo->appendChild($n);
	$n = $dom->createElement('P6',Tools::utf($cc->qrow['P6']));
	$tipo->appendChild($n);
	$n = $dom->createElement('P7',Tools::utf($cc->qrow['P7']));
	$tipo->appendChild($n);
	$n = $dom->createElement('bprice',Tools::utf($cc->qrow['bprice']));
	$tipo->appendChild($n);
	$n = $dom->createElement('cprice',Tools::utf($cc->qrow['cprice']));
	$tipo->appendChild($n);
	$n = $dom->createElement('sc',Tools::utf($cc->qrow['sc']));
	$tipo->appendChild($n);
	$n = $dom->createElement('scprice',Tools::utf($cc->qrow['scprice']));
	$tipo->appendChild($n);
	$n = $dom->createElement('ft');
	$n->appendChild($dom->createCDATASection(Tools::utf($cc->qrow['ft'])));
	$tipo->appendChild($n);
	foreach($afcat as $kc=>&$vc){
		$n = $dom->createElement($kc);
		$n->appendChild($dom->createCDATASection(Tools::utf($cc->qrow[$kc])));
		$tipo->appendChild($n);
	}
	$tipos->appendChild($tipo);
	
	if(!isset($model_l[$cc->qrow['model_id']])){
		$model_l[$cc->qrow['model_id']]='';	
		$m = $dom->createElement('m');
		$m->SetAttribute('model_id',$cc->qrow['model_id']);
		$m->SetAttribute('brand_id',$cc->qrow['brand_id']);
		$m->SetAttribute('sup_id',$cc->qrow['sup_id']);
		$m->SetAttribute('mspez_id',$cc->qrow['mspez_id']);
		$m->SetAttribute('class_id',$cc->qrow['class_id']);
		$m->SetAttribute('P1',$cc->qrow['MP1']);
		$m->SetAttribute('P2',$cc->qrow['MP2']);
		$m->SetAttribute('P3',$cc->qrow['MP3']);
		$models->appendChild($m);
		$n = $dom->createElement('name');
		$n->appendChild($dom->createCDATASection(Tools::utf($cc->qrow['mname'])));
		$m->appendChild($n);
		$n = $dom->createElement('msuffix');
		$n->appendChild($dom->createCDATASection(Tools::utf($cc->qrow['msuffix'])));
		$m->appendChild($n);
		$n = $dom->createElement('text');
		$n->appendChild($dom->createCDATASection(Tools::utf($cc->qrow['text'])));
		$m->appendChild($n);
		$m->appendChild($dom->createElement('img1',
			is_file(Cfg::get('cc_upload_path').'/'.$cc->qrow['img1'])?
					("http://{$_SERVER['SERVER_NAME']}/".Cfg::get('cc_upload_dir').'/'.$cc->qrow['img1']):''
		));
		$m->appendChild($dom->createElement('img2',@is_file(Cfg::get('cc_upload_path').'/'.$cc->qrow['img2'])?("http://{$_SERVER['SERVER_NAME']}/".Cfg::get('cc_upload_dir').'/'.$cc->qrow['img2']):''));
		$m->appendChild($dom->createElement('img3',@is_file(Cfg::get('cc_upload_path').'/'.$cc->qrow['img3'])?("http://{$_SERVER['SERVER_NAME']}/".Cfg::get('cc_upload_dir').'/'.$cc->qrow['img3']):''));
		foreach($afmodel as $km=>&$vm){
			$n = $dom->createElement($km);
			$n->appendChild($dom->createCDATASection(Tools::utf($cc->qrow[$km])));
			$m->appendChild($n);
		}
	}
	if(!isset($brand_l[$cc->qrow['brand_id']])){
		$brand_l[$cc->qrow['brand_id']]='';	
		$n = $dom->createElement('b');
		$n->SetAttribute('brand_id',$cc->qrow['brand_id']);
		$m = $dom->createElement('name');
		$m->appendChild($dom->createCDATASection(Tools::utf($cc->qrow['bname'])));
		$n->appendChild($m);
		foreach($afbrand as $kb=>&$vb){
			$b = $dom->createElement($kb);
			$b->appendChild($dom->createCDATASection(Tools::utf($cc->qrow[$kb])));
			$n->appendChild($b);
		}
		$brands->appendChild($n);
		
	}
	if(isset($mspez_arr[$cc->qrow['mspez_id']]) && !isset($mspez_l[$cc->qrow['mspez_id']])){
		$mspez_l[$cc->qrow['mspez_id']]='';	
		$n = $dom->createElement('m');
		$n->SetAttribute('mspez_id',$cc->qrow['mspez_id']);
		$n->appendChild($dom->createCDATASection(Tools::utf($mspez_arr[$cc->qrow['mspez_id']])));
		$mspez->appendChild($n);
	}
	if(isset($cc->sup_arr[$cc->qrow['sup_id']]) && $cc->qrow['sup_id'] && !isset($sup_l[$cc->qrow['sup_id']])){
		$sup_l[$cc->qrow['sup_id']]='';	
		$n = $dom->createElement('s');
		$n->SetAttribute('sup_id',$cc->qrow['sup_id']);
		$n->appendChild($dom->createCDATASection(Tools::utf($cc->sup_arr[$cc->qrow['sup_id']])));
		$sup->appendChild($n);
	}
	if(isset($cc->class_arr[$cc->qrow['class_id']]) && !isset($class_l[$cc->qrow['class_id']])){
		$class_l[$cc->qrow['class_id']]='';	
		$n = $dom->createElement('s');
		$n->SetAttribute('class_id',$cc->qrow['class_id']);
		$n->appendChild($dom->createCDATASection(Tools::utf($cc->class_arr[$cc->qrow['class_id']])));
		$class->appendChild($n);
	}
	if(isset($cc->cur_name[$cc->qrow['cur_id']]) && !isset($cur_l[$cc->qrow['cur_id']])){
		$cur_l[$cc->qrow['cur_id']]='';	
		$n = $dom->createElement('s');
		$n->SetAttribute('cur_id',$cc->qrow['cur_id']);
		$n->appendChild($dom->createCDATASection(Tools::utf($cc->cur_name[$cc->qrow['cur_id']][0])));
		$cur->appendChild($n);
	}
}
$s=$dom->saveXML();
$fname=($gr==1?'tyres_':'disks_').(date("Y-m-d_H_i_s")).'.xml';
$f=fopen(Cfg::_get('root_path').'/assets/download/'.$fname,'wb');
if (($res=fwrite($f, $s,mb_strlen($s))) === FALSE) {
       echo "Не могу произвести запись в файл.";
}else {
	echo 'OK. Записано '.$res.' байтов.';
	echo '<p>Ссылка на файл: <a target=_blank href="/assets/download/'.$fname.'">'.$fname.'</a></p>';
}
fclose($f);
} //count _POST
if(@$_POST['act']=='del'){
	@unlink(Cfg::_get('root_path').'/assets/download/'.$_POST['fname']);
}
if ($handle = opendir(Cfg::_get('root_path').'/assets/download/')) {
?>
<form method="post" name="frm">
<input type="hidden" name="act" value="-1">
<input type="hidden" name="fname" value="">
<table border="0" cellspacing="0" cellpadding="10">
<tr>
    <th>Размер, байт(ов)</th>
	<th>Имя файла</th>
	<th>Удалить</th>
</tr>
<? $l=0;
while (false !== ($file = readdir($handle))) if(is_file(Cfg::_get('root_path').'/assets/download/'.$file)){?>
<tr><td align="center" <?=td_class($l++)?>><?=filesize(Cfg::_get('root_path').'/assets/download/'.$file)?></td>
<td <?=td_class($l)?>><a target=_blank href="/assets/download/<?=$file?>"><?=$file?></a></td>
<td align="center" <?=td_class($l)?>><input type="image" src="../img/b_drop.png" onClick="document.forms['frm'].act.value='del';document.forms['frm'].fname.value='<?=$file?>'"></td>
</tr>
<? }?>
</table>
</form>
<? }?>
</body>
</html>
