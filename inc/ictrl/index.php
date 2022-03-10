<?
$param=@$_GET['i'];  
$mode=@$_GET['mode'];

define('true_enter',1);
define('ONLY_PATH_INIT',1);

require_once $_SERVER['DOCUMENT_ROOT'].'/config/init.php'; 
require_once Cfg::$config['root_path'].'/classes/WMark.php'; 
require_once Cfg::$config['root_path'].'/classes/Tools.php'; 
// ниже инициализация для BotLog
require_once Cfg::$config['root_path'].'/classes/Core.php'; 
require_once Cfg::$config['root_path'].'/classes/Stat.php'; 
require_once Cfg::$config['root_path'].'/classes/Common.php'; 
require_once Cfg::$config['root_path'].'/classes/DB.php'; 
require_once Cfg::$config['root_path'].'/classes/BotLog.php'; 


BotLog::detect();

$pi=parse_url($param);

$param='/'.$pi['path'];



//	print_r($t);
switch($mode){
	case 'cc':
		preg_match($ss="/\/".str_replace('/','\/',Cfg::$config['cc_images_dir'])
			."\/([a-zA-Z0-9]+)\/("
			.str_replace('/','\/',Cfg::$config['cc_brand_subdir'])
			."|"
			.str_replace('/','\/',Cfg::$config['cc_model_subdir'])
			.")\/([123]{1})\/([^\/]+)/",$param,$m);
//		print_r($m);
		if(@$m[4]!=''){
			switch ($m[2]){
				case Cfg::$config['cc_brand_subdir']:	$table='cc_brand';break;
				case Cfg::$config['cc_model_subdir']:	$table='cc_model';break;
				default:hi();
			}
			$imgNum=$m[3];
			$file=$m[4];
		}else hi();
		$fu=Cfg::$config['root_path'].str_replace('/'.Cfg::$config['cc_images_dir'].'/','/'.Cfg::$config['cc_upload_dir'].'/',$param);
		$fc=Cfg::$config['root_path'].str_replace('/'.Cfg::$config['cc_images_dir'].'/','/'.Cfg::get('cc_cache_images_dir').'/',$param);
		$cd=Cfg::$config['root_path'].'/'.Cfg::$config['cc_cache_images_dir'].'/'.$m[1].'/'.$m[2].'/'.$imgNum.'/';
		break;
	case 'cc_gal':
		preg_match($ss="/\/".str_replace('/','\/',Cfg::$config['cc_gal_images_dir'])."\/([123]{1})\/([^\/]+)/",$param,$m);
		if(@$m[2]!=''){
			$table='cc_gal';
			$imgNum=$m[1];
			$file=$m[2];
		}else hi();
		$fu=Cfg::$config['root_path'].str_replace('/'.Cfg::$config['cc_gal_images_dir'].'/','/'.Cfg::$config['cc_gal_upload_dir'].'/',$param);
		$fc=Cfg::$config['root_path'].str_replace('/'.Cfg::$config['cc_gal_images_dir'].'/','/'.Cfg::$config['cc_gal_cache_images_dir'].'/',$param);
		$cd=Cfg::$config['root_path'].'/'.Cfg::$config['cc_gal_cache_images_dir'].'/'.$imgNum.'/';
		break;
	case 'cc_cert':
		preg_match($ss="/\/".str_replace('/','\/',Cfg::$config['cc_cert_images_dir'])."\/([123]{1})\/([^\/]+)/",$param,$m);
		if(@$m[2]!=''){
			$table='cc_cert';
			$imgNum=$m[1];
			$file=$m[2];
		}else hi();
		$fu=Cfg::$config['root_path'].str_replace('/'.Cfg::$config['cc_cert_images_dir'].'/','/'.Cfg::$config['cc_cert_upload_dir'].'/',$param);
		$fc=Cfg::$config['root_path'].str_replace('/'.Cfg::$config['cc_cert_images_dir'].'/','/'.Cfg::$config['cc_cert_cache_images_dir'].'/',$param);
		$cd=Cfg::$config['root_path'].'/'.Cfg::$config['cc_cert_cache_images_dir'].'/'.$imgNum.'/';
		break;
	default: hi();
}
$t=Cfg::_get('cc_cache_transform');

if(@$t['wmark'][$table][$imgNum]) {
	$p=Cfg::_get('wmark_param');
    switch ($table){
        case 'cc_brand':
        case 'cc_model': WMark::init(@$p['cc_model']); break;
        case 'cc_gal': WMark::init(@$p['cc_gal']);  break;
        case 'cc_cert': WMark::init(@$p['cc_cert']);  break;
    }
	if(is_file($fc) || !WMark::wmark_exists()) output($fc);
	if(!file_exists($cd)) Tools::tree_mkdir($cd);
	if(@copy($fu,$fc)) 
		if(WMark::draw($fc)) output($fc); 
		else output($fu); 
	else hi(); // здесь можно сделать контроль битых фоток
}else output($fu);

function output ($file) {
	if(!is_file($file)) hi();
	$pi=pathinfo($file);
	switch(@$pi['extension']){
		case 'jpg':
		case 'JPG':
		case 'JPEG':
		case 'jpeg': $h="Content-type: image/jpeg"; break;
		case 'GIF':
		case 'gif': $h="Content-type: image/gif"; break;
		case 'PNG':
		case 'png': $h="Content-type: image/png"; break;
		default: h1i();
	}
	$ft=filemtime($file);
	header($h);
	//header("Cache-control: public");
	header("Content-length: ".filesize($file));
	header("Accept-ranges: bytes");
	if($ft!==false){
		header('Date: '.gmdate('D, d M Y H:i:s \G\M\T', $ft)); 
		header('Last-Modified: '.gmdate('D, d M Y H:i:s \G\M\T', $ft)); 
		header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time()+3600)); 	
	}
	echo file_get_contents($file);
	exit();
}

/*
Date: Wed, 24 Aug 2011 07:59:33 GMT
Via: 1.1 varnish
X-Varnish: 2968292237
Last-Modified: Mon, 11 Feb 2008 06:25:39 GMT
*/

function hi(){
	header("Content-type: image/gif");
	header("Cache-control: public");
	header('Date: Wed, 24 Aug 2011 07:59:33 GMT');
	header('Last-Modified: Wed, 24 Aug 2011 07:59:33 GMT');
	header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time()+3600));
    //header('cache-control: private, max-age = 3600');
	echo @file_get_contents("https://".$_SERVER['HTTP_HOST'].'/'.Cfg::get('spacer_path'));
	exit();
}

?>