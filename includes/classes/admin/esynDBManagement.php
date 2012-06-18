<?php
//##copyright##

/**
 * esynDBManagement 
 * 
 * @uses esynAdmin
 * @package 
 * @version $id$
 */
class esynDBManagement extends esynAdmin
{

	/**
	 * optimize 
	 * 
	 * @param string $table 
	 * @access public
	 * @return void
	 */
	function optimize($table='')
	{
		if(empty($table))
		{
			$table = $this->mTable;
		}
		else
		{
			$table = $this->mPrefix.$table;
		}
		return $this->getAll("OPTIMIZE TABLE `".$table."`");
	}

	/**
	 * repair 
	 * 
	 * @param string $table 
	 * @access public
	 * @return arr
	 */
	function repair($table='')
	{
		if(empty($table))
		{
			$table = $this->mTable;
		}
		else
		{
			$table = $this->mPrefix.$table;
		}
				
		return $this->getAll("REPAIR TABLE `".$this->mPrefix.$table."`");		
	}
	
	/**
	 * describe 
	 * 
	 * @param string $table 
	 * @param mixed $full 
	 * @access public
	 * @return arr
	 */
	function describe($table='', $full=false)
	{
		if(empty($table))
		{
			$table = $this->mTable;
		}
		else
		{
			$table = $this->mPrefix.$table;
		}
		
		$sql = '';
		if($full)
		{
			$sql = "SHOW TABLE `".$this->mPrefix.$table."`";			
		}

		return $this->getAll($sql);
	}

	/**
	 * getTableStatus 
	 * 
	 * @param string $table 
	 * @access public
	 * @return arr
	 */
	function getTableStatus($table='')
	{
		if(empty($table))
		{
			$table = $this->mTable;
		}
		else
		{
			$table = $this->mPrefix.$table;
		}
		
		$sql = "SHOW TABLE STATUS `".$this->mPrefix.$table."`";

		return $this->getRow($sql);
	}

	function reset($option)
	{
		if(empty($option))
		{
			return false;
		}

		$method_name = '_reset_'.$option;

		if(!method_exists($this, $method_name))
		{
			return false;
		}
		
		$reset_dependence = array(
			'categories' => array('categories', 'category_clicks', 'field_categories', 'flat_structure', 'listing_categories', 'plan_categories'),
			'listings' => array('listings', 'listing_categories', 'listing_clicks'),
			'accounts' => array('accounts', 'listings')
		);

		$this->$method_name();
	}

	function _reset_categories()
	{
		/*
		 * Clear categories table
		 *
		 * Remove all categories except ROOT category
		 *
		 */
		parent::setTable("categories");
		parent::delete("`parent_id` != '-1'");
		parent::resetTable();

		/*
		 * Clear all dependence tables
		 */
		$reset_dependence = array('category_clicks', 'field_categories', 'flat_structure', 'listing_categories', 'plan_categories');

		foreach($reset_dependence as $table)
		{
			$this->_truncate_table($table);
		}
	}

	function _reset_listings()
	{
		$reset_dependence = array('listings', 'listing_categories', 'listing_clicks');

		/*
		 * Clear all dependence tables
		 */
		foreach($reset_dependence as $table)
		{
			$this->_truncate_table($table);
		}
	}

	function _reset_accounts()
	{
		/*
		 * Clear account table
		 */
		$this->_truncate_table('accounts');

		/*
		 * Set the account id value to zero in the listings table
		 */
		parent::setTable("listings");
		parent::update(array("account_id" => '0'));
		parent::resetTable();
	}

	function _truncate_table($table)
	{
		if(empty($table))
		{
			return false;
		}

		parent::query("TRUNCATE TABLE `{$this->mPrefix}{$table}`");
	}
}
