<?
@define (true_enter,1);

define ('ROOT_PATH', realpath(dirname(__FILE__).'/..'));
require_once (ROOT_PATH.'/config/init.php');

ob_start();

echo 'Running Google Latitude Badge parser.....';
$go=new GO_Track();

$r=$go->retrieveAllLB();

if($r) echo 'Success!'; else echo 'ERROR LOG updated. Check it out!';

echo "\n";

echo 'Current ErrorLog: ';
echo print_r($go->checkLogs(),true);

echo "\n".'END CRON TASK.';

$buf=ob_get_clean();

if(!empty($_SERVER['REMOTE_ADDR'])) echo nl2br($buf); else echo $buf;
