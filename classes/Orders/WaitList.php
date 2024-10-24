<?

class Orders_WaitList extends CommonStatic
{


    public static function push($r)
    {
        /* r=array(
            reqEmail :bool
            reqUserName:bool
            reqTel:bool
            regLifeTime:bool
            email:string    !
            tel:string      !
            name:string     !
            userId:int
            lifeTime:int    !
            cat_id:int  req !
            am:int      req !
            comment:string
        */

        $db = new CC_Base();

        $r['email'] = @$r['email'];
        $r['userName'] = @$r['name'];
        $r['comment'] = @$r['comment'];
        $r['tel'] = @$r['tel'];
        $r['days_lifeTime'] = (int)@$r['lifeTime'];
        $r['userId'] = (int)@$r['userId'];
        $r['cat_id'] = (int)@$r['cat_id'];
        $r['am'] = abs((int)@$r['am']);

        unset($r['name'], $r['lifeTime']);

        $emailValid=Tools::emailValid($r['email']);
        if (!empty($r['reqEmail']) && !$emailValid || empty($r['reqEmail']) && !empty($r['email']) && !$emailValid) static::putMsg(false, 'Укажите, пожалуйста, корректный адрес электронной почты', 1);
        if (!empty($r['reqUserName']) && empty($r['userName'])) static::putMsg(false, 'Вы не указали свое имя'.$r['userName'], 1);
        if (!empty($r['reqTel']) && empty($r['tel'])) static::putMsg(false, 'Не указан контактный телефон', 1);
        if (!empty($r['regLifeTime']) && @$r['days_lifeTime']<1) static::putMsg(false, 'Определите максимальное время хранения заявки (дней)', 1);
        if (@$r['am']<1) static::putMsg(false, 'Укажите количество товара', 1);

        if (!static::$fres) return false;

        if (empty($r['cat_id'])) {
            static::putMsg(false, "Не предвиденная ошибка (1). Приносим свои извинения за неудобства",1);
            Log_Sys::put(SLOG_ERROR, "Orders_WaitList", "cat_id пустой");
        }

        $r['email'] = trim(Tools::esc($r['email']));
        $r['userName'] = trim(Tools::esc($r['userName']));
        $r['tel'] = trim(Tools::esc($r['tel']));
        $r['comment'] = trim(Tools::esc($r['comment']));

        $r['userIP'] = @$_SERVER['REMOTE_ADDR'];
        $r['userIP'] = array("INET_ATON('{$r['userIP']}')", 'noquot');
        $r['dt_added'] = date("Y-m-d H:i:s");

        $d = $db->fetchAll("SELECT * FROM os_waitList WHERE cat_id='{$r['cat_id']}' AND (tel!='' AND tel LIKE '{$r['tel']}' OR email!='' AND email LIKE '{$r['email']}')");
        if (count($d)) return static::putMsg(false, 'Пользователь с таким емейлом или телефоном уже оставил заявку на уведомление для этого товара',1);

        $r['tname'] = '';
        $db->que('cat_by_id', $r['cat_id']);
        if ($db->qnum()) {
            $db->next();
            $r['gr'] = $db->qrow['gr'];
            $r['tname'] = $db->qrow['bname'] . ' ' . $db->qrow['name'];
            switch ($r['gr']) {
                case 1:
                    $r['tname'] .= trim(Tools::cutDoubleSpaces("{$db->qrow['P3']}/{$db->qrow['P2']} " . ($db->qrow['P6'] ? 'Z' : '') . "R{$db->qrow['P1']} {$db->qrow['P7']} {$db->qrow['csuffix']}"));
                    break;
                case 2:
                    $r['tname'] .= trim(Tools::cutDoubleSpaces("{$db->qrow['P2']}Jx{$db->qrow['P5']} {$db->qrow['P4']}/{$db->qrow['P6']} ET{$db->qrow['P1']} D{$db->qrow['P3']} {$db->qrow['csuffix']}"));
                    break;
            }
        } else{
            Log_Sys::put(SLOG_ERROR, "Orders_WaitList", "Товар с cat_id={$r['cat_id']} не найден");
            return static::putMsg(false, "Непредвиденная ошибка (2). Приносим свои извинения за неудобства",1);
        }

        unset($r['reqEmail'], $r['reqUserName'], $r['reqTel'], $r['regLifeTime']);

        $db->insert('os_waitList', $r);
        return true;
    }

