<?php

include_once ('ajx_loader.php');


$model = htmlspecialchars($_GET['model']);
$year =  htmlspecialchars($_GET['year']);
$new_year =  htmlspecialchars($_GET['nyear']);
$vendor = htmlspecialchars($_GET['vendor']);

$ab=new CC_AB();

$str = 'SELECT avto_id FROM ab_avto WHERE name = "'.$new_year.'" AND model_id = "'.$model.'" AND H<>1';
$temp = $ab->getOne($str);

if($temp){
    $y_yd = $temp['avto_id'];
    put($ab,$y_yd,$model,$year,$vendor);

}else{
    $str = 'SELECT vendor_id FROM ab_avto WHERE year_id = "'.$year.'" AND model_id = "'.$model.'"';
    $temp = $ab->getOne($str, MYSQLI_ASSOC);
        $str = 'INSERT INTO ab_avto (name,sname, vendor_id,model_id,manual_insert) VALUES ("'.$new_year.'","'.$new_year.'","'.$vendor.'","'.$model.'",1)';
    $ab->query($str);
    $y_yd = $ab->lastId();
    put($ab,$y_yd,$model,$year,$temp['vendor_id']);
}

$r->fres=true;
$r->fres_msg='';
ajxEnd();

function put($ab,$y_yd,$model,$year,$vendor){
    $str = 'SELECT avto_id,name,sname FROM ab_avto WHERE year_id="'. $year.'" AND model_id="'.$model.'" AND vendor_id="'.$vendor.'"';//вытаскиваем информацию о модиикациях
    $temp = $ab->fetchAll($str, MYSQLI_ASSOC);

    $str = 'SELECT avto_id,year_id,avto_image FROM ab_avto WHERE avto_image IS NOT NULL AND model_id="'.$model.'" AND vendor_id="'.$vendor.'" ORDER BY year_id DESC,avto_id DESC'; // картинка из последнего года
    $imageRow = $ab->getOne($str, MYSQLI_ASSOC);
    $image = null;
    if (!empty($imageRow['avto_image'])) {
      $image = $imageRow['avto_image'];
    }

    foreach ($temp as $value) {
        $str = 'INSERT INTO ab_avto (name,sname,vendor_id,year_id,model_id, avto_image) VALUES ("'.$value['name'].'", "'.$value['sname'].'","'.$vendor.'","'.$y_yd.'","'.$model.'","'.$image.'")'; // помещаем название модификаци в бд с измененным параметром года
        $ab->query($str);
        $avto_id = $ab->lastId();

        $str = "SELECT * FROM ab_common WHERE avto_id =" . $value['avto_id'];
        $t = $ab->getOne($str,MYSQLI_ASSOC);
        $str = 'INSERT INTO ab_common (avto_id,pcd,dia,gaika,bolt,_upd) VALUES ("'.$avto_id.'","'.$t['pcd'].'","'.$t['dia'].'","'.$t['gaika'].'","'.$t['bolt'].'","'.$t['_upd'].'")';
        $ab->query($str);

        $oldIdToNew = array();
        $str = "SELECT * FROM ab_avtosh WHERE avto_id =" . $value['avto_id'];
        $t = $ab->fetchAll($str);
        $rel = $ab->fetchAll("SELECT * FROM ab_avtosh WHERE avto_id = '" . $value['avto_id']."' AND rel_id > 0", MYSQLI_ASSOC);
        foreach ($t as $i){
            $str = 'INSERT INTO ab_avtosh ( avto_id, P1, P2, P3, P4, P5, P6, avto_type_id, gr, rel_id, _upd, manual_insert, pos) 
                    VALUES ("'.$avto_id.'","'.$i['P1'].'","'.$i['P2'].'","'.$i['P3'].'","'.$i['P4'].'","'.$i['P5'].'","'.$i['P6'].'","'.$i['avto_type_id'].'","'.$i['gr'].'","'.$i['rel_id'].'","'.$i['_upd'].'","'.$i['manual_insert'].'","'.$i['pos'].'")';
            $ab->query($str);
            $oldIdToNew[$i['avtosh_id']] = $ab->lastId();
        }
        if(!empty($rel)) {
            foreach ($rel as $r){
                $ab->query("UPDATE `ab_avtosh` SET rel_id = '{$oldIdToNew[$r['rel_id']]}' WHERE rel_id = '{$r['rel_id']}' AND avto_id = '$avto_id';");
            }
        }
    }
}