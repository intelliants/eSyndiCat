<?php
//##copyright##

define("ESYN_REALM", "importer");

esynUtil::checkAccess();

$error = false;
$msg = array();
$report = array();

$importers_path = ESYN_INCLUDES.'imports'.ESYN_DS;

/**
 * ACTIONS
 */
if(isset($_POST['start']))
{
	$importer_name = $_POST['importer'];

	if(file_exists($importers_path.$importer_name))
	{
		require_once($importers_path.$importer_name.ESYN_DS.'esynImporter.php');

		$esynImporter = new esynImporter();

		$success = $esynImporter->doImport();
		$msg = $esynImporter->getMsg();
		$report = $esynImporter->getReport();

		$error = !$success;
		
		esynMessages::setMessage($msg, $error);
	}
}

if(isset($_GET['action']))
{
	$esynAdmin->loadClass("JSON");

	$json = new Services_JSON();

	if('getdatabases' == $_GET['action'])
	{
		$databases = $esynAdmin->getDatabases();

		if(empty($databases))
		{
			$databases = '';
		}

		echo $json->encode($databases);
	}

	exit;
}

/**
 * ACTIONS
 */

$importers = array();

$gNoBc = false;

$gBc[0]['title'] = $esynI18N['manage_importer'];
$gBc[0]['url'] = 'controller.php?file=importer';

$gTitle = $esynI18N['manage_importer']; 

require_once(ESYN_ADMIN_HOME.'view.php');

if(file_exists($importers_path))
{
	if(is_dir($importers_path))
	{
		$files = scandir($importers_path);

		foreach($files as $file)
		{
			if (substr($file, 0, 1) != ".")
			{
				if (is_dir($importers_path.$file))
				{
					$importers[] = $file;
				}
			}
		}
	}
}
else
{
	$notifications = array(
		'type' => 'error',
		'msg' => $esynI18N['no_importers']
	);

	$esynSmarty->assign('notifications', $notifications);
}

if(isset($success))
{
	$esynSmarty->assign('success', $success);
}

if(isset($report) && !empty($report))
{
	$esynSmarty->assign('report', $report);
}

$esynSmarty->assign('importers', $importers);

$esynSmarty->display('importer.tpl');

?>
