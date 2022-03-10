

$(document).ready(function ()
{

    $.ajaxSetup({
        type: 'POST',
        cache: false,
        dataType: 'json',
        error: function ()
        {
        },
        url: 'be/home.php'
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

    $('#log_grid').jqGrid({
        datatype: 'json',
        url: 'be/home.php?act=serverLog_list',
        caption: 'Лог ошибок',
        colNames: ['№ строки', 'Время', 'Тип', 'IP', 'Ошибка', 'Referer'],
        colModel: [
            {name: 'line', index: 'line', align: 'center', width: 50, sortable: false},
            {name: 'time', index: 'time', align: 'left', width: 80, sortable: false},
            {name: 'type', index: 'type', align: 'center', width: 50, sortable: false},
            {name: 'ip', index: 'ip', align: 'center', width: 80, sortable: false},
            {name: 'msg', index: 'msg', align: 'left', width: 250, sortable: false},
            {name: 'referer', index: 'referer', align: 'left', width: 250, sortable: false}
        ],
//		rownumbers: true, 
        rowNum: 300,
        mtype: "GET",
        hiddengrid: true,
        hidegrid: true,
        gridview: true,
        scroll: 1,
        viewrecords: true,
        autowidth: true,
        height: 300,
//		width:'100%',
        pager: '#log_pagered',
        loadError: Err,
        loadComplete: loadComplete
    })
        .jqGrid('navGrid', "#log_pagered", {del: false, add: false, edit: false, search: false}, {}, {}, {}, {multipleSearch: true});
    /*	.jqGrid('navButtonAdd','#log_pagered',{
     caption:"Очистить лог",
     onClickButton:function(){
     $.ajax({
     data: {act:'serverLog_clear'},
     dataType: 'json',
     error: Err,
     type:'POST',
     success: function(r){
     if(r.fres){
     $('#log_grid').trigger('reloadGrid');
     } else note(r.fres_msg,'error');
     }
     });
     }
     });*/


    if ($('#exlibDebugSw').length) {
        $('#exlibDebugSw').html(loading2);
        $.ajax({
            data: {act: 'exLibDebugGet'},
            success: function (r)
            {
                if (r.fres) {
                    if (r.debug) $('#exlibDebugSw span').text('ExLib Debug Mode now is ON'); else $('#exlibDebugSw span').text('ExLib Debug Mode now is OFF');
                } else note(r.fres_msg, 'error');
            },
            complete: function()
            {

            }
        });
    }

    $('#exlibDebugSw').button().click(function ()
    {
        $.ajax({
            data: {act: 'exLibDebugSW'},
            success: function (r)
            {
                if (r.fres) {
                    if (r.debug) $('#exlibDebugSw span').text('ExLib Debug Mode now is ON'); else $('#exlibDebugSw span').text('ExLib Debug Mode now is OFF');
                } else note(r.fres_msg, 'error');
            }
        });
    });

    $('#exlibConcatJS').button().click(function ()
    {
        $.ajax({
            data: {act: 'exLibConcatJS'},
            success: function (r)
            {
                if (r.fres) note('OK'); else note(r.fres_msg, 'error');
            }
        });
    });


    $('#exlibConcatCSS').button().click(function ()
    {
        $.ajax({
            data: {act: 'exLibConcatCSS'},
            success: function (r)
            {
                if (r.fres) note('OK'); else note(r.fres_msg, 'error');
            }
        });
    });

    $('#exlibConcatImages').button().click(function ()
    {
        $.ajax({
            data: {act: 'exLibConcatImages'},
            success: function (r)
            {
                if (r.fres) note('OK'); else note(r.fres_msg, 'error');
            }
        });
    });

    if($('#SMSBalance').length){
        $.ajax({
            data: {act: 'SMSBalance'},
            success: function(r)
            {
                if(r.fres){
                    $('<p>Баланс SMS сервиса = <b>'+(
                        isDefined(r.data.balance)
                            ?
                            (
                                r.data.balance<50
                                    ?
                                    ( '<span style="color:red">'+r.data.balance+'</span>')
                                    :
                                    r.data.balance )
                            :
                            ( r.data.statusMsg )
                    )+'</b></p>').appendTo($('#SMSBalance'));
                }
            }
        })
    }

    var calls=new Calls();

    $('.call-graph-open').click(function()
    {
        if(calls.opened){
            calls.close();
        }else{
            calls.open();
        }

        return false;
    });

    $('.djsMissesReset').click(function ()
    {
        $.ajax({
            data: {act: 'djsMissesReset'},
            success: function (r)
            {
                if (r.fres) {
                    note('OK');
                    $('.djsMisses').html('0/0');
                } else note(r.fres_msg, 'error');

            }
        });
    });


    botLogInit();

});

function botLogInit()
{
    if (botLog.enabled) {
        $('#loadBotLog')
            .button()
            .click(function(){
                $('#botLog').slideDown('fast',function(){
                    $('html, body').animate({
                        scrollTop: $("#loadBotLog").offset().top
                    }, 1000);
                });
                $('#botLog #info span').append('<img class="loader" src="/assets/images/ax/1.gif">');
                $.ajax({
                    data: {act: 'botLog_init'},
                    success: function (r)
                    {
                        if (r.fres) {
                            botLog.se = r.se;
                            botLog.botNames = r.botNames;
                            $('#botLog #info #today span:eq(0)').html(r.todayYA);
                            $('#botLog #info #today span:eq(1)').html(r.todayG);
                            $('#botLog #info #yesteday span:eq(0)').html(r.yestedayYA);
                            $('#botLog #info #yesteday span:eq(1)').html(r.yestedayG);
                            $('#botLog #info #week span:eq(0)').html(r.weekYA);
                            $('#botLog #info #week span:eq(1)').html(r.weekG);
                            $('#botLog #info #month span:eq(0)').html(r.monthYA);
                            $('#botLog #info #month span:eq(1)').html(r.monthG);
                            $('#botLog #info #total span:eq(0)').html(r.totalYA);
                            $('#botLog #info #total span:eq(1)').html(r.totalG);

                            botLog.grid();

                        } else note(r.fres_msg, 'error');
                    }
                });
            });
    }

}

botLog = {};

botLog.grid = function ()
{
    $('#botlogGrid').jqGrid({
        datatype: 'json',
        url: 'be/home.php?act=botLog_list',
        caption: 'Лог посещений поисковых роботов',
        colNames: ['Поисковая система', 'Имя бота', 'Время посещения', 'UserAgent', 'Урл сайта', 'IP бота'],
        colModel: function ()
        {
            var a = [];
            var b = {};
            for (var k in botLog.se) {
                b[k] = botLog.se[k];
            }

            a.push({name: 'se', index: 'se', align: 'center', width: 50, sortable: false, search: true, stype: 'select', searchoptions: {value: b, defaultValue: ''}});
            var b = {};
            for (var k in botLog.botNames) {
                b[k] = botLog.botNames[k];
            }
            a.push({name: 'botName', index: 'botName', align: 'center', width: 50, sortable: true, stype: 'select', searchoptions: {value: b, defaultValue: ''}});

            a.push({name: 'dt_visited', index: 'dt_visited', align: 'center', width: 80, sortable: true});
            a.push({name: 'userAgent', index: 'userAgent', align: 'left', width: 250, sortable: true});
            a.push({name: 'url', index: 'url', align: 'left', width: 250, sortable: true});
            a.push({name: 'botIP', index: 'botIP', align: 'center', width: 80, sortable: true});

            return a;
        }(),
//		rownumbers: true,
        sortname: 'dt_visited',
        sortorder: "desc",
        rowNum: 300,
        mtype: "GET",
        hiddengrid: true,
        hidegrid: true,
        gridview: true,
        scroll: 1,
        viewrecords: true,
        autowidth: true,
        height: 700,
//		width:'100%',
        pager: '#botlogPager',
        loadError: Err,
        loadComplete: loadComplete
    })
        .jqGrid('navGrid', "#botlogPager", {del: false, add: false, edit: false, search: false}, {}, {}, {}, {multipleSearch: true})
        .jqGrid('filterToolbar', {
            stringResult: true,
            searchOnEnter: false
        });
};

function Calls()
{
    var self=this;
    this.$ww=$('.call-graph');
    this.loaded=false;
    this.opened=false;
};

Calls.prototype.open=function()
{
    this.$ww
        .html('<div style="margin: 50px; text-align:center"><img src="/assets/images/ax/x140.gif"></div>')
        .slideDown(400);

    this.opened=true;

    var self=this;

    this.loadScripts(function()
    {
        Highcharts.setOptions({
            global: {
                useUTC: false
            }
        });

        $.ajax({
            url: 'be/home.php?act=callLog_GraphData',
            data: {},
            success: function (r)
            {
                if (r.fres == false) {
                    logit('callLog_GraphData error: ' + r.fres_msg);
                    return;
                }
                if (typeof r.firstDateTSM == 'udefined') {
                    logit('callLog_GraphData нет данных');
                    return;
                }

                self.$ww.html('');

                self.$ww.highcharts({

                    chart: {
                        zoomType: 'x',
                        height: 400
                    },
                    title: {
                        text: 'Принятые, пропущенные и исходящие звонки максимум за 2 года, масштаб - 1 день'
                    },
                    subtitle: {
                        text: document.ontouchstart === undefined ?
                            'Выделите область мышью для уменьшения масштаба' :
                            'Pinch the chart to zoom in'
                    },
                    xAxis: {

                        type: 'datetime',
                        dateTimeLabelFormats: {
                            day: '%e.%m.%Y'
                        },
                        minRange: 7 * 24 * 3600000 // 14 days в микросекундах
                        //categories: r.data.dates

                    },
                    yAxis: {
                        title: {
                            text: 'Кол-во звонков'
                        },
                        stackLabels: {
                            enabled: true,
                            style: {
                                fontWeight: 'bold',
                                color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                            }
                        }
                    },
                    legend: {
                        enabled: true
                    },
                    plotOptions: {
                        areaspline: {
                            fillOpacity: 0.5
                        },
                        column: {
                            stacking: 'normal',
                            dataLabels: {
                                enabled: true,
                                color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
                                style: {
                                    textShadow: '0 0 3px black, 0 0 3px black'
                                }
                            }
                        },
                        area: {
                            fillColor: {
                                linearGradient: {x1: 0, y1: 0, x2: 0, y2: 1},
                                stops: [
                                    [0, Highcharts.getOptions().colors[0]],
                                    [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                                ]
                            },
                            marker: {
                                radius: 2
                            },
                            lineWidth: 1,
                            states: {
                                hover: {
                                    lineWidth: 1
                                }
                            },
                            threshold: null
                        },
                        spline: {
                            marker: {
                                radius: 2
                            },
                            lineWidth: 1,
                            states: {
                                hover: {
                                    lineWidth: 2
                                }
                            }
                        }
                    },
                    credits: {
                        enabled: false
                    },
                    tooltip: {
                        shared: true,
                        crosshairs: true,
                        valueSuffix: ' вызовов'
                    },
                    colors: ['#7cb5ec', '#999B9A', '#FFE46D', '#7cb5ec', '#90ed7d', '#f7a35c', '#8085e9',
                             '#f15c80', '#e4d354', '#8085e8', '#8d4653', '#91e8e1'],
                    series: [
                        {
                            //type: 'area',
                            //type: 'column',
                            type: 'areaspline',
                            name: 'Принятые',
                            pointInterval: 24 * 3600 * 1000, // 1 день в микросекундах
                            pointStart: r.firstDateTSM,
                            data: r.data.successed
                        },
                        {
                            //type: 'column',
                            //type: 'area',
                            type: 'areaspline',
                            name: 'Пропущенные',
                            pointInterval: 24 * 3600 * 1000, // 1 день в микросекундах
                            pointStart: r.firstDateTSM,
                            data: r.data.missed
                        },
                        {
                            //type: 'column',
                            //type: 'area',
                            type: 'spline',
                            name: 'Исходящие',
                            pointInterval: 24 * 3600 * 1000, // 1 день в микросекундах
                            pointStart: r.firstDateTSM,
                            data: r.data.out
                        }
                    ]
                });
            }
        });
    });
};

Calls.prototype.close=function()
{
    this.opened=false;
    this.$ww.slideUp(400);
};

Calls.prototype.loadScripts=function(callback)
{
    var self=this;
    if(typeof $.fn.highcharts != 'undefined'){
        logit('scripts already loaded');
        callback.call(self);
    }else
        $.getScript('/cms/inc/highcharts4/js/highcharts.js', function()
        {
            logit('highcharts.js loaded');
            $.getScript('/cms/inc/highcharts4/js/modules/data.js', function()
            {
                logit('data.js loaded');
                self.loadedOK();
                callback.call(self);
            });
        });
};

Calls.prototype.loadedOK=function()
{
    this.loaded=true;
};