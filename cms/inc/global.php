<?php

if (!defined('true_enter')) die ("Direct access not allowed!");

function warn($msg){
	?><div class="ui-widget msg-block"><div class="ui-state-error ui-corner-all" style="padding: 0pt 0.7em;"><p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: 0.3em;"></span><?=$msg?></p></div></div><?
}

function note($msg){
	?><div class="ui-widget msg-block"><div class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0pt 0.7em;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right: 0.3em;"></span><?=$msg?></p></div></div><?
}


function flu()
{
	@ob_get_contents();
	@ob_flush();
	@flush();
}

function fdate($dt)
{
	preg_match("/([0-9]{2})-([0-9]{2})-([0-9]{4})/",$dt,$m);
	if (count($m)<3) return(false); else
		return(date("Y-m-d",mktime(0,0,0,$m[2],$m[1],$m[3])));
}

function sdate($dt)
{
	preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})/",$dt,$m);
	if (count($m)<3) return(false); else
		return("{$m[3]}-{$m[2]}-{$m[1]}");
}
function utf($t)
{
	return iconv('windows-1251',"UTF-8",$t);
}
function cp1251($t)
{
	return iconv("UTF-8",'windows-1251',$t);
}

function refr()
{
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
}

function strarr($a)
{
	$r=array();
	$s=explode('&',$a);
	foreach($s as $k){
		$v=explode('=',$k);
		$r[urldecode($v[0])]=(urldecode($v[1]));
	}
	return $r;
}


?>