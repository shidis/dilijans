<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 21.09.2015
 * Time: 20:05
 */
abstract class NamespacesBase
{

    static public $names = [], $default = ['name' => '', 'ns' => ''], $domain = '', $siteName = '', $ns = '';

    public static function getServerLocation()
    {
    }

    public static function getNamespace()
    {
        static::getServerLocation();
        $host = @$_SERVER['HTTP_HOST'];

        if (defined('server_loc') && server_loc != '' && !empty($host))
        {
            foreach (static::$names as $ns => $nsv)
            {
                if (empty($nsv[server_loc]))
                {
                    echo '[Namespaces]: namespace for ' . server_loc . ' not defined';

                    die();
                }
                if (empty($nsv[server_loc]['aliases']))
                {
                    echo '[Namespaces]: aliases for ' . server_loc . ' not defined';

                    die();
                }
                foreach ($nsv[server_loc]['aliases'] as $alias)
                {
                    if ($alias[0] == $host)
                    {
                        static::$siteName = $nsv[server_loc]['name'];
                        static::$domain = $alias[1];
                        return static::$ns = $ns;
                    }
                }
            }
        }else if (defined('server_loc') && server_loc != '')
        {
            static::$ns = static::$default['ns'];
            static::$siteName = static::$names[static::$ns][server_loc]['name'];
        }

        if (empty(static::$ns))
        {
            static::$siteName = static::$default['name'];
            static::$ns = static::$default['ns'];
            static::$domain = $host;
        }

        return static::$ns;

    }

    public static function getSiteUrl()
    {
        return static::$domain;
    }

    public static function getSiteName()
    {
        return static::$siteName;
    }

    public static function getNS()
    {
        return static::$ns;
    }

}
