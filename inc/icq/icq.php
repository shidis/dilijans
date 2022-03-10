<?php
//http://status.icq.com/online.gif?icq=318931962&img=5
function geticq($uin)
{
$fp = fsockopen("status.icq.com", 80, $errno, $errstr, 10);
if (!$fp) {
   echo "$errstr ($errno)<br />\n";
} else {
   $out = "GET /online.gif?icq=".$uin." HTTP/1.1\r\n";
   $out .= "Host: online.icq.com\r\n";
   $out .= "Connection: Close\r\n\r\n";

   fwrite($fp, $out);
   $s='';
   while (!feof($fp)) {
       $s.=fgets($fp, 128);
   }
   fclose($fp);
}
   return $s;
}

echo $s=geticq(@$_SERVER['argv'][0]);
if(strpos($s,'online0')!==false) $f='http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI']).'/hi.gif'; 
else $f='http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI']).'/icq.gif'; 
echo $f;

?> 