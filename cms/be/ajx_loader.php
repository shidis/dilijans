<?


if(@$_REQUEST['mode']=='upload') $upload_mode=1; else $upload_mode=0;

//error_reporting(E_ALL);
//ini_set('log_errors', true);


require_once dirname(__FILE__).'/../auth.php';

if (!defined('true_enter')) die ("Direct access not allowed!");

if(false && !$upload_mode && @$_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') die('HALT: NOT XMLHttpRequest');


if(!$upload_mode){
	ob_start();
}

$r=(object)array();

$r->fres=true;
$r->fres_msg='';

require_once ($_SERVER['DOCUMENT_ROOT'].'/config/init.php');

require_once($_SERVER['DOCUMENT_ROOT'].'/cms/inc/global.php');

$json = new JSON();

function ajxEnd(){
	
	global $upload_mode,$r;
	
	if(!$upload_mode) {
		//	header('Content-Type: text/html; charset=utf-8');
		$buf=ob_get_contents();
		ob_end_clean();

        if(is_array($r)){
            $r = (object)$r;
        }

		if(@$r->textOutput) {
			if(@$r->prependFresMsg) echo $r->fres_msg;
			echo $buf;
		} elseif($buf!='') {
				$r->fres=false;
				$r->fres_msg=$buf;
				echo json_encode($r);
		} else echo json_encode($r);
		
	}
}
