<? 
require_once '../auth.php';
include('../struct.php');


$cp->frm['name']='tc2';
$cp->frm['title']='Транспортные компании и доставка';

$cp->checkPermissions();

cp_head();
cp_css();
cp_js();
?>
<style type="text/css">
#co_info{
	margin:10px 0; width:100%; display:none; border:1px dashed blue;
}
</style>

<? cp_body()?>
<? cp_title()?>

<div id="tabs">

	<ul>
    	<li><a href="#tab-1">Транспортные компании</a></li>
    	<li><a href="#tab-2">Все города</a></li>
    </ul>
    
    <div id="tab-1">
    	
        <select id="co_id"></select>
        <button id="new_co_but">Добавить ТК</button>

        <div id="co_info">
            Выбрана ТК: &quot;<strong><span id="name"></span></strong>
            &quot;&nbsp;
            <button id="co_edit">Изменить компанию</button>
            &quot;&nbsp;
            <button id="del_co_but" >Удалить ТК</button>
            &quot;&nbsp;
            <button id="co_disable"></button>
        </div>
        <div id="co_dlg">
        	<form>
                <div class="row">
                    <label for="co_name">Название компании:</label>
                    <input type="text" style="width:150px" id="co_name" name="co_name">
                </div>
                <div class="row">
                    <label for="co_site">Сайт:</label>
                    <input type="text" style="width:150px" id="co_site" name="co_site">
                </div>
                <div class="row">
                    <label for="co_dSumMin">Суммарная надбавка  к стоимости перевозки груза, руб:</label>
                    <input type="text" style="width:80px" id="co_dSumMin" name="co_dSumMin">
                </div>
                <div class="row">
                    <label for="co_dSumM3">Суммарная надбавка  к стоимости перевозки груза, руб - за куб:</label>
                    <input type="text" style="width:80px" id="co_dSumM3" name="co_dSumM3">
                </div>
            </form>
        </div>
        
        <div id="co_cities"></div>
	</div>
    
    <div id="tab-2">
    
    	<div id="all_cities"></div>
        
	</div>
</div>

<? cp_end()?>
