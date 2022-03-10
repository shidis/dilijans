<?

class App_Cart_Controller extends App_Common_Controller
{

    function __construct()
    {
        $this->orders = new App_Orders();
    }

    public function index()
    {
        $this->view('cart/index');

        $this->title = $this->_title = 'Ваша корзина';
        $this->breadcrumbs['корзина покупок'] = '';

        Cart::make_back_url();
        $this->backUrl = Cart::$back_url_nosid;

        if (!Cart::is_empty()) {
            $i = 0;
            $this->cat = array();
            $c1 = $c2 = 0;
            foreach (Cart::$b_list as $k => $v) {
                $i++;
                $this->cc->que('cat_by_id', $k);
                $c = $this->cc->getOne();
                if ($c !== 0) {
                    // ДИСКИ
                    if ($c['gr'] == 2) {
                        $c2 += $v['amount'];
                        $razmer = "{$c['P2']}x{$c['P5']}";
                        $razmer .= ' ET' . $c['P1'];
                        if ($c['P4'] != '' && $c['P6'] != '') $razmer .= ' ' . "{$c['P4']}/{$c['P6']}";
                        if ($c['P3'] != '') $razmer .= " DIA {$c['P3']}";
                        $this->cat[] = array(
                            'i' => $i,
                            'gr' => $c['gr'],
                            'P1' => $c['P1'],
                            'P2' => $c['P2'],
                            'P3' => $c['P3'],
                            'P4' => $c['P4'],
                            'P5' => $c['P5'],
                            'P6' => $c['P6'],
                            'url' => '/' . App_Route::_getUrl('dTipo') . '/' . $c['sname'] . '.html',
                            'MP1' => $c['MP1'] == 1 ? 'кованый' : ($c['MP1'] == 2 ? 'литой' : ($c['MP1'] == 3 ? 'штампованный' : '')),
                            'img1' => $this->cc->make_img_path($c['img1']),
                            'img2' => $this->cc->make_img_path($c['img2']),
                            'img3' => $this->cc->make_img_path($c['img3']),
                            '_price' => $v['price'],
                            'price' => Tools::nn($v['price']) . ' р.',
                            'sum' => Tools::nn(round($v['amount'] * $v['price'])) . ' р.',
                            'sc' => $c['sc'],
                            'minQty'=>$this->minQty($c['P5'],2),
                            'color' => Tools::esc($c['csuffix'] != 'nocolor' ? $c['csuffix'] : ''),
                            'fullName' => Tools::esc('Диск ' . $c['bname'] . ' ' . $c['name'] . ' ' . $razmer . ' ' . Tools::esc($c['csuffix'] != 'nocolor' ? $c['csuffix'] : '')),
                            'cat_id' => $c['cat_id'],
                            'am' => $v['amount']
                        );

                        // ШИНЫ
                    } elseif ($c['gr'] == 1) {
                        $c1 += $v['amount'];
                        switch ($c['MP1']) {
                            case 1:
                                $sezIco = '<img src="/app/images/sun.png">';
                                break;
                            case 2:
                                $sezIco = '<img src="/app/images/snow.png">';
                                if ($c['MP3']) $sezIco .= '<img src="/app/images/ship.png">';
                                break;
                            case 3:
                                $sezIco = '<img src="/app/images/sunsnow.png">';
                                break;
                        }
                        $this->cat[] = array(
                            'i' => $i,
                            'gr' => $c['gr'],
                            'P1' => $c['P1'],
                            'P2' => $c['P2'],
                            'P3' => $c['P3'],
                            'P4' => $c['P4'],
                            'P5' => $c['P5'],
                            'P6' => $c['P6'],
                            'P7' => $c['P7'],
                            'MP1' => $c['MP1'],
                            'MP3' => $c['MP3'],
                            'sezIco' => $sezIco,
                            'url' => '/' . App_Route::_getUrl('tTipo') . '/' . $c['sname'] . '.html',
                            'img1' => $this->cc->make_img_path($c['img1']),
                            'img2' => $this->cc->make_img_path($c['img2']),
                            'img3' => $this->cc->make_img_path($c['img3']),
                            '_price' => $v['price'],
                            'price' => Tools::nn($v['price']) . ' р.',
                            'sum' => Tools::nn(round($v['amount'] * $v['price'])) . ' р.',
                            'sc' => $c['sc'],
                            'minQty'=>$this->minQty($c['P1'],1),
                            'suffix' => Tools::esc($c['csuffix'] != 'nocolor' ? $c['csuffix'] : ''),
                            'fullName' => Tools::esc('Шина ' . $c['bname'] . ' ' . $c['name'] . ' ' . $c['P3'] . '/' . $c['P2'] . ' ' . ($c['P6'] ? 'Z' : '') . 'R' . $c['P1'] . ' ' . $c['P7'] . ' ' . ($c['MP1'] == 1 ? 'летняя' : ($c['MP1'] == 2 ? 'зимняя' : ($c['MP1'] == 3 ? 'всесезонная' : ''))) . ' ' . ($c['MP3'] ? 'шипованая' : '')),
                            'cat_id' => $c['cat_id'],
                            'am' => $v['amount']
                        );
                    }
                }
            }
            unset($ct);

            $this->dops = array();
            $this->dopsSum = 0;
            if (!empty(Cart::$dop_list[0])) {
                foreach (Cart::$dop_list[0] as $k => $v) {
                    $this->dopsSum += $v['sum'];
                    $this->dops[$k] = $v;
                }
            }

            $this->deliveryCost = $this->orders->getDeliveryCost();
            $this->b_sum = Tools::nn(Cart::$asum) . ' руб.';
            $this->itog = Tools::nn(Cart::$asum + $this->deliveryCost) . ' руб.';

            $this->cartText = $this->parse($this->ss->getDoc('cart_text$6'));
            $this->warnText = $this->parse($this->ss->getDoc('cart_warning$6'));

            if ($c1 && $c2) GA::_event('Cart', 'viewCart', 'gr=12');
            elseif ($c1) GA::_event('Cart', 'viewCart', 'gr=1');
            elseif ($c2) GA::_event('Cart', 'viewCart', 'gr=2');

        }
        if (Cart::is_empty() || !count(@$this->cat)) {
            $this->empty = '<div class="box-no-nal">Ваша корзина пуста</div>';
            GA::_event('Cart', 'emptyCart', '', '', true);
        }

    }

