<?
require_once '../auth.php';
include('../struct.php');

$cp->frm['name']='catalog_bot';
$cp->checkPermissions();

$cc=new CC_Ctrl;
$cc->load_cur();


$gr=@$_REQUEST['gr'];

if(!empty($_REQUEST['modelId'])){

    $cc->que('model_by_id',$_REQUEST['modelId']);
    if($cc->next()!==false){
        $cp->frm['title']=$title="Все типоразмеры модели {$cc->qrow['bname']} {$cc->qrow['name']}";
    }

}else{

    if ($gr==1) $cp->frm['title']='База размеров шин'; elseif($gr==2) $cp->frm['title']='База размеров дисков';
}

cp_head();
cp_css();
cp_js();
cp_body();

// TODO убрать же этот ужос наконец!
foreach ($_GET as $key=>$value) if(!is_array($value))  $$key=Tools::esc($value); else $$key=$value;
foreach ($_POST as $key=>$value) if(!is_array($value))  $$key=Tools::esc($value); else $$key=$value;

$dataset_id=@$_REQUEST['dataset_id'];
$ds=new CC_Dataset();
if($dataset_id) $ds->getDataset($dataset_id);

cp_title(!empty($title)?true:false);



if($gr!=1 && $gr!=2) {
    note('Необходимо задать критерий поиска...');
    cp_end();
    exit;
}

$cc->load_sup($gr);

// доп поля - отображение
$aft=App_TFields::get('cc_cat','input',$gr);

$afcoo=@unserialize(Tools::unesc($_COOKIE['__cp_catalog_bot_af']));

// определяем условие запроса на выборку
$aq=array();
if(@$scNotZero!='') $aq[]='(cc_cat.sc>'.@$scNotZero.')';
if(@$scZero!='') $aq[]='(cc_cat.sc=0)';
if(@$onlyH!='') $aq[]='(cc_cat.H)';
if(@$scpriceNotZero!='') $aq[]='(cc_cat.scprice>0)';
if(@$zeroBprice!='') $aq[]='(cc_cat.bprice=0)';
if(@$not_zeroBprice!='') $aq[]='(cc_cat.bprice>0)';
if(@$emptySuffix!='') $aq[]="(cc_cat.suffix='')";
if(@$fotoExists) $aq[]="(cc_model.img1!='')";
if(@$fix_price) $aq[]="(cc_cat.fixPrice=1)";
if(@$fix_sc) $aq[]="(cc_cat.fixSc=1)";
if(@$suplrs_filter){
    $db = new DB();
    $spl_cids = $db->fetchAll("SELECT DISTINCT cat_id FROM cc_cat_sc WHERE suplr_id = '$suplrs_filter'", MYSQL_ASSOC);
    if (!empty($spl_cids)) {
        $spl_cids_array = Array();
        foreach ($spl_cids as $s) {
            $spl_cids_array[] = "'" . $s['cat_id'] . "'";
        }
        $aq[] = '(cc_cat.cat_id IN (' . implode(',', $spl_cids_array) . '))';
    }
    else $aq[] = '(cc_cat.cat_id = NULL)';
}
if(@$ignoreUpdate) $aq[]="(cc_cat.ignoreUpdate=1)";
if(@$is_balances) $aq[]="(cc_cat.is_balances=1)";
if(@$is_not_balances) $aq[]="(cc_cat.is_balances=0)";
if(@$is_not_updated_month) $aq[]="(cc_cat.dt_upd < '".date('Y-m-d H:i:s', strtotime('-1 month'))."')";
if(@$replica==='0') $aq[]="(NOT cc_brand.replica)"; elseif(@$replica==='1') $aq[]="(cc_brand.replica)";
if(isset($P7_1) && $P7_1==='') $aq[]="(cc_cat.P7='')"; elseif(@$P7_1=='---') $P7_1='';

if(@$search!=''){
    $search=Tools::esc($search);
    if(in_array(Cfg::get('CAT_IMPORT_MODE'),array(1,3))){
        if($search==='0')
            $aq[]="(cc_cat.ti_id=0)";
        elseif(Tools::typeOf($search)=='integer')
            $aq[]="(cc_model.name LIKE '%{$search}%' OR cc_cat.ti_id='{$search}' OR cc_cat.suffix LIKE '%{$search}%')";
        else
            $aq[]="(cc_model.name LIKE '%{$search}%' OR cc_cat.suffix LIKE '%{$search}%')";
    }else{
        if(Tools::typeOf($search)=='integer')
            $aq[]="(cc_model.name LIKE '%{$search}%' OR cc_cat.cat_id='{$search}' OR cc_cat.suffix LIKE '%{$search}%')";
        else
            $aq[]="(cc_model.name LIKE '%{$search}%' OR cc_cat.suffix LIKE '%{$search}%')";
    }
}
if(!empty($P123_1)){
    $t=explode('-',$P123_1);
    if(isset($P1_1) && $P1_1!=='') $P1_1=['list'=>[(float)$t[0],(float)$P1_1]]; else $P1_1=(float)$t[0];
    if(isset($P2_1) && $P2_1!=='') $P2_1=['list'=>[(float)$t[1],(float)$P2_1]]; else $P2_1=(float)$t[1];
    if(isset($P3_1) && $P3_1!=='') $P3_1=['list'=>[(float)$t[2],(float)$P3_1]]; else $P3_1=(float)$t[2];
}

if(isset(App_TFields::$fields['cc_cat']['app'])) if(@$emptyApp!='') $aq[]="(cc_cat.app='')";


