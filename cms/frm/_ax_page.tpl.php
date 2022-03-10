<? 

require_once '../auth.php';
include('../struct.php');

$cp->frm['name']='';
$cp->frm['title']='';

$cp->checkPermissions();

cp_head();
cp_css();
cp_js();

?>

<style type="text/css">
</style>

<script language="javascript">
$('document').ready(function(){
	
	$.ajaxSetup({
		type:'POST',
		global: true,
		cache:false,
		dataType: 'json',
		url: '../be/avto2.php',
		error: Err
	});
		
});

</script>

<? cp_body()?>
<? cp_title()?>



<? cp_end()?>