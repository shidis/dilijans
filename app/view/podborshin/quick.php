<script type="text/javascript">
    $(document).ready(function () {
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
        if ($('.hidden_wrapper input[type="checkbox"]:checked').length == 0)
        {
            $('.hidden_wrapper_title').click(function(){
                $('.hidden_wrapper').css('display', 'inline').addClass('no-hidden');
                $(this).hide();
            });
        }
        else
        {
            $('.hidden_wrapper').css('display', 'inline').addClass('no-hidden');
            $('.hidden_wrapper_title').hide();
        }
    });
</script>
<!--************* Новые фильтры *************-->
<?if (!empty($relink_href)):?>
    <div class="re_link">
        <a href="<?=$relink_href?>">Перейти в раздел дисков</a>
    </div>
<?endif;?>
<div id="filter" class="tyres_catalog_filter podbor">
    <noindex>
        <div class="nav-filter">
            <div class="active">
                <div><span>Подбор шин</span><i>по марке авто</i></div>
                <img src="/app/images/img-nav-filter-01.png" alt="">
            </div>
            <div>
                <div><span>Поиск шин</span><i>по типоразмеру</i></div>
                <img src="/app/images/img-nav-filter-02.png" alt="">
            </div>
        </div>
        <div class="items-filter">
            <!--первая вкладка!-->
            <div class="active" style="">
                <form action="#" class="form-style-01 apForm tyres_filter podbor_width">
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
                    <!--div class="ap_sel_wrap">
                        <?
                        /*if (!empty($abCookie['svendor']) && !empty($abCookie['smodel']) && !empty($abCookie['syear']) && empty($abCookie['smodif']))
                        {
                            echo '<img class="choose_helper" src="/app/images/down_arrow.png" alt="" />';
                        }*/
                        ?>
                        <select class="apModif auto-submit"><option value="">двигатель</option><?
                            /*if(isset($ab->tree['modifs'])){
                                foreach($ab->tree['modifs'] as $k=>$v){
                                    ?><option value="<?=$v['sname']?>" <?=$abCookie['smodif']==$v['sname'] ? 'selected':''?>><?=Tools::html($v['name'],false)?></option><?
                                }
                            }*/
                            ?></select>
                    </div-->
                </form>
            </div>
            <!--вторая вкладка!-->
            <div>
                <form action="/<?= App_Route::_getUrl('tSearch') ?>.html" class="form-style-01 tsForm livef nolblform" chVars="1">
                    <?
                    /*foreach($filterHF as $k=>$v){
                        */ ?><!--<input type="hidden" name="<? /*=$k*/ ?>" value="<? /*=$v*/ ?>">--><? /*
                        }*/
                    ?>
                    <table>
                        <tr class="ftsl-row">
                            <td width="125px" class="grey2">Выберите размер:</td>
                            <td class="ftsl-w1">
                                <div class="select-01">
                                    <span></span>
                                    <select name="p3" id="" group="_p3" class="pp1"><?
                                        ?>
                                        <option value="">ширина</option><?
                                        foreach ($cc->s_arr['P3_1'] as $k => $v) {
                                            ?>
                                            <option
                                            id="_p3<?= $this->makeId($v) ?>" value="<?= $v ?>"><?= $v ?></option><?
                                        }
                                        ?></select>
                                    <i></i>
                                </div>
                            </td>
                            <td width="20px" class="grey2 ftsl-w2" style="text-align:center;padding-top: 10px;">/</td>
                            <td class="ftsl-w1">
                                <div class="select-01">
                                    <span></span>
                                    <select name="p2" id="" group="_p2" class="pp2"><?
                                        ?>
                                        <option value="">профиль</option><?
                                        foreach ($cc->s_arr['P2_1'] as $k => $v) {
                                            ?>
                                            <option
                                            id="_p2<?= $this->makeId($v) ?>" value="<?= $v ?>"><?= $v ?></option><?
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
                                    <select name="p1" id="" group="_p1" class="pp3"><?
                                        ?>
                                        <option value="">диаметр</option><?
                                        foreach ($cc->s_arr['P1_1'] as $k => $v) {
                                            ?>
                                        <option
                                            id="_p1<?= $this->makeId($v) ?>"
                                            value="<?= $v ?>">
                                            R<?= $v ?></option><?
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
                            if (!empty($lf['mp1'])) {
                                ?>
                                <tr>
                                <td class="grey2" width="135px">Сезонность:</td>
                                <td class="black"
                                    group="_mp1"<?= !empty($lf['_mp3']) ? ' style="padding-bottom:0"' : '' ?>><?
                                foreach ($lf['mp1'] as $k => $v) {
                                    ?><label class="brands-sezon"><input
                                        type="checkbox" name="mp1[<?= $k ?>]"
                                        id="<?= $v['id'] ?>" value="1"><i></i><span class="sp-label-wrapper"><button type="submit" class="mobile-result-label">Найти <span class="mobile-result-label__value"> <b>-</b> шт.</span></button><?=$v['anc']?></span></label><?
                                }
                                ?></td><?
                                ?></tr><?
                            }
                            if (!empty($lf['mp3'])) {
                                ?>
                                <tr>
                                <td class="grey2" width="135px"></td>
                                <td class="black" group="_mp3"><?
                                foreach ($lf['mp3'] as $k => $v) {
                                    ?><label class="brands-ship"><input
                                        type="checkbox" name="_mp3[<?= $k ?>]"
                                        id="<?= $v['id'] ?>" value="1"><i></i><span class="sp-label-wrapper"><button type="submit" class="mobile-result-label">Найти <span class="mobile-result-label__value"> <b>-</b> шт.</span></button><?=$v['anc']?></span></label><?
                                }
                                ?></td><?
                                ?></tr><?
                            }
                        }
                        ?>

                        <tr class="last">
                            <td></td>
                            <td class="black black-short">
                                <div group="_c_index" style="float: left; margin-top: 6px;" class="psh-sb psh-sb"><label
                                        style="width: 220px;margin-right: 40px;"
                                        for="_c_index"><input
                                            style="margin-right: 5px;float: left;" type="checkbox" id="_c_index"
                                            name="c_index" value="1">Легкогрузовая
                                        шина (индекс -C)</label></div>
                                <div group="_runflat" style="float: left; margin-top: 6px;" class="cr-group"><label style="width: 220px;"
                                                                                                   for="_runflat"><input
                                            style="margin-right: 5px;float: left;" type="checkbox" id="_runflat"
                                            name="runflat" value="1">Технология
                                        Run-flat</label></div>
                                <input type="button" class="tsGo" value="найти">

                                <div class="loader"></div>
                                <div style="position: absolute; right: 90px;" class="result-label"></div>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </noindex>
</div>
</div>
<!--************* /Новые фильтры *************-->
<? if (!empty($model)) { ?>
    <div class="ext_params_switcher">
        <a>Типоразмеры шин для <?= @$mark . ' ' . @$model . ' ' . @$modif . ' ' . (@$year ? @$year . ' г/в' : '') ?></a><span>&#8964;</span>
    </div>
    <div class="search_result_box_wrap">
        <div class="box-padding">
            <div class="search_result_box">
                <table>
                    <tbody>
                    <tr>
                        <th></th>
                        <th>
                            <b>Размеры шин для <?= $mark . ' ' . $model ?>.</b>
                        </th>
                    </tr>
                    <tr class="mobile-hide">
                        <td></td>
                        <td>
                            <ul class="title">
                                <li style="float: left;">Передняя ось/обе оси</li>
                                <li style="float: right;">Задняя ось</li>
                            </ul>
                        </td>
                    </tr>
                    <?
                    foreach ($sz1 as $rad => $v) {
                        ?>
                        <tr class="sh-tp-group params-group">
                        <td>
                            <div class="radius"><span>R<?= $rad ?></span></div>
                        </td>
                        <?
                        $type0 = '';
                        ?>
                        <td>
                        <ul><?
                            foreach ($v as $type => $vv) {
                                foreach ($vv as $row) {
                                    ?>
                                    <li><?
                                    if (!empty($row[2])) {
                                        if($row['exnum'] > 0) {
                                            ?><div class="r-box"><img src="/app/images/car-l.png" height="45" alt=""><img height="45" src="/app/images/car-r.png" alt=""><img src="/app/images/chain.png" alt="" class="chant"></div><?
                                            ?><a href="<?= $tSearchUrl . '?ap=1&p3=' . $row[1]['P3'] . '&p2=' . $row[1]['P2'] . '&p1=' . $row[1]['P1'] . '&p3_=' . $row[2]['P3'] . '&p2_=' . $row[2]['P2'] . '&p1_=' . $row[2]['P1'] ?>"><?
                                                ?><span><b><?= $row[1]['P3'] . '/' . $row[1]['P2'] . ' R' . $row[1]['P1']?></b> <span class="amount-goods"><?='('.$row['exnum'].' шт.)'?></span></span><?
                                                ?><span><?= $row[2]['P3'] . '/' . $row[2]['P2'] . ' R' . $row[2]['P1'] ?></span><?
                                            ?></a><?
                                        } else {
                                            ?><span class="empty-link"><?
                                                ?><span><?= $row[1]['P3'] . '/' . $row[1]['P2'] . ' R' . $row[1]['P1']?></span><?
                                                ?><span><?= $row[2]['P3'] . '/' . $row[2]['P2'] . ' R' . $row[2]['P1'] ?></span><?
                                            ?></span><?
                                        }
                                    } else {
                                        if ($row[1]['exnum'] > 0) {
                                            ?><div class="r-box"><img src="/app/images/chain.png" alt="" class="chant"></div><?
                                            ?><a href="<?= $tSearchUrl . '?ap=1&p3=' . $row[1]['P3'] . '&p2=' . $row[1]['P2'] . '&p1=' . $row[1]['P1'] ?>"><?
                                                ?><span><b><?= $row[1]['P3'] . '/' . $row[1]['P2'] . ' R' . $row[1]['P1']?></b> <span class="amount-goods"><?='('.$row[1]['exnum'].' шт.)'?></span></span><?
                                            ?></a><?
                                        } else {
                                            ?><span class="empty-link"><?
                                                ?><span><?= $row[1]['P3'] . '/' . $row[1]['P2'] . ' R' . $row[1]['P1']?></span><?
                                            ?></span><?
                                        }
                                    }
                                    ?></li><?
                                }

                            }
                            ?></ul></td><?
                        ?></tr><?
                    } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?
}
if (!empty($lf) && !empty($model)) {
    ?>

    <div style="padding: 16px 20px;" class="box-grey-01">
        <form action="<?= '/' . implode('/', Url::$spath) . '.html' ?>"
              class="form-style-01<?= !@$sMode ? ' livef' : '' ?>" onload_refresh="true">
            <? @Url::arr2hiddenFields(@$lfh); ?>

            <table class="model_search_filter"><?
                $i = 0;
                if (!empty($lf['_p1'])) {
                    $i++
                    ?>
                    <tr group="_p1">
                        <td class="black" width="120px"><b>Диаметры:</b></td>
                        <td class="black"><?
                            foreach ($lf['_p1'] as $k => $v) {
                                ?><label<?= $v['chk'] ? ' class="active"' : '' ?>><input
                                    type="checkbox"<?= $v['chk'] ? ' checked' : '' ?> name="_p1[<?= $k ?>]"
                                    id="<?= $v['id'] ?>"
                                    value="1"><i></i><span class="sp-label-wrapper"><button type="submit" class="mobile-result-label">Найти <span class="mobile-result-label__value"> <b>-</b> шт.</span></button><?=$v['anc']?></span></label><?
                            }
                            ?></td><?
                        ?></tr>
                    <tr>
                        <td colspan="2">
                            <hr>
                        </td>
                    </tr>
                    <?
                }
                if (!empty($lf)) {
                    if (!empty($lf['mp1'])) {
                        ?>
                        <tr style="border-bottom: 0px;">
                            <td class="black" width="135px" style="padding-top: 16px;"><b>Сезонность:</b></td>
                            <td class="black" style="padding-top: 16px;"
                                group="_mp1"<?= !empty($lf['_mp3']) ? ' style="padding-bottom:0"' : '' ?>><?
                                foreach ($lf['mp1'] as $k => $v) {
                                    ?><label class="brands-sezon<?= $v['chk'] ? ' active' : '' ?>"><input
                                        type="checkbox"<?= $v['chk'] ? ' checked' : '' ?> name="mp1[<?= $k ?>]"
                                        id="<?= $v['id'] ?>" value="1"><i></i><span class="sp-label-wrapper"><button type="submit" class="mobile-result-label">Найти <span class="mobile-result-label__value"> <b>-</b> шт.</span></button><?=$v['anc']?></span></label><?
                                }
                                ?></td><?
                            ?></tr>
                        <tr>
                            <td colspan="2">
                                <hr>
                            </td>
                        </tr><?
                    }
                    if (!empty($lf['mp3'])) {
                        ?>
                        <tr>
                            <td width="76px" style="padding-top: 16px;"></td>
                            <td class="black" group="_mp3" style="padding-top: 16px;"><?
                                foreach ($lf['mp3'] as $k => $v) {
                                    ?><label class="brands-ship<?= $v['chk'] ? ' active' : '' ?>"><input
                                        type="checkbox"<?= $v['chk'] ? ' checked' : '' ?> name="_mp3[<?= $k ?>]"
                                        id="<?= $v['id'] ?>" value="1"><i></i><span class="sp-label-wrapper"><button type="submit" class="mobile-result-label">Найти <span class="mobile-result-label__value"> <b>-</b> шт.</span></button><?=$v['anc']?></span></label><?
                                }
                                ?></td><?
                            ?></tr>
                        <tr>
                            <td colspan="2">
                                <hr>
                            </td>
                        </tr><?
                    }
                }
                ?>
                <tr>
                    <td class="black" style="padding-top: 16px;"><b>Технология:</b></td>
                    <td class="black black-short" style="padding-top: 16px;">
                        <div group="_runflat" style="float: left; margin-top: 6px;"><label style="width: 220px;"
                                                                                           for="_runflat" <?= (@$lf['runflat']['chk'] ? 'class="active"' : '') ?>><input
                                    style="margin-right: 5px;float: left;" type="checkbox" id="_runflat"
                                    name="runflat" value="1" <?= (@$lf['runflat']['chk'] ? 'checked="checked"' : '') ?>><button type="submit" class="mobile-result-label">Найти <span class="mobile-result-label__value"> <b>-</b> шт.</span></button>Технология
                                Run-flat</label></div>
                        <div group="_c_index" style="float: left; margin-top: 6px;" class="psh-sb"><label
                                style="width: 220px;margin-right: 40px;"
                                for="_c_index" <?= (@$lf['c_index']['chk'] ? 'class="active"' : '') ?>><input
                                    style="margin-right: 5px;float: left;" type="checkbox" id="_c_index"
                                    name="c_index" value="1" <?= (@$lf['c_index']['chk'] ? 'checked="checked"' : '') ?>><span class="sp-label-wrapper"><button type="submit" class="mobile-result-label">Найти <span class="mobile-result-label__value"> <b>-</b> шт.</span></button>Легкогрузовая
                                шина (индекс C)</span></label></div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <hr>
                    </td>
                </tr>
                <?
                if (!empty($lf['_bids'])) {
                    ?>
                    <tr group="_bids">
                    <td class="black" width="120px" style="padding-top: 16px;"><b>Производители:</b></td>
                    <td class="black" style="padding-top: 16px;">
                    <?
                    $j = 0;
                    foreach ($lf['_bids'] as $k => $v) {
                        $j++;
                        if ($j==12 && count($lf['_bids']) > 12)
                        {
                            echo '<div class="hidden_wrapper_title">еще варианты ('.(count($lf['_bids']) - 11).')</div>';
                            echo '<div class="hidden_wrapper" style="display: none;">';
                        }
                        ?><label<?= $v['chk'] ? ' class="active"' : '' ?>><input
                            type="checkbox"<?= $v['chk'] ? ' checked' : '' ?> name="_bids[<?= $k ?>]"
                            id="<?= $v['id'] ?>" value="1"><i></i><span class="sp-label-wrapper"><button type="submit" class="mobile-result-label">Найти <span class="mobile-result-label__value"> <b>-</b> шт.</span></button><?=$v['anc']?></span></label><?
                    }
                    if ($j>11 && count($lf['_bids']) > 12)
                    {
                        echo '</div>';
                    }
                    ?></td><?
                    ?></tr><?
                }
                ?>
                <tr class="last">
                    <td colspan="2"><input type="button" class="lfGo new_design" value="Найти">
                        <div class="loader"  style="margin-top: 5px; float: left !important;"></div>
                        <div class="result-label ext new_design"></div>
                    </td>
                </tr>
            </table>
            <input type="hidden" name="submited" value="1" />
        </form>
    </div>
<?if (!empty($s_info_str)) echo $s_info_str;?>
<? } ?>

