<?
require_once '../auth.php';
include('../struct.php');

$gr = @$_GET['gr'];
if ($gr != 1 && $gr != 2) die('gr incorrect. exit.');

if ($gr == 1) $cp->frm['title'] = 'Аксессуары шин';
else $cp->frm['title'] = 'Аксессуары дисков';

$cp->frm['name'] = 'accessories';

$cp->checkPermissions();

cp_head();
cp_css();
cp_js();
cp_body();
cp_title();

$cc = new CC_Ctrl;
// Сохранение данных с общей страницы
foreach ($_POST as $key => $value) {
    if (($a = explode('_', $key)) !== FALSE) {
        if (@$_POST['pos_post'] > 0 && $a[0] == 'pos' && $a[1] > 0) {
            $bid = (int)$a[1];
            $value = (int)$value;
            if (!$cc->query("UPDATE cc_accessories SET pos='$value' WHERE acc_id='$bid'")) warn('Ошибка записи.');
        }
    }
    if (!is_array($value)) $$key = Tools::esc($value); else $$key = $value;
}
// Удаление с общей страницы
if (@$del_sel > 0) {
    $i = 0;
    foreach ($_POST as $k => $v) {
        $x = explode('_', $k);
        if (@$x[0] == 'c' && @$x[1] > 0) {
            if ($cc->ld('cc_accessories', 'acc_id', $x[1])) $i++;
        }
    }
    note("Удалено $i аксессуаров");
}
// Редактирование позиций
if (@$_POST['pos_nul'] == 1) {
    $cc->query("UPDATE cc_accessories SET pos=0 WHERE gr='$gr' ");
}
// Удаление списком
if (@$ld_id > 0) if (!$cc->ld('cc_accessories', 'acc_id', $ld_id, $gr)) {
    note('Удалено.');
}
// Снятие с публикации
if (@$hide_id > 0) $cc->hide_switch('cc_accessories', 'acc_id', $hide_id);

// Редактирование одной записи
if (@$edit_id > 0)
    if (isset($post)) {
        if (!$cc->query("UPDATE cc_accessories SET gr = '$gr',name = '$name', aprice = '$aprice', dt_upd = '".date('Y-m-d H:i:s')."' WHERE acc_id='$edit_id'")) warn('Ошибка записи.');
        else {
            $uploader = new Uploader();
            $cc->addCacheTask('accessories', $gr);
        }
    } else {
        include('accessories_post.php');
        cp_end();
        exit();
    }
// Добавление записи
elseif (isset($post)) {
    if (!$cc->query("INSERT INTO cc_accessories (gr,name,aprice,dt_added,dt_upd) VALUES('$gr','$name','$aprice','".date('Y-m-d H:i:s')."','".date('Y-m-d H:i:s')."')")) warn('Ошибка записи.');
    else {
        $acc_id = $cc->lastId();
        $cc->query("SELECT max(acc_id) FROM cc_accessories");
        $cc->next();
        $max = $cc->qrow[0];
        if (!$cc->query("UPDATE cc_accessories SET pos='$max' WHERE acc_id='$max'")) warn('Ошибка записи2.');
        note('Аксессуар добавлен.');

        $uploader = new Uploader();
        $cc->addCacheTask('accessories', $gr);
    }
}

if (@$add_id > 0) {
    include('accessories_post.php');
    cp_end();
    exit();
}
// ****************** Выбираем акссесуары ********************
$cc->query("SELECT * FROM `cc_accessories` WHERE `gr` = '{$gr}' AND NOT `LD` ORDER BY 'pos';", MYSQLI_ASSOC);
?>
<style type="text/css">
    INPUT {
        text-align: center;
        vertical-align: middle
    }

    table.ui-table th {
        cursor: pointer
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
</style>

<script>
    function del_cascade(){
        return(confirm('Подтвердите удаление. Вы хорошо подумали?'));
    }

    function do_form(id,action)
    {
        if (action==1) document.form1.edit_id.value=id ;
        if (action==2) if (del_cascade()) document.form1.ld_id.value=id ; else return false;
        if (action==6) if (del_cascade()) document.form1.del_sel.value=1;  else return false;
        document.form1.submit();
        return false;
    }
</script>

<form method="get">
    <label>
        Группа:
        <select class="brands-groups-list" name="gr" title="Выбор группы товаров">
            <option class="brands-groups-list__item" value="1" <?= $gr == 1 ? 'selected' : ''; ?>>Шины</option>
            <option class="brands-groups-list__item" value="2" <?= $gr == 2 ? 'selected' : ''; ?>>Диски</option>
        </select>
    </label>
    <input type="submit" value="Выбрать">
</form>

<form name="form1" method="post">
    <input name="edit_id" value="-1" type="hidden">
    <input name="ld_id" value="-1" type="hidden">
    <input name="del_sel" value="-1" type="hidden">
    <input name="hide_id" value="-1" type="hidden">
    <input name="add_id" value="-1" type="hidden">
    <input name="extra_post" value="-1" type="hidden">
    <input name="pos_post" value="-1" type="hidden">
    <input name="pos_nul" value="-1" type="hidden">

    <div class="row">
        <input type="submit" value="+ Добавить аксессуар" onClick="document.form1.add_id.value=1;">
        <input type="button" value="Удалить выбранное" onClick="do_form(0,6); return false">
        <input type="submit" value="Сохранить порядок" onClick="document.form1.pos_post.value=1">
    </div>
    <?
    $l = 1;
    if ($cc->qnum()) {
        ?>
        <table class="ui-table tablesorter">
            <thead>
            <tr>
                <th><input type="checkbox" onclick="SelectAll(checked,'form1')"></th>
                <th scope="col">ID</th>
                <th scope="col">Название</th>
                <th scope="col">Цена</th>
                <th scope="col">Порядок</th>
                <th scope="col">Удалить</th>
            </tr>
            </thead>
            <tbody>
            <? $dsl = array();
            while ($cc->next() != FALSE) {
                echo "<tr id=\"bid_{$cc->qrow['acc_id']}\">";
                echo '<td><input id="cc" type="checkbox" name="c_' . $cc->qrow['acc_id'] . '" value="1"></td>';
                echo '<td align=center>' . $cc->qrow['acc_id'] . '</td>';
                echo '<td><a href="javascript:;" onClick="do_form(' . $cc->qrow['acc_id'] . ',1);return false">' . Tools::unesc($cc->qrow['name']) . '</a></td>';
                echo '<td>' . $cc->qrow['aprice'] . '</a></td>';
                echo "<td align=center><span class=hide>{$cc->qrow['pos']}</span><input name=\"pos_{$cc->qrow['acc_id']}\" value=\"{$cc->qrow['pos']}\" type=\"text\" size=\"5\"></td>\n";
                echo '<td nowrap align="center"><a href="javascript:;" onClick="do_form(' . $cc->qrow['acc_id'] . ',2);return false"><img src="../img/b_drop.png" border="0"></a></td>';
                echo '</tr>';
                $l++;
            }
            ?></tbody>
        </table>
    <? } ?>
    <input type="submit" value="+ Добавить аксессуар" onClick="document.form1.add_id.value=1;">
    <input type="button" value="Удалить выбранное" onClick="do_form(0,6); return false">
    <input type="submit" value="Сохранить порядок" onClick="document.form1.pos_post.value=1">
</form>
<? cp_end() ?>
