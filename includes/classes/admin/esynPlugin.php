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
 * esynPlugin 
 * 
 * @uses esynAdmin
 * @package 
 * @version $id$
 */
class esynPlugin extends esynAdmin
{

	/**
	 * mTable 
	 * 
	 * @var string
	 * @access public
	 */
	var $mTable = 'plugins';

	/**
	 * updateServer 
	 * 
	 * @var string
	 * @access public
	 */
	var $updateServer = 'http://www.esyndicat.com/pluginUpdater.php';

	/**
	 * inTag 
	 * 
	 * @var mixed
	 * @access public
	 */
	var $inTag;
	
	/**
	 * level 
	 * 
	 * @var float
	 * @access public
	 */
	var $level = 0;

	/**
	 * path 
	 * 
	 * @var mixed
	 * @access public
	 */
	var $path;

	/**
	 * attributes 
	 * 
	 * @var mixed
	 * @access public
	 */
	var $attributes;

	/**
	 * xml 
	 * 
	 * @var mixed
	 * @access public
	 */
	var $xml;

	/**
	 * upgrade 
	 *
	 * If version of new plugin greater than installed then will run upgrade mode
	 * 
	 * @var bool
	 * @access public
	 */
	var $upgrade = false;

	/**
	 * upgradeCode 
	 *
	 * The PHP code which will run when plugin upgrades
	 * 
	 * @var string
	 * @access public
	 */
	var $upgradeCode;

	/**
	 * name 
	 *
	 * The name of plugin
	 * 
	 * @var string
	 * @access public
	 */
	var $name;

	/**
	 * title 
	 *
	 * The title of plugin
	 * 
	 * @var string
	 * @access public
	 */
	var $title;
	
	/**
	 * status
	 *
	 * The status of plugin. Must be 'active' or 'inactive'. Other statuses will be ignored and set 'inactive' status
	 *
	 * @var string
	 * @access public
	 */
	var $status;
	
	/**
	 * summary 
	 *
	 * The description of plugin
	 *
	 * @var string
	 * @access public
	 */
	var $summary;
	
	/**
	 * version 
	 *
	 * The version of plugin
	 *
	 * @var string
	 * @access public
	 */
	var $version;
	
	/**
	 * message 
	 *
	 * The message 
	 *
	 * @var string
	 * @access public
	 */
	var $message;
	
	/**
	 * author 
	 *
	 * The author of plugin
	 * 
	 * @var string
	 *
	 */
	var $author;

	/**
	 * contributor 
	 *
	 * The contributor of plugin
	 *
	 * @var string
	 * @access public
	 */
	var $contributor;

	/**
	 * notes 
	 *
	 * The notes of plugin. Will show in the notification box when plugin installed
	 *
	 * @var string
	 * @access public
	 */
	var $notes;

	/**
	 * installEvalCode
	 *
	 * The PHP code which will run when plugin installs
	 *
	 * @var string
	 * @access public
	 */
	var $installEvalCode;
	
	/**
	 * uninstallCode
	 *
	 * The PHP code which will run when plugin uninstalls
	 *
	 * @var string
	 * @access public
	 */
	var $uninstallCode;
	
	/**
	 * error
	 *
	 * True if there is any error
	 *
	 * @var	bool
	 * @access public
	 */
	var $error = false;
	/**
	 * adminBlocks 
	 *
	 * Admin blocks array
	 * 
	 * @var arr
	 * @access public
	 */
	var $adminBlocks;
	
	/**
	 * adminPages 
	 *
	 * Admin pages array
	 * 
	 * @var arr
	 * @access public
	 */
	var $adminPages;

	/**
	 * actions 
	 * 
	 * @var mixed
	 * @access public
	 */
	var $actions;
	
	/**
	 * phrases 
	 *
	 * New phrases array
	 * 
	 * @var arr
	 * @access public
	 */
	var $phrases;

	/**
	 * configGroup
	 *
	 * Config group array
	 *
	 * @var	arr
	 * @access public
	 */
	var $configGroups;

	/**
	 * config
	 *
	 * Config setting array
	 *
	 * @var arr
	 * @access public
	 */
	var $config;

	/**
	 * pages
	 *
	 * Front end pages array
	 *
	 * @var arr
	 * @access public
	 */
	var $pages;

	/**
	 * fields 
	 * 
	 * @var mixed
	 * @access public
	 */
	var $fields;

	/**
	 * blocks
	 *
	 * Front end blocks
	 *
	 * @var arr
	 * @access public
	 */
	var $blocks;

	/**
	 * hooks 
	 *
	 * Hooks array
	 *
	 * @var arr
	 * @access public
	 */
	var $hooks;

	/**
	 * installSQL
	 *
	 * SQL queries array which will be runned when plugin is installing
	 *
	 * @var arr
	 * @access public
	 */
	var $installSQL;

	/**
	 * upgradeSQL
	 *
	 * SQL queries array which will be runned when plugin is upgrading
	 * 
	 * @var arr
	 * @access public
	 */
	var $upgradeSQL;

	/**
	 * uninstallSQL
	 *
	 * SQL queries array which will be runned when plugin is uninstalling
	 *
	 * @var arr
	 * @access public
	 */
	var $uninstallSQL;
	
	/**
	 * htaccessRules 
	 *
	 * htaccess rules which must be added to .htaccess file
	 *
	 * @var string
	 * @access public
	 */
	var $htaccessRules;

	/**
	 * date
	 *
	 * date release 
	 *
	 * @var	string
	 * @access public
	 */
	var $date;

	/**
	 * update 
	 *
	 * The update mode. When update mode is enabled it will not add plugin to database.
	 * 
	 * @var mixed
	 * @access public
	 */
	var $update = false;

	/**
	 * compatibility 
	 *
	 * The number of script version with which plugin will work
	 * If it equal FALSE it means the plugin is not compatibility with current version
	 * 
	 * @var mixed
	 * @access public
	 */
	var $compatibility = false;

	/**
	 * parse 
	 * 
	 * Parsing XML document
	 *
	 * @access public
	 * @return void
	 */
	function parse()
	{
		require_once(ESYN_INCLUDES."xml".ESYN_DS."/xml_saxy_parser.php");
		
		$xmlParser = new SAXY_Parser();

		$xmlParser->xml_set_element_handler(array(&$this, "startElement"), array(&$this, "endElement"));
		$xmlParser->xml_set_character_data_handler(array(&$this, "charData"));
		$xmlParser->xml_set_comment_handler(array(&$this, "commentElement"));

		$xmlParser->parse($this->xml);
	}

	/**
	 * doAction 
	 * 
	 * Action of plugin. Can be pass 'install' or 'uprade' parameter. Other parameters will be ignored.
	 *
	 * @param string $action 
	 * @access public
	 * @return void
	 */
	function doAction($action = '')
	{
		$validAction = array('install', 'upgrade');
		
		if(empty($action) || !in_array($action, $validAction))
		{
			$this->error = true;
			$this->message = "Fatal error: Action is not valid";
			
			return false;
		}

		$this->parse();

		$this->checkFields();

		if($this->error)
		{
			return false;
		}
		else
		{
			$action = ('install' == $action && $this->exist() && $this->compare()) ? 'upgrade' : $action;

			$this->{$action}();
		}
	}

