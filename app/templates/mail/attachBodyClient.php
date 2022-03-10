<!-- Используется в качестве тела письма при отправке договора/счета вложением -->
<html>
<body>
<style type="text/css">
    BODY,TD,TH{
        font-family:Verdana, Arial, Helvetica, sans-serif; font-size:11px
    }
    H2{
        font-size:14px; margin: 5px 0px 5px 0px
    }
</style>
<a href="https://www.dilijans.org"><img height="40" alt="Дилижанс" src="https://www.dilijans.org/app/templates/images/logo.png"></a>
<h2>Здравствуйте, <?=$ptype==0 && !empty($name) ? $name : ($ptype==1 && !empty($person) ? $person :'уважаемый покупатель')?>!</h2>

<h3>Номер Вашего заказа: <?=$orderNum?> от <?=$billDateRus?></h3>
<div style="background: #FFF; padding: 10px; border: #990000 solid 2px; margin: 0px auto 15px auto; width: 80%;">
    <p><span style="color: #990000; font-size: x-large; background-color: #ffffff;">
            <? if(isset($docType[1]) && isset($docType[2])){?>
                Счет и договор находятся в приложенных к этому письму файлах.
            <? }elseif(isset($docType[1])){?>
                Счет находится в приложенном к этому письму файле.
            <? }elseif(isset($docType[2])){?>
                Договор находится в приложенном к этому письму файле.
            <? }?>
        </span>
    </p>

    <p><span style="color: #990000; background-color: #ffffff;">Чтобы ускорить отгрузку вашего заказа, сообщите, пожалуйста, об оплате в произвольной форме, ответив на это письмо или сообщив по телефону <a href="tel:<?=$tel?>"><?=$tel?></a> или <a href="tel:+<?=$tel2?>"><?=$tel2?></a> (работает только для регионов) или на электронную почту <a href="mailto:<?=$emailInfo?>?subject=По поводу заказа <?=$orderNum?> от <?=$billDateRus?>"><?=$emailInfo?></a> (можно просто ответить на это письмо). Ваш персональный менеджер &ndash;
            <strong><?=$mgr_FullName?>.</strong></span></p>

    <p><span style="color: #990000; background-color: #ffffff;">Время работы магазина - <?=$vr?>.</span></p>

    <? if($ptype==0){?>
        <p>Способы оплаты для плательщиков-физлиц описаны в этом письме ниже.</p>
    <? }?>

</div>
<p>Вы заказали следующие товары в магазине &laquo;Дилижанс&raquo; <a href="https://www.dilijans.org">www.dilijans.org</a></p>
<table class="tt" border="1" cellpadding="5" style="border-collapse: collapse">
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

