/**
 * Created by admin on 22.11.2015.
 */

function sclInit()
{
    $('.scl').each(function (e)
    {
        $(this).wrapInner('<a href="#" title="наличие по складам" rel="/ax/scl?cat_id=' + $(this).attr('cat_id') + '" class="scl-tip"></a>');
        $(this).children('a.scl-tip').cluetip({
            tracking: false,
            arrows: true,
            multiple: true,
            cluetipClass: 'jtip',
            clickThrough: false,
            activation: 'click',
            sticky: true,
            closePosition: 'title',    // location of close text for sticky cluetips; can be 'top' or 'bottom' or 'title'
            closeText: 'Закрыть',
            ajaxCache: false,
            width: 580,
            //snapToEdge: true,
            //positionBy: 'topBottom',
            //positionBy: 'fixed',
            positionBy: 'leftBottom',
            //leftOffset: '-500',
            topOffset: 30,
            ajaxSettings: {
                dataType: 'json'
            },
            ajaxProcess: function (r)
            {
                if (r.fres) {
                    if (r.count == 0) return '<p>Нет информации по наличию</p>';

                    var cc = '<table class="scl"><tr><th style="text-align: left" class="mobile-hide">Поставщик</th><th class="mobile-hide">На складе</th><th class="mobile-hide">Цена 1</th><th class="mobile-hide">Цена 2</th><th class="mobile-hide">Цена 3</th><th class="mobile-hide">Доб./Обн.</th></tr>';

                    var future, futureTitle, re, dt, dtAdded, dtUpd, ignored;

                    for (var k in r.scl) {
                        re = /([0-9]{4})-([0-9]{2})-([0-9]{2})/;
                        dtUpd = re.exec(r.scl[k]['dt_upd']);
                        dtAdded = re.exec(r.scl[k]['dt_added']);
                        ignored = parseInt(r.scl[k]['ignored']);
                        dt = dtAdded[3] + '-' + dtAdded[2] + '-' + dtAdded[1] + '<br>' + dtUpd[3] + '-' + dtUpd[2] + '-' + dtUpd[1];

                        future = '';
                        if (typeof r.futureSuplr != 'undefined') {
                            futureTitle = 'Доставки на ближайшие ' + r.futureSuplr.days + " дня с товарами этого поставщика:\n";
                            if (_.size(r.scl[k]['future'])) {
                                for (var fi = 1; fi <= r.futureSuplr.days; fi++) {
                                    if (typeof r.scl[k]['future'][fi] != 'undefined') {
                                        future += '<i class="c1"></i>';
                                        futureTitle += (fi == 1 ? '(завтра ' : ("(" + r.scl[k]['future'][fi]['deliveryDate'])) + ' -> ' + r.scl[k]['future'][fi]['itemsNum'] + " шт.) \n";
                                    } else {
                                        future += '<i class="c0"></i>';
                                    }
                                }
                            }
                            if (future.length) {
                                future = '<br><span class="sclFutureSuplr" title="' + futureTitle + '">' + future + '</span>';
                            }
                        }
                        if(ignored){
                            cc = cc + '<tr style="background: #808080;"><td><b class="only-mobile">Поставщик: </b>&nbsp;' + r.scl[k]['name'] + '</td><td><b class="only-mobile">На складе:</b>&nbsp;' + r.scl[k]['sc'] + ' шт.' + future + '</td><td nowrap><b class="only-mobile">Цена 1:</b>&nbsp;' + r.scl[k]['price1'] + ' руб</td><td nowrap><b class="only-mobile">Цена 2:</b>&nbsp;' + r.scl[k]['price2'] + ' руб</td><td nowrap><b class="only-mobile">Цена 3</b>&nbsp;' + r.scl[k]['price3'] + ' руб</td><td nowrap><b class="only-mobile">Доб./Обн.:</b>&nbsp;' + r.scl[k]['dt_added'] + '<br>' + r.scl[k]['dt_upd'] + '</td></tr>';
                        }
                        else cc = cc + '<tr><td><b class="only-mobile">Поставщик: </b>&nbsp;' + r.scl[k]['name'] + '</td><td><b class="only-mobile">На складе: </b>&nbsp;' + r.scl[k]['sc'] + ' шт.' + future + '</td><td nowrap><b class="only-mobile">Цена 1:</b>&nbsp;' + r.scl[k]['price1'] + ' руб</td><td nowrap><b class="only-mobile">Цена 2:</b>&nbsp;' + r.scl[k]['price2'] + ' руб</td><td nowrap><b class="only-mobile">Цена 3:</b>&nbsp;' + r.scl[k]['price3'] + ' руб</td><td nowrap><b class="only-mobile">Доб./Обн.:</b>&nbsp;' + r.scl[k]['dt_added'] + '<br>' + r.scl[k]['dt_upd'] + '</td></tr>';
                    }
                    cc = cc + '</table>';

                    return cc;
                } else {
                    return 'Ошибка загрузки';
                }
            }
        });
    });
}

$(document).ready(function ()
{
    sclInit();
})