    public function add()
    {
        usleep(500);
        $cat_id = (int)@$_REQUEST['cat_id'];
        $am = abs(@$_REQUEST['amount']);
        $arDop = json_decode(@$_REQUEST['dop'], true);
        if (!$am) $am = 4;


        if (!$cat_id) {
            $this->r['fres'] = false;
            $this->r['err_msg'] = 'Неверный идентификатор товара. Возможно это временная проблема на сервере. Будем вам признательны, если вы сообщите о проблеме нашему сотруднику.';
            return;
        } else {

            $this->cc->que('cat_by_id', $cat_id, 1);
            $d = $this->cc->getOne();

            if ($d !== 0) {

                if (Cfg::get('td_discount')) $dd = Data::get(($d['gr'] == 1 ? 't' : 'd') . '_discount'); else $dd = 0;
                if ($d['scprice']) $price = $d['scprice']; else $price = ceil($d['cprice'] - $d['cprice'] * $dd / 100);

                if (isset(Cart::$b_list[$cat_id])) {
                    $this->r['exist'] = 1;
                } else {
                    if ($d['sc'] < $am) $am = $d['sc'];
                    if ($am == 0) $am = 4;

                    if (!Cart::add_list($d['cat_id'], $am, $price)) {
                        $this->r['fres'] = false;
                        $this->r['err_msg'] = 'Ошибка при добавлении товара (1). Возможно это временная проблема на сервере. Будем вам признательны, если вы сообщите о проблеме нашему сотруднику.';
                        return;
                    }
                }

                foreach ($arDop as $dopID => $dops) {
                    Cart::$dop_list[$dopID] = $_SESSION['dop_list'][$dopID] = $dops;
                }

                $accessories = new Accessories();
                $accessories->setTitle('Дополнительно к дискам рекомендуем:');
                $this->r['accessoriesCheckboxes'] = $accessories->getAccessoriesCheckboxes('', $d['gr'], $cat_id);
				
				$this->r['lal'] = $d;

                if ($d['gr'] == 2) {
                    $razmer = "{$d['P2']}x{$d['P5']}";
                    $razmer .= ' ET' . $d['P1'];
                    if ($d['P4'] != '' && $d['P6'] != '') $razmer .= ' ' . "{$d['P4']}/{$d['P6']}";
                    if ($d['P3'] != '') $razmer .= " DIA {$d['P3']}";
                    $razmer .= ' ' . ($d['suffix']);
                    if (@$this->r['exist']) {
                        $this->r['fn'] = 'Диск <b style="font-size:1.1em">' . trim(Tools::html($d['bname'] . ' ' . $d['name'] . ' ' . $d['msuffix'] . ' ' . $razmer)) . '</b> был добавлен в корзину ранее.';
                    } else {
                        $this->r['fn'] = 'Диск <b style="font-size:1.1em">' . trim(Tools::html($d['bname'] . ' ' . $d['name'] . ' ' . $d['msuffix'] . ' ' . $razmer)) . '</b> добавлен в корзину.';
                    }
                    $this->r['fn_'] = trim(Tools::html($d['bname'] . ' ' . $d['name'] . ' ' . $d['msuffix'] . ' ' . $razmer));
                    $this->r['img1'] = $this->cc->makeImgPath($d['img1']);

                } else {
                    $razmer = $d['P3'] . '/' . $d['P2'] . ' ' . ($d['P6'] ? 'Z' : '') . 'R' . $d['P1'] . ' ' . $d['P7'];
                    $razmer .= ' ' . ($d['suffix']);
                    if (@$this->r['exist'])
                        $this->r['fn'] = 'Шина <b style="font-size:1.1em">' . trim(Tools::html($d['bname'] . ' ' . $d['name'] . ' ' . $d['msuffix'] . ' ' . $razmer)) . '</b> была добавлена в корзину ранее.';
                    else
                        $this->r['fn'] = 'Шина <b style="font-size:1.1em">' . trim(Tools::html($d['bname'] . ' ' . $d['name'] . ' ' . $d['msuffix'] . ' ' . $razmer)) . '</b> добавлена в корзину.';

                    $this->r['fn_'] = trim(Tools::cutDoubleSpaces(Tools::html($d['bname'] . ' ' . $d['name'] . ' ' . $d['msuffix'] . ' ' . $razmer)));
                    $this->r['img1'] = $this->cc->makeImgPath($d['img1']);

                }

                $this->r['b_count'] = Cart::$b_count;
                $this->r['bsum'] = Tools::nn(Cart::$b_sum) . ' руб.';
                $this->r['gr'] = $d['gr'];
                $this->r['price'] = $price;
            } else {
                $this->r['fres'] = false;
                $this->r['err_msg'] = 'Неверный идентификатор товара (2). Возможно это временная проблема на сервере. Будем вам признательны, если вы сообщите о проблеме нашему сотруднику.';
                return;
            }
        }
    }

