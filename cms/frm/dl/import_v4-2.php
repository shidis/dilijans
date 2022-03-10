<?
require_once '../../auth.php';
include('../../struct.php');

$cp->frm['name']='catImportII2';
$cp->frm['title']='Импорт из программы TyreIndex (ver.4.2 [CII2])';

$cp->checkPermissions();

cp_head();
cp_css();
cp_js();


?>
<style type="text/css">
    table.struct td{
        vertical-align:top;
    }
    #left-area{
        width:290px;
        margin-right:10px;
        overflow:hidden;
        margin-left:10px;
    }
    #right-area{
        overflow:hidden;
    }
    #right-area fieldset{
        display:block;
        padding:10px;
    }
    #left-area .ui-widget-content{
        padding:10px;
    }

    #left-area  .upload{
        margin-bottom:10px;
    }
    #left-area  .upload small{
        margin-top:7px;
        display:block;
    }
    .uploadifyQueueItem{
        width:228px;
    }
    #fileList .ui-selecting { background: #ccc; }
    #fileList .ui-selected { background: #70A8D2; color: white; }
    #fileList { list-style-type: none; margin: 0; padding: 0; width: 100%; }
    #fileList li { margin: 3px;  vertical-align:middle; font-size:0.9em; height: 18px; cursor:pointer; background:#E1EFFB; position:relative; overflow:hidden }

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
        width:99%;
    }
    .opts td, .opts th {
        text-align:left;
        height:20px;
        font-weight:normal;
    }

    .opts th {
        width:40%;
        padding-right:20px;
        padding-bottom:15px;
    }
    .ui-header{
        padding:0 10px 0 10px;
        height:30px;
        line-height:30px;
        display:inline-block;
        vertical-align:middle;
    }
    #selectedFile{
        width:100%;
        margin-bottom:10px;
    }
    fieldset{
        margin-bottom:10px;
    }
    table{
        border-collapse:seprate;
    }
    .workspace{
        padding-left:0;
    }
</style>
<? mt_srand();?>
<script language="javascript">

    var QSID='<?=CU::$sessVarName?>=<?=CU::$SID?>';
    var ciSID='<?=mt_rand()?>';
    //	ciSID='317046932';

</script>

<script src="/cms/js/comp/dl/import_v4-2.js" type="text/javascript"></script>

<? cp_body()?>
<? cp_title()?>

