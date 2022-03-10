<?

// TODO избавится от этого класса


class CC_ModelSC {
	
	public static function modelUpdate($model_id) {
		if(!Cfg::get('model_SC')) return true;
		if(!isset(App_TFields::$fields['cc_model']['sc'])) return false;
		if(empty($model_id)) return false;
		$cc=new CC_Base;
		$cc->que('cat_by_model',$model_id,!(bool)Cfg::get('model_SC_includeHide'));
		$sc=0;
		if($cc->qnum()) while($cc->next()!==false) $sc+=$cc->qrow['sc'];
		$cc->query("UPDATE cc_model SET sc='$sc' WHERE model_id='$model_id'");
		unset($cc);
		return true;
	}

}		
		
