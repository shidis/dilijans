if(typeof parent.framesView != 'undefined'){
    var frameSet = window.frameElement.parentNode;
    frameSet.cols=parent.framesModel.noSidebar.cols;
    frameSet.rows=parent.framesModel.noSidebar.rows;
}

$(function() {
    if(isDefined(window.setup)) INIT();

});

function INIT ()
{

    $.ajaxSetup({
        type: 'POST',
        cache: false,
        dataType: 'json',
        url: '../be/orderEdit.php',
        error: Err
    });

    window.loader = $('.workspace').cloader();
    window.axActive = false;
    $(document)
        .ajaxStart(function ()
        {
            loader.cloader('show');
            window.axActive = true;
        })
        .ajaxStop(function ()
        {
            loader.cloader('hide');
            window.axActive = false;
        });

    $('body').prepend('<div id="overlayDlg" title="Подождите..."></div>');
    $('#overlayDlg').dialog({
        autoOpen: false,
        modal: true,
        resizable: true,
        closeOnEscape: false,
        height: 80,
        width: 300
    });

    $('.edit_area').tooltip();

    if (typeof pingOrders != 'undefined' && pingOrders)
        window.pingator = setInterval(function ()
        {
            $.ajax({
                global: false,
                data: {
                    act: 'ping',
                    prevDT: window.checkDT,
                    order_id: setup.order_id
                },
                success: function (r)
                {
                    if (r.fres === false) {
                        note('ПингБОТ остановлен. <br>' + r.fres_msg + '<br>Срочно обратитесь с администратору с этой ошибкой.', 'error');
                        window.clearInterval(pingator);
                    } else {
                        window.prevDT = r.lastHitDT;
                        if (isDefined(r.fres.otherUsers)) {
                            ul = [];
                            for (var k in r.fres.otherUsers) ul.push(r.fres.otherUsers[k].shortName);
                            note('<b>ВНИМАНИЕ!</b><br>Пользователь: <br><b><i>' + ul.join(', ') + '</i></b><br>вместе с вами работает с этим заказом.');
                        }
                    }
                },
                error: efoo
            });
        }, window.pingBotInt * 1000);

    $('.settings a').click(settingsClick);

    clientDataEvents();

    specDataEvents();

    exportEvents();

    refreshInterface();


}

function clientDataEvents()
{

    $('.client-data select[name!="af[ptype]"], .edit_area [name=tech]').change(postClientData);
    $('.client-data select[name="af[ptype]"]').change(changePType);
    $('.client-data input[type=text], .client-data textarea').bind('change', postClientData);

    $('.client-data [name="af[carrier_co]"]')
        .autocomplete({
            minLength: 1,
            autoFocus: true,
            source: window.setup.carrier_co_names,
            change: function(){
                $(this).trigger('change');
            }
        });


    $('.back').button().click(function ()
    {
        if (window.axActive)
            $(document)
                .ajaxStop(function ()
                {
                    location.href = urldecode(window.setup.ref);
                });
        else {
            loader.cloader('show');
            location.href = urldecode(window.setup.ref);
            return false;
        }
    });

    if(window.opener) {
        $('.close-win').button().click(function ()
        {
            if (window.axActive)
                $(document)
                    .ajaxStop(function ()
                    {
                        window.close();
                    });
            else {
                loader.cloader('show');
                window.close();
                return false;
            }
        });
    }


    if(window.setup.LD==2 && window.setup.editable)
    {
        $('.back').hide();

        $('.confirmNewOrder')
            .button()
            .click(confirmNewOrder)
            .show();

        $('.cancelNewOrder')
            .button()
            .click(cancelNewOrder)
            .show();
    } else{
        if(window.opener) $('.close-win').show();
    }

    $('.chState').each(function ()
    {
        $(this)
            .css({'width': ($(this).outerWidth() * 1 + 30) + 'px'})
            .chosen({
                disable_search_threshold: 20
            })
            .change(changeOrderStateClick);
    });

    $('.client-data select').each(function ()
    {
        $(this)
            .css({'width': ($(this).outerWidth() * 1 + 30) + 'px'})
            .chosen({
                disable_search_threshold: 10
            });
    });


}


function postClientData(e)
{
    var axblock = $(e.target).hasClass('axblock') || $(e.target).attr('name') == 'af[method]';
    if (axblock) {
        $('#overlayDlg').html('...выполняется операция').dialog('open');
    }

    $.ajax({
        data: {
            act: 'dataEdit',
            field: $(e.target).attr('name'),
            newVal: $(e.target).val(),
            order_id: window.setup.order_id
        },
        success: function (r)
        {
            if (r.fres) {
                if (r.fres_msg != '') note(r.fres_msg);
                window.setup = array_merge(window.setup, r.setup);

                // после смены метода оплаты
                if (isDefined(r.oStates)) {
                    $('.chState').html('');
                    for (var k in r.oStates) {
                        $('.chState').append('<option value="' + k + '">' + r.oStates[k] + '</option>');
                    }
                    $('.chState option[value=' + window.setup.stateId + ']').prop('selected', true);
                    $('.chState').trigger('chosen:updated');
                }

            } else {
                if (r.fres_msg != '') err(r.fres_msg); else err('Неверное начение. Не записано и восстановлено предыдущее.');
                $(e.target).val(r.prevVal);
                if (e.target.tagName == 'SELECT') $(e.target).trigger('chosen:updated');
            }
        },
        complete: function ()
        {
            if (axblock) $('#overlayDlg').dialog('close');
        }
    })
}

function changePType(e)
{
    var ptype = $(e.target).val();
    $.ajax({
        data: {
            act: 'dataEdit',
            field: 'af[ptype]',
            newVal: ptype,
            order_id: window.setup.order_id
        },
        success: function (r)
        {
            if (r.fres) {
                window.setup = array_merge(window.setup, r.setup);

                if (r.fres_msg != '')
                    note(r.fres_msg);
                else note('Тип покупателя изменен');

                for (var k in window.setup.ptype_varList) {
                    if (ptype == k)
                        $('tbody.ptype' + k).removeClass('off');
                    else
                        $('tbody.ptype' + k).addClass('off');
                }

            } else {
                err(r.fres_msg);
                $(e.target).val(r.prevVal).trigger('chosen:updated');
            }
        },
        error: efoo
    });
}


