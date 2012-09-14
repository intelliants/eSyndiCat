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


define("ESYN_REALM", "counter");

//slight change
if (empty($_POST['id']) || empty($_POST['type']) || preg_match("/\D/", $_POST['id']) || (int)$_POST['id'] < 1)
{
	header("HTTP/1.1 404 Not found");
	print("Powered by <b><a href=\"http://www.esyndicat.com\" style=\"color:red;text-decoration:underline;\">eSyndicat Free</a></b>");
	exit;
}

include(dirname(__FILE__).DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'header-lite.php');

$id = (int)$_POST['id'];
$ip = $_SERVER['REMOTE_ADDR'];
$type = $_POST['type'];

$eSyndiCat->startHook('clickCountItem');

if(in_array($type, array('categories', 'listings')))
{
	if('categories' == $type)
	{
		$eSyndiCat->factory("Category");
	
		if ($esynCategory->exists("`id` = :id", array('id' => $id)) && !$esynCategory->checkClick($id, $ip))
		{
			$esynCategory->click($id, $ip);
		}
	}

	if('listings' == $type)
	{
		$eSyndiCat->factory("Listing");
	
		if ($esynListing->exists("`id` = :id", array('id' => $id)) && !$esynListing->checkClick($id, $ip))
		{
			$esynListing->click($id, $ip);
		}
	}
}

exit;

?>