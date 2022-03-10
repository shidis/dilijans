$(document).ready(function ()
{

    // CART control
    initChartHandlers();
});

function initChartHandlers(){
    $('#content .goods-dubble a.zoom, #content .search_replaceable_content .table-list a.zoom').fancybox(
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
                /*if(window.mobilecheck()) {
                    document.getElementById("viewport").setAttribute("content", "width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0");
                }*/
            },
            afterLoad: function(){
                var el = $(this.element[0]);
                var cid = el.attr('cid'),
                    maxQ = el.attr('maxQty'),
                    pdi = '#' + el.attr('pdi'),
                    price = parseFloat(el.attr('cprice')),
                    am = parseInt(el.attr('defQty'));
                var chart = '<span class="price scl" cat_id="' + cid + '">'+ $(pdi + ' .price.scl').html() +' * <span class="p_val">' + am + '</span> = <span class="p_sum_val">' + am * price + '</span> руб.</span>'  +
                    '<div class="buy-wrap"><div class="input-basket"><a href="#"></a><a href="#"></a><input id="input-basket-qty" type="text" value="' + am + '" minQty="1" maxQty="' + maxQ + '" cat_id="' + cid + '"></div>' +
                    '<a href="#" class="buy-new tocart" pid="mtqp' + cid + '" id="mtqp' + cid + '" title="Добавить товар в корзину" maxqty="' + maxQ + '" defqty="' + am + '" cid="' + cid + '">купить</a></div>' +
                    '<div class="clearfix"></div>';
                this.title = '<b>Узнать подробнее о модели</b><br/><a href="' + el.attr('model_href') + '">' + el.attr('model_name') + '</a>' + chart;
            },
            afterShow: function(){
                /*if(window.mobilecheck()) {
                    document.getElementById("viewport").setAttribute("content", "width=300, height=600");
                }*/
                var price = parseFloat($(this.element[0]).attr('cprice'));
                $('.fancybox-opened .input-basket').each(function ()
                {
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
                        // Обсчет цены
                        $(this).parents('.fancybox-title').find('.price .p_val').html($input.val());
                        $(this).parents('.fancybox-title').find('.price .p_sum_val').html(parseInt($input.val()) * price);
                        //
                        return false;
                    });

                    $input.change(function ()
                    {
                        if (!/^[1-9][0-9]*$/.test($(this).val())) return true;

                        checkLimit($input, $(this), minQty, maxQty);
                        cartChangeAmount(cat_id, $(this).val());
                        // Обсчет цены
                        $(this).parents('.fancybox-title').find('.price .p_val').html($input.val());
                        $(this).parents('.fancybox-title').find('.price .p_sum_val').html(parseInt($input.val()) * price);
                        //
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
                                        if($('.box-logo .basket i:first, .mobile-header__basket .basket i:first').length == 0) // Добавляем необходимое, если корзина была пуста
                                        {
                                            $('.empty_basket_title').remove();
                                            $('.basket').prepend('<i>0</i><p><a href="/cart.html">Корзина:</a><b>0 руб.</b></p><a href="/cart.html" class="buy" title="Перейти к оформлению заказа">Оформить<i></i></a>');
                                        }
                                        /*================================================================================================*/
                                        $('.box-logo .basket i:first, .mobile-header__basket .basket i:first').html(d.b_count);
                                        $('.box-logo .basket b, .mobile-header__basket .basket b').html(d.bsum);
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

    $('#cart_frm [name=ptype]').change(function (e)
    {
        if ($(this).val() == 1) {
            $('#ptype-fiz').hide().removeClass('of-show-group').addClass('of-hide-group');
            $('#ptype-ur').show().addClass('of-show-group').removeClass('of-hide-group');
            var $email_wrap = $('#e_email').closest('tr').find('td:first-child');
            $email_wrap.html($email_wrap.html() + ' <sup>*</sup>');
        } else {
            $('#ptype-fiz').show().addClass('of-show-group').removeClass('of-hide-group');
            $('#ptype-ur').hide().removeClass('of-show-group').addClass('of-hide-group');
            var $email_wrap = $('#e_email').closest('tr').find('td:first-child');
            $email_wrap.html($email_wrap.html().replace(' <sup>*</sup>', ''));
        }
    });

    // в корзине
    $('.input-basket').each(function ()
    {
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

    // Обработчик выбора дополнительного товара
    $(document).on('click', '.accessories-box__item-field input', function (e) {
        var accessoriesBox = $(this).closest('.accessories-box');
        var catID = accessoriesBox.data('cat_id');
        var arDop = getAccessories(accessoriesBox);

        updateDopList(arDop, catID);
    });

    // в картолчке товаров
    $('.input-basket2').each(function ()
    {
        var button = $(this).find('a'),
            $input = $(this).find('input'),
            maxQty = $input.attr('maxQty'),
            minQty = $input.attr('minQty');

        button.click(function ()
        {
            var val = $input.val();
            if ($(this).index()) {      // if index == 2, button is increment
                $input.val(++val);
            } else {                    // if index == 0, button is decrement
                if (val > 1)            // if val == 1, stop decrementing
                    $input.val(--val);
            }
            checkLimit($input, $(this), minQty, maxQty);
            return false;
        });

        $input.change(function ()
        {
            if (!/^[1-9][0-9]*$/.test($(this).val()))  $(this).val(minQty);
            checkLimit($input, $(this), minQty, maxQty);
        });
    });

    $('.cart_tbl .btn-del').click(function (e)
    {
        e.preventDefault();
        cartDel(e);
    });

    $('.cart_tbl .am').bind('keyup', function (e)
    {
        cartChangeAmount(e);
    });

    $('#cart_frm .btn-reset').click(function (e)
    {
        e.preventDefault();
        $('#cart_frm input[type!="radio"][type!=button], #cart_frm textarea').val('');
    });

    $('.btn-cart-clear').click(function (e)
    {
        e.preventDefault();
        clearCart(e);
    });


    $('.btn-order-send').click(function (e)
    {
        e.preventDefault();
        orderSend(e);
    });

    $('.tocart').click(function ()
    {
        var $dataEl = $('[id=' + $(this).attr('pid') + ']');
        var cat_id = $dataEl.attr('cid');
        var maxQty = $dataEl.attr('maxQty') * 1;
        if (typeof $dataEl.attr('value') != 'undefined') var am = $dataEl.val() * 1; else var am = $dataEl.attr('defQty') * 1;
        if (maxQty == 0) {
            var altt = $dataEl.attr('altt');
            if (altt != '' && altt != undefined)
                $.confirm({
                    title: 'Добавление в корзину товара',
                    message: 'Этого товара сейчас нет на складе. Но вы можете посмотреть аналогичные типразмеры других производителей.',
                    buttons: {
                        'закрыть окно': {},
                        'посмотреть аналоги': {
                            action: function ()
                            {
                                location.href = urldecode(altt);
                            }
                        }
                    }
                });
            else
                $.confirm({
                    title: 'Добавление в корзину товара',
                    message: 'Этого товара сейчас нет на складе.',
                    buttons: {
                        'закрыть окно': {}
                    }
                });
        } else if (am > maxQty) {
            $.confirm({
                title: 'Добавление в корзину товара',
                message: 'Этого товара на складе меньше ' + am + ' шт. Все равно добавить в корзину?',
                buttons: {
                    'не добавлять': {},
                    'все равно добавить': {
                        action: function ()
                        {
                            addToCart(cat_id, am);
                        }
                    }
                }
            });
        } else addToCart(cat_id, am);
        return false;
    });

    // спарка
    $('.tocart-d').click(function ()
    {
        var id12 = this.id;
        var am1 = $('[amid1="' + id12 + '"]').val();
        var am2 = $('[amid2="' + id12 + '"]').val();
        var cat_id1 = $(this).attr('cid1');
        var cat_id2 = $(this).attr('cid2');
        $.ajax({
            url: '/cart/add2',
            data: {
                list: [
                    {cat_id: cat_id1, amount: am1},
                    {cat_id: cat_id2, amount: am2}
                ]
            },
            success: function (d)
            {
                if (d.fres) {
                    $.confirm({
                        title: 'Товар в корзине',
                        message: '<p>' + d.fn + '</p><p>Вы можете остаться на этой странице или перейти в корзину для оформления заказа.</p>',
                        buttons: {
                            'Продолжить покупки': {
                                class: 'button-grey'
                            },
                            'Оформить заказ': {
                                action: function ()
                                {
                                    location.href = '/cart.html';
                                }
                            }
                        }
                    });
                    /*================================================================================================*/
                    if($('.box-logo .basket i:first, .mobile-header__basket .basket i:first').length == 0) // Добавляем необходимое, если корзина была пуста
                    {
                        $('.empty_basket_title').remove();
                        $('.basket').prepend('<i>0</i><p><a href="/cart.html">Корзина:</a><b>0 руб.</b></p><a href="/cart.html" class="buy" title="Перейти к оформлению заказа">Оформить<i></i></a>');
                    }
                    /*================================================================================================*/
                    $('.box-logo .basket i:first, .mobile-header__basket .basket i:first').html(d.b_count);
                    $('.box-logo .basket b, .mobile-header__basket .basket b').html(d.bsum);
                    if (typeof _gaq != 'undefined') {
                        if (d.gr == 1)    _gaq.push(['_trackEvent', 'Cart', 'addDoubleTyres', d.fn_]);
                        if (d.gr == 2)    _gaq.push(['_trackEvent', 'Cart', 'addDoubleDisks', d.fn_]);
                    }
                    if (typeof window['yaCounter' + YAM] != 'undefined') {
                        window['yaCounter' + YAM].reachGoal("ADD-TO-CART");
                    }
                } else {
                    if (typeof _gaq != 'undefined') {
                        _gaq.push(['_trackEvent', 'Interface', 'JSError:btn-buy2', d.err_msg, undefined, true]);
                    }
                    return emsg(d);
                }
            }
        });

        return false;

    });

    // ****** Быстрый заказ ******
    $('.fast_order').click(function () {
        var $dataEl = $('[id=' + $(this).attr('pid') + ']');
        var cat_id = $dataEl.attr('cid');
        var maxQty = $dataEl.attr('maxQty') * 1;
        if (typeof $dataEl.attr('value') != 'undefined') var am = $dataEl.val() * 1; else var am = $dataEl.attr('defQty') * 1;
        $.ajax({
            url: '/ax/quickOrderForm',
            dataType: 'html',
            data: {
                cid: cat_id,
                am: am ? am : 4
            },
            success: function (r) {
                $.confirm({
                    title: 'Быстрый заказ',
                    message: r,
                    buttons: {}
                });
                $('#confirmBox .wrap').css('width', '750px').css('margin-left', '-375px').css('top', '-100px');
                $('.qord-form #confirmButtons').remove();
                $('.qord-form table.goods td .input-basket').each(function ()
                {
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
                        $input.change();
                        return false;
                    });
                    $input.change(function ()
                    {
                        if (!/^[1-9][0-9]*$/.test($(this).val())) return true;
                        checkLimit($input, $(this), minQty, maxQty);
                    });
                });
                // ***
                var $f = $('.qord-form');
                var price = $f.find('[name=price]').val();
                var defQty = $f.find('[name=defQty]').val();
                var maxQty = $f.find('[name=maxQty]').val();
                var $sum = $f.find('.sum');
                // Изменение количества товара
                $f.find('.input-basket input').keyup(function () {
                    var sum = 0;
                    var am = Math.abs(Math.round($(this).val()));
                    // Изменение количества крепежа при изменении количества основного товара
                    if (am > 0) {
                        // Вычисляем сумму
                        sum += Math.abs(Math.round($(this).val())) * Math.abs(Math.round($(this).attr('p')));
                        $sum.html('' + sum);
                    }
                }).change(function () {
                    var sum = 0;
                    var am = Math.abs(Math.round($(this).val()));
                    // Изменение количества крепежа при изменении количества основного товара
                    if (am > 0) {
                        // Вычисляем сумму
                        sum += Math.abs(Math.round($(this).val())) * Math.abs(Math.round($(this).attr('p')));
                        $sum.html('' + sum);
                    }
                });
                // *****
                $f.find('#quickOrderSend span.button').click(function () {
                    $f.find('form').submit();
                    return false;
                });

                var $tel = $f.find('[cname=quickOrderPhone]');
                var $name = $f.find('[cname=quickOrderName]');

                $f.find('form').submit(function () {
                    // Проверяем телефон
                    var phone_number = $tel.val().match(/^(\+?)?([\d\s\+\-\(\)]+)$/i);
                    if (isDefined(phone_number) && phone_number != null && phone_number.length > 0) {
                        var striped_number = phone_number[2].replace(/[|\s&;$%@\-"<>()+,]/g, '');
                        if (striped_number.length < 10 || striped_number.length > 12) {
                            $tel.css({'border-color': 'red'});
                            return false;
                        }
                    }
                    else {
                        $tel.css({'border-color': 'red'});
                        return false;
                    }
                    if ($name.val().length < 2){
                        $name.css({'border-color': 'red'});
                        return false;
                    }
                    //!**** Отправляем заказ
                    $.ajax({
                        url: '/ax/quickOrderSend',
                        dataType: "json",
                        data: {
                            f: $(this).serialize()
                        },
                        success: function (r) {
                            if (r.fres) {
                                $f.html(r.html);
                            }
                            else return emsg(r);
                        }
                    });
                    return false;
                });
            }
            // ****** Быстрый заказ END ******
        });
        return false;
    });

    CMP.initEvents();
	
}

function addToCart(cat_id, am)
{

    var arDop = getAccessories($('.accessories-box-main'));

    $.ajax({
        url: '/cart/add',
        data: {
            cat_id: cat_id,
            amount: am,
            dop: arDop
        },
        success: function (d)
        {
			console.log(d);
            if (d.fres) {
                if (!isDefined(d.exist)) {
                    $.confirm({
                        title: 'Товар в корзине',
                        message: (d.img1!=''?('<img src="'+ d.img1+'" class="fl-l">'):'') + '<p>' + d.fn + '</p>' +
                        '<p>Вы можете остаться на этой странице или перейти в корзину для оформления заказа.</p>' +
                        (d.accessoriesCheckboxes != '' ? ('<div class="accessories-input-box">' + d.accessoriesCheckboxes + '</div>') : ''),
                        buttons: {
                            'Оформить заказ': {
                                action: function ()
                                {
                                    location.href = '/cart.html';
                                }
                            },
                            'Продолжить покупки': {
                                class: 'button-grey'
                            }
                        }
                    });
                    /*================================================================================================*/
                    if($('.box-logo .basket i:first, .mobile-header__basket .basket i:first').length == 0) // Добавляем необходимое, если корзина была пуста
                    {
                        $('.empty_basket_title').remove();
                        $('.basket').prepend('<i>0</i><p><a href="/cart.html">Корзина:</a><b>0 руб.</b></p><a href="/cart.html" class="buy" title="Перейти к оформлению заказа">Оформить<i></i></a>');
						$('.mobile-header__panel').addClass('mobile-header__panel_type_full');
                    }
                    /*================================================================================================*/
                    $('.box-logo .basket i:first, .mobile-header__basket .basket i:first').html(d.b_count);
                    $('.box-logo .basket b, .mobile-header__basket b').html(d.bsum);
                    if (typeof _gaq != 'undefined') {
                        if (d.gr == 1)    _gaq.push(['_trackEvent', 'Cart', 'addTyre', d.fn_, d.price * 1]);
                        if (d.gr == 2)    _gaq.push(['_trackEvent', 'Cart', 'addDisk', d.fn_, d.price * 1]);
                    }
                    if (typeof window['yaCounter' + YAM] != 'undefined') {
                        window['yaCounter' + YAM].reachGoal("ADD-TO-CART");
                    }
                } else {
                    $.confirm({
                        title: 'Товар уже в корзине',
                        message: (d.img1 != '' ? ('<img src="' + d.img1 + '" class="fl-l">') : '') + '<p>' + d.fn + '</p>' +
                        '<p>Вы можете остаться на этой странице или перейти в корзину для оформления заказа.</p>' +
                        (d.accessoriesCheckboxes != '' ? ('<div class="accessories-input-box">' + d.accessoriesCheckboxes + '</div>') : ''),
                        buttons: {
                            'Оформить заказ': {
                                action: function () {
                                    location.href = '/cart.html';
                                }
                            },
                            'Продолжить покупки': {
                                class: 'button-grey'
                            }
                        }
                    });
                }
            } else {
                if (typeof _gaq != 'undefined') {
                    _gaq.push(['_trackEvent', 'Interface', 'JSError:btn-buy', d.err_msg, undefined, true]);
                }
                return emsg(d);
            }
        }
    })

}

function cartChangeAmount(cat_id, am)
{
    if ((am * 1) > 0) {
        $.ajax({
            url: '/cart/changeAmount',
            type: 'GET',
            data: {amount: am, cat_id: cat_id},
            success: function (d)
            {
                $('#sum_' + cat_id).text(d.itemSum);
                $('#cartSum').text(d.summa);
                $('#cartItog').text(d.itog);
                $('#cartDelivery').text(d.dcost);
                // Меняем верхнюю корзину
                $('.box-logo .basket p b').text(d.summa);
            }
        });
    }

}

/**
 * Формирует массивы выбранных аксессуаров товара расположенных на странице
 * @param accessoriesBox - контекст выбора аксессуаров
 * @returns {string}
 */
function getAccessories(accessoriesBox) {
    var arDop = {};

    $(accessoriesBox).find('.accessories-box__item-checkbox').each(function (i, element) {
        if ($(this).prop("checked")) {
            var catID =  $(this).closest('.accessories-box').data('cat_id');

            if (!isObject(arDop[catID])) {
                arDop[catID] = {};
            }

            arDop[catID][$(this).data('id')] = {
                'acc_id': $(this).data('id'),
                'name': $(this).data('name'),
                'price': $(this).val(),
                'amount': 1,
                'sum': $(this).val()
            };
        }
    });

    return JSON.stringify(arDop);
}

/**
 * Обновляет цены в зависимости от выбранных аксессуаров
 * @param dop - массив аксессуаров (допов)
 * @param cat_id - ID элемента карзины
 */
function updateDopList(dop, cat_id) {
    $.ajax({
        url: '/cart/updateDopList',
        type: 'POST',
        data: {dop: dop, cat_id: cat_id},
        success: function (d) {
            $('#sum_' + cat_id).text(d.itemSum);
            $('#cartSum').text(d.summa);
            $('#cartItog').text(d.itog);
            $('#cartDelivery').text(d.dcost);
            // Меняем верхнюю корзину
            $('.box-logo .basket p b').text(d.summa);
        }
    });
}

function clearCart(e)
{
    $.ajax({
        url: '/cart/clear',
        type: 'GET',
        success: function (d)
        {
            if (d.fres) {
                msg('Корзина очищена.');
                location.href = '/';
            } else emsg(d);
        }
    });

}

function orderSend(e)
{
    $(e.target).attr('disabled', 'disabled');
    var self = e.target;

    if (typeof _gaq != 'undefined') {
        _gaq.push(['_trackEvent', 'Cart', 'clickSendButton', 'clickSendButton;']);
    }

    $('#cart_frm div').removeClass('uncorrect');

    $.ajax({
        url: '/cart/send',
        type: 'POST',
        data: {f: $('#cart_frm form').serialize()},
        success: function (r)
        {
            if (r.fres) {
                $('.main-cart').html('<div class="ctext">' + r.html + '</div>');
                $('#cart_frm').slideUp(200);
                $.scrollTo(0, 1000);
				
				if($('#wrapper').length) {
					$('html, body').animate({
						scrollTop: $('#wrapper').offset().top
					}, 1000);
				}
				
                var d = new Date();
                var dt = Math.round((d.getTime() - window.TSinited) / 1000);

                if (typeof window['yaCounter' + YAM] != 'undefined') {
                    window['yaCounter' + YAM].reachGoal("ORDER-SEND");
                }

                if (typeof _gaq != 'undefined') {
                    _gaq.push(['_trackPageview', '/OrderSend.event']);
                    _gaq.push(['_trackEvent', 'Cart', 'OrderSend', dt + '', undefined, true]);
                    if (r.GA_trans !== false) {

                        _gaq.push([
                            '_setCustomVar',
                            // This custom var is set to slot #1.  Required parameter.
                            r.GA_trans['GA_customVarsSlot'],
                            // The name acts as a kind of category for the user activity.  Required parameter.
                            'customerPType',
                            // This value of the custom variable.  Required parameter.
                            r.GA_trans['customerPType'],
                            // Sets the scope to session-level.  Optional parameter. The scope is also a number. A value of 1 indicates a visitor scoped custom variable, a value of 2 indicates a visit level custom variable, and a value of 3 indicates a page level custom variable.
                            2
                        ]);

                        _gaq.push([
                            '_addTrans',
                            r.GA_trans['transId'],
                            '',
                            r.GA_trans['total'],
                            '',
                            r.GA_trans['shipping'],
                            r.GA_trans['city'],
                            '',
                            r.GA_trans['country']
                        ]);

                        for (var k in r.GA_trans['items']) {
                            var item = [
                                '_addItem',
                                r.GA_trans['transId'],
                                r.GA_trans['items'][k]['SKU'],
                                r.GA_trans['items'][k]['name'],
                                r.GA_trans['items'][k]['category'],
                                r.GA_trans['items'][k]['price'],
                                r.GA_trans['items'][k]['quantity']
                            ]
                            _gaq.push(item);
                        }
                        _gaq.push(['_trackTrans']);
                    } else {
                        _gaq.push(['_trackEvent', r.GA_transErr[0], r.GA_transErr[1], r.GA_transErr[2]]);
                    }
                }
            } else {
                if (r.err_msg != '') emsg(r);
                else {
                    if (isDefined(r.eid)) {
                        $.scrollTo('#' + r.eid, 1000);
                        $('#' + r.eid).addClass('uncorrect');
                    }
                }
            }
        },
        complete: function ()
        {
            $(self).removeAttr('disabled');
        }
    });

}

function cartDel(e)
{
    var cat_id = $(e.target).attr('cat_id');
    var td = $(e.target).parent();
    var tr = $(e.target).parent().parent();
    tr.children('td').css('background', '#F00');
    $(e.target).attr('disabled', 'disabled');
    var self = $(this);
    $.ajax({
        url: '/cart/del',
        type: 'GET',
        data: {cat_id: $(e.target).attr('cat_id')},
        success: function (d)
        {
            if (d.fres) {
                $('#cartSum').text(d.summa);
                $('#cartDelivery').text(d.dcost);
                $('#cartItog').text(d.itog);
                // Меняем верхнюю корзину
                $('.box-logo .basket p b').text(d.summa);
                $('.box-logo .basket > i').text(d.count);
                tr.fadeOut('slow', function ()
                {
                    tr.remove();
                    if ($(".cart_tbl tr").length == 0) {
                        $('.box-logo .basket').html('');
                        $('.basket').prepend('<div class="empty_basket_title">Корзина<br> пуста</div>');
                    }
                });
            } else {
                emsg(d);
                tr.children('td').css('background', 'white');

            }
        }
    });

}

function checkLimit($input, $target, minQty, maxQty)
{
    if (($input.val() * 1) > maxQty) {
        $input.val(maxQty);
        $input.qtip({
            content: 'Слишком много. Такого количества нет на складе',
            show: true,
            hide: 'mouseout',
            position: {
                my: 'top left',  // Position my top left...
                at: 'bottom right', // at the bottom right of...
                target: $target // my target
            },
            style: {
                classes: 'qtip-shadow qtip-rounded'
            }
        });
    } else if (($input.val() * 1) < minQty) {
        $input.val(minQty);
        $input.qtip({
            content: 'К сожалению, этот товар продается от ' + minQty + ' штук',
            show: true,
            hide: 'mouseout',
            position: {
                my: 'top right',  // Position my top left...
                at: 'bottom left', // at the bottom right of...
                target: $target // my target
            },
            style: {
                classes: 'qtip-shadow qtip-rounded'
            }
        });
    } else {
        $input.qtip('disable');
    }
}