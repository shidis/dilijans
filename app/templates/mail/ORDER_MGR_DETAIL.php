<!-- наряд заказа. Отправляется только по запросу из админки -->
<html>
<head>
    <title>Заказ <?= $orderNum ?> от <?= $order_date ?></title>

    <style type="text/css">

        BODY, TD, TH {
            font-family: Verdana, Arial, Helvetica, sans-serif;
            font-size: 12px
        }

        H1 {
            font-size: 14px;
            margin: 10px 0px 15px 0px;
        }

        H2 {
            font-size: 12px;
            margin: 20px 0px 10px 0px
        }

        table{
            border-collapse: collapse;
        }

        td {
            vertical-align: top;
        }

        .b-round {
            border: 2px solid #000;
        }

        .b-l {
            border-left: 2px solid #000;
        }

        .b-r {
            border-right: 2px solid #000;
        }

        .b-t {
            border-top: 2px solid #000;
        }

        .b-b {
            border-bottom: 2px solid #000;
        }

        p {
            padding: 0;
            margin: 0 0 10px 0;
        }

        .rt td {
            line-height: 25px;
        }

        .rl td {
            padding: 0 0 5px;
        }
        .rl th{
            padding: 0 0 20px 0;
            text-align: left;
            font-size: 14px;
        }
        .imgs {
            margin: 0;
            padding: 0;
            clear: left;
        }

        .imgs .wrap {
            float:left;
            margin: 0 20px 10px 0;
        }

        .imgs .t {
            display: block;
            margin-bottom: 7px;
            font-size: 11px;
        }
        .hglt {
            margin-left: 15px;
            background: #555;
            padding: 2px 6px;
            color: #ffffff;
            font-weight: bold;
            border-radius: 5px;
        }
    </style>

</head>
<body topmargin="0" marginheight="0">

<table width="690" cellspacing="0" cellpadding="0" border="0">
    <tr>
        <td style="padding-right:15px;">
            <table class="rl">
                <tr>
                    <th>
                        Заказ <?= $orderNum ?> от <?= $order_date ?> <?
                        if($method)  echo '&nbsp;&nbsp;<span class="hglt" style="font-size: 17px">&nbsp;БЕЗНАЛ&nbsp;</span>';
                        if($state_id==14)  echo '&nbsp;&nbsp;<span class="hglt" style="font-size: 17px">&nbsp;КАРТА&nbsp;</span>';
                        ?>
                    </th>
                </tr>
                <tr>
                    <td>
                        <b>Клиент:</b> <?= $name ?>
                    </td>
                </tr>
                <? if (@$ptype == 1 && !empty($person)) { ?>
                    <tr>
                        <td>
                            <b>Контактное лицо:</b> <?= $person ?>
                        </td>
                    </tr>
                <? } ?>
                <? if (!empty($tel1)) { ?>
                    <tr>
                        <td>
                            <b>Тел.:</b> <?= $tel1 . ($tel2 != '' ? " / $tel2" : '') ?>
                        </td>
                    </tr>
                <? } ?>
                <? if (($addr . $city) != '') { ?>
                    <tr>
                        <td>
                            <?= trim($city . ' ' . $addr) ?>
                        </td>
                    </tr>
                <? } ?>
                <? if (!empty($carrier_co)) { ?>
                    <tr>
                        <td>
                            <b>ТК:</b> <?= $carrier_co ?>

                        </td>
                    </tr>
                <? } ?>
                <? if (!empty($passport)) { ?>
                    <tr>
                        <td>
                            <b>Паспорт:</b> <?= $passport ?>
                        </td>
                    </tr>
                <? } ?>
                <? if (!empty($email)) { ?>
                    <tr>
                        <td>
                            <b>И-мейл:</b> <?= $email ?>
                        </td>
                    </tr>
                <? } ?>
            </table>
        </td>
        <td align="right">
            <br>
            <table width="300" cellspacing="0" cellpadding="5" border="1" class="rt">
                <tr>
                    <td height="50">Доставил/дата:<br><?=$driverShortName?></td>
                </tr>
                <tr>
                    <td height="50">Принял:<br><?= $mgr_shortName ?></td>
                </tr>
                <tr>
                    <td height="80">Дата и время доставки:<br><?=$deliveryDate!='00-00-0000'?"{$deliveryDate}<br>":''?><?= $deliveryTime ?></td>
                </tr>
            </table>


        </td>
    </tr>
</table>

<h1>Спецификация</h1>

