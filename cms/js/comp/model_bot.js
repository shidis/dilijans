function SelectAll(mark,f) { 
  for (i = 0; i < document.forms[f].elements.length; i++)
     {
         var item = document.forms[f].elements[i];
	     if (item.id == "cc")  {
		     item.checked = mark;
		 };
	 }
}

function move_confirm(){
	return(window.confirm('Подтвердите объединение моделей. Вы хорошо подумали?'));
}
function other_confirm(){
	return(window.confirm('Подтвердите изменения выбранного свойства для выбранных моделей?'));
}
var mo={};

mo.atype={
	0:{
		img:'/cms/img/hi.gif',
		title:'не задан тип авто!'
	},
	1:{
		img:'/cms/img/legk.gif',
		title:'для легковых авто'
	},
	2:{
		img:'/cms/img/vnedor.gif',
		title:'для внедорожников'
	},
	3:{
		img:'/cms/img/van.gif',
		title:'для микроавтобусов'
	},
	4:{
		img:'/cms/img/legk_vnedor.gif',
		title:'легковой/внедорожник'
	}
}

mo.tsez={
	0:{
		img:'/cms/img/hi.gif',
		title:'не задан сезон!'
	},
	1:{
		img:'/cms/img/leto.gif',
		title:'лето'
	},
	2:{
		img:'/cms/img/zima.gif',
		title:'зима'
	},
	3:{
		img:'/cms/img/zimaleto.gif',
		title:'всесезонка'
	}
}

mo.dtype={
	0:{
		img:'/cms/img/hi.gif',
		title:'не задан тип диска!'
	},
	1:{
		img:'/cms/img/kovan.gif',
		title: 'кованый'
	},
	2:{
		img:'/cms/img/litoy.gif',
		title: 'литой'
	},
	3:{
		img:'/cms/img/shtamp.gif',
		title: 'штампованный'
	}
}

mo.ship={
	img:'/cms/img/ship.gif',
	title:'шип'
}

mo.atypeRC={
	sel:function(e){
		var el=$(e);
		var v=this.data.alias;
		$.ajax({
			data: {act:'change_atype',id:el.attr('mid'),atype:v},
			success: function(r){
				if(r.fres){
					$('.atype[mid='+el.attr('mid')+']')
						.html('<img src="'+mo.atype[v].img+'">')
						.parent().attr('title',mo.atype[v].title)
						.tooltip();
				}else note(r.fres_msg,'error');
			}
		});
	}
}
mo.atypeRC.option={ 
	width: 190, 
	leftclick:true,
	items: function(){
		var a=[];
		for(var k in mo.atype){
			if(k!=0)
				a.push(
					{text:mo.atype[k].title, icon:mo.atype[k].img, alias:k, action:mo.atypeRC.sel}
				);
		}
		return a;
	}()
	
}
	
