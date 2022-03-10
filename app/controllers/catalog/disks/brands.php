<?
class App_Catalog_Disks_Brands_Controller extends App_Catalog_Disks_Common_Controller {

    //бренды дисков
    public function index() {

        $this->view('catalog/disks/brands');

        $this->title='Колесные литые диски - каталог и цены на автомобильные диски';
        $this->_title='Автомобильные диски';

        $this->breadcrumbs[]='диски';

        $this->qbrands=array(0=>array(),'Replica'=>array());

        $this->h=$this->cc->maxImgBH(2,1);

        $this->brands=array();
        $this->cc->brands(array(
            'gr'=>2,
            'd_type'=> (int)@App_Route::$param['d_type'],
            'where'=>'cc_brand.replica=0',
            'qSelect'=>array(
                'modelsNum'=>array()
            ),
            'select'=>array(
                'cc_brand.name'=>'name',
                'cc_brand.alt'=>'alt',
                'cc_brand.sname'=>'sname',
                'cc_brand.img1'=>'img1',
                'cc_brand.is_popular'=>'is_popular'
            ),
            'whereCat'=>$this->minQtyRadiusSQL,
            'having'=>'modelsNum>0'
        ));
        $d=$this->cc->fetchAll('',MYSQL_ASSOC);
        $burl='/'.App_Route::_getUrl('dCat').'/';
        foreach($d as $v){
            $this->brands[$v['is_popular']][]=array(
                'img1'=>$this->cc->make_img_path($v['img1']),
                'url'=>$burl.Tools::unesc($v['sname']).'.html',
                'title'=>'Купить диски '.($v['alt']!=''?$v['alt']:$v['name']),
                'alt'=>'литые диски '.Tools::html($v['name']),
                'name'=>Tools::unesc($v['name'])
            );
        }

        foreach($d as $v){
            $this->qbrands[0][]=array(
                'name'=>Tools::unesc($v['name']),
                'sname'=>$burl.Tools::unesc($v['sname']).'.html'
            );
        }

        // replica
        /*
        $this->replicaBrands=array();
        $this->cc->brands(array(
            'gr'=>2,
            'where'=>'cc_brand.replica=1',
            'qSelect'=>array(
                'modelsNum'=>array()
            ),
            'select'=>array(
                'cc_brand.name'=>'name',
                'cc_brand.alt'=>'alt',
                'cc_brand.sname'=>'sname',
                'cc_brand.img1'=>'img1'
            ),
            'whereCat'=>'cc_cat.sc>0',
            'having'=>'modelsNum>0'
        ));
        $d=$this->cc->fetchAll('',MYSQL_ASSOC);
        foreach($d as $v){
            $this->replicaBrands[]=array(
                'img1'=>$this->cc->make_img_path($v['img1']),
                'url'=>$burl.Tools::unesc($v['sname']).'.html',
                'title'=>'Купить диски реплика '.($v['alt']!=''?$v['alt']:$v['name']),
                'alt'=>'Replica '.Tools::html($v['name']),
                'name'=>Tools::unesc($v['name'])
            );
        }

        foreach($d as $v){
            $this->qbrands['Replica'][]=array(
                'name'=>Tools::unesc($v['name']),
                'sname'=>$burl.Tools::unesc($v['sname']).'.html'
            );
        }
*/
        $this->exnum=$this->cc->cat(array(
            'gr'=>2,
            'brand_id'=>$this->brand_id,
            'where'=>array($this->minQtyRadiusSQL, 'cc_model.P1='.(int)@App_Route::$param['d_type'].''),
            'fields'=>"cc_model.model_id,cc_cat.P5+'0' AS P5,cc_cat.P4+'0' AS P4,cc_cat.P6+'0' AS P6",
            'ex'=>1,
            'nolimits'=>1,
            'exFields'=>array('P5'=>array(),'P46'=>array())
        ));

        $this->ex=$this->cc->ex_arr;
        unset($this->ex['P5'][0],$this->cc->ex_arr);

        $this->_filters();

        // ссылки по радиусам
        $this->rlinks=array();
        $burl='/'.App_Route::_getUrl('dSearch').'.html?p5=';
        foreach($this->cc->s_arr['P5_2'] as $v){
            $this->rlinks[]=array(
                'url'=>$burl.$v,
                'title'=>'купить диски r'.$v,
                'anc'=>"Диски R{$v}"
            );
        }

    }


