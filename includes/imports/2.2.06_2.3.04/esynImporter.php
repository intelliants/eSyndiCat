<?php
//##copyright##

/**
 * esynImporter 
 * 
 * @uses esynAdmin
 * @package 
 * @version $id$
 */
class esynImporter extends esynAdmin
{
	var $dbObject	= null;

	var $dbLink		= null;

	var $mXml		= null;

	var $mQueries	= array();

	var $mReport	= array();

	var $mMsg		= array();

	var $tmpPrefix	= 'esyntmp_';

	/**
	 * Description of the Variable
	 * @var		array
	 * @access	public
	 */
	var $admins		= array();

	/**
	 * Description of the Variable
	 * @var		mixed
	 * @access	public
	 */
	var $version	= '2.3.04';

	/**
	 * doImport 
	 * 
	 * @access public
	 * @return void
	 */
	function doImport()
	{
		global $esynI18N;

		$this->dbObject = new esynAdmin();
		
		$this->dbObject->mConfig['dbhost'] = isset($_POST['host']) ? $_POST['host'] : '';
		$this->dbObject->mConfig['dbport'] = '3306';
		$this->dbObject->mConfig['dbuser'] = isset($_POST['username']) ? $_POST['username'] : '';
		$this->dbObject->mConfig['dbpass'] = isset($_POST['password']) ? $_POST['password'] : '';
		$this->dbObject->mConfig['dbname'] = isset($_POST['database']) ? $_POST['database'] : '';
		$this->dbObject->mPrefix = isset($_POST['prefix']) ? $_POST['prefix'] : '';

		$result = $this->checkConnection();

		if(!$result)
		{
			$this->msg = $esynI18N['database_connection_error'];

			return false;
		}

		$result = $this->selectDatabase();

		if(!$result)
		{
			$this->msg = $esynI18N['database_selection_error'];

			return false;
		}

		$this->dbObject->connect();

		$result = $this->parseXml();

		if(!$result)
		{
			$this->msg = $esynI18N['xml_parse_error'];

			return false;
		}

		if(empty($this->mQueries))
		{
			$this->msg = $esynI18N['xml_file_empty'];

			return false;
		}

		$this->copyTables();

		$this->runQueries();

		$this->getOldAdmins();

		$this->removeCurrentTables();
		$this->renameConvertedTables();

		$this->adaptation();
		
		$this->applyPatches();

		$this->mMsg = $esynI18N['convert_database_complete'];

		return true;
	}

