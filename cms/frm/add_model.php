<? 
require_once '../auth.php';
include('../struct.php');

$gr=@$_GET['gr'];
if($gr!=1 && $gr!=2) die('gr incorrect. exit.');

$cp->frm['title']='Добавить модель '.($gr==1?'шины':'диска'); 
$cp->frm['name']='add_model';

$cp->checkPermissions();

cp_head();
cp_css();
cp_js();
cp_body();
cp_title();

$cc=new CC_Ctrl();

if (isset($_POST['add_mpost']) && @$_POST['brand_id']){
	foreach ($_POST as $key=>$value)  $$key=($value);
	$name=trim($name);
	$text=@$tmh_text!=''?$tmh_text:$text;
	if($name=='') warn('Название модели долджно быть указано');
	elseif($brand_id==0) warn('Бренд должен быть задан'); 
	else if($cc->model_ae('add',array(
        'gr'=>$gr,
        'brand_id'=>$brand_id,
        'af'=>@$af,
        'mspez_id'=>@$mspez_id,
        'class_id'=>@$class_id,
        'sup_id'=>$sup_id,
        'name'=>$name,
        'alt'=>$alt,
        'suffix'=>$suffix,
        'text'=>$text,
        'P1'=>$P1,
        'P2'=>@$P2,
        'P3'=>@$P3,
        'imgFileFileld' => 'imgFile',
        'spyUrl'=>$spyUrl
    ))) note('Модель добавлена');    else warn('Ошибка записи');
}		
?>
<style type="text/css">
	.msg-block{
		margin:5px; 0;
	}
</style>
<script type="text/javascript">
$(document).ready(function(){

		var tb_els='text<? foreach(App_TFields::get('cc_model','editor',$gr) as $k=>$v){?>, af[<?=$k?>]<? }?>';
		tinyMCE.init($.extend(TM.cfg1,{
			elements: tb_els
		}));

});
</script>

<div class="edit_area">
<form action="" method="post" enctype="multipart/form-data" name="form1">
<input type="hidden" name="MAX_FILE_SIZE" value="<?=Cfg::get('max_file_size');?>">