	/**
	 * exist 
	 * 
	 * Return true if plugin already installed and false otherwise.
	 *
	 * @access public
	 * @return bool
	 */
	function exist()
	{
		return $this->exists("`name` = :name", array('name' => $this->name));
	}

	/**
	 * compare
	 *
	 * Return true if version of new plugin greater than installed or false otherwise. 
	 * 
	 * @access public
	 * @return void
	 */
	function compare()
	{
		$version = $this->one("`version`", "`name` = :name", array('name' => $this->name));

		return version_compare($version, $this->version, "<");
	}

	/**
	 * version 
	 * 
	 * @access public
	 * @return void
	 */
	function version()
	{
		return $this->one("`version`", "`name` = :name", array('name' => $this->name));
	}

	/**
	 * checkFields 
	 *
	 * Checking mandatory fields. If there is any error the 'error' flag will set to true.
	 * 
	 * @access public
	 * @return void
	 */
	function checkFields()
	{
		$mandatoryFields = array("name", "title", "version", "summary", "author", "contributor");

		$notExist = array();

		$vars = get_object_vars($this);

		foreach($mandatoryFields as $field)
		{
			if(!array_key_exists($field, $vars))
			{
				$this->error = true;
				$notExist[] = $field;
			}
		}

		if($this->error)
		{
			if(empty($notExist))
			{
				$this->message = "Fatal error: Probably specified file is not XML file or is not acceptable";
			}
			else
			{
				$this->message = "Fatal error: The following fields are required: ";
				$this->message .= join(", ", $notExist);
			}
		}
	}

	/**
	 * checkVersion 
	 * 
	 * Check the version of plugin. The error flag will be set to true if version of installing plugin less then installed.
	 *
	 * @access public
	 * @return void
	 */
	function checkVersion()
	{
		if($this->exist() && !$this->compare())
		{
			$this->error = true;
			$this->message = "Warning: That plugin is already installed";
		}
	}

