<?
class MC extends CommonStatic
{
    private static $mc=null;
    public static $stats;
    private static $ver=null;
    public static $version;
    public static $uid;

    // TODO сделать проверку на доступную память

    public static function chk()
    {
        if(!MC::$mc) {
            if(class_exists('Memcache', false)) {
                MC::$ver=1;
                MC::$mc=new Memcache();
                MC::$mc->addServer($host=(string)Cfg::get('memcached_host'), $port=(int)Cfg::get('memcached_port'));
                MC::$stats = @MC::$mc->getExtendedStats();
                $available = (bool) @MC::$stats["$host:$port"];
                if ($available && @MC::$mc->connect($host, $port)) {
                    MC::$version='Memcache ver. '.@MC::$stats["$host:$port"]['version'];
                } else return static::putMsg(false, "MC.check():: Не могу подключиться к серверу Memcache");
                return true;
            } elseif(class_exists('Memcached', false)) {
                MC::$ver=2;
                MC::$mc=new Memcached(Cfg::get('site_name'));
                MC::$mc->addServer($host=(string)Cfg::get('memcached_host'), $port=(int)Cfg::get('memcached_port'));
                MC::$stats = @MC::$mc->getStats();
                $available = (bool) @MC::$stats["$host:$port"];
                if ($available){
                    MC::$version='Memcached ver. '.@MC::$stats["$host:$port"]['version'];
                } else return static::putMsg(false, "MC.check():: Не могу подключиться к серверу Memcached");
                return true;
            } else {
                return static::putMsg(false, "MC.check():: Не найден модуль кеширования Memcache(d)");
            }

        }else{
            if(MC::$ver==1)
                MC::$stats = @MC::$mc->getExtendedStats();
            else
                MC::$stats = @MC::$mc->getStats();
        }
        return true;
    }

    /*
     * @exp время жизни в секундах
     */
    public static function set($key,$v,$exp=0)
    {
        if(!MC::chk()) return false;
        if(MC::$ver==1){
            if(MC::$mc->replace($key,$v,0,$exp)===false){
                return MC::$mc->set($key,$v,0,$exp);
            }
        }else{
            if(MC::$mc->replace($key,$v,$exp)===false){
                return MC::$mc->set($key,$v,$exp);
            }
        }

        return true;
    }

    /*
     * Returns FALSE on failure, key is not found or key is an empty array.
     */
    public static function get($key)
    {
        if(!MC::chk()) return false;
        return MC::$mc->get($key);
    }

    public static function del($key)
    {
        if(!MC::chk()) return false;
        return MC::$mc->delete($key);
    }

    public static function sget($key)
    {
        return static::get($key.static::uid());
    }

    public static function sset($key,$v,$exp=0)
    {
        return static::set($key.static::uid(),$v,$exp);
    }

    public static function sdel($key)
    {
        return static::del($key.static::uid());
    }

    public static function uid()
    {
        if(empty(static::$uid)) static::$uid='__'.mb_substr(md5(Cfg::_get('site_name')),0,4);
        return static::$uid;
    }

    public static function checkMem()
    {
        if(!MC::chk()) return false;
        $i=@MC::$stats;
        $i=current($i);
        $total=@$i['limit_maxbytes'];
        $used=@$i['bytes'];
        $d=(int)@Cfg::$config['MemCache']['freeMemAlert'];
        if(empty($total)) return false;
        $res=[
            'status'=>'ok',
            'freePercent'=>ceil(($total-$used)/$total*100),
            'total'=>$total,
            'used'=>$used,
            'free'=>$total-$used
        ];
        if(!empty($d) && ($total*(1-$d/100))<$used) $res['status']='alert';
        return $res;
    }

}