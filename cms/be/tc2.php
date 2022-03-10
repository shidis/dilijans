<?
include_once ('ajx_loader.php');

$cp->setFN('tc2');
$cp->checkPermissions();

//sleep(2);

$r->fres=true;
$r->fres_msg='';
$page = @$_REQUEST['page']; // get the requested page
$rows = @$_REQUEST['rows']; // get how many rows we want to have into the grid
$sidx = @$_REQUEST['sidx']; // get index row - i.e. user click to sort
$sord = @$_REQUEST['sord']; // get the direction
if(!$sidx) $sidx =1;
$act=Tools::esc($_REQUEST['act']);

$tc=new TC();

switch ($act){
	
case 'change_site':
	$site=(Tools::esc(@$_REQUEST['site']));
	$co_id=(int)$_REQUEST['co_id'];
	$r->fres=$tc->update('tc_company',array('site'=>$site),"company_id='$co_id'");
	break;
	
case 'change_co_name':
	$co_name=(Tools::esc(@$_REQUEST['co_name']));
	$co_id=(int)$_REQUEST['co_id'];
	$r->fres=$tc->update('tc_company',array('name'=>$co_name),"company_id='$co_id'");
	break;
	
case 'getCo':
	$co_id=(int)$_REQUEST['co_id'];
	$d=$tc->getOne("SELECT * FROM tc_company WHERE company_id = '$co_id'");
	if($d!==false){
		$r->co_info['name']=Tools::unesc($d['name']);
		$r->co_info['site']=Tools::unesc($d['site']);
		$r->co_info['co_id']=$co_id;
		$r->co_info['dSumMin']=$d['dSumMin'];
		$r->co_info['dSumM3']=$d['dSumM3'];
		$r->co_info['disabled']=!!$d['disabled'];
	}else {
		$r->fres=false;
		$r->fres_msg='Ошибка БД';
	}
	break;

case 'loadCoCities':
	$r->textOutput=true;
	$d=$tc->fetchAll("SELECT tc_city.name, tc_city_rel.* FROM tc_city_rel INNER JOIN tc_city USING (city_id) WHERE tc_city_rel.company_id='$co_id'");
	?><table><tr><th>Город</th><th class="ed" n="tel">Телефон филиала</th><th class="ed" n="vr">Время работы филиала</th><th class="ed" n="description">Примечание</th><th class="ed" n="period">Срок доставки, дней<th class="ed" n="costMin">Минимальная стоимость отправки, руб</th><th class="ed" n="costM3">Тариф за куб, руб</th><th class="ed" n="costKG">Тариф за кг, руб</th></tr><?
	foreach($d as $v){
		?><tr><td><?=Tools::unesc($v['name'])?></td><?
		?><td n="tel"><?=Tools::unesc($v['tel'])?></td><?
		?><td n="vr"><?=Tools::unesc($v['vr'])?></td><?
		?><td n="description"><?=Tools::unesc($v['description'])?><?
		?><td n="period"><?=$v['period']?></td><?
		?><td n="costMin"><?=$v['costMin']?></td><?
		?><td n="costM3"><?=$v['costM3']?></td><?
		?><td n="costKG"><?=$v['costKG']?></td><?
		?></tr><?
	}
	?></table><?
	break;
	
case 'add_cities':
	$co_id=(int)$_REQUEST['company_id'];
	if(!$co_id) {$r->fres=false; $r->fres_msg='Выберите компанию'; break;} else $r->fres_msg='Города добавлены';
	$d=spliti("[\r\n]",Tools::stripTags(($_REQUEST['cities'])));
	for($i=0;$i<count($d);$i++) $d[$i]=trim($d[$i]);
	foreach($d as $v) if(trim($v)!=''){
		$v=Tools::esc(trim($v));
		$sname=Tools::str2iso($v,200,'');
		$v11=Tools::like_($v);
		$tc->query("SELECT * FROM tc_city WHERE name LIKE '$v11'");
		if($tc->qnum()) {
			$tc->next();
			$city_id=$tc->qrow['city_id'];
		}else {
			$tc->query("INSERT INTO tc_city (name,sname) VALUES('$v','$sname')");
			$city_id=mysql_insert_id();
		}
		$tc->query("SELECT * FROM tc_city_rel WHERE (city_id='$city_id')AND(company_id='$co_id')");
		if(!$tc->qnum()){
			$tc->query("INSERT INTO tc_city_rel (city_id,company_id) VALUES('$city_id','$co_id')");
		}
	}
	$c=$tc->fetchAll("SELECT tc_city.name,tc_city.city_id FROM tc_city_rel INNER JOIN tc_city ON tc_city.city_id=tc_city_rel.city_id WHERE (tc_city_rel.company_id='$co_id')");
	if(count($c)){
		for($i=0; $i<count($c);$i++){
			$r->cities[]=array('name'=>$c[$i]['name'],'city_id'=>$c[$i]['city_id']);
		}
	}
	break;

case 'new_co':
	$co_name=trim(Tools::esc(($_REQUEST['co_name'])));
	$site=trim(Tools::esc(($_REQUEST['co_site'])));
	$u=parse_url($site);
	$site=@$u['host'];
	$co_name1=Tools::like_($co_name);
	$tc->query("SELECT * FROM tc_company WHERE name LIKE '$co_name1'");
	if($tc->qnum()){
		$r->fres=false;
		$r->co_alredy_exists=true;
	}else{
		$tc->query("INSERT INTO tc_company (name,site) VALUES('$co_name','$site')");
		$r->fres_msg='Добавлено '.$co_name;
	}
	break;
	
case 'disable_co_switch':
	$co_id=(int)$_REQUEST['co_id'];
	$d=$tc->getOne("SELECT * FROM tc_company WHERE company_id='$co_id'");
	if($d!==0){
		$r->coDisabled=!$d['disabled'];
		$tc->query("UPDATE tc_company SET disabled='{$r->coDisabled}' WHERE company_id='$co_id'");
	}else {
		$r->fres=false;
		$r->fres_msg='Bad co_id';
	}
	break;
	
case 'reloadCo':
	$r->textOutput=true;
	$tc->que('co_list');?>
	<option value="0">Компания</option>
	<? while($tc->next()!==false){?>
	<option value="<?=$tc->qrow['company_id']?>" <?=$tc->qrow['disabled']?'style="background-color:#cccccc"':''?>><?=$tc->qrow['name']?></option>
	<? }
	break;
	
case 'del_co':
	$co_id=(int)$_REQUEST['co_id'];
	$tc->query("delete from tc_company where company_id='$co_id'");
	$tc->query("delete from tc_city_rel where company_id='$co_id'");
	$r->fres_msg='Удалено';
	break;
	
case 'del_city':
	$co_id=(int)$_REQUEST['co_id'];
	$city_id=(int)$_REQUEST['city_id'];
	$tc->query("delete from tc_city_rel where company_id='$co_id' AND city_id='$city_id'");
	$d=$tc->getOne("SELECT count(city_rel_id) FROM tc_city_rel WHERE city_id='$city_id'");
	if($d[0]==0) $tc->query("DELETE FROM tc_city WHERE city_id='$city_id'");	
	break;

default: echo 'BAD ACT ID '.$act;
}

ajxEnd();