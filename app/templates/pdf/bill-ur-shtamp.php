<style type="text/css">
    body{
        font-family:Arial, Helvetica, sans-serif;
        font-size:9px;
    }
    h1{
        font-size:16px;
        margin-top: 20px;
        text-align: center;
    }
    tbody.header td{
        background-color:#FFC;

    }
    table{
        border-collapse: collapse;
    }
    .htel_sm{
        font-size:14px;
        font-weight: bold;
        text-align: left;
        width: 250px;
    }

</style>
    <table width="100%" border="0" cellspacing="0" cellpadding="2">
        <tr>
            <td><img height="60px" src="https://<?=Cfg::get('site_url')?>/app/templates/images/logo.png"></td>
            <td class="htel_sm" nowrap>Наши телефоны:<br><a href="tel:<?=$tel?>"><strong><?=$tel?></strong></a><br><a href="tel:+<?=$tel2?>"><?=$tel2?></a> <br>(работает только для регионов)</td>
        </tr>
    </table>
<br>
<table width="100%" border="1" cellspacing="0" cellpadding="7" style="margin-top: 15px;">
    <tr>
        <td width="31%"><strong>ИНН <?=$rekvINN?></strong></td>
        <td width="28%"><strong>КПП <?=$rekvKPP?></strong></td>
        <td width="70" rowspan="3" align="center" valign="bottom" nowrap><strong>Сч. №</strong></td>
        <td width="36%" rowspan="3" valign="bottom"><?=$rekvRS?></td>
    </tr>
    <tr>
        <td colspan="2"><strong>Получатель</strong></td>
    </tr>
    <tr>
        <td colspan="2"><?=$rekvLongName?></td>
    </tr>
    <tr>
        <td colspan="2">Банк получателя</td>
        <td align="center" nowrap><strong>БИК</strong></td>
        <td><?=$rekvBIK?></td>
    </tr>
    <tr>
        <td colspan="2"><?=$rekvBank?></td>
        <td align="center" nowrap>Сч. №</td>
        <td><?=$rekvKS?></td>
    </tr>
</table>
<h1>Счет № <?=$orderNum?> от <?=$billDateDots?></h1>
<p>Плательщик: <?=$name.(!empty($INN)?", ИНН {$INN}":'')?></p>
<table width="100%" border="1" cellspacing="0" cellpadding="7">
    <tbody class="header">
    <tr>
        <td align="center">№</td>
        <td>Наименование товара, работ, услуг</td>
        <td align="center">Ед. изм.</td>
        <td align="center">Кол-во</td>
        <td align="center">Цена</td>
        <td align="center">Сумма</td>
    </tr>
    </tbody>


    <?
    $i=0;
    if(!empty($list))
        foreach ($list as $k=>$v){
            $i++; ?>
            <tr>
                <td align="center"><?=$i?></td>
                <td><?=$v['name']?></td>
                <td align="center">шт.</td>
                <td align="center"><?=$v['amount']?></td>
                <td align="center"><?=Tools::nz($v['_price'])?>р.</td>
                <td align="center"><?=Tools::nz($v['_sum'])?>р.</td>
            </tr>
        <? }

    if(!empty($dops))
        foreach($dops as $k=>$v){
            $i++; ?>
            <tr>
                <td align="center"><?=$i?></td>
                <td><?=$v['name']?></td>
                <td align="center">&nbsp;</td>
                <td align="center"><?=$v['amount']?></td>
                <td align="center"><?=Tools::nz($v['_price'])?>р.</td>
                <td align="center"><?=Tools::nz($v['_sum'])?>р.</td>
            </tr>
        <? }

    if(!empty($discount)){
        $i++ ?>
        <tr>
            <td align="center"><?=$i?></td>
            <td>Скидка</td>
            <td align="center">%</td>
            <td align="center"><?=$discount?>%</td>
            <td align="center">&nbsp;</td>
            <td align="center">-<?=Tools::nz($_discountRUR)?>р.</td>
        </tr>
    <? }

    if(!empty($_delivery_cost)){
        $i++ ?>
        <tr>
            <td align="center"><?=$i?></td>
            <td>Доставка</td>
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>
            <td align="center"><?=Tools::nz($_delivery_cost)?>р.</td>
        </tr>
    <? }?>

    <tr>
        <td colspan="5" align="right"><strong>Всего к оплате:</strong></td>
        <td align="center"><strong><?=Tools::nz($_itog)?>р.</strong></td>
    </tr>
</table>
<p>Сумма прописью: <?=$itogPropis?></p>
<p><strong>Внимание!</strong><br>
    <strong>Счет действителен в течении 3х рабочих дней. При заполнении платежного документа указывайте только номер счета по которому производится оплата. <?=$rekvName?> работает по УСН.</strong></p>
<p>&nbsp;</p>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td width="16%">Генеральный директор </td>
        <td width="29%" align="center"><img width="210" src="https://<?=Cfg::get('site_url')?>/app/templates/images/shtamp-ip.png"></td>
        <td width="25%" style="height: 40px; background: url('https://<?=Cfg::get('site_url')?>/app/templates/images/sign-ip.png') no-repeat 0px 26px">_________________________________</td>
        <td width="57%">/<?=$rekvDirector_short?>/</td>
    </tr>
</table>