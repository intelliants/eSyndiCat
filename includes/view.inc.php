<?php
//##copyright##

$eSyndiCat->loadClass("Smarty");

$eSyndiCat->startHook("phpFrontViewAfterSmartyLoad");

// preview
if(isset($_GET['preview']) || isset($_SESSION['preview']))
{
	$template = $_SESSION['preview'] = isset($_GET['preview']) ? $_GET['preview'] : $_SESSION['preview'];
	$esynConfig->setConfig('tmpl', $template);
}
else
{
	$template = $eSyndiCat->mConfig['tmpl'];
}

$esynSmarty = new esynSmarty($template);
$esynSmarty->tmpl = $template;

$esynSmarty->mHooks = $eSyndiCat->mHooks;

$eSyndiCat->createJsCache();

// preview
function smarty_block_dynamic($param, $content, &$smarty)
{
	return $content;
}

function insert_dynamic($params, &$smarty)
{
	require_once(SMARTY_DIR."plugins".ESYN_DS."function.eval.php");

	return smarty_function_eval(array("var" => $params['content']), $smarty);
}

$esynSmarty->register_block('dynamic', 'smarty_block_dynamic', false);

/** global arrays used in the script **/
$esynSmarty->assign_by_ref('config', $eSyndiCat->mConfig);
$esynSmarty->assign_by_ref('esynAccountInfo', $esynAccountInfo);
$esynSmarty->assign('lang', $esynI18N);
$esynSmarty->assign_by_ref('breadcrumb', $breadcrumb);

$esynSmarty->assign_by_ref('msg', $msg);
$esynSmarty->assign_by_ref('error', $error);

// view listing templates definition
$esynSmarty->assign(array(
	'printregular'		=> $esynSmarty->printRegular,
	'printsponsored'	=> $esynSmarty->printSponsored,
	'printfeatured'		=> $esynSmarty->printFeatured,
	'printpartner'		=> $esynSmarty->printPartner)
);

$esynSmarty->register_function("convertStr", array("esynUtil", "convertStr"));
$esynSmarty->register_function("print_listing_url", array("esynLayout", "printListingUrl"));
$esynSmarty->register_function("print_category_url", array("esynLayout", "printCategoryUrl"));
$esynSmarty->register_function("print_account_url", array("esynLayout", "printAccUrl"));

$templs = ESYN_URL.'templates/'.$esynConfig->getConfig('tmpl');

$esynSmarty->assign('templates', $templs);
$esynSmarty->assign('img', $templs.'/img/');

/** check if language switching is enabled **/
if ($esynConfig->getConfig('language_switch'))
{
	// 604800 is one week in seconds
	$languages = $eSyndiCat->mCacher->get("languages", 604800, true);

	if (!$languages)
	{
		$eSyndiCat->setTable("language");
		$languages = $eSyndiCat->keyvalue("`code`,`lang`", "1 GROUP BY `code`");
		$eSyndiCat->resetTable();
		$eSyndiCat->mCacher->write("languages", $languages);
	}

	if (count($languages) == 1)
	{
		$esynConfig->setConfig("language_switch", 0);
	}
	else
	{
		$esynSmarty->assign('languages', $languages);
	}

	/** define language **/
	if (!empty($_GET['language']))
	{
		if (!empty($_COOKIE['language']))
		{
			setcookie("language", $_COOKIE['language'], $_SERVER['REQUEST_TIME'] - 3600);
		}
		if (!empty($_GET['language']))
		{
			setcookie("language", $_GET['language'], 0);
		}
	}
}

/*
 * Getting pages
 */
$eSyndiCat->factory("Page", "Listing");

$menus = $esynPage->getPages();

$esynSmarty->assign_by_ref('menus', $menus);

$eSyndiCat->startHook("beforeBlocksLoad");

// category id
if (!isset($id))
{
	$id = '0';
}
else
{
	$id = preg_replace("/\D/","", $id);
}

/*
 * Getting blocks
 */
$eSyndiCat->setTable("blocks");

$sql = "SELECT DISTINCT `blocks`.* FROM `{$eSyndiCat->mPrefix}blocks` `blocks` ";
$sql .= "LEFT JOIN `{$eSyndiCat->mPrefix}block_show` `block_show` ";
$sql .= "ON `blocks`.`id` = `block_show`.`block_id` ";
$sql .= "WHERE `blocks`.`status` = 'active' ";
$sql .= "AND (`blocks`.`sticky` = '1' OR `block_show`.`page` = '".ESYN_REALM."' ";
$sql .= ('page' == ESYN_REALM) ? "OR `block_show`.`page` = '{$page['name']}') AND " : ") AND ";

if($eSyndiCat->mPlugins)
{
	$sql .= "`blocks`.`plugin` IN('', '".join("','", $eSyndiCat->mPlugins)."') ";
}
else
{
	$sql .= "`blocks`.`plugin` = '' ";
}

$sql .= "ORDER BY `blocks`.`position`, `blocks`.`order`";

$blocks = $eSyndiCat->getAll($sql);

$positions = array();

if($blocks)
{
	foreach($blocks as $key => $b)
	{
		if('0' == $b['multi_language'])
		{
			$eSyndiCat->setTable("language");

			$b['title'] = $eSyndiCat->one("`value`", "`key` = 'block_title_blc{$b['id']}' AND `code` = '" . ESYN_LANGUAGE . "'");
			$b['contents'] = $eSyndiCat->one("`value`", "`key` = 'block_content_blc{$b['id']}' AND `code` = '" . ESYN_LANGUAGE . "'");

			$eSyndiCat->resetTable();
		}

		$pos = $b['position'];
		${$pos."Blocks"}[] = $b;
		$positions[$pos] = 1;
	}
	foreach($positions as $p=>$d)
	{
		$esynSmarty->assign_by_ref($p."Blocks",  ${$p."Blocks"});
	}
}
$eSyndiCat->resetTable();

/*
 * Getting eSyndiCat actions
 */
$sql = "SELECT DISTINCT `actions`.* FROM `{$eSyndiCat->mPrefix}actions` `actions` ";
$sql .= "LEFT JOIN `{$eSyndiCat->mPrefix}action_show` `action_show` ";
$sql .= "ON `actions`.`name` = `action_show`.`action_name` ";
$sql .= "WHERE `action_show`.`page` = '".ESYN_REALM."' ";
$sql .= "ORDER BY `actions`.`order`";

$esyndicat_actions = $eSyndiCat->getAll($sql);

if(!empty($esyndicat_actions))
{
	$esynSmarty->assign('esyndicat_actions', $esyndicat_actions);
}

$order = $order_type = false;


/** get sponsored listings **/
if ($esynConfig->getConfig('sponsored_listings'))
{
	$esynSmarty->assign('sponsored_listings', $esynListing->getSponsored($id, 0, $esynConfig->getConfig('num_sponsored_display')));
}

/** get featured listings **/
$esynSmarty->assign('featured_listings', $esynListing->getFeatured($id, 0, $esynConfig->getConfig('num_featured_display')));

/** get partner listings **/
$esynSmarty->assign('partner_listings', $esynListing->getPartner($id, 0, $esynConfig->getConfig('num_partner_display')));


$eSyndiCat->startHook("bootstrap");

$esynSmarty->assign("manageMode", !empty($_SESSION['frontendManageMode']) && !empty($_SESSION['admin_name']));

$instead_thumbnail = $esynListing->getThumbnail();

$esynSmarty->assign('instead_thumbnail', $instead_thumbnail);
