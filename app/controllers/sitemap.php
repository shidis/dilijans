<?
class App_Sitemap_Controller extends App_Common_Controller {
	

   public function disks()
   {
       $this->view('sitemap/disks');

       $this->title="Карта сайта: диски";
       $this->_title="Карта сайта: диски";

       $n=$this->cc->cat_view(array(
           'gr'=>2,
           'nolimits'=>true,
           'where'=>$this->minQtyRadiusSQL,
           'order'=>'cc_brand.replica, cc_brand.name, cc_model.name, cc_cat.P5, cc_cat.P4, cc_cat.P6'
       ));

       $u=App_Route::_getUrl('dSearch').'.html?';

       $this->t0=array(
           '0'=>array(
               'url'=>'/'.App_Route::_getUrl('dCat').'.html',
               'anc'=>'Литые диски для авто'
           ),
           'replica'=>array(
               'url'=>'/'.App_Route::_getUrl('replcaCat').'.html',
               'anc'=>'Диски реплика'
           )
       );

       if($n){
           $this->t1=$this->t2=array();
           $b=$m=array();
           $r=$rsv=$sv=array();
           $br=$bsv=$rbsv=array();
           while($this->cc->next()!==false){
               if($this->cc->qrow['replica']){

               }else{
                   if(!isset($b[$this->cc->qrow['brand_id']])){
                       $b[$this->cc->qrow['brand_id']]=array();
                   }
                   // сверловка
                   if(!isset($sv[$this->cc->qrow['P4'].'-'.$this->cc->qrow['P6']])){
                       $sv[$this->cc->qrow['P4'].'-'.$this->cc->qrow['P6']]=1;
                       $u=$dcat."sv={$this->cc->qrow['P4']}x{$this->cc->qrow['P6']}";
                       echo "<url><loc>".self::$h.$u."</loc></url>".self::$e;
                   }
               }
           }
       }

   }

	public function index() {
		
		
	}
	
}