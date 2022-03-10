<div class="box-filter-black">
    <form action="#" class="form-style-01 apForm">
        <div class="title"><p><span>Подбор</span> по марке авто</p><img src="/app/images/img-nav-filter-01.png" alt=""></div>
        <table>
            <tr>
                <td width="60px">Марка:</td>
                <td>
                    <div class="select-01">
                        <span></span>
                        <select class="apMark"><option value="">Не выбрано</option><?
                            if(isset($ab->tree['vendors'])){
                                foreach($ab->tree['vendors'] as $k=>$v){
                                    ?><option value="<?=$v['sname']?>" <?=$abCookie['svendor']==$v['sname']?'selected':''?>><?=Tools::html($v['name'],false)?></option><?
                                }
                            }
                            ?></select>
                        <i></i>
                    </div>
                </td>
            </tr>
            <tr>
                <td>Модель:</td>
                <td>
                    <div class="select-01">
                        <span></span>
                        <select class="apModel"><option value="">Не выбрано</option><?
                            if(isset($ab->tree['models'])){
                                foreach($ab->tree['models'] as $k=>$v){
                                    ?><option value="<?=$v['sname']?>" <?=$abCookie['smodel']==$v['sname']?'selected':''?>><?=Tools::html($v['name'],false)?></option><?
                                }
                            }
                            ?></select>
                        <i></i>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="grey">Год вып.:</td>
                <td>
                    <div class="select-02">
                        <span></span>
                        <select class="apYear"><option value="">Не выбрано</option><?
                            if(isset($ab->tree['years'])){
                                foreach($ab->tree['years'] as $k=>$v){
                                    ?><option value="<?=$v['sname']?>" <?=$abCookie['syear']==$v['sname']?'selected':''?>><?=Tools::html($v['name'],false)?></option><?
                                }
                            }
                            ?></select>
                        <i></i>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="grey">Мод-ция:</td>
                <td>
                    <div class="select-02">
                        <span></span>
                        <select class="apModif"><option value="">Не выбрано</option><?
                            if(isset($ab->tree['modifs'])){
                                foreach($ab->tree['modifs'] as $k=>$v){
                                    ?><option value="<?=$v['sname']?>" <?=$abCookie['smodif']==$v['sname']?'selected':''?>><?=Tools::html($v['name'],false)?></option><?
                                }
                            }
                            ?></select>
                        <i></i>
                    </div>
                </td>
            </tr>
            <tr class="last">
                <td></td>
                <td><input type="button" value="найти" class="apGo"></td>
            </tr>
        </table>
    </form>
</div>
