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


require_once(SMARTY_DIR.'Smarty.class.php');

/**
 * esynSmarty
 *
 * Implements class-connector between script code and smarty class 
 * 
 * @uses Smarty
 * @package 
 * @version $id$
 */
class esynSmarty extends Smarty
{
	/**
	 * Description of the Variable
	 * @var		mixed
	 * @access	public
	 */
	var $mHooks = array();

	/**
	 * Description of the Variable
	 * @var		mixed
	 * @access	public
	 */
	var $tmpl = '';
	
	var $printRegular = 'regular-listing-display.tpl';
	var $printSponsored = 'regular-listing-display.tpl';
	var $printFeatured = 'regular-listing-display.tpl';
	var $printPartner = 'regular-listing-display.tpl';

	/**
	 * esynSmarty 
	 * 
	 * @access public
	 * @return void
	 */
	function esynSmarty($tmpl)
	{
		parent::Smarty();

		$this->tmpl = $tmpl;
		$this->template_dir	= ESYN_TEMPLATES.$tmpl.ESYN_DS;

		// create template directory if it does not exist
		if (!is_dir(ESYN_TMP.$tmpl.ESYN_DS))
		{
			esynUtil::mkdir( ESYN_TMP . $tmpl );
		}
		
		if (!is_writable(ESYN_TMP.$tmpl.ESYN_DS))
		{
			trigger_error("Directory Permissions Error | dir_permissions_error | The 'tmp/{$tmpl}' directory is not writable. Please set writable permissions.", E_USER_ERROR);
		}
		
		$this->compile_dir = ESYN_TMP.$tmpl.ESYN_DS;
		$this->config_dir = 'configs'.ESYN_DS;
		$this->cache_dir = ESYN_TMP.'smartycache'.ESYN_DS;

		if(ESYN_CACHING)
		{
			if(!is_dir($this->cache_dir))
			{
				$result = @mkdir($this->cache_dir);

				if(!$result)
				{
					trigger_error("Directory Creation Error | tmp_dir_permissions | Can not create the 'tmp/smartycache' directory.", E_USER_ERROR);
				}
			}

			if (!is_writable($this->cache_dir))
			{
				trigger_error("Directory Permissions Error | dir_permissions_error | The 'tmp/smartycache' directory is not writable. Please set writable permissions.", E_USER_ERROR);
			}
		}

		$this->caching = ESYN_CACHING;
		$this->cache_modified_check = true;
		$this->debugging = false;
		
		if ($this->template_exists($this->template_dir.'sponsored-listing-display.tpl') || ESYN_TEMPLATES . 'common' . ESYN_DS . 'sponsored-listing-display.tpl')
		{
			$this->printSponsored = 'sponsored-listing-display.tpl';
		}

		if ($this->template_exists($this->template_dir.'featured-listing-display.tpl') || ESYN_TEMPLATES . 'common' . ESYN_DS . 'featured-listing-display.tpl')
		{
			$this->printFeatured = 'featured-listing-display.tpl';
		}

		if ($this->template_exists($this->template_dir.'partner-listing-display.tpl') || ESYN_TEMPLATES . 'common' . ESYN_DS . 'partner-listing-display.tpl')
		{
			$this->printPartner = 'partner-listing-display.tpl';
		}
	}

    /**
	 * display 
	 *
	 * executes & displays the template results
     * 
     * @param str $resource_name 
     * @param str $cache_id 
     * @param str $compile_id 
     * @access public
     * @return void
     */
    function display($resource_name, $cache_id = null, $compile_id = null)
    {
		$resource_file = '';

		if(file_exists($resource_name))
		{
			$resource_file = $resource_name;
		}
		elseif(file_exists($this->template_dir.$resource_name))
		{
			$resource_file = $this->template_dir.$resource_name;
		}
		else
		{
			$resource_file = ESYN_TEMPLATES . 'common' . ESYN_DS . $resource_name;
		}

		if(empty($resource_file))
		{
			$error = "This file can not be found in template directory: {$resource_name}";

			$this->assign_by_ref('error', $error);
			
			parent::display($this->template_dir . 'error.tpl', $cache_id, $compile_id);
		}
		else
		{
			if (empty($_SESSION['frontendManageMode']))
			{
				$this->load_filter('output','clearemptyblocks');        
				//$this->load_filter('output','compress');        
			}
			
			parent::display($resource_file, $cache_id, $compile_id);

			if (ESYN_DEBUG == 2)
			{
				require_once(ESYN_CLASSES . 'debug.php');
			}
		}
    }

	function _smarty_include($params)
	{
		if(stristr($params['smarty_include_tpl_file'], ESYN_HOME))
		{
			$params['smarty_include_tpl_file'] = $params['smarty_include_tpl_file'];
		}
		elseif(stristr($params['smarty_include_tpl_file'], ESYN_DS))
		{
			$params['smarty_include_tpl_file'] = ESYN_HOME . $params['smarty_include_tpl_file'];
		}
		elseif(!$this->template_exists($params['smarty_include_tpl_file']))
		{
			$params['smarty_include_tpl_file'] = ESYN_TEMPLATES . 'common' . ESYN_DS . $params['smarty_include_tpl_file'];
		}
		
		parent::_smarty_include($params);
	}

	/**
	 * pass 
	 * 
	 * @param mixed $aVars 
	 * @param mixed $aName 
	 * @access public
	 * @return void
	 */
	function pass($aVars, $aName)
	{
		global $eSyndiCat;

		if(is_array($aVars))
		{
			$eSyndiCat->loadClass('JSON');

			$json = new Services_JSON();

			$out = "var {$aName} = ";
			$out .= $json->encode($aVars);
			$out .= ";";

			$this->assign('phpVariables', $out);
		}
	}
}
