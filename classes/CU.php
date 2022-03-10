<?
if (!defined('true_enter')) die ("Direct access not allowed!");

/*
 * Файловая сессия стартует помомо базовой для совместимости
 *
 *  userId==1 - корневая запись. Ее нельзя удалить и изменить уровень доступа, roleId=1
 *
 * просроченные сессии должны удаляться по крону
 */
class CU extends CommonStatic
{
    private static
        $db;

    public static
        $userId, // если не нулл, то значит рабочая сессия инициализована
        $sessVarName='csid', // имя сессионной переменной в куке или в гет/пост параметрах
        $SID, // ид рабочей сессии
        $defaultLifeTime=10080, // время жизни сессии по умолчанию (в минутах)
        $sessCookieSubDomains=true,
        $sdata, // переменные для сессии
        $udata, // переменые для пользователя
        $dtSessionStart,
        $dtSessionLastHit,
        $token,
        $disabled,
        $roleId,
        $login,
        $lifeTime,
        $fullName,
        $shortName,
        $firstName,
        $lastName,
        $email,
        $skype,
        $icq,
        $cmsStartUrl,
        $os // пользователь может работать с заказами
    ;



    /*
     * загрузка из базы инфы по юзерам в массив
     */
    public static function usersList($r=array())
    {
        if(empty(static::$db)) self::$db=new DB();

        $q=array();
        if(isset($r['os'])) $q[]="os=".(int)$r['os'];
        if(isset($r['roleId'])) $q[]="roleId=".(int)$r['roleId'];
        if(@$r['driversOnly']) $q[]="roleId=100";
        if(!empty($r['enabled'])) $q[]="disabled=0";
        if(!empty($r['orderBy'])) $order=$r['orderBy']; else $order="userId";
        if(!@isset($r['includeLD'])) $q[]="NOT LD";
        if(!empty($r['users'])){
            $a=array();
            foreach($r['users'] as $v){
                if((int)$v>0) $a[]=(int)$v;
            }
            if(!empty($a)) $q[]="userId IN(".implode(',',$a).")";
        }

        $q=implode(' AND ',$q);
        if(!empty($q)) $q='WHERE '.$q;


        $d=static::$db->fetchAll("SELECT cu_users.*, (SELECT count(*) FROM cu_sessions WHERE cu_sessions.userId=cu_users.userID) AS sessCount, (SELECT max(dtLastHit) FROM cu_sessions WHERE cu_sessions.userId=cu_users.userID) AS lastHit FROM cu_users $q ORDER BY $order");

        $r=array();
        if($d!==0){
            foreach($d as $v){
                $r[$v['userId']]=array(
                    'fullName'=>trim(Tools::unesc($v['firstName'].' '.$v['lastName'])),
                    'firstName'=>Tools::unesc($v['firstName']),
                    'lastName'=>Tools::unesc($v['lastName']),
                    'login'=>$v['login'],
                    'token'=>$v['token'],
                    'disabled'=>$v['disabled'],
                    'roleId'=>$v['roleId'],
                    'lifeTime'=>$v['lifeTime'],
                    'email'=>$v['email'],
                    'skype'=>$v['skype'],
                    'icq'=>$v['icq'],
                    'sCount'=>$v['sessCount'],
                    'lastHit'=>$v['lastHit'],
                    'os'=>$v['os'],
                    'cmsStartUrl'=>$v['cmsStartUrl']
                );
                if(!empty($v['firstName']) && !empty($v['lastName']))
                    $r[$v['userId']]['shortName']=trim(mb_substr(trim(Tools::unesc($v['firstName'])),0,1).'. '.trim(Tools::unesc($v['lastName'])));
                else
                    $r[$v['userId']]['shortName']=trim(Tools::unesc($v['firstName'].' '.$v['lastName']));
            }
        }
        return $r;
    }

    public static function destroyExpired()
    {
        if(empty(static::$db)) self::$db=new DB();

        return static::$db->query("DELETE t1 FROM `cu_sessions` AS t1 JOIN cu_users USING (userId) WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(dtLastHit))>(lifeTime*60)");

    }


