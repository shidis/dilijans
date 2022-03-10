$(document).ready(function(){

    $.ajaxSetup({
        type:'POST',
        cache:false,
        dataType: 'json',
        error: Err,
        url: 'be/sconfig.php'
    });

    var loader=$('.workspace').cloader();

    $('#ntabs').tabs();

    $(document).ajaxStart(
        function() {
            loader.cloader('show');
        })
        .ajaxStop(function() {
            loader.cloader('hide');
        });

    var ecfg = {
        url: 'be/sconfig.php',
        type: 'text',
        emptytext: 'задать',
        mode: 'popup',
        placement: 'right',
        inputclass: 'editable-popup1',
        success: function(r, newVal){
            if(r.fres) return {newValue:newVal}; else {
                note(r.fres_msg,'error');
                return 'ошибка';
            }
        }
    };

    $('#users td').each(function(){

        var userID=$(this).parent().attr('userId');

        switch($(this).attr('class')){
            case 'role':
                $(this).editable($.extend({}, ecfg, {name: 'role', pk: userID, title: 'Новый уровень доступа'}));
                break;
            case 'login':
                $(this).editable($.extend({}, ecfg, {name: 'login', pk: userID, title: 'Новый логин'}));
                break;
            case 'firstName':
                $(this).editable($.extend({}, ecfg, {name: 'firstName', pk: userID, title: 'Новое имя'}));
                break;
            case 'lastName':
                $(this).editable($.extend({}, ecfg, {name: 'lastName', pk: userID, title: 'Новая фамилия'}));
                break;
            case 'cmsStartUrl':
                $(this).editable($.extend({}, ecfg, {name: 'cmsStartUrl', pk: userID, title: 'Стартовый урл в админке'}));
                break;
            case 'email':
                $(this).editable($.extend({}, ecfg, {name: 'email', pk: userID, title: 'Новый емейл'}));
                break;
            case 'skype':
                $(this).editable($.extend({}, ecfg, {name: 'skype', pk: userID, title: 'Новый скайп'}));
                break;
            case 'icq':
                $(this).editable($.extend({}, ecfg, {name: 'icq', pk: userID, title: 'Новая ICQ'}));
                break;
            case 'lifeTime':
                $(this).editable($.extend({}, ecfg, {name: 'lifeTime', pk: userID, title: 'Время жизни сессии'}));
                break;
            case 'os':
                $(this).editable($.extend({}, ecfg, {name: 'os', pk: userID, source:
                    [
                        {value:0, text: 'запрещено'},
                        {value:1, text: 'разрешено'}
                    ]
                    , type: 'select', title: 'Работа с заказами'}));
                break;
        }
    });

    $('#users .pw').click(function()
    {
        var userID=$(this).parent().parent().attr('userId');
        var newpw=window.prompt('Сессии и токен сброшены не будут. Новый пароль:');
        if(newpw!=null)
            $.ajax({
                data:{
                    name: 'pw',
                    userId: userID,
                    q: newpw
                },
                success: function(r){
                    if(r.fres){
                        note('Пароль изменен');
                    }else err(r.fres_msg);
                }
            });
        return false;
    });

    $('#users .disabled-sw').click(function(e)
    {
        var userID=$(this).parent().parent().attr('userId');
        $.ajax({
            data:{
                name: 'disabledSwitch',
                userId: userID
            },
            success: function(r){
                if(r.fres!==false){
                    $(e.target).html(r.fres==1?'выключен':'включен');
                }else err(r.fres_msg);
            }
        });
        return false;
    });

    $('.token-reset').button().click(function(e){
        if(confirm('Сброс токена НЕ приведет к разлогиниванию пользователя во всех сессиях. Сбросить?'))
            $.ajax({
                data:{
                    name: 'resetToken',
                    userId: $(this).parent().parent().attr('userId')
                },
                success: function (r)
                {
                    if(r.fres){
                        note('Токен изменен');
                    }else err(r.fres_msg);
                }
            });
        return false;
    });

    $('.logout-all').button().click(function(e){
        e.preventDefault();
        if(confirm('Разлогиниться во всех сессиях?'))
            $.ajax({
                data:{
                    name: 'logoutAll',
                    userId: $(this).parent().parent().attr('userId')
                },
                success: function (r)
                {
                    if(r.fres){
                        location.reload();
                    }else err(r.fres_msg);
                }
            });

    });

    $('.user-del').button().click(function(e){
        if(confirm('Точно?'))
            $.ajax({
                data:{
                    name: 'userDel',
                    userId: $(this).parent().parent().attr('userId')
                },
                success: function (r)
                {
                    if(r.fres){
                        note('Пользователь удален');
                        location.reload();
                    }else err(r.fres_msg);
                }
            });
        return false;
    });

    $('#add-user').button().click(function(e){
        e.preventDefault();
        $.ajax({
            data:{
                name: 'userAdd',
                frm: $('#new-user form').serialize()
            },
            success: function (r)
            {
                if(r.fres){
                    note('Пользователь добавлен');
                    location.reload();
                }else err(r.fres_msg);
            }
        });

    });

    $('#add-driver').button().click(function(e){
        e.preventDefault();
        $.ajax({
            data:{
                name: 'driverAdd',
                frm: $('#new-driver form').serialize()
            },
            success: function (r)
            {
                if(r.fres){
                    note('Водитель добавлен');
                    location.reload();
                }else err(r.fres_msg);
            }
        });

    });






});