function refreshInterface()
{
    if (window.setup.notEditableState) {
        if (!$('#notEditableState').length) $(wrapWarn('В текущем  статусе заказ не подлежит редактированию', 'notEditableState')).appendTo('#notifArea');
    }
    else $('#notEditableState').remove();

    if (window.setup.notEditableUser) {
        if (!$('#notEditableUser').length) $(wrapWarn('Изменения в заказ может вносить только его обслуживающий в настоящий момент сотрудник', 'notEditableUser')).appendTo('#notifArea');
    }
    else $('#notEditableUser').remove();

    if (!window.setup.editable) {
        $('.client-data input, .client-data textarea, .client-data select, .spec-data input, .spec-data select, .spec-data button, #ctrls input, #ctrls select').prop('disabled', true);
        inline_disable_edit(true);
        $('.back').show();
        $('.confirmNewOrder').hide();
        $('.cancelNewOrder').hide();

    } else {
        $('.client-data input, .client-data textarea, .client-data select, .spec-data input, .spec-data select, .spec-data button, #ctrls input, #ctrls select').prop('disabled', false);
        inline_disable_edit(false);
    }

    /*
     разрешаем ввод tech и смену статуса если: менеджер заказа == текущему юзеру ИЛИ выключено изолированное изменение ИЛИ статус заказа ==0
     */
    if (window.setup.loggedUserId == window.setup.cUserId || !window.setup.oStatesGrouped[window.setup.method][window.setup.stateId]['isolatedChanges'] || window.setup.stateId == 0) {
        $('textarea[name="tech"], .chState').prop('disabled', false);
    } else {
        $('textarea[name="tech"], .chState').prop('disabled', true);
    }

    $('.edit_area select').each(function ()
    {
        $(this).trigger('chosen:updated');
    });
}

function changeOrderStateClick(e)
{
    var $el=$(e.target);

    if($el.val()==-1 && isDefined(setup.adminCfg.cancelReasons))
    {
        var html='<div id="cancelOrderDlg" title="Следует указать причину отмены заказа"><form>'
        if(_.size(setup.adminCfg.cancelReasons)){
            html+='<div style="overflow: hidden">';
            _.each(setup.adminCfg.cancelReasons, function(v,k){
                html+='<div><input type="radio" name="reason" value="'+k+'" id="__reason'+k+'"><label for="__reason'+k+'">'+v+'</label> </div>';
            });
            html+='<div><input type="radio" name="reason" value="" id="__reason" checked><label for="__reason">Указать вручную</label> </div>';
            html+='</div>';
        }
        html+='<textarea id="reason_str" class="ui-corner-all" style="width: 360px; max-width: 360px; height: 80px; margin-top: 10px" name="reason_str" placeholder="подробно причина отмены заказа (ctrl-enter)"></textarea>';
        html+='</form></div>';

        var $f=$(html)
            .appendTo('body')
            .dialog({
                autoOpen: true,
                modal: true,
                resizable: false,
                closeOnEscape: true,
                height: 'auto',
                width: 400,
                position: { my: "right center", at: "center center", of: $('.odata')},
                close: function ()
                {
                    $el.val(window.setup.stateId).trigger('chosen:updated');
                    $(this).dialog('destroy').remove();
                },
                buttons: {
                    OK: {
                        text: 'Подтвердить',
                        click: function()
                        {
                            if($f.find('#reason_str').val()=='' && ($f.find('[name=reason]').length && $f.find('[name=reason]:checked').val()=='' || !$f.find('[name=reason]').length)){
                                note('Не указана причина отмены заказа. Напишите ее подробно.');
                                return false;
                            }
                            changeOrderState(e, $f.find('form').serialize());
                            $(this).dialog('destroy').remove();
                        }
                    },
                    CANCEL: {
                        text: 'Закрыть окно',
                        click: function()
                        {
                            $(this).dialog('close');
                        }
                    }
                },
                open: function()
                {
                    //$(this).find('#reason_str').focus();
                }
            });

        $f.find('#reason_str').on({
            focus: function()
            {
                $f.find('[name=reason][value=""]').prop('checked',true);
            },
            keypress: function(e)
            {
                if(e.which==10 && e.ctrlKey) {
                    var buttons = $f.dialog("option", "buttons");
                    buttons['OK'].click();
                }
            }
        });

        $f.find('[name=reason]').change(function()
        {
            $f.find('#reason_str').val('');
        });

    }else if($el.val()==-3 && isDefined(setup.adminCfg.delayedOrders))
    {
        var html='<div id="delayOrderDlg" title="Отложить заказ"><form style="text-align: center">'
        html+="<p>Через сколько дней напомнить о заказе?</p>"
        html+='<p><input type="text" style="width: 40px; text-align: center" class="days ui-corner-all" name="days" value="" ></p>';
        html+='<p>или укажите дату напоминания:</p>';
        html+='<p><input type="text" class="date ui-corner-all" style="width: 90px; text-align: center" name="date" value="" placeholder="00-00-000" ></p>'
        html+='<p><small>* В указанный срок заказ изменит свой статус на Новый.</small></p>'
        html+='<div class="notif" style="color: red; font-weight: bold"></div>';
        html+='</form></div>';

        var $f=$(html)
            .appendTo('body')
            .dialog({
                autoOpen: true,
                modal: true,
                resizable: false,
                closeOnEscape: true,
                height: 'auto',
                width: 260,
                position: { my: "right center", at: "center center", of: $('.odata')},
                close: function ()
                {
                    $el.val(window.setup.stateId).trigger('chosen:updated');
                    $(this).dialog('destroy').remove();
                },
                buttons: {
                    OK: {
                        text: 'Подтвердить',
                        click: function()
                        {
                            if($f.find('.notif').html()!='') {
                                note('Неверная дата');
                                return;
                            }
                            changeOrderState(e, $f.find('form').serialize());
                            //$el.val(window.setup.stateId).trigger('chosen:updated');
                            $(this).dialog('destroy').remove();
                        }
                    },
                    CANCEL: {
                        text: 'Закрыть',
                        click: function()
                        {
                            $(this).dialog('close');
                        }
                    }
                },
                open: function()
                {
                    //$(this).find('.days').focus();
                }
            });

        $f.find('.date')
            .mask("99-99-9999")
            .datepicker({
                dateFormat: 'dd-mm-yy',
                changeMonth: true
            }).on({
                change: function()
                {
                    var $notif=$f.find('.notif');
                    $notif.html('');
                    $f.find('.days').val('');
                    var date=$(this).val();
                    var ex = /([0-9]{2})-([0-9]{2})-([0-9]{4})/.exec(date);
                    if(ex===null) {
                        $notif.html('некоректная дата');
                        return false;
                    }
                    var d=new Date(ex[3],ex[2]-1,ex[1]);
                    var today=new Date();
                    if((today-d)>0){
                        $notif.html('некоректная дата');
                        return false;
                    }
                    if(d.getDay()==0 || d.getDay()==6) $notif.html('Это выходной');
                    $f.find('.days').val(''+Math.ceil((d-today)/(1000*60*60*24)));
                }
            });

        $f.find('.days')
            .on({
                keyup: function()
                {
                    var days=$(this).val();
                    if(!/^[0-9]{1,3}$/.test(days)) return false;
                    var $date=$f.find('.date');
                    var $notif=$f.find('.notif');
                    var d=new Date();
                    d.setMinutes(0);
                    d.setHours(0);
                    d.setDate( d.getDate() + days*1 );
                    if(d.getDay()==0 || d.getDay()==6) $notif.html('Это выходной'); else $notif.html('');
                    $date.val(('0'+d.getDate()).slice(-2)+'-'+('0'+(d.getMonth()+1)).slice(-2)+'-'+d.getFullYear());
                }
            });


    }else{
        changeOrderState(e, []);
    }
}

