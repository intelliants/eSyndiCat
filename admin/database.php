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


define("ESYN_REALM", "database");

esynUtil::checkAccess();

if(empty($_GET['page']))
{
	$_GET['page'] = "sql";
}

/*
 * Reset database options
 */
$reset_options = array(
	'categories'	=> $esynI18N['reset'].' '.$esynI18N['categories'],
	'listings'		=> $esynI18N['reset'].' '.$esynI18N['listings'],
	'accounts'		=> $esynI18N['reset'].' '.$esynI18N['accounts']
);

$tables = $esynAdmin->getTables();

$error = false;
$msg = '';

/*
 * ACTIONS
 */
if(isset($_GET['type']) && $_GET['page'] == 'consistency')
{
	$esynAdmin->factory("DbControl");

	if($_GET['type'] == 'optimize_tables')
	{
		$query = "OPTIMIZE TABLE ";
		foreach($tables as $t)
		{
			$query .= "`".$t."`,";
		}
		$query = rtrim($query, ",");
		$esynAdmin->query($query);
	
		esynMessages::setMessage($esynI18N['done'], false);
		esynUtil::reload(array("type"=>null));
	}
	elseif($_GET['type'] == 'num_all_listings')
	{
		// map: PARENT => (id)
		$parents = array();

		@set_time_limit(100);

		$esynAdmin->setTable("categories");
		$categories = $esynAdmin->all("`id`,`parent_id`");
		$esynAdmin->resetTable();

		if(count($categories) > 0)
		{
			$esynAdmin->setTable("listings");
			$listings = $esynAdmin->keyvalue("`category_id`, count(*)", "`status`='active' GROUP BY `category_id`");
			$listings[0] = $esynAdmin->one("count(*)", "`category_id`='0' AND `status`='active'");
			$esynAdmin->resetTable();

			if(!is_array($listings))
			{
				$listings = array();
			}

			foreach($categories as $cat)
			{
				$id = $cat['id'];
				$parent_id = $cat['parent_id'];

				// (if unset, that means that there is no real listings in the category but threre might  be cross links)
				if(!isset($listings[$id]))
				{
					$listings[$id] = 0;
				}

				$esynAdmin->setTable("listing_categories");
				
				$sql = "SELECT COUNT(l.`id`) FROM `{$esynAdmin->mPrefix}listings` l ";
				$sql .= "INNER JOIN `{$esynAdmin->mPrefix}listing_categories` lc ON lc.`listing_id` = l.`id` ";
				$sql .= "WHERE l.`status` = 'active' AND lc.`category_id` = '{$id}'";
				
				$x = $esynAdmin->getOne($sql);
				
				$listings[$id] += (int)$x;
				
				$esynAdmin->resetTable();

				$parents[$parent_id][] = $id;
			}
		}

		$num_all_listings_map = array();
		unset($parents['-1']);

		$called = array();

		$total = 0;
		foreach(array_keys($parents) as $parent)
		{
			$esynDbControl->recalculateListingsCount($parent);
		}

		$empty = array();

		$esynAdmin->setTable("categories");
		$all = 0;

		foreach($listings as $id => $num)
		{
			$all = isset($num_all_listings_map[$id]) ? $num_all_listings_map[$id] : $num;
			
			if($all == 0 && $num == 0)
			{
				$empty[] = $id;
			}
			else
			{
				$esynAdmin->update(array("num_all_listings" => $all, "num_listings" => $num), "`id`='".$id."'");
			}
		}

		if(!empty($empty))
		{
			$esynAdmin->update(array("num_all_listings" => 0, "num_listings" => 0), "`id` IN('".join("','", $empty)."')");
		}

		$esynAdmin->resetTable();

		$esynAdmin->mCacher->clearAll('categories');

		esynMessages::setMessage($esynI18N['changes_saved'], false);
		esynUtil::reload(array("type" => null));
	}
	elseif($_GET['type'] == 'categories_relation')
	{
		// convert flat
		$esynAdmin->setTable("categories");
		$cats = $esynAdmin->all("`id`, `parent_id`", "`id` > 0");
		$esynAdmin->resetTable();

		$esynAdmin->setTable("flat_structure");
		$esynAdmin->cleanTable();

		@set_time_limit(100);

		if ($cats)
		{
			foreach ($cats as $key => $value)
			{
				$esynAdmin->insert(array("parent_id"=>$value['id'],"category_id"=>$value['id']));
				$esynDbControl->addFlat($value['id'], $value['parent_id']);
			}
		}

		$esynAdmin->resetTable();
			
		esynMessages::setMessage($esynI18N['changes_saved']);
		esynUtil::reload(array("type"=>null));
	}
	elseif($_GET['type'] == 'listing_categories')
	{
		// get listings
		$esynAdmin->setTable("listings");
		$listings = $esynAdmin->keyvalue("`id`,`url`");
		$esynAdmin->resetTable();

		$esynAdmin->setTable("listing_categories");
		$lc = $esynAdmin->all("`listing_id`");
		$esynAdmin->resetTable();

		$to_delete = '';
		$cnt =0;
		if($lc)
		{
			foreach ($lc as $i)
			{
				if(empty($listings[$i['listing_id']]))
				{
					$to_delete .= "'".$i['listing_id']."', ";
					$cnt++;
				}
			}
		}
		if($to_delete)
		{
			$to_delete = substr($to_delete, 0, strlen($to_delete)-2);
			$esynAdmin->setTable("listing_categories");
			$delete_it = $esynAdmin->delete("`listing_id` IN (".$to_delete.")");
			$esynAdmin->resetTable();

			if($delete_it)
			{
				esynMessages::setMessage(str_replace("{num}", $cnt, $esynI18N['orphan_listings_deleted']));
				esynUtil::reload(array("type"=>null));
			}
		}
		else
		{
			esynMessages::setMessage($esynI18N['no_orphan_listings']);
			esynUtil::reload(array("type"=>null));
		}

		// get categories
		$esynAdmin->setTable("categories");
		$cats = $esynAdmin->keyvalue("`id`,`title`");
		$esynAdmin->resetTable();

		$esynAdmin->setTable("listing_categories");
		$lc = $esynAdmin->keyvalue("`category_id`,`listing_id`", " 1 GROUP BY `category_id`");
		$esynAdmin->resetTable();

		$to_delete = '';
		$cnt =0;

		foreach ($lc as $id=>$value)
		{
			if(empty($cats[$id]))
			{
				$to_delete .= "'{$id}', ";
				$cnt++;
			}
		}

		if($to_delete)
		{
			$to_delete = substr($to_delete,0,strlen($to_delete)-2);
				
			$esynAdmin->setTable("listing_categories");
			$delete_it = $esynAdmin->delete('`category_id` IN ('.$to_delete.')');
			$esynAdmin->resetTable();

			if($delete_it)
			{
				esynMessages::setMessage(str_replace("{num}", $cnt, $esynI18N['orphan_categories_deleted']));
				esynUtil::reload(array("type"=>null));
			}
		}
		else
		{
			esynMessages::setMessage($esynI18N['no_orphan_categories']);
			esynUtil::reload(array("type"=>null));
		}
	}
	elseif($_GET['type'] == 'repair_tables')
	{
		$query = "REPAIR TABLE ";
		foreach($tables as $t)
		{
			$query .= "`".$t."`,";
		}
		$query = rtrim($query, ",");
		$esynAdmin->query($query);
	
		esynMessages::setMessage($esynI18N['done']);
		esynUtil::reload(array("page"=>"consistency", "type"=>null));
	}

	$esynAdmin->startHook('phpAdminDatabaseConsistencyType');
	
}

