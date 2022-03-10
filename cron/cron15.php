<?
@define (true_enter,1);

define ('ROOT_PATH', realpath(dirname(__FILE__).'/..'));
require_once (ROOT_PATH.'/config/init.php');


ob_start();

echo "Running 15 min cron task.....\n";

echo "\nMangoOffice balance...";
try {

    $g = new Orders_Mango();

    if(($b=$g->getInfo())!==false){
        echo 'ok.';
    }else{
        echo 'fault.';
    }

}catch (Exception $e){
    echo $e->getMessage();
    echo 'fault!';
}

echo 'Check for expired sessions...';
if(CU::destroyExpired()) echo "ok\n"; else echo "has errors!\n";

$cc=new CC_Ctrl;

$cc->execCacheTasks();


echo "\nEND CRON TASK.";

$buf=ob_get_clean();

if(!empty($_SERVER['REMOTE_ADDR'])) echo nl2br($buf); else echo $buf;
