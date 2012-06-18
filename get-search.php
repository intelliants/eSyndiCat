<?php
//##copyright##

if(!isset($_GET['q']) || !isset($_GET['fields']))
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

if(!empty($_GET['q']))
{
	$eSyndiCat->loadClass("JSON");	

	$json = new Services_JSON();

	$fields = explode(',', $_GET['fields']);
	$query = explode(" ", esynSanitize::sql($_GET['q']));
	$limit = (int)$_GET['limit'];

	foreach($fields as $field)
	{
		$field = esynSanitize::sql($field);
		
		$concatFields[] = "`listings`.`{$field}`";
	}

	foreach($query as $key => $word)
	{
		$word = trim($word);
		
		$word = esynSanitize::sql($word);

		if(!empty($word) && strlen($word) >= 4)
		{
			$likes[$key] = "(CONCAT(". implode(",' ',", $concatFields) .") LIKE '%{$word}%')";
		}
	}

	$sql = "SELECT `listings`.`id`, `listings`.`title`, `listings`.`description`, `categories`.`path` FROM `{$eSyndiCat->mPrefix}listings` `listings` ";
	$sql .= "LEFT JOIN `{$eSyndiCat->mPrefix}categories` `categories` ";
	$sql .= "ON `categories`.`id` = `listings`.`category_id` ";
	$sql .= "WHERE ";
	$sql .= implode(' OR ', $likes);
	$sql .= " AND `listings`.`status` = 'active' ";
	$sql .= "LIMIT 0, {$limit}";

	$listings = $eSyndiCat->getAll($sql);

	if(!empty($listings))
	{
		// may be move this processing to client side
		foreach($listings as $key => $listing)
		{
			$listings[$key]['url'] = (!empty($listing['path'])) ? $listing['path'].'/' : '';
			$listings[$key]['url'] .= esynUtil::convertStr(array('string' => $listing['title']));
		}
	}
	else
	{
		$listings = "";
	}

	echo $json->encode($listings);

	exit;
}

?>
