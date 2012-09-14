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


session_start();

if (false === strpos(PHP_SAPI, "ap") && isset($_SERVER['REQUEST_URI']))
{
	$t = $_SERVER['REQUEST_URI'];
	if (false!==strpos($t, "?"))
	{
		$t = substr($t, 0, strpos($t, "?"));
	}
	if (false!==strpos($t, ".php/"))
	{
		$t = substr($t, 0, strpos($t, ".php")+4);
	}
	elseif (false === strpos($t, ".php"))
	{
		$t .= "index.php";
	}
	$_SERVER['SCRIPT_NAME'] = $t;
	unset($t);
}

// installation files can only be in `install` directory!
if (false===strpos($_SERVER['SCRIPT_NAME'], "/install/"))
{
	die("Access denied");
}

$msg = '';

define("ESYN_VERSION", "1.7");

/** This will NOT notice about uninitialized variables **/
ini_set("display_errors", "0");
error_reporting (E_ALL);

define("ESYN_DS", DIRECTORY_SEPARATOR);

/** Disable Magic Quotes runtime**/
set_magic_quotes_runtime(0); 

define("ESYN_BASEDIR", dirname(__FILE__).ESYN_DS."..".ESYN_DS);

$script_path = str_replace('install', '', dirname($_SERVER['SCRIPT_NAME']));

// config file name
$filename = '..'.ESYN_DS.'includes'.ESYN_DS.'config.inc.php';
require_once(ESYN_BASEDIR.'admin'.ESYN_DS.'util.php');


$_SERVER['REQUEST_TIME'] = time();
if (version_compare("5.0", PHP_VERSION, ">"))
{
	require_once(ESYN_BASEDIR."includes".ESYN_DS."compat.inc.php");
}

$step = !isset($_GET['step']) ? 0 : (int)$_GET['step'];

