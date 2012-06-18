<?php
//##copyright##

$view  = $_GET['view'];
$types = array('new', 'top', 'popular', 'account', 'random', 'favorites');

if (!in_array($view, $types, true))
{
	$_GET['error'] = "404";
	include("./error.php");
	exit;
}

define("ESYN_REALM", "{$view}_listings");

if ('account' == $view || 'favorites' == $view)
{
	define("ESYN_THIS_PAGE_PROTECTED", true);
}

// requires common header file
require_once('.'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'header.php');

$eSyndiCat->factory("Listing", "Layout");

require_once(ESYN_INCLUDES.'view.inc.php');

if ($view == 'account' || $view == 'new' || $view == 'random' || $view == 'favorites')
{
	$esynSmarty->caching = false;
}
else
{
	// Popular and top listings can be cached
	// since they're not very dynamic
	$esynSmarty->caching = ESYN_CACHING;
	// for an hour
	$esynSmarty->cache_lifetime = 3600;
}

$render = 'listings.tpl';

$num_index = $esynConfig->getConfig('num_index_listings');
$num_listings = $esynConfig->getConfig('num_get_listings');

$page = 1;

// There is no need of pagination
if ($view != 'random')
{
	/** gets current page and defines start position **/
	$page	= !empty($_GET['page']) ? (int)$_GET['page'] : 1;
	$page	= ($page < 1) ? 1 : $page;
	$start	= ($page - 1) * $num_listings;

	/** gets number of all listings **/
	if ($view == 'account')
	{
		$total_listings = $esynListing->one("count(*)", "`account_id`='".$esynAccountInfo['id']."'");
	}
	elseif ($view == 'favorites')
	{
		$total_listings = $esynListing->one("COUNT(*)", "`fav_accounts_set` LIKE '%{$esynAccountInfo['id']},%'");
	}
	else
	{
		$total_listings = $esynListing->one("count(*)", "`status`='active'");
	}
}

$cached = $esynSmarty->is_cached($render, ESYN_LANGUAGE."|".$view."|".$page);

if ($view == 'account')
{
	$listings = $esynListing->getListingsByAccountId($esynAccountInfo['id'], '', $start, $num_index);
}
elseif ($view == 'favorites')
{
	$listings = $esynListing->getFavoriteListingByEditorId($esynAccountInfo['id'], $start, $num_index);
}
else // general settings for top, new, popular and random
{
	if (!$cached)
	{
		/** get link fields for display **/
		$fields = $esynListing->getFieldsByPage(3);
		$esynSmarty->assign_by_ref('fields', $fields);	
	}
}

if ($view == 'popular')
{
	if (!$cached)
	{
		$listings = $esynListing->getPopular($start, $num_listings, $esynAccountInfo['id']);
	}
}
elseif ($view == 'top')
{
	if (!$cached)
	{
		$listings = $esynListing->getTop($start, $num_listings, $esynAccountInfo['id']);
	}
}
elseif ($view == 'new')
{
	$listings = $esynListing->getLatest($start, $num_listings, $esynAccountInfo['id']);
}
elseif ($view == 'random')
{
	$listings = $esynListing->getRandom($num_index, $esynAccountInfo['id']);
}

$eSyndiCat->startHook("phpFrontListingsAfterGetListings");

if (!$esynSmarty->caching || !$cached)
{
	/** defines page title **/
	$t= $view;
	if ($view == 'account')
	{
		$t = 'my';
	}
	if ($view == 'favorites')
	{
		$t = 'my_favorite';
	}
	
	$title = $esynI18N['page'].' '.$page.' '.$esynI18N[$t.'_listings'];
	$esynSmarty->assign('title', $title);

	// breadcrumb formation
	$bc['listings']['caption']	= $esynI18N[$t.'_listings'];

	$breadcrumb = $esynLayout->printBreadcrumb(array(), $bc, 1);

	$esynSmarty->assign('listings', isset($listings) ? $listings : null);
	
	$url = ESYN_MOD_REWRITE ? $view.'-listings{page}.html' : "listings.php?view={$view}&page={page}";

 	$c = count($listings);
	if($num_listings > $c)
	{
		$esynSmarty->assign('num_listings', $c);
	}
	else
	{
		$esynSmarty->assign('num_listings', $num_listings);
	}

	if(isset($total_listings))
	{
		$esynSmarty->assign('total_listings', $total_listings);
	}

	$esynSmarty->assign('view', $view);
	$esynSmarty->assign('url', $url);
}

$esynSmarty->display($render, ESYN_LANGUAGE."|".$view."|".$page);
