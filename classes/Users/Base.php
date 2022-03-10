<?

/* Класс авторизации и управления учеткой пользователя на сайте

	Базовые поля в os_users:
		dt_reg,ligin, pw, email

*/

abstract class Users_Base extends DB {
	
	public $bad_login=false;
	public $bad_pass=false;
	public $info=array();
	public $login;
	public $user_id;
	

	function __construct()
	{
		parent::__construct();
	}

	function que($qname,$cond1='',$cond2='',$cond3='',$cond4='')
	{
		switch ($qname)  
		{
			case 'user_list':
				$res=$this->query("SELECT * FROM os_user WHERE (NOT LD) ORDER BY F,I,O");
				break;
			case 'user_by_email':
				$cond1=Tools::like($cond1);
				$res=$this->query("SELECT os_dc.n1, os_dc.n2, os_dc.dt_state, os_user.login, os_user.pw, os_user.email, os_user.tel1, os_user.tel2, os_user.F, os_user.I, os_user.O, os_user.avto_name, os_user.user_id, os_dc.dc_id FROM os_user INNER JOIN os_dc ON os_user.user_id = os_dc.user_id WHERE (os_dc.state_id=2) AND (NOT os_user.LD) AND (os_user.email LIKE '$cond1')");
				if($this->qnum()) $this->next();
				break;
			case 'user_by_id':
				$cond1=(int)$cond1;
				$res=$this->query("SELECT * FROM os_user WHERE (user_id='$cond1')AND (NOT LD)");
				$this->next();
				break;
				
			default:$res=false;
		}
	}

	public function checkAuth(){
		if(Session::check() && isset($_SESSION['userInfo'])) {
			$this->info=unserialize($_SESSION['userInfo']);
			$this->login=$this->info['login'];
			$this->login=$this->info['login'];
			$this->user_id=$this->info['user_id'];
		}
	}
}
	