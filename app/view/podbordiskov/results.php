<div class="box-padding">
    <h1 class="title"><?= $_title ?></h1>
    <table class="podbor_desc" border="0">
        <tr>
            <td align="left" valign="middle">
                <? if (!empty($ab->brand_img)): ?>
                    <img src="/cimg/<?= $ab->brand_img ?>" class="podbor_logo" alt="<?= $mark ?>"/>
                <? endif; ?>
            </td>
            <td align="center" valign="top">
                <? if (!empty($avto_image)): ?>
                    <img src="<?= $avto_image ?>" class="podbor_avto_image" alt="<?= $mark . ' ' . $model . ' ' . $year . ' г/в ' . $modif . '.' ?>"/>
                <? endif; ?>
            </td>
            <td align="right" width="315px" valign="middle">
                <? if (@$show_rating): ?>
                    <div class="podbor_rating_avto_wrap">
                        <div class="question">?</div>
                        <div class="podbor_rating_avto">Рейтинг авто: <img src="/app/images/z-star.png" alt="Рейтинг авто"/></div>
                    </div>
                <? endif; ?>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <div class="podbor_text">
                    <?
                    if (!empty($upText)) {
                        echo $upText;
                    } else {
                        ?>
                        <p class="title_text ctext">
                            На данной странице представлены допустимые размеры дисков для Вашего
                            автомобиля <?= $mark . ' ' . $model . ' ' . $year . ' г/в ' . $modif . '.' ?> Если вы опуститесь ниже, то
                            увидите полный каталог дисков, которые есть в наличии на Ваш авто.
                        </p>
                    <? } ?>
                </div>
            </td>
        </tr>
    </table>
    <div class="clearfix" style="margin-bottom: 10px;"></div>
    <? $this->incView('podbordiskov/quick'); ?>
    <? $this->incView('podbordiskov/search'); ?>
    <!--  <ul class="collum">
    <li>
    <div class="box-blue-car">
    <div class="title-car"><p><span>Диски</span>для <?= $mark . ' ' . $model ?></p><img src="/app/images/img-nav-filter-03.png" alt=""></div>
    <div id="select-brends-car">
    <div class="all-des">
    <b>Общие параметры</b>
    <table>
    <tr>
    <td>PCD: <?= @$abc['pcd'] ?> </td>
    <td>DIA: <?= @$abc['dia'] ?> мм</td><?
    if (!empty($abc['gaika'])) {
        ?><td>Гайка: <?= $abc['gaika'] ?></td><?
    }
    if (!empty($abc['bolt'])) {
        ?><td>Болт: <?= $abc['bolt'] ?></td><?
    }
    ?></tr>
    </table>
    </div><?
    foreach ($sz2 as $rad => $v) {
        ?><div class="title-mini">R<?= $rad ?></div><?
        ?><ul><?
        ?><li><?
        ?><p><i>Передняя ось/обе оси</i><i>Задняя ось</i></p><?
        ?></li><?
        $type0 = '';
        foreach ($v as $type => $vv) {
            ?><li><?
            if ($type0 != $type) {
                ?><div class="h-name"><?= $type ?></div><?
                $type0 = $type;
                $i = 0;
            }
            foreach ($vv as $row) {
                if ($i) {
                    ?><li><?
                }
                $i++;
                if (!empty($row[2])) {
                    ?><div class="r-box"><img src="/app/images/car-l.png" alt=""><img src="/app/images/car-r.png" alt=""><img src="/app/images/chain.png" alt="" class="chant"></div><?
                    ?><a href="<?= $dSearchUrl . '?ap=1&p2=' . $row[1]['P2'] . '&p5=' . $row[1]['P5'] . '&p1=' . $row[1]['P1'] . '&p4=' . $row[1]['P4'] . '&p6=' . $row[1]['P6'] . '&p3=' . $row[1]['P3'] . '&p2_=' . $row[2]['P2'] . '&p5_=' . $row[2]['P5'] . '&p1_=' . $row[2]['P1'] . '&p4_=' . $row[2]['P4'] . '&p6_=' . $row[2]['P6'] . '&p3_=' . $row[2]['P3'] ?>"><?
                    ?><span><?= $row[1]['P2'] . ' x ' . $row[1]['P5'] . ' ET' . $row[1]['P1'] ?></span><?
                    ?><span><?= $row[2]['P2'] . ' x ' . $row[2]['P5'] . ' ET' . $row[2]['P1'] ?></span><?
                    ?></a><?
                } else {
                    ?><div class="r-box"><img src="/app/images/chain.png" alt="" class="chant"></div><?
                    ?><a href="<?= $dSearchUrl . '?ap=1&p2=' . $row[1]['P2'] . '&p5=' . $row[1]['P5'] . '&p1=' . $row[1]['P1'] . '&p4=' . $row[1]['P4'] . '&p6=' . $row[1]['P6'] . '&p3=' . $row[1]['P3'] ?>"><?
                    ?><span><?= $row[1]['P2'] . ' x ' . $row[1]['P5'] . ' ET' . $row[1]['P1'] ?></span><?
                    ?></a><?
                }
                ?></li><?
            }
        }
        ?></ul><?
    } ?>
    </div>

    </div>
    </li>
    </ul>
    <a href="<?= $prevUrl ?>" class="back">Вернуться к выбору объема двигателя <?= $mark . ' ' . $model . ' ' . $modif . ' ' . $year ?> года</a>-->
</div>
<?
if (!empty($h2))
{
    echo '<h2>'.$h2.'</h2>';
}
if (!empty($dwText))
{
    echo '<div class="ctext justify outer_content">'.$dwText.'</div>';
}
?>