	function adaptation()
	{
		global $esynConfig;

		if(!empty($this->admins))
		{
			$this->setTable("admins");
			
			foreach($this->admins as $admin)
			{
				if(isset($admin['id']))
				{
					unset($admin['id']);
				}

				if($this->exists("`username` = '{$admin['username']}'"))
				{
					$this->delete("`username` = '{$admin['username']}'");
				}
				
				$this->insert($admin);
			}
			
			$this->resetTable();
		}

		// change admin status value to inactive
		$this->setTable("admins");
		$this->update(array("status" => "inactive"), "`status` = 'approval'");
		$this->resetTable();

		// change block status value to inactive
		$this->setTable("blocks");
		$this->update(array("status" => "inactive"), "`status` = 'approval'");
		$this->resetTable();

		// change hooks status value to inactive
		$this->setTable("hooks");
		$this->update(array("status" => "inactive"), "`status` = 'approval'");
		$this->resetTable();

		// change listing_fields.pages values
		$this->setTable("listing_fields");

		$pages = array('1' => 'suggest', '2' => 'view');
		
		$fields = $this->all("*");

		if(!empty($fields))
		{
			foreach($fields as $field)
			{
				$new_menus = array();
				$menu = explode(',', $field['old_pages']);

				foreach($menu as $mn)
				{
					if(isset($pages[$mn]))
					{
						$new_menus[] = $pages[$mn];
					}
				}

				$new_menus = implode(',', $new_menus);

				$this->update(array("pages" => $new_menus), "`id` = '{$field['id']}'");
			}
		}

		$this->query("ALTER TABLE `{$this->mPrefix}listing_fields` DROP `old_pages`");

		$this->resetTable();

		// change pages.menus values
		$this->setTable("pages");

		$menus = array('1' => 'main', '2' => 'bottom', '3' => 'account', '4' => 'inventory');
		
		$pages = $this->all("*");

		if(!empty($pages))
		{
			foreach($pages as $page)
			{
				$new_menus = array();
				$menu = explode(',', $page['old_menus']);

				foreach($menu as $mn)
				{
					if(isset($menus[$mn]))
					{
						$new_menus[] = $menus[$mn];
					}
				}

				$new_menus = implode(',', $new_menus);

				$this->update(array("menus" => $new_menus), "`id` = '{$page['id']}'");
			}
		}

		// change the menus
		$this->update(array('menus' => ''), "`name` = 'account_login'");
		$this->update(array('menus' => ''), "`name` = 'account_logout'");
		$this->update(array('menus' => ''), "`name` = 'payment_canceled'");
		$this->update(array('menus' => ''), "`name` = 'payment_complete'");
		$this->update(array('menus' => ''), "`name` = 'purchase'");
		$this->update(array('menus' => ''), "`name` = 'purchase_complete'");
		$this->update(array('menus' => ''), "`name` = 'account_register'");
		$this->update(array('menus' => ''), "`name` = 'thank'");
		$this->update(array('menus' => ''), "`name` = 'view_listing'");
		$this->update(array('menus' => ''), "`name` = 'edit_listing'");
		$this->update(array('menus' => ''), "`name` = 'account_password_forgot'");
		$this->update(array('menus' => ''), "`name` = 'error'");
		$this->update(array('menus' => ''), "`name` = 'index_browse'");

		// remove the old_menus field
		$this->query("ALTER TABLE `{$this->mPrefix}pages` DROP `old_menus`");

		// rename the home realm
		$this->update(array('name' => 'index'), "`name` = 'home'");

		// update the group of pages
		$this->update(array('group' => 'accounts'), "`name` = 'edit_account'");
		$this->update(array('group' => 'accounts'), "`name` = 'accounts'");

		$this->update(array('name' => 'new_listings'), "`name` = 'new-listings'");
		$this->update(array('name' => 'top_listings'), "`name` = 'top-listings'");
		$this->update(array('name' => 'popular_listings'), "`name` = 'popular-listings'");
		$this->update(array('name' => 'random_listings'), "`name` = 'random-listings'");
		$this->update(array('name' => 'suggest_listing'), "`name` = 'suggest-listing'");
		$this->update(array('name' => 'account_listings'), "`name` = 'account-listings'");
		$this->update(array('name' => 'favorites_listings'), "`name` = 'favorites-listings'");
		$this->update(array('name' => 'suggest_category'), "`name` = 'suggest-category'");

		$this->update(array('group' => 'listings'), "`name` = 'search'");
		$this->update(array('group' => 'listings'), "`name` = 'new_listings'");
		$this->update(array('group' => 'listings'), "`name` = 'top_listings'");
		$this->update(array('group' => 'listings'), "`name` = 'popular_listings'");
		$this->update(array('group' => 'listings'), "`name` = 'random_listings'");
		$this->update(array('group' => 'listings'), "`name` = 'suggest_listing'");
		$this->update(array('group' => 'listings'), "`name` = 'account_listings'");
		$this->update(array('group' => 'listings'), "`name` = 'favorites_listings'");

		$this->update(array('group' => 'pages'), "`name` = 'about'");
		$this->update(array('group' => 'pages'), "`name` = 'policy'");
		$this->update(array('group' => 'pages'), "`name` = 'terms'");
		$this->update(array('group' => 'pages'), "`name` = 'help'");
		$this->update(array('group' => 'pages'), "`name` = 'advertise'");
		
		$this->update(array('group' => 'miscellaneous'), "`name` = 'index'");
		$this->update(array('group' => 'miscellaneous'), "`name` = 'suggest_category'");

		// change pages.status values
		$this->update(array("status" => "inactive"), "`status` = 'approval'");
		$this->resetTable();

		// change ID for ROOT category
		$this->setTable("categories");

		$this->delete("`parent_id` = '-1' AND `id` > '0'");
		
		$root_category = $this->row("*", "`parent_id` = '-1'");
		
		if($root_category)
		{
			if('0' != $root_category['id'])
			{
				$this->update(array("id" => "0"), "`parent_id` = '-1'");
			}
		}
		else
		{
			$this->insert(array("title" => 'ROOT', "status" => 'active', "level" => '0', "parent_id" => '-1'));
			$this->update(array("id" => "0"), "`parent_id` = '-1'");
		}

		$this->resetTable();

		// change language phrases
		$this->setTable("language");

		$this->update(array("value" => "Edit Admin"), "`key` = 'edit_admin' AND `code` = 'en'");
		$this->update(array("value" => "New admin successfully created."), "`key` = 'admin_added' AND `code` = 'en'");
		$this->update(array("value" => 'No categories. <a href="controller.php?file=suggest-category&amp;id=[category_id]" style="font-weight: bold;">Click here</a> to create category.'), "`key` = 'no_categories' AND `code` = 'en'");
		$this->update(array("value" => "Check Consistency"), "`key` = 'check_consistency' AND `code` = 'en'");
		$this->update(array("value" => "optimize tables"), "`key` = 'optimize_tables' AND `code` = 'en'");
		$this->update(array("value" => "Title incorrect."), "`key` = 'title_incorrect' AND `code` = 'en'");
		$this->update(array("value" => "Could not open file with sql instructions: {filename}."), "`key` = 'cant_open_sql' AND `code` = 'en'");
		$this->update(array("category" => "common"), "`key` = 'payment_notif' AND `code` = 'en'");
		$this->update(array("category" => "common"), "`key` = 'restore_password' AND `code` = 'en'");
		$this->update(array("value" => "Passwords do not match."), "`key` = 'error_password_match' AND `code` = 'en'");
		$this->update(array("value" => "Create Category"), "`key` = 'create_category' AND `code` = 'en'");
		$this->update(array("value" => "Create Admin"), "`key` = 'create_admin' AND `code` = 'en'");
		$this->update(array("value" => "Allowed file types (comma separated values (don't include point), example: pdf, doc, odf, mov)"), "`key` = 'file_types' AND `code` = 'en'");
		$this->update(array("value" => "Add Plan"), "`key` = 'add_plan' AND `code` = 'en'");
		$this->update(array("value" => "Edit Plan"), "`key` = 'edit_plan' AND `code` = 'en'");
		$this->update(array("value" => "Search Listings"), "`key` = 'search_listings' AND `code` = 'en'");
		$this->update(array("value" => "Sponsored Listings"), "`key` = 'sponsored_listings' AND `code` = 'en'");
		$this->update(array("value" => "Search Account"), "`key` = 'search_account' AND `code` = 'en'");
		$this->update(array("value" => "Create Account"), "`key` = 'create_account' AND `code` = 'en'");
		$this->update(array("value" => "Active Listings"), "`key` = 'active_listings' AND `code` = 'en'");
		$this->update(array("value" => "Approval Categories"), "`key` = 'approval_categories' AND `code` = 'en'");
		$this->update(array("value" => "Active Categories"), "`key` = 'active_categories' AND `code` = 'en'");
		$this->update(array("value" => "Are you sure you want to delete this category? All its subcategories and listings will be deleted too!"), "`key` = 'are_you_sure_to_delete_this_category' AND `code` = 'en'");
		$this->update(array("value" => "Are you sure you want to delete selected categories? All its subcategories and listings will be deleted too!"), "`key` = 'are_you_sure_to_delete_selected_categories' AND `code` = 'en'");
		$this->update(array("value" => "Are you sure you want to delete selected administrators?"), "`key` = 'are_you_sure_to_delete_selected_admins' AND `code` = 'en'");
		$this->update(array("value" => "Are you sure you want to delete this listing field? Note: Whole column including its data will be deleted!"), "`key` = 'are_you_sure_to_delete_this_listing_field' AND `code` = 'en'");
		$this->update(array("value" => "Are you sure you want to delete selected listings? Note: There is no Undo!"), "`key` = 'are_you_sure_to_delete_selected_listings' AND `code` = 'en'");
		$this->update(array("value" => "Bad country - ISO code."), "`key` = 'bad_iso' AND `code` = 'en'");
		$this->update(array("value" => "Copy default language [<b> [lang] </b>] to "), "`key` = 'copy_default_language_to' AND `code` = 'en'");
		$this->update(array("value" => "Are you sure you want to delete selected language? Note: All phrases will be deleted. There is no undo!"), "`key` = 'are_you_sure_to_delete_selected_language' AND `code` = 'en'");
		$this->update(array("value" => "Rate this {rate}"), "`key` = 'rate_this' AND `code` = 'en'");

		$this->update(array("category" => "common"), "`key` = 'statistics'");

		$this->delete("`key` = 'no_top_menu'");

		$this->resetTable();

		// adapt the config table
		$this->setTable("config");

		$this->update(array("group_name" => 'general'), "`group_id` = '1'");
		$this->update(array("group_name" => 'email_templates'), "`group_id` = '2'");
		$this->update(array("group_name" => 'financial'), "`group_id` = '3'");
		$this->update(array("group_name" => 'categories'), "`group_id` = '21'");
		$this->update(array("group_name" => 'cronjob'), "`group_id` = '8'");
		$this->update(array("group_name" => 'listings'), "`group_id` = '20'");
		$this->update(array("group_name" => 'listing_checking'), "`group_id` = '9'");
		$this->update(array("group_name" => 'admin_panel'), "`group_id` = '18'");
		$this->update(array("group_name" => 'accounts'), "`group_id` = '12'");
		$this->update(array("group_name" => 'mail'), "`group_id` = '15'");
		$this->update(array("group_name" => 'captcha'), "`group_id` = '19'");

		$this->update(array("type" => 'hidden'), "`name` = 'tmpl'");
		$this->update(array('multiple_values' => "'html','plaintext'"), "`name` = 'mimetype'");
		$this->update(array('multiple_values' => "'alphabetic','date','clicks','rank'", 'value' => "alphabetic"), "`name` = 'listings_sorting'");

		$this->delete("`group_id` = '10'");
		$this->delete("`value` = 'Templates'");

		$this->resetTable();

		// adapt the config_groups table
		$this->setTable("config_groups");

		$this->update(array("name" => 'general'), "`id` = '1'");
		$this->update(array("name" => 'email_templates'), "`id` = '2'");
		$this->update(array("name" => 'financial'), "`id` = '3'");
		$this->update(array("name" => 'categories'), "`id` = '21'");
		$this->update(array("name" => 'cronjob'), "`id` = '8'");
		$this->update(array("name" => 'listings'), "`id` = '20'");
		$this->update(array("name" => 'listing_checking'), "`id` = '9'");
		$this->update(array("name" => 'admin_panel'), "`id` = '18'");
		$this->update(array("name" => 'accounts'), "`id` = '12'");
		$this->update(array("name" => 'mail'), "`id` = '15'");
		$this->update(array("name" => 'captcha'), "`id` = '19'");

		$this->delete("`id` = '10'");
		$this->delete("`name` = ''");
		
		$this->resetTable();

		// disable all plugins
		$this->setTable("plugins");
		$this->update(array('status' => 'inactive'));
		$this->resetTable();

		// disable hooks
		$this->setTable("hooks");
		$this->update(array('status' => 'inactive'));

		$hook['name'] = 'bootstrap';
		$hook['code'] = "global \$eSyndiCat, \$esynConfig, \$esynSmarty, \$id;\n\n\$eSyndiCat->factory(\"Listing\");\n\nglobal \$esynListing;\n\n/** get sponsored listings **/\nif (\$esynConfig->getConfig('sponsored_listings'))\n{\n	\$esynSmarty->assign('sponsored_listings', \$esynListing->getSponsored(\$id, 0, \$esynConfig->getConfig('num_sponsored_display')));\n}\n\n\$esynSmarty->assign('featured_listings', \$esynListing->getFeatured(\$id, 0, \$esynConfig->getConfig('num_featured_display')));\n\$esynSmarty->assign('partner_listings', \$esynListing->getPartner(\$id, 0, \$esynConfig->getConfig('num_partner_display')));";
		$hook['status'] = 'active';
		$hook['order'] = '5';

		$this->insert($hook);

		$this->resetTable();

		// set config
		$esynConfig->setConfig('tmpl', 'cleancss', true);
		$esynConfig->setConfig('version', $this->version, true);

		// disable plugins blocks
		$this->setTable("blocks");
		$this->update(array('status' => 'inactive'), "`plugin` != ''");
		$this->resetTable();

		// phrase bug
		$this->setTable("admin_blocks");
		$this->update(array('title' => 'Categories'), "`name` = 'categories'");
		$this->resetTable();

		// add kcaptcha plugin
		$plugin['name'] = 'kcaptcha';
		$plugin['author'] = 'Sergey Ten';
		$plugin['contributor'] = 'Intelliants LLC';
		$plugin['title'] = 'KCaptcha';
		$plugin['status'] = 'active';
		$plugin['version'] = '1.0';
		$plugin['summary'] = 'Plugin will add the CAPTCHA to all pages where it needs.';
		$plugin['uninstall_code'] = "global \$esynConfig;\n\n				\$current_value = \$esynConfig->getConfig('captcha_name');\n\n				\$esynConfig->setTable('config');\n				\$current_values = \$esynConfig->one(\"`multiple_values`\", \"`name` = 'captcha_name'\");\n				\$esynConfig->resetTable();\n\n				\$values = explode('','', \$current_values);\n\n				if(!empty(\$values))\n				{\n					foreach(\$values as \$key => \$value)\n					{\n						if('kcaptcha' == \$value)\n						{\n							unset(\$values[\$key]);\n						}\n					}\n				}\n\n				\$updated_values = join('','', \$values);\n\n				\$esynConfig->setTable('config');\n				\$esynConfig->update(array('multiple_values' => \$updated_values), \"`name` = 'captcha_name'\");\n				\$esynConfig->resetTable();";

		$this->setTable("plugins");
		$this->insert($plugin, array(), array('date' => 'NOW()'));
		$this->resetTable();

		// remove the useless fields of transactions table
		$transaction_fields = array();
		$remove_fields = array('card_holder_name', 'country', 'city', 'state', 'street', 'phone');
		
		$fields = $this->getFields($this->mPrefix . 'transactions');

		if(!empty($fields))
		{
			foreach($fields as $field)
			{
				$transaction_fields[] = $field['Field'];
			}

			foreach($remove_fields as $remove_field)
			{
				if(in_array($remove_field, $transaction_fields))
				{
					$this->query("ALTER TABLE `{$this->mPrefix}transactions` DROP `{$remove_field}`");
				}
			}
		}
	}

