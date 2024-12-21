// CALLBACK FORM

var cb = {};

cb.antispamCodeInitialUpdate = function () {
    const d = new Date();
    // нет, тут нет ошибки
    $('#callback-popup input[name=_antsc]').val(Math.ceil(Math.log10(d.getDate() * d.getMonth() * d.getFullYear())*10000));
};

cb.antispamCodeOnSubmit = function () {
    const d = new Date();
    $('#callback-popup input[name=_antsc]').val(Math.ceil(Math.log10(d.getDate() * (d.getMonth() + 1) * d.getFullYear())*10000));
}

cb.isLoaded = function ()
{
    return $('#callback-popup').length;
}

cb.open = function ()
{
    if(!cb.isLoaded()) {
        cb.load();
        return false;
    }

    if(typeof _gaq !='undefined'){
        _gaq.push(['_trackEvent', 'Interface', 'callBackFormOpen', 'callBackFormOpen;']);
    }

    $('#overlay')
    .fadeIn('slow')
    .click(function()
        {
            cb.close();
            return false;
    });
    $('#callback-popup').fadeIn('slow');
    $('#callback-popup .box-recall').fadeIn('slow');
    return false;
}

cb.close = function ()
{
    $('#overlay').fadeOut('slow');
    $('#callback-popup').fadeOut('slow');
    $('#callback-popup .box-recall').fadeOut('slow');
    return false;
}

cb.load = function ()
{
    if (!cb.isLoaded()) {
        $.ajax({
            dataType: 'html',
            url: '/ax/callbackForm',
            success: function (r) {
                $('body').append(r);

                $('#callback-popup a.close').click(cb.close);
                cb.antispamCodeInitialUpdate();

                $('#callback-popup form').submit(function (e) {
                    e.preventDefault();
                    var canSend = true;

                    if (canSend) {
                        cb.antispamCodeOnSubmit();
                        $.ajax({
                            url: '/ax/callback',
                            data: {f: $('#callback-popup form').serialize()},
                            success: function (r) {
                                if (r.fres) {
                                    $('#callback-popup form').html('Спасибо, наш менеджер свяжется с вами ближайшее время');

                                    if(typeof _gaq !='undefined'){
                                        _gaq.push(['_trackEvent', 'Interface', 'callBackFormSend', 'callBackFormSend;']);
                                    }

                                } else msg('Сообщение не отправлено. ' + r.fres_msg);
                            }
                        });
                    }
                });
                cb.open();
            }
        });
    } else cb.open();
}

// ANNOUNCE FORM

var an = {};

an.isLoaded = function ()
{
    return $('#analog-callback-popup').length;
}

an.open = function ()
{
    if(!an.isLoaded()) {
        an.load();
        return false;
    }

/*    if(typeof _gaq !='undefined'){
        _gaq.push(['_trackEvent', 'Interface', 'callBackFormOpen', 'callBackFormOpen;']);
    } */

    $('#overlay')
    .fadeIn('slow')
    .click(function()
        {
            an.close();
            return false;
    });
    $('#analog-callback-popup').fadeIn('slow');
    $('#analog-callback-popup .box-recall').fadeIn('slow');
    // заполняем форму информацией
    $('#analog-callback-popup .popup_product_info .title').html($('h1.title.cat').html());
    $('#analog-callback-popup .popup_product_info .img').html($('.img-product a').html());
    $('#analog-callback-popup .popup_product_info .img img').css('height', '100px');
    //
    return false;
}

an.close = function ()
{
    $('#overlay').fadeOut('slow');
    $('#analog-callback-popup').fadeOut('slow');
    $('#analog-callback-popup .box-recall').fadeOut('slow');
    return false;
}

