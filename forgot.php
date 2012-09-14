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


define("ESYN_REALM", "account_password_forgot");

/** requires common header file **/
require_once('.'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'header.php');

if (!$esynConfig->getConfig('accounts'))
{
	$_GET['error'] = "670";
	include(ESYN_HOME."error.php");
	exit;
}

$eSyndiCat->factory("Account", "Layout");

$error = false;
$msg = array();

$form = true;

require_once(ESYN_INCLUDES.'view.inc.php');

$esynSmarty->caching = false;

if (isset($_POST['restore']))
{
	// check emails
	if (!esynValidator::isEmail($_POST['email']))
	{
		$error = true;
		$msg[] = $esynI18N['error_email_incorrect'];
	}

	if (!$error)
	{
		$account = $esynAccount->row("*", "`email` = :email", array('email' => $_POST['email']));

		if ($account)
		{
			$form = false;

			$esynAccount->confirmEmail($account, 'confirm_email');
			$msg[] = $esynI18N['instructions_restore_password_sent'];
		}
		else
		{
			$error = true;
			$msg[] = $esynI18N['error_no_account_email'];
		}
	}
}

$esynSmarty->assign('form', $form);

/** defines page title **/
$esynSmarty->assign_by_ref('title', $esynI18N['restore_password']);

// breadcrumb formation
$bc['forgot']['caption'] = $esynI18N['restore_password'];
$breadcrumb = $esynLayout->printBreadcrumb(0, $bc, 1);

$esynSmarty->display('forgot.tpl');
