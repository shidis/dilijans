<?
if (!defined('true_enter')) die ("No direct access allowed.");

//ini_set('display_errors','On');
ini_set('memory_limit', '512M');

define('CONSOLE', empty($_SERVER['HTTP_HOST']) ? true : false);


mb_internal_encoding("UTF-8");
setlocale(LC_CTYPE, 'ru_RU.UTF-8', 'rus_RUS.UTF-8', 'Russian_Russia.UTF-8');
setlocale(LC_COLLATE, 'ru_RU.UTF-8', 'rus_RUS.UTF-8', 'Russian_Russia.UTF-8');

$dn = dirname(__FILE__);
include_once $dn . '/../config/namespaces.php';
// получаем server_loc, site_name, site_url
Namespaces::getNamespace();

include_once $dn . '/../classes/CfgBase.php';
include_once $dn . '/global.cfg.php';
CfgGlobal::init();
include_once $dn . '/' . Namespaces::getNS() . '.cfg.php';
CfgGlobal::$config['site_name'] = Namespaces::getSiteName();
CfgGlobal::$config['site_url'] = Namespaces::getSiteUrl();
Cfg::configOverrides();

if (!defined('ONLY_PATH_INIT'))
{

    include_once(Cfg::$config['root_path'] . '/classes/Core.php');

    spl_autoload_register(['Core', 'autoload']);

    Stat::init();

    Session::init();

    Log_Tables::initShutdown();
}
