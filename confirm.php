<?php
//##copyright##

define("ESYN_REALM", "confirm");

/** requires common header file **/
require_once('.'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'header.php');

include(ESYN_INCLUDES.'view.inc.php');

$esynSmarty->caching = false;

$eSyndiCat->factory("Account", "Layout");

$error = false;
$msg = array();

if (isset($_GET['action']))
{
	if ('change_email' == $_GET['action'])
	{
		$account = $esynAccount->row("*", "`id` = :id AND `sec_key` = :sec_key AND `nemail` != ''", array('id' => (int)$_GET['account'], 'sec_key' => $_GET['r']));

		if (!$account)
		{
			$error = true;
			$msg[] = $esynI18N['error_no_account_email'];
		}

		if (!$error)
		{
			$esynAccount->setNewAccountEmail($account);
			$msg[] = $esynI18N['account_successful_change_email'];
		}
	}

	if ('restore_password' == $_GET['action'])
	{
		$account = $esynAccount->row("*", "`id` = :id AND `sec_key` = :sec_key", array('id' => (int)$_GET['account'], 'sec_key' => $_GET['r']));

		if (!$account)
		{
			$form = false;

			$error = true;
			$msg[] = $esynI18N['error_no_account_email'];
		}

		if (!$error)
		{
			$form = false;

			$esynAccount->setNewPassword($account);
			$msg[] = $esynI18N['new_password_sent'];
		}
	}
}

// defines page title
$esynSmarty->assign_by_ref('title', $esynI18N['confirm_email']);

// breadcrumb formation
$bc['account-links']['caption'] = $esynI18N['confirm_email'];
$breadcrumb = $esynLayout->printBreadcrumb(0, $bc, 1);

$esynSmarty->display('confirm.tpl');

?>
