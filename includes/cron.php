<?php
//##copyright##

// configs, includes, authentication, authorization section
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'config.inc.php');

/** including common file classes **/
require_once(ESYN_CLASSES.'esynDatabase.php');
require_once(ESYN_CLASSES.'esynCacher.php');
require_once(ESYN_CLASSES.'esynMailer.php');
require_once(ESYN_CLASSES.'eSyndiCat.php');
require_once(ESYN_CLASSES.'esynConfig.php');

require_once(ESYN_INCLUDES.'util.php');

$eSyndiCat = new eSyndiCat();

$esynConfig = &esynConfig::instance();

define("ESYN_CACHING", false);
define("ESYN_IN_ADMIN", true);
define("ESYN_BROWSER_CRON_DEBUG", true);

/*
 * Checking-UP CRON
 *
 * Including:
 * 1. Checking for broken listings
 * 2. Checking for reciprocal listings
 * 3. Update pagerank of listings
 *
 */
if(ini_get('allow_url_fopen') || extension_loaded('curl'))
{
	if ($esynConfig->getConfig('cron_broken') || $esynConfig->getConfig('cron_recip') || $esynConfig->getConfig('cron_pagerank'))
	{
		$check_interval = ((int)$esynConfig->getConfig('cron_check_interval')) ? (int)$esynConfig->getConfig('cron_check_interval') : 7;
		$num_listings = ((int)$esynConfig->getConfig('cron_num_listings')) ? (int)$esynConfig->getConfig('cron_num_listings') : 10;

		if(ESYN_BROWSER_CRON_DEBUG)
		{
			echo '<strong>GETTING LISTINGS</strong>&nbsp;...&nbsp;';
		}

		// Get listings to check
		$eSyndiCat->setTable('listings');

		$where = "((`last_check_date` + INTERVAL {$check_interval} DAY < NOW()) ";
		$where .= "OR (`last_check_date` ='0000-00-00 00:00:00')) AND `cron_cycle`= '0' ";
		$where .= "ORDER BY `id` ASC LIMIT {$num_listings}";
		
		$listings = $eSyndiCat->all('`id`,`url`,`reciprocal`', $where);

		$eSyndiCat->resetTable();

		if(ESYN_BROWSER_CRON_DEBUG)
		{
			echo '<strong>DONE.</strong><br /><br />';
			echo '<strong>TOTAL LISTINGS</strong>:&nbsp;'.count($listings).'<br /><br />';
		}
		
		if(!empty($listings))
		{
			$updates= array();
			$count_all = 0;

			if ($esynConfig->getConfig('cron_report_job'))
			{
				if ($esynConfig->getConfig('cron_broken'))
				{
					$count_broken = 0;

					if ($esynConfig->getConfig('cron_report_job_extra'))
					{
						$wrng_broken = array();
					}
				}
				if ($esynConfig->getConfig('cron_recip'))
				{
					$count_reciprocal = 0;

					if ($esynConfig->getConfig('cron_report_job_extra'))
					{
						$wrng_reciprocal = array();
					}
				}
				if ($esynConfig->getConfig('cron_pagerank'))
				{
					$count_pagerank = 0;			

					if ($esynConfig->getConfig('cron_report_job_extra'))
					{
						$wrng_pagerank = array();
					}
				}
			}

			foreach ($listings as $listing)
			{
				if( empty($listing['url']) || 'http://' == $listing['url'] )
				{
					continue;
				}

				$count_all++;

				// PERFORM DEAD LINK CHECK
				if ($esynConfig->getConfig('cron_broken'))
				{
					if(ESYN_BROWSER_CRON_DEBUG)
					{
						echo 'Getting header of <i><a href="'.$listing['url'].'" target="_blank">'.$listing['url'].'</a></i>&nbsp;...&nbsp;';
					}

					$listing_header = 1;
					$headers = esynUtil::getPageHeaders(trim($listing['url']));
					if (!empty($headers))
					{
						$listing_header = (int)$headers['Status'];
					}

					if (!in_array((int)$listing_header, array (200, 403, 405),true))
					{
						if ($esynConfig->getConfig('cron_report_job'))
						{
							$count_broken++;		
							if ($esynConfig->getConfig('cron_report_job_extra'))
							{
								$wrng_broken[] = $listing['url'];
							}	
						}
					}

					if(ESYN_BROWSER_CRON_DEBUG)
					{
						echo '<strong>DONE.</strong><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
						echo '<strong>Header&nbsp;:&nbsp;</strong>'.$listing_header.'<br /><br />';
					}

					$updates['listing_header'] = $listing_header;
				}

				// PERFORM RECIPROCAL LINK CHECK
				if ($esynConfig->getConfig('cron_recip'))
				{
					$url = trim(strip_tags($listing['reciprocal']));
					$recip_valid = 0;
					if (esynValidator::isUrl($url))
					{
						do
						{
							$listing_header = 1;
							$headers = esynUtil::getPageHeaders($url);
							if (!empty($headers))
							{
								$listing_header = (int)$headers['Status'];
							}
							$redirect = in_array((int)$listing_header, array(301, 302), true);
							if ($redirect)
							{
								if (substr($headers['Location'], 0, 4) != 'http')
								{
									$parsed_url = parse_url($url);
									$url = $parsed_url['scheme'].'://'.$parsed_url['host'].$headers['Location'];
								}
								else
								{
									$url = $headers['Location'];
								}
							}
						}
						while ($redirect);
	
						if (in_array((int)$listing_header, array (200, 403, 405), true))
						{
							$content = esynUtil::getPageContent($url);
							if (!empty($content)) 
							{
								$recip_valid = esynValidator::hasUrl($content, $esynConfig->getConfig('reciprocal_text'));
							}
						}
					}
					if ( $esynConfig->getConfig('cron_report_job') && 0 == $recip_valid )
					{
						$count_reciprocal++;

						if ($esynConfig->getConfig('cron_report_job_extra'))
						{
							$wrng_reciprocal[] = $listing['url'];
						}	
					}													
					$updates['recip_valid'] = $recip_valid;
				}

				// PERFORM PAGERANK UPDATE
				if ($esynConfig->getConfig('cron_pagerank'))
				{
					if(ESYN_BROWSER_CRON_DEBUG)
					{
						echo 'Getting pagerank of <i><a href="'.$listing['url'].'" target="_blank">'.$listing['url'].'</a></i>&nbsp;...&nbsp;';
					}

					$pagerank = esynUtil::getPageRank($listing['url']);					
					$pagerank = $pagerank ? $pagerank : -1;

					if ($esynConfig->getConfig('cron_report_job') && -1 == $pagerank )
					{
						$count_pagerank++;

						if ($esynConfig->getConfig('cron_report_job_extra'))
						{							
							$wrng_pagerank[] = $listing['url'];
						}
					}
					
					$updates['pagerank'] = $pagerank;
					
					if(ESYN_BROWSER_CRON_DEBUG)
					{
						echo '<strong>DONE.</strong><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
						echo '<strong>Pagerank&nbsp;:&nbsp;</strong>'.$pagerank.'<br /><br />';
					}
				}
				$updates['cron_cycle'] = 1;

				$eSyndiCat->setTable('listings');
				$eSyndiCat->update($updates, "`id`='{$listing['id']}'", array('last_check_date'=>'NOW()'));
				$eSyndiCat->resetTable();
			}

			if ($esynConfig->getConfig('cron_report_job'))
			{
				$date_check = date("F j, Y, g:i a");
				$subject="Cron report [{$date_check}]";
				$body = $count_all." listings were checked \n=========================\n";

				if ($esynConfig->getConfig('cron_broken'))
				{
					$body .= "{$count_broken} broken listings \n";

					if ($esynConfig->getConfig('cron_report_job_extra') && $count_broken > 0)
					{
						$body .= "-->Warning these listings are Broken\n".implode("\n",$wrng_broken)."\n";				
					}
				}

				if ($esynConfig->getConfig('cron_recip'))
				{
					$body .= "{$count_reciprocal} non-reciprocal listings \n";			
			
					if ($esynConfig->getConfig('cron_report_job_extra') && $count_reciprocal > 0)
					{
						$body .= "-->Warning these listings fail Reciprocal Test\n".implode("\n",$wrng_reciprocal)."\n";				
					}
				}
				if ($esynConfig->getConfig('cron_pagerank'))
				{
					$body .= "{$count_pagerank} listings PageRank not updated\n";			
			
					if ($esynConfig->getConfig('cron_report_job_extra')  && $count_pagerank > 0)
					{
						$body .= "-->Warning these listings fail for Pagerank update\n".implode("\n",$wrng_pagerank)."\n";				
					}
				}
				mail($esynConfig->getConfig('site_email'), $subject, $body, 'From: '.$esynConfig->getConfig('site_email')."\r\n");

			}
		}

		$eSyndiCat->setTable('listings');
		$check_all = $eSyndiCat->all('`id`',"`cron_cycle`='0'");
		$eSyndiCat->resetTable();

		if ($esynConfig->getConfig('cron_report_cycle'))
		{
			if (empty($check_all))
			{
				$eSyndiCat->setTable('listings');
				$listings = $eSyndiCat->all('`url`,`listing_header`,`recip_valid`,`pagerank`',"`cron_cycle`='1'");
				$eSyndiCat->resetTable();
				
				if (!empty($listings))
				{
					$count_all = 0;

					if ($esynConfig->getConfig('cron_broken'))
					{
						$count_broken = 0;
			
						if ($esynConfig->getConfig('cron_report_cycle_extra'))
						{
							$wrng_broken = array();
						}
					}
					if ($esynConfig->getConfig('cron_recip'))
					{
						$count_reciprocal = 0;
			
						if ($esynConfig->getConfig('cron_report_cycle_extra'))
						{
							$wrng_reciprocal = array();
						}
					}
					if ($esynConfig->getConfig('cron_pagerank'))
					{
						$count_pagerank = 0;			
			
						if ($esynConfig->getConfig('cron_report_cycle_extra'))
						{
							$wrng_pagerank = array();
						}
					}			
			
					foreach ($listings as $listing)
					{
						if( empty($listing['url']) || 'http://' == $listing['url'] )
						{
							continue;
						}
						$count_all++;
						
						if (!in_array((int)$listing['listing_header'], array (200, 403, 405),true))						
						{
							$count_broken++;

							if ($esynConfig->getConfig('cron_report_cycle_extra'))
							{			
								$wrng_broken[] = $listing['url'];
							}
						}

						if (0 == $listing['recip_valid'])
						{
							$count_reciprocal++;

							if ($esynConfig->getConfig('cron_report_cycle_extra'))
							{
								$wrng_reciprocal[] = $listing['url'];
							}
						}
						
						if (-1 == $listing['pagerank'])
						{
							$count_pagerank++;

							if ($esynConfig->getConfig('cron_report_cycle_extra'))
							{
								$wrng_pagerank[] = $listing['url'];
							}
						}			
					}

					$date_check = $date_check ? $date_check : date("F j, Y, g:i a");

					$subject="Cron cycle finished  [{$date_check}]";
					$body = $count_all." listings checked \n=========================\n";

					if ($esynConfig->getConfig('cron_broken'))
					{
						$body .= "{$count_broken} broken listings \n";			

						if ($esynConfig->getConfig('cron_report_cycle_extra') && $count_broken > 0)
						{
							$body .= "-->Warning these listings are Broken\n".implode("\n",$wrng_broken)."\n";				
						}
					}

					if ($esynConfig->getConfig('cron_recip'))
					{
						$body .= "{$count_reciprocal} non-reciprocal listings \n";			
			
						if ($esynConfig->getConfig('cron_report_cycle_extra') && $count_reciprocal > 0)
						{
							$body .= "-->Warning these listings fail Reciprocal Test\n".implode("\n",$wrng_reciprocal)."\n";				
						}
					}

					if ($esynConfig->getConfig('cron_pagerank'))
					{
						$body .= "{$count_pagerank} listings PageRank not updated\n";			
			
						if ($esynConfig->getConfig('cron_report_cycle_extra') && $count_pagerank > 0)
						{
							$body .= "-->Warning these listings fail for Pagerank update\n".implode("\n",$wrng_pagerank)."\n";				
						}
					}

					mail($esynConfig->getConfig('site_email'), $subject, $body, 'From: '.$esynConfig->getConfig('site_email')."\r\n");
				}
			}
		}
		if (empty($check_all))
		{
			$eSyndiCat->setTable('listings');
			$eSyndiCat->update(array('cron_cycle'=>'0'));
			$eSyndiCat->resetTable();
		}
	}
}

