tc={
	updateCList:1,
	co_id:0,
}

$('document').ready(function(){
	$.ajaxSetup({
		type:'POST',
		cache:false,
		dataType: 'json',
//		timeout:1000,
		url: '../be/tc2.php',
		error: Err,
	});
	
	var loader=$('.workspace').cloader();
	$(document).ajaxStart(function() {
		loader.cloader('show');
	})
	.ajaxStop(function() {
	  	loader.cloader('hide');
	});
		
	$('#tabs').tabs();
	
	$('#co-dlg').dialog({
		autoOpen:false,
		modal:true,
		resizable:true,
		closeOnEscape:true,
		buttons: {
			'Записать': function() {
				if(tc.oper=='add'){
					$.ajax({
						data: {act:'new_co', f: $('#co-dlg form').serialize()},
						success: function (res){
							if(res.fres){
								$('#co_id').load('../be/tc2.php',{act:'reloadCo'});
								$('#co_info').hide();
								$('#co_cities').html('');
								tc.co_id=0;
								
							}else if(res.co_alredy_exists) note('Компания '+tc.co_name+' уже есть в базе'); else err(r.fres_msg);
						}
					});
				}else if(tc.oper=='edit'){
					
				}
				$( this ).dialog( "close" );
			},
			'Отмена': function() {
				$( this ).dialog( "close" );
			}
		}
	});

	$('#co_id').load('../be/tc2.php',{act:'reloadCo'});

	$('#co_id').change(function(e){
		if($(this).val()==0){
			$('#co_info').slideUp('fast');
			$('#co_cities').html('');
		}else
			$.ajax({
				data: {act:'getCo',co_id:$(this).val()},
				success: function(r){
					if(r.fres){
						$('#co_info').slideDown('fast');
						$.extend(tc,r.co_info);
						$('#co_info #name').html(tc.coName);
						if(tc.coDisabled) $('#co_disable').html('Показывать ТК на сайте'); else $('#co_disable').html('НЕ показывать ТК на сайте');
						$('#co_cities').load('../be/tc2.php',{act:'loadCoCities'});
					}
					else err(r.fres_msg);
				}
			});
	});
	
	$('#co_disable').click(function(e){
		e.preventDefault();
		$.ajax({
			data: {act:'disable_co_switch', co_id: tc.co_id},
			success: function(r){
				if(r.fres){
					tc.coDisabled=r.coDisabled;
					if(tc.coDisabled) $('#co_disable').html('Показывать ТК на сайте'); else $('#co_disable').html('НЕ показывать ТК на сайте');
				}else err(r.fres_msg);
			}
		});
	});
					
	$('#co_edit').click(function(e){
		e.preventDefault();
		$('co-dlg').option('title','Редактирование ТК '+tc.coName);
		$('co-dlg #co_name').val(tc.co_name);
		$('co-dlg #co_name').val(tc.co_site);
		$('co-dlg #co_name').val(tc.co_dSumMin);
		$('co-dlg #co_name').val(tc.co_dSumM3);
		tc.oper='edit';
		$('co-dlg').open();
	});
	
	$('#new_co_but').button().click(function(){
		$('co-dlg').option('title','Новая ТК ');
		$('co-dlg form').get(0).reset();
		tc.oper='add';
		$('co-dlg').open();
	});
	
	$('#del_co_but').click(function(){
		if($('#company_id').val()>0)
		if(window.confirm('Удалить ТК из базы?')){
		$.ajax({
			data: {act:'del_co', co_id: $('#co_id').val()},
			success: function (res){
				note(res.fres_msg);
				if(res.fres!==false){
					$('#co_id').load('../be/tc2.php',{act:'reloadCo'});
					$('#co_info').hide();
					$('#co_cities').html('');
				}
			}
		});
		}
	});
	$('#add_cities').click(function(){
		if($('#company_id').val()>0 && $('#cities').val()!='') {
			$.ajax({
				data: {act:'add_cities', cities: $('#cities').val(), company_id: $('#company_id').val()},
				success: function (r){
					note(r.fres_msg);
					if(r.fres!==false){
						$('#city_list').html('<div class="clist"></div>');
						if(r.cities!=undefined) for(var n=0;n<r.cities.length;n++){
							$('#city_list div:first').append('<div class=c><div class=l>'+r.cities[n]['name']+'</div><input type="image" city_id="'+r.cities[n]['city_id']+'" src="../img/delete.gif"></div>');
						}
						$('.clist input').bind('click',delCity);
					}
				}
			});
		}else note('Нельзя добавить');
	});
});

function delCity(e){
	var id=$(e.target).attr('city_id');
	if($('#confirm_delete').prop('checked') && window.confirm('Удалить '+$(e.target).prev('div.l').text()+'?') || !$('#confirm_delete').prop('checked')) {
		$.ajax({
			data: {act: 'del_city', city_id:id, co_id: $('#company_id').val()},
			success: function(r){
				if(r.fres){
					note('Удален '+$(e.target).prev('div.l').text());
					$('#city_list input[city_id='+id+']').parent().slideUp('fast',function(){
						$(this).remove();
					});
				} else note(r.fres_msg,'error');
			}
		});
	}
}
