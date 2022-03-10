<!DOCTYPE html>
<html lang="ru">
<head>
<!-- <meta name="viewport" content="width=device-width, initial-scale=0"> -->
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimal-ui" />
<meta charset="utf-8">
<title><?=preg_replace('/\s+/',' ', $title)?></title>
<meta name="description" content="<?=@$description?>">
<meta name="keywords" content="<?=@$keywords?>">
<?    
echo $metaCSS.$CSS;
echo $metaJS.$JS;
if (isset($extra_meta) && !empty($extra_meta))
{
    echo $extra_meta."\n";
}

$curr_url = $_SERVER['REQUEST_URI'];

if(!function_exists("make_route")) {
    function make_route($str){
        return "/".App_Route::_getUrl($str).".html";
    }
}

if(!function_exists("check_nofollow")) {
    function check_nofollow($str1, $str2){
        return strcmp($str1, $str2) == 0 ? ' rel="nofollow"' : '';
    }
}



$route_tCat = make_route('tCat');
$route_tWinter = make_route('tWinter');
$route_tShip = make_route('tShip');
$route_tNeShip = make_route('tNeShip');
$route_tSummer = make_route('tSummer');
$route_tAllW = make_route('tAllW');
$route_tSUV = make_route('tSUV');
$route_tStrong = make_route('tStrong');
$route_tBySize = make_route('tBySize');
$route_avtoPodbor0 = make_route('avtoPodborShin');
$route_avtoPodbor1 = make_route('avtoPodborDiskovIndex');

$route_scalc = make_route('scalc');
$route_articles_maker = "/".App_Route::_getUrl('articles')."/marker.html";

$route_tuning = "/".App_Route::_getUrl('articles')."/amerikanskie-diski-dlya-tyuninga.html";
$route_articles_maker_d = "/".App_Route::_getUrl('articles')."/markirovka.html";

$route_dCat = make_route('dCat');
$route_dShtamp = make_route('dShtamp');
$route_dKovanye = make_route('dKovanye');
$route_dBySize = make_route('dBySize');
$route_replicaCat = make_route('replicaCat');

$route_page_dostavka = "/".App_Route::_getUrl('page')."/dostavka.html";
$route_page_garant = "/".App_Route::_getUrl('page')."/garant.html";

$route_page_about = "/i/about.html";
$route_novinkiSezona = make_route('novinkiSezona');
$route_news = make_route('news');
$route_articles = make_route('articles');
$route_entrysection = make_route('entrysection');

$route_page_contacts = "/i/contacts.html";?>
<!--[if lt IE 9]><script src="/app/js/lib/html5shiv.js"></script><![endif]-->
<!--<script>
    (function(d, w, c, e, l) {
        w[c] = w[c] || '7ZV5sYAIXs7USmCnbjUXFNtopWyOZznS';
        w[e] = w[e] || 'antisov.ru';
        w[l] = w[l] || 1;
        var s = document.createElement('script');
        s.type = 'text/javascript';
        s.src = 'https://cdn.' + w[e] + '/advisor.js';
        s.async = true;
        try {
            d.getElementsByTagName('head')[0].appendChild(s);
        } catch (e) {}
    })(document, window, 'AdvisorApiToken', 'AdvisorHost', 'AdvisorSecure');
</script>-->
</head>
<?php
	$body_class_array = array();
	if(!empty($_SERVER['REQUEST_URI'])) {
		$path_params = pathinfo($_SERVER['REQUEST_URI']);
		
		if(!empty($path_params['dirname'])) {
			if($path_params['dirname'] !== '/' && !empty($path_params['filename'])) {
				$body_class_array[] = 'dir-' . str_replace(['/', '.', ' '], '', $path_params['dirname']);
				$body_class_array[] = 'inner-page';
			}
			
			if ($path_params['dirname'] == '/' && empty($path_params['filename'])) {
				$body_class_array[] = 'front-page';
			}
		}
		
		if(!empty($path_params['filename'])) {
			$body_class_array[] = 'file-' . str_replace(['/', '.', ' '], '_', $path_params['filename']);
			
		}
		
		$body_class = implode(' ', $body_class_array);
	}
	
