// модуль импорта App_CC_CI версия 1.0 (класс алгоритма 1)

CatImport = {init: function() { return new function() {
	$$.ajax.init('../be/import.php');
	
	function resizeCM(){
		$('.import #cm_tyreGrid').setGridHeight($('#west-center').innerHeight()-80);
		$('.import #cm_diskGrid').setGridHeight($('#west-center').innerHeight()-80);
	}
	function resizeView(){
		$('.import #viewGrid').setGridWidth($('#uil-center').innerWidth()-($.browser.opera?30:30));
		$('.import #viewGrid').setGridHeight($('#uil-center').innerHeight()-($.browser.opera?140:140));
	}
	var layoutSettings={
		applyDefaultStyles: false,
		resizeWithWindow: true,
		fxName: 'slide',	
		fxSpeed: 'slow',
		west__closable: true,
		west__size: '300',
		west__resizable: false,
		north__closable: false,
		north__resizable: false,
		center__onresize_end: function(){
			westLayout.resizeAll();
			resizeView();
		},
		west__onclose_end: function(){ resizeView()},
		west__onopen_end: function(){ resizeView()}
			
	};
	var westLayoutSettings={
		applyDefaultStyles: false,
		resizeWithWindow: true,
		fxName: 'slide',	
		fxSpeed: 'slow',
		minSize: 150,
		south__resizable: true,
		south__closable: true,
		center__resizable: false,
		center__paneSelector: "#west-center",
		south__paneSelector: "#west-south",
		south__size:260,
		center__onresize: function(){ resizeCM();},
		center__onclose_end: function(){ resizeCM();},
		center__onopen_end: function(){ resizeCM();}
	};
	
	var layout=$('body').layout(layoutSettings);
	var westLayout=$('.ui-layout-west').layout(westLayoutSettings);

	$('#uil-models').append('<div class="import"><div id="colModel"></div></div>');
	$('#uil-files').append('<div class="import"><div id="files"></div></div>');

	var cm_tyre_lastsel,cm_disk_lastsel;
	var cm1_editurl='../be/import.php?act=cm1_saverow';
	var cm2_editurl='../be/import.php?act=cm2_saverow';

	 function cm1Param (file_id){
			$('.import #cm_tyreGrid').setGridParam({'postData':{'file_id':file_id}}).trigger('reloadGrid');
	};
	function cm2Param (file_id){
			$('.import #cm_diskGrid').setGridParam({'postData':{'file_id':file_id}}).trigger('reloadGrid');
	};
	var cmTabs=new $$.Tabs({
		renderTo:'.import #colModel',
		css: {overflow:'hidden'},
		id: 'cmTabs'
	});
	cmTabs.add({
		label: 'Шины',
		id: 'gr1',
		padding: '0px 0 0px 0px',
		css: {overflow:'hidden'},
		html: '<table id="cm_tyreGrid"></table>',
		setActive: true,
		onNewTab: function(){
			$('.import #cm_tyreGrid').jqGrid({
				colNames:['Параметр', 'Колонка'], 
				colModel:[ 
					{name:'fieldName',index:'fieldName',sortable:false,editable:false,width:170}, 
					{name:'colName',index:'colName', width:90,editable: true,sortable:false,align:'center'} 
				], 
				datatype: 'json',
				url: '../be/import.php?act=cm1',
				editurl: this.cm1_editurl,
//				autowidth: true,
				width: 290,
				shrinkToFit: false,
				height: $('#west-center').innerHeight()-80,
				hoverrows: true,
				postData: {},
				editable: false,
				beforeSelectRow: function(rowid,e){
					if(self.file.gr>0) return false; else return true;
				},
				onSelectRow: function(id){ 
					if(id && id!==cm_tyre_lastsel){ 
						$('.import #cm_tyreGrid').setGridParam({'editurl':cm1_editurl+'&file_id='+self.file.file_id}).jqGrid('restoreRow',cm_tyre_lastsel).jqGrid('editRow',id,true); 
						cm_tyre_lastsel=id; 
					} 
				},
				loadError: $$.ajax.err
			});
		}
	});
	cmTabs.add({
		label: 'Диски',
		id: 'gr2',
		css: {overflow:'hidden'},
		padding: '0px 0 0px 0px',
		html: '<table id="cm_diskGrid"></table>',
		onNewTab: function(){
			$('.import #cm_diskGrid').jqGrid({
				colNames:['Параметр', 'Колонка'], 
				colModel:[ 
					{name:'fieldName',index:'fieldName',sortable:false,editable:false,width:170}, 
					{name:'colName',index:'colName', width:90,editable: true,sortable:false,align:'center'} 
				], 
				datatype: 'json',
				url: '../be/import.php?act=cm2',
				editurl: this.cm2_editurl,
//				autowidth: true,
				width:290,
				shrinkToFit: false,
				height: $('#west-center').innerHeight()-80,
				postData: {},
				loadComplete: function(xhr){
					var r=xhr.responseText;
				},
				beforeSelectRow: function(rowid,e){
					if(self.file.gr>0) return false; else return true;
				},
				onSelectRow: function(id){ 
					if(id && id!==cm_disk_lastsel){ 
						$('.import #cm_diskGrid').setGridParam({'editurl':cm2_editurl+'&file_id='+self.file.file_id}).jqGrid('restoreRow',cm_disk_lastsel).jqGrid('editRow',id,true); 
						cm_disk_lastsel=id; 
					} 
				},
				loadError: $$.ajax.err
			});
		}
	});

	var tabs=new $$.Tabs({
		renderTo: $('#uil-center'),
		css: { overflow:'auto'},
		id:'centerTabs'
	});
	tabs.add({
		label:'Загрузка файла',
		id: 'tabLoad',
		padding: '15px',
		setActive: true,
		onNewTab:function(){
			tabs.add({
				label:'Посмотр файла',
				id: 'tabView',
				css: {padding:'5px 0 0 5px'},
				onNewTab: function(){
				}
			});
			tabs.hide('tabView');
			$$._append('.import #info',' #tabLoad');
			$('.import #info').hide();
			$$._append('.import #uploadForm',' #tabLoad');
        }
	});

	var viewBut=new $$.Button({
		renderTo: '.import #info',
		anc: 'Просмотр файла',
		onClick: function(){
			$$.setCookie('vgChk','');
			$('#tabView').empty().html('<div class="import"><table id="viewGrid"></table><div id="pagered"></div><div id="colCh"></div></div>');
			var viewGrid=$('.import #viewGrid').jqGrid({
				datatype: 'json',
				url: '../be/import.php?act=view',
				colNames: function(){
					var a=['X','ID','Статус размера','Статус модели','Статус бренда'];
					var i=0;
					if(self.file.param.status!=0)
						for(var v in self.CMI[self.file.param.gr]){
							a.push(v);
							i++;
						}
					else
						for(var v in self.file.param.CM){
							a.push(v);
							i++;
						}
					return a;
				}(),
				colModel: function(){
					var a=[
						{name:'sel',index:'sel',align:'center',width:50,sortable:false,stype:''},
						{name:'item_id',index:'item_id',sortable:true,align:'center',width:90},
						{name:'cstatus',index:'cstatus',sortable:true,align:'center',width:200,stype:(self.file.param.status!=0?'select':''),editoptions:{value:":Все;1:связано;2:добавлено;3:перемещено;4:обновлено;5:пропущен;40:пропустить;6:проблема"}},
						{name:'mstatus',index:'mstatus',sortable:true,align:'center',width:200,stype:(self.file.param.status!=0?'select':''),editoptions:{value:":Все;1:связано;2:добавлено;3:перемещено;4:обновлено;5:пропущен;40:пропустить;6:проблема"}},
						{name:'bstatus',index:'bstatus',sortable:true,align:'center',width:200,stype:(self.file.param.status!=0?'select':''),editoptions:{value:":Все;1:связано;2:добавлено;3:перемещено;4:обновлено;5:пропущен;40:пропустить;6:проблема"}}
					];
					var i=0;
					var b;
					if(self.file.param.status!=0)
						for(var v in self.CMI[self.file.param.gr]){
							b={name:self.CMI[self.file.param.gr][v]['item_field'],index:self.CMI[self.file.param.gr][v]['item_field'],sortable:true,align:'center'};
							if(self.CMI[self.file.param.gr][v]['type']=='id'){
								b.editoptions={value:':Все'};
								b.stype="select";
								for(var l in self.CMI[self.file.param.gr][v]['list']){
									b.editoptions.value+=';'+self.CMI[self.file.param.gr][v]['list'][l]+':'+l;
								}
							}
							a.push(b);
							i++;
						}
					else
						for(var v in self.file.param.CM){
							a.push({name:'n'+v,index:'i'+v,sortable:false,align:'center',stype:''});
							i++;
						}
					return a;
				}(),
				onSelectRow: function(id){},
				gridComplete: function(){ 
					var ids = $('#viewGrid').jqGrid('getDataIDs'); 
					var c=$$.getCookie('vgChk');
					if(c!=null && c!='') c=$$.unserialize(c); else c={};
					var d,s;
					for(var i=0;i < ids.length;i++){ 
						d=$('#viewGrid').jqGrid('getRowData',ids[i]);
						s='<input type="checkbox" value="1" class="vg_chk" rowId="'+ids[i]+'" '+($$.isDefined(c[d['item_id']])?'checked="checked"':'')+'>';
						$('#viewGrid').jqGrid('setRowData',ids[i],{sel:s}); 
					}
					$('.import .vg_chk').change(viewGridChk);
				},
				ondblClickRow: function(rowid,iRow,iCol,e){
					doViewGrid();
				},
//				autowidth: true,
				sortable: true,
				shrinkToFit: true,
				sortname: 'item_id',
				sortorder: 'asc',
				width:$('#uil-center').innerWidth()-30,
				height: $('#uil-center').innerHeight()-($.browser.opera?140:140),
				rowNum: 70,
				postData: {'file_id':self.file.file_id},
				viewrecords: true, 
				rowList: [10,20,30,40,50,60,70,80,100,120,150,200,300,400,500,600,700,800,1000,2000],
				pager: '#tabView #pagered',
//				scroll: true,
				rownumbers: true, 
				rownumWidth: 40, 
				gridview: true, 
				loadError: $$.ajax.err,
				editurl: '#',
				cellEdit: false
			})
			.jqGrid('navGrid','#tabView #pagered',{
				add:false,
				edit:false,
				del:false,
				search:false,
				refresh:true,
				view:true
			})
			.jqGrid('navButtonAdd','#tabView  #pagered',{ 
				caption: "Колонки", 
				title: "Состав колонок", 
				onClickButton : function (){ 
					$(".import #viewGrid").jqGrid('columnChooser'); 
				} 
			})
			.jqGrid('navButtonAdd',"#tabView  #pagered",{
				caption:"Сброс",
				title:"Очистить форму поиска",
				buttonicon :'ui-icon-refresh', 
				onClickButton:function(){ 
					viewGrid[0].clearToolbar() 
				} 
			})
			.jqGrid('navButtonAdd',"#tabView  #pagered",{
				caption:"Действие",
				title:"Действия с записью",
				buttonicon :'ui-icon-link', 
				onClickButton:function(){ 
					doViewGrid();
				} 
			})
			.jqGrid('filterToolbar')
			.jqGrid('sortableRows')
			.trigger('reloadGrid');
			tabs.show('tabView');
			tabs.select('tabView');
			viewBut.hide();
		}
	});
	
	function doViewGrid(){
		$$.note('Нечего делать, сорри :(');
	}
	
	function viewGridChk (e){
		var c=$$.getCookie('vgChk');
		if(c!=null && c!='') c=$$.unserialize(c); else c={};
		var d=$('#viewGrid').jqGrid('getRowData',$(e.target).attr('rowId'));
		if($(e.target).is(':checked')) c[d['item_id']]=1; 
			else if($$.isDefined(c[d['item_id']])) delete c[d['item_id']];
		$$.setCookie('vgChk',$$.serialize(c));
//		$$.note($.param(c));
	}
		
	var checkBut=new $$.Button({
		renderTo: '.import #buts',
		anc: 'Проверить структуру',
		css: {'float':'left','margin-right':20},
		onClick: function(){
			$$.overlay(1);
			$.ajax({
				url: $$.ajax.q({act:'check_files',file_id:self.file.file_id}),
				success: function(r){
					if(r.fres){
						if(r.gr>0){
							self.file.gr=r.gr;
							self.file.param.CM=r.CM;
							cmTabs.hide('gr1');
							cmTabs.hide('gr2');
							cmTabs.show('gr'+self.file.gr);
							cmTabs.select('gr'+self.file.gr);
							parseBut.show();
							checkParseBut.show();
							viewBut.show();
							if(r.gr==2) $('#replicaBrand_sw').show('fast'); else $('#replicaBrand_sw').hide('fast');
							if(r.gr==1) $('#tyresSuffixes_sw').show('fast'); else $('#tyresSuffixes_sw').hide('fast');
							if(r.gr==1) $('#updateTyresSuffix_sw').show('fast'); else $('#tyresSuffixes_sw').hide('fast');
							$$.note($$.fresMsg(r.fres_msg),'long');
						}else{
							parseBut.hide();
							checkParseBut.hide();
							viewBut.hide();
							cmTabs.show('gr1');
							cmTabs.show('gr2');
							$$.note($$.fresMsg(r.fres_msg));
						}
					}else $$.note($$.fresMsg(r.fres_msg),'error');
				},
				complete: function(){
					$$.overlay(0);
				}
			});
		}
	});
	checkBut.hide();

	function parseFile(check){
		var pbar=new $$.ProgressDialog({title:'Парсинг файла...'});
		var limit=200;
		var cou=Math.ceil((self.file.param.numRows)/limit)-1;
		if(cou==0) cou=1; 
		var it=0;
		function iter(it){
			$.ajax({
				url: $$.ajax.q({act:'parse', file_id: self.file.file_id, page:it, limit:limit, last_page:cou, 'check': check}),
				data: {f: $('.import #uploadFrm').serialize()},
				error: function (XMLHttpRequest, textStatus, errorThrown){
					$$.note('ajx ERROR: '+textStatus+'<br>'+XMLHttpRequest.responseText+' :: итeрация: '+it+' из '+cou,'error');
					pbar.destroy();
				},
				success: function(r){
					if(r.fres){
						it++;
						pbar.setValue(Math.ceil(it*100/cou));
						if(it<=cou && r.finish!=true) iter(it); else{
							pbar.setValue(100);
							pbar.setHTML('<b><font style="color:red">П</font>роверяю сделанную работу...</b>');
							$.ajax({
								url: $$.ajax.q({act:'make_stat', file_id: self.file.file_id, 'check': check}),
								data: {f: $('.import #uploadFrm').serialize()},
								complete: function(){
									pbar.destroy();
								},
								success: function(r){
									viewBut.show();
									tabs.hide('tabView');
									if(r.fres){
										self.file.param=r.param;
										fileInfo();
										if(self.file.param.status==2){
											checkParseBut.hide();
											parseBut.hide();
											checkBut.hide();
										}
										for(var i=1; i<=3;i++) $('.import #info').animate({'opacity': '0.4'},50).animate({'opacity': '1'},50);
										$$.note('Парсинг завершен успешно.','long');
									} else $$.note($$.fresMsg(r.fres_msg),'error');
								}
							});
						}
					}else {
						pbar.destroy();
						$$.note($$.fresMsg(r.fres_msg),'error');
					}
				}
			});
		}
		iter(it);
	}

	function fileInfo(){
		var s='';
		if($$.isDefined(self.file.param.status)){
			switch (self.file.param.status){
				case 1: s='ОБРАБОТАН БЕЗ ЗАПИСИ';  break;
				case 2: s='ИМПОРТИРОВАН В БАЗУ'; break;
				case 0: s='НОВЫЙ'; break;
				case -1: s='ЧАСТИЧНО ОБРАБОТАН БЕЗ ЗАПИСИ'; break;
				case -2: s='ЧАСТИЧНО ИМПОРТИРОВАН В БАЗУ'; break;
			}
		} else s='НОВЫЙ';
		$('.import #info #status').html(s);		
		$('.import #info #rows').html($$.isDefined(self.file.param.numRows)?(self.file.param.numRows-1):'?'); 
		$('.import #info #brands span:eq(0)').html($$.isDefined(self.file.param.newBrands)?self.file.param.newBrands:'?'); 
		$('.import #info #brands span:eq(1)').html($$.isDefined(self.file.param.relBrands)?self.file.param.relBrands:'?'); 
		$('.import #info #models span:eq(0)').html($$.isDefined(self.file.param.newModels)?self.file.param.newModels:'?'); 
		$('.import #info #models span:eq(1)').html($$.isDefined(self.file.param.relModels)?self.file.param.relModels:'?'); 
		$('.import #info #models span:eq(2)').html($$.isDefined(self.file.param.moveModels)?self.file.param.moveModels:'?'); 
		$('.import #info #tipos span:eq(0)').html($$.isDefined(self.file.param.newTipos)?self.file.param.newTipos:'?'); 
		$('.import #info #tipos span:eq(1)').html($$.isDefined(self.file.param.relTipos)?self.file.param.relTipos:'?'); 
		$('.import #info #tipos span:eq(3)').html($$.isDefined(self.file.param.refreshTipos)?self.file.param.refreshTipos:'?'); 
		$('.import #info #tipos span:eq(2)').html($$.isDefined(self.file.param.moveTipos)?self.file.param.moveTipos:'?'); 

		$('.import #info #code6 span:eq(0)').html($$.isDefined(self.file.param.b_code6)?self.file.param.b_code6:'?'); 
		$('.import #info #code6 span:eq(1)').html($$.isDefined(self.file.param.m_code6)?self.file.param.m_code6:'?'); 
		$('.import #info #code6 span:eq(2)').html($$.isDefined(self.file.param.c_code6)?self.file.param.c_code6:'?'); 

		$('.import #info #c_code5').html($$.isDefined(self.file.param.c_code5)?self.file.param.c_code5:'?'); 

		$('.import #info #notZeroPriceNum').html($$.isDefined(self.file.param.notZeroPriceNum)?self.file.param.notZeroPriceNum:'?'); 
		$('.import #info #notZeroSkladNum').html($$.isDefined(self.file.param.notZeroSkladNum)?self.file.param.notZeroSkladNum:'?'); 
		
		$('.import #info #comment').empty();
		if(self.file.param.moveTipos>0 ) $('.import #info #comment').show().append('<p>Есть перемещенные типоразмеры в другую модель. Склад и цены обновились, но ситуация требует рассмотрения, так как на сайте они фактически остались в прежней категории.</p></br>');
		if(self.file.param.moveModels>0) $('.import #info #comment').show().append('<p style="color:red">Есть перемещенные модели в другой бренд. Посмотрите отчет для выяснения причины.</p><br>');
	}
	
	var checkParseBut=new $$.Button({
		renderTo: '.import #buts',
		anc: 'Парсинг без записи',
		css: {'float':'left','margin-right':20},
		onClick: function(){
			if(self.file.gr>0) parseFile(true);
		}
	});
	checkParseBut.hide();

	var parseBut=new $$.Button({
		renderTo: '.import #buts',
		anc: 'Начать импорт!',
		css: {'float':'left','margin-right':20},
		onClick: function(){
			if(self.file.gr>0) parseFile(false);
		}
	});
	parseBut.hide();

	var files=new $$.FileList({
		renderTo:'.import #files'
		,height:'auto'
		,onSelected: function(id,data,label){
			$$.overlay(1);
			$.ajax({
				url: $$.ajax.q({act:'select_file',file_id:id}),
				complete: function(){
					$$.overlay(0);
				},
				success:function(r){
					if(r.fres){
						cm1Param(id);
						cm2Param(id);
						files.select(id);
						self.file=r;
						$('.import #info').show('fast');
						fileInfo();
						tabs.hide('tabView');
						if(self.file.gr>0) {
							viewBut.show();
							cmTabs.hide('gr1');
							cmTabs.hide('gr2');
							cmTabs.show('gr'+self.file.gr);
							cmTabs.select('gr'+self.file.gr);
							if(self.file.gr==2) $('#replicaBrand_sw').show('fast'); else $('#replicaBrand_sw').hide('fast');
							if(self.file.gr==1) $('#tyresSuffixes_sw').show('fast'); else $('#tyresSuffixes_sw').hide('fast');
							if(self.file.gr==1) $('#updateTyresSuffix_sw').show('fast'); else $('#tyresSuffixes_sw').hide('fast');
							if(self.file.status==2){
								checkParseBut.hide();
								parseBut.hide();
								checkBut.hide()
								checkBut.hide();
							}else{
								parseBut.show();
								checkParseBut.show();
							}
						}else{
							viewBut.hide();
							cmTabs.show('gr1');
							cmTabs.show('gr2');
							checkBut.show();
							parseBut.hide();
							checkParseBut.hide();
						}
						$$.note('Загружен '+label+'<br>'+$$.fresMsg(r.fres_msg));
					}else {
						$$.note('НЕ ЗАГРУЖЕН '+label+'<br>'+$$.fresMsg(r.fres_msg),'error');
					}
				}
			});
			return false;
		}
		,onDel: function(id,data,label){
			$$.overlay(1);
			$.ajax({
				url: $$.ajax.q({act:'del_file',file_id:id}),
				complete: function(){
					$$.overlay(0);
				},
				success:function(r){
					files.del(id);
					if(r.fres){
						if(id==self.file.file_id){
							cm1Param('');
							cm2Param('');
							cmTabs.show('gr1');
							cmTabs.show('gr2');
							tabs.hide('tabView');
							parseBut.hide();
							checkParseBut.hide();
							checkBut.hide();
							$('.import #info').hide('fast');
							self.file={'file_id':0};
						}
						$$.note('Удален '+label);
					}else {
						$$.note('НЕ УДАЛЕН '+label+'<br>'+$$.fresMsg(r.fres_msg),'error');
					}
				}
			});
			return false;
		}
	});

	var self=this;
	this.file={'file_id':0};
	
	
	$.ajax({
		url: $$.ajax.q({act:'files'}),
		success: function(r){
			if(r.fres){
				files.addItems(r.files);
			}
		}
	});
	$.ajax({
		url: $$.ajax.q({act:'get_config'}),
		success: function(r){
			if(r.fres){
				$$.populate('.import #uploadForm',r.opt.uploadFrm);
				self.CMI=r.CMI;
			}
		}
	});

	$(".import #upload").uploadify({
        swf: '/cms/inc/uploadify/uploadify.swf',
        uploader: '../be/import.php?mode=upload&'+window.QSID,
        buttonText: 'Выбрать файл',
        queueID: 'fileQueue',
        auto: true,
        multi: false,
        width: 251,
        height: 30,
        method: 'POST',
        progressData : 'percentage',
		fileExt: '*.xls;*.csv;',
		fileDesc: "Файлы EXCEL (*.xls;*.csv)",

        onUploadError: function(file, errorCode, errorMsg, errorString){
			alert('ОШИБКА ПРИ ЗАГРУЗКЕ :: Error code = '+errorCode+' INFO: '+errorMsg + ' ' + errorString);
		},

        onUploadSuccess: function(file, data, response){
			var res=data.split('|');
			if(res[0]=='1'){
				$$.overlay(1);
				$.ajax({
					url: $$.ajax.q({act:'upload', fname:res[1]}),
					success:function(res){
						if(res.fres) {
							cm1Param(res.file_id);
							cm2Param(res.file_id);
							files.addItems([{id:res.file_id,label:res.name,position:'before'}]);
							files.select(res.file_id);
							self.file=res;
							$('.import #info').show('fast');
							fileInfo();
							tabs.hide('tabView');
							checkBut.show();
							if(self.file.gr>0) {
								viewBut.show();
								cmTabs.hide('gr1');
								cmTabs.hide('gr2');
								cmTabs.show('gr'+self.file.gr);
								cmTabs.select('gr'+self.file.gr);
								parseBut.show();
								if(self.file.gr==2) $('#replicaBrand_sw').show('fast'); else $('#replicaBrand_sw').hide('fast');
								if(self.file.gr==1) $('#tyresSuffixes_sw').show('fast'); else $('#tyresSuffixes_sw').hide('fast');
								if(self.file.gr==1) $('#updateTyresSuffix_sw').show('fast'); else $('#tyresSuffixes_sw').hide('fast');
								checkParseBut.show();
							}else{
								viewBut.hide();
								cmTabs.show('gr1');
								cmTabs.show('gr2');
								parseBut.hide();
								checkParseBut.hide();
							}
							
							$$.note('Загружен '+file.name+'<br>Средняя скорость '+Math.round(data.speed)+' kB/s<br>'+$$.fresMsg(res.fres_msg),'long'); 
						}else $$.note('Файл '+file.name+' НЕ ЗАГРУЖЕН!<br>'+$$.fresMsg(res.fres_msg),'error');
					},
					complete: function(){
						$$.overlay(0);
					}
				});
		  	}else{
                $$.note('Файл '+file.name+' НЕ ЗАГРУЖЕН!<br>ОТВЕТ: '+response+'<br>DATA: '+$.param(data),'error');
			}
		}
	});
/*this.file.file_id=1;
var a=1;

function foo (){
	self.f();	
}

	new $$.Button({
		anc:'new tab',
		renderTo: '.import #_upload',
		onClick: self.f
	});
		new $$.Button({
		anc:'del active tab',
		renderTo: '#uil-center',
		onClick: function(){
			tabs.del(tabs.selectedId());
		}
	});
*/
/*	new $$.Button({
		anc:'открыть модал',
		renderTo: '.import #_upload',
		onClick: function(){
			new $$.Dialog({html:Math.random()+'',modal:false,renderTo:'#uil-center',
			onOpen: function(e){
				new $$.Button({
					anc:'button',
					renderTo: '#'+$(e.target).attr('id')
				});
			}
			});
		}
	});*/
/*
	$$.Dlg2=$$.extend($$.Dialog,{
				   constructor: function(e){
					   $$.Dlg2.superclass.constructor.call(this,e)
				   },
						  onOK: function(e){
							  $$.Dlg2.superclass.onOK.call(this,e);
							  $$.note(' OK from Dlg2');
						  }
	});
	
	new $$.Button({
		anc:'открыть модал 2',
		renderTo: '#uil-center',
		onClick: function(){
			new $$.Dlg2({html:Math.random()+'',modal:false})
		}
	});
	
	
	new $$.Button({
		anc:'открыть окно',
		renderTo: '#uil-center',
		onClick: function(){
			new $$.Window({html:Math.random()+'',position:[Math.abs(Math.round(screen.width*Math.random()+200)-400),Math.abs(Math.round(screen.height*Math.random())-400)],buttons: { "Ok": function() { $(this).dialog("close"); } }});
		}
	});
	*/

}}};
$(document).ready(CatImport.init);