<? if(@$_POST['brand_id']) {?><input name="add_mpost" type="submit" id="add_post" value="Записать"><? }?>

  <table width="100%"  border="0" cellpadding="0" cellspacing="6">
    <tr>
      <td><strong>Бренд</strong></td>
      <td width="100%" colspan="2">
	  <select name="brand_id" id="brand_id" onChange="document.forms['form1'].submit();">
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
      <td colspan="2"><input name="name" type="text" id="name" style="width:100%"></td>
    </tr>
    <tr>
      <td>Альт. названия</td>
      <td colspan="2"><input name="alt" type="text" id="alt" style="width:100%"></td>
    </tr>
	<? if($gr==1){?>
    <tr>
      <td>Тип авто</td>
      <td colspan="2"><select name="P2">
	  <option value="0" >Не указан</option>
      <option value="1" <?=(@$_POST['P2']=='1'?'selected':'')?>>Легковой</option>
      <option value="2" <?=(@$_POST['P2']=='2'?'selected':'')?>>Внедорожник</option>
      <option value="3" <?=(@$_POST['P2']=='3'?'selected':'')?>>Микроавтобус</option>
      <option value="4" <?=(@$_POST['P2']=='4'?'selected':'')?>>Легковой/внедорожник</option>
    </select></td>
    </tr>
    <tr>
      <td>Сезонность</td>
      <td colspan="2"><select name="P1">
      <option value="1" <?=(@$_POST['P1']=='1'?'selected':'')?>>Лето</option>
      <option value="2" <?=(@$_POST['P1']=='2'?'selected':'')?>>Зима</option>
      <option value="3" <?=(@$_POST['P1']=='3'?'selected':'')?>>Всесезон</option>
    </select></td>
    </tr>
    <tr>
      <td>Шипы</td>
      <td colspan="2"><select name="P3">
      <option value="0" <?=(@$_POST['P3']=='0'?'selected':'')?>>Нет шипов</option>
      <option value="1" <?=(@$_POST['P3']=='1'?'selected':'')?>>Шипы</option>
    </select></td>
    </tr>
	<? }elseif($gr==2){?>
    <tr>
      <td>Тип диска</td>
      <td colspan="2"><select name="P1">
      <option value="2" <?=(@$_POST['P1']=='2'?'selected':'')?>>Литой</option>
      <option value="1" <?=(@$_POST['P1']=='1'?'selected':'')?>>Кованый</option>
      <option value="3" <?=(@$_POST['P1']=='3'?'selected':'')?>>Штампованный</option>
    </select></td>
    </tr><?
	}
    $cc->load_mspez($gr);
    if(!empty($cc->mspez_arr)){
      ?><tr>
      <td>Доп. параметр</td>
      <td colspan="4">
          <select name="mspez_id">
              <option value="0">Нет</option>
              <? foreach($cc->mspez_arr as $k=>$v){?>
                  <option value="<?=$k?>"><?=$v?></option>
              <? }?>
          </select>
      </td>
      </tr><?
    }
    $cc->load_class($gr);
    if(!empty($cc->class_arr)){
      ?><tr>
      <td>Класс модели</td>
      <td colspan="4">
          <select name="class_id">
              <option value="0">Нет</option>
              <? foreach($cc->class_arr as $k=>$v){?>
                  <option value="<?=$k?>"><?=$v?></option>
              <? }?>
          </select>
      </td>
      </tr><?
    }
    ?>

    <tr>
    <td>Суффикс</td>
    <td colspan="2"><input name="suffix" type="text" id="suffix" size="40"></td>
    </tr>
	<tr>
	  <td>Поставщик</td>
	  <td colspan="2"><select name="sup_id" id="sup_id">
        <? $cc->load_sup();
	foreach($cc->sup_arr as $k=>$v){?>
        <option value="<?=$k?>">
        <?=$k==0?'< без поставщика >':$v?>
        </option>
        <? }?>
      </select></td>
	</tr>
    <? $af=App_TFields::formEl('cc_model','all',$gr);
	foreach($af as $v){?><tr><td><?=$v[0]?></td><td colspan="2"><?=$v[1]?></td></tr><? }?>
      <tr>
          <td colspan="3"><hr /></td>
      </tr>
    <tr>
      <td colspan="3">
          <fieldset class="ui" style="border:1px dashed #999; padding: 10px; width: 99%"><legend class="ui">Загрузить изображение</legend>
              <p><label>Файл</label><br /><input name="imgFile" type="file" id="imgFile" style="width: 99%"></p>
              <p><labeL>Загрузка по урлу http://</labeL><br /><input name="spyUrl" type="text" id="spyUrl"  style="width: 99%;" /></p>
          </fieldset>
      </td>
    </tr>
    <tr>
      <td colspan="3"><hr /></td>
    </tr>
    
  </table>
  
<strong>Текстовое описание модели</strong> <button class="TM_sw" forel="text">/</button>
<textarea class="TM" name="text" style="width:100%; height:500px"><?=Tools::taria(@$cc->qrow['text'])?></textarea>

<? foreach(App_TFields::get('cc_model','editor',$gr) as $k=>$v){
	?><p><strong><?=$v['caption']?></strong> <button class="TM_sw" forel="af[<?=$k?>]">/</button></p><?
	?><textarea class="TM" name="af[<?=$k?>]" style="width:100%; height:500px"><?=Tools::taria(@$cc->qrow[$k])?></textarea><?
}?><br />

<? if(@$_POST['brand_id']) {?><input name="add_mpost" type="submit" id="add_post" value="Записать"><? }?>

</form>

<? 
if(@$_POST['brand_id']=='') note('Сначала выбирайте бренд затем все остальное. При смене бренда - поля очищаются!')?>
</div>

<? cp_end();
