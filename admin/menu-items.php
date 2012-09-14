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


require_once('.'.DIRECTORY_SEPARATOR.'header.php');

$esynAdmin->loadClass("JSON");

$json = new Services_JSON();

$block_names = isset($_GET['menu']) && !empty($_GET['menu']) ? $_GET['menu'] : array();

/**
 * Get admin menu items
 */
$adminMenuItems = array();

if(isset($currentAdmin) && !empty($currentAdmin))
{
	/* create where clause if admin is not super */
	$where = !$currentAdmin['super'] ? "AND `aco` IN ('".join("','", $currentAdmin['permissions'])."')" : "";

	$i = 0;

	if(!empty($block_names))
	{
		/* get menu items */
		$esynAdmin->setTable("admin_pages");

		foreach($block_names as $block_name)
		{
			$items = $esynAdmin->all("*", "`block_name` = '{$block_name}' AND FIND_IN_SET('main', `menus`) > 0 {$where}");

			if(!empty($items))
			{
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

					$icon = ESYN_ADMIN_FOLDER.'/templates/'.$esynConfig->getConfig('admin_tmpl').'/img/icons/menu/'.$item['aco'].'.png';

					if(!is_file(ESYN_HOME.$icon) || !file_exists(ESYN_HOME.$icon))
					{
						$icon = ESYN_ADMIN_FOLDER.'/templates/'.$esynConfig->getConfig('admin_tmpl').'/img/icons/menu/default.png';
					}

					$style = 'style="background-image: url(\''.ESYN_URL.$icon.'\');"';

					$url .= implode("&amp;", $params);

					$adminMenuItems[$block_name][$jey]['text'] = $item['title'];
					$adminMenuItems[$block_name][$jey]['href'] = $url;
					$adminMenuItems[$block_name][$jey]['aco'] = $item['aco'];
					$adminMenuItems[$block_name][$jey]['style'] = $style;

					if(isset($item['attr']) && !empty($item['attr']))
					{
						$adminMenuItems[$block_name][$jey]['attr'] = $item['attr'];
					}
				}

				$i++;
			}
		}

		$esynAdmin->resetTable();
	}
}

echo $json->encode($adminMenuItems);

exit;

?>
