<? require_once '../auth.php';
include('../struct.php');

$cp->frm['name']='orders_sidebar';

$cp->checkPermissions();

cp_head();
cp_css();
cp_js();

cp_body();

$os=new App_Orders();

$os->initOrderStatesByUser();

foreach ($_GET as $key=>$value) $$key=$value;
foreach ($_POST as $key=>$value) $$key=$value;
?>
<style type="text/css">
	body{
		color:#FFF;
		margin:0; 
		background-color:#88B6D9;
	}
    table{
        border-collapse: collapse;
        margin: 5px 0 0 10px;
    }
    table td{
        padding: 0 5px;;
    }
</style>
    <form method="get" target="center" action="orders_c.php" name="form1">
        <table width="99%">
            <tr>
                <td nowrap><strong>Период с</strong></td>
                <td nowrap>
                    <input name="from_d" type="text" id="from_d" size="5" value="<?=date("d",time()-(30*24*60*60))?>">
                    <select name="from_m" id="from_m"><?
                        $m=date("m",time()-(30*24*60*60));
                        ?>
                        <option value="1" <?=$m==1?'selected':''?>>Январь</option>
                        <option value="2" <?=$m==2?'selected':''?>>Февраль</option>
                        <option value="3" <?=$m==3?'selected':''?>>Март</option>
                        <option value="4" <?=$m==4?'selected':''?>>Апрель</option>
                        <option value="5" <?=$m==5?'selected':''?>>Май</option>
                        <option value="6" <?=$m==6?'selected':''?>>Июнь</option>
                        <option value="7" <?=$m==7?'selected':''?>>Июль</option>
                        <option value="8" <?=$m==8?'selected':''?>>Август</option>
                        <option value="9" <?=$m==9?'selected':''?>>Сентябрь</option>
                        <option value="10" <?=$m==10?'selected':''?>>Октябрь</option>
                        <option value="11" <?=$m==11?'selected':''?>>Ноябрь</option>
                        <option value="12" <?=$m==12?'selected':''?>>Декабрь</option>
                    </select>
                    <input name="from_y" type="text" id="from_y" size="10" value="<?=date("Y",time()-(30*24*60*60))?>"></td>
                <td><strong>по</strong></td>
                <td nowrap><input name="to_d" type="text" id="to_d" size="5" value="<?=date("d")?>">
                    <select name="to_m" id="to_m">
                        <option value="1" <?=date("m")==1?'selected':''?>>Январь</option>
                        <option value="2" <?=date("m")==2?'selected':''?>>Февраль</option>
                        <option value="3" <?=date("m")==3?'selected':''?>>Март</option>
                        <option value="4" <?=date("m")==4?'selected':''?>>Апрель</option>
                        <option value="5" <?=date("m")==5?'selected':''?>>Май</option>
                        <option value="6" <?=date("m")==6?'selected':''?>>Июнь</option>
                        <option value="7" <?=date("m")==7?'selected':''?>>Июль</option>
                        <option value="8" <?=date("m")==8?'selected':''?>>Август</option>
                        <option value="9" <?=date("m")==9?'selected':''?>>Сентябрь</option>
                        <option value="10" <?=date("m")==10?'selected':''?>>Октябрь</option>
                        <option value="11" <?=date("m")==11?'selected':''?>>Ноябрь</option>
                        <option value="12" <?=date("m")==12?'selected':''?>>Декабрь</option>
                    </select>
                    <input name="to_y" type="text" id="to_y" size="10" value="<?=date("Y")?>"></td>
                <td nowrap>
                    <input tabindex="3" type="button" value="За месяц" onClick="document.forms['form1'].from_d.value='<?=date("d",time()-(30*24*60*60))?>';document.forms['form1'].from_m.value=<?=date("m",time()-(30*24*60*60))?>;document.forms['form1'].from_y.value=<?=date("Y",time()-(30*24*60*60))?>;document.forms['form1'].to_d.value='<?=date("d")?>';document.forms['form1'].to_m.value=<?=date("m")?>;document.forms['form1'].to_y.value=<?=date("Y")?>;document.forms['form1'].submit()">
                </td>
                <td nowrap>
                    <input tabindex="3" type="button" value="Сегодня" onClick="document.forms['form1'].from_d.value='<?=date("d")?>';document.forms['form1'].from_m.value=<?=date("m")?>;document.forms['form1'].from_y.value=<?=date("Y")?>;document.forms['form1'].to_d.value='<?=date("d")?>';document.forms['form1'].to_m.value=<?=date("m")?>;document.forms['form1'].to_y.value=<?=date("Y")?>;document.forms['form1'].submit()">
                </td>
                <? $os->min_od(); $os->max_od();?>
                <td nowrap>
                    <input tabindex="2" type="button" value="За все время" onClick="document.forms['form1'].from_d.value='<?=$os->min_d?>';document.forms['form1'].from_m.value=<?=$os->min_m?>;document.forms['form1'].from_y.value=<?=$os->min_y?>;document.forms['form1'].to_d.value=<?=$os->max_d?>;document.forms['form1'].to_m.value=<?=$os->max_m?>;document.forms['form1'].to_y.value=<?=$os->max_y?>;document.forms['form1'].submit()">
                </td>
                <td align="right" width="100%">Вы: <?=CU::$fullName?>&nbsp;&nbsp;&nbsp;<a href="javascript:;"  onClick="top.location.href='/cms/?logout=1'">&lt;выход&gt;</a></td>
            </tr>
        </table>
        <table>
            <tr>
                <?
                $u=CU::usersList(array('os'=>1));

                if(!empty($u)){
                    ?>
                    <td nowrap><strong>Менеджер</strong></td>
                    <td nowrap><select name="cUserId" onchange="document.forms['form1'].submit()"><option value="">все</option> <?
                        foreach($u as $k=>$v){
                            ?><option value="<?=$k?>"<?=CU::$os && CU::$userId==$k?' selected':''?>><?=$v['shortName']?></option><?
                        }
                        ?></select></td><?
                }?>
                <td nowrap><strong>Состояние заказа</strong></td>
                <td>
                    <? foreach ($os->_orderStates as $k=>$v){?>
                        <nobr><input onchange="document.forms['form1'].submit()" type="checkbox" value="<?=$k?>"<?=$v['cmsDefaultChk']?' checked':''?> name="state_id[]" id="stid<?=$k?>">&nbsp;<label for="stid<?=$k?>"><?=$v['label']?></label></nobr>&nbsp;&nbsp;&nbsp;
                    <? }?>
                    </select>
                </td>
            </tr>
        </table>
        <table>
            <tr>
                <td style="padding-right: 15px;"><input name="search" type="text" placeholder="поиск по данным клиента (диапазон дат: за все время)" style="width: 350px"></td>
                <td><input name="order_num" type="text" placeholder="поиск по номеру заказа" style="width: 140px"></td>
                <td><input name="searchItems" type="text" placeholder="поиск по артикулу и названию товара" style="width: 280px"></td>
                <td style="padding-left: 20px;"><input tabindex="1" type="submit" value="ИСКАТЬ!"></td>
            </tr>
        </table>
    </form>
    <script language="javascript">document.forms['form1'].submit()</script>

<? cp_end();

