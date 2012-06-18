<?php
//##copyright##
	
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
