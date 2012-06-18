<?php
//##copyright##

define("ESYN_REALM", "account_login");

/** requires common header file **/
require_once('.'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'header.php');

$eSyndiCat->factory("Account", "Layout");

$error = false;
$msg = array();

require_once(ESYN_INCLUDES."view.inc.php");

$esynSmarty->cache_lifetime	= 0;
$esynSmarty->caching = false;

if ($esynConfig->getConfig('accounts'))
{
	if (!empty($_POST['login']))
	{
		if (empty($_POST['username']))
		{
			$error = true;
			$msg[] = $esynI18N['error_account_incorrect'];
		}

		if (empty($_POST['password']))
		{
			$error = true;
			$msg[] = $esynI18N['error_accountpsw_incorrect'];
		}

		if (!$error)
		{
			$successfullyAuthenticated	= false;
			$ownAuthenTicationMechanism = false;

			$eSyndiCat->startHook('beforeAuthenticate');

			// default authentication mechanism
			if(!$ownAuthenTicationMechanism)
			{
				$login = esynSanitize::sql($_POST['username']);
				$condition = sprintf("(`username` = '%s' OR `email` = '%s') AND `status` IN ('%s', '%s')", $login, $login, 'active', 'banned');
				$account = $esynAccount->row("`id`, `password`, `status`", $condition);

				if (!$account)
				{
					$error = true;
					$msg[] = $esynI18N['username_empty'];
				}
				elseif ('banned' == $account['status'])
				{
					$error = true;
					$msg[] = $esynI18N['username_banned'];
				}
				elseif ($account['password'] != md5($_POST['password']))
				{
					$error = true;
					$msg[] = $esynI18N['password_incorrect'];
				}
				elseif ('active' == $account['status']) // success
				{
					$successfullyAuthenticated = true;
				}
			}

			if($successfullyAuthenticated)
			{
				$eSyndiCat->startHook('afterLogged');

				$pwd = crypt($account['password'], ESYN_SALT_STRING);

				$expireTime = (isset($_POST['rememberme']) && 1 == $_POST['rememberme']) ? time() + 60 * 60 * 24 * 14 : 0;

				setcookie("account_id",  $account['id'], $expireTime);
				setcookie("account_pwd", $pwd, $expireTime);

				$go2_url = ESYN_MOD_REWRITE ? ESYN_URL.'account-listings.html' : ESYN_URL.'listings.php?view=account';
				
				esynUtil::go2($go2_url);
			}
		}
		else
		{
			$eSyndiCat->startHook('afterFailLogin');
		}
	}
}
else
{
	$_GET['error'] = '670';
	require 'error.php';
	exit;
}

/** defines page title **/
$esynSmarty->assign('title', $esynI18N['account_login']);

/** breadcrumb formation **/
$bc['login']['caption'] = $esynI18N['account_login'];
$breadcrumb = $esynLayout->printBreadcrumb(0, $bc, 1);

$esynSmarty->display('login.tpl');
