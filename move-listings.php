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


if(preg_match("/\D/", $_GET['idlink']) || preg_match("/\D/", $_GET['idcat']))
{
	header("HTTP/1.1 404 Not found");
	print("Powered By eSyndicat ");
	die();		
}

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'header.php');
	
$eSyndiCat->factory("Listing");

if(isset($_GET['action']) && 'moving' == $_GET['action'])
{
	$idLink = (int)$_GET['idlink'];
	$idCat = (int)$_GET['idcat'];	

	$listing = $esynListing->row("`id`,`title`,`category_id`,`moved_from`,`status`", "`id` = '{$idLink}'");

	$eSyndiCat->setTable("listing_categories");
	$crossed_categories = $eSyndiCat->all("`category_id`", "`listing_id` = '{$idLink}'");
	$eSyndiCat->resetTable();

	if(!empty($crossed_categories))
	{
		foreach($crossed_categories as $onecategory)
		{
			$exception[] = $onecategory['category_id'];
		}
	}

	if(!empty($exception))
	{
		if(in_array($idCat, $exception))
		{
			$msg = 'Unexpected error!';
			die();
		}
	}
	
	$eSyndiCat->setTable("language");
	$esynI18N = $eSyndiCat->keyvalue("`key`,`value`", "`code` = '".ESYN_LANGUAGE."' AND `key` IN ('listing_returned','listing_moved')");
	$eSyndiCat->resetTable();		

	$updater = array();
	
	$updater['id'] = $idLink;
	$updater['category_id'] = $idCat;
	
	if(-1 == (int)$listing['moved_from'])
	{
		$updater['moved_from'] = $listing['category_id'];
	}
	elseif($idCat == (int)$listing['moved_from'])
	{
		$updater['moved_from'] = -1;				
	}

	if('approval' != $listing['status'])
	{
		$updater['status'] = 'approval';
	}

	$check = $esynListing->update($updater);

	if($check)
	{
		$eSyndiCat->factory("Category");			

		$category = $esynCategory->row('`id`,`title`,`path`', "`id` = '{$idCat}'");

		$url = ESYN_URL;
		
		if($esynConfig->getConfig('mod_rewrite'))
		{
			$category['path'] .= (!empty($category['path'])) ? '/' : '';

			$linkUrl = $category['path'].esynUtil::convertStr(array('string' => $listing['title'])).'-l'.$listing['id'].'.html';
			$categoryUrl = ($esynConfig->getConfig('use_html_path')) ? $category['path'].'.html' : $category['path'];
		}
		else
		{
			$linkUrl = "view-listing.php?id={$listing['id']}";
			$categoryUrl = "index.php?category={$category['id']}";
		}

		if($idCat == (int)$listing['moved_from'])
		{
			$msg = "Listing <a href=\"{$url}{$linkUrl}\">{$listing['title']}</a> was returned back to <a href=\"{$url}{$categoryUrl}\">{$category['title']}</a>";
		}
		else
		{
			$msg = "Listing <a href=\"{$url}{$linkUrl}\">{$listing['title']}</a> successfully moved to <a href=\"{$url}{$categoryUrl}\">{$category['title']}</a>";
		}
		
		if('active' == $listing['status'])
		{
			$esynCategory->adjustNumListings($listing['category_id'], "-1");		
		}
	}

	echo '<p class="field"><strong>'.$msg.'</strong></p>';
	exit;
}

?>
