<? $this->incView('general.top'); ?>

<div class="inner-page-wrapper">

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
	<div id="main" class="inner-page">

		<?  if(empty($noSidebar)){?>

		<div id="sidebar">

			<?
			// Убираем баннер, т.к. его или переделывать на выбор шин/дисков или делать 2 баннера 
			//$this->incView('blocks/sidebarAP');

			if(!isset(App_Route::$param['gr']) || App_Route::$param['gr']==1) {
				$this->incView('blocks/sidebarTFilter');
				$this->incView('blocks/sidebarDFilter');
			}else{
				$this->incView('blocks/sidebarDFilter');
				$this->incView('blocks/sidebarTFilter');
			}

			if(!empty($qbrands)){
				?><div class="box-border">
					<h3 class="wim">Переход в каталог</h3>
					<div>
						<div class="select-02">
							<span></span>
							<select name="bid0" class="qbrands">
								<option value="">Производитель</option><?
								foreach($qbrands as $group=>$v){
									if($group!==0){
										?><optgroup label="<?=$group?>"><?
									}
									foreach($v as $vv){
										?><option value="<?=$vv['sname']?>"><?=$vv['name']?></option><?
									}
									if(count($qbrands>1)){
										?></optgroup><?
									}
								}
							?></select>
							<i></i>
						</div>
					</div>
				</div><?
			}

	//
			$nofollow = '';
			// на странице брендов шин или дисков
			/*if (strpos($curr_url, '/db/') !== FALSE or strpos($curr_url, '/tb/') !== FALSE){
				$nofollow = ' rel="nofollow"';
			}*/
	//

			if(@App_Route::$param['gr']==2 && !empty($qmodels)){
				?><div class="box-border"><?
					?><h3 class="wim">Линейка дисков <?=$bname?></h3><?
					?><div><?
						?><ul class="wnal"><?
						foreach($qmodels as $v){
							?><li<?=!$v['scDiv']?' class="nnal"':''?>><?=check_link($v['url'], $v['anc'], $nofollow, $curr_url)?></li><?
						}
						?></ul><?
					?></div><?
				?></div><?
			}


			if(@App_Route::$param['gr']==1 && !empty($qmodels)){
				?><div class="box-border"><?
					?><h3 class="wim">Линейка шин <?=$bname?></h3><?
					?><ul class="menu-tipes"><?
						if(!empty($qmodels[1])){
							?><li<?=$qmodels['active']==1?' class="active"':''?>><?
								?><a href="#" class="h1">Летние шины <?=$bname?></a><?
								?><ul class="wnal"><?
									foreach($qmodels[1] as $v){
										?><li<?=!$v['scDiv']?' class="nnal"':''?>><?=check_link($v['url'], $v['anc'], $nofollow, $curr_url)?></li><?
									}
								?></ul><?
							?></li><?
						}
						if(!empty($qmodels[2])){
							?><li<?=$qmodels['active']==2?' class="active"':''?>><?
								?><a href="#" class="h1">Зимние шины <?=$bname?></a><?
								?><ul class="wnal"><?
									foreach($qmodels[2] as $v){
										?><li<?=!$v['scDiv']?' class="nnal"':''?>><?=check_link($v['url'], $v['anc'], $nofollow, $curr_url)?></li><?
									}
								?></ul><?
							?></li><?
						}
						if(!empty($qmodels[3])){
							?><li<?=$qmodels['active']==3?' class="active"':''?>><?
								?><a href="#" class="h1">Всесезонные шины <?=$bname?></a><?
								?><ul class="wnal"><?
									foreach($qmodels[3] as $v){
										?><li<?=!$v['scDiv']?' class="nnal"':''?>><?=check_link($v['url'], $v['anc'], $nofollow, $curr_url)?></li><?
									}
								?></ul><?
							?></li><?
						}
					?></ul><?
				?></div><?
			}

			if(!empty($articlesSB)){
				?><div class="box-border"><?
					?><h3><img src="/app/images/icon-info.png" alt="">Информация</h3><?
					?><div><?
						?><ul class="list-01"><?
							foreach($articlesSB as $v){
								if (strcmp($curr_url,$v['url']) == 0)
									continue;
								?><li><a href="<?=$v['url']?>"><?=$v['title']?></a></li><?
							}
							?></ul><?
						?><a href="<?=$allArticlesUrl?>" class="more">Все статьи</a><?
					?></div><?
				?></div><?
			}?>


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
	</div><!--#wrapper-->
</div>

<? $this->incView('general.bottom');