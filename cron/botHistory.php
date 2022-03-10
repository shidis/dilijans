<?
@define (true_enter,1);

define ('ROOT_PATH', realpath(dirname(__FILE__).'/..'));
require_once (ROOT_PATH.'/config/init.php');

ob_start();

echo "Run bot history collect cron task.....";

BotLog::makeHistory();

echo 'ok';
echo "\n";

echo "Clearing logs...";

Log_Tables::clear();
Log_Sys::clear();

echo 'ok';
echo "\n";

echo 'END CRON TASK.';

$buf=ob_get_clean();

if(!empty($_SERVER['REMOTE_ADDR'])) echo nl2br($buf); else echo $buf;
