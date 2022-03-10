<? 
require_once '../auth.php';
include('../struct.php');

$gr=@$_GET['gr'];
if($gr!=1 && $gr!=2) die('gr incorrect. exit.');

$cp->frm['title']='Добавить размер '.($gr==1?'шины':'диска'); 
$cp->frm['name']='add_cat';

$cp->checkPermissions();

cp_head();
cp_css();
cp_js();
cp_body();
cp_title();

$cc=new CC_Ctrl();

if (isset($_POST['add_cpost']))
{
	foreach ($_POST as $key=>$value) if(!is_array($value)) $$key=Tools::esc($value); else $$key=($value);

	if($model_id==0 || $brand_id==0) {warn('Модель и/или бренд не задан'); $err=true;}
	if($gr==2 && (@$P2=='' || @$P5=='')) {warn('Поля JxR должны быть заполнены'); $err=true;}
	if($gr==1 && (@$P1=='' || @$P2=='' || @$P3=='')) {warn('Поля ширина, высота, диаметр должны быть заполнены'); $err=true;}
	if (!isset($P4)) $P4=0;
	if (!isset($P5)) $P5=0;
	if (!isset($P6)) $P6=0;
	if (!isset($P7)) $P7='';
	if($gr==1) $P4=$cc->isCinSuffix($suffix);
	if(!@$err){
		$base_price=floatval($base_price);
		$scprice=floatval($scprice);
		if($cprice!=='') $fixPrice=1; else $fixPrice=0;
		$cprice=floatval($cprice);
		$a=App_TFields::DBinsert('cc_cat',@$af,$gr);
        $dt=Tools::dt();
		if (!$cc->query("INSERT INTO cc_cat (model_id, gr, dt_added, suffix, P1,P2,P3,P4,P5,P6,P7, bprice, cprice, fixPrice, fixSc, cur_id,scprice{$a[0]}) VALUES('$model_id','$gr', '$dt','$suffix','$P1','$P2','$P3','$P4','$P5','$P6','$P7','$base_price','$cprice','$fixPrice','$fixSc','$cur_id','$scprice'{$a[1]})"))
			note('Ошибка записи');
		else {
			note('Типоразмер добавлен');
			$cat_id=$cc->lastId();
			$cc->sname_cat($cat_id);
			if(!$fixPrice) $cc->extra_price_update($cat_id);
			if($gr==1 && Cfg::get('INIS_S1S2')) CC_inis::modelUpdate($model_id);
			if($gr==2 && isset($cc->RDisk)) $cc->RDisk->modelUpdate($model_id);
			if($gr==2 && isset($cc->RTyre)) $cc->RTyre->modelUpdate($model_id);
			if(isset($cc->intPrice)) $cc->intPrice->modelUpdate($model_id);
			//if(Cfg::get('model_SC')) CC_ModelSC::modelUpdate($model_id);
			$cc->addCacheTask('sizes',$gr);
		}
	}
}		

?>
<style type="text/css">
	.msg-block{
		margin:5px; 0;
	}
</style>
<? 
if ($gr==1) include('add_cat_sh.php');
if ($gr==2) include('add_cat_di.php');

cp_end();
