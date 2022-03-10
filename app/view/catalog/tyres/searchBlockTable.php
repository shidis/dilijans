<div class="goods-dubble">
    <ul><?
        foreach($cat as $v){
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
                    <?=$v['sezIcoBlk']?>
                    <a href="<?=$v['url']?>" class="h1">
                        <img src="<?=$v['img1Blk']?>" alt="<?=$v['imgAlt']?>">
                    </a>
                    <?
                    if (!empty($v['video_link'])){
                        ?>
                        <a class="video-call tyres fancybox.iframe" title="" rel="" href="<?=$v['video_link']?>?rel=0&amp;autoplay=1"><span>Смотреть видео</span></a>
                        <?
                    }
                    ?>
                </div>
                <div class="ba_desc">
                    <div class="brand_img"><img src="<?=@$v['brand_img2']?>" alt="<?=$v['bname']?>" /></div>
                    <div class="article_wrap"><span class="article"><b>АРТИКУЛ:</b> <?=$v['cat_id']?></span></div>
                </div>
                <div id="pdi<?=$v['cat_id']?>" class="des">
                    <div class="name">
                        <a href="<?=$v['url']?>" class="h1"><?=$v['bname']?></a>
                        <a href="<?=$v['url']?>" class="h1"><?=$v['mname']?></a>
                    </div>
                    <span class="tip"><?=$v['razmer']?> <i><?=$v['inisUrl']?></i>&nbsp;&nbsp;<i><?=$v['suffixUrl']?></i></span>
                    <?=$v['scText']?>
                    <!--<div class="code">Артикул: <?/*=$v['cat_id']*/?></div>-->
                    <?=$v['priceTextBlk']?>
                    <box class="ov">
                        <a href="#" class="buy-new tocart" pid="tqp<?=$v['cat_id']?>" id="tqp<?=$v['cat_id']?>" title="Добавить товар в корзину" maxQty="<?=$v['maxQty']?>" defQty="<?=$v['defQty']?>" cid="<?=$v['cat_id']?>">купить</a>
                        <label><input type="checkbox" cid="<?=$v['cat_id']?>" class="compare" gr="1" value="1"<?=@in_array($v['cat_id'],$cmpData['t'])?' checked':''?>>
                            <span><?=@in_array($v['cat_id'],$cmpData['t'])?'<a href="/compare/tyres.html" target="_blank">в сравнении</a>':'сравнить'?></span>
                        </label>
                    </box>
                </div>
            </li>
        <? }?>
    </ul>
</div>