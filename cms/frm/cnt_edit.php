<? if (!defined('true_enter')) die ("Direct access not allowed!");

?>

<script type="text/javascript">

    var noEditor=<?=(int)@$ss->qrow['no_editor']?>;

    var tm={
        inited:false,
        init: function(){
            tinyMCE.init($.extend(TM.cfg1,{
                elements: 'sstext',
                oninit: function(){

                    tm.inited=true;

                }
            }));
        }
    };

    $(document).ready(function(){

        if(!noEditor) tm.init();

        $('#no_editor').change(function(e){
            var v=$(this).get(0).checked;
            if(v) {
                if(tm.inited) $('[name=sstext]').tinymce().hide();
                //tinyMCE.execCommand('mceRemoveControl', false, 'sstext');
            } else {
                if(!tm.inited) tm.init(); else $('[name=sstext]').tinymce().show();
                $('[name="tmh_sstext"]').html('');
                //tinyMCE.execCommand('mceAddControl', false, 'sstext');
            }
        });

        $('[name=form1]').submit(function(){
            if($('[name=title]').length && $('[name=title]').val()=='' && ($('[name=act]').val()=='add_post' || $('[name=act]').val()=='edit_post')) {
                alert('Заголовок должен быть пустым');
                return false;
            }
        })


    });
</script>


<?=$act=='add'?'<h3>Добавить статью</h3>':"<h3>Изменить статью &quot;$title&quot;</h3>"?>
<? if(!empty($tDescr)) {
    ?><div style="border: 1px dashed #ccc; border-radius:5px; padding:15px; margin: 10px 0"><?=$tDescr?></div><?
}?>
<input type="hidden" name="cnt_id" value="<?=$cnt_id?>">
<input type="hidden" name="cnt_type_id" value="<?=$cnt_type_id?>">
<input name="drop_img" type="hidden" value="-1">
<div class="edit_area">
    <input type="submit" onClick="document.forms['form1'].act.value='<?=$act=='add'?'add_post':'edit_post'?>'" value="Записать"> <input type="submit" value="Вернуться не сохраняя" onclick="document.forms['form1'].act.value=-1">
    <table width="100%" border="0" cellspacing="0" cellpadding="5">
        <tr>
            <td><b>Рубрика</b></td>
            <td><select name="cnt_type_id"><? getTree($tree,$cnt_type_id);?></select></td>
        </tr>
        <tr>
            <td valign="top"><strong>Псевдоним</strong></td>
            <td width="100%"><input type="text" style="width:100%" name="sname" value="<?=$cnt_id!=-1?$ss->qrow['sname']:''?>"> <br />
                <sub><small>(допустимые символы: a-z0-9_-)</small><br />(оставьте поле пустым для автоматического создания системного имени в транслитерации)</sub></label></td>
        </tr>
        <tr>
            <td><strong>Заголовок</strong></td>
            <td><input type="text" style="width:100%" name="title" value="<?=$cnt_id!=-1?Tools::html($ss->qrow['title']):''?>"></td>
        </tr>
        <tr>
            <td><strong>Ссылка</strong></td>
            <td>
                <input type="text" style="width:100%" name="link" value="<?=$cnt_id!=-1?Tools::html($ss->qrow['link']):''?>">
            </td>
        </tr>
        <tr>
            <td><strong>Позиция</strong><br><small>большее значение - выше в списке</small></td><td>
                    <table>
                        <tr>
                            <td>
                                <input name="pos" id="pos" type="text" size="3" value="<?=@$ss->qrow['pos']?>">
                            </td>
                            <td style="padding: 0 10px 0 20px"><b>Опубликовано</b></td>
                            <td><input type="checkbox" name="published" value="1"<?=$cnt_id!=-1 && $ss->qrow['published'] || $cnt_id==-1?' checked="checked"':''?>"></td>
                            <td nowrap style="padding: 0 10px 0 20px"><b>Дата публикации</b></td>
                            <td><input name="publishedDate" id="pos" type="text" style="width:80px" value="<?=$cnt_id!=-1?sdate($ss->qrow['publishedDate']):date("d-m-Y")?>"></td>
                        </tr>
                    </table>

            </td>
        </tr>
        <tr>
            <td valign="top"><strong>Вступление</strong></td>
            <td><textarea name="intro" style="width:100%; height:100px" id="intro"><?=$cnt_id!=-1?Tools::taria($ss->qrow['intro']):''?></textarea></td>
        </tr>
        <tr>
            <td nowrap="nowrap">Не использовать
                редактор</td>
            <td><input name="no_editor" type="checkbox" id="no_editor" value="1" <?=($cnt_id!=-1 && $ss->qrow['no_editor']==1)?'checked':''?> />
                <small>(состояние переключателя сохраняется)</small>
            </td>
        </tr>
    </table>


    <p><strong>Текстовое содержание</strong></p>
    <textarea class="TM" name="sstext" style="width:100%; height:560px"><?=Tools::taria(@$ss->qrow['text'])?></textarea>


    <table width="100%" border="0" cellspacing="0" cellpadding="5">
        <tr>
            <td nowrap>Изображение 1<br /><small>* автоматическое изменение размера<br /> не производиться</small></td>
            <td><?	if (@$ss->qrow['img1']==''){?>
                    <input type="file" name="img1">
                <? }else{?>
                    <a href="<?=$ss->makeImgPath(1)?>" target="_blank" class="highslide"><img src="<?=$ss->imgPath . '?' . mt_rand()?>" width="100"></a>
                    <a href="javascript:;" onClick="document.forms['form1'].drop_img.value=1;document.forms['form1'].act.value='<?=$act=='add'?'add_post':'edit_post'?>'; document.forms['form1'].submit(); return false;"><img src="/cms/img/b_drop.png" border="0"></a>
                <? }?>
            </td>
        </tr>
        <tr>
            <td>Изображение 2<br /><small>* автоматическое изменение размера<br /> не производиться</small></td>
            <td><?	if (@$ss->qrow['img2']==''){?>
                    <input type="file" name="img2">
                <? }else{?>
                    <a href="<?=$ss->makeImgPath(2)?>" target="_blank" class="highslide"><img src="<?=$ss->imgPath . '?' . mt_rand()?>" width="100"></a>
                    <a href="javascript:;" onClick="document.forms['form1'].drop_img.value=2;document.forms['form1'].act.value='<?=$act=='add'?'add_post':'edit_post'?>'; document.forms['form1'].submit(); return false;"><img src="/cms/img/b_drop.png" border="0"></a>
                <? }?></td>
        </tr>
        <tr><td>Description</td><td width="100%"><textarea style="width:100%; height:70px" name="discription"><?=$cnt_id!=-1?Tools::taria($ss->qrow['description']):''?></textarea></td></tr>

        <tr><td nowrap="nowrap">Keywords</td><td><textarea style="width:100%; height:70px" name="keywords"><?=$cnt_id!=-1?Tools::taria($ss->qrow['keywords']):''?></textarea></td></tr>
        <tr>
            <td><input type="submit" onClick="document.forms['form1'].act.value='<?=$act=='add'?'add_post':'edit_post'?>'" value="Записать"></td>
            <td><input type="submit" value="Вернуться не сохраняя"></td>
        </tr>
    </table>
</div>