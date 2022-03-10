var dmCfgTabEvt=false;
var dmPingSheduler;
var SBcollapsing=false;
var file={file_id: 0};
var axUrl='../../be/dl/import_v4_dm.php';

$(document).ready(function ()
{

    $.ajaxSetup({
        type: 'POST',
        cache: false,
        dataType: 'json',
        url: axUrl,
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

    $('.workspace').append('<div id="collapseSB" style="position:fixed; left:0; top:50px;  width:10px; padding-top:100px; height:230px; background:#ccc; cursor:pointer; color:#999; line-height:20px; border-radius:0 10px 10px 0">&lt; &lt; &lt; &lt; &lt; &lt; &lt;</div>');

    $('#collapseSB').click(function ()
    {
        if (SBcollapsing) return;
        if (!$(this).hasClass('lt')) {
            SBcollapsing = true;
            $('#left-area').animate({'margin-left': -295}, 300, function ()
            {
                $('#collapseSB').html('&gt; &gt; &gt; &gt; &gt; &gt; &gt;');
                $('#collapseSB').addClass('lt');
                SBcollapsing = false;
                $('#viewGrid').setGridWidth($('#view-grid').outerWidth());
            });
        } else {
            SBcollapsing = true;
            $('#left-area').animate({'margin-left': 0}, 300, function ()
            {
                $('#collapseSB').html('&lt; &lt; &lt; &lt; &lt; &lt; &lt;');
                $('#collapseSB').removeClass('lt');
                $('#viewGrid').setGridWidth($('#view-grid').outerWidth() - 290);
                SBcollapsing = false;
            });
        }
    });


    $('#mtabs').tabs({
        activate: function (event, ui)
        {
            if(ui.newTab.context.hash=='#tab-history'){
                $('#tab-history').html('<img src="/assets/images/ax/siteheart.gif">');
                $.ajax({
                    url: axUrl+'?act=filesHistory',
                    success: function (r)
                    {
                        $('#tab-history').html('');
                        if(r.table.length){
                            var $t=$('<table><tr><th>Поставщик</th><th>Дата обновления</th></tr></table>').appendTo('#tab-history');
                            _.each(r.table, function(v)
                            {
                                $t.append('<tr><td>'+ v.name +'</td><td>'+ v.d +'</td>');
                            });
                        }

                        $('#tab-history').append(print_r(r.files));
                        logit(r.files);
                    }
                });
            }else if(ui.newTab.context.hash=='#dm-cfg'){
                dmCfgTabEvt=true;

            }else if(ui.newTab.context.hash=='#tab-info'){

                $('#tab-history').html('<img src="/assets/images/ax/siteheart.gif">');
                $.ajax({
                    url: axUrl+'?act=infoTab',
                    dataType: 'html',
                    success: function (r)
                    {
                        $('#tab-info').html(r);
                    }
                });

            }else{
                dmCfgTabEvt=false;
            }
        }
    });


    $('#loading').dialog({
        autoOpen: false,
        modal: true,
        resizable: false,
        height: 110,
        width: 300,
        closeOnEscape: false,
        beforeClose: function (e, ui)
        {
            if (isDefined(e.currentTarget) && e.currentTarget.nodeName == 'BUTTON') return false;
        },
        close: function ()
        {
            $('#loading').dialog('option', {'width': 300});
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

    $('.ci form').submit(function (e)
    {
        e.preventDefault();
    });


    filesEvents();
    getConfig();
    uploadEvents();
    diaMergeEvents();
    svMergeEvents();
    YSTMergeEvents();
    cfgTabEvents();
    DM.pingEvents();
    executeTaskEvents();
    delSuplrs();


    // Обслуживание базы
    sb.init();
});

function delSuplrs()
{
    $('#delSuplrsWin').dialog({
        autoOpen: false,
        modal: true,
        appendTo: $('#configFrm'),
        height: 500,
        width: 380,
        buttons: {
            'OK': function ()
            {
                $(this).dialog("close");
            }
        },
        open: function()
        {
            var $sp=$('#delSuplrsWin');
            $sp.html('<img src="/assets/images/ax/siteheart.gif">');

            $.ajax({
                url: axUrl+'?act=getSuplrs&gr='+file.gr,
                success: function(r)
                {
                    var s='';
                    _.each(r.suplrs, function(v,k)
                    {
                        s+='<div><input type="checkbox" name="delSuplrs[]" value="'+k+'" id="dsp'+k+'"><label for="dsp'+k+'">'+ v.name+'</label></div>';
                    });
                    $sp.html('<div class="spWinList"><p class="ui-widget-content" style="padding: 5px 10px"><i>Отметки поставщиков будут сняты сразу после запуска любого типа импорта, включая тестовый</i></p>'+s+'</div>');

                }
            });
        }
    });

    $('#delSuplrs').button().click(function ()
    {
        $('#delSuplrsWin').dialog('open');
    });
}

/*
Яршинторг
 */
function YSTMergeEvents()
{
    var YSTWin_lastsel;

    $('#YSTWin').dialog({
        autoOpen: false,
        modal: !true,  // TODO низя сделать модальым - не будут ошибки грида корректно всплывать!
        height: 500,
        width: 380,
        buttons: {
            'Закрыть': function ()
            {
                $(this).dialog("close");
            }
        },
        open: function ()
        {
            var $win=$('#YSTWin').empty().html(
                '<p><small>*) Если наценка не указана, то Яршинторг для этого бренда будет обработан по общей схеме ценообразования.</small></p>'
                    +'<p><input type="text" placeholder="Загружаю..." class="YSTName ui-corner-all" value="" style="width: 320px; display: inline-block"><br><small id="YSTFounded"></small></p>'
                    +'<table id="YSTGrid"></table><div id="YSTPager"></div>'
            );

            $.ajax({
                url: axUrl+'?act=YSTGetSuplrName',
                success: function(r)
                {
                    $win.find('.YSTName').attr('placeholder','Название поставщика Яршинторг в файлах').val(r.YSTSuplrName).trigger('keyup');
                }
            });

            $win.find('.YSTName').keyup(function()
            {
                $.ajax({
                    url: axUrl+'?act=YSTChSuplrName',
                    data: {
                        name: $(this).val()
                    },
                    success: function(r)
                    {
                        if(r.fres){
                            if(r.suplrId)
                                $('#YSTFounded').html('<span style="color: green">* название найдено в списке поставщиков ID='+ r.suplrId+'</span>');
                            else
                                $('#YSTFounded').html('<span style="color: red">* название НЕ найдено в списке поставщиков</span>');
                        }
                    }
                })
            });

            $('#YSTGrid').jqGrid({
                datatype: 'json',
                hidegrid: false,
                regional: 'ru',
                url: axUrl+'?act=YSTList&gr=2',
                editurl: axUrl+'?act=YSTMod',
                colNames: ['Бренд', 'Наценка, %'],
                colModel: [
                    {name: 'bname', index: 'bname', align: 'center', width: 150, sortable: false, editable: false},
                    {name: 'extra', index: 'extra', align: 'center', width: 100, sortable: false, editable: true}
                ],
                rowNum: 999999,
                shrinkToFit: true,
                width: '330',
                height: '100%',
                pgbuttons: false,
                scroll: 1,
                loadError: Err,
                pager: '#YSTPager',
                onSelectRow: function (id)
                {
                    if (YSTWin_lastsel) jQuery('#YSTGrid').jqGrid('restoreRow', YSTWin_lastsel);
                    $('#YSTGrid').jqGrid('editRow', id, true, null, function (xhr)
                    {
                        if (xhr.readyState == 4) {
                            if (xhr.responseText == 0) $('#YSTGrid').trigger('reloadGrid'); else return true;
                        }
                    });
                    YSTWin_lastsel = id;
                }

            }).jqGrid('navGrid', "#svPager", {
                edit: true,
                add: true,
                del: true,
                search: false
            });

        }
    });

    $('#showYSTWin').button().click(function ()
    {
        $('#YSTWin').dialog('open');
    });


}

function filesEvents()
{
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
                        selectFile( $(ui.selected).attr('file_id'));

                    }
                });
                for (var i = 0; i < r.files.length; i++)
                    $('#fileList').append('<li class="ui-corner-all ui-widget-content" file_id="' + r.files[i]['id'] + '">' + (r.files[i]['gr'] == 1 ? 'Ш: ' : 'Д: ') + r.files[i]['label'] + (r.files[i]['status'] == 1 ? ' [OK]' : '') + '</li>');
            }
        }
    });

}

