<?
define ('UNIT_NAME', 'SMSChk.bot');

class Bot_SMS extends Bot_Common
{


    function __construct() {
        parent::__construct();
    }

    public function run()
    {
        //Log_Sys::put(SLOG_INFO, UNIT_NAME.".run(-)", "Основная программа", UNIT_NAME.'_ID.'.$this->_id." = {$this->id}");

        $sms=SMS_Reactor::factory();
        if($sms===false) Log_Sys::put(SLOG_ESTOP,'Bot_SMSChk','SMS_Reactor::factory() fault.');

        do{
            $d=$this->fetchAll("SELECT msgId FROM log_sms WHERE status='1' AND msgId!='' ORDER BY GREATEST(dtCreated,dtCheck) ASC LIMIT 0,5000", MYSQLI_ASSOC);

            foreach($d as $v){
                $r=$sms->check($v['msgId']);
                if($r===false){
                    Log_Sys::put(SLOG_ESTOP,'Bot_SMSChk',$sms->strMsg("\n"));
                }

                if(!$this->canContinue()) $this->end();
                $this->lifeProlong();
            }

            sleep(10);
            if(!$this->canContinue()) $this->end();
            $this->lifeProlong();

        }while(!empty($d));


        $this->end();


    }

    public function end()
    {

        // ставим статус не доставлен просроченным записям старше 2 дней
        $this->query("UPDATE log_sms SET status='3', dtCheck='".Tools::dt()."' WHERE status='1' AND dtCreated < DATE_SUB(CURRENT_DATE, INTERVAL 2 DAY)");

        //Log_Sys::put(SLOG_INFO, UNIT_NAME.".end(-)", "Завершен.", UNIT_NAME.'_ID.'.$this->_id." = {$this->id}");

        // метка для контроля работоспособности. Двойное время жизни процесса бота
        MC::set($this->mcBotName(),Tools::getMicroTime(),$this->lifeTime*2);
        parent::end();
        exit(0);
    }

    public function mcBotName()
    {
        return UNIT_NAME.MC::uid();
    }


}