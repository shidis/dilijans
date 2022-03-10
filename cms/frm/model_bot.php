<?
require_once '../auth.php';
include('../struct.php');

$gr = @$_GET['gr'];
if ($gr != 1 && $gr != 2) die('gr incorrect. exit.');

$cp->frm['name'] = 'models_bot';

$cp->checkPermissions();

cp_head();
cp_css();
cp_js();
cp_body();
cp_title(false);

$cc = new CC_Ctrl;
$cc1 = new CC_Ctrl;


foreach ($_GET as $key => $value) if (!is_array($value)) $$key = Tools::esc($value); else $$key = $value;
foreach ($_POST as $key => $value) if (!is_array($value)) $$key = Tools::esc($value); else $$key = $value;

if (isset($medit_post1) || isset($medit_post)) {
    $res = -1;

    if ($cp->isAllow('models.edit')) {
        // Стикеры
        $cc1->que('model_by_id', $medit_id);
        $cc1->next();
        if (!empty($cc1->qrow['sticker_id']))
        {
            $cc1->update('cc_model_stickers', Array('sticker_type' => $model_sticker_type, 'sticker_text' => @$model_sticker_text), "sticker_id = '".$cc1->qrow['sticker_id']."'");
            $sticker_id = $cc1->qrow['sticker_id'];
        }
        elseif (!empty($model_sticker_type))
        {
            $cc1->insert('cc_model_stickers',Array('sticker_type' => $model_sticker_type, 'sticker_text' => @$model_sticker_text));
            $sticker_id = $cc->lastId();
        }
        //
        $text = @$tmh_text != '' ? $tmh_text : $text;
        $res = $cc->model_ae('edit', array(
            'af' => @$af,
            'model_id' => $medit_id,
            'brand_id' => $brand_id_,
            'sup_id' => $sup_id,
            'name' => $name,
            'sname' => $sname,
            'alt' => @$alt,
            'suffix' => $suffix,
            'mspez_id' => @$mspez_id,
            'class_id' => @$class_id,
            'text' => $text,
            'P1' => @$P1,
            'P2' => @$P2,
            'P3' => @$P3,
            'imgFileFileld' => 'imgFile',
            'spyUrl' => $spyUrl,
            'delImg' => @$delImg,
            'hit_quant' => $hit_quant,
            'is_seo' => @$is_seo,
            'seo_h1' => $seo_h1,
            'seo_title' => $seo_title,
            'seo_keywords' => $seo_keywords,
            'seo_description' => $seo_description,
            'sticker_id' => @$sticker_id,
            'video_link' => @$video_link
        ));

    } elseif ($cp->isAllow('models.imageEdit')) {
        $res = $cc->model_ae('edit', array(
            'model_id' => $medit_id,
            'imgFileFileld' => 'imgFile',
            'spyUrl' => $spyUrl,
            'delImg' => @$delImg
        ));
    }

    if ($res === -1) warn('Нет прав на редактирование');
    elseif ($res) {
        note('Модель отредактирована' . (!empty($cc->fres_msg) ? (". ".$cc->strMsg()) : ''));
        if (isset($ti_id)) {
            $ti_id = (int)$ti_id;
            if ($ti_id != 0) $d = $cc->getOne("SELECT count(ti_id) FROM cc_model WHERE gr='$gr' AND model_id!='$medit_id' AND ti_id='$ti_id'");
            if ($ti_id != 0 && $d[0]) warn("Код ТИ модели $ti_id уже присутсвует у другой модели - не обновлен"); else
                $cc->query("UPDATE cc_model SET ti_id='$ti_id' WHERE model_id='$medit_id'");
        }

    } else warn('Ошибка в процессе обновления модели' . (!empty($cc->fres_msg) ? (". ".$cc->strMsg()) : ''));

} elseif (@$medit_id > 0 || isset($medit_post)) {
    include('catalog_bot_medit.php');
    cp_end();
    exit();
}

if (!empty($act) && $act != -1 && !$cp->isAllow('models.acts')) {
    warn($cp->d());
    $act = -1;
}


if (@$act == 'mdel') if ($cc->ld('cc_model', 'model_id', $_POST['id'], $gr)) note('Удалено.');

