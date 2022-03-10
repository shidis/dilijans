<div class="box-shadow ctext">
    <h1><?=$_title?></h1><?

    if($img2!=''){
        ?><img class="float-left" src="<?=$img2?>" alt="<?=$_title?>" /><?
    }
    echo $content;

?></div><?

?><div class="box-shadow"><?

    if(!empty($lenta)){
        ?><div class="offers"><?
            ?><h2 class="other">Другие публикации</h2><?
        ?><ul><?
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
            ?></ul><?
        ?></div><?
    }?>

</div>