	function removeCurrentTables()
	{
		$tables = $this->getTables();

		if(!empty($tables))
		{
			foreach($tables as $table)
			{
				$this->query("DROP TABLE `{$table}`;");
			}
		}
	}

	function renameConvertedTables()
	{
		$tables = $this->getTables($this->tmpPrefix);

		if(!empty($tables))
		{
			foreach($tables as $table)
			{
				$new_table_name = str_replace($this->tmpPrefix, ESYN_DBPREFIX, $table);

				$this->query("RENAME TABLE `{$table}` TO `{$new_table_name}`");
			}
		}
	}

	function copyTables()
	{
		$tables = $this->dbObject->getTables();

		if(!empty($tables))
		{
			foreach($tables as $table)
			{
				$new_table_name = str_replace($this->dbObject->mPrefix, $this->tmpPrefix, $table);

				$this->dbObject->query("CREATE TABLE `". ESYN_DBNAME ."`.`{$new_table_name}` LIKE `{$this->dbObject->mConfig['dbname']}`.`{$table}`;");
				$this->dbObject->query("INSERT INTO `". ESYN_DBNAME ."`.`{$new_table_name}` SELECT * FROM `{$this->dbObject->mConfig['dbname']}`.`{$table}`;");
			}
		}
	}

	function getOldAdmins()
	{
		$this->setTable("admins");
		$this->admins = $this->all("*");
		$this->resetTable();
	}

