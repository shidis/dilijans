<?
abstract class Bot_Common extends DB
{
    public  $id=null; // ид бота
    public  $pingInterval=null; // примерное время между пингами в секундах с долями
    public  $lifeTime=null; // время жизни скрипта в секундах.
    public $_id; // ид процесса бота

    /*
     * бот начинает работать
     * необходим запуск этого метода перед .run()
     */
    public function lifeStart()
    {
        ini_set('max_execution_time', $this->lifeTime);
        if(ini_get('max_execution_time') != $this->lifeTime){
            Log_Sys::put(SLOG_ESTOP, UNIT_NAME.".lifeStart()", "Не могу изменить max_execution_time");
        }
        $this->id=mb_substr(md5(mt_rand()), 0,10);
        MC::set(UNIT_NAME.'_ID.'.$this->_id, $this->id);
        MC::set(UNIT_NAME.'_START_AT.'.$this->_id, Tools::getMicroTime());
        MC::set(UNIT_NAME.'_PING.'.$this->_id, Tools::getMicroTime());
        //Log_Sys::put(SLOG_INFO, UNIT_NAME.'.lifeStart()','Запущен', UNIT_NAME.'_ID.'.$this->_id.' = '.$this->id);
        return $this;
    }

    /*
     * пинг в процессе работы бота
     */
    public function lifeProlong()
    {
        if(MC::get(UNIT_NAME.'_ID.'.$this->_id)!=$this->id){
            Log_Sys::put(SLOG_ETERM, UNIT_NAME.".lifeProlong()", 'Не найден или заменен ID запущенного бота в MC. Возможно MC был перезапущен', UNIT_NAME.'_ID.'.$this->_id." != {$this->id}");
        }
        MC::set(UNIT_NAME.'_PING.'.$this->_id, Tools::getMicroTime());
        return $this;
    }

    /*
     * запустить это для корректной остановки бота
     */
    public function end()
    {
        if(MC::get(UNIT_NAME.'_ID.'.$this->_id)!=$this->id){
            Log_Sys::put(SLOG_ETERM, UNIT_NAME.".end()", 'Не найден или заменен ID запущенного бота в MC. Возможно MC был перезапущен', UNIT_NAME.'_ID.'.$this->_id." != {$this->id}");
        }
        MC::del(UNIT_NAME.'_PING.'.$this->_id);
        MC::del(UNIT_NAME.'_ID.'.$this->_id);
        MC::del(UNIT_NAME.'_START_AT.'.$this->_id);
    }

    /*
     * проверка на дубль рабочего бота перед началом выполнения
     * считаем любого бота закончившим работу, если метка в пинге устарела более чем на pingInterval * 2 ИЛИ нет метки пинга вообще
     */
    public function alone()
    {
        if(!defined('UNIT_NAME')){
            Log_Sys::put(SLOG_ESTOP, UNIT_NAME.".alone()", 'Не определана константа UNIT_NAME');
        }

        $t=MC::get(UNIT_NAME.'_PING.'.$this->_id);
        //echo Tools::getMicroTime().'-'.$t.'='.(Tools::getMicroTime()-$t);
        if($t!==false && ((Tools::getMicroTime()-$t) < $this->pingInterval*2)){
            //дубль бота
            //echo UNIT_NAME.'_PING.'.$this->_id." Уже работает. Прерывание запуска.\n";
            //Log_Sys::put(SLOG_INFO, UNIT_NAME.".alone()", "Уже работает. Прерывание запуска.", UNIT_NAME.'_ID.'.$this->_id);
            exit (0);
        }
        //Log_Sys::put(SLOG_INFO, UNIT_NAME.'.alone()','Нет дубля', UNIT_NAME.'_ID.'.$this->_id);
        return $this;
    }

    /*
     * максимальное время выполнения скрипта (работы боты)
     * зависит только от возможностей хостинга
     * это значение должно быть больше pingInterval
     */
    public function lifeTime($sec)
    {
        $this->lifeTime=$sec;
        return $this;
    }

    /*
     * максимальное оценочное время между пингами
     * должно быть больше чем суммарные задержки на подключения к внешним хостам за один цикл между пингами
     * критический параметр для избежания дублей ботов в памяти
     */
    public function pingInterval($sec)
    {
        $this->pingInterval=$sec;
        return $this;
    }

    /*
     * файловый запрет (локер)  запуска бота в будующем
     */
    public static function stopUnit()
    {
        if(defined('UNIT_NAME')){
            file_put_contents((dirname(__FILE__).'/../../assets/res/'.UNIT_NAME.'.locked'), 'stoped', LOCK_EX);
        }
        exit (100);
    }

    public function resetStopFactor()
    {
        if(defined('UNIT_NAME')){
            @unlink((dirname(__FILE__).'../../assets/res/'.UNIT_NAME.'.locked'));
        }
        return $this;
    }

    /*
     * проверяет можно ли запускать бота чекая файловый стопер
     */
    public function runnableUnit()
    {
        if(defined('UNIT_NAME')){
            if(@file_get_contents(dirname(__FILE__).'/../../assets/res/'.UNIT_NAME.'.locked')=='stoped') {
                echo "Cant run: ".UNIT_NAME." is stopped by log.";
                exit (101);
            } else {
                if(!MC::chk()) {
                    echo "Cant run: ".UNIT_NAME." не подключен Memcache(d).";
                    Log_Sys::put(SLOG_ETERM, UNIT_NAME.".runnableUnit()", 'MC не подключен');
                }
                return $this;
            }
        }
    }

    /*
     * бот может продолжвать работать или пора завершать.
     * Зависит от lifeTime. Если время выполнения скрипта не превышает lifiteme-pingInterval то можно продорлжить выполение иначе пора завершать .end()
     */
    public function canContinue()
    {
        if(MC::get(UNIT_NAME.'_ID.'.$this->_id)!=$this->id){
            Log_Sys::put(SLOG_ETERM, UNIT_NAME.".canContinue()", 'Не найден или заменен ID запущенного бота в MC. Возможно MC был перезапущен', UNIT_NAME.'_ID.'.$this->_id." != {$this->id}");
        }
        $startedAt=MC::get(UNIT_NAME.'_START_AT.'.$this->_id); // время старта бота с микросекундами
        if((Tools::getMicroTime()-$startedAt) > ($this->lifeTime-$this->pingInterval*2)) {
            //Log_Sys::put(SLOG_INFO, UNIT_NAME.'.canContinue()','Нельзя продолжать', UNIT_NAME.'_ID.'.$this->_id.' = '.$this->id);
            return false;
        } else {
            //Log_Sys::put(SLOG_INFO, UNIT_NAME.'.canContinue()','Можно продолжать', UNIT_NAME.'_ID.'.$this->_id.' = '.$this->id);
            return true;
        }
    }



    function __construct() {
        parent::__construct();
        $this->_id=crc32(Cfg::_get('site_name'));
    }
}

class BotException extends CommonException {

}
