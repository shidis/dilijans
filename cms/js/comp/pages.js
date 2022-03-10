var pages={};

$('document').ready(function(){
	
	$.ajaxSetup({
		type:'POST',
		global: true,
		cache:false,
		dataType: 'json',
		url: '../be/pages.php',
		error: Err,
		complete: ajaxComplete,
		beforeSend: ajaxBeforeSend
	});
	

	$('#page_edit_form').submit(function(e) { 
		e.preventDefault();
		$('input[name=ed_text0]').val($('#ed_text').html());
		$.ajax({
			   data: {act:'save',f:$('#page_edit_form').serialize()},
			   success: function (res){
					if(!res.fres) note(res.fres_msg,'stick'); else {
						$('#grid').trigger('reloadGrid');
						$('#pages_edit').fadeOut('fast');
						$('#pages_list').fadeIn('fast');
						$('#ed_text').html('');
					}
			   }
		});
	}); 

 
 	$.ajax({
		data: {act:'init'},
		success: function (r){
			if(r.fres){
				pages.blocks=r.blocks;
                logit(pages.blocks);
				$('#grid').jqGrid({
					hidegrid:false,
					datatype: 'json',
					url: '../be/pages.php?act=list',
					type: 'POST',
					colNames:['#','Блок','Url','Параметры','Строго','Title','K&D','Заголовок блока','Порядок',''],
					colModel:[
							  {name:'page_id',index:'page_id',align:'center',width:20,search:false},
							  {name:'block_id',index:'block_id',align:'center',width:70,stype:'select',searchoptions: {value:pages.blocks}},
							  {name:'url',index:'url',align:'left'},
							  {name:'param',index:'param',align:'center',width:100},
							  {name:'strict',index:'strict',align:'center',width:30,search:true,stype:'select',searchoptions:{value:{'-1':'Все',0:'нет',1:'да'},defaultValue:-1}},
							  {name:'title',index:'title',align:'left'},
							  {name:'kd',index:'kd',align:'center',search:false, width:20},
							  {name:'header',index:'header',align:'left'},
							  {name:'pos',index:'pos',align:'center',width:30},
							  {name:'ctrl',index:'ctrl',align:'center',sortable:false,width:30,search:false}
							], 
					caption:'Список страниц',
					sortname:'url',
					sortorder: "asc",
					viewrecords:true,
					height:'100%',
					rowNum:50,
					rowList:[10,20,30,50,100,200,300,1000],
					autowidth: true,
					pager: '#pagered',
					loadComplete: function(){
						var ids = jQuery("#grid").getDataIDs();
						var d;
						for(var i=0;i<ids.length;i++){
							d = jQuery("#grid").getRowData(ids[i]);
							d.ctrl = "<input type='image' src='/cms/img/delete.gif' title='удалить' onclick=_del("+ids[i]+",'#grid'); >"; 
							d.url="<a href='javascript:;' title='редактировать' onClick=\"_edit("+ids[i]+",'#grid');\" >"+d.url+'</a>';
							d.block_id="<a href='javascript:;' title='редактировать' onClick=\"_edit("+ids[i]+",'#grid');\" >"+d.block_id+'</a>';
							d.title="<a href='javascript:;' title='редактировать' onClick=\"_edit("+ids[i]+",'#grid');\" >"+d.title+'</a>';
							d.header="<a href='javascript:;' title='редактировать' onClick=\"_edit("+ids[i]+",'#grid');\" >"+d.header+'</a>';
							d.strict='<input disabled="disabled" type="checkbox" '+(d.strict==1?'checked="checked"':'')+'>';
							d.param="<a href='javascript:;' title='редактировать' onClick=\"_edit("+ids[i]+",'#grid');\" >"+d.param+'</a>';
							jQuery("#grid").setRowData(ids[i],d);
						}
					}
				}).jqGrid('filterToolbar',{
					stringResult: true,
					searchOnEnter : false
				});
			} else note(r.fres_msg,'error');
		}
	});
	
	$('#new_page').click(function(){
		_new();
		return false;
	});
	
	$('.sbmt').button();
	
	$('.f_cancel').button().click(function(e){
		e.preventDefault();
		$('#pages_edit').fadeOut('fast');
		$('#pages_list').fadeIn('fast');
	});
	
	$('#get_title').click(function(){
		if($('[name=ed_url]').val()=='') return false;
		$(this).fadeOut('fast');
		$('[name=ed_title]').attr('disabled','disabled');
		$('#get_title').after(loading2);
		$.ajax({
			   data:{act:'query',url:$('[name=ed_url]').val(),strict:$('[name=ed_strict]').prop('checked'),param:$('[name=ed_param]').val(),pos:$('[name=ed_pos]').val()},
			   success: function(res){
				   
				   if(res.fres) $('[name=ed_title]').val(res.title);
			   		else note(res.fres_msg,'error');
			   		
					$('#get_title').next('#loading2').remove();
					$('#get_title').fadeIn('fast');
					$('[name=ed_title]').removeAttr('disabled');
			   },
			   complete: efoo, beforeSend: efoo
		});
	});
	
	$('input[type=button], input[type=submit]').addClass('ui-state-default ui-corner-all ui-a');
	
	$('#show_help').click(function(e){
		$(this).hide();
		e.preventDefault();
		$('#help').slideDown('fast');
	});

	overlay(1);
	
	$('#ed_text').tinymce(
		$.extend(
			TM.cfg1,{
				oninit: function(){
					overlay(0);
				}
			}
		)
	);
	
	
});


