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
 * @uses esynAdmin
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
	 * addRelation 
	 *
	 * Adds records to table containing flat structure of tree
	 * 
	 * @param int $aParent parent category id
	 * @param int $aId category id
	 * @access public
	 * @return void
	 */
	function addRelation($aParent = 0, $aId = 0)
	{
		$sql = "INSERT INTO `".$this->mPrefix."flat_structure`(`parent_id`, `category_id`) ";
		$sql .= "VALUES ('".$aParent."', '".$aId."')";

		$this->mCacher->remove("categoriesByParent_" . $aParent);

		return $this->query($sql);
	}

	/**
	 * validPath
	 *
	 * Checks if path is valid (can be called statically) 
	 * 
	 * @param int $aPath category path
	 * @access public
	 * @return void
	 */
	function validPath($aPath)
	{
		return (bool)preg_match("/^[a-z\/0-9_-]*$/i", $aPath);
		//empty($aPath) || (strlen($aPath) == strcspn($aPath, " '><\"\#@~`!;,.?#$\'%^&*()+|"));
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
			$par_cat = $this->row("parent_id",	"id='".$top."'");
			$top = $par_cat['parent_id'];
		}

		if($step > 49)
		{
			return false;
		}
		return true;
	}

	/**
	 * getNumSubcategories 
	 * 
	 * Returns number of all child categories
	 *
	 * @param str $aId parent category id
	 * @access public
	 * @return void
	 */
	function getNumSubcategories($aId = 0)
	{
		$sql = "SELECT COUNT(DISTINCT t1.`category_id`) ";
		$sql .= "FROM `".$this->mPrefix."flat_structure` t1 ";
		$sql .= "LEFT JOIN `".$this->mTable."` t2 ";
		$sql .= "ON t1.`category_id` = t2.`id` ";
		$sql .= "WHERE t1.`parent_id` = '".$aId."' ";
		$sql .= "AND t1.`category_id` <> t1.`parent_id` ";
		$sql .= "AND t2.`status` = 'active' ";
		$sql .= "GROUP BY t1.`parent_id`";

		return $this->getOne($sql);
	}

	/**
	 * getSubCategories 
	 *
	 * Return all subcategories by parent id
	 * 
	 * @param int $aId 
	 * @access public
	 * @return void
	 */
	function getSubCategories($aId = 0)
	{
		$sql = "SELECT `t2`.* ";
		$sql .= "FROM `".$this->mPrefix."flat_structure` t1 ";
		$sql .= "LEFT JOIN `".$this->mTable."` t2 ";
		$sql .= "ON t1.`category_id` = t2.`id` ";
		$sql .= "WHERE t1.`parent_id` = '".$aId."' ";
		$sql .= "AND t1.`category_id` <> t1.`parent_id` ";
		$sql .= "AND t2.`status` = 'active' ";

		return $this->getAll($sql);
	}

	/**
	 * getNumCategoriesByStatus 
	 *
	 * Returns number of all categories by status
	 * 
	 * @param str $aStatus category status
	 * @access public
	 * @return void
	 */
	function getNumCategoriesByStatus($aStatus)
	{
		$sql = "SELECT COUNT(`id`) ";
		$sql .= "FROM `".$this->mTable."` ";
		$sql .= "WHERE `status` = '".$aStatus."' " ;
		$sql .= "AND `parent_id` <> '-1' ";

		return $this->getOne($sql);
	}

	/**
	 * getNumListings 
	 *
	 * Returns number of active listings for the category
	 * 
	 * @param int $aCategory category id
	 * @param mixed $all 
	 * @access public
	 * @return arr
	 */
	function getNumListings($aCategory, $all=false)
	{
		if(!$all)
		{
			$sql = "SELECT COUNT(t2.`id`) ";
			$sql .= "FROM `".$this->mPrefix."listing_categories` t1 ";
			$sql .= "LEFT JOIN `".$this->mPrefix."listings` t2 ";
			$sql .= "ON t1.`listing_id` = t2.`id` ";
			$sql .= "AND t2.`status` = 'active' ";
			$sql .= "WHERE t1.`category_id` = '".$aCategory."'";
		}
		else
		{
			$sql = "SELECT COUNT(t4.`id`) num_listings ";
			$sql .= "FROM `".$this->mPrefix."flat_structure` t1 ";
			$sql .= "LEFT JOIN `".$this->mPrefix."listing_categories` t2 ";
			$sql .= "ON t2.`category_id` = t1.`category_id` ";
			$sql .= "LEFT JOIN `".$this->mTable."` t3 ";
			$sql .= "ON t3.`id` = t2.`category_id` ";
			$sql .= "LEFT JOIN `".$this->mPrefix."listings` t4 ";
			$sql .= "ON t4.`id` = t2.`listing_id` ";
			$sql .= "WHERE t1.`parent_id` = '".$aCategory."' ";
			$sql .= "AND t3.`status` = 'active' ";
		}

		return $this->getOne($sql);
	}

	/**
	 * getListings 
	 *
	 * Returns listings by category
	 * 
	 * @param int $aCategory category id
	 * @param int $aStart starting position
	 * @param int $aLimit number of listings to be returned
	 * @param bool $aFeatured if true returns featured on the top
	 * @param mixed $aSponsored 
	 * @param string $account 
	 * @access public
	 * @return arr
	 */
	function getListings($aCategory = 0, $aStart = 0, $aLimit = 0, $aFeatured = TRUE, $aSponsored = FALSE, $account = '')
	{
		$sql = 'SELECT `t1`.*, `t1`.`comments_active` `comments`, `t2`.`crossed`, `t3`.`path`, ';
		$sql .= $account ? 'IF((`t1`.`account_id` = 0) OR (`t1`.`account_id` = \'0\'), \'0\', \'1\') `account_id_edit`' : '\'0\' `account_id_edit` ';
		$sql .= 'FROM `'.$this->mPrefix.'listing` `t1` ';
		$sql .= 'LEFT JOIN `'.$this->mPrefix.'listing_categories` `t2` ON `t1`.`id` = `t2`.`listing_id` ';
		$sql .= 'LEFT JOIN `'.$this->mPrefix.'categories` `t3` ON `t2`.`category_id` = `t3`.`id` ';
		$sql .= 'LEFT JOIN `'.$this->mPrefix.'accounts` `t4` ON `t1`.`account_id` = `t4`.`id` ';
        $sql .= 'WHERE `t2`.`category_id` = \''.$aCategory.'\' AND `t1`.`status` <> \'\' ';
        $sql .= 'GROUP BY `t1`.`id` ';
        $sql .= 'ORDER BY ';
        if ($aFeatured)
        {
            // Featured go first
            $sql .= '`t1`.`featured` DESC, ';
        }
        if ($aSponsored)
        {
            $sql .= '`t1`.`sponsored` DESC, ';
        }
        // Default order
		$sql .= '`t1`.`title` ASC';
		$sql .= $aLimit ? 'LIMIT '.$aStart.', '.$aLimit : '';

        return $this->getAll($sql);
	}

	/**
	 * getAllByParent 
	 * 
	 * @param int $aCategory 
	 * @param mixed $aFull 
	 * @param int $aSubcategories 
	 * @param mixed $aNoCross 
	 * @access public
	 * @return arr
	 */
	function getAllByParent($aCategory = 0, $aFull = FALSE, $aSubcategories = 0, $aNoCross = TRUE)
	{
		/** get categories **/
		$sql = "(SELECT t1.*, COUNT(t4.`id`) num_listings, '0' `crossed`, ";
		$sql .= "t1.`title` `title` ";
		$sql .= "FROM `".$this->mTable."` t1 ";
		$sql .= "LEFT JOIN `".$this->mPrefix."flat_structure` t2 ";
		$sql .= "ON t1.`id` = t2.`parent_id` ";
		$sql .= "LEFT JOIN `".$this->mPrefix."listings` t4 ";
		$sql .= "ON t4.`category_id` = t1.`id` ";

		$sql .= "WHERE t1.`parent_id` = '".$aCategory."' ";

		$sql .= "GROUP BY t1.`id` ";
		$sql .= "ORDER BY t1.`".$this->mConfig['categories_order']."` LIMIT 0, 1000) ";
		if ($aNoCross)
		{
			$sql .= "UNION ALL ";
			$sql .= "(SELECT t1.*, COUNT(t5.`id`) num_listings, '1' `crossed`, ";
			$sql .= "IF((`t2`.`category_title` <> ''), t2.`category_title`, t1.`title`) `title` ";
			$sql .= "FROM `".$this->mTable."` t1 ";
			$sql .= "RIGHT JOIN `".$this->mPrefix."crossed` t2 ";
			$sql .= "ON t1.`id` = t2.`crossed_id` ";
			$sql .= "LEFT JOIN `".$this->mPrefix."flat_structure` t3 ";
			$sql .= "ON t1.`id` = t3.`parent_id` ";
			$sql .= "LEFT JOIN `".$this->mPrefix."listings` t5 ";
			$sql .= "ON t5.`category_id` = t1.`id` ";
			$sql .= "WHERE t2.`category_id` = '".$aCategory."' ";
			$sql .= "GROUP BY t2.`id` ";
			$sql .= "ORDER BY t1.`title` LIMIT 0, 1000) ";
		}
		
		$categories = $this->getAll($sql);
		/** get subcategories **/
		if (!empty($categories))
		{
			$i = 0;
			$sql = '';
			foreach ($categories as $key => $value)
			{
				if (!$value['crossed'])
				{
					if ($i > 0)
					{
						$sql .= 'UNION ALL ';
					}
					$sql .= "(SELECT `parent_id`, `id`, `title`, `status` ";
					$sql .= "FROM `".$this->mTable."`";
					$sql .= "WHERE `parent_id` = ".$value['id']." ";
					$sql .= "ORDER BY `{$this->mConfig['categories_order']}` ";
					$sql .= "LIMIT ".$aSubcategories.") ";
				}
				$i++;
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
						$categories[$key]['subcategories'] = isset($subcategories[$value['id']]) ? $subcategories[$value['id']] : '';
					}
				}
			}
		}

		return $categories;
	}

	/**
	 * insert 
	 * 
	 * Adds new category to the database
	 *
	 * @param arr $aCategory category information
	 * @access public
	 * @return int the id of the newly added category
	 */
	function insert($aCategory)
	{
		// Get new category level.
		// New category level == Parent category level + 1
		$parent = (int)$aCategory['parent_id'];

		$aCategory['level'] = $this->one("`level`", "`id`='".$parent."'");
		
		$aCategory['level']	= (int)$aCategory['level'] + 1;
		$aCategory['level']	= (string)$aCategory['level'];

		$aCategory['status'] = empty($aCategory['status']) ? 'active' : $aCategory['status'];
		if(!empty($aCategory['order']))
		{
			$aCategory['order'] 	= $this->getMaxOrder($aCategory['parent_id']) + 1;
		}

		// Prepare and execute the query for inserting new
		// category into the database

		return parent::insert($aCategory);
	}

	function getMaxOrder($id)
	{
		return $this->one("MAX(`order`)", "`parent_id` = '{$id}'");
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
		$sql = "SELECT t1.`id` related_id, t2.* ";
		$sql .= "FROM `".$this->mPrefix."related` t1 ";
		$sql .= "LEFT JOIN `".$this->mTable."` t2 ";
		$sql .= "ON t1.`related_id` = t2.`id` ";
		$sql .= "WHERE t1.`category_id` = '".$aCategory."'";

		return $this->getAll($sql);
	}

	/**
	 * getCrossedById 
	 *
	 * Returns crossed categories by id
	 * 
	 * @param int $aCategory category id
	 * @access public
	 * @return arr
	 */
	function getCrossedById($aCategory)
	{
		$sql = "SELECT t1.`id` crossed_id, t2.* ";
		$sql .= "FROM `".$this->mPrefix."crossed` t1 ";
		$sql .= "LEFT JOIN `".$this->mTable."` t2 ";
		$sql .= "ON t1.`crossed_id` = t2.`id` ";
		$sql .= "WHERE t1.`category_id` = '".$aCategory."'";

		return $this->getAll($sql);
	}

	/**
	 * getNeighbourCategoriesById 
	 *
	 * Returns neighbour categories
	 * 
	 * @param int $aCategory category id
	 * @access public
	 * @return arr
	 */
	function getNeighbourCategoriesById($aCategory)
	{
		$sql = "SELECT t1.* ";
		$sql .= "FROM `".$this->mTable."` t1 ";
		$sql .= "LEFT JOIN `".$this->mTable."` t2 ";
		$sql .= "ON t1.`parent_id` = t2.`parent_id` ";
		$sql .= "WHERE t2.id = '".$aCategory."' ";
		$sql .= "AND t1.id <> '".$aCategory."' ";

		return $this->getAll($sql);
	}

	/**
	 * addRelated 
	 *
	 * Adds related category
	 * 
	 * @param int $aCategory category id FOR 
	 * @param int $aRelated related category id WHICH
	 * @access public
	 * @return bool
	 */
	function addRelated($aCategory, $aRelated)
	{
		$this->setTable("related");
			$id = parent::insert(array("category_id" => $aCategory,"related_id" => $aRelated));
		$this->resetTable();

		 return $id;
	}

	/**
	 * deleteRelated 
	 *
	 * Deletes related category
	 * 
	 * @param int $aRelated related category record id
	 * @access public
	 * @return bool
	 */
	function deleteRelated($aRelated)
	{
		$sql = "DELETE FROM `".$this->mPrefix."related` WHERE `id` = '".$aRelated."'";

		return $this->query($sql);
	}

	/**
	 * addCrossed 
	 *
	 * Adds crossed category
	 * 
	 * @param int $aCategory category id
	 * @param int $aCrossed crossed category id
	 * @access public
	 * @return bool
	 */
	function addCrossed($aCategory, $aCrossed)
	{
		$this->setTable("crossed");
		$id = parent::insert(array("category_id"=>$aCategory,"crossed_id"=>$aCrossed));
		$this->resetTable();

		$this->mCacher->remove("categoriesByParent_" . $aCategory);
		
		return $id;
	}

	/**
	 * deleteCrossed 
	 * 
	 * Deletes crossed category
	 *
	 * @param int $aId crossed category id
	 * @access public
	 * @return bool
	 */
	function deleteCrossed($aId)
	{
		$this->setTable("crossed");
		parent::delete("`id` = '".$aId."'");
		$this->resetTable();

		return 1;
	}

	/**
	 * getCategoriesByStatus 
	 * 
	 * Returns categories by their status
	 *
	 * @param string $aStatus categories status
	 * @param int $aStart starting position
	 * @param int $aLimit number of categories to be returned
	 * @access public
	 * @return arr
	 */
	function getCategoriesByStatus($aStatus = '', $aStart = 0, $aLimit = 0)
	{
		$sql = "SELECT * ";
		$sql .= "FROM `".$this->mTable."` ";
		$sql .= "WHERE `id` > 0 ";
		$sql .= $aStatus ? "AND `status` = '".$aStatus."' " : ' ';
		$sql .= "ORDER BY `level`, `path` ";
		$sql .= $aLimit ? "LIMIT ".$aStart.", ".$aLimit." " : ' ';

		return $this->getAll($sql);
	}

	/**
	 * adjustLevel 
	 *
	 * Adjusts level values for category as well as its subcategories
	 * 
	 * @param arr $params contains ID and new LEVEL value to start increasing from
	 * @access public
	 * @return void
	 */
	function adjustLevel($params)
	{
		if(!isset($params['id']) || !isset($params['level']))
		{
			return false;
		}
		$id = $params['id'];
		$level = $params['level'];
		// get all children
		$success = parent::update(array("level"=>$level, "id"=>$id));
		if($success)
		{
			$k = $id;
			// 25 iterations 25 is max deepnes
			for($i = 0; $i < 25; $i++)
			{
				$level++;
				if(is_array($k))
				{
					$s = join(",", $k);
					$cats = $this->all("`id`", "`parent_id` IN(".$s.")");					
				}
				else
				{
					$cats = $this->all("`id`", "`parent_id`='".$k."'");
				}
				if(empty($cats))
				{
					break;
				}
				else
				{
					$ids = array();
					foreach($cats as $c)
					{
						$ids[] = $c['id'];
					}
					$s = join(",", $ids);
					parent::update(array("level" => $level), "`id` IN(".$s.")");

					$k = $ids;
				}
			}
			if($i == 25)
			{
				trigger_error("Possible recursive hell while at ".__CLASS__."::adjustLevel() for category #$id", E_USER_WARNING);
			}
		}
	}

	/**
	 * move 
	 *
	 * Moves category to other category
	 * 
	 * @param int $aCategory category id to be moved
	 * @param int $aId category id, where should be moved
	 * @param bool $updatePath update path moved category trigger
	 * @access public
	 * @return void
	 */
	function move($aCategory, $aId, $updatePath = true)
	{
		$category= $this->row("`parent_id`,`title`,`level`,`path`,`num_all_listings`","`id`='".$aCategory."'");

	    // trying to move to the same place where source is already located
	    if($category['parent_id'] == $aId)
	    {
	    	return false;
	    }

		$parent = $this->row("`path`,`level`","`id`='".$aId."'");

		$path = esynCategory::getPath($parent['path'], $this->lastPartOfPath($category['path']));

		$this->setTable("flat_structure");
			$children = $this->onefield("`category_id`", "`parent_id`='".$aCategory."' or `parent_id`='".$aId."'");
		$this->resetTable();

		// mass `deleteRelation`
		if(!empty($children))
		{
			$this->setTable("flat_structure");
				$chs = "'".join("','", $children)."'";
				parent::delete("`parent_id` IN(".$chs.") or `category_id` IN(".$chs.")");
				unset($chs);
			$this->resetTable();
		}

		$fields['parent_id'] = $aId;

		if($updatePath)
		{
			$fields['path'] = $path;
		}

		$success = parent::update($fields, "`id` = '{$aCategory}'");

		/**
		 * Update the childs' paths
		 */
		$sql = "UPDATE `{$this->mPrefix}categories` ";
		$sql .= "SET `path` = REPLACE(`path`, '{$category['path']}/', '{$path}/') ";
		$sql .= "WHERE `path` LIKE '{$category['path']}/%'";

		parent::query($sql);

		if($success)
		{
			$this->adjustLevel(
				array(
					"id" => $aCategory,
					// new level
					"level"	=> $parent['level']+1)
				);

		      $this->adjustNumListings($aCategory);

			  // adjustion of previous parent category
			  $this->adjustNumListings($category['parent_id'], '', "-".$category['num_all_listings']);
			  // adjustion of new parent
		      $this->adjustNumListings($aId);
		}
		$path = esynCategory::getPath($parent['path'], $category['title']);

		if(is_array($children))
		{
			foreach($children as $ch)
			{
				$this->buildRelation($ch);
			}
		}
	}

	/**
	 * deleteRelation 
	 *
	 * Deletes categories relation
	 * 
	 * @param int $aId category id
	 * @access public
	 * @return void
	 */
	function deleteRelation($aId)
	{
		$this->setTable("flat_structure");
		parent::delete("`parent_id`='".$aId."' or `category_id`='".$aId."'");
		$this->resetTable();
	}

	/**
	 * delete 
	 *
	 * Deletes category by id
	 * 
	 * @param int $aId category id
	 * @access public
	 * @return void
	 */
	function delete($aId)
	{
		/** delete listings **/
		$sql = "SELECT t2.`listing_id` ";
		$sql .= "FROM `".$this->mPrefix."flat_structure` t1 ";
		$sql .= "RIGHT JOIN `".$this->mPrefix."listing_categories` t2 ";
		$sql .= "ON t2.`category_id` = t1.`category_id` ";
		$sql .= "WHERE t1.`parent_id` = '".$aId."' ";

		$listings = $this->getAll($sql);

		if ($listings)
		{
			foreach($listings as $value)
			{
				$this->deleteListing("`id`='".$value['listing_id']."'", $value['listing_id']);
			}
		}

		parent::setTable("listings");
		parent::delete("`category_id` = '{$aId}'");
		parent::resetTable();
		
		/** delete categories **/
		$sql = "SELECT `category_id` ";
		$sql .= "FROM `".$this->mPrefix."flat_structure` ";
		$sql .= "WHERE `parent_id` = '".$aId."' ";
		$subcategories = $this->getAll($sql);

		if ($subcategories)
		{
			foreach($subcategories as $value)
			{
				$this->deleteOneCategory($value['category_id']);
			}
		}

		$this->deleteOneCategory($aId);

		$this->mCacher->clearAll('categories');
	}

	/**
	 * deleteListing 
	 *
	 * Deletes link from database
	 * 
	 * @param string $where 
	 * @param int $aLink link id
	 * @access public
	 * @return bool
	 */
	function deleteListing($where, $aLink)
	{
		if(empty($aLink))
		{
			return false;
		}

		$this->setTable("listings");
		$aLink = $this->row("*","`id`='".$aLink."'");

		$deleted = parent::delete($where);

		$this->resetTable();
		
		// Send email in case the link email exists
		// and the corresponding email notification option [listing_delete] is enabled
		if($aLink['email'] && $this->mConfig['listing_delete'])
		{
			$this->setTable("categories");
			$category = $this->row("*", "`id`='".$aLink['category_id']."'");

			$event 	= array(
				"action" => "listing_delete",
				"params" => array(
					"rcpts"=> array($aLink['email']),
					"listing"=> $aLink,
					"path"=> $category['path'],
					"category_id" => $aLink['category_id']
				)
			);
			
			$this->mMailer->dispatcher($event);
		}
		$this->setTable("listings");
			$totalDeleted = $this->cascadeDelete(array("listing_clicks","listing_categories"),"`listing_id`='".$aLink['id']."'");
		$this->resetTable();

		return $deleted;
	}

	/**
	 * deleteOneCategory 
	 *
	 * Deletes category records
	 * 
	 * @param int $aId category id
	 * @access public
	 * @return bool
	 */
	function deleteOneCategory($aId)
	{
		$sql = "DELETE FROM `".$this->mPrefix."flat_structure` ";
		$sql .= "WHERE `parent_id` = '".$aId."' ";
		$sql .= "OR `category_id` = '".$aId."' ";
		$this->query($sql);

		$sql = "DELETE FROM `".$this->mTable."` ";
		$sql .= "WHERE `id` = '".$aId."' OR `parent_id`='".$aId."'";
		$this->query($sql);

		$sql = "DELETE FROM `".$this->mPrefix."related` ";
		$sql .= "WHERE `category_id` = '".$aId."' ";
		$sql .= "OR `related_id` = '".$aId."'";
		$this->query($sql);

		$totalDeleted = $this->cascadeDelete(
			array(
				"category_clicks"
			),
			"`category_id`='".$aId."'"
		);

		$sql = "DELETE FROM `".$this->mPrefix."crossed` ";
		$sql .= "WHERE `category_id` = '".$aId."' ";
		$sql .= "OR `crossed_id` = '".$aId."'";
		$this->query($sql);

		return true;
	}

	/**
	 * lock 
	 *
	 * Locks category by id
	 * 
	 * @param int $aCategory category id 
	 * @param bool $aLock lock action
	 * @access public
	 * @return bool
	 */
	function lock($aCategory, $aLock = true, $subcategories)
	{
		$where = '';

		if($subcategories)
		{
			$sql = "SELECT `t2`.`id`, `t2`.`parent_id` ";
			$sql .= "FROM `".$this->mPrefix."flat_structure` t1 ";
			$sql .= "LEFT JOIN `".$this->mTable."` t2 ";
			$sql .= "ON t1.`category_id` = t2.`id` ";
			$sql .= "WHERE t1.`parent_id` = '".$aCategory."' ";
			$sql .= "AND t1.`category_id` <> t1.`parent_id` ";

			$categories = $this->getKeyValue($sql);

			$categories[$aCategory] = $aCategory;

			if(!empty($categories))
			{
				$where = "`id` IN ('" . implode("','", array_keys($categories)) ."')";
			}
		}
		else
		{
			$where = "`id` = '{$aCategory}'";
		}

		$sql = "UPDATE `".$this->mTable."` ";
		$cause = $aLock ? 1 : 0;
		$sql .= "SET `locked` = '".$cause."' ";
		$sql .= "WHERE " . $where;

		return $this->query($sql);
	}

	/**
	 * unlock
	 *
	 * Unlocks category by id 
	 * 
	 * @param int $aCategory category id
	 * @access public
	 * @return bool
	 */
	function unlock($aCategory, $subcategories)
	{
		return $this->lock($aCategory, false, $subcategories);
	}

	/**
	 * setUnique 
	 *
	 * Makes category template unique by id
	 * 
	 * @param int $aCategory category id
	 * @param bool $aUnique unique action
	 * @access public
	 * @return bool
	 */
	function setUnique($aCategory, $aUnique = true)
	{
		$sql = "UPDATE `".$this->mTable."` ";
		$cause = $aUnique ? 1 : 0;
		$sql .= "SET `unique_tpl` = '".$cause."' ";
		$sql .= "WHERE `id` = '".$aCategory."' ";

		return $this->query($sql);
	}

	/**
	 * getChildCount 
	 *
	 * Checks if some category has any child
	 * 
	 * @param int $catId category id
	 * @access public
	 * @return bool
	 */
	function getChildCount($catId)
	{
		$sql = "SELECT count(category_id) FROM `".$this->mPrefix."flat_structure`";
		$sql .= "WHERE parent_id='$catId'";

		return $this->getOne($sql);
	}

	/**
	 * copy 
	 *
	 * Copies a subcategory recursively
	 * 
	 * @param arr $aTo destination category
	 * @param arr $aWhat target category
	 * @param bool $recursive is copy all of subcategories
	 * @param bool $withListings whether to copy with listings
	 * @access public
	 * @return void
	 */
	function copy($aTo, $aWhat, $recursive=true, $withListings = false)
	{
		$old_id = $aWhat['id'];

		// Prepare the target category for copying.
		unset($aWhat['id']);

		$aWhat['parent_id'] = $aTo['id'];
		$aWhat['path'] = esynCategory::getPath($aTo['path'], $this->lastPartOfPath($aWhat['path']));

		$target = array_map(array("esynSanitize", "sql"), $aWhat);
		$new_id = $this->insert($target);
		$aWhat['id'] = $new_id;

		if($withListings)
		{
			$this->setTable("listings");
				$listings = $this->onefield("id", "`category_id`='".$old_id."'");
			$this->resetTable();
			if($listings)
			{
				$this->setTable("listing_categories");
				$ls = array();
				foreach($listings as $l)
				{
					$ls[] = array("category_id" => $new_id, "listing_id" => $l);
				}
				parent::insert($ls);
				unset($ls);
				$this->resetTable();
			}
		}

		$this->adjustLevel(
			array(
				"id" => $aWhat['id'],
				// new level
				"level"	=> $aTo['level'] + 1
			)
		);

		// Now that category is copied we have to
		// add "flat" records.
		$this->buildRelation($new_id);

		if($recursive)
		{
			// Get a list of target's child categories
			$categories = $this->all("*", "parent_id='".$old_id."'");
			if (!empty($categories))
			{
				foreach ($categories as $category)
				{
					$this->copy($aWhat, $category, $recursive);
				}
			}
		}
	}

	/**
	 * copySubCategories 
	 *
	 * Copies subcategories from one category to another one
	 * 
	 * @param int $aSource source category id
	 * @param int $aDest destination category id
	 * @param bool $recursive whether to copy all its categories
	 * @param bool $withListings whether to copy with listings
	 * @access public
	 * @return void
	 */
	function copySubCategories($aSource = 0, $aDest = 0, $recursive=false, $withListings=false)
	{
		// Get destination category fields.
		$to = $this->row("`id`, `parent_id`, `path`, `level`", "`id`='".$aDest."'");

		// Get a list of child categories, iterate through
		// the list, and add each subcategory to the
		// destination category (recursively).
		$categories = $this->all("*","parent_id='".$aSource."'");
		if (!empty($categories))
		{
			foreach ($categories as $category)
			{
				$this->copy($to, $category, $recursive, $withListings);
			}
		}
	}

	/**
	 * update 
	 * 
	 * @param mixed $fields 
	 * @param string $where 
	 * @param array $addit 
	 * @access public
	 * @return void
	 */
	function update($fields, $where = '', $addit=array())
	{
		$ret = false;

		if(isset($fields['old_path']) && !empty($fields['old_path']) && isset($fields['path']) && !empty($fields['path']))
		{
			$oldpath = $fields['old_path'];
			
			unset($fields['old_path']);

			$ret = parent::update($fields, $where, $addit);

			// regenerate path of the category's childrens
			// REPLACE function is multibyte safe!!
			
			$sql = "UPDATE `{$this->mPrefix}categories` ";
			$sql .= "SET `path` = REPLACE(`path`, '{$oldpath}/', '{$fields['path']}/') ";
			$sql .= "WHERE `path` LIKE '{$oldpath}/%'";

			parent::query($sql);
		}
		else
		{
			unset($fields['old_path']);

			$ret = parent::update($fields, $where, $addit);
		}

		if(!isset($fields['id']))
		{
			$fields['id'] = $this->scanForId($where);
		}

		if(isset($fields['id']) && false !== $fields['id'])
		{
			$this->adjustNumListings($fields['id']);
		}

		$this->mCacher->remove('categoriesByParent_' . $fields['id']);

		return $ret;
	}

	/**
	 * lastPartOfPath 
	 * 
	 * @param mixed $path 
	 * @access public
	 * @return void
	 */
	function lastPartOfPath($path)
	{
		$pos = strrpos($path,'/');
		if($pos !== false)
		{
			$pos++;
		}
		else
		{
			$pos = 0;
		}
		return substr($path, $pos);
	}

	/**
	 * adjustNumListings 
	 * 
	 * This method used to recalculate num_link and num_all_listings fields
	 * if only two arguments passed, then $str_num will be appliable for both fields
	 *
	 * @param int $id Category id
	 * @param string $str_num (optional) Update string (if specified, then there will be no calculations, just updation)
	 * @param string $str_all_num (optional) Same as previous but for num_all_listings fields
	 * @access public
	 * @return bool success or fail
	 */
	function adjustNumListings($id=0, $str_num='', $str_all_num='')
	{
		$ret = false;

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
			$ret = parent::update(array(), "`id` IN('".join("','", $parents)."')", array(), $params);
		}

		$this->mCacher->clearAll('categories');

		return (bool)$ret;
	}

	/**
	 * getPath 
	 *
	 * returns path
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
}
