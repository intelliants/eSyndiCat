<?php
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2004 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/3_0.txt.                                  |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Laurent Laville <pear@laurent-laville.org>                  |
// |          Aidan Lister <aidan@php.net>                                |
// +----------------------------------------------------------------------+
//

/**
 * Replace array_walk_recursive()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/function.array_walk_recursive
 * @author      Tom Buskens <ortega@php.net>
 * @author      Aidan Lister <aidan@php.net>
 * @version     $Revision: 1.7 $
 * @since       PHP 5
 * @require     PHP 4.0.6 (is_callable)
 */
function array_walk_recursive(&$input, $funcname)
{
		if (!is_callable($funcname)) {
            if (is_array($funcname)) {
                $funcname = $funcname[0] . '::' . $funcname[1];
            }
            user_error('array_walk_recursive() Not a valid callback ' . $user_func,
                E_USER_WARNING);
            return;
        }

        if (!is_array($input)) {
            user_error('array_walk_recursive() The argument should be an array',
                E_USER_WARNING);
            return;
        }

        $args = func_get_args();

        foreach ($input as $key => $item) {
            if (is_array($item)) {
                array_walk_recursive($item, $funcname, $args);
                $input[$key] = $item;
            } else {
                $args[0] = &$item;
                $args[1] = &$key;
                call_user_func_array($funcname, $args);
                $input[$key] = $item;
            }
        }
    }

/**
 * Replace debug_print_backtrace()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/function.debug_print_backtrace
 * @author      Laurent Laville <pear@laurent-laville.org>
 * @author      Aidan Lister <aidan@php.net>
 * @version     $Revision: 1.3 $
 * @since       PHP 5
 * @require     PHP 4.3.0 (debug_backtrace)
 */
    function debug_print_backtrace()
    {
    	// Get backtrace
      $backtrace = debug_backtrace();

      // Unset call to debug_print_backtrace
      array_shift($backtrace);
        die();
      // Iterate backtrace
      $calls = array();
      foreach ($backtrace as $i => $call) {
        $location = $call['file'] . ':' . $call['line'];
        $function = (isset($call['class'])) ?
          $call['class'] . '.' . $call['function'] :
          $call['function'];
           
            $params = '';
            if (isset($call['args'])) {
                $params = implode(', ', $call['args']);
            }

            $calls[] = sprintf('#%d  %s(%s) called at [%s]',
                $i,
                $function,
                $params,
                $location); 
        }

        echo implode("\n", $calls);
    }

/**
 * Replace file_put_contents()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/function.file_put_contents
 * @author      Aidan Lister <aidan@php.net>
 * @version     $Revision: 1.25 $
 * @internal    resource_context is not supported
 * @since       PHP 5
 * @require     PHP 4.0.0 (user_error)
 */
	if (!defined('FILE_USE_INCLUDE_PATH')) {
	    define('FILE_USE_INCLUDE_PATH', 1);
	}
	
	if (!defined('LOCK_EX')) {
	    define('LOCK_EX', 2);
	}
	
	if (!defined('FILE_APPEND')) {
	    define('FILE_APPEND', 8);
	}	
	
    function file_put_contents($filename, $content, $flags = null, $resource_context = null)
    {
        // If $content is an array, convert it to a string
        if (is_array($content)) {
            $content = implode('', $content);
        }

        // If we don't have a string, throw an error
        if (!is_scalar($content)) {
            user_error('file_put_contents() The 2nd parameter should be either a string or an array',
                E_USER_WARNING);
            return false;
        }

        // Get the length of data to write
        $length = strlen($content);

        // Check what mode we are using
        $mode = ($flags & FILE_APPEND) ?
                    'a' :
                    'wb';

        // Check if we're using the include path
        $use_inc_path = ($flags & FILE_USE_INCLUDE_PATH) ?
                    true :
                    false;

        // Open the file for writing
        if (($fh = fopen($filename, $mode, $use_inc_path)) === false) {
            user_error('file_put_contents() failed to open stream: Permission denied',
                E_USER_WARNING);
            return false;
        }

        // Attempt to get an exclusive lock
        $use_lock = ($flags & LOCK_EX) ? true : false ;
        if ($use_lock === true) {
            if (!flock($fh, LOCK_EX)) {
                return false;
            }
        }

        // Write to the file
        $bytes = 0;
        if (($bytes = fwrite($fh, $content)) === false) {
            $errormsg = sprintf('file_put_contents() Failed to write %d bytes to %s',
                            $length,
                            $filename);
            user_error($errormsg, E_USER_WARNING);
            return false;
        }

        // Close the handle
        fclose($fh);

        // Check all the data was written
        if ($bytes != $length) {
            $errormsg = sprintf('file_put_contents() Only %d of %d bytes written, possibly out of free disk space.',
                            $bytes,
                            $length);
            user_error($errormsg, E_USER_WARNING);
            return false;
        }

        // Return length
        return $bytes;
    }

