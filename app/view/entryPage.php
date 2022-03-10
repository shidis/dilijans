<? $this->incView('general.top'); ?>
<?php
if(!function_exists("check_link")) {
    function check_link($url, $anc, $nofollow, $curr_url){

        $link = "<a href='{$url}' {$nofollow}>{$anc}</a>";
        if (strcmp($url, $curr_url) == 0)
            $link = $anc;
        return $link;
    }
}

$curr_url = $_SERVER['REQUEST_URI'];

?>
<div id="main">

    <?  if(empty($noSidebar)){?>

    <div id="sidebar">

        <?if(!empty($articlesSB)){
        ?><div class="box-border"><?
            ?><h3><img src="/app/images/icon-news.png" alt="">Категории</h3><?
            ?><div><ul class="list-01"><?
                    foreach($arSectionList as $v){?>
                        <li>
                            <a href="<?=$v['url']?>"><?=$v['title']?></a>
                        </li>
                    <?
                    }
                    ?></ul><?
                ?>
                </div><?
            ?></div><?
        }?>

        <div class="box-mailer">
            <h4>Новинки <br>События<br>Остатки</h4>
            Подписаться на email <br>рассылку
            <form action="#" id="subscribe">
                <input type="text" name="email" value="" placeholder="Ваш e-mail"><input type="submit" value="Ok">
            </form>
        </div>

    </div>

<?  }   // noSideBar ?>

    <div id="content"><?


        $this->incView($_view);

        if(!empty($bottomTextTitle)){
            ?><div class="box-padding ctext"><?
                ?><div class="title"><?
                    ?><h2><?=$bottomTextTitle?></h2><?
                ?></div><?
                echo $bottomText;
            ?></div><?
        }elseif(!empty($bottomText)){
            ?><div class="box-padding ctext"><?
                echo $bottomText;
            ?></div><?
        }
        if(!empty($this->controllerInstance->yandex_social_share))
        {
            echo '<div class="social_share">'.$this->controllerInstance->yandex_social_share.'</div>';
        }

    ?></div>

</div>

<? $this->incView('general.bottom');