<?php
//##copyright##

define("ESYN_REALM", "language");

esynUtil::checkAccess();

$esynAdmin->factory("Language");

$error = false;

if(empty($_GET['view']))
{
	$_GET['view'] = 'language';
}

/*
 * ACTIONS
 */
if(isset($_GET['do']))
{
	/*
	 * Download language sql file
	 */
	if('download' == $_GET['do'])
	{
		if(isset($_GET['language']) && !empty($_GET['language']))
		{
			$out = '';
			
			$lang_values = $esynLanguage->getPhrases('', '', $_GET['language']);
			
			$out .= "INSERT INTO `{prefix}language` (`id`, `key`, `value`, `lang`, `category`, `code`, `plugin`) VALUES";
			$data = '';
			
			foreach($lang_values as $key=>$value)
			{
				$data .= "(";
				foreach($value as $key2=>$value2)
				{
					if(!isset($value[$key2]))
					{
						$data .= "NULL, ";
					}
					elseif($value[$key2] != "")
					{
						$data .= ('id' == $key2) ? "NULL, " : "'".esynSanitize::sql($value[$key2])."', ";
					}
					else
					{
						$data .= "'', ";
					}
				}
				$data = preg_replace("#, \$#", "", $data);

				$data .= "),\n";
			}
			$out .= rtrim(rtrim($data), ",");
			$out .= ";";
			
			$sqlfile = urlencode(isset($_POST['filename']) ? $_POST['filename'] : $_GET['language'].'.sql');

			header("Content-Type: text/plain; chartset=utf-8");
			header("Content-Disposition: attachment;filename=\"{$sqlfile}\"");
			header("Content-Length: ".strlen($out));

			print $out;
			exit;
		}
	}

	/*
	 * Remove language
	 */
	if('delete' == $_GET['do'])
	{
		if(array_key_exists($_GET['language'], $esynAdmin->mLanguages))
		{
			$esynLanguage->delete("`code` = :lang", array('lang' => $_GET['language']));

			$languages = $esynAdmin->getLanguages();

			if(count($languages) == 1)
			{
				$esynConfig->setConfig('language_switch', '0', true);
			}
			
			esynMessages::setMessage($esynI18N['language_deleted'], false);

			$esynAdmin->mCacher->clearAll('language');
			$esynAdmin->mCacher->clearAll('lang');
			$esynAdmin->mCacher->clearAll('config');

			esynUtil::reload(array("view" => null));
		}
	}

	/*
	 * Set default language
	 */
	if('default' == $_GET['do'])
	{
		$esynConfig->setConfig('lang', $_GET['language'], true);

		$esynI18N = $esynLanguage->getLang($_GET['language']);

		setcookie('admin_lng', '', $_SERVER['REQUEST_TIME'] - 3600);
		setcookie('admin_lng', $_GET['language']);

		$_SESSION['admin_lng'] = $_GET['language'];

		$esynAdmin->mCacher->clearAll('config');
		$esynAdmin->mCacher->clearAll('lang');
		$esynAdmin->mCacher->clearAll('language');
		$esynAdmin->mCacher->clearAll('languages');
	
		esynMessages::setMessage($esynI18N['changes_saved'], false);
	}
}

