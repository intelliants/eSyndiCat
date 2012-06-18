<?php
//##copyright##

define("ESYN_REALM", "counter");

//slight change
if (empty($_POST['id']) || empty($_POST['type']) || preg_match("/\D/", $_POST['id']) || (int)$_POST['id'] < 1)
{
	header("HTTP/1.1 404 Not found");
	print("Powered by <b><a href=\"http://www.esyndicat.com\" style=\"color:red;text-decoration:underline;\">eSyndicat Free</a></b>");
	exit;
}

include(dirname(__FILE__).DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'header-lite.php');

$id = (int)$_POST['id'];
$ip = $_SERVER['REMOTE_ADDR'];
$type = $_POST['type'];

$eSyndiCat->startHook('clickCountItem');

if(in_array($type, array('categories', 'listings')))
{
	if('categories' == $type)
	{
		$eSyndiCat->factory("Category");
	
		if ($esynCategory->exists("`id` = :id", array('id' => $id)) && !$esynCategory->checkClick($id, $ip))
		{
			$esynCategory->click($id, $ip);
		}
	}

	if('listings' == $type)
	{
		$eSyndiCat->factory("Listing");
	
		if ($esynListing->exists("`id` = :id", array('id' => $id)) && !$esynListing->checkClick($id, $ip))
		{
			$esynListing->click($id, $ip);
		}
	}
}

exit;

?>