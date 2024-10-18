<?
if (!defined('true_enter')) die ("Direct access not allowed!");

class Url
{
	public static $spath = []; // урл текущей страницы в массиве. Если главная то ===''
	public static $path = '';   // урл текущей страницы в строке начинается с / |  Главная == /   |  urlSuffix не отбрасывается
	public static $sq = []; // get параметры
	public static $urlSuffix;
	public static $escape = false;
	public static $trailingSlash = false;

	static public function setUrlSuffix($s)
	{
		Url::$urlSuffix = $s;
	}

	public static function parseUrl()
	{
		/*
        RewriteCond %{REQUEST_FILENAME} -f [OR]
        RewriteCond %{REQUEST_FILENAME} -d
        RewriteRule .+ - [L]
        RewriteRule ^(.*)$ index.php?__q=$1 [L,QSA]
        __q - можно переименовать без каких либо изменений в коде
        */
		$s = parse_url($_SERVER['REQUEST_URI']);
		Url::$sq = [];
		while ($s['path'] != ($ss = str_replace('//', '/', $s['path']))) $s['path'] = $ss;
		Url::$path = $s['path'];
		if ($s['path'] == '' || $s['path'] == '/') Url::$spath = '';
		else
		{
			Url::$spath = [];
			$spath = explode('/', str_replace(Url::$urlSuffix, '', $s['path']));
			foreach ($spath as $k => $v) if ($v != '') if (Url::$escape) Url::$spath[Tools::esc($k)] = Tools::esc($v);
			else Url::$spath[$k] = $v;
		}
		if (@$s['query'] != '')
		{
			if (Url::$escape) $s['query'] = Tools::esc(($s['query']));
			parse_str($s['query'], Url::$sq);
		}

	}

	public static function checkTrailingSlash()
	{
		if (!Url::$trailingSlash && !is_dir(realpath(Cfg::$config['root_path'] . Url::$path)) && rtrim(Url::$path, '/') != Url::$path) return false;

		return true;
	}


	public static function arr2hiddenFields($arr)
	{ // отрабатываются только одномерные массивы
		foreach ($arr as $k => $v)
		{
			if (is_array($v))
			{
				foreach ($v as $k1 => $v1)
				{
					?><input type="hidden" name="<?= $k ?>[<?= $k1 ?>]" value="<?= $v1 ?>"><? }
			}
			else
			{ ?><input type="hidden" name="<?= $k ?>" value="<?= $v ?>"><? }
		}
	}

	public static function qarr($remove = [])
	{
		$a = Url::$sq;
		foreach ($remove as $v) unset($a[$v]);

		return $a;
	}

	public static function qstr($remove = [])
	{
		$a = Url::$sq;
		foreach ($remove as $v) unset($a[$v]);

		return http_build_query($a);
	}

	public static function qurl($url, $qs1 = '', $qs2 = '')
	{
		if ($qs1 != '') if (mb_strpos($url, '?') !== false) $url .= '&' . $qs1;
		else $url .= '?' . $qs1;
		if ($qs2 != '') if (mb_strpos($url, '?') !== false) $url .= '&' . $qs2;
		else $url .= '?' . $qs2;

		return $url;
	}

	public static function trimWWW($url)
	{
		if (mb_strpos($url, '://') === false)
		{
			return preg_replace("/^www\.(.*)$/", "\$1", $url);
		}
		$s = parse_url($url);
		$s['host'] = str_replace('www.', '', @$s['host']);
		if (@$s['scheme'] != '') $s['scheme'] .= '://';
		if (@$s['password'] != '' && @$s['user'] != '')
		{
			$s['user'] .= ':';
			$s['password'] .= '@';
		}
		elseif (@$s['user'] != '') $s['user'] .= '@';
		if (@$s['query'] != '') $s['query'] = '?' . $s['query'];
		echo @$s['scheme'] . @$s['user'] . @$s['password'] . @$s['host'] . @$s['path'] . @$s['query'];

		return @$s['scheme'] . @$s['user'] . @$s['password'] . @$s['host'] . @$s['path'] . @$s['query'];
	}

	public static function hackDetect()
	{
		$stateFile = Cfg::$config['root_path'] . '/assets/res/hackdetect.state';
		$hack = false;
		// проверка гет параметров на подозрительные комбинации
		$s = urldecode($_SERVER['REQUEST_URI']);
		if (preg_match("~([<>]+|file_get_contents[\s]*\(|eval[\s]*\(|')~iu", $s)) $hack = true;
		if ($hack)
		{
			$dt = Tools::dt();
			@file_put_contents(Tools::getLogPath() . 'hackdetect.log', "\n" . $dt . ' - ' . $_SERVER['REMOTE_ADDR'] . ' - ' . $s, FILE_APPEND);
			if (is_file($stateFile))
			{
				$c = unserialize(file_get_contents($stateFile));
			}
			else $c = [];
			$c['h_url_lastDtAdded'] = $dt; // метка времени последней добавленной записи
			if (empty($c['lastAlertEmailed']) || (time() - $c['lastAlertEmailed']) > 60 * 60)
			{
				// шлем емейл каждые 60 минут
				Mailer::sendmail([
					'fromAddr' => 'hackdetect@' . static::trimWWW(Cfg::get('site_url')), 'toAddr' => Cfg::get('supportEmail'),
					'subject'  => 'Попытка взлома ' . Cfg::get('site_name') . ' детектед',
					'body'     => 'С момента последней проверки в ' . date("Y-m-d H:i:s", !empty($c['lastAlertEmailed']) ? $c['lastAlertEmailed'] : time()) . ' появились новый записи в /assets/logs/h_url.txt',
				]);
				$c['lastAlertEmailed'] = time();
			}

			file_put_contents($stateFile, serialize($c));
		}
	}

  public static function getServerProtocol()
  {
    if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) {
      return 'https';
    } else {
      return 'http';
    }
  }

  public static function getLink($type, $carParams = [], $absolute = true) {
    $url = '';
    if ($absolute) {
      $url = Url::getServerProtocol() . '://' . Url::trimWWW(Cfg::get('site_url'));
    }
    if ($type == 'tyre') {
      $url .= '/'. App_Route::_getUrl('avtoPodborShin') ;
    } elseif ($type =='disk') {
      $url .= '/'. App_Route::_getUrl('avtoPodborDiskov') ;
    } else {
      return 'Неправильный тип ссылки';
    }
    if (!empty($carParams)) {
      $url .= '/' . implode('--', $carParams);
    }
    $url .= '.html';
    return $url;
  }
}