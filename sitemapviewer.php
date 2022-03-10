<?php
require_once('./mysitemapgenerator-lib/config.php');

Header('Content-Type:text/xml; charset=UTF-8');
        
if(isset($_GET['f']) && file_exists(_CACHEDIR.'/'.$_GET['f']))
    exit( file_get_contents(_CACHEDIR.'/'.$_GET['f']) );

if(is_dir(_CACHEDIR))
{
    $Buff=glob(_CACHEDIR.'/*.xml');
    
    if(isset($Buff))
    {
        if(sizeof($Buff)==1)
            exit(file_get_contents($Buff[0]));
        else
        {
            $raw='<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
            
            foreach($Buff AS $filename)
            {
                if(file_exists($filename) && filesize($filename) > 0) {
                    $raw .= '
<sitemap>
    <loc>http://' . $_SERVER['HTTP_HOST'] . '/sitemapviewer.php?f=' . basename($filename) . '</loc>
    <lastmod>' . date('Y-m-d', filemtime($filename)) . '</lastmod>
</sitemap>';
                }
            }
            
            $raw.='
</sitemapindex>
';
            
            exit($raw);
        }
    }
}
else
{
    exit('Error');
}
?>