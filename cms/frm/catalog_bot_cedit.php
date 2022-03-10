<? if (!defined('true_enter')) die ("Direct access not allowed!");

$cc1=new CC_Ctrl;

if(@$gr!=1 && @$gr!=2 || !@$cedit_id) die('no param');


$cc->que('cat_by_id',$cedit_id);
$cc->next();


$ccc=new CC_Ctrl;

?>
<style>
  .suitable{
    resize: both;
  }
</style>
<h1>Редактирование типоразмера <?=$gr==1?'шины':'диска'?> / код <?=$cedit_id?></h1>
<script src="/cms/inc/tinymce/tiny_mce.js" type="text/javascript"></script>
<script src="/cms/inc/tinymce/jquery.tinymce.js" type="text/javascript"></script>
<script src="/cms/inc/tinybrowser/tb_tinymce.js.php" type="text/javascript"></script>
<script src="/cms/js/tinymce.js" type="text/javascript"></script>
<script type="text/javascript">
  $(document).ready(function(){

    $('#cprice').bind('change',function(e){
      if($(this).val()!=$('[name=cprice0]').val()) $('#fixPrice').attr('checked','checked');
    });

    var tb_els='adv_text<? foreach(App_TFields::get('cc_cat','editor',$gr) as $k=>$v){?>, af[<?=$k?>]<? }?>';
    tinyMCE.init($.extend(TM.cfg1,{
      elements: tb_els
    }));

  });