function changeOrderState(elist, d)
{
    $('#overlayDlg').html('...выполняется операция').dialog('open');
    $.ajax({
        data: {
            act: 'changeState',
            newStateId: $(elist.target).val(),
            order_id: $(elist.target).attr('order_id'),
            data: d
        },
        success: function (r)
        {
            if (r.fres) {
                window.setup = array_merge(window.setup, r.setup);
                if (r.fres_msg != '') note(r.fres_msg); else note('Статус заказа изменен');
                if (isDefined(r.mgrFullName)) $('.mgrFullNameName').html(r.mgrFullName);
                $el=$($('#slogTypeLogProtected').render(r.slogRow)).prependTo('.slog-box .data .list');
                slogEdit_bind($el);
                setup.slog.numLogs++;
                updateSLogCounters();
                refreshInterface();
            } else {
                err(r.fres_msg);
                $(elist.target).val(window.setup.stateId).trigger('chosen:updated');
            }
        },
        complete: function ()
        {
            $('#overlayDlg').dialog('close');
        }

    })

}


// *********************************************************

function specDataEvents()
{

    items_name_bind();
    dops_name_bind();

    $(document).on('change', '#items .iprice', items_price_change);
    $(document).on('change', '#dops .dprice', dops_price_change);

    $(document).on('change', '#items .iam', items_am_change);
    $(document).on('change', '#dops .dam', dops_am_change);

    $(document).on('click', '#items .idel', items_del_click);
    $(document).on('click', '#dops .ddel', dops_del_click);

    if (isDefined(setup.adminCfg.purchase)) {
        $(document).on('change', '#items .ipprice', items_pprice_change);
        if (isDefined(window.setup.adminCfg.purchase.suplrSelectEnabled) && window.setup.adminCfg.purchase.suplrSelectEnabled)
            $(document).on('click', '#items .suplrSel', items_suplrSel_click);
        if (isDefined(setup.adminCfg.purchase.dopPPriceEnabled) && setup.adminCfg.purchase.dopPPriceEnabled)
            $(document).on('change', '#dops .dpprice', dops_pprice_change);
    }

    if (isDefined(setup.adminCfg.reservation)) {
        $(document).on('change', '#items .ireserveNum', items_reserveNum_change);
        items_reserveDate_bind();
        items_suplrId_bind();
    }

    if (isDefined(setup.adminCfg.delivery)) {

        $("#ctrls #deliveryDate")
            .mask("99-99-9999")
            .datepicker({
                dateFormat: 'dd-mm-yy',
                changeMonth: true
            })
            .unbind('change')
            .change(deliveryDate_change);

        $("#ctrls #suplrPaymentDate")
            .mask("99-99-9999")
            .datepicker({
                dateFormat: 'dd-mm-yy',
                changeMonth: true
            })
            .unbind('change')
            .change(suplrPaymentDate_change);

        $("#ctrls #billDate")
            .mask("99-99-9999")
            .datepicker({
                dateFormat: 'dd-mm-yy',
                changeMonth: true
            })
            .unbind('change')
            .change(billDate_change);



        $("#ctrls #TTN").unbind('change').change(TTN_change);
    }

    if (isDefined(setup.adminCfg.drivers)) {

        $("#ctrls #driverId").each(function ()
        {
            $(this).css({'width': ($(this).outerWidth() * 1 + 30) + 'px'})
                .chosen({
                    disable_search_threshold: 8
                })
                .unbind('change')
                .change(driverId_change);
        })

    }

    $('#item-add-frm').unbind('submit').submit(items_postNew);

    $('#item-add .ai-cat_id').unbind('keydown').keydown(function (e)
    {
        if (e.which == 13) {
            e.preventDefault();
            findCatById(e);
        } else if (e.which == 86) {
            setTimeout(function ()
            {
                findCatById(e)
            }, 500);
        }
    });

    $('#item-add .ai-name').autocomplete({
        minLength: 1,
        autoFocus: true,
        source: window.setup.dopNames
    });

    $('#itogs #discount').change(discount_change);

    $('#itogs #delivery_cost').change(deliveryCost_change);


}

function findCatById(e)
{
    $('#overlayDlg').html('ищю товар по введенному коду...').dialog('open');
    $.ajax({
        data: {
            act: 'findCatById',
            value: $(e.target).val()
        },
        success: function (r)
        {
            if (r.fres) {
                $('#item-add .ai-name').val(r.name);
                $('#item-add .ai-price').val(r.price);
                if (r.sc < 4)  $('#item-add .ai-am').val(r.sc); else  $('#item-add .ai-am').val('4');
                note('На складе ' + r.sc + ' шт.')
            } else note(r.fres_msg);
        },
        complete: function ()
        {
            $('#overlayDlg').dialog('close');
        }
    });
}

function inline_disable_edit(disable)
{
    $('#items .iname, #dops .dname').each(function ()
    {
        if (disable) $(this).editable('disable'); else $(this).editable('enable');
    });
}

function items_name_bind()
{
    $('#items .iname').each(function ()
    {
        $(this).editable({
            url: '../be/orderEdit.php?act=item',
            name: 'name',
            pk: $(this).parent().parent().attr('item_id'),
            params: {
                order_id: window.setup.order_id
            },
            title: 'Наименование',
            inputclass: 'xe-items-name',
            validate: function (value)
            {
                if ($.trim(value) == '') {
                    return 'Название товара не может быть пустым';
                }
            },
            success: function (r, newValue)
            {
                // если return !true то можно вывести месседж о чем то
                // newValue - введенное юзером значение новое
                return {newValue: r.newVal}
            }
        });
    });

}

function dops_name_bind()
{
    $('#dops .dname').each(function ()
    {
        $(this).editable({
            url: '../be/orderEdit.php?act=dop',
            name: 'name',
            pk: $(this).parent().parent().attr('dop_id'),
            params: {
                order_id: window.setup.order_id
            },
            title: 'Наименование',
            inputclass: 'xe-items-name',
            validate: function (value)
            {
                if ($.trim(value) == '') {
                    return 'Наименование не может быть пустым';
                }
            },
            success: function (r, newValue)
            {
                // если return !true то можно вывести месседж о чем то
                // newValue - введенное юзером значение новое
                return {newValue: r.newVal}
            }
        });
    });

}

