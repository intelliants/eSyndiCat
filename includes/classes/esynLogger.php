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
 * esynLogger 
 * 
 * @package 
 * @version $id$
 */
class esynLogger
{
	/**
	 * log 
	 * 
	 * @param mixed $errno 
	 * @param mixed $errstr 
	 * @param mixed $errfile 
	 * @param mixed $errline 
	 * @access public
	 * @return void
	 */
	function log($errno, $errstr, $errfile, $errline)
	{
		// if error source is smarty from smarty then ignore
		if (ESYN_DEBUG == 0 && (E_NOTICE == $errno || E_USER_NOTICE == $errno)
					|| (defined("E_STRICT") && $errno == E_STRICT)
					|| false!==strpos(dirname($errfile), ESYN_CLASSES."smarty"))
		{
			return true;
		}

		global $esynDb;

		$esynDb->setTable("logs");

		$msg = "";

		$die = false;

		switch ($errno)
		{
			case E_NOTICE:
				$msg .="Notice\t $errstr";
				$type = "notice";
				break;
		 	case E_USER_NOTICE:
				$msg .="\t $errstr";
				$type = "user notice";
			  break;
			case E_WARNING:
			  $msg .="WARNING\t $errstr";
			  $type = "warning";
			  break;
			case E_USER_ERROR:
			  $msg .= "Fatal ERROR\t $errstr";
			  $type = "error";
			  $die = true;
			  break;
			case E_USER_WARNING:
			 	$type = "user warning";
			  $msg .="eSynDicat WARNING\t $errstr";
			  break;
		}

		// don't log notices
		if(E_NOTICE != $errno)
		{
			$data = array(
				"type" => $type,
				"msg" =>	esynSanitize::sql($msg),
				"source" => esynSanitize::sql($errfile),
				"line" => $errline
			);
			if(ESYN_DEBUG != 2 && $errno != E_USER_NOTICE)
			{
				$esynDb->insert($data, array("date" => "NOW()"));	
			}
			else
			{
				file_put_contents(ESYN_HOME.'tmp/log/user_notice.txt', $msg."\n\n", FILE_APPEND);
			}
		}

		// show only if debug level is set
		if(ESYN_DEBUG > 0 && $errno != E_NOTICE && $errno != E_USER_NOTICE)
		{
			if($errno == E_ERROR || $errno == E_USER_ERROR || $errno == E_WARNING)
			{
				// if error occured in "eval"
				if(false === strpos($data['source'], "eval()"))
				{
					$source = file($data['source']);
					$code	= array_slice($source, $data['line']-5, 10);
					unset($source);
	
					if($code)
					{
						echo "<table class=\"striped bd\">";
						echo "<tr>";
						echo "<th>Line</th>";
						echo "<th>Code <i>".$data['source']."</i></th>";
						echo "</tr>";
						foreach($code as $k => $v)
						{
							$k = $k+$data['line']-4;
							echo "<tr";
							if($k == $data['line'])
							{
								echo " bgcolor=\"#E7D3D3\"";
							}

							echo ">";
							echo "<td>".$k;
							echo "</td>";
							echo "<td>";
							$r = '<span style="color: #0000BB">&lt;?php';
							echo preg_replace("/".preg_quote($r)."(&nbsp;)+<\/span>/", "", highlight_string("<?php ".$v, true));
							if($k == $data['line'])
							{
								echo "<HR>";
								echo "<PRE>";
								$backtrace = debug_backtrace();
					     // Unset call to esynLogger::log
		     				array_shift($backtrace);

								esynDebug::debugPrintBacktrace($backtrace);
								echo "</PRE>";
								echo "<h4 style=\"color:maroon\">".$data['msg']."</h4>";
							}
							echo "</td>";
							echo "</tr>";
						}
						echo "</table>";
					}
				}
			}
			else
			{
				esynDebug::dump($data);
			}
		}

		if ($die)
		{
			die("Error occured. Please see the logs or report an error to the administrator");
		}

		$esynDb->resetTable();
	}
	
	/**
	 * insert 
	 * 
	 * @param mixed $v 
	 * @access public
	 * @return void
	 */
	function insert($v)
	{
		if(is_scalar($v)) {
			$msg = $v;
		}
		else
		{
			ob_start();
			print_r($v);
			$msg = ob_get_clean();
		}

		global $esynDb;

		$esynDb->setTable("logs");
			$data = array(
				"type" 		=> "debug",
				"msg" 		=>	esynSanitize::sql($msg)
			);
			$esynDb->insert($data, array("date" => "NOW()"));
		$esynDb->resetTable();
	}
}
