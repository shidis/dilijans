<? 

require_once '../auth.php';
include('../struct.php');

$cp->frm['name']='oshed';
$cp->frm['title']='Каланедарь заказов';

$cp->checkPermissions();

cp_head();
cp_css();
cp_js();

?>

<style type="text/css">
</style>

<script language="javascript">

</script>

<? cp_body()?>

<? cp_title(true,true,true)?>

<?
$os=new App_Orders();

if(empty($os->adminCfg['purchase']['suplrSelectEnabled']) || empty($os->adminCfg['delivery']['DBF_deliveryDate'])){
    warn('<p>Функциональные ограничения. Останавлено.</p>');
    cp_end();
}



?>

<? cp_end()?>