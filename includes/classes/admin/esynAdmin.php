<?php
//##copyright##

/**
 * esynAdmin 
 *
 * Implements main class for eSyndiCat administration board
 *
 * @uses esynDatabase
 * @package 
 * @version $id$
 */
class esynAdmin extends eSyndiCat
{
	/**
	 * getMaxOrder 
	 *
	 * Returns max order
	 * 
	 * @access public
	 * @return int
	 */
	function getMaxOrder()
	{
		$where = '';
		if($this->mTable == $this->mPrefix."categories" && func_num_args() > 0 && ($aCategory = func_get_arg(0)) > 0)
		{
			$where = " `parent_id` = '".$aCategory."'";
		}

		return $this->one("MAX(`order`)", $where);
	}

	/**
	 * cleanTable 
	 *
	 * Cleans table
	 * 
	 * @param mixed $aTable 
	 * @access public
	 * @return bool
	 */
	function cleanTable($aTable=false)
	{
		if($aTable == false)
		{
			$aTable = $this->mTable;
		}
		$sql = "TRUNCATE TABLE `".$aTable."`";
		
		return $this->query($sql);
	}

	/**
	 * getTables 
	 * 
	 * Returns array of tables
	 *
	 * @access public
	 * @return arr
	 */
	function getTables($prf = '')
	{
		$prefix	= !empty($prf) ? $prf : $this->mPrefix;
		$out 	= false;
		$sql 	= "SHOW TABLES";
		$r 		= $this->query($sql);
		if($this->getNumRows($r) > 0)
		{
			$out = array();
			while ($row = mysql_fetch_row($r))
			{
				if(!empty($prefix) && 0===strpos($row[0], $prefix))
				{
					$out[] = $row[0];
				}
			}
		}

		return $out;
	}

	/**
	 * getFields 
	 *
	 * Returns list of table fields
	 * 
	 * @param string $aTable table name
	 * @access public
	 * @return void
	 */
	function getFields($aTable)
	{
		if(empty($aTable))
		{
			$aTable = $this->mTable;
		}
		$sql = "DESC `".$aTable."`";

		return $this->getAll($sql);
	}

	/**
	 * getKeys 
	 *
	 * Returns keys of a table
	 * 
	 * @param string $aTable table name
	 * @access public
	 * @return arr
	 */
	function getKeys($aTable)
	{
		$sql = "SHOW KEYS FROM `".$aTable."` ";

		return $this->getAll($sql);
	}

	/**
	 * getDatabases 
	 *
	 * Return the list of databases
	 * 
	 * @access public
	 * @return void
	 */
	function getDatabases()
	{
		$out 	= false;
		$sql 	= "SHOW DATABASES";
		$r 		= $this->query($sql);
		
		if($this->getNumRows($r) > 0)
		{
			$out = array();
			while ($row = mysql_fetch_row($r))
			{
				$out[] = $row[0];
			}
		}

		return $out;
	}
}