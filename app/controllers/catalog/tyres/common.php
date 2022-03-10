<?
class App_Catalog_Tyres_Common_Controller extends  App_Common_Controller
{

    function dict_url($r)
    {
        $s='';
        foreach($r as $k=>$v) if($v)
            $s.=($s!=''?'&nbsp;&nbsp;':'')."<a href=\"#\" rel=\"/ax/explain/suf?v=$v\" title=\"Что значит $k?\" class=\"atip gr\">$k</a>";
        else $s.=($s!=''?'&nbsp;&nbsp;':'').$k;
        return trim($s);
    }

    public function makeId($v)
    {
        return preg_replace("~[^a-z0-9_-]~iu",'_',$v);
    }


    function _sidebar()
    {
        // быстрые бренды
        $burl='/'.App_Route::_getUrl('tCat').'/';
        $this->qbrands=array(0=>array());
        $r=$this->cc->brands(array(
            'gr'=>1,
            'whereCat'=>"IF(cc_cat.gr=1, IF(cc_cat.P1<".(int)Data::get('cc_border_radius').", cc_cat.sc>=4 , cc_cat.sc>=2), IF(cc_cat.P5<".(int)Data::get('cc_border_radius').", cc_cat.sc>=4 , cc_cat.sc>=2))",
            'qSelect'=>array(
                'modelsNum'=>array()
            ),
            'select'=>array(
                'cc_brand.name'=>'name',
                'cc_brand.sname'=>'sname'
            )
        ));
        if($r){
            $this->qbrands=array();
            while($this->cc->next()!==false){
                $this->qbrands[0][]=array(
                    'name'=>Tools::unesc($this->cc->qrow['name']),
                    'sname'=>$burl.$this->cc->qrow['sname'].'.html'
                );
            }
        }

        // модели в сайдбаре. bname должно быть определено выше
        if(!empty($this->brand_id)){
            $r=array(
                'gr'=>1,
                'brand_id'=>$this->brand_id,
                'nolimits'=>1,
                'qSelect'=>array(
                    'scDiv'=>array('where'=>$this->minQtyRadiusSQL)
                ),
                'order'=>"cc_model.P1, cc_model.name"
            );

            $this->cc->models($r);
            $d=$this->cc->fetchAll('', MYSQL_ASSOC);
            $this->qmodels=array();
            $burl='/'.App_Route::_getUrl('tModel').'/';
            foreach($d as $v){
                $mname=Tools::unesc($v['name']);
                $v['suffix']=Tools::unesc($v['suffix']);
                $vi=array(
                    'anc'=>"{$this->bname} {$mname} {$v['suffix']}",
                    'url'=>$burl.$v['sname'].'.html',
                    'scDiv'=>$v['scDiv']
                );
                $this->qmodels[$v['P1']][]=$vi;
            }

            // активная вкладка в списке моделей
            $this->qmodels['active']=$this->sezonId;
        }

    }