/*
 * BackUP CRON
 *
 */
if ( $esynConfig->getConfig('cron_backup') )
{
	$dirname = ESYN_HOME.$esynConfig->getConfig('backup');
	$cron_backup_last_time = (int)$esynConfig->getConfig('cron_backup_last_time');
	$cron_backup_interval = (int)$esynConfig->getConfig('cron_backup_interval');
	$cron_backup_interval = 0 == $cron_backup_interval ? 30 : $cron_backup_interval;
	
	$last_check = $cron_backup_last_time + ($cron_backup_interval*24*60*60) - time();

	if ( $last_check < 0 && is_writable($dirname) )
	{
		$alltables = array();
		$sql = "SHOW TABLES";
		$r = $eSyndiCat->query($sql);

		if ($eSyndiCat->getNumRows($r) > 0)
		{
			while ($row = mysql_fetch_row($r))
			{
				$alltables[] = $row[0];
			}
		}

		for($i=0;$i<count($alltables);$i++)
		{
			$search = strstr($alltables[$i], ESYN_DBPREFIX);
			if ($search)
			{
				$tables[]=$search;
			}
		}

		$out = "# MySQL COMMON INFORMATION:\n";
		$out .= "#  MySQL CLIENT INFO: ".mysql_get_client_info()."\n";
		$out .= "#  MySQL HOST INFO: ".mysql_get_host_info()."\n";
		$out .= "#  MySQL PROTOCOL VERSION: ".mysql_get_proto_info()."\n";
		$out .= "#  MySQL SERVER VERSION: ".mysql_get_server_info()."\n\n";
		$out .= "#  __MySQL DUMP GENERATED BY ESYNDICAT__ #"."\n";
		$out .= "\n\n";

		$out .= "CREATE DATABASE `".ESYN_DBNAME."`;\n\n";

		foreach($tables as $table)
		{
			$out1 = "DROP TABLE `{$table}`;\n";
			$out1 .= "CREATE TABLE `{$table}` (\n";
			$sql = "SHOW FIELDS FROM `{$table}`";

			$fields = $eSyndiCat->getAll($sql);

			$out3 = array();

			foreach($fields as $fkey=>$fvalue)
			{
				$out2 = " `{$fvalue['Field']}` {$fvalue['Type']}";
				if ($fvalue['Null'] != "YES")
				{
					$out2 .= " NOT NULL";
				}
				if ($fvalue['Default'] != "")
				{
					$out2 .= " DEFAULT '{$fvalue['Default']}'";
				}
				if ($fvalue['Extra'] != "")
				{
					$out2 .= " {$fvalue['Extra']}";
				}
				$out3[] = $out2;
			}
			$out1.= implode(",\n",$out3);

			unset($out3);

			$sql = "SHOW KEYS FROM `{$table}` ";

			$keys = $eSyndiCat->getAll($sql);

			if(!empty($keys))
			{
				foreach($keys as $kkey=>$kvalue)
				{
					$kname=$kvalue['Key_name'];
					if (($kname != "PRIMARY") && ($kvalue['Non_unique'] == 0))
					{
						$kname="UNIQUE|$kname";
					}
					if (!isset($index[$kname]))
					{
						$index[$kname] = array();
					}
					$index[$kname][] = $kvalue['Column_name'];
				}
				   
				while(list($x, $columns) = @each($index))
				{
					$out1 .= ",\n";
					if ($x == "PRIMARY")
					{
						$out1 .= "   PRIMARY KEY (" . implode(", ", $columns) . ")";
					}
					elseif (substr($x,0,6) == "UNIQUE")
					{
						$out1 .= "   UNIQUE ".substr($x,7)." (" . implode(", ",$columns) . ")";
					}
					else
					{
						$out1 .= "   KEY $x (" . implode(", ",$columns) . ")";
					}
				}	
			}
			$out1 .= "\n);";

			$out .= stripslashes($out1);			
			
			$out1 = "\n\n";
			$sql = "SHOW FIELDS FROM `{$table}`";

			$fields = $eSyndiCat->getAll($sql);
			$out3 = array();
		
			foreach($fields as $fvalue)
			{
				$out3[] = "`{$fvalue['Field']}`";
			}

			$complete = ' ('.implode(",",$out3).')';
			unset($out3);

			$sql = "SELECT * FROM `{$table}`";

			$data = $eSyndiCat->getAll($sql);
		
			if (!empty($data))
			{	
				$out3 = array();
				$out4 = array();				

				$out1 .= "INSERT INTO `{$table}`{$complete} VALUES \n";
				
				foreach($data as $dvalue)
				{
					foreach($dvalue as $key2=>$value2)
					{
						if (!isset($dvalue[$key2]))
						{
							$out3[] = "NULL";
						}
						elseif ($dvalue[$key2] != "")
						{
							$out3[] = "'".addslashes($dvalue[$key2])."'";
						}
						else
						{
							$out3[] = "''";
						}
					}
					$out4[] = "(".implode(",",$out3).")";
					$out3= array();					
				}
				$out1 .= implode(",\n",$out4).";\n";
				unset($out4);
			}
			$out .= $out1;			
			$out .= "\n\n";
		}

		$backupfile = $dirname.'db-'.date("Y-m-d").'.sql' ;

		if ($esynConfig->getConfig('cron_backup_archive') && extension_loaded('zlib'))
		{
			$backupfile .= '.gz' ;
			
			$zp = gzopen($backupfile, "w9");
			$update_backup = gzwrite($zp, $out);
			gzclose($zp);
		}
		else
		{
			$fd = fopen($backupfile, 'w');
			$update_backup = fwrite($fd,$out);
			fclose($fd);
		}

		if ($update_backup)
		{
			$esynConfig->setConfig('cron_backup_last_time',time(),true);
		}
	}
}