    public static function check($gr)
    {
        $r = array();

        $gr = (int)$gr;

        $db = new DB();

        $sclimit = (int)Data::get('zero_sklad_limit');
        if ($sclimit <= 0) $sclimit = 0;

        if (($wlc = Cfg::_get('waitList')) == 1) {
            // на почту
            $m = trim(Data::get('mail_robot'));
            if ($m == '' || mb_strpos($m, '@') === false) $m = 'no-reply@' . str_replace('www.', '', $_SERVER['SERVER_NAME']);
            $r['fromAddr'] = $m;
            $r['fromName'] = Cfg::get('site_name');
            $r['toAddr'] = Data::get('mail_order');
            $r['toName'] = trim(Data::get('mail_order_name'));
            $r['charset'] = Data::get('order_mail_charset');
            $r['host'] = Data::get('mail_robot_host');
            $r['logpw'] = Data::get('mail_robot_logpw');
            $r['debug'] = 0;
            $r['SMTPSecure'] = Data::get('mail_robot_smtp_secure');
            $r['tpl'] = 'mail/WAITLIST_BASE.php';


            $d = $db->fetchAll("SELECT cc_cat.sc, cc_cat.cat_id, os_waitList.id, os_waitList.dt_added, os_waitList.days_lifeTime, os_waitList.userName, os_waitList.email, os_waitList.tel, os_waitList.am, os_waitList.comment, os_waitList.tname FROM os_waitList JOIN cc_cat USING (cat_id) JOIN cc_model USING (model_id) JOIN cc_brand USING (brand_id) WHERE cc_cat.sc >= '$sclimit' AND os_waitList.am < cc_cat.sc AND NOT cc_cat.LD AND NOT cc_model.LD AND NOT cc_brand.LD AND DATEDIFF(NOW(),os_waitList.dt_added)<=days_lifeTime AND cc_cat.gr={$gr} AND os_waitList.noticed=0");


            foreach ($d as $dv) {
                $r['body'] = array();
                $r['body']['siteName'] = $r['fromName'];
                $r['body']['cat_id'] = $dv['cat_id'];
                $r['body']['sc'] = $dv['sc'];
                $r['body']['dt_added'] = Tools::sDateTime($dv['dt_added']);
                $r['body']['days'] = $dv['days_lifeTime'];
                $r['body']['uname'] = Tools::html($dv['userName']);
                $r['body']['email'] = Tools::html($dv['email']);
                $r['body']['tel'] = Tools::html($dv['tel']);
                $r['body']['am'] = Tools::html($dv['am']);
                $r['body']['comment'] = nl2br(Tools::html($dv['comment']));
                $r['body']['tname'] = ($gr == 1 ? 'Шина' : 'Диск') . ' ' . Tools::html($dv['tname']);
                $r['body']['turl'] = 'http://' . Cfg::get('site_url') . ($gr == 1 ? App_SUrl::tTipo($dv['cat_id']) : App_SUrl::dTipo($dv['cat_id']));

                if (!preg_match('|([a-z0-9_\.\-]{1,20})@([a-z0-9\.\-]{1,20})\.([a-z]{2,4})|is', $dv['email'])) $r['replyToAddr'] = ''; else $r['replyToAddr'] = $dv['email'];

                if (!preg_match("/[a-zабвгдеёжзиклмнопрстуфхцчшщъыьэюя_\-\.]+/iu", $dv['userName'])) $r['replyToName'] = ''; else $r['replyToName'] = $dv['userName'];

                $r['title'] = $r['subject'] = "Уведомление о поступлении на склад " . ($gr == 1 ? 'шины' : 'диска') . " [{$dv['cat_id']}]";

                Mailer::sendmail($r);

                $db->update('os_waitList', array('noticed' => 1, 'dt_noticed' => Tools::dt()), "id={$dv['id']}");

            }

        } else {
            // в новый заказ
            $d = $db->fetchAll("SELECT cc_cat.sc, cc_cat.cat_id, cc_cat.scprice, cc_cat.cprice, os_waitList.id, os_waitList.dt_added, os_waitList.days_lifeTime, os_waitList.userName, os_waitList.email, os_waitList.tel, os_waitList.am, os_waitList.comment, os_waitList.tname FROM os_waitList JOIN cc_cat USING (cat_id) JOIN cc_model USING (model_id) JOIN cc_brand USING (brand_id) WHERE cc_cat.sc >= '$sclimit' AND os_waitList.am < cc_cat.sc AND NOT cc_cat.LD AND NOT cc_model.LD AND NOT cc_brand.LD AND DATEDIFF(NOW(),os_waitList.dt_added)<=days_lifeTime AND cc_cat.gr={$gr} AND os_waitList.noticed=0");


            $orders = new App_Orders();

            foreach ($d as $dv) {
                $r = array();
                $ri = array();
                $r['order_num'] = $orders->newOrderNum();
                $r['dt_add'] = Tools::dt();
                if(isset(App_TFields::$fields['os_order']['source'])) $r['source'] = 2;
                $r['tech'] = "[Заявка на уведомление о поступлении товара была размещена клиентом " . Tools::sdate($dv['dt_added']). " сроком на {$dv['days_lifeTime']} дней. Сейчас товар на складе.]";
                $r['name'] = $dv['userName'];
                $r['email'] = $dv['email'];
                $r['tel1'] = $dv['tel'];
                $r['delivery_cost'] = $orders->getDeliveryCost();
                if ($dv['scprice']) $ri['price'] = $dv['scprice']; else $ri['price'] = $dv['cprice'];
                $ri['cat_id'] = $dv['cat_id'];
                $ri['gr'] = $gr;
                $ri['name'] = $dv['tname'];
                $ri['amount'] = $dv['am'];
                $r['bcost'] = $ri['price'] * $ri['amount'];
                $r['cost'] = $r['bcost'] + $r['delivery_cost'];
                if ($orders->insert('os_order', $r)) {
                    $order_id = $orders->lastId();
                    $ri['order_id'] = $order_id;
                    $orders->insert('os_item', $ri);
                }
                $db->update('os_waitList', array('noticed' => 1, 'comment'=>"Размещен заказ № {$r['order_num']}",  'dt_noticed' => Tools::dt()), "id={$dv['id']}");
            }
        }

        unset($db, $orders);

    }

