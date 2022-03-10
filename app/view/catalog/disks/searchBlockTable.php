<style type="text/css">
    #content .goods-dubble li .img{
        height: 175px;
    }
    #content .goods-dubble li{
        padding-top: 210px;
    }
</style>

<div class="goods-dubble">
    <ul><?
        foreach($cat as $v){
            // Стикеры
            if (!empty($v['m_sticker'])){
                $sticker_img = '<div class="sticker_image_wrap cat"><img class="sticker_image" src="'.$v['m_sticker']['img'].'" alt="" />';
                if ($v['m_sticker']['allow_text'] && !empty($v['m_sticker']['sticker_text'])){
                    $sticker_img .= '<span class="sticker_image_text">'.$v['m_sticker']['sticker_text'].'</span>';
                }
                $sticker_img .= '</div>';
            }
            else $sticker_img = '';
            // *******
            ?>
            <li class="replaceable_goods" ><a class="retl" id="<?=$v['cat_id']?>"></a>
                <div class="img">
                    <?if (!empty($v['img2'])):?>
                        <a href="<?=$v['img2']?>" class="zoom" cprice="<?=Tools::toFloat($v['cprice'])?>" pdi="pdi<?=$v['cat_id']?>" maxQty="<?=$v['maxQty']?>" defQty="<?=$v['defQty']?>" cid="<?=$v['cat_id']?>" model_href="<?=$v['url']?>" model_name="<?=$v['bname']?> <?=$v['mname']?>">
                            <i></i>
                            <span>Увеличить</span>
                        </a>
                    <?endif;?>
                    <?=$v['newTbl']?>
                    <a href="<?=$v['url']?>" class="h1">
                        <img src="<?=$v['img1Blk']?>" alt="<?=$v['imgAlt']?>">
                    </a>
                </div>
                <?
                if (!empty($v['video_link'])){
                    ?>
                    <a class="video-call fancybox.iframe" title="" rel="" href="<?=$v['video_link']?>?rel=0&amp;autoplay=1"><span>Смотреть видео</span></a>
                    <?
                }
                ?>
                <div class="ba_desc" style="margin-top: -20px;">
                    <div class="brand_img"><img src="<?=@$v['brand_img2']?>" alt="<?=$v['bname']?>" /></div>
                    <div class="article_wrap"><span class="article"><b>АРТИКУЛ:</b> <?=$v['cat_id']?></span></div>
                </div>
                <?=$sticker_img?>
                <div id="pdi<?=$v['cat_id']?>" class="des" style="margin-top: 63px;">
                    <a href="<?=$v['url']?>" class="h1"><?=$v['ancBlk']?></a>
                    <div class="size">
                        <span class="tip"><?=$v['fullName']?></span>
                        <i><?=$v['colorUrl']?></i>
                    </div>
                    <?=$v['scText']?>
                    <!--<div class="code">Артикул: <?/*=$v['cat_id']*/?></div>-->
                    <?=$v['priceTextBlk']?>
                    <box class="ov">
                        <a href="#" class="buy-new tocart" pid="tqp<?=$v['cat_id']?>" id="tqp<?=$v['cat_id']?>" title="Добавить товар в корзину" maxQty="<?=$v['maxQty']?>" defQty="<?=$v['defQty']?>" cid="<?=$v['cat_id']?>">купить</a>
                        <label>
                            <input type="checkbox" cid="<?=$v['cat_id']?>" class="compare" gr="2" value="1"<?=@in_array($v['cat_id'],$cmpData['d'])?' checked':''?>>
                            <span><?=@in_array($v['cat_id'],$cmpData['d'])?'<a href="/compare/disks.html" target="_blank">в сравнении</a>':'сравнить'?></span>
                        </label>
                    </box>
                </div>
            </li>
        <? }?>
    </ul>
</div>