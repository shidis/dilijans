$(document).ready(function ()
{

    $.ajaxSetup({
        type: 'POST',
        cache: false,
        dataType: 'json',
        url: '../../be/dl/import_v4-2.php',
        error: Err,
        complete: ajaxComplete
    });

    var loader = $('.workspace').cloader();
    $(document).ajaxStart(function ()
    {
        loader.cloader('show');
    })
        .ajaxStop(function ()
        {
            loader.cloader('hide');
        });

    $('.pb').progressbar();

    $('#mtabs').tabs();

    $('#progress').dialog({
        autoOpen: false,
        modal: true,
        resizable: false,
        height: 140,
        closeOnEscape: false,
        beforeClose: function (e, ui)
        {
            if (isDefined(e.currentTarget) && e.currentTarget.nodeName == 'BUTTON') return false;
        }
    });
    $('#loading').dialog({
        autoOpen: false,
        modal: true,
        resizable: false,
        height: 90,
        closeOnEscape: false,
        beforeClose: function (e, ui)
        {
            if (isDefined(e.currentTarget) && e.currentTarget.nodeName == 'BUTTON') return false;
        }
    });

    $('#errWin').dialog({
        autoOpen: false,
        modal: false,
        resizable: true,
        closeOnEscape: true,
        buttons: {
            'Закрыть': function ()
            {
                $(this).dialog("close");
            }
        }
    });

    $('#brandsWin').dialog({
        autoOpen: false,
        modal: true,
        height: 350,
        width: 390,
        buttons: {
            'Закрыть': function ()
            {
                $(this).dialog("close");
            }
        },
        open: function ()
        {
            $('#brandsWin').empty().html('<table id="brandsGrid"></table><div id="brandsPager"></div>');
            $('#brandsGrid').jqGrid({
                datatype: 'json',
                hidegrid: false,
                url: '../../be/dl/import_v4-2.php?act=brands&gr=' + self.file.gr,
                colNames: ['Бренд', 'Не обновлять цены'],
                colModel: [
                    {name: 'bname', index: 'bname', align: 'left', width: 130, sortable: false},
                    {name: 'noupd', index: 'noupd', align: 'center', width: 130, sortable: false}
                ],
//				pager: '#brandsPager',
                rowNum: 999999,
                shrinkToFit: true,
                width: '310',
                height: '100%',
                loadError: Err,
                loadComplete: function ()
                {
                    var ids = jQuery("#brandsGrid").getDataIDs();
                    var d;
                    for (var i in ids) {
                        d = jQuery("#brandsGrid").getRowData(ids[i]);
                        d.noupd = '<input type="checkbox" value="1" brand_id="' + ids[i] + '" ' + (d.noupd == 1 ? 'checked="checked"' : '') + '>';
                        jQuery("#brandsGrid").setRowData(ids[i], d);
                    }
                }
            });
        }
    });


    $(document).on('change', '#brandsWin [type=checkbox]', function (e)
    {
        $.ajax({
            data: {
                act: 'brandMod',
                brand_id: $(e.target).attr('brand_id'),
                noupd: $(e.target).prop('checked'),
                gr: self.file.gr
            },
            success: function (r)
            {
                if (!r.fres) note(r.fres_msg, 'error');
                else {
                    if (r.rv) $(e.target).prop('checked', true); else $(e.target).prop('checked', false);
                    $('#showBrandsWin').button("option", "label", 'Необновляемые бренды (' + r.brandsNoUpdNum + ')');
                }
            }
        });

    });

    $('#showBrandsWin').button().click(function ()
    {
        $('#brandsWin').dialog('open');
    });

    $('#testImport').button().click(function ()
    {
        goImport(1)
    });

    $('#goImport').button().click(function ()
    {
        if (!isDefined($('#configFrm [name=pricing]').val())) {
            alert('Метод расчета цен не задан');
            return;
        }
        if (confirm('Загружаем на сайт, используя метод расчета цен "' + ($('#configFrm [name=pricing] option[value=' + $('#configFrm [name=pricing]').val() + ']').html()) + '"?')) goImport(0);
        return;
    });

    $('.ci form').submit(function (e)
    {
        e.preventDefault();
    });

    $(".ci #upload").uploadify({
        swf: '/cms/inc/uploadify/uploadify.swf',
        uploader: '../../be/dl/import_v4-2.php?mode=upload&' + window.QSID,
        buttonText: 'Выбрать файл',
        queueID: 'fileQueue',
        auto: true,
        multi: false,
        width: 251,
        height: 30,
        method: 'POST',
        progressData: 'percentage',
        buttonImg: '../inc/uploadify/browse.gif',
        fileExt: '*.xls;*.csv;',
        fileDesc: "Файлы EXCEL (*.xls;*.csv)",

        onUploadError: function (file, errorCode, errorMsg, errorString)
        {
            alert('ОШИБКА ПРИ ЗАГРУЗКЕ :: Error code = ' + errorCode + ' INFO: ' + errorMsg + ' ' + errorString);
        },

        onUploadSuccess: function (file, data, response)
        {
            var res = data.split('|');
            logit(res);
            if (res[0] == '1') {
                $('#loading').html('<p>Идет распознавание колонок файла</p>');
                $('#loading').dialog('open');
                $.ajax({
                    data: {
                        act: 'upload',
                        fname: res[1],
                        config: $('#configFrm').serialize()
                    },
                    success: function (r)
                    {
                        if (r.fres) {
                            self.file.status = 0;
                            self.file.gr = r.gr
                            self.file.file_id = r.file_id;
                            self.file.param = r.param;
                            self.file.name = r.name;
                            note('Файл ' + r.name + ' загружен');
                            $('#selectedFile').html(self.file.name + ' - ' + 'не импортирован');
                            $('#fileList').prepend('<li class="ui-corner-all ui-widget-content" file_id="' + r.file_id + '">' + r.name + '</li>');
                            $('#selectedFile').html(self.file.name);
                            $('#fileList li').removeClass('ui-selected');
                            $('#fileList li[file_id=' + r.file_id + ']').addClass('ui-selected');

                            for (var k in r.deletedFiles)
                                $('#fileList li[file_id=' + r.deletedFiles[k] + ']').remove();

                            $('#showBrandsWin').show(100).button("option", "label", 'Необновляемые бренды (' + r.brandsNoUpdNum + ')');

                            if (r.gr == 2) {
                                $('#showDIAWin').show(100);
                                $('#showSVWin').show(100);
                            } else {
                                $('#showDIAWin').hide(100);
                                $('#showSVWin').hide(100);
                            }

                            $('#importSetup').show();
                            $('#showBrandsWin').show(100);
                            $('#testImport').show(100);
                            $('#goImport').show(100);
                            viewGrid();
                        } else err(r.fres_msg, 'error');
                    },
                    complete: function ()
                    {
                        $('#loading').dialog('close');
                    }
                });
            } else {
                err('Файл ' + file.name + ' НЕ ЗАГРУЖЕН!<br>ОТВЕТ: ' + response + '<br>DATA: ' + $.param(data), 'error');
            }
        }
    });

    $.ajax({
        data: {act: 'files'},
        success: function (r)
        {
            if (r.fres) {
                $('#fileList').selectable({
                    tolerance: 'touch',
                    selected: function (e, ui)
                    {
                        $('#fileList li').removeClass('ui-selected');
                        $(ui.selected).addClass('ui-selected');
                        $.ajax({
                            data: {act: 'select_file', file_id: $(ui.selected).attr('file_id')},
                            success: function (r)
                            {
                                if (r.fres) {
                                    self.file.file_id = r.file_id;
                                    self.file.param = r.param;
                                    self.file.gr = r.gr;
                                    self.file.CM = r.CM;
                                    self.file.status = r.status;
                                    self.file.name = r.name;
                                    $('#showBrandsWin').show(100).button("option", "label", 'Необновляемые бренды (' + r.brandsNoUpdNum + ')');
                                    if (r.gr == 2) {
                                        $('#showDIAWin').show(100);
                                        $('#showSVWin').show(100);
                                    } else {
                                        $('#showSVWin').hide(100);
                                        $('#showDIAWin').hide(100);
                                    }
                                    $('#selectedFile').html((self.file.gr == 1 ? 'Шины: ' : 'Диски:') + self.file.name + ' - ' + (self.file.status == 1 ? 'ИМПОРТИРОВАН' : 'не импортирован'));
                                    if (self.file.status == 1) {
                                        $('#importSetup').hide();
                                    } else {
                                        $('#importSetup').show();
                                        $('#showBrandsWin').show();
                                        $('#testImport').show();
                                        $('#goImport').show();
                                    }
                                    viewGrid();
                                } else err(r.fres_msg, 'error');
                            }
                        });

                    }
                });
                for (var i = 0; i < r.files.length; i++)
                    $('#fileList').append('<li class="ui-corner-all ui-widget-content" file_id="' + r.files[i]['id'] + '">' + (r.files[i]['gr'] == 1 ? 'Ш: ' : 'Д:') + r.files[i]['label'] + (r.files[i]['status'] == 1 ? ' [OK]' : '') + '</li>');
            }
        }
    });


    $.ajax({
        data: {act: 'get_config'},
        success: function (r)
        {
            if (r.fres) {
                populate('#configFrm', r.opt.config);
                self.CMI = r.CMI;
                $('#showDIAWin').button('option', 'label', 'Таблица преобразований DIA (' + r.diaMergeNum + ')');
                $('#showSVWin').button('option', 'label', 'Таблица преобразований LZxPCD (' + r.svMergeNum + ')');
            }
        }
    });


    $(document).on('change', '#configFrm textarea, #configFrm input, #configFrm select', function ()
    {
        $.ajax({
            data: {act: 'set_config', configFrm: $('#configFrm').serialize()},
            success: function (r)
            {
                if (!r.fres) err(r.fres_msg, 'error');
            }
        });
    });

    diaMergeEvents();
    svMergeEvents();

});

