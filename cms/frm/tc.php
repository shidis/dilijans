<? 
require_once '../auth.php';
include('../struct.php');


$cp->frm['name']='tc';
$cp->frm['title']='Транспортные компании';

$cp->checkPermissions();

cp_head();
cp_css();
cp_js();
?>
<style type="text/css">
div.clist{overflow:hidden}
div.clist div.c { margin:3px 0;border:1px solid green; overflow:hidden}
div.clist div.l{float:left; width:200px; overflow:hidden; margin:3px}
div.clist input{margin:3px 3px 0 10px; float:right}
</style>

<? cp_body()?>
<? cp_title()?>

<div  style=" width:99%">
  <div style="overflow:visible; width:700px; float:left"> Список транспортных компаний:
    <select id="company_id">
    </select>
    <br>
    <div id="co_info" style="margin:10px 0; width:100%; display:none; border:1px dashed blue;"><div style="margin:10px">Выбрана ТК: &quot;<strong><span id="name"></span></strong>&quot;&nbsp;<input type="image" src="../img/edit.gif" id="co_name_edit" />&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;Сайт ТС: <a id="site" href="#"></a>&nbsp;
      <input type="image" src="../img/edit.gif" id="co_site_edit" />
      &nbsp;
      <input id="del_co_but" type="image" value="удалить ТК" src="../img/delete.gif"><br />
	  Отображать ТК на сайте: <a id="co_disable" href="#"></a>
    </div></div>
    Добавить новую компанию
    <input type="text" style="width:150px" id="new_co">
    cайт:
    <input type="text" style="width:150px" id="new_co_site">
    <input id="new_co_but" type="button" value="добавить">
    <div id="new_cities" style="display:none">
    <p>Добавить города к ТК (по одному в строке):</p>
    <textarea id="cities" style="width:100%; height:100px"></textarea>
    <br>
    <input type="button" value="Добавить города" id="add_cities">
    </div>
  </div>
      <div style="margin-left:20px; float:left ">
        <input type="checkbox" id="confirm_delete" value="1" checked="checked" />Подтверждать удаление<br clear="left" />
        <div id="city_list" style="float:left; margin-top:20px;"></div>
      </div>
</div>
<? cp_end()?>
