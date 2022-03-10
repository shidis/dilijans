if(typeof parent.framesView != 'undefined'){
    var frameSet = window.frameElement.parentNode;
    frameSet.cols=parent.framesModel[parent.framesView].cols;
    frameSet.rows=parent.framesModel[parent.framesView].rows;
}

$(document).ready(function(){

    $.ajaxSetup({
        type:'POST',
        cache:false,
        dataType: 'json',
        url: '../be/orders.php',
        error: Err
    });

    window.loader=$('body').cloader();
    $(document)
        .ajaxStart(function() {
            loader.cloader('show');
        })
        .ajaxStop(function() {
            loader.cloader('hide');
        });

    $('body').prepend('<div id="overlayDlg" title="Подождите..."></div>');
    $('#overlayDlg').dialog({
        autoOpen:false,
        modal:true,
        resizable:true,
        closeOnEscape:false,
        height: 90,
        width:350
    });

    $('.workspace').tooltip({
        track: true
    });

    $('.state0go').click(function(e)
    {
        var order_id=$(this).parents('tr').attr('order_id');
        var method=$(this).parents('tr').attr('method');

        $('#overlayDlg').html('...выполняется операция').dialog('open');

        $.ajax({
            data:{
                act: 'changeState',
                newStateId: window.setup.oStatesGrouped[method][0]['next'],
                order_id: order_id
            },
            success: function(r)
            {
                if(r.fres){
                    if(r.fres_msg!='') $('#overlayDlg').html(r.fres_msg);
                    loader.cloader('show');
                    var $olink=$('.orders-table tr[order_id='+order_id+'] .olink');
                    location.href=$olink.attr('href')+'&ref='+setup.ref;
                } else {
                    $('#overlayDlg').dialog('close');
                    err(r.fres_msg);
                }
            }
        });
        return false;
    });

    $('.chState').each(function(){
        $(this)
            .css({'width':($(this).outerWidth()*1+30)+'px'})
            .chosen({
                disable_search_threshold: 15
            })
            .change(function(e)
            {
                var newStateId=$(this).val();
                var method=$(this).parents('tr').attr('method');
                var order_id=$(this).parents('tr').attr('order_id');
                var $el=$(this);

                $.ajax({
                    data:{
                        act: 'changeState',
                        newStateId: newStateId,
                        order_id: order_id
                    },
                    success: function(r)
                    {
                        if(r.fres){

                            if(r.reload){
                                if(r.fres_msg!='') alert(r.fres_msg);
                                window.loader.cloader('show');
                                location.reload();
                            }else{
                                if(r.fres_msg!='') note(r.fres_msg);
                                $el.html('');
                                for(var k in window.setup.oStatesGrouped[method]){
                                    if(newStateId==k)
                                        $el.append('<option value="'+k+'" selected>'+window.setup.oStatesGrouped[method][k]['label']+'</option>');
                                    else{
                                        if(_.indexOf(window.setup.oStatesGrouped[method][k]['allowFrom'],newStateId*1)!=-1)
                                            $el.append('<option value="'+k+'">'+window.setup.oStatesGrouped[method][k]['label']+'</option>');
                                    }
                                }
                                $el.trigger('chosen:updated');

                                var $tds=$('.orders-table tr[order_id='+order_id+']').find('td');
                                $tds.css(window.setup.oStatesGrouped[method][newStateId]['bgStyle']);
                                $tds.css(window.setup.oStatesGrouped[method][newStateId]['textStyle']);
                            }

                        } else {
                            err(r.fres_msg);
                            $(e.target).val(r.prevState).trigger('chosen:updated');
                        }
                    }
                });
            });
    });

    if(typeof pingOrders != 'undefined' && pingOrders)
        var pingator=setInterval(function()
        {
            $.ajax({
                global: false,
                data:{
                    act: 'ping',
                    dt: checkDT
                },
                success: function(r)
                {
                    if(r.fres){
                        if(r.newOrds) {
                            note('Поступил новый заказ <button onclick="location.reload()">обновить страницу</button>','stick');
                            checkDT=r.lastOrderDT;
                            logit(checkDT);
                        }

                    }
                },
                error: efoo
            });
        }, window.pingBotInt*1000);

    $('.orders-table tr').each(function(){
        var $this=$(this);
        var state_id=$this.attr('state_id');
        var method=$this.attr('method');
        if(typeof state_id != 'undefined' && isDefined(window.setup.oStatesGrouped[method][state_id])){
            var $el=$this.find('td');
            $el.css(window.setup.oStatesGrouped[method][state_id]['bgStyle']);
            $el.css(window.setup.oStatesGrouped[method][state_id]['textStyle']);
        }
    });

    $('.orders-table th').addClass('ui-widget-header');
    $('input, select, textarea').addClass('ui-corner-all');

    exportInit();

    $('.reloadPage').button().click(function()
    {
        loader.cloader('show');
        location.href=location.protocol+'//'+location.hostname+(location.port?":"+location.port:"")+location.pathname+(location.search?location.search:"");
    });

    $('.switchSB')
        .button({
            icons: { primary: "ui-icon-transfer-e-w", secondary: null },
            text: false
        })
        .css({width:'30px', height:'30px', margin: '0 10px'})
        .click(function(){
            if(typeof parent.framesView != 'undefined'){
                var frameSet = window.frameElement.parentNode;
                if(frameSet.cols == parent.framesModel.def.cols && frameSet.rows == parent.framesModel.def.rows){
                    frameSet.cols=parent.framesModel.noSidebar.cols;
                    frameSet.rows=parent.framesModel.noSidebar.rows;
                    parent.framesView='noSidebar';
                }else{
                    frameSet.cols=parent.framesModel.def.cols;
                    frameSet.rows=parent.framesModel.def.rows;
                    parent.framesView='def';
                }
            }
            return false;
        });

    $('.orders-table .orderDel').click(orderDel);

    $('.newOrder').button().click(newOrder);

    $('.orders-table .infosw').each(function()
    {
        var order_id=$(this).closest('tr').attr('order_id');
        var $tbody=$('.orders-table tbody[oid='+order_id+']');
        var $td=$tbody.find('td');

        if($tbody.length){
            $td.html('<span class="i ui-icon ui-icon-arrowreturnthick-1-e"></span>'+$td.html());
            $(this)
                .click(function()
                {
                    if($tbody.hasClass('shown'))
                        $tbody.hide().removeClass('shown');
                    else{
                        $tbody.show().addClass('shown');

                        if($tbody.attr('slogNum')==0 && $tbody.attr('calls')!='1') return;

                        //подгрузка slog
                        if($tbody.attr('slogLoaded')=='1') return;

                        new function($tbody)
                        {
                            $tbody.attr('slogLoaded','1');
                            if($td.html()!=''){
                                $td.html('<p>'+$td.html()+'</p>');
                            }
                            var $slog=$('<p><img src="/assets/images/ax/siteheart.gif"></p>').appendTo($td);
                            $.ajax({
                                data: {
                                    act: 'slogByOrder',
                                    order_id: order_id
                                },
                                success: function(r)
                                {
                                    $slog.remove();
                                    if (!r.fres)
                                        $td.append(r.fres_msg);
                                    else {
                                        $td.append(r.slogHTML);

                                        if (typeof r.callLog != 'undefined'){
                                            if(_.size(r.callLog.data)) {

                                                var $el;
                                                $td.html('<div class="column1">' + $td.html() + '</div><div class="column2"></div>');
                                                $el = $td.find('.column2');

                                                var $callLog = $(
                                                    '<div class="calls">' +
                                                    '<fieldset>' +
                                                    '<legend class="ui">История звонков</legend>' +
                                                    '<table><tr>' +
                                                    '<th title="кол-во минут с момента оформления заказа и до телефонного контакта с клиентом">от созд.</th>' +
                                                    '<th>время звонка</th>' +
                                                    '<th>тип</th>' +
                                                    '<th>продолжит.</th>' +
                                                    '<th>с номера</th>' +
                                                    '<th>на номер</th>' +
                                                    '</tr></table>' +
                                                    '</fieldset>' +
                                                    '</div>'
                                                ).prependTo($el);

                                                $callLog = $callLog.find('table');

                                                for (var k in r.callLog['data']) {
                                                    $callLog.append(
                                                        '<tr>' +
                                                        '<td style="text-align: center">' + r.callLog['data'][k].deltaDTAddMinutes + ' мин</td>' +
                                                        '<td>' + r.callLog['data'][k].dt + '</td>' +
                                                        '<td>' + r.callLog['data'][k]._type + '</td>' +
                                                        '<td style="text-align: center">' + r.callLog['data'][k].duration + ' сек</td>' +
                                                        '<td>' + r.callLog['data'][k].source + '</td>' +
                                                        '<td>' + r.callLog['data'][k].dest + '</td>' +
                                                        '</tr>'
                                                    );
                                                }
                                            }else {
                                                $td.append('<p><em>телефонные вызовы по заказу не найдены</em></p>');
                                            }
                                        }
                                    }
                                }
                            });
                        }($tbody);

                    }
                });


            if($tbody.attr('slogNum')>0 || $tbody.attr('info')=='1'){
                $(this).css({
                    'cursor':'pointer',
                    'border-left':'2px solid #CC3300'
                });
            }else{
                $(this).css({
                    'cursor':'pointer'
                });
            }
        }

    });

    $('.wscroll').click(function (e)
    {
        location.href=$(this).attr('href')+'&ref='+setup.ref+urlencode('#'+scrollPage.getHash(e));
        return false;
    });

    $('.settings a').click(settingsClick);

    if(typeof window.setup.adminCfg['purchase']['suplrHinting']['minSuplrSC'] != 'undefined')
        $('.suphint').tooltip({
            content: 'Рекомендуемые поставщики для товаров в заказе. Принцип: минимальная закуп. цена + наличие на складе больше '+window.setup.adminCfg['purchase']['suplrHinting']['minSuplrSC']+' и больше заказанного кол-ва',
            track: true
        });

});

