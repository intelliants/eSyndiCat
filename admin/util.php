<?php
//##copyright##

class esynMessages
{
	function getMessages($type = 'info')
	{
		if(!empty($_SESSION['messages']))
		{
			$ret = $_SESSION['messages'];

			unset($_SESSION['messages']);

			return $ret;
		}
		else
		{
			return array();
		}
	}

	function setMessage($msg, $error = false)
	{
		$valid_types = array('error', 'notification', 'alert');

		if(is_bool($error))
		{
			$msg_type = $error ? 'error' : 'notification';
		}
		else
		{
			$msg_type = $error;
		}

		$msg_type = in_array($msg_type, $valid_types) ? $msg_type : 'notification';

		if(!empty($msg))
		{
			$_SESSION['messages']['type'] = $msg_type;
			$_SESSION['messages']['msg'] = $msg;

			return true;
		}

		return false;
	}
}

class esynUtil
{
	function getPageRank($url)
	{
		do
		{
			$status = 1;
			
			$headers = esynUtil::getPageHeaders($url);
			
			if (!empty($headers))
			{
				$status = (int)$headers['Status'];
			}
			
			$redirect = in_array($status, array (301, 302), true);
			
			if ($redirect)
			{
				if (substr($headers['Location'], 0, 4) != 'http')
				{
					$parsed_url = parse_url($url);
					$url = $parsed_url['scheme'].'://'.$parsed_url['host'].$headers['Location'];
				}
				else
				{
					$url = $headers['Location'];
				}
			}
		}
		while ($redirect);

		require_once(ESYN_INCLUDES."checksum.inc.php");

		if (in_array($status, array (200, 405, 403), true))
		{
			$checksum = esynGetChecksum($url);;

			if (preg_match('/^[\d\-]+$/', $checksum))
			{
				$pr_url = 'http://toolbarqueries.google.com/search?client=navclient-auto&ch=';
				$pr_url .= $checksum.'&ie=UTF-8&oe=UTF-8&features=Rank&q=info:'.urlencode($url);

				$pr_content = trim(esynUtil::getPageContent($pr_url));
				if (preg_match('/^Rank_\d{1}:\d{1}:(\d+)$/u', $pr_content, $matches))
				{
					return (int)$matches[1];
				}
			}
		}

		return false;
	}

	// Generates hidden form element
	function preventCsrf()
	{
		$token = esynUtil::getToken();

		return '<input type="hidden" name="prevent_csrf" value="'.$token.'" />';
	}

	function getToken()
	{
		// support several post forms in the page
		static $calledTimes = 0;
		$_SESSION['prevent_csrf'][] = $token = esynUtil::getNewToken();
		$calledTimes++;

		return $token;
	}

	// Invoked when csrf attttt detected
	function csrfAttack()
	{
		esynUtil::logout();
		die("Suspicous referrer. Possible CSRF attack prevented.
			Wikipedia contains nice article:
				<a href=\"http://en.wikipedia.org/wiki/Cross-site_request_forgery\">Learn about CSRF attack</a>");
	}

	function logout()
	{
		unset($_SESSION['admin_name'], $_SESSION['admin_pwd']);
	}

	// Invoked when access denied
	function accessDenied()
	{
		global $esynI18N;

		$esynI18N['access_denied'] .= '<br /><br /><a href="javascript:history.go(-1)">&laquo; Back</a>';
		$content = esyn_display_error("Forbidden | 1 | {$esynI18N['access_denied']}");
		$content = preg_replace('/<p class="solution">.*<\/p>/i', ' ', $content);

		echo $content;

		exit;
	}

	// Return keys for search in two dim arrays..
	function arraySearch($needle, $haystack)
  	{
		$value = false;

		if(!is_scalar($needle))
		{
			return false;
		}
		if(is_array($haystack))
		{
	    	foreach($haystack as $k=>$temp)
	    	{
		   		$search = array_search($needle, $temp);
				if (strlen($search) > 0 && $search >= 0)
				{
		    		$value[0] = $k;
					$value[1] = $search;
		    	}
		  	}
		}

		return $value;
 	}

