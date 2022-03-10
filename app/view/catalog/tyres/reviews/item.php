<div class="rvws-item" rid="<?=$id?>">

    <div class="f">
        <div class="aname"><?=$userName?></div>

        <? if(!empty($avtoName)){?>

            <div class="avto"><b>автомобиль: </b><span class="avtoName"><?=$avtoName?></span></div>

        <? }?>

    </div>

    <div class="h">

        <ul class="stars" v="<?=ceil($rating)?>"></ul>
        <div class="rating"><?=sprintf("%01.1f", $rating)?></div>

        <? if(!empty($vals)) {?>

                <? foreach($vals as $k=>$v){?>

                    <div class="val">
                        <span class="k"><?=$revRatingItems[$k]?>:</span>
                        <span class="v"><?=$v?>.0</span>
                    </div>

                <? }?>


        <? }?>

    </div>

    <div class="b">
        <div class="row">
            <div class="n">Достоинства:</div>
            <div class="v"><?=nl2br($advants)?></div>
        </div>
        <div class="row">
            <div class="n">Недостатки:</div>
            <div class="v"><?=nl2br($defects)?></div>
        </div>
        <div class="row">
            <div class="n">Комментарий:</div>
            <div class="v"><?=nl2br($comment)?></div>
        </div>

        <? if(!empty(CU::$userId)){?>

            <div class="row">
                <div style="border: 1px dashed #CCC; border-radius: 6px; padding: 5px 10px; box-shadow: #0081c2; overflow: hidden">

                    <div style="float: right; font-weight: bold;">
                        <? if($state!=1) echo '<span style="color:red">не опубликован</span>';?>
                    </div>

                    <p>reviewId=<b><?=$id?></b>, postedByAdmin=<b><?=$postedByAdmin?$postedBy_shortName: 'нет'?></b>, state=<b><?=$state?></b>, cUserId=<b><?=$cUserId?$cUser_shortName:0?></b></p>

                </div>
            </div>

        <? }?>

        <? if(!empty($editable)){?>

            <div class="row">
                <a href="#" class="rev-edit">редактировать отзыв</a>
                <a href="#" class="rev-del" style="margin-left: 30px">удалить отзыв</a>
            </div>

        <? }?>

    </div>

</div>