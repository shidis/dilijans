<?
if (!defined('true_enter')) die ("No direct access allowed.");


class SMS_Reactor extends DB {

    private $o=NULL,
        $user,
        $pw,
        $checkDeliveryMaxTime;

    public $service;

    public function lastResponse()
    {
        return $this->o->lastResponse;
    }

    public static function factory($service='',$user='',$pw='')
    {
        if(empty($service)) $service=Data::get('SMS_service');
        if(empty($user)) $user=Data::get('SMS_user');
        if(empty($pw)) $pw=Data::get('SMS_pw');
        if(empty($service) || empty($user) || empty($pw)) {
            return false;
        }
        if(is_file(Cfg::$config['root_path'].'/classes/SMS/'.$service.'.php')){
            $instance=new SMS_Reactor();
            $instance->user=$user;
            $instance->pw=$pw;
            $s='SMS_'.$service;
            $instance->o=new $s($instance->user,$instance->pw);
            $instance->service=$service;
            return $instance;
        } else return false;
    }

    /*
     * $source    отправитель - зарегистрированое имя в сервисе рассылки смс, =='_default_' - основной отправитель, берется из Cfg::$config['SMS']['defaultSource']
     * $dest  получатель - номер телефона в формате 79161234567
     * $msg     сообщение
     *
     * @return array(status=>(1|0), statusMsg=>сообщение с ошибкой/кодом, msgId - ID сообщения, если status==1)
     *
     * метод подкласса должен возвращать такой же формат return
     */
    public function send($source, $dest, $msg)
    {
        if(is_null($this->o)) return $this->putMsg(false, "Сервис рассылки не инициализирован");
        if($source=='_default_') $source=Data::get('SMS_defaultSource');
        $r=$this->o->send($source, $dest, $msg) + array('dt'=>Tools::dt());
        $log=array(
            'source'=>Tools::esc($source),
            'dest'=>Tools::esc($dest), // телефон должен быть в корректном формате уже здесь
            'cUserId'=>CU::$userId,
            'dtCreated'=>$r['dt'],
            'msg'=>Tools::esc($msg),
            'statusMsg'=>Tools::esc(@$r['statusMsg'])
        );
        if($r['status']){
            $log['msgId']=Tools::esc(@$r['msgId']);
            $log['status']=1;
        }else{
            $log['status']=0;
        }
        $this->insert('log_sms',$log);
        return $r;
    }

    /*
     * проверка доствки сообщения
     * @return  array(status=> 0 - фатальная ошибка, 1 - не доставлено, пингуем дальше, 2 - доставлено, задание выполенно, statusMsg=>строка с расшифровкой статуса)
     *
     * ststus=3 - не доставлено/подтверждение не получено - статус ставится крон-пингатором по истечению срока обслуживания сообщения
     *
     * поле status может отсуствовать - в базе сохранится прежнее значение
     *
     *  метод подкласса должен возвращать такой же формат return
     */
    public function check($msgId)
    {
        if(is_null($this->o)) return $this->putMsg(false, "Сервис рассылки не инициализирован");
        $r=$this->o->check($msgId) + array('dt'=>Tools::dt());
        $log=array(
            'dtCheck'=>$r['dt'],
            'statusMsg'=>Tools::esc(@$r['statusMsg'])
        );
        if(isset($r['status'])) $log['status']=(int)$r['status'];
        $this->update('log_sms',$log,"msgId='".Tools::esc($msgId)."'");
        return $r;
    }

    /*
     * проверка баланса
     * return array('status=>0|1, statusMsg=>с сообщением об ошибке, balance=>float
     */
    public function balance()
    {
        if(is_null($this->o)) return $this->putMsg(false, "Сервис рассылки не инициализирован");
        return $this->o->balance() + array('dt'=>Tools::dt());
    }

    /*
     * проверяет на валидность номер получателя
     * приводит и возвращает коррекную запись
     * или возвращает false если отправка не возможна
     *
     * корректным здесь считаем запись из 11 чисел с семеркой вначале 71234567890
     * при возможности приводжим к этому формату
     */
    public function checkTelNumber($tel)
    {
        $tel=explode(',',$tel);
        $tel=$tel[0];
        $s=trim(preg_replace("~[^0-9]~u",'',$tel));
        if(empty($s)) return false;
        if($s{0}=='8')
            $s{0}=7;
        elseif($s{0}!='7')
            $s="7{$s}";

        if(mb_strlen($s)!=11) return '';

        return $s;
        /*
        $s=preg_replace("~[^0-9]~iu",'',$tel);
        if(mb_strlen($s)==10)
            $s='7'.$s;
        else
            if(mb_strlen($s)==11){
                if(mb_substr($s,0,1)=='8') $s=preg_replace("~^8(.+)$~","7$1",$s);
                elseif(mb_substr($s,0,1)!='7') $s='';
            } else $s='';

        if(empty($s)) return false; else return $s;
        */
    }


}