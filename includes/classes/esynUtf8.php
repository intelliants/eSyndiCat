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
 * The class for including the UTF8 function
 *
 */
class esynUtf8
{
	function loadUTF8Core()
	{
		static $loaded = false;
		
		$type = 'native';

		if($loaded)
		{
			return false;
		}

		$p = ESYN_INCLUDES.'phputf8'.ESYN_DS;

		if(extension_loaded('mbstring'))
		{
			mb_internal_encoding('UTF-8');

			$type = 'mbstring';
		}

		require_once $p . $type . ESYN_DS . 'core.php';

		$loaded = true;

		return true;
	}

	function loadUTF8Function($fn)
	{
		$p = ESYN_INCLUDES . 'phputf8' . ESYN_DS . $fn . ".php";

		if(file_exists($p))
		{
			require_once $p;

			if(function_exists($fn))
			{
				return true;
			}

			trigger_error("No such function from phputf8 package: '$fn'", E_USER_ERROR);
		}
	}

	function loadUTF8Util()
	{
		if(func_num_args() == 0)
		{
			return false;
		}

		foreach(func_get_args() as $fn)
		{
			require_once ESYN_INCLUDES . 'phputf8' . ESYN_DS . 'utils' . ESYN_DS . $fn . ".php";
		}
	}
}
?>
