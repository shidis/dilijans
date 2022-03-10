<script type="text/javascript">
    $( document ).ready(function() {
        if ($('.choose_helper').length > 0)
        {
            setInterval(function(){
                $(".choose_helper").animate({
                    top: "-=5",
                }, 100, function() {
                    $(this).css('top', $(this).css('top') + 5);
                });
            }, 1000)
        }
    });
</script>
<!--************* Новые фильтры *************-->
<?if (!empty($relink_href)):?>
    <div class="re_link">
        <a href="<?=$relink_href?>">Перейти в раздел шины</a>
    </div>
<?endif;?>
<div id="filter" class="podbor disks_catalog_filter" style="margin-bottom: 10px;">
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
            <div class="active" style="padding-top: 30px;">
                <form action="#" class="form-style-01 apForm podbor_width">
                    <div class="f_title">Быстрый переход:</div>
                    <div class="ap_sel_wrap">
                    <?
                    if (empty($abCookie['svendor']) && empty($abCookie['smodel']) && empty($abCookie['syear']) && empty($abCookie['smodif']))
                    {
                        echo '<img class="choose_helper" src="/app/images/down_arrow.png" alt="" />';
                    }
                    ?>
                    <select class="apMark"><option value="">марка авто</option><?
                        if(isset($ab->tree['vendors'])){
                            foreach($ab->tree['vendors'] as $k=>$v){
                                ?><option value="<?=$v['sname']?>" <?=$abCookie['svendor']==$v['sname']?'selected':''?>><?=Tools::html($v['name'],false)?></option><?
                            }
                        }
                        ?></select>
                    </div>
                    <div class="ap_sel_wrap">
                    <?
                    if (!empty($abCookie['svendor']) && empty($abCookie['smodel']) && empty($abCookie['syear']) && empty($abCookie['smodif']))
                    {
                        echo '<img class="choose_helper" src="/app/images/down_arrow.png" alt="" />';
                    }
                    ?>
                    <select class="apModel"><option value="">модель авто</option><?
                        if(isset($ab->tree['models'])){
                            foreach($ab->tree['models'] as $k=>$v){
                                ?><option value="<?=$v['sname']?>" <?=$abCookie['smodel']==$v['sname']?'selected':''?>><?=Tools::html($v['name'],false)?></option><?
                            }
                        }
                        ?></select>
                    </div>
                    <div class="ap_sel_wrap">
                    <?
                    if (!empty($abCookie['svendor']) && !empty($abCookie['smodel']) && empty($abCookie['syear']) && empty($abCookie['smodif']))
                    {
                        echo '<img class="choose_helper" src="/app/images/down_arrow.png" alt="" />';
                    }
                    ?>
                    <select class="apYear"><option value="">год выпуска</option><?
                        if(isset($ab->tree['years'])){
                            foreach($ab->tree['years'] as $k=>$v){
                                ?><option value="<?=$v['sname']?>" <?=$abCookie['syear']==$v['sname']?'selected':''?>><?=Tools::html($v['name'],false)?></option><?
                            }
                        }
                        ?></select>
                    </div>
                    <div class="ap_sel_wrap">
                    <?
                    if (!empty($abCookie['svendor']) && !empty($abCookie['smodel']) && !empty($abCookie['syear']) && empty($abCookie['smodif']))
                    {
                        echo '<img class="choose_helper" src="/app/images/down_arrow.png" alt="" />';
                    }
                    ?>
                    <select class="apModif auto-submit"><option value="">двигатель</option><?
                        if(isset($ab->tree['modifs'])){
                            foreach($ab->tree['modifs'] as $k=>$v){
                                ?><option value="<?=$v['sname']?>" <?=$abCookie['smodif']==$v['sname'] ? 'selected':''?>><?=Tools::html($v['name'],false)?></option><?
                            }
                        }
                    ?></select>
                    </div>
                </form>
            </div>
            <!--вторая вкладка!-->
            <div>
                <form action="/<?=App_Route::_getUrl('dSearch')?>.html" method="get" class="form-style-01 dsForm liveC" chVars="1">
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
                                        ?><option value="">все</option>
                                        <?
                                        foreach($cc->s_arr['r_brands_sname_2'] as $k=>$v){
                                            ?><option id="_vendor<?=$k?>" value="<?=$v['sname']?>"<?//@$cc->filters_coo[2]['vendor']==$v['sname']?' selected="selected"':''?>>Replica <?=$v['name']?></option><?
                                        }                     
                                        foreach($cc->s_arr['nor_brands_sname_2'] as $k=>$v){
                                            ?><option id="_vendor<?=$k?>" value="<?=$v['sname']?>"<?//@$cc->filters_coo[2]['vendor']==$v['sname']?' selected="selected"':''?>><?=$v['name']?></option><?
                                        }
                                        ?><!--<optgroup label="Replica">--><!--</optgroup>-->
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
