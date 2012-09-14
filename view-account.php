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


define("ESYN_REALM", "view_account");

/** requires common header file **/
require_once('.'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'header.php');

if(!$esynConfig->getConfig('accounts'))
{
	$_GET['error'] = "404";
	include("./error.php");
	die();
}

$eSyndiCat->factory("Account", "Listing", "Layout");

require_once(ESYN_INCLUDES.'view.inc.php');

$account = $esynAccount->row("*", "`username` = :account AND `status` = 'active'", array('account' => urldecode($_GET['account'])));

if(empty($account))
{
	$_GET['error'] = "404";
	include("./error.php");
	die();
}

/** gets current page and defines start position **/
$page = empty($_GET['page']) ? 0 : (int)$_GET['page'];
$page = ($page < 1) ? 1 : $page;
$start = ($page - 1) * $esynConfig->getConfig('num_index_listings');

$listings = $esynListing->getListingsByAccountId($account['id'], 'active', $start, $esynConfig->getConfig('num_index_listings'));
$total_listings = $esynListing->getNumListingsByAccountId($account['id'], 'active');

/** set the property lettter **/
$alphas = array('0-9','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

$account_first_letter = strtoupper(substr($account['username'], 0, 1));

$alpha = in_array($account_first_letter, $alphas) ? $account_first_letter : 'A';

/** set the title of page **/
$title = strip_tags($account['username']) . ' ' . strtolower($esynI18N['account']);

/** set url for pagination menu **/
$url = $esynLayout->printAccUrl(array('account' => $account));
$url .= ESYN_MOD_REWRITE ? '?' : '&';
$url .= 'page={page}';

/** set breadcrumb **/
$bc[0]['caption']	= $esynI18N['accounts'];
$bc[0]['url']		= ESYN_MOD_REWRITE ? 'accounts/' : 'accounts.php';
$bc[1]['url']		= ESYN_MOD_REWRITE ? 'accounts/' . $alpha . '/' : 'accounts.php?alpha=' . $alpha;
$bc[1]['caption']	= $alpha;
$bc[2]['caption']	= esynSanitize::html(strip_tags($account['username']));

$breadcrumb = $esynLayout->printBreadcrumb(null, $bc);

$esynSmarty->assign('title', $title);
$esynSmarty->assign('account', $account);
$esynSmarty->assign('listings', $listings);
$esynSmarty->assign('total_listings', $total_listings);
$esynSmarty->assign('url', $url);

$eSyndiCat->startHook("phpFrontViewAccountBeforeDisplay");

$esynSmarty->display('view-account.tpl');

?>
