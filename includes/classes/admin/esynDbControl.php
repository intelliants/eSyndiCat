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
 * esynDbControl 
 * 
 * @uses esynAdmin
 * @package 
 * @version $id$
 */
class esynDbControl extends esynAdmin
{

	/**
	 * makeStructureBackup 
	 * 
	 * Return structure sql dump
	 *
	 * @param string $aTable table name
	 * @param bool $aDrop if true use DROP TABLE
	 * @param bool $aPrefix if true use prefix
	 * @access public
	 * @return string
	 */
	function makeStructureBackup($aTable, $aDrop = false, $aPrefix = true)
	{
		$out .= $aDrop ? "DROP TABLE IF EXISTS `$aTable`;\n" : '';

		$res = $this->getRow("SHOW CREATE TABLE `$aTable`");
		$out .= $res ? array_pop($res) . ';' : false;
		$out = !$aPrefix ? str_replace($this->mPrefix, '{prefix}', $out) : $out;
		return $out;
	}

	/**
	 * makeDataBackup 
	 *
	 * Return data sql dump
	 * 
	 * @param string $aTable $aTable table name
	 * @param bool $aComplete if true use complete inserts
	 * @param bool $aPrefix if true use prefix
	 * @access public
	 * @return string
	 */
	function makeDataBackup($aTable, $aComplete = false, $aPrefix = true) 
	{
		$this->setTable($aTable, false);
		
		$table_name = !$aPrefix ? str_replace($this->mPrefix, "{prefix}" ,$aTable) : $aTable;

		$out = '';
		$complete = '';
		
		if ($aComplete)
		{
			$fields = $this->getFields($aTable);

			$complete = ' (';

			foreach($fields as $key => $value)
			{
				$complete .= "`".$value['Field']."`, ";
			}
			$complete = ereg_replace(", $", "", $complete);
			$complete .= ')';
		}

		$data = $this->all("*");

		if($data)
		{
			foreach($data as $key=>$value)
			{
				$out .= "INSERT INTO `".$table_name."`".$complete." VALUES (";
				foreach($value as $key2 => $value2)
				{
					if(!isset($value[$key2]))
					{
						$out .= "NULL, ";
					}
					elseif($value[$key2] != "")
					{
						$out .= "'".esynSanitize::sql($value[$key2])."', ";
					}
					else
					{
						$out .= "'', ";
					}
				}
				//$out = str_replace(', $', '', $out);
				$out = rtrim($out, ', ');
			
				$out .= ");\n";
			}
		}

		$this->resetTable();

		return $out;
	}

	/**
	 * makeFullBackup 
	 *
	 * Return data + structure sql dump
	 * 
	 * @param string $aTable table name
	 * @param bool $aDrop if true use DROP TABLE
	 * @param bool $aComplete if true use complete inserts
	 * @param bool $aPrefix if true use prefix
	 * @access public
	 * @return string
	 */
	function makeFullBackup($aTable, $aDrop = false, $aComplete = false, $aPrefix = true)
	{
		$out = $this->makeStructureBackup($aTable, $aDrop, $aPrefix);
		$out .= "\n\n";
		$out .= $this->makeDataBackup($aTable, $aComplete, $aPrefix);
		$out .= "\n\n";

		return $out;	
	}

	/**
	 * makeDbStructureBackup 
	 *
	 * Returns structure dump of a database
	 * 
	 * @param bool $aDrop if true use DROP TABLE
	 * @param bool $aPrefix if true use prefix
	 * @access public
	 * @return string
	 */
	function makeDbStructureBackup($aDrop = false, $aPrefix = true)
	{
		$out = "CREATE DATABASE `{$this->mConfig['dbname']}`;\n\n";

		$tables = $this->getTables();

		foreach($tables as $table)
		{
			$out .= $this->makeStructureBackup($table, $aDrop, $aPrefix);
			$out .= "\n\n";
		}

		return $out;
	}

	/**
	 * makeDbDataBackup 
	 *
	 * Returns data dump of a database
	 * 
	 * @param bool $aComplete if true use complete inserts
	 * @param bool $aPrefix if true use prefix
	 * @access public
	 * @return string
	 */
	function makeDbDataBackup($aComplete = false, $aPrefix = true)
	{
		$tables = $this->getTables();

		foreach($tables as $table)
		{
			$out .= $this->makeDataBackup($table, $aComplete, $aPrefix);
			$out .= "\n\n";
		}

		return $out;
	}

	/**
	 * makeDbBackup 
	 *
	 * Returns whole database dump
	 * 
	 * @param bool $aDrop if true use DROP TABLE
	 * @param bool $aComplete if true use complete inserts
	 * @param bool $aPrefix if true use prefix
	 * @access public
	 * @return string
	 */
	function makeDbBackup($aDrop = false, $aComplete = false, $aPrefix = true)
	{
		$out = "CREATE DATABASE `".$this->mConfig['dbname']."`;\n\n";
		
		$tables = $this->getTables();

		foreach($tables as $table)
		{
			$out .= $this->makeStructureBackup($table, $aDrop, $aPrefix);
			$out .= "\n\n";
			$out .= $this->makeDataBackup($table, $aComplete, $aPrefix);
			$out .= "\n\n";
		}

		return $out;
	}

	/**
	 * addFlat 
	 * 
	 * @param mixed $id 
	 * @param mixed $parent_id 
	 * @access public
	 * @return void
	 */
	function addFlat($id, $parent_id)
	{
		// Insert record into "flat" table
		$this->setTable("flat_structure");
		$this->insert(array("parent_id" => $parent_id, "category_id" => $id));
		$this->resetTable();

		// Check if parent > 0 and call itself recursively
		if ((int)$parent_id > 0)
		{
			$this->setTable("categories");
			$parent_id2 = $this->one("`parent_id`", "`id` = '".$parent_id."'");
			$this->resetTable();
			
			$this->addFlat($id, $parent_id2);
		}
	}

	/**
	 * recalculateListingsCount 
	 * 
	 * @param mixed $parent 
	 * @access public
	 * @return void
	 */
	function recalculateListingsCount($parent)
	{
		global $listings;
		global $parents;
		global $num_all_listings_map;
		global $called;
		
		$total = 0;
		foreach($parents[$parent] as $id)
		{
			if(isset($num_all_listings_map[$id]))
			{
				// num listings of all its children
				$total += $num_all_listings_map[$id] - $listings[$id];
			}
			elseif(isset($parents[$id]))
			{
				$this->recalculateListingsCount($id);
				$called[] = $id;
			}

			if(!in_array($id, $called))
			{
				$total += $listings[$id];
			}
			else
			{
				$total += $num_all_listings_map[$id];
			}
		}

		if(!isset($num_all_listings_map[$parent]))
		{
			$num_all_listings_map[$parent] = 0;
		}

		if(!in_array($parent, $called))
		{
			if(isset($listings[$parent]))
			{
				$total += $listings[$parent];
			}
			
			$num_all_listings_map[$parent] += $total;
		}
	}
}
?>
