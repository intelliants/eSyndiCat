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


session_start();

header("Content-Type: text/html; charset=utf-8");

/** include configuration file **/
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'config.inc.php');

require_once(ESYN_INCLUDES.'function.php');

start_time_render();

/** checking for tmp directory **/
if(!file_exists(ESYN_TMP))
{
	trigger_error("Temporary Directory Absent | tmp_dir_absent | The 'tmp' directory does not exist. Please create it and set writable permissions.", E_USER_ERROR);
}

if(!is_writeable(ESYN_TMP))
{
	trigger_error("Temporary Directory Permissions | tmp_dir_permissions | The 'tmp' directory is not writable. Please set writable permissions.", E_USER_ERROR);
}

/** including common file classes **/
require_once(ESYN_CLASSES.'esynDatabase.php');
require_once(ESYN_CLASSES.'esynCacher.php');
require_once(ESYN_CLASSES.'esynMailer.php');
require_once(ESYN_CLASSES.'eSyndiCat.php');
require_once(ESYN_CLASSES.'esynConfig.php');

require_once(ESYN_INCLUDES.'util.php');

$eSyndiCat = new eSyndiCat();

time_render('aftereSyndiCatObjectInit');

$esynConfig = &esynConfig::instance();
//$esynConfig = $eSyndiCat;

$eSyndiCat->startHook("theVeryStart");

$esynAccountInfo = null;

$esynConfig->setConfig('esyn_url', ESYN_URL);
$eSyndiCat->mConfig['esyn_url'] = ESYN_URL;

if(isset($_GET['switchToNormalMode']) && !empty($_SESSION['frontendManageMode']))
{
	$_SESSION['frontendManageMode'] = false;
}
elseif(isset($_GET['switchToNormalMode']) && !empty($_SESSION['preview']))
{
	unset($_SESSION['preview']);
}

if (!empty($_COOKIE['account_id']) && !empty($_COOKIE['account_pwd']) && ctype_digit($_COOKIE['account_id']) && $_COOKIE['account_id'] > 0 && !preg_match("/\s/", $_COOKIE['account_pwd']))
{
	$eSyndiCat->factory("Account");

	$esynAccountInfo = $esynAccount->getInfo($_COOKIE['account_id']);	
}

/** Check if account is logged in **/
if (!empty($esynAccountInfo) && is_array($esynAccountInfo))
{
	$pwd = crypt($esynAccountInfo['password'], ESYN_SALT_STRING);
	if (($_COOKIE['account_id'] != $esynAccountInfo['id']) || ($_COOKIE['account_pwd'] != $pwd))
	{
		esynUtil::go2('login.php');
	}
}
elseif (defined("ESYN_THIS_PAGE_PROTECTED"))
{
	esynUtil::go2('login.php');
}

if(isset($esynAccountInfo) && !empty($esynAccountInfo) && '1' == $esynConfig->getConfig('captcha'))
{
	$esynConfig->setConfig('captcha', '0');
}

/** define used language **/
$language = !empty($_GET['language']) ? $_GET['language'] : (!empty($_COOKIE['language']) ? $_COOKIE['language'] : false);

if (!$language || !array_key_exists($language, $eSyndiCat->mLanguages))
{
	$language = $esynConfig->getConfig('lang');
}

$esynConfig->setConfig('lang', $language);

$esynI18N = $eSyndiCat->getI18N($language);

define("ESYN_LANGUAGE", $language);
define("ESYN_MOD_REWRITE", $esynConfig->getConfig('mod_rewrite'));
define("ESYN_CACHING", FALSE);
define("ESYN_VERSION", $esynConfig->getConfig('version'));
define('ESYN_TEMPLATE', ESYN_TEMPLATES.$esynConfig->getConfig('tmpl').'/');

header("X-Drectory-Script: eSyndiCat v".ESYN_VERSION);

if(!$esynConfig->getConfig('display_frontend'))
{
	$error = $esynConfig->getConfig('underconstruction');
	
	$content = file_get_contents(ESYN_INCLUDES.'common'.ESYN_DS.'error_handler.html');

	$error_solutions = '';
	$error_description = $error;
	$error_title = '&nbsp;';

	$search = array('{title}', '{base_url}', '{error_title}', '{error_description}', '{error_solutions}', '{additional}');
	$replace = array($esynConfig->getConfig('site').' '.$esynConfig->getConfig('suffix'), ESYN_URL, $error_title, $error_description, $error_solutions, '');

	$content = str_replace($search, $replace, $content);
	$content = preg_replace('/<p class="solution">.*<\/p>/i', ' ', $content);

	echo $content;
	exit;
}

