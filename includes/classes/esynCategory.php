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
 * esynCategory 
 * 
 * @uses eSyndiCat
 * @package 
 * @version $id$
 */
class esynCategory extends eSyndiCat
{
	/**
	 * mTable 
	 * 
	 * @var string
	 * @access public
	 */
	var $mTable = 'categories';

	/**
	 * validPath
	 *
	 * Validates a path (NOTE: empty string is treated as valid)
	 * 
	 * @param mixed $aPath 
	 * @access public
	 * @return bool
	 */
	function validPath($aPath)
	{
		return (bool)preg_match("/^[a-z\/0-9._-]*$/i", $aPath);
	}

	/**
	 * getAllByParent 
	 * 
	 * Returns categories by parent category
	 *
	 * @param int $aCategory category id
	 * @param int $aSubcategories number of subcategories
	 * @param bool $aNoCross if tru show crosslistings
	 * @access public
	 * @return void
	 */
	function getAllByParent($aCategory = 0, $aSubcategories = 0, $aNoCross = TRUE)
	{
		/** get categories **/
		$sql = "(SELECT t1.*, '0' `crossed`, t21.`num_cols`, ";
		$sql .= "t1.`title` `category_title` ";
		$sql .= "FROM `".$this->mTable."` t1 ";

		$sql .= "LEFT JOIN `".$this->mPrefix."flat_structure` t2 ";
		$sql .= "ON t1.`id` = t2.`parent_id` ";

		$sql .= "LEFT JOIN `".$this->mTable."` t21 ";
		$sql .= "ON t1.`parent_id` = t21.`id` ";
		$sql .= "WHERE t1.`parent_id` = '".$aCategory."' ";
		$sql .= "AND t1.`status` = 'active' ";
		$sql .= "AND t1.`hidden` = '0' ";

		$sql .= "GROUP BY t1.`id` ";
		$sql .= "ORDER BY t1.`".$this->mConfig['categories_order']."` LIMIT 0, 10000) ";

		if ($aNoCross)
		{
			$sql .= "UNION ALL ";
			$sql .= "(SELECT t1.*, '1' `crossed`, t21.`num_cols`, ";
			$sql .= "IF((`t2`.`category_title` <> ''), t2.`category_title`, t1.`title`) `category_title` ";
			$sql .= "FROM `".$this->mTable."` t1 ";

			$sql .= "RIGHT JOIN `".$this->mPrefix."crossed` t2 ";
			$sql .= "ON t1.`id` = t2.`crossed_id` ";

			$sql .= "LEFT JOIN `".$this->mPrefix."flat_structure` t3 ";
			$sql .= "ON t1.`id` = t3.`parent_id` ";

			$sql .= "LEFT JOIN `".$this->mTable."` t21 ";
			$sql .= "ON t1.`parent_id` = t21.`id` ";
			$sql .= "WHERE t2.`category_id` = '".$aCategory."' ";
			$sql .= "AND t1.`status` = 'active' ";
			$sql .= "AND t1.`hidden` = '0' ";
			$sql .= "GROUP BY t2.`id` ";
			$sql .= "ORDER BY t1.`title` LIMIT 0, 10000) ";
		}
		$order = ($this->mConfig['categories_order'] == 'title') ? 'category_title' : 'order';
		$sql .= " ORDER BY `".$order."`";

		$categories = $this->getAll($sql);

		/** get subcategories **/
		if (!empty($categories))
		{
			$i = 0;
			$sql = '';
			if (isset($this->mConfig['num_cols_suffix']))
			{
				$aSubcategories++;
			}

			foreach ($categories as $key => $value)
			{
				if (!$value['crossed'])
				{
					if ($i > 0)
					{
						$sql .= 'UNION ALL ';
					}
					$sql .= "(SELECT `parent_id`,`id`,`title`, `path`,`no_follow` ";
					$sql .= "FROM `".$this->mTable."`";
					$sql .= "WHERE `parent_id` = ".$value['id'];
					$sql .= " AND `status` = 'active' ";
					$sql .= " AND `hidden` = '0' ";
					$sql .= "ORDER BY `".$this->mConfig['categories_order']."` ";
					$sql .= "LIMIT ".$aSubcategories.") ";

					$i++;
				}
			}
			if ($sql)
			{
				$subcategories = $this->getAssoc($sql);
			}
			/** assign subcategories to categories **/
			if (!empty($subcategories))
			{
				foreach ($categories as $key => $value)
				{
					if (!$value['crossed'])
					{
						$categories[$key]['subcategories'] =& $subcategories[$value['id']];
					}
				}
			}
		}

		return $categories;
	}

