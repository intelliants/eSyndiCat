<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {print_categories} function plugin
 * prints categories blocks on frontend. Used for related and neighbour categories also
 *
 * Type:     function<br>
 * Name:     print_categories<br>
 * Purpose:  printing categories box<br>
 * @author
 * @param array
 * @param Smarty
 */
function smarty_function_print_categories($params, &$smarty)
{
	global $esynConfig, $eSyndiCat;
	
	if (!class_exists("esynCategory"))
	{
		$eSyndiCat->factory("Category");
	}
	
	global $esynCategory;

	$aCols = (isset($params['aCols']) && 0 != $params['aCols']) ? $params['aCols'] : $esynConfig->getConfig('num_categories_cols');
	$aSubcategories = (isset($params['aSubcategories']) && 0 != $params['aSubcategories']) ? $params['aSubcategories'] : $esynConfig->getConfig('subcats_display');
	$noFollow = isset($params['nofollow']) ? $params['nofollow'] : '';
	$display_type = isset($params['display_type']) && in_array($params['display_type'], array('horizontal', 'vertical')) ? $params['display_type'] : 'vertical';

	$path_title = isset($params['path_title']) ? $params['path_title'] : false;
	$truncate_path_title = isset($params['truncate_path_title']) ? $params['truncate_path_title'] : false;
	$icon_config_name = isset($params['icon_config_name']) ? $params['icon_config_name'] : 'default_categories_icon';

	$out = '';
	$default_icon = $esynConfig->getConfig($icon_config_name);

	$cnt = 0;
	$row = 1;
	$col = 1;

	$vertical_categories = array();

	if(isset($params['aCategories']))
	{
		$rows = ceil(count($params['aCategories']) / $aCols);
		
		if('vertical' == $display_type)
		{
			$out = '<table border="0" class="categories">';
		}
		else
		{
			$out = '<div class="categories">';
		}
		
		$categories_count = count($params['aCategories']);
		$rows = ceil(count($params['aCategories']) / $aCols);
		$dimension = '';

		if($esynConfig->getConfig('categories_icon_width') > 0)
		{
			$dimension .= 'width="' . $esynConfig->getConfig('categories_icon_width') . '"';
		}

		if($esynConfig->getConfig('categories_icon_height') > 0)
		{
			$dimension .= 'height="' . $esynConfig->getConfig('categories_icon_height') . '"';
		}

		foreach($params['aCategories'] as $key => $value)
		{
			$cnt++;
			
			$subcats = '';
			
			$url = esynLayout::printCategoryUrl(array('cat' => $value));

			$cause = '';
			if ($esynConfig->getConfig('num_listings_display'))
			{
				$cause = ' ('.(int)$value['num_all_listings'].')';
			}

			$crossed = isset($value['crossed']) && $value['crossed'] ? ' @' : '';

			if (isset($value['subcategories']) && !empty($value['subcategories']))
			{
				$subcats_count = count($value['subcategories']);
				
				if (isset($value['subcategories']))
				{
					$subcats = "<div class=\"subcategories\" style=\"padding-left: " . $esynConfig->getConfig('categories_icon_width') . "px; \">";
					$cnt2 = 1;
					foreach ($value['subcategories'] as $key2=>$value2)
					{
						$value2['title'] = esynSanitize::html($value2['title']);
						$min = ($subcats_count < $aSubcategories) ? $subcats_count : $aSubcategories;
						$divid = ($cnt2 < $min) ? ', ' : ",&nbsp;<a href=\"{$url}\">...</a>";
						$nofollow2 = $noFollow || $value['no_follow'] || $value2['no_follow'] ? ' rel="nofollow"' : '';
						
						$url2 = esynLayout::printCategoryUrl(array('cat' => $value2));
						$subcats .= "<a href=\"{$url2}\" class=\"countable categories\" id=\"ctg_{$value2['id']}\"{$nofollow2}>{$value2['title']}</a>{$divid}";
						$cnt2++;
						if ($esynConfig->getConfig('num_cols_suffix'))
						{
							if ($cnt2 > $aSubcategories)
							{
								break;
							}
						}
					}
					if ($esynConfig->getConfig('num_cols_suffix'))
					{
						$subcats .= ($subcats_count > $aSubcategories) ? '...' : '';
					}
					$subcats .= "</div>";
				}
			}

			$nofollow = $noFollow || $value['no_follow'] ? ' rel="nofollow"' : '';

			$icon = isset($value['icon']) && !empty($value['icon']) ? $value['icon'] : $default_icon;

			$widthCol = 100 / $aCols - 1;

			$categories = $esynCategory->getParents(array("title"), $value['id']);
			if ($path_title)
			{
				$category_title = $category_full_title = implode(' > ', $categories);
				if ($truncate_path_title && strlen($category_title) > $truncate_path_title)
				{
					$start = strpos($category_title, '>')+1;
					$end = strrpos($category_title, '>');
					$category_title = substr_replace($category_title, '...', $start, $end-$start);
				}
			}
			else
			{
				$category_title = $category_full_title = esynSanitize::html($value['category_title'], ENT_QUOTES);
			}
			
			if('horizontal' == $display_type)
			{
				if (!($cnt % $aCols) || ($cnt == $categories_count))
				{
					$out .= '<div class="last" style="width: '.(int)$widthCol.'%;">';
					$out .= '<div class="categ">';
					$out .= "<img src=\"{$icon}\" alt=\"".$category_title."\" {$dimension} />";
					$out .= "<a title=\"{$category_full_title}\" href=\"{$url}\" class=\"countable categories\" id=\"ctg_{$value['id']}\"{$nofollow}>".$category_title."</a>{$crossed}{$cause}</div>";
					$out .= isset($subcats) ? $subcats : '';
					$out .= '</div>';

					if ($row < ($categories_count / (int)$aCols))
					{
						$out .= '<div class="divider"></div>';
					}

					$row++;
				}
				else
				{
					$out .= '<div class="col" style="width: '.(int)$widthCol.'%;">';
					$out .= '<div class="categ">';
					$out .= "<img src=\"{$icon}\" alt=\"".$category_title."\" {$dimension} />";
					$out .= "<a title=\"{$category_full_title}\" href=\"{$url}\" class=\"countable categories\" id=\"ctg_{$value['id']}\"{$nofollow}>".$category_title."</a>{$crossed}{$cause}</div>";
					$out .= isset($subcats) ? $subcats : '';
					$out .= '</div>';
				}
			}
			else
			{
				$tmp = "<img src=\"{$icon}\" alt=\"".$category_title."\" {$dimension} />";;
				$tmp .= "<a href=\"{$url}\" title=\"{$category_full_title}\" class=\"countable categories\" id=\"ctg_{$value['id']}\"{$nofollow}>".$category_title."</a>{$crossed}{$cause}";
				$tmp .= isset($subcats) ? $subcats : '';

				$vertical_categories[$col][] = $tmp;

				if(($cnt % $rows == 0) || $cnt == $categories_count)
				{
					$col++;
				}
			}
		}

		if('vertical' == $display_type)
		{
			$vertical_categories_count = count($vertical_categories);

			if(!empty($vertical_categories))
			{
				for($i = 0; $i < $rows; $i++)
				{
					for($j = 1; $j <= $aCols; $j++)
					{
						$out .= (1 == $j) ? '<tr>' : '';

						$out .= (isset($vertical_categories[$j][$i])) ? '<td style="vertical-align: top; width: '.(int)$widthCol.'%;">' . $vertical_categories[$j][$i] . '</td>' : '';

						$out .= ($vertical_categories_count == $key || !isset($vertical_categories[$j][$i])) ? '</tr>' : '';
					}
				}
			}
		}
	}

	if('vertical' == $display_type)
	{
		$out .= '</table>';
	}
	else
	{
		$out .= '<div class="divider"></div>';
		$out .= '</div>';
	}

	return $out;
}
?>
