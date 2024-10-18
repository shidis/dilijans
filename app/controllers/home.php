<?
class App_Home_Controller extends App_Common_Controller {
	
	public function index ()
    {
		$this->title='Автомобильные шины и колесные диски';
		$this->template('mainPage');

        $this->mpAbout=$this->split_text($this->parse($this->ss->getDoc('mp_about$13')));

        // баннеры
        $this->parse($this->ss->getDoc('mp_sidebar_1$14'));
        $this->bnr1=array(
            'img'=>$this->ss->img1,
            'url'=>$this->ss->link
        );
        $this->parse($this->ss->getDoc('mp_sidebar_tyres$14'));
        $this->bnrTyres=array(
            'img'=>$this->ss->img1,
            'url'=>$this->ss->link
        );
        $this->parse($this->ss->getDoc('mp_sidebar_disks$14'));
        $this->bnrDisks=array(
            'img'=>$this->ss->img1,
            'url'=>$this->ss->link
        );
        $this->parse($this->ss->getDoc('mp_sidebar_replica$14'));
        $this->bnrReplica=array(
            'img'=>$this->ss->img1,
            'url'=>$this->ss->link
        );
        $this->parse($this->ss->getDoc('mp_center$14'));
        $this->bnrCenter=array(
            'text'=>$this->ss->cnt_text
        );
        // дилижанс гарантирует
        $this->ss->que('cnt_list',16);
        $d=$this->ss->fetchAll();
        $this->mpBlockTop=array();
        foreach($d as $v){
            $this->mpBlockTop[]=array(
                'anc'=>Tools::unesc($v['title']),
                'url'=>Tools::unesc($v['link']),
                'img'=>$this->ss->makeImgPath($v['img1'])
            );
        }
        // интересные предложения
        $this->ss->que('cnt_list',15);
        $d=$this->ss->fetchAll();
        $this->mpBlockInterest=array();
        foreach($d as $v){
            $this->mpBlockInterest[]=array(
                'title'=>Tools::unesc($v['title']),
                'sname'=>Tools::unesc($v['sname']),
                'text'=>$this->ss->parse(Tools::unesc($v['text'])),
                'url'=>Tools::unesc($v['link']),
                'img'=>$this->ss->makeImgPath($v['img1'])
            );
        }


        // новости
        $this->n=new News();
        $this->newsSB=array();
        $this->n->que('news_list',Data::get('sidebar_news'),1);
        $this->allNewsUrl=($u='/'.App_Route::_getUrl('news')).'.html';
        while($this->n->next()!==false){
            $d=Tools::sdate($this->n->qrow['dt'],'-');
            $d=explode('-',$d);
            $d[1]=$this->month($d[1],3);
            $s=mb_substr($s1=Tools::html($this->n->qrow['title']),0,150);
            if($s!=$s1) $s.='...';
            $this->newsSB[]=array(
                'url'=>$u.'/'.$this->n->qrow['sname'].'.html',
                'img'=>$this->n->makeImgPath(1),
                'title'=>$s,
                'intro'=>Tools::unesc($this->n->qrow['intro']),
                'day'=>$d[0],
                'month'=>$d[1]
            );
        }

        // бренды шин
        $n=$this->cc->brands(array(
			'gr'=>1
		));
		$this->tBrands=array();
		if($n) while($this->cc->next()!==false){
			$this->tBrands[]=array(
				'name'=>Tools::html($this->cc->qrow['name']),
				'url'=>'/'.App_Route::_getUrl('tCat').'/'.Tools::unesc($this->cc->qrow['sname']).'.html'
			);
		}

        // ьренды дисков
		$n=$this->cc->brands(array(
			'gr'=>2,
			'where'=>array(
				'replica!=1',
			)
		));
		$this->dBrands=array();
		if($n) while($this->cc->next()!==false){
			$this->dBrands[]=array(
				'name'=>Tools::html($this->cc->qrow['name']),
				'url'=>'/'.App_Route::_getUrl('dCat').'/'.Tools::unesc($this->cc->qrow['sname']).'.html'
			);
		}

        // бренды реплики
		$n=$this->cc->brands(array(
			'gr'=>2,
			'where'=>array(
				'replica=1'
			)
		));
		$this->replicaBrands=array();
		if($n) while($this->cc->next()!==false){
			$this->replicaBrands[]=array(
				'name'=>Tools::html($this->cc->qrow['name']),
				'url'=>'/'.App_Route::_getUrl('dCat').'/'.Tools::unesc($this->cc->qrow['sname']).'.html',
                'img'=>$this->cc->makeImgPath(1)
			);
		}

        // Слайдер
        $this->slides = $this->ss->fetchAll("SELECT * FROM slider ORDER BY slide_id DESC", MYSQLI_ASSOC);
        if (!empty($this->slides))
        {
            foreach ($this->slides as $sid => $slide)
            {
                $this->slides[$sid]['src'] = str_replace(Cfg::get('root_path'),'',$slide['image']);
            }
        }

        $db = new DB();
        $this->result = Array();
        $res = $db->fetchAll("SELECT * FROM ab_avto WHERE (year_id=0)AND(NOT H)AND(showOnTheMain=1) ORDER BY sortOnTheMain", MYSQLI_ASSOC);
        if (!empty($d)) {
            foreach ($res as $item) {
                if ($item['vendor_id'] == 0){
                    $models_info = $db->fetchAll("SELECT * FROM ab_avto WHERE (vendor_id={$item['avto_id']})AND(model_id=0)AND(NOT H) ORDER BY name", MYSQLI_ASSOC);
                    $this->result[$item['avto_id']]['brand_info'] = $item;
                    foreach ($models_info as $model) {
                        $model['d_url'] = '/' . App_Route::_getUrl('avtoPodborDiskov') . '/' . Tools::unesc($item['sname']) . '--' . Tools::unesc($model['sname']) . '.html';
                        $model['t_url'] = '/' . App_Route::_getUrl('avtoPodborShin')   . '/' . Tools::unesc($item['sname']) . '--' . Tools::unesc($model['sname']) . '.html';
                        $this->result[$item['avto_id']]['models_info'][] = $model;
                    }
                }else {
                    $brand_info = $db->getOne("SELECT * FROM ab_avto WHERE (avto_id={$item['vendor_id']})AND(NOT H) ORDER BY name", MYSQLI_ASSOC);
                    $item['d_url'] = '/' . App_Route::_getUrl('avtoPodborDiskov') . '/' . Tools::unesc($brand_info['sname']) . '--' . Tools::unesc($item['sname']) . '.html';
                    $item['t_url'] = '/' . App_Route::_getUrl('avtoPodborShin')   . '/' . Tools::unesc($brand_info['sname']) . '--' . Tools::unesc($item['sname']) . '.html';
                    $item['brand_info'] = $brand_info;
                    $this->result[] = $item;
                }
            }
            foreach($this->result as $id=>$brand){
                $brand_img_info = $db->getOne("SELECT img1, img2 FROM cc_brand WHERE LOWER(name) = '".mb_strtolower($brand['brand_info']['name'])."'", MYSQLI_ASSOC);
                if (!empty($brand_img_info)) {
                    $this->result[$id]['brand_info'] = array_merge($brand['brand_info'], $brand_img_info);
                }
            }
        }
        //Tools::p($this->result);

        // Лента нвоостей
        $this->entrysection = new Entrysection();
        $this->entry = new Entry();
        $this->entry->que('entry', "published=1 AND (dt_published = '0000-00-00' OR dt_published <= '".date('Y-m-d')."')", '', 3);
        $d = $this->entry->fetchAll();
        $this->entryList = array();
        $this->entryListCount = count($d);

        foreach ($d as $v) {
            $d = Tools::sdate($v['published'], '-');
            $this->entryList[] = array(
                'url' => '/' . App_Route::_getUrl('entry') . '/' . $v['sname'] . '.html',
                'title' => Tools::unesc($v['title']),
                'intro' => Tools::unesc($v['intro']),
                'text' => $v['text'],
                'img1' => !empty($v['img1']) ? $this->ss->makeImgPath($v['img1']) : '',
                'published' => !empty($v['published']) ? $v['published'] : ''
            );
        }

	}
	
}

