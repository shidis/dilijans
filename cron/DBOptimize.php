<?
@define (true_enter,1);

define ('ROOT_PATH', realpath(dirname(__FILE__).'/..'));
require_once (ROOT_PATH.'/config/init.php');

ob_start();

echo 'Optimizing database.....';
$db=new DB();

$db->query("OPTIMIZE TABLE cc_cat");
$db->query("OPTIMIZE TABLE cc_model");
$db->query("OPTIMIZE TABLE cc_brand");
$db->query("OPTIMIZE TABLE cc_cat_img");
$db->query("OPTIMIZE TABLE cc_cat_sc");
$db->query("OPTIMIZE TABLE cc_dataset_cat");
$db->query("OPTIMIZE TABLE cc_dataset_model");
$db->query("OPTIMIZE TABLE cc_dataset_brand");
$db->query("OPTIMIZE TABLE cc_dict");
$db->query("OPTIMIZE TABLE cc_gal");
$db->query("OPTIMIZE TABLE cc_suffix");
$db->query("OPTIMIZE TABLE cc_suplr");
$db->query("OPTIMIZE TABLE system_data");
$db->query("OPTIMIZE TABLE ss_news");
$db->query("OPTIMIZE TABLE ss_cnt");
$db->query("OPTIMIZE TABLE ss_pages");
$db->query("OPTIMIZE TABLE cu_users");
$db->query("OPTIMIZE TABLE cu_sessions");
$db->query("OPTIMIZE TABLE os_order");
$db->query("OPTIMIZE TABLE os_item");
$db->query("OPTIMIZE TABLE os_dop");

switch(Cfg::get('CAT_IMPORT_MODE')){
	case 1:
		$db->query("OPTIMIZE TABLE ci_file");
		$db->query("OPTIMIZE TABLE ci_item");
		$db->query("OPTIMIZE TABLE ci_model");
		$db->query("OPTIMIZE TABLE ci_tipo");
		$db->query("OPTIMIZE TABLE ci_model");
		break;
	case 2:
	case 3:
		$db->query("OPTIMIZE TABLE cii_file");
		$db->query("OPTIMIZE TABLE cii_item");
		break;
}

echo "ok\nEND CRON TASK.";

$buf=ob_get_clean();

if(!empty($_SERVER['REMOTE_ADDR'])) echo nl2br($buf); else echo $buf;
