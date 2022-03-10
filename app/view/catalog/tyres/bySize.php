<div class="box-padding">

    <h1 class="title cat"><?=$_title?></h1><?

    if(!empty($topText)){
        ?><div class="ctext"><?
            echo $topText;
            ?></div><?
    }

    ?>

    <div class="filter-types-size">
    <div class="nav">
        <a href="#" class="active">
            <img src="/app/images/img-nav-filter-05.png" alt="">
            <b>Поиск разноразмерных шин</b><br>по типоразмеру
        </a>
        <a href="#">
            <img src="/app/images/img-nav-filter-02.png" alt="">
            <b>Поиск шин</b><br>по типоразмеру
        </a>
    </div>
    <div class="items">
        <div class="active">
            <form action="/<?=App_Route::_getUrl('tSearch')?>.html" method="get" class="tsForm" chVars="1" sMode="1" target="_blank">
                <table>
                    <tr>
                        <td rowspan="10" width="70px" style="vertical-align:top;"><img src="/app/images/img-size2.png" alt="" style="display:block; margin-top:23px;"></td>
                        <td><span>Ширина перед:</span></td>
                        <td width="45px">&nbsp;</td>
                        <td><span>Высота шины:</span></td>
                        <td width="45px">&nbsp;</td>
                        <td><span>Диаметр шины:</span></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="select-01">
                                <span></span>
                                <select name="p3" group="_p3"><?
                                    ?><option value="">все</option><?
                                    foreach($cc->s_arr['P3_1'] as $k=>$v){
                                        ?><option id="_p3<?=$this->makeId($v)?>" value="<?=$v?>"<?//@$cc->filters_coo[1]['p3']==$v?' selected="selected"':''?>><?=$v?></option><?
                                    }
                                    ?></select>
                                <i></i>
                            </div>
                        </td>
                        <td width="45px" style="text-align:center;">/</td>
                        <td>
                            <div class="select-01">
                                <span></span>
                                <select name="p2" group="_p2"><?
                                    ?><option value="">все</option><?
                                    foreach($cc->s_arr['P2_1'] as $k=>$v){
                                        ?><option id="_p2<?=$this->makeId($v)?>" value="<?=$v?>"<?//@$cc->filters_coo[1]['p2']==$v?' selected="selected"':''?>><?=$v?></option><?
                                    }
                                    ?></select>
                                <i></i>
                            </div>
                        </td>
                        <td width="45px">&nbsp;</td>
                        <td>
                            <div class="select-01">
                                <span></span>
                                <select name="p1" group="_p1"><?
                                    ?><option value="">все</option><?
                                    foreach($cc->s_arr['P1_1'] as $k=>$v){
                                        ?><option id="_p1<?=$this->makeId($v)?>" value="<?=$v?>"<?//@$cc->filters_coo[1]['p1']==$v?' selected="selected"':''?>>R <?=$v?></option><?
                                    }
                                    ?></select>
                                <i></i>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" style="height:15px;"></td>
                    </tr>
                    <tr>
                        <td><span>Ширина зад:</span></td>
                        <td width="45px">&nbsp;</td>
                        <td><span>Высота шины:</span></td>
                        <td width="45px">&nbsp;</td>
                        <td><span>Диаметр шины:</span></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="select-01">
                                <span></span>
                                <select name="p3_" group="_p3"><?
                                    ?><option value="">все</option><?
                                    foreach($cc->s_arr['P3_1'] as $k=>$v){
                                        ?><option id="p3_<?=$this->makeId($v)?>" value="<?=$v?>"<?//@$cc->filters_coo[1]['p3_']==$v?' selected="selected"':''?>><?=$v?></option><?
                                    }
                                    ?></select>
                                <i></i>
                            </div>
                        </td>
                        <td width="45px" style="text-align:center;">/</td>
                        <td>
                            <div class="select-01">
                                <span></span>
                                <select name="p2_" group="_p2"><?
                                    ?><option value="">все</option><?
                                    foreach($cc->s_arr['P2_1'] as $k=>$v){
                                        ?><option id="p2_<?=$this->makeId($v)?>" value="<?=$v?>"<?//@$cc->filters_coo[1]['p2_']==$v?' selected="selected"':''?>><?=$v?></option><?
                                    }
                                    ?></select>
                                <i></i>
                            </div>
                        </td>
                        <td width="45px">&nbsp;</td>
                        <td>
                            <div class="select-01">
                                <span></span>
                                <select name="p1_" group="_p1"><?
                                    ?><option value="">все</option><?
                                    foreach($cc->s_arr['P1_1'] as $k=>$v){
                                        ?><option id="p1_<?=$this->makeId($v)?>" value="<?=$v?>"<?//@$cc->filters_coo[1]['p1_']==$v?' selected="selected"':''?>>R <?=$v?></option><?
                                    }
                                    ?></select>
                                <i></i>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" style="height:15px;"></td>
                    </tr>
                    <tr>
                        <td><span>Производитель шин</span></td>
                        <td width="45px">&nbsp;</td>
                        <td style="color:#9ba0a7;"><span>Сезон:</span></td>
                        <td width="45px">&nbsp;</td>
                        <td style="color:#9ba0a7;"><span>Шипы:</span></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="select-01">
                                <span></span>
                                <select name="vendor" group="_vendor"><?
                                    ?><option value="">все</option><?
                                    foreach($cc->s_arr['brands_sname_1'] as $k=>$v){
                                        ?><option id="_vendor<?=$k?>" value="<?=$v['sname']?>"<?//@$cc->filters_coo[1]['vendor']==$v['sname']?' selected="selected"':''?>><?=$v['name']?></option><?
                                    }
                                    ?></select>
                                <i></i>
                            </div>
                        </td>
                        <td width="45px" style="text-align:center;">/</td>
                        <td>
                            <div class="select-02">
                                <span></span>
                                <select name="mp1" group="_mp1">
                                    <option value="">все</option>
                                    <option id="_mp11" value="1"<?//@$cc->filters_coo[1]['mp1']==1?' selected="selected"':''?>>летняя</option>
                                    <option id="_mp12" value="2"<?//@$cc->filters_coo[1]['mp1']==2?' selected="selected"':''?>>зимняя</option>
                                    <option id="_mp13" value="3"<?//@$cc->filters_coo[1]['mp1']==3?' selected="selected"':''?>>всесезонная</option>
                                </select>
                                <i></i>
                            </div>
                        </td>
                        <td width="45px">&nbsp;</td>
                        <td>
                            <div class="select-02">
                                <span></span>
                                <select name="mp3" id="mp3" group="_mp3">
                                    <option value="">все</option>
                                    <option id="_mp31" value="1"<?//@$cc->filters_coo[1]['mp3']==1?' selected="selected"':''?>>есть шипы</option>
                                    <option id="_mp30" value="0"<?//@$cc->filters_coo[1]['mp3']==='0'?' selected="selected"':''?>>нет шипов</option>
                                </select>
                                <i></i>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" style="height:15px;"></td>
                    </tr>
                    <tr>
                        <td colspan="4"><label for="runflat"><input type="checkbox" id="runflat" name="runflat" value="1"<?//@$cc->filters_coo[1]['runflat']?' checked="checked"':''?>>Технология Run-flat <div class="help ntatip" rel="/ax/tip/runflat.html"></div></label></td>
                        <td><input type="button" value="Подобрать шины" class="tsGo"><div class="loader"></div> <div class="result-label"></div></td>
                    </tr>
                </table>
            </form>
        </div>
        <div>
            <form action="/<?=App_Route::_getUrl('tSearch')?>.html" method="get" class="tsForm liveC" chVars="1">
                <table>
                    <tr>
                        <td width="33%"><span>Ширина шины:</span></td>
                        <td width="5%">&nbsp;</td>
                        <td width="33%"><span>Высота шины:</span></td>
                        <td width="5%">&nbsp;</td>
                        <td width="33%"><span>Диаметр шины:</span></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="select-01">
                                <span></span>
                                <select name="p3" group="_p3"><?
                                    ?><option value="">все</option><?
                                    foreach($cc->s_arr['P3_1'] as $k=>$v){
                                        ?><option id="_p3<?=$this->makeId($v)?>" value="<?=$v?>"<?//@$cc->filters_coo[1]['p3']==$v?' selected="selected"':''?>><?=$v?></option><?
                                    }
                                    ?></select>
                                <i></i>
                            </div>
                        </td>
                        <td style="text-align:center; width:45px;">/</td>
                        <td>
                            <div class="select-01">
                                <span></span>
                                <select name="p2" group="_p2"><?
                                    ?><option value="">все</option><?
                                    foreach($cc->s_arr['P2_1'] as $k=>$v){
                                        ?><option id="_p2<?=$this->makeId($v)?>" value="<?=$v?>"<?//@$cc->filters_coo[1]['p2']==$v?' selected="selected"':''?>><?=$v?></option><?
                                    }
                                    ?></select>
                                <i></i>
                            </div>
                        </td>
                        <td width="45px">&nbsp;</td>
                        <td>
                            <div class="select-01">
                                <span></span>
                                <select name="p1" group="_p1"><?
                                    ?><option value="">все</option><?
                                    foreach($cc->s_arr['P1_1'] as $k=>$v){
                                        ?><option id="_p1<?=$this->makeId($v)?>" value="<?=$v?>"<?//@$cc->filters_coo[1]['p1']==$v?' selected="selected"':''?>>R <?=$v?></option><?
                                    }
                                    ?></select>
                                <i></i>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" style="height:15px;"></td>
                    </tr>
                    <tr>
                        <td><span>Производитель шин</span></td>
                        <td width="45px">&nbsp;</td>
                        <td style="color:#9ba0a7;"><span>Сезон:</span></td>
                        <td width="45px">&nbsp;</td>
                        <td style="color:#9ba0a7;"><span>Шипы:</span></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="select-01">
                                <span></span>
                                <select name="vendor" group="_vendor"><?
                                    ?><option value="">все</option><?
                                    foreach($cc->s_arr['brands_sname_1'] as $k=>$v){
                                        ?><option id="_vendor<?=$k?>" value="<?=$v['sname']?>"<?//@$cc->filters_coo[1]['vendor']==$v['sname']?' selected="selected"':''?>><?=$v['name']?></option><?
                                    }
                                ?></select>
                                <i></i>
                            </div>
                        </td>
                        <td width="45px">&nbsp;</td>
                        <td>
                            <div class="select-02">
                                <span></span>
                                <select name="mp1" group="_mp1">
                                    <option value="">все</option>
                                    <option id="_mp11" value="1"<?//@$cc->filters_coo[1]['mp1']==1?' selected="selected"':''?>>летняя</option>
                                    <option id="_mp12" value="2"<?//@$cc->filters_coo[1]['mp1']==2?' selected="selected"':''?>>зимняя</option>
                                    <option id="_mp13" value="3"<?//@$cc->filters_coo[1]['mp1']==3?' selected="selected"':''?>>всесезонная</option>
                                </select>
                                <i></i>
                            </div>
                        </td>
                        <td width="45px">&nbsp;</td>
                        <td>
                            <div class="select-02">
                                <span></span>
                                <select name="mp3" id="mp3" group="_mp3">
                                    <option value="">все</option>
                                    <option id="_mp31" value="1"<?//@$cc->filters_coo[1]['mp3']==1?' selected="selected"':''?>>есть шипы</option>
                                    <option id="_mp30" value="0"<?//@$cc->filters_coo[1]['mp3']==='0'?' selected="selected"':''?>>нет шипов</option>
                                </select>
                                <i></i>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" style="height:15px;"></td>
                    </tr>
                    <tr>
                        <td colspan="1" group="_runflat"><label for="_runflat"><input type="checkbox" id="_runflat" name="runflat" value="1"<?//@$cc->filters_coo[1]['runflat']?' checked="checked"':''?>>Технология Run-flat <div class="help ntatip" rel="/ax/tip/runflat.html"></div></label></td>
                        <td colspan="4"><input type="button" value="Подобрать шины" class="tsGo"><div class="loader"></div> <div class="result-label"></div></td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
    </div>


</div>