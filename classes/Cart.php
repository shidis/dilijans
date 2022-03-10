<?
if (!defined('true_enter')) die ("Direct access not allowed!");

/* Класс для работы с корзиной юзера с возможностью сохранения производьных доп данных*/

class Cart
{
    public static $b_count = 0; // кол-во пунктов в корзине
    public static $b_amount = 0; // общее кол-во товаров разного наменования  в корзине
    public static $b_sum = 0; // сумма корзины, только товары без скидок и допов, но с учетом красной цены и спецпредложений
    public static $b_list = array(); //array[$cat_id](count,price)
    public static $dop_list = array(); // допы  $dop_list[0] - допы без привязки к cat_id
    public static $dop_sum;   // сумма допов
    public static $asum;   // сумма допов и товаров dop_sum+b_sum
    public static $cleared;
    public static $back_url = '', $back_url_nosid = '';


    static function check_load()
    {
        if (Session::check()) {
            if (isset($_SESSION['unset_list'])) {
                unset($_SESSION['b_list']);
                unset($_SESSION['dop_list']);
                unset($_SESSION['unset_list']);
            } elseif(isset($_SESSION['b_list']) && is_array($_SESSION['b_list'])) {
                Cart::$b_list =  $_SESSION['b_list'];
                if(isset($_SESSION['dop_list']) && is_array($_SESSION['dop_list'])) Cart::$dop_list = $_SESSION['dop_list'];
            }
            $s = 0;
            $am = 0;
            $ds = 0;
            foreach (Cart::$b_list as $k => $v) {
                $s += $v['amount'] * $v['price'];
                $am += $v['amount'];
                if (isset(Cart::$dop_list[$k]))
                    foreach (Cart::$dop_list[$k] as $k1 => $v1)
                        $ds += $v1['sum'];
            }
            if(!empty(Cart::$dop_list[0])){
                foreach(Cart::$dop_list[0] as $v) $ds += $v['sum'];
            }
            Cart::$b_sum = $s;
            Cart::$b_count = count(Cart::$b_list);
            Cart::$b_amount = $am;
            Cart::$dop_sum = $ds;
            Cart::$asum = Cart::$b_sum + Cart::$dop_sum;
            if (@$_SESSION['back_url'] != '') Cart::$back_url = $_SESSION['back_url'];
            return true;
        } else return false;
    }

    static function is_empty()
    {
        if (Cart::$b_count == 0) return true; else return false;
    }

    static function add_list($id, $count, $value)
    {
        if (!intval($id)) return false;
        if(!Cart::check_load()) Session::start();
        $count = abs(intval($count));
        $id = intval($id);
        Cart::$b_list[$id] = array('amount' => abs(intval($count)), 'price' => abs(intval($value)));
        if (Cfg::get('auto_dop_including')) {
            $dop = new CC_DOP;
            Cart::$dop_list[$id] = array();
            foreach ($dop->related_cat($id) as $k => $v) {
                if (Cfg::get('dop_not_calc_amount')) {
                    Cart::$dop_list[$id][$v['dop_id']] = array('name' => Tools::unesc($v['name']), 'price' => $v['price'], 'amount' => 1, 'sum' => $v['price']);
                } else {
                    // кол-во допов будет таким же как и кол-во товаров  которому он отнесен
                    Cart::$dop_list[$id][$v['dop_id']] = array('name' => Tools::unesc($v['name']), 'price' => $v['price'], 'amount' => $count, 'sum' => $count * $v['price']);
                }
            }
            $_SESSION['dop_list'] = Cart::$dop_list;
            unset($dop);
        }
        $_SESSION['b_list'] = Cart::$b_list;
        Cart::check_load();
        return true;
    }

    static function addUncatDOP($name,$price,$amount=1)
    {
        if(empty($name)) return false;
        if(!Cart::check_load()) Session::start();
        $amount=abs(intval($amount));
        $price=(float)$price;
        Cart::$dop_list[0][]=array('name' => $name, 'price' => $price, 'amount' => $amount, 'sum' => $price*$amount);
        $_SESSION['dop_list'] = Cart::$dop_list;
        Cart::check_load();
        $a=Cart::$dop_list[0];
        end($a);
        return key($a); // возвращает ключ последнего элемента $dop_list[0]
    }

