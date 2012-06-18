<?php
//##copyright##

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