var self = this;
this.file = {'file_id': 0};

function goImport(test)
{

    $('#progress .pb').progressbar('option', 'value', 0);
    $('#progress').dialog({
        title: test ? 'Делаем тестовый импорт...' : 'Делаем импорт файла...'
    }).dialog('open');
    $('#progress .t').empty();
    var limit = 500; // кол-во строк за раз
    var cou = Math.ceil((self.file.param.numRows) / limit);
    if (cou <= 0) cou = 1;
    var it = 0;
    var data = {};
    data.act = 'parse';
    data.file_id = self.file.file_id;
    data.gr = self.file.gr;
    data.limit = limit;
    data.test = test;
    data.ciSID = ciSID;

    function iter(it)
    {
        data.iter = it;
        if (it == 0) {
            data.config = $('.ci #configFrm').serialize();
        }
        $.ajax({
            data: data,
            error: function (XMLHttpRequest, textStatus, errorThrown)
            {
                $('#progress').dialog('close');
                err('ajx ERROR: ' + textStatus + '<br>' + XMLHttpRequest.responseText + ' :: итeрация: ' + it + ' из ' + cou);
            },
            success: function (r)
            {
                if (r.fres) {
                    it++;
                    $('#progress .pb').progressbar('option', 'value', Math.ceil(it * 100 / cou));
                    if (isDefined(r.brandName)) $('#progress .t').html('Обработка ' + r.brandName + ' ...');
                    logit(r.logs);
                    if (r.finish != true)
                        iter(it);
                    else {
                        $('#progress').dialog('close');
                        note('Импорт завершен успешно.', 'long');
                        self.file.status = r.status;
                        self.file.param = r.param;
                        $('#selectedFile').html((self.file.gr == 1 ? 'Шины: ' : 'Диски:') + self.file.name + ' - ' + (self.file.status == 1 ? 'ИМПОРТИРОВАН' : 'не импортирован'));
                        if (r.status == 1) {
                            $('#importSetup').hide();
                            $('#fileList [file_id=' + self.file.file_id + ']').append(' [OK]');
                        }
                        viewGrid();
                    }
                } else {
                    $('#progress').dialog('close');
                    err(fresMsg(r.fres_msg), 'error');
                }
            }
        });
    }

    iter(it);

}

