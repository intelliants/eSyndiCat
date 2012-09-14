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


require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'header-lite.php');
require_once(ESYN_INCLUDES."util.php");

$from	= isset($_GET['from']) && !empty($_GET['from']) ? $_GET['from'] : NULL;
$limit	= isset($_GET['limit']) && !empty($_GET['limit']) ? (int)$_GET['limit'] : 10;
$out	= '';

if(NULL == $from)
{
	header("HTTP/1.1 404 Not found");
	die("404 Not found. Powered by eSyndicat");
}

$out .= '<?xml version="1.0" encoding="utf-8"?>';
$out .= '<rss version="2.0">';
$out .= '<channel>';

$out .= '<image>';
$out .= '<url>' . ESYN_URL . 'templates/' . $esynConfig->getConfig('tmpl') . '/img/feed-esyndicat.png</url>';
$out .= '<title>eSyndiCat Directory v' . ESYN_VERSION . '</title>';
$out .= '<link>' . ESYN_URL . '</link>';
$out .= '</image>';

if((is_array($from) && in_array('category', $from)) || ('category' == $from))
{
	$eSyndiCat->factory("Category", "Listing", "Layout");

	require_once(ESYN_CLASSES.'esynUtf8.php');

	esynUtf8::loadUTF8Core();
	esynUtf8::loadUTF8Util('utf8_to_ascii');

	$category_id = isset($_GET['id']) && !empty($_GET['id']) ? (int)$_GET['id'] : 0;

	$category = $esynCategory->row("*", "`id` = '{$category_id}'");
	
	$out .= '<title>' . esynSanitize::html($category['title']) . '</title>';
    $out .= '<description>' . esynSanitize::html(strip_tags($category['description'])) . '</description>';
    $out .= '<link>';
	
	$out .= $esynLayout->printCategoryUrl(array('cat' => $category));

    $out .= '</link>';

    // Get link for the selected category
    $listings = $esynListing->getListingsByCategory($category_id, $limit, false, false, 0);

	if(!empty($listings))
	{
		foreach ($listings as $key => $value)
		{
			$item['title'] = $value['title'];

			if(empty($category['path']) && empty($value['path']))
			{
				$path = '';
			}
			else
			{
				$path = (!empty($category['path'])) ? $category['path'].'/' : $value['path'].'/';
			}
			
			if ($esynConfig->getConfig('mod_rewrite'))
			{
				$value['title'] = utf8_to_ascii($value['title']);
				$value['title'] = preg_replace('/[^A-Za-z0-9]+/u', '-', $value['title']);
				$value['title'] = preg_replace('/\-+/', '-', $value['title']);
				$value['title'] = trim($value['title'], '-');
		
				$item['link'] = ESYN_URL . $path . $value['title'] . '-l' . $value['id'] . '.html';
			}
			else
			{
				$item['link'] = ESYN_URL . 'view-listing.php?id=' . $value['id'];
			}

			$item['description'] = truncateText($value['description'], $esynConfig->getConfig('description_num_chars'), '...', '...');
			$item['date'] = $value['date'];

			$out .= create_rss_item($item);
		}
	}
}

if((is_array($from) && in_array('new', $from)) || ('new' == $from))
{
	$eSyndiCat->factory("Listing");

	require_once(ESYN_CLASSES.'esynUtf8.php');

	esynUtf8::loadUTF8Core();
	esynUtf8::loadUTF8Util('utf8_to_ascii');

	$out .= '<title>' . $esynI18N['new_listings'] . '</title>';
    $out .= '<description>' . $esynI18N['newly_added_listings'] . '</description>';
    $out .= '<link>' . ESYN_URL;
    $out .= $esynConfig->getConfig('mod_rewrite') ? 'new-listings.html' : 'new-listings.php';
    $out .= '</link>';

    $listings = $esynListing->getLatest($start = 0, $limit);

	if(!empty($listings))
	{
		foreach ($listings as $key => $value)
		{
			$item['title'] = $value['title'];

			if(empty($category['path']) && empty($value['path']))
			{
				$path = '';
			}
			else
			{
				$path = (!empty($category['path'])) ? $category['path'].'/' : $value['path'].'/';
			}
			
			if ($esynConfig->getConfig('mod_rewrite'))
			{
				$value['title'] = utf8_to_ascii($value['title']);
				$value['title'] = preg_replace('/[^A-Za-z0-9]+/u', '-', $value['title']);
				$value['title'] = preg_replace('/\-+/', '-', $value['title']);
				$value['title'] = trim($value['title'], '-');
		
				$item['link'] = ESYN_URL . $path . $value['title'] . '-l' . $value['id'] . '.html';
			}
			else
			{
				$item['link'] = ESYN_URL . 'view-listing.php?id=' . $value['id'];
			}

			$item['description'] = truncateText($value['description'], $esynConfig->getConfig('description_num_chars'), '...', '...');
			$item['date'] = $value['date'];

			$out .= create_rss_item($item);
		}
	}
}

