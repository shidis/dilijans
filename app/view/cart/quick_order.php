<div class="qord-form">
    <form id="res_but">
        <input type="hidden" name="defQty" value="<?= $defQty ?>">
        <input type="hidden" name="maxQty" value="<?= $maxQty ?>">
        <input type="hidden" name="price" value="<?= $price ?>">
        <input type="hidden" name="cid" value="<?= $cid ?>">

        <div>
            <img class="gimage" src="<?=$img?>" alt="<?=$tname?>" />
            <table class="goods">
                <tr>
                    <th>Наименование товара</th>
                    <th>Цена, руб.</th>
                    <th>Количество, шт.</th>
                </tr>
                <tr class="items">
                    <td style="text-align: left"><a href="<?=$url?>" class="naimenov"><?= $tname ?></a><!--<br><span class="desc"><?/*=(!empty($desc) ? $desc : '')*/?></span>--></td>
                    <td style="text-align: left"><?= $price ? "<span class=\"cena\">$price</span> руб." : "-" ?></td>
                    <td><div class="input-basket"><a href="#"></a><a href="#"></a><input name="am" type="text" value="<?=$defQty?>" p="<?=$price?>" minQty="1" maxQty="<?=$maxQty?>" cat_id="<?=$cat_id?>"></div></td>
                </tr>
                <tr>
                    <td colspan="3" class="price_cell">Итого к оплате: <span class="sum"><?= $sum ?></span> <span class="p_cur">руб.</span> (Без учета доставки)</td>
                </tr>
            </table>
        </div>
        <div class="row_message">
            <p>Пожалуйста, укажите Ваш номер телефона и имя. Наш специалист обязательно перезвонит для согласования времени и места доставки.</p>
        </div>
        <div class="form_fields_wrap">
            <div class="row">
                <div class="label">
                    Телефон:<sup>*</sup>
                </div>
                <div class="field">
                    <input type="tel" cname="quickOrderPhone" name="tel">
                </div>
            </div>
            <div class="row">
                <div class="label">
                    Ваше имя:<sup>*</sup>
                </div>
                <div class="field">
                    <input name="name" cname="quickOrderName" type="text"/>
                </div>
            </div>
            <div class="row">
                <div class="label">
                    Комментарий:
                </div>
                <div class="field">
                    <textarea cname="quickOrderComment" type="text" name="comment"></textarea>
                </div>
            </div>
            <div class="row">
                <div id="quickOrderSend"><span class="button">Отправить заказ<i></i></span></div>
            </div>
        </div>
    </form>
</div>