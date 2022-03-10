<?
$fname=@$_REQUEST['__q'];

ini_set('max_execution_time', 600);

//* у файла должно быть расширение. sname получаем, отбрасывая его
$fname=explode('.',$fname);
$ext=array_pop($fname);
$fname=join('.',$fname);


@define (true_enter,1);
require_once ($_SERVER['DOCUMENT_ROOT'].'/config/init.php');
require_once Cfg::$config['root_path'].'/app/classes/Sitemap.php';
BotLog::detect();

if($fname=='') {
	App_Sitemap::index();
	exit;
}

if(is_callable(array('App_Sitemap',$fname),false)) {
	header ("Content-type: text/xml");
	if(isset($_GET['f'])) header("Content-disposition: attachment; filename=$fname");
	header('Pragma: public');
	App_Sitemap::header();
	call_user_func(array('App_Sitemap',$fname));
	App_Sitemap::close();
}else{
	echo 'Файл не найден';
}




