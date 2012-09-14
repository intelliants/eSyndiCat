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