</script>
<form action="" method="post" enctype="multipart/form-data" name="form1">
  <div class="edit_area">
    <input type="hidden" name="MAX_FILE_SIZE" value="<?=Cfg::get('max_file_size')?>">
    <input type="hidden" name="model_id0" value="<?=$cc->qrow['model_id']?>" />
    <input type="hidden" name="page" value="<?=@$page?>" />
    <input type="hidden" name="cedit_id" value="<?=$cedit_id?>" />
    <input type="hidden" name="cprice0" value="<?=$cc->qrow['cprice']?>" />
    <input type="hidden" name="sname0" value="<?=$cc->qrow['sname']?>" />

    <? if ($gr==1){?>
      <table width="100%" border="0" cellpadding="0" cellspacing="5">
        <tr>
          <td width="101"><strong>Модель1</strong></td>
          <td colspan="4">
            <select name="model_id">
              <?
              $ccc->que('model_list',1,$cc->qrow['brand_id']);
              while($ccc->next()!==false){?>
                <option <?=$cc->qrow['model_id']==$ccc->qrow['model_id']?'selected':''?> value="<?=$ccc->qrow['model_id']?>"><?=(Tools::html($ccc->qrow['name'].' '.$ccc->qrow['suffix']).($ccc->qrow['H']!=0?' <--скрыта':''))?></option>
              <? }?>
            </select>	  </td>
        </tr>
        <tr>
          <td>Ширина</td>
          <td><input name="P3" type="text" id="P3" value="<?=$cc->qrow['P3']?>"></td>
          <td width="118">Сезонность</td>
          <td width="100%">
            <?=($cc->qrow['MP1']=='1'?'Лето':'')?>
            <?=($cc->qrow['MP1']=='2'?'Зима':'')?>
            <?=($cc->qrow['MP1']=='3'?'Всесезон':'')?>    </td>
        </tr>
        <tr>
          <td>Высота</td>
          <td width="155"><input name="P2" type="text" id="P2" value="<?=$cc->qrow['P2']?>"></td>
          <td nowrap="nowrap">Скоростная ZR </td>
          <td><input name="P6" type="checkbox" id="P6" value="1" <?=($cc->qrow['P6']=='1'?'checked':'')?>></td>
        </tr>
        <tr>
          <td>Радиус</td>
          <td><input name="P1" type="text" id="P1" value="<?=$cc->qrow['P1']?>"></td>
          <td>Ин / Ис </td>
          <td><input name="P7" type="text" id="P7" value="<?=Tools::html($cc->qrow['P7'])?>"></td>
        </tr>
        <tr>
          <td>Шипы </td>
          <td>
            <?=($cc->qrow['MP3']=='1'?'Есть':'')?>
            <?=($cc->qrow['MP3']=='0'?'Нет':'')?></td>
          <td>Суффикс</td>
          <td><input name="suffix" type="text" size="40" value="<?=Tools::html($cc->qrow['suffix'])?>"></td>
        </tr>
        <tr>
          <td nowrap="nowrap">Базовая цена</td>
          <td colspan="3"><input type="text" name="base_price" value="<?=$cc->qrow['bprice']?>" />
            <select name="cur_id">
              <? $cc1->que('cur');
              while($cc1->next()!=FALSE)
                echo'<option value="'.$cc1->qrow['cur_id'].'" '.($cc1->qrow['cur_id']==$cc->qrow['cur_id']?'selected':'').'>'.$cc1->qrow['name'].'</option>';?>
            </select>&nbsp;</td>
        </tr>
        <tr>
          <td>
            Розница <br />
            <input type="checkbox" name="fixPrice" id="fixPrice" value="1"<?=$cc->qrow['fixPrice']?' checked':''?> /><label style="color:red" for="fixPrice">фикс цены</label> <br />
            <input type="checkbox" name="fixSc" id="fixSc" value="1"<?=$cc->qrow['fixSc']?' checked':''?> /><label style="color:red" for="fixSc">фикс к-ва</label>
          </td>
          <td colspan="2"><input type="text" name="cprice" id="cprice" value="<?=$cc->qrow['cprice']?>" />
            руб</td>
          <td valign="middle">
            <small>Примечание: статус цены &quot;фиксированный&quot; означает, что для этого товара не будет применяться автоматический пересчет цены в соответсвии с наценками. Если вы установите флаг &quot;фикс&quot; с нулевой ценой - нулевая цена и будет отображаться на сайте. При ручном изменение розничной цены автоматически будет ставиться статус &quot;фикс&quot;.</small><br>
            <small>Примечание: статус количества &quot;фиксированный&quot; означает, что для этого товара не будет применяться автоматический пересчет количества при импорте.</small>
          </td>
        </tr>
        <tr>
          <td>Спец. цена </td>
          <td colspan="3"><input name="scprice" type="text" id="scprice" value="<?=$cc->qrow['scprice']?>" />
            руб </td>
        </tr>
        <tr>
          <td style="color:red">Псевдоним</td>
          <td colspan="3"><input name="sname" type="text" value="<?=$cc->qrow['sname']?>" size="100" /></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td colspan="3"><small>Примечание:  псевдоним - это часть урла. При изменении параметров шины псевдоним <strong>не изменится</strong>, за исключением варианта если вы перемещаете размер в другую модель - псевдоним сформируется автоматически.. Можете изменить его в ручную или удалите все символы из этого поля - псевдоним сгенериться в соответсвиии с текущими параметрами шины.</small></td>
        </tr>
        <? $af=App_TFields::formEl('cc_cat','all',$gr,@$cc->qrow);
        foreach($af as $v){?>
          <tr><td><?=$v[0]?></td><td colspan="3"><?=$v[1]?></td></tr>
        <? }?>

        <?
        // ************* Применяемость *************
        $AB = new CC_AB();
        $suitable = $AB->getAvtoArrayByTipo(Array(
            'P1' => $cc->qrow['P1'],
            'P2' => $cc->qrow['P2'],
            'P3' => $cc->qrow['P3'],
        ), 1);
        // *****************************************
        if (!empty($suitable)):?>
        <tr>
          <td>Применяемоть типоразмера</td>
          <td colspan="3">
            <select name="app[]" multiple="Y" class="suitable">
              <?
              $app_selected = unserialize($cc->qrow['app']);
              foreach ($suitable as $brand => $models)
              {
                foreach($models as $model=>$modifs)
                {
                  foreach($modifs as $modif=>$years) // Можно дальше пройтись по годам
                  {
                    echo '<option ';
                    if (!empty($app_selected) && in_array($brand.' '.$model.' '.$modif, $app_selected)){
                      echo 'selected="true"';
                    }
                    echo ' value="'.$brand.' '.$model.' '.$modif.'">'.$brand.' '.$model.' '.$modif.' '.'</option>';
                  }
                }
              }
              ?>
            </select>
          </td>
        </tr>
        <?endif;?>
        <!-- дополнительное текстовое описание start-->
        <tr>
          <td colspan="4">
            <br /><hr />
            <strong>Текстовое описание типоразмера</strong> <button class="TM_sw" forel="text">/</button>
            <textarea class="TM" name="adv_text" style="width:100%; height:300px"><?=Tools::taria(@$cc->qrow['adv_text'])?></textarea>

          </td>
        </tr>
        <!-- дополнительное текстовое описание end-->

        <!-- meta start-->
        <tr>
          <td colspan="4">
            <?php include_once('seo.php')?>
          </td>
        </tr>
        <!-- meta end-->


        <tr>
          <td colspan="2"><input type="submit" name="cedit_post" value="Записать и вернуться"></td>
          <td colspan="2"><input type="submit" value="<<< Вернуться в каталог без записи" onClick="document.forms['form1'].cedit_id.value=-1"></td>
        </tr>
      </table>

      <? foreach(App_TFields::get('cc_cat','editor',$gr) as $k=>$v){
        ?><p><strong><?=$v['caption']?></strong> <button class="TM_sw" forel="af[<?=$k?>]">/</button></p><?
        ?><textarea class="TM" name="af[<?=$k?>]" style="width:100%; height:160px"><?=Tools::taria(@$cc->qrow[$k])?></textarea><?
      }?>

    <? }else{?>
      <table width="100%" border="0" cellpadding="0" cellspacing="5">
        <tr>
          <td width="101"><strong>Модель</strong></td>
          <td colspan="4"><select name="model_id">
              <?
              $ccc->que('model_list',2,$cc->qrow['brand_id']);
              while($ccc->next()!==false){?>
                <option <?=$cc->qrow['model_id']==$ccc->qrow['model_id']?'selected':''?> value="<?=$ccc->qrow['model_id']?>"><?=Tools::html(($ccc->qrow['name'].' '.$ccc->qrow['suffix']).($ccc->qrow['H']!=0?' <--скрыта':''))?></option>
              <? }?>
            </select></td>
        </tr>
        <tr>
          <td>J</td>
          <td width="155"><input name="P2" type="text" id="P2" value="<?=$cc->qrow['P2']?>"></td>
          <td width="118">Тип</td>
          <td width="100%">
            <?=($cc->qrow['MP1']=='1'?'Кованый':'')?>
            <?=($cc->qrow['MP1']=='2'?'Литой':'')?>
            <?=($cc->qrow['MP1']=='3'?'Штампованый':'')?>      </td>
        </tr>
        <tr>
          <td>Диаметр</td>
          <td><input name="P5" type="text" id="P5" value="<?=$cc->qrow['P5']?>"></td>
          <td>LZ</td>
          <td><input name="P4" type="text" id="P4" value="<?=$cc->qrow['P4']?>"></td>
        </tr>
        <tr>
          <td>ET</td>
          <td><input name="P1" type="text" id="P1" value="<?=$cc->qrow['P1']?>"></td>
          <td>PCD</td>
          <td><input name="P6" type="text" id="P6" value="<?=$cc->qrow['P6']?>"></td>
        </tr>
        <tr>
          <td>DIA</td>
          <td><input name="P3" type="text" id="P3" value="<?=$cc->qrow['P3']?>"></td>
          <td>Суффикс</td>
          <td><input name="suffix" type="text" size="40" value="<?=Tools::html($cc->qrow['suffix'])?>"></td>
        </tr>
        <tr>
          <td nowrap="nowrap">Базовая цена</td>
          <td colspan="3"><input type="text" name="base_price" value="<?=$cc->qrow['bprice']?>">
            <select name="cur_id">
              <? $cc1->que('cur');
              while($cc1->next()!=FALSE)
                echo'<option value="'.$cc1->qrow['cur_id'].'" '.($cc1->qrow['cur_id']==$cc->qrow['cur_id']?'selected':'').'>'.$cc1->qrow['name'].'</option>';?>
            </select>	</td>
        </tr>
        <tr>
          <td>
            Розница <br />
            <input type="checkbox" name="fixPrice" id="fixPrice" value="1"<?=$cc->qrow['fixPrice']?' checked':''?> /><label style="color:red" for="fixPrice">фикс цены</label> <br />
            <input type="checkbox" name="fixSc" id="fixSc" value="1"<?=$cc->qrow['fixSc']?' checked':''?> /><label style="color:red" for="fixSc">фикс к-ва</label>
          </td>
          <td colspan="2"><input type="text" name="cprice" id="cprice" value="<?=$cc->qrow['cprice']?>" />
            руб</td>
          <td valign="middle">
            <small>Примечание: статус цены &quot;фиксированный&quot; означает, что для этого товара не будет применяться автоматический пересчет цены в соответсвии с наценками. Если вы установите флаг &quot;фикс&quot; с нулевой ценой - нулевая цена и будет отображаться на сайте. При ручном изменение розничной цены автоматически будет ставиться статус &quot;фикс&quot;.</small><br>
            <small>Примечание: статус количества &quot;фиксированный&quot; означает, что для этого товара не будет применяться автоматический пересчет количества при импорте.</small>
          </td>
        </tr>
        <tr>
          <td>Спец. цена </td>
          <td colspan="3"><input name="scprice" type="text" id="scprice" value="<?=$cc->qrow['scprice']?>" />
            руб. </td>
        </tr>
        <tr>
          <td style="color:red">Псевдоним</td>
          <td colspan="3"><input name="sname" type="text" value="<?=$cc->qrow['sname']?>" size="100" /></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td colspan="3"><small>Примечание:  псевдоним - это часть урла. При изменении параметров шины псевдоним <strong>не изменится</strong>, за исключением, варианта если вы перемещаете размер в другую модель - псевдоним сформируется автоматически. Можете изменить его в ручную или удалите все символы из этого поля - псевдоним сгенериться в соответсвиии с текущими параметрами диска.</small></td>
        </tr>

        <? $af=App_TFields::formEl('cc_cat','all',$gr,@$cc->qrow);
        foreach($af as $v){?><tr><td><?=$v[0]?></td><td colspan="3"><?=$v[1]?></td></tr><? }?>

        <?
        // ************* Применяемость *************
        $AB = new CC_AB();
        $_deltaDia = -0.1;
        $_deltaET = -5;
        $deltaET_ = 3;
        if ($gr == 2){
          $suitable = $AB->getAvtoArrayByTipo(Array(
              'P1' => array('_from'=>$_deltaET, '_to'=> $deltaET_, 'ex' => $cc->qrow['P1']),
              'P2' => $cc->qrow['P2'],
              'P3' => array('from'=>0, 'to'=> $cc->qrow['P3'] + abs($_deltaDia)),
              'P4' => $cc->qrow['P4'],
              'P5' => $cc->qrow['P5'],
              'P6' => $cc->qrow['P6']
          ), $gr);
        }
        // *****************************************
        if (!empty($suitable)):?>
        <tr>
          <td>Применяемоть типоразмера</td>
          <td colspan="3">
            <select name="app[]" multiple="Y" class="suitable">
              <?
              $app_selected = unserialize($cc->qrow['app']);
              foreach ($suitable as $brand => $models)
              {
                foreach($models as $model=>$modifs)
                {
                  foreach($modifs as $modif=>$years) // Можно дальше пройтись по годам
                  {
                    echo '<option ';
                    if (!empty($app_selected) && in_array($brand.' '.$model.' '.$modif, $app_selected)){
                      echo 'selected="true"';
                    }
                    echo ' value="'.$brand.' '.$model.' '.$modif.'">'.$brand.' '.$model.' '.$modif.' '.'</option>';
                  }
                }
              }
              ?>
            </select>
          </td>
        </tr>
        <?endif;?>

        <!-- дополнительное текстовое описание start-->
        <tr>
          <td colspan="4">
            <br /><hr />
            <strong>Текстовое описание типоразмера</strong> <button class="TM_sw" forel="text">/</button>
            <textarea class="TM" name="adv_text" style="width:100%; height:300px"><?=Tools::taria(@$cc->qrow['adv_text'])?></textarea>

          </td>
        </tr>
        <!-- дополнительное текстовое описание end-->

        <!-- meta start-->
        <tr>
          <td colspan="4">
            <?php include_once('seo.php')?>
          </td>
        </tr>
        <!-- meta end-->




        <tr>
          <td colspan="2"><input type="submit" name="cedit_post" value="Записать и вернуться"></td>
          <td colspan="2"><input type="submit" value="<<< Вернуться в каталог без записи" onClick="document.forms['form1'].cedit_id.value=-1"></td>
        </tr>
      </table>

      <? foreach(App_TFields::get('cc_cat','editor',$gr) as $k=>$v){
        ?><p><strong><?=$v['caption']?></strong> <button class="TM_sw" forel="af[<?=$k?>]">/</button></p><?
        ?><textarea class="TM" name="af[<?=$k?>]" style="width:100%; height:160px"><?=Tools::taria(@$cc->qrow[$k])?></textarea><?
      }?>

    <? }?>
  </div>
</form>
