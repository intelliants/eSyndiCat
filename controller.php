<?php
//##copyright##

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