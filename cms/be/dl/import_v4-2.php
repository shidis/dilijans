<?  // модуль импорта App_CC_CII версия 4.2 (класс алгоритма 2)
include_once ('../ajx_loader.php');

$cp->setFN('catImportII2');
$cp->checkPermissions();

$ci=new App_CC_CII2Base();

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
	
		$f=fopen($_SERVER['DOCUMENT_ROOT']."/tmp/upload_file.log",'w');
		fwrite($f,date("Y-m-d H:i:s").' - '.print_r($_FILES,true).print_r($_GET,true).print_r($_POST,true)."\r\n");
		fclose($f);
	
		//convert_file($tempFile, 'cp1251', 'utf8');
		if(move_uploaded_file($tempFile,$targetFile)) echo "1|".basename($targetFile); else echo "0|".basename($targetFile);
		
	}
	exit();
}

$act=Tools::esc($_REQUEST['act']);

switch ($act){
    case 'get_config':
        $r->opt=$ci->getConfig();
        $r->CMI=$ci->CMI;
        $r->diaMergeNum=count($r->opt['config']['diaMerge']);
        $r->svMergeNum=count($r->opt['config']['svMerge']);
        break;

    case 'set_config':
        $f=Tools::_parseStr(@$_REQUEST['configFrm']);
        $cfg=$ci->getConfig();
        $cfg['config']['replicaBrand']=@$f['replicaBrand'];
        $cfg['config']['maxFileList']=(int)@$f['maxFileList'];
        $ci->setConfig($cfg);
        break;

    case 'getDiasNum':
        $cfg=$ci->getConfig();
        $r->diaMergeNum=count($cfg['config']['diaMerge']);
        break;

    case 'dias':
        $cfg=$ci->getConfig();
        $r->records=count($cfg['config']['diaMerge']);
        $r->page = 1;
        $r->total = 1;
        $i=0;
        ksort($cfg['config']['diaMerge'],SORT_NUMERIC );
        foreach($cfg['config']['diaMerge'] as $k=>$v){
            $r->rows[$i]['id']=(string)$k;
            $r->rows[$i]['cell'][]=(string)$k;
            $r->rows[$i]['cell'][]=$v;
            $i++;
        }
        break;

    case 'diasMod':
        $r->textOutput=true;
        $cfg=$ci->getConfig();
        $dia0=(string)str_replace(',','.',@$_REQUEST['dia0']);
        $dia1=(float)str_replace(',','.',@$_REQUEST['dia1']);
        $id=(string)str_replace(',','.',@$_REQUEST['id']);

        if(@$_REQUEST['oper']=='add'){
            if(isset($cfg['config']['diaMerge'][$dia0])){
                echo 'Дубль значения исходного DIA';
                break;
            }
            $cfg['config']['diaMerge'][$dia0]=$dia1;
        }elseif(@$_REQUEST['oper']=='edit'){
            if($id!=$dia0){
                if(isset($cfg['config']['diaMerge'][$dia0])){
                    echo 'Дубль значения исходного DIA';
                    break;
                }else unset($cfg['config']['diaMerge'][$id]);
            }
            $cfg['config']['diaMerge'][$dia0]=$dia1;
        }elseif(@$_REQUEST['oper']=='del'){
            unset($cfg['config']['diaMerge'][$id]);
        }
        $ci->setConfig($cfg);
        echo '0';
        break;


    case 'upload':
        $fname=@$_REQUEST['fname'];
        $config=Tools::_parseStr($_REQUEST['config']);
        if($fname=='') {
            $r->fres=false;
            $r->fres_msg='Ошибка программы: не указан файл';
            break;
        }
        $r->fres=$ci->parse($_SERVER['DOCUMENT_ROOT'].'/tmp/'.$fname, $config);
        $r->fres_msg=$ci->strMsg();
        if($ci->fres){
            $r->param=$ci->param;
            $r->name=$ci->name;
            $r->file_id=$ci->file_id;
            $r->gr=$ci->gr;
            $r->dt_added=$ci->dt_added;
            $r->deletedFiles=$ci->deletedFiles;
            $bb=$ci->getBrandsPriceNoUpdCount($r->gr);
            if(!empty($bb)){
                $r->brandsNoUpdNum = $bb['count'];
                $r->brandsNoUpd = $bb['bids'];
            }
        }
        break;

    case 'files':
        $d=$ci->fetchAll("SELECT file_id,name,gr,status,param FROM cii_file ORDER BY dt_added DESC");
        $r->files=array();
        foreach($d as &$v)
            $r->files[]=array('id'=>$v['file_id'],'label'=>$v['name'],'status'=>$v['status'],'gr'=>$v['gr'],'param'=>$v['param']);
        break;

    case 'select_file':
        $file_id=(int)@$_REQUEST['file_id'];
        $d=$ci->getOne("SELECT * FROM cii_file WHERE file_id='$file_id'");
        if($d!==0){
            if(mb_strpos($d['param'],':')!==false) $r->param=unserialize(stripslashes($d['param'])); else $r->param=Tools::DB_unserialize(($d['param']));
            if(mb_strpos($d['CM'],':')!==false) $r->CM=unserialize(stripslashes($d['CM'])); else $r->CM=Tools::DB_unserialize(Tools::unesc($d['CM']));
            $r->name=$d['name'];
            $r->file_id=$d['file_id'];
            $r->gr=$d['gr'];
            $r->status=$d['status'];
            $r->dt_added=$d['dt_added'];
            $bb=$ci->getBrandsPriceNoUpdCount($r->gr);
            if(!empty($bb)){
                $r->brandsNoUpdNum = $bb['count'];
                $r->brandsNoUpd = $bb['bids'];
            }
        } else {
            $r->fres=false;
            $r->fres_msg='Файл не выбран';
        }
        break;
    case 'view':
        $file_id=(int)@$_REQUEST['file_id'];
        $d=$ci->view($file_id);
        if($d!==false) $r=$d; else {
            $r->fres=$ci->fres;
            $r->fres_msg=$ci->strMsg();
        }
        break;
    case 'getSup':
        $file_id=(int)@$_REQUEST['file_id'];
        if($file_id){
            $d=$ci->fetchAll("SELECT cii_item.sup_id, cc_sup.name FROM cii_item INNER JOIN cc_sup ON cii_item.sup_id=cc_sup.sup_id WHERE cii_item.file_id='$file_id' GROUP BY cc_sup.name ORDER BY cc_sup.name",MYSQLI_ASSOC);
            $r->supList=array();
            if(!empty($d)) foreach($d as $v) $r->supList[Tools::unesc($v['name'])]=$v['sup_id'];

        } else {
            $r->fres=false;
            $r->fres_msg='[getSup]: Файл не выбран';
        }
        break;
    case 'parse':
        $gr=(int)@$_REQUEST['gr'];
        if(empty($gr)) {
            $r->fres=false;
            $r->fres_msg='Группа товаров неизвестна';
            break;
        }
        $file_id=(int)@$_REQUEST['file_id'];
        $iter=(int)@$_REQUEST['iter'];
        $limit=(int)@$_REQUEST['limit'];
        $f=Tools::_parseStr(@$_REQUEST['config']);
        $f['test']=@$_REQUEST['test'];
        $ciSID=@$_REQUEST['ciSID'];
        if($gr==1) $ci=new App_CC_CII2t(); else $ci=new App_CC_CII2d();
        $r=$ci->recognize($file_id,$iter,$limit,$f,$ciSID);
    //	sleep(1);
        break;
    case 'brands':
        $gr=(int)@$_REQUEST['gr'];
        if($gr==0) {
            $r->fres=false;
            $r->fres_msg='Группа товаров не выбрана';
            break;
        }
        $cfg=$ci->getConfig();
        $cc=new CC_Ctrl;
        $cc->que('brands',$gr,0);
        $d=$cc->fetchAll();
        $r->records=count($d);
        $r->page = 1;
        $r->total = 1;
        $i=0;
        foreach($d as $v){
            $r->rows[$i]['id']=$v['brand_id'];
            $r->rows[$i]['cell'][]=Tools::unesc($v['name']);
            $r->rows[$i]['cell'][]=@$cfg['brands'][$v['brand_id']]['priceNoUpd'];
            $i++;
        }
        break;
    case 'brandMod':
        $brand_id=(int)@$_REQUEST['brand_id'];
        $gr=(int)@$_REQUEST['gr'];
        $v=@$_REQUEST['noupd'];
        if($v=='checked' || $v=='true') $v=1; else $v=0;
        if($brand_id==0) {
            $r->fres=false;
            $r->fres_msg='Бренд не выбран';
            break;
        }
        $cfg=$ci->getConfig();
        $r->rv=$cfg['brands'][$brand_id]['priceNoUpd']=(int)$v;
        $ci->setConfig($cfg);
        $bb=$ci->getBrandsPriceNoUpdCount($gr);
        $r->brandsNoUpdNum=$bb['count'];
        break;

    case 'getSVNum':
        $cfg=$ci->getConfig();
        $r->svMergeNum=count($cfg['config']['svMerge']);
        break;

    case 'svMod':
        $r->textOutput=true;
        $cfg=$ci->getConfig();
        if(@$_REQUEST['oper']!='del'){
            $sv0=(string)str_replace(',','.',@$_REQUEST['sv0']);
            $sv0=str_replace(' ','',$sv0);
            $sv00=explode('*',$sv0);
            if(count($sv00)!=2) {
                echo 'Неверный формат Исходного значения сверловки';
                break;
            }
            $sv1=str_replace(',','.',@$_REQUEST['sv1']);
            $sv1=str_replace(' ','',$sv1);
            $sv1=explode('*',$sv1);
            if(count($sv1)!=2) {
                echo 'Неверный формат результирующего значения сверловки';
                break;
            }
        }

        $id=(string)str_replace(',','.',@$_REQUEST['id']);
        $id=str_replace(' ','',$id);

        if(@$_REQUEST['oper']=='add'){
            if(isset($cfg['config']['svMerge'][$sv0])){
                echo 'Дубль значения исходного LZ*PCD';
                break;
            }
            $cfg['config']['svMerge'][$sv0]=$sv1;
        }elseif(@$_REQUEST['oper']=='edit'){
            $id_=explode('*',$id);
            if(count($id_)!=2) {
                echo 'Неверный формат ключа';
                break;
            }
            if($id!=$sv0){
                if(isset($cfg['config']['svMerge'][$sv0])){
                    echo 'Дубль значения исходного LZ*PCD';
                    break;
                }else unset($cfg['config']['svMerge'][$id]);
            }
            $cfg['config']['svMerge'][$sv0]=$sv1;
        }elseif(@$_REQUEST['oper']=='del'){
            unset($cfg['config']['svMerge'][$id]);
        }
        $ci->setConfig($cfg);
        echo '0';
        break;

    case 'SVs':
        $cfg=$ci->getConfig();
        $r->records=count($cfg['config']['svMerge']);
        $r->page = 1;
        $r->total = 1;
        $i=0;
        ksort($cfg['config']['svMerge'],SORT_NUMERIC );
        foreach($cfg['config']['svMerge'] as $k=>$v){
            $r->rows[$i]['id']=(string)$k;
            $r->rows[$i]['cell'][]=(string)$k;
            $r->rows[$i]['cell'][]=implode('*',$v);
            $i++;
        }
        break;

    default: $r->fres=false; $r->fres_msg= 'BAD ACT ID '.$act;

}

ajxEnd();
