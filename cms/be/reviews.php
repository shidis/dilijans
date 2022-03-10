<?
include_once ('ajx_loader.php');

$cp->setFN('reviews');
$cp->checkPermissions();

//sleep(1);


$r->fres=true;
$r->fres_msg='';

$act=Tools::esc(@$_REQUEST['act']);

$rev=new Users_Reviews();

switch ($act){

    case 'items':
        $a=array('forceCatJoin'=>true);
        $state=@$_REQUEST['state'];
        $postedBy=(int)@$_REQUEST['postedBy'];
        $page=(int)@$_REQUEST['page']; // первая страница ==1
        $limit=(int)@$_REQUEST['limit'];
        $prodId=(int)@$_REQUEST['model_id'];
        $brand_id=(int)@$_REQUEST['brand_id'];
        $date1=Tools::fdate(@$_REQUEST['date1']);
        $date2=Tools::fdate(@$_REQUEST['date2']);

        if(!empty($date1)) $a['dtAddStart']=$date1;
        if(!empty($date2)) $a['dtAddEnd']=$date2;

        if(!empty($prodId)) $a['prodId']=$prodId;
        if(!empty($brand_id)) $a['brand_id']=$brand_id;

        if($postedBy=='-2'){
            $a['postedByAdmin']=0;
        }elseif($postedBy=='-1'){
            $a['postedByAdmin']='!=0';
        }elseif(!empty($postedBy)){
            $a['postedByAdmin']=$postedBy;
        }

        $a['states']=array(0,1,2);

        if($state=='0'){
            $a['states']=array(0,2);
        }elseif($state=='1'){
            $a['states']=1;
        }elseif($state=='-1'){
            $a['states']=-1;
        }

        // по всем критериям но без лимитов
        $d=$rev->olist($a);

        if($d===false){
            $r->fres=false;
            $r->fres_msg=$rev->strMsg();
            break;
        }
        $sym=0;
        foreach($d['data'] as $k=>$v){
            $sym+=mb_strlen(preg_replace("~[\r\n\s\t]~", '', trim($v['advants'].$v['defects'].$v['comment'])));
        }

        $r->canModerate=$rev->canModerate();

        // по всем критериям с лимитами
        $a['start']=($page-1)*$limit;
        $a['limit']=$limit;
        $d=$rev->olist($a);
        if($d===false){
            $r->fres=false;
            $r->fres_msg=$rev->strMsg();
            break;
        }
        $af = App_TFields::get('reviews', 'all', 1);

        foreach($d['data'] as $k=>$v){
            $d['data'][$k]['CM']=$r->canModerate;
            $d['data'][$k]['turl']=App_SUrl::tModel($v['model_id']);
            $d['data'][$k]['valsNum']=count($d['data'][$k]['vals']);
            $d['data'][$k]['_advants']=nl2br($d['data'][$k]['advants']);
            $d['data'][$k]['_defects']=nl2br($d['data'][$k]['defects']);
            $d['data'][$k]['_comment']=nl2br($d['data'][$k]['comment']);
            $d['data'][$k]['dt_add']=Tools::sDateTime($d['data'][$k]['dt_add']);
            $d['data'][$k]['__af']=array();
            foreach ($af as $fv){
                if ((mb_stripos($fv['dbType'], 'varchar') !== false || mb_stripos($fv['dbType'], 'text') !== false) && empty($fv['implodeVals']) && !empty($v[$fv['as']]))
                $d['data'][$k]['__af'][]=array('caption'=>$fv['caption'].':', 'v'=>$v[$fv['as']]);
            }
            if(empty($d['data'][$k]['__af'])) $d['data'][$k]['__af']=false;
        }
        $r->revs=$d;
        $r->revs['pages']=ceil($d['gtotal']/$limit);
        $r->revs['symbols']=$sym;

        // список моделй и брендов
        unset($a['start'],$a['prodId'],$a['brand_id']);
        if(empty($d['total'])) {
            $a['states']=array(0,1,2);
            unset($a['postedByAdmin'],$a['dtAddStart'],$a['dtAddEnd']);
        }

        $d=$rev->olist($a);
        if($d===false){
            $r->fres=false;
            $r->fres_msg=$rev->strMsg();
            break;
        }
        $sym=0;
        if(!empty($d['data'])){
            $m=array();
            $mi=array();
            $b=array();
            $bi=array();
            $bid=0;
            foreach($d['data'] as $k=>$v){
                if($bid!=$v['brand_id']){
                    $b[$v['brand_id']]=$v['bname'];
                    if(isset($m[$bid])) asort($m[$bid],SORT_STRING);
                    $bid=$v['brand_id'];
                }

                if(!isset($bi[$bid])) $bi[$bid]=1; else $bi[$bid]++;

                if(isset($m[$v['brand_id']][$v['model_id']])) $mi[$v['model_id']]++;
                else{
                    if(!isset($m[$v['brand_id']])) $m[$v['brand_id']]=array();
                    $m[$v['brand_id']][$v['model_id']]=$v['mname'];
                    $mi[$v['model_id']]=1;
                }

            }
            asort($b,SORT_STRING);
            asort($m[$bid],SORT_STRING);

            $sum=array_sum($bi);

            $r->models=array(
                array('title'=>"<b>Все отзывы ($sum)</b>",'key'=>0)
            );

            if(count($mi)>20) $exp=false; else $exp=true;

            foreach($b as $bid=>$bname){
                $mm=array();
                foreach($m[$bid] as $mid=>$mv){
                    $mm[]=array('title'=>$mv." ({$mi[$mid]})",'key'=>$mid);
                }
                $r->models[]=array('title'=>$bname." ({$bi[$bid]})",'key'=>$bid,'children'=>$mm,'expanded'=>$exp,'folder'=>true);
            }
        }
        break;

    case 'moderate':
        $rid=@$_REQUEST['rid'];
        $state=@$_REQUEST['state'];
        if(!$rev->moderate($rid,$state)){
            $r->fres=false;
            $r->fres_msg=$rev->strMsg();
            break;
        }
        $d=$rev->getReview(array(
            'reviewId'=>$rid
        ));
        if($d===false){
            $r->fres=false;
            $r->fres_msg=$rev->strMsg();
            break;
        }

        $r->rev=$d;

        break;

    default: $r->fres=false; $r->fres_msg='BAD ACT_CASE '.$act;
}

ajxEnd();