if (isset($cedit_post)){
    $P1=floatval($P1);
    $P2=floatval($P2);
    $P3=floatval($P3);
    if (!isset($P4)) $P4=0; else $P4=floatval($P4);
    if (!isset($P5)) $P5=0; else $P5=floatval($P5);
    if (!isset($P6)) $P6=0; else $P6=floatval($P6);
    if (!isset($P7)) $P7=''; else $P7=Tools::esc($P7);
    if($gr==1) $P4=$cc->isCinSuffix($suffix);
    $base_price=floatval($base_price);
    $suffix=Tools::esc($suffix);
    $scprice=floatval($scprice);
    $cprice=floatval($cprice);
    $fixPrice=(int)@$fixPrice;
    $fixSc=(int)@$fixSc;

    $is_seo = (int)@($is_seo);
    $seo_title = Tools::esc($seo_title);
    $seo_keywords = Tools::esc($seo_keywords);
    $seo_description = Tools::esc($seo_description);
    $adv_text = Tools::esc($adv_text);
    $app = !empty($app) ? Tools::esc(serialize($app)) : '';

    $a=App_TFields::DBupdate('cc_cat',@$af,$gr);
    $s="UPDATE cc_cat SET model_id='$model_id', scprice='$scprice'{$a}, suffix='$suffix',
      fixPrice='$fixPrice', fixSc='$fixSc', P1='$P1', P2='$P2', P3='$P3', P4='$P4', P5='$P5', P6='$P6', P7='$P7',
      bprice='$base_price', cprice='$cprice', cur_id='$cur_id',
      adv_text= '$adv_text',  is_seo = '$is_seo', seo_title = '$seo_title', seo_keywords = '$seo_keywords', seo_description = '$seo_description', app = '$app'
       WHERE cat_id='$cedit_id'";

    if (!$cc->query($s)) warn('<strong>Ошибка записи. Запись не обновлена.</strong>'); else{
        if($model_id!=$model_id0) $cc->sname_cat($cedit_id,$sname,true);
        elseif($sname0!=$sname) $cc->sname_cat($cedit_id,$sname,false);
        $cc->extra_price_update($cedit_id);
        if($gr==1 && Cfg::get('INIS_S1S2')) CC_inis::modelUpdate($model_id);
        if($gr==2 && isset($cc->RDisk)) $cc->RDisk->modelUpdate($model_id);
        if($gr==1 && isset($cc->RTyre)) $cc->RTyre->modelUpdate($model_id);
        if(isset($cc->intPrice)) $cc->intPrice->modelUpdate($model_id);
        if($model_id0!=$model_id){
            if($gr==1 && Cfg::get('INIS_S1S2')) CC_inis::modelUpdate($model_id0);
            if($gr==2 && isset($cc->RDisk)) $cc->RDisk->modelUpdate($model_id0);
            if($gr==1 && isset($cc->RTyre)) $cc->RTyre->modelUpdate($model_id0);
            if(isset($cc->intPrice)) $cc->intPrice->modelUpdate($model_id0);
            if(Cfg::get('model_SC')) CC_ModelSC::modelUpdate($model_id);
            if(Cfg::get('model_SC')) CC_ModelSC::modelUpdate($model_id0);
        }
        $cc->addCacheTask('sizes',$gr);
    }

}elseif (@$cedit_id>0){

    include('catalog_bot_cedit.php');
    cp_end();
    exit();
}

if (isset($medit_post) || isset($medit_post1)) {
    $text=@$tmh_text!=''?$tmh_text:$text;
    if ($cc->model_ae('edit',array(
        'gr'=>$gr,
        'model_id'=>$medit_id,
        'af'=>@$af,
        'brand_id'=>$brand_id_,
        'sup_id'=>$sup_id,
        'name'=>$name,
        'sname'=>$sname,
        'mspez_id'=>@$mspez_id,
        'class_id'=>@$class_id,
        'suffix'=>$suffix,
        'text'=>$text,
        'P1'=>@$P1,
        'P2'=>@$P2,
        'P3'=>@$P3,
        'imgFileFileld' => 'imgFile',
        'spyUrl'=>@$spyUrl,
        'delImg'=>@$delImg,
        'alt'=>$alt,
        'hit_quant'=>$hit_quant
    ))) {

        note('Модель отредактирована');

        if(isset($ti_id)){
            $ti_id=(int)$ti_id;
            if($ti_id!=0) $d=$cc->getOne("SELECT count(ti_id) FROM cc_model WHERE gr='$gr' AND model_id!='$medit_id' AND ti_id='$ti_id'");
            if($ti_id!=0 && $d[0]) warn("Код ТИ модели $ti_id уже присутсвует у другой модели - не обновлен"); else
                $cc->query("UPDATE cc_model SET ti_id='$ti_id' WHERE model_id='$medit_id'");
        }

    }else warn('Ошибка в процессе обновления модели' . (!empty($cc->fres_msg) ? (". ".$cc->strMsg()) : ''));

}elseif (@$medit_id>0){
    include('catalog_bot_medit.php');
    exit();
}

if(@$_POST['linkDataset']==1 && $dataset_id){
    $i=0;
    $ids=array();
    foreach($_POST as $k=>$v) {
        $x=explode('_',$k);
        if(@$x[0]=='c' && @$x[1]>0){ // x[1] - id
            $ids[$x[1]]=(int)$x[1];
        }
    }
    if(count($ids)){
        $d=$ds->fetchAll("SELECT cat_id FROM cc_dataset_cat WHERE dataset_id='$dataset_id' AND cat_id IN (".join(',',$ids).")");
        $dd=array();
        foreach($d as $v) $dd[$v['cat_id']]=$v['cat_id'];
        $ids=array_diff($ids,$dd);
        if(count($ids)){
            $d=$ds->fetchAll("SELECT cc_cat.cat_id, cc_model.model_id, cc_model.brand_id FROM cc_cat INNER JOIN cc_model ON cc_cat.model_id=cc_model.model_id WHERE cc_cat.cat_id IN (".join(',',$ids).")");
            foreach($d as $v){
                $ds->query("INSERT INTO cc_dataset_cat (dataset_id,brand_id,model_id,cat_id) VALUES('$dataset_id','{$v['brand_id']}','{$v['model_id']}','{$v['cat_id']}')");
                $i++;
            }
        }
    }else{
        // добавляем все на всех страницах
        $cc->query("SELECT cat_id FROM cc_dataset_cat JOIN cc_cat USING (cat_id) WHERE cc_cat.gr='$gr' AND dataset_id='$dataset_id'");
        $ds_ids=array();
        if($cc->qnum()) {
            while($cc->next(MYSQL_NUM)!==false)
                $ds_ids[]=$cc->qrow[0];
        }

        $aqs=join('AND',$aq);

        if ($gr==2){

            $num=$cc->cat_view(array(
                'gr'=>$gr,
                'nolimits'=>1,
                'brand_id'=>@$brand_id_2,
                'P1'=>@$P1_2,
                'P2'=>@$P2_2,
                'P3'=>@$P3_2,
                'P4'=>@$P4_2,
                'P5'=>@$P5_2,
                'P6'=>@$P6_2,
                'M1'=>@$MP1_2,
                'add_query'=>$aqs,
                'fields'=>'cc_cat.cat_id, cc_cat.model_id, cc_model.brand_id',
                'H'=>0,
                'sup_id'=>@$sup_id_2
            ));

        }elseif ($gr==1){

            if (!isset($P6_1)) $P6_1='';
            if (!isset($P4_1)) $P4_1='';
            if (!isset($MP1_1)) $MP1_1='';
            if (!isset($MP3_1)) $MP3_1='';
            $num=$cc->cat_view(array(
                'gr'=>$gr,
                'nolimits'=>1,
                'brand_id'=>@$brand_id_1,
                'P1'=>@$P1_1,
                'P2'=>@$P2_1,
                'P3'=>@$P3_1,
                'P4'=>@$P4_1,
                'M1'=>@$MP1_1,
                'M2'=>@$MP2_1,
                'M3'=>@$MP3_1,
                'P6'=>@$P6_1,
                'P7'=>@$P7_1,
                'add_query'=>$aqs,
                'fields'=>'cc_cat.cat_id, cc_cat.model_id, cc_model.brand_id',
                'H'=>0,
                'sup_id'=>@$sup_id_1
            ));
        }

        if($num) {
            $db=new DB();
            while($cc->next(MYSQL_ASSOC)){
                if(!in_array($cc->qrow['cat_id'],$ds_ids)){
                    $db->insert('cc_dataset_cat',array('dataset_id'=>$dataset_id,'brand_id'=>$cc->qrow['brand_id'],'model_id'=>$cc->qrow['model_id'],'cat_id'=>$cc->qrow['cat_id']));
                    $i++;
                }
            }
        }
        unset($ds_ids,$db);
    }

    note ("Включено в набор <b>$i</b> размеров. Размеры, добавленные в выбранный набор будут отображаться в списке на зеленом фоне.");
}

