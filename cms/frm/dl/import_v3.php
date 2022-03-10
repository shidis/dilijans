<? 
require_once '../../auth.php';
include('../../struct.php');

$cp->frm['name']='catImport';
$cp->frm['title']='Импорт из программы TyreIndex ver 3.0 [CIv3]';

$cp->checkPermissions();

cp_head();
cp_css();
cp_js();

?>
<style type="text/css">
.tblsrc {
	background:#DDD;
}
.tbldist {
	background:#FC0
}
.import .item .t {
	width:550px;
	height:24px;
	float:left;
}
.import .item .v {
	width:220px;
	height:24px;
	float:left;
}
.import .item .v input {
}
.import .item {
	overflow:hidden;
	width:800px;
	text-align:left;
}
.ui-tabs {
	height:98%;
}
INPUT {
	padding:0 5px 0 5px
}
#info #status {
	color:#C00;
	font-weight:bold
}
.opts {
	margin-top:10px;
	overflow:hidden;
	height:1px;
	width:99%;
}
.opts td, .opts th {
	text-align:left;
	height:20px;
	font-weight:normal;
}
.opts th {
	width:550px;
}
.ui-layout-header{
	padding:	10px 14px;
	border:solid #BBB 1px;
	background:#88B6D9;
	color:#FFFFFF;
}
.ui-layout-content{
	padding:0px;
	margin:0;
	height:auto;
	overflow:hidden;
}

</style>
<script language="javascript">
    var QSID='<?=CU::$sessVarName?>=<?=CU::$SID?>';
</script>
<script language="javascript" src="../../js/comp/dl/import_v3.js"></script>
<? cp_body()?>
<? cp_title(false,false)?>

<? include Cfg::_get('root_path').'/inc/excel/reader.php';
if(!class_exists('Spreadsheet_Excel_Reader')) die('CLASS Spreadsheet_Excel_Reader не существует. Необходимл установить')?>

<div class="ui-layout-north ui-widget-header" style="height:30px">
  <div class="h0">
    <?=$GLOBALS['cp']->frm['title']?>
  </div>
</div>
<DIV class="ui-layout-center ">
  <DIV id="uil-center" class="ui-layout-content"></DIV>
</DIV>
<div class="ui-layout-west">
  <div id="west-center">
    <!--<div class="ui-layout-header  ui-corner-all">Колонки файла</div>-->
    <div id="uil-models" style="overflow:hidden"></div>
  </div>
  <div id="west-south">
    <div class="ui-layout-header  ui-corner-all">Загруженные файлы</div>
    <div id="uil-files" class="ui-layout-content"></div>
  </div>
</div>
<!---------------------------------------->
<div class="import" style="display:none">
<div id="progressbar"></div>
<div id="ahtung" class="ui-widget">
  <div class="ui-state-error ui-corner-all" style="padding: 0pt 0.7em;">
    <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: 0.3em;"></span> <span class="b"><strong>Внимание</strong> </span></p>
  </div>