    public function add2()
    {
        usleep(500);

        if (!is_array(@$_REQUEST['list']) || count(@$_REQUEST['list']) != 2) {
            $this->r['fres'] = false;
            $this->r['err_msg'] = 'Нет данных. Возможно это временная проблема на сервере. Будем вам признательны, если вы сообщите о проблеме нашему сотруднику.';
            return;
        } else {

            $fns = array();
            foreach ($_REQUEST['list'] as $i => $v) {

                $cat_id = (int)@$v['cat_id'];
                $am = abs(@$v['amount']);
                $this->cc->que('cat_by_id', $cat_id, 1);
                $d = $this->cc->getOne();

                if ($d !== 0) {

                    if (Cfg::get('td_discount')) $dd = Data::get(($d['gr'] == 1 ? 't' : 'd') . '_discount'); else $dd = 0;
                    if ($d['scprice']) $price = $d['scprice']; else $price = ceil($d['cprice'] - $d['cprice'] * $dd / 100);

                    if ($d['sc'] < $am) $am = $d['sc'];
                    if ($am == 0) $am = 4;

                    if (!Cart::add_list($d['cat_id'], $am, $price)) {
                        $this->r['fres'] = false;
                        $this->r['err_msg'] = 'Ошибка при добавлении товара (1). Возможно это временная проблема на сервере. Будем вам признательны, если вы сообщите о проблеме нашему сотруднику.';
                        return;
                    }

                    if ($d['gr'] == 2) {
                        $razmer = "{$d['P2']}x{$d['P5']}";
                        $razmer .= ' ET' . $d['P1'];
                        if ($d['P4'] != '' && $d['P6'] != '') $razmer .= ' ' . "{$d['P4']}/{$d['P6']}";
                        if ($d['P3'] != '') $razmer .= " DIA {$d['P3']}";
                        $fns[] = '<b style="font-size:1.1em">' . trim(Tools::html($d['bname'] . ' ' . $d['name'] . ' ' . $d['msuffix'] . ' ' . $razmer)) . '</b>';
                    } else {
                        $razmer = $d['P3'] . '/' . $d['P2'] . ' ' . ($d['P6'] ? 'Z' : '') . 'R' . $d['P1'] . ' ' . $d['P7'];
                        $fns[] = '<b style="font-size:1.1em">' . trim(Tools::html($d['bname'] . ' ' . $d['name'] . ' ' . $d['msuffix'] . ' ' . $razmer)) . '</b>';
                    }

                } else {
                    $this->r['fres'] = false;
                    $this->r['err_msg'] = 'Неверный идентификатор товара (2). Возможно это временная проблема на сервере. Будем вам признательны, если вы сообщите о проблеме нашему сотруднику.';
                    return;
                }
            }

            $this->r['fn'] = ($d['gr'] == 1 ? 'Разноразмерные шины' : 'Разноразмерные диски') . '<br>' . implode('<br>', $fns) . '<br>добавлены в корзину.';
            $this->r['fn_'] = '[SPARKA]';
            $this->r['gr'] = $d['gr'];
            $this->r['b_count'] = Cart::$b_count;
            $this->r['bsum'] = Tools::nn(Cart::$b_sum) . ' руб.';
        }
    }

