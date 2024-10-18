<?
if (!defined('true_enter')) die ("No direct access allowed.");

class BotLog
{

	static public $UA=array(
		1=>array(
			'label'=>'Yandex',
			'mainString'=>'yandex',
			'UA'=>array(
				// http://help.yandex.ru/webmaster/?id=995329
				'YandexBot'=>array('regex'=>"~YandexBot~i",'info'=>'сновной индексирующий робот'),
				'YandexMetrika'=>array('regex'=>"~YandexMetrika~",'info'=>' робот Яндекс.Метрики'),
				'YandexMarket'=>array('regex'=>"~YandexMarket~",'info'=>'робот Яндекс.Маркет'),
				'YandexWebmaster'=>array('regex'=>"~YandexWebmaster~",'info'=>'робот, обращающийся к странице при добавлении ее через форму «Добавить URL»'),
				'YandexImages'=>array('regex'=>"~YandexImages~",'info'=>'индексатор Яндекс.Картинок'),
				'YandexVideo'=>array('regex'=>"~YandexVideo~",'info'=>'индексатор Яндекс.Видео'),
				'YandexMedia'=>array('regex'=>"~YandexMedia~",'info'=>'робот, индексирующий мультимедийные данные'),
				'YandexBlogs'=>array('regex'=>"~YandexBlogs~",'info'=>'робот поиска по блогам, индексирующий комментарии постов'),
				'YandexFavicons'=>array('regex'=>"~YandexFavicons~",'info'=>'робот, индексирующий пиктограммы сайтов (favicons)'),
				'YandexPagechecker'=>array('regex'=>"~YandexPagechecker~",'info'=>'робот, обращающийся к странице при валидации микроразметки через форму «Валидатор микроразметки»'),
				'YandexImageResizer'=>array('regex'=>"~YandexImageResizer~",'info'=>'робот мобильных сервисов'),
				'YandexDirectDyatel'=>array('regex'=>"~YandexDirect.+?Dyatel~",'info'=>'«простукивалка» Яндекс.Директа. Она проверяет корректность ссылок из объявлений перед модерацией'),
				'YandexDirect'=>array('regex'=>"~YandexDirect~",'info'=>'робот, индексирующий страницы сайтов, участвующих в Рекламной сети Яндекса'),
				'YandexNews'=>array('regex'=>"~YandexNews~",'info'=>'робот Яндекс.Новостей'),
				'YandexNewslinks'=>array('regex'=>"~YandexNewslinks~",'info'=>'«простукивалка» Яндекс.Новостей. Используется для проверки ссылок из новостных материалов'),
				'YandexCatalog'=>array('regex'=>"~YandexCatalog~",'info'=>'«простукивалка» Яндекс.Каталога. Если сайт недоступен в течение нескольких дней, он снимается с публикации. Как только сайт начинает отвечать, он автоматически появляется в Каталоге'),
				'YandexAntivirus'=>array('regex'=>"~YandexAntivirus~",'info'=>'антивирусный робот, который проверяет страницы на наличие опасного кода'),
				'YandexZakladki'=>array('regex'=>"~YandexZakladki~",'info'=>'«простукивалка» Яндекс.Закладок. Используется для проверки доступности страниц, добавленных в закладки'),
				'YandexSitelinks'=>array('regex'=>"~YandexSitelinks~",'info'=>'«простукивалка» быстрых ссылок. Используется для проверки доступности страниц, определившихся в качестве быстрых ссылок.'),
				'YandexVertis'=>array('regex'=>"~YandexVertis~",'info'=>'Робот поисковых вертикалей (отвечает за классификацию инфы по тематикам: новости, афиша, работа, википедия и т.п.)')
			),
			'exclude'=>array(
				"~Yandex Browser~",
				"~Edition Yandex~"
			)
		),
		2=>array(
			'label'=>'Google',
			'mainString'=>'google',
			'UA'=>array(
				'Googlebot'=>array('regex'=>"~Googlebot~i",'info'=>'сновной индексирующий робот'),
				'GooglebotNews'=>array('regex'=>"~Googlebot-News~",'info'=>'Googlebot News'),
				'GooglebotImages'=>array('regex'=>"~Googlebot-Image~",'info'=>'Googlebot Images'),
				'GooglebotVideo'=>array('regex'=>"~Googlebot-Video~",'info'=>'Googlebot Video'),
				'GoogleMobile'=>array('regex'=>"~Googlebot-Mobile~",'info'=>'Google Mobile'),
				'GoogleAdSense'=>array('regex'=>"~Mediapartners.+?Google~",'info'=>'Google Mobile AdSense'),
				'GoogleWebPreview'=>array('regex'=>"~Google Web Preview~",'info'=>'Google Web Preview'),
				'GoogleWireTrans'=>array('regex'=>"~Google Wireless Transcoder~",'info'=>'Google Wireless Transcoder http://www.webmasterworld.com/forum11/3210.htm'),
				'GoogleAdsBot'=>array('regex'=>"~AdsBot-Google~",'info'=>'Google AdsBot landing page quality check')
			),
			'exclude'=>array(
			)
		),
		3=>[
			'label'=>'Bing',
			'mainString'=>['Bing','Adidxbot', 'msnbot'],
			'UA'=>[
				'Bingbot'=>['regex'=>"~Bingbot~i",'info'=>''],
				'Adidxbot'=>['regex'=>"~Adidxbot~i",'info'=>'Adidxbot'],
				'MSNBot'=>['regex'=>"~MSNBot~/i",'info'=>'MSNBot'],
				'BingPreview'=>['regex'=>"~BingPreview~i",'info'=>'Page snapshots in Bing Windows 8 app to bring new crawl traffic to sites http://blogs.bing.com/webmaster/2012/10/26/page-snapshots-in-bing-windows-8-app-to-bring-new-crawl-traffic-to-sites/'],

			]
		],
		4=>[
			'label'=>'Mail.ru',
			'mainString'=>'Mail.ru',
			'UA'=>[
				'Bingbot'=>['regex'=>"~Mail.Ru~",'info'=>'mail.ru crawler']
			]

		],
		5=>[
			'label'=>'Yahoo!',
			'mainString'=>'Yahoo',
			'UA'=>[
				'Yahoo!Slurp'=>['regex'=>"~Yahoo! Slurp~",'info'=>'Yahoo! crawler']
			]

		]

	);