	/**
	 * upgrade 
	 *
	 * Upgrade plugin.
	 * 
	 * @access public
	 * @return void
	 */
	function upgrade()
	{
		$this->upgrade = true;

		/**
		 * Update plugin
		 */
		$plugin['name'] = $this->name;
		$plugin['author'] = $this->author;
		$plugin['contributor'] = $this->contributor;
		$plugin['title'] = $this->title;
		$plugin['status'] = in_array($this->status, array("active", "inactive"), true) ? $this->status : 'inactive';
		$plugin['version'] = $this->version;
		$plugin['summary'] = $this->summary;

		if(!empty($this->uninstallCode))
		{
			$plugin['uninstall_code'] = $this->uninstallCode;
		}
		
		if(!empty($this->uninstallSQL))
		{
			$plugin['uninstall_sql'] = serialize($this->uninstallSQL);
		}

		/**
		 * Update htaccess rules  
		 */
		if(!empty($this->htaccessRules))
		{
			$plugin['htaccess'] = serialize($this->htaccessRules);

			$htacessRules = unserialize($this->one("htaccess", "`name` = '{$plugin['name']}'"));

			if($htacessRules && is_array($htacessRules))
			{
				$this->setTable("config");

				foreach($this->htaccessRules as $htline)
				{
					$oldcode = $this->one("`value`", "`name` = 'htaccessfile_{$htline['section']}'");

					$oldcode = trim($oldcode);
						
					$newcode = ('top' == $htline['addto']) ? $htline['rules']."\r\n\r\n".$oldcode : $oldcode."\r\n\r\n".$htline['rules']."\r\n\r\n";

					if($this->exists("`name` = 'htaccessfile_{$htline['section']}'"))
					{
						$this->update(array("value" => $newcode), "`name` = 'htaccessfile_{$htline['section']}'");
					}
					else
					{
						$this->insert(array("name" => "htaccessfile_{$htline['section']}", "value" => $newcode, "type" => "hidden"));						
					}
				}

				$this->resetTable();
			}

			$this->updateHtaccess();
		}

		/**
		 * Update admin blocks
		 */
		if(!empty($this->adminBlocks))
		{
			$this->setTable("admin_blocks");

			$maxorder = $this->one("MAX(`order`) + 1");
			$maxorder = (NULL == $maxorder) ? 1 : $maxorder;

			foreach($this->adminBlocks as $block)
			{
				if($this->exists("`plugin` = '{$plugin['name']}' AND `name` = '{$block['name']}'"))
				{
					$this->update($block, "`plugin` = '{$plugin['name']}' AND `name` = '{$block['name']}'");
				}
				else
				{
					$this->insert($block, array("order" => $maxorder++));
				}
			}
			
			$this->resetTable();
		}

		/**
		 * Update admin pages
		 */
		if(!empty($this->adminPages))
		{
			$this->setTable("admin_pages");

			$this->delete("`plugin` = '{$plugin['name']}'");

			$maxorder = $this->one("MAX(`order`) + 1", "`block_name` = 'plugins'");
			$maxorder = (NULL == $maxorder) ? 1 : $maxorder;

			foreach($this->adminPages as $page)
			{
				$this->insert($page, array("order" => $maxorder++));
			}

			$this->resetTable();
		}

		/**
		 * Update eSyndiCat actions
		 */
		if(!empty($this->actions))
		{
			foreach($this->actions as $action)
			{
				$action['name'] = strtolower(str_replace(' ', '_', $action['name']));
				
				if(isset($action['name']) && !empty($action['name']))
				{
					$this->setTable("actions");
					
					if(!isset($action['order']) || empty($action['order']) || !ctype_digit($action['order']))
					{
						$maxorder = $this->one("MAX(`order`) + 1");
						$maxorder = (NULL == $maxorder) ? 1 : $maxorder;
					}
					else
					{
						$maxorder = $action['order'];
					}

					$action['order'] = $maxorder;

					$show = $action['show'];
					$title = $action['title'];

					unset($action['show']);

					if($this->exists("`name` = :name", array('name' => $action['name'])))
					{
						$this->update($action, "`name` = :name", array('name' => $action['name']));
					}
					else
					{
						$this->insert($action);
					}

					$this->resetTable();

					if(empty($show) || 'all' == strtolower($show))
					{
						foreach($acos as $aco)
						{
							$actionAco[] = array(
								"page"			=> $aco,
								"action_name"	=> $action['name']
							);
						}
					}
					else
					{
						$show = explode(',', $show);

						if(!empty($show) && is_array($show))
						{
							foreach($show as $sh)
							{
								$actionAco[] = array(
									"page"			=> $sh,
									"action_name"	=> $action['name']
								);
							}
						}
					}

					if(isset($actionAco) && !empty($actionAco))
					{
						$this->setTable("action_show");
						$this->delete("`action_name` = :name", array('name' => $action['name']));
						$this->insert($actionAco);
						$this->resetTable();
					}

					if (!empty($title))
					{
						$lang_content = array();

						foreach ($this->mLanguages as $lngcode => $lngvalue)
						{
							$lang_content[] = array(
								"key"		=> 'esyndicat_action_'.$action['name'],
								"value"		=> $title,
								"code"		=> $lngcode,
								"lang"		=> $lngvalue,
								"category"	=> 'frontend',
								"plugin"	=> $this->name
							);
						}

						if (!empty($lang_content))
						{
							parent::setTable("language");
							parent::delete("`key` = 'esyndicat_action_{$action['name']}'");
							parent::insert($lang_content);
							parent::resetTable();
						}
					}
				}
			}
		}

		/**
		 * Update language phrases
		 */
		if(!empty($this->phrases))
		{
			$this->setTable("language");

			foreach($this->phrases as $phrase)
			{
				if(!$this->exists("`key` = '{$phrase['key']}' AND `plugin` = '{$plugin['name']}'"))
				{
					$this->insert($phrase);
				}
			}
			
			$this->resetTable();
		}

		/**
		 * Update config groups
		 */
		if(!empty($this->configGroups))
		{
			$this->setTable("config_groups");
			$this->delete("`plugin` = '{$plugin['name']}'");
			
			$maxorder = $this->one("MAX(`order`) + 1");
			$maxorder = (NULL == $maxorder) ? 1 : $maxorder;

			foreach($this->configGroups as $config)
			{
				$this->insert($config, array("order" => $maxorder++));
			}
			
			$this->resetTable();
		}

		/**
		 * Update config settings
		 */
		if(!empty($this->config))
		{
			$this->setTable("config");
			
			$maxorder = $this->one("MAX(`order`) + 1");
			$maxorder = (NULL == $maxorder) ? 1 : $maxorder;

			foreach($this->config as $config)
			{
				if($this->exists("`name` = '{$config['name']}'"))
				{
					if(isset($config['value']))
					{
						unset($config['value']);
					}

					$this->update($config, "`name` = '{$config['name']}'");
				}
				else
				{
					$this->insert($config, array("order" => $maxorder++));
				}
			}

			$this->resetTable();
		}

		/**
		 * Update front end pages
		 */
		if(!empty($this->pages))
		{
			$this->setTable("pages");

			$maxorder = $this->one("MAX(`order`) + 1");
			$maxorder = (NULL == $maxorder) ? 1 : $maxorder;

			$this->resetTable();

			foreach($this->pages as $page)
			{
				$lang_content = array();

				if(isset($page['title']) && !empty($page['title']))
				{
					$title = $page['title'];

					foreach($this->mLanguages as $lngcode => $lngvalue)
					{
						$lang_content[] = array(
							"key"		=> 'page_title_'.$page['name'],
							"value"		=> $title,
							"code"		=> $lngcode,
							"lang"		=> $lngvalue,
							"category"	=> "page",
							"plugin"	=> $this->name
						);
					}
				}

				unset($page['title']);

				if(isset($page['contents']) && !empty($page['contents']))
				{
					$contents = $page['contents'];

					foreach($this->mLanguages as $lngcode => $lngvalue)
					{
						$lang_content[] = array(
							"key"		=> 'page_content_'.$page['name'],
							"value"		=> $contents,
							"code"		=> $lngcode,
							"lang"		=> $lngvalue,
							"category"	=> "page",
							"plugin"	=> $this->name
						);
					}
				}

				unset($page['contents']);

				$this->setTable("pages");
				$exists = $this->exists("`name` = '{$page['name']}' AND `plugin` = '{$plugin['name']}'");
				$this->resetTable();
				
				if(!$exists)
				{
					$this->setTable("pages");
					$this->insert($page, array("order" => $maxorder++));
					$this->resetTable();

					if (!empty($lang_content))
					{
						parent::setTable("language");
						parent::insert($lang_content);
						parent::resetTable();
					}
				}
				else
				{
					$this->setTable("pages");
					$this->update($page, "`name` = '{$page['name']}' AND `plugin` = '{$plugin['name']}'");
					$this->resetTable();

					if (!empty($lang_content))
					{
						parent::setTable("language");

						foreach ($lang_content as $lc)
						{
							parent::update($lc, "`key` = :key AND `code` = :code", array('key' => $lc['key'], 'code' => $lc['code']));
						}
						
						parent::resetTable();
					}
				}
			}
		}

		/**
		 * Update front end blocks
		 */
		if(!empty($this->blocks))
		{
			$this->setTable("pages");
			$pages = $this->onefield("`name`");
			$this->resetTable();

			$version = $this->version();

			$this->setTable("blocks");

			$maxorder = $this->one("MAX(`order`) + 1");
			$maxorder = (NULL == $maxorder) ? 1 : $maxorder;

			$this->resetTable();

			foreach($this->blocks as $block)
			{
				if(isset($block['added']) && !empty($block['added']) && (1 == version_compare($block['added'], $version)))
				{
					$blockPages = $block['pages'];
					$blockExceptPages = $block['pagesexcept'];

					unset($block['pages'], $block['pagesexcept'], $block['added']);
						
					if(isset($block['contents']) && !empty($block['contents']))
					{
						$block['contents'] = str_replace('{PLUGIN}', $this->name, $block['contents']);
					}
					
					$this->setTable("blocks");
					$id = $this->insert($block, array("order" => $maxorder++));
					$this->resetTable();

					if(!empty($blockPages))
					{
						if(!empty($blockExceptPages))
						{
							$blockExceptPages = explode(',', $blockExceptPages);
						}
						else
						{
							$blockExceptPages = array();
						}

						if('all' == strtolower($blockPages))
						{
							foreach($pages as $page)
							{
								if(!in_array($page, $blockExceptPages))
								{
									$pluginAco[] = array(
										"page"		=> esynSanitize::sql($page),
										"block_id"	=> $id
									);
								}
							}
						}
						else
						{
							$blockPages = explode(',', $blockPages);

							foreach($blockPages as $page)
							{
								if(!in_array($page, $blockExceptPages))
								{
									$pluginAco[] = array(
										"page"		=> esynSanitize::sql($page),
										"block_id"	=> $id
									);
								}
							}
						}

						$this->setTable("block_show");
						$this->insert($pluginAco);
						$this->resetTable();
					}
				}
			}
		}

		/**
		 * Update new hooks
		 */
		if(!empty($this->hooks))
		{
			$this->setTable("hooks");
			
			foreach($this->hooks as $hook)
			{
				$names = explode(',', $hook['name']);
				
				if(!empty($names))
				{
					foreach($names as $name)
					{
						$addit = array();

						$hook['name'] = $name;

						if (!isset($hook['order']) || empty($hook['order']))
						{
							$addit = array("order" => $maxorder);

							unset($hook['order']);
						}

						if(isset($hook['code']) && !empty($hook['code']))
						{
							$hook['code'] = str_replace('{PLUGIN}', $this->name, $hook['code']);
						}

						if($this->exists("`plugin` = '{$this->name}' AND `name` = '{$hook['name']}'"))
						{
							$this->update($hook, "`plugin` = '{$this->name}' AND `name` = '{$hook['name']}'");
						}
						else
						{
							$this->insert($hook, $addit);

							$maxorder++;
						}
					}
				}
			}
			
			$this->resetTable();
		}

		/**
		 * Run upgrade SQL queries
		 */
		if(!empty($this->upgradeSQL))
		{
			$mysql_ver_data = (ESYN_MYSQLVER == "41") ? "ENGINE=MyISAM DEFAULT CHARSET=utf8" : "TYPE=MyISAM";
			$installed_version = $this->one("`version`", "`name` = '{$this->name}'");

			if($installed_version && isset($this->upgradeSQL[$installed_version]))
			{	
				foreach($this->upgradeSQL[$installed_version] as $sql)
				{
					if(!empty($sql['query']))
					{
						$sql['query'] = str_replace('{prefix}', $this->mPrefix, $sql['query']);
						$sql['query'] = str_replace('{mysql_version}', $mysql_ver_data, $sql['query']);
	
						$this->query($sql['query']);
					}
				}
			}
			else
			{
				foreach($this->upgradeSQL as $sql)
				{
					if(!empty($sql['query']))
					{
						$sql['query'] = str_replace('{prefix}', $this->mPrefix, $sql['query']);
						$sql['query'] = str_replace('{mysql_version}', $mysql_ver_data, $sql['query']);
	
						$this->query($sql['query']);
					}
				}
			}
		}

		/**
		 * Run upgrade PHP code
		 */
		if(!empty($this->upgradeCode))
		{
			eval($this->upgradeCode);
		}

		$this->update($plugin, "`name` = '{$plugin['name']}'");

		$this->mCacher->clearAll('', true);
	}