function items_price_change(e)
{
    var $v = $(e.target).val();
    var item_id = $(e.target).parents('tr').attr('item_id');
    $.ajax({
        data: {
            act: 'item',
            name: 'price',
            pk: item_id,
            value: Math.abs($v),
            order_id: window.setup.order_id
        },
        success: function (r)
        {
            if (r.fres) {
                $(e.target).val(r.newVal)
                $('#oItog').html(r.cost);
                if(isDefined(window.setup.adminCfg['purchase']) && isDefined(window.setup.adminCfg['purchase']['dopPPriceEnabled']) && window.setup.adminCfg['purchase']['dopPPriceEnabled']) {
                    checkMarginAlert(r._margin, r.margin);
                }
            } else err(r.fres_msg);
        }
    })
}

function dops_price_change(e)
{
    var $v = $(e.target).val();
    var dop_id = $(e.target).parents('tr').attr('dop_id');
    $.ajax({
        data: {
            act: 'dop',
            name: 'price',
            pk: dop_id,
            value: Math.abs($v),
            order_id: window.setup.order_id
        },
        success: function (r)
        {
            if (r.fres) {
                $(e.target).val(r.newVal)
                $('#oItog').html(r.cost);
                if(isDefined(window.setup.adminCfg['purchase']) && isDefined(window.setup.adminCfg['purchase']['dopPPriceEnabled']) && window.setup.adminCfg['purchase']['dopPPriceEnabled']) {
                    checkMarginAlert(r._margin, r.margin);
                }
            } else err(r.fres_msg);
        }
    })
}

function dops_pprice_change(e)
{
    var $v = $(e.target).val();
    var dop_id = $(e.target).parents('tr').attr('dop_id');
    $.ajax({
        data: {
            act: 'dop',
            name: 'af[' + setup.adminCfg.purchase.DBF_dop_pprice + ']',
            pk: dop_id,
            value: Math.abs($v),
            order_id: window.setup.order_id
        },
        success: function (r)
        {
            if (r.fres) {
                $(e.target).val(r.newVal);
                $('#pItog').html(r.pcost);
                checkMarginAlert(r._margin, r.margin);
            } else err(r.fres_msg);
        }
    })
}


function items_am_change(e)
{
    var $v = $(e.target).val();
    if (($v * 1) <= 0) {
        note('Введите количество');
        return false;
    }
    var item_id = $(e.target).parents('tr').attr('item_id');
    $.ajax({
        data: {
            act: 'item',
            name: 'amount',
            pk: item_id,
            value: $v,
            order_id: window.setup.order_id
        },
        success: function (r)
        {
            if (r.fres) {
                $(e.target).val(r.newVal);
                $('#oItog').html(r.cost);
                if(isDefined(window.setup.adminCfg['purchase']) && isDefined(window.setup.adminCfg['purchase']['dopPPriceEnabled']) && window.setup.adminCfg['purchase']['dopPPriceEnabled']) {
                    $('#pItog').html(r.pcost);
                    checkMarginAlert(r._margin, r.margin);
                }
            } else err(r.fres_msg);
        }
    })
}

function checkMarginAlert(_margin, margin)
{
    var $pm=$('#pmargin');
    $pm.html('маржа: '+margin+' руб');
    if(_margin<window.setup.marginAlert){
        $pm.addClass('pmargin-alert');
    } else {
        $pm.removeClass('pmargin-alert');
    }
}

function dops_am_change(e)
{
    var $v = $(e.target).val();
    if (($v * 1) <= 0) {
        note('Введите количество');
        return false;
    }
    var dop_id = $(e.target).parents('tr').attr('dop_id');
    $.ajax({
        data: {
            act: 'dop',
            name: 'amount',
            pk: dop_id,
            value: $v,
            order_id: window.setup.order_id
        },
        success: function (r)
        {
            if (r.fres) {
                $(e.target).val(r.newVal);
                $('#oItog').html(r.cost);
                if(isDefined(window.setup.adminCfg['purchase']['dopPPriceEnabled']) && window.setup.adminCfg['purchase']['dopPPriceEnabled']) {
                    $('#pItog').html(r.pcost);
                    checkMarginAlert(r._margin, r.margin);
                }
            } else err(r.fres_msg);
        }
    })
}

function items_del_click(e)
{
    var item_id = $(e.target).parents('tr').attr('item_id');
    $.ajax({
        data: {
            act: 'item',
            name: 'del',
            pk: item_id,
            order_id: window.setup.order_id
        },
        success: function (r)
        {
            if (r.fres)
                $('#items tr[item_id=' + item_id + ']').fadeOut(function ()
                {
                    $(this).remove();
                    $('#oItog').html(r.cost);
                    if(isDefined(window.setup.adminCfg['purchase']['dopPPriceEnabled']) && window.setup.adminCfg['purchase']['dopPPriceEnabled']) {
                        $('#pItog').html(r.pcost);
                        checkMarginAlert(r._margin, r.margin);
                    }
                });
            else err(r.fres_msg);
        }
    })
}

function dops_del_click(e)
{
    var dop_id = $(e.target).parents('tr').attr('dop_id');
    $.ajax({
        data: {
            act: 'dop',
            name: 'del',
            pk: dop_id,
            order_id: window.setup.order_id
        },
        success: function (r)
        {
            if (r.fres)
                $('#dops tr[dop_id=' + dop_id + ']').fadeOut(function ()
                {
                    $(this).remove();
                    $('#oItog').html(r.cost);
                    if(isDefined(window.setup.adminCfg['purchase']['dopPPriceEnabled']) && window.setup.adminCfg['purchase']['dopPPriceEnabled']) {
                        $('#pItog').html(r.pcost);
                        checkMarginAlert(r._margin, r.margin);
                    }
                });
            else err(r.fres_msg);
        }
    })
}

function items_pprice_change(e)
{
    var $v = $(e.target).val();
    var item_id = $(e.target).parents('tr').attr('item_id');
    $.ajax({
        data: {
            act: 'item',
            name: 'af[' + setup.adminCfg.purchase.DBF_pprice + ']',
            pk: item_id,
            value: Math.abs($v),
            order_id: window.setup.order_id
        },
        success: function (r)
        {
            if (r.fres) {
                $(e.target).val(r.newVal);
                if(isDefined(window.setup.adminCfg['purchase']['dopPPriceEnabled']) && window.setup.adminCfg['purchase']['dopPPriceEnabled']) {
                    $('#pItog').html(r.pcost);
                    checkMarginAlert(r._margin, r.margin);
                }
            } else err(r.fres_msg);
        }
    })
}

