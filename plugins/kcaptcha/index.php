<?php

require_once(dirname(__FILE__).ESYN_DS.'includes'.ESYN_DS.'kcaptcha'.ESYN_DS.'captcha.php');

$captcha = new KCAPTCHA();

$captcha->length = $esynConfig->getConfig('captcha_num_chars');

echo $captcha->getImage();

$_SESSION['pass'] = $captcha->getKeyString();

?>
