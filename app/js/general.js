$(function ($) {
    if(isDefined(window.JSD)){
        for(var k in JSD){
            $(k).html(Base64.decode(JSD[k]));
        }
    }

    if(isDefined(window.JSDW)){
        for(var k in JSDW){
            $(k).wrap(Base64.decode(JSDW[k]));
        }
    }

    /*
        showNotification({
            message: '<p><b>проверка</b> связи</p>',
            description:'это дескрипшн',
            type: "information"
        });
    */

    $.ajaxSetup({
        type:'POST',
        dataType: 'json',
        cache: false,
        error: Err
    });

    $.prettyLoader({
        theme: 'ttt',
        delay:false,
        loader: '/app/images/prettyLoader/ajax-loader.gif'
    });


    $('input[placeholder]').placeholder();

    // http://qtip2.com/demos

    $('.atip, .ntatip').qtip({
        position: {
            my: 'top left',
            at: 'bottom center'
        },
        style: {
            classes: 'qtip-shadow qtip-rounded'
        },
        content: {
            text: function(event, api) {
                $
                    .ajax({
                        url: $(this).attr('rel'),
                        dataType: 'html'
                    })
                    .done(function(html) {
                        api.set('content.text', html)
                    })
                    .fail(function(xhr, status, error) {
                        api.set('content.text', status + ': ' + error)
                    });

                return 'Загрузка...';
            }
        }
    });

    $('.nttip').qtip({
        position: {
            my: 'top left',
            at: 'bottom center'
        },
        style: {
            classes: 'qtip-shadow qtip-rounded'
        }
    });

    $('.ntatip, .atip').click(function(){
        return false;
    });

    $("a[rel^='zoom']").prettyPhoto({
        deeplinking: false,
        show_title: false,
        social_tools: ''
    });


    $('.qbrands').change(function(){
        if($(this).val()!='') location.href=$(this).val();
    });

    $('.search [type=button]').click(function(){
        $(this).parents('form').submit();
    });

    $('#subscribe').submit(subscribe);

    $('.tsGo').click(function(){
        var $f=$(this).parents('.tsForm');
        var empty=true;
        $f.find('select, [type=checkbox]').each(function(){
            if(this.nodeName == 'SELECT' && $(this).val()!='' || this.nodeName == 'INPUT' && $(this).prop('checked')) empty=false;
        });
        if(!empty) $f.submit(); else return false;
    });

    $('.dsGo').click(function(){

        var $f=$(this).parents('.dsForm');
        var empty=true;
        $f.find('select, [type=checkbox]').each(function(){
            if(this.nodeName == 'SELECT' && $(this).val()!='' || (this.nodeName == 'INPUT' && $(this).prop('checked') && $(this).attr('name') != 'ap')) empty=false;
        });
        if(!empty) $f.submit();
        else
        {
            if($f.find('select[name="sv"]').length > 0) // Подсвечиваем свердловку
            {
                $f.find('select[name="sv"]').parents('.select-01').addClass('error');
            }
            return false;
        }
    });

    $('.tsForm input, .tsForm select').change(tsf1);
    $('.dsForm input, .dsForm select').change(tsf2);

    $('.lfGo').click(function(e){
        $(this).closest('form').submit();
        return false;
    });

    $('#cityId, #rc_cityId').change(function(){
        setCookie('cityId',$(this).val());
    });

    $('#rc-btn').click(function()
    {
        $('#rc-result').html('<img align="center" src="/assets/images/ax/3.gif">&nbsp;&nbsp;&nbsp;Идет расчет...');
        $.ajax({
            url: '/ax/regionDelivery',
            data: {
                f: $('#rc-form').serialize()
            },
            success: function(r){
                if(r.fres!==false){
                    $('#rc-result').html(r.fres);
                }else $('#rc-result').html('Расчет не возможен');
            }
        });

        var cityId=$('#rc_cityId').val();
        if(typeof _gaq !='undefined'){
            _gaq.push(['_trackEvent', 'Interface', 'delveryCostByCity', $('#rc_cityId option[value='+cityId+']').html()]);
        }

        return false;
    });

    if (typeof apUrl != 'undefined' && (apUrl == '/avto-podbor' || apUrl == '/podbor-shin' || apUrl == '/t_filter' || apUrl == '/d_filter')) {
        vids_change();

        $('.tsort > li')
            /*.chosen({
             disable_search_threshold: 10
             })*/
            .click(tsort_click);

        $('.limits')
            .chosen({
                disable_search_threshold: 20
            })
            .change(limits_change);
    }
    else{
        $('.vids .setLentaMode').click(function(){
            setCookie('stype','lenta');
            location.reload();
            return false;
        });

        $('.vids .setBlockMode').click(function(){
            setCookie('stype','block');
            location.reload();
            return false;
        });

        $('.vids .active').click(function(){
            return false;
        });

        $('.tsort > li')
            /*.chosen({
             disable_search_threshold: 10
             })*/
            .click(function()
            {
                var a=location.href.replace(/[&\?]{1}ord=[0-9\-]*/,'').replace(/&?\??page=[0-9]*/,'').replace(/&?\??num=[0-9]*/,'');
                location.href=a+(a.indexOf('?')!=-1?'&':'?')+'ord='+$(this).attr('val');
            });

        $('.limits')
            .chosen({
                disable_search_threshold: 20
            })
            .change(function()
            {
                var a=location.href.replace(/[&\?]{1}num=[0-9\-]*/,'').replace(/&?\??page=[0-9]*/,'');
                location.href=a+(a.indexOf('?')!=-1?'&':'?')+'num='+$(this).val();
            });
    }

    var navDiametrActive=null;
    $('.nav-diametr > a').click(function()
    {
        var r=$(this).attr('r');

        if(typeof r != 'undefined') {
            if(navDiametrActive){
                $('.table-list tbody.rad__'+navDiametrActive).hide();
                navDiametrActive=r;
                $('.table-list tbody.rad__'+navDiametrActive).show(400);
            }else{
                navDiametrActive=r;
                $('.table-list tbody:not(.rad__'+navDiametrActive+')').hide(400);
            }
        }else {
            $('.table-list tbody').show(400);
            navDiametrActive=r;
        }

        $('.nav-diametr a').removeClass('active');
        $(this).addClass('active');

        return false;
    });

    $('.alt-brands > a').click(function()
    {
        var bid=$(this).attr('r');
        $('#alt_brand').val(bid);
        if(typeof bid != 'undefined') {
            $.ajax({
                url: $('#target_url').val(),
                data: {bid: bid},
                dataType: 'html',
                success: function(r){
                    if(r){
                        $('.tab-box.analog .replaceable_content').html(r);
                        if(isDefined(window.adminLogged) && window.adminLogged) {
                            sclInit();
                        }
                        $('.vids .setLentaMode').click(function(){
                            setCookie('stype','lenta');
                            location.reload();
                            return false;
                        });

                        $('.vids .setBlockMode').click(function(){
                            setCookie('stype','block');
                            location.reload();
                            return false;
                        });

                        $('.vids .active').click(function(){
                            return false;
                        });
                        initChartHandlers();
                    }else {
                        console.log(r);
                    }

                }
            });
        }

        $('.alt-brands a').removeClass('active');
        $(this).addClass('active');

        return false;
    });

    $('#altt-bybrand').click(function()
    {
        $(this).closest('form').submit();
    });

    feedbackSubmitInit();

    singleRunInit();

    // Страница доставки и оплаты
    $('.dostavka .dris > a').click(function(e){
        e.preventDefault();
        $('html, body').animate({
            scrollTop: $('a[name="' + $(this).attr('href').replace('#', '') + '"]').offset().top
        }, 1000);
        return false;
    });
    $('.dostavka .sylka > a').click(function(e){
        e.preventDefault();
        if ($('.dostavka .cities_list').is(':visible')){
            $(this).html('Показать список городов <img alt="" src="/app/images/eup/d-strelka.png">');
            $('.dostavka .cities_list').hide('slow');
        }
        else{
            $(this).html('Скрыть список городов <img alt="" src="/app/images/eup/d-strelka-up.png">');
            $('.dostavka .cities_list').show('slow');
        }
        return false;
    });
    $('.dostavka .sylka1.karta > a').click(function(e){
        e.preventDefault();
        if ($('.dostavka .formkarta').is(':visible')){
            $(this).html('Показать реквизиты карты <img alt="" src="/app/images/eup/d-strelka.png">');
            $('.dostavka .formkarta').hide('slow');
        }
        else{
            $(this).html('Скрыть реквизиты карты <img alt="" src="/app/images/eup/d-strelka-up.png">');
            $('.dostavka .formkarta').show('slow');
        }
        return false;
    });
    $('.dostavka .sylka1.dannye > a').click(function(e){
        e.preventDefault();
        if ($('.dostavka .reg_data_wrap').is(':visible')){
            $(this).html('Показать регистрационные данные <img alt="" src="/app/images/eup/d-strelka.png">');
            $('.dostavka .reg_data_wrap').hide('slow');
        }
        else{
            $(this).html('Скрыть регистрационные данные <img alt="" src="/app/images/eup/d-strelka-up.png">');
            $('.dostavka .reg_data_wrap').show('slow');
        }
        return false;
    });
    $('.dostavka label[id="raz"]').click(function(e){
        e.preventDefault();
        if ($('.dostavka .raz_del').is(':visible')){
            $(this).html('Показать таблицу веса и объема шин <img alt="" src="/app/images/eup/d-strelka.png">');
            $('.dostavka .raz_del').hide('slow');
        }
        else{
            $(this).html('Скрыть таблицу веса и объема шин <img alt="" src="/app/images/eup/d-strelka-up.png">');
            $('.dostavka .raz_del').show('slow');
        }
        return false;
    });

    $(".dostavka .ddoki > a").fancybox({
        openEffect    : 'none',
        closeEffect    : 'none',
        closeBtn  : true,
        afterShow: function(){
            $(".fancybox-wrap, .fancybox-skin").css({'overflow':'hidden'});
        }
    });

    $(".tyres-calculator #calculate_button").click(function() {
        calculateTires();
    });
    $(".tyres-calculator #calculateWidth").change(function() {
        $('.t_calc_table').css('display', 'none');
        $(".tyres-calculator #calculateProfile").html('<option value="-1">Профиль</option>').attr('disabled', true).siblings('span').html('Профиль').css('color', '#bbbbbb');
        $(".tyres-calculator #calculateDiameter").html('<option value="-1">Диаметр</option>').attr('disabled', true).siblings('span').html('Диаметр').css('color', '#bbbbbb');
        calculateTires();
    });
    $(".tyres-calculator #calculateProfile").change(function() {
        $(".tyres-calculator #calculateDiameter").html('<option value="-1">Диаметр</option>').attr('disabled', true).siblings('span').html('Диаметр').css('color', '#bbbbbb');
        calculateTires();
    });


    /* Подгрузка товаров при скроллинге */
    /*$(document).scroll(function() {
        if ($('.replaceable_content').length > 0) {
            var $replaceable_content = $('.replaceable_content');
        } else if ($(".search_replaceable_content").length > 0) {
            var $replaceable_content = $(".search_replaceable_content");
        }
        var wHeight = $(window).height();
        if ($replaceable_content.length) {
            scrollReplaceableContent($replaceable_content,wHeight);
        }
    });*/

});   /* END $()  ***************************** */

