<?
include_once ('ajx_loader.php');
require_once '../auth.php';
include_once ('../struct.php');

$cp->setFN('podbor_pages');
$cp->checkPermissions();

//sleep(2);
$ab=new CC_AB();

$r->fres=true;
$r->fres_msg='';

$gr = Tools::esc(@$_REQUEST['gr']);
$act=Tools::esc($_REQUEST['act']);
$vendor_id = intval(@$_REQUEST['vendor_id']);
$model_id = intval(@$_REQUEST['model_id']);
$year_id = intval(@$_REQUEST['year_id']);
$modif_id = intval(@$_REQUEST['modif_id']);

// Добавление / редактирование
switch ($act){
    case 'edit':
        $page_id = intval(@$_POST['page_id']);
        if (!empty($_POST['ae_post']) && !empty($page_id))
        {
            $page_id   = intval(@$_POST['page_id']);
            $gr        = intval(@$_POST['gr']);
            $seo_h1    = @$_POST['seo_h1'];
            $seo_h2    = @$_POST['seo_h2'];
            $seo_title = @$_POST['seo_title'];
            $seo_desc  = @$_POST['seo_desc'];
            $seo_key   = @$_POST['seo_key'];
            $text_up   = @Tools::untaria($_POST['text1']);
            $text_dw   = @Tools::untaria($_POST['text2']);
            $is_seo    = (int)@$_POST['is_seo'];
            $show_rating   = (int)@$_POST['show_rating'];
            $showOnTheMain = (int)@$_POST['showOnTheMain'];
            $sortOnTheMain = (int)@$_POST['sortOnTheMain'];
            //
            if (!$ab->query("UPDATE ab_podbor_meta SET gr='$gr', text1='$text_up', text2='$text_dw', dt_upd='".Tools::dt()."',
        seo_h1='$seo_h1', seo_h2='$seo_h2', seo_title='$seo_title', seo_desc='$seo_desc', seo_key='$seo_key', is_seo='$is_seo'
        WHERE podbor_id='$page_id'"))
            {
                $error = 'Ошибка записи.';
            }
            if ($page_id) {
                $page_info = $ab->getOne("SELECT * FROM ab_podbor_meta WHERE podbor_id='$page_id'", MYSQLI_ASSOC);
                $page_url_detail = '';
                $ab->getTree(array('svendor' => (int)$page_info['vendor_id'],'smodel' => (int)$page_info['model_id'],'syear' => (int)$page_info['year_id'], 'smodif' => (int)$page_info['modif_id']), true);
                if (!empty($ab->tree['vendor_sname'])) {
                    $page_url_detail .= $ab->tree['vendor_sname'];
                }
                if (!empty($ab->tree['model_sname'])) {
                    $page_url_detail .= '--' . $ab->tree['model_sname'];
                }
                if (!empty($ab->tree['year_sname'])) {
                    $page_url_detail .= '--' . $ab->tree['year_sname'];
                }
                if (!empty($ab->tree['modif_sname'])) {
                    $page_url_detail .= '--' . $ab->tree['modif_sname'];
                }
                if (!empty($page_url_detail)) {
                    $page_url = 'http://'.$_SERVER['HTTP_HOST'].'/' . (($gr == 1) ? App_Route::_getUrl('avtoPodborShin') : App_Route::_getUrl('avtoPodborDiskov')) . '/' . $page_url_detail . '.html';
                }
                // Загрузка картинок
                if (!empty($ab->tree['avto_id'])) {
                    $res = true;
                    $cc = new CC_Ctrl;
                    if (!empty($_FILES['imgFile']['tmp_name'])) {
                        $uploader = new Uploader();
                        if (!$uploader->upload('imgFile', Uploader::$EXT_GRAPHICS)) {
                            $res = false;
                            $error = $uploader->strMsg();
                        }
                    } elseif (!empty($_POST['spyUrl'])) {
                        $uploader = new Uploader();
                        if (!$uploader->spyUrl($_POST['spyUrl'], Uploader::$EXT_GRAPHICS)) {
                            $res = false;
                            $error = $uploader->strMsg();
                        }
                    } elseif (!empty($_POST['delImg'])) {
                        if (!$cc->imgDelete('ab_avto', 'avto_id', $ab->tree['avto_id'], 'avto_image')) {
                            $res = false;
                        }
                    }

                    if ($res && isset($uploader)) {
                        if (!$cc->imgUpload('ab_avto', $ab->tree['avto_id'], $gr, 1, $uploader->sfile, 'avto_image')) {
                            $res = false;
                        }
                    }

                    if (!$res) $error = 'Ошибка при добавлении картинки!';

                    if (isset($uploader)) {
                        $uploader->del();
                        unset($uploader);
                    }
                    //
                    if (!$ab->query("UPDATE ab_avto SET show_rating='$show_rating' WHERE avto_id='{$ab->tree['avto_id']}'")) {
                        $error = 'Ошибка записи ab_avto.';
                    }
                    //
                    if (!$ab->query("UPDATE ab_avto SET showOnTheMain='$showOnTheMain' WHERE avto_id='{$ab->tree['avto_id']}'")) {
                        $error = 'Ошибка записи ab_avto.';
                    }
                    //
                    if (!$ab->query("UPDATE ab_avto SET sortOnTheMain='$sortOnTheMain' WHERE avto_id='{$ab->tree['avto_id']}'")) {
                        $error = 'Ошибка записи ab_avto.';
                    }
                    $image = $ab->getOne("SELECT avto_image, show_rating, showOnTheMain, sortOnTheMain FROM ab_avto WHERE avto_id='{$ab->tree['avto_id']}'", MYSQLI_ASSOC);
                    $page_info['avto_image']  = $image['avto_image'];
                    $page_info['show_rating'] = $image['show_rating'];
                    $page_info['showOnTheMain'] = $image['showOnTheMain'];
                    $page_info['sortOnTheMain'] = $image['sortOnTheMain'];
                    // Загрузка картинок END
                }
                else {
                    $error = 'Ошибка записи ab_avto (выбор авто).';
                }
                cp_head();
                cp_css();
                cp_js();
                cp_body();
                cp_title();
                include('../frm/podbor_pages_post.php');
            }
            else {
                echo "Не верный page_id!";
            }
            cp_end();
            exit();
        }
        else {
            $page_id = intval(@$_POST['page_id']);
            if ($page_id) {
                $page_info = $ab->getOne("SELECT * FROM ab_podbor_meta WHERE podbor_id='$page_id'", MYSQLI_ASSOC);
                $page_url_detail = '';
                $ab->getTree(array('svendor' => (int)$page_info['vendor_id'],'smodel' => (int)$page_info['model_id'],'syear' => (int)$page_info['year_id'], 'smodif' => (int)$page_info['modif_id']), true);
                if (!empty($ab->tree['vendor_sname'])) {
                    $page_url_detail .= $ab->tree['vendor_sname'];
                }
                if (!empty($ab->tree['model_sname'])) {
                    $page_url_detail .= '--' . $ab->tree['model_sname'];
                }
                if (!empty($ab->tree['year_sname'])) {
                    $page_url_detail .= '--' . $ab->tree['year_sname'];
                }
                if (!empty($ab->tree['modif_sname'])) {
                    $page_url_detail .= '--' . $ab->tree['modif_sname'];
                }
                if (!empty($page_url_detail)) {
                    $page_url = 'http://'.$_SERVER['HTTP_HOST'].'/' . (($gr == 1) ? App_Route::_getUrl('avtoPodborShin') : App_Route::_getUrl('avtoPodborDiskov')) . '/' . $page_url_detail . '.html';
                }
                //
                $image = $ab->getOne("SELECT avto_image, show_rating, showOnTheMain, sortOnTheMain FROM ab_avto WHERE avto_id='{$ab->tree['avto_id']}'", MYSQLI_ASSOC);
                $page_info['avto_image']  = $image['avto_image'];
                $page_info['show_rating'] = $image['show_rating'];
                $page_info['showOnTheMain'] = $image['showOnTheMain'];
                $page_info['sortOnTheMain'] = $image['sortOnTheMain'];
                //
                cp_head();
                cp_css();
                cp_js();
                cp_body();
                cp_title();
                include('../frm/podbor_pages_post.php');
                cp_end();
                exit();
            }
            else {
                echo "Не верный page_id!";
                exit();
            }
        }
        break;
    case 'addNew':
        if (!empty($_POST['ae_post']))
        {
            $gr        = intval(@$_POST['gr']);
            $seo_h1    = @$_POST['seo_h1'];
            $seo_h2    = @$_POST['seo_h2'];
            $seo_title = @$_POST['seo_title'];
            $seo_desc  = @$_POST['seo_desc'];
            $seo_key   = @$_POST['seo_key'];
            $text_up   = @Tools::untaria($_POST['text1']);
            $text_dw   = @Tools::untaria($_POST['text2']);
            $is_seo    = (int)@$_POST['is_seo'];
            $show_rating   = (int)@$_POST['show_rating'];
            $showOnTheMain = (int)@$_POST['showOnTheMain'];
            $sortOnTheMain = (int)@$_POST['sortOnTheMain'];
            $query_r = $ab->query("INSERT INTO ab_podbor_meta VALUES (NULL, '$gr', '$text_up', '$text_dw', '0', '0', '".Tools::dt()."', '".Tools::dt()."',
        '$vendor_id', '$year_id', '$model_id', '$modif_id',
        '$seo_title', '$seo_desc', '$seo_key', '$is_seo', '', '$seo_h1', '$seo_h2');");
            if (!$query_r)
            {
                $error = 'Ошибка записи.';
            }
            else{
                $ab->getTree(array('svendor' => (int)$vendor_id,'smodel' => (int)$model_id,'syear' => (int)$year_id, 'smodif' => (int)$modif_id), true);
                if (!empty($ab->tree['avto_id'])) {
                    $res = true;
                    $cc = new CC_Ctrl;
                    if (!empty($_FILES['imgFile']['tmp_name'])) {
                        $uploader = new Uploader();
                        if (!$uploader->upload('imgFile', Uploader::$EXT_GRAPHICS)) {
                            $res = false;
                            $error = $uploader->strMsg();
                        }
                    } elseif (!empty($_POST['spyUrl'])) {
                        $uploader = new Uploader();
                        if (!$uploader->spyUrl($_POST['spyUrl'], Uploader::$EXT_GRAPHICS)) {
                            $res = false;
                            $error = $uploader->strMsg();
                        }
                    } elseif (!empty($_POST['delImg'])) {
                        if (!$cc->imgDelete('ab_avto', 'avto_id', $ab->tree['avto_id'], 'avto_image')) {
                            $res = false;
                        }
                    }

                    if ($res && isset($uploader)) {
                        if (!$cc->imgUpload('ab_avto', $ab->tree['avto_id'], $gr, 1, $uploader->sfile, 'avto_image')) {
                            $res = false;
                        }
                    }

                    if (!$res) $error = 'Ошибка при добавлении картинки!';

                    if (isset($uploader)) {
                        $uploader->del();
                        unset($uploader);
                    }
                    //
                    if (!$ab->query("UPDATE ab_avto SET show_rating='$show_rating' WHERE avto_id='{$ab->tree['avto_id']}'")) {
                        $error = 'Ошибка записи ab_avto.';
                    }
                    //
                    if (!$ab->query("UPDATE ab_avto SET showOnTheMain='$showOnTheMain' WHERE avto_id='{$ab->tree['avto_id']}'")) {
                        $error = 'Ошибка записи ab_avto.';
                    }
                    //
                    if (!$ab->query("UPDATE ab_avto SET sortOnTheMain='$sortOnTheMain' WHERE avto_id='{$ab->tree['avto_id']}'")) {
                        $error = 'Ошибка записи ab_avto.';
                    }
                    $image = $ab->getOne("SELECT avto_image, show_rating, showOnTheMain, sortOnTheMain FROM ab_avto WHERE avto_id='{$ab->tree['avto_id']}'", MYSQLI_ASSOC);
                    $page_info['avto_image']  = $image['avto_image'];
                    $page_info['show_rating'] = $image['show_rating'];
                    $page_info['showOnTheMain'] = $image['showOnTheMain'];
                    $page_info['sortOnTheMain'] = $image['sortOnTheMain'];
                    // Загрузка картинок END
                }
                else $error = 'Ошибка записи картинки add_new.';
                // Загрузка картинок END
            }
            include('../frm/podbor_pages.php');
            cp_end();
            exit();
        }
        else {
            $page = $ab->getOne("SELECT * FROM `ab_podbor_meta` WHERE LD = 0 AND gr='".$gr."'
                                ".(!empty($vendor_id) ? " AND vendor_id='".$vendor_id."'" : '')."
                                ".(!empty($model_id) ? " AND model_id='".$model_id."'" : ' AND model_id=0')."
                                ".(!empty($year_id) ? " AND year_id='".$year_id."'" : ' AND year_id=0')."
                                ".(!empty($modif_id) ? " AND modif_id='".$modif_id."'" : ' AND modif_id=0')
                ,MYSQLI_ASSOC);
            if (!empty($vendor_id) && empty($page)) {
                $page_url_detail = '';
                $ab->getTree(array('svendor' => (int)$vendor_id,'smodel' => (int)$model_id,'syear' => (int)$year_id, 'smodif' => (int)$modif_id), true);
                if (!empty($ab->tree['vendor_sname'])) {
                    $page_url_detail .= $ab->tree['vendor_sname'];
                }
                if (!empty($ab->tree['model_sname'])) {
                    $page_url_detail .= '--' . $ab->tree['model_sname'];
                }
                if (!empty($ab->tree['year_sname'])) {
                    $page_url_detail .= '--' . $ab->tree['year_sname'];
                }
                if (!empty($ab->tree['modif_sname'])) {
                    $page_url_detail .= '--' . $ab->tree['modif_sname'];
                }
                if (!empty($page_url_detail)) {
                    $page_url = 'http://'.$_SERVER['HTTP_HOST'].'/' . (($gr == 1) ? App_Route::_getUrl('avtoPodborShin') : App_Route::_getUrl('avtoPodborDiskov')) . '/' . $page_url_detail . '.html';
                }
                //
                cp_head();
                cp_css();
                cp_js();
                cp_body();
                cp_title();
                include('../frm/podbor_pages_post.php');
                cp_end();
                exit();
            }
            else
            {
                $error ='Не выбраны параметры или такая страница уже существует.';
                include('../frm/podbor_pages.php');
                cp_end();
                exit();
            }
        }
        break;
}
// Остальные действия (AJAX)
switch ($act){
    case 'hSwitch':
        $page_id = intval(@$_REQUEST['page_id']);
        if ($page_id) {
            $page_info = $ab->getOne("SELECT * FROM ab_podbor_meta WHERE podbor_id='$page_id'",MYSQLI_ASSOC);
            if ($ab->query("UPDATE ab_podbor_meta SET H=".(($page_info['H'] == 1) ? '0' : '1')." WHERE podbor_id='$page_id'")) {
                $r->fres = true;
                $r->fres_msg = 'Обновили.';
                $r->v="<a href=\"#\" class=\"h-sw\">";
                if($page_info['H'] == 0) $r->v.='отобразить'; else $r->v.='скрыть';
                $r->v.='</a>';
            } else {
                $r->fres = false;
                $r->fres_msg = '['.$page_id.'] При удалении страницы возникла ошибка!';
            }
        }
        else {
            $r->fres = false;
            $r->fres_msg = 'Не верный page_id!';
        }
        break;
    case 'del':
        $page_id = intval(@$_REQUEST['page_id']);
        if ($page_id) {
            if ($ab->query("UPDATE ab_podbor_meta SET LD=1 WHERE podbor_id='$page_id'")) {
                $r->fres = true;
                $r->fres_msg = 'Убрали.';
            } else {
                $r->fres = false;
                $r->fres_msg = '['.$page_id.'] При удалении страницы возникла ошибка!';
            }
        }
        else {
            $r->fres = false;
            $r->fres_msg = 'Не верный page_id!';
        }
        break;
    case 'vendors':
        $ab->query('SELECT * FROM ab_avto WHERE (vendor_id=0)AND(NOT H) ORDER BY name');
        $r->data=
            <<<html
            <select id="vendor" name="vendor_id" e="марку авто" >
<option value="0">Выбираем марку авто</option>
html;
        while($ab->next()!==false){
            $r->data.=
                <<<html
                <option value="{$ab->qrow['avto_id']}"
html;
            if($ab->qrow['avto_id']==$id) $r->data.=" selected";
            $ab->qrow['name']=Tools::unesc($ab->qrow['name']);
            $r->data.=
                <<<html
                >{$ab->qrow['name']}</option>
html;
        }
        break;

    case 'models':
        $ab->query("SELECT * FROM ab_avto WHERE (vendor_id='{$vendor_id}')AND(model_id=0)AND(NOT H) ORDER BY name");
        $r->data=
            <<<html
            <select id="model" name="model_id" e="модель" >
<option value="0">Модель авто</option>
html;
        while($ab->next()!==false){
            $r->data.=
                <<<html
                <option value="{$ab->qrow['avto_id']}"
html;
            if($ab->qrow['avto_id']==$vendor_id) $r->data.=" selected";
            $ab->qrow['name']=Tools::unesc($ab->qrow['name']);
            $r->data.=
                <<<html
                >{$ab->qrow['name']}</option>
html;
        }
        break;

    case 'years':
        $ab->query("SELECT * FROM ab_avto WHERE (model_id='{$model_id}')AND(year_id=0)AND(NOT H) ORDER BY name");
        $r->data=
            <<<html
            <select id="year" name="year_id" e="год выпуска" >
<option value="0">Выбираем год</option>
html;
        while($ab->next()!==false){
            $r->data.=
                <<<html
                <option value="{$ab->qrow['avto_id']}"
html;
            if($ab->qrow['avto_id']==$model_id) $r->data.=" selected";
            $ab->qrow['name']=Tools::unesc($ab->qrow['name']);
            $r->data.=
                <<<html
                >{$ab->qrow['name']}</option>
html;
        }
        break;

    case 'modifs':
        $ab->query("SELECT * FROM ab_avto WHERE (year_id='{$year_id}')AND(NOT H) ORDER BY name");
        $r->data=
            <<<html
            <select id="modif" name="modif_id" e="модификацию" >
<option value="0">Модификация</option>
html;
        while($ab->next()!==false){
            $r->data.=
                <<<html
                <option value="{$ab->qrow['avto_id']}"
html;
            if($ab->qrow['avto_id']==$year_id) $r->data.=" selected";
            $ab->qrow['name']=Tools::unesc($ab->qrow['name']);
            $r->data.=
                <<<html
                >{$ab->qrow['name']}</option>
html;
        }
        break;
// *******************************************************
    case 'getData':
        if (!empty($vendor_id)) {
            $pages = $ab->fetchAll("SELECT * FROM `ab_podbor_meta` WHERE LD = 0 AND gr='" . $gr . "'
	" . (!empty($vendor_id) ? " AND vendor_id='" . $vendor_id . "'" : '') . "
	" . (!empty($model_id) ? " AND model_id='" . $model_id . "'" : '') . "
	" . (!empty($year_id) ? " AND year_id='" . $year_id . "'" : '') . "
	" . (!empty($modif_id) ? " AND modif_id='" . $modif_id . "'" : '')
                . " ORDER BY vendor_id, model_id, year_id, modif_id", MYSQLI_ASSOC);
            if (!empty($pages)) {
                $r->data =
                    <<<html
                    <table class="ui-table tablesorter" style="margin: 25px 0px;">
        <thead>
          <tr>
            <th class="ui-widget-header"><input type="checkbox" onclick="SelectAll(checked,'form_pp')" class="ui-corner-all"></th>
            <th scope="col" class="header ui-widget-header">ID</th>
            <th scope="col" class="header ui-widget-header">Верхний текст</th>
            <th scope="col" class="header ui-widget-header">Нижний текст</th>
            <th scope="col" class="header ui-widget-header">Псевдоним</th>
            <th scope="col" class="header ui-widget-header">SEO Title</th>
            <th scope="col" class="header ui-widget-header">SEO Description</th>
            <th scope="col" class="header ui-widget-header">SEO Keywords</th>
            <th scope="col" class="header ui-widget-header">SEO оптимизация</th>
            <th scope="col" class="header ui-widget-header">Скрыть</th>
            <th scope="col" class="ui-widget-header">Удалить</th>
          </tr>
        </thead>
        <tbody>
html;
                foreach ($pages as $page) {
                    $page_url_detail = '';
                    if (!empty($page['vendor_id'])) {
                        $vendor = $ab->getOne("SELECT * FROM ab_avto WHERE avto_id = '{$page['vendor_id']}'", MYSQLI_ASSOC);
                        $page_url_detail .= $vendor['sname'];
                    }
                    if (!empty($page['model_id'])) {
                        $model = $ab->getOne("SELECT * FROM ab_avto WHERE avto_id = '{$page['model_id']}'", MYSQLI_ASSOC);
                        $page_url_detail .= '--' . $model['sname'];
                    }
                    if (!empty($page['year_id'])) {
                        $year = $ab->getOne("SELECT * FROM ab_avto WHERE avto_id = '{$page['year_id']}'", MYSQLI_ASSOC);
                        $page_url_detail .= '--' . $year['sname'];
                    }
                    if (!empty($page['modif_id'])) {
                        $modif = $ab->getOne("SELECT * FROM ab_avto WHERE avto_id = '{$page['modif_id']}'", MYSQLI_ASSOC);
                        $page_url_detail .= '--' . $modif['sname'];
                    }
                    $ab->getTree(array('svendor' => @$vendor['sname'], 'smodel' => @$model['sname'], 'syear' => @$year['sname'], 'smodif' => @$modif['sname']));
                    if (!empty($page_url_detail)) {
                        $page_url = 'http://' . $_SERVER['HTTP_HOST'] . '/' . (($gr == 1) ? App_Route::_getUrl('avtoPodborShin') : App_Route::_getUrl('avtoPodborDiskov')) . '/' . $page_url_detail . '.html';
                    }
                    $r->data .= '
				<tr id="' . $page['podbor_id'] . '">
					<td class="ui-table-td-even"><input class="cc" type="checkbox" name="c_' . $page['podbor_id'] . '" value="1" class="ui-corner-all"></td>
					<td align="center" class="ui-table-td-even">' . $page['podbor_id'] . '</td>
					<td align="center" class="ui-table-td-even">' . (!empty($page['text1']) ? '<img src="../img/mods.gif" border="0">' : '') . '</td>
					<td align="center" class="ui-table-td-even">' . (!empty($page['text2']) ? '<img src="../img/mods.gif" border="0">' : '') . '</td>
					<td class="ui-table-td-even"><a href="javascript:;" onclick="edit(this);return false;">' . $page_url . '</a></td>
					<td class="ui-table-td-even">' . $page['seo_title'] . '</a></td>
					<td class="ui-table-td-even">' . $page['seo_desc'] . '</a></td>
					<td class="ui-table-td-even">' . $page['seo_key'] . '</a></td>
					<td class="ui-table-td-even" align="center">' . ($page['is_seo'] ? '<img src="../img/checked.gif" border="0">' : '<img src="../img/delete.gif" border="0">') . '</a></td>
					<td nowrap="" align="center" class="ui-table-td-even"><span class="hide">0</span><a href="#" class="h-sw">' . ($page['H'] == 1 ? 'отобразить' : 'скрыть') . '</a></td>
					<td nowrap="" align="center" class="ui-table-td-even delete"><a href="javascript:;" onclick="del(this);return false;"><img src="../img/b_drop.png" border="0"></a></td>
				</tr>
			';
                }
                $r->data .=
                    <<<html
                    </tbody>
</table>
html;
            } else $r->data = '<p>Нет данных...</p>';
        }
        else $r->data = '<p>Выберите бренд...</p>';
        break;
// *******************************************************
    default: echo 'BAD ACT ID '.$act;
}

ajxEnd();