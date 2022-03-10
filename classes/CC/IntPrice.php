<?

// TODO избавится от этого класса

class CC_IntPrice {

	
	function modelUpdate($model_id)
	{
		if(!Cfg::get('int_price_F1F2')) return true;
		if(!isset(App_TFields::$fields['cc_model']['F1']) || !isset(App_TFields::$fields['cc_model']['F2'])) return false;
		if(empty($model_id)) return false;
		$cc=new CC_Base;
		$cc->que('cat_by_model',$model_id,!(bool)Cfg::get('int_price_F1F2_includeHide'));
		if(Data::get('td_discount_intPrice')){
			$td=floatval(Data::get('t_discount'));
			$dd=floatval(Data::get('d_discount'));
		}else $td=$dd=0;
		$f1=999999999; 
		$f2=0; 
		if($cc->qnum()) while($cc->next()!==false){
			$p=$cc->qrow['cprice'];
			if($cc->qrow['gr']==1) $p=round($p-$p*$td/100);
			if($cc->qrow['gr']==2) $p=round($p-$p*$dd/100);
			if($f1>$p && $p)  $f1=$p;
			if($f2<$p)  $f2=$p;
		}
		if($f1==999999999) $f1=0;
		$cc->query("UPDATE cc_model SET F1='$f1', F2='$f2' WHERE model_id='$model_id'");
		unset($cc);
		return true;
	}

}