<?
if (!defined('true_enter')) die ("Direct access not allowed!");

class CC_inis
{

    private static $loaded = false;
    public static $in_arr = array();
    public static $is_arr = array();


    static function load()
    {
        if (CC_inis::$loaded) return true;
        CC_inis::$is_arr = array();
        CC_inis::$in_arr = array();
        if (!is_file(Cfg::_get('root_path') . '/' . Cfg::get('res_dir') . '/is.tbl')) return false;
        if (!is_file(Cfg::_get('root_path') . '/' . Cfg::get('res_dir') . '/in.tbl')) return false;
        $f = fopen(Cfg::_get('root_path') . '/' . Cfg::get('res_dir') . '/is.tbl', 'r');
        $r = fread($f, filesize(Cfg::_get('root_path') . '/' . Cfg::get('res_dir') . '/is.tbl'));
        CC_inis::$is_arr = unserialize($r);
        fclose($f);
        $f = fopen(Cfg::_get('root_path') . '/' . Cfg::get('res_dir') . '/in.tbl', 'r');
        $r = fread($f, filesize(Cfg::_get('root_path') . '/' . Cfg::get('res_dir') . '/in.tbl'));
        CC_inis::$in_arr = unserialize($r);
        fclose($f);
        CC_inis::$loaded = true;
        return true;
    }

    static function explainToArr($s)
    {
        list($in,$is) = CC_inis::splitINIS($s);
        if (!CC_inis::load()) return array('','');

        if (!empty($is) && isset(CC_inis::$is_arr[$is]))
            if ($is == 'Z' || $is == 'ZR') {
                $is_ = CC_inis::$is_arr[$is];
                $is = 'более ' . CC_inis::$is_arr[$is] . ' км/ч';
            } else {
                $is_ = CC_inis::$is_arr[$is];
                $is = 'до ' . CC_inis::$is_arr[$is] . ' км/ч';
            }

        if (!empty($in) && isset(CC_inis::$in_arr[$in])) {
            $in = CC_inis::$in_arr[$in];
        }

        return array('in' => @$in, 'is' => @$is, 'is_' => @$is_);
    }

    static function splitINIS($inis)
    {
        $inis=trim(Tools::toup($inis));
        $in = $is = '';
        if (preg_match("/^([0-9]+)\/[0-9]+[\s]*([A-Z]{1,2})$/u", $inis, $m)) {
            $in = $m[1];
            $is = $m[2];
        } elseif (preg_match("/^([0-9]+)[\s]*([A-Z]{1,2})$/u", $inis, $m)) {
            $in = $m[1];
            $is = $m[2];
        } elseif (preg_match("/^([A-Z]{1,2})$/u", $inis, $m)) {
            $is = $m[1];
        } elseif (preg_match("/^([0-9]+)$/u", $inis, $m)) {
           $in = $m[1];
        }
        return array($in, $is);
    }

    static function explainIN($in)
    {
        if (!CC_inis::load()) return false;
        return @CC_inis::$in_arr[(int)$in];
    }

    static function explainIS($is)
    {
        $is = trim(Tools::toup($is));
        if (!in_array($is, array('Z', 'ZR')))
            if (isset(CC_inis::$is_arr[$is])) return 'до ' . CC_inis::$is_arr[$is] . ' км/ч';
            else return '';
        else return 'более ' . CC_inis::$is_arr[$is] . ' км/ч';
    }

    static function check($s)
    {
        if (!CC_inis::load()) return false;
        $s = Tools::toup(trim($s));
        if (!preg_match("/^([0-9]+)\/([0-9]+)[\s]*([A-Z]{1,2})$/", $s, $m) || !isset(CC_inis::$in_arr[intval($m[1])]) || !isset(CC_inis::$in_arr[intval($m[2])]) || !isset(CC_inis::$is_arr[$m[3]]))
            if (!preg_match("/^([0-9]+)[\s]*([A-Z]{1,2})$/", $s, $m) || !isset(CC_inis::$in_arr[intval($m[1])]) || !isset(CC_inis::$is_arr[$m[2]]))
                if (!isset(CC_inis::$is_arr[$s]))
                    if (isset(CC_inis::$in_arr[$s])) return true;
                    else return false;
                else return true;
            else return true;
        else return true;
    }

