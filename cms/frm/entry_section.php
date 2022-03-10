<? require_once '../auth.php';
include('../struct.php');

$cp->frm['name']='entry_section';
$cp->frm['title']='Управление категориями';

$cp->checkPermissions();

cp_head();
cp_css();
cp_js();

cp_body();
cp_title();

foreach ($_GET as $key=>$value) $$key=$value;
foreach ($_POST as $key=>$value) $$key=$value;

$ess=new Entry();
$ss=new Entrysection();

function pf($edit=false)
{
    global $ss;


    ?>
    <script type="text/javascript">

        $(document).ready(function(){

            var tb_els='sstext';
            tinyMCE.init($.extend(TM.cfg1,{
                elements: tb_els
            }));

            $('[name=form1]').submit(function(){
                if($('[name=title]').length && $('[name=title]').val()=='' && ($('[name=act]').val()=='add_entry_section' || $('[name=act]').val()=='edit_entry_section')) {
                    alert('Название категории не должен быть пустым');
                    return false;
                }
            })

        });
    </script>

    <input type="hidden" name="act" value="-1">
    <input type="hidden" name="edit" value="<?=$_POST['edit_news']?>">
    <input type="hidden" name="edit_news" value="-1">
    <div class="edit_area">

        <input type="submit" onClick="document.forms['form1'].act.value='<?=$edit?'edit_entry_section':'add_entry_section'?>'" value="Записать"><input type="submit" value="Вернуться без записи" onclick="document.forms['form1'].act.value=-1">

        <table width="100%" border="0" cellspacing="0" cellpadding="5">
            <tr>
                <td><b>Опубликовано</b></td>
                <td><input type="checkbox" name="published" value="1"<?=$edit && $ss->qrow['published'] || !$edit?' checked="checked"':''?>"></td>

            </tr>
            <tr>
                <td><strong>Заголовок</strong></td>
                <td><input type="text" name="title" style="width:100%" value="<?=$edit?htmlspecialchars($ss->qrow['title']):''?>"></td>
            </tr>
            <tr>
                <td nowrap><strong>Псевдоним<br><small>(допустимые символы:<br>
                            a-z0-9_-)</small>
                    </strong></td>
                <td><input name="sname" type="text"  id="sname" style="width:100%" value="<?=@$ss->qrow['sname']?>" />
                    <br />
                    <small>оставьте пустым для автоматической генерации</small></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td valign="top"><strong>Аннотация</strong></td>
                <td><textarea style="width:100%;height:100px" name="intro"><?=$edit?Tools::taria($ss->qrow['intro']):''?></textarea></td>
            </tr>
        </table>

        <strong>Текст новости</strong> <button class="TM_sw" forel="sstext">/</button>
        <textarea class="TM" name="sstext" style="width:100%; height:460px"><?=Tools::taria(@$ss->qrow['text'])?></textarea><br />


        <p><strong>Description</strong></p>
        <textarea name="description" style="width: 99%; height: 70px;"><?=Tools::taria(@$ss->qrow['description'])?></textarea>
        <p><strong>Keywords</strong></p>
        <textarea name="keywords" style="width: 99%; height: 70px;"><?=Tools::taria(@$ss->qrow['keywords'])?></textarea>

        <p><input type="submit" onClick="document.forms['form1'].act.value='<?=$edit?'edit_entry_section':'add_entry_section'?>'" value="Записать"><input type="submit" value="Вернуться без записи"></p>
    </div>

<? }?>




    <form method="post" name="form1" enctype="multipart/form-data">
        <input type="hidden" name="MAX_FILE_SIZE" value="<?=Cfg::get('max_file_size')?>">
        <?
        if(@$act=='add_entry_section'){
            $is='';
            $sname=Tools::str2iso(trim($sname)!=''?$sname:$title,Cfg::get('SNAME_CNT_LEN'),Cfg::get('SNAME_CNT_REG'));
            $sname0=$sname;
            do{
                $sname=$sname0.($is!=''?"_$is":'');
                $sname1=Tools::like($sname);
                $ss->query("SELECT count(entry_section_id) FROM entry_section WHERE (sname LIKE '$sname1')");
                $ss->next();
                if($ss->qrow[0]) $is++;
            }while ($ss->qrow[0]);
            $text=@$tmh_sstext!=''?$tmh_sstext:$sstext;
            $text=Tools::untaria($text);
            $intro=Tools::untaria($intro);
            $description=Tools::untaria($description);
            $keywords=Tools::untaria($keywords);
            $published=@$published;
            if (!$ss->query("INSERT INTO entry_section (sname, title, intro, text, description, keywords, published) VALUES('$sname', '$title','$intro', '$text', '$description', '$keywords', '$published')")) warn('<p><strong>Ошибка. Категория не добавлена.</strong></p>');
            else{
                note ('<p>Категория добавлена</p>');
                $id=$ss->lastId();
            }
        }

        if (@$act=='edit_entry_section'){
            $is='';
            $sname=Tools::str2iso(trim($sname)!=''?$sname:$title,Cfg::get('SNAME_CNT_LEN'),Cfg::get('SNAME_CNT_REG'));
            $sname0=$sname;
            do{
                $sname=$sname0.($is!=''?"_$is":'');
                $sname1=Tools::like($sname);
                $ss->query("SELECT count(entry_section_id) FROM entry_section WHERE (sname LIKE '$sname1')AND(entry_section_id!='$edit')");
                $ss->next();
                if($ss->qrow[0]) $is++;
            }while ($ss->qrow[0]);
            $text=@$tmh_sstext!=''?$tmh_sstext:$sstext;
            $text=Tools::untaria($text);
            $intro=Tools::esc($intro);
            $description=Tools::untaria($description);
            $keywords=Tools::untaria($keywords);
            $published=@$published;
            /*if(!$ss->query("UPDATE `entry_section` SET `title`='$title', `text`='$text', `intro`='$intro', `sname`='$sname', `description`='$description', `keywords`='$keywords', `published`='$published', WHERE `entry_section_id`=$edit")) echo '<strong>Ошибка. Категория не отредактирована.</strong>';*/
            if(!$ss->query("UPDATE `entry_section` SET `sname` = '$sname', `title` = '$title', `intro` = '$intro', `text` = '$text', `description` = '$description', `keywords` = '$keywords', `published` = '$published' WHERE `entry_section_id` = $edit")) echo '<strong>Ошибка. Категория не отредактирована.</strong>';
        }

        if (@$drop_entry_section){
            if(!$ss->query('DELETE FROM `entry_section` WHERE `entry_section_id` = '.$drop_entry_section)) echo '<strong>Ошибка. Запись не удалена.</strong>';
            $ess->query("UPDATE `entry` SET `entry_section_id` = '' WHERE `entry_section_id` = $drop_entry_section");
        }

        if (@$add_news>0){?>
            <h3>Добавление категории</h3>
            <? pf();?>
        <? }elseif(@$edit_news>0){?>
            <h3>Редактирование категории</h3>
            <?
            $ss->que('entry_section_by_id',(int)$edit_news);
            pf(true);
        }else{?>
            <input type="hidden" name="add_news" value="-1">
            <input type="hidden" name="edit_news" value="-1">
            <input type="hidden" name="drop_entry_section" value="-1">

            <input type="submit" value="+ Добавить категорию." onClick="document.forms['form1'].add_news.value=1"><br>
            <?

            $l=1;
            $ss->que('entry_section',@$group_id);
            if(!$ss->qnum()) note('Категорий нет.');
            else{?>
                <br><table class="ui-table ltable">
                    <tr>
                        <th>Заголовок</th>
                        <th>Аннотация</th>
                        <th>Текст</th>
                        <th>sname</th>
                        <th>Description</th>
                        <th>Keywords</th>
                        <th>Опубликовано</th>
                        <th>Удалить</th>
                    </tr>
                    <?
                    while($ss->next()!=false){?>
                        <tr>
                            <td><a title='<?=$ss->qrow['sname']?>' href="javascript://" onClick="document.forms['form1'].edit_news.value=<?=$ss->qrow['entry_section_id']?>; document.forms['form1'].submit(); return false"><?=Tools::html($ss->qrow['title'])?></a></td>
<!--                            <td align="center">--><?// if(mb_strlen(trim($ss->qrow['intro']))!='') echo 'есть'; else echo 'нет';?><!--</td>-->
                            <td align="left"><?=trim($ss->qrow['intro'])?></td>
                            <td align="left"><?=trim($ss->qrow['text'])?></td>
                            <td align="center"><?=$ss->qrow['sname']?></td>
<!--                            <td align="center">--><?//=!empty($ss->qrow['description'])?'есть':'нет'?><!--</td>-->
                            <td align="center"><?=$ss->qrow['description']?></td>
<!--                            <td align="center">--><?//=!empty($ss->qrow['keywords'])?'есть':'нет'?><!--</td>-->
                            <td align="center"><?=$ss->qrow['keywords']?></td>

                            <td align="center"><? if($ss->qrow['published']) echo 'да'; else echo 'нет';?></td>
                            <td align="center"><a href="javascript://" onClick="if (confirm('Удалить?')) {document.forms['form1'].drop_entry_section.value=<?=$ss->qrow['entry_section_id']?>; document.forms['form1'].submit(); return false}"><img src="/cms/img/b_drop.png" border="0"></a></td>
                        </tr>
                    <? }?>
                </table>
            <? }}?>
    </form>

<? cp_end();
