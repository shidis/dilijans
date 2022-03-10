$(document).ready(function() {
    $('a.remindPass').click(function(e) {
        e.preventDefault();
        GoTo(2);
    });
    $('a.signin').click(function(e) {
        e.preventDefault();
        GoTo(1);
    });
    $('a.call-shaman').click(function(e) {
        e.preventDefault();
        GoTo(0);
    });
	
	$('#signin-form').submit(SignIn);
	
});



function GoTo(num) {
    margin = num * 382;
    $('#wr').animate({ marginLeft: -margin },
     { duration: 800, easing: 'easeInOutBack' });
}

// --------
//  ERRORS
// --------

var er1 = "Логин и пароль не должны быть пустыми"; //0
var er3 = "Не угадали пароль. Или логин. Попробуйте еще раз"; //1
var er4 = "Пользователя с таким email'oм у нас еще нету"; //2
var er5 = "Неправильный формат email'a"; //2

// ----------
//  MESSAGES
// ----------

var m0 = "Письмо с паролем было послано.";

function SignIn(e) {
	e.preventDefault();
    pw = $('#pw').val();
    login = $('#login').val();
	if(login=='' || pw=='')  {
		e.preventDefault();
		ShowError(1);
	}else {
		$('#signin-but').attr('disabled','disabled');
		$.ajax({
			type:'POST',
			cache:false,
			dataType: 'json',
			url: '/cms/be/login.php',
			error: function(XMLHttpRequest, textStatus, errorThrown){
				e.preventDefault();
				alert('ajx ERROR: '+textStatus,'error');
			},
			data: {'login':login,'pw':pw},
			complete: function(){
				$('#signin-but').removeAttr('disabled');
			},
			success: function(r){
				if(r.pass==1) {
					location.href=location.href;
				}else {
						e.preventDefault();
						ShowError(3);
					}
			}
		});
    }
}

function RemindPassword() {
    var email = $('#remindEmail').val();
    if (!ValidEmail(email)) {
        ShowError(5);
    } else {

        if (SendRemind()) {
            ShowMessage(0);
        }
        else {
            ShowError(4);
        }
    }
}

function Err (XMLHttpRequest, textStatus, errorThrown){
	alert('ajx ERROR: '+textStatus,'error');
}


function SendRemind(email) {

    //check if email exists, send email
    //return true if it goes okay, false otherwise

    return true;
}

function HideError() {
    $('.error').hide();
}
function ShowError(code) {
    HideError();
    switch (code) {
        case 1:
            $('#error1').html(er1).slideDown();
            break;
        case 3:
            $('#error1').html(er3).slideDown();
            break;
        case 4:
            $('#error2').html(er4).slideDown();
            break;
        case 5:
            $('#error2').html(er5).slideDown();
            break;
    }
}



function ShowMessage(code) {
    HideError();
    $('.message').fadeOut();
    switch (code) {
        case 0:
            $('#message0').html(m0).slideDown();
            break;
    }
}

function ValidEmail(email) {
    var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
    return reg.test(email)
}


