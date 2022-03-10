/*
    Динамика взаимодействия, внешний вид
 */

$(function ($) {

    $('blockquote').prepend('<i></i>');

    $('header nav a[href=#]').click(function () {
        return false;
    });

    //стилизация селектов
    $('div.select-01 select, div.select-02 select, div.select-03 select, div.select-04 select, div.select-05 select').each(function () {
        var select = $(this)
            , span = $(this).prev()
            , i = this.selectedIndex
            , text = $('option', this).eq(i).text();

        span.text(text);
        select
            .css({opacity: 0})
            .change(function () {
                var i = this.selectedIndex
                    , text = $('option', this).eq(i).text();
                span.text(text);
            });
    });

    //переключение вкладок filter
    $('#filter .nav-filter > div').click(function () {
        $(this).addClass('active')
            .siblings().removeClass('active');
        $('#filter .items-filter > div').eq($(this).index()).addClass('active')
            .siblings().removeClass('active');
    });
	
	$('#filter .items-filter__mobile-trg').click(function () {
		var $triggers = $('#filter .items-filter__mobile-trg'),
		$tabs = $('#filter .items-filter > div'),
		index = $triggers.index($(this));
		$tabs.removeClass('active');
        $tabs.eq(index).addClass('active');
    });


    //ховер для меню
    $('header nav > ul > li > a').click(
        function (e) {
            var block = $(this).parents('li'),
                a = block.children('a'),
                div = block.find('div.wrapper');
            var visible = div.is(':visible');
            // Закрываем остальные
            $('header nav > ul > li > div.wrapper').each(function(){
                $(this).hide();
                $(this).parents('li').removeClass('hover');
            });
            //
            if (div.length) {
                if (div.hasClass('nav-it1') || div.hasClass('nav-it2')){
                    div.css('width', $('header nav').innerWidth() - 8);
                    $('header nav div.right').css('width', $('header nav').innerWidth() - $('header nav div.left').innerWidth() - $('header nav div.middle').innerWidth() - 11);
                }
                if (visible)
                {
                    a.height(30).css('border-radius', "5px");
                    div.fadeOut("slow");
                    block.removeClass('hover');
                }
                else {
                    a.height(50)
                        .css('border-radius', "5px 5px 0 0");
                    div.fadeIn("fast");
                    block.addClass('hover');
                }
            }
            if (div.hasClass('nav-it1') || div.hasClass('nav-it2')) {
                e.stopPropagation();
                e.preventDefault();
            }
        }
    );
    $(document).click( function(event){
        if( $(event.target).closest("header nav > ul > li.hover div.wrapper").length )
            return;
        $("header nav > ul > li.hover div.wrapper").fadeOut("slow");
        $("header nav > ul > li.hover > a").height(30).css('border-radius', "5px");
        $("header nav > ul > li.hover").removeClass('hover');
        event.stopPropagation();
    });

    //меню гармошка сайдбар
    $('.menu-tipes > li a.h1').click(function () {
        var block = $(this).parent(),
            ul = block.find('ul');

        if (ul.length) {
            $(this).parent('li').toggleClass('active')
                .siblings('li').removeClass('active');
            return false;
        } else {
            return;
        }
        ;
    });

    $('.faq > ul li > a').click(function () {
        $(this).parent().toggleClass('active');
        return false;
    });

    // развернуть описание товара
    $('.des-link a').click(function () {
        $(this).next('div').toggleClass('active');
        return false;
    });

    // подсветка строк
    /*$('#content .goods-dubble a.zoom').click(function () {
        $(this).parents('.img').addClass('hov');
        return false;
    });*/
    $('#content .goods-dubble a.zoom, .lenta_image a.zoom').fancybox(
        {
            openEffect    : 'none',
            closeEffect    : 'none',
            closeBtn  : true,
            helpers : {
                title : {
                    type: 'inside'
                },
            },
            beforeLoad: function(){
				console.log('beforeLoad');
                /*if(window.mobilecheck()) {
                    document.getElementById("viewport").setAttribute("content", "width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0");
                }*/ 
            },
            afterLoad: function(){
				console.log('afterLoad');
                var el = $(this.element[0]);
                var cid = el.attr('cid'),
                    maxQ = el.attr('maxQty'),
                    pdi = '#' + el.attr('pdi'),
                    am = el.attr('defQty');
                var chart = '<span class="price scl" cat_id="' + cid + '">'+ $(pdi + ' .price.scl').html() +'</span>' +
                    '<div class="buy-wrap"><div class="input-basket"><a href="#"></a><a href="#"></a><input id="input-basket-qty" type="text" value="' + am + '" minQty="1" maxQty="' + maxQ + '" cat_id="' + cid + '"></div>' +
                    '<a href="#" class="buy-new tocart" pid="mtqp' + cid + '" id="mtqp' + cid + '" title="Добавить товар в корзину" maxqty="' + maxQ + '" defqty="' + am + '" cid="' + cid + '">купить</a></div>' +
                    '<div class="clearfix"></div>';
                this.title = '<b>Узнать подробнее о модели</b><br/><a href="' + el.attr('model_href') + '">' + el.attr('model_name') + '</a>' + chart;
            },
            afterShow: function(){
				console.log('afterShow');
                $('.fancybox-opened .input-basket').each(function ()
                {
                    /*if(window.mobilecheck()) {
                        document.getElementById("viewport").setAttribute("content", "width=300, height=600");
                    }*/
                    var $buttons = $(this).find('a'),
                        $input = $(this).find('input'),
                        maxQty = $input.attr('maxQty'),
                        minQty = $input.attr('minQty'),
                        cat_id = $input.attr('cat_id');

                    $buttons.click(function ()
                    {
                        var val = $input.val();
                        if ($(this).index()) {      // if index == 2, button is increment
                            $input.val(++val);
                        } else {                    // if index == 0, button is decrement
                            if (val > 1)            // if val == 1, stop decrementing
                                $input.val(--val);
                        }
                        checkLimit($input, $(this), minQty, maxQty);
                        cartChangeAmount(cat_id, $input.val());
                        return false;
                    });

                    $input.change(function ()
                    {
                        if (!/^[1-9][0-9]*$/.test($(this).val())) return true;

                        checkLimit($input, $(this), minQty, maxQty);
                        cartChangeAmount(cat_id, $(this).val());
                    });
                });
                // Добавление в корзину
                $('.fancybox-opened .tocart').click(function ()
                {
                    var $dataEl = $('.fancybox-opened [id=' + $(this).attr('pid') + ']');
                    var cat_id = $dataEl.attr('cid');
                    var maxQty = $dataEl.attr('maxQty') * 1;
                    if (typeof $dataEl.parents('.buy-wrap').find('#input-basket-qty').attr('value') != 'undefined') var am = $dataEl.parents('.buy-wrap').find('#input-basket-qty').val() * 1; else var am = $dataEl.attr('defQty') * 1;
                    if (maxQty == 0) {
                        $dataEl.qtip({
                            content: 'Этого товара сейчас нет на складе.',
                            show: true,
                            hide: 'mouseout',
                            position: {
                                my: 'top left',  // Position my top left...
                                at: 'bottom right', // at the bottom right of...
                                target: $dataEl // my target
                            },
                            style: {
                                classes: 'qtip-shadow qtip-rounded'
                            }
                        });
                    } else if (am > maxQty) {
                        $dataEl.qtip({
                            content: 'Этого товара на складе меньше ' + am + ' шт. Все равно добавить в корзину?',
                            show: true,
                            hide: 'mouseout',
                            position: {
                                my: 'top left',  // Position my top left...
                                at: 'bottom right', // at the bottom right of...
                                target: $dataEl // my target
                            },
                            style: {
                                classes: 'qtip-shadow qtip-rounded'
                            }
                        });
                    } else { // Добавляем в корзину
                        $.ajax({
                            url: '/cart/add',
                            data: {
                                cat_id: cat_id,
                                amount: am
                            },
                            success: function (d)
                            {
                                if (d.fres) {
                                    if (!isDefined(d.exist)) {
                                                                                /*================================================================================================*/
                                        if($('.box-logo .basket i:first').length == 0) // Добавляем необходимое, если корзина была пуста
                                        {
                                            $('.empty_basket_title').remove();
                                            $('.basket').prepend('<i>0</i><p><a href="/cart.html">Корзина:</a><b>0 руб.</b></p><a href="/cart.html" class="buy" title="Перейти к оформлению заказа">Оформить<i></i></a>');
                                        }
                                        /*================================================================================================*/
                                        $('.box-logo .basket i:first').html(d.b_count);
                                        $('.box-logo .basket b').html(d.bsum);
                                        if (typeof _gaq != 'undefined') {
                                            if (d.gr == 1)    _gaq.push(['_trackEvent', 'Cart', 'modalAddTyre', d.fn_, d.price * 1]);
                                            if (d.gr == 2)    _gaq.push(['_trackEvent', 'Cart', 'modalAddDisk', d.fn_, d.price * 1]);
                                        }
                                        if (typeof window['yaCounter' + YAM] != 'undefined') {
                                            window['yaCounter' + YAM].reachGoal("MODAL-ADD-TO-CART");
                                        }
                                        location.href = '/cart.html';
                                    } else $dataEl.qtip({
                                        content: 'Товар уже в корзине',
                                        show: true,
                                        hide: 'mouseout',
                                        position: {
                                            my: 'top left',  // Position my top left...
                                            at: 'bottom right', // at the bottom right of...
                                            target: $dataEl // my target
                                        },
                                        style: {
                                            classes: 'qtip-shadow qtip-rounded'
                                        }
                                    });
                                } else {
                                    if (typeof _gaq != 'undefined') {
                                        _gaq.push(['_trackEvent', 'Interface', 'JSError:btn-buy', d.err_msg, undefined, true]);
                                    }
                                    return emsg(d);
                                }
                            }
                        })
                    }
                    return false;
                });
            }
        });

    $('#content .goods-dubble ul li').mouseleave(function () {
        $(this).children('.img').removeClass('hov');
    });

    // модели шин - фильтр
    $('.season-f li a').click(function () {
        $(this).parent().addClass('active')
            .siblings('li').removeClass('active');
    });

    // модели шин - фильтр
    $('.season-gf li a').click(function () {
        $(this).parent().addClass('active')
            .siblings('li').removeClass('active');
    });

    // Подобрать по диаметру - модели шин
    $('.list-param li a').click(function () {
        $(this).addClass('active')
        $(this).parent().siblings('li').children('a').removeClass('active');
    });

    //переключение вкладок filter NEW
    $('.filter-types-size .nav a').click(function() {
        $(this).addClass('active')
            .siblings().removeClass('active');
        $('.filter-types-size .items > div').eq($(this).index()).addClass('active')
            .siblings().removeClass('active');
        return false;
    });
	
	$('.filter-discs-size__mobile-trg').on('click', function () {
		var $scope = $(this).closest('.items');	
		$scope.find('.active').removeClass('active');
		$(this).addClass('active').next('div').addClass('active'); 

		return false;
	});

    $('.filter-discs-size .nav a').click(function() {
        $(this).addClass('active')
            .siblings().removeClass('active');
        $('.filter-discs-size .items > div').eq($(this).index()).addClass('active')
            .siblings().removeClass('active');
        return false;
    });

    $('.cut-ctrl').each(function(){
        var cid=$(this).attr('forid');
        $(this).click(function(){
            $(this).hide();
            $('.cut-ctrl-hide[forid="'+cid+'"]').show();
            $('#'+cid).slideDown(500);
            return false;
        });

        $('.cut-ctrl-hide[forid="'+cid+'"]').click(function()
        {
            $(this).hide();
            $('#'+cid).slideUp(500,function(){
                $('.cut-ctrl[forid="'+cid+'"]').show();
            });
            return false;
        })
    });

    $('[rel=scrollto]').click(function()
    {
        var $target=$('[data-container="'+$(this).attr('data-target')+'"]');
        if($target != undefined) $.scrollTo($target,500);
        return false;
    });

    scrollUpInit();

    // Подсказка при подборе по авто
    $('.podbor_rating_avto_wrap .question').qtip({
        content: 'Исключительно субъективный рейтинг, показывающий наше отношение к данному автомобилю и напрямую влияющий на ассортимент товара.',
        show: 'mouseenter',
        hide: 'mouseout',
        position: {
            my: 'bottom left',
            at: 'top right',
            target: $('.podbor_rating_avto_wrap .question') // my target
        },
        style: {
            classes: 'qtip-shadow qtip-rounded'
        }
    });
});

function scrollUpInit()
{

    $('<div class="scroll-up"></div>').appendTo('body');

    var scrollUpOn=false;

    $(document).scroll(function()
    {
        var dy=$(document).scrollTop();

        if(dy>250 && !scrollUpOn) {
            scrollUpOn=true;
            $('.scroll-up')
                .fadeIn(700)
                .click(function(){
                    $('html, body').animate({scrollTop: '0px'}, 600);
                    if (typeof _gaq != 'undefined') _gaq.push(['_trackEvent', 'Interface', 'Scroll-Up', 'Scroll-Up;']);
                })
                .hover(
                function(){
                    $('.scroll-up').stop().animate({'opacity':0.9},100);
                },
                function(){
                    $('.scroll-up').stop().animate({'opacity':0.3},100);
                });

        }else if(dy<250 && scrollUpOn){
            scrollUpOn=false;
            $('.scroll-up').stop().fadeOut(700).unbind('mouseenter').unbind('mouseleave').unbind('click');
        }

    });
}