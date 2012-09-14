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


define("ESYN_REALM", "update");

esynUtil::checkAccess();

$esynAdmin->factory("Update");

$msg = '';
$error = false;
$success = false;
$notification = array();

/**
 * ACTIONS
 */
if(isset($_POST['update']))
{
	$esynUpdate->doUpdateCore();

	$msg[] = $esynUpdate->getMsg();
	$msg[] = $esynUpdate->getUpdateInfo();
	$success = $esynUpdate->success;
	$error = !$success;

	esynMessages::setMessage($msg, $error);
}

$gNoBc = false;
$gTitle = $esynI18N['update_version'];

$gBc[0]['title'] = $esynI18N['update_version'];
$gBc[0]['url'] = 'controller.php?file=update';

require_once(ESYN_ADMIN_HOME.'view.php');

if(!$success)
{
	$esyndicat_messages[] = array(
		'type'	=> 'alert',
		'msg'	=> $esynI18N['update_notice']
	);
}

$esynSmarty->assign('success', $success);

$esynSmarty->display('update.tpl');

?>
