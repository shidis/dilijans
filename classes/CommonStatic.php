<?
if (!defined('true_enter')) die ("Direct access not allowed!");

abstract class CommonStatic {

	public static 	
		$fres=true,
		$fres_msg='';
			
	public static function putMsg($fres,$msg='',$toArray=false){
		static::$fres=$fres;
		if($toArray && is_array(static::$fres_msg) || !$toArray && is_array(static::$fres_msg) && $msg!='') static::$fres_msg[]=$msg;
			elseif($toArray && !is_array(static::$fres_msg) && static::$fres_msg!='' && $msg!='') static::$fres_msg=array(static::$fres_msg,$msg);
				else static::$fres_msg=$msg;

        Msg::put($fres,$msg);
		return $fres;
	}
	
	public static function strMsg($glue='<br>'){
		return is_array(static::$fres_msg)?implode($glue,static::$fres_msg):static::$fres_msg;
	}

}
	