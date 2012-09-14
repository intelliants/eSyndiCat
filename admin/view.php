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


defined("ESYN_ADMIN_CLASSES") or die("Forbidden"); 

/*
 * Loading Smarty class
 */
$esynAdmin->loadClass("Smarty");

$esynSmarty = new esynSmarty($esynAdmin->mConfig['tmpl']);
$esynSmarty->mHooks = $esynAdmin->mHooks;

if(!file_exists(ESYN_HOME.'tmp'.ESYN_DS.'admin'))
{
	esynUtil::mkdir('tmp/admin');
}

$esynSmarty->template_dir = ESYN_ADMIN_HOME.'templates'.ESYN_DS.'default'.ESYN_DS;
$esynSmarty->compile_dir = ESYN_HOME.'tmp'.ESYN_DS.'admin';
$esynSmarty->config_dir	= 'configs'.ESYN_DS;
$esynSmarty->cache_dir = ESYN_ADMIN_HOME.'tmp'.ESYN_DS.'smartycache'.ESYN_DS;

$esynSmarty->caching = false;
$esynSmarty->cache_modified_check = true;
$esynSmarty->debugging = false;

$esynSmarty->register_function("preventCsrf", array("esynUtil", "preventCsrf"));

$messages = esynMessages::getMessages();
$esynSmarty->assign_by_ref('messages', $messages);

$esynAdmin->createJsCache();

/*
 * Admin menu items
 */

if(isset($currentAdmin) && !empty($currentAdmin))
{
	/**
	 * Get admin menu items
	 */
	$adminMenu = array();

	$esynAdmin->setTable("admin_blocks");
	$adminBlocks = $esynAdmin->all("*", "1=1 ORDER BY `order`");
	$esynAdmin->resetTable();

	$state = array();

	if (!empty($currentAdmin['state']))
	{
		$state = unserialize($currentAdmin['state']);
	}
	
	if (isset($state['admin_blocks_order']) && !empty($state['admin_blocks_order']))
	{
		$tmp = array();

		foreach ($state['admin_blocks_order'] as $key => $name)
		{
			foreach ($adminBlocks as $j => $block)
			{
				if ($name == $block['name'])
				{
					$tmp[] = $adminBlocks[$j];
				}
			}
		}

		$adminBlocks = $tmp;
	}

	/* create where clause if admin is not super */
	$where = !$currentAdmin['super'] ? "AND `aco` IN ('".join("','", $currentAdmin['permissions'])."')" : "";

	if(!empty($adminBlocks))
	{
		$i = 0;

		/* get menu items */
		$esynAdmin->setTable("admin_pages");

		foreach($adminBlocks as $key => $adminBlock)
		{
			$items = $esynAdmin->all("*", "`block_name` = '{$adminBlock['name']}' AND FIND_IN_SET('main', `menus`) > 0 {$where} ORDER BY `order`");

			if(!empty($items))
			{
				if (empty($state))
				{
					$open = 'open';
				}
				else
				{
					if (isset($state['admin_blocks_close'][$adminBlock['name']]))
					{
						$open = $state['admin_blocks_close'][$adminBlock['name']] == 1 ? 'open' : 'close';
					}
					else
					{
						$open = 'open';
					}
				}
				
				$adminMenu[$i]['text'] = $adminBlock['title'];
				$adminMenu[$i]['name'] = $adminBlock['name'];
				$adminMenu[$i]['open'] = $open;

				foreach($items as $jey => $item)
				{
					$params = array();
					$url = 'controller.php?';

					if(!empty($item['file']))
					{
						$params[] = "file={$item['file']}";
					}
					if(!empty($item['plugin']))
					{
						$params[] = "plugin={$item['plugin']}";
					}
					if(!empty($item['params']))
					{
						$params[] = $item['params'];
					}

					$url .= implode("&amp;", $params);

					$adminMenu[$i]['items'][$jey]['text'] = $item['title'];
					$adminMenu[$i]['items'][$jey]['href'] = $url;
					$adminMenu[$i]['items'][$jey]['aco'] = $item['aco'];

					if(isset($item['attr']) && !empty($item['attr']))
					{
						$adminMenu[$i]['items'][$jey]['attr'] = $item['attr'];
					}
				}
			}

			$i++;
		}

		$esynAdmin->resetTable();
	}

	$esynSmarty->assign('adminMenu', $adminMenu);

	/**
	 * Get admin header menu
	 */
	$esynAdmin->setTable("admin_pages");
	$adminHeaderMenu = $esynAdmin->all("*", "FIND_IN_SET('header', `menus`) > 0 {$where} ORDER BY `header_order` ASC");
	$esynAdmin->resetTable();

	if(!empty($adminHeaderMenu))
	{
		foreach($adminHeaderMenu as $key => $item)
		{
			$params = array();
			$url = 'controller.php?';

			if(!empty($item['file']))
			{
				$params[] = "file={$item['file']}";
			}
			if(!empty($item['plugin']))
			{
				$params[] = "plugin={$item['plugin']}";
			}
			if(!empty($item['params']))
			{
				$params[] = $item['params'];
			}

			$url .= implode("&amp;", $params);

			$adminHeaderMenu[$key]['text'] = $item['title'];
			$adminHeaderMenu[$key]['href'] = $url;

			if(isset($item['attr']) && !empty($item['attr']))
			{
				$adminHeaderMenu[$key]['attr'] = $item['attr'];
			}
		}

		$esynSmarty->assign('adminHeaderMenu', $adminHeaderMenu);
	}
}