    /*
     * логин и старт новой сессии
     *
     * если админская сессия уже инициализована в классе (userId>0), то ничего не будет сделано
     * если сессия в базе существует, то ее данные обновятся, ИД не изменится
     * если логпас не подошел, то текущая сессия остнется без изменений (если на есть)
     */
    public static function login($user,$pw)
    {
        if(empty(static::$db)) self::$db=new DB();

        $user=trim(Tools::esc($user));
        if(empty($user) || empty($pw)) return false;
        $pw=md5($pw);

        $d=static::$db->getOne("SELECT * FROM cu_users WHERE login='{$user}' AND pw='{$pw}' AND NOT LD AND NOT disabled");

        if($d!==0){
            if(static::$userId==$d['userId']) return true;
            static::$userId=$d['userId'];
            Session::start();
            static::$firstName=Tools::unesc($d['firstName']);
            static::$lastName=Tools::unesc($d['lastName']);
            static::$fullName=trim(Tools::unesc($d['firstName'].' '.$d['lastName']));
            if(!empty($d['firstName']) && !empty($d['lastName']))
                static::$shortName=trim(mb_substr(trim(Tools::unesc($d['firstName'])),0,1).'. '.trim(Tools::unesc($d['lastName'])));
            else
                static::$shortName=trim(Tools::unesc($d['firstName'].' '.$d['lastName']));

            static::$login=$d['login'];
            static::$token=$d['token'];
            static::$disabled=$d['disabled'];
            static::$roleId=$d['roleId'];
            static::$lifeTime=$d['lifeTime'];
            static::$email=$d['email'];
            static::$skype=$d['skype'];
            static::$icq=$d['icq'];
            static::$os=$d['os'];
            static::$cmsStartUrl=$d['cmsStartUrl'];
/*
            if(!empty($d['data'])){
                static::$udata=Tools::DB_unserialize($d['data']);
            } else
                static::$udata=array();
*/

            /*
             * проверяем на возможность существания сессионной записи
             * токен доложен совпадать
            */
            $dt=Tools::dt();
            $ua=Tools::esc(@$_SERVER['HTTP_USER_AGENT']);
            if(($SID=static::getSID())!=null){
                // TODO может стоит сделать проверку по айпи и юзерагенту
                $d=static::$db->getOne("SELECT * FROM cu_sessions WHERE userId=".static::$userId." AND sid='{$SID}' AND token='".static::$token."'", MYSQL_ASSOC);
                if($d!==0){
                    // есть базе запись - используем ее, дату старта не меняем
                    static::$SID=$SID;
                    static::$dtSessionStart=Tools::unesc($d['dtStart']);
                    static::$dtSessionLastHit=$dt;

                    if(!empty($d['data'])){
                        static::$sdata=Tools::DB_unserialize($d['data']);
                    } else
                        static::$sdata=array();

                    static::$db->query("UPDATE cu_sessions SET dtLastHit='".static::$dtSessionLastHit."', ip=INET_ATON('{$_SERVER['REMOTE_ADDR']}') WHERE sid='$SID'");
                }
            }
            if(empty(static::$SID)){
                // иначе добавляем в базу сессию
                static::$db->insert('cu_sessions', array(
                    'sid'=>static::$SID=Tools::randString(32),
                    'userId'=>static::$userId,
                    'dtStart'=>static::$dtSessionStart=$dt,
                    'dtLastHit'=> static::$dtSessionLastHit=$dt,
                    'token'=>static::$token,
                    'ip'=>array("INET_ATON('{$_SERVER['REMOTE_ADDR']}')",'noquot'),
                    'userAgent'=>$ua
                ));
                static::$sdata=array();
            }

            static::mergeSDataFromCookie();
            static::mergeUDataFromCookie();

            // ставим/обновляем вечную куку сессиионную
            $_COOKIE[static::$sessVarName]=static::$SID;
            if(static::$sessCookieSubDomains)
                setcookie(static::$sessVarName, static::$SID, time()+3600*24*365*3, '/', '.'.Url::trimWWW(Cfg::$config['site_url']), false, true);
            else
                setcookie(static::$sessVarName, static::$SID, time()+3600*24*365*3, '/', Cfg::$config['site_url'], false, true);
            return true;
        }
        // удаляем куку невалидной сессии
//        unset($_COOKIE[static::$sessVarName]);
//        setcookie(static::$sessVarName, static::$SID, time()-3600);
        return false;
    }


