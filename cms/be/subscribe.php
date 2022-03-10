<?
include_once ('ajx_loader.php');

$cp->setFN('waitlist');
$cp->checkPermissions();

//sleep(1);
function array_cp1251($a)
{
    foreach($a as &$v) $v=@Tools::cp1251($v);
    return $a;
}

$r->fres=true;
$r->fres_msg='';

$act=Tools::esc(@$_REQUEST['act']);

switch ($act){
    case 'del':
        $db=new DB();
        $email_id=(int)@$_REQUEST['id'];
        $order_num=(int)@$_REQUEST['orderNum'];
        $db->query("DELETE FROM scr_email WHERE email_id=$email_id;");
        $db->query("UPDATE os_order SET subscribe = '0' WHERE os_order.order_num =$order_num;");

        break;

    case 'csv':
        if(isset(App_TFields::$fields['os_order']['ptype'])) $ptypeOn=1; else $ptypeOn=0;
        if(isset(App_TFields::$fields['os_order']['method'])) $methodOn=1; else $methodOn=0;
        $cUsers=CU::usersList(array('includeLD'=>1));
        $drivers=CU::usersList(array('driversOnly'=>true));

        // Выборка заказов
        $DB = new DB();
        $susers_form = $DB->fetchAll("SELECT *, email_id as id, 'form' as dest FROM scr_email INNER JOIN os_order USING (email) ORDER BY scr_email.dt_add DESC;", MYSQL_ASSOC);
        $susers_order = $DB->fetchAll("SELECT *, order_id as id, 'order' as dest FROM os_order WHERE subscribe > 0 ORDER BY dt_add DESC;", MYSQL_ASSOC);
        $susers = array_merge($susers_form, $susers_order);
        $out_file_name = '/assets/order_files/export/sub_export_'.date('d_m_y__').time().'.csv';
        $output = fopen($_SERVER['DOCUMENT_ROOT'].$out_file_name, "w");

        $result = array(
            'п/п',
            'Имя клиента',
            'Email',
            'Адрес',
            'Телефон',
            'Марка авто',
            '№ заказа',
            'Наименование товара',
            'Дата заказа'
        );
        fputcsv($output, array_cp1251($result), ';');

        $i = 1;
        foreach($susers as $order) {
            $result = Array();
            $result[] = $i++;
            $result[] = Tools::unesc($order['name']);
            $result[] = Tools::unesc($order['email']);
            $result[] = Tools::unesc($order['addr']);
            $result[] = Tools::unesc(trim($order['tel1'].' '.$order['tel2']));
            $result[] = Tools::unesc($order['avto_name']);
            $result[] = Tools::unesc($order['order_num']);

            $order_info = $DB->fetchAll("SELECT * FROM os_item WHERE order_id = '{$order['order_id']}';", MYSQL_ASSOC);
            if (!empty($order_info)) {
                $goods_names = array();
                foreach ($order_info as $order_name) {
                    $goods_names[] = $order_name['name'];
                }
            }

            $result[] = Tools::unesc(implode(', ', $goods_names));
            $result[] = Tools::sdate($order['dt_add']);

            fputcsv($output, array_cp1251($result), ';');
        }

        fclose($output);
        $r->fres=true;
        $r->fname = $out_file_name;
        break;

    default: $r->fres=false; $r->fres_msg='BAD ACT_CASE '.$act;
}

ajxEnd();