<?php 
/**
 * User Agent Class
 *
 * Identifies the platform, browser, robot, or mobile devise of the browsing agent
 *
 * @package		Kohana
 * @subpackage	Libraries
 * @category	User Agent
 * @author		Rick Ellis
 * @link		http://kohanaphp.com/user_guide/libraries/user_agent.html
 */
 
class _speedy_User_agent {

	var $agent		= NULL;
	
	var $is_browser	= FALSE;
	var $is_robot	= FALSE;
	var $is_mobile	= FALSE;

	var $languages	= array();
	var $charsets	= array();
	
	var $platforms	= array();
	var $browsers	= array();
	var $mobiles	= array();
	var $robots		= array();
	
	var $platform	= '';
	var $browser	= '';
	var $version	= '';
	var $mobile		= '';
	var $robot		= '';
	
	/**
	 * Constructor
	 *
	 * Sets the User Agent and runs the compilation routine
	 *
	 * @access	public
	 * @return	void
	 */		
	function _speedy_User_agent()
	{
		if (isset($_SERVER['HTTP_USER_AGENT']))
		{
			$this->agent = trim($_SERVER['HTTP_USER_AGENT']);
		}
		
		if ( ! is_null($this->agent))
		{
			if ($this->_load_agent_file())
			{
				$this->_compile_data();
			}
		}
		
		//log_message('debug', "Table Class Initialized");
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Compile the User Agent Data
	 *
	 * @access	private
	 * @return	bool
	 */		
	function _load_agent_file()
	{
		
	/*
	| -------------------------------------------------------------------
	| USER AGENT TYPES
	| -------------------------------------------------------------------
	| This file contains four arrays of user agent data.  It is used by the
	| User Agent Class to help identify browser, platform, robot, and
	| mobile device data.  The array keys are used to identify the device
	| and the array values are used to set the actual name of the item.
	|
	*/
	
	$platforms = array(
						'windows nt 6.0' => 'Windows Vista',
						'windows nt 5.2' => 'Windows 2003',
						'windows nt 5.0' => 'Windows 2000',
						'windows nt 5.1' => 'Windows XP',
						'windows nt 4.0' => 'Windows NT',
						'winnt4.0'       => 'Windows NT',
						'winnt 4.0'      => 'Windows NT',
						'winnt'          => 'Windows NT',
						'windows 98'     => 'Windows 98',
						'win98'          => 'Windows 98',
						'windows 95'     => 'Windows 95',
						'win95'          => 'Windows 95',
						'windows'        => 'Unknown Windows OS',
						'os x'           => 'Mac OS X',
						'intel mac'      => 'Intel Mac',
						'ppc mac'        => 'PowerPC Mac',
						'powerpc'        => 'PowerPC',
						'ppc'            => 'PowerPC',
						'cygwin'         => 'Cygwin',
						'linux'          => 'Linux',
						'debian'         => 'Debian',
						'openvms'        => 'OpenVMS',
						'sunos'          => 'Sun Solaris',
						'amiga'          => 'Amiga',
						'beos'           => 'BeOS',
						'apachebench'    => 'ApacheBench',
						'freebsd'        => 'FreeBSD',
						'netbsd'         => 'NetBSD',
						'bsdi'           => 'BSDi',
						'openbsd'        => 'OpenBSD',
						'os/2'           => 'OS/2',
						'warp'           => 'OS/2',
						'aix'            => 'AIX',
						'irix'           => 'Irix',
						'osf'            => 'DEC OSF',
						'hp-ux'          => 'HP-UX',
						'hurd'           => 'GNU/Hurd',
						'unix'           => 'Unknown Unix OS',
					);
	
	
	// The order of this array should NOT be changed. Many browsers return
	// multiple browser types so we want to identify the sub-type first.
	$browsers = array(
						'Opera'				=> 'Opera',
						'MSIE'				=> 'Internet Explorer',
						'Internet Explorer'	=> 'Internet Explorer',
						'Shiira'			=> 'Shiira',
						'Firefox'			=> 'Firefox',
					    'Chrome'			=> 'Google Chrome',						
						'Chimera'			=> 'Chimera',
						'Phoenix'			=> 'Phoenix',
						'Firebird'			=> 'Firebird',
						'Camino'			=> 'Camino',
						'Netscape'			=> 'Netscape',
						'OmniWeb'			=> 'OmniWeb',
						'Mozilla'			=> 'Mozilla',
						'Safari'			=> 'Safari',
						'Konqueror'			=> 'Konqueror',
						'icab'				=> 'iCab',
						'Lynx'				=> 'Lynx',
						'Links'				=> 'Links',
						'hotjava'			=> 'HotJava',
						'amaya'				=> 'Amaya',
						'IBrowse'			=> 'IBrowse'
					);
	
	$mobiles = array(
						'mobileexplorer'	=> 'Mobile Explorer',
						'openwave'			=> 'Open Wave',
						'opera mini'		=> 'Opera Mini',
						'operamini'			=> 'Opera Mini',
						'elaine'			=> 'Palm',
						'palmsource'		=> 'Palm',
						'digital paths'		=> 'Palm',
						'avantgo'			=> 'Avantgo',
						'xiino'				=> 'Xiino',
						'palmscape'			=> 'Palmscape',
						'nokia'				=> 'Nokia',
						'ericsson'			=> 'Ericsson',
						'blackBerry'		=> 'BlackBerry',
						'motorola'			=> 'Motorola'
					);	
	
	// There are hundreds of bots but these are the most common.
	$robots = array(
						'googlebot'			=> 'Googlebot',
						'msnbot'			=> 'MSNBot',
						'slurp'				=> 'Inktomi Slurp',
						'yahoo'				=> 'Yahoo',
						'askjeeves'			=> 'AskJeeves',
						'fastcrawler'		=> 'FastCrawler',
						'infoseek'			=> 'InfoSeek Robot 1.0',
						'lycos'				=> 'Lycos'
					);			
		
		
		$return = FALSE;
		
		if (isset($platforms))
		{
			$this->platforms = $platforms;
			unset($platforms);
			$return = TRUE;
		}

		if (isset($browsers))
		{
			$this->browsers = $browsers;
			unset($browsers);
			$return = TRUE;
		}

		if (isset($mobiles))
		{
			$this->mobiles = $mobiles;
			unset($mobiles);
			$return = TRUE;
		}
		
		if (isset($robots))
		{
			$this->robots = $robots;
			unset($robots);
			$return = TRUE;
		}

		return $return;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Compile the User Agent Data
	 *
	 * @access	private
	 * @return	bool
	 */		
	function _compile_data()
	{
		$this->_set_platform();
	
		foreach (array('_set_browser', '_set_robot', '_set_mobile', '_set_languages', '_set_charsets') as $function)
		{
			if ($this->$function() === TRUE)
			{
				//break;
			}
		}	
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Set the Platform
	 *
	 * @access	private
	 * @return	mixed
	 */		
	function _set_platform()
	{
		if (is_array($this->platforms) AND count($this->platforms) > 0)
		{
			foreach ($this->platforms as $key => $val)
			{
				if (preg_match("|".preg_quote($key)."|i", $this->agent))
				{
					$this->platform = $val;
					return TRUE;
				}
			}
		}
		$this->platform = 'Unknown Platform';
	}

	// --------------------------------------------------------------------
	
	/**
	 * Set the Browser
	 *
	 * @access	private
	 * @return	bool
	 */		
	function _set_browser()
	{
		if (is_array($this->browsers) AND count($this->browsers) > 0)
		{
			foreach ($this->browsers as $key => $val)
			{		
				if (preg_match("|".preg_quote($key).".*?([0-9\.]+)|i", $this->agent, $match))
				{
					$this->is_browser = TRUE;
					$this->version = $match[1];
					$this->browser = $val;
					$this->_set_mobile();
					return TRUE;
				}
			}
		}
		return FALSE;
	}
			
	// --------------------------------------------------------------------
	
	/**
	 * Set the Robot
	 *
	 * @access	private
	 * @return	bool
	 */		
	function _set_robot()
	{
		if (is_array($this->robots) AND count($this->robots) > 0)
		{		
			foreach ($this->robots as $key => $val)
			{
				if (preg_match("|".preg_quote($key)."|i", $this->agent))
				{
					$this->is_robot = TRUE;
					$this->robot = $val;
					return TRUE;
				}
			}
		}
		return FALSE;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Set the Mobile Device
	 *
	 * @access	private
	 * @return	bool
	 */		
	function _set_mobile()
	{
		if (is_array($this->mobiles) AND count($this->mobiles) > 0)
		{		
			foreach ($this->mobiles as $key => $val)
			{
				if ((strpos(strtolower($this->agent), $key)) !== FALSE)
				{
					$this->is_mobile = TRUE;
					$this->mobile = $val;
					return TRUE;
				}
			}
		}	
		return FALSE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Set the accepted languages
	 *
	 * @access	private
	 * @return	void
	 */			
	function _set_languages()
	{
		if ((count($this->languages) == 0) AND isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) AND $_SERVER['HTTP_ACCEPT_LANGUAGE'] != '')
		{
			$languages = preg_replace('/(;q=.+)/i', '', trim($_SERVER['HTTP_ACCEPT_LANGUAGE']));			
			$this->languages = explode(',', $languages);
		}
		
		if (count($this->languages) == 0)
		{
			$this->languages = array('Undefined');
		}	
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Set the accepted character sets
	 *
	 * @access	private
	 * @return	void
	 */			
	function _set_charsets()
	{	
		if ((count($this->charsets) == 0) AND isset($_SERVER['HTTP_ACCEPT_CHARSET']) AND $_SERVER['HTTP_ACCEPT_CHARSET'] != '')
		{
			$charsets = preg_replace('/(;q=.+)/i', '', trim($_SERVER['HTTP_ACCEPT_CHARSET']));
			
			$this->charsets = explode(',', $charsets);
		}
		
		if (count($this->charsets) == 0)
		{
			$this->charsets = array('Undefined');
		}	
	}

	// --------------------------------------------------------------------
	
	/**
	 * Is Browser
	 *
	 * @access	public
	 * @return	bool
	 */		
	function is_browser()
	{
		return $this->is_browser;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Is Robot
	 *
	 * @access	public
	 * @return	bool
	 */		
	function is_robot()
	{
		return $this->is_robot;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Is Mobile
	 *
	 * @access	public
	 * @return	bool
	 */		
	function is_mobile()
	{
		return $this->is_mobile;
	}	

	// --------------------------------------------------------------------
	
	/**
	 * Is this a referral from another site?
	 *
	 * @access	public
	 * @return	bool
	 */			
	function is_referral()
	{
		return ( ! isset($_SERVER['HTTP_REFERER']) OR $_SERVER['HTTP_REFERER'] == '') ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Agent String
	 *
	 * @access	public
	 * @return	string
	 */			
	function agent_string()
	{
		return $this->agent;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Get Platform
	 *
	 * @access	public
	 * @return	string
	 */			
	function platform()
	{
		return $this->platform;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Get Browser Name
	 *
	 * @access	public
	 * @return	string
	 */			
	function browser()
	{
		return $this->browser;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Get the Browser Version
	 *
	 * @access	public
	 * @return	string
	 */			
	function version()
	{
		return $this->version;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Get The Robot Name
	 *
	 * @access	public
	 * @return	string
	 */				
	function robot()
	{
		return $this->robot;
	}
	// --------------------------------------------------------------------
	
	/**
	 * Get the Mobile Device
	 *
	 * @access	public
	 * @return	string
	 */			
	function mobile()
	{
		return $this->mobile;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Get the referrer
	 *
	 * @access	public
	 * @return	bool
	 */			
	function referrer()
	{
		return ( ! isset($_SERVER['HTTP_REFERER']) OR $_SERVER['HTTP_REFERER'] == '') ? '' : trim($_SERVER['HTTP_REFERER']);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Get the accepted languages
	 *
	 * @access	public
	 * @return	array
	 */			
	function languages()
	{
		if (count($this->languages) == 0)
		{
			$this->_set_languages();
		}
	
		return $this->languages;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Get the accepted Character Sets
	 *
	 * @access	public
	 * @return	array
	 */			
	function charsets()
	{
		if (count($this->charsets) == 0)
		{
			$this->_set_charsets();
		}
	
		return $this->charsets;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Test for a particular language
	 *
	 * @access	public
	 * @return	bool
	 */			
	function accept_lang($lang = 'en')
	{
		return (in_array(strtolower($lang), $this->languages(), TRUE)) ? TRUE : FALSE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Test for a particular character set
	 *
	 * @access	public
	 * @return	bool
	 */			
	function accept_charset($charset = 'utf-8')
	{
		return (in_array(strtolower($charset), $this->charsets(), TRUE)) ? TRUE : FALSE;
	}
	
	
}
?>