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


/**
 * esynListing 
 * 
 * @uses eSyndiCat
 * @package 
 * @version $id$
 */
class esynListing extends eSyndiCat
{
	/**
	 * mTable 
	 * 
	 * @var string
	 * @access public
	 */
	var $mTable = 'listings';
	
	/**
	 * checkDuplicateListings 
	 * 
	 * Checks if listing already exists in database
	 *
	 * @param string $aUrl listing url
	 * @param int $aType checking by domain name
	 * @access public
	 * @return void
	 */
	function checkDuplicateListings($aUrl, $aType = 'exact')
	{
		$sql = "SELECT `id` ";
		$sql .= "FROM `".$this->mTable."` ";
		$cause = ($aType == 'domain') ? "WHERE `domain` = '{$aUrl}'" : "WHERE `url` = '{$aUrl}'";
		$sql .= ($aType == 'contain') ? "WHERE `url` LIKE '%{$aUrl}%'" : $cause;

		return $this->getOne($sql);
	}

	/**
	 * insert 
	 * 
	 * Adds new listing to database and send email
	 *
	 * @param arr $aListing listing information
	 * @param arr $addit listing information as SQL expressions, not values
	 * @access public
	 * @return int the id of the newly inserted listing or 0 otherwise
	 */
	function insert($aListing, $addit=array())
	{
		$retval = 0;
		$aListing['moved_from'] = '-1';
		
		// Generate and execute the query for adding the listing.
		$sql = "INSERT INTO `".$this->mTable."` (";
		foreach($aListing as $key=>$value)
		{
			$sql .= !in_array($key, array('multi_crossed'), true) ? " `{$key}`, " : '';
		}
		if($addit)
		{
			foreach($addit as $key=>$value)
			{
				$sql .= " `{$key}`, ";
			}
		}
		$sql .= "`date`) VALUES (";
		foreach($aListing as $key=>$value)
		{
			if(!in_array($key, array('multi_crossed'), true))
			{
				$value = esynSanitize::sql($value);

				$sql .= "'{$value}',";
			}
		}
		
		if($addit)
		{
			foreach($addit as $key=>$value)
			{
				$value = esynSanitize::sql($value);
				
				$sql .= " {$value}, ";
			}
		}
		$sql .= "NOW())";
		$this->query($sql);

		// Generate and execute the query for adding the
		// listing <-> category connection.
		$retval = $this->getInsertId();
		$aListing['id']	= $retval;

		$this->setTable('categories');
		$category = $this->row("`path`","`id`='".$aListing['category_id']."'");
		$this->resetTable();

		if (isset($aListing['email']))
		{
			$action = $this->mConfig['auto_approval'] ? "listing_approve" : 'listing_submit';
			
			$event = array(
				"action" => $action,
				"params" => array(
					"rcpts"			=> array($aListing['email']),
					"listing"		=> $aListing,
					"path"			=> $category['path'],
					"category_id"	=> $aListing['category_id']
				)
			);
			
			$this->mMailer->dispatcher($event);
		}

		return $retval;
	}
	
	/**
	 * update 
	 * 
	 * Updates listing information
	 *
	 * @param arr $aListing listing info array
	 * @param string $where 
	 * @param mixed $addit 
	 * @access public
	 * @return bool
	 */
	function update($aListing, $addit=null)
	{
		$category_id = $aListing['category_id'];

		$id = false;
		
		if(isset($aListing['id']))
		{
			$id = (int)$aListing['id'];
		}

		$aListing['status'] = 'approval';

		$where = $this->convertIds('id', $id);

		parent::update($aListing, $where, false, $addit);

		return true;
	}

	/**
	 * getListingById 
	 *
	 * Returns listing information
	 * 
	 * @param int $aListing listing id
	 * @param int $aAccount account id
	 * @access public
	 * @return void
	 */
	function getListingById($aListing, $aAccount = false)
	{
		$sql = "SELECT t1.*, ";
		$sql .= "t2.`path`, t2.`title` `category_title` ";
		$sql .= "FROM `".$this->mTable."` t1 ";
		$sql .= "INNER JOIN `".$this->mPrefix."categories` t2 ";
		$sql .= "ON t1.`category_id` = t2.`id` ";
		$sql .= "WHERE t1.`id` = '{$aListing}' ";
		if(empty($aAccount))
		{
			$sql .= "AND `t2`.`status` = 'active' AND `t1`.`status`='active'";
		}
		else
		{
			$sql .= "AND `t2`.`status` = 'active' AND (`t1`.`status`='active' OR `t1`.`account_id`='{$aAccount}' )";			
		}

		return $this->getRow($sql);
	}

	/**
	 * getFavoriteListingByEditorId 
	 *
	 * Returns listing information
	 * 
	 * @param int $aAccount account id
	 * @param int $aStart 
	 * @param int $aLimit 
	 * @access public
	 * @return arr
	 */
	function getFavoriteListingByEditorId($aAccount, $aStart = 0, $aLimit = 0)
	{
		$sql = "SELECT t1.*, t9.`path`, t9.`title` `category_title`, ";
		$sql .= "IF((`t1`.`date` + INTERVAL 7 DAY < NOW()), '0', '1') `interval`, ";
		$sql .= $aAccount ? "IF (`fav_accounts_set` LIKE '%{$aAccount},%', '1', '0') `favorite` " : "'0' `favorite` ";
		$sql .= $aAccount ? ', IF((`t1`.`account_id` = 0) OR (`t1`.`account_id` = \'0\'), \'0\', \'1\') `account_id_edit` ' : ', \'0\' `account_id_edit` ';
		$sql .= "FROM `".$this->mTable."` t1 ";
		$sql .= "LEFT JOIN `".$this->mPrefix."categories` t9 ";
		$sql .= "ON t1.`category_id` = t9.`id` ";
		$sql .= "WHERE t1.`status` = 'active' ";
		$sql .= "AND t9.`status` = 'active' ";
		$sql .= "AND `fav_accounts_set` LIKE '%{$aAccount},%' ";
		$sql .= $aLimit ? "LIMIT ".$aStart.", ".$aLimit : '';

		return $this->getAll($sql);
	}

