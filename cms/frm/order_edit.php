<? require_once '../auth.php';
include('../struct.php');

$cp->frm['name']='order_edit';

$os=new App_Orders();
$cc=new CC_Ctrl();

$order_id=(int)@$_REQUEST['order_id'];

$os->que('order_by_id',$order_id);
$os->next();
$order=$os->qrow;
$order_id=@(int)$order['order_id'];

$cp->frm['title']='Редактирование заказа '.$order['order_num'];

$os->initOrderStatesByUser();

$cp->checkPermissions();

CU::loadUData();

cp_head();
cp_css();
cp_js();

cp_body();
cp_title(true,true,false);

if(!MC::chk()){
    warn(MC::strMsg());
    cp_end();
    exit();
}

if($order_id){

    if(isset(App_TFields::$fields['os_order']['ptype'])) $ptypeOn=true; else $ptypeOn=false;
    if(isset(App_TFields::$fields['os_order']['method'])) {
        $methodOn=true;
        $order['method']=$os->qrow['method'];
    } else {
        $methodOn=false;
        $order['method']=0;
    }

    if($methodOn){
        $oStates=array(0=>array(),1=>array());
        foreach($os->_orderStates as $k=>$v){
            if(in_array(0,$v['method']))
                $oStates[0][$k]=$v;
            if(in_array(1,$v['method']))
                $oStates[1][$k]=$v;
        }
    }else{
        $oStates=array(0=>$os->_orderStates);
    }

    $items=$os->que('item_list',$order_id);
    $dops=$os->listDOP($order_id, 'all');

    $cusers=CU::usersList(array('includeLD'=>1));
    $drivers=CU::usersList(array('driversOnly'=>true));

    $editable=true;
    $notEditableState=$notEditableUser=false;
    if(!@$oStates[$order['method']][$order['state_id']]['editable']){
        $notEditableState=true;
        $editable=false;
    }else
        if(@$oStates[$order['method']][$order['state_id']]['isolatedChanges'] && CU::$userId!=$order['cUserId']){
            $notEditableUser=true;
            $editable=false;
        }

    $addCol=0;
    if(!empty($os->adminCfg['reservation'])){
        $addCol+=3;
        $suplrs=$cc->suplrList(array());
    }

    if(!empty($os->adminCfg['purchase'])){
        $addCol+=1;
    }

	if($order['DeliveryType'] != ''){
		Data::get(Tools::esc(Tools::stripTags($order['DeliveryType'])));
		if(isset(Data::$current['comment']) AND isset(Data::$current['V'])){
			$order['DeliveryType'] = Data::$current['comment'].": <b>".Data::$current['V']." руб</b>";
		}else{
			$order['DeliveryType'] = Tools::esc(Tools::stripTags($order['DeliveryType']));
		}
	}


    ?>
    <style type="text/css">
        tr.af input, tr.af textarea{

            width:100%;
        }
        tr.af input[type=checkbox]{
            width:auto;
        }
        tr.af textarea{
            height:60px;
        }
        tbody.odata td, tbody.odata th{
            border-collapse: collapse;
            border-bottom: 1px solid #CCC;
        }
        tbody.odata th{
            text-align: right;
            padding: 8px 10px 8px;
        }
        tbody.odata td{
            font-size: 1.2em;
        }
        tbody.off{
            display: none;
        }
        #oItog{
            font-size: 1.1em;
            font-weight: bold;
            color: red;
        }
        #items .iprice, #dops .dprice, #items .ipprice, #dops .dpprice {
            width:60px;
            text-align: center;
        }
        #items .iam, #dops .dam{
            width: 30px;
            text-align: center;
        }
        #items .iname, #dops .dname {
            width: 100%;
        }
        #items .ireserveNum{
            width: 150px;
            text-align: center;
        }
        #items .isuplrId{
            width: 170px;
        }
        #items .ireserveDate{
            width: 80px;
            text-align: center;
        }
        .xe-items-name{
            width: 400px;
        }
        #item-add .ai-cat_id{
            width: 80px;
            text-align: center;
        }
        #item-add .ai-name{
            width: 99%;
        }
        #item-add .ai-price{
            width: 60px;
            text-align: center;
        }
        #item-add .ai-am{
            width: 30px;
            text-align: center;
        }
        #item-add .ai-am{
            width: 30px;
        }
        #items .suplrSel{
            height: 25px;
            vertical-align: middle;
        }
        #suplrListDlg table{
            border-collapse: collapse;
        }
        #suplrListDlg table td{
            border-top: 1px solid #CCC;
            padding: 4px 5px;
        }
        input.back{
            padding: 5px 10px 5px 30px;
            background: url('/cms/img/back.png') no-repeat 5px 50%;
        }
        input.confirmNewOrder{
            background:#33CC33 !important;
            margin-right: 20px;
            display: none;
            color: white;
        }
        input.cancelNewOrder{
            background:#FF3333 !important;
            margin-right: 20px;
            display: none;
            color: white;
        }
        #itogs table td{
            padding: 6px 20px;
            font-weight: bold;
        }
        #itogs table th{
            text-align: left;
        }
        .settings{
            position: absolute; top:-27px; right: 15px
        }
        .settings a{
            background: url(/cms/img/settings23.png) no-repeat 0 0; padding-left: 31px; height: 23px; display: block; line-height: 22px; color: white; font-weight: bold
        }
    </style>

    <div class="settings"><a href="#"> настройки</a></div>
