<div class="box-shadow">
    <div class="articles">
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
                    ?><a href="<?=$v['url']?>" class="more">Читать всю статью</a><?
                ?></div><?
             ?></li><?
        }
        ?></ul>
    </div><?
    if(!empty($paginator)){
        ?><div class="paginator"><ul><?
            echo $paginator;
        ?></ul></div><?
    }?>

</div>