<?php
//##copyright##

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
