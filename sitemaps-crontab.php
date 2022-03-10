<?php
/* ѕуть к файлу config.php */
require_once('./mysitemapgenerator-lib/config.php');
/* ѕуть к файлу mysitemapgenerator.lib.php */
require_once('./mysitemapgenerator-lib/mysitemapgenerator.lib.php');

$MySitemapGenerator=new MySitemapGenerator(_MYSITEMAPGENERATOR_API_KEY);
$MySitemapGenerator->returntype='array';

$Buff=$MySitemapGenerator->call('SitemapsGetFiles',Array('cid'=>_MYSITEMAPGENERATOR_SITE_CID));
    
if($Buff['result']!=='error')
{
        if(!file_exists(_CACHEDIR))
        {
            if(!mkdir(_CACHEDIR,077))
            {
                if(defined('_DEBUG') && _DEBUG) print '<p>Error: Cache Dir not exists!</p>';
            }
        }
        
        if(isset($Buff['files']) && sizeof($Buff['files']))
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, True);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, True);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                        
            foreach($Buff['files'] AS $i=>$file)
            {
                curl_setopt($ch, CURLOPT_URL, $file);
                $raw=curl_exec($ch);
                
                $filename=_CACHEDIR.'/sitemap_'.$i.'.xml';
                
                $fp =   fopen($filename, 'w');
                        fwrite($fp,$raw);
                        fclose($fp);
                        chmod($filename,0777);
                        
                unset($fp,$raw,$filename);
                
                if(defined('_DEBUG') && _DEBUG) print '<p>Ok: Download File: '._CACHEDIR.'/sitemap_'.$i.'.xml</p>';
            }
            
            curl_close($ch);
        }
        else
        {
            if(defined('_DEBUG') && _DEBUG) print '<p>Error: Files not found!</p>';
        }
}
else
{
    if(defined('_DEBUG') && _DEBUG) print '<p>Error: Connection error!</p>';
}
?>