<?
@define (true_enter,1);

define ('ROOT_PATH', realpath(dirname(__FILE__).'/..'));
require_once (ROOT_PATH.'/config/init.php');

ob_start();

echo "Running 5 min cron task.....\n";

echo "Orders.phoenix() : ";
$os=new App_Orders();

$o=$os->phoenix();
if(is_array($o)) {
    foreach($o as $k=>$v){
        echo "[$k]: ";
        echo implode(', ',$v);
        echo 'ok. ';
    }

} else echo "disabled";

/*
echo "\nMangoOffice : ";
try {

    $g = new Orders_Mango();

    echo "\nMangoOffice callreactor running...";
    if(($b=$g->getCallHistory())!==false){
        var_dump($b);
        echo 'callreactor ok.';

    }else{
        echo 'callreactor fault.';
    }

    echo "\nMangoOffice balance...";
    if(($b=$g->getInfo())!==false){
        echo 'ok.';
    }else{
        echo 'fault.';
    }

}catch (Exception $e){
    echo $e->getMessage();
    echo 'fault!';
}


*/

echo "\nEND CRON TASK.";

$buf=ob_get_clean();

if(!empty($_SERVER['REMOTE_ADDR'])) echo nl2br($buf); else echo $buf;
