<? $ym=new App_CC_Dataset_YM();?>
<div class="dsTabs" style="">
    <ul>
        <li><a href="#dstab-0">Общие свойства</a></li>
        <li><a href="#dstab-1">Свойства шин</a></li>
        <li><a href="#dstab-2">Свойства дисков</a></li>
    </ul>
    <div id="dstab-0">
        <table cellpadding="10">
            <tr>
                <td width="33%"><label><?=$ym->dataFields['shopName']['info']?></label><input type="input" name="shopName" /></td>
                <td width="33%"><label><?=$ym->dataFields['company']['info']?></label><input type="input" name="company" /></td>
                <td width="33%"><label><?=$ym->dataFields['shopUrl']['info']?></label><input type="input" name="shopUrl" value="https://<?=Cfg::get('site_url')?>" /></td>
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
                <td width="30%"><label>
                        <?=$ym->dataFields['modelPrefix']['info']?>
                    </label>
                    <input type="input" name="modelPrefix[1]" /></td>
                <td width="30%"><label>
                        <?=$ym->dataFields['typePrefix']['info']?>
                    </label>
                    <input type="input" name="typePrefix[1]" /></td>
                <td><label>
                        <?=$ym->dataFields['pickup']['info']?>
                    </label>
                    <select name="pickup[1]">
                        <option value="1">да</option>
                        <option value="0">нет</option>
                    </select></td>
            </tr>
            <tr>
                <td><label>
                        <?=$ym->dataFields['local_delivery_cost']['info']?>
                    </label>
                    <input type="input" name="local_delivery_cost[1]" /></td>
                <td><label>
                        <?=$ym->dataFields['country_of_origin']['info']?>
                    </label>
                    <input type="input" name="country_of_origin[1]" /></td>
                <td><label>
                        <?=$ym->dataFields['manufacturer_warranty']['info']?>
                    </label>
                    <select name="manufacturer_warranty[1]">
                        <option value="0">нет</option>
                        <option value="1">да</option>
                    </select></td>
                <td><label>
                        <?=$ym->dataFields['delivery']['info']?>
                    </label>
                    <select name="delivery[1]">
                        <option value="1">да</option>
                        <option value="0">нет</option>
                    </select></td>
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
                <td colspan="4"><label>
                        <?=$ym->dataFields['sales_notes']['info']?>
                    </label>
                    <textarea name="sales_notes[1]"></textarea></td>
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
                <td width="30%"><label>
                        <?=$ym->dataFields['modelPrefix']['info']?>
                    </label>
                    <input type="input" name="modelPrefix[2]" /></td>
                <td width="30%"><label>
                        <?=$ym->dataFields['typePrefix']['info']?>
                    </label>
                    <input type="input" name="typePrefix[2]" /></td>
                <td><label>
                        <?=$ym->dataFields['pickup']['info']?>
                    </label>
                    <select name="pickup[2]">
                        <option value="1">да</option>
                        <option value="0">нет</option>
                    </select></td>
                <td><label>
                        <?=$ym->dataFields['delivery']['info']?>
                    </label>
                    <select name="delivery[2]">
                        <option value="1">да</option>
                        <option value="0">нет</option>
                    </select></td>
            </tr>
            <tr>
                <td><label>
                        <?=$ym->dataFields['local_delivery_cost']['info']?>
                    </label>
                    <input type="input" name="local_delivery_cost[2]" /></td>
                <td><label>
                        <?=$ym->dataFields['country_of_origin']['info']?>
                    </label>
                    <input type="input" name="country_of_origin[2]" /></td>
                <td><label>
                        <?=$ym->dataFields['categoryReplicaPrefix']['info']?>
                    </label>
                    <input type="input" name="categoryReplicaPrefix" /></td>
                <td><label>
                        <?=$ym->dataFields['manufacturer_warranty']['info']?>
                    </label>
                    <select name="manufacturer_warranty[2]">
                        <option value="1">да</option>
                        <option value="0">нет</option>
                    </select></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td colspan="5"><label>
                        <?=$ym->dataFields['urlSuffix']['info']?>
                    </label>
                    <input type="input" name="urlSuffix[2]" value="" /></td>
            </tr>
            <tr>
                <td colspan="5"><label>
                        <?=$ym->dataFields['description']['info']?>
                    </label>
                    <textarea name="description[2]"></textarea></td>
            </tr>
            <tr>
                <td colspan="5"><label>
                        <?=$ym->dataFields['sales_notes']['info']?>
                    </label>
                    <textarea name="sales_notes[2]"></textarea></td>
            </tr>
        </table>

    </div>
</div>

<fieldset class="ui" style="padding:15px"><legend>Особенности</legend>
    <p>***) В файл выгрузки попадут только размеры с ненулевой ценой</p>
</fieldset>
