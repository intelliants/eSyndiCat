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


$cid = 0; // category ID
$listing_id = isset($_POST['listing_id']) ? (int)$_POST['listing_id'] : false;
$listing_id = false === $listing_id && isset($_GET['edit']) ? (int)$_GET['edit'] : $listing_id;
$mode = $listing_id ? 'edit' : 'suggest';

define("ESYN_REALM", $mode . "_listing");

$msg = array();
$error = false;

require_once('.'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'header.php');
$eSyndiCat->factory("Listing", "Category", "Layout");
include(ESYN_INCLUDES."view.inc.php");

$listing = $listing_id ? $esynListing->row("*", "`id` = {$listing_id}") : false;

// listing submission disabled
if ('suggest' == $mode && !$esynConfig->getConfig('allow_listings_submission'))
{
	$_GET['error'] = "671";
	require(ESYN_HOME."error.php");
	exit;
}

// listing submission disabled for not authenticated
if (empty($esynAccountInfo) && !$esynConfig->getConfig('accounts_registered'))
{
	$_GET['error'] = "671";
	require(ESYN_HOME."error.php");
	exit;
}

// listing not found
if ('edit' == $mode && empty($listing))
{
	$_GET['error'] = "404";
	require(ESYN_HOME."error.php");
	exit;
}

// The listing is not owned by this account
if ('edit' == $mode && (empty($esynAccountInfo) || $listing['account_id'] != $esynAccountInfo['id']))
{
	$_GET['error'] = "671";
	require(ESYN_HOME."error.php");
	exit;
}

if($esynConfig->getConfig('captcha') && '' != $esynConfig->getConfig('captcha_name'))
{
	$eSyndiCat->factory("Captcha");
}

if (isset($_POST['category_id']))
{
	$cid = (int)$_POST['category_id'];
}
elseif ('edit' == $mode)
{
	$cid = $listing['category_id'];
}
elseif (isset($_GET['cid']))
{
	$cid = (int)$_GET['cid'];
}

$category = $esynCategory->row("*", "`id` = :category", array('category' => $cid));