	/**
	 * uninstall 
	 *
	 * Uninstall plugin
	 * 
	 * @param mixed $plugin the name of uninstalled plugin
	 * @access public
	 * @return void
	 */
	function uninstall($plugin)
	{
		if(empty($plugin))
		{
			$this->error = true;
			$this->message = "The plugin name is empty.";

			return false;
		}

		$this->parse();

		$this->checkFields();

		/**
		 * Remove plugin
		 */
		$plugin = esynSanitize::sql($plugin);

		$code = $this->row("`htaccess`, `uninstall_code`, `uninstall_sql`", "`name` = '{$plugin}'");

		$this->delete("`name` = '{$plugin}'");

		/**
		 * Remove all data related with plugin
		 */
		$this->cascadeDelete(array("admin_blocks", "admin_pages", "language", "config_groups", "config", "pages", "hooks"), "`plugin` = '{$plugin}'");

		/**
		 * Remove front end blocks 
		 */
		$this->setTable("blocks");
		$block_ids = $this->onefield("id", "`plugin` = '{$plugin}'");
		$this->resetTable();
		
		if($block_ids)
		{
			$this->setTable("blocks");
			$this->delete("`id` IN('".join("','", $block_ids)."')");
			$this->resetTable();

			$this->setTable("block_show");
			$this->delete("`block_id` IN('".join("','", $block_ids)."')");
			$this->resetTable();
		}

		/**
		 * Remove eSyndiCat actions
		 */
		$this->setTable("actions");
		$actions = $this->onefield("name", "`plugin` = '{$plugin}'");
		$this->delete("`plugin` = '{$plugin}'");
		$this->resetTable();

		if(!empty($actions))
		{
			$this->setTable("action_show");
			$this->delete("`action_name` IN('".join("','", $actions)."')");
			$this->resetTable();
		}

		/**
		 * Remove htaccess rules 
		 */
		if(!empty($code['htaccess']))
		{
			$htacessRules = unserialize($code['htaccess']);
			
			if($htacessRules && is_array($htacessRules))
			{
				$this->setTable("config");

				foreach($htacessRules as $rule)
				{
					if($this->exists("`name` = 'htaccessfile_{$rule['section']}'"))
					{
						$oldcode = $this->one("`value`", "`name` = 'htaccessfile_{$rule['section']}'");

						$newcode = trim(str_replace($rule['rules']."\r\n\r\n", "", $oldcode));
						$newcode .= "\r\n\r\n";
						
						$this->update(array("value" => $newcode), "`name` = 'htaccessfile_{$rule['section']}'");
					}
				}

				$this->resetTable();
				$this->updateHtaccess();
			}
		}

		/**
		 * Remove fields
		 */
		if(!empty($this->fields))
		{
			$this->factory("ListingField");

			global $esynListingField;

			foreach($this->fields as $field)
			{
				$id = $esynListingField->one("id", "`name` = '{$field['name']}'");

				$esynListingField->delete($id);
			}
		}

		/**
		 * Run uninstall SQL queries
		 */
		if(!empty($code['uninstall_sql']))
		{
			$code['uninstall_sql'] = unserialize($code['uninstall_sql']);
			
			if($code['uninstall_sql'] && is_array($code['uninstall_sql']))
			{
				foreach($code['uninstall_sql'] as $sql)
				{
					$sql['query'] = str_replace('{prefix}', $this->mPrefix, $sql['query']);

					$this->query($sql['query']);
				}
			}
		}

		/**
		 * Run uninstall PHP code 
		 */
		if(!empty($code['uninstall_code']))
		{
			eval($code['uninstall_code']);
		}

		$this->mCacher->clearAll('', true);

		return true;
	}

