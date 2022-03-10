<?
include_once ('ajx_loader.php');

//sleep(2);

$cp->setFN('tags');
$cp->checkPermissions();

$r->fres=true;
$r->fres_msg='';

$act=Tools::esc(@$_REQUEST['name']);
$userId=(int)@$_REQUEST['pk'];

switch ($act){

    case 'role':
        $r->fres=CU::modUser($userId, array('roleId'=>@$_REQUEST['value']));
        $r->fres_msg=CU::strMsg();
        break;

    case 'login':
        $r->fres=CU::modUser($userId, array('login'=>@$_REQUEST['value']));
        $r->fres_msg=CU::strMsg();
        break;

    case 'firstName':
        $r->fres=CU::modUser($userId, array('firstName'=>@$_REQUEST['value']));
        $r->fres_msg=CU::strMsg();
        break;

    case 'lastName':
        $r->fres=CU::modUser($userId, array('lastName'=>@$_REQUEST['value']));
        $r->fres_msg=CU::strMsg();
        break;

    case 'cmsStartUrl':
        $r->fres=CU::modUser($userId, array('cmsStartUrl'=>@$_REQUEST['value']));
        $r->fres_msg=CU::strMsg();
        break;

    case 'email':
        $r->fres=CU::modUser($userId, array('email'=>@$_REQUEST['value']));
        $r->fres_msg=CU::strMsg();
        break;

    case 'skype':
        $r->fres=CU::modUser($userId, array('skype'=>@$_REQUEST['value']));
        $r->fres_msg=CU::strMsg();
        break;

    case 'icq':
        $r->fres=CU::modUser($userId, array('icq'=>@$_REQUEST['value']));
        $r->fres_msg=CU::strMsg();
        break;

    case 'lifeTime':
        $r->fres=CU::modUser($userId, array('lifeTime'=>@$_REQUEST['value']));
        $r->fres_msg=CU::strMsg();
        break;

    case 'pw':
        if(!CU::modUser(@$_REQUEST['userId'], array('pw'=>@$_REQUEST['q']))){
            $r->fres=false;
            $r->fres_msg=CU::strMsg();
        }
        break;

    case 'resetToken':
        if(!CU::resetToken(@$_REQUEST['userId'])){
            $r->fres=false;
            $r->fres_msg=CU::strMsg();
        }
        break;

    case 'disabledSwitch':
        $r->fres=CU::accessSwitch(@$_REQUEST['userId']);
        if($r->fres===false){
            $r->fres=false;
            $r->fres_msg=CU::strMsg();
        }
        break;

    case 'logoutAll':
        if(!CU::logoutAllSessionsByUser(@$_REQUEST['userId'])){
            $r->fres=false;
            $r->fres_msg=CU::strMsg();
        }
        break;

    case 'os':
        $r->fres_msg=CU::modUser($userId, array('os'=>@$_REQUEST['value']));
        $r->fres_msg=CU::strMsg();
        break;

    case 'userAdd':
        $frm=Tools::parseStr(@$_REQUEST['frm']);
        if(!CU::addUser($frm)){
            $r->fres=false;
            $r->fres_msg=CU::strMsg();
        }
        break;

    case 'driverAdd':
        $frm=Tools::parseStr(@$_REQUEST['frm']);
        $frm['roleId']=100;
        $frm['login']=Tools::randString(7);
        $frm['pw']=Tools::randString(32);
        if(!CU::addUser($frm)){
            $r->fres=false;
            $r->fres_msg=CU::strMsg();
        }
        break;

    case 'userDel':
        if(!CU::deleteUser(@$_REQUEST['userId'])){
            $r->fres=false;
            $r->fres_msg=CU::strMsg();
        }
        break;

    default: $r->fres=false; $r->fres_msg='BAD ACT_CASE '.$act;
}

ajxEnd();
