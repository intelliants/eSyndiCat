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
 * PHP ctype compatibility functions.
 * 
 *
 */
    function ctype_digit($c) {
		return (string)(int)$c === $c && (int)$c > -1;
    }
    function ctype_graph($c) {
    		$x = (bool)preg_match("/\s/", $c);
    		return !$x;
    }
    function ctype_print($c) {
		$x = (bool)preg_match("/\s/", $c);
    		return !$x;
    }
    function ctype_space($c) {
        return (bool)preg_match("/^\s+$/i", $c);
    }     
    function ctype_alnum($c) {
		return (bool)preg_match("/^[a-z0-9]$/i",$c);
    }
    function ctype_alpha($c) {
		return (bool)preg_match("/^[a-z]$/i",$c);
    }
    function ctype_cntrl($c) {

    }
    function ctype_lower($c) {

    }
    function ctype_punct($c) {

    }
    function ctype_upper($c) {

    }
    function ctype_xdigit($c) {

    }
