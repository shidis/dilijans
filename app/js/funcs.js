// GLOBAL FUNCs

function urldecode(str) {
    return decodeURIComponent((str + '')
        .replace(/%(?![\da-f]{2})/gi, function() {
            // PHP tolerates poorly formed escape sequences
            return '%25';
        })
        .replace(/\+/g, '%20'));
}

function getRandom(min, max)
{
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

function setCookie (cookieName, cookieContent, ses){
	if(isNaN(ses)){
		var expDate=new Date();
		expDate.setTime(expDate.getTime()+8640000000);
		var expires=expDate.toGMTString();
	}else var expires='';
	
	document.cookie=cookieName+"="+escape(cookieContent)+"; path="+escape('/')+"; expires="+expires+"; domain="+'.'+window.location.hostname.match(/(www.|)(.*)/)[2];
}

function delCookie (cookieName){
	var expDate=new Date();
	expDate.setTime(expDate.getTime()-1000);
	var expires=expDate.toGMTString();
	document.cookie=cookieName+"=; path="+escape('/')+"; expires="+expires+"; domain="+'.'+window.location.hostname.match(/(www.|)(.*)/)[2];
}

function getCookie(name){ 
	var cname = name + "=";               
	var dc = document.cookie;             
	if (dc.length > 0) 
	{ begin = dc.indexOf(cname);       
	  if (begin != -1) 
	  { begin += cname.length;       
		end = dc.indexOf(";", begin);
		if (end == -1) end = dc.length;
		return unescape(dc.substring(begin, end));
	  } 
	}
	return null;
}

function parseQuery(qstr)
{
    var query = {};
    var a = qstr.split('&');
    for (var i in a) {
        var b = a[i].split('=');
        query[decodeURIComponent(b[0])] = decodeURIComponent(b[1]);
    }

    return query;
}

function mergeFormWithCookie(cookieName, $form)
{
    var c=getCookie(cookieName);
    if(c===null || c.indexOf('%')!=-1) c='';
    if(c!='') c=parseQuery(Base64.decode(c)); else c={};
    $form.find('input,select').each(function()
    {
        if($(this).is('[name]')){
            if(this.type == 'checkbox') {
                if($(this).prop('checked')) c[$(this).attr('name')]=$(this).val(); else delete c[$(this).attr('name')];
            }
            else c[$(this).attr('name')]=$(this).val();
        }
    });

    setCookie(cookieName, c=Base64.encode($.param(c)));
}

function Err(XMLHttpRequest, textStatus, errorThrown){
	if(ax_err_show) 
		if(MD || true) window.alert('Ошибка при загрузке данных ('+textStatus+")\r\n"+XMLHttpRequest.responseText+"\r\nПопробуйте обновить страницу и повторить запрос");
		else Boxy.alert('<p>Ошибка при загрузке данных ('+textStatus+')<br>'+XMLHttpRequest.responseText+'</p> <p>Попробуйте обновить страницу и повторить запрос</p>',null,{title:'ОШИБКА!'});
}

function _alert(msg){
	if(ax_err_show) window.alert(msg);
}
function msg(msg){
	if(MD || true) window.alert(strip_tags(msg)); else Boxy.alert('<div style="text-align:center">'+msg+'</div>',null,{title:'Информация'});
//	window.alert(msg);
}


function emsg(d){
	if(MD || true) window.alert("(!)\r\n"+strip_tags(d.err_msg)); else Boxy.alert('<p>'+d.err_msg+'</p>',null,{title:'ОШИБКА!'});
}

function logit(msg){
	if (($.browser.webkit || $.browser.mozilla) && typeof(console)!='undefined') console.log(msg);
}


function strip_tags( str ){	// Strip HTML and PHP tags from a string

	return str.replace(/<\/?[^>]+>/gi, '');
}


function isArray (v){
	return typeof(v)=='object'&&typeof(v.length)=='number';
}
function isObject (v){
	return v && typeof v == "object";
}
function isFunction (v){
	return toString.apply(v) === '[object Function]';
}
function isNumber (v){
	return typeof v === 'number'; // && $$.isFinite(v)
}
function isString (v){
	return typeof v === 'string';
}
function isBoolean (v){
	return typeof v === 'boolean';
}
function isDefined (v){
	return typeof v !== 'undefined';
}
function isEmpty (v, allowBlank){
	return v === null || v === undefined || ((isArray(v) && !v.length)) || (!allowBlank ? v === '' : false);
}
function isPrimitive (v){
	return isString(v) || isNumber(v) || isBoolean(v);
}

function serialize (mixed_value) {
    // http://kevin.vanzonneveld.net
    // +   original by: Arpad Ray (mailto:arpad@php.net)
    // +   improved by: Dino
    // +   bugfixed by: Andrej Pavlovic
    // +   bugfixed by: Garagoth
    // +      input by: DtTvB (http://dt.in.th/2008-09-16.string-length-in-bytes.html)
    // +   bugfixed by: Russell Walker (http://www.nbill.co.uk/)
    // +   bugfixed by: Jamie Beck (http://www.terabit.ca/)
    // +      input by: Martin (http://www.erlenwiese.de/)
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net/)
    // +   improved by: Le Torbi (http://www.letorbi.de/)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net/)
    // +   bugfixed by: Ben (http://benblume.co.uk/)
    // %          note: We feel the main purpose of this function should be to ease the transport of data between php & js
    // %          note: Aiming for PHP-compatibility, we have to translate objects to arrays
    // *     example 1: serialize(['Kevin', 'van', 'Zonneveld']);
    // *     returns 1: 'a:3:{i:0;s:5:"Kevin";i:1;s:3:"van";i:2;s:9:"Zonneveld";}'
    // *     example 2: serialize({firstName: 'Kevin', midName: 'van', surName: 'Zonneveld'});
    // *     returns 2: 'a:3:{s:9:"firstName";s:5:"Kevin";s:7:"midName";s:3:"van";s:7:"surName";s:9:"Zonneveld";}'
    var val, key, okey, 
        ktype = '', vals = '', count = 0, 
        _utf8Size = function (str) {
            var size = 0,
                i = 0,
                l = str.length,
                code = '';
            for (i = 0; i < l; i++) {
                code = str.charCodeAt(i);
                if (code < 0x0080) {
                    size += 1;
                }
                else if (code < 0x0800) {
                    size += 2;
                }
                else {
                    size += 3;
                }
            }
            return size;
        },
        _getType = function (inp) {
            var match, key, cons, types, type = typeof inp;

            if (type === 'object' && !inp) {
                return 'null';
            }
            if (type === 'object') {
                if (!inp.constructor) {
                    return 'object';
                }
                cons = inp.constructor.toString();
                match = cons.match(/(\w+)\(/);
                if (match) {
                    cons = match[1].toLowerCase();
                }
                types = ['boolean', 'number', 'string', 'array'];
                for (key in types) {
                    if (cons == types[key]) {
                        type = types[key];
                        break;
                    }
                }
            }
            return type;
        },
        type = _getType(mixed_value)
    ;
    
    switch (type) {
        case 'function':
            val = '';
            break;
        case 'boolean':
            val = 'b:' + (mixed_value ? '1' : '0');
            break;
        case 'number':
            val = (Math.round(mixed_value) == mixed_value ? 'i' : 'd') + ':' + mixed_value;
            break;
        case 'string':
            val = 's:' + _utf8Size(mixed_value) + ':"' + mixed_value + '"';
            break;
        case 'array': case 'object':
            val = 'a';
    /*
                if (type == 'object') {
                    var objname = mixed_value.constructor.toString().match(/(\w+)\(\)/);
                    if (objname == undefined) {
                        return;
                    }
                    objname[1] = this.serialize(objname[1]);
                    val = 'O' + objname[1].substring(1, objname[1].length - 1);
                }
                */

            for (key in mixed_value) {
                if (mixed_value.hasOwnProperty(key)) {
                    ktype = _getType(mixed_value[key]);
                    if (ktype === 'function') {
                        continue;
                    }

                    okey = (key.match(/^[0-9]+$/) ? parseInt(key, 10) : key);
                    vals += this.serialize(okey) + this.serialize(mixed_value[key]);
                    count++;
                }
            }
            val += ':' + count + ':{' + vals + '}';
            break;
        case 'undefined':
            // Fall-through
        default:
            // if the JS object has a property which contains a null value, the string cannot be unserialized by PHP
            val = 'N';
            break;
    }
    if (type !== 'object' && type !== 'array') {
        val += ';';
    }
    return val;
}

function unserialize (data) {
    // Takes a string representation of variable and recreates it  
    // 
    // version: 1008.1718
    // discuss at: http://phpjs.org/functions/unserialize    // +     original by: Arpad Ray (mailto:arpad@php.net)
    // +     improved by: Pedro Tainha (http://www.pedrotainha.com)
    // +     bugfixed by: dptr1988
    // +      revised by: d3x
    // +     improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)    // +        input by: Brett Zamir (http://brett-zamir.me)
    // +     improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +     improved by: Chris
    // +     improved by: James
    // +        input by: Martin (http://www.erlenwiese.de/)    // +     bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +     improved by: Le Torbi
    // +     input by: kilops
    // +     bugfixed by: Brett Zamir (http://brett-zamir.me)
    // -      depends on: utf8_decode    // %            note: We feel the main purpose of this function should be to ease the transport of data between php & js
    // %            note: Aiming for PHP-compatibility, we have to translate objects to arrays
    // *       example 1: unserialize('a:3:{i:0;s:5:"Kevin";i:1;s:3:"van";i:2;s:9:"Zonneveld";}');
    // *       returns 1: ['Kevin', 'van', 'Zonneveld']
    // *       example 2: unserialize('a:3:{s:9:"firstName";s:5:"Kevin";s:7:"midName";s:3:"van";s:7:"surName";s:9:"Zonneveld";}');    // *       returns 2: {firstName: 'Kevin', midName: 'van', surName: 'Zonneveld'}
    var that = this;
    var utf8Overhead = function(chr) {
        // http://phpjs.org/functions/unserialize:571#comment_95906
        var code = chr.charCodeAt(0);        if (code < 0x0080) {
            return 0;
        }
        if (code < 0x0800) {
             return 1;        }
        return 2;
    };
 
     var error = function (type, msg, filename, line){throw new that.window[type](msg, filename, line);};
    var read_until = function (data, offset, stopchr){
        var buf = [];
        var chr = data.slice(offset, offset + 1);
        var i = 2;        while (chr != stopchr) {
            if ((i+offset) > data.length) {
                error('Error', 'Invalid');
            }
            buf.push(chr);            chr = data.slice(offset + (i - 1),offset + i);
            i += 1;
        }
        return [buf.length, buf.join('')];
    };    var read_chrs = function (data, offset, length){
        var buf;
 
        buf = [];
        for (var i = 0;i < length;i++){            var chr = data.slice(offset + (i - 1),offset + i);
            buf.push(chr);
            length -= utf8Overhead(chr); 
        }
        return [buf.length, buf.join('')];    };
    var _unserialize = function (data, offset){
        var readdata;
        var readData;
        var chrs = 0;        var ccount;
        var stringlength;
        var keyandchrs;
        var keys;
         if (!offset) {offset = 0;}
        var dtype = (data.slice(offset, offset + 1)).toLowerCase();
 
        var dataoffset = offset + 2;
        var typeconvert = function(x) {return x;}; 
        switch (dtype){
            case 'i':
                typeconvert = function (x) {return parseInt(x, 10);};
                readData = read_until(data, dataoffset, ';');                chrs = readData[0];
                readdata = readData[1];
                dataoffset += chrs + 1;
            break;
            case 'b':                typeconvert = function (x) {return parseInt(x, 10) !== 0;};
                readData = read_until(data, dataoffset, ';');
                chrs = readData[0];
                readdata = readData[1];
                dataoffset += chrs + 1;            break;
            case 'd':
                typeconvert = function (x) {return parseFloat(x);};
                readData = read_until(data, dataoffset, ';');
                chrs = readData[0];                readdata = readData[1];
                dataoffset += chrs + 1;
            break;
            case 'n':
                readdata = null;            break;
            case 's':
                ccount = read_until(data, dataoffset, ':');
                chrs = ccount[0];
                stringlength = ccount[1];                dataoffset += chrs + 2;
 
                readData = read_chrs(data, dataoffset+1, parseInt(stringlength, 10));
                chrs = readData[0];
                readdata = readData[1];                dataoffset += chrs + 2;
                if (chrs != parseInt(stringlength, 10) && chrs != readdata.length){
                    error('SyntaxError', 'String length mismatch');
                }
                 // Length was calculated on an utf-8 encoded string
                // so wait with decoding
                readdata = that.utf8_decode(readdata);
            break;
            case 'a':                readdata = {};
 
                keyandchrs = read_until(data, dataoffset, ':');
                chrs = keyandchrs[0];
                keys = keyandchrs[1];                dataoffset += chrs + 2;
 
                for (var i = 0; i < parseInt(keys, 10); i++){
                    var kprops = _unserialize(data, dataoffset);
                    var kchrs = kprops[1];                    var key = kprops[2];
                    dataoffset += kchrs;
 
                    var vprops = _unserialize(data, dataoffset);
                    var vchrs = vprops[1];                    var value = vprops[2];
                    dataoffset += vchrs;
 
                    readdata[key] = value;
                } 
                dataoffset += 1;
            break;
            default:
                error('SyntaxError', 'Unknown / Unhandled data type(s): ' + dtype);            break;
        }
        return [dtype, dataoffset - offset, typeconvert(readdata)];
    };
        return _unserialize((data+''), 0)[2];
}

function utf8_decode ( str_data ) {
    // Converts a UTF-8 encoded string to ISO-8859-1  
    // 
    // version: 1008.1718
    // discuss at: http://phpjs.org/functions/utf8_decode    // +   original by: Webtoolkit.info (http://www.webtoolkit.info/)
    // +      input by: Aman Gupta
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Norman "zEh" Fuchs
    // +   bugfixed by: hitwork    // +   bugfixed by: Onno Marsman
    // +      input by: Brett Zamir (http://brett-zamir.me)
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // *     example 1: utf8_decode('Kevin van Zonneveld');
    // *     returns 1: 'Kevin van Zonneveld'    var tmp_arr = [], i = 0, ac = 0, c1 = 0, c2 = 0, c3 = 0;
    
    str_data += '';
    var i=0;
	var tmp_arr=[];
	ac=0;
    while ( i < str_data.length ) {        c1 = str_data.charCodeAt(i);
        if (c1 < 128) {
            tmp_arr[ac++] = String.fromCharCode(c1);
            i++;
        } else if ((c1 > 191) && (c1 < 224)) {            c2 = str_data.charCodeAt(i+1);
            tmp_arr[ac++] = String.fromCharCode(((c1 & 31) << 6) | (c2 & 63));
            i += 2;
        } else {
            c2 = str_data.charCodeAt(i+1);            c3 = str_data.charCodeAt(i+2);
            tmp_arr[ac++] = String.fromCharCode(((c1 & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
            i += 3;
        }
    } 
    return tmp_arr.join('');
}

var Base64 = {

	// private property
	_keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",

	// public method for encoding
	encode : function (input) {
		var output = "";
		var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
		var i = 0;

		input = Base64._utf8_encode(input);

		while (i < input.length) {

			chr1 = input.charCodeAt(i++);
			chr2 = input.charCodeAt(i++);
			chr3 = input.charCodeAt(i++);

			enc1 = chr1 >> 2;
			enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
			enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
			enc4 = chr3 & 63;

			if (isNaN(chr2)) {
				enc3 = enc4 = 64;
			} else if (isNaN(chr3)) {
				enc4 = 64;
			}

			output = output +
			this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
			this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);

		}

		return output;
	},

	// public method for decoding
	decode : function (input) {
		var output = "";
		var chr1, chr2, chr3;
		var enc1, enc2, enc3, enc4;
		var i = 0;

		input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

		while (i < input.length) {

			enc1 = this._keyStr.indexOf(input.charAt(i++));
			enc2 = this._keyStr.indexOf(input.charAt(i++));
			enc3 = this._keyStr.indexOf(input.charAt(i++));
			enc4 = this._keyStr.indexOf(input.charAt(i++));

			chr1 = (enc1 << 2) | (enc2 >> 4);
			chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
			chr3 = ((enc3 & 3) << 6) | enc4;

			output = output + String.fromCharCode(chr1);

			if (enc3 != 64) {
				output = output + String.fromCharCode(chr2);
			}
			if (enc4 != 64) {
				output = output + String.fromCharCode(chr3);
			}

		}

		output = Base64._utf8_decode(output);

		return output;

	},

	// private method for UTF-8 encoding
	_utf8_encode : function (string) {
		string = string.replace(/\r\n/g,"\n");
		var utftext = "";

		for (var n = 0; n < string.length; n++) {

			var c = string.charCodeAt(n);

			if (c < 128) {
				utftext += String.fromCharCode(c);
			}
			else if((c > 127) && (c < 2048)) {
				utftext += String.fromCharCode((c >> 6) | 192);
				utftext += String.fromCharCode((c & 63) | 128);
			}
			else {
				utftext += String.fromCharCode((c >> 12) | 224);
				utftext += String.fromCharCode(((c >> 6) & 63) | 128);
				utftext += String.fromCharCode((c & 63) | 128);
			}

		}

		return utftext;
	},

	// private method for UTF-8 decoding
	_utf8_decode : function (utftext) {
		var string = "";
		var i = 0;
		var c = c1 = c2 = 0;

		while ( i < utftext.length ) {

			c = utftext.charCodeAt(i);

			if (c < 128) {
				string += String.fromCharCode(c);
				i++;
			}
			else if((c > 191) && (c < 224)) {
				c2 = utftext.charCodeAt(i+1);
				string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
				i += 2;
			}
			else {
				c2 = utftext.charCodeAt(i+1);
				c3 = utftext.charCodeAt(i+2);
				string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
				i += 3;
			}

		}

		return string;
	}

}