	function inArray($needle, $haystack)
	{
		if(is_array($haystack))
		{
			foreach($haystack as $item)
			{
				if(is_array($item))
				{
					$ret = esynUtil::inArray($needle, $item);
				}
				else
				{
					$ret = ($item == $needle);
				}
			}

			return $ret;
		}

		return false;
	}


	/*
	 This function can(should) be used by the array_walk and array_walk_recursive functions
	*/
	function filenameEscape(&$item,$key)
	{
		$item = str_replace(array("`","~","/","\\"), "", $item);
	}

	/*
		Proxy function to smarty
	*/
	function clearCache($tpl=null, $cacheName=null)
	{
		if(!isset($GLOBALS['esynSmarty']) || !is_object($GLOBALS['esynSmarty']))
		{
			esynLoadClass("Smarty");
			$GLOBALS['esynSmarty'] = new esynSmarty;
		}
		if(empty($tpl))
		{
			$GLOBALS['esynSmarty']->clear_all_cache();
		}
		else
		{
			$GLOBALS['esynSmarty']->clear_cache($tpl, $cacheName);
		}
	}

	function go2($url)
	{
		if(empty($url))
		{
			die("Fatal error: empty url param for function 'goto'");
		}
		if(!headers_sent($file,$line))
		{
			header("Location: ".$url);
			exit;
		}
		else
		{
			die(basename($file).":".$line);
		}
	}

	// reload current page
	function reload($params=false)
	{
		$url = ESYN_BASE_URL.ltrim($_SERVER['SCRIPT_NAME'], "/");

		if(is_array($params))
		{
			foreach($params as $k=>$v)
			{
				// remove key
				if($v === null)
				{
					unset($params[$k]);
					if(array_key_exists($k, $_GET))
					{
						unset($_GET[$k]);
					}
				}
				elseif(array_key_exists($k, $_GET)) // set new value
				{
					$_GET[$k] = $v;
					unset($params[$k]);
				}
			}
		}
		if(!empty($_GET) || !empty($params))
		{
			$url .= "?";
		}
		foreach($_GET as $k=>$v)
		{
			// Unfort. At this time we delete an individual items using GET requests instead of POST
			// so when reloading we should skip delete action
			if($k == 'action' && $v == 'delete')
			{
				continue;
			}
			$url .= $k."=".urlencode($v)."&";
		}
		if($params)
		{
			if(is_array($params))
			{
				foreach($params as $k=>$v)
				{
					$url .= $k."=".urlencode($v)."&";
				}
			}
			else
			{
				$url .= $params;
			}
		}
		$url = rtrim($url, "&");
		esynUtil::go2($url);
	}

	/**
	* Returns domain name by a given URL
	*
	* @param str $aUrl url
	*
	* @return str
	*/
	function getDomain($aUrl = '')
	{
		if (preg_match('/^(http|https|ftp):\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)(:(\d+))?/i', $aUrl))
		{
			$domain = parse_url($aUrl);

			return $domain['host'];
		}
	}

	/**
	* Uploads file to server
	*
	* @param str $aName index into $_FILES array
	* @param str $aDest destination file name
	*
	* @return bool true if file uploaded, false otherwise
	*/
	function upload($aName, $aDest)
	{
		$ret = false;
		// Check upload error
		if (0 == $_FILES[$aName]['error'])
		{
			$src = $_FILES[$aName]['tmp_name'];
			if (is_uploaded_file($src) && move_uploaded_file($src, $aDest))
			{
				$ret = true;
			}
		}

		return $ret;
	}

	/**
	* Returns 10-character unique alphanum string
	*
	* @return string
	*/
	function getNewToken($size=10)
	{
		$ret = md5(uniqid(rand(), true));
		$ret = substr($ret, 0, $size);

		return $ret;
	}

	/**
	* Checks link and returns its header
	*
	* @param str $aUrl page url
	*
	* @return int
	*/
	function getListingHeader($aUrl)
	{
		$h = esynUtil::getPageHeaders($aUrl);
		if($h)
		{
			return $h['Status'];
		}

		return 0;
	}

