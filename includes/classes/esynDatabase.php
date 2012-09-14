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
 * esynDatabase
 *
 * Implements generic class needed for work with database 
 * 
 * @package 
 * @version $id$
 */
class esynDatabase
{
	/**
	 * mConfig 
	 * 
	 * @var mixed
	 * @access public
	 */
	var $mConfig;
	/**
	 * mLink 
	 * 
	 * @var mixed
	 * @access public
	 */
	var $mLink = null;

	/**
	 * mTable 
	 *
	 * specific for every derived class
	 * 
	 * @var string
	 * @access public
	 */
	var $mTable = '';

	/**
	 * _tableNameBackup 
	 * 
	 * used in resetTable and setTable methods
	 *
	 * @var string
	 * @access protected
	 */
	var $_tableNameBackup = '';

	/**
	 * connect 
	 *
	 * Connects to database
	 * 
	 * @access public
	 * @return void
	 */
	function connect($new_link = false)
	{
		static $link;

		if ($link == null || $new_link)
		{
			$link = @mysql_connect($this->mConfig['dbhost'].":".$this->mConfig['dbport'], $this->mConfig['dbuser'], $this->mConfig['dbpass'], $new_link);
			
			$this->mLink = $link;
			
			if(!$this->mLink)
			{
				trigger_error("Database Connection Error | db_connect_error | Could not connect to database.", E_USER_ERROR);
			}

			if (ESYN_MYSQLVER > 40)
			{
				$this->query("SET NAMES 'utf8'");

				// TODO: execute only in MySQL 5.0
				// $this->query("SET sql_mode =''");
			}
			if (!mysql_select_db($this->mConfig['dbname'], $this->mLink))
			{
				trigger_error("Selection Database Error | db_select_error | An error occured while selecting database '{$this->mConfig['dbname']}'.", E_USER_ERROR);
			}
		}

		$this->mLink = $link;
	}

	/**
	 * setTable 
	 * 
	 * Used to set table to work with (almost always resetTable should be called after calling this method)
	 *
	 * @param str $tablename 
	 * @param str $prefix  with or without prefix
	 * @access public
	 * @return string
	 */
	function setTable($tablename, $prefix = true)
	{
		// store for further usage in resetTable
		$this->_tableNameBackup = $this->mTable;
		
		if ($prefix)
		{
			$this->mTable = $this->mPrefix.$tablename;
		}
		else
		{
			$this->mTable = $tablename;
		}
	}
	
	/**
	 * resetTable 
	 *
	 * Reset previously changed table
	 * 
	 * @access public
	 * @return void
	 */
	function resetTable()
	{
		if (empty($this->_tableNameBackup))
		{
			return false;
		}
		
		// store for further usage in resetTable
		$this->mTable = $this->_tableNameBackup;
		$this->_tableNameBackup = '';
	}

	/**
	 * query 
	 *
	 * Executes sql query
	 * 
	 * @param str $aSql sql query
	 * @access public
	 * @return bool
	 */
	function query($aSql)
	{
		if (!$this->mLink)
		{
			$this->connect();
		}
		
		if (ESYN_DEBUG === 2)
		{
			$t = _time();
		}

		$rs = mysql_query($aSql, $this->mLink);

		if (ESYN_DEBUG === 2)
		{
			$t = round(_time() - $t, 4);

			/*$backtrace = debug_backtrace();
			$backtrace = format_backtrace($backtrace);
			var_dump($backtrace);
			exit;*/

			//d(array('sql' => $aSql, 'time' => $t), '', 'sql_debug');
			$GLOBALS['debug_sql'][] = array('sql' => $aSql, 'time' => $t/*, 'backtrace' => $backtrace*/);
		}

		if (!$rs && mysql_errno() != 2013)
		{
			$error = mysql_error($this->mLink);

			ob_start();
			echo '<div style="border: 1px solid #F00; font: 12px verdana; width: 500px; margin: 0 auto; color: #F00; background-color: #EFEFEF; clear: both;font-weight:bold;">';
			echo "<div style=\"border-bottom: 1px solid #F00; padding: 10px;\"><strong>Error:</strong> ".$error."</div>";
			echo "<div style=\"background-color: #EAEAEA; padding: 10px;\">".$aSql."</div>";
			echo '</div>';

			echo "<PRE style=\"color:red;background-color:white;padding:5px;font-size:16px;font-weight:bold;\">";
			echo "\n\n<h3>Debug backtrace:</h3>\n";
			debug_print_backtrace();
			echo "<hr />";
			echo "</PRE>";
			$data = ob_get_clean();
			if (ESYN_DEBUG)
			{
				echo $data;
			}
			else
			{
				echo "<PRE>".mysql_errno().": ".$error."</PRE>";
			}
			trigger_error("Database query error: ".strip_tags($data), E_USER_ERROR);

			die("Fatal database error");
		}

		return $rs;
	}

