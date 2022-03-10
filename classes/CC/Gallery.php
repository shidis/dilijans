<?
if (!defined('true_enter')) die ("Direct access not allowed!");

class CC_Gallery extends DB
{
    public $img_path;
    private $toJPG=true;
    
    function __construct()
    {
        parent::__construct();
    }

    function que($qname, $cond1 = '', $cond2 = '', $cond3 = '', $cond4 = '')
    {
        switch ($qname) {
            case 'gal_list':
                $cond1 = intval($cond1); // model_id
                $res = $this->query("SELECT * FROM cc_gal WHERE (model_id='$cond1') ORDER BY pos");
                break;
            default:
                echo 'GAL BAD CASE ' . $qname;
                $res = false;
        }
        return ($res);
    }

    function http_upath($file, $serv = false, $replace_protocol = true)
    {
        if (defined('FROM_CMS'))
            if ($serv) $CC_GAL_UPLOAD_DIR = Cfg::_get('root_path') . '/' . Cfg::get('cc_gal_upload_dir'); else $CC_GAL_UPLOAD_DIR = Cfg::get('cc_gal_upload_dir');
        else
            if ($serv) $CC_GAL_UPLOAD_DIR = Cfg::_get('root_path') . '/' . Cfg::get('cc_gal_cache_images_dir'); else $CC_GAL_UPLOAD_DIR = Cfg::get('cc_gal_images_dir');

        if (mb_strpos($CC_GAL_UPLOAD_DIR, 'http') === false)
            if ($serv) return $CC_GAL_UPLOAD_DIR . '/' . $file;
            else {
                if ($replace_protocol) {
                    return '//' . $_SERVER['HTTP_HOST'] . '/' . $CC_GAL_UPLOAD_DIR . '/' . $file;
                } else {
                    return 'http://' . $_SERVER['HTTP_HOST'] . '/' . $CC_GAL_UPLOAD_DIR . '/' . $file;
                }

            }
        else return $CC_GAL_UPLOAD_DIR . '/' . $file;
    }

    function make_img_path($img, $serv = false, $replace_protocol=true)
    {
        if (is_numeric($img)) $img = @$this->qrow['img' . $img];
        if (trim($img) != '') $this->img_path = $this->http_upath($img, $serv, $replace_protocol); else $this->img_path = '';
        return ($this->img_path);
    }

    function get_img_path($id, $field, $serv = false)
    {
        $c = new CC_Base;
        $c->query("SELECT $field FROM cc_gal WHERE gal_id='$id'");
        if ($c->qnum()) {
            $c->next();
            if ($c->qrow[$field] != '') $this->img_path = $this->http_upath($c->qrow[$field], $serv); else $this->img_path = '';
        } else $this->img_path = '';
        unset($c);
        return ($this->img_path);
    }

