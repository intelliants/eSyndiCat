<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {navigation} function plugin
 *
 * Type:     function<br>
 * Name:     print_categories<br>
 * Purpose:  print navigation menu<br>
 * @author Sergey Ten
 * @param array
 * @param Smarty
 */
function smarty_function_navigation($params, &$smarty)
{
	if ($params['aTotal'] && $params['aTotal'] > $params['aItemsPerPage'])
	{
		global $esynI18N;

		$regex = ESYN_MOD_REWRITE ? '/(_?{page})|([\?|&]page={page})|(index{page}\.html)/' : '/([\?|&]page={page})/';

		$params['aTruncateParam'] = isset($params['aTruncateParam']) ? $params['aTruncateParam'] : 0;

		$num_pages = ceil($params['aTotal'] / $params['aItemsPerPage']);
		$current_page = isset($_GET['page']) && (int)$_GET['page'] > 0 ? (int)$_GET['page'] : 1;
		$current_page = min($current_page, $num_pages);

		$left_offset = ceil($params['aNumPageItems'] / 2) - 1;
		
		$first = $current_page - $left_offset;
		$first = ($first < 1) ? 1 : $first;

		$last = $first + $params['aNumPageItems'] - 1;
		$last = min($last, $num_pages);

		$first = $last - $params['aNumPageItems'] + 1;
		$first = ($first < 1) ? 1 : $first;

		$pages = range($first, $last);

		$out = '<div class="navigation">';

		foreach ($pages as $page)
		{
			if ($current_page == $page)
			{
				$out .= "<span>{$esynI18N['page']} {$page} / {$num_pages}</span>&nbsp;";
				break;
			}
		}

		// the first and previous items menu
		if ($current_page > 1)
		{
			$prev = $current_page - 1;

			$first_url = preg_replace($regex, '', $params['aTemplate']);
			$previous_url = (1 == $prev) ? preg_replace($regex, '', $params['aTemplate']) : str_replace('{page}', $prev, $params['aTemplate']);

			$out .= "<a href=\"{$first_url}\" title=\"{$esynI18N['first']}\">&#171;</a>&nbsp;";
			$out .= "<a href=\"{$previous_url}\" title=\"{$esynI18N['previous']}\">&lt;</a>&nbsp;";
		}

		// the pages items
		foreach ($pages as $page)
		{
			if ($current_page == $page)
			{
				$out .= '<span class="active">'.$page.'</span>&nbsp;';
			}
			else
			{
				if(1 == $page)
				{
					$page_url = preg_replace($regex, '', $params['aTemplate']);
				}
				else
				{
					$page_url = str_replace('{page}', $page, $params['aTemplate']);
				}

				$out .= "<a href=\"{$page_url}\">{$page}</a>&nbsp;";
			}
		}

		// the next and last items menu
		if ($current_page < $num_pages)
		{
			$next = $current_page + 1;

			$next_url = str_replace('{page}', $next, $params['aTemplate']);
			$last_url = str_replace('{page}', $num_pages, $params['aTemplate']);

			$out .= "<a href=\"{$next_url}\" title=\"{$esynI18N['next']}\">&gt;</a>&nbsp;";
			$out .= "<a href=\"{$last_url}\" title=\"{$esynI18N['last']}\">&#187;</a>";
		}

		$out .= '</div>';

		return $out;
	}
}

?>
