<?

class CC_DOP extends DB
{
		
function __construct()
{
	parent::__construct();
}

function que($qname,$cond1='',$cond2='',$cond3='',$cond4='')
{
	switch ($qname)  
	{
		case 'dop_list':
			$cond1=intval($cond1);
			$res=$this->query("SELECT cc_dop.*, cc_brand.name AS bname FROM cc_dop INNER JOIN cc_brand ON cc_dop.brand_id = cc_brand.brand_id WHERE (cc_dop.brand_id='$cond1')AND(NOT cc_dop.LD) ORDER BY cc_dop.name");
			break;
		case 'dop_by_id':
			$cond1=intval($cond1);
			$res=$this->query("SELECT cc_dop.*, cc_brand.name AS bname FROM cc_dop INNER JOIN cc_brand ON cc_dop.brand_id = cc_brand.brand_id WHERE (cc_dop.dop_id='$cond1')");
			break;
		default: echo 'CC_DOP BAD CASE '.$qname; $res=false;
	}
	return($res);
}

function related_cat($cat_id)
{
	$this->query("SELECT cc_dop.* FROM cc_cat INNER JOIN (cc_dop INNER JOIN cc_model ON cc_dop.brand_id = cc_model.brand_id) ON cc_cat.model_id = cc_model.model_id WHERE (cc_cat.cat_id='$cat_id')AND(NOT cc_dop.LD)");
	$r=array();
	if($this->qnum()) $r=$this->fetchAll('',MYSQL_ASSOC);
	return $r;
}


}?>