    static function editUncatDOP ($id, $name='', $price='', $amount='')
    {
        if (!Cart::check_load() || !isset(Cart::$dop_list[0][$id])) return false;
        if ($name !== '') Cart::$dop_list[0][$id]['name'] = $name;
        if ($amount !== '') Cart::$dop_list[0][$id]['amount'] = abs(intval($amount));
        if ($price !== '') Cart::$dop_list[0][$id]['price'] = abs(floatval($price));
        Cart::$dop_list[0][$id]['sum']=Cart::$dop_list[0][$id]['price']*Cart::$dop_list[0][$id]['amount'];
        $_SESSION['dop_list'] = Cart::$dop_list;
        Cart::check_load();
        return  Cart::$dop_list[0][$id];
    }

    static function edit_list($b_list_id, $count = '', $value = '')
    {
        if (!intval($b_list_id) || !isset(Cart::$b_list[$b_list_id]) || !Cart::check_load()) return false;
        if ($count !== '') Cart::$b_list[$b_list_id]['amount'] = abs(intval($count));
        if ($value !== '') Cart::$b_list[$b_list_id]['price'] = abs(intval($value));
        if (Cfg::get('auto_dop_including') && isset(Cart::$dop_list[$b_list_id])) {
            foreach (Cart::$dop_list[$b_list_id] as $k => $v) {
                if (Cfg::get('dop_not_calc_amount')) {
                    Cart::$dop_list[$b_list_id][$k]['amount'] = 1;
                    Cart::$dop_list[$b_list_id][$k]['sum'] = Cart::$dop_list[$b_list_id][$k]['price'];
                } else {
                    Cart::$dop_list[$b_list_id][$k]['amount'] = $count;
                    Cart::$dop_list[$b_list_id][$k]['sum'] = $count * Cart::$dop_list[$b_list_id][$k]['price'];
                }
            }
        }
        $_SESSION['b_list'] = Cart::$b_list;
        $_SESSION['dop_list'] = Cart::$dop_list;
        Cart::check_load();
        return true;
    }

    static function del_list($id)
    {
        if (!intval($id) || !Cart::check_load()) return false;
        if(Cart::check_load()) Session::start();
        unset(Cart::$b_list[$id]);
        unset(Cart::$dop_list[$id]);
        $_SESSION['b_list'] = Cart::$b_list;
        $_SESSION['dop_list'] = Cart::$dop_list;
        Cart::check_load();
    }

    function del_dop_list($id, $dop_id = 0)
    {
        if(!Cart::check_load()) return false;
        if ($dop_id) {
            unset(Cart::$dop_list[$id][$dop_id]);
        } else unset(Cart::$dop_list[$id]);
        $_SESSION['dop_list'] = Cart::$dop_list;
        Cart::check_load();
    }

    static function delUncatDOP($id)
    {
        if(!Cart::check_load()) return false;
        unset(Cart::$dop_list[0][$id]);
        $_SESSION['dop_list'] = Cart::$dop_list;
        Cart::check_load();
    }

    static function clear()
    {
        if(!Cart::check_load()) return false;
        $_SESSION['unset_list'] = '1';
        Cart::$cleared = true;
        Cart::$b_count = 0;
        Cart::$b_sum = 0;
        Cart::$b_amount = 0;
        Cart::$b_list = array();
        Cart::$dop_list = array();
        Cart::$dop_sum = 0;
        Cart::$asum = 0;
    }

    static private function trim_host($url)
    {
        if (preg_match("/([^\/]*)(\/.*)/", str_replace('http://', '', $url), $m) !== false) return $m[2]; else return "/";
    }

    static function make_back_url()
    {
        $s = str_replace('?' . Session::$name . '=' . Session::$sid, '', @$_SERVER['HTTP_REFERER']);
        $s = str_replace('&' . Session::$name . '=' . Session::$sid, '', $s);

        Cart::$back_url_nosid = Cart::$back_url = @Cart::trim_host($s);
        if (Session::$sid != '') Cart::$back_url .= (mb_strpos($s, '?') !== false ? '&' : '?') . Session::$name . '=' . Session::$sid;
        if (Cart::$back_url != '') {
            $_SESSION['back_url'] = Cart::$back_url;
            $_SESSION['back_url_nosid'] = Cart::$back_url_nosid;
        }
    }


}

?>
