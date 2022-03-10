<?
class App_YaSearch_Controller extends App_Common_Controller {


    public function index() {

        $this->view('ya_search');
        $this->title=$this->_title='Поиск по сайту';
        $this->breadcrumbs[]='Поиск по сайту';
    }

}