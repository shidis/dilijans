<?php
//JSmart Configuration File

//Show error messages if any error occurs (true or false)
define('JSMART_DEBUG_ENABLED', false);

//Encoding of your js and css files. (utf-8 or iso-8859-1 or cp1251) 
define('JSMART_CHARSET', 'utf-8');

//Base dir for javascript files
define('JSMART_JS_DIR', '../../');

//Base dir for css files
define('JSMART_CSS_DIR', '../../');

//Change it to false only for debugging purposes
define('JSMART_CACHE_ENABLED', true);

//JSmart cache dir
define('JSMART_CACHE_DIR', '../../assets/cache/jsmart/');


define('true_enter',1);
define('ONLY_PATH_INIT',1);

require_once $_SERVER['DOCUMENT_ROOT'].'/config/init.php';
require_once Cfg::$config['root_path'].'/classes/Tools.php';
require_once Cfg::$config['root_path'].'/classes/Core.php';
require_once Cfg::$config['root_path'].'/classes/Stat.php';
require_once Cfg::$config['root_path'].'/classes/Common.php';
require_once Cfg::$config['root_path'].'/classes/DB.php';
require_once Cfg::$config['root_path'].'/classes/BotLog.php';

BotLog::detect();