    /*
     * создает заказ для заявки
     * возращает array(order_id, order_num) созданного заказа или false если ошибка
     */
    public static function createOrder($id)
    {
        $db=new CC_Base();
        $id=(int)$id;
        $dv=$db->getOne("SELECT *, INET_NTOA(userIP) AS ip FROM os_waitList WHERE id='$id'");
        if($dv===0){
            return static::putMsg(false,"[Orders_WaitList.createOrder()]:: Не найдена заявка ID=$id");
        }
        $db->que('cat_by_id',$dv['cat_id']);
        if($db->qnum()){
            $db->next();
            $dc=$db->qrow;
        }
        $o = new App_Orders();
        $r = array();
        $ri = array();
        $o->initOrderStatesByUser();
        if(empty($o->_orderStates[0])){
            return static::putMsg(false, "[Orders_WaitList.createOrder()]:: Не настроена опция NEXT для текущего пользователя. Заказ не создан.");
        }
        $r['state_id'] = $o->orderStates[0]['next'];
        $r['cUserId']=CU::$userId;
        $r['ip']=$dv['ip'];
        $r['LD']=2;
        $r['order_num'] = $o->newOrderNum();
        $r['dt_add'] = Tools::dt();
        if(isset(App_TFields::$fields['os_order']['source'])) $r['source'] = 2;
        $r['tech'] = "[Заказ создан в ручном режиме по заявке посетителя сайта на уведомление об отсутсвующем товаре. Время заявки ".Tools::sdate($dv['dt_added']).", срок  ".$dv['days_lifeTime']." дней.]";
        $r['name'] = $dv['userName'];
        $r['email'] = $dv['email'];
        $r['tel1'] = $dv['tel'];
        $r['delivery_cost'] = $o->getDeliveryCost();
        $ri['gr'] = $dv['gr'];
        $ri['name'] = $dv['tname'];
        $ri['amount'] = $dv['am'];
        if(!empty($dc)){
            if ($dc['scprice']) $ri['price'] = $dc['scprice']; else $ri['price'] = $dc['cprice'];
            $ri['cat_id'] = $dc['cat_id'];
            $r['bcost'] = $ri['price'] * $dv['am'];
            $r['cost'] = $r['bcost'] + $r['delivery_cost'];
        }
        if ($o->insert('os_order', $r)) {
            $order_id = $o->lastId();
            $ri['order_id'] = $order_id;
            $o->insert('os_item', $ri);
            $db->update('os_waitList', array('noticed' => 5, 'comment'=>"Размещен заказ № {$r['order_num']} /".CU::$shortName.'/', 'dt_noticed' => Tools::dt()), "id=$id");
        }else{
            return static::putMsg(false, "[Orders_WaitList.createOrder()]:: Ошибка БД при добавлении заказа.");
        }

        return [
            'order_id'=>$order_id,
            'order_num'=>$r['order_num']
        ];
    }



