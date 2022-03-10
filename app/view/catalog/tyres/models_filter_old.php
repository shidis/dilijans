<?
if(!empty($filter)){
?><div class="box-grey-01"><?

    ?><div class="nav-catalog-types"><?
        ?><table><?
            ?><tr><?
                if(!empty($filter['sezon'])){
                    ?><td>Подобрать по сезону:</td><?
                }
                if(!empty($filter['at'])){
                    ?><td>Подобрать по автомобилю:</td><?
                }
                ?></tr><?
            ?><tr><?
                if(!empty($filter['sezon'])){
                    ?><td width="47%"><?
                    ?><ul class="season-f"><?
                    foreach($filter['sezon'] as $k=>$v){
                        ?><li<?=$v['active']?' class="active"':''?>><a href="<?=$v['url']?>"></a><span><?
                            if(!empty($v['ico'])){
                                ?><img src="<?=$v['ico']?>" alt=""><?
                            }
                            ?><b><i><?=$v['anc']?></i><u></u></b></span></li><?
                    }
                    ?></ul><?
                    ?></td><?
                }
                if(!empty($filter['at'])){
                    ?><td><?
                    ?><ul class="season-gf"><?
                    foreach($filter['at'] as $k=>$v){
                        ?><li<?=$v['active']?' class="active"':''?>><a href="<?=$v['url']?>"></a><span><?
                            if(!empty($v['ico'])){
                                ?><img src="<?=$v['ico']?>" alt=""><?
                            }
                            if(!empty($v['ico_active'])){
                                ?><img src="<?=$v['ico_active']?>" alt=""><?
                            }
                            ?><b><i><?=$v['anc']?></i><u></u></b></span></li><?
                    }
                    ?></ul><?
                    ?></td><?
                }
                ?></tr><?
            if(!empty($filter['rads'])){
                ?><tr><?
                ?><td colspan="2">Подобрать по диаметру:</td><?
                ?></tr><?
                ?><tr><?
                ?><td colspan="2"><?
                ?><ul class="list-param"><?
                foreach($filter['rads'] as $k=>$v){
                    ?><li><a<?=$v['active']?' class="active"':''?> href="<?=$v['url']?>"><b><i><?=$v['anc']?></i><u></u></b></a></li><?
                }
                ?></ul><?
                ?></td><?
                ?></tr><?
            }
            ?></table><?
        ?></div><?
    ?></div><?
}
