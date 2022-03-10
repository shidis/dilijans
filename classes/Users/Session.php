<?
class Users_Session
{
    public static $UID;
    public static $UID_Name='uuid';
    public static $storageEnabled=false;
    public static $firstHit=true;
    public static $doAdmin=false;
    // пользовательская сессия из БД
    public static $us;
    // курсоры
    public static $usess, $uacts, $epoints, $orders;
    //переданные данные из броузера
    public static $url;
    public static $referrer;
    public static $screen;
    public static $userAgent;
    //внутрисайтовый хит или внешний заход - если true то создается epoint
    public static $externalHit;

    /*
    * стартует сессию для нового юзера или подключает существующую
     * запускается из яваскрипта чтобы исключить ботов после init()
    * для посеителя с одним UID может быть несколько сессий
    * записывает в юзер сессию основные параемтры перехода:
    * реферер - для перехода извне
    */
    public static function start()
    {
        if(!static::$storageEnabled) return false;

        if(!empty(CU::$userId) && !static::$doAdmin) return false;

        // проверем куку
        if(!empty($_COOKIE[static::$UID_Name])) {
            // если есть, то проверяем наличие дока в базе
            static::$us=static::$usess->findOne(array('_id'=>new MongoId ($_COOKIE[static::$UID_Name])));
            if(!empty(static::$us['_id'])){
                // док найден
                static::$firstHit=false;
                static::$us['lh']=new MongoDate();
                static::$UID=(string)static::$us['_id'];
                //проверяем на реферер и ставим epi
                if(static::$externalHit){
                    static::$us['epi']++;
                }
                static::$usess->save(static::$us);

            }else{
                // перезаписываем куку
                static::addSess();
                static::setIDCookie();
                static::$firstHit=true;
            }

        }else{
            static::addSess();
            static::setIDCookie();
            static::$firstHit=true;
        }

        if(!empty(static::$UID)){
            if(static::$firstHit || static::$externalHit){
                static::addEPoint();
            }
        }

        return true;
    }

    /*
     * проверяет есть ли сессию у текущего пользователя (по куке)
     * если есть то загружает профиль юзера в класс
     * вызывается из стартового php скрипта
     * не запускает новою сессию и не ставит новой куки
     */
    public static function check()
    {
        if(!static::$storageEnabled) return false;
    }

    /*
     * инициализация движка
     * сессия еще не стартует
     * $r{
     * doAdmin - считать ли админов
     * referrer - document.referrer
     * url - document.URL
     * sw - screen.width
     * sh - screen.height
     * userAgent
     * }
     */
    public static function init($r=array())
    {
        if(!MDB::connect()) return false;
        static::$storageEnabled=true;
        static::$usess=MDB::$db->usess;
        static::$uacts=MDB::$db->uacts;
        static::$epoints=MDB::$db->epoints;
        static::$orders=MDB::$db->orders;
        if(!empty($r['doAdmin'])) static::$doAdmin=true;
        if(isset($r['url'])) static::$url=$r['url']; else {
            if(Request::isHTTPS()) $s='https://'; else $s='http://';
            $s.=@$_SERVER['HTTP_HOST'].@$_SERVER['REQUEST_URI'];
            static::$url=$s;
        }
        if(isset($r['referrer'])) static::$referrer=$r['referrer']; else static::$referrer=@$_SERVER['HTTP_REFERER'];
        if(isset($r['userAgent'])) static::$referrer=$r['userAgent']; else static::$referrer=@$_SERVER['HTTP_USER_AGENT'];
        if(isset($r['sw']) && isset($r['sh'])){
            static::$screen=array($r['sw'],$r['sh']);
        }else{
            static::$screen=null;
        }
        if(!empty(static::$referrer)){
            if(preg_match("~^http[s]*?\/\/".Cfg::$config['site_url']."~iu", static::$referrer))
                static::$externalHit=true;
            else
                static::$externalHit=false;
        }

        return true;

    }

    /*
     * добавляет сессию со всеми известными параметрами
     */
    public static function addSess()
    {
       $us=array(
            'ip'=>@$_SERVER['REMOTE_ADDR'],
            'lh'=>new MongoDate(),
            'epi'=>1
        );
        if(!empty(CU::$userId)) $us['cuid']=CU::$userId;
        try{
            $res=static::$usess->insert($us);
            // значением куки будет ObjectID(id)
            static::$UID=(string)$us['_id'];
            static::$us=$us;
        } catch(MongoException $e){
            new MDBException($e);
            return false;
        }
        return true;
    }


    /*
     * добавляет подсесиию/точку входа
     * в usess.epi для текущего юзера должно быть новое значение подсессии
     */
    public static function addEPoint()
    {
        if(empty(static::$us['epi'])) return false;
        $r=array(
            'i'=>static::$us['epi'],
            'usessId'=>static::$UID,
            'dt'=>new MongoDate(),
            'ip'=>$_SERVER['REMOTE_ADDR'],
            'du'=>static::$url,
            'ua'=>static::$userAgent
        );
        if(Tools::isMobile()) $r['ism']=true;
        if(!empty(static::$referrer)) $r['su']=static::$referrer;
        else{
            $r['s']='ti';
        }
        // предыдущая/последняя точка
        $prev=static::$epoints->findOne(array('usessId'=>static::$UID,'i'=>static::$us['epi']-1));
        if(!empty($prev)){
            //была уже точка входа.
            $r['ddt']=$prev['dt']-time();
        }
        if($prev['i']==1){
            //предыдущая точка онаже первая
            $r['ddt1']=$r['ddt'];
        }else{
            $prev=static::$epoints->findOne(array('usessId'=>static::$UID,'i'=>1));
            if(!empty($prev)){
                //первая точка входа
                $r['ddt1']=$prev['dt']-time();
            }
        }
        try{
            static::$epoints->insert($r);
        } catch(MongoException $e){
            new MDBException($e);
            return false;
        }
    }


    public static function setIDCookie()
    {
        if(empty(static::$UID)) return false;
        setcookie(static::$UID_Name, static::$UID, time()+8640000000 ,'/', Url::trimWWW(Cfg::get('site_url')));
        return $_COOKIE[static::$UID_Name]=static::$UID;
    }

    /*
     * внутренний переход по сайту
     */
    public static function internalHit()
    {
        if(!static::$storageEnabled) return;
    }

    /*
     * заход извне/нет реферера
     */
    public static function externalHit()
    {

    }

    public static function uSetVar($var,$val)
    {
        static::$us['uv'][$var]=$val;
        static::$usess->save(static::$us);
    }



}