<? include('../auth.php')?>
<? @define (true_enter,1);

require_once ($_SERVER['DOCUMENT_ROOT'].'/config/init.php');
require_once('../inc/utils.php');

$db=new DB();

$gr=@$_GET['gr'];
if(empty($gr)) die('gr require!');

if($gr==1) echo $fname='apTModels.txt'; else echo $fname='apDModels.txt';

$f=fopen ($fname,'r'); 

$s1=fread($f,filesize($fname));
$s1=preg_replace("/[\r\n]/",'',Tools::utf($s1));
$s1=explode('-----',$s1);
echo ' -- '.count($s1).' фрагментов<br>';
fclose($f);

$db->query("UPDATE ab_avto SET text{$gr}='' WHERE vendor_id>0 AND year_id=0 AND model_id=0");

$d=$db->fetchAll("select avto_id,name from ab_avto where vendor_id=0",MYSQLI_ASSOC);
$vendors=array();
foreach($d as $v){
	$vendors[$v['avto_id']]=Tools::unesc($v['name']);
}

$models=$db->fetchAll("SELECT avto_id,name,alt,vendor_id,hit_quant FROM ab_avto WHERE NOT H AND vendor_id>0 AND year_id=0 AND model_id=0 ORDER BY hit_quant DESC",MYSQLI_ASSOC);
echo 'Всего моделей '.count($models).'<br><br>';

$mi=0;
foreach($models	as $v){
	
		echo "$mi: {$vendors[$v['vendor_id']]} {$v['name']} -> ";
		
		if(count($s1)>$mi) {
			$t1=trim(Tools::esc($s1[$mi]));
			
			$db->query("UPDATE ab_avto SET text{$gr}='$t1' WHERE avto_id={$v['avto_id']}");
			echo "HQ={$v['hit_quant']} Len=".mb_strlen($t1).' OK<br>';
		} else echo "HQ={$v['hit_quant']} nooo--text<br>";
		$mi++;
		
	
}

if(count($s1)<$mi) {
	echo 'Не хватает текстов: '.($mi-count($s1));
}




