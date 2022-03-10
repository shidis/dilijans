<?

class SSCommon extends DB
{

    public $imgPath = '';

    function __construct()
    {
        parent::__construct();
    }

    function imgUpload($table = 'ss_news', $id, $imgNum = 1, $post_var = 'img1')
    {

        if ($_FILES[$post_var]['size'] > @$_POST['MAX_FILE_SIZE']) {
            echo('Слишком большой файл. Загрузка остановлена !');
            return (false);
        }
        $u = parse_url($_FILES[$post_var]['name']);
        $name_parts = pathinfo($u['path']);
        if (mb_strpos(Uploader::$EXT_GRAPHICS, $name_parts['extension']) === false) {
            echo 'Недопустимый тип графического файла.';
            return false;
        }
        $tmp = $_FILES[$post_var]['tmp_name'];
        switch ($table) {
            case 'ss_news':
                $mi = 'n' . '_' . $imgNum . '_';
                $field_name = 'news_id';
                break;
            case 'ss_cnt':
                $mi = 'c' . '_' . $imgNum . '_';
                $field_name = 'cnt_id';
                break;
            case 'entry':
                $mi = 'e' . '_' . $imgNum . '_';
                $field_name = 'entry_id';
                break;
            default:
                echo 'Неверное название таблицы. Файл не загружен';
                return false;
        }
        $fname = $mi . $id . '.' . $name_parts['extension'];
        if (!move_uploaded_file($tmp, Cfg::_get('root_path') . '/' . Cfg::get('cnt_upload_dir') . '/' . $fname)) {
            echo(" Файл не загружен. Максимальный размер=" . @$_POST['MAX_FILE_SIZE'] . ' байт');
        } else {
            $this->query("SELECT img$imgNum FROM $table WHERE $field_name='$id'");
            if ($this->qnum()) {
                $this->next();
                @unlink(Cfg::_get('root_path') . '/' . Cfg::get('cnt_upload_dir') . '/' . $this->qrow['img' . $imgNum]);
            }
            if (!$this->query("UPDATE $table SET img$imgNum='$fname' WHERE $field_name='$id'")) {
                @unlink(Cfg::_get('root_path') . '/' . Cfg::get('cnt_upload_dir') . '/' . $fname);
                echo 'Ошибка записи данных в таблицу.';
                return (false);
            }
        }
        return (true);
    }

    function fupload($table, $id, $value, $gr, $sw = '', $post_var = 'img') //имя переменной POST файла должна быть равна $field иначе равна $post_var
    {
        if ($_FILES[$post_var]['size'] > @$_POST['MAX_FILE_SIZE']) {
            echo('Слишком большой файл. Загрузка остановлена !');
            return (false);
        }
        if ($value == 'max') {
            $err = $this->query("SELECT max($id) FROM $table");
            $this->next();
            $value = $this->qrow[0];
        }
        $u = parse_url($_FILES[$post_var]['name']);
        $name_parts = pathinfo($u['path']);
        if (mb_strpos(Uploader::$EXT_GRAPHICS, $name_parts['extension']) === false) {
            echo 'Недопустимый тип графического файла.';
            return false;
        }
        $tmp = $_FILES[$post_var]['tmp_name'];
        $s = explode("_", $table);
        $err = $this->query("INSERT INTO ss_img VALUES('','','','','')");
        $err = $this->query("SELECT max(img_id) FROM ss_img");
        $this->next();
        $mi = $this->qrow[0];
        $fname = $s[1] . '_' . $mi . '.' . $name_parts['extension'];
        if (!move_uploaded_file($tmp, Cfg::_get('root_path') . '/' . Cfg::get('cnt_upload_dir') . '/' . $fname)) {
            echo(" Файл не загружен. Максимальный размер=" . @$_POST['MAX_FILE_SIZE'] . ' байт');
            $err = $this->query("DELETE FROM ss_img WHERE img_id='$mi'");
        } else if (!$this->query("UPDATE ss_img SET gr='$gr', id='$value', sw='$sw', img='$fname' WHERE img_id='$mi'")) {
            @unlink(Cfg::_get('root_path') . '/' . Cfg::get('cnt_upload_dir') . '/' . $fname);
            chmod(Cfg::_get('root_path') . '/' . Cfg::get('cnt_upload_dir') . '/', 0755);
            return (false);
        } else @chmod(Cfg::_get('root_path') . '/' . Cfg::get('cnt_upload_dir') . '/' . $fname, 0644);
        @chmod(Cfg::_get('root_path') . '/' . Cfg::get('cnt_upload_dir') . '/', 0755);
        return (true);
    }

