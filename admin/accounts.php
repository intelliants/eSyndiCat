<?php
//##copyright##

define("ESYN_REALM", "accounts");

esynUtil::checkAccess();

$esynAdmin->factory("Account");

/*
 * ACTIONS
 */

/*
  Add new account
 */
if (isset($_POST['save']))
{
	$esynAdmin->startHook('adminAddAccountValidation');

	if(!defined('ESYN_NOUTF'))
	{
		require_once(ESYN_CLASSES.'esynUtf8.php');

		esynUtf8::loadUTF8Core();
		esynUtf8::loadUTF8Util('ascii', 'validation', 'bad', 'utf8_to_ascii');
	}

	$error = false;

	$account = array();

	$account['username'] = trim($_POST['username']);
	$account['status'] = isset($_POST['status']) && in_array($_POST['status'], array('active', 'approval', 'banned')) ? $_POST['status'] : 'approval';

	if(empty($account['username']))
	{
		$error = true;
		$msg[] = $esynI18N['error_username_empty'];
	}
	else
	{
		if(!utf8_is_ascii($account['username']))
		{
			$error = true;
			$msg[] = 'Username: '.$esynI18N['ascii_required'];
		}
	}

	if(isset($_GET['do']) && 'edit' == $_GET['do'])
	{
		if($account['username'] != $_POST['old_name'])
		{
			if($esynAccount->exists("`username` = :username", array('username' => $account['username'])))
			{
				$error = true;
				$msg[] = $esynI18N['error_username_exists'];
			}
		}
	}
	else
	{
		if($esynAccount->exists("`username` = :username", array('username' => $account['username'])))
		{
			$error = true;
			$msg[] = $esynI18N['error_username_exists'];
		}
	}

	/**
	 * checking password
	 * don't need to check password if user edits account and don't enter password
	 *
	 */
	if(!empty($_POST['password']) || !empty($_POST['password2']))
	{
		$account['password'] = $_POST['password'];
		
		if (empty($account['password']))
		{
			$error = true;
			$msg[] = $esynI18N['error_password_empty'];
		}
		elseif(!utf8_is_ascii($account['password']))
		{
			$error = true;
			$msg[] = 'Password: '.$esynI18N['ascii_required'];
		}
		elseif(md5($account['password']) != md5($_POST['password2']))
		{
			$error = true;
			$msg[] = $esynI18N['error_password_match'];
		}
	}

	if(empty($_POST['password']) && isset($_GET['do']) && 'add' == $_GET['do'])
	{
		$error = true;
		$msg[] = $esynI18N['error_password_empty'];
	}

	// checking email
	$account['email'] = $_POST['email'];

	if(!esynValidator::isEmail($account['email']))
	{
		$error = true;
		$msg[] = $esynI18N['incorrect_email'];
	}

	if (!$error)
	{
		if ('edit' == $_POST['do'])
		{
			$result = $esynAccount->update($account, (int)$_POST['id']);

			if($result)
			{
				$msg[] = $esynI18N['changes_saved'];
			}
			else
			{
				$error = true;

				$msg[] = $esynAccount->getMessage();
			}
		}
		else
		{
			$result = $esynAccount->insert($account);
			
			if($result)
			{
				$msg[] = $esynI18N['account_added'];
			}
			else
			{
				$error = true;

				$msg[] = $esynAccount->getMessage();
			}
		}

		esynMessages::setMessage($msg, $error);

		if(!$error)
		{
			$do = (isset($_POST['goto']) && 'add' == $_POST['goto']) ? 'add' : null;
				
			esynUtil::reload(array("do" => $do));
		}
	}

	esynMessages::setMessage($msg, $error);
}

/*
 * AJAX
 */
