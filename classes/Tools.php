<?
if (!defined('true_enter')) die ("Direct access not allowed!");

class Tools
{
    static private $sname_max_len = 255;
    static private $iso = [
        "Є"  => "YE",
        "І"  => "I",
        "Ѓ"  => "G",
        "і"  => "i",
        "№"  => "#",
        "є"  => "ye",
        "ѓ"  => "g",
        "А"  => "A",
        "Б"  => "B",
        "В"  => "V",
        "Г"  => "G",
        "Д"  => "D",
        "Е"  => "E",
        "Ё"  => "YO",
        "Ж"  => "ZH",
        "З"  => "Z",
        "И"  => "I",
        "Й"  => "J",
        "К"  => "K",
        "Л"  => "L",
        "М"  => "M",
        "Н"  => "N",
        "О"  => "O",
        "П"  => "P",
        "Р"  => "R",
        "С"  => "S",
        "Т"  => "T",
        "У"  => "U",
        "Ф"  => "F",
        "Х"  => "H",
        "Ц"  => "C",
        "Ч"  => "CH",
        "Ш"  => "SH",
        "Щ"  => "SHH",
        "Ъ"  => "'",
        "Ы"  => "Y",
        "Ь"  => "",
        "Э"  => "E",
        "Ю"  => "YU",
        "Я"  => "YA",
        "а"  => "a",
        "б"  => "b",
        "в"  => "v",
        "г"  => "g",
        "д"  => "d",
        "е"  => "e",
        "ё"  => "yo",
        "ж"  => "zh",
        "з"  => "z",
        "и"  => "i",
        "й"  => "j",
        "к"  => "k",
        "л"  => "l",
        "м"  => "m",
        "н"  => "n",
        "о"  => "o",
        "п"  => "p",
        "р"  => "r",
        "с"  => "s",
        "т"  => "t",
        "у"  => "u",
        "ф"  => "f",
        "х"  => "h",
        "ц"  => "c",
        "ч"  => "ch",
        "ш"  => "sh",
        "щ"  => "shh",
        "ъ"  => "",
        "ы"  => "i",
        "ь"  => "",
        "э"  => "e",
        "ю"  => "yu",
        "я"  => "ya",
        '+'  => "-",
        "-"  => "-",
        "."  => ".",
        ","  => "",
        //	   ","=>"-",
        " "  => "-",
        "^"  => "-",
        "/"  => "-",
        "\"" => "-",
    ];

    static private $specSymbols = [".", ",", "-", "_"]; // символы, которые могут быть в урле, но не могут повторяться
    static private $trimSpecSymbols = [".", ",", "-", "_"]; // запрещенные пограничный символы (подмножество specSymbols)

    // http://www.faqs.org/rfcs/rfc1738.html


    static public function _substr_replace($string, $replace, $position_needle, $length_needle)  // substr_replace для ЮТФ
    {
        return mb_substr($string, 0, $position_needle) . $replace . mb_substr($string, $position_needle + $length_needle);
    }

    static function mb_substr_replace($string, $replacement, $start, $length = null, $encoding = null)
    {
        if ($encoding == null)
        {
            if ($length == null)
            {
                return mb_substr($string, 0, $start) . $replacement;
            }
            else
            {
                return mb_substr($string, 0, $start) . $replacement . mb_substr($string, $start + $length);
            }
        }
        else
        {
            if ($length == null)
            {
                return mb_substr($string, 0, $start, $encoding) . $replacement;
            }
            else
            {
                return mb_substr($string, 0, $start, $encoding) . $replacement . mb_substr($string, $start + $length, mb_strlen($string, $encoding), $encoding);
            }
        }
    }

