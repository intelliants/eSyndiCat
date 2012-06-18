<?php
//##copyright##

define("ESYN_REALM", "account_register");

if (isset($_GET['id']) && preg_match("/\D/", $_GET['id']))
{
	$_GET['error'] = "404";
	require("./error.php");
	exit;
}

// requires common header file
require_once('.'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'header.php');

$eSyndiCat->factory("Account", "Layout");

if(!$esynConfig->getConfig('accounts'))
{
	$_GET['error'] = "670";
	require(ESYN_HOME."error.php");
	exit;
}

// Smarty and other View related things
require_once(ESYN_INCLUDES.'view.inc.php');

// confirm account registration email address in any case, no matter auto approval enabled/disabled
if (isset($_GET['action']) && ('confirm' == $_GET['action']))
{
	$success = false;
	
	if (isset($_GET['user']) && isset($_GET['key']) && !empty($_GET['user']) && !empty($_GET['key']))
	{
		$result = $esynAccount->row("*", "`sec_key` = '".esynSanitize::sql($_GET['key'])."' AND `username` = '".esynSanitize::sql($_GET['user'])."'");
		
		// if account exists based on input values
		if ($result)
		{
			if ($esynConfig->getConfig('accounts_autoapproval'))
			{
				$status = 'active';
				$msg[] = $esynI18N['reg_confirmed'];
			}
			else
			{
				$status = 'approval';
				$msg[] = $esynI18N['reg_confirmed_pending'];
			}
			
			$esynAccount->update(array('status' => $status, 'sec_key' => ''), "`sec_key` = '".esynSanitize::sql($_GET['key'])."'");
			$success = true;
			
			$event 	= array(
				"action" => "account_confirmed",
				"params" => array(
					"editor"		=> $result['username'],
					"email"			=> $result['email'],
					"status"		=> $status
				)
			);
	
			$esynAccount->mMailer->dispatcher($event);
		}
		else
		{
			$error = true;
			$msg[] = $esynI18N['reg_confirm_err'];
		}
	}
	else
	{
		$error = true;
		$msg[] = $esynI18N['confirm_not_valid'];
	}
	$esynSmarty->assign_by_ref('success', $success);

	/** defines page title **/
	$esynSmarty->assign_by_ref('title', $esynI18N['reg_confirmation']);

	// breadcrumb formation
	$bc['confirm']['caption'] = $esynI18N['reg_confirmation'];
	$breadcrumb = $esynLayout->printBreadcrumb(0, $bc, 1);
	
	$esynSmarty->display('confirm.tpl');
	exit;
}

if($esynConfig->getConfig('captcha') && '' != $esynConfig->getConfig('captcha_name'))
{
	$eSyndiCat->factory("Captcha");
}

$error = false;
$msg = array();

$accountCreated = false;

// set cache time for this page
$esynSmarty->caching = false;

if(isset($_POST['register']))
{
	$account['username'] = isset($_POST['username']) ? $_POST['username'] : '';
	$account['email'] = isset($_POST['email']) ? $_POST['email'] : '';
	$account['password'] = isset($_POST['password']) ? $_POST['password'] : '';
	$account['auto_generate'] = isset($_POST['auto_generate']) ? (int)$_POST['auto_generate'] : 0;
	
	/** check username **/
	if(!preg_match("/^[\w\s]{3,30}$/", $account['username']))
	{
		$error = true;
		$msg[] = $esynI18N['error_username_incorrect'];
	}
	elseif(!$account['username'])
	{
		$error = true;
		$msg[] = $esynI18N['error_username_empty'];
	}
	elseif($esynAccount->exists("`username` = '{$account['username']}'"))
	{
		$error = true;
		$msg[] = $esynI18N['error_username_exists'];
	}
	
	/** check email **/
	if(!esynValidator::isEmail($account['email']))
	{
		$error = true;
		$msg[] = $esynI18N['error_email_incorrect'];
	}
	elseif($esynAccount->exists("`email` = '".esynSanitize::sql($account['email'])."'"))
	{
		$error = true;
		$msg[] = $esynI18N['account_email_exists'];
	}

	/** check password **/
	if (!$account['auto_generate'])
	{
		if (!$account['password'])
		{
			$error = true;
			$msg[] = $esynI18N['error_password_empty'];
		}
		else
		{
			if($_POST['password'] != $_POST['password2'])
			{
				$error = true;
				$msg[] = $esynI18N['error_password_match'];
			}
		}
	}
	
	if ($esynConfig->getConfig('captcha') && '' != $esynConfig->getConfig('captcha_name'))
	{
		if(!$esynCaptcha->validate())
		{
			$error = true;
			$msg[] = $esynI18N['error_captcha'];
		}
	}

	$_SESSION['pass'] = '';
	
	if(!$error)
	{
		// create account
		$account_id = $esynAccount->registerAccount($account);
	
		$accountCreated = true;

		$msg[] = $esynI18N['account_created'];

		$eSyndiCat->startHook("afterAccountCreated");
	}

	$esynSmarty->assign('account', $account);
}

// breadcrumb formation
$bc['register']['caption'] = $esynI18N['register'];
$breadcrumb = $esynLayout->printBreadcrumb(0, $bc, 1);

if($accountCreated)
{
	$title = $esynI18N['account_created'];
	$esynSmarty->assign('email', $account['email']);
	$tpl = 'thank.tpl';
}
else
{
	$title = $esynI18N['register_account'];
	$tpl = 'register.tpl';
}

// defines page title
$esynSmarty->assign_by_ref('title', $title);

$esynSmarty->display($tpl);