if(isset($_GET['action']))
{
	$esynAdmin->loadClass("JSON");

	$json = new Services_JSON();

	$start = (int)$_GET['start'];
	$limit = (int)$_GET['limit'];

	$out = array('data' => '', 'total' => 0);
	
	if('get' == $_GET['action'])
	{
		$sort = $_GET['sort'];
		$dir = in_array($_GET['dir'], array('ASC', 'DESC')) ? $_GET['dir'] : 'ASC';

		$where = array();
		$values = array();

		if(!empty($sort) && !empty($dir))
		{
			$sort = ('date' == $sort) ? 'date_reg' : $sort;

			$order = " ORDER BY `{$sort}` {$dir}";
		}

		if(isset($_GET['status']) && in_array($_GET['status'], array('active', 'approval', 'unconfirmed')))
		{
			$where[] = "`status` = :status";
			$values['status'] = $_GET['status'];
		}

		if(isset($_GET['username']) && !empty($_GET['username']))
		{
			$where[] = "`username` LIKE :username";
			$values['username'] = '%'.$_GET['username'].'%';
		}

		if(isset($_GET['email']) && !empty($_GET['email']))
		{
			$where[] = "`email` = :email";
			$values['email'] = $_GET['email'];
		}

		if(isset($_GET['id']) && !empty($_GET['id']))
		{
			$where[] = "`id` = :id";
			$values['id'] = (int)$_GET['id'];
		}

		if(empty($where))
		{
			$where[] = '1=1';
			$values = array();
		}

		$where = implode(" AND ", $where);

		$out['total'] = $esynAccount->one("COUNT(*)", $where, $values);
		$out['data'] = $esynAccount->all("*, `id` `edit`, IF(`status` = 'unconfirmed', `id`, 0) `sendemail` ", $where.$order, $values, $start, $limit);
	}

	if(empty($out['data']))
	{
		$out['data'] = '';
	}

	echo $json->encode($out);
	exit;
}

if(isset($_POST['action']))
{
	$esynAdmin->loadClass("JSON");

	$json = new Services_JSON();

	$out = array('msg' => 'Unknow error', 'error' => true, 'notes' => '');
	
	if('remove' == $_POST['action'])
	{
		$result = $esynAccount->delete($_POST['ids']);

		if($result)
		{
			$out['error'] = false;
			$out['msg'] = (count($_POST['ids']) > 1) ? $esynI18N['accounts'] : $esynI18N['account'];
			$out['msg'] .= ' '.$esynI18N['deleted'];
		}
		else
		{
			$out['error'] = true;
			$out['msg'] = $esynAccount->getMessage();
		}
	}

	if('update' == $_POST['action'])
	{
		$result = $esynAccount->update(array($_POST['field'] => $_POST['value']), $_POST['ids']);

		if($result)
		{
			$out['error'] = false;
			$out['msg'] = $esynI18N['changes_saved'];
		}
		else
		{
			$out['error'] = true;
			$out['msg'] = $esynAccount->getMessage();
		}
	}
	
	if('sendemail' == $_POST['action'])
	{
		$ids = $esynAccount->convertIds('id', $_POST['ids']);
		$accounts = $esynAccount->all("*", $ids);
		
		if($accounts)
		{	
			foreach($accounts as $account)
			{
				// set a new password for account and update it
				$password = $esynAccount->createPassword();
				$account['password'] = $password;
				$account['sec_key'] = md5(esynUtil::getNewToken());
				
				$esynAccount->update(array('password' => $account['password'], 'sec_key' => $account['sec_key']), $account['id']);
	
				$esynAccount->resendEmail($account);
			}
		
			$out['error'] = false;
			$out['msg'] = $esynI18N['confirmation_resent'];
		}
		else
		{
			$out['error'] = true;
			$out['msg'] = $esynAccount->getMessage();
		}
	}
	
	echo $json->encode($out);
	exit;	
}

/* 
 * ACTIONS
 */

$gNoBc = false;

$gBc[0]['title'] = $esynI18N['manage_accounts'];
$gBc[0]['url'] = 'controller.php?file=accounts';

$gTitle = $esynI18N['manage_accounts'];

if(isset($_GET['do']))
{
	if (('add' == $_GET['do']) || ('edit' == $_GET['do']))
	{
		$gBc[0]['title'] = $esynI18N['manage_accounts'];
		$gBc[0]['url'] = 'controller.php?file=accounts';

		$gBc[1]['title'] = ('edit' == $_GET['do']) ? $esynI18N['edit_account'] : $esynI18N['create_account'];
		$gTitle = $gBc[1]['title'];
	}
}

$actions = array(
	array("url" => "controller.php?file=accounts&amp;do=add", "icon" => "add_account.png", "label" => $esynI18N['create']),
	array("url" => "controller.php?file=accounts", "icon" => "view_account.png", "label" => $esynI18N['view'])
);

require_once(ESYN_ADMIN_HOME.'view.php');

if(isset($_GET['do']))
{
	if('edit' == $_GET['do'])
	{
		$account = $esynAccount->row("*", "`id` = :id", array('id' => (int)$_GET['id']));

		$esynSmarty->assign('account', $account);
	}
}

$esynSmarty->display('accounts.tpl');
