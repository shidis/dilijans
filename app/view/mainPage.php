<? $this->incView('general.top')?>
<div id="main" class="box-grey__main">
    <div id="sidebar" class="mobile-hide"><?
        if(!empty($bnr1['img'])){
            ?><div class="box-banner"><a href="<?=$bnr1['url']?>" rel="nofollow"><img src="<?=$bnr1['img']?>" alt=""></a></div><?
        } ?>
        <?
        if(!empty($articlesSB)){
            ?><div class="box-border"><?
            ?><h3><img src="/app/images/icon-info.png" alt="">Информация</h3><?
            ?><div><?
            ?><ul class="list-01"><?
            foreach($articlesSB as $v){
                ?><li><a href="<?=$v['url']?>"><?=$v['title']?></a></li><?
            }
            ?></ul><?
            ?><a href="<?=$allArticlesUrl?>" class="more">Все статьи</a><?
            ?></div><?
            ?></div><?
        }?>

        <div class="box-mailer mobile-hide"><?
            ?><h4>Новинки <br>События<br>Остатки</h4><?
            ?>Подписаться на email <br>рассылку<?
            ?><form action="#" id="subscribe"><?
                ?><input type="text" name="email" value="" placeholder="Ваш e-mail"><input type="submit" value="Ok"><?
            ?></form><?
        ?></div>
    </div>

    <div id="content" class="box-grey__content">
        <?if (!empty($slides)):?>
        <script src="/assets/js/jquery.bxslider.min.js"></script>
        <div class="main_page_slider">
            <ul class="bxslider">
            <?
                foreach ($slides as $slide)
                {
                    echo '
                    <li>
                        <div class="mp_s_wrap"><a class="mp_s_button" href="'.$slide['slide_link'].'">перейти к поиску</a><img src="'.$slide['src'].'" alt="" /></div>
                    </li>
                    ';
                }
            ?>
            </ul>
            <!--<ul class="bxslider">
                <li>
                    <div class="mp_s_wrap"><a class="mp_s_button" href="/letnie_shiny.html">перейти к поиску</a><img src="/app/images/mp_banners/summer_t.png" alt="Летние шины" /></div>
                </li>
                <li>
                    <div class="mp_s_wrap"><a class="mp_s_button" href="/shiny-dlya-vnedorozhnikov.html">перейти к поиску</a><img src="/app/images/mp_banners/vnedoroj_t.png" alt="Шины для внедорожников" /></div>
                </li>
                <li>
                    <div class="mp_s_wrap"><a class="mp_s_button" href="/db.html">перейти к поиску</a><img src="/app/images/mp_banners/diski.png" alt="Диски" /></div>
                </li>
            </ul>-->
        </div>
        <script type="text/javascript">
            $(document).ready(function(){
                $('.bxslider').bxSlider({
                    auto: true,
                    autoHover: true,
                    pause: 10000,
                    autoControls: false
                });
            });
        </script>
        <?endif;?>
		<div class="mobile-main-header">Большой ассортимент литых дисков и шин</div>
        <div class="box-grey box-grey_type_trt" style="background: url('/app/images/mp_box_gray_bg.png') 0 0 no-repeat; margin-top: -62px; border: 0px; border-radius: 0px;">
            <!--<img src="/app/images/radian.png" alt="" class="radio-img">-->
            <div class="relat"><?
                if(!empty($mpBlockTop) && false){
                    ?><h1>Интернет магазин шин и дисков Дилижанс</h1><?
                    ?><div class="service-items"><?
                        ?><ul><?
                            foreach($mpBlockTop as $v){
                                ?><li><a href="<?=$v['url']?>" rel="nofollow"></a><img src="<?=$v['img']?>" alt=""><a href="<?=$v['url']?>" rel="nofollow"><?=$v['anc']?></a></li><?
                            }
                        ?></ul><?
                    ?></div><?
                }?>
												
                <div id="filter">
                    <div class="nav-filter">
                        <div class="active">
                            <div><span>Подбор</span><i>по марке авто</i></div>
                            <img src="/app/images/img-nav-filter-01.png" alt="">
                        </div>
                        <div>
                            <div><span>Поиск шин</span><i>по типоразмеру</i></div>
                            <img src="/app/images/img-nav-filter-02.png" alt="">
                        </div>
                        <div>
                            <div><span>Поиск дисков</span><i>по типоразмеру</i></div>
                            <img src="/app/images/img-nav-filter-03.png" alt="">
                        </div>
                    </div>
                    <div class="items-filter">
                        <!--первая вкладка!-->
						<button type="button" class="items-filter__mobile-trg">Подбор по марке автомобиля</button>
                        <div class="active" style="height: 170px; background: url('/app/images/bg-div-001.png') 0 -195px repeat-x;">
                            <form action="#" class="form-style-01 apForm" style="float: left; width: 60%">
                                <input type="hidden" name="submited" value="1" />
                                <table>
                                    <tr>
                                        <td width="30%"><span class="trd__label">Выберите марку:</span></td>
                                        <td width="70%" class="trd__field-wrapper">
                                            <div class="select-01">
                                                <span></span>
                                                <select style="width: 100%;" class="apMark"><option value="">Не выбрано</option><?
                                                    if(isset($ab->tree['vendors'])){
                                                        foreach($ab->tree['vendors'] as $k=>$v){
                                                            ?><option value="<?=$v['sname']?>" <?=$abCookie['svendor']==$v['sname']?'selected':''?>><?=Tools::html($v['name'],false)?></option><?
                                                        }
                                                    }
                                                ?></select>
                                                <i></i>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="30%"><span class="trd__label">Выберите модель:</span></td>
                                        <td width="70%" class="trd__field-wrapper">
                                            <div class="select-01">
                                                <span></span>
                                                <select style="width: 100%;" class="apModel"><option value="">Не выбрано</option><?
                                                    if(isset($ab->tree['models'])){
                                                        foreach($ab->tree['models'] as $k=>$v){
                                                            ?><option value="<?=$v['sname']?>" <?=$abCookie['smodel']==$v['sname']?'selected':''?>><?=Tools::html($v['name'],false)?></option><?
                                                        }
                                                    }
                                                ?></select>
                                                <i></i>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="30%"><span class="trd__label">Год выпуска:</span></td>
                                        <td width="70%" class="trd__field-wrapper">
                                            <div class="select-01">
                                                <span></span>
                                                <select style="width: 100%;" class="apYear"><option value="">Не выбрано</option><?
                                                    if(isset($ab->tree['years'])){
                                                        foreach($ab->tree['years'] as $k=>$v){
                                                            ?><option value="<?=$v['sname']?>" <?=$abCookie['syear']==$v['sname']?'selected':''?>><?=Tools::html($v['name'],false)?></option><?
                                                        }
                                                    }
                                                ?></select>
                                                <i></i>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="30%"><span class="trd__label">Модификация:</span></td>
                                        <td width="70%" class="trd__field-wrapper">
                                            <div class="select-01">
                                                <span></span>
                                                <select style="width: 100%;" class="apModif"><option value="">Не выбрано</option><?
                                                    if(isset($ab->tree['modifs'])){
                                                        foreach($ab->tree['modifs'] as $k=>$v){
                                                            ?><option value="<?=$v['sname']?>" <?=$abCookie['smodif']==$v['sname']?'selected':''?>><?=Tools::html($v['name'],false)?></option><?
                                                        }
                                                    }
                                                ?></select>
                                                <i></i>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="trd__controls">
                                            <input type="button" p_type="bus" value="Подобрать шины" class="main_apGo" style="background: url('/app/images/new_buttons.png') no-repeat; width: 175px; height: 29px;">
                                            <input p_type="disk" type="button" value="Подобрать диски" class="main_apGo" style="background: url('/app/images/new_buttons.png') no-repeat; width: 175px; height: 29px;">
                                        </td>
                                    </tr>
                                </table>
                            </form>
                            <div style="float: right; width: 39%;" class="box-grey_type_info">
                                <div style="color: #f4e607; font-size: 16px; margin-bottom: 15px;">Самый быстрый и эффективный способ подбора шин / дисков на Ваш автомобиль.</div>
                                <p style="color: #FFFFFF; font-size: 13px; text-align: justify;">
                                    Для поиска достаточно знать Марку и модель Вашего автомобиля. Далее нажать кнопку “подобрать шины / диски”. После чего сформируется список типоразмеров выбранного товара подходящих на Ваш автомобиль.
                                </p>
                                <div style="color: #bcbcbc; float: right; margin-top: 10px;  font-size: 13px;">Удачных покупок</div>
                            </div>
                        </div>
                        <!--вторая вкладка!-->
						<button type="button" class="items-filter__mobile-trg">Поиск шин по типоразмеру</button>
                        <div style="background: #323a48 url('/app/images/bg-div-02.png') 0 -65px repeat-x;">
                            <form action="/<?=App_Route::_getUrl('tSearch')?>.html" method="get" class="form-style-01 tsForm liveC" chVars="1">
                                <table>
                                    <tr>
                                        <td width="120px"><span class="trd__label">Производитель:</span></td>
                                        <td style="padding-right:28px;">
                                            <div class="select-01">
                                                <span></span>
                                                <select name="vendor" group="_vendor"><?
                                                    ?><option value="">все</option><?
                                                    foreach($cc->s_arr['brands_sname_1'] as $k=>$v){
                                                        ?><option id="_vendor<?=$k?>" value="<?=$v['sname']?>"<?=@$cc->filters_coo[1]['vendor']==$v['sname']?' selected0="selected"':''?>><?=$v['name']?></option><?
                                                    }
                                                ?></select>
                                                <i></i>
                                            </div>
                                        </td>
                                        <td width="43px"><span class="trd__label">Сезон:</span></td>
                                        <td width="100px">
                                            <div class="select-01">
                                                <span></span>
                                                <select name="mp1" group="_mp1">
                                                    <option value="">все</option>
                                                    <option id="_mp11" value="1"<?=@$cc->filters_coo[1]['mp1']==1?' selected0="selected"':''?>>летняя</option>
                                                    <option id="_mp12" value="2"<?=@$cc->filters_coo[1]['mp1']==2?' selected0="selected"':''?>>зимняя</option>
                                                    <option id="_mp13" value="3"<?=@$cc->filters_coo[1]['mp1']==3?' selected0="selected"':''?>>всесезонная</option>
                                                </select>
                                                <i></i>
                                            </div>
                                        </td>
                                        <td width="100px" class="ps__hide">&nbsp;</td>
                                    </tr>
                                    <tr class="last">
                                        <td class="ps__hide">Выберите размер:</td>
                                        <td style="padding-right:28px;" class="ps-group">
                                            <table>
                                                <tr>
                                                    <td>
														<div class="ps-group__label"><span class="trd__label">Ширина:</span></div>
                                                        <div class="select-01">
                                                            <span></span>
                                                            <select name="p3" group="_p3"><?
                                                                ?><option value="">все</option><?
                                                                foreach($cc->s_arr['P3_1'] as $k=>$v){
                                                                    ?><option id="_p3<?=$this->makeId($v)?>" value="<?=$v?>"<?=@$cc->filters_coo[1]['p3']==$v?' selected0="selected"':''?>><?=$v?></option><?
                                                                }
                                                            ?></select>
                                                            <i></i>
                                                        </div>
                                                    </td>
                                                    <td style="width:19px; text-align:center;"><div class="trd__label">&nbsp;</div><span class="pr-separator">/</span></td>
                                                    <td>
														<div class="ps-group__label"><span class="trd__label">Высота:</span></div>
                                                        <div class="select-01">
                                                            <span></span>
                                                            <select name="p2" group="_p2"><?
                                                                ?><option value="">все</option><?
                                                                foreach($cc->s_arr['P2_1'] as $k=>$v){
                                                                    ?><option id="_p2<?=$this->makeId($v)?>" value="<?=$v?>"<?=@$cc->filters_coo[1]['p2']==$v?' selected0="selected"':''?>><?=$v?></option><?
                                                                }
                                                            ?></select>
                                                            <i></i>
                                                        </div>
                                                    </td>
                                                    <td style="width:39px; text-align:center;"><div class="help ntatip" rel="/ax/tip/sizesFilter.html"></div>R</td>
                                                    <td>
														<div class="ps-group__label"><span class="trd__label">Диаметр:</span></div>
                                                        <div class="select-01">
                                                            <span></span>
                                                            <select name="p1" group="_p1"><?
                                                                ?><option value="">все</option><?
                                                                foreach($cc->s_arr['P1_1'] as $k=>$v){
                                                                    ?><option id="_p1<?=$this->makeId($v)?>" value="<?=$v?>"<?=@$cc->filters_coo[1]['p1']==$v?' selected0="selected"':''?>><?=$v?></option><?
                                                                }
                                                            ?></select>
                                                            <i></i>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td><span class="trd__label">Шипы:</span></td>
                                        <td>
                                            <div class="select-02">
                                                <span></span>
                                                <select name="mp3" id="mp3" group="_mp3">
                                                    <option value="">все</option>
                                                    <option id="_mp31" value="1"<?=@$cc->filters_coo[1]['mp3']==1?' selected0="selected"':''?>>есть шипы</option>
                                                    <option id="_mp30" value="0"<?=@$cc->filters_coo[1]['mp3']==='0'?' selected0="selected"':''?>>нет шипов</option>
                                                </select>
                                                <i></i>
                                            </div>
                                        </td>
                                        <td width="100px" class="ps__control"><input type="button" value="Найти" class="tsGo"></td>
                                    </tr>
                                </table>
                            </form>
                        </div>
                        <!--третья вкладка!-->
						<button type="button" class="items-filter__mobile-trg">Поиск дисков по типоразмеру</button>
                        <div style="background: #323a48 url('/app/images/bg-div-03.png') 0 -60px repeat-x;">
                            <form action="/<?=App_Route::_getUrl('dSearch')?>.html" method="get" class="form-style-01 dsForm liveC" chVars="1">
                                <table>
                                    <tr class="vrow">
                                        <td width="130px" class="mobile-hide">Выберите ширину:</td>
                                        <td style="padding-right:33px;" class="vcol-l">
											<span class="trd__label mobile-show">Ширина:</span>
                                            <div class="select-01">
                                                <span></span>
                                                <select name="p2" group="_p2"><?
                                                    ?><option value="">все</option><?
                                                    foreach($cc->s_arr['P2_2'] as $k=>$v){
                                                        ?><option id="_p2<?=$this->makeId($v)?>" value="<?=$v?>"<?=@$cc->filters_coo[2]['p2']==$v?' selected0="selected"':''?>><?=$v?></option><?
                                                    }
                                                ?></select>
                                                <i></i>
                                            </div>
                                        </td>
                                        <td width="160px"class="mobile-hide"><div class="help ntatip" rel="/ax/tip/lzpcd.html"></div>Выберите PCD:</td>
                                        <td class="vcol-b vcol-f">
											<span class="trd__label mobile-show">Сверловка (PCD):</span>
                                            <div class="select-01">
                                                <span></span>
                                                <select name="sv" group="_sv"><?
                                                    ?><option value="">все</option><?
                                                    foreach($cc->s_arr['P4x6_2'] as $k=>$v){
                                                        ?><option id="_sv<?=$this->makeId($v)?>" value="<?=$v?>"<?=@$cc->filters_coo[2]['sv']==$v?' selected0="selected"':''?>><?=$v?></option><?
                                                    }
                                                ?></select>
                                                <i></i>
                                            </div>
                                        </td>
                                        <td width="100px" class="mobile-hide">&nbsp;</td>
                                    </tr>
                                    <tr class="last vcol-row">
                                        <td class="mobile-hide">Выберите диаметр:</td>
                                        <td style="padding-right:35px;" class="vcol-t vcol-r">
											<span class="trd__label mobile-show">Диaметр:</span>
                                            <div class="select-01">
                                                <span></span>
                                                <select name="p5" group="_p5"><?
                                                    ?><option value="">все</option><?
                                                    foreach($cc->s_arr['P5_2'] as $k=>$v){
                                                        ?><option id="_p5<?=$this->makeId($v)?>" value="<?=$v?>"<?=@$cc->filters_coo[2]['p5']==$v?' selected0="selected"':''?>><?=$v?></option><?
                                                    }
                                                ?></select>
                                                <i></i>
                                            </div>
                                        </td>
                                        <td class="mobile-hide"><div class="help ntatip" rel="/ax/tip/et.html"></div> <span class="trd__label">Выберите вылет(ET):</span></td>
                                        <td class="vcol-f">
											<span class="trd__label mobile-show">Выберите вылет(ET):</span>
                                            <div class="select-01">
                                                <span></span>
                                                <select name="p1" group="_p1"><?
                                                    ?><option value="">все</option><?
                                                    foreach($cc->s_arr['P1_2'] as $k=>$v){
                                                        ?><option id="_p1<?=$this->makeId($v)?>" value="<?=$v?>"<?=@$cc->filters_coo[2]['p1']==$v?' selected0="selected"':''?>><?=$v?></option><?
                                                    }
                                                ?></select>
                                                <i></i>
                                            </div>
                                        </td>
                                        <td width="100px" class="ps__control"><input type="button" value="Найти" class="dsGo"></td>
                                    </tr>
                                </table>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?
        if (!empty($result)) {
            ?><div class="popular-goods popular-goods_type_main">
                <div class="popular-banner popular-banner_type_main box-shadow box-mobile-no-shadow">
                    <div class="img mobile-hide">
                        <img src="/app/images/img-nav-filter-03.png">
                    </div>
                    <h4>Популярные подборки дисков и шин</h4>
                </div>
                <div class="products box-shadow">
                    <ul>
                    <?foreach ($result as $brand) {
                        ?>
                        <li>
                        <div class="product_brand">
                            <span class="logo-brand"><img src="/cimg/<?=$brand['brand_info']['img2']?>" /></span>
                            <span class="rating"><img src="/app/images/z-star.png" height="14"></span>
                        </div>
                        <div class="product_img"><img src="/assets/ui/<?=(!empty($brand['avto_image']) ? $brand['avto_image'] : $brand['brand_info']['avto_image'])?>" /></div>
                        <div class="product_brand_model"><?=$brand['brand_info']['name'] . ' '. $brand['name']?></div>
                        <div class="product_button">
                            <div><a href="<?=$brand['d_url']?>">ДИСКИ</a></div>
                            <div><a href="<?=$brand['t_url']?>">ШИНЫ</a></div>
                        </div>
                        </li>
                        <?
                    }
                    ?>
                    </ul>
                    <!--<div class="box-shadow"></div>-->
                </div>
            </div><?
        }?>

        <?// Блок новостей (лента)?>
        <div class="be-page mobile-hide">
            <div class="box-shadow">
                <div class="articles">
                    <h4 class="box-list__title">Новости</h4>

                    <ul style="display: none;"><?
                        foreach ($lenta as $v) {
                            ?>
                            <li>
                            <div><?
                            ?><a href="<?= $v['url'] ?>" class="h1"><?= $v['title'] ?></a><?
                            ?><p><?= $v['intro'] ?></p><?
                            ?><a href="<?= $v['url'] ?>" class="more">Смотреть записи</a><?
                            ?></div><?
                            ?></li><?
                        }
                        ?></ul>
                    <?
                    if (!empty($paginator)) {
                        ?>
                        <div class="paginator">
                        <ul><?
                            echo $paginator;
                            ?></ul></div><?
                    } ?>

                    <? if (!empty($entryList)) { ?>
                        <?
                        $firstElm = array_shift($entryList);

                        ?>

                        <? if (!empty($firstElm)): ?>
                            <div class="be-block"><?
                                if (!empty($firstElm['img1'])) {
                                    ?>
                                    <div class="be-block__img"><a href="<?= $firstElm['url'] ?>"><img
                                                src="<?= $firstElm['img1'] ?>" alt="<?= $firstElm['title'] ?>"></a>
                                    </div><?
                                }
                                ?>
                                <div><a href="<?= $firstElm['url'] ?>"
                                        class="be-block__h1"><?= $firstElm['title'] ?></a></div>
                                <p class="be-block__desc"><?= $firstElm['intro'] ?></p>
                                <a href="<?= $firstElm['url'] ?>" class="more">Читать полностью</a>
                            </div>
                        <? endif; ?>

                        <div class="be-cl">
                            <div class="be-cl__list">
                                <? foreach ($entryList as $v) { ?>
                                    <? if (!empty($v['title']) && $v['published']): ?>
                                        <div class="be-cl__item">
                                            <div class="be-cl__img"><?
                                                if (!empty($v['img1'])) {
                                                    ?><a href="<?= $v['url'] ?>"><img src="<?= $v['img1'] ?>"
                                                                                      alt="<?= $v['title'] ?>"></a><?
                                                }
                                                ?></div>
                                            <div><a href="<?= $v['url'] ?>" class="be-cl__h1"><?= $v['title'] ?></a>
                                            </div>
                                            <p class="be-cl__desc"><?= $v['intro'] ?></p>
                                            <a href="<?= $v['url'] ?>" class="more">Читать полностью</a>
                                        </div>
                                    <? endif; ?>
                                <? } ?>
                            </div>
                        </div>

                    <? } else { ?>
                        <? if (count($entryList) == 0): ?><p>Записей нет</p><? endif; ?>
                    <? } ?>
                </div>
            </div>
        </div>

        <?
        if(!empty($bnrCenter['text'])){
            ?><div class="box-padding mobile-hide"><?
                ?><div class="box-banner-02"><?
                    echo $bnrCenter['text'];
                ?></div><?
            ?></div><?
        }?>

    </div>

    <div class="box-shadow mobile-hide"><?
        if(!empty($bnrTyres['img'])){
            ?><div id="sidebar"><?
                ?><div class="box-banner2"><a href="<?=$bnrTyres['url']?>"><img src="<?=$bnrTyres['img']?>" alt=""></a></div><?
            ?></div><?
        }
        ?><div id="content"><?
            ?><div class="box-list"><?
                ?><div class="img"><img src="/app/images/icon-tire.png" alt=""></div><?
                ?><h4>Продажа автомобильных шин</h4><?
                ?><ul class="list-02"><?
                    foreach($tBrands as $v){
                        ?><li><a href="<?=$v['url']?>"><?=$v['name']?></a></li><?
                    }
                ?></ul><?
            ?></div><?
        ?></div><?
    ?></div><?

    ?><div class="box-shadow mobile-hide"><?
        if(!empty($bnrDisks['img'])){
            ?><div id="sidebar"><?
                ?><div class="box-banner2"><a href="<?=$bnrDisks['url']?>"><img src="<?=$bnrDisks['img']?>" alt=""></a></div><?
            ?></div><?
        }
        ?><div id="content"><?
            ?><div class="box-list"><?
                ?><div class="img"><img src="/app/images/icon-felly.png" alt=""></div><?
                ?><h4>Продажа литых дисков для авто</h4><?
                ?><ul class="list-02"><?
                    foreach($dBrands as $v){
                        ?><li><a href="<?=$v['url']?>"><?=$v['name']?></a></li><?
                    }
                ?></ul><?
            ?></div><?
        ?></div><?
    ?></div><?

    ?><div class="box-shadow mobile-hide"><?
        if(!empty($bnrReplica['img'])){
            ?><div id="sidebar"><?
                ?><div class="box-banner2"><a href="<?=$bnrReplica['url']?>"><img src="<?=$bnrReplica['img']?>" alt=""></a></div><?
            ?></div><?
        }
        ?><div id="content"><?
            ?><div class="box-list"><?
                ?><div class="img"><img src="/app/images/icon-marka.png" alt=""></div><?
                ?><h4>Диски Replica<br><span>подбор по марке автомобиля</span></h4><?
                ?><ul class="list-03"><?
                    foreach($replicaBrands as $v){
                        ?><li><?
                            if(!empty($v['img'])){
                                ?><img width="17" src="<?=$v['img']?>" alt="<?=$v['name']?>"><?
                            }
                            ?><a href="<?=$v['url']?>"><?=$v['name']?></a></li><?
                    }
                ?></ul><?
            ?></div><?
        ?></div><?
    ?></div><?

    ?><div class="box-ov" data-bind="developed by QMark.ru"><?
        ?><div id="content"><?
            ?><div class="box-pad">
				<div class="mp-toggle">
					<div class="mp-toggle__trigger">Информация о магазине</div>
					<div class="mp-toggle__content">
					<?
						?><h3>Купить шины и диски в интернет магазине</h3><?
						?><div class="box-about"><?
							if(is_array($mpAbout)){
								?><ul class="collum"><?
									?><li class="ctext"><?
										echo $mpAbout[1];
									?></li><?
									?><li class="ctext"><?
										echo $mpAbout[2];
									?></li><?
								?></ul><?
							} else echo $mpAbout;
						?></div><?
					?>
					</div>
				</div>
			</div><?
        ?></div><?
    ?></div>

</div>

<? echo $this->incView('general.bottom');