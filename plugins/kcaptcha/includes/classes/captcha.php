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