	/**
	 * install 
	 *
	 * Install plugin
	 * 
	 * @access public
	 * @return void
	 */
	function install()
	{
		/**
		 * Add new plugin
		 */
		$plugin['name']			= $this->name;
		$plugin['author']		= $this->author;
		$plugin['contributor']	= $this->contributor;
		$plugin['title']		= $this->title;
		$plugin['status']		= in_array($this->status, array("active", "inactive"), true) ? $this->status : 'inactive';
		$plugin['version']		= $this->version;
		$plugin['summary']		= $this->summary;

		if(FALSE !== stristr('update', $plugin['name']))
		{
			$this->update = true;
		}

		/**
		 * Add new htaccess rules 
		 */
		if(!empty($this->htaccessRules))
		{
			$plugin['htaccess'] = serialize($this->htaccessRules);

			$this->setTable("config");

			foreach($this->htaccessRules as $htline)
			{
				$oldcode = $this->one("`value`", "`name` = 'htaccessfile_{$htline['section']}'");

				$oldcode = trim($oldcode);
					
				$newcode = ('top' == $htline['addto']) ? $htline['rules']."\r\n\r\n".$oldcode."\r\n\r\n" : $oldcode."\r\n\r\n".$htline['rules']."\r\n\r\n";

				if($this->exists("`name` = 'htaccessfile_{$htline['section']}'"))
				{
					$this->update(array("value" => $newcode), "`name` = 'htaccessfile_{$htline['section']}'");
				}
				else
				{
					$this->insert(array("name" => "htaccessfile_{$htline['section']}", "value" => $newcode, "type" => "hidden"));						
				}
			}

			$this->resetTable();
			$this->updateHtaccess();
		}

		if(!empty($this->uninstallCode))
		{
			$plugin['uninstall_code'] = $this->uninstallCode;
		}

		if(!empty($this->uninstallSQL))
		{
			$plugin['uninstall_sql'] = serialize($this->uninstallSQL);
		}

		if(!$this->update)
		{
			$this->insert($plugin, array("date" => "NOW()"));
		}

		/**
		 * Add new admin blocks
		 */
		if(!empty($this->adminBlocks))
		{
			$this->setTable("admin_blocks");

			$maxorder = $this->one("MAX(`order`) + 1");
			$maxorder = (NULL == $maxorder) ? 1 : $maxorder;

			foreach($this->adminBlocks as $block)
			{
				$this->insert($block, array("order" => $maxorder));
				$maxorder++;
			}
			
			$this->resetTable();
		}

		/**
		 * Add new admin pages
		 */
		if(!empty($this->adminPages))
		{
			$this->setTable("admin_pages");
			
			$maxorder = $this->one("MAX(`order`)", "`block_name` = 'plugins' AND `menus` IN ('main')");
			$maxorder = (NULL == $maxorder) ? 1 : $maxorder;

			$maxheaderorder = $this->one("MAX(`header_order`)", "`menus` IN ('header')");
			$maxheaderorder = (NULL == $maxheaderorder) ? 1 : $maxheaderorder;

			foreach($this->adminPages as $page)
			{
				$addit = array();

				if(!isset($page['order']))
				{
					$maxorder++;

					$addit['order'] = $maxorder;
				}

				if(!isset($page['header_order']))
				{
					$maxheaderorder++;

					$addit['header_order'] = $maxheaderorder;
				}

				$this->insert($page, $addit);
			}
			
			$this->resetTable();
		}

		/**
		 * Add new eSyndiCat actions
		 */
		if(!empty($this->actions))
		{
			foreach($this->actions as $action)
			{
				$action['name'] = strtolower(str_replace(' ', '_', $action['name']));
				
				if(isset($action['name']) && !empty($action['name']) && !$this->exists("`name` = :name", array('name' => $action['name'])))
				{
					$this->setTable("actions");

					if(!isset($action['order']) || empty($action['order']) || !ctype_digit($action['order']))
					{
						$maxorder = $this->one("MAX(`order`) + 1");
						$maxorder = (NULL == $maxorder) ? 1 : $maxorder;
					}
					else
					{
						$maxorder = $action['order'];
					}

					$action['order'] = $maxorder;

					$show = $action['show'];
					$title = $action['title'];

					unset($action['show']);
					unset($action['title']);

					$this->insert($action);
					$this->resetTable();

					if(empty($show) || 'all' == strtolower($show))
					{
						foreach($acos as $aco)
						{
							$actionAco[] = array(
								"page"			=> $aco,
								"action_name"	=> $action['name']
							);
						}
					}
					else
					{
						$show = explode(',', $show);

						if(!empty($show) && is_array($show))
						{
							foreach($show as $sh)
							{
								$actionAco[] = array(
									"page"			=> $sh,
									"action_name"	=> $action['name']
								);
							}
						}
					}

					if(isset($actionAco) && !empty($actionAco))
					{
						$this->setTable("action_show");
						$this->insert($actionAco);
						$this->resetTable();
					}

					if (!empty($title))
					{
						$lang_content = array();

						foreach ($this->mLanguages as $lngcode => $lngvalue)
						{
							$lang_content[] = array(
								"key"		=> 'esyndicat_action_'.$action['name'],
								"value"		=> $title,
								"code"		=> $lngcode,
								"lang"		=> $lngvalue,
								"category"	=> 'frontend',
								"plugin"	=> $this->name
							);
						}

						if (!empty($lang_content))
						{
							parent::setTable("language");
							parent::insert($lang_content);
							parent::resetTable();
						}
					}
				}
			}
		}

		/**
		 * Add new phrases
		 */
		if(!empty($this->phrases))
		{
			if(!array_key_exists('en', $this->mLanguages))
			{
				foreach($this->mLanguages as $code => $language)
				{
					foreach($this->phrases as $key => $phrase)
					{
						$this->phrases[$key]['lang'] = $language;
						$this->phrases[$key]['code'] = $code;
					}
				}
			}
			else
			{
				foreach($this->mLanguages as $code => $language)
				{
					if('en' != $code)
					{
						foreach($this->phrases as $key => $phrase)
						{
							$new_phrases[] = array(
								"key"		=> $phrase['key'],
								"value"		=> $phrase['value'],
								"lang"		=> $language,
								"category"	=> $phrase['category'],
								"code"		=> $code,
								"plugin"	=> $this->name
							);
						}
					}
				}
			}

			$this->setTable("language");
			$this->insert($this->phrases);
			
			if(!empty($new_phrases))
			{
				$this->insert($new_phrases);
			}
			
			$this->resetTable();
		}

		/**
		 * Add new config groups
		 */
		if(!empty($this->configGroups))
		{
			$this->setTable("config_groups");
			
			$maxorder = $this->one("MAX(`order`) + 1");
			$maxorder = (NULL == $maxorder) ? 1 : $maxorder;

			foreach($this->configGroups as $config)
			{
				$this->insert($config, array("order" => $maxorder));
				$maxorder++;
			}

			$this->resetTable();
		}

		/**
		 * Add new config settings
		 */
		if(!empty($this->config))
		{
			$this->setTable("config");
			
			$maxorder = $this->one("MAX(`order`) + 1");
			$maxorder = (NULL == $maxorder) ? 1 : $maxorder;

			foreach($this->config as $config)
			{
				$this->insert($config, array("order" => $maxorder));
				$maxorder++;
			}

			$this->resetTable();
		}

		/**
		 * Add new front end pages
		 */
		if(!empty($this->pages))
		{
			$lang_content = array();

			$this->setTable("pages");
			
			$maxorder = $this->one("MAX(`order`) + 1");
			$maxorder = (NULL == $maxorder) ? 1 : $maxorder;

			foreach($this->pages as $page)
			{
				if(isset($page['title']) && !empty($page['title']))
				{
					$title = $page['title'];

					foreach($this->mLanguages as $lngcode => $lngvalue)
					{
						$lang_content[] = array(
							"key"		=> 'page_title_'.$page['name'],
							"value"		=> $title,
							"code"		=> $lngcode,
							"lang"		=> $lngvalue,
							"category"	=> "page",
							"plugin"	=> $this->name
						);
					}
				}

				unset($page['title']);

				if(isset($page['contents']) && !empty($page['contents']))
				{
					$contents = $page['contents'];

					foreach($this->mLanguages as $lngcode => $lngvalue)
					{
						$lang_content[] = array(
							"key"		=> 'page_content_'.$page['name'],
							"value"		=> $contents,
							"code"		=> $lngcode,
							"lang"		=> $lngvalue,
							"category"	=> "page",
							"plugin"	=> $this->name
						);
					}
				}

				unset($page['contents']);
				
				$this->insert($page, array("order" => $maxorder, "last_updated" => "NOW()"));
				$maxorder++;
			}

			$this->resetTable();

			if(isset($lang_content) && !empty($lang_content))
			{
				parent::setTable("language");
				parent::insert($lang_content);
				parent::resetTable();
			}
		}

		/**
		 * Add new fields
		 */
		if(!empty($this->fields))
		{
			require_once(ESYN_CLASSES.'esynUtf8.php');

			esynUtf8::loadUTF8Core();
			esynUtf8::loadUTF8Util('ascii', 'validation', 'bad', 'utf8_to_ascii');

			foreach($this->fields as $field)
			{
				if(!utf8_is_ascii($field['name']))
				{
					$field['name'] = utf8_strip_non_ascii($field['name']);
				}

				$field['name'] = strtolower($field['name']);

				if(!preg_match('/^[a-z0-9\-_]{2,20}$/', $field['name']))
				{
					preg_replace('/^[a-z0-9\-_]{2,20}$/', '//', $field['name']);
				}

				$title = utf8_bad_replace($field['title']);

				unset($field['title']);

				foreach($this->mLanguages as $code => $lang)
				{
					$field['title'][$code] = $title;
				}

				if(empty($field['type']))
				{
					$field['type'] = 'text';
				}
				
				if('text' == $field['type'])
				{
					if (isset($field['length']) && ((int)$field['length'] > 255 || (int)$field['length'] < 1))
					{
						$field['length'] = 100;
					}
					else
					{
						$field['length'] = (int)$field['length'];
					}
				}
				else
				{
					$field['length'] = '';
				}

				$field['searchable'] = isset($field['searchable']) ? (int)$field['searchable'] : 0;

				if(isset($field['show_as']) && in_array($field['show_as'], array("combo", "radio", "checkbox")))
				{
					$field['show_as'] = $field['show_as'];
				}
				else
				{
					unset($field['show_as']);
				}
				
				$field['pages'] = isset($field['pages']) && !empty($field['pages']) ? $field['pages'] : '';

				switch ($field['type'])
				{
					case 'text':
						$field['default'] = $field['default'];
						break;
					case 'textarea':
						$length = '';
						
						if(isset($field['min_length']) && '' != $field['min_length'])
						{
							$length = (int)$field['min_length'];
						}
						
						$length .= ',';
						
						if(isset($field['max_length']) && '' != $field['max_length'])
						{
							$length .= (int)$_POST['max_length'];
						}
						
						$field['length'] = $length;
						
						break;
					case 'image':
						$field['image_height'] = $field['image_height'];
						$field['image_width'] = $field['image_width'];
						$field['thumb_width'] = $field['thumb_width'];
						$field['thumb_height'] = $field['thumb_height'];
						$field['resize_mode'] = isset($field['resize_mode']) && in_array($field['resize_mode'], array('1001', '1002', '1003')) ? $field['resize_mode'] : 1001;
						$field['file_prefix'] = $field['file_prefix'];
						break;
					default:
						if (isset($field['values']) && '' != $field['values'])
						{
							$field['values'] = explode(',', $field['values']);

							foreach($field['values'] as $key => $value)
							{
								if(strtolower($value) == strtolower($field['default']))
								{
									$field['default'] = $key;
								}
							}

							foreach($this->mLanguages as $lkey => $lang)
							{
								if(ESYN_LANGUAGE != $lkey)
								{
									$field['lang_values'][$lkey] = $field['values'];
								}
							}
						}
						break;
				}

				/**
				 * Plans
				 */
				$this->setTable('plans');
				$field['_plans'] = $this->onefield("`id`");
				$this->resetTable();

				/**
				 * Categories
				 */
				$this->setTable('categories');
				$field["categories"][] = $this->one("`id`", "`parent_id` = '-1'");
				$this->resetTable();

				$field["recursive"] = 1;


				/**
				 * Insert field
				 */
				$this->factory("ListingField");

				global $esynListingField;

				$esynListingField->insert($field);
			}
		}

		/**
		 * Add new front end blocks
		 */
		if(!empty($this->blocks))
		{
			$this->setTable("pages");
			$pages = $this->onefield("`name`");
			$this->resetTable();

			$this->setTable("blocks");

			$maxorder = $this->one("MAX(`order`) + 1");
			$maxorder = (NULL == $maxorder) ? 1 : $maxorder;

			foreach($this->blocks as $block)
			{
				$blockPages = $block['pages'];
				$blockExceptPages = $block['pagesexcept'];

				unset($block['pages'], $block['pagesexcept'], $block['added']);
					
				if(isset($block['contents']) && !empty($block['contents']))
				{
					$block['contents'] = str_replace('{PLUGIN}', $this->name, $block['contents']);
				}

				$id = $this->insert($block, array("order" => $maxorder));

				if(!empty($blockPages))
				{
					if(!empty($blockExceptPages))
					{
						$blockExceptPages = explode(',', $blockExceptPages);
					}
					else
					{
						$blockExceptPages = array();
					}

					if('all' == strtolower($blockPages))
					{
						foreach($pages as $page)
						{
							if(!in_array($page, $blockExceptPages))
							{
								$pluginAco[] = array(
									"page"		=> esynSanitize::sql($page),
									"block_id"	=> $id
								);
							}
						}
					}
					else
					{
						$blockPages = explode(',', $blockPages);

						foreach($blockPages as $page)
						{
							if(!in_array($page, $blockExceptPages))
							{
								$pluginAco[] = array(
									"page"		=> esynSanitize::sql($page),
									"block_id"	=> $id
								);
							}
						}
					}

					$this->setTable("block_show");
					$this->insert($pluginAco);
					$this->resetTable();
				}
				
				$maxorder++;
			}

			$this->resetTable();
		}

		/**
		 * Add new hooks
		 */
		if(!empty($this->hooks))
		{
			$this->setTable("hooks");

			$maxorder = $this->one("MAX(`order`) + 1");
			$maxorder = (NULL == $maxorder) ? 1 : $maxorder;

			foreach($this->hooks as $hook)
			{
				$names = explode(',', $hook['name']);
				
				if(!empty($names))
				{
					foreach($names as $name)
					{
						$addit = array();

						if (!isset($hook['order']) || empty($hook['order']))
						{
							$addit = array("order" => $maxorder);

							unset($hook['order']);
						}

						$hook['name'] = $name;

						if(isset($hook['code']) && !empty($hook['code']))
						{
							$hook['code'] = str_replace('{PLUGIN}', $this->name, $hook['code']);
						}

						$this->insert($hook, $addit);
						
						$maxorder++;
					}
				}
			}	
			
			$this->resetTable();
		}

		/**
		 * Run install SQL queries
		 */
		if(!empty($this->installSQL))
		{
			$mysql_ver_data = (ESYN_MYSQLVER == "41") ? "ENGINE=MyISAM DEFAULT CHARSET=utf8" : "TYPE=MyISAM";

			require_once(ESYN_INCLUDES.'pclzip'.ESYN_DS.'pclzip.lib.php');

			foreach($this->installSQL as $sql)
			{
				if($sql['external'])
				{
					$file_path = str_replace('{DIRECTORY_SEPARATOR}', ESYN_DS, $sql['query']);
					$file_full_path = ESYN_PLUGINS . $this->name . ESYN_DS . $file_path;

					$archive = new PclZip($file_full_path);

					$files = $archive->extract(PCLZIP_OPT_PATH, ESYN_TMP);

					if(0 == $files)
					{
						continue;
					}

					foreach($files as $file)
					{
						$sql['query'] = file_get_contents($file['filename']);
						
						$sql['query'] = str_replace("\r\n", '', $sql['query']);
						$sql['query'] = str_replace("\n", '', $sql['query']);

						unset($file['filename']);
					}
				}
				
				if(!empty($sql['query']))
				{
					$sql_query = str_replace('{prefix}', $this->mPrefix, $sql['query']);
					$sql_query = str_replace('{mysql_version}', $mysql_ver_data, $sql_query);

					$this->query($sql_query);
				}
			}
		}

		/**
		 * Run install php code
		 */
		if(!empty($this->installEvalCode))
		{
			eval($this->installEvalCode);
		}
		
		$this->mCacher->clearAll('', true);

		return true;
	}

