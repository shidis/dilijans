<?php

class Log_Sys
{
    private static $db=null;


    private static function init()
    {
        if(empty(Log_Sys::$db)){
            Log_Sys::$db=new DB();
        }
    }

    /*
     * Вывести в лог
     *
     * @param string $etype тип сообщения
     * @param string $event   событие, например нзвание функции
     * @param string $msg   сообщение
     * @param string $data   доп данные
     */
    public static function put($etype, $event, $msg='', $data='')
    {
        Log_Sys::init();

        Log_Sys::$db->insert('log_sys',array(
            'dt'=>Tools::dt(),
            'event'=>Tools::esc($event),
            'msg'=>Tools::esc($msg),
            'data'=>Tools::esc($data),
            'etype'=>intval($etype),
            'cUserId'=>CU::$userId
        ));

        if($etype==SLOG_ESTOP && defined('CONSOLE')) {
            Bot_Common::stopUnit();
        }
        if($etype==SLOG_ETERM){
            exit (100);
        }
        if($etype==SLOG_ESTOP){
            exit (100);
        }
        return 0;
    }


    static function clear()
    {
        $days=abs((int)Data::get('log_tbl_lifeTime'));
        if(empty($days)) $days=180;

        $db=new DB();
        $db->query("DELETE FROM log_sys WHERE dt < DATE_SUB(CURRENT_DATE, INTERVAL $days DAY)");

        $db->query("OPTIMIZE TABLE log_sys");

        unset($db);
    }


}
