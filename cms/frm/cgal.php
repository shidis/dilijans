<?
require_once '../auth.php';
include('../struct.php');

$gr=intval(@$_GET['gr']);
if(!$gr) die('Группа товаров не задана [gr]');


$cp->frm['name']='cgal';
$cp->frm['title']='Галерея '.($gr==2?'дисков':'шин');

$cp->checkPermissions();

cp_head();
cp_css();
cp_js();

?>
<? cp_body()?>
<? cp_title()?>
<style type="text/css">

</style>


<? cp_end()?>
