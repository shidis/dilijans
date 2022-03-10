$('document').ready(function(){
	$.ajaxSetup({
		type:'POST',
		global: true,
		cache:false,
		dataType: 'json',
//		timeout:1000,
		url: '../be/tc.php',
		error: Err,
		complete: ajaxComplete,
		beforeSend: ajaxBeforeSend
	});
	
	axC=ajaxComplete;
	
	$('#company_id').ajaxComplete(axC);
	$('#company_id').load('../be/tc.php',{act:'reload_co'});

	$('#company_id').change(function(e){
		if($(this).val()==0){
			$('#co_info').hide();
			$('#new_cities').hide();
			$('#city_list').html('<div class="clist"></div>');
		}else
			$.ajax({
				data: {act:'get_co',co_id:$(this).val()},
				success: function(r){
					if(r.fres!==false){
						$('#co_info').show();
						$('#co_info #name').html(r.co_info['name']);
						$('#co_info #site').html(r.co_info['site']);
						$('#co_disable').html(r.co_info['disabled']?'нет':'да');
						$('#new_cities').show();
						$('#city_list').html('<div class="clist"></div>');
						if(r.cities!=undefined) for(var n=0;n<r.cities.length;n++){
							$('#city_list div:first').append('<div class=c><div class=l>'+r.cities[n]['name']+'</div><input type="image" city_id="'+r.cities[n]['city_id']+'" src="../img/delete.gif"></div>');
						}
						$('.clist input').bind('click',delCity);
					}
					else {
						$('#city_list').html('<div class="clist"></div>');
						$('#co_info').hide();
						$('#new_cities').hide();
					}
				}
			});
	});
	
	$('#co_disable').click(function(e){
		e.preventDefault();
		$.ajax({
			data: {act:'disable_co_switch', co_id: $('#company_id').val()},
			success: function(r){
				if(r.fres){
					if(r.coDisabled) {
						$('#co_disable').html('нет'); 
						$('#company_id option[value='+$('#company_id').val()+']').css({'background-color':'#cccccc'});
					}else {
						$('#co_disable').html('да');
						$('#company_id option[value='+$('#company_id').val()+']').css({'background-color':'inherit'});
					}
				}else note(r.fres_msg,'error');
			}
		});
	});
					
	$('#co_site_edit').click(function(e){
		e.preventDefault();
		var s=window.prompt('Изменить сайт ТК на:',$('#co_info #site').text());
		if(s!=null) {
			$.ajax({
				data: {act:'change_site', site:s, co_id: $('#company_id').val()},
				success: function(r){
					if(r.fres){
						$('#co_info #site').html(s);
						note('Изменен сайт ТК');
					}else note(r.fres_msg,'error');
				}
			});
		}
	});
	$('#co_name_edit').click(function(e){
		e.preventDefault();
		var s=window.prompt('Изменить название ТК на:',$('#co_info #name').text());
		if(s!=null && s!='') {
			$.ajax({
				data: {act:'change_co_name', co_name:s, co_id: $('#company_id').val()},
				success: function(r){
					if(r.fres){
						$('#co_info #name').html(s);
						$('#company_id option[value='+$('#company_id').val()+']').text(s);
						note('Изменено название ТК');
					}else note(r.fres_msg,'error');
				}
			});
		}
	});
	
	$('#new_co_but').click(function(){
		$.ajax({
			data: {act:'new_co', co_name: $('#new_co').val(), co_site: $('#new_co_site').val()},
			success: function (res){
				note(res.fres_msg);
				if(res.fres!==false){
					$('#new_co').val('');
					$('#new_co_site').val('');
					$('#co_disable').text('да');
					$('#company_id').load('../be/tc.php',{act:'reload_co'});
					$('#co_info').hide();
					$('#new_cities').hide();
					
				}else if(res.co_alredy_exists) window.alert('Компания '+$('#new_co').val()+' уже есть в базе');
			}
		});
	});
	$('#del_co_but').click(function(){
		if($('#company_id').val()>0)
		if(window.confirm('Удалить ТК из базы?')){
		$.ajax({
			data: {act:'del_co', co_id: $('#company_id').val()},
			success: function (res){
				note(res.fres_msg);
				if(res.fres!==false){
					$('#company_id').load('../be/tc.php',{act:'reload_co'});
					$('#co_info').hide();
					$('#new_cities').hide();
					$('#city_list').html('<div class="clist"></div>');
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
