<?
echo '<?xml version="1.0" encoding="windows-1251"?>';
echo '
<!DOCTYPE yml_catalog SYSTEM "shops.dtd">';
?>

<yml_catalog date="2010-04-01 17:00">
    <shop>
        <name>Magazin</name>
        <company>Magazin</company>
        <url>http://www.magazin.ru/</url>
        <currencies>
            <currency id="RUR" rate="1" plus="0"/>
        </currencies>
        <categories>
            <category id="1">Категория</category>
        </categories>
        <offers>
            <?
            if (!empty($items)){
                foreach ($items as $item){
                    ?>
                    <offer id="12341" type="vendor.model" bid="13" cbid="20" available="true">
                        <url>http://magazin.ru/product_page.asp?pid=14344</url>
                        <price>15000</price>
                        <currencyId>RUR</currencyId>
                        <categoryId type="Own">101</categoryId>
                        <picture>http://magazin.ru/img/device14344.jpg</picture>
                        <delivery>true</delivery>
                        <local_delivery_cost>300</local_delivery_cost>
                        <typePrefix>Принтер</typePrefix>
                        <vendor>НP</vendor>
                        <vendorCode>Q7533A</vendorCode>
                        <model>Color LaserJet 3000</model>
                        <description>
                            A4, 64Mb, 600x600 dpi, USB 2.0, 29стр/мин ч/б / 15стр/мин цв, лотки на 100л и 250л, плотность до 175г/м, до 60000 стр/месяц
                        </description>
                        <manufacturer_warranty>true</manufacturer_warranty>
                        <country_of_origin>Япония</country_of_origin>
                    </offer>
                </offers>
                <?
                }
            }
        ?>
    </shop>
</yml_catalog>