/**
 * Replace scandir()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/function.scandir
 * @author      Aidan Lister <aidan@php.net>
 * @version     $Revision: 1.18 $
 * @since       PHP 5
 * @require     PHP 4.0.0 (user_error)
 */
  function scandir($directory, $sorting_order = 0)
    {
        if (!is_string($directory)) {
            user_error('scandir() expects parameter 1 to be string, ' .
                gettype($directory) . ' given', E_USER_WARNING);
            return;
        }

        if (!is_int($sorting_order) && !is_bool($sorting_order)) {
            user_error('scandir() expects parameter 2 to be long, ' .
                gettype($sorting_order) . ' given', E_USER_WARNING);
            return;
        }

        if (!is_dir($directory) || (false === $fh = @opendir($directory))) {
            user_error('scandir() failed to open dir: Invalid argument', E_USER_WARNING);
            return false;
        }

        $files = array ();
        while (false !== ($filename = readdir($fh))) {
            $files[] = $filename;
        }

        closedir($fh);

        if ($sorting_order == 1) {
            rsort($files);
        } else {
            sort($files);
        }

        return $files;
    }
    
	function str_ireplace($search, $replace, $subject, $count = null)
    {
        // Sanity check
        if (is_string($search) && is_array($replace)) {
            trigger_error('Array to string conversion', E_USER_NOTICE);
            $replace = (string) $replace;
        }

        // If search isn't an array, make it one
        if (!is_array($search)) {
            $search = array ($search);
        }
        $search = array_values($search);

        // If replace isn't an array, make it one, and pad it to the length of search
        if (!is_array($replace)) {
            $replace_string = $replace;

            $replace = array ();
            for ($i = 0, $c = count($search); $i < $c; $i++) {
                $replace[$i] = $replace_string;
            }
        }
        $replace = array_values($replace);

        // Check the replace array is padded to the correct length
        $length_replace = count($replace);
        $length_search = count($search);
        if ($length_replace < $length_search) {
            for ($i = $length_replace; $i < $length_search; $i++) {
                $replace[$i] = '';
            }
        }

        // If subject is not an array, make it one
        $was_array = false;
        if (!is_array($subject)) {
            $was_array = true;
            $subject = array ($subject);
        }

        // Loop through each subject
        $count = 0;
        foreach ($subject as $subject_key => $subject_value) {
            // Loop through each search
            foreach ($search as $search_key => $search_value) {
                // Split the array into segments, in between each part is our search
                $segments = explode(strtolower($search_value), strtolower($subject_value));

                // The number of replacements done is the number of segments minus the first
                $count += count($segments) - 1;
                $pos = 0;

                // Loop through each segment
                foreach ($segments as $segment_key => $segment_value) {
                    // Replace the lowercase segments with the upper case versions
                    $segments[$segment_key] = substr($subject_value, $pos, strlen($segment_value));
                    // Increase the position relative to the initial string
                    $pos += strlen($segment_value) + strlen($search_value);
                }

                // Put our original string back together
                $subject_value = implode($replace[$search_key], $segments);
            }

            $result[$subject_key] = $subject_value;
        }

        // Check if subject was initially a string and return it as a string
        if ($was_array === true) {
            return $result[0];
        }

        // Otherwise, just return the array
        return $result;
    }
