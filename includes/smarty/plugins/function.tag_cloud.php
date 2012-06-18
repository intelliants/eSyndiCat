<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {tag_cloud} function plugin
 *
 * Type:     function<br>
 * Name:     tag_cloud<br>
 * Input:<br>
 *      - tags (required) - the tags and the totals for each tag - associative array
 *      - base_url (required) - the base url to which the tag will be appended 	- string
 *			- min_font_size (optional) - minimum font size (default 12) - integer
 *			- max_font_size (optional) - maximum font size (default 30) - integer
 *			- id (optional) - css id	(default 'tag_cloud') - string
 *			- class (optional) - css class (default 'tag_cloud') - string
 *							
 *
 * Purpose:  Prints a 'tag cloud' generated from the passed parameters
 *
 * Examples:<br>
 * <pre>
 * {tag_cloud tags=$associative_array_of_tags_and_totals base_url='/search.php?q='}
 * </pre> 
 *
 * The "tags" array should be structured as follows:<br>
 *
 * <pre>
 * $tags['horse'] = 22;
 * $tags['cow'] = 12;
 * $tags['chicken']	= 30;
 * $tags['goat'] = 23;
 * </pre>
 *
 * @link http://jaybill.com/smarty-plugins/tag-cloud
 * @author Jaybill McCarthy <heybill at jaybill dot com>
 * @param array
 * @param Smarty
 * @return string 
 */
function smarty_function_tag_cloud($params, &$smarty)
{
	$tags = $params['tags'];

	$base_url = $params['base_url'];
	
	if(is_null($params['min_font_size'])){
		$min_font_size = 12;
	} else {
		$min_font_size = $params['min_font_size'];
	}
	
	if(is_null($params['max_font_size'])){
		$max_font_size = 30;
	} else {
		$max_font_size = $params['max_font_size'];
	}
	
	if(is_null($params['id'])){
		$id = "tag_cloud";
	} else {
		$id = $params['id'];
	}
	
	if(is_null($params['class'])){
		$class = "tag_cloud";
	} else {
		$class = $params['class'];
	}
	
	$minimum_count = min($tags);
	$maximum_count = max($tags);
	$spread = $maximum_count - $minimum_count;
	
	if($spread == 0) {
	    $spread = 1;
	}
	
	$cloud_html = '';
	$cloud_tags = array(); // create an array to hold tag code

	foreach ($tags as $tag => $count) {
		$size = $min_font_size + ($count - $minimum_count) 
			* ($max_font_size - $min_font_size) / $spread;
		$cloud_tags[] = '<a style="font-size: '. floor($size) . 'px'  
			. '" id="'.$id.'" class="'.$class.'" href="' . $base_url . $tag 
			. '" title="\'' . $tag  . '\' returned a count of ' . $count . '">' 
			. htmlspecialchars($tag) . '</a>';
	}

	$cloud_html = join("\n", $cloud_tags) . "\n";
	return $cloud_html;
}