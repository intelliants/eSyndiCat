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