    /*
     * проверка на наличии сессии в базе и если есть - старт
     * токен должен совпадать
     */
    public static function isLogged()
    {
        if(static::$userId) return true;
        if(($SID=static::getSID())==null) return false;

        if(empty(static::$db)) self::$db=new DB();

        // TODO может стоит сделать проверку по айпи
        $d=static::$db->getOne("SELECT cu_users.*, cu_sessions.dtStart, cu_sessions.dtLastHit, cu_sessions.data AS sdata FROM cu_sessions JOIN cu_users USING (userId) WHERE sid='{$SID}' AND cu_users.token=cu_sessions.token AND NOT LD AND NOT disabled", MYSQL_ASSOC);

        if($d!==0){
            // есть базе запись сессии. Стартуем
            Session::start();
            static::$userId=$d['userId'];
            static::$firstName=Tools::unesc($d['firstName']);
            static::$lastName=Tools::unesc($d['lastName']);
            static::$fullName=trim(Tools::unesc($d['firstName'].' '.$d['lastName']));
            if(!empty($d['firstName']) && !empty($d['lastName']))
                static::$shortName=trim(mb_substr(trim(Tools::unesc($d['firstName'])),0,1).'. '.trim(Tools::unesc($d['lastName'])));
            else
                static::$shortName=trim(Tools::unesc($d['firstName'].' '.$d['lastName']));

            static::$email=$d['email'];
            static::$skype=$d['skype'];
            static::$icq=$d['icq'];
            static::$login=$d['login'];
            static::$token=$d['token'];
            static::$disabled=$d['disabled'];
            static::$roleId=$d['roleId'];
            static::$lifeTime=$d['lifeTime'];
            static::$SID=$SID;
            static::$os=$d['os'];
            static::$cmsStartUrl=$d['cmsStartUrl'];
            static::$dtSessionStart=Tools::unesc($d['dtStart']);
            static::$dtSessionLastHit=Tools::dt();

            if(!empty($d['sdata'])){
                static::$sdata=Tools::DB_unserialize($d['sdata']);
            } else
                static::$sdata=array();
/*
            if(!empty($d['data'])){
                static::$sdata=unserialize(base64_decode(Tools::unesc($d['udata'])));
            } else
                static::$udata=array();
*/

            static::mergeSDataFromCookie();
            static::mergeUDataFromCookie();

            $ua=Tools::esc(@$_SERVER['HTTP_USER_AGENT']);
            static::$db->query("UPDATE cu_sessions SET dtLastHit='".static::$dtSessionLastHit."', ip=INET_ATON('{$_SERVER['REMOTE_ADDR']}'), userAgent='{$ua}' WHERE sid='$SID'");
            return true;
        }
        return false;
    }



    /*
     * по гет и пост параметрам определить ИД сессии
     * смотрим также и в _GET[$sessVarName] и _POST[$sessVarName]
     * сессия не инициализируется
     */
    public static function getSID()
    {
        $SID=null;
        if(@$_GET[static::$sessVarName]!='') $SID=$_GET[static::$sessVarName];
        elseif(@$_POST[static::$sessVarName]!='') $SID=$_POST[static::$sessVarName];
        elseif(@$_COOKIE[static::$sessVarName]!='') $SID=$_COOKIE[static::$sessVarName];
        else return null;

        if(mb_strlen($SID)>255) {
            $SID=null;
            return false;
        }
        $SID=Tools::esc($SID);
        return $SID;
    }

