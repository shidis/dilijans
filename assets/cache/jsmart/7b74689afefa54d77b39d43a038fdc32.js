function dt2humanDM(dt,fullYear)
{var ex=/([0-9]{2})([0-9]{2})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})/.exec(dt);if(isDefined(fullYear))
return ex[4]+'.'+ex[3]+'.'+ex[1]+ex[2]+' '+ex[5]+':'+ex[6];else
return ex[4]+'.'+ex[3]+'.'+ex[2]+' '+ex[5]+':'+ex[6];}
function date2Int(date)
{var ex=/([0-9]{2})-([0-9]{2})-([0-9]{4})/.exec(date);if(ex===null)
return false;else
return new Date(ex[3],ex[2]-1,ex[1]);}
function getNowDate()
{var today=new Date();today=new Date(today.getFullYear(),today.getMonth(),today.getDate());return today;}
function print_r(obj)
{return JSON.stringify(obj,null,'\t').replace(/\n/g,'<br>').replace(/\t/g,'&nbsp;&nbsp;&nbsp;');}
function array_merge()
{var args=Array.prototype.slice.call(arguments),argl=args.length,arg,retObj={},k='',argil=0,j=0,i=0,ct=0,toStr=Object.prototype.toString,retArr=true;for(i=0;i<argl;i++){if(toStr.call(args[i])!=='[object Array]'){retArr=false;break;}}
if(retArr){retArr=[];for(i=0;i<argl;i++){retArr=retArr.concat(args[i]);}
return retArr;}
for(i=0,ct=0;i<argl;i++){arg=args[i];if(toStr.call(arg)==='[object Array]'){for(j=0,argil=arg.length;j<argil;j++){retObj[ct++]=arg[j];}}
else{for(k in arg){if(arg.hasOwnProperty(k)){if(parseInt(k,10)+''===k){retObj[ct++]=arg[k];}
else{retObj[k]=arg[k];}}}}}
return retObj;}
function isArray(v)
{return typeof(v)=='object'&&typeof(v.length)=='number';}
function isObject(v)
{return v&&typeof v=="object";}
function isFunction(v)
{return toString.apply(v)==='[object Function]';}
function isNumber(v)
{return typeof v==='number';}
function isString(v)
{return typeof v==='string';}
function isBoolean(v)
{return typeof v==='boolean';}
function isDefined(v)
{return typeof v!=='undefined';}
function isEmpty(v,allowBlank)
{return v===null||v===undefined||((isArray(v)&&!v.length))||(!allowBlank?v==='':false);}
function isPrimitive(v)
{return isString(v)||isNumber(v)||isBoolean(v);}
function strip_tags(str)
{return str.replace(/<\/?[^>]+>/gi,'');}
function isPrintableASCII(c)
{if(c>=0&&c<=47&&c!=8||c>=58&&c<=64||c>=91&&c<=96||c>=123&&c<=191&&c!=46)return false;else return true;}
function populate(form,data)
{for(var tag in data){if($(form+' [name='+tag+']').length)
switch($(form+' [name='+tag+']').get(0).tagName){case'INPUT':if($(form+' [name='+tag+']').attr('type')=='text')$(form+' [name='+tag+']').val(data[tag]);else if($(form+' [name='+tag+']').attr('type')=='checkbox'&&(parseInt(data[tag])==1))$(form+' [name='+tag+']').prop('checked',true);break;case'TEXTAREA':$(form+' [name='+tag+']').val(data[tag]);break;default:$(form+' select[name='+tag+']').val(data[tag]);break;}}}
function parseQuery(qstr)
{var query={};var a=qstr.split('&');for(var i in a){var b=a[i].split('=');query[decodeURIComponent(b[0])]=decodeURIComponent(b[1]);}
return query;}
function _parseQuery(qstr)
{var query=[];var a=qstr.split('&');for(var i in a){var b=a[i].split('=');var c=decodeURIComponent(b[1]);query.push([decodeURIComponent(b[0]),c!='undefined'?c:'']);}
return query;}
function urldecode(str)
{return decodeURIComponent((str+'')
.replace(/%(?![\da-f]{2})/gi,function()
{return'%25';})
.replace(/\+/g,'%20'));}
function urlencode(str)
{str=(str+'')
.toString();return encodeURIComponent(str)
.replace(/!/g,'%21')
.replace(/'/g,'%27')
.replace(/\(/g,'%28')
.
replace(/\)/g,'%29')
.replace(/\*/g,'%2A')
.replace(/%20/g,'+');}
function getRandom(min,max)
{return Math.floor(Math.random()*(max-min+1))+min;}
function setCookie(name,value,props)
{props=props||{};if(typeof props.domain=='undefined')props.domain=window.location.hostname.match(/(www.|)(.*)/)[2];if(typeof props.path=='undefined')props.path='/';var exp=props.expires;if(typeof exp=="number"&&exp){var d=new Date();d.setTime(d.getTime()+exp*1000);exp=props.expires=d}
if(exp&&exp.toUTCString){props.expires=exp.toUTCString();}
value=encodeURIComponent(value);var updatedCookie=name+"="+value;for(var propName in props){updatedCookie+="; "+propName;var propValue=props[propName];if(propValue!==true){updatedCookie+="="+propValue;}}
document.cookie=updatedCookie;}
function delCookie(cookieName,props)
{props=props||{}
var expDate=new Date();expDate.setTime(expDate.getTime()-1000);var expires=expDate.toGMTString();if(typeof props.domain=='undefined')props.domain=window.location.hostname.match(/(www.|)(.*)/)[2];if(typeof props.path=='undefined')props.path='/';document.cookie=cookieName+"=; expires="+expires+"; domain="+escape(props.domain)+"; path="+escape(props.path);}
function getCookie(name)
{var matches=document.cookie.match(new RegExp("(?:^|; )"+name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g,'\\$1')+"=([^;]*)"));return matches?decodeURIComponent(matches[1]):null}
function str_replace(search,replace,subject)
{return subject.split(search).join(replace);}
function count(mixed_var,mode)
{var key,cnt=0;if(mode=='COUNT_RECURSIVE')mode=1;if(mode!=1)mode=0;for(key in mixed_var){cnt++;if(mode==1&&mixed_var[key]&&(mixed_var[key].constructor===Array||mixed_var[key].constructor===Object)){cnt+=count(mixed_var[key],1);}}
return cnt;}
function logit(msg)
{if($.browser.webkit||$.browser.mozilla)console.log(msg);}
function implode(glue,pieces)
{return((pieces instanceof Array)?pieces.join(glue):pieces);}
function serialize(mixed_value)
{var val,key,okey,ktype='',vals='',count=0,_utf8Size=function(str)
{var size=0,i=0,l=str.length,code='';for(i=0;i<l;i++){code=str.charCodeAt(i);if(code<0x0080){size+=1;}
else if(code<0x0800){size+=2;}
else{size+=3;}}
return size;},_getType=function(inp)
{var match,key,cons,types,type=typeof inp;if(type==='object'&&!inp){return'null';}
if(type==='object'){if(!inp.constructor){return'object';}
cons=inp.constructor.toString();match=cons.match(/(\w+)\(/);if(match){cons=match[1].toLowerCase();}
types=['boolean','number','string','array'];for(key in types){if(cons==types[key]){type=types[key];break;}}}
return type;},type=_getType(mixed_value);switch(type){case'function':val='';break;case'boolean':val='b:'+(mixed_value?'1':'0');break;case'number':val=(Math.round(mixed_value)==mixed_value?'i':'d')+':'+mixed_value;break;case'string':val='s:'+_utf8Size(mixed_value)+':"'+mixed_value+'"';break;case'array':case'object':val='a';for(key in mixed_value){if(mixed_value.hasOwnProperty(key)){ktype=_getType(mixed_value[key]);if(ktype==='function'){continue;}
okey=(key.match(/^[0-9]+$/)?parseInt(key,10):key);vals+=this.serialize(okey)+this.serialize(mixed_value[key]);count++;}}
val+=':'+count+':{'+vals+'}';break;case'undefined':default:val='N';break;}
if(type!=='object'&&type!=='array'){val+=';';}
return val;}
function unserialize(data)
{var that=this,utf8Overhead=function(chr)
{var code=chr.charCodeAt(0);if(code<0x0080){return 0;}
if(code<0x0800){return 1;}
return 2;},error=function(type,msg,filename,line)
{throw new that.window[type](msg,filename,line);},read_until=function(data,offset,stopchr)
{var i=2,buf=[],chr=data.slice(offset,offset+1);while(chr!=stopchr){if((i+offset)>data.length){error('Error','Invalid');}
buf.push(chr);chr=data.slice(offset+(i-1),offset+i);i+=1;}
return[buf.length,buf.join('')];},read_chrs=function(data,offset,length)
{var i,chr,buf;buf=[];for(i=0;i<length;i++){chr=data.slice(offset+(i-1),offset+i);buf.push(chr);length-=utf8Overhead(chr);}
return[buf.length,buf.join('')];},_unserialize=function(data,offset)
{var dtype,dataoffset,keyandchrs,keys,readdata,readData,ccount,stringlength,i,key,kprops,kchrs,vprops,vchrs,value,chrs=0,typeconvert=function(x)
{return x;};if(!offset){offset=0;}
dtype=(data.slice(offset,offset+1)).toLowerCase();dataoffset=offset+2;switch(dtype){case'i':typeconvert=function(x)
{return parseInt(x,10);};readData=read_until(data,dataoffset,';');chrs=readData[0];readdata=readData[1];dataoffset+=chrs+1;break;case'b':typeconvert=function(x)
{return parseInt(x,10)!==0;};readData=read_until(data,dataoffset,';');chrs=readData[0];readdata=readData[1];dataoffset+=chrs+1;break;case'd':typeconvert=function(x)
{return parseFloat(x);};readData=read_until(data,dataoffset,';');chrs=readData[0];readdata=readData[1];dataoffset+=chrs+1;break;case'n':readdata=null;break;case's':ccount=read_until(data,dataoffset,':');chrs=ccount[0];stringlength=ccount[1];dataoffset+=chrs+2;readData=read_chrs(data,dataoffset+1,parseInt(stringlength,10));chrs=readData[0];readdata=readData[1];dataoffset+=chrs+2;if(chrs!=parseInt(stringlength,10)&&chrs!=readdata.length){error('SyntaxError','String length mismatch');}
break;case'a':readdata={};keyandchrs=read_until(data,dataoffset,':');chrs=keyandchrs[0];keys=keyandchrs[1];dataoffset+=chrs+2;for(i=0;i<parseInt(keys,10);i++){kprops=_unserialize(data,dataoffset);kchrs=kprops[1];key=kprops[2];dataoffset+=kchrs;vprops=_unserialize(data,dataoffset);vchrs=vprops[1];value=vprops[2];dataoffset+=vchrs;readdata[key]=value;}
dataoffset+=1;break;default:error('SyntaxError','Unknown / Unhandled data type(s): '+dtype);break;}
return[dtype,dataoffset-offset,typeconvert(readdata)];};return _unserialize((data+''),0)[2];}
function utf8_decode(str_data)
{var tmp_arr=[],i=0,ac=0,c1=0,c2=0,c3=0;str_data+='';while(i<str_data.length){c1=str_data.charCodeAt(i);if(c1<128){tmp_arr[ac++]=String.fromCharCode(c1);i++;}else if(c1>191&&c1<224){c2=str_data.charCodeAt(i+1);tmp_arr[ac++]=String.fromCharCode(((c1&31)<<6)|(c2&63));i+=2;}else{c2=str_data.charCodeAt(i+1);c3=str_data.charCodeAt(i+2);tmp_arr[ac++]=String.fromCharCode(((c1&15)<<12)|((c2&63)<<6)|(c3&63));i+=3;}}
return tmp_arr.join('');}
function implode(glue,pieces)
{var i='',retVal='',tGlue='';if(arguments.length===1){pieces=glue;glue='';}
if(typeof(pieces)==='object'){if(pieces instanceof Array){return pieces.join(glue);}
else{for(i in pieces){retVal+=tGlue+pieces[i];tGlue=glue;}
return retVal;}}else{return pieces;}}
function explode(delimiter,string,limit)
{var emptyArray={0:''};if(arguments.length<2||typeof arguments[0]=='undefined'||typeof arguments[1]=='undefined'){return null;}
if(delimiter===''||delimiter===false||delimiter===null){return false;}
if(typeof delimiter=='function'||typeof delimiter=='object'||typeof string=='function'||typeof string=='object'){return emptyArray;}
if(delimiter===true){delimiter='1';}
if(!limit){return string.toString().split(delimiter.toString());}else{var partA=splitted.splice(0,limit-1);var partB=splitted.join(delimiter.toString());partA.push(partB);return partA;}}
function arrayCount(mixed_var,mode)
{var key,cnt=0;if(mode=='COUNT_RECURSIVE')mode=1;if(mode!=1)mode=0;for(key in mixed_var){cnt++;if(mode==1&&mixed_var[key]&&(mixed_var[key].constructor===Array||mixed_var[key].constructor===Object)){cnt+=count(mixed_var[key],1);}}
return cnt;}
var Base64={_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",encode:function(input)
{var output="";var chr1,chr2,chr3,enc1,enc2,enc3,enc4;var i=0;input=Base64._utf8_encode(input);while(i<input.length){chr1=input.charCodeAt(i++);chr2=input.charCodeAt(i++);chr3=input.charCodeAt(i++);enc1=chr1>>2;enc2=((chr1&3)<<4)|(chr2>>4);enc3=((chr2&15)<<2)|(chr3>>6);enc4=chr3&63;if(isNaN(chr2)){enc3=enc4=64;}else if(isNaN(chr3)){enc4=64;}
output=output+this._keyStr.charAt(enc1)+this._keyStr.charAt(enc2)+this._keyStr.charAt(enc3)+this._keyStr.charAt(enc4);}
return output;},decode:function(input)
{var output="";var chr1,chr2,chr3;var enc1,enc2,enc3,enc4;var i=0;input=input.replace(/[^A-Za-z0-9\+\/\=]/g,"");while(i<input.length){enc1=this._keyStr.indexOf(input.charAt(i++));enc2=this._keyStr.indexOf(input.charAt(i++));enc3=this._keyStr.indexOf(input.charAt(i++));enc4=this._keyStr.indexOf(input.charAt(i++));chr1=(enc1<<2)|(enc2>>4);chr2=((enc2&15)<<4)|(enc3>>2);chr3=((enc3&3)<<6)|enc4;output=output+String.fromCharCode(chr1);if(enc3!=64){output=output+String.fromCharCode(chr2);}
if(enc4!=64){output=output+String.fromCharCode(chr3);}}
output=Base64._utf8_decode(output);return output;},_utf8_encode:function(string)
{string=string.replace(/\r\n/g,"\n");var utftext="";for(var n=0;n<string.length;n++){var c=string.charCodeAt(n);if(c<128){utftext+=String.fromCharCode(c);}
else if((c>127)&&(c<2048)){utftext+=String.fromCharCode((c>>6)|192);utftext+=String.fromCharCode((c&63)|128);}
else{utftext+=String.fromCharCode((c>>12)|224);utftext+=String.fromCharCode(((c>>6)&63)|128);utftext+=String.fromCharCode((c&63)|128);}}
return utftext;},_utf8_decode:function(utftext)
{var string="";var i=0;var c=c1=c2=0;while(i<utftext.length){c=utftext.charCodeAt(i);if(c<128){string+=String.fromCharCode(c);i++;}
else if((c>191)&&(c<224)){c2=utftext.charCodeAt(i+1);string+=String.fromCharCode(((c&31)<<6)|(c2&63));i+=2;}
else{c2=utftext.charCodeAt(i+1);c3=utftext.charCodeAt(i+2);string+=String.fromCharCode(((c&15)<<12)|((c2&63)<<6)|(c3&63));i+=3;}}
return string;}}
function twoDigits(retval)
{if(retval<10){return("0"+retval.toString());}
else{return retval.toString();}}
function validateEmail(email)
{var re=/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;return re.test(email);}
function checkTel(tel)
{tel=$.trim(tel);var digit=tel.replace(/[^0-9]/g,'');return/^[0-9\(\)\-\s\+]+$/i.test(tel)&&digit.length<=16&&digit.length>=10;}