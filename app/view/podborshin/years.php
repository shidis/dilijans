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
<div class="box-shadow">
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
                        <img src="<?= $avto_image ?>" class="podbor_avto_image" alt="<?= $mark . ' ' . $model?>"/>
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
                                Для того чтобы подобрать более точные размеры шин для <?= $mark . ' ' . $model ?>, укажите год выпуска вашего авто.
                            </p>
                        <? } ?>
                    </div>
                </td>
            </tr>
        </table>
        <div class="clearfix" style="margin-bottom: 10px;"></div>
        <? $this->incView('podborshin/quick'); ?>
        <!--Место для каталога-->
        <div class="box-shadow">

            <? $this->incView($searchTpl); ?>

        </div>
        <!--/Место для каталога-->
        <div class="box-block-filter">
            <h4>Выберите год выпуска <?= $mark_alt . ' ' . $model ?></h4>
            <ul class="list-08"><?
                foreach ($years as $v) {
                    ?>
                    <li><a href="<?= $v['url'] ?>"><?= $v['anc'] ?></a></li><?
                }
                ?></ul>
        </div>
    </div>
    <?
    if (!empty($h2))
    {
        echo '<h2>'.$h2.'</h2>';
    }
    ?>
    <div class="ctext justify outer_content">
        <?
        if (!empty($dwText))
        {
            echo $dwText;
        }
        else
        {
        ?>
        <p>
            Вероятно, Вас интересует покупка шин для <?= $mark . ' ' . $model ?>. В этом магазине вам с удовольствием
            помогут подобрать шины для Вашего авто
            всех типоразмеров – <?= "R" . implode(', R', array_keys($sz1)) ?>.
        </p>

        <p>
            Далее предлагаем приступить сразу к сути. Теперь мы расскажем, какими функциями онлайн располагает наш
            сервис подбора <?= $mark_alt ?>.
        </p>

        <h2>
            Подбор шин для <?= $mark . ' ' . $model ?>
        </h2>

        <p>
            Многие обладатели <?= $mark . ' ' . $model ?> представляют, как сложен подбор шин для <?= $mark_alt ?>.
            Отсутствие консультативной поддержки
            специалистов - это большое количество ушедшего впустую времени.
        </p>

        <p>
            Вам некогда осматривать и примерять на авто десятки шин, мучить вопросами продавцов-консультантов?
        </p>

        <p>
            Смотрите, что мы предложим! Наш высокоточный сервис дает возможность выполнить подбор автомобильных шин для
            авто <?= $mark . ' ' . $model ?>,
            используя специальный сервис. Посмотрите, насколько это просто:
        </p>
        <ul>
            <li>
                сначала надо указать модель и серию вашей машины;
            </li>
            <li>
                после этого кликните на слово "искать";
            </li>
            <li>
                появится список шин, которые Вам подойдут.
            </li>
        </ul>
        <p>
            Сделайте это сами, и вы убедитесь, как это элементарно!
        </p>
        <?}?>
    </div>
</div>