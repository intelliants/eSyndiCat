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
