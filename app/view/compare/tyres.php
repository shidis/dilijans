<div class="box-padding">
<h1 class="title"><?=$_title?></h1>

<? if(!empty($cat)){?>

    <div id="cmp-wrapper">

        <div class="left-collum">
            <table>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>Производитель:</td>
                </tr>
                <tr>
                    <td>Модель:</td>
                </tr>
                <tr>
                    <td>Размер:</td>
                </tr>
                <tr>
                    <td>Сезонность / шипы:</td>
                </tr>
                <tr>
                    <td>Индекс нагрузки / индекс скорости:</td>
                </tr>
                <tr>
                    <td>Цена:</td>
                </tr>
            </table>
        </div>
        <div class="box-ov">
            <div class="box-compare">
                <ul>

                <? foreach($cat as $v){?>

                <li cid="<?=$v['cat_id']?>">
                    <table>
                        <tr>
                            <td>
                                <div class="img"><a href="#" class="del" cid="<?=$v['cat_id']?>" title="Удалить товар из таблицы сравнения"></a><a rel="zoom" href="<?=$v['img2']?>" title="<?=$v['imgAlt']?>"><img src="<?=$v['img1Blk']?>" alt="<?=$v['imgAlt']?>"></a></div>
                            </td>
                        </tr>
                        <tr>
                            <td style="border-top:1px solid #fff;"><?=$v['bname']?></td>
                        </tr>
                        <tr>
                            <td><a href="<?=$v['url']?>" class="h1"><?=$v['ancBlk']?></a></td>
                        </tr>
                        <tr>
                            <td><?=$v['razmer']?></td>
                        </tr>
                        <tr>
                            <td><?=$v['sezIco']?></td>
                        </tr>
                        <tr>
                            <td><?=$v['inisUrl']?></td>
                        </tr>
                        <tr>
                            <td><b class="scl" cat_id="<?=$v['cat_id']?>"><?=$v['priceText']?></b></td>
                        </tr>
                        <tr>
                            <td><a href="#" class="buy-new tocart" pid="tqp<?=$v['cat_id']?>" id="tqp<?=$v['cat_id']?>" title="Добавить товар в корзину" maxQty="<?=$v['maxQty']?>" defQty="<?=$v['defQty']?>" cid="<?=$v['cat_id']?>">купить</a></td>
                        </tr>
                    </table>
                </li>

                <? }?>

                </ul>
            </div>
        </div>
    </div>
</div>
<? }else{
    ?><div class="box-no-nal" style="margin: 50px 0 300px">Список сравнения пуст. Если вы попали на эту страницу не случайно и список сравнения не должен быть пуст, проверьте что куки в браузере включены</div><?
}