<div class="box-filter-grey">
    <form action="/<?=App_Route::_getUrl('tSearch')?>.html" class="form-style-01 tsForm liveSB" chVars="1">
        <div class="title"><p><span>Поиск шин</span>по типоразмеру</p><img src="/app/images/img-nav-filter-02.png" alt=""></div>
        <table>
            <tr width="60px">
                <td>Бренд:</td>
                <td>
                    <div class="select-01">
                        <span></span>
                        <select name="vendor" group="_vendor"><?
                            ?><option value="">все</option><?
                            foreach($cc->s_arr['brands_sname_1'] as $k=>$v){
                                ?><option id="_vendor<?=$k?>" value="<?=$v['sname']?>"<?=@$cc->filters_coo[1]['vendor']==$v['sname']?' selected0="selected"':''?>><?=$v['name']?></option><?
                            }
                            ?></select>
                        <i></i>
                    </div>
                </td>
            </tr>
            <tr>
                <td>Размер:</td>
                <td>
                    <table>
                        <tr>
                            <td>
                                <div class="select-01">
                                    <span></span>
                                    <select name="p3" group="_p3"><?
                                        ?><option value="">все</option><?
                                        foreach($cc->s_arr['P3_1'] as $k=>$v){
                                            ?><option id="_p3<?=$this->makeId($v)?>" value="<?=$v?>"<?=@$cc->filters_coo[1]['p3']==$v?' selected0="selected"':''?>><?=$v?></option><?
                                        }
                                        ?></select>
                                    <i></i>
                                </div>
                            </td>
                            <td style="width:24px; text-align:center;">/</td>
                            <td>
                                <div class="select-01">
                                    <span></span>
                                    <select name="p2" group="_p2"><?
                                        ?><option value="">все</option><?
                                        foreach($cc->s_arr['P2_1'] as $k=>$v){
                                            ?><option id="_p2<?=$this->makeId($v)?>" value="<?=$v?>"<?=@$cc->filters_coo[1]['p2']==$v?' selected0="selected"':''?>><?=$v?></option><?
                                        }
                                        ?></select>
                                    <i></i>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>Радиус:</td>
                <td>
                    <div class="select-01">
                        <span></span>
                        <select name="p1" group="_p1"><?
                            ?><option value="">все</option><?
                            foreach($cc->s_arr['P1_1'] as $k=>$v){
                                ?><option id="_p1<?=$this->makeId($v)?>" value="<?=$v?>"<?=@$cc->filters_coo[1]['p1']==$v?' selected0="selected"':''?>><?=$v?></option><?
                            }
                            ?></select>
                        <i></i>
                    </div>
                </td>
            </tr>
            <tr>
                <td>Сезон:</td>
                <td>
                    <div class="select-01">
                        <span></span>
                        <select name="mp1" group="_mp1">
                            <option value="">все</option>
                            <option id="_mp11" value="1"<?=@$cc->filters_coo[1]['mp1']==1?' selected0="selected"':''?>>летняя</option>
                            <option id="_mp12" value="2"<?=@$cc->filters_coo[1]['mp1']==2?' selected0="selected"':''?>>зимняя</option>
                            <option id="_mp13" value="3"<?=@$cc->filters_coo[1]['mp1']==3?' selected0="selected"':''?>>всесезонная</option>
                        </select>
                        <i></i>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="grey">Шипы:</td>
                <td>
                    <div class="select-02">
                        <span></span>
                        <select name="mp3" id="mp3" group="_mp3">
                            <option value="">все</option>
                            <option id="_mp31" value="1"<?=@$cc->filters_coo[1]['mp3']==1?' selected0="selected"':''?>>есть шипы</option>
                            <option id="_mp30" value="0"<?=@$cc->filters_coo[1]['mp3']==='0'?' selected0="selected"':''?>>нет шипов</option>
                        </select>
                        <i></i>
                    </div>
                </td>
            </tr>
            <tr class="last">
                <td colspan="2"><input type="button" value="найти" class="tsGo"><div class="loader"></div><div class="result-label"></div></td>
            </tr>
        </table>
    </form>
</div>