function selectFile(file_id)
{
    $.ajax({
        data: {act: 'select_file', file_id: file_id},
        success: function (r)
        {
            if (r.fres) {
                file.file_id = r.file_id;
                file.param = r.param;
                file.gr = r.gr;
                file.CM = r.CM;
                file.status = r.status;
                file.name = r.name;
                if (r.gr == 2) {
                    $('#showDIAWin').show(100);
                    $('#showSVWin').show(100);
                    $('#showYSTWin').show(100);
                } else {
                    $('#showSVWin').hide(100);
                    $('#showDIAWin').hide(100);
                    $('#showYSTWin').hide(100);
                }
                selectedFileInfo((file.gr == 1 ? 'Шины: ' : 'Диски:') + file.name + ' - ' + (file.status == 1 ? 'ИМПОРТИРОВАН' : 'не импортирован'), true);
                if (file.status == 1) {
                    $('#importSetup').hide();
                    infoPanel();
                }else if(DM.infoBlockShowed && DM.curTaskState!==false && DM.curTaskState!='error'){
                    $('#importSetup').hide();
                } else {
                    $('#importSetup').show();
                    $('#delSuplrs').parents('td').show();
                    $('#testImport').parents('td').show();
                    $('#goImportPartial').parents('td').show();
                    $('#goImportFull').parents('td').show();
                    $('#info').hide();
                }
                viewGrid();
            } else err(r.fres_msg, 'error');
        }
    });
}