/** update sql database **/
if (isset($_POST['run_update']))
{
	if ($_FILES)
	{
		$filename = $_FILES['sql_file']['tmp_name'];
	}
	else
	{
		if(is_scalar($_POST['sqlfile']))
		{
			$_POST['sqlfile'] = str_replace(array("`","~","/","\\"), "", $_POST['sqlfile']);
		}
		else
		{
			$_POST['sqlfile'][0] = str_replace(array("`","~","/","\\"), "", $_POST['sqlfile'][0]);
		}

		$filename = ESYN_HOME.'updates'.ESYN_DS.$_POST['sqlfile'].'.sql';
	}

	if (!($f = fopen ($filename, "r" )))
	{
		$error = true;
		$msg = str_replace("{filename}", $filename, $esynI18N['cant_open_sql']);
	}
	else
	{
		$sql_file_content = file($filename);

		if (!empty($sql_file_content))
		{
			$sql = array_map(array($esynAdmin, 'strip_sql_comment'), $sql_file_content);

			$sql = trim(implode('', $sql));

			$sql_array = $esynAdmin->split_sql($sql);

			foreach($sql_array as $sql)
			{
				$sql = str_replace("{prefix}", $esynAdmin->mPrefix, $sql);

				$esynAdmin->query($sql);
			}
		}

		$msg = $esynI18N['upgrade_completed'];

		$esynAdmin->mCacher->clearAll();
	}

	esynMessages::setMessage($msg, $error);
}

