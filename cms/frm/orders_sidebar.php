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
            margin:10px;
            background-color:#88B6D9;
        }
        table{
            border-collapse: collapse;
            margin: 5px 0 0 0;
        }
        table td{
            padding: 4px 4px;
        }
        table th{
            text-align: left;
            padding: 7px 0 0;
        }
        .states{
            float: left;
            margin-right: 10px;
        }
    </style>
    <form method="get" target="center" action="orders_c.php" name="form1">
        <input type="hidden" name="markup" value="<?=@$_GET['markup']?>">
        <table width="99%">
            <tr>
                <td width="100%" nowrap>
                    <?
                    /*
                    $isOfficeWork = Tools::isOfficeWorking(Data::get('cc_workHoursArray'), Data::get('cc_workDaysArray'));  
                    switch ($isOfficeWork['status'])
                    {
                        case 'working':
                            ?><img style="float: left; margin-right: 3px;" src="/cms/img/work_icons/rabotaem.gif" border="0" width="20" alt="Сайт работает" title="Сайт работает"><?
                            break;
                        case 'not_working':
                            ?><img style="float: left; margin-right: 3px;" src="/cms/img/work_icons/ne-rabotaem.gif" border="0" width="20" alt="Сайт НЕ работает" title="Сайт НЕ работает"><?
                            break;
                        case 'errors':  
                            ?><img style="float: left; margin-right: 3px;" src="/cms/img/work_icons/oshibka.gif" border="0" width="20" alt="Ошибки в переменных!" title="Ошибки в переменных:<?
                            if (!empty($isOfficeWork['errors']))
                            {
                                echo "\n";
                                foreach ($isOfficeWork['errors'] as $error) echo 'Переменная: '. $error['var'] .', позиция: '. $error['position'] .', сообщение: '.$error['message']."\n";
                            }
                            ?>"><?
                            break;
                    }
                    */
                    ?>
                    Вы: <?=CU::$fullName?>
                    &nbsp;&nbsp;&nbsp;<a href="javascript:;"  onClick="top.location.href='/cms/?logout=1'">&lt;выход&gt;</a></td>
            </tr>
        </table>
        <table width="100%">

            <? if(!empty($os->adminCfg['delivery']['DBF_deliveryDate'])){?>

            <tr>
                <th nowrap>Критерий отбора</th>
            </tr>
            <tr>
                <th>
                    <select name="rangeBy">
                        <option value="">по умолчанию</option><?
                        if(!empty($os->adminCfg['delivery']['DBF_deliveryDate'])){
                            ?><option value="deliveryDate">по дате доставки</option><?
                        }?>
                        <option value="orderDate">по дате заказа</option>
                    </select>
                </th>
            </tr>

            <? }?>

            <tr>
                <th nowrap><a href="#" class="toggle-states">Состояние заказа</a></th>
            </tr>
            <tr>
                <td nowrap>
                    <? foreach ($os->_orderStates as $k=>$v){?>
                        <nobr class="states"><input type="checkbox" value="<?=$k?>"<?=$v['cmsDefaultChk']?' checked':''?> name="state_id[]" id="stid<?=$k?>"><label for="stid<?=$k?>">&nbsp;&nbsp;<?=$v['label']?></label></nobr>
                    <? }?>
                </td>
            </tr>
        </table>
        <input type="submit" style="width: 99%" value="ИСКАТЬ">
        <? $u=CU::usersList(array('os'=>1,'orderBy'=>"disabled,lastName"));
        if(!empty($u)){?>
            <table width="100%">
                <tr>
                    <th>Менеджер</th>
                </tr>
                <tr>
                    <td width="100%"><select name="cUserId" onChange="document.forms['form1'].submit()">
                            <option value="">Все</option>
                            <?
                            $di=0;
                            foreach($u as $k=>$v){
                                if(!empty($v['disabled']) && !$di){
                                    $di=1;
                                    ?><optgroup label="Неактивные"><?
                                }
                                ?><option value="<?=$k?>"<?=CU::$os && CU::$userId==$k?' selected':''?>><?=$v['shortName']?></option><?
                            }
                            if($di) {?></optgroup><? }
                            ?>
                        </select></td>
                </tr>
            </table>
        <? }?>
        <table width="100%">
            <tr>
                <th nowrap>Период с</th>
                <? $os->min_od(); $os->max_od();?>
            </tr>
            <tr>
                <td nowrap><input name="from_d" type="text" id="from_d" size="3" value="<?=date("d",time()-(30*24*60*60))?>">
                    <select name="from_m" id="from_m">
                        <?
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
                    <input name="from_y" type="text" id="from_y" size="5" value="<?=date("Y",time()-(30*24*60*60))?>"></td>
            </tr>
            <tr>
                <th nowrap>по</th>
            </tr>
            <tr>
                <td nowrap><input name="to_d" type="text" id="to_d" size="3" value="<?=date("d")?>">
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
                    <input name="to_y" type="text" id="to_y" size="5" value="<?=date("Y")?>"></td>
            </tr>
            <tr>
                <td nowrap><input tabindex="3" type="button" value="За месяц" onClick="document.forms['form1'].from_d.value='<?=date("d",time()-(30*24*60*60))?>';document.forms['form1'].from_m.value=<?=date("m",time()-(30*24*60*60))?>;document.forms['form1'].from_y.value=<?=date("Y",time()-(30*24*60*60))?>;document.forms['form1'].to_d.value='<?=date("d")?>';document.forms['form1'].to_m.value=<?=date("m")?>;document.forms['form1'].to_y.value=<?=date("Y")?>;document.forms['form1'].submit()">
                    <input tabindex="3" type="button" value="Сегодня" onClick="document.forms['form1'].from_d.value='<?=date("d")?>';document.forms['form1'].from_m.value=<?=date("m")?>;document.forms['form1'].from_y.value=<?=date("Y")?>;document.forms['form1'].to_d.value='<?=date("d")?>';document.forms['form1'].to_m.value=<?=date("m")?>;document.forms['form1'].to_y.value=<?=date("Y")?>;document.forms['form1'].submit()">
            </td></tr>
            <tr><td>
                    <input tabindex="2" type="button" value="За все время" onClick="document.forms['form1'].from_d.value='<?=$os->min_d?>';document.forms['form1'].from_m.value=<?=$os->min_m?>;document.forms['form1'].from_y.value=<?=$os->min_y?>;document.forms['form1'].to_d.value=<?=$os->max_d?>;document.forms['form1'].to_m.value=<?=$os->max_m?>;document.forms['form1'].to_y.value=<?=$os->max_y?>;document.forms['form1'].submit()"></td>
            </td></tr>

        </table>
        <table width="100%">
            <tr>
                <td ><input name="search" type="text" placeholder="поиск по данным клиента (диапазон дат: за все время)" style="width: 99%"></td>
            </tr>
            <tr>
                <td><input name="order_num" type="text" placeholder="поиск по номеру заказа" style="width: 99%"></td>
            </tr>
            <tr>
                <td ><input name="searchItems" type="text" placeholder="поиск по артикулу и названию товара" style="width: 99%"></td>
            </tr>
            <tr>
                <td>
                    <input type="submit" style="width: 100%" value="ИСКАТЬ">
                </td>
            </tr>
        </table>
    </form>

    <? if(!empty($os->adminCfg['purchase']['suplrSelectEnabled']) && !empty($os->adminCfg['delivery']['DBF_deliveryDate']) && false){
        ?><p style="margin: 15px 0 0 6px"><input type="button" value="календарь доставок" style="height: 24px; padding: 0px 10px" onclick="window.open('oshed.php','_blank'); return false;"></p><?
    }?>


    <script language="javascript">

        document.forms['form1'].submit();

        $(document).ready(function()
        {
            $('.toggle-states').click(function ()
            {
                if ($('.states input:checked').length) $('.states input[type=checkbox]').prop('checked', false); else $('.states input[type=checkbox]').prop('checked', true);
                return false;
            }).css({
                'color': 'white',
                'text-decoration': 'none',
                'border-bottom': '1px dashed white'
            });
        })

    </script>

<? cp_end();

