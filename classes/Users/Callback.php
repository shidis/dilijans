<?

class Users_Callback extends CommonStatic
{


    public static function push($r)
    {
        /* r=array(
            tel:string      !
            name:string
            comment:string
            time:string
            sendMail:bool
        */

        $db = new CC_Base();

        $r['tel'] = trim(@$r['tel']);
        $r['name'] = trim(@$r['name']);
        $r['comment'] = trim(@$r['comment']);
        $r['time'] = trim(@$r['time']);
        $ref = @$_SERVER['HTTP_REFERER'];

        if(!empty($r['sendMail'])) {
            $toAddr = Data::get('mail_info');
            $fromName = '';
            $fromAddr = Data::get('mail_robot');
            $subj = 'Запрос на обратный звонок';
            $name=preg_replace("~[^a-zа-я\s\-]~iu",'',$r['name']);
            $body = "Имя: <b>$name</b><br>\n\n";
            $body .= "Телефон: <b>{$r['tel']}</b><br>\n\n";
            if ($ref != '') $body .= "Урл страницы: <a href=\"$ref\" target=\"_blank\">$ref</a><br>\n\n";
            if(!empty($r['time'])) $body .= "Предпочтительное время звонка: {$r['time']}<br>\n\n";
            $comment = nl2br($r['comment']);
            if(!empty($r['comment'])) $body .= "Комментарий/вопрос: <br>\n<blockquote>{$comment}</blockquote>";

            if (!Mailer::sendmail($ar=array(
                'fromAddr' => $fromAddr,
                'fromName' => $fromName,
                'toAddr'   => $toAddr,
                'toName'   => $name,
                'body'     => $body,
                'subject'  => $subj
            ))
            ) {
                Log_Sys::put(SLOG_ERROR, 'Users_Callback.push', 'Ошибка отправки письма', Tools::esc(print_r($ar,true)));
            }
        }

        $tel = Tools::esc($r['tel']);
        $name = Tools::esc($r['name']);
        $comment = $r['comment'];
        if(!empty($r['time']))
            if(!empty($comment))
                $comment.="\n\nУдобное время звонка: {$r['time']}";
            else
                $comment="Удобное время звонка: {$r['time']}";

        $comment=Tools::esc($comment);

        $db->insert('os_callback', [
            'tel'=>$tel,
            'name'=>$name,
            'comment'=>$comment,
            'dt'=>Tools::dt(),
            'udata'=>empty(CU::$userId)?App_Users_UData::makeDBStr(['ref'=>$ref]):''
        ]);

        return true;
    }
}
