<? if (!defined('true_enter')) die ("Direct access not allowed!");

if (@$add_id>0){
    echo'<h1>Добавить бренд</h1>';
    $edit_id=0;
}elseif (@$edit_id>0){
    $cc->que('brand_by_id',$edit_id);
    $cc->next();
    echo'<h1>Редактировать бренд '.$cc->qrow['name'].'</h1>';
}
$cc1=new DB;
?>
<style type="text/css">
    .row {
        display:block;
        overflow:hidden;
        width:100%;
    }
    label {
        display:block;
    }
</style>
<script type="text/javascript">
    var tb_els='text<? foreach(App_TFields::get('cc_brand','editor',$gr) as $k=>$v){?>, af[<?=$k?>]<? }?>';
    $().ready(function(){
        tinyMCE.init($.extend(TM.cfg1,{
            elements: tb_els
        }));

        $('[name=form1]').submit(function(){
            if($('[name=name]').length && $('[name=name]').val()=='' && $('[name=edit_id]').val()>=0) {
                alert('Не введено название бренда');
                return false;
            }
        })

    });
</script>

<div class="edit_area">
    <form action="" method="post" enctype="multipart/form-data" name="form1">

        <input type="hidden" name="MAX_FILE_SIZE" value="<?=Cfg::get('max_file_size');?>">
        <input type="hidden" name="edit_id" value="<?=$edit_id;?>">
        <input type="hidden" name="name0" value="<?=Tools::unesc($cc->qrow['name'])?>">
        <input type="hidden" name="sname0" value="<?=Tools::unesc($cc->qrow['sname'])?>">
        <input name="post" type="submit" id="post"  value="Записать" />
        <input  type="submit" value="Веpнуться без записи" onclick="document.form1.edit_id.value=-1" />

        <table width="100%" border="0" cellpadding="0" cellspacing="5" style="margin: 10px 0 0">
            <tr>
                <td valign="top"><fieldset>
                        <legend>Основные свойства</legend>
                        <table border="0" cellpadding="0" cellspacing="5">
                            <tr valign="top">
                                <td width="107"><strong>Название</strong></td>
                                <td><input name="name" type="text" id="name" style="width:200px" value="<?=@htmlspecialchars($cc->qrow['name']);?>"></td>
                            </tr>
                            <tr valign="top">
                                <td width="107"><strong>Название алт.</strong></td>
                                <td><input name="name_alt" type="text" id="name_alt" style="width:200px" value="<?=@htmlspecialchars($cc->qrow['alt']);?>"></td>
                            </tr>
                            <tr valign="top">
                                <td><strong>Псевдоним</strong><br />
                                    <small>(допустимые символы: a-z0-9_-)</small></td>
                                <td><input name="sname" type="text"  id="sname" style="width:200px" value="<?=@htmlspecialchars($cc->qrow['sname']);?>" />
                                    <br />
                                    <small>оставьте пустым для автоматической генерации</small></td>
                            </tr>
                            <? if($gr==2){?>
                                <tr align="left" valign="top">
                                    <td nowrap><strong>Реплика</strong></td>
                                    <td><input name="replica" type="checkbox" value="1" <?=@$cc->qrow['replica']==1 || @$add_id>0 && isset($_GET['replica'])?'checked':''?> /></td>
                                </tr>
                                <? if(isset($_GET['replica'])){?>
                                    <tr align="left" valign="top">
                                        <td nowrap>Привязка к поставщику<br />
                                            (для брендованной реплики)</td>
                                        <td><select name="sup_id"><option value="0">- Поставщик не выбран -</option><?
                                                $cc1->query("SELECT * FROM cc_sup ORDER BY name");
                                                while($cc1->next()!==false){
                                                    ?><option value="<?=$cc1->qrow['sup_id']?>" <?=$cc1->qrow['sup_id']==@$cc->qrow['sup_id']?'selected':''?>><?=Tools::html($cc1->qrow['name'])?></option><?
                                                }?></select></td>
                                    </tr>
                                    <tr align="left" valign="top">
                                        <td nowrap>Привязка к марке авто из базы подбора</td>
                                        <td><select name="avto_id"><option value="0">- Нет привязки к авто -</option><?
                                                $cc1->query("SELECT avto_id,name FROM ab_avto WHERE vendor_id=0 ORDER BY name");
                                                while($cc1->next()!==false){
                                                    ?><option value="<?=$cc1->qrow['avto_id']?>" <?=$cc1->qrow['avto_id']==@$cc->qrow['avto_id']?'selected':''?>><?=Tools::html($cc1->qrow['name'])?></option><?
                                                }?></select></td>
                                    </tr>
                                <? }?>
                            <? }?>
                            <tr align="left" valign="top">
                                <td nowrap><strong>Базовая наценка </strong></td>
                                <td><input name="extra_b" type="text" size="10" id="extra_b" value="<?=@$cc->qrow['extra_b'];?>">
                                    %</td>
                            </tr>
                            <tr align="left" valign="top">
                                <td nowrap>Частотность</td>
                                <td><input name="hit_quant" type="text" size="10" id="hit_quant" value="<?=@$cc->qrow['hit_quant'];?>">
                                </td>
                            </tr>
                        </table>
                    </fieldset></td>

                <td width="100%" valign="top"><table>
                        <tr>
                            <td style="padding-right:15px;"><fieldset>
                                    <legend>Изображение 1</legend>
                                    <table border="0" cellpadding="0" cellspacing="5">
                                        <tr align="left" valign="top">
                                            <td nowrap><div class="row">
                                                    <input name="img1" type="file" id="img1"  style="width:100%" />
                                                </div>
                                                <div class="row">
                                                    <label>Ссылка на файл</label>
                                                    <input name="spy1" type="text" id="spy1" style="width:100%" />
                                                </div>
                                                <div class="row">
                                                    <?=(isset($edit_id)?'<input name="del_img1" type="checkbox" id="img1_del" value="1"> Удалить ':'');?>
                                                </div>                </td>
                                            <td valign="middle" style="padding-left:20px;"><?=((@$cc->qrow['img1']!='')?'<img src="'.$cc->make_img_path(1).'?'.mt_rand().'">':'<img src="../img/noimg.gif">');?></td>
                                        </tr>
                                    </table>
                                </fieldset></td>
                            <td><fieldset>
                                    <legend>Изображение 2</legend>
                                    <table border="0" cellpadding="0" cellspacing="5">
                                        <tr align="left" valign="top">
                                            <td nowrap><div class="row">
                                                    <input name="img2" type="file" id="img2"  style="width:100%" />
                                                </div>
                                                <div class="row">
                                                    <label>Ссылка на файл</label>
                                                    <input name="spy2" type="text" id="spy2" style="width:100%" />
                                                </div>
                                                <div class="row">
                                                    <?=(isset($edit_id)?'<input name="del_img2" type="checkbox" id="img2_del" value="1"> Удалить ':'');?>
                                                </div>                </td>
                                            <td valign="middle" style="padding-left:20px;"><?=((@$cc->qrow['img2']!='')?'<img src="'.$cc->make_img_path(2).'?'.mt_rand().'">':'<img src="../img/noimg.gif">');?></td>
                                        </tr>
                                    </table>
                                </fieldset></td>
                        </tr>
                    </table>

                </td>
            </tr>
        </table>

        <? $af=App_TFields::formEl('cc_brand','all',$gr,@$cc->qrow);
        if(!empty($af)){
            ?><fieldset style="margin:7px;"><table cellspacing="5"><?
                foreach($af as $v){?>
                    <tr>
                    <td valign="top"><?=$v[0]?></td>
                    <td valign="top" width="100%"><?=$v[1]?></td>
                    </tr><?
                }
                ?></table></fieldset><?
        }?>


        <p><strong>Текстовое описание</strong> <button class="TM_sw" forel="text">/</button></p>
        <textarea class="TM" name="text" style="width:100%; height:460px"><?=Tools::taria(@$cc->qrow['text'])?></textarea>
        <? foreach(App_TFields::get('cc_brand','editor',$gr) as $k=>$v){?>
            <p><strong><?=$v['caption']?></strong> <button class="TM_sw" forel="af[<?=$k?>]">/</button></p>
            <textarea class="TM" name="af[<?=$k?>]" style="<?=@$v['style']?>"><?=Tools::taria(@$cc->qrow[$k])?></textarea>
        <? }?><br />
        <!--Новые SEO поля-->
        <?if ($gr == 2):?>
        <br><hr>
        <h2>Дополнительные данные</h2>
        <table width="800">
            <tr>
                <td style="padding-right:15px;"><legend>Изображение 3</legend></td>
                <td>
                    <fieldset>
                        <table border="0" cellpadding="0" cellspacing="5" width="100%">
                            <tr align="left" valign="top">
                                <td nowrap><div class="row">
                                        <input name="seo_img" type="file" id="seo_img"  style="width:225px;" />
                                    </div>
                                    <div class="row">
                                        <label>Ссылка на файл</label>
                                        <input name="seo_img_spy" type="text" id="seo_img_spy" style="width:225px;" />
                                    </div>
                                    <div class="row">
                                        <?=(isset($edit_id)?'<input name="del_seo_img" type="checkbox" id="del_seo_img" value="1"> Удалить ':'');?>
                                    </div>                </td>
                                <td valign="middle" style="padding-left:20px;"><?=((@$cc->qrow['seo_img']!='')?'<img style="max-height: 200px;" src="'.$cc->make_img_path($cc->qrow['seo_img']).'?'.mt_rand().'">':'<img src="../img/noimg.gif">');?></td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <td>Заголовок H1:</td>
                <td><input type="text" name="seo_h1" value="<?=@$cc->qrow['seo_h1']?>" style="width: 650px" /></td>
            </tr>
            <tr>
                <td>Заголовок H2:</td>
                <td><input type="text" name="seo_h2" value="<?=@$cc->qrow['seo_h2']?>" style="width: 650px" /></td>
            </tr>
            <tr>
                <td>SEO Title:</td>
                <td><input type="text" name="seo_title" value="<?=@$cc->qrow['seo_title']?>" style="width: 650px" /></td>
            </tr>
            <tr>
                <td>SEO Description:</td>
                <td><textarea  name="seo_desc" style="width: 650px; height: 65px;"><?=@$cc->qrow['seo_desc']?></textarea></td>
            </tr>
            <tr>
                <td>SEO Keywords:</td>
                <td><input type="text" name="seo_key" value="<?=@$cc->qrow['seo_key']?>" style="width: 650px" /></td>
            </tr>
            <tr>
                <td>SEO оптимизация:</td>
                <td>
                    <label style="float: left;"><input type="radio" name="is_seo" value="0" <?=(@$cc->qrow['is_seo'] == 0 ? 'checked="true"' : '')?> class="ui-corner-all">Нет</label>
                    <label><input type="radio" name="is_seo" value="1" <?=(@$cc->qrow['is_seo'] == 1 ? 'checked="true"' : '')?> class="ui-corner-all">Да</label>
                    <!--<input type="checkbox" name="is_seo" value="1" <?/*=(@$cc->qrow['is_seo'] == 1 ? 'checked="true"' : '')*/?> />-->
                </td>
            </tr>
        </table>
       <hr><br>
        <?endif;?>
        <!--Новые SEO поля END-->
        <input name="post" type="submit" id="post"  value="Записать" />
        <input  type="submit" value="Веpнуться без записи" onclick="document.form1.edit_id.value=-1" />

    </form>

</div>
