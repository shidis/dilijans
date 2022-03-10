<?
if (!defined('true_enter')) die ("Direct access not allowed!");

class App_Subscribe extends DB
{

    function __construct()
    {
        parent::__construct();
    }

    function subscribe_email($email)
    {
        $email=Tools::esc(Tools::like(trim($email)));
        $dt=date("Y-m-d H:i:s");
        $ip=$_SERVER['REMOTE_ADDR'];
        $r=new stdClass();
        $r->fres=false;
        if(Tools::emailValid($email)){
            $this->query("SELECT * FROM scr_email WHERE email LIKE '$email'");
            if(!$this->qnum()){
                if($this->query("INSERT INTO scr_email (email,dt_add,ip) VALUES('$email','$dt','$ip')")) $r->fres=true;
                else $r->msg='Ошибка записи в БД';
            }else $r->msg='Уже есть подписка на этот адрес';
        }else $r->msg='Не корректный адрес электронной почты';
        return $r;
    }

}