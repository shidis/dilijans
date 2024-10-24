<? include('../auth.php')?>
<? @define (true_enter,1);

require_once ($_SERVER['DOCUMENT_ROOT'].'/config/init.php');
require_once('../inc/utils.php');

$db=new DB();

$f=fopen ('apTyres.txt','r'); 

$s1=fread($f,filesize('apTyres.txt'));
$s1=preg_replace("/[\r\n]/",'',Tools::utf($s1));
$s1=explode('-----',$s1);
echo count($s1).' фрагментов шин<br>';
fclose($f);

$f=fopen ('apDisks.txt','r'); 

$s2=fread($f,filesize('apDisks.txt'));
$s2=preg_replace("/[\r\n]/",'',Tools::utf($s2));
$s2=explode('-----',$s2);
echo count($s2).' фрагментов дисков<br><br>';

$db->query("UPDATE ab_avto SET text1='', text2=''");

$vendors=$db->fetchAll("SELECT avto_id,name,alt FROM ab_avto WHERE NOT H AND vendor_id=0",MYSQLI_ASSOC);
echo 'Всего марок '.count($vendors).'<br><br>';

$mi=0;
foreach($vendors as $v){
	
	$models=$db->fetchAll("SELECT avto_id,name,alt FROM ab_avto WHERE NOT H AND vendor_id={$v['avto_id']} AND year_id=0 AND model_id=0");
	
	foreach($models as $m){
		
		echo "$mi: {$v['name']} {$m['name']} -> ";
		
		if(count($s1)>$mi && count($s2)>$mi) {
			$t1=trim(Tools::esc($s1[$mi]));
			$t2=trim(Tools::esc($s2[$mi]));
			
			$db->query("UPDATE ab_avto SET text1='$t1',text2='$t2' WHERE avto_id={$m['avto_id']}");
			echo mb_strlen($t1).'/'.mb_strlen($t2).' OK<br>';
		} else echo '<br>';
		$mi++;
		
	}
	
}

if(count($s1)<$mi || count($s2)<$mi) {
	echo 'Не хватает текстов: '.($mi-max(count($s1),count($s2)));
}




