<?
if (!defined('true_enter')) die ("Direct access not allowed!");

class Gallery extends DB {

	function __construct() {
		parent::__construct();
	}
	
	function topicAdd($f){
		$name=trim(Tools::esc($f['name']));
		$sname=trim($f['sname'])==''?Tools::str2iso($f['name']):Tools::str2iso($f['sname']);
		$info=trim(Tools::esc($f['info']));
		$param=Tools::DB_serialize(array('img1_resize_mode'=>Tools::esc($f['img1_resize_mode']),'img1_w'=>(int)$f['img1_w'],'img1_h'=>(int)$f['img1_h'],'img2_resize_mode'=>Tools::esc($f['img2_resize_mode']),'img2_w'=>(int)$f['img2_w'],'img2_h'=>(int)$f['img2_h']));
		if($sname==''){
			return $this->putMsg(false,'Не задано имя/системное имя');
		}
		$this->query("INSERT INTO gl_topic (name,sname,info,param) VALUES('$name','$sname','$info','$param')");
		return $this->lastId();
	}
	function topicEdit($f){
		$name=trim(Tools::esc($f['name']));
		$sname=trim($f['sname'])==''?Tools::str2iso($f['name']):Tools::str2iso($f['sname']);
		$info=trim(Tools::esc($f['info']));
		$topic_id=(int)$f['topic_id'];
		$param=Tools::DB_serialize(array('img1_resize_mode'=>Tools::esc($f['img1_resize_mode']),'img1_w'=>(int)$f['img1_w'],'img1_h'=>(int)$f['img1_h'],'img2_resize_mode'=>Tools::esc($f['img2_resize_mode']),'img2_w'=>(int)$f['img2_w'],'img2_h'=>(int)$f['img2_h']));
		if(!$topic_id) return $this->putMsg(false,'Не задан идентификатор галереи');
		if($sname==''){
			return $this->putMsg(false,'Не задано имя/системное имя');
		}
		$d=$this->getOne("SELECT * FROM gl_topic WHERE topic_id='$topic_id'");
		if($d===0)  return $this->putMsg(false,'Запись не найдена'); 
		if($d['sname']!=$sname && is_dir(Cfg::get('root_path').'/'.Cfg::get('GL_DIR').'/'.$d['sname'])){
			@chmod(Cfg::get('root_path').'/'.Cfg::get('GL_DIR').'/'.$d['sname'], 0777);
			if(!@rename(Cfg::get('root_path').'/'.Cfg::get('GL_DIR').'/'.$d['sname'],Cfg::get('root_path').'/'.Cfg::get('GL_DIR').'/'.$sname)) return $this->putMsg(false,'Не удалось переименовать директорию галлереи'); 
		}
		$this->query("UPDATE gl_topic SET name='$name', sname='$sname', info='$info', param='$param' WHERE topic_id='$topic_id'");
		if($this->unum()) return $this->putMsg(true,'Запись отредактирована'); 
			else return $this->putMsg(true,'Запись не изменена');
	}
	
	function topicDel($topic_id){
		$topic_id=(int)$topic_id;
		if(!$topic_id) return $this->putMsg(false,'Не задан идентификатор галереи');
		$d=$this->getOne("SELECT * FROM gl_topic WHERE topic_id='$topic_id'");
		if($d===0)  return $this->putMsg(false,'Запись не найдена'); 
		Tools::removeDir(Cfg::get('root_path').'/'.Cfg::get('GL_DIR').'/'.$d['sname']);
		if(is_dir(Cfg::get('root_path').'/'.Cfg::get('GL_DIR').'/'.$d['sname']))  return $this->putMsg(false,'Директория не полностью очищена'); 
		$this->query("DELETE FROM gl_image WHERE topic_id='$topic_id'");
		$this->query("DELETE FROM gl_topic WHERE topic_id='$topic_id'");
		if($this->unum()) return $this->putMsg(true,'Удалено'); 
			else return $this->putMsg(true,'Нет изменений');
	}
	
