<div class="box-shadow">
    <div class="offers">
        <h1 class="pad"><?=$_title?></h1>
        <ul><?
            foreach($lenta as $v){
                ?><li><?
                if(!empty($v['img1'])){
                    ?><div class="img"><a href="<?=$v['url']?>"><img src="<?=$v['img1']?>" width="110" alt="<?=$v['title']?>"></a></div><?
                }
                ?><div><?
                ?><a href="<?=$v['url']?>" class="h1"><?=$v['title']?></a><?
                ?><p><?=$v['intro']?></p><?
                ?><a href="<?=$v['url']?>" class="more">Подробнее</a><?
                ?></div><?
                ?></li><?
            }
            ?></ul>
    </div>



</div>