<?

// TODO избавится от этого класса

class CC_RDisk {

	function modelUpdate($model_id)
	{
		if(!Cfg::get('RDisk_S1')) return true;
		if(!isset(App_TFields::$fields['cc_model']['S1'])) return false;
		if(empty($model_id)) return false;
		$cc=new CC_Base;
		$cc->que('cat_by_model',$model_id,!(bool)Cfg::get('RDisk_S1_includeHide'));
		$s1='';
		$r1=array();
		if($cc->qnum()) while($cc->next()!==false){
			$r1[$cc->qrow['P5']]=$cc->qrow['P5'];
		}
		sort($r1);
		$s1=Tools::esc(implode(' ',$r1));
		$cc->query("UPDATE cc_model SET S1='$s1' WHERE model_id='$model_id'");
		unset($cc);
		return true;
	}

}