<?php

if (!defined('true_enter')) die ("Direct access not allowed!");

require_once dirname(__FILE__).'/global.php';

$cc=new CC_Ctrl;
$cp=new App_CP;

function td_class($line,$addClass='')
{
	if ($line==1) return(" class=\"first $addClass\""); 
		elseif ($line/2==intval($line/2)) return("class=\"chet $addClass\""); else return("class=\"nechet $addClass\"");
}
function tr_class($line)
{
	if ($line==1) return('#EEEEEE'); 
		elseif ($line/2==intval($line/2)) return('#DDDDDD'); else return('#EEEEEE');
}


?>
<SCRIPT language=JavaScript src="/cms/js/main.js" type=text/javascript></SCRIPT>