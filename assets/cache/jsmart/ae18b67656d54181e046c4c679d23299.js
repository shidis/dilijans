function urldecode(str){return decodeURIComponent((str+'')
.replace(/%(?![\da-f]{2})/gi,function(){return'%25';})
.replace(/\+/g,'%20'));}
function getRandom(min,max)
{return Math.floor(Math.random()*(max-min+1))+min;}
function setCookie(cookieName,cookieContent,ses){if(isNaN(ses)){var expDate=new Date();expDate.setTime(expDate.getTime()+8640000000);var expires=expDate.toGMTString();}else var expires='';document.cookie=cookieName+"="+escape(cookieContent)+"; path="+escape('/')+"; expires="+expires+"; domain="+'.'+window.location.hostname.match(/(www.|)(.*)/)[2];}
function delCookie(cookieName){var expDate=new Date();expDate.setTime(expDate.getTime()-1000);var expires=expDate.toGMTString();document.cookie=cookieName+"=; path="+escape('/')+"; expires="+expires+"; domain="+'.'+window.location.hostname.match(/(www.|)(.*)/)[2];}
function getCookie(name){var cname=name+"=";var dc=document.cookie;if(dc.length>0)
{begin=dc.indexOf(cname);if(begin!=-1)
{begin+=cname.length;end=dc.indexOf(";",begin);if(end==-1)end=dc.length;return unescape(dc.substring(begin,end));}}
return null;}
function parseQuery(qstr)
{var query={};var a=qstr.split('&');for(var i in a){var b=a[i].split('=');query[decodeURIComponent(b[0])]=decodeURIComponent(b[1]);}
return query;}
function mergeFormWithCookie(cookieName,$form)
{var c=getCookie(cookieName);if(c===null||c.indexOf('%')!=-1)c='';if(c!='')c=parseQuery(Base64.decode(c));else c={};$form.find('input,select').each(function()
{if($(this).is('[name]')){if(this.type=='checkbox'){if($(this).prop('checked'))c[$(this).attr('name')]=$(this).val();else delete c[$(this).attr('name')];}
else c[$(this).attr('name')]=$(this).val();}});setCookie(cookieName,c=Base64.encode($.param(c)));}
function Err(XMLHttpRequest,textStatus,errorThrown){if(ax_err_show)
if(MD||true)window.alert('Ошибка при загрузке данных ('+textStatus+")\r\n"+XMLHttpRequest.responseText+"\r\nПопробуйте обновить страницу и повторить запрос");else Boxy.alert('<p>Ошибка при загрузке данных ('+textStatus+')<br>'+XMLHttpRequest.responseText+'</p> <p>Попробуйте обновить страницу и повторить запрос</p>',null,{title:'ОШИБКА!'});}
function _alert(msg){if(ax_err_show)window.alert(msg);}
function msg(msg){if(MD||true)window.alert(strip_tags(msg));else Boxy.alert('<div style="text-align:center">'+msg+'</div>',null,{title:'Информация'});}
function emsg(d){if(MD||true)window.alert("(!)\r\n"+strip_tags(d.err_msg));else Boxy.alert('<p>'+d.err_msg+'</p>',null,{title:'ОШИБКА!'});}
function logit(msg){if(($.browser.webkit||$.browser.mozilla)&&typeof(console)!='undefined')console.log(msg);}
function strip_tags(str){return str.replace(/<\/?[^>]+>/gi,'');}
function isArray(v){return typeof(v)=='object'&&typeof(v.length)=='number';}
function isObject(v){return v&&typeof v=="object";}
function isFunction(v){return toString.apply(v)==='[object Function]';}
function isNumber(v){return typeof v==='number';}
function isString(v){return typeof v==='string';}
function isBoolean(v){return typeof v==='boolean';}
function isDefined(v){return typeof v!=='undefined';}
function isEmpty(v,allowBlank){return v===null||v===undefined||((isArray(v)&&!v.length))||(!allowBlank?v==='':false);}
function isPrimitive(v){return isString(v)||isNumber(v)||isBoolean(v);}
function serialize(mixed_value){var val,key,okey,ktype='',vals='',count=0,_utf8Size=function(str){var size=0,i=0,l=str.length,code='';for(i=0;i<l;i++){code=str.charCodeAt(i);if(code<0x0080){size+=1;}
else if(code<0x0800){size+=2;}
else{size+=3;}}
return size;},_getType=function(inp){var match,key,cons,types,type=typeof inp;if(type==='object'&&!inp){return'null';}
if(type==='object'){if(!inp.constructor){return'object';}
cons=inp.constructor.toString();match=cons.match(/(\w+)\(/);if(match){cons=match[1].toLowerCase();}
types=['boolean','number','string','array'];for(key in types){if(cons==types[key]){type=types[key];break;}}}
return type;},type=_getType(mixed_value);switch(type){case'function':val='';break;case'boolean':val='b:'+(mixed_value?'1':'0');break;case'number':val=(Math.round(mixed_value)==mixed_value?'i':'d')+':'+mixed_value;break;case'string':val='s:'+_utf8Size(mixed_value)+':"'+mixed_value+'"';break;case'array':case'object':val='a';for(key in mixed_value){if(mixed_value.hasOwnProperty(key)){ktype=_getType(mixed_value[key]);if(ktype==='function'){continue;}
okey=(key.match(/^[0-9]+$/)?parseInt(key,10):key);vals+=this.serialize(okey)+this.serialize(mixed_value[key]);count++;}}
val+=':'+count+':{'+vals+'}';break;case'undefined':default:val='N';break;}
if(type!=='object'&&type!=='array'){val+=';';}
return val;}
function unserialize(data){var that=this;var utf8Overhead=function(chr){var code=chr.charCodeAt(0);if(code<0x0080){return 0;}
if(code<0x0800){return 1;}
return 2;};var error=function(type,msg,filename,line){throw new that.window[type](msg,filename,line);};var read_until=function(data,offset,stopchr){var buf=[];var chr=data.slice(offset,offset+1);var i=2;while(chr!=stopchr){if((i+offset)>data.length){error('Error','Invalid');}
buf.push(chr);chr=data.slice(offset+(i-1),offset+i);i+=1;}
return[buf.length,buf.join('')];};var read_chrs=function(data,offset,length){var buf;buf=[];for(var i=0;i<length;i++){var chr=data.slice(offset+(i-1),offset+i);buf.push(chr);length-=utf8Overhead(chr);}
return[buf.length,buf.join('')];};var _unserialize=function(data,offset){var readdata;var readData;var chrs=0;var ccount;var stringlength;var keyandchrs;var keys;if(!offset){offset=0;}
var dtype=(data.slice(offset,offset+1)).toLowerCase();var dataoffset=offset+2;var typeconvert=function(x){return x;};switch(dtype){case'i':typeconvert=function(x){return parseInt(x,10);};readData=read_until(data,dataoffset,';');chrs=readData[0];readdata=readData[1];dataoffset+=chrs+1;break;case'b':typeconvert=function(x){return parseInt(x,10)!==0;};readData=read_until(data,dataoffset,';');chrs=readData[0];readdata=readData[1];dataoffset+=chrs+1;break;case'd':typeconvert=function(x){return parseFloat(x);};readData=read_until(data,dataoffset,';');chrs=readData[0];readdata=readData[1];dataoffset+=chrs+1;break;case'n':readdata=null;break;case's':ccount=read_until(data,dataoffset,':');chrs=ccount[0];stringlength=ccount[1];dataoffset+=chrs+2;readData=read_chrs(data,dataoffset+1,parseInt(stringlength,10));chrs=readData[0];readdata=readData[1];dataoffset+=chrs+2;if(chrs!=parseInt(stringlength,10)&&chrs!=readdata.length){error('SyntaxError','String length mismatch');}
readdata=that.utf8_decode(readdata);break;case'a':readdata={};keyandchrs=read_until(data,dataoffset,':');chrs=keyandchrs[0];keys=keyandchrs[1];dataoffset+=chrs+2;for(var i=0;i<parseInt(keys,10);i++){var kprops=_unserialize(data,dataoffset);var kchrs=kprops[1];var key=kprops[2];dataoffset+=kchrs;var vprops=_unserialize(data,dataoffset);var vchrs=vprops[1];var value=vprops[2];dataoffset+=vchrs;readdata[key]=value;}
dataoffset+=1;break;default:error('SyntaxError','Unknown / Unhandled data type(s): '+dtype);break;}
return[dtype,dataoffset-offset,typeconvert(readdata)];};return _unserialize((data+''),0)[2];}
function utf8_decode(str_data){str_data+='';var i=0;var tmp_arr=[];ac=0;while(i<str_data.length){c1=str_data.charCodeAt(i);if(c1<128){tmp_arr[ac++]=String.fromCharCode(c1);i++;}else if((c1>191)&&(c1<224)){c2=str_data.charCodeAt(i+1);tmp_arr[ac++]=String.fromCharCode(((c1&31)<<6)|(c2&63));i+=2;}else{c2=str_data.charCodeAt(i+1);c3=str_data.charCodeAt(i+2);tmp_arr[ac++]=String.fromCharCode(((c1&15)<<12)|((c2&63)<<6)|(c3&63));i+=3;}}
return tmp_arr.join('');}
var Base64={_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",encode:function(input){var output="";var chr1,chr2,chr3,enc1,enc2,enc3,enc4;var i=0;input=Base64._utf8_encode(input);while(i<input.length){chr1=input.charCodeAt(i++);chr2=input.charCodeAt(i++);chr3=input.charCodeAt(i++);enc1=chr1>>2;enc2=((chr1&3)<<4)|(chr2>>4);enc3=((chr2&15)<<2)|(chr3>>6);enc4=chr3&63;if(isNaN(chr2)){enc3=enc4=64;}else if(isNaN(chr3)){enc4=64;}
output=output+this._keyStr.charAt(enc1)+this._keyStr.charAt(enc2)+this._keyStr.charAt(enc3)+this._keyStr.charAt(enc4);}
return output;},decode:function(input){var output="";var chr1,chr2,chr3;var enc1,enc2,enc3,enc4;var i=0;input=input.replace(/[^A-Za-z0-9\+\/\=]/g,"");while(i<input.length){enc1=this._keyStr.indexOf(input.charAt(i++));enc2=this._keyStr.indexOf(input.charAt(i++));enc3=this._keyStr.indexOf(input.charAt(i++));enc4=this._keyStr.indexOf(input.charAt(i++));chr1=(enc1<<2)|(enc2>>4);chr2=((enc2&15)<<4)|(enc3>>2);chr3=((enc3&3)<<6)|enc4;output=output+String.fromCharCode(chr1);if(enc3!=64){output=output+String.fromCharCode(chr2);}
if(enc4!=64){output=output+String.fromCharCode(chr3);}}
output=Base64._utf8_decode(output);return output;},_utf8_encode:function(string){string=string.replace(/\r\n/g,"\n");var utftext="";for(var n=0;n<string.length;n++){var c=string.charCodeAt(n);if(c<128){utftext+=String.fromCharCode(c);}
else if((c>127)&&(c<2048)){utftext+=String.fromCharCode((c>>6)|192);utftext+=String.fromCharCode((c&63)|128);}
else{utftext+=String.fromCharCode((c>>12)|224);utftext+=String.fromCharCode(((c>>6)&63)|128);utftext+=String.fromCharCode((c&63)|128);}}
return utftext;},_utf8_decode:function(utftext){var string="";var i=0;var c=c1=c2=0;while(i<utftext.length){c=utftext.charCodeAt(i);if(c<128){string+=String.fromCharCode(c);i++;}
else if((c>191)&&(c<224)){c2=utftext.charCodeAt(i+1);string+=String.fromCharCode(((c&31)<<6)|(c2&63));i+=2;}
else{c2=utftext.charCodeAt(i+1);c3=utftext.charCodeAt(i+2);string+=String.fromCharCode(((c&15)<<12)|((c2&63)<<6)|(c3&63));i+=3;}}
return string;}}