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


define("ESYN_REALM", "blocks");

esynUtil::checkAccess();

$esynAdmin->factory("Block");

/*
 * ACTIONS
 */
if(isset($_POST['save']))
{
	$esynAdmin->startHook('adminAddBlockValidation');

	if(!defined('ESYN_NOUTF'))
	{
		require_once(ESYN_CLASSES.'esynUtf8.php');

		esynUtf8::loadUTF8Core();
		esynUtf8::loadUTF8Util('ascii', 'validation', 'bad', 'utf8_to_ascii');
	}

	$error = false;

	$block = array();

	$block['position'] = $_POST['position'];
	$block['type'] = $_POST['type'];
	$block['show_header'] = isset($_POST['show_header']) ? '1' : '0';
	$block['collapsible'] = isset($_POST['collapsible']) ? '1' : '0';
	$block['collapsed'] = isset($_POST['collapsed']) ? '1' : '0';
	$block['multi_language'] = isset($_POST['multi_language']) ? '1' : '0';
	$block['sticky'] = isset($_POST['sticky']) ? '1' : '0';
	$block['visible_on_pages'] = isset($_POST['visible_on_pages']) ? $_POST['visible_on_pages'] : '';

	if('1' == $block['multi_language'])
	{
		$block['title'] = $_POST['multi_title'];
		
		if(empty($block['title']))
		{
			$error = true;
			$msg[] = $esynI18N['error_title'];
		}
		elseif(!utf8_is_valid($block['title']))
		{
			$block['title'] = utf8_bad_replace($block['title']);
		}
		
		$block['contents'] = $_POST['multi_contents'];
		
		if(empty($block['contents']))
		{
			$error = true;
			$msg[] = $esynI18N['error_contents'];
		}
		
		if ('html' != $block['type'])
		{
			if(!utf8_is_valid($block['contents']))
			{
				$block['contents'] = utf8_bad_replace($block['contents']);
			}
		}
	}
	else
	{
		if(isset($_POST['block_languages']) && !empty($_POST['block_languages']))
		{
			$block['block_languages'] = $_POST['block_languages'];
			$block['title'] = $_POST['title'];
			$block['contents'] = $_POST['contents'];

			foreach($block['block_languages'] as $block_language)
			{
				if(isset($block['title'][$block_language]))
				{
					if(empty($block['title'][$block_language]))
					{
						$error = true;
						$msg[] = str_replace('{lang}', $esynAdmin->mLanguages[$block_language], $esynI18N['error_lang_title']);
					}
					elseif(!utf8_is_valid($block['title'][$block_language]))
					{
						$block['title'][$block_language] = utf8_bad_replace($block['title'][$block_language]);
					}
				}

				if(isset($block['contents'][$block_language]))
				{
					if(empty($block['contents'][$block_language]))
					{
						$error = true;
						$msg[] = str_replace('{lang}', $esynAdmin->mLanguages[$block_language], $esynI18N['error_lang_contents']);
					}
					
					if ('html' != $block['type'])
					{
						if(!utf8_is_valid($block['contents'][$block_language]))
						{
							$block['contents'][$block_language] = utf8_bad_replace($block['contents'][$block_language]);
						}
					}
				}
			}
		}
		else
		{
			$error = true;
			$msg[] = $esynI18N['block_languages_empty'];
		}
	}

	if(!$error)
	{
		if('edit' == $_POST['do'])
		{
			$result = $esynBlock->update($block, (int)$_POST['id']);

			if($result)
			{
				$msg[] = $esynI18N['changes_saved'];
			}
			else
			{
				$error = true;
				$msg[] = $esynBlock->getMessage();
			}
		}
		else
		{
			$result = $esynBlock->insert($block);

			if($result)
			{
				$msg[] = $esynI18N['block_created'];
			}
			else
			{
				$error = true;
				$msg[] = $esynBlock->getMessage();
			}
		}

		$do = (isset($_POST['goto']) && 'add' == $_POST['goto']) ? 'add' : null;
		
		esynMessages::setMessage($msg, $error);
		
		esynUtil::reload(array("do" => $do));
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

	$start = (int)$_GET['start'];
	$limit = (int)$_GET['limit'];

	$out = array('data' => '', 'total' => 0);

	if('get' == $_GET['action'])
	{
		$sort = $_GET['sort'];
		$dir = in_array($_GET['dir'], array('ASC', 'DESC')) ? $_GET['dir'] : 'ASC';

		if(!empty($sort) && !empty($dir))
		{
			$order = " ORDER BY `{$sort}` {$dir}";
		}

		$out['total'] = $esynBlock->one("COUNT(*)");
		$out['data'] = $esynBlock->all("*, `id` `edit`, '1' `remove`", "1=1{$order}", array(), $start, $limit);

		if($out['data'])
		{
			foreach($out['data'] as $key => $block)
			{
				$esynAdmin->setTable("block_show");
				$pages = $esynAdmin->all("*", "`block_id` = :id", array('id' => $block['id']));
				$esynAdmin->resetTable();

				if($pages)
				{
					foreach($pages as $page)
					{
						$out['data'][$key]["visible_on_pages[{$page['page']}]"] = 1;
					}
				}

				if('0' == $block['multi_language'])
				{
					$esynAdmin->setTable("language");
					$title_languages = $esynAdmin->keyvalue("`code`, `value`", "`key` = 'block_title_blc{$block['id']}'");
					$esynAdmin->resetTable();

					if(!empty($title_languages))
					{
						if(!empty($title_languages[ESYN_LANGUAGE]))
						{
							$out['data'][$key]['title'] = $title_languages[ESYN_LANGUAGE];
						}
						else
						{
							unset($title_languages[ESYN_LANGUAGE]);

							foreach($title_languages as $title_language)
							{
								if(!empty($title_language))
								{
									$out['data'][$key]['title'] = $title_language;

									break;
								}
							}
						}
					}
				}
			}
		}

		if(empty($out['data']))
		{
			$out['data'] = '';
		}
	}

	echo $json->encode($out);
	exit;
}

if(isset($_POST['action']))
{
	$esynAdmin->loadClass("JSON");
	
	$json = new Services_JSON();

	$out = array('msg' => '', 'error' => true);
	
	if('update' == $_POST['action'])
	{
		$result = $esynBlock->update(array($_POST['field'] => $_POST['value']), $_POST['ids']);

		if($result)
		{
			$out['error'] = false;
			$out['msg'] = $esynI18N['changes_saved'];
		}
		else
		{
			$out['error'] = true;
			$out['msg'] = $esynBlock->getMessage();
		}
	}

	if('remove' == $_POST['action'])
	{
		$result = $esynBlock->delete($_POST['ids']);

		if($result)
		{
			$out['error'] = false;
			$out['msg'] =  $esynI18N['changes_saved'];
		}
		else
		{
			$out['error'] = true;
			$out['msg'] = $esynBlock->getMessage();
		}
	}

	echo $json->encode($out);
	exit;
}
/*
 * ACTIONS
 */

$gNoBc = false;

$gTitle = $esynI18N['manage_blocks'];

$gBc[0]['title'] = $esynI18N['manage_blocks'];
$gBc[0]['url'] = 'controller.php?file=blocks';

if(isset($_GET['do']))
{
	if(('edit' == $_GET['do']) || ('add' == $_GET['do']))
	{
		$gBc[0]['title'] = $esynI18N['manage_blocks'];
		$gBc[0]['url'] = 'controller.php?file=blocks';

		$gBc[1]['title'] = ('add' == $_GET['do']) ? $esynI18N['add_block'] : $esynI18N['edit_block'];
		$gTitle = $gBc[1]['title'];
	}
}

$actions = array(
	array("url" => "controller.php?file=blocks&amp;do=add", "icon" => "add_block.png", "label" => $esynI18N['create']),
	array("url" => "controller.php?file=blocks", "icon" => "view_block.png", "label" => $esynI18N['view']),
	array("url" => "controller.php?file=visual", "icon" => "visual_mode.png", "label" => $esynI18N['visual_mode'], "attributes" => 'target="_blank"'),
);

require_once(ESYN_ADMIN_HOME.'view.php');

if(isset($_GET['do']))
{
	if('edit' == $_GET['do'])
	{
		$block = $esynBlock->row("*", "`id` = :id", array('id' => (int)$_GET['id']));

		if('0' == $block['multi_language'])
		{
			$esynAdmin->setTable("language");
			
			$block['block_languages'] = $esynAdmin->onefield("code", "`key` = 'block_content_blc{$block['id']}'");

			$block['title'] = $esynAdmin->keyvalue("`code`, `value`", "`key` = 'block_title_blc{$block['id']}'");
			$block['contents'] = $esynAdmin->keyvalue("`code`, `value`", "`key` = 'block_content_blc{$block['id']}'");
			
			$esynAdmin->resetTable();
		}

		$esynAdmin->setTable("block_show");
		$visibleOn = $esynAdmin->onefield("`page`", "`block_id` = :id", array('id' => (int)$_GET['id']));
		$esynAdmin->resetTable();
		
		if(empty($visibleOn))
		{
			$visibleOn = array();
		}

		$esynSmarty->assign('block', $block);
		$esynSmarty->assign('visibleOn', $visibleOn);
	}

	if('add' == $_GET['do'])
	{
		$visibleOn = isset($_POST['visible_on_pages']) ? $_POST['visible_on_pages'] : array();

		$esynSmarty->assign('visibleOn', $visibleOn);
	}

	$esynAdmin->setTable("pages");
	$pages = $esynAdmin->all("*");
	$pages_group = $esynAdmin->onefield("`group`", "1=1 GROUP BY `group` ORDER BY `group` ASC");
	$esynAdmin->resetTable();

	if(!empty($pages))
	{
		$esynAdmin->setTable("language");

		foreach($pages as $key => $page)
		{
			$pages[$key]['title'] = $esynAdmin->one("`value`", "`key` = 'page_title_{$page['name']}' AND `code` = '" . ESYN_LANGUAGE . "'");
		}

		$esynAdmin->resetTable();
	}

	$esynSmarty->assign('types', $esynBlock->types);
	$esynSmarty->assign('positions', $esynBlock->positions);
	$esynSmarty->assign('pages_group', $pages_group);
	$esynSmarty->assign('pages', $pages);
}

$esynSmarty->display('blocks.tpl');
