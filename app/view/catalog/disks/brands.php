<div class="box-shadow">
    <div class="">
        <h1 class="title"><?=$_title?></h1>
        <!--************* Новые фильтры *************-->
        <div id="filter" class="disks_catalog_filter disks_catalog_filter_type_mobile">
            <noindex>
                <div class="nav-filter">
                    <div class="active">
                        <div><span>Подбор</span><i>по марке авто</i></div>
                        <img src="/app/images/img-nav-filter-01.png" alt="">
                    </div>
                    <div>
                        <div><span>Поиск дисков</span><i>по типоразмеру</i></div>
                        <img src="/app/images/img-nav-filter-03.png" alt="">
                    </div>
                </div>
                <div class="items-filter">
                    <!--первая вкладка!-->
                    <div class="active" style="">
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
                    <div>
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
                                                    ?><option id="_vendor<?=$k?>" value="<?=$v['sname']?>"<?//@$cc->filters_coo[2]['vendor']==$v['sname']?' selected="selected"':''?>>Replica <?=$v['name']?></option><?
                                                }
                                                foreach($cc->s_arr['nor_brands_sname_2'] as $k=>$v){
                                                    ?><option id="_vendor<?=$k?>" value="<?=$v['sname']?>"<?//@$cc->filters_coo[2]['vendor']==$v['sname']?' selected="selected"':''?>><?=$v['name']?></option><?
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
    </div>
    <p style="margin: 15px 0px;">
        В нашем магазине представлен очень большой ассортимент разных брендов дисков. Для того, чтобы понять каке диски точно подходят на Ваш автомобиль, 
        воспользуйтесь подбором по марке автомобиля, или выберите параметры дисков в поиске по типоразмеру.
    </p>
    <!--************* /Новые фильтры *************-->
    <?  if(!empty($lfi)){?>

        <!--<div class="box-grey-01"><noindex>
        <form action="/<?=App_Route::_getUrl('dSearch')?>.html" class="form-style-01 livef dsForm" chVars="1">

        <? Url::arr2hiddenFields($lfh);?>

        <table>

        <? if(!empty($lf['p5'])){ ?>

            <tr group="_p5">
            <td class="black">Диаметр диска:</td>
            </tr>
            <tr>
            <td class="black" group=""><?
            foreach($lf['p5'] as $k=>$v){
                ?><label><input type="checkbox" name="p5[<?=$k?>]" id="<?=$v['id']?>" value="1"><i></i><?=$v['anc']?></label><?
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
                ?><label><input type="checkbox" name="sv[<?=$k?>]" id="<?=$v['id']?>" value="1"><i></i><?=$v['anc']?></label><?
            }
            ?></td>
            </tr>

            <? }?>

        <tr class="last">
        <td><input type="button" class="dsGo" value="найти"><div class="loader"></div> <div class="result-label"></div></td>
        </tr>
        </table>
        </form>       
        </noindex></div>-->
        <? }
    if (!empty($brands[1])) {
        echo '<b>Популярные бренды</b><br><br>';
        ?>
        <ul class="list-brends-02"><?
            foreach ($brands[1] as $v) {
                ?>
                <li><a href="<?= $v['url'] ?>"></a><?
                ?>
                <table><?
                    ?>
                    <tr><?
                        ?>
                        <td><img src="<?= $v['img1'] ?>" alt="<?= $v['alt'] ?>"></td><?
                        ?></tr><?
                    ?></table><?
                ?>
                <div class="bl_title"><a href="<?= $v['url'] ?>" title="<?= $v['title'] ?>"><?= $v['name'] ?></a></div>
                </li><?
            }
            ?>
        </ul>
        <?
        echo '<b>Прочие бренды</b><br><br>';
    }
    ?>
    <?if (!empty($brands[0])):?>
    <ul class="list-brends-02"><?
            foreach ($brands[0] as $v) {
                ?>
                <li><a href="<?= $v['url'] ?>"></a><?
                ?>
                <table><?
                    ?>
                    <tr><?
                        ?>
                        <td><img src="<?= $v['img1'] ?>" alt="<?= $v['alt'] ?>"></td><?
                        ?></tr><?
                    ?></table><?
                ?>
                <div class="bl_title"><a href="<?= $v['url'] ?>" title="<?= $v['title'] ?>"><?= $v['name'] ?></a></div>
                </li><?
            }
        ?>
    </ul>
    <?else: if (empty($brands[1])) echo '<p>Не найдено ни одного бренда!</p>'; endif;?>
</div>
<?

if(!empty($replicaBrands)){
    ?><div class="box-shadow">
        <div class="box-list">
            <div class="img"><img src="/app/images/icon-marka.png" alt=""></div>
            <h4>Диски Replica<br><span>подбор по марке автомобиля</span></h4>
        </div>
        <div class="box-padding">
            <ul class="list-brends-03"><?
                foreach ($replicaBrands as $v) {
                    ?><li><a href="<?= $v['url'] ?>"></a><?
                        ?><table><?
                            ?><tr><?
                                ?><td><img src="<?= $v['img1'] ?>" alt="<?= $v['alt'] ?>"></td><?
                            ?></tr><?
                        ?></table><?
                        ?><span><a href="<?= $v['url'] ?>" title="<?= $v['title'] ?>"><?= $v['name'] ?></a></span></li><?
                }
                ?>
                <li class="ll"></li>
                <li class="ll"></li>
                <li class="ll"></li>
                <li class="ll"></li>
            </ul>
        </div>
    </div><?
}
?><div class="box-padding">
    <h4>Быстрый переход в каталог дисков по радиусу</h4>

    <div class="box-list2">
        <ul class="list-06"><?
            foreach($rlinks as $v){
                ?><li><a href="<?=$v['url']?>" title="<?=$v['title']?>"><?=$v['anc']?></a></li><?
            }
        ?></ul>
    </div>
</div>