if(@$_POST['unlinkDataset']==1 && $dataset_id){
    $i=0;
    $ids=array();
    foreach($_POST as $k=>$v) {
        $x=explode('_',$k);
        if(@$x[0]=='c' && @$x[1]>0){ // x[1] - id
            $ids[$x[1]]=(int)$x[1];
        }
    }
    if(count($ids)){
        $ds->query("DELETE FROM cc_dataset_cat WHERE dataset_id='$dataset_id' AND cat_id IN (".join(',',$ids).")");
        $i=$ds->updatedNum();
    }else{
        // удаляем все на всех страницах

        $aqs=join('AND',$aq);

        if ($gr==2){

            $num=$cc->cat_view(array(
                'gr'=>$gr,
                'nolimits'=>1,
                'brand_id'=>@$brand_id_2,
                'P1'=>@$P1_2,
                'P2'=>@$P2_2,
                'P3'=>@$P3_2,
                'P4'=>@$P4_2,
                'P5'=>@$P5_2,
                'P6'=>@$P6_2,
                'M1'=>@$MP1_2,
                'add_query'=>$aqs,
                'fields'=>'cc_cat.cat_id, cc_cat.model_id, cc_model.brand_id',
                'H'=>0,
                'sup_id'=>@$sup_id_2,
                'datasetTo'=>@$inDSonly?'cat':'',
                'dataset_id'=>$dataset_id
            ));

        }elseif ($gr==1){

            if (!isset($P6_1)) $P6_1='';
            if (!isset($P4_1)) $P4_1='';
            if (!isset($MP1_1)) $MP1_1='';
            if (!isset($MP3_1)) $MP3_1='';
            $num=$cc->cat_view(array(
                'gr'=>$gr,
                'nolimits'=>1,
                'brand_id'=>@$brand_id_1,
                'P1'=>@$P1_1,
                'P2'=>@$P2_1,
                'P3'=>@$P3_1,
                'P4'=>@$P4_1,
                'M1'=>@$MP1_1,
                'M2'=>@$MP2_1,
                'M3'=>@$MP3_1,
                'P6'=>@$P6_1,
                'P7'=>@$P7_1,
                'add_query'=>$aqs,
                'fields'=>'cc_cat.cat_id, cc_cat.model_id, cc_model.brand_id',
                'H'=>0,
                'sup_id'=>@$sup_id_1,
                'datasetTo'=>@$inDSonly?'cat':'',
                'dataset_id'=>$dataset_id
            ));
        }

        if(!empty($num)) {
            $db=new DB();
            $ids=[];
            while($cc->next(MYSQL_ASSOC)){
                $ids[]=$cc->qrow['cat_id'];
                if(count($ids)>=100){
                    $db->query("DELETE FROM cc_dataset_cat WHERE dataset_id='$dataset_id' AND cat_id IN (".join(',',$ids).")");
                    $i+=$ds->updatedNum();
                }
            }
            if(count($ids)){
                $db->query("DELETE FROM cc_dataset_cat WHERE dataset_id='$dataset_id' AND cat_id IN (".join(',',$ids).")");
                $i+=$ds->updatedNum();
            }
        }
        unset($ds_ids,$db);
    }

    note ("Исключено из набора <b>$i размеров</b>.");
}



if (@$ld_id>0) if ($cc->ld('cc_cat','cat_id',$ld_id,$gr)) {
    note('Размер удален');
}

if(@$del_sel>0){
    $i=0;
    foreach($_POST as $k=>$v) {
        $x=explode('_',$k);
        if(@$x[0]=='c' && @$x[1]>0){
            if($cc->ld('cc_cat','cat_id',$x[1])) $i++;
        }
    }
    note("Удалено $i типоразмеров");
}

if(@$act=='hide_sel' || @$act=='show_sel'){
    $i=0;
    $models=array();
    if($act=='hide_sel') $h=1; else $h=0;
    foreach($_POST as $k=>$v) {
        $x=explode('_',$k);
        if(@$x[0]=='c' && @$x[1]>0){
            $cc->query("UPDATE cc_cat SET H=$h WHERE cat_id='{$x[1]}'");
            $un=$cc->updatedNum();
            if($un){
                $cc->query("SELECT model_id FROM cc_cat WHERE cat_id='{$x[1]}'");
                $cc->next();
                $models[]=$cc->qrow['model_id'];
            }
            $i+=$un;

        }
    }
    if($i){
        foreach($models as $model_id){
            if($gr==1 && Cfg::get('INIS_S1S2')) CC_inis::modelUpdate($model_id);
            if($gr==2 && isset($cc->RDisk)) $cc->RDisk->modelUpdate($model_id);
            if($gr==1 && isset($cc->RTyre)) $cc->RTyre->modelUpdate($model_id);
            if(isset($cc->intPrice)) $cc->intPrice->modelUpdate($model_id);
            if(Cfg::get('model_SC')) CC_ModelSC::modelUpdate($model_id);
        }
        $cc->addCacheTask('sizes',$gr);
    }
    note("Изменен статус для $i размеров");
}

