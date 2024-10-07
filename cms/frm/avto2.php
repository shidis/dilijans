<? 

require_once '../auth.php';
include('../struct.php');

$cp->frm['name']='avto2';
$cp->frm['title']='База подбора шин и дисков по марке автомобиля';

$cp->checkPermissions();

cp_head();
cp_css();
cp_js();
?>

<style type="text/css">
.choice{ float:left; margin-right:20px}
option.h{color:#999999}
#tables{margin-top:15px}
.nav1, .nav2{margin-top:10px}
.add,.edit,.del{margin-right:5px; vertical-align:baseline}
H1.h{font-size:16px;}
.manual_insert{
		background: yellow;
		font-weight: 700;
	}
</style>

<script language="javascript">

var gridimgpath='../css/jqgrid/basic/images';

$('document').ready(function(){

try{	
	$.ajaxSetup({
		type:'POST',
		global: true,
		cache:false,
		dataType: 'json',
//		timeout:1000,
		url: '../be/avto2.php',
		error: Err
	});
		
	var modif_id;
	var lastsel2;

	$('#vendor').change(vendorChange);

	jQuery('#tyres1').jqGrid({
			datatype: 'local',
			caption: 'Шины',
			imgpath: gridimgpath,
			colNames: ['#','Позиция','Удалить','Тип','Ширина','Высота','Радиус','','Ширина','Высота','Радиус',''],
			colModel: [
				{name:'id',index:'id',align:'center', sortable:false},
				{name:'pos',index:'pos',align:'center', sortable:true,editable:true,edittype:'text', editoptions:{size: 3}},
				{name:'act',index:'act',align:'center', sortable:false},
				{name:'type',index:'type',align:'center', width:230, sortable:false,editable:true,edittype:"select",editoptions:{value:"zavod:заводская;zamena:алтернатива"}},
				{name:'p3',index:'p3',align:'center', sortable:false,editable:true, edittype:'text'},
				{name:'p2',index:'p2',align:'center', sortable:false,editable:true, edittype:'text'},
				{name:'p1',index:'p1',align:'center', sortable:false,editable:true, edittype:'text'},
				{name:'os',index:'os',align:'center', width:200, sortable:false},
				{name:'p3_1',index:'p3_1',align:'center', sortable:false,editable:true, edittype:'text'},
				{name:'p2_1',index:'p2_1',align:'center', sortable:false,editable:true, edittype:'text'},
				{name:'p1_1',index:'p1_1',align:'center', sortable:false,editable:true, edittype:'text'},
				{name:'manual_insert',index:'manual_insert',hidden:true,editable:true, edittype:'text'}
			],
			loadComplete: function(data){
				var ids = jQuery("#tyres1").getDataIDs();
				for(var i=0;i<ids.length;i++){ 
					var cl = ids[i]; 
					de = "<input title='Удалить' type='image' src='../img/row_delete.gif' onclick='_del("+cl+",\"#tyres1\");' >"; 
					jQuery("#tyres1").setRowData(ids[i],{act:de})
					var dataFromTheRow = jQuery('#tyres1').jqGrid ('getRowData', ids[i]);
					if (dataFromTheRow.manual_insert == 1){
						$("#tyres1").find('tr#' + ids[i]).css('background', '#FFF000');
					}
				}

        let linkText = '';
        if (data['link'] !== undefined) {
          linkText = '<a href="' + data['link'] + '" target="_blank">' + data['link'] + '</a>';
        } else {
          linkText = 'Ссылка не найдена';
        }
        jQuery("#link-tyres").html(linkText);
			},
			onHeaderClick: function(gridstate){
				$('.nav1').toggle();
			},
			onSelectRow: function(id){
				if(id && id!==lastsel2){
					jQuery('#tyres1').jqGrid('restoreRow',lastsel2);
					jQuery('#tyres1').jqGrid('editRow',id,true);
					lastsel2=id;
				}
			},
			multiselect: false,
			loadtext: 'Загрузка...',
			width:560, height:'100%'
	});

	jQuery('#disks1').jqGrid({
			datatype: 'local',
			caption: 'Диски',
			imgpath: gridimgpath,
			colNames: ['#','Позиция','Удалить','Тип','Ширина','Радиус','Вылет','Дырки','ДЦО','DIA','','Ширина','Радиус','Вылет','Дырки','ДЦО','DIA',''],
			colModel: [
				{name:'id',index:'id',align:'center', sortable:false},
				{name:'pos',index:'pos',align:'center', sortable:true,editable:true, edittype:'text', editoptions:{size: 3}},
				{name:'act',index:'act',align:'center', sortable:false},
				{name:'type',index:'type',align:'center', width:230, sortable:false,editable:true,edittype:"select",editoptions:{value:"zavod:заводская;zamena:алтернатива"}},
				{name:'p2',index:'p2',align:'center', sortable:false,editable:true, edittype:'text'},
				{name:'p5',index:'p5',align:'center', sortable:false,editable:true, edittype:'text'},
				{name:'p1',index:'p1',align:'center', sortable:false,editable:true, edittype:'text'},
				{name:'p4',index:'p4',align:'center', sortable:false,editable:true, edittype:'text'},
				{name:'p6',index:'p6',align:'center', sortable:false,editable:true, edittype:'text'},
				{name:'p3',index:'p3',align:'center', sortable:false,editable:true, edittype:'text'},
				{name:'os',index:'os',align:'center', width:200, sortable:false},
				{name:'p2_1',index:'p2_1',align:'center', sortable:false,editable:true, edittype:'text'},
				{name:'p5_1',index:'p5_1',align:'center', sortable:false,editable:true, edittype:'text'},
				{name:'p1_1',index:'p1_1',align:'center', sortable:false,editable:true, edittype:'text'},
				{name:'p4_1',index:'p4_1',align:'center', sortable:false,editable:true, edittype:'text'},
				{name:'p6_1',index:'p6_1',align:'center', sortable:false,editable:true, edittype:'text'},
				{name:'p3_1',index:'p3_1',align:'center', sortable:false,editable:true, edittype:'text'},
				{name:'manual_insert',index:'manual_insert',hidden:true,editable:false}
			],
			onHeaderClick: function(gridstate){
				$('.nav2').toggle();
			},
			loadComplete: function(data){
				var ids = jQuery("#disks1").getDataIDs(); 
				for(var i=0;i<ids.length;i++){ 
					var cl = ids[i];
					de = "<input title='Удалить' type='image' src='../img/row_delete.gif' onclick='_del("+cl+",\"#disks1\");' >";
					jQuery("#disks1").setRowData(ids[i],{act:de})
					var dataFromTheRow = jQuery('#disks1').jqGrid ('getRowData', ids[i]);
					if (dataFromTheRow.manual_insert == 1){
						$("#disks1").find('tr#' + ids[i]).css('background', '#FFF000');
					}
				}

        let linkText = '';
        if (data['link'] !== undefined) {
          linkText = '<a href="' + data['link'] + '" target="_blank">' + data['link'] + '</a>';
        } else {
          linkText = 'Ссылка не найдена';
        }
        jQuery("#link-disks").html(linkText);
      },
			onSelectRow: function(id){
				if(id && id!==lastsel2){
					jQuery('#disks1').jqGrid('restoreRow',lastsel2);
					jQuery('#disks1').jqGrid('editRow',id,true);
					lastsel2=id;
				}
			},
			multiselect: false,
			loadtext: 'Загрузка...',
			width: 800, height: "100%"
	});

    var lastsel;
    jQuery('#sizeList').jqGrid({
        datatype: 'local',
        caption: 'Общие параметры',
        imgpath: gridimgpath,
        colNames: ['#','PCD','DIA','Болт','Гайка', ''],
        colModel: [
            {name:'common_id', index:'common_id',align:'center', sortable:false, search:true},
            {name:'pcd', index:'pcd', align:'center', sortable:true, editable:true, edittype:'text', search:true},
            {name:'dia', index:'dia', align:'center', sortable:true, editable:true, edittype:'text', search:true},
            {name:'bolt', index:'bolt', align:'center', sortable:true, editable:true, edittype:'text', search:true},
            {name:'gaika', index:'gaika', align:'center', sortable:true, editable:true, edittype:'text', search:true},
            {name:'act',index:'act',align:'center', sortable:false, search:false},
        ],
        rowNum: 25, // число отображаемых строк
        rowList: [25, 50, 75],
        pager: '#gridpager',
        loadonce:true,
//                onHeaderClick: function(gridstate){
//                    $('.nav1').toggle();
//                },
        loadComplete: function(){
            var ids = jQuery("#sizeList").getDataIDs();

            for(var i=0;i<ids.length;i++){
                var cl = ids[i];
                de = "<input title='Удалить' type='image' src='../img/row_delete.gif' onclick='del_common("+cl+",\"#tyres1\");' >";
                jQuery("#sizeList").setRowData(ids[i],{act:de})
                var dataFromTheRow = jQuery('#sizeList').jqGrid ('getRowData', ids[i]);
                if (dataFromTheRow.manual_insert == 1){
                    $("#sizeList").find('tr#' + ids[i]).css('background', '#FFF000');
                }
            }
        },
        onSelectRow: function(id){
            if(id && id!==lastsel){
                jQuery('#sizeList').jqGrid('restoreRow', lastsel);
                jQuery('#sizeList').jqGrid('editRow', id, true);
                lastsel = id;
            }
        },
        multiselect: false,
        loadtext: 'Загрузка...',
        width:700, height:'100%'
    });
//    .jqGrid("filterToolbar", {
//        autosearch: true,
//        searchOnEnter: false
//    });
	
	$('.nav1 > .add').click(function(){
		note('добавляем размер шины...');		
	});
	
	$('.nav2 > .add').click(function(){
		note('добавляем размер диска...');		
	});

  $('#add_tyre')
    .jqDrag('.jqDrag')
    .jqResize('.jqResize')
    .jqm({
      trigger:'.nav1 > .add',
      overlay: 1,
      onShow: function(h) {
        /* callback executed when a trigger click. Show notice */
		$('.dlg').css('width','320px').css('margin-left','-100px');
        h.w.css('opacity',0.89).show('fast'); 
		$('.dTitle').html('Дабавить размер шины');
        },
      onHide: function(h) {
        /* callback executed on window hide. Hide notice, overlay. */
		document.forms['at_frm'].reset();
        h.w.hide("fast",function() { if(h.o) h.o.remove(); }); } 
      });
	  
  $('#add_disk')
    .jqDrag('.jqDrag')
    .jqResize('.jqResize')
    .jqm({
      trigger:'.nav2 > .add',
      overlay: 1,
      onShow: function(h) {
        /* callback executed when a trigger click. Show notice */
		$('.dlg').css('width','520px').css('margin-left','-250px');
        h.w.css('opacity',0.89).show('fast'); 
		$('.dTitle').html('Дабавить размер диска');
        },
      onHide: function(h) {
        /* callback executed on window hide. Hide notice, overlay. */
		document.forms['ad_frm'].reset();
        h.w.hide("fast",function() { if(h.o) h.o.remove(); }); } 
      });
	  
	$('img.jqmdX')
  	.hover(
    function(){ $(this).addClass('jqmdXFocus'); }, 
    function(){ $(this).removeClass('jqmdXFocus'); })
  	.focus( 
    function(){ this.hideFocus=true; $(this).addClass('jqmdXFocus'); })
  	.blur( 
    function(){ $(this).removeClass('jqmdXFocus'); });

	$('#at_frm').submit(at_add);

	$('#ad_frm').submit(ad_add);
	
	$('input[type=button], input[type=submit]').addClass('button');
	
  
  $('#dlg1')
    .jqDrag('.jqDrag')
    .jqResize('.jqResize')
    .jqm({
      overlay: 1,
	  trigger: false,
      onShow: function(h) {
        /* callback executed when a trigger click. Show notice */
		$('.dlg').css('width','420px').css('margin-left','-250px');
        h.w.css('opacity',0.75).show('fast'); 
      },
      onHide: function(h) {
        /* callback executed on window hide. Hide notice, overlay. */
		document.forms['dlg1_frm'].reset();
        h.w.hide("fast",function() { if(h.o) h.o.remove(); });
      }
	});
	
	$('#dlg1_frm').submit(dlg1);

	
}catch(e){alert(e)}


	vendorLoad();
	cinit();
});





	function ad_add(){
		$.ajax({
			data: {act:'disks_edit', modif_id:modif_id, f:$('#ad_frm').serialize()},
			success: function(res){
				if(res.fres==false) note(res.fres_msg,'error');
				else if(res.fres==-1) note(res.fres_msg,'long');
				else {
					note(res.fres_msg);
					jQuery('#disks1').trigger("reloadGrid");
					document.forms['ad_frm'].reset();
				}
			}
		});
		return false;
	};

	function at_add(){
		$.ajax({
			data: {act:'tyres_edit', modif_id:modif_id, f:$('#at_frm').serialize()},
			success: function(res){
				if(res.fres==false) note(res.fres_msg,'error');
				else if(res.fres==-1) note(res.fres_msg,'long');
				else {
					note(res.fres_msg);
					jQuery('#tyres1').trigger("reloadGrid");
					document.forms['at_frm'].reset();
				}
			}
		});
		return false;
	};
	function vendorLoad(){
		$('.choice:gt(0)').empty();	
		$('#tables').hide('fast');
		$('#_vendors').html($('#loading1').html());
		$.ajax({
			data: {act:'vendors'},
			success: function(res){
				$('#_vendors').html(res.data);
				$('#vendor').change(vendorChange);
				cinit();
			}
		});
	};

	function vendorChange(){
		$('.choice:gt(0)').empty();	
		if($(this).val()==0) return;
		$('#tables').hide('fast');
		$('#_models').html($('#loading1').html());
		$.ajax({
			data: {act:'models', vendor_id:$(this).val()},
			success: function(res){
				$('#_models').html(res.data);
				$('#model').change(modelChange);
				cinit();
			}
		});
	};

	function modelChange(){
		$('.choice:gt(1)').empty();	
		if($(this).val()==0) return;
		$('#tables').hide('fast');
		$('#_years').html($('#loading1').html());
		$.ajax({
			data: {act:'years', model_id:$(this).val()},
			success: function(res){
				$('#_years').html(res.data);
				$('#year').change(yearChange);
				cinit();
			}
		});
	};
	
	function yearChange(){
		$('.choice:gt(2)').empty();	
		if($(this).val()==0) return;
		$('#tables').hide('fast');
		$('#_modifs').html($('#loading1').html());
		$.ajax({
			data: {act:'modifs', year_id:$(this).val()},
			success: function(res){
				$('#_modifs').html(res.data);
				$('#modif').change(showTables);
				cinit();
			}
		});
	};

    function del_common(common_id){
        if(typeof common_id == "undefined") return;
        $.ajax({
            data: {act:'del_common', common_id: common_id},
            success: function(res){
                jQuery('#sizeList').delRowData(common_id);
                note(res.fres_msg);
            }
        });
    }

	function show_common_table(modif_id){
        if(typeof modif_id == 'undefined') modif_id = $('#modif').val();
        jQuery('#sizeList').setGridParam({datatype:'json'});
        jQuery('#sizeList').setGridParam({url:'../be/avto2.php?act=get_common_list&avto_id='+modif_id});
        jQuery('#sizeList').setGridParam({editurl:'../be/avto2.php?act=edit_common'});

        jQuery('#sizeList').trigger("reloadGrid");
    }

	function showTables(m){
		if(m>0) modif_id=m; else modif_id=$('#modif').val();
		note('modif_id= '+modif_id);

        show_common_table(modif_id);

		if(modif_id==0) {
			$('#tables').hide('fast');
			return;
		}
		jQuery('#tyres1').setGridParam({datatype:'json'});
		jQuery('#tyres1').setGridParam({url:'../be/avto2.php?act=tyres&modif_id='+modif_id}) 
		jQuery('#tyres1').setGridParam({editurl:'../be/avto2.php?act=tyres_edit&modif_id='+modif_id})

		jQuery('#disks1').setGridParam({datatype:'json'});
		jQuery('#disks1').setGridParam({url:'../be/avto2.php?act=disks&modif_id='+modif_id})
		jQuery('#disks1').setGridParam({editurl:'../be/avto2.php?act=disks_edit&modif_id='+modif_id})
		$('#tables').show();
		jQuery('#tyres1').trigger("reloadGrid");
		jQuery('#disks1').trigger("reloadGrid");
		// **** //
		// заполняем инпуты #common_params данными из ab_common
		$.ajax({
			data: {act:'common', modif_id:modif_id},
			success: function(res){
				if (res.fres){
					$(Object.keys(res.commons)).each(function(i, key){
						$('#common_params input[name="' + key+ '"]').val(res.commons[key]);
					});
				}
				else{
					$('#common_params input[type="text"]').each(function(){
						$(this).val('');
					});
					$('#common_params input[name="common_id"]').val(0);
				}
			}
		});
	}
function copy(){
    if($('#year').val()>0) {
        var nyear = prompt("Введите год", "");
        
        if (nyear >= 1990 & nyear <= 3000) {
            var vendor = $('#vendor').val();
            var model = $('#model').val();
            var year = $('#year').val();
            $.get("../be/copy.php?model=" + model + "&year=" + year + "&nyear=" + nyear + '&vendor=' + vendor, function (data) {
                $('#tables').hide('fast');
                $.ajax({
                    data: {act: 'years', model_id: model},
                    success: function (res) {
                        $('#_years').html(res.data);
                        $('#year').change(yearChange);
                        cinit();
                        $('#_modifs').empty();
                    }
                });
            });
        }else{
            alert('Значение указанно неверно');
        }
    }else{
        alert('Сначала надо выбрать значение из списка');
    }
}

var bdata;

	function dlg1(){
		if (bdata.ok==0) $.ajax({
			data: {act:bdata.act, box:bdata.box, v:$('#dlg1_frm>input[name=v]').val(), id:bdata.id, parent_name:bdata.parent_name, parent_id:bdata.parent_id},
			success: function(res){
				if(res.fres==false) note(res.fres_msg,'error');
				else if(res.fres==-1) note(res.fres_msg,'long');
				else {
					bdata.ok=1;
					note(res.fres_msg);
					if(bdata.act=='add' || bdata.act=='edit') {
						$('#dlg1').jqmHide();
						document.forms['dlg1_frm'].reset();
					}
					var gt={vendor:'0',model:'0',year:'1',modif:'2'};
					if(bdata.act=='hide') {$('.choice:gt('+gt[bdata.box]+')').empty();}
					if(bdata.act=='hide') { $('#tables').hide('fast');}
					$('#_'+bdata.box+'s').html(res.data);
					switch (bdata.box){
						case 'vendor': $('#'+bdata.box).change(vendorChange); break;
						case 'model': $('#'+bdata.box).change(modelChange); break;
						case 'year': $('#'+bdata.box).change(yearChange); break;
						case 'modif': $('#'+bdata.box).change(showTables); break;

					}
					cinit();
				}
			}
		});
		return false;
	};

function cinit(){
	$("input[class=e]").unbind('click').bind('click',function(){
		var p=$(this).parent().parent().find('select');
		var v=p.find('option:selected');
		console.log(v);
		if(($(this).attr('id')=='edit' || $(this).attr('id')=='hide') && v.val()==0) {note('Сначала надо выбрать значение из списка');return false;}
		var t={edit:'Изменить',add:'Добавить',hide:'Удалить',}
		var parents={vendor:'',model:'vendor',year:'model',modif:'year'};
		$('.dTitle').html(t[$(this).attr('id')]+' '+p.attr('e'));
		bdata={
			box: p.attr('id'),
			act: $(this).attr('id'),
			id: v.val(),
			parent_name: parents[p.attr('id')],
			parent_id: parents[p.attr('id')]!=''?$('#'+parents[p.attr('id')]).val():'',
			ok:0
		};
		if($(this).attr('id')=='edit') $('#dlg1_frm>input[name=v]').val(v.text()); else $('#dlg1_frm>input[name=v]').val('');
		if(bdata.act=='edit' || bdata.act=='add' ) $('#dlg1').jqmShow(); else if(window.confirm('Удаляем??')) dlg1();
		return false;
	});
}


function _del(ids,c){
//	note('Удаляем id='+ids+'...');
	$.ajax({
		data: {act:'del',id: ids},
		success: function (res){
			if(!res.fres) note(res.fres_msg,'stick'); else {
				jQuery(c).delRowData(ids);
				note('Удалено id='+ids+' OK');
			}
		}
	});
}

function save_common(){
	var modif_id=$('#modif').val();
	var data = {};
	$('#common_params input:not([type="button"]').each(function(){
		data[$(this).attr('name')] = $(this).val();
	});
	$.ajax({
		data: {act:'save_common', modif_id: modif_id, cdata: data},
		success: function (res){
			if(!res.fres) note(res.fres_msg,'stick'); else {
				note('Общие параметры сохранены.');
			}
		}
	});
}


function add_common(){
    var modif_id=$('#modif').val();
    var data = {};
    $('#addNewParams input:not([type="button"]').each(function(){
        data[$(this).attr('name')] = $(this).val();
    });
    $.ajax({
        data: {act:'save_common', modif_id: modif_id, cdata: data},
        success: function (res){
            if(!res.fres) note(res.fres_msg,'stick'); else {
                jQuery('#sizeList').setGridParam({datatype:'json'});
                jQuery('#sizeList').trigger("reloadGrid");

                note('Общие параметры сохранены.');
            }
        }
    });
}

function remove_common(){
    var common_id = $('#common_params').find('input[name="common_id"]').val();
    console.log(common_id);

    $.ajax({
        data: {act:'remove_common', common_id: common_id},
        success: function (res){
            if(!res.fres) note(res.fres_msg,'stick'); else {
                note('Общие параметры удалены.');
            }
        }
    });
}
</script>

<? cp_body()?>
<? cp_title()?>
<div id="loading1" style="display:none"><div id="_loading1"><img src="../img/loading.gif" width="16" height="16"></div></div>
<div style="width:100%; overflow:hidden">
<div class="choice" id="_vendors"></div>
<div class="choice" id="_models"></div>
<div class="choice" id="_years"></div>
<div class="choice" id="_modifs"></div>
</div>

<div id="tables" style="overflow:hidden; display:none">
<div id="_tyres">
<table width="100%" id="tyres1" class="scroll" cellpadding="0" cellspacing="0"></table>
<div class="nav1">
  <input class="add" type="image" src="../img/row_add.gif">&nbsp;&nbsp;
  <a href="javascript:;" class="add">Добавить запись</a>
  <span id="link-tyres"></span>
</div>
</div>
<div id="_disks" style="margin-top:10px">
<table id="disks1" class="scroll" cellpadding="0" cellspacing="0"></table>
<div class="nav2">
  <input class="add" type="image" src="../img/row_add.gif">&nbsp;&nbsp;
  <a href="javascript:;" class="add">Добавить запись</a>
  <span id="link-disks"></span>
</div>
</div>
<br>
<br>
<table width="100%" id="sizeList" class="scroll" cellpadding="0" cellspacing="0"></table>
<div id="gridpager"></div
    <br>
    <br>
<b>Добавить общие параметры:</b>
<br>
<br>
<form id="addNewParams">
    PCD:<input type="text" name="pcd">
    DIA:<input type="text" name="dia">
    Гайка:<input type="text" name="gaika">
    Болт:<input type="text" name="bolt">
    <input type="button" onclick="add_common();" value="Сохранить">
</form>
<br>
<br>
<div id="common_params" style="display: none;">
	<input type="hidden" name="common_id" value="0">
	PCD:<input type="text" name="pcd">
	DIA:<input type="text" name="dia">
	Гайка:<input type="text" name="gaika">
	Болт:<input type="text" name="bolt">
	<input type="button" onclick="save_common();" value="Сохранить">
</div>

</div>

<style type="text/css">
    .removal__button {
        padding: 0 10px;
        line-height: 35px;
    }
    .removal__icon {
        display: inline-block;
        vertical-align: middle;
    }

img.jqResize {
	position: absolute; right: 2px; bottom: 2px;
}

div.dlg {
  display: none;
  position: fixed;
  top: 17%;
  left: 50%;
  margin-left: -300px;
  font-family:verdana,tahoma,helvetica;
  font-size:11px;
  width: 420px;
  background:#FFFFCC url(../img/note_icon.png) 5px 5px no-repeat;
  border: 1px solid #000;
  padding: 0;
  overflow:auto;
}

img.jqmdX {
  position: absolute;
  cursor: pointer;
  right: 4px;
  top: 6px;
  height: 19px;
  width: 0px;
	padding: 0 0 0 19px;

  background: url(../img/dlg_close.gif) no-repeat bottom left;
  overflow: hidden;
}
img.jqmdXFocus {background-position: top left; outline: none;}

.dTitle{
  margin: 5px 0;
  margin-left:25px;
  margin-right:25px;
  padding:3px 5px;
  cursor:move;
  font-size:11px;
  color:#FFFFCC;
  font-weight:bold;
  background-color:#505050;
}

.dContent{
  border-top:1px;
  color:#000;
  text-align:center;
  padding:0 20px 5px;
}
.dT1{width:30px}

.button{background-color:#FFFFFF; font-size:10px; font-family:Arial, Helvetica, sans-serif, Times, serif}
</style>
<div id="add_tyre" class="dlg">
<div class="dTitle jqDrag">Добавить размер шины</div>
<div class="dContent">
<form id="at_frm" name="at_frm" style="margin:10px 0px">
<select name="type">
<option value="zavod">Заводская комплектация</option>
<option value="zamena">Варианты замены</option>
</select>
<fieldset><legend>Передняя ось</legend>
<nobr>Ширина <input type="text" name="p3" class="dT1"> Выcота <input type="text" name="p2" class="dT1"> Радиус <input type="text" name="p1" class="dT1"></nobr>
</fieldset>
<fieldset><legend>Задняя ось</legend>
<nobr>Ширина <input type="text" name="p3_1" class="dT1"> Выcота <input type="text" name="p2_1" class="dT1"> Радиус <input type="text" name="p1_1" class="dT1"></nobr>
</fieldset>
<br>
<nobr><input type="submit" value="Добавить">&nbsp;&nbsp;&nbsp;&nbsp; <input type="button" value="Отмена" class="jqmClose"></nobr>
<p>Обязательные поля: Ширина, Высота, Радиус.</p>
</form>
</div>
<img src="../img/dlg_close.gif" alt="close" class="jqmClose jqmdX " />
<img src="../img/resize.gif" alt="resize" class="jqResize" />
</div>



<div id="add_disk" class="dlg">
<div class="dTitle jqDrag">Добавить размер диска</div>
<div class="dContent">
<form id="ad_frm" name="ad_frm" style="margin:10px 0px">
<select name="type">
<option value="zavod">Заводская комплектация</option>
<option value="zamena">Варианты замены</option>
</select>
<fieldset><legend>Передняя ось</legend>
<nobr>Ширина <input type="text" name="p2" class="dT1"> Радиус <input type="text" name="p5" class="dT1"> Вылет <input type="text" name="p1" class="dT1">
Дырки <input type="text" name="p4" class="dT1"> ДЦО <input type="text" name="p6" class="dT1"> DIA <input type="text" name="p3" class="dT1"></nobr>
</fieldset>
<fieldset><legend>Задняя ось</legend>
<nobr>Ширина <input type="text" name="p2_1" class="dT1"> Радиус <input type="text" name="p5_1" class="dT1"> Вылет <input type="text" name="p1_1" class="dT1">
Дырки <input type="text" name="p4_1" class="dT1"> ДЦО <input type="text" name="p6_1" class="dT1"> DIA <input type="text" name="p3_1" class="dT1"></nobr>
</fieldset>
<br>
<nobr><input type="submit" value="Добавить">&nbsp;&nbsp;&nbsp;&nbsp; <input type="button" value="Отмена" class="jqmClose"></nobr>
<p>Обязательные значения: Ширина, Радиус, Вылет. Для указания дипазона вылетов - указывайте пару чисел через дефис (например, "10-25")</p>
</form>
</div>
<img src="../img/dlg_close.gif" alt="close" class="jqmClose jqmdX " />
<img src="../img/resize.gif" alt="resize" class="jqResize" />
</div>

<div id="dlg1" class="dlg">
<div class="dTitle jqDrag">Добавить......</div>
<div class="dContent">
<form id="dlg1_frm">
<input type="hidden" name="id">
<input type="hidden" name="box">
<input type="hidden" name="act">
<input type="text" name="v" id="v" style="width:100%; margin:15px 0px;">
<nobr><input type="submit" value="Записать">&nbsp;&nbsp;&nbsp;&nbsp; <input type="button" value="Отмена" class="jqmClose"></nobr>
</form>
</div>
<img src="../img/dlg_close.gif" alt="close" class="jqmClose jqmdX " />
<img src="../img/resize.gif" alt="resize" class="jqResize" />

</div>

<? cp_end()?>