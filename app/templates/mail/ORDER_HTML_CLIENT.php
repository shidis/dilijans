<!-- Отправляется клиенту при оформлении заказа. Тип плательщика: физик -->
<?
$ss=new Content();
?>
<html>
<head>
    <title>Заказ № <?=$orderNum?>  на сайте <?=$siteName?></title>
    <style type="text/css">
        BODY,TD,TH{
            font-family:Verdana, Arial, Helvetica, sans-serif; font-size:11px
        }
        H2{
            font-size:14px; margin: 5px 0px 5px 0px
        }
        .tt{
            border-collapse: collapse;
        }
        .tt td, .tt th{
            border: 1px solid black;
        }
        .itog td{
            border:0;
        }
    </style>
</head>
<body topmargin="0" marginheight="0">

<a href="https://samohodoff.ru"><img height="40" alt="Дилижанс" src="http://<?=Cfg::get('site_url').'/app/templates/images/logo.png'?>" title="Дилижанс" style="float: right;"></a>
<p>Здравствуйте, <?=$ptype==0 && !empty($name) ? $name : ($ptype==1 && !empty($person) ? $person :'уважаемый покупатель')?>!</p>
<p> Вы разместили  заказ  в интернет-магазине "Дилижанс" <a href="https://<?=Cfg::get('site_url')?>/"><?=Cfg::get('site_name')?></a></p>
<p>Наш телефон: <a href="tel:<?=$tel?>"><strong><?=$tel?></strong></a> или <a href="tel:+<?=$tel2?>"><?=$tel2?></a> (работает только для регионов)</p>
<p>Электронная почта <a href="mailto:<?=$emailInfo?>?subject=По поводу заказа <?=$orderNum?> от <?=$billDateRus?>"><?=$emailInfo?></a></p>
<p><b>Заказ принят в обработку, и после подтверждения наличия товара на складе наш менеджер свяжется с вами по телефону.</b></p>
<p><b>Обратите внимание!</b> График работы нашего офиса <?=$vr?>. В случае если заказ отправлен в нерабочее время, наш сотрудник выйдет с вами на связь утром следующего рабочего дня.</p>


<h2>Ваш заказ № <?=@$orderNum?> от <?=$order_date?> принят.</h2>

<hr><table width="650" border="0" cellspacing="0" cellpadding="4">
    <tr>
        <td width="122"><strong>№ заказа</strong></td>
        <td width="464"><?=$orderNum?></td>
    </tr>
    <tr>
        <td><strong>Метод оплаты</strong></td>
        <td><?=@App_TFields::$fields['os_order']['method']['varList'][$method]?></td>
    </tr>
    <tr>
        <td width="122"><strong>Дата и время заказа</strong></td>
        <td width="464"><?=$order_dt?></td>
    </tr>
    <? if(!empty($name)){?>
        <tr>
            <td><strong>Имя</strong></td>
            <td><?=$name?></td>
        </tr>
    <? }?>
    <? if(!empty($email)){?>
        <tr>
            <td><strong>E-mail</strong></td>
            <td><?=$email?></td>
        </tr>
    <? }?>
    <? if(!empty($tel1)){?>
        <tr>
            <td><strong>Телефон</strong></td>
            <td><?=$tel1?></td>
        </tr>
    <? }?>
    <? if(!empty($city)){?>
        <tr>
            <td nowrap><strong>Город доставки</strong></td>
            <td><?=$city?></td>
        </tr>
    <? }?>
    <? if(!empty($addr)){?>
        <tr>
            <td nowrap><strong>Адрес доставки</strong></td>
            <td><?=$addr?></td>
        </tr>
    <? }?>
    <? if(!empty($deliveryTime)){?>
        <tr>
            <td nowrap><strong>Желаемое время доставки</strong></td>
            <td><?=$deliveryTime?></td>
        </tr>
    <? }?>
    <? if(!empty($carrier_co)){?>
        <tr>
            <td nowrap><strong>Транспортная компания</strong></td>
            <td><?=$carrier_co?></td>
        </tr>
    <? }?>
    <? if(!empty($cUserId)){?>
        <tr>
            <td nowrap><strong>Ваш персональный менеджер</strong> &nbsp;</td>
            <td><?="$mgr_FullName (e-mail: {$mgr_email})"?></td>
        </tr>
    <? }?>
