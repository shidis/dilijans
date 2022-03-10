<?
@define ('true_enter',1);
define ('ROOT_PATH', realpath(dirname(__FILE__).'/..'));

require_once (ROOT_PATH.'/config/init.php');

ob_start();

$icq=new ICQ;

$icq->icq_update_state(true);

echo "ok\nEND CRON TASK.";

$buf=ob_get_clean();

if(!empty($_SERVER['REMOTE_ADDR'])) echo nl2br($buf); else echo $buf;