    static function explain_1($s)
    {
        if (!CC_inis::load()) return false;
        $s = Tools::toup(trim($s));
        $rr = true;
        $r = '';
        if (preg_match("/^([0-9]+)\/([0-9]+)[\s]*([A-Z]{1,2})$/", $s, $m)) list($in1, $in2, $is) = array_slice($m, 1);
        elseif (preg_match("/^([0-9]+)[\s]*([A-Z]{1,2})$/", $s, $m)) list($in1, $is) = array_slice($m, 1);
        elseif (isset(CC_inis::$in_arr[$s])) $in1 = $s;
        elseif (isset(CC_inis::$is_arr[$s])) $is = $s;
        else $rr = false;

        if (!empty($is) && isset(CC_inis::$is_arr[$is]))
            if ($is == 'Z' || $is == 'ZR') $r .= 'скоростная шина. Допускает движение со скоростью более ' . CC_inis::$is_arr[$is] . ' км/ч';
            else $r .= 'шина допускает движение со скоростью, не превышающей ' . CC_inis::$is_arr[$is] . ' км/ч';

        if (!empty($in1) && isset(CC_inis::$in_arr[$in1])) {
            $r .= ($r != '' ? ' и ' : 'шина ') . 'может выдержать нагрузку до ' . CC_inis::$in_arr[$in1] . ' кг';
            if (!empty($in2) && isset(CC_inis::$in_arr[$in2])) $r .= ' в случае монтажа одинарных шин и до ' . CC_inis::$in_arr[$in2] . ' кг в случае сдвоенных';
        }
        $r .= '.';
        $r = ucfirst($r);
        // возврщает array(статус удачно расшифровки, ис/ин, текстовая расшифровка)
        return array($rr, $s, $r);
    }


    static function modelUpdate($model_id)
    {
        if (!@Cfg::get('INIS_S1S2')) return true;
        if (!isset(App_TFields::$fields['cc_model']['S1']) || !isset(App_TFields::$fields['cc_model']['S2'])) return false;
        if (!@$model_id) return false;
        $cc = new DB;
        $cc->query("SELECT P7 FROM cc_cat WHERE model_id='$model_id' AND gr=1 AND NOT LD ORDER BY P7");
        $s1 = $s2 = array();
        if ($cc->qnum()) while ($cc->next() !== false) {
            $v = Tools::unesc(trim($cc->qrow['P7']));
            if (preg_match("/^([0-9]+)\/[0-9]+[\s]*([A-Z]{1,2})$/", $v, $m)) {
                if (!in_array($m[1], $s1)) $s1[] = $m[1];
                if (!in_array($m[2], $s2)) $s2[] = $m[2];
            } elseif (preg_match("/^([0-9]+)[\s]*([A-Z]{1,2})$/", $v, $m)) {
                if (!in_array($m[1], $s1)) $s1[] = $m[1];
                if (!in_array($m[2], $s2)) $s2[] = $m[2];
            } elseif (preg_match("/^([A-Z]{1,2})$/", $v, $m)) {
                if (!in_array($m[1], $s2)) $s2[] = $m[1];
            } elseif (preg_match("/^([0-9]+)$/", $v, $m)) {
                if (!in_array($m[1], $s1)) $s1[] = $m[1];
            }
        }
        sort($s1);
        sort($s2);
        $s1 = implode(' ', $s1);
        $s2 = implode(' ', $s2);
        $cc->query("UPDATE cc_model SET S1='$s1', S2='$s2' WHERE model_id='$model_id'");
        unset($cc);
        return true;
    }


    static function getModelInis($model_id = 0, $qrow = array())
    {
        if (!@Cfg::get('INIS_S1S2')) return true;
        if (!CC_inis::load()) return false;
        if (!isset(App_TFields::$fields['cc_model']['S1']) || !isset(App_TFields::$fields['cc_model']['S2'])) return false;
        if ($model_id) {
            $cc = new CC_Base;
            $cc->que('model_by_id', $model_id);
            $s1 = Tools::unesc($cc->qrow['S1']);
            $s2 = Tools::unesc($cc->qrow['S2']);
        } else {
            $s1 = Tools::unesc($qrow['S1']);
            $s2 = Tools::unesc($qrow['S2']);
        }
        $r = array('in' => array(), 'is' => array());
        $x = explode(' ', $s1);
        foreach ($x as $v) {
            $v = intval($v);
            if ($v && isset(CC_inis::$in_arr[$v])) $r['in'][$v] = CC_inis::$in_arr[$v];
        }
        $x = explode(' ', $s2);
        foreach ($x as $v) {
            $v = trim($v);
            if ($v != '' && isset(CC_inis::$is_arr[$v])) $r['is'][$v] = ($v == 'Z' || $v == 'ZR' ? 'от ' : '') . CC_inis::$is_arr[$v];
        }
        asort($r['in']);
        asort($r['is']);
        if ($model_id) unset($cc);
        return $r;
    }



}