    public function del()
    {
        $cat_id = (int)@$_REQUEST['cat_id'];
        Cart::del_list($cat_id);
        $this->r['summa'] = Tools::nn(Cart::$asum) . ' руб.';
        $this->r['count'] = Cart::$b_count;
        $this->r['dcost'] = Tools::nn($dcost = $this->orders->getDeliveryCost());
        $this->r['itog'] = Tools::nn(Cart::$asum + $dcost) . ' руб.';
    }

    public function clear()
    {
        Cart::clear();
    }

    public function changeAmount()
    {
        $cat_id = (int)@$_REQUEST['cat_id'];
        $am = (int)(@$_REQUEST['amount']);
        Cart::edit_list($cat_id, $am);
        $this->r['amount'] = @Cart::$b_list[$cat_id]['amount'];
        $this->r['itemSum'] = Tools::nn(round(@Cart::$b_list[$cat_id]['amount'] * @Cart::$b_list[$cat_id]['price'])) . ' р.';
        $this->r['summa'] = Tools::nn(Cart::$asum, true) . ' руб.';
        $this->r['dcost'] = Tools::nn($dcost = $this->orders->getDeliveryCost());
        $this->r['itog'] = Tools::nn(Cart::$asum + $dcost) . ' руб.';
    }

    function updateDopList()
    {
        $arDop = json_decode(@$_REQUEST['dop'], true);
        $catID = @$_REQUEST['cat_id'];

        if (!empty($arDop)) {
            foreach ($arDop as $dopID => $dops) {
                $_SESSION['dop_list'][$dopID] = $dops;
            }
        } else unset($_SESSION['dop_list'][$catID]);


        Cart::check_load();

        $this->r['summa'] = Tools::nn(Cart::$asum) . ' руб.';
        $this->r['dcost'] = Tools::nn($dcost = $this->orders->getDeliveryCost());
        $this->r['itog'] = Tools::nn(Cart::$asum + $dcost) . ' руб.';
    }

