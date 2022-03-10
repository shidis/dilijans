var mx={};
mx.gridLoading=false;
mx.gridUpdatePQ=false; // есть отоженные запросы на обновление грида
mx.deletedIds=[];


$(document).ready(function(){


	$.ajaxSetup({
		type:'POST',
		cache:false,
		dataType: 'json',
		url: '../be/s-matrix.php',
		error: Err
		/*,beforeSend: ajaxBeforeSend,
		complete: ajaxComplete*/
	});
	
	var loader=$('.workspace').cloader();
	$(document).ajaxStart(function() {
		loader.cloader('show');
	})
	.ajaxStop(function() {
	  	loader.cloader('hide');
	});

	mx.reloadGrid();
	
	$('#mx #post').button().click(mx.postFrm);
	$('#mx #clear').button().click(function(e){
		e.preventDefault()
		$('#mx form#frm').get(0).reset();
		mx.gridUpdatePQ=true;
	});
	$('#mx #gridRefresh').button().click(function(e){
		e.preventDefault()
		mx.gridUpdatePQ=true;
	});
	
	$('#mx form#frm').submit(mx.postFrm);
	
	$('#mx #cSuffix').bind('keyup',function(e){
		if(isPrintableASCII(e.keyCode)) mx.gridUpdatePQ=true;
	});
	
	mx.gridUpdateTimer=setInterval(function(){
		if(mx.gridUpdatePQ && !mx.gridLoading) {
			$('#mxGrid').jqGrid('setGridParam',{'postData':{ext_cSuffix:$('#mx #cSuffix').val()}}).trigger('reloadGrid');
			mx.gridUpdatePQ=false;
		}
	},800);

	mx.catDlg=$('#mx #mxCatDlg').dialog({
		autoOpen:false,
		modal:true,
		resizable:true,
		closeOnEscape:true,
		height: 600,
		width:800,
		buttons: {
			'Закрыть': function() {
				$( this ).dialog( "close" );
			}
		}
	});

    var $brands=$('#mx select[name=brand_id]');

    $brands
        .css({'width': '95%'})
        .chosen({
            disable_search_threshold: 20
        })
	

});

mx.postFrm=function(e){
	e.preventDefault();
	$.ajax({
		data: {act:'post', frm:$('#mx #frm').serialize()},
		success: function(r){
			if(r.fres){
				note(r.fres_msg);
				$('#mxGrid').trigger('reloadGrid');	
			}else note(r.fres_msg,'long');
		}
	});
}


mx.reloadGrid = function (){
	$('#mx #gridWrapper').html('<div id="mxPager"></div><table id="mxGrid"></table>');
	$('#mxGrid').jqGrid({
		url: '../be/s-matrix.php?act=list',
		editurl: '../be/s-matrix.php?act=mod',
		colNames: function (){
			var a=[];
			a.push('');
			a.push('key');
			a.push('Базовый суффикс');
			if(CIM==2){
                a.push('Тег');
				a.push('Суффикс для сайта');
				a.push('Синонимы для импорта');
				a.push('Длинный суффикс');
			}else if(CIM==3){
				a.push('Синонимы для импорта');
				a.push('Суффикс для сайта');
			}
			a.push('Дата добавления');
			return a;
		}(),
		colModel: function (){
			var a=[];
			a.push({name: 'actions', width:73, fixed:true, sortable:false, resize:false}),
			a.push({name:'key', index:'key', hidden: true, editable:true});
			a.push({name:'cSuffix',index:'cSuffix',align:'left',width:230,sortable:false,editable: false, key:true});
			if(CIM==2){
                a.push({name:'tag',index:'tag',align:'center',width:250, hidden: false,sortable:false,editable: true});
				a.push({name:'suffix1',index:'suffix1',align:'center',width:250, hidden: false,sortable:false,editable: true});
				a.push({name:'iSuffixes',index:'iSuffixes',align:'center',width:350, hidden: false,sortable:false,editable: true});
                a.push({name:'suffix2',index:'suffix2',align:'center',width:350, hidden: false,sortable:false,editable: true});
			}else if(CIM==3){
				a.push({name:'iSuffixes',index:'iSuffixes',align:'center',width:350, hidden: false,sortable:false,editable: true});
				a.push({name:'suffix1',index:'suffix1',align:'center',width:250, hidden: false,sortable:false,editable: true});
			}
			a.push({name:'dt_added',index:'dt_added',align:'center',width:150, hidden: false,sortable:false,editable: false});
			return a;
		}(),
		caption: 'Матрица суффиксов (цветов)',
		hidegrid:false,
		datatype: 'xml',
		mtype: "POST",
		treeGridModel : 'adjacency', 
		ExpandColumn : 'cSuffix',
		ExpandColClick: true,
		treeGrid: true,
//		rownumbers: true, 
		height:'100%',
		autowidth: true,
//		width:'100%',
		shrinkToFit: true,
//		loadonce:true,
		pager: '#mxPager',
		viewrecords:true,
		loadError:Err
		,loadComplete: function(){
			mx.gridLoading=false;
		}
		,beforeRequest: function(){
			mx.gridLoading=true;
		}
		,beforeSelectRow: function (rowid,e){
			var s=str_replace('<sub>','',rowid);
			s=str_replace('</sub>','',s);
			
			if(mx.deletedIds.indexOf(s)!=-1) {
				logit('--');
				return false;
			}
			return true;
		}
		,onRightClickRow: function(rowid,iRow,iCol,e){
			var d = jQuery("#mxGrid").getLocalRow(rowid);
			logit(d);
			return false;
		}
		,onSelectRow: function(id){ 
			if(id){
				var d = jQuery("#mxGrid").getRowData(id);
				if(mx.lastsel) jQuery('#mxGrid').jqGrid('restoreRow',mx.lastsel); 
				jQuery('#mxGrid').jqGrid('editRow',id,true,null,function(xhr){
					if(xhr.readyState==4){
						if(xhr.responseText=='0') $('#mxGrid').trigger('reloadGrid'); else {
							return true;
						}
					}
				}); 
				mx.lastsel=id; 
			}
		}
	}).jqGrid('navGrid',"#mxPager",{
		edit:false,add:false,del:false,search:false
	}).trigger('reloadGrid'); 
}

