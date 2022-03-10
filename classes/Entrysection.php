<?

class Entrysection extends SSCommon {
	
	function __construct()
	{
		parent::__construct();
	}

	function que($qname,$cond1='',$cond2='',$cond3='',$cond4='')
	{
		switch ($qname)  
		{
            case 'news':
                $cond1=intval($cond1); // group
                $cond2=intval($cond2); // исключить id новости
                $cond3=intval($cond3); // limit
                $cond4;// where
                $res=$this->query("SELECT * FROM ss_news WHERE (NOT LD)".
                    ($cond1?" AND (news_group_id='$cond1')":'').
                    ($cond4?"AND $cond4":'').
                    ($cond2?" AND (news_id!='$cond2')":'')." ORDER BY dt DESC".
                    ($cond3!=''?" LIMIT $cond3":''));
                break;
			case 'entry_section':
				$cond1=intval($cond1); // group
				$cond2=intval($cond2); // исключить id
				$cond3=intval($cond3); // limit
                $cond4;// where
				$res=$this->query("SELECT * FROM entry_section");
				break;
			case 'entry_section_by_id':
				$cond1=intval($cond1);
                $cond2; // where
				$res=$this->query("SELECT * FROM entry_section WHERE entry_section_id='$cond1' ".(!empty($cond2)?"AND $cond2":''));
				$this->next();
				break;
			case 'entry_section_by_sname':
				$cond1=Tools::like($cond1);
                $cond2; // where
                $cond3=intval($cond3); // group
				$res=$this->query("SELECT * FROM entry_section WHERE sname LIKE '$cond1' ".(!empty($cond2)?"AND $cond2":''));
				$this->next();
				break;
		}
		return($res);
	}
	
}