<?
abstract class Route
{
    public static $action = '';
    public static $controller = '';
    public static $param = array();
    public static $class;
    public static $ns;


    /*
     * этот методы вызывается только один раз в index.php
     */
    public static function _getController()
    {
        static::$class = get_called_class();
        static::$ns = Namespaces::$ns;

        /* Путь контроллера включает в себя путь, имя класса, имя метода. Разделенные слешом  */
        $controllerName = '';

        static::$action = static::$controller = '';

        if (Url::$spath == '') {
            $controllerName = call_user_func(array(static::$class, '_index'));
            static::$action = '_index';
        }
        if ($controllerName == '')
            foreach (static::$actions as $k => &$v)
                if (Url::$spath[1] == $v['url']) { // эта проверка не подразумевает наличие / в $action[url]. TODO  если нужны вложенные урл в $action[url]  - сюда регулярник вставить надо
                    $controllerName = call_user_func(array(static::$class, $k));
                    static::$action = $k;
                }

        if ($controllerName == '' && $controllerName !== false) {
            $controllerName = call_user_func(array(static::$class, '_default'));
            static::$action = '_default';
        }

        if (!Url::checkTrailingSlash()) return static::$controller = static::redir404();

        // контроль вложенности урл
        if (isset(static::$actions[static::$action]['spathLength']) && count(Url::$spath) > static::$actions[static::$action]['spathLength'])
            static::$controller = static::redir404();
        elseif ($controllerName != '' && $controllerName !== false) static::$controller = $controllerName;

        // контроль кол-ва GET параметров без проверки их содержимого
        if (isset(static::$actions[static::$action]['noGET']) && count(Url::$sq) && static::$actions[static::$action]['noGET'])
            static::$controller = static::redir404();

        /*
         * путь к контроллеру внутри path[0]
         */
        return static::$controller;

    }

    static function _getUrl($action)
    {
        return @static::$actions[$action]['url'];
    }

    static function _getAction()
    {
        return static::$action;
    }

    static function _getCurURL()
    {
        return $_SERVER['REQUEST_URI'];
    }

    /* главная страница */
    static function _index()
    {
        return 'home/index';
    }

    /* если страница первого уровня вложенности и для не определен action, то вызывается этот метод  */
    static function _default()
    {
        if (count(Url::$spath) == 1) return 'page/index';
        else  return static::redir404();
    }

    static function redir404()
    {
        return 'e404/index';
    }

}