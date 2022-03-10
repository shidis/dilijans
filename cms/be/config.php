<?
include_once ('ajx_loader.php');

//sleep(1);


$cp->setFN('config');
$cp->checkPermissions();


$r->fres=true;
$r->fres_msg='';

$page = @$_REQUEST['page']; // get the requested page
$limit = @$_REQUEST['rows']; // get how many rows we want to have into the grid
$sidx = @$_REQUEST['sidx']; // get index row - i.e. user click to sort
$sord = @$_REQUEST['sord']; // get the direction
if(!$sidx) $sidx =1; 
$act=Tools::esc($_REQUEST['act']);
$gr=@$_REQUEST['gr'];

$db=new DB();

function event($name,$v){
	$cc=new CC_Ctrl;
	switch ($name){
		case 'global_t_extra': $cc->addCacheTask('prices',1); break;
		case 'global_d_extra': $cc->addCacheTask('prices',2); break;
		case 'sezon_extra_1': 
		case 'sezon_extra_2': 
		case 'sezon_extra_3': 
			$cc->addCacheTask('prices',1); 
			break;
		case 't_discount':
			if(Data::get('td_discount_intPrice')) $cc->addCacheTask('prices',1); 
			break;
		case 'd_discount':
			if(Data::get('td_discount_intPrice')) $cc->addCacheTask('prices',2); 
			break;
		case 'td_discount_intPrice':
			if($v) $cc->addCacheTask('prices');
			break;
		case 'cc_replica_title':
			$cc->addCacheTask('brands',2);
			break;
	}
	unset($cc);
}

