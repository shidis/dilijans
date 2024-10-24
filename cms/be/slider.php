<?
include_once ('ajx_loader.php');

define('MAX_FILE_SIZE', 12400000);

$cp->setFN('datasets');
$cp->checkPermissions();

$r->fres=true;
$r->fres_msg='';

$act=Tools::esc(@$_REQUEST['act']);

$db = new DB();

switch ($act){
	case 'saveSlider':
		$uploader=new Uploader();
		$_POST['MAX_FILE_SIZE'] = MAX_FILE_SIZE;
		if($uploader->upload('imgFile', Uploader::$EXT_GRAPHICS)){
			$path = Cfg::get('root_path') . '/' . Cfg::get('cc_upload_dir') . '/slider/' . $uploader->originFN;
			Tools::tree_mkdir($path);
			if(@copy($uploader->sfile, $path)) {
				$link = Tools::esc(str_replace(Array('http://', 'https://', 'www.'), Array('', '', ''), $_POST['imgLink']));
				$db->insert('slider', Array('image' => $path, 'slide_link' => $link));
				@unlink($uploader->sfile);
			}
		}
		header('Location: '.$_SERVER['HTTP_REFERER']);
		exit(200);
		break;
	case 'editSlider':
		$slide_id = (int)$_POST['slide_id'];
		if (!empty($slide_id)) {
			if (!empty($_FILES['imgFile_'.$slide_id]['name'])) { // Сохранение файла
				$uploader = new Uploader();
				$_POST['MAX_FILE_SIZE'] = MAX_FILE_SIZE;
				if ($uploader->upload('imgFile_'.$slide_id, Uploader::$EXT_GRAPHICS)) {
					$path = Cfg::get('root_path') . '/' . Cfg::get('cc_upload_dir') . '/slider/' . $uploader->originFN;
					Tools::tree_mkdir($path);
					if (@copy($uploader->sfile, $path)) {
						$current_file = $db->getOne("SELECT `image` FROM slider WHERE slide_id = '$slide_id'", MYSQLI_ASSOC);
						$db->update('slider', Array('image' => $path), "slide_id='".$slide_id."'");
						@unlink($uploader->sfile);
						@unlink($current_file['image']);
					}
				}
			}
			elseif (!empty($_POST['delImg_'.$slide_id])) { // Удаление файла
				$current_file = $db->getOne("SELECT `image` FROM slider WHERE slide_id = '$slide_id'", MYSQLI_ASSOC);
				$db->del('slider', 'slide_id', $slide_id);
				@unlink($current_file['image']);
			}

			if (!empty($_POST['imgLink_'.$slide_id])) { // Изменение ссылки
				$link = Tools::esc(str_replace(Array('http://', 'https://', 'www.'), Array('', '', ''), $_POST['imgLink_'.$slide_id]));
				$db->update('slider', Array('slide_link' => $link), "slide_id='".$slide_id."'");
			}
		}
		header('Location: '.$_SERVER['HTTP_REFERER']);
		exit(200);
		break;
    default:
		header('Location: '.$_SERVER['HTTP_REFERER']);
		exit(200);
		break;
}

ajxEnd();