if (isset($_POST['db_action']))
{
	if (strlen($script_path) > 1)
	{
		$base = str_replace($script_path, '', $_POST['script_path']);
	}
	else
	{
		$base = $_POST['script_path'];
	}

	$base = rtrim($base, '/');

	$err = false;

	if (!$_POST['dbhost'])
	{
		$err[] = 1;
	}

	if (!$_POST['dbuser'])
	{
		$err[] = 2;
	}

	if (!$_POST['dbname'])
	{
		$err[] = 3;
	}

	if (!$_POST['dbport'] || (int)$_POST['dbport'] > 65536 || preg_match("/\D/", $_POST['dbport']))
	{
		$_POST['dbport'] = $_POST['dbport'] = "3306";
	}	

	if (!$_POST['admin_username'])
	{
		$err[] = 4;
	}

	if (!$_POST['admin_password'])
	{
		$err[] = 5;
	}

	if ($_POST['admin_password'] != $_POST['admin_password2'])
	{
		$err[] = 6;
	}

	if (!esynValidator::isEmail($_POST['admin_email']))
	{
		$err[] = 7;
	}

	if(empty($_POST['prefix']))
	{
		$err[] = 9;
	}

	if (!$err)
	{
		$link = mysql_connect($_POST['dbhost'].":".$_POST['dbport'], $_POST['dbuser'], $_POST['dbpwd']);
		
		$error = false;
		
		if (!$link)
		{
			$error = true;
			$msg = 'Could not connect to MySQL server: '.mysql_error().'<br />';
		}
		
		if (!mysql_select_db($_POST['dbname'], $link))
		{
			$error = true;
			$msg .= 'Could not select database '.esynSanitize::html($_POST['dbname']).': '.mysql_error();
		}

		if (!$error && !isset($_POST['delete_tables']))
		{
			$res = mysql_query('SHOW TABLES', $link);
			if(mysql_num_rows($res) > 0)
			{
				while($temp = mysql_fetch_row($res))				
				{
					if(strpos($temp[0],esynSanitize::sql($_POST['prefix'])) !== false)
					{
						$error = true;
						$msg .= 'Tables with prefix "'.esynSanitize::sql($_POST['prefix']).'" are already present.';
						$err[] = 8;

						break;
					}
				}
			}
			unset($res);
		}

		$dirp = trim($script_path, "/");

		if (!empty($dirp))
		{
			$dirp .= '/';
		}
	
		/** Writing to database **/
		if (!$error)
		{
			// if version upper than 40 then use 41.sql (even if MySQL 5.x ..)
			$mysql_ver = version_compare("4.1", mysql_get_server_info($link), "<=") ? "41" : "40";
			$mysql_ver_data = ($mysql_ver == "41") ? "ENGINE=MyISAM DEFAULT CHARSET=utf8" : "TYPE=MyISAM";
			$filename = ESYN_BASEDIR.'install'.ESYN_DS.'database'.ESYN_DS.'mysql-data.sql';			

			if (!($f = fopen ($filename, "r" )))
			{
				$error = true;
				$msg = 'Could not open file with sql instructions: '.$filename;
			}
			else
			{
				$msg = $s_sql = '';
				$cnt = 0;
				while ($s = fgets ($f, 10240))
				{
					$s = trim ($s);
					if (isset($s[0]) && ($s[0] == '#' || $s[0] == ''))
					{
						continue;
					}

					if (!empty($s) && $s[strlen($s)-1] == ';')
					{
						$s_sql .= $s;
					}
					else
					{
						$s_sql .= $s;
						continue;
					}

					$s_sql = str_replace("{install:prefix}", esynSanitize::sql($_POST['prefix']), $s_sql);
					$s_sql = str_replace('{install:tmpl}', esynSanitize::sql($_POST['tmpl']), $s_sql);
					$s_sql = str_replace('{install:lang}', 'en', $s_sql);
					$s_sql = str_replace('{install:admin_username}', esynSanitize::sql($_POST['admin_username']), $s_sql);
					$s_sql = str_replace('{install:admin_password}', md5(esynSanitize::sql($_POST['admin_password'])), $s_sql);
					$s_sql = str_replace('{install:email}', esynSanitize::sql($_POST['admin_email']), $s_sql);
					$s_sql = str_replace('{install:mysql_version}', $mysql_ver_data, $s_sql);
					$s_sql = str_replace("{install:url}", $base.'/'.$dirp, $s_sql);
					$s_sql = str_replace("{config_version}", ESYN_VERSION, $s_sql);
					
					$delete_tables = isset($_POST['delete_tables']) && 'on' == $_POST['delete_tables'] ? '' : '#';
					$s_sql = str_replace('{install:drop_tables}', $delete_tables, $s_sql);					

					$res = true;
					
					$res = mysql_query($s_sql, $link);
					if (!$res)
					{
						if ($cnt == 0)
						{
							$cnt++;
							$msg .= '<div class="db_errors">';
						}
						$msg .= "<div class=\"qerror\">'".mysql_errno()." ".mysql_error()."' during the following query:</div> <div class=\"query\">{$s_sql} </div>";
					}
					$s_sql = "";
				}
				$msg .= $msg ? '</div>' : '';
				fclose($f);
			}
			mysql_close($link);
		}

		/** Writing to config file **/
		if (!$error)
		{
			//{date}
			$configurator = file_get_contents(ESYN_BASEDIR."install".ESYN_DS."config.inc.php.sample");

			$configurator = str_replace("{esyn_base_url}", $base.'/', $configurator);
			$configurator = str_replace("{esyn_url}", $base.'/'.$dirp, $configurator);

			$configurator = str_replace("{date}", date("d F Y H:i:s"), $configurator);
			
			$q = get_magic_quotes_gpc() ? "strip_params();" : "//strip_params();";

			$configurator = str_replace("{magic_quotes}", $q, $configurator);

			$rt = ini_get("magic_quotes_runtime");
			$q = '';
			//{quotes_runtime}
			if (!empty($rt))
			{
				$q = 'ini_set("magic_quotes_runtime", "off");';
			}
			$configurator = str_replace("{quotes_runtime}", $q, $configurator);
			ini_set("magic_quotes_runtime",	"off");
			
			if(stristr($_SERVER['SERVER_SOFTWARE'], "Microsoft"))
			{
				$_SERVER["PHP_SELF"] = str_replace('/', '\\\\', $_SERVER["PHP_SELF"]);
				$q2 = preg_replace("/{$_SERVER['PHP_SELF']}/", '', $_SERVER["SCRIPT_FILENAME"]);
			}
			else
			{
				$q2 = $_SERVER['DOCUMENT_ROOT'];
			}

			if (PATH_SEPARATOR != ';')
			{
				$q 	= "'$q2'";
			}
			else
			{
				$q	= "'".addslashes($q2)."'";
			}

			$last = strlen($q)-1;
			if (substr($q2, -1, 1) != ESYN_DS || substr($q2, -1, 1) != '/')
			{
				$q .= ".ESYN_DS";
			}
			$configurator = str_replace("{doc_root}", $q, $configurator);
			
			//{dir}
			$q = trim($script_path, "/");
			$q = trim($q, ESYN_DS);
			if (!empty($q))
			{
				$_dir = $q.ESYN_DS;
				$q = "'".$q."'.ESYN_DS";
			}
			else
			{
				$q = "''";
				$_dir = '';
			}
			$configurator = str_replace("{dir}", $q, $configurator);

			//{salt_string}
			$q = esynUtil::getNewToken();
			$configurator = str_replace("{salt_string}", $q, $configurator);

			//{mysql_version}
			$configurator = str_replace("{mysql_version}", $mysql_ver, $configurator);

			if (empty($_POST['dbport']))
			{
				$_POST['dbport'] = "3306";
			}

			$configurator = str_replace("{dbhost}",	  $_POST['dbhost'],	$configurator);
			$configurator = str_replace("{dbuser}",	  $_POST['dbuser'],	$configurator);
			$configurator = str_replace("{dbpass}",	  $_POST['dbpwd' ],	$configurator);
			$configurator = str_replace("{dbname}",	  $_POST['dbname'],	$configurator);
			$configurator = str_replace("{dbport}",	  $_POST['dbport'], $configurator);			
			$configurator = str_replace("{dbprefix}", $_POST['prefix'],	$configurator);

			$php4 = version_compare("5.0", PHP_VERSION, ">");

			// ctype
			$ctype_available = extension_loaded('ctype');
			$q = '';
			if (!$ctype_available)
			{
				$q = 'include(ESYN_INCLUDES."compat".ESYN_DS."ctype.php");';
			}
			$configurator = str_replace("{ctype_loader}", $q, $configurator);

			//{phputf8_driver_loaders}
			// mbstring			
			$mbstring_available	= function_exists('mb_internal_encoding');
			$q = '';
			if ($mbstring_available)
			{
				$q =<<<BKTN
mb_internal_encoding('UTF-8');
require_once \$p.'mbstring'.ESYN_DS.'core.php';
BKTN;
			}
			else
			{
				$q =<<<BKTN
require_once \$p.'utils'.ESYN_DS.'unicode.php';
require_once \$p.'native'.ESYN_DS.'core.php';
BKTN;
			}
			$configurator = str_replace("{phputf8_driver_loaders}", $q, $configurator);

			$filename = 'config.inc.php';

			$cv = ESYN_VERSION."|"."http://".$_SERVER['HTTP_HOST']."/".$_dir;			
			
			// uncomment this on production
			//@file_get_contents("http://www.esyndicat.com/check_version.php?cv=".base64_encode($cv));

			send_admin_email($_POST['admin_email'], $_POST['admin_username'], $_POST['admin_password'], $base.'/'.$dirp);

			if (is_writable('..'.ESYN_DS.'includes'.ESYN_DS.$filename))
			{
				if (!$handle = fopen('..'.ESYN_DS.'includes'.ESYN_DS.$filename, 'w'))
				{
					$error = true;
					$msg = "Cannot open file: {$filename}";
				}

				/** write to opened file **/
				if (fwrite($handle, $configurator, strlen($configurator)) === FALSE)
				{
					$error = true;
					$msg .= "Cannot write to file: ".$filename;
				}
				else
				{
					header('Location: index.php?step=3');
					exit;
				}
				fclose($handle);
			}
			else
			{
				esynPrintHeader();
			?>
<div class="inner-content">pre-installation check &#187; license &#187; configuration &#187; <b>completed</b></div>
<h2 id="install">Installation completed</h2>

<table width="97%" cellpadding="0" cellspacing="0" style="margin: 0 10px 10px 10px;">
<tr>
	<td colspan="2"><h3>Installation log:</h3></td>
</tr>
<tr>
	<td class="item-desc">Your config file is un-writeable now. A copy of the configuration file will be downloaded to your computer when you click the button 'Download config.inc.php'. You should upload this file to the same directory where you have eSyndiCat Free. Once this is done you should log in using the admin credentials you provided on the previous form and configure the software according to your needs.
	<p>You can also copy the content to that file. You can see it in a box after you <a href="javascript:void(0);" onclick="if (document.getElementById('file_content').style.display=='block') { document.getElementById('file_content').style.display='none';} else {document.getElementById('file_content').style.display='block'}">click here</a>.</p>
	<p style="font-weight: bold;">Thank you for choosing eSyndiCat Free.</p></td>
	<td class="inner-content" style="vertical-align: top;">
		<table width="100%">
		<tr>
			<td class="elem">Database Installation</td>
			<td align="left">
				<?php
					if ($msg)
					{
						echo '<span class="no">Error during MySQL queries execution:</span>';
						echo $msg;
					}
					else
					{
						echo '<span class="yes">OK</span>';
					}
				?>
			</td>
		</tr>
		<tr>
			<td class="elem">Configuration File</td>
			<td align="left">
				<span class="no">Not available for writing</span><br />
				You MUST save config.inc.php file to your local PC and then upload to eSyndiCat includes directory. <a href="javascript:void(0);" onclick="if (document.getElementById('file_content').style.display=='block') { document.getElementById('file_content').style.display='none';} else {document.getElementById('file_content').style.display='block'}">Click here</a> to view the content of config.inc.php file.<br />
				<form action="index.php?step=3" method="post">
					<p><input type="hidden" name="config_content" value="<?php echo esynSanitize::html($configurator); ?>" /></p>
				  <p><input type="hidden" name="download_config" value="1" /></p>
					<div style="margin: 10px 0; text-align: center;"><input type="submit" value="Download config.inc.php" /></div>
				</form>
			</td>
		</tr>
		<tr>
			<td colspan="2"><div style="display:none;border: 1px solid #777; width: 423px; height: 400px; background-color: #ededed; padding:10px;overflow:auto;" id="file_content"><?php
			if (is_callable("highlight_string"))
			{
				highlight_string($configurator);
			}
			else
			{
				echo nl2br(esynSanitize::html($configurator));
			}
			?></div></td>
		</tr>		
		<tr>
			<td colspan="2"><div class="remove_install">Now you MUST completely remove 'install' directory from your server.</div></td>
		</tr>
		</table>
	</td>
</tr>
</table>

<div class="btn lgn">
	<button type="button" onclick="history.go(-1);" name="check">Back</button>&nbsp;&nbsp;
	<button type="button" onclick="document.location.href='../admin/';" name="next" tabindex="3">Next</button>
</div>
			<?php
				esynPrintFooter();
				exit;
			}
		}
	}
}

