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
 * esynHook 
 * 
 * @uses eSyndiCat
 * @package 
 * @version $id$
 */
class esynHook extends eSyndiCat
{	
	/**
	 * mTable 
	 * 
	 * @var string
	 * @access public
	 */
	var $mTable = 'hooks';

	/**
	 * mLink 
	 * 
	 * @var mixed
	 * @access public
	 */
	var $mLink 	= null;

	/**
	 * esynHook 
	 * 
	 * @access public
	 * @return void
	 */
	function esynHook()
	{
		parent::eSyndiCat();
	}	
}
