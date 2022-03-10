<?
@define (true_enter,1);

define ('ROOT_PATH', realpath(dirname(__FILE__).'/..'));
require_once (ROOT_PATH.'/config/init.php');


ob_start();

echo "Running night (1) cron task.....\n";

if(Cfg::_get('sessionProbability')===0){
    echo 'Check for expired PHP sessions...';
    if(Session::destroyExpired()) echo "ok\n"; else echo "has errors!\n";
}

echo "\nEND CRON TASK.";

$buf=ob_get_clean();

if(!empty($_SERVER['REMOTE_ADDR'])) echo nl2br($buf); else echo $buf;
