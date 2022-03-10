<? 
require_once '../../auth.php';
include('../../struct.php');

$cp->frm['name']='catImportII';
$cp->frm['title']='Импорт из программы TyreIndex [CIIdm]';

$cp->checkPermissions();

if(!MC::chk()){
    die('MC не запущен.');
}
cp_head();
cp_css();
cp_js();


?>
    <style type="text/css">
        table.struct td{
            vertical-align: top;
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

        #right-area .wrap{
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
        .opts input{
            box-sizing: border-box;
        }
        .opts td, .opts th {
            text-align:left;
            font-weight:normal;
            border-bottom: 1px solid #CCC;
            vertical-align: middle !important;
        }

        .opts th {
            width:40%;
            padding-right:20px;
            padding-bottom:5px;
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
            border-collapse:separate;
        }
        .workspace{
            padding-left:0;
        }
        #info{
            display:none;
            margin-bottom:10px;
            padding:0 15px;
        }
        #sync{
            display:none;
            margin-bottom:10px;
            padding:0 15px;
        }
        #tab-history table{
            border-collapse: collapse;
        }
        #tab-history table td,  #tab-history table th{
            border: 1px dashed #CCC;
            padding: 3px 10px;
        }
        #tab-history table th{
            text-align: left;
        }

        #dm-cfg table th{
            text-align: left;
        }
        #dm-cfg table th, #dm-cfg table td{
            padding: 5px 20px 5px 0;
        }
        #DM_task .cp{
            overflow: hidden;
        }
        #DM_task .row{
            overflow: hidden;
        }
        #DM_task .it{
            overflow: hidden;
            margin-bottom: 10px;
        }
        #DM_task .it > .l{
            display: inline-block;
            margin-right: 15px;
            font-weight: bold;
        }
        #DM_task .it > .r{
            display: inline-block;
        }

        #DM_task{
            width:100%;
            margin-bottom:10px;
            overflow: hidden;
        }

        #delSuplrsWin .spWinList div{
            margin: 0 0 4px;
        }
        #delSuplrsWin .spWinList label{
            margin-left: 10px;
        }
    </style>


