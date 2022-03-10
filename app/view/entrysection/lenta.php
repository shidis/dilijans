<div class="be-page">
    <div class="box-shadow">
        <div class="articles">
            <h1 class="pad title"><?=$_title?></h1>

            <ul style="display: none;"><?
                foreach($lenta as $v){
                    ?><li>
                    <div><?
                    ?><a href="<?=$v['url']?>" class="h1"><?=$v['title']?></a><?
                    ?><p><?=$v['intro']?></p><?
                    ?><a href="<?=$v['url']?>" class="more">Смотреть записи</a><?
                    ?></div><?
                    ?></li><?
                }
                ?></ul>
        <?
        if(!empty($paginator)){
            ?><div class="paginator"><ul><?
                echo $paginator;
                ?></ul></div><?
        }?>

        <? if(!empty($entryList)){?>
            <?
            $firstElm = array_shift($entryList);

            ?>

            <?if(!empty($firstElm)):?>
            <div class="be-block"><?
                if(!empty($firstElm['img1'])){
                    ?><div class="be-block__img"><a href="<?=$firstElm['url']?>"><img src="<?=$firstElm['img1']?>"  alt="<?=$firstElm['title']?>"></a></div><?
                }
                ?>
                <div><a href="<?=$firstElm['url']?>" class="be-block__h1"><?=$firstElm['title']?></a></div>
                <p  class="be-block__desc"><?=$firstElm['intro']?></p>
                <a href="<?=$firstElm['url']?>" class="more">Читать полностью</a>
            </div>
            <?endif;?>

            <div class="be-cl">
                <div class="be-cl__list">
                    <?foreach($entryList as $v){?>
                        <?if(!empty($v['title']) && $v['published']):?>
                            <div class="be-cl__item"><div class="be-cl__img"><?
                                if(!empty($v['img1'])){
                                    ?><a href="<?=$v['url']?>"><img src="<?=$v['img1']?>" alt="<?=$v['title']?>"></a><?
                                }
                                ?></div>
                                <div><a href="<?=$v['url']?>" class="be-cl__h1"><?=$v['title']?></a></div>
                                <p class="be-cl__desc"><?=$v['intro']?></p>
                                <a href="<?=$v['url']?>" class="more">Читать полностью</a>
                            </div>
                        <?endif;?>
                    <?}?>
                </div>
            </div>

        <? }?>
        </div>
    </div>
</div>