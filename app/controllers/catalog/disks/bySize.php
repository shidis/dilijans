<?
class App_Catalog_Disks_bySize_Controller extends App_Catalog_Disks_Common_Controller {

    public function index()
    {
        $this->view('catalog/disks/bySize');
        $this->_title=$this->title="Подбор дисков по размеру";


        $this->_sidebar();
    }

}