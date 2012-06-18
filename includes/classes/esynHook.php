<?php
//##copyright##

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
