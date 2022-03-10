<?
if (!defined('true_enter')) die ("Direct access not allowed!");

final class Log_Tables
{

    /*
     * Создание триггеров
     * Запускается в режиме инсталятора. Перезапуск только в случае изменения структуры таблиц
     * @param   $tbl    string
     * @param   $columns    array(список наблюжаемых колонок)
     * @param   keyName     int
     * @param   keyValue
     * http://habrahabr.ru/post/153445/
     */
    static function createWatchTrigger($tbl, $columns, $keyName)
    {
        $db=new DB();

        $types=array('update','insert','delete');

        foreach($types as $type)
        {
            $db->query("DROP TRIGGER IF EXISTS {$tbl}_t_$type");

            $triggerContent=
                "CREATE TRIGGER {$tbl}_t_$type AFTER $type ON {$tbl}
                FOR EACH ROW
                BEGIN
                    CREATE TEMPORARY TABLE IF NOT EXISTS temp_watch_changes (
                        id_change int(10) UNSIGNED NOT NULL
                    );

                    SET @oldV='';
                    SET @newV='';
                    SET @write='';

                \n";

            foreach($columns as $columnName)
            {
                if($type=='update')
                {
                    $triggerContent.=
                        "IF NEW.{$columnName} != OLD.$columnName\n THEN \n".
                            "SET @oldV=CONCAT(@oldV, '[^[{$columnName}:]]\n', OLD.$columnName, '\n[$[:{$columnName}]]\n\n');\n".
                            "SET @newV=CONCAT(@newV, '[^[{$columnName}:]]\n', NEW.$columnName, '\n[$[:{$columnName}]]\n\n');\n".
                            "SET @write='1';\n".
                        "END IF;\n";
                }

                elseif($type=='insert')
                {
                    $triggerContent.=
                        "SET @newV=CONCAT(@newV, '[^[{$columnName}:]]\n', NEW.$columnName, '\n[$[:{$columnName}]]\n\n');\n";
                }
            }

            if($type=='update')
            {
                $triggerContent.=
                    "IF @write != '' THEN\n".
                        "INSERT INTO log_tbl_changes (tbl, keyName, keyValue, op, dt, oldValue, newValue) VALUES ( '$tbl', '$keyName', NEW.$keyName, 'update', NOW(), @oldV, @newV);\n".
                        "SET @last_id=last_insert_id();\n".
                        "INSERT INTO temp_watch_changes (id_change) values (@last_id);\n".
                    "END IF;\n";
            }
            elseif($type=='insert')
            {
                $triggerContent.=
                    "INSERT INTO log_tbl_changes (tbl, keyName, keyValue, op, dt, newValue) VALUES ( '$tbl', '$keyName', NEW.$keyName, 'insert', NOW(), @newV);\n".
                    "SET @last_id=last_insert_id();\n".
                    "INSERT INTO temp_watch_changes (id_change) values (@last_id);\n";
            }
            else
            {
                $triggerContent.=
                    "INSERT INTO log_tbl_changes (tbl, keyName, keyValue, op, dt) VALUES ( '$tbl', '$keyName', OLD.$keyName, 'delete', NOW());\n".
                    "SET @last_id=last_insert_id();\n".
                    "INSERT INTO temp_watch_changes (id_change) values (@last_id);\n";
            }


            $triggerContent.=
                "END;\n\n";

            $res=$db->query($triggerContent);
        }

        unset($db);
        return $res;
    }

    /*
     * Заполнение полей cUserid, ip в лог-таблице
     */
    static function flushChanges()
    {
        $db=new DB();
        $r=$db->fetchAll("SELECT * FROM temp_watch_changes", MYSQL_ASSOC, true);
        if($r!==false)
            foreach($r as $v){
                if(!empty(CU::$userId)) $userId=CU::$userId; else $userId=0;
                $ip=@$_SERVER['REMOTE_ADDR'];
                if(!empty($_SERVER['HTTP_HOST']) && !empty($_SERVER['REQUEST_URI'])){
                    $uri=Tools::esc(Tools::utf($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']));
                    if(!empty($uri)) $uri=(Request::isHTTPS()?'https://':'http://').$uri;
                }else $uri='';
                $db->query("UPDATE log_tbl_changes SET cUserId='$userId', ip=INET_ATON('$ip'), uri='$uri' WHERE id='{$v['id_change']}'");
            }
        unset($db);
    }

    /*
     * Вызов в конце работы скрипта
     */
    static function initShutdown()
    {
        register_shutdown_function(array('Log_Tables','flushChanges'));
    }

    static function clear()
    {
        $days=abs((int)Data::get('log_tbl_lifeTime'));
        if(empty($days)) $days=180;

        $db=new DB();
        $db->query("DELETE FROM log_tbl_changes WHERE dt < DATE_SUB(CURRENT_DATE, INTERVAL $days DAY)");

        $db->query("OPTIMIZE TABLE log_tbl_changes");

        unset($db);
    }

}