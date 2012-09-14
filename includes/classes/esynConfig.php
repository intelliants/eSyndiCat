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
 * esynConfig 
 * 
 * @uses esynDatabase
 * @package 
 */
class esynConfig extends esynDatabase
{
	var $config;
	var $mCacher = null;

	function esynConfig($arg = false)
	{
		$this->mConfig['dbhost'] = ESYN_DBHOST;
		$this->mConfig['dbuser'] = ESYN_DBUSER;
		$this->mConfig['dbpass'] = ESYN_DBPASS;
		$this->mConfig['dbname'] = ESYN_DBNAME;
		$this->mConfig['dbport'] = ESYN_DBPORT;
		$this->mConfig['prefix'] = $this->mPrefix = ESYN_DBPREFIX;

		$this->mTable = $this->mPrefix.$this->mTable;

		$this->connect();

		$this->mCacher = new esynCacher();

		$this->config = (false == $arg) ? array() : $arg;

		if(empty($this->config))
		{
			$this->config = $this->mCacher->get("config", 604800, true);

			if(ESYN_DEBUG > 0 || empty($this->config))
			{
				$this->setTable('config');
				$this->config = $this->keyvalue("`name`,`value`", "`type` NOT IN('divider')");
				$this->resetTable();

				$this->mCacher->write("config", $this->config);
			}
		}
	}
	
	/**
	 * getConfig returns config value by a given key
	 * 
	 * @param string $key config key
	 * @param boolean $db [optional] true: value fetched from db, false: value fetched from cached file
	 * 
	 * @return string 
	 */
	function getConfig($key, $db = false)
	{
		if(isset($this->config[$key]) && !$db)
		{
			return $this->config[$key];
		}

		$this->setTable("config");
		$value = $this->one("`value`", "`name` = :key", array('key' => $key));
		$this->resetTable();

		return $value;
	}
	
	/**
	 * setConfig method modifies configuration array
	 * 
	 * @param string $key configuration array key
	 * @param object $value configuration array value
	 * @param object $db [optional] true: writes to database, false: changes config array for execution only 
	 * 
	 * @return bool
	 */
	function setConfig($key, $value, $db = false)
	{
		$result = true;
		
		if ($db && !is_scalar($value))
		{
			trigger_error("Couldn't store non scalar value to the database".__CLASS__."::set", E_USER_ERROR);
		}

		$this->config[$key] = $value;

		if ($db)
		{
			$this->setTable("config");
			$result = (bool)$this->update(array("value" => $value), "`name` = :key", array('key' => $key));
			$this->resetTable();

			$this->mCacher->remove("config");
			$this->mCacher->remove("intelli.lang");
			$this->mCacher->remove("intelli.admin.lang");
			$this->mCacher->remove("intelli.config");
		}

		return $result;
	}

	function &instance($arg = false)
	{
		static $s;

		if ($s == null)
		{
			$s = new esynConfig($arg);
		}
		elseif ($arg)
		{
			$s->config = $arg;
		}

		return $s;
	}
}
?>