function items_suplrSel_click(e)
{
    var cat_id = $(e.target).parents('tr').attr('cat_id');

    $('<div id="suplrListDlg" title="Все поставщики типоразмера"></div>')
        .appendTo('body')
        .dialog({
            autoOpen: true,
            modal: true,
            resizable: true,
            closeOnEscape: true,
            height: 'auto',
            width: 'auto',
            position: { my: "left top", at: "left bottom", of: e.target},
            close: function ()
            {
                $(this).dialog('destroy').remove();
            },
            buttons: [
                {
                    text: 'Закрыть',
                    click: function ()
                    {
                        $(this).dialog('close');
                    }
                }
            ],
            open: function ()
            {
                $(this).html('<img src="/assets/images/ax/siteheart.gif" align="middle">&nbsp;&nbsp;&nbsp;Загружаю список поставщиков...');
                var $w = $(this);
                $.ajax({
                    data: {
                        act: 'suplrList',
                        cat_id: cat_id
                    },
                    success: function (r)
                    {
                        if (r.fres) {
                            $w.html('');
                            var $tbl = $('<table></table>')
                                .appendTo($w)
                                .append('<tr><th></th><th align="left">Название</th><th>Кол-во на складе</th><th>Цена</th><th>Дата обновления</th></tr>');

                            var i = 0;
                            var future, futureTitle, $td;
                            for (var k in r.suplrs) {
                                i++;
                                future='';
                                futureTitle='Доставки на ближайшие '+window.setup.adminCfg.purchase.futureSuplr.days+" дня с товарами этого поставщика:\n";
                                if(_.size(r.suplrs[k]['future'])){
                                    for(var fi=1; fi<=window.setup.adminCfg.purchase.futureSuplr.days; fi++){
                                        if(typeof r.suplrs[k]['future'][fi] != 'undefined'){
                                            future+='<i class="c1"></i>';
                                            futureTitle+=(fi==1?'(завтра ':("("+r.suplrs[k]['future'][fi]['deliveryDate'])) + ' -> '+r.suplrs[k]['future'][fi]['itemsNum']+" шт.) \n";
                                        }else{
                                            future+='<i class="c0"></i>';
                                        }
                                    }
                                }
                                if(future.length) {
                                    future='<span class="future3" title="'+futureTitle+'">'+future+'</span>';
                                }

                                $td = $('<tr></tr>')
                                    .appendTo($tbl)
                                    .append('<td></td><td nowrap>' + r.suplrs[k]['name'] + ' ' + future + '</td><td align="center">' + r.suplrs[k]['sc'] + '</td><td align="center" nowrap>' + r.suplrs[k]['_price'] + '</td><td align="center">' + r.suplrs[k]['dateUpdate'] + '</td>');

                                (function(cat_id, suplr_id, price)
                                {
                                    $('<button>Выбрать</button>')
                                        .appendTo($td.find('td:first'))
                                        .button()
                                        .click(function ()
                                        {
                                            var $tr = $('#items tr[cat_id=' + cat_id + ']');
                                            $tr.find('.ipprice').val(price).trigger('change');
                                            if (isDefined(setup.adminCfg.reservation)) {
                                                $tr.find('.isuplrId').val(suplr_id).trigger('chosen:updated').trigger('change');
                                            }
                                            $w.dialog('close');
                                        });
                                })(cat_id, r.suplrs[k]['suplr_id'], r.suplrs[k]['price']);

                            }

                            $('.future3').tooltip();

                            if (!i) $w.html("<p>нет поставщиков в нашей базе.</p>");

                        } else {
                            $w.dialog('close');
                            err(r.fres_msg);
                        }
                    }
                });
            }
        });
}


function items_reserveNum_change(e)
{
    var $v = $(e.target).val();
    var item_id = $(e.target).parents('tr').attr('item_id');
    $.ajax({
        data: {
            act: 'item',
            name: 'af[' + setup.adminCfg.reservation.DBF_reserveNum + ']',
            pk: item_id,
            value: $v,
            order_id: window.setup.order_id
        },
        success: function (r)
        {
            if (r.fres) {
                $(e.target).val(r.newVal)
            } else err(r.fres_msg);
        }
    })
}

function items_reserveDate_bind()
{
    $("#items .ireserveDate")
        .mask("99-99-9999")
        .datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true
        })
        .unbind('change')
        .change(function (e)
        {
            var $v = $(e.target).val();
            var item_id = $(e.target).parents('tr').attr('item_id');
            $.ajax({
                data: {
                    act: 'item',
                    name: 'af[' + setup.adminCfg.reservation.DBF_reserveDate + ']',
                    pk: item_id,
                    value: $v,
                    order_id: window.setup.order_id
                },
                success: function (r)
                {
                    if (r.fres) {
                        $(e.target).val(r.newVal)
                    } else err(r.fres_msg);
                }
            })
        });
}

function items_suplrId_bind()
{
    $('#items .isuplrId').each(function ()
    {
        $(this)
            .chosen({
                disable_search_threshold: 15
            })
            .unbind('change')
            .change(function (e)
            {
                var $v = $(e.target).val();
                var item_id = $(e.target).parents('tr').attr('item_id');
                $.ajax({
                    data: {
                        act: 'item',
                        name: 'af[' + setup.adminCfg.reservation.DBF_suplrId + ']',
                        pk: item_id,
                        value: $v,
                        order_id: window.setup.order_id
                    },
                    success: function (r)
                    {
                        if (!r.fres) {
                            $(e.target).val(r.prevVal).trigger('chosen:updated');
                            err(r.fres_msg);
                        }
                    }
                })
            });
    })
}

function items_postNew(e)
{
    var $frm = $("#item-add");
    if ($frm.find('[name=name]').val() == '') {
        note('Название не может быть пустым');
        return false;
    }
    if (($frm.find('[name=am]').val() * 1) <= 0) {
        note('Укажите количество');
        return false;
    }

    $('#overlayDlg').html('добавляю позицию...').dialog('open');
    $.ajax({
        data: {
            act: 'itemPost',
            frm: $("#item-add-frm").serialize()
        },
        success: function (r)
        {
            if (r.fres) {
                if (r.type == 'item') {
                    $('tbody#items').append($('#newItemRow').render(r.data));
                    items_name_bind();
                    items_reserveDate_bind();
                    items_suplrId_bind();
                } else {
                    $('tbody#dops').append($('#newDopRow').render(r.data));
                    dops_name_bind();
                    window.setup.dopNames.push(r.data.name)
                }
                $('#oItog').html(r.cost);
                if(isDefined(window.setup.adminCfg['purchase']) && isDefined(window.setup.adminCfg['purchase']['dopPPriceEnabled']) && window.setup.adminCfg['purchase']['dopPPriceEnabled']) {
                    $('#pItog').html(r.pcost);
                    checkMarginAlert(r._margin, r.margin);
                }
                $('#item-add-frm').get(0).reset();
            } else {
                err(r.fres_msg);
            }
        },
        complete: function ()
        {
            $('#overlayDlg').dialog('close');
        }
    });

    return false;
}

