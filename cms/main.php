<? include('auth.php')?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="css/left_frm.css" rel="stylesheet" type="text/css">
</head>

<?
@define (true_enter,1);
require_once ($_SERVER['DOCUMENT_ROOT'].'/config/init.php');

require_once($_SERVER['DOCUMENT_ROOT'].'/cms/inc/utils.php');
?>
<body style="<?=($bgColor=Data::get('cms_left_col_bg'))!=''?"background:$bgColor":''?>">
<div style="margin-left:0px;">
<?


function blk($t='',$id='',$a="javascript:;", $align='center')
{?>
    <DIV class="m"><DIV class="h"  align="<?=$align?>"><?=$a!=''?"<a class=\"left-menu-header\" target=\"body\" href=\"$a\"".($id!=''?" onClick=\"toggle('$id'); return false;\"":'').">":''?><?=$t?><?=$a!=''?"</a>":''?></DIV></DIV>
    <DIV class="mb" id="<?=$id?>" style="<?=$id!='' && @$_COOKIE[$id]=='1'?'display:none':'display:block'?>">
<? }

function blk_close()
{?>
    </DIV>
<? }

function menu($t,$id)
{
global $cp;
if(!$cp->qnum()) return;
blk($t,$id);?>
<ul class="menu">
<? 
	if($cp->qnum()) while($cp->next()!==false) if($cp->qrow['H']!=1){?>
		<li class="menu">
		<? if($cp->qrow['path']!=''){
			$path=explode('|',$cp->qrow['path']);
			if(count($path)>1 && $path[0]=='win') {$win=true; $u=$path[1];} else $win=false;
			if(count($path)>1 && $path[0]=='blank') {$blank=true; $u=$path[1];} else $blank=false;
			if(count($path)==1)  $u=$path[0];
			$path[2]=@$path[2]!=''?$path[2]:'';?>
			<a href="<?=$win?'javascript:;':$u?>" target="<?=(@$blank || $win)?'_blank':'body'?>" <?=$win?"onclick=\"return openwin('$u','{$path[2]}');\"":''?>> <?=Tools::unesc($cp->qrow['title'])?></a>
		<? }?></li>
	<? }?>
</ul>
<? blk_close();
}


blk(Cfg::get('site_name'),'','/cms/body.php');
blk_close();
$cp->getMenuList('control',1,1);
menu('Управление сайтом','__cp_menu_control');
$cp->getMenuList('orders',1,1);
menu('Заказы','__cp_menu_orders');
$cp->getMenuList('tyres',1,1);
menu('Шины','__cp_menu_tyres');
$cp->getMenuList('disks',1,1);
menu('Диски','__cp_menu_disks');
$cp->getMenuList('stat',1,1);
menu('Статистика','__cp_menu_stat');

blk('Admin','__cp_menu_admin');?>
<ul class="menu">
    <li class="menu"><a href="body.php" target="body">Главная CMS</a>
    <li class="menu"><strong>IP:</strong> <?=$_SERVER['REMOTE_ADDR']?></li>
    <li class="menu"><strong>server_loc:</strong> <?=server_loc?></li>
    <li class="menu"><strong>Пользователь: </strong><?=CU::$login?></li>
    <li class="menu"><strong>Уровень доступа: </strong><?=CMS_LEVEL_ACCESS?></li>
    <li class="menu"><a href="dumper.php" target="body">DB Dumper</a></li>
    <li class="menu"><a href="sxd76/" target="body">DB Dumper Pro</a></li>
    <? if(CU::$roleId==1){?>
    <li class="menu"><a href="pi.php" target="body">PHP Info</a></li>
    <li class="menu"><a href="memcache.php" target="body">Memcached</a></li>
    <li class="menu"><a href="apc.php" target="body">APC Accelerator</a></li>
    <? }?>
    <li class="menu"><a href="javascript:;"  onClick="top.location.href='/cms/?logout=1'">Выход</a></li>
</ul>
<? blk_close();?>
</div>
</body>
</html>