<? cp_body()?>
<? cp_title()?>

    <div class="ci">

        <div id="loading" style="text-align:center" title="Подождите..."></div>
        <div id="errWin" title="Ошибка программы"></div>

        <div id="diaWin" title="Таблица преобразований ступиц (DIA) дисков"></div>
        <div id="svWin" title="Таблица преобразований сверловок LZxPCD"></div>
        <div id="YSTWin" title="Наценки на диски для поставщика Яршинторг"></div>

        <table width="100%" class="struct">
            <tr><td class="left">
                    <div id="left-area">
            
                        <div class="ui-widget-content upload ui-corner-all">
                
                            <form id="uploadForm" enctype="multipart/form-data"><input type="file" id="upload"  /></form>
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
                                <li><a href="#tab-info">Описание работы модуля</a></li>
                                <li><a href="#tab-3">Обслуживание базы</a></li>
                                <li><a href="#tab-history">История</a></li>
                                <li><a href="#dm-cfg">Состояние</a></li>
                            </ul>
                    
                            <div id="tab-1">
                    
                                <div id="selectedFile" class="ui-widget-content ui-header">Для начала работы выберите загруженный или загрузите новый файл</div>

                                <div id="DM_task" style="display: none">
                                    <fieldset class="ui"><legend class="ui">Состояние выполнения задачи</legend>
                                        <table style="width: 100%">
                                            <tr>
                                                <td width="50%" style="padding-right: 20px">

                                                    <div class="dm_task_pb" style="width: 100%;"></div>
                                                    <div class="dm_task_label" style="margin:15px 0 0; text-align:center; font-weight:bold"></div>
                                                    <div class="cp" style="margin: 15px 0 0">
                                                        <div class="row">
                                                            <div class="it">
                                                                <div class="l">Состояние задачи</div>
                                                                <div class="r"><span class="dm_task_state">-</span></div>
                                                            </div>
                                                            <div class="it">
                                                                <div class="l">Время начала выполенения / прошло времени</div>
                                                                <div class="r"><span class="dm_task_t_run">-</span> / <span class="dm_task_elapsed">-</span></div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="it">
                                                                    <div class="l">Занимаемая память</div>
                                                                    <div class="r"><span class="dm_memUsage">-</span></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="butttons">
                                                            <button class="dm_pause">Приостановить выполнение </button>
                                                            <button class="dm_task_stop">Прервать задачу</button>
                                                        </div>
                                                    </div>

                                                </td>
                                                <td>
                                                    <textarea class="dm_task_log" disabled style="width: 100%; resize: vertical; height: 100%; box-sizing: border-box; padding: 6px 10px; min-height: 180px"></textarea>
                                                </td>
                                            </tr>
                                        </table>
                                    </fieldset>
                                </div>
        
                                <div class="ui-widget-content wrap">
                                    <div id="sync" class="ui-state-active ui ui-corner-all"></div>
                                    <div id="info" class="ui-state-highlight ui ui-corner-all"></div>
                                    <fieldset class="ui-state-highlight ui" id="importSetup"><legend class="ui">Настройки импорта</legend>

                                        <button id="showDIAWin" style="displays:none">Таблица преобразований DIA</button>
                                        <button id="showSVWin" style="displays:none">Таблица преобразований LZxPCD</button>
                                        <button id="showYSTWin" style="displays:none">Ценообразование ЯШТ</button>

                                        <form id="configFrm">
                                            <table class="opts">
                                                <!--
                                                <tr>
                                                    <th>Названия бренда реплики в файле (через зяпятую)</th>
                                                    <td><input name="replicaBrand" class="autosave" type="text" style="width:100%" value="" />
                                                        <small>если оставить пустым, то будет создан новый бренд Replica и в него запишутся все модели реплики</small>
                                                    </td>
                                                </tr>
                                                -->
                                                <tr>
                                                    <th>Базовый цвет диска по умолчанию для загружаемых позиций с пустым значением цвета</th>
                                                    <td><input name="emptyDiskSuffix" class="autosave" type="text" style="width:100%;" value="" /></td>
                                                </tr><tr>
                                                    <th>Список цветов (для дисков) в загружаемом файле, которые будут игнорироваться при поиске соответствий в базе сайта (через запятую)</th>
                                                    <td><input name="ignoreDiskSuffixes" class="autosave" type="text" style="width:100%;" value="" /></td>
                                                </tr>
                                                <tr>
                                                    <th>Варианты написания RunFlat (через зяпятую)</th>
                                                    <td><input name="cc_runflat_suffix" class="autosave" type="text" style="width:100%; box-sizing: border-box" value="<?=Data::get('cc_runflat_suffix')?>" />
                                                        <small>Используется только для дополнения недостающего ИН. Для выделения из полного названия всех возможных суффиксов, добавляем их <a href="/cms/frm/dict.php" target="_blank">в словарь</a> (включая все написания ранфлет). Этот список используется не только в этом модуле импорта, но и на сайте: для поиска шин ранфлет.</small>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Минимальная допустимая цена поставщиков</th>
                                                    <td>
                                                        <input type="text" class="autosave" style="width: 100px" name="suplrsMinPrice" value="500">, руб
                                                    </td>
                                                </tr>
                                            </table>
                                            <table>
                                                <tr>
                                                    <td style="text-align: center; width: 200px; display: none">
                                                        <button id="delSuplrs">Удалить поставщика ⇒</button>
                                                        <br><small>из базы сайта и/или из загружаемого файла в процессе полного или частичного импорта </small>
                                                    </td>
                                                    <td style="text-align: center; width: 180px; display: none">
                                                        <button id="testImport">ᗧ Тестовый импорт</button>
                                                        <br><small>Без изменения товарного каталога</small>
                                                    </td>
                                                    <td style="text-align: center; width: 190px; display:none;">
                                                        <button id="goImportPartial" style="color:green">ᕘ Частичный импорт</button>
                                                        <br><small>Обновятся только товары, поставщики которых есть в загружаемом файле</small>
                                                    </td>
                                                    <td style="text-align: center; width: 185px; display:none; ">
                                                        <button id="goImportFull" style="color:red">ᔫ Полный импорт</button>
                                                        <br><small>Обнулится склад и цены у товаров, поставщиков которых нет в загружаемом файле</small>
                                                    </td>
                                                </tr>
                                            </table>

                                            <div id="delSuplrsWin" title="Удалить поставщика">

                                            </div>


                                        </form>
                                    </fieldset>
                
                                    <div id="view-grid"></div>
                                </div>
                            </div>
                    
                            <div id="tab-info">

                            </div>

                            <div id="tab-3">
                                <fieldset class="ui"><legend class="ui">Удалить устаревшие типоразмеры</legend>
                                    <form>
                                        <select id="sb_gr" style="display: inline-block">
                                            <option value="1">Шины</option>
                                            <option value="2">Диски</option>
                                        </select>
                                        <div style="margin-left: 15px; display: inline-block">
                                            <label for="sb_date1">Ни разу не обновленные с </label>
                                            <input type="text" id="sb_date1">
                                        </div>
                                        <div style="margin-left: 15px; display: inline-block">
                                            <button id="sb_view">Показать записи</button>
                                            <button id="sb_del">Удалить записи</button>
                                        </div>
                                    </form>
                                </fieldset>
                                <div id="sb_result"></div>
                            </div>

                            <div id="tab-history">
                            </div>

                            <div id="dm-cfg">
                                <fieldset class="ui"><legend class="ui">Состояние фоновой программы</legend>
                                    <table width="100%">
                                        <tr>
                                            <td style="width: 50%">
                                                <table>
                                                    <tr>
                                                        <th>Состояние процесса</th>
                                                        <td class="dm_state"></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Время запуска / последняя активность</th>
                                                        <td class="dm_startTime"></td>
                                                    </tr>
                                                    <tr>
                                                        <th>PID фонового процесса</th>
                                                        <td class="dm_pid"></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Занимаемая память</th>
                                                        <td class="dm_memUsage"></td>
                                                    </tr>
                                                </table>
                                                <div class="dm_ctrl">
                                                    <button class="dm_restart">Перезапустить</button>
                                                    <button class="dm_pause">Приостановить</button>
                                                </div>
                                            </td>
                                            <td>
                                                <textarea class="dm_task_log" disabled style="width: 100%; resize: vertical; height: 100%; box-sizing: border-box; padding: 6px 10px; min-height: 180px"></textarea>
                                            </td>
                                        </tr>
                                    </table>
                                </fieldset>

                                <fieldset class="ui"><legend class="ui">Настройки</legend>
                                    <form method="post" id="dmCfgFrm">
                                        <table>
                                            <tr>
                                                <th>Максимальное кол-во хранимых файлов в списке</th>
                                                <td><input name="maxFileList" class="autosave" type="text" style="width:50px; text-align:center" value="" /></td>
                                            </tr>
                                            <tr>
                                                <th>Количество строк, обрабатываемых парсером за одну итерацию</th>
                                                <td><input name="DM_rowsLimitByIter" class="autosave" type="text" style="width:50px; text-align:center" value="" /></td>
                                            </tr>

                                        </table>
                                        <button id="dm_refreshForm">Записать</button>
                                    </form>
                                </fieldset>
                            </div>
                    
                        </div>
                
                    </div>
            
                </td></tr>
        </table>
    </div>

<? cp_end();