/** Last step to download the config file **/
if (isset($_POST['download_config'])  && $_POST['download_config'] == 1)
{
	header('Content-Type: text/x-delimtext; name="config.inc.php"');
	header('Content-disposition: attachment; filename="config.inc.php"');
	
	echo get_magic_quotes_gpc() ? stripslashes($_POST['config_content']) : $_POST['config_content'];
	exit;	
}

/** Prints common page header **/
esynPrintHeader();

if (!$step)
{
	clearstatcache();
?>
	<div class="inner-content"><b>pre-installation check</b> &#187; license &#187; configuration &#187; completed</div>
	<h2 id="install">Pre-installation check</h2>
	
	<table width="97%" cellpadding="0" cellspacing="0" style="margin: 0 10px 10px 10px;">
	<tr>
		<td colspan="2"><h3>Server configuration</h3></td>
	</tr>
	<tr>
		<td class="item-desc">If any of these items are highlighted in red then please take actions to correct them. Failure to do so could lead to your installation not functioning correctly.</td>
		<td class="inner-content">
			<table width="100%">
			<tr>
				<td class="elem">MySQL version</td>
			<td align="left"><?php echo function_exists('mysql_connect') ? '<span class="yes">'.mysql_get_client_info().'</span>' : '<span class="no">MySQL 4.x or upper required</span>'; ?></td>
			</tr>
			<tr>
				<td class="elem">PHP version</td>
				<td align="left"><?php echo version_compare("4.3", PHP_VERSION, ">") ? '<span class="no">Not available</span>' : '<span class="yes">'.PHP_VERSION.'</span>';?></td>
			</tr>
			<tr>
				<td>&nbsp; - Remote files access support</td>
				<td align="left"><?php echo hasAccessToRemote() ? '<span class="yes">Available</span>' : '<span class="no">Unavailable (highly recommended to enable "CURL" extension or "allow_url_fopen")</span>';?></td>
			</tr>
			<tr>
				<td>&nbsp; - XML support</td>
				<td align="left"><?php echo extension_loaded('xml') ? '<span class="yes">Available</span>' : '<span class="no">Unavailable (recommended)</span>';?></td>
			</tr>
			<tr>
				<td>&nbsp; - MySQL support</td>
				<td align="left"><?php echo function_exists('mysql_connect') ? '<span class="yes">Available</span>' : '<span class="no">Unavailable (required)</span>';?></td>
			</tr>
			<tr>
				<td>&nbsp; - GD extension</td>
				<td align="left"><?php echo extension_loaded('gd') ? '<span class="yes">Available</span>' : '<span class="no">Unavailable (highly recommended)</span>';?></td>
			</tr>
			<tr>
				<td>&nbsp; - Mbstring extension</td>
				<td align="left"><?php echo extension_loaded('mbstring') ? '<span class="yes">Available</span>' : '<span class="no">Unavailable (not required) </span>';?></td>
			</tr>			
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2"><h3>Recommended Settings</h3></td>
	</tr>
	<tr>
		<td class="item-desc">These settings are recommended for PHP in order to ensure full compatibility with eSyndiCat Free.
However, eSyndiCat Free will still operate if your settings do not quite match the recommended.</td>
		<td class="inner-content">
			<table width="100%">
			<tr style="font-weight: bold;">
				<td style="width: 150px;">Directive</td>
				<td>Recommended</td>
				<td>Actual</td>
			</tr>
			<?php
				$php_recommended_settings = array(array ('File Uploads','file_uploads','ON'), array ('Magic Quotes GPC','magic_quotes_gpc','OFF'), array ('Register Globals','register_globals','OFF'));
				foreach ($php_recommended_settings as $phprec)
				{
			?>
			<tr>
				<td><?php echo $phprec[0]; ?>:</td>
				<td><?php echo $phprec[2]; ?>:</td>
			<td><?php if ( esynGetIniSetting($phprec[1]) == $phprec[2] ) {  ?><span class="yes"><?php } else { ?> <span class="no"><?php } echo esynGetIniSetting($phprec[1]); ?></span></td>
			</tr>
			<?php
				}
			?>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2"><h3>Directory &amp; File Permissions</h3></td>
	</tr>
	<tr>
		<td class="item-desc">In order for eSyndiCat Free to function correctly it needs to be able to access or write to certain files or directories. If you see "Unwriteable" you need to change the permissions on the file or directory to allow eSyndiCat Free to write to it.
		
		<?php
		$allowNextStep = true;
		foreach(array('tmp') as $d)
		{
			if (!is_writable(ESYN_BASEDIR.$d))
			{
				$allowNextStep = false;
				break;
			}
		}
?>
		</td>
		<td class="inner-content">
			<table width="100%">
			<?php
				esynWritableCell('backup', " (optional)");
				esynWritableCell('tmp');
				esynWritableCell('plugins', " (optional)");
				esynWritableCell('uploads', " <br />(optional - used when you create listing field with type \"file storage\" and \"image\")");
			?>
			<tr>
				<td valign="top" class="elem">includes<?php echo ESYN_DS?>config.inc.php</td>
				<td align="left">
				<?php
					if (@is_writable(ESYN_BASEDIR.'includes'.ESYN_DS.'config.inc.php' ))
					{
						echo '<span class="yes">Writeable</span>';
					}
					elseif (is_writable( '..' ))
					{
						echo '<span class="yes">Writeable</span>';
					}
					else
					{
						echo '<span class="no">Unwriteable</span> '. $msg .'<br />';
						echo 'You can still continue the install as the configuration will be displayed at the end, just copy &amp; paste this and upload.';
					}
				?>
				</td>
			</tr>
			</table>			
		</td>
	</tr>
	</table>

	<div class="btn lgn">
		<button type="button" onclick="document.location.href='index.php';" name="check">Check</button>&nbsp;&nbsp;
		<button type="button" onclick="document.location.href='index.php?step=1';" name="next" tabindex="3" <?php if (!$allowNextStep):?>disabled="disabled"<?php endif;?>>Next</button>
	</div>
<?php
	}
	elseif ($step == 1)
	{
?>
	<div class="inner-content"><a href="index.php">pre-installation check</a> &#187; <b>license</b> &#187; configuration &#187; completed</div>
	<h2 id="install">eSyndiCat License</h2>
	<iframe src="../LICENSE.htm" class="license" frameborder="0" scrolling="auto"></iframe>
	
	<div class="btn lgn">
		<button type="button" onclick="document.location.href='index.php';" name="back" tabindex="3">Back</button>&nbsp;&nbsp;
		<button type="button" onclick="document.location.href='index.php?step=2';" name="next" tabindex="3">Next</button>
	</div>
<?php
	}
	elseif ($step == '2')
	{
?>
	<div class="inner-content"><a href="index.php">pre-installation check</a> &#187; <a href="index.php?step=1">license</a> &#187; <b>configuration</b> &#187; completed</div>
	<h2 id="install">General Configuration</h2>

<?php
	if ($msg)
	{
		echo "<div class=\"error\">{$msg}</div>";
	}
?>

	<form action="index.php?step=2" method="post">
	<table cellpadding="0" cellspacing="0" style="margin: 0 10px 10px 10px;">
	<tr>
		<td colspan="2"><h3>MySQL database configuration:</h3></td>
	</tr>
	<tr>
		<td class="item-desc">
			<p>Setting up eSyndiCat to run on your server involves 3 simple steps...</p>
			<p>Please enter the hostname of the server eSyndiCat Free is to be installed on.</p>
			<p>Enter the MySQL username, password and database name you wish to use with eSyndiCat Free.</p>
			<p>Enter the a table name prefix to be used by eSyndiCat Free and select what to do with existing tables from former installations.</p>
		</td>
		<td class="inner-content" style="width: 480px; vertical-align: top;">
			<table>
  			<tr>
				<td>MySQL Hostname:</td>
				<td><input type="text" name="dbhost" size="20" value="<?php echo isset($_POST['dbhost']) ? esynSanitize::html($_POST['dbhost']) : 'localhost'; ?>" id="t1" /></td>
				<td><div class="err" id="err1">Please input correct MySQL hostname.</div></td>
  			</tr>
			<tr>
				<td>MySQL User Name:</td>
				<td><input type="text" name="dbuser" size="20" value="<?php echo isset($_POST['dbuser']) ? esynSanitize::html($_POST['dbuser']) : ''; ?>" id="t2" /></td>
				<td><div class="err" id="err2">Please input correct MySQL username.</div></td>
			</tr>
			<tr>
				<td>MySQL Password:</td>
				<td><input type="password" name="dbpwd" size="20" value="" /></td>
				<td></td>
			</tr>
  		  	<tr>
				<td>MySQL Database Name:</td>
				<td><input type="text" name="dbname" size="20" value="<?php echo isset($_POST['dbname']) ? esynSanitize::html($_POST['dbname']) : ''; ?>" id="t3"/></td>
				<td><div class="err" id="err3">Please input correct database name.</div></td>
			</tr>
  		  	<tr>
				<td>MySQL Port:</td>
				<td><input type="text" name="dbport" size="20" value="<?php echo isset($_POST['dbport']) ? (int)$_POST['dbport'] : '3306'; ?>" /></td>
				<td>&nbsp;</td>
			</tr>
  		 <tr>
				<td>Table Prefix:</td>
				<td><input type="text" name="prefix" id="t8" value="<?php echo (isset($_POST['prefix'])) ? $_POST['prefix'] : 'v'.preg_replace("/[^a-z0-9]/i","",ESYN_VERSION).'_'; ?>" /></td>
				<td>
					<div class="err" id="err8">Please choose another table prefix.</div>
					<div class="err" id="err9">Please input table prefix.</div>
				</td>
  			</tr>
  		 <tr>
			<td colspan="3"><input type="checkbox" id="delete_tables" name="delete_tables" <?php echo isset($_POST['delete_tables']) ? 'checked="checked"' : ''; ?> />&nbsp;<label for="delete_tables">Delete tables if exist</label></td>
  			</tr>
  		 	</table>
			<input type="hidden" name="db_action" id="db_action" value="1" />
		</td>
	</tr>
	<tr>
		<td colspan="2"><h3>Common configuration</h3></td>
	</tr>
	<tr>
		<td class="item-desc">
			<p>Configure correct paths and URLs to your eSyndiCat Free.</p>
			<p>Please select a template from a list of available templates uploaded to your templates directory.</p>
		</td>
		<td class="inner-content" style="width: 480px; vertical-align: top;">
			<table>
			<tr>
				<td><div style="width:120px">URL:</div></td>
				<td align="center"><input type="text" name="script_path" value="http://<?php echo $_SERVER['SERVER_NAME'].$script_path;?>" size="30"/></td>
			</tr>
			<tr>
				<td>Template:</td>
				<td>
				<?php
					/** gets templates **/
					$templ_path = ESYN_BASEDIR."templates/";
					$directory = opendir($templ_path);
					while (false !== ($file=readdir($directory)))
					{
						if (substr($file,0,1) != "." && 'common' != $file)
						{
							if (is_dir($templ_path.$file))
							{
								$templates[] = $file;
							}
						}
					}
					closedir($directory);
					if (count($templates) < 2)
					{
						echo '<select disabled="disabled">';
						$value = $templates[0];
						echo "<option value=\"".$value."\">".$value."</option>\n\t\t";
						echo "</select>";
						echo '<input type="hidden" name="tmpl" value="'.$value.'" />';
					}
					else
					{
						echo '<select name="tmpl">';
						foreach($templates as $key=>$value)
						{
							echo "<option value=\"".$value."\">".$value."</option>\n\t\t";
						}
						echo "</select>";
					}
				?>
				</td>
			</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2"><h3>Administrator configuration</h3></td>
	</tr>
	<tr>
		<td class="item-desc">
			<p>Please set your admin username. It will be used for loggin to your admin panel.</p>
			<p>You should input admin password. Make sure your entered passwords match each other.</p>
			<p>Input your email. All the notifications will be sent from this email. It can be changed in your admin panel later.</p>
		</td>
		<td class="inner-content">
			<table>
			<tr>
				<td>Admin username:</td>
				<td align="center"><input type="text" name="admin_username" value="<?php echo isset($_POST['admin_username']) ? esynSanitize::html($_POST['admin_username']) : 'admin'; ?>" size="20" id="t4" /></td>
				<td><div class="err" id="err4">Please input correct admin username.</div></td>
			</tr>
			<tr>
				<td>Admin password:</td>
				<td align="center"><input type="password" name="admin_password" value="<?php echo isset($_POST['admin_password']) ? esynSanitize::html($_POST['admin_password']) : ''; ?>" size="20" id="t5" /></td>
				<td><div class="err" id="err5">Please input password.</div></td>
			</tr>
			<tr>
				<td>Admin password[confirm]:</td>
				<td align="center"><input type="password" name="admin_password2" value="" size="20" id="t6" /></td>
				<td><div class="err" id="err6">Entered passwords do not match.</div></td>
			</tr>
			<tr>
				<td>Admin e-mail:</td>
				<td align="center"><input type="text" name="admin_email" value="<?php echo isset($_POST['admin_email']) ? esynSanitize::html($_POST['admin_email']) : ''; ?>" size="20" id="t7" /></td>
				<td><div class="err" id="err7">Please input correct admin email.</div></td>
			</tr>
			</table>
		</td>
	</tr>
	</table>

	<div class="btn lgn">
		<button type="button" onclick="document.location.href='index.php?step=1';" name="back">Back</button>&nbsp;&nbsp;
		<button type="submit" name="next">Next</button>
	</div>
	</form>
<?php
	}
	else if ($step == '3')
	{
		// Auto create folders that must be writable for script.
		$folders = array();
//		$folders[] = ESYN_BASEDIR.'tmp'.ESYN_DS.'cache';
//		$folders[] = ESYN_BASEDIR.'tmp'.ESYN_DS.'smartycache';
//		$folders[] = ESYN_BASEDIR.'tmp'.ESYN_DS.'log';
//		$folders[] = ESYN_BASEDIR.'tmp'.ESYN_DS.'admin';
		umask(0);
		mkdir_r($folders);
?>
	<div class="inner-content"><a href="index.php">pre-installation check</a> &#187; <a href="index.php?step=1">license</a> &#187; <a href="index.php?step=2">configuration	</a> &#187; <b>completed</b></div>
	<h2 id="install">Installation Completed</h2>

	<table width="97%" cellpadding="0" cellspacing="0" style="margin: 0 10px 10px 10px;">
	<tr>
		<td colspan="2"><h3>Installation log:</h3></td>
	</tr>
	<tr>
		<td class="item-desc">Your config file has been created. You can log in your admin panel using the admin credentials you provided on the previous form and configure the software according to your needs.
		<p style="font-weight: bold;">Thank you for choosing eSyndiCat Free.</p></td>
		<td class="inner-content" style="vertical-align: top;">
			<table width="100%">
			<tr>
				<td class="elem">Database Installation</td>
				<td align="left"><span class="yes">OK</span></td>
			</tr>
			<tr>
				<td class="elem">Configuration File</td>
				<td align="left"><span class="yes">OK</span></td>
			</tr>	
			<tr>
				<td colspan="2"><p style="color: #F00; font-weight: bold; text-align: center;">Now you MUST completely remove 'install' directory from your server.</p></td>
			</tr>
			</table>
		</td>
		</tr>
		</table>

	<div class="btn lgn">
		<button type="button" onclick="document.location.href='../admin/';" name="next" tabindex="3">Next</button>
	</div>
<?php
}
else
{
	echo 'Incorrect step. Please follow installation instructions.';
}
esynPrintFooter();

