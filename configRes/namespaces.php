<?
if (!defined('true_enter')) die ("No direct access allowed.");

include_once dirname(__FILE__) . '/../classes/NamespacesBase.php';

class Namespaces extends NamespacesBase
{

    /*
     * aliases: пары [HTTP_HOST, redirectUrl(==site_url)]
     * Для каждого server_loc ДОЛЖНЫ быть секции в $names иначе fatal error
     * $defaut применяется если не определились: HTTP_HOST || server_loc
     */
    static public $names=array(
        'app'=>[
            'local'=>[
                'name'=> 'dilijans.org::local',
                'aliases'=> [
                    ['dl.s', 'dl.s'],
                ]
            ],
            'test'=>[
                'name'=> 'dilijans.org::test',
                'aliases'=> [
                    ['test.dilijans.org', 'test.dilijans.org'],
                ]
            ],
            'remote'=>[
                'name'=> 'dilijans.org',
                'aliases'=> [
                    ['dilijans.org', 'www.dilijans.org'],
                ]
            ]
        ]
    ), $default = [
        // если server_loc или HTTP_HOST не определены применятся эти параметры
        'name' => 'dilijans.org',
        'ns' => 'app',
    ];


    public static function getServerLocation()
    {
        //местоположение сервера
        if (!defined('server_loc')) {
            if (@$_SERVER['SERVER_ADDR'] == '127.0.0.1' || isset($_SERVER['APPDATA']))
            {
                define('server_loc', 'local');
            }
            elseif (is_file(realpath(dirname(__FILE__) . '/../../dl_test.server_loc')))
            {
                define('server_loc', 'test');
            }
            else
            {
                define('server_loc', 'remote');
            }
        }

        if (server_loc == 'local' || server_loc == 'test')
        {
            error_reporting(E_ALL);
            ini_set('error_reporting', E_ALL);
        }
        else
        {
            error_reporting(0);
        }

        return server_loc;
    }

}