	static private $db = NULL;

	public static function makeHistory()
	{
		if(empty(static::$db)) static::$db=new DB();

		// берем все строки старше 30 дней
		$dts=static::$db->fetchAll("SELECT DATE(dt_visited) AS d FROM bot_log WHERE dt_visited < DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY) GROUP by d ORDER BY d ASC",MYSQLI_ASSOC);

		foreach($dts as $v){
			static::$db->query("DELETE FROM bot_history WHERE `date`='{$v['d']}'");
			static::$db->query("SELECT * FROM bot_log WHERE dt_visited LIKE '{$v['d']}%'");
			$dr=array();
			while(static::$db->next()!==false){
				$r=static::$db->qrow;
				if(empty($dr[$r['se']])) $dr[$r['se']]=array();
				if(empty($dr[$r['se']][$r['botName']])) $dr[$r['se']][$r['botName']]=1; else $dr[$r['se']][$r['botName']]++;
			}
			if(!empty($dr))
				foreach($dr as $se=>$vv)
					foreach($vv as $botName=>$hits){
						static::$db->insert('bot_history',array('se'=>$se,'`date`'=>$v['d'],'botName'=>$botName,'hits'=>$hits));
						echo "{$v['d']} : ";
						echo static::$UA[$se]['label']." -> $botName -> $hits hits \n";
					}
			static::$db->query("DELETE FROM bot_log WHERE dt_visited LIKE '{$v['d']}%'");

			echo "----\n";
		}
		static::$db->query("OPTIMIZE TABLE bot_log");
	}


