<? require_once '../auth.php';
include('../struct.php');

$cp->frm['name']='sup';
$cp->frm['title']='Поставщики';

$cp->checkPermissions();

cp_head();
cp_css();
cp_js();

cp_body();
cp_title();

$cc=new CC_Ctrl();

if(@$_POST['act']=='add' && @$_POST['name']!=''){
	$name=Tools::esc($_POST['name']);
	if($cc->query("INSERT INTO cc_sup (name) VALUES('$name')")) note("<p>Добавлено <strong>\"$name\"</strong></p>");
		else warn('<p>Ошибка записи</p>');
}
if(@$_POST['act']=='e_post' && @$_POST['id'] && @$_POST['e_name']!=''){
	$name=Tools::esc($_POST['e_name']);
	if(!$cc->query("UPDATE cc_sup SET name='$name' WHERE  sup_id='{$_POST['id']}'")) warn ('<p>Ошибка записи</p>');
}
if(@$_POST['act']=='del' && @$_POST['id']){
	$cc->query("UPDATE cc_model SET sup_id=0 WHERE sup_id='{$_POST['id']}'");
	if($cc->unum()) $cc->addCacheTask('prices');
	if($cc->query("DELETE FROM cc_sup WHERE sup_id='{$_POST['id']}'")) {
		$cc->query("DELETE FROM cc_extra WHERE (extra_group=2)AND(P_value='{$_POST['id']}')");
		note ('<p>Удалено</p>');
		$cc->addCacheTask('prices');
	}else warn ('<p>Ошибка БД</p>');
}
?>
<form name="form1" method="post">
<input type="hidden" name="id" value="0">
<input type="hidden" name="act" value="add">
<table class="ui-table ltable">
  <tr>
    <th>id</th>
	<th>Название поставщика</th>
	<th>&nbsp;</th>
	<th>&nbsp;</th>
  </tr>
<? $l=0;
$cc->query("SELECT * FROM cc_sup ORDER BY name");
while($cc->next()!==false){?>
<tr><td align="center"><?=$cc->qrow['sup_id']?></td>
<td>
<? if(@$_POST['act']=='edit' && @$_POST['id']==$cc->qrow['sup_id']){?>
<input type="text" name="e_name" style="width:200px" value="<?=htmlspecialchars(Tools::unesc($cc->qrow['name']))?>">
<? }else echo Tools::unesc($cc->qrow['name']);?></td>
<td align="center">
<? if(@$_POST['act']=='edit' && @$_POST['id']==$cc->qrow['sup_id']){?>
<input type="image" src="../img/checked.gif" onClick="document.forms['form1'].id.value='<?=$cc->qrow['sup_id']?>'; document.forms['form1'].act.value='e_post';">
<? }else{?>
<input type="image" src="../img/b_edit.png" onClick="document.forms['form1'].id.value='<?=$cc->qrow['sup_id']?>'; document.forms['form1'].act.value='edit';">
<? }?></td>
<td align="center"><input type="image" src="../img/b_drop.png" onClick="if(window.confirm('Возможно есть модели с этим поставщиком. Вы уверены?')){document.forms['form1'].act.value='del';document.forms['form1'].id.value='<?=$cc->qrow['sup_id']?>'} else return false"></td></tr>
</tr>
<? }?>
<tr><td>Добавить</td>
<td><input type="text" name="name" style="width:200px"></td>
<td colspan="3" align="center"><input type="image" src="../img/checked.gif"></td>
</tr>
</table>
<p>
  <input type="button" value="Закрыть окно" onClick="window.close()">
</p>
</form>

<? cp_end();