?>
<body class="<?=strtolower($body_class)?>">
<div class="mobile-header">
	<div class="mobile-header__holder">
		<div class="mobile-header__panel">
			<div class="mobile-header__logo">
				<?php
				if (strcmp($curr_url, '/') == 0) {
				?>
					<img src="/app/images/logo.png" alt="Dilijans - шины и диски" class="mobile-header__logo-image">
				<?php
				} else {?>
					<a href="/" alt="Dilijans - шины и диски" title="Dilijans - шины и диски" class="mobile-header__logo-wrapper">
						<img src="/app/images/logo.png" alt="Dilijans - шины и диски" class="mobile-header__logo-image">
					</a>
				<?php
				}
				?>
				
				<div class="mobile-header__region">
					<div class="select-05 region-snippet">
						<span class="region-snippet__label"></span>
						<select id="cityIdMob" class="region-snippet__select"><?
							foreach($cities as $k=>$v){
								?><option value="<?=$k?>"<?=$k==$cityId?' selected':''?>><?=$v['city']?></option><?
							}
						?></select>
					</div>
				</div>
			</div>
						
			<div class="mobile-header__work-time">
				<div class="work_time">
					<span class="wt_header">График работы</span>
					<ul>
						<li><span>Будни с 9-00 до 20-00</span></li>
						<li><span>Сб-Вс с 10-00 до 18-00</span></li>
					</ul>
					<a class="button phone recall" href="#">Обратный звонок</a>
				</div>
			</div>
			
			<div class="mobile-header__phone">
				<div class="tel1">
					<p>для Москвы и области</p>
					<? if($isMobile){?>
						<a href="tel:<?=$mtel?>"><span><?=$telHeader?></span></a>
					<? } else{?>
						<span><?=$telHeader?></span>
					<? }?>
				</div>
				<div class="tel2">
					<p>Звонок по России бесплатный</p>
					<? if($isMobile){?>
						<a href="tel:<?=$mtel2?>"><span><?=$tel2Header?></span></a>
					<? } else{?>
						<span><?=$tel2Header?></span>
					<? }?>
				</div>
			</div>
			
			<div class="mobile-header__basket">
				<div class="basket">

				</div>
			</div>
			<div class="mobile-header__info">
				<button type="button" class="info-trigger js-info-trigger">
					<svg height="512pt" viewBox="0 0 512 512" width="512pt" xmlns="http://www.w3.org/2000/svg"><path d="m277.332031 128c0 11.78125-9.550781 21.332031-21.332031 21.332031s-21.332031-9.550781-21.332031-21.332031 9.550781-21.332031 21.332031-21.332031 21.332031 9.550781 21.332031 21.332031zm0 0"/><path d="m256 394.667969c-8.832031 0-16-7.167969-16-16v-154.667969h-21.332031c-8.832031 0-16-7.167969-16-16s7.167969-16 16-16h37.332031c8.832031 0 16 7.167969 16 16v170.667969c0 8.832031-7.167969 16-16 16zm0 0"/><path d="m453.332031 512h-394.664062c-32.363281 0-58.667969-26.304688-58.667969-58.667969v-394.664062c0-32.363281 26.304688-58.667969 58.667969-58.667969h394.664062c32.363281 0 58.667969 26.304688 58.667969 58.667969v394.664062c0 32.363281-26.304688 58.667969-58.667969 58.667969zm-394.664062-480c-14.699219 0-26.667969 11.96875-26.667969 26.667969v394.664062c0 14.699219 11.96875 26.667969 26.667969 26.667969h394.664062c14.699219 0 26.667969-11.96875 26.667969-26.667969v-394.664062c0-14.699219-11.96875-26.667969-26.667969-26.667969zm0 0"/><path d="m304 394.667969h-96c-8.832031 0-16-7.167969-16-16s7.167969-16 16-16h96c8.832031 0 16 7.167969 16 16s-7.167969 16-16 16zm0 0"/></svg>
				</button>
			</div>
			
			<div class="mobile-header__super-phone">
				<a href="tel:<?=$mtel?>"><?=$telHeader?></a>
			</div>
			<div class="mobile-header__burger-wrapper">
				<div class="mobile-header__burger">
					<button type="button" class="burger js-burger-trigger">Показать/скрыть навигацию</button>
				</div>
			</div>
			
		</div>
	</div>
</div>
 
