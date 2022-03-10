<?
class App_Calculator_Controller extends App_Common_Controller {


    public function index() {

        $this->view('calculator');
        $this->JSPush('/app/js/calculator.js');
        $this->title=$this->_title='Шинный калькулятор';
        $this->tyreText=$this->parse($this->ss->getDoc('scalc_tyre_text$6'));
        $this->diskText=$this->parse($this->ss->getDoc('scalc_disk_text$6'));
        $this->breadcrumbs['Шинный калькулятор']='';
//проверка
    }

}