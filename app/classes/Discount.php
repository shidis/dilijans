<?

class App_Discount extends Orders_Discount {
	
	function __construct()
	{
		parent::__construct();
	}

    function getDiscountValue($r=array())
    {
        return 0;
    }

    function getDiscount($r=array())
    {
        return 0;
    }

    function dc_activate($r)
	{	
		$this->fres='';
		$res=true;
		$dt=date("Y-m-d H:i:s");
		if(!isset($r['email'])) $r['email']='';
		if(!isset($r['login'])) $r['login']='';
		if(!isset($r['pw'])) $r['pw']='';
		if(!isset($r['tel1'])) $r['tel1']='';
		if(!isset($r['tel2'])) $r['tel2']='';
		if(!isset($r['F'])) $r['F']='';
		if(!isset($r['I'])) $r['I']='';
		if(!isset($r['O'])) $r['O']='';
		$n11=Tools::like($r['n1']);
		$n22=Tools::like($r['n2']);
		if(mb_strlen($r['n1'])==Cfg::get('digit_n1_num') && (!isset($r['n2']) || mb_strlen(@$r['n2'])==Cfg::get('digit_n2_num'))){
			$this->query("SELECT * FROM os_dc WHERE (n1 LIKE '{$n11}')".(isset($r['n2'])?"AND(n2 LIKE '{$n22}')":''));
			if($this->qnum()){
				$this->next();
				$dc_id=$this->qrow['dc_id'];
				if($this->qrow['state_id']==1){
					$dc_id=$this->qrow['dc_id'];
				}else {
					$res=false;
					switch ($this->qrow['state_id']){
					case '3':
					case '0': $this->fres='Карта не может быть ативирована [0]';break;
					case '4': $this->fres='Карта не активации не подлежит [4]';break;
					case '2': $this->fres='Карта уже активирована [2]';break;
					default: $this->fres='Неизвестная ошибка [SID]';break;}
				}
			}else {$res=false; $this->fres='Карта не может быть активирована [-1]';}
			if($res)
			if(@$r['user_id']){	// для существующего юзера
				$this->que('user_by_id',$r['user_id']);
				if($this->qnum()){
					$user_id=$this->qrow['user_id'];
					$n11=Tools::like($r['n1']);
					$this->query("SELECT * FROM os_dc WHERE (n1 LIKE '{$n11}')");
					if($this->qnum()){
						$this->next();
						if($this->qrow['state_id']==1){
							$dc_id=$this->qrow['dc_id'];
							$this->query("UPDATE os_dc SET state_id=5, dt_state='$dt' WHERE user_id='{$r['user_id']}'");
							$this->query("UPDATE os_dc SET user_id='$user_id', state_id=2 WHERE dc_id='$dc_id'");
						}else {
							$res=false;
							switch ($this->qrow['state_id']){
							case '3':
							case '0': $this->fres='Карта не может быть ативирована [0]';break;
							case '4': $this->fres='Карта не активации не подлежит [4]';break;
							case '2': $this->fres='Карта уже активирована [2]';break;
							default: $this->fres='Неизвестная ошибка [SID]';break;}
						}
					}else {$res=false; $this->fres='Карта не может быть активирована [-1]';}
				}else  {$res=false; $this->fres='Неверный логин';}
			// для НЕсуществующего юзера c регистрацией. 
			// ТОДО:: перенести регистрацию юзера в класс User
			}else{ 
				$this->query("INSERT INTO os_user (dt_reg,login,pw,email,tel1,tel2,F,I,O) VALUES ('$dt','{$r['login']}','{$r['pw']}','{$r['email']}','{$r['tel1']}','{$r['tel2']}','{$r['F']}','{$r['I']}','{$r['O']}')");
				$user_id=mysql_insert_id();
			}
		}else  {$res=false; $this->fres='Номер карты или ПИН-код введен не корректно';}
		if($res){
			$this->query("UPDATE os_dc SET user_id='$user_id', state_id=2, dt_state='$dt' WHERE dc_id='$dc_id'");
			switch (Cfg::get('dc_auth_type')){
				case 'n1_pw': $msg="Вас приветствует интернет-магазин ".Cfg::get('site_name').".\r\n\r\nДисконтная карта под номером {$r['n1']} активирована. \r\nПароль для доступа в личный кабинет и получения скидки - {$r['pw']}";break;
				case 'n1_n2': $msg="Вас приветствует интернет-магазин ".Cfg::get('site_name').".\r\n\r\nДисконтная карта под номером {$r['n1']} активирована. \r\nИспользуйте номер карты и ПИН-код для авторизации на сайте";break;
				default: $msg='';
			}
			if(mb_strpos($r['email'],'@')) $this->sendmail('Интернет магазин '.Cfg::get('site_name').' <'.Data::get('mail_info').'>',$r['email'],$msg,'Активация дисконтной карты');
			
		}
		return $res;
	}


}