function viewGrid()
{

    if (self.file.gr == 2)
        $.ajax({
            data: {act: 'getSup', file_id: self.file.file_id},
            success: function (r)
            {
                if (r.fres) {
                    self.CMI[self.file.gr]['Поставщик']['list'] = r.supList;
                    viewGrid_();
                } else err(fresMsg(r.fres_msg), 'error');
            }
        });
    else viewGrid_();
}

function viewGrid_()
{

    $('.ci #view-grid').empty();

    if (self.file.gr == 2) $('.ci #view-grid').append('<div class="ui-widget" style="margin:0 0 10px 0"><div style="border:1px dashed; padding:10px;" class="ui-corner-all" style="margin-top: 20px; padding: 0pt 0.7em;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right: 0.3em;"></span><b>Легенда для колонки "Трансформации":</b> DIRECT - полное совпадение с базовым суффиксом; ЗНАЧЕНИЕ ЦВЕТА - найден аналог в матрице и указан к какому базовому цвету он относится; ЕСЛИ ПУСТО - значит цвета нет в матрице цветов. приставки T:/M: - параметры для размера и модели соответсвенно. В режиме тестовой загрузки для позиций, для которых еще нет в базе сайта бренда, подстановка брендозависомого альтернативного суффикса не сработает - отобразиться альтернатива без привязки к бренду (если есть). Также, в колонке отображается преобразования Dia (Dia новое значение) и преобразование ступиц (SV новое значение)</p></div></div>');

    $('.ci #view-grid').append('<table id="viewGrid"></table><div id="lfPager"></div>');
    $('.ci #viewGrid').jqGrid({
        datatype: 'json',
        url: '../../be/dl/import_v4-2.php?act=view',
        colNames: function ()
        {
            //var a=['X','ID'];
            var a = ['ID'];
            a.push('Статус размера', 'Статус модели', 'Статус бренда');
            var i = 0;
            for (var v in self.CMI[self.file.gr]) {
                a.push(v);
                i++;
            }
            return a;
        }(),
        colModel: function ()
        {
            var a = [
//				{name:'sel',index:'sel',align:'center',width:50,sortable:false,stype:''},
                {name: 'item_id', index: 'item_id', sortable: true, align: 'center', width: 20}
            ];
            a.push({name: 'cstatus', index: 'cstatus', sortable: true, align: 'center', width: 30, stype: 'select', editoptions: {value: ":все;1:обновлен;2:добавлен;3:игнор"}});
            a.push({name: 'mstatus', index: 'mstatus', sortable: true, align: 'center', width: 30, stype: 'select', editoptions: {value: ":все;1:обновлен;2:добавлен"}});
            a.push({name: 'bstatus', index: 'bstatus', sortable: true, align: 'center', width: 30, stype: 'select', editoptions: {value: ":все;1:обновлен;2:добавлен"}});
            var i = 0;
            var b;
            for (var v in self.CMI[self.file.gr]) {
                b = {name: self.CMI[self.file.gr][v]['item_field'], index: self.CMI[self.file.gr][v]['item_field'], sortable: true, align: 'center', width: self.CMI[self.file.gr][v]['fieldWidth']};
                if (self.CMI[self.file.gr][v]['type'] == 'id') {
                    b.editoptions = {value: ':Все'};
                    b.stype = "select";
                    for (var l in self.CMI[self.file.gr][v]['list']) {
                        b.editoptions.value += ';' + self.CMI[self.file.gr][v]['list'][l] + ':' + l;
                    }
                }
                a.push(b);
                i++;
            }
            return a;
        }(),
        onSelectRow: function (id)
        {
        },
        gridComplete: function ()
        {
            /*			var ids = $('#viewGrid').jqGrid('getDataIDs');
             var c=getCookie('vgChk');
             if(c!=null && c!='') c=unserialize(c); else c={};
             var d,s;
             for(var i=0;i < ids.length;i++){
             d=$('#viewGrid').jqGrid('getRowData',ids[i]);
             s='<input type="checkbox" value="1" class="vg_chk" rowId="'+ids[i]+'" '+(isDefined(c[d['item_id']])?'checked="checked"':'')+'>';
             $('#viewGrid').jqGrid('setRowData',ids[i],{sel:s});
             }
             $('.ci .vg_chk').change(viewGridChk);
             */
        },
        caption: 'Просмотр файла',
        autowidth: true,
//		shrinkToFit: true,
        sortname: 'item_id',
        sortable: true,
        sortorder: 'asc',
        width: '100%',
        height: 'auto',
//		scroll: true,
        rowNum: 40,
        postData: {'file_id': self.file.file_id},
        viewrecords: true,
        rowList: [10, 20, 30, 40, 50, 60, 70, 80, 100, 120, 150, 200, 300, 400, 500, 600, 700, 800, 1000, 2000],
        pager: '#lfPager',
//		rownumbers: true, 
//		rownumWidth: 40, 
        loadError: Err,
        editurl: '#',
        cellEdit: false
    })
        .jqGrid('navGrid', '#lfPager', {
            add: false,
            edit: false,
            del: false,
            search: false,
            refresh: true,
            view: true
        })
        .jqGrid('navButtonAdd', '#lfPager', {
            caption: "Колонки",
            title: "Состав колонок",
            onClickButton: function ()
            {
                $(".ci #viewGrid").jqGrid('columnChooser');
            }
        })
        .jqGrid('navButtonAdd', "#lfPager", {
            caption: "Сброс",
            title: "Очистить форму поиска",
            buttonicon: 'ui-icon-refresh',
            onClickButton: function ()
            {
                viewGrid[0].clearToolbar()
            }
        })
        /*	.jqGrid('navButtonAdd',"#lfPager",{
         caption:"Действие",
         title:"Действия с записью",
         buttonicon :'ui-icon-link',
         onClickButton:function(){
         doViewGrid();
         }
         })
         */
        .jqGrid('filterToolbar')
//	.jqGrid('sortableRows')
        .trigger('reloadGrid');
}

