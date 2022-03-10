<?
@define (true_enter,1);

define ('ROOT_PATH', realpath(dirname(__FILE__).'/..'));
require_once (ROOT_PATH.'/config/init.php');

ob_start();

if(!(int)Data::get('yandex_market_crowler')){

    echo "skip run. YM is Off.";

}else {

    echo "Yandex.Market parser running...";

    if (MC::sget('YandexMarketParser.run') && 0) {

        echo 'YandexMarketParser.run -> skipped';

    } else {
        MC::sset('YandexMarketParser.run', 1, 60 * 20);

        $mk = new CC_API_Market();

        //$mk->forceUpdate(1);

        if (!$mk->task()) {
            echo $s = 'CronTask[YM_Parser]: task return error code.';
            $mk->log($s);
        } else {
            print_r($mk->opt);
            echo 'ok';
        }


        MC::sset('YandexMarketParser.ts_run', time());
        MC::sdel('YandexMarketParser.run');
    }

}

echo "\nEND CRON TASK.\n";

$buf=ob_get_clean();

if(!empty($_SERVER['REMOTE_ADDR'])) echo nl2br($buf); else echo $buf;
