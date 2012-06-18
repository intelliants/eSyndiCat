<?php
//##copyright##

if(!isset($_GET['id']) || preg_match("/\D/", $_GET['id']))
{
	header("HTTP/1.1 404 Not found");
	print("Powered By eSyndicat ");
	die();
}

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'header.php');

$eSyndiCat->factory("Category");

$eSyndiCat->loadClass('JSON');

$json = new Services_JSON();

$catId = (int)$_GET['id'];

if(isset($_GET['type']) && 'dropdown' == $_GET['type'])
{
	$out = getDropMenu(-1);

	echo $out;

	exit;
}
elseif(isset($_GET['type']) && 'simple dropdown' == $_GET['type'])
{
	$out = '';
	$iter = '';
	
	getSimpleDropDown(-1, $out, $iter);

	echo $out;
	
	exit;
}
else
{
	$out = getTree($catId);

	echo $json->encode($out);
}

if(empty($out))
{
	$out[] = 'null';
}

function getSimpleDropDown($aCategory, &$tree, &$iter)
{
	global $esynCategory;

	$categories = $esynCategory->getAllByParent($aCategory);
	
	foreach($categories as $key => $category)
	{
		$div = '';
		
		$subcategories = $esynCategory->getAllByParent($category['id']);

		$tree .= "<option value=\"{$category['id']}\" class=\"{$category['title']}\">";
		
		if ($category['level'] >= 1)
		{
			$div = '&#x251C;';
			
			$div = ($iter == $esynCategory->one("count(*)", "`status`='active'")) ? '&#x2514;' : $div;
		
			for($j = 0; $j < $category['level']; $j++)
			{
				$div .= '&ndash;';
			}
		}
		else
		{
			$div = $iter ? '&#x251C;' : '&#x250C;';
			$div = ($iter == $esynCategory->one("count(*)") - 1) ? '&#x2514;' : $div;
		}
		
		if ($subcategories)
		{
			$tree .= $div.$category['title'];
		}   
		else
		{
			$tree .= $div.$category['title'];
		}
		$tree .= "</option>";

		$iter++;
		$div = '';
		
		if($subcategories)
		{
			getSimpleDropDown($category['id'], $tree, $iter);
		}
		
	}
}

function getTree($catId)
{
	global $esynCategory;

	$out = array();

	$categories = $esynCategory->getAllByParent($catId);

	if($categories)
	{
		foreach($categories as $key => $category)
		{
			$out[$key]['id'] = $category['id'];
			$out[$key]['title'] = esynSanitize::html($category['title']);
			$out[$key]['crossed'] = (1 == $category['crossed']) ? true : false;
			$out[$key]['locked'] = (1 == $category['locked']) ? true : false;
			$out[$key]['sub'] = $esynCategory->exists("`parent_id` = '{$category['id']}' AND `status` = 'active'");
		}
	}

	return $out;
}

function getDropMenu($parentId = 0)
{
	global $esynCategory;
	
	static $out;
	
	$categories = $esynCategory->all("`id`, `parent_id`, `title`", "`parent_id` = '{$parentId}' AND `status` = 'active'");
	
	if($categories)
	{
		foreach($categories as $key => $category)
		{    
			$category['title'] = esynSanitize::html($category['title']);
			
			if(!$esynCategory->exists("`parent_id` = '{$category['id']}' AND `status` = 'active'"))
			{
				$out .= "<li rel=\"{$category['id']}\" title=\"{$category['title']}\" id=\"mcdropdown_{$category['id']}\">{$category['title']}</li>";
			}
			else
			{
				$out .= "<li rel=\"{$category['id']}\" title=\"{$category['title']}\" id=\"mcdropdown_{$category['id']}\">{$category['title']}";
				$out .= "<ul>";
				getDropMenu($category['id']);
				$out .= "</ul>";
				$out .= "</li>";
			}
		}  
	}
	
	return $out; 
}

?>