mo.dtypeRC={
	sel:function(e){
		var el=$(e);
		var v=this.data.alias;
		$.ajax({
			data: {act:'change_dtype',id:el.attr('mid'),dtype:v},
			success: function(r){
				if(r.fres){
					$('.dtype[mid='+el.attr('mid')+'] img')
						.attr('src',mo.dtype[v].img)
						.parent().attr('title',mo.dtype[v].title)
						.tooltip();
				}else note(r.fres_msg,'error');
			}
		});
	}
}
mo.dtypeRC.option={ 
	width: 190, 
	leftclick:true,
	items: function(){
		var a=[];
		for(var k in mo.dtype){
			if(k!=0)
				a.push(
					{text:mo.dtype[k].title, icon:mo.dtype[k].img, alias:k, action:mo.dtypeRC.sel}
				);
		}
		return a;
	}()
	
}
	
	
$(document).ready(function(){

    $('.chosen, .chosen-s0').css({
        'margin':'2px 0 2px 0',
        border:'1px solid #BBBBBB'
    });

    $('.button0').css({
        'margin':'2px 3px 2px 0'
    });

    $('.button').button().css({
        'margin':'2px 3px 2px 0'
    });

	$.ajaxSetup({
		type:'POST',
		cache:false,
		dataType: 'json',
		url: '../be/model.php',
		error: Err
	});
	
	$('.tooltip').tooltip({
		track:true, fade:100, showURL:false
	});

	$('.sez').each(function(){
		var id=$(this).attr('sez');
		$(this).attr('title',mo.tsez[id].title).tooltip({
			track:true, fade:100, showURL:false
		}).append('<img src="'+mo.tsez[id].img+'">');
	});
	
	$('.ship').each(function(){
		if($(this).attr('ship')==1)
			$(this).attr('title',mo.ship.title).tooltip({
				track:true, fade:100, showURL:false
			}).append('<img src="'+mo.ship.img+'">');
	});
	
	$('.atype').each(function(){
		var id=$(this).attr('atype');
		$(this).css({width:'100%',display:'block','text-align':'center',height:'17px'});
		$(this).attr('title',mo.atype[id].title).tooltip({
			track:true, fade:100, showURL:false
		});
		var s_id=$(this).attr('suggest');
		if(s_id=='') $(this).html('<img src="'+mo.atype[id].img+'">');
		else $(this).html('<img style="opacity:0.3;'+(id ? 'border:1px dashed red' : '')+'" src="'+mo.atype[s_id].img+'">');
		$(this).contextmenu(mo.atypeRC.option);
	});
	
	$('.dtype').each(function(){
		var id=$(this).attr('dtype');
		$(this).css({width:'100%',display:'block','text-align':'center',height:'17px'});
		$(this).attr('title',mo.dtype[id].title).tooltip({
			track:true, fade:100, showURL:false
		}).html('<img src="'+mo.dtype[id].img+'">');
		$(this).contextmenu(mo.dtypeRC.option);
	});
	
	
	$('tr.inds td').css('background','#63FF94');
	
	var loader=$('.workspace').cloader();
	$(document).ajaxStart(function() {
		loader.cloader('show');
	})
	.ajaxStop(function() {
	  	loader.cloader('hide');
	});
	
	$('form').submit(function(e){
		loader.cloader('show');
	});
	
	
	$('#showGroupOp').click(function(e){
		$(this).parent().hide();
		$('#groupOp').slideDown('fast');
		$.fn.setCookie('__cp_model_showGroupOp',1);
		e.preventDefault();
	});
	
	$('#hideGroupOp').click(function(e){
		e.preventDefault();
		$('#groupOp').slideUp('fast');
		$.fn.delCookie('__cp_model_showGroupOp');
		$('#showGroupOp').parent().show();
	});

	$('.medit').click(function(e){
		e.preventDefault();
		var id=$(this).parent().parent('tr').attr('id');
		id=id.split('_');
		id=id[1];
		if(id!=null) {
			$('[name=medit_id]').val(id);
			$('#form1').get(0).submit();
		}
	});
		
		
	$('.ealt').bind('click',function(e){
		var v=prompt('Альтернативное название модели',$(this).attr('title'));
		var id=$(this).parent().parent('tr').attr('id');
		id=id.split('_');
		id=id[1];
		if(v!=null && id>0) {
			var self=this;
			$.ajax({
				data: {act: 'saveModelAlt', 'id':id, 'v':v},
				success: function(r){
					if(r.fres){
						$(self).attr('title',r.v);
						if(r.v!='') $(self).addClass('bold'); else $(self).removeClass('bold');
					}else note(r.fres_msg,'error');
				}
			});
		}
		e.preventDefault();
	});


	$(document).on('click','a.h-sw',function(e){
		e.preventDefault();
		var td=$(this).parent();
		var id=$(this).parent().parent('tr').attr('id');
		id=id.split('_');
		id=id[1];
		var s=td.html();
		if(id>0){
			td.html(loading2);
			$.ajax({
				data: {act:'hSwitch', 'id':id},
				success:function(r){
					if(r.fres){
						td.html(r.v);
					}else {
						td.html(s);
						note(r.fres_msg,'error');
					}
				}
			});
		} else note('нет ИД','note');
	});
	
	$('.mdel').click(function(e){
		e.preventDefault();
		var id=$(this).parent().parent('tr').attr('id');
		id=id.split('_');
		id=id[1];
		if(id!=null) {
			$('[name=id]').val(id);
			$('[name=act]').val('mdel');
			$('#form1').get(0).submit();
		}
	});
			
	var selRow={color:'',id:''};

	$('.ltable td').bind('mouseover',function(e){
//		alert($(e.target).parent().attr('cat_id'));
		var id=$(e.target).closest('tr').attr('id');
		id=id.split('_');
		id=id[1];
		if(selRow.id==''){
			selRow.id=id;
			selRow.color=$(e.target).closest('tr').children('td').css('background-color');
		}else{
			$('.ltable tr[id=t_'+selRow.id+'] td').css({'background-color':selRow.color});
			selRow.color=$(e.target).closest('tr').children('td').css('background-color');
			selRow.id=id;
		}
		$('.ltable tr[id=t_'+selRow.id+'] td').css({'background-color':'#bbbbbb'});
	});

    $('.suplrList').click(suplrList);
    $('.catList').click(catList);

	$('#model_sticker_type').change(function(){
		var allow_text = $(this).find('option:selected').attr('allow_text');
		if (allow_text){
			$('#model_sticker_text').show();
		}
		else{
			$('#model_sticker_text input').val('');
			$('#model_sticker_text').hide();
		}
	});
});

function suplrList(e)
{
    var model_id=$(this).parents('tr').attr('mid');

    $('<div id="suplrListDlg" title="Все поставщики модели"></div>')
        .appendTo('body')
        .dialog({
            autoOpen:true,
            modal:true,
            resizable:true,
            closeOnEscape:true,
            height: 'auto',
            width:500,
            position: { my: "left top", at: "left top", of: e.target},
            close: function()
            {
                $(this).dialog('destroy').remove();
            },
            buttons:[
                {
                    text: 'Закрыть',
                    click: function()
                    {
                        $(this).dialog('close');
                    }
                }
            ],
            open: function()
            {
                $(this).html('<img src="/assets/images/ax/siteheart.gif" align="middle">&nbsp;&nbsp;&nbsp;Загружаюсь...');
                var $w=$(this);
                $.ajax({
                    data:{
                        act: 'suplrList',
                        model_id: model_id
                    },
                    success: function (r)
                    {
                        if(r.fres){
                            var s='<h3>Поставщики модели &quot;'+ r.modelName+'&quot;:</h3><ul>';
                            var i=0;
                            for(var k in r.suplrs){
                                i++;
                                s+='<li>'+ r.suplrs[k]+'</li>';
                            }
                            s+='</ul>';

                            if(!i) s+="<p>нет поставщиков в нашей базе.</p>";

                            $w.html(s);
                        }else {
                            $w.dialog('close');
                            err(r.fres_msg);
                        }
                    }
                })
            }

        });

    return false;
}

function catList(e)
{
    var model_id=$(this).parents('tr').attr('mid');
    $(this).attr('href','/cms/frm/catalog_bot.php?gr='+window.gr+'&modelId='+model_id).attr('target','_blank');

}