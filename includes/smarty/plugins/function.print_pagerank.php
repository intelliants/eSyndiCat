<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {print_pagerank} function plugin
 *
 * Type:     function<br>
 * Name:     print_pagerank<br>
 * Purpose:  print pagerank<br>
 * @author Sergey Ten
 * @param array
 * @param Smarty
 */
function smarty_function_print_pagerank($params, &$smarty)
{
	$out = '';
	$pagerank_label = '';

	if (isset($params['pagerank']))
	{
		global $esynI18N;

		$padding = $params['pagerank'] * 10;

		if (isset($params['label']) && $params['label'])
		{
			$pagerank_label = $params['label'] ? $esynI18N['pagerank'] . ': ' : '';
		}

		if('-1' == $params['pagerank'])
		{
			$pagerank_label .= $esynI18N['not_available'];
		}
		else
		{
			$pagerank_label .= $params['pagerank'] . '/10';
		}

		if(!empty($pagerank_label))
		{
			$out .= "<div class=\"pr-text\">";
			$out .= $pagerank_label;
			$out .= "</div>";
		}
		
		$out .= "<div style=\"float: left;\">";
		$out .= "<div class=\"pagerank\" title=\"{$pagerank_label}\">";
		$out .= "<div class=\"inner-pagerank\" style=\"padding-left: {$padding}%; width: 0;\">";
		$out .= smarty_function_print_img(array('fl' => 'sp.gif', 'full' => true), $smarty);
		$out .= "</div>";
		$out .= "</div>";
		$out .= "</div>";
	}

	echo $out;
}