function orderDel(e)
{
    if (window.confirm('Уверены?')){
        var id=$(e.target).closest('tr').attr('order_id')*1;
        $.ajax({
            data: {
                act: 'orderDel',
                order_id: id
            },
            success: function(r)
            {
                if(r.fres){
                    location.reload();
                }else err(r.fres_msg);
            }
        });
    }
    return false;
}

function newOrder()
{
    $('#overlayDlg').html('...создаю заказ...').dialog('open');
    $.ajax({
        data: {
            act: 'newOrder'
        },
        success: function(r)
        {
            if(r.fres && r.order_id){
                location.href="order_edit.php?order_id="+ r.order_id+"&ref="+setup.ref;
            }else {
                $('#overlayDlg').dialog('close');
                err(r.fres_msg);
            }
        }
    });
    return false;
}

function exportInit()
{
    $('.exportCSV').click(function(e)
    {
        $('<div id="exportCSVdlg" title="Экспорт заказов"></div>')
            .appendTo('body')
            .dialog({
                autoOpen: true,
                modal: false,
                resizable: true,
                closeOnEscape: true,
                height: 'auto',
                width: 300,
                position: { my: "right top", at: "left bottom", of: e.target},
                close: function ()
                {
                    $(this).dialog('destroy').remove();
                },
                open: function()
                {
                    $('<button>Экспорт заказов (CSV)</button>')
                        .appendTo(this)
                        .button()
                        .click(function()
                        {
                            $('#exportCSVdlg').dialog('close');
                            logit(location.protocol+'//'+location.hostname+(location.port?":"+location.port:"")+location.pathname+(location.search?location.search:"")+'&output=csv');
                            window.open(location.protocol+'//'+location.hostname+(location.port?":"+location.port:"")+location.pathname+(location.search?location.search:"")+'&output=csv', '_self');
                            return false;
                        });

                    if(window.setup.ordersExportCSVDetail){

                        $('<button>Экспорт заказов+товары (CSV)</button>')
                            .appendTo(this)
                            .button()
                            .click(function()
                            {
                                $('#exportCSVdlg').dialog('close');
                                window.open(location.protocol+'//'+location.hostname+(location.port?":"+location.port:"")+location.pathname+(location.search?location.search:"")+'&output=exportCSVDetail', '_self');
                                return false;
                            });
                    }
                }
            });
    });
}