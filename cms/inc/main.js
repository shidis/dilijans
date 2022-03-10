// JavaScript Document

function SelectAll(mark,f) { 
  for (i = 0; i < document.forms[f].elements.length; i++)
     {
         var item = document.forms[f].elements[i];
	     if (item.id == "cc")  {
		     item.checked = mark;
		 };
	 }
}


function del_cascade(){
	return(confirm('Подтвердите удаление. Вы хорошо подумали?'));
}

function openwin(url,wname){
	var w=screen.width;
	var h=screen.height-120;
	switch (wname) {
		case 'catalog':w=w-30;h-80;l=15;t=0; break;
		case 'sup':w=330;h=400;l=200;t=150; break;
		case 'export_xml':w=330;h=400;l=200;t=150; break;
		case 'extra':w=600;h=500;l=200;t=50; break;
		default: w=400;h=400;l=200;t=200; break;
	}
//	var wn=wname+Math.round(Math.random()*1000);
	var wn=wname;
	w=window.open(url,wn,'top='+t+',left='+l+', width='+w+', height='+h+',directories=no,status=yes,scrollbars=yes, resizable=yes, resize=yes,menubar=no, titlebar=no,toolbar=no');
	return false;
}

function fsubmit(f)
{
		document.forms[f].submit();
}

function setCookie (cookieName, cookieContent)
{
 var expDate=new Date();
 expDate.setTime(expDate.getTime()+8640000000000);
 var expires=expDate.toGMTString();
 document.cookie=cookieName+"="+escape(cookieContent)+"; path="+escape('/')+"; expires="+expires;
}

function toggle(v)
{
	var d=v;
	if (document.getElementById(d).style.display == 'none') {
		document.getElementById(d).style.display = 'block';
		setCookie(v,'0');
	}
	else if (document.getElementById(d).style.display == 'block') {
		document.getElementById(d).style.display = 'none';
		setCookie(v,'1');
	}
}