    /**
     * Вывод объекта
     *
     * @param mixed $data - объект
     * @param bool $die = true - закончить вывод?
     * @param bool $debug = true - выводить отладочную информацию?
     */
    static function p($data, $die = true, $debug = false)
    {
        echo "<xmp>" . print_r($data, true) . "</xmp>";
        if ($debug) echo "<br><hr><b>DEBUG</b><br><xmp>" . print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true) . "</xmp>";
        if ($die) die((string)rand(1000, 20000));
    }

    /*
    * корректная отработка PREG_OFFSET_CAPTURE
    */
    static function mb_preg_match_all($ps_pattern, $ps_subject, &$pa_matches, $pn_flags = PREG_PATTERN_ORDER, $pn_offset = 0, $ps_encoding = NULL)
    {
        // WARNING! - All this function does is to correct offsets, nothing else:
        //
        if (is_null($ps_encoding)) $ps_encoding = mb_internal_encoding();

        $pn_offset = strlen(mb_substr($ps_subject, 0, $pn_offset, $ps_encoding));
        $ret = preg_match_all($ps_pattern, $ps_subject, $pa_matches, $pn_flags, $pn_offset);

        if ($ret && ($pn_flags & PREG_OFFSET_CAPTURE)) foreach ($pa_matches as &$ha_match)
        {
            foreach ($ha_match as &$ha_match)
            {
                $ha_match[1] = mb_strlen(substr($ps_subject, 0, $ha_match[1]), $ps_encoding);
            }
        }
        //
        // (code is independent of PREG_PATTER_ORDER / PREG_SET_ORDER)

        return $ret;
    }

    /*
    * корректная отработка PREG_OFFSET_CAPTURE
    */
    static function mb_preg_match($ps_pattern, $ps_subject, &$pa_matches, $pn_flags = PREG_PATTERN_ORDER, $pn_offset = 0, $ps_encoding = NULL)
    {
        // WARNING! - All this function does is to correct offsets, nothing else:
        //
        if (is_null($ps_encoding)) $ps_encoding = mb_internal_encoding();

        $pn_offset = strlen(mb_substr($ps_subject, 0, $pn_offset, $ps_encoding));
        $ret = preg_match($ps_pattern, $ps_subject, $pa_matches, $pn_flags, $pn_offset);

        if ($ret && ($pn_flags & PREG_OFFSET_CAPTURE)) foreach ($pa_matches as &$ha_match)
        {
            $ha_match[1] = mb_strlen(substr($ps_subject, 0, $ha_match[1]), $ps_encoding);
        }
        //
        // (code is independent of PREG_PATTER_ORDER / PREG_SET_ORDER)

        return $ret;
    }

    static function mb_str_pad($input, $pad_length, $pad_string = ' ', $pad_type = STR_PAD_RIGHT)
    {
        $diff = strlen($input) - mb_strlen($input);

        return str_pad($input, $pad_length + $diff, $pad_string, $pad_type);
    }

    static public function str2iso($s, $len = -1, $reg = 'lower', $iso_table = '')
    {
        $iso_table = $iso_table === '' ? Tools::$iso : $iso_table;
        if ($len == -1) $len = Tools::$sname_max_len;
        $s = (trim(strip_tags($s)));
        if ($s == '') return '';
        $s = strtr($s, $iso_table);
        //оставляем только допустимые для урла символы, из набора specSymbols
        $s = preg_replace("/[^0-9A-Za-z" . implode('\\', Tools::$specSymbols) . "]/", '', $s);


        do
        {
            // удаляем дубли разделителей и пуктуации
            $i = 0;
            foreach (Tools::$specSymbols as $v)
            {
                while ($s != ($s0 = str_replace($v . $v, $v, $s)))
                {
                    $i++;
                    $s = $s0;
                }
            }

            // удаляем особые комбинации
            $j = 0;
            while ($s != ($s0 = str_replace("-_", '-', $s)))
            {
                $j++;
                $s = $s0;
            }
            while ($s != ($s0 = str_replace("_-", '-', $s)))
            {
                $j++;
                $s = $s0;
            }

        } while ($i || $j);

        // удаляем начальные и конечные разделители / пунктуацию
        do
        {
            $i = 0;
            foreach (Tools::$trimSpecSymbols as $v)
            {
                if (mb_substr($s, 0, 1) == $v)
                {
                    $s = Tools::_substr_replace($s, '', 0, 1);
                    $i++;
                }
                if (mb_substr($s, mb_strlen($s) - 1, 1) == $v)
                {
                    $s = Tools::_substr_replace($s, '', mb_strlen($s) - 1, 1);
                    $i++;
                }
            }
        } while ($i);

        if ($reg == 'lower') $s = mb_strtolower($s);
        elseif ($reg == 'upper') $s = mb_strtoupper($s);

        return $len ? mb_substr($s, 0, $len) : $s;
    }

    static public function fname2iso($s, $reg = '')
    {
        $t = static::$iso;
        $t['.'] = '.';

        return Tools::str2iso($s, -1, $reg, $t);
    }

    static public function prn($arr, $pref = '', $echo = true)
    {
        $s = $pref . ' ' . nl2br(str_replace(Chr(32), '&nbsp;', print_r($arr, true)));
        if ($echo) echo $s;
        else return $s;
    }

    static public function flu()
    {
        @ob_get_contents();
        @ob_flush();
        @flush();
    }


    static public function imp($a)  // == http_build_query()
    {
        $a = http_build_query($a);

        return $a != '' ? "?$a" : '';
    }

    static public function toup($str)
    {
        //$s=strtr($str,'abcdefgihjklmnopqrstuvwxyzабвгдеёжзийклмнопрстуфхцчшщъыьэюя','ABCDEFGIHJKLMNOPQRSTUVWXYZАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ');
        return (mb_strtoupper($str));
    }

    static public function tolow($str)
    {
        //$s=strtr($str,'ABCDEFGIHJKLMNOPQRSTUVWXYZАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ','abcdefgihjklmnopqrstuvwxyzабвгдеёжзийклмнопрстуфхцчшщъыьэюя');
        return (mb_strtolower($str));
    }

    static public function emailValid($email)
    {
        $email = trim($email);
        if (!empty($email) && preg_match('|^([a-z0-9_\.\-]{1,20})@([a-z0-9\.\-]{1,20})\.([a-z]{2,4})$|uis', $email)) return true;
        else return false;
    }

    static public function checkEmail($email)
    {
        $email = trim($email);
        $res = preg_match("/^([-_a-zA-Z0-9]*)$@^([-_a-zA-Z0-9]*)$\.^([-_a-zA-Z0-9]*)$/ui", $email);

        return ($res);
    }

    public static function phoneValid($tel)
    {
        if (preg_match("~^[0-9\-\(\)\s\+]+$~u", $tel)) return true;
        else return false;
    }

    public static function phoneValid2($tel)
    {
        /*
          * пропускаем строки длиной не более 20 символов [+0-9\-\s\(\)]
          */
        $tel = trim($tel);
        if (!preg_match("~^[0-9\(\)\-\s\+]+$~iu", $tel) || mb_strlen($tel) > 20 || mb_strlen($tel) < 10) return false;

        return true;
    }

    /*
     * приводит номер телефона к формату 71231234567
     */
    public static function leadFormatTel($tel)
    {
        /*
         * длину не проверяем
         * убираем не цифры
         * если начинается с 8 то меняем ее на 7
         * если начинается с +, то убираем +
         * если номер не 3 и не с 7 начинается, то добаляем 7 в начале
         */
        $tel = trim(preg_replace("~[^0-9]~u", '', trim($tel)));
        $tel = preg_replace("~^8~u", '7', $tel);
        $tel = preg_replace("~^([^37])(.+)~u", "7$1$2", $tel);

        return $tel;
    }


    /*
     * форматирует тедефон к человепонятному стандартному формату
     */
    public static function humanPhoneNumber($tel)
    {
        return preg_replace("~(.*)([0-9]{3})([0-9]{3})([0-9]{2})([0-9]{2})$~", "$1 ($2) $3-$4-$5", static::leadFormatTel($tel));
    }

    // переворачивает дату из 00-00-0000 в 0000-00-00
    static public function fdate($dt)
    {
        if (preg_match("/([0-9]{2})-([0-9]{2})-([0-9]{4})/", $dt, $m)) return (date("Y-m-d", mktime(0, 0, 0, $m[2], $m[1], $m[3])));
        else return false;
    }

    // переворачивает дату из 0000-00-00 в 00-00-0000
    static public function sdate($dt, $delimeter = '-', $shortYear = false)
    {
        if (preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})/", $dt, $m)) if ($shortYear) return "{$m[3]}{$delimeter}{$m[2]}{$delimeter}" . mb_substr($m[1], 2, 2);
        else return "{$m[3]}{$delimeter}{$m[2]}{$delimeter}{$m[1]}";

        elseif (preg_match("/([0-9]{2})-([0-9]{2})-([0-9]{4})/", $dt, $m)) if ($shortYear) return "{$m[1]}{$delimeter}{$m[2]}{$delimeter}" . mb_substr($m[3], 2, 2);
        else return "{$m[1]}{$delimeter}{$m[2]}{$delimeter}{$m[3]}";

        else return false;
    }

    // только время
    static public function stime($dt, $delimeter = ':', $noSec = false)
    {
        if (preg_match("/([0-9]{2}):([0-9]{2}):([0-9]{2})/", $dt, $m)) if ($noSec) return ("{$m[1]}{$delimeter}{$m[2]}");
        else return ("{$m[1]}{$delimeter}{$m[2]}{$delimeter}{$m[3]}");
        else return false;
    }

    // переворачивает год из 0000-00-00 в 00-00-0000 с сохранением времени
    static public function sDateTime($dt, $delimeter = '-')
    {
        if (preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})/", $dt, $m)) return ("{$m[3]}{$delimeter}{$m[2]}{$delimeter}{$m[1]} {$m[4]}:{$m[5]}:{$m[6]}");

        elseif (preg_match("/([0-9]{2})-([0-9]{2})-([0-9]{4}) ([0-9]{2}):([0-9]{2}):([0-9]{2})/", $dt, $m)) return ("{$m[1]}{$delimeter}{$m[2]}{$delimeter}{$m[3]} {$m[4]}:{$m[5]}:{$m[6]}");

        else return false;
    }

    static public function dateArr($dt)
    {
        preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})/", $dt, $m);
        if (count($m) < 3) return (false);
        else
            return [1 => $m[3], 2 => $m[2], 3 => $m[1]];
    }

    static public function dt()
    {
        return date("Y-m-d H:i:s");
    }


    static public function stripTags($s)
    {
        return strip_tags($s);
    }

    /**
     * @param string $a
     * @return array
     */
    static public function parseStr($a)
    {
        parse_str($a, $r);

        return $r;
    }

    /**
     * @param string $a
     * @param string $c [optional]
     *
     * @return mixed
     */
    static public function _parseStr($a, $c = '')
    {
        parse_str($a, $r);
        if ($c != '')
        {
            $c = explode('|', $c);  // $c='UTF-8|windows-1251'
            foreach ($r as $k => &$v) $v = iconv($c[0], $c[1], $v);
        }

        return $r;
    }

    static public function arrayKeyDiff($a1, $a2)
    {
        if (is_array($a2))
        {
            foreach ($a2 as $k => $v) if (array_key_exists($k, $a1)) unset($a1[$k]);
        }
        elseif (isset($a1[$a2])) unset($a1[$a2]);

        return $a1;
    }

    static public function utf($t)
    {
        return iconv('windows-1251', "UTF-8//IGNORE", $t);
    }

    static public function cp1251($t)
    {
        return iconv("UTF-8", 'windows-1251//IGNORE', $t);
    }

    static public function cutDoubleSpaces($s)
    {
        while (($s1 = str_replace('  ', ' ', $s)) != $s) $s = $s1;

        return $s1;
    }

    /*
    * убирает пробелы и заменяет запятые на точки. Для корректной записи строкового числа в БД
    */
    static function toFloat($s)
    {
        $s = str_replace(',', '.', $s);
        while (($s1 = str_replace(' ', '', $s)) != $s) $s = $s1;

        return (float)trim($s);
    }

    static public function post_utf_to_cp1251()
    {
        foreach ($_POST as $k => $v) $_POST[$k] = iconv('utf-8', 'cp1251//IGNORE', urldecode($v));
    }

    static public function esc($s, $decode = false)
    {

        if ($decode) return addslashes(html_entity_decode($s, ENT_QUOTES, 'UTF-8'));
        else return addslashes($s);
    }

    static public function unesc($s)
    {
        return stripslashes($s);
    }

    // для применения внутри кавычек типа <input value="....">
    static public function html($s, $strip = true)
    {
        return htmlentities($strip ? stripslashes($s) : $s, ENT_QUOTES, 'UTF-8');
    }

    // для применения внутри textarea <textarea>...</textarea>
    static public function taria($s, $strip = true)
    {
        return preg_replace("/<[\s]*\/[\s]*textarea[\s]*>/is", '&lt;/textarea&gt;', $strip ? stripslashes($s) : $s);
    }

    // обратная taria(), для записи в БД
    static public function untaria($s, $esc = true)
    {
        $s = preg_replace("/&lt;[\s]*\/[\s]*textarea[\s]*&gt;/is", '</textarea>', $s);

        return $esc ? addslashes($s) : $s;
    }


    static public function tree_mkdir($path)
    { // вконце должен быть слеш иначе последний фрагмент определиться как файл а не папка
        $s = '';
        if (mb_substr($path, -1, 1) == '/') $path .= '.';
        $path = str_replace('//', '/', $path);
        foreach (explode('/', dirname($path)) as $v)
        {
            $s = $s . $v . '/';
            if (!is_dir($s))
            {
                mkdir($s);
                //				chmod($s,'0777');
            }
        }
    }

    private static function string_sort_asc($a, $b)
    {
        if (mb_strlen($a) < mb_strlen($b))
        {
            return -1;
        }
        elseif (mb_strlen($a) == mb_strlen($b))
        {
            return 0;
        }
        else
        {
            return 1;
        }
    }

    private static function string_sort_desc($a, $b)
    {
        if (mb_strlen($a) < mb_strlen($b))
        {
            return 1;
        }
        elseif (mb_strlen($a) == mb_strlen($b))
        {
            return 0;
        }
        else
        {
            return -1;
        }
    }

    // сортировка массива строк по длине строк элеметов без сохранения ключей
    static public function usortStr($a = [], $order = 'ASC')
    {
        $order == 'ASC' ? usort($a, 'Tools::string_sort_asc') : usort($a, 'Tools::string_sort_desc');

        return $a;
    }

    // сортировка массива строк по длине строк элеметов с сохранением ключей
    static public function uasortStr($a = [], $order = 'ASC')
    {
        $order == 'ASC' ? uasort($a, 'Tools::string_sort_asc') : uasort($a, 'Tools::string_sort_desc');

        return $a;
    }

    // сортировка массива строк по длине строк по ключам с сохранением ключей
    static public function uksortStr($a = [], $order = 'ASC')
    {
        $order == 'ASC' ? uksort($a, 'Tools::string_sort_asc') : uksort($a, 'Tools::string_sort_desc');

        return $a;
    }

    // возвращает фактический тип параметра, например при передаче строки '123.4' будет возвращено 'float', а при 10.0 - integer
    static public function typeOf($a)
    {
        if (is_array($a)) return 'array';
        if (is_integer($a)) return 'integer';
        if (is_float($a)) return 'float';
        if (preg_match("~^[0-9\-]+$~u", $a) && intval($a * 1) == $a) return 'integer';
        if (preg_match("~^[0-9\.\-]+[0-9]$~u", $a)) return 'float';
        if (is_string($a)) return 'string';

    }

    static public function zeroFill($s, $digits)
    {
        return (str_pad($s, $digits, "0", STR_PAD_LEFT));
    }

    /*
    * рекурсивное удаление папки
    */
    static public function removeDir($dir)
    {
        if (!is_dir($dir)) return;
        $files = @scandir($dir);
        array_shift($files); // remove '.' from array
        array_shift($files); // remove '..' from array

        foreach ($files as $file)
        {
            $file = $dir . '/' . $file;
            if (is_dir($file))
            {
                Tools::removeDir($file);
                if (is_dir($file))
                {
                    chmod($file, 0777);
                    @rmdir($file);
                }
            }
            else
            {
                chmod($file, 0777);
                @unlink($file);
            }
        }
        chmod($dir, 0777);
        @rmdir($dir);
    }

    static private function iconv_array_callback(&$v, $k, $param)
    {
        $v = iconv($param[0], $param[1], $v);
    }

    static public function iconv_array($inCharset, $outCharset, $inArray)
    {
        array_walk_recursive($inArray, 'Tools::iconv_array_callback', [$inCharset, $outCharset]);

        return $inArray;
    }

    static public function isMobile()
    {
        if (preg_match("/(midp|samsung|iphone|ipad|android|nokia|j2me|avant|docomo|novarra|palmos|palmsource|opwv|chtml|pda|mmp|blackberry|mib)/i", @$_SERVER['HTTP_USER_AGENT'])) return true;
        else return false;
    }

    // возвращает первую часть строки до первого разделителя
    static public function firstS($str, $delimeter = ',')
    {
        $s = explode($delimeter, $str);

        return $s[0];
    }

    // возвращает всю подстроку после первого разделителя
    static public function otherS($str, $delimeter = ',', $emptyIfNoDelimeter = true)
    {
        $s = explode($delimeter, $str);
        if (count($s) > 1) return trim(implode($delimeter, array_slice($s, 1)));
        elseif ($emptyIfNoDelimeter) return '';
        else return $str;
    }

    static public function month($month, $v = 1)
    {
        switch ($v)
        {
            case 1:
                $a = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];
                break;
            default:
                $a = ['январь', 'февраль', 'март', 'апрель', 'май', 'июнь', 'июль', 'август', 'сентябрь', 'октябрь', 'ноябрь', 'декабрь'];
        }

        return @$a[$month - 1];
    }

    static public function like($s, $escapeUnderscore = true, $escapePercent = true, $doubleSlash = false)
    {
        if ($escapeUnderscore) $s = str_replace('_', '\_', $s);
        if ($doubleSlash) $s = str_replace('\\', '', $s);
        if ($escapePercent) $s = str_replace('%', '\%', $s);

        return addslashes($s);
    }

    static public function like_($s, $escapeUnderscore = true, $escapePercent = true)
    {
        if ($escapeUnderscore) $s = str_replace('_', '\_', $s);
        if ($escapePercent) $s = str_replace('%', '\%', $s);

        return ($s);
    }

    static public function delCookie($name)
    {
        setcookie($name, "", time() - 3600, "/", '.' . Url::trimWWW(Cfg::get('site_url')));
        unset($_COOKIE[$name]);
    }

    static public function setCookie($name, $value, $expire = 31536000)
    {
        setcookie($name, $value, time() + $expire, "/", '.' . Url::trimWWW(Cfg::get('site_url')));
        $_COOKIE[$name] = $value;
    }

    static public function getCookie($name)
    {
        return @$_COOKIE[$name];
    }

    static public function strarr($a)
    {
        $r = [];
        $s = explode('&', $a);
        foreach ($s as $k)
        {
            $v = explode('=', $k);
            $r[urldecode($v[0])] = (urldecode($v[1]));
        }

        return $r;
    }

    // возвращает 0 - если строки равны
    // работает через преобразование в cp1251 !!!
    static public function mb_strcasecmp($str1, $str2)
    {
        //return strcasecmp(iconv("UTF-8",'windows-1251//IGNORE',$str1), iconv("UTF-8",'windows-1251//IGNORE',$str2));  // 21000
        //return strcmp(strtoupper(iconv("UTF-8",'windows-1251//IGNORE',$str1)), strtoupper(iconv("UTF-8",'windows-1251//IGNORE',$str2)));  //22000
        //return strcmp(mb_strtoupper($str1), mb_strtoupper($str2));  // 48000
        if (preg_match("/^" . preg_quote($str1, '/') . "$/iu", $str2)) return 0;
        else return 1;  //37000
        //if(strtoupper(iconv("UTF-8",'windows-1251//IGNORE',$str1))===strtoupper(iconv("UTF-8",'windows-1251//IGNORE',$str2))) return 0; else return -1;  //22000
    }

    static public function mb_array_search($needle, $haystack)
    { // работает только с одномерными массивами
        foreach ($haystack as $k => $v)
        {
            if (is_scalar($v) && Tools::mb_strcasecmp($v, $needle) === 0) return $k;
        }

        return false;
    }

    static public function mb_ucfirst($s)
    {
        return mb_strtoupper(mb_substr($s, 0, 1)) . mb_substr($s, 1);
    }

    static public function wlog($fname, $data)
    {
        $f = fopen(Tools::getLogPath() . $fname, 'a+');
        $s = date("Y-m-d H:i:s") . ' - ' . @$_SERVER['REMOTE_ADDR'] . ' - ' . @$_SERVER['HTTP_USER_AGENT'] . ' - ';
        if (is_array($data)) $s .= print_r($data, true) . "\n";
        else $s .= $data . "\n";
        fwrite($f, $s);
        fclose($f);
    }

    static public function getMicroTime()
    {
        //list($usec, $sec) = explode(" ", microtime());
        //return ((float)$usec + (float)$sec);
        return microtime(true);
    }

    //сливает непучиые элементы массива $a в строку через delimeter
    static public function uJoin($delimeter, $a)
    {
        $s = '';
        foreach ($a as $v)
        {
            if (!empty($v)) $s .= ($s != '' ? $delimeter : '') . $v;
        }

        return $s;
    }

    // вывод чисел с запятой а не с точкой
    static public function n($a)
    {
        return str_replace('.', ',', $a);
    }

    // вывод числа с разделением разрядов пробелом и десятичной запятой, нули после зяпятой не выводятся
    static public function nn($a)
    {
        $ex = explode('.', rtrim($a, '0'));
        $decimals = mb_strlen(@$ex[1]);

        return number_format($a, $decimals, ',', ' ');
    }

    // вывод числа с разделением разрядов пробелом и десятичной запятой, нули после зяпятой дополняются
    static public function nz($a, $decimals = 2, $decPoint = ',')
    {
        return number_format($a, $decimals, $decPoint, ' ');
    }

    static function roundUpTo($number, $increments)
    {
        $increments = 1 / $increments;

        return (round($number * $increments) / $increments);
    }

    static function DB_serialize($arr)
    {
        return base64_encode(@serialize($arr));
    }

    static function DB_unserialize($s)
    {
        return @unserialize(base64_decode($s));
    }

    /*
    * первый массив - ведущий. Т.е функция находит недостающие элементы из первом массе во втором массиве
    */
    static function arrayRecursiveDiff($aArray1, $aArray2)
    {
        $aReturn = [];

        foreach ($aArray1 as $mKey => $mValue)
        {
            if (array_key_exists($mKey, $aArray2))
            {
                if (is_array($mValue))
                {
                    $aRecursiveDiff = static::arrayRecursiveDiff($mValue, $aArray2[$mKey]);
                    if (count($aRecursiveDiff))
                    {
                        $aReturn[$mKey] = $aRecursiveDiff;
                    }
                }
                else
                {
                    if ($mValue != $aArray2[$mKey])
                    {
                        $aReturn[$mKey] = $mValue;
                    }
                }
            }
            else
            {
                $aReturn[$mKey] = $mValue;
            }
        }

        return $aReturn;
    }

    static function cutByWords($text, $maxLen)
    {
        $ex = preg_split("~([\.,\s\r\n]{1})~u", $text, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        $curLen = 0;
        $res = "";
        foreach ($ex as $k => &$v)
        {
            if ((($l = mb_strlen($v)) + $curLen) <= $maxLen) $res .= $v;
            $curLen += $l;
        }

        return trim($res, "\r\n ,");
    }

    static function paginator($baseUrl, $urlParam, $page, $num, $limit, $pageVar = 'page', $tpl = ['active'   => '',
                                                                                                   'noActive' => '',
                                                                                                   'dots'     => '',
    ], $itemNum = 15)
    {
        // Первая страница передается с номером 0, вторая  с номер два  и т.д
        // page - выбранная страница
        // limit - записей на странице
        // num - всего записей на всех страницах
        // itemNum - кол-во непрерывной нумерации
        $page = abs($page);
        $num = abs($num);
        $pages = ceil($num / $limit);
        if ($pages <= 1) return [];
        $q = Tools::arrayKeyDiff($urlParam, $pageVar);
        $r = [];
        $a = intval($itemNum / 2);
        $from = $page - $a;
        if ($from < 1)
        {
            $d = abs($from);
            $to = $page + $a + $d;
            $from = 1;
            if ($to > $pages) $to = $pages;
        }
        else
        {
            $from = $from + 1;
            $to = $page + $a - 1;
            if ($to > $pages)
            {
                $d = abs($pages - $to);
                $to = $pages;
                $from = $from - $d;
                if ($from < 1) $from = 1;
            }
        }
        if ($from > 1)
        {
            $r[] = str_replace('{url}', $baseUrl . Tools::imp($q), str_replace('{page}', 1, $tpl['noActive']));
            if ($from > 2) $r[] = $tpl['dots'];
        }
        for ($i = $from; $i <= $to; $i++)
        {
            $r[] = str_replace('{url}', $baseUrl . Tools::imp($i == 1 ? $q : array_merge($q, ["$pageVar" => $i])), str_replace('{page}', $i, $i == $page || ($i == 1 && $page == 0) ? $tpl['active'] : $tpl['noActive']));
        }
        if ($to < $pages)
        {
            if ($pages - $to > 1) $r[] = $tpl['dots'];
            $r[] = str_replace('{url}', $baseUrl . Tools::imp(array_merge($q, ["$pageVar" => $pages])), str_replace('{page}', $pages, $tpl['noActive']));
        }

        return $r;
    }

    static function randString($length)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $size = strlen($chars);
        $str = '';
        for ($i = 0; $i < $length; $i++)
        {
            $str .= $chars[mt_rand(0, $size - 1)];
        }

        return $str;
    }

    static function parse_utma($utma)
    {
        if (empty($utma)) return false;
        $c = explode('.', $utma);
        if (count($c) != 6) return false;
        $c[2] = date("Y/m/d (H:i)", $c[2]);
        $c[3] = date("Y/m/d (H:i)", $c[3]);
        $c[4] = date("Y/m/d (H:i)", $c[4]);

        return [
            'first'   => $c[2],
            'prev'    => $c[3],
            'current' => $c[4],
            'visits'  => $c[5],
        ];
    }

    static function memSizeConvert($size)
    {
        $unit = ['b', 'kb', 'mb', 'gb', 'tb', 'pb'];

        return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
    }

    static function memoryGetUsage()
    {
        //If its Windows
        //Tested on Win XP Pro SP2. Should work on Win 2003 Server too
        //Doesn't work for 2000
        //If you need it to work for 2000 look at http://us2.php.net/manual/en/function.memory-get-usage.php#54642
        if (substr(PHP_OS, 0, 3) == 'WIN')
        {
            if (substr(PHP_OS, 0, 3) == 'WIN')
            {
                $output = [];
                exec('tasklist /FI "PID eq ' . getmypid() . '" /FO LIST', $output);

                return preg_replace('/[\D]/', '', $output[5]) * 1024;
            }
        }
        else
        {
            //We now assume the OS is UNIX
            //Tested on Mac OS X 10.4.6 and Linux Red Hat Enterprise 4
            //This should work on most UNIX systems
            $pid = getmypid();
            exec("ps -eo%mem,rss,pid | grep $pid", $output);
            $output = explode("  ", $output[0]);

            //rss is given in 1024 byte units
            return $output[1] * 1024;
        }
    }

    static function formatSeconds($secondsLeft)
    {

        $minuteInSeconds = 60;
        $hourInSeconds = $minuteInSeconds * 60;
        $dayInSeconds = $hourInSeconds * 24;

        $days = floor($secondsLeft / $dayInSeconds);
        $secondsLeft = $secondsLeft % $dayInSeconds;

        $hours = floor($secondsLeft / $hourInSeconds);
        $secondsLeft = $secondsLeft % $hourInSeconds;

        $minutes = floor($secondsLeft / $minuteInSeconds);

        $seconds = $secondsLeft % $minuteInSeconds;

        $timeComponents = [];

        if ($days > 0)
        {
            $timeComponents[] = $days . " д" . ($days > 1 ? "сек" : "");
        }

        if ($hours > 0)
        {
            $timeComponents[] = $hours . " ч" . ($hours > 1 ? "сек" : "");
        }

        if ($minutes > 0)
        {
            $timeComponents[] = $minutes . " м" . ($minutes > 1 ? "сек" : "");
        }

        if ($seconds > 0)
        {
            $timeComponents[] = $seconds . " сек" . ($seconds > 1 ? "сек" : "");
        }

        return implode(' ', $timeComponents);
    }

    static function replaceMetaInCatalog($string)
    {
        return str_replace(['series', 'Series'], ['серии', 'Серии'], $string);
    }

    /**
     * Сортирует картинки по размеру. При ресайзе картинки необходима новая высота картинки в px
     *
     * @param mixed $ar_images - массив изображений из ф-ии Gallery->glist
     * @param int $new_height - высота картинки при ресайзе
     */
    static function sortImagesBySize($ar_images, $new_height = 0)
    {
        $result = $ar_images;
        if (!empty($ar_images))
        {
            $sizes_array = [];
            foreach ($ar_images as $id => $img_info)
            {
                @list($img_width, $img_height, $img_type, $img_attr) = getimagesize($img_info['img3']);
                $sizes_array[$id] = ($new_height > 0 && $img_height > 0) ? round(($img_width * $new_height) / $img_height) : $img_width;
            }
            asort($sizes_array, SORT_NUMERIC);
            unset($result);
            foreach ($sizes_array as $rid => $size)
            {
                $result[$rid] = $ar_images[$rid];
            }
        }

        return $result;
    }

    public static function xml_sformat($s)
    {
        $s = Tools::unesc(trim($s));
        $ss = Tools::html($s, false);
        if ($ss == $s) return $s;
        else
            return "<![CDATA[$s]]>";
    }

    public static function goodsTokensToArray($content)
    {
        if (!empty($content))
        {
            //preg_match_all("~\[\K[^[\]]++~", $content, $matches);
            preg_match_all("~\[[^[\]]++]~", $content, $matches);
            $arResult = [];

            if (!empty($matches[0]))
            {
                $matches = $matches[0];
                //$matches = array_unique($matches);

                foreach ($matches as $match)
                {
                    $matchSTR = str_replace(["[", "]"], "", $match);
                    $matchSTR = strip_tags($match);
                    $value = explode(',', $matchSTR);
                    $sortValue = [];

                    //sort($value);
                    foreach ($value as $key => $id)
                    {
                        $value[$key] = preg_replace("/[^a-zA-ZА-Яа-я0-9]/", "", $id);
                        $sortValue[$key]['type'] = strtolower(preg_replace("/[^a-zA-Z]/", "", $id));
                        $sortValue[$key]['value'] = preg_replace("/[^0-9]/", "", $value[$key]);
                    }


                    $output = [
                        'token' => $match,
                        'value' => $sortValue,
                    ];

                    $arResult[] = $output;
                }
            }

            return $arResult;
        }
    }

    /*
     * проверяет, создает и возвращает со слешом в конце путь на сервере до папки с логами
     */
    static public function getLogPath()
    {
        $path = rtrim(Cfg::get('log_path'), '/');
        Tools::tree_mkdir($path . '/');

        return is_dir($path) ? ($path . '/') : '';
    }


}