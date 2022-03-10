<? 
if (!defined('true_enter')) die ("Direct access not allowed!");

abstract class Log_Base {
	
	// для файлового лога
	private static $logFName;
	private static $fNameMode='daily';
	private static $handle=false;
//	[Sun Mar 27 11:02:20 2011] [error] [client 178.176.29.170] File does not exist: /storage/home/srv18372/htdocs/dl/images/hline3_1.gif, referer: http://dilijans.org/css/style.css

//  2013/12/13 01:59:26 [warn] 1338#0: *1334838 an upstream response is buffered to a temporary file /var/nginx/fastcgi_temp/6/57/0000002576 while reading upstream, client: 85.115.224.151, server: megatrack.ru, request: "GET /cimg/diski/m/2/wiger-kia-wgr-1203-i21200.jpg HTTP/1.1", upstream: "fastcgi://127.0.0.1:9002", host: "www.megatrack.ru", referrer: "http://images.yandex.ru/yandsearch?source=wiz&fp=2&uinfo=ww-1007-wh-531-fw-782-fh-448-pd-1&p=2&text=%D0%BB%D0%B8%D1%82%D1%8B%D0%B5%20%D0%B4%D0%B8%D1%81%D0%BA%D0%B8%20Kia%20Sportage%20II%20R16%205x114.3%20ET41&noreask=1&pos=79&rpt=simage&lr=213&img_url=http%3A%2F%2Fwww.nakolesah.ru%2FExports%2FGetImage.ashx%3F3a9397fc-8e02-49d9-95da-2fd355fcbbdc"

    // должна возвращаться массив из значений [0]=>номер строки, [1]=>time ,[2]=>строка типа ошибки, [3]=>IP, [4]=>сообщение об ошибке
	public static $serverErrorLogFormat="/^\[([^\]]+)\] \[([^\]]+)\] \[client ([^\]]+)\] (.*)$/";
	
	// для мускульного лога
	
	
	
	private static function openFile()
	{
	
		if(empty(static::$logFName)) {
			echo '[Error_Base.openFile]: logFName is empty. Stoped!';
			return false;
		}
		$fn=Cfg::$config['root_path'].'/'.Cfg::$config['logDir'].'/'.$logFName;
		switch (static::$fNameMode){
			case 'daily': $dn=date("Y-m-d"); break;
			case 'monthly': $dn=date("Y-m");	break;	
			case 'hourly': $dn=date("Y-m-d-H"); break;
			default: $dn=date("Y-m-d"); break;
		}
		$fn.='.'.$dn;
		$i=0;
		do{
			static::$handle=fopen($fn,'a+');
			$i++;
		}while(FALSE===static::$handle && $i<=20);
		if(FALSE===static::$handle){
			echo '[Error_Base.openFile]: Error while opening file '.$fn.'. Stoped!';
			return false;
		}
		return true;	
	
	}

	private static function writeLine($data=array())
	{
		$dt=date("Y-m-d H:i:s");
	    // ..........
	}



	// СЕРВЕРНЫЕ ЛОГИ
		
	public static function serverErrorLogRead($startLine,$limit)
	{
	
		$r=array('fres'=>true,'fres_msg'=>'');
		$i=0;
		$lf=Cfg::get('serverErrorLogFormat');
		if(!empty($lf)) static::$serverErrorLogFormat=$lf;
		$fn=Cfg::get('serverErrorLogFilePath');
		if(!is_file($fn)) return array();
		do{
			$handle=fopen($fn,'r');
			$i++;
		}while(FALSE===$handle && $i<=20);
		if(FALSE===$handle){
			$r['fres_msg']='[Error_Base.openFile]: Error while opening file '.$fn.'. Stoped!';
			$r['fres']=false;
			return $r;
		}
		$r=array();
		$i=$j=0;
		while($i<($startLine+$limit) && !feof($handle)) {
			$s=fgets($handle,2048);
			$i++;
			if($i>=$startLine) {
				if(preg_match(static::$serverErrorLogFormat,$s,$m)){
					$r[$j]['id']=$j;
					$ref=explode(", referer: ",$m[4]);
					$r[$j]['cell']=array($i,$m[1],$m[2],$m[3],@$ref[0],@$ref[1]!=''?"<a href=\"{$ref[1]}\" target=_blank>{$ref[1]}</a>":'');
				}
				$j++;
			}
		}
		return $r;
			
	}
		
	public static function clearServerLog()
	{
	
		$f=@fopen(Cfg::get('serverErrorLogFilePath'),'w');
		@fclose($f);
	
	}
}
		