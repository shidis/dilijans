<?
if (!defined('true_enter')) die ("Direct access not allowed!");

abstract class Common {

	public 	$fres=true,
			$fres_msg='';
			
	public function putMsg($fres,$msg='',$toArray=false){
		$this->fres=$fres;
		if($toArray && is_array($this->fres_msg) || !$toArray && is_array($this->fres_msg) && $msg!='') $this->fres_msg[]=$msg;
			elseif($toArray && !is_array($this->fres_msg) && $this->fres_msg!='' && $msg!='') $this->fres_msg=array($this->fres_msg,$msg);
				else $this->fres_msg=$msg;

        Msg::put($fres,$msg);
		return $fres;
	}
	
	public function strMsg($glue='<br>'){
		return is_array($this->fres_msg)?implode($glue,$this->fres_msg):$this->fres_msg;
	}

}
	