function scrollReplaceableContent($replaceable_content,wHeight) {
    var scroll = $(window).scrollTop(),
        position = $replaceable_content.offset();

        if($replaceable_content.length > 0) {
            num = $replaceable_content.attr('num');
        } else {
            num = $(".search_replaceable_content").attr('num');
        }

    if ((scroll > (position.top + $replaceable_content.height() - (wHeight / 1.2))) && !lockPage && ($('.replaceable_goods').length < num)) {
        lockPage = true;
        var a=location.href.replace(/[&]{1}num=[0-9\-]*/,'').replace(/\&ajax_tsort=1/i,'').replace(/&?\??page=[0-9]*/,'');
        cur_limit += 12;
        $.ajax({
            url: a+(a.indexOf('?')!=-1?'&':'?')+'num='+ cur_limit + '&ajax_tsort=1',
            dataType: 'html',
            success: function(r){
                if(r){

                    $('.search_replaceable_content, .replaceable_content ').each(function(){
                        this.innerHTML = r;
                    });

                    lockPage = false;

                    $('.tsort > li').click(tsort_click);

                    $('.limits')
                        .chosen({
                            disable_search_threshold: 20
                        })
                        .change(limits_change);
                    vids_change();
                    if(isDefined(window.adminLogged) && window.adminLogged) {
                        sclInit();
                    }
                    initChartHandlers();
                }else {
                    console.log(r);
                }
            }
        });
    }
}