	/**
	 * getRow 
	 *
	 * Returns row of elements
	 * 
	 * @param str $aSql sql query
	 * @access public
	 * @return arr
	 */
	function getRow($aSql, $aValues = array())
	{
		$this->mysql_bind($aSql, $aValues);

		$out = false;

		$r = $this->query($aSql);
		
		if ($this->getNumRows($r) > 0)
		{
			$out = mysql_fetch_assoc($r);	
		}

		return $out;
	}

	/**
	 * getAll 
	 *
	 * Returns array of rows
	 * 
	 * @param str $aSql sql query
	 * @access public
	 * @return arr|FALSE
	 */
	function getAll($aSql, $aValues = array())
	{
		$this->mysql_bind($aSql, $aValues);

		$out = false;

		$r = $this->query($aSql);
		
		if ($this->getNumRows($r) > 0)
		{
			$out = array();
			while($temp = mysql_fetch_assoc($r))
			{
				$out[] = $temp;
			}
			return $out;
		}
	}

	/**
	 * getAssoc 
	 *
	 * Returns recordset as associative array where the key is the first field
	 * 
	 * @param str $aSql sql query
	 * @access public
	 * @return arr
	 */
	function getAssoc($aSql, $aValues = array())
	{
		$this->mysql_bind($aSql, $aValues);

		$out = false;
		
		$r = $this->query($aSql);
		
		if ($this->getNumRows($r))
		{
			$out = array();
			while ($temp = mysql_fetch_assoc($r))
			{
				$key = array_shift($temp);
				$out[$key][] = $temp;
			}
		}

		return $out;
	}

	/**
	 * getKeyValue 
	 *
	 * Returns recordset as associative array where the key is the first field
	 * 
	 * @param str $aSql sql query 
	 * @access public
	 * @return arr
	 */
	function getKeyValue($aSql, $aValues = array()) 
	{
		$this->mysql_bind($aSql, $aValues);

		$out = false;
		
		$r = $this->query($aSql);
		
		if ($this->getNumRows($r) > 0)
		{
			$out = array();
			$temp = mysql_fetch_row($r);			
			$asArray = false;
			if(count($temp) > 2)
			{
				$out[$temp[0]] = $temp;
				$asArray = true;
			}
			else
			{
				$out[$temp[0]] = $temp[1];
			}

			while ($temp = mysql_fetch_row($r))
			{
				if($asArray)
				{
					$out[$temp[0]] = $temp;
				}
				else
				{
					$out[$temp[0]] = $temp[1];
				}
			}
		}
		return $out;
	}

	/**
	 * getOne 
	 *
	 * Returns only one element or false!
	 * 
	 * @param str $aSql sql query
	 * @access public
	 * @return string
	 */
	function getOne($aSql, $aValues = array())
	{
		$this->mysql_bind($aSql, $aValues);

		$ret = false;
		$r = $this->query($aSql);
		
		if ($this->getNumRows($r) > 0)
		{
			$ret = mysql_result($r,0,0);	
		}

		return $ret;
	}

	/**
	 * getArray 
	 * 
	 * @param mixed $aSql 
	 * @access public
	 * @return void
	 */
	function getArray($aSql, $aValues = array())
	{
		$this->mysql_bind($aSql, $aValues);

		$r = $this->getAll($aSql);

		if (!$r)
		{
			return false;
		}
		
		$ret = array();

		$temp = array_keys($r[0]);
		$field = $temp[0];
		
		foreach($r as $r1)
		{
			$ret[] = $r1[$field];
		}

		return $ret;
	}

	/**
	 * exists 
	 *
	 * Returns true if at least 1 record exists
	 * 
	 * @param str $where where clause
	 * @access public
	 * @return bool
	 */
	function exists($where, $aValues = array())
	{
		$this->mysql_bind($where, $aValues);

		$sql = "SELECT 1 FROM ".$this->mTable;
		$sql .= " WHERE ".$where;
		$r = $this->query($sql);

		return $this->getNumRows($r) > 0;
	}

	/**
	 * getInsertId 
	 * 
	 * @access public
	 * @return void
	 */
	function getInsertId()
	{
		return mysql_insert_id($this->mLink);
	}

	/**
	 * getAffected 
	 * 
	 * @access public
	 * @return void
	 */
	function getAffected()
	{
		return mysql_affected_rows();
	}

	/**
	 * getNumRows 
	 * 
	 * @param mixed $rs 
	 * @access public
	 * @return void
	 */
	function getNumRows($rs)
	{
		if(is_resource($rs))
		{
			return mysql_num_rows($rs);
		}

		return 0;
	}