	function runQueries()
	{
		foreach($this->mQueries['import']['sql'] as $key => $sql)
		{
			$query = str_replace('{prefix}', $this->tmpPrefix, $sql['query']);

			$result = $this->query($query);

			$this->mReport[] = array(
				'success'	=> $result,
				'msg'		=> $sql['notes']
			);
		}
	}

	function parseXml()
	{
		require_once(ESYN_INCLUDES.'xml2array'.ESYN_DS.'xml2array.php');

		if(!file_exists(ESYN_INCLUDES.'imports'.ESYN_DS.'2.2.06_2.3.04'.ESYN_DS.'import.xml'))
		{
			return false;
		}

		$this->mQueries = xml2array(file_get_contents(ESYN_INCLUDES.'imports'.ESYN_DS.'2.2.06_2.3.04'.ESYN_DS.'import.xml', false));

		return true;
	}

	function checkConnection()
	{
		$this->dbLink = @mysql_connect($this->dbObject->mConfig['dbhost'].":".$this->dbObject->mConfig['dbport'], $this->dbObject->mConfig['dbuser'], $this->dbObject->mConfig['dbpass']);
		
		if(!$this->dbLink)
		{
			return false;
		}

		return true;
	}

	function selectDatabase()
	{
		$select = @mysql_select_db($this->dbObject->mConfig['dbname'], $this->dbLink);

		if(!$select)
		{
			return false;
		}

		return true;
	}

