<?
include_once ('ajx_loader.php');

$cp->setFN('oshed');
$cp->checkPermissions();

//sleep(2);


$os=new App_Orders();

$r->fres=true;
$r->fres_msg='';

$act=Tools::esc(@$_REQUEST['act']);

switch ($act){

    default: $r->fres=false; $r->fres_msg='BAD ACT ID '.$act; break;
}

ajxEnd();