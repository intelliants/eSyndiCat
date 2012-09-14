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


class esynGYSMap extends esynAdmin
{
	
	var $mTable = '';
	
	var $out = '';
	
	var $path_to_file = '';
	
	function init ()
	{
		if (!is_dir($this->path_to_file))
		{
			if (!mkdir($this->path_to_file))
			{
				return 'cannot_create_folder';
			}
		}
		elseif (!is_writable($this->path_to_file))
		{
			if (chmod($this->path_to_file, 777));
			{
				return 'not_writable_folder';
			}
		}
		
		if (!is_dir($this->path_to_file."google".ESYN_DS))
		{
			if (!mkdir($this->path_to_file."google".ESYN_DS))
			{
				return 'cannot_create_folder';
			}
		}
		elseif (!is_writable($this->path_to_file."google".ESYN_DS))
		{
			if (chmod($this->path_to_file."google".ESYN_DS, 777));
			{
				return 'not_writable_folder';
			}
		}
		
		if (!is_dir($this->path_to_file."yahoo".ESYN_DS))
		{
			if (!mkdir($this->path_to_file."yahoo".ESYN_DS))
			{
				return 'cannot_create_folder';
			}
		}
		elseif (!is_writable($this->path_to_file."yahoo".ESYN_DS))
		{
			if (chmod($this->path_to_file."yahoo".ESYN_DS, 777));
			{
				return 'not_writable_folder';
			}
		}
	}
	
	function printListingUrl ($aListing)
	{
		if ($this->mConfig['mod_rewrite'])
		{
			$url = ESYN_URL;
			
			if (isset($aListing['path']) && !empty($aListing['path']))
			{
				$url .= $aListing['path'];
				$url .= ('/' == substr($aListing['path'], -1)) ? '' : '/';
			}
			
			if($this->mConfig['lowercase_urls'])
			{
				$aListing['title'] = strtolower($aListing['title']);
			}
			
			$url .= esynUtil::convertStr($aListing['title']);
			$url .= '-l'.$aListing['id'].'.html';
	
			return $url;
		}else{
			return ESYN_URL.'view-listing.php?id='.$aListing['id'];
		}
	}
	
	function getGoogleHeader ()
	{
		$header_google  = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
		$header_google .= '<urlset xmlns="http://www.google.com/schemas/sitemap/0.84">'."\n";
		$header_google .= '<url>';
		$header_google .= ' <loc><![CDATA['.ESYN_URL.']]></loc>';
		$header_google .= ' <changefreq>daily</changefreq>';
		$header_google .= ' <priority>0.9</priority>';
		$header_google .= '</url>'."\n";
		
		return $header_google;
	}

	function getGoogleFooter () 
	{
		return '</urlset>';
	}
	
	function getTotal()
	{
		$this->setTable('categories');
		$items['categories'] = $this->one("COUNT(`id`)", "`status`='active'");
		$this->resetTable();
		
		$sql = "SELECT COUNT(`link`.`id`) ";
		$sql .= "FROM `".$this->mPrefix."categories` AS `cat` ";
		$sql .= "LEFT JOIN `".$this->mPrefix."listings` AS `link` ";
		$sql .= "ON `link`.`category_id` = `cat`.`id` ";
		$sql .= "WHERE `cat`.`status` = 'active' ";
		$sql .= "AND `link`.`status` = 'active' ";
		$sql .= "ORDER BY `cat`.`id` ";

		$items['listings'] = $this->getOne($sql);

		$this->setTable('pages');
		$items['pages']= $this->one("COUNT(`id`)", "`status`='active' AND `nofollow` = '0'");
		$this->resetTable();
		
		$this->setTable('accounts');
		$items['accounts']= $this->one("COUNT(`id`)", "`status`='active'");
		$this->resetTable();
		
		return $items;
	}
	