    function getBrandAccessories()
    {

        $cat_id = (int)@$_REQUEST['cat_id'];
        $group = @$_REQUEST['group'];

        if ($cat_id && $group) {
            $accessories = new Accessories();
            $accessories->setTitle('Дополнительно к дискам рекомендуем:');
            $this->r['accessoriesCheckboxes'] = $accessories->getAccessoriesCheckboxes('', $group, $cat_id);
        }
    }

    public function send()
    {
        $f = @$_REQUEST['f'];
        parse_str($f, $f);

        if (!Cart::$b_count) {
            $this->r['fres'] = false;
            $this->r['err_msg'] = "<p><b>НЕ ОТПРАВЛЕНО!</b>\r\n Пустая корзина. Что-то пошло не так. Пожалуйста, свяжитесь с нашими менеджерами и оформите заказ в телефонном режиме</p>";
            return;
        }

        if ($f['ptype'] == 0) {
            if (mb_strlen(trim($f["name_fiz"])) < 2) {
                $this->r["fres"] = false;
                $this->r["eid"] = 'e_name_fiz';
                return;
            }
        } else {
            if (mb_strlen(trim($f["name_ur"])) < 6) {
                $this->r["fres"] = false;
                $this->r["eid"] = 'e_name_ur';
                return;
            }
            if (mb_strlen(trim($f["INN_ur"])) < 2) {
                $this->r["fres"] = false;
                $this->r["eid"] = 'e_INN_ur';
                return;
            }
            if (mb_strlen(trim($f["email"])) < 2) {
                $this->r["fres"] = false;
                $this->r["eid"] = 'e_email';
                return;
            }
        }

        if (mb_strlen(trim($f["tel"])) < 5) {
            $this->r["fres"] = false;
            $this->r["eid"] = 'e_tel';
            return;
        }

        if (mb_strlen(trim($f["city"])) < 2) {
            $this->r["fres"] = false;
            $this->r["eid"] = 'e_city';
            return;
        }
        if (mb_strlen(trim($f["addr"])) < 2) {
            $this->r["fres"] = false;
            $this->r["eid"] = 'e_addr';
            return;
        }

        if (@$f['avto_name'] == '') $f['avto_name'] = $this->ab->fname;

        $rr = $this->orders->add_order($f);

        if (!Data::get('dont_clear_basket')) {
            Cart::clear();
            unset($_SESSION['deliveryCost']);
            unset($_SESSION['cartData']);
        }

        $this->r['GA_trans'] = $rr['GA_trans'];
        $this->r['GA_transErr'] = $rr['GA_transErr'];

        $this->r['html'] = '<div class="box-no-nal"><p>Заказ успешно отправлен на обработку.</p><p><strong>Номер вашего заказа: <font class="red">' . $rr['order_num'] . '.</font></strong></p><p>Благодарим Вас за оказанное доверие. Менеджер нашей компании свяжется с Вами в ближайшее время.<br>Спасибо за покупку!</p></div>';
    }

