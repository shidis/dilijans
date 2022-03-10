<?
class App_E404_Controller extends App_Common_Controller {
	
	
	public function index()
    {
        if(Request::$ajax) {
            header("HTTP/1.0 404 Not Found");
            return;
        }

		$this->view('404');
		$this->content=$this->parse($this->ss->getDoc('404$3'));
		$this->_title=$this->title=$this->ss->cnt_title;
		header("HTTP/1.0 404 Not Found");
        GA::_event('Other','e404',ltrim(@$_SERVER['REQUEST_URI'],'/'),'',true);
	}

}

