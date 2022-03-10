<div class="box-padding">

    <h1 class="title cat"><?=$_title?></h1><?

    if(!empty($topText)){
        ?><div class="ctext"><?
            echo $topText;
            ?></div><?
    }

    ?>

    <div class="filter-discs-size filter-discs-size_type_mobile">
        <div class="nav mobile-hide">
            <a href="#" class="active">
                <img src="/app/images/img-nav-filter-03.png" alt="">
                <b>Поиск дисков</b><br>по типоразмеру
            </a>
            <a href="#">
                <img src="/app/images/img-nav-filter-04.png" alt="">
                <b>Поиск разноразмерных дисков</b><br>по типоразмеру
            </a>
        </div>
        <div class="items">
			<button type="button" class="filter-discs-size__mobile-trg active">Поиск дисков по типоразмеру</button>
            <div class="filter-discs-size__panel active">
                <form action="/<?=App_Route::_getUrl('dSearch')?>.html" method="get" class="dsForm liveC" chVars="1">
                    <table>
                        <tr class="mobile-hide">
                            <td width="100px"><span>Диаметр<br>диска:</span></td>
                            <td width="30px">&nbsp;</td>
                            <td width="100px"><span>Ширина<br>диска(J):</span></td>
                            <td width="45px">&nbsp;</td>
                            <td><span>Крепежные<br>отверстия (LZxPСD):</span></td>
                            <td width="45px">&nbsp;</td>
                            <td><span>Вылет<br>(ET):</span></td>
                            <td width="45px">&nbsp;</td>
                            <td style="color:#93b0cd;"><span>Центральное<br>отверстие (DIA):</span></td>
                        </tr>
                        <tr class="td-fields-row">
                            <td>
								<div class="td-mobile-label">Диаметр диска:</div>
                                <div class="select-01">
                                    <span></span><select name="p5" group="_p5"><?
                                        ?><option value="">все</option><?
                                        foreach($cc->s_arr['P5_2'] as $k=>$v){
                                            ?><option id="_p5<?=$this->makeId($v)?>" value="<?=$v?>"<?=@$cc->filters_coo[2]['p5']==$v?' selected="selected"':''?>><?=$v?></option><?
                                        }
                                        ?></select>

                                    <i></i>
                                </div>
                            </td>
                            <td width="30px" style="text-align:center;"><div class="td-mobile-label">&nbsp;</div>x</td>
                            <td>
								<div class="td-mobile-label">Ширина диска (J):</div>
                                <div class="select-01">
                                    <span></span>
                                    <select name="p2" group="_p2"><?
                                        ?><option value="">все</option><?
                                        foreach($cc->s_arr['P2_2'] as $k=>$v){
                                            ?><option id="_p2<?=$this->makeId($v)?>" value="<?=$v?>"<?=@$cc->filters_coo[2]['p2']==$v?' selected="selected"':''?>><?=$v?></option><?
                                        }
                                        ?></select>
                                    <i></i>
                                </div>
                            </td>
                            <td width="45px" class="mobile-hide">&nbsp;</td>
                            <td>
								<div class="td-mobile-label">(LZxPСD):</div>
                                <div class="select-01">
                                    <span></span>
                                    <select name="sv" group="_sv"><?
                                        ?><option value="">все</option><?
                                        foreach($cc->s_arr['P4x6_2'] as $k=>$v){
                                            ?><option id="_sv<?=$this->makeId($v)?>" value="<?=$v?>"<?=@$cc->filters_coo[2]['sv']==$v?' selected="selected"':''?>><?=$v?></option><?
                                        }
                                        ?></select>
                                    <i></i>
                                </div>
                            </td>
                            <td width="45px" class="mobile-hide">&nbsp;</td>
                            <td>
								<div class="td-mobile-label">Вылет (ET):</div>
                                <div class="select-01">
                                    <span></span>
                                    <select name="p1" group="_p1"><?
                                        ?><option value="">все</option><?
                                        foreach($cc->s_arr['P1_2'] as $k=>$v){
                                            ?><option id="_p1<?=$this->makeId($v)?>" value="<?=$v?>"<?=@$cc->filters_coo[2]['p1']==$v?' selected="selected"':''?>><?=$v?></option><?
                                        }
                                        ?></select>
                                    <i></i>
                                </div>
                            </td>
                            <td width="45px" class="mobile-hide">&nbsp;</td>
                            <td>
								<div class="td-mobile-label">Центральное отверстие (DIA):</div>
                                <div class="select-02">
                                    <span></span>
                                    <select name="p3" group="_p3"><?
                                        ?><option value="">все</option><?
                                        foreach($cc->s_arr['P3_2'] as $k=>$v){
                                            ?><option id="_p3<?=$this->makeId($v)?>" value="<?=$v?>"<?=@$cc->filters_coo[2]['p3']==$v?' selected="selected"':''?>><?=$v?></option><?
                                        }
                                        ?></select>
                                    <i></i>
                                </div>
                            </td>
                        </tr>
                        <tr class="mobile-hide">
                            <td colspan="9" style="height:15px;">&nbsp;</td>
                        </tr>
                        <tr class="mobile-hide">
                            <td colspan="9"><span>Производитель дисков:</span></td>
                        </tr>
                        <tr class="td-fields-row">
                            <td colspan="3" class="td-wf">
								<div class="td-mobile-label">Производитель дисков:</div>
                                <div class="select-01">
                                    <span></span>
                                    <select name="vendor" group="_vendor"><?
                                        ?><option value="">все</option><?
                                        foreach($cc->s_arr['nor_brands_sname_2'] as $k=>$v){
                                            ?><option id="_vendor<?=$k?>" value="<?=$v['sname']?>"<?=@$cc->filters_coo[2]['vendor']==$v['sname']?' selected="selected"':''?>><?=$v['name']?></option><?
                                        }
                                        ?><optgroup label="Replica"><?
                                        foreach($cc->s_arr['r_brands_sname_2'] as $k=>$v){
                                            ?><option id="_vendor<?=$k?>" value="<?=$v['sname']?>"<?=@$cc->filters_coo[2]['vendor']==$v['sname']?' selected="selected"':''?>><?=$v['name']?></option><?
                                        }
                                        ?></optgroup>
                                    </select>
                                    <i></i>
                                </div>
                            </td>
                            <td colspan="6" class="td-control"><input type="button" value="Подобрать диски" class="dsGo"><div class="loader"></div> <div class="result-label"></div></td>
                        </tr>
                    </table>
                </form>
            </div>
            <button type="button" class="filter-discs-size__mobile-trg">Поиск разноразмерных дисков</button>
			<div>
                <form action="/<?=App_Route::_getUrl('dSearch')?>.html" method="get" class="dsForm dsForm_mobile" chVars="1" target="_blank">
                    <table>
                        <tr class="mobile-hide">
                            <td rowspan="5" width="50px" style="vertical-align:top;"><img src="/app/images/img-size.png" alt="" style="display:block; margin-top:38px;"></td>
                            <td><span>Ширина<br>перед:</span></td>
                            <td width="45px">&nbsp;</td>
                            <td><span>Диаметр<br>диска:</span></td>
                            <td width="35px">&nbsp;</td>
                            <td style="color:#93b0cd;"><span>Крепежные<br>отверстия (PCD):</span></td>
                            <td width="35px">&nbsp;</td>
                            <td style="color:#93b0cd;"><span>Центральное<br>отверстие:</span></td>
                        </tr>
                        <tr class="td-fields-row-next">
                            <td>
								<div class="td-mobile-label">Ширина перед:</div>
                                <div class="select-01">
                                    <span></span>
                                    <select name="p2" group="_p2"><?
                                        ?><option value="">все</option><?
                                        foreach($cc->s_arr['P2_2'] as $k=>$v){
                                            ?><option id="_p2<?=$this->makeId($v)?>" value="<?=$v?>"<?=@$cc->filters_coo[2]['p2']==$v?' selected="selected"':''?>><?=$v?></option><?
                                        }
                                        ?></select>
                                    <i></i>
                                </div>
                            </td>
                            <td width="45px" class="mobile-hide">&nbsp;</td>
                            <td>
								<div class="td-mobile-label">Диаметр диска:</div>
                                <div class="select-01">
                                    <span></span>
                                    <select name="p5" group="_p5"><?
                                        ?><option value="">все</option><?
                                        foreach($cc->s_arr['P5_2'] as $k=>$v){
                                            ?><option id="_p5<?=$this->makeId($v)?>" value="<?=$v?>"<?=@$cc->filters_coo[2]['p5']==$v?' selected="selected"':''?>><?=$v?></option><?
                                        }
                                        ?></select>
                                    <i></i>
                                </div>
                            </td>
                            <td width="35px" class="mobile-hide">&nbsp;</td>
                            <td class="td-mobile-lz">
								<div class="td-mobile-label">(LZxPCD):</div>
                                <div class="select-02">
                                    <span></span>
                                    <select name="sv" group="_sv"><?
                                        ?><option value="">все</option><?
                                        foreach($cc->s_arr['P4x6_2'] as $k=>$v){
                                            ?><option id="_sv<?=$this->makeId($v)?>" value="<?=$v?>"<?=@$cc->filters_coo[2]['sv']==$v?' selected="selected"':''?>><?=$v?></option><?
                                        }
                                        ?></select>
                                    <i></i>
                                </div>
                            </td>
                            <td width="35px" class="mobile-hide">&nbsp;</td>
                            <td>
								<div class="td-mobile-label">Центральное отверстие(DIA):</div>
                                <div class="select-02">
                                    <span></span>
                                    <select name="p3" group="_p3"><?
                                        ?><option value="">все</option><?
                                        foreach($cc->s_arr['P3_2'] as $k=>$v){
                                            ?><option id="_p3<?=$this->makeId($v)?>" value="<?=$v?>"<?=@$cc->filters_coo[2]['p3']==$v?' selected="selected"':''?>><?=$v?></option><?
                                        }
                                        ?></select>
                                    <i></i>
                                </div>
                            </td>
                        </tr>
                        <tr class="mobile-hide">
                            <td colspan="7" style="height:15px;">&nbsp;</td>
                        </tr>
                        <tr class="mobile-hide">
                            <td><span>Ширина<br>зад:</span></td>
                            <td width="45px">&nbsp;</td>
                            <td colspan="4"><span>Производитель<br>дисков:</span></td>
                        </tr>
                        <tr class="td-fields-row-next">
                            <td class="td-mobile-back">
								<div class="td-mobile-label">Ширина зад:</div>
                                <div class="select-01">
                                    <span></span>
                                    <select name="p2_" group="_p2"><?
                                        ?><option value="">все</option><?
                                        foreach($cc->s_arr['P2_2'] as $k=>$v){
                                            ?><option id="_p2<?=$this->makeId($v)?>" value="<?=$v?>"<?=@$cc->filters_coo[2]['p2']==$v?' selected="selected"':''?>><?=$v?></option><?
                                        }
                                        ?></select>
                                    <i></i>
                                </div>
                            </td>
                            <td width="45px" class="mobile-hide">&nbsp;</td>
                            <td colspan="3" class="td-mobile-make">
								<div class="td-mobile-label">Производитель дисков:</div>
                                <div class="select-01">
                                    <span></span>
                                    <select name="vendor" group="_vendor"><?
                                        ?><option value="">все</option><?
                                        foreach($cc->s_arr['nor_brands_sname_2'] as $k=>$v){
                                            ?><option id="_vendor<?=$k?>" value="<?=$v['sname']?>"<?=@$cc->filters_coo[2]['vendor']==$v['sname']?' selected="selected"':''?>><?=$v['name']?></option><?
                                        }
                                        ?><optgroup label="Replica"><?
                                            foreach($cc->s_arr['r_brands_sname_2'] as $k=>$v){
                                                ?><option id="_vendor<?=$k?>" value="<?=$v['sname']?>"<?=@$cc->filters_coo[2]['vendor']==$v['sname']?' selected="selected"':''?>><?=$v['name']?></option><?
                                            }
                                            ?></optgroup>
                                    </select>
                                    <i></i>
                                </div>
                            </td>
                            <td width="35px">&nbsp;</td>
                            <td><input type="button" value="Подобрать диски" class="dsGo"><div class="loader"></div> <div class="result-label"></div></td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>

</div>