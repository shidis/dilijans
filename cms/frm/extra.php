<? 
require_once '../auth.php';
include('../struct.php');

$cp->frm['name']='extra';
$cp->frm['title']='Наценки';

$cp->checkPermissions();

$cc=new CC_Ctrl();

if(@$_GET['brand_id']==0 ) die('Неверный параметр');
$brand_id=(int)$_GET['brand_id'];
$cc->que('brand_by_id',$brand_id);
if(!$cc->qnum()) die('Бренд не найден');
$cc->next();
$name=$cc->qrow['name'];
$extra_b=$cc->qrow['extra_b'];
$gr=$cc->qrow['gr'];

cp_head();
cp_css();
cp_js();
?>

<?='
<style type="text/css">
</style>
<script type="text/javascript">
	extra.gr='.$gr.';
	extra.brand_id='.$brand_id.';
</script>
'?>

<? cp_body()?>
<? cp_title()?>

<?
/* Поля в таблице cc_extra:
	brand_id - привязка к бренду
	P_value[float] - поле привязки со значением поля P1-P6 из таблицы cc_cat  или sup_id. Например радиус в численном выражении
	S_value[tinyint4] - поле приявязки  для extra_group=3. Например код сезона для шин
	extra[float] - размер наценки в процентах
	minExtra[float] - поля для extra_group=1,2,3. Минимальная наценка в рублях
	extra_group - группа наценок. 
		1- наценка на радиус
		2- наценка на поставщика
		3- наценка на сезон и радиус только для шин
*/
?>

<h3>Наценка на бренд <?=$name?> = <?=$extra_b?> %</h3>


<div id="tabs">
	<ul>
		<li><a href="#tab-extra">Наценки на радиус и поставщика</a></li>
        <? if($gr==1){?>
		<li><a href="#tab-extra-sez">Сезонные наценки</a></li>
       	<? }?>
	</ul>
	<div id="tab-extra">
        <div class="extra" style="overflow:hidden">
            <div id="pagered1"></div>
            <div id="pagered2"></div>
            <div style="width:310px; float:left; overflow:hidden; margin-right:20px;">
            	<table id="grid1"></table> 
            </div>
            <div style="width:310px; overflow:hidden">
            	<table id="grid2"></table> 
            </div>
        </div>
	</div>
    <? if($gr==1){?>
	<div id="tab-extra-sez">
    	<div class="extra-sez" style="overflow:hidden">
        </div>
    </div>
    <? }?>
</div>
<fieldset style="margin:20px 0">
<img src="../img/HelpIcon.png" width="50" style="margin-right:20px;" align="left" />
<p>Отображаются только присутсвующие в базе данных радиусы.</p>
<p>Редактирование наценок: левый клик на ячейке с наценкой, ввести число, нажать клавишу Enter.</p>
</fieldset>
<button id="close" onClick="window.close()">Закрыть окно</button>
<? cp_end()?>
