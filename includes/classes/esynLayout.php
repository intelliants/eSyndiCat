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


class esynLayout extends eSyndiCat
{
	/**
	 * getCategories returns category parents chain by a given category id
	 * 
	 * @param integer $aId category id
	 * 
	 * @return array 
	 */
	function getCategoriesChain($aId)
	{
		if ($aId > 0)
		{
			esynUtil::getBreadcrumb($aId, $breadcrumb);
		}

		$breadcrumb['home']['title'] = $this->mConfig['site'];

		return $breadcrumb;
	}

	/**
	 * getTitle returns page title value by a given category id
	 * @param integer $aId category id
	 * @param string $aTitle [optional] custom page title
	 * @param object $aPage
	 * 
	 * @return string
	 */
	function getTitle($aId, $aTitle = '', $aPage)
	{
		global $esynI18N;

		if ($this->mConfig['title_breadcrumb'])
		{
			$out = '';
			$tree = $this->getCategoriesChain($aId);

			if ($tree)
			{
				$size = count($tree);
				$first = array_shift($tree);

				$first['title'] = empty($first['page_title']) ? $first['title'] : $first['page_title'];

				$out .= $first['title'];

				if($aPage > 1)
				{
					$out .= '&nbsp;'.$esynI18N['page'].'&nbsp;'.$aPage;
				}

				unset($first);
				foreach($tree as $value)
				{
					$value['title'] = esynSanitize::html($value['title']);
					$out .= '&nbsp;&#171;&nbsp;'.$value['title'];
				}
			}
		}
		else
		{
			if($aPage > 1)
			{
				$aTitle .= '&nbsp;'.$esynI18N['page'].'&nbsp;'.$aPage;
			}

			$out = $aTitle.'&nbsp;::&nbsp;'.$this->mConfig['site'];
		}

		return $out;
	}

	/**
	 * printBreadcrumb prints breadcrumb
	 * 
	 * @param array $aCategory category information array
	 * @param array $aAdditional [optional] additional custom breadcrumb element
	 * @param boolean $aFull [optional] display final element, true - display
	 * @param object $rootNoFollow [optional]
	 * 
	 * @return string
	 */
	function printBreadcrumb($aCategory, $aAdditional = '', $aFull = FALSE, $rootNoFollow='')
	{
		global $esynI18N;
		
		if(empty($aCategory))
		{
			$this->setTable("categories");
			$aCategory = $this->row("*", "`parent_id` = -1");
			$this->resetTable();
		}
		
		$out = '<div class="breadcrumb">';
		
		if (-1 != $aCategory['parent_id'])
		{
			esynUtil::getBreadcrumb($aCategory['id'], $breadcrumb);
		}

		$url = ESYN_URL;
		
		$rootNoFollow = ($rootNoFollow) ? 'rel="nofollow"' : '';
		$out .= "<a href=\"{$url}\" {$rootNoFollow}>".$this->mConfig['site']."</a>";
	
		if (isset($breadcrumb))
		{
			$breadcrumb = array_reverse($breadcrumb);

			$cnt = 1;
			$size = count($breadcrumb);
			// default
			$noFollow = $rootNoFollow ? " rel=\"nofollow\"" : '';
			foreach($breadcrumb as $item)
			{
				$caption = esynSanitize::html($item['title']);
				if (($size == $cnt) && !$aFull)
				{
					$out .= "&nbsp;/&nbsp;<strong>{$caption}</strong>";
				}
				else
				{
					$link = esynLayout::printCategoryUrl(array('cat' => $item));
					
					if(!$noFollow)
					{
						$noFollow = $item['no_follow'] ? " rel=\"nofollow\"" : '';
					}
					$out .= "&nbsp;/&nbsp;<a href=\"".$link."\" ".$noFollow.">".$caption."</a>";
				}
				$cnt++;
			}
		}

		/** if there are additional elements displays after breadcrumb to category **/
		if ($aAdditional)
		{
			$cnt = count($aAdditional);
			$i = 0;
			foreach($aAdditional as $item)
			{
				$i++;
				$caption = $item['caption'];
				if (isset($item['url']) && ($cnt != $i))
				{
					$url = ESYN_URL.$item['url'];
					$out .= "&nbsp;/&nbsp;<a href=\"{$url}\">".$caption."</a>";
				}
				else
				{
					$out .= "&nbsp;/&nbsp;<strong>".$caption."</strong>";
				}
			}
		}
		else
		{
			if (!isset($breadcrumb))
			{
				$out .= "&nbsp;/&nbsp;<strong>".$esynI18N['page_title_'.ESYN_REALM]."</strong>";
			}
		}

		$out .= '</div>';

		return $out;
	}
	
	/**
	 * printCategoryUrl displays category url depending on configuration
	 * 
	 * @param array $aParams['category'] category information array
	 * 
	 * @return void 
	 */
	function printCategoryUrl($aParams)
	{
		global $esynLayout;
		
		if (isset($aParams['fprefix']))
		{
			$aParams['cat']['id'] = $aParams['cat'][$aParams['fprefix'].'_id'];
			$aParams['cat']['path'] = $aParams['cat'][$aParams['fprefix'].'_path'];
		}
		
		return $esynLayout->{$esynLayout->mCatUrlFunction}($aParams['cat']);
	}
	
