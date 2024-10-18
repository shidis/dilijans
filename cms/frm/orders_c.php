<? require_once '../auth.php';

$cp->frm['name']='orders_c';
$cp->frm['title']='Заказы '.Cfg::get('site_name');

$cp->checkPermissions();

$os=new App_Orders();

$os->initOrderStatesByUser();

$cUsers=CU::usersList(array('includeLD'=>1));
$drivers=CU::usersList(array('driversOnly'=>true));

if(isset(App_TFields::$fields['os_order']['ptype'])) $ptypeOn=1; else $ptypeOn=0;
if(isset(App_TFields::$fields['os_order']['method'])) $methodOn=1; else $methodOn=0;

CU::loadUData();

if($methodOn){
    $oStates=array(0=>array(),1=>array());
    foreach($os->_orderStates as $k=>$v){
        if(in_array(0,$v['method']))
            $oStates[0][$k]=$v;
        if(in_array(1,$v['method']))
            $oStates[1][$k]=$v;
            $oStates[1][$k]=$v;
    }
}else{
    $oStates=array(0=>$os->_orderStates);
}

if(@$_REQUEST['from_y']!='' && @$_REQUEST['to_y']!=''){

    $from_d=$_REQUEST['from_d']==''?'01':Tools::zeroFill($_REQUEST['from_d'],2);
    $from_m=$_REQUEST['from_m']==''?'01':Tools::zeroFill(@$_REQUEST['from_m'],2);
    $to_d=$_REQUEST['to_d']==''?'31':Tools::zeroFill($_REQUEST['to_d'],2);
    $to_m=$_REQUEST['to_m']==''?'12':Tools::zeroFill(@$_REQUEST['to_m'],2);
    $from=@$_REQUEST['from_y']."-$from_m-$from_d 00:00:00";
    $to=@$_REQUEST['to_y']."-$to_m-$to_d 23:59:59";

}

if(!empty($_REQUEST['rangeBy'])) {
    if($_REQUEST['rangeBy']=='deliveryDate' && !empty($os->adminCfg['delivery']['DBF_deliveryDate'])) $rangeBy='os_order.'.$os->adminCfg['delivery']['DBF_deliveryDate'];
    elseif($_REQUEST['rangeBy']=='orderDate') $rangeBy='os_order.dt_add';
}

if(@$_REQUEST['output']=='csv'){

    $os->ordersExportCSV(array(
        'from'=>$from,
        'to'=>$to,
        'rangeBy'=>@$rangeBy,
        'search'=>@$_REQUEST['search'],
        'searchItems'=>$_REQUEST['searchItems'],
        'state_id'=>@$_REQUEST['state_id'],
        'order_num'=>$_REQUEST['order_num'],
        'cUserId'=>@$_REQUEST['cUserId'],
        'sort'=>'os_order.order_num DESC',
        'beforeProceedTime'=>5
    ));
    exit();
}

if(@$_REQUEST['output']=='exportCSVDetail'){

    $os->ordersExportCSVDetail(array(
        'from'=>$from,
        'to'=>$to,
        'rangeBy'=>@$rangeBy,
        'mode'=>'detail',
        'search'=>@$_REQUEST['search'],
        'searchItems'=>$_REQUEST['searchItems'],
        'state_id'=>@$_REQUEST['state_id'],
        'order_num'=>$_REQUEST['order_num'],
        'cUserId'=>@$_REQUEST['cUserId'],
        'sort'=>'os_order.order_num DESC'
    ));
    exit();
}

$mango = false;
if($cp->isAllow('callLog')) {
    try {
        $mango = new Orders_Mango();
    } catch (Exception $e) {
        $mango = false;
    }
}


function array_cp1251($a)
{
    foreach($a as &$v) $v=@Tools::cp1251($v);
    return $a;
}
include('../struct.php');

cp_head();
cp_css();
cp_js();

cp_body();

$wrongStateOrders=array();

if(in_array(@$_GET['markup'],array('left','right'))) cp_title(true,true,false);

