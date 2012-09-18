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


define("ESYN_REALM", "listings");

esynUtil::checkAccess();

$esynAdmin->factory("Listing", "Category", "Plan");

$error = false;

/*
 * ACTIONS
 */
/*
 * AJAX
 */
if(isset($_GET['action']))
{
	$esynAdmin->loadClass("JSON");
	
	$json = new Services_JSON();
	
	$out = array('data' => '', 'total' => 0);

	if('get' == $_GET['action'])
	{
		$start = (int)$_GET['start'];
		$limit = (int)$_GET['limit'];

		$sort = $_GET['sort'];
		$dir = in_array($_GET['dir'], array('ASC', 'DESC')) ? $_GET['dir'] : 'ASC';

		if(!empty($sort) && !empty($dir))
		{
			if('account_id' == $sort)
			{
				$order = " ORDER BY `accounts`.`username` {$dir} ";
			}
			elseif ('parents' == $sort)
			{
				$order = " ORDER BY `categories`.`title` {$dir} ";
			}
			else
			{
				$order = " ORDER BY `{$sort}` {$dir} ";
			}
		}

		if(isset($_GET['what']) && !empty($_GET['what']))
		{
			$what = esynSanitize::sql($_GET['what']);
			$type = (isset($_GET['type']) && in_array($_GET['type'], array('any', 'all', 'exact'))) ? $_GET['type'] : 'any';

			$words = preg_split('/[\s]+/', $what);

			if (ctype_digit($what))
			{
				$where[] = "`listings`.`id` = '{$what}' ";
			}
			else
			{
				if('any' == $type || 'all' == $type)
				{
					foreach ($words as $word)
					{
						if(isset($_GET['from']) && 'quick' == $_GET['from'])
						{
							$tmp[] = "(CONCAT(`listings`.`url`,' ',`listings`.`title`,' ',`listings`.`description`) LIKE '%{$word}%') ";
						}
						else
						{
							$tmp[] = "(CONCAT(`listings`.`url`,' ',`listings`.`title`,' ',`listings`.`description`) LIKE '%{$word}%') ";
						}
					}
					$where[] = ('any' == $type) ? ' ('.implode(" OR ",$tmp).')' : ('all' == $type) ? implode(" AND ", $tmp) : '';
				}
				elseif('exact' == $type)
				{
					if(isset($_GET['from']) && 'quick' == $_GET['from'])
					{
						$where[] = "(CONCAT(`listings`.`url`,' ',`listings`.`title`,' ',`listings`.`description`) LIKE '%{$what}%') ";
					}
					else
					{
						$where[] = "(CONCAT(`listings`.`url`,' ',`listings`.`title`,' ',`listings`.`description`) LIKE '%{$what}%') ";
					}
				}
			}
		}

		if(isset($_GET['status']) && 'all' != $_GET['status'] && !empty($_GET['status']))
		{
			$where[] = "`listings`.`status` = '".esynSanitize::sql($_GET['status'])."'";
		}

		if(isset($_GET['type']) && !empty($_GET['type']))
		{
			if('featured' == $_GET['type'])
			{
				$where[] = "`listings`.`featured` = '1'";
			}
			elseif('partner' == $_GET['type'])
			{
				$where[] = "`listings`.`partner` = '1'";
			}
			elseif('regular' == $_GET['type'])
			{
				$where[] = "`listings`.`partner` = '0' AND `listings`.`featured` = '0'";
			}
		}

		if(isset($_GET['state']) && !empty($_GET['state']))
		{
			if('destvalid' == $_GET['state'])
			{
				$where[] = "`listings`.`listing_header` IN('200', '301', '302')";
			}
			elseif('destbroken' == $_GET['state'])
			{
				$where[] = "`listings`.`listing_header` NOT IN('200', '301', '302')";
			}
			elseif('recipvalid' == $_GET['state'])
			{
				$where[] = "`listings`.`recip_valid` = '1'";
			}
			elseif('recipbroken' == $_GET['state'])
			{
				$where[] = "`listings`.`recip_valid` = '0'";
			}
		}

		if(isset($_GET['account']) && !empty($_GET['account']))
		{
			$account = (int)$_GET['account'];

			$where[] = "`listings`.`account_id` = '{$account}'";
		}
		
		$sql = "SELECT `listings`.*, `listings`.`id` `edit`, `accounts`.`username`, `categories`.`id` `category_id`, `categories`.`path`, `categories`.`title` `category` ";
		//MOD: Display payment ID
		//$sql .= ",`transactions`.`order_number` `payment_id`, `transactions`.`status` `payment_status` ";
		$sql .= "FROM `{$esynAdmin->mPrefix}listings` `listings` ";
		$sql .= "LEFT JOIN `{$esynAdmin->mPrefix}accounts` `accounts` ";
		$sql .= "ON `listings`.`account_id` = `accounts`.`id` ";
		$sql .= "LEFT JOIN `{$esynAdmin->mPrefix}categories` `categories` ";
		$sql .= "ON `listings`.`category_id` = `categories`.`id` ";
//		$sql .= "LEFT JOIN `{$esynAdmin->mPrefix}transactions` `transactions` ";
//		$sql .= "ON `listings`.`id` = `transactions`.`item_id` ";

		if(!empty($where))
		{
			$sql .= "WHERE ";
			$sql .= join(' AND ', $where);
		}

		//MOD: Display payment ID
		//$sql .= " AND `transactions`.`item` = 'listings'";

		$sql .= $order;

		$sql .= "LIMIT {$start}, {$limit}";

		$out['data'] = $esynListing->getAll($sql);

		if(!empty($out['data']))
		{
			$out['data'] = esynSanitize::applyFn($out['data'], "html", array('title'));
			$out['data'] = esynSanitize::applyFn($out['data'], "striptags", array('description'));

			foreach($out['data'] as $key => $listing)
			{
				$out['data'][$key]['listing_details'] = '';

				esynView::getBreadcrumb($listing['category_id'], $categories_chain, true);

				if(!empty($categories_chain))
				{
					$parents = array();
					$categories_chain = array_reverse($categories_chain);
					
					if(count($categories_chain) > 1)
					{
						unset($categories_chain[0]);
					}

					foreach($categories_chain as $chain)
					{
						$parents[] = '<a href="controller.php?file=browse&id=' . $chain['id'] . '">' . $chain['title'] . '</a>';
					}
					
					$out['data'][$key]['parents'] = implode('&nbsp;/&nbsp;', $parents);
				}

				if($esynConfig->getConfig('pagerank'))
				{
					$out['data'][$key]['listing_details'] .= '<b>' . $esynI18N['pagerank'] . '</b>&nbsp;:&nbsp;' . $listing['pagerank'] . '<br />';
				}

				$out['data'][$key]['listing_details'] .= '<b>' . $esynI18N['clicks'] . '</b>&nbsp;:&nbsp;' . $listing['clicks'] . '<br />';

				if($listing['featured'])
				{
					$out['data'][$key]['listing_details'] .= '<b>' . $esynI18N['featured_since'] . '</b>&nbsp;:&nbsp;' . esynUtil::dateFormat($listing['featured_start']) . '<br />';
				}

				if($listing['partner'])
				{
					$out['data'][$key]['listing_details'] .= '<b>' . $esynI18N['partner_since'] . '</b>&nbsp;:&nbsp;' . esynUtil::dateFormat($listing['partner_start']) . '<br />';
				}

				$out['data'][$key]['listing_details'] .= '<b>' . $esynI18N['submitted'] . '</b>&nbsp;:&nbsp;' . esynUtil::dateFormat($listing['date']) . '<br />';
				$out['data'][$key]['listing_details'] .= '<b>' . $esynI18N['description'] . '</b>&nbsp;:&nbsp;' . $out['data'][$key]['description'] . '<br /l>';

				unset($categories_chain);
			}
		}

		if(!empty($where))
		{
			$where = join(" AND ", $where);
			$where = str_replace("`listings`.", "", $where);

			$total = $esynListing->one("COUNT(*)", $where);
		}
		else
		{
			$total = $esynListing->one("COUNT(*)");
		}

		$out['total'] = $total;
	}

	if('getaccounts' == $_GET['action'])
	{
		$query = isset($_GET['query']) ? esynSanitize::sql(trim($_GET['query'])) : '';

		$esynAdmin->setTable("accounts");
		$out['data'] = $esynAdmin->all("`id`, `username`", "`username` LIKE '{$query}%'");
		$out['total'] = $esynAdmin->one("COUNT(*)", "`username` LIKE '{$query}%'");
		$esynAdmin->resetTable();
	}

	if(empty($out['data']))
	{
		$out['data'] = '';
	}

	echo $json->encode($out);
	exit;
}