/**
* Prints page header
*
* @return str
*/
function esynPrintHeader()
{
if (isset($_GET['step']) && $_GET['step'] == 1)
{
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
}
else
{
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "DTD/xhtml1-strict.dtd">';
}
?>
<html>

<head>
	<title>eSyndiCat Free <?php echo ESYN_VERSION?> - Web Installer</title>
	<meta http-equiv="Content-Type" content="text/html;charset=iso-8859-1" />
	<link rel="stylesheet" type="text/css" href="style.css" />
</head>

<body>

<div id="installation">
<?php
}

/**
* Prints page footer
*
* @return str
*/
function esynPrintFooter()
{
	global $err;

?>
</div>

<div id="copyright">
	Powered by <a href="http://www.esyndicat.com/">eSyndiCat Free</a> Version <?php echo ESYN_VERSION?><br />
	Copyright &copy; <?php echo date("Y")?> <a href="http://www.intelliants.com/">Intelliants LLC</a>

<p>
    <a href="http://validator.w3.org/check?uri=referer"><img
        src="img/valid-xhtml10.png"
        alt="Valid XHTML 1.0 Strict" height="31" width="88" /></a>
  </p>
</div>

<script type="text/javascript">

<?php
	if ($err)
	{
		$j = 0;
		foreach($err as $key=>$i)
		{
			if ($i > 0)
			{
				$first = ($j > 0) ? $i : '';
				echo "document.getElementById('err{$i}').style.display = 'block';\n";
				$i = (9 == $i) ? 8 : $i;
				echo "document.getElementById('t{$i}').style.background = '#FFD5D5';\n";
				$j++;
			}
		}
		echo "document.getElementById('t{$err[0]}').focus();\n";
	}
?>
</script>

</body>
</html>
<?php
}