    /*
     * добавлять могут только авторизованные пользователи
     */
    public static function addUser($r)
    {
        if(!static::isLogged()) return static::putMsg(false, '[CU.addUser] Не авторизован');

        if(@$r['roleId'] <= 0) return static::putMsg(false, '[CU.addUser]: Уровень доступа не ясен');
        if(@$r['roleId'] < static::$roleId) return static::putMsg(false, '[CU.addUser]: Можно добавить пользователя с уровнем доступа не меньше чем у вас');

        $q=array();

        foreach($r as $k=>$v){

            // имена полей в $r должны совпадать с полями БД
            switch ($k){

                case 'roleId':
                    $q['roleId']=(int)$v;
                    break;

                case 'os':
                    $q['os']=(int)$v;
                    break;

                case 'login':
                    $v=trim($v);
                    if(empty($v) || !preg_match("~^[a-zA-Z0-9_-]+$~iu", $v))
                        static::putMsg(false,'[CU.addUser]: недопустимые символы в логине или логин пустой');
                    $q['login']=$v;
                    break;

                case 'firstName':
                    $q['firstName']=Tools::esc(Tools::stripTags($v));
                    break;

                case 'lastName':
                    $q['lastName']=Tools::esc(Tools::stripTags($v));
                    break;

                case 'cmsStartUrl':
                    $q['cmsStartUrl']=Tools::esc(Tools::stripTags($v));
                    break;

                case 'email':
                    if(!empty($v) && !Tools::emailValid($v))
                        static::putMsg(false,'[CU.addUser]: недопустимые символы в email');

                    $q['email']=Tools::esc($v);
                    break;

                case 'icq':
                    if(!preg_match("~^[0-9\-]*$~iu", $v))
                        return static::putMsg(false,'[CU.addUser]: недопустимые символы в ICQ UIN');
                    $q['icq']=$v;
                    break;

                case 'skype':
                    if(!preg_match("~^[a-zA-Z0-9_-]*$~iu", $v))
                        static::putMsg(false,'[CU.addUser]: недопустимые символы в SKYPE');
                    $q['skype']=$v;
                    break;

                case 'lifeTime':
                    if($v<=0)  static::putMsg(false,'[CU.addUser]: Время жизни сессии должно быть больше нуля');
                    $q['lifeTime']=(int)$v;
                    break;

                case 'pw':
                    $v=trim(Tools::stripTags($v));
                    if(empty($v)) static::putMsg(false,'[CU.addUser]: Пароль не может быть пустым');
                    $q['pw']=md5($v);
                    break;

            }
        }

        if(!static::$fres) return false;

        if(empty($q['roleId'])) return static::putMsg(false,'[CU.addUser]: Не задан уровень доступа');
        if(empty($q['lifeTime'])) $q['lifeTime']=10080;

        if(empty(static::$db)) self::$db=new DB();

        $d=static::$db->getOne("SELECT * FROM cu_users WHERE login='{$q['login']}' AND NOT LD");
        if($d!==0) return static::putMsg(false,'[CU.addUser]: Дубликат логина');

        $q['token']=Tools::randString(32);

        if(!static::$db->insert('cu_users', $q))
            return static::putMsg(false, '[CU.addUser] Ошибка записи БД');

        return true;

    }

    /*
     * авторизация должна быть
     */
    public static function deleteUser($userId)
    {
        if(!static::isLogged()) return static::putMsg(false, '[CU.deleteUser] Не авторизован');

        $userId=(int)$userId;

        if(!$userId) return static::putMsg(false, '[CU.deleteUser]: Нет пользователя с ID='.$userId);

        if($userId==static::$userId) return static::putMsg(false, '[CU.deleteUser]: Нельзя удалить себя');

        if(empty(static::$db)) self::$db=new DB();

        static::$db->query("DELETE FROM cu_sessions WHERE userId=$userId");
        $d=static::$db->getOne("SELECT * FROM cu_users WHERE NOT LD AND userId=$userId", MYSQL_ASSOC);

        if(@$d['roleId']<static::$roleId)
            return static::putMsg(false, '[CU.deleteUser]: Можно удалить пользователя с уровнем доступа не меньше чем у вас');

        static::$db->ld("cu_users",'userId',$userId);
        return true;
    }

