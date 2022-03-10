<?
@define(true_enter, 1);
require_once('loader.php');
$cert = new CC_Certificates();
$cc = new CC_Ctrl();

switch ($_GET['act']) {
    default:
        echo '[certificates.php ERROR]: Неверный параметр вызова.';
        break;
    case 'save_discr':
        $cert_id = (int)@$_REQUEST['cert_id'];
        $text = Tools::esc(@$_REQUEST['gtext']);
        if ($cert->query("UPDATE cc_cert SET text='$text' WHERE cert_id='$cert_id'")) $_RESULT['fres'] = 1;
        break;
    case 'load_discr':
        $cert_id = (int)@$_REQUEST['cert_id'];
        $d = $cert->getOne("SELECT text FROM cc_cert WHERE cert_id='$cert_id'");
        $_RESULT['gtext'] = Tools::unesc(@$d['text']);
        break;
    case 'delete':
        $cert_id = @$_REQUEST['cert_id'];
        if (!$cert_id) break;
        $_RESULT['deleted'] = $cert->delImage($cert_id);
        $_RESULT['block'] = '<img src="../img/noimg1.gif">';
        break;
    case 'cert_add':
        $model_id = @$_REQUEST['model_id'];
        if (!$model_id) {
            echo('Надо выбрать модель');
            break;
        }
        $_RESULT['model_id'] = $model_id;
        $op = unserialize(Data::get('cert_img_param'));
        $op['img1_resize_h'] += 5;
        $text = @$_POST['text'];
        $res=$cert->ae('add', array('spy_url'=>@$_POST['spy_url'], 'model_id'=>$model_id, 'text'=>$text));
        if ($res===false) {
            $_RESULT['cert_id'] = 0;
            echo Msg::asStr();
        } else {
            echo 'Добавлено';
            $_RESULT['cert_id'] = $cert_id = $res;
            $ip1 = $cert->get_img_path($cert_id, 'img1');
            $ip3 = $cert->get_img_path($cert_id, 'img3');
            $cert->que('cert_list', $model_id);
            $_RESULT['block'] =
<<<HTML
                <div  id="g{$cert_id}" style="float:left; padding-right:20px; height:{$op['img1_resize_h']}px"><div style=" float:left; padding-right:5px"><a target="_blank" onclick="return hs.expand(this, {captionText: '$text'})" href="{$ip3}" class="highslide"><img src="{$ip1}"></a></div><div style=" float:left"><input type="image" src="../img/delete.gif" onClick="return doLoad('delete',{cert_id: $cert_id})"><br /><input type="image" border="0" src="../img/b_edit.png" class="tedit" value="$cert_id" />
HTML;
            if ($cert->qnum() > 1) $_RESULT['block'] .=
<<<HTML
                <br /><input title="Сделать основной" type="image" border="0" src="../img/activate.gif" onclick="doLoad('first', {cert_id:$cert_id, model_id:$model_id})" />
HTML;
<<<html
                    <br /><input type="image" border="0" src="../img/b_edit.png" class="tedit" value="$cert_id" /></div>
html;
        }
        break;
    case 'first':
        $model_id = @$_REQUEST['model_id'];
        $cert_id = @$_REQUEST['cert_id'];
        if (!$model_id) {
            echo('Нет ID модели.');
            break;
        }
        if (!$cert_id) {
            echo('Нет ID изображения');
            break;
        }
        $res=$cert->ae('edit', array('cert_id'=>$cert_id, 'first'=>true));
        if($res===false){
            echo Msg::asStr();
        }
    case 'model_sel':
        $model_id = @$_REQUEST['model_id'];
        if (!$model_id) {
            echo('Надо выбрать модель');
            break;
        }
        $op = unserialize(Data::get('cert_img_param'));
        $op['img1_resize_h'] += 5;
        $cert->que('cert_list', $model_id);?>
        <div id="cert" class="rama_green" style=" overflow:auto">
            <? if ($cert->qnum()) {
                $i = 0;
                while ($cert->next() !== false) {
                    $i++;?>
                    <div id="g<?= $cert->qrow['cert_id'] ?>"
                         style="float:left; padding-right:20px; height:<?= $op['img1_resize_h'] ?>px">
                        <div style=" float:left; padding-right:5px"><a target="_blank" onclick="return hs.expand(this, {captionText: '<?= Tools::html($cert->qrow['text']) ?>'})" href="<?= $cert->make_img_path(3) ?>" class="highslide"><img src="<?= $cert->make_img_path(1) ?>"></a></div>
                        <div style=" float:left"><input type="image" src="../img/delete.gif" onClick="return doLoad('delete',{cert_id:<?= $cert->qrow['cert_id'] ?>})"><? if ($i > 1) { ?>
                                <br/><input title="Сделать основной" type="image" border="0" src="../img/activate.gif" onclick="doLoad('first', {cert_id:<?= $cert->qrow['cert_id'] ?>, model_id:<?= $cert->qrow['model_id'] ?>})" /><? } ?>
                            <br/><input type="image" border="0" src="../img/b_edit.png" value="<?= $cert->qrow['cert_id'] ?>" class="tedit"/></div>
                    </div>
                <?
                }
            }?>
        </div>
        <div id="cert_add" class="rama_green">
            <h2>Добавить изображение</h2>

            <div style="" id="cert_add_saving" class="saving"></div>
            <form enctype="multipart/form-data" id="cert_add_form">
                <input type="hidden" name="model_id" value="<?= $model_id ?>">
                <input type="hidden" name="MAX_FILE_SIZE" value="<?= Cfg::get('max_file_size'); ?>">
                <? model_sel_data(); ?>
            </form>

        </div>
        <?
        break;
    case 'model_sel_upd':
        model_sel_data();
        break;
    case 'brand_sel':
        $brand_id = @$_REQUEST['brand_id'];
        if (!$brand_id) {
            echo('Надо выбрать бренд');
            break;
        }?>
        <select name="model_id" id="model_id" onChange="doLoad('model_sel', {model_id: this.form.model_id.value})">
            <option value="0">Выберите модель</option>
            <?
            $cc->query("SELECT cc_model.model_id, cc_model.name, cc_model.suffix, cc_model.H, (SELECT count(*) FROM cc_cert WHERE cc_cert.model_id=cc_model.model_id) AS gNum FROM cc_model JOIN cc_brand USING (brand_id) WHERE  NOT cc_brand.LD AND NOT cc_model.LD AND cc_model.brand_id=$brand_id ORDER BY cc_model.name");
            while ($cc->next() != FALSE){
                $class=array();
                if($cc->qrow['H']) $class[]='isH';
                if($cc->qrow['gNum']) $class[]='bold';
                $class=implode(' ',$class);
                echo "<option class=\"$class\" value=\"{$cc->qrow['model_id']}\" " . ($cc->qrow['model_id'] == @$_POST['model_id'] ? 'selected' : '') . ">{$cc->qrow['name']} {$cc->qrow['suffix']}" . ($cc->qrow['gNum']?"  [{$cc->qrow['gNum']}]":'') . "</option>";
            }
            ?>
        </select>
        <? break;
    case 'options_save':
        $op = array();
        $op0 = @unserialize(Data::get('cert_img_param'));
        $op['img1_resize_mode'] = @$_POST['img1_resize_mode'];
        $op['img2_resize_mode'] = @$_POST['img2_resize_mode'];
        $op['img3_resize_mode'] = @$_POST['img3_resize_mode'];
        $op['img1_resize_w'] = abs(ceil(@$_POST['img1_resize_w']));
        $op['img2_resize_w'] = abs(ceil(@$_POST['img2_resize_w']));
        $op['img3_resize_w'] = abs(ceil(@$_POST['img3_resize_w']));
        $op['img1_resize_h'] = abs(ceil(@$_POST['img1_resize_h']));
        $op['img2_resize_h'] = abs(ceil(@$_POST['img2_resize_h']));
        $op['img3_resize_h'] = abs(ceil(@$_POST['img3_resize_h']));
        $op = serialize($op);
        Data::set('cert_img_param', $op);
        break;
    case 'options':
        $op = unserialize(Data::get('cert_img_param'));
?>
<h2>Настройки сертификатов</h2>
<div style="overflow: hidden">
    <img src="../img/warning.gif" style="float: left; margin-right: 15px;">
    <p>
        Изображение 1 - наименьшего размера, изображение 3 - наибольшего. При изменении допустимых размерова, пересчет всех  закаченных изображений произведен НЕ будет.<br>
        Все изображения будут конвертироваться в формат JPG.
    </p>
</div>
<form id="options_form" name=="options_form" enctype="multipart/form-data" onSubmit="return false">
<table border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td class="white">
            <fieldset class="ui">
                <legend class="ui">Изображение 1</legend>
                <select name="img1_resize_mode" id="img1_resize_mode">
                    <option value="NO" <?= @$op['img1_resize_mode'] == 'NO' ? 'selected' : '' ?>>Не использовать</option>
                    <option value="SO" <?= @$op['img1_resize_mode'] == 'SO' ? 'selected' : '' ?>>Только уменьшать</option>
                    <option value="BW" <?= @$op['img1_resize_mode'] == 'BW' ? 'selected' : '' ?>>Ограничивать по ширине</option>
                    <option value="BH" <?= @$op['img1_resize_mode'] == 'BH' ? 'selected' : '' ?>>Ограничивать по высоте</option>
                </select>
                <label for="img1_resize_mode">Режим преобразования </label>
                <br>
                <input name="img1_resize_w" type="text" id="img1_resize_w" size="5"
                       value="<?= @$op['img1_resize_w'] ?>">
                <label for="img1_resize_w">Лимит по ширине<br>
                </label>
                <input name="img1_resize_h" type="text" id="img1_resize_h" size="5"
                       value="<?= @$op['img1_resize_h'] ?>">
                <label for="img1_resize_h">Лимит по высоте </label>
            </fieldset>
        </td>
        <td class="white">
            <fieldset>
                <legend>Изображение 2</legend>
                <select name="img2_resize_mode" id="img2_resize_mode">
                    <option value="NO" <?= @$op['img2_resize_mode'] == 'NO' ? 'selected' : '' ?>>Не использовать</option>
                    <option value="SO" <?= @$op['img2_resize_mode'] == 'SO' ? 'selected' : '' ?>>Только уменьшать</option>
                    <option value="BW" <?= @$op['img2_resize_mode'] == 'BW' ? 'selected' : '' ?>>Ограничивать по ширине</option>
                    <option value="BH" <?= @$op['img2_resize_mode'] == 'BH' ? 'selected' : '' ?>>Ограничивать по высоте</option>
                </select>
                <label for="img2_resize_mode">Режим преобразования </label>
                <br>
                <input name="img2_resize_w" type="text" id="img2_resize_w" size="5"
                       value="<?= @$op['img2_resize_w'] ?>">
                <label for="img1_resize_w">Лимит по ширине<br>
                </label>
                <input name="img2_resize_h" type="text" id="img2_resize_h" size="5"
                       value="<?= @$op['img2_resize_h'] ?>">
                <label for="img2_resize_h">Лимит по высоте </label>
            </fieldset>
        </td>
        <td class="white">
            <fieldset>
                <legend>Изображение 3</legend>
                <select name="img3_resize_mode" id="img3_resize_mode">
                    <option value="NO" <?= @$op['img3_resize_mode'] == 'NO' ? 'selected' : '' ?>>Не использовать</option>
                    <option value="SO" <?= @$op['img3_resize_mode'] == 'SO' ? 'selected' : '' ?>>Только уменьшать</option>
                    <option value="BW" <?= @$op['img3_resize_mode'] == 'BW' ? 'selected' : '' ?>>Ограничивать по ширине</option>
                    <option value="BH" <?= @$op['img3_resize_mode'] == 'BH' ? 'selected' : '' ?>>Ограничивать по высоте</option>
                </select>
                <label for="img3_resize_mode">Режим преобразования </label>
                <br>
                <input name="img3_resize_w" type="text" id="img3_resize_w" size="5"
                       value="<?= @$op['img3_resize_w'] ?>">
                <label for="img3_resize_w">Лимит по ширине<br>
                </label>
                <input name="img3_resize_h" type="text" id="img3_resize_h" size="5"
                       value="<?= @$op['img3_resize_h'] ?>">
                <label for="img3_resize_h">Лимит по высоте </label>
            </fieldset>
        </td>
    </tr>
</table>
<input style="float:left; margin-right:30px" id="options_sbut" type="button" value="СОХРАНИТЬ НАСТРОЙКИ" onClick="return doLoad('options_save',{f:document.getElementById('options_form')})">
<div style="float:left;" id="options_saving" class="saving"></div>
</form><br>
<br><br><a href="javascript:toggle('options');toggle('options_on')"><img class="nob" src="../img/drop-no.gif" align="baseline"> скрыть панель настроек</a>
<?
    break;
    case 'brands':
        ?>
        <select name="brand_id" id="brand_id" onChange="doLoad('brand_sel', {brand_id: this.form.brand_id.value})">
            <option value="0">Выберите брэнд</option>
            <?
            $gr=(int)@$_REQUEST['gr'];
            $cc->query("SELECT cc_brand.replica, cc_brand.brand_id, cc_brand.name, cc_brand.H, (SELECT count(cc_model.model_id) FROM cc_model JOIN cc_cert USING (model_id) WHERE cc_model.brand_id=cc_brand.brand_id AND NOT cc_model.LD) AS gNum FROM cc_brand  WHERE  NOT cc_brand.LD  AND cc_brand.gr=$gr ORDER BY cc_brand.replica ASC, cc_brand.name");
            $replica=0;
            while ($cc->next() !== FALSE) {
                $class=array();
                if($cc->qrow['H']) $class[]='isH';
                if($cc->qrow['gNum']) $class[]='bold';
                $class=implode(' ',$class);
                if(!$replica && $cc->qrow['replica']){
                    $replica=1;
                    ?><optgroup label="Replica"><?
                }
                ?><option class="<?=$class?>" value="<?= $cc->qrow['brand_id'] ?>"><?= Tools::unesc($cc->qrow['name']) . ($cc->qrow['gNum'] ? " [{$cc->qrow['gNum']}]" : '') ?></option><?
            }
            if($replica){
                ?></optgroup><?
            }
        ?></select><?
        break;
}
debug();

$debug_off =
<<<HTML
    <br><br><a href="javascript:toggle('debug_on');toggle('debug')"><img class="nob" src="../img/drop-no.gif" align="baseline">Debug off</a>
HTML;

$_RESULT['debug'] = '<h2>Отладочная информация</h2>' . $_RESULT['debug'] . $debug_off;

function model_sel_data()
{
    ?>
    <div id="cert_data">
        <table border="0" cellspacing="0" cellpadding="0" width="100%">
            <tr>
                <td>Загрузить файл:</td>
                <td><input type="file" name="img" id="img" size="30"></td>
                <td rowspan="2" style="padding-left:40px">Текстовое описание:<br>
                    <textarea name="text" id="text" style="width:500px; height:45px"></textarea></td>
                <td width="100%" rowspan="2" style="padding-left:40px">&nbsp;</td>
            </tr>
            <tr>
                <td>Spy Url:</td>
                <td><input type="text" name="spy_url" id="spy_url" style="width:100%"></td>
            </tr>
        </table>
        <br>
        <input style="" id="cert_add_sbut" type="button" value="ДОБАВИТЬ"
               onClick="return doLoad('cert_add',{f:document.getElementById('cert_add_form')})">
    </div>
<?
}

?>