an.load = function ()
{
    if (!an.isLoaded()) {
        $.ajax({
            dataType: 'html',
            url: '/ax/announceForm',
            success: function (r) {
                $('body').append(r);

                $('#analog-callback-popup a.close').click(an.close);

                $('#analog-callback-popup form').submit(function (e) {
                    e.preventDefault();
                    var canSend = true;

                    if (canSend) {
                        $.ajax({
                            url: '/ax/announce',
                            data: {f: $('#analog-callback-popup form').serialize()},
                            success: function (r) {
                                if (r.fres) {
                                    $('#analog-callback-popup form').html('Спасибо, наш менеджер свяжется с вами при поступлении товара');

                                    /*if(typeof _gaq !='undefined'){
                                        _gaq.push(['_trackEvent', 'Interface', 'callBackFormSend', 'callBackFormSend;']);
                                    }*/

                                } else msg('Сообщение не отправлено. ' + r.fres_msg);
                            }
                        });
                    }
                });
                an.open();
            }
        });
    } else an.open();
}

// gallery
var gallery = {};

gallery.isLoaded = function ()
{
    return $('#gallery-popup').length;
}

gallery.open = function (e)
{
    if(!gallery.isLoaded()) {
        gallery.load(e);
        return false;
    }
    $('#overlay')
    .fadeIn('slow')
    .click(function()
        {
            gallery.close();
            return false;
    });
    $('#gallery-popup').fadeIn('slow');
    return false;
}

gallery.close = function ()
{
    $('#overlay').fadeOut('slow');
    $('#gallery-popup').fadeOut('slow');
    return false;
}

gallery.load = function (e)
{
    if (!gallery.isLoaded()) {
        $.ajax({
            dataType: 'html',
            url: '/ax/galleryForm?mid='+$(e.target).attr('mid')+'&gr='+$(e.target).attr('gr'),
            success: function (r) {
                $('body').append(r);

                $('#gallery-popup a.close2').click(gallery.close);

                $('#gallery-popup .nav-gallery a').click(function () {
                    $(this).parent().addClass('active')
                    .siblings('li').removeClass('active');
                    $(this).parents('#gallery-popup').find('.block > img').eq($(this).parent().index()).addClass('active')
                    .siblings('img').removeClass('active');
                    return false;
                });


                gallery.open();
            }
        });
    } else gallery.open();
}



$(document).ready(function ()
    {
        $('body').append('<div id="overlay"></div>');

        //открытие "обратный звонок"
        $('a.recall').click(function () {
            return cb.open();
        });

        //открытие "сообщить о поступлении"
        $('#show_order_form').click(function () {
            return an.open(); 
        });
        
        //открытие "галеря"
        /*$('a.gallery-popup-call').click(function (e) {
        return gallery.open(e);
        });   */
        /* FancyBox */
        $("a.gallery-popup-call").fancybox(
            {
                openEffect    : 'none',
                closeEffect    : 'none',
                
                next     : '<a class="fancybox-nav fancybox-next" href="javascript:;"><span></span></a>',
                prev     : '<a class="fancybox-nav fancybox-prev" href="javascript:;"><span></span></a>',
                
                closeBtn  : true,
                arrows    : true,
                nextClick : true,

                helpers : {
                     title : {
                        position : 'top'
                    },
                    thumbs : {
                        position: 'bottom',
                        width  : 50,
                        height : 50
                    },
                    buttons    : {
                        position: 'top' 
                    }
                }
        });

        $("a.video-call").fancybox({
            openEffect    : 'none',
            closeEffect    : 'none',
            closeBtn  : true,
            afterShow: function(){
                $(".fancybox-wrap, .fancybox-skin").css({'overflow':'hidden'});
            }
        });
        /* /FancyBox */

        //открытие "сертификаты"
        /*$('a.gallery-popup-call').click(function (e) {
        return gallery.open(e);
        });   */
        /* FancyBox */
        $("a.cert-popup-call").fancybox(
            {
                openEffect    : 'none',
                closeEffect    : 'none',

                next     : '<a class="fancybox-nav fancybox-next" href="javascript:;"><span></span></a>',
                prev     : '<a class="fancybox-nav fancybox-prev" href="javascript:;"><span></span></a>',

                closeBtn  : true,
                arrows    : true,
                nextClick : true,

                helpers : {
                     title : {
                        position : 'top'
                    },
                    thumbs : {
                        position: 'bottom',
                        width  : 50,
                        height : 50
                    },
                    buttons    : {
                        position: 'top'
                    }
                }
        });
        /* /FancyBox */
});