if($esynConfig->getConfig('expiration_period') > 0)
{
	$eSyndiCat->factory("Listing", "Plan");

	$plans = $esynPlan->one("COUNT(*)", "`status` = 'active'");

	if(0 == $plans)
	{
		$sql = "SELECT `link`.`id`, `link`.`url`, `link`.`title`, `link`.`description`, `link`.`email`, ";
		$sql .= "`cat`.`path`, `cat`.`id` `category_id`, `link`.`expire` `days`, `link`.`action_expire` `action_expire`, ";
		$sql .= "`link`.`date` `start_date`, ";
		$sql .= "`link`.`date` + INTERVAL `link`.`expire` DAY `end_date` ";
		$sql .= "FROM `".ESYN_DBPREFIX."listings` `link` ";
		$sql .= "LEFT JOIN `".ESYN_DBPREFIX."categories` `cat` ";
		$sql .= "ON `link`.`category_id` = `cat`.`id` ";
		$sql .= "WHERE `link`.`date` + INTERVAL `link`.`expire` DAY < NOW() ";
		$sql .= "AND `link`.`expire` <> 0";

		$listings = $esynListing->getAll($sql);

		if(!empty($listings))
		{
			foreach($listings as $listing)
			{
				$action_expire = !empty($listing['action_expire']) ? $listing['action_expire'] : $esynConfig->getConfig('expiration_action');

				if(!empty($action_expire))
				{
					if(in_array($action_expire, array('approval', 'banned', 'suspended')))
					{
						$esynListing->update(array('status' => $action_expire), "`id` = '{$listing['id']}'");
					}
					elseif(in_array($action_expire, array('regular', 'featured', 'partner')))
					{
						if('regular' == $action_expire)
						{
							$fields = array(
								'sponsored' => '0',
								'partner'	=> '0',
								'featured'	=> '0',
								'plan_id'	=> '0'
							);

							$esynListing->update($fields, "`id` = '{$listing['id']}'");
						}
						else
						{
							$esynListing->update(array($action_expire => '1'), "`id` = '{$listing['id']}'");
						}
					}
					elseif('remove' == $action_expire)
					{
						$esynListing->delete("`id` = '{$listing['id']}'");
					}
				}
			}
		}
	}
}