<div class="mobile-navigation">
	<div class="mobile-header__holder">
		<div class="mobile-navigation__region">
			<span class="wt_header">Доставка по России:</span>
			<div class="select-05 region-snippet">
				<span class="region-snippet__label"></span>
				<select id="cityIdMob" class="region-snippet__select"><?
					foreach($cities as $k=>$v){
						?><option value="<?=$k?>"<?=$k==$cityId?' selected':''?>><?=$v['city']?></option><?
					}
				?></select>
			</div>
		</div>
		<div class="mobile-navigation__search">
			<div class="search">
				<form name="mobile" action="/<?=App_Route::_getUrl('search')?>.html">
					<input type="text" name="q" <?=!empty($search->q)?"value=\"{$search->q}\"":'placeholder="поиск по сайту"'?>>
					<input type="button" value="Найти" title="Найти">
				</form>
			</div>
		</div>
		<div class="mobile-navigation__nav">
			<ul class="mb-nav">
				<li class="mb-nav__item">
				  <a href="<?=$route_tCat?>" class="mb-nav__link" <?=check_nofollow($curr_url,$route_tCat)?>>Шины</a>
				  <button class="mb-nav__trigger js-nav-trigger">Переключить меню</button>
				  <div class="mb-nav__subnav">
					<ul class="mb-sub-nav">
					  <li class="mb-sub-nav__item">
						<a href="<?=$route_tSummer?>"<?=check_nofollow($curr_url,$route_tSummer)?> class="mb-sub-nav__link">
						  <span class="mb-nav__icon-wrapper">
							<img src="/app/images/sun.png" alt="" class="mb-sub-nav__icon">
						  </span>
						  <span class="mb-nav__text">Летние шины</span>
						</a>
					  </li>
					  <li class="mb-sub-nav__item">
						<a href="<?=$route_tAllW?>"<?=check_nofollow($curr_url,$route_tAllW)?> class="mb-sub-nav__link">
						  <span class="mb-nav__icon-wrapper">
							<img src="/app/images/sunsnow.png" alt="" class="mb-sub-nav__icon">
						 </span>
						 <span class="mb-nav__text">Всесезонные шины</span>
						</a>
					  </li>
					  <li class="mb-sub-nav__item">
						<a href="<?=$route_tWinter?>"<?=check_nofollow($curr_url,$route_tWinter)?> class="mb-sub-nav__link">
						  <span class="mb-nav__icon-wrapper">
							<img src="/app/images/snow.png" alt="" class="mb-sub-nav__icon">
						  </span>
						  <span class="mb-nav__text">Зимние шины</span>
						</a>
						<button class="mb-nav__trigger js-nav-trigger">Переключить меню</button>
						<div class="mb-nav__subnav">
						  <ul class="mb-sub-nav mb-sub-nav_type_super">
							<li class="mb-sub-nav__item">
							  <a href="<?=$route_tShip?>"<?=check_nofollow($curr_url,$route_tShip)?> class="mb-sub-nav__link">
								<span class="mb-nav__icon-wrapper">
								  <img src="/app/images/ship.png" alt="" class="mb-sub-nav__icon">
								</span>
								<span class="mb-nav__text">Зимние шипованные шины</span>
							  </a>
							</li>
							<li class="mb-sub-nav__item">
							  <a href="<?=$route_tNeShip?>"<?=check_nofollow($curr_url,$route_tNeShip)?> class="mb-sub-nav__link">
								<span class="mb-nav__icon-wrapper">&nbsp;</span>
								<span class="mb-nav__text">Зимние нешипованные шины</span>
							  </a>
							</li>
						  </ul>
						</div>
					  </li>
					  <li class="mb-sub-nav__item">
						<a href="<?=$route_tSUV?>"<?=check_nofollow($curr_url,$route_tSUV)?> class="mb-sub-nav__link">
						  <span class="mb-nav__icon-wrapper">
							<img src="/app/images/vned_ico.png" alt="" class="mb-sub-nav__icon">   
						  </span>
						  <span class="mb-nav__text">Шины для внедорожников</span>
						</a>
					  </li>
					  <li class="mb-sub-nav__item">
						<a href="<?=$route_tStrong?>"<?=check_nofollow($curr_url,$route_tStrong)?> class="mb-sub-nav__link">
						  <span class="mb-nav__icon-wrapper">
							<img src="/app/images/usil_ico.png" alt="" class="mb-sub-nav__icon">
						  </span>
						  <span class="mb-nav__text">Усиленные шины</span>
						</a>
					  </li>
					  <li class="mb-sub-nav__item">
						<span href="#" class="mb-sub-nav__link">
						  <span class="mb-nav__icon-wrapper">
							<img src="/app/images/snow.png" alt="" class="mb-sub-nav__icon">
						  </span>
						  <span class="mb-nav__text">Быстрый подбор</span>
						</span>
						<button class="mb-nav__trigger js-nav-trigger">Переключить меню</button>
						<div class="mb-nav__subnav">
						  <ul class="mb-sub-nav mb-sub-nav_type_super">
							<li class="mb-sub-nav__item">
							  <a href="<?=$route_tCat?>"<?=check_nofollow($curr_url, $route_tCat)?> class="mb-sub-nav__link">
								<span class="mb-nav__text">По размеру</span>
							  </a>
							</li>
							<li class="mb-sub-nav__item">
							  <a href="<?=$route_avtoPodbor0?>"<?=check_nofollow($curr_url,$route_avtoPodbor0)?> class="mb-sub-nav__link">
								<span class="mb-nav__text">По автомобилю</span>
							  </a>
							</li>
							<li class="mb-sub-nav__item">
							  <a href="<?=$route_tBySize?>"<?=check_nofollow($curr_url,$route_tBySize)?> class="mb-sub-nav__link">
								<span class="mb-nav__text">Разноразмерные шины</span>
							  </a>
							</li>
							<li class="mb-sub-nav__item">
							  <a href="<?=$route_scalc?>"<?=check_nofollow($curr_url,$route_scalc)?> class="mb-sub-nav__link">
								<span class="mb-nav__text">Калькулятор шин</span>
							  </a>
							</li>
							<li class="mb-sub-nav__item">
							  <a href="<?=$route_articles_maker?>"<?=check_nofollow($curr_url,$route_articles_maker)?> class="mb-sub-nav__link">
								<span class="mb-nav__text">Маркировка шин</span>
							  </a>
							</li>
						  </ul>
						</div>
					  </li>
					  <li class="mb-sub-nav__item">
						<span href="#" class="mb-sub-nav__link">
						  <span class="mb-nav__text">Бренды шин</span>
						</span>
						<button class="mb-nav__trigger js-nav-trigger">Переключить меню</button>
						<div class="mb-nav__subnav">
						  <ul class="mb-sub-nav mb-sub-nav_type_super">
							<?
								foreach ($menu_brands[1] as $prior=>$brands_array)
								{
									foreach ($brands_array as $brand) {
										echo '<li class="mb-sub-nav__item"><a href="'.$brand['url'].'" class="mb-sub-nav__link"><span class="mb-nav__text">'.$brand['name'].'</span></a></li>';
									}
								}
							?>
							<li class="mb-sub-nav__item"><a href="<?='/'.App_Route::_getUrl('tCat').'.html'?>" class="mb-sub-nav__link"><span class="mb-nav__text">Все производители</span></a></li>
						  </ul>
						</div>
					  </li>
					</ul>
				  </div>
				</li>
				<li class="mb-nav__item">
				  <a href="<?=$route_dCat?>"<?=check_nofollow($curr_url,$route_dCat)?> class="mb-nav__link">Диски</a>
				  <button class="mb-nav__trigger js-nav-trigger">Переключить меню</button>
				  <div class="mb-nav__subnav">
					<ul class="mb-sub-nav">
					  <li class="mb-sub-nav__item">
						<a href="<?=$route_dCat?>"<?=check_nofollow($curr_url,$route_dCat)?> class="mb-sub-nav__link">
						  <span class="mb-nav__icon-wrapper">
							<img src="/app/images/l_disk_ico.png" alt="" class="mb-sub-nav__icon">
						  </span>
						  <span class="mb-nav__text">Литые диски</span>
						</a>
					  </li>
					  <li class="mb-sub-nav__item">
						<a href="<?=$route_dKovanye?>" class="mb-sub-nav__link">
						  <span class="mb-nav__icon-wrapper">
							<img src="/app/images/k_disk_ico.png" alt="" class="mb-sub-nav__icon">
						 </span>
						 <span class="mb-nav__text">Кованые диски</span>
						</a>
					  </li>
					  <li class="mb-sub-nav__item">
						<a href="<?=$route_dShtamp?>" class="mb-sub-nav__link">
						  <span class="mb-nav__icon-wrapper">
							<img src="/app/images/s_disk_ico.png" alt="" class="mb-sub-nav__icon">
						  </span>
						  <span class="mb-nav__text">Штампованые диски</span>
						</a>
					  </li>
					  <li class="mb-sub-nav__item">
						<span href="#" class="mb-sub-nav__link mb-sub-nav__link_type_inactive">
						  <span class="mb-nav__icon-wrapper">
							<img src="/app/images/vn_disk_ico.png" alt="" class="mb-sub-nav__icon">   
						  </span>
						  <span class="mb-nav__text">Диски для внедорожников</span>
						</span>
					  </li>
					  <li class="mb-sub-nav__item">
						<span href="#" class="mb-sub-nav__link">
						  <span class="mb-nav__icon-wrapper">
							<img src="/app/images/snow.png" alt="" class="mb-sub-nav__icon">
						  </span>
						  <span class="mb-nav__text">Быстрый подбор</span>
						</span>
						<button class="mb-nav__trigger js-nav-trigger">Переключить меню</button>
						<div class="mb-nav__subnav">
						  <ul class="mb-sub-nav mb-sub-nav_type_super">
							<li class="mb-sub-nav__item">
							  <a href="<?=$route_dBySize?>"<?=check_nofollow($curr_url,$route_dBySize)?> class="mb-sub-nav__link">
								<span class="mb-nav__text">По размеру</span>
							  </a>
							</li>
							<li class="mb-sub-nav__item">
							  <a href="<?=$route_avtoPodbor1?>"<?=check_nofollow($curr_url,$route_avtoPodbor1)?> class="mb-sub-nav__link">
								<span class="mb-nav__text">По автомобилю</span>
							  </a>
							</li>
							<li class="mb-sub-nav__item">
							  <a href="<?=$route_tuning?>"<?=check_nofollow($curr_url,$route_scalc)?> class="mb-sub-nav__link">
								<span class="mb-nav__text">Диски для тюнинга</span>
							  </a>
							</li>
							<li class="mb-sub-nav__item">
							  <a href="<?=$route_articles_maker_d?>"<?=check_nofollow($curr_url,$route_articles_maker)?> class="mb-sub-nav__link">
								<span class="mb-nav__text">Маркировка колес</span>
							  </a>
							</li>
						  </ul>
						</div>
					  </li>
					  <li class="mb-sub-nav__item">
						<span href="#" class="mb-sub-nav__link">
						  <span class="mb-nav__text">Популярные производители дисков</span>
						</span>
						<button class="mb-nav__trigger js-nav-trigger">Переключить меню</button>
						<div class="mb-nav__subnav">
						  <ul class="mb-sub-nav mb-sub-nav_type_super">
							<?
							foreach ($menu_brands[2] as $prior=>$brands_array)
							{
								foreach ($brands_array as $brand) {
									echo '<li class="mb-sub-nav__item"><a href="' . $brand['url'] . '" class="mb-sub-nav__link"><span class="mb-nav__text">' . $brand['name'] . '</span></a></li>';
								}
							}
							?>
							<li class="mb-sub-nav__item"><a href="<?='/'.App_Route::_getUrl('dCat').'.html'?>" class="mb-sub-nav__link"><span class="mb-nav__text">Все производители</span></a></li>
						  </ul>
						</div>
					  </li>
					</ul>
				  </div>
				</li>
				<li class="mb-nav__item">
				  <a href="<?=$route_replicaCat?>"<?=check_nofollow($curr_url,$route_replicaCat)?> class="mb-nav__link">Диски Replica</a>
				</li>
				<li class="mb-nav__item">
				  <a href="<?=$route_page_dostavka?>"<?=check_nofollow($curr_url,$route_page_dostavka)?> class="mb-nav__link">Доставка и оплата</a>
				</li>
				<li class="mb-nav__item">
				  <a href="<?=$route_page_garant?>"<?=check_nofollow($curr_url,$route_page_garant)?> class="mb-nav__link">Гарантия</a>
				</li>
				<li class="mb-nav__item">
				  <a href="<?=$route_entrysection?>"<?=check_nofollow($curr_url,$route_entrysection)?> class="mb-nav__link">Новости</a>
				</li>
				<li class="mb-nav__item">
				  <a href="<?=$route_page_contacts?>"<?=check_nofollow($curr_url,$route_page_contacts)?> class="mb-nav__link">Контакты</a>
				</li>
				<li class="mb-nav__item">
				  <a href="<?=$route_page_about?>"<?=check_nofollow($curr_url,$route_page_about)?> class="mb-nav__link">О магазине</a>
				</li>
				<li class="mb-nav__item">
				  <a target="_blank" href="http://clck.yandex.ru/redir/dtype=stred/pid=47/cid=2508/*https://market.yandex.ru/shop/98540/reviews" class="mb-nav__link">Отзывы</a>
				</li>
			</ul>
		</div>
	</div>
