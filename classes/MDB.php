<?
class MDB
{
    public static $connId=null;
    public static $db=null;
    public static $e;


    /*
     * в случае ошибки ошибки exeption не выбрасывается, но пишутся в текстовый лог и возвращается false
     */
    public static function  connect()
    {
        if(empty(Cfg::$config['mongodb_host'])) return false;
        try{
            static::$connId=new Mongo('mongodb://'.Cfg::$config['mongodb_host'].':'.Cfg::$config['mongodb_port']);
            static::$db=static::$connId->selectDB(Cfg::$config['mongodb_db'].'');
        } catch (MongoConnectionException $e) {
            $ex=new MDBException($e);
            static::$e = $ex->errorObject();
            return false;
        } catch (Exception  $e) {
            $ex = new MDBException($e);
            static::$e = $ex->errorObject();
            return false;
        }
        return static::$db;
    }


}


class MDBException extends MongoException
{

    public $e;

    function __construct($e)
    {
        $this->e=$e;

        $buf=
            "ERROR (".$e->getCode()."): ".$e->getMessage()."\n"
            .'Error at '.$e->getFile()." (".$e->getLine().")\n"
            ."Stack:\n"
            .$this->getExceptionTraceAsString($e)
            ."******end stack *******\n\n";

        $dt=Tools::dt();
        Tools::tree_mkdir(Cfg::$config['root_path'].'/assets/logs/');
        @file_put_contents(Cfg::$config['root_path'].'/assets/logs/mongoDB_errors.txt',"\n".$dt.' - '.@$_SERVER['REMOTE_ADDR'].' - '.@$_SERVER['HTTP_HOST'].@$_SERVER['REQUEST_URI'].' - '.$buf, FILE_APPEND);

        if(CU::isLogged()){
            echo nl2br(
                "<b>ERROR (".$e->getCode()."): ".$e->getMessage()."</b>\n\n"
                .'<b>Error at</b> '.$e->getFile()." (".$e->getLine().")\n\n <b>Stack:</b>\n"
                .$this->getExceptionTraceAsString($e)
                ."******end stack *******<br>\n\n"
            );
        }


    }

    function errorObject()
    {
        return $this->e;
    }

    function getExceptionTraceAsString($e) {
        $rtn = "";
        $count = 0;
        foreach ($e->getTrace() as $frame) {
            $args = "";
            if (isset($frame['args'])) {
                $args = array();
                foreach ($frame['args'] as $arg) {
                    if (is_string($arg)) {
                        $args[] = "'" . $arg . "'";
                    } elseif (is_array($arg)) {
                        $args[] = "Array";
                    } elseif (is_null($arg)) {
                        $args[] = 'NULL';
                    } elseif (is_bool($arg)) {
                        $args[] = ($arg) ? "true" : "false";
                    } elseif (is_object($arg)) {
                        $args[] = get_class($arg);
                    } elseif (is_resource($arg)) {
                        $args[] = get_resource_type($arg);
                    } else {
                        $args[] = $arg;
                    }
                }
                $args = join(", ", $args);
            }
            $rtn .= sprintf( "#%s %s(%s): %s(%s)\n",
                $count,
                @$frame['file'],
                @$frame['line'],
                @$frame['function'],
                $args );
            $count++;
        }
        return $rtn;
    }
}