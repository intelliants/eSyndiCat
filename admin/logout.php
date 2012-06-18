<?php
//##copyright##

require_once('.'.DIRECTORY_SEPARATOR.'header.php');

unset($_SESSION['admin_name'], $_SESSION['admin_pwd'], $_SESSION['admin_lastAction'], $_SESSION['frontendManageMode']);

setcookie('admin_lasturl', '', time() - 3600, '/');

$gNoBc = true;

require_once(ESYN_ADMIN_HOME.'view.php');

$esynSmarty->display('logout.tpl');

?>