</table>

<hr><H2>Спецификация</H2>

<? if(!empty($quickOrderNotif)){?>
    <p><b>Оформлен быстрый заказ.</b> Необходимо согласование товарных позиций и контактных данных с сотрудником нашей компании. Ожидайте телефонного звонка!</p>
<? }?>

<? if(!empty($avto_name)){?>
    <table width="650" border="0" cellspacing="6" cellpadding="0">
        <tr>
            <td width="124" nowrap><strong>Марка автомобиля</strong></td>
            <td width="462"><?=$avto_name?></td>
        </tr>
        <? if(!empty($checkAvto)){?>
            <tr>
                <td colspan="2"><strong>Проверить мой заказ на соответствие моему автомобилю </strong></td>
            </tr>
        <? }?>
    </table>
    <br>
<? }?>

<br>
<table  border="0" cellpadding="5" cellspacing="0" class="tt">
    <tr>
        <th nowrap="nowrap">№<br />п/п</th>
        <th nowrap="nowrap">Код товара</th>
        <th align="left" nowrap="nowrap">Наименование товара</th>
        <th nowrap="nowrap">Цена, руб</th>
        <th nowrap>Кол-во</th>
        <th nowrap="nowrap">Стоимость, руб</th>
    </tr>
    <? $i=0;
    if(!empty($list)) {
        foreach ($list as $k=>$v){
            $i++; ?>
            <tr><td align="center"><?=$i?></td>
            <td align="center"><?=$v['cat_id']?></td>
            <td><?=($v['spez']?'[спеццена] ':'')."<a href=\"{$v['turl']}\">{$v['name']}</a>"?></td>
            <td align="center"><?=$v['price']?></td>
            <td align="center"><?=$v['amount']?></td>
            <td align="center"><?=$v['sum']?></td>
            </tr><?
        }
    }
    if(!empty($dops)){
        foreach($dops as $dk=>$dv){
            $i++; ?>
            <tr>
            <td align="center"><?=$i?></td>
            <td align="center">-</td>
            <td><?=$dv['name']?></td>
            <td align="center"><?=$dv['price']?></td>
            <td align="center"><?=$dv['amount']?></td>
            <td align="center"><?=$dv['sum']?></td>
            </tr><?
        }
    }

    if(!empty($discount)){
        $i++ ?>
        <tr>
            <td align="center"><?=$i?></td>
            <td align="center">-</td>
            <td>Скидка</td>
            <td align="center">-</td>
            <td align="center"><?=$discount?>%</td>
            <td align="center">-<?=$discountRUR?></td>
        </tr>
    <? }

    if(!empty($delivery_cost)){
        $i++ ?>
        <tr>
            <td align="center"><?=$i?></td>
            <td align="center">-</td>
            <td>Доставка</td>
            <td align="center">-</td>
            <td align="center">-</td>
            <td align="center"><?=$delivery_cost?></td>
        </tr>
    <? }

    ?>
    <tr><td colspan="6" align="right"><table border="0" cellspacing="0" cellpadding="5" class="itog">
                <tr>
                    <td nowrap="nowrap" id="red"><strong>К оплате</strong></td>
                    <td align="right" nowrap="nowrap" id="red"><strong><?=$itog?> руб</strong></td>
                </tr>

            </table></td>
    </tr>
</table>
<br /><?

if(!empty($info)){?><h2>Ваш комментарий к заказу</h2><p><?=$info?></p><? }?>

<? if(!empty($sign)){?>
    <br><br><br>
    <?=$sign?>
<? }?>
<p>&nbsp;</p><p>&nbsp;</p>
<p><strong>С уважением, компания &laquo;Дилижанс&raquo;</strong></p>
<p><a href="https://www.dilijans.org">https://www.dilijans.org</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://www.dilijans.org/i/contacts.html">Контакты</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://www.dilijans.org/i/about.html">Информация о компании</a></p>
<p><strong>Спасибо за оказанное доверие!</strong></p>
</body>
</html>
