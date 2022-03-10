<? if (!defined('true_enter')) die ("Direct access not allowed!");

if (@$add_id > 0) {
    echo '<h1>Добавить аксессуар</h1>';
    $edit_id = 0;
} elseif (@$edit_id > 0) {
    $cc->que('acc_by_id', $edit_id);
    $cc->next();
    echo '<h1>Редактировать аксессуар ' . $cc->qrow['name'] . '</h1>';
}
?>
<style type="text/css">
    .row {
        display: block;
        overflow: hidden;
        width: 100%;
    }

    label {
        display: block;
    }
</style>
<script type="text/javascript">
    var tb_els = 'text<? foreach(App_TFields::get('cc_accessories','editor',$gr) as $k=>$v){?>, af[<?=$k?>]<? }?>';

    $().ready(function () {
        $('[name=form1]').submit(function () {
            if ($('[name=name]').length && $('[name=name]').val() == '' && $('[name=edit_id]').val() >= 0) {
                alert('Не введено название аксессуара');
                return false;
            }
        })
    });
</script>

<div class="edit_area">
    <form action="" method="post" enctype="multipart/form-data" name="form1">
        <input type="hidden" name="MAX_FILE_SIZE" value="<?= Cfg::get('max_file_size'); ?>">
        <input type="hidden" name="edit_id" value="<?= $edit_id; ?>">
        <input type="hidden" name="name0" value="<?= Tools::unesc($cc->qrow['name']) ?>">
        <input name="post" type="submit" id="post" value="Записать"/>
        <input type="submit" value="Веpнуться без записи" onclick="document.form1.edit_id.value=-1"/>

        <table width="100%" border="0" cellpadding="0" cellspacing="5" style="margin: 10px 0 0">
            <tr>
                <td valign="top">
                    <fieldset>
                        <legend>Основные свойства</legend>
                        <table border="0" cellpadding="0" cellspacing="5">
                            <tr valign="top">
                                <td width="107"><strong>Название</strong></td>
                                <td><input name="name" type="text" id="name" style="width:200px"
                                           value="<?= @htmlspecialchars($cc->qrow['name']); ?>"></td>
                            </tr>
                            <tr valign="top">
                                <td width="107"><strong>Цена</strong></td>
                                <td><input name="aprice" type="text" id="aprice" style="width:200px"
                                           value="<?= @htmlspecialchars($cc->qrow['aprice']); ?>"></td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <td valign="top">
                </td>
            </tr>
        </table>
        <br/>
        <input name="post" type="submit" id="post" value="Записать"/>
        <input type="submit" value="Веpнуться без записи" onclick="document.form1.edit_id.value=-1"/>
    </form>
</div>
