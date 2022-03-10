<div class="box-shadow" itemscope itemtype="http://schema.org/Product">
    <meta itemprop="url" content="<?=$modelUrl?>">
    <meta itemprop="name" content="<?=$bname.' '.$mname?>">
    <div class="box-padding">
        <h1 class="title cat" itemprop="name"><?=$_title?></h1><?

        if(!empty($topText)){
            ?><div class="ctext"><?
                echo $topText;
            ?></div><?
        }

        ?><div class="img-product"><?
            if(!empty($img2)){
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
            <div class="articul" style="width: 185px;">
                <div>
                    <span class="l">Артикул модели:</span>
                    <span>№ M<?=$model_id?></span>
                </div>
            </div>
        </div>

        <div class="box-right-product">

            <? if(!empty($bimg1)){?>

                <img src="<?=$bimg1?>" alt="<?=$bAnc?>" style="margin-bottom: 5px; max-width: 200px">

                <? }

            if($classId==1){?>

                <img src="/app/images/our-choice.png" style="box-shadow: -10px 10px 30px rgba(0,0,0,0.1); margin-bottom: 30px; border-radius: 10px; ">

                <? }?>


        </div>

        <div class="box-ov">

            <div class="line-dess">

                <li><i>Производитель</i><b><a href="<?=$burl?>" rel="nofollow"><?=$bname?></a></b></b></li>
                <li><i>Модель</i><b><?=$mname.' '.$msuffix?></b></li>
                <li><i>Сезонность</i><b><?=$sezon.' '.$sezIco?></b></li>

                <? if($sezonId==2){?>

                    <li><i>Шипы</i><b><?=$shipId?'есть':'нет'?> <?=$shipIco?></b></li>

                    <? }?>
                <?
                /*Отзывы*/
                if (@$rvws['mrate']>0){
                    ?>
                    <li>
                        <i>Рейтинг шины:</i>
                        <b class="mrate">
                            <ul class="stars stars-fix " v="<?=ceil($rvws['mrate'])?>"></ul>
                        </b>
                    </li>
                    <?
                }
                /********/
                ?>
            </div>
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
                            <a class="gallery-popup-call" title="<?=(!empty($v['text']) ? $v['text'] : 'Шины '.$bname.' '.$mname)?>" data-fancybox-group="thumb" rel="tyres_gallery" href="<?=$v['img3']?>">
                                <img src="<?=$v['img3']?>" alt="<?=(!empty($v['text']) ? $v['text'] : 'Шины '.$bname.' '.$mname)?>" />
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
        }?>
        <div class="links-p clearfix">
            <a href="javascript:goBack();">вернуться назад</a>
        </div>
        <div style="float: right; margin-top: -20px;">
            <script type="text/javascript" src="//yastatic.net/es5-shims/0.0.2/es5-shims.min.js" charset="utf-8"></script>
            <script type="text/javascript" src="//yastatic.net/share2/share.js" charset="utf-8"></script>
            <div class="ya-share2" data-services="vkontakte,facebook,odnoklassniki,gplus" data-counter=""></div>
        </div>
        <div class="tabs-wrap clearfix">
            <ul class="tabs-nav clearfix">

                <? if(!empty($cat['gt0']) || !empty($cat[0])){?>

                    <li class="active"><a href="#sizes"><u>Типоразмеры</u></a></li>

                    <? }?>

                <li class="active"><a href="#descr"><u>Описание</u></a></li>
                <li class="rvws"><a href="#reviews"><span><u>Отзывы</u></span> <i class="rvws-num">(<?=@$rvws['total']?>)</i></a></li>
                <?if(!empty($gallery))
                    {
                        ?>
                        <!--<li><a href="#gallery"><img src="/app/images/gallery.png" style="float: left; border: 0px; margin: 7px 10px 0px 0px; width: 25px;" alt=""><u>Галерея</u>&nbsp;<span style="color: red;">(<?/*=count($gallery)*/?>)</span></a></li>-->
                    <?}?>
            </ul>


            <div class="tabs-content clearfix active">

                <? if(!empty($cat['gt0']) || !empty($cat[0])){?>

                    <div class="tab-box clearfix">

                        <? if(!empty($cat['gt0'])){
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
                                <div></div>
								<div class="table-responsive-pd">
									<table>
										<thead>
											<tr>
												<td width="85px"><b>Радиус</b></a></td>
												<td><b>Название</b></td>
												<td width="85px"><b>Размер</b></td>
												<td width="70px"><b>Ин/Ис</b></td>
												<td width="50px"><b>Сезон</b></td>
												<td width="50px"><b>Шипы</b></td>
												<td width="65px"><b>Цена</b></a></td>
												<td width="50px"><b>Склад</b></td>
												<td width="54px" style="text-align:center; padding-right:10px;"><img src="/app/images/icon-basket.png" alt=""></td>
											</tr>
										</thead><?

										foreach($cat['gt0'] as $rad=>$rows){
											?><tbody class="rad__<?=$rad?>"><?
												$i=0;
												foreach($rows as $v){
													$i++;
													?><tr itemscope itemtype="http://schema.org/Product"><?
														if($i==1){
															?><td rowspan="<?=count($rows)?>" class="rad">R<?=$rad?> "</td><?
														}
														?>
														<td><div class="dess"><a itemprop="url" href="<?=$v['url']?>" class="h1"><?=$v['anc']?></a> <?=$v['suffixUrl']?></div></td>
														<td itemprop="model"><?=$v['razmer']?></td>
														<td><?=$v['inisUrl']?></td>
														<td><?=$v['sezIco']?></td>
														<td><?=$v['shipIco']?></td>
														<td><b class="scl"  cat_id="<?=$v['cat_id']?>"><?=$v['priceText']?></b></td>
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

                            if(!empty($cat['gt0'])){?><a href="#" forid="gt0w" class="cut-ctrl">показать размеры временно отсутствующие на складе</a> <div id="gt0w" class="cut-c"><? }

                                ?><div class="box-padding">
                                    <noindex><h3 class="im-h"><img src="/app/images/img-nnal.png" alt="">Позиции отсутствующие на складе</h3></noindex>
                                </div>
                                <div class="table-list">
                                    <div></div>
									<div class="table-responsive-pd">
										<table>
											<thead>
												<tr>
													<td width="85px"><b>Радиус</b></a></td>
													<td><b>Название</b></td>
													<td width="85px"><b>Размер</b></td>
													<td width="70px"><b>Ин/Ис</b></td>
													<td width="50px"><b>Сезон</b></td>
													<td width="50px"><b>Шипы</b></td>
													<td width="65px"><b>Цена</b></a></td>
													<td width="50px"><b>Склад</b></td>
													<td width="54px" style="text-align:center; padding-right:10px;"><img src="/app/images/icon-basket.png" alt=""></td>
												</tr>
											</thead><?

											foreach($cat[0] as $rad=>$rows){
												?><tbody class="prozr rad__<?=$rad?>"><?
													$i=0;
													foreach($rows as $v){
														$i++;
														?><tr><?
															if($i==1){
																?><td rowspan="<?=count($rows)?>" class="rad">R<?=$rad?> "</td><?
															}
															?>
															<td><div class="dess"><a href="<?=$v['url']?>" class="h1"><?=$v['anc']?></a></div></td>
															<td><?=$v['razmer']?></td>
															<td><?=$v['inisUrl']?></td>
															<td><?=$v['sezIco']?></td>
															<td><?=$v['shipIco']?></td>
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

                        }?>

                    </div>

                    <? }?>

                <div class="tab-box clearfix">
                    <div class="ctext model-mtext" itemprop="description"><?

                        if(!empty($mtext) && is_array($mtext)){
                            if(count($mtext)==1)
                                echo $mtext[0];
                            else
                                echo $mtext[0].$mtext[1];

                        }?>
                    </div>
                </div>


                <div class="tab-box clearfix">
                    <? $this->incView('catalog/tyres/reviews/list')?>
                </div>

                <?if(!empty($gallery))
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
                                <a class="gallery-popup-call" title="<?=(!empty($v['text']) ? $v['text'] : 'Шины '.$bname.' '.$mname)?>" data-fancybox-group="thumb" rel="tyres_gallery" href="<?=$v['img3']?>">
                                    <img src="<?=$v['img3']?>" alt="<?=(!empty($v['text']) ? $v['text'] : 'Шины '.$bname.' '.$mname)?>" />
                                </a>
                                <?
                            }
                        ?></div>
                    </div>
                    -->
                    <?
                }?>

            </div>
        </div>


    </div>
</div>
<?
if (!empty($mtext)){
    echo '<div class="ctext">';
    echo $mtext;
    echo '</div>';
}
?>
