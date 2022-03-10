<?
include_once ('ajx_loader.php');

$cp->setFN('orders');
$cp->checkPermissions();

//sleep(2);


$os=new App_Orders();

$r->fres=true;
$r->fres_msg='';

$act=Tools::esc(@$_REQUEST['act']);

switch ($act){

    default: $r->fres=false; $r->fres_msg='BAD ACT ID '.$act; break;

    case 'ping':
        $dt=Tools::esc(@$_REQUEST['dt']);
        $d=$os->getOne("SELECT GREATEST(dt_add,dt_state) AS dt FROM os_order WHERE GREATEST(dt_add,dt_state) > '$dt' AND NOT LD AND state_id=0 LIMIT 1");
        if($d!==0) {
            $r->newOrds=1;
            $r->lastOrderDT=$d['dt'];
        } else {
            $r->newOrds=0;
            $r->lastOrderDT=0;
        }
        break;

    case 'changeState':
        $r->fres=call_user_func(array($os, @$os->orderStates[@$_REQUEST['newStateId']]['handler']), @$_REQUEST['order_id'], @$_REQUEST['newStateId']);
        $r->fres_msg=$os->strMsg();
        if(empty($r->fres_msg)) $r->fres_msg='Статус заказа изменен';
        $r->prevState=@$os->CHS_prevState;
        $r->reload=false;
        break;

    case 'orderDel':
        $os->ld('os_order','order_id',@$_REQUEST['order_id']);
        break;

    case 'newOrder';
        $rr=$os->createNewOrder();
        if($rr===false){
            $r->fres=false;
            $r->fres_msg="Возникла ошибка при создании заказа";
        } else
            $r->order_id=$rr;

        break;

    case 'getPurchaseForPeriod':
        $rr=$os->ordersList((array)@$_REQUEST['criteria']+array('mode'=>'getPurchaseForPeriod'));
        if($rr===false){
            $r->fres=false;
            $r->fres_msg=$os->fres_msg;
        } else $r->value=Tools::nn($rr);
        break;

    case 'slogByOrder':
        $rr=$os->getSLogs(array(
            'order_id'=>@$_REQUEST['order_id'],
            'order'=>'desc',
            'mode'=>'logs',
            'whereSLog'=>"TRIM(msg)!=''"
        ));
        if($rr===false){
            $r->fres=false;
            $r->fres_msg=$os->strMsg();
            break;
        } else{
            $r->slogHTML='';
            foreach($rr['data'] as $v){
                $r->slogHTML.="<p><b><small>".Tools::sdate($v['dt_added'])." / {$v['createdBy_shortName']}:</small></b><br><span class=\"slogmsg\">{$v['msg']}</span></p>";
            }
        }

        if($cp->isAllow('callLog')) {
            try {
                $mango = new Orders_Mango();
                $r->callLog=$mango->orderCallLog(@$_REQUEST['order_id']);
            } catch (Exception $e) {
                $mongo = false;
            }

        }

        break;


}

ajxEnd();