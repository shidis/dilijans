<?
class App_Compare_Disks_Controller extends App_Catalog_Disks_Common_Controller {

    public function index()
    {
        if(Request::$ajax) {
            if(empty($this->cmpData['d'])){
                $this->r['html']='Список сравнения пуст';
                return;
            }
        }
        else {
            $this->view('compare/disks');

            $this->breadcrumbs['сравнение дисков']='';
            $this->title=$this->_title="Сравнение выбранных дисков";

            if(empty($this->cmpData['d'])) return;
        }

        $num=$this->cc->cat(array(
            'gr'=>2,
            'cat_id'=>array('list'=>$this->cmpData['d'])
        ));

        if(!$num)
            if(Request::$ajax) return $this->putMsg(false,'Список сравнения пуст (2)');
                else return App_Route::redir404();

        $d=$this->cc->fetchAll('',MYSQLI_ASSOC);

        if(Request::$ajax){
            $this->r['html']='<ol>';
            foreach($d as $v){
                $this->r['html'].='<li><a href="#" class="delete" cid="'.$v['cat_id'].'" title="исключить из списка"></a><a href="/'.App_Route::_getUrl('dTipo').'/'.$v['cat_sname'].'.html">'.Tools::unesc('<b>'.$v['bname'].' '.$v['mname']."</b> {$v['P2']}x{$v['P5']}"." {$v['P4']}/{$v['P6']}"." ET{$v['P1']}".($v['P3']!=0?" DIA{$v['P3']}":'').($v['csuffix']!=''?" {$v['csuffix']}":'')).'</a></li>';
            }

            $this->r['html'].='</ol><a class="button" href="/'.App_Route::_getUrl('compare').'/disks.html">перейти к сравнению<span></span></a>';

        } else {

            $this->noSidebar=true;

            $burl='/'.App_Route::_getUrl('dTipo').'/';

            $this->cat=array();
            foreach($d as $v){
                $this->cat[]=$this->catRow($v,$burl);
            }
        }


    }

}