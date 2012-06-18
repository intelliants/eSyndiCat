<?php
//##copyright##

class captcha
{
	var $captcha = null;
	var $config = null;

	function getImage()
	{
		global $esynConfig;
		global $esynI18N;

		$url = ESYN_URL."controller.php?plugin=kcaptcha";
		$num_chars = $esynConfig->getConfig('captcha_num_chars');

		$html = '<p class="field-captcha">';
		$html .= "<img id=\"captcha_image_1\" src=\"{$url}\" onclick=\"$('#captcha_image_1').attr('src', '{$url}&amp;h='+Math.random())\" title=\"{$esynI18N['redraw_captcha']}\" alt=\"captcha\" style=\"cursor:pointer; margin-right: 10px;\" align=\"left\" />";
		$html .= "{$esynI18N['text_captcha']}<br />{$esynI18N['redraw_captcha']}<br />";
		$html .= "<input type=\"text\" class=\"text\" name=\"security_code\" size=\"{$num_chars}\" maxlength=\"{$num_chars}\" id=\"securityCode\" />";
		$html .= '</p>';
		$html .= '<div style="clear: both;"></div>';

		return $html;
	}

	function validate()
	{
		global $esynConfig;

		$sc1 = $_POST['security_code'];
		$sc2 = $_SESSION['pass'];
		
		$func = $esynConfig->getConfig('captcha_case_sensitive') ? "strcmp" : "strcasecmp";

		if (empty($_SESSION['pass']) || $func($sc1, $sc2) !== 0)
		{
			return false;
		}

		$_SESSION['pass'] = '';

		return true;
	}

	function getPreview()
	{
		$url = ESYN_URL."controller.php?plugin=kcaptcha";

		$html = "<img id=\"captcha_image_1\" src=\"{$url}\" onclick=\"$('#captcha_image_1').attr('src', '{$url}&amp;h='+Math.random())\" title=\"\" alt=\"captcha\" style=\"cursor:pointer; margin-right: 10px;\" align=\"left\" />";

		return $html;
	}
}
?>
