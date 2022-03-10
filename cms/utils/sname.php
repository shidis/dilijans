<? include('../auth.php')?>
<? @define (true_enter,1);

require_once ($_SERVER['DOCUMENT_ROOT'].'/config/init.php');
require_once('../inc/utils.php');
?>Параметры: <br />
force - принудительный апдейт alias <br />
zero - только для пустых алиасов<br />
b - для брендов<br />
m - для моделей<br />
t - для размеров<br /><br /><br />
<?
$force=isset($_GET['force'])?1:0;

if(isset($_GET['b'])){
	$cc->query("UPDATE cc_model SET sname=''");
	$cc->query("select * from cc_brand where not LD");
	if($cc->qnum()) while($cc->next()!==false){
		echo "brand_id={$cc->qrow['brand_id']}, bname={$cc->qrow['name']}, sname=";
		if(($s=$cc->sname_brand(0,'',$force))===false) echo " ERROR"; else echo $s;
		echo '<br>';
		flu();
	}
}elseif(isset($_GET['m'])){
	//$cc->query("UPDATE cc_model SET sname=''");
	$cc->query("SELECT cc_model.*, cc_brand.brand_id, cc_brand.name AS bname, cc_brand.replica, cc_brand.sname AS brand_sname FROM cc_model INNER JOIN cc_brand ON cc_model.brand_id = cc_brand.brand_id where NOT cc_model.LD  ORDER BY cc_brand.name, cc_model.name");
	if($cc->qnum()) while($cc->next()!==false){
		echo "model_id={$cc->qrow['model_id']}, bname={$cc->qrow['bname']}, name={$cc->qrow['name']}, sname=";
		if(($s=$cc->sname_model(0,'',$force))===false) echo " ERROR"; else echo $s;
		echo '<br>';
		flu();
	}
}elseif(isset($_GET['t'])){
	//$cc->query("UPDATE cc_cat SET sname=''");
	$cc->query('SELECT cc_cat.*, cc_brand.name AS bname, cc_model.name FROM cc_cat INNER JOIN (cc_model INNER JOIN cc_brand ON cc_model.brand_id = cc_brand.brand_id) ON cc_cat.model_id=cc_model.model_id where (NOT cc_cat.LD)  ORDER BY cc_brand.name, cc_model.name');
	if($cc->qnum()) while($cc->next()!==false){
		echo "cat_id={$cc->qrow['cat_id']}, bname={$cc->qrow['bname']}, name={$cc->qrow['name']}, sname=";
		if(($s=$cc->sname_cat(0,'',$force))===false) echo " ERROR"; else echo $s;
		echo '<br>';
		flu();
	}
}elseif(isset($_GET['zero'])){
	$cc->query("SELECT cc_model.*, cc_brand.brand_id, cc_brand.name AS bname, cc_brand.replica, cc_brand.sname AS brand_sname FROM cc_model INNER JOIN cc_brand ON cc_model.brand_id = cc_brand.brand_id where NOT cc_model.LD  AND cc_model.sname='' ORDER BY cc_brand.name, cc_model.name");
	echo '<strong>Нулевые модели ('.$cc->qnum().'):</strong><br>';
	if($cc->qnum()) while($cc->next()!==false){
		echo "model_id={$cc->qrow['model_id']}, bname={$cc->qrow['bname']}, name={$cc->qrow['name']}, sname=";
		if(($s=$cc->sname_model(0,'',$force))===false) echo " ERROR"; else echo $s;
		echo '<br>';
		flu();
	}
	$cc->query("SELECT cc_cat.*, cc_brand.name AS bname, cc_model.name FROM cc_cat INNER JOIN (cc_model INNER JOIN cc_brand ON cc_model.brand_id = cc_brand.brand_id) ON cc_cat.model_id=cc_model.model_id where (NOT cc_cat.LD) and cc_cat.sname='' ORDER BY cc_brand.name, cc_model.name");
	echo '<strong>Нулевые типоразмеры ('.$cc->qnum().'):</strong><br>';
	if($cc->qnum()) while($cc->next()!==false){
		echo "cat_id={$cc->qrow['cat_id']}, bname={$cc->qrow['bname']}, name={$cc->qrow['name']}, sname=";
		if(($s=$cc->sname_cat(0,'',$force))===false) echo " ERROR"; else echo $s;
		echo '<br>';
		flu();
	
	}
}
$c=new DB;
if(isset($_GET['avto'])){
	echo 'avto....';
	if($force) $cc->query("SELECT * FROM `ab_avto`"); else $cc->query("SELECT * FROM `ab_avto` WHERE sname = ''");
	while($cc->next()!==false){
		$s=Tools::str2iso($cc->qrow['name'],-1,'');
		$c->query("update ab_avto set sname='$s' where avto_id='{$cc->qrow['avto_id']}'");
		echo "{$cc->qrow['name']}: $s<br>";
		flu();
	}
}
?>