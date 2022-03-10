<div class="box-padding">
    <h1>Ваша корзина</h1><?

    if(!empty($topText)){
        ?><div class="ctext"><?
        echo $topText;
        ?></div><?
    }
    ?>
</div><?

if(count(@$cat)){?>

<div class="box-shadow box-shadow_no-mobile">
    <div class="box-padding main-cart">
        <div class="basket">
            <div></div>
            <table>
                <thead>
                <tr>
                    <td width="124px"><b>Фото</td>
                    <td><b>Наименование товара</td>
                    <td width="95px"><b>Цена</b>(руб.)</td>
                    <td width="110px"><b>Кол-во</b></td>
                    <td width="92px"><b>Сумма</b>(руб.)</td>
                    <td width="30px"><img src="/app/images/urna.png" alt=""></td>
                </tr>
                </thead>
                <tbody class="cart_tbl"><?
                foreach($cat as $k=>$v){
                ?>
                <tr>
                    <td>
                        <div class="img">
                            <a href="<?=$v['img2']?>" rel="zoom" title="<?=$v['fullName']?>"><img src="<?=$v['img1']?>" alt="<?=$v['fullName']?>" ></a>
                        </div>
                    </td>
                    <td>
                        <div class="dess">
                            <a href="<?=$v['url']?>"><?=$v['fullName']?></a>
                            <?if (!empty($v['sezIco'])):?><div style="float: left;margin: 0 15px 10px 0;"><?=@$v['sezIco']?></div><?endif;?>
							<?
                                $accessories = new Accessories();
                                $accessories->setMarkPointer('plus');
                                echo $accessories->getAccessoriesCheckboxes('', $v['gr'], $v["cat_id"]);
							?>
                            <?
                            if (!empty($ab->tree['vendor_id']))
                            {
                                $page_url_detail = $ab->tree['vendor_sname'];
                                if (!empty($ab->tree['model_sname'])) {
                                    $page_url_detail .= '--' . $ab->tree['model_sname'];
                                }
                                if (!empty($ab->tree['year_sname'])) {
                                    $page_url_detail .= '--' . $ab->tree['year_sname'];
                                }
                                if (!empty($ab->tree['modif_sname'])) {
                                    $page_url_detail .= '--' . $ab->tree['modif_sname'];
                                }
                                if (!empty($page_url_detail)) {
                                    $page_url = '/' . (($v['gr'] == 2) ? App_Route::_getUrl('avtoPodborShin') : App_Route::_getUrl('avtoPodborDiskov')) . '/' . $page_url_detail . '.html';
                                }
                                ?>

                                <div class="ab_link"><?
                                if ($v['gr'] == 1){
                                    echo '<a class="t_ab_link disk" href="'.$page_url.'?_p5['.$v['P1'].']=1">подобрать диски к этим шинам</a>';
                                }
                                else echo '<a class="t_ab_link tyers" href="'.$page_url.'?_p1['.$v['P5'].']=1">подобрать шины к этим дискам</a>';
                                ?></div><?
                            }
                            ?>
                        </div>
                    </td>
                    <td class="bascket__prise-item bascket__prise-item_right"><b><?=$v['price']?></b></td>
                    <td class="bascket__prise-item bascket__prise-item_center"><div class="input-basket"><a href="#"></a><a href="#"></a><input type="text" value="<?=$v['am']?>" minQty="<?=$v['minQty']?>" maxQty="<?=$v['sc']?>" cat_id="<?=$v['cat_id']?>"></div></td>
                    <td class="bascket__prise-item bascket__prise-item_left"><span class="bascket__prise-eq">=</span><b id="sum_<?=$v['cat_id']?>"><?=$v['sum']?></b></td>
                    <td><a href="#" class="del btn-del" cat_id="<?=$v['cat_id']?>" title="Удалить из корзины">Удалить из корзины</a></td>
                </tr>
                <? }?>
                </tbody>
            </table>
        </div>
        <div class="box-itogo">
            <a href="<?=$backUrl?>" class="back-p">Продолжить покупки</a>
            <p>
                <span>Итого к оплате: <b id="cartSum"><?=$b_sum?></b> (сумма без учета доставки)</span>
            </p>
        </div>
    </div>
</div>
<div class="box-bd-basket">
    <img src="/app/images/img-tex-06.jpg" alt="">
    <div>
        <?=$cartText?>
    </div>
</div>

<? if(!empty($warnText)){?>
    <div class="box-bd">
        <div>
            <?=$warnText?>
        </div>
    </div>
<? }?>

<div class="box-grey-01" id="cart_frm">
    <h5 class="h-form"><a href="#" class="btn-reset">очистить форму</a>Оформить заказ</h5>
    <form action="#" class="form-style-02" >
        <table>
            <tr>
                <td>
                    <p class="line">
                        <label for="ptype_0" class="radio-01"><input type="radio" id="ptype_0" value="0" name="ptype" checked>Физическое лицо</label>
                        <label for="ptype_1" class="radio-01"><input type="radio" id="ptype_1" value="1" name="ptype">Юридическое лицо</label>
                    </p>
                </td>
                <td rowspan="2" style="padding-left:40px; width:190px;">
                    <p class="line"><b>Обратите внимание!</b>Поля, помеченные звездочкой *, обязательны для заполнения.</p>
                    <p>Наш магазин обязуется сохранять конфиденциальность указанной Вами информации и не передавать ее третьим лицам. Вся информация, которую Вы укажете при регистрации, будет храниться в защищенной базе данных. Доступ к этой информации будут иметь только лица непосредственно работающие с Вашим заказом.</p>
                </td>
            </tr>
            <tr>
                <td>
                    <table>
                        <tbody id="ptype-fiz" class="of-show-group">
                            <tr>
                                <td width="170px">ФИО <sup>*</sup></td>
                                <td><div class="input" id="e_name_fiz"><input type="text" name="name_fiz"><img src="/app/images/uncorrect.png" alt=""><img src="/app/images/correct.png" alt=""></div></td>
                            </tr>
                        </tbody>

                        <tbody id="ptype-ur" class="of-hide-group" style="display: none">
                            <tr>
                                <td width="170px">Организация <sup>*</sup></td>
                                <td><div class="input" id="e_name_ur"><input type="text" name="name_ur"><img src="/app/images/uncorrect.png" alt=""><img src="/app/images/correct.png" alt=""></div></td>
                            </tr>

                            <tr>
                                <td>ИНН <sup>*</sup></td>
                                <td><div class="input" id="e_INN_ur"><input type="text" name="INN_ur"><img src="/app/images/uncorrect.png" alt=""><img src="/app/images/correct.png" alt=""></div></td>
                            </tr>

                            <tr>
                                <td>КПП</td>
                                <td><div class="input"><input type="text" name="KPP_ur"><img src="/app/images/uncorrect.png" alt=""><img src="/app/images/correct.png" alt=""></div></td>
                            </tr>

                            <tr>
                                <td>Банк</td>
                                <td><div class="input"><input type="text" name="bank_ur"><img src="/app/images/uncorrect.png" alt=""><img src="/app/images/correct.png" alt=""></div></td>
                            </tr>

                            <tr>
                                <td>БИК</td>
                                <td><div class="input"><input type="text" name="BIK_ur"><img src="/app/images/uncorrect.png" alt=""><img src="/app/images/correct.png" alt=""></div></td>
                            </tr>

                            <tr>
                                <td>Расчетный счет</td>
                                <td><div class="input"><input type="text" name="rs_ur"><img src="/app/images/uncorrect.png" alt=""><img src="/app/images/correct.png" alt=""></div></td>
                            </tr>

                            <tr>
                                <td>Кор. счет</td>
                                <td><div class="input"><input type="text" name="ks_ur"><img src="/app/images/uncorrect.png" alt=""><img src="/app/images/correct.png" alt=""></div></td>
                            </tr>

                            <tr>
                                <td>Юридический адрес</td>
                                <td><div class="input"><input type="text" name="u_addr_ur"><img src="/app/images/uncorrect.png" alt=""><img src="/app/images/correct.png" alt=""></div></td>
                            </tr>

                            <tr>
                                <td>Контактное лицо</td>
                                <td><div class="input"><input type="text" name="person_ur"><img src="/app/images/uncorrect.png" alt=""><img src="/app/images/correct.png" alt=""></div></td>
                            </tr>

                        </tbody>



                        <tr>
                            <td>Контактный телефон <sup>*</sup></td>
                            <td><div class="input" id="e_tel"><input type="text" name="tel"><img src="/app/images/uncorrect.png" alt=""><img src="/app/images/correct.png" alt=""></div></td>
                        </tr>
                        <tr>
                            <td>Адрес электронной почты</td>
                            <td>
                                <div class="input" id="e_email"><input type="text" name="email"><img src="/app/images/uncorrect.png" alt=""><img src="/app/images/correct.png" alt=""></div>
                                <label for="ch1" class="checkbox-01"><input type="checkbox" id="ch1" name="subscribe">подписаться на рассылку <br>проведения спец. акций</label>
                            </td>
                        </tr>
                        <tr>
                            <td>Город <sup>*</sup></td>
                            <td><div class="input" id="e_city"><input type="text" name="city"><img src="/app/images/uncorrect.png" alt=""><img src="/app/images/correct.png" alt=""></div></td>
                        </tr>
                        <tr>
                            <td>Адрес доставки <sup>*</sup></td>
                            <td>
                                <div class="textarea" id="e_addr"><textarea name="addr" id="" ></textarea><img src="/app/images/uncorrect.png" alt=""><img src="/app/images/correct.png" alt=""></div>
                            </td>
                        </tr>
                        <tr>
                            <td>Комментарий к заказу</td>
                            <td>
                                <div class="textarea"><textarea name="info" id="" ></textarea></div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>





            <tr class="last">
                <td colspan="2" style="padding-right:14px;"><input type="button" class="oform btn-order-send" value="Оформить"></td>
            </tr>
        </table>
    </form>
</div>
<? }else echo @$empty;