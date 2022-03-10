<?php
error_reporting(0);
$radius = 6;

/**
 * Чем выше rate, тем лучше качество сглаживания и больше время обработки и
 * потребление памяти.
 *  
 * Оптимальный rate подбирается в зависимости от радиуса.
 */ 
$rate = 2;
$img=@$_GET['i'];
if(strpos($img,'http://')!==false){
	$img=parse_url($img);
	$img=$img['path'];
}
$img=$_SERVER['DOCUMENT_ROOT'].$img;
if(!@is_file($img)) return '';

$img = imagecreatefromstring(file_get_contents($img));
imagealphablending($img, false);
imagesavealpha($img, true);

$width = imagesx($img);
$height = imagesy($img);

$rs_radius = $radius * $rate;
$rs_size = $rs_radius * 2;

$corner = imagecreatetruecolor($rs_size, $rs_size);
imagealphablending($corner, false);

$trans = imagecolorallocatealpha($corner, 255, 255, 255, 127);
imagefill($corner, 0, 0, $trans);

$positions = array(
    array(0, 0, 0, 0),
    array($rs_radius, 0, $width - $radius, 0),
    array($rs_radius, $rs_radius, $width - $radius, $height - $radius),
    array(0, $rs_radius, 0, $height - $radius),
);

foreach ($positions as $pos) {
    imagecopyresampled($corner, $img, $pos[0], $pos[1], $pos[2], $pos[3], $rs_radius, $rs_radius, $radius, $radius);
}

$lx = $ly = 0;
$i = -$rs_radius;
$y2 = -$i;
$r_2 = $rs_radius * $rs_radius;

for (; $i <= $y2; $i++) {

    $y = $i;
    $x = sqrt($r_2 - $y * $y);

    $y += $rs_radius;
    $x += $rs_radius;

    imageline($corner, $x, $y, $rs_size, $y, $trans);
    imageline($corner, 0, $y, $rs_size - $x, $y, $trans);

    $lx = $x;
    $ly = $y;
}

foreach ($positions as $i => $pos) {
    imagecopyresampled($img, $corner, $pos[2], $pos[3], $pos[0], $pos[1], $radius, $radius, $rs_radius, $rs_radius);
}

header('Content-Type: image/png');
imagepng($img);