function calculateTires(){
    $.ajax({
        type: 'POST',
        url: '/ax/calculateTyres',
        data: {
            width: $('#calculateWidth').val(),
            profile: $('#calculateProfile').val(),
            diameter: $('#calculateDiameter').val(),
            amount: $('#calculateAmount').val()
        },
        success: function(r){
            if(r.fres){
                $('.t_calc_table').css('display', 'none');
                if (typeof(r.typo) != 'undefined'){
                    $('.t_calc_table').css('display', 'table');
                    $('.t_calc_table').find('.v1').html(r.v1);
                    $('.t_calc_table').find('.m1').html(r.m1);
                    $('.t_calc_table').find('.ma').html(r.ma);
                    $('.t_calc_table').find('.va').html(r.va);
                    $('.t_calc_table').find('.typo').html(r.typo);
                    $('.t_calc_table').find('.am').html(r.am);
                }
                else if (typeof(r.p2) != 'undefined'){
                    var html = '<option value="-1">Профиль</option>';
                    r.p2.forEach(function(elem){
                        html += '<option value="' + parseFloat(elem) + '">' + parseFloat(elem) + '</option>';
                    });
                    $('#calculateProfile').html(html).val("-1").attr('disabled', false).siblings('span').html('Профиль').css('color', '#35393d');
                }
                else if (typeof(r.p3) != 'undefined'){
                    var html = '<option value="-1">Диаметр</option>';
                    r.p3.forEach(function(elem){
                        html += '<option value="' + parseFloat(elem) + '">' + parseFloat(elem) + '</option>';
                    });
                    $('#calculateDiameter').html(html).val("-1").attr('disabled', false).siblings('span').html('Диаметр').css('color', '#35393d');
                }
            }else {
                $('.t_calc_table').css('display', 'none');
            }
        }
    });
}
function singleRunInit()
{

    if(getCookie('region')===null){  // только один запуск для каждого посетителя
        $.ajax({
            url: '/ax/geoCity',
            success: function(r)
            {
                if(r.fres) {

                    var region=0;
                    var _region='';
                    if(r.geo.sx_country_code=='RU'){
                        if(r.geo.sx_region == 'Москва') {
                            region=77;
                            _region='Москва';
                        }
                        else if(r.geo.sx_region == 'Московская область') {
                            region=50;
                            _region='Московская область';
                        }
                        else {
                            region=-7750;
                            _region='Россия без Москвы и области (8-800)';
                        }
                    }
                    else if((r.geo.sx_country_code+'').length==2) {
                        region=-7;
                        _region='Не Россия'
                    }
                    else {
                        region=-1;
                        _region='Не определен';
                    }

                    setCookie('region', region, true);

                    if(region==-7750) $('header .phones').addClass('p800');

                    citySelect(r.geo.cityId);

                    if(typeof _gaq !='undefined'){

                        _gaq.push(['_trackEvent', 'Interface', 'region', _region, undefined, true]);
                        _gaq.push(['_trackEvent', 'Interface',
                            'geo',
                                r.geo.sx_country_code +
                                ' / ' +
                                r.geo.sx_region +
                                ' / ' +
                                r.geo.sx_city +
                                (r.geo.cityId ? '*1*' : '*0*')
                            , undefined, true]);

                        if(r.geo.sx_city=='')
                            _gaq.push(['_trackEvent', 'Interface', 'geoIPNotResolved', r.geo.ip, undefined, true]);

                    }
                } else {
                    if(typeof _gaq !='undefined'){
                        _gaq.push(['_trackEvent', 'Interface', 'GEOError', r.geo.ip + ' * ' + r.err_msg, undefined, true]);
                    }
                }
            }
        });

    }
}

