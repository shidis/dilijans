<?
@define (true_enter,1);
require_once('loader.php');

$brand_id=(int)@$_POST['brand_id'];
if(!$brand_id) die('Неверный параметр '.print_r(@$_POST));

$dop=new CC_DOP;

switch ($_GET['act']){
default: echo '[dop.php ERROR]: Неверный параметр вызова.'; break;
case 'list':
$dop->que('dop_list',$brand_id);?>
<? if($dop->qnum()){?>
<table cellpadding="3" cellspacing="1" border="0">
<tr><th>#</th><th>Бренд</th><th>Название</th><th>Цена (розница)</th><th>Удалить</th></tr>
<? while($dop->next()!==false){?>
<tr><td><?=$dop->qrow['dop_id']?></td>
<td><?=Tools::unesc($dop->qrow['bname'])?></td>
<td><a href="#_edit" onClick="return doLoad('edit',{brand_id:<?=$brand_id?>, dop_id: '<?=$dop->qrow['dop_id']?>'})"><?=Tools::unesc($dop->qrow['name'])?></a></td>
<td><?=$dop->qrow['price']?></td>
<td><a href="#" onClick="return doLoad('del',{brand_id:<?=$brand_id?>, dop_id: '<?=$dop->qrow['dop_id']?>'})"><img src="../img/b_drop.png" border="0"></a></td></tr>
<? }?>
</table>
<? }?>
<?
break;

case 'save':
$name=trim(Tools::esc($_POST['name']));
if($name=='') {echo '<b>ОШИБКА! Не задано наименование</b>'; break;}
$price=(float)$_POST['price'];
$dop_id=(int)@$_POST['dop_id'];
$_RESULT['edit']=@$_POST['edit'];
if(!$dop_id){
	if($dop->query("INSERT INTO cc_dop (brand_id,name,price) VALUES('$brand_id','$name','$price')")) $_RESULT['dop_id']=mysql_insert_id(); else echo 'Ошибка записи';
}else{
	if(!$dop->query("UPDATE cc_dop SET name='$name',price='$price' WHERE dop_id='$dop_id'")) echo 'Ошибка записи';
}
break;

case 'edit':?>
<fieldset><legend>Изменить комплектацию</legend>
<? $dop->que('dop_by_id',$_POST['dop_id']);
if(!$dop->qnum()) {echo 'Запись не найдена'; break;} else $dop->next();
$edit=1;
case 'add':
if($_GET['act']!='edit'){?><fieldset><legend>Новая комплектация</legend><? }?>
<form enctype="multipart/form-data" id="eform" name="eform">
<input type="hidden" name="brand_id" value="<?=$brand_id?>">
<input type="hidden" name="edit" value="<?=$edit?>">
<input type="hidden" name="dop_id" value="<?=(int)@$dop->qrow['dop_id']?>">
<input type="hidden" name="MAX_FILE_SIZE" value="<?=Cfg::get('max_file_size');?>">
  <table border="0" cellspacing="0" cellpadding="0" width="100%">
    <tr>
      <td>Наименование</td>
      <td width="100%"><input name="name" type="text" id="name" style="width:100%" value="<?=Tools::unesc(@$dop->qrow['name'])?>"></td>
    </tr>
    <tr>
      <td nowrap>Розничная цена</td>
      <td><input type="text" name="price" id="price" style="width:70px" value="<?=@$dop->qrow['price']?>">
      руб</td>
    </tr>
    <tr>
      <td nowrap><input id="save_but" type="button" value="Записать" onClick="return doLoad('save', {f:document.getElementById('eform')});"></td>
      <td><div id="save_loader"></div></td>
    </tr>
  </table>
  </form>
</fieldset>
<? break;

case 'del':
$dop_id=(int)$_POST['dop_id'];
if($dop_id) $dop->ld('cc_dop','dop_id',$dop_id);
break;
}

debug();

$debug_off=
<<<HTML
<br><br><a href="javascript:toggle('debug_on');toggle('debug')"><img class="nob" src="../img/drop-no.gif" align="baseline">Debug off</a>
HTML;

$_RESULT['debug']='<h2>Отладочная информация</h2>'.$_RESULT['debug'].$debug_off;
?>