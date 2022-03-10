<style type="text/css">
    #content .goods-01 .img{
        width: 150px;
    }
</style>
<div class="box-shadow">
    <div class="box-padding">
        <h1 class="title cat"><img src="/app/images/img-warranty-02.jpg" alt=""><?=$_title?></h1><?
        if(!empty($seo_img))
        {
            ?>
            <p><img title="<?=@$_title?>" src="<?=$seo_img?>" alt="<?=@$_title?>" /></p>
            <?
        }
        if(!empty($topText)){
            ?><div class="ctext"><?
                echo $topText;
            ?></div><?
        }
        /*if(!empty($lf)){?>

        <div class="box-grey-01">
        <form action="/<?=App_Route::_getUrl('dSearch')?>.html" class="form-style-01 livef dsForm">

        <? Url::arr2hiddenFields($lfh);?>

        <table>

        <? if(!empty($lf['p5'])){ ?>

        <tr group="_p5">
        <td class="black">Диаметр диска:</td>
        </tr>
        <tr>
        <td class="black" group=""><?
        foreach($lf['p5'] as $k=>$v){
        ?><label><input type="checkbox" name="_p5[<?=$k?>]" id="<?=$v['id']?>" value="1"><i></i><?=$v['anc']?></label><?
        }
        ?></td>
        </tr>

        <? }?>

        <? if(!empty($lf['sv'])){ ?>

        <tr group="_sv">
        <td class="black">Сверловка (PCD):</td>
        </tr>
        <tr>
        <td class="black"><?
        foreach($lf['sv'] as $k=>$v){
        ?><label><input type="checkbox" name="_sv[<?=$k?>]" id="<?=$v['id']?>" value="1"><i></i><?=$v['anc']?></label><?
        }
        ?></td>
        </tr>

        <? }?>

        <tr class="last">
        <td><input type="button" class="dsGo" value="найти"><div class="loader"></div> <div class="result-label"></div></td>
        </tr>
        </table>
        </form>


        </div>

        <?

        }

        if(!empty($replicaCross)){

        $this->incView('podbor/quick');

        ?><div class="box-block-filter" style="margin-bottom: 20px">
        <ul class="list-08"><?
        foreach($abModels as $v){
        ?><li style="width: 170px;"><a href="<?=$v['url']?>" title="<?=$v['title']?>"><?=$v['anc']?></a></li><?
        }
        ?></ul>
        </div><?

        }  */
        ?> 
        <!--************* Новые фильтры *************-->
        <div id="filter" class="disks_catalog_filter disks_catalog_filter_type_mobile">
            <noindex>
                <div class="nav-filter">
                    <div>
                        <div><span>Подбор</span><i>по марке авто</i></div>
                        <img src="/app/images/img-nav-filter-01.png" alt="">
                    </div>
                    <div class="active">
                        <div><span>Поиск дисков</span><i>по типоразмеру</i></div>
                        <img src="/app/images/img-nav-filter-03.png" alt="">
                    </div>
                </div>
                <div class="items-filter">
                    <!--первая вкладка!-->
                    <div style="">
                        <form action="#" class="form-style-01 apForm">
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
                                <input p_type="disk" type="button" value="найти" class="main_apGo">
                        </form>
                    </div>
                    <!--вторая вкладка!-->
                    <div class="active">
                        <form action="/<?=App_Route::_getUrl('dSearch')?>.html" method="get" class="form-style-01 dsForm liveC sdf" chVars="1">
                            <table>
                                <tr>
                                    <td>Диаметр диска (R*):<br>
                                        <div class="select-01">
                                            <span></span>
                                            <select name="p5" group="_p5"><?
                                                ?><option value="">все</option><?
                                                foreach($cc->s_arr['P5_2'] as $k=>$v){
                                                    ?><option id="_p5<?=$this->makeId($v)?>" value="<?=$v?>"<?//@$cc->filters_coo[2]['p5']==$v?' selected="selected"':''?>><?=$v?></option><?
                                                }
                                            ?></select>
                                            <i></i>
                                        </div>
                                    </td>
                                    <td><!--<div class="help ntatip" rel="/ax/tip/lzpcd.html"></div>-->Сверловка (LZxPCD):<br>
                                        <div class="select-01">
                                            <span></span>
                                            <select name="sv" group="_sv"><?
                                                ?><option value="">все</option><?
                                                foreach($cc->s_arr['P4x6_2'] as $k=>$v){
                                                    ?><option id="_sv<?=$this->makeId($v)?>" value="<?=$v?>"<?//@$cc->filters_coo[2]['sv']==$v?' selected="selected"':''?>><?=$v?></option><?
                                                }
                                            ?></select>
                                            <i></i>
                                        </div>
                                    </td>
                                    <td>Производитель:<br>
                                        <div class="select-01">
                                            <span></span>
                                            <select name="vendor" group="_vendor"><?
                                                ?><option value="">все</option><? 
                                                foreach($cc->s_arr['r_brands_sname_2'] as $k=>$v){
                                                    ?><option id="_vendor<?=$k?>" value="<?=$v['sname']?>"<?=($brand_sname==$v['sname'])?' selected="selected"':''?>>Replica <?=$v['name']?></option><?
                                                }  
                                                foreach($cc->s_arr['nor_brands_sname_2'] as $k=>$v){
                                                    ?><option id="_vendor<?=$k?>" value="<?=$v['sname']?>"<?=($brand_sname==$v['sname'])?' selected="selected"':''?>><?=$v['name']?></option><?
                                                }  
                                                ?><!--</optgroup>-->
                                            </select>
                                            <i></i>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="last">
                                    <td>Ширина диска (J):<br>
                                        <div class="select-01">
                                            <span></span>
                                            <select name="p2" group="_p2"><?
                                                ?><option value="">все</option><?
                                                foreach($cc->s_arr['P2_2'] as $k=>$v){
                                                    ?><option id="_p2<?=$this->makeId($v)?>" value="<?=$v?>"<?//@$cc->filters_coo[2]['p2']==$v?' selected="selected"':''?>><?=$v?></option><?
                                                }
                                            ?></select>
                                            <i></i>
                                        </div>
                                    </td>
                                    <td><!--<div class="help ntatip" rel="/ax/tip/et.html"></div>-->Вылет(ET):<br>
                                        <div class="select-01">
                                            <span></span>
                                            <select name="p1" group="_p1"><?
                                                ?><option value="">все</option><?
                                                foreach($cc->s_arr['P1_2'] as $k=>$v){
                                                    ?><option id="_p1<?=$this->makeId($v)?>" value="<?=$v?>"<?//@$cc->filters_coo[2]['p1']==$v?' selected="selected"':''?>><?=$v?></option><?
                                                }
                                            ?></select>
                                            <i></i>
                                        </div>
                                    </td>
                                    <td>Ступица (DIA):<br>
                                        <div class="select-01" style="float: left; width: 100px; margin-right: 15px;">
                                            <span></span>
                                            <select name="p3" group="_p3"><?
                                                ?><option value="">все</option><?
                                                foreach($cc->s_arr['P3_2'] as $k=>$v){
                                                    ?><option id="_p3<?=$this->makeId($v)?>" value="<?=$v?>"<?//@$cc->filters_coo[2]['p3']==$v?' selected="selected"':''?>><?=$v?></option><?
                                                }
                                            ?></select>
                                            <i></i>
                                        </div>
                                        <label for="ap" class="checkbox-01" style="font-weight: 700; margin-top: 13px; float: right; margin-right: 35px;"><input type="checkbox" undisabled="1" value="1" id="ap" name="ap" checked="1">и более</label>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-top: 10px; text-align: left;" colspan="2">Выберите необходимые параметры диска и нажмите кнопку 'Подобрать диски'</td>
                                    <td style="padding-top: 10px; text-align: right"><input style="background: url('/app/images/new_buttons.png') no-repeat; width: 175px; height: 29px;" type="button" value="подобрать диски" class="dsGo"><div class="loader"></div> <div class="result-label"></div></td>
                                </tr>
                            </table>
                        </form>
                    </div>
                </div>
            </noindex>
        </div>
        <?

        if(!empty($models)){

            ?><div class="replaceable_content" num="<?=$num?>">
                <div class="box-rez"><?

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
                            ?><li><?
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
                                            <a class="video-call catalog fancybox.iframe" title="" rel="" href="<?=$v['video_link']?>?rel=0&amp;autoplay=1"><span>Смотреть видео</span></a>
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

            if(!empty($mLimit) && !empty($paginator)){
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
        </div>

        <? if(!empty($replicaCross)){

            ?><div style="margin-top: 25px; overflow: hidden" class="mobile-hide">
                <h4>Быстрый переход в каталог дисков реплика по радиусу</h4>

                <div class="box-list2">
                    <ul class="list-06"><?
                        foreach($rlinks as $v){
                            ?><li><a href="<?=$v['url']?>" title="<?=$v['title']?>"><?=$v['anc']?></a></li><?
                        }
                    ?></ul>
                </div>
            </div>

            <p style="margin: 0 0 25px 0" class="models-mobile-str"><a href="<?=$replicaCross['url']?>" title="<?=$replicaCross['title']?>"><?=$replicaCross['anc']?></a> </p>

            <? }?>

        <a href="<?=$backUrl?>" class="back">Вернуться в каталог дисков</a>

    </div>
</div>