if(defined("ESYN_REALM"))
{
	/*
	 * Get header tabs
	 */
	$esynAdmin->setTable("tabs");

	$sql = "SELECT `tabs`.* FROM `{$esynAdmin->mPrefix}tabs` `tabs` ";
	$sql .= "LEFT JOIN `{$esynAdmin->mPrefix}tab_pages` `tab_pages` ";
	$sql .= "ON `tab_pages`.`tab_name` = `tabs`.`name` ";
	$sql .= "WHERE `tabs`.`sticky` = 1 OR `tab_pages`.`page_name` = '".ESYN_REALM."' ";
	$sql .= "ORDER BY `tabs`.`order` ASC";

	$esyn_tabs = $esynAdmin->getAll($sql);

	$esynAdmin->resetTable();

	if(!empty($esyn_tabs))
	{
		$esynSmarty->assign('esyn_tabs', $esyn_tabs);
	}
}

$esyn_top_notices = array();

/*
 * Check the install folder
 */
if(file_exists(ESYN_HOME . 'install' . ESYN_DS . 'index.php'))
{
	$esyn_top_notices[] = $esynI18N['installer_not_removed'];
}

if(file_exists(ESYN_ADMIN_HOME . 'service.php'))
{
	$esyn_top_notices[] = sprintf($esynI18N['service_login_enabled'], ESYN_ADMIN_HOME);
}


/*
 * Check the includes/config.inc.php permission
 */
if(esynUtil::checkUid() && is_writeable(ESYN_INCLUDES . 'config.inc.php'))
{
	$esyn_top_notices[] = $esynI18N['config_writable'];
}

if(!empty($esyn_top_notices))
{
	$esyndicat_messages[] = array(
		'type'	=> 'alert',
		'msg'	=> $esyn_top_notices
	);
}

/*
 * Build breadcrumb
 */
if(empty($category))
{
	// hack to display the correct breadcrumb on suggest category page
	// the $category variable is used for creating breadcrumb
	// and it is used on suggest category page and can't be changed
	if(defined("ESYN_REALM") && 'create_category' == ESYN_REALM && isset($parent))
	{
		$breadcrumb_category = $parent;
	}
	else
	{
		$breadcrumb_category = array("id" => 0);
	}
}
else
{
	$breadcrumb_category = $category;
}

if (isset($gNoBc) && !$gNoBc)
{
	$gBc[0]['title'] = $gBc[0]['title'] ? $gBc[0]['title'] : $gTitle;

	$breadcrumb = esynView::printBreadcrumb($breadcrumb_category['id'], $gBc);
}
else
{
	$breadcrumb = '';
}

$esynSmarty->assign('breadcrumb', $breadcrumb);

if(empty($url))
{
	$url = '';
}

if(!empty($actions))
{
	$esynSmarty->assign('actions', $actions);
}

if(isset($gTitle) && !empty($gTitle))
{
	$esynSmarty->assign('gTitle', $gTitle);
}

$esynSmarty->assign('esynI18N', $esynI18N);
$esynSmarty->assign('currentAdmin', $currentAdmin);
$esynSmarty->assign('langs', $esynAdmin->mLanguages);

$esynSmarty->assign_by_ref('esyndicat_messages', $esyndicat_messages);

$esynSmarty->assign_by_ref('config', $esynAdmin->mConfig);

?>
