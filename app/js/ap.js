/* AP   */

$.prettyLoader({
    theme: 'ttt',
    delay:false,
    loader: '/app/images/prettyLoader/ajax-loader.gif'
});

var ap = {};

ap.clear = function ()
{
    ap.data = {svendor: '', smodel: '', syear: '', smodif: '', apMode: 0};
}

ap.getData = function ()
{
    var d = getCookie('apData') || '';
    if (d != '') {
        try {
            ap.data = unserialize(Base64.decode(d));
        } catch (err) {
            delCookie(ap.apData);
            ap.clear();
        }
        return true;
    } else {
        ap.clear();
    }
}

ap.setData = function ()
{
    setCookie('apData', Base64.encode(serialize(ap.data)));
}

ap.getData();

ap.helper_arrow = '<div class="submit_helper_wrapper"><img class="submit_helper" src="/app/images/down_arrow.png" alt="" /></div>';
ap.model0 = '<option value="">не выбрано</option>';
ap.year0 = '<option value="">не выбрано</option>';
ap.modif0 = '<option value="">не выбрано</option>';

ap.goSearch = function (e, type, suffix)
{
    var $form=$(e.target).parents('form');
    var $marks=$form.find('.apMark');
    var $models=$form.find('.apModel');
    var $years=$form.find('.apYear');
    var $modifs=$form.find('.apModif');

    /*var pass = true;
    if ($marks.val() == '') {
    pass = false;
    }
    if ($models.val() == '') {
    pass = false;
    }
    if ($years.val() == '') {
    pass = false;
    }
    if ($modifs.val() == '') {
    pass = false;
    }
    if (pass) 
    {*/
    ap.data = {svendor: $marks.val(), smodel: $models.val(), syear: $years.val(), smodif: $modifs.val()};

    var delimeter = '--';
    var v = $marks.val() != '' && $marks.val() != '0' && $marks.val() != 'null' ? ($marks.val()) : '';
    var m = $models.val() != '' && $models.val() != '0' && $models.val() != 'null' ? (delimeter + $models.val()) : '';
    var y = $years.val() != '' && $years.val() != '0' && $years.val() != 'null' ? (delimeter + $years.val()) : '';
    var md = $modifs.val() != '' && $modifs.val() != '0' && $modifs.val() != 'null' ? (delimeter + $modifs.val()) : '';
    var m = v + m + y + md;
    if (typeof window.apUrl != 'undefined' && typeof type == 'undefined')
    {
        var url = window.apUrl+'/'+ m;
    }
    else
    {
        if (typeof suffix != 'undefined')
        {
            if (parseInt($form.find('[name="submited"]').val())){
                suffix += '?submited=1';
            }
            var url = type+'/'+ m + suffix; 
        }
        else{
            if (parseInt($form.find('[name="submited"]').val())){
                m += '?submited=1';
            }
            var url = type+'/'+ m;
        }
    }
    ap.setData(); 
    if (m != '') location.href = url;
    else location.href = type + '.html';
    //}else msg('Не все параметры авто выбраны');
}

ap.updateStyles=function($form)
{
    $('.submit_helper_wrapper').remove();// Проверка, что стрелки еще нет, в противном случае убираем её
    $form.find('select').each(function(){
        if($(this).find('option').length<=1) 
        {
            $(this).prop('disabled', true);
            $(this).parent('div').addClass('bgrey').parent().prev('td').addClass('grey');
        }
        else
        {
            $(this).prop('disabled', false);
            $(this).parent('div').removeClass('bgrey').parent().prev('td').removeClass('grey');
        }
    });
    $.prettyLoader.hide();
}

ap.markSelected = function (e)
{
    $.prettyLoader.show();
    var $form=$(e.target).parents('form');
    var $marks=$form.find('.apMark');
    var $models=$form.find('.apModel');
    $models.html(ap.model0);
    var $years=$form.find('.apYear');
    $years.html(ap.year0);
    var $modifs=$form.find('.apModif');
    $modifs.html(ap.modif0);
    var $dsbl=$form.find('select,input');
    $dsbl.prop('disabled',true);
    ap.clear();
    if ($("#filter.tyres_catalog_filter .nav-filter > div:last-child").hasClass("active")) {
        window.apUrl = "/podbor-shin";
    }else if($("#filter.disks_catalog_filter .nav-filter > div:first-child").hasClass("active")) {
        window.apUrl = "/avto-podbor";
    }
    if (typeof window.apUrl != 'undefined')
    {
        if ($marks.val() != '')
        {
            window.location.href = window.apUrl + '/' + $marks.val() + '.html';
        }
        else
        {
            window.location.href = '/podbor_sd.html';
        }
    }
    else
    {
        $.ajax({
            url: '/ajax-podbor/getModels.html',
            data: {mark: $marks.val()},
            complete: function () {
                $dsbl.prop('disabled',false);
                ap.updateStyles($form);
            },
            success: function (r) {
                if (r.fres) {
                    $models.show().html(r.s).siblings('span').html($models.children(':first').text());
                    $years.siblings('span').html($years.children(':first').text());
                    $modifs.siblings('span').html($modifs.children(':first').text());
                } 
                else 
                {
                    emsg(r);
                }
            }
        });
    }
};

