<?
@define (true_enter,1);
require_once ($_SERVER['DOCUMENT_ROOT'].'/config/init.php');

require_once('../inc/utils.php');

$gr=@$_GET['gr'];
if($gr==0) $gr=1;

$cc->query("SELECT (SELECT SUM(sc) FROM cc_cat WHERE cc_cat.model_id=cc_model.model_id AND NOT cc_cat.LD AND NOT cc_cat.H) AS scSum, cc_model.hit_quant, cc_brand.name AS bname, cc_model.name, cc_model.P1 AS MP1, cc_model.P3 AS M3, cc_model.P2 AS M2, cc_brand.alt AS balt,cc_model.alt AS malt,cc_brand.sname AS brand_sname,cc_model.sname AS model_sname, cc_brand.replica FROM cc_brand INNER JOIN cc_model ON cc_brand.brand_id=cc_model.brand_id WHERE NOT cc_brand.LD AND NOT cc_model.LD AND NOT cc_brand.H AND NOT cc_model.H AND cc_model.text='' AND cc_brand.gr=1 ORDER BY cc_model.hit_quant DESC,cc_brand.name, cc_model.name");
?>
<h2>Модели шин для которых нужны тексты.</h2>
<p>Список обновляемый. По мере заполения описаний модели исчезают из списка. Также, добавляются новые. Модели отранжированы в порядке убывания популярности по статистике Яндекса</p>
<table cellpadding="7" border="1">
    <tr><th align="left">N</th><th align="left">Модель</th><th align="left">Сезон</th><th align="left">Частотность</th><th>На складе</th></tr>
    <? $i=0;
    while($cc->next()!==false){
        ?><tr><td><?=++$i?></td><?
        $ship='';
        switch($cc->qrow['MP1']){
            case 1:
                $url=App_SUrl::tModel(0,$cc->qrow);
                $sezon='летние';
                break;
            case 2:
                $url=App_SUrl::tModel(0,$cc->qrow);
                $sezon='зимние';
                if($cc->qrow['M3']) $ship='шип.';
                break;
            case 3:
                $url=App_SUrl::tModel(0,$cc->qrow);
                $sezon='всесезонные';
                break;
            default:
                $url=App_SUrl::tModel(0,$cc->qrow);
                $sezon='???';
                break;
        }
        ?><td><a href="<?=$url?>" target="_blank"><?=$cc->qrow['bname'].' '.$cc->qrow['name']?></a></td><?
        ?><td><?=$sezon?> <?=@$ship?></td><?
        ?><td><?=$cc->qrow['hit_quant']?></td><?
        ?><td><?=$cc->qrow['scSum']?></td><?
        ?>
        </tr><?
    }
    ?></table>