    function catRow($v,$burl)
    {
        $fullSize="{$v['P3']}/{$v['P2']} R{$v['P1']}".($v['csuffix']!=''?" {$v['csuffix']}":'');
        $vi=array(
            'video_link'=>  $v['video_link'],
            'img3'=>		$this->cc->make_img_path($v['img3']),
            'img2'=>		$this->cc->make_img_path($v['img2']),
            'img1'=>		$this->cc->make_img_path($v['img1']),
            'img1Blk'=>     $this->cc->img_path==''?$this->noimg1:$this->cc->img_path,
            'url'=>			$burl.$v['cat_sname'].'.html',
            'bname'=>       Tools::html($v['bname']),
            'mname'=>       Tools::html($v['mname'].' '.$v['msuffix']),
            'imgAlt'=>      'Фото шины '.Tools::html($v['bname'].' '.$v['mname'].' '.$fullSize),
            'suffixUrl'=>    $this->dict_url($this->cc->dict_search_key($v['csuffix'],$v['gr'],$this->brand_id)),
            'anc'=>	        Tools::unesc($v['bname'].' '.$v['mname']),
            'ancBlk'=>	    Tools::unesc($v['bname'].' '.$v['mname'].' '.$v['msuffix']),
            'title'=>	    "резина ".Tools::html($v['bname'].' '.$v['mname'].' '.$fullSize),
            'qtyText'=>		$v['sc']>12?"&gt;&nbsp;12&nbsp;шт":(!$v['sc']?'-':"{$v['sc']}&nbsp;шт"),
            'scText'=>      $v['sc']?("<span class=\"nal\">на&nbsp;складе&nbsp;(".($v['sc']>12?'&gt;12':$v['sc'])."&nbsp;шт.)</span>"):"<span class=\"nnal\">нет&nbsp;на&nbsp;складе</span>",
            'maxQty'=>		$v['sc'],
            'defQty'=>		$v['sc']>4 || $v['sc']==0?4:$v['sc'],
            'priceText'=>	$v['cprice']?(Tools::nn($v['cprice'])."&nbsp;р."):'звоните',
            'cprice' =>    $v['cprice']?(Tools::nn($v['cprice'])):'0',
            'priceTextBlk'=>$v['cprice']?('<span class="price scl" cat_id="'.$v['cat_id'].'">'.Tools::nn($v['cprice'])."<span class='cur'>&nbsp;руб. за шину</span></span>"):'<span class="price">-<span class="cur">&nbsp;руб. за шину</span></span>',
            'cat_id'=>		$v['cat_id'],
            'razmer'=>		"{$v['P3']}/{$v['P2']}&nbsp;R{$v['P1']}",
            'INIS'=>        "{$v['P7']}",
            'shipIco'=>     '',
            'sezIco'=>      '',
            'inisUrl'=>     $v['P7']!=''?('<a href="#" rel="/ax/explain/inis?v='.$v['P7'].'" title="Что означает '.$v['P7'].'?" class="atip gr">'.$v['P7'].'</a>'):'',
            'newBlk'=>         ($v['mspez_id']==1?'<i></i>':''),
            'newTbl'=>         ($v['mspez_id']==1?'<div class="new">новинка</div>':''),
            'brand_img1'=>  $this->cc->make_img_path($v['brand_img1']),
            'brand_img2'=>  $this->cc->make_img_path($v['brand_img2'])
        );
        if($this->sMode){
            if($v['sc']>=2 || $v['sc']==0) $vi['defQty']=2;
        }

        switch($v['MP1']){
            case 1:
                $vi['sezIcoBlk']='<u class="sun nttip" title="Летние шины"></u>';
                $vi['sezIco']='<img src="/app/images/sun.png" title="Летние шины" class="nttip">';
                break;
            case 2:
                $vi['sezIco']='<img src="/app/images/snow.png" title="Зимние шины" class="nttip">';
                $vi['sezIcoBlk']='<u class="snow nttip" title="Зимние шины" class="nttip"></u>';                if($v['MP3']) {
                    $vi['shipIco']='<img src="/app/images/ship.png" title="Шипованные шины" class="nttip">';
                    $vi['sezIcoBlk'].='<em title="Шипованные шины" class="nttip"></em>';
                }
                break;
            case 3:
                $vi['sezIco']='<img src="/app/images/sunsnow.png" title="Всесезонные шины" class="nttip">';
                $vi['sezIcoBlk']='<u class="sun-snow nttip" title="Всесезонные шины"></u>';
                break;
        }

        return $vi;
    }


