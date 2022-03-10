<script type="text/javascript">
    $(document).ready(function () {
        $('.change_typo_switcher').click(function () {
            $('.change_typo_wrap').toggle('fast', function () {
                if ($(this).is(':visible'))
                {
                    $('.change_typo_switcher > span')[0].innerHTML = '&#8963;';
                    $('.ext_filter').fadeOut('fast');
                }
                else{
                    $('.change_typo_switcher > span')[0].innerHTML = '&#8964;';
                    $('.ext_filter').fadeIn('fast');
                }
            });
        });
    });
</script>
<!--************* Новые фильтры *************-->
<div class="change_typo_switcher"><img src="/app/images/img-nav-filter-02.png" style="margin-bottom: -12px;" alt=""
                                       width="23"/><a>Изменить
        типоразмер</a><span>&#8964;</span></div>
<div class="change_typo_wrap">
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
                    <form action="/<?= App_Route::_getUrl('tSearch') ?>.html" class="form-style-01 tsForm livef"
                          chVars="1"><?
                        /*foreach($filterHF as $k=>$v){
                            ?><input type="hidden" name="<?=$k?>" value="<?=$v?>"><?
                        }*/
                        ?>
                        <table>
                            <tr>
                                <td width="135px" class="grey2">Выберите размер:</td>
                                <td>
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
                                <td width="20px" class="grey2" style="text-align:center;padding-top: 10px;">/</td>
                                <td>
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
                                <td width="39px" class="grey2" style="text-align:center;padding-top: 10px;">
                                    <div class="help2 ntatip" rel="/ax/tip/sizesFilter.html"></div>
                                    R
                                </td>
                                <td>
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
                                if (!empty($lf['_mp1'])) {
                                    ?>
                                    <tr>
                                    <td class="grey2" width="145px">Сезонность:</td>
                                    <td class="black"
                                        group="_mp1"<?= !empty($lf['_mp3']) ? ' style="padding-bottom:0"' : '' ?>><?
                                    foreach ($lf['_mp1'] as $k => $v) {
                                        ?><label class="brands-sezon"><input
                                            type="checkbox" name="_mp1[<?= $k ?>]"
                                            id="<?= $v['id'] ?>"
                                            value="1"><i></i><?= $v['anc'] ?></label><?
                                    }
                                    ?></td><?
                                    ?></tr><?
                                }
                                if (!empty($lf['_mp3'])) {
                                    ?>
                                    <tr>
                                    <td class="grey2" width="145px"></td>
                                    <td class="black" group="_mp3"><?
                                    foreach ($lf['_mp3'] as $k => $v) {
                                        ?><label class="brands-ship"><input
                                            type="checkbox" name="_mp3[<?= $k ?>]"
                                            id="<?= $v['id'] ?>"
                                            value="1"><i></i><?= $v['anc'] ?></label><?
                                    }
                                    ?></td><?
                                    ?></tr><?
                                }
                                if (!empty($lf['_mp2'])) {
                                    ?>
                                    <tr>
                                    <td class="grey2" width="145px">Тип автомобиля:</td>
                                    <td class="black" group="_at"><?
                                    foreach ($lf['_mp2'] as $k => $v) {
                                        ?><label class="atype"><input
                                            type="checkbox" name="_at[<?= $k ?>]"
                                            id="<?= $v['id'] ?>"
                                            value="1"><i></i><?= $v['anc'] ?></label><?
                                    }
                                    ?></td><?
                                    ?></tr><?
                                }
                                ?>
                            <? } ?>
                            <tr class="last">
                                <td></td>
                                <td class="black">
                                    <div group="_c_index" style="float: left; margin-top: 6px;"><label
                                            style="width: 220px;margin-right: 40px;"
                                            for="_c_index"><input
                                                style="margin-right: 5px;float: left;" type="checkbox" id="_c_index"
                                                name="c_index" value="1">Легкогрузовая
                                            шина (индекс C)</label></div>
                                    <div group="_runflat" style="float: left; margin-top: 6px;"><label
                                            style="width: 220px;"
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
</div>
</div>
<!--************* /Новые фильтры *************-->
<?if(!empty($lfi) && App_Route::_getAction()!='search'):?>
<div class="box-grey-01 ext_filter">
    <form action="/<?= App_Route::_getUrl('tSearch') ?>.html" class="form-style-01 livef" sMode="<?= @$sMode ?>"
          onload_refresh="true">

        <? Url::arr2hiddenFields($lfh); ?>
        <div style="display: none;">
            <input type="checkbox" value="1" checked="checked">
        </div>
        <table><?
            $i = 0;
            if (!empty($lf['_bids'])) {
                $i++
                ?>
            <tr<?= $i == $lfi ? ' class="last"' : '' ?>>
                <td class="black" width="120px">Производители:</td>
                <td class="black" group="_bids"><?
                foreach ($lf['_bids'] as $k => $v) {
                    ?><label<?= $v['chk'] ? ' class="active"' : '' ?>><input
                        type="checkbox"<?= $v['chk'] ? ' checked' : '' ?> name="_bids[<?= $k ?>]" id="<?= $v['id'] ?>"
                        value="1"><i></i><?= $v['anc'] ?></label><?
                }
                ?></td><?
                ?></tr><?
            }
            if (!empty($lf['_p1'])) {
                $i++
                ?>
            <tr<?= $i == $lfi ? ' class="last"' : '' ?>>
                <td class="black" width="120px">Диаметр:</td>
                <td class="black" group="_p1"><?
                foreach ($lf['_p1'] as $k => $v) {
                    ?><label<?= $v['chk'] ? ' class="active"' : '' ?>><input
                        type="checkbox"<?= $v['chk'] ? ' checked' : '' ?> name="_p1[<?= $k ?>]" id="<?= $v['id'] ?>"
                        value="1"><i></i><?= $v['anc'] ?></label><?
                }
                ?></td><?
                ?></tr><?
            }
            if (!empty($lf['_mp1'])) {
                $i++
                ?>
            <tr<?= $i == $lfi ? ' class="last"' : '' ?>>
                <td class="black" width="120px">Сезонность:</td>
                <td class="black" group="_mp1"<?= !empty($lf['_mp3']) ? ' style="padding-bottom:0"' : '' ?>><?
                foreach ($lf['_mp1'] as $k => $v) {
                    ?><label class="sezon<?= $v['chk'] ? ' active' : '' ?>"><input
                        type="checkbox"<?= $v['chk'] ? ' checked' : '' ?> name="_mp1[<?= $k ?>]" id="<?= $v['id'] ?>"
                        value="1"><i></i><?= $v['anc'] ?></label><?
                }
                ?></td><?
                ?></tr><?
            }
            if (!empty($lf['_mp3'])) {
                $i++
                ?>
            <tr<?= $i == $lfi ? ' class="last"' : '' ?>>
                <td class="black" width="120px"></td>
                <td class="black" group="_mp3"><?
                foreach ($lf['_mp3'] as $k => $v) {
                    ?><label class="ship<?= $v['chk'] ? ' active' : '' ?>"><input
                        type="checkbox"<?= $v['chk'] ? ' checked' : '' ?> name="_mp3[<?= $k ?>]" id="<?= $v['id'] ?>"
                        value="1"><i></i><?= $v['anc'] ?></label><?
                }
                ?></td><?
                ?></tr><?
            }
            if (!empty($lf['_mp2'])) {
                $i++
                ?>
            <tr<?= $i == $lfi ? ' class="last"' : '' ?>>
                <td class="black" width="120px">Тип автомобиля:</td>
                <td class="black" group="_at"><?
                foreach ($lf['_mp2'] as $k => $v) {
                    ?><label class="atype<?= $v['chk'] ? ' active' : '' ?>"><input
                        type="checkbox"<?= $v['chk'] ? ' checked' : '' ?> name="_at[<?= $k ?>]" id="<?= $v['id'] ?>"
                        value="1"><i></i><?= $v['anc'] ?></label><?
                }
                ?></td><?
                ?></tr><?
            }
            ?>
            <tr class="last">
                <td class="grey2" width="150"></td>
                <td class="black">
                    <div group="_c_index" style="float: left; margin-top: 6px;"><label
                            style="width: 200px;margin-right: 60px;"
                            for="_c_index" <?= (@$c_index ? 'class="active"' : '') ?>><input
                                style="margin-right: 5px;float: left;" type="checkbox" id="_c_index" name="c_index"
                                value="1" <?= (@$c_index ? 'checked="checked"' : '') ?>>Легкогрузовая шина (индекс
                            C)</label></div>
                    <div group="_runflat" style="float: left; margin-top: 6px; width: 100%;"><label style="width: 200px;"
                                                                                       for="_runflat" <?= (@$runflat ? 'class="active"' : '') ?>><input
                                style="margin-right: 5px;float: left;" type="checkbox" id="_runflat" name="runflat"
                                value="1" <?= (@$runflat ? 'checked="checked"' : '') ?>>Технология Run-flat</label>
                    </div>
                    <input type="button" class="lfGo" value="найти">

                    <div class="loader"></div>
                    <div class="result-label ext"></div>
                </td>
            </tr>
        </table>
    </form>
</div>
<?endif;?>