<script type="text/javascript">
    $( document ).ready(function() {
        if ($('.hidden_wrapper input[type="checkbox"]:checked').length == 0) 
        { 
            $('.hidden_wrapper_title').click(function(){
                $('.hidden_wrapper').css('display', 'inline');
                $(this).hide();
            });
        }
        else
        {
            $('.hidden_wrapper').css('display', 'inline');
            $('.hidden_wrapper_title').hide();
        }
        $('.ext_params_switcher').click(function(){
			$(this).toggleClass('ext_params_switcher_active');
            $('.search_result_box_wrap').toggle('fast', function(){
                if ($(this).is(':visible'))
                {
                    $('.ext_params_switcher > span')[0].innerHTML = '&#8963;';
                    $('.ext_filter').fadeOut('fast');
                }
                else{
                    $('.ext_params_switcher > span')[0].innerHTML = '&#8964;';
                    $('.ext_filter').fadeIn('fast');
                }
            }); 
        });
    });
</script>
<div class="ext_params_switcher">
    <a>
		<span class="ext_params_switcher_mobile_name">Показать параметры дисков</span>
		параметры дисков для <?=@$mark.' '.@$model.' '.@$modif.' '.(@$year ? @$year.' г/в' : '')?></a><span>&#8964;</span>
</div>
<div class="search_result_box_wrap">
    <div class="search_result_box blue">
        <table>
            <tbody>
                <tr class="mobile-hide">
                    <th></th>
                    <th>
                        <b>Размеры дисков для <?=@$mark.' '.@$model.' '.@$modif.' '.@$year?>.</b>
                    </th>
                </tr>  
                <tr>
                    <td colspan="2" class="disks_title">
                        <b>Общие параметры</b>
                        <table class="common-params-row">
                            <?
                            if (is_array(@$abc[0]))
                            {
                                foreach($abc as $a):?>
                                    <tr>
                                        <td>PCD: <?=@$a['pcd']?> </td>
                                        <td>DIA: <?=@$a['dia']?> мм</td><?
                                        if(!empty($a['gaika'])){
                                            ?><td>Гайка: <?=$a['gaika']?></td><?
                                        }
                                        if(!empty($a['bolt'])){
                                            ?><td>Болт: <?=$a['bolt']?></td><?
                                        }
                                    ?></tr>
                                    <?endforeach;
                            }
                            else
                            {
                                ?>
                                <tr>
                                    <td>PCD: <?=@$abc['pcd']?> </td>
                                    <td>DIA: <?=@$abc['dia']?> мм</td><?
                                    if(!empty($abc['gaika'])){
                                        ?><td>Гайка: <?=$abc['gaika']?></td><?
                                    }
                                    if(!empty($abc['bolt'])){
                                        ?><td>Болт: <?=$abc['bolt']?></td><?
                                    }
                                ?></tr>
                                <? 
                            }
                            ?>
                        </table>
                    </td>
                </tr>                 
                <tr class="mobile-hide">
                    <td></td>
                    <td><ul class="title">
                            <li style="float: left;">Передняя ось/обе оси</li>
                            <li style="float: right;">Задняя ось</li>
                        </ul>
                    </td>
                </tr>       
                <?
                foreach($sz2 as $rad=>$v)
                {
                    ?>
                    <tr class="params-group">
                        <td><div class="radius"><span>R<?=$rad?></span></div></td>
                        <?
                        $type0='';
                        ?><td><ul><?
                                foreach($v as $type=>$vv)
                                {
                                    foreach($vv as $row)
                                    {
                                        ?><li><?
                                            if(!empty($row[2]))
                                            {
                                                if ($row['exnum'] > 0) {
                                                    ?><div class="r-box"><img src="/app/images/car-l.png" height="45" alt=""><img height="45" src="/app/images/car-r.png" alt=""><img src="/app/images/chain.png" alt="" class="chant"></div><?
                                                    ?><a href="<?=$dSearchUrl.'?ap=1&p2='.$row[1]['P2'].'&p5='.$row[1]['P5'].'&p1='.$row[1]['P1'].'&p4='.$row[1]['P4'].'&p6='.$row[1]['P6'].'&p3='.$row[1]['P3'].'&p2_='.$row[2]['P2'].'&p5_='.$row[2]['P5'].'&p1_='.$row[2]['P1'].'&p4_='.$row[2]['P4'].'&p6_='.$row[2]['P6'].'&p3_='.$row[2]['P3']?>"><?
                                                        ?><span><b><?=$row[1]['P2'].' x '.$row[1]['P5'].' ET'.$row[1]['P1']?></b><span class="amount-goods mobile-hide"><?='('.$row['exnum'].' шт.)'?></span></span><?
                                                        ?><span class="only-mobile r-separator">=</span><span><?=$row[2]['P2'].' x '.$row[2]['P5'].' ET'.$row[2]['P1']?></span> &nbsp;<span class="amount-goods"><?='('.$row['exnum'].' шт.)'?></span></span><?
                                                    ?></a><?
                                                } else {
                                                    ?><span class="empty-link"><?
                                                        ?><span><?=$row[1]['P2'].' x '.$row[1]['P5'].' ET'.$row[1]['P1']?></span><?
                                                        ?><span><?=$row[2]['P2'].' x '.$row[2]['P5'].' ET'.$row[2]['P1']?></span><?
                                                    ?></span><?
                                                }
                                            }
                                            else 
                                            {
                                                if ($row[1]['exnum'] > 0) {
                                                    ?><div class="r-box"><img src="/app/images/chain.png" alt="" class="chant"></div><?
                                                    ?><a href="<?=$dSearchUrl.'?ap=1&p2='.$row[1]['P2'].'&p5='.$row[1]['P5'].'&p1='.$row[1]['P1'].'&p4='.$row[1]['P4'].'&p6='.$row[1]['P6'].'&p3='.$row[1]['P3']?>"><?
                                                        ?><span><b><?=$row[1]['P2'].' x '.$row[1]['P5'].' ET'.$row[1]['P1']?></b> &nbsp;<span class="amount-goods"><?='('.$row[1]['exnum'].' шт.)'?></span></span><?
                                                    ?></a><?
                                                } else {
                                                    ?><span class="empty-link"><?
                                                        ?><span><?=$row[1]['P2'].' x '.$row[1]['P5'].' ET'.$row[1]['P1']?></span><?
                                                    ?></span><?
                                                }
                                            }
                                        ?></li><?
                                    }

                                }
                            ?></ul></td><?
                    ?></tr><?
                }?>
            </tbody>
        </table>
    </div>
