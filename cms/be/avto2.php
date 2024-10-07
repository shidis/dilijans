<?
include_once ('ajx_loader.php');

$cp->setFN('avto2');
$cp->checkPermissions();

//sleep(2);
$ab=new CC_AB();

$r->fres=true;
$r->fres_msg='';

$page = @$_REQUEST['page']; // get the requested page
$rows = @$_REQUEST['rows']; // get how many rows we want to have into the grid
$sidx = @$_REQUEST['sidx']; // get index row - i.e. user click to sort
$sord = @$_REQUEST['sord']; // get the direction
if(!$sidx) $sidx =1;
$id = intval(@$_REQUEST['id']);
$parent_id = intval(@$_REQUEST['parent_id']);
$parent_name = Tools::esc(@$_REQUEST['parent_name']);
$v = Tools::esc(@$_REQUEST['v']);
$sname=Tools::str2iso($v,-1,'');
$act=Tools::esc($_REQUEST['act']);
$box=Tools::esc(@$_REQUEST['box']);
$vendor_id = intval(@$_REQUEST['vendor_id']);
$model_id = intval(@$_REQUEST['model_id']);
$year_id = intval(@$_REQUEST['year_id']);
$modif_id = intval(@$_REQUEST['modif_id']);
if(!empty($_REQUEST['avto_id'])) $avto_id = intval(@$_REQUEST['avto_id']);

switch ($act){

case 'edit':
    $ab->query("UPDATE ab_avto SET name='$v' WHERE avto_id='$id'");
    $r->fres_msg='Отредактировано.';
    if($parent_name!=''){
    	$parent_name.='_id';
    	$$parent_name=$parent_id;
    }
$act=$box.'s';
break;

case 'add':
$r->parent=$parent_name;
switch ($parent_name){
	case 'model': 
		$ab->query("SELECT * FROM ab_avto WHERE (avto_id='$parent_id')"); 
		$ab->next(); 
		$vendor_id=$ab->qrow['vendor_id'];
		$ab->query("INSERT INTO ab_avto (name,sname,vendor_id,model_id,manual_insert) VALUES('$v','$sname','$vendor_id','$parent_id',1)");
	break;	
	case 'year': 
		$ab->query("SELECT * FROM ab_avto WHERE (avto_id='$parent_id')"); 
		$ab->next(); 
		$vendor_id=$ab->qrow['vendor_id'];
		$model_id=$ab->qrow['model_id'];
		$ab->query("INSERT INTO ab_avto (name,sname,vendor_id,model_id,year_id,manual_insert) VALUES('$v','$sname','$vendor_id','$model_id','$parent_id',1)");
	break;	
	case '': $ab->query("INSERT INTO ab_avto (name,sname,manual_insert) VALUES('$v','$sname',1)"); break;
	case 'vendor': $ab->query("INSERT INTO ab_avto (name,sname,{$parent_name}_id,manual_insert) VALUES('$v','$sname','$parent_id',1)"); break;
}
$r->fres_msg='Добавлено.';
if($parent_name!=''){
	$parent_name.='_id';
	$$parent_name=$parent_id;
}
//$id=mysql_insert_id();
$act=$box.'s';
break;

case 'hide':
$ab->query("UPDATE ab_avto SET H=1 WHERE avto_id='$id'");
$r->fres_msg='Убрали.';
if($parent_name!=''){
	$parent_name.='_id';
	$$parent_name=$parent_id;
}
$act=$box.'s';
break;

}