    /*
     * fname0 - временный файл
     * value - gal_id
     */
    function resizeImages($fname0,$value)
    {
        GD::$msgNoOutput=true;
        $rm = @unserialize(Data::get('gal_img_param'));
        $img1 = $img2 = $img3 = '';

        $fname = '1/' . $value . '.' . pathinfo($fname0, PATHINFO_EXTENSION);
        Tools::tree_mkdir(Cfg::_get('root_path') . '/' . Cfg::get('cc_gal_upload_dir') . '/1/');
        if (!copy($fname0, $fn = Cfg::_get('root_path') . '/' . Cfg::get('cc_gal_upload_dir') . '/' . $fname))
            return Msg::put(false, "[CC_Gallery.resizeImages]: Ошибка копированиях [1]");
        else {
            $newf = GD::resize(@$rm['img1_resize_mode'], $fn, @$rm['img1_resize_w'], @$rm['img1_resize_h'], $this->toJPG?'image/jpeg':'');
            if ($newf !== false) {
                if ($fn != $newf) @unlink($fn);
                $img1 = '1/'.pathinfo($newf, PATHINFO_BASENAME);
            } else {
                @unlink($fn);
                return Msg::put(false, '[CC_Gallery.resizeImages]: Не могу изменить размер изображения 1');
            }
        }

        $fname = '2/' . $value . '.' . pathinfo($fname0, PATHINFO_EXTENSION);
        Tools::tree_mkdir(Cfg::_get('root_path') . '/' . Cfg::get('cc_gal_upload_dir') . '/2/');
        if (!copy($fname0, $fn = Cfg::_get('root_path') . '/' . Cfg::get('cc_gal_upload_dir') . '/' . $fname))
            return Msg::put(false, "[CC_Gallery.resizeImages]: Ошибка копированиях [2]");
        else {
            $newf = GD::resize(@$rm['img2_resize_mode'], $fn, @$rm['img2_resize_w'], @$rm['img2_resize_h'], $this->toJPG?'image/jpeg':'');
            if ($newf !== false) {
                if ($fn != $newf) @unlink($fn);
                $img2 = '2/'.pathinfo($newf, PATHINFO_BASENAME);
            } else {
                @unlink($fn);

                return Msg::put(false, '[CC_Gallery.resizeImages]: Не могу изменить размер изображения 2');
            }
        }

        $fname = '3/' . $value . '.' . pathinfo($fname0, PATHINFO_EXTENSION);
        Tools::tree_mkdir(Cfg::_get('root_path') . '/' . Cfg::get('cc_gal_upload_dir') . '/3/');
        if (!copy($fname0, $fn = Cfg::_get('root_path') . '/' . Cfg::get('cc_gal_upload_dir') . '/' . $fname))
            return Msg::put(false, "[CC_Gallery.resizeImages]: Ошибка копированиях [3]");
        else {
            $newf = GD::resize(@$rm['img3_resize_mode'], $fn, @$rm['img3_resize_w'], @$rm['img3_resize_h'], $this->toJPG?'image/jpeg':'');
            if ($newf !== false) {
                if ($fn != $newf) @unlink($fn);
                $img3 = '3/'.pathinfo($newf, PATHINFO_BASENAME);
            }  else {
                @unlink($fn);
                return Msg::put(false, '[CC_Gallery.resizeImages]: Не могу изменить размер изображения 3');
            }
        }

        return array($img1,$img2,$img3);

    }

    function fupload($id = 'model_id', $value, $imgFilesVar = 'img')
    {
        $value=(int)$value;
        if (Cfg::get('cc_gal_upload_dir') == '') return false;
        if ($_FILES[$imgFilesVar]['size'] > @$_POST['MAX_FILE_SIZE']) {
            return Msg::put(false,'[CC_Gallery.fupload]: Слишком большой файл. Максимальный размер=' . @$_POST['MAX_FILE_SIZE'] . ' байт. Загрузка остановлена.');
        }
        $name_parts = pathinfo($_FILES[$imgFilesVar]['name']);
        if (mb_stripos(Uploader::$EXT_GRAPHICS, $name_parts['extension']) === false) {
            return Msg::put(false,'[CC_Gallery.fupload]: Недопустимый тип графического файла.');
        }
        $this->fdel($id, $value);
        @mkdir(Cfg::_get('root_path') . '/tmp');
        if (!move_uploaded_file($_FILES[$imgFilesVar]['tmp_name'], ($fname0 = Cfg::_get('root_path') . '/tmp/gal' . $value . '.' . Tools::tolow($name_parts['extension'])))) {
            return Msg::put(false, "[CC_Gallery.fupload.move_uploaded_file]: Файл не загружен.");
        }

        $ires=$this->resizeImages($fname0,$value);
        @unlink($fname0);
        if($ires!==false) {
            list($img1,$img2,$img3) = $ires;
            $this->query("UPDATE cc_gal SET img1='$img1', img2='$img2', img3='$img3' WHERE $id='$value'");
        } else {
            return Msg::put(false);
        }

        return true;

    }

