<?php
//##copyright##

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