switch ($act){
	case 'get':
		$id=(int)$_REQUEST['id'];
		if(!$id) {$r->fres=false; $r->fres_msg='Нет ID';}
		$d=$db->getOne("SELECT * FROM system_data WHERE data_id='$id'",MYSQL_ASSOC);
		if($d!==0) {
			foreach($d as $k=>&$v) $v=Tools::unesc($v);
			$r->data=$d; 
		}else {$r->fres=false; $r->fres_msg='Нет данных';};
		break;
	case 'save':
		$f=strarr(($_REQUEST['f']));
		$id=(int)@$_REQUEST['id'];
		if(!$id) {$r->fres=false; $r->fres_msg='Нет ID';}
		$r->V=$V=Tools::esc(trim(@$f['V']));
		if(@$_REQUEST['adm']){
			$name=Tools::esc(trim(@$f['name']));
			if($name=='') {$r->fres=false;$r->fres_msg="Пустое значение имени параметра"; break;}
			$title=Tools::esc(trim(@$f['title']));
			$r->pos=$pos=(int)(trim(@$f['pos']));
			$r->H=$H=(bool)@$f['H'];
			$comment=Tools::esc(trim(@$f['comment']));
			$widget=Tools::esc(trim(@$f['widget']));
			$d=$db->getOne("SELECT name FROM system_data WHERE data_id='$id'");
			if(Tools::unesc($d['name'])!=$name){
				$d=$db->getOne("SELECT count(data_id) FROM system_data WHERE data_id!='$id' AND name='$name'");
				if($d[0]!=0) {$r->fres=false; $r->fres_msg='Дубликат имени'; break;};
			}
			$w=Tools::unesc(trim(@$f['widget']));
			if(preg_match("/^array\([^\)]*\)[;]*$/",$w)) {
				$er=error_reporting(E_ALL);
				ob_start();
				eval('$d='.$w.';');
				$res=ob_get_contents();
				ob_end_clean();
				error_reporting($er);
				if($res!=='') {$r->fres=false;$r->fres_msg="Есть ошибки выполнения @EVAL: $res"; break;} 
			} elseif($widget!='') {$r->fres=false;$r->fres_msg="Есть ошибки @EVAL: здесь должен быть массив array(...)"; break;} 
			$db->query("UPDATE system_data SET name='$name',V='$V',title='$title',pos='$pos',comment='$comment',widget='$widget',H='$H' WHERE data_id='$id'");
			$r->title=Tools::unesc($title);
			$r->name=Tools::unesc($name);
			$r->comment=Tools::unesc($comment);
			$r->name=Tools::unesc($name);
		} else $db->query("UPDATE system_data SET V='$V',H='$H'  WHERE data_id='$id'");
		$r->V=Tools::unesc($V);
		event(Tools::unesc($name),$r->V);
		break;
	
	case 'add':
		$f=strarr(($_REQUEST['f']));
		$V=Tools::esc(trim(@$f['V']));
		$name=Tools::esc(trim(@$f['name']));
		if($name=='') {$r->fres=false;$r->fres_msg="Пустое значение имени параметра (name)"; break;}
		$group=(int)@$_REQUEST['group'];
		if(!$group) {$r->fres=false;$r->fres_msg="Не передан ИД группы )(group)"; break;}
		$title=Tools::esc(trim(@$f['title']));
		if($title=='') {$r->fres=false;$r->fres_msg="Пустое значение названия параметра (title)"; break;}
		$pos=(int)(trim(@$f['pos']));
		$H=(bool)@$f['H'];
		$comment=Tools::esc(trim(@$f['comment']));
		$widget=Tools::esc(trim(@$f['widget']));
		$d=$db->getOne("SELECT count(name) FROM system_data WHERE name='$name'");
		if($d[0]!=0) {$r->fres=false; $r->fres_msg='Дубликат имени'; break;};
		$w=Tools::unesc(trim(@$f['widget']));
		if(preg_match("/^array\([^\)]*\)[;]*$/",$w)) {
			$er=error_reporting(E_ALL);
			ob_start();
			eval('$d='.$w.';');
			$res=ob_get_contents();
			ob_end_clean();
			error_reporting($er);
			if($res!=='') {$r->fres=false;$r->fres_msg="Есть ошибки выполнения @EVAL: $res"; break;} 
		} elseif($widget!='') {$r->fres=false;$r->fres_msg="Есть ошибки @EVAL: здесь должен быть массив array(...)"; break;} 
		$db->query("INSERT INTO system_data (group_id,name,V,title,pos,comment,widget,H) VALUES ('$group','$name','$V','$title','$pos','$comment','$widget','$H')");
		$r->id=$db->lastId();
		$r->title=Tools::unesc($title);
		$r->group=$group;
		$r->name=Tools::unesc($name);
		$r->V=Tools::unesc($V);
		event($r->name,$r->V);
		break;
	case 'del':
		$id=(int)@$_REQUEST['id'];
		if(!$id) {$r->fres=false; $r->fres_msg='Нет ID';}
		$db->query("DELETE FROM system_data WHERE data_id='$id'");
		break;
		
	case 'saveAll':
		$a=$_REQUEST['a'];
		if(!is_array($a)) {$r->fres=false;$r->fres_msg="Нет данных"; break;}
		foreach($a as $k=>&$v) {
			$v=Tools::esc(trim(($v)));
			$k=(int)$k;
			$db->query("UPDATE system_data SET V='$v' WHERE data_id='$k'");
			$d=$db->getOne("SELECT name,V FROM system_data WHERE data_id='$k'");
			event(@$d['name'],@$d['V']);
		}
		break;
	
	case 'curvalList':
		$cc=new CC_Ctrl();
		$d=$cc->fetchAll("SELECT * FROM cc_cur");
		$r->records=count($d);
		$r->page = 1;
		$r->total = 1;
		$i=0;
		foreach($d as $v){
			$r->rows[$i]['id']=$v['cur_id'];
			$r->rows[$i]['cell']=array($v['name'],$v['V']);
			$i++;
		}
		break;
	
	case 'curValUpdate':
		$cc=new CC_Ctrl();
		$cur_id=(int)@$_REQUEST['id'];
		$curval=(float)@$_REQUEST['curval'];
		$d=$cc->getOne("SELECT cur_id,V FROM cc_cur WHERE cur_id='$cur_id'");
		if(@$d['cur_id']){
			$cc->query("UPDATE cc_cur SET V='$curval' WHERE (cur_id='$cur_id')");
			if($cc->updatedNum()) {
				$cc->addCacheTask('prices');
			}
		}
	break;
	
	case 'ringsList':
		$cc=new CC_Ctrl();
		$d=$cc->fetchAll("SELECT * FROM cc_fitting_rings ORDER BY id");
		$r->records=count($d);
		$r->page = 1;
		$r->total = 1;
		$i=0;
		foreach($d as $v){
			$r->rows[$i]['id']=$v['id'];
			$r->rows[$i]['cell']=array($v['v1'],$v['v2']);
			$i++;
		}
		break;

	case 'ringsUpdate':
		$cc=new CC_Ctrl();
		$id=(int)@$_REQUEST['id'];
		$v1=Tools::toFloat(@$_REQUEST['v1']);
		$v2=Tools::toFloat(@$_REQUEST['v2']);
		$r->textOutput=true;
		$r->prependFresMsg=true;
		if(@$_REQUEST['oper']=='add'){
			$d=$cc->getOne("SELECT count(v1) FROM cc_fitting_rings WHERE v1='$v1' AND v2='$v2'");
			if($d[0]){
				$r->fres_msg='Дубль комбинации значений';
				break;
			}
			$cc->query("INSERT INTO cc_fitting_rings (v1,v2) VALUES('$v1','$v2')");
			if($cc->updatedNum()) echo 'добавлено';
		}elseif(@$_REQUEST['oper']=='edit'){
			$d=$cc->getOne("SELECT count(v1) FROM cc_fitting_rings WHERE v1='$v1' AND v2='$v2' AND id<>'$id'");
			if($d[0]){
				$r->fres_msg='Дубль комбинации значений';
				break;
			}
			$cc->query("UPDATE cc_fitting_rings SET v1='$v1',v2='$v2' WHERE id='$id'");
			if($cc->updatedNum()) {
				$r->fres_msg='0';
			}else $r->fres_msg='0';
		}elseif(@$_REQUEST['oper']=='del'){
			$cc->query("DELETE FROM cc_fitting_rings WHERE id='$id'");
			$r->fres_msg='0';
		}
	break;
	
	
	case 'testMailInfo':
		$r->fres=Mailer::sendmail(array(
		  'fromAddr'=>Data::get('mail_robot'),
		  'fromName'=>'тест '.Cfg::get('site_name'), 
		  'replyToAddr'=>Data::get('mail_info'), 
		  'replyToName'=>'основной почтовый ящик', 
		  'toAddr'=>Data::get('mail_info'), 
		  'toName'=>'mail_info', 
		  'body'=>'Отправка писем работает',
		  'subject'=>'тест!', 
		  'charset'=>Data::get('mail_charset'),
		  'host'=>Data::get('mail_robot_host'),
		  'logpw'=>Data::get('mail_robot_logpw'),
		  'SMTPSecure'=>Data::get('mail_smtp_secure'),
		  'debug'=>2
	  	));
		if(!$r->fres) echo "\r\nОтправка не удалась"; else echo "\r\nУспешно отправлено";
	break;
		
	case 'testMailOrder':
		$r->fres=Mailer::sendmail(array(
		  'fromAddr'=>Data::get('mail_robot'),
		  'fromName'=>'тест '.Cfg::get('site_name'), 
		  'replyToAddr'=>Data::get('mail_order'), 
		  'replyToName'=>'почтовый ящик для заказов', 
		  'toAddr'=>Data::get('mail_order'), 
		  'toName'=>'mail_order', 
		  'body'=>'Отправка писем работает',
		  'subject'=>'тест!', 
		  'charset'=>Data::get('mail_charset'),
		  'host'=>Data::get('mail_robot_host'),
		  'logpw'=>Data::get('mail_robot_logpw'),
		  'SMTPSecure'=>Data::get('mail_smtp_secure'),
		  'debug'=>2
	  	));
		if(!$r->fres) echo "\r\nОтправка не удалась"; else echo "\r\nУспешно отправлено";
	break;
		
		
			
default: $r->fres=false; $r->fres_msg='BAD ACT_CASE '.$act;
}

ajxEnd();