<?

require_once '../auth.php';
include('../struct.php');

$cp->frm['name']='config';
$cp->frm['title']='Настройка системы заказов и пользователей';

$cp->checkPermissions();

cp_head();
cp_css();
cp_js();
?>


<style type="text/css">
#tabs table {width:100%;}
.th3{width:200px;}
.th2{width:30%}
.th1{width:30%}
#tabs input{width:100%;}

#editWin input, #editWin textarea{width:100%}
#editWin fieldset{margin-top:10px;}
#editWin div {margin-top:10px; display:block;}
#editWin .h{width:auto}

#min_extra td, #min_extra th, #order_discount td, #order_discount th{
	padding:0;
}
#order_discount .ui-pg-input{
	width:30px;
}
</style>

<? cp_body()?>
<? cp_title()?>

<?
	/* Группы (system_data.group) :
		0 - не оттобжаемые данные в таблице настроек
		1 - заголовки страниц  (name начинается с title_)
		2 - емейлы (name начинается с mail_)
		3 - настройка скидок
		4 - параметры отображения каталога на сайте
		5 - параметры конвертации изображений в каталоге шин и дисков
		6 - настройка системы заказов и юзеров
	*/

$adm=@$_GET['adm'] || CMS_LEVEL_ACCESS==1?1:0;

$db=new DB();

function load($group)
{
	global $db,$adm;
	return $db->fetchAll("SELECT * FROM system_data WHERE system_data.group_id='{$group}' ".(!$adm?"AND NOT H":'')." ORDER BY pos");
}
?>		
<div id="editWin" title="Редактировать">

	<form>
    	<label>Значение</label>
        <textarea name="V" style="height:50px" class="ui text ui-widget-content ui-corner-all"></textarea>
    	<? if($adm){?>
        <fieldset class="ui-widget-content ui-corner-all"><legend class="ui">Администрирование</legend>
        <div>
        <label>Заголовок</label> <textarea name="title" style="height:30px" class="text ui-widget-content ui-corner-all"></textarea>
        </div>
        <div>
       	<label>Имя</label> <input type="text" name="name" class="text ui-widget-content ui-corner-all" />
        </div>
        <div>
        <label>Позиция</label> <input type="text" name="pos" class="text ui-widget-content ui-corner-all" />
        </div>
        <div>
        <label>Скрытый</label> <input type="checkbox" value="1" name="H" class="h text ui-widget-content ui-corner-all" />
        </div>
        <div>
        <label>Комментарий</label> <textarea name="comment" style="height:30px" class="text ui-widget-content ui-corner-all"></textarea>
        </div>
        <div>
        <label>Widget @EVAL</label> <textarea name="widget" style="height:50px" class="text ui-widget-content ui-corner-all"></textarea>
        </div>
        </fieldset>
        <? }?>
    </form>

</div>
<?
$d1=load(1);
?>
<div id="tabs">
<form id="frm">
<input type="hidden" name="adm" value="<?=$adm?>" />
	<ul>
		<li><a href="#tabs-3">Скидки/наценки</a></li>
		<? if(!empty($d1)){?><li><a href="#tabs-1">Заголовки страниц</a></li><? }?>
		<li><a href="#tabs-2">Эл. почта</a></li>
		<li><a href="#tabs-4">Каталог</a></li>
		<li><a href="#tabs-6">Заказы и пользователи</a></li>
        <li><a href="#tabs-7">Курсы валют</a></li>
        <li><a href="#tabs-8">Система</a></li>
        <li><a href="#tabs-9">Реквизиты</a></li>
	</ul>
    <div id="tabs-3">
    <? $d=load(3);
	if(count($d)){?>
    	<? if($adm){?><div style="margin-bottom:10px;"><button group="3" class="add">Добавить</button></div><? }?>
	    <table class="ui-table">
        <tr><th class="th1">Параметр</th><th class="th3">Псевдоним</th><th class="th2">Значение</th><? if($adm){?><th class="th3">&nbsp;</th><th width="1%">Скрытый</th><? }?></tr>
        <? foreach($d as $v){?>
        <tr id="<?=$v['data_id']?>"><td><?=nl2br(Tools::unesc($v['title']))?></td><td><?=Tools::html($v['name'])?></td><td><input type="text" value="<?=Tools::html($v['V'])?>" /></td><? if($adm){?><td nowrap="nowrap"><button class="edit">Изменить</button><button class="del">Удалить</button></td><td><?=$v['H']?'да':'нет'?></td><? }?></tr>
        <? }?>
        </table>
    <? }?>
    <div id="min_extra" style="margin:15px 0 0 0">
    	<fieldset class="ui"><legend class="ui">Минимальные наценки на радиус (в рублях)</legend>
        <div style="padding:15px">
            <div style="width:390px; float:left; overflow:hidden">
             <table id="me_grid1" class="scroll" cellpadding="0" cellspacing="0"></table> 
            </div>
            <div style="width:340px; overflow:hidden; margin-left:50px;">
                <table id="me_grid2" class="scroll" cellpadding="0" cellspacing="0"></table> 
            </div>
            <fieldset style="margin:20px 0"><div style="padding:15px">
                <img src="../img/HelpIcon.png" width="50" style="margin-right:20px;" align="left" />
                <p>Отображаются только присутсвующие в базе данных радиусы.</p>
                <p>Редактирование наценок: левый клик на ячейке с скидкой, изменить значение, нажать клавишу Enter.</p>
            </div></fieldset>
        </div>
        </fieldset>
    </div>
    </div>
