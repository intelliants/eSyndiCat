<?php
/******************************************************************************
 *
 *	 COMPANY: Intelliants LLC
 *	 PROJECT: eSyndiCat Directory Software
 *	 VERSION: 1.7 [Cushy]
 *	 LISENSE: http://www.esyndicat.com/license.html
 *	 http://www.esyndicat.com/
 *
 *	 This program is a limited version. It does not include the major part of 
 *	 the functionality that comes with the paid version. You can purchase the
 *	 full version here: http://www.esyndicat.com/order.html
 *
 *	 Any kind of using this software must agree to the eSyndiCat license.
 *
 *	 Link to eSyndiCat.com may not be removed from the software pages without
 *	 permission of the eSyndiCat respective owners.
 *
 *	 This copyright notice may not be removed from source code in any case.
 *
 *	 Useful links:
 *	 Installation Manual:	http://www.esyndicat.com/docs/install.html
 *	 eSyndiCat User Forums: http://www.esyndicat.com/forum/
 *	 eSyndiCat Helpdesk:	http://www.esyndicat.com/desk/
 *
 *	 Intelliants LLC
 *	 http://www.esyndicat.com
 *	 http://www.intelliants.com
 *
 ******************************************************************************/


/*
	These classes are defined mostly as namespace seperators
*/

class esynValidator
{
	/**
	* Validates URL
	*
	* @param str $url Url
	*
	* @return bool
	*/
	function isUrl($url)
	{
		return (bool)preg_match('/^https?:\/\/[a-zA-Z0-9-]{2,63}(?:\.[a-zA-Z0-9-]{2,})*(?::[0-9]{0,5})?(?:\/|$)\S*$/', $url);
	}

	/**
	* Validates email
	*
	* @param str $email Email
	*
	* @return bool
	*/
	function isEmail($email)
	{
		return (bool)preg_match('/^(?:(?:\"[^\"\f\n\r\t\v\b]+\")|(?:[\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+(?:\.[\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+)*))@(?:(?:\[(?:(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:[0-1]?[0-9]?[0-9]))\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:[0-1]?[0-9]?[0-9]))\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:[0-1]?[0-9]?[0-9]))\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:[0-1]?[0-9]?[0-9])))\])|(?:(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:[0-1]?[0-9]?[0-9]))\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:[0-1]?[0-9]?[0-9]))\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:[0-1]?[0-9]?[0-9]))\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:[0-1]?[0-9]?[0-9])))|(?:(?:(?:[A-Za-z0-9\-])+\.)+[A-Za-z\-]+))$/', $email);
	}

	/**
	* Checks if reciprocal link exists
	*
	* @param str $text (text where to search or URL where to get the text)
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

		// if there were no WWW. part in the URL then check again with
		if(!$res && esynValidator::isUrl($text) && false===strpos($text, "://www."))
		{
			$href = str_replace("://", "://www.", $href);
			$reciprocal = "#<a[^>]+href=(?:[\'\"]{0,1})(?:\s*)".preg_quote($href, "#")."(?:\s*)(?:[\'\"]{0,1})(?:[^>]*)>(?:.*)<\/a>#is";
			$res = preg_match($reciprocal, $content);
		}

		return $res;
	}
}

class esynSanitize
{
	function paranoid($string)
	{
		return preg_replace( "/[^a-z_0-9-]/i", "", $string );
	}

	/**
	* SQL
	*
	* @param	string	Databaes specific escape
	*
	* @return	string
	*/
	function sql($string)
	{
		// (this function requires database connection)
		return mysql_real_escape_string($string);
	}

	/**
	* Wrapper
	*
	* @param	string	Text to be made html-safe
	* @param	constant	Mode
	*
	* @return	string
	*/
	function html($string, $mode=ENT_QUOTES)
	{
		return htmlspecialchars($string, $mode);
	}