    public function replica() {

        $this->view('catalog/disks/brands');

        $this->title='Диски реплика - каталог и цены на литые диски Replica';
        $this->_title='Диски реплика';

        $this->breadcrumbs['диски']='/'.App_Route::_getUrl('dCat').'.html';
        $this->breadcrumbs[]='диски реплика';

        $this->replica=1;

        $this->h=$this->cc->maxImgBH(2,1);

        $this->brands=array();
        $this->cc->brands(array(
            'gr'=>2,
            'where'=>'cc_brand.replica=1',
            'qSelect'=>array(
                'modelsNum'=>array()
            ),
            'select'=>array(
                'cc_brand.name'=>'name',
                'cc_brand.alt'=>'alt',
                'cc_brand.sname'=>'sname',
                'cc_brand.img1'=>'img1'
            ),
            'whereCat'=>$this->minQtyRadiusSQL,
            'having'=>'modelsNum>0'
        ));
        $burl='/'.App_Route::_getUrl('dCat').'/';
        $d=$this->cc->fetchAll('',MYSQL_ASSOC);
        foreach($d as $v){
            $this->brands[0][]=array(
                'img1'=>$this->cc->make_img_path($v['img1']),
                'url'=>$burl.Tools::unesc($v['sname']).'.html',
                'title'=>'Купить диски реплика '.($v['alt']!=''?$v['alt']:$v['name']),
                'alt'=>'Replica '.Tools::html($v['name']),
                'name'=>Tools::unesc($v['name'])
            );
        }

        $this->qbrands=array(0=>array());
        foreach($d as $v){
            $this->qbrands[0][]=array(
                'name'=>Tools::unesc($v['name']),
                'sname'=>$burl.Tools::unesc($v['sname']).'.html'
            );
        }

        //фильтр

        $this->exnum=$this->cc->cat(array(
            'gr'=>2,
            'where'=>"$this->minQtyRadiusSQL AND cc_brand.replica=1",
            'fields'=>"cc_model.model_id,cc_cat.P5+'0' AS P5,cc_cat.P4+'0' AS P4,cc_cat.P6+'0' AS P6",
            'ex'=>1,
            'nolimits'=>1,
            'exFields'=>array('P5'=>array(),'P46'=>array())
        ));
        $this->ex=$this->cc->ex_arr;
        unset($this->ex['P5'][0],$this->cc->ex_arr);

        /*
        $this->_filters();
        */

        // ссылки по радиусам
        $this->rlinks=array();
        $burl='/'.App_Route::_getUrl('dSearch').'.html?replica=1&p5=';
        foreach(array_keys($this->ex['P5']) as $v){
            $this->rlinks[]=array(
                'url'=>$burl.$v,
                'title'=>'купить диски реплика r'.$v,
                'anc'=>"Replica R{$v}"
            );
        }

    }

    private function _filters()
    {
        $this->lf=array();
        $this->lfi=0;
        $this->lfh=array();

        if(count(@$this->ex['P5'])>1){
            $this->lfi++;
            ksort($this->ex['P5']);
            $this->lf['p5']=array();
            $si=App_Route::_getUrl('dSearch').'?';
            foreach($this->ex['P5'] as $k=>$v){
                $this->lf['p5'][$k]=array(
                    'chk'=>false,
                    'anc'=>"R$k",
                    'id'=>'_p5'.$this->makeId($k),
                    'url'=>$si.'p5='.$k
                );
            }
        }
        if(count(@$this->ex['P46'])>1){
            $this->lfi++;
            uksort($this->ex['P46'], array($this,'usortSVfoo'));
            $this->lf['sv']=array();
            $si=App_Route::_getUrl('dSearch').'?';
            foreach($this->ex['P46'] as $k=>$v) if($k>0) {
                $this->lf['sv'][$k]=array(
                    'chk'=>false,
                    'anc'=>$k,
                    'id'=>'_sv'.$this->makeId($k),
                    'url'=>$si.'sv='.$k
                );
            }
        }

        if(@$this->replica) $this->lfh['replica']=@$this->replica;


    }


}