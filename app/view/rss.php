<?
    echo '<?xml version="1.0" encoding="UTF-8" ?>';
?>
<rss version="2.0">
    <channel>
        <title>dilijans.org - новости</title>
        <link>https://www.dilijans.org/</link>
        <description>На нашем сайте для вас подготовлены интересные предложения по продаже оригинальных литых дисков и автомобильных шин от производителей с мировым именем из Германии, Италии, Японии, России и других стран. Вы получаете возможность совершить выгодное приобретение, которое удовлетворит вас не только достойным качеством, но и ценой.</description>
        <language>RU</language>
        <image>
            <url>https://www.dilijans.org/app/images/logo.png</url>
            <title>dilijans.org - новости</title>
            <link>https://www.dilijans.org/</link>
        </image>
        <?
            if (!empty($lenta)){
                foreach ($lenta as $li){
                    ?>
                    <item>
                        <title><?=$li['name']?></title>
                        <link>https://<?=$li['link']?></link>
                        <pubDate><?=date('r', strtotime($li['date']))?></pubDate>
                    </item>
                    <?
                }
            }
        ?>
    </channel>
</rss>