if (@$act == 'copy_img' && @$to_id > 0) {
    $cc->que('model_by_id', $to_id);
    $cc->next();
    $i = 0;
    $res = true;
    $c = new CC_Ctrl;
    if (!$cc->qrow['model_id']) {
        warn('[copy_img]: модель model_id=' . $x[1] . ' не найдена');
        $res = false;
    }
    $img2 = $cc->qrow['img2'];
    if ($img2 != '' && !is_file(Cfg::get('cc_upload_path') . '/' . $img2)) {
        warn('[copy_img]: Проблема с изображением №2');
        $res = false;
    }
    if ($res) {
        $res=true;
        $uploader=new Uploader();
        $__url=$cc->makeImgPath($cc->qrow['img2'], true);
        if(!$uploader->spyUrl($__url, Uploader::$EXT_GRAPHICS)) {
            warn('[copy_img.Uploader]:: ' . $__url . ': ' . $uploader->strMsg());
        }else
            foreach ($_POST as $k => $v) {
                $x = explode('_', $k);
                if ($x[0] == 'cc')
                    if ($x[1] != $to_id) {
                        $destId=$x[1];
                        if(!$cc->imgUpload('cc_model', $destId, $gr, 1, $uploader->sfile)){
                            $res=false;
                        }elseif(!$cc->imgUpload('cc_model', $destId, $gr, 2, $uploader->sfile)){
                            $res=false;
                        }elseif(!$cc->imgUpload('cc_model', $destId, $gr, 3, $uploader->sfile)){
                            $res=false;
                        }
                        $i++;
                    }
            }
        if(!$res) warn("[copy_img]:: ".$cc->strMsg());
        $uploader->del();
    }
    note("В <b>$i</b> моделей скопированы изображения");
}

if (@$act == 'move' && @$to_id > 0) {
    $i = 0;
    $cc1 = new CC_Ctrl;
    $cc->que('model_by_id', $to_id);
    $cc->next();
    $cr = $cc->qrow;
    $msuf = explode(' ', trim($cc->qrow['suffix']));
    foreach ($_POST as $k => $v) {
        $x = explode('_', $k);
        if ($x[0] == 'cc') if ($x[1] != $to_id) {
            $cc->que('model_by_id', $x[1]);
            $cc->next();
            $to_suf = explode(' ', trim($cc->qrow['suffix']));
            $cc->query("SELECT * FROM cc_cat WHERE model_id={$x[1]}");
            while ($cc->next() !== false) {
                $suf = explode(' ', trim($cc->qrow['suffix']));
                $suf = trim(implode(' ', array_diff(array_merge($to_suf, $suf), $msuf)));
                $cc1->query("UPDATE cc_cat SET model_id='$to_id', suffix='$suf' WHERE cat_id={$cc->qrow['cat_id']}");
                $cc1->sname_cat($cc->qrow['cat_id']);
            }
            $i++;
            $cc->ld('cc_model', 'model_id', $x[1]);
        }
    }
    $cc->qrow = $cr;
    if ($i) {
        $cc->sname_model(0, '', false);
        if ($gr == 1 && Cfg::get('INIS_S1S2')) CC_inis::modelUpdate($to_id);
        if ($gr == 2 && isset($cc->RDisk)) $cc->RDisk->modelUpdate($to_id);
        if ($gr == 1 && isset($cc->RTyre)) $cc->RTyre->modelUpdate($to_id);
        $cc->extra_price_update_for_model($to_id);
        if (isset($cc->intPrice)) $cc->intPrice->modelUpdate($to_id);
        if (Cfg::get('model_SC')) CC_ModelSC::modelUpdate($to_id);
    }
    note(" Объеденено $i моделей");
}
if (@$act == 'P3_1') {
    $mcou = 0;
    foreach ($_POST as $k => $v) {
        $x = explode('_', $k);
        if ($x[0] == 'cc') if ($x[1] > 0) {
            if ($cc->query("UPDATE cc_model SET P3='$_P3_1' WHERE model_id={$x[1]}")) $mcou++;
        }
    }
    note("Изменен флаг шип/нешип для $mcou моделей");
}
if (@$act == 'P1_2') {
    $mcou = 0;
    foreach ($_POST as $k => $v) {
        $x = explode('_', $k);
        if ($x[0] == 'cc') if ($x[1] > 0) {
            if ($cc->query("UPDATE cc_model SET P1='$_P1_2' WHERE model_id={$x[1]}")) $mcou++;
        }
    }
    note("Изменен тип для $mcou моделей OK");
}
if (@$act == 'P2_1') {
    $cou = $mcou = 0;
    foreach ($_POST as $k => $v) {
        $x = explode('_', $k);
        if ($x[0] == 'cc') if ($x[1] > 0) {
            if ($cc->query("UPDATE cc_model SET P2='$_P2_1' WHERE model_id={$x[1]}")) $mcou++;
        }
    }
    note("Изменен тип авто для $mcou моделей");
}
if (@$act == 'P1_1' && $_P1_1 > 0) {
    $mcou = 0;
    foreach ($_POST as $k => $v) {
        $x = explode('_', $k);
        if ($x[0] == 'cc') if ($x[1] > 0) {
            if ($cc->query("UPDATE cc_model SET P1='$_P1_1' WHERE model_id={$x[1]}")) $mcou++;
        }
    }
    note("Изменен сезон для $mcou моделей");

} elseif (@$act == 'P1_1' && !@$P1_1) warn('Сезон должен быть задан!');

