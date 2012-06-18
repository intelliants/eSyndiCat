<?php
//##copyright##

define("ESYN_REALM", "accounts");

/** requires common header file **/
require_once('.'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'header.php');

if(!$esynConfig->getConfig('accounts'))
{
	$_GET['error'] = "404";
	include("./error.php");
	die();
}

$eSyndiCat->factory("Account", "Layout");

require_once(ESYN_INCLUDES.'view.inc.php');

if(!$esynPage->exists("`name` = '" . ESYN_REALM . "' AND `status` = 'active'"))
{
	$_GET['error'] = "404";
	include("./error.php");
	die();
}

$search_alphas = array('0-9','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
$alpha = (isset($_GET['alpha']) && in_array($_GET['alpha'], $search_alphas)) ? $_GET['alpha'] : false;
$cause = $alpha ? ('0-9' == $alpha ?  "(`username` REGEXP '^[0-9]') AND " : "(`username` LIKE '{$alpha}%') AND ") : '';

/** get list of accounts **/
$accounts = $esynAccount->all("*", "{$cause} `status` = 'active'");

$eSyndiCat->startHook("phpFrontAccountsAfterGetAccounts");

/** breadcrumb formation **/
$bc[0]['url'] = ESYN_MOD_REWRITE ? 'accounts/' : 'accounts.php';
$bc[0]['caption'] = $esynI18N['accounts'];

if ($alpha)
{
	$bc[1]['url'] = '';
	$bc[1]['caption'] = $alpha;
}

$breadcrumb = $esynLayout->printBreadcrumb(null, $bc);

$esynSmarty->assign('title', $esynI18N['accounts']);
$esynSmarty->assign('accounts', $accounts);
$esynSmarty->assign('search_alphas', $search_alphas);
$esynSmarty->assign('alpha', $alpha);

$esynSmarty->display('accounts.tpl');

?>
