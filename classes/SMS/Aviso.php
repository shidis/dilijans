<?
if (!defined('true_enter')) die ("No direct access allowed.");

class SMS_Aviso extends Common  {

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

        curl_setopt($ch, CURLOPT_URL,"http://api.avisosms.ru/sms/json/1");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            /*
             * 'send_message':
                    [{
                        'destination_address': '79853018865',
                        'message': 'привет',
                        'source_address': '792511761201'
                    }],
                'username': 'pzero',
                'password': '****'
             */
            json_encode(array(
                'send_message'=>array(
                    array(
                        'destination_address'=> htmlentities($dest, ENT_NOQUOTES, 'UTF-8'),
                        'message'=> htmlentities($msg, ENT_NOQUOTES, 'UTF-8'),
                        'source_address'=> htmlentities($source, ENT_NOQUOTES, 'UTF-8')
                    )
                ),
                'username'=>$this->user,
                'password'=>$this->pw
            )));

        $response = curl_exec($ch);

        if (curl_errno($ch)) {

            return array('status'=>0, 'statusMsg'=>'SMS.send(): httpError: '.curl_error($ch));

        } else {

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if($httpCode !=200){
                return array('status'=>0, 'statusMsg'=>'SMS.send(): httpError code='.$httpCode);
            } else{

                $this->lastResponse=$r=@json_decode($response);

                // http://avisosms.ru/docs/doku.php/send-json

                $r=(array)$r;
                if(@$r['status']!='OK_Operation_Completed'){
                    return array('status'=>0, 'statusMsg'=>"SMS.send(): ".@$r['status']);
                } else {
                    $r['send_message'][0]=(array)$r['send_message'][0];
                    if(empty($r['send_message'][0]['id'])){
                        return array('status'=>0, 'statusMsg'=>'SMS.send(): Не получен msgId :: '.@$r['send_message'][0]['status']);
                    } else {
                        return array('status'=>1, 'statusMsg'=>"SMS.send({$r['send_message'][0]['id']}): ".@$r['send_message'][0]['status'], 'msgId'=>$r['send_message'][0]['id']);
                    }
                }
            }
        }
    }

    function balance()
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,"http://api.avisosms.ru/sms/json/1");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            /*
                'get_billing_balance': [],
                'username': '',
                'password': ''
             */
            json_encode(array(
                'get_sms_balance'=>array(),
                'username'=>$this->user,
                'password'=>$this->pw
            )));

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

                // http://avisosms.ru/docs/doku.php/balance-json

                $r=(array)$r;
                if(@$r['status']!='OK_Operation_Completed'){
                    return array('status'=>0, 'statusMsg'=>"SMS.balance(): ".@$r['status']);
                } else {
                    if(!isset($r['sms_balance'])){
                        return array('status'=>0, 'statusMsg'=>"SMS.balance(): Неверный формат ответа");
                    }
                    return array('status'=>1, 'balance'=>(float)$r['sms_balance']);
                }
            }
        }
    }

    function check($msgId)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,"http://api.avisosms.ru/sms/json/1");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            /*
                'get_message_state': ['msgid'],
                'username': '',
                'password': ''
             */
            json_encode(array(
                'get_message_state'=>array($msgId),
                'username'=>$this->user,
                'password'=>$this->pw
            )));

        $response = curl_exec($ch);

        if (curl_errno($ch)) {

            return array('status'=>0, 'statusMsg'=>'SMS.check(): httpError: '.curl_error($ch));

        } else {

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if($httpCode !=200){
                return array('statusMsg'=>"SMS.check($msgId): httpError code=$httpCode");
            } else{

                $this->lastResponse=$r=@json_decode($response);

                // http://avisosms.ru/docs/doku.php/send-json

                $r=(array)$r;
                if(@$r['status']!='OK_Operation_Completed'){
                    return array('status'=>0, 'statusMsg'=>"SMS.check($msgId): ".@$r['status']);
                } else {
                    if(!isset($r['get_message_state'])){
                        return array('status'=>0, 'statusMsg'=>"SMS.check($msgId): Неверный формат ответа");
                    }
                    $r['get_message_state']=(array)$r['get_message_state'];
                    if(!isset($r['get_message_state'][$msgId])){
                        return array('status'=>0, 'statusMsg'=>"SMS.check($msgId): В ответе не найден msgId");
                    }
                    if($r['get_message_state'][$msgId]=='Delivered_To_Recipient'){
                        return array('status'=>2,'statusMsg'=>"SMS.check($msgId): {$r['get_message_state'][$msgId]}");
                    }elseif(in_array($r['get_message_state'][$msgId], array('Sent','Delivered_To_Gateway'))){
                        return array('status'=>1,'statusMsg'=>"SMS.check($msgId): {$r['get_message_state'][$msgId]}");
                    }else{
                        return array('status'=>0, 'statusMsg'=>"SMS.check($msgId): Ошибка доставки: {$r['get_message_state'][$msgId]}");
                    }
                }
            }
        }

    }


}