	function basicAuth()
	{
		eval(base64_decode('Z2xvYmFsICRlc2Nfcm10X3JzdDsNCg0KJG1zZyA9ICc8ZGl2IGNsYXNzPSJtc2dfZXJyb3IiPntlcnJvcn08L2Rpdj4nOw0KDQokZm9ybSA9ICcNCjxmb3JtIG1ldGhvZD0icG9zdCIgc3R5bGU9Im1hcmdpbjogMDsgcGFkZGluZzogMDsiPg0KCTx0YWJsZSB3aWR0aD0iMTAwJSIgY2VsbHBhZGRpbmc9IjUiPg0KCTx0cj4NCgkJPHRkPkFjY291bnQgVXNlcm5hbWU6PC90ZD4NCgkJPHRkPjxpbnB1dCB0eXBlPSJ0ZXh0IiBuYW1lPSJfYXR5IiAvPjwvdGQ+DQoJPC90cj4NCgk8dHI+DQoJCTx0ZD5BY2NvdW50IExpY2Vuc2UgSUQ6PC90ZD4NCgkJPHRkPjxpbnB1dCB0eXBlPSJ0ZXh0IiBuYW1lPSJfYWNoa3ljaCIgLz48L3RkPg0KCTwvdHI+DQoJPHRyPg0KCQk8dGQgY29sc3Bhbj0iMiI+PGlucHV0IGNsYXNzPSJjb21tb24iIHR5cGU9InN1Ym1pdCIgdmFsdWU9IiBTdWJtaXQgIiAvPjwvdGQ+DQoJPC90cj4NCgk8L3RhYmxlPg0KPC9mb3JtPic7DQoNCiRlcnJvcl9tc2cgPSAnJzsNCg0KaWYoaXNzZXQoJGVzY19ybXRfcnN0Wydtc2cnXSkgJiYgIWVtcHR5KCRlc2Nfcm10X3JzdFsnbXNnJ10pKQ0Kew0KCSRlcnJvcl9tc2cgPSBzdHJfcmVwbGFjZSgne2Vycm9yfScsICRlc2Nfcm10X3JzdFsnbXNnJ10sICRtc2cpOw0KCSRmb3JtID0gJGVycm9yX21zZyAuICRmb3JtOw0KfQ0KDQokc2NyaXB0X3BhdGggPSBzdHJfcmVwbGFjZSgnaW5zdGFsbCcsICcnLCBkaXJuYW1lKCRfU0VSVkVSWydTQ1JJUFRfTkFNRSddKSk7DQoNCiRjb250ZW50ID0gZmlsZV9nZXRfY29udGVudHMoJy4uJy5ESVJFQ1RPUllfU0VQQVJBVE9SLidpbmNsdWRlcycuRElSRUNUT1JZX1NFUEFSQVRPUi4nY29tbW9uJy5ESVJFQ1RPUllfU0VQQVJBVE9SLidlcnJvcl9oYW5kbGVyLmh0bWwnKTsNCiRzZWFyY2ggPSBhcnJheSgne3RpdGxlfScsICd7YmFzZV91cmx9JywgJ3tlcnJvcl90aXRsZX0nLCAne2Vycm9yX2Rlc2NyaXB0aW9ufScsICd7ZXJyb3Jfc29sdXRpb25zfScsICd7ZXJyb3Jfa2V5fScsICd7YWRkaXRpb25hbH0nKTsNCiRyZXBsYWNlID0gYXJyYXkoJ0RvbWFpbiBWZXJpZmljYXRpb24gU3lzdGVtIDo6IGVTeW5kaUNhdCBEaXJlY3RvcnkgU29mdHdhcmUgJy5FU1lOX1ZFUlNJT04sICRzY3JpcHRfcGF0aCwgJ2VTeW5kaUNhdCBMaWNlbnNlIFZlcmlmaWNhdGlvbicsICdQbGVhc2UgaW5wdXQgeW91ciBlc3luZGljYXQuY29tIGFjY291bnQgdXNlcm5hbWUgYW5kIGFjY291bnQgbGljZW5zZSBJRC4nLCAkZm9ybSwgJ2xpY2Vuc2VfdmFsaWRhdGlvbicpOw0KJGNvbnRlbnQgPSBzdHJfcmVwbGFjZSgkc2VhcmNoLCAkcmVwbGFjZSwgJGNvbnRlbnQpOw0KZWNobyAkY29udGVudDsNCmRpZSgpOw=='));
	}

