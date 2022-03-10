function openwin(url,wname){
	var w=screen.width;
	var h=screen.height-120;
	switch (wname) {
		case 'catalog':w=w-30;h=h-80;l=15;t=0; break;
		case 'sup':w=330;h=400;l=200;t=150; break;
		case 'export_xml':w=330;h=400;l=200;t=150; break;
		case 'extra':w=w-60;h=700;l=15;t=20; break;
		case 'dop':w=w-200;h=500;l=150;t=0; break;
		case 'import': w=w-100;h=h-50;l=50;t=50; break;
		default: w=550;h=500;l=150;t=150; break;
	}
//	var wn=wname+Math.round(Math.random()*1000);
	var wn=wname;
	w=window.open(url,wn,'top='+t+',left='+l+', width='+w+', height='+h+',directories=no,status=yes,scrollbars=yes, resizable=yes, resize=yes,menubar=no, titlebar=no,toolbar=no');
	return false;
}


function logit(msg){
	if (($.browser.webkit || $.browser.mozilla) && typeof(console)!='undefined') console.log(msg);
}


var loading1='<div id="loading1" style="display:none;"><div id="_loading1"><img src="/assets/images/ax/1.gif" width="16" height="16"></div></div>';
var loading2='<img id=loading2 src="/assets/images/ax/1.gif" width="16" height="16" align=absmiddle>';

function note(text,mode){
	switch (mode){
		case 'error':$.jGrowl(text, { sticky: true, header:'<font style="color:#ff00">ОШИБКА!</font>' }); break;
		case 'stick':$.jGrowl(text, { sticky: true, header:'Информация' }); break;
		case 'long':$.jGrowl(text, { life: 10000 }); break;
		default:$.jGrowl(text);
	}
}

function fresMsg(msg){
	var r='';
	if(isArray(msg))
		for(i=0;i<msg.length;i++)
			r=r+'<br>'+msg[i];
	else r=msg;
	r='<pre>'+r+'</pre>';
	return r;
}

function Err (XMLHttpRequest, textStatus, errorThrown){
	overlay(0);
	if($('#errorDlg').length) $('#errorDlg').html(fresMsg('ajax error: '+textStatus+"<br>************************<br>"+XMLHttpRequest.responseText)).dialog('open');
	else alert('ajax error: '+textStatus+"\r\n"+XMLHttpRequest.responseText);
}

function err(msg){
	if($('#errorDlg').length) $('#errorDlg').html(fresMsg(msg)).dialog('open');
	else alert(msg);
}


function ajaxComplete(XMLHttpRequest, textStatus){
	overlay(0);
}

function loadComplete(data){
	overlay(0);
	if(typeof data.fres!='undefined' && !data.fres) note('ERROR: '+data.fres_msg,'error');
}

function ajaxBeforeSend(XMLHttpRequest){
	overlay(1);
}

efoo=function (){}

function ui_highlight(id,msg){
$(id).append('<div class="ui-widget"><div class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0 .7em;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>'+msg+'</p></div></div>');
}

function ui_error(id,msg){
$(id).append('<div class="ui-widget"><div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"><p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>'+msg+'</p></div></div>');
}

function ui_button(append_id,button_id,href,anc){
$(append_id).append('<a href="'+href+'" id="'+button_id+'" class="ui-state-default ui-corner-all ui-a">'+anc+'</a>');
}

function overlay(on){
	if(on) $('#overlay').css({'opacity':0.5,'height':'100%','width':'100%','left':0,'top':0,'z-index':1110,'background-color':'#ffffff','position':'fixed'}).fadeIn('fast');
	else $('#overlay').fadeOut('fast');
}

