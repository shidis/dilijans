/*
	QMark Lib
*/


/*Browser detection patch*/
jQuery.browser = {};
jQuery.browser.mozilla = /mozilla/.test(navigator.userAgent.toLowerCase()) && !/webkit/.test(navigator.userAgent.toLowerCase());
jQuery.browser.webkit = /webkit/.test(navigator.userAgent.toLowerCase());
jQuery.browser.opera = /opera/.test(navigator.userAgent.toLowerCase());
jQuery.browser.msie = /msie/.test(navigator.userAgent.toLowerCase());

(function($) {
	
jQuery.fn.logit=function (msg){
	if (($.browser.webkit || $.browser.mozilla) && typeof(console)!='undefined') console.log(msg);
}

jQuery.fn.randomInt=function (min, max)
{
  return Math.floor(Math.random() * (max - min + 1)) + min;
}

jQuery.fn.delCookie=function (cookieName)
{
 var expDate=new Date();
 expDate.setTime(expDate.getTime()-1000);
 var expires=expDate.toGMTString();
 document.cookie=cookieName+"=; path="+escape('/')+"; expires="+expires;
}

jQuery.fn.setCookie=function (cookieName, cookieContent, expires){
	var expDate=new Date();
	expDate.setTime(expDate.getTime()+8640000000000);
	if(!isNaN(expires)) expires=expires=='long'?expDate.toGMTString():(expires=='session'?'':expires);
		else var expires=expDate.toGMTString();
	document.cookie=cookieName+"="+escape(cookieContent)+"; path="+escape('/')+(expires!=''?("; expires="+expires):'');
}

jQuery.fn.getCookie=function (name){
	var cname = name + "=";               
	var dc = document.cookie;             
	if (dc.length > 0) { 
		begin = dc.indexOf(cname);       
		if (begin != -1) { 
			begin += cname.length;
			end = dc.indexOf(";", begin);
			if (end == -1) end = dc.length;
			return unescape(dc.substring(begin, end));
		} 
	}
	return null;
}



})(jQuery);



