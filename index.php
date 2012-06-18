<?php
//##copyright##

require_once('.'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'header.php');

$eSyndiCat->factory("Category", "Listing");

$category = false;

/** get current category information **/
if (ESYN_MOD_REWRITE)
{
	if (isset($_SERVER['HTTP_X_REWRITE_URL']))
	{
		$_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_REWRITE_URL'];
	}
	
	if(isset($_GET['category']))
	{
		if('0' == $_GET['category'] && isset($_GET['page']))
		{
			$path = '';
		}
		else
		{
			$path = $_GET['category'];
		}
	}
	else
	{
		$path = '';
	}

	if($esynConfig->getConfig('use_html_path') && !empty($path) && (!stristr($_SERVER['REQUEST_URI'], '.html')))
	{
		$_GET['error'] = "404";
		include(ESYN_HOME."error.php");
		exit;
	}
	
	if ($esynCategory->validPath($path))
	{
		$category = $esynCategory->row("*", "`path` = :path AND `status` = 'active'", array('path' => $path));
	}
}
else
{
	if(!isset($_GET['id']) || !ctype_digit($_GET['id']))
	{
		$category = $esynCategory->row("*", "`parent_id` = '-1'");
	}
	else
	{
		$category = $esynCategory->row("*", "`id` = :id AND `status` = 'active'", array('id' => $_GET['id']));
	}

}
unset($_GET['category']);

// by this time category might be active BUT one of it's parent categories might be approval
if ($category)
{
	/** define tab name for this page **/
	$GLOBALS['currentTab'] = 0 == $category['id'] ? 'home' : 'home'.$category['id'];

	
	$eSyndiCat->setTable("flat_structure");
	$parents = $eSyndiCat->onefield("`parent_id`", "`category_id` = :id", array('id' => $category['id']));
	$eSyndiCat->resetTable();
	
	if ($parents)
	{
		// $parents array contains itself also (as a parent to itself) so it must be more than 1 elements
		array_shift($parents);
		$parents = implode("','",$parents);

		if ($esynCategory->exists("`id` IN('".$parents."') AND status <> 'active'"))
		{
			// see below
			$category = false;
		}
	}
}

// no such category OR category is approval
if (empty($category))
{
	$eSyndiCat->factory("Page");

	if ($esynPage->exists("`name` = :name AND `status` = 'active'", array('name' => $path)))
	{
		$_GET['name'] = $path;
		include(ESYN_HOME . 'page.php');
		exit;
	}
	
	$_GET['error'] = "404";
	include(ESYN_HOME."error.php");
	exit;
}

if(-1 != $category['parent_id'])
{
	define("ESYN_REALM", "index_browse");
}
else
{
	define("ESYN_REALM", "index");
}

/** gets current page and defines start position **/
$page = empty($_GET['page']) ? 0 : (int)$_GET['page'];
$page = ($page < 1) ? 1 : $page;
$start = ($page - 1) * $esynConfig->getConfig('num_index_listings');

$id = $category['id'];

$cat_tpl = $category['unique_tpl'] ? $id : '';
$render = "index".$cat_tpl.".tpl";

$rootNoFollow = $esynCategory->one('`no_follow`', "`id` = '0'");
$fields = $join = array();
$where = '';

// Smarty and other View related things
include(ESYN_INCLUDES.'view.inc.php');

if(isset($esynAccountInfo['id']))
{
	$esynSmarty->caching = false;
}
else
{
	// set cache time for this page
	$esynSmarty->cache_lifetime	= 3600;
}

$cache_id = ESYN_LANGUAGE."|".$id."|".$page;

$order = $order_type = false;

/** define links sorting **/
if ($esynConfig->getConfig('visitor_sorting'))
{
	$eSyndiCat->setTable("config");
	$res = $eSyndiCat->one("`multiple_values`", "`name`='listings_sorting'");
	$eSyndiCat->resetTable();	

	$sortings = explode(',', $res);
	
	for($i = 0; $i < count($sortings); $i++)
	{
		$sortings[$i] = trim($sortings[$i], "'");
	}
	
	$esynSmarty->assign('sortings', $sortings);

	if (!empty($_GET['order']))
	{
		$order = in_array($_GET['order'], $sortings) ? $_GET['order'] : 'alphabetic';
		
		setcookie("listings_sorting", $order, 0, '/'.ESYN_DIR);
	}
	else
	{
		$order = !empty($_COOKIE['listings_sorting']) ? $_COOKIE['listings_sorting'] : false;
	}

	// validation
	if (!$order || !ctype_alpha($order))
	{
		$order = $esynConfig->getConfig('listings_sorting');
	}

	$esynConfig->setConfig('listings_sorting', $order);

	if (!empty($_GET['order_type']))
	{
		setcookie("listings_sorting_type", $_GET['order_type'], 0, '/'.ESYN_DIR);

		$order_type = $_GET['order_type'];
	}
	else
	{
		$order_type = !empty($_COOKIE['listings_sorting_type']) ? $_COOKIE['listings_sorting_type'] : false;
	}

	$order_type = $order_type ? preg_replace("/[^a-z]/", "", $order_type) : $esynConfig->getConfig('listings_sorting_type');

	$esynConfig->setConfig('listings_sorting_type', $order_type);

	$cache_id .= "|".$order_type;
}