function deliveryCost_change(e)
{
    var $v = $(e.target).val();
    if (($v * 1) < 0) {
        note('Неверная стоимость доставки');
        return false;
    }

    $.ajax({
        data: {
            act: 'dataEdit',
            field: 'delivery_cost',
            newVal: $v,
            order_id: window.setup.order_id
        },
        success: function (r)
        {
            if (r.fres) {
                if (r.fres_msg != '') note(r.fres_msg);
                window.setup = array_merge(window.setup, r.setup);
                $('#oItog').html(r.cost);
                $(e.target).val(r.newVal);
                if(isDefined(window.setup.adminCfg['purchase']) && isDefined(window.setup.adminCfg['purchase']['dopPPriceEnabled']) && window.setup.adminCfg['purchase']['dopPPriceEnabled']) {
                    checkMarginAlert(r._margin, r.margin);
                }
            } else {
                if (r.fres_msg != '') err(r.fres_msg); else note('Неверное начение. Не записано и восстановлено предыдущее.');
                $(e.target).val(r.prevVal);
            }
        }
    })
}

function discount_change(e)
{
    $.ajax({
        data: {
            act: 'dataEdit',
            field: 'discount',
            newVal: $(e.target).val(),
            order_id: window.setup.order_id
        },
        success: function (r)
        {
            if (r.fres) {
                if (r.fres_msg != '') note(r.fres_msg);
                window.setup = array_merge(window.setup, r.setup);
                $('#oItog').html(r.cost);
                $(e.target).val(r.newVal);
                if(isDefined(window.setup.adminCfg['purchase']) && isDefined(window.setup.adminCfg['purchase']['dopPPriceEnabled']) && window.setup.adminCfg['purchase']['dopPPriceEnabled']) {
                    checkMarginAlert(r._margin, r.margin);
                }
            } else {
                if (r.fres_msg != '') err(r.fres_msg); else note('Неверное начение. Не записано и восстановлено предыдущее.');
                $(e.target).val(r.prevVal);
            }
        }
    })
}

function driverId_change(e)
{
    $.ajax({
        data: {
            act: 'dataEdit',
            field: 'af[driverId]',
            newVal: $(e.target).val(),
            order_id: window.setup.order_id
        },
        success: function (r)
        {
            if (r.fres) {
                if (r.fres_msg != '') note(r.fres_msg);
                window.setup = array_merge(window.setup, r.setup);
            } else {
                if (r.fres_msg != '') err(r.fres_msg); else note('Неверное начение. Не записано и восстановлено предыдущее.');
                $(e.target).val(r.prevVal);
            }
        }
    })
}

function deliveryDate_change(e)
{
    $.ajax({
        data: {
            act: 'dataEdit',
            field: 'af[deliveryDate]',
            newVal: $(e.target).val(),
            order_id: window.setup.order_id
        },
        success: function (r)
        {
            if (r.fres) {
                if (r.fres_msg != '') note(r.fres_msg);
                window.setup = array_merge(window.setup, r.setup);
            } else {
                if (r.fres_msg != '') err(r.fres_msg); else note('Неверное начение. Не записано и восстановлено предыдущее.');
                $(e.target).val(r.prevVal);
            }
        }
    })

}

function suplrPaymentDate_change(e)
{
    $.ajax({
        data: {
            act: 'dataEdit',
            field: 'af[suplrPaymentDate]',
            newVal: $(e.target).val(),
            order_id: window.setup.order_id
        },
        success: function (r)
        {
            if (r.fres) {
                if (r.fres_msg != '') note(r.fres_msg);
                window.setup = array_merge(window.setup, r.setup);
            } else {
                if (r.fres_msg != '') err(r.fres_msg); else note('Неверное начение. Не записано и восстановлено предыдущее.');
                $(e.target).val(r.prevVal);
            }
        }
    })

}

function billDate_change(e)
{
    $.ajax({
        data: {
            act: 'dataEdit',
            field: 'af[billDate]',
            newVal: $(e.target).val(),
            order_id: window.setup.order_id
        },
        success: function (r)
        {
            if (r.fres) {
                if (r.fres_msg != '') note(r.fres_msg);
                window.setup = array_merge(window.setup, r.setup);
            } else {
                if (r.fres_msg != '') err(r.fres_msg); else note('Неверное начение. Не записано и восстановлено предыдущее.');
                $(e.target).val(r.prevVal);
            }
        }
    })

}


function TTN_change(e)
{
    $.ajax({
        data: {
            act: 'dataEdit',
            field: 'af[TTN]',
            newVal: $(e.target).val(),
            order_id: window.setup.order_id
        },
        success: function (r)
        {
            if (r.fres) {
                if (r.fres_msg != '') note(r.fres_msg);
                window.setup = array_merge(window.setup, r.setup);
            } else {
                if (r.fres_msg != '') err(r.fres_msg); else note('Неверное начение. Не записано и восстановлено предыдущее.');
                $(e.target).val(r.prevVal);
            }
        }
    })

}


function exportEvents()
{
    $('#exportBody').button().click(exportBody);
    $('#exportAttach').button().click(exportAttach);
    $('#exportFile').button().click(exportFile);
}

