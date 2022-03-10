<?
@define (true_enter,1);

// ЭТО НАДО ВРУЧНУЮ ЗАДАВАТЬ ДЛЯ CLI!
define('server_loc','local');

define ('ROOT_PATH', realpath(dirname(__FILE__).'/../..'));
require_once (ROOT_PATH.'/config/init.php');

if(!CONSOLE) die('console mode only.');

ini_set('error_reporting', E_ALL);
ini_set('max_execution_time', 30*60);

$cc=new CC_Ctrl();
$upl=new Uploader();

define('IMG',1); // какую фотку будем переписывать из img2   допустимы {1,3}
define('GR',2);     // группа товаров
define('TEST',0); // тест, без реальной записи

$qr=$cc->fetchAll("select cc_model.img1, cc_model.img2, cc_model.img3, cc_model.model_id from cc_model join cc_brand using(brand_id) WHERE NOT cc_model.LD AND NOT cc_brand.LD AND cc_model.gr=".GR, MYSQL_ASSOC);

$i=0;
$n=$cc->qnum();

echo "\nВсего $n моделей. Поехали...\n";

foreach($qr as $r)
{
    $i++;
    if(!empty($r['img'.IMG]) && empty($r['img2'])){
        echo "{$r['img'.IMG]} есть, а img2 нет!";
        if(IMG!=1 && !empty($r['img1'])) echo " НО есть img1";
        echo "\n";
    }

	if(!empty($r['img2'])){
        $s2=Cfg::$config['root_path'].'/'.Cfg::get('cc_upload_dir').'/'.$r['img2'];
        if(is_file($s2)){
            if(!TEST)
                if($upl->spyUrl($s2,Uploader::$EXT_GRAPHICS)){
                    //copy/resize  imgUpload($table, $id, $gr, $imgNum, $sfile)
                    if(!$cc->imgUpload('cc_model', $r['model_id'], GR, IMG, $upl->sfile)){
                        echo "ccError $s2 => ".$upl->strMsg(' | ')."\n";
                    }
                    $upl->del();
                }else{
                    echo "Upl_Error $s2 => ".$upl->strMsg(' | ')."\n";
                }
        }else{
            echo "$s2 => notExists\n";
        }
    }elseif(IMG!=1 && !empty($r['img1'])){
        $s2=Cfg::$config['root_path'].'/'.Cfg::get('cc_upload_dir').'/'.$r['img1'];
        echo "Rаботаем с IMG1 ($s2) ...";
        if(is_file($s2)){
            if(!TEST)
                if($upl->spyUrl($s2,Uploader::$EXT_GRAPHICS)){
                    //copy/resize  imgUpload($table, $id, $gr, $imgNum, $sfile)
                    if(!$cc->imgUpload('cc_model', $r['model_id'], GR, IMG, $upl->sfile)){
                        echo "ccError $s2 => ".$upl->strMsg(' | ')."\n";
                    }
                    $upl->del();
                }else{
                    echo "Error $s2 => ".$upl->strMsg(' | ')."\n";
                }
        }else{
            echo "$s2 => notExists\n";
        }
        echo "ok\n";
    }

    if($i % 50 === 0)  echo ceil($i*100/$n)."%\n";
	
}
echo "\nJOB IS DONE.\n";

