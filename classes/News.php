<?

class News extends SSCommon {
	
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
			case 'news_list':
				$cond1=intval($cond1); // limit
				$cond2=intval($cond2); // group
                $cond4;// where
				$res=$this->query("SELECT * FROM ss_news WHERE (NOT LD)".
					($cond2?"AND (news_group_id='$cond2')":'').
                    ($cond4?"AND $cond4":'').
					" ORDER BY dt DESC LIMIT 0,$cond1");
				break;
			case 'news_by_id':
				$cond1=intval($cond1);
                $cond2; // where
				$res=$this->query("SELECT * FROM ss_news WHERE NOT LD AND news_id='$cond1' ".(!empty($cond2)?"AND $cond2":''));
				$this->next();
				break;
			case 'news_by_sname':
				$cond1=Tools::like($cond1);
                $cond2; // where
                $cond3=intval($cond3); // group
				$res=$this->query("SELECT * FROM ss_news WHERE  NOT LD AND sname LIKE '$cond1' ".(!empty($cond2)?"AND $cond2":'').(!empty($cond3)?" AND news_group_id=$cond3":''));
				$this->next();
				break;
			case 'news_group':
				$res=$this->query("SELECT * FROM ss_news_group ORDER BY pos");
				break;
		}
		return($res);
	}
	
}