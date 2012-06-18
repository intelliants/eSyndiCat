<?php
/*
 * Checking-UP CRON
 *
 * Including:
 * 1. Checking for broken listings
 * 2. Checking for reciprocal listings
 * 3. Update pagerank of listings
 *
 */

global $esynConfig;

if(ini_get('allow_url_fopen') || extension_loaded('curl'))
{
	if ($esynConfig->getConfig('cron_broken') || $esynConfig->getConfig('cron_recip') || $esynConfig->getConfig('cron_pagerank'))
	{
		$check_interval = ((int)$esynConfig->getConfig('cron_check_interval')) ? (int)$esynConfig->getConfig('cron_check_interval') : 7;
		$num_listings = ((int)$esynConfig->getConfig('cron_num_listings')) ? (int)$esynConfig->getConfig('cron_num_listings') : 10;

		// Get listings to check
		$eSyndiCat->setTable('listings');

		$where = "((`last_check_date` + INTERVAL {$check_interval} DAY < NOW()) ";
		$where .= "OR (`last_check_date` ='0000-00-00 00:00:00')) AND `cron_cycle`= '0' ";
		$where .= "ORDER BY `id` ASC LIMIT {$num_listings}";

		$listings = $eSyndiCat->all('`id`,`url`,`reciprocal`', $where);
		$eSyndiCat->resetTable();

		if(!empty($listings))
		{
			$updates= array();
			$count_all = 0;

			$count_broken = $count_reciprocal = $count_pagerank = 0;
			$wrng_broken = $wrng_reciprocal = $wrng_pagerank = array();

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
					$listing_header = 1;
					$headers = esynUtil::getPageHeaders(trim($listing['url']));
					if (!empty($headers['Status']))
					{
						$listing_header = (int)$headers['Status'];
					}

					if (!in_array($listing_header, array(200, 403, 405)))
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
				}
				$updates['cron_cycle'] = 1;

				$eSyndiCat->setTable('listings');
				$eSyndiCat->update($updates, "`id`='{$listing['id']}'", false, array('last_check_date'=>'NOW()'));
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
		$check_all = $eSyndiCat->one('COuNT(`id`)',"`cron_cycle`='0'");
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

					$count_broken = 0;
					$wrng_broken = array();
					$count_reciprocal = 0;
					$wrng_reciprocal = array();
					$count_pagerank = 0;
					$wrng_pagerank = array();

					foreach ($listings as $listing)
					{
						if( empty($listing['url']) || 'http://' == $listing['url'] )
						{
							continue;
						}
						$count_all++;

						if (!in_array($listing['listing_header'], array (200, 403, 405)))						
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