	/**
	 * getTop 
	 *
	 * Returns top listings
	 * 
	 * @param int $aStart starting position
	 * @param int $aLimit number of listings to be returned
	 * @param int $aAccount account id
	 * @access public
	 * @return arr
	 */
	function getTop($aStart = 0, $aLimit = 0, $aAccount = '')
	{
		$sql = "SELECT t1.*, t9.`path`, t9.`title` `category_title`, ";
		$sql .= "IF((`t1`.`date` + INTERVAL 7 DAY < NOW()), '0', '1') `interval`, ";
		$sql .= $aAccount ? "IF (`fav_accounts_set` LIKE '%{$aAccount},%', '1', '0') `favorite` " : "'0' `favorite` ";
		$sql .= $aAccount ? ', IF((`t1`.`account_id` = 0) OR (`t1`.`account_id` = \'0\'), \'0\', \'1\') `account_id_edit` ' : ', \'0\' `account_id_edit` ';
		$sql .= "FROM `".$this->mTable."` t1 ";
		$sql .= "LEFT JOIN `".$this->mPrefix."categories` t9 ";
		$sql .= "ON t1.`category_id` = t9.`id` ";
		$sql .= "WHERE t1.`status` = 'active' ";
		$sql .= "AND t9.`status` = 'active' ";
		$sql .= "AND t9.`hidden` = '0' ";
		$sql .= "ORDER BY t1.`rank` DESC, ";
		$sql .= "`t1`.`featured` DESC, ";
		$sql .= "`t1`.`partner` DESC ";
		
		$sql .= $aLimit ? "LIMIT ".$aStart.", ".$aLimit : '';

		return $this->getAll($sql);
	}

	/**
	 * getLatest 
	 *
	 * Returns last added listings
	 * 
	 * @param int $aStart starting position
	 * @param int $aLimit number of listings to be returned
	 * @param int $aAccount account id
	 * @access public
	 * @return arr
	 */
	function getLatest($aStart = 0, $aLimit = 0, $aAccount = '')
	{
		$sql = "SELECT t1.*, t9.`path`, t9.`title` `category_title`, ";
		$sql .= "IF((`t1`.`date` + INTERVAL 7 DAY < NOW()), '0', '1') `interval`, ";
		$sql .= $aAccount ? "IF (`fav_accounts_set` LIKE '%{$aAccount},%', '1', '0') `favorite` " : "'0' `favorite` ";
		$sql .= $aAccount ? ', IF((`t1`.`account_id` = 0) OR (`t1`.`account_id` = \'0\'), \'0\', \'1\') `account_id_edit` ' : ', \'0\' `account_id_edit` ';
		$sql .= "FROM `".$this->mTable."` t1 ";
		$sql .= "LEFT JOIN `".$this->mPrefix."categories` t9 ";
		$sql .= "ON t1.`category_id` = t9.`id` ";
		$sql .= "WHERE t1.`status` = 'active' ";
		$sql .= "AND t9.`status` = 'active' ";
		$sql .= "AND t9.`hidden` = '0' ";
		$sql .= "ORDER BY t1.`date` DESC, ";
		$sql .= "`t1`.`featured` DESC, ";
		$sql .= "`t1`.`partner` DESC ";
		$sql .= $aLimit ? "LIMIT {$aStart}, {$aLimit}" : '';
		
		return $this->getAll($sql);
	}

	/**
	 * getPopular 
	 *
	 * Returns most popular listings
	 * 
	 * @param int $aStart starting position
	 * @param int $aLimit number of listings to be returned
	 * @param int $aAccount account id 
	 * @access public
	 * @return void
	 */
	function getPopular($aStart = 0, $aLimit = 0, $aAccount = '')
	{
		$sql = "SELECT t1.*, t9.`path`, t9.`title` `category_title`, ";
		$sql .= "IF((`t1`.`date` + INTERVAL 7 DAY < NOW()), '0', '1') `interval`, ";
		$sql .= $aAccount ? "IF (`fav_accounts_set` LIKE '%{$aAccount},%', '1', '0') `favorite` " : "'0' `favorite` ";
		$sql .= $aAccount ? ', IF((`t1`.`account_id` = 0) OR (`t1`.`account_id` = \'0\'), \'0\', \'1\') `account_id_edit` ' : ', \'0\' `account_id_edit` ';
		$sql .= "FROM `".$this->mTable."` t1 ";
		$sql .= "LEFT JOIN `".$this->mPrefix."categories` t9 ";
		$sql .= "ON t1.`category_id` = t9.`id` ";
		$sql .= "WHERE t1.`status` = 'active' ";
		// $sql .= "AND t1.`clicks` > 0 "; fixed for ticket #412
		$sql .= "AND t9.`status` = 'active' ";
		$sql .= "AND t9.`hidden` = '0' ";
		$sql .= "ORDER BY `clicks` DESC, ";
		$sql .= "`t1`.`featured` DESC, ";
		$sql .= "`t1`.`partner` DESC ";
		$sql .= $aLimit ? "LIMIT {$aStart}, {$aLimit}" : '';
		
		return $this->getAll($sql);
	}
	
