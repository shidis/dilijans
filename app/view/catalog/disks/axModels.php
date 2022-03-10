<?if(!empty($models)){
    ?><div class="box-rez"><?

        if(!empty($paginator)){
            ?><div class="paginator"><ul><?
                    echo $paginator;
                ?></ul></div><?
        }
        ?><b>Найдено моделей <?=$num?></b><?

    ?></div><?

    ?><div class="goods-01 catalog_item"><?
        ?><ul><?
            foreach($models as $v){
                ?><li class="replaceable_goods" ><a class="retl" id="<?=$v['cat_id']?>"></a><?
                    // Стикеры
                    if (!empty($v['model_sticker']['sticker_id'])){
                        $sticker_img = '<div class="sticker_image_wrap"><img class="sticker_image" src="'.$v['model_sticker']['img'].'" alt="" />';
                        if ($v['model_sticker']['allow_text'] && !empty($v['model_sticker']['sticker_text'])){
                            $sticker_img .= '<span class="sticker_image_text">'.$v['model_sticker']['sticker_text'].'</span>';
                        }
                        $sticker_img .= '</div>';
                    }
                    // *******
                    else $sticker_img = '';
                    ?><div class="img"><?
                        if($v['spezId']){
                            ?><i></i><?
                        }
                        ?><a href="<?=$v['url']?>"><img height="150" src="<?=$v['img']?>" alt="<?=$v['alt']?>"></a><?=$sticker_img?>
                        <?
                        if (!empty($v['video_link'])){
                            ?>
                                <a class="video-call catalog fancybox.iframe" title="" rel="" href="<?=$v['video_link']?>"><span>Смотреть видео</span></a>
                            <?
                        }
                        ?>
                </div><?
                    ?><div class="catalog_item_content"><?
                        ?><a href="<?=$v['url']?>" class="h1"><?=$v['anc']?></a><?
                        if($v['scDiv']){
                            ?><span class="nal">есть на складе</span><?
                        }else{
                            ?><span class="nnal">нет в наличии</span><?
                        }
                        // R
                        if (!empty($v['radiuses']))
                        {
                            echo '<div class="catalog_item_r">Диаметры: ';
                            $i = 0;
                            foreach ($v['radiuses'] as $rad => $empty)
                            {
                                $i++;
                                if ($i != count($v['radiuses']))
                                {
                                    echo 'R'.$rad.', ';
                                }
                                else echo 'R'.$rad;
                            }
                            echo '</div>';
                        }
                        // Color
                        if (!empty($v['colors']) && !empty($v['colors'][0]))
                        {
                            echo '<div class="catalog_item_colors">Цвет: ';
                            $i = 0;
                            foreach ($v['colors'] as $color)
                            {
                                $i++;
                                if (!empty($color))
                                {
                                    if ($i != count($v['colors']))
                                    {
                                        echo $color.', ';
                                    }
                                    else echo $color;
                                }
                            }
                            echo '</div>';
                        }
                        // Prices
                        echo '<div class="catalog_item_prices">';
                        if (!empty($v['prices']) && min($v['prices']) > 0 && max($v['prices']) > 0)
                        {
                            if (min($v['prices']) == max($v['prices']))
                            {
                                echo 'Цена: '.Tools::nn(min($v['prices'])).' р.';
                            }
                            else echo 'Цены: от '.Tools::nn(min($v['prices'])).' до '.Tools::nn(max($v['prices'])).' р.';
                        }
                        else echo 'Цены: уточняйте по тел.';
                        echo '</div>';
                    ?></div><?
                ?></li><?
            }
        ?></ul><?
    ?></div><?
} else{
    ?><div class="box-no-nal"><?=$noResults?></div><?
}

 if(!empty($limit) && !empty($paginator)){
    ?><div class="showmore" onclick="showmore()" id="showmore" style="padding-bottom: 15px;"><div id="showmore_ajaxloading"></div><a>Показать еще</a></div><?
    ?><script>
        var cur_limit = <?=$mLimit?>;
        var lockPage = false;
    </script><?
}

if(!empty($paginator)){
    ?><div class="paginator"><ul><?
            echo $paginator;
        ?></ul></div><?
}?>

<? if(!empty($replicaCross)){

    ?><div style="margin-top: 25px; overflow: hidden">
        <h4>Быстрый переход в каталог дисков реплика по радиусу</h4>

        <div class="box-list2">
            <ul class="list-06"><?
                foreach($rlinks as $v){
                    ?><li><a href="<?=$v['url']?>" title="<?=$v['title']?>"><?=$v['anc']?></a></li><?
                }
            ?></ul>
        </div>
    </div>

    <p style="margin: 0 0 25px 0"><a href="<?=$replicaCross['url']?>" title="<?=$replicaCross['title']?>"><?=$replicaCross['anc']?></a> </p>

    <? }?>