if (@$act == 'mspez') {
    $cou = 0;
    foreach ($_POST as $k => $v) {
        $x = explode('_', $k);
        if ($x[0] == 'cc') if ($x[1] > 0) {
            if ($cc->query("UPDATE cc_model SET mspez_id='$_mspez_id' WHERE model_id={$x[1]}")) $cou++;
        }
    }
    note("Изменена настройка для $cou моделей");
}

if (@$act == 'class') {
    $cou = 0;
    foreach ($_POST as $k => $v) {
        $x = explode('_', $k);
        if ($x[0] == 'cc') if ($x[1] > 0) {
            if ($cc->query("UPDATE cc_model SET class_id='$_class_id' WHERE model_id={$x[1]}")) $cou++;
        }
    }
    note("Изменен класс для $cou моделей");
}
if (@$act == 'hide_models') {
    $cou = 0;
    foreach ($_POST as $k => $v) {
        $x = explode('_', $k);
        if ($x[0] == 'cc') if ($x[1] > 0) {
            if ($cc->query("UPDATE cc_model SET H=1 WHERE model_id={$x[1]}")) $cou++;
        }
    }
    note("Скрыты $cou моделей");
}
if (@$act == 'show_models') {
    $cou = 0;
    foreach ($_POST as $k => $v) {
        $x = explode('_', $k);
        if ($x[0] == 'cc') if ($x[1] > 0) {
            if ($cc->query("UPDATE cc_model SET H=0 WHERE model_id={$x[1]}")) $cou++;
        }
    }
    note("Включено отображение для $cou моделей");
}
if (@$act == 'sup') {
    $cou = 0;
    foreach ($_POST as $k => $v) {
        $x = explode('_', $k);
        if ($x[0] == 'cc') if ($x[1] > 0) {
            if ($cc->query("UPDATE cc_model SET sup_id='$_sup_id' WHERE model_id={$x[1]}")) {
                $cou++;
                $cc->extra_price_update_for_model($x[1]);
                if (isset($cc->intPrice)) $cc->intPrice->modelUpdate($x[1]);
            }
        }
    }
    note("Изменен поставщик для $cou моделей");
}
if (@$act == 'del_cat') {
    echo '<p>Удаляем типоразмеры для выбранных моделей...';
    $cou = 0;
    foreach ($_POST as $k => $v) {
        $x = explode('_', $k);
        if ($x[0] == 'cc') if ($x[1] > 0) {
            if ($cc->query("UPDATE cc_cat SET LD=1 WHERE model_id={$x[1]}")) $cou++;
            if ($gr == 1 && Cfg::get('INIS_S1S2')) CC_inis::modelUpdate($x[1]);
            if ($gr == 2 && isset($cc->RDisk)) $cc->RDisk->modelUpdate($x[1]);
            if ($gr == 1 && isset($cc->RTyre)) $cc->RTyre->modelUpdate($x[1]);
            if (isset($cc->intPrice)) $cc->intPrice->modelUpdate($x[1]);
            if (Cfg::get('model_SC')) CC_ModelSC::modelUpdate($x[1]);
        }
    }
    if ($cou) $cc->addCacheTask('sizes', $gr);
    note("Удалены типоразмеры для $cou моделей");
}
if (@$act == 'del_model') {
    $cou = 0;
    foreach ($_POST as $k => $v) {
        $x = explode('_', $k);
        if ($x[0] == 'cc') if ($x[1] > 0) {
            $cc->query("UPDATE cc_cat SET LD=1 WHERE model_id={$x[1]}");
            if ($cc->query("UPDATE cc_model SET LD=1 WHERE model_id={$x[1]}")) $cou++;
        }
    }
    if ($cou) $cc->addCacheTask('sizes', $gr);
    note("Удалено $cou моделей");
}
if (@$act == 'save_order') {
    $cou = 0;
    foreach ($_POST as $k => $v) {
        $x = explode('_', $k);
        if ($x[0] == 'pos') if ($x[1] > 0) {
            if ($cc->query("UPDATE cc_model SET pos=".intval($v)." WHERE model_id={$x[1]}")) $cou++;
        }
    }
    if ($cou) $cc->addCacheTask('sizes', $gr);
    note("Изменен порядок для всех моделей бренда");
}

