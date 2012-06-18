<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty clearemptyblocks outputfilter plugin
 *
 * File:     outputfilter.clearemptyblocks.php<br>
 * Type:     outputfilter<br>
 * Name:     clearemptyblocks<br>
 * Date:     Nov 21, 2007<br>
 * Purpose:  clear output content which generates by Esyndicat script
 *           Each block should begins with comment <!--__b_{BLOCK_ID}-->
 *           and ends with <!--__e_{BLOCK_ID}-->
 *           Each block content should begins with comment <!--__b_c_{BLOCK_ID}-->
 *           and ends with <!--__e_c_{BLOCK_ID}-->
 * Install:  Drop into the plugin directory, call
 *           <code>$smarty->load_filter('output','clearemptyblocks');</code>
 *           from application.
 * @author   Adigamov Ruslan <radigamov at intelliants dot com>
 * @author Contributions from Bastov Pavel <pbastov@intelliants.com>
 * @version  1.0
 * @param string
 * @param Smarty
 */
function smarty_outputfilter_clearemptyblocks($source, &$smarty)
{
	$pattern = '/<!--__b_(\d+).*?-->.*?<!--__b_c_\1-->(.*?)<!--__e_c_\1-->.*?<!--__e_\1-->/s';
	if (preg_match_all($pattern, $source, $matches))
	{
		if ($matches)
		{
			foreach ($matches[0] as $key => $value)
			{
				$content = trim($matches[2][$key]);
				if (empty($content))
				{
					$source = str_replace($value, '', $source);
				}
			}
		}
	}
	return $source;
}
