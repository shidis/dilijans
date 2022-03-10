var gals={};
gals.topic_id=0;
gals.queueCount=0;
gals.topics={};
gals.files=[];
gals.image_id=0;

$(document).ready(function(){

	$.ajaxSetup({
		type:'POST',
		global: true,
		cache:false,
		dataType: 'json',
		url: '../be/galleries.php',
		error: Err,
//		beforeSend: ajaxBeforeSend,
		complete: ajaxComplete
	});
	
	$('.gals #add').button().click(gals.add);
	$('.gals #edit').button().click(gals.edit);
	$('.gals #del').button().click(gals.del);
	
	gals.topicList();
	
	$('#gals_editWin').dialog({
		autoOpen: false,
		width: 500,
		modal: true,
		buttons: {
			'Записать': function(){
				$.ajax({
					data: {act:gals.act,f:	$('#gals_editWin form').serialize(), topic_id: gals.topic_id},
					success: function (r){
						if(r.fres){
							note(r.fres_msg);
							if(gals.act=='galAdd'){
								gals.topic_id=r.topic_id;
								gals.reloadImgs();
							}
							gals.topicList();
						} else note(r.fres_msg,'error');
					}
				});
				$( this ).dialog( "close" );
			},

			'Отмена': function(){
				$( this ).dialog( "close" );
			}
		}
	});

	//http://www.uploadify.com/documentation/

	$(".gals #upload").uploadify({
		swf: '/cms/inc/uploadify/uploadify.swf',
        uploader: '/cms/be/galleries.php?mode=upload&'+window.QSID,
		buttonText: 'Выбрать файлы',
		queueID: 'fileQueue',
		auto: true,
		multi: true,
		width: 251,
		height: 30,
		method: 'POST',
        progressData : 'percentage',
		fileExt: '*.*;*.jpg;*.jpeg;*.gif;*.png',
		fileDesc: "Файлы изображений (*.jpg;*.jpeg;*.gif;*.png)",

		onSelect: function(fileObj){
			$(".gals #upload").uploadify('settings','formData', {topic_id:gals.topic_id});
			gals.files=[];
		},
        onUploadError: function(file, errorCode, errorMsg, errorString){
			alert('ОШИБКА ПРИ ЗАГРУЗКЕ :: Error code = '+errorCode+' INFO: '+errorMsg + ' ' + errorString);
		},
		onCancel: function(fileObj) {
			note('Загрузка ' + fileObj.name + ' прервана!');
    	},
        onQueueComplete: function(queueData) {
			note((gals.queueCount)+' файлов из '+queueData.filesQueued+' ЗАГРУЖЕНО. ');
			gals.queueCount=0;
			gals.reloadImgs();
    	},
        onSWFReady : function() {
            note('The Flash file is ready to go.');
        },
        onUploadSuccess: function(file, data, response){
            if(response){
                if(data.indexOf('|')!=-1){
                    var res=data.split('|');
                    if(res[0]=='1'){
                        gals.queueCount++;
                        gals.files.push(res[1]);
                    }else note(res[2]);
                }else  note(data);
            }else{
                alert('onUploadSuccess: Ошибка '+ data + '  при загрузке файла '+ file);
            }
		},
        onSelectError: function(file){
            alert('The file ' + file.name + ' returned an error and was not added to the queue.');
        }
	});
	
	$('.gals #glist').change(function(e){
		gals.topic_id=$(this).val();
		$('.gals #tinfo').html(gals.topics[gals.topic_id]['info']);
		gals.reloadImgs();
	});
	
	$('#gals_iEditWin').dialog({
		autoOpen: false,
		width: 500,
		modal: true,
		buttons: {
			'Записать': function(){
				$.ajax({
					data: {act:gals.act,f:	$('#gals_iEditWin form').serialize(), image_id: gals.image_id},
					success: function (r){
						if(r.fres){
						} else note(r.fres_msg,'error');
					}
				});
				$( this ).dialog( "close" );
			},

			'Отмена': function(){
				$( this ).dialog( "close" );
			}
		}
	});

});

gals.iEdit=function(e){
	gals.image_id=$(this).parent().parent().attr('tid');
	gals.act='iEdit';
	$.ajax({
		data: {act:'get_image_info', image_id: gals.image_id},
		success: function (r){
			if(r.fres){
				$('#gals_iEditWin [name=title]').val(r.title);
				$('#gals_iEditWin [name=alt]').val(r.alt);
				$('#gals_iEditWin [name=link]').val(r.link);
				$('#gals_iEditWin [name=info1]').val(r.info1);
				$('#gals_iEditWin [name=info2]').val(r.info2);
				$('#gals_iEditWin').dialog('open');
			} else note(r.fres_msg,'error');
		}
	});
	
}

