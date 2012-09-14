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


define("ESYN_REALM", "add_to_favorites");

if(empty($_POST['listing_id']) || preg_match("/\D/", $_POST['listing_id']) && empty($_POST['account_id']) || preg_match("/\D/", $_POST['account_id']))
{
	exit;
}

/** requires common header file **/
require_once('.'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'header.php');

$eSyndiCat->factory("Listing");

$listing_id	= (int)$_POST['listing_id'];
$account_id = (int)$_POST['account_id'];

if ('add' == $_POST['action'])
{
	$duplicate = $esynListing->exists("`id` = '{$listing_id}' AND `fav_accounts_set` LIKE '%{$account_id},%'");

	if (!$duplicate)
	{
		$eSyndiCat->setTable("listings");
	
		$sql = "UPDATE `{$eSyndiCat->mPrefix}listings` SET `fav_accounts_set` = CONCAT(`fav_accounts_set`, '{$account_id},' ) ";
		$sql .= "WHERE `id` = '{$listing_id}'";

		$return = $eSyndiCat->query($sql);
		$eSyndiCat->resetTable();
	}
}
elseif ('remove' == $_POST['action'])
{
	$eSyndiCat->setTable("listings");

	$sql = "SELECT `fav_accounts_set` FROM `{$eSyndiCat->mPrefix}listings` ";
	$sql .= "WHERE `id` = '{$listing_id}' ";
	$sql .= "AND `fav_accounts_set` LIKE '%{$account_id},%'";

	$accounts_id = $eSyndiCat->getOne($sql);

	if($accounts_id)
	{
		$newValue = str_replace("{$account_id},", "", $accounts_id);
		
		$sql = "UPDATE `{$eSyndiCat->mPrefix}listings` SET `fav_accounts_set` = '{$newValue}' ";
		$sql .= "WHERE `id` = '{$listing_id}'";

		$return = $eSyndiCat->query($sql);
	}

	$eSyndiCat->resetTable();
}
?>
