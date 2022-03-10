<div class="be-page">
    <div class="ctext clearfix">
        <?if(!empty($img1)):?>
            <div class="be-block">
                <div class="be-block__img"><img src="<?=$img1?>" alt="<?=$_title?>"></div>
            </div>
        <?endif;?>
        <h1 class="title"><?=$_title?></h1>

        <?
        if(!empty($modelsForToken)) {

            foreach ($modelsForToken as $item) {
                $token = $item['token'];
                $items = '<div class="goods-01 catalog_item tyres catalog_item_inner"><ul class="items">';

                if(!empty($item['value'])) {
                    foreach ($item['value'] as $v) {
//                        if(!empty($v['model_fields']['scDiv'])) {
                        if($v['type'] == 'm') {
                            if (!empty($v['model_fields'])) {
                                $items .= '<li class="item item-' . $v['gr'] . '">';

                                if ($v['gr'] == 1) {
                                    // Шины
                                    if (isset($v['model_fields']['sez']) && $v['model_fields']['sez'] == 1) {
                                        $items .= '<u class="sun"></u>';
                                    }
                                    if (isset($v['model_fields']['sez']) && $v['model_fields']['sez'] == 2) {
                                        $items .= '<u class="snow"></u>';
                                    }
                                    if (isset($v['model_fields']['sez']) && $v['model_fields']['sez'] == 3) {
                                        $items .= '<u class="sun-snow"></u>';
                                    }
                                    if (isset($v['model_fields']['sez']) && $v['model_fields']['ship']) {
                                        $items .= '<em></em>';
                                    }

                                    if (isset($v['model_fields']['sez']) && $v['model_fields']['spezId']) {
                                        $items .= '<i></i>';
                                    }

                                    $items .= '<div class="img">';
                                    $items .= '<a href="' . $v['url'] . '"><img height="150" src="' . $v['img'] . '" alt="' . $v['alt'] . '"></a>';
                                    $items .= '</div>';

                                    $items .= '<div class="catalog_item_content">';
                                    $items .= '<a href="' . $v['url'] . '" class="h1">' . $v['anc'] . '</a>';
                                    if (isset($v['model_fields']['scDiv']) && $v['model_fields']['scDiv']) {
                                        $items .= '<span class="nal">есть на складе</span>';
                                    } else {
                                        $items .= '<span class="nnal">нет в наличии</span>';
                                    }

                                    // R
                                    if (!empty($v['model_fields']['radiuses'])) {
                                        $items .= '<div class="catalog_item_r">Диаметры: ';
                                        $i = 0;
                                        foreach ($v['model_fields']['radiuses'] as $rad => $empty) {
                                            $i++;
                                            if ($i != count($v['model_fields']['radiuses'])) {
                                                $items .= 'R' . $rad . ', ';
                                            } else  $items .= 'R' . $rad;
                                        }
                                        $items .= '</div>';
                                    }

                                    $items .= '<div class="catalog_item_prices">';
                                    if (!empty($v['model_fields']['prices']) && min($v['model_fields']['prices']) > 0 && max($v['model_fields']['prices']) > 0) {
                                        if (min($v['model_fields']['prices']) == max($v['model_fields']['prices'])) {
                                            $items .= 'Цена: ' . Tools::nn(min($v['model_fields']['prices'])) . ' р.';
                                        } else $items .= 'Цены: от ' . Tools::nn(min($v['model_fields']['prices'])) . ' до ' . Tools::nn(max($v['model_fields']['prices'])) . ' р.';
                                    } else $items .= 'Цены: уточняйте по тел.';
                                    $items .= '</div>';


                                    $items .= '</div>';

                                } elseif ($v['gr'] == 2) {
                                    // Диски
                                    // Стикеры
                                    $sticker_img = '';
                                    if (!empty($v['model_sticker']['sticker_id'])) {
                                        $sticker_img = '<div class="sticker_image_wrap"><img class="sticker_image" src="' . $v['model_sticker']['img'] . '" alt="" />';
                                        if ($v['model_sticker']['allow_text'] && !empty($v['model_sticker']['sticker_text'])) {
                                            $sticker_img .= '<span class="sticker_image_text">' . $v['model_sticker']['sticker_text'] . '</span>';
                                        }
                                        $sticker_img .= '</div>';
                                    }
                                    $items .= '<div class="img">';
                                    $items .= '<a href="' . $v['url'] . '"><img height="150" src="' . $v['model_fields']['img'] . '" alt="' . $v['model_fields']['alt'] . '"></a>' . $sticker_img;
                                    $items .= '</div>';

                                    $items .= '<div class="catalog_item_content">';
                                    $items .= '<a href="' . $v['url'] . '" class="h1">' . $v['model_fields']['anc'] . '</a>';

                                    if (isset($v['model_fields']['scDiv']) && $v['model_fields']['scDiv']) {
                                        $items .= '<span class="nal">есть на складе</span>';
                                    } else {
                                        $items .= '<span class="nnal">нет в наличии</span>';
                                    }

                                    if (!empty($v['model_fields']['radiuses'])) {
                                        $items .= '<div class="catalog_item_r">Диаметры: ';
                                        $i = 0;
                                        foreach ($v['model_fields']['radiuses'] as $rad => $empty) {
                                            $i++;
                                            if ($i != count($v['model_fields']['radiuses'])) {
                                                $items .= 'R' . $rad . ', ';
                                            } else $items .= 'R' . $rad;
                                        }
                                        $items .= '</div>';
                                    }

                                    // Color
                                    if (!empty($v['model_fields']['colors']) && !empty($v['model_fields']['colors'][0])) {
                                        $items .= '<div class="catalog_item_colors">Цвет: ';
                                        $i = 0;
                                        foreach ($v['model_fields']['colors'] as $color) {
                                            $i++;
                                            if (!empty($color)) {
                                                if ($i != count($v['model_fields']['colors'])) {
                                                    $items .= $color . ', ';
                                                } else  $items .= $color;
                                            }
                                        }
                                        $items .= '</div>';
                                    }

                                    // Prices
                                    $items .= '<div class="catalog_item_prices">';
                                    if (!empty($v['model_fields']['prices']) && min($v['model_fields']['prices']) > 0 && max($v['model_fields']['prices']) > 0) {
                                        if (min($v['model_fields']['prices']) == max($v['model_fields']['prices'])) {
                                            $items .= 'Цена: ' . Tools::nn(min($v['model_fields']['prices'])) . ' р.';
                                        } else $items .= 'Цены: от ' . Tools::nn(min($v['model_fields']['prices'])) . ' до ' . Tools::nn(max($v['model_fields']['prices'])) . ' р.';
                                    } else $items .= 'Цены: уточняйте по тел.';
                                    $items .= '</div>';

                                    $items .= '</div>';
                                }
                                $items .= '</li>';
                            }
                        } else {
                            if(!empty($v['gr'])) {
                                $items .= '<li class="item item-' . $v['gr'] . '">';
                                // Иконки шин
                                if (isset($v['sezonId']) && $v['sezonId'] == 1) {
                                    $items .= '<u class="sun"></u>';
                                }
                                if (isset($v['sezonId']) && $v['sezonId'] == 2) {
                                    $items .= '<u class="snow"></u>';
                                }
                                if (isset($v['sezonId']) && $v['sezonId'] == 3) {
                                    $items .= '<u class="sun-snow"></u>';
                                }
                                if (isset($v['sezonId']) && $v['shipId']) {
                                    $items .= '<em></em>';
                                }

                                if (isset($v['model_fields']['sez']) && $v['model_fields']['spezId']) {
                                    $items .= '<i></i>';
                                }
                                $items .= '<div class="img">';
                                $items .= '<a href="' . $v['self_url'] . '"><img height="150" src="' . $v['img1'] . '" alt="' . $v['title'] . '"></a>';
                                $items .= '</div>';
                                $items .= '<div class="catalog_item_content">';
                                $items .= '<a href="' . $v['self_url'] . '" class="h1">' . $v['_title'] . '</a>';
                                if (isset($v['scDiv']) && $v['scDiv']) {
                                    $items .= '<span class="nal">есть на складе</span>';
                                } else {
                                    $items .= '<span class="nnal">нет в наличии</span>';
                                }

                                if (!empty($v['radius'])) {
                                    $items .= '<div class="catalog_item_r">Диаметр: R' . $v['radius'];
                                    $items .= '</div>';
                                }


                                // Color
                                if (!empty($v['color'])) {
                                    $items .= '<div class="catalog_item_colors">Цвет: '.$v['color_url'].'</div>';
                                }


                                // Prices
                                $items .= '<div class="catalog_item_prices">Цена: '.$v['priceText'].'</div>';

                                $items .= '</div></li>';
                            }
                        }
                    }
                }
                $items .= '</ul></div>';

                $content = str_replace($token, $items, $content);
            }
        }
        echo $content;

    ?>
    </div>

    <? if(!empty($entryList)){?>

        <div class="be-cl be-cl--in_page">
            <div class="be-cl__subtitle">Другие публикации</div>

            <div class="be-cl__list">
                <?foreach($entryList as $v){?>
                    <?if(!empty($v['title']) && $v['published']):?>
                        <div class="be-cl__item"><div class="be-cl__img">
                                <?
                                if(!empty($v['img1'])){
                                    ?><a href="<?=$v['url']?>"><img src="<?=$v['img1']?>" alt="<?=$v['title']?>"></a><?
                                }
                                ?></div>
                            <div><a href="<?=$v['url']?>" class="be-cl__h1"><?=$v['title']?></a></div>
                            <p class="be-cl__desc"><?=$v['intro']?></p>
                            <a href="<?=$v['url']?>" class="more">Читать полностью</a>
                        </div>
                    <?endif;?>
                <? }?>
            </div>
        </div>
    <? }?>
</div>