/**
* Checks PHP settings
*
* @param str $aSetting setting name
*
* @return str
*/
function esynGetIniSetting($aSetting)
{
	$out = (ini_get($aSetting) == '1' ? 'ON' : 'OFF');

	return $out;
}

/**
* Prints results for permission checking
*
* @param str $aDir
* @param str $aMsg
*
* @return str
*/
function esynWritableCell($aDir, $aMsg='')
{
	echo '<tr>';
	echo '<td class="elem">'.$aDir .ESYN_DS.'</td>';
	echo '<td align="left">';
	echo is_writable(ESYN_BASEDIR.$aDir) ? '<span class="yes">Writeable</span>' : '<span class="no">Unwriteable</span> '.$aMsg;
	echo '</td>';
	echo '</tr>';
}

/**
* This functions checks all possible abilities to access remote pages (this is used to check reciprocal link, get page contents etc.., get pagerank)
*
* @return bool
*/
function hasAccessToRemote()
{
	if (ini_get('allow_url_fopen'))
	{
		if(function_exists("fsockopen"))
		{
			return true;
		}
		if(function_exists("stream_get_meta_data") && in_array("http", stream_get_wrappers()))
		{
			return true;
		}
	}

	if (extension_loaded('curl'))
	{
		return true;
	}
	
	return false;
}