?>
    <style type="text/css">
        .ostate{
            line-height: 20px;
        }
        .state0go{
            padding: 5px 10px;
            font-weight: bold;
        }
        .orders-table{
            border-collapse: collapse;
        }
        .orders-table th {
            line-height: 17px;
            font-weight: normal;
            text-align: center;
            padding: 7px 4px;
        }

        .orders-table td{
            height: 30px;
            padding: 3px 10px;
            border: 1px solid #CCC;
        }
        .paginator{
            margin: 5px 0;
        }
        .paginator li{
            list-style: none;
            display: inline-block;
            margin-right: 15px;
            font-size: 14px;
            font-weight: bold;
        }
        .paginator li.active{
            font-size: 16px;
        }
        .reloadPage{
            background: #FFFFEE;
            margin-left: 15px;
        }
        .orders-table{
            position: relative;
        }
        .orders-table tbody.info{
            display: none;
            position: relative;
        }
        .orders-table tbody.info > tr > td{
            border: 1px dashed #999;
            padding-left: 40px;
            line-height: 17px;
        }
        .orders-table tbody.info .column1, .orders-table tbody.info .column2{
            display: inline-block;
            vertical-align: top;
            margin-right: 20px;
        }
        .orders-table tbody.info .calls{
            margin-bottom: 10px;
        }
        .orders-table tbody.info .calls fieldset{
            border-radius: 5px;
            border: 1px dashed #CCC;
        }
        .orders-table tbody.info .calls th{
            text-align: left;
            border-width: 0 0 1px 0;
            border-color: #808080;
            padding: 3px 5px;
            font-weight: bold;
        }
        .orders-table tbody.info .calls td{
            text-align: left;
            border-width: 0 0 1px 0;
            border-color: #EEE;
            padding: 1px 5px;
            height: auto;
        }
        .orders-table tbody.info td .slogmsg{
            margin-left: 10px;
            display: inline-block;
        }
        .orders-table tbody.info span.i{
            float: left;
            margin-left: -25px;
        }
        .settings{
            position: absolute; top:-27px; right: 15px
        }
        .settings a{
            background: url(/cms/img/settings23.png) no-repeat 0 0; padding-left: 31px; height: 23px; display: block; line-height: 22px; color: white; font-weight: bold
        }
        .suphint{
            font-style: normal;
            font-weight: normal;
            font-size: 10px;
        }

    </style>
<?

