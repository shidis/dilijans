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
	  <select  tabindex="1" name="brand_id" id="brand_id" onChange="document.forms['form1'].model_id.value=0; document.forms['form1'].submit();">
	  <option value="">Выберите брэнд</option>
	  	<?
		$cc->que('brands',$gr);
		$r=-1;
		$sb=-1;
		while($cc->next()!=FALSE){
			if($r<=0 && $cc->qrow['replica']==1){
				$r=1;
				echo '<optgroup label="Replica">';
			}
			if($r==1 && $cc->qrow['replica']!=1){
				$r=0;
				echo '</optgroup>';
			}
			if($sb<=0 && $cc->qrow['sup_id']){
				$sb=1;
				echo '<optgroup label="Бренд-реплика">';
			}
			if($sb==1 && !$cc->qrow['sup_id']){
				$sb=0;
				echo '</optgroup>';
			}
			echo "<option value=\"{$cc->qrow['brand_id']}\" ".($cc->qrow['brand_id']==@$_POST['brand_id']?'selected':'').">{$cc->qrow['name']}".($cc->qrow['H']!=0?' <--скрыт':'')."</option>";
		}
		if($r==1 || $sb==1) echo '</optgroup>';
		?>
      </select></td>
    </tr>
    <tr>
      <td width="101"><strong>Модель</strong></td>
      <td colspan="3">
	  	<select tabindex="2" name="model_id" id="name_list" onChange="document.forms['form1'].submit();">
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
		$cc->next();?>
	<? }?>
    <tr>
      <td>J</td>
      <td width="144"><input name="P2" type="text" id="P2" tabindex="3"></td>
      <td width="129">LZ</td>
      <td width="100%"><input name="P4" type="text" id="P4" tabindex="8"></td>
    </tr>
    <tr>
      <td>Диаметр</td>
      <td><input name="P5" type="text" id="P5" tabindex="4"></td>
      <td>PCD</td>
      <td><input name="P6" type="text" id="P6" tabindex="9"></td>
    </tr>
    <tr>
      <td>ET</td>
      <td><input name="P1" type="text" id="P1" tabindex="5"></td>
      <td>Суффикс</td>
      <td><input name="suffix" type="text" tabindex="10"></td>
    </tr>
	<tr>
      <td>DIA</td>
      <td><input name="P3" type="text" id="P3" tabindex="6"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
	</tr>
    <tr>
      <td nowrap>Базовая цена</td>
	  <td colspan="3"><input type="text" name="base_price"  tabindex="11">
	  <select name="cur_id" tabindex="12">
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
      <td colspan="3"><input name="scprice" type="text" id="scprice"  tabindex="11">
      руб.</td>
    </tr>
    <? $af=App_TFields::formEl('cc_cat','all',2,@$cc->qrow);
	foreach($af as $v){?><tr><td><?=$v[0]?></td><td colspan="3"><?=$v[1]?></td></tr><? }?>
    <tr>
      <td colspan="2"><? if((@$_POST['brand_id']>'') && (@$_POST['model_id']>'')) echo'<input name="add_cpost" type="submit" id="add_post" value="Записать" tabindex="13">';?></td>
      <td colspan="2">&nbsp;</td>
    </tr>
<? if((@$_POST['brand_id']>'') && (@$_POST['model_id']>'')){?>
<? }?>
  </table>
  
<? foreach(App_TFields::get('cc_cat','editor',$gr) as $k=>$v){
	?><p><strong><?=$v['caption']?></strong> <button class="TM_sw" forel="af[<?=$k?>]">/</button></p><?
	?><textarea class="TM" name="af[<?=$k?>]" style="width:100%; height:160px"><?=Tools::taria(@$cc->qrow[$k])?></textarea><?
}?>

</form>
<? if(@$_POST['brand_id']=='') note('Сначала выбирайте бренд, модель, затем все остальное. При смене бренда и модели - поля очищаются!')?>

</div>
