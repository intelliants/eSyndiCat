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


/**
 * show phpinfo() information
 */
if(isset($_GET['info']))
{
	die(phpinfo());
}

require_once('.'.DIRECTORY_SEPARATOR.'header.php');

$esynAdmin->factory('Category', 'Listing', 'Account');

// get listings by status
$statuses = array('Approval', 'Banned', 'Active');

foreach($statuses as $key => $status)
{
	$count = $esynListing->one("COUNT(*)", "`status` = '{$status}'");
	$listings[] = array(
		'statuses'	=> $status,
		'total'		=> $count
	);
}

/*
 * ACTIONS
 */
if(isset($_GET['action']))
{
	$esynAdmin->loadClass("JSON");

	$json = new Services_JSON();

	if('getlistingschart' == $_GET['action'])
	{
		$out = $listings;
	}

	if(empty($out))
	{
		$out = '';
	}

	echo $json->encode($out);
	exit;
}

if (isset($_POST['action']))
{
	$esynAdmin->loadClass("JSON");

	$json = new Services_JSON();

	if ('saveState' == $_POST['action'])
	{
		$state = unserialize($currentAdmin['state']);

		if (isset($_POST['data']) && !empty($_POST['data']))
		{
			$state['index_blocks'] = $_POST['data'];
		}

		$state = $currentAdmin['state'] = serialize($state);

		$esynAdmin->setTable('admins');
		$esynAdmin->update(array('state' => $state), "`id` = '{$currentAdmin['id']}'");
		$esynAdmin->resetTable();

		$out['error'] = false;
		$out['msg'] = 'ok';
	}

	if ('submitrequest' == $_POST['action'])
	{
		mail('support@esyndicat.com', $esynConfig->getConfig('site').' - '.$_POST['subject'], $_POST['body'], "From: ".$esynConfig->getConfig('site_email'));

		$out['msg'] = $esynI18N['request_submitted'];
	}

	if(empty($out))
	{
		$out = '';
	}

	echo $json->encode($out);
	exit;
}

// delete all temporary uploaded files that was not deleted
// For example: user tried to submit a link and uploaded some file but due to some (validating) errors listing wasn't submited.
// the temporary file still exist.
$md = date("md");

if(is_dir(ESYN_HOME.'uploads'.ESYN_DS))
{
	chdir(ESYN_HOME.'uploads'.ESYN_DS);

	$temp_files = glob("*.BAK-*");
	
	if(!empty($temp_files))
	{
		foreach($temp_files as $fn)
		{
			if(!preg_match("/.*\.BAK-".$md."/", $fn))
			{
				unlink($fn);
			}
		}
	}

	chdir(dirname(__FILE__));
}

$gNoBc = false;

$gTitle = $esynI18N['admin_panel'];

$gBc[0]['title'] = $esynI18N['admin_panel'];
$gBc[0]['url'] = 'controller.php?file=index';

if(!ini_get('safe_mode'))
{
	set_time_limit(100);
}

$actions = array(
	array("url" => "controller.php?file=browse", "icon" => "browse.png", "label" => $esynI18N['browse']),
	array("url" => "controller.php?file=suggest-category&amp;id=0", "icon" => "create_category.png", "label" => $esynI18N['create_category']),
	array("url" => "controller.php?file=suggest-listing&amp;id=0", "icon" => "create_listing.png", "label" => $esynI18N['create_listing'])
);

require_once(ESYN_ADMIN_HOME.'view.php');

/*
 * Get update version information
 */
if($esynConfig->getConfig('check_updates'))
{
	$esynAdmin->factory("Update");

	$updates = $esynUpdate->getUpdates();

	if(!empty($updates) && version_compare($updates['version'], ESYN_VERSION, ">"))
	{
		$esynI18N['update_msg'] = str_replace('{version}', $updates['version'], $esynI18N['update_msg']);

		$esynSmarty->assign('update_msg', $esynI18N['update_msg']);
	}
}

