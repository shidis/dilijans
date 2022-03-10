<?
class App_Search_Controller extends App_Common_Controller {


    public function index()
    {
        $this->search=new App_Search(array('q'=>@Url::$sq['q']));
        $res=$this->search->go();

        $this->view('page/index');

        $this->ss=new Content($this);

        if($res===false) {
            $this->content=$this->ss->parseText($this->search->fres_msg);
        }else{
            $this->_where=$this->search->_where;
            $this->_whereCat=$this->search->_whereCat;
            $ctrl=$this->search->fres_msg;
            $this->gr=$this->search->gr;
            switch($ctrl){
                case 'catalog/tyres/model':
                    $this->model_id=$this->search->model_id;
                    return 'catalog/tyres/model/index';
                    break;
                case 'catalog/tyres/tipo':
                    $this->cat_id=$this->search->cat_id;
                    return 'catalog/tyres/tipo/index';
                    break;
                case 'catalog/tyres/qsearch':
                    return 'catalog/tyres/search/qsearch';
                    break;
                case 'catalog/disks/model':
                    $this->model_id=$this->search->model_id;
                    return 'catalog/disks/model/index';
                    break;
                case 'catalog/disks/tipo':
                    $this->cat_id=$this->search->cat_id;
                    return 'catalog/disks/tipo/index';
                    break;
                case 'poisk/disks/qsearch':
                    return 'catalog/disks/search/qsearch';
                    break;
                default:
                    $this->makeTitle();
                    $this->content=$this->parse($this->ss->getDoc('noresult_search'));
                    $this->view('ya_search');
            }

        }
    }

    public function axView()
    {
        Request::$ajax = false;
        $this->search=new App_Search(array('q'=>@Url::$sq['q']));
        $res=$this->search->go();

        $this->view('page/index');

        $this->ss=new Content($this);

        if($res===false) {
            $this->content=$this->ss->parseText($this->search->fres_msg);
        }else{
            $this->_where=$this->search->_where;
            $this->_whereCat=$this->search->_whereCat;
            $ctrl=$this->search->fres_msg;
            $this->gr=$this->search->gr;
            switch($ctrl){
                case 'catalog/tyres/tipo':
                    $this->cat_id=$this->search->cat_id;
                    return 'catalog/tyres/tipo/axView';
                    break;
                /*case 'catalog/tyres/qsearch':
                    return 'catalog/tyres/search/qsearch';
                    break;*/
                case 'catalog/disks/tipo':
                    $this->cat_id=$this->search->cat_id;
                    return 'catalog/disks/tipo/axView';
                    break;
                /*case 'poisk/disks/qsearch':
                    return 'catalog/disks/search/qsearch';
                    break;*/
            }

        }
    }

    private function makeTitle()
    {
        $this->_title=$this->title="Поиск по сайту: &quot;{$this->search->q}&quot;";
        $this->breadcrumbs=array();
        $this->breadcrumbs['поиск по сайту']='';
    }

    public function stage2() // вызов из шин
    {
        $this->makeTitle();
        // если не найдены шины, то ищем диски
        if(!$this->num)
            if($this->gr==0) return 'catalog/disks/search/qsearch';
            else GA::_event('searchBySite','TyresNoResult',$this->search->q,'',true);
        else GA::_event('searchBySite','Tyres',$this->search->q,$this->num,true);

    }

    public function stage3()  // вызов из дисков
    {
        $this->makeTitle();
        if(!$this->num) {
            $this->view('ya_search');
            $this->fromInternalSearch=1;
            $this->content=$this->parse($this->ss->getDoc('noresult_search'));
            if($this->gr==2) GA::_event('searchBySite','DisksNoResult',$this->search->q,'',true);
            else GA::_event('searchBySite','UnGroupNoResult',$this->search->q,'',true);
        }else GA::_event('searchBySite','Disks',$this->search->q,$this->num,true);
    }

}