<div class="table-list new">
    <table>
        <tbody><?
        foreach($cat as $v){
            // Стикеры
            if (!empty($v['m_sticker'])){
                $sticker_img = '<div class="sticker_image_wrap cat" style="margin: -38px 0px 0px 32px;"><img style="width: 50px; height: 25px;" class="sticker_image" src="'.$v['m_sticker']['img'].'" alt="" />';
                if ($v['m_sticker']['allow_text'] && !empty($v['m_sticker']['sticker_text'])){
                    $sticker_img .= '<span class="sticker_image_text">'.$v['m_sticker']['sticker_text'].'</span>';
                }
                $sticker_img .= '</div>';
            }
            else $sticker_img = '';
            // *******
            ?>
            <tr class="replaceable_goods">
                <td class="lenta_image"><a class="retl" id="<?=$v['cat_id']?>"></a>
                    <?if (!empty($v['img2'])):?>
                        <a href="<?=$v['img2']?>" class="zoom" pdi="pdi<?=$v['cat_id']?>" maxQty="<?=$v['maxQty']?>" defQty="<?=$v['defQty']?>" cid="<?=$v['cat_id']?>" model_href="<?=$v['url']?>" model_name="<?=$v['bname']?> <?=$v['mname']?>">
                            <i></i>
                            <span>Увеличить</span>
                        </a>
                    <?endif;?>
                    <?=@$v['newTbl']?>
                    <div class="img">
                        <a href="<?=$v['url']?>" title="<?=$v['imgAlt']?>">
                            <img src="<?=$v['img1Blk']?>" alt="<?=$v['imgAlt']?>">
                        </a>
                        <div class="brand_img_lenta"><img src="<?=@$v['brand_img2']?>" alt="<?=$v['bname']?>" /></div>
                    </div>
                </td>
            <td class="lenta_desc">
                <div class="dess"><a href="<?=$v['url']?>"><?=$v['anc']?></a></div>
                <p><?=$v['razmer']?> <?=$v['sverlovka1']?> ET<?=$v['et']?> <?=$v['dia']?></p>
                <p style="height: 20px;">Цвет: <?=$v['colorUrl']?></p>
                <!--Отзывы-->
                <div class="lenta_article">Артикул: <span><?=$v['cat_id']?></span></div>
            </td>
            <td class="lenta_qyt">
                <div class="qtyLable">в наличии</div>
                <div class="qtyNum"><?=$v['qtyText']?>.</div>
                <div class="wrapCompare">
                    <label for="ch<?=$v['cat_id']?>" class="checkbox-02">
                        <input type="checkbox" id="ch<?=$v['cat_id']?>" cid="<?=$v['cat_id']?>" class="compare" gr="2" value="1"<?=@in_array($v['cat_id'],$cmpData['d'])?' checked':''?>>
                        <span><?=@in_array($v['cat_id'],$cmpData['d'])?'<a href="/compare/disks.html" target="_blank">в сравнении</a>':'сравнить'?></span>
                    </label>
                </div>
            </td>
            <td class="lenta_buy">
                <div id="pdi<?=$v['cat_id']?>" class="lenta_buy_wrap">
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