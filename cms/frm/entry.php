<? require_once '../auth.php';
include('../struct.php');

$cp->frm['name']='entry';
$cp->frm['title']='Управление записями';

$cp->checkPermissions();

cp_head();
cp_css();
cp_js();

cp_body();
cp_title();

foreach ($_GET as $key=>$value) $$key=$value;
foreach ($_POST as $key=>$value) $$key=$value;

$ss=new Entry();
$ess=new Entrysection();

function pf($edit=false)
{
    global $ss;
    global $ess;


    ?>
    <script type="text/javascript">

        $(document).ready(function () {

            var tb_els = 'sstext';
            tinyMCE.init($.extend(TM.cfg1, {
                elements: tb_els
            }));

            $('[name=form1]').submit(function () {
                if ($('[name=title]').length && $('[name=title]').val() == '' && ($('[name=act]').val() == 'add_entry' || $('[name=act]').val() == 'edit_entry')) {
                    alert('Название записи не должен быть пустым');
                    return false;
                }
            })

        });
    </script>
<input type="hidden" name="drop_img" value="-1">
<input type="hidden" name="act" value="-1">
<input type="hidden" name="edit" value="<?= $_POST['edit_entry'] ?>">
<input type="hidden" name="edit_entry" value="-1">
    <div class="edit_area">

        <input type="submit" onClick="document.forms['form1'].act.value='<?= $edit ? 'edit_entry' : 'add_entry' ?>'"
               value="Записать"><input type="submit" value="Вернуться без записи"
                                       onclick="document.forms['form1'].act.value=-1">



        <table width="100%" border="0" cellspacing="20" cellpadding="5">
            <tr>
                <td><strong>Заголовок H1</strong><br><input type="text" name="title" style="width:100%" value="<?=$edit?htmlspecialchars($ss->qrow['title']):''?>"></td>
                <?
                $ess->que('entry_section');
                if (!$ess->qnum()) {?>
                    <td>
                        <b>Категорий нет.</b>
                    </td>
                    <?
                }else{?>
                    <td>
                        <b>Категория</b><br>
                        <select name="entry_section_id" style="width: 100%;">
                            <?
                            while($ess->next()!=false){?>
                                <option value="<?=$ess->qrow['entry_section_id']?>" <?=($ss->qrow['entry_section_id'] == $ess->qrow['entry_section_id'])?' selected':''?>><?=Tools::html($ess->qrow['title'])?></option>
                            <? }?>
                        </select>
                    </td>
                <? }?>
                <td><b>Дата публикации</b><br><input type="date" name="dt_published" value="<?=(!empty($ss->qrow['dt_published']) ? $ss->qrow['dt_published'] : date("Y-m-d"))?>">
                &nbsp;&nbsp;&nbsp;<input type="checkbox" name="published" value="1"<?=$edit && $ss->qrow['published'] || !$edit?' checked="checked"':''?>"><b>Опубликовано</b></td>
            </tr>
            <tr>
                <td><strong>URL<br><small>(допустимые символы:<br>
                            a-z0-9_-)</small>
                    </strong><br><input name="sname" type="text"  id="sname" style="width:100%" value="<?=@$ss->qrow['sname']?>" />
                    <br />
                    <small>оставьте пустым для автоматической генерации</small>
                    <br><strong>Аннотация</strong><br><textarea style="width:100%;height:100px" name="intro"><?=$edit?Tools::taria($ss->qrow['intro']):''?></textarea>
                </td>
                <?	if (@$ss->qrow['img1']==''){?>
                    <td valign="top"><b>Присоединить изображение</b><br><input type="file" name="img1"></td>
                <? }else{?>
                    <td valign="top"><a href="<?=$ss->makeImgPath(1)?>" target="_blank"><img src="<?=$ss->makeImgPath(1)?>" style="max-width: 100px; height: auto;" alt="Изображение"></a><br><a href="javascript:;" onClick="document.forms['form1'].drop_img.value=1; document.forms['form1'].edit_entry.value=<?=$_POST['edit_entry']?>; document.forms['form1'].act.value='<?=$edit?'edit_entry':'add_entry'?>'; document.forms['form1'].submit(); return false;"><img src="/cms/img/b_drop.png" border="0"></a></td>
                <? }?>
            </tr>
        </table>
        <br>
        <strong>Текст записи</strong> <button class="TM_sw" forel="sstext">/</button>
        <br>
        <textarea id="ed_text" class="TM" name="sstext" style="width:100%; height:460px"><?=Tools::taria(@$ss->qrow['text'])?></textarea><br />

        <!--Мета-теги-->
        <p><strong>Title</strong></p>
        <textarea name="seo_title" style="width: 99%; height: 70px;"><?=Tools::taria(@$ss->qrow['seo_title'])?></textarea>
        <p><strong>Description</strong></p>
        <textarea name="description" style="width: 99%; height: 70px;"><?=Tools::taria(@$ss->qrow['description'])?></textarea>
        <p><strong>Keywords</strong></p>
        <textarea name="keywords" style="width: 99%; height: 70px;"><?=Tools::taria(@$ss->qrow['keywords'])?></textarea>
        <p><strong>SEO оптимизация:</strong></p>
        <label><input style="float: left;" type="radio" name="is_seo" value="1" <?=(@$ss->qrow['is_seo'] == 1 ? 'checked="true"' : '')?> class="ui-corner-all">Да</label>
        <label><input type="radio" name="is_seo" value="0" <?=(@$ss->qrow['is_seo'] == 0 ? 'checked="true"' : '')?> class="ui-corner-all">Нет</label>


        <p><input type="submit" onClick="document.forms['form1'].act.value='<?=$edit?'edit_entry':'add_entry'?>'" value="Записать"><input type="submit" value="Вернуться без записи"></p>
    </div>

<? }?>

    <form method="post" name="form1" enctype="multipart/form-data">
        <input type="hidden" name="MAX_FILE_SIZE" value="<?=Cfg::get('max_file_size')?>">
        <?
        if(@$act=='add_entry'){
            $is='';
            $sname=Tools::str2iso(trim($sname)!=''?$sname:$title,Cfg::get('SNAME_CNT_LEN'),Cfg::get('SNAME_CNT_REG'));
            $sname0=$sname;
            do{
                $sname=$sname0.($is!=''?"_$is":'');
                $sname1=Tools::like($sname);
                $ss->query("SELECT count(entry_id) FROM entry WHERE (sname LIKE '$sname1')");
                $ss->next();
                if($ss->qrow[0]) $is++;
            }while ($ss->qrow[0]);
            $text=@$tmh_sstext!=''?$tmh_sstext:$sstext;
            $text=Tools::untaria($text);
            $intro=Tools::untaria($intro);
            $description=Tools::untaria($description);
            $keywords=Tools::untaria($keywords);
            $published=@$published;
            //$dt_published = fdate($dt_published);
            $es_id = $entry_section_id;
            $dt_added=Tools::dt();
            if (!$ss->query("INSERT INTO entry (entry_section_id, sname, title, `seo_title`, `is_seo`, intro, text, description, keywords, dt_added, published, dt_published) VALUES('$es_id', '$sname', '$title', '$seo_title', '$is_seo', '$intro', '$text', '$description', '$keywords', '$dt_added', '$published', '$dt_published')")) warn('<p><strong>Ошибка. запись не добавлена.</strong></p>');
            else{
                note ('<p>Запись добавлена</p>');
                $id=$ss->lastId();
                if (@$_FILES['img1']['name']!='') if (!($ss->imgUpload('entry',$id,1,'img1'))) warn('<p><strong>Файл не передан!</strong></p>');
            }
        }

        if (@$act=='edit_entry'){
            $is='';
            $sname=Tools::str2iso(trim($sname)!=''?$sname:$title,Cfg::get('SNAME_CNT_LEN'),Cfg::get('SNAME_CNT_REG'));
            $sname0=$sname;
            do{
                $sname=$sname0.($is!=''?"_$is":'');
                $sname1=Tools::like($sname);
                $ss->query("SELECT count(entry_id) FROM entry WHERE (sname LIKE '$sname1')AND(entry_id!='$edit')");
                $ss->next();
                if($ss->qrow[0]) $is++;
            }while ($ss->qrow[0]);
            $text=@$tmh_sstext!=''?$tmh_sstext:$sstext;
            $text=Tools::untaria($text);
            $intro=Tools::esc($intro);
            $description=Tools::untaria($description);
            $keywords=Tools::untaria($keywords);
            $published=@$published;
            $seo_title=@$seo_title;
            $is_seo=intval(@$is_seo);
            //$dt_published = fdate($dt_published);
            $es_id = $entry_section_id;
            if(!$ss->query("UPDATE `entry` SET `entry_section_id` = '$es_id', `sname` = '$sname', `title` = '$title', `seo_title` = '$seo_title', `is_seo` = '$is_seo', `intro` = '$intro', `text` = '$text', `description` = '$description', `keywords` = '$keywords', `published` = '$published', `dt_published` = '$dt_published' WHERE `entry_id` = $edit")) echo '<strong>Ошибка. Запись не отредактирована.</strong>';
            else{
                if (@$_FILES['img1']['name']!='') if (!($ss->imgUpload('entry',$edit,1,'img1'))) warn('<p><strong>Файл не передан!</strong></p>');
            }
        }
        if (@$drop_img>0){
            if ($ss->imgDel('entry',(int)$edit_entry,(int)$drop_img)) note('<p>Изображение удалено.</p>');
        }
        if (@$drop_news>0){
            $ss->ld('entry','entry_id',(int)$drop_entry);
        }
        if (@$drop_entry){
            if(!$ss->query('DELETE FROM `entry` WHERE `entry_id` = '.$drop_entry)) echo '<strong>Ошибка. Запись не удалена.</strong>';
        }

        if (@$add_entry>0){?>
            <h3>Добавление записи</h3>
            <? pf();?>
        <? }elseif(@$edit_entry>0){?>
            <h3>Редактирование записи</h3>
            <?
            $ss->que('entry_by_id',(int)$edit_entry);
            pf(true);
        }else{?>
            <input type="hidden" name="add_entry" value="-1">
            <input type="hidden" name="edit_entry" value="-1">
            <input type="hidden" name="drop_entry" value="-1">

            <input type="submit" value="+ Добавить запись." onClick="document.forms['form1'].add_entry.value=1"><br>
            <?

            $l=1;
            $ss->que('entry');
            if(!$ss->qnum()) note('Записей нет.');
            else{?>
                <br><table class="ui-table ltable">
                    <tr>
                        <th>Изображение</th>
                        <th>Заголовок</th>
                        <th>Категория</th>
                        <!--<th>Аннотация</th>
                        <th>Текст</th>-->
                        <th>sname</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Keywords</th>
                        <th>Дата публикации</th>
                        <th>Опубликовано</th>
                        <th>Оптимизированно</th>
                        <th>Удалить</th>
                    </tr>
                    <?
                    while($ss->next()!=false){?>
                        <tr>
                            <td><?if(!empty($ss->qrow['img1'])):?><a title='<?=$ss->qrow['sname']?>' href="javascript://" onClick="document.forms['form1'].edit_entry.value=<?=$ss->qrow['entry_id']?>; document.forms['form1'].submit(); return false"><img src="<?=$ss->makeImgPath(1)?>" alt="Картинка" style="max-width: 150px;"></a><?else:?>нет<?endif;?></td>
                            <td><a title='<?=$ss->qrow['sname']?>' href="javascript://" onClick="document.forms['form1'].edit_entry.value=<?=$ss->qrow['entry_id']?>; document.forms['form1'].submit(); return false"><?=Tools::html($ss->qrow['title'])?></a></td>
<!--                            <td align="center">--><?// if(mb_strlen(trim($ss->qrow['intro']))!='') echo 'есть'; else echo 'нет';?><!--</td>-->
                            <?
                            $ess->que('entry_section');
                            if (!$ess->qnum()) {?>
                                <td>
                                    Категорий нет.
                                </td>
                            <?} else {?>
                                <td>
                                    <?
                                    while($ess->next()!=false){?>
                                        <?=($ss->qrow['entry_section_id'] == $ess->qrow['entry_section_id'])? Tools::html($ess->qrow['title']) : ''?>
                                    <? }?>
                                </td>
                            <?}?>
                            <!--<td align="left"><?/*=trim($ss->qrow['intro'])*/?></td>
                            <td align="left"><?/*=trim($ss->qrow['text'])*/?></td>-->
                            <td align="center"><?=$ss->qrow['sname']?></td>
<!--                            <td align="center">--><?//=!empty($ss->qrow['description'])?'есть':'нет'?><!--</td>-->
                            <td align="center"><?=$ss->qrow['seo_title']?></td>
                            <td align="center"><?=$ss->qrow['description']?></td>
<!--                            <td align="center">--><?//=!empty($ss->qrow['keywords'])?'есть':'нет'?><!--</td>-->
                            <td align="center"><?=$ss->qrow['keywords']?></td>
                            <td align="center"><?=$ss->qrow['dt_published']?></td>
                            <td align="center"><? if($ss->qrow['published']) echo 'да'; else echo 'нет';?></td>
                            <td align="center"><? if($ss->qrow['is_seo']) echo 'да'; else echo 'нет';?></td>
                            <td align="center"><a href="javascript://" onClick="if (confirm('Удалить?')) {document.forms['form1'].drop_entry.value=<?=$ss->qrow['entry_id']?>; document.forms['form1'].submit(); return false}"><img src="/cms/img/b_drop.png" border="0"></a></td>
                        </tr>
                    <? }?>
                </table>
            <? }}?>
    </form>

<? cp_end();
