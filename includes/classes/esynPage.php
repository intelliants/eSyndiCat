<?php
//##copyright##

/**
 * esynPage 
 * 
 * @uses eSyndiCat
 * @package 
 * @version $id$
 */
class esynPage extends eSyndiCat
{
	/**
	 * mTable 
	 * 
	 * @var string
	 * @access public
	 */
	var $mTable = "pages";

	/**
	 * mConfig 
	 * 
	 * @var mixed
	 * @access public
	 */
	var $mConfig;
	
	/**
	 * getPageByRealm returns page information array by a given realm
	 * 
	 * @param string $aRealm page name
	 * 
	 * @return array
	 */
	function getPageByRealm()
	{
		return $this->row("*", "`name` = '".ESYN_REALM."'");
	}

	function getPages()
	{
		global $category;

		$out = array();

		if(isset($this->mConfig['esyndicat_menu_type']) && !empty($this->mConfig['esyndicat_menu_type']))
		{
			$menu_types = explode(',', $this->mConfig['esyndicat_menu_type']);
		}
		else
		{
			$menu_types = array('inventory', 'main', 'bottom', 'account');
		}

		$where = "`status` = 'active' ";

		if(!$this->mConfig['suggest_category'])
		{
			$where .= "AND `name` != 'suggest_category' ";
		}

		if(!$this->mConfig['accounts'])
		{
			$where .= "AND `name` != 'accounts' ";
		}

		if(!$this->mConfig['allow_listings_submission'])
		{
			$where .= "AND `name` != 'suggest_listing' ";
		}

		if(!empty($this->mPlugins))
		{
			$where .= "AND `plugin` IN('', '".join("','", $this->mPlugins)."') ";
		}

		$where .= "ORDER BY `order`";

		$menus = $this->all("*", $where);

		if(!empty($menus))
		{
			foreach($menus as $key => $menu)
			{
				$menu_items = explode(',', $menu['menus']);

				foreach($menu_types as $jey => $menu_type)
				{
					if(in_array($menu_type, $menu_items))
					{
						$tmp_menu = array();

						$tmp_menu['title'] = $this->mI18N['page_title_' . $menu['name']];
						$tmp_menu['order'] = $menu['order'];

						if(!empty($menu['unique_url']))
						{
							$tmp_menu['url'] = (ESYN_MOD_REWRITE) ? $menu['unique_url'] : $menu['non_modrewrite_url'];
						}
						else
						{
							if(ESYN_MOD_REWRITE)
							{
								if(!empty($menu['custom_url']))
								{
									$tmp_menu['url'] = $menu['custom_url'].'.html';
								}
								else
								{
									$tmp_menu['url'] = $menu['name'].'.html';
								}
							}
							else
							{
								$tmp_menu['url'] = 'page.php?name=' . $menu['name'];
							}
						}

						$tmp_menu['url'] = str_replace('{idcat}', (int)$category['id'], $tmp_menu['url']);

						$tmp_menu['nofollow'] = $menu['nofollow'];
						$tmp_menu['name'] = $menu['name'];
						
						$out[$menu_type][] = $tmp_menu;
					}
				}
			}
		}

		return $out;
	}
}