	/**
	 * foundRows 
	 * 
	 * Returns found rows of previous DQL with SQL_CALC_FOUND_ROWS
	 * Note: this SQL function is MySQL specific!
	 *
	 * @access public
	 * @return void
	 */
	function foundRows()
	{
		return (int)$this->getOne("SELECT FOUND_ROWS()");
	}

	/**
	 * close 
	 *
	 * Close connection to database
	 * 
	 * @param mixed $aConn connection
	 * @access public
	 * @return bool
	 */
	function close($aConn=null)
	{
		if (null==$aConn)
		{
			$aConn = $this->mLink;
		}

		return mysql_close($aConn);
	}

	/**
	 * scanForId 
	 *
	 * get's ID from the string like "`id`='123'" or "id='123'" or "id=123" or "`id`=123" (123 in this case) or false
	 * 
	 * @param mixed $str 
	 * @param string $f 
	 * @access public
	 * @return void
	 */
	function scanForId($str, $f='id')
	{
		$id = false;
		if (preg_match("/.?".$f.".?\s*=\s*'?(\d+)/", $str, $m))
		{
			$id = (int)$m[1];
		}

		return $id;
	}

	/**
	 * describe 
	 * 
	 * @param mixed $table 
	 * @access public
	 * @return void
	 */
	function describe($table = false)
	{
		if (!$table)
		{
			$table = $this->mTable;
		}

		return $this->getAll("DESC `".$table."`");
	}

	/**
	 * onefield 
	 * 
	 * @param mixed $field 
	 * @param string $where 
	 * @param int $start 
	 * @param mixed $limit 
	 * @access public
	 * @return void
	 */
	function onefield($field, $where = '', $values = array(), $start=0, $limit=null)
	{
		if (false!==strpos($field, ","))
		{
			return false;
		}

		$r = $this->get("all", $field, $where, $values, $start, $limit);

		if (!$r)
		{
			return false;
		}

		$ret = array();
		$field = str_replace("`","", $field);
		foreach($r as $r1)
		{
			$ret[] = $r1[$field];
		}

		return $ret;
	}

	/**
	 * all 
	 * 
	 * @param mixed $fields 
	 * @param string $where 
	 * @param int $start 
	 * @param mixed $limit 
	 * @access public
	 * @return void
	 */
	function all($fields, $where='', $values = array(), $start=0, $limit=null)
	{
		return $this->get("all", $fields, $where, $values, $start, $limit);
	}

	/**
	 * keyvalue 
	 * 
	 * @param mixed $fields 
	 * @param string $where 
	 * @param int $start 
	 * @param mixed $limit 
	 * @param mixed $calcRows 
	 * @access public
	 * @return void
	 */
	function keyvalue($fields, $where = '', $values = array(), $start = 0, $limit=null, $calcRows=false)
	{
		return $this->get("keyval", $fields, $where, $values, $start, $limit, $calcRows);
	}	

	/**
	 * row 
	 * 
	 * @param mixed $fields 
	 * @param string $where 
	 * @param int $start 
	 * @param mixed $limit 
	 * @access public
	 * @return void
	 */
	function row($fields, $where = '', $values = array(), $start=0, $limit=null)
	{
		return $this->get("row", $fields, $where, $values, $start, 1);
	}

	/**
	 * one 
	 * 
	 * @param mixed $field 
	 * @param string $where 
	 * @param int $start 
	 * @param mixed $limit 
	 * @access public
	 * @return void
	 */
	function one($field, $where = '', $values = array(), $start=0, $limit=null)
	{
		$x = $this->row($field, $where, $values, $start, 1);

		return is_bool($x) ? $x : array_shift($x);
	}

	/**
	 * get 
	 *
	 * Generic function for getting data
	 * 
	 * @param mixed $type 
	 * @param mixed $fields 
	 * @param string $where 
	 * @param int $start 
	 * @param mixed $limit 
	 * @access public
	 * @return arr
	 */
	function get($type, $fields, $where = '', $values = array(), $start=0, $limit=false)
	{
		if (is_array($fields))
		{
			$fields = implode("`,`",$fields);
			$fields = "`".$fields."`";
		}

		$this->mysql_bind($where, $values);

		if (!empty($where))
		{
			$where = " WHERE ".$where;
			if ($type=='row')
			{
				$where .= " LIMIT 0,1";
			}
			elseif ($limit)
			{
				$where .= " LIMIT ".$start.", ".$limit;
			}
		}

		$q = "SELECT ".$fields." FROM ".$this->mTable." ".$where;
		switch($type)
		{
		case 'all':
			return $this->getAll($q);
		case 'keyval':
			return $this->getKeyValue($q);
		default:
			return $this->getRow($q);
		}

		return $this->getRow($q);
	}

