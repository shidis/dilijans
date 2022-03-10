<?  // модуль импорта App_CC_CI версия 1.0 (класс алгоритма 1)
include_once ('ajx_loader.php');

$cp->setFN('catImport');
$cp->checkPermissions();

$ci=new App_CC_CI();

$r->fres=true;
$r->fres_msg='';


function convert_file($filename, $f = 'utf8', $t = 'cp1251'){
	if(file_exists($filename)){
		$temp_file = Cfg::get('root_path')."/tmp/convert_file.tmp";
		exec("iconv -f {$f} -t {$t} {$filename} > {$temp_file}");
		copy($temp_file, $filename);
	}
}

if($upload_mode){
    if (!empty($_FILES)) {
        $tempFile = $_FILES['Filedata']['tmp_name'];
        $targetPath = $_SERVER['DOCUMENT_ROOT'] . '/tmp/';
        $targetFile =  str_replace('//','/',$targetPath) . Tools::fname2iso(($_FILES['Filedata']['name']));
        if(@$_REQUEST['fileext']!=''){
            $fileTypes  = str_replace('*.','',$_REQUEST['fileext']);
            $fileTypes  = str_replace(';','|',$fileTypes);
            $typesArray = explode('|',$fileTypes);
            $fileParts  = pathinfo($_FILES['Filedata']['name']);
            if (in_array($fileParts['extension'],$typesArray)) {
                @mkdir(str_replace('//','/',$targetPath), 0755, true);
            } else {
                echo 'Некорректный тип файла';
                exit();
            }
        }

        //convert_file($tempFile, 'cp1251', 'utf8');
        if(move_uploaded_file($tempFile,$targetFile)) echo "1|".basename($targetFile); else echo "0|".basename($targetFile);

    }
exit();
}
	
//sleep(1);

$page = (int)@$_REQUEST['page']; // get the requested page
$limit = (int)@$_REQUEST['rows']; // get how many rows we want to have into the grid
$sidx = Tools::esc(@$_REQUEST['sidx']); // get index row - i.e. user click to sort
$sord = Tools::esc(@$_REQUEST['sord']); // get the direction
if(!$sidx) $sidx =1;
$act=Tools::esc($_REQUEST['act']);

