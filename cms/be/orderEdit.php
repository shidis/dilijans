<?
include_once ('ajx_loader.php');

$cp->setFN('order_edit');
$cp->checkPermissions();

//sleep(2);


$os=new App_Orders();

$r->fres=true;
$r->fres_msg='';

$act=Tools::esc(@$_REQUEST['act']);

switch ($act){

    default: $r->fres=false; $r->fres_msg='BAD ACT ID '.$act; break;

    case 'confirmNewOrder':
        $rr=$os->confirmNewOrder(@$_REQUEST['order_id']);
        if($rr===false){
            $r->fres=false;
            $r->fres_msg=$os->strMsg();
        }
        break;

    case 'cancelNewOrder':
        $rr=$os->cancelNewOrder(@$_REQUEST['order_id']);
        if($rr===false){
            $r->fres=false;
            $r->fres_msg=$os->strMsg();
        }
        break;

    case 'slogAddFile':
        $r->request=array(
            'REQUEST'=> $_REQUEST,
            'FILES'=> @$_FILES
        );
        $rr=$os->addOrderFile(array(
            'order_id'=>@$_REQUEST['order_id'],
            'uploadedFileFieldName'=>'file',
            'msg'=>@$_REQUEST['msg']
        ));
        if($rr===false){
            $r->fres=false;
            $r->fres_msg=$os->strMsg();
        }else $r->data=$rr;
        break;

    case 'slogDelFile':
        $rr=$os->modOrderFile(array(
            'id'=>@$_REQUEST['id'],
            'mode'=>'del'
        ));
        if($rr===false){
            $r->fres=false;
            $r->fres_msg=$os->strMsg();
        }else $r->data=$rr;
        break;

    case 'fileMsgPost':
        $rr=$os->modOrderFile(array(
            'id'=>@$_REQUEST['pk'],
            'mode'=>'edit',
            'msg'=>@$_REQUEST['value']
        ));
        if($rr===false){
            $r->fres=false;
            $r->fres_msg=$os->strMsg();
        }else $r->data=$rr;
        break;


    case 'slogAddLog':
        $rr=$os->modSLog(array(
            'order_id'=>@$_REQUEST['order_id'],
            'mode'=>'add',
            'msg'=>@$_REQUEST['msg']
        ));
        if($rr===false){
            $r->fres=false;
            $r->fres_msg=$os->strMsg();
        }else $r->data=$rr;
        break;

    case 'slogDelLog':
        $rr=$os->modSLog(array(
            'id'=>@$_REQUEST['id'],
            'mode'=>'del'
        ));
        if($rr===false){
            $r->fres=false;
            $r->fres_msg=$os->strMsg();
        }else $r->data=$rr;
        break;

    case 'slogMsgPost':
        $rr=$os->modSLog(array(
            'id'=>@$_REQUEST['pk'],
            'mode'=>'edit',
            'msg'=>@$_REQUEST['value']
        ));
        if($rr===false){
            $r->fres=false;
            $r->fres_msg=$os->strMsg();
        }else $r->data=$rr;
        break;

    case 'changeState':
        $newStateId=(int)@$_REQUEST['newStateId'];

        if(!empty($_REQUEST['data']))
            $r->fres=call_user_func(array($os, @$os->orderStates[$newStateId]['handler']), @$_REQUEST['order_id'], $newStateId, Tools::parseStr($_REQUEST['data']));
            else
                $r->fres=call_user_func(array($os, @$os->orderStates[$newStateId]['handler']), @$_REQUEST['order_id'], $newStateId);

        $r->fres_msg=$os->strMsg();
        $r->prevState=@$os->CHS_prevState;
        if($r->fres){
            $r->setup['cUserId']=@$os->CHS_cUserId;
            if($r->setup['cUserId']) {
                $r->mgrShortName=CU::$shortName;
                $r->mgrFullName=CU::$fullName;
            }
            $r->setup=array('stateId'=>$newStateId);
            $r->setup['editable']=true;
            $r->setup['notEditableState']=false;
            if(!$os->_orderStates[$newStateId]['editable']){
                $r->setup['notEditableState']=true;
                $r->setup['editable']=false;
            }
            $r->slogRow=$os->getSLogById($os->lastAddedSLogId);
        }

        break;

    case "dataEdit":
        $name=@$_REQUEST['field'];
        $value=$_REQUEST['newVal'];
        if($name=='af[deliveryDate]') $value=Tools::fdate($value);
        if($name=='af[suplrPaymentDate]') $value=Tools::fdate($value);
        if($name=='af[billDate]') $value=Tools::fdate($value);

        $r=$os->orderDataEdit(array(
            'order_id'=>@$_REQUEST['order_id'],
            'field'=>$name,
            'newVal'=>$value
        ));
        $r['fres_msg']=$os->fres_msg;
        if($r['fres']){
            $r['setup']=array();
            if($name=='af[method]') $r['setup']['method']=$value;
            if($name=='af[ptype]') $r['setup']['ptype']=$value;
        }
        $r['data']=$r;
        if(isset($r['pcost']) && isset($r['cost'])) {
            $r['margin']=Tools::nn($r['_margin']=$r['cost']-$r['pcost']);
            $r['pcost']=Tools::nn($r['pcost']);
        }
        if(isset($r['cost'])) $r['cost']=Tools::nn($r['cost']);
        break;

    case 'ping':
        /*
        fres - false - остановка бота / fres_msg - ошибка бота
        или
        fres = array() пар команда и жданные для команды
        fres_msg - для всплывающих сообщений
        */
        $prevDT=Tools::esc(@$_REQUEST['prevDT']);
        if(!MC::chk()) {
            $r->fres=false;
            $r->fres_msg=MC::strMsg();;
        }else{
            $r->fres=array();
            $r->lastHitDT=Tools::dt();
            $pingBotInt=(int)Data::get('pingBotInterval');
            /*
             * для каждого заказа редактируемого сохраняем в МС:
             * cmsActiveUsers_<userId> = > array('ordersInEdit'=>array(ids заказов))
             */
            $cUsers=CU::usersList();
            $order_id=(int)@$_REQUEST['order_id'];
            if($pingBotInt){
                $oe=MC::get('cmsActiveUsers_'.CU::$userId.MC::uid());
                if($oe==false){
                    MC::set('cmsActiveUsers_'.CU::$userId.MC::uid(), array('ordersInEdit'=>array($order_id=>1)), $pingBotInt*2);
                }else{
                    $oe['ordersInEdit'][$order_id]=1;
                    MC::set('cmsActiveUsers_'.CU::$userId.MC::uid(), $oe, $pingBotInt*2);
                }
                // проверяем соседей по заказу открытому
                $r->fres['otherUsers']=array();
                foreach($cUsers as $k=>$v)
                    if($k!=CU::$userId) {
                        $oe=MC::get('cmsActiveUsers_'.$k.MC::uid());
                        if($oe!==false && !empty($oe['ordersInEdit']) && isset($oe['ordersInEdit'][$order_id]))
                            $r->fres['otherUsers'][$k]=array('shortName'=>$v['shortName']);
                    }
                if(empty($r->fres['otherUsers'])) unset($r->fres['otherUsers']);
            }
        }
        break;

    case 'item':
        switch (@$_REQUEST['name']){
            default:
                $name=@$_REQUEST['name'];
                $value=$_REQUEST['value'];
                if($name=='af[reserveDate]') $value=Tools::fdate($value);

                $rr=$os->orderSpec(array(
                    'mode'=>'edit',
                    'order_id'=>@$_REQUEST['order_id'],
                    'item_id'=>@$_REQUEST['pk'],
                    'field'=>$name,
                    'newVal'=>$value
                ));
                if(!$rr['fres']){
                    $r->fres=false;
                    $r->fres_msg=$os->fres_msg;
                }else{
                    if(isset($rr['pcost']) && isset($rr['cost'])) {
                        $r->pcost=Tools::nn($rr['pcost']);
                        $r->margin=Tools::nn($r->_margin=$rr['cost']-$rr['pcost']);
                    }
                    if(isset($rr['cost'])) $r->cost=Tools::nn($rr['cost']);
                    $r->newVal=$rr['newVal'];
                    if($name=='af[reserveDate]') $r->newVal=Tools::sdate($r->newVal);
                    if($_REQUEST['name']=='price') $r->newVal*=1;
                    if(!empty($os->adminCfg['purchase']) && $_REQUEST['name']=="af[{$os->adminCfg['purchase']['DBF_pprice']}]") $r->newVal*=1;
                }
                break;


            case 'del':
                $rr=$os->orderSpec(array(
                    'mode'=>'del',
                    'order_id'=>@$_REQUEST['order_id'],
                    'item_id'=>@$_REQUEST['pk']
                ));
                if($rr['fres']){
                    $r->cost=$rr['cost'];
                    if(isset($rr['pcost'])) {
                        $r->pcost=Tools::nn($rr['pcost']);
                        $r->margin=Tools::nn($r->_margin=$rr['cost']-$rr['pcost']);
                    }
                }else{
                    $r->fres=false;
                    $r->fres_msg=$os->fres_msg;
                }
                break;


        }

        break;

    case 'dop':
        switch (@$_REQUEST['name']){

            default:
                $rr=$os->orderSpec(array(
                    'mode'=>'edit',
                    'order_id'=>@$_REQUEST['order_id'],
                    'dop_id'=>@$_REQUEST['pk'],
                    'field'=>$_REQUEST['name'],
                    'newVal'=>@$_REQUEST['value']
                ));
                if(!$rr['fres']){
                    $r->fres=false;
                    $r->fres_msg=$os->fres_msg;
                }else{
                    if(isset($rr['cost'])) $r->cost=Tools::nn($rr['cost']);
                    if(isset($rr['pcost'])) {
                        $r->pcost=Tools::nn($rr['pcost']);
                        $r->margin=Tools::nn($r->_margin=$rr['cost']-$rr['pcost']);
                    }
                    $r->newVal=$rr['newVal'];
                    if($_REQUEST['name']=='price') $r->newVal*=1;
                    if(@$os->adminCfg['purchase']['dopPPriceEnabled'] && $_REQUEST['name']=="af[{$os->adminCfg['purchase']['DBF_dop_pprice']}]") $r->newVal*=1;
                }
                break;

            case 'del':
                $rr=$os->orderSpec(array(
                    'mode'=>'del',
                    'order_id'=>@$_REQUEST['order_id'],
                    'dop_id'=>@$_REQUEST['pk']
                ));
                if($rr['fres']){
                    if(isset($rr['pcost'])) {
                        $r->pcost=Tools::nn($rr['pcost']);
                        $r->margin=Tools::nn($r->_margin=$rr['cost']-$rr['pcost']);
                    }
                    $r->cost=Tools::nn($rr['cost']);
                }else{
                    $r->fres=false;
                    $r->fres_msg=$os->fres_msg;
                }
                break;
        }

        break;



    case 'findCatById':
        $cc=new CC_Base();
        $cat_id=(int)@$_REQUEST['value'];
        $cc->que('cat_by_id',$cat_id,1);
        if(false!==$cc->next()){
            $r->gr=$cc->qrow['gr'];
            $qr=$cc->qrow;
            if($r->gr==1){
                if($qr['P6']) $rad='R'; else $rad='ZR';
                $r->name=Tools::html(trim(Tools::cutDoubleSpaces("Шина {$qr['bname']} {$qr['name']} {$qr['msuffix']} {$qr['P3']}/{$qr['P2']} {$rad}{$qr['P1']} {$qr['P7']} {$qr['suffix']}")));
            }else{
                if($qr['P1']) $et="ET{$qr['P1']}"; else $et='';
                if($qr['P3']) $dia="DIA {$qr['P3']}"; else $dia='';
                if($qr['replica']) $rep='для'; else $rep='';
                $r->name=Tools::html(trim(Tools::cutDoubleSpaces("Диски $rep {$qr['bname']} {$qr['name']} {$qr['msuffix']} {$qr['P2']}x{$qr['P5']} {$qr['P4']}/{$qr['P6']} $et $dia {$qr['suffix']}")));
            }
            $r->price=$qr['cprice'];
            $r->sc=$qr['sc'];

        }else{
            $r->fres=false;
            $r->fres_msg='Не найден код '.$cat_id;
        }

        break;

    case 'itemPost':
        $cc=new CC_Base();
        $frm=Tools::parseStr(@$_REQUEST['frm']);
        $cat_id=(int)@$frm['cat_id'];
        $cc->que('cat_by_id',$cat_id,1);
        if(false!==$cc->next()){

            $rr=$os->orderSpec(array(
                'mode'=>'add',
                'order_id'=>@$frm['order_id'],
                'item_id'=>1,
                'row'=>array(
                    'gr'=>$cc->qrow['gr'],
                    'cat_id'=>$cc->qrow['cat_id'],
                    'name'=>@$frm['name'],
                    'price'=>@$frm['price'],
                    'amount'=>@$frm['am']
                )
            ));

            if(!$rr['fres']){
                $r->fres=false;
                $r->fres_msg=$os->fres_msg;
            }else{
                $r->type='item';
                if(isset($rr['pcost'])) {
                    $r->pcost=Tools::nn($rr['pcost']);
                    $r->margin=Tools::nn($r->_margin=$rr['cost']-$rr['pcost']);
                }
                $r->cost=Tools::nn($rr['cost']);
                $r->data=array(
                    'item_id'=>$rr['newRow']['item_id'],
                    'cat_id'=>$rr['newRow']['cat_id'],
                    'name'=>$rr['newRow']['name'],
                    'price'=>$rr['newRow']['price']*1,
                    'am'=>$rr['newRow']['amount']*1
                );
            }
        }else{
            $rr=$os->orderSpec(array(
                'mode'=>'add',
                'order_id'=>@$frm['order_id'],
                'dop_id'=>1,
                'row'=>array(
                    'name'=>@$frm['name'],
                    'price'=>@$frm['price'],
                    'amount'=>@$frm['am']
                )
            ));

            if(!$rr['fres']){
                $r->fres=false;
                $r->fres_msg=$os->fres_msg;
            }else{
                $r->type='dop';
                if(isset($rr['pcost'])) {
                    $r->pcost=Tools::nn($rr['pcost']);
                    $r->margin=Tools::nn($r->_margin=$rr['cost']-$rr['pcost']);
                }
                $r->cost=Tools::nn($rr['cost']);
                $r->data=array(
                    'dop_id'=>$rr['newRow']['dop_id'],
                    'name'=>$rr['newRow']['name'],
                    'price'=>$rr['newRow']['price']*1,
                    'am'=>$rr['newRow']['amount']*1
                );
            }

        }
        break;

    case 'suplrList':
        if(!@$os->adminCfg['purchase']['suplrSelectEnabled']){
            $r->fres=false;
            $r->fres_msg='запрещенная функция';
            break;
        }
        $cat_id=(int)@$_REQUEST['cat_id'];
        if(empty($cat_id)) {
            $r->fres=false;
            $r->fres_msg='Нет CAT_ID';
            break;
        }
        $d=$os->fetchAll("SELECT cc_suplr.suplr_id, cc_cat_sc.sc, cc_cat_sc.price1, cc_cat_sc.price2, cc_cat_sc.price3, cc_suplr.name, cc_cat_sc.dt_added, cc_cat_sc.dt_upd FROM cc_cat_sc INNER JOIN cc_suplr ON cc_cat_sc.suplr_id=cc_suplr.suplr_id WHERE cat_id='{$cat_id}' ORDER BY cc_cat_sc.{$os->adminCfg['purchase']['DBF_suplrPrice']} ASC, cc_cat_sc.sc",MYSQLI_ASSOC);

        $ids=[];
        foreach($d as $k=>$v){
            $d[$k]=array(
                'suplr_id'=>$v['suplr_id'],
                'name'=>Tools::html($v['name']),
                'price'=>$v[$os->adminCfg['purchase']['DBF_suplrPrice']]*1,
                '_price'=>Tools::nn($v[$os->adminCfg['purchase']['DBF_suplrPrice']]),
                'sc'=>$v['sc'],
                'future'=>[],
                'dateUpdate'=>Tools::sdate($v['dt_upd']=='0000-00-00 00:00:00'?$v['dt_added']:$v['dt_upd'])
            );
            $ids[$v['suplr_id']]=$k;
        }


        if(!empty($d))
            if(!empty($os->adminCfg['purchase']['futureSuplr']) && !empty($os->adminCfg['purchase']['futureSuplr']['deliveringStateId']) && !empty($os->adminCfg['delivery']['DBF_deliveryDate'])) {
                $ds = $os->futureSuplr([
                    'stateId'=>$os->adminCfg['purchase']['futureSuplr']['deliveringStateId'],
                    'days'=>$os->adminCfg['purchase']['futureSuplr']['days'],
                    'suplrIds'=>array_keys($ids)
                ]);
                if($ds!==false)
                    foreach($ds as $v){
                        $d[$ids[$v['suplrId']]]['future'][$v['dayNo']]=[
                            'deliveryDate'=>Tools::sdate($v['deliveryDate']),
                            'itemsNum'=>$v['itemsNum']
                        ];
                    }
            }


        $r->suplrs=$d;

        break;

    case 'exportMailBody':
        $order_id=(int)@$_REQUEST['order_id'];
        $os->que('order_by_id',$order_id);
        if($os->next()===false){
            $r->fres=false;
            $r->fres_msg="Заказ $order_id не найден";
            break;
        }
        switch(@$_REQUEST['mode']){
            case 'exportMailClient':
                $emailTo=$os->qrow['email'];
                if(!Tools::emailValid($emailTo)){
                    $r->fres=false;
                    $r->fres_msg='Не валидный email клиента : '.$emailTo;
                    break 2;
                }
                CU::loadUData();
                $rr=$os->exportEmailInBody($order_id, @$_REQUEST['doc'], $emailTo, @$_REQUEST['subject'], @CU::$udata['clientMailSign']);
                if(!$rr){
                    $r->fres=false;
                    $r->fres_msg=$os->fres_msg;
                }
                break;
            case 'exportMailDriver':
                $cUsers=CU::usersList();
                $emailTo=$cUsers[$os->qrow[$os->adminCfg['drivers']['DBF_driverId']]]['email'];
                if(!Tools::emailValid($emailTo)){
                    $r->fres=false;
                    $r->fres_msg='Не валидный email водителя : '.$emailTo;
                    break 2;
                }
                $rr=$os->exportEmailInBody($order_id, @$_REQUEST['doc'], $emailTo, @$_REQUEST['subject']);
                if(!$rr){
                    $r->fres=false;
                    $r->fres_msg=$os->fres_msg;
                }
                break;
            case 'exportMailMgr':
                $emailTo=CU::$email;
                if(!Tools::emailValid($emailTo)){
                    $r->fres=false;
                    $r->fres_msg='Не валидный email получателя : '.$emailTo;
                    break 2;
                }
                $rr=$os->exportEmailInBody($order_id, @$_REQUEST['doc'], $emailTo, @$_REQUEST['subject']);
                if(!$rr){
                    $r->fres=false;
                    $r->fres_msg=$os->fres_msg;
                }
                break;
            default:
                $r->fres=false;
                $r->fres_msg='Режим отправки не ясен :: '.@$_REQUEST['mode'];
        }

        break;


    case 'exportMailMultiple':
        $order_id=(int)@$_REQUEST['order_id'];
        $os->que('order_by_id',$order_id);
        if($os->next()===false){
            $r->fres=false;
            $r->fres_msg="Заказ $order_id не найден";
            break;
        }
        switch(@$_REQUEST['mode']){
            case 'exportMailClient':
                $emailTo=$os->qrow['email'];
                if(!Tools::emailValid($emailTo)){
                    $r->fres=false;
                    $r->fres_msg='Не валидный email клиента : '.$emailTo;
                    break 2;
                }
                $rr=$os->exportEmailMulti($order_id,@$_REQUEST['docs'],$emailTo,@$_REQUEST['subject'],@$_REQUEST['body'],false);
                if(!$rr){
                    $r->fres=false;
                    $r->fres_msg=$os->fres_msg;
                }
                break;
            case 'exportMailDriver':
                $cUsers=CU::usersList();
                $emailTo=$cUsers[$os->qrow[$os->adminCfg['drivers']['DBF_driverId']]]['email'];
                if(!Tools::emailValid($emailTo)){
                    $r->fres=false;
                    $r->fres_msg='Не валидный email водителя : '.$emailTo;
                    break 2;
                }
                $rr=$os->exportEmailMulti($order_id,@$_REQUEST['docs'],$emailTo,@$_REQUEST['subject'],@$_REQUEST['body'],false);
                if(!$rr){
                    $r->fres=false;
                    $r->fres_msg=$os->fres_msg;
                }
                break;
            case 'exportMailMgr':
                $emailTo=CU::$email;
                if(!Tools::emailValid($emailTo)){
                    $r->fres=false;
                    $r->fres_msg='Не валидный email получателя : '.$emailTo;
                    break 2;
                }
                $rr=$os->exportEmailMulti($order_id,@$_REQUEST['docs'],$emailTo,@$_REQUEST['subject'],@$_REQUEST['body'],false);
                if(!$rr){
                    $r->fres=false;
                    $r->fres_msg=$os->fres_msg;
                }
                break;
            default:
                $r->fres=false;
                $r->fres_msg='Режим отправки не ясен :: '.@$_REQUEST['mode'];
        }

        break;
}

ajxEnd();
