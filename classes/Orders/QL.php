<?
class Orders_QL extends CommonStatic 
{
	

    static function addHucksterOrder($r)
    {
        /* r=array(
            reqUserName:bool
            minPrice:int
            email:string
            tel:string
            userName:string
            userId:int
            cat_id:int
            am:int
            comment:string
            price:float
        */

        $db=new CC_Base();

        $r['email']=@$r['email'];
        $r['userName']=@$r['name'];
        $r['comment']=@$r['comment'];
        $r['tel']=@$r['tel'];
        $r['userId']=(int)@$r['userId'];
        $r['cat_id']=(int)@$r['cat_id'];
        $r['am']=abs((int)@$r['am']);
        $r['price2']=abs((float)@$r['price']);

        if(!isset($r['minPrice'])) $r['minPrice']=0;

        if(!empty($r['reqEmail'])) static::putMsg(false,'Укажите, пожалуйста, адрес электронной почты',1);
        if(!empty($r['email']) && !Tools::emailValid($r['email'])) static::putMsg(false,'Не корректный электронной почты',1);
        if(!empty($r['reqName']) && empty($r['userName'])) static::putMsg(false,'Не указано имя',1);
        if(!empty($r['reqTel']) && empty($r['tel'])) static::putMsg(false,'Не указан контактный телефон',1);
        if($r['price2']<$r['minPrice']) static::putMsg(false,'Введите, пожалуйста, корректную цену товара',1);

        if(!static::$fres) return false;

        $r['email']=Tools::esc($r['email']);
        $r['userName']=Tools::esc($r['userName']);
        $r['tel']=Tools::esc($r['tel']);
        $r['comment']=Tools::esc($r['comment']);

        $ip=$r['userIP']=@$_SERVER['REMOTE_ADDR'];
        $r['userIP']=array("INET_ATON('{$r['userIP']}')",'noquot');
        $r['dt_added']=date("Y-m-d H:i:s");

        $d=$db->fetchAll("SELECT * FROM os_qlist WHERE cat_id='{$r['cat_id']}' AND (tel!='' AND tel LIKE '{$r['tel']}' OR email!='' AND email LIKE '{$r['email']}')");
        if(count($d)) return static::putMsg(false,'Пользователь с такой электронной почтой или телефоном уже отправлял заказ на этот товар');

        $r['tname']='';
        $db->que('cat_by_id',$r['cat_id']);
        if($db->qnum()){
            $db->next();
            $r['gr']=$db->qrow['gr'];
            $r['tname']=$db->qrow['bname'].' '.$db->qrow['name'];
            $r['price1']=$db->qrow['cprice'];
            switch($r['gr']){
                case 1:
                    $r['tname'].=trim(Tools::cutDoubleSpaces("{$db->qrow['P3']}/{$db->qrow['P2']} ".($db->qrow['P6']?'Z':'')."R{$db->qrow['P1']} {$db->qrow['P7']} {$db->qrow['csuffix']}"));
                    break;
                case 2:
                    $r['tname'].=trim(Tools::cutDoubleSpaces("{$db->qrow['P2']}Jx{$db->qrow['P5']} {$db->qrow['P4']}/{$db->qrow['P6']} ET{$db->qrow['P1']} D{$db->qrow['P3']} {$db->qrow['csuffix']}"));
                    break;
            }
        }

        unset($r['reqUserName'],$r['reqTel'],$r['price'],$r['minPrice'],$r['name'],$r['reqEmail']);

        $db->insert('os_qlist',$r);
        $id=$db->lastId();

        $toAddr=Data::get('mail_info');
        $charset=Data::get('mail_charset');
        $host=Data::get('mail_robot_host');
        $logpw=Data::get('mail_robot_logpw');
        $toAddr=Data::get('mail_order');
        $secure=Data::get('mail_robot_smtp_secure');
        $fromName='';
        $fromAddr=Data::get('mail_robot');
        $subj='Новый заказ с торгом №'.$id.' на '.Cfg::get('site_name');
        $body="Имя: <b>{$r['userName']}</b><br>";
        $body.="Телефон: <b>{$r['tel']}</b><br>";
        $body.="Email: <b>{$r['email']}</b><br>";
        $body.="Артикул: <b>{$r['cat_id']}</b><br>";
        $body.="Наименование товара: <b>{$r['tname']}</b><br>";
        $body.="Цена на сайте: <b>{$r['price1']}</b> руб.<br>";
        $body.="Желаемая цена: <b>{$r['price2']}</b> руб.<br>";
        $body.="Кол-во: <b>{$r['am']}</b><br>";
        if(($ref=@$_SERVER['HTTP_REFERER'])!='') $body.="Урл страницы: <a href=\"$ref\" target=\"_blank\">$ref</a><br>";
        $r['comment']=nl2br($r['comment']);
        $body.="Комментарий: <br><div style=\"margin:15px; border:1px dashed #000000; padding:15px;\">{$r['comment']}</div>";

        if(!Mailer::sendmail(array(
            'fromAddr'=>$fromAddr,
            'fromName'=>$fromName,
            'toAddr'=>$toAddr,
            'toName'=>$r['userName'],
            'body'=>$body,
            'subject'=>$subj,
            'charset'=>$charset,
            'host'=>$host,
            'SMTPSecure'=>$secure,
            'logpw'=>$logpw
        ))){
            return static::putMsg(false,'Ошибка отправки письма');
        }

        return true;
    }


}