$(document).ready(function(){
	$.ajaxSetup({
		type:'POST',
//		global: true,
		cache:false,
		dataType: 'json',
		url: '../be/slider.php',
		error: Err
	});

	$('.sl-add').button().click(slideAdd);
	$('.sl-save').button();
	$('.sl-edit').button();
	$('.sl-hideForm').button().click(function(){
		$('#sl-form').fadeOut(0);
	});
	
	$("#sl-class").buttonset();

	/**
	 * Добавление слайда
	 */
	function slideAdd(){
		$('#sl-form form').each(function(i,el){
			el.reset();
		});
		$('#sl-form').fadeIn(100);
	}
});

