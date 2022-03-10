<?
include_once ('ajx_loader.php');

$cp->setFN('min_extra');
$cp->checkPermissions();

//sleep(1);


$cc=new CC_Ctrl();

$r->fres=true;
$r->fres_msg='';

$page = @$_REQUEST['page']; // get the requested page
$limit = @$_REQUEST['rows']; // get how many rows we want to have into the grid
$sidx = @$_REQUEST['sidx']; // get index row - i.e. user click to sort
$sord = @$_REQUEST['sord']; // get the direction
if(!$sidx) $sidx =1; 
$act=Tools::esc($_REQUEST['act']);
$gr=@$_REQUEST['gr'];
$P=@$_REQUEST['P'];

switch ($act){

    case 'list':
        if(!$gr || $gr>2) {
            $r->fres_msg='gr not set';
            $r->fres=false;
            break;
        }
        if(!$P) {
            $r->fres_msg='P not set';
            $r->fres=false;
            break;
        }
        $d=$cc->fetchAll("SELECT cc_cat.P{$P}+'0' AS PVal FROM cc_cat INNER JOIN (cc_model INNER JOIN cc_brand ON cc_model.brand_id=cc_brand.brand_id) ON cc_cat.model_id=cc_model.model_id WHERE cc_cat.P{$P}!=0 AND cc_cat.gr='$gr' AND NOT cc_brand.LD AND NOT cc_model.LD AND NOT cc_cat.LD  GROUP BY PVal ORDER BY PVal");
        $PVal=array();

        foreach($d as $v)
            $PVal[$v['PVal']]=array('PVal'=>$v['PVal'],'extra'=>0);

        $a=implode(',',array_keys($PVal));
        if(count($PVal)){
            $d=$cc->fetchAll("SELECT cc_min_extra.PVal+'0' AS PVal, cc_min_extra.extra+'0' AS extra FROM cc_min_extra WHERE PVal IN ($a) AND P='{$P}' ORDER BY $sidx $sord");
            foreach($d as $v) $PVal[$v['PVal']]['extra']=$v['extra'];
        }

        $r->records=count($PVal);
        if( $r->records ) $total_pages = ceil($r->records/$limit); else $total_pages = 0;
        if ($page > $total_pages) $page=$total_pages;
        $start = $limit*$page - $limit; // do not put $limit*($page - 1)
        if($start<0) $start=0;
        $r->page = $page;
        $r->total = $total_pages;
        $i=0;
    //	$r->sql=$cc->sql_query;

        foreach($PVal as $v){
            $r->rows[$i]['id']=$v['PVal'];
            $r->rows[$i]['cell']=array($v['PVal'],$v['extra']);
            $i++;
        }
    break;


    case 'update':
        if(!$gr || $gr>2) {
            $r->fres_msg='gr not set';
            $r->fres=false;
            break;
        }
        if(!$P) {
            $r->fres_msg='P not set';
            $r->fres=false;
            break;
        }
        $PVal=(float)@$_REQUEST['id'];
        $oe=trim(@$_REQUEST['extra']);
        $extra=(float)str_replace(',','.',@$_REQUEST['extra']);
        $d=$cc->getOne("SELECT count(P) FROM cc_min_extra WHERE gr='$gr' AND P='$P' AND PVal='$PVal'");
        if($d[0]) $cc->query("UPDATE cc_min_extra SET extra='$extra' WHERE gr='$gr' AND P='$P' AND PVal='$PVal'");
        else $cc->query("INSERT INTO cc_min_extra (extra,gr,P,PVal) VALUES('$extra','$gr','$P','$PVal')");
        if(strcmp($oe,$extra)!==0) echo '0'; else echo '1';
        $cc->addCacheTask('prices',$gr);
        exit();
    break;

    default: $r->fres=false; $r->fres_msg='BAD ACT ID '.$act;
}

ajxEnd();