</div>

<div class="mobile-info">
	<div class="mobile-header__holder">
		<div class="mobile-info__work-time">
			<div class="work_time">
				<span class="wt_header">График работы:</span>
				<ul>
					<li><span>Будни с 9-00 до 20-00</span></li>
					<li><span>Сб-Вс с 10-00 до 18-00</span></li>
				</ul>
			</div>
		</div>
		<div class="mobile-info__phone">	
			<div class="tel1">
				<p>для Москвы и области:</p>
				<? if($isMobile){?>
					<a href="tel:<?=$mtel?>"><span><?=$telHeader?></span></a>
				<? } else{?>
					<span><?=$telHeader?></span>
				<? }?>
			</div>
			<div class="tel2">
				<p>Звонок по России бесплатный:</p>
				<? if($isMobile){?>
					<a href="tel:<?=$mtel2?>"><span><?=$tel2Header?></span></a>
				<? } else{?>
					<span><?=$tel2Header?></span>
				<? }?>
			</div>
		</div>
		<div class="mobile-info__controls">
			<div class="mobile-info__control">
				<a class="button phone recall" href="#">Обратный звонок</a>
			</div>
			<div class="mobile-info__control">
				<div class="vk-c"></div>
			</div>
		</div>
	</div>
</div>
<div id="wrapper">
	<header>
        <div class="box-logo">
            <?php
                $logo = '<img src="/app/images/logo.png" alt="Dilijans - шины и диски">';
            ?>
            <div class="logo">
                <?php
                if (strcmp($curr_url, '/') == 0)
                    echo $logo;
                else{?>
                    <a href="/" alt="Dilijans - шины и диски" title="Dilijans - шины и диски"><?=$logo?></a>
                <?php
                }
                ?>
            </div>
            <div class="city">
                Доставка по России:
                <div class="select-05">
                    <span></span>
                    <select id="cityId"><?
                        foreach($cities as $k=>$v){
                            ?><option value="<?=$k?>"<?=$k==$cityId?' selected':''?>><?=$v['city']?></option><?
                        }
                    ?></select>
                </div>
            </div>
            <div class="basket"></div>
            <div class="work_time">
                <span class="wt_header">График работы</span>
                <ul>
                    <li><span>Будни с 9-00 до 20-00</span></li>
                    <li><span>Сб-Вс с 10-00 до 18-00</span></li>
                </ul>
            </div>
            <div class="phones">

                <div class="k1">
                    <a class="button phone recall" href="#">Обратный звонок</a>
                    <div class="vk-c"></div>
                </div>

                <div class="k2">
                    <div class="tel1">
                        <p>для Москвы и области</p>
                        <? if($isMobile){?>
                            <a href="tel:<?=$mtel?>"><span><?=$telHeader?></span></a>
                        <? } else{?>
                            <span><?=$telHeader?></span>
                        <? }?>
                    </div>
                    <div class="tel2">
                        <p>Звонок по России бесплатный</p>
                        <? if($isMobile){?>
                            <a href="tel:<?=$mtel2?>"><span><?=$tel2Header?></span></a>
                        <? } else{?>
                            <span><?=$tel2Header?></span>
                        <? }?>
                    </div>
                </div>
            </div>
        </div>
        <nav>
            <ul>
                <li class="dropdown">
                    <a href="<?=$route_tCat?>"<?=check_nofollow($curr_url,$route_tCat)?>><i>шины</i></a>
                    <div class="wrapper nav-it1">
                        <div class="left">
                            <ul>
                                <li><img src="/app/images/sun.png" alt="летние шины"><a href="<?=$route_tSummer?>"<?=check_nofollow($curr_url,$route_tSummer)?>>Летние шины</a></li>
                                <li><img src="/app/images/sunsnow.png" alt="всесезонные шины"><a href="<?=$route_tAllW?>"<?=check_nofollow($curr_url,$route_tAllW)?>>Всесезонные шины</a></li>
                                <li>
                                    <img src="/app/images/snow.png" alt="зимние шины"><a href="<?=$route_tWinter?>"<?=check_nofollow($curr_url,$route_tWinter)?>>Зимние шины</a>
                                    <ul>
                                        <li><i></i><a href="<?=$route_tNeShip?>"<?=check_nofollow($curr_url,$route_tNeShip)?>>Зимние нешипованные шины</a></li>
                                        <li><i class="short"><img src="/app/images/ship.png" alt="шипованные шины"></i><a href="<?=$route_tShip?>"<?=check_nofollow($curr_url,$route_tShip)?>>Зимние шипованные шины</a></li>
                                    </ul>
                                </li>
                                <li><img style="top: 18px" src="/app/images/vned_ico.png" alt="Шины для внедорожников"><a style="padding-left: 35px" href="<?=$route_tSUV?>"<?=check_nofollow($curr_url,$route_tSUV)?>>Шины для внедорожников</a></li>
                                <li><img style="top: 18px" src="/app/images/usil_ico.png" alt="Усиленные шины"><a  style="padding-left: 35px" href="<?=$route_tStrong?>"<?=check_nofollow($curr_url,$route_tStrong)?>>Усиленные шины</a></li>
                            </ul>
                        </div>
                        <div class="middle">
                            <div class="middle_title">
                                Быстрый подбор
                            </div>
                            <a class="hm_button" href="<?=$route_tCat?>"<?=check_nofollow($curr_url, $route_tCat)?>>По размеру</a>
                            <a class="hm_button" href="<?=$route_avtoPodbor0?>"<?=check_nofollow($curr_url,$route_avtoPodbor0)?>>По автомобилю</a>
                            <ul>
                                <li><a href="<?=$route_tBySize?>"<?=check_nofollow($curr_url,$route_tBySize)?>>Разноразмерные шины</a></li>
                                <li><a href="<?=$route_scalc?>"<?=check_nofollow($curr_url,$route_scalc)?>>Калькулятор шин</a></li>
                                <li><a href="<?=$route_articles_maker?>"<?=check_nofollow($curr_url,$route_articles_maker)?>>Маркировка шин</a></li>
                            </ul>
                        </div>
                        <div class="right">
                            <img class="hbg_img" src="/app/images/header_shina.png" alt="">
                            <div class="right_title">
                                Бренды шин
                            </div>
                            <ul class="header_m_brands">
                                <?
                                    foreach ($menu_brands[1] as $prior=>$brands_array)
                                    {
                                        foreach ($brands_array as $brand) {
                                            echo '<li><a href="'.$brand['url'].'">'.$brand['name'].'</a></li>';
                                        }
                                    }
                                ?>
                            </ul>
                            <a href="<?='/'.App_Route::_getUrl('tCat').'.html'?>" class="all_brands">Все производители</a>
                        </div>
                    </div>
                </li>
                <li class="dropdown">
                    <a href="<?=$route_dCat?>"<?=check_nofollow($curr_url,$route_dCat)?>><i>Диски</i></a>
                    <div class="wrapper nav-it2">
                        <div class="left">
                            <ul>
                                <li><img src="/app/images/l_disk_ico.png" alt="Литые диски"><a href="<?=$route_dCat?>"<?=check_nofollow($curr_url,$route_dCat)?>>Литые диски</a></li>
                                <li><img src="/app/images/k_disk_ico.png" alt="Кованые диски"><a href="<?=$route_dKovanye?>">Кованые диски</a></li>
                                <li><img src="/app/images/s_disk_ico.png" alt="Штампованые диски"><a href="<?=$route_dShtamp?>">Штампованые диски</a></li>
                                <li style="padding-bottom: 40px;"><img src="/app/images/vn_disk_ico.png" alt="Диски для внедорожников"><a class="inactive">Диски для внедорожников</a></li>
                            </ul>
                        </div>
                        <div class="middle">
                            <div class="middle_title">
                                Быстрый подбор
                            </div>
                            <a class="hm_button" href="<?=$route_dBySize?>"<?=check_nofollow($curr_url,$route_dBySize)?>>По размеру</a>
                            <a class="hm_button" href="<?=$route_avtoPodbor1?>"<?=check_nofollow($curr_url,$route_avtoPodbor1)?>>По автомобилю</a>
                            <ul>
                                <li><a href="<?=$route_tuning?>"<?=check_nofollow($curr_url,$route_scalc)?>>Диски для тюнинга</a></li>
                                <li><a href="<?=$route_articles_maker_d?>"<?=check_nofollow($curr_url,$route_articles_maker)?>>Маркировка колес</a></li>
                            </ul>
                        </div>
                        <div class="right">
                            <img class="hbg_img" src="/app/images/header_disk.png" alt="">
                            <div class="right_title">
                                Популярные производители дисков
                            </div>
                            <ul class="header_m_brands">
                                <?
                                foreach ($menu_brands[2] as $prior=>$brands_array)
                                {
                                    foreach ($brands_array as $brand) {
                                        echo '<li><a href="' . $brand['url'] . '">' . $brand['name'] . '</a></li>';
                                    }
                                }
                                ?>
                            </ul>
                            <a href="<?='/'.App_Route::_getUrl('dCat').'.html'?>" class="all_brands">Все производители</a>
                        </div>
                    </div>
                </li>
                <li><a href="<?=$route_replicaCat?>"<?=check_nofollow($curr_url,$route_replicaCat)?>>Диски Replica</a></li>
                <li><a href="<?=$route_page_dostavka?>"<?=check_nofollow($curr_url,$route_page_dostavka)?>>доставка и оплата</a></li>
                <li><a href="<?=$route_page_garant?>"<?=check_nofollow($curr_url,$route_page_garant)?>>Гарантия</a></li>
                <li><a href="<?=$route_entrysection?>"<?=check_nofollow($curr_url,$route_entrysection)?>>новости</a></li>
                <li><a href="<?=$route_page_contacts?>"<?=check_nofollow($curr_url,$route_page_contacts)?>>Контакты</a></li>
                <li><a href="<?=$route_page_about?>"<?=check_nofollow($curr_url,$route_page_about)?>>О магазине</a></li>
                <li><a target="_blank" href="http://clck.yandex.ru/redir/dtype=stred/pid=47/cid=2508/*https://market.yandex.ru/shop/98540/reviews">Отзывы</a></li>
            </ul>
        </nav>
        <div class="box-br-search">
            <div class="search">
                <form name="desktop" action="/<?=App_Route::_getUrl('search')?>.html">
                    <input type="text" name="q" <?=!empty($search->q)?"value=\"{$search->q}\"":'placeholder="поиск по сайту"'?>>
                    <input type="button" value="Найти" title="Найти">
                </form>
            </div>
