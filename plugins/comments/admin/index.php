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


define("ESYN_REALM", "comments");

/*
 * ACTIONS
 */
if(isset($_POST['edit_comments']) && isset($_POST['id']))
{
	if(!defined('ESYN_NOUTF'))
	{
		require_once(ESYN_CLASSES.'esynUtf8.php');

		esynUtf8::loadUTF8Core();
		esynUtf8::loadUTF8Util('ascii', 'validation', 'bad', 'utf8_to_ascii');
	}

	$comment = array();

	$comment['id'] = (int)$_POST['id'];
	$comment['author'] = esynSanitize::html($_POST['author']);
	$comment['url'] = esynSanitize::html($_POST['url']);
	
	if(utf8_is_valid($comment['author']))
	{
		$comment['author'] = utf8_bad_replace($comment['author']);
	}

	$comment['body'] = $_POST['body'];
	
	if(isset($_POST['status']))
	{
		$comment['status'] = in_array($_POST['status'], array('active', 'inactive')) ? $_POST['status'] : 'inactive';
	}

	if(isset($_POST['email']) && esynValidator::isEmail($_POST['email']))
	{
		$comment['email'] = $_POST['email'];
	}
	
	$esynAdmin->setTable("comments");
	$esynAdmin->update($comment);
	$esynAdmin->resetTable();

	$msg = $esynI18N['changes_saved'];

	esynMessages::setMessage($msg, false);

	esynUtil::reload(array("do" => null, "id" => null));
}

/*
 * AJAX
 */
if(isset($_GET['action']))
{
	$esynAdmin->loadClass("JSON");
	
	$json = new Services_JSON();
	
	if('get' == $_GET['action'])
	{
		$start = (int)$_GET['start'];
		$limit = (int)$_GET['limit'];

		$out = array('data' => '', 'total' => 0);

		$esynAdmin->setTable("comments");

		$out['total'] = $esynAdmin->one("COUNT(*)");
		$out['data'] = $esynAdmin->all("*, `id` `edit`", "1=1", $start, $limit);
		
		$esynAdmin->resetTable();

		if($out['data'])
		{
			$esynAdmin->setTable("listings");
			
			$out['data'] = esynSanitize::applyFn($out['data'], "html", array('body'));
			//$out['data'] = esynSanitize::applyFn($out['data'], "striptags", array('description'));
			
			foreach($out['data'] as $key => $comment)
			{
				$out['data'][$key]['listing'] = $esynAdmin->one("title", "`id` = '{$comment['listing_id']}'");
			}
			
			$esynAdmin->resetTable();
		}
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

	if('remove' == $_POST['action'])
	{
		$out = array('msg' => 'Unknow error', 'error' => true);

		$comments = $_POST['ids'];

		if(!is_array($comments) || empty($comments))
		{
			$out['msg'] = 'Wrong params';
			$out['error'] = true;
		}
		else
		{
			$comments = array_map(array('esynSanitize', 'sql'), $comments);
			$out['error'] = false;
		}

		if(!$out['error'])
		{
			if(is_array($comments))
			{
				foreach($comments as $comment)
				{
					$ids[] = (int)$comment;
				}

				$where = "`id` IN ('".join("','", $ids)."')";
			}
			else
			{
				$id = (int)$comments;

				$where = "`id` = '{$id}'";
			}

			$esynAdmin->setTable("comments");
			$esynAdmin->delete($where);
			$esynAdmin->resetTable();

			$out['msg'] = (count($comments) > 1) ? $esynI18N['comments'] : $esynI18N['comment'];
			$out['msg'] .= ' '.$esynI18N['deleted'];
			
			$out['error'] = false;
		}
	}

	if('update' == $_POST['action'])
	{
		$out = array('msg' => 'Unknow error', 'error' => true);

		$field = esynSanitize::sql($_POST['field']);
		$value = esynSanitize::sql($_POST['value']);

		if(is_array($_POST['ids']))
		{
			$comments = array_map(array('esynSanitize', 'sql'), $_POST['ids']);
		}
		elseif(!empty($accounts))
		{
			$comments[] = esynSanitize::sql($_POST['ids']);
		}
		else
		{
			$out['msg'] = 'Wrong params';
			$out['error'] = true;
		}

		if(!empty($field) && !empty($value) && !empty($comments))
		{
			foreach($comments as $comment)
			{
				$ids[] = (int)$comment;
			}

			$where = "`id` IN ('".join("','", $ids)."')";

			$esynAdmin->setTable("comments");
			$esynAdmin->update(array($field => $value), $where);
			$esynAdmin->resetTable();
			
			$out['msg'] = $esynI18N['changes_saved'];
			$out['error'] = false;
		}
		else
		{
			$out['msg'] = 'Wrong parametes';
			$out['error'] = true;
		}
	}
	
	echo $json->encode($out);
	exit;	
}
/*
 * ACTIONS
 */

$gNoBc = false;

$gTitle = $esynI18N['manage_comments'];

$gBc[0]['title'] = $esynI18N['manage_plugins'];
$gBc[0]['url'] = 'controller.php?file=plugins';

$gBc[1]['title'] = $esynI18N['manage_comments'];
$gBc[1]['url'] = 'controller.php?plugin=comments';

if(isset($_GET['do']))
{
	if(('edit' == $_GET['do']))
	{
		$gBc[2]['title'] = $esynI18N['edit_comment'];
		$gTitle = $gBc[2]['title'];
	}
}

require_once(ESYN_ADMIN_HOME.'view.php');

if(isset($_GET['do']) && 'edit' == $_GET['do'])
{
	$id = (int)$_GET['id'];

	$esynAdmin->setTable("comments");
	$comment = $esynAdmin->row("*", "`id` = '{$id}'");
	$esynAdmin->resetTable();

	$esynSmarty->assign('comment', $comment);
}

$esynSmarty->display(ESYN_PLUGIN_TEMPLATE.'index.tpl');