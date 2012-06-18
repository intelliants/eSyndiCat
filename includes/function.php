<?php 
//##copyright##

function e($val, $key='')
{
	d($val, $key, 'error');
}

function d($val, $key = '', $type = 'debug')
{
	if (is_bool($val))
    {
    	if ($val)
    	{
    		$val = '<i style="color:green">true</i>';	
    	}
    	else
    	{
    		$val = '<i style="color:red">false</i>';	
    	}
    }
    
	if ('' == $key)
	{
		$_SESSION[$type][] = $val;	
	}
    else 
    {
    	if (isset($_SESSION[$type][$key]))
    	{
    		if (is_array($_SESSION[$type][$key]))
    		{
	    		$_SESSION[$type][$key][] = $val;
    		}
	    	else
	    	{
	    		$_SESSION[$type][$key] = array($_SESSION[$type][$key], $val);
	    	}
    	}
    	else
    	{
    		$_SESSION[$type][$key] = $val;
    	}
    }
}

function _lang($key = '', $default = false, $echo = false)
{
	return _t($key, $default, $echo);
}

function _t($key = '', $default = false, $echo = false)
{
	global $esynI18N;
	
	$return = '';

	if(isset($esynI18N[$key]))
	{
		$return = $esynI18N[$key];
	}
	else
	{
		$return = !$default ? '{' . $key . '}' : $default;
	}
		
	if($echo)
	{
		echo $return;
	}
	
	return $return;
}

function v($val = '<br />', $title='')
{
	if(is_array($val))
    {
    	if('' != $title)
    	{
	    	$name = 'pre_' . mt_rand(1000,9999);
	        echo '<h2 onclick="document.getElementById(\''.$name.'\').style.display = (document.getElementById(\''.$name.'\').style.display==\'none\' ? \'block\' : \'none\');" style="margin:0px;cursor:pointer">
	        	<b><i style="color:gray">'.$title.'</i></b> ['.count($val).']</h2>
	        	<pre style="margin:0px;display:none;" id="'.$name.'">';
    	}
    	else echo '<pre>';
        print_r($val);
        echo '</pre>';
    }
    else
    {
    	if(is_bool($val)) 
    	{
    		if($val) $val = '<i style="color:green">true</i>';
    		else $val = '<i style="color:red">false</i>';
    	}
        echo '<div>'.($title != '' ? '<b><i style="color:RoyalBlue">'.$title.':</i></b> ' : '') . $val . '</div>';
    }
}

function byte_view($num = 0)
{
	$text = '';
	$num = (int)$num;
	$blist = array('Kb', 'Mb', 'Gb', 'Pb');
	
	$i = 0;
	while($num > 0 && $i < 10)
	{
		if(isset($blist[$i]))
		{
			$temp = ($num / 1024);
			if(floor($temp) > 0)
			{
				$num = number_format($temp, 5, '.', '');
				$text = number_format($num, 2, '.', ' ') . $blist[$i];
			}
		}
		else $num = 0;
		$i++;
	}
	return $text;
}

function shutdownHandler($str)
{
	preg_match('#Parse error:(.*)in(.*)on line(.*)#i', $str, $matches);
	if(empty($matches)) return $str;
	define('SHUTDOWN', true);
	e(iaErrorHandler(0,$matches[1],$matches[2], $matches[3]));
	return '';
}

function shutdown()
{
	if(defined('SHUTDOWN'))
	{
		include IA_HOME . 'debug.php';
		exit('Aborting...');
	}
}

function iaErrorHandler($errno = 0, $errstr = '', $errfile = '', $errline = 0)
{
	$text = '';
	$exit = false;
	$errfile = str_replace(IA_HOME, '', $errfile);
	$errortype = array (
		E_ERROR              => 'Error',
		0                    => 'Parsing Error',
		2048                 => 'Error',
		E_WARNING            => 'Warning',
		E_PARSE              => 'Parsing Error',
		E_NOTICE             => 'Notice',
		E_CORE_ERROR         => 'Core Error',
		E_CORE_WARNING       => 'Core Warning',
		E_COMPILE_ERROR      => 'Compile Error',
		E_COMPILE_WARNING    => 'Compile Warning',
		E_USER_ERROR         => 'User Error',
		E_USER_WARNING       => 'User Warning',
		E_USER_NOTICE        => 'User Notice',
	);
	$error = ' '.$errstr.' <i><br /> '.($errline!=0?'on line <b>'.$errline.'</b>':'')
		.' in file '.$errfile.'</i><div class="hr">&nbsp;</div>';
    switch ($errno) 
    {
    	case 2048:
    		$text = '';
    		break;
    	case 0:
    	case E_COMPILE_ERROR:
    	case E_PARSE:
    	case E_ERROR:
	    case E_USER_ERROR:
            $text = '<b style="color:red">'.$errortype[$errno].':</b> '.$error.'<br>';
            $exit = true;
	        break;
	
	    case E_WARNING:
	    case E_USER_WARNING:
            $text = '<b class="e_warning">'.$errortype[$errno].':</b> '.$error.'';
	        break;
	
	    case E_NOTICE:
	    case E_USER_NOTICE:
            $text = '<b class="e_notice">'.$errortype[$errno].':</b> '.$error.'';
	        break;
	
	    default:
	        $text = (!isset($errortype[$errno]) ? 'Unknown error type ['.$errno.']:' : $errortype[$errno]) . ' '.$error.'';
	        break;
    }
	if($errno == 0) return $text;
	if($exit) 
	{
		e($text);
		include IA_HOME . 'debug.php';
		exit('Aborting...');
	}
	elseif($text != '') e($text);
	
	return true;
	
}