	/**
	 * getRandom 
	 *
	 * Returns $num of random listings
	 * 
	 * @param int $num 
	 * @param string $aAccount account id
	 * @access public
	 * @return arr
	 */
	function getRandom($num=10, $aAccount = '')
	{
		$sql = "SELECT t1.*, t9.`path`, t9.`title` `category_title`, '0' `account_id_edit`, ";
		$sql .= "IF((`t1`.`date` + INTERVAL 7 DAY < NOW()), '0', '1') `interval`, ";
		$sql .= $aAccount ? "IF (`fav_accounts_set` LIKE '%{$aAccount},%', '1', '0') `favorite` " : "'0' `favorite` ";
		$sql .= "FROM `".$this->mTable."` t1 ";
		$sql .= "LEFT JOIN `".$this->mPrefix."categories` t9 ";
		$sql .= "ON t1.`category_id` = t9.`id` ";

		$sql .= "WHERE t9.`status` = 'active' ";
		$sql .= "AND t9.`hidden` = '0' ";
		$sql .= "AND t1.`status` = 'active' ";		

		$all = $this->one("COUNT(*)");

		// avoiding very expensive ORDER BY RAND()
		if ($all > 1000)
		{
			$ids = array();
			$max_id = $this->one("MAX(`id`)");

			for($i = 1; $i <= $num; $i++)
			{
				$ids[] = mt_rand(0, $max_id);
			}
			
			$sql .= "AND t1.`id` IN('".implode("','", $ids)."') ";
		}
		else
		{
			$sql .= "ORDER BY RAND() ";
		}

		$sql .= "LIMIT ".$num;
		
		return $this->getAll($sql);
	}

	/**
	 * getAdvSearchListings 
	 *
	 * Returns listings by advanced search
	 * 
	 * @param mixed $select 
	 * @param mixed $cause 
	 * @param string $sortBy 
	 * @param int $aStart 
	 * @param int $aLimit 
	 * @access public
	 * @return arr
	 */
	function getAdvSearchListings($select, $cause, $sortBy='search_score', $aStart = 0, $aLimit = 0)
	{
		$cause .= "AND `t44`.`status` = 'active' ";

		$a = "SQL_CALC_FOUND_ROWS ".$select;

		$sql = "SELECT ".$a." `t1`.*, ";
		$sql .= "`t44`.`path` `path`, ";
		$sql .= "IF((`t1`.`featured` = '1'), '2', IF((`t1`.`partner` = '1'), '1', '0')) `listing_type` ";
		$sql .= "FROM `".$this->mTable."` `t1` ";
		$sql .= "LEFT JOIN `".$this->mPrefix."categories` `t44` ";
		$sql .= "ON `t44`.`id` = `t1`.`category_id` ";
		$sql .= "WHERE ";
		if(!empty($cause))
		{
			$sql .= $cause. " AND ";
		}
		$sql .= "`t1`.`status` = 'active' AND `t44`.`status` = 'active' ";
		$sql .= "AND t44.`hidden` = '0' ";
		$sql .= " GROUP BY `t1`.`id` ";
		$sql .= "ORDER BY ";
		$sql .= '`listing_type` DESC, ';
		$sql .= $sortBy;
		$sql .= " DESC ";
		$sql .= $aLimit ? "LIMIT ".$aStart.", ".$aLimit."" : '';

		return $this->getAll($sql);
	}

	/**
	 * getNumSearchListings 
	 *
	 * Returns number of listings
	 * 
	 * @param string $aWhat what to search for
	 * @param int $aType type of search
	 * @access public
	 * @return int
	 */
	function getNumSearchListings($aWhat = '', $aType = 1)
	{
		$sql = "SELECT COUNT(t1.`id`) FROM `".$this->mTable."` t1 ";
		$sql .= "LEFT JOIN `".$this->mPrefix."categories` t3 ";
		$sql .= "ON t1.`category_id` = t3.`id` ";
		$sql .= $this->getSearchCriterias($aWhat, $aType);
		$sql .= "AND t3.`status` = 'active' ";
		$sql .= "AND t3.`hidden` = '0' ";

		return $this->getOne($sql);
	}	

	/**
	 * getByStatus 
	 * 
	 * Returns Listings by status
	 *
	 * @param mixed $state 
	 * @param mixed $aCategory 
	 * @param int $aStart 
	 * @param int $aLimit 
	 * @access public
	 * @return arr
	 */
	function getByStatus($state,$aCategory=false, $aStart=0, $aLimit=0)
	{
		$sql = "SELECT t1.*, t1.`title` `title`, `t3`.`path` `path` ";
		$sql .= "FROM `".$this->mTable."` t1 ";
		$sql .= "LEFT JOIN `".$this->mPrefix."categories` t3 ";
		$sql .= "ON t1.`category_id` = t3.`id` ";
		$sql .= "WHERE t1.`status` = 'active' ";
		$sql .= "AND `t1`.`".$state."` = '1' ";
		$sql .= "AND t3.`status` = 'active' ";
		$sql .= "AND t3.`hidden` = '0' ";
		$sql .= false!==$aCategory ? "AND t1.`category_id` <> '{$aCategory}' " : '';
		$sql .= "GROUP BY t1.`id` ";
		$sql .= "ORDER BY RAND() ";
		$sql .= $aLimit ? "LIMIT {$aStart}, {$aLimit}" : '';
		
		return $this->getAll($sql);
	}