function exportBody(e)
{
    $('<div id="exportDlg" title="Отправить документ на почту в теле письма"></div>')
        .appendTo('body')
        .dialog({
            autoOpen: true,
            modal: true,
            resizable: true,
            closeOnEscape: true,
            height: 'auto',
            width: 600,
            position: { my: "left top", at: "left+120 center-270", of: e.target},
            close: function ()
            {
                $(this).dialog('destroy').remove();
            },
            buttons: [
                {
                    text: 'Закрыть',
                    click: function ()
                    {
                        $(this).dialog('close');
                    }
                }
            ],
            open: function ()
            {
                var $dlg = $(this);
                var docs = {};
                var allInternalUse = true;
                _.each(window.setup.docCfg['html'], function (v, k)
                {
                    var ok = true;
                    if (isDefined(v.useInCMS) && !v.useInCMS) ok = false;
                    else if (setup.ptypeOn && isDefined(v.ptype) && _.indexOf(v.ptype, window.setup.ptype * 1) == -1) ok = false;
                    else if (setup.methodOn && isDefined(v.method) && _.indexOf(v.method, window.setup.method * 1) == -1) ok = false;
                    else if (typeof v.availabilityJSFoo != 'undefined' && !window[v.availabilityJSFoo]()) ok = false;

                    if (ok) {
                        docs[k] = v;
                        if (!isDefined(v.internalUse) || !v.internalUse) allInternalUse = false;
                    }
                });

                if (_.size(docs)) {


                    $('<fieldset class="ui"><legend>Тема письма</legend><input style="width: 99%" class="ui-corner-all" id="exportMailSubject" placeholder="Оставьте пустым - тема письма будет заполнена автоматически "></fieldset>').appendTo($dlg);

                    var $tbl=$('<fieldset class="ui"><legend>Выберите документ</legend><table id="docs" style="width: 99%"></table></fieldset>').appendTo($dlg);
                    _.each(docs, function(v,k)
                    {
                        $('<tr format="html" doc="'+k+'"><td><button class="select">выбрать</button></td><td><img src="/cms/img/docs/pdf-24.png" height="24"></td><td width="100%">&nbsp;'+ v.name+'</td></tr>').appendTo($tbl.find('table'));
                    });

                    $dlg.find('.select').button().click(function()
                    {
                        $tbl.find('tr').removeClass('selected').css({background: 'white'});
                        var $tr=$(this).parents('tr');
                        $tr.addClass('selected').css({background: '#66CC99'});
                    });

                    var $buttonSet=$('<div style="margin-top: 15px;"><button id="exportMailMgr">отправить себе на почту</button> </div> ').appendTo($dlg);

                    $buttonSet.find('#exportMailMgr').button().click(exportMailBody);

                    if($('#ctrls #driverId').val()*1)
                        $('<button id="exportMailDriver">отправить водителю</button>').appendTo($buttonSet).button().click(exportMailBody);

                    if(!allInternalUse && $('.client-data [name=email]').val().length>5)
                        $('<button id="exportMailClient">отправить КЛИЕНТУ</button>').appendTo($buttonSet).button().click(exportMailBody);

                    $buttonSet.after('<small>* при отправке клиенту ваша подпись будет автоматически добавлена в конец письма</small>');

                } else
                    $('<p>Нет доступных документов</p>');
            }
        });

}

function exportMailBody(e)
{
    if(e.target.tagName=='SPAN') var $el=$(e.target).parent(); else var $el=$(e.target);

    var docs=[];
    var canSend=true;
    var $tr=$('#exportDlg #docs tr.selected:first');
    if($tr.length){
        var doc=$tr.attr('doc');
        var format=$tr.attr('format');
        if($el.attr('id')=='exportMailClient' && isDefined(window.setup.docCfg[format][doc]['internalUse']) && window.setup.docCfg[format][doc]['internalUse']) {
            note('<b>ОШИБКА!</b><br>Документ <b>&quot;'+window.setup.docCfg[format][doc]['name']+'&quot</b> не предназначен для отправки клиенту');
            return false;
        }
        var subj=$('#exportDlg #exportMailSubject').val();

        $('#overlayDlg').html('отправляю...').dialog('open');
        $.ajax({
            data:{
                act: 'exportMailBody',
                order_id: window.setup.order_id,
                mode: $el.attr('id'),
                doc: format+'.'+doc,
                subject: subj
            },
            success: function(r)
            {
                if(r.fres){
                    note('Отправлено ОК');
                    //$('#exportDlg').dialog('close');
                }else err(r.fres_msg);
            },
            complete: function()
            {
                $('#overlayDlg').dialog('close');
            }
        });
    }
    return false;
}

function exportAttach(e)
{
    $('<div id="exportDlg" title="Отправить документы на почту вложением"></div>')
        .appendTo('body')
        .dialog({
            autoOpen: true,
            modal: true,
            resizable: true,
            closeOnEscape: true,
            height: 'auto',
            width: 600,
            position: { my: "left top", at: "left+120 center-370", of: e.target},
            close: function ()
            {
                $(this).dialog('destroy').remove();
            },
            buttons: [
                {
                    text: 'Закрыть',
                    click: function ()
                    {
                        $(this).dialog('close');
                    }
                }
            ],
            open: function ()
            {
                var $dlg = $(this);
                var docs = {};
                var allInternalUse = false;
                _.each(window.setup.docCfg['pdf'], function (v, k)
                {
                    var ok = true;
                    if (isDefined(v.useInCMS) && !v.useInCMS) ok = false;
                    else if (setup.ptypeOn && isDefined(v.ptype) && _.indexOf(v.ptype, window.setup.ptype * 1) == -1) ok = false;
                    else if (setup.methodOn && isDefined(v.method) && _.indexOf(v.method, window.setup.method * 1) == -1) ok = false;
                    else if (typeof v.availabilityJSFoo != 'undefined' && !window[v.availabilityJSFoo]()) ok = false;

                    if (ok) {
                        docs[k] = v;
                        if (isDefined(v.internalUse) && !v.internalUse) allInternalUse = false;
                    }
                });

                if (_.size(docs)) {


                    var $tt=$('<fieldset class="ui"><legend>Тема и текст письма</legend><input style="width: 99%" class="ui-corner-all" id="exportMailSubject"><textarea id="exportMailBody" class="ui-corner-all" style="width: 99%; height: 100px; margin-top: 10px">'+"\n\n\n"+window.UDATA.clientMailSign+'</textarea></fieldset>').appendTo($dlg);

                    $tt.find('#exportMailSubject').focus();

                    var $tbl=$('<fieldset class="ui"><legend>Прикрепить документы к письму</legend><table id="docs" style="width: 99%"></table></fieldset>').appendTo($dlg);
                    _.each(docs, function(v,k)
                    {
                        $('<tr format="pdf" doc="'+k+'"><td><button class="select">выбрать</button></td><td><img src="/cms/img/docs/pdf-24.png" height="24"></td><td width="100%">&nbsp;'+ v.name+'</td></tr>').appendTo($tbl.find('table'));
                    });

                    $dlg.find('.select').button().click(function()
                    {
                        var $tr=$(this).parents('tr');
                        if($tr.hasClass('selected')) $tr.removeClass('selected').css({background: 'white'});
                        else $tr.addClass('selected').css({background: '#66CC99'});
                    });

                    var $buttonSet=$('<div style="margin-top: 15px;"><button id="exportMailMgr">отправить себе на почту</button> </div> ').appendTo($dlg);

                    $buttonSet.find('#exportMailMgr').button().click(exportMailWithAttachments);

                    if($('#ctrls #driverId').val()*1)
                        $('<button id="exportMailDriver">отправить Водителю</button>').appendTo($buttonSet).button().click(exportMailWithAttachments);

                    if(!allInternalUse && $('.client-data [name=email]').val().length>5)
                        $('<button id="exportMailClient">отправить КЛИЕНТУ</button>').appendTo($buttonSet).button().click(exportMailWithAttachments);

                } else
                    $('<p>Нет доступных документов</p>');
            }
        });

}

