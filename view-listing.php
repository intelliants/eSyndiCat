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


define("ESYN_REALM", "view_listing");

if (isset($_GET['id']) && preg_match("/\D/", $_GET['id']) || isset($_POST['id']) && preg_match("/\D/", $_POST['id']))
{
	$_GET['error'] = "404";
	include("./error.php");
	die();
}

/** requires common header file **/
require_once('.'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'header.php');

$eSyndiCat->factory("Listing", "Category", "ListingField", "Layout");

$id = (int)$_GET['id'];
$error = false;
$msg = '';

require_once(ESYN_INCLUDES.'view.inc.php');

$eSyndiCat->startHook("topViewListing");

$listing = $esynListing->getListingById($id, $esynAccountInfo['id']);

$eSyndiCat->startHook('viewListingAfterGetListing');

/** get parent category info **/
if (!isset($_GET['cat']) || !$esynCategory->validPath($_GET['cat']))
{
	$_GET['cat'] = "";
}

$category_id = $esynCategory->one("`id`", "`path` = '{$_GET['cat']}'");

$appropriateCategory = true;

if (!empty($listing) && $esynConfig->getConfig("mod_rewrite"))
{
	$sql = "SELECT 1 FROM `{$eSyndiCat->mPrefix}listings` listings ";
	$sql .= "LEFT JOIN `{$eSyndiCat->mPrefix}categories` categories ";
	$sql .= "ON `categories`.`id` = `listings`.`category_id` ";
	$sql .= "WHERE `listings`.`id` = '{$id}' AND ";
	$sql .= "`categories`.`path` = '{$_GET['cat']}'";
	
	$exist = $eSyndiCat->getOne($sql);
	
	$appropriateCategory = $exist ? true : false;
}
else
{
	$appropriateCategory = true;
}

if (empty($listing) || !$appropriateCategory)
{
	$_GET['error'] = "404";
	include(ESYN_HOME."error.php");
	exit;
}

$eSyndiCat->startHook("viewListing");

// display crossed categories modification
// get list of crossed categories
$sql = "SELECT `categories`.* FROM `{$eSyndiCat->mPrefix}listing_categories` `listing_categories` ";
$sql .= "LEFT JOIN `{$eSyndiCat->mPrefix}categories` `categories` ";
$sql .= "ON `listing_categories`.`category_id` = `categories`.`id` ";
$sql .= "WHERE `listing_categories`.`listing_id` = '{$listing['id']}'";

$crossed_categories = $eSyndiCat->getAll($sql);

$esynSmarty->assign_by_ref('crossed_categories', $crossed_categories);

$esynSmarty->assign_by_ref('listing', $listing);

/** get meta description and meta keywords for display **/
$description 	= isset($listing['meta_description']) 	&& !empty($listing['meta_description']) ? strip_tags($listing['meta_description']) 	: '';
$keywords 		= isset($listing['meta_keywords']) 		&& !empty($listing['meta_keywords']) 	? strip_tags($listing['meta_keywords']) 	: '';

$esynSmarty->assign_by_ref('description', $description);
$esynSmarty->assign_by_ref('keywords', $keywords);


$category_id = $listing['category_id'];
$plan_id = $listing['plan_id'] > 0 ? $listing['plan_id'] : NULL;
$plan = NULL;

if($plan_id)
{
	$eSyndiCat->setTable("plans");
	$plan = $eSyndiCat->row("*", "`id` = '{$plan_id}'");
	$eSyndiCat->resetTable();
}

$category = $esynCategory->row("*", "`id` = '{$listing['category_id']}'");

/** get link fields for display **/
$fields = $esynListing->getFieldsByPage('view', $category, $plan);

if($fields)
{
	foreach($fields as $key => $field)
	{
		if(in_array($field['name'], array('url', 'description', 'title')))
		{
			unset($fields[$key]);
		}
	}
}
$esynSmarty->assign('fields', $fields);

/** defines page title **/
$full_title_array = array();
esynUtil::getBreadcrumb($listing['category_id'], $full_title_array);

if($category['parent_id'] != '-1')
{
	$category['path'] = array_reverse($full_title_array);
}

$esynSmarty->assign('category', $category);

$out_title = esynSanitize::html(strip_tags($listing['title']));

if(!empty($full_title_array))
{
	foreach ($full_title_array as $jt_value)
	{
		$out_title.= " &#171; ".esynSanitize::html(strip_tags($jt_value['title']));		
	}
}

$out_title.= " &#171; ".esynSanitize::html(strip_tags($esynConfig->getConfig('site')));

$esynSmarty->assign('title', $out_title);

// breadcrumb formation
$bc['viewlink']['caption'] = $esynI18N['view_listing'];
$breadcrumb = $esynLayout->printBreadcrumb($category, $bc, 1);

$esynSmarty->display('view-listing.tpl');
