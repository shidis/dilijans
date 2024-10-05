<?

class App_Ax_Controller extends App_Common_Controller
{

    public function geoCity()
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        if (server_loc == 'local' || $this->adminLogged)
        {
            // москва
            //$ip='77.37.184.126';
            //Иркутск
            //$ip='176.215.234.104';
            // Сактывкар, Коми
            //$ip='5.142.22.86';
        }

        $this->r['fres'] = true;
        $this->r['geo'] = ['ip' => $ip];
        $geo = new App_Geo(true);
        $res = $geo->resolveWithMapsTable($ip);
        if ($res !== false) $this->r['geo'] = array_merge($this->r['geo'], $res);
        else
        {
            $this->r['fres'] = false;
            $this->r['err_msg'] = Msg::asStr("\r\n");
        }
    }

    public function subscribe()
    {
        sleep(1);
        $email = @$_REQUEST['email'];
        $sc = new App_Subscribe();
        $rr = $sc->subscribe_email($email);
        if (!$rr->fres)
        {
            $this->r['err_msg'] = $rr->msg;
            $this->r['fres'] = false;

            return;
        }
    }

    public function tip()
    {
        echo $this->parse($this->ss->getDoc(@Url::$spath[3] . '$17'));
    }

    public function regionDeliveryCost()
    {
        sleep(2);
        $f = Tools::strarr(@$_REQUEST['f']);

        $this->r['fres'] = false;
        if ($this->cityId == -77 || empty($this->cityId)) $this->r['fres'] = "Стоимость доставки по Москве составляет " . Data::get('delivery_cost') . ' руб';
        else
        {

            $gr = (int)@$f['gr'];
            if ($gr == 2)
            {
                $width = @$f['w'];
                $radius = @$f['d'];
                $dcost = App_Dostavka::getCost(2, ['d' => $radius, 'j' => $width, 'city_id' => $this->cityId, 'am' => 4]);
                if ($dcost !== false)
                {
                    $this->r['fres'] = "<p>Стоимость доставки комплекта из <b>четырех</b> дисков составляет <b>{$dcost['cost']}</b> р. Приблизительный срок доставки <b>{$dcost['info']}</b> суток.</p>
                    <p><b>Внимание!</b> Рассчитанная сумма доставки предварительная! Точную стоимость доставки уточняйте в самой транспортной компании которая Вам предпочтительнее, так-как сумма может отличаться. Дополнительная упаковка (обрешетка)  и страхование груза увеличивает стоимость пересылки.</p>";
                }
                else
                    $this->r['fres'] = "Извините, но расчет для этого города дисков размером {$width} x {$radius} сделать не получилось. Обратитесь к нашим менеджерам по телефону, пожалуйста.";
            }
            else
            {
                $width = @$f['w'];
                $height = @$f['h'];
                $radius = @$f['d'];
                $dcost = App_Dostavka::getCost(1, ['d' => $radius, 'w' => $width, 'h' => $height, 'city_id' => $this->cityId, 'am' => 4]);
                if ($dcost !== false)
                {
                    $this->r['fres'] = "<p>Стоимость доставки комплекта из <b>четырех</b> шин составляет <b>{$dcost['cost']}</b> р. Приблизительный срок доставки <b>{$dcost['info']}</b> суток.</p>
                    <p><b>Внимание!</b> Рассчитанная сумма доставки предварительная! Точную стоимость доставки уточняйте в самой транспортной компании которая Вам предпочтительнее, так-как сумма может отличаться. Дополнительная упаковка (обрешетка)  и страхование груза увеличивает стоимость пересылки.</p>";
                }
                else
                    $this->r['fres'] = "Извините, но расчет для этого города шин размером {$width}/{$height} R{$radius} сделать не получилось. Обратитесь к нашим менеджерам по телефону, пожалуйста.";
            }
        }


    }


    public function explain()
    {
        switch (@Url::$spath[3])
        {
            default:
                echo '';
                break;
            case 'suf':
                if (intval(@$_GET['v']))
                {
                    $this->cc->que('dict_by_id', intval($_GET['v']));
                    if ($this->cc->qnum())
                    {
                        $this->cc->next();
                        echo Tools::stripTags(Tools::unesc($this->cc->qrow['text']));
                    }
                    else echo '';
                }
                else echo '';
                break;
            case 'color':
                if (intval(@$_GET['v']))
                {
                    $this->cc->que('dict_by_id', intval($_GET['v']));
                    if ($this->cc->qnum())
                    {
                        $this->cc->next();
                        echo 'Цвет диска: ' . Tools::stripTags(Tools::unesc($this->cc->qrow['text']));
                    }
                    else echo '';
                }
                else echo '';
                break;
            case 'inis':
                $inis = new CC_inis();
                $r = $inis->explain_1(@$_GET['v']);
                echo $r[2];
                break;
        }
    }

    public function callbackForm()
    {
        $this->template('callbackForm');
    }

    public function galleryForm()
    {
        $this->template('galleryForm');
        $gl = new CC_Gallery();
        $this->gallery = $gl->glist(@$_REQUEST['mid']);
        if (@$_REQUEST['gr'] == 1) $this->h1 = 'Фото шин в эксплуатации';
        else $this->h1 = 'Фото дисков в эксплуатации';
    }

    public function callback()
    {

        $this->r['fres'] = true;

        $f = Tools::strarr(@$_REQUEST['f']);

        $name = Tools::esc(trim(@$f['name']));
        $tel = Tools::esc(trim(@$f['tel']));
        $comment = Tools::esc(trim(@$f['comment']));

        if (mb_strlen($name) < 4)
        {
            $this->r['fres'] = false;
            $this->r['fres_msg'] = 'Не задано имя';
        }
        
        $this->r['hp'] = $tel2 = Tools::humanPhoneNumber($tel);

        if (!Tools::phoneValid2($tel2))
        {
            $this->r['fres'] = false;
            $this->r['fres_msg'] = 'Проверьте номер телефона';
        }
        
        if(!$this->r['fres']) return;

        $toAddr = Data::get('mail_info');
        $fromName = '';
        $fromAddr = Data::get('mail_robot');
        $subj = 'Запрос на обратный звонок';
        $body = "Имя: <b>$name</b><br>";
        $body .= "Телефон: <b>$tel2</b><br>";
        if (($ref = @$_SERVER['HTTP_REFERER']) != '') $body .= "Урл страницы: <a href=\"$ref\" target=\"_blank\">$ref</a><br>";
        $comment = nl2br($comment);
        $body .= "Комментарий/вопрос: <br><blockquote>$comment</blockquote>";

        if (mb_strpos($comment, "||") !== false)
        {
            file_put_contents("callback_spam.log",Tools::dt() . " -- ". $_SERVER['REMOTE_ADDR'] . " -- ". $comment . " --- \n", FILE_APPEND);
        }
        else
        {
            return;
            if (!Mailer::sendmail([
                'fromAddr' => $fromAddr,
                'fromName' => $fromName,
                'toAddr'   => $toAddr,
                'toName'   => $name,
                'body'     => $body,
                'subject'  => $subj,
            ]))
            {
                $this->r['fres'] = false;
                $this->r['fres_msg'] = 'Ошибка отправки письма';
            }
        }
    }

    // *********** ANNOUNCE FORM 
    public function announceForm()
    {
        $this->template('announceForm');
    }

    public function announce()
    {

        $this->r['fres'] = true;

        $f = Tools::strarr(@$_REQUEST['f']);

        $quantity = Tools::esc(@$f['quantity']);
        $name = Tools::esc(@$f['name']);
        $tel = Tools::esc(@$f['tel']);
        $email = Tools::esc(@$f['email']);
        $comment = Tools::esc(@$f['comment']);

        if (mb_strlen($name) < 4)
        {
            $this->r['fres'] = false;
            $this->r['fres_msg'] = 'Не задано имя';
        }
        if (mb_strlen($tel) < 4)
        {
            $this->r['fres'] = false;
            $this->r['fres_msg'] = 'Не указан телефон';
        }

        $toAddr = Data::get('mail_info');
        $fromName = '';
        $fromAddr = Data::get('mail_robot');
        $subj = 'Клиент просит сообщить о поступлении товара';
        $body = "Имя: <b>$name</b><br>";
        $body .= "Телефон: <b>$tel</b><br>";
        $body .= "Email: <b>$email</b><br>";
        $body .= "Количество: <b>$quantity</b><br>";
        if (($ref = @$_SERVER['HTTP_REFERER']) != '') $body .= "Урл страницы: <a href=\"$ref\" target=\"_blank\">$ref</a><br>";
        $comment = nl2br($comment);
        $body .= "Комментарий/вопрос: <br><blockquote>$comment</blockquote>";

        if (!Mailer::sendmail(['fromAddr' => $fromAddr, 'fromName' => $fromName, 'toAddr' => $toAddr, 'toName' => $name, 'body' => $body, 'subject' => $subj]))
        {
            $this->r['fres'] = false;
            $this->r['fres_msg'] = 'Ошибка отправки письма';
        }
    }

    // *****************

    public function feedback()
    {
        parse_str(@$_REQUEST['f'], $f);
        $err = '';
        $this->r['uncorrect'] = [];
        if (!Tools::emailValid(@$f['email'])) $this->r['uncorrect']['email'] = false;
        if (strlen(@$f['msg']) < 10) $this->r['uncorrect']['msg'] = false;
        if (strlen($f['name']) < 3) $this->r['uncorrect']['name'] = false;
        if (!empty($this->r['uncorrect']))
        {
            $this->r['fres'] = false;

            return;
        }

        $f['msg'] = Tools::html($f['msg']);
        $f['name'] = Tools::html($f['name']);
        $f['email'] = Tools::html($f['email']);

        ob_start();
        $mailRobot = Data::get('mail_robot');
        $toAddr = Data::get('mail_info');
        $charset = Data::get('order_mail_charset');
        $host = Data::get('mail_robot_host');
        $logpw = Data::get('mail_robot_logpw');
        $secure = Data::get('mail_smtp_secure');
        $msg = "[Контактный емейл: {$f['email']}]<br>[Имя: {$f['name']}]<br>[Телефон: {$f['tel']}]<br><br>Сообщение:<br>{$f['msg']}";

        if ($f['email'] != '') $res = Mailer::sendmail([
            'fromAddr'    => $mailRobot,
            'fromName'    => $f['name'],
            'replyToAddr' => $f['email'],
            'replyToName' => $f['name'],
            'toAddr'      => $toAddr,
            'toName'      => Cfg::get('site_name'),
            'body'        => $msg,
            'subject'     => 'Письмо с сайта ' . Cfg::get('site_name'),
            'charset'     => $charset,
            'host'        => $host,
            'SMTPSecure'  => $secure,
            'logpw'       => $logpw,
            'debug'       => 1,

        ]);
        else
            $res = Mailer::sendmail([
                'fromAddr'   => $mailRobot,
                'fromName'   => $f['name'],
                'toAddr'     => $toAddr,
                'toName'     => Cfg::get('site_name'),
                'body'       => $msg,
                'subject'    => 'Письмо с сайта ' . Cfg::get('site_name'),
                'charset'    => $charset,
                'host'       => $host,
                'SMTPSecure' => $secure,
                'logpw'      => $logpw,
            ]);

        $errs = ob_get_contents();
        ob_end_clean();

        if (!$res)
        {
            $this->r['fres'] = false;
            $this->r['err_msg'] = "[Feedback.Send]: Ошибка отправки (" . Mailer::$errors . ")";
        }


    }


    public function delveryCostByCity()
    {
        $id = (int)@$_REQUEST['city_id'];
        $gr = (int)@$_REQUEST['gr'];
        $d = (float)@$_REQUEST['d'];
        if ($gr == 2)
        {
            $j = (float)@$_REQUEST['j'];
            $this->r['d'] = Dostavka::getCost($gr, ['j' => $j, 'd' => $d, 'city_id' => $id, 'am' => 4]);
        }
        else
        {
            $w = (float)@$_REQUEST['w'];
            $h = (float)@$_REQUEST['h'];
            $this->r['d'] = Dostavka::getCost($gr, ['w' => $w, 'h' => $h, 'd' => $d, 'city_id' => $id, 'am' => 4]);
        }
    }


    public function scl()
    {

        if (!CU::isLogged()) return;

        $cat_id = (int)@$_REQUEST['cat_id'];
        $this->db = new DB();
        $d = $this->db->fetchAll("SELECT cc_cat_sc.sc,cc_cat_sc.price1,cc_cat_sc.price2,cc_cat_sc.price3,cc_suplr.name, cc_suplr.suplr_id, cc_cat_sc.dt_added, cc_cat_sc.dt_upd, cc_cat_sc.ignored FROM cc_cat_sc INNER JOIN cc_suplr ON cc_cat_sc.suplr_id=cc_suplr.suplr_id WHERE cat_id='{$cat_id}' AND cc_cat_sc.sc>0 ORDER BY cc_cat_sc.price1,cc_cat_sc.price2,cc_cat_sc.price3", MYSQL_ASSOC);
        //  AND (cc_cat_sc.price1>0 OR cc_cat_sc.price2>0 OR cc_cat_sc.price3>0)

        $ids = [];
        foreach ($d as $k => $v)
        {
            $d[$k][$v['suplr_id']]['future'] = [];
            $ids[$v['suplr_id']] = $k;
        }
        if (!empty($ids)) if (!empty($this->os->adminCfg['reservation']['futureSuplr']) && !empty($this->os->adminCfg['reservation']['futureSuplr']['deliveringStateId']) && !empty($this->os->adminCfg['delivery']['DBF_deliveryDate']))
        {
            $ds = $this->os->futureSuplr([
                'stateId'  => $this->os->adminCfg['reservation']['futureSuplr']['deliveringStateId'],
                'days'     => $this->os->adminCfg['reservation']['futureSuplr']['days'],
                'suplrIds' => array_keys($ids),
            ]);
            if ($ds !== false) foreach ($ds as $v)
            {
                $d[$ids[$v['suplrId']]]['future'][$v['dayNo']] = [
                    'deliveryDate' => Tools::sdate($v['deliveryDate']),
                    'itemsNum'     => $v['itemsNum'],
                ];
            }

            $this->r['futureSuplr'] = [
                'days' => $this->os->adminCfg['reservation']['futureSuplr']['days'],
            ];
        }

        $this->r['scl'] = $d;
        $this->r['count'] = count($this->r['scl']);

    }

    public function calculateTyres()
    {
        $this->r['fres'] = false;
        $p1 = (float)$_POST['width'] > 0 ? (float)$_POST['width'] : 0;
        $p2 = (float)$_POST['profile'] > 0 ? (float)$_POST['profile'] : 0;
        $p3 = (float)$_POST['diameter'] > 0 ? (float)$_POST['diameter'] : 0;
        $am = (int)$_POST['amount'];
        $this->db = new DB();
        if (!empty($p1) && empty($p2) && empty($p3))
        {
            $res = $this->db->fetchAll("SELECT DISTINCT p2 FROM tc_sizes WHERE p1='$p1' ORDER BY p2 ASC;");
            if (!empty($res))
            {
                $this->r['fres'] = true;
                $this->r['p2'] = [];
                foreach ($res as $r)
                {
                    $this->r['p2'][] = $r['p2'];
                }
            }
        }
        elseif (!empty($p1) && !empty($p2) && empty($p3))
        {
            $res = $this->db->fetchAll("SELECT DISTINCT p3 FROM tc_sizes WHERE p1='$p1' AND p2='$p2' ORDER BY p3 ASC;");
            if (!empty($res))
            {
                $this->r['fres'] = true;
                $this->r['p3'] = [];
                foreach ($res as $r)
                {
                    $this->r['p3'][] = $r['p3'];
                }
            }
        }
        elseif (!empty($p1) && !empty($p2) && !empty($p3))
        {
            $res = $this->db->getOne("SELECT v1, m1 FROM tc_sizes WHERE p1='$p1' AND p2='$p2' AND p3='$p3';");
            if (!empty($res))
            {
                $this->r['fres'] = true;
                $this->r['typo'] = $p1 . '/' . $p2 . ' R' . $p3;
                $this->r['v1'] = $res['v1'];
                $this->r['m1'] = $res['m1'];
                $this->r['ma'] = $res['m1'] * $am;
                $this->r['va'] = $res['v1'] * $am;
                $this->r['am'] = $am;
            }
        }
    }

}