function citySelect(cityId)
{
    if(cityId!=0) $('header #cityId').val(cityId).change();
}

function feedbackSubmitInit()
{

    $('form.feedback').submit(function(){

        $.ajax({
            url: '/ax/feedback',
            data: {f:$(this).serialize()},
            success: function(r){
                if(r.fres){
                    $.scrollTo($('div.feedback'),300);
                    $('div.feedback').slideUp('fast',function(){
                        $(this).html('Сообщение отправлено.').slideDown('fast');
                    });
                }else {
                    $('form.feedback *').removeClass('uncorrect');
                    if(r.err_msg!='') emsg(r);
                    else{
                        $.scrollTo($('div.feedback'),300);
                        for(var k in r.uncorrect){
                            $('form.feedback [for='+k+']').addClass('uncorrect');
                        }
                    }

                }

            }
        });
        return false;
    });

}


function subscribe()
{
    $.ajax({
        url: '/ax/subscribe',
        data: {email:$('#subscribe [name=email]').val()},
        success: function(r){
            if(r.fres){
                $('#subscribe').html('Подписка прошла успешно.');
            }else emsg(r);
        }
    });
    return false;
}

function tsf1(e)
{
    mergeFormWithCookie('tsf1',$(e.target).parents('form:eq(0)'));
}

