<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {print_img} plugin
 *
 * Type:     function<br>
 * Name:     include_file<br>
 * Purpose:  include javascript|css files
 * @author  Sergey Ten <sergei.ten at gmail dot com>
 * @param array
 * @param Smarty
 * @return string
 *
 */
function smarty_function_print_img($params, &$smarty)
{
	$out = ESYN_URL;
	
	$folder = isset($params['folder']) ? $params['folder'] : '';
	
	$attrs = array('id', 'title', 'width', 'height', 'border', 'style', 'class', 'alt');
	
	if(isset($params['ups']) && !empty($params['ups']))
	{
		$out .= "uploads/".$folder.$params['fl'];
	}
	elseif(isset($params['pl']) && !empty($params['pl']))
	{
		$admin = (isset($params['admin']) && !empty($params['admin'])) ? 'admin/' : '';
		$out .= 'plugins/'.$params['pl'].'/'.$admin.'templates/img/'.$folder.$params['fl'];
	}
	else
	{
		$admin = (isset($params['admin']) && !empty($params['admin'])) ? 'admin/templates/' : '';

		if(!empty($admin))
		{
			$out .= $admin.'default/img/'.$folder.$params['fl'];
		}
		else
		{
			$out .= 'templates/'.$smarty->tmpl.'/img/'.$folder.$params['fl'];
		}
	}
	
	// prints including image tag
	if (isset($params['full']))
	{	
		$params['alt'] = isset($params['alt']) ? $params['alt'] : '';
		
		$atrs = '';
		foreach($params as $key=>$attr)
		{
			$atrs .= (in_array($key, $attrs) && isset($attr)) ? $key.'="'.$attr.'" ' : ''; 
		}
		$out = '<img src="'.$out.'" '.$atrs.'/>'; 
	}
	
	echo $out;
}
?>
