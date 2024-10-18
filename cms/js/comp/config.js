var opt = {id: null, tr: null, act: null, ch: [], group: null};

$(document).ready(function () {


    var loader = $('.workspace').cloader();
    $(document).ajaxStart(function () {
        loader.cloader('show');
    })
        .ajaxStop(function () {
            loader.cloader('hide');
        });

    $('.del').button().click(conf_del);
    $('#tabs').tabs();
    $('.add').button().click(conf_add);
    $('.edit').button().click(conf_edit);
    $('#testMailInfo').click(function (e) {
        $(e.target).append(loading2);
        $.ajax({
            data: {act: 'testMailInfo'},
            success: function (r) {
                if (r.fres) {
                    note(r.fres_msg);
                } else {
                    alert(r.fres_msg);
                }
            },
            complete: function () {
                $(e.target).children('#loading2').remove();
            }
        });
    });
    $('#testMailOrder').click(function (e) {
        $(e.target).append(loading2);
        $.ajax({
            data: {act: 'testMailOrder'},
            success: function (r) {
                if (r.fres) {
                    note(r.fres_msg);
                } else {
                    alert(r.fres_msg);
                }
            },
            complete: function () {
                $(e.target).children('#loading2').remove();
            }
        });
    });

    $('.save').button().hide().click(function (e) {
        opt.cha = {};
        for (var i = 0; i < opt.ch.length; i++)
            opt.cha[opt.ch[i]] = $('#tabs tr[id=' + opt.ch[i] + '] input').val();

        $.ajax({
            data: {act: 'saveAll', a: opt.cha},
            success: function (r) {
                if (r.fres) {
                    note('Записано ' + opt.ch.length);
                    $('.save').hide('fast');
                    opt.ch = [];
                } else {
                    note(r.fres_msg, 'error');
                }
            }
        });
    });

    $('#frm').submit(function (e) {
        e.preventDefault();
    });

    $('#tabs input').keydown(function (e) {
        $('.save').show('fast');
        opt.ch.push($(e.target).closest('tr').attr('id'));
    });

    $.ajaxSetup({
        type: 'POST',
        cache: false,
        dataType: 'json',
        url: '../be/config.php',
        error: Err
        /*,beforeSend: ajaxBeforeSend,
        complete: ajaxComplete*/
    });

    $('#editWin').dialog({
        autoOpen: false,
        width: 500,
        modal: true,
        buttons: {
            'Записать': function () {
                if (opt.act == 'edit') {
                    $.ajax({
                        data: {act: 'save', id: opt.id, f: $('#editWin form').serialize(), adm: $('#tabs [name=adm]').val()},
                        success: function (r) {
                            if (r.fres) {
                                var e = $('#tabs tr[id=' + opt.id + ']').children('td');
                                e.eq(2).children('input').val(r.V);
                                if ($('#tabs [name=adm]').val() == 1) {
                                    $('#editWin').dialog('option', 'title', r.title);
                                    e.eq(0).html(r.title);
                                    e.eq(1).html(r.name);
                                    e.eq(4).html(r.H ? 'да' : 'нет');
                                }
                                $('#editWin').dialog("close");
                            } else {
                                note(r.fres_msg, 'error');
                            }
                        }
                    });
                } else if (opt.act == 'add') {
                    $.ajax({
                        data: {act: 'add', group: opt.group, f: $('#editWin form').serialize()},
                        success: function (r) {
                            if (r.fres) {
                                $('#tabs-' + r.group + ' table')
                                    .append('<tr id="' + r.id + '">' +
                                        '<td title="' + r.name + '">' + r.title + '</td>' +
                                        '<td title="' + r.name + '">' + r.name + '</td>' +
                                        '<td><input type="text" value="' + r.V + '" /></td>' +
                                        '<td><button class="edit">Изменить</button><button class="del">Удалить</button></td>' +
                                        '</tr>');
                                applyStyles();
                                $('.edit').button().click(conf_edit);
                                $('.del').button().click(conf_del);
                                $('#editWin').dialog("close");
                                //							$('.save').button( "option", "disabled", false );
                            } else {
                                note(r.fres_msg, 'error');
                            }
                        }
                    });
                }

            },
            'Отмена': function () {
                $(this).dialog("close");
            }
        }
    });


    /* MIN_EXTRA   */

    var me = {lastsel1: '', lastsel2: ''};

    $('#me_grid1').jqGrid({
        hidegrid: false,
        datatype: 'json',
        url: '../be/min_extra.php?act=list&gr=1&P=1',
        editurl: '../be/min_extra.php?act=update&gr=1&P=1',
        colNames: ['Радиус', 'Минимальная наценка'],
        colModel: [
            {name: 'PVal', index: 'PVal', align: 'center', width: 50, sortable: false},
            {name: 'extra', index: 'extra', align: 'center', width: 50, editable: true, sortable: false}
        ],
        caption: 'Наценки на шины',
        sortname: 'PVal',
        sortorder: "asc",
        rownumbers: true,
        height: '100%',
        width: 300,
        loadError: Err,
        scroll: 1,
        loadComplete: loadComplete,
        onSelectRow: function (id) {
            if (me.lastsel1) {
                jQuery('#me_grid1').jqGrid('restoreRow', me.lastsel1);
            }
            jQuery('#me_grid1').jqGrid('editRow', id, true, null, function (xhr) {
                if (xhr.readyState == 4) {
                    if (xhr.responseText == 0) {
                        $('#me_grid1').trigger('reloadGrid');
                    } else {
                        return true;
                    }
                }
            });
            me.lastsel1 = id;
        }
    });

    $('#me_grid2').jqGrid({
        hidegrid: false,
        datatype: 'json',
        url: '../be/min_extra.php?act=list&gr=2&P=5',
        editurl: '../be/min_extra.php?act=update&gr=2&P=5',
        colNames: ['Радиус', 'Минимальная наценка'],
        colModel: [
            {name: 'PVal', index: 'PVal', align: 'center', width: 50, sortable: false},
            {name: 'extra', index: 'extra', align: 'center', width: 50, editable: true, sortable: false}
        ],
        caption: 'Наценки на диски',
        sortname: 'PVal',
        sortorder: "asc",
        rownumbers: true,
        height: '100%',
        width: 300,
        loadError: Err,
        scroll: 1,
        loadComplete: loadComplete,
        onSelectRow: function (id) {
            if (me.lastsel2) {
                jQuery('#me_grid2').jqGrid('restoreRow', me.lastsel2);
            }
            jQuery('#me_grid2').jqGrid('editRow', id, true, null, function (xhr) {
                if (xhr.readyState == 4) {
                    if (xhr.responseText == 0) {
                        $('#me_grid2').trigger('reloadGrid');
                    } else {
                        return true;
                    }
                }
            });
            me.lastsel2 = id;
        }
    });

    /*   END MIN_EXTRA   */

    /* ORDER DISCOUNT   */

    var os_lastsel;

    $('#os_grid').jqGrid({
        hidegrid: false,
        datatype: 'json',
        url: '../be/order_discount.php?act=list',
        editurl: '../be/order_discount.php',
        colNames: ['Критерий', 'Предел', 'Значение'],
        colModel: [
            {
                name: 'type', index: 'type', align: 'left', width: 150, sortable: false, editable: true, edittype: 'select',
                editoptions: {value: "0:При общей сумме заказов от (руб);1:При кол-ве заказов от"}
            },
            {name: 'lim', index: 'lim', align: 'center', width: 50, sortable: false, editable: true},
            {name: 'value', index: 'value', align: 'center', width: 50, sortable: false, editable: true},
        ],
        rownumbers: true,
        height: '100%',
//		viewrecords:true,
//		autowidth: true,
        pager: '#os_pagered',
        pgbuttons: false,
        pginput: false,
        width: 400,
        loadError: Err,
        scroll: 1,
        loadComplete: loadComplete,
        onSelectRow: function (id) {
            if (os_lastsel) {
                jQuery('#os_grid').jqGrid('restoreRow', os_lastsel);
            }
            jQuery('#os_grid').jqGrid('editRow', id, true, null, function (xhr) {
                if (xhr.readyState == 4) {
                    if (xhr.responseText == 0) {
                        $('#os_grid').trigger('reloadGrid');
                    } else {
                        return true;
                    }
                }
            });
            os_lastsel = id;
        }
    }).jqGrid('navGrid', "#os_pagered", {
        edit: true, add: true, del: true, search: false
    });

    /* END ORDER DISCOUNT    */

    /* CURVAL   */

    var curval_lastsel;

    $('#curval_grid1').jqGrid({
        hidegrid: false,
        datatype: 'json',
        url: '../be/config.php?act=curvalList',
        editurl: '../be/config.php?act=curValUpdate',
        colNames: ['Валюта', 'Курс'],
        colModel: [
            {name: 'cur', index: 'cur', align: 'center', width: 50, sortable: false},
            {name: 'curval', index: 'curval', align: 'center', width: 50, editable: true, sortable: false}
        ],
        caption: 'Валюты и курсы',
        rownumbers: true,
//		height:'100%',
        width: 300,
        loadError: Err,
        scroll: 1,
        loadComplete: loadComplete,
        onSelectRow: function (id) {
            if (curval_lastsel) {
                jQuery('#curval_grid1').jqGrid('restoreRow', curval_lastsel);
            }
            jQuery('#curval_grid1').jqGrid('editRow', id, true, null, function (xhr) {
                if (xhr.readyState == 4) {
                    if (xhr.responseText == 0) {
                        $('#curval_grid1').trigger('reloadGrid');
                    } else {
                        return true;
                    }
                }
            });
            curval_lastsel = id;
        }
    });

    /* END CURVAL  */

    /* проставочные кольца  */

    var rings_lastsel;

    $('#ringsWin').dialog({
        autoOpen: false,
        modal: false,
        height: 350,
        width: 350,
        position: [100, 50],
        buttons: {
            'Закрыть': function () {
                $(this).dialog("close");
            }
        },
        open: function () {
            $('#ringsWin').empty().html('<table id="ringsGrid"></table><div id="ringsNav"></div><div  id="ringsGrid_toppager"></div>');
            $('#ringsGrid').jqGrid({
                datatype: 'json',
                hidegrid: false,
                url: '../be/config.php?act=ringsList',
                editurl: '../be/config.php?act=ringsUpdate',
                colNames: ['DIA1', 'DIA2'],
                colModel: [
                    {name: 'v1', index: 'v1', align: 'center', width: 80, sortable: false, editable: true},
                    {name: 'v2', index: 'v2', align: 'center', width: 80, sortable: false, editable: true}
                ],
                rowNum: 999999,
                pager: 'ringsNav',
                shrinkToFit: true,
                width: '310',
                height: '100%',
                altRows: true,
                pgbuttons: false,
                scroll: 1,
                pginput: false,
                loadComplete: loadComplete,
                onSelectRow: function (id) {
                    if (rings_lastsel) {
                        jQuery('#ringsGrid').jqGrid('restoreRow', rings_lastsel);
                    }
                    jQuery('#ringsGrid').jqGrid('editRow', id, true, null, function (xhr) {
                        if (xhr.readyState == 4) {
                            if (xhr.responseText == 0) {
                                $('#ringsGrid').trigger('reloadGrid');
                            } else {
                                return true;
                            }
                        }
                    });
                    rings_lastsel = id;
                }
            }).jqGrid('navGrid', "#ringsNav", {
                    edit: true, add: true, del: true, search: false, cloneToTop: true
                }, {}, {}, {}, {}
            );
        }
    });

    $('#showRingsWin').button().click(function () {
        $('#ringsWin').dialog('open');
    });

    /* конец проставочные кольца  */

});   // ready

