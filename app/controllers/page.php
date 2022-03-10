<?
class App_Page_Controller extends App_Common_Controller {
	
	
	public function index()
    {
			
		$this->view('page/index');
		
		if(@Url::$spath[2]=='') return App_Route::redir404();
		$this->content=$this->parse($this->ss->getDoc(Url::$spath[2].'$3'));
		if(!$this->ss->cnt_id) {
			return App_Route::redir404();
		}
		$this->title=$this->_title=$this->ss->cnt_title;
		$this->breadcrumbs[]=$this->ss->cnt_title;
		
	}

    public function city()
    {

        $this->view('page/city');
        $tc=new TC();
        $tc->que('city_by_sname',Url::$spath[2]);
        if($tc->qnum()){
            $tc->next();
            $city_id=$tc->qrow['city_id'];
            $this->city=Tools::unesc($tc->qrow['name']);
            $tc->que('co_by_city',$city_id);
            $this->coList='';
            if($tc->qnum())
                while($tc->next()!==false) $this->coList.="<li><a rel=\"nofollow\" href=\"https://{$tc->qrow['site']}\" target=\"_blank\">{$tc->qrow['name']}</a></li>";
            if($this->coList!='') $this->coList='<noindex><ul>'.$this->coList.'</ul></noindex>';
            $this->title="Шины и диски в {$this->city}. Доставка автошин в {$this->city}.";
            $this->_title="Шины и литые диски в {$this->city}. Доставка резины в {$this->city}.";
            $this->description="автошины в {$this->city}, шины и диски {$this->city}, продажа автошин в {$this->city}, авто шины в {$this->city}, литые диски в {$this->city}, колесные диски в {$this->city}, купить шины в {$this->city}, продажа шин и литых дисков в {$this->city}, интернет магазин шин и дисков, зимние шины в {$this->city}, автомобильная резина в {$this->city}, доставка шин и дисков в {$this->city}, авто покрышки в {$this->city}";

            $this->content=$this->parse($this->ss->getDoc('dostavka_vgorod$10'));

            $this->breadcrumbs['Доставка и оплата']='/i/dostavka.html';
            $this->breadcrumbs['Доставка по России']='';

        }else return App_Route::redir404();
    }

}