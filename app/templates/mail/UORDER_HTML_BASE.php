<!-- Отправляется при оформлении заказа менеджеру и на коллективный адрес офиса. Тип плательщика: юрик-->
<?
$ss=new Content();
?><html>
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

<table width="650" border="0" cellspacing="0" cellpadding="4">
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
    <? if(!empty($name)){?>
        <tr>
            <td><strong>Покупатель</strong></td>
            <td><?=$name?></td>
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
    <tr>
        <td><strong>ИНН</strong></td>
        <td><?=@$INN?></td>
    </tr>
    <tr>
        <td><strong>КПП</strong></td>
        <td><?=@$KPP?></td>
    </tr>
    <tr>
        <td><strong>р/с</strong></td>
        <td><?=@$rs?></td>
    </tr>
    <tr>
        <td><strong>Банк</strong></td>
        <td><?=@$bank?></td>
    </tr>
    <tr>
        <td><strong>к/с</strong></td>
        <td><?=@$ks?></td>
    </tr>
    <tr>
        <td><strong>БИК</strong></td>
        <td><?=@$BIK?></td>
    </tr>
    <tr>
        <td><strong>Юр. адрес</strong></td>
        <td><?=@$u_addr?></td>
    </tr>
    <tr>
        <td><strong>Контактное лицо</strong></td>
        <td><?=@$person?></td>
    </tr>
    <? if(!empty($cUserId)){?>
        <tr>
            <td nowrap><strong>Персональный менеджер</strong> &nbsp;</td>
            <td><?="$mgr_FullName (e-mail: {$mgr_email})"?></td>
        </tr>
    <? }?>
</table>

<hr><H2>Спецификация</H2>

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

<table width="650"  border="0" cellpadding="5" cellspacing="0" class="tt">
    <tr>
        <th nowrap="nowrap">№<br />п/п</th>
        <th nowrap="nowrap">Код товара</th>
        <th width="60%" align="left" nowrap="nowrap">Наименование товара</th>
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
<br />
<? if(!empty($info)){?><h2>Комментарий</h2><p><?=$info?></p><? }?>
</body>
</html>
