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


/**
 * eSyndiCat
 *
 * Implements generic class for eSyndiCat listings and categories database
 *
 * @uses esynDatabase
 * @package
 * @version $id$
 */
class eSyndiCat extends esynDatabase
{
	/**
	 * mPrefix
	 *
	 * @var mixed
	 * @access public
	 */
	var $mPrefix;

	/**
	 * mMailer
	 *
	 * The object of mailer class
	 *
	 * @var mixed
	 * @access public
	 */
	var $mMailer;

	/**
	 * mCacher
	 *
	 * The object of cacher class
	 *
	 * @var mixed
	 * @access public
	 */
	var $mCacher;

	/**
	 * Description of the Variable
	 * @var		mixed
	 * @access	public
	 */
	var $mPlugins = array();

	/**
	 * Description of the Variable
	 * @var		mixed
	 * @access	public
	 */
	var $mHooks = array();

	/**
	 * Description of the Variable
	 * @var		mixed
	 * @access	public
	 */
	var $mLanguages = array();

	var $mI18N = array();

	var $message;

	var $mUrlFunction = '';

	/**
	 * eSyndiCat
	 *
	 * @return void
	 */
	function eSyndiCat()
	{
		$this->mConfig['dbhost'] = ESYN_DBHOST;
		$this->mConfig['dbuser'] = ESYN_DBUSER;
		$this->mConfig['dbpass'] = ESYN_DBPASS;
		$this->mConfig['dbname'] = ESYN_DBNAME;
		$this->mConfig['dbport'] = ESYN_DBPORT;
		$this->mConfig['prefix'] = $this->mPrefix = ESYN_DBPREFIX;

		$this->mTable = $this->mPrefix.$this->mTable;

		$this->connect();

		//$this->fetchConfig();
		
		$config = &esynConfig::instance();

		$this->mConfig = &$config->config;

		$this->mCacher = new esynCacher();
		$this->mMailer = new esynMailer();
		
		$this->mMailer->admins = $this->getAdmins();
		$this->mMailer->mConfig = $this->mConfig;
		$this->mMailer->init();

		$this->mCacher->caching = true;

		$this->getPlugins();
		$this->getHooks();
		$this->getLanguages();

		$side = defined("ESYN_IN_ADMIN") ? 'admin' : 'frontend';

		$this->getI18N($this->mConfig['lang'], $side);

		$seo = $this->mConfig['mod_rewrite'] ? 'Seo' : '';
		$forward = $this->mConfig['forward_to_listing_details'] ? 'Forward' : '';
		$html = $this->mConfig['use_html_path'] ? 'Html' : '';

		$this->mUrlFunction = 'print'.$seo.$forward.'Url';
		$this->mCatUrlFunction = 'print'.$seo.$html.'CatUrl';
		$this->mAccountUrlFunction = 'printAccount'.$seo.'Url';
	}

	function getHooks()
	{
		$where = '';

		$this->setTable("hooks");

		if (!empty($this->mPlugins))
		{
			$where = "AND `plugin` IN('', '".join("','", $this->mPlugins)."')";
		}

		$hooks = $this->all("`name`,`code`,`type`,`file`,`plugin`", "`status` = 'active' {$where} ORDER BY `order`");

		if ($hooks)
		{
			foreach ($hooks as $key => $hook)
			{
				$this->mHooks[$hook['name']][$key]['type']		= $hook['type'];
				$this->mHooks[$hook['name']][$key]['code']		= $hook['code'];
				$this->mHooks[$hook['name']][$key]['file']		= $hook['file'];
				$this->mHooks[$hook['name']][$key]['plugin']	= $hook['plugin'];
			}
		}

		$this->resetTable();
	}

	function getLanguages()
	{
		$this->setTable("language");
		$this->mLanguages = $this->keyvalue("`code`,`lang`", "1 GROUP BY `code`");
		$this->resetTable();

		return $this->mLanguages;
	}

	function getPlugins()
	{
		$this->setTable("plugins");
		$this->mPlugins = $this->onefield("`name`", "`status` = 'active'");
		$this->resetTable();

		return $this->mPlugins;
	}

	function getI18N($lang = 'en', $side = 'frontend')
	{
		$prefix = 'admin' == $side ? 'admin' : 'frontend';

		$lang = array_key_exists($lang, $this->mLanguages) ? $lang : 'en';

		$this->mI18N = $this->mCacher->get("language_{$prefix}_{$lang}", 604800, true);

		if (empty($this->mI18N))
		{
			$this->setTable("language");
			$where = 'admin' == $side ? "`category` IN ('common', 'admin', 'page')" : "`category` IN ('common', 'frontend', 'page')";
			$this->mI18N = $this->keyvalue("`key`,`value`", "`code` = '{$lang}' AND {$where}");
			$this->resetTable();

			$this->mCacher->write("language_{$prefix}_{$lang}", $this->mI18N);
		}

		return $this->mI18N;
	}

