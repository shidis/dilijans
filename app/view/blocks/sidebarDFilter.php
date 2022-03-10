<div class="box-filter-blue">
    <form action="/<?=App_Route::_getUrl('dSearch')?>.html" class="form-style-01 dsForm liveSB" chVars="1">
        <div class="title"><p><span>Поиск дисков</span>по типоразмеру</p><img src="/app/images/img-nav-filter-03.png" alt="">
        </div>
        <table>
            <tr>
                <td>Ширина:</td>
                <td>
                    <div class="select-01">
                        <span></span>
                        <select name="p2" id="" group="_p2"><?
                            ?><option value="">все</option><?
                            foreach($cc->s_arr['P2_2'] as $k=>$v){
                                ?><option id="_p2<?=$this->makeId($v)?>" value="<?=$v?>"<?=@$cc->filters_coo[2]['p2']==$v?' selected0="selected"':''?>><?=$v?></option><?
                            }
                            ?></select>
                        <i></i>
                    </div>
                </td>
            </tr>
            <tr>
                <td>Диаметр:</td>
                <td>
                    <div class="select-01">
                        <span></span>
                        <select name="p5" id="" group="_p5"><?
                            ?><option value="">все</option><?
                            foreach($cc->s_arr['P5_2'] as $k=>$v){
                                ?><option id="_p5<?=$this->makeId($v)?>" value="<?=$v?>"<?=@$cc->filters_coo[2]['p5']==$v?' selected0="selected"':''?>><?=$v?></option><?
                            }
                            ?></select>
                        <i></i>
                    </div>
                </td>
            </tr>
            <tr>
                <td>PCD:</td>
                <td>
                    <div class="select-01">
                        <span></span>
                        <select name="sv" id="" group="_sv"><?
                            ?><option value="">все</option><?
                            foreach($cc->s_arr['P4x6_2'] as $k=>$v){
                                ?><option id="_sv<?=$this->makeId($v)?>" value="<?=$v?>"<?=@$cc->filters_coo[2]['sv']==$v?' selected0="selected"':''?>><?=$v?></option><?
                            }
                            ?></select>
                        <i></i>
                    </div>
                </td>
            </tr>
            <tr>
                <td>Вылет:</td>
                <td>
                    <div class="select-01">
                        <span></span>
                        <select name="p1" id="" group="_p1"><?
                            ?><option value="">все</option><?
                            foreach($cc->s_arr['P1_2'] as $k=>$v){
                                ?><option id="_p1<?=$this->makeId($v)?>" value="<?=$v?>"<?=@$cc->filters_coo[2]['p1']==$v?' selected0="selected"':''?>><?=$v?></option><?
                            }
                            ?></select>
                        <i></i>
                    </div>
                </td>
            </tr>
            <tr class="last">
                <td colspan="2"><input type="button" value="найти" class="dsGo"><div class="loader"></div><div class="result-label"></div></td>
            </tr>
        </table>
    </form>

</div><?