function exportMailWithAttachments(e)
{
    if(e.target.tagName=='SPAN') var $el=$(e.target).parent(); else var $el=$(e.target);

    var docs=[];
    var canSend=true;
    $('#exportDlg #docs tr').each(function()
    {
        if($(this).hasClass('selected')){
            var doc=$(this).attr('doc');
            var format=$(this).attr('format');
            if($el.attr('id')=='exportMailClient' && isDefined(window.setup.docCfg[format][doc]['internalUse']) && window.setup.docCfg[format][doc]['internalUse']) {
                note('<b>ОШИБКА!</b><br>Документ <b>&quot;'+window.setup.docCfg[format][doc]['name']+'&quot</b> не предназначен для отправки клиенту');
                canSend=false;
            }else docs.push(format+'.'+doc);

        }
    });

    if(!docs.length) return;

    if(!canSend)  return false;

    var subj=$('#exportDlg #exportMailSubject').val();

    if(subj.length<3 && confirm('Не задана тема письма. Отправить без темы?') || subj.length>=3){

        $('#overlayDlg').html('отправляю...').dialog('open');
        $.ajax({
            data:{
                act: 'exportMailMultiple',
                order_id: window.setup.order_id,
                mode: $el.attr('id'),
                'docs': docs,
                subject: subj,
                body: $('#exportDlg #exportMailBody').val()
            },
            success: function(r)
            {
                if(r.fres){
                    note('Отправлено ОК');
                    //$('#exportDlg').dialog('close');
                }else err(r.fres_msg);
            },
            complete: function()
            {
                $('#overlayDlg').dialog('close');
            }
        });
    }

    return false;
}

function exportFile(e)
{
    $('<div id="exportDlg" title="Открыть / скачать документ"></div>')
        .appendTo('body')
        .dialog({
            autoOpen: true,
            modal: true,
            resizable: true,
            closeOnEscape: true,
            height: 'auto',
            width: 500,
            position: { my: "left top", at: "left+120 center-270", of: e.target},
            close: function ()
            {
                $(this).dialog('destroy').remove();
            },
            buttons: [
                {
                    text: 'Закрыть',
                    click: function ()
                    {
                        $(this).dialog('close');
                    }
                }
            ],
            open: function ()
            {
                var $dlg = $(this);
                var docs = {};
                _.each(window.setup.docCfg['pdf'], function (v, k)
                {
                    var ok = true;
                    if (isDefined(v.useInCMS) && !v.useInCMS) ok = false;
                    else if (setup.ptypeOn && isDefined(v.ptype) && _.indexOf(v.ptype, window.setup.ptype * 1) == -1) ok = false;
                    else if (setup.methodOn && isDefined(v.method) && _.indexOf(v.method, window.setup.method * 1) == -1) ok = false;
                    else if (typeof v.availabilityJSFoo != 'undefined' && !window[v.availabilityJSFoo]()) ok = false;

                    if (ok) docs[k] = v;
                });

                if (_.size(docs)) {
                    var $tbl=$('<table></table>').appendTo($dlg);
                    _.each(docs, function(v,k)
                    {
                        $('<tr><td><button class="open" format="pdf" doc="'+k+'">открыть</button><button class="dl" format="pdf" doc="'+k+'">скачать</button></td><td><img src="/cms/img/docs/pdf-24.png" height="24"></td><td>&nbsp;'+ v.name+'</td></tr>').appendTo($tbl);
                    });

                    $dlg.find('.open').button().click(exportOpenDoc);
                    $dlg.find('.dl').button().click(exportDownloadDoc);

                } else
                    $('<p>Нет документов для выгрузки</p>');
            }
        });

}

/*
 availabilityJSFoo  для счетов
 */
function exportBillCheck()
{
    if($('#billDate').val()=='00-00-0000') return false; else return true;
}

/*
 availabilityJSFoo для договоров с физиками
 */
function exportContractCheck()
{
    if($('#billDate').val()=='00-00-0000') return false; else return true;
}

/*
 availabilityJSFoo для договоров с юриками
 */
function exportUrContractCheck()
{
    if($('#billDate').val()=='00-00-0000') {
        return false;
    } else if($('[name="af[directorGenitive]"]').val()=='') {
        return false;
    }
        return true;
}

function exportOpenDoc(e)
{
    if(e.target.tagName=='SPAN') var $el=$(e.target).parent(); else var $el=$(e.target);
    var doc=$el.attr('format')+'.'+$el.attr('doc');
    window.open('/cms/ext/ordersDoc.php?do='+doc+'&output=&orderId='+window.setup.order_id, '_blank');
}

function exportDownloadDoc(e)
{
    if(e.target.tagName=='SPAN') var $el=$(e.target).parent(); else var $el=$(e.target);
    var doc=$el.attr('format')+'.'+$el.attr('doc');
    window.open('/cms/ext/ordersDoc.php?do='+doc+'&output=file&orderId='+window.setup.order_id, '_self');
}

function confirmNewOrder(e)
{
    $('#overlayDlg').html('Сохраняю заказ...').dialog('open');
    $.ajax({
        data: {
            act: 'confirmNewOrder',
            order_id: window.setup.order_id
        },
        success: function (r)
        {
            if(r.fres){
                window.setup.LD=0;
                $('.back').show('fast');
                if(window.opener) $('.close-win').show();
                $('.confirmNewOrder').hide('fast');
                $('.cancelNewOrder').hide('fast');
                note('Заказ сохранен.')
            } else{
                err(r.fres_msg);
            }
        },
        complete: function()
        {
            $('#overlayDlg').dialog('close');
        }
    });

    return false;


}

function cancelNewOrder(e)
{
    $('#overlayDlg').html('Отменяю заказ...').dialog('open');
    $.ajax({
        data: {
            act: 'cancelNewOrder',
            order_id: window.setup.order_id
        },
        success: function (r)
        {
            if(r.fres){
                window.setup.editable=false;
                window.setup.LD=1;
                refreshInterface();
                if(window.setup.ref)
                    location.href=urldecode(window.setup.ref);
                else {
                    $('.edit_area').fadeOut('fast');
                    if(window.opener) $('.close-win').show();
                }
            } else{
                err(r.fres_msg);
            }
        },
        complete: function()
        {
            $('#overlayDlg').dialog('close');
        }
    });

    return false;
}