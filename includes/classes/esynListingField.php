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
