<div class="box-padding">

    <h1 class="title cat"><?=$_title?></h1><?

    if(!empty($topText)){
        ?><div class="ctext"><?
        echo $topText;
        ?></div><?
    }

    if(!empty($filter)){
        $this->incView('catalog/tyres/sizesFilter');
    }else{
        $this->incView('catalog/tyres/searchFilter');
    }?>
    <?if (!empty($s_info_str)) echo $s_info_str;?>
    <div class="box-shadow">

        <? $this->incView($searchTpl); ?>

    </div>

    <? if(!empty($rlinks)){?>

        <? foreach($rlinks as $kk=>$vv){?>

            <div class="box-padding clearfix">
                <h4><?=$vv['label']?></h4>

                <div class="box-list2">
                    <ul class="list-06"><?
                        foreach($vv['data'] as $v){
                            ?><li style="<?=$vv['listyle']?>"><a href="<?=$v['url']?>" title="<?=$v['title']?>"><?=$v['anc']?></a></li><?
                        }
                        ?></ul>
                </div>
            </div>

        <? }?>
    <? }?>

</div>