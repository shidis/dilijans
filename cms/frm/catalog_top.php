<? 
require_once '../auth.php';
include('../struct.php');

$cp->frm['name']='catalog_top';

$cp->checkPermissions();

require_once($_SERVER['DOCUMENT_ROOT'].'/cms/inc/utils.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" type="text/css" media="screen" href="/cms/themes/redmond/ui-custom.css" />
<link rel="stylesheet" type="text/css" media="screen" title="basic" href="/cms/css/ui.css" />
<SCRIPT language=JavaScript src="/assets/js/jquery.min.js" type=text/javascript></SCRIPT>
<SCRIPT language=JavaScript src="/cms/js/ax_global.js" type=text/javascript></SCRIPT>
<script type="text/javascript" src="/cms/js/lib/jquery-ui.custom.min.js"></script>
<SCRIPT language=JavaScript src="/assets/js/func.lib.js" type=text/javascript></SCRIPT>
<SCRIPT language=JavaScript src="/cms/js/jquery.ext.js" type=text/javascript></SCRIPT>
<SCRIPT language=JavaScript src="/cms/js/jquery.ui.ext.js" type=text/javascript></SCRIPT>
<SCRIPT language=JavaScript src="/cms/js/ui.js" type=text/javascript></SCRIPT>

<style type="text/css">
	body{
		color:#FFF;
		margin:5px 0 0 0; 
		background-color:#88B6D9;
	}
	table td{
		font-size:12px;
		padding-left:10px;
	}
	
</style>

<script type="text/javascript">

	$(document).ready(function(){
		
		//$('select').chosen();
		
        var $form=$('[name=form1]');
        var form1=$form.get(0);
		
		var loader=$('body').cloader();
		
		$('[name=dataset_id]').change(function(){
//			form1.action='catalog_top.php'; 
//			form1.target='_self';
//			loader.cloader('show');
//			form1.submit();
			if($(this).val()>0){
				$('#inDSonly').show('slow');
				$('label[for=inDSonly]').show('slow');
			}else{
				$('#inDSonly').hide('slow');
				$('label[for=inDSonly]').hide('slow');
			}
		});

		$('.refresh').click(function(){
			form1.action='catalog_top.php';
			form1.target='_self';
			loader.cloader('show');
			form1.submit('self');
            return false;
		});

        $('[name=form1]').submit(function(e){
            if(e!='self'){
                form1.action='catalog_bot.php';
                form1.target='catalog_bot';
            }
        });

        $('.reset').click(function(){
            form1.reset();
            $form.find('input:checkbox').prop('checked',false);
            $form.find('select').each(function()
            {
               $(this).val($(this).find('option:first').val());
            });
            $('[name=dataset_id]').change();
            return false;
        })
	});
</script>

</head>
<body>
<?
$gr=@$_GET['gr'];
if($gr!=1 && $gr!=2) {warn('Неверный вызов. Группа товаров не задана.'); exit();}

$cc=new CC_Ctrl();

$dataset_id=@$_REQUEST['dataset_id'];
$ds=new CC_Dataset();
$ds->d=$ds->datasetList(array('gr'=>$gr));

if($gr==1)
    $cc->cat_view(array(
        'gr'=>$gr,
        'ex'=>1,
        'exFields'=>['brand','P1','P2','P3','P123','MP1','MP2','MP3','P6','P7','P4','mTags'],
    //	'datasetTo'=>'model',
    //	'dataset_id'=>$dataset_id,
        'nolimits'=>1,
        'H'=>0,
    ));
else
    $cc->cat_view(array(
        'gr'=>$gr,
        'ex'=>1,
        //	'datasetTo'=>'model',
        //	'dataset_id'=>$dataset_id,
        'nolimits'=>1,
        'H'=>0,
    ));

$cc->load_sup($gr);

$mTags=[];
if(!empty($cc->ex_arr['mTags']))
{
    $mTags=$cc->getTagsByIds(array_keys($cc->ex_arr['mTags']));
}
// Поставщики
$sp = new CC_Base();
$suplrs = $sp->suplrList([]);
$db = new DB();
foreach ($suplrs as $sup_id => $sup){
    if (empty($_GET['scNotZero'])) {
        @$_GET['scNotZero'] = 0;
    }
    $spl_cids = $db->fetchAll("SELECT cat_id FROM cc_cat_sc WHERE suplr_id = '$sup_id' AND sc > '{$_GET['scNotZero']}' ", MYSQL_ASSOC);
    $spl_cids_array = Array();
    foreach ($spl_cids as $s) {
        $spl_cids_array[] = "'" . $s['cat_id'] . "'";
    }
    if (!empty($spl_cids)) {
        $aq[$sup_id] = '(cc_cat.cat_id IN (' . implode(',', $spl_cids_array) . '))';
        $count = $db->fetchAll("SELECT count(cc_cat.cat_id) as quantity FROM cc_cat INNER JOIN (cc_model JOIN cc_brand ON cc_model.brand_id = cc_brand.brand_id) ON cc_cat.model_id = cc_model.model_id WHERE NOT cc_cat.LD AND NOT cc_model.LD AND NOT cc_brand.LD AND cc_cat.sc > 0 AND cc_cat.gr = $gr  AND $aq[$sup_id]", MYSQL_ASSOC);
        if ($count[0]['quantity']==0) {
            unset ($suplrs[$sup_id]);
        }
    } else {
        unset ($suplrs[$sup_id]);
    }
}
?>
<form method="get" name="form1">
<input name="gr" value="<?=$gr?>" type="hidden">
<?


if ($gr=='1'){?>
    <table cellpadding="0" cellspacing="1">
        <tr valign="middle">
            <td colspan="9"><select name="dataset_id"><option value="0">Без набора данных</option><?
                    foreach($ds->d as $v){
                        ?><option<?=$v['dataset_id']==$dataset_id?' selected':''?> value="<?=$v['dataset_id']?>"><?=Tools::unesc($v['name'])." (".$ds->classes[$v['class']]['name'].")"?></option><?
                    }
                    ?></select>
                <select name="MP2_1">
                    <option value="">тип авто</option>
                    <option value="0" <?=(@$_GET['MP2_1']==='0'?'selected':'')?>>Не указан</option>
                    <option value="1" <?=(@$_GET['MP2_1']=='1'?'selected':'')?>>Легковой</option>
                    <option value="2" <?=(@$_GET['MP2_1']=='2'?'selected':'')?>>Внедорожник</option>
                    <option value="3" <?=(@$_GET['MP2_1']=='3'?'selected':'')?>>Микроавтобус</option>
                    <option value="4" <?=(@$_GET['MP2_1']=='4'?'selected':'')?>>Легковой/внедорожник</option>
                </select>
                <? if(!empty($mTags)){
                    $group='';
                    ?><select name="mTag"><option value="">теги моделей</option><?
                    foreach($mTags as $v)
                    {
                        if($group!=$v['tag_group_id']){
                            if($group!='') echo '</optgroup>';
                            $group=$v['tag_group_id'];
                            ?><optgroup label="<?=$v['groupName']?>"><?
                        }
                        ?><option value="<?=$v['tag_id']?>"><?=$v['name']?></option><?
                    }
                    if($group!='') echo '</optgroup>';
                    ?></select><?
                }
                ?>
            <input name="search" type="text" id="search" style="width:300px" placeholder="Модель/суффикс/<?=in_array(Cfg::get('CAT_IMPORT_MODE'),array(1,3))?'код TI':'ID'?>">
            </td>
        </tr>
        <tr valign="middle">
            <td width="57">Ширина:</td>
            <td width="54"><select name="P3_1">
                    <option value="">Все</option><?
                    ksort($cc->ex_arr['P3']);
                    foreach($cc->ex_arr['P3'] as $k=>$v) echo "<option".(isset($_GET['P3_1']) && $_GET['P3_1']===$k?' selected':'')." value=\"{$k}\">{$k}</option>";
                    ?></select></td>
            <td width="56">Высота:</td>
            <td width="54"><select name="P2_1">
                    <option value="">Все</option><?
                    ksort($cc->ex_arr['P2']);
                    foreach($cc->ex_arr['P2'] as $k=>$v) echo "<option".(isset($_GET['P2_1']) && $_GET['P2_1']===$k?' selected':'')." value=\"{$k}\">{$k}</option>";
                    ?></select></td>
            <td width="12">R</td>
            <td width="85"><select name="P1_1">
                    <option value="">Все</option><?
                    ksort($cc->ex_arr['P1']);
                    foreach($cc->ex_arr['P1'] as $k=>$v) echo "<option".(isset($_GET['P1_1']) && $_GET['P1_1']===$k?' selected':'')." value=\"{$k}\">{$k}</option>";
                    ?></select></td>
            <td colspan="3" rowspan="2">
                <nobr>
                    <select name="scNotZero" id="scNotZero">
                        <option value="-1"<?=@$_GET['scNotZero']?' selected':''?>>Все</option>
                        <option value="1"<?=@$_GET['scNotZero']?' selected':''?>>1</option>
                        <option value="4"<?=@$_GET['scNotZero']?' selected':''?>>4</option>
                        <option value="8"<?=@$_GET['scNotZero']?' selected':''?>>8</option>
                        <option value="12"<?=@$_GET['scNotZero']?' selected':''?>>12</option>
                    </select>
                    <label for="scNotZero">есть на складе</label>
                </nobr>
                <nobr><input name="scZero" type="checkbox" id="scZero"  value="1"<?=@$_GET['scZero']?' checked':''?>><label for="scZero">нет на складе</label></nobr>
                <nobr><input name="scpriceNotZero" type="checkbox" id="scpriceNotZero"  value="1"<?=@$_GET['scpriceNotZero']?' checked':''?>><label for="scpriceNotZero">с спец. ценой</label></nobr>
                <? if(in_array(Cfg::get('CAT_IMPORT_MODE'),array(1,3))){?><nobr><input name="showTI" type="checkbox" id="showTI"  value="1"<?=@$_GET['showTI']?' checked':''?>><label for="showTI">код <strong>T</strong>yre<strong>I</strong>ndex</label></nobr><? }?>
                <nobr><input name="zeroBprice" type="checkbox" id="zeroBprice"  value="1"<?=@$_GET['zeroBprice']?' checked':''?>><label for="zeroBprice">только с ценой = 0</label></nobr>
                <nobr><input name="not_zeroBprice" type="checkbox" id="not_zeroBprice"  value="1"<?=@$_GET['not_zeroBprice']?' checked':''?>><label for="not_zeroBprice">только с ценой</label></nobr>
                <br>
                <nobr><input name="onlyH" id="onlyH" value="1" type="checkbox"<?=@$_GET['onlyH']?' checked':''?>><label for="onlyH">только скрытые</label></nobr>
                <nobr><input name="inDSonly" id="inDSonly" value="1" type="checkbox"<?=@$_GET['inDSonly']?' checked':''?><?=!@$_GET['dataset_id']?' style="display:none"':''?>><label<?=!@$_GET['dataset_id']?' style="display:none"':''?> for="inDSonly">только набор</label></nobr>
                <nobr><input name="fotoExists" id="fotoExists" type="checkbox" value="1"<?=@$_GET['fotoExists']?' checked':''?>><label for="fotoExists">есть фото </label></nobr>
                <nobr><input name="fix_price" id="fixPrice" type="checkbox" value="1"<?=@$_GET['fixPrice']?' checked':''?>><label for="fixPrice">фиксЦена </label></nobr>
                <nobr><input name="fix_sc" id="ignoreUpdate" type="checkbox" value="1"<?=@$_GET['fixSc']?' checked':''?>><label for="ignoreUpdate">фиксКол-во</label></nobr>
                <nobr><input name="is_balances" id="is_balances" type="checkbox" value="1"<?=@$_GET['is_balances']?' checked':''?>><label for="is_balances">Остатки</label></nobr>
                <nobr><input name="is_not_balances" id="is_not_balances" type="checkbox" value="1"<?=@$_GET['is_not_balances']?' checked':''?>><label for="is_not_balances">Не остатки</label></nobr>
                <nobr><input name="is_not_updated_month" id="is_not_updated_month" type="checkbox" value="1"<?=@$_GET['is_not_updated_month']?' checked':''?>><label for="is_not_updated_month">Не обновлялись месяц</label></nobr>
                <?if (!empty($suplrs)):?>
                    <br><select name="suplrs_filter" id="suplrs_filter">
                        <option value="">Поставщики</option>
                        <?
                            foreach ($suplrs as $sid => $sup){
                                echo '<option value="'.$sid.'">'.$sup['name'].'</option>';
                            }
                        ?>
                    </select>
                <?endif;?>
            </td>
        </tr>
        <tr valign="middle">
            <td nowrap>Сезон:
                <? if(isset($cc->ex_arr['MP3'][1])){?><br><input name="MP3_1" type="checkbox" id="MP3_1" value="1"<?=@$_GET['MP3_1']?' checked':''?>> шипы<? }?>
            </td>
            <td><select name="MP1_1">
                    <option value="">Все</option><?
                    if(isset($cc->ex_arr['MP1'][1])){?><option<?=@$_GET['MP1_1']==1?' selected':''?> value="1">Лето</option><? }
                    if(isset($cc->ex_arr['MP1'][2])){?><option<?=@$_GET['MP1_1']==2?' selected':''?> value="2">Зима</option><? }
                    if(isset($cc->ex_arr['MP1'][3])){?><option<?=@$_GET['MP1_1']==3?' selected':''?> value="3">Всесезон</option><? }
                    ?></select></td>
            <td>Ис/Ин:</td>
            <td><select name="P7_1">
                    <option value="---">Все</option><?
                    ksort($cc->ex_arr['P7']);
                    foreach($cc->ex_arr['P7'] as $k=>$v) echo "<option".(isset($_GET['P7_1']) && $_GET['P7_1']===$k?' selected':'')." value=\"{$k}\">{$k}</option>";
                    ?></select></td>
            <td>ZR</td>
            <td>
                <input name="P6_1" type="checkbox"<?=!isset($cc->ex_arr['P6'][1])?' disabled':''?> value="1"<?=@$_GET['P6_1']?' checked':''?>>
                &nbsp;C <input name="P4_1" type="checkbox"<?=!isset($cc->ex_arr['P4'][1])?' disabled':''?> value="1"<?=@$_GET['P4_1']?' checked':''?>></td>
        </tr>
        <tr valign="middle">
            <td colspan="2"><select name="brand_id_1" id="brand_id_1">
                    <option value="">Производитель шины</option><?
                    if(count($cc->ex_arr['brand'][0])){
                        foreach($cc->ex_arr['brand'][0] as $k=>$v)
                            echo'<option value="'.$k.'"'.($v['H']!=0?' class="isH"':'').(@$_GET['brand_id_1']==$k?' selected':'').">{$v['name']} ({$v['amount']})</option>";
                    }
                    ?>
                </select></td>
            <td align="left" colspan="2">
                <select name="P123_1">
                    <option value="">типоразмер</option>
                    <? ksort($cc->ex_arr['P123'], SORT_STRING);
                    foreach($cc->ex_arr['P123'] as $k=>$v){
                        $t=explode('-',$k);
                        ?><option<?=(isset($_GET['P123_1']) && $_GET['P123_1']===$k?' selected':'')?> value="<?=$k?>"><?="{$t[2]} / {$t[1]}&nbsp;&nbsp;&nbsp;R{$t[0]}"?></option><?
                    }?>
                </select>
            </td>
            <td colspan="2"><input type="submit" value="Искать &gt;&gt;&gt;"></td>
            <td nowrap>Выводить  по <input name="lines" type="text" id="lines" style="width:30px; text-align:center" maxlength="4" value="<?=isset($_GET['lines'])?$_GET['lines']:50?>"> строк</td>
            <td  nowrap colspan="1"><input class="refresh" type="submit" value="Обновить форму"><input class="reset" type="submit" value="Сбросить форму"></td>
        </tr>
    </table>


<? }elseif ($gr=='2'){?>
    <table border="0" cellpadding="0" cellspacing="2">
        <tr>
            <td colspan="4"><select name="dataset_id"><option value="0">Без набора данных</option><?
                    foreach($ds->d as $v){
                        ?><option<?=$v['dataset_id']==$dataset_id?' selected':''?> value="<?=$v['dataset_id']?>"><?=Tools::unesc($v['name'])." (".$ds->classes[$v['class']]['name'].")"?></option><?
                    }
                    ?></select></td>
            <td>Replica</td>
            <td><select name="replica">
                    <option value="">не важно</option>
                    <option value="1">только реплика</option>
                    <option value="0">без реплики</option>
                </select></td>
            <td colspan="3"><input name="search" type="text" id="search" placeholder="Модель/цвет/<?=in_array(Cfg::get('CAT_IMPORT_MODE'),array(1,3))?'код TI':'ID'?>" style="width:100%"></td>
        </tr>
        <tr>
            <td width="35">J</td>
            <td><select name="P2_2">
                    <option value="">Все</option><?
                    ksort($cc->ex_arr['P2']);
                    foreach($cc->ex_arr['P2'] as $k=>$v) echo "<option".(isset($_GET['P2_2']) && $_GET['P2_2']==$k?' selected':'')." value=\"{$k}\">{$k}</option>";
                    ?></select></td>
            <td>R</td>
            <td><select name="P5_2">
                    <option value="">Все</option><?
                    ksort($cc->ex_arr['P5']);
                    foreach($cc->ex_arr['P5'] as $k=>$v) echo "<option".(isset($_GET['P5_2']) && $_GET['P5_2']==$k?' selected':'')." value=\"{$k}\">{$k}</option>";
                    ?></select></td>
            <td>Тип:</td>
            <td width="55"><select name="MP1_2">
                    <option value="">Все</option><?
                    if(isset($cc->ex_arr['MP1'][1])){?><option<?=@$_GET['MP1_2']==1?' selected':''?> value="1">Кованый</option><? }
                    if(isset($cc->ex_arr['MP1'][2])){?><option<?=@$_GET['MP1_2']==2?' selected':''?> value="2">Литой</option><? }
                    if(isset($cc->ex_arr['MP1'][3])){?><option<?=@$_GET['MP1_2']==3?' selected':''?> value="3">Штампованый</option><? }
                    ?></select></td>
            <td colspan="3" rowspan="2" valign="middle">
                <nobr>
                    <select name="scNotZero" id="scNotZero">
                        <option value="-1"<?=@$_GET['scNotZero']?' selected':''?>>Все</option>
                        <option value="1"<?=@$_GET['scNotZero']?' selected':''?>>1</option>
                        <option value="4"<?=@$_GET['scNotZero']?' selected':''?>>4</option>
                        <option value="8"<?=@$_GET['scNotZero']?' selected':''?>>8</option>
                        <option value="12"<?=@$_GET['scNotZero']?' selected':''?>>12</option>
                    </select>
                    <label for="scNotZero">есть на складе</label>
                </nobr>
                <nobr><input name="scZero" type="checkbox" id="scZero"  value="1"<?=@$_GET['scZero']?' checked':''?>><label for="scZero">нет на складе</label></nobr>
                <nobr><input name="scpriceNotZero" type="checkbox" id="scpriceNotZero"  value="1"<?=@$_GET['scpriceNotZero']?' checked':''?>><label for="scpriceNotZero">с спец. ценой</label></nobr>
                <? if(in_array(Cfg::get('CAT_IMPORT_MODE'),array(1,3))){?><nobr><input name="showTI" type="checkbox" id="showTI"  value="1"<?=@$_GET['showTI']?' checked':''?>><label for="showTI">код <strong>T</strong>yre<strong>I</strong>ndex</label></nobr><? }?>
                <nobr><input name="zeroBprice" id="zeroBprice" type="checkbox"  value="1"<?=@$_GET['zeroBprice']?' checked':''?>><label for="zeroBprice">только с ценой = 0</label></nobr>
                <nobr><input name="not_zeroBprice" id="not_zeroBprice" type="checkbox"  value="1"<?=@$_GET['not_zeroBprice']?' checked':''?>><label for="not_zeroBprice">только с ценой</label><br></nobr>
                <nobr><input name="onlyH" id="onlyH" value="1" type="checkbox"<?=@$_GET['onlyH']?' checked':''?>><label for="onlyH">только скрытые</label></nobr>
                <nobr><input name="emptySuffix" id="emptySuffix" type="checkbox" value="1"<?=@$_GET['emptySuffix']?' checked':''?>><label for="emptySuffix">цвет не задан </label></nobr>
                <? if(isset(App_TFields::$fields['cc_cat']['app'])){?><nobr><input name="emptyApp" id="emptyApp" type="checkbox" value="1"<?=@$_GET['emptyApp']?' checked':''?>><label for="emptyApp">нет применяемости</label></nobr><? }?>
                <nobr><input name="inDSonly" id="inDSonly" value="1" type="checkbox"<?=@$_GET['inDSonly']?' checked':''?><?=!@$_GET['dataset_id']?' style="display:none"':''?>><label<?=!@$_GET['dataset_id']?' style="display:none"':''?> for="inDSonly">только набор</label></nobr>
                <nobr><input name="fotoExists" id="fotoExists" type="checkbox" value="1"<?=@$_GET['fotoExists']?' checked':''?>><label for="fotoExists">есть фото </label></nobr>
                <nobr><input name="fix_price" id="fixPrice" type="checkbox" value="1"<?=@$_GET['fixPrice']?' checked':''?>><label for="fixPrice">фиксЦена </label></nobr>
                <nobr><input name="fix_sc" id="ignoreUpdate" type="checkbox" value="1"<?=@$_GET['fixSc']?' checked':''?>><label for="ignoreUpdate">фиксКол-во</label></nobr>
                <nobr><input name="is_balances" id="is_balances" type="checkbox" value="1"<?=@$_GET['is_balances']?' checked':''?>><label for="is_balances">Остатки</label></nobr>
                <nobr><input name="is_not_balances" id="is_not_balances" type="checkbox" value="1"<?=@$_GET['is_not_balances']?' checked':''?>><label for="is_not_balances">Не остатки</label></nobr>
                <nobr><input name="is_not_updated_month" id="is_not_updated_month" type="checkbox" value="1"<?=@$_GET['is_not_updated_month']?' checked':''?>><label for="is_not_updated_month">Не обновлялись месяц</label></nobr>
                <?if (!empty($suplrs)):?>
                    <br><select name="suplrs_filter" id="suplrs_filter">
                        <option value="">Поставщики</option>
                        <?
                        foreach ($suplrs as $sid => $sup){
                            echo '<option value="'.$sid.'">'.$sup['name'].'</option>';
                        }
                        ?>
                    </select>
                <?endif;?>
            </td>
        </tr>
        <tr>
            <td>PCD</td>
            <td width="54"><select name="P4_2">
                    <option value="">Все</option><?
                    ksort($cc->ex_arr['P4']);
                    foreach($cc->ex_arr['P4'] as $k=>$v) echo "<option".(isset($_GET['P4_2']) && $_GET['P4_2']==$k?' selected':'')." value=\"{$k}\">{$k}</option>";
                    ?></select></td>
            <td width="20">ДЦО</td>
            <td width="54"><select name="P6_2">
                    <option value="">Все</option><?
                    ksort($cc->ex_arr['P6']);
                    foreach($cc->ex_arr['P6'] as $k=>$v) echo "<option".(isset($_GET['P6_2']) && $_GET['P6_2']==$k?' selected':'')." value=\"{$k}\">{$k}</option>";
                    ?></select></td>
            <td>Бренд:</td>
            <td><select name="brand_id_2" id="brand_id_2">
                    <option value="">Производитель диска</option><?
                    if(!empty($cc->ex_arr['brand'][0])){
                        foreach($cc->ex_arr['brand'][0] as $k=>$v)
                            echo'<option value="'.$k.'"'.($v['H']!=0?' class="isH"':'').(@$_GET['brand_id_2']==$k?' selected':'').">{$v['name']} ({$v['amount']})</option>";
                    }
                    if(!empty($cc->ex_arr['brand']['replica'])){
                        ?><optgroup label="Replica"><?
                        foreach($cc->ex_arr['brand']['replica'] as $k=>$v)
                            echo'<option value="'.$k.'"'.($v['H']!=0?' class="isH"':'').(@$_GET['brand_id_2']==$k?' selected':'').">{$v['name']} ({$v['amount']})</option>";
                        ?></optgroup><?
                    }
                    if(!empty($cc->ex_arr['sbrand'])){
                        ?><optgroup label="Бренд-реплика"><?
                        foreach($cc->ex_arr['sbrand'] as $k=>$v)
                            echo'<option value="'.$k.'"'.($v['H']!=0?' class="isH"':'').(@$_GET['brand_id_2']==$k?' selected':'').">{$v['name']} ({$v['amount']})</option>";
                        ?></optgroup><?
                    }
                    ?></select>
            </td>
        </tr>
        <tr>
            <td>ET</td>
            <td><select name="P1_2">
                    <option value="">Все</option><?
                    ksort($cc->ex_arr['P1']);
                    foreach($cc->ex_arr['P1'] as $k=>$v) echo "<option".(isset($_GET['P1_2']) && $_GET['P1_2']==$k?' selected':'')." value=\"{$k}\">{$k}</option>";
                    ?></select></td>
            <td>DIA</td>
            <td><select name="P3_2">
                    <option value="">Все</option><?
                    ksort($cc->ex_arr['P3']);
                    foreach($cc->ex_arr['P3'] as $k=>$v) echo "<option".(isset($_GET['P3_2']) && $_GET['P3_2']==$k?' selected':'')." value=\"{$k}\">{$k}</option>";
                    ?></select></td>
            <td nowrap>Реплика</td>
            <td align="left"><select name="sup_id_2">
                    <option value="">все поставщики</option><?
                    ksort($cc->ex_arr['sup_id']);
                    foreach($cc->ex_arr['sup_id'] as $k=>$v) echo "<option".(@$_GET['sup_id_2']===(string)$k?' selected':'')." value=\"{$k}\">".(($cc->sup_arr[$k]==''?'- без поставщика -':$cc->sup_arr[$k])." ($v)")."</option>";
                    ?></select></td>
            <td width="100"><input type="submit" value="Искать &gt;&gt;&gt;">
            </td>
            <td nowrap>Выводить  по <input name="lines" type="text" id="lines" style="width:30px; text-align:center" maxlength="4" value="<?=isset($_GET['lines'])?$_GET['lines']:50?>"> строк</td>
            <td nowrap><input class="refresh" type="submit" value="Обновить форму"><input class="reset" type="submit" value="Сбросить форму"></td>
        </tr>
    </table>
<? }?>
</form>
</body>
</html>
