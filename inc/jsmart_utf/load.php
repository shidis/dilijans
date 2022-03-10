<?php
/* JSmart v1.0 Final Release
 * Copyright (c) 2006 by Ali Farhadi.
 * Released under the terms of the GNU Public License.
 * See the GPL for details.
 *
 * Email: ali@farhadi.ir
 * Website: http://farhadi.ir/
 */
if (!function_exists('getallheaders')) 
{
    function getallheaders() 
    {
       foreach ($_SERVER as $name => $value) 
       {
           if (substr($name, 0, 5) == 'HTTP_') 
           {
               $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
           }
       }
       return $headers;
    }
}

 if (!function_exists('apache_request_headers')) { 
        function apache_request_headers() { 
            foreach($_SERVER as $key=>$value) { 
                if (substr($key,0,5)=="HTTP_") { 
                    $key=str_replace(" ","-",ucwords(strtolower(str_replace("_"," ",substr($key,5))))); 
                    $out[$key]=$value; 
                }else{ 
                    $out[$key]=$value; 
        } 
            } 
            return $out; 
        } 
} 

function header_exit($status) {
	header("HTTP/1.0 $status");
	exit();
}

function header_nocache() {
	// already expired
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

	// always modified
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	
	// HTTP/1.1
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Cache-Control: max-age=0", false);
	
	// HTTP/1.0
	header("Pragma: no-cache");
	
	//generate a unique Etag each time
	header('Etag: '.time());
}

function debug_exit($msg){
	if (!JSMART_DEBUG_ENABLED) {
		header_exit('404 Not Found');
	}
	header_nocache();
	header('Content-Type: text/html; charset='.JSMART_CHARSET);
	header("Content-Encoding: none");
	echo "//<script>\n";
	echo "alert('JSmart Error: ".($msg)."');\n";
	echo "//</script>\n";
	exit();
}

(@include('config.php')) or debug_exit('missing configuration file');

function remove_css_comments($str) {
	$res = '';
	$i=0;
	$inside_block = false;
	while ($i+1<strlen($str)) {
		if ($str{$i}=='"' || $str{$i}=="'") {//quoted string detected
			$quote = $str{$i};
			do {
				if ($str{$i} == '\\') {
					$res .= $str{$i++};
				}
				$res .= $str{$i++};
			} while ($i<strlen($str) && $str{$i}!=$quote);
			$res .= $str{$i++};
			continue;
		} elseif (strtolower(substr($res, -4))=='url(') {//uri detected
			do {
				if ($str{$i} == '\\') {
					$res .= $str{$i++};
				}
				$res .= $str{$i++};
			} while ($i<strlen($str) && $str{$i}!=')');
			$res .= $str{$i++};
			continue;
		} elseif ($str{$i}.$str{$i+1}=='/*') {//css comment detected
			$i+=3;
			while ($i<strlen($str) && $str{$i-1}.$str{$i}!='*/') $i++;
			if (@$current_char == "\n") $str{$i} = "\n";
			else $str{$i} = ' ';
		}
		
		if (strlen($str) <= $i+1) break;
		
		$current_char = $str{$i};
		
		if ($inside_block && $current_char == '}') {
			$inside_block = false;
		}
		
		if ($current_char == '{') {
			$inside_block = true;
		}
		
		if (preg_match('/[\n\r\t ]/', $current_char)) $current_char = " ";
		
		if ($current_char == " ") {
			$pattern = $inside_block?'/^[^{};,:\n\r\t ]{2}$/':'/^[^{};,>+\n\r\t ]{2}$/';
			if (strlen($res) &&	preg_match($pattern, $res{strlen($res)-1}.$str{$i+1}))
				$res .= $current_char;
		} else $res .= $current_char;
		
		$i++;
	}
	if ($i<strlen($str) && preg_match('/[^\n\r\t ]/', $str{$i})) $res .= $str{$i};
	return $res;
}

