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
