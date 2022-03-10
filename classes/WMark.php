<?
if (!defined('true_enter')) die ("Direct access not allowed!");

class WMark
{
    private static $left_x = 5; // отступ до левого края в процентах от ширины исходного изображения по горизонтали
    private static $top_y = 25; // отступ до верха в процентах от высоты исходного изображения по вертикади, если отрицат. то отступ снизу до низа вотермарка
    private static $width = 70; // ширина wmark в процентах от ширины исходного изображения, если отриц., то отсуп слева до правого края вотермарка
    // то же самое, но если отношение высоты к ширине больше yx
    private static $left_x1 = 5;
    private static $top_y1 = 25;
    private static $width1 = 90;
    private static $yx = 1.3;

    public static $fres = '';
    private static $fill_color = 0xFFFFFF;
    public static $wmark_path;
    public static $wmark_filename;
    private static $fn;
    public static $quality = 100;

    static function init($param = array())
    {
        foreach ($param as $k => $v) WMark::$$k = $v;
        WMark::set_dir(Cfg::_get('root_path') . '/' . Cfg::get('res_dir') . '/wmark/', WMark::$fn);
    }

    static function set_dir($path = '', $filename = '')
    {
        WMark::$wmark_path = Cfg::_get('root_path') . '/' . Cfg::get('res_dir') . '/wmark/';
        WMark::$wmark_filename = 'wmark.png';
        if ($path != '') WMark::$wmark_path = $path;
        if ($filename != '') WMark::$wmark_filename = $filename;
    }

    static function wmark_exists($path = '', $filename = '')
    {
        if (is_file(WMark::$wmark_path . WMark::$wmark_filename))
            return true;
        else return false;
    }

    static function draw($source)
    {
        $wmark = WMark::$wmark_path . '/' . WMark::$wmark_filename;
        $info_s = @getimagesize($source);
        if (!$info_s) {
            WMark::$fres = 'WMARK() error: Ошибка в исходном изображении. ';
            return false;
        }
        $info_w = @getimagesize($wmark);
        if (!$info_w) {
            WMark::$fres = 'WMARK() error: Ошибка в формате watermark. ';
            return false;
        }
        switch ($info_s[2]) {
            case 2:
                $s = imagecreatefromjpeg($source);
                break;
            case 1;
                $s = @imagecreatefromgif($source);
                break;
            case 3:
                $s = @imagecreatefrompng($source);
                break;
            default:
                WMark::$fres = 'WMARK() error: Ошибка открытия исходного файла';
                return false;
        }

        switch ($info_w[2]) {
            case 2:
                $w = imagecreatefromjpeg($wmark);
                break;
            case 1;
                $w = @imagecreatefromgif($wmark);
                break;
            case 3:
                $w = @imagecreatefrompng($wmark);
                break;
            default:
                WMark::$fres = 'WMARK() error: Ошибка открытия файла watermark';
                return false;
        }

        $ww = intval(WMark::$width / 100 * $info_s[0]);
        $wh = intval($info_s[1] / ($ww / $info_s[0]));
        $out = imagecreatetruecolor($info_s[0], $info_s[1]);
        // TODO наложение на прозрачное изображение здесь не сработвет
        imagefill($out, 0, 0, WMark::$fill_color);
        imagecopy($out, $s, 0, 0, 0, 0, $info_s[0], $info_s[1]);
        if ($info_s[1] / $info_s[0] < WMark::$yx) {
            $ww = intval($info_s[0] * WMark::$width / 100);
            $wh = intval($ww * $info_w[1] / $info_w[0]);
            if(WMark::$left_x>0) $dx = intval($info_s[0] * WMark::$left_x / 100); else $dx = intval($info_s[0] - $ww - $info_s[0] * WMark::$left_x / 100);
            if(WMark::$top_y>0) $dy = intval($info_s[1] * WMark::$top_y / 100); else $dy = intval($info_s[1] - $wh - $info_s[1] * WMark::$top_y / 100);
        } else {
            $ww = intval($info_s[0] * WMark::$width1 / 100);
            $wh = intval($ww * $info_w[1] / $info_w[0]);
            if(WMark::$left_x1>0) $dx = intval($info_s[0] * WMark::$left_x1 / 100); else $dx = intval($info_s[0] - $ww - $info_s[0] * WMark::$left_x1 / 100);
            if(WMark::$top_y1>0) $dy = intval($info_s[1] * WMark::$top_y1 / 100); else $dy = intval($info_s[1] - $wh - $info_s[1] * WMark::$top_y1 / 100);
        }
//	echo "ww=$ww, wh=$wh, dx=$dx, dy=$dy";
        imagecopyresampled($out, $w, $dx, $dy, 0, 0, $ww, $wh, $info_w[0], $info_w[1]);
        switch ($info_s[2]) {
            case 2:
                $res = @imagejpeg($out, $source, WMark::$quality);
                break;
            case 1;
                $res = @imagegif($out, $source);
                break;
            case 3:
                $res = @imagepng($out, $source);
                break;
        }
        if (!$res) {
            WMark::$fres = 'WMARK() error: Ошибка сохранения конечного файла';
            return false;
        }
        imagedestroy($s);
        imagedestroy($w);
        imagedestroy($out);
        return true;
    }


}