	/**
	 * factory
	 *
	 * @access public
	 * @return void
	 */
	function factory()
	{
		$classes = func_get_args();

		$admin = defined("ESYN_IN_ADMIN") ? true : false;

		foreach($classes as $cls)
		{
			$c = "esyn".$cls;

			global $$c;

			if(!is_object($$c))
			{
				$this->loadClass($cls, 'esyn', $admin);
			}

			$$c = new $c();
		}
	}

	function loadClass($class, $prefix = 'esyn', $admin = false)
	{
		if(empty($class))
		{
			trigger_error("Fatal error: The class name is empty", E_USER_ERROR);

			return false;
		}

		$classes = array();
		$path = $admin ? ESYN_ADMIN_CLASSES : ESYN_CLASSES;

		if(is_string($class))
		{
			if(FALSE !== strstr(',', $class))
			{
				$classes = explode(',', $class);
			}
			else
			{
				$classes[] = $class;
			}
		}
		elseif(is_array($class))
		{
			$classes = $class;
		}

		foreach($classes as $cls)
		{
			$file = $path.$prefix.trim($cls).'.php';

			if(!file_exists($file))
			{
				trigger_error("Fatal error: The file class {$cls} is not found.", E_USER_ERROR);

				return false;
			}

			require_once($file);
		}
	}

	function loadPluginClass($class, $plugin, $prefix = '', $admin = false)
	{
		if(empty($class))
		{
			trigger_error("Fatal error: The plugin class name is empty", E_USER_ERROR);

			return false;
		}

		if(empty($plugin))
		{
			trigger_error("Fatal error: The plugin name is empty", E_USER_ERROR);

			return false;
		}

		$classes = array();
		$path = $admin ? ESYN_PLUGINS.$plugin.ESYN_DS.'includes'.ESYN_DS.'classes'.ESYN_DS.'admin'.ESYN_DS : ESYN_PLUGINS.$plugin.ESYN_DS.'includes'.ESYN_DS.'classes'.ESYN_DS;

		if(is_string($class))
		{
			if(FALSE !== strstr(',', $class))
			{
				$classes = explode(',', $class);
			}
			else
			{
				$classes[] = $class;
			}
		}
		elseif(is_array($class))
		{
			$classes = $class;
		}

		foreach($classes as $cls)
		{
			$file = $path.$prefix.trim($cls).'.php';

			if(!file_exists($file))
			{
				trigger_error("Fatal error: The plugin file class {$cls} is not found.", E_USER_ERROR);

				return false;
			}

			require_once($file);
		}

		return true;
	}

	/**
	 * startHook
	 *
	 * @param mixed $name
	 * @access public
	 * @return void
	 */
	function startHook($name)
	{
		if (!isset($name))
		{
			trigger_error("Mandatory parameter for hook is empty", E_USER_ERROR);
	    	return false;
	  	}

		if(!array_key_exists($name, $this->mHooks) || empty($this->mHooks[$name]))
		{
    		return false;
		}

		foreach($this->mHooks[$name] as $hook)
		{
			if('php' == $hook['type'])
			{
				if (ESYN_DEBUG === 2)
				{
					$t = _time();
				}
				
				if(!empty($hook['file']))
				{
					$files = explode(',', $hook['file']);
					
					if(!empty($files))
					{
						foreach($files as $file)
						{
							$file = ESYN_HOME . $file;
					
							if(file_exists($file) && is_file($file))
							{
								require_once($file);
							}
						}
					}
				}
				
				eval($hook['code']);

				if (ESYN_DEBUG === 2)
				{
					$t = round(_time() - $t, 4);

					$a = array(
						'name'		=> $name,
						'plugin'	=> $hook['plugin'],
						'code'		=> $hook['code'],
						'type'		=> $hook['type'],
						'time'		=> $t
					);

					d($a, '', 'hook_debug');
				}
			}
		}
	}

	/**
	 * cascadeDelete
	 *
	 * Accepts array of table names and delete by $where (second param)
	 *
	 * @param mixed $tbl
	 * @param string $where
	 * @access public
	 * @return void
	 */
	function cascadeDelete($tbl, $where='')
	{
		if(empty($tbl) || empty($where))
		{
			return false;
		}

		if(!is_array($tbl))
		{
			$tbl = (array)$tbl;
		}

		// We don't use setTable because this is multiple changing
		$old = $this->mTable;
		$totalDeleted = 0;
		foreach($tbl as $t)
		{
			$this->mTable = $this->mPrefix.$t;
			$totalDeleted += esynDatabase::delete($where);
		}
		$this->mTable = $old;

		return $totalDeleted;
	}

	function convertIds($field, $ids)
	{
		$where = '';

		if(empty($ids) || empty($field))
		{
			return false;
		}

		if(is_array($ids))
		{
			foreach($ids as $id)
			{
				$where_ids[] = (int)$id;
			}

			$where = "`{$field}` IN ('".join("','", $where_ids)."')";
		}
		elseif(is_string($ids))
		{
			$id = (int)$ids;

			$where = "`{$field}` = '{$id}'";
		}
		elseif(is_int($ids))
		{
			$where = "`{$field}` = '{$ids}'";
		}
		else
		{
			return false;
		}

		return $where;
	}

