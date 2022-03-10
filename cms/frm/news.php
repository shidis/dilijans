<? require_once '../auth.php';
include('../struct.php');

$cp->frm['name']='news';
$cp->frm['title']='Ленты новостей';

$cp->checkPermissions();

cp_head();
cp_css();
cp_js();

cp_body();
cp_title();

foreach ($_GET as $key=>$value) $$key=$value;
foreach ($_POST as $key=>$value) $$key=$value;

$ss=new News();

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
                if($('[name=title]').length && $('[name=title]').val()=='' && ($('[name=act]').val()=='add_post' || $('[name=act]').val()=='edit_post')) {
                    alert('Заголовок новости не должен быть пустым');
                    return false;
                }
            })

        });
    </script>

    <input type="hidden" name="drop_img" value="-1">
    <input type="hidden" name="act" value="-1">
    <input type="hidden" name="edit" value="<?=$_POST['edit_news']?>">
    <input type="hidden" name="group_id" value="<?=$_POST['group_id']?>">
    <input type="hidden" name="edit_news" value="-1">
    <div class="edit_area">

        <input type="submit" onClick="document.forms['form1'].act.value='<?=$edit?'edit_post':'add_post'?>'" value="Записать"><input type="submit" value="Вернуться без записи" onclick="document.forms['form1'].act.value=-1">

        <table width="100%" border="0" cellspacing="0" cellpadding="5">
            <tr>
                <td><strong>Дата публикации</strong></td>
                <td width="50%">
                    <table>
                        <tr>
                            <td><input type="text" name="dt" style="width:80px" value="<?=$edit?sdate($ss->qrow['dt']):date("d-m-Y")?>"></td>
                            <td style="padding: 0 10px 0 20px"><b>Опубликовано</b></td>
                            <td><input type="checkbox" name="published" value="1"<?=$edit && $ss->qrow['published'] || !$edit?' checked="checked"':''?>"></td>
                        </tr>
                    </table>
                </td>
                <td width="50%" style="padding-left: 20px">
                    <?	if (@$ss->qrow['img1']==''){?>
                        Присоединить изображение 1
                        <input type="file" name="img1">
                    <? }else{?>
                        <a href="<?=$ss->makeImgPath(1)?>" target="_blank">Изображение 1</a>
                        <a href="javascript:;" onClick="document.forms['form1'].drop_img.value=1; document.forms['form1'].edit_news.value=<?=$_POST['edit_news']?>; document.forms['form1'].act.value='<?=$edit?'edit_post':'add_post'?>'; document.forms['form1'].submit(); return false;"><img src="/cms/img/b_drop.png" border="0"></a>
                    <? }?>
                </td>
            </tr>
            <tr>
                <td><strong>Заголовок</strong></td>
                <td><input type="text" name="title" style="width:100%" value="<?=$edit?htmlspecialchars($ss->qrow['title']):''?>"></td>
                <td style="padding-left: 20px">
                    <?	if (@$ss->qrow['img2']==''){?>
                        Присоединить изображение 2
                        <input type="file" name="img2">
                    <? }else{?>
                        <a href="<?=$ss->makeImgPath(2)?>" target="_blank">Изображение 2</a>
                        <a href="javascript:;" onClick="document.forms['form1'].drop_img.value=2; document.forms['form1'].edit_news.value=<?=$_POST['edit_news']?>; document.forms['form1'].act.value='<?=$edit?'edit_post':'add_post'?>'; document.forms['form1'].submit(); return false;"><img src="/cms/img/b_drop.png" border="0"></a>
                    <? }?>
                </td>
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
                <td colspan="2"><textarea style="width:100%;height:100px" name="intro"><?=$edit?Tools::taria($ss->qrow['intro']):''?></textarea></td>
            </tr>
        </table>

        <strong>Текст новости</strong> <button class="TM_sw" forel="sstext">/</button>
        <textarea class="TM" name="sstext" style="width:100%; height:460px"><?=Tools::taria(@$ss->qrow['text'])?></textarea><br />

        <table cellpaddind="5" width="99%"><tr><td align="left"><b>Ссылка</b><br><small>(например, первоисточник)</small></td><td width="100%"><input type="text" name="link" style="width:100%" value="<?=$edit?htmlspecialchars($ss->qrow['link']):''?>"></td></tr></table>

        <p><strong>Description</strong></p>
        <textarea name="description" style="width: 99%; height: 70px;"><?=Tools::taria(@$ss->qrow['description'])?></textarea>
        <p><strong>Keywords</strong></p>
        <textarea name="keywords" style="width: 99%; height: 70px;"><?=Tools::taria(@$ss->qrow['keywords'])?></textarea>

        <p><input type="submit" onClick="document.forms['form1'].act.value='<?=$edit?'edit_post':'add_post'?>'" value="Записать"><input type="submit" value="Вернуться без записи"></p>
    </div>