    function spy_url($url, $id, $value)
    {
        if (Cfg::get('cc_gal_upload_dir') == '' || trim($url) == '') return false;
        $url = str_replace('file:///', '', $url);
        $url = str_replace('file://localhost/', '', $url);
        $name_parts = pathinfo($url);
        if (@mb_stripos(Uploader::$EXT_GRAPHICS, $name_parts['extension']) === false) {
            return Msg::put(false, '[CC_Gallery.spy_url]: Недопустимый тип графического файла.');
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $sss = curl_exec($ch);
        if (($chret = curl_getinfo($ch, CURLINFO_HTTP_CODE)) != '200') {
            curl_close($ch);
            return Msg::put(false, '[CC_Gallery.spy_url]: Ошибка чтения удаленного файла ' . $url . ' CODE=' . $chret . '. ');
        }
        curl_close($ch);
        $this->fdel($id, $value);
        @mkdir(Cfg::_get('root_path') . '/tmp');
        $fname0 = Cfg::_get('root_path') . '/tmp/gal' . $value . '.' . Tools::tolow($name_parts['extension']);
        $handle1 = fopen($fname0, 'w');
        if (fwrite($handle1, $sss) === FALSE) {
            fclose($handle1);
            unlink($fname0);
            return Msg::put(false, '[CC_Gallery.spy_url]: Ошибка записи временного файла');
        }
        fclose($handle1);

        $ires=$this->resizeImages($fname0,$value);
        @unlink($fname0);
        if($ires!==false) {
            list($img1,$img2,$img3) = $ires;
            $this->query("UPDATE cc_gal SET img1='$img1', img2='$img2', img3='$img3' WHERE $id='$value'");
        } else {
            return Msg::put(false);
        }

        return true;
    }

    function fdel($id, $value)
    {
        if (Cfg::get('cc_gal_upload_dir') == '') return false;
        $cc1 = new DB;
        $id=Tools::esc($id);
        $value=(int)$value;
        $cc1->query("SELECT * FROM cc_gal WHERE $id='$value'");
        $fd = true;
        if ($cc1->qnum()) {
            $cc1->next();
            if (($cc1->qrow['img1'] != '') && (file_exists(Cfg::_get('root_path') . '/' . Cfg::get('cc_gal_upload_dir') . '/' . $cc1->qrow['img1'])))
                if (!(@unlink(Cfg::_get('root_path') . '/' . Cfg::get('cc_gal_upload_dir') . '/' . $cc1->qrow['img1']))) $fd = false;
                else @unlink(Cfg::_get('root_path') . '/' . Cfg::get('cc_gal_cache_images_dir') . '/' . $cc1->qrow['img1']);
            if (($cc1->qrow['img2'] != '') && (file_exists(Cfg::_get('root_path') . '/' . Cfg::get('cc_gal_upload_dir') . '/' . $cc1->qrow['img2'])))
                if (!(@unlink(Cfg::_get('root_path') . '/' . Cfg::get('cc_gal_upload_dir') . '/' . $cc1->qrow['img2']))) $fd = false;
                else @unlink(Cfg::_get('root_path') . '/' . Cfg::get('cc_gal_cache_images_dir') . '/' . $cc1->qrow['img2']);
            if (($cc1->qrow['img3'] != '') && (file_exists(Cfg::_get('root_path') . '/' . Cfg::get('cc_gal_upload_dir') . '/' . $cc1->qrow['img3'])))
                if (!(@unlink(Cfg::_get('root_path') . '/' . Cfg::get('cc_gal_upload_dir') . '/' . $cc1->qrow['img3']))) $fd = false;
                else @unlink(Cfg::_get('root_path') . '/' . Cfg::get('cc_gal_cache_images_dir') . '/' . $cc1->qrow['img2']);
        }
        if (!$fd) Msg::put(false, '[CC_Gallery.fdel]: Проблема с удалением файлов галереи');
        if (!$cc1->query("UPDATE cc_gal SET img1='', img2='', img3='' WHERE $id='$value'")) {
            unset($cc1);
            return Msg::put(false,'[CC_Gallery.fdel]: Ошибка записи в БД');
        }
        unset($cc1);
        return true;
    }

    function delImage($gal_id)
    {
        $gal_id = intval($gal_id);
        if ($this->fdel('gal_id', $gal_id)) 
            $this->query("DELETE FROM cc_gal WHERE gal_id='$gal_id'"); 
        else 
            return Msg::put(false,'[CC_Gallery.del]: Ошибка удаления записи из БД');
        
        return true;
    }

    /*
     * возвращает кол-во обновленных строк в случае обновления, идентификатор строки в случае добавления или false в случае ошибки
     * Входные параметры:
     * gal_id
     * model_id
     * first
     * text
     * imgFilesVar - $_FILES[imgFilesVar]
     * spy_url
     */
    function ae ($act, $r=array()) 
    {
        $q=array();
        if(isset($r['text'])) $q['text'] = Tools::esc($r['text']);

        if ($act == 'add') {
            if (!isset($r['model_id'])) {
                return Msg::put(false,'[CC_Gallery.ae]: Не задан model_id');
            }
            $model_id=$q['model_id']=(int)$r['model_id'];
            $d=$this->getOne("SELECT max(pos) FROM cc_gal WHERE model_id=$model_id ORDER BY pos DESC");
            $q['pos']=$this->qrow[0] + 1;
            $res=$this->insert('cc_gal', $q);
            $gal_id = $this->lastId();
        } else {
            if (!isset($r['gal_id'])) {
                return Msg::put(false,'[CC_Gallery.ae]: Не задан gal_id');
            }
            $gal_id=(int)$r['gal_id'];
            $d=$this->getOne("SELECT * FROM cc_gal WHERE gal_id=$gal_id");
            if($d===0){
                return Msg::put(false,'[CC_Gallery.ae]: Не найдена запись gal_id='.$gal_id);
            }
            $model_id=$d['model_id'];
            if (@$r['first']) {
                $d=$this->glist($model_id);
                $pos=1;
                $this->query("UPDATE cc_gal SET pos=$pos WHERE gal_id=$gal_id");
                foreach($d as $id=>$v){
                    $pos++;
                    if($id!=$gal_id)
                        $this->query("UPDATE cc_gal SET pos=$pos WHERE gal_id=$id");
                }
            }
            if(!empty($q)){
                $this->update('cc_gal', $q, "gal_id=$gal_id");
                $res=$this->unum();
            }else $res=0;
        }
        
        if(empty($r['imgFilesVar'])) $r['imgFilesVar']='img';
        
        if ($res!==false) {
            if (!empty($r['spy_url'])){
                if (!$this->spy_url($r['spy_url'], 'gal_id', $gal_id)) {
                    $res=Msg::put(false, '[CC_Gallery.ae]: Ссылка на изображение не обработана');
                }
            } else {
                if (@$_FILES[$r['imgFilesVar']]['name'] != ''){
                    if (!($this->fupload('gal_id', $gal_id, $r['imgFilesVar']))) {
                        $res=Msg::put(false, '[CC_Gallery.ae]: Файл изображения не загружен');
                    }
                }
            }
            
            if($res===false) {
                if($act=='add'){
                    $this->query("DELETE FROM cc_gal WHERE gal_id=$gal_id");
                }
                return false;
            }else
                if($act=='add') return $gal_id; else return $res;

        } else return false;
    }

    function glist($model_id)
    {
        $model_id=(int)$model_id;
        $d=$this->fetchAll("SELECT * FROM cc_gal WHERE (model_id='$model_id') ORDER BY pos");
        $r=array();
        foreach($d as $v){
            $r[$v['gal_id']]=array(
                'img1'=>$this->make_img_path($v['img1']),
                'img2'=>$this->make_img_path($v['img2']),
                'img3'=>$this->make_img_path($v['img3']),
                'text'=>Tools::unesc($v['text'])
            );
        }
        return $r;
    }


}
