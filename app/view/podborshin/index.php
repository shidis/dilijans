<div class="box-padding">
    <h1 class="title"><?=$_title?></h1>
    <div class="title_text ctext">
        <p>
            Здесь представлен сервис, при помощи которого в каталоге осуществляется подбор шин по марке автомобиля, сервис подбора поможет вам в том
            случае, если вы затрудняетесь самостоятельно определиться, какие представленные в продаже шины купить к вашему авто.
        </p>
        <p>
            Для того, чтобы осуществить выбор шин по марке авто, для начала выберите в выпадающем списке марку вашей машины. После этого система предложит вам выбрать
            интересующую модель.
        </p>
        <p>
            Сервис работает с каталогом, осуществляя выбор шин по марке автомобиля, рекомендованных к использованию заводами-производителями машин.
        </p>
    </div>
    <?      $this->incView('podborshin/quick'); ?>
    <?
    $result = Array();
    foreach ($marks as $val)
    {
        $first_letter = mb_substr($val['anc'], 0, 1, 'UTF-8');
        $result[$first_letter][] = $val; 
    }
    ?>
    <div class="search_vendors_wrapper box-block-filter">
        <h4>Выберите интересующую марку автомобиля</h4>
        <table id="search_vendors">
            <tr>
                <?
                foreach($result as $letter=>$data)
                {
                    echo '<tr><td class="search_vendors">';
                    echo '<div class="letter"><span>'.$letter.'</span></div>';
                    echo '<ul>';
                    foreach ($data as $v)
                    {
                        ?><li>
                            <a href="<?=$v['url']?>" title="<?=$v['title']?>"><?=$v['anc']?></a>
                        </li><?
                    }
                    echo '</ul>';
                    echo '</td></tr>';
                }
                ?>   
            </tr>
        </table>
        <div class="clearfix"></div>
    </div>
</div>
<div class="ctext justify outer_content">
    <p>
        В случае, если Ваш автомобиль находиться на гарантии автосалона и Вы намерены установить шины, отличные по типоразмеру от существующих, мы настоятельно
        рекомендуем проконсультироваться на предмет сохранения гарантийного обслуживания Вашего автомобиля после такой замены.
    </p>
    <h2>Подобрать шины по марке авто</h2>
    <p>
        Для того, чтобы подобрать шины для Вашего автомобиля, используйте наш сервис, и вы получите полную информацию о типоразмерах шин для Вашего автомобиля. Сервис работает следующем образом: из огромной базы типоразмеров он подбирает именно те шины,
        которые соответствуют характеристикам Вашего автомобиля, т.е. вы можете быть на 100% уверены, что если Вы купите шины, то они подойдут для Вашего автомобиля.
    </p>
</div>