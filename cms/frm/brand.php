<?
require_once '../auth.php';
include('../struct.php');

$gr=@$_GET['gr'];
if($gr!=1 && $gr!=2) die('gr incorrect. exit.');

if ($gr==1) $cp->frm['title']='Бренды шин'; 
elseif(isset($_GET['replica'])) $cp->frm['title']='Бренды Реплики'; 
else $cp->frm['title']='Бренды дисков';

$cp->frm['name']='brands';

$cp->checkPermissions();

cp_head();
cp_css();
cp_js();
cp_body();
cp_title();



$dataset_id=@$_REQUEST['dataset_id'];
$ds=new CC_Dataset();

$cc=new CC_Ctrl;
$aq='';
if($gr==2 && isset($_GET['replica'])) $aq='replica=1';
elseif($gr==2 && isset($_GET['noreplica'])) $aq='replica=0';

if(@$_POST['linkDataset']==1 && $dataset_id){
	$i=0;
	$ids=array();
	foreach($_POST as $k=>$v) {
		$x=explode('_',$k);
		if(@$x[0]=='c' && @$x[1]>0){ // x[1] - brand_id
			$ids[$x[1]]=(int)$x[1];
		}
	}
	if(count($ids)){
		$d=$ds->fetchAll("SELECT brand_id FROM cc_dataset_brand WHERE dataset_id='$dataset_id' AND brand_id IN (".join(',',$ids).")");
		$dd=array();
		foreach($d as $v) $dd[$v['brand_id']]=$v['brand_id'];
		$ids=array_diff($ids,$dd);
		if(count($ids)){
			foreach($ids as $v){
				$ds->query("INSERT INTO cc_dataset_brand (dataset_id,brand_id) VALUES('$dataset_id','$v')");
				$i++;
			}
		}
	}
	note ("Включено в набор <b>$i</b> брендов. Бренды, добавленные в выбранный набор будут отображаться в списке на зеленом фоне.");
}
if(@$_POST['unlinkDataset']==1 && $dataset_id){
	$i1=$i2=$i3=0;
	$ids=array();
	foreach($_POST as $k=>$v) {
		$x=explode('_',$k);
		if(@$x[0]=='c' && @$x[1]>0){ // x[1] - brand_id
			$ids[$x[1]]=(int)$x[1];
		}
	}
	if(count($ids)){
//		$ds->query("DELETE FROM cc_dataset_cat WHERE dataset_id='$dataset_id' AND brand_id IN (".join(',',$ids).")");
//		$i3=$ds->updatedNum();
		$ds->query("DELETE FROM cc_dataset_model WHERE dataset_id='$dataset_id' AND brand_id IN (".join(',',$ids).")");
		$i2=$ds->updatedNum();
		$ds->query("DELETE FROM cc_dataset_brand WHERE dataset_id='$dataset_id' AND brand_id IN (".join(',',$ids).")");
		$i1=$ds->updatedNum();
	}
		
	note ("Исключено из набора <b>$i1 брендов</b>, <b>$i2 моделей</b>, <b>$i3 размеров</b>.");
}
/// ***** обработка чекбоксов популярности брендов
if (@$_POST['popular_posted']>0 && !isset($_GET['replica'])){
    $d=$ds->fetchAll("SELECT cc_brand.brand_id AS brand_id FROM cc_brand WHERE NOT cc_brand.LD AND gr='$gr' ORDER BY cc_brand.name ASC");
    foreach ($d as $br)
    {
        $value=(int)in_array($br['brand_id'], array_keys(!empty($_POST['bpupular']) ? $_POST['bpupular'] : Array()));
        if (!$cc->query("UPDATE cc_brand SET is_popular='$value' WHERE brand_id='{$br['brand_id']}'")) warn('Ошибка записи.');
    }

}
// ***
foreach ($_POST as $key=>$value) {
	if (($a=explode('_',$key))!==FALSE){
		if (@$_POST['extra_post']>0 && $a[0]=='extra' && $a[1]>0){
			$bid=(int)$a[1];
			$value=(float)$value;
			if (!$cc->query("UPDATE cc_brand SET extra_b='$value' WHERE brand_id='$bid'")) warn ('Ошибка записи.');
			else $cc->addCacheTask('prices',$gr);
		} elseif (@$_POST['pos_post']>0 && $a[0]=='pos' && $a[1]>0){
			$bid=(int)$a[1];
			$value=(int)$value;
			if (!$cc->query("UPDATE cc_brand SET pos='$value' WHERE brand_id='$bid'")) warn('Ошибка записи.');
		}
	} 
	if(!is_array($value))  $$key=Tools::esc($value); else $$key=$value;
}
if(@$del_sel>0){
	$i=0;
	foreach($_POST as $k=>$v) {
		$x=explode('_',$k);
		if(@$x[0]=='c' && @$x[1]>0){
			if($cc->ld('cc_brand','brand_id',$x[1])) $i++;
		}
	}
	note("Удалено $i брендов");
}