	function upload($file,$topic_id)
    {  // для работы необходим файл во временной папке сайта. файл в этой папке не удаляется по завершению
		if(!is_file($file)) return $this->putMsg(false,'Файл '.$file.' не найден');
		$topic_id=(int)$topic_id;
		if(!$topic_id) return $this->putMsg(false,'Не задан ID галереи');
		$typesArray = explode(' ', Uploader::$EXT_GRAPHICS);
		$fileParts  = pathinfo($file);
		if (!in_array($fileParts['extension'],$typesArray))  return $this->putMsg(false,'Запрещенный тип файла');
		$source=$fileParts['dirname'].'/'.$fileParts['basename'];
		
		$d=$this->getOne("SELECT * FROM gl_topic WHERE topic_id='$topic_id'");
		if($d===0) return $this->putMsg(false,'Не найден ID галереи');
		$param=Tools::DB_unserialize($d['param']);
		$this->query("INSERT INTO gl_image (topic_id) VALUES('$topic_id')");
		$image_id=$this->lastId();
		$bn=$image_id.'.'.$fileParts['extension'];
		
		GD::$msgNoOutput=true;
		
		$img1=Cfg::get('root_path').'/'.Cfg::get('GL_DIR').'/'.$d['sname'].'/1/';
		Tools::tree_mkdir($img1);
		if(@copy($source,$img1.$bn)) {
			if(GD::resize($param['img1_resize_mode'],$img1.$bn,$param['img1_w'],$param['img1_h'])!==false){
				$img2=Cfg::get('root_path').'/'.Cfg::get('GL_DIR').'/'.$d['sname'].'/2/';
				Tools::tree_mkdir($img2);
				if(@copy($source,$img2.$bn)) {
					if(GD::resize($param['img2_resize_mode'],$img2.$bn,$param['img2_w'],$param['img2_h'])!==false){
						$thumb=Cfg::get('root_path').'/'.Cfg::get('GL_DIR').'/'.$d['sname'].'/thumb/';
						Tools::tree_mkdir($thumb);
						if(@copy($source,$thumb.$bn)) {
							if(GD::resize('BH',$thumb.$bn,150,150)===false) $this->putMsg(false,GD::$fres_msg);
						} else $this->putMsg(false,'Не удалось скопировать '.$source.' -> '.$thumb.$bn);
					} else  $this->putMsg(false,GD::$fres_msg);
				} else $this->putMsg(false,'Не удалось скопировать '.$source.' -> '.$img2.$bn);
			} else  $this->putMsg(false,GD::$fres_msg);
		} else $this->putMsg(false,'Не удалось скопировать '.$source.' -> '.$img1.$bn);

		if(!$this->fres){
			@unlink($img1.$bn);
			@unlink($img2.$bn);
			@unlink($thumb.$bn);
			$this->query("DELETE FROM gl_image WHERE image_id='$image_id'");
			return false;
		}else{
			$thumb=$bn;
			$img1=$bn;
			$img2=$bn;
			$this->query("UPDATE gl_image SET thumb='$thumb', img1='$img1', img2='$img2' WHERE image_id='$image_id'");
			return true;
		}
	}
	
	function imageList($topic_id,$order='image_id'){
		$topic_id=(int)$topic_id;
		if(!$topic_id) return $this->putMsg(false,'Не задан идентификатор галереи');
		$d=$this->getOne("SELECT * FROM gl_topic WHERE topic_id='$topic_id'");
		if($d===0)  return $this->putMsg(false,'Запись не найдена'); 
		$dd=$this->fetchAll("SELECT * FROM gl_image WHERE topic_id='$topic_id' ORDER BY $order",MYSQLI_ASSOC);
		$im=array();
		$p='/'.Cfg::get('GL_DIR').'/'.$d['sname'].'/';
		foreach($dd as $v){
			$im[$v['image_id']]=array(
				'thumb'=>$p.'thumb/'.$v['thumb'],
				'img1'=>$p.'1/'.$v['img1'],
				'img2'=>$p.'2/'.$v['img2'],
				'title'=>Tools::unesc($v['title']),
				'alt'=>Tools::unesc($v['alt']),
				'info1'=>Tools::unesc($v['info1']),
				'info2'=>Tools::unesc($v['info2']),
				'link'=>Tools::unesc($v['link'])
			);
		}
		return $im;
	}
	
	function imageDel($image_id){
		$image_id=(int)$image_id;
		if(!$image_id) return $this->putMsg(false,'Не задан идентификатор изображения');
		$d=$this->getOne("SELECT gl_image.*, gl_topic.sname FROM gl_image INNER JOIN gl_topic ON gl_image.topic_id=gl_topic.topic_id WHERE gl_image.image_id='$image_id'");
		if($d===0)  return $this->putMsg(false,'Запись не найдена'); 
		$p=Cfg::get('root_path').'/'.Cfg::get('GL_DIR').'/'.$d['sname'].'/';
		@unlink($p.'1/'.$d['img1']);
		@unlink($p.'2/'.$d['img1']);
		@unlink($p.'thumb/'.$d['thumb']);
		$this->query("DELETE FROM gl_image WHERE image_id='$image_id'");
		return true;
	}

}
?>