function _del(ids,c){
	$.ajax({
		data: {act:'del',id: ids},
		success: function (res){
			if(!res.fres) note(res.fres_msg,'stick'); else {
				jQuery($(c)).delRowData(ids);
				note('Удалено id='+ids+' OK');
			}
		}
	});
};


function _edit(ids,c){
	$('[name=ed_page_id]').val(ids);
	$.ajax({
		   data:{act:'get_page',id:ids},
	   		success:function(res){
				if(res.fres){
					$('#ed_text').html(res.text);
			   		$('#get_title').next('#loading2').remove();
					$('#get_title').show();
					$('[name=ed_url]').val(res.url);
					$('[name=ed_title]').val(res.title);
					$('[name=ed_keywords]').val(res.keywords);
					$('[name=ed_description]').val(res.description);
					$('[name=ed_header]').val(res.header);
					$('[name=ed_param]').val(res.param);
					$('[name=ed_pos]').val(res.pos);
					$('[name=ed_page_id]').val(res.page_id);
					if(res.strict==1) $('[name=ed_strict]').prop('checked',true);
						else $('[name=ed_strict]').prop('checked',false);
					$('[name=ed_block_id]').html(res.block_list);
					$('#pages_list').fadeOut('fast');
					$('#pages_edit').fadeIn('fast');
					window.scroll(0,0);
				}else note(res.fres_msg,'error');
   			}
	});
}

function _new(){
	$('#ed_text').html('');
	$('[name=ed_url]').val('');
	$('[name=ed_title]').val('');
	$('[name=ed_description]').val('');
	$('[name=ed_keywords]').val('');
	$('[name=ed_header]').val('');
	$('[name=ed_param]').val('');
	$('[name=ed_pos]').val('0');
	$('[name=ed_page_id]').val(0);
	$('[name=ed_strict]').prop('checked',true);
	$('#get_title').next('#loading2').remove();
	$('#get_title').show();
	if($('[name=ed_block_id]').html()==''){
		$.ajax({
		   data:{act:'get_block_list'},
	   		success:function(res){
				if(res.fres){
					$('[name=ed_block_id]').html(res.block_list);
				}else note(res.fres_msg,'error');
			}
		});
	}
	$('#pages_list').fadeOut('fast');
	$('#pages_edit').fadeIn('fast');
}

