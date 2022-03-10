<!--<div class="popup" id="gallery-popup">
    <a href="#" class="close2" title="Закрыть"></a>
    <h1><?=$h1?></h1><?
    if(!empty($gallery)){
    ?>
    <div>
        <div class="block"><?
            $i=0;
            foreach($gallery as $v){
                $i++;
                ?><img src="<?=$v['img3']?>" alt=""<?=$i==1?' class="active"':''?> width="545"><?
            }
        ?></div>
        <div class="nav-gallery">
            <ul><?
                $i=0;
                foreach($gallery as $v){
                    $i++;
                    ?><li<?=$i==1?' class="active"':''?>><a href="#"></a><img src="<?=$v['img1']?>" alt=""></li><?
                }
                ?>
            </ul>
        </div>
    </div><?
    }else{
        ?><p>Нет фото</p><?
    }
    ?>
</div> -->
