<?
@define (true_enter,1);

define ('ROOT_PATH', realpath(dirname(__FILE__).'/..'));
require_once (ROOT_PATH.'/config/init.php');

ini_set('log_errors', 'On');
ini_set('error_log', ROOT_PATH.'/assets/logs/ciidm.log');

echo 'server_loc='.server_loc."\n";


$ci=new App_Import_CIIdm();

/*
 * состояние фоновой задачи
 * MC::ciidm.daemon{
 *      state: STRING {waiting|exectask|stopped} - {ждет задачу в MC::cii.task | выполняет парсинг  | выброшен из памяти}
 *      paused: bool  - демон в цикле ожидания
 *      pid: INT - pid процесса
 *      ts_started - время запуска процесса time()
 *      ts - время последнего обновления записи
 *      mem
 *      mem_peak
 * }
 *
 * команды демон принимает из простого массива
 * MC::ciidm.cmd - array()
 *      'pause' - поставить на паузу, если парсинг выполняется, то будет пауза в парсинге
 *      'stop' - выполнить exit(101)
 *
 * Таск
 * MC::ciidm.task - array{
 *      state - string - {возможные значения: new|exec|error|interrupted|finished}
 *      ts_added, - время добавления таска
 *      cUserId - админ создавший таск
 *      file_id,
 *      opt = ci->getConfig + даныне формы параметров,
 *      ts_run - время начала выполения
 *      ts_finished - время завершения,
 *      ts - время последнего обновления записи
 *      pid - процесс в котором выполняется таск
 *      pg_label  - строка для progressbar
 *      pg_index -  позиция progressbar
 * }
 * признак ошибки выполнения задачи: task.pid != daemon.pid или task.state == error
 *
 *
 * MC::ciidm.taskLog - лог задачи - очитщается при запуске ci->recognize()
 * MC::ciidm.log - лог демона
 */

//$ci->dm_clearLog();
//$ci->dm_clearTaskLog();

$daemon=$ci->dm_updateInfo([], true);
$ci->dm_log("Демон запущен. PID={$daemon['pid']}");

// проеряем на наличие аварийно завершенной задачи из предыдущего процесса
$task=$ci->dm_modTask();
if(@$task['state']=='exec'){
    $ci->dm_taskError("[init]: Фоновый процесс перезапущен в режиме ожидания (pid={$daemon['pid']}. Обнаружена варийно завершенная задача от предыдущего процесса. Задача переведена в состояние ошибки.");
}

/*
 * запускаем цикл опроса
 */

do{

    $ci->dm_cmd();
    $ci->dm_updateInfo();

    // проверяем на новую задачу
    $task=MC::sget('ciidm.task');
    if(@$task['state']=='new') {
        $ci->dm_updateInfo();
        $ci->dm_setState('exectask');
        $ci->dm_clearTaskLog();
        $ci->recognize($task);
        //$ci->test();
        $ci->dm_setState('waiting');
        $ci->dm_updateInfo();
    }

    sleep(1);

} while (true);
