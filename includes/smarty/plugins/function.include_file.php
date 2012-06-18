<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {include_file} plugin
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
function smarty_function_include_file($params, &$smarty)
{
	global $esynConfig;
	$base = ESYN_URL;
	//$join = $esynConfig->getConfig('join_js');
	//$join = defined('ESYN_IN_ADMIN') ? false : $join;
	$join = false;

	if(isset($params['base']) && !empty($params['base']))
	{
		$params['base'] = str_replace('TMPL', $smarty->tmpl, $params['base']);

		$base .= $params['base'];
	}

	if(!empty($params['js']))
	{
		$files = explode(',', $params['js']);

		if ($join)
		{
			if (count($files) > 1)
			{
				$fname = '';
				foreach ($files as $i => $f)
				{
					$files[$i] = trim($f);
					$fname .= basename($f) . '-';
				}

// 				$gzip = empty($_SERVER['HTTP_ACCEPT_ENCODING'])
// 						|| false === strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')
// 						? false : true;
				$fname = 'cache/' . $fname . '.js';
//				$fname .= $gzip ? '.gz' : '';

				if (!file_exists(ESYN_TMP . $fname))
				{
//					$f_stream = fopen(($gzip ? 'compress.zlib://' : '') . ESYN_TMP . $fname, 'w');
					$f_stream = fopen(ESYN_TMP . $fname, 'w');

					foreach ($files as $f)
					{
						$buf = file_get_contents(ESYN_HOME . $f . '.js');
						fwrite($f_stream, $buf);
					}
					fclose($f_stream);
				}
				printf( '<script type="text/javascript" src="tmp/%s"></script>', $fname);
			}
			else
			{
				$js = trim(array_shift($files));

				if (filesize(ESYN_HOME . $js . '.js') > 1024)
				{
					$path = (FALSE !== stristr($js, ESYN_URL)) ? $js : $base . $js;
					echo "<script type=\"text/javascript\" src=\"{$path}.js\"></script>\n";
				}
				else
				{
					print '<script type="text/javascript"><!--' . PHP_EOL;
					print file_get_contents(ESYN_HOME . $js . '.js');
					print PHP_EOL . '--></script>';
				}
			}
		}
		else
		{
			foreach($files as $js)
			{
				$js = trim($js);
				$path = (FALSE !== stristr($js, ESYN_URL)) ? $js : $base . $js;
				print '<script type="text/javascript" src="' . $path . '.js"></script>' . PHP_EOL;
			}
		}
	}


	if(!empty($params['css']))
	{
		foreach(explode(',', $params['css']) as $css)
		{
			$css = trim($css);
			$path = (FALSE !== stristr($css, ESYN_URL)) ? $css : $base . $css;
			echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"{$path}.css\" />\n";
		}
	}
}

