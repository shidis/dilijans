<? $ym=new App_CC_Dataset_TorgMail();?>

<div class="dsTabs" style="">
    <ul>
        <li><a href="#dstab-0">Общие свойства</a></li>
        <li><a href="#dstab-1">Свойства шин</a></li>
        <li><a href="#dstab-2">Свойства дисков</a></li>
    </ul>
    <div id="dstab-0">
        <table cellpadding="10">
            <tr>
                <td width="33%"><label>
                        <?=$ym->dataFields['shopName']['info']?>
                    </label>
                    <input type="input" name="shopName" /></td>
                <td width="33%"><label>
                        <?=$ym->dataFields['company']['info']?>
                    </label>
                    <input type="input" name="company" /></td>
                <td width="33%"><label>
                        <?=$ym->dataFields['shopUrl']['info']?>
                    </label>
                    <input type="input" name="shopUrl" value="https://<?=Cfg::get('site_url')?>" /></td>
            </tr>
            <tr>
                <td><label>
                        <?=$ym->dataFields['SCMin']['info']?>
                    </label>
                    <input type="input" name="SCMin" /></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </table>
    </div>
    <div id="dstab-1">
        <table border="0" cellspacing="0" cellpadding="10">
            <tr>
                <td width="30%"><label>
                        <?=$ym->dataFields['categoryPrefix']['info']?>
                    </label>
                    <input type="input" name="categoryPrefix[1]" /></td>
                <td width="15%"><label>
                        <?=$ym->dataFields['deliveryType']['info']?>
                    </label>
                    <select name="deliveryType[1]">
                        <option value="1">да</option>
                        <option value="0">нет</option>
                    </select></td>
                <td width="30%"><label>
                        <?=$ym->dataFields['deliveryPrice']['info']?>
                    </label>
                    <input type="input" name="deliveryPrice[1]" /></td>
                <td width="30%"><label>
                        <?=$ym->dataFields['MPC']['info']?>
                    </label>
                    <input type="input" name="MPC[1]" /></td>
            </tr>
            <tr>
                <td colspan="4"><label>
                        <?=$ym->dataFields['urlSuffix']['info']?>
                    </label>
                    <input type="input" name="urlSuffix[1]" value="" /></td>
            </tr>
            <tr>
                <td colspan="4"><label>
                        <?=$ym->dataFields['description']['info']?>
                    </label>
                    <textarea name="description[1]"></textarea></td>
            </tr>
            <tr>
                <td colspan="4"></td>
            </tr>
        </table>
    </div>
    <div id="dstab-2">
        <table border="0" cellspacing="0" cellpadding="10">
            <tr>
                <td width="30%"><label>
                        <?=$ym->dataFields['categoryPrefix']['info']?>
                    </label>
                    <input type="input" name="categoryPrefix[2]" /></td>
                <td width="30%"><?=$ym->dataFields['categoryReplicaPrefix']['info']?>
                    </label>
                    <input type="input" name="categoryReplicaPrefix" /></td>
                <td width="30%"><label>
                        <?=$ym->dataFields['deliveryPrice']['info']?>
                    </label>
                    <input type="input" name="deliveryPrice[2]" /></td>
            </tr>
            <tr>
                <td><label>
                        <?=$ym->dataFields['MPC']['info']?>
                    </label>
                    <input type="input" name="MPC[2]" /></td>
                <td><label>
                        <?=$ym->dataFields['deliveryType']['info']?>
                    </label>
                    <select name="deliveryType[2]">
                        <option value="1">да</option>
                        <option value="0">нет</option>
                    </select></td>
                <td><label>
                        <?=$ym->dataFields['urlSuffix']['info']?>
                    </label>
                    <input type="input" name="urlSuffix[2]" value="" /></td>
            </tr>
            <tr>
                <td colspan="3"><label>
                        <?=$ym->dataFields['description']['info']?>
                    </label>
                    <textarea name="description[2]"></textarea></td>
            </tr>
            <tr>
                <td colspan="3"></td>
            </tr>
        </table>
    </div>
</div>
<fieldset class="ui" style="padding:15px">
    <legend>Особенности</legend>
    <p>***) В файл выгрузки попадут только размеры с ненулевой ценой</p>
</fieldset>
