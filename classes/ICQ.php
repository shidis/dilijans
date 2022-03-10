<?
if (!defined('true_enter')) die ("Direct access not allowed!");

class ICQ extends DB
{
	var $icq_last_state=-1;
	var $icq_conn=true;
	var $icq_list=array();
	
function __construct()
{
	parent::__construct();
}

function icq_connect($cdb_host='',$cdb_user='',$cdb_pass='',$cdb_db='')
{
	if ($cdb_db==''){
		$res= $this->sql_connect();
	}else{
		$this->sql_host=$cdb_host;
		$this->sql_user=$cdb_user;
		$this->sql_pass=$cdb_pass;
		$this->sql_db=$cdb_db;
		$res= $this->sql_connect();
	}
	$this->icq_conn=$res;
	return $res;
}
	
function icq_get_state($uin)
{
$s='';
$fp = fsockopen("status.icq.com", 80, $errno, $errstr, 10);
if (!$fp) {
   echo "$errstr ($errno)<br />\n";
} else {
   $out = "GET /online.gif?icq=".$uin."&img=5 HTTP/1.1\r\n";
   $out .= "User-Agent: Microsoft Internet Explorer\r\n";
   $out .= "Host: online.icq.com\r\n";
   $out .= "Connection: Close\r\n\r\n";

   fwrite($fp, $out);
   $s='';
   while (!feof($fp)) {
       $s.=fgets($fp, 128);
   }
   fclose($fp);
}
if(mb_strpos($s,'online1')!==false) $this->icq_last_state=1; else $this->icq_last_state=0;
return $this->icq_last_state;
}
	
function icq_update_state($out=true)
{
	if($out) {
		echo "ICQ scanner started...\n";
	}
	$icq=new ICQ;
	$this->query("SELECT * FROM icq WHERE active=1");
	echo $this->qnum();
	while($this->next()!==false){
		$this->icq_get_state($this->only_num($this->qrow['uin']));
		$dt=date("Y-m-d H:i:s");
		$icq->query("UPDATE icq SET dt_check='$dt', online={$this->icq_last_state} WHERE icq_id={$this->qrow['icq_id']}");
		if($out) {
			echo "{$this->qrow['uin']} ({$this->qrow['name']}) -> ".($this->icq_last_state?'ONLINE':'OFFLINE').' / '.$dt."\n";
		}
	}
	if($out) echo 'finished OK';
	unset($icq);
}

function icq_get_list($mode='online',$active='1',$gr='')
{
	$this->icq_list=array();
	$this->query("SELECT * FROM icq WHERE ".($active?'(active=1)':($active==='0'?'(active=0)':'((active=0)OR(active=1))')).($mode=='online'?'AND(online=1)':($mode=='offline'?'AND(online=0)':'')).($gr!=''?"AND(gr LIKE '$gr')":'')." ORDER BY online DESC, name ASC");
	while($this->next()!==false)
		$this->icq_list[$this->qrow['icq_id']]=array('uin'=>trim($this->qrow['uin']),'name'=>Tools::unesc($this->qrow['name']),'url'=>("http://wwp.icq.com/scripts/contact.dll?msgto=".$this->only_num($this->qrow['uin'])),'online'=>$this->qrow['online'],'img'=>($this->qrow['online']?(Cfg::get('icq_images_dir').'/online.gif'):(Cfg::get('icq_images_dir').'/offline.gif')));
	if($this->qnum()) $this->first();
}
		
function only_num($s)
{
	$s=trim($s);
	$abc=array('1','2','3','4','5','6','7','8','9','0');
	$r='';
	for($i=0;$i<mb_strlen($s);$i++) if(array_search($s[$i],$abc)!==false)  $r.=$s[$i];
	return $r;
}

}?>