	/**
	 * addRelation 
	 * 
	 * Adds records to table containing flat structure of tree
	 *
	 * @param int $aParent parent category id
	 * @param int $aId category id
	 * @access public
	 * @return bool
	 */
	function addRelation($aParent = 0, $aId = 0)
	{
		$this->setTable("flat_structure");
		$x = parent::insert(array("parent_id"=>$aParent, "category_id"=>$aId));
		$this->resetTable();

		return $x;
	}

	/**
	 * buildRelation
	 *
	 * Builds relation records by a category id
	 * 
	 * @param int $aId category id
	 * @access public
	 * @return void
	 */
	function buildRelation($aId = 0)
	{
		$top = $aId;
		// protect against recursive hell
		$step = 0;
		while($top > -1 && $step < 50)
		{
			$step++;
			$this->addRelation($top, $aId);
			$par_cat = $this->row("parent_id","id='".$top."'");
			$top = $par_cat['parent_id'];
		}
		
		if ($step > 49)
		{
			return false;
		}
		return true;
	}

	/**
	 * insert 
	 *
	 * Adds new category to database
	 * 
	 * @param arr $aCategory category information
	 * @access public
	 * @return int the id of the newly added category
	 */
	function insert($aCategory)
	{
		$parent	= $aCategory['parent_id'];
		$parent_category = $this->row("level","id='".$aCategory['parent_id']."'");
		$aCategory['level'] = $parent_category['level'] + 1;

		$aCategory['status'] = 'approval';

		$event 	= array(
			"action" => "suggest_category",
			"params" => array(
				"category"	=> $aCategory,
				"path" => ''
			)
		);

		$this->mMailer->dispatcher($event);

		return parent::insert($aCategory);
	}

	/**
	 * getRelated
	 *
	 * Returns related categories 
	 * 
	 * @param int $aCategory category id
	 * @access public
	 * @return void
	 */
	function getRelated($aCategory)
	{
		$sql = "SELECT t2.*, t2.`title` `category_title` ";
		$sql .= "FROM `".$this->mPrefix."related` t1 ";
		$sql .= "LEFT JOIN `".$this->mTable."` t2 ";
		$sql .= "ON t1.`related_id` = t2.`id` ";
		$sql .= "LEFT JOIN `".$this->mPrefix."flat_structure` t3 ";
		$sql .= "ON t2.`id` = t3.`parent_id` ";
		$sql .= "LEFT JOIN `".$this->mPrefix."listing_categories` t4 ";
		$sql .= "ON t3.`category_id` = t4.`category_id` ";
		$sql .= "WHERE t1.`category_id` = '".$aCategory."' ";
		$sql .= "AND t2.`status` = 'active' ";
		$sql .= "GROUP BY t1.`id` ";
		$sql .= "ORDER BY t2.`".$this->mConfig['categories_order']."`";

		return $this->getAll($sql);
	}

	/**
	 * getNeighbours 
	 * 
	 * Returns neighbour categories
	 *
	 * @param int $aCategory category id
	 * @param int $aLimit number of categories to be returned
	 * @access public
	 * @return arr
	 */
	function getNeighbours($aCategory, $aLimit = 0)
	{
		$sql = "SELECT t2.*, t2.`title` `category_title` ";
		$sql .= "FROM `".$this->mTable."` t1 ";
		$sql .= "RIGHT JOIN `".$this->mTable."` t2 ";
		$sql .= "ON t1.`parent_id` = t2.`parent_id` ";
		$sql .= "LEFT JOIN `".$this->mPrefix."flat_structure` t3 ";
		$sql .= "ON t2.`id` = t3.`parent_id` ";
		$sql .= "LEFT JOIN `".$this->mPrefix."listing_categories` t4 ";
		$sql .= "ON t3.`category_id` = t4.`category_id` ";
		$sql .= "WHERE t1.`id` <> t2.`id` ";
		$sql .= "AND t1.`id` = '".$aCategory."' ";
		$sql .= "AND t2.`status` = 'active'";
		$sql .= "GROUP BY t2.`id` ";
		$sql .= "ORDER BY t2.`".$this->mConfig['categories_order']."`";
		$sql .= $aLimit ? " LIMIT 0, {$aLimit}" : '';

		return $this->getAll($sql);
	}

