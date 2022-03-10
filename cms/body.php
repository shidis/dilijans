<?
require_once 'auth.php';
include('struct.php');


$cp->frm['name']='main';
$cp->frm['title']='Dashboard '.Tools::toup(Cfg::get('remote_site_url')).' <img src="img/green_dot.gif" width="10" height="10">';


cp_head();
cp_css();
cp_js();
cp_title(true);
cp_body();

$os=new App_Orders();
$cc=new CC_Ctrl();

?>

    <script type="text/javascript">

        botLog.enabled=<?=Cfg::get('botLog')?>;

    </script>

    <style type="text/css">
        #botLog #info td{
            font-size:14px;
            font-weight:bold;
        }
        fieldset{
            padding: 15px;
        }
        .row{
            margin: 0 0 15px 0;
        }
        .column{
            display: inline-block;
            margin: 0 20px 0 0;
            vertical-align: top;
        }
        fieldset{
            margin: 0;
            padding: 10px 10px;
        }
        P{
            padding: 3px 0 3px 0;
            margin: 0;
        }

    </style><?

?><div class="row">

    <div class="column">
        <fieldset><legend>Проверка системы</legend><?

            $d=$cc->getOne("SELECT UNIX_TIMESTAMP(NOW())");
            $t1=$d[0];
            $t2=time();
            if($t1!=$t2){
                warn("Время MySQL не совпадет с временем PHP. MySQL = ".date("m-d-Y H:i:s",$t1)." PHP = ".date("m-d-Y H:i:s",$t2));
            }else{
                ?><p>Системное время: <?=date("m-d-Y H:i:s",$t1)?></p><?
            }
            if(MC::chk()){
                $i=MC::checkMem();
                echo '<p>'.MC::$version." (свободно = {$i['freePercent']} % памяти)</p>";
                if(is_array($i)) {
                    if ($i['status'] == 'alert') warn("<p>Размер свободной памяти MemCached опустился до опасного значения (свободно = {$i['freePercent']} %)");
                } else
                    warn('Проверка памяти memCached не удалась');

                $pid=@Cfg::$config['DJS']['pid'];
                if(!empty($pid)){
                    $v=(int)MC::get("DJS:$pid:js_misses");
                    $k=(int)MC::get("DJS:$pid:js_reloadMisses");
                    $l=(int)MC::get("DJS:$pid:js_afterReload");
                    if($v){
                        echo "<p>[DJS]: не удачных/повторных неудачных запросов / повторных удачных = <span class=\"djsMisses\">$v/$k/$l</span> ".(CU::$roleId==1?"<a href=\"#\" class=\"djsMissesReset\">(reset vars)</a>":'')."</p>";
                    }
                }

                $v=MC::get('exec_cc_tasks'.MC::uid());

                if(empty($v))
                    warn('<b style="color: red">ВНИМАНИЕ!</b> Задание по обновлению базы данных (cron/cron15) не запускалось более 30 минут. Возможно это нарушение настроек CRON сервера. Необходимо вмешательство администратора сайта.');

                if(Data::get('SMS_enabled')){
                    $sms=new Bot_SMS();
                    $v=MC::get($sms->mcBotName());
                    if(empty($v))
                        warn('<b style="color: red">ВНИМАНИЕ!</b> Давно не выполнялось задание по контролю доставки СМС сообщений (cron/smsChk). Возможно это нарушение настроек CRON сервера. Необходимо вмешательство администратора сайта.');

                    ?><div id="SMSBalance"></div><?
                }


            }else{
                warn('<b>WARNING!</b> Memcache(d) не запущен.');
            }

            try {
                $mango = new Orders_Mango();
                $v = Data::get('os_log_calls');
                if (!empty($v)) {
                    $v = Tools::DB_unserialize($v);
                    if ((time() - @$v['tsLastRun_1']) > 60 * 30 || (time() - @$v['tsLastRun_2']) > 60 * 30) warn('<b style="color: red">ВНИМАНИЕ!</b> Агрегатор звонков MangoOffice не запускался более 30 минут. Возможно это нарушение настроек CRON сервера. Необходимо вмешательство администратора сайта.');

                    if (isset($v['balance'])) {
                        echo "<p>Баланс сервиса MangoOffice = <b>{$v['balance']}</b> </p>";
                    }

                    $mangoData=[];
                    // считаем вызов несостоявшимся если co==source AND type=1 OR type=11
                    $d=$mango->fetchAll("SELECT DATE(dt) AS date1, olc.type, COUNT(*) AS calls FROM os_log_calls olc WHERE DATE(dt) >= DATE_SUB(DATE(NOW()), INTERVAL 4 DAY) AND (olc.type=1 AND olc.co=olc.dest OR olc.type=11) GROUP BY date1, olc.type ORDER BY date1 DESC, olc.type");

                    foreach($d as $v){
                        $date=Tools::sdate($v['date1'],'.');
                        if(!isset($mangoData[$date])) $mangoData[$date]=[1=>'-', 11=>'-'];
                        $mangoData[$date][11]+=(int)$v['calls'];
                    }

                    // успешные
                    $d=$mango->fetchAll("SELECT DATE(dt) AS date1, olc.type, COUNT(*) AS calls FROM os_log_calls olc WHERE DATE(dt) >= DATE_SUB(DATE(NOW()), INTERVAL 4 DAY) AND (olc.type=1 AND olc.co!=olc.dest) GROUP BY date1, olc.type ORDER BY date1 DESC, olc.type");

                    foreach($d as $v){
                        $date=Tools::sdate($v['date1'],'.');
                        if(!isset($mangoData[$date])) $mangoData[$date]=[1=>'-', 11=>'-'];
                        $mangoData[$date][1]+=(int)$v['calls'];
                    }
                }
            } catch (Exception $e) {}

            if(0 && MDB::connect()){
                $r=MDB::$db->command(array('buildinfo'=>true));
                $cls=MDB::$db->listCollections();
                echo '<p>MongoDB enabled. ';
                echo ' Version: '.$r['version'];
                echo ' Collections: '.implode(', ',$cls);
                echo '</p>';
            }


            ?></fieldset>
    </div>
