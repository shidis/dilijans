<?
include_once ('ajx_loader.php');

//sleep(1);

$cp->setFN('cgal');
$cp->checkPermissions();

$r->fres=true;
$r->fres_msg='';

$act=Tools::esc($_REQUEST['act']);

switch ($act){


    default: $r->fres=false; $r->fres_msg='BAD ACT ID '.$act;
}

ajxEnd();