	/**
	 * getNumSubcategories 
	 *
	 * Returns number of all child categories
	 * 
	 * @param str $aId parent category id
	 * @access public
	 * @return int
	 */
	function getNumSubcategories($aId = 0)
	{
		$sql = "SELECT COUNT(DISTINCT t1.`category_id`) ";
		$sql .= "FROM `".$this->mPrefix."flat_structure` t1 ";
		$sql .= "LEFT JOIN `".$this->mTable."` t2 ";
		$sql .= "ON t1.`category_id` = t2.`id` ";
		$sql .= "WHERE t1.`parent_id` = '".$aId."' ";
		$sql .= "AND t2.`status` = 'active' ";

		$num = $this->getOne($sql);
		if ($num && $aId != 0)
		{
			// as flat-structure contains aId as its parent (both category_id and parent_id equals aId)
			// we need only SUB categories
			$num = (int)$num-1;
		}

		return $num;
	}

	/**
	 * getNumCategories 
	 *
	 * Returns number of categories by status
	 * 
	 * @param string $aStatus categories status
	 * @access public
	 * @return int
	 */
	function getNumCategories($aStatus = '')
	{
		$sql = "SELECT COUNT(`id`) ";
		$sql .= "FROM `".$this->mTable."` ";
		$sql .= "WHERE `id` > 0 ";
		$sql .= $aStatus ? "AND `status` = '{$aStatus}' " : '';

		return $this->getOne($sql);
	}

	/**
	 * getNumCategoriesByStatus 
	 *
	 * Returns number of all categories by status
	 * 
	 * @param str $aStatus category status
	 * @access public
	 * @return int
	 */
	function getNumCategoriesByStatus($aStatus)
	{
		$sql = "SELECT COUNT(`id`) ";
		$sql .= "FROM `".$this->mTable."` ";
		$sql .= "WHERE `status` = '{$aStatus}' " ;
		$sql .= "AND `parent_id` <> '-1' ";

		return $this->getOne($sql);
	}

	/**
	 * getPath 
	 *
	 * Returns path
	 * 
	 * @param mixed $parentPath 
	 * @param mixed $childPath 
	 * @access public
	 * @return void
	 */
	function getPath($parentPath, $childPath)
	{
		return !empty($parentPath) ? $parentPath.'/'.$childPath : $childPath;
	}

