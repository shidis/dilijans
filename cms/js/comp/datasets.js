$(document).ready(function(){


	$.ajaxSetup({
		type:'POST',
//		global: true,
		cache:false,
		dataType: 'json',
		url: '../be/dataset.php',
		error: Err
	});

	$('.ds-add').button().click(datasetAdd);
	$('.ds-save').button().click(datasetSave);
	$('.ds-edit').button().click(datasetEdit);
    $('.ds-del').button().click(datasetDel);
    $('.ds-clear').button().click(datasetClear);
	$('.ds-hideForm').button().click(function(){
		$('#ds-form').fadeOut(0);
		$('#ds-list').fadeIn(100);
	});
	
	var loader=$('.workspace').cloader();
	$(document).ajaxStart(function() {
		loader.cloader('show');
	})
	.ajaxStop(function() {
	  	loader.cloader('hide');
	});
	
	$("#ds-class").buttonset();
	
	$('.dsTabs').each(function(){
        $(this).tabs();
    });

	$('#ds-class input').change(function(e){
		var c=$(this).attr('id');
		c=c.split('-');
		c=c[1];
		$('#ds-form .dsf').hide();
		$('#ds-form .dsf').removeClass('active');
		$('#ds-form #dsf-'+c).show();
		$('#ds-form #dsf-'+c).addClass('active');
	});
	

	function datasetEdit(){
		console.log('datasetEdit');
		var c=$(this).parent().parent().attr('c');
		var dataset_id=$(this).parent().parent().attr('dataset_id');
		$.ajax({
			data: {
				act: 'loadDataSet',
				'dataset_id': dataset_id,
				'class': c
			},
			success: function(r){
				if(r.fres){
					$('#ds-form .fs-class').hide();
					$('#ds-form .fs-gr').hide();
					$('#ds-form .dsf').hide();
					$('#ds-form .dsf').removeClass('active');
					$('#ds-form #dsf-'+c).show();
					$('#ds-form #dsf-'+c).addClass('active');
					$('#ds-form').fadeIn(100);
					$('#ds-list').fadeOut(0);
					$('#ds-form #dsf-'+c+' [name=dataset_id]').val(dataset_id);
					$("#dsTabs").tabs( "option", "active", 0 );
					for(var field in r.data){
						if(typeof r.data[field][1] != 'undefined'){
							var el=$('#ds-form #dsf-'+c+' [name="'+field+'[1]"]');
							if(el.tagName=='textarea') el.html(r.data[field][1]); else el.val(r.data[field][1]);
						}
						if(typeof r.data[field][2] != 'undefined'){
							var el=$('#ds-form #dsf-'+c+' [name="'+field+'[2]"]');
							if(el.tagName=='textarea') el.html(r.data[field][2]); else el.val(r.data[field][2]);
						}
						if(!isArray(r.data[field])){
							var el=$('#ds-form #dsf-'+c+' [name="'+field+'"]');
							if(el.tagName=='textarea') el.html(r.data[field]); else el.val(r.data[field]);
						}
					}
				}else err(r.fres_msg);
			}
		});
	}
	
	function datasetAdd(){
        console.log('datasetAdd');
		$('#ds-class input').prop('checked',false);
		$('#ds-class input').removeAttr('aria-pressed');
		$('#ds-class label').removeClass('ui-state-active');
		$('#ds-class input').get(0).checked;
		$('#ds-class input').first().attr('aria-pressed','true');
		$('#ds-class label').first().addClass('ui-state-active');

		$('#ds-form .fs-class').show();
		$('#ds-form .fs-gr').show();
		$('#ds-form form.dsf:first').show();
		$('#ds-form form').each(function(i,el){
			el.reset();
		});
		$('#ds-form form:first').addClass('active');
		$('#ds-list').fadeOut(100);
		$('#ds-form').fadeIn(100);
		$("#dsTabs").tabs( "option", "active", 0 );
		
	}
	
	function datasetSave(){

        var c=$('#ds-form .active').attr('id'),
        $classInput = $('#ds-form .active').find('[name="class"]'),
		data;
        c=c.split('-');
        c=c[1];
		console.log($classInput.val(), !$classInput.val());
        if(!$classInput.val()) {
            data = {
                act: 'saveDataSet',
                'class': c,
                f: $('form#dsf-'+c).serialize()
            }
        } else {
            data = {
                act: 'saveDataSet',
                'class': $classInput.val(),
                f: $('form#dsf-'+c).serialize()
            }
        }
        console.log('Дата ', data);
		$.ajax({
			data: data,
			success: function(r){
				if(r.fres){
					note('Записано');
					$('#ds-form').fadeOut(0);
					$('#ds-list').fadeIn(100);
					location.href=location.href;
				}else err(r.fres_msg);
			}
		});
	}
	
	function datasetDel(){
        console.log('datasetDel');
		var c=$(this).parent().parent().attr('c');
		var dataset_id=$(this).parent().parent().attr('dataset_id');
		if(!confirm('Удаляем?')) return;
		$.ajax({
			data: {
				act: 'delDataSet','dataset_id': dataset_id
			},
			success: function(r){
				if(r.fres){
					location.href=location.href;
				}else err(r.fres_msg);
			}
		});
		
	}

    function datasetClear(){
        console.log('datasetClear');
        var c=$(this).parent().parent().attr('c');
        var dataset_id=$(this).parent().parent().attr('dataset_id');
        if(!confirm('Очистить содержимое набора?')) return;
        $.ajax({
            data: {
                act: 'clearDataSet','dataset_id': dataset_id
            },
            success: function(r){
                if(r.fres){
                    location.href=location.href;
                }else err(r.fres_msg);
            }
        });

    }
});

