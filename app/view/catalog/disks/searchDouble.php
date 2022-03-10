<?
if(!empty($cat)){
    ?>

    <div class="box-padding">
        <div class="box-rez">
            <b>Найдено вариантов: <?=$exnum?>.</b>
        </div>
    </div>

    <div class="search-rezult">

    <table>
        <thead>
        <tr>

            <td><b>Наименование</td>
            <td width="40px"></td>
            <td width="70px"><b>Размер</b></td>
            <td width="55px"><b>PCD</b></td>
            <td width="50px"><b>Вылет</b></td>
            <td width="40px"><b>DIA</b></td>
            <td width="60px"><b>Склад</b></td>
            <td width="60px"><b>Цена</b></td>
            <td width="100px"><b>Количество</b></td>
            <td width="54px" style="text-align:center; padding-right:20px;"><img src="/app/images/icon-basket.png" alt=""></td>

        </tr>
        </thead>
        <tbody class="pdd"><?
        foreach($cat as $v){
            ?>
            <tr>
            <td style="vertical-align:middle;">
                <div class="name-disc"><a href="<?=$v[0]['url']?>"><?=$v[0]['anc']?></a><br><?=$v[0]['colorUrl']?></div>
                <div class="img-rez"><?
                    if(!empty($v[0]['img3'])){
                        ?><a rel="zoom" href="<?=$v[0]['img2']?>"><img src="<?=$v[0]['img1']?>" alt="<?=$v[0]['imgAlt']?>"></a><?
                    }
                    ?></div>

            </td>
            <td width="50px"><div class="duble-types"><img src="/app/images/duble-types.png" alt=""></div></td>

            <td colspan="7">
                <table class="dulbe-in">
                    <tr>
                        <td width="60px"><?=$v[0]['razmer']?></td>
                        <td width="55px"><?=$v[0]['sverlovka']?></td>
                        <td width="50px"><?=$v[0]['et']?></td>
                        <td width="45px"><?=$v[0]['dia']?></td>
                        <td width="60px"><?=$v[0]['qtyText']?></td>
                        <td width="60px"><b class="scl" cat_id="<?=$v[0]['cat_id']?>"><?=$v[0]['priceText']?></b></td>
                        <td width="100px">
                            <div class="input-basket2">
                                <a href="#"></a>
                                <a href="#"></a>
                                <input type="text" value="<?=$v[0]['defQty']?>" amid1="tqp_<?=$v[0]['cat_id']?>_<?=$v[1]['cat_id']?>">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td width="60px"><?=$v[1]['razmer']?></td>
                        <td width="45px"><?=$v[1]['sverlovka']?></td>
                        <td width="50px"><?=$v[1]['et']?></td>
                        <td width="40px"><?=$v[1]['dia']?></td>
                        <td width="60px"><?=$v[1]['qtyText']?></td>
                        <td width="60px"><b class="scl" cat_id="<?=$v[1]['cat_id']?>"><?=$v[1]['priceText']?></b></td>
                        <td><div class="input-basket2">
                                <a href="#"></a>
                                <a href="#"></a>
                                <input type="text" value="<?=$v[1]['defQty']?>" amid2="tqp_<?=$v[0]['cat_id']?>_<?=$v[1]['cat_id']?>">
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="vertical-align:middle;"><a href="#" class="buy-new tocart-d" cid1="<?=$v[0]['cat_id']?>" cid2="<?=$v[1]['cat_id']?>" id="tqp_<?=$v[0]['cat_id']?>_<?=$v[1]['cat_id']?>" title="Добавить товар в корзину">купить</a></td>
            </tr><?
        }?>
        </tbody>
    </table>
    </div><?


}else{
    ?><div class="box-no-nal"><?=@$qtext?></div><?
}