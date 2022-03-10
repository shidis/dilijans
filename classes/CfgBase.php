<?
if (!defined('true_enter')) die ("Direct access not allowed!");

abstract class CfgBase {

	public static $config=array();
	
	
	public static function set($key,$value){
		static::$config[$key]=$value;
	}

	public static  function get($key){
		if (isset(static::$config[$key])) {
			return static::$config[$key];
		}else {
			$r='';
			return $r;
		}
	}

	public static  function _get($key){  // без запроса к БД
		return static::get($key);
	}
	
	public static function print_r() {
		print_r(static::$config);
	}

}