	/**
	* Returns array of page headers
	*
	* @param string $aUrl page url
	*
	* @return mixed array on success, null on failure
	*/
	function getPageHeaders($aUrl)
	{
		$user_agent = 'eSyndiCat Bot';

		if (empty($aUrl))
		{
			return null;
		}

		$content = '';

		// Connect to the remote web server
		// and get headers content

		if (extension_loaded('curl'))
		{
			// Get content via cURL module
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $aUrl);
			curl_setopt($ch, CURLOPT_HEADER, 1);
			curl_setopt($ch, CURLOPT_NOBODY, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
			$content = @curl_exec($ch);
			curl_close($ch);
		}
		elseif (ini_get('allow_url_fopen'))
		{
			// Get content via fsockopen
			$parsed_url = parse_url($aUrl);

			// Get host to connect to
			$host = $parsed_url['host'];

			// Get path to insert into HTTP HEAD request
			$path = empty($parsed_url['path']) ? '/' : $parsed_url['path'];
			$path .= empty($parsed_url['query']) ? '' : '?'.$parsed_url['query'];
			$path .= empty($parsed_url['fragment']) ? '' : '#'.$parsed_url['fragment'];

 			if(function_exists("fsockopen"))
 			{
				// Build request
				$request = 'GET '.$path.' HTTP/1.0'."\r\n";
				$request .= 'Host: '.$host."\r\n";
				$request .= 'User-Agent: '.$user_agent."\r\n";
				$request .= 'Connection: Close'."\r\n\r\n";

				// Get headers via system calls
				$f = @fsockopen($host, 80, $errno, $errstr, 5);
				if ($f)
				{
					$retval = array ();

					// Send request
					fwrite($f, $request);
					// Read response
					while (!feof($f))
					{
						$content .= fgets($f, 2048);
					}
					fclose($f);
				}
			}
			else
			{
				$stream = fopen($host, "r");
				$data = stream_get_meta_data($stream);
				$content = $data['wrapper_data'];
				unset($data);
				fclose($stream);
			}

		}

		// Parse content into headers and return
		if (!empty($content))
		{
			$retval = array ();
			// stream_get_meta_data returns array
			if(is_string($content))
			{
				$content = str_replace("\r\n", "\n", $content);
			}

			$temp = explode("\n", $content);
			foreach ($temp as $entry)
			{
				if (preg_match('/^HTTP\/[\d\.]+\s(\d+).*$/i', $entry, $matches))
				{
					$retval['Status'] = $matches[1];
				}
				else if (preg_match('/^(.*):\s(.*)$/i', $entry, $matches))
				{
					$retval[$matches[1]] = $matches[2];
				}
			}
			return $retval;
		}
		else
		{
			return null;
		}
	}

	/**
	* Returns page web content
	*
	* @param string $aUrl url of the page
	*
	*
	* @return mixed string on success, false on failure
	*/
	function getPageContent($aUrl)
	{
		$retval = false;
		$user_agent = 'eSyndiCat Bot';

		if (extension_loaded('curl'))
		{
			// Get page contents via cURL module
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $aUrl);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
			curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);

