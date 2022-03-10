<?
class App_News_Controller extends App_Common_Controller {


    public function item() {

        $this->view('news/item');

        $this->news=new News();

        if(@Url::$spath[2]=='') return App_Route::redir404();
        $this->news->que('news_by_sname',Url::$spath[2],"published=1",1);
        if(!$this->news->qrow['news_id']) {
            return App_Route::redir404();
        }
        $this->title=$this->_title=Tools::unesc($this->news->qrow['title']);
        $this->content=$this->ss->parseText(Tools::unesc($this->news->qrow['text']));
        $this->img1=$this->news->makeImgPath($this->news->qrow['img1']);
        $this->img2=$this->news->makeImgPath($this->news->qrow['img2']);
        $this->description=Tools::html($this->news->qrow['description']);
        $this->keywords=Tools::html($this->news->qrow['keywords']);
        $this->dateSys=($this->news->qrow['dt']);
        $d=Tools::sdate($this->news->qrow['dt'],'-');
        $this->date=$d;

        $this->allItemsUrl='/'.App_Route::_getUrl('news').'.html';

        $this->news->que('news',1,$this->news->qrow['news_id'],5,"published=1");
        $d=$this->news->fetchAll();
        $this->lenta=array();
        $this->lentaNum=count($d);
        foreach($d as $v){
            $d=Tools::sdate($v['dt'],'-');
            $this->lenta[]=array(
                'url'=>'/'.App_Route::_getUrl('news').'/'.$v['sname'].'.html',
                'title'=>Tools::unesc($v['title']),
                'intro'=>Tools::unesc($v['intro']),
                'img1'=>$this->news->makeImgPath($v['img1']),
                'date'=>$d
            );
        }

        $this->breadcrumbs['Новости']=$this->allItemsUrl;
    }

    public function lenta() {

        $this->view('news/lenta');

        $this->news=new News();

        $this->title=$this->_title='Новости компании';
        $this->news->que('news',1,'','',"published=1");
        $d=$this->news->fetchAll();
        $this->lenta=array();
        $this->lentaNum=count($d);
        foreach($d as $v){
            $d=Tools::sdate($v['dt'],'-');
            $this->lenta[]=array(
                'url'=>'/'.App_Route::_getUrl('news').'/'.$v['sname'].'.html',
                'title'=>Tools::unesc($v['title']),
                'intro'=>Tools::unesc($v['intro']),
                'img1'=>$this->news->makeImgPath($v['img1']),
                'date'=>$d
            );
        }

        $this->breadcrumbs[]='Новости магазина';

    }


}