<?php
//##copyright##

define("ESYN_REALM", "resend_activation_email");

/** requires common header file **/
require_once('.'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'header.php');

if (!$esynConfig->getConfig('accounts'))
{
	$_GET['error'] = "670";
	include(ESYN_HOME."error.php");
	exit;
}

$eSyndiCat->factory("Account", "Layout");

require_once(ESYN_INCLUDES.'view.inc.php');

$esynSmarty->caching = false;

if (isset($_POST['resend']))
{
	$username = esynSanitize::sql($_POST['username']);
	
	/** check username **/
	if (!$username)
	{
		$error = true;
		$msg[] = $esynI18N['error_username_empty'];
	}

	if (!$error)
	{
		$account = $esynAccount->row("*", "`username` = '{$username}' AND `sec_key` != ''");
		
		if ($account)
		{
			$esynAccount->resendEmail($account);
			$msg[] = $esynI18N['email_sent'];
		}
		else
		{
			$error = true;
			$msg[] = $esynI18N['error_no_account'];
		}
	}
}

/** defines page title **/
$esynSmarty->assign_by_ref('title', $esynI18N['resend_email']);

/** breadcrumb formation **/
$bc['resend']['url'] = '';
$bc['resend']['caption'] = $esynI18N['resend_email'];

$breadcrumb = $gDirLayout->printBreadcrumb(0, $bc, 1);

$esynSmarty->display('resend-email.tpl');

