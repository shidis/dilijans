<?

class Entry extends SSCommon {
	
	function __construct()
	{
		parent::__construct();
	}

	function que($qname,$cond1='',$cond2='',$cond3='',$cond4='')
	{
		switch ($qname)  
		{

			case 'entry_list_by_section_id':
				$res=$this->query("SELECT * FROM entry WHERE ".$cond1  . (!empty($cond2)?" AND $cond2":'') . ($cond3!=''?" LIMIT $cond3":''));
				break;

            case 'entry_by_sname':
                $cond1=Tools::like($cond1);
                $cond2; // where
                $res=$this->query("SELECT * FROM entry WHERE sname LIKE '$cond1' ".(!empty($cond2)?"AND $cond2":''));
                $this->next();
                break;

            case 'entry':
                $res=$this->query("SELECT * FROM entry ".(!empty($cond1) ? " WHERE ".$cond1 : '')." ORDER BY dt_added DESC" . ($cond3!=''?" LIMIT $cond3":''));
                break;

            case 'entry_by_id':
                $cond1=intval($cond1);
                $cond2; // where
                $res=$this->query("SELECT * FROM entry WHERE entry_id='$cond1' ".(!empty($cond2)?"AND $cond2":''));
                $this->next();
                break;
		}
		return($res);
	}
	
}