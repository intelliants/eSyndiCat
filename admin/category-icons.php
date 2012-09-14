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


define("ESYN_REALM", "category_icons");

esynUtil::checkAccess();

define("ESYN_CATEGORY_ICONS_DIR", ESYN_HOME.'uploads'.ESYN_DS.'category-icons'.ESYN_DS);

if(!file_exists(ESYN_CATEGORY_ICONS_DIR))
{
	if(!is_writeable(ESYN_HOME.'uploads'.ESYN_DS))
	{
		trigger_error('Icons Category Directory Permissions | dir_permissions_error | The uploads directory is not writeable. Please set writeable permissions.', E_USER_ERROR);
	}

	mkdir(ESYN_CATEGORY_ICONS_DIR);
	chmod(ESYN_CATEGORY_ICONS_DIR, 0777);
}

/*
 * ACTIONS
 */

/*
 * AJAX
 */
if(isset($_GET['action']))
{
	$esynAdmin->loadClass("JSON");

	$json = new Services_JSON();

	$out = array('data' => '', 'total' => 0, 'error' => false);

	if('getimages' == $_GET['action'])
	{
		$directories = array(
			ESYN_TEMPLATES . 'common' . ESYN_DS . 'img' . ESYN_DS . 'category_icons' . ESYN_DS,
			ESYN_TEMPLATES . $esynAdmin->mConfig['tmpl'] . ESYN_DS . 'img'. ESYN_DS . 'category_icons' . ESYN_DS,
			ESYN_CATEGORY_ICONS_DIR
		);

		foreach ($directories as $d)
		{
			if (file_exists($d))
			{
				$directory = opendir($d);

				while (false !== ($file = readdir($directory)))
				{
					if (substr($file,0,1) != ".")
					{
						if(preg_match('/\.(jpg|gif|png)$/', strtolower($file)))
						{
							$size = filesize($d . $file);
							$lastmod = filemtime($d . $file) * 1000;
							
							$url = str_replace(ESYN_HOME, '', $d);

							if ('\\' == ESYN_DS)
							{
								$url = str_replace(ESYN_DS, '/', $url);
							}

							$out['data'][] = array(
								'name'			=> $file,
								'size'			=> $size,
								'lastmod'		=> $lastmod,
								'url'			=> $url . $file,
								'removeable'	=> $d == ESYN_CATEGORY_ICONS_DIR ? true : false,
								'default'		=> $url . $file == $esynAdmin->mConfig['default_categories_icon'] ? true : false
							);
						}
					}
				}
				closedir($directory);
			}
		}
	}

	if('upload' == $_GET['action'])
	{
		$imgtypes = array(
			"image/gif"		=> "gif",
			"image/jpeg"	=> "jpg",
			"image/pjpeg"	=> "jpg",
			"image/png"		=> "png"
		);

		if((bool)$_FILES['icon']['error'])
		{
			$out['error'] = true;
			$out['msg'] = 'Error occurs while icon is uploading.';
		}
		else
		{
			if(is_uploaded_file($_FILES['icon']['tmp_name']))
			{
				$ext = strtolower(substr($_FILES['icon']['name'], -3));

				// if 'jpeg'
				if($ext == 'peg')
				{
					$ext = 'jpg';
				}

				if(!array_key_exists($_FILES['icon']['type'], $imgtypes) || !in_array($ext, $imgtypes))
				{
					$out['error'] = true;

					$images_types = join(",", array_unique($imgtypes));
					$tmp_msg = str_replace("{types}", $images_types, $esynI18N['wrong_image_type']);
					$out['msg'] = strip_tags(str_replace("{name}", 'Icon', $tmp_msg));
				}
				else
				{
					// convert image name to lower case
					$_FILES['icon']['name'] = strtolower($_FILES['icon']['name']);
					
					if(move_uploaded_file($_FILES['icon']['tmp_name'], ESYN_CATEGORY_ICONS_DIR.$_FILES['icon']['name']))
					{
						$out['error'] = false;
						$out['msg'] = "Icon {$_FILES['icon']['name']} is successfully uploaded.";
					}
				}
			}
			else
			{
				$out['error'] = true;
				$out['msg'] = 'Template can not be uploaded.';
			}
		}

		$out['success'] = true;
	}

	if(empty($out['data']))
	{
		$out['data'] = '';
	}

	echo $json->encode($out);
	exit;
}

if(isset($_POST['action']))
{
	$esynAdmin->loadClass("JSON");

	$json = new Services_JSON();

	$out = array('msg' => array(), 'error' => false);

	if('remove' == $_POST['action'])
	{
		if(empty($_POST['icons']))
		{
			$out['error'] = true;
			$out['msg'][] = 'Param is wrong';
		}

		if(!$out['error'])
		{
			foreach($_POST['icons'] as $icon)
			{
				if(file_exists(ESYN_CATEGORY_ICONS_DIR.$icon))
				{
					unlink(ESYN_CATEGORY_ICONS_DIR.$icon);
				}
			}

			$out['msg'] = 'Icons have been removed';
		}
	}

	if ('default' == $_POST['action'])
	{
		if (empty($_POST['url']))
		{
			$out['error'] = true;
			$out['msg'][] = 'Param is wrong';
		}
		
		if (!$out['error'])
		{
			$icon = $_POST['url'];

			if (file_exists(ESYN_HOME . $icon))
			{
				$esynConfig->setConfig('default_categories_icon', $icon, true);
			}

			$out['msg'] = '<b><i>' . ESYN_URL . $icon . '</i></b> ' . $esynI18N['image_set_as_default_icon_categories'];
		}
	}

	echo $json->encode($out);
	exit;
}
/*
 * ACTIONS
 */


$gNoBc = false;

$gBc[0]['title'] = $esynI18N['category_icons'];
$gBc[0]['url'] = 'controller.php?file=category_icons';

$gTitle = $esynI18N['category_icons'];

$actions = array(
	array("url" => "#", "icon" => "add.png", "label" => 'Upload new category icon', "attributes" => 'id="upload_icon"'),
);

require_once(ESYN_ADMIN_HOME.'view.php');

$esynSmarty->display('category-icons.tpl');

?>