<? if($ptype==0 && isset($docType[1])){?>
    <p>Оплата этого счета – стандартная процедура, похожая на оплату коммунальных платежей.</p>
    <p><strong>Оплатить можно следующим образом:</strong></p>
    <ol>
        <li>В любом удобном банке заполните квитанцию по реквизитам в таблице ниже. Если у вас есть принтер – просто распечатайте приложенный к письму счет и подойдите к сотруднику банка с паспортом и распечаткой. Все вопросы по заполнению квитанции можно решить, обратившись к сотруднику банка.
        </li>

        <li>Для оплаты через электронный банк-клиент просто заполните форму (по реквизитам, указанным ниже) в разделе «перевод в другой банк» или «оплата счета» (формулировка фразы может меняться в зависимости от банка)</li>
    </ol>

    <p><strong>Реквизиты для оплаты счета:</strong></p>

    <table border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td width="329">
                <p>Наименование получателя платежа:</p>
            </td>
            <td width="432">
                <?=$rekvLongName?>
            </td>
        </tr>
        <tr>
            <td width="329">
                ИНН получателя платежа:
            </td>
            <td width="432">
                <?=$rekvINN?>
            </td>
        </tr>
        <tr>
            <td width="329">
                Банк получателя:
            </td>
            <td width="432">
                <?=$rekvBank?>
            </td>
        </tr>
        <tr>
            <td width="329">
                Расчетный счет получателя:
            </td>
            <td width="432">
                <?=$rekvRS?>
            </td>
        </tr>
        <tr>
            <td width="329">
                Корр. счет банка получателя (в ОПЕРУ Москва):
            </td>
            <td width="432">
                <?=$rekvKS?>
            </td>
        </tr>
        <tr>
            <td width="329">
                БИК банка получателя:
            </td>
            <td width="432">
                <?=$rekvBIK?>
            </td>
        </tr>
        <tr>
            <td colspan="2" width="761">
                <p>В назначении платежа укажите: &laquo;оплата по счету  <?=$orderNum?> от <?=$billDateRus?> НДС не облагается.&raquo;</p>
            </td>
        </tr>
    </table>
    <p><strong>Чтобы ускорить отгрузку:</strong></p>
    <p>Сообщите нам об оплате в произвольной форме по телефону <a href="tel:<?=$tel?>"><?=$tel?></a> <?=$vr?> по рабочим дням или на электронную почту <a href="mailto:<?=$emailInfo?>?subject=По поводу заказа <?=$orderNum?> от <?=$billDateRus?>"><?=$emailInfo?></a> (можно просто ответить на это письмо).</p>
    <p><br><strong>После оплаты:</strong></p>
    <p>1. Мы отгрузим ваш заказ в течение 1-2х дней с момента поступления оплаты на расчетный счет фирмы.
    <p>2. Если доставка нужна по России, Казахстану, Белоруссии, Армении и Киргизии – согласуйте с вашим менеджером способ отправки вашего товара (через транспортную компанию, почту или другим способом).</p>
    <p>3. Доставка по Москве обеспечивается нашей курьерской службой. Пожелания о времени и месте доставки также озвучивайте вашему менеджеру.</p>
    <p>Оригиналы документов будут переданы вместе с товаром.</p>
    <p>&nbsp;</p>

<? }else{ // юр лицо?>
    <p><br><strong>После оплаты:</strong></p>
    <p>1. Мы отгрузим ваш заказ в течение 1-2х дней с момента поступления оплаты на расчетный счет фирмы.
    <p>2. Если доставка нужна по России, Казахстану, Белоруссии, Армении и Киргизии – согласуйте с вашим менеджером способ отправки вашего товара (через транспортную компанию, почту или другим способом).</p>
    <p>3. Доставка по Москве обеспечивается нашей курьерской службой. Пожелания о времени и месте доставки также озвучивайте вашему менеджеру.</p>

    <p>Внимание! Наша компания работает по УСН. Оригиналы документов будут переданы вместе с товаром (при самовывозе или
        доставке по Москве) или высланы почтой (при отправке в регионы).</p>

<? }?>

<p><strong>Если остались вопросы:</strong></p>
<ol>
    <li>Обращайтесь к вашему менеджеру:<br><?=$mgr_FullName?>, <a href="tel:<?=$tel?>"><?=$tel?></a>, <a href="tel:+<?=$tel2?>"><?=$tel2?></a> (работает только для регионов) <?=$vr?> по московскому времени</li>
    <li>Подробно об условиях доставки можно ознакомиться <strong><a href="https://www.dilijans.org/i/dostavka.html">здесь</a>.</strong></li>
    <li>Претензии и благодарности отправляйте на почту нашему директору: <a href="mailto:<?=$emailInfo?>?subject=Письмо руководителю по заказу <?=$orderNum?> от <?=$billDateRus?>"><?=$emailInfo?></a></li>
</ol>
<p>&nbsp;</p>
<p><strong>Адрес нашего офиса:</strong></p>
<p><?=$rekvAddrPost?> <?=$vr?>.</p>
<p>(обращаем внимание, что товар в офисе не представлен)</p>
<p><img src="https://www.dilijans.org/app/templates/images/karta.png" alt="" width="400" height="279"/>&nbsp;</p>

<p>&nbsp;</p>
<table border="0" cellpadding="5" cellspacing="5">
    <tr>
        <td></td>
        <td><p><strong>С уважением, интернет-магазин Dilijans.org</strong></p>
            <p><a href="https://www.dilijans.org">https://www.dilijans.org</a></p>
            <p><a href="tel:<?=$tel?>"><?=$tel?></a></p>
            <p><strong>Спасибо за покупку!</strong></p>
            <p><img height="40" alt="Дилижанс" src="https://www.dilijans.org/app/templates/images/logo.png"></p>
        </td>
    </tr>
</table>

</body>
</html>