function conf_edit(e)
{
    opt.id = $(e.target).closest('tr').attr('id');
    opt.tr = $(e.target).closest('tr');
    opt.act = 'edit';
    $.ajax({
        data: {'act': 'get', id: opt.id},
        success: function (r) {
            if (r.fres) {
                $('#editWin').dialog('open');
                $('#editWin [name=V]').val(r.data.V);
                if ($('#tabs [name=adm]').val() == 1) {
                    $('#editWin [name=title]').val(r.data.title);
                    $('#editWin [name=name]').val(r.data.name);
                    $('#editWin [name=comment]').val(r.data.comment);
                    $('#editWin [name=widget]').val(r.data.widget);
                    $('#editWin [name=H]').prop('checked', r.data.H == true ? true : false);
                    $('#editWin [name=pos]').val(r.data.pos);
                    $('#editWin').dialog('option', 'title', r.data.title);
                }
            } else {
                note(r.fres_msg, 'error');
            }
        }
    });
}

function conf_add(e)
{
    opt.group = $(e.target).closest('button').attr('group');
    $('#editWin').dialog('option', 'title', 'Добавить новый');
    opt.act = 'add';
    $('#editWin').dialog('open');
    $('#editWin form').get(0).reset();
    $('#editWin [name=V]').html('');
    $('#editWin [name=title]').html('');
    $('#editWin [name=name]').val('');
    $('#editWin [name=comment]').html('');
    $('#editWin [name=widget]').html('');
    $('#editWin [name=H]').prop('checked', false);
}

function conf_del(e)
{
    if (!window.confirm('Удалить параметр?')) {
        return;
    }
    opt.id = $(e.target).closest('tr').attr('id');
    opt.tr = $(e.target).closest('tr');
    opt.act = 'del';
    $.ajax({
        data: {'act': 'del', id: opt.id},
        success: function (r) {
            if (r.fres) {
                $('#tabs tr[id=' + opt.id + ']').remove();
                applyStyles();
            } else {
                note(r.fres_msg, 'error');
            }
        }
    });
}