/*
 * Sponsored CRON
 *
 */
if ($esynConfig->getConfig('sponsored_listings'))
{
	$eSyndiCat->setTable("plans");
	$plans = $eSyndiCat->all("*", "`status` = 'active'");
	$eSyndiCat->resetTable();

	$eSyndiCat->factory("Listing");

	if($plans)
	{
		foreach($plans as $key => $plan)
		{
			$type_date = (in_array($plan['mark_as'], array('sponsored', 'featured', 'partner'))) ? $plan['mark_as'].'_start' : 'date';
			$type_where = (in_array($plan['mark_as'], array('sponsored', 'featured', 'partner'))) ? "AND `link`.`{$plan['mark_as']}` = '1'" : '';

			// Send expire emails
			//
			if(!empty($plan['email_expire']))
			{
				$days = explode(',', $plan['email_expire']);

				if($days)
				{
					foreach($days as $day)
					{
						$day = (int)$day;

						$query = "SELECT `link`.`id`, `link`.`url`, `link`.`title`, `link`.`description`, `link`.`email`, ";
						$query .= "`cat`.`path`, `cat`.`id` `category_id`, `plan`.`cost` `plan_cost`, `plan`.`title` `plan_name`, `plan`.`action_expire`, {$day} `days`, ";
						$query .= "`link`.`{$type_date}` `start_date`, ";
						$query .= "`link`.`{$type_date}` + INTERVAL `plan`.`period` DAY `end_date` ";
						$query .= "FROM `".ESYN_DBPREFIX."listings` `link` ";
						$query .= "LEFT JOIN `".ESYN_DBPREFIX."plans` `plan` ";
						$query .= "ON `link`.`plan_id` = `plan`.`id` ";
						$query .= "LEFT JOIN `".ESYN_DBPREFIX."categories` `cat` ";
						$query .= "ON `link`.`category_id` = `cat`.`id` ";
						$query .= "WHERE TO_DAYS(`link`.`{$type_date}` + INTERVAL `plan`.`period` DAY) - TO_DAYS(NOW()) = '{$day}' ";
						$query .= "{$type_where} ";
						$query .= "AND `plan`.`id` = '{$plan['id']}'";

						$listings = $esynListing->getAll($query);
						
						if(!empty($listings))
						{
							$ids = array();			

							$mailer = new esynMailer;

							foreach ($listings as $listing)
							{
								$mailer->sendExpirationNotif($listing);
							}
						}
					}
				}
			}

			// Change listing after expire time
			//
			if(!empty($plan['action_expire']))
			{
				$query = "SELECT `link`.`id`, `link`.`url` ";
				$query .= "FROM `".ESYN_DBPREFIX."listings` `link` ";
				$query .= "LEFT JOIN `".ESYN_DBPREFIX."plans` `plan` ";
				$query .= "ON `link`.`plan_id` = `plan`.`id` ";
				$query .= "WHERE `link`.`{$type_date}` + INTERVAL `plan`.`period` DAY < NOW() ";
				$query .= "{$type_where} ";
				$query .= "AND `link`.`plan_id` = '{$plan['id']}' ";
				$query .= "ORDER BY `link`.`id` ASC";

				$listings = $esynListing->getAll($query);

				if(!empty($listings))
				{
					foreach($listings as $listing)
					{
						if(in_array($plan['action_expire'], array('approval', 'banned', 'suspended')))
						{
							$esynListing->updateStatus($listing['id'], $plan['action_expire']);
						}
						elseif(in_array($plan['action_expire'], array('regular', 'featured', 'partner')))
						{
							if('regular' == $plan['action_expire'])
							{
								$fields = array(
									'sponsored' => '0',
									'partner' => '0',
									'featured' => '0',
									'plan_id' => '0'
								);

								$esynListing->update($fields, "`id` = '{$listing['id']}'");
							}
							else
							{
								$esynListing->update(array($plan['action_expire'] => '1'), "`id` = '{$listing['id']}'");
							}
						}
						elseif('remove' == $plan['action_expire'])
						{
							$esynListing->delete("`id` = '{$listing['id']}'");
						}
					}
				}
			}
		}
	}
}

// Executing cron tasks from DB
$eSyndiCat->startHook("cronjob");

?>