if(@$act=='add_to_balances_sel' || @$act=='remove_from_balances_sel'){
    $i=0;
    $models=array();
    if($act=='add_to_balances_sel') $h=1; else $h=0;
    foreach($_POST as $k=>$v) {
        $x=explode('_',$k);
        if(@$x[0]=='c' && @$x[1]>0){
            $cc->query("UPDATE cc_cat SET is_balances=$h WHERE cat_id='{$x[1]}'");
            $un=$cc->updatedNum();
            $i+=$un;

        }
    }
    note(($h == 1 ? 'В остатки добавлено' : 'Из остатков удалено')." $i типоразмеров");
}

if(@$act=='upd'){
    $c=0;
    $f=array();
    $pricesUpd=false;
    $sizesUpd=false;
    $modelUpd=false;
    foreach($_POST as $k=>$v) {
        $x=explode('_',$k);
        if(@$x[1]>0 && !isset($f[$x[1]])){
            $a=array();
            $f[$x[1]]=1;
            $sr=false;
            if(isset($_POST['sc_'.$x[1]])) {$a[]="sc='".intval($_POST['sc_'.$x[1]])."'"; $modelUpd=true;}
            if(isset($_POST['scprice_'.$x[1]])) $a[]="scprice='".floatval($_POST['scprice_'.$x[1]])."'";
            if(isset($_POST['csuffix_'.$x[1]])) {
                $a[]="suffix='".($suf=Tools::esc($_POST['csuffix_'.$x[1]]))."'";
                if($gr==1) $a[]="P4=".$cc->isCinSuffix($suf);
                $sr=true;
            }
            if($gr==1) {
                if(isset($_POST['uP1_'.$x[1]])) {
                    if(strpos($_POST['uP1_'.$x[1]],'Z')!==false) $a[]="P6=1"; else $a[]="P6=0";
                    $a[]="P1='".floatval(preg_replace("/[ZR]/",'',$_POST['uP1_'.$x[1]]))."'";
                    $sizesUpd=true;
                }
            }elseif(isset($_POST['uP1_'.$x[1]])) {$a[]="P1='".floatval($_POST['uP1_'.$x[1]])."'"; $sizesUpd=true;}
            if(isset($_POST['uP2_'.$x[1]])) {$a[]="P2='".floatval($_POST['uP2_'.$x[1]])."'"; $sizesUpd=true;}
            if(isset($_POST['uP3_'.$x[1]])) {$a[]="P3='".floatval($_POST['uP3_'.$x[1]])."'"; $sizesUpd=true;}
            if(isset($_POST['uP4_'.$x[1]])) {$a[]="P4='".floatval($_POST['uP4_'.$x[1]])."'"; $sizesUpd=true;}
            if(isset($_POST['uP5_'.$x[1]])) {$a[]="P5='".floatval($_POST['uP5_'.$x[1]])."'"; $sizesUpd=true;}
            if(isset($_POST['uP6_'.$x[1]])) {$a[]="P6='".floatval($_POST['uP6_'.$x[1]])."'"; $sizesUpd=true;}
            if(isset($_POST['uP7_'.$x[1]])) {$a[]="P7='".Tools::esc($_POST['uP7_'.$x[1]])."'"; $sizesUpd=true;}
            if(isset($_POST['ubprice_'.$x[1]])) {$a[]="bprice='".floatval($_POST['ubprice_'.$x[1]])."'"; $pricesUpd=true;}
            if(isset($_POST['ucprice_'.$x[1]])) {$a[]="cprice='".floatval($_POST['ucprice_'.$x[1]])."'";}
            //			if(isset($_POST['tiBrand_'.$x[1]])) $a[]="bti='".intval($_POST['tiBrand_'.$x[1]])."'";
            //			if(isset($_POST['tiModel_'.$x[1]])) $a[]="mti='".intval($_POST['tiModel_'.$x[1]])."'";
            if(isset($_POST['tiTipo_'.$x[1]])) $a[]="ti_id='".intval($_POST['tiTipo_'.$x[1]])."'";
            if(count($aft))
                foreach($aft as $k=>$v)
                    if(@$afcoo[$k] && isset($_POST[$k.'_'.$x[1]])) $a[]="$k='".Tools::esc($_POST[$k.'_'.$x[1]])."'";
            if(count($a)){
                $q=implode(',',$a);
                $cc->query("UPDATE cc_cat SET $q WHERE cat_id='{$x[1]}'");
                if($c+=$cc->updatedNum()) $cc->sname_cat($x[1]);
            }
        }
    }
    if($pricesUpd && $c) $cc->addCacheTask('prices',$gr);
    if($sizesUpd && $c) $cc->addCacheTask('sizes modAll',$gr);
    if($modelUpd && $c) $cc->addCacheTask('modelsSC',$gr);
    note("Отредактировано $c размеров");
}
if(@$act=='mbp'){
    $bp=floatval($multi_bprice);
    $aa=array();
    $c=0;
    foreach($_POST as $k=>$v) {
        $x=explode('_',$k);
        if(@$x[0]=='c' && @$x[1]>0){
            $cc->query("UPDATE cc_cat SET bprice='$bp', cur_id='$cur_id' WHERE cat_id='{$x[1]}'");
            $c+=$cc->updatedNum();
            $cc->extra_price_update($x[1]);
            $cc->que('cat_by_id',$x[1]);
            $cc->next();
            if(isset($cc->intPrice)) $cc->intPrice->modelUpdate($cc->qrow['model_id']);
        }
    }
    note("Базовая цена изменена для $c размеров");
}

if(@$act=='replace'){
    $s1=Tools::esc($replaceSource);
    $s2=Tools::esc($replaceStr);
    $c=0;
    if($replaceField=='mname'){
        $s11=Tools::like_($s1);
        $cc->query("SELECT model_id FROM cc_model WHERE gr='{$gr}' AND name LIKE '%$s11%' AND NOT LD");
        $in=array();
        while($cc->next()!==false) $in[]=$cc->qrow['model_id'];
        if(count($in)){
            $cc->query("UPDATE cc_model SET name=REPLACE(name,'{$s1}','{$s2}') WHERE gr='{$gr}' AND NOT LD");
            $c=$cc->updatedNum();
            if($c) {
                foreach($in as $v) $cc->sname_model($v);
            }
        }
    }
    if($replaceField=='csuffix'){
        $s11=Tools::like_($s1);
        $cc->query("SELECT cat_id FROM cc_cat WHERE gr='{$gr}' AND suffix LIKE '%$s11%' AND NOT LD");
        $in=array();
        while($cc->next()!==false) $in[]=$cc->qrow['cat_id'];
        if(count($in)){
            $cc->query("UPDATE cc_cat SET suffix=REPLACE(suffix,'{$s1}','{$s2}') WHERE gr='{$gr}' AND NOT LD");
            $c=$cc->updatedNum();
            if($c) {
                foreach($in as $v) {
                    $cc->sname_cat($v);
                    if($gr==1){
                        $r=$cc->getOne("SELECT suffix,P4 FROM cc_cat WHERE cat_id=$v");
                        $p4=$cc->isCinSuffix($r['suffix']);
                        if($r['P4']!=$p4) $cc->query("UPDATE cc_cat SET P4=$p4 WHERE cat_id=$v");
                    }
                }
            }
        }
    }
    if($replaceField=='app'){
        $cc->query("UPDATE cc_cat SET app=REPLACE(app,'{$s1}','{$s2}') WHERE gr='{$gr}' AND NOT LD");
        $c=$cc->updatedNum();
    }
    note("Замена &quot;$s1&quot; на &quot;$s2&quot; для $c размеров OK");
}
if(@$act=='sc_all'){
    $sc=intval($new_sc);
    $cc->query("UPDATE cc_cat SET sc='$sc' WHERE NOT LD AND gr='$gr'");
    $un=$cc->updatedNum();
    if($un) $cc->addCacheTask('modelsSC',$gr);
    note('Обновлено '.$un.' записей.');
}