if(isset($_POST['do']))
{
	/*
	 * Copying language
	 */
	if('add_lang' == $_POST['do'])
	{
		if(empty($_POST['new_lang']) || strlen(trim($_POST['new_lang'])) == 0)
		{
			$error = true;
			$msg[] = $esynI18N['title_incorrect'];
		}

		if(preg_match('/^[a-z]{2}$/i', $_POST['new_code']))
		{
			if(array_key_exists($_POST['new_code'], $esynAdmin->mLanguages))
			{
				$error = true;
				$msg[] = $esynI18N['language_already_exists'];				
			}
		}
		else
		{
			$error = true;
			$msg[] = $esynI18N['bad_iso'];
		}

		if(!$error)
		{
			$all_fields = $esynLanguage->all("`key`,`value`,`category`", "`code` = '".ESYN_LANGUAGE."'");

			$counter = 0;
			$new_code = strtolower($_POST['new_code']);
			$new_lang = $_POST['new_lang'];

			foreach($all_fields as $value)
			{
				$row = array(
					"key"		=> $value['key'] ,
					"value"		=> $value['value'],
					"lang"		=> $new_lang,
					"code"		=> $new_code,
					"category"	=> $value['category']
				);

				if($esynLanguage->insert($row))
				{
					$counter++;
				}
			}

			$msg[] = esynSanitize::html($_POST['new_lang'])." successfully added ( {$counter} strings )";

			esynMessages::setMessage($msg, $error);

			$esynAdmin->mCacher->clearAll('language');
			$esynAdmin->mCacher->clearAll('lang');

			esynUtil::reload(array("view" => null));
		}

		esynMessages::setMessage($msg, $error);
	}

	/*
	 * Download language sql file
	 */
	if('download' == $_POST['do'])
	{
		if(isset($_POST['lang']) && !empty($_POST['lang']))
		{
			$out = '';
			
			$lang_values = $esynLanguage->getPhrases('', '', $_POST['lang']);

			$format = isset($_POST['file_format']) && in_array($_POST['file_format'], array('csv', 'sql')) ? $_POST['file_format'] : 'sql';

			if('sql' == $format)
			{
				$out .= "INSERT INTO `{prefix}language` (`id`, `key`, `value`, `lang`, `category`, `code`, `plugin`) VALUES";
				$data = '';
				
				foreach($lang_values as $key => $value)
				{
					$data .= "(";
					foreach($value as $key2 => $value2)
					{
						if(!isset($value[$key2]))
						{
							$data .= "NULL, ";
						}
						elseif($value[$key2] != "")
						{
							$data .= ('id' == $key2) ? "NULL, " : "'".esynSanitize::sql($value[$key2])."', ";
						}
						else
						{
							$data .= "'', ";
						}
					}
					$data = preg_replace("#, \$#", "", $data);

					$data .= "),\n";
				}
				$out .= rtrim(rtrim($data), ",");
				$out .= ";";
			}

			if('csv' == $format)
			{
				foreach($lang_values as $key => $value)
				{
					unset($value['id']);

					$value = array_map(array("esynSanitize", "sql"), $value);

					$out .= implode("|", $value);
					$out .= "\r\n";
				}
			}
			
			$sqlfile = urlencode(isset($_POST['filename']) ? $_POST['filename'].'.'.$format : $_GET['lang'].'.'.$format);

			header("Content-Type: text/plain; chartset=utf-8");
			header("Content-Disposition: attachment;filename=\"{$sqlfile}\"");
			header("Content-Length: ".strlen($out));

			print $out;
			exit;
		}
	}

	/*
	 * Importing sql file
	 */
	if('import' == $_POST['do'])
	{
		$filename = $_FILES ? $_FILES['language_file']['tmp_name'] : $_POST['language_file2'];
		$format = isset($_POST['file_format']) && in_array($_POST['file_format'], array('csv', 'sql')) ? $_POST['file_format'] : 'sql';

		if(empty($filename))
		{
			$error = true;
			$msg[] = 'Language file name is empty';
		}
		elseif(!($f = fopen ($filename, "r" )))
		{
			$error = true;
			$msg[] = str_replace("{filename}", $filename, $esynI18N['cant_open_sql']);
		}
		
		if(!$error)
		{
			if('sql' == $format)
			{
				$sql = '';

				while ($s = fgets ($f, 10240))
				{
					$s = trim ($s);
					if ( $s[0] == '#' ) continue;
					if ( $s[0] == '' ) continue;

					$sql .= $s;
					if ( $s[strlen($s)-1] != ';' )
					{
						continue;					
					}

					$sql = str_replace("{prefix}", $esynAdmin->mPrefix, $sql);

					$esynAdmin->query($sql);

					$sql = "";
				}

				fclose($f);
			}

			if('csv' == $format)
			{
				$csv_content = file($filename);

				$tmp = array();

				if(!empty($csv_content))
				{
					$sql = "INSERT INTO `{$esynAdmin->mPrefix}language` (`key`, `value`, `lang`, `category`, `code`, `plugin`) VALUES ";
					
					foreach($csv_content as $row)
					{
						if(!empty($row))
						{
							$fields = explode("|", trim($row));

							$fields = array_map(array("esynSanitize", "sql"), $fields);

							$tmp[] = "('" . implode("','", $fields) . "')";
						}
					}

					$sql .= implode(",", $tmp);
					$sql .= ';';
				}

				$esynAdmin->query($sql);
			}

			$msg[] = $esynI18N['changes_saved'];

			$esynAdmin->mCacher->clearAll('language');
			$esynAdmin->mCacher->clearAll('lang');
		}

		esynMessages::setMessage($msg, $error);
	}
}

