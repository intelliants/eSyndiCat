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