function doViewGrid()
{
    note('Нечего делать, сорри :(');
}

function viewGridChk(e)
{
    var c = getCookie('vgChk');
    if (c != null && c != '') c = unserialize(c); else c = {};
    var d = $('#viewGrid').jqGrid('getRowData', $(e.target).attr('rowId'));
    if ($(e.target).is(':checked')) c[d['item_id']] = 1;
    else if (isDefined(c[d['item_id']])) delete c[d['item_id']];
    setCookie('vgChk', serialize(c));
//		$$.note($.param(c));
}


function populate(form, data)
{
    for (var tag in data) {
        if ($(form + ' [name=' + tag + ']').length)
            switch ($(form + ' [name=' + tag + ']').get(0).tagName) {
                case 'INPUT':
                    if ($(form + ' [name=' + tag + ']').attr('type') == 'text') $(form + ' [name=' + tag + ']').val(data[tag]);
                    else if ($(form + ' [name=' + tag + ']').attr('type') == 'checkbox') $(form + ' [name=' + tag + ']').prop('checked', data[tag] ? true : false);
                    break;
                case 'TEXTAREA':
                    $(form + ' [name=' + tag + ']').val(data[tag]);
                    break;
                default:
                    $(form + ' select[name=' + tag + ']').val(data[tag]);
                    break;

            }
    }
}