    /*
     * для CMS. Получение списка заявок.
     *
     * actual   int {
     *      0 | empty - все,
     *      1 - актуальные заявки (актуальность по daysLifeTime определяется),
     *      -1 - не актуальные
     * }
     *
     * по заявке можно делать заказ в ручную, в этом случае noticed==5, иначе noticed==1 - оповещенные, 0 - не оповещенные
     * noticed: array() | int {
     *      -1 - не оповещенные
     *      1 - оповещененные
     *      2 - заказы сделанные по заявке вручную
     * }
     * start, limit
     * gr - {1,2}
     * sortBy   string | по умолчанию - по убыванию dt_added
     *
     * Выводим заявки без джойна к cc_cat&cc_model&cc_brand, но для каждой актуальной заяки делается запрос по наличию
     *
     * Выходные параметры = {
     *      total: int  всего заявок в выборке
     *      data: array заявки
     * }
     * или false
     */
    public static function olist($r=array())
    {
        $w=[];
        if(!empty($r['gr'])) $w[]="gr=".(int)$r['gr'];

        if(!empty($r['actual']))
            if($r['actual']==1) $w[]="DATEDIFF(NOW(),os_waitList.dt_added)<=days_lifeTime";
            elseif($r['actual']==-1) $w[]="DATEDIFF(NOW(),os_waitList.dt_added)>days_lifeTime";

        $n=[];
        if(!empty($r['noticed'])){
            if(!is_array($r['noticed'])) $r['noticed']=[$r['noticed']];
            if(in_array(-1, $r['noticed'])) $n[]=0;
            if(in_array(1, $r['noticed'])) $n[]=1;
            if(in_array(2, $r['noticed'])) $n[]=5;
        }
        if(!empty($n)) $w[]="noticed IN (".implode(',', $n).")";

        $w=implode(' AND ',$w);

        if(!empty($r['limit'])) {
            $start = (int)$r['start'];
            $limit = (int)$r['limit'];
        }

        if(empty($r['sortBy'])) $sortBy='dt_added DESC'; else $sortBy=$r['sortBy'];

        $af = App_TFields::DBselect('os_waitList', 'all', (int)$r['gr']);

        $sql="SELECT id, gr, cat_id, userId, dt_added, days_lifeTime, userName, email, tel, am, comment, noticed, dt_noticed, tname, INET_NTOA(userIP) AS userIP, UNIX_TIMESTAMP(dt_added) AS dtAddedTS, IF(DATEDIFF(NOW(),os_waitList.dt_added)<=days_lifeTime, 1, 0) AS actual $af FROM os_waitList".(!empty($w)?" WHERE $w":'').(" ORDER BY $sortBy").(!empty($limit)?(" LIMIT $start, $limit"):'');

        $r=[];
        $r['sql']=$sql;

        $db=new DB();
        $cc=new CC_Base();
        if(!empty($limit)){
            $d=$db->getOne("SELECT count(*) FROM os_waitList".(!empty($w)?" WHERE $w":''));
            $r['total']=$d[0];
        }

        $d=$db->fetchAll($sql, MYSQLI_ASSOC);
        foreach($d as &$v){
            $v['userName']=Tools::unesc($v['userName']);
            $v['comment']=Tools::unesc($v['comment']);
            $v['tname']=Tools::unesc($v['tname']);
            if($v['gr']==1) $v['gr_']='Шины'; else $v['gr_']="Диски";
            $v['dt_added']=Tools::sDateTime($v['dt_added']);
            $v['dt_noticed']=Tools::sDateTime($v['dt_noticed']);

            if($v['noticed']!=0) $v['actual']=0;

            if($v['actual']) {
                $cc->que('cat_by_id', $v['cat_id'], 1); // скрытые не участвуют
                if ($cc->qnum()) {
                    $cc->next();
                    $v['sc'] = $cc->qrow['sc'];
                    $v['price'] = $cc->qrow['cprice'];
                    if($v['gr']==1) $v['turl']=App_SUrl::tTipo(0, $cc->qrow); else $v['turl']=App_SUrl::dTipo(0, $cc->qrow);
                } else {
                    $v['sc'] = -1;
                    if($v['gr']==1)  $v['turl']=App_SUrl::tTipo($v['cat_id']); else $v['turl']=App_SUrl::dTipo($v['cat_id']);
                }
            }else{
                if($v['gr']==1)  $v['turl']=App_SUrl::tTipo($v['cat_id']); else $v['turl']=App_SUrl::dTipo($v['cat_id']);
            }
        }
        $r['data']=$d;

        if(empty($limit)) $r['total']=count($d);

        unset($cc,$db,$d);

        return $r;
    }

    /*
     * удалить заявку
     */
    public static function del($id)
    {
        $id=(int)$id;
        $db=new DB();
        $db->query("DELETE FROM os_waitList WHERE id='$id'");
        return $db->unum();
    }

}

