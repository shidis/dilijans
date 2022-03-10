$('document').ready(function(){
	$.ajaxSetup({
		type:'POST',
		global: true,
		cache:false,
		dataType: 'json',
		url: '../be/dict.php',
		error: Err
	});

	overlay(1);
	$('#text').tinymce(
		$.extend(
			TM.cfg1,{
				//theme: 'simple',
				oninit: function(){
					overlay(0);
				}
			}
		)
	);
	
	loadBrands(1);
	$('#gr').change(function(e){
		loadBrands($(this).val());
	});
	$('#brand_id').change(function(){
		$(loading2).insertAfter('#brand_id');
		$.ajax({
			data: {act:'selectBrand','gr':$('#gr').val(),brand_id: $('#brand_id').val()},
			success: function(r){
				$('#editor').slideUp('fast');
				fillTerms(r);
				$('#brand_id + #loading2').remove();
			}
		});
	});
	$('#frm').submit(searchTerm);
	$('#save').click(saveTerm);
});

function searchTerm(e){
	try{
	e.preventDefault();
	$(loading2).insertAfter('#search');
	$.ajax({
		data: {'act':'termByStr','gr':$('#gr').val(),brand_id:$('#brand_id').val(),'name':$('#name').val()},
		success: function(r){
			$('#search + #loading2').remove();
			if(r.fres){
				$('#text').html(r.text);
				$('#editor').slideDown('fast');
				dict_id=r.dict_id;
			}else note(r.fres_msg);
		}
	});
	}catch(e){alert(e);}
}

function saveTerm(e){
	try{
	e.preventDefault();
	$(loading2).insertAfter('#save');
	$.ajax({
		data: {'act':'saveTerm','gr': $('#gr').val(),brand_id: $('#brand_id').val(),'name': $('#name').val(),text: $('#text').html()},
		success: function(r){
			$('#save + #loading2').remove();
			note(r.fres_msg+' '+$('#name').val());
			dict_id=r.dict_id;
			$.ajax({
				data: {act:'selectBrand','gr':$('#gr').val(),brand_id: $('#brand_id').val()},
				success: function(r){
					fillTerms(r);
				}
			});
		}
	});
	}catch(e){ alert(e);}
}

function loadBrands(gr){
	try{
	$(loading2).insertAfter('#brand_id');
	$.ajax({
		data: {'act':'loadBrands','gr':gr},
		success: function(r){
			$('#editor').slideUp('fast');
			$('#brand_id').empty().append('<option value="0">Бренд</option>');
			$('#brand_id + #loading2').remove();
			for(var k in r.brands)
				$('#brand_id').append('<option value="'+r.brands[k].id+'">'+r.brands[k].name+'</option>');
			fillTerms(r);			
		}
	});
	}catch(e){ alert(e);}
}

function fillTerms(r){
	$('#terms').empty();
	for(var k in r.items){
		$('#terms').append('<div id="term'+r.items[k].dict_id+'"><a href="#">'+r.items[k].name+'</a><a class="termDel" href="#"><img src="../img/delete.gif"  align="absmiddle" width="12"></a></div>');
	}
	$('div#terms div[id*=term] a:even').bind('click',clickTerm);
	$('div#terms div[id*=term] a:odd').bind('click',delTerm);
}

var dict_id;

function clickTerm(e){
	try{
	e.preventDefault();
	var target=$(e.target);
	dict_id=target.parent().attr('id').substr(4);
	$('#name').val(target.text());
	$.ajax({
		data:{act:'termById','dict_id':dict_id}
		,complete: ajaxComplete
		,beforeSend: ajaxBeforeSend
		,success: function(r){
			$('#text').html(r.text);
			$('#editor').slideDown('fast');
		}
	});
	}catch(e){ alert(e);}
}
function delTerm(e){
	e.preventDefault();
	var target=$(e.target);
	var id=target.parent().parent().attr('id').substr(4);
	$.ajax({
		data:{act:'delTermById','dict_id':id}
		,complete: ajaxComplete
		,beforeSend: ajaxBeforeSend
		,success: function(r){
			if(id==dict_id) {
				$('#text').html('');
				$('#name').val('');
				$('#editor').slideUp('fast');
				dict_id=0;
			}
			$('#terms div#term'+id).remove();
		}
	});
}

