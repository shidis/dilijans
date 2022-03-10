<?
if (!defined('true_enter')) die ("Direct access not allowed!");

class GD
{
    public static $msgNoOutput = false; // TODO после перевода на асинхронку убрать этот режим
    public static $fres_msg = ''; // TODO убрать это, есть Msg::

    public static function get_w($f)
    {
        if (is_file($f)) {
            list($width, $height, $type, $attr) = getimagesize($f);
            return ($width);
        } else return 0;
    }

    public static function get_h($f)
    {
        if (is_file($f)) {
            list($width, $height, $type, $attr) = getimagesize($f);
            return ($height);
        } else return 0;
    }


    /*
    ресайз методом замещения исходного файла
    файл должен быть на сервере, fileName - полный путь к нему
    outputToStream == (0|1|2)

    возвращет путь и имя нового файла или false в случае ошибки

    outputFormat  {image/jpeg, image/gif, image/png, ''}

    если jpegQuality == 100 , не требуется переформатирование, method==SO и размер уже подходящий, действий над изображением не производится
    */
    public static function resize($method = 'SO', $fileName = '', $newwidth = 0, $newheight = 0, $outputFormat = '', $quality=100, $outputToStream = 0, $jpgFillColor = 0xFFFFFF)
    {
        if ($newwidth == 0 || $newheight == 0) {
            $msg = "[GD.resize]: выходные высота/ширина изображения нулевые";
            Msg::put(false,$msg);
            if (GD::$msgNoOutput) GD::$fres_msg = $msg; else echo $msg;
            return false;
        }
        // fileName только с сервера
        if (!is_file($fileName)) {
            $msg = "[GD.resize]: файл не найден";
            Msg::put(false,$msg);
            if (GD::$msgNoOutput) GD::$fres_msg = $msg; else echo $msg;
            return false;
        }
        list($width, $height, $type) = getimagesize($fileName);
        switch($type){
            case IMAGETYPE_JPEG:
                $format = "image/jpeg";
                break;
            case IMAGETYPE_GIF:
                $format = "image/gif";
                break;
            case IMAGETYPE_PNG:
                $format = "image/png";
                break;
            default:
                $msg = "[GD.resize]: MIME тип изображения не определился";
                Msg::put(false,$msg);
                if (GD::$msgNoOutput) GD::$fres_msg = $msg; else echo $msg;
                return false;
        }

        if ($width > 0 && $height > 0)
            $ratio = $width / $height;
        else {
            $msg = "[GD.resize]: высота/ширина исходного изображения не определились";
            Msg::put(false,$msg);
            if (GD::$msgNoOutput) GD::$fres_msg = $msg; else echo $msg;
            return false;
        }

        if ($method == 'BW') {
            // ограничение по ширине, высота как получиться. С увеличением
            $newheight = ceil($height * $newwidth / $width);
        } elseif ($method == 'BH') {
            // ограничение по высоте, ширина как получиться. С увеличением
            $newwidth = ceil($newheight * $width / $height);
        } elseif ($method == 'SB') {
            // TODO вписываем в максимальный размер -- пока увеличивает до макс значения, а должно вписывать в размер
            if (max($newwidth, $newheight) == $newwidth) $newheight = intval($height / $width * $newwidth);
            if (max($newwidth, $newheight) == $newheight) $newwidth = intval($width / $height * $newheight);
        } elseif (($method == 'SO' && ($newwidth < $width || $newheight < $height))) {
            // вписываем в размер с пропорциями, без увеличения, только уменьшение
            if ($newwidth < $width && $newheight > $height) $newheight = intval($height / $width * $newwidth);
            elseif ($newheight < $height && $newwidth > $width) $newwidth = intval($width / $height * $newheight); else {
                if (max($width, $height) == $width) {
                    $newheight0 = intval($height / $width * $newwidth);
                    if ($newheight0 > $newheight) $newwidth = intval($newheight * $ratio); else $newheight = $newheight0;
                }
                if (max($width, $height) == $height) {
                    $newwidth0 = intval($width / $height * $newheight);
                    if ($newwidth0 > $newwidth) $newheight = intval($newwidth / $ratio); else $newwidth = $newwidth0;
                }
            }
        } else {
            list($newwidth, $newheight) = array($width, $height);
        }

        if(empty($quality)) $quality=null;

        if($quality==100 && $outputFormat==$format && 0){
            if(
                $newheight==$width && $newheight==$height ||
                $method=='BW' && $newwidth==$width ||
                $method=='BH' && $newheight==$height ||
                $method=='SO' && $width<=$newwidth && $height<=$newheight
            ) {
                return $fileName;
            }
        }

        switch ($format) {
            case 'image/jpeg':
                $source = @imagecreatefromjpeg($fileName);
                break;
            case 'image/gif';
                $source = @imagecreatefromgif($fileName);
                break;
            case 'image/png':
                $source = imagecreatefrompng($fileName);
                break;
        }
        if (empty($source)) {
            $msg = "[GD.resize]: imagecreate() error";
            Msg::put(false,$msg);
            if (GD::$msgNoOutput) GD::$fres_msg = $msg; else echo $msg;
            return false;
        }

        $target = @imagecreatetruecolor($newwidth, $newheight);

        if($target===false){
            imagedestroy($source);
            $msg = "[GD.resize]: imagecreatetruecolor() error";
            Msg::put(false,$msg);
            if (GD::$msgNoOutput) GD::$fres_msg = $msg; else echo $msg;
            return false;
        }

        if(!empty($outputFormat) && $outputFormat!=$format){
            switch($outputFormat){
                case 'image/jpeg':
                    $ext='jpg';
                    break;
                case 'image/png':
                    $ext='png';
                    break;
                case 'image/gif':
                    $ext='gif';
                    break;
            }
            $format=$outputFormat;
            $dir_name = dirname($fileName);
            $ext0=pathinfo($fileName,PATHINFO_EXTENSION);
            $oldFile=$fileName;
            $fileName = $dir_name . ($dir_name != '' ? '/' : '') . basename($fileName, '.' . $ext0) . '.'.$ext;
        }

        switch ($format) {
            case 'image/jpeg':
                imagefill($target, 0, 0, $jpgFillColor);
                break;
            case "image/gif":
                // integer representation of the color black (rgb: 0,0,0)
                $background = imagecolorallocate($target, 0, 0, 0);
                // removing the black from the placeholder
                imagecolortransparent($target, $background);
                break;
            case "image/png":
                // integer representation of the color black (rgb: 0,0,0)
                $background = imagecolorallocate($target, 0, 0, 0);
                // removing the black from the placeholder
                imagecolortransparent($target, $background);
                // turning off alpha blending (to ensure alpha channel information
                // is preserved, rather than removed (blending with the rest of the
                // image in the form of black))
                imagealphablending($target, false);
                // turning on alpha channel information saving (to ensure the full range
                // of transparency is preserved)
                imagesavealpha($target, true);
                break;
        }

        if(!@imagecopyresampled($target, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height)){
            imagedestroy($target);
            imagedestroy($source);
            $msg = "[GD.resize]: imagecopyresampled() error";
            Msg::put(false,$msg);
            if (GD::$msgNoOutput) GD::$fres_msg = $msg; else echo $msg;
            return false;
        }

        if ($outputToStream) {
            header("Content-type: $format");
            if($outputToStream==2)
                header("Content-Disposition: attachment; filename=".basename($fileName));
            else {
                header("Cache-control: public");
                header('Date: Wed, 24 Aug 2011 07:59:33 GMT');
                header('Last-Modified: Wed, 24 Aug 2011 07:59:33 GMT');
                header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 3600));
            }
        }

        switch ($format) {
            case 'image/jpeg':
                $res = imagejpeg($target, !$outputToStream ? $fileName : NULL, $quality);
                break;
            case 'image/gif';
                $res = imagegif($target, !$outputToStream ? $fileName : NULL);
                break;
            case 'image/png':
                $res = imagepng($target, !$outputToStream ? $fileName : NULL, $quality);
                break;
            default:
                $res = false;
        }

        imagedestroy($target);
        imagedestroy($source);

        if ($res===false) return Msg::put(false,'[GD.resize]: Ошибка во время изменения размера');

        if(!empty($oldFile) && !$outputToStream) unlink($oldFile);

        return $fileName;
    }

    /*
     * Размеры обрезки задаются или в абс величинах ИЛИ в процентах, совмещать абс и % низя
     *
     *  outputFormat  {image/jpeg, image/gif, image/png, ''}
     */
    static public function crop ($fileName, $x1, $y1, $x2, $y2, $outputFormat = '', $quality=100, $outputToStream = 0, $jpgFillColor=0xFFFFFF)
    {
        // fileName только с сервера
        if (!is_file($fileName)) {
            $msg = "[GD.crop]: файл не найден";
            Msg::put(false,$msg);
            if (GD::$msgNoOutput) GD::$fres_msg = $msg; else echo $msg;
            return false;
        }

        list($width, $height, $type) = getimagesize($fileName);

        switch($type){
            case IMAGETYPE_JPEG:
                $format = "image/jpeg";
                break;
            case IMAGETYPE_GIF:
                $format = "image/gif";
                break;
            case IMAGETYPE_PNG:
                $format = "image/png";
                break;
            default:
                $msg = "[GD.crop]: MIME тип изображения не определился";
                Msg::put(false,$msg);
                if (GD::$msgNoOutput) GD::$fres_msg = $msg; else echo $msg;
                return false;
        }

        switch ($format) {
            case 'image/jpeg':
                $source = @imagecreatefromjpeg($fileName);
                break;
            case 'image/gif';
                $source = @imagecreatefromgif($fileName);
                break;
            case 'image/png':
                $source = @imagecreatefrompng($fileName);
                break;
        }
        if (empty($source)) {
            $msg = "[GD.crop]: imagecreate() error";
            Msg::put(false,$msg);
            if (GD::$msgNoOutput) GD::$fres_msg = $msg; else echo $msg;
            return false;
        }

        if(!empty($outputFormat) && $outputFormat!=$format){
            switch($outputFormat){
                case 'image/jpeg':
                    $ext='jpg';
                    break;
                case 'image/png':
                    $ext='png';
                    break;
                case 'image/gif':
                    $ext='gif';
                    break;
            }
            $format=$outputFormat;
            $dir_name = dirname($fileName);
            $ext0=pathinfo($fileName,PATHINFO_EXTENSION);
            $oldFile=$fileName;
            $fileName = $dir_name . ($dir_name != '' ? '/' : '') . basename($fileName, '.' . $ext0) . '.'.$ext;
        }

        if(mb_strpos($x1,'%')!==false){
            $x1a=str_replace('%','',$x1);
            $_x1=round($width*$x1a/100);
        }else{
            $_x1=$x1;
        }
        if(mb_strpos($x2,'%')!==false){
            $x2a=str_replace('%','',$x2);
            $_x2=round($width*$x2a/100);
        }else{
            $_x2=$x2;
        }
        $newwidth=abs($_x2-$_x1);
        if($newwidth>$width) $newwidth=$width;

        if(mb_strpos($y1,'%')!==false){
            $y1a=str_replace('%','',$y1);
            $_y1=round($width*$y1a/100);
        }else{
            $_y1=$y1;
        }
        if(mb_strpos($y2,'%')!==false){
            $y2a=str_replace('%','',$y2);
            $_y2=round($height*$y2a/100);
        }else{
            $_y2=$y2;
        }
        $newheight=abs($_y2-$_y1);
        if($newheight>$height) $newheight=$height;

        $target = @imagecreatetruecolor($newwidth, $newheight);

        if($target===false){
            imagedestroy($source);
            $msg = "[GD.crop]: imagecreatetruecolor() error";
            Msg::put(false,$msg);
            if (GD::$msgNoOutput) GD::$fres_msg = $msg; else echo $msg;
            return false;
        }

        switch ($format) {
            case 'image/jpeg':
                imagefill($target, 0, 0, $jpgFillColor);
                break;
            case "image/gif":
                // integer representation of the color black (rgb: 0,0,0)
                $background = imagecolorallocate($target, 0, 0, 0);
                // removing the black from the placeholder
                imagecolortransparent($target, $background);
                break;
            case "image/png":
                // integer representation of the color black (rgb: 0,0,0)
                $background = imagecolorallocate($target, 0, 0, 0);
                // removing the black from the placeholder
                imagecolortransparent($target, $background);
                // turning off alpha blending (to ensure alpha channel information
                // is preserved, rather than removed (blending with the rest of the
                // image in the form of black))
                imagealphablending($target, false);
                // turning on alpha channel information saving (to ensure the full range
                // of transparency is preserved)
                imagesavealpha($target, true);
                break;
        }
        if(!@imagecopy($target, $source, 0, 0, $_x1, $_y1, $newwidth, $newheight)){
            imagedestroy($target);
            imagedestroy($source);
            $msg = "[GD.crop]: imagecopy() error";
            Msg::put(false,$msg);
            if (GD::$msgNoOutput) GD::$fres_msg = $msg; else echo $msg;
            return false;
        }

        if ($outputToStream) {
            header("Content-type: $format");
            if($outputToStream==2)
                header("Content-Disposition: attachment; filename=".basename($fileName));
            else {
                header("Cache-control: public");
                header('Date: Wed, 24 Aug 2011 07:59:33 GMT');
                header('Last-Modified: Wed, 24 Aug 2011 07:59:33 GMT');
                header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 3600));
            }
        }

        if(empty($quality)) $quality=null;

        switch ($format) {
            case 'image/jpeg':
                $res = imagejpeg($target, !$outputToStream ? $fileName : NULL, $quality);
                break;
            case 'image/gif';
                $res = imagegif($target, !$outputToStream ? $fileName : NULL);
                break;
            case 'image/png':
                $res = imagepng($target, !$outputToStream ? $fileName : NULL, $quality);
                break;
            default:
                $res = false;
        }

        imagedestroy($target);
        imagedestroy($source);

        if ($res===false) {
            $msg='[GD.crop]: Ошибка во время изменения размера';
            if (GD::$msgNoOutput) GD::$fres_msg = $msg; else echo $msg;
            return Msg::put(false,$msg);
        }

        if(!empty($oldFile) && !$outputToStream) unlink($oldFile);

        return $fileName;

    }


}

