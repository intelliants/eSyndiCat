<?php
//##copyright##

define("ESYN_REALM", "create_category");

define("ESYN_CATEGORY_ICONS_DIR", ESYN_HOME.'uploads'.ESYN_DS.'category-icons'.ESYN_DS);
define("ESYN_CATEGORY_ICONS_COMMON_DIR", ESYN_INCLUDES.'common'.ESYN_DS.'category-icons'.ESYN_DS);

esynUtil::checkAccess();

$esynAdmin->factory("Category");

/**
 *
 * #1 add category without GET ID param
 *		The new category will be added to ROOT category.
 *
 *		The category array is empty.
 *		The parent array contents ROOT category.
 *
 * #2 add category with GET ID param
 *		The category array is empty.
 *		The parent array contents parent category which gets by GET ID param. If there is no category with passed ID than script will get the ROOT category.
 *
 * #3 edit category
 *		There is ID param which is ID editing category.
 *
 *		The category array contents the data of editing category.
 *		The parent array contents parent category which gets by POST ID param. We can get the parent array by category parent_id value also.
 *
 * #4 edit ROOT category
 *		Script have to ignore change in the title field and the parent id value of ROOT category has to be '-1'. But changes in the other fields should be updated.
 *
 *		The category array contents the data of ROOT category.
 *		The parent array is empty.
 *
 */

$category	= array();
$parent		= array();

if(isset($_GET['do']) && 'edit' == $_GET['do'] && isset($_GET['id']) && ctype_digit($_GET['id']))
{
	$category = $esynCategory->row("*", "`id` = :id", array('id' => $_GET['id']));
	$parent = $esynCategory->row("*", "`id` = :parent_id", array('parent_id' => $category['parent_id']));
}
else
{
	if(isset($_GET['id']) && ctype_digit($_GET['id']))
	{
		$parent = $esynCategory->row("*", "`id` = :id", array('id' => $_GET['id']));
	}

	if(empty($parent))
	{
		$parent = $esynCategory->row("*", "`parent_id` = '-1'");
	}
}

/*
 * ACTIONS
 */