if((is_array($from) && in_array('popular', $from)) || ('popular' == $from))
{
	$eSyndiCat->factory("Listing");

	require_once(ESYN_CLASSES.'esynUtf8.php');

	esynUtf8::loadUTF8Core();
	esynUtf8::loadUTF8Util('utf8_to_ascii');
	
	$out .= '<title>' . $esynI18N['popular_listings'] . '</title>';
    $out .= '<description>' . $esynI18N['most_popular_listings'] . '</description>';
    $out .= '<link>' . ESYN_URL;
    $out .= $esynConfig->getConfig('mod_rewrite') ? 'popular-listings.html' : 'popular-listings.php';
    $out .= '</link>';

    $listings = $esynListing->getPopular($start = 0, $limit);

	if(!empty($listings))
	{
		foreach ($listings as $key => $value)
		{
			$item['title'] = $value['title'];

			if(empty($category['path']) && empty($value['path']))
			{
				$path = '';
			}
			else
			{
				$path = (!empty($category['path'])) ? $category['path'].'/' : $value['path'].'/';
			}
			
			if ($esynConfig->getConfig('mod_rewrite'))
			{
				$value['title'] = utf8_to_ascii($value['title']);
				$value['title'] = preg_replace('/[^A-Za-z0-9]+/u', '-', $value['title']);
				$value['title'] = preg_replace('/\-+/', '-', $value['title']);
				$value['title'] = trim($value['title'], '-');
		
				$item['link'] = ESYN_URL . $path . $value['title'] . '-l' . $value['id'] . '.html';
			}
			else
			{
				$item['link'] = ESYN_URL . 'view-listing.php?id=' . $value['id'];
			}

			$item['description'] = truncateText($value['description'], $esynConfig->getConfig('description_num_chars'), '...', '...');
			$item['date'] = $value['date'];

			$out .= create_rss_item($item);
		}
	}
}

if((is_array($from) && in_array('top', $from)) || ('top' == $from))
{
	$eSyndiCat->factory("Listing");

	require_once(ESYN_CLASSES.'esynUtf8.php');

	esynUtf8::loadUTF8Core();
	esynUtf8::loadUTF8Util('utf8_to_ascii');
	$item['description'] = truncateText($value['description'], $esynConfig->getConfig('description_num_chars'), '', '...', true);
	$out .= '<title>' . $esynI18N['top_listings'] . '</title>';
    $out .= '<description>' . $esynI18N['top_listings'] . '</description>';
    $out .= '<link>' . ESYN_URL;
    $out .= $esynConfig->getConfig('mod_rewrite') ? 'top-listings.html' : 'top-listings.php';
    $out .= '</link>';

    $listings = $esynListing->getTop($start = 0, $limit);

	if(!empty($listings))
	{
		foreach ($listings as $key => $value)
		{
			$item['title'] = $value['title'];

			if(empty($category['path']) && empty($value['path']))
			{
				$path = '';
			}
			else
			{
				$path = (!empty($category['path'])) ? $category['path'].'/' : $value['path'].'/';
			}
			
			if ($esynConfig->getConfig('mod_rewrite'))
			{
				$value['title'] = utf8_to_ascii($value['title']);
				$value['title'] = preg_replace('/[^A-Za-z0-9]+/u', '-', $value['title']);
				$value['title'] = preg_replace('/\-+/', '-', $value['title']);
				$value['title'] = trim($value['title'], '-');
		
				$item['link'] = ESYN_URL . $path . $value['title'] . '-l' . $value['id'] . '.html';
			}
			else
			{
				$item['link'] = ESYN_URL . 'view-listing.php?id=' . $value['id'];
			}

			$item['description'] = truncateText($value['description'], $esynConfig->getConfig('description_num_chars'), '...', '...');
			$item['date'] = $value['date'];

			$out .= create_rss_item($item);
		}
	}
}

$eSyndiCat->startHook("feed");

function create_rss_item($item)
{
	$out = '';

	$out .= '<item>';
	
	$out .= '<title>' . esynSanitize::html($item['title']) . '</title>';
	$out .= '<link>' . $item['link'] . '</link>';
	$out .= '<description>' . esynSanitize::html($item['description']) . '</description>';
	$out .= '<pubDate> '. date("D, d M Y H:m:s T", strtotime($item['date'])) . '</pubDate>';
	
	$out .= '</item>';
			
	return $out;
}

function truncateText($string, $limit, $break = '...', $pad = '...', $strict = true)
{
    // If the $string is shorter than the $limit, return the original source
    if (strlen($string) <= $limit)
    {
        return $string;
    }
    
    // If the string MUST be shorter than the $limit set.
    // Otherwise shorten to the first $break after the $limit
    if ($strict)
    {
        $string = substr($string, 0, $limit);
        
        if (($breakpoint = strrpos($string, $break)) !== false)
        {
            $string = substr($string, 0, $breakpoint).$pad;
        }
    }
    else
    {
        // If $break is present between $limit and the end of the string
        if (($breakpoint = strpos($string, $break, $limit)) !== false)
        {
            if ($breakpoint < strlen($string) - 1)
            {
                $string = substr($string, 0, $breakpoint).$pad;
            }
        }
    }
    
    return $string;
}



$out .= '</channel>';
$out .=  '</rss>';

header('Content-Type: text/xml');

echo $out;
// to prevent auto append files
die();