</div>
<div id="uploadForm" class="ui-widget" style="overflow:hidden;">
  <div class="ui-state-highlight ui-corner-all" style="padding: 10pt 0.7em; overflow:hidden">
    <div  style="float: left; overflow:hidden;" id="_upload">
      <form id="uploadFrm">
        <table>
          <tr>
            <td style="padding-top:10px"><input type="file" id="upload"  /></td>
            <td id="buts" style="padding-left:15px"></td>
          </tr>
        </table>
        <div id="fileQueue"></div>
        <div style="margin-top:15px; font-size:0.8em;"><u>После</u> загрузки файла настройте поля для распознования данных.<br />
          Данные в файле должны располагаться на первом листе (название листа значение не имеет). Поддерживается загрузка файлов в формате XLS и CSV (разделитель - точка с запятой). </div>
        <table class="opts">
          <tr>
            <th><font style="color:red">Удалять</font> отсутствующие в файле <strong>бренды</strong></th>
            <td><input name="delBrandsAbsent" type="checkbox" value="1" /></td>
          </tr>
          <tr>
            <th><font style="color:red">Удалять</font> отсутсвующие в файле <strong>модели</strong></th>
            <td><input name="delModelsAbsent" type="checkbox" value="1"  /></td>
          </tr>
          <tr>
            <th><font style="color:red">Удалять</font> отсутствующие в файле <strong>типоразмеры</strong></th>
            <td><input name="delTiposAbsent" type="checkbox" value="1" /></td>
          </tr>
          <tr>
            <th>Обнулять складской остаток у отстуствующих в файле размеров</th>
            <td><input name="resetAbsent" type="checkbox" value="1" /></td>
          </tr>
          <tr>
            <th>Скрывать с сайта типоразмеры, отсутсвующие на складе</th>
            <td><input name="hideZeroTipo" type="checkbox" value="1" /></td>
          </tr>
          <tr>
            <th>Скрывать с сайта типоразмеры, модели и бренды, отстутсвующие на складе <font style="color:red">*</font></th>
            <td><input name="hideZero" type="checkbox" value="1" /></td>
          </tr>
          <tr>
            <th>Автоматически выключать статус скрытости для брендов, моделей, размеров, присутствующих на складе</th>
            <td><input name="hideOff" type="checkbox" value="1" /></td>
          </tr>
          <tr>
            <th>Автоматически скрывать с сайта типоразмеры, имеющие статус "Пропустить"</th>
            <td><input name="hideMiss" type="checkbox" value="1" /></td>
          </tr>
          <tr>
            <th>Обновлять цены</th>
            <td><input name="updatePrices" type="checkbox" value="1" /></td>
          </tr>
          <tr>
            <th>Обновлять остатки</th>
            <td><input name="updateStock" type="checkbox" value="1" /></td>
          </tr>
          <tr id="replicaBrand_sw" style="display:none">
            <th>Названия реплики в файле (через зяпятую) <font style="color:red">*</font></th>
            <td><input name="replicaBrand" type="text" style="width:100%" value="" /></td>
          </tr>
          <tr id="tyresSuffixes_sw" style="display:none">
            <th>Извлекаемые из полного размера суффиксы шин (через запятую)</th>
            <td><textarea name="tyresSuffixes"  class="ui-corner-all" style="width:99%; height:45px"></textarea></td>
          </tr>
        </table>
        <fieldset class="ui" style="padding:5px; margin-bottom:10px; margin-top:5px;">
          <legend class="ui" style="margin-bottom:10px">Настройки обновления только для связанных размеров</legend>
          <div class="item" id="updateTyresSuffix_sw">
            <div class="t">Обновлять суффикс шин <font style="color:red">*</font></div>
            <div class="v">
              <input name="updateTyresSuffix" type="checkbox" value="1" />
            </div>
          </div>
        </fieldset>
        <div class="item">
          <div class="t">Максимальное кол-во хранимых файлов в списке</div>
          <div class="v">
            <input name="maxFileList" type="text" style="width:50px; text-align:center" value="" />
          </div>
        </div>
        <p>В этой версии модуля импорта привязка к IDBRAND не производится. В Связка бренда в базе сайта будет всегда равна нулю.<p>
        <p><font style="color:red">*</font>Программа работает только с рублями. При обновлении цен валюта автоматически будет переустановлена на рубли<br />
          <font style="color:red">*</font>Не удаляйте извлекаемые из полного названия суффиксы для шин, иначе, в случае установки опции "Обновлять суффикс шин", убранные суффиксы исчезнут из базы сайта. Убирайте их, если вы точно уверены, что они не должны присутствовать в типоразмере.<br />
          <font style="color:red">*</font>Если вы импортируете реплику, поле "Название реплики в файле" не должно быть пустым. Иначе вся реплика будет загружена в один бренд. В файлах ТайрИндекс в качестве бренда реплики используется название Replica", оно и должно быть в этом поле. Если в загружаемом файле реплика идет под несколькими брендами - перечислите их через зяпятую.<br />
          <font style="color:red">*</font>Опции "<em>Скрывать с сайта типоразмеры, модели и бренды, отстутсвующие на складе</em>",       	  &quot;<span class="t"><em>Скрывать с сайта типоразмеры, отсутсвующие на складе</em></span>&quot;                      корректно будет работать только вместе с включенной опцией "<em>Обнулять складской остаток у отстуствующих в файле размеров</em>"<br />
          <font style="color:red">*</font>Все настройки на этой вкладке сохраняются после парсинга или импорта файла. <br />
          <font style="color:red">*</font>Для типоразмеров со статусом "фиксированная цена" будет изменена базовая цена, но розница, при следующем пересчет цен. останется прежней<br />
          <font style="color:red">*</font>Для типоразмеров со статусом "не обновлять цену и склад при импорте из внешних источников" бедет установлен статус "игнор", базовая цена и склад обновлен не будет<br />
        </p>
        <p>.
      </form>
    </div>
  </div>
</div>
<div id="info" style="margin-bottom:20px; position:static">
  <div class="ui-state-default ui-corner-all" style="padding: 10pt 0.7em;">
    <div class="item">
      <div class="t">Новых / связанных производителей</div>
      <div class="v">
        <div id="brands"><span>?</span> / <span>?</span></div>
      </div>
    </div>
    <div class="item">
      <div class="t">Новых / связанных / перемещенных  моделей</div>
      <div class="v">
        <div id="models"><span>?</span> / <span>?</span> / <span>?</span></div>
      </div>
      <div class="item">
        <div class="t">Новых / связанных / перемещенных / обновленных  типоразмеров</div>
        <div class="v">
          <div id="tipos"><span>?</span> / <span>?</span> / <span>?</span>/ <span>?</span></div>
        </div>
      </div>
      <div class="item">
        <div class="t">С не нулевой ценой</div>
        <div class="v">
          <div id="notZeroPriceNum"></div>
        </div>
      </div>
      <div class="item">
        <div class="t">С не нулевым остатком</div>
        <div class="v">
          <div id="notZeroSkladNum"></div>
        </div>
      </div>
      <div class="item">
        <div class="t">Строк в файле (без заголовка)</div>
        <div class="v">
          <div id="rows"></div>
        </div>
      </div>

      <div class="item">
        <div class="t">Проигнорированных размеров</div>
        <div class="v">
          <div id="c_code5"></div>
        </div>
      </div>
      <div class="item">
        <div class="t">Проблемных брендов/моделей/типоразмеров</div>
        <div class="v">
          <div id="code6"><span>?</span> / <span>?</span> / <span>?</span> </div>
        </div>
      </div>


      <div class="item">
        <div class="t"><strong>Статус файла</strong></div>
        <div class="v">
          <div id="status"></div>
        </div>
      </div>
      <div id="comment" style="padding-top:15px; display:none"></div>
    </div>
  </div>
</div>
<? cp_end(false)?>
