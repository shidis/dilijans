<div class="search_replaceable_content" num="<?=$num?>">
<?
if(!empty($cat)){
    ?>
    <table class="sorting_area">
        <tr>
            <td style="text-align: left;">
                <b>Найдено шин: <?=$num?>.</b>
            </td>
            <td>
                <div class="vids">
                    <a href="#" class="setBlockMode"></a>
                    <a href="#" class="active"></a>
                </div>
            </td>
            <td>
                <?/* if(!empty($limits)){*/?><!--

                    <div class="limits-wrapper">
                        <span>Выводить по:</span>
                        <select name="" class="limits"><?/*
                            foreach($limits as $v) {
                                */?><option value="<?/*= $v */?>"<?/*=$v==$limit?' selected':''*/?>><?/*= $v */?></option><?/*
                            }
                            */?></select>
                        <span class="r">строк</span>
                    </div>

                --><?/* }*/?>
            </td>
        </tr>
        <tr>
            <td colspan="3" style="text-align: left;">
                <div class="sort-01">
                    <span>Сортировать:</span>
                    <div>
                        <ul class="tsort">
                            <li val="0" class="<?=$sortBy==0?'selected':''?>">по популярности</li>
                            <!--<li val="1" class="<?/*=$sortBy==1?'selected':''*/?>">названию (А-Я)</li>
                            <li val="-1" class="<?/*=$sortBy==-1?'selected':''*/?>">названию (Я-А)</li>-->
                            <li val="2" class="<?=$sortBy==2?'selected':''?>">сначала дешевые</li>
                            <li val="-2" class="<?=$sortBy==-2?'selected':''?>">сначала дорогие</li>
                            <li val="3" class="<?=$sortBy==3?'selected':''?>">сначала новинки</li>
                        </ul>
                    </div>
                </div>
            </td>
        </tr>
       <!-- <tr>
            <td colspan="3">
                <?/* if(!empty($paginator)){
                    */?><div class="paginator"><?/*
                    */?><ul><?/*
                    foreach($paginator as $v) echo $v;
                    */?></ul><?/*
                    */?></div><?/*
                }*/?>
            </td>
        </tr>-->
    </table>
    <?
    if (method_exists($this, 'incView'))
    {
        echo $this->incView('catalog/tyres/searchLentaTable');
    }
    else{
        global $app;
        echo $app->incView('catalog/tyres/searchLentaTable');
    }
    ?>
    <div class="box-padding">

        <? if(!empty($limit) && !empty($paginator)){
            // Не выводим кнопку "Показать еще" на странице поиска
            if(!strstr($_SERVER['REQUEST_URI'], 'search.html')) {
                ?><div class="showmore" onclick="showmore()" id="showmore" style="padding-bottom: 15px;"><div id="showmore_ajaxloading"></div><a>Показать еще</a></div><?
            }
            ?><script>
                var cur_limit = <?=$limit?>;
                var lockPage = false;
            </script><?
        }?>

        <div class="vids">
            <a href="#" class="setBlockMode"></a>
            <a href="#" class="active"></a>
        </div>


        <div class="box-rez"><?
            if(!empty($paginator)){
                ?><div class="paginator" style="float: none; text-align: center;"><?
                ?><ul style="float: none;"><?
                foreach($paginator as $v) echo $v;
                ?></ul><?
                ?></div><?
            }?>

        </div>
    </div><?

}else{
    ?><div class="box-no-nal"><?=@$qtext?></div><?
}?>
</div>
