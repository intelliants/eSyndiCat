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


require_once('.'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'header.php');

$plugin = esynSanitize::sql($_GET['plugin']);

if(!empty($plugin))
{
	$file = (isset($_GET['file']) && !empty($_GET['file'])) ? esynSanitize::paranoid($_GET['file']) : 'index';
	$plugin = esynSanitize::paranoid($_GET['plugin']);

	if(!in_array($plugin, $eSyndiCat->mPlugins))
	{
		$_GET['error'] = "404";
		include(ESYN_HOME."error.php");
		exit;
	}

	if(is_file(ESYN_HOME.'plugins'.ESYN_DS.$plugin.ESYN_DS.$file.'.php'))
	{
		define('ESYN_CURRENT_PLUGIN', $plugin);
		define('ESYN_PLUGIN_TEMPLATE', ESYN_HOME.'plugins'.ESYN_DS.ESYN_CURRENT_PLUGIN.ESYN_DS.'templates'.ESYN_DS);

		require_once(ESYN_HOME.'plugins'.ESYN_DS.$plugin.ESYN_DS.$file.'.php');
	}
	else
	{
		$_GET['error'] = "404";
		include(ESYN_HOME."error.php");
		exit;
	}
}
else
{
	$_GET['error'] = "404";
	include(ESYN_HOME."error.php");
	exit;
}