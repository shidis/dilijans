<div class="box-shadow">
    <div class="box-padding">
        <? if (!empty($ab->brand_img)): ?>
            <img src="/cimg/<?= $ab->brand_img ?>" class="podbor_logo" alt="<?= $mark ?>"/>
        <? endif; ?>
        <h1 class="title"><?= $_title ?></h1>

        <div class="title_text ctext">
            <?
            if (!empty($upText)) {
                echo $upText;
            } else {
                ?>
                <p>
                    Для того чтобы подобрать диски для вашего <?= $mark ?> воспользуйтесь формой ниже и выберите
                    интересующую модель <?= $mark_alt ?>. Далее выберите год выпуска вашего авто и модификацию
                    двигателя.
                </p>
            <? } ?>
        </div>
        <? $this->incView('podbordiskov/quick'); ?>

        <div class="box-block-filter">
            <h4>Выберите интересующую модель <?= $mark_alt ?></h4>
            <ul class="list-08"><?
                foreach ($models as $v) {
                    ?>
                    <li><a href="<?= $v['url'] ?>" title="<?= $v['title'] ?>"><?= $v['anc'] ?></a></li><?
                }
                ?></ul>
        </div>

        <? if (!empty($replicaCross)) { ?>

            <p style="margin-top: 15px"><a href="<?= $replicaCross['url'] ?>"
                                           title="<?= $replicaCross['title'] ?>"><?= $replicaCross['anc'] ?></a></p>

        <? } ?>

    </div>
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