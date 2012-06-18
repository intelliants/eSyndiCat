<?php
//##copyright##

/**
 * esynDebug 
 * 
 * @package 
 * @version $id$
 */
class esynDebug
{
	/**
	 * m 
	 * 
	 * @access public
	 * @return void
	 */
	function m()
	{
		$r = explode(" ",microtime());
		return (float)$r[0] + (float)$r[1]; 
	}
	
	/**
	 * timeSpent 
	 * 
	 * @param mixed $s 
	 * @param mixed $ret 
	 * @param string $start 
	 * @param string $end 
	 * @access public
	 * @return void
	 */
	function timeSpent(&$s,$ret=true, $start='',$end='')
	{
		$x = round(esynDebug::m()-$s,4);
		$x = $wr.$start.$end;
		$s = esynDebug::m();
		if ($ret)
		{
			return $wr.$x.$wr;
		}
		echo $x;
	}
	
	/**
	 * dump 
	 * 
	 * @param mixed $v 
	 * @access public
	 * @return void
	 */
	function dump($v)
	{
		echo "<pre class=\"dump\">";
		$x	= func_get_args();
		foreach($x as $v)
		{
			if (is_array($v))
			{
				print_r($v);
			}
			else
			{
				var_dump($v);
			}	
		}
		echo "</pre>";
	}

	/**
	 * rdump 
	 * 
	 * @access public
	 * @return void
	 */
	function rdump()
	{
		ob_start();
			$x = func_get_args();
			call_user_func_array(array('Debug','dump'), $x);
		return ob_get_clean();
	}
	
	/**
	 * benchStart 
	 * 
	 * @access public
	 * @return void
	 */
	function benchStart()
	{
		$GLOBALS['debugTimerBegin__'] = esynDebug::m();
		return $GLOBALS['debugTimerBegin__'];
	}

	/**
	 * benchStop 
	 * 
	 * @param mixed $show 
	 * @access public
	 * @return void
	 */
	function benchStop($show=true)
	{
		if (!isset($GLOBALS['debugTimerBegin__']))
		{
			trigger_error(__CLASS__."::benchStart() must be called fist", E_USER_ERROR);
		}
		$x = esynDebug::m() - $GLOBALS['debugTimerBegin__'];
		$x = round($x,4);
		unset($GLOBALS['debugTimerBegin__']);
		if ($show)
		{
			echo $x;
		}
		else
		{
			return $x;
		}
	}
	
	/**
	 * debugPrintBacktrace 
	 * 
	 * @param mixed $backtrace 
	 * @access public
	 * @return void
	 */
	function debugPrintBacktrace($backtrace)
	{
		// Iterate backtrace
		$calls = array();
		foreach ($backtrace as $i => $call)
		{
			$location = $call['file'] . ':' . $call['line'];
			$function = (isset($call['class'])) ? $call['class'] . '.' . $call['function'] : $call['function'];
			$params = '';
			if (isset($call['args']))
			{
				foreach($call['args'] as $k=>$c)
				{
					// don't dump the object - too verbose
					if(is_object($c))
					{
						$call['args'][$k] = "Objects class: ".get_class($c);
					}
					elseif(is_array($c) && array_key_exists("GLOBALS", $c)) // we don't need to dump GLOBALS array
					{
						unset($call['args'][$k]);
						break;
					}
				}
				$params = join(', ', $call['args']);
			}
			
			$calls[] = sprintf('#%d  %s(%s) called at [%s]', $i, $function, $params, $location); 
		}

		echo implode("\n", $calls);
	}
}
