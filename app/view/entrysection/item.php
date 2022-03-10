<div class="be-page">
    <div class="box-shadow ">
        <div class="articles">
            <h1 class="title"><?=$_title?></h1>
                <?if(!empty($content)):?>
                    <div class="be-page__desc">
                        <div class="ctext"><?echo $content;?></div>
                    </div>
                <?endif;?>

            <?
                $list = $entryList;
            ?>

        <? if(!empty($list)){?>

            <?$firstElm = array_shift($list);?>

            <?if(!empty($firstElm)):?>
                <div class="be-block"><div class="be-block__img"><?
                    if(!empty($firstElm['img1'])){
                        ?><a href="<?=$firstElm['url']?>"><img src="<?=$firstElm['img1']?>"  alt="<?=$firstElm['title']?>"></a><?
                    }
                    ?></div>
                    <div><a href="<?=$firstElm['url']?>" class="be-block__h1"><?=$firstElm['title']?></a></div>
                    <p  class="be-block__desc"><?=$firstElm['intro']?></p>
                    <a href="<?=$firstElm['url']?>" class="more">Читать полностью</a>
                </div>
            <?endif;?>

            <div class="be-cl">

                <div class="be-cl__list">
                <?foreach($list as $v){?>
                    <?if(!empty($v['title']) && $v['published']):?>
                        <div class="be-cl__item"><div class="be-cl__img">
                            <?
                            if(!empty($v['img1'])){
                                ?><a href="<?=$v['url']?>"><img src="<?=$v['img1']?>" alt="<?=$v['title']?>"></a><?
                            }
                            ?></div>
                            <div><a href="<?=$v['url']?>" class="be-cl__h1"><?=$v['title']?></a></div>
                            <p class="be-cl__desc"><?=$v['intro']?></p>
                            <a href="<?=$v['url']?>" class="more">Читать полностью</a>
                        </div>
                    <?endif;?>
                <? }?>
                </div>
            </div>
        <? }?>
        </div>
    </div>
</div>

