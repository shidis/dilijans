<?php

/* вызов
картинка долдна быть на сервере по указанному пути
http://site.ru/inc/ictrl/crop.php?i=http://site.ru/imageAdress.jpg&x1=0&y1=0&x2=50%&y2%=50%&stream=1
http://sm.s/inc/ictrl/crop.php?i=http://sm.s/tmp/test.png&x1=0&y1=0&x2=50%&y2=50%&stream=1
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

$res=GD::crop($img,@$_GET['x1'],@$_GET['y1'],@$_GET['x2'],@$_GET['y2'],'','',true);