    /*
     * даныне для ленты отзывов
     * r=[
     *      modelId
     *      noList - вергнуть без списка, только статистику
     * ]
     *
     * возвращает [
     *      mrate - рейтинг модели - от cc_model
     *      canAdd - можно добавить новый
     *      total - всего отзывов
     *      list  -[] - лист отзывов
     */
    public function _reviewsList($r)
    {
        $res=[
            'total'=>0,
            'mrate'=>0
        ];
        $mid=(int)@$r['modelId'];
        $rv=new App_Users_Reviews();

        if(!$rv->isEnabled(1)) return false;

        $res['canAdd']=$rv->allowToAdd(1,$mid);

        if(!empty(CU::$userId)){

            $d = $rv->olist(array(
                'prodId'=>$mid,
                'states' => array(0, 1, 2),
                'listStates' => empty($r['noList']) ? array(0, 1, 2) : [],
                'includeOwnPosts' => true
            ));
        }else{

            $d = $rv->olist(array(
                'prodId'=>$mid,
                'states' => array(1),
                'listStates' => empty($r['noList']) ? array(1) : [],
                'includeOwnPosts' => true
            ));
        }

        if ($d === false) {
            //echo $rv->strMsg();
            return false;
        }
        //Tools::prn($d);
        $this->revRatingItems=$rv->cfg['ratingItems'];
        if(!Request::$ajax) {
            $this->VJS['revRatingScale'] = $rv->cfg['ratingScale'];
            $this->VJS['mid'] = $mid;
        }

        $res['total']=$d['gtotal'];
        $db=new DB();
        $md=$db->getOne("SELECT rating FROM cc_model WHERE model_id=$mid");
        $res['mrate']=@$md[0];
        $res['list']=$d['data'];

        return $res;
    }

    function getReviewsHtml()
    {
        $rv=new App_Users_Reviews();
        if(!$rv->isEnabled(1)) {
            $this->content='Отзывы выключны';
            $this->template('blank');
            return;
        }
        $this->revRatingItems=$rv->cfg['ratingItems'];

        $this->rvws=$this->_reviewsList([
            'modelId'=>@$_REQUEST['mid']
        ]);
        $this->template('catalog/tyres/reviews/list');
    }

    function getReviewForm()
    {
        $rv=new App_Users_Reviews();
        if(!$rv->isEnabled(1)) {
            $this->content='Отзывы выключны';
            $this->template('blank');
            return;
        }
        $this->revRatingItems=$rv->cfg['ratingItems'];
        $mid=(int)@$_REQUEST['mid'];
        $cc=new CC_Base();
        $cc->que('model_by_id',$mid);
        if($cc->qnum()) $cc->next();
        $this->mname=@$cc->qrow['name'];
        $this->bname=@$cc->qrow['bname'];
        $this->template('catalog/tyres/reviews/form-add');
    }

    function getModReviewForm()
    {
        $rv=new App_Users_Reviews();
        if(!$rv->isEnabled(1)) {
            $this->content='Отзывы выключны';
            $this->template('blank');
            return;
        }
        $this->revRatingItems=$rv->cfg['ratingItems'];

        $this->ritem=$rv->getReview([
            'reviewId'=>@$_REQUEST['reviewId']
        ]);
        if(empty($this->ritem)){
            if(!empty($rv->fres_msg)) {
                $this->content=$rv->strMsg();
                $this->template('blank');
            }else{
                $this->content='Ошибка в процессе формирования формы редактирования';
                $this->template('blank');
            }
            return;
        }
        $mid=(int)@$_REQUEST['mid'];
        $cc=new CC_Base();
        $cc->que('model_by_id',$mid);
        if($cc->qnum()) $cc->next();
        $this->mname=@$cc->qrow['name'];
        $this->bname=@$cc->qrow['bname'];
        $this->template('catalog/tyres/reviews/form-add');
    }