</div>
    <div class="row">

        <div class="column">
            <fieldset><legend>Статистика базы данных</legend><?
                $m1=$cc->getOne("SELECT count(cc_model.model_id) FROM cc_model INNER JOIN cc_brand ON cc_model.brand_id=cc_brand.brand_id WHERE (cc_model.gr=1) AND NOT cc_model.LD AND NOT cc_model.H AND NOT cc_brand.LD AND NOT cc_brand.H");
                $m1=$m1[0];
                ?>В базе отображаемых на сайте моделей ШИН = <strong><?=$m1;?></strong><br><?

                $m2=$cc->getOne("SELECT count(cc_model.model_id) FROM cc_model INNER JOIN cc_brand ON cc_model.brand_id=cc_brand.brand_id WHERE (cc_model.gr=2) AND NOT cc_model.LD AND NOT cc_model.H AND NOT cc_brand.LD AND NOT cc_brand.H");
                $m2=$m2[0];
                ?>В базе отображаемых на сайте моделей ДИСКОВ = <strong><?=$m2;?></strong><br><?

                $t1=$cc->getOne("SELECT count(cc_cat.cat_id) FROM (cc_cat INNER JOIN cc_model ON cc_cat.model_id = cc_model.model_id) INNER JOIN cc_brand ON cc_model.brand_id = cc_brand.brand_id WHERE cc_cat.gr=1 AND NOT cc_cat.H AND NOT cc_cat.LD AND NOT cc_model.LD AND NOT cc_model.H AND NOT cc_brand.LD AND NOT cc_brand.H");
                $t1=$t1[0];
                ?>В базе отображаемых на сайте размеров ШИН = <strong><?=$t1;?></strong><br><?

                $t2=$cc->getOne("SELECT count(cc_cat.cat_id) FROM (cc_cat INNER JOIN cc_model ON cc_cat.model_id = cc_model.model_id) INNER JOIN cc_brand ON cc_model.brand_id = cc_brand.brand_id WHERE cc_cat.gr=2 AND NOT cc_cat.H AND NOT cc_cat.LD AND NOT cc_model.LD AND NOT cc_model.H AND NOT cc_brand.LD AND NOT cc_brand.H");
                $t2=$t2[0];
                ?>В базе отображаемых на сайте размеров ДИСКОВ = <strong><?=$t2;?></strong><br><?

                $d=$cc->getOne("SELECT count(cc_model.model_id) FROM cc_model JOIN cc_brand USING (brand_id) WHERE cc_model.gr=1 AND NOT cc_model.LD AND NOT cc_model.H AND NOT cc_brand.LD AND NOT cc_brand.H AND cc_model.img1!=''");
                ?>Моделей шин с фото (img2) = <strong><?=$d[0]?></strong> (без фото = <strong><?=$m1-$d[0]?></strong>)<br><?

                $d=$cc->getOne("SELECT count(cc_model.model_id) FROM cc_model JOIN cc_brand USING (brand_id) WHERE cc_model.gr=2 AND NOT cc_model.LD AND NOT cc_model.H AND NOT cc_brand.LD AND NOT cc_brand.H AND cc_model.img1!=''");
                ?>Моделей дисков с фото (img2) = <strong><?=$d[0]?></strong> (без фото = <strong><?=$m2-$d[0]?></strong>)<br><?

                $d=$cc->getOne("SELECT count(cc_model.model_id) FROM cc_model JOIN cc_brand USING (brand_id) WHERE cc_model.gr=1 AND NOT cc_model.LD AND NOT cc_model.H AND NOT cc_brand.LD AND NOT cc_brand.H AND cc_model.text=''");
                ?>Моделей шин без описания = <strong><?=$d[0]?></strong><br><?

                //
                $s1=$cc->getOne("SELECT count(cc_model.model_id) FROM cc_model WHERE (cc_model.gr=1) AND is_seo=1");
                $s1=$s1[0];
                ?>
                Оптимизировано шин  = <strong><?=$s1;?></strong><br><?

                $s1=$cc->getOne("SELECT count(cc_cat.cat_id) FROM cc_cat WHERE (cc_cat.gr=1) AND is_seo=1");
                $s1=$s1[0];
                ?>
                Оптимизировано типоразмеров шин  = <strong><?=$s1;?></strong><br><?

                $s1=$cc->getOne("SELECT count(cc_model.model_id) FROM cc_model WHERE (cc_model.gr=2) AND is_seo=1");
                $s1=$s1[0];
                ?>
                Оптимизировано дисков  = <strong><?=$s1;?></strong><br><?

                $s1=$cc->getOne("SELECT count(cc_cat.cat_id) FROM cc_cat WHERE (cc_cat.gr=2) AND is_seo=1");
                $s1=$s1[0];
                ?>
                Оптимизировано типоразмеров дисков  = <strong><?=$s1;?></strong><br><?
                //

                ?></fieldset>
        </div>
    </div>

    <div class="row">
        <div>
            <fieldset><legend>Статистика звонков и заказов</legend><?

                if(Cfg::get('waitList')){
                    $d=$cc->getOne("SELECT count(*) FROM os_waitList");
                    ?><p>Количество заявок посетителей на отсутсвующие товары = <strong><?=@$d[0]?></strong><?
                    $d=$cc->getOne("SELECT count(*) FROM os_waitList JOIN cc_cat USING (cat_id) JOIN cc_model USING (model_id) JOIN cc_brand USING (brand_id) WHERE cc_cat.sc>=os_waitList.am AND NOT cc_cat.LD AND NOT cc_model.LD AND NOT cc_brand.LD AND DATEDIFF(NOW(),os_waitList.dt_added)<=days_lifeTime");
                    if($d[0]){
                        ?> / актуальных <b class="red"><?=$d[0]?></b><?
                    }
                    $d=$cc->getOne("SELECT count(*) FROM os_waitList JOIN cc_cat USING (cat_id) JOIN cc_model USING (model_id) JOIN cc_brand USING (brand_id) WHERE cc_cat.sc>=os_waitList.am AND NOT cc_cat.LD AND NOT cc_model.LD AND NOT cc_brand.LD AND DATEDIFF(NOW(),os_waitList.dt_added)<=days_lifeTime AND NOT noticed");
                    if($d[0]){
                        ?> / без уведомлений <b><?=$d[0]?></b></p><?
                    }
                }

                if(!empty($mangoData)){
                    echo '<p><a href="#" class="call-graph-open">MangoOffice: принятые/пропущенные вызовы:</a> ';
                    $i=0;
                    foreach($mangoData as $k=>$v){
                        $i++;
                        echo "$k -> <b>{$v[1]}</b>/<b class='red'>{$v[11]}</b>";
                        if($i<count($mangoData)) echo ', ';
                    }
                    echo '</p><div style="margin: 10px 0 20px 0; width: 99%; height:400px; display: none; border: 1px dashed #CCC" class="call-graph"></div> ';
                }

                $d=$os->fetchAll("SELECT DATE(dt_add) AS date1, COUNT(*) AS ordersNum FROM os_order oo WHERE NOT LD AND DATE(dt_add) >= DATE_SUB(DATE(NOW()), INTERVAL 1 DAY) GROUP BY date1 ORDER BY date1 DESC", MYSQL_ASSOC);
                echo '<p><b>ЗАКАЗОВ сегодня/вчера: ';
                $orders=[0,0];
                if(count($d)==2)
                    $orders = [$d[0]['ordersNum'], $d[1]['ordersNum']];
                else $orders = [0, (int)@$d[0]['ordersNum']];
                echo '<a href="frm/orders.php" class="red">&nbsp;&nbsp;' . $orders[0] . '&nbsp;&nbsp;</a>';
                echo ' / <span style="color:#808080;">' . $orders[1] . '</span>';
                echo '</b></p>';

                ?></fieldset>
        </div>

    </div>