	/**
	 * setXml 
	 *
	 * Set XML content
	 * 
	 * @param mixed $str the XML content
	 * @access public
	 * @return void
	 */
	function setXml($str)
	{
		$this->xml = $str;
	}

	/**
	 * getFromPath 
	 *
	 * Set XML file by path
	 * 
	 * @param mixed $filePath the path to XML file
	 * @access public
	 * @return void
	 */
	function getFromPath($filePath)
	{
		if(empty($filePath))
		{
			trigger_error("Install XML path wasn't specified", E_USER_ERROR);

			return false;
		}
		$this->xml = file_get_contents($filePath);
	}

	/**
	 * startElement 
	 * 
	 * @param mixed $parser 
	 * @param mixed $name 
	 * @param mixed $attributes 
	 * @access public
	 * @return void
	 */
	function startElement($parser, $name, $attributes)
	{
		$this->level++;

		$this->inTag = $name;

		$this->attributes = $attributes;

		if($this->inTag == 'plugin' && isset($attributes['name']))
		{
			$this->name = $attributes['name'];
		}

		$this->path[] = $name;
	}

	/**
	 * endElement 
	 * 
	 * @param mixed $parser 
	 * @param mixed $name 
	 * @access public
	 * @return void
	 */
	function endElement($parser, $name)
	{
		$this->level--;
		array_pop($this->path);
	}