ap.modelSelected = function (e)
{
    $.prettyLoader.show();
    var $form=$(e.target).parents('form');
    var $marks=$form.find('.apMark');
    var $models=$form.find('.apModel');
    var $years=$form.find('.apYear');
    $years.html(ap.year0);
    var $modifs=$form.find('.apModif');
    $modifs.html(ap.modif0);
    var $dsbl=$form.find('select,input');
    $dsbl.prop('disabled',true);
    ap.clear();
    if ($("#filter.tyres_catalog_filter .nav-filter > div:last-child").hasClass("active")) {
        window.apUrl = "/podbor-shin";
    }
    else if($("#filter.disks_catalog_filter .nav-filter > div:first-child").hasClass("active")) {
        window.apUrl = "/avto-podbor";
    }
    if (typeof window.apUrl != 'undefined')
    {
        window.location.href = window.apUrl + '/' + $marks.val() + '--' + $models.val() + '.html';
    }
    else
    {
        $.ajax({
            url: '/ajax-podbor/getYears.html',
            data: {mark: $marks.val(), model: $models.val()},
            complete: function () {
                $dsbl.prop('disabled',false);
                ap.updateStyles($form);
            },
            success: function (r) {
                if (r.fres) {
                    $years.show().html(r.s).siblings('span').html($years.children(':first').text());
                    $modifs.siblings('span').html($modifs.children(':first').text());
                } else emsg(r);
            }
        }); 
    }
}

ap.yearSelected = function (e)
{
    $.prettyLoader.show();
    var $form=$(e.target).parents('form');
    var $marks=$form.find('.apMark');
    var $models=$form.find('.apModel');
    var $years=$form.find('.apYear');
    var $modifs=$form.find('.apModif');
    $modifs.html(ap.modif0);
    var $dsbl=$form.find('select,input');
    $dsbl.prop('disabled',true);
    ap.clear();
    if ($("#filter.tyres_catalog_filter .nav-filter > div:last-child").hasClass("active")) {
        window.apUrl = "/podbor-shin";
    }
    else if($("#filter.disks_catalog_filter .nav-filter > div:first-child").hasClass("active")) {
        window.apUrl = "/avto-podbor";
    }
    if (typeof window.apUrl != 'undefined')
    {
        window.location.href = window.apUrl + '/' + $marks.val() + '--' + $models.val() + '--' + $years.val() + '.html';
    }
    else
    {
        $.ajax({
            url: '/ajax-podbor/getModifs.html',
            data: {mark: $marks.val(), model: $models.val(), year: $years.val()},
            complete: function () {
                $dsbl.prop('disabled',false);
                ap.updateStyles($form);
            },
            success: function (r) {
                if (r.fres) {
                    $modifs.show().html(r.s).siblings('span').html($modifs.children(':first').text());
                } else emsg(r);
            }
        });
    }
}

ap.modifSelected = function (e)
{
    /*$.prettyLoader.show();*/
    var $el=$(e.target);
    if($el.hasClass('auto-submit')) ap.goSearch(e);
    if (isDefined($('#filter.disks_catalog_filter .main_apGo')[0]))
    {
       $('.submit_helper_wrapper').remove(); // Проверка, что стрелки еще нет
       $('#filter.disks_catalog_filter .main_apGo').parent().append(ap.helper_arrow); 
       if ($('.submit_helper_wrapper .submit_helper').length > 0)
        {
            $(".submit_helper_wrapper .submit_helper").effect( "shake", {
                times: 4,
                direction: 'up',
                distance: 1
                }, 1500);
            setInterval(
                function(){
                    $(".submit_helper_wrapper .submit_helper").effect( "shake", {
                        times: 4,
                        direction: 'up',
                        distance: 1
                        }, 1500);
                }, 3000);
        } 
    }
}

ap.initEvents = function ()
{
    $('.main_apGo').click(function (e) {
        e.preventDefault();
        if ($(this).attr('p_type') == 'disk')
        {
            if ($('.apMark').val().length == 0)
            {
                var type = '/podbor_sd'; 
            }
            else var type = '/avto-podbor';
            // Костыль для .html в конце
            if ($('.apModif').val().length == 0)
            {
                var suffix = '.html'; 
            }
        }
        if ($(this).attr('p_type') == 'bus')
        {
            var type = '/podbor-shin';
            // Костыль для .html в конце
            if ($('.apModif').val().length == 0)
            {
                var suffix = '.html';
            }
        }
        ap.goSearch(e, type, suffix);
    });

    $('.apMark').change(function (e) {
        ap.markSelected(e);
    });

    $('.apModel').change(function (e) {
        ap.modelSelected(e);
    });

    $('.apYear').change(function (e) {
        ap.yearSelected(e);
    });

    $('.apModif').change(function (e) {
        ap.modifSelected(e);
    });

}

$(document).ready(function ()
    {

        ap.initEvents();
        ap.updateStyles($('.apForm'));

        /*Цвета типоразмеров*/
        $('#content .mini-gallery ul li > img').click(function(e){
            $('#typo_color_form input').val(this.id);
            $('#typo_color_form').submit();
        });
        $('#show_all_tcolors').click(function(e){
            window.location.replace(window.location.href.replace(/([\?\&]typo\_color=[^\&\?\#]+)/i, ''));
            //$('#typo_color_form input').val('');
            //$('#typo_color_form').submit();
        });
        /*******************/
});

// END AB
