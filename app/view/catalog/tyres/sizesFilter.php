<!--************* Новые фильтры *************-->
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
            <div  class="active">
                <? if(!empty($markir)){?>

                    <div class="tmarkir">
                        <div class="i">
                            <div class="im1"></div>
                            <div class="im2"></div>
                            <div class="im3"></div>
                            <img src="/app/images/tmarkir.png" width="200" height="53">
                        </div>
                        <div class="t">
                            <p><?=$markir['text']?></p>
                        </div>
                    </div>

                <? }?>
                <!--<div class="tmarkir__p1"></div>
                <div class="tmarkir__p2"></div>
                <div class="tmarkir__p3"></div>-->
                <form action="/<?=App_Route::_getUrl('tSearch')?>.html" class="form-style-01 tsForm livef" chVars="1" onload_refresh="true" ><?
                    foreach($filterHF as $k=>$v){
                        ?><input type="hidden" name="<?=$k?>" value="<?=$v?>"><?
                    }
                    ?><table>
                        <tr class="ftsl-row">
                            <td><!--<img src="/app/images/img-text-04.png" alt="">--></td>
                            <td width="135px" class="grey2">Выберите размер:</td>
                            <td class="ftsl-w1">
                                <div class="select-01">
                                    <span></span>
                                    <select name="p3" id="" group="_p3" class="pp1"><?
                                        ?><option value="">ширина</option><?
                                        if(count($filter['P3'])==1){
                                            ?><option<?=$filter['P3'][0]==Url::$sq['p3']?' selected="selected"':''?> id="_p3<?=$this->makeId($filter['P3'][0])?>" value="<?=$filter['P3'][0]?>"><?=$filter['P3'][0]?></option><?
                                        }else{
                                            foreach ($filter['P3'] as $k => $v) {
                                                ?>
                                                <option id="_p3<?= $this->makeId($v) ?>" value="<?= $v ?>"><?= $v ?></option><?
                                            }
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
                                        ?><option value="">профиль</option><?
                                        if(count($filter['P2'])==1){
                                            ?><option<?=$filter['P2'][0]==Url::$sq['p2']?' selected="selected"':''?> id="_p2<?=$this->makeId($filter['P2'][0])?>" value="<?=$filter['P2'][0]?>"><?=$filter['P2'][0]?></option><?
                                        }else {
                                            foreach ($filter['P2'] as $k => $v) {
                                                ?><option id="_p2<?= $this->makeId($v) ?>" value="<?= $v ?>"><?= $v ?></option><?
                                            }
                                        }
                                        ?></select>
                                    <i></i>
                                </div>
                            </td>
                            <td width="39px" class="grey2 ftsl-w2" style="text-align:center;padding-top: 10px;"><div class="help2 ntatip" rel="/ax/tip/sizesFilter.html"></div>R</td>
                            <td  class="ftsl-w3">
                                <div class="select-01">
                                    <span></span>
                                    <select name="p1" id="" group="_p1" class="pp3"><?
                                        ?><option value="">диаметр</option><?
                                        if(count($filter['P1'])==1){
                                            ?><option<?=$filter['P1'][0]==Url::$sq['p1']?' selected="selected"':''?> id="_p1<?=$this->makeId($filter['P1'][0])?>" value="<?=$filter['P1'][0]?>"><?=$filter['P1'][0]?></option><?
                                        }else {
                                            foreach ($filter['P1'] as $k => $v) {
                                                ?><option id="_p1<?= $this->makeId($v) ?>" value="<?= $v ?>"><?= $v ?></option><?
                                            }
                                        }
                                        ?></select>
                                    <i></i>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <table>
                        <?
                        if(!empty($lf)){
                            $i=0;
                            if(!empty($lf['mp1'])){
                                $i++
                                ?><tr<?=$i==$lfi?' class="last"':''?>>
                                <td></td>
                                <td class="grey2" width="145px">Сезонность:</td>
                                <td class="black" group="_mp1"<?=!empty($lf['_mp3'])?' style="padding-bottom:0"':''?>><?
                                foreach($lf['mp1'] as $k=>$v){
                                    ?><label class="brands-sezon<?=$v['chk']?' active':''?>"><input type="checkbox"<?=$v['chk']?' checked':''?> name="mp1[<?=$k?>]" id="<?=$v['id']?>" value="1"><i></i><?=$v['anc']?></label><?
                                }
                                ?></td><?
                                ?></tr><?
                            }
                            if(!empty($lf['mp3'])){
                                $i++
                                ?><tr<?=$i==$lfi?' class="last"':''?>>
                                <td class="mobile-hide"></td>
                                <td class="grey2 mobile-hide" width="145px"></td>
                                <td class="black" group="_mp3"><?
                                foreach($lf['mp3'] as $k=>$v){
                                    ?><label class="brands-ship<?=$v['chk']?' active':''?>"><input type="checkbox"<?=$v['chk']?' checked':''?> name="_mp3[<?=$k?>]" id="<?=$v['id']?>" value="1"><i></i><?=$v['anc']?></label><?
                                }
                                ?></td><?
                                ?></tr><?
                            }
                        }
                        ?>

                        <tr class="last">
                            <td class="mobile-hide"></td>
                            <td class="mobile-hide"></td>
                            <td class="black">
                                <div group="_c_index" style="float: left; margin-top: 6px;"><label style="width: 220px;margin-right: 40px;" for="_c_index" <?=(@$c_index ? 'class="active"' : '')?>><input style="margin-right: 5px;float: left;" type="checkbox" id="_c_index" name="c_index" value="1" <?=(@$c_index ? 'checked="checked"' : '')?>>Легкогрузовая шина (индекс C)</label></div>
                                <div group="_runflat" style="float: left; margin-top: 6px;"><label style="width: 220px;" for="_runflat" <?=(@$runflat ? 'class="active"' : '')?>><input style="margin-right: 5px;float: left;" type="checkbox" id="_runflat" name="runflat" value="1" <?=(@$runflat ? 'checked="checked"' : '')?>>Технология Run-flat</label></div>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td><input type="button" style="float: left" class="tsGo" value="найти"><div style="float: left" class="loader"></div> <div style="position: absolute; left: 75px;margin-top:0px;" class="result-label ext"></div></td>
                        </tr>
                    </table>
                    <input type="hidden" name="submited" value="1" />
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
<!--************* /Новые фильтры *************-->