/** run query **/
if(isset($_POST['exec_query']))
{
	$esynAdmin->startHook('adminRunSqlQuery');

	if(!defined('ESYN_NOUTF'))
	{
		require_once(ESYN_CLASSES.'esynUtf8.php');

		esynUtf8::loadUTF8Core();
		esynUtf8::loadUTF8Util('ascii', 'validation', 'bad', 'utf8_to_ascii');
	}

	$queryOut = '';
	$error = false;
	
	$sql_query = $_POST['query'];

	if(!utf8_is_valid($sql_query))
	{
		$sql_query = utf8_bad_replace($sql_query);
	}

	$select = false;

	$res = mysql_query(str_replace("{prefix}",$esynAdmin->mPrefix, $sql_query));

	$numrows = 0;
	
	if($res)
	{
		$numrows = $rows = $esynAdmin->getNumRows($res);
		
		if ($rows)
		{
			$rows .= ($rows > 1) ? ' rows' : ' row';
			$msg = "<b>Query OK:</b> {$rows} selected.";
		}
		else
		{
			$msg = '<b>Query OK:</b> '.$esynAdmin->getAffected().' rows affected.';
		}
	}
	else
	{
		$error = true;
		$msg = '<b>Query Failed:</b><br />'.mysql_error();
	}

	if($numrows)
	{
		$queryOut .= '<table cellspacing="0" cellpadding="0" class="common">';
		$queryOut .= '<tr>';
		
		$nf = mysql_num_fields($res);
		
		for($i = 0; $i < $nf; $i++)
		{
			$class = (0 == $i) ? 'class="first"' : '';
			$queryOut .= '<th '.$class.'>'.mysql_field_name($res, $i).'</th>';
		}
		unset($nf);
		
		$queryOut .= '</tr>';

		while($row = mysql_fetch_row($res))
		{
			$queryOut .= "<tr>";
			for($i = 0; $i < mysql_num_fields($res); $i++)
			{
				$class = (0 == $i) ? 'class="first"' : '';
				$queryOut .= '<td '.$class.'>'.esynSanitize::html($row[$i]).'</td>';
			}
			$queryOut .= '</tr>';
		}
		$queryOut .= '</table>';
	}

	esynMessages::setMessage($msg, $error);
}