if (isset($brand_id)) {?>

    <style type="text/css">

        .ealt {
            cursor: pointer;
            width: 20px;
        }

        <? echo '#groupOp{
            display:none;
        }'?>
        fieldset {
            margin: 10px 0;
        }

        #showGroupOp {
            margin: 5px 0;
            display: block;
        }

        .ltable INPUT {
            text-align: center;
            vertical-align: middle
        }

        table.ui-table th {
            -cursor: pointer
        }

        .row {
            margin: 5px 0;
            display: block;
            overflow: hidden
        }

        .msg-block {
            margin: 5px;
            0;
        }

        input.remote {
            color: #00F;
        }

        .fl-l {
            float: left;
        }

        .suplrList{
            float:right;
            background:url(/cms/img/folder-open.gif) no-repeat 50% 50%;
            width:16px;
            height:16px
        }
        .catList{
            float:right;
            background:url(/cms/img/folder-open.gif) no-repeat 50% 50%;
            width:16px;
            height:16px
        }
        .murl{
            float: right;
            margin-left: 10px;
        }
    </style>
    <?
    $brand_id = @$_REQUEST['brand_id'];

    $cc->load_sup();
    $cc->load_mspez($gr);
    $cc->load_class($gr);

    $w = $h = array();
    if (@$show_only_h) $w[] = 'cc_model.H' . (!$brand_id ? " AND NOT cc_brand.H" : '');
    else if (@$hide_h) $w[] = 'NOT cc_model.H' . (!$brand_id ? " AND NOT cc_brand.H" : '');
    else if (@$has_video) $w[] = 'cc_model.video_link != ""';
    if (@$mcNOTzero) {
        $h[] = 'catNum>0';
        if(!$brand_id) $w[] = "NOT cc_brand.H";
    }
    elseif (@$mczero) $h[] = 'catNum=0';
    if (@$_REQUEST['noImg']) $w[] = "(cc_model.img1='' || cc_model.img2='' || cc_model.img3='')" . (!$brand_id ? " AND NOT cc_brand.H" : '');

    if (@$_REQUEST['show_stickers'] == 1 && empty($_REQUEST['no_stickers'])) $w[] = "(cc_model.sticker_id > 0)";
    if (@$_REQUEST['no_stickers'] == 1 && empty($_REQUEST['show_stickers'])) $w[] = "(cc_model.sticker_id = 0)";
    if (@$_REQUEST['priority']) $w[] = "(cc_model.pos > 0)";

    if(!empty($_REQUEST['q'])) {
        $s=trim(Tools::cutDoubleSpaces($_REQUEST['q']));
        if(Tools::typeOf($s)=='integer') $w[]="cc_model.model_id='$s'";
        else {
            $s = explode(' ', $s);
            $h['bm'] = array();
            foreach ($s as $ss) {
                $ss = trim($ss);
                if (empty($ss)) break 1;
                $ss = Tools::esc($ss);
                $h['bm'][] = "BMJOIN LIKE '%$ss%'";
            }
            $h['bm'] = '(' . implode(' AND ', $h['bm']) . ')';
        }
    }

    $dbStart=((int)@$_GET['page'] > 0 ? ($_GET['page'] - 1) : 0) * (int)@$_GET['lines'];
    $lines=(int)@$_GET['lines'];
    if(empty($lines)) $lines=5000; // если нет пагинации, то задаем допустимый макс. чтобы не повесить браузер

    $num = $cc->models(array(
        'gr' => $gr,
        'brand_id' => $brand_id,
        'H' => 0,
        'P1' => @$_REQUEST['p1'] != '' ? (int)$_REQUEST['p1'] : '',
        'nolimits' => 1,
        'seekMode' => 1,
        'mspez_id' => @$_GET['mspez_id'],
        'start' => $dbStart,
        'lines' => $lines,
        'datasetTo' => 'model',
        'sqlReturn' => 0,
        'dataset_id' => @$inDSonly ? $dataset_id : 0,
        'select' => "cc_model.is_seo, cc_model.has_text, cc_model.text AS text, cc_model.H, cc_model.hit_quant, cc_model.P1 AS MP1, cc_model.tags, cc_model.dt_added, cc_model.dt_upd".(!empty($h['bm'])?", CONCAT(cc_brand.name,' ',cc_brand.alt,' ',cc_model.name,' ',cc_model.alt,' ') AS BMJOIN":''),
        'qSelect' => array(
            'catNum' => array('notH' => 1),
            'scSum' => array('notH' => 1)
        ),
        'sqlReturn' => 0,
        'where' => $w,
        'having' => $h
    ));

    if(is_callable(array('App_SUrl',$gr==2?'dModel':'tModel'))) $show_mlinks=true; else $show_mlinks=false;

    if ($num)
    {
        ?><script><?
        if(!empty(Cfg::$config['dbsync'])){
            $sync=Cfg::$config['dbsync'];
            $rdb=new DB();
            $rdb->set_db($sync['remote_sql_host'],$sync['remote_sql_db'],$sync['remote_sql_user'],$sync['remote_sql_pass']);
            if($rdb->sql_connect()) {
                ?>var dbsync = 1;<?
        } else unset($sync);
        }
        ?></script>
        <form method="post" name="form1" id="form1">
            <input name="medit_id" value="-1" type="hidden">
            <input name="id" value="-1" type="hidden">
            <input name="act" value="-1" type="hidden">

            <p<?= @$_COOKIE['__cp_model_showGroupOp'] ? ' style="display:none"' : '' ?>><a href="#" id="showGroupOp">развернуть групповые операции</a></p>
            <fieldset class="ui" id="groupOp"<?= @$_COOKIE['__cp_model_showGroupOp'] ? ' style="display:block"' : '' ?>>
                <legend>Групповые операции</legend>
                <table border="0" cellspacing="5" cellpadding="0">
                    <tr>
                        <td valign="top" nowrap>
                            <? if ($gr == 1) { ?>
                                <select name="_P3_1" class="chosen-s0" style="width: 200px;">
                                    <option value="0">Убрать шипы</option>
                                    <option value="1">Есть шипы</option>
                                </select><input class="button0" type="submit" onClick="document.forms['form1'].act.value='P3_1'"
                                                value=">>">
                                <br>
                                <select name="_P1_1" class="chosen-s0" style="width: 200px">
                                    <option value="0">Установить сезон</option>
                                    <option value="1">Лето</option>
                                    <option value="2">Зима</option>
                                    <option value="3">Всесезон.</option>
                                </select><input class="button0" type="submit" onClick="document.forms['form1'].act.value='P1_1'"
                                                value=">>">
                                <br>
                                <select name="_P2_1" class="chosen-s0" style="width: 200px">
                                    <option value="0">Задать тип авто</option>
                                    <option value="1">Легковой</option>
                                    <option value="2">Внедорожник</option>
                                    <option value="3">Микроавтобус</option>
                                    <option value="4">Легковой/внедор.</option>
                                </select><input class="button0" type="submit"
                                                onClick="document.forms['form1'].act.value='P2_1';" value=">>"><br>
                            <? } elseif ($gr == 2) { ?>
                                <select name="_P1_2" class="chosen-s0" style="width: 200px">
                                    <option value="2">Литой диск</option>
                                    <option value="1">Кованый</option>
                                    <option value="3">Штампованный</option>
                                </select><input class="button0" type="submit" onClick="document.forms['form1'].act.value='P1_2'"
                                                value=">>"><br>
                            <? } ?>
                            <? $cc1->que('class_list', $gr);
                            if ($cc1->qnum()) {
                                ?>
                                <select name="_class_id" class="chosen-s0" style="width: 200px">
                                    <option value="0">Убрать класс</option>
                                    <? while ($cc1->next() !== false) { ?>
                                        <option value="<?= $cc1->qrow['class_id'] ?>"><?= $cc1->qrow['name'] ?></option>
                                    <? } ?>
                                </select><input class="button0" type="submit"
                                                onClick="document.forms['form1'].act.value='class'" value=">>"><br>
                            <? } ?>
                            <select name="_sup_id" class="chosen" style="width: 200px" id="sup_id">
                                <? foreach ($cc->sup_arr as $k => $v) { ?>
                                    <option value="<?= $k ?>"><?= $k == 0 ? 'Убрать поставщика' : $v ?></option>
                                <? } ?>
                            </select><input class="button0" type="submit" onClick="document.forms['form1'].act.value='sup'" value=">>"><br>
                            <? $cc1->query("SELECT * FROM cc_mspez WHERE gr='$gr' ORDER BY name");
                            if ($cc1->qnum()) {
                                ?>
                                <select name="_mspez_id" class="chosen-s0" style="width: 200px">
                                    <option value="0">Убрать доп. параметр</option>
                                    <? while ($cc1->next() !== false) { ?>
                                        <option value="<?= $cc1->qrow['mspez_id'] ?>"><?= $cc1->qrow['name'] ?></option>
                                    <? } ?>
                                </select><input class="button0" type="submit"
                                                onClick=" document.forms['form1'].act.value='mspez'" value=">>"><br>
                            <? } ?>
                        </td>
                        <td valign="top">
                            <div style="max-width: 600px">
                                <input class="button fl-l" type="button"
                                       onClick="document.forms['form1'].act.value='copy_img'; document.forms['form1'].submit();"
                                       value="Скопировать изображения в выбранные модели">
                                <input class="button fl-l" type="submit" onClick="document.forms['form1'].act.value='move';"
                                       value="Объединить выбранные модели">
                                <input class="button fl-l" type="submit" value="Удалить модели"
                                       onClick="if(window.confirm('УДАЛИТЬ!?')){ document.forms['form1'].act.value='del_model'} else return false;">
                                <input class="button fl-l" type="submit" value="Удалить типоразмеры"
                                       onClick="if(window.confirm('УДАЛИТЬ!?')){ document.forms['form1'].act.value='del_cat'} else return false;">
                                <input class="button fl-l" type="submit" value="Скрыть модели"
                                       onClick="document.forms['form1'].act.value='hide_models'">
                                <input class="button fl-l" type="submit" value="Отобразить модели"
                                       onClick="document.forms['form1'].act.value='show_models'">
                                <? if (@$tagWork) { ?><input type="button" class="button fl-l" id="allTags"
                                                             value="настройка тегов"><? } ?>
                                <?if ($gr == 2):?>
                                    <input class="button fl-l" type="submit" value="Сохранить порядок"
                                           onClick="document.forms['form1'].act.value='save_order'">
                                <?endif;?>
                            </div>
                        </td>
                    </tr>
                </table>

                <p style="margin:15px;"><a href="#" id="hideGroupOp">скрыть панель групповых операций</a></p>

            </fieldset><? // конец групповые операции

            $suplrList=(bool)Data::get('cms_suplrsByModel');

            $supExist = $classExist = $mspezExist = false;
            $dbi=0;
            while ($cc->next() != false && $dbi<$lines) {
                $dbi++;
                if ($cc->qrow['sup_id']) $supExist = true;
                if ($cc->qrow['class_id']) $classExist = true;
                if ($cc->qrow['mspez_id']) $mspezExist = true;
            }
            $cc->seek($dbStart);
            $tagCfg = Cfg::get('ccTags');
            $tags = new CC_Tags();
            ?>
            <script type="text/javascript">
                Tags.enabled = <?=(int)@$tagCfg['enabled']*@$tagWork?>;
                Tags.gr = <?=(int)$gr?>;
                Tags.allTagsButton = '#allTags';
                Tags.from = 'model_bot';
                Tags.tags =<?=$tags->tagsList(array('gr'=>$gr,'json'=>1))?>;
                Tags.groups =<?=$tags->groupsList(array('gr'=>$gr,'json'=>1))?>;
            </script>
            <?
            if (!empty($_GET['lines'])) {
                ?><style type="text/css">
                    .paginator {
                        margin: 5px 0;
                        padding-left: 10px;
                    }

                    .paginator li {
                        display: inline;
                        list-style: none;
                        font-size: 13px;
                        padding: 0 2px;
                    }

                    .paginator li a {
                        padding: 0 5px;
                    }

                    .paginator li.active {
                        padding: 0 7px;
                    }
                </style><?

                $pg = $cc->paginator('/cms/frm/model_bot.php', $_GET, @$_GET['page'], $num, @$_GET['lines'], 'page', array(
                    'active' => '<li class="active">{page}</li>',
                    'noActive' => '<li><a href="{url}">{page}</a></li>',
                    'dots' => '<li>...</li>'
                ), 25);
                if(!empty($pg)){
                    ?><ul class="paginator">
                    <li>Всего <?= $num ?>. Страницы: </li><?
                    foreach ($pg as $v) {
                        echo $v;
                    }
                    ?></ul><?
                }
            }

            ?><table class="ui-table ltable" style="width:100%"><?
                ?><tr><?
                    ?><th><input type="checkbox" onclick="SelectAll(checked,'form1')"></th><?
                    ?><th>&lt;*&gt;</th><?
                    ?><th>изо</th><?
                    ?><th>видео</th><?
                    ?><th>Текст</th><?
                    ?><th>Бренд</th><?
                    ?><th colspan="2">Название модели<br>(кол-во не скрытых размеров)</th><?
                    if ($hq = Cfg::get('cmsShowHitQuant')) {
                        ?><th>Популярность</th><?
                    }if ($gr == 1) {
                        ?><th>Сезон / шипы</th><?
                        ?><th>Тип авто</th><?
                    } else {
                        ?><th>Тип диска</th><?
                    }
                    if ($gr == 2)
                    {
                        ?><th>Стикер</th><?
                        ?><th>Приоритет<br>выдачи</th><?
                    }
                    if ($classExist || $mspezExist) {
                        ?><th>Доп. параметр</th><?
                    }
                    if (@$tagWork) {
                        ?><th>Теги</th><?
                    }
                    if ($supExist) {
                        ?><th>Поставщик</th><?
                    }
                    ?><th>На складе (не скрытых)</th><?
                    ?><th>SEO</th><?
                    ?><th>Скрыть</th><?
                    if(!($hideDT=Data::get('cms_models_hideDT'))){
                        ?><th>Добавлено</th><?
                    }
                    ?><th>Удалить</th><?
                    ?></tr><?
                $dbi=0;

                while ($cc->next() != false && $dbi<$lines) {
                    $dbi++;
                    $suggestP2 = $suggestP3 = $suggestP1 = '';
                    if (@$dop == 'syncGetParams' && $gr == 1) {
                        $rd = $rdb->getOne("SELECT cc_model.P1, cc_model.P2, cc_model.P3 FROM cc_model JOIN cc_brand USING (brand_id) WHERE cc_model.gr=1 AND NOT cc_brand.LD AND NOT cc_model.LD AND cc_brand.name LIKE '{$cc->qrow['bname']}' AND cc_model.name LIKE '{$cc->qrow['name']}'");
                        if ($rd !== 0) {
                            if ($cc->qrow['P2'] != $rd['P2']) $suggestP2 = $rd['P2'];
                            if ($cc->qrow['P3'] != $rd['P3']) if ($rd['P3']) $suggestP3 = '<br><span style="color:red">шипы</span>'; else $suggestP3 = '<br><span style="color:red">нешин</span>';
                            if($cc->qrow['P1'] != $rd['P1']){
                                switch($rd['P1']){
                                    case 1: $suggestP1='лето'; break;
                                    case 2: $suggestP1='зима'; break;
                                    case 3: $suggestP1='всесезон.'; break;
                                }
                                if(!empty($suggestP1)) $suggestP1="<br><span style=\"color:red\">$suggestP1</span>";
                            }

                        }
                    }

                    if($show_mlinks) $murl=$gr==2?App_SUrl::dModel(0,$cc->qrow):App_SUrl::tModel(0,$cc->qrow);

                    ?><tr id="t_<?= $cc->qrow['model_id'] ?>" mid="<?=$cc->qrow['model_id']?>"><?
                    ?><td align="center"><input id="cc" name="cc_<?= $cc->qrow['model_id'] ?>" type="checkbox" value="1"></td><?
                    ?><td align="center"><input name="to_id" type="radio" value="<?= $cc->qrow['model_id'] ?>" onClick="if(cc_<?= $cc->qrow['model_id'] ?>.checked)return false"></td><?
                    ?><td align="center"><?
                    if($cc->qrow['img1']) {
                        echo '<a class="iPreview highslide" img="' . $cc->make_img_path(2) . '?' . mt_rand() . '" alt="' . Tools::html($cc->qrow['bname'] . ' ' . $cc->qrow['name']) . '" title="' . Tools::html($cc->qrow['bname'] . ' ' . $cc->qrow['name']) . '" href="' . $cc->make_img_path(1) . '?' . mt_rand() . '"><img src="../img/img.gif"></a>';
                    } else{
                        echo '<a href="http://images.yandex.ru/yandsearch?text='.urlencode(Tools::unesc($cc->qrow['bname'] . ' ' . $cc->qrow['name'])).'" target="_blank">Y.I</a> ';
                        echo '<a href="https://www.google.ru/images?q='.urlencode(Tools::unesc($cc->qrow['bname'] . ' ' . $cc->qrow['name'])).'" target="_blank">G.I</a>';
                    } ?></td><?
                    ?><td align="center"><?= !empty($cc->qrow['video_link']) ? '<a href="'.$cc->qrow['video_link'].'" target="_blank"><img src="/app/images/watch_video_button.png" width="25px"></a>' : '<a href="https://www.youtube.com/results?search_query='.Tools::unesc($cc->qrow['bname'] . ' ' . $cc->qrow['name']).'" target="_blank">YT</a>' ?></td><?
                    ?><td align="center"><?= $cc->qrow['has_text'] ? '<img src="../img/mods.gif">' : '' ?></td><?
                    ?><td align="center"><?=$cc->qrow['bname'] ?></td><?
                    ?><td align="left"><a href="#" class="medit" title="<?= $cc->qrow['sname'] ?>"><?= Tools::unesc($cc->qrow['name']) ?></a> <?= Tools::unesc($cc->qrow['suffix']) ?> (<?= $cc->qrow['catNum'] ?>)<?
                    if(!empty($murl)) {?><a href="<?=$murl?>" class="murl" target="_blank">(на&nbsp;сайт)</a><? }
                    ?><a href="#" class="catList"></a></td><?
                    ?><td align="center"><a title="<?= Tools::unesc($cc->qrow['alt']) ?>" class="ealt<?= $cc->qrow['alt'] != '' ? " bold" : '' ?>" href="#">alt</a></td><?
                    if ($hq) {
                        ?><td align="center"><?= $cc->qrow['hit_quant'] ?></td><?
                    }

                    if ($gr == 1) {
                        ?><td align="center"><?
                        ?><span sez="<?= $cc->qrow['P1'] ?>" class="sez"></span><?
                        ?><span class="ship" ship="<?= $cc->qrow['P3'] ?>"></span><?
                        echo $suggestP1.$suggestP3;
                        ?></td><?
                        ?><td align="center"><span atype="<?= $cc->qrow['P2'] ?>" mid="<?= $cc->qrow['model_id'] ?>" class="atype" suggest="<?= $suggestP2 ?>"></span></td><?
                    } elseif ($gr == 2) {
                        ?><td align="center"><span class="dtype" dtype="<?= $cc->qrow['P1'] ?>" mid="<?= $cc->qrow['model_id'] ?>"></span></td><?
                        ?><td align="center"><?=(!empty($cc->qrow['sticker_id']) ? '<span>Да</span>' : '<span>Нет</span>')?></td><?
                        ?><td align="center"><input name="pos_<?=$cc->qrow['model_id']?>" value="<?=@$cc->qrow['pos']?>" type="text" size="5"></td><?
                    }

                    if ($classExist || $mspezExist) {
                        ?><td align="center"><?= @$cc->class_arr[$cc->qrow['class_id']] ?><?= (isset($cc->mspez_arr[$cc->qrow['mspez_id']]) ? (' <span title="дополнительный параметр" class="tooltip red">(' . $cc->mspez_arr[$cc->qrow['mspez_id']] . ')</span>') : '') ?></td><?
                    }

                    if (@$tagWork) {
                        ?><td align="center"><select class="tags" v="<?= $cc->qrow['tags'] ?>" model_id="<?= $cc->qrow['model_id'] ?>"></select></td><?
                    }

                    if ($supExist) {
                        ?><td align="center"><?= @$cc->sup_arr[$cc->qrow['sup_id']] ?></td><?
                    }
                    ?><td align="center"><?
                    if($suplrList){
                        ?><a href="#" class="suplrList"></a><?
                    }
                    ?><?=$cc->qrow['scSum'] ?></td><?

                    ?><td align="center"><?=$cc->qrow['is_seo'] == 1 ? "Да":"" ?>
                    </td><?

                    ?><td align="center"><a href="#" class="h-sw"><?= $cc->qrow['H'] != '1' ? 'скрыть' : 'отобразить' ?></a></td><?
                    if(!$hideDT){
                        ?><td align="center"><?=$cc->qrow['dt_added']?></td><?
                    }
                    ?><td align="center"><a href="#" class="mdel"><img src="../img/b_drop.png" border="0"></a></td><?
                    ?></tr><?
                }
                ?></table><?
            ?></form><?

        if (!empty($_GET['lines'])) {
            ?><ul class="paginator"><?
            foreach ($pg as $v) {
                echo $v;
            }
            ?></ul><?
        }

        ?>

        <script type="text/javascript">
            var gr='<?=$gr?>';
        </script>

        <?

    } else note('Пустой результат');

} else note('Выберите производителя и нажмите "Искать"');

cp_end();