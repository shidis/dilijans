<?
@define (true_enter,1);

// ЭТО НАДО ВРУЧНУЮ ЗАДАВАТЬ ДЛЯ CLI!

/*
 * проверяет наличие файлов для всех моделей шин и дисков,
 * проверка забытых файлов фоток
 * проверяет наличие всех трех фоток. Если чего то не хвататет то дополняет по схеме IMG2->IMG1   IMG2->IMG3 IMG1=>IMG3  IMG1->IMG2
 * для всех LD=1 удаляет фотки
 */
define('server_loc','local');

define ('ROOT_PATH', realpath(dirname(__FILE__).'/../..'));
require_once (ROOT_PATH.'/config/init.php');

ini_set('error_reporting', E_ALL);
ini_set('max_execution_time', 30*60);

if(!CONSOLE) die('console mode only.');

if(mb_stripos(php_uname('s'),'win')!==false) define('OS','windows'); else define('OS','linux');
echo "OS: ".OS,"\n\n";

$cc=new CC_Ctrl();
$upl=new Uploader();

/*
 * удаление фоток моделей
 */
$qr=$cc->fetchAll("select cc_model.img1, cc_model.img2, cc_model.img3, cc_model.model_id FROM cc_model join cc_brand using(brand_id) WHERE (cc_model.LD || cc_brand.LD) AND (cc_model.img1='' OR cc_model.img2!='' OR cc_model.img3!='')", MYSQL_ASSOC);

$i=0;
$n=$cc->qnum();

echo "\Total $n models with LD=1. Deleting LD fotos...\n";

foreach($qr as $r){
    $i++;
    if(!empty($r['img1'])) {
        echo "{$r['img1']} deleting...";
        if(!$cc->imgDelete('cc_model','model_id', $r['model_id'], 'img1')) {
            echo $cc->strMsg(' | ');
        }else{
            echo 'ok';
        }
        echo "\n";
    }
    if(!empty($r['img2'])) {
        echo "{$r['img2']} deleting...";
        if(!$cc->imgDelete('cc_model','model_id', $r['model_id'], 'img2')) {
            echo $cc->strMsg(' | ');
        }else{
            echo 'ok';
        }
        echo "\n";
    }
    if(!empty($r['img3'])) {
        echo "{$r['img3']} deleting...";
        if(!$cc->imgDelete('cc_model','model_id', $r['model_id'], 'img3')) {
            echo $cc->strMsg(' | ');
        }else{
            echo 'ok';
        }
        echo "\n";
    }
}

/*
 * удаление фоток брендов
 */
$qr=$cc->fetchAll("select cc_brand.img1, cc_brand.img2, cc_brand.brand_id FROM cc_brand WHERE cc_brand.LD AND (cc_brand.img1='' OR cc_brand.img2!='')", MYSQL_ASSOC);

$i=0;
$n=$cc->qnum();

echo "\Total $n brands LD=1. Deleting LD fotos...\n";

foreach($qr as $r){
    $i++;
    if(!empty($r['img1'])) {
        echo "{$r['img1']} deleting...";
        if(!$cc->imgDelete('cc_brand','brand_id', $r['brand_id'], 'img1')) {
            echo $cc->strMsg(' | ');
        }else{
            echo 'ok';
        }
        echo "\n";
    }
    if(!empty($r['img2'])) {
        echo "{$r['img2']} deleting...";
        if(!$cc->imgDelete('cc_brand','brand_id', $r['brand_id'], 'img2')) {
            echo $cc->strMsg(' | ');
        }else{
            echo 'ok';
        }
        echo "\n";
    }
}

/*
 * Проверки целостности файлов картинок
 */

$bu=Cfg::_get('root_path') . '/' . Cfg::get('cc_upload_dir') . '/';

$qr=$cc->fetchAll("select cc_model.img1, cc_model.img2, cc_model.img3, cc_model.model_id, cc_model.gr from cc_model join cc_brand using(brand_id) WHERE NOT cc_model.LD AND NOT cc_brand.LD", MYSQL_ASSOC);

$i=0;
$n=$cc->qnum();


echo "\Total $n models. Test for physical fotos existing...\n";