<!--
    <div class="ui-widget">
        <div class="ui-state-highlight ui-corner-all" style="padding: 0pt 0.7em;">
            <p><span class="ui-icon ui-icon-info" style="float: left; margin-right: 0.3em;"></span><b><i>Новое.</i></b> Отправляются СМС в помент закрытия заказа. Формат сообщения: &quot;Ваш заказ № 00000 отправлен #TK# ТТН #TTN#&quot;.</p>НЕ ВКЛЮЧАЙТЕ В НАЗВАНИЕ ТК И ТТН лишней информации, чтобы длина СМС была минимальной.</p><p>ТК берется из поля заказа &quot;Транспортная компания&quot;. ТТН из поля &quot;Номер ТТН&quot;. В поле ТТН не публикуйте название ТК - будет дублирование информации в СМС.</p>
        </div>
    </div>
-->

    <div id="notifArea" style="margin: 10px 0"></div>

    <script type="text/javascript">
        <?

        $setup=array_merge([],array(
            'ptypeOn'=>$ptypeOn,
            'methodOn'=>$methodOn,
            'stateId'=>$order['state_id'],
            'cUserId'=>$order['cUserId'],
            'loggedUserId'=>CU::$userId,
            'notEditableState'=>$notEditableState,
            'notEditableUser'=>$notEditableUser,
            'editable'=>$editable,
            'oStatesGrouped'=>$oStates,
            'order_id'=>$order['order_id'],
            'ref'=>@$_REQUEST['ref'],
            'LD'=>$order['LD'],
            'adminCfg'=>$os->adminCfg,
            'docCfg'=>$os->docCfg
        ));

        if($ptypeOn) {
            $setup['ptype_varList']=App_TFields::$fields['os_order']['ptype']['varList'];
            $setup['ptype']=$order['ptype'];
        }
        if($methodOn) $setup['method']=$order['method'];


        $d=$os->fetchAll("SELECT name FROM os_dop WHERE NOT LD GROUP BY name ORDER BY name");
        $setup['dopNames']=array();
        foreach($d as $v){
            $setup['dopNames'][]=Tools::html($v['name']);
        }

        if(isset(App_TFields::$fields['os_order']['carrier_co'])){
            $d=$os->fetchAll("SELECT carrier_co FROM os_order WHERE NOT LD GROUP BY carrier_co ORDER BY carrier_co");
            $setup['carrier_co_names']=array();
            foreach($d as $v){
                $setup['carrier_co_names'][]=Tools::html($v['carrier_co']);
            }
        }

        $setup['slog']=$os->getSLogs(array('order_id'=>$order_id, 'order'=>'desc'));

        $setup['marginAlert']=Data::get('mgr_marginAlert');

        ?>

        var setup=<?=json_encode($setup)?>;


        var checkDT='<?=Tools::dt()?>';
        var pingBotInt='<?=$pingBotInt=(int)Data::get('pingBotInterval')?>';
        var pingOrders=<?=(server_loc=='remote') && $pingBotInt?'true':'false'?>;

        //if(!pingOrders) note('Ping Bot Off');


    </script>

    <input type="hidden" name="act" value="-1">

    <? if(!empty($_GET['ref'])){?>
        <input type="button" value="Вернуться назад" class="back">
    <? } else {?>
        <input type="button" value="Закрыть окно" class="close-win" style="display: none">
    <? }?>
    <input type="button" value="Подтвердить заказ" class="confirmNewOrder">
    <input type="button" value="Отменить заказ" class="cancelNewOrder">

    <style type="text/css">
        #slog{
            position:absolute;
            top:-60px;
            right: 0;
            width: 250px;
        }
        .slog-active{
            width: 20% !important;
        }
        .edit_area-active{
            padding-right: 21%
        }
        .slog-active .slog-box{
            height: auto;
        }
        .slog-box{
            border: 1px solid #CCC;
            border-radius: 5px;
            box-shadow:  3px 3px 10px rgba(0,0,0,0.7);
            background: #FFFFFF;
            height: 35px;
            margin: 20px 20px 20px 0;
            padding: 10px;
            overflow: hidden;
        }
        .slog-box .toggle{
            padding: 5px 10px;
            background: #DBEAF9;
            border-radius: 5px;
            overflow: hidden;
        }
        .slog-box .toggle button{
            float: right;
            overflow: hidden;
        }
        .slog-box .ctrl{
            margin: 10px 0;
            border-bottom: 1px dashed #CCC;
            padding-bottom: 10px;
        }
        .slog-box #slogAddMsg{
            width: 95%;
            height: 20px;
            margin-bottom: 6px;
        }
        .slog-box .ctrl button{
            margin-right: 10px;
        }
        .slog-box .data .row{
            margin: 5px 0;
            padding: 10px;
            background: #EEE;
            border-radius: 5px;
        }
        .slog-box .data .row .tit{
            font-weight: bold;
            margin-bottom: 5px;
            overflow: hidden;
            font-size: 0.9em;

        }
        .slog-box .data .row .tit em{
            font-style: normal;
            display: inline-block;
            float: right;
        }
        .slog-box .data .row .state{
            margin-bottom: 5px;
            font-size: 0.8em;
            font-weight: bold;
            color: #990000;
        }
        .slog-box .data .row .b{
            margin-top: 10px;;
        }
        .slog-box .data .row .b .del{
            float: right;
            padding-left: 20px;
            background: url(/cms/img/delete.gif) no-repeat 100% 50%;
            display: inline-block;
            width: 16px;
            height: 16px;
            cursor: pointer;
        }
        .slog-box .data .row .msg{
            line-height: 19px;
            white-space: normal;
        }
        #slogAddFilesDlg .area{
            border-radius: 6px;
            border: 3px #EEE dashed;
            padding: 20px 0 0 0;
            width: 99%;
            height: 260px;
            text-align: center;
            font-weight: bold;
            font-size: 12px;
            color: #AAA;
            position: relative;
            overflow: hidden;
        }
        .slog-box .data .row .file{
            padding: 3px 15px 3px 15px;
            border-radius: 6px;
            border: 1px solid #BBB;
            background: #0099CC;
            color: #FFFFFF;
            font-size: 10px;
            display: inline-block;
            cursor: pointer;
        }
        #slogAddFilesDlg .area .file{
            width: 110px;
            height: 30px;
            padding: 10px;
            border-radius: 6px;
            border: 2px dashed #BBB;
            float: left;
            margin: 10px 10px 0 10px;
            color: #555;
            font-size: 10px;
        }

        .future3{
            height: 12px;
            overflow: hidden;
            display: inline-block;
            vertical-align: middle;
        }
        .future3 > i{
            width: 12px;
            height: 12px;
            border-right: 1px solid #AAA;
            margin-right: 1px;;
            display: inline-block;
        }
        .future3 > i.c0{
            background: #CCC;
        }
        .future3 > i.c1{
            background: #00AA00;
        }

        .pmargin-alert{
            color: white;
            font-weight: bold;
            background: red;
            border: 1px dotted white;
            padding: 4px 10px;
            border-radius: 4px;
            text-shadow: #333 1px 1px 1px;
        }

    </style>

    <div class="edit_area" style="margin:10px 0; overflow: visible; position: relative;">

    <script id="slogTypeLogProtected" type="text/x-jsrender">
                <div class="row" slog_id="{{:id}}">
                    <div class="tit">
                        <i>{{:dt_added}}</i>
                        <em>{{:createdBy_shortName}}</em>
                    </div>
                    <div class="state">{{:new_state}}</div>
                    <div class="b"><span class="msg">{{:msg}}</span></div>
                </div>
            </script>

    <script id="slogTypeLogEditable" type="text/x-jsrender">
                <div class="row logEditable" slog_id="{{:id}}">
                    <div class="tit">
                        <i>{{:dt_added}}</i>
                        <em>{{:createdBy_shortName}}</em>
                    </div>
                    <div class="state">{{:new_state}}</div>
                    <div class="b"><span class="del"></span><span class="msg">{{:msg}}</span></div>
                </div>
            </script>

    <script id="slogTypeFileEditable" type="text/x-jsrender">
                <div class="row fileItemEditable" file_id="{{:id}}">
                    <div class="tit">
                        <i>{{:dt_added}}</i>
                        <em>{{:createdBy_shortName}}</em>
                    </div>
                     <div class="msg">{{:msg}}</div>
                    <div class="b"><span class="del"></span><span class="file" hash="{{:hash}}">{{:title}}</span></div>
                </div>
            </script>

    <script id="slogTypeFileProtected" type="text/x-jsrender">
                <div class="row" file_id="{{:id}}">
                    <div class="tit">
                        <i>{{:dt_added}}</i>
                        <em>{{:createdBy_shortName}}</em>
                    </div>
                     <div class="msg">{{:msg}}</div>
                    <div class="file" hash="{{:hash}}">{{:title}}</div>
                </div>
            </script>

    <div id="slog">
        <div class="slog-box">
            <div class="toggle">
                <button>лог операций</button>
            </div>
            <div class="data">
                <div class="ctrl">
                    <textarea id="slogAddMsg"></textarea>
                    <button id="slogAddLog">+ запись</button>
                    <button id="slogAddFiles">+ файл</button>
                </div>
                <div class="list">

                </div>
            </div>
        </div>
    </div>

    <div style="max-width: 1300px; overflow: hidden">

    <!--спецификаия-->
    <fieldset class="ui spec-data" style="background-color: #EEEEFF"><legend class="ui">Спецификация</legend>
        <table class="ui-table ltable" width="100%">
            <tr>
                <th>Артикул</th>
                <th width="100%" nowrap scope="col">Наименование товара</th>
                <th nowrap>Цена, без скидки</th>
                <th nowrap>Кол-во</th>
                <? if(!empty($os->adminCfg['purchase'])){?>
                    <th>Закуп</th>
                <? }?>
                <? if(!empty($os->adminCfg['reservation'])){?>
                    <th>Поставщик</th>
                    <th>Номер резерва</th>
                    <th>Дата резерва</th>
                <? }?>
                <th>Удалить</th>
            </tr>

            <tbody id="items">

            <? $l=0;
            $pItog=0;
            if(!empty($items))
                foreach($items as $v){
                    $l++;
                    /*
                    $imeta=Tools::unesc($v['meta']);
                    if(mb_strpos($imeta,'{')!==false) $imeta=unserialize($imeta); else $imeta=Tools::DB_unserialize($imeta);
                    */
                    ?>
                    <tr item_id="<?=$v['item_id']?>" cat_id="<?=$v['cat_id']?>">
                        <td align="center"><?=$v['cat_id']?>&nbsp;<a href="/<?=App_Route::_getUrl('search')?>.html?q=<?=$v['cat_id']?>" target="_blank" style="text-decoration: none; font-weight: bold; border: none">></a></td>
                        <td><span class="iname"><?=Tools::html($v['name'])?></span></td>
                        <td align="center"><input class="iprice" type="text" value="<?=floatval($v['price'])?>"></td>
                        <td align="center"><input class="iam" type="text" value="<?=($v['amount'])?>"></td>

                        <? if(!empty($os->adminCfg['purchase'])){
                            $pItog+=$v[$os->adminCfg['purchase']['DBF_pprice']]*$v['amount'] ?>
                            <td align="center" nowrap>
                                <input class="ipprice" type="text" value="<?=floatval($v[$os->adminCfg['purchase']['DBF_pprice']])?>">
                                <? if(@$os->adminCfg['purchase']['suplrSelectEnabled']){?>
                                    <button class="suplrSel"><span class="ui-icon ui-icon-folder-open"></span></button>
                                <? }?>
                            </td>
                        <? }?>

                        <? if(!empty($os->adminCfg['reservation'])){?>
                            <td align="center">
                                <select class="isuplrId">
                                    <option value="0">не задан</option>
                                    <? foreach($suplrs as $kk=>$vv){
                                        ?><option value="<?=$kk?>"<?=$kk==$v[$os->adminCfg['reservation']['DBF_suplrId']]?' selected="selected"':''?>><?=$vv['name']?></option><?
                                    }?>
                                </select>
                            </td>
                            <td align="center"><input class="ireserveNum" type="text" value="<?=Tools::html($v[$os->adminCfg['reservation']['DBF_reserveNum']])?>"></td>
                            <td align="center"><input class="ireserveDate" type="text" placeholder="00-00-0000" value="<?=Tools::sdate($v[$os->adminCfg['reservation']['DBF_reserveDate']])?>"></td>
                        <? }?>

                        <td align="center"><button class="idel"><span class="ui-icon ui-icon-circle-close"></span></button></td>
                    </tr>


                <? }?>

            <script id="newItemRow" type="text/x-jsrender">
                                <tr item_id="{{:item_id}}" cat_id="{{:cat_id}}">
                                    <td align="center">{{:cat_id}}</td>
                                    <td><span class="iname">{{:name}}</span></td>
                                    <td align="center"><input class="iprice" type="text" value="{{:price}}"></td>
                                    <td align="center"><input class="iam" type="text" value="{{:am}}"></td>

                                    <? if(!empty($os->adminCfg['purchase'])){?>
                                        <td align="center" nowrap>
                                            <input class="ipprice" type="text" value="0">
                                            <? if($os->adminCfg['purchase']['suplrSelectEnabled']){?>
                                                <button class="suplrSel"><span class="ui-icon ui-icon-folder-open"></span></button>
                                            <? }?>
                                        </td>
                                    <? }?>

                <? if(!empty($os->adminCfg['reservation'])){?>
                                        <td align="center">
                                            <select class="isuplrId">
                                                <option value="0">не задан</option>
                                                <? foreach($suplrs as $kk=>$vv){
                    ?><option value="<?=$kk?>"><?=$vv['name']?></option><?
                }?>
                                            </select>
                                        </td>
                                        <td align="center"><input class="ireserveNum" type="text" value=""></td>
                                        <td align="center"><input class="ireserveDate" type="text" placeholder="0000-00-00" value=""></td>
                                    <? }?>

                                    <td align="center"><button class="idel"><span class="ui-icon ui-icon-circle-close"></span></button></td>
                                </tr>
                            </script>

            </tbody>

            <tbody id="dops">

            <? if(!empty($dops)) foreach($dops as $v){?>
                <tr dop_id="<?=$v['dop_id']?>">
                    <td align="center">Доп.</td>
                    <td><span class="dname"><?=$v['name']?></span></td>
                    <td align="center"><input class="dprice" type="text" value="<?=floatval($v['price'])?>"></td>
                    <td align="center"><input class="dam" type="text" value="<?=($v['amount'])?>"></td>

                    <? if(@$os->adminCfg['purchase']['dopPPriceEnabled']){
                        $pItog+=$os->adminCfg['purchase']['DBF_dop_pprice']*$v['amount'] ?>
                        <td align="center"><input class="dpprice" type="text" value="<?=floatval($v[$os->adminCfg['purchase']['DBF_dop_pprice']])?>"></td>
                    <? }?>

                    <td align="center"><button class="ddel"><span class="ui-icon ui-icon-circle-close"></span></button></td>
                    <? if($addCol){?>
                        <td colspan="<?=$addCol?>"></td>
                    <? }?>
                </tr>
            <? }?>

            <script id="newDopRow" type="text/x-jsrender">
                            <tr dop_id="{{:dop_id}}">
                                <td align="center">Доп.</td>
                                <td><span class="dname">{{:name}}</span></td>
                                <td align="center"><input class="dprice" type="text" value="{{:price}}"></td>
                                <td align="center"><input class="dam" type="text" value="{{:am}}"></td>
                                 <? if(@$os->adminCfg['purchase']['dopPPriceEnabled']){?>
                                    <td align="center"><input class="dpprice" type="text" value="{{:<?=$os->adminCfg['purchase']['DBF_dop_pprice']?>}}"></td>
                                <? }?>
                                <td align="center"><button class="ddel"><span class="ui-icon ui-icon-circle-close"></span></button></td>
                                <? if($addCol){?>
                                <td colspan="<?=$addCol?>"></td>
                                <? }?>
                            </tr>
                        </script>

            </tbody>

            <tbody id="item-add">
            <form id="item-add-frm">
                <input type="hidden" name="order_id" value="<?=$order['order_id']?>">
                <tr>
                    <td><input type="text" name="cat_id" class="ai-cat_id" value="" placeholder="артикул" title="Введите артикул и нажмите Enter или вставьте артикул из буфера обмена для поиска товара и заполнения полей. Если артикул пуст, то, введенное значение в поле &quot;Наименование&quot; будет добавлено как дополнение"></td>
                    <td><input type="text" name="name" class="ai-name" value="" placeholder="Наименование дополнения"></td>
                    <td align="center"><input type="text" name="price" class="ai-price" value=""></td>
                    <td align="center"><input type="text" name="am" class="ai-am" value="4"></td>
                    <td align="center"><button class="ai-post"><span class="ui-icon ui-icon-plusthick"></span></button></td>
                    <? if($addCol){?>
                        <td colspan="<?=$addCol?>"></td>
                    <? }?>
                </tr>
            </form>

            </tbody>


        </table>
        <div id="itogs">
            <table>
                <tr>
                    <th nowrap><i>Скидка, %</i></th>
                    <td><input type="text" id="discount" style="width:50px; background-color: #FFF; text-align: center" value="<?=$order['discount']*1?>"></td>
                    <th><i>Стоимость доставки</i></th>
                    <td><input type="text" id="delivery_cost" style="width:50px; background-color: #FFF; text-align: center" value="<?=$order['delivery_cost']*1?>"> руб</td>
					<td><?=$order['DeliveryType']?></td>
                </tr>
            </table>
            <div style="position: relative; overflow:hidden;">
                <table style="display: inline-block; vertical-align: middle">
                    <tr>
                        <th>Итоговая сумма заказа</th>
                        <td><span id="oItog"><?=Tools::nn($order['cost'])?></span> руб</td>
                    </tr>
                    <? if(!empty($os->adminCfg['purchase'])){?>
                        <tr>
                            <th>Итоговая сумма закупки</th>
                            <td><span id="pItog"><?=Tools::nn($pItog)?></span> руб</td>
                        </tr>
                    <? }?>
                </table>
                <? if(!empty($os->adminCfg['purchase'])){?>
                    <div id="pmargin"<?=($order['cost']-$pItog)<$setup['marginAlert']?' class="pmargin-alert"':''?> style="display: inline-block; margin-left: 10px;">маржа: <?=Tools::nn($order['cost']-$pItog)?> руб</div>
                <? }?>
            </div>
        </div>
    </fieldset>

    <!-- сводка по заказу-->
    <fieldset class="ui" id="ctrls">
        <table style="width: 100%">
            <tbody class="odata">
            <tr>
                <th>Номер заказа </th>
                <td><b><?=Cfg::get('orderPrefix').$order['order_num']?></b></td>
                <th>Время заказа </th>
                <td><?=Tools::sDateTime($order['dt_add'])?></td>
                <th>IP</th>
                <td><?=$order['ip']?></td>
            </tr>

            <tr>
                <th>Состояние заказа</th>
                <td>

                    <select order_id="<?=$order['order_id']?>" class="chState axblock"><?
                        if(!in_array($order['state_id'],array_keys($oStates[$order['method']]))) echo '<option value="0">ошибка в статусе</option>';
                        else
                            foreach($oStates[$order['method']] as $k=>$v){
                                if(empty($v['excludeFromDropList']) || 1)
                                    if($order['state_id']==$k || in_array($order['state_id'],$v['allowFrom'])) {
                                        if($k==$order['state_id']){
                                            ?><option value="<?=$k?>" selected><?=$v['label']?></option><?
                                        }else{
                                            ?><option value="<?=$k?>"><?=$v['actLabel']?></option><?
                                        }
                                    }

                            }
                        ?></select>

                </td>
                <th>Менеджер:</th>
                <td class="mgrFullNameName"><?=$order['cUserId']?@$cusers[$order['cUserId']]['fullName']:'---'?></td>
                <th>Оформлен:</th>
                <td><?=$order['createdBy']?$cusers[$order['createdBy']]['shortName']:'клиентом'?></td>
            </tr>

            <? if(!empty($os->adminCfg['drivers']) || !empty($os->adminCfg['delivery'])){?>

                <tr>

                    <? if(!empty($os->adminCfg['drivers'])){?>
                        <th>Водитель</th>
                        <td>
                            <select id="driverId">
                                <option value="0">не задан</option>
                                <? foreach($drivers as $k=>$v){?>
                                    <? if($k==$order[$os->adminCfg['drivers']['DBF_driverId']] || empty($v['disabled'])){?>
                                      <option value="<?=$k?>"<?=$k==$order[$os->adminCfg['drivers']['DBF_driverId']]?' selected="selected"':''?>><?=$v['shortName']?></option>
                                    <? }?>
                                <? }?>
                            </select>
                        </td>
                    <? }?>

                    <? if(!empty($os->adminCfg['delivery']['DBF_deliveryDate'])){?>
                        <th>Дата доставки</th>
                        <td>
                            <input type="text" id="deliveryDate" value="<?=Tools::sdate($order[$os->adminCfg['delivery']['DBF_deliveryDate']])?>" style="width: 100px; text-align: center" placeholder="00-00-0000">
                        </td>
                    <? }?>

                    <? if(!empty($os->adminCfg['delivery']['DBF_TTN'])){?>
                        <td colspan="2" align="center">
                            <input type="text" id="TTN" value="<?=$order[$os->adminCfg['delivery']['DBF_TTN']]?>" style="width: 180px; text-align: center" placeholder="ТТН" title="номер товарно-транспортной накладной">
                        </td>
                    <? }?>

                </tr>

                <? if(!empty($os->adminCfg['suplrPaymentDate']['DBF_suplrPaymentDate']) || !empty($os->adminCfg['billDate']['DBF_billDate'])){?>
                    <tr>
                        <? if(!empty($os->adminCfg['suplrPaymentDate']['DBF_suplrPaymentDate'])){?>
                            <th>Оплачено поставщику</th>
                            <td>
                                <input type="text" id="suplrPaymentDate" value="<?=Tools::sdate($order[$os->adminCfg['suplrPaymentDate']['DBF_suplrPaymentDate']])?>" style="width: 100px; text-align: center" placeholder="00-00-0000" <?=!empty($os->adminCfg['suplrPaymentDate']['roleIds']) && !in_array(CU::$roleId,$os->adminCfg['suplrPaymentDate']['roleIds']) ?'disabled="disabled"':''?>
                            </td>
                        <? }?>

                        <? if(!empty($os->adminCfg['billDate']['DBF_billDate'])){?>
                            <th>Дата счета</th>
                            <td>
                                <input type="text" id="billDate" value="<?=Tools::sdate($order[$os->adminCfg['billDate']['DBF_billDate']])?>" style="width: 100px; text-align: center" placeholder="00-00-0000" title="Счет и договор будет доступен для выгрузки после заполнения этого поля"  <?=!empty($os->adminCfg['billDate']['roleIds']) && !in_array(CU::$roleId,$os->adminCfg['billDate']['roleIds']) ?'disabled="disabled"':''?>
                            </td>
                        <? }?>
                    </tr>
                <? }?>

            <? }?>

            </tbody>
        </table>

        <?
        $docExist=false;
        $docHtml=$docNonHtml=0;
        foreach($os->docCfg as $type=>$vtype){
            foreach($vtype as $k=>$v){
                if(@$v['useInCMS']) {
                    $docExist=true;
                    if($type=='html') $docHtml++; else $docNonHtml++;
                }
            }
        }
        ?>
        <table style="width: 100%">
            <tr>
                <? if($docExist){?>
                    <td style="width: 200px">
                        <fieldset class="ui" style="height: 130px"><legend>Выгрузка документов</legend>
                            <? if($docHtml){
                                ?><button id="exportBody" style="height: 40px; width: 180px">Отправка на почту в теле письма</button><?
                            }
                            if($docNonHtml){
                                ?><button id="exportAttach" style="height: 40px; width: 180px">Отправка на почту вложением</button><?
                            }
                            if($docNonHtml){
                                ?><button id="exportFile" style="height: 40px; width: 180px">Сохранить на диск</button><?
                            }?>
                        </fieldset>
                    </td>
                <? }?>
                <td>
                    <fieldset class="ui" style="height: 130px;">
                        <legend>Комментарий менеджера к заказу</legend>
                        <textarea name="tech" id="tech" style="width:99%; background-color: #e1cccc; font-size: 1.2em; padding: 5px; height: 100px"><?=Tools::taria($order['tech'])?></textarea>
                    </fieldset>
                </td>
            </tr>

        </table>
    </fieldset>

    <!-- адреса и контакты -->
    <fieldset class="ui client-data">
        <table style="width: 100%">
            <tbody class="odata">

            <?
            $af=App_TFields::formEl('os_order','all','all',$order);

            if($ptypeOn || $methodOn){
                $i=0;
                ?><tr><?
                if($ptypeOn){
                    $i++;
                    ?><th><?=$af['ptype'][0]?></th><?
                    ?><td style="width: 200px"><?=$af['ptype'][1]?></td><?
                }
                if($methodOn){
                    $i++;
                    ?><th style="width: 200px"><?=$af['method'][0]?></th><?
                    ?><td><?=$af['method'][1]?></td><?
                }
                if($i<2){
                    ?><td></td><td></td><?
                }
                ?></tr><?
            }

            ?>
            <tr>
                <th style="width: 30%">Имя клиента</th>
                <td colspan="3"><input type="text" name="name" id="name" style="width:100%" value="<?=Tools::html($order['name'])?>"></td>
            </tr>
            <tr>
                <th>Е-mail</th>
                <td colspan="3"><input type="text" name="email" id="email" style="width:100%" value="<?=Tools::html($order['email'])?>"></td>
            </tr>
            <tr>
                <th>Город</th>
                <td colspan="3"><input type="text" name="city" id="city" style="width:100%" value="<?=Tools::html($order['city'])?>"></td>
            </tr>
            <tr>
                <th>Адрес</th>
                <td colspan="3"><textarea name="addr" id="addr" style="width:100%" rows="3"><?=Tools::taria($order['addr'])?></textarea></td>
            </tr>
            <tr>
                <th nowrap>Комментарий клиента</th>
                <td colspan="3"><textarea name="info" id="info" style="width:100%" rows="3"><?=Tools::taria($order['info'])?></textarea></td>
            </tr>
            </tbody>

            <?

            foreach($af as $k=>$v){
                if($k!='ptype' && $k!='method')
                    if(!$ptypeOn || !isset(App_TFields::$fields['os_order'][$k]['ptype'])){
                        ?><tbody class="odata"><tr class="af"><th><?=$v[0]?></th><td colspan="3"><?=$v[1]?></td></tr></tbody><?
                    } else{
                        if(App_TFields::$fields['os_order'][$k]['ptype']==$order['ptype']){
                            ?><tbody class="odata ptype<?=App_TFields::$fields['os_order'][$k]['ptype']?>"><tr class="af"><th><?=$v[0]?></th><td colspan="3"><?=$v[1]?></td></tr></tbody><?
                        }else{
                            ?><tbody class="odata ptype<?=App_TFields::$fields['os_order'][$k]['ptype']?> off"><tr class="af"><th><?=$v[0]?></th><td colspan="3"><?=$v[1]?></td></tr></tbody><?
                        }
                    }
            }
            ?>

        </table>
    </fieldset>

    </div>


    </div>

    <? if(!empty($_GET['ref'])){?>

        <input type="button" value="Вернуться назад" class="back">

    <?}?>

    <input type="button" value="Подтвердить заказ" class="confirmNewOrder">
    <input type="button" value="Отменить заказ" class="cancelNewOrder">

<? } else warn('<p>Заказ не найден</p>');
cp_end();