if(@$act=='sc_select'){
    $sc=intval($new_sc);
    $a=array();
    foreach($_POST as $k=>$v) {
        $x=explode('_',$k);
        if(@$x[0]=='c' && @$x[1]>0) $a[]=$x[1];
    }
    if(count($a)) {
        $cc->query("UPDATE cc_cat SET sc='$sc' WHERE NOT LD AND gr='$gr' AND cat_id IN (".implode(',',$a).")");
        $cou=$cc->updatedNum();
    }else $cou=0;
    if($cou) $cc->addCacheTask('modelsSC',$gr);
    note('Обновлено '.$cou.' записей.');
}

if(@$act=='etalonSW'){
    $a=array();
    foreach($_POST as $k=>$v) {
        $x=explode('_',$k);
        if(@$x[0]=='c' && @$x[1]>0) $a[]=$x[1];
    }
    if(count($a)) {
        $cc->query("UPDATE cc_cat SET etalon=IF(`etalon`=1,0,1) WHERE NOT LD AND gr='$gr' AND cat_id IN (".implode(',',$a).")");
        $cou=$cc->updatedNum();
    }else $cou=0;
    note('Обновлено '.$cou.' записей.');
}

if(@$act=='ti_zero_all'){
    $cc->query("UPDATE cc_cat SET ti_id=0,ti_file_id=0 WHERE gr='$gr'");
    $cc->query("UPDATE cc_model SET ti_id=0,ti_file_id=0 WHERE gr='$gr'");
    $cc->query("UPDATE cc_brand SET ti_id=0,ti_file_id=0 WHERE gr='$gr'");
    note('Привязки ТайрИндекс удалены');
}


if(@$act=='ti_zero_sel'){
    echo '<p>Удаление привязки ТайрИндекс...';$c=0;
    $i=0;
    foreach($_POST as $k=>$v) {
        $x=explode('_',$k);
        if(@$x[0]=='c' && @$x[1]>0){
            /*			$d=$cc->getOne("SELECT model_id FROM cc_cat WHERE cat_id='{$x[1]}'");
                        $model_id=@$d[0];
                        $cc->query("UPDATE cc_cat SET ti_id=0,ti_file_id=0 WHERE cat_id='{$x[1]}' ");
                        $d=$cc->getOne("SELECT brand_id FROM cc_model WHERE model_id='{$model_id}'");
                        $brand_id=@$d[0];
                        $cc->query("UPDATE cc_model SET ti_id=0,ti_file_id=0 WHERE model_id='$model_id' ");
                        $cc->query("UPDATE cc_brand SET ti_id=0,ti_file_id=0 WHERE brand_id='$brand_id'");*/
            $cc->query("UPDATE cc_cat SET ti_id=0,ti_file_id=0 WHERE cat_id='{$x[1]}' ");

            $i++;
        }
    }
    note('Привязки ТайрИндекс удалены для '.$i.' размеров');
}

if (!isset($page)) $page=0;
if (!isset($lines)) $lines=50; elseif (intval($lines)==0) $lines=50;



function view_pages($num,$page,$lines)
{
    echo 'Страницы: ';
    $p=0;
    for($i=0;$i<=$num;$i=$i+$lines){
        echo '<a class="pages'.($p==$page?'_sel':'').'" href="#" page="'.$p.'">';
        echo $p+1;
        echo "</a> &nbsp;";
        $p++;
    }
}

?>
    <style type="text/css">
        .ltable INPUT {width:100px}
        INPUT.sc_{width:30px; text-align:center}
        INPUT.scprice_{width:50px; text-align:center}
        INPUT.csuffix_{width:300px;}
        .pages{ line-height:20px; font-size:12px; padding:0 3px;}
        .pages_sel{ line-height:20px; font-weight:bold; color:#FF0000; font-size:14px;}
        fieldset.af{margin:10px 0; padding:5px 5px 10px 5px; display:none}
        fieldset.af span{margin:0 10px}
        #groupAfToggle{margin:0 0 0 20px;}
        .msg-block{
            margin:5px; 0;
        }
        a.cprice{color:inherit; text-decoration:none}

        #sclPopup{
            display:none;
            border-radius:2px;
            box-shadow:0 0 5px black;
            position:absolute;
            background:#eeeeee;
            z-index:999 !important;
            overflow:hidden;
            padding:15px;
            min-width:200px;
            min-height:40px;
        }
        #sclPopup h3{
            font-size:16px;
            margin:0 0 10px 0;
        }
        #sclPopup #c table{
        }
        tr.fprice td{
            background: #FFF000;
        }
    </style>
    <div id="sclPopup">
        <div id="c" style="overflow:hidden"></div>
        <div id="loader" style="display:block; overflow:hidden; margin:10px 0 10px 0"><img src="../img/loader.large.gif"></div>
    </div>