<table width="690" cellspacing="0" cellpadding="6" border="1">
    <tr>
        <th>№</th>
        <th width="200">Товар</th>
        <th>Кол-во</th>
        <th>Закуп</th>
        <th nowrap><b>Сумма зак.</b></th>
        <th>Продажа</th>
        <th class="b-round"><b>Сумма</b></th>
        <th nowrap>Поставщик/резерв</th>
    </tr>
    <?  $i = 0;
    foreach ($list as $k => $v) {
        $i++; ?>
        <tr>
            <td align="center" height="40" rowspan="2"><?= $i ?></td>
            <td rowspan="2"><?= $v['name'] ?></td>
            <td align="center" rowspan="2"><?= $v['amount'] ?></td>
            <td align="center" rowspan="2"><?=$v['pprice']?></td>
            <td align="center" rowspan="2"><?=$v['psum']?></td>
            <td align="center" rowspan="2"><?= $v['price'] ?></td>
            <td rowspan="2" align="center" class="b-l b-r"><?=$v['sum']?></td>
            <td><?=$v['suplrName']?></td>
        </tr>
        <tr>
            <td><?=$v['reserveNum']?></td>
        </tr>
    <? } ?>
    <? if (!empty($dops)) {
        foreach ($dops as $k => $v) {
            $i++; ?>
            <tr>
                <td align="center" rowspan="2"><?= $i ?></td>
                <td rowspan="2"><?= $v['name'] ?></td>
                <td align="center" rowspan="2"><?= $v['amount'] ?></td>
                <td align="center" rowspan="2"><?= $v['pprice'] ?></td>
                <td align="center" rowspan="2"><?= $v['psum'] ?></td>
                <td align="center" rowspan="2"><?= $v['price'] ?></td>
                <td rowspan="2" align="center" class="b-l b-r"><?= $v['sum'] ?></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
        <? } ?>
    <? } ?>

    <? if(!empty($discount)){
        $i++;?>
        <tr>
            <td align="center"><?= $i ?></td>
            <td colspan="5">Скидка (<?=$discount?>%)</td>
            <td align="center" class="b-l b-r">- <?= $discountRUR ?></td>
            <td>&nbsp;</td>
        </tr>
    <? }?>

    <? if(!empty($_delivery_cost)){
        $i++;?>
        <tr>
            <td align="center"><?= $i ?></td>
            <td colspan="5">Доставка</td>
            <td align="center" class="b-l b-r"><?= $delivery_cost ?></td>
            <td>&nbsp;</td>
        </tr>
    <? }?>

    <tr>
        <td colspan="2">&nbsp;</td>
        <td colspan="2" class="b-l b-t b-b"><strong>Итого закуп:</strong></td>
        <td class="b-r b-t b-b" align="center"><?=$pItog?></td>
        <td class="b-l b-t b-b"><b>Итого:</b></td>
        <td colspan="2" class="b-r b-t b-b">
            <strong><?= $itog ?></strong><?
            ?></td>
    </tr>
</table>

<h2>Комментарий клиента:</h2>

<? if (!empty($avto_name)) { ?><p>Марка авто: <?= $avto_name ?></p><? } ?>
<? if (!empty($checkAvto)) { ?><p>Проверить мой заказ на соответствие моему автомобилю</p><? } ?>
<? if (!empty($info)) { ?><p><?= nl2br($info) ?></p><? } ?>

<? if (!empty($tech)) { ?>
    <h2>Комментарий менеджера:</h2>
    <?= nl2br($tech) ?>
<? } ?>

<? if (@$ptype == 1) { ?>
    <h2>Банковские реквизиты</h2>
    <table cellspacing="0" cellpadding="3" border="0">
        <tr>
            <td>Наименование:</td>
            <td><?= $name ?></td>
        </tr>
        <tr>
            <td nowrap>ИНН / КПП:</td>
            <td><?= $INN . ' / ' . $KPP ?></td>
        </tr>
        <tr>
            <td nowrap>р/с:</td>
            <td><?= $rs ?></td>
        </tr>
        <tr>
            <td>Банк:</td>
            <td><?= $bank ?></td>
        </tr>
        <tr>
            <td>БИК:</td>
            <td><?= $BIK ?></td>
        </tr>
        <tr>
            <td>к/с:</td>
            <td><?= $ks ?></td>
        </tr>
        <tr>
            <td nowrap>Юр. адрес:</td>
            <td><?= $u_addr ?></td>
        </tr>
    </table>
<? } ?>

<? if (!empty($imgs)) { ?>
    <h2>Фото товаров</h2>
    <div class="imgs">
        <? foreach ($imgs as $v) { ?>
            <div class="wrap" style="width: <?=$v['w']+15?>px; height: <?=$v['h']?>px"><div class="t"><?= $v['name'] ?></div><img src="<?= $v['img'] ?>" height="<?=$imgsH-25?>"></div>
        <? } ?>
    </div>
<? }

if(!empty($sign)){?>
    <br><br><br>
    <?=$sign?>
<? }?>

</body>
</html>