function err(msg)
{
    $('#errorDlg').html(msg).dialog('open');
}

function diaMergeEvents()
{
    var diaWin_lastsel;

    $('#diaWin').dialog({
        autoOpen: false,
        modal: !true,  // TODO низя сделать модальым - не будут ошибки грида корректно всплывать!
        height: 380,
        width: 350,
        buttons: {
            'Закрыть': function ()
            {
                $(this).dialog("close");
            }
        },
        open: function ()
        {
            $('#diaWin').empty().html('<table id="diaGrid"></table><div id="diaPager"></div>');
            $('#diaGrid').jqGrid({
                datatype: 'json',
                hidegrid: false,
                url: '../../be/dl/import_v4-2.php?act=dias',
                editurl: '../../be/dl/import_v4-2.php?act=diasMod',
                colNames: ['Исходный DIA', 'Преобразовать в'],
                colModel: [
                    {name: 'dia0', index: 'dia0', align: 'center', width: 100, sortable: false, editable: true},
                    {name: 'dia1', index: 'dia1', align: 'center', width: 100, sortable: false, editable: true}
                ],
                rowNum: 999999,
                shrinkToFit: true,
                width: '310',
                height: '100%',
                pgbuttons: false,
                scroll: 1,
                loadError: Err,
                pager: '#diaPager',
                onSelectRow: function (id)
                {
                    if (diaWin_lastsel) jQuery('#diaGrid').jqGrid('restoreRow', diaWin_lastsel);
                    jQuery('#diaGrid').jqGrid('editRow', id, true, null, function (xhr)
                    {
                        if (xhr.readyState == 4) {
                            if (xhr.responseText == 0) $('#diaGrid').trigger('reloadGrid'); else return true;
                        }
                    });
                    diaWin_lastsel = id;
                }

            }).jqGrid('navGrid', "#diaPager", {
                edit: true,
                add: true,
                del: true,
                search: false
            });
        },
        close: function ()
        {
            $.ajax({
                data: {act: 'getDiasNum'},
                success: function (r)
                {
                    $('#showDIAWin').button('option', 'label', 'Таблица преобразований DIA (' + r.diaMergeNum + ')');
                }
            });
        }
    });

    $('#showDIAWin').button().click(function ()
    {
        $('#diaWin').dialog('open');
    });

}