if(@$delTI>0){
	$ic=$im=$ib=0;
	foreach($_POST as $k=>$v) {
		$x=explode('_',$k);
		if(@$x[0]=='c' && @$x[1]>0){
			$models=$cc->fetchAll("SELECT model_id FROM cc_model WHERE brand_id='{$x[1]}'");
			foreach($models as $mid){
				$cc->query("UPDATE cc_cat SET ti_id=0, ti_file_id=0 WHERE model_id='{$mid['model_id']}'");
				$ic=$ic+$cc->updatedNum();
			}
			$cc->query("UPDATE cc_model SET ti_id=0, ti_file_id=0 WHERE brand_id='{$x[1]}'");
			$im=$im+$cc->updatedNum();
			$cc->query("UPDATE cc_brand SET ti_id=0, ti_file_id=0 WHERE brand_id='{$x[1]}'");
			$ib=$ib+$cc->updatedNum();
		}
	}
	note("Удалено привязок: для размеров $ic, для моделей $im, для брендов $ib");
}

if(@$_POST['pos_nul']==1){
	$cc->query("UPDATE cc_brand SET pos=0 WHERE gr='$gr' ".($aq!=''?" AND $aq":''));
}

if (@$ld_id>0) if (!$cc->ld('cc_brand','brand_id',$ld_id,$gr)) {
	note('Удалено.');
}

if (@$hide_id>0) $cc->hide_switch('cc_brand','brand_id',$hide_id);

