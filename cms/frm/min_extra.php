<? 

require_once '../auth.php';
include('../struct.php');

$cp->frm['name']='extra_min';
$cp->frm['title']='Минимальные наценки';

$cp->checkPermissions();

cp_head();
cp_css();
cp_js();
?>

<?='
<style type="text/css">
</style>
'?>

<? cp_body()?>
<? cp_title()?>

<div id="pagered1" style="text-align:center;"></div>
<div id="pagered2" style="text-align:center;"></div>
<div style="width:340px; float:left; overflow:hidden">
<table id="grid1" class="scroll" cellpadding="0" cellspacing="0"></table> 
</div>
<div style="width:340px; overflow:hidden; margin-left:50px;">
<table id="grid2" class="scroll" cellpadding="0" cellspacing="0"></table> 
</div>
<fieldset style="margin:20px 0">
<img src="../img/HelpIcon.png" width="50" style="margin-right:20px;" align="left" />
<p>Отображаются только присутсвующие в базе данных радиусы.</p>
<p>Редактирование наценок: левый клик на ячейке с наценкой, ввести число, нажать клавишу Enter.</p>
</fieldset>
<? cp_end()?>