	/**
	 * printCatUrl prints category url on mod_rewrite disabled
	 * 
	 * @param array $aCategory category information array
	 * 
	 * @return string 
	 */
	function printCatUrl($aCategory)
	{
		return ESYN_URL.'index.php?id='.$aCategory['id'];
	}
	
	/**
	 * printSeoCatUrl prints category url on mod_rewrite enabled
	 * 
	 * @param array $aCategory category information array
	 * 
	 * @return string 
	 */
	function printSeoCatUrl($aCategory)
	{
		$url = ESYN_URL;
		if (isset($aCategory['path']) && !empty($aCategory['path']))
		{
			$url .= $aCategory['path'].'/';
		}
		
		return $url;
	}

	/**
	 * printHtmlCatUrl prints category url on mod_rewrite disabled
	 * 
	 * @param array $aCategory category information array
	 * 
	 * @return string 
	 */	
	function printHtmlCatUrl($aCategory)
	{
		return ESYN_URL.'index.php?id='.$aCategory['id'];
	}

	/**
	 * printSeoHtmlCatUrl prints category url on mod_rewrite enabled and .html extension
	 * 
	 * @param array $aCategory category information array
	 * 
	 * @return string 
	 */
	function printSeoHtmlCatUrl($aCategory)
	{
		$url = ESYN_URL;
		
		if (isset($aCategory['path']) && !empty($aCategory['path']))
		{
			$url .= $aCategory['path'].'.html';
		}
		
		return $url;
	}
	
	/**
	 * printListingUrl displays listing url depending on configuration
	 * 
	 * @param array $aParams['listing'] listing information array
	 * 
	 * @return void 
	 */
	function printListingUrl($aParams)
	{
		global $esynLayout;
		
		if (isset($aParams['details']) && (true == $aParams['details']))
		{
			return ESYN_MOD_REWRITE ? $esynLayout->printSeoForwardUrl($aParams['listing']) : $esynLayout->printForwardUrl($aParams['listing']);
		}
		
		return $esynLayout->{$esynLayout->mUrlFunction}($aParams['listing']);
	}
	
	/**
	 * printUrl prints listing url field value
	 * 
	 * @param array $aListing listing informaiton array
	 * 
	 * @return string
	 */
	function printUrl($aListing)
	{
		return strtolower($aListing['url']);
	}
	
	/**
	 * printSeoUrl prints listing url field value on mod_rewrite config enabled
	 *  
	 * @param array $aListing listing information array
	 * 
	 * @return string 
	 */
	function printSeoUrl($aListing)
	{
		return strtolower($aListing['url']);
	}
	
	/**
	 * printForwardUrl prints url to view listing details on mod_rewite disabled
	 * 
	 * @param array $aListing listing information array
	 * 
	 * @return string 
	 */
	function printForwardUrl($aListing)
	{
		return ESYN_URL.'view-listing.php?id='.$aListing['id'];
	}
		
	/**
	 * printSeoForwardUrl prints url to view listing details on mod_rewrite enabled
	 * 
	 * @param array $aListing listing information array
	 * 
	 * @return string 
	 */
	function printSeoForwardUrl($aListing)
	{
		global $esynConfig;
		
		$url = ESYN_URL;
		
		if (isset($aListing['path']) && !empty($aListing['path']))
		{
			$url .= $aListing['path'];
			$url .= ('/' == substr($aListing['path'], -1)) ? '' : '/';
		}
		
		if($esynConfig->getConfig('lowercase_urls'))
		{
			$aListing['title'] = strtolower($aListing['title']);
		}
		
		$url .= esynUtil::convertStr(array('string' => $aListing['title']));
		$url .= '-l'.$aListing['id'].'.html';

		return $url;
	}

	/**
	 * printAccUrl displays account url depending on configuration
	 * 
	 * @param array $aParams['account'] account information array
	 * 
	 * @return void 
	 */
	function printAccUrl($aParams)
	{
		global $esynLayout;
		
		return $esynLayout->{$esynLayout->mAccountUrlFunction}($aParams['account']);
	}
	
	/**
	 * printAccountUrl prints url to view account details on mod_rewite disabled
	 * 
	 * @param array $aAccount account information array
	 * 
	 * @return string 
	 */
	function printAccountUrl($aAccount)
	{
		return ESYN_URL . 'view-account.php?account=' . urlencode($aAccount['username']);
	}
		
	/**
	 * printAccountSeoUrl prints url to view account details on mod_rewrite enabled
	 * 
	 * @param array $aAccount account information array
	 * 
	 * @return string 
	 */
	function printAccountSeoUrl($aAccount)
	{
		$url = ESYN_URL . 'accounts/' . urlencode($aAccount['username']) . '.html';

		return $url;
	}
}
