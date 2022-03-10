<style type="text/css">
    #content .goods-01 .img {
        width: 120px;
        height: 200px;
    }
</style>
<div class="box-shadow">
    <div class="box-padding">
        <h1 class="title cat"><img src="/app/images/img-warranty-01.jpg" alt=""><?= $_title ?></h1><?

        if (!empty($topText)) {
            ?>
            <div class="ctext"><?
            echo $topText;
            ?></div><?
        }
        ?>
        <div id="filter" class="tyres_catalog_filter">
            <noindex>
                <div class="nav-filter">
                    <div class="active">
                        <div><span>Поиск шин</span><i>по типоразмеру</i></div>
                        <img src="/app/images/img-nav-filter-02.png" alt="">
                    </div>
                    <div>
                        <div><span>Подбор шин</span><i>по марке авто</i></div>
                        <img src="/app/images/img-nav-filter-01.png" alt="">
                    </div>
                </div>
                <div class="items-filter">
                    <!--первая вкладка!-->
                    <div class="active">
                        <? if ($lf || $filter){ ?>
                        <form action="/<?= App_Route::_getUrl('tSearch') ?>.html" class="form-style-01 tsForm livef"
                              chVars="1">

                            <? Url::arr2hiddenFields($lfh);

                            ?>
                            <table>
                                <tr class="ftsl-row">
                                    <td width="76px"><!--<img src="/app/images/img-text-04.png" alt="">--></td>
                                    <td width="135px" class="grey2">Выберите размер:</td>
                                    <td class="ftsl-w1">
                                        <div class="select-01">
                                            <span></span>
                                            <select name="p3" id="" group="_p3"><?
                                                ?>
                                                <option value="">ширина</option><?
                                                foreach ($filter['P3'] as $k => $v) {
                                                    ?>
                                                    <option id="_p3<?= $this->makeId($v) ?>"
                                                            value="<?= $v ?>"><?= $v ?></option><?
                                                }
                                                ?></select>
                                            <i></i>
                                        </div>
                                    </td>
                                    <td width="20px" class="grey2 ftsl-w2" style="text-align:center;padding-top: 10px;">/</td>
                                    <td class="ftsl-w1">
                                        <div class="select-01">
                                            <span></span>
                                            <select name="p2" id="" group="_p2"><?
                                                ?>
                                                <option value="">профиль</option><?
                                                foreach ($filter['P2'] as $k => $v) {
                                                    ?>
                                                    <option id="_p2<?= $this->makeId($v) ?>"
                                                            value="<?= $v ?>"><?= $v ?></option><?
                                                }
                                                ?></select>
                                            <i></i>
                                        </div>
                                    </td>
                                    <td width="39px" class="grey2 ftsl-w2" style="text-align:center;padding-top: 10px;">
                                        <div class="help2 ntatip" rel="/ax/tip/sizesFilter.html"></div>
                                        R
                                    </td>
                                    <td class="ftsl-w3">
                                        <div class="select-01">
                                            <span></span>
                                            <select name="p1" id="" group="_p1"><?
                                                ?>
                                                <option value="">диаметр</option><?
                                                foreach ($filter['P1'] as $k => $v) {
                                                    ?>
                                                    <option id="_p1<?= $this->makeId($v) ?>"
                                                            value="<?= $v ?>"><?= $v ?></option><?
                                                }
                                                ?></select>
                                            <i></i>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            <table>
                                <?
                                if (!empty($lf)) {
                                    $i = 0;
                                    if (!empty($lf['_mp1'])) {
                                        $i++
                                        ?>
                                        <tr<?= $i == $lfi ? ' class="last"' : '' ?>>
                                        <td width="76px"></td>
                                        <td class="grey2" width="135px">Сезонность:</td>
                                        <td class="black"
                                            group="_mp1"<?= !empty($lf['_mp3']) ? ' style="padding-bottom:0"' : '' ?>><?
                                        foreach ($lf['_mp1'] as $k => $v) {
                                            ?><label class="brands-sezon<?= $v['chk'] ? ' active' : '' ?>"><input
                                                type="checkbox"<?= $v['chk'] ? ' checked' : '' ?> name="mp1[<?= $k ?>]"
                                                id="<?= $v['id'] ?>" value="1"><i></i><?= $v['anc'] ?></label><?
                                        }
                                        ?></td><?
                                        ?></tr><?
                                    }
                                    if (!empty($lf['_mp3'])) {
                                        $i++
                                        ?>
                                        <tr<?= $i == $lfi ? ' class="last"' : '' ?>>
                                        <td width="76px"></td>
                                        <td class="grey2" width="135px"></td>
                                        <td class="black" group="_mp3"><?
                                        foreach ($lf['_mp3'] as $k => $v) {
                                            ?><label class="brands-ship<?= $v['chk'] ? ' active' : '' ?>"><input
                                                type="checkbox"<?= $v['chk'] ? ' checked' : '' ?> name="_mp3[<?= $k ?>]"
                                                id="<?= $v['id'] ?>" value="1"><i></i><?= $v['anc'] ?></label><?
                                        }
                                        ?></td><?
                                        ?></tr><?
                                    }
                                }
                                ?>

                                <tr class="last">
                                    <td colspan="3"><input type="button" class="tsGo" value="найти">

                                        <div class="loader"></div>
                                        <div style="right: 95px !important;" class="result-label"></div>
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </div>
                    <? } ?>
                    <!--вторая вкладка!-->
                    <div>
                        <form action="#" class="form-style-01 apForm tyres_filter">
                            <div class="f_title">Быстрый переход:</div>
                            <select class="apMark"><option value="">марка авто</option><?
                                if(isset($ab->tree['vendors'])){
                                    foreach($ab->tree['vendors'] as $k=>$v){
                                        ?><option value="<?=$v['sname']?>" <?=$abCookie['svendor']==$v['sname']?'selected':''?>><?=Tools::html($v['name'],false)?></option><?
                                    }
                                }
                                ?></select>
                            <select class="apModel"><option value="">модель авто</option><?
                                if(isset($ab->tree['models'])){
                                    foreach($ab->tree['models'] as $k=>$v){
                                        ?><option value="<?=$v['sname']?>" <?=$abCookie['smodel']==$v['sname']?'selected':''?>><?=Tools::html($v['name'],false)?></option><?
                                    }
                                }
                                ?></select>
                            <select class="apYear"><option value="">год выпуска</option><?
                                if(isset($ab->tree['years'])){
                                    foreach($ab->tree['years'] as $k=>$v){
                                        ?><option value="<?=$v['sname']?>" <?=$abCookie['syear']==$v['sname']?'selected':''?>><?=Tools::html($v['name'],false)?></option><?
                                    }
                                }
                                ?></select>
                            <select class="apModif"><option value="">двигатель</option><?
                                if(isset($ab->tree['modifs'])){
                                    foreach($ab->tree['modifs'] as $k=>$v){
                                        ?><option value="<?=$v['sname']?>" <?=$abCookie['smodif']==$v['sname'] ? 'selected':''?>><?=Tools::html($v['name'],false)?></option><?
                                    }
                                }
                                ?></select>
                            <input p_type="bus" type="button" value="найти" class="main_apGo">
                        </form>
                    </div>
                </div>
            </noindex>
        </div>
        <?
        if (!empty($models)) {


            if ($doubleDimension) {
                foreach ($models as $kk => $vv)
                    if (!empty($vv)) {
                        ?><h2 class="bcat"><?
                        if (!empty($h2[$kk]['url'])) {
                            ?><a href="<?= $h2[$kk]['url'] ?>"><?= $h2[$kk]['title'] ?></a><?
                        } else {
                            ?><?= $h2[$kk]['title'] ?><?
                        }
                        ?></h2><?
                        ?>
                        <div class="goods-01 catalog_item tyres"><?
                        ?>
                        <ul><?
                        foreach ($vv as $v) {
                            ?>
                            <li><?
                            if ($v['sez'] == 1) {
                                ?><u class="sun"></u><?
                            }
                            if ($v['sez'] == 2) {
                                ?><u class="snow"></u><?
                            }
                            if ($v['sez'] == 3) {
                                ?><u class="sun-snow"></u><?
                            }
                            if ($v['ship']) {
                                ?><em></em><?
                            }
                            ?>
                            <div class="img"><?
                            if ($v['spezId']) {
                                ?><i></i><?
                            }
                            /*Отзывы*/
                            if (@$v['reviews']['mrate']>0){
                                ?>
                                <div class="mrate">
                                    <ul class="stars stars-fix " v="<?=ceil($v['reviews']['mrate'])?>"></ul>
                                </div>
                                <?
                            }
                            /********/
                            ?><a href="<?= $v['url'] ?>"><img src="<?= $v['img'] ?>" height="200"
                                                              alt="<?= $v['alt'] ?>"></a>
                            <?
                            if (!empty($v['video_link'])){
                                ?>
                                <a class="video-call catalog tyres fancybox.iframe" title="" rel="" href="<?=$v['video_link']?>?rel=0&amp;autoplay=1"><span>Смотреть видео</span></a>
                                <?
                            }
                            ?>
                            </div><?
                            ?>
                            <div class="catalog_item_content"><?
                            ?><a href="<?= $v['url'] ?>" class="h1"><?= $v['anc'] ?></a><?
                            if ($v['scDiv']) {
                                ?><span class="nal">есть на складе</span><?
                            } else {
                                ?><span class="nnal">нет в наличии</span><?
                            }

                            // R
                            if (!empty($v['radiuses'])) {
                                echo '<div class="catalog_item_r">Диаметры: ';
                                $i = 0;
                                foreach ($v['radiuses'] as $rad => $empty) {
                                    $i++;
                                    if ($i != count($v['radiuses'])) {
                                        echo 'R' . $rad . ', ';
                                    } else echo 'R' . $rad;
                                }
                                echo '</div>';
                            }
                            // Prices
                            echo '<div class="catalog_item_prices">';
                            if (!empty($v['prices']) && min($v['prices']) > 0 && max($v['prices']) > 0) {
                                if (min($v['prices']) == max($v['prices'])) {
                                    echo 'Цена: ' . Tools::nn(min($v['prices'])) . ' р.';
                                } else echo 'Цены: от ' . Tools::nn(min($v['prices'])) . ' до ' . Tools::nn(max($v['prices'])) . ' р.';
                            } else echo 'Цены: уточняйте по тел.';
                            echo '</div>';
                            ?></div><?
                            ?></li><?
                        }
                        ?></ul><?
                        ?></div><?

                    }
            } else {
                ?>
                <div class="box-rez"><?
                if (!empty($paginator)) {
                    ?>
                    <div class="paginator">
                    <ul><?
                        echo $paginator;
                        ?></ul></div><?
                }
                ?><b>Найдено моделей <?= $num ?></b><?
                ?></div><?

                ?>
                <div class="goods-01 catalog_item tyres"><?
                ?>
                <ul><?
                foreach ($models as $v) {
                    ?>
                    <li><?
                    if ($v['sez'] == 1) {
                        ?><u class="sun"></u><?
                    }
                    if ($v['sez'] == 2) {
                        ?><u class="snow"></u><?
                    }
                    if ($v['sez'] == 3) {
                        ?><u class="sun-snow"></u><?
                    }
                    if ($v['ship']) {
                        ?><em></em><?
                    }
                    ?>
                    <div class="img"><?
                    if ($v['spezId']) {
                        ?><i></i><?
                    }
                    ?><a href="<?= $v['url'] ?>"><img src="<?= $v['img'] ?>" height="200" alt="<?= $v['alt'] ?>"></a>
                    </div><?
                    ?>
                    <div class="catalog_item_content"><?
                    ?><a href="<?= $v['url'] ?>" class="h1"><?= $v['anc'] ?></a><?
                    if ($v['scDiv']) {
                        ?><span class="nal">есть на складе</span><?
                    } else {
                        ?><span class="nnal">нет в наличии</span><?
                    }

                    // R
                    if (!empty($v['radiuses'])) {
                        echo '<div class="catalog_item_r">Диаметры: ';
                        $i = 0;
                        foreach ($v['radiuses'] as $rad => $empty) {
                            $i++;
                            if ($i != count($v['radiuses'])) {
                                echo 'R' . $rad . ', ';
                            } else echo 'R' . $rad;
                        }
                        echo '</div>';
                    }
                    // Prices
                    echo '<div class="catalog_item_prices">';
                    if (!empty($v['prices']) && min($v['prices']) > 0 && max($v['prices']) > 0) {
                        if (min($v['prices']) == max($v['prices'])) {
                            echo 'Цена: ' . Tools::nn(min($v['prices'])) . ' р.';
                        } else echo 'Цены: от ' . Tools::nn(min($v['prices'])) . ' до ' . Tools::nn(max($v['prices'])) . ' р.';
                    } else echo 'Цены: уточняйте по тел.';
                    echo '</div>';
                    ?></div><?
                    ?></li><?
                }
                ?></ul><?
                ?></div><?

                if (!empty($paginator)) {
                    ?>
                    <div class="paginator">
                    <ul><?
                        echo $paginator;
                        ?></ul></div><?
                }
            }


        } else {
            ?>
            <div class="box-no-nal"><?= $noResults ?></div><?
        }


        ?><a href="<?= $backUrl ?>" class="back">Вернуться в каталог шин</a>

    </div>
</div>