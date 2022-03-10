var extra={};
var sezon={1:'ЛЕТО',2:'ЗИМА',3:'ВСЕСЕЗОННЫЕ'};

extra.seztab=false;

extra.lastsel_sez=[];

$(document).ready(function(){

	$.ajaxSetup({
		type:'POST',
		cache:false,
		dataType: 'json',
		url: '../be/extra.php&gr='+extra.gr,
		error: Err,
//		beforeSend: ajaxBeforeSend,
		complete: ajaxComplete
	});

	$('button#close').button();
	
	$('.extra #grid1').jqGrid({
		hidegrid:false,
		datatype: 'json',
		url: '../be/extra.php?act=list_r&gr='+extra.gr+'&brand_id='+extra.brand_id,
		editurl: '../be/extra.php?act=update&extra_group=1&gr='+extra.gr+'&brand_id='+extra.brand_id,
		colNames:['Радиус','Наценка, %','не менее, руб'],
		colModel:[
				  {name:'R',index:'R',align:'center',width:50,sortable:false},
				  {name:'extra',index:'extra',align:'center',width:50, editable:true,sortable:false},
				  {name:'minExtra',index:'minExtra',align:'center',width:50, editable:true,sortable:false}
				], 
		caption:'Наценки на радиус',
		rownumbers: false, 
		height:380,
		width:300,
		loadError: Err,
		scroll:1,
		loadComplete: loadComplete,
		onSelectRow: function(id){ 
			if(extra.lastsel1) jQuery('.extra #grid1').jqGrid('restoreRow',extra.lastsel1); 
			jQuery('.extra #grid1').jqGrid('editRow',id,true,null,function(xhr){
				if(xhr.readyState==4){
					if(xhr.responseText==0) $('.extra #grid1').trigger('reloadGrid'); else return true;
				}
			}); 
			extra.lastsel1=id; 
		}
	});
	$('.extra #grid2').jqGrid({
		hidegrid:false,
		datatype: 'json',
		url: '../be/extra.php?act=list_sup&gr='+extra.gr+'&brand_id='+extra.brand_id,
		editurl: '../be/extra.php?act=update&extra_group=2&gr='+extra.gr+'&brand_id='+extra.brand_id,
		colNames:['Поставщик','Наценка, %','не менее, руб'],
		colModel:[
				  {name:'name',index:'name',align:'center',width:50,sortable:false},
				  {name:'extra',index:'extra',align:'center',width:50, editable:true,sortable:false},
				  {name:'minExtra',index:'minExtra',align:'center',width:50, editable:true,sortable:false}
				], 
		caption:'Наценки на поставщика',
		rownumbers: false, 
		height:380,
		width:300,
		loadError: Err,
		scroll:1,
		loadComplete: loadComplete,
		onSelectRow: function(id){ 
			if(extra.lastsel2) jQuery('.extra #grid2').jqGrid('restoreRow',extra.lastsel2); 
			jQuery('.extra #grid2').jqGrid('editRow',id,true,null,function(xhr){
				if(xhr.readyState==4){
					if(xhr.responseText==0) $('.extra #grid2').trigger('reloadGrid'); else return true;
				}
			}); 
			extra.lastsel2=id; 
		} 
	});

    // наценка на сезон и радиус
    for(var i=1;i<=3;i++){
        sez_grid(i);
    }

    $('#tabs').tabs();



});

function sez_grid(i){
    extra.lastsel_sez[i]=0;
    $('.extra-sez').append('<div id="pagered'+i+'"></div><div style="width:310px; float:left; overflow:hidden; margin-right:20px;"><table id="egrid'+i+'"></table></div>');
    $('.extra-sez #egrid'+i).jqGrid({
        hidegrid:true,
//			hiddengrid: true,
        datatype: 'json',
        mtype: 'POST',
        url: '../be/extra.php?act=list_sez&gr='+extra.gr+'&brand_id='+extra.brand_id+'&S_value='+i,
        editurl: '../be/extra.php?act=update_sez&extra_group=3&gr='+extra.gr+'&brand_id='+extra.brand_id+'&S_value='+i,
        colNames:['Радиус','Наценка, %','не менее, руб'],
        colModel:[
            {name:'R',index:'R',align:'center',width:50,sortable:false},
            {name:'extra',index:'extra',align:'center',width:50, editable:true,sortable:false},
            {name:'minExtra',index:'minExtra',align:'center',width:50, editable:true,sortable:false}
        ],
        caption: sezon[i],
        rownumbers: false,
        height:380,
        width:300,
        loadError: Err,
        scroll:1,
        loadComplete: loadComplete,
        onSelectRow: function(id){
            if(extra.lastsel_sez[i]) jQuery('.extra-sez #egrid'+i).jqGrid('restoreRow',extra.lastsel_sez[i]);
            jQuery('.extra-sez #egrid'+i).jqGrid('editRow',id,true,null,function(xhr){
                if(xhr.readyState==4){
                    if(xhr.responseText==0) $('.extra-sez #egrid'+i).trigger('reloadGrid'); else return true;
                }
            });
            extra.lastsel_sez[i]=id;
        }
    });
};

