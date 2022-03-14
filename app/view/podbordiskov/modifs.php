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
                    <img src="<?= $avto_image ?>" class="podbor_avto_image" alt="<?= $mark . ' ' . $model . ' ' . $year . ' г/в' ?>"/>
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
                        <!--p class="title_text ctext">
                            Для того что бы более точно подобрать колесные диски для <?= $mark . ' ' . $model . ' ' . $year . ' г/в' ?>
                            укажите модификацию двигателя.
                        </p-->
                    <? } ?>
                </div>
            </td>
        </tr>
    </table>
    <div class="clearfix" style="margin-bottom: 10px;"></div>
    <? $this->incView('podbordiskov/quick'); ?>
    <? $this->incView('podbordiskov/search'); ?>
    <!--<div class="box-block-filter">
    <h4>Выберите год выпуска <?= $mark_alt . ' ' . $model_alt . ' (' . $year . ' года выпуска)' ?></h4>
    <ul class="list-08"><?
    foreach ($modifs as $v) {
        ?><li><a href="<?= $v['url'] ?>"><?= $v['anc'] ?></a></li><?
    }
    ?></ul>
    </div>
    </div>

    <div class="box-padding" style="margin-top: 30px">
    <ul class="collum"><?
    if (!empty($introText[1])) {
        ?><li class="ctext"><?
        ?><p><?= $introText[1] ?></p><?
        ?></li><?
    }
    if (!empty($introText[2])) {
        ?><li><?
        ?><div class="speak"><?
        ?><i></i><?
        ?><div class="ctext"><? echo $introText[2]; ?></div><?
        ?></div><?
        ?></li><?
    }
    ?></ul>
    </div>         -->
    <?
    if (!empty($h2)) {
        echo '<h2>' . $h2 . '</h2>';
    }
    if (!empty($dwText)) {
        echo '<div class="ctext justify outer_content">'.$dwText.'</div>';
    }
    else {
        $text_file_name = $_SERVER['DOCUMENT_ROOT'] . '/app/view/podbordiskov/d_texts/modifs.txt';
        if (file_exists($text_file_name)) {
            $d_text = $ab->parseText(file_get_contents($text_file_name), '/\(\s*([^\)]+)\s*\)/iU', '/');
            echo '<div class="ctext justify outer_content">' . str_replace(Array('%model%', '%radiuses%'), Array($mark . ' ' . $model . ' ' . $year . ' г/в', "R" . implode(' R', array_keys($sz2))), $d_text) . '</div>';
        }
    }
    ?>
</div>
