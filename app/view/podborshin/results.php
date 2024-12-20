<script type="text/javascript">
    $(document).ready(function () {
        if ($('.hidden_wrapper input[type="checkbox"]:checked').length == 0) {
            $('.hidden_wrapper_title').click(function () {
                $('.hidden_wrapper').toggle('fast');
                $(this).hide();
            });
        }
        else {
            $('.hidden_wrapper').show();
            $('.hidden_wrapper_title').hide();
        }
        $('.ext_params_switcher').click(function () {
            $('.search_result_box_wrap').toggle('fast', function () {
                if ($(this).is(':visible')) {
                    $('.ext_params_switcher > span')[0].innerHTML = '&#8963;';
                }
                else $('.ext_params_switcher > span')[0].innerHTML = '&#8964;';
            });
        });
    });
</script>
<div class="box-padding">
    <h1 class="title" style="padding-top: 20px;"><?= $_title ?></h1>
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
                            На данной странице представлены допустимые размеры шин для Вашего
                            автомобиля <?= $mark . ' ' . $model . ' ' . $year . ' г/в ' . $modif ?>.
                        </p>
                    <? } ?>
                </div>
            </td>
        </tr>
    </table>
    <div class="clearfix" style="margin-bottom: 10px;"></div>
    <? $this->incView('podborshin/quick'); ?>
    <!--<div class="box-padding">
        <div class="ext_params_switcher">
            <a>Выберите интересующий вас типоразмер шин
                для <?/*= @$mark . ' ' . @$model . ' ' . @$modif . ' ' . (@$year ? @$year . ' г/в' : '') */?></a><span>&#8964;</span>
        </div>
        <div class="search_result_box_wrap">
            <div class="search_result_box">
                <table>
                    <tbody>
                    <tr>
                        <th></th>
                        <th>
                            <b>Размеры шин для <?/*= $mark . ' ' . $model . ' ' . $modif . ' ' . $year */?> г/в.</b>
                        </th>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <ul class="title">
                                <li style="float: left;">Передняя ось/обе оси</li>
                                <li style="float: right;">Задняя ось</li>
                            </ul>
                        </td>
                    </tr>
                    <?/*
                    foreach ($sz1 as $rad => $v) {
                        */?>
                        <tr>
                        <td>
                            <div class="radius"><span>R<?/*= $rad */?></span></div>
                        </td>
                        <?/*
                        $type0 = '';
                        */?>
                        <td>
                        <ul><?/*
                            foreach ($v as $type => $vv) {
                                foreach ($vv as $row) {
                                    */?>
                                    <li><?/*
                                    if (!empty($row[2])) {
                                        */?>
                                        <div class="r-box"><img src="/app/images/car-l.png" height="45" alt=""><img
                                                height="45" src="/app/images/car-r.png" alt=""><img
                                                src="/app/images/chain.png" alt="" class="chant"></div><?/*
                                        */?><a
                                        href="<?/*= $tSearchUrl . '?ap=1&p3=' . $row[1]['P3'] . '&p2=' . $row[1]['P2'] . '&p1=' . $row[1]['P1'] . '&p3_=' . $row[2]['P3'] . '&p2_=' . $row[2]['P2'] . '&p1_=' . $row[2]['P1'] */?>"><?/*
                                        */?>
                                        <span><?/*= $row[1]['P3'] . '/' . $row[1]['P2'] . ' R' . $row[1]['P1'] */?></span><?/*
                                        */?>
                                        <span><?/*= $row[2]['P3'] . '/' . $row[2]['P2'] . ' R' . $row[2]['P1'] */?></span><?/*
                                        */?></a><?/*
                                    } else {
                                        */?>
                                        <div class="r-box"><img src="/app/images/chain.png" alt="" class="chant">
                                        </div><?/*
                                        */?><a
                                        href="<?/*= $tSearchUrl . '?ap=1&p3=' . $row[1]['P3'] . '&p2=' . $row[1]['P2'] . '&p1=' . $row[1]['P1'] */?>"><?/*
                                        */?>
                                        <span><?/*= $row[1]['P3'] . '/' . $row[1]['P2'] . ' R' . $row[1]['P1'] */?></span><?/*
                                        */?></a><?/*
                                    }
                                    */?></li><?/*
                                }

                            }
                            */?></ul></td><?/*
                        */?></tr><?/*
                    } */?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>-->
    <!--Место для каталога-->
    <div class="box-shadow">

        <? $this->incView($searchTpl); ?>

    </div>
    <!--/Место для каталога-->
    <!--<center><img src="/app/images/shina.jpg" border="0" width="407" height="381" alt="shina.jpg (97 134 bytes)"></center>-->
    <!--<a href="<?= $prevUrl ?>" class="back">Вернуться к выбору объема двигателя <?= $mark . ' ' . $model . ' ' . $modif . ' ' . $year ?> года</a>-->
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