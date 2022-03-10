<!-- meta start-->
<?
$bname=Tools::unesc($cc->qrow['bname']);
$mname=Tools::unesc($cc->qrow['name']);
$balt=Tools::unesc($cc->qrow['balt']!=''?$cc->qrow['balt']:$cc->qrow['bname']);
$malt=Tools::unesc($cc->qrow['alt']!=''?$cc->qrow['alt']:$cc->qrow['name']);
$s = explode(',', $balt);
$balt1=$s[0];
$s = explode(',', $malt);
$malt1=$s[0];
$msuffix=Tools::unesc($cc->qrow['suffix']);
if ($cc->qrow['gr'] == 2){
    $uri_replace = '/'.App_Route::_getUrl('dModel').'/'.$cc->qrow['sname'].'.html';
    if($cc->qrow['replica']==1) {
        $_title="Replica {$bname} {$mname} {$msuffix}";
        $title="Replica {$bname} {$mname} {$msuffix} - цены, фото, купить";
        $description="Все размеры и цены дисков replica {$bname} {$mname}. Актуальное наличие, фото и цены на литые диски реплика {$balt1} {$malt}";
        $keywords="{$bname} {$mname}, купить диски {$balt1} {$malt}";
    }else{
        $_title="{$bname} {$mname} {$msuffix}";
        $title="{$bname} {$mname} {$msuffix} - цены, фото, купить";
        $description="Все размеры и цены дисков {$bname} {$mname}. Актуальное наличие, фото и цены на литые диски {$balt1} {$malt}";
        $keywords="{$bname} {$mname}, купить диски {$balt1} {$malt}";
    }
}
else{
    $uri_replace = '/'.App_Route::_getUrl('tModel').'/'.$cc->qrow['sname'].'.html';
    switch($cc->qrow['P1']) {
        case 1:
            $title="Шины {$bname} {$mname} {$msuffix}. Купить {$balt} {$malt}, цена, фото, доставка";
            $_title="Летняя шина {$bname} {$mname} {$msuffix}";
            $description="Цены на летние авто шины {$bname} {$mname}. Актуальное наличие, фото резины {$balt1} {$malt}";
            $keywords="{$bname} {$mname}, купить летние шины {$balt1} {$malt}";
            break;
        case 2:
            $title="Шины {$bname} {$mname} {$msuffix}. Купить {$balt} {$malt}, цена, фото, доставка";
            $_title="Зимняя шина {$bname} {$mname} {$msuffix}";
            $description="Цены на зимние авто шины {$bname} {$mname}. Актуальное наличие, фото резины {$balt1} {$malt}";
            $keywords="{$bname} {$mname}, купить зимние шины {$balt1} {$malt}";
            break;
        case 3:
            $title="Шины {$bname} {$mname} {$msuffix}. Купить {$balt} {$malt}, цена, фото, доставка";
            $_title="Всесезонная шина {$bname} {$mname} {$msuffix}";
            $description="Цены на всесезонные авто шины {$bname} {$mname}. Актуальное наличие, фото резины {$balt1} {$malt}";
            $keywords="{$bname} {$mname}, купить всесезонные шины {$balt1} {$malt}";
            break;
        default:
            $title="Шины {$bname} {$mname} {$msuffix}. Купить {$balt} {$malt}, цена, фото, доставка";
            $_title="{$bname} {$mname} {$msuffix}";
            $description="Цены на шины {$bname} {$mname}. Актуальное наличие, фото резины {$balt1} {$malt}";
            $keywords="{$bname} {$mname}, купить шины {$balt1} {$malt}";
            break;
    }
}
$REQUEST_URI = $_SERVER['REQUEST_URI'];
$_SERVER['REQUEST_URI'] = $uri_replace;
$pages = new App_Pages();
$kd = $pages->kd(array(1, 2)); // (1,2)  - приоритет имеет KD из верхнего влока
if (!empty($kd['description'])) $description = $kd['description'];
if (!empty($kd['keywords'])) $keywords = $kd['keywords'];
$title1 = $pages->title(2); // верхний блок
$title2 = $pages->title(1); // нижний блок
if ($title1 != '') $title = $title1; elseif ($title2 != '') $title = $title2; // тайтл из верхнего блока имеет больший приоритет.
$title = Tools::stripTags($title);
$_SERVER['REQUEST_URI'] = $REQUEST_URI;
?>
<table>
    <tr>
        <td style="width: 100px">SEO H1</td>
        <td colspan="4"><input name="seo_h1" type="text" size="100" value="<?=Tools::html($cc->qrow['seo_h1'] ? trim($cc->qrow['seo_h1']) : trim($_title))?>" /></td>
    </tr>
    <tr>
        <td style="width: 100px">SEO Title</td>
        <td colspan="4"><input name="seo_title" type="text" size="100" value="<?=Tools::html($cc->qrow['seo_title'] ? trim($cc->qrow['seo_title']) : trim($title))?>" /></td>
    </tr>
    <tr>
        <td style="width: 100px">SEO Keywords</td>
        <td colspan="4"><input name="seo_keywords" type="text" size="100" value="<?=Tools::html($cc->qrow['seo_keywords'] ? trim($cc->qrow['seo_keywords']) : trim($keywords))?>" /></td>
    </tr>
    <tr>
        <td style="width: 100px">SEO Description</td>
        <td colspan="4">
            <textarea class="TM" name="seo_description" style="width:100%; height:100px"><?=Tools::taria(@$cc->qrow['seo_description'] ? trim($cc->qrow['seo_description']) : trim($description))?></textarea>
        </td>
    </tr>
    <tr>
        <td>
            SEO оптимизация
        </td>
        <td colspan="4">
            <label><input type="radio" name="is_seo" value="0" <?=empty($cc->qrow['is_seo']) ? 'checked="checked"' : ''?>>Нет</label>
            <label><input type="radio" name="is_seo" value="1" <?=!empty($cc->qrow['is_seo']) ? 'checked="checked"' : ''?>>Да</label>
        </td>
    </tr>
</table>

<!-- meta end-->