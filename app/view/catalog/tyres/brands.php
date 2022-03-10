<div class="box-padding">
    <h1 class="title"><?=$_title?></h1><?

    if(!empty($topText)){
        ?><div class="ctext"><?
            echo $topText;
        ?></div><?
    }
    if(App_Route::_getAction()=='tSUV'){
        ?><div class="box-grey"><?
        ?><h6 class="sp">Если вы уже определилсь с сезонностью внедорожной резины, перейдите в нужный раздел:</h6><?
        ?><div class="season"><a href="/<?=App_Route::_getUrl('tSummerSUV')?>.html"><img src="/app/images/sun.png" alt="">Летняя резина</a><a href="/<?=App_Route::_getUrl('tWinterSUV')?>.html"><img src="/app/images/snow.png" alt="">Зимняя резина</a><a href="/<?=App_Route::_getUrl('tAllWSUV')?>.html"><img src="/app/images/sun.png" alt=""><img src="/app/images/snow.png" alt="">Всесезонная резина</a></div><?
        ?></div><?
    }
    if(!empty($filter)) $this->incView('catalog/tyres/sizesFilter');

    if(@$doubleDimension){
        foreach($brands as $sezon=>$vv)
            if(!empty($vv)){
                ?><h2 class="bcat"><img src="<?=$h2[$sezon]['img']?>"><?
                    if(!empty($h2[$sezon]['url'])){
                        ?><a href="<?=$h2[$sezon]['url']?>"><?=$h2[$sezon]['title']?></a><?
                    }else{
                        ?><?=$h2[$sezon]['title']?><?
                    }
                ?></h2><?
                ?><ul class="list-brends-02"><?
                    foreach($vv as $v){
                        ?><li><a href="<?=$v['url']?>"></a><?
                        ?><table><?
                            ?><tr><?
                                ?><td><img src="<?=$v['img1']?>" alt="<?=$v['alt']?>"></td><?
                            ?></tr><?
                        ?></table><?
                        ?><div class="bl_title"><a href="<?=$v['url']?>" title="<?=$v['title']?>"><?=$v['name']?></a></div></li><?
                    }
                    ?>

                    <li class="ll"></li>
                    <li class="ll"></li>
                    <li class="ll"></li>
                    <li class="ll"></li>
                </ul><?
            }
    }else{
        if (!empty($brands[1])) {
            echo '<b>Популярные бренды</b><br><br>';
            ?>
            <ul class="list-brends-02"><?
            foreach ($brands[1] as $v) {
                ?>
                <li><a href="<?= $v['url'] ?>"></a><?
                ?>
                <table><?
                    ?>
                    <tr><?
                        ?>
                        <td><img src="<?= $v['img1'] ?>" alt="<?= $v['alt'] ?>"></td><?
                        ?></tr><?
                    ?></table><?
                ?>
                <div class="bl_title"><a href="<?= $v['url'] ?>" title="<?= $v['title'] ?>"><?= $v['name'] ?></a></div>
                </li><?
            }
            ?>
            </ul>
            <?
            echo '<b>Прочие бренды</b><br><br>';
        }
        ?><ul class="list-brends-02"><?
        foreach($brands[0] as $v){
            ?><li><a href="<?=$v['url']?>"></a><?
            ?><table><?
                ?><tr><?
                    ?><td><img src="<?=$v['img1']?>" alt="<?=$v['alt']?>"></td><?
                    ?></tr><?
                ?></table><?
            ?><div class="bl_title"><a href="<?=$v['url']?>" title="<?=$v['title']?>"><?=$v['name']?></a></div></li><?
        }
        ?></ul>
<!--
        <li class="ll"></li>
        <li class="ll"></li>
        <li class="ll"></li>
        <li class="ll"></li>-->
    <?}?>
    <div class="box-padding">
        <h4>Быстрый переход в каталог шин по радиусу</h4>

        <div class="box-list2">
            <ul class="list-06"><?
                foreach($rlinks as $v){
                    ?><li><a href="<?=$v['url']?>" title="<?=$v['title']?>"><?=$v['anc']?></a></li><?
                }
                ?></ul>
        </div>
    </div>
</div>

