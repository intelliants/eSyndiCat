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


header("Content-Type: text/html; charset=utf-8");

session_start();

/** reload browser cache if the trigger is true **/
if(isset($_SESSION['reloadCache']) && $_SESSION['reloadCache'])
{
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

	$_SESSION['reloadCache'] = false;
}

/** including common file classes **/
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'config.inc.php');

require_once(ESYN_INCLUDES.'function.php');

start_time_render();

/** checking for tmp directory **/
if(!file_exists(ESYN_TMP))
{
	trigger_error('Temporary Directory Absent | tmp_dir_absent | The tmp directory does not exist. Please create it and set writeable permissions.', E_USER_ERROR);
}

if(!is_writeable(ESYN_TMP))
{
	trigger_error('Temporary Directory Permissions | tmp_dir_permissions | The tmp directory is not writeable. Please set writeable permissions.', E_USER_ERROR);
}

define("ESYN_IN_ADMIN", true);
define("ESYN_CACHING", false);

require_once(ESYN_CLASSES.'esynDatabase.php');
require_once(ESYN_CLASSES.'esynCacher.php');
require_once(ESYN_CLASSES.'esynMailer.php');
require_once(ESYN_CLASSES.'eSyndiCat.php');
require_once(ESYN_ADMIN_CLASSES.'esynAdmin.php');
require_once(ESYN_CLASSES.'esynConfig.php');

require_once(ESYN_HOME . ESYN_ADMIN_FOLDER . ESYN_DS . 'util.php');

$esynAdmin = new esynAdmin();

$esynConfig = &esynConfig::instance();

$esynAdmin->startHook("adminTheVeryStart");

$currentAdmin = array();
$login = true;

$esynConfig->setConfig('esyn_url', ESYN_URL);
$esynAdmin->mConfig['esyn_url'] = ESYN_URL;

// user is _not_ at login page and already logged in. just authenticate him
if(false === strpos($_SERVER['SCRIPT_NAME'], "login.php") && !empty($_SESSION['admin_name']) && !empty($_SESSION['admin_pwd']))
{
	$name = $_SESSION['admin_name'];
	
	$esynAdmin->setTable("admins");
	$currentAdmin = $esynAdmin->row("*", "username = :name AND `status` = 'active'", array('name' => $name));
	$esynAdmin->resetTable();

	$esynAdmin->setTable("admin_permissions");
	$currentAdmin['permissions'] = $esynAdmin->onefield("`aco`", "`allow`='1' AND `admin_id` = :id", array('id' => $currentAdmin['id']));
	$esynAdmin->resetTable();
	
	if(!is_array($currentAdmin['permissions']))
	{
		$currentAdmin['permissions'] = array();
	}

	$pwd = crypt($currentAdmin['password'], ESYN_SALT_STRING);
	
	// save the last URL of page admin visited
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 'XMLHttpRequest' == $_SERVER['HTTP_X_REQUESTED_WITH'])
	{
		$last_url = $_SERVER['HTTP_REFERER'];
	}
	else
	{
		$last_url = $_SERVER['REQUEST_URI'];
	}

	setcookie('admin_lasturl', $last_url, time() + 3600, '/');
				
	if (0 === strcmp($pwd, $_SESSION['admin_pwd']))
	{
		$login = false;
		
		// admin activity expiration
		if($_SERVER['REQUEST_TIME'] - $_SESSION['admin_lastAction'] > (60*60))
		{
			$_SESSION['admin_name'] = $_SESSION['admin_pwd'] = false;

			session_destroy();

			$login = true;
		}
		else
		{
			$_SESSION['admin_lastAction'] = $_SERVER['REQUEST_TIME'];
		}
	}
	else
	{
		if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 'XMLHttpRequest' == $_SERVER['HTTP_X_REQUESTED_WITH'])
		{
			$last_url = $_SERVER['HTTP_REFERER'];
		}
		else
		{
			$last_url = $_SERVER['REQUEST_URI'];
		}

		setcookie('admin_lasturl', $last_url, time() + 3600, '/');
				
		$login = true;
	}
}
elseif(false !== strpos($_SERVER['SCRIPT_NAME'], "login.php") || false !== strpos($_SERVER['SCRIPT_NAME'], "password-restore.php"))
{
	$login = false;
}

// Calling all registered hooks to admin authentication
$esynAdmin->startHook("adminAuthentication");

if($login && !defined("ESYN_INTEGRATED"))
{
	$f = ESYN_URL . ESYN_ADMIN_FOLDER . '/';

	$esynAdmin->startHook("adminAuthenticationFailedAction");

	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 'XMLHttpRequest' == $_SERVER['HTTP_X_REQUESTED_WITH'] && in_array($_SERVER['HTTP_X_FLAGTOPREVENTCSRF'], array('using ExtJS', 'using jQuery')))
	{
		header("X-eSyndiCat-Redirect: login");
		exit;
	}

	esynUtil::go2($f . 'login.php');
}

if(!empty($_SESSION['admin_lng']) && array_key_exists($_SESSION['admin_lng'], $esynAdmin->mLanguages))
{
	$language = $_SESSION['admin_lng'];
}
elseif(isset($_GET['lang']) && 'default' == $_GET['lang'] || !empty($_GET['lang']) && array_key_exists($_GET['lang'], $esynAdmin->mLanguages))
{
	$language = $_GET['lang'];
}
else
{
	$language = "en";
}

define("ESYN_LANGUAGE", $language);
define("ESYN_VERSION", $esynConfig->getConfig('version'));
define("ESYN_MOD_REWRITE", $esynConfig->getConfig('mod_rewrite'));
define("ESYN_ADMIN_URL", ESYN_URL . ESYN_ADMIN_FOLDER . '/');

$esynI18N = $esynAdmin->getI18N($language, 'admin');

// protect against CSRF attack - bad referrer
if(!empty($_SERVER['HTTP_REFERER']) && FALSE === strpos($_SERVER['HTTP_REFERER'], strtolower(ESYN_BASE_URL)))
{
	if(false === strpos($_SERVER['SCRIPT_NAME'], "login.php"))
	{
		esynUtil::csrfAttack();
	}
}

// protect against CSRF attack - look for hidden key
// if using XMLHTTPREQUEST then it should be considered as safe
//$doubleSubmit = (!empty($_SESSION['prevent_csrf']) && !empty($_POST['prevent_csrf']) && (!in_array($_POST['prevent_csrf'], $_SESSION['prevent_csrf'], true)));
$emptyCsrf = (!empty($_SESSION['prevent_csrf']) && empty($_POST['prevent_csrf']));
$emptyFlag = (!empty($_POST) && empty($_POST['prevent_csrf']) && !isset($_SERVER['HTTP_X_FLAGTOPREVENTCSRF']));

if($emptyCsrf || $emptyFlag)
{
	/*if($doubleSubmit)
	{
		unset($_SESSION['prevent_csrf']);
		esynUtil::reload();
	}*/
	if(!empty($_POST))
	{
		unset($_SESSION['prevent_csrf']);
		esynUtil::csrfAttack();
	}
}

// action allowed. this var should be deleted
unset($_SESSION['prevent_csrf']);

?>