			$retval = curl_exec($ch);
			curl_close($ch);
		}
		elseif (ini_get('allow_url_fopen'))
		{
			$ua = ini_get('user_agent');
			ini_set('user_agent', $user_agent);
			$retval = file_get_contents($aUrl);
			ini_set('user_agent', $ua);
		}
		else
		{
			trigger_error("Impossible to get remote URLs (turn on 'allow url fopen' or enable 'CURL' extension) ", E_USER_WARNING);
			return false;
		}

		return $retval;
	}

	function checkAccess()
	{
		global $esynAdmin;
		global $currentAdmin;

		$esynAdmin->setTable("admin_pages");
		$esynAcos = $esynAdmin->keyvalue("`aco`, `title`", "1=1 GROUP BY `aco`");
		$esynAdmin->resetTable();

		if(defined("ESYN_REALM") && !$currentAdmin['super'])
		{
			if(!in_array(ESYN_REALM, $currentAdmin['permissions']+$esynAcos, true))
			{
				esynUtil::accessDenied();
			}
		}
	}

	/**
	* Converts string to url valid string
	*
	* @param arr $aParams text string
	*
	* @return str
	*/
	function convertStr($str)
	{
		require_once(ESYN_CLASSES.'esynUtf8.php');

		esynUtf8::loadUTF8Core();
		esynUtf8::loadUTF8Util('ascii', 'utf8_to_ascii');

		if(!utf8_is_ascii($str))
		{
			$str = utf8_to_ascii($str);
		}
		
		$str = preg_replace('/[^a-z0-9]+/i', '-', $str);
		$str = preg_replace('/\-+/', '-', $str);
		$str = trim($str, '-');

		return empty($str) ? "listing" : $str;
	}

	function dateFormat($date)
	{
		global $esynConfig;

		return strftime($esynConfig->getConfig('date_format'), strtotime($date));
	}

	/**
	 * Recursive array search keys or values
	 *
	 * @param str $needle
	 * @param arr $haystack
	 * @param arr $nodes
	 *
	 * @return array of all found keys and values ($key=>$value) or false elsewise
	 */
    function ArraySearchRecursive($needle, $haystack, $nodes=array())
    {
        foreach ($haystack as $key1=>$value1)
        {
            if (is_array($value1))
            {
                $nodes = esynUtil::ArraySearchRecursive($needle, $value1, $nodes);
            }
            elseif (($key1 == $needle) or ($value1 == $needle))
            {
                $nodes[] = array($key1=>$value1);
            }
        }
        if (empty($nodes))
        {
            return false;
        }else{
            return $nodes;
        }
    }

	/*
	 * Is PHP running from other account?
	 */
	function checkUid()
	{
		$ret = false;
		if (0 === strpos($_SERVER['DOCUMENT_ROOT'], '/'))
		{
			$ret = getmyuid() != posix_getuid();
		}
		return $ret;
	}

	/*
	 * Create directory recursively
	 *
	 * @param string $aDirName path
	 */
	function mkdir($aDirName)
	{
		$cwd = getcwd();
		$path = false === strpos($aDirName, ESYN_HOME) ? $aDirName : substr($aDirName, strlen(ESYN_HOME) - 1);
		$path = explode(ESYN_DS, $path);
		chdir(ESYN_HOME);

		foreach ($path as $p)
		{
			if (empty($p)) continue;
			if (file_exists($p))
			{
				chdir( $p );
				continue;
			}

			if ( is_writeable( getcwd() ) )
			{
				mkdir( $p );
				esynUtil::checkUid() && chmod( $p, 0777 );
			}
			else
			{
				$e  = 'Directory Creation Error | tmp_dir_permissions | ';
				$e .= 'Please set writable permission for ' . getcwd() . ' directory.';
				trigger_error($e, E_USER_ERROR);
			}
		}
		chdir( $cwd );
	}
}

class esynValidator
{
	/**
	* Checks if reciprocal link exists
	*
	* @param str $url (text where to search or URL where to get the text)
	* @param str $href
	*
	* @return int
	*/
	function hasUrl($text, $href='')
	{
		$reciprocal = "#<a[^>]+href=(?:[\'\"]{0,1})(?:\s*)".preg_quote($href, "#")."(?:\s*)(?:[\'\"]{0,1})(?:[^>]*)>(?:.*)<\/a>#is";

		$res = 0;

		$content = esynValidator::isUrl($text) ? esynUtil::getPageContent($text) : $text;
		if ($content)
		{
			$res = preg_match($reciprocal, $content);
		}

		// if there were no WWW. part in the urlthen check again
		if(!$res && esynValidator::isUrl($text) && false===strpos($text, "://www."))
		{
			$href = str_replace("://", "://www.", $href);
			$reciprocal = "#<a[^>]+href=(?:[\'\"]{0,1})(?:\s*)".preg_quote($href, "#")."(?:\s*)(?:[\'\"]{0,1})(?:[^>]*)>(?:.*)<\/a>#is";
			$res = preg_match($reciprocal, $content);
		}

		return $res;
	}


	/**
	* Validates URL (simple yet)
	*
	* @param str $aUrl Url
	*
	* @return bool
	*/
	function isUrl($aUrl)
	{
		$pattern = '/^(([\w]+:)?\/\/)?(([\d\w]|%[a-fA-f\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?@)?([\d\w][-\d\w]{0,253}[\d\w]\.)+[\w]{2,4}(:[\d]+)?(\/([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)*(\?(&amp;?([-+_~.\d\w]|%[a-fA-f\d]{2,2})=?)*)?(#([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)?/';

		return (bool)preg_match($pattern, $aUrl);

	}