	/**
	* This short function used to convert special characters to their appropriate code - this function used in input fields as values
	*
	* @param str $str any string
	*
	* @return str
	*/
	function quote($str)
	{
		return str_replace(array(">","<", "'", "\""), array("&gt;","&lt;", "&#039;", "&quot;"), $str);
	}

	/**
	* Converts string to url valid string
	*
	* @param arr $params params (passed by Smarty)
	*
	* @return str
	*/
	function urlAcceptable($params)
	{
		// array passed from the Smarty
		if (is_array($params))
		{
			$str = $params['string'];
		}
		else
		{
			$str = $params;
		}

		$str = preg_replace('/[^a-z0-9]+/i', '-', $str);
		$str = preg_replace('/\-+/', '-', $str);
		$str = trim($str, '-');

		return $str;
	}

	// Filter against email header injection
	function headerInjectionFilter($name)
	{
		return preg_replace("/(?:%0A|%0D|\n+|\r+)(?:content-type:|to:|cc:|bcc:)/i", "", $name);
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
				$location = isset($headers['Location']) ? $headers['Location'] : $headers['location'];

				if (substr($location, 0, 4) != 'http')
				{
					$parsed_url = parse_url($url);

					$scheme = isset($parsed_url['scheme']) ? $parsed_url['scheme'] : $parsed_url['Scheme'];

					$host = isset($parsed_url['host']) ? $parsed_url['host'] : $parsed_url['Host'];

					$url = $scheme.'://'.$host.$location;
				}
				else
				{
					$url = $location;
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
				$pr_url = 'http://toolbarqueries.google.com/tbr?client=navclient-auto&ch=';
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

	/**
	* Checks link and returns its header
	*
	* @param str $aUrl page url
	*
	* @return int
	*/
	function getListingHeader($url)
	{
		if (empty($url) || 'http://' == $url)
		{
			return 0;
		}

		if (!esynValidator::isUrl($url))
		{
			return 0;
		}

		$redirect = false;
		
		do
		{
			$listing_header = 1;
			
			$headers = esynUtil::getPageHeaders($url);

			if (!empty($headers))
			{
				$listing_header = (int)$headers['Status'];
			}
		
			$redirect = in_array((int)$listing_header, array(301, 302), true);

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

		if (in_array((int)$listing_header, array (200, 403, 405), true))
		{
			return $listing_header;
		}
		
		return 0;
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
			// set time limit
			set_time_limit(60);

			// Get content via cURL module
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $aUrl);
			curl_setopt($ch, CURLOPT_HEADER, true);
			curl_setopt($ch, CURLOPT_NOBODY, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);

			$content = @curl_exec($ch);

			/*if (!$content)
			{
				echo '<b>CURL error&nbsp;:&nbsp;</b>';
				echo curl_error($ch);
				echo '<br />';
			}*/
			curl_close($ch);
		}
		elseif (ini_get('allow_url_fopen'))
		{
			// Get content via fsockopen
			$parsed_url = parse_url($aUrl);

			// Get host to connect to
			$host = $parsed_url['host'];

			$port = 80;

			if ('https' == $parsed_url['scheme'])
			{
				$port = 443;
				$host = 'ssl://' . $host;
			}

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
				$f = @fsockopen($host, $port, $errno, $errstr, 5);
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
				$stream = @fopen($host, "r");
				$data = stream_get_meta_data($stream);
				$content = $data['wrapper_data'];
				unset($data);
				fclose($stream);
			}
		}
		
		// Parse content into headers and return
		if (!empty($content))
		{
			$retval = array();
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
		$retval = null;
		$user_agent = 'eSyndiCat Bot';

		if (extension_loaded('curl'))
		{
			// Get page contents via cURL module
			set_time_limit(60);
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
			ini_set('user_agent', $user_agent);

			// Context support was added with PHP 5.0.0.
			//$ctx = stream_context_create(array('http' => array('timeout' => 3)));

			$retval = file_get_contents($aUrl, false/*, $ctx*/);

			ini_restore('user_agent');
		}
		else
		{
			return false;
		}

		return $retval;
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
		if (0 == $_FILES[$aName]['error'] && is_uploaded_file($_FILES[$aName]['tmp_name']))
		{
			if (move_uploaded_file($_FILES[$aName]['tmp_name'], $aDest))
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

	// Invoked when access denied
	function accessDenied()
	{
		header("HTTP/1.1 403 Forbidden");
		die($GLOBALS['esynI18N']['access_denied']);
	}

	function go2($url)
	{
		if (!headers_sent($file,$line))
		{
			header("Location: ".$url);
			exit;
		}
		else
		{
			trigger_error("Headers already sent in $file:$line", E_USER_ERROR);
		}
	}

	// reload current page
	function reload($params=false)
	{
		global $esynConfig;

		$url = $esynConfig->getConfig("base").ltrim($_SERVER['SCRIPT_NAME'], "/");

		if(!empty($_GET))
		{
			$url .= "?";
		}
		foreach($_GET as $k=>$v)
		{
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
		if (preg_match('/^(?:http|https|ftp):\/\/((?:[A-Z0-9][A-Z0-9_-]*)(?:\.[A-Z0-9][A-Z0-9_-]*)+)(:(\d+))?/i', $aUrl,$m))
		{
			return $m[1];
		}

		return false;
	}

	/**
	* Returns all parent categories
	*
	* @param int $aCategory category id
	* @param array $aBreadcrumb breadcrumb array
	*
	* @return array
	*/
	function getBreadcrumb($aCategory, &$aBreadcrumb)
	{
		global $eSyndiCat;

		static $times = 0;

		if($times > 75)
		{
			trigger_error("Recursion in esynUtil::getBreadcrumb() in file: ".__FILE__." on line: ".__LINE__, E_USER_ERROR);
		}

		$eSyndiCat->setTable("categories");
		$category = $eSyndiCat->row("`parent_id`,`title`,`page_title`,`path`, `no_follow`", "`id` = '{$aCategory}'");

		if ('-1' != $category['parent_id'])
		{
			$aBreadcrumb[] = array ('id' => $aCategory, 'title' => $category['title'], 'page_title' => $category['page_title'], 'path' => $category['path'], 'no_follow' => $category['no_follow']);

			$times++;
			$eSyndiCat->resetTable();
			esynUtil::getBreadcrumb($category['parent_id'], $aBreadcrumb);
		}
		else
		{
			$times = 0;
		}
	}

	/**
	 * convertStr
	 *
	 * Converts string to url valid string
	 *
	 * @param arr $aParams text string
	 * @access public
	 * @return str
	 */
	function convertStr($aParams)
	{
		require_once(ESYN_CLASSES.'esynUtf8.php');

		esynUtf8::loadUTF8Core();
		esynUtf8::loadUTF8Util('ascii', 'utf8_to_ascii');

		if(!utf8_is_ascii($aParams['string']))
		{
			$aParams['string'] = utf8_to_ascii($aParams['string']);
		}

		$aParams['string'] = preg_replace('/[^a-z0-9]+/i', '-', $aParams['string']);
		$aParams['string'] = preg_replace('/\-+/', '-', $aParams['string']);
		$aParams['string'] = trim($aParams['string'], '-');

		return empty($aParams['string']) ? "listing" : $aParams['string'];
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

class esynMessages
{
	function getMessages($type = 'info')
	{
		if(array_key_exists($type, $_SESSION['messages']))
		{
			unset($_SESSION['messages'][$type]);

			return $_SESSION['messages'][$type];
		}

		unset($_SESSION['messages']);

		return $_SESSION['messages'];
	}

	function setMessages($msg, $type = 'info')
	{
		if(strlen($msg))
		{
			$_SESSION['messages'][$type][] = $msg;

			return true;
		}

		return false;
	}
}

/**
* Deep strip slashing of arrays more then one level
*
* @value arr
*
* @return arr
*/
function stripslashes_deep($value)
{
	   $value = is_array($value) ?
				   array_map('stripslashes_deep', $value) :
				   stripslashes($value);

	   return $value;
}

