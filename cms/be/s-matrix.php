<?
include_once ('ajx_loader.php');

$cp->setFN('s-matrix');
$cp->checkPermissions();

//sleep(1);


$r->fres=true;
$r->fres_msg='';

if(!in_array($CIM=Cfg::get('CAT_IMPORT_MODE'),array(2,3))){
    echo 'Настройками сайта не поддерживается этот функционал';
    exit();
}

$act=Tools::esc(@$_REQUEST['act']);


switch ($act){

    case 'saveCSuffix':
        $r->textOutput=true;
        $r->prependFresMsg=true;
        $v=Tools::esc(@$_REQUEST['value']);
        $id=(int)(@$_REQUEST['id']);
        $remakeUrl=@$_REQUEST['remakeUrl']=='checked'?true:false;
        if(!empty($id)){
            $cc=new CC_Ctrl();
            $cc->query("UPDATE cc_cat SET suffix='$v' WHERE cat_id='$id'");
            if($remakeUrl) $cc->sname_cat($id,'',true);
        }
        echo Tools::unesc($v);
        break;

    case 'saveMSuffix':
        $r->textOutput=true;
        $r->prependFresMsg=true;
        $v=Tools::esc(@$_REQUEST['value']);
        $id=(int)(@$_REQUEST['id']);
        $remakeUrl=@$_REQUEST['remakeUrl']=='checked'?true:false;
        if(!empty($id)){
            $cc=new CC_Ctrl();
            $cc->query("UPDATE cc_model SET suffix='$v' WHERE model_id='$id'");
            if($remakeUrl) $cc->sname_model($id,'',true);
        }
        echo Tools::unesc($v);
        break;

    case 'getCatData':
        $id=@$_REQUEST['id'];
        if(empty($id)){
            $r->fres=false;
            $r->fres_msg='Идентификатор суффикса не передан';
            break;
        }
        $id=explode('|',$id);
        $gr=@$_REQUEST['gr'];
        if(empty($gr)) $gr=2;

        $cc=new CC_Ctrl();
        if(!empty($id[1])){
            // есть в матрице
            $suffix_id=(int)@$id[1];
            $d=$cc->getOne("SELECT * FROM cc_suffix WHERE gr='$gr' AND id='$suffix_id'");
            if($d===0){
                $r->fres=false;
                $r->fres_msg="Запись с ID=$suffix_id не найдена в матрице";
                break;
            }
            $brand_id=$d['brand_id'];
            $cSuffix=Tools::unesc($d['cSuffix']);
        }else{
            // нет в матрице
            $cSuffix=Tools::esc($id[0]);
            $brand_id=0;
        }

        if($brand_id) $s="AND cc_brand.brand_id='$brand_id'"; else $s='';

        // ищем размеры или модели
        if($gr==2){
            $order="cc_brand.name,cc_model.name,cc_cat.P1,cc_cat.P3,cc_cat.P2";
            $r->title='Типоразмеры дисков в цвете '.$cSuffix;
        }else $order="cc_brand.name,cc_model.name,cc_cat.P1,cc_cat.P3,cc_cat.P2";

        $d=$cc->fetchAll("SELECT cc_cat.cat_id,cc_model.model_id,cc_cat.suffix AS csuffix,cc_model.suffix AS msuffix,cc_brand.name AS bname,cc_model.name AS mname,cc_cat.P1+'0' AS P1,cc_cat.P2+'0' AS P2,cc_cat.P3+'0' AS P3,cc_cat.P4+'0' AS P4,cc_cat.P5+'0' AS P5,cc_cat.P6+'0' AS P6,cc_cat.P7,cc_cat.cat_id,cc_model.P1 AS M1,cc_model.P2 AS M2,cc_model.P3 AS M3 FROM cc_cat INNER JOIN (cc_model INNER JOIN cc_brand ON cc_model.brand_id=cc_brand.brand_id) ON cc_cat.model_id=cc_model.model_id WHERE NOT cc_brand.LD AND NOT cc_model.LD AND NOT cc_cat.LD AND cc_cat.gr=$gr AND (cc_cat.suffix LIKE '$cSuffix' OR cc_model.suffix LIKE '$cSuffix') $s ORDER BY $order",MYSQLI_ASSOC);

        $r->tbl=array();
        $brand='';
        if(!empty($d)) {
            foreach($d as $v){
                if($gr==2){
                    $brand=Tools::unesc($v['bname']);
                    $r->tbl[Tools::unesc($v['bname'])][$v['model_id']][$v['cat_id']]=array(
                        'mname'=>Tools::unesc($v['mname']),
                        'msuffix'=>Tools::unesc($v['msuffix']),
                        'csuffix'=>Tools::unesc($v['csuffix']),
                        'tipo'=>"{$v['P2']}xJ{$v['P5']} {$v['P4']}/{$v['P6']} ET{$v['P1']} DIA {$v['P3']}"
                    );
                }elseif($gr==1){
                    //шины
                }
            }
        }
        if($brand_id && $brand!='') $r->title.=" внутри вренда $brand";
        break;


    case 'delete':
        $id=explode('|',@$_REQUEST['id']);
        $id=(int)@$id[1];
        if(empty($id)){
            $r->fres=false;
            $r->fres_msg='Идентификатор суффикса не передан';
            break;
        }

        $db=new DB();
        $db->query("DELETE FROM cc_suffix WHERE id='$id'");
        break;

    case 'mod':
        $r->textOutput=true;
        $r->prependFresMsg=true;
        $gr=@$_REQUEST['gr'];
        if(empty($gr)) $gr=2;

        $cc=new CC_Ctrl();
        $cc->loadSMatrix($gr,true);
        $suf=$cc->sMatrix[$gr];

        $key=@$_REQUEST['key'];
        $key=explode('##',$key);
        $id=@$key[1];
        if(empty($id)){
            $r->fres=false;
            $r->fres_msg='Значение базового суффикса не передано';
            break;
        }
        $tag=trim(Tools::esc(@$_REQUEST['tag']));
        $suffix1=trim(Tools::esc(@$_REQUEST['suffix1']));
        $suffix2=trim(Tools::esc(@$_REQUEST['suffix2']));
        $iSuffixes=trim(Tools::esc(@$_REQUEST['iSuffixes']));
        if($tag=='' && $suffix1=='' && $suffix2=='' && $iSuffixes==''){
            $r->fres=false;
            $r->fres_msg='Необходимо указать хотя бы одно значение помимо базового суффикса';
            break;
        }
        if($iSuffixes!='') $iSuffixes=' '.$iSuffixes;
        switch($key[0]){
            case 'csuf':
                // новая запись
                /*
                key:csuf##AMS/D
                suffix1:
                iSuffixes:
                suffix2:
                level:0
                parent:NULL
                isLeaf:true
                expanded:false
                oper:edit
                id:AMS/D
                */

                // проверка на случай редактирования сразу после добавления
                if(!isset($suf[$id][0])){
                    $id_=Tools::esc($id);
                    $dt=date("Y-m-d H:i:s");
                    $sql="INSERT INTO cc_suffix (gr,cSuffix,iSuffixes,suffix1,suffix2,dt_added,tag) VALUES('$gr','$id_','$iSuffixes','$suffix1','$suffix2','$dt','$tag')";
                    $cc->query($sql);
                    if($cc->lastId()){
                        $r->fres_msg='';
                        echo '0';
                    }else{
                        $r->fres=false;
                        $r->fres_msg='Произошла ошибка в процессе записи!';
                    }
                    break;
                }else {
                    $id=$suf[$id][0]['id'];
                }
            case 'id':
                /* обновление
                    для дитяти:
                    key:id##1
                    suffix1:черный блестючий
                    iSuffixes:BD1, bD2
                    suffix2:это очень красивый цвет
                    level:1
                    parent:BD
                    isLeaf:true
                    expanded:true
                    oper:edit
                    id:ADVAN

                    для парента:
                    key:id##2
                    suffix1:полированый серебристый
                    iSuffixes:HSP
                    suffix2:
                    level:0
                    parent:NULL
                    isLeaf:false
                    expanded:true
                    oper:edit
                    id:BD  */
                $id=(int)$id;
                if(empty($id)){
                    $r->fres=false;
                    $r->fres_msg='Идентификатор записи не передан';
                    break;
                }

                $sql="UPDATE cc_suffix SET iSuffixes='$iSuffixes',suffix1='$suffix1',suffix2='$suffix2', tag='$tag' WHERE id='$id'";
                $cc->query($sql);
                if($cc->updatedNum()){
                    $r->fres_msg='';
                    echo '0';
                }else{
                    $r->fres=false;
                    $r->fres_msg='Запись НЕ обновлена';
                }
                break;
            default:
                $r->fres=false;
                $r->fres_msg='Неверный префикс идентификатора записи';
        }

        break;

    case 'post':
        $gr=@$_REQUEST['gr'];
        if(empty($gr)) $gr=2;

        $cc=new CC_Ctrl();
        $cc->loadSMatrix($gr,true);
        $suf=$cc->sMatrix[$gr];
        $frm=@$_REQUEST['frm'];
        parse_str($frm,$frm);
        $cSuffix=trim(@$frm['cSuffix']);
        $tag=trim(@$frm['tag']);
        $suffix1=trim(@$frm['suffix1']);
        $suffix2=trim(@$frm['suffix2']);
        $iSuffixes=trim(@$frm['iSuffixes']);
        $brand_id=(int)@$frm['brand_id'];

        if($cSuffix==''){
            $r->fres=false;
            $r->fres_msg='Значение базового суффикса не задано';
            break;
        }
        if($suffix1=='' && $suffix2=='' && $iSuffixes==''){
            $r->fres=false;
            $r->fres_msg='Необходимо указать хотя бы одно значение помимо базового суффикса';
            break;
        }


        $ic=false;
        foreach($suf as $k=>$v)
            if(Tools::mb_strcasecmp($k,$cSuffix)===0){
                $r->__cSuffix=$cSuffix=$k; // делаем подмену введенного суффикса регистронезависимо
                $bids=array_keys($v);
                if(in_array($brand_id,$bids)){
                    $ic=true;
                    break 1;
                }
            }
        if($ic){
            $r->fres=false;
            $r->fres_msg='Дубль. Не записано';
            break;
        }

        $dt=date("Y-m-d H:i:s");
        $cSuffix=Tools::esc($cSuffix);
        $tag=Tools::esc($tag);
        $suffix1=Tools::esc($suffix1);
        $suffix2=Tools::esc($suffix2);
        $iSuffixes=Tools::esc($iSuffixes);
        if($iSuffixes!='') $iSuffixes=' '.$iSuffixes;

        $sql="INSERT INTO cc_suffix (gr,brand_id,cSuffix,iSuffixes,suffix1,suffix2,dt_added,tag) VALUES('$gr','$brand_id','$cSuffix','$iSuffixes','$suffix1','$suffix2','$dt','$tag')";
        $cc->query($sql);
        if($cc->lastId()){
            $r->fres_msg='Запись добавлена';
        }else{
            $r->fres=false;
            $r->fres_msg='Произошла ошибка в процессе добавления!';
        }

        break;

    case 'list':
        $r->textOutput=true;
        $gr=@$_REQUEST['gr'];
        if(empty($gr)) $gr=2;


        $cc=new CC_Ctrl();
        $cc->loadSMatrix($gr,true);
        $suf=$cc->sMatrix[$gr];

        $cc->que('brands',$gr);
        $brands=array();
        while($cc->next()!==false){
            $brands[$cc->qrow['brand_id']]=array(
                'name'=>Tools::unesc($cc->qrow['name'])
            );
        }

        $s='';
        if(!empty($_REQUEST['ext_cSuffix'])){ // поиск
            $cs=Tools::like($cs_=$_REQUEST['ext_cSuffix']);
            $s="AND cc_cat.suffix LIKE '%$cs%'";
            foreach($suf as $k=>$v){
                if(mb_stripos($k,$cs_)===false) {
                    $founded0=$founded2=$founded1=false;
                    if(!empty($v[0]['iSuffixes']))
                        foreach($v[0]['iSuffixes'] as $v1) if(mb_stripos($v1,$cs_)) {
                            $founded0=true;
                        }
                    if(mb_stripos(@$v[0]['tag'],$cs_)!==false) $founded0=true;
                    if(mb_stripos(@$v[0]['suffix1'],$cs_)!==false) $founded0=true;
                    if(mb_stripos(@$v[0]['suffix2'],$cs_)!==false) $founded0=true;
                    $bids=array_diff(array_keys($suf[$k]),array(0));
                    if(!empty($bids)){
                        foreach($bids as $brand_id){
                            $founded2=false;
                            if(!empty($v[$brand_id]['iSuffixes']))
                                foreach($v[$brand_id]['iSuffixes'] as $v1) if(mb_stripos($v1,$cs_)!==false) {
                                    $founded2=true;
                                }
                            if(mb_stripos(@$v[$brand_id]['tag'],$cs_)!==false) $founded2=true;
                            if(mb_stripos(@$v[$brand_id]['suffix1'],$cs_)!==false) $founded2=true;
                            if(mb_stripos(@$v[$brand_id]['suffix2'],$cs_)!==false) $founded2=true;
                            if(!$founded2) {
                                unset($suf[$k][$brand_id]);
                            }else $founded1=true;
                        }
                    }
                    if(!$founded0 && !$founded1) unset($suf[$k][0]);
                    if(!count($suf[$k])) unset($suf[$k]);
                }
            }
        }

        $d=$cc->fetchAll("SELECT cc_cat.suffix FROM cc_cat INNER JOIN (cc_model INNER JOIN cc_brand ON cc_model.brand_id=cc_brand.brand_id) ON cc_cat.model_id=cc_model.model_id WHERE NOT cc_brand.LD AND NOT cc_model.LD AND NOT cc_cat.LD AND cc_cat.gr=$gr AND cc_cat.suffix!='' $s GROUP BY cc_cat.suffix",MYSQLI_ASSOC);


        if(!empty($d)) {
            foreach($d as $v){
                $s=trim(Tools::unesc($v['suffix']));
                // если матрицы для суффикса нет, то добавляем его к матрице как пустышку
                // сверяем регистронезависимо
                $in=false;
                foreach($suf as $k=>$v) if(Tools::mb_strcasecmp($k,$s)===0) $in=true;
                if(!$in)
                    $suf[$s][0]=array(
                        'iSuffixes'=>NULL,
                        'tag'=>NULL,
                        'suffix1'=>NULL,
                        'suffix2'=>NULL
                    );
            }
            ksort($suf);
        }

        $iSuffs=array(); // все iSuffix`а
//print_r($suf);
        //  break;
        foreach($suf as $v){
            $isuf=@$v[0]['iSuffixes'];
            if(is_array($isuf))
                foreach($isuf as $vs){
                    if(!empty($vs)) $iSuffs[Tools::tolow($vs).'']=1;
                }

        }


        echo "<rows>";
        /*	echo '<page>1</page>';
            echo '<total>1</total>';
            echo '<records>1</records>';*/
        $kid=0;
        foreach($suf as $k=>$v){
            $bids=array_diff(array_keys($v),array(0));
            $kid++;
            echo "<row>";
            if(!empty($v[0]['id'])) {

                echo '<cell><![CDATA[<nobr><button class="ui-state-default ui-corner-all" onclick="mx.cat(\''.$k.'|'.$v[0]['id'].'\')"><span class="ui-icon ui-icon-newwin"></span></button><button class="ui-state-default ui-corner-all" onclick="mx.delete(\''.$k.'|'.$v[0]['id'].'\')"><span class="ui-icon ui-icon-trash"></span></button></nobr>]]></cell>';
                echo "<cell><![CDATA[id##{$v[0]['id']}]]></cell>"; // key
                echo "<cell><![CDATA[{$k}".(isset($iSuffs[Tools::tolow($k).''])?'   <b>(!)</b>':'')."<sub>|{$v[0]['id']}</sub>]]></cell>"; // cSuffix
                if($CIM==2){
                    echo "<cell><![CDATA[{$v[0]['tag']}]]></cell>"; // tag
                    echo "<cell><![CDATA[{$v[0]['suffix1']}]]></cell>"; // suffix1
                    echo "<cell><![CDATA[".implode(', ',$v[0]['iSuffixes'])."]]></cell>"; // iSuffix
                    echo "<cell><![CDATA[{$v[0]['suffix2']}]]></cell>"; // suffix2
                }elseif($CIM==3){
                    echo "<cell><![CDATA[".implode(', ',$v[0]['iSuffixes'])."]]></cell>"; // iSuffix
                    echo "<cell><![CDATA[{$v[0]['suffix1']}]]></cell>"; // suffix1
                }
                echo "<cell><![CDATA[".$v[0]['dt_added']."]]></cell>"; // дата

            }else{
                echo '<cell><![CDATA[<button class="ui-state-default ui-corner-all" onclick="mx.cat(\''.$k.'\')"><span class="ui-icon ui-icon-newwin"></span></button>]]></cell>'; // actions
                echo "<cell><![CDATA[csuf##{$k}]]></cell>"; // key
                echo "<cell><![CDATA[$k".(isset($iSuffs[Tools::tolow($k).''])?'   <b>(!)</b>':'')."]]></cell>"; // cSuffix
                if($CIM==2){
                    echo "<cell></cell>"; // tag
                    echo "<cell></cell>"; // suffix1
                    echo "<cell></cell>"; // iSuffix
                    echo "<cell></cell>"; // suffix2
                }elseif($CIM==3){
                    echo "<cell></cell>"; // iSuffix
                    echo "<cell></cell>"; // suffix1
                }
                echo "<cell></cell>"; // дата
            }
            echo "<cell>0</cell>"; // уровень
            echo "<cell><![CDATA[NULL]]></cell>";  // парент
            echo "<cell>".(count($bids)?'false':'true')."</cell>"; // последний листок дерева
            echo "<cell>".(count($bids)?'true':'false')."</cell>"; // expanded
            echo "<cell>true</cell>";
            echo "</row>";
            if(count($bids)) {
                $kid0=$kid;
                foreach($bids as $brand_id){
                    $kid++;
                    echo "<row>";
                    echo '<cell><![CDATA[<nobr><button class="ui-state-default ui-corner-all" onclick="mx.cat(\''.((!empty($brands[$brand_id])?$brands[$brand_id]['name']:'&lt;ne&gt;').'|'.$v[$brand_id]['id']).'\')"><span class="ui-icon ui-icon-newwin"></span></button><button class="ui-state-default ui-corner-all" onclick="mx.delete(\''.((!empty($brands[$brand_id])?$brands[$brand_id]['name']:'&lt;ne&gt;').'|'.$v[$brand_id]['id']).'\')"><span class="ui-icon ui-icon-trash"></span></button></nobr>]]></cell>'; // actions
                    echo "<cell><![CDATA[id##{$v[$brand_id]['id']}]]></cell>"; // key
                    echo "<cell><![CDATA[".(!empty($brands[$brand_id])?$brands[$brand_id]['name']:'&lt;ne&gt;')."<sub>|{$v[$brand_id]['id']}</sub>]]></cell>"; // brand_id
                    if($CIM==2){
                        echo "<cell><![CDATA[{$v[$brand_id]['tag']}]]></cell>"; // suffix1
                        echo "<cell><![CDATA[{$v[$brand_id]['suffix1']}]]></cell>"; // suffix1
                        echo "<cell><![CDATA[".implode(', ',$v[$brand_id]['iSuffixes'])."]]></cell>"; // iSuffix
                        echo "<cell><![CDATA[{$v[$brand_id]['suffix2']}]]></cell>"; // suffix2
                    }elseif($CIM==3){
                        echo "<cell><![CDATA[".implode(', ',$v[$brand_id]['iSuffixes'])."]]></cell>"; // iSuffix
                        echo "<cell><![CDATA[{$v[$brand_id]['suffix1']}]]></cell>"; // suffix1
                    }
                    echo "<cell><![CDATA[".$v[$brand_id]['dt_added']."]]></cell>"; // уровень
                    echo "<cell>1</cell>"; // уровень
                    echo "<cell><![CDATA[$k]]></cell>";  // парент == cSuffix
                    echo "<cell>true</cell>"; // последний листок дерева
                    echo "<cell>true</cell>";  // expanded
                    echo "<cell>false</cell>";
                    echo "</row>";
                }
            }
        }
        echo "</rows>";
        break;

    default: $r->fres=false; $r->fres_msg='BAD ACT_CASE '.$act;
}

ajxEnd();