	/**
	 * adjustNumListings 
	 * 
	 * This method used to recalculate num_link and num_all_listings fields
	 * if only two arguments passed, then $str_num will be appliable for both fields
	 *
	 * @param int $id category id
	 * @param string $str_num (optional) Update string (if specified, then there will be no calculations, just updation)
	 * @param string $str_all_num (optional) Same as previous but for num_all_listings fields
	 * @access public
	 * @return bool succcess or fail
	 */
	function adjustNumListings($id=0, $str_num='', $str_all_num='')
	{
		if(empty($str_num) && empty($str_all_num))
		{
			$row = $this->row("`num_listings`,`num_all_listings`", "`id`='".$id."'");

			$old_value = $row['num_listings'];
			$old_value_for_all = $row['num_all_listings'];

			$this->setTable("listings");
			$new_value = $this->one("count(*)", "`category_id`= '".$id."' AND `status`='active'");
			$this->resetTable();

			$x = $this->getOne("SELECT COUNT(l.`id`)
				FROM `".$this->mPrefix."listings` l
					INNER JOIN `".$this->mPrefix."listing_categories` lc
					ON `lc`.`listing_id` = l.`id`
					AND `lc`.`category_id`= '".$id."'
					WHERE l.`status` = 'active'
					AND `l`.`category_id`= '".$id."'");

			$new_value += (int)$x;

			$this->setTable("flat_structure");
			$children = $this->onefield("`category_id`", "`parent_id` = '$id' AND `parent_id` <> `category_id`");
			$this->resetTable();

			$new_value_for_all = 0;
			
			if(!empty($children))
			{
				$this->setTable("listings");
				$temp = $this->onefield("count(*)", "`category_id` IN('".join("','", $children)."')
                                AND `status`='active' GROUP BY `category_id`");
				$this->resetTable();
				// get count of cross links
				$crossLinksCount = $this->getKeyValue("SELECT `lc`.`category_id`, COUNT(l.`id`)
						FROM `".$this->mPrefix."listings` l
						INNER JOIN `".$this->mPrefix."listing_categories` lc ON lc.`listing_id` = l.`id`
						WHERE l.`status` = 'active' AND lc.`category_id` IN ('".join("','", $children)."')
						GROUP BY `lc`.`category_id`");

				if($crossLinksCount)
				{
					$temp[] = array_sum($crossLinksCount);
				}

				unset($children);

				if(!empty($temp))
				{
					$new_value_for_all = array_sum($temp);
				}
				unset($temp);
			}

			$new_value_for_all += $new_value;

			$diff1 = ( $new_value - $old_value );
			$diff2 = ( $new_value_for_all - $old_value_for_all );

			// there were no actual changes
			if($diff1 == 0 && $diff2 == 0)
			{
				return false;
			}

			// num_listings increased
			if($diff1 > 0)
			{
				$num = "+".$diff1;
			}
			else
			{
				$num = $diff1;
			}
			
			// num_all_listings increased
			if($diff2 > 0)
			{
				$all = "+".$diff2;
			}
			else
			{
				$all = $diff2;
			}
		}
		else
		{ 
			if(!empty($str_num))
			{
				$num = $str_num;
			}
			else
			{
				$num = false;
			}

			if(!empty($str_all_num))
			{
				$all = $str_all_num;
			}
			else
			{
				$all = $num;
			}

			if(!$num && !$all)
			{
				return false;
			}
		}

		$this->setTable("flat_structure");
   		$parents = $this->onefield("`parent_id`", "`category_id` = '".$id."' AND `parent_id` <> `category_id`");
		$this->resetTable();

		if(!is_array($parents))
		{
			$parents = array();
		}
		$parents[] = $id;

		$params = array();
		if($num != 0)
		{
			$params['num_listings'] = "num_listings".$num;
		}

	    if($all != 0)
		{
			$params['num_all_listings'] = "num_all_listings".$all;
		}
	
		if(!empty($params))
		{
			$ret = $this->update(array(), "`id` IN('".join("','", $parents)."')", array(), $params);

			if(!empty($parents))
			{
				foreach($parents as $parent)
				{
					$this->mCacher->remove("categoriesByParent_" . $parent);
				}
			}
		}

		return (bool)$ret;
	}

	/**
	 * checkClick 
	 * 
	 * Checks if a category was already clicked
	 *
	 * @param mixed $aId category id
	 * @param mixed $aIp ip address
	 * @access public
	 * @return int
	 */
	function checkClick($aId, $aIp)
	{
		$sql = "SELECT `id` FROM `".$this->mPrefix."category_clicks` ";
		$sql .= "WHERE `ip` = '{$aIp}' ";
		$sql .= "AND `category_id` = '{$aId}' ";
		$sql .= "AND (TO_DAYS(NOW()) - TO_DAYS(`date`)) <= 1 ";

		return $this->getOne($sql);
	}
		
	/**
	 * click 
	 *
	 * Adds record when listing is clicked
	 * 
	 * @param int $aCategory category id
	 * @param str $aIp ip address
	 * @access public
	 * @return void
	 */
	function click($aCategory, $aIp)
	{
		parent::setTable("category_clicks");
		parent::insert(array('category_id' => $aCategory, 'ip' => $aIp), array("date" => "NOW()"));
		parent::resetTable();

		parent::update(array(), "id = :id", array('id' => $aCategory), array("clicks" => "clicks+1"));

		return true;
	}
	
	/**
	 * getCatSearchCriterias 
	 *
	 * Returns cause for a query
	 * 
	 * @param string $aWhat string to search for
	 * @param int $aType search type
	 * @access public
	 * @return string
	 */
	function getCatSearchCriterias($aWhat, $aType)
	{
		$sql = '';
		$words = preg_split('/[\s]+/u', $aWhat);
		$tmp = array();
		if (1 == $aType || 2 == $aType)
		{
			foreach ($words as $word)
			{
				$tmp[] = "(CONCAT(`t44`.`title`,' ',`t44`.`description`) LIKE '%{$word}%')";
			}
			$sql .= 1 == $aType ? 'WHERE ('.implode(" OR ",$tmp).')' : (2 == $aType ? 'WHERE '.implode(" AND ",$tmp) : '');
		}
		else if (3 == $aType)
		{
			$sql .= "WHERE (CONCAT(`t44`.`title`,' ',`t44`.`description`) LIKE '%{$aWhat}%')";
		}
		$sql .= " AND `t44`.`status` = 'active' ";
		$sql .= "AND t44.`hidden` = '0' ";	

		return $sql;
	}
	
	/**
	 * getCatByCriteria 
	 *
	 * Returns categories by some value
	 * 
	 * @param int $aStart starting position 
	 * @param int $aLimit number of categories to be returned
	 * @param string $aCause sql condition on select categories
	 * @param mixed $calcFoundRows 
	 * @access public
	 * @return void
	 */
	function getCatByCriteria($aStart = 0, $aLimit = 0, $aCause = '', $calcFoundRows=false)
	{
		$a = '';
		if ($calcFoundRows)
		{
			$a = "SQL_CALC_FOUND_ROWS";
		}
		$sql = "SELECT ".$a." `t44`.*, ";
		$sql .= "`t44`.`path` `path`, `t44`.`title` `category_title` ";
		$sql .= "FROM `".$this->mTable."` `t44` ";
		$sql .= $aCause;
		$sql .= " ORDER BY ";
		$sql .= "`t44`.`num_listings` ASC ";	
		
		$sql .= $aLimit ? "LIMIT ".$aStart.", ".$aLimit."" : '';

		return $this->getAll($sql);
	}
	
	/**
	 * getParents
	 * 
	 * Returns parent categories of current category
	 * 
	 * @param string $aFields returned fields
	 * @param int $aId category id
	 * @access public
	 * @return void
	 */
	function getParents($aFields, $aId)
	{
		if (isset($aFields) && is_array($aFields))
		{
			$fields = '`t44`.`' . implode('`,`t44`.`', $aFields) . '`';
		}
		$sql = "SELECT $fields FROM ";
		$sql .= "`".$this->mTable."` `t44` ";
		$sql .= "LEFT JOIN `".$this->mPrefix."flat_structure` `fs`";
		$sql .= "ON `fs`.`parent_id` = `t44`.`id` ";
		$sql .= "WHERE `fs`.`category_id` = '{$aId}' ";
		$sql .= "AND `t44`.`id` != '0' ";
		$sql .= "ORDER BY `t44`.`id` ";

		$data = $this->getAll($sql);
		$data = $data ? $data : array();

		if (count($aFields) > 1)
		{
			return $data;
		}
		else
		{
			$return = array();
			foreach ($data as $key => $value) 
			{
				$return[$key] = $value[$aFields[0]];
			}
			return $return;
		}
	}
	
	function getAdvSearchCategories($where, $sortBy='`t44`.`id` ASC', $aStart = 0, $aLimit = 0)
	{
		$a = "SQL_CALC_FOUND_ROWS ";

		$sql = "SELECT ".$a." `t44`.* ";
		$sql .= "FROM `".$this->mTable."` `t44` ";
		$sql .= "WHERE ";
		$sql .= $where;
		$sql .= " AND `t44`.`status` = 'active' ";
		$sql .= "AND t44.`hidden` = '0' ";
		$sql .= "ORDER BY ";
		$sql .= $sortBy;
		$sql .= ",`t44`.`order` ASC ";
		$sql .= $aLimit ? "LIMIT ".$aStart.", ".$aLimit."" : '';

		return $this->getAll($sql);
	}
	
}
