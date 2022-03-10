<?
if (!defined('true_enter')) die ("No direct access allowed.");

abstract class ExLib {
	
	public static
		$JS=array(),
		$CSS=array(),
		$debug=0,
		$cJS='',
		$cCSS='',
		$cImg='';
		
	
	public static function concatJS()
	{
		$js='';
		foreach(static::$JS as $k=>$v){
			$js.=';;'.file_get_contents(Cfg::$config['root_path'].$v['file'])."\r\n";
		}
		$f=@fopen(Cfg::$config['root_path'].'/assets/cache/js/concat.js','w');
		if($f!==false) {
			fwrite($f,$js);
			fclose($f);
			return true;
		}else return false;
	}
	
	public static function concatCSS()
	{
		$css='';
		foreach(static::$CSS as $k=>$v){
			$css.=@file_get_contents(Cfg::$config['root_path'].$v['file'])."\r\n";
		}
		$f=@fopen(Cfg::$config['root_path'].'/assets/cache/css/concat.css','w');
		if($f!==false) {
			fwrite($f,$css);
			fclose($f);
			return true;
		}else return false;
	}
	
	public static function vJS()
	{
		$f=@fopen(Cfg::$config['root_path'].'/assets/res/jsv','w');
		if($f!==false){
			fwrite($f,(string)mt_rand(1000,9999));
			fclose($f);
		}
	}
	
	public static function vCSS()
	{
		$f=@fopen(Cfg::$config['root_path'].'/assets/res/cssv','w');
		if($f!==false){
			fwrite($f,(string)mt_rand(1000,9999));
			fclose($f);
		}
	}

	public static function vImages()
	{
		$f=@fopen(Cfg::$config['root_path'].'/assets/res/imgv','w+');
		if($f!==false){
			fwrite($f,(string)mt_rand(1000,9999));
			fclose($f);
			return true;
		}else{
			return false;
		}
	}

    public static function loadCSSId()
    {
        return static::$cCSS=$vv=@file_get_contents(Cfg::$config['root_path'].'/assets/res/cssv');
    }

    public static function loadJSId()
    {
        return static::$cJS=$vv=@file_get_contents(Cfg::$config['root_path'].'/assets/res/jsv');
    }

	public static function loadImagesId()
    {
        return static::$cImg=$vv=@file_get_contents(Cfg::$config['root_path'].'/assets/res/imgv');
    }

    public static function getMetaJS()
	{
		$debug=static::isDebug();
		$vv=static::loadJSId();
		$s='';
		if(!$debug){
			$s.="<script type=\"text/javascript\" src=\"/assets/cache/js/concat.js?{$vv}\"></script>\r\n";
		}else{
			foreach(static::$JS as $k=>$v)
				$s.="<script type=\"text/javascript\" src=\"{$v['file']}?{$vv}\"></script>\r\n";
		}
		return $s;
	}

	public static function getMetaCSS()
	{
		$debug=static::isDebug();
		$vv=static::loadCSSId();
		$s='';
		if(!$debug){
			$s.="<link rel=\"stylesheet\" href=\"/assets/cache/css/concat.css?{$vv}\" />\r\n";
		}else{
			foreach(static::$CSS as $k=>$v)
				$s.="<link rel=\"stylesheet\" href=\"{$v['file']}?{$vv}\" />\r\n";
		}
		return $s;
	}
	
	public static function debug($v)
	{
		if($v) $v=1; else $v=0;
		
		$f=@fopen(Cfg::$config['root_path'].'/assets/res/exlibdebug','w');
		if($f!==false){
			fwrite($f,static::$debug=$v);
			fclose($f);
			return true;
		}else return false;
	}
	
	public static function isDebug()
	{
		$v=@file_get_contents(Cfg::$config['root_path'].'/assets/res/exlibdebug');
		return static::$debug=(int)$v;
	}


}
	
	
		
		
