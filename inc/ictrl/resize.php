<?php

/* вызов 
http://site.ru/inc/ictrl/resize.php?i=http://site.ru/imageAdress.jpg&w=1000&h=1300&method=SO&stream=1&jpg=1
http://sm.s/inc/ictrl/resize.php?i=http://sm.s/tmp/test.png&w=100&h=1300&method=SO&stream=1&jpg=0
*/

define('true_enter',1);
define('ONLY_PATH_INIT',1);

require_once $_SERVER['DOCUMENT_ROOT'].'/config/init.php'; 
require_once Cfg::$config['root_path'].'/classes/GD.php';
require_once Cfg::$config['root_path'].'/classes/Msg.php';

//error_reporting(0);

$img=@$_GET['i'];
if(strpos($img,'http://')!==false){
	$img=parse_url($img);
	$img=$img['path'];
}
$img=$_SERVER['DOCUMENT_ROOT'].$img;
if(!@is_file($img)) return '';

switch (@$_GET['method']){
	case 'SO': $method='SO'; break;
	case 'BW': $method='BW'; break;
	case 'BH': $method='BH'; break;
	case 'SB': $method='SB'; break;
	default: $method='SO';
}

$res=GD::resize($method,$img,@$_GET['w'],@$_GET['h'], '','',true);

