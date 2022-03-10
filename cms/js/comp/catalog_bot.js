function do_form(id, action) {
    if (action == 0) document.forms['form1'].medit_id.value = id;
    document.forms['form1'].submit();
    return false;
}

$(document).ready(function () {

    $('.tooltip').tooltip({
        track: true, fade: 100, showURL: false
    });

    $.ajaxSetup({
        type: 'POST',
        cache: false,
        dataType: 'json',
        url: '../be/catalog.php',
        error: Err
    });

    var loader = $('.workspace').cloader();
    $(document).ajaxStart(function () {
        loader.cloader('show');
    })
        .ajaxStop(function () {
            loader.cloader('hide');
        });

    $('tr.inds td').css('background', '#63FF94');


    $('#dsAdd').click(function () {
        if ($('input.chks:checked').length > 0) {
            document.form1.linkDataset.value = 1;
            return true;
        } else if (confirm('Добавить ВСЕ размеры на всех страницах в набор?')) {
            document.form1.linkDataset.value = 1;
            return true;
        } else return false;
    });

    $('#dsRemove').click(function () {
        if ($('input.chks:checked').length > 0) {
            document.form1.unlinkDataset.value = 1;
            return true;
        } else if (confirm('Исключить ВСЕ размеры на всех страницах из набора?')) {
            document.form1.unlinkDataset.value = 1;
            return true;
        } else return false;
    });


    $('#tiErase').click(function (e) {
        if (window.confirm('Удалить все привязки?')) $('input[name=act]').val('ti_zero_all'); else e.preventDefault();
    });

    $('#tiEraseSel').click(function (e) {
        $('input[name=act]').val('ti_zero_sel');
    });

    $('.pages').click(function (e) {
        $('input[name=page]').val($(this).attr('page'));
        e.preventDefault();
        $('form[name=form1]').submit();
    });

    $('a.medit').click(function (e) {
        e.preventDefault();
        $('input[name=medit_id]').val($(this).parent().parent().attr('model_id'));
        $('form[name=form1]').submit();
    });

    $('a.cedit').click(function (e) {
        e.preventDefault();
        $('input[name=cedit_id]').val($(this).parent().parent().attr('cat_id'));
        $('form[name=form1]').submit();
    });

    $('a.bprice').click(function (e) {
        e.preventDefault();
        openwin('extra.php?brand_id=' + $(this).parent().parent().attr('brand_id'), 'extra');
    });


    $(document).on('click', 'a.chide', function (e) {
        e.preventDefault();
        var td = $(this).parent();
        var id = $(this).parent().parent().attr('cat_id');
        var s = td.html();
        if (id > 0) {
            td.html(loading2);
            $.ajax({
                data: {act: 'hSwitch', 'id': id},
                success: function (r) {
                    if (r.fres) {
                        td.html(r.v);
                    } else {
                        td.html(s);
                        note(r.fres_msg, 'error');
                    }
                }
            });
        } else note('нет ИД', 'note');
    });

    $(document).on('click', 'a.fixPrice', function (e) {
        e.preventDefault();
        var td = $(this).parent();
        var id = $(this).parent().parent().attr('cat_id');
        var cprice = parseInt($(this).attr('cprice'));
        var s = td.html();
        td.html(loading2);
        if (id > 0) {
            $.ajax({
                data: {
                    act: 'fixPriceSwitch',
                    id: id,
                    cprice: cprice
                },
                success: function (r) {
                    if (r.fres) {
                        td.html(r.v);
                    } else {
                        td.html(s);
                        note(r.fres_msg, 'error');
                    }
                }
            });
        } else note('нет ИД', 'note');
    });

    $(document).on('click', 'a.fixSc', function (e) {
        e.preventDefault();
        var td = $(this).parent();
        var id = $(this).parent().parent().attr('cat_id');
        var cprice = parseInt($(this).attr('cprice'));
        var s = td.html();
        td.html(loading2);
        if (id > 0) {
            $.ajax({
                data: {
                    act: 'fixScSwitch',
                    id: id
                },
                success: function (r) {
                    if (r.fres) {
                        td.html(r.v);
                    } else {
                        td.html(s);
                        note(r.fres_msg, 'error');
                    }
                }
            });
        } else note('нет ИД', 'note');
    });

    $(document).on('click', 'a.ignoreUpdate', function (e) {
        e.preventDefault();
        var td = $(this).parent();
        var id = $(this).parent().parent().attr('cat_id');
        var s = td.html();
        td.html(loading2);
        if (id > 0) {
            $.ajax({
                data: {act: 'ignoreUpdateSwitch', 'id': id},
                success: function (r) {
                    if (r.fres) {
                        td.html(r.v);
                        note(r.fres_msg);
                    } else {
                        td.html(s);
                        note(r.fres_msg, 'error');
                    }
                }
            });
        } else note('нет ИД', 'note');
    });

    $('a.cld').click(function (e) {
        e.preventDefault();
        $('input[name=ld_id]').val($(this).parent().parent().attr('cat_id'));
        $('form[name=form1]').submit();
    });

    $('form[name=form1]').bind('submit', function () {
        loader.cloader('show');
    });

    $('#del_sel').click(function (e) {
        $('input[name=del_sel]').val(1);
        $('form[name=form1]').submit();
    });

    $('#etalonSW').click(function (e) {
        $('input[name=act]').val('etalonSW');
        $('form[name=form1]').submit();
    });

    $('#hide_sel').click(function (e) {
        $('input[name=act]').val('hide_sel');
        $('form[name=form1]').submit();
    });
    $('#show_sel').click(function (e) {
        $('input[name=act]').val('show_sel');
        $('form[name=form1]').submit();
    });

    $('#add_to_balances_sel').click(function (e) {
        $('input[name=act]').val('add_to_balances_sel');
        $('form[name=form1]').submit();
    });
    $('#remove_from_balances_sel').click(function (e) {
        $('input[name=act]').val('remove_from_balances_sel');
        $('form[name=form1]').submit();
    });

    $('#show_all').click(function (e) {
        $('input[name=act]').val('show_all');
        $('form[name=form1]').submit();
    });

    $('#upd').click(function (e) {
        $('input[name=act]').val('upd');
    });

    $('#mbp').click(function (e) {
        $('input[name=act]').val('mbp');
    });

    $('#changeSC').click(function (e) {
        if ($('select[name=new_sc_mode]').val() == 'all')
            if (window.confirm('Изменения каснуться всех записей в базе данных. Уверены?'))
                $('input[name=act]').val('sc_all');
            else e.preventDefault();
        else if ($('select[name=new_sc_mode]').val() == 'select')
            $('input[name=act]').val('sc_select');
        else e.preventDefault();
    });

    $('#replaceBut').click(function (e) {
        $('input[name=act]').val('replace');
    });

    var selRow = {color: '', id: ''};

    $('.ltable td').bind('mouseover', function (e) {
        if (selRow.id == '') {
            selRow.id = $(e.target).closest('tr').attr('cat_id');
            selRow.color = $(e.target).closest('tr').children('td').css('background-color');
        } else {
            $('.ltable tr[cat_id=' + selRow.id + '] td').css({'background-color': selRow.color});
            selRow.color = $(e.target).closest('tr').children('td').css('background-color');
            selRow.id = $(e.target).closest('tr').attr('cat_id');
        }
        $('.ltable tr[cat_id=' + selRow.id + '] td').css({'background-color': '#bbbbbb'});
    });

    $('.ltable th').click(function (e) {
        if (isDefined($(this).attr('id'))) {
            var id = $(this).attr('id');
            var i = 0;
            var pi = $(this).prevAll().each(function () {
                i++;
            });
            $('#upd').fadeIn('slow');
            $('#groupOpToggle').trigger('click');
            $('.ltable tr').each(function () {
                var cur = $(this).children('td').eq(i);
                if (cur.attr('e') == undefined) {
                    cur.children('span').remove();
                    cur.html('<input type="input" name="' + id + $(this).attr('cat_id') + '"value="' + cur.text() + '" class="' + id + '">');
                    cur.attr('e', 1);
                }
            });

        }
    });

    $('.ltable th').each(function (i, e) {
        if (isDefined($(this).attr('id')) && $(this).attr('id') != '') {
            $(this).css({'background': '#123456', 'cursor': 'pointer'});
        }
    });

    $('.ltable input[type=checkbox]').css({'width': '20px'});

    $('#groupOpToggle').click(function (e) {
        e.preventDefault();
        $(this).hide();
        $('#groupOp').slideDown('fast');
        $('input[name=showGroupOp]').val('1');
    });

    $('#groupAfToggle').click(function (e) {
        e.preventDefault();
        $(this).hide();
        $('fieldset.af').slideDown('fast');
    });

    $('#hideGroupOp').click(function (e) {
        e.preventDefault();
        $('#groupOp').slideUp('fast');
        $('#groupOpToggle').show();
        $('input[name=showGroupOp]').val('0');
    });


    var af = $.fn.getCookie('__cp_catalog_bot_af');
    if (af != null && af != '') {
        af = unserialize(af);
    } else af = {};

    $('fieldset.af input[type=checkbox]').click(function (e) {
        if ($(this).prop('checked')) {
            af[$(this).val()] = 1;
            $.fn.setCookie('__cp_catalog_bot_af', serialize(af));
        } else {
            af[$(this).val()] = 0;
            $.fn.setCookie('__cp_catalog_bot_af', serialize(af));
        }
    });


    var sclLock=false;

    $('div#sclPopup').hover(function(){
        sclLock=true;
    },function(){
        sclLock=false;
        $('div#sclPopup').hide();
    });

    $('a.bprice').hover(function (e) {
        if (sclLock) return;
        sclEl=$(this);
        $('div#sclPopup').show()
            .css({'top': e.pageY - 8, 'left': e.pageX - 370, 'cursor': 'pointer', 'min-width': '450px'})
            .click(function(){
                sclEl.click();
            });
        //$('div#sclPopup #loader').show();
        var c = '<p>Отображаемая на сайте розничная цена <b>' + $(e.target).attr('cprice') + ' руб</b></p>';
        $('div#sclPopup #c').html(c);
        $.ajax({
            data: {act: 'getSclByCatId', cat_id: sclEl.parent().parent().attr('cat_id')},
            cache: true,
            success: function (r) {
                if (r.fres) {
                    var cc = '';
                    if (isDefined(r.scl)) for (var k in r.scl) {
                        var name = r.scl[k]['name'];
                        var price1 = r.scl[k]['price1'];
                        var price2 = r.scl[k]['price2'];
                        var price3 = r.scl[k]['price3'];
                        var sc = r.scl[k]['sc'];
                        var ignored = parseInt(r.scl[k]['ignored']);
                        if (ignored){
                            cc = cc + '<tr style="background: #808080;"><td>' + name + '</td><td align=center>' + sc + '</td><td align=center>' + price1 + ' руб</td><td align=center>' + price2 + ' руб</td><td align=center>' + price3 + ' руб</td><td align=center><sub>' + r.scl[k]['dt_added'] + '<br>' + r.scl[k]['dt_upd'] + '</sub></td></tr>';
                        }
                        else cc = cc + '<tr><td>' + name + '</td><td align=center>' + sc + '</td><td align=center>' + price1 + ' руб</td><td align=center>' + price2 + ' руб</td><td align=center>' + price3 + ' руб</td><td align=center><sub>' + r.scl[k]['dt_added'] + '<br>' + r.scl[k]['dt_upd'] + '</sub></td></tr>';
                    }
                    if (cc != '') {
                        c = c + '<h3>Информация о наличии по поставщикам</h3><table><tr><th align="left">Склад</th><th>Кол-во, шт.</th><th>Цена 1</th><th>Цена 2</th><th>Цена 3</th><th>Доб./Обн.</th></tr>' + cc + '</table>';
                        c = c + '<p>*) В контексте результатов импорта из программы TyreIndex: <br>цена 1 - Опт, Цена 2 - Рекомендуемая розница, Цена 3 - Собственная розница</p>';
                    }
                    c = c + '<table cellspacing="5"><tr><td>Время добавления</td><td>'+ r.dt_added+'</td></tr>';
                    c = c + '<tr><td>Время изменения</td><td>'+ r.dt_upd+'</td></tr>';
                    c = c + '<tr><td>UPD_ID</td><td>'+ r.upd_id+'</td></tr></table>';
                } else c = c + '<p>' + r.fres_msg + '</p>';
                $('div#sclPopup #c').html(c);
                $('div#sclPopup #c table td, div#sclPopup #c table th').css({'border': '1px solid #cccccc', 'padding': '5px'});
            },
            complete: function () {
                $('div#sclPopup #loader').hide();
            }
        });
    }, function () {
        //if(!sclLock) setTimeout(function(){$('div#sclPopup').hide()},1000);
       // $('div#sclPopup').hide()
    });

    $('.invertChks').change(function () {
        var form = $(this).attr('data-form');
        $('#' + form + ' input.chks').each(function () {
            $(this).prop('checked', !$(this).prop('checked'));
        });
    });


});

