<? 
require_once '../auth.php';
include('../struct.php');

$cp->frm['name']='balances';
$cp->frm['title']='Остатки';

$cp->checkPermissions();

cp_head();
cp_css();
cp_js();
?>
<? cp_body()?>
<? cp_title()?>
<style>
	.link a{
		margin: 10px 5px;
		font-weight: 700;
	}
</style>
<div id="bs-form" class="edit_area">
	<h2>Для дисков</h2>
	<table width="100%">
		<tr><th align="left">Ссылки для пользователей</th><th align="left">Ссылки для отладки</th></tr>
		<tr>
			<td>
				<div class="link">В формате xml: <a target="_blank" href="http://<?=Cfg::get('site_url')?>/balances/balances_disk.xml">http://<?=Cfg::get('site_url')?>/balances/balances_disk.xml</a></div>
				<div class="link">В формате csv: <a target="_blank" href="http://<?=Cfg::get('site_url')?>/balances/balances_disk.csv">http://<?=Cfg::get('site_url')?>/balances/balances_disk.csv</a></div>
				<div class="link">В формате xls: <a target="_blank" href="http://<?=Cfg::get('site_url')?>/balances/balances_disk.xls">http://<?=Cfg::get('site_url')?>/balances/balances_disk.xls</a></div>
				<div class="link">Все диски в формате xml: <a target="_blank" href="http://<?=Cfg::get('site_url')?>/balances/all-balances_disk.xml">http://<?=Cfg::get('site_url')?>/balances/all-balances_disk.xml.xls</a></div>
			</td>
			<td>
				<div class="link">В формате xml: <a target="_blank" href="http://<?=Cfg::get('site_url')?>/balances/balances_disk.xml?debug=Y">http://<?=Cfg::get('site_url')?>/balances/balances_disk.xml</a></div>
				<div class="link">В формате csv: <a target="_blank" href="http://<?=Cfg::get('site_url')?>/balances/balances_disk.csv?debug=Y">http://<?=Cfg::get('site_url')?>/balances/balances_disk.csv</a></div>
				<div class="link">Все диски в формате xml: <a target="_blank" href="http://<?=Cfg::get('site_url')?>/balances/all-balances_disk.xml?debug=Y">http://<?=Cfg::get('site_url')?>/balances/all-balances_disk.xml.xls</a></div>
			</td>
		</tr>
	</table>
	<h2>Для шин</h2>
	<table width="100%">
		<tr><th align="left">Ссылки для пользователей</th><th align="left">Ссылки для отладки</th></tr>
		<tr>
			<td>
				<div class="link">В формате xml: <a target="_blank" href="http://<?=Cfg::get('site_url')?>/balances/balances_shiny.xml">http://<?=Cfg::get('site_url')?>/balances/balances_shiny.xml</a></div>
				<div class="link">В формате csv: <a target="_blank" href="http://<?=Cfg::get('site_url')?>/balances/balances_shiny.csv">http://<?=Cfg::get('site_url')?>/balances/balances_shiny.csv</a></div>
				<div class="link">В формате xls: <a target="_blank" href="http://<?=Cfg::get('site_url')?>/balances/balances_shiny.xls">http://<?=Cfg::get('site_url')?>/balances/balances_shiny.xls</a></div>
				<div class="link">Все шины в формате xml: <a target="_blank" href="http://<?=Cfg::get('site_url')?>/balances/all-balances_shiny.xml">http://<?=Cfg::get('site_url')?>/balances/all-balances_shiny.xml</a></div>
			</td>
			<td>
				<div class="link">В формате xml: <a target="_blank" href="http://<?=Cfg::get('site_url')?>/balances/balances_shiny.xml?debug=Y">http://<?=Cfg::get('site_url')?>/balances/balances_shiny.xml</a></div>
				<div class="link">В формате csv: <a target="_blank" href="http://<?=Cfg::get('site_url')?>/balances/balances_shiny.csv?debug=Y">http://<?=Cfg::get('site_url')?>/balances/balances_shiny.csv</a></div>
				<div class="link">Все шины в формате xml: <a target="_blank" href="http://<?=Cfg::get('site_url')?>/balances/all-balances_shiny.xml?debug=Y">http://<?=Cfg::get('site_url')?>/balances/all-balances_shiny.xml</a></div>
			</td>
		</tr>
	</table>
	<br>
    <!--<button class="bs-refresh">Обновить файлы</button>-->
</div>
<? cp_end()?>
