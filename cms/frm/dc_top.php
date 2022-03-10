<? require_once '../auth.php';
include('../struct.php');

$cp->frm['name']='dc_top';

$cp->checkPermissions();

cp_head();
cp_css();
cp_js();

cp_body();

$os=new App_Discount();

foreach ($_GET as $key=>$value) $$key=$value;
foreach ($_POST as $key=>$value) $$key=$value;
?>
<body>
<style type="text/css">
	body{
		color:#FFF;
		margin:0; 
		background-color:#88B6D9;
	}
</style>

<form method="get" target="bot" action="dc_bot.php" name="form1">
<table border="0" cellspacing="0" cellpadding="5">
  <tr>
    <td><strong>Период с</strong></td>
<td nowrap class="noline"><input name="from_d" type="text" id="from_d" size="5" value="<?=date("d")?>">
        <select name="from_m" id="from_m">
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
        <input name="from_y" type="text" id="from_y" size="10" value="<?=date("Y")?>"></td>
      <td class="noline"><strong>по</strong></td>
      <td nowrap class="noline"><input name="to_d" type="text" id="to_d" size="5" value="<?=date("d")?>">
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
      <td nowrap class="noline"><input tabindex="3" type="button" value="Сегодня" onClick="document.forms['form1'].from_d.value='<?=date("d")?>';document.forms['form1'].from_m.value=<?=date("m")?>;document.forms['form1'].from_y.value=<?=date("Y")?>;document.forms['form1'].to_d.value='<?=date("d")?>';document.forms['form1'].to_m.value=<?=date("m")?>;document.forms['form1'].to_y.value=<?=date("Y")?>;document.forms['form1'].submit()"></td>
	  <?$os->min_dcd();$os->max_dcd()?>
    <td nowrap class="noline"><input tabindex="2" type="button" value="За все время" onClick="document.forms['form1'].from_d.value='<?=$os->min_d?>';document.forms['form1'].from_m.value=<?=$os->min_m?>;document.forms['form1'].from_y.value=<?=$os->min_y?>;document.forms['form1'].to_d.value=<?=$os->max_d?>;document.forms['form1'].to_m.value=<?=$os->max_m?>;document.forms['form1'].to_y.value=<?=$os->max_y?>;document.forms['form1'].submit()"></td>  </tr>
</table>
<table border="0" cellspacing="5" cellpadding="0">
  <tr>
    <td nowrap><strong>Статус карты</strong></td>
    <td><select name="dc_state_id" id="dc_state_id">
      <option value="0">Добавленные</option>
      <option value="1">Доступна для активации</option>
      <option value="2" selected>Активирована</option>
      <option value="3">Анулирована до активации</option>
      <option value="4">Анулирована после активации</option>
      <option value="5">Анулирована по замене карты</option>
    </select>    </td>
    <td><strong>Поиск по номеру 
        <input name="sea_dcnum" type="text" id="sea_dcnum" size="15">
    </strong></td>
    <td><span class="noline">
      <input tabindex="1" type="submit" value="ОТОБРАТЬ >>>">
    </span></td>
  </tr>
</table>
</form>
</body>
<script language="javascript">document.forms['form1'].submit()</script>

<? cp_end()?>