if(isset($_POST['action']))
{
	$esynAdmin->loadClass("JSON");
	
	$json = new Services_JSON();

	$out = array('msg' => 'Unknow error', 'error' => false);

	if('update' == $_POST['action'])
	{
		$field = $_POST['field'];
		$value = $_POST['value'];

		if(empty($field) || empty($_POST['ids']))
		{
			$out['error'] = true;
			$out['msg'] = 'Wrong params';
		}
		else
		{
			$out['error'] = false;
		}

		if(!$out['error'])
		{
			if(is_array($_POST['ids']))
			{
				foreach($_POST['ids'] as $id)
				{
					$ids[] = (int)$id;
				}

				$where = "`id` IN ('".join("','", $ids)."')";
			}
			else
			{
				$id = (int)$_POST['ids'];

				$where = "`id` = '{$id}'";
			}

			if('status' == $field)
			{
				foreach($_POST['ids'] as $id)
				{
					$esynListing->updateStatus((int)$id, $value);
				}

				$esynAdmin->mCacher->clearAll('categories');
			}
			else
			{
				$esynAdmin->setTable('listings');
				$esynAdmin->update(array($field => $value), $where);
				$esynAdmin->resetTable();
			}

			$out['msg'] = $esynI18N['changes_saved'];
		}
	}
	
	if('remove' == $_POST['action'])
	{
		if(empty($_POST['ids']))
		{
			$out['error'] = true;
			$out['msg'] = 'Wrong params';
		}
		else
		{
			$out['error'] = false;
		}

		if(!$out['error'])
		{
			if(is_array($_POST['ids']))
			{
				foreach($_POST['ids'] as $id)
				{
					$ids[] = (int)$id;
				}

				$where = "`id` IN ('".join("','", $ids)."')";
			}
			else
			{
				$id = (int)$_POST['ids'];

				$where = "`id` = '{$id}'";
			}

			$reason = isset($_POST['reason']) && !empty($_POST['reason']) ? $_POST['reason'] : '';

			$esynListing->delete($where, $reason);

			$esynCategory->adjustNumListings(0);

			$out['msg'] = $esynI18N['changes_saved'];
		}
	}

	if('move' == $_POST['action'])
	{
		$listings = array_map("intval", $_POST['ids']);
		$category = (int)$_POST['category'];
		
		$count = count($listings);
		$crosslink = false;
		$sendEmail = ($esynConfig->getConfig('listing_move')) ? true : false;
		
		$esynAdmin->setTable("listing_categories");
		
		foreach($listings as $value)
		{
			// Don't allow moving
			if($esynAdmin->exists("`category_id` = '{$category}' AND `listing_id` = '{$value}'"))
			{
				$crosslink = true;
				continue;
			}

			$esynListing->move($value, $category, $sendEmail);
		}
		
		$esynAdmin->resetTable();

		$esynCategory->adjustNumListings($category);

		if($crosslink && $count == 1)
		{
			$out['msg'] = $esynI18N['cannot_move_due_to_crosslink'];
		}
		else
		{
			$out['msg'] = ($count > 1) ? $esynI18N['listings_moved'] : $esynI18N['listing_moved'];	
		}
	}

	if('copy' == $_POST['action'])
	{
		$listings = array_map("intval", $_POST['ids']);
		$category = (int)$_POST['category'];

		$esynAdmin->setTable("listings");
		
		foreach($listings as $value)
		{
			$link = $esynAdmin->row("*", "`id` = '{$value}'");	

			unset($link['id']);

			$link['category_id'] = $category;
			
			$esynAdmin->insert($link);
		}
		
		$esynAdmin->resetTable();

		$esynCategory->adjustNumListings($category);
		
		$out['msg'] = (count($listings) > 1) ? $esynI18N['listings_copied'] : $esynI18N['listing_copied'];
	}

	if('update_pagerank' == $_POST['action'])
	{
		$listings = array_map("intval", $_POST['ids']);

		$esynAdmin->setTable("listings");
		foreach($listings as $value)
		{
			$pr = esynUtil::getPageRank($esynListing->one("url", "`id` = '{$value}'"));	

			$esynAdmin->update(array("pagerank" => $pr), "`id` = '{$value}'");
		}
		$esynAdmin->resetTable();

		$out['msg'] = $esynI18N['done'];
	}

	if('check_broken' == $_POST['action'])
	{
		$listings = array_map("intval", $_POST['ids']);
		
		$esynAdmin->setTable("listings");		
		foreach($listings as $value)
		{
			$listing_header = esynUtil::getListingHeader($esynListing->one("url", "`id` = '{$value}'"));	

			$esynAdmin->update(array("listing_header" => $listing_header), "`id` = '{$value}'");
		}
		$esynAdmin->resetTable();

		$out['msg'] = $esynI18N['done'];
	}

	if('recip_recheck' == $_POST['action'])
	{
		$listings = array_map("intval", $_POST['ids']);
		
		$recipText = $esynConfig->getConfig('reciprocal_text');
		
		$esynAdmin->setTable("listings");
		foreach($listings as $value)
		{
			$recipValid = esynValidator::hasUrl($esynListing->one("reciprocal", "`id` = '{$value}'"), $recipText);

			$data = array("recip_valid" => (int)$recipValid, "id" => $value);
			$addit = array();
			
			if($esynConfig->getConfig("recip_featured") && $recipValid)
			{
				$data['featured'] = "1";
				$addit['feature_start'] = "NOW()";
			}
			
			$esynAdmin->update($data, "`id` = '{$value}'", array(), $addit);
		}
		$esynAdmin->resetTable();

		$out['msg'] = $esynI18N['done'];
	}

	if ('unbroken' == $_POST['action'])
	{
		$where = $esynAdmin->convertIds('id', $_POST['ids']);

		$esynAdmin->setTable("listings");
		$esynAdmin->update(array('listing_header' => 200), $where);
		$esynAdmin->resetTable();

		$out['msg'] = $esynI18N['done'];
	}

	echo $json->encode($out);
	exit;
}
/*
 * ACTIONS
 */

$gTitle = $esynI18N['manage'].' '.$esynI18N['listings'];

$gNoBc = false;

$gTitle = $esynI18N['manage_listings'];

$gBc[0]['title'] = $esynI18N['manage_listings'];
$gBc[0]['url'] = 'controller.php?file=listings';

$actions[] = array("url" => "controller.php?file=suggest-listing&amp;id=0",	"icon" => "create_listing.png", "label"	=> $esynI18N['create_listing']);

require_once(ESYN_ADMIN_HOME.'view.php');

$esynSmarty->display('listings.tpl');