function tsf2(e) {
    mergeFormWithCookie('tsf2',$(e.target).parents('form:eq(0)'));
    $(this).parents('.dsForm').find('.select-01').removeClass('error');
}

function tsort_click(){
    var a=location.href.replace(/[&\?]{1}ord=[0-9\-]*/,'').replace(/&?\??page=[0-9]*/,'').replace(/&?\??num=[0-9]*/,'');
    //location.href=a+(a.indexOf('?')!=-1?'&':'?')+'ord='+$(this).attr('val');
    $.ajax({
        url: a+(a.indexOf('?')!=-1?'&':'?')+'ord='+$(this).attr('val')+'&ajax_tsort=1',
        dataType: 'html',
        success: function(r){
            if(r){
                $('.search_replaceable_content').html(r);

                $('.tsort > li').click(tsort_click);

                $('.limits')
                    .chosen({
                        disable_search_threshold: 20
                    })
                    .change(limits_change);

                vids_change();
                if(isDefined(window.adminLogged) && window.adminLogged) {
                    sclInit();
                }
                initChartHandlers();
            }else {
                console.log(r);
            }

        }
    });
}

function limits_change(){
    var a=location.href.replace(/[&\?]{1}num=[0-9\-]*/,'').replace(/&?\??page=[0-9]*/,'');
    $.ajax({
        url: a+(a.indexOf('?')!=-1?'&':'?')+'num='+$(this).val()+'&ajax_tsort=1',
        dataType: 'html',
        success: function(r){
            if(r){
                $('.search_replaceable_content').html(r);

                $('.tsort > li').click(tsort_click);

                $('.limits')
                    .chosen({
                        disable_search_threshold: 20
                    })
                    .change(limits_change);

                vids_change();
                if(isDefined(window.adminLogged) && window.adminLogged) {
                    sclInit();
                }
                initChartHandlers();
            }else {
                console.log(r);
            }

        }
    });
}

