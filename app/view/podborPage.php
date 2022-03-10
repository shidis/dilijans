<div class="inner-page-wrapper">
	<? 
	$this->incView('general.top');
	?>
	<div id="main" class="podbor-page">

		<?  if(empty($noSidebar)){?>

		<div id="sidebar">

			<?

			//$this->incView('blocks/sidebarAP');

			if(strstr(App_Route::$controller, 'podbordiskov/')) {
				//$this->incView('blocks/sidebarTFilter');
				$this->incView('blocks/sidebarDFilter');
			}elseif(strstr(App_Route::$controller, 'podborshin/')) {
				//$this->incView('blocks/sidebarDFilter');
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

			if(@App_Route::$param['gr']==2 && !empty($qmodels)){
				?><div class="box-border"><?
					?><h3 class="wim">Линейка дисков <?=$bname?></h3><?
					?><div><?
						?><ul class="wnal"><?
						foreach($qmodels as $v){
							?><li<?=!$v['scDiv']?' class="nnal"':''?>><a href="<?=$v['url']?>"><?=$v['anc']?></a></li><?
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
										?><li<?=!$v['scDiv']?' class="nnal"':''?>><a href="<?=$v['url']?>"><?=$v['anc']?></a></li><?
									}
								?></ul><?
							?></li><?
						}
						if(!empty($qmodels[2])){
							?><li<?=$qmodels['active']==2?' class="active"':''?>><?
								?><a href="#" class="h1">Зимние шины <?=$bname?></a><?
								?><ul class="wnal"><?
									foreach($qmodels[2] as $v){
										?><li<?=!$v['scDiv']?' class="nnal"':''?>><a href="<?=$v['url']?>"><?=$v['anc']?></a></li><?
									}
								?></ul><?
							?></li><?
						}
						if(!empty($qmodels[3])){
							?><li<?=$qmodels['active']==3?' class="active"':''?>><?
								?><a href="#" class="h1">Всесезонные шины <?=$bname?></a><?
								?><ul class="wnal"><?
									foreach($qmodels[3] as $v){
										?><li<?=!$v['scDiv']?' class="nnal"':''?>><a href="<?=$v['url']?>"><?=$v['anc']?></a></li><?
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


		?></div>

	</div>

	</div><!--#wrapper-->
	
	<? $this->incView('general.bottom');
