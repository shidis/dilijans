<? require_once '../auth.php';
include('../struct.php');

$cp->frm['name']='cnt';
$cp->frm['title']='Текстовые разделы сайта';

$cp->checkPermissions();

cp_head();
cp_css();
cp_js();

cp_body();
cp_title();

$ss=new Content();

foreach ($_GET as $key=>$value) $$key=$value;
foreach ($_POST as $key=>$value) $$key=$value;

/*
 * возможные значения cnt_type_id.type={banners,articles,blocks,pages}
 */

$tree=$ss->getTree();
if(empty($cnt_type_id)) {
    reset($tree);
    $cnt_type_id=key($tree);
    $cntType=current($tree);
}else{
    $ss->que("cnt_type_by_id",$cnt_type_id);
    $cntType=array(
        'type'=>$ss->qrow['type']
    );
}
?>
<form method="post" name="form1" enctype="multipart/form-data">
    <input type="hidden" name="act" value="-1">
    <input type="hidden" name="cnt_id" value="-1">
    <input type="hidden" name="MAX_FILE_SIZE" value="<?=Cfg::get('max_file_size')?>">
<?
if (@$act=='add_post'){

    $ti=$ss->getOne("SELECT * FROM ss_cnt_type WHERE cnt_type_id='$cnt_type_id'");
    if(!$ti['cnt_type_id']) echo 'Нет ID рубрики'; else {
        $is='';
        $sname=Tools::str2iso(trim($sname)!=''?$sname:$title,Cfg::get('SNAME_CNT_LEN'),Cfg::get('SNAME_CNT_REG'));
        $sname0=$sname;
        do{
            $sname=$sname0.($is!=''?"_$is":'');
            $sname1=Tools::like($sname);
            $ss->query("SELECT count(cnt_id) FROM ss_cnt WHERE sname LIKE '$sname1' AND NOT LD AND (cnt_type_id='$cnt_type_id'".($ti['noDoubleIn']!=''?" OR cnt_type_id IN({$ti['noDoubleIn']}))":')'));
            $ss->next();
            if($ss->qrow[0]) $is++;
        }while ($ss->qrow[0]);
        $title=Tools::esc($title);
        $link=Tools::esc($link);
        $no_editor=@$no_editor;
        $cnt_type_id=(int)$cnt_type_id;
        $text=Tools::untaria(!empty($tmh_sstext)?$tmh_sstext:$sstext);
        $intro=Tools::untaria($intro);
        $dt_added=Tools::dt();
        $publishedDate=fdate($publishedDate);
        $published=@$published;
        $kw=Tools::untaria($keywords,0);
        $descr=Tools::untaria($discription,0);
        $pos=intval($pos);
        if ($ss->query("INSERT INTO ss_cnt (cnt_type_id,sname,title,intro,text,dt_added,publishedDate,published,pos, no_editor,description,keywords,link) VALUES('$cnt_type_id','$sname','$title','$intro', '$text','$dt_added', '$publishedDate', '$published', '$pos','$no_editor','$descr','$kw','$link')")) {
            note('Добавлено');
            $id=$ss->lastId();
            if (@$_FILES['img1']['name']!='') if (!($ss->imgUpload('ss_cnt',$id,1,'img1'))) warn ('<p><strong>Файл 1 не передан!</strong></p>');
            if (@$_FILES['img2']['name']!='') if (!($ss->imgUpload('ss_cnt',$id,2,'img2'))) warn ('<p><strong>Файл 2 не передан!</strong></p>');
        }else warn ('Ошибка записи');
    }

}elseif(@$act=='edit_post'){
    $ti=$ss->getOne("SELECT * FROM ss_cnt_type WHERE cnt_type_id='$cnt_type_id'");
    if(!$ti['cnt_type_id']) echo 'Нет ID рубрики'; else {
        $is='';
        $sname=Tools::str2iso(trim($sname)!=''?$sname:$title,Cfg::get('SNAME_CNT_LEN'),Cfg::get('SNAME_CNT_REG'));
        $sname0=$sname;
        do{
            $sname=$sname0.($is!=''?"_$is":'');
            $sname1=Tools::like($sname);
            $ss->query("SELECT count(sname) FROM ss_cnt WHERE  NOT LD AND sname LIKE '$sname1' AND cnt_id!='$cnt_id' AND NOT LD AND (cnt_type_id='$cnt_type_id'".($ti['noDoubleIn']!=''?" OR cnt_type_id IN({$ti['noDoubleIn']}))":')'));
            $ss->next();
            if($ss->qrow[0]) $is++;
        }while ($ss->qrow[0]);
        $sname=Tools::esc($sname);
        $title=Tools::esc($title);
        $cnt_type_id=(int)$cnt_type_id;
        $link=Tools::esc($link);
        $no_editor=@$no_editor;
        $text=Tools::untaria(!empty($tmh_sstext)?$tmh_sstext:$sstext);
        $intro=Tools::untaria($intro);
        $pos=intval($pos);
        $kw=Tools::untaria($keywords,0);
        $descr=Tools::untaria($discription,0);
        $publishedDate=fdate($publishedDate);
        $published=@$published;
        if ($ss->query("UPDATE ss_cnt SET cnt_type_id='$cnt_type_id', pos='$pos', sname='$sname',title='$title', intro='$intro', text='$text', no_editor='$no_editor', description='$descr', keywords='$kw', link='$link', published='$published', publishedDate='$publishedDate' WHERE cnt_id='$cnt_id'")) {
            note('Отредактировано');
            if (@$_FILES['img1']['name']!='') if (!($ss->imgUpload('ss_cnt',$cnt_id,1,'img1'))) warn ('<p><strong>Файл 1 не передан!</strong></p>');
            if (@$_FILES['img2']['name']!='') if (!($ss->imgUpload('ss_cnt',$cnt_id,2,'img2'))) warn ('<p><strong>Файл 2 не передан!</strong></p>');
        }else warn('Ошибка записи');
    }
}
if(@$act=='del') if( $ss->ld('ss_cnt','cnt_id',$cnt_id)) note('Удалено');