	function getPhrase($key)
	{
	}

	function getAdmins()
	{
		$this->setTable("admins");
		$admins = $this->all("*");
		$this->resetTable();

		return $admins;
	}

	function getMessage()
	{
		return $this->message;
	}

	function createJsCache($forceRebuild = false)
	{
		/*
		 * Create js file with all phrases
		 */
		$adminLangFile = $this->mCacher->savePath."intelli.admin.lang.{$this->mConfig['lang']}.js";

		if(!file_exists($adminLangFile) || $forceRebuild)
		{
			$this->loadClass('JSON');

			$json = new Services_JSON();

			$jsonContent = $json->encode($this->getI18N($this->mConfig['lang'], 'admin'));
			$jsonLangs = $json->encode($this->mLanguages);

			$fileContent = "intelli.admin.lang.{$this->mConfig['lang']} = {$jsonContent};";
			$fileContent .= "intelli.admin.langList = {$jsonLangs};";

			$filePoint = fopen($adminLangFile, 'w');

			if ($filePoint)
			{
				fwrite($filePoint, $fileContent);
				fclose($filePoint);
			}
		}

		/*
		 * Create js config file
		 */
		$jsConfigFile = $this->mCacher->savePath.'intelli.config.js';

		if(!file_exists($jsConfigFile) || $forceRebuild)
		{
			if (!empty($this->mConfig['config_keys']))
			{
				$personal_keys = explode(',', $this->mConfig['config_keys']);
			}
			else
			{
				$personal_keys = array('sendmail_path', 'smtp_secure_connection', 'smtp_server', 'smtp_user', 'smtp_password');
			}
			
			$js_config = array();
			
			$this->loadClass('JSON');

			$json = new Services_JSON();

			$js_config = $this->mConfig;

			foreach($personal_keys as $key)
			{
				if(isset($js_config[$key]))
				{
					unset($js_config[$key]);
				}
			}

			$jsonContent = $json->encode($js_config);

			$fileContent = 'intelli.config = '.$jsonContent.';';

			$filePoint = fopen($jsConfigFile, 'w');

			if ($filePoint)
			{
				fwrite($filePoint, $fileContent);
				fclose($filePoint);
			}
		}

		/*
		 * Create js file with all phrases
		 */
		$jsLangFile = $this->mCacher->savePath."intelli.lang.{$this->mConfig['lang']}.js";

		if(!file_exists($jsLangFile) || $forceRebuild)
		{
			$this->loadClass('JSON');

			$json = new Services_JSON();

			$jsonContent = $json->encode($this->getI18N($this->mConfig['lang']));
			$jsonLangs = $json->encode($this->mLanguages);

			$fileContent = "intelli.lang.{$this->mConfig['lang']} = {$jsonContent};";
			$fileContent .= "intelli.langList = {$jsonLangs};";

			$filePoint = fopen($jsLangFile, 'w');

			if ($filePoint)
			{
				fwrite($filePoint, $fileContent);
				fclose($filePoint);
			}
		}
	}

	function fetchConfig()
	{
		static $s;

		if ($s == null)
		{
			$this->mConfig = $this->mCacher->get("config", 604800, true);

			if(ESYN_DEBUG > 0 || empty($this->mConfig))
			{
				$this->setTable('config');
				$this->mConfig = $this->keyvalue("`name`,`value`", "`type` NOT IN('divider')");
				$this->resetTable();

				$this->mCacher->write("config", $this->mConfig);
			}

			$s = $this->mConfig;
		}
		else
		{
			$this->mConfig = $s;
		}

		return $s;
	}

	/**
	 * getConfig returns config value by a given key
	 * 
	 * @param string $key config key
	 * @param boolean $db [optional] true: value fetched from db, false: value fetched from cached file
	 * 
	 * @return string 
	 */
	function getConfig($key, $db = false)
	{
		if(isset($this->mConfig[$key]) && !$db)
		{
			return $this->mConfig[$key];
		}

		$this->setTable("config");
		$value = $this->one("`value`", "`name` = :key", array('key' => $key));
		$this->resetTable();

		return $value;
	}
	
	/**
	 * setConfig method modifies configuration array
	 * 
	 * @param string $key configuration array key
	 * @param object $value configuration array value
	 * @param object $db [optional] true: writes to database, false: changes config array for execution only 
	 * 
	 * @return bool
	 */
	function setConfig($key, $value, $db = false)
	{
		$result = true;
		
		if ($db && !is_scalar($value))
		{
			trigger_error("Couldn't store non scalar value to the database".__CLASS__."::set", E_USER_ERROR);
		}

		$this->mConfig[$key] = $value;

		if ($db)
		{
			$this->setTable("config");
			$result = (bool)$this->update(array("value" => $value), "`name` = :key", array('key' => $key));
			$this->resetTable();

			$this->mCacher->remove("config");
			$this->mCacher->remove("intelli.lang");
			$this->mCacher->remove("intelli.admin.lang");
			$this->mCacher->remove("intelli.config");
		}

		return $result;
	}
}
