<?
class App_Faq_Controller extends App_Common_Controller {


    public function faqLenta() {

        $this->view('faq');

        $this->title=$this->_title='Часто задаваемые вопросы';

        $page=(int)@Url::$sq['page'];
        $limit=7;
        $this->lenta=$this->ss->cntList(array(
            'rIds'=>array(8),
            'fields'=>array('text'),
            'start'=>max(0,$limit*$page-$limit),
            'limit'=>$limit
        ));

        $this->lentaNum=count($this->lenta);
        $this->allItemsUrl='/'.App_Route::_getUrl('faq').'.html';

        $this->paginator=Tools::paginator(Url::$path,Url::$sq,$page,$this->ss->docsNum,$limit,'page',array(
            'active'=>	'<li class="active">{page}</li>',
            'noActive'=>'<li><a href="{url}">{page}</a></li>',
            'dots'=>	'<li>...</li>'
        ),18);
        $s='';
        foreach($this->paginator as $vv) $s.=$vv;
        $this->paginator=$s;

        $this->breadcrumbs[]='Часто задаваемые вопросы';

    }


}