    /*
     * inline - однопараметровый режим count($r)==1 must be
     * добавлять могут только авторизованные пользователи
     */
    public static function modUser($userId, $r=array(), $inline=false)
    {

        if(!static::isLogged()) return static::putMsg(false, '[CU.modUser] Не авторизован');

        if(empty($userId))
            if($inline) return 'ОШИБКА! Не передан ID пользователя';
            else return static::putMsg(false,'[CU.modUser]: Не передан ID пользователя');

        $userId=(int)$userId;

        if(empty(static::$db)) self::$db=new DB();

        $d=static::$db->getOne("SELECT * FROM cu_users WHERE NOT LD AND userId=$userId", MYSQL_ASSOC);

        if(@$d['roleId']<static::$roleId)
            if($inline) return "ОШИБКА! Нельзя изменить";
            else
                static::putMsg(false, '[CU.deleteUser]: Можно изменять пользователя с уровнем доступа не меньше чем у вас');

        if(static::$fres){
            $q=array();

            foreach($r as $k=>$v){

                // имена полей в $r должны совпадать с полями БД
                switch ($k){

                    case 'roleId':
                        $result=$q['roleId']=(int)$v;
                        if($v <= 0) static::putMsg(false, '[CU.modUser]: Уровень доступа не ясен');
                        break;

                    case 'os':
                        $q['os']=(int)$v;
                        $result=$v?'разрешено':'запрещено';
                        break;

                    case 'login':
                        if(!preg_match("~^[a-zA-Z0-9_-]+$~iu", $v))
                            static::putMsg(false,'[CU.modUser]: недопустимые символы в логине');

                        $d=static::$db->getOne("SELECT count(*) FROM cu_users WHERE login='{$v}' AND userId!=$userId AND NOT LD");
                        if($d[0]) {
                            static::putMsg(false,'[CU.addUser]: Дубликат логина');
                        }

                        $result=$q['login']=$v;
                        break;

                    case 'firstName':
                        $result=$q['firstName']=trim(Tools::esc(Tools::stripTags($v)));
                        break;

                    case 'lastName':
                        $result=$q['lastName']=trim(Tools::esc(Tools::stripTags($v)));
                        break;

                    case 'cmsStartUrl':
                        $result=$q['cmsStartUrl']=Tools::esc(Tools::stripTags($v));
                        break;

                    case 'email':
                        if(!empty($v) && !Tools::emailValid($v))
                            static::putMsg(false,'[CU.modUser]: недопустимые символы в email');

                        $result=$q['email']=Tools::esc($v);
                        break;

                    case 'icq':
                        if(!preg_match("~^[0-9\-]*$~iu", $v))
                            return static::putMsg(false,'[CU.modUser]: недопустимые символы в ICQ UIN');
                        $result=$q['icq']=$v;
                        break;

                    case 'skype':
                        if(!preg_match("~^[a-zA-Z0-9_-]*$~iu", $v))
                            static::putMsg(false,'[CU.modUser]: недопустимые символы в SKYPE');
                        $result=$q['skype']=$v;
                        break;

                    case 'lifeTime':
                        if($v<=0)  static::putMsg(false,'[CU.modUser]: Время жизни сессии должно быть больше нуля');
                        $result= $q['lifeTime']=(int)$v;
                        break;

                    case 'pw':
                        $v=trim(Tools::stripTags($v));
                        if(empty($v)) static::putMsg(false,'[CU.modUser]: Пароль не может быть пустым');
                        $result=$q['pw']=md5($v);
                        if(!empty($r['resetToken'])){
                            $q['token']=Tools::randString(32);
                            $mustDelSessions=true;
                        }
                        break;

                }
            }


            if(!count($q))
                if($inline) return 'ОШИБКА! Нету параметра';
                else return static::putMsg(false,'[CU.modUser]: Не передан ни один параметр');
        }

        if(!$inline && !static::$fres) return false;

        if($inline && !static::$fres){

            $d=static::$db->getOne("SELECT* FROM cu_users WHERE NOT LD AND userId=$userId", MYSQL_ASSOC);

            if($d!==0){
                // берем первое значение из $q и возвращаемся
                list($k)=each($q);
                return Tools::unesc($d[$k]);

            } else return "ОШИБКА! Нет юзера ID=$userId";
        }

        static::$db->update('cu_users', $q, "userId=$userId");

        if(!empty($mustDelSessions)){
            // уничтожаем сессии
            static::$db->del('cu_sessions', 'userId', $userId);
        }

        if($inline) {
            return $result;
        }else return true;


    }


    public static function logoutCurrentSession()
    {
        if(empty(static::$db)) self::$db=new DB();

        if(!static::$userId) return;

        static::$db->query("DELETE FROM cu_sessions WHERE sid='".static::$SID."'");

    }

    public static function logoutAllSessionsByUser($userId)
    {
        $userId=(int)$userId;

        if(!static::isLogged()) return static::putMsg(false, '[CU.logoutAllSessionsByUser] Не авторизован');

        if(empty($userId)) return static::putMsg(false,'[CU.logoutAllSessionsByUser]: Не передан ID пользователя');

        if(empty(static::$db)) self::$db=new DB();

        $d=static::$db->getOne("SELECT * FROM cu_users WHERE NOT LD AND userId=$userId", MYSQL_ASSOC);

        if(@$d['roleId'] < static::$roleId) return static::putMsg(false, '[CU.logoutAllSessionsByUser]: Уровень доступа не достаточен для проведения операции.');

        static::$db->query("DELETE FROM cu_sessions WHERE userId='$userId'");
        return true;
    }