if ($order)
{
	$cache_id .= "|".$order;
}

// if page cache time elapsed
if (!$esynSmarty->is_cached($render, $cache_id))
{
	$eSyndiCat->factory("Layout");

	$esynSmarty->assign_by_ref('category', $category);
	
	if ('-1' != $category['parent_id'])
	{
		$esynSmarty->assign('description', $category['meta_description']);
		$esynSmarty->assign('keywords',	$category['meta_keywords']);
			
		$title = $esynLayout->getTitle($id, empty($category['page_title']) ? $category['title'] : $category['page_title'], $page);
		$header = $category['title'];
		$breadcrumb	= $esynLayout->printBreadcrumb($category, '', false, $rootNoFollow);
	}
	else
	{
		$esynSmarty->assign('description', $esynConfig->getConfig('site_description'));
		$esynSmarty->assign('keywords',	$esynConfig->getConfig('site_keywords'));
		$category['description'] = $esynConfig->getConfig('site_main_content'); 
		
		$header = $title = $esynConfig->getConfig('site');
		$breadcrumb = '';
	}

	// $title is title for current page
	$esynSmarty->assign('title', $title);

	// $header is page header 
	$esynSmarty->assign('header', $header);

	/** categories box formation **/
	$num_subcategories = $esynConfig->getConfig('subcats_display') ? $esynConfig->getConfig('subcats_display') : 0;

	$categories = $eSyndiCat->mCacher->get("categoriesByParent_".$id, 86400, true);
	
	if (!$categories)
	{
		$categories = $esynCategory->getAllByParent($id, $num_subcategories);
		$eSyndiCat->mCacher->write("categoriesByParent_".$id, $categories);
	}

	$esynSmarty->assign('categories', $categories);

	/** get listings for this category **/
	$listings = $esynListing->getListingsByCategory($id, $start, $esynConfig->getConfig('num_index_listings'), $esynAccountInfo['id'], $sqlFoundRows = true, $sqlCountRows = false,  $fields, $where, $join);

	/** gets number of listings for this category for navigation **/
	
	//TODO think about total listings with filtration by location
	//$total_listings = isset($esynAccountInfo['id']) || $esynConfig->getConfig('show_children_listings') ? $esynListing->foundRows() : $category['num_listings'];
	
	$total_listings = $esynListing->foundRows();

	$esynSmarty->assign('total_listings', $total_listings);
	
	if(!$esynConfig->getConfig('show_children_listings'))
	{
		$p = '';
		
		if($category['id'] != '0')
		{
			$p = $category['path'];
		}
		
		$c = count($listings);
		
		for($i=0; $i < $c; $i++)
		{
			$p2 = $p;
			
			$listings[$i]['path'] = $p2;
			$listings[$i]['category_title'] = $esynCategory->one("title", "`id`='{$listings[$i]['category_id']}'");
			$listings[$i]['category_id'] = $category['id'];
		}
	}	

	$eSyndiCat->startHook("afterGetListingList");

	$esynSmarty->assign('listings', $listings);

	if (ESYN_MOD_REWRITE)
	{
		if($category['id']>0 && $esynConfig->getConfig('use_html_path'))
		{
			$url = ESYN_URL.$category['path'].'_{page}.html';
		}
		elseif($category['parent_id'] == '-1')
		{
			$url = ESYN_URL.'index{page}.html';
		}
		else
		{
			$url = ESYN_URL.$category['path'].'/index{page}.html';
		}		
	}
	else
	{
		$url = ESYN_URL.'index.php?id='.$category['id'].'&page={page}';
	}

	$esynSmarty->assign('url', $url);

	/** get related categories **/
	if ($esynConfig->getConfig('related'))
	{
		$related_categories = $esynCategory->getRelated($id);
		
		$esynSmarty->assign('related_categories', $related_categories);
	}
	
	/** get neighbour categories **/
	// if num_neighbours == 0 that means that user don't want to show neighbour categories
	// if -1 that means that user wants to show all neighbour categories

	if ($category['id'] > 0 && $esynConfig->getConfig('neighbour') && $category['num_neighbours'] != 0)
	{
		if((int)$category['num_neighbours'] == -1)
		{
			// show all
			$category['num_neighbours'] = 0;
		}
		$neighbour_categories = $esynCategory->getNeighbours($id, $category['num_neighbours']);
		
		$esynSmarty->assign('neighbour_categories', $neighbour_categories);
	}

	/** number of listings for current category and subcategories **/
	$esynSmarty->assign('num_listings', $category['num_all_listings']);

	/** number of subcategories for current category **/
	$num_categories = $esynCategory->getNumSubcategories($id);
	$esynSmarty->assign('num_categories', $num_categories);
}

// trick to display RSS in header 
$esynSmarty->assign('view', 'all');

// if unique template does not exist, then reset to default
if (!file_exists($esynSmarty->template_dir.$render))
{
	$render = "index.tpl";	
}

$esynSmarty->display($render, $cache_id);
