<? 
require_once '../auth.php';
include('../struct.php');

$cp->frm['name']='slider';
$cp->frm['title']='Управление слайдером';

$cp->checkPermissions();

$db = new DB();

cp_head();
cp_css();
cp_js();

?>
<? cp_body()?>
<? cp_title()?>
<style type="text/css">

</style>
<h1>Редактирование слайдера</h1>
<?

?>
<form enctype="multipart/form-data" action="/cms/be/slider.php?act=saveSlider" method="post" id="sl-form" class="edit_area" style="display:none">
	<fieldset class="ui" style="border:1px dashed #999; padding: 10px; width: 99%">
		<legend class="ui">Загрузить изображение</legend>
		<p><label>Файл</label><br/><input name="imgFile" type="file" id="imgFile"
										  style="width: 99%"></p>

		<p><labeL>Ссылка на страницу</labeL><br/><input name="imgLink" type="text"
															  id="imgLink"
															  style="width: 99%;"/></p>
	</fieldset>
    <button type="submit" class="sl-save">Записать изменения</button>
    <button class="sl-hideForm">Скрыть форму</button>
</form>


<div id="sl-list">
	<table width="100%" border="0" cellpadding="0" cellspacing="4">
		<tr>
			<td colspan="2" valign="top">
				<?
				$slider_info = $db->fetchAll("SELECT * FROM slider ORDER BY slide_id DESC", MYSQLI_ASSOC);
				if (!empty($slider_info)) {
					$cc = new CC_Ctrl();
					?><table width="75%" border="0"><?
					foreach ($slider_info as $slide) {
						$niw = 300;
						$i = str_replace(Cfg::get('root_path'),'',$slide['image']);
						$i_ = $i . '?' . mt_rand();

						if (!empty($i)) {
							list($width, $height) = @getimagesize($slide['image']);
							?>
							<tr><td>
							<br>
							<form enctype="multipart/form-data" action="/cms/be/slider.php?act=editSlider" method="post">
								<input type="hidden" value="<?=$slide['slide_id']?>" name="slide_id" />
								<fieldset class="ui"
										  style="float:left; margin:0 15px 15px; border:1px dashed #999; width:320px; overflow:hidden; padding:10px;">
									<legend class="ui">Изображение</legend>
									<div style="float:left; overflow:hidden; margin-right:20px; width: <?= $niw ?>px">
										<a href="<?= $i_ ?>" rel="zoom" title="наибольшее изображение"><img
												width="<?= $niw ?>"
												src="<?= $i_ ?>"/></a>
									</div>

									<div style="float:left; overflow:hidden; margin-right:10px; width: 190px">
										<p style="line-height: 25px">
											<b><?= $width ?></b> x <b><?= $height ?></b> px<br>
										</p>
									</div>
								</fieldset>
								<fieldset class="ui" style="border:1px dashed #999; padding: 10px">
									<legend class="ui">Земенить изображение</legend>
									<p><label>Файл</label><br/><input name="imgFile_<?=$slide['slide_id']?>" type="file" id="imgFile_<?=$slide['slide_id']?>"
																	  style="width: 99%"></p>

									<p><labeL>Ссылка на страницу</labeL><br/><input name="imgLink_<?=$slide['slide_id']?>" type="text"
																						  id="imgLink_<?=$slide['slide_id']?>"
																						  style="width: 99%;" value="<?=$slide['slide_link']?>"/></p>
									<p><input name="delImg_<?=$slide['slide_id']?>" type="checkbox" id="delImg_<?=$slide['slide_id']?>" value="1"> <label for="delImg_<?=$slide['slide_id']?>">удалить
											изображение</label></p>
									<button class="sl-edit" style="margin-top:15px;" type="submit">Сохранить</button>
								</fieldset>
							</form>
							</td></tr>
							<?
						}
					}
					?></table><?
				}
				?>

			</td>
		</tr>
	</table>
   <button style="margin-top:15px;" class="sl-add">Добавить изображение</button>
</div>

<? cp_end()?>
