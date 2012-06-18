<?php
//##copyright##

define("ESYN_REALM", "redirect");

if (empty($_GET['id']) || $_GET['id']{0} == '0' || preg_match("/\D/", $_GET['id']))
{
	$_GET['error'] = "404";
	include("./error.php");
	die();
}

// requires common header file
require_once('.'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'header.php');

$id	= (int)$_GET['id'];

$eSyndiCat->factory("Listing");

$listing_url = $esynListing->one("`url`", "`id`='{$id}'");

if (empty($listing_url))
{
	$_GET['error'] = "404";
	include(ESYN_HOME."error.php");
}
else
{
	header('Location: '.$listing_url);
}
exit;