	/**
	 * getPartner 
	 * 
	 * Returns partner listings
	 *
	 * @param int $aCategory category id
	 * @param int $aStart starting position
	 * @param int $aLimit number of listings to be returned
	 * @access public
	 * @return arr
	 */
	function getPartner($aCategory = 0, $aStart =0, $aLimit = 0)
	{
		return $this->getByStatus("partner",  $aCategory, $aStart, $aLimit);
	}

	/**
	 * getFeatured 
	 *
	 * Returns featured listings
	 * 
	 * @param int $aCategory category id
	 * @param int $aStart starting position
	 * @param int $aLimit number of listings to be returned
	 * @access public
	 * @return arr
	 */
	function getFeatured($aCategory = 0, $aStart =0, $aLimit = 0)
	{
		return $this->getByStatus("featured",  $aCategory, $aStart, $aLimit);
	}

	/**
	 * getByCriteria 
	 *
	 * Returns listings by some value
	 * 
	 * @param int $aStart starting position 
	 * @param int $aLimit number of listings to be returned
	 * @param string $aCause sql condition on select listings
	 * @param mixed $calcFoundRows 
	 * @access public
	 * @return void
	 */
	function getByCriteria($aStart = 0, $aLimit = 0, $aCause = '', $calcFoundRows = false, $aAccount = false)
	{
		$a = '';
		if ($calcFoundRows)
		{
			$a = "SQL_CALC_FOUND_ROWS";
		}
		$sql = "SELECT ".$a." `t1`.*, ";
		$sql .= "IF((`t1`.`featured` = '1'), '2', IF((`t1`.`partner` = '1'), '1', '0')) `listing_type`, ";
		$sql .= "`t44`.`path` `path`, `t44`.`title` `category_title`, ";
		$sql .= ($aAccount) ? ", IF (`fav_accounts_set` LIKE '%{$aAccount},%', '1', '0') `favorite` " : "'0' `favorite` ";
		$sql .= "FROM `".$this->mTable."` `t1` ";
		$sql .= "LEFT JOIN `".$this->mPrefix."categories` `t44` ";
		$sql .= "ON `t44`.`id` = `t1`.`category_id` ";
		$sql .= $aCause;
		$sql .= " GROUP BY `t1`.`id` ";
		$sql .= "ORDER BY ";
		$sql .= '`listing_type` DESC, ';
		$sql .= "`t1`.`date` DESC ";	
		
		$sql .= $aLimit ? "LIMIT ".$aStart.", ".$aLimit."" : '';

		return $this->getAll($sql);
	}
	
	/**
	 * getThumbnail 
	 *
	 * Returns field name if is set instead thumbnail
	 * 
	 * @param array $aCategory array of category
	 * @param array $aPlan array of plan
	 * @access public
	 * @return string
	 */
	function getThumbnail($aCategory = NULL, $aPlan = NULL)
	{
		$sql = "SELECT `name` ";
		$sql .= "FROM `{$this->mPrefix}listing_fields` `listing_fields` ";
		$sql .= "LEFT JOIN `{$this->mPrefix}field_categories` `field_categories` ";
		$sql .= "ON `field_categories`.`field_id` = `listing_fields`.`id` ";
		
		if(NULL != $aPlan)
		{
			$sql .= "LEFT JOIN `{$this->mPrefix}field_plans` `field_plans` ";
			$sql .= "ON `field_plans`.`field_id` = `listing_fields`.`id` ";
		}
		
		$sql .= "WHERE `type`='image' AND `instead_thumbnail`=1 ";
		$sql .= "AND (`field_categories`.`category_id` = '{$aCategory['id']}' ";

		if(NULL != $aCategory && '-1' != $aCategory['parent_id'])
		{
			$this->setTable("flat_structure");
			$parents = parent::onefield('`parent_id`', "`category_id` = '{$aCategory['id']}' AND `parent_id` <> '{$aCategory['id']}'");
			$this->resetTable();			
			$sql.= " OR ( `listing_fields`.`recursive` = '1' AND `field_categories`.`category_id` IN ('".implode("','", $parents)."'))";
		}
		$sql .= ") ";

		if(NULL != $aPlan)
		{
			$sql .= "AND `field_plans`.`plan_id` = '{$aPlan['id']}' ";
		}

		$sql .= "GROUP BY `listing_fields`.`id` ";
		$sql .= "ORDER BY `order` ASC LIMIT 1";
		
		$instead_thumbnail = $this->getAll($sql);
		return $instead_thumbnail[0]['name'];
	}
	
