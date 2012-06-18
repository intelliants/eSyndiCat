<?php
//##copyright##

require_once(dirname(__FILE__).'/includes/config.inc.php');

esynLoadClass('Db');
require_once(ESYN_CLASSES."eSyndiCat.php");

$esynDb = &new eSyndicat(ESYN_DBHOST,ESYN_DBUSER, ESYN_DBPASS, ESYN_DBNAME, ESYN_DBPREFIX, ESYN_DBPORT);

class esynApi extends eSyndiCat
{
	var $test = '';
	
	function esynApi()
	{
		parent::eSyndiCat(ESYN_DBHOST, ESYN_DBUSER, ESYN_DBPASS, ESYN_DBNAME, ESYN_DBPREFIX, ESYN_DBPORT);
	}
	
	/**
	 * Inserts new account and returns id of inserted account
	 * 
	 * @param array $aAccount account information
	 * 
	 * @return integer
	 */
	function insertAccount($aAccount)
	{
		parent::setTable('accounts');
		$retval = parent::insert($aAccount, array('date_reg'=>'NOW()'));
		parent::resetTable();
		
		if (!$retval)
			die();
	
		return $retval;
	}
}

$esynApi = new esynApi();