/** export **/
if (isset($_POST['export']))
{
	$esynAdmin->factory("DbControl");

	if(!isset($_POST['tbl']) || empty($_POST['tbl']))
	{
		$error = true;
		$msg[] = $esynI18N['export_tables_incorrect'];
	}

	if(!$error)
	{
		$sql = '';

		$out = "\n";
		$out .= "#  MySQL COMMON INFORMATION:\n";
		$out .= "#  MySQL CLIENT INFO: ".mysql_get_client_info()."\n";
		$out .= "#  MySQL HOST INFO: ".mysql_get_host_info()."\n";
		$out .= "#  MySQL PROTOCOL VERSION: ".mysql_get_proto_info()."\n";
		$out .= "#  MySQL SERVER VERSION: ".mysql_get_server_info()."\n\n";
		$out .= "#  __MySQL DUMP GENERATED BY ESYNDICAT__ #"."\n";
		$out .= "\n\n";
		$out .= 'SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";' . "\n\n";

		$drop = isset($_POST['drop']) ? $_POST['drop'] : 0;
		$showcolumns = isset($_POST['showcolumns']) ? $_POST['showcolumns'] : 0;
		$real_prefix = isset($_POST['real_prefix']) ? $_POST['real_prefix'] : 0;

		if (isset($_POST['sql_structure']) && empty($_POST['sql_data']))
		{
			if (!empty($_POST['tbl']) && is_array($_POST['tbl']))
			{
				foreach($_POST['tbl'] as $value)
				{
					$sql .= $esynDbControl->makeStructureBackup($value, $drop, $real_prefix);
				}
			}
			else
			{
				$sql = $esynDbControl->makeDbStructureBackup($drop, $real_prefix);
			}
		}
		elseif (isset($_POST['sql_data']) && empty($_POST['sql_structure']))
		{
			if (!empty($_POST['tbl']) && is_array($_POST['tbl']))
			{
				foreach($_POST['tbl'] as $value)
				{
					$sql .= $esynDbControl->makeDataBackup($value, $showcolumns, $real_prefix);
				}
			}
			else
			{
				$sql = $esynDbControl->makeDbDataBackup($showcolumns, $real_prefix);
			}
		}
		elseif (isset($_POST['sql_structure']) && isset($_POST['sql_data']))
		{
			if (!empty($_POST['tbl']) && is_array($_POST['tbl']))
			{
				foreach($_POST['tbl'] as $value)
				{
					$sql .= $esynDbControl->makeFullBackup($value, $drop, $showcolumns, $real_prefix);
				}
			}
			else
			{
				$sql = $esynDbControl->makeDbBackup($drop, $showcolumns, $real_prefix);
			}
		}
		$sql = $out.$sql;

		if (isset($_POST['save_file']))
		{
			$sqlfile = ESYN_HOME.$esynConfig->getConfig('backup');

			/** saves to server **/
			if ('server' == $_POST['savetype'])
			{
				array_walk_recursive($_POST['tbl'], array("esynUtil", 'filenameEscape'));
				$sqlfile .= !empty($_POST['tbl']) ? date("Y-m-d").'-'.$_POST['tbl'][0].'.sql' : 'db-'.date("Y-m-d").'.sql' ;
				if (!$fd = @fopen($sqlfile, 'w'))
				{
					@chmod($sqlfile, 0775);
					$error = true;
					$msg = str_replace("{filename}", $sqlfile, $esynI18N['cant_open_sql']);
				}
				elseif (fwrite($fd,$sql) === FALSE)
				{
					$error = true;
					$msg = str_replace("{filename}", $sqlfile, $esynI18N['cant_write_sql']);

					fclose($fd);
				}
				else
				{
					$tbls = '';
					if(!empty($_POST['tbl']))
					{
						$tbls = implode(", ",$_POST['tbl']);
					}
					$msg = str_replace("{table}", $tbls, $esynI18N['table_dumped']);
					$msg = str_replace("{filename}", $sqlfile, $msg);

					fclose($fd);
				}
			}
			/** saves to computer **/
			elseif ('client' == $_POST['savetype'])
			{
				$sqlfile = ($_POST['tbl']) ? date('Y-m-d').'-'.$_POST['tbl'][0].'.sql' : 'db_'.date('Y-m-d').'.sql';
				if (function_exists('gzencode') && isset($_POST['gzip_compress']) && $_POST['gzip_compress'])
				{
					$sql = gzencode($sql);
					$sqlfile .= '.gz';

					header('Content-Type: application/x-gzip');
					header('Content-Encoding: gzip');
				}
				else
				{
					header('Content-Type: text/plain');
				}

				header('Content-Disposition: attachment; filename="'.$sqlfile.'"');

				echo $sql;
				exit;
			}
		}
		else
		{
			/** show on the screen **/
			$out_sql = $sql;
		}
	}

	esynMessages::setMessage($msg, $error);
}

/*
 * Resetting tables
 */