	/**
	 * update 
	 *
	 * Generic update function returns last insert id
	 * 
	 * @param mixed $fields 
	 * @param string $where 
	 * @param mixed $addit 
	 * @access public
	 * @return int
	 */
	function update($fields, $where = '', $values = array(), $addit=null)
	{
		if (empty($this->mTable))
		{
			return false;
		}

		$this->mysql_bind($where, $values);

		if (!empty($where))
		{
			$where = "WHERE ".$where;
		}
		elseif (isset($fields['id']))
		{
			$where = "WHERE `id` = '{$fields['id']}'";
			unset($fields['id']);
		}

		$chain = array();
		
		if (!empty($fields))
		{		
			foreach($fields as $field => $value)
			{
				$value = $this->escape_sql($value);
				$chain[] = "`{$field}` = '{$value}'";
			}
		}
		
		if (!empty($addit))
		{
			foreach($addit as $field => $value)
			{
				$value = $this->escape_sql($value);
				
				$chain[] = "`{$field}` = {$value}";
			}
		}
		
		if(empty($chain))
		{
			return false;
		}

		$this->query("UPDATE `{$this->mTable}` SET ".implode(',',$chain)." ".$where);

		$return = $this->getAffected();

		return $return;
	}

	/**
	 * insert 
	 *
	 * Generic insert function returns last insert id
	 * 
	 * @param arr $fields Can be array of arrays to multiple insert
	 * @param mixed $addit 
	 * @access public
	 * @return int
	 */
	function insert($fields, $addit = null)
	{
		if (empty($this->mTable))
		{
			return false;
		}

		// not for multiple insert
		if (!is_array(array_shift(array_slice($fields, 0,1))))
		{
			$fields = array($fields);
		}

		// fields list
		$flds = '`'.implode("`,`", array_keys($fields[0])).'`';

		// additional values will be appended to the values
		$additValues = '';
		if (!empty($addit))
		{
			foreach($addit as $field => $value)
			{
				$value = $this->escape_sql($value);

				$flds .= ",`$field`";
				$additValues .= ",$value";
			}
			$additValues = substr($additValues, 1);
		}

		$query = "INSERT INTO `".$this->mTable."` (".$flds.") VALUES";

		$vals = '';
		
		foreach($fields as $i => $row)
		{
			$vals .= '(';
			$v = '';

			foreach($row as $r)
			{
				$r = $this->escape_sql($r);
				$v .= "'$r',";
			}
			
			if(empty($additValues))
			{
				$v = substr($v, 0, -1);
			}
			
			$v .= $additValues;
			
			$vals .= $v.'),';
		}

		$vals = substr($vals, 0, -1);

		$this->query($query.$vals);

		return $this->getInsertId();
	}

	/**
	 * delete 
	 * 
	 * @param string $where 
	 * @param mixed $truncate 
	 * @access public
	 * @return void
	 */
	function delete($where = '', $values = array(), $truncate=false)
	{
		if(empty($where))
		{
			trigger_error(__CLASS__.'::delete Parameters required "where clause"). All rows deletion is restricted', E_USER_ERROR);
		}

		$this->mysql_bind($where, $values);
		
		$sql = "DELETE FROM ".$this->mTable." WHERE ".$where;

		$this->query($sql);

		return $this->getAffected();
	}
	
	/*
	 * a simple named binding function for queries that makes SQL more readable:
	 * $sql = "SELECT * FROM users WHERE user = :user AND password = :password";
	 * mysql_bind($sql, array('user' => $user, 'password' => $password));
	 * mysql_query($sql);
	 */
	function mysql_bind(&$sql, $vals)
	{
		if(!empty($vals) && is_array($vals))
		{
			foreach ($vals as $name => $val)
			{
				$sql = str_replace(":$name", "'" . mysql_real_escape_string($val) . "'", $sql);
			}
		}
	}

	function escape_sql($sql)
	{
		return mysql_real_escape_string($sql);
	}

	function split_sql($sql)
	{
		$out = array();

		$sql = trim($sql);

		if (substr($sql, -1) != ";")
		{
			$sql .= ";";
		}

		preg_match_all("/(?>[^;']|(''|(?>'([^']|\\')*[^\\\]')))+;/ixU", $sql, $matches, PREG_SET_ORDER);

		foreach ($matches as $match)
		{
			$out[] = substr($match[0], 0, -1);
		}

		return $out;
	}

	function strip_sql_comment($sql)
	{
		$t = substr($sql, 0, 2);
		
		if ($t == '--')
		{
			$sql = '';
		}

		if ($t == '/*')
		{
			$sql = '';
		}

		if ($t[0] == '#')
		{
			$sql = '';
		}
		
		return $sql;
	}
}
