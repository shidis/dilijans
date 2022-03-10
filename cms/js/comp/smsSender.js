var loader;
var axActive=false;
$(document).ready(function(){

    $.ajaxSetup({
        type:'POST',
        cache:false,
        dataType: 'json',
        error: Err,
        url: '../be/smsSender.php'
    });

    window.loader=$('.workspace').cloader();

    window.axActive = false;
    $(document)
        .ajaxStart(function ()
        {
            window.axActive = true;
        })
        .ajaxStop(function ()
        {
            window.axActive = false;
        });

    getBalance();

    $('[name=dest]')
        .mask("(999) 999-99-99");

    $('#send')
        .button()
        .click(sendSMS);

    $('[name=msg]').keyup(function(){
        $('.len span').html($(this).val().length);
    })

    log('Готов к отправке сообщений');

});

function getBalance()
{
    $('#balance').html('<img class="loader" src="/assets/images/ax/1.gif">');
    $.ajax({
        data:{
            act: 'balance'
        },
        complete: function()
        {
            loader.cloader('hide');
        },
        success: function(r)
        {
            if(r.fres){
                $('#balance').html(r.balance);
            }else{
                $('#balance').html('-');
                err(r.fres_msg);
            }
        }
    })
}

function sendSMS()
{
    loader.cloader('show');
    $.ajax({
        global: false,
        data:{
            act:'send',
            f: $('.left form').serialize()
        },
        complete: function()
        {
            loader.cloader('hide');
        },
        success: function(r)
        {
            if(r.fres){
                log(r.fres_msg);
                if(r.status){
                    new function(msgId){
                        var pingResponse=setInterval(function()
                        {
                            if(!window.axActive)
                                $.ajax({
                                    data:{
                                        act: 'pingResponse',
                                        msgId: msgId
                                    },
                                    success: function(r)
                                    {
                                        if(r.fres){
                                            if(r.status==2){
                                                log(r.fres_msg);
                                                clearInterval(pingResponse);
                                                getBalance();
                                            }
                                        }else {
                                            err(r.fres_msg);
                                            clearInterval(pingResponse);
                                        }
                                    }
                                });
                        },1000);
                    }(r.msgId);
                }
            } else err(r.fres_msg);
        }
    });
    return false;
}

function log(msg)
{
    var d=new Date();
    $('#log').append('\n\n['+ twoDigits(d.getHours())+':'+twoDigits(d.getMinutes())+':'+twoDigits(d.getSeconds())+'] '+msg);
}

