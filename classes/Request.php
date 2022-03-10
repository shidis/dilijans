<?

class Request
{

    static public $method;
    static public $ajax;
    static public $ajaxMethod='json';

    public static function checkMethod($checkAJAXHeader=true)
    {
        if (@$_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
            static::$ajax = true;
        else
            static::$ajax=false;

        if($_SERVER['REQUEST_METHOD'] == 'GET') static::$method='GET';
        elseif($_SERVER['REQUEST_METHOD'] == 'POST') static::$method='POST';
        elseif($_SERVER['REQUEST_METHOD'] == 'PUT') static::$method='PUT';

        if($checkAJAXHeader && static::$ajax && @$_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
            die('[Request()]: not XMLHttpRequest');
        }

    }

    public static function ajaxMethod($method='')
    {
        if(empty($method)) return static::$ajaxMethod; else static::$ajaxMethod=$method;
    }

	public static function checkRedirect()
    {
		$f=@fopen (Cfg::_get('root_path').'/'.Cfg::get('res_dir').'/redirect.csv','r');
		if($f) {
			while (($d = fgetcsv($f, 1000, ";")) !== FALSE )if(trim(@$d[0])!='' && trim(@$d[1])!='' && mb_strpos($d[0],'/*')===false){ 
				$s=str_ireplace('http://','',trim($d[0]));
				$s=str_ireplace('https://','',trim($d[0]));
				$s=str_ireplace($_SERVER['HTTP_HOST'],'',$s);
				$s=str_ireplace('www.','',$s);
				if($s==$_SERVER['REQUEST_URI']){
					header("HTTP/1.1 301 Moved Permanently");
					header('Location: '.trim($d[1]));
					exit();
				}
			}
			fclose($f);
		}
	}
	
	public static function checkDomain()
    {
		$url=@$_SERVER['REQUEST_URI'];
		if(strcasecmp(Cfg::$config['site_url'],$_SERVER['HTTP_HOST'])!==0) {
			header("HTTP/1.1 301 Moved Permanently");
			if(isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']=='on'))
				header('Location: https://'.Cfg::$config['site_url'].$url);
			else
				header('Location: http://'.Cfg::$config['site_url'].$url);
			exit();
		}
	}

    public static function isHTTPS()
    {
        if(Tools::mb_strcasecmp(@$_SERVER['HTTPS'],'on')==0 || @$_SERVER['HTTPS']==1) return true; else return false;
    }

    
}