function getConfig()
{
    $.ajax({
        data: {act: 'get_config'},
        success: function (r)
        {
            if (r.fres) {
                populate('#configFrm', r.opt);
                populate('#dmCfgFrm', r.opt);
                self.CMI = r.CMI;
                $('#showDIAWin').button('option', 'label', 'Таблица преобразований DIA (' + r.diaMergeNum + ')');
                $('#showSVWin').button('option', 'label', 'Таблица преобразований LZxPCD (' + r.svMergeNum + ')');

                // синхронизация
                $.ajax({
                    data: {act: 'sync'},
                    success: function (r)
                    {
                        if (r.fres && r.fres != 2) {
                            $('#sync').append('<p>Удаленный сервер ' + r.mysqlHost + '</p>');
                            $('#sync').slideDown(100);
                            //if (r.smatrixMustBeUpdate) $('#sync').append('<p>Матрица цветов на удаленном сервере изменилась <button id="syncUpdateSMatrix">обновить</button></p>');
                            if (r.diaMerge != '--') $('#sync').append('<p>Таблица преобразования диаметров ступиц = ' + r.diaMerge + '</p>');
                            if (r.svMerge != '--') $('#sync').append('<p>Таблица преобразования сверловок = ' + r.svMerge + '</p>');
                            if (r.emptyDiskSuffix != '--') $('#sync').append('<p>Базовый цвет диска по умолчанию = ' + r.emptyDiskSuffix + '</p>');
                            if (r.ignoreDiskSuffixes != '--') $('#sync').append('<p>Список игнорируемых цветов = ' + r.ignoreDiskSuffixes + '</p>');
                            //if (r.replicaBrand != '--') $('#sync').append('<p>Бренды реплики = ' + r.replicaBrand + '</p>');
                            $('#sync').append('<p>*) Возможность синхронизации матрицы цветов временно отключена</p>')
                            self.sync = {
                                turl: r.turl,
                                durl: r.durl,
                                siteName: r.siteName
                            }
                            $('#left-area .upload').append(
                                '<p><a href="#" id="syncTyresFromUrl">загрузить шины с ' + r.siteName + '</a></p>' +
                                    '<p><a href="#" id="syncDisksFromUrl">загрузить диски с ' + r.siteName + '</a></p>'
                            );
                            $('#syncTyresFromUrl').click(function (e)
                            {
                                e.preventDefault();
                                $('#loading').html('<p>Загрузка файла <b>' + self.sync.turl + '</b> ...</p>');
                                $('#loading').dialog('option', {'width': 600});
                                $('#loading').dialog('open');
                                $.ajax({
                                    data: {act: 'syncUpload', url: self.sync.turl},
                                    success: function (r)
                                    {
                                        $('#loading').dialog('close');
                                        if (r.fs > 0) {
                                            note('Загружено ' + r.fs + ' байт');
                                            parseFile(r.fname);
                                        } else err('Файл не загружен', 'error');
                                    }
                                });
                            });
                            $('#syncDisksFromUrl').click(function (e)
                            {
                                e.preventDefault();
                                $('#loading').html('<p>Загрузка файла <b>' + self.sync.durl + '</b> ...</p>');
                                $('#loading').dialog('option', {'width': 600});
                                $('#loading').dialog('open');
                                $.ajax({
                                    data: {act: 'syncUpload', url: self.sync.durl},
                                    success: function (r)
                                    {
                                        $('#loading').dialog('close');
                                        if (r.fs > 0) {
                                            note('Загружено ' + r.fs + ' байт');
                                            parseFile(r.fname);
                                        } else err('Файл не загружен', 'error');
                                    }
                                });
                            });

                            $('#sync').append('<p><a href="#" id="syncHide">&Oslash; убрать</a></p>');
                            $('#syncHide').click(function (e)
                            {
                                e.preventDefault();
                                $('#sync').slideUp(100);
                            });
                            $('#syncUpdateSMatrix').click(function ()
                            {
                                $.ajax({
                                    data: {act: 'syncUpdateSMatrix'},
                                    beforeSend: function ()
                                    {
                                        $('#syncUpdateSMatrix').attr('disabled', 'disabled');
                                    },
                                    success: function (r)
                                    {
                                        if (r.fres) {
                                            note('Матрица обновлена');
                                        } else err(r.fres_msg, 'error');
                                    },
                                    complete: function ()
                                    {
                                        $('#syncUpdateSMatrix').removeAttr('disabled');
                                    }
                                });
                            });
                        } else if (r.fres != 2) err(r.fres_msg, 'error');
                    }
                });
            }
        }
    });


    $(document).on('change', '#configFrm .autosave, #dmCfgFrm .autosave', function ()
    {
        logit($('#configFrm .autosave, #dmCfgFrm .autosave').serialize());
        $.ajax({
            data: {
                act: 'set_config',
                f: $('#configFrm .autosave, #dmCfgFrm .autosave').serialize()
            },
            success: function (r)
            {
                if (!r.fres) err(r.fres_msg, 'error');
            }
        });
    });

    $('.relate').each(function()
    {
        $(this).change(function()
        {
            $('[name='+$(this).attr('rel')+']').val($(this).prop('checked')*1).change();
        })
    })

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
                regional: 'ru',
                url: axUrl+'?act=dias',
                editurl: axUrl+'?act=diasMod',
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
                regional: 'ru',
                url: axUrl+'?act=SVs',
                editurl: axUrl+'?act=svMod',
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


function uploadEvents()
{
    if (window.File && window.FileReader && window.FileList && window.Blob) {

        self.uploader = $("#upload").damnUploader({
            url: axUrl+'?mode=upload',
            limit: 1,
            fieldName: 'Filedata',
            multiple: false
        });

        self.uploader.on(
            {
                'du.add': function (e)
                {

                    var ui = e.uploadItem;
                    var filename = ui.file.name || "";

                    ui.progressCallback = function (percent)
                    {
                        $('#fileQueue').progressbar('option', 'value', percent);
                    };
                    $('#fileQueue').show();

                    ui.completeCallback = function (success, data, errorCode)
                    {
                        if (success) {
                            var res = data;
                            res = res.split('|');
                            logit(res);
                            if (res[0] == '1') {
                                parseFile(res[1]);
                            } else {
                                logit(data);
                                err('Файл ' + file.name + ' НЕ ЗАГРУЖЕН!<br>ОТВЕТ: ' + errorCode + '<br>DATA: ' + data, 'error');
                            }
                        } else {
                            err('ОШИБКА ПРИ ЗАГРУЗКЕ :: Error code = ' + errorCode);
                        }
                        $("#uploadForm").get(0).reset();
                        $('#fileQueue').hide();
                        $('#fileQueue').progressbar('option', 'value', 0);

                    }
                    ui.upload();

                    return false; // отменить стандартную обработку выбора файла
                },
                'du.completed': function ()
                {
                }
            });

        $('#fileQueue').progressbar({
            'max': 100
        }).hide();

    } else {
        $(".ci #upload").uploadify({
            uploader: '../inc/uploadify/uploadify.swf',
            script: axUrl+'?mode=upload',
            cancelImg: '../inc/uploadify/cancel1.png',
            folder: '../../tmp',
            queueID: 'fileQueue',
            auto: true,
            multi: false,
            rollover: !true,
            width: 251,
            height: 30,
            scriptData: {},
            buttonImg: '../inc/uploadify/browse.gif',
            fileExt: '*.csv;*.xls',
            fileDesc: "Файлы EXCEL (*.csv,*.xls)",
            onOpen: function ()
            {
            },
            onError: function (event, queueId, fileObj, error)
            {
                err('ОШИБКА ПРИ ЗАГРУЗКЕ :: Error type = ' + error.type);
            },
            onComplete: function (e, queueId, file, response, data)
            {
                var res = response.split('|');
                logit(res);
                if (res[0] == '1') {
                    parseFile(res[1]);
                } else {
                    err('Файл ' + file.name + ' НЕ ЗАГРУЖЕН!<br>ОТВЕТ: ' + response + '<br>DATA: ' + $.param(data), 'error');
                }
            }
        });
    }

}

var sb = {};

sb.init = function ()
{
    // $('#mtabs').tabs('option','active',2);
    var now = new Date();
    var d = new Date();
    now = now.getTime();
    d.setTime(now - 1000 * 60 * 60 * 24 * 30 * 3);
    $('#sb_date1')
        .val(d.getDate() + '.' + (d.getMonth() + 1) + '.' + d.getFullYear())
        .css({width: '100px'})
        .datepicker();

    $('#sb_view')
        .button()
        .click(function ()
        {
            $('#sb_result').html('<img src="/assets/images/ax/siteheart.gif">');
            $.ajax({
                data: {act: 'sb_getTipos', date: $('#sb_date1').val(), gr: $('#sb_gr').val()},
                success: function (r)
                {
                    if (r.fres) {
                        var el = $('#sb_result');
                        el.html('<p>Найдено размеров: ' + r.num + '</p>');
                        if (r.num > 10000 && confirm("Количество записей больше 10000. Вывести на экран?") || r.num <= 10000) {
                            $('<table><tr><th>Бренд</th><th>Модель</th><th>Размер</th></tr><tbody></tbody></table>').appendTo(el);
                            el = $('#sb_result table tbody');
                            for (var k in r.data) {
                                $('<tr><td>' + r.data[k]['bname'] + '</td><td>' + r.data[k]['mname'] + '</td><td>' + r.data[k]['size'] + '</td></tr>').appendTo(el);
                            }
                            $('#sb_result table td, #sb_result table th').css({'border-bottom': '1px solid #CCC', 'text-align': 'left'});
                        }
                    } else $('#sb_result').html(r.fres_msg);
                }
            })
        });
    $('#sb_del')
        .button()
        .click(function ()
        {
            if (confirm('Удалять?')) {
                $('#sb_result').html('<img src="/assets/images/ax/siteheart.gif">');
                $.ajax({
                    data: {act: 'sb_delTipos', date: $('#sb_date1').val(), gr: $('#sb_gr').val()},
                    success: function (r)
                    {
                        if (r.fres) {
                            var el = $('#sb_result');
                            el.html('<p>Удалено размеров: ' + r.num + '</p>');
                        } else $('#sb_result').html(r.fres_msg);
                    }
                });
            }
        });
}

function infoPanel()
{
    var c = '';
    c += '<table><tr>';
    c += '<td>Добавлено брендов = <b>' + file.param['result']['insertedBrandsCounter'] + '</b></td>';
    c += '<td>Добавлено моделей = <b>' + file.param['result']['insertedModelsCounter'] + '</b></td>';
    c += '<td>Добавлено размеров = <b>' + file.param['result']['insertedTiposCounter'] + '</b></td>';
    if (isDefined(file.param['result']['hidedTipos']) && file.param['result']['hidedTipos'].length)
        c += '<td>Скрыто размеров = <b>' + file.param['result']['hidedTipos'].length + '</b></td>';
    c += '</tr>';
    c += '</table>';
    if (c != '') $('#info').html(c).show('slow'); else $('#info').hide();
}

function viewGrid()
{
    $('#mtabs').tabs('option','active',0);

    if (file.gr == 2)
        $.ajax({
            data: {act: 'getSup', file_id: file.file_id},
            success: function (r)
            {
                if (r.fres) {
                    self.CMI[file.gr]['Поставщик']['list'] = r.supList;
                    viewGrid_();
                } else err(fresMsg(r.fres_msg), 'error');
            }
        });
    else viewGrid_();
}

function viewGrid_()
{
    if(!file.file_id) return;

    var $ci=$('.ci #view-grid');
    $ci.html('');
    var $legend=$('<div class="ui-widget" style="margin:0 0 10px 0"><div style="border:1px dashed; padding: 0 10px;" class="ui-corner-all ww"></div></div>').appendTo($ci);
    $legend=$legend.find('.ww');

    if (file.gr == 2) $('<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: 0.3em;"></span><b>Колонка "Трансформации":</b> DIRECTHIT - полное совпадение цвета с базовым суффиксом; IDS - цвет попадает под список игнорированных; EDS - подставлено значение для пустого значения цвета; значение цвета - найден аналог в матрице и указан к какому базовому цвету он относится; ЕСЛИ НЕТ ЦВЕТА - значит цвета нет в матрице цветов. В режиме тестовой загрузки для позиций, для которых еще нет в базе сайта бренда, подстановка брендозависомого альтернативного суффикса не сработает - отобразиться альтернатива без привязки к бренду (если есть). Также, в колонке отображается преобразования Dia (Dia новое значение) и преобразование ступиц (SV новое значение).</p>').appendTo($legend);

    $legend.append('<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: 0.3em;"></span>С пометкой IGN в поле трансформаций могут выводиться причины игнорирования записи.</p>');

    $('<table id="viewGrid"></table><div id="lfPager"></div>').appendTo($ci);
    $('.ci #viewGrid').jqGrid({
        datatype: 'json',
        regional: 'ru',
        url: axUrl+'?act=view',
        colNames: function ()
        {
            //var a=['X','ID'];
            //var a=['ID'];
            var a = [];
            a.push('Статус размера', 'Статус модели', 'Статус бренда');
            var i = 0;
            for (var v in self.CMI[file.gr]) {
                a.push(v);
                i++;
            }
            return a;
        }(),
        colModel: function ()
        {
            var a = [
//				{name:'sel',index:'sel',align:'center',width:50,sortable:false,stype:''},
//				{name:'item_id',index:'item_id',sortable:true,align:'center',width:20}
            ];
            a.push({name: 'cstatus', index: 'cstatus', sortable: true, align: 'center', width: 30,  stype: 'select', editoptions: {}, searchoptions: {clearSearch: false, value: ":все;1:обновлен;2:добавлен;3:игнор"}});
            a.push({name: 'mstatus', index: 'mstatus', sortable: true, align: 'center', width: 30, stype: 'select', editoptions: {}, searchoptions: {clearSearch: false, value: ":все;1:обновлен;2:добавлен"}});
            a.push({name: 'bstatus', index: 'bstatus', sortable: true, align: 'center', width: 30, stype: 'select', editoptions: {}, searchoptions: {clearSearch: false, value: ":все;1:обновлен;2:добавлен"}});
            var i = 0;
            var b;
            for (var v in self.CMI[file.gr]) {
                b = {name: self.CMI[file.gr][v]['item_field'], index: self.CMI[file.gr][v]['item_field'], searchoptions: {clearSearch: false}, sortable: true, align: 'center', width: self.CMI[file.gr][v]['fieldWidth']};
                if (self.CMI[file.gr][v]['type'] == 'id') {
                    b.searchoptions = {clearSearch: false, value: ':Все'};
                    b.stype = "select";
                    for (var l in self.CMI[file.gr][v]['list']) {
                        b.searchoptions.value += ';' + self.CMI[file.gr][v]['list'][l] + ':' + l;
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
        //width: '100%',
        height: 'auto',
//		scroll: true,
        rowNum: 40,
        postData: {'file_id': file.file_id},
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
                $('.ci #viewGrid')[0].clearToolbar();
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

function parseFile(fn)
{
    $('#loading').html('<p>Идет распознавание колонок файла</p>');
    $('#loading').dialog('open');
    $.ajax({
        data: {act: 'upload', fname: fn, config: $('#configFrm').serialize()},
        success: function (r)
        {
            if (r.fres) {
                file.status = 0;
                file.gr = r.gr
                file.file_id = r.file_id;
                file.param = r.param;
                file.name = r.name;

                note('Файл ' + r.name + ' загружен');

                selectedFileInfo(file.name + ' - ' + 'не импортирован', true);
                $('#fileList').prepend('<li class="ui-corner-all ui-widget-content" file_id="' + r.file_id + '">' + (file.gr == 1 ? 'Ш: ' : 'Д: ') + r.name + '</li>');
                selectedFileInfo(file.name, true);
                $('#fileList li').removeClass('ui-selected');
                $('#fileList li[file_id=' + r.file_id + ']').addClass('ui-selected');

                for (var k in r.deletedFiles)
                    $('#fileList li[file_id=' + r.deletedFiles[k] + ']').remove();

                $('#importSetup').show();
                $('#delSuplrs').parents('td').show();
                $('#testImport').parents('td').show(100);

                if (r.gr == 2) {
                    $('#showDIAWin').show(100);
                    $('#showSVWin').show(100);
                    $('#showYSTWin').show(100);
                } else {
                    $('#showSVWin').hide(100);
                    $('#showDIAWin').hide(100);
                    $('#showYSTWin').hide(100);
                }

                $('#goImportPartial').parents('td').show();
                $('#goImportFull').parents('td').show();
                $('#info').hide();
                viewGrid();
            } else err(r.fres_msg, 'error');
        },
        complete: function ()
        {
            $('#loading').dialog('close');
        }
    });
}


function populate(form, data)
{
    for (var tag in data) {
        if ($(form + ' [name=' + tag + ']').length)
            switch ($(form + ' [name=' + tag + ']').get(0).tagName) {
                case 'INPUT':
                    if ($(form + ' [name=' + tag + ']').attr('type') == 'text') $(form + ' [name=' + tag + ']').val(data[tag]);
                    else if ($(form + ' [name=' + tag + ']').attr('type') == 'checkbox' && (parseInt(data[tag]) == 1)) $(form + ' [name=' + tag + ']').prop('checked', true);
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



function goImport(mode)
{
    DM.timerNotif();
    logit('file_id='+file.file_id);
    $('.dm_task_log').html('');
    $.ajax({
        data: {
            act: 'parse',
            configFrm: $('.ci #configFrm').serialize(),
            file_id: file.file_id,
            fileName: file.name,
            mode: mode
        },
        success: function(r)
        {
            if(r.fres) {
                DM.runFileId = file.file_id;
                $('.dm_task_pb').progressbar('option', 'value', 0);
                $('.dm_task_label').html('запуск задачи....');
                logit($('.ci #configFrm [name="delSuplrs[]"]').val());
                $('.ci #configFrm [name="delSuplrs[]"]').remove();
            }else{
                err(r.fres_msg);
            }
        }
    });
}

function executeTaskEvents()
{

    $('.dm_task_pb').progressbar();

    $('#testImport').button().click(function ()
    {
        goImport(1)
    });

    $('#goImportFull').button().click(function ()
    {
        if (confirm('**Полный** импорт, да?')) goImport(3);

    });

    $('#goImportPartial').button().click(function ()
    {
        if (confirm("Частичный импорта, да?")) goImport(2);

    });
}

function cfgTabEvents()
{
    $('.dm_restart').button().click(function()
    {
        logit('dm_restart fire');
        DM.sendCmd('stop');
        DM.timerNotif();
    });

    $('.dm_task_stop').button().click(function()
    {
        logit('dm_task_stop fire');
        DM.sendCmd('task_break');
        DM.timerNotif();
    });

    $('.dm_pause').button().click(function()
    {
        logit('dm_pause fire');
        if($(this).attr('paused')) {
            DM.sendCmd('resume');
        } else{
            DM.sendCmd('pause');
        }
        DM.timerNotif();
    });

    $('#dm_refreshForm').button();
}



DM={

    runFileId:'',
    lastLogTS:0,
    curTaskState: false,
    infoBlockShowed: false,

    timerNotif: function(msg)
    {
        if(!isDefined(msg)) msg='Выполнение операции...';
        $('#loading').html('<p>'+msg+'</p>').dialog('open');

        setTimeout(function()
        {
            $('#loading').dialog('close');
        },2000)
    },

    pingEvents: function ()
    {
        var self=this;
        dmPingSheduler=setInterval(function()
        {
            $.ajax({
                global: false,
                data: {
                    'act': 'DM_pingState',
                    lastTS: DM.lastLogTS
                },
                success: function(r)
                {
                    var taskState=r.data.task===false?false:r.data.task.state;

                    if(r.data.task===false || taskState=='finished' || taskState=='interrupted' || taskState=='error'){
                        if(DM.curTaskState!==taskState) {
                            if(taskState=='finished') {
                                self.hideInfoBlock(taskState);
                                selectedFileInfo(r.data.task.opt.fileName + ' - завершено', true);
                                $('.dm_task_label').html('');
                                $('.dm_task_pb').progressbar('option', 'value', 0);
                                if(DM.runFileId === file.file_id) {
                                    viewGrid_();
                                    selectFile(r.data.task.opt.file_id);
                                }
                            }else if(taskState=='interrupted') {
                                self.hideInfoBlock(taskState);
                                $('#importSetup').slideDown(300);
                                selectedFileInfo(r.data.task.opt.fileName + ' - прервано', true);
                                $('.dm_task_label').html('');
                                $('.dm_task_pb').progressbar('option', 'value', 0);
                                if(DM.runFileId === file.file_id) {
                                    viewGrid_();
                                }
                            }else if(taskState=='error') {
                                self.showInfoBlock(taskState);
                                $('#importSetup').slideDown(300);
                                selectedFileInfo(r.data.task.opt.fileName + ' - ошибка', true);
                            }
                        }
                    }else{
                        if(DM.curTaskState!==taskState) {
                            self.showInfoBlock(taskState);
                            $('#importSetup').slideUp(300);
                            selectedFileInfo(r.data.task.opt.fileName+' - в обработке', false)
                        }
                    }

                    DM.lastLogTS= r.data.TS;
                    if (r.data.log) {
                        var $tl = $('.dm_task_log');
                        var d, s;
                        logit('[DM.log]: start logging');
                        for (var k in r.data.log) {
                            d = new Date(r.data.log[k][0] * 1000);
                            s = (d.getHours()) + ':' + (d.getMinutes()) + ':' + (d.getSeconds()) + ' ' + r.data.log[k][1];
                            logit('[DM.log]: ' + s);
                            if(s.indexOf('[insertModelCache]:')==-1) $tl.prepend(s + "\n");
                        }
                    }

                    $('.dm_state').html(r.data.daemon.state+(r.data.daemon.paused?" / paused":''));
                    $('.dm_startTime').html(r.data.daemon.dt_started+'  ('+ r.data.daemon.tdiff+')');
                    $('.dm_tdiff').html(r.data.daemon.tdiff);
                    $('.dm_pid').html(r.data.daemon.pid);
                    $('.dm_memUsage').html((typeof r.data.daemon.memc_ != 'undefined'?(r.data.daemon.memc_+' / '):'')+r.data.daemon.mem_+' / '+ r.data.daemon.mem_peak_);

                    var $p=$('.dm_pause');
                    if(r.data.daemon.paused && $p.attr('paused') == undefined) {
                        $p
                            .attr('paused','1')
                            .button('option','label', 'Запустить');
                    } else if(!r.data.daemon.paused) {
                        $p
                            .removeAttr('paused')
                            .button('option','label', 'Приостановить');
                    }

                    if(r.data.task){
                        $('.dm_task_state').html(r.data.task.state+(r.data.daemon.paused?" (на паузе)":'')+ ' / daemon '+ r.data.daemon.state);
                        if(isDefined(r.data.task.dt_run)) {
                            $('.dm_task_t_run').html(r.data.task.dt_run);
                            $('.dm_task_elapsed').html(r.data.task.elapsed_);
                        }
                        if(isDefined(r.data.task.pg_label)) $('.dm_task_label').html(r.data.task.pg_label);
                        if(isDefined(r.data.task.pg_index)) $('.dm_task_pb').progressbar('option', 'value', r.data.task.pg_index);
                    }else{
                        $('.dm_task_state').html('нет задачи'+(r.data.daemon.paused?" (на паузе)":'')+ ' / daemon '+ r.data.daemon.state);
                        $('.dm_task_t_run, .dm_task_elapsed').html('-');
                    }

                    DM.curTaskState= r.data.task===false?false:r.data.task.state;
                }
            })
        },3000);
    },

    sendCmd: function (cmd)
    {
        $.ajax({
            data:{
                act: 'DM_sendCommand',
                cmd: cmd
            }
        });
    },

    hideInfoBlock: function(taskState)
    {
        $('#DM_task').slideUp(300);
        $('.ci .upload').slideDown(300);
        $('#importSetup').slideDown(300);
        DM.infoBlockShowed=false;
    },
    showInfoBlock: function(taskState)
    {
        $('#DM_task').slideDown(300);
        $('.ci .upload').slideUp(300);
        $('#importSetup').slideUp(300);
        DM.infoBlockShowed=true;
    }
}

function selectedFileInfo(msg, canExport)
{
    var $el=$('.ci #selectedFile');
    $el.html(msg)
        if(canExport) {
            $el.prepend('<a href="#" class="export"></a>');
            $('.ci #selectedFile .export')
                .css({
                    width: '24px',
                    height: '24px',
                    display: 'block',
                    float: 'left',
                    margin: '3px 20px 0 10px',
                    background: 'url(/cms/img/docs/csv-24.png) 0 0'
                })
                .click(function ()
                {
                    logit(self.file);
                    window.open(axUrl+'?act=exportGrid&gr=' + file.gr + '&file_id=' + file.file_id + '&fn=' + file.param.fname, '_self');

                    return false;
                });
        }
}