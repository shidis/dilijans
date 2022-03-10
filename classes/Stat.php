<?
if (!defined('true_enter')) die ("Direct access not allowed!");

class Stat {
	public static $DBQueries=0;
	private static $timeStart=0;
	private static $timeFinish=0;
    public static $dbConnIds=array(); // идентификаторы подключения к базе
    public static $dbConnNum=0;

	public static function init(){
		Stat::$timeStart=Stat::getMicroTime(1);
	}
	public static function incDB(){
		Stat::$DBQueries++;
	}
	public static function finish(){
		Stat::$timeFinish=Stat::getMicroTime(1);
	}
	public static function execTime(){
		return Stat::$timeFinish-Stat::$timeStart;
	}
	public static function getMicroTime() { 
    	list($usec, $sec) = explode(" ", microtime()); 
    	return ((float)$usec + (float)$sec); 
	} 		
}