</div>
</div>
<?
if(!empty($lfi) && App_Route::_getAction()!='search'){
    ?>

    <div style="padding: 16px 20px;" class="box-grey-01 ext_filter">
        <form action="<?=App_Route::_getCurURL()?>" class="form-style-01<?=!@$sMode?' livef':''?>">
        <? Url::arr2hiddenFields($lfh);?>

        <table class="model_search_filter"><?
        $i=0;
        if(!empty($lf['_p5'])){
            $i++
            ?><tr<?=$i==$lfi?' class="last"':''?> group="_p5">
                <td class="black" width="120px"><b>Диаметры:</b></td>
                <td class="black"><?    
                    foreach($lf['_p5'] as $k=>$v){
                        ?><label<?=$v['chk']?' class="active"':''?>><input type="checkbox"<?=$v['chk']?' checked':''?> name="_p5[<?=$k?>]" id="<?=$v['id']?>"   value="1"><i></i><span class="sp-label-wrapper"><button type="submit" class="mobile-result-label">Найти <span class="mobile-result-label__value"> <b>-</b> шт.</span></button><?=$v['anc']?></span></label><span class="sp-label-separator"></span><?
                    }
                ?></td><?
            ?></tr><tr><td colspan="2"><hr></td></tr><?
        }        
        if(!empty($lf['_rbids']))
        {
            ?><tr<?=$i==$lfi?' class="last"':''?> group="_p5">
            <td class="black" width="120px" style="padding-top: 16px; padding-bottom: 0px;"><b>Диски Replica:</b></td>
            <td class="black" style="padding-top: 16px; padding-bottom: 0px;"><?
            foreach($lf['_rbids'] as $k=>$v)
            {
                ?><!--<div class="replica">--></div><label<?=$v['chk']?' class="active"':''?> style="width: 95px;"><input type="checkbox"<?=$v['chk']?' checked':''?> name="_bids[<?=$k?>]"  id="<?=$v['id']?>" value="1"><i></i><span class="sp-label-wrapper"><button type="submit" class="mobile-result-label">Найти <span class="mobile-result-label__value"> <b>-</b> шт.</span></button><?=$v['anc']?></span></label><span class="sp-label-separator"></span><?
        }
        ?></td><?
        ?></tr><tr><td colspan="2"><hr></td></tr><?
    }
    if(!empty($lf['_bids']))
    {
        $i++         
        ?><tr<?=$i==$lfi?' class="last"':''?> group="_bids">
            <td class="black" width="137px" style="padding-top: 16px;"><b>Производители:</b></td>
            <td class="black" style="padding-top: 16px;">
                <?
                $j=0;        
                foreach($lf['_bids'] as $k=>$v){ 
                    if (!empty($lf['valid_brands']) && in_array($k,$lf['valid_brands']))
                    {
                        $j++;
                        if ($j==12 && count($lf['_bids']) > 12)
                        {
                            echo '<div class="hidden_wrapper_title"><span class="hidden_wrapper_title_label">еще варианты ('.(count($lf['valid_brands']) - 11).')</span></div>';
                            echo '<div class="hidden_wrapper" style="display: none;"><div class="hidden_wrapper_panel">';
                        }
                        ?><label<?=$v['chk']?' class="active"':''?>><input type="checkbox"<?=$v['chk']?' checked':''?> name="_bids[<?=$k?>]" id="<?=$v['id']?>"  value="1"><i></i><span class="sp-label-wrapper"><button type="submit" class="mobile-result-label">Найти <span class="mobile-result-label__value"> <b>-</b> шт.</span></button><?=$v['anc']?></span></label><span class="sp-label-separator"></span><?   
                    }
                } 
                if ($j>11 && count($lf['_bids']) > 12)
                {
                    echo '</div></div>';
                }  
            ?></td><?
        ?></tr><?
    }
    ?>
    <tr class="last">
        <td colspan="2"><input type="button" class="lfGo new_design" value="Найти"><div class="loader" style="margin-top: 5px; float: left !important;"></div> <div class="result-label ext new_design"></div></td>
    </tr>
    </table>
    <input type="hidden" name="submited" value="1" />
    </form>
    </div>

    <? }?>
<?if (!empty($s_info_str)) echo $s_info_str;?>
<div class="box-shadow">

    <? $this->incView($searchTpl); ?>

</div>