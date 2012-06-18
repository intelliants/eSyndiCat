<?php
//##copyright##

require_once('.'.DIRECTORY_SEPARATOR.'header.php');

if(isset($_GET['plugin']))
{
	$_plugin = esynSanitize::paranoid($_GET['plugin']);
}

if(isset($_GET['file']))
{
	$file = esynSanitize::paranoid($_GET['file']);
}
else
{
	$file = 'index';
}

if(!empty($file))
{
	$esynAdmin->setTable("admin_pages");
	$fileExist = $esynAdmin->exists("`file` = :file", array('file' => $file));
	$esynAdmin->resetTable();

	if($fileExist)
	{
		$includefile = (!empty($_plugin)) ? ESYN_HOME.'plugins'.ESYN_DS.$_plugin.ESYN_DS.'admin'.ESYN_DS.$file.'.php' : ESYN_ADMIN_HOME.$file.'.php';

		if(is_file($includefile) && file_exists($includefile))
		{
			if(!empty($_plugin))
			{
				define('ESYN_CURRENT_PLUGIN', $_plugin);
				define('ESYN_PLUGIN_TEMPLATE_URL', ESYN_URL.'plugins'.ESYN_DS.ESYN_CURRENT_PLUGIN.ESYN_DS.'admin'.ESYN_DS.'templates'.ESYN_DS);
				define('ESYN_PLUGIN_TEMPLATE', ESYN_HOME.'plugins'.ESYN_DS.ESYN_CURRENT_PLUGIN.ESYN_DS.'admin'.ESYN_DS.'templates'.ESYN_DS);
			}
			$gBc[0]['title'] = $esynI18N['manage_plugins'];
			$gBc[0]['url'] = 'controller.php?file=plugins';

			require_once($includefile);
		}
		else
		{
			$msg = "Cannot find the following file: {$includefile}";
		
			$gNoBc = true;
			$gTitle = 'Error';
			
			require_once('.'.DIRECTORY_SEPARATOR.'view.php');
			
			$esynSmarty->assign('gTitle', 'Error');
			$esynSmarty->assign('error', $msg);
			$esynSmarty->display('error.tpl');
		}
	}
	else
	{
		$msg = "The file parameter is wrong. Please check the URL.";
		
		$gNoBc = true;
		$gTitle = 'Error';
		
		require_once('.'.DIRECTORY_SEPARATOR.'view.php');
		
		$esynSmarty->assign('gTitle', 'Error');
		$esynSmarty->assign('error', $msg);
		$esynSmarty->display('error.tpl');
	}
}
else
{
	$msg = "The file parameter is empty. Please check the URL.";

	$gNoBc = true;
	$gTitle = 'Error';

	require_once('.'.DIRECTORY_SEPARATOR.'view.php');
	
	$esynSmarty->assign('gTitle', 'Error');
	$esynSmarty->assign('error', $msg);
	$esynSmarty->display('error.tpl');
}

?>
