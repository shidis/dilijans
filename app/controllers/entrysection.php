<?

class App_Entrysection_Controller extends App_Common_Controller

{
    public function item() {

        $this->template('entryPage');
        $this->view('entrysection/item');

        $this->entrysection = new Entrysection();

        if(@Url::$spath[2]=='') return App_Route::redir404();
        $this->entrysection->que('entry_section_by_sname',Url::$spath[2],"published=1",1);
        if(!$this->entrysection->qrow['entry_section_id']) {
            return App_Route::redir404();
        }
        $this->title=$this->_title=Tools::unesc($this->entrysection->qrow['title']);
        $this->content=$this->ss->parseText(Tools::unesc($this->entrysection->qrow['text']));
        $this->description=Tools::html($this->entrysection->qrow['description']);
        $this->keywords=Tools::html($this->entrysection->qrow['keywords']);
        $this->dateSys=($this->entrysection->qrow['published']);
        $d=Tools::sdate($this->entrysection->qrow['published'],'-');
        $this->date=$d;

        $this->allItemsUrl='/'.App_Route::_getUrl('entrysection').'.html';

        $this->breadcrumbs['Новости'] = $this->allItemsUrl;
        $this->breadcrumbs[] = Tools::unesc($this->entrysection->qrow['title']);

        $this->entry = new Entry();
        $sectionId = $this->entrysection->qrow['entry_section_id'];

        $this->entry->que('entry_list_by_section_id', "entry_section_id=" . $sectionId . " AND published=1 AND (dt_published = '0000-00-00' OR dt_published <= '".date('Y-m-d')."') ORDER BY dt_added DESC");

        $d=$this->entry->fetchAll();
        $this->entryList=array();
        $this->entryListCount=count($d);
        foreach($d as $v){
            $d=Tools::sdate($v['published'],'-');
            $this->entryList[]=array(
                'url'=>'/'.App_Route::_getUrl('entry').'/'.$v['sname'].'.html',
                'title'=>Tools::unesc($v['title']),
                'intro'=>Tools::unesc($v['intro']),
                'text'=>$v['text'],
                'img1'=> !empty($v['img1']) ? $this->ss->makeImgPath($v['img1']) : '',
                'published'=> !empty($v['published']) ? $v['published'] : ''

            );
        }

    }

    public function lenta() {

        $this->template('entryPage');
        $this->view('entrysection/lenta');

        $this->entrysection=new Entrysection();

        $this->title=$this->_title='Новости';
        $this->entrysection->que('entry_section',1,'','',"published=1 AND (dt_published = '0000-00-00' OR dt_published <= '".date('Y-m-d')."') ORDER BY dt_added DESC");
        $d=$this->entrysection->fetchAll();
        $this->lenta=array();
        $this->lentaNum=count($d);
        foreach($d as $v){
            $d=Tools::sdate($v['published'],'-');
            $this->lenta[]=array(
                'url'=>'/'.App_Route::_getUrl('entrysection').'/'.$v['sname'].'.html',
                'title'=>Tools::unesc($v['title']),
                'intro'=>Tools::unesc($v['intro']),
                'date'=>$d,
                'img1'=> !empty($v['img1']) ? $this->ss->makeImgPath($v['img1']) : '',
                'published'=> !empty($v['published']) ? $v['published'] : ''
            );
        }

        $this->breadcrumbs[]='Новости';

        $this->entry = new Entry();

        $this->entry->que('entry', "published=1 AND (dt_published = '0000-00-00' OR dt_published <= '".date('Y-m-d')."')");

        $d=$this->entry->fetchAll();
        $this->entryList=array();
        $this->entryListCount=count($d);
        foreach($d as $v){
            $d=Tools::sdate($v['published'],'-');
            $this->entryList[]=array(
                'url'=>'/'.App_Route::_getUrl('entry').'/'.$v['sname'].'.html',
                'title'=>Tools::unesc($v['title']),
                'intro'=>Tools::unesc($v['intro']),
                'text'=>$v['text'],
                'img1'=> !empty($v['img1']) ? $this->ss->makeImgPath($v['img1']) : '',
                'published'=> !empty($v['published']) ? $v['published'] : ''
            );
        }

    }

}