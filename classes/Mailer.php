<?

class Mailer
{

    static $mail = NULL;
    static $errors = '';

    static public function sendmail($data = [])
    {
        // http://php.russofile.ru/ru/translate/mail/phpmailer/
        /*	параметры:
        fromAddr,
        fromName='',
        toAddr,
        toName='',
        replyToAddr='',
        replyToName='',
        body:{текст письма или массив с переменными для шаблона tpl},
        subject='',
        tpl='',
        charset='utf-8',
        host='',
        logpw='',
        debug=0,
        attachments: array(array(0=>'файл в локальной файловой системе', 1=>'имя файла во вложении письма'),...),
        isHTML - сказать классу что у нас в теле html  default=true
        */

        $tpl = trim(@$data['tpl']);
        if (!empty($tpl) && is_file(Cfg::_get('root_path') . "/app/templates/{$tpl}"))
        {
            extract($data['body']);
            $er = error_reporting(0);
            ob_start();
            eval("include '" . Cfg::_get('root_path') . "/app/templates/{$tpl}';");
            $body = ob_get_contents();
            ob_end_clean();
            error_reporting($er);
        }
        else $body = $data['body'];

        try
        {

            if (empty(static::$mail))
            {
                include_once(Cfg::$config['root_path'] . '/inc/PHPMailer/class.phpmailer.php');
                static::$mail = new PHPMailer(true);
            }
            else
            {
                static::$mail->clearAddresses();
                static::$mail->clearAttachments();
            }

            if (!empty($data['host']))
            {
                // авторизация
                static::$mail->isSMTP();
                $host = explode(':', $data['host']);
                static::$mail->SMTPAuth = true;
                static::$mail->AuthType = 'LOGIN';
                static::$mail->Host = $host[0];
                if (empty($host[1])) $host[1] = 25;
                static::$mail->Port = $host[1];
                static::$mail->SMTPDebug = (int)@$data['debug'];
                static::$mail->Debugoutput = 'echo';
                $logpw = explode(':', $data['logpw']);
                static::$mail->Username = @$logpw[0];
                static::$mail->Password = @$logpw[1];
                if (!isset($data['SMTPSecure'])) $secure = '';
                else $secure = $data['SMTPSecure'];
                static::$mail->SMTPSecure = $secure;
            }
            else
            {
                static::$mail->isMail();
            }

            //Sets the Encoding of the message. Options for this are "8bit", "7bit", "binary", "base64", and "quoted-printable".
            static::$mail->Encoding = '8bit';

            //Sets the CharSet of the message.
            if (empty($data['charset'])) $data['charset'] = 'utf-8';
            static::$mail->CharSet = $data['charset'];

            if (!empty($data['replyToAddr']))
            {
                //Adds a "Reply-to" address.
                // boolean AddReplyTo (string $address, [string $name = ''])
                $replyAddr = preg_split("/[;,]/", $data['replyToAddr']);
                foreach ($replyAddr as $v)
                {
                    $v = trim($v);
                    if (Tools::emailValid($v)) static::$mail->addReplyTo($v, @$data['replyToName']);
                }
            }

            //Adds a "To" address.
            //boolean AddAddress (string $address, [string $name = ''])
            $toAddr = preg_split("/[;,]/", $data['toAddr']);
            foreach ($toAddr as $v)
            {
                $v = trim($v);
                if (Tools::emailValid($v))
                {
                    static::$mail->addAddress($v, @$data['toName']);
                }
            }

            //Set the From and FromName properties
            //boolean SetFrom (string $address, [string $name = ''])
            static::$mail->setFrom($data['fromAddr'], @$data['fromName']);

            static::$mail->Subject = @$data['subject'];

            /*Sets the text-only body of the message. This automatically sets the email to multipart/alternative. This body can be read by mail clients that do not have HTML email capability such as mutt. Clients that can read HTML will view the normal Body.*/
            static::$mail->AltBody = 'To view the message, please use an HTML compatible email viewer!';

            static::$mail->msgHTML($body);

            if (!empty($data['attachments']))
            {
                foreach ($data['attachments'] as $v)
                {
                    if (is_array($v)) static::$mail->addAttachment($v[0], $v[1]);
                    else static::$mail->addAttachment($v);
                }
            }

            static::$mail->Priority = 1;

            if (isset($data['isHTML']) && !$data['isHTML']) static::$mail->isHTML(false);
            else
                static::$mail->isHTML(true);

            static::$mail->send();

            static::$mail->clearAddresses();
            static::$mail->clearAttachments();

            return true;

        } catch (phpmailerException $e)
        {
            static::$errors = $e->getMessage(); //Pretty error messages from PHPMailer

            return false;
        } catch (Exception $e)
        {
            static::$errors = $e->getMessage(); //Boring error messages from anything else!

            return false;
        }
    }


}