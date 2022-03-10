<div class="box-padding">

    <h1 class="title cat"><?=$_title?></h1><?

    if(!empty($topText)){
        ?><div class="ctext"><?
            echo $topText;
        ?></div><?
    }

    ?>
    <script type="text/javascript">
        $( document ).ready(function() {
            $('.change_typo_switcher').click(function(){
                $('.change_typo_wrap').toggle('fast', function(){
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
    <div class="change_typo_switcher"><img src="/app/images/img-nav-filter-03.png" alt="" width="23" /><a>Изменить типоразмер</a><span>&#8964;</span></div>
    <div class="change_typo_wrap">
        <!--************* Новые фильтры *************-->
        <div id="filter" class="disks_catalog_filter">
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
                    <div>
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
                    <div  class="active">
                        <form action="/<?=App_Route::_getUrl('dSearch')?>.html" method="get" class="form-style-01 dsForm liveC" chVars="1">
                            <table>
                                <tr>
                                    <td>Диаметр диска (R*):<br>
                                        <div class="select-01">
                                            <span></span>
                                            <select name="p5" group="_p5"><?
                                                ?><option value="">все</option><?
                                                foreach($cc->s_arr['P5_2'] as $k=>$v){
                                                    ?><option id="_p5<?=$this->makeId($v)?>" value="<?=$v?>"<?=@$P5[0]==$v?' selected="selected"':''?>><?=$v?></option><?
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
                                                    ?><option id="_sv<?=$this->makeId($v)?>" value="<?=$v?>"<?=@$P46[0][0].'x'.@$P46[0][1]==$v?' selected="selected"':''?>><?=$v?></option><?
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
                                                    ?><option id="_vendor<?=$k?>" value="<?=$v['sname']?>"<?=@$brand_sname==$v['sname']?' selected="selected"':''?>>Replica <?=$v['name']?></option><?
                                                }
                                                foreach($cc->s_arr['nor_brands_sname_2'] as $k=>$v){
                                                    ?><option id="_vendor<?=$k?>" value="<?=$v['sname']?>"<?=@$brand_sname==$v['sname']?' selected="selected"':''?>><?=$v['name']?></option><?
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
                                                    ?><option id="_p2<?=$this->makeId($v)?>" value="<?=$v?>"<?=@$P2[0]==$v?' selected="selected"':''?>><?=$v?></option><?
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
                                                    ?><option id="_p1<?=$this->makeId($v)?>" value="<?=$v?>"<?=@$P1[0]==$v?' selected="selected"':''?>><?=$v?></option><?
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
                                                    ?><option id="_p3<?=$this->makeId($v)?>" value="<?=$v?>"<?=@$P3[0]==$v?' selected="selected"':''?>><?=$v?></option><?
                                                }
                                            ?></select>
                                            <i></i>
                                        </div>
                                        <label for="ap" class="checkbox-01" style="font-weight: 700; margin-top: 13px; float: right; margin-right: 35px;"><input type="checkbox" undisabled="1" value="1" id="ap" name="ap" <?=(($apMode) ? 'checked="true"' : '')?>>и более</label>
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
    <!--  ********************  -->
    <?
    if(!empty($lfi) && App_Route::_getAction()!='search'){
        ?>
        <div class="box-grey-01 ext_filter">
            <form action="" class="form-style-01 livef">

                <? Url::arr2hiddenFields($lfh);?>

                <table><?
                    $i=0;
                    if(!empty($lf['_p5'])){
                        $i++
                        ?><tr<?=$i==$lfi?' class="last"':''?> group="_p5">
                            <td class="black" width="120px">Диаметры:</td>
                            <td class="black"><?
                                foreach($lf['_p5'] as $k=>$v){
                                    ?><label<?=$v['chk']?' class="active"':''?>><input type="checkbox"<?=$v['chk']?' checked':''?> name="_p5[<?=$k?>]" id="<?=$v['id']?>"   value="1"><i></i><?=$v['anc']?></label><?
                                }
                            ?></td><?
                        ?></tr><?
                    }

                    if(!empty($lf['_sv'])){
                        $i++
                        ?><tr<?=$i==$lfi?' class="last"':''?> group="_sv">
                            <td class="black" width="120px">Сверловки:</td>
                            <td class="black"><?
                                foreach($lf['_sv'] as $k=>$v){
                                    ?><label<?=$v['chk']?' class="active"':''?>><input type="checkbox"<?=$v['chk']?' checked':''?> name="_sv[<?=$k?>]" id="<?=$v['id']?>"  value="1"><i></i><?=$v['anc']?></label><?
                                }
                            ?></td><?
                        ?></tr><?
                    }
                    ?>
                    <tr class="last">
                        <td colspan="2"><input type="button" style="display: none;" class="lfGo" value="найти"><div class="loader"></div> <div class="result-label ext"></div></td>
                    </tr>
                </table>
            </form>
        </div>

        <? }?>

    <div class="box-shadow">

        <? $this->incView($searchTpl); ?>

    </div>

</div>