<div class="ci">

    <div id="loading" style="text-align:center" title="Подождите..."></div>
    <div id="errWin" title="Ошибка программы"></div>
    <div id="progress" style="text-align:center" title="Подождите..."><div class="pb"></div><div class="t" style="margin:15px 0 0; text-align:center; font-weight:bold"></div></div>
    <div id="brandsWin" title="Настройка параметров импорта: производители"></div>
    <div id="diaWin" title="Таблица преобразований ступиц (DIA) дисков"></div>
    <div id="svWin" title="Таблица преобразований сверловок LZxPCD"></div>

    <table width="100%" class="struct">
        <tr><td>
                <div id="left-area">

                    <div class="ui-widget-content upload ui-corner-all">

                        <input type="file" id="upload"  />
                        <div id="fileQueue"></div>
                        <small>Поддерживается формат файлов CSV и XLS</small>
                        <small>Внимание! Загрузка xls файлов более 2МB может вызвать ошибку нехватки памяти</small>
                    </div>

                    <div class="ui-widget-header ui-corner-top">
                        <div class="ui-header">Сохраненные файлы</div>
                    </div>

                    <div class="ui-widget-content ui-corner-bottom">

                        <ol id="fileList"></ol>

                    </div>

                </div>

            </td><td width="100%">

                <div id="right-area">

                    <div id="mtabs">

                        <ul>
                            <li><a href="#tab-1">Импорт</a></li>
                            <li><a href="#tab-2">Описание работы модуля</a></li>
                        </ul>

                        <div id="tab-1">

                            <div id="selectedFile" class="ui-widget-content ui-header">Для начала работы выберите загруженный или загрузите новый файл</div>

                            <fieldset class="ui-state-highlight ui" id="importSetup"><legend class="ui">Настройки импорта</legend>

                                <button id="showBrandsWin" style="display:none">Необновляемые бренды</button>
                                <button id="showDIAWin" style="displays:none">Таблица преобразований DIA</button>
                                <button id="showSVWin" style="displays:none">Таблица преобразований LZxPCD</button>

                                <form id="configFrm">
                                    <table class="opts">
                                        <tr>
                                            <th>Названия бренда реплики в файле (через зяпятую)</th>
                                            <td><input name="replicaBrand" type="text" style="width:98%" value="" />
                                                <small>если оставить пустым, то будет создан новый бренд Replica и в него запишутся все модели реплики</small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Метод вычисления цены</th>
                                            <td><select name="pricing">
                                                    <option value="1">Ценообразование на сайте</option>
                                                </select></td>
                                        </tr>
                                        <tr>
                                            <th>Максимальное кол-во хранимых файлов в списке (по умолчанию 30)</th>
                                            <td><input name="maxFileList" type="text" style="width:50px; text-align:center" value="" /></td>
                                        </tr>
                                    </table>

                                    <button id="testImport" style="display:none">Тестовый импорт файла</button>
                                    <button id="goImport" style="display:none; color:red">Импортировать файл</button>
                                </form>
                            </fieldset>

                            <div id="view-grid"></div>

                        </div>

                        <div id="tab-2">
                            <p><strong>Логика поиска и сравнения</strong></p>
                            <p>*) В базе сайта у шин не должно быть в поле суффикса значений ZR - этот параметр извлекается из полного названия шины и на этапе импорта отображается как ZR в поле "суффикс"<br />
                                **) в качестве десятичного разделителя используется точка.
                                <br />
                                ***) Файл из ТИ должен содержать информацию о складских остках по поставщикам.<br />
                                <!--***) для дисков реплика автоматически определяется поставщик по формату написания модели: "H-" - replica H, "R[цифры]" - replica WSP, "[Буква][цифры]" - replica LS, "только цифры и слеш" - replica FR
                                    <br />
                                    ****) Replica FR (цифры) будут распознаны как бренд FR Design</p>-->
                            <p>Если в качестве бренда программа встретит одно из перечисленных в поле <strong>&quot;Названия бренда реплики в файле&quot;</strong> значений (для дисков), модель будет счиаться моделью реплики и название модели будет обработано по специальному алгоритму: <br />
                                <em>значение слева от скобок - название бренда реплики, <br />
                                    значение в скобках - название модели диска. <br />
                                    Значение справа от скобок записывается в поле применяемости (отображается в таблице импорта с символами @@)</em>
                            <p>Для дисков применяется алгоритм: </p>
                            <p><em>0. При поиске моделей и типоразмеров используется матрица цветов</em>.<br />
                                <em>1. проверяем сначала наличие модель+цвет, если есть, то провеяем в ней размер+цвет, если нет размера+суффикс то помещаем в модель+цвет размер+цвет, если нет модель+цвет, то п.2<br />
                                    2. если нет модель+цвет, то проверяем наличие модель-без-цвета и в ней размер+цвет, если нет размер+цвет, то добавляем размер+цвет, если нет модели-без-суффикса, то добавляем модель и в нее добавляем размер+цвет</em><br />
                                <em>*. Если в результате всех сравнений типоразмер диска не найден и для его цвета есть связка с базовым суффиксом в матрице цветов, то он будет добавлен с базовым суффиксом. </em><br />
                                <br />
                                При проверке существования типоразмера <strong>диска</strong> испльзуются параметры: диаметр, ширина, вылет, сверловка, диаметр ступицы (с использованием альтернатив из "Таблицы диаметров ступиц", цвет (с использованием матрицы соотвеветсвия цветов)<br /><br />
                                При проверке существования типоразмера <strong>шины</strong> испльзуются параметры: ширина, высота, радиус, ин/ис, суффиксы. В ТИ не все суффиксы указываются в колнке &quot;суффикс&quot;, поэтому при импорте из &quot;полного названия&quot; шины будут извлекаться суффиксы, заданные в словаре на сайте.</p>
                            <p>Такие парметры как сезонность/шиповка, тип авто для шин и  тип диска при поиске соответствия не используются.</p>
                            <p>В настройках импорта для брендов можно отметить те бренды, для которых будет проигнорирован расчет цен, суммируются по поставщикам только складской остаток.</p>
                            <p><strong>Логика работы с ценами:</strong><br />
                                *)  В импортируемом файле  допустимая валюта - рубль.</p>
                            <p><strong>Термины и определения</strong></p>
                            <p>На сайте для каждого типоразмера есть три значения, используемые для ценообразования:</p>
                            <ol>
                                <li>Базовая цена - цена, к которой применяется таблица наценок для расчета розничной цены</li>
                                <li>Розничная цена - цена, отображаемая на сайте</li>
                                <li>Фиксированная цена - розничная цена на сайте не подвергается пересчету по наценкам, выводится как есть.</li>
                            </ol>
                            <p><br />
                                <strong>Для установки цен и остатков на сайте используем алгоритм:</strong></p>
                            <p>*) Если для типоразмера в общем каталоге установлен статус &quot;не обновлять цену и склад при импорте из внешних источников&quot;, то для него цена и сумма остатков по складам поставщиков не будут обновлены, но таблица наличия и цен по постащикам будет перезагружена. Для типоразмеров с этим статусом в отчете будет фигурировать статус &quot;игнор&quot;.<br />
                                **) Цена меньше 500 рублей в колонках загружаемого файла приравнивается к нулевой	.<br />
                                ***)
                                При пересчете розничных цен на сайте, позиции со статусом &quot;Фиксированная цена&quot; игнорируются.</p>
                            <p>Для каждого типоразмера:</p>
                            <p>1. Складские остатки суммируются по  поставщикам этого типоразмера, у которых кол-во на складе больше или равно 1.<br />
                                2. Проходом по всем поставщикам со складским остатком &gt;=1, определяется минимальное ненулевое значение из колонки Розница. Это значение записывается для типоразмера как базовая цена. Розничная цена на сайте изменится при следующем пересчете цен.<br />
                                3. Если для типоразмера нет ни одного поставщика со складом &gt;=1. то складской остаток этого типоразмера
                                обнуляется, базовая цена рассчитывается как минимальная от всех поставщиков, с наличием на складе &gt;0.<br />
                                <br />
                                <br />
                            </p>
                        </div>

                    </div>

                </div>

            </td></tr>
    </table>
</div>

<? cp_end()?>
