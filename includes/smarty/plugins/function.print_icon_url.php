<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */
function smarty_function_print_icon_url($params, &$smarty)
{
    global $esynConfig;

    $realm = isset($params['realm']) && !empty($params['realm']) ? $params['realm'] : ESYN_REALM;
	$ext = isset($params['ext']) && !empty($params['ext']) ? $params['ext'] : 'png';
	$header = isset($params['header']) ? true : false;

	$file = $realm.'.'.$ext;

    if (defined('ESYN_CURRENT_PLUGIN') && $header)
    {
        $path = isset($params['path']) && !empty($params['path']) ? $params['path'] : 'img';

        $icon = 'plugins/'.ESYN_CURRENT_PLUGIN.'/admin/'.$path.'/'.$file;
    }
    else
    {
        $path = isset($params['path']) && !empty($params['path']) ? $params['path'] : 'h1';

        $icon = ESYN_ADMIN_FOLDER.'/templates/'.$esynConfig->getConfig('admin_tmpl').'/img/icons/'.$path.'/'.$file;
    }

    $file = ESYN_HOME.$icon;

    if(is_file($file) && file_exists($file))
    {
        echo ' style="background-image: url(\''.ESYN_URL.$icon.'\');"';
	}
	elseif (isset($params['path']) && !empty($params['path']))
	{
		$icon = ESYN_ADMIN_FOLDER.'/templates/'.$esynConfig->getConfig('admin_tmpl').'/img/icons/'.$params['path'].'/default.png';
		echo ' style="background-image: url(\''.ESYN_URL.$icon.'\');"';
	}
}
?>