	function buildCategoriesMap ($aStart, $aLimit, $aSitemap)
	{
		$this->setTable('categories');
		$categories = $this->all('`id`, `path`, `num_listings`',"`status`='active' AND `id`>'0' LIMIT {$aStart}, {$aLimit}");
		$this->resetTable();

		$listings_per_page = $this->mConfig['num_index_listings'];
		$jt_cat_url = $this->mConfig['use_html_path'] ? '.html' : '/';
		$jt_cat_url_page = $this->mConfig['use_html_path'] ? '_' : '/index';
		
		$feed = '';

		if(!empty($categories))
		{
			foreach ($categories as $category)
			{
				$numPagesForCat = ceil($category['num_listings']/$listings_per_page);

				if('google' == $aSitemap)
				{
					$feed .= '<url>'."\n";
					$feed .= '<loc><![CDATA['.ESYN_URL;
					$feed .= $this->mConfig['mod_rewrite'] ? $category['path'].$jt_cat_url : 'index.php?category='.$category['id'];
					$feed .= ']]></loc>'."\n";
					$feed .= '<changefreq>weekly</changefreq>'."\n";
					$feed .= '<priority>0.5</priority>'."\n";
					$feed .= '</url>'."\n";
				}
				elseif('yahoo' == $aSitemap)
				{
					$feed.= ESYN_URL;
					$feed .= $this->mConfig['mod_rewrite'] ? $category['path'].$jt_cat_url."\n" : 'index.php?category='.$category['id']."\n";
				}

				// We have to add as many pages to the Sitemap
				// as the category contains.
				if ($numPagesForCat > 1)
				{
					for ($i=2; $i <= $numPagesForCat; $i++)
					{
						if('google' == $aSitemap)
						{
							$feed .= '<url>'."\n";
							$feed .= '<loc><![CDATA['.ESYN_URL;
							$feed .= $this->mConfig['mod_rewrite'] ? $category['path'].$jt_cat_url_page.$i.'.html' : 'index.php?category='.$category['id'].'&amp;page='.$i;
							$feed .= ']]></loc>'."\n";
							$feed .= '<changefreq>weekly</changefreq>'."\n";
							$feed .= '<priority>0.5</priority>'."\n";
							$feed .= '</url>'."\n";
						}
						elseif('yahoo' == $aSitemap)
						{
							$feed .= ESYN_URL;
							$feed .= $this->mConfig['mod_rewrite'] ? $category['path'].$jt_cat_url_page.$i.'.html'."\n" : 'index.php?category='.$category['id'].'&amp;page='.$i."\n";
						}
					}
				}
			}
		}
		return $feed;
	}

	function buildListingsMap ($aStart, $aLimit, $aSitemap)
	{
		$sql = "SELECT `cat`.`path`, `link`.`id` `id`, `link`.`title` ";
		$sql .= "FROM `".$this->mPrefix."categories` AS `cat` ";
		$sql .= "LEFT JOIN `".$this->mPrefix."listings` AS `link` ";
		$sql .= "ON `link`.`category_id` = `cat`.`id` ";
		$sql .= "WHERE `cat`.`status` = 'active' ";
		$sql .= "AND `link`.`status` = 'active' ";
		$sql .= "ORDER BY `cat`.`id`";
		$sql .= "LIMIT ".$aStart.", ".$aLimit;

		$listings = $this->getAll($sql);

		$feed = '';
		
		if(!empty($listings))
		{
			foreach ($listings as $listing)
			{
				if('google' == $aSitemap)
				{
					$feed .= '<url>'."\n";
					$feed .= '<loc><![CDATA[';
					$feed .= $this->printListingUrl($listing);
					$feed .= ']]></loc>'."\n";
					$feed .= '<changefreq>monthly</changefreq>'."\n";
					$feed .= '<priority>0.6</priority>'."\n";
					$feed .= '</url>'."\n";
				}
				elseif('yahoo' == $aSitemap)
				{
					$feed .= $this->printListingUrl($listing)."\n";
				}
			}
		}
		return $feed;
	}
	
