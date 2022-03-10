<?
class App_Compare_Tyres_Controller extends App_Catalog_Tyres_Common_Controller {

    public function index()
    {
        if(Request::$ajax) {
            if(empty($this->cmpData['t'])){
                $this->r['html']='Список сравнения пуст';
                return;
            }
        }
        else {
            $this->view('compare/tyres');

            $this->breadcrumbs['сравнение шин']='';
            $this->title=$this->_title="Сравнение выбранных шин";

            if(empty($this->cmpData['t'])) return;
        }

        $num=$this->cc->cat(array(
            'gr'=>1,
            'cat_id'=>array('list'=>$this->cmpData['t'])
        ));

        if(!$num)
            if(Request::$ajax) return $this->putMsg(false,'Список сравнения пуст (2)');
            else return App_Route::redir404();

        $d=$this->cc->fetchAll('',MYSQL_ASSOC);

        if(Request::$ajax){
            $this->r['html']='<ol>';
            foreach($d as $v){
                $this->r['html'].='<li><a href="#" class="delete" cid="'.$v['cat_id'].'" title="исключить из списка"></a><a href="/'.App_Route::_getUrl('tTipo').'/'.$v['cat_sname'].'.html">'.Tools::unesc('<b>'.$v['bname'].' '.$v['mname']."</b> {$v['P3']}/{$v['P2']} R{$v['P1']}".($v['csuffix']!=''?" {$v['csuffix']}":'')).'</a></li>';
            }

            $this->r['html'].='</ol><a class="button" href="/'.App_Route::_getUrl('compare').'/tyres.html">перейти к сравнению<span></span></a>';

        } else {

            $this->noSidebar=true;

            $burl='/'.App_Route::_getUrl('tTipo').'/';

            $this->cat=array();
            foreach($d as $v){
                $this->cat[]=$this->catRow($v,$burl);
            }

        }


    }


}