<? if (!defined('true_enter')) die ("Direct access not allowed!");

$cc->que('model_by_id',$medit_id);
$cc->next();
$gr=$cc->qrow['gr'];

if(@$gr!=1 && @$gr!=2 || !@$medit_id) die('no param');

$c=new CC_Ctrl;
?>
<style type="text/css">
    .medd0 input{
        width:100%;
    }
    .row {
        display:block;
        overflow:hidden;
        width:100%;
    }
</style>
<link href="/cms/css/jquery.pf.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="/cms/js/lib/jquery.pf.js"></script>
<script src="/cms/inc/tinymce/tiny_mce.js" type="text/javascript"></script>
<script src="/cms/inc/tinymce/jquery.tinymce.js" type="text/javascript"></script>
<script src="/cms/inc/tinybrowser/tb_tinymce.js.php" type="text/javascript"></script>
<script src="/cms/js/tinymce.js" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function(){

        $("a[rel^='zoom']").prettyPhoto();

        var tb_els='text<? foreach(App_TFields::get('cc_model','editor',$gr) as $k=>$v){?>, af[<?=$k?>]<? }?>';
        tinyMCE.init($.extend(TM.cfg1,{
            elements: tb_els
        }));

        $('[name=form1]').submit(function(){
            if($('[name=name]').length && $('[name=name]').val()=='' && $('[name=medit_id]').val()>0) {
                alert('Не введено название модели');
                return false;
            }
        })


    });
</script>

<h1>Редактирование модели <?=$gr==1?'шины':'диска'?> <?=Tools::html($cc->qrow['name'])?> / код <?=$medit_id?></h1>