	/**
	* Validates email
	*
	* @param str $aEmail email
	*
	* @return bool
	*/
	function isEmail($aEmail)
	{
		return (bool)preg_match('/^(?:(?:\"[^\"\f\n\r\t\v\b]+\")|(?:[\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+(?:\.[\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+)*))@(?:(?:\[(?:(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:[0-1]?[0-9]?[0-9]))\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:[0-1]?[0-9]?[0-9]))\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:[0-1]?[0-9]?[0-9]))\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:[0-1]?[0-9]?[0-9])))\])|(?:(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:[0-1]?[0-9]?[0-9]))\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:[0-1]?[0-9]?[0-9]))\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:[0-1]?[0-9]?[0-9]))\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:[0-1]?[0-9]?[0-9])))|(?:(?:(?:[A-Za-z0-9\-])+\.)+[A-Za-z\-]+))$/', $aEmail);
	}

	/*
		Used to validate status field from user input
	*/
	function isStatus($status)
	{
		return in_array($status, array("approval", "active", "suspended", "banned"));
	}
}

class esynSanitize
{
	function paranoid($string)
	{
		return preg_replace( "/[^a-z_0-9-.]/i", "", $string );
	}

	/*
	*	Notice: this function requires mysql connection
	*/
	function sql($string)
	{
		return mysql_real_escape_string($string);
	}

	function html($string,$mode=ENT_QUOTES)
	{
		return htmlspecialchars(trim($string), $mode);
	}

	function notags($string)
	{
		return str_replace(array(">","<"), array("&gt;", "&lt;"), $string);
	}

	function striptags($string)
	{
		return strip_tags($string);
	}

	/**
	 * applyFn
	 *
	 * Apply any function of esynSanitize class to multi-dimension array
	 * The array should be multi-dimension.
	 * The fn should be valid name of function or will be returned false otherwise.
	 *
	 * @param array $array multi-dimension array
	 * @param string $fn
	 * @access public
	 * @return void
	 */
	function applyFn($array, $fn, $keys = array())
	{
		$validFn = array('paranoid', 'sql', 'html', 'notags', 'striptags');

		if(!is_array($array))
		{
			return false;
		}

		if(empty($array))
		{
			return false;
		}

		if(!in_array($fn, $validFn))
		{
			return false;
		}

		foreach($array as $key => $value)
		{
			if(empty($keys))
			{
				$array[$key] = array_map(array("esynSanitize", $fn), $array[$key]);
			}
			else
			{
				foreach($keys as $k)
				{
					$array[$key][$k] = esynSanitize::$fn($array[$key][$k]);
				}
			}
		}

		return $array;
	}
}

class esynView
{
	/**
	* Returns text value for boolean values
	*
	* @param bool $aValue if 1 - returns true, 0 - false
	*
	* @return str
	*/
	function getBoolStr($aValue = 0)
	{
		global $esynI18N;

		return $aValue ? $esynI18N['yes'] : $esynI18N['no'];
	}

	/**
	* Returns all parent categories
	*
	* @param int $aCategory category id
	* @param array $aBreadcrumb breadcrumb array
	*
	* @return array
	*/
	function getBreadcrumb($aCategory, &$aBreadcrumb, $root = false)
	{
		global $esynAdmin;

		static $times = 0;

		if($times > 75)
		{
			trigger_error("Recursion in esynUtil::getBreadcrumb() in file: ".__FILE__." on line: ".__LINE__, E_USER_ERROR);
		}

		$esynAdmin->setTable("categories");

		$category = $esynAdmin->row("`parent_id`,`title`,`page_title`,`path`, `no_follow`", "`id` = '{$aCategory}'");

		if ('-1' == $category['parent_id'])
		{
			if($root)
			{
				$aBreadcrumb[] = array(
					'id'			=> $aCategory,
					'title'			=> $category['title'],
					'page_title'	=> $category['page_title'],
					'path'			=> $category['path'],
					'no_follow'		=> $category['no_follow']
				);
			}

			$times = 0;
		}
		else
		{
			$aBreadcrumb[] = array(
				'id'			=> $aCategory,
				'title'			=> $category['title'],
				'page_title'	=> $category['page_title'],
				'path'			=> $category['path'],
				'no_follow'		=> $category['no_follow']
			);

			$times++;
			$esynAdmin->resetTable();
			esynView::getBreadcrumb($category['parent_id'], $aBreadcrumb, $root);
		}
	}

