<?php
//##copyright##

if(!isset($_GET['node']) || preg_match("/\D/", $_GET['node']))
{
	header("HTTP/1.1 404 Not found");
	print("Powered By eSyndicat ");
	die();
}

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");

require_once('.'.DIRECTORY_SEPARATOR.'header.php');

$esynAdmin->factory('Category');
$esynAdmin->loadClass("JSON");

$json = new Services_JSON();

$category_id = (int)$_GET['node'];
$single = (isset($_GET['single']) && '1' == $_GET['single']) ? true : false;

$checked = array();
$disabled = array();

if(isset($_GET['checked']) && !empty($_GET['checked']))
{
	$checked = explode('|', $_GET['checked']);
	$checked = array_unique(array_map("intval", $checked));
}

if(isset($_GET['disabled']) && !empty($_GET['disabled']))
{
	$disabled = array_unique(array_map("intval", $_GET['disabled']));
}

$nodes = array();

$categories = $esynCategory->getAllByParent($category_id);

if($categories)
{
	foreach($categories as $key => $category)
	{
		$leaf = ($esynCategory->getNumSubcategories($category['id']) > 0) ? false : true;

		$nodes[$key]['text'] = esynSanitize::html($category['title']);
		$nodes[$key]['id'] = $category['id'];
		$nodes[$key]['leaf'] = $leaf;
		
		if(!$single)
		{
			$nodes[$key]['checked'] = (in_array($category['id'], $checked)) ? true : false;
		}

		if(!empty($disabled) && in_array($category['id'], $disabled))
		{
			$nodes[$key]['disabled'] = true;
		}

		$nodes[$key]['cls'] = 'folder';
	}
}

echo $json->encode($nodes);
exit;
?>
