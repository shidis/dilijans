<?
include('auth.php');
@define (true_enter,1);
require_once ($_SERVER['DOCUMENT_ROOT'].'/config/init.php');



if(@$_GET['logout']==1){

    CU::logoutCurrentSession();

	?><script>location.href='/cms/'</script><? 
	exit;
}



?>

<html>
<head>
    <title>CMS: <?=strtoupper(str_replace('www.','',Cfg::get('site_url')))?></title><?
    if(!empty(CU::$cmsStartUrl)){
        ?><script>location.href='<?=CU::$cmsStartUrl?>';</script><?
    }
    ?>
<script>
    window.name='<?=Cfg::get('site_name').'_cms'?>';
</script>
<frameset cols="156,*" >
	<frame name="main" src="main.php" scrolling="yes" frameborder="0" >
	<frame name="body" src="body.php" frameborder="0">
</frameset><noframes></noframes>
</head>
</html>
