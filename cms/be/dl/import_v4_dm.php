<?  // модуль импорта App_Import_CII версия 4.0 (класс алгоритма 2)
include_once('../ajx_loader.php');

$cp->setFN('catImportII');
$cp->checkPermissions();

$ci=new App_Import_CII();

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

        $backupPath = $_SERVER['DOCUMENT_ROOT'] . '/assets/upload/ci/';
        $targetPath = $_SERVER['DOCUMENT_ROOT'] . '/tmp/';
        Tools::tree_mkdir(str_replace('//','/',$targetPath));
        Tools::tree_mkdir(str_replace('//','/',$backupPath));

		if(!empty($_FILES['Filedata'])){
			$tempFile = $_FILES['Filedata']['tmp_name'];
            $tmpFN=$_FILES['Filedata']['name'];
		}else{
			$tempFile = $_FILES['file']['tmp_name'];
            $tmpFN=$_FILES['file']['name'];
		}

        $pi=pathinfo($tmpFN);
        if (in_array(@$pi['extension'],array('csv','CSV','xls','XLS'))) {
        } else {
            echo 'Некорректный тип файла';
            exit();
        }

        $backupFile =  str_replace('//','/',$backupPath) . date("Y-m-d_H-m-s").'__'. Tools::fname2iso($tmpFN);
        $targetFile =  str_replace('//','/',$targetPath) . Tools::fname2iso($tmpFN);

        //convert_file($tempFile, 'cp1251', 'utf8');

		if(move_uploaded_file($tempFile,$targetFile)) {
            copy($targetFile, $backupFile);
            echo "1|" . basename($targetFile);
        } else
			echo "0|".basename($targetFile);
		
	}
	exit();
}

$act=Tools::esc($_REQUEST['act']);