if (@$edit_id>0)
    if(isset($post)) {
        $rep=(int)@$replica;
        $a=App_TFields::DBupdate('cc_brand',@$af,$gr);
        $sname=trim($sname);
        $sup_id=(int)@$_POST['sup_id'];
        $avto_id=(int)@$avto_id;
        $text=@$tmh_text!=''?$tmh_text:$text;
        // SEO - поля
        $seo_h1    = @$_POST['seo_h1'];
        $seo_h2    = @$_POST['seo_h2'];
        $seo_title = @$_POST['seo_title'];
        $seo_desc  = @$_POST['seo_desc'];
        $seo_key   = @$_POST['seo_key'];
        $is_seo    = (int)@$_POST['is_seo'];
        $seo_img_spy = @$_POST['seo_img_spy'];
        //
        $text=Tools::untaria($text,0);
        if (!$cc->query("UPDATE cc_brand SET alt='$name_alt', name='$name', text='$text', avto_id='$avto_id', hit_quant='$hit_quant', extra_b='$extra_b', replica='$rep', sup_id='$sup_id', seo_h1='$seo_h1', seo_h2='$seo_h2', seo_title='$seo_title', seo_desc='$seo_desc', seo_key='$seo_key', is_seo='$is_seo' {$a}  WHERE brand_id='$edit_id'")) warn('Ошибка записи.');
        else{
            if($sname0!=$sname) $cc->sname_brand($edit_id,$sname,false);

            $uploader=new Uploader();
            // SEO - картинка
            if (!empty($_FILES['seo_img']['name'])) {
                if(!$uploader->upload('seo_img', Uploader::$EXT_GRAPHICS)){
                   warn($uploader->strMsg());
                }else{
                    if(!$cc->imgUpload('cc_brand', $edit_id, $gr, 3, $uploader->sfile, 'seo_img')){
                        warn($cc->strMsg());
                    }
                    $uploader->del();
                }
            }elseif(!empty($seo_img_spy)){
                if(!$uploader->spyUrl($seo_img_spy, Uploader::$EXT_GRAPHICS)){
                    warn($uploader->strMsg());
                }else{
                    if(!$cc->imgUpload('cc_brand', $edit_id, $gr, 3, $uploader->sfile, 'seo_img')){
                        warn($cc->strMsg());
                    }
                }
                $uploader->del();
            }else{
                if (@$del_seo_img==1) if (!$cc->imgDelete('cc_brand','brand_id',$edit_id,'seo_img')) warn($cc->strMsg());
            }
            // ***

            if (!empty($_FILES['img1']['name'])) {
                if(!$uploader->upload('img1', Uploader::$EXT_GRAPHICS)){
                   warn($uploader->strMsg());
                }else{
                    if(!$cc->imgUpload('cc_brand', $edit_id, $gr, 1, $uploader->sfile)){
                        warn($cc->strMsg());
                    }
                    $uploader->del();
                }
            }elseif(!empty($spy1)){
                if(!$uploader->spyUrl($spy1, Uploader::$EXT_GRAPHICS)){
                    warn($uploader->strMsg());
                }else{
                    if(!$cc->imgUpload('cc_brand', $edit_id, $gr, 1, $uploader->sfile)){
                        warn($cc->strMsg());
                    }
                }
                $uploader->del();
            }else{
                if (@$del_img1==1) if (!$cc->imgDelete('cc_brand','brand_id',$edit_id,'img1')) warn($cc->strMsg());
            }


            if (!empty($_FILES['img2']['name'])) {
                if(!$uploader->upload('img2', Uploader::$EXT_GRAPHICS)){
                    warn($uploader->strMsg());
                }else{
                    if(!$cc->imgUpload('cc_brand', $edit_id, $gr, 2, $uploader->sfile)){
                        warn($cc->strMsg());
                    }
                }
                $uploader->del();
            }elseif(!empty($spy2)){
                if(!$uploader->spyUrl($spy2, Uploader::$EXT_GRAPHICS)){
                    warn($uploader->strMsg());
                }else{
                    if(!$cc->imgUpload('cc_brand', $edit_id, $gr, 2, $uploader->sfile)){
                        warn($cc->strMsg());
                    }
                }
                $uploader->del();
            }else{
                if (@$del_img2==1) if (!$cc->imgDelete('cc_brand','brand_id',$edit_id,'img2')) warn($cc->strMsg());
            }

           $cc->addCacheTask('brands prices',$gr);
        }

    }else {
        include('brand_post.php');
        cp_end();
        exit();
    }

