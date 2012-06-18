<?php
//##copyright##

define("ESYN_REALM", "visual");

esynUtil::checkAccess();

$_SESSION['frontendManageMode'] = true;

esynUtil::go2(ESYN_URL);

?>
