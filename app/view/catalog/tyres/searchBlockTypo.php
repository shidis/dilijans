<div class="search_replaceable_content">
<?
if(!empty($cat)){
    ?>
    <div class="box-padding">
        <div class="box-rez">

            <? if(!empty($paginator)){
                ?><div class="paginator" style="float: left"><?
                ?><ul><?
                foreach($paginator as $v) echo $v;
                ?></ul><?
                ?></div><?
            }?>
            <div class="vids">

                <? foreach($altViewMode as $v){
                    ?><a href="#" class="<?=$v?>"></a><?
                }?>

            </div>
        </div>

    </div>

    <?
    if (method_exists($this, 'incView'))
    {
        echo $this->incView('catalog/tyres/searchBlockTable');
    }
    else{
        global $app;
        echo $app->incView('catalog/tyres/searchBlockTable');
    }
    ?>

    <div class="box-padding">

        <? if(!empty($limit) && !empty($paginator)){
            ?><div onclick="showmore()" class="showmore" id="showmore" style="padding-bottom: 15px;"><div id="showmore_ajaxloading"></div><a>Показать еще</a></div><?
            ?><script>
                var cur_limit = <?=$limit?>;
                var lockPage = false;
            </script><?
        }?>

        <div class="vids">
            <a href="#" class="active"></a>
            <a href="#" class="setLentaMode"></a>
        </div>

        <div class="box-rez"><?
            if(!empty($paginator)){
                ?><div class="paginator" style="float: left; text-align: center;"><?
                ?><ul style="float: none;"><?
                foreach($paginator as $v) echo $v;
                ?></ul><?
                ?></div><?
            }?>

        </div>
    </div>

<?}else{
    ?><div class="box-no-nal"><?=@$qtext?></div><?
}?>
</div>
