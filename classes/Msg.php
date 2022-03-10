<?php

class Msg {

    public static
        $result=true,
        $msg=array();

    public static function put($result=true,$msg='')
    {
        if(!empty($msg)) static::$msg[]=array($result,$msg);
        if(!$result) static::$result=false;
        return $result;
    }

    public static function asStr($glue='<br>')
    {
        $s=array();
        foreach(static::$msg as $v) if(!empty($v[1])) $s[]=$v[1];
        return implode($glue,$s);
    }

    public static function getResult()
    {
        foreach(static::$msg as $v) if(!$v[0]) return false;
        return true;
    }

    public static function clear()
    {
        static::$result=true;
        static::$msg=array();
    }

}
