// JavaScript Document

function loading(cont, type){
	if(type==-1)document.getElementById(cont).innerHTML='';
		else document.getElementById(cont).innerHTML='<img src="/cms/img/loader'+(type!=''?('.'+type):'')+'.gif">';
}

function saving(but,cont,text){
	if(text=='') text='Записываю...';
	document.getElementById(but).disabled=true;
	document.getElementById(cont).style.display='block';
	document.getElementById(cont).innerHTML='<img align=absmiddle src="/cms/img/loader.balls.gif" class=rmar30><b class=red>'+text+'</b>';
}
function saved(but,cont,text){
	if(text=='') text='Записано';
	document.getElementById(but).disabled=false;
	document.getElementById(cont).innerHTML='<font class=red>'+text+'</font>';
}

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

function setCookie (cookieName, cookieContent)
{
 var expDate=new Date();
 expDate.setTime(expDate.getTime()+8640000000000);
 var expires=expDate.toGMTString();
 document.cookie=cookieName+"="+escape(cookieContent)+"; path="+escape('/')+"; expires="+expires;
}
function delCookie (cookieName)
{
 var expDate=new Date();
 expDate.setTime(expDate.getTime()-1000);
 var expires=expDate.toGMTString();
 document.cookie=cookieName+"=; path="+escape('/')+"; expires="+expires;
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

function serialize (mixed_value) {
    // Returns a string representation of variable (which can later be unserialized)  
    // 
    // version: 1009.820
    // discuss at: http://phpjs.org/functions/serialize    // +   original by: Arpad Ray (mailto:arpad@php.net)
    // +   improved by: Dino
    // +   bugfixed by: Andrej Pavlovic
    // +   bugfixed by: Garagoth
    // +      input by: DtTvB (http://dt.in.th/2008-09-16.string-length-in-bytes.html)    // +   bugfixed by: Russell Walker (http://www.nbill.co.uk/)
    // +   bugfixed by: Jamie Beck (http://www.terabit.ca/)
    // +      input by: Martin (http://www.erlenwiese.de/)
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net/)
    // +   improved by: Le Torbi (http://www.letorbi.de/)    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net/)
    // -    depends on: utf8_encode
    // %          note: We feel the main purpose of this function should be to ease the transport of data between php & js
    // %          note: Aiming for PHP-compatibility, we have to translate objects to arrays
    // *     example 1: serialize(['Kevin', 'van', 'Zonneveld']);    // *     returns 1: 'a:3:{i:0;s:5:"Kevin";i:1;s:3:"van";i:2;s:9:"Zonneveld";}'
    // *     example 2: serialize({firstName: 'Kevin', midName: 'van', surName: 'Zonneveld'});
    // *     returns 2: 'a:3:{s:9:"firstName";s:5:"Kevin";s:7:"midName";s:3:"van";s:7:"surName";s:9:"Zonneveld";}'
        var _utf8Size = function (str) {
            var size = 0, i = 0, l = str.length, code = '';            for (i = 0; i < l; i++) {
                code = str[i].charCodeAt(0);
                if (code < 0x0080) {
                    size += 1;
                } else if (code < 0x0800) {                    size += 2;
                } else {
                        size += 3;
                        }
            }            return size;
        };
    var _getType = function (inp) {
        var type = typeof inp, match;
        var key; 
        if (type === 'object' && !inp) {
            return 'null';
        }
        if (type === "object") {            if (!inp.constructor) {
                return 'object';
            }
            var cons = inp.constructor.toString();
            match = cons.match(/(\w+)\(/);            if (match) {
                cons = match[1].toLowerCase();
            }
            var types = ["boolean", "number", "string", "array"];
            for (key in types) {                if (cons == types[key]) {
                    type = types[key];
                    break;
                }
            }        }
        return type;
    };
    var type = _getType(mixed_value);
    var val, ktype = '';    
    switch (type) {
        case "function": 
            val = ""; 
            break;        case "boolean":
            val = "b:" + (mixed_value ? "1" : "0");
            break;
        case "number":
            val = (Math.round(mixed_value) == mixed_value ? "i" : "d") + ":" + mixed_value;            break;
        case "string":
                        val = "s:" + _utf8Size(mixed_value) + ":\"" + mixed_value + "\"";
            break;
        case "array":        case "object":
            val = "a";
            /*
            if (type == "object") {
                var objname = mixed_value.constructor.toString().match(/(\w+)\(\)/);                if (objname == undefined) {
                    return;
                }
                objname[1] = this.serialize(objname[1]);
                val = "O" + objname[1].substring(1, objname[1].length - 1);            }
            */
            var count = 0;
            var vals = "";
            var okey;            var key;
            for (key in mixed_value) {
                            if (mixed_value.hasOwnProperty(key)) {
                   ktype = _getType(mixed_value[key]);
                       if (ktype === "function") {                            continue; 
                       }
               
                       okey = (key.match(/^[0-9]+$/) ? parseInt(key, 10) : key);
                       vals += this.serialize(okey) +                               this.serialize(mixed_value[key]);
                       count++;
                        }
            }
            val += ":" + count + ":{" + vals + "}";            break;
        case "undefined": // Fall-through
        default: // if the JS object has a property which contains a null value, the string cannot be unserialized by PHP
            val = "N";
            break;    }
    if (type !== "object" && type !== "array") {
        val += ";";
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

function implode (glue, pieces) {
    // Joins array elements placing glue string between items and return one string  
    // 
    // version: 1008.1718
    // discuss at: http://phpjs.org/functions/implode    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Waldo Malqui Silva
    // +   improved by: Itsacon (http://www.itsacon.net/)
    // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
    // *     example 1: implode(' ', ['Kevin', 'van', 'Zonneveld']);    // *     returns 1: 'Kevin van Zonneveld'
    // *     example 2: implode(' ', {first:'Kevin', last: 'van Zonneveld'});
    // *     returns 2: 'Kevin van Zonneveld'
    var i = '', retVal='', tGlue='';
    if (arguments.length === 1) {        pieces = glue;
        glue = '';
    }
    if (typeof(pieces) === 'object') {
        if (pieces instanceof Array) {            return pieces.join(glue);
        }
        else {
            for (i in pieces) {
                retVal += tGlue + pieces[i];                tGlue = glue;
            }
            return retVal;
        }
    }    else {
        return pieces;
    }
}

function explode (delimiter, string, limit) {
    // Splits a string on string separator and return array of components. If limit is positive only limit number of components is returned. If limit is negative all components except the last abs(limit) are returned.  
    // 
    // version: 1008.1718
    // discuss at: http://phpjs.org/functions/explode    // +     original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +     improved by: kenneth
    // +     improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +     improved by: d3x
    // +     bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)    // *     example 1: explode(' ', 'Kevin van Zonneveld');
    // *     returns 1: {0: 'Kevin', 1: 'van', 2: 'Zonneveld'}
    // *     example 2: explode('=', 'a=bc=d', 2);
    // *     returns 2: ['a', 'bc=d']
     var emptyArray = { 0: '' };
    
    // third argument is not required
    if ( arguments.length < 2 ||
        typeof arguments[0] == 'undefined' ||        typeof arguments[1] == 'undefined' ) {
        return null;
    }
 
    if ( delimiter === '' ||        delimiter === false ||
        delimiter === null ) {
        return false;
    }
     if ( typeof delimiter == 'function' ||
        typeof delimiter == 'object' ||
        typeof string == 'function' ||
        typeof string == 'object' ) {
        return emptyArray;    }
 
    if ( delimiter === true ) {
        delimiter = '1';
    }    
    if (!limit) {
        return string.toString().split(delimiter.toString());
    } else {
        // support for limit argument        var splitted = string.toString().split(delimiter.toString());
        var partA = splitted.splice(0, limit - 1);
        var partB = splitted.join(delimiter.toString());
        partA.push(partB);
        return partA;    }
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
