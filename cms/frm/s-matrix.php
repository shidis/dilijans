<?
require_once '../auth.php';
include('../struct.php');

$gr=@$_REQUEST['gr'];
if(empty($gr)) $gr=2;

$cp->frm['name']='s-matrix';
$cp->frm['title']='Матрица цветов для дисков';

$cp->checkPermissions();

cp_head();
cp_css();
cp_js();

?>
<? cp_body()?>
<? cp_title()?>

<style type="text/css">
    #mx table td{
        vertical-align:top;
    }
    #mx table .left{
        min-width:200px;
        width:20%;
    }
    #mx table .left .ui-widget-content{
        padding:10px;
        margin-right:10px;
    }
    #mx table .left input,#mx table .left textarea{
        width:95%;
    }
    #mx table .left .row{
        margin:6px 0 6px 0;
    }
    #mx #mxGrid sub{
        display:none;
    }
    #mx .ui-jqgrid-btable  tr.ui-row-ltr td {
        font-size:12px;
    }
    #mx .left .buttons button{
        margin: 10px 10px 0 0;
    }
    #mx table .left label{
        margin-bottom: 4px;
        display: block;
    }
    #mx .ui-jqgrid-btable td{
        vertical-align: middle;
    }

</style>

<script type="text/javascript">
    var CIM=<?=$CIM=Cfg::get('CAT_IMPORT_MODE')?>;
</script>

<div id="mx">
    <div id="mxCatDlg" title="Просмотр моделей и типоразмеров для выбранного суффикса"></div>
    <table style="width:100%">
        <tr>
            <td class="left"><div class="ui-widget-content ui-corner-all"><form id="frm">
                        <div class="row">
                            <label><strong>Базовый суффикс</strong> (добавление нового или поиск по матрице)</label>
                            <input type="text" id="cSuffix" name="cSuffix" />
                        </div>
                        <? if($CIM==2){?>
                            <div class="row">
                                <label>Тег</label>
                                <input type="text" name="tag" />
                            </div>
                            <div class="row">
                                <label>Суффикс для сайта</label>
                                <input type="text" name="suffix1" />
                            </div>
                            <div class="row">
                                <label>Суффиксы-синонимы для импорта</label>
                                <textarea name="iSuffixes" rows="3"></textarea>
                            </div>
                            <div class="row">
                                <label>Длинный суффикс</label>
                                <textarea class="ui-corner-all" name="suffix2" rows="3"></textarea>
                            </div>
                        <? }elseif($CIM==3){?>
                            <div class="row">
                                <label>Суффиксы-синонимы для импорта</label>
                                <textarea name="iSuffixes" rows="3"></textarea>
                            </div>
                            <div class="row">
                                <label>Суффикс для сайта</label>
                                <input type="text" name="suffix1" />
                            </div>
                        <? }?>
                        <div class="row">
                            <label>Бренд</label>
                            <select name="brand_id"><option value="0">не использовать привязку</option>
                                <? $cc=new CC_Base;
                                $cc->que('brands',$gr);
                                while($cc->next()!==false){
                                if($cc->qrow['replica'] && empty($rep)){
                                ?><optgroup label="REPLICA"><?
                                    $rep=1;
                                    }
                                    ?><option value="<?=$cc->qrow['brand_id']?>"><?=Tools::unesc($cc->qrow['name'])?></option><?
                                    }
                                    if(!empty($rep)){
                                    ?></optgroup><?
                            }
                            ?></select>
                        </div>
                        <div class="buttons">
                            <button id="post">Записать</button>
                            <button id="clear">очистить форму</button>
                            <button id="gridRefresh">обновить таблицу</button>
                        </div>
                    </form></div>

                <fieldset class="ui-widget-content ui-corner-all ui" style="margin-top:20px"><legend><em>шпаргалка</em></legend>

                    <p>Матрица служит, чтобы поставить в соответствие парамеры цвета, которые у одного и того же типоразмера могут обозначаться по-разному в зависимости от поставщика. Матрица помогает избежать дубля одного и того же товара с разными написаниями параметра цвета. Суффикс в данном контексте - это цвет диска.</p>

                    <p><strong>Базовый суффикс</strong> - он же суффикс типоразмера, используется для формирования урла карточки товара. Базовое значение для связи матрицы с товарной базой. Значком <b>(!)</b> отмеччаются те базовые суффиксы, которые встречаются где-либо в перечислениях суффиксов-синонимах.</p>
                    <p><strong>Тег</strong> - значение цвета - тег для группировки в фильтрах на сайте.</p>
                    <p><strong>Суффикс для сайта</strong> - отображаемое на сайте значение.</p>
                    <p><strong>Суффиксы-синонимы</strong> используются в процессе импорта из внешних источников прайс-листов для поиска аналогичных написаний суффиксов. Перечисление - через запятую.</p>
                    <? if($CIM==2){?>
                        <p><strong>Длинный суффикс</strong> - дополнительное поле, можно сделать красивое и содержательное описание цвета.</p>
                    <? }?>
                    <p><strong>Бренд</strong> - используется для указания специфических только для выбранного бренда значений. Если для базового суффикса указано значение без указания бренда и значение с указанием бренда, второе будет иметь более высокий приоритет.</p>


                    <p>В таблице справа (матрице) содержаться все значения (без повторений) суффиксов, встречаемые в базе типоразмеров сайта + введенные в матрицу вручную с помощью формы слева.</p>
                    <p>Поле ввода базового суффикса используется для двух целей: ввода нового значения базового суффикса для добавления его в матрицу и для поиска по значениям в таблице матрицы справа. Поиск осуществляется сразу после любого изменения поля ввода. На медленных интернетах, в отклике могут быть задержки</p>
                    <p>Форма ввода позволяет только добавлять новые соответсвия суффиксов, редактирование же осуществляется в правой таблице. Клик на строке в таблице переводит запись в режим редактирования. После внесения изменений нажимаем Ентер для сохранения изменений. Кнопка удаления записи предназначена для удаления вручную введенных базовых суффиксов. При удалении суффиксов для существующих в базе типоразмеров, удаляется только значение в матрице, типоразмеры никаким образом не затрагиваются.</p>
                    <p>&nbsp;</p>


                </fieldset>
            </td>
            <td>
                <div id="gridWrapper"></div>
            </td>
        </tr>
    </table>
</div>

<? cp_end()?>
