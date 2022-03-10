<?
if (!defined('true_enter')) die ("No direct access allowed.");

abstract class Controller extends Common {

	public $_data=array();
	
	public  function & __get($key){
		if (isset($this->_data[$key])) {
			return $this->_data[$key];
		}else {
			$r='';
			return $r;
		}

	}
 
 	public  function __set($key,$value) {
		$this->set($key, $value);
	}
	
	public function set($key,$value){
		$this->_data[$key]=$value;
	}
	
	public function __unset($key){
		unset($this->_data[$key]);
	}

	public function __isset($key){
        if (isset($this->_data[$key])) {
            return (false === empty($this->_data[$key]));
        } else {
            return null;
        }
    }	

	public function JSPush($js){
		$_js=$this->JS;
		if(is_array($js)) foreach($js as $v)	$_js[]=$v;
		else $_js[]=$js;
		$this->JS=$_js;
	}

	public function CSSPush($css){
		$_css=$this->CSS;
		if(is_array($css)) foreach($css as $v)	$_css[]=$v;
		else $_css[]=$css;
		$this->CSS=$_css;
	}
	
	public function getJS()
	{
		$r=array();
        ExLib::loadJSId();
		if(!empty(ExLib::$cJS)) $cjs='?'.ExLib::$cJS; else $cjs='';
		if(is_array($this->JS)) 
			foreach($this->JS as $v) $r[]=str_replace('//','/',"<script type=\"text/javascript\" src=\"/$v$cjs\"></script>\r\n");
/*		if(is_file('app/js/'.@$this->_view.'.js'))
			$r[]="<script type=\"text/javascript\" src=\"/app/js/{$this->_view}.js$cjs\"></script>\r\n";
*/
		return implode('',$r);
	}

	public function getCSS()
	{
		$r=array();
        ExLib::loadCSSId();
		if(!empty(ExLib::$cCSS)) $ccss='?'.ExLib::$cCSS; else $ccss='';
		if(is_array($this->CSS)) 
			foreach($this->CSS as $v) $r[]=str_replace('//','/',"<link rel=\"stylesheet\" type=\"text/css\" href=\"/$v$ccss\">\r\n");
/*
		if(is_file('app/css/'.@$this->_view.'.css')) 
			$r[]="<link rel=\"stylesheet\" type=\"text/css\" href=\"/app/css/{$this->_view}.css$ccss\">\r\n";
*/
		return implode('',$r);
	}
	
	// view  для функции App::output()
	public function template($template=''){
		if($template=='') return $this->_template; else $this->_template=$template;
	}
	
	// view  для текущего контроллера. Не передается в App, может использоваться только в шаблоне
	public function view($view){
		if($view=='') return $this->_view; else $this->_view=$view;
	}



}