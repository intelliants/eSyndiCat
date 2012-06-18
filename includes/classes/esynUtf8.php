<?php
//##copyright##

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
