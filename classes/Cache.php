<?
if (!defined('true_enter')) die ("Direct access not allowed!");

class Cache {
	static $defaultLT=15; //min
	static $cConfig=array(  // LT: lifetime in min | LT==-1 -> ==defaultLT | LT==0 => unlimited
		'ab'=>array('LT'=>43200),
		'ss_news'=>array('LT'=>-1),
		'ss_cnt'=>array('LT'=>-1),
		'tc'=>array('LT'=>100000),
		''=>array('LT'=>-1)
	);

function cBlock(){  // foo, block_name,....func_args | результаты выполнения foo не кешируется, только поток
	if(func_num_args()<2) {
		echo 'CACHE :: invalid arguments num.';
		return false;
	}
	$foo=func_get_arg(0);
	$blockName=func_get_arg(1);
	if(!function_exists($foo)){
		echo 'CACHE :: function not exists: '.$foo;
		return false;
	}
	if(!isset(static::$cConfig[$blockName])) {
		echo 'CACHE :: invalid block_name '.$blockName;
		return false;
	}
	$arg_list= array_slice(func_get_args(),2);
	$cf=md5($foo.serialize($arg_list)).'.html';
	$cp=$_SERVER['DOCUMENT_ROOT'].'/'.Cfg::get('cache_dir').'/'.$blockName.'/'.$cf;
	$LT=static::$cConfig[$blockName]['LT']!=-1?static::$cConfig[$blockName]['LT']:static::$defaultLT;
	if(is_file($cp) && ($LT>0 && (time()-filemtime($cp))<$LT*60 || $LT==0)){
		$f=fopen($cp,'r');
		echo fread($f,filesize($cp));
		fclose($f);
		return true;
	}
	if(!is_dir($_SERVER['DOCUMENT_ROOT'].'/'.Cfg::get('cache_dir').'/'.$blockName)) mkdir($_SERVER['DOCUMENT_ROOT'].'/'.CACHE_DIR.'/'.$blockName,0777);
	ob_start();
	$res=call_user_func_array($foo,$arg_list);
	$f=fopen($cp,'w');
	$out=ob_get_contents();
	fwrite($f,$out,mb_strlen($out));
	fclose($f);
	ob_end_flush();
	return true;
}

}
?>