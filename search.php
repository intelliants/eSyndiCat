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


define("ESYN_REALM", "search");

$showForm = true;

$searchType = isset($_GET['type']) ? $_GET['type'] : false;

if(isset($_GET['type']) && preg_match("/\D/", $searchType) && $searchType)
{
	$_GET['error'] = '404';
	include("./error.php");
	die();
}

/** requires common header file **/
require_once('.'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'header.php');

require_once(ESYN_INCLUDES.'view.inc.php');

$error = false;
$msg = '';

$eSyndiCat->factory("Listing", "Category");

/** gets current page and defines start position **/
$page = 0;

if(isset($_GET['page']))
{
	$page = (int)$_GET['page'];
}
elseif(isset($_POST['_settings']['page']))
{
	$page = (int)$_POST['_settings']['page'];
}

$page = ($page < 1) ? 1 : $page;
$start = ($page - 1) * $esynConfig->getConfig('num_index_listings');

$esynSmarty->assign("page", $page);

$esynSmarty->assign("POST_json", "[]");

/** get link fields for display **/
$fields = $esynListing->getFieldsForSearch();

$listings = array();

if(isset($_GET['what']))
{
	$eSyndiCat->startHook("searchBeforeValidation");

	if(!defined('ESYN_NOUTF'))
	{
		require_once(ESYN_CLASSES.'esynUtf8.php');

		esynUtf8::loadUTF8Core();
		esynUtf8::loadUTF8Util('ascii', 'validation', 'bad', 'utf8_to_ascii');
	}

	$total_listings	= 0;

	$what = trim($_GET['what']);

	if(!utf8_is_valid($what))
	{
		trigger_error("Bad characters in 'query' variable in searching", E_USER_NOTICE);
		$what = utf8_bad_replace($what);
	}

	$len = utf8_strlen($what);

	if($len < 2)
	{
		$error = true;
	}

	if($len > 50)
	{
		$error = true;
	}

	if(!$error)
	{
		// escape wild characters
		$what = str_replace(array("%","_"),array("\%","\_"), $what);
		$what = esynSanitize::sql($what);
		$cats_only = isset($_GET['cats']) ? $_GET['cats'] : false;

		if(!$searchType)
		{
			$searchType = 1;
		}

		/** search listings box formation **/
		$nm = $esynConfig->getConfig('num_index_listings');

		$cause = $esynListing->getSearchCriterias($what, $searchType);
		$url = "search.php?what=".urlencode($what)."&type=".$searchType."&page={page}";

		$nmCat = $cats_only ? 0 : $esynConfig->getConfig('num_cats_for_search');
		$causeCat = $esynCategory->getCatSearchCriterias($what, $searchType);
		
		$replaceQuery = false;
		$replaceQueryCat = false;
		$eSyndiCat->startHook("injectSearchClause");

		if(!$replaceQueryCat)
		{
			$categories = $esynCategory->getCatByCriteria(0, $nmCat, $causeCat, true);
			if(!empty($categories))
			{
				$total_categories = $esynCategory->foundRows();
			}
		}
		
		if (!$replaceQuery)
		{
			if (!$cats_only)
			{
				$listings = $esynListing->getByCriteria($start, $nm, $cause, true);
				if(!empty($listings))
				{
					$total_listings	= $esynListing->foundRows();
				}
			}else{
				$listings = array();
			}
		}

		$eSyndiCat->startHook("afterGetSearchResult");

		$c = count($listings);

		$esynSmarty->assign('categories', $categories);
		$esynSmarty->assign('listings', $listings);
		
		$esynSmarty->assign('url', $url);
	}

	$esynSmarty->assign('total_categories', $total_categories);
	$esynSmarty->assign('total_listings', $total_listings);
}

$eSyndiCat->factory("Layout");

/** defines page title **/
$esynSmarty->assign('title', $esynI18N['search']);

/** breadcrumb formation **/
$bc['search']['url'] = '';
$bc['search']['caption'] = $esynI18N['search'];

$breadcrumb = $esynLayout->printBreadcrumb(0, $bc, 1);

$esynSmarty->display('search.tpl');
