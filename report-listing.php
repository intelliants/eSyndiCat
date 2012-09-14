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


define("ESYN_REALM", "report_broken_listing");

if (empty($_GET['id']) || $_GET['id']{0} == '0' || preg_match("/\D/", $_GET['id']))
{
	$_GET['error'] = "404";
	include("./error.php");
	die();
}

/** requires common header file **/
require_once('.'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'header.php');

$id	= (int)$_GET['id'];
$ip	= $_SERVER['REMOTE_ADDR'];

$eSyndiCat->factory("Listing");

$listing = $esynListing->row("`id`,`url`,`title`", "`id`='".$id."'");

if (empty($listing))
{
	$_GET['error'] = "404";
	include(ESYN_HOME."error.php");
	exit;
}

include(ESYN_INCLUDES.'view.inc.php');

if (!empty($_GET['report']))
{
	// Send an email to the site administrator.
	$event 	= array(
		"action"	=> 'broken_listing_report',
		"params"	=> array(
			"rcpts"		=>	array($esynConfig->getConfig("site_email")),
			"listing"	=> $listing
		)
	);
	
	$eSyndiCat->mMailer->dispatcher($event);	
}

$esynSmarty->caching = false;

$esynI18N['report_as_broken'] = str_replace('{lurl}', $listing['url'], $esynI18N['report_as_broken']);

echo "<script type=\"text/javascript\">";
echo "alert('{$esynI18N['report_as_broken']}');";
echo "</script>";
exit;
