<?
if (!defined('true_enter')) die ("No direct access allowed.");

class SMS_SMSAero extends Common  {

    private $user, $pw;
    public $lastResponse;

    function __construct($user,$pw)
    {
        $this->user=$user;
        $this->pw=$pw;
    }

    function send($source, $dest, $msg)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,"http://gate.smsaero.ru/send/");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 4);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            http_build_query(array(
                'to'=>htmlentities($dest, ENT_NOQUOTES, 'UTF-8'),
                'text'=>htmlentities($msg, ENT_NOQUOTES, 'UTF-8'),
                'from'=>htmlentities($source, ENT_NOQUOTES, 'UTF-8'),
                'user'=>$this->user,
                'password'=>$this->pw
            ),'','&'));

        $response = curl_exec($ch);

        if (curl_errno($ch)) {

            return array('status'=>0, 'statusMsg'=>'SMS.send(): httpError: '.curl_error($ch));

        } else {

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if($httpCode !=200){
                return array('status'=>0, 'statusMsg'=>'SMS.send(): httpError code='.$httpCode);
            } else{

                $this->lastResponse=$r=$response;

                /*
                 * Параметр	Описание
                    accepted	                            Сообщение принято сервисом
                    empty field. reject	                    Не все обязательные поля заполнены
                    incorrect user or password. reject	    Ошибка авторизации
                    no credits	                            Недостаточно sms на балансе
                    incorrect sender name. reject	        Неверная (незарегистрированная) подпись отправителя
                    incorrect destination adress. reject	Неверно задан номер телефона (формат 71234567890)
                    incorrect date. reject	                Неправильный формат даты
                    in blacklist. reject	                Телефон находится в черном списке
                 */

                $r=explode('=',$response);
                if(count($r)!=2) return array('status'=>0, 'statusMsg'=>"SMS.send(): $response");
                $msgId=$r[0];

                if($r[1]=='accepted')
                    return array('status'=>1, 'statusMsg'=>"SMS.send({$msgId}): Сообщение принято сервисом", 'msgId'=>$msgId);

            }
        }
    }

    function balance()
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,"http://gate.smsaero.ru/balance/?answer=json");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            /*
                'get_billing_balance': [],
                'username': '',
                'password': ''
             */
            http_build_query(array(
                'user'=>$this->user,
                'password'=>$this->pw
            ),'','&'));

        $response = curl_exec($ch);

        if (curl_errno($ch)) {

            return array('status'=>0, 'statusMsg'=>'SMS.balance(): httpError: '.curl_error($ch));

        } else {

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if($httpCode !=200){
                return array('status'=>0, 'statusMsg'=>'SMS.balance(): httpError code='.$httpCode);
            } else{

                $this->lastResponse=$r=@json_decode($response);

                // http://smsaero.ru/api/

                $r=(array)$r;
                if(!isset($r['balance'])){
                    if(isset($r['reason']))
                        return array('status'=>0, 'statusMsg'=>"SMS.balance(): {$r['reason']}");
                    else
                        return array('status'=>0, 'statusMsg'=>"SMS.balance(): неизвестная ошибка");
                } else {
                    return array('status'=>1, 'balance'=>(float)$r['balance']);
                }
            }
        }
    }

    function check($msgId)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,"http://gate.smsaero.ru/status/");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            http_build_query(array(
                'id'=>$msgId,
                'user'=>$this->user,
                'password'=>$this->pw
            ),'','&'));

        $response = curl_exec($ch);

        if (curl_errno($ch)) {

            return array('status'=>1, 'statusMsg'=>'SMS.check(): httpError: '.curl_error($ch));

        } else {

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if($httpCode !=200){
                return array('statusMsg'=>"SMS.check($msgId): httpError code=$httpCode");
            } else{

                $this->lastResponse=$r=$response;

                /*
                 * Параметр	Описание
                    delivery success	                Сообщение доставлено
                    delivery failure	                Ошибка доставки SMS (абонент в течение времени доставки находился вне зоны действия сети или номер абонента заблокирован)
                    smsc submit	                        Сообщение доставлено в SMSC
                    smsc reject	                        отвергнуто SMSC
                    queue	                            Ожидает отправки
                    wait status	                        Ожидание статуса (запросите позднее)
                    incorrect id. reject	            Неверный идентификатор сообщения
                    empty field. reject	                Не все обязательные поля заполнены
                    incorrect user or password. reject	Ошибка авторизации
                 */

                $r=explode('=',$response);
                if(count($r)!=2) return array('status'=>0, 'statusMsg'=>"SMS.check({$msgId}): $response");

                switch ($r[1]){
                    case 'delivery success':
                        return array('status'=>2, 'statusMsg'=>"SMS.check({$msgId}): Доставлено");
                    case 'smsc submit':
                        return array('status'=>1, 'statusMsg'=>"SMS.check({$msgId}): Сообщение доставлено в SMSC");
                    case 'delivery failure':
                        return array('status'=>0, 'statusMsg'=>"SMS.check({$msgId}): Ошибка доставки SMS (абонент в течение времени доставки находился вне зоны действия сети или номер абонента заблокирован)");
                    case 'smsc reject':
                        return array('status'=>0, 'statusMsg'=>"SMS.check({$msgId}): Отвергнуто SMSC");
                    case 'queue':
                        return array('status'=>1, 'statusMsg'=>"SMS.check({$msgId}): Ожидает отправки");
                    case 'wait status':
                        return array('status'=>1, 'statusMsg'=>"SMS.check({$msgId}): Ожидание статуса (запросите позднее)");
                    default:
                        return array('status'=>1, 'statusMsg'=>"SMS.check({$msgId}): {$r[1]}");
                }
            }
        }

    }


}