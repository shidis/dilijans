<? 
require_once '../auth.php';
include('../struct.php');

$cp->frm['name']='datasets';
$cp->frm['title']='Наборы данных';

$cp->checkPermissions();

cp_head();
cp_css();
cp_js();

?>
<? cp_body()?>
<? cp_title()?>
<style type="text/css">
	fieldset{
		margin:10px 10px;
	}
	
	.incFrm table{
		border-collapse:collapse;
	}
	.incFrm td{
		vertical-align:bottom;
	}
	.incFrm label{
		display:block;
		margin:3px 0;
	}
	.incFrm input{
		width:100%;
	}
	.incFrm textarea{
		width:100%;
		height:100px;
	}
	.row{
		margin:10px;
		overflow:hidden;
		display:block;
	}
	.row label{
		display:block;
		margin:5px 0 0 0;
	}
	.row input{
		width:300px;
		margin-right:10px;
	}
	.row select{
		margin-right:10px;
		width:170px;
	}
	.fs-class legend{
		display:block;
	}
		
</style>

<?
$ds=new CC_Dataset();
if(!count($ds->classes)) {
	?><div class="ui-widget">
        <div class="ui-state-error ui-corner-all" style="padding: 0pt 0.7em;"> 
            <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: 0.3em;"></span> 
            Классы наборов недоступны. Необходимо подключение.</p>
        </div>
    </div><?
	cp_end();
	exit;
}
//if($ds->classExists('YM')) $ds->classInstance('YM');
//print_r($ds->c['YM']->cc());



?>
<div id="ds-form" class="edit_area" style="display:none">
	
        <div class="ui-buttonset" id="ds-class"><fieldset class="ui fs-class"><legend>Выбор класса набора</legend><div style="padding:10px">
            <? $i=0;
                foreach($ds->classes as $classId=>$class){
                $i++;
                ?><input class="ui-helper-hidden-accessible" id="class-<?=$classId?>" name="class" type="radio"<?=$i==1?' checked="checked"':''?>><label aria-disabled="false" role="button" class="ui-button ui-widget ui-state-default ui-button-text-only<?=$i==1?' ui-corner-left ':''?><?=$i==count($ds->classes)?' ui-corner-right ':''?>" aria-pressed="false" for="class-<?=$classId?>"><span class="ui-button-text"><?=$class['name']?></span></label><?
            }?></div></fieldset>
        </div>

	<? $i=0;
	foreach($ds->classes as $classId=>$class){
		$i++;
		if(!file_exists(Cfg::$config['root_path'].'/app/templates/datasets/'.$classId.'.php')){
			?><div class="ui-widget"><div class="ui-state-error ui-corner-all" style="padding: 0pt 0.7em;"><p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: 0.3em;"></span>Не найден файл формы класса <?='/app/templates/datasets/'.$classId.'.php'?> </p></div></div><?
		}else {
			?><div>
            	<form class="dsf" style="<?=$i>1?'display:none':'display:block'?>" id="dsf-<?=$classId?>">
                	<input type="hidden" name="dataset_id" value="0" />
                	<fieldset class="ui">
                    	<div class="row"><label>Имя набора</label><input type="input" name="name" /></div>
                    	<div class="row"><label>Системное имя набора (для формирования имени файла) <br />Если пустое, то сформирутся автоматически</label><input type="input" name="sname" /></div>
                        <div class="row"><label>Класс набора</label><input type="input" name="class" value="<?=$classId?>" /></div>
                    </fieldset>
                    <div class="incFrm ui"><? include(Cfg::$config['root_path'].'/app/templates/datasets/'.$classId.'.php')?></div>
                </form>
              </div><?
		}
	}?>
		
    <button class="ds-save">Записать изменения</button>  
    <button class="ds-hideForm">Скрыть форму</button>
    	
</div>


<div id="ds-list">

	 <? $d=$ds->datasetList();
	 if(count($d)){
		 ?><button style="margin:10px 0;" class="ds-add">Добавить набор</button>
         <table class="ui-table"><tr><th colspan="3">Управление</th><th>Имя набора</th><th>Класс набора</th><th>Дата добавления</th><th>Кол-во размеров в наборе</th><th  class="last">Ссылка на файл данных</th><th>Отладочная ссылка</th></tr><?
		 foreach($d as $v){
			 ?><tr c="<?=$v['class']?>" dataset_id="<?=$v['dataset_id']?>">
             	<td><button class="ds-edit">Изменить</button></td>
             <td><button class="ds-del">Удалить</button></td>
             <td><button class="ds-clear">Очистить</button></td>
                <td><?=Tools::unesc($v['name'])?></td>
                <td><?=$ds->classes[$v['class']]['name']?></td>
                <td align="center"><?=Tools::sdate($v['dt_added'])?></td>
                <td align="center"><?=$v['catNum']?></td>
                <td><? if($ds->classes[$v['class']]['ext']!=''){?><a href="http://<?=Cfg::get('site_url').'/'.Cfg::get('datasetDir').'/'.$v['sname'].'.'.$ds->classes[$v['class']]['ext']?>" target="_blank">http://<?=Cfg::get('site_url').'/'.Cfg::get('datasetDir').'/'.$v['sname'].'.'.$ds->classes[$v['class']]['ext']?></a><? }else echo 'нет'?></td>
                <td><? if($ds->classes[$v['class']]['ext']!=''){?><a href="http://<?=Cfg::get('site_url').'/'.Cfg::get('datasetDir').'/'.$v['sname'].'.'.$ds->classes[$v['class']]['ext']?>?debug" target="_blank">тынц</a><? }else echo 'нет'?></td>
              </tr><?
		 }?>
	    </table>
    <? }else{?>
   		<div class="ui-widget"><div class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0pt 0.7em;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right: 0.3em;"></span>Наборов нет.</p></div></div>
    <? }?>
   <button style="margin-top:15px;" class="ds-add">Добавить набор</button>
    
        
        
</div>

<? cp_end()?>