	/**
	 * getFieldsByPage 
	 *
	 * Returns array of additional fields
	 * 
	 * @param string $aPage page to be displayed on
	 * @param array $aCategory array of category
	 * @param array $aPlan array of plan
	 * @access public
	 * @return arr
	 */
	function getFieldsByPage($aPage = '', $aCategory = NULL, $aPlan = NULL)
	{
		$sql = "SELECT * ";
		$sql .= "FROM `{$this->mPrefix}listing_fields` `listing_fields` ";
		$sql .= "LEFT JOIN `{$this->mPrefix}field_categories` `field_categories` ";
		$sql .= "ON `field_categories`.`field_id` = `listing_fields`.`id` ";
		
		if(NULL != $aPlan)
		{
			$sql .= "LEFT JOIN `{$this->mPrefix}field_plans` `field_plans` ";
			$sql .= "ON `field_plans`.`field_id` = `listing_fields`.`id` ";
		}
		
		$sql .= "WHERE ";
		$sql .= $aPage ? "FIND_IN_SET('{$aPage}', `pages`) > 0 AND " : '';
		$sql .= "`adminonly` = '0' ";
		$sql .= "AND (`field_categories`.`category_id` = '{$aCategory['id']}' ";

		if(NULL != $aCategory && '-1' != $aCategory['parent_id'])
		{
			$this->setTable("flat_structure");
			$parents = parent::onefield('`parent_id`', "`category_id` = '{$aCategory['id']}' AND `parent_id` <> '{$aCategory['id']}'");
			$this->resetTable();			
			$sql.= " OR ( `listing_fields`.`recursive` = '1' AND `field_categories`.`category_id` IN ('".implode("','", $parents)."'))";
		}
		$sql .= ") ";

		if(NULL != $aPlan)
		{
			$sql .= "AND `field_plans`.`plan_id` = '{$aPlan['id']}' ";
		}

		$sql .= "GROUP BY `listing_fields`.`id` ";
		$sql .= "ORDER BY `order` ASC ";

		return $this->getAll($sql);
	}

	/**
	 * getSearchCriterias 
	 *
	 * Returns cause for a query
	 * 
	 * @param string $aWhat string to search for
	 * @param int $aType search type
	 * @access public
	 * @return string
	 */
	function getSearchCriterias($aWhat, $aType)
	{
		$sql = '';
		$words = preg_split('/[\s]+/u', $aWhat);
		$tmp = array();
		if (1 == $aType || 2 == $aType)
		{
			foreach ($words as $word)
			{
				$tmp[] = "(CONCAT(`t1`.`url`,' ',`t1`.`title`,' ',`t1`.`description`) LIKE '%{$word}%')";
			}
			$sql .= 1 == $aType ? 'WHERE ('.implode(" OR ",$tmp).')' : (2 == $aType ? 'WHERE '.implode(" AND ",$tmp) : '');
		}
		else if (3 == $aType)
		{
			$sql .= "WHERE (CONCAT(`t1`.`url`,' ',`t1`.`title`,' ',`t1`.`description`) LIKE '%{$aWhat}%')";
		}
		$sql .= " AND `t1`.`status` = 'active' AND `t44`.`status` = 'active' ";
		$sql .= "AND t44.`hidden` = '0' ";	

		return $sql;
	}

	/**
	 * getListingsByAccountId 
	 *
	 * Returns listings by account id
	 * 
	 * @param int $aAccount account id
	 * @param int $aStart starting position
	 * @param int $aLimit number of listings to be returned
	 * @access public
	 * @return arr
	 */
	function getListingsByAccountId($aAccount, $aStatus = '', $aStart = 0, $aLimit = 0)
	{
		$cause = "WHERE t1.`account_id` = '".$aAccount."' ";
		$cause .= (!empty($aStatus)) ? "AND `t1`.`status` = '{$aStatus}'" : '';

		return $this->getByCriteria($aStart, $aLimit, $cause);
	}

	/**
	 * getNumListingsByAccountId 
	 *
	 * Returns the number of all account listings
	 * 
	 * @param int $aAccount account id
	 * @access public
	 * @return int
	 */
	function getNumListingsByAccountId($aAccount, $aStatus = '')
	{
		$sql = "SELECT COUNT(*) FROM `{$this->mPrefix}listings` ";
		$sql .= "WHERE `account_id` = '{$aAccount}'";
		$sql .= (!empty($aStatus)) ? " AND `status` = '{$aStatus}'" : "";

		return $this->getOne($sql);
	}

	/**
	 * checkClick 
	 *
	 * Checks if a listing was already clicked
	 * 
	 * @param int $aId 
	 * @param string $aIp ip address
	 * @access public
	 * @return int
	 */
	function checkClick($aId, $aIp)
	{
		$sql = "SELECT `id` FROM `".$this->mPrefix."listing_clicks` ";
		$sql .= "WHERE `ip` = '{$aIp}' ";
		$sql .= "AND `listing_id` = '{$aId}' ";
		$sql .= "AND (TO_DAYS(NOW()) - TO_DAYS(`date`)) <= 1 ";

		return $this->getOne($sql);
	}

	/**
	 * click 
	 *
	 * Adds record when listing is clicked
	 * 
	 * @param int $aListing listing id
	 * @param string $aIp ip address
	 * @access public
	 * @return void
	 */
	function click($aListing, $aIp)
	{
		parent::setTable("listing_clicks");
		parent::insert(array('listing_id' => $aListing, 'ip' => $aIp), array("date" => "NOW()"));
		parent::resetTable();

		parent::update(array(), "id = :id", array('id' => $aListing), array("clicks" => "clicks+1"));

		return true;
	}