    function fdel($id)
    {
        @chmod(Cfg::_get('root_path') . '/' . Cfg::get('cnt_upload_dir') . '/', 0777);
        $this->query("SELECT * FROM ss_img WHERE img_id='$id'");
        if ($this->qnum() > 0) {
            $this->next();
            if (($this->qrow['img'] > '') && (file_exists(Cfg::_get('root_path') . '/' . Cfg::get('cnt_upload_dir') . '/' . $this->qrow['img'])))
                if (!(@unlink(Cfg::_get('root_path') . '/' . Cfg::get('cnt_upload_dir') . '/' . $this->qrow['img']))) {
                    @chmod(Cfg::_get('root_path') . '/' . Cfg::get('cnt_upload_dir') . '/', 0755);
                    echo 'Файл не удаляется!';
                    return (false);
                }
        }
        $this->query("DELETE FROM ss_img WHERE img_id='$id'");
        @chmod(Cfg::_get('root_path') . '/' . Cfg::get('cnt_upload_dir') . '/', 0755);
        return (true);
    }

    function imgDel($table, $id, $imgNum)
    {
        switch ($table) {
            case 'ss_news':
                $field_name = 'news_id';
                break;
            case 'ss_cnt':
                $field_name = 'cnt_id';
                break;
            case 'entry':
                $field_name = 'entry_id';
                break;
            default:
                echo 'Неверное название таблицы. Файл не удален';
                return false;
        }
        $this->query("SELECT img$imgNum FROM $table WHERE $field_name='$id'");
        if ($this->qnum()) {
            $this->next();
            if (($this->qrow['img' . $imgNum] != '') && (file_exists(Cfg::_get('root_path') . '/' . Cfg::get('cnt_upload_dir') . '/' . $this->qrow['img' . $imgNum])))
                if (!(@unlink(Cfg::_get('root_path') . '/' . Cfg::get('cnt_upload_dir') . '/' . $this->qrow['img' . $imgNum]))) {
                    echo 'Файл не удаляется!';
                    return (false);
                }
        }
        $this->query("UPDATE $table SET img$imgNum='' WHERE $field_name='$id'");
        return (true);
    }

    function makeImgPath($imgNum = 1)
    {
        if (is_integer($imgNum))
            if (@$this->qrow['img' . $imgNum] != '') return $this->imgPath = '/' . Cfg::get('cnt_upload_dir') . '/' . $this->qrow['img' . $imgNum];
            else return $this->imgPath = '';
        else
            if ($imgNum != '') return $this->imgPath = '/' . Cfg::get('cnt_upload_dir') . '/' . $imgNum;
            else return $this->imgPath = '';
    }


    function getImgList($id, $gr, $sw = '')
    {
        $id = intval($id);
        $gr = intval($gr);
        $sw = Tools::esc($sw);
        if ($sw != '') $res = $this->query("SELECT * FROM ss_img WHERE (id='$id')AND(gr='$gr')AND(sw='$sw')"); else
            $res = $this->query("SELECT * FROM ss_img WHERE (id='$id')AND(gr='$gr')");

        return $res;
    }

    function getImgById($img_id)
    {
        $img_id = intval($img_id);
        $res = $this->query("SELECT * FROM ss_img WHERE img_id='$img_id'");
        $this->next();
        return $res;
    }


}