if(@$_REQUEST['from_y']!=''&& @$_REQUEST['to_y']!=''){


    $page=(int)abs(@$_REQUEST['page']);
    if(!$page) $page=1;
    $limit=$os->adminCfg['ordersListLimit'];

    $searchCriteria=array(
        'from'=>$from,
        'to'=>$to,
        'rangeBy'=>@$rangeBy,
        'search'=>@$_REQUEST['search'],
        'searchItems'=>$_REQUEST['searchItems'],
        'state_id'=>@$_REQUEST['state_id'],
        'order_num'=>$_REQUEST['order_num'],
        'cUserId'=>@$_REQUEST['cUserId'],
        'start'=>abs(($page-1)*$limit),
        'limit'=>$limit,
        'getPurchaseForPeriod'=>true
    );

    $ores=$os->ordersList($searchCriteria);

    ?>
    <script type="text/javascript">
        var searchCriteria=<?=json_encode($searchCriteria)?>;
    </script>

    <div class="settings"><a href="#"> настройки</a></div>

    <fieldset class="ui" style="background-color: <?=Data::get('cms_left_col_bg')?>; margin-bottom: 10px; padding: 5px 0">
        <legend class="ui">Статистика за выбранный период</legend>
        <div style="padding: 0px 15px; position: relative;">
            <button style="float: right" class="switchSB" title="включить/выключить фильтры"></button>
            <div style="float: right; width: 100px; overflow: hidden;">
                <button class="exportCSV ui-state-default ui-corner-all" style="background: url('/cms/img/docs/csv-24.png') no-repeat 50% 50%; width:30px; height: 30px; float: right"></button>
            </div>
            <strong>Заказов <font class="red"><?=$ores['total']?></font></strong>.
            <strong>Новых <font class="red"><?=$ores['newOrders']?></font></strong>.
            <strong title="без учета доставки">Выручка <font class="red"><?=Tools::nn($ores['proceeds'])?></font> р.</strong>
            <? if(isset($ores['profit'])){
                ?><strong title="Если для товарной позиции в каком либо заказе за выбранный период не указана закупка, то стоимость закупки принимется равной розничной стоимости. Т.е для получения верного значения прибыли, удостоверьтесь, что все поля закупа для всех заказов в выбранном периоде заполенены.">Прибыль <span class="red"><?=Tools::nn($ores['profit'])?></span> р.</strong><?
            }?>
            <input type="button" class="reloadPage" value="обновить страницу">
            <? if(!empty($os->_orderStates[0])){?>
                <input type="button" class="newOrder" value="+ новый заказ">
            <? }?>
        </div>

    </fieldset>


    <?

    if ($ores['total']){

        Url::parseUrl();
        $paginator=Tools::paginator(Url::$path,Url::$sq,$page,$ores['total'],$limit,'page',array(
            'active'=>	'<li class="active">{page}</li>',
            'noActive'=>'<li><a href="{url}">{page}</a></li>',
            'dots'=>	'<li>...</li>'
        ),35);
        $pagi='';
        foreach($paginator as $vv) $pagi.=$vv;

        ?><ul class="paginator"><?=$pagi?></ul><?

        $allowDelete=$cp->isAllow('orders.del');   ?>

        <form method="post" action="" name="frm">
        <input type="hidden" name="order_id" value="">
        <input type="hidden" name="act" value="">
        <table class="orders-table">
            <tr>
                <th>Дата заказа</th>
                <th>Номер заказа</th><?
                if($ptypeOn){
                    ?><th>Тип</th><?
                }
                if($methodOn){
                    ?><th>Оплата</th><?
                }
                ?><th>Клиент</th>
                <th>Адрес</th>
                <th>Сумма заказа, руб</th>
                <th>Состояние заказа</th>
                <th>Менеджер</th>
                <th>Кто оформил заказ</th><?
                if(!empty($os->adminCfg['delivery']['DBF_deliveryDate'])){
                    ?><th>Дата доставки</th><?
                }
                if($allowDelete){
                    ?><th>Удалить</th><?
                }
                ?></tr><?

            $l=0;
            $last_onum=0;
            $cols=9;
            while ($os->next()!=false){

                if($methodOn) $method=$os->qrow['method']; else $method=0;
                $l++;
                ?><tr order_id="<?=$os->qrow['order_id']?>" state_id="<?=$os->qrow['state_id']?>" method="<?=$method?>"><?
                ?><td nowrap align="left" class="infosw"><?=Tools::sdate($os->qrow['dt_add'],'-',true)?><br><?=Tools::stime($os->qrow['dt_add'])?></td><?
                ?><td align="center" title="order_id=<?=$os->qrow['order_id']?>"><a class="wscroll" id="wscroll<?=$os->qrow['order_id']?>_1" href="order_edit.php?order_id=<?=($os->qrow['order_id'])?>"><?=($os->qrow['order_num'])?></a></td><?
                if($ptypeOn){
                    ?><td align="center"><?=App_TFields::$fields['os_order']['ptype']['varList2'][$os->qrow['ptype']]?></td><?
                    $cols++;
                }
                if($methodOn){
                    ?><td align="center"><?=App_TFields::$fields['os_order']['method']['varList2'][$os->qrow['method']]?></td><?
                    $cols++;
                }
                ?><td align="left"><a class="olink wscroll" id="wscroll<?=$os->qrow['order_id']?>_2" href="order_edit.php?order_id=<?=($os->qrow['order_id'])?>"><?=Tools::unesc($os->qrow['name'])?></a></td><?
                ?><td align="left"><?
                $addr=Tools::unesc($os->qrow['city'].' '.$os->qrow['addr']);
                if(mb_strlen($addr)>50) $addr=mb_substr($addr,0,50).'...';
                if(@$os->qrow['source']==1) {
                    if(trim($addr)=='')
                        echo '<span title="заказ в один клик" class="red"><b>*1 click*</b></span> ';
                    else
                        echo $addr.' <span title="заказ в один клик">*1 click*</span>';
                } elseif(@$os->qrow['source']==2) {
                    if(trim($addr)=='')
                        echo '<span title="заявка на отсутствующий ранее товар" class="red"><b>*сообщить о поступлении*</b></span> ';
                    else
                        echo $addr.' <span title="заявка на отсутсвующий ранее товар">*СоП.*</span>';
                }else{
                    echo $addr;
                }

                ?></td><?
                ?><td align="center" nowrap><?
                    echo Tools::nn($os->qrow['cost']);
                    if(!empty($os->adminCfg['purchase']['suplrHinting']['oStates']) && empty(CU::$udata['oList_suplrSuggestOff']))
                        if(in_array($os->qrow['state_id'], $os->adminCfg['purchase']['suplrHinting']['oStates'])){
                            $sh=$os->suplrSuggest($os->qrow['order_id']);
                            if($sh!==false && !empty($sh)) {
                                $s='';
                                foreach ($sh as $v) {
                                    $suplrName = $v['suplrName'];
                                    $suplrName = explode(' ', $suplrName);
                                    $suplrName = array_slice($suplrName, 0, 2);
                                    foreach ($suplrName as $ks => $vs) {
                                        $vs = trim($vs);
                                        if (mb_strlen($vs) > 5) $suplrName[$ks] = mb_substr($vs, 0, 5) . '.';
                                    }
                                    $suplrName = implode(' ', $suplrName);
                                    if ($v['gr'] == 1) $s.= "<br>Ш: $suplrName"; else $s.= "<br>Д: $suplrName";
                                }
                                if(!empty($s)) echo "<i class='suphint' title=''>$s</i>";
                            }
                        }
                ?></td><?
                ?><td  class="ostate" align="center"><?
                if($os->qrow['state_id']==-1){
                    echo $os->_orderStates[-1]['label'];
                    if(isset($os->adminCfg['cancelReasons'])){
                        $reason=Tools::unesc($os->qrow[App_TFields::$fields['os_order']['cancelReason']['as']]);
                        if(preg_match("~^\[([a-z]+)\]$~iu", $reason, $re)){
                            echo '<br>'.@$os->adminCfg['cancelReasons'][$re[1]];
                        }elseif(!empty($reason)) echo "<br>".$reason;
                    }
                }elseif($os->qrow['state_id']==0 && !empty($oStates[$method][0]['next']) && !empty($oStates[$method][$oStates[$method][0]['next']])){
                    ?><input type="button" class="state0go" value="<?=$oStates[$method][$oStates[$method][0]['next']]['actLabel']?>"><?
                }else{

                    if(@CU::$udata['oList_hideStates']){

                        ?><b><?=$os->_orderStates[$os->qrow['state_id']]['label']?></b><?

                    }else {

                        ?><select class="chState"><?

                        if (!in_array($os->qrow['state_id'], array_keys($oStates[$method]))) {
                            $wrongStateOrders[] = $os->qrow['order_num'] . ' - ' . $os->_orderStates[$os->qrow['state_id']]['label'];
                            ?><option value="<?= $os->qrow['state_id'] ?>" selected><?= $os->_orderStates[$os->qrow['state_id']]['label'] ?></option><?
                        }

                        foreach ($oStates[$method] as $k => $v) if (empty($v['excludeFromDropList']) || $os->qrow['state_id'] == $k) if ($os->qrow['state_id'] == $k || in_array($os->qrow['state_id'], $v['allowFrom'])) {
                            if ($k == $os->qrow['state_id']) {
                                ?><option value="<?= $k ?>" selected<?= in_array($k, array(
                                    -1,
                                    -3
                                )) || !empty($v['excludeFromDropList']) ? ' disabled' : '' ?>><?= $v['label'] ?></option><?
                            } else {
                                ?><option value="<?= $k ?>"<?= in_array($k, array(
                                    -1,
                                    -3
                                )) || !empty($v['excludeFromDropList']) ? ' disabled' : '' ?>><?= $v['actLabel'] ?></option><?
                            }
                        }

                        ?></select><?

                    }

                }
                if($os->qrow['state_id']!=0){
                    ?><br><? echo Tools::sDateTime($os->qrow['dt_state']);
                }
                ?></td><?
                ?><td align="center"><?
                    if($os->qrow['cUserId']) echo @$cUsers[$os->qrow['cUserId']]['shortName']; else echo 'нет';
                ?></td><?
                ?><td align="center"><?
                if($os->qrow['createdBy']) {
                    echo @$cUsers[$os->qrow['createdBy']]['shortName'];
                } else echo 'клиент';
                ?></td><?
                if(!empty($os->adminCfg['delivery']['DBF_deliveryDate'])){
                    $dd=$os->qrow[$os->adminCfg['delivery']['DBF_deliveryDate']];
                    if($dd*1) $dd=Tools::sdate($dd); else $dd="&nbsp;";
                    ?><td nowrap><?=$dd?></td><?
                    $cols++;
                }
                if($allowDelete){
                    ?><td align="center"><a href="#" class="orderDel"><img src="../img/b_drop.png" border="0"></a></td><?
                    $cols++;
                }
                ?></tr><?

                if(trim($os->qrow['tech'])!='' || !empty($os->qrow['slogNum']) || $mango!==false){
                    ?><tbody class="info" slogNum="<?=$os->qrow['slogNum']?>" oid="<?=$os->qrow['order_id']?>" <?=trim($os->qrow['tech'])!=''?'info="1"':''?> <?=$mango!==false?' calls="1"':''?>><tr><td colspan="<?=$cols?>"><?=nl2br(Tools::unesc($os->qrow['tech']))?></td></tr></tbody><?
                }
            }
            ?></table>


        </form><?


        ?><ul class="paginator"><?=$pagi?></ul><?

    } else{
        note("По заданным критериям ничего не найдено");
    }
}else note('Задайте временной нтервал и фильтры для отбора заказов');