<!-- seo mark -->
<?php
$home_tag_start = '';
$home_tag_end = '';
if (strcmp($curr_url,'/') != 0){
    $home_tag_start = '<a href="/" itemprop="url">';
    $home_tag_end = '</a>';
}
?>
<!-- seo mark -->
            <div class="bread-crumbs">
                <div itemscope itemtype="http://data-vocabulary.org/Breadcrumb">

                    <?=$home_tag_start?><img src="/app/images/home.png" alt=""><span itemprop="title">Интернет магазин шин и дисков</span><?=$home_tag_end?>
                </div><?
                $i=0;

                foreach($breadcrumbs as $k=>$v){
                    /*
                     * каждая крошка может быть:
                     * 'ancor'=>array(href,title)
                     * ancor=>href
                     * ancor=>''
                     * text
                     */
                    ?><div itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><?
                        if(is_array($v)){

                            ?><a itemprop="url" href="<?=$v[0]?>" title="<?=trim($v[1])?>"><i><?=@$breadcrumbsMarkers[$i]?> </i> <span itemprop="title"><?=trim($k)?></span></a><?
                        }elseif(is_string($k)){
                            if($v!='') {?><a itemprop="url" href="<?=$v?>"><i><?=@$breadcrumbsMarkers[$i]?> </i> <span itemprop="title"><?=trim($k)?></span></a><? } else echo trim($k);
                        }elseif($v!='') echo $v;

                    ?></div><?

                    $i++;
                }
            ?></div>
        </div>
    </header>
	<div class="bread-crumbs bread-crumbs--mobile">
		<div itemscope itemtype="http://data-vocabulary.org/Breadcrumb">

			<?=$home_tag_start?><img src="/app/images/home.png" alt=""><span itemprop="title">Интернет магазин шин и дисков</span><?=$home_tag_end?>
		</div><?
		$i=0;

		foreach($breadcrumbs as $k=>$v){
			/*
			 * каждая крошка может быть:
			 * 'ancor'=>array(href,title)
			 * ancor=>href
			 * ancor=>''
			 * text
			 */
			?><div itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><?
				if(is_array($v)){

					?><a itemprop="url" href="<?=$v[0]?>" title="<?=trim($v[1])?>"><i><?=@$breadcrumbsMarkers[$i]?> </i> <span itemprop="title"><?=trim($k)?></span></a><?
				}elseif(is_string($k)){
					if($v!='') {?><a itemprop="url" href="<?=$v?>"><i><?=@$breadcrumbsMarkers[$i]?> </i> <span itemprop="title"><?=trim($k)?></span></a><? } else echo trim($k);
				}elseif($v!='') echo $v;

			?></div><?

			$i++;
		}
	?></div>