//
//     function str_split($string, $split_length = 1)
//    {
//        if (!is_scalar($split_length)) {
//            user_error('str_split() expects parameter 2 to be long, ' .
//                gettype($split_length) . ' given', E_USER_WARNING);
//            return false;
//        }
//
//        $split_length = (int) $split_length;
//        if ($split_length < 1) {
//            user_error('str_split() The length of each segment must be greater than zero', E_USER_WARNING);
//            return false;
//        }
//        
//        // Select split method
//        if ($split_length < 65536) {
//            // Faster, but only works for less than 2^16
//            preg_match_all('/.{1,' . $split_length . '}/s', $string, $matches);
//            return $matches[0];
//        } else {
//            // Required due to preg limitations
//            $arr = array();
//            $idx = 0;
//            $pos = 0;
//            $len = strlen($string);
//
//            while ($len > 0) {
//                $blk = ($len < $split_length) ? $len : $split_length;
//                $arr[$idx++] = substr($string, $pos, $blk);
//                $pos += $blk;
//                $len -= $blk;
//            }
//
//            return $arr;
//        }
//    }
//    
//        function array_combine($keys, $values)
//    {
//        if (!is_array($keys)) {
//            user_error('array_combine() expects parameter 1 to be array, ' .
//                gettype($keys) . ' given', E_USER_WARNING);
//            return;
//        }
//
//        if (!is_array($values)) {
//            user_error('array_combine() expects parameter 2 to be array, ' .
//                gettype($values) . ' given', E_USER_WARNING);
//            return;
//        }
//
//        $key_count = count($keys);
//        $value_count = count($values);
//        if ($key_count !== $value_count) {
//            user_error('array_combine() Both parameters should have equal number of elements', E_USER_WARNING);
//            return false;
//        }
//
//        if ($key_count === 0 || $value_count === 0) {
//            user_error('array_combine() Both parameters should have number of elements at least 0', E_USER_WARNING);
//            return false;
//        }
//
//        $keys    = array_values($keys);
//        $values  = array_values($values);
//
//        $combined = array();
//        for ($i = 0; $i < $key_count; $i++) {
//            $combined[$keys[$i]] = $values[$i];
//        }
//
//        return $combined;
//    }
//    
//    function strpbrk($haystack, $char_list)
//    {
//        if (!is_scalar($haystack)) {
//            user_error('strpbrk() expects parameter 1 to be string, ' .
//                gettype($haystack) . ' given', E_USER_WARNING);
//            return false;
//        }
//
//        if (!is_scalar($char_list)) {
//            user_error('strpbrk() expects parameter 2 to be scalar, ' .
//                gettype($needle) . ' given', E_USER_WARNING);
//            return false;
//        }
//
//        $haystack  = (string) $haystack;
//        $char_list = (string) $char_list;
//
//        $len = strlen($haystack);
//        for ($i = 0; $i < $len; $i++) {
//            $char = substr($haystack, $i, 1);
//            if (strpos($char_list, $char) === false) {
//                continue;
//            }
//            return substr($haystack, $i);
//        }
//
//        return false;
//    }
//    
//    
///**
// * Replace function http_build_query()
// *
// * @category    PHP
// * @package     PHP_Compat
// * @link        http://php.net/function.http-build-query
// * @author      Stephan Schmidt <schst@php.net>
// * @author      Aidan Lister <aidan@php.net>
// * @version     $Revision: 1.16 $
// * @since       PHP 5
// * @require     PHP 4.0.0 (user_error)
// */
//if (!function_exists('http_build_query')) {
//    function http_build_query($formdata, $numeric_prefix = null)
//    {
//        // If $formdata is an object, convert it to an array
//        if (is_object($formdata)) {
//            $formdata = get_object_vars($formdata);
//        }
//
//        // Check we have an array to work with
//        if (!is_array($formdata)) {
//            user_error('http_build_query() Parameter 1 expected to be Array or Object. Incorrect value given.',
//                E_USER_WARNING);
//            return false;
//        }
//
//        // If the array is empty, return null
//        if (empty($formdata)) {
//            return;
//        }
//
//        // Argument seperator
//        $separator = ini_get('arg_separator.output');
//
//        // Start building the query
//        $tmp = array ();
//        foreach ($formdata as $key => $val) {
//            if (is_integer($key) && $numeric_prefix != null) {
//                $key = $numeric_prefix . $key;
//            }
//
//            if (is_scalar($val)) {
//                array_push($tmp, urlencode($key).'='.urlencode($val));
//                continue;
//            }
//
//            // If the value is an array, recursively parse it
//            if (is_array($val)) {
//                array_push($tmp, __http_build_query($val, urlencode($key)));
//                continue;
//            }
//        }
//
//        return implode($separator, $tmp);
//    }
//
//    // Helper function
//    function __http_build_query ($array, $name)
//    {
//        $tmp = array ();
//        foreach ($array as $key => $value) {
//            if (is_array($value)) {
//                array_push($tmp, __http_build_query($value, sprintf('%s[%s]', $name, $key)));
//            } elseif (is_scalar($value)) {
//                array_push($tmp, sprintf('%s[%s]=%s', $name, urlencode($key), urlencode($value)));
//            } elseif (is_object($value)) {
//                array_push($tmp, __http_build_query(get_object_vars($value), sprintf('%s[%s]', $name, $key)));
//            }
//        }
//
//        // Argument seperator
//        $separator = ini_get('arg_separator.output');
//
//        return implode($separator, $tmp);
//    }
//}


