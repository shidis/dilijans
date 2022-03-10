<style type="text/css">
    #content .goods-01 .img{
        width: 120px;
        height: 200px;
    }
</style>
<div class="box-shadow">
<div class="box-padding">
    <h1 class="title cat"><img src="/app/images/img-warranty-01.jpg" alt=""><?=$_title?></h1><?

    if(!empty($topText)){
        ?><div class="ctext"><?
            echo $topText;
        ?></div><?
    }

    if($lf){
        ?>

        <div class="box-grey-01">
            <form action="/<?=App_Route::_getUrl('tSearch')?>.html" class="form-style-01 livef" sMode="<?=@$sMode?>">

                <? Url::arr2hiddenFields($lfh);?>

                <table><?
                    $i=0;
                    if(!empty($lf['_p1'])){
                        $i++
                        ?><tr<?=$i==$lfi?' class="last"':''?>>
                        <td class="black" width="120px">Диаметр:</td>
                        <td class="black" group="_p1"><?
                        foreach($lf['_p1'] as $k=>$v){
                            ?><label<?=$v['chk']?' class="active"':''?>><input type="checkbox"<?=$v['chk']?' checked':''?> name="_p1[<?=$k?>]" id="<?=$v['id']?>"   value="1"><i></i><?=$v['anc']?></label><?
                        }
                        ?></td><?
                        ?></tr><?
                    }
                    if(!empty($lf['_mp1'])){
                        $i++
                        ?><tr<?=$i==$lfi?' class="last"':''?>>
                        <td class="black" width="120px">Сезонность:</td>
                        <td class="black" group="_mp1"<?=!empty($lf['_mp3'])?' style="padding-bottom:0"':''?>><?
                        foreach($lf['_mp1'] as $k=>$v){
                            ?><label class="sezon<?=$v['chk']?' active':''?>"><input type="checkbox"<?=$v['chk']?' checked':''?> name="_mp1[<?=$k?>]" id="<?=$v['id']?>" value="1"><i></i><?=$v['anc']?></label><?
                        }
                        ?></td><?
                        ?></tr><?
                    }
                    if(!empty($lf['_mp3'])){
                        $i++
                        ?><tr<?=$i==$lfi?' class="last"':''?>>
                        <td class="black" width="120px"></td>
                        <td class="black" group="_mp3"><?
                        foreach($lf['_mp3'] as $k=>$v){
                            ?><label class="ship<?=$v['chk']?' active':''?>"><input type="checkbox"<?=$v['chk']?' checked':''?> name="_mp3[<?=$k?>]" id="<?=$v['id']?>" value="1"><i></i><?=$v['anc']?></label><?
                        }
                        ?></td><?
                        ?></tr><?
                    }
                    if(!empty($lf['_mp2'])){
                        $i++
                        ?><tr<?=$i==$lfi?' class="last"':''?>>
                        <td class="black" width="120px">Тип автомобиля:</td>
                        <td class="black" group="_at"><?
                        foreach($lf['_mp2'] as $k=>$v){
                            ?><label class="atype<?=$v['chk']?' active':''?>"><input type="checkbox"<?=$v['chk']?' checked':''?> name="_at[<?=$k?>]" id="<?=$v['id']?>" value="1"><i></i><?=$v['anc']?></label><?
                        }
                        ?></td><?
                        ?></tr><?
                    }
                    ?>
                    <tr class="last">
                        <td colspan="2"><input type="button" class="lfGo" value="найти"><div class="loader"></div> <div class="result-label"></div></td>
                    </tr>
                </table>
            </form>
        </div>

    <? }

    if(!empty($models)){

        ?><div class="box-rez"><?
            if(!empty($paginator)){
                ?><div class="paginator"><ul><?
                    echo $paginator;
                    ?></ul></div><?
            }
            ?><b>Найдено моделей <?=$num?></b><?
        ?></div><?

        ?><div class="goods-01"><?
        ?><ul><?
            foreach($models as $v){
            ?><li><?
                ?><div class="img"><?
                if($v['spezId']){
                    ?><i></i><?
                }
                if($v['sez']==1){
                    ?><u class="sun"></u><?
                }
                if($v['sez']==2){
                    ?><u class="snow"></u><?
                }
                if($v['sez']==3){
                    ?><u class="sun-snow"></u><?
                }
                if($v['ship']){
                    ?><em></em><?
                }
                ?><a href="<?=$v['url']?>"><img src="<?=$v['img']?>" height="200" alt="<?=$v['alt']?>"></a></div><?
                ?><a href="<?=$v['url']?>" class="h1"><?=$v['anc']?></a><?
                if($v['scDiv']){
                    ?><span class="nal">есть на складе</span><?
                }else{
                    ?><span class="nnal">нет в наличии</span><?
                }
            ?></li><?
            }
        ?></ul><?
        ?></div><?

        if(!empty($paginator)){
            ?><div class="paginator"><ul><?
                echo $paginator;
                ?></ul></div><?
        }

    } else{
        ?><div class="box-no-nal"><?=$noResults?></div><?
    }


    ?><a href="<?=$backUrl?>" class="back">Вернуться в каталог шин</a>

</div>
</div>