	/**
	 * getListings 
	 *
	 * Returns top listings
	 * 
	 * @param string $aType cause value
	 * @param int $aStart starting position
	 * @param int $aLimit number of listings to be returned
	 * @access public
	 * @return arr
	 */
	function getListings($aType, $aStart = 0, $aLimit = 0)
	{
		$sql = "SELECT t1.*, COUNT(DISTINCTROW(t2.`id`)) clicks, ";
		$sql .= ($aAccount) ? "IF (t5.account_id = 0, '0', '1') account_id_edit," : "'0' account_id_edit,";
		$sql .= " t4.`path` path ";
		$sql .= "FROM `".$this->mTable."` t1 ";
		$sql .= "LEFT JOIN `{$this->mPrefix}listing_clicks` t2 ";
		$sql .= "ON t1.`id` = t2.`listing_id` ";
		$sql .= "LEFT JOIN `{$this->mPrefix}categories` t4 ";
		$sql .= "ON t1.`category_id` = t4.`id` ";
		$sql .= "WHERE t1.`status` = 'active' ";
		$sql .= "AND t4.`hidden` = '0' ";
		$sql .= "GROUP BY t1.`id` ORDER BY ";
		if ('new' == $aType)
		{
			$sql .= "t1.`date` DESC ";
		}
		elseif ('top' == $aType)
		{
			$sql .= "t1.`rank` DESC ";
		}
		elseif ('popular' == $aType)
		{
			$sql .= "`clicks` DESC ";
		}
		elseif ('random' == $aType)
		{
			$sql .= "RAND() ";
		}
		else
		{
			$sql .= "t1.`{$aType}` DESC ";
		}
		$sql .= $aLimit ? "LIMIT ".$aStart.", ".$aLimit : '';
		
		return $this->getAll($sql);
	}

	/**
	 * getListingsByCategory 
	 *
	 * Returns listings by category
	 * 
	 * @param int $aCategory category id
	 * @param int $aStart starting position
	 * @param int $aLimit number of listings to be returned
	 * @param string $aAccount account id
	 * @param mixed $sqlFoundRows founded rows
	 * @access public
	 * @return array
	 */
	//function getListingsByCategory($aCategory = 0, $aStart = 0, $aLimit = 0, $aAccount = '', $sqlFoundRows = false, $sqlCountRows = false)
	function getListingsByCategory ($aCategory = 0, $aStart = 0, $aLimit = 0, $aAccount = '', $sqlFoundRows = false, $sqlCountRows = false, $aFields = array(), $aWhere = '', $aJoin = array())
	{
		$a = $sqlFoundRows ? "SQL_CALC_FOUND_ROWS " : '';
		$c = $sqlCountRows ? 'COUNT(*) `num`,' : '';

		$sql = 'SELECT '.$a.' DISTINCTROW '.$c.' '; 
		$sql .= !empty($aFields) && is_array($aFields) ? implode(",", $aFields).',' : '';
		$sql .= '`t1`.`id`, `t1`.*, ';
		$sql .= "IF((`t1`.`date` + INTERVAL 7 DAY < NOW()), '0', '1') `interval`,";
		$sql .= "IF((`t1`.`featured` = '1'), '2', IF((`t1`.`partner` = '1'), '1', '0')) `listing_type`, ";
		$sql .= (0 == $aCategory || !$this->mConfig['show_children_listings']) ? '' : '`t11`.`path` `path`, `t11`.`title` `category_title`, ';
		$sql .= $aAccount ? 'IF((`t1`.`account_id` = \'0\'), \'0\', \'1\') `account_id_edit`, ' : '\'0\' `account_id_edit`, ';
		$sql .= ($aAccount) ? "IF (`fav_accounts_set` LIKE '%{$aAccount},%', '1', '0') `favorite` " : "'0' `favorite` ";

		if(0 == $aCategory || !$this->mConfig['show_children_listings'])
		{
			$sql .= 'FROM `'.$this->mPrefix.'listings` `t1` ';
		}
		else
		{
			$sql .= 'FROM `'.$this->mPrefix.'flat_structure` `t10` ';		
			$sql .= 'LEFT JOIN `'.$this->mPrefix.'listings` `t1` ON `t10`.`category_id`=`t1`.`category_id` ';
			$sql .= 'LEFT JOIN `'.$this->mPrefix.'categories` `t11` ON `t11`.`id`=`t1`.`category_id` ';			
		}
		if (is_array($aJoin) && !empty($aJoin))
		{
			foreach ($aJoin as $table => $on)
			{
				$sql .= 'LEFT JOIN `'.$this->mPrefix.$table.'` `'.$table.'` ON '.$on;
			}
		}
		$sql .= 'LEFT JOIN `'.$this->mPrefix.'listing_categories` `t2` ON `t1`.`id`= `t2`.`listing_id` ';
		$sql .= 'WHERE (`t2`.`category_id` = \''.$aCategory.'\' ';
		$sql .= (0 == $aCategory || !$this->mConfig['show_children_listings']) ? '' : 'OR `t10`.`parent_id` = \''.$aCategory.'\' ';
		$sql .= 'OR `t1`.`category_id` = \''.$aCategory.'\') ';
		$sql .= $aAccount ? 'AND (`t1`.`status` = \'active\' OR `t1`.`account_id`='.$aAccount.') ' : 'AND `t1`.`status` = \'active\' ';
		$sql .= !empty($aWhere) ? 'AND '.$aWhere.' ' : '';
        $sql .= 'ORDER BY ';
		$sql .= '`listing_type` DESC, ';
        
		// Default order
        $order = 'alphabetic' == $this->mConfig['listings_sorting'] ? 'title' : $this->mConfig['listings_sorting'];
        $order_type = 'ascending' == $this->mConfig['listings_sorting_type'] ? 'ASC' : 'DESC';
        $sql .= '`t1`.`'.$order.'` '.$order_type.' ';
        $sql .= $aLimit ? 'LIMIT '.$aStart.', '.$aLimit : '';
        
        return $this->getAll($sql);
	}

