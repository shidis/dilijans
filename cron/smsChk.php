<?php
@define (true_enter,1);
define ('ROOT_PATH', realpath(dirname(__FILE__).'/..'));
require_once (ROOT_PATH.'/config/init.php');

try {

    //Log_Sys::put(SLOG_INFO, "SMSChk.php", "Запущен...");

    $updates=new Bot_SMS();

    // стандартный вызов бота с проверками и установками
    $updates->
        lifeTime(10*60)->  // CRON 10
        pingInterval(10)->
        runnableUnit()->
        alone()->
        lifeStart()->
        run();


} catch (DBException $e) {
    $e->getError();

} catch (AppException $e){
    $e->getError();
}
