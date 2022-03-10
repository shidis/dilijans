<? 
require_once '../auth.php';
include('../struct.php');

$cp->frm['name']='dict';
$cp->frm['title']='Словарь терминов';

$cp->checkPermissions();

cp_head();
cp_css();
cp_js();
?>
<?='
<style type="text/css">
#editor{display:none; margin-top:15px}
div.sel{overflow:hidden; margin: 5px 0}
div#terms{overflow:hidden}
#gr{margin-right:20px}
div#terms div {float:left; margin:5px 20px}
div#terms div a{float:left}
a.termDel{margin-left:5px;}
a.termDel img {border:0}
</style>
'?>
<? cp_body()?>
<? cp_title()?>
<fieldset class="ui"><legend class="ui">Ищем/добавляем сокращение</legend>
<div style="padding:10px">
<form id="frm">
<input type="text" id="name" size="50" />
<input type="submit" id="search" value="ИСКАТЬ >>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</form>

<div class="sel">
	<select id="gr"><option value="1">Шины</option><option value="2">Диски</option></select>
    <select id="brand_id"><option value="0">Бренд</option></select>
</div>
<div id="terms"></div>
</div>
</fieldset>
<div id="editor" class="edit_area">
<fieldset class="ui"><legend class="ui">Определение</legend>
<div>
<textarea id="text" name="text"></textarea>
<input type="button" id="save" value="Сохранить изменения">
</div>
</fieldset>
</div>
<? cp_end()?>