if(isset($_POST['reset']))
{
	if(empty($_POST['options']))
	{
		$error = true;
		$msg[] = $esynI18N['reset_choose_table'];
	}

	if(!$error)
	{
		$esynAdmin->factory("DBManagement");

		foreach($_POST['options'] as $option)
		{
			$esynDBManagement->reset($option);
		}

		$msg[] = $esynI18N['reset_success'];

		$esynAdmin->mCacher->clearAll();
	}

	esynMessages::setMessage($msg, $error);
}

/*
 * AJAX
 */
if(isset($_GET['action']) && 'fields' == $_GET['action'] && !empty($_GET['table']))
{
	$esynAdmin->loadClass("JSON");

	$json = new Services_JSON();
	
	$fields = $esynAdmin->getFields($_GET['table']);
	
	$out = array();
	if($fields)
	{
		foreach($fields as $key => $value)
		{
			$out[] = $value['Field'];
		}
	}

	echo $json->encode($out);
	exit;
}

/*
 * ACTIONS
 */

$gNoBc = false;

$gBc[0]['title'] = $esynI18N['manage_database'];
$gBc[0]['url'] = 'controller.php?file=database&amp;page=sql';

if ($_GET['page'] == 'export')
{
	/** checks if file is writable **/
	$dirname = ESYN_HOME.$esynConfig->getConfig('backup');

	if (!is_writable($dirname))
	{
		$backup_is_not_writeable = str_replace('{dirname}', $dirname, $esynI18N['directory_not_writable']);
	}
	
	$gBc[1]['title'] = $esynI18N['export'];
}
elseif ('sql' == $_GET['page'])
{
	$gBc[1]['title'] = $esynI18N['sql_management'];
}
elseif ('import' == $_GET['page'])
{
	$gBc[1]['title'] = $esynI18N['import'];
}
elseif ('consistency' == $_GET['page'])
{
	$gBc[1]['title'] = $esynI18N['check_consistency'];
}
elseif ('reset' == $_GET['page'])
{
	$gBc[1]['title'] = $esynI18N['reset'];

	$notifications = array(
		'msg'	=> $esynI18N['reset_backup_alert'],
		'type'	=> 'alert'
	);
}

$gTitle = $gBc[1]['title'];

$actions = array(
	array("url" => "controller.php?file=database&amp;page=reset", "icon" => "reset_database.png", "label" => $esynI18N['reset']),
	array("url" => "controller.php?file=database&amp;page=consistency", "icon" => "tools.png", "label" => $esynI18N['check_consistency']),
	array("url" => "controller.php?file=database&amp;page=sql", "icon" => "view_database.png", "label" => $esynI18N['sql_management']),
	array("url" => "controller.php?file=database&amp;page=export", "icon" => "export.png", "label" => $esynI18N['export']),
	array("url" => "controller.php?file=database&amp;page=import", "icon" => "import.png", "label" => $esynI18N['import']),
);

require_once(ESYN_ADMIN_HOME.'view.php');

if(isset($_POST['exec_query']) && !empty($queryOut))
{
	$esynSmarty->assign('queryOut', $queryOut);
}

if(isset($sql_query))
{
	$esynSmarty->assign('sql_query', $sql_query);
}

/** gets sql files that exist in updates directory and converts files in array **/
$upgrades = array();

if(is_dir(ESYN_HOME."updates".ESYN_DS))
{
	$path = ESYN_HOME."updates".ESYN_DS;
	$files = scandir($path);
	foreach($files as $file)
	{
		if (substr($file,0,1) != ".")
		{
			if (is_file($path.$file))
			{
				$upgrades[] = substr($file, 0, count($file) - 5);
			}
		}
	}
}

if(isset($backup_is_not_writeable))
{
	$esynSmarty->assign('backup_is_not_writeable', $backup_is_not_writeable);
}

if(isset($out_sql))
{
	$esynSmarty->assign('out_sql', $out_sql);
}

if(isset($notifications))
{
	$esynSmarty->assign('notifications', $notifications);
}

if (isset($hooks))
{
	$esynSmarty->assign('hooks', $hooks);
}

$esynSmarty->assign('upgrades', $upgrades);
$esynSmarty->assign('tables', $tables);
$esynSmarty->assign('reset_options', $reset_options);

$esynSmarty->display('database.tpl');