/*
 * Categories box
 */
$catstats = $esynCategory->keyvalue("`status`, count(*)",'1 GROUP BY `status`');
$rootStatus = $esynCategory->one("`status`", "`id`='0'");
$approval = isset($catstats['approval']) ? $catstats['approval'] : 0;

if($approval > 0 && $rootStatus=='approval')
{
	$approval--;
}

$active = isset($catstats['active']) ? $catstats['active'] : 0;

if($active > 0 && $rootStatus == 'active')
{
	$active--;
}

$summary = $approval + $active;

$esynSmarty->assign('approval', $approval);
$esynSmarty->assign('active', $active);
$esynSmarty->assign('summary', $summary);

/*
 * Listings box
 */
$broken_listings = $esynListing->getNumBroken();

$listingstats = $esynListing->keyvalue("CASE `recip_valid` WHEN '0' THEN 'no' WHEN '1' THEN 'yes' END, count(*) as n","1 GROUP BY `recip_valid`");
$no_reciprocal_listings = isset($listingstats['no']) ? $listingstats['no'] : 0;

$reciprocal_listings = isset($listingstats['yes']) ? $listingstats['yes'] : 0;
$featured_listings = $esynListing->one("count(*)","`featured`='1'");
$partner_listings = $esynListing->one("count(*)","`partner`='1'");

$all_listings = $esynListing->one("count(*)");

$esynSmarty->assign('broken_listings', $broken_listings);
$esynSmarty->assign('no_reciprocal_listings', $no_reciprocal_listings);
$esynSmarty->assign('reciprocal_listings', $reciprocal_listings);

$esynSmarty->assign('listings', $listings);
$esynSmarty->assign('featured_listings', $featured_listings);
$esynSmarty->assign('partner_listings', $partner_listings);
$esynSmarty->assign('all_listings', $all_listings);

/*
 * Account box
 */
if($esynConfig->getConfig('accounts') && $currentAdmin['super'])
{
	$stats = $esynAccount->keyvalue("`status`, count(id)", '1=1 GROUP BY `status`');
	
	$approval_accounts = !empty($stats['approval']) ? $stats['approval'] : 0;
	$active_accounts = !empty($stats['active']) ? $stats['active'] : 0;
	$unconfirmed_accounts = !empty($stats['unconfirmed']) ? $stats['unconfirmed'] : 0;
	
	$all_accounts = $approval_accounts + $active_accounts + $unconfirmed_accounts;

	$esynSmarty->assign('approval_accounts', $approval_accounts);
	$esynSmarty->assign('active_accounts', $active_accounts);
	$esynSmarty->assign('unconfirmed_accounts', $unconfirmed_accounts);
	$esynSmarty->assign('all_accounts', $all_accounts);
}

/*
 * Display eSyndiCat News
 */
require_once(ESYN_INCLUDES.'rss2array'.ESYN_DS.'rss2array.php');

$news_url = $esynConfig->getConfig('esyndicat_news_url');

if(!empty($news_url))
{
	$esyndicat_news = rss2array($news_url);

	$esynSmarty->assign('esyndicat_news', $esyndicat_news);
}

/*
 * Display eSyndiCat New Plugins
 */
if($esynConfig->getConfig('display_new_plugins'))
{
	require_once(ESYN_INCLUDES.'rss2array'.ESYN_DS.'rss2array.php');

	$new_plugins_url = $esynConfig->getConfig('esyndicat_new_plugins_url');

	if(!empty($new_plugins_url))
	{
		$esyndicat_new_plugins = rss2array($new_plugins_url.'?version='.ESYN_VERSION);
		
		$esynSmarty->assign('esyndicat_new_plugins', $esyndicat_new_plugins);
	}
}

$esynAdmin->startHook("adminIndexBeforeDisplay");

$esynSmarty->display('index.tpl');
