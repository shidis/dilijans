$('document').ready(function(){
	
	var lastsel1,lastsel2; 
	$('#grid1').jqGrid({
		hidegrid:false,
		datatype: 'json',
		url: '../be/min_extra.php?act=list&gr=1&P=1',
		editurl: '../be/min_extra.php?act=update&gr=1&P=1',
		colNames:['Радиус','Минимальная наценка'],
		colModel:[
				  {name:'PVal',index:'PVal',align:'center',width:50},
				  {name:'extra',index:'extra',align:'center',width:50, editable:true},
				], 
		caption:'Наценки на шины',
		sortname:'PVal',
		sortorder: "asc",
		rownumbers: true, 
		height:'100%',
//		viewrecords:true,
//		rowNum:50,
//		rowList:[10,20,30,50,100,200,300,1000],
//		autowidth: true,
//		pager: '#pagered1',
		width:300,
		loadError: Err,
		loadComplete: loadComplete,
		onSelectRow: function(id){ 
			if(lastsel1) jQuery('#grid1').jqGrid('restoreRow',lastsel1); 
			jQuery('#grid1').jqGrid('editRow',id,true,null,function(xhr){
				if(xhr.readyState==4){
					if(xhr.responseText==0) $('#grid1').trigger('reloadGrid'); else return true;
				}
			}); 
			lastsel1=id; 
		}
	});
//	jQuery("#pagered1").jqGrid('navGrid',"#pagered",{edit:true,add:false,del:false}); 

	$('#grid2').jqGrid({
		hidegrid:false,
		datatype: 'json',
		url: '../be/min_extra.php?act=list&gr=2&P=5',
		editurl: '../be/min_extra.php?act=update&gr=2&P=5',
		colNames:['Радиус','Минимальная наценка'],
		colModel:[
				  {name:'PVal',index:'PVal',align:'center',width:50},
				  {name:'extra',index:'extra',align:'center',width:50, editable:true},
				], 
		caption:'Наценки на диски',
		sortname:'PVal',
		sortorder: "asc",
		rownumbers: true, 
		height:'100%',
//		viewrecords:true,
//		rowNum:50,
//		rowList:[10,20,30,50,100,200,300,1000],
//		autowidth: true,
//		pager: '#pagered2',
		width:300,
		loadError:Err,
		loadComplete: loadComplete,
		onSelectRow: function(id){ 
			if(lastsel2) jQuery('#grid2').jqGrid('restoreRow',lastsel2); 
			jQuery('#grid2').jqGrid('editRow',id,true,null,function(xhr){
				if(xhr.readyState==4){
					if(xhr.responseText==0) $('#grid2').trigger('reloadGrid'); else return true;
				}
			}); 
			lastsel2=id; 
		}
	});
	//jQuery("#pagered2").jqGrid('navGrid',"#pagered",{edit:true,add:false,del:false}); 

});

