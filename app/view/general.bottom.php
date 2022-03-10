</div><!--#wrapper-->
<?php
$curr_url = $_SERVER['REQUEST_URI'];
$logo = '<img src="/app/images/logo-footer.png" alt="Dilijans - шины и диски">';

?>
<footer>
    <div>
        <div class="box-links">
            <div class="logo-footer">
                <?php
                if (strcmp($curr_url, '/') == 0)
                    echo $logo;
                else{?>
                    <a href="/" alt="Dilijans - шины и диски" title="Dilijans - шины и диски"><?=$logo?></a>
                <?php
                }
                ?>
                <?
                $yam_show_id = Data::get('yam_raiting');
                if ($yam_show_id == 2 || $yam_show_id == 3){
                    ?>
                    <img style="margin-left: 12px;margin-top: 15px;" src="//grade.market.yandex.ru/?id=98540&amp;action=image&amp;size=0" border="0" width="88" height="31" alt="Читайте отзывы покупателей и оценивайте качество магазина на Яндекс.Маркете">
                <?}?>
				<img src="/app/images/logo1h.png" alt="mastercard visa paykeeper" class="footer-payment">
            </div>
            <div><?
                echo $footerMenu;
            ?></div>
        </div>
        <div class="copy">
            <p>&copy; 2006-<?=date("Y")?> dilijans.org. <br>Интернет-магазин шин и дисков</p>
            <div class="counter"><img class="liveinternet" src="/app/images/counter.jpg" alt=""></div>
        </div>
        <div class="offerta_footer">
            <?=$offertaFooter?>
        </div>
    </div>
</footer>



<script type="text/javascript">
    var MD=<?=(int)Tools::isMobile()?>;
    var gr=<?=(int)@App_Route::$param['gr']?>;
    <?if (App_Route::_getAction() == 'avtoPodborShin'):?>
    var apUrl='/<?=App_Route::_getUrl('avtoPodborShin')?>';
    <?elseif (App_Route::_getAction() == 'avtoPodborDiskov' or App_Route::_getAction() == 'avtoPodborDiskovIndex'):?> 
    var apUrl='/<?=App_Route::_getUrl('avtoPodborDiskov')?>';
    <?elseif (App_Route::_getAction() == 'tSearch'):?>
    var apUrl='/<?=App_Route::_getUrl('tSearch')?>';
    <?elseif (App_Route::_getAction() == 'dSearch'):?>
    var apUrl='/<?=App_Route::_getUrl('dSearch')?>';
    <?endif;?>
    var d=new Date();
    var TSinited = d.getTime();
    var YAM = '<?=Cfg::$config['YAM']['counterId']?>';
    var ax_err_show=<?=server_loc!='local'?'false':'true'?>;
    CMP.data=<?=json_encode($cmpData)?>;
    var compareUrl={
        't': '/<?=App_Route::_getUrl('compare').'/tyres'?>',
        'd': '/<?=App_Route::_getUrl('compare').'/disks'?>'
    };
    <? if(!empty($num)){
        ?>var exnum=<?=$num?>;<?
    }?>
    <? if(isset($ext_filter) && $ext_filter){
        ?>var ext_filter=<?=$ext_filter?>;<?
    }?>
<?
if($adminLogged){
	?>var adminLogged=true;<?
}
?>var VJS=<?=json_encode($VJS)?>;<?

if(!empty($seoJS)){
	?>var JSD=<?=json_encode($seoJS)?>;<?
}
echo "\r\n";
if(!empty($seoJSW)){
	?>var JSDW=<?=json_encode($seoJSW)?>;<?
}
?>
</script>
<?

if(!$adminLogged){
    echo GA::getCounter();
    echo $counters;
}
?>
<script type="text/javascript">
$.browser = {};
$.browser.mozilla = /mozilla/.test(navigator.userAgent.toLowerCase()) && !/webkit/.test(navigator.userAgent.toLowerCase());
$.browser.webkit = /webkit/.test(navigator.userAgent.toLowerCase());
$.browser.opera = /opera/.test(navigator.userAgent.toLowerCase());
$.browser.msie = /msie/.test(navigator.userAgent.toLowerCase());
$.browser.safari = /safari/.test(navigator.userAgent.toLowerCase());

if(window.mobilecheck()) {
    // $('head').append('<meta id="viewport" name="viewport" content="width=300, height=600">');
}
</script>
</body>
</html>
<!-- DB Queries=<?=Stat::$DBQueries?> | execTime=<? Stat::finish(); echo Stat::execTime()?> sec -->