function start_time_render()
{
	$_SESSION['site_time'] = array();
	$_SESSION['sql_debug'] = array();
	$_SESSION['hook_debug'] = array();
	time_render('start');
}

function end_time_render($tp=1)
{
	time_render('end');
	
	$time_list	= $_SESSION['site_time'];
	$count		= count($time_list);
	$all		= 0;
	$last_bytes	= 0;
	$bytes		= 0;
	$text		= '';
	$last[0]	= $last[1] = $time_list[0]['time'];
	unset($_SESSION['site_time']);
	
	$start		= $time_list[0]['time'];
	$end		= $time_list[$count-1]['time'];
	$real_all	= number_format((($end[1] + $end[0]) - ($start[1] + $start[0])),5,'.','');
	
	if($tp == 1)
	{
		for( $i = 1; $i < $count; $i++ )
		{
			$type	= $time_list[$i]['type'];
			$bytes	= (int)$time_list[$i]['bytes'];
			$start	= $last[$type][1] + $last[$type][0];
			$end	= $time_list[$i]['time'][1] + $time_list[$i]['time'][0];
			$times	= number_format($end - $start, 5, '.', '');
			$perc	= $bytes > 0 ? ceil(($bytes - $last_bytes) * 100 / $bytes) : 0;
			$pdng	= ( $type == 0 ? '' : ' style="padding-left:15px;"' );
			
			if($type==0) 
			{
				$last[0] = $last[1] = $time_list[$i]['time'];
				$all	+= $times;
			}
			else $last[1] = $time_list[$i]['time'];
			$last_bytes = $bytes;
			
			$text .= ('<tr><td width="1">' . $i . '.</td><td colspan="3" width="100%"><div class="hr">&nbsp;</div></td></tr>
				<tr>
					<td rowspan="2">&nbsp;</td>
					<td rowspan="2" width="60%"' . $pdng . '>
						<i>' . $time_list[$i]['about'] . '</i> <br />
						' . ( $perc >= 5 ? '<font color="orange"><i>memory up:</i></font> ' . $perc . '%' : '' )
					. '</td>
					<td><b>Rendering time:</b></td>
					<td' . $pdng . '>' . ( $times > 0.01 ? '<font color="red">' . $times . '</font>' : $times ) . '</td>
				</tr>
				<tr>
					<td><b>Memory usage:</b></td>
					<td' . $pdng . '>' 
						. byte_view($bytes) 
						. '('.number_format($bytes, 0, '', ' ')
					.'b)</td>
				</tr>');
		}
		
		$search = array('START', 'END');
		$replace = array('<b class="d_green">START</b>', '<b class="d_red">END</b>');
		$text = str_replace($search, $replace, '<b>Real time render:</b> '.$real_all.'<br />
			<b>Math time render:</b> '.$all.'<br />
			<b>Memory usage without gz compress:</b> ' . byte_view($bytes)	. '('.number_format($bytes, 0, '', ' ')	.'b)
			<table border="0" cellspacing="2" cellpadding="2" width="100%">'.$text.'</table>');
	}
	
	return ($tp == 1 ? $text : 'Render Time: '.$real_all);
	
}

function time_render($about = '', $type = 0)
{
	if (ESYN_DEBUG == 2)
	{
		if(function_exists('memory_get_peak_usage')) $bytes = memory_get_peak_usage(1);
		elseif(function_exists('memory_get_usage')) $bytes = memory_get_usage(1);
		$bytes = '-';
		$_SESSION['site_time'][] = array('time' => explode(' ',microtime()), 'about' => $about, 'type' => $type, 'bytes' => $bytes);
	}
}

function mkdir_recursive($pathname, $mode=0777)
{
    is_dir(dirname($pathname)) || mkdir_recursive(dirname($pathname), $mode);
    return is_dir($pathname) || @mkdir($pathname, $mode);
}

function _time()
{
	$r = explode(" ",microtime());

	return (float)$r[0] + (float)$r[1]; 
}

function format_backtrace($backtrace)
{
	$t = array();

	foreach($backtrace as $bt)
	{
		$t[] = array('file' => $bt['file'], 'line' => $bt['line'], 'function' => $bt['function']);
	}
	
	return $t;
}
