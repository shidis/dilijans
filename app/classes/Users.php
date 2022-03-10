<?

class App_Users extends Users {
	
	public $n1;  // номер карты os_dc

	function __construct()
	{
		parent::__construct();
	}
	

	public function user_check_by_n1_n2($n1,$n2)
	{
		$n1=Tools::esc($n1);
		$n11=Tools::like($n1);
		$this->query("SELECT os_dc.n1, os_dc.n2, os_dc.dc_id, os_user.avto_name, os_user.user_id, os_user.login, os_user.F, os_user.I, os_user.O FROM os_user INNER JOIN os_dc ON os_user.user_id = os_dc.user_id WHERE (os_dc.state_id=2) AND (NOT os_user.LD) AND (os_dc.n1 LIKE '$n11')");
		if ($this->qnum()){
			$this->next();
			if(Tools::tolow(Tools::unesc($this->qrow['n2']))==Tools::tolow($n2)){
				Session::start();
				$this->n1=$n1;
				$this->login=$this->qrow['login'];
				$this->user_id=$this->qrow['user_id'];
				$this->info=array(
					'login'=>$this->qrow['login'],
					'n1'=>$this->qrow['n1'],
					'user_id'=>$this->qrow['user_id'],
					'dc_id'=>$this->qrow['dc_id'],
					'avto_name'=>Tools::unesc($this->qrow['avto_name']),
					'F'=>Tools::unesc($this->qrow['F']),
					'I'=>Tools::unesc($this->qrow['I']),
					'O'=>Tools::unesc($this->qrow['O']), 
					'FIO'=>Tools::unesc($this->qrow['I'].' '.$this->qrow['O'].' '.$this->qrow['F'])
				);
				$_SESSION['userInfo']=serialize($this->info);
				return(true);
			}else{
				$this->bad_pass=true;
				return(false);
			}
		}else {
			$this->bad_login=true;
			return(false);
		}
					
	}
/*
	public function user_check_by_login_pw($user,$pass)
	{
		$user=Tools::esc($user);
		$this->query("SELECT * FROM os_user WHERE (login LIKE '$user')AND(NOT LD))");
		if ($this->qnum()){
			$this->next();
			if(Tools::tolow(Tools::unesc($this->qrow['pw']))==Tools::tolow($pass)){
				Session::start();
				$this->login=$user;
				$this->user_id=$this->qrow['user_id'];
				$this->info=array('login'=>$this->qrow['login'], 'avto_name'=>$this->qrow['avto_name']);
				$_SESSION['userInfo']=serialize($this->info);
				return(true);
			}else{
				$this->bad_pass=true;
				return(false);
			}
		}else {
			$this->bad_login=true;
			return(false);
		}
					
	}
	public function user_check_by_n1_pw($n1,$pass)
	{
		$n1=Tools::esc($n1);
		$this->query("SELECT os_dc.n1, os_dc.dc_id, os_user.pw, os_user.avto_id, os_user.avto_name, os_user.user_id FROM os_user INNER JOIN os_dc ON os_user.user_id = os_dc.user_id WHERE (os_dc.state_id=2) AND (NOT os_user.LD) AND (os_dc.n1 LIKE '$n1')");
		if ($this->qnum()){
			$this->next();
			if(Tools::tolow(Tools::unesc($this->qrow['pw']))==Tools::tolow($pass)){
				Session::start();
				$this->n1=$n1;
				$this->user_id=$this->qrow['user_id'];
				$this->info=array('n1'=>$this->qrow['n1'], 'user_id'=>$this->qrow['user_id'], 'dc_id'=>$this->qrow['dc_id'], 'avto_name'=>$this->qrow['avto_name']);
				$_SESSION['userInfo']=serialize($this->info);
				return(true);
			}else{
				$this->bad_pass=true;
				return(false);
			}
		}else {
			$this->bad_login=true;
			return(false);
		}
					
	}
	
*/	
	
}