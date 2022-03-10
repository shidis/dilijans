<?
class DJS {

    static $js=[], $jsw=[], $kn, $md5, $userId;

    /*
 * помещает data внутрь элемента selector
 */
    static function putJSD($selector,$data)
    {
        static::$js[$selector]=base64_encode($data);
    }

    /*
 * оборачивает тегом data элемент selector
 */
    static function putJSDW($selector,$data)
    {
        static::$jsw[$selector]=base64_encode($data);
    }

    /*
     * упаковывает массивы js jsw в MC
     *
     */
    private static function pack()
    {
        $i=MC::checkMem();
        $pid=@Cfg::$config['DJS']['pid'];
        $ttl=@Cfg::$config['DJS']['ttl'];
        if ($i===false || @$i['status']=='alert' || empty($pid) || empty($ttl) || empty($_SERVER['HTTP_HOST'])) return false;
        static::$md5=md5($_SERVER['HTTP_HOST'].@$_SERVER['REQUEST_URI']);
        static::$kn="DJS:$pid:".static::$md5;

        if(static::getUserId()!==false) static::$kn.=":".static::$userId;

        return MC::set(static::$kn, gzdeflate(json_encode(['JSD'=>static::$js, 'JSDW'=>static::$jsw, 'TS'=>time(), 'URI'=>@$_SERVER['REQUEST_URI']]), 9), $ttl);
    }

    /*
     * вызывается перед рендерингом страницы html
     * внутри вызывается pack
     */
    static function getScript($protocol='')
    {
        $s='';
        if(!static::pack()) {
            if (!empty(DJS::$js)) {
                $s.=';var JSD='.json_encode(static::$js).';';
            }
            if (!empty(DJS::$jsw)) {
                $s.=';var JSDW='.json_encode(static::$jsw).';';
            }
        }else{
            if(empty($protocol)) {
                if(isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']=='on')) $p='https://'; else $p='http://';
            }  else $p="$protocol://";

            if(static::getUserId()!==false)
                $s='<script type="text/javascript" src="'.$p.Namespaces::$names['react'][server_loc]['domain'].'/dyn/'.static::$md5.'/'.static::$userId.'.js"></script>';
            else
                $s='<script type="text/javascript" src="'.$p.Namespaces::$names['react'][server_loc]['domain'].'/dyn/'.static::$md5.'.js"></script>';
        }
        return $s;
    }

    /*
     * вызывается из контроллера react
     * вытаскивает и подготаливает кеш JS для вывода
     */
    static function makeScript($md5,$userId)
    {
        if(MC::chk()){
            $pid=@Cfg::$config['DJS']['pid'];
            $ttl=@Cfg::$config['DJS']['ttl'];
            if (empty($pid) || empty($ttl)) return 'MCCfgErr;';
            $k="DJS:$pid:$md5".(!empty($userId)?":$userId":'');
            $rl_k="DJS:$pid:rl:$md5".(!empty($userId)?":$userId":''); // метка перезагрузки страницы
            $s=MC::get($k);

            if(!empty($s)){
                $d=(object)['ttl'=>$ttl, 'data'=>(object)[]];
                $d->data=json_decode(gzinflate($s));
                if(MC::get($rl_k)){
                    static::logit('+AfterReload+');
                    MC::del($rl_k);

                    $s=(int)MC::get("DJS:$pid:js_afterReload");
                    if(empty($s)) $s=1; else $s++;
                    MC::set("DJS:$pid:js_afterReload", $s);
                }
                return $d;
            }else {
                // проверяем есть ли метка о перезагрузке. если нет, пишем в МС метку что будет перезагрузка страницы
                // иначе ничего не делаем, предполагая что уже была перезагрузка
                if(!MC::get($rl_k)){
                    // то reload page
                    $s=(int)MC::get("DJS:$pid:js_misses");
                    if(empty($s)) $s=1; else $s++;
                    MC::set("DJS:$pid:js_misses", $s);

                    static::logit('MC misses++');
                    $d=(object)['data'=>(object)[]];
                    $d->data->reloadPage=true;
                    MC::set($rl_k,time(), 60); // метка действительна 60 секунд
                    return $d;
                }else{

                    $s=(int)MC::get("DJS:$pid:js_reloadMisses");
                    if(empty($s)) $s=1; else $s++;
                    MC::set("DJS:$pid:js_reloadMisses", $s);

                    static::logit('+ReloadMisses+');
                    MC::del($rl_k);
                    return ';';
                }
            }
        } else return ';;';
    }

    private static function logit($msg)
    {
        file_put_contents(Cfg::$config['root_path'].'/assets/logs/react-dyn.log', Tools::dt()." - ".$_SERVER['REMOTE_ADDR'].' - '.@$_SERVER['HTTP_USER_AGENT'].' - '.@$_SERVER['HTTP_REFERER'].' - '.@$_SERVER['REQUEST_URI'].' - '.$msg."\n\n", FILE_APPEND );
    }

    private static function getUserId()
    {
        $sVar=@Cfg::$config['DJS']['sVar'];
        $userIdVar=@Cfg::$config['DJS']['userIdVar'];
        if(!empty($sVar) && !empty($userIdVar)) {

            // если есть это:
            if (isset($_COOKIE[$sVar])) $sv = $_COOKIE[$sVar]; elseif (isset($_REQUEST[$sVar])) $sv = $_REQUEST[$sVar];

            // то прибавляем к ключу МС это:
            $userId = @$_COOKIE[$userIdVar];
            if (empty($userId)) $userId = @$_REQUEST[$userIdVar];

            if (isset($sv) && !empty($userId) && preg_match("~[0-9a-z]{16,}~iu", $userId)) return static::$userId=$userId;
        }
        return false;
    }

}
