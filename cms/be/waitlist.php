<?
include_once ('ajx_loader.php');

$cp->setFN('waitlist');
$cp->checkPermissions();

//sleep(1);


$r->fres=true;
$r->fres_msg='';

$act=Tools::esc(@$_REQUEST['act']);

switch ($act){

    case 'items':
        if(@$_REQUEST['sortBy']==2) $sortBy='dt_added ASC'; else $sortBy='dt_added DESC';
        $state=@$_REQUEST['state'];

        if($state==1) {// актуальные по сроку
            $actual = 1;
            $noticed = [-1];
        }elseif($state==2){//с сделанным уведомлением
            $actual=0;
            $noticed=1;
        }elseif($state==3){//с сделаными отсюда заказами
            $actual=0;
            $noticed=2;
        }elseif($state==4){//просроченные
            $actual=-1;
            $noticed=-1;
        }else{ // все
            $actual = 0;
            $noticed = [];
        }
        $page=(int)@$_REQUEST['page']; // первая страница ==1
        $rr=Orders_WaitList::olist([
            'actual'=>$actual,
            'noticed'=>$noticed,
            'sortBy'=>$sortBy,
            'gr'=>@$_REQUEST['gr'],
            'limit'=>$limit=(int)$_REQUEST['limit'],
            'start'=>($page-1)*$limit
            ]);
        if($rr===false) {
            $r->fres = false;
            $r->fres_msg = Orders_WaitList::strMsg();
        } else {
            $r->data = $rr['data'];
            $r->total = $rr['total'];
            $r->pages=ceil($r->total/$limit);
            $r->sql=$rr['sql'];
        }
        break;

    case 'createOrder':
        $rr=Orders_WaitList::createOrder((int)@$_REQUEST['wlID']);
        if($rr===false){
            $r->fres=false;
            $r->fres_msg=Orders_WaitList::strMsg();
        }else{
            $r->order_id=$rr['order_id'];
            $r->order_num=$rr['order_num'];
        }
        break;

    case 'checkOrder':
        $db=new DB();
        $order_id=(int)@$_REQUEST['order_id'];
        $wlID=(int)@$_REQUEST['wlID'];
        $r->state='not_saved';
        $d=$db->getOne("SELECT LD FROM os_order WHERE order_id=$order_id");
        if($d!==0){
            if($d[0]==0) $r->state='saved'; else $r->LD=$d[0];
        }else{
            $r->state='deleted';
            $db->query("UPDATE os_waitList SET noticed=0, comment='' WHERE id=$wlID");
        }
        break;

    default: $r->fres=false; $r->fres_msg='BAD ACT_CASE '.$act;
}

ajxEnd();