<? require_once '../auth.php';
include('../struct.php');

$cp->frm['name']='cc_mspez';
$cp->frm['title']='Дополнительные параметры моделей шин/дисков';

$cp->checkPermissions();

cp_head();
cp_css();
cp_js();

cp_body();
cp_title();

$cc=new CC_Ctrl();


if(@$_POST['act']=='add' && @$_POST['name']!='' && @$_POST['gr']>0){
	$name=Tools::esc($_POST['name']);
	$gr=$_POST['gr'];
	$dt=date("Y-m-d h:i:s");
	if($cc->query("INSERT INTO cc_mspez (gr,name,dt_added) VALUES('$gr','$name','$dt')")) note("<p>Добавлено <strong>\"$name\"</strong></p>");
		else warn('<p>Ошибка записи</p>');
}
if(@$_POST['act']=='e_post' && @$_POST['id'] && @$_POST['e_name']!=''){
	$name=Tools::esc($_POST['e_name']);
	if($cc->query("UPDATE cc_mspez SET name='$name' WHERE  mspez_id='{$_POST['id']}'")) note('<p>Запись изменена</p>');
		else warn('<p>Ошибка записи</p>');
}
if(@$_POST['act']=='del' && @$_POST['id']){
	$cc->query("UPDATE cc_model SET mspez_id=0 WHERE mspez_id='{$_POST['id']}'");
	if($cc->query("DELETE FROM cc_mspez WHERE mspez_id='{$_POST['id']}'")) note('<p>Удалено</p>');
		else warn('<p>Ошибка БД</p>');
}
?>
<form name="form1" method="post">
<input type="hidden" name="id" value="0">
<input type="hidden" name="act" value="add">
<table class="ui-table ltable">
  <tr>
	<th>#</th>
    <th>Категория</th>
	<th>Название параметра</th>
	<th>&nbsp;</th>
	<th>&nbsp;</th>
  </tr>
<? $l=0;
$cc->query("SELECT * FROM cc_mspez ORDER BY gr,name");
while($cc->next()!==false){?>
<tr><td align="center"><?=$cc->qrow['mspez_id']?></td>
<td align="center"><?=$cc->qrow['gr']==1?'ШИНЫ':'ДИСКИ'?></td>
<td>
<? if(@$_POST['act']=='edit' && @$_POST['id']==$cc->qrow['mspez_id']){?>
<input type="text" name="e_name" style="width:380px" value="<?=$cc->qrow['name']?>">
<? }else echo $cc->qrow['name'];?></td>
<td align="center">
<? if(@$_POST['act']=='edit'){?>
<input type="image" src="../img/checked.gif" onClick="document.forms['form1'].id.value='<?=$cc->qrow['mspez_id']?>'; document.forms['form1'].act.value='e_post';">
<? }else{?>
<input type="image" src="../img/b_edit.png" onClick="document.forms['form1'].id.value='<?=$cc->qrow['mspez_id']?>'; document.forms['form1'].act.value='edit';">
<? }?>
</td>
<td align="center"><input type="image" src="../img/b_drop.png" onClick="if(window.confirm('Вы уверены?')){document.forms['form1'].act.value='del';document.forms['form1'].id.value='<?=$cc->qrow['mspez_id']?>'} else return false"></td></tr>
</tr>
<? }?>
<tr><td align="center">+</td><td><select name="gr"><option value="1">ШИНЫ</option><option value="2">ДИСКИ</option></select></td>
<td><input type="text" name="name" style="width:380px"></td>
<td align="center" colspan="2"><input type="image" src="../img/add.gif"></td></tr>
</table>
</form>

<? cp_end();
