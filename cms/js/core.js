
$$={
	setCookie: function (cookieName, cookieContent, expires){
		var expDate=new Date();
		expDate.setTime(expDate.getTime()+8640000000000);
		if(!isNaN(expires)) expires=expires=='long'?expDate.toGMTString():(expires=='session'?'':expires);
			else var expires=expDate.toGMTString();
		document.cookie=cookieName+"="+escape(cookieContent)+"; path="+escape('/')+(expires!=''?("; expires="+expires):'');
	},
	getCookie: function(name){ 
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
	,lg: function(a){
		if(($.browser.webkit || $.browser.mozilla) && typeof(console)!='undefined') window.console.log(arguments[0]);
	}
	,msg: function(s){
		window.alert(s);
	}
	,note: function (text,mode){
		switch (mode){
			case 'error':$.jGrowl(text, { sticky: true, header:'<font style="color:#ff00">ОШИБКА!</font>' }); break;
			case 'stick':$.jGrowl(text, { sticky: true, header:'Важно!' }); break;
			case 'long':$.jGrowl(text, { life: 10000 }); break;
			default:$.jGrowl(text);
		}
	}
	,fresMsg: function(msg){
		var r='';
		if($$.isArray(msg))
			for(i=0;i<msg.length;i++)
				r=r+'<br>'+msg[i];
		else r=msg;
		return r;
	}
    ,extend : function(){    // subclass_constructor, overrides(с конструктором!)  или subclass_consructor,superclass,overrides. $$ расширять низя
		var io = function(o){
        	for(var m in o){
            	this[m] = o[m];
            }
        };
        var oc = Object.prototype.constructor;
    	return function(sb, sp, overrides){  
        	if($$.isObject(sp)){
            	overrides = sp;
                sp = sb;
                sb = overrides.constructor != oc ? overrides.constructor : function(){sp.apply(this, arguments);};
           	}
            var F = function(){}, sbp, spp = sp.prototype;
            F.prototype = spp;
            sbp = sb.prototype = new F();
            sbp.constructor=sb;
            sb.superclass=spp;
            if(spp.constructor == oc){
            	spp.constructor=sp;
            }
            sb.override = function(o){
            	$$.override(sb, o);
            };
            sbp.superclass = sbp.supr = (function(){
            	return spp;
            });
            sbp.override = io;
            $$.override(sb, overrides);
            sb.extend = function(o){return $$.extend(sb, o);};
            return sb;
        };
	}()
	,override : function(origclass, overrides){
            if(overrides){
                var p = origclass.prototype;
                $$.apply(p, overrides);
            }
    }
	,apply: function(o, c, defaults){
    	if(defaults){
        	$$.apply(o, defaults);
    	}
    	if(o && c && typeof c == 'object'){
        	for(var p in c){
            	o[p] = c[p];
        	}
    	}
    	return o;
	}
	,isArray : function(v){
       return typeof(v)=='object'&&typeof(v.length)=='number';
    }
	,isObject : function(v){
       return v && typeof v == "object";
    }
	,isFunction : function(v){
       return toString.apply(v) === '[object Function]';
    }
	,isNumber : function(v){
       return typeof v === 'number'; // && $$.isFinite(v)
    }
	,isString : function(v){
       return typeof v === 'string';
    }
	,isBoolean : function(v){
       return typeof v === 'boolean';
    }
	,isDefined : function(v){
       return typeof v !== 'undefined';
    }
	,isEmpty : function(v, allowBlank){
       return v === null || v === undefined || (($$.isArray(v) && !v.length)) || (!allowBlank ? v === '' : false);
    }
	,isPrimitive : function(v){
       return $$.isString(v) || $$.isNumber(v) || $$.isBoolean(v);
    }
	,isIterable: function(v){
		//check for array or arguments
        if($$.isArray(v) || v.callee){
            return true;
        }
        //check for node list type
        if(/NodeList|HTMLCollection/.test(toString.call(v))){
            return true;
        }
	}
	,each : function(array, fn, scope){
       if($$.isEmpty(array, true)){
           return;
       }
       if(!$$.isIterable(array) || $$.isPrimitive(array)){
           array = [array];
       }
       for(var i = 0, len = array.length; i < len; i++){
           if(fn.call(scope || array[i], array[i], i, array) === false){
               return i;
           };
       }
    }
    ,destroy : function(){
    	$$.each(arguments, function(arg){
          if(arg){
             if($$.isArray(arg)){
                  this.destroy.apply(this, arg);
             }else if(Ext.isFunction(arg.destroy)){
                 arg.destroy();
             }else if(arg.dom){
                 arg.remove();
             }    
          }
       }, this);
   }
   ,overlay : function(on){
		if(on) $('#overlay').css({'opacity':0.5,'height':'100%','width':'100%','left':0,'top':0,'z-index':1110,'background-color':'#ffffff','position':'fixed'}).show();
		else $('#overlay').hide();
	}
	,ajax: {
		init: function(axUrl){
			$.ajaxSetup({
				type:'POST',
				global: true,
				cache:false,
				async: true,
				dataType: 'json',
				error: $$.ajax.err,
				complete: $$.ajax.complete,
				beforeSend: $$.ajax.before,
				traditional:true,
				url: axUrl
			});
			$$.ajax.url=axUrl;
		},
		url: '',
		q: function(param){
			var s=$$.ajax.url+($$.ajax.url.indexOf('?')!=-1?'&':'?')+$$.buildQuery(param,'','&');
			return s+(s.indexOf('?')!=-1?'&':'?')+'_='+(Math.random()+'').substr(2,13);
		},
		before: function (XMLHttpRequest){
//			$$.overlay(1);
		},
		complete: function (XMLHttpRequest, textStatus){
//			$$.overlay(0);
		},
		err: function (XMLHttpRequest, textStatus, errorThrown){
			$$.note('ajx ERROR: '+textStatus+'<br>'+XMLHttpRequest.responseText,'error');
			alert('');
		}
	},
	_append: function(from, to){
		var id1=from.split(' ');
		$(from).appendTo(to);
		if(id1.length>1) $(to).wrap('<div class="'+id1[0].substr(1)+'"></div>');
//		$(from).remove();
	},
	gridFit: function (id){
       	gridId = $(id).attr('id');
       	gridParentWidth = $('#gbox_' + gridId).parent().width();
       	$(id).setGridWidth(gridParentWidth);
	},
	cp1251toUTF8: function (string) {
		string = string.replace(/\r\n/g,"\n");
		var utftext = "",h0,h1,h2;
		for (var n = 0; n < string.length; n++)
		{
			var c = string.charCodeAt(n);
			if (c < 128)
			{
				utftext += String.fromCharCode(c);
			}
			else if((c > 127) && (c < 2048))
				{
					h1=((c >> 6) | 192).toString(16);
					if (h1.length==1) h1='0'+h1;
					h0=((c & 63) | 128).toString(16);
					if (h0.length==1) h0='0'+h0;
					utftext += '%'+h1+'%'+h0;
				}
				else
				{
					h2=((c >> 12) | 224).toString(16);
					if (h2.length==1) h2='0'+h2;
					h1=(((c >> 6) & 63) | 128).toString(16);
					if (h1.length==1) h1='0'+h1;
					h0=((c & 63) | 128).toString(16);
					if (h0.length==1) h0='0'+h0;
					utftext += '%'+h2+'%'+h1+'%'+h0;
				}
			}
		return utftext;
	},
	serialize: function serialize (mixed_value) {
		// *     example 1: serialize(['Kevin', 'van', 'Zonneveld']);
		// *     returns 1: 'a:3:{i:0;s:5:"Kevin";i:1;s:3:"van";i:2;s:9:"Zonneveld";}'
		// *     example 2: serialize({firstName: 'Kevin', midName: 'van', surName: 'Zonneveld'});
		// *     returns 2: 'a:3:{s:9:"firstName";s:5:"Kevin";s:7:"midName";s:3:"van";s:7:"surName";s:9:"Zonneveld";}'
		var _getType = function (inp) {
			var type = typeof inp, match;
			var key;
			if (type == 'object' && !inp) {
				return 'null';
			}
			if (type == "object") {
				if (!inp.constructor) {
					return 'object';
				}
				var cons = inp.constructor.toString();
				match = cons.match(/(\w+)\(/);
				if (match) {
					cons = match[1].toLowerCase();
				}
				var types = ["boolean", "number", "string", "array"];
				for (key in types) {
					if (cons == types[key]) {
						type = types[key];
						break;
					}
				}
			}
			return type;
		};
		var type = _getType(mixed_value);
		var val, ktype = '';
		
		switch (type) {
			case "function": 
				val = ""; 
				break;
			case "boolean":
				val = "b:" + (mixed_value ? "1" : "0");
				break;
			case "number":
				val = (Math.round(mixed_value) == mixed_value ? "i" : "d") + ":" + mixed_value;
				break;
			case "string":
				mixed_value = this.utf8_encode(mixed_value);
				val = "s:" + encodeURIComponent(mixed_value).replace(/%../g, 'x').length + ":\"" + mixed_value + "\"";
				break;
			case "array":
			case "object":
				val = "a";
				/*
				if (type == "object") {
					var objname = mixed_value.constructor.toString().match(/(\w+)\(\)/);
					if (objname == undefined) {
						return;
					}
					objname[1] = this.serialize(objname[1]);
					val = "O" + objname[1].substring(1, objname[1].length - 1);
				}
				*/
				var count = 0;
				var vals = "";
				var okey;
				var key;
				for (key in mixed_value) {
					ktype = _getType(mixed_value[key]);
					if (ktype == "function") { 
						continue; 
					}
					
					okey = (key.match(/^[0-9]+$/) ? parseInt(key, 10) : key);
					vals += this.serialize(okey) +
							this.serialize(mixed_value[key]);
					count++;
				}
				val += ":" + count + ":{" + vals + "}";
				break;
			case "undefined": // Fall-through
			default: // if the JS object has a property which contains a null value, the string cannot be unserialized by PHP
				val = "N";
				break;
		}
		if (type != "object" && type != "array") {
			val += ";";
		}
		return val;
	},
	utf8_encode: function  ( argString ) {
		// Encodes an ISO-8859-1 string to UTF-8  
		// 
		// version: 909.322
		// discuss at: http://phpjs.org/functions/utf8_encode
		// *     example 1: utf8_encode('Kevin van Zonneveld');
		// *     returns 1: 'Kevin van Zonneveld'
		var string = (argString+''); // .replace(/\r\n/g, "\n").replace(/\r/g, "\n");
	 
		var utftext = "";
		var start, end;
		var stringl = 0;
	 
		start = end = 0;
		stringl = string.length;
		for (var n = 0; n < stringl; n++) {
			var c1 = string.charCodeAt(n);
			var enc = null;
	 
			if (c1 < 128) {
				end++;
			} else if (c1 > 127 && c1 < 2048) {
				enc = String.fromCharCode((c1 >> 6) | 192) + String.fromCharCode((c1 & 63) | 128);
			} else {
				enc = String.fromCharCode((c1 >> 12) | 224) + String.fromCharCode(((c1 >> 6) & 63) | 128) + String.fromCharCode((c1 & 63) | 128);
			}
			if (enc !== null) {
				if (end > start) {
					utftext += string.substring(start, end);
				}
				utftext += enc;
				start = end = n+1;
			}
		}
	 
		if (end > start) {
			utftext += string.substring(start, string.length);
		}
	 
		return utftext;
	},
	utf8_decode: function ( str_data ) {
		// Converts a UTF-8 encoded string to ISO-8859-1  
		// 
		// version: 909.322
		// *     example 1: utf8_decode('Kevin van Zonneveld');
		// *     returns 1: 'Kevin van Zonneveld'
		var tmp_arr = [], i = 0, ac = 0, c1 = 0, c2 = 0, c3 = 0;
		
		str_data += '';
		
		while ( i < str_data.length ) {
			c1 = str_data.charCodeAt(i);
			if (c1 < 128) {
				tmp_arr[ac++] = String.fromCharCode(c1);
				i++;
			} else if ((c1 > 191) && (c1 < 224)) {
				c2 = str_data.charCodeAt(i+1);
				tmp_arr[ac++] = String.fromCharCode(((c1 & 31) << 6) | (c2 & 63));
				i += 2;
			} else {
				c2 = str_data.charCodeAt(i+1);
				c3 = str_data.charCodeAt(i+2);
				tmp_arr[ac++] = String.fromCharCode(((c1 & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
				i += 3;
			}
		}
	 
		return tmp_arr.join('');
	},
	unserialize: function (data) {
		// *       example 1: unserialize('a:3:{i:0;s:5:"Kevin";i:1;s:3:"van";i:2;s:9:"Zonneveld";}');
		// *       returns 1: ['Kevin', 'van', 'Zonneveld']
		// *       example 2: unserialize('a:3:{s:9:"firstName";s:5:"Kevin";s:7:"midName";s:3:"van";s:7:"surName";s:9:"Zonneveld";}');
		// *       returns 2: {firstName: 'Kevin', midName: 'van', surName: 'Zonneveld'}
		var that = this;
		var utf8Overhead = function(chr) {
			// http://phpjs.org/functions/unserialize:571#comment_95906
			var code = chr.charCodeAt(0);
			if (code < 0x0080) {
				return 0;
			}
			if (code < 0x0800) {
				 return 1;
			}
			return 2;
		};
	 
	 
		var error = function (type, msg, filename, line){throw new that.window[type](msg, filename, line);};
		var read_until = function (data, offset, stopchr){
			var buf = [];
			var chr = data.slice(offset, offset + 1);
			var i = 2;
			while (chr != stopchr) {
				if ((i+offset) > data.length) {
					error('Error', 'Invalid');
				}
				buf.push(chr);
				chr = data.slice(offset + (i - 1),offset + i);
				i += 1;
			}
			return [buf.length, buf.join('')];
		};
		var read_chrs = function (data, offset, length){
			var buf;
	 
			buf = [];
			for (var i = 0;i < length;i++){
				var chr = data.slice(offset + (i - 1),offset + i);
				buf.push(chr);
				length -= utf8Overhead(chr); 
			}
			return [buf.length, buf.join('')];
		};
		var _unserialize = function (data, offset){
			var readdata;
			var readData;
			var chrs = 0;
			var ccount;
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
					readData = read_until(data, dataoffset, ';');
					chrs = readData[0];
					readdata = readData[1];
					dataoffset += chrs + 1;
				break;
				case 'b':
					typeconvert = function (x) {return parseInt(x, 10) !== 0;};
					readData = read_until(data, dataoffset, ';');
					chrs = readData[0];
					readdata = readData[1];
					dataoffset += chrs + 1;
				break;
				case 'd':
					typeconvert = function (x) {return parseFloat(x);};
					readData = read_until(data, dataoffset, ';');
					chrs = readData[0];
					readdata = readData[1];
					dataoffset += chrs + 1;
				break;
				case 'n':
					readdata = null;
				break;
				case 's':
					ccount = read_until(data, dataoffset, ':');
					chrs = ccount[0];
					stringlength = ccount[1];
					dataoffset += chrs + 2;
	 
					readData = read_chrs(data, dataoffset+1, parseInt(stringlength, 10));
					chrs = readData[0];
					readdata = readData[1];
					dataoffset += chrs + 2;
					if (chrs != parseInt(stringlength, 10) && chrs != readdata.length){
						error('SyntaxError', 'String length mismatch');
					}
	 
					// Length was calculated on an utf-8 encoded string
					// so wait with decoding
					readdata = that.utf8_decode(readdata);
				break;
				case 'a':
					readdata = {};
	 
					keyandchrs = read_until(data, dataoffset, ':');
					chrs = keyandchrs[0];
					keys = keyandchrs[1];
					dataoffset += chrs + 2;
	 
					for (var i = 0; i < parseInt(keys, 10); i++){
						var kprops = _unserialize(data, dataoffset);
						var kchrs = kprops[1];
						var key = kprops[2];
						dataoffset += kchrs;
	 
						var vprops = _unserialize(data, dataoffset);
						var vchrs = vprops[1];
						var value = vprops[2];
						dataoffset += vchrs;
	 
						readdata[key] = value;
					}
	 
					dataoffset += 1;
				break;
				default:
					error('SyntaxError', 'Unknown / Unhandled data type(s): ' + dtype);
				break;
			}
			return [dtype, dataoffset - offset, typeconvert(readdata)];
		};
		
		return _unserialize((data+''), 0)[2];
	},
	buildQuery: function (formdata, numeric_prefix, arg_separator) { // LIKE PHP http_build_query
		// *     example 1: http_build_query({foo: 'bar', php: 'hypertext processor', baz: 'boom', cow: 'milk'}, '', '&amp;');
		// *     returns 1: 'foo=bar&amp;php=hypertext+processor&amp;baz=boom&amp;cow=milk'
		// *     example 2: http_build_query({'php': 'hypertext processor', 0: 'foo', 1: 'bar', 2: 'baz', 3: 'boom', 'cow': 'milk'}, 'myvar_');
		// *     returns 2: 'php=hypertext+processor&myvar_0=foo&myvar_1=bar&myvar_2=baz&myvar_3=boom&cow=milk'
	
		var value, key, tmp = [];
	
		var _http_build_query_helper = function (key, val, arg_separator) {
			var k, tmp = [];
			if (val === true) {
				val = "1";
			} else if (val === false) {
				val = "0";
			}
			if (val !== null && typeof(val) === "object") {
				for (k in val) {
					if (val[k] !== null) {
						tmp.push(_http_build_query_helper(key + "[" + k + "]", val[k], arg_separator));
					}
				}
				return tmp.join(arg_separator);
			} else if (typeof(val) !== "function") {
				return $$.urlencode(key) + "=" + $$.urlencode(val);
			} else {
				throw new Error('There was an error processing for http_build_query().');
			}
		};
	
		if (!arg_separator) {
			arg_separator = "&";
		}
		for (key in formdata) {
			value = formdata[key];
			if (numeric_prefix && !isNaN(key)) {
				key = String(numeric_prefix) + key;
			}
			tmp.push(_http_build_query_helper(key, value, arg_separator));
		}
	
		return tmp.join(arg_separator);
	},
	urlencode: function (str) {
		str = (str+'').toString();
		return encodeURIComponent(str).replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28').replace(/\)/g, '%29').replace(/\*/g, '%2A').replace(/%20/g, '+');
	},
	populate: function(form,data){
		for(var tag in data){
			switch ($(form+' [name='+tag+']').get(0).tagName){
				case 'INPUT': 
					if($(form+' [name='+tag+']').attr('type')=='text') $(form+' [name='+tag+']').val(data[tag]); 
					else if($(form+' [name='+tag+']').attr('type')=='checkbox') {
						if((data[tag]+'')!='0') $(form+' [name='+tag+']').prop('checked',true); else $(form+' [name='+tag+']').prop('checked',false);
					}
					break;
				case 'TEXTAREA': $(form+' [name='+tag+']').val(data[tag]); break;
				default: $(form+' select[name='+tag+']').val(data[tag]); break;
					
			}
		}
	}
}

$$.Ajax=function(o){
	var self=this;
	$.ajaxSetup({
		type:'POST',
		global: true,
		cache:false,
		dataType: 'json',
		error: self.Err,
		complete: self.complete,
		beforeSend: self.before
	});
	if($$.isObject(o)) $.ajaxSetup(o);
}


$$.override($$.Ajax,{
	before: function (XMLHttpRequest){
		$$.overlay(1);
	},
	complete: function (XMLHttpRequest, textStatus){
		$$.overlay(0);
	},
	Err: function (XMLHttpRequest, textStatus, errorThrown){
		$$.note('ajx ERROR: '+textStatus,'error');
	}
});
			
$$.Window=function(o){
	var self=this;
	this.renderTo = this.renderTo || document.body;
	if($$.isObject(o)) $$.apply(this,o);
	if(!$$.isDefined(this.id)) this.id='win'+(Math.random()+'').substring(2,10);
	this._id=this.id;
	this.id='#'+this._id;
	$(this.renderTo).append('<div id="'+this._id+'" title="'+this.title+'">'+this.html+'</div>');
	$(this.id).hide();
	$(this.id).bind('dialogclose', function(e, ui){
		self.onAfterClose(e,ui);
	});
	$(this.id).bind('dialogbeforeclose', function(e, ui){
		return self.onBeforeClose(e,ui);
	});
	$(this.id).bind('dialogopen', function(e, ui){
		self.onOpen(e,ui);
	});
	this.noRender=this.noRender || false;
	if(!this.noRender) this.render(); else this.autoOpen=false;
	
}
$$.override($$.Window,{
	title: 'Окно',
	html: '',
	option: {bgiframe: false,autoOpen:false},
	modal: false,
	show:'puff',
	height: 'auto',
	width: 300,
	stack: true,
	resizable: true,
	position: 'center',
	minHeight: 60,
	maxHeight: false,
	dialogClass: '',
	autoOpen: true,
	render: function(o){
		if($$.isDefined(o)) $$.apply(this,o);
		$(this.id).html(this.html);
		this.onRender.call(this);
		this.setOptions();
		if(this.autoOpen) $(this.id).dialog('open');
	},
	setOptions: function(){
		$$.apply(this.option,{modal:this.modal,resizable:this.resizable,position:this.position,buttons:this.buttons,height: this.height, width:this.width, title:this.title,stack: this.stack, show:this.show,minHeight:this.minHeight,maxHeight:this.maxHeight,'z-index':this.zIndex, stack:this.stack,autoOpen:this.autoOpen,dialogClass:this.dialogClass,position:this.position});
		$(this.id).dialog(this.option);
	},
	setOption: function (option,value){
		$(this.id).dialog('option',option,value);
	},
	isOpen: function(){
		return $(this.id).dialog('isOpen');
	},
	enable: function(){
		$(this.id).dialog('enable');
	},
	disable: function(){
		$(this.id).dialog('disable');
	},
	destroy: function(){
		$(this.id).dialog('destroy');
	},
	close: function(){
		$(this.id).dialog('close');
	},
	open: function(){
		$(this.id).dialog('open');
	},
	setHTML: function(s){
		$(this.id).html(s);
	},
	onRender: function(){},
	onBeforeClose: function(e,ui){},
	onAfterClose: function(e,ui){},
	onOpen: function(e,ui){}
});

$$.Dialog=$$.extend($$.Window,{
	modal:true,
	resizable: false,
	title:'Диалог',
	zIndex: 3000,	
	stack: true,
	constructor: function(o){
		var self=this;
		this.buttons={};
		for(var v in this.buts){
			this.buttons[v]=function(x){ 
				return function(){
					if(self!=this) self['on'+x].call(this,self);
				}
			}(v);
		};
		$$.Dialog.superclass.constructor.call(this,o);
	},
	buts: {OK:'OK',Cancel:'Отмена'},
	buttons: {},
	onOK: function(e){ $(e.id).dialog('close');},
	onCancel: function(e){ $(e.id).dialog('close');}
});

$$.ProgressDialog=$$.extend($$.Dialog,{
	title: 'Выполняю...',
	value: 0,
	height:140,
	resizable: false,
	html: 'Выполенено: ',
	constructor: function(o){
		var self=this;
		var html=this.html;
		this.html='';
		$$.ProgressDialog.superclass.constructor.call(this,o);
		this.html=html;
		$(this.id).append('<div style="margin-top:10px"></div><p style="margin-top:15px;text-align:center">'+this.html+this.value+' %');
		$(this.id+' div').progressbar({value: this.value+40});
	},
	buts: {},
	setHTML: function(s){
		$(this.id+' p').html(s);
	},
	setValue: function(v){
		$(this.id+' div').progressbar({value:v});
		this.value=v;
		$(this.id+' p').html(this.html+this.value+' %');
	},
	onBeforeClose: function(e,ui){
		return false;
	}
});
	
$$.Button=function(o){
	$$.apply(this,o);
	if(!$$.isDefined(this.id)) this.id='buton'+(Math.random()+'').substring(2,10);
	this._id=this.id;
	this.id='#'+this._id;
	this.renderTo = this.renderTo || (o.renderTo ? o.renderTo : document.body);
	this.html='<button id="'+this._id+'" class="ui-state-default ui-corner-all ui-a">'+this.anc+'</button>';
	this.noRender=this.noRender || false;
	if(!this.noRender) this.render();
};

$$.override($$.Button,{
	anc: 'Кнопка',
	margin: '10px 0px 0 0',
	align: 'center',
	html: '',
	css: {},
	render: function (){
		var self=this;
		$(this.renderTo).append(this.html);
		$(this.id).css('margin',this.margin).css('text-align',this.align).css('display','block');
		$(this.id).css(this.css);
		$('button'+this.id)
			.addClass('ui-state-default ui-corner-all')
			.hover(function() {
				$(this).addClass('ui-state-hover');
			},
			function() {
				$(this).removeClass('ui-state-hover');
			})
			.focus(function() {
//				$(this).addClass('ui-state-focus');
			})
			.blur(function() {
//				$(this).removeClass('ui-state-focus');
			});
		$(this.id).bind('click',function(e){
			e.preventDefault();
			self.onClick(e);
		});
	},
	hide: function(){
		$(this.id).hide();
	},
	show: function(){
		$(this.id).show('fast');
	},
	onClick: function(e){}
});

$$.Tabs=function(o){
	this.renderTo = this.renderTo || document.body;
	if($$.isObject(o)) $$.apply(this,o);
	if(!$$.isDefined(this.id)) this.id='tabs'+(Math.random()+'').substring(2,10);
	this._id=this.id;
	this.id='#'+this._id;
	$(this.renderTo).append('<div id="'+this._id+'"><ul></ul></div>');
	$(this.id).css(this.css);
	$(this.id).tabs().hide();
	var self=this;
	$(this.id).bind('tabsselect', function(e, ui){
		self.onSelect(e,ui);
	});
	this.noRender=this.noRender || false;
	if(!this.noRender) this.render();
};

$$.override($$.Tabs,{
	option: {},
	disabledTabs: [],
	css:{'overflow':'auto'}, // css конетейнера всех табов
	render: function(o){
		if($$.isDefined(o)) $$.apply(this,o);
		this.setOptions();
		$(this.id).show();
	},
	add: function(o){   // {label,id,html,url,padding}   id без #
		var id;
		if(!$$.isDefined(o['id'])) id='tab'+(Math.random()+'').substring(2,10); else id=o['id'];
		if(!$$.isDefined(o['url'])) o['url']='#'+id;

		$(this.id+' .ui-tabs-nav').append('<li><a href="'+($$.isDefined(o['url'])?o['url']:('#'+id))+'">'+o['label']+'</a></li>');
		$(this.id).append('<div id="'+id+'">'+($$.isDefined(o['html'])?o['html']:'')+'</div>');
		$(this.id).tabs( "refresh" );
		
		if($$.isDefined(o['padding'])) {
			$('#'+id).css({'padding':o['padding']});
		}
		if($$.isDefined(o['css'])) {
			$('#'+id).css(o['css']);
		}
		if(o['setActive']) this.select(id);
		if($$.isDefined(o['onNewTab'])) o['onNewTab'].call(this);
	},
	select: function(id){ //id без #  or index
		if($$.isNumber(id)) $(this.id).tabs('option','active',id+1); else $(this.id).tabs('option','active',this.indexById(id));
		//logit(id+' === '+this.indexById(id));
	},
	del: function (id){  // id без #
		//if($$.isNumber(id)) $(this.id).tabs('remove',id); else $(this.id).tabs('remove',this.indexById(id));
		if($$.isNumber(id)) {
			var tab = $(this.id).find( ".ui-tabs-nav li:eq("+id+")" ).remove();
		}else{
			var tab = $(this.id).find( ".ui-tabs-nav li#"+id ).remove();
		}
		// Find the id of the associated panel
		var panelId = tab.attr( "aria-controls" );
		// Remove the panel
		$( "#" + panelId ).remove();
		// Refresh the tabs widget
		$(this.id).tabs( "refresh" );
	},
	hide: function(id){   // здесь index не допустим
		$('a[href=#'+id+']').parent().hide();
		if(this.selected() && this.selectedId()==id) this.select(this.selected()-1);
	},
	show: function(id){  // здесь index не допустим
		$('a[href=#'+id+']').parent().show();
	},
	indexById: function(id){  // id без #
		var self=this;
		self.i=-1;
		self.j=0;
		self.k=id;
		$(this.id+' .ui-tabs-nav li').each(function(o){
			if($(this).attr('aria-controls')==self.k) self.i=self.j;
			self.j++;
		});
		return self.i;
	},
	idByIndex: function(index){
		return $(this.id+' .ui-tabs-nav li:eq('+index+')').children('a').attr('href').slice(1);
	},
	selected: function(){  // при вызове из onSelect возврщает предыдущую вкладку
		return $(this.id).tabs('option', 'active');
	},
	selectedId: function(){ // при вызове из onSelect возврщает предыдущую вкладку
		return this.idByIndex($(this.id).tabs('option', 'active'));
	},
	isId: function(id){
		return $(this.id+' #'+id).length?true:false;
	},
	setOptions: function(){
		$$.apply(this.option,{disabled:this.disabled,'id-prefix':this['id-prefix'],active:this.selected});
		for(var v in this.option)
			$(this.id).tabs('option',v,this.option[v]);
	},
	setHTML: function(s){
		$(this.id).html(s);
	},
	empty: function(s){
		$(this.id).html('');
	},
	onSelect: function(e,ui){}
});

$$.FileList=function(o){
	var self=this;
	this.renderTo = o.renderTo || document.body;
	if($$.isObject(o) && $$.isDefined(o.items)) {
		var it=o.items; 
		o.items={};
	}else it={};
	if($$.isObject(o)) $$.apply(this,o);
	if(!$$.isDefined(this.id)) this.id='fl'+(Math.random()+'').substring(2,10);
	this._id=this.id;
	this.id='#'+this._id;
	$(this.renderTo).append('<ol id="'+this._id+'"></ol>');
	this.addItems(it);
	$(this.id).css({
			'list-style-type':'none',
			'margin':'0',
			'padding':'0',
			'width':this['width'],
			'overflow': this.scroll?'auto':'hidden',
			'height': this['height']
		});
	$(this.id).bind('flclick',function(e){
		var $target = $(e.target).parent().parent();
		if(self.selectedId!=($target.attr('id'))) {
			try{
				if(self.items[$target.attr('id')]['onSelected']!=null) {
					if(self.items[$target.attr('id')]['onSelected'].call(this)==false) return false;
				}else if(self.onSelected.call(this,self.items[$target.attr('id')]['id'],self.items[$target.attr('id')]['data'],self.items[$target.attr('id')]['label'])==false) return false;
			}catch(e){	$$.msg(e);	}
			$target.css(self.selectedCSS);
			self.selectedId=$target.attr('id');
		}
		if(self.selectedId!=$target.attr('id') && self.selectedId!='') {
			$('#'+self.selectedId).css(self.unselectedCSS);
		}
	});
	$(this.id).bind('fldel',function(e){
		var $target = $(e.target).parent();
		if(self.items[$target.attr('id')]['onDel']!=null) {
			if(self.items[$target.attr('id')]['onDel'].call(this)!=false){
				if(self.selectedId==$target.attr('id')) {
					$target.css(self.unselectedCSS);
					self.selectedId='';
				}
				$target.remove();
			}
		}else{
			if(self.onDel.call(this,self.items[$target.attr('id')]['id'],self.items[$target.attr('id')]['data'],self.items[$target.attr('id')]['label'])!=false){
				$target.remove();
				if(self.selectedId==$target.attr('id')) {
					$target.css(self.unselectedCSS);
					self.selectedId='';
				}
			}
		}
	});
};

$$.override($$.FileList,{
	items: {}, // [ _id:{id,label:'',onSelected:function(),onDel:foo(),data:{}} ]
	selectedId: '',
	liClass: 'ui-widget-content',
	liCSS: {'margin': '3px','padding':'0.4em','font-size':'0.9em','height':'18px'},
	scroll: true,
	width: '100%',
	height: '100%',
	onSelected: function(id,data,label){$$.note(id);},   // return false for prevent delete
	onDel: function(id,data,label){}, // return false for prevent delete
	selectedCSS: {'background': '#F39814','color':'white'},
	unselectedCSS: {'background': '#FFFFFF','color':'black'},
	addItems: function(o){   // [ {id:'',label:'',onSelected:function(),onDel:foo(),position: before || after} ]
		if($$.isObject(o)){
			for(var i=0;i<o.length; i++){
				var id='flItem'+(Math.random()+'').substring(2,10);
				var ii='<li id="'+id+'" class="'+this.liClass+'"><span style="width:90%;overflow:hidden"><nobr>'+o[i]['label']+'</nobr></span><span class="ui-icon ui-icon-closethick"></span></li>';
				if($$.isDefined(o[i]['position'])) {
					if(o[i]['position']=='before') $(this.id).prepend(ii);
					if(o[i]['position']=='after') $(this.id).append(ii);
				}else $(this.id).append(ii);
				$('#'+id).css(this.liCSS);
				$('#'+id+' span').css('cursor','pointer');
				$('#'+id+' span:last').css({'float': 'right'}).click(function(e){$(e.target).trigger('fldel')});
				$('#'+id+' span:first').css({'float': 'left'}).click(function(e){$(e.target).trigger('flclick')});
				this.items[id]={id:o[i]['id']||null,'label':o[i]['label'],'onSelected':o[i]['onSelected']||null,'onDel':o[i]['onDel']||null,'data':o[i]['data']||null};
			}
//		alert($.param(o));
		}
	},
	empty: function(){
		this.selectedId='';
		this.items={};
		$(this.id).empty();
	},
	del:function(id){
		for(var v in this.items)
			if(this.items[v]['id']==id) {
				if(this.selectedId==v) {
					this.selectedId='';
				}
				$(this.id+' li#'+v).remove(); 
			}
	},
	setHTML: function(s){
		$(this.id).html(s);
	},
	select: function(id){
		var _id='';
		if($$.isDefined(id) && id!='') 
		  for(var v in this.items){
			if(this.items[v]['id']==id){
				_id=v;
				break;
			}
		  }
		if(this.selectedId!='') $(this.id+' li#'+this.selectedId).css(this.unselectedCSS);
		this.selectedId='';
		if(_id!=''){
			this.selectedId=_id;
			$(this.id+' li#'+this.selectedId).css(this.selectedCSS);
		}
	}
});
				


/*
B=function(){};
B=$$.extend(B,$$,{asd:1});
b=new B();
b.msg(b.asd);
*/
/*
C=function(){};
$$.BB=$$.extend(C,{asd:1,constructor:function(){}});
$$.b=new $$.BB();
$$.msg($$.b.asd);
*/

