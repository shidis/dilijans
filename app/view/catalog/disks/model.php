<div class="box-shadow">
    <div class="box-padding">
        <h1 class="title cat"><?=$_title?></h1><?

        if(!empty($topText)){
            ?><div class="ctext"><?
                echo $topText;
            ?></div><?
        }

        ?><div class="img-product"><?
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
            if(!empty($img2)){
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

            }
        ?>
            <div class="articul" style="width: 185px;">
                <div>
                    <span class="l">Артикул модели:</span>
                    <span>№ M<?=$model_id?></span>
                </div>
            </div>
        </div><?

        ?><div class="box-right-product">

            <? if(!empty($bimg1)){?>
                <img style="margin-top: 15px;" src="<?=$bimg1?>" alt="<?=$bAnc?>" style="margin-bottom: 25px; max-width: 200px">
                <? } ?>

        </div>

        <div class="box-ov">

            <div class="line-dess">

                <li><i>Производитель</i><b><a href="<?=$burl?>" rel="nofollow"><?=$bname?></a></b></li>
                <li><i>Модель</i><b><?=trim($mname)?></b></li>
                <li><i>Тип диска</i><b><?=$dType?></b></li><?
                if(!empty($default_color)){
                    ?><li><i>Цвет</i><b><?
                            echo $default_color;
                            if(!empty($extcolor)) echo " ($extcolor)";
                        ?></b></li><?
                }?>


            </div>

            <? if(!empty($relModels)){?>

                <div class="mini-gallery">
                    <span>Цветовая гамма:</span>
                    <ul>

                        <? foreach($relModels as $v){?>

                            <li><a href="<?=$v['url']?>"><img src="<?=$v['img']?>" title="<?=$v['anc']?>"></a></li>

                            <? }?>

                    </ul>
                </div>

                <? }?>

        </div><?
        if(!empty($gallery) || !empty($video_link))
        {
            ?>
            <!--Табы-->
            <div class="tab-box clearfix" style="max-width: 427px;margin-left: 220px;">
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
                if (!empty ($video_link)) {
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
        <div class="links-p">
            <!--<a href="<?=$bcatUrl?>">Перейти в каталог <?=$bname?></a>-->
            <a href="javascript:goBack();">вернуться назад</a>
        </div>
        <div style="float: right; margin-top: -20px;">
            <script type="text/javascript" src="//yastatic.net/es5-shims/0.0.2/es5-shims.min.js" charset="utf-8"></script>
            <script type="text/javascript" src="//yastatic.net/share2/share.js" charset="utf-8"></script>
            <div class="ya-share2" data-services="vkontakte,facebook,odnoklassniki,gplus" data-counter=""></div>
        </div>
    </div>
</div>
<!--Табы-->
<div class="tabs-wrap clearfix">
    <ul class="tabs-nav clearfix">

        <? if(!empty($cat['gt0']) || !empty($cat[0])){?>

            <li class="active"><a href="#sizes"><u>Типоразмеры</u></a></li>

            <? }?>
        <?
        if(!empty($gallery))
        {
            ?>
            <!--<li><a href="#gallery"><img src="/app/images/gallery.png" style="float: left; border: 0px; margin: 7px 10px 0px 0px; width: 25px;" alt=""><u>Галерея</u>&nbsp;<span style="color: red;">(<?/*=count($gallery)*/?>)</span></a></li>-->
            <?
        }
        if(!empty($certificates))
        {
            ?>
            <li><a href="#cert"><img src="/app/images/sertifikat.png" style="float: left; border: 0px; margin: 5px 10px 0px 0px; width: 18px;" alt=""><u>Сертификаты</u>&nbsp;<span style="color: red;">(<?=count($certificates)?>)</span></a></li>
            <?}?>
    </ul>
    <div class="tabs-content clearfix active">
        <!--Табы-->
        <?    
        if(!empty($cat['gt0']) || !empty($cat[0])){
            ?>
            <!--Табы-->
            <div class="tab-box clearfix active">
                <!--Табы-->
                <?
                if(!empty($cat['gt0'])){
                    ?><div class="box-padding">
                        <h3 class="im-h"><img src="/app/images/img-nal.png" alt="">Позиции присутствующие на складе</h3>
                    </div>
                    <div class="nav-diametr">
                        <a href="#"<?=empty($diametr)?' class="active"':''?>>Все диаметры</a><?
                        foreach($rads as $rad=>$active){
                            ?><a r="<?=$rad?>" href="#"<?=$active?' class="active"':''?>>R<?=$rad?></a><?
                        }
                        ?>
                    </div>
                    <div class="table-list">
						<div class="table-responsive-pd">
							<table>
								<thead>
									<tr>
										<td width="65px"><b>Радиус</b></td>
										<td><b>Название</b></td>
										<td width="100px"><b>Цвет</b></td>
										<td width="65px"><b>Размер</b></td>
										<td width="60px"><b>PCD</b></td>
										<td width="60px"><b>Вылет</b></td>
										<td width="75px"><b>Диаметр</b></td>
										<td width="60px"><b>Цена</b></td>
										<td width="50px"><b>Склад</b></td>
										<td width="54px" style="text-align:center; padding-right:10px;"><img src="/app/images/icon-basket.png" alt=""></td>
									</tr>
								</thead><?

								foreach($cat['gt0'] as $rad=>$rows){
									?><tbody class="pd-l-td rad__<?=$rad?>">
										<tr class="title-r">
											<td colspan="10" class="rad">R<?=$rad?> "</td>
										</tr><?
										foreach($rows as $v){
											?><tr>
												<td colspan="2"><div class="dess"><a href="<?=$v['url']?>" class="h1"><?=$v['anc']?></a></div></td>
												<td><?=$v['colorUrl']?></td>
												<td><?=$v['razmer']?></td>
												<td><?=$v['sverlovka']?></td>
												<td><?=$v['ET']?></td>
												<td><?=$v['DIA']?></td>
												<td class="scl" cat_id="<?=$v['cat_id']?>"><b><?=$v['priceText']?></b></td>
												<td><?=$v['qtyText']?></td>
												<td><a href="#" class="buy-new tocart" pid="tqp<?=$v['cat_id']?>" id="tqp<?=$v['cat_id']?>" title="Добавить товар в корзину" maxQty="<?=$v['maxQty']?>" defQty="<?=$v['defQty']?>" cid="<?=$v['cat_id']?>">купить</a></td>
											</tr><?
										}
									?></tbody><?
								}
							?></table>
						</div>
					</div><?
                }
                if(!empty($cat[0])){

                    if(!empty($cat['gt0'])){?><a href="#" forid="gt0w" class="cut-ctrl">показать размеры временно отсустующие на складе</a> <div id="gt0w" class="cut-c"><? }

                        ?><div class="box-padding">
                            <noindex><h3 class="im-h"><img src="/app/images/img-nnal.png" alt="">Позиции отсутствующие на складе</h3></noindex>
                        </div>
                        <div class="table-list">
							<div class="table-responsive-pd">
								<table>
									<thead>
										<tr>
											<td width="85px"><b>Радиус</b></td>
											<td><b>Название</b></td>
											<td width="70px"><b>Цвет</b></td>
											<td width="65px"><b>Размер</b></td>
											<td width="60px"><b>PCD</b></td>
											<td width="60px"><b>Вылет</b></td>
											<td width="75px"><b>Диаметр</b></td>
											<td width="60px"><b>Цена</b></td>
											<td width="50px"><b>Склад</b></td>
											<td width="54px" style="text-align:center; padding-right:10px;"><img src="/app/images/icon-basket.png" alt=""></td>
										</tr>
									</thead><?

									foreach($cat[0] as $rad=>$rows){
										?><tbody class="prozr pd-l-td rad__<?=$rad?>">
											<tr class="title-r">
												<td colspan="10" class="rad">R<?=$rad?> "</td>
											</tr><?
											foreach($rows as $v){
												?><tr>
													<td colspan="2"><div class="dess"><a href="<?=$v['url']?>" class="h1"><?=$v['anc']?></a></div></td>
													<td><?=$v['colorUrl']?></td>
													<td><?=$v['razmer']?></td>
													<td><?=$v['sverlovka']?></td>
													<td><?=$v['ET']?></td>
													<td><?=$v['DIA']?></td>
													<td><b><?=$v['priceText']?></b></td>
													<td><?=$v['qtyText']?></td>
													<td><a href="#" class="buy tocart" pid="tqp<?=$v['cat_id']?>" id="tqp<?=$v['cat_id']?>" title="Добавить товар в корзину" maxQty="<?=$v['maxQty']?>" defQty="<?=$v['defQty']?>" cid="<?=$v['cat_id']?>" altt="<?=$v['altt']?>">купить</a></td>
												</tr><?
											}
										?></tbody><?
									}
								?></table>
							</div>
						</div><?

                        if(!empty($cat['gt0'])){?><p><a href="#" forid="gt0w" class="cut-ctrl-hide">скрыть размеры временно отсутствующие на складе</a></p></div><? }
                }
                ?>
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
    </div>
</div>
<?
if (!empty($mtext)){
    echo '<div class="ctext">';
    echo $mtext;
    echo '</div>';
}
?>