elseif (isset($post)) {
    $rep=@$replica;
    $sup_id=(int)@$_POST['sup_id'];
    $text=@$tmh_text!=''?$tmh_text:$text;
	$text=Tools::untaria($text,0);
    $avto_id=(int)@$avto_id;
	$a=App_TFields::DBinsert('cc_brand',@$af,$gr);
	if (!$cc->query("INSERT INTO cc_brand (gr,name,text,extra_b,replica,alt,hit_quant,sup_id,avto_id{$a[0]}) VALUES('$gr','$name','$text','$extra_b','$rep','$name_alt','$hit_quant','$sup_id', '$avto_id' {$a[1]})")) warn('Ошибка записи.');
	else {
		$brand_id=$cc->lastId();
		$cc->query("SELECT max(brand_id) FROM cc_brand");
		$cc->next();
		$max=$cc->qrow[0];
		if (!$cc->query("UPDATE cc_brand SET pos='$max' WHERE brand_id='$max'")) warn('Ошибка записи2.');
		note('Бренд добавлен.');
		$cc->sname_brand($brand_id,$sname);


        $uploader=new Uploader();

        if (!empty($_FILES['img1']['name'])) {
            if(!$uploader->upload('img1', Uploader::$EXT_GRAPHICS)){
                warn($uploader->strMsg());
            }else{
                if(!$cc->imgUpload('cc_brand', $brand_id, $gr, 1, $uploader->sfile)){
                    warn($cc->strMsg());
                }
                $uploader->del();
            }
        }elseif(!empty($spy1)){
            if(!$uploader->spyUrl($spy1, Uploader::$EXT_GRAPHICS)){
                warn($uploader->strMsg());
            }else{
                if(!$cc->imgUpload('cc_brand', $brand_id, $gr, 1, $uploader->sfile)){
                    warn($cc->strMsg());
                }
            }
            $uploader->del();
        }


        if (!empty($_FILES['img2']['name'])) {
            if(!$uploader->upload('img2', Uploader::$EXT_GRAPHICS)){
                warn($uploader->strMsg());
            }else{
                if(!$cc->imgUpload('cc_brand', $brand_id, $gr, 2, $uploader->sfile)){
                    warn($cc->strMsg());
                }
            }
            $uploader->del();
        }elseif(!empty($spy2)){
            if(!$uploader->spyUrl($spy2, Uploader::$EXT_GRAPHICS)){
                warn($uploader->strMsg());
            }else{
                if(!$cc->imgUpload('cc_brand', $brand_id, $gr, 2, $uploader->sfile)){
                    warn($cc->strMsg());
                }
            }
            $uploader->del();
        }


		$cc->addCacheTask('brands',$gr);
	}
}

if (@$add_id>0){
	include('brand_post.php');
	cp_end();
	exit();
}

	
?>
<style type="text/css">
	INPUT{
		text-align:center; 
		vertical-align:middle
	}
	table.ui-table th{
		cursor:pointer
	}
	.row{
		margin:5px 0;
		display:block;
		overflow:hidden
	}
	.msg-block{
		margin:5px; 0;
	}
</style>

