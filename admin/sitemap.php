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


define("ESYN_REALM", "sitemap");

$esynAdmin->factory('GYSMap');

$gNoBc = false;

$gBc[0]['title'] = $esynI18N['create_gy_sitemap'];
$gBc[0]['url'] = '';
$gTitle	= $esynI18N['create_gy_sitemap'];

require_once(ESYN_ADMIN_HOME.'view.php');

$error = false;
$msg = '';

$esynGYSMap->path_to_file = ESYN_HOME . 'tmp' . ESYN_DS . 'sitemap' . ESYN_DS;
$error_msg = $esynGYSMap->init();
if (!empty($error_msg))
{
	$error = true;
	esynMessages::setMessage($esynI18N[$error_msg], $error);
	$esynSmarty->assign("disabled", "disabled='disabled'");
}
$esynSmarty->assign("disabled", "");

if(isset($_POST['action']) && 'create' == $_POST['action'])
{
	$type_sitemap = $_POST['type_sitemap'];
	$items_count  = (int)$_POST['items_count'];
	$stage_all = (int)$_POST['stage_all'];
	$start = (int)$_POST['start'];
	$limit = (int)$_POST['limit'];
	$stage = (int)$_POST['stage'];
	$file  = (int)$_POST['file'] == 0 ? '' : (int)$_POST['file'];
	$item  = $_POST['item'];
	
	
	$sitemap = '';
	
	if ('categories' == $item)
	{
		$sitemap .= $esynGYSMap->buildCategoriesMap ($start, $limit, $type_sitemap);
	}
	
	if ('listings' == $item)
	{
		$sitemap .= $esynGYSMap->buildListingsMap ($start, $limit, $type_sitemap);
	}
	
	if ('pages' == $item)
	{
		$sitemap .= $esynGYSMap->buildPagesMap ($start, $limit, $type_sitemap);
	}
	
	if ('accounts' == $item)
	{
		$sitemap .= $esynGYSMap->buildAccountsMap ($start, $limit, $type_sitemap);
	}
	
	$esynAdmin->startHook('adminGYSMBuildMap');

	if (1 == $stage)
	{
		$esynGYSMap->deleteOldSitemaps($type_sitemap);
	}
		
	if ('google' == $type_sitemap)
	{
		$filename = $esynGYSMap->path_to_file."google".ESYN_DS."sitemap{$file}.xml";
		
		if(!file_exists($filename))
		{
			$sitemap = $esynGYSMap->getGoogleHeader().$sitemap;
			if ($file > 0)
			{
				$old_file = (intval($file) - 1);
				$old_file = $old_file < 1 ? '' : $old_file;
				if (!$fp = $esynGYSMap->openFile($esynGYSMap->path_to_file."google".ESYN_DS."sitemap".$old_file.".xml", "a"))
				{
					$error = true;
					$msg = "Cannot open file ".$esynGYSMap->path_to_file."google".ESYN_DS."sitemap".$old_file.".xml in tmp directory";
					echo "{error: '{$error}', msg: '{$msg}'}";
					exit();
				}
				
				if(!$esynGYSMap->writeToFile($esynGYSMap->getGoogleFooter(), $fp))
				{
					$error = true;
					$msg = "Cannot write to file ".$esynGYSMap->path_to_file."google".ESYN_DS."sitemap".$old_file.".xml";
					echo "{error: '{$error}', msg: '{$msg}'}";
			        exit;
				}
			}
		}
		
		if ($stage == $stage_all)
		{
			$sitemap = $sitemap.$esynGYSMap->getGoogleFooter();
		}
		
		if (!$fp = $esynGYSMap->openFile($filename, "a"))
		{
			$error = true;
			$msg = "Cannot open file $filename in tmp directory";
			echo "{error: '{$error}', msg: '{$msg}'}";
			exit();
		}

		if(!$esynGYSMap->writeToFile($sitemap, $fp))
		{
			$error = true;
			$msg = "Cannot write to file ".$filename;
			echo "{error: '{$error}', msg: '{$msg}'}";
	        exit;
		}
		
	    if ($stage == $stage_all && $file > 0)
	    {
	    	$msg = $esynGYSMap->getGoogleSMIndex($file);
	    	if (!empty($msg))
	    	{
	    		$error = true;
	    		echo "{error: '{$error}', msg: '{$msg}'}";
	       		exit;
	    	}
	    }
	}
	
	if ('yahoo' == $type_sitemap)
	{
		if (!$fp = $esynGYSMap->openFile($esynGYSMap->path_to_file."yahoo".ESYN_DS."urllist.txt", "a"))
		{
			$error = true;
			$msg = "Cannot open file ".$esynGYSMap->path_to_file."yahoo".ESYN_DS."urllist.txt";
			echo "{error: '{$error}', msg: '{$msg}'}";
			exit();
		}
		
		if(!$esynGYSMap->writeToFile($sitemap, $fp))
		{
			$error = true;
			$msg = "Cannot write to file ".$esynGYSMap->path_to_file."yahoo".ESYN_DS."urllist.txt";
			echo "{error: '{$error}', msg: '{$msg}'}";
	        exit;
		}
		
	}
	
	echo "{msg: '{$msg}', error: '{$error}'}";
	exit;
}
else 
{
	$items = $esynGYSMap->getTotal();
	
	$esynAdmin->startHook('adminGYSMTotalItems');
	
	$esynSmarty->assign_by_ref('items', $items);
}

$esynSmarty->display('sitemap.tpl');
