<?
if (!defined('true_enter')) die ("Direct access not allowed!");

class App_Dostavka
{

    public static function getCost($gr, $p = array())
    {

        $duim = 25.4; // mm

        if (@$gr == 1 && @$p['w'] < 100) {
            $p['w'] = @$p['w'] * $duim;
            $p['h'] = @$p['h'] * $duim;
            $v = $p['w'] * $p['w'] * $p['h'] / 1000000000;
        } elseif (@$gr == 1) {
            $v = ((2 * @$p['w'] * @$p['h'] / 100 + @$p['d'] * $duim));
            $v = $v * $v * @$p['w'] / 1000000000;
        } else {
            $d = (@$p['d'] + 2) * $duim;
            $j = (@$p['j'] + 1) * $duim;
            $v = $d * $d * $j / 1000000000; //м
        }

        $db = new DB();
        $p['city_id'] = (int)@$p['city_id'];
        $q = $db->getOne("SELECT city,v1,v2,info FROM dostavka WHERE id='{$p['city_id']}'");
        if ($q !== 0) {
            $cost['city'] = Tools::unesc($q['city']);
            $cost['info'] = Tools::unesc($q['info']);
            $cost['volume1'] = $v;
            $c = $v * $q['v2'];
            if ($c < $q['v1']) $c = $q['v1'];
            $ccc = Data::get('region_delivery_cost_inc');
            $cost['cost1'] = round($c + (float)$ccc);
            $c = $v * $q['v2'] * @$p['am'];
            if ($c < $q['v1']) $c = $q['v1'];
            $cost['cost'] = round($c + (float)$ccc);
            $cost['am'] = @$p['am'];
            $cost['v1'] = $q['v1'];
            $cost['v2'] = $q['v2'];
        } else $cost = false;

        return $cost;
    }

    public static function cities()
    {
        $db = new DB();
        $d = $db->fetchAll("SELECT * FROM dostavka ORDER BY city", MYSQLI_ASSOC);
        $r = array();
        foreach ($d as $v)
            if ($v['city'] != 'Москва') {
                $v['city'] = Tools::unesc($v['city']);
                $r[$v['id']] = $v;
                unset($r[$v['id']]['id']);
            }
        return $r;
    }

}