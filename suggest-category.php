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


define("ESYN_REALM", "suggest_category");

$id = $cid = false;
if (
	isset($_POST['category_id']) && preg_match("/\D/", $cid = $_POST['category_id'])
	||
	isset($_GET['category_id']) && preg_match("/\D/", $cid = $_GET['category_id'])
	||	
	isset($_GET['id']) && preg_match("/\D/", $id=$_GET['id'])
	||
	isset($_POST['plan']) && preg_match("/\D/", $planId=$_POST['plan'])
)
{
	$_GET['error'] = "404";
	include("./error.php");
	die();
}

/** requires common header file **/
require_once('.'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'header.php');

$error = false;
$msg = array();

if (!$esynConfig->getConfig('suggest_category'))
{
	// Internal error message that tells that category suggestion is not allowed
	$_GET['error'] = "672";
	include(ESYN_HOME."error.php");
	exit;
}

/** defines tab name for this page **/
$currentTab = 'suggest-category';

require_once(ESYN_INCLUDES.'view.inc.php');

$eSyndiCat->factory("Category", "Layout");

if($esynConfig->getConfig('captcha') && '' != $esynConfig->getConfig('captcha_name'))
{
	$eSyndiCat->factory("Captcha");
}

$esynSmarty->caching = false;

if (isset($_GET['id']))
{
	$id = (int)$_GET['id'];
}

if (isset($_POST['category_id']))
{
	$cid = (int)$_POST['category_id'];
}

/** gets information about current category **/
if ($cid || (isset($_POST['category_title']) && 'ROOT' == $_POST['category_title']))
{
	$category_id = (int)$cid;
}
else
{
	$category_id = (int)$id;
}

/** gets information about current category **/
$category = $esynCategory->row("*", "id=".$category_id);

if (empty($category))
{
	$category = $esynCategory->row("*", "`parent_id` = '-1'");
}

$cid = $category['id'];
unset($id, $category_id);

$esynSmarty->assign('category', $category);

//$root_disable = 0 == $category['id'] ? $category['locked'] : $esynCategory->one("`locked`", "`id` = '0'");
//$esynSmarty->assign('rootLocked', $root_disable);

if ($category['locked'])
{
	$error = true;
	$msg[] = $esynI18N['error_category_locked'];
}

if (isset($_POST['add_category']))
{
	$eSyndiCat->startHook("suggestCategoryBeforeValidation");

	if(!defined('ESYN_NOUTF'))
	{
		require_once(ESYN_CLASSES.'esynUtf8.php');

		esynUtf8::loadUTF8Core();
		esynUtf8::loadUTF8Util('ascii', 'validation', 'bad', 'utf8_to_ascii');
	}

	if (!$category['locked'])
	{
		$temp['parent_id'] = $cid;
		$temp['account_id'] = $esynAccountInfo ? $esynAccountInfo['id'] : 0;

		$title = $_POST['title'];

		/** check for captcha **/
		if ($esynConfig->getConfig('captcha') && '' != $esynConfig->getConfig('captcha_name'))
		{
			if(!$esynCaptcha->validate())
			{
				$error = true;
				$msg[] = $esynI18N['error_captcha'];
			}
		}
		$_SESSION['pass'] = '';
	
		if (utf8_is_valid($title))
		{
			$temp['title'] = $title;
		}
		else
		{
			$temp['title'] = utf8_bad_replace($temp['title']);
		}

		/** check title **/
		if ('' == trim($temp['title']))
		{
			$error = true;
			$msg[] = $esynI18N['title_empty'];
		}
		else
		{
			$temp['path'] = $temp['title'];

			// transliteration
			if(!utf8_is_ascii($temp['path']))
			{
				$temp['path'] = utf8_to_ascii($temp['path']);
			}

			$temp['path'] = preg_replace("/[^a-z0-9._-]+/i", "-", $temp['path']);
			$temp['path'] = trim($temp['path'], "-");

			/** check for duplicate categories **/
			if (!empty($temp['path']))
			{
				$temp['path'] = esynCategory::getPath($category['path'], $temp['path']);
				if($esynCategory->exists("`path` = '".esynSanitize::sql($temp['path'])."'"))
				{
					$error = true;
					$msg[] = $esynI18N['error_category_exists'];
				}
			}
			else
			{
				 $error = true;
				 $msg[] = $esynI18N['title_incorrect'];
			}
		}
		
		if (!$error)
		{
			/** get order **/
			$temp['num_neighbours'] = '-1';
			$new_id = $esynCategory->insert($temp);
			$temp['id'] = $new_id;

			$msg[] = $esynI18N['category_submitted'];

			/** recursively add records to non-tree structure table of categories **/
			$_s = $esynCategory->buildRelation($temp['id']);

			// something wrong (may be infinite recursive detected)
			if (!$_s)
			{
				trigger_error("Error in Category::buildRelation method possibly infinite recursive", E_USER_ERROR);
			}
		}
	}
}

if(isset($title))
{
	$esynSmarty->assign('cat_title', $title);
}

$esynSmarty->assign('title', $esynI18N['suggest_category']);

/** breadcrumb formation **/
$bc['suggestcat']['url'] = '';
$bc['suggestcat']['caption'] = $esynI18N['suggest_category'];

$breadcrumb = $esynCategory ? $esynLayout->printBreadcrumb($category, $bc,1) : '';

$esynSmarty->display('suggest-category.tpl');