<?
$ds->d=$ds->datasetList(array('gr'=>$gr));
if($gr==2) $cc->load_sup(0);
?>
<form name="form1" method="post">
    <input name="edit_id" value="-1" type="hidden">
    <input name="ld_id" value="-1" type="hidden">
    <input name="del_sel" value="-1" type="hidden">
    <input name="hide_id" value="-1" type="hidden">
    <input name="add_id" value="-1" type="hidden">
    <input name="delTI" value="-1" type="hidden">
    <input name="extra_post" value="-1" type="hidden">
    <input name="pos_post" value="-1" type="hidden">
    <input name="popular_posted" value="-1" type="hidden">
    <input name="pos_nul" value="-1" type="hidden">
    <input name="linkDataset" value="-1" type="hidden">
    <input name="unlinkDataset" value="-1" type="hidden">
    <div class="row">
        <? if(count($ds->d) && false){?>
	        <input type="submit" value="Добавить выбранное в набор" onClick="document.form1.linkDataset.value=1">
	        <input type="submit" value="Удалить выбранное из набора" onClick="document.form1.unlinkDataset.value=1">
            <select name="dataset_id">
				<option value="0">Набор данных</option><?
	            foreach($ds->d as $v){
                	?><option<?=$v['dataset_id']==$dataset_id?' selected':''?> value="<?=$v['dataset_id']?>"><?=Tools::unesc($v['name'])." (".$ds->classes[$v['class']]['name'].")"?></option><?
            	}
            ?></select>
	    	<input type="submit" value="Выбрать набор" />
        <? }?>
        <input type="checkbox" value="1"<?=@$_COOKIE['__cp_hide_hidden_brands'.$gr]?' checked="checked"':''?> id="hhb<?=$gr?>" /><label for="hhb<?=$gr?>">не показывать скрытые бренды</label>
     </div>
    <div class="row">
        <input type="submit" value="+ Добавить бренд" onClick="document.form1.add_id.value=1;">
        <input type="submit" value="Сохранить наценки" onClick="document.form1.extra_post.value=1">
        <input type="button" value="Удалить выбранное" onClick="do_form(0,6); return false">
        <input type="submit" value="Сохранить порядок" onClick="document.form1.pos_post.value=1">
        <?if(!isset($_GET['replica'])):?><input type="submit" value="Сохранить популярность" onClick="document.form1.popular_posted.value=1"><?endif;?>
        <? if(in_array(Cfg::get('CAT_IMPORT_MODE'),array(1,3))){?><input type="submit" value="Удалить привязки TyreIndex" onClick="document.form1.delTI.value=1"><? }?>
    </div>
    <?
	$cc->brands(array(
		'gr'=>$gr,
		'order'=>'sdiv DESC, cc_brand.pos DESC,cc_brand.name ASC',
		'where'=>$aq,
		'notH'=>@$_COOKIE['__cp_hide_hidden_brands'.$gr]?1:0,
		'qSelect'=>array(
			'modelsNum'=>array(
				'notH'=>1
			)
		),
		'select'=>array(
            'cc_brand.sup_id DIV cc_brand.sup_id'=>'sdiv',
			'cc_brand.brand_id'=>'brand_id',
			'cc_brand.extra_b'=>'extra_b',
			'cc_brand.H'=>'H',
			'cc_brand.name'=>'name',
			'cc_brand.alt'=>'alt',
			'cc_brand.sname'=>'sname',
			'cc_brand.text'=>'text',
			'cc_brand.img1'=>'img1',
			'cc_brand.img2'=>'img2',
			'cc_brand.pos'=>'pos',
			'cc_brand.is_popular'=>'is_popular',
			'cc_brand.is_seo'=>'is_seo',
			'cc_brand.replica'=>'replica',
			'cc_brand.hit_quant'=>'hit_quant',
			'cc_brand.sup_id'=>'bsup_id',
            '(SELECT name FROM ab_avto WHERE ab_avto.avto_id=cc_brand.avto_id)'=>'avtoName'
		)
	));