	function buildPagesMap ($aStart, $aLimit, $aSitemap)
	{
		$this->setTable("pages");
		$pages = $this->all("`id`,`name`,`menus`,`order`,`unique_url`,`non_modrewrite_url`,`nofollow`", "`status` = 'active' ORDER BY `order` LIMIT {$aStart}, {$aLimit}");
		$this->resetTable();

		$feed = '';
		
		if(!empty($pages))
		{
			foreach ($pages as $page)
			{
				if('google' == $aSitemap)
				{
					$feed .= '<url>'."\n".'<loc><![CDATA['.ESYN_URL;
					if(!empty($page['unique_url']))
					{
						$feed .= ($this->mConfig['mod_rewrite']) ? $page['unique_url'] : $page['non_modrewrite_url'];
					}
					else
					{
						$feed .= ($this->mConfig['mod_rewrite']) ? 'p'.$page['name'].'.html' : 'page.php?name='.$page['name'];
					}

					$feed .= ']]></loc>'."\n".'<changefreq>monthly</changefreq>'."\n";
					$feed .= '<priority>0.6</priority>'."\n".'</url>'."\n";
				}
				elseif('yahoo' == $aSitemap)
				{
					$feed.= ESYN_URL;
					if(!empty($page['unique_url']))
					{
						$feed .= ($this->mConfig['mod_rewrite']) ? $page['unique_url'] : $page['non_modrewrite_url'];
					}
					else
					{
						$feed .= ($this->mConfig['mod_rewrite']) ? 'p'.$page['name'].'.html' : 'page.php?name='.$page['name'];
					}
					$feed .="\n";
				}
			}
		}
		return $feed;
	}
	
	function buildAccountsMap ($aStart, $aLimit, $aSitemap)
	{
		$this->setTable("accounts");
		$accounts = $this->all("`id`,`username`", "`status` = 'active' ORDER BY `date_reg` LIMIT {$aStart}, {$aLimit}");
		$this->resetTable();

		$feed = '';
		
		if(!empty($accounts))
		{
			foreach ($accounts as $account)
			{
				if('google' == $aSitemap)
				{
					$feed .= '<url>'."\n".'<loc><![CDATA['.ESYN_URL;
					$feed .= ($this->mConfig['mod_rewrite']) ? 'accounts/'.urlencode($account['username']).'.html' : 'view-account.php?account='.urlencode($account['username']);
					$feed .= ']]></loc>'."\n".'<changefreq>monthly</changefreq>'."\n";
					$feed .= '<priority>0.6</priority>'."\n".'</url>'."\n";
				}
				elseif('yahoo' == $aSitemap)
				{
					$feed.= ESYN_URL;
					$feed .= ($this->mConfig['mod_rewrite']) ? 'accounts/'.urlencode($account['username']).'.html' : 'view-account.php?account='.urlencode($account['username']);
					$feed .="\n";
				}
			}
		}
		return $feed;
	}
	
	function deleteOldSitemaps ($aType_sitemap)
	{
		if ('google' == $aType_sitemap)
		{
			$sitemap_files = scandir($this->path_to_file."google".ESYN_DS);
			$sitemap_files = array_slice($sitemap_files,2);
	
			if (is_array($sitemap_files) && !empty($sitemap_files))
			{
				foreach ($sitemap_files as $sitemap_file)
				{
					unlink($this->path_to_file."google".ESYN_DS.$sitemap_file);
				}
			}
		}
		if ('yahoo' == $aType_sitemap)
		{
			if (file_exists($this->path_to_file."yahoo".ESYN_DS."urllist.txt"))
			{
				unlink($this->path_to_file."yahoo".ESYN_DS."urllist.txt");
			}
		}
	}
	
	function writeToFile ($aStr, $aFp)
	{
		if (fwrite($aFp, $aStr) === FALSE)
		{
	        return false;
	    }
	    return true;
	}
	
	function openFile ($aFile, $aMode)
	{
		if(!$fp = fopen($aFile,$aMode))
		{
			return false;
		}
		return $fp;
	}
	
	function getGoogleSMIndex ($aFile)
	{
		$str  = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
		$str .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
		
		while ($aFile >= 0)
		{
			$aFile = $aFile == 0 ? '' : $aFile;
			$str .= "<sitemap>\n";
      		$str .= "<loc><![CDATA[".$this->path_to_file."google".ESYN_DS."sitemap{$aFile}.xml]]></loc>\n";
      		$str .= "<lastmod>".date("Y-m-d")."</lastmod>\n";
   			$str .= "</sitemap>\n";
   			$aFile--;
		}
		
		$str .= '</sitemapindex>';
		
		if(!$fp = $this->openFile($this->path_to_file."google".ESYN_DS."sitemap_index.xml","w"))
		{
			$error = "Cannot open file sitemap.xml in tmp directory";
			return $error;
		}

		if (!$this->writeToFile($str, $fp))
		{
		     $error = "Cannot write to file ".$this->path_to_file."google".ESYN_DS."sitemap_index.xml";
		     return $error;
		}
	}
	
		
	
	
}
