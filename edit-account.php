<?php
//##copyright##

define("ESYN_REALM", "edit_account");

define("ESYN_THIS_PAGE_PROTECTED", true);

/** requires common header file **/
require_once('.'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'header.php');

include(ESYN_INCLUDES.'view.inc.php');

$esynSmarty->caching = false;

$eSyndiCat->factory("Account", "Layout");

$error = false;
$msg = array();

if (isset($_POST['change_email']))
{
	$account['nemail'] = $_POST['email'];
	
	// check email
	if(!esynValidator::isEmail($account['nemail']))
	{
		$error = true;
		$msg[] = $esynI18N['error_email_incorrect'];
	}
	
	if($_POST['old_email'] != $account['nemail'])
	{
		if($esynAccount->exists("`email` = :email", array('email' => $account['nemail'])))
		{
			$error = true;
			$msg[] = $esynI18N['account_email_exists'];
		}
	}
	
	if (!$error)
	{
		$esynAccountInfo['nemail'] = $account['nemail'];

		$esynAccount->confirmEmail($esynAccountInfo, 'change_email');

		$msg[] = $esynI18N['instructions_change_email_sent'];
	}
}

if (isset($_POST['change_pass']))
{
	$eSyndiCat->startHook("editAccountBeforeValidation");

	if(!defined('ESYN_NOUTF'))
	{
		require_once(ESYN_CLASSES.'esynUtf8.php');

		esynUtf8::loadUTF8Core();
		esynUtf8::loadUTF8Util('ascii', 'validation', 'bad', 'utf8_to_ascii');
	}

	/** checks for current password **/
	if ($esynAccountInfo['password'] != md5($_POST['current']))
	{
		$error = true;
		$msg[] = $esynI18N['password_incorrect'];
	}

	if (!$_POST['new'])
	{
		$error = true;
		$msg[] = $esynI18N['password_empty'];
	}

	if ($_POST['new'] != $_POST['confirm'])
	{
		$error = true;
		$msg[] = $esynI18N['passwords_not_match'];
	}

	/** clear compiled templates **/
	if (!$error)
	{
		if (utf8_is_ascii($_POST['new']))
		{
			// delete cookies
			setcookie('account_id', '',	$_SERVER['REQUEST_TIME'] - 3600);
			setcookie('account_pwd', '', $_SERVER['REQUEST_TIME'] - 3600);

			$esynAccount->changePassword($esynAccountInfo['id'], $_POST['new']);

			$pwd = crypt(md5($_POST['new']), ESYN_SALT_STRING);

			setcookie("account_id", $esynAccountInfo['id']);
			setcookie("account_pwd", $pwd);

			$msg[] = $esynI18N['password_changed'];
		}
		else
		{
			$error = true;
			$msg[] = $esynI18N['ascii_required'];
		}
	}
}

// defines page title
$esynSmarty->assign_by_ref('title', $esynI18N['edit_account']);

// breadcrumb formation
$bc['account-links']['caption'] = $esynI18N['edit_account'];
$breadcrumb = $esynLayout->printBreadcrumb(0, $bc, 1);

$esynSmarty->display('edit-account.tpl');