// $Id: get_headers.php,v 1.4 2007/04/17 10:09:56 arpad Exp $

define('PHP_COMPAT_GET_HEADERS_MAX_REDIRECTS', 5);

/**
 * Replace get_headers()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @license     LGPL - http://www.gnu.org/licenses/lgpl.html
 * @copyright   2004-2007 Aidan Lister <aidan@php.net>, Arpad Ray <arpad@php.net>
 * @link        http://php.net/function.get_headers
 * @author      Aeontech <aeontech@gmail.com>
 * @author      Cpurruc <cpurruc@fh-landshut.de>
 * @author      Aidan Lister <aidan@php.net>
 * @author      Arpad Ray <arpad@php.net>
 * @version     $Revision: 1.4 $
 * @since       PHP 5.0.0
 * @require     PHP 4.0.0 (user_error)
 */
function get_headers2($url, $format = 0)
{
    $result = array();
    for ($i = 0; $i < PHP_COMPAT_GET_HEADERS_MAX_REDIRECTS; $i++) {
        $headers = php_compat_get_headers_helper($url, $format);
        if ($headers === false) {
            return false;
        }
        $result = array_merge($result, $headers);
        if ($format == 1 && isset($headers['Location'])) {
            $url = $headers['Location'];
            continue;
        }
        if ($format == 0) {
            for ($j = count($headers); $j--;) {
                if (preg_match('/^Location: (.*)$/i', $headers[$j], $matches)) {
                    $url = $matches[1];
                    continue 2;
                }
            }
        }
        return $result;
    }
    return empty($result) ? false : $result;
}

function php_compat_get_headers_helper($url, $format)
{
    // Init
    $urlinfo = parse_url($url);
    $port    = isset($urlinfo['port']) ? $urlinfo['port'] : 80;

    // Connect
    $fp = fsockopen($urlinfo['host'], $port, $errno, $errstr, 30);
    if ($fp === false) {
        return false;
    }
          
    // Send request
    $head = 'HEAD ' . (isset($urlinfo['path']) ? $urlinfo['path'] : '/') .
        (isset($urlinfo['query']) ? '?' . $urlinfo['query'] : '') .
        ' HTTP/1.0' . "\r\n" .
        'Host: ' . $urlinfo['host'] . "\r\n\r\n";
    fputs($fp, $head);

    // Read
    $headers = array();
    while (!feof($fp)) {
        if ($header = trim(fgets($fp, 1024))) {
            list($key) = explode(':', $header);

            if ($format === 1) {
                // First element is the HTTP header type, such as HTTP 200 OK
                // It doesn't have a separate name, so check for it
                if ($key == $header) {
                    $headers[] = $header;
                } else {
                    $headers[$key] = substr($header, strlen($key)+2);
                }
            } else {
                $headers[] = $header;
            }
        }
    }

    fclose($fp);

    return $headers;
}



function stripos($haystack, $needle, $offset = null)
{
    if (!is_scalar($haystack)) {
        user_error('stripos() expects parameter 1 to be string, ' .
            gettype($haystack) . ' given', E_USER_WARNING);
        return false;
    }

    if (!is_scalar($needle)) {
        user_error('stripos() needle is not a string or an integer.', E_USER_WARNING);
        return false;
    }

    if (!is_int($offset) && !is_bool($offset) && !is_null($offset)) {
        user_error('stripos() expects parameter 3 to be long, ' .
            gettype($offset) . ' given', E_USER_WARNING);
        return false;
    }

    // Manipulate the string if there is an offset
    $fix = 0;
    if (!is_null($offset)) {
        if ($offset > 0) {
            $haystack = substr($haystack, $offset, strlen($haystack) - $offset);
            $fix = $offset;
        }
    }

    $segments = explode(strtolower($needle), strtolower($haystack), 2);

    // Check there was a match
    if (count($segments) === 1) {
        return false;
    }

    $position = strlen($segments[0]) + $fix;
    return $position;
}