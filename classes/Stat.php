<?
if (!defined('true_enter')) die ("Direct access not allowed!");

class Stat
{
    public static $DBQueriesCount = 0;
    private static $timeStart = 0;
    private static $timeFinish = 0;
    public static $dbConnIds = []; // идентификаторы подключения к базе
    public static $dbConnNum = 0;
    public static $logDBQueries = false;
    public static $dbQueries = [];
    public static $dbQueriesTotalTime = 0;

    public static function init()
    {
        Stat::$timeStart = Stat::getMicroTime();
    }

    public static function incDB()
    {
        Stat::$DBQueriesCount++;
    }

    public static function finish()
    {
        Stat::$timeFinish = Stat::getMicroTime();
    }

    public static function execTime()
    {
        return Stat::$timeFinish - Stat::$timeStart;
    }

    public static function getMicroTime()
    {
        list($usec, $sec) = explode(" ", microtime());

        return ((float)$usec + (float)$sec);
    }
}
