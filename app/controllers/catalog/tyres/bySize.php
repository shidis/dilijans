<?
class App_Catalog_Tyres_bySize_Controller extends App_Catalog_Tyres_Common_Controller
{

    public function index()
    {
        $this->view('catalog/tyres/bySize');
        $this->_title=$this->title="Подбор шин по размеру";


        $this->_sidebar();
    }

}