	/**
	 * charData 
	 * 
	 * @param mixed $parser 
	 * @param mixed $text 
	 * @access public
	 * @return void
	 */
	function charData($parser, $text)
	{
		$text = trim($text);

		/* Hooks */
		if('hook' == $this->inTag)
		{
			$this->hooks[] = array(
				"name"		=> $this->attributes['name'],
				"type"		=> isset($this->attributes['type']) && in_array($this->attributes['type'], array('php', 'html', 'smarty', 'plain')) ? $this->attributes['type'] : 'php',
				"plugin"	=> $this->name,
				"code"		=> $text,
				"file"		=> isset($this->attributes['file']) && !empty($this->attributes['file']) ? $this->attributes['file'] : '',
				"status"	=> "active",
				"order"		=> isset($this->attributes['order']) && '' != $this->attributes['order'] ? $this->attributes['order'] : ''
			);
		}

		/* Install code */
		if(in_array("install", $this->path) && 'code' == $this->inTag)
		{
			$this->installEvalCode = $text;
		}

		/* Uninstall code */
		if(in_array("uninstall", $this->path) && 'code' == $this->inTag)
		{
			$this->uninstallCode = $text;
		}

		/* Upgrade code */
		if(in_array("upgrade", $this->path) && 'code' == $this->inTag)
		{
			$this->upgradeCode = $text;
		}

		/* Admin pages */
		if(in_array('adminpages', $this->path) && 'page' == $this->inTag)
		{
			$temp = array();

			$temp['file']		= isset($this->attributes['file']) ? $this->attributes['file'] : '';
			$temp['block_name']	= isset($this->attributes['block']) ? $this->attributes['block'] : '';
			$temp['title']		= $text;
			$temp['attr']		= isset($this->attributes['attr']) ? $this->attributes['attr'] : '';
			$temp['params']		= isset($this->attributes['params']) ? $this->attributes['params'] : '';
			$temp['aco']		= isset($this->attributes['aco']) ? $this->attributes['aco'] : $this->name;
			$temp['menus']		= isset($this->attributes['menus']) ? $this->attributes['menus'] : '';

			if(isset($this->attributes['order']) && !empty($this->attributes['order']))
			{
				$temp['order'] = $this->attributes['order'];
			}

			if(isset($this->attributes['header_order']) && !empty($this->attributes['header_order']))
			{
				$temp['header_order'] = $this->attributes['header_order'];
			}

			$temp['plugin'] = $this->name;

			$this->adminPages[] = $temp;
		}

		/* Admin blocks */
		if(in_array('adminblocks', $this->path) && 'block' == $this->inTag)
		{
			$this->adminBlocks[] = array(
				"name"		=> $this->attributes['name'],
				"plugin"	=> $this->name,
				"title"		=> $text
			);
		}

		/* eSyndiCat actions */
		if(in_array('actions', $this->path) && 'action' == $this->inTag)
		{
			$this->actions[] = array(
				"name"		=> $text,
				"title"		=> isset($this->attributes['title']) ? $this->attributes['title'] : '',
				"url"		=> isset($this->attributes['url']) ? $this->attributes['url'] : '',
				"order"		=> isset($this->attributes['order']) ? $this->attributes['order'] : '',
				"show"		=> isset($this->attributes['show']) ? $this->attributes['show'] : 'all',
				"plugin"	=> $this->name
			);
		}

		/* Language phrases */
		if(in_array('phrases', $this->path) && 'phrase' == $this->inTag)
		{
			$this->phrases[] = array(
				"key"		=> $this->attributes['key'],
				"value"		=> $text,
				"lang"		=> $this->attributes['lang'],
				"category"	=> $this->attributes['category'],
				"code"		=> $this->attributes['code'],
				"plugin"	=> $this->name
			);
		}

		// tooltips
		if(in_array('tooltips', $this->path) && 'tooltip' == $this->inTag)
		{
			$this->phrases[] = array(
				"key"		=> $this->attributes['key'],
				"value"		=> $text,
				"lang"		=> isset($this->attributes['lang']) ? $this->attributes['lang'] : $this->mLanguages[ESYN_LANGUAGE],
				"category"	=> 'tooltip',
				"code"		=> isset($this->attributes['code']) ? $this->attributes['lang'] : ESYN_LANGUAGE,
				"plugin"	=> $this->name
			);
		}


		/* Fields */
		if(in_array('fields', $this->path) && 'field' == $this->inTag)
		{
			$this->fields[] = array(
				"name"			=> $this->attributes['name'],
				"title"			=> $this->attributes['title'],
				"type"			=> $this->attributes['type'],
				"editor"		=> isset($this->attributes['editor']) ? $this->attributes['editor'] : '0',
				"required"		=> isset($this->attributes['required']) ? $this->attributes['required'] : '0',
				"adminonly"		=> isset($this->attributes['adminonly']) ? $this->attributes['adminonly'] : '0',
				"pages"			=> $this->attributes['pages'],
				"length"		=> isset($this->attributes['length']) ? $this->attributes['length'] : '',
				"section_key"	=> isset($this->attributes['section_key']) ? $this->attributes['section_key'] : '',
				"default"		=> isset($this->attributes['default']) ? $this->attributes['default'] : '',
				"values"		=> isset($this->attributes['values']) ? $this->attributes['values'] : '',
				"tooltip"		=> $text,
				"file_types"	=> isset($this->attributes['file_types']) ? $this->attributes['file_types'] : '',
				"image_height"	=> isset($this->attributes['image_height']) ? $this->attributes['image_height'] : '',
				"image_width"	=> isset($this->attributes['image_width']) ? $this->attributes['image_width'] : '',
				"thumb_height"	=> isset($this->attributes['thumb_height']) ? $this->attributes['thumb_height'] : '',
				"thumb_width"	=> isset($this->attributes['thumb_width']) ? $this->attributes['thumb_width'] : '',
				"file_prefix"	=> isset($this->attributes['file_prefix']) ? $this->attributes['file_prefix'] : 'img_',
				"resize_mode"	=> isset($this->attributes['resize_mode']) ? $this->attributes['resize_mode'] : 1001
			);
		}

		/* Config groups */
		if('configgroup' == $this->inTag)
		{
			$this->configGroups[] = array(
				"name"		=> $this->attributes['name'],
				"plugin"	=> $this->name,
				"title"		=> $text
			);
		}

		/* Config */
		if('config' == $this->inTag)
		{
			$this->config[] = array(
				"group_name"		=> $this->attributes['configgroup'],
				"name"				=> $this->attributes['name'],
				"value"				=> $text,
				"multiple_values"	=> isset($this->attributes['multiplevalues']) ? $this->attributes['multiplevalues'] : '',
				"type"				=> $this->attributes['type'],
				"description"		=> isset($this->attributes['description']) ? $this->attributes['description'] : '',
				"editor"			=> isset($this->attributes['editor']) ? $this->attributes['editor'] : '0',
				"plugin"			=> $this->name
			);
		}

		/* Front end pages */
		if(in_array('pages', $this->path) && 'page' == $this->inTag)
		{
			$this->pages[] = array(
				"name"					=> $this->attributes['name'],
				"title"					=> isset($this->attributes['title']) ? $this->attributes['title'] : '',
				"contents"				=> $text,
				"menus"					=> isset($this->attributes['menus']) ? $this->attributes['menus'] : '',
				"status"				=> in_array($this->attributes['status'], array("active", "inactive"), true) ? $this->attributes['status'] : 'inactive',
				"unique_url"			=> isset($this->attributes['uniqueurl']) ? $this->attributes['uniqueurl'] : '',
				"non_modrewrite_url"	=> isset($this->attributes['nonmodrewriteurl']) ? $this->attributes['nonmodrewriteurl'] : '',
				"nofollow"				=> isset($this->attributes['nofollow']) ? (int)$this->attributes['nofollow'] : 0,
				"readonly"				=> isset($this->attributes['readonly']) ? (int)$this->attributes['readonly'] : 0,
				"plugin"				=> $this->name,
				"group"					=> 'plugins'
			);
		}

		/* Front end blocks */
		if(in_array('blocks', $this->path) && 'block' == $this->inTag)
		{
			$status = 'inactive';

			if(isset($this->attributes['status']))
			{
				$status = in_array($this->attributes['status'], array("active", "inactive"), true) ? $this->attributes['status'] : 'inactive';
			}

			$this->blocks[] = array(
				"title"				=> isset($this->attributes['title']) ? $this->attributes['title'] : '',
				"contents"			=> $text,
				"lang"				=> $this->attributes['lang'],
				"position"			=> $this->attributes['position'],
				"type"				=> $this->attributes['type'],
				"plugin"			=> $this->name,
				"status"			=> $status,
				"show_header"		=> isset($this->attributes['showheader']) ? $this->attributes['showheader'] : 0,
				"collapsible"		=> isset($this->attributes['collapsible']) ? $this->attributes['collapsible'] : 0,
				"sticky"			=> isset($this->attributes['sticky']) ? $this->attributes['sticky'] : 0,
				"multi_language"	=> isset($this->attributes['multilanguage']) ? $this->attributes['multilanguage'] : 1,
				"pages"				=> isset($this->attributes['pages']) ? $this->attributes['pages'] : 'all',
				"pagesexcept"		=> isset($this->attributes['pagesexcept']) ? $this->attributes['pagesexcept'] : '',
				"added"				=> isset($this->attributes['added']) ? $this->attributes['added'] : '',
				"rss"				=> isset($this->attributes['rss']) ? $this->attributes['rss'] : ''
			);
		}

		/* install SQL queries */
		if(in_array('installsql', $this->path) && 'sql' == $this->inTag)
		{
			$this->installSQL[] = array(
				'query'		=> $text,
				'external'	=> isset($this->attributes['external']) ? true : false
			);
		}

		/* upgrade SQL queries */
		if(in_array('upgradesql', $this->path) && 'sql' == $this->inTag)
		{
			if(isset($this->attributes['version']) && !empty($this->attributes['version']))
			{
				$this->upgradeSQL[$this->attributes['version']][] = array(
					'query'		=> $text,
					'external'	=> isset($this->attributes['external']) ? true : false
				);
			}
			else
			{
				$this->upgradeSQL[] = array(
					'query'		=> $text,
					'external'	=> isset($this->attributes['external']) ? true : false
				);
			}
		}

		/* uninstall SQL queries */
		if(in_array('uninstallsql', $this->path) && 'sql' == $this->inTag)
		{
			$this->uninstallSQL[] = array(
				'query'		=> $text,
				'external'	=> isset($this->attributes['external']) ? true : false
			);
		}

		/* htaccess rules */
		if(in_array('htaccess', $this->path) && 'rules' == $this->inTag)
		{
			$this->htaccessRules[] = array(
				"section"	=> $this->attributes['section'],
				"addto"		=> (isset($this->attributes['addto'])) ? $this->attributes['addto'] : '',
				"rules"		=> str_replace("\t", '', trim($text))
			);
		}

		/* Other fields */
		if(in_array($this->inTag, array('version', 'summary', 'title', 'author', 'contributor', 'notes', 'status', 'date', 'compatibility')))
		{
			$this->{$this->inTag} = $text;
		}
	}