switch ($act)
{
    case 'get_config':
        $r->opt=$ci->getConfig();
        $r->CMI=$ci->CMI;
        $r->diaMergeNum=count($r->opt['diaMerge']);
        $r->svMergeNum=count($r->opt['svMerge']);
        $r->opt['cc_runflat_suffix']=Data::get('cc_runflat_suffix');
        break;

    case 'set_config':
        $ci->setConfig($r->param=Tools::parseStr($_REQUEST['f']));
        break;

    case 'getSuplrs':
        $cc=new CC_Ctrl();
        $r->suplrs=$cc->suplrList(['existSC'=>true, 'gr'=>$_REQUEST['gr']]);
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
            $r->name=$ci->name;
            $r->file_id=$ci->file_id;
            $r->gr=$ci->gr;
            $r->dt_added=$ci->dt_added;
            $r->deletedFiles=$ci->deletedFiles;
        }
        break;

    case 'files':
        $d=$ci->fetchAll("SELECT file_id,name,gr,status,param FROM cii_file WHERE NOT LD ORDER BY dt_added DESC");
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
            $d=$ci->fetchAll("SELECT cii_item.sup_id, cc_sup.name FROM cii_item INNER JOIN cc_sup ON cii_item.sup_id=cc_sup.sup_id WHERE cii_item.file_id='$file_id' GROUP BY cc_sup.name ORDER BY cc_sup.name",MYSQL_ASSOC);
            $r->supList=array();
            if(!empty($d)) foreach($d as $v) $r->supList[Tools::unesc($v['name'])]=$v['sup_id'];

        } else {
            $r->fres=false;
            $r->fres_msg='[getSup]: Файл не выбран';
        }
        break;

    case 'YSTGetSuplrName':
        $cfg=$ci->getConfig();
        $r->YSTSuplrName=@$cfg['YST']['suplrName'];
        break;

    case 'YSTChSuplrName':
        $cfg=$ci->getConfig();
        $cfg['YST']['suplrName']=@$_REQUEST['name'];
        $suplr=Tools::esc(@$_REQUEST['name']);
        $d=$ci->getOne("SELECT suplr_id FROM cc_suplr WHERE name != '' AND name LIKE '$suplr'");
        $r->suplrId=@$d[0];
        $ci->setConfig($cfg);

        break;

    case 'YSTList':
        $gr=(int)@$_REQUEST['gr'];
        if($gr==0) {
            $r->fres=false;
            $r->fres_msg='Группа не выбрана';
            break;
        }
        $cfg=$ci->getConfig();
        $cc=new CC_Ctrl;
        $cc->que('brands',$gr,0, 'AND sup_id=0');
        $d=$cc->fetchAll();

        $r->records=count($d);
        $r->page = 1;
        $r->total = 1;
        $i=0;
        foreach($d as $v){
            $r->rows[$i]['id']=$v['brand_id'];
            $r->rows[$i]['cell'][]=($v['replica'] && empty($v['sup_id'])?'Replica ':'').Tools::unesc($v['name']);
            $r->rows[$i]['cell'][]=@$cfg['YST']['bExtras'][$v['brand_id']];
            $i++;
        }
        break;

    case 'YSTMod':
        $r->textOutput=true;
        $brand_id=(int)@$_REQUEST['id'];
        $v=(float)@$_REQUEST['extra'];
        if(!$brand_id) {
            echo 'Бренд не выбран';
            break;
        }
        $cfg=$ci->getConfig();
        $cfg['YST']['bExtras'][$brand_id]=$v;
        $ci->setConfig($cfg);
        echo '1'; // 1 -- без релоада грид, 0 -- релоад грид
        break;

    case 'getDiasNum':
        $cfg=$ci->getConfig();
        $r->diaMergeNum=count($cfg['diaMerge']);
        break;

    case 'diasMod':
        $r->textOutput=true;
        $cfg=$ci->getConfig();
        $dia0=(string)str_replace(',','.',@$_REQUEST['dia0']);
        $dia1=(float)str_replace(',','.',@$_REQUEST['dia1']);
        $id=(string)str_replace(',','.',@$_REQUEST['id']);

        if(@$_REQUEST['oper']=='add'){
            if(isset($cfg['diaMerge'][$dia0])){
                echo 'Дубль значения исходного DIA';
                break;
            }
            if($dia0==0){
                echo "Исходный DIA не может быть нулевым";
                break;
            }

            $cfg['diaMerge'][$dia0]=$dia1;
        }elseif(@$_REQUEST['oper']=='edit'){
            if($id!=$dia0){
                if(isset($cfg['diaMerge'][$dia0])){
                    echo 'Дубль значения исходного DIA';
                    break;
                }else unset($cfg['diaMerge'][$id]);
            }
            $cfg['diaMerge'][$dia0]=$dia1;
        }elseif(@$_REQUEST['oper']=='del'){
            unset($cfg['diaMerge'][$id]);
        }
        unset($cfg['diaMerge']['']);
        $ci->setConfig($cfg);
        echo '0';
        break;

    case 'dias':
        $cfg=$ci->getConfig();
        $r->records=count($cfg['diaMerge']);
        $r->page = 1;
        $r->total = 1;
        $i=0;
        ksort($cfg['diaMerge'],SORT_NUMERIC );
        foreach($cfg['diaMerge'] as $k=>$v){
            $r->rows[$i]['id']=(string)$k;
            $r->rows[$i]['cell'][]=(string)$k;
            $r->rows[$i]['cell'][]=$v;
            $i++;
        }
        break;


    case 'getSVNum':
        $cfg=$ci->getConfig();
        $r->svMergeNum=count($cfg['svMerge']);
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
            if(isset($cfg['svMerge'][$sv0])){
                echo 'Дубль значения исходного LZ*PCD';
                break;
            }
            $cfg['svMerge'][$sv0]=$sv1;
        }elseif(@$_REQUEST['oper']=='edit'){
            $id_=explode('*',$id);
            if(count($id_)!=2) {
                echo 'Неверный формат ключа';
                break;
            }
            if($id!=$sv0){
                if(isset($cfg['svMerge'][$sv0])){
                    echo 'Дубль значения исходного LZ*PCD';
                    break;
                }else unset($cfg['svMerge'][$id]);
            }
            $cfg['svMerge'][$sv0]=$sv1;
        }elseif(@$_REQUEST['oper']=='del'){
            unset($cfg['svMerge'][$id]);
        }
        $ci->setConfig($cfg);
        echo '0';
        break;

    case 'SVs':
        $cfg=$ci->getConfig();
        $r->records=count($cfg['svMerge']);
        $r->page = 1;
        $r->total = 1;
        $i=0;
        ksort($cfg['svMerge'],SORT_NUMERIC );
        foreach($cfg['svMerge'] as $k=>$v){
            $r->rows[$i]['id']=(string)$k;
            $r->rows[$i]['cell'][]=(string)$k;
            $r->rows[$i]['cell'][]=implode('*',$v);
            $i++;
        }
        break;

    case 'sync':
        $sync=Cfg::get('dbsync');
        if(empty($sync['remote_sql_host']) || empty($sync['ci_sync'])) {
            $r->fres=2;
            $r->fres_msg='Удаленное обновление не доступно';
            break;
        }
        $r->siteName=$sync['siteName'];
        $r->turl=$sync['turl'];
        $r->durl=$sync['durl'];
        $dbr=new DB();
        $dbr->set_db($sync['remote_sql_host'],$sync['remote_sql_db'],$sync['remote_sql_user'],$sync['remote_sql_pass']);
        if(!$dbr->sql_connect()) {
            $r->fres=false;
            $r->fres_msg='Ошибка соединения с удаленным сервером для синхронизации';
            break;
        }
        $r->mysqlHost=$sync['remote_sql_host'];
        $db=new DB();

        // cc_suffix
        $d=$db->fetchAll("SELECT * FROM cc_suffix ORDER BY id",MYSQL_ASSOC);
        $dr=$dbr->fetchAll("SELECT * FROM cc_suffix ORDER BY id",MYSQL_ASSOC);
        $c=true;
        $e=current($d);
        $ed=current($dr);
        if(count($d)==count($dr))
            do{
                if($e['id']!=$ed['id']) $c=false;
                if($e['gr']!=$ed['gr']) $c=false;
                if($e['cSuffix']!=$ed['cSuffix']) $c=false;
                if($e['iSuffixes']!=$ed['iSuffixes']) $c=false;
                if($e['tag']!=$ed['tag']) $c=false;
                if($e['suffix1']!=$ed['suffix1']) $c=false;
    //			if($e['suffix2']!=$ed['suffix2']) $c=false;
                $e=next($d);
                $ed=next($dr);
            }while($c && $e!==false && $ed!==false);
        else  $c=false;
        if(!$c) {
            $r->smatrixMustBeUpdate=1;
        }else $r->smatrixMustBeUpdate=0;
        // ci_config
        $cfg=$ci->getConfig();
        $cfgr=$dbr->getOne("SELECT V FROM system_data WHERE name LIKE 'cii_config'");
        if(mb_strpos($cfgr['V'],':')!==false) $cfgr=unserialize(stripslashes($cfgr['V'])); else $cfgr=Tools::DB_unserialize($cfgr['V']);
        if(isset($cfgr['config'])) {
            $cfgr=array_merge($cfgr['config'],$cfgr);
            unset($cfgr['config']);
}
        $r->remoteCfg=$cfgr;
        if(count($r->diaMerge=Tools::arrayRecursiveDiff(@$cfgr['diaMerge'], @$cfg['diaMerge']))) $r->diaMerge='различия в элементах: '. implode(', ',array_keys($r->diaMerge)); else $r->diaMerge='--';
        if(count($r->svMerge=Tools::arrayRecursiveDiff(@$cfgr['svMerge'], @$cfg['svMerge']))) $r->svMerge='различия в элементах: '. implode(', ',array_keys($r->svMerge)); else $r->svMerge='--';
        if(@$cfg['emptyDiskSuffix']!=@$cfgr['emptyDiskSuffix']) $r->emptyDiskSuffix=@$cfgr['emptyDiskSuffix']; else $r->emptyDiskSuffix='--';
        if(@$cfg['ignoreDiskSuffixes']!=@$cfgr['ignoreDiskSuffixes']) $r->ignoreDiskSuffixes=@$cfgr['ignoreDiskSuffixes']; else $r->ignoreDiskSuffixes='--';
 //       if(@$cfg['replicaBrand']!=@$cfgr['replicaBrand']) $r->replicaBrand=@$cfgr['replicaBrand']; else $r->replicaBrand='--';
        break;

    case 'syncUpload':
        $u=@$_REQUEST['url'];
        $pi=pathinfo($u);
        $src = fopen($u, 'r');
        $fname=$_SERVER['DOCUMENT_ROOT'].'/tmp/'.$pi['basename'];
        $dest = fopen($fname, 'w');
        $r->fs=stream_copy_to_stream($src,$dest);
        $r->fname=$pi['basename'];
        break;

    case 'syncUpdateSMatrix':
        $sync=Cfg::get('dbsync');
        if(empty($sync['remote_sql_host'])) {
            $r->fres=true;
            $r->fres_msg='Удаленное обновление не доступно';
            break;
        }
        $dbr=new DB();
        $dbr->set_db($sync['remote_sql_host'],$sync['remote_sql_db'],$sync['remote_sql_user'],$sync['remote_sql_pass']);
        if(!$dbr->sql_connect()) return $this->putMsg(false,'Ошибка соединения с удаленным сервером для синхронизации');
        $r->mysqlHost=$sync['remote_sql_host'];
        $db=new DB();

        $db->query('TRUNCATE cc_suffix');
        $dr=$dbr->fetchAll("SELECT * FROM cc_suffix ORDER BY id",MYSQL_ASSOC);
        foreach($dr as $v){
            $db->insert('cc_suffix',array(
                'id'=>$v['id'],
                'gr'=>$v['gr'],
                'brand_id'=>$v['brand_id'],
                'cSuffix'=>$v['cSuffix'],
                'iSuffixes'=>$v['iSuffixes'],
                'suffix1'=>$v['suffix1'],
                'suffix2'=>$v['suffix2'],
                'img1'=>$v['img1'],
                'dt_added'=>$v['dt_added']
            ));
        }
        break;

    case 'sb_delTipos':
        $db=new CC_Base();
        $gr=(int)@$_REQUEST['gr'];
        $d=@$_REQUEST['date'];
        if(!preg_match("/([0-9]+)\.([0-9]+)\.([0-9]{4})/i",$d,$m)){
            $r->fres=false;
            $r->fres_msg='Не верная дата';
            break;
        }
        $d="{$m[3]}-{$m[2]}-{$m[1]}";
        $db->query("UPDATE cc_cat SET LD=1 WHERE gr=$gr AND cc_cat.dt_upd<'$d'");
        $r->num=$db->unum();

        break;

    case 'sb_getTipos':
        $db=new CC_Base();
        $gr=(int)@$_REQUEST['gr'];
        $d=@$_REQUEST['date'];
        if(!preg_match("/([0-9]+)\.([0-9]+)\.([0-9]{4})/i",$d,$m)){
            $r->fres=false;
            $r->fres_msg='Не верная дата';
            break;
        }
        $d="{$m[3]}-{$m[2]}-{$m[1]}";
        $r->num=$db->cat_view(array(
            'nolimits'=>1,
            'gr'=>$gr,
            'notH'=>false,
            'where'=>"cc_cat.dt_upd<'$d'"
        ));
        $r->data=array();
        while($db->next()!==false){
            $r->data[$db->qrow['cat_id']]=array(
                'bname'=>Tools::unesc($db->qrow['bname']),
                'mname'=>Tools::unesc($db->qrow['mname'])
            );
            if($gr==1){
                $r->data[$db->qrow['cat_id']]['size']=Tools::unesc("{$db->qrow['P3']}/{$db->qrow['P2']} R{$db->qrow['P1']} {$db->qrow['P7']} {$db->qrow['csuffix']}".($db->qrow['P6']?' ZR':''));
            }else{
                //{P2}xJ{P5} {P4}/{P6} ET{P1} DIA {P3}
                $r->data[$db->qrow['cat_id']]['size']=Tools::unesc("{$db->qrow['P2']}xJ{$db->qrow['P5']} {$db->qrow['P4']}/{$db->qrow['P6']} ET{$db->qrow['P1']} DIA {$db->qrow['P3']} {$db->qrow['csuffix']}");
            }
        }
        break;

    case 'exportGrid':
        $r->textOutput=true;
        $fn=preg_replace("~\..+$~u", '', @$_REQUEST['fn']);
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=\"$fn.csv\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        $file_id=(int)@$_REQUEST['file_id'];

        if (($gr=$_REQUEST['gr']) == 2) {
            $cc = new CC_Ctrl();
            $cc->load_sup(2);
            $ci->CMI[2]['Поставщик']['list'] = array_flip($cc->sup_arr);
        }

        $ci->query("SELECT * FROM cii_item WHERE file_id='$file_id' ORDER BY item_id", MYSQL_ASSOC);

        $a=['ИД размера', 'ИД модели', 'Статус размера', 'Статус модели', 'Статус бренда'];
        foreach($ci->CMI[$gr] as $k=>$v){
            if($k=='Трансформации') $a[]='Трансформации';
            elseif($k=='Поставщик') $a[]='Поставщик';
            else  $a[]=$ci->CMT[$gr][$k];

        }
        if($gr==2) $a[]=$ci->PCD2ColName;
        csvRow($a);

        while($ci->next()!==false) {
            $q=$ci->qrow;
            $a=[$q['cat_id'], $q['model_id'], $ci->status[$q['cstatus']], $ci->status[$q['mstatus']], $ci->status[$q['bstatus']]];

            foreach ($ci->CMI[$gr] as $k => $v) {

                switch ($v['type']) {
                    case 'string':
                        $a[]='"'.Tools::unesc($q[$v['item_field']]).'"';
                        break;
                    case 'integer':
                    case 'price':
                    case 'float':
                        $a[]=Tools::n(1*$q[$v['item_field']]);
                        break;
                    case 'id':
                        $a[]=@Tools::mb_array_search(Tools::unesc($q[$v['item_field']]), $v['list']);
                        break;

                }
            }
            if($gr==2)  $a[]='';
            csvRow($a);
        }
        exit;
        break;

    case 'filesHistory':
        $d=$ci->fetchAll("SELECT * FROM cii_file ORDER BY dt_added DESC");
        $r->files=array();
        foreach($d as $v) {
            $param = Tools::DB_unserialize($v['param']);
            //unset($param['result']['diaMerge'], $param['result']['svMerge'], $param['result']['blist'], $param['result']['exSuffixes'], $param['result']['runflatSuffixes']);
            if(!empty($param['result']['suplrsFromFile'])) $param['result']['suplrsFromFile']=implode(', ', $param['result']['suplrsFromFile']);
            if(!empty($param['result']['brandsFromFile'])) $param['result']['brandsFromFile']=implode(', ', $param['result']['brandsFromFile']);
            if(!empty($param['result']['delSuplrs'])) $param['result']['delSuplrs']=implode(', ', $param['result']['delSuplrs']);
            if(!empty($param['result']['suplrListFromFile'])) $param['result']['suplrListFromFile']=implode(', ', $param['result']['suplrListFromFile']);
            if(!empty($param['result']['bExtras'])) $param['result']['bExtras']=implode(', ', $param['result']['bExtras']);
            if(!empty($param['opt']['YST']['bExtras'])) $param['opt']['YST']['bExtras']=json_encode($param['opt']['YST']['bExtras'], JSON_UNESCAPED_SLASHES);
/*
            $r->files[] = array(
                'id' => $v['file_id'],
                'label' => $v['name'],
                'status' => $v['status'],
                'statusLabel' => ($v['status'] == 1 ? 'импортирован' : 'не импортирован'),
                'gr' => $v['gr'],
                'param' => $param,
                'SID' => $v['SID']
            );
*/
        }

        $d=$ci->fetchAll("select cs.name, (SELECT DATE(MAX(GREATEST(ccs.dt_upd, ccs.dt_added))) AS d FROM cc_cat_sc ccs WHERE ccs.suplr_id=cs.suplr_id) AS d FROM cc_suplr cs ORDER BY d DESC, name ASC", MYSQL_ASSOC);

        $r->table=[];
        foreach($d as $v){
            if($v['d'] && $v['d']!='0000-00-00') {
                $r->table[] = [
                    'name' => $v['name'],
                    'd' => Tools::sdate($v['d'])
                ];
            }
        }
        break;

    case 'infoTab':
        $r->textOutput=true;
        $ss=new Content();
        echo $ss->getDoc("cii_info");
        break;

    case 'parse':
        $task=MC::sget('ciidm.task');
        if(@$task['state']=='exec' || @$task['state']=='new'){
            $r->fres=false;
            $r->fres_msg='Другая задача уже выполняется. Дождитесь окончания.';
            break;
        }
        $f=Tools::parseStr(@$_REQUEST['configFrm']);
        $ci->setConfig($r->cfg=@array_diff_assoc($f, ['delSuplrs'=>[] ]));
        $f=array_merge($ci->getConfig(), $f);
        $f['mode']=@$_REQUEST['mode'];
        $f['file_id']=(int)@$_REQUEST['file_id'];
        $f['fileName']=@$_REQUEST['fileName'];
        $ci=new App_Import_CIIdm();
        ob_start();
        $ci->dm_newTask($f);
        ob_clean();
        $r->opt=$f;
        break;

    case 'DM_pingState':
        $ci=new App_Import_CIIdm();
        $r->data=$ci->dm_getState(@$_REQUEST['lastTS']);
        break;

    case 'DM_sendCommand':
        $ci=new App_Import_CIIdm();
        $r->cmds=$ci->DM_pushCmd(@$_REQUEST['cmd']);
        break;

    default:
        $r->fres=false;
        $r->fres_msg= 'BAD ACT ID '.$act;

}


function csvRow($a)
{
    echo Tools::cp1251(implode(';',$a))."\n";
}


ajxEnd();
