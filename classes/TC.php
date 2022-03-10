<?
if (!defined('true_enter')) die ("Direct access not allowed!");

class TC extends DB
{

function __construct()
{
	parent::__construct();
}

function que($qname,$cond1='',$cond2='',$cond3='',$cond4='')
{
	switch ($qname)  
	{
		case 'co_list':
			$res=$this->query("SELECT * FROM tc_company ORDER BY name");
			break;
		case 'co_by_city':
			$cond1=intval($cond1);
			$res=$this->query("SELECT tc_company.name,tc_company.site FROM (tc_company RIGHT JOIN tc_city_rel ON tc_city_rel.company_id=tc_company.company_id) RIGHT JOIN tc_city ON tc_city.city_id=tc_city_rel.city_id WHERE tc_city.city_id='$cond1' ORDER BY name");
			break;
		case 'co_by_city_name':
			$cond1=Tools::like($cond1);
			$res=$this->query("SELECT tc_company.name,tc_company.site FROM (tc_company RIGHT JOIN tc_city_rel ON tc_city_rel.company_id=tc_company.company_id) RIGHT JOIN tc_city ON tc_city.city_id=tc_city_rel.city_id WHERE tc_city.name LIKE '$cond1' ORDER BY name");
			break;
		case 'city_list':
			$res=$this->query("SELECT * FROM tc_city ORDER BY name");
			break;
		case 'city_by_id':
			$cond1=intval($cond1);
			$res=$this->query("SELECT * FROM tc_city WHERE city_id='$cond1'");
			break;
		case 'city_by_sname':
			$cond1=Tools::like($cond1);
			$res=$this->query("SELECT * FROM tc_city WHERE sname LIKE '$cond1'");
			break;
	}
	return($res);
}

function cities($hq=0)
{
	$hq=(int)$hq;
	$this->query("SELECT * FROM tc_city WHERE hit_quant>=$hq ORDER BY name");
	$r=array();
	if($this->qnum())
		while($this->next()!==false){
			$r[$this->qrow['city_id']]=array(
				'name'=>Tools::unesc($this->qrow['name']),
				'hit_quant'=>$this->qrow['hit_quant'],
				'sname'=>$this->qrow['sname'],
				'nameGde'=>Tools::unesc($this->qrow['nameGde']),
				'nameKuda'=>Tools::unesc($this->qrow['nameKuda'])
			);
		}
	return $r;
}


}