<form action="" method="post" enctype="multipart/form-data" name="form1">
    <input type="submit" name="medit_post1" value="Записать и вернуться" />
    <input type="submit" value="<<<  Вернуться назад без записи" onClick="document.forms['form1'].medit_id.value=-1">
    <div class="edit_area" style="margin-top: 11px">
        <input type="hidden" name="MAX_FILE_SIZE" value="<?=Cfg::get('max_file_size')?>">
        <input type="hidden" name="page" value="<?=@$page?>" />
        <input type="hidden" name="medit_id" value="<?=$medit_id?>" />
        <table width="100%" border="0" cellpadding="0" cellspacing="4">
            <tr>
                <td><strong>Производитель</strong></td>
                <td colspan="4">
                    <?
                    $c->que('brands',$gr);
                    ?>
                    <select name="brand_id_">
                        <?
                        $r=-1;
                        $sb=-1;
                        while($c->next()!=false) {
                            if($r<=0 && $c->qrow['replica']==1){
                                $r=1;
                                echo '<optgroup label="Replica">';
                            }
                            if($r==1 && $c->qrow['replica']!=1){
                                $r=0;
                                echo '</optgroup>';
                            }
                            if($sb<=0 && $c->qrow['sup_id']){
                                $sb=1;
                                echo '<optgroup label="Бренд-реплика">';
                            }
                            if($sb==1 && !$c->qrow['sup_id']){
                                $sb=0;
                                echo '</optgroup>';
                            }
                            echo'<option value="'.$c->qrow['brand_id'].'" '.($c->qrow['brand_id']==$cc->qrow['brand_id']?'selected':'').($c->qrow['H']!=0?' class="isH"':'').'>'.Tools::html($c->qrow['name']).'</option>';
                        }
                        if($r==1 || $sb==1) echo '</optgroup>';
                        ?>
                    </select>		</td>
            </tr>
            <tr>
                <td width="101"><strong>Модель</strong></td>
                <td colspan="4"><input name="name" type="text" size="100" value="<?=Tools::html($cc->qrow['name'])?>"></td>
            </tr>
            <tr>
                <td style="color:red">Псевдоним</td>
                <td colspan="4"><input name="sname" type="text"  value="<?=$cc->qrow['sname']?>" size="100" /></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td colspan="4"><small>Примечание:  псевдоним - это часть урла. При изменении параметров модели псевдоним <strong>не изменится</strong>, за исключением варианта перемещения модели к другому производителю - псевдоним сформируется автоматически. Можете изменить его в ручную или удалите все символы из этого поля - псевдоним сгенериться в соответсвиии с текущими параметрами модели.</small></td>
            </tr>
            <tr>
                <td>Альтернативные названия</td>
                <td colspan="4"><input name="alt" type="text" size="100" value="<?=Tools::html($cc->qrow['alt'])?>" /></td>
            </tr>
            <? if($gr==1){?>
                <tr>
                    <td>Тип авто</td>
                    <td colspan="4"><select name="P2">
                            <option value="0" >Не указан</option>
                            <option value="1" <?=($cc->qrow['P2']=='1'?'selected':'')?>>Легковой</option>
                            <option value="2" <?=($cc->qrow['P2']=='2'?'selected':'')?>>Внедорожник</option>
                            <option value="3" <?=($cc->qrow['P2']=='3'?'selected':'')?>>Микроавтобус</option>
                            <option value="4" <?=($cc->qrow['P2']=='4'?'selected':'')?>>Легковой/внедорожник</option>
                        </select></td>
                </tr>
                <tr>
                    <td>Сезонность</td>
                    <td colspan="4"><select name="P1">
                            <option value="0" >!!! Не указан !!!</option>
                            <option value="1" <?=($cc->qrow['P1']=='1'?'selected':'')?>>Лето</option>
                            <option value="2" <?=($cc->qrow['P1']=='2'?'selected':'')?>>Зима</option>
                            <option value="3" <?=($cc->qrow['P1']=='3'?'selected':'')?>>Всесезон.</option>
                        </select></td>
                </tr>
                <tr>
                    <td>Шипы</td>
                    <td colspan="4"><select name="P3">
                            <option value="0" >Нет шипов</option>
                            <option value="1" <?=($cc->qrow['P3']=='1'?'selected':'')?>>Есть шипы</option>
                        </select></td>
                </tr>
            <? }elseif($gr==2){?>
                <input type="hidden" name="P3" value="<?=$cc->qrow['P3']?>">
                <input type="hidden" name="P2" value="<?=$cc->qrow['P2']?>">
                <tr>
                    <td>Тип диска</td>
                    <td colspan="4"><select name="P1">
                            <option value="0" >!!! Нет !!!</option>
                            <option value="2" <?=($cc->qrow['P1']=='2'?'selected':'')?>>Литой</option>
                            <option value="1" <?=($cc->qrow['P1']=='1'?'selected':'')?>>Кованый</option>
                            <option value="3" <?=($cc->qrow['P1']=='3'?'selected':'')?>>Штампованный</option>
                        </select></td>
                </tr>
            <? }
            $cc->load_mspez($gr);
            if(!empty($cc->mspez_arr)){
                ?><tr>
                <td>Доп. параметр</td>
                <td colspan="4">
                    <select name="mspez_id">
                        <option value="0">Нет</option>
                        <? foreach($cc->mspez_arr as $k=>$v){?>
                            <option value="<?=$k?>" <?=$cc->qrow['mspez_id']==$k?'selected':''?>><?=$v?></option>
                        <? }?>
                    </select>
                </td>
                </tr><?
            }
            $cc->load_class($gr);
            if(!empty($cc->class_arr)){
                ?><tr>
                <td>Класс модели</td>
                <td colspan="4">
                    <select name="class_id">
                        <option value="0">Нет</option>
                        <? foreach($cc->class_arr as $k=>$v){?>
                            <option value="<?=$k?>" <?=$cc->qrow['class_id']==$k?'selected':''?>><?=$v?></option>
                        <? }?>
                    </select>
                </td>
                </tr><?
            }
            ?>
            <tr>
                <td width="101">Суффикс</td>
                <td colspan="4"><input name="suffix" type="text" size="100" value="<?=Tools::html($cc->qrow['suffix'])?>"></td>
            </tr>
            <tr>
                <td valign="top" nowrap>Поставщик</td>
                <td><select name="sup_id" id="sup_id">
                        <? $cc->load_sup();
                        foreach($cc->sup_arr as $k=>$v){?>
                            <option value="<?=$k?>" <?=$cc->qrow['sup_id']==$k?'selected':''?>>
                                <?=$k==0?'< без поставщика >':$v?>
                            </option>
                        <? }?>
                    </select></td>
            </tr>
            <? if(in_array(Cfg::get('CAT_IMPORT_MODE'),array(1,3))){?>
                <tr>
                    <td>Код модели ТИ</td>
                    <td><input name="ti_id" type="text" size="20" value="<?=$cc->qrow['ti_id']?>"></td>
                </tr>
            <? }?>
            <tr>
                <td>Частотность</td>
                <td><input name="hit_quant" type="text" size="20" value="<?=$cc->qrow['hit_quant']?>"></td>
            </tr>
            <? $af=App_TFields::formEl('cc_model','all',$gr,@$cc->qrow);
            foreach($af as $v){?>
                <tr><td><?=$v[0]?></td><td class="medd0"><?=$v[1]?></td></tr><? }?>

            <tr>
                <td colspan="2"><hr /></td>
            </tr>
            <?if ($gr == 2):?>
            <tr>
                <td>Стикеры</td>
                <td>
                    <select id="model_sticker_type" name="model_sticker_type">
                    <option value="0">Нет</option>
                    <?
                        $model_sticker = $c->getModelSticker($cc->qrow['model_id']);
                        $stickers = CC_Ctrl::getStickersList();
                        foreach($stickers as $sid=>$sticker)
                        {
                            echo "<option allow_text='{$sticker['allow_text']}' value='{$sid}'".($model_sticker['sticker_type'] == $sid ? ' selected="selected"' : '').">{$sticker['desc']}</option>";
                        }
                    ?>
                    </select>
                    <span id="model_sticker_text" <?=(!@$stickers[@$model_sticker['sticker_type']]['allow_text'] ? 'style="display: none;"' : '')?>>Текст (максимум 12 символов):&nbsp;<input type="text" size="12" name="model_sticker_text" value="<?=@$model_sticker['sticker_text']?>" /></span>
                </td>
            </tr>
            <?endif;?>
            <tr>
                <td colspan="2"><hr /></td>
            </tr>
            <tr>
                <td>Видео</td>
                <td>
                    <input name="video_link" type="text" size="64" value="<?=$cc->qrow['video_link']?>">
                </td>
            </tr>
            <tr>
                <td colspan="2"><hr /></td>
            </tr>
            <tr>
                <td colspan="2" valign="top">
                    <?
                    $niw=130;
                    $i2=$cc->make_img_path(2);
                    $i2_=$i2.'?'.mt_rand();
                    $i1=$cc->make_img_path(1);
                    $i1_=$i1.'?'.mt_rand();
                    $i3=$cc->make_img_path(3);
                    $i3_=$i3.'?'.mt_rand();

                    if(!empty($i2) || !empty($i1) || !empty($i3)){

                        list($width1, $height1) = @getimagesize($cc->make_img_path(1,true));
                        list($width2, $height2) = @getimagesize($cc->make_img_path(2,true));
                        list($width3, $height3) = @getimagesize($cc->make_img_path(3,true));

                        ?><fieldset class="ui" style="float:left; margin:0 15px 15px; border:1px dashed #999; width:350px; overflow:hidden; padding:10px;"><legend class="ui">Изображение</legend>
                        <div style="float:left; overflow:hidden; margin-right:20px; width: <?=$niw?>px">
                            <a href="<?=$i2_?>" rel="zoom" title="наибольшее изображение"><img width="<?=$niw?>" src="<?=$i1_?>" /></a>
                        </div>

                        <div style="float:left; overflow:hidden; margin-right:10px; width: 190px">
                            <p style="line-height: 25px">
                                <a href="<?=$i2_?>" rel="zoom" title="наибольшее изображение">Изо.2</a>: <b><?=$width2?></b> x <b><?=$height2?></b> px<br>
                                <a href="<?=$i1_?>" rel="zoom" title="среднее изображение">Изо.1</a>: <b><?=$width1?></b> x <b><?=$height1?></b> px<br>
                                <a href="<?=$i3_?>" rel="zoom">Изо.3</a>: <b><?=$width3?></b> x <b><?=$height3?></b> px<br>
                            </p>
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
                <td colspan="2"><hr /></td>
            </tr>
        </table>

        <input type="submit" name="medit_post" value="Записать" />
        <input type="submit" value="<<<  Вернуться назад без записи" onClick="document.forms['form1'].medit_id.value=-1">
        <br /><hr />
        <strong>Текстовое описание модели</strong> <button class="TM_sw" forel="text">/</button>
        <textarea class="TM" name="text" style="width:100%; height:500px"><?=Tools::taria(@$cc->qrow['text'])?></textarea>

        <!-- meta start-->
        <?php include_once('seo.php')?>
        <!-- meta end-->

        <? foreach(App_TFields::get('cc_model','editor',$gr) as $k=>$v){
            ?><p><strong><?=$v['caption']?></strong> <button class="TM_sw" forel="af[<?=$k?>]">/</button></p><?
            ?><textarea class="TM" name="af[<?=$k?>]" style="width:100%; height:500px"><?=Tools::taria(@$cc->qrow[$k])?></textarea><?
        }?><br />

        <input type="submit" name="medit_post" value="Записать" />
        <input type="submit" value="<<<  Вернуться назад без записи" onClick="document.forms['form1'].medit_id.value=-1">

    </div>
</form>
