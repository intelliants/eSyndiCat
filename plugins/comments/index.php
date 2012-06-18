<?php
//##copyright##

define("ESYN_REALM", "comments");

if(isset($_POST['action']))
{
	$eSyndiCat->loadClass("JSON");
	
	if($esynConfig->getConfig('captcha') && '' != $esynConfig->getConfig('captcha_name'))
	{
		$eSyndiCat->factory("Captcha");
	}

	$json = new Services_JSON();
	
	if('add' == $_POST['action'])
	{
		$error = false;
		$msg = array();
		$comment = array();

		if(!defined('ESYN_NOUTF'))
		{
			require_once(ESYN_CLASSES.'esynUtf8.php');

			esynUtf8::loadUTF8Core();
			esynUtf8::loadUTF8Util('ascii', 'validation', 'bad', 'utf8_to_ascii');
		}

		// checking author
		if(isset($_POST['author']) && !empty($_POST['author']))
		{
			$comment['author'] = $_POST['author'];
			
			/** check for author name **/
			if (!$comment['author'])
			{
				$error = true;
				$msg[] = $esynI18N['error_comment_author'];
			}		
			elseif (!utf8_is_valid($comment['author']))
			{
				$comment['author'] = utf8_bad_replace($comment['author']);
			}
		}
		else
		{
			$error = true;
			$msg[] = $esynI18N['error_comment_author'];
		}

		// checking email
		if(isset($_POST['email']) && !empty($_POST['email']))
		{
			$comment['email'] = $_POST['email'];
			
			/** check for author email **/
			if (!esynValidator::isEmail($comment['email']))
			{
				$error = true;
				$msg[] = $esynI18N['error_comment_email'];
			}
		}
		else
		{
			$error = true;
			$msg[] = $esynI18N['error_comment_email'];
		}
		
		// checking url
		if(isset($_POST['url']) && !empty($_POST['url']))
		{
			$comment['url'] = $_POST['url'];
			
			if (!esynValidator::isUrl($comment['url']))
			{
				$error = true;
				$msg[] = $esynI18N['error_url'];
			}
		}
		
		// checking body
		$comment['body'] = $_POST['body'];
		
		if (!utf8_is_valid($comment['body']))
		{
			$comment['body'] = utf8_bad_replace($comment['body']);
		}
		
		if (utf8_is_ascii($comment['body']))
		{
			$len = strlen($comment['body']);
		}
		else
		{
			$len = utf8_strlen($comment['body']);
		}
		
		/** check for minimum chars **/
		if ($esynConfig->getConfig('comment_min_chars') > 0)
		{
			if ($len < $esynConfig->getConfig('comment_min_chars'))
			{
				$error = true;
				$esynI18N['error_min_comment'] = str_replace('{minLength}', $esynConfig->getConfig('comment_min_chars'), $esynI18N['error_min_comment']);
				$msg[] = $esynI18N['error_min_comment'];
			}
		}

		/** check for minimum chars **/
		if ($esynConfig->getConfig('comment_max_chars') > 0)
		{
			if ($len > $esynConfig->getConfig('comment_max_chars'))
			{
				$error = true;
				$esynI18N['error_max_comment'] = str_replace('{maxLength}', $esynConfig->getConfig('comment_max_chars'), $esynI18N['error_max_comment']);
				$msg[] = $esynI18N['error_max_comment'];
			}
		}
		
		/** check for captcha **/
		if ($esynConfig->getConfig('captcha') && '' != $esynConfig->getConfig('captcha_name'))
		{
			if(!$esynCaptcha->validate())
			{
				$error = true;
				$msg[] = $esynI18N['error_captcha'];
			}
		}
		

		if (empty($comment['body']))
		{
			$error = true;
			$msg[] = $esynI18N['error_comment'];
		}
		else
		{
			require_once(ESYN_INCLUDES.'safehtml/safehtml.php');
			$safehtml = new safehtml();
			$comment['body'] = $safehtml->parse($comment['body']);
		}

		if(!$error)
		{
			if (!empty($esynAccountInfo['id']) && ctype_digit($esynAccountInfo['id']))
			{
				$comment['account_id'] = (int)$esynAccountInfo['id'];
			}
			
			if (!empty($_POST['rating']) && ctype_digit($_POST['rating']))
			{
				$comment['rating'] = (int)$_POST['rating'];
			}
			
			if (!empty($_POST['listing_id']) && ctype_digit($_POST['listing_id']))
			{
				$comment['listing_id'] = (int)$_POST['listing_id'];
			}

			$comment['ip_address'] = $_SERVER['REMOTE_ADDR'];
			$comment['status'] = $esynConfig->getConfig('comments_approval') ? 'active' : 'inactive';

			$eSyndiCat->setTable("comments");
			$id = $eSyndiCat->insert($comment, array("date" => "NOW()"));
			
			$out['comment'] = $eSyndiCat->row("*", "`id` = '{$id}'");
			$out['comment']['date'] = strftime($esynConfig->getConfig('date_format'), strtotime($out['comment']['date']));
			
			$eSyndiCat->resetTable();

			$esynI18N['comment_added'] .= (!$esynConfig->getConfig('comments_approval')) ? ' '.$esynI18N['comment_waits_approve'] : '';

			$msg[] = $esynI18N['comment_added'];
		}

	}

	if('vote' == $_POST['action'])
	{
		require_once(ESYN_HOME . 'plugins' . ESYN_DS . 'comments' . ESYN_DS . 'includes' . ESYN_DS . 'classes' . ESYN_DS . 'esynRating.php');

		$esynRating = new esynRating();

		if(!$esynRating->isVoted($_SERVER['REMOTE_ADDR'], $_POST['id']))
		{
			$esynRating->insert($_POST['id'], $_POST['rating'], $_SERVER['REMOTE_ADDR']);

			$rating = $esynRating->getRating($_POST['id']);

			$rating['html'] = number_format($rating['rating'], 2);
			$rating['html'] .= '&nbsp;/&nbsp;';
			$rating['html'] .= $esynConfig->getConfig('listing_rating_block_max');
			$rating['html'] .= '&nbsp;(';
			$rating['html'] .= $rating['num_votes'];
			$rating['html'] .= '&nbsp;';
			$rating['html'] .= $rating['num_votes'] > 1 ? $esynI18N['votes_cast'] : $esynI18N['vote_cast'];
			$rating['html'] .= ')&nbsp;';
			$rating['html'] .= '<span style="color: green;">';
			$rating['html'] .= $esynI18N['thanks_for_voting'];
			$rating['html'] .= '</span>';

			echo $json->encode($rating);
			exit;
		}
		else
		{
			die(" ");
		}
	}

	$out['error'] = $error;
	$out['msg'] = $msg;

	echo $json->encode($out);
	exit;
}

?>
