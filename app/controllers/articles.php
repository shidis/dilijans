<?

class App_Articles_Controller extends App_Common_Controller
{


    public function articlesItem()
    {

        $this->view('articles/item');

        if (@Url::$spath[2] == '') return App_Route::redir404();
        $this->content = $this->parse($this->ss->getDoc(Url::$spath[2] . '$1,2,7'));
        $this->img1 = $this->ss->img1;
        $this->img2 = $this->ss->img2;
        $this->title = $this->_title = $this->ss->cnt_title;
        $this->description = $this->ss->meta['description'];
        $this->keywords = $this->ss->meta['keywords'];
        $this->allItemsUrl = '/' . App_Route::_getUrl('articles') . '.html';
        if (!$this->ss->cnt_id) {
            return App_Route::redir404();
        }
        $this->lenta = $this->ss->cntList(array(
            'rIds' => $this->ss->cnt_type_id,
            'start' => 0,
            'limit' => 4,
            'fields' => array('intro'),
            'exDocIds' => $this->ss->cnt_id
        ));
        $this->lentaNum = count($this->lenta);
        foreach ($this->lenta as &$v) $v['url'] = '/' . App_Route::_getUrl('articles') . '/' . $v['sname'] . '.html';

        $this->breadcrumbs['Полезные статьи'] = $this->allItemsUrl;

    }


    public function articlesLenta()
    {

        $this->view('articles/lenta');

        $this->title = $this->_title = 'Полезные статьи';

        $page = (int)@Url::$sq['page'];
        $limit = 7;
        $this->lenta = $this->ss->cntList(array(
            'rIds' => array(1, 2, 7),
            'fields' => array('intro'),
            'start' => max(0, $limit * $page - $limit),
            'limit' => $limit
        ));
        $this->lentaNum = count($this->lenta);
        $this->allItemsUrl = '/' . App_Route::_getUrl('articles') . '.html';
        foreach ($this->lenta as &$v) $v['url'] = '/' . App_Route::_getUrl('articles') . '/' . $v['sname'] . '.html';

        $this->paginator = Tools::paginator(Url::$path, Url::$sq, $page, $this->ss->docsNum, $limit, 'page', array(
            'active' => '<li class="active">{page}</li>',
            'noActive' => '<li><a href="{url}">{page}</a></li>',
            'dots' => '<li>...</li>'
        ), 5);
        $s = '';
        foreach ($this->paginator as $vv) $s .= $vv;
        $this->paginator = $s;

        $this->breadcrumbs[] = 'Полезные статьи';

    }

    public function novinkiLenta()
    {

        $this->view('articles/novinkiLenta');

        $this->title = $this->_title = 'Новинки сезона';

        $this->lenta = $this->ss->cntList(array(
            'rIds' => 18,
            'fields' => array('intro'),
        ));
        $this->allItemsUrl = '/' . App_Route::_getUrl('novinkiSezona') . '.html';
        foreach ($this->lenta as &$v) $v['url'] = '/' . App_Route::_getUrl('novinkiSezona') . '/' . $v['sname'] . '.html';

        $this->breadcrumbs[] = 'Новинки сезона';

    }

    public function novinkiItem()
    {

        $this->view('articles/item');

        if (@Url::$spath[2] == '') return App_Route::redir404();
        $this->content = $this->parse($this->ss->getDoc(Url::$spath[2] . '$18'));
        $this->img1 = $this->ss->img1;
        $this->img2 = $this->ss->img2;
        $this->title = $this->_title = $this->ss->cnt_title;
        $this->description = $this->ss->meta['description'];
        $this->keywords = $this->ss->meta['keywords'];
        $this->allItemsUrl = '/' . App_Route::_getUrl('novinkiSezona') . '.html';
        if (!$this->ss->cnt_id) {
            return App_Route::redir404();
        }
        $this->lenta = $this->ss->cntList(array(
            'rIds' => $this->ss->cnt_type_id,
            'start' => 0,
            'limit' => 4,
            'fields' => array('intro'),
            'exDocIds' => $this->ss->cnt_id
        ));
        $this->lentaNum = count($this->lenta);
        foreach ($this->lenta as &$v) $v['url'] = '/' . App_Route::_getUrl('novinkiSezona') . '/' . $v['sname'] . '.html';

        $this->breadcrumbs['Новинки сезона'] = $this->allItemsUrl;

    }


}