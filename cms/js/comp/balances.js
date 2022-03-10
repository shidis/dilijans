$(document).ready(function(){


	$.ajaxSetup({
		type:'POST',
//		global: true,
		cache:false,
		dataType: 'json',
		url: '../be/balances.php',
		error: Err
	});

	$('.bs-refresh').button().click(balancesRefresh);

	var loader=$('.workspace').cloader();
	$(document).ajaxStart(function() {
		loader.cloader('show');
	})
	.ajaxStop(function() {
	  	loader.cloader('hide');
	});
	
	function balancesRefresh(){

	}
});

