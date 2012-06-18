<?php
//##copyright##

session_start();

header("Content-Type: text/html; charset=utf-8");

// configs, includes, authentication, authorization section
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'config.inc.php');

/** checking for tmp directory **/
if(!file_exists(ESYN_TMP))
{
	trigger_error('Temporary Directory Absent | tmp_dir_absent | The tmp directory does not exist. Please create it and set writeable permissions.', E_USER_ERROR);
}

if(!is_writeable(ESYN_TMP))
{
	trigger_error('Temporary Directory Permissions | tmp_dir_permissions | The tmp directory is not writeable. Please set writeable permissions.', E_USER_ERROR);
}

/** including common file classes **/
require_once(ESYN_CLASSES.'esynDatabase.php');
require_once(ESYN_CLASSES.'esynCacher.php');
require_once(ESYN_CLASSES.'esynMailer.php');
require_once(ESYN_CLASSES.'eSyndiCat.php');
require_once(ESYN_CLASSES.'esynConfig.php');

require_once(ESYN_INCLUDES.'util.php');

$eSyndiCat = new eSyndiCat();

$esynConfig = &esynConfig::instance();

$eSyndiCat->startHook("theVeryStart");

$esynConfig->setConfig('esyn_url', ESYN_URL);

if(isset($_GET['switchToNormalMode']) && !empty($_SESSION['frontendManageMode']))
{
	$_SESSION['frontendManageMode'] = false;
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
define("ESYN_CACHING", $esynConfig->getConfig('caching'));
define("ESYN_VERSION", $esynConfig->getConfig('version'));

header("X-Drectory-Script: eSyndiCat v".ESYN_VERSION);

?>