/*
 * AJAX
 */
if(isset($_GET['action']))
{
	$esynAdmin->loadClass("JSON");

	$json = new Services_JSON();

	/*
	 * Add new phrase
	 */
	if('add_phrase' == $_GET['action'])
	{
		$out = array('msg' => '', 'error' => false, 'success' => true);

		if(empty($_POST['key']))
		{
			$out['error'] = true;
			$out['msg'][] = $esynI18N['incorrect_key'];
		}

		if(empty($_POST['value']))
		{
			$out['error'] = true;
			$out['msg'][] = $esynI18N['incorrect_value'];
		}

		if(!$out['error'])
		{
			if(isset($_POST['language']) && array_key_exists($_POST['language'], $esynAdmin->mLanguages))
			{
				$lang = $_POST['language'];
			}
			else
			{
				$lang = ESYN_LANGUAGE;
			}

			$key = preg_replace("#[^a-z0-9_]#", "", $_POST['key']);
			$value = esynSanitize::sql($_POST['value']);
			$category = preg_replace("#[^a-z0-9_]#", "", $_POST['category']);

			if(empty($key))
			{
				$out['error'] = true;
				$out['msg'][] = $esynI18N['key_not_valid'];
			}

			if(empty($value))
			{
				$out['error'] = true;
				$out['msg'][] = $esynI18N['incorrect_value'];
			}
		}

		if($esynLanguage->exists("`key` = '{$key}' AND `code` = '{$lang}' AND `category` = '{$category}'"))
		{
			$out['error'] = true;
			$out['msg'][] = $esynI18N['key_exists'];
		}

		if(!$out['error'])
		{
			$phrase = array(
				"key"		=> $key,
				"value"		=> $value,
				"code"		=> $lang,
				"category"	=> $category, 
				"lang"		=> $esynAdmin->mLanguages[$lang]
			);

			$esynLanguage->insert($phrase);

			$out['success'] = !$out['error'];
			$out['msg'] = $esynI18N['phrase_added'];

			$esynAdmin->mCacher->clearAll('language');
			$esynAdmin->mCacher->clearAll('lang');
		}
	}

	/*
	 * Get json phrases
	 */
	if('get' == $_GET['action'])
	{
		$start = (int)$_GET['start'];
		$limit = (int)$_GET['limit'];

		$sort = $_GET['sort'];
		$dir = in_array($_GET['dir'], array('ASC', 'DESC')) ? $_GET['dir'] : 'ASC';

		if('phrase' == $_GET['grid'])
		{
			if(!empty($sort) && !empty($dir))
			{
				$order = " ORDER BY `{$sort}` {$dir}";
			}

			$out = array('data' => '', 'total' => 0);
			$conds = array();
			$values = array();

			if(isset($_GET['language']) && !empty($_GET['language']) && array_key_exists($_GET['language'], $esynAdmin->mLanguages))
			{
				$conds[] = "`code` = :language";
				$values['language'] = $_GET['language'];
			}

			if(isset($_GET['key']) && !empty($_GET['key']))
			{
				$conds[] = "`key` LIKE :key";
				$values['key'] = '%'.$_GET['key'].'%';
			}

			if(isset($_GET['value']) && !empty($_GET['value']))
			{
				$conds[] = "`value` LIKE :value";
				$values['value'] = '%'.$_GET['value'].'%';
			}

			if(isset($_GET['category']) && !empty($_GET['category']) && 'all' != $_GET['category'])
			{
				$conds[] = "`category` = :category";
				$values['category'] = $_GET['category'];
			}

			if(isset($_GET['filter_plugin']) && !empty($_GET['filter_plugin']))
			{
				$conds[] = "`plugin` = :plugin";
				$values['plugin'] = $_GET['filter_plugin'];
			}

			if(empty($conds))
			{
				$where = '1=1';
			}
			else
			{
				$where = join(" AND ", $conds);
			}

			$out['total'] = $esynLanguage->one("COUNT(*)", $where, $values);
			$out['data'] = $esynLanguage->all("*, '1' `remove`", $where.$order, $values, $start, $limit);
		}

		if('compare' == $_GET['grid'])
		{
			if(!empty($sort) && !empty($dir))
			{
				$order = " ORDER BY `{$sort}` {$dir}";
			}

			$out = array('data' => '', 'total' => 0);
			$conds = array();
			$values = array();
			$language_result = array();

			if(isset($_GET['key']) && !empty($_GET['key']))
			{
				$conds[] = "`key` LIKE :key";
				$values['key'] = '%'.$_GET['key'].'%';
			}

			if(isset($_GET['value']) && !empty($_GET['value']))
			{
				$conds[] = "`value` LIKE :value";
				$values['value'] = '%'.$_GET['value'].'%';
			}

			if(isset($_GET['category']) && !empty($_GET['category']) && 'all' != $_GET['category'])
			{
				$conds[] = "`category` = :category";
				$values['category'] = $_GET['category'];
			}

			if(isset($_GET['filter_plugin']) && !empty($_GET['filter_plugin']))
			{
				$conds[] = "`plugin` = :plugin";
				$values['plugin'] = $_GET['filter_plugin'];
			}
			
			if(empty($conds))
			{
				$where = '1=1';
			}
			else
			{
				$where = join(" AND ", $conds);
			}

			$count_where = $where . " AND `code` = '".ESYN_LANGUAGE."'";

			$out['total'] = $esynLanguage->one("COUNT(*)", $count_where, $values);

			foreach($esynAdmin->mLanguages as $code => $language)
			{
				$lang_where = $where . " AND `code` = '{$code}'";

				$language_result[$code] = $esynLanguage->all("*, '1' `remove`", $lang_where.$order, $values, $start, $limit);
			}

			if(!empty($language_result))
			{
				foreach($language_result as $code => $language)
				{
					if(!empty($language))
					{
						foreach($language as $key => $phrase)
						{
							$out['data'][$key]['id'] = $phrase['id'];
							$out['data'][$key]['key'] = $phrase['key'];
							$out['data'][$key]['lang_'.$code] = $phrase['value'];
							$out['data'][$key]['category'] = $phrase['category'];
							$out['data'][$key]['remove'] = 1;
						}
					}
				}
			}
		}
	}

	if('getplugins' == $_GET['action'])
	{
		$esynAdmin->setTable("plugins");
		$plugins = $esynAdmin->keyvalue("`id`, `name`");

		if(!empty($plugins))
		{
			foreach($plugins as $key => $plugin)
			{
				$out['data'][] = array('value' => $plugin, 'display' => $plugin);
			}
		}

		$esynAdmin->resetTable();
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

	$out = array('msg' => 'Unknow error', 'error' => true);

	/*
	 * Update grid field
	 */
	if('update' == $_POST['action'])
	{
		$field = $_POST['field'];
		$value = $_POST['value'];
		$where = '';
		$values = array();

		if(empty($field) || empty($value))
		{
			$out['error'] = true;
			$out['msg'] = 'Wrong params';
		}
		else
		{
			$out['error'] = false;
		}

		if(!$out['error'])
		{
			if(isset($_POST['ids']))
			{
				$where = $esynAdmin->convertIds('id', $_POST['ids']);
			}
			elseif(isset($_POST['key']))
			{
				$where = "`key` = :key";
				$values['key'] = $_POST['key'];
			}

			if(isset($_POST['lang']) && !empty($_POST['lang']) && array_key_exists($_POST['lang'], $esynAdmin->mLanguages))
			{
				$where .= " AND `code` = :code";
				$values['code'] = $_POST['lang'];
			}

			if($esynLanguage->exists($where, $values))
			{
				$esynLanguage->update(array($field => $value), $where, $values);
			}
			else
			{
				$row = $esynLanguage->row("*", "`key` = :key", $values);

				if(!empty($row))
				{
					unset($row['id']);

					$row['lang'] = $esynAdmin->mLanguages[$values['code']];
					$row['code'] = $values['code'];
					$row[$field] = $value;

					$esynLanguage->insert($row);
				}
			}

			$out['msg'] = $esynI18N['changes_saved'];

			$esynAdmin->mCacher->clearAll('language');
			$esynAdmin->mCacher->clearAll('lang');
		}
	}

	/*
	 * Remove phrase by id
	 */
	if('remove' == $_POST['action'])
	{
		if(empty($_POST['ids']))
		{
			$out['error'] = true;
			$out['msg'] = 'Wrong params';
		}
		else
		{
			$out['error'] = false;
		}

		if(!$out['error'])
		{
			if(is_array($_POST['ids']))
			{
				foreach($_POST['ids'] as $id)
				{
					$ids[] = (int)$id;
				}
				$where = "`id` IN ('".join("','", $ids)."')";
			}
			else
			{
				$ids = (int)$_POST['ids'];
				$where = "`id` = '{$id}'";
			}

			$esynLanguage->delete($where);

			$out['msg'] = $esynI18N['changes_saved'];

			$esynAdmin->mCacher->clearAll('language');
			$esynAdmin->mCacher->clearAll('lang');
		}
	}

	echo $json->encode($out);
	exit;
}

/*
 * ACTIONS
 */

$gNoBc = false;

$gBc[0]['title'] = $esynI18N['manage_language'];
$gBc[0]['url'] = 'controller.php?file=language&amp;view=language';

if(isset($_GET['view']))
{
	if('phrase' == $_GET['view'])
	{
		$gBc[1]['title'] = $esynI18N['phrase_manager'];
	}
	elseif('search' == $_GET['view'])
	{
		$gBc[1]['title'] = $esynI18N['search_in_phrases'];
	}
	elseif('download' == $_GET['view'])
	{
		$gBc[1]['title'] = $esynI18N['download_lang'];
	}
	elseif('compare' == $_GET['view'])
	{
		$gBc[1]['title'] = $esynI18N['compare_languages'];
	}
	elseif('add_lang' == $_GET['view'])
	{
		$gBc[1]['title'] = $esynI18N['copy_language'];
	}
	elseif('add' == $_GET['view'])
	{
		$gBc[1]['title'] = $esynI18N['add_phrase'];
	}
}

$gTitle = isset($gBc[1]['title']) ? $gBc[1]['title'] : $esynI18N['manage_language'];

$actions = array(
	array("url" => "controller.php?file=language", "icon" => "view_language.png", "label" => $esynI18N['view']),
	array("url" => "controller.php?file=language&amp;view=phrase", "icon" => "search_language.png", "label" => $esynI18N['search_in_phrases']),
	array("url" => "#", "icon" => "add.png", "label" => $esynI18N['add_phrase'], "attributes" => 'id="add_phrase"'),
	array("url" => "controller.php?file=language&amp;view=download", "icon" => "download_language.png", "label" => $esynI18N['download_upload']),
	array("url" => "controller.php?file=language&amp;view=add_lang", "icon" => "add_language.png", "label" => $esynI18N['new_language']),
	array("url" => "controller.php?file=language&amp;view=compare", "icon" => "compare_languages.png", "label" => $esynI18N['compare_languages'])
);

require_once(ESYN_ADMIN_HOME.'view.php');

$categories = array(
	'admin'		=> 'Administration Board',
	'frontend'	=> 'User Frontend',
	'common'	=> 'Common'
);

$esynSmarty->assign('categories', $categories);

$esynSmarty->display('language.tpl');

?>