<? if(CMS_LEVEL_ACCESS==1 || CMS_LEVEL_ACCESS==2){?>
    <div class="row">
        <div class="column">
            <button id="exlibDebugSw">ExLib Debug Mode</button>
            <button id="exlibConcatJS">update JS</button>
            <button id="exlibConcatCSS">update CSS</button>
            <button id="exlibConcatImages">update Images</button>
        </div>
    </div>
<? }?>



<? $ss=new Content();
$t=$ss->getDoc('cms_mp');
if(CMS_LEVEL_ACCESS==1) $t1=$ss->getDoc('cms_mp_level1'); else $t1='';

if(!empty($t) || !empty($t1)){?>

    <fieldset class="row">

        <div>
            <? if(!empty($t)){?>
                <div class="column">
                    <?=$t?>
                </div>
            <? }?>
            <? if(!empty($t1)){?>
                <div class="column">
                    <?=$t1?>
                </div>
            <? }?>
        </div>
    </fieldset>
<? }?>

<? $ss=new Content();
$widgets=$ss->getDoc('cms_widgets$6');
if(!empty($widgets)){
    ?><div style="margin:15px 0"><?=$widgets?></div><?
}

?><div style="margin: 15px 0"><?

if(Cfg::get('botLog')){?>
    <div id="botLog" style="width:99%; display:none; overflow:visible; margin-top:15px;">
        <fieldset class="ui" style="border:1px dashed #039; padding:15px; margin:10px 0"><legend class="ui">Статистика посещений сайта поисковыми ботами</legend>
            <div id="botlogPager"></div>
            <table id="botlogGrid"></table>
            <table id="info" class="ui-table">
                <tr><th>За сегодня Yandex/Гугл</th><td id="today"><span></span> / <span></span></td></tr>
                <tr><th>Вчера Yandex/Гугл</th><td id="yesteday"><span></span> / <span></span></td></tr>
                <tr><th>За неделю Yandex/Гугл</th><td id="week"><span></span> / <span></span></td></tr>
                <tr><th>За 30 дней Yandex/Гугл</th><td id="month"><span></span> / <span></span></td></tr>
                <tr><th>Всего записанных в лог посещений Yandex/Гугл</th><td id="total"><span></span> / <span></span></td></tr>
            </table>
        </fieldset>
    </div>
    <button id="loadBotLog">отобразить Bot Log</button>
    <?
}

?><div style="width:99%; display:none; overflow:visible;">
    <div id="log_pagered"></div>
    <table id="log_grid"></table>
</div>

</div

<?

cp_end();