/**
 * Create directory, if no such path - create all parent folders
 *
 * @param arr $aDirPath
 *
 */
function mkdir_r($aDirPath)
{
	if (!is_array($aDirPath))
	{
		$aDirPath = array($aDirPath);
	}
	foreach ($aDirPath AS $val)
	{
		if ( is_dir($val) ) continue;

		$path = explode(ESYN_DS, $val);
		$_tmp = false;
		if ('' == $path[0]) $path[0] = ESYN_DS; // for UNIX full path
		foreach ($path AS $dir)
		{
			$_tmp .= $dir;
			$_tmp .= (ESYN_DS == $dir) ? false : ESYN_DS;
			if (!is_dir($_tmp) && !mkdir($_tmp))
				continue 2;
		}
	}
}

/**
 * send_admin_email 
 * 
 * @access public
 * @return void
 */
function send_admin_email($email, $username, $password, $url)
{
	$email_template = "
Congratulations,
	
You have successfully installed eSyndiCat on your server.

This e-mail contains important information on your installation that you should keep safe. Later you can change credentials in admin panel.

Username: {username}
Password: {password}

Admin Panel URL: {url}admin/



Useful information on your eSyndiCat installation can be found on eSyndiCat.com's support page -
http://www.esyndicat.com/support/

-- 
Thanks,
eSyndiCat Support Team";

	$email_template = str_replace(array('{username}', '{password}', '{url}'), array($username, $password, $url), $email_template);

	mail($email, "eSyndiCat Script Installation Information", $email_template);
}

?>
