<? if (!defined('true_enter')) die ("Direct access not allowed!");

if (empty($edit_id)) {
    echo '<h1>Редактировать страницу</h1>';
    $edit_id = 0;
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
    var tb_els = 'text1, text2';
    $().ready(function () {
        tinyMCE.init($.extend(TM.cfg1, {
            elements: tb_els
        }));

        /*$('[name=form1]').submit(function(){
         if($('[name=name]').length && $('[name=name]').val()=='' && $('[name=edit_id]').val()>=0) {
         alert('Не введено название бренда');
         return false;
         }
         })*/
    });
</script>

<div class="edit_area">
    <form action="" method="post" enctype="multipart/form-data" name="form1">
        <input type="hidden" name="edit_id" value="<?= $edit_id; ?>">
        <input type="hidden" name="act" value="<?= $act; ?>">
        <input type="hidden" name="gr" value="<?= $gr; ?>">
        <input type="hidden" name="page_id" value="<?= @$page_id; ?>">
        <input type="hidden" name="MAX_FILE_SIZE" value="<?=Cfg::get('max_file_size');?>">
        <!---->
        <input type="hidden" name="vendor_id" value="<?= $vendor_id; ?>">
        <input type="hidden" name="model_id" value="<?= $model_id; ?>">
        <input type="hidden" name="year_id" value="<?= $year_id; ?>">
        <input type="hidden" name="modif_id" value="<?= $modif_id; ?>">
        <!---->
        <input name="ae_post" type="submit" id="ae_post" value="Записать"/>
        <input type="button" value="Веpнуться без записи"
               onclick="window.location.replace('/cms/frm/podbor_pages.php?gr=<?= $gr; ?>');return false;"/>
        <br/>
        <p><b>Адрес страницы:</b>&nbsp;&nbsp;<a href="<?=@$page_url?>" target="_blank"><?=@$page_url?></a></p>
        <br>
        <hr>
        <p><strong>Текст сверху</strong>
        </p>
        <textarea class="TM" name="text1"
                  style="width:100%; height:350px"><?= Tools::taria(@$page_info['text1']) ?></textarea>

        <p><strong>Текст снизу</strong>
        </p>
        <textarea class="TM" name="text2"
                  style="width:100%; height:450px"><?= Tools::taria(@$page_info['text2']) ?></textarea>
        <!--Новые SEO поля-->
        <h2>Дополнительные данные</h2>
        <table width="800">
            <tr>
                <td>Заголовок H1:</td>
                <td><input type="text" name="seo_h1" value="<?= @$page_info['seo_h1'] ?>" style="width: 650px"/></td>
            </tr>
            <tr>
                <td>Заголовок H2:</td>
                <td><input type="text" name="seo_h2" value="<?= @$page_info['seo_h2'] ?>" style="width: 650px"/></td>
            </tr>
            <tr>
                <td>SEO Title:</td>
                <td><input type="text" name="seo_title" value="<?= @$page_info['seo_title'] ?>" style="width: 650px"/>
                </td>
            </tr>
            <tr>
                <td>SEO Description:</td>
                <td><textarea name="seo_desc" style="width: 650px; height: 65px;"><?= @$page_info['seo_desc'] ?></textarea></td>
            </tr>
            <tr>
                <td>SEO Keywords:</td>
                <td><input type="text" name="seo_key" value="<?= @$page_info['seo_key'] ?>" style="width: 650px"/></td>
            </tr>
            <tr>
                <td>SEO оптимизация:</td>
                <td>
                    <label style="float: left;"><input type="radio" name="is_seo" value="0" <?=(@$page_info['is_seo'] == 0 ? 'checked="true"' : '')?> class="ui-corner-all">Нет</label>
                    <label><input type="radio" name="is_seo" value="1" <?=(@$page_info['is_seo'] == 1 ? 'checked="true"' : '')?> class="ui-corner-all">Да</label>
                </td>
            </tr>
            <tr>
                <td>Изображение авто</td>
                <td class="white">
                    <?
                    if(!empty($page_info['avto_image'])){
                    $cc = new CC_Ctrl();
                    $i1_ = $cc->make_img_path($page_info['avto_image']);
                    ?><fieldset class="ui" style="float:left; margin:0 15px 15px; border:1px dashed #999; width:200px; overflow:hidden; padding:10px;"><legend class="ui">Изображение</legend>
                        <div style="float:left; overflow:hidden; margin-right:20px; max-width: 110px">
                           <img width="100" src="<?=$i1_?>" />
                        </div>
                        <div style="float:left; overflow:hidden; margin-right:10px; width: 190px">
                            <p><input name="delImg" type="checkbox" id="delImg" value="1"> <label for="delImg">удалить изображение</label></p>
                        </div>
                    </fieldset>
                    <fieldset class="ui" style="border:1px dashed #999; padding: 10px"><legend class="ui">Земенить изображение</legend>
                        <p><label>Файл</label><br /><input name="imgFile" type="file" id="imgFile" style="width: 99%"></p>
                        <p><labeL>Загрузка по урлу http://</labeL><br /><input name="spyUrl" type="text" id="spyUrl"  style="width: 99%;" /></p>
                    </fieldset><?

                    } else {
                        ?><fieldset class="ui" style="border:1px dashed #999; padding: 10px; width: 99%"><legend class="ui">Загрузить изображение</legend>
                            <p><label>Файл</label><br /><input name="imgFile" type="file" id="imgFile" style="width: 99%"></p>
                            <p><labeL>Загрузка по урлу http://</labeL><br /><input name="spyUrl" type="text" id="spyUrl"  style="width: 99%;" /></p>
                        </fieldset><?
                    }

                    ?>
                </td>
            </tr>
            <tr>
                <td>Показывать рейтинг авто:</td>
                <td>
                    <label style="float: left;"><input type="radio" name="show_rating" value="0" <?=(@$page_info['show_rating'] == 0 ? 'checked="true"' : '')?> class="ui-corner-all">Нет</label>
                    <label><input type="radio" name="show_rating" value="1" <?=(@$page_info['show_rating'] == 1 ? 'checked="true"' : '')?> class="ui-corner-all">Да</label>
                </td>
            </tr>
            <?
            if (@$page_info['year_id']==0 && @$page_info['modif_id']==0) {
                ?><tr>
                    <td>Показывать на главной:</td>
                    <td>
                        <input type="checkbox" name="showOnTheMain" value="1" <?=(@$page_info['showOnTheMain'] == 1 ? 'checked="true"' : '')?>>
                        <span style="margin-left:30px;">Сортировка: <input id="sortOnTheMain" value="<?=@$page_info['sortOnTheMain']?>" type="text" name="sortOnTheMain" style="width: 20px;text-align: center;"></span>
                    </td>
                </tr><?
            }
            ?>
        </table>
        <hr>
        <br>
        <!--Новые SEO поля END-->
        <input name="ae_post" type="submit" id="ae_post" value="Записать"/>
        <input type="submit" value="Веpнуться без записи"
               onclick="window.location.replace('/cms/frm/podbor_pages.php?gr=<?= $gr; ?>');return false;"/>

    </form>

</div>