if(isset($_POST['save_changes']))
{
	$eSyndiCat->startHook('editListingValidation');

	if(!defined('ESYN_NOUTF'))
	{
		require_once(ESYN_CLASSES.'esynUtf8.php');

		esynUtf8::loadUTF8Core();
		esynUtf8::loadUTF8Util('ascii', 'validation', 'bad', 'utf8_to_ascii');
	}

	$fields = $esynListing->getFieldsByPage($mode, $category);

	list($data, $error, $msg) = $esynListing->processFields($fields, $listing);

	if ($esynConfig->getConfig('captcha') && '' != $esynConfig->getConfig('captcha_name'))
	{
		if(!$esynCaptcha->validate())
		{
			$error = true;
			$msg[] = $esynI18N['error_captcha'];
		}
	}
	unset($temp);


	$data['ip_address'] = $_SERVER['REMOTE_ADDR'];
	$data['account_id'] = $esynAccountInfo['id'];
	$data['category_id'] = $cid;

	/** check emails **/
	if (!esynValidator::isEmail(stripslashes($data['email'])))
	{
		$error = true;
		$msg[] = $esynI18N['error_email_incorrect'];
	}

	// check URL
	if (empty($data['url']))
	{
		$error = true;
		$msg[] = $esynI18N['error_url'];
	}
	else
	{
		if (!esynValidator::isUrl($data['url']))
		{
			$error = true;
			$msg[] = $esynI18N['error_url'];
		}
		else
		{
			$data['domain'] = esynUtil::getDomain($data['url']);
			$data['pagerank'] = $esynConfig->getConfig('pagerank') ? esynUtil::getPageRank($data['url']) : -1;

			// check if listing already exists
			if ($esynConfig->getConfig('duplicate_checking'))
			{
				$check = $esynConfig->getConfig('duplicate_type') == 'domain' ? $data['domain'] : $data['url'];
				$res = $esynListing->checkDuplicateListings($check, $esynConfig->getConfig('duplicate_type'));
				if ($res && $listing['id'] != $res)
				{
					$error = true;
					$msg[] = $esynI18N['error_listing_present'];
				}
			}

			// Check if listing is broken or not.
			$listing_header = 200;
			if ($esynConfig->getConfig('listing_check'))
			{
				$listing_header = 1;
				$headers = esynUtil::getPageHeaders($data['url']);

				$isIIS = isset($headers['Server']) && (false !== strpos($headers['Server'], "IIS"));

				if (!empty($headers))
				{
					$listing_header = (int)$headers['Status'];
				}

				// Some (IIS) web servers don't allow HEAD methods
				// and return 403 or 405 errors, while the page
				// exists and GET method would return 200.
				// So, 403 and 405 are considered valid return
				// codes here.
				$allow = $isIIS && ($listing_header == 403 || $listing_header == 405);
				if (!$allow && !in_array((string)$listing_header, explode(',',$esynConfig->getConfig('http_headers')), true))
				{
					$error = true;
					$msg[] = $esynI18N['error_broken_listing'];
				}
			}
			$data['listing_header'] = $listing_header;
		}
	}

	// reciprocal link checking
	if ($esynConfig->getConfig('reciprocal_check'))
	{
		$pageContent = esynUtil::getPageContent($data['reciprocal']);
		$pageContent = str_replace("\r", "", $pageContent);
		$recipCode = str_replace("\r", "", $esynConfig->getConfig("reciprocal_code"));

		if ($esynConfig->getConfig('reciprocal_domain'))
		{
			if (esynUtil::getDomain($data['reciprocal']) != $data['domain'])
			{
				$error = true;
				$msg[] = $esynI18N['error_reciprocal_domain'];
			}
			else
			{
				$data['recip_valid'] = false !== strpos($pageContent, $recipCode);
				
// 				if (!$data['recip_valid'])
// 				{
// 					if(!$esynConfig->getConfig('reciprocal_required_only_for_free')
// 						|| ($esynConfig->getConfig('reciprocal_required_only_for_free') && empty($plan['cost'])))
// 					{
// 						$error = true;
// 						$msg[] = $esynI18N['error_reciprocal_listing'];
// 					}
// 				}
			}
		}
		else
		{
			$data['recip_valid'] = false !== strpos($pageContent, $recipCode);
			if (!$data['recip_valid'])
			{
				$error = true;
				$msg[] = $esynI18N['error_reciprocal_listing'];
			}
		}
		if ($data['recip_valid'])
		{
			$data['featured'] = '1';
		}
		unset($pageContent, $recipCode);
	}

	if (!$error)
	{
		$data['status'] = 'approval';
		$data['status'] = ($esynConfig->getConfig('auto_approval') && ('approval' == $data['status'])) ? 'active' : $data['status'];

		if ('suggest' == $mode)
		{
			$data['id'] = $esynListing->insert($data);
			$msg[] = $esynI18N['listing_submitted'];
		}
		else
		{
			$data['id'] = $listing['id'];
			$esynListing->update($data, array('last_modified' => 'NOW()'));
			$msg[] = $esynI18N['listing_changed'];
		}

		$params['id'] = $data['id'];
		$params['item_type'] = 'listings';
				
		$eSyndiCat->startHook("afterEditListing");
	}

	$msg = '<ul><li>' . implode('</li><li>', $msg) . '</li></ul>';
	$msg = str_replace('"', '\"', $msg);
	printf ('{"err":%d,"msg":"%s"}', $error, $msg);
	exit;
}

$esynSmarty->assign('listing', $listing);
$esynSmarty->assign('category', $category);

/** defines page title **/
$title = $esynI18N[ESYN_REALM];
$esynSmarty->assign('title', $title);

// breadcrumb formation
$bc['editlink']['caption'] = $title;
$breadcrumb = $category ? $esynLayout->printBreadcrumb($category['id'], $bc, 1) : '';

$esynSmarty->display('suggest-listing.tpl');