<?	if(count($d1)){?>	
	<div id="tabs-1">
    	<? if($adm){?><div style="margin-bottom:10px;"><button class="add" group="1">Добавить</button></div><? }?>
	    <table class="ui-table">
        <tr><th class="th1">Параметр</th><th class="th3">Псевдоним</th><th class="th2">Значение</th><? if($adm){?><th class="th3">&nbsp;</th><th width="1%">Скрытый</th><? }?></tr>
        <? foreach($d1 as $v){?>
        <tr id="<?=$v['data_id']?>"><td><?=nl2br(Tools::unesc($v['title']))?></td><td><?=Tools::html($v['name'])?></td><td><input type="text" value="<?=Tools::html($v['V'])?>" /></td><? if($adm){?><td nowrap="nowrap"><button class="edit">Изменить</button><button class="del">Удалить</button></td><td><?=$v['H']?'да':'нет'?></td><? }?></tr>
        <? }?>
        </table>
	</div>
    <? }?>
    <div id="tabs-2">
    <? $d=load(2);
	if(count($d)){?>
    	<? if($adm){?><div style="margin-bottom:10px;"><button group="2" class="add">Добавить</button></div><? }?>
	    <table class="ui-table">
        <tr><th class="th1">Параметр</th><th class="th3">Псевдоним</th><th class="th2">Значение</th><? if($adm){?><th class="th3">&nbsp;</th><th width="1%">Скрытый</th><? }?></tr>
        <? foreach($d as $v){?>
        <tr id="<?=$v['data_id']?>"><td><?=nl2br(Tools::unesc($v['title']))?></td><td><?=Tools::html($v['name'])?></td><td><input type="text" value="<?=Tools::html($v['V'])?>" /></td><? if($adm){?><td nowrap="nowrap"><button class="edit">Изменить</button><button class="del">Удалить</button></td><td><?=$v['H']?'да':'нет'?></td><? }?></tr>
        <? }?>
        </table>
    <? }?>
    </div>
    <div id="tabs-4">
    <? $d=load(4);
	if(count($d)){?>
    	<div style="margin-bottom:10px;">
			<? if($adm){?><button group="4" class="add">Добавить</button><? }?>
            <? if(Cfg::get('fittingRingsEnable')){?><button id="showRingsWin">Проставочные кольца дисков</button>
            <div id="ringsWin" title="Проставочные кольца"></div><? }?>
        </div>
	    <table class="ui-table">
        <tr><th class="th1">Параметр</th><th class="th3">Псевдоним</th><th class="th2">Значение</th><? if($adm){?><th class="th3">&nbsp;</th><th width="1%">Скрытый</th><? }?></tr>
        <? foreach($d as $v){?>
        <tr id="<?=$v['data_id']?>"><td><?=nl2br(Tools::unesc($v['title']))?></td><td><?=Tools::html($v['name'])?></td><td><input type="text" value="<?=Tools::html($v['V'])?>" /></td><? if($adm){?><td nowrap="nowrap"><button class="edit">Изменить</button><button class="del">Удалить</button></td><td><?=$v['H']?'да':'нет'?></td><? }?></tr>
        <? }?>
        </table>
    <? }?>
    </div>
    <div id="tabs-6">
    <? $d=load(6);
	if(count($d)){?>
    	<? if($adm){?><div style="margin-bottom:10px;"><button group="6" class="add">Добавить</button></div><? }?>
	    <table class="ui-table">
        <tr><th class="th1">Параметр</th><th class="th3">Псевдоним</th><th class="th2">Значение</th><? if($adm){?><th class="th3">&nbsp;</th><th width="1%">Скрытый</th><? }?></tr>
        <? foreach($d as $v){?>
        <tr id="<?=$v['data_id']?>"><td><?=nl2br(Tools::unesc($v['title']))?></td><td><?=Tools::html($v['name'])?></td><td><input type="text" value="<?=Tools::html($v['V'])?>" /></td><? if($adm){?><td nowrap="nowrap"><button class="edit">Изменить</button><button class="del">Удалить</button></td><td><?=$v['H']?'да':'нет'?></td><? }?></tr>
        <? }?>
        </table>
    <? }?>
    <div id="order_discount" style="margin:15px 0 0 0">
    	<fieldset class="ui"><legend class="ui">Таблица скидок для зарегистрированных покупателей</legend>
        	<div style="padding:15px">
                <div id="os_pagered"></div>
                <table id="os_grid" class="scroll" cellpadding="0" cellspacing="0"></table> 
                <fieldset style="margin:20px 0"><div style="padding:15px">
                    <img src="../img/HelpIcon.png" width="50" style="margin-right:20px;" align="left" />
                    <p>Редактирование: левый клик на ячейке с скидкой, изменить значение, нажать клавишу Enter.</p>
                </div></fieldset>
            </div>
        </fieldset>
    </div>
    </div>
    <div id="tabs-7">
		<div style="overflow:hidden">
        	<table id="curval_grid1" cellpadding="0" cellspacing="0"></table> 
        </div>    
    </div>
    <div id="tabs-8">
        <? $d=load(8);
        if($adm){?><div style="margin-bottom:10px;"><button group="8" class="add">Добавить</button></div><? }
        if(count($d)){?>
            <table class="ui-table">
                <tr><th class="th1">Параметр</th><th class="th3">Псевдоним</th><th class="th2">Значение</th><? if($adm){?><th class="th3">&nbsp;</th><th width="1%">Скрытый</th><? }?></tr>
                <? foreach($d as $v){?>
                    <tr id="<?=$v['data_id']?>"><td><?=nl2br(Tools::unesc($v['title']))?></td><td><?=Tools::html($v['name'])?></td><td><input type="text" value="<?=Tools::html($v['V'])?>" /></td><? if($adm){?><td nowrap="nowrap"><button class="edit">Изменить</button><button class="del">Удалить</button></td><td><?=$v['H']?'да':'нет'?></td><? }?></tr>
                <? }?>
            </table>
        <? }?>
    </div>
    <div id="tabs-9">
        <? $d=load(9);
        if($adm){?><div style="margin-bottom:10px;"><button group="9" class="add">Добавить</button></div><? }
        if(count($d)){?>
            <table class="ui-table">
                <tr><th class="th1">Параметр</th><th class="th3">Псевдоним</th><th class="th2">Значение</th><? if($adm){?><th class="th3">&nbsp;</th><th width="1%">Скрытый</th><? }?></tr>
                <? foreach($d as $v){?>
                    <tr id="<?=$v['data_id']?>"><td><?=nl2br(Tools::unesc($v['title']))?></td><td><?=Tools::html($v['name'])?></td><td><input type="text" value="<?=Tools::html($v['V'])?>" /></td><? if($adm){?><td nowrap="nowrap"><button class="edit">Изменить</button><button class="del">Удалить</button></td><td><?=$v['H']?'да':'нет'?></td><? }?></tr>
                <? }?>
            </table>
        <? }?>
    </div>
</form>

</div>

<div style="margin-top:10px;">
	<button class="save">Записать изменения</button>
</div>

<br /><br /><span style="color:red">* Примечание:</span> В качестве значений параметров, предусматривающих ответ ДА/НЕТ, необходимо использовать числовые значения 1/0 соответсвенно.
<? cp_end()?>
