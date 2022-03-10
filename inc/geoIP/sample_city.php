<?php

if(empty($_GET['ip'])) die('_get[ip] need');

error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);


// This code demonstrates how to lookup the country, region, city,
// postal code, latitude, and longitude by IP Address.
// It is designed to work with GeoIP/GeoLite City

// Note that you must download the New Format of GeoIP City (GEO-133).
// The old format (GEO-132) will not work.

include("geoipcity.inc");
include("geoipregionvars.php");

// uncomment for Shared Memory support
// geoip_load_shared_mem("/usr/local/share/GeoIP/GeoIPCity.dat");
// $gi = geoip_open("/usr/local/share/GeoIP/GeoIPCity.dat",GEOIP_SHARED_MEMORY);

$gi = geoip_open("data/GeoLiteCity.dat",GEOIP_STANDARD);

$record = geoip_record_by_addr($gi,@$_GET['ip']);
print "<b>country_code |  country_name = </b>" . $record->country_code . " | " . $record->country_code3 . " | " . $record->country_name . "<br>";
print "<b>region | GEOIP_REGION_NAME[country_code][region] = </b>" . $record->region . " | " . $GEOIP_REGION_NAME[$record->country_code][$record->region] . "<br>";
print "<b>city = </b>" . $record->city . "<br>";
print "<b>postal_code = </b>" . $record->postal_code . "<br>";
print "<b>latitude = </b>" . $record->latitude . "<br>";
print "<b>longitude = </b>" . $record->longitude . "<br>";
print "<b>metro_code = </b>" . $record->metro_code . "<br>";
print "<b>area_code = </b>" . $record->area_code . "<br>";
print "<b>continent_code = </b>" . $record->continent_code . "<br>";

geoip_close($gi);

?>
