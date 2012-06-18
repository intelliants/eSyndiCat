<?php
//##copyright##

global $esynConfig;

require_once(ESYN_PLUGINS.$esynConfig->getConfig('captcha_name').ESYN_DS.'includes'.ESYN_DS.'classes'.ESYN_DS.'captcha.php');

class esynCaptcha extends captcha
{
	function getImage()
	{
		return parent::getImage();
	}

	function validate()
	{
		return parent::validate();
	}
}

?>
