<div class="box-padding">
    <div class="box-shadow box-shadow_no-mobile">
        <h1 class="title cat"><?=$_title?></h1><?

        if(!empty($topText)){
            ?><div class="ctext"><?
                echo $topText;
            ?></div><?
        }?>

        <div class="img-product">

            <? if(!empty($img2)){
                // Стикеры
                if (!empty($m_sticker['sticker_id'])){
                    $sticker_img = '<div class="sticker_image_wrap p_card"><img class="sticker_image" src="'.$m_sticker['img'].'" alt="" />';
                    if ($m_sticker['allow_text'] && !empty($m_sticker['sticker_text'])){
                        $sticker_img .= '<span class="sticker_image_text">'.$m_sticker['sticker_text'].'</span>';
                    }
                    $sticker_img .= '</div>';
                }
                else $sticker_img = '';
                // *******
                ?><div><?
                    if($new){ ?><b></b><? }
                    ?><a href="<?=$img2?>" rel="zoom" title="<?=$imgTitle?>"><img src="<?=$img1?>" alt="<?=$imgAlt?>"></a><?
                    echo $sticker_img;
                ?></div><?
            }else{
                ?><div><?
                    if($new){ ?><b></b><? }
                    ?><img src="<?=$noimg2m?>" alt="<?=$imgAlt?>"><?
                ?></div><?

            }?>

            <div class="articul">
                <div>
                    <span class="l">Артикул:</span>
                    <span>№ <?=$cat_id?></span>
					<?
						echo "<img src='$bimg1' alt='$brand_alt' class='only-mobile' />";
					?>
                </div>
                <div>* Для заказа по телефону нужен только артикул</div>
            </div>

        </div>
        <div style="float: right;">
            <table border="0">
                <?if (!empty($bimg1)):?>
                    <tr class="mobile-hide">
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
                                <span class="price-p scl" cat_id="<?=$cat_id?>"><div class="price_title">цена за 1 диск</div><?=$priceText?></span>
                                <table>
                                    <tr>
                                        <td width="35px" class="mobile-hide">Кол-<br>во:</td>
                                        <td><div class="input-basket2"><a href="#"></a><a href="#"></a><input id="tqp" type="text" value="<?=$defQty?>" minQty="<?=$minQty?>" maxQty="<?=$maxQty?>" cid="<?=$cat_id?>"></div></td>
                                    </tr>
                                </table>
                                <a href="#" class="buy tocart" pid="tqp" title="Добавить товар в корзину">купить<i></i></a>
                                <label for="ch1" class="checkbox-02 mobile-hide"><input type="checkbox" id="ch1" cid="<?=$cat_id?>" class="compare" gr="2" value="1"<?=@in_array($cat_id,$cmpData['d'])?' checked':''?>>
                                    <span class="compared"><?=@in_array($cat_id,$cmpData['d'])?'<a href="/compare/disks.html" target="_blank">в сравнении</a>':'сравнить'?></span>
                                </label>
                                <?else:?>
                                <span  class="nnal"><?=$scText?></span>
                                <?if(!empty($cat)):?><a href="#" rel="scrollto" data-target="analog" onclick="$('.tabs-nav li.analog').eq(0).trigger('click'); return false;" title="Перейти к аналогам" class="analog_button">Аналоги</a><?endif;?>
                            <a style="display: block; margin-top: 40px; margin-bottom: 20px;" id="show_order_form"><img src="/app/images/email-icon.png" style="height: 25px; float: left; margin-right: 7px;margin-top: 3px;" alt="Сообщить о поступлении" />Сообщить о поступлении</a>
                            <?endif;?>
                            <div class="box-yell-art" style="font-size: 0.8em">
                                * Все диски поставляются в коробках чтобы не повредить лакокрасочное покрытие
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="box-ov">
            <ul class="line-dess">
                <li><i>Производитель</i><b><a href="<?=$burl?>" rel="nofollow"><?=$bname?></a></b></li>
                <li><i>Модель</i><b><?=trim($mname)?></b></li><?
                if(!empty($color)){
                    ?><li><i>Цвет</i><b><?
                            echo $color;
                            if(!empty($colorExplain)) echo " ($colorExplain)";
                        ?></b></li><?
                }
                ?><li><i>Размер</i><b><?=$size?></b></li>
                <li><i>Сверловка (PCD)</i><b><?=$sverlovka?></b></li>
                <li><i>Вылет (ET)</i><b><?=$et?>&nbsp;мм</b></li>
                <li><i>Центральное отверстие (DIA)</i><b><?=$dia?>&nbsp;мм</b></li>
                <?if ($qty > 0):?><li><i>Кол-во на складе</i><b><?=$qtyText?></b></li><?endif;?>
            </ul><?

            if(!$qty){
                ?><noindex><div class="qty0 new"><div class="alert_img"></div><p class="t1">Товар отсутствует<? if(!empty($cat)):?> - аналоги <a href="#" rel="scrollto" data-target="analog">на этой странице ниже</a><?endif;?>.</p> </div></noindex><?
            }
            ?>

            <?
            $accessories = new Accessories();
            $accessories->setTitle('Так же c этими дисками можно докупить:');
            $accessories->setClassMain();
            echo $accessories->getAccessoriesCheckboxes($brand_id, 2, $cat_id)
            ?>
        </div>

        <?
        if(!empty($gallery) || !empty($video_link))
        {
        ?>
            <!--Табы-->
            <div class="tab-box clearfix bart-ph">
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
        <div class="box-cl"></div>

        <div class="links-p"><a href="javascript:goBack();">вернуться назад</a> </div>
        <div style="float: right; margin-top: -20px;">
            <script type="text/javascript" src="//yastatic.net/es5-shims/0.0.2/es5-shims.min.js" charset="utf-8"></script>
            <script type="text/javascript" src="//yastatic.net/share2/share.js" charset="utf-8"></script>
            <div class="ya-share2" data-services="vkontakte,facebook,odnoklassniki,gplus" data-counter=""></div>
        </div>
        <? if(trim(Tools::stripTags($mtext))!=''){?>

            <noindex>
                <div class="des-link">
                    <a href="#"><i>Описание диска <span><?=$bname.' '.$mname?></span></i></a>
                    <div class="ctext wrap"><?=$mtext?></div>
                </div>
            </noindex>

            <? }?>


        <div class="ctext">
            <?=$middleText?>

            <?=$adv_text?>
        </div>

    </div>

</div>
<div class="tabs-wrap clearfix">
    <ul class="tabs-nav clearfix">
        <?
        if(!empty($cat))
        {
            ?>
            <li class="rvws analog active"><a href="#analogs"><span><u>Аналоги</u></span></a></li>
            <?
        }
        if(!empty($gallery))
        {
            ?>
            <!--<li><a href="#gallery"><img src="/app/images/gallery.png" style="float: left; border: 0px; margin: 7px 10px 0px 0px; width: 25px;" alt=""><u>Галерея</u>&nbsp;<span style="color: red;">(<?/*=count($gallery)*/?>)</span></a></li>-->
            <?}
        if(!empty($certificates))
        {
            ?>
            <li><a href="#cert"><img src="/app/images/sertifikat.png" style="float: left; border: 0px; margin: 5px 10px 0px 0px; width: 18px;" alt=""><u>Сертификаты</u>&nbsp;<span style="color: red;">(<?=count($certificates)?>)</span></a></li>
        <?}?>
        <?if ($qty > 0):?><li class="rvws delivery"><a href="#delivery"><span><u>Доставка</u></span></a></li><?endif;?>
        <?if (!empty($suitable)):?><li class="rvws suitable"><a href="#suitable"><span><u>Применяемость</u></span></a></li><?endif;?>
    </ul>

    <div class="tabs-content clearfix">
        <? if(!empty($cat)){
            ?>
            <div class="tab-box analog clearfix active">
                <form method="get" data-container="analog">
                    <div class="title-punkt-01">
                        Подборка дисков с параметрами аналогичными:<span><?=$bname.' '.$mname.' '.$fullSize?></span><?
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
                                echo '<a '.((@Url::$sq['bid'] == $bid)?'class="active"':'').' r="'.$bid.'" href="#">'.$a_bname.'</a>';
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


                    <div class="box-shadow box-shadow_no-mobile">

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
            </div>
            <?
        }
    if(!empty($gallery))
    {
        ?>
        <!--Табы--
        <div class="tab-box clearfix">
            <?
            $i=0;
            ?><div class="box-preview-photo"><?
                foreach($gallery as $v)
                {
                    ?>
                    <a class="gallery-popup-call" title="<?=(!empty($v['text']) ? $v['text'] : 'Диски '.$bname.' '.$mname)?>" data-fancybox-group="thumb" rel="disk_gallery" href="<?=$v['img3']?>">
                        <img src="<?=$v['img3']?>" alt="<?=(!empty($v['text']) ? $v['text'] : 'Диски '.$bname.' '.$mname)?>" />
                    </a>
                    <?
                }
            ?></div>
        </div>
        -->
        <?
    }
    if(!empty($certificates))
    {
        ?>
        <!--Табы-->
        <div class="tab-box clearfix">
            <!--Табы-->
            <?
            $i=0;
            ?><div class="box-preview-photo"><?
                foreach($certificates as $v)
                {
                    ?>
                    <a class="cert-popup-call" title="<?=(!empty($v['text']) ? $v['text'] : 'Сертификат '.$bname.' '.$mname)?>" data-fancybox-group="thumb" rel="disk_cert" href="<?=$v['img3']?>">
                        <img src="<?=$v['img2']?>" alt="<?=(!empty($v['text']) ? $v['text'] : 'Сертификат '.$bname.' '.$mname)?>" />
                    </a>
                    <?
                }
            ?></div>
        </div>
        <?
    }?>
        <?if ($qty > 0):?>
        <div class="tab-box clearfix">
            <div class="box-padding">
                <div class="box-ov50">
                    <div class="box-dost-p"><img src="/app/images/cars.png" alt="">Доставка по Москве <?=$deliveryCost?> руб. <a href="/i/dostavka.html">подробнее</a></div>
                    <div class="box-ov">
                        <div class="box-calculation box-calculation--555">
                            <form id="rc-form">
                                <input type="hidden" name="w" value="<?=$width?>">
                                <input type="hidden" name="d" value="<?=$radius?>">
                                <input type="hidden" name="gr" value="2">
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
            </div>
            </div>
        <?endif;?>
        <?if (!empty($suitable)):?>
            <div class="tab-box suitable clearfix">
                <table class="suit_table">
                    <?
                    foreach ($suitable as $brand => $models)
                    {
                        echo '<tr><td class="suit_t_row"><span class="suit_title'.(@$ab->tree['vendor_name'] == $brand ? ' active' : '').'">'.$brand.'</span></td><td>';
                        foreach($models as $model=>$modifs)
                        {
                            echo '<b'.(@$ab->tree['model_name'] == $model ? ' class="active"' : '').'>'.$model.'</b>'.(@$ab->tree['model_name'] == $model ? ' ' : ': ');
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