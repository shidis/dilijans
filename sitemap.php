<?php
error_reporting(0);
/* Введите цифровой идентификатор Вашего сайта (вместо 12345) */
define('_USECHANNEL',745);
/* Файл для создания кэш-копии */
define('_CACHEFILE','./sitemap.xml');

Header('Content-type:text/xml; charset=utf-8');

if(!file_exists(_CACHEFILE))
{
    $fc=fopen(_CACHEFILE,'a+');fclose($fc);
    chmod(_CACHEFILE,0777) or die( 'Error! Lets you set File Permissions on '._CACHEFILE );
}

$fp=fsockopen('www.mysitemapgenerator.com',80,$errno,$errstr,10)
    or die( file_get_contents(_CACHEFILE) );
    fputs($fp,'GET /channels/'._USECHANNEL.'.xml HTTP/1.0'."\r\n".
          'Host: www.mysitemapgenerator.com'."\r\n".
          (filesize(_CACHEFILE) ? 'If-Modified-Since: '.gmdate('D, d M Y H:i:s',filemtime(_CACHEFILE))."\r\n" : Null).
          'Connection: Close'."\r\n\r\n");    
    
    for($Buffer=Null;!feof($fp);)
    {
        $Buffer.=fgets($fp);
    }    
    fclose($fp);
    
if(!$Buffer || stristr($Buffer,'304 Not Modified'))
    die( file_get_contents(_CACHEFILE) );

$Buffer=trim(substr($Buffer,strpos($Buffer,"\r\n\r\n")));
/* The patch is only for special cases */
$Buffer=preg_replace(Array('/^[^<]+</','/>[^>]+$/'),Array('<','>'),$Buffer);
        
if(!$Buffer || !stristr($Buffer,'urlset') || stristr($Buffer,'!--Error'))
    die( file_get_contents(_CACHEFILE) );
else
{
    $fc=fopen(_CACHEFILE,'w');
    if(!flock($fc, LOCK_EX | LOCK_NB))
    {	header('HTTP/1.0 503 Service Temporarily Unavailable'); die();	}
    
        fwrite($fc,$Buffer);
        flock($fc, LOCK_UN);
        fclose($fc);
    die( $Buffer );
}
?>