	function getNumListingsByCategory($aCategory = 0, $aAccount = '')
	{
		$row = $this->getListingsByCategory($aCategory, 0, false, $aAccount, false, true);

		return $row['num'];
	}

	/**
	 * getFieldsForSearch 
	 *
	 * Returns array of additional searchable fields
	 * 
	 * @access public
	 * @return arr
	 */
	function getFieldsForSearch()
	{
		$sql = "SELECT * ";
		$sql .= "FROM `".$this->mPrefix."listing_fields` ";
		$sql .= "WHERE ";
		$sql .= "`adminonly` = '0' AND searchable <> '0'";
		$sql .= "ORDER BY `order` ASC ";

		$temp = array();

		$fields = $this->getAll($sql);

		if (!empty($fields))
		{
			foreach($fields as $f)
			{
				$temp[$f['name']] = $f;
			}
		}

		return $temp;
	}

	/**
	 * @param array $aFields
	 * @param array $aOldListing old listing data, needed for image fields when edit
	 */
	function processFields($aFields, $aOldListing = false)
	{
		global $esynI18N;
		$listing = $msg = array();
		$error = false;
		$aFields = is_array($aFields) ? $aFields : array();

		foreach($aFields as $key=>$value)
		{
			$field_name = $value['name'];
			$field_value = isset($_POST[$field_name]) ? $_POST[$field_name] : '';

			// Check the UTF-8 is well formed
			if(is_string($field_value))
			{
				if ( !utf8_is_valid($field_value))
				{
					// Strip out bad sequences - replace with ? character
					$field_value = utf8_bad_replace($field_value);
					trigger_error("Bad UTF-8 detected (replacing with '?')", E_USER_NOTICE);
				}
			}

			switch ($value['type'])
			{
				case 'text':
					if ($value['required'] && empty($field_value))
					{
						$error = true;
						$err_mes = str_replace('{field}', $esynI18N['field_'.$field_name], $esynI18N['field_is_empty']);
						$msg[] = $err_mes;
					}

					$listing[$field_name] = htmlspecialchars($field_value);
					break;

				case 'textarea':
					list($minLength, $maxLenght) = explode(',', $this->mConfig['description_limit']);

					if ($value['required'] && empty($field_value))
					{
						$error = true;
						$err_mes = str_replace('{field}', $esynI18N['field_'.$field_name], $esynI18N['field_is_empty']);
						$msg[] = $err_mes;
					}

					$fieldlen = utf8_strlen($field_value);

					/** check for minimum chars **/
					if ('' != $minLength)
					{
						if ($fieldlen < $minLength)
						{
							$error = true;

							$tmp = str_replace('{field}', $esynI18N['field_'.$field_name], $esynI18N['error_min_textarea']);
							$tmp = str_replace("{num}", $minLength, $tmp);

							$msg[] = $tmp;
						}
					}

					/** check for max chars **/
					if ('' != $maxLenght)
					{
						if ($fieldlen > $maxLenght)
						{
							$error = true;

							$tmp = str_replace('{field}', $esynI18N['field_'.$field_name], $esynI18N['error_max_textarea']);
							$tmp = str_replace("{num}", $maxLenght, $tmp);
							
							$msg[] = $tmp;
						}
					}

					if(1 == $value['editor'])
					{
						require_once(ESYN_INCLUDES.'safehtml/safehtml.php');

						$safehtml = new safehtml();

						$listing[$field_name] = $safehtml->parse($field_value);
					}
					else
					{
						$listing[$field_name] = htmlspecialchars($field_value);
					}
					break;

				case 'combo':
				case 'radio':
					$listing[$field_name] = $field_value;
					break;

				case 'checkbox':
					if(empty($field_value) && $value['required'])
					{
						$error = true;

						$err_mes = str_replace('{field}', $esynI18N['field_'.$field_name], $esynI18N['field_is_empty']);

						$msg[] = $err_mes;
					}
					else
					{
						if (is_array($field_value))
						{
							$field_value = join(",", $field_value);
							$field_value = trim($field_value, ',');
						}
					}

					$listing[$field_name] = $field_value;
					break;

				case 'storage':
					if ($value['required'] && $_FILES[$field_name]['error'])
					{
						$error = true;

						$err_mes = str_replace('{field}', $esynI18N['field_'.$field_name], $esynI18N['field_is_empty']);
						
						$msg[] = $err_mes;
					}
					elseif (!$_FILES[$field_name]['error'])
					{
						$ext	= utf8_substr($_FILES[$field_name]['name'], -3);
						$token	= esynUtil::getNewToken();
						$file_name = $cid."-".$token.".".$ext;
						if (!is_writable(ESYN_HOME.'uploads'.ESYN_DS))
						{
							$error = true;
							$msg[] = $esynI18N['error_directory_readonly'];
						}
						else
						{
							if (esynUtil::upload($field_name, ESYN_HOME.'uploads'.ESYN_DS.$file_name))
							{
								$listing[$field_name] = $file_name;
							}
							else
							{
								$error = true;
								$msg[] = $esynI18N['error_file_upload'];
							}
						}
					}
					break;

				case 'pictures':
					$picture_names = array();
					
					$picture_names = explode(',', $original_listing[$field_name]);

					if(isset($_FILES[$field_name]['tmp_name']) && !empty($_FILES[$field_name]['tmp_name']))
					{
						foreach($_FILES[$field_name]['tmp_name'] as $key => $tmp_name)
						{
							if ((bool)$value['required'] && (bool)$_FILES[$field_name]['error'][$key])
							{
								$error = true;
								$err_mes = str_replace('{field}', $esynI18N['field_'.$field_name], $esynI18N['field_is_empty']);
								$msg[] = $err_mes;
							}
							else
							{
								if (@is_uploaded_file($_FILES[$field_name]['tmp_name'][$key]))
								{
									$ext = strtolower(utf8_substr($_FILES[$field_name]['name'][$key], -3));

									// if jpeg
									if ($ext == 'peg')
									{
										$ext = 'jpg';
									}

									if (!array_key_exists($_FILES[$field_name]['type'][$key], $imgtypes) || !in_array($ext, $imgtypes, true) || !getimagesize($_FILES[$field_name]['tmp_name'][$key]))
									{
										$error = true;

										$a = implode(",",array_unique($imgtypes));

										$err_msg = str_replace("{types}", $a, $esynI18N['wrong_image_type']);
										$err_msg = str_replace("{name}", $field_name, $err_msg);

										$msg[] = $err_msg;
									}
									else
									{
										$eSyndiCat->loadClass("Image");

										$token = esynUtil::getNewToken();

										$file_name = $value['file_prefix'].$cid."-".$token.".".$ext;

										$picture_names[] = $file_name;

										$file = array();
										
										foreach ($_FILES[$field_name] as $key1 => $tmp_name)
										{
											$file[$key1] = $_FILES[$field_name][$key1][$key];
										}

										if($value['thumb_width'] > 0 || $value['thumb_height'] > 0)
										{
											$fname = ESYN_HOME . 'uploads' . ESYN_DS . 'small_' . $file_name;

											$image = new esynImage();

											$image->processImage($file, $fname, $value['thumb_width'], $value['thumb_height'], $value['resize_mode']);

										}

										if($value['image_width'] > 0 || $value['image_height'] > 0)
										{
											$fname = ESYN_HOME.'uploads'.ESYN_DS.$file_name;
										
											$image = new esynImage();

											$image->processImage($file, $fname, $value['image_width'], $value['image_height'], $value['resize_mode']);
										}
										else
										{
											$fname = ESYN_HOME.'uploads'.ESYN_DS.$file_name;
										
											@move_uploaded_file($_FILES[$field_name]['tmp_name'][$key], $fname);
										}
									}
								}
							}
						}

						if(!empty($picture_names))
						{
							$listing[$field_name] = implode(',', $picture_names);
						}
					}
					break;

				case 'image':
					if ((bool)$value['required'] && (bool)$_FILES[$field_name]['error'])
					{
						$error = true;
						$err_mes = str_replace('{field}', $esynI18N['field_'.$field_name], $esynI18N['field_is_empty']);
						$msg[] = $err_mes;
					}
					else
					{
						if (is_uploaded_file($_FILES[$field_name]['tmp_name']))
						{
							$ext = strtolower(utf8_substr($_FILES[$field_name]['name'], -3));

							// if jpeg
							if ($ext == 'peg')
							{
								$ext = 'jpg';
							}

							if (!array_key_exists($_FILES[$field_name]['type'], $imgtypes) || !in_array($ext, $imgtypes, true) || !getimagesize($_FILES[$field_name]['tmp_name']))
							{
								$error = true;
								
								$a = join(",",array_unique($imgtypes));
								
								$err_msg = str_replace("{types}", $a, $esynI18N['wrong_image_type']);
								$err_msg = str_replace("{name}", $field_name, $err_msg);

								$msg[] = $err_msg;
							}

							if(is_file(ESYN_HOME.'uploads'.ESYN_DS.$original_listing[$field_name]))
							{
								unlink(ESYN_HOME.'uploads'.ESYN_DS.$original_listing[$field_name]);
							}

							if(is_file(ESYN_HOME.'uploads'.ESYN_DS.'small_'.$original_listing[$field_name]))
							{
								unlink(ESYN_HOME.'uploads'.ESYN_DS.'small_'.$original_listing[$field_name]);
							}

							$eSyndiCat->loadClass("Image");

							$token = esynUtil::getNewToken();

							$file_name = $value['file_prefix'].$cid."-".$token.".".$ext;

							$listing[$field_name] = $file_name;

							if($value['thumb_width'] > 0 || $value['thumb_height'] > 0)
							{
								$fname = ESYN_HOME.'uploads'.ESYN_DS.'small_'.$file_name;

								$image = new esynImage();

								$image->processImage($_FILES[$field_name], $fname, $value['thumb_width'], $value['thumb_height'], $value['resize_mode']);

							}
							
							if($value['image_width'] > 0 || $value['image_height'] > 0)
							{
								$fname = ESYN_HOME.'uploads'.ESYN_DS.$file_name;

								$image = new esynImage();

								$image->processImage($_FILES[$field_name], $fname, $value['image_width'], $value['image_height'], $value['resize_mode']);
							}
							else
							{
								$fname = ESYN_HOME.'uploads'.ESYN_DS.$file_name;
								
								@move_uploaded_file($_FILES[$field_name]['tmp_name'], $fname);
							}
						}
					}
					break;

				default:
					$listing[$field_name] = htmlspecialchars($field_value);
					break;
			}
		}
		return array($listing, $error, $msg);
	}
}
