<?

class Session
{

    public static $sid;
    public static $name = 'sid';
    public static $life_time = 0;
    public static $path = '/';
    public static $domain = '';
    private static $inited = false;

    public static function init($sname = 'sid', $life_time = 0, $path = '/', $domain = '', $probability='', $divisor='')
    {
        static::$inited = true;
        static::$name = $sname;
        if(empty($life_time)) $life_time = Cfg::$config['sessionCookieLifeTime'];
        static::$life_time = $life_time;
        static::$path = $path;
        if (empty($domain)) $domain = Url::trimWWW(Cfg::$config['site_url']);;
        static::$domain = $domain;
        /*
        Для сессий необходимо задать время жизни и логику работы мусорщика
        gc_probability/gc_divisor - вероятность запуска мусорщика в момент старта сессии
        session.gc_maxlifetime == session.cookie_lifetime == $life_time
        Можно выключить мусорщика, установив gc_probability=0  и чистить папку по крону.
        http://www.php.net/manual/en/session.configuration.php
        */
        if($probability!=='') ini_set('session.gc_probability', $probability);
        elseif(!isset(Cfg::$config['sessionProbability'])) ini_set('session.gc_probability', 1); else ini_set('session.gc_probability', Cfg::$config['sessionProbability']);
        if($divisor!=='') ini_set('session.gc_divisor', $divisor);
        elseif(!empty(Cfg::$config['sessionDivisor'])) ini_set('session.gc_divisor', Cfg::$config['sessionDivisor']); else ini_set('session.gc_divisor', 1000);
        session_name(static::$name);
        session_set_cookie_params($life_time, $path, $domain, false, true);
        session_save_path(realpath(Cfg::$config['root_path']) . '/' . Cfg::$config['sessionsSaveDir']);
        ini_set('session.gc_maxlifetime', $life_time);
        ini_set('session.use_cookies', 1);
        ini_set('session.use_trans_sid', 0);
        ini_set('session.name', $sname);
        ini_set('session.auto_start', 0);
        ini_set('session.cookie_path', $path);
        ini_set('session.cookie_domain', $domain);
        ini_set('session.cookie_secure', 0);
        ini_set('session.cookie_httponly', 1);
        return true;
    }

    /*
     * очистка файлов сессий по крону
     */
    public static function destroyExpired()
    {
        return static::init('sid', 0, '/', '', 1,1);
    }

    private static function cookieProlong()
    {
        return; // нельзя если http_only
        if(static::$sid != '')
            setcookie(
                static::$name,
                static::$sid,
                static::$life_time,
                static::$path,
                static::$domain,
                ini_get('session.cookie_secure'),
                ini_get('session.cookie_httponly')
            );
    }

    public static function destroy()
    {
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 42000, static::$path);
        }
        $_SESSION = array();
        @session_unset();
        @session_destroy();
        static::$sid = '';
    }

    /* принудительные старт сессии*/
    //!!!! наличие значение в session_id() НЕ ОЗНАЧАЕТ ЧТО СЕССИЯ СТАРТОВАНА. session_start() обязательно
    public static function start($id = '')
    {
        if (!static::$inited) static::init();
        if ($id != '') session_id($id);
        else {
            /*			if(isset($_GET[static::$name]) && isset($_REQUEST[static::$name]) && $_GET[static::$name]!=$_REQUEST[static::$name]) session_id($_GET[static::$name]);
                        elseif(isset($_POST[static::$name]) && isset($_REQUEST[static::$name]) && $_POST[static::$name]!=$_REQUEST[static::$name]) session_id($_POST[static::$name]);
            */
            if (isset($_GET[static::$name])) session_id($_GET[static::$name]);
            elseif (isset($_POST[static::$name])) session_id($_POST[static::$name]);

        }
        if (static::$sid == '') session_start();
        static::$sid = session_id();
        if (static::$sid != '') {
            static::mergeDataFromCookie();
            $_SESSION['started']=time();
            static::cookieProlong();
            return true;
        } else return false;
    }

    /* Проверяем на возможность старта сессии и стартуем ее  или проверяем на уже стартованную сессию*/
    public static function check()
    {
        if (!empty(static::$sid)) return true;
        if (!static::$inited) static::init();
        if (session_id() != '') {
            static::$sid = session_id();
//			session_start();
            return true;
        }
        if (!empty($_REQUEST[static::$name]) || !empty($_COOKIE[static::$name])) {
            if (!empty($_GET[static::$name]) && $_GET[static::$name] != @$_COOKIE[static::$name]) static::start($_GET[static::$name]);
            elseif (!empty($_POST[static::$name]) && $_POST[static::$name] != @$_COOKIE[static::$name]) static::start($_POST[static::$name]);
            else {
                session_start();
                static::$sid = session_id();
                if (static::$sid != '') {
                    static::mergeDataFromCookie();
                } else return false;
            }
        } else {
            static::$sid = '';
            return false;
        }
        static::cookieProlong();
        return true;
    }

    /*
     * сессия должны быть стартована
     */
    public static function mergeDataFromCookie()
    {
        if (empty(static::$sid)) return false;

        $coo=@$_COOKIE['SessVars'];
        if(!empty($coo)) {
            $coo=Tools::DB_unserialize($coo);
            if(is_array($coo)){
                foreach($coo as $k=>$v){
                    $_SESSION[$k]=$v;
                }
                Tools::delCookie('SessVars');
            }
        }
        return true;
    }

}
	