    public function quickOrderForm(){
        Request::ajaxMethod('html');

        $cid=(int)@$_REQUEST['cid'];
        $am=(int)@$_REQUEST['am'];

        $this->cc->que('cat_by_id',$cid,1);
        $d=$this->cc->getOne('');

        $this->cat_id = $d['cat_id'];

        if($d===0){
            $this->defQty=0;
            Log_Sys::put(SLOG_ERROR,"[App_Cart_Controller.quickOrderForm]:: товар не найден","cat_id={$cid}");
        }

        if(!$d['sc']) $this->defQty=0;
        elseif($d['sc']<4 && $am > $d['sc']) $this->defQty=$d['sc'];
        else $this->defQty=$am;

        $this->maxQty=$d['sc'];

        if($d['scprice'])
            $this->price=$d['scprice'];
        elseif($d['cprice'])
            $this->price=$d['cprice'];
        else
            $this->price=0;

        $this->sum = $this->defQty*$this->price;
        $this->pcd = (int)$d['P4'];

        if($d['gr']==1){
            $this->tname="Шины ".Tools::unesc("{$d['bname']} {$d['name']} {$d['P3']}/{$d['P2']} ".($d['P6']?"Z":'')."R{$d['P1']} {$d['P7']}");
            $this->desc = 'Сезон: ' . ($this->cc->qrow['MP1'] == 1 ? 'летние шины' : ($this->cc->qrow['MP1'] == 2 ? 'зимние шины' : 'всесезонные шины'));
            $this->desc .= $this->cc->qrow['MP3'] ? ', шипованные' : ', нешипованные';
        }else{
            if($d['replica'])
                $this->tname="Диски Replica ".Tools::unesc("{$d['name']} ({$d['bname']}) {$d['P2']}x{$d['P5']} {$d['P4']}/{$d['P6']} ET{$d['P1']}".($d['P3']?" DIA {$d['P3']}":''));
            else
                $this->tname="Диски ".Tools::unesc("{$d['bname']} {$d['name']} {$d['P2']}x{$d['P5']} {$d['P4']}/{$d['P6']} ET{$d['P1']}".($d['P3']?" DIA {$d['P3']}":''));
            // ***
            $suffix = Tools::unesc($d['suffix']);
            $sa=$this->cc->getSuffix12($suffix, $d['brand_id']);
            if(is_array($sa)) list($suffix1, $suffix2) = $sa; else list($suffix1, $suffix2) = [$suffix, ''];
            if (!empty($suffix1)) {
                $this->desc = 'Цвет: ' . $suffix1 . (!empty($suffix2) ? ' (' . $suffix2 . ')' : '');
            }
        }
        $this->img = $this->cc->make_img_path($d['img1']);
        $this->url = '/' . ($d['gr'] == 1 ? App_Route::_getUrl('tTipo') : App_Route::_getUrl('dTipo')) . '/' . $d['sname'] . '.html';
        $this->tel=Data::get('tel');
        $this->cid=$cid;
        $this->template('cart/quick_order');
    }

    public function quickOrderSend(){
        parse_str(@$_REQUEST['f'], $form_data);
        $rr = $this->orders->add_quick_order($form_data);
        if (!$rr) {
            $this->r['fres'] = false;
            $this->r['err_msg'] = "[Cart.quickOrderSend]: Ошибка создания заказа.";
        }
        else{
            $this->r['fres'] = true;
            $this->r['html'] = '<div class="box-no-nal"><p>Заказ успешно отправлен на обработку.</p><p><strong>Номер вашего заказа: <font class="red">' . $rr['order_num'] . '.</font></strong></p><p>Благодарим Вас за оказанное доверие. Менеджер нашей компании свяжется с Вами в ближайшее время.<br>Спасибо за покупку!</p></div>';
        }
    }

}