	function getMsg()
	{
		return $this->mMsg;
	}

	function getReport()
	{
		return $this->mReport;
	}
	
	function execSqlByFile($filename)
	{
		$result = false;
		
		if (!($f = fopen ($filename, "r" )))
		{
			$error = true;
		}
		else
		{
			$sql = '';
			
			while ($s = fgets ($f, 10240))
			{
				$s = trim($s);
				
				if(!empty($s))
				{
					if($s[0] == '#' || $s[0] == '')
					{
						continue;
					}
				}
				else
				{
					continue;
				}
	
				if ( $s[strlen($s)-1] == ';' )
				{
					$sql .= $s;
				}
				else
				{
					$sql .= $s;
					continue;
				}
				
				$sql = str_replace("{prefix}", $this->mPrefix, $sql);
	
				$result = $this->query($sql);
				
				$sql = "";
			}
			fclose($f);
		}
		
		return $result;
	}
	
	function applyPatches()
	{
		$dir = ESYN_INCLUDES . 'imports' . ESYN_DS . 'v2.2.06_2.3.04' . ESYN_DS . 'patches' . ESYN_DS;
		
		$directory = opendir($dir);
	
		while (false !== ($file = readdir($directory)))
		{
			if (substr($file, 0, 1) != ".")
			{
				if (is_file($dir . $file))
				{
					$this->execSqlByFile($dir . $file);
				}
			}
		}
		
		closedir($directory);
	}
}

?>