switch ($act){
case 'allLinks_v2':
	$gr=(int)@$_REQUEST['gr'];
	$r->m=$ci->fetchAll("SELECT * FROM ci_model WHERE gr='$gr' ORDER BY ID_MODEL_to");
	$r->t=$ci->fetchAll("SELECT * FROM ci_tipo WHERE gr='$gr' ORDER BY sys_code_to");
	break;
case 'delLinkModel_v2':
	$model_id=(int)@$_REQUEST['model_id'];
	$ci->query("DELETE FROM ci_model WHERE model_id='$model_id'");
	$r->LA=$ci->getLinkArray();
	break;
case 'delLinkTipo_v2':
	$tipo_id=(int)@$_REQUEST['tipo_id'];
	$ci->query("DELETE FROM ci_tipo WHERE tipo_id='$tipo_id'");
	$r->LA=$ci->getLinkArray();
	break;
case 'linkModel_v2':
	$r=$ci->linkModel($_REQUEST['item_id'],unserialize(Tools::unesc($_REQUEST['chk'])));
	$r->fres_msg=$ci->strMsg();
	if($r->fres) $r->fres_msg.='<br>Выполнено';
	$r->LA=$ci->getLinkArray();
	break;
case 'linkTipo_v2':
	$r=$ci->linkTipo($_REQUEST['item_id'],unserialize(Tools::unesc($_REQUEST['chk'])));
	$r->fres_msg=$ci->strMsg();
	if($r->fres) $r->fres_msg.='<br>Выполнено';
	$r->LA=$ci->getLinkArray();
	break;
case 'getLA':
	$r->LA=$ci->getLinkArray();
	break;
case 'get_config':	
	$r->opt=$ci->getConfig();
	$r->CMI=$ci->CMI;
	// отключаем в целях безопасности
	$r->opt['uploadFrm']['delBrandsAbsent']=0;
	$r->opt['uploadFrm']['delModelsAbsent']=0;
	$r->opt['uploadFrm']['delTiposAbsent']=0;
	$r->opt['uploadFrm']['updateTyresSuffix']=0;
	break;
case 'make_stat':
	$file_id=(int)@$_REQUEST['file_id'];
	$f=Tools::_parseStr(@$_REQUEST['f']);
	$f['check']=@$_REQUEST['check'];
	$r=$ci->makeStat($file_id,$f);
	break;
case 'parse':
	$file_id=(int)@$_REQUEST['file_id'];
	$page=(int)@$_REQUEST['page'];
	$limit=(int)@$_REQUEST['limit'];
	$last_page=(int)@$_REQUEST['last_page'];
	$f=Tools::_parseStr(@$_REQUEST['f']);
	$f['check']=@$_REQUEST['check'];
	$r=$ci->recognize($file_id,$limit*$page,$limit,$f);
	break;
case 'check_files':
	$file_id=(int)@$_REQUEST['file_id'];
	$res=$ci->checkStructure($file_id,true);
	$r->fres=$ci->fres;
	$r->fres_msg=$ci->strMsg();
	$r->gr=$ci->gr;
	$r->CM=$ci->CM;
	$r->header=@$ci->header;
	$r->name=$ci->name;
	if($res && $r->gr) $r->fres_msg.="<br>Файл {$r->name} имеет структуру ".($r->gr==1?'&quot;ШИНЫ&quot;':'&quot;ДИСКИ&quot;');
	break;
case 'upload':
	$fname=@$_REQUEST['fname'];
	if($fname=='') {
		$r->fres=false;
		$r->fres_msg='Ошибка программы: не указан файл';
		break;
	}
	$r->fres=$ci->parse($_SERVER['DOCUMENT_ROOT'].'/tmp/'.$fname);
	$r->fres_msg=$ci->strMsg();
	if($ci->fres){
		$r->param=$ci->param;
		$r->colModel=$ci->colModel;
		$r->name=$ci->name;
		$r->file_id=$ci->file_id;
		$r->gr=$ci->gr;
		$r->dt_add=$ci->dt_add;
		// прикрываем косяк с параетрами:*/
        if(!isset($r->param['gr'])) $r->param['gr']=$ci->gr;
        $r->param['status']=0;
		/*--*/
	}
	break;
case 'del_file':
	$file_id=(int)@$_REQUEST['file_id'];
	$ci->del('ci_item','file_id',$file_id);
	if(!$ci->del('ci_file','file_id',$file_id)) $r->fres=false;
	else{
		$d=$ci->getOne("SELECT count(file_id) FROM ci_file");
		if(!$d[0]){
			$ci->query("TRUNCATE `ci_item`");
		}else $ci->query("OPTIMIZE TABLE `ci_item`");
	}
	break;
case 'view':
	$file_id=(int)@$_REQUEST['file_id'];
	$d=$ci->view($file_id,$page,$limit,$sidx,$sord);
	if($d!==false) $r=$d; else {
		$r->fres=$ci->fres;
		$r->fres_msg=$ci->strMsg();
	}
	break;
case 'select_file':
	$file_id=(int)@$_REQUEST['file_id'];
	$d=$ci->getOne("SELECT * FROM ci_file WHERE file_id='$file_id'");
	if($d!==0){
		$r->param=unserialize(Tools::unesc($d['param']));
//		print_r($r->param);
		$r->colModel=unserialize(Tools::unesc($d['col_model']));
		$r->name=$d['name'];
		$r->file_id=$d['file_id'];
		$r->gr=$d['gr'];
		$r->status=$d['status'];
		// прикрываем косяк с параетрами:*/
        if(!isset($r->param['gr'])) $r->param['gr']=$d['gr'];
        if(!isset($r->param['status'])) $r->param['status']=$d['status'];
		/*--*/
		$r->dt_add=$d['dt_add'];
		Data::set('ci_cm_default',$d['col_model']);
	} else $r->fres=false;
	break;	
case 'files':
	$cfg=$ci->getConfig(true);
	$ci->clearFileList(@$cfg['uploadFrm']['maxFileList']);
	$d=$ci->fetchAll("SELECT file_id,name,gr,status,param FROM ci_file ORDER BY dt_add DESC");
	$r->files=array();
	foreach($d as &$v)
		$r->files[]=array('id'=>$v['file_id'],'label'=>$v['name']/*,'data'=>array('status'=>$v['status'],'gr'=>$v['gr'],'param'=>$v['param'])*/);
	break;
case 'cm1_saverow':
	$colName=@$_REQUEST['colName'];
	$id=(int)@$_REQUEST['id'];
	$file_id=(int)@$_REQUEST['file_id'];
	if($file_id) {
		$d=$ci->getOne("SELECT col_model FROM ci_file WHERE file_id='$file_id'");
		if($d!==0 && ($d=unserialize($d['col_model']))!=array() && count(@$d[1]['rows'])){
			$d[1]['rows'][$id]['cell'][1]=$colName;
			if($ci->update('ci_file',array('col_model'=>Tools::esc(serialize($d))),"file_id='$file_id'")) $r->fres_msg='Записано для file_id='.$file_id;
				else {$r->fres=false; $r->fres_msg='Ошибка записи';}
			Data::set('ci_cm_default',serialize($d));
			break;
		}
	}
	$d=Data::get('ci_cm_default');
	if($d!=''){
		$d=unserialize(($d));
		$d[1]['rows'][$id]['cell'][1]=$colName;
		if($file_id) {
			if($ci->update('ci_file',array('col_model'=>Tools::esc(serialize($d))),"file_id='$file_id'")) $r->fres_msg='Записано заново для file_id='.$file_id;
		}else Data::set('ci_cm_default',serialize($d));
	}else {
		$r->fres=false;
		$r->fres_msg='Ошибка. Нет таблицы параметров CM';
	}
	$r->d=$d;
	break;
case 'cm2_saverow':
	$colName=@$_REQUEST['colName'];
	$id=(int)@$_REQUEST['id'];
	$file_id=(int)@$_REQUEST['file_id'];
	if($file_id) {
		$d=$ci->getOne("SELECT col_model FROM ci_file WHERE file_id='$file_id'");
		if($d!==0 && ($d=unserialize($d['col_model']))!=array() && count(@$d[2]['rows'])){
			$d[2]['rows'][$id]['cell'][1]=$colName;
			if($ci->update('ci_file',array('col_model'=>Tools::esc(serialize($d))),"file_id='$file_id'")) $r->fres_msg='Записано для file_id='.$file_id;
				else {$r->fres=false; $r->fres_msg='Ошибка записи';}
				Data::set('ci_cm_default',serialize($d));
			break;
		}
	}
	$d=Data::get('ci_cm_default');
	if($d!=''){
		$d=unserialize(($d));
		$d[2]['rows'][$id]['cell'][1]=$colName;
		if($file_id) {
			if($ci->update('ci_file',array('col_model'=>Tools::esc(serialize($d))),"file_id='$file_id'")) $r->fres_msg='Записано заново для file_id='.$file_id;
		}else Data::set('ci_cm_default',serialize($d));
	}else {
		$r->fres=false;
		$r->fres_msg='Ошибка. Нет таблицы параметров CM';
	}
	$r->d=$d;
	break;
case 'cm1': 
	$file_id=(int)@$_REQUEST['file_id'];
	if($file_id) {
		$d=$ci->getOne("SELECT col_model FROM ci_file WHERE file_id='$file_id'",MYSQL_ASSOC);
		$d=unserialize(Tools::unesc($d['col_model']));
		if($d!==0 && count(@$d[1]['rows'])){
			$r->rows=$d[1]['rows'];
			$r->page=1;
			$r->total=1;
			$r->records=count($r->rows);
			break;
		}
	}
	$d=Data::get('ci_cm_default');
	if($d!=''){
		$d=unserialize(($d));
		if(isset($d[1]) && count($d[1]['rows'])==count($ci->CMT[1])){
			$r->rows=$d[1]['rows'];
			$r->page=1;
			$r->total=1;
			$r->records=count($r->rows);
			break;
		}
	}else $d=array();
	$i=0;
	foreach($ci->CMT[1] as $k=>&$v) {
		$r->rows[$i]['cell']=array($k,$v);
		$r->rows[$i]['id']=$i;
		$i++;
	}
	$d[1]['rows']=$r->rows;
	Data::set('ci_cm_default',(serialize($d)));
	$r->page=1;
	$r->total=1;
	$r->records=16;
break;
case 'cm2': 
	$file_id=(int)@$_REQUEST['file_id'];
	if($file_id) {
		$d=$ci->getOne("SELECT col_model FROM ci_file WHERE file_id='$file_id'");
		$d=unserialize(Tools::unesc($d['col_model']));
		if($d!==0 && count(@$d[2]['rows'])){
			$r->rows=$d[2]['rows'];
			$r->page=1;
			$r->total=1;
			$r->records=count($r->rows);
			break;
		}
	}
	$d=Data::get('ci_cm_default');
	if($d!=''){
		$d=unserialize(($d));
		if(isset($d[2]) && count($d[2]['rows'])==count($ci->CMT[2])){
			$r->rows=$d[2]['rows'];
			$r->page=1;
			$r->total=1;
			$r->records=count($r->rows);
			break;
		}
	}else $d=array();
	$i=0;
	foreach($ci->CMT[2] as $k=>&$v) {
		$r->rows[$i]['cell']=array($k,$v);
		$r->rows[$i]['id']=$i;
		$i++;
	}
	$d[2]['rows']=$r->rows;
	Data::set('ci_cm_default',(serialize($d)));
	$r->page=1;
	$r->total=1;
	$r->records=16;
break;
	
default: $r->fres=false; $r->fres_msg= 'BAD ACT ID '.$act;
}

ajxEnd();