<? }?>




    <form method="post" name="form1" enctype="multipart/form-data">
        <input type="hidden" name="MAX_FILE_SIZE" value="<?=Cfg::get('max_file_size')?>">
        <?
        if(@$act=='add_post'){
            $is='';
            $sname=Tools::str2iso(trim($sname)!=''?$sname:$title,Cfg::get('SNAME_CNT_LEN'),Cfg::get('SNAME_CNT_REG'));
            $sname0=$sname;
            do{
                $sname=$sname0.($is!=''?"_$is":'');
                $sname1=Tools::like($sname);
                $ss->query("SELECT count(news_id) FROM ss_news WHERE (news_group_id='$group_id')AND(sname LIKE '$sname1')AND(NOT LD)");
                $ss->next();
                if($ss->qrow[0]) $is++;
            }while ($ss->qrow[0]);
            $text=@$tmh_sstext!=''?$tmh_sstext:$sstext;
            $text=Tools::untaria($text);
            $intro=Tools::untaria($intro);
            $description=Tools::untaria($description);
            $keywords=Tools::untaria($keywords);
            $dt=fdate($dt);
            $dt_added=Tools::dt();
            $published=@$published;
            $link=Tools::esc($link);
            if (!$dt) warn('<p><strong>Неправильный формат даты</strong></p>');
            if (!$ss->query("INSERT INTO ss_news (news_group_id, sname, title, intro, text, dt, description, keywords, dt_added, published, link) VALUES('$group_id', '$sname', '$title','$intro', '$text','$dt', '$description', '$keywords', '$dt_added', '$published', '$link')")) warn('<p><strong>Ошибка. Новость не добавлена.</strong></p>');
            else{
                note ('<p>Новость добавлена</p>');
                $id=$ss->lastId();
                if (@$_FILES['img1']['name']!='') if (!($ss->imgUpload('ss_news',$id,1,'img1'))) warn('<p><strong>Файл 1 не передан!</strong></p>');
                if (@$_FILES['img2']['name']!='') if (!($ss->imgUpload('ss_news',$id,2,'img2'))) warn('<p><strong>Файл 2 не передан!</strong></p>');
            }
        }
        if (@$act=='edit_post'){
            $is='';
            $sname=Tools::str2iso(trim($sname)!=''?$sname:$title,Cfg::get('SNAME_CNT_LEN'),Cfg::get('SNAME_CNT_REG'));
            $sname0=$sname;
            do{
                $sname=$sname0.($is!=''?"_$is":'');
                $sname1=Tools::like($sname);
                $ss->query("SELECT count(news_id) FROM ss_news WHERE (news_group_id='$group_id')AND(sname LIKE '$sname1')AND(news_id!='$edit')AND(NOT LD)");
                $ss->next();
                if($ss->qrow[0]) $is++;
            }while ($ss->qrow[0]);
            $text=@$tmh_sstext!=''?$tmh_sstext:$sstext;
            $text=Tools::untaria($text);
            $intro=Tools::esc($intro);
            $description=Tools::untaria($description);
            $keywords=Tools::untaria($keywords);
            $published=@$published;
            $dt=fdate($dt);
            $link=Tools::esc($link);
            if(!$dt) warn('<p><strong>Неправильный формат даты</strong></p>');
            if(!$ss->query("UPDATE ss_news SET title='$title', text='$text', intro='$intro', dt='$dt', sname='$sname', description='$description', keywords='$keywords', published='$published', link='$link' WHERE news_id='$edit'")) echo '<strong>Ошибка. Новость не отредактирована.</strong>';
            else{
                if (@$_FILES['img1']['name']!='') if (!($ss->imgUpload('ss_news',$edit,1,'img1'))) warn('<p><strong>Файл 1 не передан!</strong></p>');
                if (@$_FILES['img2']['name']!='') if (!($ss->imgUpload('ss_news',$edit,2,'img2'))) warn('<p><strong>Файл 2 не передан!</strong></p>');
            }
        }
        if (@$drop_img>0){
            if ($ss->imgDel('ss_news',(int)$edit_news,(int)$drop_img)) note('<p>Изображение удалено.</p>');
        }
        if (@$drop_news>0){
            $ss->ld('ss_news','news_id',(int)$drop_news);
        }
        if (@$add_news>0){?>
            <h3>Добавление новости</h3>
            <? pf();?>
        <? }elseif(@$edit_news>0){?>
            <h3>Редактирование новости</h3>
            <?
            $ss->que('news_by_id',(int)$edit_news);
            pf(true);
        }else{?>
            <input type="hidden" name="add_news" value="-1">
            <input type="hidden" name="edit_news" value="-1">
            <input type="hidden" name="drop_news" value="-1">

            <select name="group_id" onchange="document.forms['form1'].submit();">
                <? $ss->que('news_group');
                $i=0;
                while($ss->next()!==false){ if(!$i) $i=$ss->qrow['news_group_id'];?>
                    <option value="<?=$ss->qrow['news_group_id']?>" <?=$ss->qrow['news_group_id']==@$group_id?'selected':''?>><?=Tools::unesc($ss->qrow['name'])?> (id=<?=$ss->qrow['news_group_id']?>)</option>
                <? }?>
            </select>

            <input type="submit" value="+ Добавить новость" onClick="document.forms['form1'].add_news.value=1"><br>
            <?
            $group_id=@$group_id?$group_id:$i;
            $l=1;
            $ss->que('news',@$group_id);
            if(!$ss->qnum()) note('Лента новостей пуста.');
            else{?>
                <br><table class="ui-table ltable">
                    <tr>
                        <th>Дата публикации</th>
                        <th>иИ</th>
                        <th>Аннотация</th>
                        <th>Description</th>
                        <th>Keywords</th>
                        <th>Ссылка</th>
                        <th>Заголовок</th>
                        <th>Опубликовано</th>
                        <th>Удалить</th>
                    </tr>
                    <?
                    while($ss->next()!=false){?>
                        <tr>
                            <td align="center" nowrap><?=sdate($ss->qrow['dt'])?></td>
                            <td align="center">
                                <? if($ss->qrow['img1']!=''){?><img src="../img/img.gif" width="15" /><? }?>
                                <? if($ss->qrow['img2']!=''){?><img src="../img/img.gif" width="19" /><? }?>
                            </td>
                            <td align="center"><? if(mb_strlen(trim($ss->qrow['intro']))!='') echo 'есть'; else echo 'нет';?></td>
                            <td align="center"><?=!empty($ss->qrow['description'])?'есть':'нет'?></td>
                            <td align="center"><?=!empty($ss->qrow['keywords'])?'есть':'нет'?></td>
                            <td align="center"><?=!empty($ss->qrow['link'])?'есть':'нет'?></td>
                            <td><a title='<?=$ss->qrow['sname']?>' href="javascript://" onClick="document.forms['form1'].edit_news.value=<?=$ss->qrow['news_id']?>; document.forms['form1'].submit(); return false"><?=Tools::html($ss->qrow['title'])?></a></td>
                            <td align="center"><? if($ss->qrow['published']) echo 'да'; else echo 'нет';?></td>
                            <td align="center"><a href="javascript://" onClick="if (confirm('Удалить?')) {document.forms['form1'].drop_news.value=<?=$ss->qrow['news_id']?>; document.forms['form1'].submit(); return false}"><img src="/cms/img/b_drop.png" border="0"></a></td>
                        </tr>
                    <? }?>
                </table>
            <? }}?>
    </form>

<? cp_end();
