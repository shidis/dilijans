<? 
require_once '../auth.php';
include('../struct.php');

$cp->frm['name']='subscribe';
$cp->frm['title']='Подписавшиеся пользователи';

$cp->checkPermissions();
$DB = new DB();
$susers_form = $DB->fetchAll("SELECT *, email_id as id, 'form' as dest FROM scr_email INNER JOIN os_order USING (email) ORDER BY scr_email.dt_add DESC;", MYSQL_ASSOC);
$susers_order = $DB->fetchAll("SELECT *, order_id as id, 'order' as dest FROM os_order WHERE subscribe > 0 ORDER BY dt_add DESC;", MYSQL_ASSOC);
$susers = array_merge($susers_form, $susers_order);
cp_head();
cp_css();
cp_js();
?>
<?='
<style type="text/css">
    .subscribe_table th {
        border: 1px solid black;
        padding: 15px 3px;
        background: #8EB4E3;
        text-align: center;
    }

    .subscribe_table td {
        max-width: 250px;
        border: 1px solid black;
        padding: 15px 10px 15px 4px;
    }
    .subscribe_table{
        border-collapse: collapse;
    }
    .paginator{
            margin: 5px 0;
        }
    .paginator li{
        list-style: none;
        display: inline-block;
        margin-right: 15px;
        font-size: 14px;
        font-weight: bold;
    }
</style>
<script language="javascript">
$("document").ready(function(){
	$.ajaxSetup({
		type:"POST",
		global: true,
		cache:false,
		dataType: "json",
//		timeout:1000,
		url: "../be/subscribe.php",
		error: Err
	});
});
function _del(id, table, orderNum){
    $.ajax({
		data: {act:"del",id: id, table: table, orderNum: orderNum},
		success: function (res){
			if(!res.fres) note(res.fres_msg,"stick");
			else {
				$(".subscribe_table #" + id).remove();
			}
		}
	});
}
function csv(){
    $.ajax({
		data: {act:"csv"},
		success: function (res){
			if(!res.fres) note(res.fres_msg,"stick");
			else {
				window.location = res.fname;
			}
		}
	});
}
</script>
'?>



<? cp_body()?>
<? cp_title()?>
<?
    if (empty($_GET['page'])) {
        $page = 1;
    }else $page = (int)$_GET['page'];
    if (!empty($susers)){
        usort($susers, function($e1, $e2){
            if ($e1['dt_add'] > $e2['dt_add']) return -1;
            if ($e1['dt_add'] < $e2['dt_add']) return 1;
            if ($e1['dt_add'] = $e2['dt_add']) return 0;
        });
        ?>
        <fieldset class="ui" style="background-color: <?=Data::get('cms_left_col_bg')?>; margin-bottom: 10px; padding: 5px 0">
            <div style="padding: 0px 15px; position: relative;">
                <div style="float: right; width: 100px; overflow: hidden;">
                    <button class="exportCSV ui-state-default ui-corner-all" onclick="csv()" style="background: url('/cms/img/docs/csv-24.png') no-repeat 50% 50%; width:30px; height: 30px; float: right"></button>
                </div>
            </div>
        </fieldset>
        <?

        Url::parseUrl();
        $paginator=Tools::paginator(Url::$path,Url::$sq,$page,count($susers),50,'page',array(
            'active'=>	'<li class="active">{page}</li>',
            'noActive'=>'<li><a href="{url}">{page}</a></li>',
            'dots'=>	'<li>...</li>'
        ),35);
        $pagi='';
        foreach($paginator as $vv) $pagi.=$vv;

        ?><ul class="paginator"><?=$pagi?></ul><?

        $checked = Array();
        echo '<table class="subscribe_table" cellpadding="0" cellspacing="0"><tr><th>п/п</th><th>Имя клиента</th><th>Email</th><th>Адрес</th><th>Телефон</th>
            <th>Марка авто</th><th>№ заказа</th><th>Наименование товара</th><th>Дата заказа</th><th>Удалить</th></tr>';
        for ($i = (($page - 1) * 50); $i < ((($page - 1) * 50) + 50); $i++) {
            if (!empty($susers[$i])) {
                $su = $susers[$i];
                if (!is_array($su['email'])) {
                    $count = $i+1;
                    $order_info = $DB->fetchAll("SELECT * FROM os_item WHERE order_id = '{$su['order_id']}';", MYSQL_ASSOC);
                    if (!empty($order_info)) {
                        $goods_names = array();
                        foreach ($order_info as $order_name) {
                            $goods_names[] = $order_name['name'];
                        }
                    }
                    echo "<tr id='{$su['id']}'><td>{$count}</td><td>{$su['name']}</td><td>{$su['email']}</td><td>{$su['addr']}</td><td>{$su['tel1']}</td><td>{$su['avto_name']}</td>
                    <td><a href='order_edit.php?order_id={$su['order_id']}'>{$su['order_num']}</a></td><td>" . implode(', ', $goods_names) . "</td><td>{$su['dt_add']}</td><td align='center'><input title='Удалить' type='image' src='../img/row_delete.gif' onclick='_del({$su['id']}, \"{$su['dest']}\", {$su['order_num']});'></td></tr>";
                    $checked[] = $su['email'];
                }
            }
        }
        echo '</table>';
        ?><ul class="paginator"><?=$pagi?></ul><?
    }
?>
<? cp_end()?>
