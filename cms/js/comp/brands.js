$(document).ready(function() { 

	$.ajaxSetup({
		type:'POST',
		cache:false,
		dataType: 'json',
		url: '../be/brands.php',
		error: Err
	});

    $("table").tablesorter({ 
		//   http://tablesorter.com/docs/#Configuration
		headers: {
			0: {sorter:false},
			8: {sorter:false},
			9: {sorter:false},
			13: {sorter:false},
			14: {sorter:false}
		}
    }); 
	
	var loader=$('.workspace').cloader();
	$(document).ajaxStart(function() {
		loader.cloader('show');
	})
	.ajaxStop(function() {
	  	loader.cloader('hide');
	});
	
	$('form').submit(function(e){
		loader.cloader('show');
	});
	
	$('#hhb1').change(function(){
		setCookie('__cp_hide_hidden_brands1',$(this).prop('checked') ? 1 : 0);
		location.href=location.href;
	});
	$('#hhb2').change(function(){
		setCookie('__cp_hide_hidden_brands2',$(this).prop('checked') ? 1 : 0);
		location.href=location.href;
	});

	$('tr.inds td').css('background','#63FF94');

	
	$(document).on('click','a.h-sw',function(e){
		e.preventDefault();
		var td=$(this).parent();
		var id=$(this).parent().parent('tr').attr('id');
		id=id.split('_');
		id=id[1];
		var s=td.html();
		if(id>0){
			td.html(loading2);
			$.ajax({
				data: {act:'hSwitch', 'id':id},
				success:function(r){
					if(r.fres){
						td.html(r.v);
					}else {
						td.html(s);
						note(r.fres_msg,'error');
					}
				}
			});
		} else note('нет ИД','note');
	});

    $('[name=avto_id], [name=sup_id]').chosen();

}); 

function del_cascade(){
	return confirm('Уверены?');
}

function do_form(id,action)
{
	if (action==1) document.form1.edit_id.value=id ;
	if (action==2) if (del_cascade()) document.form1.ld_id.value=id ; else return false;
	if (action==6) if (del_cascade()) document.form1.del_sel.value=1;  else return false;
	document.form1.submit();
	return false;
}
function SelectAll(mark,f) { 
  for (i = 0; i < document.forms[f].elements.length; i++)
     {
         var item = document.forms[f].elements[i];
	     if (item.id == "cc")  {
		     item.checked = mark;
		 };
	 }
}