foreach($qr as $r){

    $i++;

    if(!empty($r['img2'])){
        if(!is_file($bu.$r['img2'])){
            echo "{$r['model_id']} => IMG2  {$r['img2']} file not exist!\n";
        }
    }elseif(!empty($r['img1'])){
        echo "IMG_2 not exist, but IMG_1 is. Copying in IMG_2...";
        $s2=Cfg::$config['root_path'].'/'.Cfg::get('cc_upload_dir').'/'.$r['img1'];
        if($upl->spyUrl($s2,Uploader::$EXT_GRAPHICS)){
            //copy/resize  imgUpload($table, $id, $gr, $imgNum, $sfile)
            if(!$cc->imgUpload('cc_model', $r['model_id'], $r['gr'], 2, $upl->sfile)){
                echo "ccError $s2 => ".$upl->strMsg(' | ')."\n";
            }
            $upl->del();
            echo 'ok';
        }else{
            echo "Upl_Error $s2 => ".$upl->strMsg(' | ')."\n";
        }
        echo "\n";
    }

    if(!empty($r['img1'])){
        if(!is_file($bu.$r['img1'])){
            echo "{$r['model_id']} => IMG1  {$r['img1']} file not exist!\n";
        }
    }elseif(!empty($r['img2'])){
        echo "IMG_1 not exist, but IMG_2 is. Copying in IMG_1...";
        $s2=Cfg::$config['root_path'].'/'.Cfg::get('cc_upload_dir').'/'.$r['img2'];
        if($upl->spyUrl($s2,Uploader::$EXT_GRAPHICS)){
            //copy/resize  imgUpload($table, $id, $gr, $imgNum, $sfile)
            if(!$cc->imgUpload('cc_model', $r['model_id'], $r['gr'], 1, $upl->sfile)){
                echo "ccError $s2 => ".$upl->strMsg(' | ')."\n";
            }
            $upl->del();
            echo 'ok';
        }else{
            echo "Upl_Error $s2 => ".$upl->strMsg(' | ')."\n";
        }
        echo "\n";

    }
    if(!empty($r['img3'])){
        if(!is_file($bu.$r['img3'])){
            echo "{$r['model_id']} => IMG2  {$r['img3']} file not exist!\n";
        }
    }elseif(!empty($r['img2'])){
        echo "IMG_3 not exist, but IMG_2 is. Copying in IMG_3...";
        $s2=Cfg::$config['root_path'].'/'.Cfg::get('cc_upload_dir').'/'.$r['img2'];
        if($upl->spyUrl($s2,Uploader::$EXT_GRAPHICS)){
            //copy/resize  imgUpload($table, $id, $gr, $imgNum, $sfile)
            if(!$cc->imgUpload('cc_model', $r['model_id'], $r['gr'], 3, $upl->sfile)){
                echo "ccError $s2 => ".$upl->strMsg(' | ')."\n";
            }
            $upl->del();
            echo 'ok';
        }else{
            echo "Upl_Error $s2 => ".$upl->strMsg(' | ')."\n";
        }
        echo "\n";
    }elseif(!empty($r['img1'])){
        echo "IMG_3 not exist, but IMG_1 is. Copying in IMG_3...";
        $s2=Cfg::$config['root_path'].'/'.Cfg::get('cc_upload_dir').'/'.$r['img1'];
        if($upl->spyUrl($s2,Uploader::$EXT_GRAPHICS)){
            //copy/resize  imgUpload($table, $id, $gr, $imgNum, $sfile)
            if(!$cc->imgUpload('cc_model', $r['model_id'], $r['gr'], 3, $upl->sfile)){
                echo "ccError $s2 => ".$upl->strMsg(' | ')."\n";
            }
            $upl->del();
            echo 'ok';
        }else{
            echo "Upl_Error $s2 => ".$upl->strMsg(' | ')."\n";
        }
        echo "\n";
    }


    if($i % 500 === 0)  echo ceil($i*100/$n)."%\n";

}

//echo "\nJOB IS DONE.\n";
//exit;


$paths=array(
    Cfg::get('cc_tyres_subdir').'/'.Cfg::get('cc_model_subdir').'/1/'=>'11',
    Cfg::get('cc_tyres_subdir').'/'.Cfg::get('cc_model_subdir').'/2/'=>'12',
    Cfg::get('cc_tyres_subdir').'/'.Cfg::get('cc_model_subdir').'/3/'=>'13',
    Cfg::get('cc_wheels_subdir').'/'.Cfg::get('cc_model_subdir').'/1/'=>'21',
    Cfg::get('cc_wheels_subdir').'/'.Cfg::get('cc_model_subdir').'/2/'=>'22',
    Cfg::get('cc_wheels_subdir').'/'.Cfg::get('cc_model_subdir').'/3/'=>'23'
);

echo "\nTest for forgot files...";

$qr=$cc->fetchAll("select cc_model.img1, cc_model.img2, cc_model.img3, cc_model.model_id, cc_model.gr from cc_model join cc_brand using(brand_id)", MYSQL_ASSOC);

$dd=array();
$dd["11"]=array();
$dd["12"]=array();
$dd["13"]=array();
$dd["21"]=array();
$dd["22"]=array();
$dd["23"]=array();

foreach($qr as $r){
    $pi=pathinfo($r['img2']);
    $dd["{$r['gr']}2"][]=h($pi['basename']);

    $pi=pathinfo($r['img1']);
    $dd["{$r['gr']}1"][]=h($pi['basename']);

    $pi=pathinfo($r['img3']);
    $dd["{$r['gr']}3"][]=h($pi['basename']);
}

foreach($paths as $dir=>$gr){

    echo "\nScaning $dir ...\n";

    if (is_dir($bu.$dir)) {
        if ($dh = opendir($bu.$dir)) {
            while (($file = readdir($dh)) !== false)
                if($file!='.' && $file!='..') {
                    if(!in_array(h($file), $dd[$gr])){
                        echo "$dir$file  -> deleted";
                        unlink("$bu$dir$file");
                        echo "\n";
                    }else{
                        //echo ".";
                    }
                }
            closedir($dh);
        }else{
            echo "$dir CANT OPEN! ****************\n";
        }
    }else{
        echo "$dir IS NOT A DIR! ***************************\n";
    }

}
echo "\nJOB IS DONE.\n";


function h($s)
{
    if(OS=='linux') return md5($s); else return md5(Tools::tolow($s));
}