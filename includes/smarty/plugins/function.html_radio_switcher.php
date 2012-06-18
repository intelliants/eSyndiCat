<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * prints on/off iphone switcher
 * 
 * @param object $params
 * @param object $smarty
 * @return html code
 */
function smarty_function_html_radio_switcher($params, &$smarty)
{
	$value = $params['value'];
	$name = $params['name'];

	if (isset($params['conf']))
	{
		$id = 'conf-'.$name;
		
		$name = "param[".$name."]";
		$class = ('1' == $value) ? 'on' : 'off';
	}
	else
	{
		$id = $name;
		
		if (isset($value) && isset($_GET['do']) && 'edit' == $_GET['do'])
		{
			if ('1' == $value)
			{
				$class = 'on';
			}
			elseif ('0' == $value)
			{
				$class = 'off';
			}
		}
		elseif(isset($_POST[$name]))
		{
			if ('1' == $_POST[$name])
			{
				$class = 'on';
			}
			else
			{
				$class = 'off';
			}	
		}
		elseif(empty($value))
		{
			$class = 'off';
		}
	}

	$out = '<span class="iphoneswitch '.$class.'" id="box-'.$id.'" style="float: left;"></span>';
	$out .= '<input type="hidden" name="'.$name.'" value="'.$value.'" />';
	
	echo $out;
}
?>