if(isset($_POST['save']))
{
	$error			= false;
	$new_category	= array();

	$isCategoryMoved	= false;
	$pathSetFromTitle	= false;
	$invalidPath		= false;

	$esynAdmin->startHook('adminSuggestCategoryValidation');

	if(!defined('ESYN_NOUTF'))
	{
		require_once(ESYN_CLASSES.'esynUtf8.php');

		esynUtf8::loadUTF8Core();
		esynUtf8::loadUTF8Util('ascii', 'validation', 'bad', 'utf8_to_ascii');
	}

	$new_category['no_follow']			= (int)$_POST['no_follow'];
	$new_category['locked']				= (int)$_POST['locked'];
	$new_category['hidden']				= (int)$_POST['hidden'];
	$new_category['unique_tpl']			= (int)$_POST['unique_tpl'];
	$new_category['confirmation']		= (int)$_POST['confirmation'];
	$new_category['confirmation_text']	= (!empty($_POST['confirmation_text'])) ? $_POST['confirmation_text'] : '';
	$new_category['num_cols']			= (!$_POST['num_cols_type']) ? (int)$_POST['num_cols'] : 0;
	$new_category['account_id']			= 0;
	$new_category['status']				= (isset($_POST['status']) && in_array($_POST['status'], array('approval', 'active'))) ? $_POST['status'] : 'approval';
	$new_category['icon']				= (isset($_POST['icon']) && !empty($_POST['icon'])) ? $_POST['icon'] : '';
	$new_category['parent_id']			= isset($parent['id']) ? $parent['id'] : '-1';
	
	if($esynConfig->getConfig('neighbour') && isset($_POST['num_neighbours_type']))
	{
		// don't display neighbours categories
		if('-1' == $_POST['num_neighbours_type'])
		{
			$new_category['num_neighbours'] = '0';
		}

		// display all neighbours categories
		if('0' == $_POST['num_neighbours_type'])
		{
			$new_category['num_neighbours'] = '-1';
		}

		// display custom number of neighbours categories
		if('1' == $_POST['num_neighbours_type'])
		{
			$new_category['num_neighbours'] = isset($_POST['num_neighbours']) ? (int)$_POST['num_neighbours'] : '0';
		}
	}

	/**
	 * If user choose the parent category from tree.
	 * We have already the parent array which contents parent category data.
	 *
	 * If the id parent is not similar with choosen id parent we get new parent array.
	 * But if choosen parent category is not exist we get the ROOT category and assign it to parent array.
	 *
	 */
	if(isset($_POST['parent_id']) && !empty($_POST['parent_id']) && ctype_digit($_POST['parent_id']))
	{
		if($parent['id'] != $_POST['parent_id'])
		{
			$parent = $esynCategory->row("*", "`id` = :id", array('id' => (int)$_POST['parent_id']));
		
			if(empty($parent))
			{
				$parent = $esynCategory->row("*", "`parent_id` = '-1'");
			}

			$new_category['parent_id'] = $parent['id'];
		}
	}

	/**
	 * Checking the UTF-8 is well formed all texts POST fields
	 *
	 */
	$fields = array("title", "page_title", "description", "meta_description", "meta_keywords");
	
	foreach($fields as $field)
	{
		$_POST[$field] = !empty($_POST[$field]) ? $_POST[$field] : '';
		
		if(!utf8_is_valid($_POST[$field]))
		{
			$_POST[$field] = utf8_bad_replace($_POST[$field]);
		}

		$new_category[$field] = $_POST[$field];
	}

	/**
	 * When user is editing category he can change parent category by choosing it in the category tree.
	 *
	 */
	if(isset($_GET['do']) && 'edit' == $_GET['do'])
	{
		if(isset($_POST['parent_id']) && !empty($_POST['parent_id']) && $category['parent_id'] != $_POST['parent_id'])
		{
			$parents = array();

			$temp_parent = $new_category['parent_id'];

			// get all parents until ROOT and break
			for($k = 0; $k < 25; $k++)
			{
				$parents[] = (int)$temp_parent;

				$temp_parent = $esynCategory->one("`parent_id`", "`id` = '{$temp_parent}'");
				
				if(0 == $temp_parent)
				{
					break;
				}
			}

			// $move_to category have on some of its parents $category_id then do not allow moving
			if(in_array($category['id'], $parents))
			{
				$error = true;
				
				$title = $esynCategory->one("`title`", "id = '{$category['parent_id']}'");
				
				$msg = str_replace("{move_to}", $title, $esynI18N['category_cannot_be_moved']);
			}
			else
			{
				/**
				 * Move category but don't update the path. Other there will be error 'path already exists'.
				 *
				 * Path will be updated by update function.
				 *
				 */
				$esynCategory->move($category['id'], $new_category['parent_id'], false);
				
				$isCategoryMoved = true;
			}
		}
	}

	/**
	 * When user edit category he can change title but don't touch the path value.
	 * Therefore script assign old value from $category variable to $_POST['path'] and after
	 * the script will build the category path from new entered title.
	 *
	 */
	if(isset($_GET['do']) && 'edit' == $_GET['do'])
	{
		if(isset($_POST['path']) && $_POST['path'] == $_POST['old_path'])
		{
			$_POST['path'] = $category['path'];
		}
	}

	/**
	 * Building category path.
	 *
	 * If user enter path manually, validate it and build path for new category.
	 * If user doesn't enter path, create path by title field.
	 *
	 */
	if(isset($_POST['path']) && !empty($_POST['path']))
	{
		$_POST['path'] = str_replace("/", "", $_POST['path']);
		
		if(!utf8_is_ascii($_POST['path']))
		{
			$_POST['path'] = utf8_to_ascii($_POST['path']);
		}
		
		$_POST['path'] = preg_replace("/[^a-z0-9_-]+/i", "-", $_POST['path']);
		$_POST['path'] = trim($_POST['path'], "-");
		
		if($esynConfig->getConfig('lowercase_urls'))
		{
			$_POST['path'] = strtolower($_POST['path']);
		}

		if(empty($_POST['path']))
		{
			$invalidPath = true;
		}
		else
		{
			$new_category['path'] = $esynCategory->getPath($parent['path'], $_POST['path']);
		}
	}
	else
	{
		$titlepath = $_POST['title'];
		
		if(!utf8_is_ascii($titlepath))
		{
			$titlepath = utf8_to_ascii($titlepath);
		}
		
		$titlepath = preg_replace("/[^a-z0-9_-]+/i", "-", $titlepath);
		$titlepath = trim($titlepath, "-");
		
		if($esynConfig->getConfig('lowercase_urls'))
		{
			$titlepath = strtolower($titlepath);
		}

		if(empty($titlepath))
		{
			if(isset($_GET['do']) && 'edit' == $_GET['do'])
			{
				$new_category['path'] = $titlepath;
			}
			else
			{
				$invalidPath = true;
			}
		}
		else
		{
			$new_category['path'] = $esynCategory->getPath($parent['path'], $titlepath);
		}

		$pathSetFromTitle = true;
	}

	if (empty($new_category['title']))
	{
		$error = true;
		$msg[] = $esynI18N['title_incorrect'];
	}
	elseif ($esynCategory->exists("`title` = :title AND `parent_id` = :parent_id", array('title' => $new_category['title'], 'parent_id' => $new_category['parent_id'])) && isset($_GET['do']) && 'edit' != $_GET['do'])
	{
		$error = true;
		$msg[] = $esynI18N['category_title_exists'];
	}
	elseif(empty($new_category['path']) || $invalidPath)
	{
		$error = true;
		$msg[] = $esynI18N['error_invalid_path'];
	}
	elseif (!$esynCategory->validPath($new_category['path'])) // additional check as of empty path treated as valid
	{
		$error = true;
		$msg[] = $esynI18N['error_invalid_path'];
	}
	/**
	 * if the path values was changed then checking for existing this path
	 *
	 */
	elseif(0 != strcasecmp($new_category['path'], $_POST['old_path']) && $esynCategory->exists("`path` = :path", array('path' => $new_category['path'])))
	{
		$error = true;

		if($pathSetFromTitle)
		{
			$path = $esynCategory->lastPartOfPath($new_category['path']);

			$msg[] = str_replace("{name}", $path, $esynI18N['generated_path_exists']);
		}
		else
		{
			$msg[] = $esynI18N['category_path_exists'];
		}
	}

	/**
	 * If user want to change the ROOT category.
	 * Script doesn't allow to change the title and parent id values.
	 * Set these values to default anyways.
	 * The path has to be empty.
	 *
	 */
	if(isset($_GET['do']) && 'edit' == $_GET['do'] && '-1' == $new_category['parent_id'])
	{
		$new_category['path'] = '';
		$new_category['parent_id'] = -1;
	}

	if (!$error)
	{
		if(isset($_GET['do']) && 'edit' == $_GET['do'])
		{	
			if ('-1' == $new_category['parent_id'])
			{
				$esynConfig->setConfig('site_description', $new_category['meta_description'], true);
				$esynConfig->setConfig('site_keywords', $new_category['meta_keywords'], true);
				$esynConfig->setConfig('site_main_content', $new_category['description'], true);
			}
		
			$new_id = (int)$_POST['id'];

			/**
			 * Script needs to know the old path of editing category to update all subcategories' paths
			 */
			$new_category['old_path'] = $_POST['old_path'];
			
			$esynCategory->update($new_category, "`id` = '{$new_id}'");

			if($category['locked'] != $new_category['locked'])
			{
				$subcategories = isset($_POST['subcategories']) ? true : false;
				$locked = (1 == $new_category['locked']) ? true : false;

				$esynCategory->lock($new_id, $locked, $subcategories);
			}

			$msg[] = $esynI18N['save_changes'];
		}
		else
		{
			$new_id = $esynCategory->insert($new_category);

			if(1 == $new_category['locked'])
			{
				$subcategories = isset($_POST['subcategories']) ? true : false;

				$esynCategory->lock($new_id, true, $subcategories);
			}

			$msg[] = $esynI18N['category_added'];
		}
		
		/** recursively add records to non-tree structure table of categories **/
		$esynCategory->buildRelation($new_id);

		esynMessages::setMessage($msg, $error);

		if(isset($_POST['goto']))
		{
			if('add' == $_POST['goto'])
			{
				esynUtil::reload();
			}
			elseif('list' == $_POST['goto'])
			{
				esynUtil::go2("controller.php?file=categories");
			}
			elseif('browse_add' == $_POST['goto'])
			{
				esynUtil::go2("controller.php?file=browse&id={$new_category['parent_id']}");
			}
			elseif('browse_new' == $_POST['goto'])
			{
				esynUtil::go2("controller.php?file=browse&id={$new_id}");
			}
		}
	}

	esynMessages::setMessage($msg, $error);
}

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
		if(file_exists(ESYN_CATEGORY_ICONS_COMMON_DIR))
		{
			$directory = opendir(ESYN_CATEGORY_ICONS_COMMON_DIR);

			while (false !== ($file = readdir($directory)))
			{
				if (substr($file,0,1) != ".")
				{
					if(preg_match('/\.(jpg|gif|png)$/', $file))
					{
						$size = filesize(ESYN_CATEGORY_ICONS_COMMON_DIR.$file);
						$lastmod = filemtime(ESYN_CATEGORY_ICONS_COMMON_DIR.$file) * 1000;

						$out['data'][] = array(
							'name'		=> $file,
							'size'		=> $size,
							'lastmod'	=> $lastmod,
							'url'		=> ESYN_URL.'includes/common/category-icons/'.$file
						);
					}
				}
			}
			closedir($directory);
		}

		if(file_exists(ESYN_CATEGORY_ICONS_DIR))
		{
			$directory = opendir(ESYN_CATEGORY_ICONS_DIR);

			while (false !== ($file = readdir($directory)))
			{
				if (substr($file,0,1) != ".")
				{
					if(preg_match('/\.(jpg|gif|png)$/', $file))
					{
						$size = filesize(ESYN_CATEGORY_ICONS_DIR.$file);
						$lastmod = filemtime(ESYN_CATEGORY_ICONS_DIR.$file) * 1000;

						$out['data'][] = array(
							'name'		=> $file,
							'size'		=> $size,
							'lastmod'	=> $lastmod,
							'url'		=> ESYN_URL.'uploads/category-icons/'.$file
						);
					}
				}
			}
			closedir($directory);
		}
	}

	if('getcategoryurl' == $_GET['action'])
	{
		$parent_id = isset($_GET['parent_id']) ? (int)$_GET['parent_id'] : NULL;
		$titlepath = isset($_GET['title']) ? $_GET['title'] : '';

		if(NULL !== $parent_id && !empty($titlepath))
		{
			$esynAdmin->loadClass("Layout");

			$esynLayout = new esynLayout();

			require_once(ESYN_CLASSES.'esynUtf8.php');

			esynUtf8::loadUTF8Core();
			esynUtf8::loadUTF8Util('ascii', 'validation', 'utf8_to_ascii');

			$parent_path = $esynCategory->one('`path`', "`id` = '{$parent_id}'");
			$max_id = $esynCategory->one('MAX(`id`) + 1');

			if(!utf8_is_ascii($titlepath))
			{
				$titlepath = utf8_to_ascii($titlepath);
			}
			
			$titlepath = preg_replace("/[^a-z0-9_-]+/i", "-", $titlepath);
			$titlepath = trim($titlepath, "-");
			
			if($esynConfig->getConfig('lowercase_urls'))
			{
				$titlepath = strtolower($titlepath);
			}
		
			$path = $esynCategory->getPath($parent_path, $titlepath);

			$out['data'] = $esynLayout->printCategoryUrl(array('cat' => array('path' => $path, 'id' => $max_id, 'title' => $titlepath)));
		}
	}

	if(empty($out['data']))
	{
		$out['data'] = '';
	}

	echo $json->encode($out);
	exit;
}

/*
 * ACTIONS
 */


$gNoBc = false;

$gBc[0]['title'] = $esynI18N['browse'];
$gBc[0]['url'] = 'controller.php?file=browse';

$gBc[1]['title'] = $esynI18N['create_category'];
$gBc[1]['url'] = 'controller.php?file=suggest-category';

$gTitle = $esynI18N['create_category'];

if(isset($_GET['do']) && ('edit' == $_GET['do']))
{
	if ('-1' == $category['parent_id'])
	{
		$category['meta_description'] = $esynConfig->getConfig('site_description');
		$category['meta_keywords'] = $esynConfig->getConfig('site_keywords');
		$category['description'] = $esynConfig->getConfig('site_main_content');
	}
		
	$category['old_path'] = $category['path'];
	$category['path'] = $esynCategory->lastPartOfPath($category['path']);

	$gBc[1]['title'] = $esynI18N['edit_category'];
	$gBc[1]['url'] = 'controller.php?file=suggest-category';

	$gTitle = $esynI18N['edit_category'];
}

require_once(ESYN_ADMIN_HOME.'view.php');

$esynSmarty->assign('category', $category);
$esynSmarty->assign('parent', $parent);

$esynSmarty->display('suggest-category.tpl');