	/**
	 * getMessage 
	 * 
	 * Return message string
	 *
	 * @access public
	 * @return string
	 */
	function getMessage()
	{
		return $this->message;
	}

	/**
	 * getNotes 
	 *
	 * Return notes string
	 * 
	 * @access public
	 * @return string
	 */
	function getNotes()
	{
		return $this->notes;
	}

	/**
	 * commentElement 
	 * 
	 * @param mixed $parser 
	 * @param mixed $name 
	 * @access public
	 * @return void
	 */
	function commentElement($parser, $name)
	{
	}

	/**
	 * updateHtaccess 
	 *
	 * saves mod_rewrite rules in .htaccess file if it's writable 
	 * 
	 * @access public
	 * @return bool
	 */
	function updateHtaccess()
	{
		$out = '';
		$htaccessFile = ESYN_HOME.'.htaccess';

		if (is_file($htaccessFile))
		{
			if (is_writable($htaccessFile))
			{
				// build .htaccess file
				$this->setTable("config");
				$all_htaccess = $this->keyvalue("REPLACE(`name`, 'htaccessfile_', ''),`value`", "`name` LIKE 'htaccessfile_%'");
				
				if($all_htaccess)
				{	
					ksort($all_htaccess);
					foreach($all_htaccess as $kline => $vline)
					{
						$out .= str_replace("\r\n", "\n", $vline);
					}
				}
				
				// save to htaccess
				if (!$handle = fopen($htaccessFile, 'w'))
				{
					return false;
				}
				
				if (fwrite($handle, $out) === FALSE)
				{
					return false;
				}
				
				fclose($handle);

				$this->resetTable();
			}
			else
			{
				$this->notes = 'You need to reupload .htaccess file. You can download it <a href="controller.php?file=configuration&amp;download_htaccess">here</a>';

				return false;
			}
		}
		else
		{
			$this->notes = "There is no .htaccess file.";

			return false;
		}

		return false;
	}
}