//	echo $cc->sql_query;
    $l=1;
	if($cc->qnum()){?>
    <table class="ui-table tablesorter">
        <thead>
          <tr>
            <th><input type="checkbox"  onclick="SelectAll(checked,'form1')"></th>
            <th scope="col">ID</th>
            <th scope="col">и</th>
            <th scope="col">И</th>
            <th scope="col">i</th>
            <th scope="col">Название</th>
            <th scope="col">Псевдоним</th>
            <th scope="col">Наценка
              на бренд %</th>
            <th scope="col">% на радиус</th>
            <th scope="col">Доп. комплек.</th>
            <th scope="col">Скрыть</th>
            <th scope="col" style="width:120px">Кол-во моделей (не скрытых)</th><?
            if($hq=Cfg::get('cmsShowHitQuant')){
				?><th>Популярность</th><? 
            }
              ?>
              <?if(!isset($_GET['replica'])):?><th>Популярен?</th><?endif;?>
              <th>SEO</th>
              <th scope="col">Порядок</th>
              <?
            if(isset($_GET['replica'])){
              ?><th>Связь с базой подбора</th><? }
            ?><th scope="col">Удалить</th>
          </tr>
        </thead>
        <tbody>
        <? $dsl=array();
		if($dataset_id){
			$ds->query("SELECT brand_id FROM cc_dataset_brand WHERE dataset_id='$dataset_id'");
			if($ds->qnum()) while($ds->next()!==false) $dsl[]=$ds->qrow['brand_id'];
		}
        while ($cc->next()!=FALSE){
            echo "<tr id=\"bid_{$cc->qrow['brand_id']}\"".(in_array($cc->qrow['brand_id'],$dsl)?' class="inds"':'').">";
            echo '<td><input id="cc" type="checkbox" name="c_'.$cc->qrow['brand_id'].'" value="1"></td>';	
            echo '<td align=center>'.$cc->qrow['brand_id'].'</td>';
            echo '<td>';if($cc->qrow['img1']!='')echo'<span class=hide>1</span><img src="../img/img.gif" border="0">';echo'</td>';
            echo '<td>';if($cc->qrow['img2']!='')echo'<span class=hide>1</span><img src="../img/img.gif" border="0">';echo'</td>';
            echo '<td>';
                if(trim(Tools::stripTags($cc->qrow['text']))!='')echo'<span class=hide>1</span><img src="../img/mods.gif" border="0">';
            echo'</td>';
            echo '<td><a href="javascript:;" onClick="do_form('.$cc->qrow['brand_id'].',1);return false">'.Tools::unesc($cc->qrow['name']).'</a>'.($cc->qrow['alt']!=''?(' ('.Tools::unesc($cc->qrow['alt']).')'):'').($cc->qrow['bsup_id']?(' <b style="cursor:help" title="Привязан к поставщику '.$cc->sup_arr[$cc->qrow['bsup_id']].'">('.$cc->sup_arr[$cc->qrow['bsup_id']].')</b> '):'').'</td>';
            echo '<td>'.$cc->qrow['sname'].'</td>';
            echo "<td align=center><span class=hide>{$cc->qrow['extra_b']}</span><input name=\"extra_{$cc->qrow['brand_id']}\" value=\"{$cc->qrow['extra_b']}\" type=\"text\" size=\"5\"></td>\n";
            echo "<td align=\"center\"><a href=\"javascript:;\" onClick=\"openwin('extra.php?brand_id={$cc->qrow['brand_id']}','extra');return false;\"><...></a></td>\n";
            echo "<td align=\"center\"><a href=\"javascript:;\" onClick=\"openwin('dop.php?brand_id={$cc->qrow['brand_id']}','dop');return false;\"><...></a></td>\n";
            echo '<td nowrap align="center"><span class=hide>'.((int)$cc->qrow['H']).'</span><a href="#" class="h-sw">'.(($cc->qrow['H']!='1')?'скрыть':'отобразить').'</a></td>';
            echo '<td align="center">'.$cc->qrow['modelsNum'].'</td>';
            if($hq) echo '<td align="center">'.$cc->qrow['hit_quant'].'</td>';

            if(!isset($_GET['replica'])){
                echo "<td align=center><input name=\"bpupular[{$cc->qrow['brand_id']}]\" value=\"1\" type=\"checkbox\"".($cc->qrow['is_popular'] ? ' checked="true"' : '')."></td>\n";
            }
            echo "<td align=center>".(($cc->qrow['is_seo'] == 1) ? 'Да' : 'Нет')."</td>\n";
            echo "<td align=center><span class=hide>{$cc->qrow['pos']}</span><input name=\"pos_{$cc->qrow['brand_id']}\" value=\"{$cc->qrow['pos']}\" type=\"text\" size=\"5\"></td>\n";
            if(isset($_GET['replica'])) echo '<td align="center">'.$cc->qrow['avtoName'].'</td>';
            echo '<td nowrap align="center"><a href="javascript:;" onClick="do_form('.$cc->qrow['brand_id'].',2);return false"><img src="../img/b_drop.png" border="0"></a></td>';
            echo '</tr>';
            $l++;
        }
        ?></tbody>
    </table>
    <? }?>
    <input type="submit" value="+ Добавить бренд" onClick="document.form1.add_id.value=1;">
    <input type="submit" value="Сохранить наценки" onClick="document.form1.extra_post.value=1">
    <input type="button" value="Удалить выбранное" onClick="do_form(0,6); return false">
    <input type="submit" value="Сохранить порядок" onClick="document.form1.pos_post.value=1">
    <?if(!isset($_GET['replica'])):?><input type="submit" value="Сохранить популярность" onClick="document.form1.popular_posted.value=1"><?endif;?>
    <? if(in_array(Cfg::get('CAT_IMPORT_MODE'),array(1,3))){?><input type="submit" value="Удалить привязки TyreIndex" onClick="document.form1.delTI.value=1"><? }?>
</form>



<? cp_end()?>
