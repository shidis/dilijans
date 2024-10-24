<?php

include_once('ajx_loader.php');
require_once '../auth.php';
include_once('../struct.php');

$cp->setFN('podbor_pages');
$cp->checkPermissions();

//sleep(2);
$ab = new CC_AB();

$r->fres = true;
$r->fres_msg = '';

$gr = 1; //Tools::esc(@$_REQUEST['gr']);
$act = Tools::esc($_REQUEST['act']);
$vendor_id = intval(@$_REQUEST['vendor_id']);
$model_id = intval(@$_REQUEST['model_id']);
$year_id = intval(@$_REQUEST['year_id']);
$modif_id = intval(@$_REQUEST['avto_id']);

// Добавление / редактирование
switch ($act) {
  case 'edit':

    $ab->getTree(array('svendor' => $vendor_id,'smodel' => $model_id,'syear' => $year_id, 'smodif' => $modif_id), true);
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
        } else {
          $r->delImage = true;
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
      $image = $ab->getOne("SELECT avto_image FROM ab_avto WHERE avto_id='{$ab->tree['avto_id']}'", MYSQL_ASSOC);
      $cc = new CC_Ctrl();
      $r->image = $cc->make_img_path($image['avto_image']);
      // Загрузка картинок END
    } else {
      echo "Не верный avto_id!";
      exit();
    }
    break;

  case 'addNew':
    $ab->getTree(array('svendor' => (int)$vendor_id, 'smodel' => (int)$model_id, 'syear' => (int)$year_id, 'smodif' => (int)$modif_id), true);
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
      $image = $ab->getOne("SELECT avto_image FROM ab_avto WHERE avto_id='{$ab->tree['avto_id']}'", MYSQL_ASSOC);
      $page_info['avto_image'] = $image['avto_image'];
      echo $image['avto_image'];
      // Загрузка картинок END
    } else {
      $error = 'Ошибка записи картинки add_new.';
    }
    // Загрузка картинок END

    break;
}

ajxEnd();