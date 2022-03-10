<?
@define (true_enter,1);

define ('ROOT_PATH', realpath(dirname(__FILE__).'/..'));
require_once (ROOT_PATH.'/config/init.php');

ob_start();


echo "MangoOffice callreactor running...";

if(MC::sget('MangoOfficeCollector.run')) {
    echo 'skipped';
}else {
    MC::sset('MangoOfficeCollector.run', 1);
    try {

        $g = new Orders_Mango();

        if (($b = $g->getCallHistory()) !== false) {
            var_dump($b);
            echo ' ok.';

        } else {
            echo ' fault.';
        }

    } catch (Exception $e) {
        echo $e->getMessage();
        echo 'fault!';
    }
    MC::sdel('MangoOfficeCollector.run');
}



echo "\nEND CRON TASK.";

$buf=ob_get_clean();

if(!empty($_SERVER['REMOTE_ADDR'])) echo nl2br($buf); else echo $buf;
