<?
if (PHP_SAPI == 'cli')
    die('This example should only be run from a Web Browser');



require_once dirname(__FILE__).'/../auth.php';

ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

$gr=@$_REQUEST['gr'];
if(empty($gr)) $gr=1;

if($gr==1) $fn="YndexMarket выгрузка шины.csv"; else $fn="YndexMarket выгрузка диски.csv";

function row($a)
{
    echo Tools::cp1251(implode(';',$a))."\n";
}

$mk=new CC_API_Market();

header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=\"$fn\"");
header("Pragma: no-cache");
header("Expires: 0");

if ($gr==1) {


    // ШИНЫ

    row([
        'Артикул размера',
        'Артикул модели',
        'Сезон',
        'Шип',
        'YM_Название',
        'Бренд',
        'Модель',
        'Суффикс',
        'ZR',
        'С',
        'Ширина',
        'Профиль',
        'Диаметр',
        'ИнИс',
        'Цена1 мин',
        'Цена1 макс',
        'Цена2 мин',
        'Цена2 макс',
        'Цена3 мин',
        'Цена3 макс',
        'Склад',
        'Закупка',
        'Розница',
        'Маржа на сайте',
        'Маржа по YM',
        'Сред. откл. розн.',
        'YM_avg',
        'YM_min',
        'YM_max',
        'YM_онлайн',
        'YM_оффлайн',
        'YM_ДатаПроверки',
        'YM_НетНаМаркете',
        'YM_modelId',
        'YM_inQueue'
    ]);

    $mk->query("SELECT
    cc.cat_id,
  cm.model_id,
  cm.P1 AS sezon,
  cm.P2 AS ship,
  cb.name AS bname,
  cm.name AS mname,
  cc.suffix AS csuffix,
  cc.P6+'' AS ZR,
  cc.P4 AS C,
  cc.P3 + '' AS width,
  cc.P2 + '' AS profile,
  cc.P1 + '' AS diametr,
  cc.P7 AS inis,
  yc.name AS YM_name,
  (SELECT MIN(ccs_min1.price1) FROM cc_cat_sc ccs_min1 WHERE ccs_min1.cat_id=cc.cat_id AND ccs_min1.price1!=0 AND ccs_min1.sc!=0) AS scMin1,
  (SELECT MAX(ccs_max1.price1) FROM cc_cat_sc ccs_max1 WHERE ccs_max1.cat_id=cc.cat_id) AS scMax1,
  (SELECT MIN(ccs_min2.price2) FROM cc_cat_sc ccs_min2 WHERE ccs_min2.cat_id=cc.cat_id AND ccs_min2.price2!=0 AND ccs_min2.sc!=0) AS scMin2,
  (SELECT MAX(ccs_max2.price2) FROM cc_cat_sc ccs_max2 WHERE ccs_max2.cat_id=cc.cat_id) AS scMax2,
  (SELECT MIN(ccs_min3.price3) FROM cc_cat_sc ccs_min3 WHERE ccs_min3.cat_id=cc.cat_id AND ccs_min3.price3!=0 AND ccs_min3.sc!=0) AS scMin3,
  (SELECT MAX(ccs_max3.price3) FROM cc_cat_sc ccs_max3 WHERE ccs_max3.cat_id=cc.cat_id) AS scMax3,
  cc.bprice AS bprice,
  cc.cprice AS cprice,
  cc.sc,
  yc.min AS YM_min,
  yc.max AS YM_max,
  yc.avg AS YM_avg,
  yc.onlineOffers AS YM_onlineOffers,
  yc.offlineOffers AS YM_offlineOffers,
  DATE(yc.dtCheck) AS YM_LastCheckDate,
  yc.NE AS YM_NotInYML,
  yc.E AS YM_Error,
  yc.modelId AS YM_modelId,
  yc.dtCheck AS YM_dtCheck,
  yc.dtAdd AS YM_dtAdd,
  yc.inQueue
  FROM ym_cat yc
  JOIN cc_cat cc USING (cat_id)
  JOIN cc_model cm USING (model_id)
  JOIN cc_brand cb USING (brand_id)
WHERE cc.gr = 1
AND yc.modelId != 0
AND yc.E = '0'
AND yc.avg != 0
ORDER BY cb.name, cm.name, cc.P1, cc.P3, cc.P2");

    while($mk->next()!==false){

        $qr=(object)$mk->qrow;

        $sezon='';
        if($qr->sezon==1) $sezon='лето';
        elseif($qr->sezon==2) $sezon='зима';
        elseif($qr->sezon==3) $sezon='всесез';

        row([
            $qr->cat_id,
            $qr->model_id,
            $sezon,
            $qr->ship?'шип':'',
            Tools::unesc($qr->YM_name),
            Tools::unesc($qr->bname),
            Tools::unesc($qr->mname),
            $qr->csuffix,
            $qr->ZR?'ZR':'',
            $qr->C?'C':'',
            Tools::n($qr->width),
            Tools::n($qr->profile),
            Tools::n($qr->diametr),
            $qr->inis,
            (int)$qr->scMin1,
            (int)$qr->scMax1,
            (int)$qr->scMin2,
            (int)$qr->scMax2,
            (int)$qr->scMin3,
            (int)$qr->scMax3,
            (int)$qr->sc,
            (int)$qr->bprice,
            (int)$qr->cprice,
            (int)($qr->cprice - $qr->bprice),
            (int)($qr->YM_avg - $qr->bprice),
            $qr->YM_avg?(Tools::n(  ceil(((($qr->YM_avg-$qr->cprice)/$qr->YM_avg*10000) /100))  ).'%'):'',
            (int)$qr->YM_avg,
            (int)$qr->YM_min,
            (int)$qr->YM_max,
            (int)$qr->YM_onlineOffers,
            (int)$qr->YM_offlineOffers,
            $qr->YM_LastCheckDate,
            $qr->YM_NotInYML,
            $qr->YM_modelId,
            $qr->inQueue
        ]);

    }

}else{


    // ДИСКИ

    row([
        'Артикул размера',
        'Артикул модели',
        'YM_Название',
        'Бренд',
        'Модель',
        'Цвет',
        'Ширина',
        'Диаметр',
        'Сверловка',
        'Вылет',
        'DIA',
        'Цена1 мин',
        'Цена1 макс',
        'Цена2 мин',
        'Цена2 макс',
        'Цена3 мин',
        'Цена3 макс',
        'Склад',
        'Закупка',
        'Розница',
        'Маржа на сайте',
        'Маржа по YM',
        'Сред. откл. розн.',
        'YM_avg',
        'YM_min',
        'YM_max',
        'YM_онлайн',
        'YM_оффлайн',
        'YM_ДатаПроверки',
        'YM_НетНаМаркете',
        'YM_modelId',
        'YM_inQueue'
    ]);

    $mk->query("SELECT
 cc.cat_id,
  cm.model_id,
  yc.name,
  cb.name AS bname,
  cm.name AS mname,
  cc.suffix AS csuffix,
  cc.P2+'' AS J,
  cc.P5+'' AS diametr,
  cc.P4 + '' AS PCD,
  cc.P6 + '' AS DCO,
  cc.P1 + '' AS ET,
  cc.P3 + '' AS DIA,
  yc.name AS YM_name,
  (SELECT MIN(ccs_min1.price1) FROM cc_cat_sc ccs_min1 WHERE ccs_min1.cat_id=cc.cat_id AND ccs_min1.price1!=0 AND ccs_min1.sc!=0) AS scMin1,
  (SELECT MAX(ccs_max1.price1) FROM cc_cat_sc ccs_max1 WHERE ccs_max1.cat_id=cc.cat_id) AS scMax1,
  (SELECT MIN(ccs_min2.price2) FROM cc_cat_sc ccs_min2 WHERE ccs_min2.cat_id=cc.cat_id AND ccs_min2.price2!=0 AND ccs_min2.sc!=0) AS scMin2,
  (SELECT MAX(ccs_max2.price2) FROM cc_cat_sc ccs_max2 WHERE ccs_max2.cat_id=cc.cat_id) AS scMax2,
  (SELECT MIN(ccs_min3.price3) FROM cc_cat_sc ccs_min3 WHERE ccs_min3.cat_id=cc.cat_id AND ccs_min3.price3!=0 AND ccs_min3.sc!=0) AS scMin3,
  (SELECT MAX(ccs_max3.price3) FROM cc_cat_sc ccs_max3 WHERE ccs_max3.cat_id=cc.cat_id) AS scMax3,
  cc.bprice AS bprice,
  cc.cprice AS cprice,
  cc.sc,
  yc.min AS YM_min,
  yc.max AS YM_max,
  yc.avg AS YM_avg,
  yc.onlineOffers AS YM_onlineOffers,
  yc.offlineOffers AS YM_offlineOffers,
  DATE(yc.dtCheck) AS YM_LastCheckDate,
  yc.NE AS YM_NotInYML,
  yc.E AS YM_Error,
  yc.modelId AS YM_modelId,
  yc.dtCheck AS YM_dtCheck,
  yc.dtAdd AS YM_dtAdd,
  yc.inQueue
FROM ym_cat yc
  JOIN cc_cat cc USING (cat_id)
  JOIN cc_model cm USING (model_id)
  JOIN cc_brand cb USING (brand_id)
WHERE cc.gr = 2
AND yc.modelId != 0
AND yc.E = '0'
AND yc.avg != 0
ORDER BY cb.name, cm.name, cc.P5, cc.P4, cc.P6");

    while($mk->next()!==false){

        $qr=(object)$mk->qrow;

        row([
            $qr->cat_id,
            $qr->model_id,
            Tools::unesc($qr->YM_name),
            Tools::unesc($qr->bname),
            Tools::unesc($qr->mname),
            $qr->csuffix,
            Tools::n($qr->J),
            Tools::n($qr->diametr),
            Tools::n($qr->PCD).'x'.Tools::n($qr->DCO),
            $qr->ET,
            $qr->DIA,
            (int)$qr->scMin1,
            (int)$qr->scMax1,
            (int)$qr->scMin2,
            (int)$qr->scMax2,
            (int)$qr->scMin3,
            (int)$qr->scMax3,
            $qr->sc,
            (int)$qr->bprice,
            (int)$qr->cprice,
            (int)($qr->cprice - $qr->bprice),
            (int)($qr->YM_avg - $qr->bprice),
            $qr->YM_avg?(Tools::n(  ceil(((($qr->YM_avg-$qr->cprice)/$qr->YM_avg*10000) /100))  ).'%'):'',
            (int)$qr->YM_avg,
            (int)$qr->YM_min,
            (int)$qr->YM_max,
            (int)$qr->YM_onlineOffers,
            (int)$qr->YM_offlineOffers,
            $qr->YM_LastCheckDate,
            $qr->YM_NotInYML,
            $qr->YM_modelId,
            $qr->inQueue
        ]);


    }


}

exit;