	function langList($select = false, $name = '')
	{
		if(!$select)
		{
			//default
			$select = ESYN_LANGUAGE;
		}
		// if only 1 language then make disabled
		$x = count($GLOBALS['langs']) == 1 ? " disabled=\"disabled\"" : "";
		$s = "<select id=\"language_list_".$name."\" name=\"".$name."\"$x>";
		foreach($GLOBALS['langs'] as $code=>$l)
		{
			$s .= "<option value=\"".$code."\"";
			if($code == $select)
			{
				$s .= " selected=\"selected\"";
			}
			$s .= ">".$l."</option>";
		}
		$s .= "</select>";

		return $s;
	}

	/**
	* Prints breadcrumb
	*
	* @param int $aCategory category id
	* @param str $aCaption page title for element
	* @param str $aUrl page url for breadcrumb element
	*/
	function printBreadcrumb($aCategory = '', $aBc = '')
	{
		global $esynI18N;

		$str = '';

		$chain = (count($aBc) > 1) ? count($aBc) : 0;

		if ($aBc[0] || $aCategory)
		{
			$str .= '<div class="breadcrumb">';
			$str .= "<a href=\"index.php\">{$esynI18N['admin_panel']}</a>";

			if ($aCategory)
			{
				$breadcrumb = array();

				$str .= '<div class="arrow"></div>';
				esynView::getBreadcrumb($aCategory, $breadcrumb);

				$breadcrumb = array_reverse($breadcrumb);

				$size = count($breadcrumb);
				$cnt = 0;

				$str .= (0 == $size) ? "<strong>".esynSanitize::html($aBc[0]['title'])."</strong>" : "<a href=\"{$aBc[0]['url']}\">".esynSanitize::html($aBc[0]['title'])."</a>";

				// don't make link in browse category section
				if(false !== strpos($_SERVER['QUERY_STRING'], "browse"))
				{
					$cnt++;
				}

				foreach($breadcrumb as $item)
				{
					$item['title'] = esynSanitize::html($item['title']);

					if ($size == $cnt)
					{
						$str .= "<div class=\"arrow\"></div><strong>{$item['title']}</strong>";
					}
					else
					{
						$str .= "<div class=\"arrow\"></div><a href=\"{$aBc[0]['url']}&amp;id={$item['id']}\">{$item['title']}</a>";
					}
					$cnt++;
				}

				if ($chain)
				{
					$cnt = 1;
					foreach($aBc as $k=>$item)
					{
						if($k < $cnt)
						{
							continue;
						}
						if ($chain-1 == $k)
						{
							$str .= "<div class=\"arrow\"></div><strong>".esynSanitize::html($item['title'])."</strong>";
						}
						else
						{
							if (isset($item['url']))
							{
								$str .= "<div class=\"arrow\"></div><a href=\"{$item['url']}\">".esynSanitize::html($item['title'])."</a>";
							}
							else
							{
								$str .= "<div class=\"arrow\"></div>".esynSanitize::html($item['title']);
							}
						}
						$cnt++;
					}
				}
			}
			else
			{
				if ($chain)
				{
					$size = count($aBc);
					$cnt = 1;
					foreach($aBc as $item)
					{
						if ($size == $cnt)
						{
							$str .= "<div class=\"arrow\"></div><strong>".esynSanitize::html($item['title'])."</strong>";
						}
						else
						{
							$str .= "<div class=\"arrow\"></div><a href=\"{$item['url']}\">".esynSanitize::html($item['title'])."</a>";
						}
						$cnt++;
					}
				}
				else
				{
					$str .= "<div class=\"arrow\"></div>".$aBc[0]['title'];
				}
			}

			$str .= '</div>';
		}

		return $str;
	}
}

