<? require_once '../auth.php';
include('../struct.php');

$cp->frm['name']='icq_list';
$cp->frm['title']='ICQ менеджеры';

$cp->checkPermissions();

cp_head();
cp_css();
cp_js();

cp_body();
cp_title();

$icq=new ICQ;

if(@$_POST['act']=='add' && @$_POST['uin']!='' && @$_POST['name']!=''){
	$uin=Tools::esc($_POST['uin']);
	$name=Tools::esc($_POST['name']);
	$group=Tools::esc($_POST['group']);
	$active=$_POST['active'];
	if($icq->query("INSERT INTO icq (uin,name,active,gr) VALUES('$uin','$name','$active','$group')"))  note("Добавлено $name ($uin)");
		else warn('Ошибка записи');
}
if(@$_POST['act']=='sw_active' && @$_POST['id']){
	if($icq->query("UPDATE icq SET active={$_POST['sw_active']} WHERE  icq_id='{$_POST['id']}'")) note('Видимость изменена');
		else warn('Ошибка записи');
}
if(@$_POST['act']=='del' && @$_POST['id']){
	if($icq->query("DELETE FROM icq WHERE icq_id='{$_POST['id']}'")) note('Удалено');
		else warn('Ошибка БД');
}
$icq->icq_get_list('all','');?>
<form name="form1" method="post">
<input type="hidden" name="id" value="0">
<input type="hidden" name="sw_active" value="">
<input type="hidden" name="act" value="add">
<table class="ui-table ltable">
  <tr>
    <th>UIN</th>
	<th>Имя на сайте</th>
	<th>Показывать</th>
    <th>Группа</th>
    <th>Cron</th>
	<th>Удалить</th>
  </tr>
<? $l=0;
while($icq->next()!==false){?>
<tr><td><img src="http://wwp.icq.com/scripts/online.dll?icq=<?=$icq->only_num($icq->qrow['uin'])?>&img=5" border="0" align="middle" width="18" height="18">&nbsp;&nbsp;<?=$icq->qrow['uin']?></td>
<td><?=$icq->qrow['name']?></td>
<td align="center"><a href="javascript:;" title="переключить..." onClick="document.forms['form1'].id.value='<?=$icq->qrow['icq_id']?>'; document.forms['form1'].act.value='sw_active';  document.forms['form1'].sw_active.value='<?=$icq->qrow['active']?0:1?>'; document.forms['form1'].submit(); return false"><?=$icq->qrow['active']?'да':'нет'?></a></td>
<td align="center"><?=Tools::unesc($icq->qrow['gr'])?></td>
<td align="center"><span title="<?=$icq->qrow['dt_check']?>"><?=$icq->qrow['active'] && $icq->qrow['online']?'online':'offline'?></span></td>
<td align="center"><input type="image" src="../img/b_drop.png" onClick="if(window.confirm('Вы уверены?')){document.forms['form1'].act.value='del';document.forms['form1'].id.value='<?=$icq->qrow['icq_id']?>';document.forms['form1'].submit();} else return false;"></td></tr>
</tr>
<? }?>
<tr><td><input type="text" name="uin" style="width:100px"></td>
<td><input type="text" name="name" style="width:180px"></td>
<td align="center"><select name="active">
  <option value="1">ДА</option>
  <option value="0">НЕТ</option>
</select></td>
<td align="center"><input type="text" size="30" name="group"></td>
<td align="center" colspan="2"><input type="image" src="../img/add.gif"></td></tr>
</table>
</form>

<? cp_end()?>
