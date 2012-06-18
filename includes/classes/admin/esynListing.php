<?php
//##copyright##

/**
 * esynListing 
 * 
 * @uses esynAdmin
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
	 * field	 
	 * 
	 * @var mixed
	 * @access public
	 */
	var $field	= null;

	/**
	 * fields 
	 * 
	 * @var array
	 * @access public
	 */
	var $fields = array();

	/**
	 * fieldLoaded
	 *
	 * flag 
	 * 
	 * @var mixed
	 * @access public
	 */
	var $fieldLoaded = false;

	/**
	 * loadFieldClass 
	 *
	 * Proxy pattern
	 * 
	 * @access public
	 * @return void
	 */
	function loadFieldClass()
	{
		$this->loadClass("ListingField", 'esyn', true);

		$this->field = new esynListingField;
	}

	/**
	 * &field 
	 * 
	 * @access public
	 * @return void
	 */
	function &field()
	{
		if(!$this->fieldLoaded)
		{
			$this->loadFieldClass();
		}

		return $this->field; 
	}

	/**
	 * insert 
	 *
	 * Adds new listing to database and send email
	 * 
	 * @param arr $aListing link information
	 * @access public
	 * @return int the id of the newly inserted listing or 0 otherwise
	 */
	function insert($aListing)
	{
		$retval = 0;
		$notify = !empty($aListing['_notify']);
		// not to generate (_notify='value') in $sql
		unset($aListing['_notify']);

		// Generate and execute the query for adding the link.
		$sql = "INSERT INTO `".$this->mTable."` (";
		foreach($aListing as $key => $value)
		{
			$sql .= !in_array($key, array('multi_crossed'), true) ? " `{$key}`, " : '';
		}
		$sql .= "`date`) VALUES (";
		foreach($aListing as $key => $value)
		{
			$sql .= !in_array($key, array('multi_crossed'), true) ? " '".esynSanitize::sql($value)."', " : '';
		}
		$sql .= "NOW())";
		$this->query($sql);

		// Generate and execute the query for adding the
		// link <-> category connection.
		$aListing['id'] = $this->getInsertId();

		$this->setTable('categories');
		$category = $this->row("`path`","`id`='".$aListing['category_id']."'");
		$this->resetTable();

		if($aListing['status'] == 'active')
		{
//			$this->increaseNumListings($aListing['category_id']);
		}

		if($aListing['email'] && $notify)
		{
			$event 	= array(
				"action" => "listing_admin_add",
				"params" => array(
					"rcpts"=> array($aListing['email']),
					"listing"=> $aListing,
					"path"=> $category['path'],
					"category_id" => $aListing['category_id']
				)
			);
			
			$this->mMailer->dispatcher($event);
		}

		return $aListing['id'];
	}

	/**
	 * getListingsByUrl 
	 *
	 * Returns listings by url
	 * 
	 * @param string $aUrl link url
	 * @param int $aStart starting position
	 * @param int $aLimit number of listings to be returned
	 * @access public
	 * @return arr
	 */
	function getListingsByUrl($aUrl, $aStart = 0, $aLimit = 0)
	{
		$cause = "WHERE t1.`url` = '{$aUrl}' ";
		
		return $this->getByCriteria($aStart, $aLimit, $cause);
	}

	/**
	 * getSearch 
	 *
	 * Returns listings by search
	 * 
	 * @param mixed $aWhat 
	 * @param int $aType 
	 * @param int $aStart 
	 * @param int $aLimit 
	 * @access public
	 * @return arr
	 */
	function getSearch($aWhat, $aType = 1, $aStart = 0, $aLimit = 0)
	{
		$cause = $this->getSearchCriterias($aWhat, $aType);

		return $this->getByCriteria($aStart, $aLimit, $cause, true);
	}

	/**
	 * getByStatus 
	 * 
	 * @param mixed $state 
	 * @param mixed $aCategory 
	 * @param int $aStart 
	 * @param int $aLimit 
	 * @param mixed $aCalc 
	 * @access public
	 * @return void
	 */
	function getByStatus($state,$aCategory, $aStart=0, $aLimit=0, $aCalc = false)
	{
		$aCalc = $aCalc ? "SQL_CALC_FOUND_ROWS" : '';		
		$sql = "SELECT $aCalc *, t1.`title` `title` ";
		$sql .= "FROM `".$this->mTable."` t1 ";
		$sql .= "LEFT JOIN `".$this->mPrefix."listing_categories` t2 ";
		$sql .= "ON t2.`listing_id` = t1.`id` ";
		$sql .= "LEFT JOIN `".$this->mPrefix."categories` t3 ";
		$sql .= "ON t2.`category_id` = t3.`id` ";
		$sql .= "WHERE t1.`status` = 'active' ";
		$sql .= "AND `t1`.`".$state."` = '1' ";
		$sql .= "AND t3.`status` = 'active' ";
		$sql .= $aCategory ? "AND t2.`category_id` <> '".$aCategory."' " : '';

		$sql .= "ORDER BY RAND() ";
		$sql .= $aLimit ? "LIMIT ".$aStart.", ".$aLimit : '';
	}

	/**
	 * getByCriteria
	 *
	 * Returns listings by some value 
	 * 
	 * @param int $aStart starting position 
	 * @param int $aLimit number of listings to be returned
	 * @param string $aCause sql condition on select listings
	 * @param mixed $aCalc 
	 * @access public
	 * @return arr
	 */
	function getByCriteria($aStart = 0, $aLimit = 0, $aCause = '', $aCalc=false)
	{
		$aCalc = $aCalc ? "SQL_CALC_FOUND_ROWS" : '';
		$sql = "SELECT ".$aCalc." t1.*, t44.`id` category_id, ";
		$sql .= "t44.`title` category_title, t44.`path` path ";
		$sql .= "FROM `".$this->mTable."` t1 ";
		$sql .= "LEFT JOIN `".$this->mPrefix."categories` t44 ";
		$sql .= "ON t44.`id` = t1.`category_id` ";
		$sql .= $aCause;
		
		$order = 'title';
		$order_type = 'ASC';
		$sql .= "ORDER BY t1.`".$order."` ".$order_type." ";		

		$sql .= $aLimit ? "LIMIT ".$aStart.", ".$aLimit : '';		

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
		$words = preg_split('/[\s]+/', $aWhat);
		$cnt = count($words);
		if (1 == $aType)
		{
			$sql .= "WHERE (";
			$i = 1;	
			foreach ($words as $word)
			{
				$sql .= "(CONCAT(`t1`.`url`,' ',`t1`.`title`,' ',`t1`.`description`) LIKE '%{$word}%') ";
				if ($i < $cnt)
				{
					$sql .= " OR ";
				}
				$i++;
			}
			$sql .= ") ";
		}
		else if (2 == $aType)
		{
			$sql .= "WHERE ";
			$i = 1;	
			foreach ($words as $word)
			{
				$sql .= "(CONCAT(`t1`.`url`,' ',`t1`.`title`,' ',`t1`.`description`) LIKE '%{$word}%') ";
				if ($i < $cnt)
				{
					$sql .= " AND ";
				}
				$i++;
			}
		}
		else if (3 == $aType)
		{
			$sql .= "WHERE (CONCAT(`t1`.`url`,' ',`t1`.`title`,' ',`t1`.`description`) LIKE '%{$aWhat}%') ";
		}

		return $sql;
	}

	/**
	 * getListingsByPagerank 
	 *
	 * Returns listings by its exact pagerank
	 * 
	 * @param int $aPr pagerank 
	 * @param int $aStart starting position 
	 * @param int $aLimit number of listings to be returned
	 * @access public
	 * @return arr
	 */
	function getListingsByPagerank($aPr, $aStart = 0, $aLimit = 0)
	{
		$cause = "WHERE t1.`pagerank` = '$aPr' ";
		return $this->getByCriteria($aStart, $aLimit, $cause);
	}
	
	/**
	 * getListingsByDates 
	 *
	 * Returns listings by date range
	 * 
	 * @param int $from from date (in MySQL datetime format)
	 * @param int $to todate (in MySQL datetime format)	
	 * @param int $aStart starting position 
	 * @param int $aLimit number of listings to be returned
	 * @access public
	 * @return arr
	 */
	function getListingsByDates($from, $to, $aStart, $aLimit = 0)
	{
		$cause = "WHERE t1.`date` BETWEEN '{$from}' and '{$to}'";

		return $this->getByCriteria($aStart, $aLimit, $cause);
	}	

	/**
	 * getNumListingsByDates 
	 *
	 * Returns number of listings by date range
	 * 
	 * @param int $from from date (in MySQL datetime format)
	 * @param int $to todate (in MySQL datetime format)
	 * @access public
	 * @return int
	 */
	function getNumListingsByDates($from, $to)
	{
		$cause = "WHERE t1.`date` BETWEEN '{$from}' and '{$to}'";

		return $this->getNumListingsBy($cause);	
	}		

	/**
	 * getListingsBy 
	 *
	 * Returns listings by some value
	 * 
	 * @param int $aStart starting position  
	 * @param int $aLimit number of listings to be returned
	 * @param string $aCause sql condition on select listings
	 * @param mixed $aCalc 
	 * @access public
	 * @return arr
	 */
	function getListingsBy($aStart = 0, $aLimit = 0, $aCause = '', $aCalc)
	{
		$aCalc = $aCalc ? "SQL_CALC_FOUND_ROWS" : '';
		$sql = "SELECT $aCalc t1.*, `t1`.`comments_total` `comments`, t41.`id` category_id, ";
		$sql .= "t41.`title` category_title ";
		$sql .= "FROM `".$this->mTable."` t1 ";

		$sql .= "LEFT JOIN `".$this->mPrefix."categories` t41 ";
		$sql .= "ON t1.`category_id` = t41.`id` ";
		$sql .= $aCause;

		$sql .= "ORDER BY `date` DESC ";
		$sql .= $aLimit ? "LIMIT {$aStart}, {$aLimit}" : '';

		$listings &= $this->getAll($sql);

		/** get categories for every link **/
		if (!empty($listings))
		{
			$i = 0;
			$sql = '';
			foreach ($listings as $key => $value)
			{
				if ($i > 0)
				{
					$sql .= 'UNION ALL ';
				}
				$sql .= "(SELECT t2.`listing_id`, t1.`id`, t1.`title` ";
				$sql .= "FROM `".$this->mPrefix."categories` t1 ";
				$sql .= "LEFT JOIN `".$this->mPrefix."listing_categories` t2 ";
				$sql .= "ON t1.`id` = t2.`category_id` ";
				$sql .= "WHERE `listing_id` = {$value['id']} ";
				$sql .= "ORDER BY t1.`title` ) ";
				$i++;
			}
			if ($sql)
			{
				$categories =& $this->getAssoc($sql);
			}

			/** assign categories to listings **/
			if (!empty($categories))
			{
				foreach ($listings as $key => $value)
				{
					$listings[$key]['categories'] =& $categories[$value['id']];
				}
			}
		}

		return $listings;
	}

	/**
	 * getListingsByCategory 
	 *
	 * Returns listings by category
	 * 
	 * @param int $aCategory category id
	 * @param int $aStart starting position 
	 * @param int $aLimit number of listings to be returned
	 * @access public
	 * @return arr
	 */
	function getListingsByCategory($aCategory = 0, $aStart = 0, $aLimit = 0)
	{
		$sql = "SELECT t1.*, `t1`.`comments_total` `comments` ";
		$sql .= "FROM  `".$this->mTable."` t1 ";
		$sql .= "RIGHT JOIN `".$this->mPrefix."listing_categories` t11 ";
		$sql .= "ON t11.`listing_id` = t1.`id` ";
		$sql .= "WHERE t11.`category_id` = '{$aCategory}' ";
		$sql .= "ORDER BY `date` DESC ";
		$sql .= $aLimit ? "LIMIT {$aStart}, {$aLimit}" : '';

		$listings =& $this->getAll($sql);

		/** get categories for every link **/
		if (!empty($listings))
		{
			$i = 0;
			$sql = '';
			foreach ($listings as $key => $value)
			{
				if ($i > 0)
				{
					$sql .= 'UNION ALL ';
				}
				$sql .= "(SELECT t2.`listing_id`, t1.`id`, t1.`title` ";
				$sql .= "FROM `".$this->mPrefix."categories` t1 ";
				$sql .= "LEFT JOIN `".$this->mPrefix."listing_categories` t2 ";
				$sql .= "ON t1.`id` = t2.`category_id` ";
				$sql .= "WHERE `listing_id` = {$value['id']} ";
				$sql .= "ORDER BY t1.`title`) ";
				$i++;
			}
			if ($sql)
			{
				$categories =& $this->getAssoc($sql);
			}

			/** assign categories to listings **/
			if (!empty($categories))
			{
				foreach ($listings as $key => $value)
				{
					$listings[$key]['categories'] =& $categories[$value['id']];
				}
			}
		}

		return $listings;
	}

	/**
	 * getListingById
	 *
	 * Returns link information 
	 * 
	 * @param int $aListing link id
	 * @access public
	 * @return arr
	 */
	function getListingById($aListing)
	{
		$sql = "SELECT t1.*, `t1`.`comments_total` `comments`, ";
		$sql .= "t8.`category_id` category_id ";
		$sql .= "FROM `".$this->mTable."` t1 ";
		$sql .= "LEFT JOIN `".$this->mPrefix."listing_categories` t8 ";
		$sql .= "ON t1.`id` = t8.`listing_id` ";
		$sql .= "WHERE t1.`id` = '{$aListing}' ";

		$link = $this->getRow($sql);

		//** get categories for every link **
		if (!empty($link))
		{
			$sql = "SELECT t1.`id`, t1.`title` ";
			$sql .= "FROM `".$this->mPrefix."categories` t1 ";
			$sql .= "LEFT JOIN `".$this->mPrefix."listing_categories` t2 ";
			$sql .= "ON t1.`id` = t2.`category_id` ";
			$sql .= "WHERE t2.`listing_id` = '{$aListing}' ";
			$sql .= "ORDER BY t1.`title` ";
			
			$categories = $this->getAll($sql);
		}

		$link['categories'] =& $categories;

		return $link;
	}

	/**
	 * getCause 
	 *
	 * Returns cause for a query
	 * 
	 * @param string $aWhat string to search for
	 * @param int $aType search type
	 * @access public
	 * @return string
	 */
	function getCause($aWhat, $aType)
	{
		$sql = '';
		$words = preg_split('/\s+/', $aWhat);
		$cnt = count($words);
		if (1 == $aType)
		{
			$sql .= "WHERE ";
			$i = 1;	
			foreach ($words as $word)
			{
				$sql .= "(CONCAT(`t1`.`url`,' ',`t1`.`title`,' ',`t1`.`description`) LIKE '%{$word}%') ";
				if ($i < $cnt)
				{
					$sql .= " OR ";
				}
				$i++;
			}
		}
		else if (2 == $aType)
		{
			$sql .= "WHERE ";
			$i = 1;	
			foreach ($words as $word)
			{
				$sql .= "(CONCAT(`t1`.`url`,' ',`t1`.`title`,' ',`t1`.`description`) LIKE '%{$word}%') ";
				if ($i < $cnt)
				{
					$sql .= " AND ";
				}
				$i++;
			}
		}
		else if (3 == $aType)
		{
			$sql .= "WHERE (CONCAT(`t1`.`url`,' ',`t1`.`title`,' ',`t1`.`description`) LIKE '%{$aWhat}%') ";
		}

		return $sql;
	}

	/**
	 * getNumListingsBy 
	 *
	 * Returns number of listings by some cause
	 * 
	 * @param string $aCause sql condition on select count
	 * @access public
	 * @return int
	 */
	function getNumListingsBy($aCause = ' WHERE')
	{
		$sql = "SELECT COUNT(t1.`id`) ";
		$sql .= "FROM `".$this->mTable."` t1 ";
		$sql .= "LEFT JOIN `".$this->mPrefix."listing_categories` t2 ";
		$sql .= "ON t1.`id` = t2.`listing_id` ";
		$sql .= $aCause;

		return $this->getOne($sql);
	}	

	/**
	 * getNumByStatus 
	 *
	 * Returns number of listings by status
	 * 
	 * @param string $aStatus listings status
	 * @access public
	 * @return int
	 */
	function getNumByStatus($aStatus = '')
	{
		$cause = $aStatus ? "WHERE t1.`status` = '".$aStatus."'" : '';

		return $this->getNumListingsBy($cause);
	}

	/**
	 * getNumListingsByPagerank 
	 *
	 * Returns number of listings by pagerank
	 * 
	 * @param int $aPr pagerank
	 * @access public
	 * @return int
	 */
	function getNumListingsByPagerank($aPr=0)
	{
		$cause = "WHERE t1.`pagerank` = '{$aPr}'";
		return $this->getNumListingsBy($cause);
	}	

	/**
	 * getNumListingsByHeader 
	 *
	 * Returns number of listings by HTTP header
	 * 
	 * @param int $aHeader 
	 * @access public
	 * @return int
	 */
	function getNumListingsByHeader($aHeader = 200)
	{
		if (($aHeader == '') || ($aHeader == 200))
		{
			$cause = "WHERE `listing_header` <> '200' ";
		}
		else
		{
			$cause = "WHERE `listing_header` = '{$aHeader}' ";
		}

		return $this->getNumListingsBy($cause);
	}

	/**
	 * getListingsByHeader 
	 *
	 * Returns listings by header
	 * 
	 * @param int $aHeader link header
	 * @param int $aStart starting position
	 * @param int $aLimit number of broken listings to be returned
	 * @access public
	 * @return arr
	 */
	function getListingsByHeader($aHeader = 200, $aStart = 0, $aLimit = 0)
	{
		if (($aHeader == '') || ($aHeader == 200))
		{
			$cause = "WHERE t1.`listing_header` <> '200' ";
		}
		else
		{
			$cause = "WHERE t1.`listing_header` = '{$aHeader}'";
		}

		return $this->getByCriteria($aStart, $aLimit, $cause);
	}

	/**
	 * getNumRecip 
	 *
	 * Returns number of listings with correct recipocal listings
	 * 
	 * @param string $aStatus link status
	 * @access public
	 * @return int
	 */
	function getNumRecip($aStatus='')
	{
		$sql = "SELECT COUNT(*) ";
		$sql .= "FROM `".$this->mTable."` ";
		$sql .= "WHERE `recip_valid` = '1' ";
		$sql .= $aStatus ? "AND `status` = '{$aStatus}'" : '';
		
		return $this->getOne($sql);
	}

	/**
	 * getRecip 
	 *
	 * Returns listings that have correct reciprocal listings
	 * 
	 * @param int $aStart starting position
	 * @param int $aLimit number of listings to be returned
	 * @param string $aStatus link status
	 * @param mixed $aCalc 
	 * @access public
	 * @return arr
	 */
	function getRecip($aStart = 0, $aLimit = 0, $aStatus='',$aCalc=false)
	{
		$cause = "WHERE t1.`recip_valid` = '1' ";
		$cause .= $aStatus ? " AND t1.`status`='".$aStatus."'" : '';

		return $this->getByCriteria($aStart, $aLimit, $cause, $aCalc);
	}

	/**
	 * getNumNorecip 
	 *
	 * Returns number of listings with invalid recipocal listings
	 * 
	 * @param string $aStatus link status
	 * @access public
	 * @return int
	 */
	function getNumNorecip($aStatus='')
	{
		$sql = "SELECT COUNT(*) ";
		$sql .= "FROM `".$this->mTable."` ";
		$sql .= "WHERE `recip_valid` = '0' ";
		if(!empty($aStatus)) {
			$sql .=" and status='$aStatus'";
		}
		return $this->getOne($sql);
	}

	/**
	 * getNorecip 
	 *
	 * Returns listings that have no correct reciprocal listings
	 * 
	 * @param int $aStart starting position
	 * @param int $aLimit number of listings to be returned
	 * @param string $aStatus link status
	 * @param mixed $aCalc 
	 * @access public
	 * @return arr
	 */
	function getNorecip($aStart = 0, $aLimit = 0,$aStatus='', $aCalc = false)
	{
		$cause = "WHERE t1.`recip_valid` = '0' ";
		if(!empty($aStatus))
		{
			$cause .=" and t1.`status`='".$aStatus."'";
		}

		return $this->getByCriteria($aStart, $aLimit, $cause, $aCalc);
	}

	/**
	 * getNumListingsByCategory 
	 *
	 * Returns number of listings by category id
	 * 
	 * @param int $aCategory category id
	 * @access public
	 * @return int
	 */
	function getNumListingsByCategory($aCategory)
	{
		$cause = "WHERE `category_id` = '{$aCategory}'";

		return $this->getNumListingsBy($cause);
	}	

	/**
	 * getListingsByStatus 
	 *
	 * Returns listings by their status
	 * 
	 * @param string $aStatus listings status
	 * @param int $aStart starting position
	 * @param int $aLimit number of listings to be returned
	 * @param mixed $calcRows 
	 * @access public
	 * @return arr
	 */
	function getListingsByStatus($aStatus = '', $aStart = 0, $aLimit = 0, $calcRows=false)
	{
		$cause = $aStatus ? "WHERE t1.`status` = '".$aStatus."' " : '';

		return $this->getByCriteria($aStart, $aLimit, $cause,$calcRows);
	}
	
	/**
	 * getListings 
	 *
	 * Returns all listings
	 * 
	 * @param int $aStart starting position
	 * @param int $aLimit number of listings to be returned
	 * @access public
	 * @return arr
	 */
	function getListings($aStart = 0, $aLimit = 0)
	{
		return $this->getByCriteria($aStart, $aLimit, '',false);
	}

	/**
	 * updatePageRank 
	 *
	 * Updates link pagerank
	 * 
	 * @param int $aListing link id
	 * @param int $aPr url pagerank
	 * @access public
	 * @return bool
	 */
	function updatePageRank($aListing, $aPr)
	{
		$sql = "UPDATE `".$this->mTable."` ";
		$sql .= "SET `pagerank` = '{$aPr}' ";
		$sql .= "WHERE `id` = '{$aListing}'";

		return $this->query($sql);
	}		

	/**
	 * moveCrossListing 
	 *
	 * Moves cross listing to other category
	 * 
	 * @param int $aListing listing id
	 * @param int $aCategory category id where listing should be moved
	 * @param int $aCategoryFrom category id where listing is moved from
	 * @access public
	 * @return bool
	 */
	function moveCrossListing($aListing, $aCategory, $aCategoryFrom)
	{
		$this->setTable("listing_categories");
			$deleted = parent::delete("`listing_id`='".$aListing."' AND `category_id` = '".$aCategoryFrom."'");

			// nothing to move
			if(!$deleted)
			{
				return false;
			}
			$this->decreaseNumListings($aCategoryFrom);

			$already = parent::exists("`listing_id`='".$aListing."' AND `category_id`='".$aCategory."'");
			if(!$already)
			{
				parent::insert(array(
					"listing_id"=>$aListing,
					"category_id" => $aCategory
				));				

				$this->increaseNumListings($aCategory);
			}

		$this->resetTable();

		return true;
	}

	/**
	 * move 
	 *
	 * Moves listing to other category
	 * 
	 * @param mixed $listing listing id or listing itself
	 * @param int $aCategory category id where listing should be moved
	 * @param bool $aSendmail sends email in case true
	 * @access public
	 * @return void
	 */
	function move($listing, $aCategory, $notify=false)
	{
		if(!is_array($listing))
		{
			$listing = $this->row("*", "`id`='".$listing."'");
		}

		if(isset($listing['status']) && $listing['status'] == 'active')
		{
			// destination category increase
			$this->increaseNumListings($aCategory);
			// decrease source category
			$this->decreaseNumListings($listing['category_id']);
		}

		parent::update(array("category_id" => $aCategory), "`id`='".$listing['id']."'");
		$listing['category_id'] = $aCategory;

		if(isset($listing['email']) && !empty($listing['email']) && $notify)
		{
			$this->setTable('categories');
				$category = $this->row("`path`","`id`='".$listing['category_id']."'");
			$this->resetTable();

			$action = "listing_move";
			
			$event = array(
				"action" => $action,
				"params" => array(
					"rcpts" => array($listing['email']),
					"listing" => $listing,
					"path" => $category['path'],
					"category_id" => $listing['category_id']
				)
			);

			$this->mMailer->dispatcher($event);
		}

		return true;
	}

	/**
	 * copy 
	 *
	 * Copies link to another category
	 * 
	 * @param int $aListing link id 
	 * @param int $aCategory new category id
	 * @access public
	 * @return bool
	 */
	function copy($aListing, $aCategory = 0)
	{
		$this->setTable("listing_categories");
		$x = parent::insert(array("listing_id"=>$aListing, "category_id"=>$aCategory));
		$this->resetTable();

		$this->increaseNumListings($aCategory);

		return $x;
	}

	/**
	 * deleteCrossListing 
	 *
	 * Removes crosslink
	 * 
	 * @param int $aListing link id
	 * @param int $aCategory category id
	 * @access public
	 * @return bool
	 */
	function deleteCrossListing($aListing, $aCategory)
	{
		$this->setTable("listings");
			$status = parent::one("`status`", "`id`='".$aListing."'");
		$this->resetTable();
		$sql = "DELETE FROM `".$this->mPrefix."listing_categories` ";
		$sql .= "WHERE `listing_id` = '".$aListing."' AND `category_id` = '".$aCategory."'";

		$this->query($sql);

		if($status == 'active')
		{
			$this->decreaseNumListings($aCategory);
		}

		return $this->getAffected();
	}

	/**
	 * updateStatus 
	 * 
	 * @param mixed $aListing 
	 * @param string $aStatus 
	 * @param mixed $aSendmail 
	 * @access public
	 * @return void
	 */
	function updateStatus($aListing, $aStatus = 'active', $aSendmail = false)
	{
		return $this->updateListingStatus($aListing, $aStatus, $aSendmail);
	}

	/**
	 * updateListingStatus 
	 *
	 * Updates status for link
	 * 
	 * @param int $aListing link id
	 * @param string $aStatus link status
	 * @param bool $aSendmail if true send mail
	 * @access public
	 * @return bool
	 */
	function updateListingStatus($aListing, $aStatus = 'active', $aSendmail = false)
	{
		$sql = "UPDATE `".$this->mTable."` SET `status` = '{$aStatus}' ";
		$sql .= "WHERE `id` = '{$aListing}'";

		$cats = $this->one("`category_id`", "`id` = '{$aListing}'");
		$currentStatus = $this->one('`status`', "`id` = '{$aListing}'");

		/** send email in case link email exist and option is enabled **/
		if ($aStatus == 'banned')
		{
			$aSendmail = ($aSendmail || $this->mConfig['listing_reject']) ? true : false;
			$action = 'listing_reject';
		}
		elseif ($aStatus == 'active')
		{
			$aSendmail = ($aSendmail || $this->mConfig['listing_approve']) ? true : false;
			$action = 'listing_approve';
		}
		elseif ($aStatus == 'approval')
		{
			$aSendmail = ($aSendmail || $this->mConfig['listing_disapprove']) ? true : false;
			$action = 'listing_disapprove';
		}

		if($currentStatus != $aStatus)
		{
			if($aStatus == 'active')
			{
				$this->increaseNumListings($cats);
			}
			else
			{
				$this->decreaseNumListings($cats);
			}
		}

		if ($aSendmail && $action)
		{
			$listing = $this->row("*","`id` = '{$aListing}'");
			
			if ($listing['email'])
			{
				$this->setTable("categories");
				$category = $this->row("`path`","`id` = '{$listing['category_id']}'");
				$this->resetTable();

				$event 	= array(
					"action" => $action,
					"params" => array(
						"rcpts"=> array($listing['email']),
						"listing"=> $listing,
						"path"=> $category['path'],
						"category_id" => $listing['category_id']
					)
				);
				
				$this->mMailer->dispatcher($event);
			}
		}

		return $this->query($sql);
	}

	/**
	 * delete 
	 *
	 * Deletes link from database
	 * 
	 * @param string $where 
	 * @param string $reason 
	 * @access public
	 * @return bool
	 */
	function delete($where, $reason=false)
	{
		$aListings = $this->all("*", $where);
		if(!$aListings)
		{
			return 0;
		}

		if(!$this->fields)
		{
			$this->setTable("listing_fields");
				$this->fields = $this->keyvalue("`name`,`type`");
			$this->resetTable();
		}

		$deleted = parent::delete($where);
		$ids = array();
		foreach($aListings as $aListing)
		{
			foreach($this->fields as $n=>$type)
			{
				if($type=='image' || $type == 'storage')
				{
					if(is_file(ESYN_HOME.'uploads'.ESYN_DS.$aListing[$n]))
					{
						unlink(ESYN_HOME.'uploads'.ESYN_DS.$aListing[$n]);
					}
				}
			}

			$totalDeleted = $this->cascadeDelete(
				array(
					"listing_clicks",
				),
				"`listing_id`='".$aListing['id']."'"
			);

			// Send email in case the link email exists
			// and the corresponding email notification option [listing_delete] is enabled
			if($aListing['email'] && $this->mConfig['listing_delete'])
			{
				$this->setTable("categories");
					$category = $this->row("*", "`id`='".$aListing['category_id']."'");
				$this->resetTable();

				$event = array(
					"action" => "listing_delete",
					"params" => array(
						"rcpts"=> array($aListing['email']),
						"listing"=> $aListing,
						"reason"=> $reason,
						"path"=> $category['path'],
						"category_id" => $aListing['category_id']
					)
				);
				
				$this->mMailer->dispatcher($event);
			}

			$ids[] = $aListing['id'];

			if('active' == $aListing['status'])
			{
				$this->decreaseNumListings($aListing['category_id'], 1);
			}
		}

		// work with crossed link. adjust num listings
		$this->setTable("listing_categories");
		$map = parent::keyvalue("`category_id`, count(`category_id`)", "`listing_id` IN('".join("','", $ids)."') GROUP BY `category_id`");
		parent::delete("`listing_id` IN ('".join("','", $ids)."')");
		$this->resetTable();

		if(is_array($map))
		{
			foreach($map as $cat => $count)
			{
				$this->decreaseNumListings($cat, $count);
			}
		}

		return $deleted;
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
	function update($aListing, $where='', $addit=null)
	{
		$newCategory = isset($aListing['category_id']) ? $aListing['category_id'] : false;
		$notify = !empty($aListing['_notify']);
		unset($aListing['_notify']);

		$newStatus = isset($aListing['status']) ? $aListing['status'] : false;
		$x = $this->row("`id`, `status`, `category_id`", isset($aListing['id']) ? "`id`='".$aListing['id']."'" : $where);
		$aListing['id'] = $id = $x['id'];
		$oldStatus = $x['status'];
		$oldCategory = $x['category_id'];
		unset($x);

		$sql = "UPDATE `".$this->mTable."` SET ";
		foreach($aListing as $key => $value)
		{
			$value = esynSanitize::sql($value);
			$sql .= "`".$key."` = '".$value."', ";
		}
		if($addit)
		{
			foreach($addit as $key => $value)
			{
				$sql .= "`".$key."` = ".$value.", ";
			}
		}

		$sql = rtrim($sql, ", ");

		if(!empty($where))
		{
			$where = " WHERE ".$where;
			if($id!==false)
			{
				$where .= " AND `id`='".$id."'";
			}
		}
		elseif($id!==false)
		{
			$where .= " WHERE `id`='".$id."'";
		}

		$sql .= $where;

		$this->query($sql);
		$r = $this->getAffected();

		// two scenarios
		// 1. status changed but not category
		// 2. status not changed but category changed
		if((int)$newCategory == (int)$oldCategory)
		{
			// 1. scenario
			if($newStatus && $newStatus != $oldStatus && in_array("active", array($newStatus, $oldStatus), true))
			{
				if($newStatus == 'active')
				{
					$this->increaseNumListings($oldCategory);
				}
				else
				{
					$this->decreaseNumListings($oldCategory);
				}
			}
		}
		else
		{
			$this->move($aListing, $newCategory, $notify);
		}

		/** send email in case listing email exist and option is enabled **/
		if (isset($aListing['email']) && !empty($aListing['email']) && $notify)
		{
			$this->setTable("categories");
			$category = $this->row("`path`","`id`='".$newCategory."'");
			$this->resetTable();

			$event = array(
				"action" => "listing_modify",
				"params" => array(
					"rcpts"=> array($aListing['email']),
					"listing"=> $aListing,
					"path"=> $category['path'],
					"category_id" => $newCategory
				)
			);
			
			$this->mMailer->dispatcher($event);
		}

		return $r;
	}

	/**
	 * getSponsored 
	 *
	 * Returns sponsored listings
	 * 
	 * @param int $aStart starting position
	 * @param int $aLimit number of listings to be returned
	 * @param string $aStatus link status
	 * @param mixed $aCalc 
	 * @access public
	 * @return arr
	 */
	function getSponsored($aStart =0, $aLimit = 0,$aStatus='', $aCalc=false)
	{
		$aCalc = $aCalc ? "SQL_CALC_FOUND_ROWS" : '';
		$sql = "SELECT $aCalc t2.*, `t2`.`sponsored_start` `start`, `t2`.`comments_total` `comments` ";
		$sql .= "FROM `".$this->mTable."` t2 ";
		$sql .= "LEFT JOIN `".$this->mPrefix."listing_comments` t5 ";
		$sql .= "ON t5.`listing_id` = t2.`id` ";
		$sql .= 'WHERE `t2`.`sponsored` = \'1\' ';
		if(!empty($aStatus))
		{
			$sql .=" and t2.`status`='".$aStatus."' ";
		}
		$sql .= "GROUP BY t2.`id` ";
		$sql .= $aLimit ? "LIMIT {$aStart}, {$aLimit}" : '';

		$listings = $this->getAll($sql);

		//** get categories for every link
		if (!empty($listings))
		{
			$i = 0;
			$sql = '';
			foreach ($listings as $key => $value)
			{
				if ($i > 0)
				{
					$sql .= 'UNION ALL ';
				}
				$sql .= "(SELECT t2.`listing_id`, t1.`id`, t1.`title` ";
				$sql .= "FROM `".$this->mPrefix."categories` t1 ";
				$sql .= "LEFT JOIN `".$this->mPrefix."listing_categories` t2 ";
				$sql .= "ON t1.`id` = t2.`category_id` ";
				$sql .= "WHERE `listing_id` = {$value['id']} ";
				$sql .= "ORDER BY t1.`title`) ";
				$i++;
			}
			if ($sql)
			{
				$categories =& $this->getAssoc($sql);
			}

			//** assign categories to listings
			if (!empty($categories))
			{
				foreach ($listings as $key => $value)
				{
					$listings[$key]['categories'] =& $categories[$value['id']];
				}
			}
		}

		return $listings;
	}

	/**
	 * checkSponsored 
	 *
	 * Checks if the link is not sponsored
	 * 
	 * @param int $aListing link id
	 * @access public
	 * @return bool true if the link is sponsored, false otherwise
	 */
	function checkSponsored($aListing)
	{
        $sql = 'SELECT COUNT(`id`) ';
        $sql .= 'FROM `'.$this->mTable.'` ';
        $sql .= 'WHERE `id` = \''.$aListing.'\' AND `sponsored` = \'1\'';
        return (bool)$this->getOne($sql);
	}

	/**
	 * checkFeatured 
	 *
	 * Check if the link is marked as Featured
	 * 
	 * @param int $aListing link id
	 * @access public
	 * @return bool true if featured, false otherwise
	 */
	function checkFeatured($aListing)
	{
		$sql = 'SELECT COUNT(`id`) ';
		$sql .= 'FROM `'.$this->mTable.'` ';
		$sql .= 'WHERE `featured` = \'1\' AND `id` = \''.$aListing.'\'';
		return (bool)$this->getOne($sql);
	}

	/**
	 * setFeaturedListing 
	 * 
	 * Marks the link as Featured
	 *
	 * @param int $aListing link id
	 * @access public
	 * @return void
	 */
	function setFeaturedListing($aListing)
	{
		$sql = 'UPDATE `'.$this->mTable.'` ';
		$sql .= 'SET `featured` = \'1\', `feature_start` = NOW() ';
		$sql .= 'WHERE `id` = \''.$aListing.'\'';
		$this->query($sql);
	}

	/**
	 * setPartner 
	 *
	 * Mark link as Partner
	 * 
	 * @param int $aListing link id 
	 * @access public
	 * @return void
	 */
	function setPartner($aListing)
	{
		$sql = 'UPDATE `'.$this->mTable.'` ';
		$sql .= 'SET `partner` = \'1\', `partners_start` = NOW() ';
		$sql .= 'WHERE `id` = \''.$aListing.'\'';
		$this->query($sql);
	}

	/**
	 * checkPartner 
	 * 
	 * Check if link is not yet marked as Partner
	 *
	 * @param int $aListing link id
	 * @access public
	 * @return bool true if already is Partner, false otherwise
	 */
	function checkPartner($aListing)
	{
		$sql = 'SELECT COUNT(`id`) ';
		$sql .= 'FROM `'.$this->mTable.'` ';
		$sql .= 'WHERE `partner` = \'1\' AND `id` = \''.$aListing.'\'';
		return (bool)$this->getOne($sql);
	}

	/**
	 * getPartner 
	 *
	 * Returns partner listings
	 * 
	 * @param int $aStart starting position
	 * @param int $aLimit number of listings to be returned
	 * @param string $aStatus listings status
	 * @param mixed $aCalc 
	 * @access public
	 * @return arr
	 */
	function getPartner($aStart =0, $aLimit = 0, $aStatus = '', $aCalc=false)
	{
		$aCalc = $aCalc ? "SQL_CALC_FOUND_ROWS" : '';
		$sql = "SELECT $aCalc t2.*, t2.`partners_start` `start`, `t2`.`comments_total` `comments` ";
		$sql .= "FROM `".$this->mTable."` t2 ";
		$sql .= 'WHERE `t2`.`partner` = \'1\' ';
		$status = ($aStatus == 'all') ? '' : $aStatus;
		$sql .= $status ? "AND `status` = '".$status."'" : '';
		$sql .= $aLimit ? "LIMIT ".$aStart.", ".$aLimit : '';
		
		$listings = $this->getAll($sql);

		/** get categories for every link **/
		if (!empty($listings))
		{
			$i = 0;
			$sql = '';
			foreach ($listings as $key => $value)
			{
				if ($i > 0)
				{
					$sql .= 'UNION ALL ';
				}
				$sql .= "(SELECT t2.`listing_id`, t1.`id`, t1.`title` ";
				$sql .= "FROM `".$this->mPrefix."categories` t1 ";
				$sql .= "LEFT JOIN `".$this->mPrefix."listing_categories` t2 ";
				$sql .= "ON t1.`id` = t2.`category_id` ";
				$sql .= "WHERE `listing_id` = {$value['id']} ";
				$sql .= "ORDER BY t1.`title`) ";
				$i++;
			}
			if ($sql)
			{
				$categories =& $this->getAssoc($sql);
			}

			/** assign categories to listings **/
			if (!empty($categories))
			{
				foreach ($listings as $key => $value)
				{
					$listings[$key]['categories'] =& $categories[$value['id']];
				}
			}
		}

		return $listings;
	}

	/**
	 * getFeatured 
	 * 
	 * Returns partner listings
	 *
	 * @param int $aStart starting position
	 * @param int $aLimit number of listings to be returned
	 * @param string $aStatus listings status
	 * @param mixed $aCalc 
	 * @access public
	 * @return arr
	 */
	function getFeatured($aStart =0, $aLimit = 0, $aStatus = '', $aCalc = false)
	{
		$aCalc = $aCalc ? "SQL_CALC_FOUND_ROWS" : '';
		$sql = "SELECT $aCalc t2.*, t2.`feature_start` `start`, `t2`.`comments_total` `comments` ";
		$sql .= "FROM `".$this->mTable."` t2 ";
		$sql .= 'WHERE `featured` = \'1\' ';
		$status = ($aStatus == 'all') ? '' : $aStatus;
		$sql .= $status ? "AND `status` = '{$status}'" : '';
		$sql .= $aLimit ? "LIMIT {$aStart}, {$aLimit}" : '';

		$listings =& $this->getAll($sql);

		//** get categories for every link
		if (!empty($listings))
		{
			$i = 0;
			$sql = '';
			foreach ($listings as $key => $value)
			{
				if ($i > 0)
				{
					$sql .= 'UNION ALL ';
				}
				$sql .= "(SELECT t2.`listing_id`, t1.`id`, t1.`title` ";
				$sql .= "FROM `".$this->mPrefix."categories` t1 ";
				$sql .= "LEFT JOIN `".$this->mPrefix."listing_categories` t2 ";
				$sql .= "ON t1.`id` = t2.`category_id` ";
				$sql .= "WHERE `listing_id` = {$value['id']} ";
				$sql .= "ORDER BY t1.`title`) ";
				$i++;
			}
			if ($sql)
			{
				$categories =& $this->getAssoc($sql);
			}

			//** assign categories to listings
			if (!empty($categories))
			{
				foreach ($listings as $key => $value)
				{
					$listings[$key]['categories'] =& $categories[$value['id']];
				}
			}
		}

		return $listings;
	}

	/**
	 * getNumBroken 
	 *
	 * Returns number of broken listings
	 * 
	 * @param string $aStatus link status
	 * @access public
	 * @return int
	 */
	function getNumBroken($aStatus='')
	{
		$cause = "WHERE `listing_header` NOT IN('200','301','302')";
		if(!empty($aStatus))
		{
			$cause .=" and `status`='".$aStatus."'";
		}
		return $this->getNumListingsBy($cause);
	}

	/**
	 * getBroken 
	 * 
	 * Returns broken listings list (not 200, 301, 302)
	 *
	 * @param int $aStart starting position
	 * @param int $aLimit number of listings to be returned
	 * @param string $aStatus 
	 * @param mixed $aCalc link status
	 * @access public
	 * @return arr
	 */
	function getBroken($aStart = 0, $aLimit = 0, $aStatus='', $aCalc=false)
	{
		$cause = "WHERE t1.`listing_header` NOT IN('200', '301','302')";
		if(!empty($aStatus))
		{
			$cause .=" and t1.`status`='".$aStatus."'";
		}

		return $this->getByCriteria($aStart, $aLimit, $cause,$aCalc);
	}
	
	/**
	 * setPlan 
	 * 
	 * @param mixed $aListing 
	 * @param mixed $aId 
	 * @access public
	 * @return void
	 */
	function setPlan($aListing, $aId)
	{
		return parent::update(array("plan_id" => $aId), "`id` = :id", array('id' => $aListing), array("sponsored_start"=>"NOW()"));
	}

	/**
	 * resetPlan 
	 * 
	 * @param mixed $aListing 
	 * @access public
	 * @return void
	 */
	function resetPlan($aListing)
	{
		return parent::update(array("plan_id" => "0", "sponsored_start" => "0000-00-00 00:00:00"), "`id` = :id", array('id' => $aListing));
	}

	/**
	 * changePlan 
	 *
	 * Changes plan for the sponsored link
	 * 
	 * @param int $aListing link id
	 * @param int $aPlan plan id
	 * @access public
	 * @return void
	 */
	function changePlan($aListing, $aPlan)
	{
		$this->setTable("plans");
		// Get sponsored plan name
		$plan_name = $this->one("`name`", "`id`='".$aPlan."'");
		$this->resetTable();
		
		// Update link
		$sql = 'UPDATE `'.$this->mPrefix.'listings` ';
		$sql .= 'SET `sponsored` = \'1\', `sponsored_plan_id` = \''.$aPlan.'\' ';
		$sql .= 'WHERE `id` = \''.$aListing.'\'';

		$this->query($sql);
	}

	/**
	 * increaseNumListings 
	 * 
	 * @param mixed $id 
	 * @param int $count 
	 * @access public
	 * @return void
	 */
	function increaseNumListings($id, $count=1)
	{
		$this->factory("Category");
		
		global $esynCategory;

		$esynCategory->adjustNumListings($id, "+".(int)$count);
	}

	/**
	 * decreaseNumListings 
	 * 
	 * @param mixed $id 
	 * @param int $count 
	 * @access public
	 * @return void
	 */
	function decreaseNumListings($id, $count=1)
	{
		$this->factory("Category");

		global $esynCategory;
		
		$esynCategory->adjustNumListings($id, "-".(int)$count);
	}

	/**
	 * adjustClicks 
	 * 
	 * @param mixed $id 
	 * @access public
	 * @return void
	 */
	function adjustClicks($id)
	{
		$this->setTable("listing_clicks");
		$count = $this->one("count(*)", "`listing_id`='".$id."'");
		$this->resetTable();
		
		parent::update(array("clicks"=>$count, "id"=>$id));
	}	
}
