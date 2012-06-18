<?php
//##copyright##

/**
 * esynListingField 
 * 
 * @uses eSyndiCat
 * @package 
 * @version $id$
 */
class esynListingField extends eSyndiCat
{	
	/**
	 * mTable 
	 * 
	 * @var string
	 * @access public
	 */
	var $mTable = 'listing_fields';
	
	/**
	 * getAllFieldsByCategory 
	 *
	 * Returns all editable fields by category
	 * 
	 * @param mixed $id 
	 * @access public
	 * @return arr
	 */
	function getAllFieldsByCategory($id)
	{
		$sql = 'SELECT f.* FROM `'.$this->mTable.'` `f` ';
		$sql.= 'LEFT JOIN `'.$this->mPrefix.'field_categories` `fc` ON `fc`.`field_id` = `f`.`id` ';
		
		$sql.= "WHERE f.`editable`='1' AND ( fc.`category_id` = '{$id}'";
		
		if($id > 0)
		{
			$this->setTable("flat_structure");
			$parents = parent::onefield('`parent_id`', "`category_id`='{$id}' AND `parent_id`<>'{$id}'");
			$this->resetTable();			
			$sql.= " OR ( `f`.`recursive`='1' AND `fc`.`category_id` IN ('".implode("','",$parents)."'))";
		}
		$sql.= ") ORDER BY f.`order` ASC ";
		
		return $this->getAll($sql);
	}

	/**
	 * getAllFieldsByPlan 
	 *
	 * Returns all editable fields by plan
	 * 
	 * @param mixed $id 
	 * @access public
	 * @return arr
	 */
	function getAllFieldsByPlan($id = false)
	{
		$sql = 'SELECT f.* FROM `'.$this->mTable.'` `f` ';
		$sql.= 'LEFT JOIN `'.$this->mPrefix.'field_plans` `fp` ON `fp`.`field_id` = `f`.`id` ';
		$sql.= "WHERE f.`editable`='1' ";
		$sql.= $id ? "AND fp.`plan_id` = '{$id}' " : '';
		$sql.= "ORDER BY f.`order` ASC ";
		
		return $this->getAll($sql);
	}	
}
