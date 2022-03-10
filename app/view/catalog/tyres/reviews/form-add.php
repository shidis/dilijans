<div class="box-grey review-form" rid="<?=@$ritem['id']?>">
    <h5 class="h-form"><a href="#" class="btn-reset">очистить форму</a>Мой отзыв о <?=$bname.' '.$mname?></h5>
    <form action="#" class="form-style-02">
        <table>
            <tr>
                <td width="170px">Ваша оценка резины</td>
                <td>
                    <ul class="stars rating-s" fieldname="rating" v="<?=ceil(@$ritem['rating'])?>"></ul>
                    <div class="rating-n"><?=sprintf("%01.1f", (float)@$ritem['rating'])?></div>
                </td>
                <td style="padding-left:44px; width:190px;" rowspan="6">

                    <h6>Подробные оценки</h6>

                    <? foreach($revRatingItems as $k=>$v){?>

                        <div class="vi">
                            <label><?=$v?></label>
                            <ul class="stars stars-dash" fieldname="vals" fieldkey="<?=$k?>" v="<?=(int)@$ritem['vals'][$k]?>"></ul>
                        </div>

                    <? }?>

                </td>
            </tr>
            <tr>
                <td>Достоинства</td>
                <td>
                    <div class="input" for="advants"><input type="text" name="advants" value="<?=@$ritem['advants']?>"><img src="/app/images/uncorrect.png" alt=""><img src="/app/images/correct.png" alt=""></div>
                </td>
            </tr>
            <tr>
                <td>Недостатки</td>
                <td>
                    <div class="input" for="defects"><input type="text" name="defects"value="<?=@$ritem['defects']?>"><img src="/app/images/uncorrect.png" alt=""><img src="/app/images/correct.png" alt=""></div>
                </td>
            </tr>
            <tr>
                <td>Комментарий к шине</td>
                <td>
                    <div class="input textarea" for="comment"><textarea name="comment"><?=@$ritem['comment']?></textarea><img src="/app/images/uncorrect.png" alt=""><img src="/app/images/correct.png" alt=""></div>
                </td>
            </tr>
            <tr>
                <td>Ваше имя <sup>*</sup></td>
                <td>
                    <div class="input" for="userName"><input type="text" name="userName" value="<?=@$ritem['userName']?>"><img src="/app/images/uncorrect.png" alt=""><img src="/app/images/correct.png" alt=""></div>
                </td>
            </tr>
            <tr>
                <td>Марка и модель вашего авто:</td>
                <td>
                    <div class="input" for="avtoName"><input type="text" name="avtoName" value="<?=@$ritem['avtoName']?>"><img src="/app/images/uncorrect.png" alt=""><img src="/app/images/correct.png" alt=""></div>
                </td>
            </tr>
            <tr class="last">
                <td><input type="button" class="oform" style="float: left" onclick="$(this).parents('form').submit(); return false;" value="Отправить"></td>
                <td class="note"></td>
                <td></td>
            </tr>
        </table>
    </form>
</div>


