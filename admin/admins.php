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


define("ESYN_REALM", "admins");

esynUtil::checkAccess();

$esynAdmin->factory("Admins");

/*
 * ACTIONS
 */
if (isset($_POST['save']))
{
	$esynAdmin->startHook('adminAddAdminValidation');

	if(!defined('ESYN_NOUTF'))
	{
		require_once(ESYN_CLASSES.'esynUtf8.php');

		esynUtf8::loadUTF8Core();
		esynUtf8::loadUTF8Util('ascii', 'validation', 'bad', 'utf8_to_ascii');
	}

	if(isset($_GET['id']))
	{
		$id = (int)$_GET['id'];

		$old_admin = $esynAdmins->row("*", "`id` = :id", array('id' => $id));
	}
	
	$error = false;

	$admin = array();

	/* Checking admin username */
	$admin['username'] = $_POST['username'];

	if(!utf8_is_valid($admin['username']))
	{
		$admin['username'] = utf8_bad_replace($admin['username']);
	}
	
	/* checking admin fullname */
	$admin['fullname'] = $_POST['fullname'];

	if(!utf8_is_valid($admin['fullname']))
	{
		$admin['fullname'] = utf8_bad_replace($admin['fullname']);
	}

	$admin['email'] = $_POST['email'];
	$admin['submit_notif'] = (int)$_POST['submit_notif'];
	$admin['payment_notif'] = (int)$_POST['payment_notif'];
	$admin['status'] = in_array($_POST['status'], array('active', 'inactive')) ? $_POST['status'] : 'inactive';
	$admin['super'] = in_array($_POST['super'], array('0', '1')) ? $_POST['super'] : '0';

	if(isset($_GET['do']) && 'edit' == $_GET['do'])
	{
		if($old_admin['username'] != $admin['username'] && $esynAdmins->exists("`username` = :username", array('username' => $admin['username'])))
		{
			$error = true;
			$msg[] = $esynI18N['username_exists'];
		}
	}
	else
	{
		if($esynAdmins->exists("`username` = :username", array('username' => $admin['username'])))
		{
			$error = true;
			$msg[] = $esynI18N['username_exists'];
		}
	}

	if($_POST['new_pass'] || $_POST['new_pass2'])
	{
		if(!utf8_is_ascii($_POST['new_pass']))
		{
			$error = true;
			$msg[] = $esynI18N['ascii_required'];
		}
		elseif($_POST['new_pass'] != $_POST['new_pass2'])
		{
			$error = true;
			$msg[] = $esynI18N['incorrect_password_confirm'];
		}
		else
		{
			$admin['password'] = md5($_POST['new_pass']);
		}
	}

	if (!$admin['username'])
	{
		$error = true;
		$msg[] = $esynI18N['incorrect_username'];
	}

	if (!$admin['fullname'])
	{
		$error = true;
		$msg[] = $esynI18N['incorrect_fullname'];
	}

	if (!esynValidator::isEmail($admin['email']))
	{
		$error = true;
		$msg[] = $esynI18N['incorrect_email'];
	}

	if (!$_POST['new_pass'] && ($_POST['do'] != 'edit'))
	{
		$error = true;
		$msg[] = $esynI18N['incorrect_password'];
	}

	if('0' == $admin['super'] && empty($_POST['permissions']))
	{
		$error = true;
		$msg[] = $esynI18N['incorrect_permissions'];
	}

	if(isset($_POST['permissions']) && is_array($_POST['permissions']) && '0' == $admin['super'])
	{
		$admin['permissions'] = $_POST['permissions'];
	}

	if (!$error)
	{
		if ('edit' == $_POST['do'])
		{
			$result = $esynAdmins->update($admin, $_POST['id']);

			if($result)
			{
				$msg[] = $esynI18N['changes_saved'];
			}
			else
			{
				$error = true;
				$msg[] = $esynAdmins->getMessage();
			}
		}
		else
		{
			$result = $esynAdmins->insert($admin);

			if($result)
			{
				$msg[] = $esynI18N['admin_added'];
			}
			else
			{
				$error = true;
				$msg[] = $esynAdmins->getMessage();
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

		if(!empty($sort) && !empty($dir))
		{
			$order = "ORDER BY `{$sort}` {$dir}";
		}

		$out['total'] = $esynAdmins->one("COUNT(*)");
		$out['data'] = $esynAdmins->all("*, `id` `edit`, '1' `remove`", "1=1 {$order}", array(), $start, $limit);
		$out['data'] = esynSanitize::applyFn($out['data'], "html");

		if(!empty($out['data']))
		{
			foreach($out['data'] as $key => $value)
			{
				if($value['id'] == $currentAdmin['id'])
				{
					$out['data'][$key]['remove'] = 0;
				}
			}
		}
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

	$out = array('msg' => 'Unknow error', 'error' => true);

	if('update' == $_POST['action'])
	{
		$result = $esynAdmins->update(array($_POST['field'] => $_POST['value']), $_POST['ids']);

		if($result)
		{
			$out['error'] = false;
			$out['msg'] = $esynI18N['changes_saved'];
		}
		else
		{
			$out['error'] = true;
			$out['msg'] = $esynAdmins->getMessage();
		}
	}

	if('remove' == $_POST['action'])
	{
		// check if user tries to remove current admin
		if(is_array($_POST['ids']))
		{
			$key = array_search($currentAdmin['id'], $_POST['ids']);

			if($key)
			{
				unset($_POST['ids'][$key]);
			}
		}

		$result = $esynAdmins->delete($_POST['ids']);

		if($result)
		{
			$out['error'] = false;
			$out['msg'] = $esynI18N['changes_saved'];
		}
		else
		{
			$out['error'] = true;
			$out['msg'] = $esynAdmins->getMessage();
		}
	}

	echo $json->encode($out);
	exit;
}
/*
 * ACTIONS
 */

$gNoBc = false;
$gTitle = $esynI18N['manage_admins'];

$gBc[0]['title'] = $esynI18N['manage_admins'];
$gBc[0]['url'] = 'controller.php?file=admins';

if(isset($_GET['do']))
{
	if ('add' == $_GET['do'])
	{
		$gBc[0]['title'] = $esynI18N['manage_admins'];
		$gBc[0]['url'] = 'controller.php?file=admins';

		$gBc[1]['title'] = $esynI18N['create_admin'];
		$gTitle = $gBc[1]['title'];
	}
	elseif ('edit' == $_GET['do'])
	{
		$gBc[0]['title'] = $esynI18N['manage_admins'];
		$gBc[0]['url'] = 'controller.php?file=admins';

		$gBc[1]['title'] = $esynI18N['edit_admin'];
		$gTitle = $gBc[1]['title'];
	}
}

$actions = array(
	array("url" => "controller.php?file=admins&amp;do=add", "icon" => "add_admin.png", "label" => $esynI18N['create']),
	array("url" => "controller.php?file=admins", "icon" => "view_admin.png", "label" => $esynI18N['view'])
);

require_once(ESYN_ADMIN_HOME.'view.php');

if(isset($_GET['do']))
{
	if('edit' == $_GET['do'])
	{
		$id = (int)$_GET['id'];

		$admin = $esynAdmins->row("*", "`id` = :id", array('id' => $id));

		$esynAdmin->setTable("admin_permissions");
		$admin['permissions'] = $esynAdmin->onefield("`aco`", "`allow` = '1' AND `admin_id` = :id", array('id' => $id));
		$esynAdmin->resetTable();

		if(empty($admin['permissions']))
		{
			$admin['permissions'] = array();
		}

		$esynSmarty->assign('admin', $admin);
	}

	$esynAdmin->setTable("admin_pages");
	$esynAcos = $esynAdmin->keyvalue("`aco`, `title`", "`block_name` != '' GROUP BY `aco`");
	$esynAdmin->resetTable();

	$esynSmarty->assign('esynAcos', $esynAcos);
}

$esynSmarty->display('admins.tpl');

?>