    function getReviewHtml()
    {
        $rv=new App_Users_Reviews();
        if(!$rv->isEnabled(1)) {
            $this->content='Отзывы выключны';
            $this->template('blank');
            return;
        }
        $this->revRatingItems=$rv->cfg['ratingItems'];

        $d=$rv->getReview([
            'reviewId'=>@$_REQUEST['reviewId']
        ]);
        if(empty($d)){
            if(!empty($rv->fres_msg)) {
                $this->content=$rv->strMsg();
                $this->template('blank');
            }else{
                $this->content='Ошибка в процессе формирования данных';
                $this->template('blank');
            }
            return;
        }
        $this->userName=$d['userName'];
        $this->avtoName=$d['avtoName'];
        $this->vals=$d['vals'];
        $this->rating=$d['rating'];
        $this->postedByAdmin=$d['postedByAdmin'];
        $this->advants=$d['advants'];
        $this->defects=$d['defects'];
        $this->comment=$d['comment'];
        $this->editable=@$d['editable'];
        $this->id=@$d['id'];
        $this->postedBy_shortName=@$d['postedBy_shortName'];
        $this->cUserId=@$d['cUserId'];
        $this->cUser_shortName=@$d['cUser_shortName'];
        $this->state=@$d['state'];

        $this->template('catalog/tyres/reviews/item');

    }

    function postReview()
    {
        $rv=new App_Users_Reviews();
        if(!$rv->isEnabled(1)) {
            $this->fres=false;
            $this->err_msg='Отзывы выключены';
        }
        parse_str(@$_REQUEST['f'], $f);
        $this->r['incorrect'] = array();
        //$this->r['fff']=$f;
        $advants=trim($f['advants']);
        $defects=trim($f['defects']);
        $comment=trim($f['comment']);
        $name=trim($f['userName']);
        $avto=trim($f['avtoName']);
        $rating=(int)@$f['rating'];
        $vv=0;
        if(!empty($f['vals'])) foreach($f['vals'] as $k=>$v){
            if((int)$v>0) $vv++;
        }
        if(!$vv && !$rating) {
            $this->r['incorrect'][]='rating';
            $this->r['fres_msg']='Необходима общая или подробная оценка';
        }

        if(empty($advants) && empty($defects) && empty($comment)) {
            $this->r['incorrect']= array_merge($this->r['incorrect'], ['advants', 'defects', 'comment']);
            $this->r['fres_msg'] = 'Заполните одно или несколько полей: достоинсва, недостатки, комментарий';
        }else{
            if(!$rv->userText($advants, true)){
                $this->r['incorrect'][]='advants';
                $this->r['fres_msg']='Недопустимые символы в поле достоинств';
            }
            if(!$rv->userText($defects, true)){
                $this->r['incorrect'][]='defects';
                $this->r['fres_msg']='Недопустимые символы в поле недостатков';
            }
            if(!$rv->userText($comment, true)){
                $this->r['incorrect'][]='comment';
                $this->r['fres_msg']='Недопустимые символы в поле комментария';
            }
        }

        if(empty($name)){
            $this->r['incorrect'][]='userName';
            $this->r['fres_msg']='Представьтесь, пожалуйста';
        }else if(!$rv->userText($name, true)){
            $this->r['incorrect'][]='userName';
            $this->r['fres_msg']='Недопустимые символы в поле имени';
        }

        if(!$rv->userText($avto, true)){
            $this->r['incorrect'][]='avtoName';
            $this->r['fres_msg']='Недопустимые символы в поле марки/модели автомобиля';
        }


        if (!empty($this->r['incorrect'])) {
            $this->r['fres'] = false;
            return;
        }

        $f['prodId']=@$_REQUEST['mid'];

        $reviewId=$f['reviewId']=(int)@$_REQUEST['reviewId'];

        if(!empty($f['reviewId'])){
            if(!$rv->mod($f)){
                $this->r['fres'] = false;
                $this->r['err_msg'] = $rv->strMsg();
                return;
            }
        }else {
            $reviewId = $rv->add($f);
            if (!$reviewId) {
                $this->r['fres'] = false;
                $this->r['err_msg'] = $rv->strMsg();
                return;
            }
        }

        $this->r['data']=$rv->getReview([
            'reviewId'=>$reviewId
        ]);

    }

    function delReview()
    {
        $rid=(int)@$_REQUEST['reviewId'];
        $rv=new App_Users_Reviews();
        $d=$rv->delReview($rid);
        if(!$d){
            $this->r['fres']=false;
            $this->r['fres_msg']=$rv->strMsg();
        }
    }
}
