<?

/* Базовый Класс управления дисконтной системой  */

abstract class Orders_Discount extends DB {
	
	public $dc_states=array(0=>'добавлена', 1=>'доступна для активации', 2=>'активирована', 3=>'анулирована до активации', 4=>'анулирована после активации');

	public $min_d,$min_m,$min_y,$max_d,$max_m,$max_y;
	
	function __construct()
	{
		parent::__construct();
	}


    /*
     * возвращает процент скидки
     */
	function getDiscount ($r=array())
	{
        $user_id=(int)@$r['user_id'];
        $cost=(float)@$r['cost'];
        $orders=(int)@$r['orders'];
		$os=new DB();
		$res=false;
		$res1=$res2=0;
		$os->query("SELECT * FROM os_user WHERE (user_id='$user_id')AND(NOT LD)");
		if($os->qnum()){
			$os->next();
			$user_id=$os->qrow['user_id'];
			$os->query("SELECT count(order_id) FROM os_order WHERE (user_id='$user_id')AND(NOT LD)AND((state_id=1)OR(state_id=4))");
			$os->next(MYSQL_NUM);
			$on=$os->qrow[0]+$orders;
			$os->query("SELECT max(value) FROM os_limit WHERE (type=1)AND(lim<='$on')");
			$os->next(MYSQL_NUM);
			$res1=$os->qrow[0];
			$os->query("SELECT sum(bcost) FROM os_order WHERE (user_id='$user_id')AND(NOT LD)AND((state_id=1)OR(state_id=4))");
			$os->next(MYSQL_NUM);
			$s=$os->qrow[0]+$cost;
			$os->query("SELECT max(value) FROM os_limit WHERE (type=0)AND(lim<='$s')");
			$os->next(MYSQL_NUM);
			$res2=$os->qrow[0];
			$res=max($res1,$res2);
		} // <-- не найден юзер
		unset($os);
		return($res);
	}

	/* TODO:: переделать на шаблоны   */
	function sendmail($from, $to, $body='',$subj='', $tpl='')
	{
		$tpl=trim($tpl);
		$header="From: $from\r\nReply-To: $from\r\n";
		if($tpl!='') {
			$r=$body;
			$body='';
			if(($code=Data::get('order_mail_charset'))=='') $code='utf-8';
			if($code=='koi8-r') $subj='=?koi8-r?B?'.base64_encode(convert_cyr_string($subj, "w","k")).'?=';
			$header="Content-type: text/html; charset=\"$code\"\r\n".$header;
			$er=error_reporting(0);
			ob_start();
			eval("include '".Cfg::_get('root_path')."/app/templates/mail/{$tpl}.php';");
			if($code!='utf-8') $body = iconv('utf-8','KOI8-R//IGNORE',ob_get_contents());
				else $body=ob_get_contents();
			ob_end_clean();
			error_reporting($er);
		}
		return mail($to, $subj, $body, $header);
	}

    /*
     * возвращает значение в рублях скидки
     */
	function getDiscountValue($r=array())
	{
        $user_id=(int)@$r['user_id'];
        $cost=(float)@$r['cost'];
		return(intval($cost-$cost*$this->getDiscount(array('user_id'=>$user_id,'cost'=>$cost))/100));
	}

	function min_dcd()
	{
		$this->query("SELECT min(dt_state) FROM os_dc");
		$this->next(MYSQL_NUM);
		if($this->qrow[0]!=0){
		preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})/",$this->qrow[0],$m);
		$this->min_d=$m[3];
		$this->min_m=$m[2];
		$this->min_y=$m[1];
		return($this->qrow[0]);
		}else return(date("now"));
	}
	function max_dcd()
	{
		$this->query("SELECT max(dt_state) FROM os_dc");
		$this->next(MYSQL_NUM);
		if($this->qrow[0]!=0){
		preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})/",$this->qrow[0],$m);
		$this->max_d=$m[3];
		$this->max_m=$m[2];
		$this->max_y=$m[1];
		return($this->qrow[0]);
		}else return(date("now"));
	}

}