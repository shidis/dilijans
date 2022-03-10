/*
	QMark Lib
*/

(function($) {
	
	/* Функция, лежащая в поле под именем _create, служит конструктором, и будет вызвана при создании инстанса виджета; на этот инстанс и указывает this.
this.element — это элемент, на который был навешен виджет. Это всегда одиночный элемент, а не коллекция (как в случае обычных плагинов); если навешивать виджет на jQuery-объект, который содержит больше одного элемента, то будет создано столько инстансов, сколько элементов.
В поле options хранятся дефолтные настройки виджета. Это поле наследуется, так что оно всегда будет в виджете, даже если не объявлять его явно.
Если при вызове виджета передать объект, то переданный объект будет «смерджен» (с помощью метода $.merge) с дефолтными настройками еще до вызова _create.
За работу с настройками отвечает метод setOption*/
	
$.widget('my.cloader', {
	
	options: {
		title:'Загрузка...',
		width:'90px',
		height:'25px',
		background:'red',
		color:'white',
		font:'Arial 12px bold',
		line_height:'23px'
	},
	
	_create: function(){
		this._render();
	},
	
	id:0,
	
	_render: function(){

		if(this.id!=0) $('#'+this.id).remove(); else this.id='el'+$.fn.randomInt(1,10000);
		this.element.append('<div id="'+this.id+'" style="display:none; position:fixed; top:0; right:0;width:'+this.options.width+';height:'+this.options.height+';background:'+this.options.background+';color:'+this.options.color+';font:'+this.options.font+';line-height:'+this.options.line_height+';text-align:center">'+this.options.title+'</div>');

	},
	
	show: function(){
		$('#'+this.id).show();
		return this;
	},

	hide: function(){
		$('#'+this.id).hide();
		return this;
	},

	setOption: function(key, value) {
		if (value != undefined) {
		  this.options[key] = value;
		  this._render();
		  return this;
		}
		else {
		  return this.options[key];
		}
	  }
	
});
	
	
})(jQuery);

$(document).ready(function(){
    if(jQuery().dialog){
        $('body').prepend('<div id="errorDlg" title="Ошибка"></div>');
        $('#errorDlg').dialog({
            autoOpen:false,
            modal:false,
            resizable:true,
            closeOnEscape:true,
            height: 320,
            width:600,
            buttons: {
                'Закрыть': function() {
                    $( this ).dialog( "close" );
                }
            }
        });
    }
});