gals.iDel=function(e){
	gals.image_id=$(this).parent().parent().attr('tid');
	gals.act='iDel';
	$.ajax({
		data: {act:gals.act, image_id: gals.image_id},
		success: function (r){
			if(r.fres){
				gals.reloadImgs();
				gals.image_id=0;
				note(r.fres_msg);
			} else note(r.fres_msg,'error');
		}
	});
}

gals.topicList=function(){
	$.ajax({
		data: {act:'topic_list'},
		success:function(r){
			if(r.fres){
				$('.gals #glist').html('');
				gals.topics={};
				if(r.topics.length){
					var i=0;
					for(var v in r.topics){
						gals.topics[r.topics[v]['topic_id']]=r.topics[v];
						$('.gals #glist').append('<option value="'+r.topics[v]['topic_id']+'">'+r.topics[v]['name']+'</option>');
						if(gals.topic_id==0 && i==0) gals.topic_id=r.topics[v]['topic_id'];
						i++;
					}
				}
				if($('.gals #glist option[value='+gals.topic_id+']').length) {
					$('.gals #glist option[value='+gals.topic_id+']').attr('selected','selected');
					$('.gals #tinfo').html(gals.topics[gals.topic_id]['info'])
					$('.gals #glist').show();
					$('.gals #edit').show();
					$('.gals #del').show();
					$('.gals #upl').show();
					gals.reloadImgs();
				}else{
					$('.gals #tinfo').html('')
					$('.gals #glist').hide();
					$('.gals #edit').hide();
					$('.gals #del').hide();
					$('.gals #upl').hide();
					$('.gals #imgs').html('<ul></ul>');
				}
				//console.log(gals.topics);
			}else note(r.fres_msg,'error');
		}
	});
}

gals.add=function(e){
	$('#gals_editWin input').val('');
	$('#gals_editWin textarea').val('');
	gals.act='galAdd';
	$('#gals_editWin').dialog('option','title','Добавить галерею');
	$('#gals_editWin').dialog('open');
}



gals.edit=function(e){
	if(gals.topic_id==0) return;
	gals.act='galEdit';
	$('#gals_editWin [name=name]').val(gals.topics[gals.topic_id]['name']);
	$('#gals_editWin [name=sname]').val(gals.topics[gals.topic_id]['sname']);
	$('#gals_editWin [name=info]').val(gals.topics[gals.topic_id]['info']);
	$('#gals_editWin [name=img1_resize_mode] option[value='+gals.topics[gals.topic_id]['param']['img1_resize_mode']+']').attr('selected','selected');
	$('#gals_editWin [name=img1_w]').val(gals.topics[gals.topic_id]['param']['img1_w']);
	$('#gals_editWin [name=img1_h]').val(gals.topics[gals.topic_id]['param']['img1_h']);
	$('#gals_editWin [name=img2_resize_mode] option[value='+gals.topics[gals.topic_id]['param']['img2_resize_mode']+']').attr('selected','selected');
	$('#gals_editWin [name=img2_w]').val(gals.topics[gals.topic_id]['param']['img2_w']);
	$('#gals_editWin [name=img2_h]').val(gals.topics[gals.topic_id]['param']['img2_h']);
	$('#gals_editWin').dialog('option','title','Редактируем галерею '+gals.topics[gals.topic_id]['name']);
	$('#gals_editWin').dialog('open');
}
gals.del=function(e){
	if(gals.topic_id==0) return;
	if(!window.confirm('Удалить галерею со всеми изображениями?')) return;
	gals.act='galDel';
	$.ajax({
		data: {act:gals.act, topic_id: gals.topic_id},
		success: function (r){
			if(r.fres){
				note(r.fres_msg);
				gals.topic_id=0;
				gals.topicList();
			} else note(r.fres_msg,'error');
		}
	});
}

gals.reloadImgs=function(){
	$('.gals #imgs').html('<ul></ul>');
	$.ajax({
		data: {act:'imageList', topic_id: gals.topic_id},
		success: function (r){
			if(r.fres){
				//console.log(r.imgs);
				for(var v in r.imgs){
					$('.gals #imgs ul').append(
						'<li tid="'+v+'">'+
							'<div class="c"><input class="del" type="image" src="../img/delete.gif"><input class="edit" type="image" src="../img/edit.gif"></div>'+
							'<div class="i"><a href="'+r.imgs[v]['img2']+'"><img src="'+r.imgs[v]['thumb']+'"></a></div>'+
						'</li>'
					);
				}
				$('.gals #imgs .edit').click(gals.iEdit);
				$('.gals #imgs .del').click(gals.iDel);
			} else note(r.fres_msg,'error');
		}
	});
}