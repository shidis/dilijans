<?
if (!defined('true_enter')) die ("Direct access not allowed!");

class CP extends DB
{

    public $frm=array(
        'name'=>'',
        'title'=>''
    );

    public $lastOpName='';

    function getMenuList($gr='', $notH=false, $roleId=0)
    {
        $gr=Tools::esc($gr);
        $notH=intval($notH);
        $roleId=$roleId?CU::$roleId:0;
        /*
        если CMS_LEVEL_ACCESS >=50 то перечисление доступных пунктов меню в поле class_exist
        если CMS_LEVEL_ACCESS <50 то в class_exist список запрещенных пунктов меню
        */
        $res=$this->query("SELECT * FROM cp_menu WHERE 1=1"
            .($gr!=''?" AND gr LIKE '$gr' ":'')
            .($notH?" AND NOT H":'')
            .($roleId>=50?" AND (class_exists LIKE '%,$roleId,%' OR class_exists LIKE '%,$roleId' OR class_exists LIKE '$roleId,%' OR class_exists LIKE '$roleId')":'')
            .($roleId<50?" AND (class_exists NOT LIKE '%,$roleId,%' AND class_exists NOT LIKE '%,$roleId' AND class_exists NOT LIKE '$roleId,%' AND class_exists NOT LIKE '$roleId')":'')
            ." ORDER BY pos"
        );

    }
	

	function setFN($name)
	{
		$this->frm['name']=$name;
	}
	
	function isAllow($name)
	{
		$this->lastOpName=$name;

        if(CU::$roleId<50) return true;
		
		return false;
	}
	
	function d()
	{
		return "Операция [{$this->lastOpName}] не разрешена";
	}


	function checkPermissions()
	{

		if(CU::$roleId<50) return true;

		die('[CP.checkPermissions]: Доступ запрещен');
	}

}