function svMergeEvents()
{
    var svWin_lastsel;

    $('#svWin').dialog({
        autoOpen: false,
        modal: !true,  // TODO низя сделать модальым - не будут ошибки грида корректно всплывать!
        height: 380,
        width: 350,
        buttons: {
            'Закрыть': function ()
            {
                $(this).dialog("close");
            }
        },
        open: function ()
        {
            $('#svWin').empty().html('<p><small>*) Сверловки вводятся в формате LZ*PCD, например 5*114.3</small></p><table id="svGrid"></table><div id="svPager"></div>');
            $('#svGrid').jqGrid({
                datatype: 'json',
                hidegrid: false,
                url: '../../be/dl/import_v4-2.php?act=SVs',
                editurl: '../../be/dl/import_v4-2.php?act=svMod',
                colNames: ['Исходная сверловка', 'Преобразовать в'],
                colModel: [
                    {name: 'sv0', index: 'sv0', align: 'center', width: 100, sortable: false, editable: true},
                    {name: 'sv1', index: 'sv1', align: 'center', width: 100, sortable: false, editable: true}
                ],
                rowNum: 999999,
                shrinkToFit: true,
                width: '310',
                height: '100%',
                pgbuttons: false,
                scroll: 1,
                loadError: Err,
                pager: '#svPager',
                onSelectRow: function (id)
                {
                    if (svWin_lastsel) jQuery('#svGrid').jqGrid('restoreRow', svWin_lastsel);
                    jQuery('#svGrid').jqGrid('editRow', id, true, null, function (xhr)
                    {
                        if (xhr.readyState == 4) {
                            if (xhr.responseText == 0) $('#svGrid').trigger('reloadGrid'); else return true;
                        }
                    });
                    svWin_lastsel = id;
                }

            }).jqGrid('navGrid', "#svPager", {
                edit: true,
                add: true,
                del: true,
                search: false
            });
        },
        close: function ()
        {
            $.ajax({
                data: {act: 'getSVNum'},
                success: function (r)
                {
                    $('#showSVWin').button('option', 'label', 'Таблица преобразований LZxPCD (' + r.svMergeNum + ')');
                }
            });
        }
    });

    $('#showSVWin').button().click(function ()
    {
        $('#svWin').dialog('open');
    });

}
