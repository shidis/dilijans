<? if (!defined('true_enter')) die ("Direct access not allowed!");?>

<script type="text/javascript">
	$(document).ready(function(){
		
		var tb_els='text<? foreach(App_TFields::get('cc_cat','editor',$gr) as $k=>$v){?>, af[<?=$k?>]<? }?>';
		tinyMCE.init($.extend(TM.cfg1,{
			elements: tb_els
		}));

	});
</script>


<div class="edit_area">
<form action="" method="post" name="form1">
  <table width="100%" border="0" cellpadding="0" cellspacing="5">
    <tr>
      <td><strong>Бренд</strong></td>
      <td colspan="3">
	  <select name="brand_id"  onChange="document.forms['form1'].model_id.value=0; document.forms['form1'].submit();">
	  <option value="">Выберите брэнд</option>
	  	<?
		$cc->que('brands',$gr);
		while($cc->next()!=FALSE)
			echo "<option value=\"{$cc->qrow['brand_id']}\" ".($cc->qrow['brand_id']==@$_POST['brand_id']?'selected':'').">{$cc->qrow['name']}".($cc->qrow['H']!=0?' <--скрыт':'')."</option>";
		?>
      </select></td>
    </tr>
    <tr>
      <td width="101"><strong>Модель</strong></td>
      <td colspan="3">
	  	<select name="model_id" onChange="document.forms['form1'].submit();">
	  		<option value="">Выберите модель</option>
		<?
		if(@$_POST['brand_id']!=''){
			$cc->que('model_by_brand',$_POST['brand_id']);
			while($cc->next()!=FALSE)
			echo "<option value=\"{$cc->qrow['model_id']}\" ".($cc->qrow['model_id']==@$_POST['model_id']?'selected':'').">{$cc->qrow['name']}".($cc->qrow['H']!=0?' <--скрыт':'')."</option>";
		}
		?>
    </select>    </tr>
	<? if(@$_POST['model_id']){
		$cc->que('model_by_id',@$_POST['model_id']);
		$cc->next();
	}?>
    <tr>
      <td>Ширина</td>
      <td width="155"><input tabindex="1" name="P3" type="text" id="P3"></td>
      <td width="118">Суффикс</td>
      <td width="100%"><input name="suffix" type="text"></td>
    </tr>
    <tr>
      <td>Высота</td>
      <td><input name="P2" tabindex="2" type="text" id="P2"></td>
      <td nowrap>Скоростная ZR </td>
      <td><input name="P6" type="checkbox" id="P6" value="1" ></td>
    </tr>
    <tr>
      <td>Радиус</td>
      <td><input tabindex="3" name="P1" type="text" id="P1"></td>
      <td>Ин / Ис</td>
      <td><input name="P7" type="text" id="P7"></td>
    </tr>
    <tr>
      <td nowrap>Базовая цена</td>
	  <td colspan="3"><input tabindex="4" type="text" name="base_price" >
	  <select name="cur_id">
	  <?
	  $cc->que('cur');
	  while($cc->next()!=FALSE)
	  	echo'<option value="'.$cc->qrow['cur_id'].'">'.$cc->qrow['name'].'</option>';
	  ?>
	  </select>	  </td>
    </tr>
    <tr>
      <td>Розница</td>
      <td colspan="2"><input name="cprice" type="text" id="cprice" > 
        руб.</td>
      <td><small>Примечание: при вводе значения в поле &quot;Рознца&quot; для нее автоматически устанавливается статус &quot;фиксированная&quot; - цена не будет рассчитываться пот наценкам.</small></td>
    </tr>
    <tr>
      <td>Спец. цена </td>
      <td colspan="3"><input name="scprice" type="text" id="scprice" > 
        руб.</td>
    </tr>
    <? $af=App_TFields::formEl('cc_cat','all',1,@$cc->qrow);
	foreach($af as $v){?><tr><td><?=$v[0]?></td><td colspan="3"><?=$v[1]?></td></tr><? }?>
    <tr>
      <td colspan="2"><? if((@$_POST['brand_id']>'') && (@$_POST['model_id']>'')) echo'<input name="add_cpost" type="submit" id="add_post" value="Записать">';?></td>
      <td colspan="2">&nbsp;</td>
    </tr>
<? if((@$_POST['brand_id']>'') && (@$_POST['model_id']>'')){?>
<? }?>
  </table>
  
<? foreach(App_TFields::get('cc_cat','editor',1) as $k=>$v){
	?><p><strong><?=$v['caption']?></strong> <button class="TM_sw" forel="af[<?=$k?>]">/</button></p><?
	?><textarea class="TM" name="af[<?=$k?>]" style="width:100%; height:160px"><?=Tools::taria(@$cc->qrow[$k])?></textarea><?
}?>


</form>
<? if(@$_POST['brand_id']=='') note('Сначала выбирайте бренд, модель, затем все остальное. При смене бренда и модели - поля очищаются!')?>

</div>