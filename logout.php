<?php
//##copyright##

define("ESYN_REALM", "account_logout");

/** deletes cookies **/
setcookie('account_id',	'', time() - 3600);
setcookie('account_pwd', '', time() - 3600);

/** requires common header file **/
require_once('.'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'header.php');

(isset($_GET['action']) && 'logout' == $_GET['action']) && esynUtil::go2('logout.php');

$eSyndiCat->factory("Layout");

include(ESYN_INCLUDES.'view.inc.php');

$eSyndiCat->startHook("afterAccountLogout");

/** defines page title **/
$esynSmarty->assign('title', $esynI18N['logout']);

// breadcrumb formation
$bc['logout']['caption'] = $esynI18N['logout'];
$breadcrumb = $esynLayout->printBreadcrumb(0, $bc, 1);

$esynSmarty->display('logout.tpl');