?>


    <script type="text/javascript">
        /*
         Notification.requestPermission( newMessage );

         function newMessage(permission) {
         if( permission != "granted" ) return false;
         var notify = new Notification("<?=strtoupper(str_replace('www.','',Cfg::get('site_url')))?>", {
         tag : "test",
         body : "Добро пожаловать, <?=CU::$fullName?>. Здесь будет отображаться важная информация.",
         icon : "/images/logo.png"
         });
         notify.onerror = function(){
         note('Необходимо разрешить отправку уведомлений');
         };
         };
         */

        var setup=<?=json_encode(array_merge(['adminCfg'=>$os->adminCfg],array(
        'oStatesGrouped'=>$oStates,
        'methodOn'=>$methodOn,
        'ordersExportCSVDetail'=>is_callable(array($os,'ordersExportCSVDetail'))?true:false,
        'ref'=>urlencode($_SERVER['REQUEST_URI'])
    )))?>;

        var checkDT='<?=Tools::dt()?>';
        var pingBotInt='<?=$pingBotInt=(int)Data::get('pingBotInterval')?>';
        var pingOrders=<?=(server_loc=='remote') && $pingBotInt?'true':'false'?>;


        <? if(!empty($wrongStateOrders)){?>
        $('.paginator').before('<? warn('Позвоните админу! Неверный статус у заказов: '.implode(', ',$wrongStateOrders))?>');
        <? }?>
    </script>

<?
cp_end();

