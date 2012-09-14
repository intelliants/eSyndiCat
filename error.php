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


defined("ESYN_REALM") || define("ESYN_REALM", "error");

// error not specified then 404 :)
if (empty($_GET['error']) || preg_match("/\D/", $_GET['error']))
{
	$error = "404";
}
else
{
	$error = $_GET['error'];
}
// don't need them.
$_GET = $_POST = array();

if ($error == "404")
{
	header("HTTP/1.1 404 Not found");
}

/** requires common header file **/
require_once('.'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'header.php');
require_once(ESYN_INCLUDES.'view.inc.php');

$eSyndiCat->factory("Layout");

if(!array_key_exists($error, $esynI18N))
{
	$error = "404";
	header("HTTP/1.1 404 Not found");
}

if($error == '671')
{
	header("HTTP/1.1 403 Forbidden");
}

$eSyndiCat->startHook("error");

// we can easily cache this page
$esynSmarty->caching = ESYN_CACHING;
// for 24 hours = 86400
$esynSmarty->cache_lifetime = 86400;

if (!$esynSmarty->is_cached("error.tpl", $error))
{
	$templ = $esynConfig->getConfig('tmpl');
	if (!$templ)
	{
		die("Error: Template doesn't exists");
	}
	
	// defines page title
	$esynSmarty->assign_by_ref('title', $esynI18N['error']);
	
	// breadcrumb formation
	$bc['error']['caption'] = $esynI18N['error'];
	$breadcrumb = $esynLayout->printBreadcrumb(0, $bc, 1);

	$esynSmarty->assign_by_ref('error', $esynI18N[$error]);
}

$esynSmarty->display('error.tpl', $error);