<? if(isset($_GET['lines']) || isset($_GET['modelId'])){?>

    <form action="" method="post" name="form1" id="form1">
        <input name="page" value="<?=$page?>" type="hidden">
        <input name="medit_id" value="-1" type="hidden">
        <input name="cedit_id" value="-1" type="hidden">
        <input name="ld_id" value="-1" type="hidden">
        <input name="del_sel" value="-1" type="hidden">
        <input name="act" value="" type="hidden">
        <input name="showGroupOp" value="<?=@$showGroupOp?>" type="hidden">
        <input name="linkDataset" value="-1" type="hidden">
        <input name="unlinkDataset" value="-1" type="hidden">
        <?

        $aqs=join('AND',$aq);

        if ($gr==2){

            $num=$cc->cat_view(array(
                'start'=>$page*$lines,
                'lines'=>$lines,
                'gr'=>$gr,
                'model_id'=>@$modelId,
                'brand_id'=>@$brand_id_2,
                'P1'=>@$P1_2,
                'P2'=>@$P2_2,
                'P3'=>@$P3_2,
                'P4'=>@$P4_2,
                'P5'=>@$P5_2,
                'P6'=>@$P6_2,
                'M1'=>@$MP1_2,
                'add_query'=>$aqs,
                'select'=>'cc_cat.is_seo, cc_cat.ignoreUpdate, cc_cat.is_balances, cc_cat.ti_id AS cti, cc_model.ti_id AS mti, cc_brand.ti_id AS bti, cc_cat.bprice, cc_cat.cur_id, cc_cat.fixPrice, cc_cat.fixSc, cc_brand.extra_b, cc_cat.H AS CH, cc_model.text'.(Cfg::$config['CAT_IMPORT_MODE']==2 && isset(App_TFields::$fields['cc_cat']['etalon'])?', cc_cat.etalon':''),
                'H'=>0,
                'sup_id'=>@$sup_id_2,
                'datasetTo'=>@$inDSonly?'cat':'',
                'dataset_id'=>$dataset_id
            ));


        }elseif ($gr==1){

            if (!isset($P6_1)) $P6_1='';
            if (!isset($P4_1)) $P4_1='';
            if (!isset($MP1_1)) $MP1_1='';
            if (!isset($MP3_1)) $MP3_1='';
            $num=$cc->cat_view($r=array(
                'start'=>$page*$lines,
                'lines'=>$lines,
                'gr'=>$gr,
                'model_id'=>@$modelId,
                'brand_id'=>@$brand_id_1,
                'P1'=>@$P1_1,
                'P2'=>@$P2_1,
                'P3'=>@$P3_1,
                'P4'=>@$P4_1,
                'M1'=>@$MP1_1,
                'M2'=>@$MP2_1,
                'M3'=>@$MP3_1,
                'P6'=>@$P6_1,
                'P7'=>@$P7_1,
                'add_query'=>$aqs,
                'select'=>'cc_cat.is_seo, cc_cat.ignoreUpdate, cc_cat.is_balances, cc_cat.ti_id AS cti, cc_model.ti_id AS mti, cc_brand.ti_id AS bti, cc_cat.bprice, cc_cat.cur_id, cc_cat.fixPrice, cc_cat.fixSc, cc_brand.extra_b, cc_cat.H AS CH, cc_model.text'.(Cfg::$config['CAT_IMPORT_MODE']==2 && isset(App_TFields::$fields['cc_cat']['etalon'])?', cc_cat.etalon':''),
                'H'=>0,
                'sup_id'=>@$sup_id_1,
                'datasetTo'=>@$inDSonly?'cat':'',
                'dataset_id'=>$dataset_id
            ));

        }

        if(!$num) {
            note('Найдено <b>0</b>');
            cp_end();
            exit();
        }
        ?><div style="margin:10px 0; display:block">Найдено <strong><?=$num?></strong> типразмеров.</div><?

        view_pages($num,$page,$lines);

        if(empty($_REQUEST['modelId'])){
            ?>
            <p>
                <span id="groupOpToggle"<?=@$showGroupOp?' style="display:none"':''?>><a href="#">Развернуть панель групповых операций</a></span>
                <? if(count($aft)){?><span id="groupAfToggle"><a href="#">Раскрыть панель настройки отображения полей</a></span><? }?>
            </p>

            <? if(!empty($ds->selectedDataset)){?>
                <div class="row">
                    <input type="submit" id="dsAdd" value="Добавить в набор &quot;<?=$ds->selectedDataset['name']." (".$ds->classes[$ds->selectedDataset['class']]['name'].")"?>&quot;">
                    <input type="submit" id="dsRemove" value="Удалить из набора">
                </div>
            <? }?>

            <? if(count($aft)){?>
                <fieldset class="af ui"><legend>Отображение полей</legend>
                    <? foreach($aft as $k=>$v){?><span><input type="checkbox"  value="<?=$k?>" <?=@$afcoo[$k]?'checked':''?>> <?=$v['caption']?></span><? }?>
                    <span><input type="submit" value="Обновить таблицу"></span>
                </fieldset>
            <? }?>
            <fieldset class="ui" style="margin:10px 0; padding:0 15px; <?=!@$showGroupOp?'display:none':''?>" id="groupOp"><legend class="ui">Групповые операции <sup>*</sup></legend>
                <div style="margin:10px 0; display:block; overflow:hidden">
                    <div style="overflow:hidden; display:block; display:block; width:100%; margin:5px 0">
                        Базовая цена (для выделенных) =
                        <input type="text" name="multi_bprice"  style="width:50px;">
                        <select name="cur_id"  style="width:70px;">
                            <? foreach ($cc->cur_name as $k=>$v)if($k){?>
                                <option value="<?=$k?>"><?=$v[0]?></option>
                            <? }?>
                        </select>
                        <input type="submit" value="Записать" id="mbp">
                    </div>
                    <div style="overflow:hidden; display:block; width:100%; margin:5px 0">
                        Заменить складской остаток <select name="new_sc_mode"><option value="select">у отмеченных</option><option value="all">во всей базе <?=$gr==1?'шин':'дисков'?></option></select> на
                        <input type="text" name="new_sc"  style="width:120px;">
                        <input type="submit" value="Записать" id="changeSC">
                    </div>
                    <fieldset class="ui">
                        <? if(Cfg::get('CAT_IMPORT_MODE')==2 && isset(App_TFields::$fields['cc_cat']['etalon'])){?><input type="button" value="Установить/снять статус &quotэталон&quot" id="etalonSW"><? }?>
                        <input type="button" value="Удалить отмеченное" id="del_sel">
                        <input type="button" value="Скрыть отмеченное" id="hide_sel">
                        <input type="button" value="Отобразить отмеченное" id="show_sel">
                        <? if(in_array(Cfg::get('CAT_IMPORT_MODE'),array(1,3))){?><input type="submit" value="Обнулить привязки TireIndex во всей базе <?=$gr==1?'шин':'дисков'?>" id="tiErase">
                            <input type="submit" value="Обнулить привязки TireIndex для отмеченных размеров" id="tiEraseSel"><? }?>
                        <hr>
                        <input type="button" value="Добавить отмеченное в остатки" id="add_to_balances_sel">
                        <input type="button" value="Убрать отмеченное из остатков" id="remove_from_balances_sel">
                    </fieldset>
                </div>
                <div style="margin:10px 0; display:block">
                    Замена по всей базе <?=$gr==1?'шин':'дисков'?> <sup>**</sup>
                    <select name="replaceField">
                        <? if($gr==2){?>
                            <option value="csuffix">Суффикс (цвет)</option>
                            <?/* if(isset(App_TFields::$fields['cc_cat']['app'])){*/?><!--<option value="app"<?/*=@$replaceField=='app'?' selected':''*/?>>Применяемость</option>--><?/* }*/?>
                            <option value="mname"<?=@$replaceField=='mname'?' selected':''?>>Название диска</option>
                        <? } else{?>
                            <option value="csuffix">Суффикс</option>
                            <option value="mname"<?=@$replaceField=='mname'?' selected':''?>>Название шины</option>
                        <? }?>
                    </select>
                    Исходный фрагмент:
                    <input type="text" name="replaceSource" style="width:100px">
                    Строка замены:
                    <input type="text" name="replaceStr" style="width:100px">
                    <input type="submit" value="Заменить" id="replaceBut">
                </div>
                <div style="display:block; overflow:hidden; line-height:30px;">*) Цветом <div style="width:50px; height:15px; background:#123456; display:inline-block"></div> отмечены колонки, доступные для быстрого редактирования</div>
                <p>**) При внесении изменений  в  модель/типоразмер псевдоним (транслителированное название модели/типоразмера в урле) также изменится. Редактируйте параметры &quot;поштучно&quot; если псевдоним необходимо оставить без изменения.</p>
                <div style="margin:10px 0; width:100%; overflow:hidden">
                    <input style="display:none; float:left" type="submit" value="Сохранить изменения" id="upd">
                </div>
                <p style="display:block; width:100%"><a href="#" id="hideGroupOp">Скрыть панель групповых операций</a></p>
            </fieldset>
        <? }?>


        <table width="100%" class="ui-table ltable">
            <tr><th width="1%"><input title="инвертировать отметки" type="checkbox" class="tooltip invertChks" data-form="form1"></th>
                <? if(@$showTI){?><th>TI код бренда</th><th>TI код модели</th><th id="tiTipo_">TI код типоразмера</th><? }?>
                <? if ($gr==1){?>
                    <th width="1%">и</th><th>Артикул на сайте</th><th>описание</th><th>Бренд</th><th>Модель</th><th id="csuffix_"><?=$gr==1?'Суффикс':'Цвет'?></th><th id="uP3_">Ширина</th><th id="uP2_">Высота</th><th id="uP1_">Радиус</th><th id="uP7_">Ин/Ис</th><th>Сезон</th><th>SEO</th>
                <? }else{?>
                    <th width="1%">и</th><th>Артикул на сайте</th><th>описание</th><th>Бренд</th><th>Модель</th><th id="csuffix_">Цвет (суффикс)</th><th>Тип</th><th id="uP2_">J</th><th id="uP5_">R (радиус)</th><th id="uP1_">ET (вылет)</th><th id="uP4_">PCD (дырки)</th><th id="uP6_">ДЦО</th><th id="uP3_">DIA</th><th id="is_seo_">SEO</th>
                <? }?>
                <? if(count($aft)) foreach($aft as $k=>$v) if(@$afcoo[$k]) {?> <th id="<?=$k?>_"><?=$v['caption']?></th><? }?>
                <th id="ubprice_">Базовая цена</th><th id="ucprice_" class="tooltip" title="Цена с учетом всех наценок. Цена, указанная на сайте отображается при наведении курсора">Розница, руб</th><th class="tooltip" title="не обновлять цену при импорте из внешних источников">фикс цены</th><th class="tooltip" title="не обновлять склад при импорте из внешних источников">фикс кол-ва</th>
                <th id="scprice_">Спец цена</th>
                <th>Скрыть</th><th id="sc_">На складе</th><th>Поставщик</th><th>Остатки</th><th>Удалить</th></tr>
            <?

            $l=1;
            $inis_v='';

            $dsl=array();
            if($dataset_id){
                $ds->query("SELECT cat_id FROM cc_dataset_cat WHERE dataset_id='$dataset_id'");
                if($ds->qnum()) while($ds->next()!==false) $dsl[]=$ds->qrow['cat_id'];
            }
            $DB = new DB();
            while($cc->next()!=FALSE){

                $inis_v=CC_inis::check($cc->qrow['P7']);
                echo "<tr ".($cc->qrow['fixPrice'] ? 'class="fprice"' : '')." cat_id=\"{$cc->qrow['cat_id']}\" model_id=\"{$cc->qrow['model_id']}\" brand_id=\"{$cc->qrow['brand_id']}\" ".(in_array($cc->qrow['cat_id'],$dsl)?' class="inds"':'').">";
                echo '<td><input class="chks" type="checkbox" name="c_'.$cc->qrow['cat_id'].'" value="1"></td>';
                if(@$showTI){
                    ?><td align="center"><?=$cc->qrow['bti']?></td>
                    <td align="center"><?=$cc->qrow['mti']?></td>
                    <td align="center" title="cat_id=<?=$cc->qrow['cat_id']?>"><?=$cc->qrow['cti']?></td><?
                }
                echo '<td>';
                if($cc->qrow['img1']!='' || $cc->qrow['img2']!='' || $cc->qrow['img3']!='') echo '<a class="iPreview" title="'.Tools::unesc($cc->qrow['bname'].' '.$cc->qrow['mname']).'" href="'.$cc->make_img_path(1).'"><img src="../img/img.gif"></a>';echo'</td>';
                echo '<td align="center"><a href="/search.html?q='.$cc->qrow['cat_id'].'" target="_blank">'.$cc->qrow['cat_id'].'</a></td>';
                echo '<td align="center">';
                if(Cfg::$config['CAT_IMPORT_MODE']==2 && isset(App_TFields::$fields['cc_cat']['etalon'])) echo $cc->qrow['etalon']?'[Э] ':'';
                if(trim(Tools::stripTags($cc->qrow['text']!=''))) echo '<img src="../img/mods.gif" border="0">-';
                if(isset(App_TFields::$fields['cc_cat']['ft']) && trim(Tools::stripTags(@$cc->qrow['ft']))!='') echo'<img src="../img/mods.gif">';
                echo '</td>';
                echo '<td align="left">'.Tools::html($cc->qrow['bname']).'</td>';
                echo '<td align="left"><a class="medit" href="#">'.Tools::unesc($cc->qrow['mname']).'</a> '.Tools::unesc($cc->qrow['msuffix']).'</td>';
                if ($gr==1){
                    echo "<td align=\"center\" nowrap>".Tools::html($cc->qrow['csuffix'])."</td>";
                    echo "<td align=\"center\" nowrap><a href=\"#\" class=\"cedit\">{$cc->qrow['P3']}</a></td>";
                    echo "<td  nowrap align=\"center\"><a href=\"#\" class=\"cedit\">{$cc->qrow['P2']}</a></td>";
                    echo "<td nowrap><a href=\"#\" class=\"cedit\">".($cc->qrow['P6']==1?'ZR':'R')."{$cc->qrow['P1']}".($cc->qrow['P4']==1?'C':'')."</a></td>";
                    //		echo "<td nowrap  align=\"center\">".($inis_v?"<a href=\"javascript:;\" onClick=\"return (openwin('inis_explain.php?inis={$cc->qrow['P7']}'))\">":'').Tools::html($cc->qrow['P7']).($inis_v?"</a>":'')."</td>";
                    echo "<td nowrap><a href=\"#\" class=\"cedit\">{$cc->qrow['P7']}</a></td>";
                    echo "<td align=\"center\" nowrap><a href=\"#\" class=\"medit\">";
                    if($cc->qrow['MP1']=='1') echo 'Лето';
                    elseif($cc->qrow['MP1']=='2') echo 'Зима';
                    elseif($cc->qrow['MP1']=='3') echo 'Всесезон';
                    else echo'???';
                    echo ($cc->qrow['MP3']=='1'?' (ш)':'');
                    echo '</a></td>';
                    echo "<td align=\"center\" nowrap>{$cc->qrow['is_seo']}</td>";
                }else{
                    echo "<td align=\"center\">".Tools::html($cc->qrow['csuffix'])."</td>";
                    echo "<td align=\"center\"><a href=\"#\" class=\"cedit\">";
                    if ($cc->qrow['MP1']=='1') echo 'Кованый';
                    elseif($cc->qrow['MP1']=='2') echo 'Литой';
                    elseif($cc->qrow['MP1']=='3') echo 'Штампованый';
                    else echo '???';
                    echo"</a></td>";
                    echo "<td align=\"center\" nowrap><a href=\"#\" class=\"cedit\">{$cc->qrow['P2']}</a></td>";
                    echo "<td align=\"center\" nowrap><a href=\"#\" class=\"cedit\">{$cc->qrow['P5']}</a></td>";
                    echo "<td align=\"center\" nowrap><a href=\"#\" class=\"cedit\">{$cc->qrow['P1']}</a></td>";
                    echo "<td align=\"center\" nowrap><a href=\"#\" class=\"cedit\">{$cc->qrow['P4']}</a></td>";
                    echo "<td align=\"center\" nowrap><a href=\"#\" class=\"cedit\">{$cc->qrow['P6']}</a></td>";
                    echo "<td align=\"center\" nowrap><a href=\"#\" class=\"cedit\">{$cc->qrow['P3']}</a></td>";
                    echo "<td align=\"center\" nowrap>{$cc->qrow['is_seo']}</td>";
                }
                if(count($aft)) foreach($aft as $k=>$v) if(@$afcoo[$k]) echo '<td align="center">'.Tools::html($cc->qrow[$v['as']]).'</td>';
                //
                $ext_prices = $DB->getOne("SELECT cc_cat_sc.cat_id, cc_cat_sc.sc, MIN(cc_cat_sc.price1) as price1,cc_cat_sc.price2,cc_cat_sc.price3,cc_suplr.name,cc_cat_sc.dt_added,cc_cat_sc.dt_upd FROM cc_cat_sc INNER JOIN cc_suplr ON cc_cat_sc.suplr_id=cc_suplr.suplr_id WHERE cat_id='{$cc->qrow['cat_id']}' AND cc_cat_sc.sc>0 AND cc_cat_sc.ignored=0 GROUP BY cc_cat_sc.cat_id ORDER BY cc_suplr.name",MYSQL_ASSOC);
                if (empty($ext_prices)){
                    $ext_prices = Array('price1' => $cc->qrow['bprice'], 'price2' => $cc->qrow['cprice']);
                }
                //
                echo '<td align="center" nowrap>'.($cc->qrow['bprice'] > 0 ? $cc->qrow['bprice'] : $ext_prices['price1']).'<span> '.$cc->cur_name[$cc->qrow['cur_id']][0].'</span></td>';
                if(!$cc->qrow['fixPrice']) {
                    if ($gr == 1)
                        $pb = $cc->extra_price($cc->qrow['bprice'], $cc->qrow['cur_id'], $cc->qrow['P1'], $cc->qrow['sup_id'], $cc->qrow['brand_id'], $cc->qrow['extra_b'], 1, $cc->qrow['MP1']);
                    else
                        $pb = $cc->extra_price($cc->qrow['bprice'], $cc->qrow['cur_id'], $cc->qrow['P5'], $cc->qrow['sup_id'], $cc->qrow['brand_id'], $cc->qrow['extra_b'], 2);
                    //$price_2 = $pb;
                }
                else {
                    //$price_2 = $cc->qrow['cprice'];
                    $pb=$cc->qrow['cprice'];
                }

                echo "<td align=\"center\" nowrap><a class=\"bprice".($pb!=$cc->qrow['cprice']?' red':'')."\" href=\"#\" cprice=\"{$cc->qrow['cprice']}\">{$cc->qrow['cprice']}</a></td>";

                ?><td align="center"><a href="#" class="fixPrice" cprice="<?=$cc->qrow['cprice']?>"><?=$cc->qrow['fixPrice']?' да ':' нет '?></a></td>
                <td align="center"><a href="#" class="fixSc"><?=$cc->qrow['fixSc']?' да ':' нет '?></a></td>
                <td align="center" nowrap><?=$cc->qrow['scprice']?><span> руб</span></td><?
                echo '<td align="center" nowrap><a href="#" class="chide">'.(($cc->qrow['CH']!='1')?'скрыть':'отобразить').'</a></td>';
                echo '<td align="center" nowrap>'.$cc->qrow['sc'].'</td>';
                echo '<td align="center">'.@$cc->sup_arr[$cc->qrow['sup_id']].'</td>';
                echo '<td align="center">'.(@$cc->qrow['is_balances'] ? 'Да' : 'Нет').'</td>';
                echo '<td align="center" nowrap><a href="#" class="cld"><img src="../img/b_drop.png" border="0"></a></td>';
                echo "</tr>";
                $l++;
            }
            ?>
        </table>
        <? view_pages($num,$page,$lines);?>
    </form>
<? }else note('Поиск по базе типоразмеров. Ожидаю ввод параметров...');
cp_end();