function showmore(){
    var a=location.href.replace(/&?\??num=[0-9\-]*/,'').replace(/\&ajax_tsort=1/i,'').replace(/&?\??page=[0-9]*/,'').replace(/\#\d+/,'');
    cur_limit += 12;
    $.ajax({
        url: a+(a.indexOf('?')!=-1?'&':'?')+'num='+ cur_limit + '&ajax_tsort=1',
        dataType: 'html',
        success: function(r){
            if(r){
                $('.search_replaceable_content, .replaceable_content').each(function(){
                    this.innerHTML = r;
                    // Переделываем ссылки из search_replaceable_content
                    var cur_url = window.location.href.replace(/&?\??num=[0-9\-]*/, '').replace(/\&ajax_tsort=1/i, '').replace(/&?\??page=[0-9]*/, '').replace(/\#\d+/,'');
                    $('.search_replaceable_content a, .replaceable_content a').each(function(){
                        if (isDefined($(this).attr('href')) && $(this).attr('href') != '#'){
                            var pid = parseInt($(this).parents('.replaceable_goods').find('a.retl').attr('id'));
                            $(this).click(function(){
                                var new_url = cur_url + ((cur_url.indexOf('?') != -1 ? '&' : '?') + 'num=' + parseInt(cur_limit) + '&ajax_tsort=1' + '#' + pid);
                                window.history.replaceState({retUrl : new_url}, document.title, new_url);
                                //window.location.replace(new_url);
                            });
                        }
                    });
                });

                $('.tsort > li').click(tsort_click);

                $('.limits')
                    .chosen({
                        disable_search_threshold: 20
                    })
                    .change(limits_change);

                vids_change();
                if(isDefined(window.adminLogged) && window.adminLogged) {
                    sclInit();
                }
                initChartHandlers();
            }else {
                console.log(r);
            }
        }
    });
}

function goBack(){
    /*var prev_url = document.referrer.replace(/&?\??num=[0-9\-]*!/,'').replace(/\&ajax_tsort=1/i,'').replace(/&?\??page=[0-9]*!/,'');
    var cur_url = window.location.href;
    var url_matches = cur_url.match(/.+\#(\d+\-\d+)$/);
    if (isDefined(url_matches) && url_matches !== null && url_matches[1] != '') {
        var e_params = url_matches[1].split('-');
        window.location.replace(prev_url+(prev_url.indexOf('?')!=-1?'&':'?')+'num='+parseInt(e_params[0])+'&ajax_tsort=1'+'#'+e_params[1]);
    }
    else */history.back();
}

function vids_change(){
    $('.vids .setLentaMode').click(function(){
        setCookie('stype','lenta');
        $.ajax({
            url: location.href+(location.href.indexOf('?')!=-1?'&':'?') + '&ajax_tsort=1',
            dataType: 'html',
            success: function(r){
                if(r){
                    $('.search_replaceable_content').html(r);

                    $('.tsort > li').click(tsort_click);

                    $('.limits')
                        .chosen({
                            disable_search_threshold: 20
                        })
                        .change(limits_change);

                    vids_change();
                    if(isDefined(window.adminLogged) && window.adminLogged) {
                        sclInit();
                    }
                    initChartHandlers();
                }else {
                    console.log(r);
                }

            }
        });
        return false;
    });

    $('.vids .setBlockMode').click(function(){
        setCookie('stype','block');
        $.ajax({
            url: location.href+(location.href.indexOf('?')!=-1?'&':'?') + '&ajax_tsort=1',
            dataType: 'html',
            success: function(r){
                if(r){
                    $('.search_replaceable_content').html(r);

                    $('.tsort > li').click(tsort_click);

                    $('.limits')
                        .chosen({
                            disable_search_threshold: 20
                        })
                        .change(limits_change);

                    vids_change();
                    if(isDefined(window.adminLogged) && window.adminLogged) {
                        sclInit();
                    }
                    initChartHandlers();
                }else {
                    console.log(r);
                }

            }
        });
        return false;
    });

    $('.vids .active').click(function(){
        return false;
    });
}


// Ютуб-видео в модальном окне
$(document).ready(function () {
    $(".js-modal-video").fancybox({
        type: "iframe",
        maxWidth: 800,
        maxHeight: 600,
        fitToView: false,
        width: '70%',
        height: '70%',
        padding: 0,
        autoSize: false,
        closeClick: false,
        openEffect: 'none',
        closeEffect: 'none'
    });
});

window.mobilecheck = function() {
    var check = false;
    (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
    return check;
};