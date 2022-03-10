<?
include_once ('ajx_loader.php');


$cp->setFN('galleries');
$cp->checkPermissions();

$r->fres=true;
$r->fres_msg='';



if(@$upload_mode){

    if (!empty($_FILES)) {
        Tools::wlog('galleriesUploads.log',@$res);
        $r='';
        $u=false;
        $tempFile = $_FILES['Filedata']['tmp_name'];
        $targetPath = $_SERVER['DOCUMENT_ROOT'] . '/tmp/';
        $targetFile =  str_replace('//','/',$targetPath) . $_FILES['Filedata']['name'];
        Tools::tree_mkdir($targetPath);
        if(@move_uploaded_file($tempFile,$targetFile)) {
            $gl=new Gallery();
            $u=$gl->upload($targetFile,@$_REQUEST['topic_id']);
            $r=$gl->strMsg(';');
            @unlink($targetFile);
        } else $r='Ошибка перемещения файла';
        if(@$u) echo $res="1|".basename($targetFile)."|".$r;
        else echo $res="0|".basename($targetFile)."|".$r;


    }



    exit();
}
	
//sleep(1);

$act=Tools::esc($_REQUEST['act']);

$gl=new Gallery();

switch ($act){

case 'topic_list':
	$r->topics=$gl->fetchAll("SELECT * FROM gl_topic ORDER BY name",MYSQL_ASSOC);
	foreach($r->topics as $k=>&$v) {
		$v['name']=Tools::unesc($v['name']);
		$v['sname']=Tools::unesc($v['sname']);
		$v['info']=Tools::unesc($v['info']);
		$v['param']=Tools::DB_unserialize($v['param']);
	}
	break;

case 'galAdd':
	$f=Tools::_parseStr($_REQUEST['f']);
	if($res=$gl->topicAdd($f)) {
		$r->fres_msg='Добавлено '.$f['name']; 
		$r->topic_id=$res;
	}else {
		$r->fres_msg=$gl->strMsg();
		$r->fres=$gl->fres;
	}
	break;

case 'galEdit':
	$f=Tools::_parseStr($_REQUEST['f']);
	$f['topic_id']=$_REQUEST['topic_id'];
	if($gl->topicEdit($f)) $r->fres_msg='Отредактировано '.$f['name']; else {
		$r->fres_msg=$gl->strMsg();
		$r->fres=$gl->fres;
	}
	break;

case 'galDel':
	$topic_id=$_REQUEST['topic_id'];
	if($gl->topicDel($topic_id)) $r->fres_msg='Удалено'; else {
		$r->fres_msg=$gl->strMsg();
		$r->fres=$gl->fres;
	}
	break;

case 'imageList':
	$topic_id=$_REQUEST['topic_id'];
	if($res=$gl->imageList($topic_id)) $r->imgs=$res; else {
		$r->fres_msg=$gl->strMsg();
		$r->fres=$gl->fres;
	}
	break;

case 'get_image_info':
	$image_id=$_REQUEST['image_id'];
	if(!$image_id) {
		$r->fres=false;
		$r->fres_msg='Не задан ID изображения';
	}else{
		$d=$gl->getOne("SELECT * FROM gl_image WHERE image_id='$image_id'");
		if($d===0){
			$r->fres=false;
			$r->fres_msg='Идентификатор не найден';
		}else{
			$r->title=Tools::unesc($d['title']);
			$r->link=Tools::unesc($d['link']);
			$r->info1=Tools::unesc($d['info1']);
			$r->info2=Tools::unesc($d['info2']);
			$r->alt=Tools::unesc($d['alt']);
		}
	}
	break;

case 'iEdit':
	$image_id=$_REQUEST['image_id'];
	$f=Tools::_parseStr($_REQUEST['f']);
	if(!$image_id) {
		$r->fres=false;
		$r->fres_msg='Не задан ID изображения';
	}else{
		$title=Tools::esc($f['title']);
		$alt=Tools::esc($f['alt']);
		$info1=Tools::esc($f['info1']);
		$info2=Tools::esc($f['info2']);
		$link=Tools::esc($f['link']);
		$gl->query("UPDATE gl_image SET title='$title', alt='$alt', link='$link', info1='$info1', info2='$info2' WHERE image_id='$image_id'");
	}
	break;

case 'iDel':
	$image_id=$_REQUEST['image_id'];
	if($gl->imageDel($image_id)) $r->fres_msg='Удалено'; else{
		$r->fres_msg=$gl->strMsg();
		$r->fres=$gl->fres;
	}
	break;
	
default: $r->fres=false; $r->fres_msg= 'BAD ACT ID '.$act;
}

ajxEnd();