mx.delete=function(id){
	$.ajax({
		data: {act:'delete', id:id},
		success: function(r){
			if(r.fres){
				note('Удалено');
				mx.deletedIds.push(id);
				logit(id);
				jQuery('#mxGrid').jqGrid('restoreRow',mx.lastsel);
				var s=str_replace('|','<sub>|',id);
				s=s+'</sub>';
				var d=$('#mxGrid').jqGrid('getRowData',s);
				logit(d);
				d.actions='';
				d.cSuffix='--удалено--';
				if(CIM==2){
                    d.tag='--удалено--';
                    d.suffix1='--удалено--';
					d.suffix2='--удалено--';
					d.iSuffixes='--удалено--';
				}else if(CIM==3){
					d.iSuffixes='--удалено--';
					d.suffix1='--удалено--';
				}
				d.dt_added='--удалено--';
				$('#mxGrid').jqGrid('setRowData',s,d,{'backgroud':'#cc000'});
			}else note(r.fres_msg,'long');
		}
	});
	return false;
}

mx.cat=function(id){

	jQuery('#mxGrid').jqGrid('restoreRow',mx.lastsel);
	$.ajax({
		data: {act:'getCatData', id:id},
		success: function(r){
			if(r.fres){
				mx.catDlg.dialog('option','title',r.title);
				$("#mxCatDlg").html('');
				var i=0;
				for(var brand in r.tbl){
					if(i==0) {
						$("#mxCatDlg").append('<p>изменять урл при изменении суффикса <input type="checkbox" value=1 id="remakeUrl"></p>');
						$("#mxCatDlg").append('<table></table>');
//						$("#mxCatDlg table").append('<tr><th>Наименование</th><th>Суффикс размера</th></tr>');
					}
					i++;
					$("#mxCatDlg table").append('<tr><th colspan=2><b>'+brand+'</b></th></tr>');
					for(var model_id in r.tbl[brand]){
						for(var cat_id in r.tbl[brand][model_id]){
							var c=r.tbl[brand][model_id][cat_id];
							$("#mxCatDlg table").append('<tr><td>'
								+brand
								+' '
								+c['mname']
								+' '
								+(c['msuffix']!=''?('<nobr><span class="msufClk" id="'+model_id+'">'+c['msuffix']+'</span></nobr>'):'')
								+' '
								+c['tipo']
								+'</td><td>'
								+'<nobr><span class="csufClk" id="'+cat_id+'">'+c['csuffix']+'</span></nobr>'
								+'</td></tr>'
							);
						}
					}
				}
					
				$("#mxCatDlg .csufClk").editable("../be/s-matrix.php?act=saveCSuffix", { 
					indicator : "<img src='../img/loader.white.gif'>",
					tooltip   : "Клик для изменения",
					style  : "inherit",
					cancel    : 'Отмена',
					submit    : 'OK',
					callback : function(value, settings) {},
					submitdata : function(value, settings) {
						// value - текущее значение поля ввода
						// settings.id - id поля ввода
						// отправятся данные: id,value, и то что будет в массиве return этой функции
						//return {foo: settings};
						return {'remakeUrl':$('#mxCatDlg #remakeUrl').prop('checked')};
					}
				});
				$("#mxCatDlg .msufClk").editable("../be/s-matrix.php?act=saveMSuffix", { 
					indicator : "<img src='../img/loader.white.gif'>",
					tooltip   : "Клик для изменения",
					style  : "inherit",
					cancel    : 'Отмена',
					submit    : 'OK',
					submitdata : function(value, settings) {
						return {'remakeUrl':$('#mxCatDlg #remakeUrl').prop('checked')};
					}
				});
				$('#mxCatDlg table td').css({'padding':'3px 15px 3px 15px','border':'1px solid #CCC'});
				$('#mxCatDlg table th').css({padding:'10px 0 3px 0','border':'0','text-align':'left'});
				$('#mxCatDlg .csufClk').css({'display':'inline-block','border-bottom':'1px dashed #333','border-bottom':'1px solid #CCC','font-weight':'bold','min-width':'90px'});
				$('#mxCatDlg .msufClk').css({'border-bottom':'1px dashed #333','border':'1px solid #CCC','font-weight':'bold','min-width':'70px','color':'#006'});
				
				if(i==0) $("#mxCatDlg").append('<p>Не типоразмеров с таким цветом</p>');
				
				mx.catDlg.dialog('open');
				
			}else note(r.fres_msg,'long');
		}
	});
	return false;
}