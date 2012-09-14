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


define("ESYN_REALM", "redirect");

if (empty($_GET['id']) || $_GET['id']{0} == '0' || preg_match("/\D/", $_GET['id']))
{
	$_GET['error'] = "404";
	include("./error.php");
	die();
}

// requires common header file
require_once('.'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'header.php');

$id	= (int)$_GET['id'];

$eSyndiCat->factory("Listing");

$listing_url = $esynListing->one("`url`", "`id`='{$id}'");

if (empty($listing_url))
{
	$_GET['error'] = "404";
	include(ESYN_HOME."error.php");
}
else
{
	header('Location: '.$listing_url);
}
exit;