function remove_js_comments($str) {
	$res = '';
	$maybe_regex = true;
	$i=0;
	$current_char = '';
	while ($i+1<strlen($str)) {
		if ($maybe_regex && $str{$i}=='/' && $str{$i+1}!='/' && $str{$i+1}!='*') {//regex detected
			if (strlen($res) && $res{strlen($res)-1} === '/') $res .= ' ';
			do {
				if ($str{$i} == '\\') {
					$res .= $str{$i++};
				} elseif ($str{$i} == '[') {
					do {
						if ($str{$i} == '\\') {
							$res .= $str{$i++};
						}
						$res .= $str{$i++};
					} while ($i<strlen($str) && $str{$i}!=']');
				}
				$res .= $str{$i++};
			} while ($i<strlen($str) && $str{$i}!='/');
			$res .= $str{$i++};
			$maybe_regex = false;
			continue;
		} elseif ($str{$i}=='"' || $str{$i}=="'") {//quoted string detected
			$quote = $str{$i};
			do {
				if ($str{$i} == '\\') {
					$res .= $str{$i++};
				}
				$res .= $str{$i++};
			} while ($i<strlen($str) && $str{$i}!=$quote);
			$res .= $str{$i++};
			continue;
		} elseif ($str{$i}.$str{$i+1}=='/*') {//multi-line comment detected
			$i+=3;
			while ($i<strlen($str) && $str{$i-1}.$str{$i}!='*/') $i++;
			if ($current_char == "\n") $str{$i} = "\n";
			else $str{$i} = ' ';
		} elseif ($str{$i}.$str{$i+1}=='//') {//single-line comment detected
			$i+=2;
			while ($i<strlen($str) && $str{$i}!="\n") $i++;
		}
		
		$LF_needed = false;
		if (preg_match('/[\n\r\t ]/', $str{$i})) {
			if (strlen($res) && preg_match('/[\n ]/', $res{strlen($res)-1})) {
				if ($res{strlen($res)-1} == "\n") $LF_needed = true;
				$res = substr($res, 0, -1);
			}
			while ($i+1<strlen($str) && preg_match('/[\n\r\t ]/', $str{$i+1})) {
				if (!$LF_needed && preg_match('/[\n\r]/', $str{$i})) $LF_needed = true;
				$i++;
			}
		}
		
		if (strlen($str) <= $i+1) break;
		
		$current_char = $str{$i};
		
		if ($LF_needed) $current_char = "\n";
		elseif ($current_char == "\t") $current_char = " ";
		elseif ($current_char == "\r") $current_char = "\n";
		
		// detect unnecessary white spaces
		if ($current_char == " ") {
			if (strlen($res) &&
				(
				preg_match('/^[^(){}[\]=+\-*\/%&|!><?:~^,;"\']{2}$/', $res{strlen($res)-1}.$str{$i+1}) ||
				preg_match('/^(\+\+)|(--)$/', $res{strlen($res)-1}.$str{$i+1}) // for example i+ ++j;
				)) $res .= $current_char;
		} elseif ($current_char == "\n") {
			if (strlen($res) &&
				(
				preg_match('/^[^({[=+\-*%&|!><?:~^,;\/][^)}\]=+\-*%&|><?:,;\/]$/', $res{strlen($res)-1}.$str{$i+1}) ||
				(strlen($res)>1 && preg_match('/^(\+\+)|(--)$/', $res{strlen($res)-2}.$res{strlen($res)-1})) ||
				preg_match('/^(\+\+)|(--)$/', $current_char.$str{$i+1}) ||
				preg_match('/^(\+\+)|(--)$/', $res{strlen($res)-1}.$str{$i+1})// || // for example i+ ++j;
				)) $res .= $current_char;
		} else $res .= $current_char;
		
		// if the next charachter be a slash, detects if it is a divide operator or start of a regex
		if (preg_match('/[({[=+\-*\/%&|!><?:~^,;]/', $current_char)) $maybe_regex = true;
		elseif (!preg_match('/[\n ]/', $current_char)) $maybe_regex = false;
		
		$i++;
	}
	if ($i<strlen($str) && preg_match('/[^\n\r\t ]/', $str{$i})) $res .= $str{$i};
	return $res;
}

$current_path = addslashes(realpath(getcwd()));

$file = @$_GET['file'] or debug_exit('missing file parameter');
if (!preg_match('/\.((js)|(css))$/i', $file, $file_type)) debug_exit($file." is not a javascript file.");
$file_type = strtolower($file_type[1]);

$file = constant('JSMART_'.strtoupper($file_type).'_DIR') . $file;

if (!file_exists($file)) debug_exit($file." not found.\\n(the path is relative to $current_path)");

$mtime = filemtime($file);

if ($mtime < filemtime('load.php')) $mtime = filemtime('load.php');
if ($mtime < filemtime('config.php')) $mtime = filemtime('config.php');
$mtimestr = gmdate(DateTime::RFC822, $mtime) . " GMT";

$headers = getallheaders();

header('Content-Type: '.($file_type=='js'?'application/x-javascript':'text/css').'; charset='.JSMART_CHARSET);

if (JSMART_CACHE_ENABLED) {	
	if (isset($headers['If-Modified-Since']) && $headers['If-Modified-Since'] == $mtimestr) 
		header_exit('304 Not Modified');
	
	header("Last-Modified: " . $mtimestr);
	header("Cache-Control: must-revalidate", false);
} else header_nocache();

$gzip_supported = 
	(isset($headers['Accept-Encoding']) &&
	array_search('gzip', array_map('trim', explode(',' , $headers['Accept-Encoding']))) !== false &&
	function_exists('gzencode'));

$cached_file = JSMART_CACHE_DIR.md5($file).'.'.$file_type;

if ($gzip_supported) {
	header("Content-Encoding: gzip");
	$cached_file .= '.gz';
}

if (JSMART_CACHE_ENABLED && file_exists($cached_file) && $mtime < filemtime($cached_file)) {
	@readfile($cached_file) or debug_exit("Cannot read file ($cached_file)\\n(the path is relative to $current_path)");
} else {
	$file_contents = @file_get_contents($file) or debug_exit("Cannot read file ($file)\\n(the path is relative to $current_path)");
	
	$remove_comments = 'remove_'.$file_type.'_comments';
	$file_contents = $remove_comments($file_contents);
	
	if ($gzip_supported) $file_contents = @gzencode($file_contents, 9) or debug_exit("Gzipping failed");
	
	$handle = @fopen($cached_file, 'w') or debug_exit("Cache dir is not writable or is not exist (".JSMART_CACHE_DIR.")\\n(the path is relative to $current_path)");
	fwrite($handle, $file_contents);
	fclose($handle);

	echo $file_contents;
}
?>