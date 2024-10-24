<?
class App_Catalog_Disks_Common_Controller extends  App_Common_Controller
{
    public $noimg2 = '/app/images/noimg2-m.jpg';

    public function dict_url($r)
    {
        $s='';
        foreach($r as $k=>$v) if($v)
            $s.=($s!=''?'&nbsp;&nbsp;':'')."<a href=\"#\" rel=\"/ax/explain/color?v=$v\" title=\"Что значит $k?\" class=\"atip gr\">$k</a>";
        else $s.=($s!=''?'&nbsp;&nbsp;':'').$k;
        return trim($s);
    }

    public function makeId($v)
    {
        return preg_replace("~[^a-z0-9_-]~iu",'_',str_replace('*','x',$v));
    }

    public function usortSVfoo($a,$b)
    {
        $a=explode('*',$a);
        $b=explode('*',$b);
        if($a[0]<$b[0]) return -1;
        if($a[0]>$b[0]) return 1;
        if($a[0]==$b[0] && $a[1]<$b[1]) return -1;
        if($a[0]==$b[0] && $a[1]>$b[1]) return 1;
        return 0;
    }

    function _sidebar()
    {
        // быстрые бренды
        $burl='/'.App_Route::_getUrl('dCat').'/';
        $this->qbrands=array(0=>array());
        $r=array(
            'gr'=>2,
            'd_type'=>(int)@App_Route::$param['d_type'],
            'whereCat'=>$this->minQtyRadiusSQL,
            'qSelect'=>array(
                'modelsNum'=>array()
            ),
            'select'=>array(
                'cc_brand.replica'=>'replica',
                'cc_brand.name'=>'name',
                'cc_brand.sname'=>'sname'
            )
        );
        $r=$this->cc->brands($r);
        if($r){
            $this->qbrands=array();
            while($this->cc->next()!==false){
                $this->qbrands[$this->cc->qrow['replica']?'Реплика':0][]=array(
                    'name'=>Tools::unesc($this->cc->qrow['name']),
                    'sname'=>$burl.$this->cc->qrow['sname'].'.html'
                );
            }
        }

        if(!empty($this->brand_id)){
            // модели в сайдбаре. bname, brand_id должно быть определено выше
            $r=array(
                'gr'=>2,
                'P1' => (int)@App_Route::$param['d_type'],
                'brand_id'=>$this->brand_id,
                'nolimits'=>1,
                'qSelect'=>array(
                    'scDiv'=>array('where'=>$this->minQtyRadiusSQL)
                ),
                'order'=>"m_pos ASC, cc_model.name"
            );
            $this->cc->models($r);
            $d=$this->cc->fetchAll('', MYSQLI_ASSOC);
            $this->qmodels=array();
            $burl='/'.App_Route::_getUrl('dModel').'/';
            foreach($d as $v){
                $mname=Tools::unesc($v['name']);
                $v['suffix']=Tools::unesc($v['suffix']);
                $vi=array(
                    'anc'=>"{$this->bname} {$mname} {$v['suffix']}",
                    'url'=>$burl.$v['sname'].'.html',
                    'scDiv'=>$v['scDiv']
                );
                $this->qmodels[]=$vi;
            }

        }

    }

    function catRow($v,$burl)
    {
        $fullSize=trim("{$v['P2']}x{$v['P5']} {$v['P4']}/{$v['P6']} ET{$v['P1']}".' '.($v['P3']!=0?"DIA {$v['P3']}":''));
        // Стикеры
        if (!empty($v['sticker_id'])) {
            $CC_Ctrl = new CC_Ctrl();
            $stickers_list = $CC_Ctrl::getStickersList();
            $m_sticker = $CC_Ctrl->getModelSticker($v['model_id']);
            if (!empty($m_sticker)) {
                @$v['m_sticker'] = array_merge($m_sticker, $stickers_list[$m_sticker['sticker_type']]);
            }
            unset($CC_Ctrl);
        }
        else $v['m_sticker'] = array();
        //
        $vi=array(
            'video_link'=>  $v['video_link'],
            'img3'=>		$this->cc->make_img_path($v['img3']),
            'img2'=>		$this->cc->make_img_path($v['img2']),
            'img1'=>		$this->cc->make_img_path($v['img1']),
            'img1Blk'=>     $this->cc->img_path==''?$this->noimg2:$this->cc->img_path,
            'url'=>			$burl.$v['cat_sname'].'.html',
            'bname'=>       Tools::html($v['bname']),
            'mname'=>       Tools::html($v['mname'].' '.$v['msuffix']),
            'imgAlt'=>    'Фото диска '.Tools::html($v['bname'].' '.$v['mname'].' '.$fullSize.' '.$v['csuffix']),
            'anc'=>	        Tools::unesc($v['bname'].' '.$v['mname']),
            'ancBlk'=>	    Tools::unesc($v['bname'].' '.$v['mname']),
            'title'=>	    "резина ".Tools::html($v['bname'].' '.$v['mname'].' '.$fullSize.' '.$v['csuffix']),
            'qtyText'=>		$v['sc']>12?"&gt;&nbsp;12&nbsp;шт":(!$v['sc']?'-':"{$v['sc']}&nbsp;шт"),
            'scText'=>      $v['sc']?("<span class=\"nal\">на&nbsp;складе&nbsp;(".($v['sc']>12?'&gt;12':$v['sc'])."&nbsp;шт.)</span>"):"<span class=\"nnal\">нет&nbsp;на&nbsp;складе</span>",
            'maxQty'=>		$v['sc'],
            'defQty'=>		$v['sc']>4 || $v['sc']==0?4:$v['sc'],
            'priceText'=>	$v['cprice']?(Tools::nn($v['cprice'])."&nbsp;р."):'звоните',
            'cprice' =>    $v['cprice']?(Tools::nn($v['cprice'])):'0',
            'priceTextBlk'=> $v['cprice']?('<span class="price scl" cat_id="'.$v['cat_id'].'">'.Tools::nn($v['cprice'])."<span class='cur'>&nbsp;руб. за диск</span></span>"):'<span class="price">-<span class="cur">&nbsp;руб. за диск</span></span>',
            'cat_id'=>		$v['cat_id'],
            'razmer'=>		"{$v['P2']} x {$v['P5']}",
            'sverlovka'=>	"{$v['P4']} x {$v['P6']}",
            'sverlovka1'=>	"{$v['P4']}/{$v['P6']}",
            'dia'=>			$v['P3']!=0?$v['P3']:'',
            'et'=>			$v['P1'],
            'color'=>       $v['csuffix'],
            'fullName'=>    $fullSize,
            'colorUrl'=>     $this->dict_url($this->cc->dict_search_key($v['csuffix'],$v['gr'],$v['brand_id'])),
            'newBlk'=>         ($v['mspez_id']==2?'<i></i>':''),
            'newTbl'=>         ($v['mspez_id']==2?'<div class="new">новинка</div>':''),
            'm_sticker'=>   $v['m_sticker'],
            'model_sticker'=>   $v['m_sticker'],
            'brand_img1'=>  $this->cc->make_img_path($v['brand_img1']),
            'brand_img2'=>  $this->cc->make_img_path($v['brand_img2'])
        );

        if($this->sMode){
            if($v['sc']>=2 || $v['sc']==0) $vi['defQty']=2;
        }

        return $vi;
    }


}