    /*
     * меняет токен в сессиях и в таблице пользователей. Логаут не происходит
     */
    public static function resetToken($userId)
    {
        $userId=(int)$userId;

        if(!static::isLogged()) return static::putMsg(false, '[CU.resetToken] Не авторизован');

        if(empty($userId)) return static::putMsg(false,'[CU.resetToken]: Не передан ID пользователя');

        if(empty(static::$db)) self::$db=new DB();

        $d=static::$db->getOne("SELECT * FROM cu_users WHERE NOT LD AND userId=$userId", MYSQL_ASSOC);

        if(@$d['roleId'] < static::$roleId) return static::putMsg(false, '[CU.resetToken]: Уровень доступа не достаточен для проведения операции.');

        $newToken=Tools::randString(32);

        static::$db->query("UPDATE cu_sessions SET token='$newToken' WHERE userId='$userId'");
        static::$db->query("UPDATE cu_users SET token='$newToken' WHERE userId='$userId'");
        return true;
    }

    public static function accessSwitch($userId)
    {
        $userId=(int)$userId;

        if(!static::isLogged()) return static::putMsg(false, '[CU.accessSwitch] Не авторизован');

        if(empty($userId)) return static::putMsg(false,'[CU.accessSwitch]: Не передан ID пользователя');

        if($userId==static::$userId) return static::putMsg(false, '[CU.accessSwitch]: Нельзя выключить себя');

        if(empty(static::$db)) self::$db=new DB();

        $d=static::$db->getOne("SELECT * FROM cu_users WHERE NOT LD AND userId=$userId", MYSQL_ASSOC);

        if(@$d['roleId'] < static::$roleId) return static::putMsg(false, '[CU.accessSwitch]: Уровень доступа не достаточен для проведения операции.');

        if($d['disabled']) {
            static::$db->update('cu_users',array('disabled'=>0),"userId=$userId");
            return 0;
        }else{
            static::$db->update('cu_users',array('disabled'=>1),"userId=$userId");
            return 1;
        }
    }

    public static function setSDataVar($key, $val)
    {

    }

    public static function setUDataVar($key, $val)
    {

    }

    /*
     * выдергивает значения из куки SSetVars, мерджит их sdata, записывает в БД сессии, удаляет куку SsetVars
     * вызываться должн после login() или isLogged()
     */
    public static function mergeSDataFromCookie()
    {
        if(empty(static::$SID)) return false;

        $coo=@$_COOKIE['SSetVars'];
        if(!empty($coo)) {
            $coo=Tools::DB_unserialize($coo);
            if(is_array($coo)){
                foreach($coo as $k=>$v){
                    static::$sdata[$k]=$v;
                }
                $d=Tools::DB_serialize(static::$sdata);
                static::$db->query("UPDATE cu_sessions SET data='$d' WHERE sid='".static::$SID."'");
                Tools::delCookie('SSetVars');
            }
        }
        return true;
    }

    /*
     * вызываться должн после login() или isLogged()
     */
    public static function mergeUDataFromCookie()
    {
        if(empty(static::$userId)) return false;

        $coo=@$_COOKIE['USetVars'];
        if(!empty($coo)) {
            $coo=Tools::DB_unserialize($coo);
            if(is_array($coo)){
                
                if(!is_array(static::$udata)){
                    $d=static::$db->getOne("SELECT data FROM cu_users WHERE userId='".static::$userId."'");
                    if($d!==0 && !empty($d[0]))
                        static::$udata=Tools::DB_unserialize($d[0]);
                    else
                        static::$udata=array();
                }
                
                foreach($coo as $k=>$v){
                    static::$udata[$k]=$v;
                }
                $d=Tools::DB_serialize(static::$udata);
                static::$db->query("UPDATE cu_users SET data='$d' WHERE userId='".static::$userId."'");
                Tools::delCookie('USetVars');
            }
        }
        return true;
    }

    /*
     * в отличии от SData, UData не загружаются каждый раз при isLogged()
     * для заполенения массива udata вызываем эту фу
     * вернет false если не авторизован
     */
    public static function loadUData()
    {
        if(!static::isLogged()) return false;
        if(!is_array(static::$udata)){
            $d=static::$db->getOne("SELECT data FROM cu_users WHERE userId='".static::$userId."'");
            if($d!==0 && !empty($d[0])){
                static::$udata=Tools::DB_unserialize($d[0]);
            }else
                static::$udata=array();
        }
        return static::$udata;
    }

}