<div class="box-padding">
    <div class="box-shadow">
        <h1 class="title cat"><?=$_title?></h1><?

        if(!empty($topText)){
            ?><div class="ctext"><?
                echo $topText;
            ?></div><?
        }?>

        <div class="img-product">

            <? if(!empty($img2)){
                ?><div><?
                    if($new){ ?><b></b><? }
                    ?><a href="<?=$img2?>" rel="zoom" title="<?=$imgTitle?>"><img src="<?=$img1?>" alt="<?=$imgAlt?>"></a><?
                ?></div><?
            }else{
                ?><div><?
                    if($new){ ?><b></b><? }
                    ?><img src="<?=$noimg1m?>" alt="<?=$imgAlt?>"><?
                ?></div><?

            }?>

            <div class="articul">
                <div>
                    <span class="l">Артикул:</span>
                    <span>№ <?=$cat_id?></span>
                </div>
                <div>* Для заказа по телефону нужен только артикул</div>
            </div>

        </div>
        <div style="float: right;">
            <table border="0">
                <?if (!empty($bimg1)):?>
                <tr>
                    <td style="padding-left: 30px;">
                        <div class="box-info-product-bimage">
                            <?
                            echo "<img src='$bimg1' alt='$brand_alt' />";
                            ?>
                        </div>
                    </td>
                </tr>
                <?endif;?>
                <tr>
                    <td>
                    <div class="box-info-product">
                        <?if ($qty > 0):?>
                            <span class="nal"><?=$scText?></span>
                            <span class="price-p scl" cat_id="<?=$cat_id?>"><div class="price_title">цена за 1 шину</div><?=$priceText?></span>
                            <table>
                                <tr>
                                    <td width="35px" class="mobile-hide">Кол-<br>во:</td>
                                    <td><div class="input-basket2"><a href="#"></a><a href="#"></a><input id="tqp" type="text" value="<?=$defQty?>" minQty="<?=$minQty?>" maxQty="<?=$maxQty?>" cid="<?=$cat_id?>"></div></td>
                                </tr>
                            </table>
                            <a href="#" class="buy tocart" pid="tqp" title="Добавить товар в корзину">купить<i></i></a>
                            <label for="ch1" class="checkbox-02 mobile-hide">
                                    <input type="checkbox" id="ch1" cid="<?=$cat_id?>" class="compare" gr="1" value="1"<?=@in_array($cat_id,$cmpData['t'])?' checked':''?>>
                                    <span class="compared"><?=@in_array($cat_id,$cmpData['t'])?'<a href="/compare/tyres.html" target="_blank">в сравнении</a>':'сравнить'?></span>
                            </label>
                            <?else:?>
                            <span class="nnal"><?=$scText?></span>
                            <?if(!empty($cat)):?><a href="#" rel="scrollto" data-target="analog" onclick="$('.tabs-nav li.analog').eq(0).trigger('click'); return false;" title="Перейти к аналогам" class="analog_button">Аналоги</a><?endif;?>
                        <a style="display: block; margin-top: 40px; margin-bottom: 20px;" id="show_order_form"><img src="/app/images/email-icon.png" style="height: 25px; float: left; margin-right: 7px;margin-top: 3px;" alt="Сообщить о поступлении" />Сообщить о поступлении</a>
                        <?endif;?>

                        <div class="box-yell-art">

                            <? if(@$rvws['mrate']>0){?>

                                <div class="mrate">
                                    <span>Рейтинг шины:</span>
                                    <ul class="stars stars-fix " v="<?=ceil($rvws['mrate'])?>"></ul>
                                </div>

                                <? }else{?>
                                Доставка по всей России
                                <? }?>

                        </div>
                    </div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="box-ov">
            <ul class="line-dess">
                <li><i>Производитель</i><b><a href="<?=$burl?>"><?=$bname?></a></b></b></li>
                <li><i>Модель</i><b><?=$mname.' '.$suffix?></b></li>
                <li><i>Размер</i><b><?=$size?></b></li>
                <li><i>Сезонность / шипы</i><b><?=$sezIco?></b></li>
                <li><i>Индекс нагрузки / индекс скорости</i><b><?=$inisUrl?></b></li>
                <?if ($qty > 0):?><li><i>Кол-во на складе</i><b><?=$qtyText?></b></li><?endif;?>
            </ul><?

            if(!$qty){
                ?><noindex><div class="qty0 new"><div class="alert_img"></div><p class="t1">Товар отсутствует<? if(!empty($cat)):?> - аналоги <a href="#" rel="scrollto" data-target="analog">на этой странице ниже</a><?endif;?>.</p> </div></noindex><?
            }
            ?>

            <?
            $accessories = new Accessories();
            $accessories->setTitle('Так же c этими шинам можно докупить:');
            $accessories->setClassMain();
            echo $accessories->getAccessoriesCheckboxes($brand_id, 1, $cat_id)
            ?>
        </div>
        <?
        if(!empty($gallery) || !empty($video_link))
        {
            ?>
            <!--Табы-->
            <div class="tab-box clearfix" style="max-width: 475px;margin-left: 220px;">
                <!--Табы-->
                <?
                if (!empty($gallery)) {
                    $i=0;
                    ?><div class="box-preview-photo top"><?
                    foreach($gallery as $v)
                    {
                        ?>
                        <a class="gallery-popup-call" title="<?=(!empty($v['text']) ? $v['text'] : 'Диски '.$bname.' '.$mname)?>" data-fancybox-group="thumb" rel="disk_gallery" href="<?=$v['img3']?>">
                            <img src="<?=$v['img3']?>" alt="<?=(!empty($v['text']) ? $v['text'] : 'Диски '.$bname.' '.$mname)?>" />
                        </a>
                        <?
                    }
                    ?></div><?
                }?>

                <?
                if (!empty($video_link)) {
                    ?>
                    <div class="box-preview-video">
                        <a class="video-call catalog fancybox.iframe" href="<?=$video_link?>?rel=0&amp;autoplay=1"></a>
                    </div>
                    <?
                }
                ?>
            </div>
            <?
        }
        ?>
        <div class="box-cl">
            <div style="float: right; margin-top: 3px;">
                <script type="text/javascript" src="//yastatic.net/es5-shims/0.0.2/es5-shims.min.js" charset="utf-8"></script>
                <script type="text/javascript" src="//yastatic.net/share2/share.js" charset="utf-8"></script>
                <div class="ya-share2" data-services="vkontakte,facebook,odnoklassniki,gplus" data-counter=""></div>
            </div>
        </div>
            <div class="tabs-wrap clearfix">
                <noindex>
                <ul class="tabs-nav clearfix">
                    <?if(!empty($cat)):?><li class="rvws analog active"><a href="#analogs"><span><u>Аналоги</u></span></a></li><?endif;?>
                    <li class="rvws"><a href="#descr"><u>Описание</u></a></li>
                    <?if ($qty > 0):?><li class="rvws delivery"><a href="#delivery"><span><u>Доставка</u></span></a></li><?endif;?>
                    <li class="rvws"><a href="#reviews" class="rvws-tab-nav"><span><u>Отзывы</u></span> <i class="rvws-num">(<?=@$rvws['total']?>)</i></a></li>
                    <?if(!empty($gallery))
                    {
                        ?>
                        <!--<li><a href="#gallery"><img src="/app/images/gallery.png" style="float: left; border: 0px; margin: 7px 10px 0px 0px; width: 25px;" alt=""><u>Галерея</u>&nbsp;<span style="color: red;">(<?/*=count($gallery)*/?>)</span></a></li>-->
                        <?}?>
                    <?if (!empty($suitable)):?><li class="rvws suitable"><a href="#suitable"><span><u>Применяемость</u></span></a></li><?endif;?>
                </ul>
                </noindex>
                <div class="tabs-content clearfix">
                    <? if(!empty($cat)){ ?>
                        <div class="tab-box analog clearfix active">
                            <noindex>
                            <form method="get" data-container="analog">
                                <div class="title-punkt-01">
                                    Подборка шин с параметрами аналогичными:<span><?=$bname.' '.$mname.' '.$fullSize?></span><?
                                    if(@$alttByBrandSwitcher){
                                    ?><!--<p><input type="checkbox" name="bid" value="<?=$brand_id?>"<?=!empty(Url::$sq['bid'])?' checked':''?> id="altt-bybrand"><label for="altt-bybrand">&nbsp;из списка выбрать только шины <?=$bname?></label></p>--><?
                                    }
                                    // Фильтр по брендам
                                    if (!empty($alt_brands))
                                    {
                                        echo '<input type="hidden" name="bid" value="'.((@Url::$sq['bid'])?Url::$sq['bid']:'-1').'"  id="alt_brand" />';
                                        echo '<input type="hidden" name="target_url" value="'.(@$self_url).'"  id="target_url" />';
                                        echo '<div class="alt-brands"><a '.((@Url::$sq['bid'])?'':'class="active"').' r="-1" href="#">Все</a>';
                                        foreach ($alt_brands as $bid=>$a_bname)
                                        {
                                            if ($bid==$brand_id) {
                                                echo '<a '.((@Url::$sq['bid'] == $bid)?'class="active"':'').' r="'.$bid.'" href="#">'.$a_bname.'</a>';
                                                unset($alt_brands[$brand_id]);
                                            }
                                        }
                                        foreach ($alt_brands as $bid2=>$a_bname2) {
                                            echo '<a '.((@Url::$sq['bid'] == $bid2)?'class="active"':'').' r="'.$bid2.'" href="#">'.$a_bname2.'</a>';
                                        }
                                        echo '</div>';
                                    }
                                    // ***
                                    ?></div>
                            </form>
                            <div class="replaceable_content" num="<?=$num?>">
                                <div class="box-padding">
                                    <div class="box-rez">

                                        <? if(!empty($paginator)){
                                            ?><div class="paginator" style="float: left"><?
                                                ?><ul><?
                                                    foreach($paginator as $v) echo $v;
                                                ?></ul><?
                                            ?></div><?
                                        }?>

                                        <div class="vids">

                                            <? foreach($altViewMode as $v){
                                                ?><a href="#" class="<?=$v?>"></a><?
                                            }?>

                                        </div>
                                    </div>

                                </div>


                                <div class="">

                                    <?=$this->incView($altTpl)?>

                                    <div class="box-padding">

                                        <? if(!empty($limit) && !empty($paginator)){
                                            ?><div class="showmore" onclick="showmore()" id="showmore" style="padding-bottom: 15px;"><div id="showmore_ajaxloading"></div><a>Показать еще</a></div><?
                                            ?><script>
                                                var cur_limit = <?=$limit?>;
                                                var lockPage = false;
                                            </script><?
                                        }?>

                                        <div class="vids">

                                            <? foreach($altViewMode as $v){
                                                ?><a href="#" class="<?=$v?>"></a><?
                                            }?>

                                        </div>

                                        <? if(!empty($paginator)){
                                            ?><div class="paginator" style="float: left"><?
                                                ?><ul><?
                                                    foreach($paginator as $v) echo $v;
                                                ?></ul><?
                                            ?></div><?
                                        }?>

                                    </div>
                                </div>
                            </div>
                                </noindex>
                        </div>
                        <?
                    }
                    ?>
                    <div class="tab-box clearfix">
                        <?
                        if(!empty($adv_text))
                        {
                            echo '<div class="typo_adv_text">'.$adv_text.'</div>';
                        }
                        ?>
                        <noindex>
                        <div class="ctext model-mtext" itemprop="description"><?
                            if(!empty($bimg1)) {
                                ?><img src="<?= $bimg1 ?>" alt="<?= @$bAnc ?>" class="model-blogo"><?
                            }
                            if(!empty($mtext)){
                                if(count($mtext)==1) echo $mtext[0];
                                else {
                                    echo $mtext[0];
                                    ?><div class="cut-c" id="cutm1"><?=$mtext[1]?></div><p class="cut-ctrl" forid="cutm1"><a href="#">показать полное описание</a> </p><?
                                }
                            }?>
                        </div>
                            </noindex>
                    </div>
                    <div class="tab-box delivery clearfix">
                        <?if ($qty > 0):?>
                            <div class="box-ov20">
                                <div class="box-dost-p"><img src="/app/images/cars.png" alt="">Доставка по Москве <?=$deliveryCost?> руб. <a href="/i/dostavka.html">подробнее</a></div>
                                <div class="box-ov">
                                    <div class="box-calculation">
                                        <form id="rc-form">
                                            <input type="hidden" name="w" value="<?=$width?>">
                                            <input type="hidden" name="h" value="<?=$height?>">
                                            <input type="hidden" name="d" value="<?=$radius?>">
                                            <input type="hidden" name="gr" value="1">
                                            <img class="imap" src="/app/images/map-calc.png" alt="">
                                            <b>Расчет доставки через ТК ЖелдорЭкспедиция</b>
                                            <div class="inp">
                                                <div class="select-04">
                                                    <span></span>
                                                    <select id="rc_cityId"><?
                                                        foreach($cities as $k=>$v){
                                                            ?><option value="<?=$k?>"<?=$k==$cityId?' selected':''?>><?=$v['city']?></option><?
                                                        }
                                                        ?>

                                                    </select>
                                                </div>
                                                <input type="button" id="rc-btn" value="Рассчитать">
                                            </div>
                                            <div class="ctext" id="rc-result"></div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?endif;?>
                    </div>
                    <div class="tab-box clearfix">
                        <noindex>
                        <div class="rvws-c"></div>
                            </noindex>
                    </div>
                    <?if(!empty($gallery))
                    {
                        ?>
                        <!--Табы--
                        <div class="tab-box clearfix">
                            <noindex>
                            <?
                            $i=0;
                            ?><div class="box-preview-photo"><?
                                foreach($gallery as $v)
                                {
                                    ?>
                                    <a class="gallery-popup-call" title="<?=(!empty($v['text']) ? $v['text'] : 'Шины '.$bname.' '.$mname)?>" data-fancybox-group="thumb" rel="tyres_gallery" href="<?=$v['img3']?>">
                                        <img src="<?=$v['img3']?>" alt="<?=(!empty($v['text']) ? $v['text'] : 'Шины '.$bname.' '.$mname)?>" />
                                    </a>
                                    <?
                                }   
                            ?></div>
                            </noindex>
                        </div>
                        -->
                        <?
                    }?>
                    <?if (!empty($suitable)):?>
                        <div class="tab-box suitable clearfix">
                            <table class="suit_table">
                                <?
                                foreach ($suitable as $brand => $models)
                                {
                                    echo '<tr><td class="suit_t_row"><span class="suit_title'.(@$ab->tree['vendor_name'] == $brand ? ' active' : '').'">'.$brand.'</span></td><td>';
                                    foreach($models as $model=>$modifs)
                                    {
                                        echo '<b'.(@$ab->tree['model_name'] == $model ? ' class="active"' : '').'>'.$model.'</b>: ';
                                        $modifs_names = Array();
                                        foreach($modifs as $modif=>$years) // Можно дальше пройтись по годам
                                        {
                                            $modifs_names[] = (@$ab->tree['modif_name'] == $modif && @$ab->tree['model_name'] == $model ? '<span class="active">'.$modif.'</span>' : $modif);
                                        }
                                        echo implode(', ', $modifs_names).'; ';
                                    }
                                    echo '</td></tr>';
                                }
                                ?>
                            </table>
                        </div>
                    <?endif;?>
                </div>
            </div>
    </div>

</div>


<? if(!empty($seoLinks)){

    ?><div class="box-padding ctext" style="margin-top: 30px"><?
        ?><h5>Другие тематические разделы</h5><?

        ?><ul><?
            foreach($seoLinks as $v){
                ?><li><a href="<?=$v['url']?>" title="<?=$v['title']?>"><?=$v['anc']?></a></li><?
            }
        ?></ul><?
    ?></div><?
}