if (@$drop_img>0){
    if ($ss->imgDel('ss_cnt',(int)$cnt_id,(int)$drop_img)) note('<p>Изображение удалено.</p>');
    $act='edit';
}

if (@$act=='edit' || @$act=='add') {

    if(!@$cnt_id && ($act=='edit' || $act=='add_img' ||$act=='del_img')) die('Неверный параметр');
    $ss->que('cnt_type_by_id',$cnt_type_id);
    $tDescr=Tools::unesc($ss->qrow['description']);
    if($act=='edit' || $act=='add_img' ||$act=='del_img') {
        $ss->que('cnt_by_id',$cnt_id);
        $title=Tools::html($ss->qrow['title']);
    }
    include('cnt_edit.php');

} else {

    ?><select name="cnt_type_id" onChange="document.forms['form1'].submit();"><?
    getTree($tree,$cnt_type_id);
    ?></select>
    <input type="submit" value="+ Добавить документ" OnClick="document.forms['form1'].act.value='add';"><?
    $ss->que('cnt_list',$cnt_type_id);
    if($ss->qnum()){
        ?><table class="ui-table ltable">
        <tr>
        <th>#</th>
        <th>Заголовок</th><?
        if(in_array($cntType['type'], array('','articles','banners','pages'))){
            ?><th>Дата публикации</th><?
        }
        if(in_array($cntType['type'], array('','blocks','pages'))){
            ?><th>Псевдоним</th><?
        }?>
        <th>иИ</th>
        <th>Description</th>
        <th>Keywords</th><?
        if(!in_array($cntType['type'], array('blocks'))){
            ?><th>Опубликовано</th><?
        }?>
        <th>Позиция</th>
        <th>Удалить</th>
        </tr><?
        $l=1;
        while($ss->next()!=false){?>
            <tr><td align="center"><?=$ss->qrow['cnt_id']?></a></td>
                <td><a href="#" onClick=" document.forms['form1'].act.value='edit'; document.forms['form1'].cnt_id.value=<?=$ss->qrow['cnt_id']?>; document.forms['form1'].submit(); return false;"><?=$ss->qrow['title']?></a></td><?

                if(in_array($cntType['type'], array('','articles','banners','pages'))){
                    ?><td align="center"><?=sdate($ss->qrow['publishedDate'])?></td><?
                }

                if(in_array($cntType['type'], array('','blocks','pages'))){
                    ?><td><?=$ss->qrow['sname']?></td><?
                }?>

                <td align="center">
                    <? if($ss->qrow['img1']!=''){?><img src="../img/img.gif" width="15" /><? }?>
                    <? if($ss->qrow['img2']!=''){?><img src="../img/img.gif" width="19" /><? }?>
                </td>

                <td align="center"><?=!empty($ss->qrow['description'])?'есть':'нет'?></td>
                <td align="center"><?=!empty($ss->qrow['keywords'])?'есть':'нет'?></td><?
                if(!in_array($cntType['type'], array('blocks'))){
                    ?><td align="center"><? if($ss->qrow['published']) echo 'да'; else echo 'нет';?></td><?
                }?>

                <td align="center"><?=$ss->qrow['pos']?></td>

                <td align="center"><a href="#" onClick="if (confirm('Удалить?')){ document.forms['form1'].act.value='del'; document.forms['form1'].cnt_id.value=<?=$ss->qrow['cnt_id']?>; document.forms['form1'].submit();} return false;"><img src="../img/b_drop.png" border="0"></a></td>
            </tr>
            <? $l++;
        } ?>
        </table><?
    }else{
        note("Элементов в этой рубрике нет");
    }
}
?></form><?


function getTree($tree,$cnt_type_id)
{
    foreach($tree as $k=>$v){
        ?><option value="<?=$k?>" <?=$k==$cnt_type_id?'selected':''?>><?
        echo str_replace(' ','&nbsp;',str_pad(" ",$v['level']*4));
        echo $v['name']." (id:$k)";
        ?></option><?
        if(!empty($v['childrens'])) getTree($v['childrens'],$cnt_type_id);
    }

}
cp_end();
