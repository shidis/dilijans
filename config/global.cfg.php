<?
if (!defined('true_enter')) die ("No direct access allowed.");

/*
 * в CfgGlobal обязательные дефиниции следующих парметров:
 * root_path
 *
 */

// константы для модуля Sys_Log
define ('SLOG_ESTOP', 4); // остановка очереди запуска робота
define ('SLOG_ETERM', 3); // остановка текущей сессии робота
define ('SLOG_ERROR', 2); // робот и сессия продолжат работу
define ('SLOG_INFO', 1);


class CfgGlobal extends CfgBase
{

    public static $config = array(

        'remote_sql_host'=>'localhost',
        'remote_sql_db'=>'dilijans',
        'remote_sql_user'=>'dilijans',
        'remote_sql_pass'=>'tilidili',

        'test_sql_host'=>'localhost',
        'test_sql_db'=>'dilijans_test',
        'test_sql_user'=>'dilijans_test',
        'test_sql_pass'=>'ZHIyewmx4xOzwqsmKKXB',

        'local_sql_host'=>'localhost',
        'local_sql_db'=>'dilijans_new',
        'local_sql_user'=>'root',
        'local_sql_pass'=>'1',

        'remote_memcached_host'=>'localhost',
        'remote_memcached_port'=>11211,
        'test_memcached_host'=>'localhost',
        'test_memcached_port'=>11211,
        'local_memcached_host'=>'localhost',
        'local_memcached_port'=>11211,

// res dir
        'res_dir' => 'assets/res',

        'spacer_path' => 'images/spacer.gif',
        'icq_images_dir' => '/inc/icq',

// upload contents dir
        'cnt_upload_dir' => 'cnt_images',

        'cache_dir' => 'assets/cache',

// максимальный размер файла для POST
        'max_file_size' => '30000000',

        'php_eval_enabled' => true,

// скрипрты php подключаемые через eval
        'snippets_dir' => 'assets/snippets',

        'page_break_code' => array(
            "/<p><!-- pagebreak --><\/p>/iu",
            "/<!-- pagebreak -->/iu",
            "/<div style=\"page-break-after.+?<\/div>/"
        ),

        'logDir' => 'log',

        'supportEmail' => 'pavel@qmark.ru',

        // Галлерея изображений (класс Gallery)
        'GL_DIR' => 'assets/galleries',


        'sessionsSaveDir' => 'assets/cache/sess',
        'sessionCookieLifeTime' => 259200, // 3 days

        'botLog' => 1,

        'MemCache'=>[
            'freeMemAlert'=>10 // алерт при достижении свободной памяти  МС этого порога (%)
        ],

        'TB_obfuscate' => 'ladfmoi0-amdt9-v-89tuy8953',


        'orderPrefix'=>''



    );

    static function init()
    {
        if (defined('ROOT_PATH')) {
            CfgGlobal::$config['root_path'] = ROOT_PATH;
        }
        elseif (!empty($_SERVER['DOCUMENT_ROOT']))
        {
            CfgGlobal::$config['root_path'] = $_SERVER['DOCUMENT_ROOT'];
        }
        else
        {
            CfgGlobal::$config['root_path'] = realpath(dirname(__FILE__) . '/..');
        }

        CfgGlobal::$config['sql_host'] = CfgGlobal::$config[server_loc . '_sql_host'];
        CfgGlobal::$config['sql_user'] = CfgGlobal::$config[server_loc . '_sql_user'];
        CfgGlobal::$config['sql_pass'] = CfgGlobal::$config[server_loc . '_sql_pass'];
        CfgGlobal::$config['sql_db'] = CfgGlobal::$config[server_loc . '_sql_db'];

        CfgGlobal::$config['memcached_host'] = CfgGlobal::$config[server_loc . '_memcached_host'];
        CfgGlobal::$config['memcached_port'] = CfgGlobal::$config[server_loc . '_memcached_port'];

        if(!empty(CfgGlobal::$config[server_loc . '_serverUdatesLogFile'])) {
            CfgGlobal::$config['serverUdatesLogFile'] = CfgGlobal::$config[server_loc . '_serverUdatesLogFile'];
        }
    }
}

