<?
include_once ('ajx_loader.php');

$cp->setFN('smsSender');
$cp->checkPermissions();

//sleep(1);


$sms=SMS_Reactor::factory();

$r->fres=true;
$r->fres_msg='';

$act=Tools::esc(@$_REQUEST['act']);

switch ($act){

    default:
        $r->fres=false;
        $r->fres_msg='BAD ACT ID '.$act;
        break;

    case 'balance':
        $rr=$sms->balance();
        if($rr['status']){
            $r->balance=$rr['balance'];
        }else{
            $r->fres=false;
            $r->fres_msg=$rr['statusMsg'];
        }
    break;

    case 'send':
        parse_str(@$_REQUEST['f'],$f);
        $f['dest']='7'.preg_replace("~[^0-9]~u",'',$f['dest']);
        $rr=$sms->send($f['source'],$f['dest'],$f['msg']);
        $r->fres_msg=$rr['statusMsg'];
        $r->status=$rr['status'];
        if(!$rr['status']){
            $r->fres=false;
        }else{
            $r->msgId=$rr['msgId'];
        }
        break;

    case 'pingResponse':
        $rr=$sms->check(@$_REQUEST['msgId']);
        $r->fres_msg=$rr['statusMsg'];
        $r->status=$rr['status'];
        if(!$rr['status']){
            $r->fres=false;
        }
        break;

}

ajxEnd();