	public static function detect()
	{
		if (!Cfg::get('botLog')) return;

		$ua = trim(Tools::utf(@$_SERVER['HTTP_USER_AGENT']));
		//		$ua='Mozilla/5.0 (compatible; YandexBot/3.0; +http://yandex.com/bots)';
		//        $ua='Mozilla/5.0 Mediapartners-Google/2.1; +http://www.google.com/bot.html)';
		//        $ua='Mobile/8B117 Safari/6531.22.7 (compatible; Googlebot-Mobile/2.1; +http://www.google.com/bot.html)';
		//        $ua="Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/534+ (KHTML, like Gecko) BingPreview/1.0b";
		//        $ua="Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)";
		//        $ua="Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)";

		foreach (static::$UA as $seId => $v)
		{ // детект поисковика
			$inua = false;
			if (!is_array($v['mainString']))
			{
				if (mb_stripos($ua, $v['mainString']) !== false) $inua = true;
			}
			else
			{
				foreach ($v['mainString'] as $msv)
				{
					if (mb_stripos($ua, $msv) !== false)
					{
						$inua = true;
						break;
					}
				}
			}
			if ($inua)
			{
				try
				{
					// дополнительная проверка на exclude
					if (!empty($v['exclude'])) foreach ($v['exclude'] as $ex)
					{
						if (preg_match($ex, $ua)) return;
					}


					$dt = date("Y-m-d H:i:s");
					$ip = @$_SERVER['REMOTE_ADDR'];
					$url = Tools::esc(Tools::utf($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']));
					$ua = mb_substr(Tools::esc($ua), 0,255);
					if (empty(static::$db)) static::$db = new DB();
					foreach ($v['UA'] as $botName => &$d)
					{ // детект имени бота
						if (preg_match($d['regex'], $ua))
						{
							static::$db->query("INSERT INTO bot_userAgent (userAgent) VALUES ('$ua') ON DUPLICATE KEY UPDATE userAgentId=LAST_INSERT_ID(userAgentId), userAgent='$ua'");
							$uaId = static::$db->lastId();
							static::$db->query("INSERT DELAYED INTO bot_log (se,botName,userAgentId,url,dt_visited,botIP) VALUES('$seId','$botName','$uaId','$url','$dt',INET_ATON('$ip'))");

							return;
						}
					}
					// если нет детальной информации о боте, записываем без botName
					static::$db->query("INSERT INTO bot_userAgent (userAgent) VALUES ('$ua') ON DUPLICATE KEY UPDATE userAgentId=LAST_INSERT_ID(userAgentId), userAgent='$ua'");
					$uaId = static::$db->lastId();
					static::$db->query("INSERT DELAYED INTO bot_log (se,userAgentId,url,dt_visited,botIP) VALUES('$seId','$uaId','$url','$dt',INET_ATON('$ip'))");

					return;
				} catch(DBException $e){
					$buf=
						"ERROR (".$e->getCode()."): ".$e->getMessage()."\n"
						.'Error at '.$e->getFile()." (".$e->getLine().")\n"
						."Stack:\n"
						.$e->getExceptionTraceAsString()
						."******end stack *******\n\n";
					$dt=Tools::dt();
					Tools::tree_mkdir(Cfg::$config['root_path'].'/assets/logs/');
					@file_put_contents(Cfg::$config['root_path'].'/assets/logs/exceptions.log',"\n".$dt.' - '.@$_SERVER['REMOTE_ADDR'].' - '.@$_SERVER['HTTP_HOST'].@$_SERVER['REQUEST_URI'].' - '.'[BotLog.detect]: '.$buf, FILE_APPEND);
				}
			}

		}

	}


}
	
	
		
		
