<? 

require_once '../auth.php';
include('../struct.php');

$cp->frm['name']='galleries';
$cp->frm['title']='Галереи изображений';

$cp->checkPermissions();

cp_head();
cp_css();
cp_js();
?>


<style type="text/css">
.gals{
	position:relative;
	overflow:visible;
	display:block;
	-border:1px solid red;
}
.gals #imgs{
	position:relative;
	margin-left:300px;
	overflow:hidden;
	min-width:500px;
}
.gals #nav{
	position:absolute;
	left:0px;
	top:0px;
	width: 270px;
	overflow:hidden;
	border-right:1px solid #CCC;
}
.gals #upl{
	margin:10px 0;
}
.gals #nav #gselect div{
	margin:0 0 5px 0;
	display:block;
}
.gals #edit, .gals #glist, .gals #del, .gals #upl{
	display:none;
}
.gals #tinfo {
	margin: 10px 0;
	color:#666;
	font-style:italic;
}
#gals_editWin input, #gals_editWin textarea, #gals_iEditWin input, #gals_iEditWin textarea{width:100%}
#gals_editWin div, #gals_iEditWin div {margin-top:10px; display:block;}
#gals_editWin fieldset{
	margin-top:10px;
}
.gals #imgs ul{
	list-style:none;
	margin:0;
	padding:0;
}
.gals #imgs ul li{
	display:inline-block;
	vertical-align:top;
	margin:10px;
	position:relative;

}
.gals #imgs ul li .i{
	overflow:hidden;
	display:block;
	border:1px solid #06F;
}
.gals #imgs ul li .c{
	position:absolute;
	overflow:visible;
	height:23px;
	width:60px;
	left:1px;
	top:1px;
	background:#FFF;
}
.gals #imgs ul li .c input{
	margin:2px 6px 2px 6px;
}
</style>
<script>
    var QSID='<?=CU::$sessVarName?>=<?=CU::$SID?>';
</script>

<? cp_body()?>
<? cp_title()?>

<div class="gals">
	<div id="imgs">
    
    </div>
    <div id="nav">
        <div id="gselect">
            <div><select id="glist" class="ui-widget-content ui-corner-all"></select></div>
            <div id="tinfo">

            </div>
            <button id="edit">Изменить</button>
            <button id="add">Добавить галерею</button>
            <button id="del">Удалить галерею</button>
        </div>
        <div id="upl">
            <input type="file" id="upload" name="upload"  /><div id="fileQueue"></div>
        </div>
    </div>
    
    <div id="gals_editWin" title="Редактировать">
    
        <form>
            <div>
            <label>Название</label> <input type="text" name="name" class="text ui-widget-content ui-corner-all" />
            </div>
            <div>
            <label>Системное имя<br /><sub>(участвует в формировании имени файла, остаьте пустым для автоматической генерации)</sub></label> <input type="text" name="sname" class="text ui-widget-content ui-corner-all" />
            </div>
            <div>
            <label>Описание</label> <textarea name="info" style="height:50px" class="text ui-widget-content ui-corner-all"></textarea>
            </div>
            <fieldset class="ui-widget-content ui-corner-all"><legend class="ui">Настройки конвертаци изображения #1</legend>
            <div>
            <label>Режим конвертации </label>
            <select name="img1_resize_mode" class="text ui-widget-content ui-corner-all">
            	<option value="SO">Только уменьшать</option>
                <option value="BW">Фиксированная ширина</option>
                <option value="BH">Фиксированная высота</option>
            </select>
            </div>
            <div>
            <label>Максимальная ширина, px</label> <input type="text" name="img1_w" style="width:60px;" class="text ui-widget-content ui-corner-all" />
            </div>
            <div>
            <label>Максимальная высота, px</label> <input type="text" name="img1_h" style="width:60px;" class="text ui-widget-content ui-corner-all" />
            </div>
            </fieldset>
            <fieldset class="ui-widget-content ui-corner-all"><legend class="ui">Настройки конвертаци изображения #2</legend>
            <div>
            <label>Режим конвертации </label>
            <select name="img2_resize_mode" class="text ui-widget-content ui-corner-all">
            	<option value="SO">Только уменьшать</option>
                <option value="BW">Фиксированная ширина</option>
                <option value="BH">Фиксированная высота</option>
            </select>
            </div>
            <div>
            <label>Максимальная ширина, px </label> <input type="text" name="img2_w" style="width:60px;" class="text ui-widget-content ui-corner-all" />
            </div>
            <div>
            <label>Максимальная высота, px </label> <input type="text" name="img2_h"style="width:60px;" class="text ui-widget-content ui-corner-all" />
            </div>
            </fieldset>
            
            
        </form>
    
    </div>
    
    <div id="gals_iEditWin" title="Редактировать изображение">
    
        <form>
            <div>
            <label>Заголовок</label> <input type="text" name="title" class="text ui-widget-content ui-corner-all" />
            </div>
            <div>
            <label>Альтернативное название</label> <input type="text" name="alt" class="text ui-widget-content ui-corner-all" />
            </div>
            <div>
            <label>Ссылка (http://...)</label> <input type="text" name="link" class="text ui-widget-content ui-corner-all" />
            </div>
            <div>
            <label>Описание 1</label> <textarea name="info1" style="height:50px" class="text ui-widget-content ui-corner-all"></textarea>
            </div>
            <div>
            <label>Описание 2</label> <textarea name="info2" style="height:50px" class="text ui-widget-content ui-corner-all"></textarea>
            </div>
        </form>
    
    </div>
    
</div>

<? cp_end()?>