switch ($act){
case 'vendors': 
$ab->query('SELECT * FROM ab_avto WHERE (vendor_id=0)AND(NOT H) ORDER BY name');
$r->data=
<<<html
<select id="vendor" e="марку авто" >
<option value="0">Выбираем марку авто</option>
html;
while($ab->next()!==false){
	if ($ab->qrow['manual_insert']){
		$manual_insert = ' class="manual_insert"';
	}else $manual_insert = '';
$r->data.=
<<<html
<option value="{$ab->qrow['avto_id']}" {$manual_insert}
html;
if($ab->qrow['avto_id']==$id) $r->data.=" selected";
$ab->qrow['name']=Tools::unesc($ab->qrow['name']);
$r->data.=
<<<html
>{$ab->qrow['name']}</option>
html;
}
$r->data.=
<<<html
</select>
<nobr>
<input type="image" class="e" id="hide" src="../img/delete.gif" title="Скрыть">
<input type="image" class="e" id="add" src="../img/add.gif" title="Добавить">
<input type="image" class="e" id="edit" src="../img/b_edit.png" title="Изменить">
</nobr>
html;
break;

case 'models': 
$ab->query("SELECT * FROM ab_avto WHERE (vendor_id='{$vendor_id}')AND(model_id=0)AND(NOT H) ORDER BY name");
$r->data=
<<<html
<select id="model" e="модель" >
<option value="0">Модель авто</option>
html;
while($ab->next()!==false){
	if ($ab->qrow['manual_insert']){
		$manual_insert = ' class="manual_insert"';
	}else $manual_insert = '';
$r->data.=
<<<html
<option value="{$ab->qrow['avto_id']}" {$manual_insert}
html;
if($ab->qrow['avto_id']==$id) $r->data.=" selected";
$ab->qrow['name']=Tools::unesc($ab->qrow['name']);
$r->data.=
<<<html
>{$ab->qrow['name']}</option>
html;
}
$r->data.=
<<<html
</select>
<nobr>
<input type="image" class="e" id="hide" src="../img/delete.gif" title="Скрыть">
<input type="image" class="e" id="add" src="../img/add.gif" title="Добавить">
<input type="image" class="e" id="edit" src="../img/b_edit.png" title="Изменить">
</nobr>
html;
break;

case 'years': 
$ab->query("SELECT * FROM ab_avto WHERE (model_id='{$model_id}')AND(year_id=0)AND(NOT H) ORDER BY name");
$r->data=
<<<html
<select id="year" e="год выпуска" >
<option value="0">Выбираем год</option>
html;
while($ab->next()!==false){
	if ($ab->qrow['manual_insert']){
		$manual_insert = ' class="manual_insert"';
	}else $manual_insert = '';
$r->data.=
<<<html
<option value="{$ab->qrow['avto_id']}" {$manual_insert}
html;
if($ab->qrow['avto_id']==$id) $r->data.=" selected";
$ab->qrow['name']=Tools::unesc($ab->qrow['name']);
$r->data.=
<<<html
>{$ab->qrow['name']}</option>
html;
}
$r->data.=
<<<html
</select>
<nobr>
<input type="image" class="e" id="hide" src="../img/delete.gif" title="Скрыть">
<input type="image" class="e" id="add" src="../img/add.gif" title="Добавить">
<input type="image" class="e" id="edit" src="../img/b_edit.png" title="Изменить">
<input type="image" class="" id="copy" onclick="copy()" width="16px" height="16px" src="../img/copy.png" title="Копировать">
</nobr>
html;
break;

case 'modifs': 
$ab->query("SELECT * FROM ab_avto WHERE (year_id='{$year_id}')AND(NOT H) ORDER BY name");
$r->data=
<<<html
<select id="modif" e="модификацию" >
<option value="0">Модификация</option>
html;
while($ab->next()!==false){
	if ($ab->qrow['manual_insert']){
		$manual_insert = ' class="manual_insert"';
	}else $manual_insert = '';
$r->data.=
<<<html
<option value="{$ab->qrow['avto_id']}" {$manual_insert}
html;
if($ab->qrow['avto_id']==$id) $r->data.=" selected";
$ab->qrow['name']=Tools::unesc($ab->qrow['name']);
$r->data.=
<<<html
>{$ab->qrow['name']}</option>
html;
}
$r->data.=
<<<html
</select>
<nobr>
<input type="image" class="e" id="hide" src="../img/delete.gif" title="Скрыть">
<input type="image" class="e" id="add" src="../img/add.gif" title="Добавить">
<input type="image" class="e" id="edit" src="../img/b_edit.png" title="Изменить">

</nobr>
html;
break;

case 'tyres':
  if(!$modif_id) break;
  $ab->avto_sh0($modif_id);
  $i=0;
  foreach(array(10,15,18,12) as $type_id){
    if(isset($ab->avto[1][$type_id]))
      foreach($ab->avto[1][$type_id] as $k=>$v){
        $r->rows[$i]['id']=$v['avtosh_id'];
        $r->rows[$i]['manual_insert']=$v['manual_insert'];
        $r->rows[$i]['cell']=array($v['avtosh_id'],$v['pos'],'',array_search($type_id,array(10,12))!==false?'заводская':'альтернатива',$v['P3'],$v['P2'],$v['P1']);
        if(isset($ab->avto[1][$type_id+1][$k])) $r->rows[$i]['cell']=array_merge($r->rows[$i]['cell'],array('задняя ось',$ab->avto[1][$type_id+1][$k]['P3'],$ab->avto[1][$type_id+1][$k]['P2'],$ab->avto[1][$type_id+1][$k]['P1'],$v['manual_insert'])); else $r->rows[$i]['cell']=array_merge($r->rows[$i]['cell'], array('задняя ось','','','',$v['manual_insert']));
        $i++;
      }
  }

  $tree = $ab->getTree($modif_id);
  $addonParts = [];
  if (isset($ab->tree['vendor_sname'])) $addonParts[] = Tools::unesc($ab->tree['vendor_sname']);
  if (isset($ab->tree['model_sname'])) $addonParts[] = Tools::unesc($ab->tree['model_sname']);
  if (isset($ab->tree['year_sname'])) $addonParts[] = Tools::unesc($ab->tree['year_sname']);
  if (isset($ab->tree['modif_sname'])) $addonParts[] = Tools::unesc($ab->tree['modif_sname']);
  $r->link = Url::getLink('tyre', $addonParts);
break;

case 'disks':
  if(!$modif_id) break;
  $ab->avto_sh0($modif_id);
  $i=0;
  foreach(array(22,28,20,25) as $type_id){
    if(isset($ab->avto[2][$type_id]))
      foreach($ab->avto[2][$type_id] as $k=>$v){
        $r->rows[$i]['id']=$v['avtosh_id'];
        $r->rows[$i]['cell']=array($v['avtosh_id'],$v['pos'],'',array_search($type_id,array(20,22))!==false?'заводская':'альтернатива',$v['P2'],$v['P5'],$v['P1'],$v['P4'],$v['P6'],$v['P3']);
        if(isset($ab->avto[2][$type_id+1][$k]))
          $r->rows[$i]['cell']=array_merge($r->rows[$i]['cell'],array('задняя ось',$ab->avto[2][$type_id+1][$k]['P2'],$ab->avto[2][$type_id+1][$k]['P5'],$ab->avto[2][$type_id+1][$k]['P1'],$ab->avto[2][$type_id+1][$k]['P4'],$ab->avto[2][$type_id+1][$k]['P6'],$ab->avto[2][$type_id+1][$k]['P3'], $v['manual_insert']));
          else $r->rows[$i]['cell']=array_merge($r->rows[$i]['cell'], array('задняя ось','','','','','','',$v['manual_insert']));
        $i++;
      }
  }
  $tree = $ab->getTree($modif_id);
  $addonParts = [];
  if (isset($ab->tree['vendor_sname'])) $addonParts[] = Tools::unesc($ab->tree['vendor_sname']);
  if (isset($ab->tree['model_sname'])) $addonParts[] = Tools::unesc($ab->tree['model_sname']);
  if (isset($ab->tree['year_sname'])) $addonParts[] = Tools::unesc($ab->tree['year_sname']);
  if (isset($ab->tree['modif_sname'])) $addonParts[] = Tools::unesc($ab->tree['modif_sname']);
  $r->link = Url::getLink('disk', $addonParts);
break;

case 'del':
case 'del_tyres':
case 'del_disks':
if(!$id) {
	$r->fres=false;
	$r->fres_msg='Нулевой ID';
}else{
	$ab->query("DELETE FROM ab_avtosh WHERE (avtosh_id='$id')");
	$ab->query("DELETE FROM ab_avtosh WHERE (rel_id='$id')");
}
break;

case 'tyres_edit':
$r->id=$id=intval(@$_REQUEST['id']);
$r->fres=true;
$r->fres_msg='';
if ($id && empty($_REQUEST['f'])){
	$gv = $_REQUEST;
}else $gv=strarr($_REQUEST['f']);
$p1=intval(@$gv['p1']);
$p2=intval(@$gv['p2']);
$p3=intval(@$gv['p3']);
$p1_1=intval(@$gv['p1_1']);
$p2_1=intval(@$gv['p2_1']);
$p3_1=intval(@$gv['p3_1']);
$type=@$gv['type'];
if(!$modif_id){
	$r->fres=false;
	$r->fres_msg='Нет modif_id';
}elseif($p1==0 || $p2==0 || $p3==0){
	$r->fres=false;
	$r->fres_msg='Не все значения передней оси указаны';
}else{
	$zad=false;
	$_type_id=0;
	if($p1_1!=0 || $p2_1!=0 || $p3_1!=0) 
		if($p1_1==0 || $p2_1==0 || $p3_1==0) {
			$r->fres_msg='Задняя ось не записана. Не все значения указаны';
			$r->fres=-1;
		}else $zad=true;
	if($type=='zavod' && !$zad) $type_id=10;
	else
	if($type=='zavod' && $zad) {$type_id=12; $_type_id=13;}
	else
	if($type=='zamena' && !$zad) $type_id=15;
	else
	if($type=='zamena' && $zad) {$type_id=18; $_type_id=19;}
	$r->type_id=$type_id;
	$r->_type_id=$_type_id;
	if($id) $ab->query("UPDATE ab_avtosh SET P1='$p1',P2='$p2',P3='$p3', avto_type_id='$type_id' WHERE avtosh_id='$id'");
	else {
		$ab->query("INSERT INTO ab_avtosh (avto_id,P1,P2,P3,avto_type_id,gr,manual_insert) VALUES('$modif_id','$p1','$p2','$p3','$type_id','1','1')");
		$r->id=mysql_insert_id();
	}
	$r->zad=$zad;
	if(!$r->id) {
		$r->fres=false;
		$r->fres_msg='Ошибка записи (1)';
	}else{
		if($id){
			$ab->query("SELECT * FROM ab_avtosh WHERE rel_id='$id'");
			$r->f0=$ab->sql_query;
			if($ab->qnum()){
				$ab->next();
				$r->zad_id=$zad_id=$ab->qrow['avtosh_id'];
				if($zad){ 
					$ab->query("UPDATE ab_avtosh SET P1='$p1_1',P2='$p2_1',P3='$p3_1', avto_type_id='$_type_id' WHERE avtosh_id='$zad_id'");
					$r->f1=$ab->sql_query;
				}else{
					$ab->query("DELETE FROM ab_avtosh WHERE avtosh_id='$zad_id'");
					$r->f1=$ab->sql_query;
				}
			}
		}
		if($zad && (!$id || !$ab->qnum)){
			$ab->query("INSERT INTO ab_avtosh (avto_id,P1,P2,P3,avto_type_id,gr,rel_id,manual_insert) VALUES('$modif_id','$p1_1','$p2_1','$p3_1','$_type_id','1','{$r->id}','1')");
			$r->f2=$ab->sql_query;
		}

	}
	// Выставляем позицию
	$pos=intval(@$_REQUEST['pos']);
	if($id && !empty($pos)){
		$ab->query("UPDATE ab_avtosh SET pos='$pos'WHERE avtosh_id='$id'");
	}
	//
	$r->fres_msg='Записано id='.$r->id;
}
break;

case 'disks_edit':
$r->id=$id=intval(@$_REQUEST['id']);
$gv=strarr($_REQUEST['f']);
if ($id && empty($_REQUEST['f'])){
	$gv = $_REQUEST;
}else $gv=strarr($_REQUEST['f']);
$p=explode('-',@$gv['p1']);
$p1=floatval(@$p[0]);
$p_1=floatval(@$p[1]);
$p2=floatval(@$gv['p2']);
$p3=floatval(@$gv['p3']);
$p5=floatval(@$gv['p5']);
$p4=floatval(@$gv['p4']);
$p6=floatval(@$gv['p6']);
$p=explode('-',@$gv['p1_1']);
$p1_1=floatval(@$p[0]);
$p_1_1=floatval(@$p[1]);
$p2_1=floatval(@$gv['p2_1']);
$p3_1=floatval(@$gv['p3_1']);
$p5_1=floatval(@$gv['p5_1']);
$p4_1=floatval(@$gv['p4_1']);
$p6_1=floatval(@$gv['p6_1']);
$type=@$gv['type'];
if(!$modif_id){
	$r->fres=false;
	$r->fres_msg='Нет modif_id';
}elseif($p2==0 || $p5==0 || $p1==0){
	$r->fres=false;
	$r->fres_msg='Не все значения передней оси указаны';
}else{
	$zad=false;
	$_type_id=0;
	if($p2_1!=0 || $p5_1!=0 || $p1_1!=0) 
		if($p1_1==0 || $p5_1==0 || $p2_1==0) {
			$r->fres_msg='Задняя ось не записана. Не все значения указаны';
			$r->fres=-1;
		}else $zad=true;
	if($type=='zavod' && !$zad) $type_id=20;
	else
	if($type=='zavod' && $zad) {$type_id=22; $_type_id=23;}
	else
	if($type=='zamena' && !$zad) $type_id=25;
	else
	if($type=='zamena' && $zad) {$type_id=28; $_type_id=29;}
	$r->type_id=$type_id;
	$r->_type_id=$_type_id;
	if($id) $ab->query("UPDATE ab_avtosh SET P1='$p1',P2='$p2',P3='$p3',P4='$p4',P5='$p5',P6='$p6', avto_type_id='$type_id' WHERE avtosh_id='$id'");
	else {
		$ab->query("INSERT INTO ab_avtosh (avto_id,P1,P2,P3,P4,P5,P6,avto_type_id,gr,manual_insert) VALUES('$modif_id','$p1','$p2','$p3','$p4','$p5','$p6','$type_id','2','1')");
		$r->id=mysql_insert_id();
	}
	$r->zad=$zad;
	if(!$r->id) {
		$r->fres=false;
		$r->fres_msg='Ошибка записи (1)';
	}else{
		if($id){
			$ab->query("SELECT * FROM ab_avtosh WHERE rel_id='$id'");
			$r->f0=$ab->sql_query;
			if($ab->qnum()){
				$ab->next();
				$r->zad_id=$zad_id=$ab->qrow['avtosh_id'];
				if($zad){ 
					$ab->query("UPDATE ab_avtosh SET P1='$p1_1',P2='$p2_1',P3='$p3_1',P4='$p4_1',P5='$p5_1',P6='$p6_1', avto_type_id='$_type_id' WHERE avtosh_id='$zad_id'");
					$r->f1=$ab->sql_query;
				}else{
					$ab->query("DELETE FROM ab_avtosh WHERE avtosh_id='$zad_id'");
					$r->f1=$ab->sql_query;
				}
			}
		}
		if($zad && (!$id || !$ab->qnum)){
			$ab->query("INSERT INTO ab_avtosh (avto_id,P1,P2,P3,P4,P5,P6,avto_type_id,gr,rel_id,manual_insert) VALUES('$modif_id','$p1_1','$p2_1','$p3_1','$p4_1','$p5_1','$p6_1','$_type_id','2','{$r->id}','1')");
			$r->f2=$ab->sql_query;
		}

	}
	// Выставляем позицию
	$pos=intval(@$_REQUEST['pos']);
	if($id && !empty($pos)){
		$ab->query("UPDATE ab_avtosh SET pos='$pos'WHERE avtosh_id='$id'");
	}
	//
	$r->fres_msg='Записано id='.$r->id;
}
break;
case 'common':
	$modif_id = intval(@$_REQUEST['modif_id']);
	$cdata = $ab->getCommon($modif_id);
	if (!empty($cdata)){
		$r->commons = $cdata;
		$r->fres=true;
	}else $r->fres=false;
break;
case 'save_common':
	$modif_id = intval(@$_REQUEST['modif_id']);
	if (!empty($modif_id) && !empty($_REQUEST['cdata'])){
		$mod_data = $_REQUEST['cdata'];
		foreach($mod_data as &$dt){
			$dt = str_replace(Array('x', ','), Array('*', '.'), strtolower($dt));
		}
		$r->fres=$ab->setCommon($modif_id, $mod_data);
	}
	else $r->fres=false;
break;

    case 'get_common_list':
        $cdata = $ab->getCommonsList($avto_id);
        if (!empty($cdata)) {
            $r->fres=true;
            foreach ($cdata as $value) {
                $r->rows[] = array(
                    'id' => $value['common_id'],
                    'cell' => array(
                        'common_id' => $value['common_id'],
                        'pcd' => $value['pcd'],
                        'dia' => $value['dia'],
                        'bolt' => $value['bolt'],
                        'gaika' => $value['gaika'],
                    )
                );
            }
        } else {
            $r->fres=false;
        }
        break;
    case 'edit_common':
        $fields = $_POST;
        if(!empty($_POST['id'])) {
            $id = $_POST['id'];
            unset($fields['id']);
            unset($fields['oper']);

            if(count($fields)) {
                $setStr = '';
                $arSets = array();
                foreach ($fields as $fieldKey => $fieldValue) {
                    $arSets[] = "`".$fieldKey."` = '".$fieldValue."'";
                }
                $setStr = implode(', ', $arSets);
            }

            if (!empty($setStr)) {
//                print_r("UPDATE ab_common SET " . $setStr . " WHERE `common_id`=".$id);
//                die();
                $ab->query("UPDATE ab_common SET " . $setStr . " WHERE `common_id`=".$id);
                $r->fres_msg='Отредактировано.';
            } else {
                $r->fres_msg='Произошла ошибка при редактировании.';
            }
//            print_r($setStr);
//            die();

        } else {
            $r->fres = false;
        }

//        $ab->query("UPDATE ab_avto SET name='$v' WHERE avto_id='$id'");


        break;
    case 'del_common':
        $common_id = intval(@$_REQUEST['common_id']);
        if (!empty($common_id)){
            $r->fres = true;
            $r->fres=$ab->delCommon($common_id);
            $r->fres_msg='Удалено.';
        } else {
            $r->fres = false;
            $r->fres_msg='Произошла ошибка при удалении.';
        }
        break;

default: echo 'BAD ACT ID '.$act;
}

ajxEnd();