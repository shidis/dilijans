<div class="table-list new">
    <table>
        <tbody><?
        foreach($cat as $v){
            ?>
            <tr class="replaceable_goods">
            <td class="lenta_image" style="padding-bottom: 0px"><a class="retl" id="<?=$v['cat_id']?>"></a><?
                echo $v['sezIco'];
                echo $v['shipIco'];
                if(!empty($v['img1'])){
                    ?><div class="img lenta">
                    <a href="<?=$v['img2']?>" class="zoom" cprice="<?=Tools::toFloat($v['cprice'])?>" pdi="pdi<?=$v['cat_id']?>" maxQty="<?=$v['maxQty']?>" defQty="<?=$v['defQty']?>" cid="<?=$v['cat_id']?>" model_href="<?=$v['url']?>" model_name="<?=$v['bname']?> <?=$v['mname']?>">
                        <i></i>
                        <span>Увеличить</span>
                    </a>
                    <?=$v['newTbl']?>
                    <a href="<?=$v['url']?>" title="<?=$v['imgAlt']?>"><img src="<?=$v['img1']?>" alt="<?=$v['imgAlt']?>"></a></div>
                    <div class="brand_img_lenta" style="margin-top: 10px;"><img src="<?=@$v['brand_img2']?>" alt="<?=$v['bname']?>" /></div>
                    <?
                }?>
            </td>
            <td class="lenta_desc">
                <div class="dess"><a href="<?=$v['url']?>"><?=$v['anc']?></a></div>
                <p><?=$v['razmer']?>&nbsp;<span class="tip"><i><?=$v['inisUrl']?></i>&nbsp;&nbsp;<i><?=$v['suffixUrl']?></i></span></p>
                <p style="height: 20px;"><?=$v['suffixUrl']?></p>
                <!--Отзывы-->
                <div class="lenta_article">Артикул: <span><?=$v['cat_id']?></span></div>
            </td>
            <td class="lenta_qyt">
                <div class="qtyLable">в наличии</div>
                <div class="qtyNum"><?=$v['qtyText']?>.</div>
                <div class="wrapCompare">
                    <label for="ch<?=$v['cat_id']?>" class="checkbox-02">
                        <input type="checkbox" id="ch<?=$v['cat_id']?>" cid="<?=$v['cat_id']?>" class="compare" gr="1" value="1"<?=@in_array($v['cat_id'],$cmpData['d'])?' checked':''?>>
                        <span><?=@in_array($v['cat_id'],$cmpData['t'])?'<a href="/compare/tyres.html" target="_blank">в сравнении</a>':'сравнить'?></span>
                    </label>
                </div>
            </td>
            <td class="lenta_buy" id="pdi<?=$v['cat_id']?>">
                <div class="lenta_buy_wrap">
                    <?=$v['priceTextBlk']?>
                    <div class="buy-wrap">
                        <div class="input-basket"><a href="#"></a><a href="#"></a><input id="tqpid<?=$v['cat_id']?>" type="text" value="4" defQty="<?=$v['defQty']?>" cid="<?=$v['cat_id']?>" minQty="1" maxQty="<?=$v['maxQty']?>" cat_id="<?=$v['cat_id']?>"></div>
                        <a href="#" class="buy-new tocart" pid="tqpid<?=$v['cat_id']?>" title="Добавить товар в корзину">в корзину</a>
                    </div>
                    <div class="fast_order" pid="tqpid<?=$v['cat_id']?>">Быстрый заказ</div>
                </div>
            </td>
            </tr><?
        }?>
        </tbody>
    </table>
</div>