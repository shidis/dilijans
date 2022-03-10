<?
class Uploader extends Common
{
    // последний загруженный файл;
    public $fn; // имя перемещенного файла в папке tmp без пути к нему
    public $sfile; // путь+имя перемещенного файла в tmp
    public $originFN; // имя загруженного файла. Файл загружается с рандомным именем, здесь хранится имя файла из $_FILES['tmp_name']
    public $ext; // расширение без точки в нижнем регистре последнего загруженного файла

    // допускает или все отрицания или все допущения, мешать нельзя
    static $EXT_GRAPHICS='png jpg jpeg gif';
    static $EXT_ALL='!php';

    /*
     * берез из _FILES файл и перемещает его во временную папку с уникальным именем
     * проверяет на валидность файл по расширению
     *
     * если не был удален предыдущий загруженный файл, то здесь он удалится
     */
    public function upload($_FILES_fieldName='file', $validExts=null)
    {
        if(is_null($validExts)) $validExts=Uploader::$EXT_ALL;

        // http://www.php.net/manual/ru/features.file-upload.errors.php
        if (@$_FILES[$_FILES_fieldName]['size'] > @$_POST['MAX_FILE_SIZE'] || @$_FILES[$_FILES_fieldName]['error'] == UPLOAD_ERR_FORM_SIZE) {
            return $this->putMsg(false, '[Uploader.upload]: Код 2. Размер загружаемого файла превысил значение MAX_FILE_SIZE');
        }

        if(!empty($_FILES[$_FILES_fieldName]['error'])){
            $s='';
            switch($_FILES[$_FILES_fieldName]['error']){
                case UPLOAD_ERR_INI_SIZE:
                    $s='Код 1. Размер принятого файла превысил максимально допустимый размер, который задан директивой upload_max_filesize конфигурационного файла php.ini';
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $s='Код 3. Загружаемый файл был получен только частично';
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $s='Код 4. Файл не был загружен';
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $s='Код 6. Отсутствует временная папка';
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $s='Код 7. Не удалось записать файл на диск';
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $s='Код 8. PHP-расширение остановило загрузку файла. PHP не предоставляет способа определить какое расширение остановило загрузку файла';
                    break;
            }
            return $this->putMsg(false, '[Uploader.upload]: Ошибка загрузки. '.$s);
        }


        $sfile=@$_FILES[$_FILES_fieldName]['tmp_name'];
        $sname=@$_FILES[$_FILES_fieldName]['name'];

        if(!is_uploaded_file($sfile)){
            return $this->putMsg(false, '[Uploader.upload]: Загруженный файл не найден на сервере');
        }

        $finfo=pathinfo($sname);
        $finfo['extension']=@$finfo['extension'];

        if(!Uploader::extValid($finfo['extension'],$validExts)) {
            return $this->putMsg(false, '[Uploader.upload]: Недопустимое расширение .'.$finfo['extension']);
        }

        $_fn=Tools::randString(10).'.'.Tools::tolow($finfo['extension']);
        $_sfile=Cfg::$config['root_path'].'/tmp/'.$_fn;
        if(!move_uploaded_file($sfile,$_sfile)){
            return $this->putMsg(false, '[Uploader.upload]: Файл или не загружен или ошибка перемещения');
        }

        if(!empty($this->sfile)){
            @unlink($this->sfile);
        }
        $this->sfile=$_sfile;
        $this->fn=$_fn;
        $this->originFN=@$finfo['basename'];
        $this->ext=Tools::tolow($finfo['extension']);

        return $this->putMsg(true,'');
    }

    public function spyUrl($url,$validExts=null)
    {
        if(is_null($validExts)) $validExts=Uploader::$EXT_ALL;

        if(is_file($url)){
            // загрузка с нашего сервера по абс пути
            $finfo=pathinfo($url);
            $finfo['extension']=@$finfo['extension'];

            if(!Uploader::extValid($finfo['extension'],$validExts)) {
                return $this->putMsg(false, '[Uploader.spyUrl]: Недопустимое расширение .'.$finfo['extension']);
            }

            $_fn=Tools::randString(10).'.'.Tools::tolow($finfo['extension']);
            $_sfile=Cfg::$config['root_path'].'/tmp/'.$_fn;
            if(!copy($url,$_sfile)){
                return $this->putMsg(false, '[Uploader.spyUrl]: Ошибка перемещения');
            }

        }else{
            // загрузка с удаленного сервера
            $iurl=parse_url($url);
            $finfo=pathinfo(@$iurl['path']);
            $finfo['extension']=@$finfo['extension'];

            if(!Uploader::extValid($finfo['extension'],$validExts)) {
                return $this->putMsg(false, '[Uploader.spyUrl]: Недопустимое расширение .'.$finfo['extension']);
            }

            $buf=@file_get_contents($url,false);
            if(false===$buf){
                return $this->putMsg(false, '[Uploader.spyUrl]: Не могу получить файл с удаленного сервера ('.$url.')');
            }

            $_fn=Tools::randString(10).'.'.Tools::tolow($finfo['extension']);
            $_sfile=Cfg::$config['root_path'].'/tmp/'.$_fn;

            if(false===file_put_contents($_sfile,$buf)){
                return $this->putMsg(false, '[Uploader.spyUrl]: Не могу сохранить файл ('.$_sfile.')');
            }
        }

        if(!empty($this->sfile)){
            @unlink($this->sfile);
        }
        $this->sfile=$_sfile;
        $this->fn=$_fn;
        $this->originFN=$finfo['basename'];
        $this->ext=Tools::tolow($finfo['extension']);

        return $this->putMsg(true,'');
    }

    /*
     * удалем последний загруженный файл
     */
    public function del()
    {
        if(!empty($this->sfile)){
            @unlink($this->sfile);
            $this->sfile=null;
            $this->fn=null;
            $this->originFN=null;
            $this->ext=null;
        }

        return true;
    }

    public static function extValid($ext,$validExts)
    {
        if($validExts==='') return true;

        $validExts=trim(Tools::cutDoubleSpaces($validExts));
        $validExts=" $validExts ";

        if(false!==mb_strpos($validExts,'!')){
            //режим исключения
            if(mb_stripos($validExts," !$ext ")!==false) return false;
        }else{
            //режим разрешения
            if(mb_stripos($validExts," $ext ")===false) return false;
        }

        return true;
    }




}