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


define("ESYN_REALM", "create_listing");

esynUtil::checkAccess();

$esynAdmin->factory("Category", "Listing", "Account", "ListingField");

$imgtypes = array(
	"image/gif"=>"gif",
	"image/jpeg"=>"jpg",
	"image/pjpeg"=>"jpg",
	"image/png"=>"png"
);

$error = false;
$msg = array();

$id = isset($_GET['id']) && ctype_digit($_GET['id']) ? $_GET['id'] : 0;

if(isset($_GET['do']) && 'edit' == $_GET['do'])
{
	$error = false;

	/** get listing information **/
	$listing = $old_listing = $esynListing->row("*", "`id` = {$id}");

	/** get information about parent category **/
	$category = $esynCategory->row("*", "`id` = '{$listing['category_id']}'");

	$esynCategory->setTable("flat_structure");
	$parents = $esynCategory->all("`parent_id`", "`category_id` = :id", array('id' => $category['id']));
	$esynCategory->resetTable();

	/** get account info **/
	if($listing['account_id'] > 0)
	{
		$account = $esynAccount->row("*", "`id` = '{$listing['account_id']}'");
	}

	if(!empty($parents))
	{
		foreach($parents as $parent)
		{
			$category['parents'][] = $parent['parent_id'];
		}

		$category['parents'] = array_reverse($category['parents']);
		array_pop($category['parents']);
		
		$category['parents'] = '/'.join('/', $category['parents']).'/';
	}

	$esynAdmin->startHook('adminSuggestListingEditSection');
}
else
{
	$listing = array();

	/** get information about parent category **/
	$category = $esynCategory->row("*", "`id` = '{$id}'");
}

$parent = $esynCategory->row("*", "`id` = '{$category['id']}'");

/** get extra fields **/
$fields = $esynListingField->all("*","1 ORDER BY `order`");

if (isset($_POST['save']))
{
	$esynAdmin->startHook('adminSuggestListingValidation');

	if(!defined('ESYN_NOUTF'))
	{
		require_once(ESYN_CLASSES.'esynUtf8.php');

		esynUtf8::loadUTF8Core();
		esynUtf8::loadUTF8Util('ascii', 'validation', 'bad', 'utf8_to_ascii');
	}

	$account = array();

	$listing['status'] = esynValidator::isStatus($_POST['status']) ? $_POST['status'] : '';
	$listing['ip_address'] = $_SERVER['REMOTE_ADDR'];
	$listing['email'] = trim($_POST['email']);

	$listing['rank'] = isset($_POST['rank']) && $_POST['rank'] > 0 ? (int)$_POST['rank'] : '0';

	if(isset($_POST['date']) && !empty($_POST['date']))
	{
		$listing['date'] = esynSanitize::sql($_POST['date']);
	}

	$listing['category_id'] = $cid = $category['id'];
	// if parent choosed from the TREE
	if(isset($_POST['category_id']) && ctype_digit($_POST['category_id']))
	{
		$listing['category_id'] = (int)$_POST['category_id'];

		if(!$esynCategory->exists("`id`='".$listing['category_id']."'"))
		{
			$listing['category_id'] = $category['id'];
		}
	}

	if($esynConfig->getConfig('expiration_period') > 0 && isset($_POST['expire']) && !empty($_POST['expire']))
	{
		if(!ctype_digit($_POST['expire']))
		{
			$error = true;
			$msg[] = $esynI18N['expiration_period_incorrect'];
		}
		else
		{
			$listing['expire'] = (int)$_POST['expire'];
		}

		if(isset($_POST['action_expire']) && !empty($_POST['action_expire']))
		{
			$listing['action_expire'] = $_POST['action_expire'];
		}
		else
		{
			$listing['action_expire'] = $esynConfig->getConfig('expiration_action');
		}
	}

	// account auto-creating 
	if(isset($_POST['assign_account']) && '0' != $_POST['assign_account'])
	{
		if('1' == $_POST['assign_account'])
		{
			$account['username'] = $_POST['new_account'];
			$account['email'] = $_POST['new_account_email'];
			$account['status'] = 'active';

			/** check username **/
			if (!preg_match("/^[\w\s]{3,30}$/", $account['username']))
			{
				$error = true;
				$msg[] = $esynI18N['error_username_incorrect'];
			}
			elseif (empty($account['username']))
			{
				$error = true;
				$msg[] = $esynI18N['error_username_empty'];
			}
			elseif ($esynAccount->exists("`username`='".$account['username']."'"))
			{
				$error = true;
				$msg[] = $esynI18N['error_username_exists'];
			}
			else
			{
				$account['username'] = esynSanitize::sql($account['username']);
			}

			/** check email **/
			if (!esynValidator::isEmail($account['email']))
			{
				$error = true;
				$msg[] = $esynI18N['error_email_incorrect'];
			}
			elseif ($esynAccount->exists("`email`='".esynSanitize::sql($account['email'])."'"))
			{
				$error = true;
				$msg[] = $esynI18N['account_email_exists'];
			}
			else
			{
				$account['email'] = esynSanitize::sql($account['email']);
			}	

			/*if(!$error)
			{
				$listing['account_id'] = $esynAccount->registerAccount($account);
			}*/
		}

		if('2' == $_POST['assign_account'])
		{
			if(isset($_POST['account']))
			{
				//$listing['account_id'] = (int)$_POST['account'];
				$account['account_id'] = (int)$_POST['account'];
			}
		}
	}

	if(!empty($listing['email']) && !esynValidator::isEmail($listing['email']))
	{
		$error = true;
		$msg[] = $esynI18N['error_email_incorrect'];
	}
	else
	{
		$listing['email'] = esynSanitize::sql($listing['email']);
	}
	
	if ($fields)
	{
		$url_required = false;

		foreach($fields as $key=>$value)
		{
			$field_name = $value['name'];
			
			if(in_array($value['type'], array('storage', 'image', 'pictures')))
			{
				if(isset($_FILES[$field_name]))
				{
					if(!is_writeable(ESYN_HOME.'uploads'.ESYN_DS))
					{
						$error = true;
						$msg[] = $esynI18N['upload_writable_permission'];
					}
				}
				else
				{
					continue;
				}
			}
			else
			{
				if (is_array($_POST[$field_name]))
				{
					$field_value = join(",", $_POST[$field_name]);
					$field_value = trim($field_value, ',');
				}
				else
				{
					$field_value = $_POST[$field_name];
				}

				if(!utf8_is_valid($field_value))
				{
					$field_value = utf8_bad_replace($field_value);
					trigger_error("Bad UTF-8 detected (replacing with '?') in suggest listing", E_USER_NOTICE);
				}
			}

			/** magic quotes stripping for text and textarea fields **/
			if (($value['type'] == 'text') || ($value['type'] == 'textarea'))
			{
				$listing[$field_name] = $field_value;

				if ($field_name == 'url')
				{
					$url_required = $value['required'];
				}

				if ($field_name == 'title')
				{
					if (utf8_strlen($listing['title']) > (int)$value['length'])
					{
						$listing['title'] = utf8_substr($listing['title'], 0, (int)$value['length']);
					}
				}
			}
			elseif ('storage' == $value['type'])
			{
				if (!$_FILES[$field_name]['error'])
				{
					$ext = substr($_FILES[$field_name]['name'], -3);
					$token = esynUtil::getNewToken();
					
					$file_name = $value['file_prefix'].$cid."-".$token.".".$ext;
					if (esynUtil::upload($field_name, ESYN_HOME.'uploads'.ESYN_DS.$file_name))
					{
						$listing[$field_name] = $file_name;
					}
					else
					{
						$error = true;
						$msg[] = $esynI18N['unknown_upload'];
					}
				}
			}
			elseif ('image' == $value['type'])
			{
				if (isset($_FILES[$field_name]) && !$_FILES[$field_name]['error'])
				{
					if(is_uploaded_file($_FILES[$field_name]['tmp_name']))
					{
						$ext = strtolower(utf8_substr($_FILES[$field_name]['name'], -3));

						// if 'jpeg'
						if($ext == 'peg')
						{
							$ext = 'jpg';
						}

						if(!array_key_exists($_FILES[$field_name]['type'], $imgtypes) || !in_array($ext, $imgtypes) || !getimagesize($_FILES[$field_name]['tmp_name']))
						{
							$error	= true;
							$a		= join(",",array_unique($imgtypes));
							$tmp 	= str_replace("{types}", $a, $esynI18N['wrong_image_type']);
							$tmp 	= str_replace("{name}", $field_name, $tmp);

							$msg[] = $tmp;
						}
						else
						{
							if(isset($_GET['do']) && 'edit' == $_GET['do'])
							{
								if(!empty($listing[$field_name]) && file_exists(ESYN_HOME.'uploads'.ESYN_DS.$listing[$field_name]))
								{
									unlink(ESYN_HOME.'uploads'.ESYN_DS.$listing[$field_name]);
								}

								if(!empty($listing[$field_name]) && file_exists(ESYN_HOME.'uploads'.ESYN_DS.'small_'.$listing[$field_name]))
								{
									unlink(ESYN_HOME.'uploads'.ESYN_DS.'small_'.$listing[$field_name]);
								}
							}

							$esynAdmin->loadClass("Image");

							$token = esynUtil::getNewToken();

							$file_name = $value['file_prefix'].$cid."-".$token.".".$ext;

							$listing[$field_name] = $file_name;
							
							if($value['thumb_width'] > 0 || $value['thumb_height'] > 0)
							{
								$fname = ESYN_HOME.'uploads'.ESYN_DS.'small_'.$file_name;

								$image = new esynImage();

								$image->processImage($_FILES[$field_name], $fname, $value['thumb_width'], $value['thumb_height'], $value['resize_mode']);

							}
							
							if($value['image_width'] > 0 || $value['image_height'] > 0)
							{
								$fname = ESYN_HOME.'uploads'.ESYN_DS.$file_name;

								$image = new esynImage();

								$image->processImage($_FILES[$field_name], $fname, $value['image_width'], $value['image_height'], $value['resize_mode']);
							}
							else
							{
								$fname = ESYN_HOME.'uploads'.ESYN_DS.$file_name;
								
								@move_uploaded_file($_FILES[$field_name]['tmp_name'], $fname);
							}
						}
					}
				}
			}
			elseif ('pictures' == $value['type'])
			{
				$picture_names = array();

				foreach($_FILES[$field_name]['tmp_name'] as $key => $tmp_name)
				{
					if ((bool)$value['required'] && (bool)$_FILES[$field_name]['error'][$key])
					{
						$error = true;
						$err_mes = str_replace('{field}', $esynI18N['field_'.$field_name], $esynI18N['field_is_empty']);
						$msg[] = $err_mes;
					}
					else
					{
						if (@is_uploaded_file($_FILES[$field_name]['tmp_name'][$key]))
						{
							$ext = strtolower(utf8_substr($_FILES[$field_name]['name'][$key], -3));

							// if jpeg
							if ($ext == 'peg')
							{
								$ext = 'jpg';
							}

							if (!array_key_exists($_FILES[$field_name]['type'][$key], $imgtypes) || !in_array($ext, $imgtypes, true) || !getimagesize($_FILES[$field_name]['tmp_name'][$key]))
							{
								$error = true;

								$a = implode(",",array_unique($imgtypes));

								$err_msg = str_replace("{types}", $a, $esynI18N['wrong_image_type']);
								$err_msg = str_replace("{name}", $field_name, $err_msg);

								$msg[] = $err_msg;
							}
							else
							{
								$esynAdmin->loadClass("Image");

								$token = esynUtil::getNewToken();

								$file_name = $value['file_prefix'].$cid."-".$token.".".$ext;

								$picture_names[] = $file_name;

								$file = array();
								
								foreach ($_FILES[$field_name] as $key1 => $tmp_name)
								{
									$file[$key1] = $_FILES[$field_name][$key1][$key];
								}

								if($value['thumb_width'] > 0 || $value['thumb_height'] > 0)
								{
									$fname = ESYN_HOME . 'uploads' . ESYN_DS . 'small_' . $file_name;

									$image = new esynImage();

									$image->processImage($file, $fname, $value['thumb_width'], $value['thumb_height'], $value['resize_mode']);

								}

								if($value['image_width'] > 0 || $value['image_height'] > 0)
								{
									$fname = ESYN_HOME.'uploads'.ESYN_DS.$file_name;
								
									$image = new esynImage();

									$image->processImage($file, $fname, $value['image_width'], $value['image_height'], $value['resize_mode']);
								}
								else
								{
									$fname = ESYN_HOME.'uploads'.ESYN_DS.$file_name;
								
									@move_uploaded_file($_FILES[$field_name]['tmp_name'][$key], $fname);
								}
							}
						}
					}
				}

				if(!empty($picture_names))
				{
					if(isset($_GET['do']) && 'edit' == $_GET['do'])
					{
						if(!empty($listing[$field_name]))
						{
							$exists_picture_names = explode(',', $listing[$field_name]);

							$picture_names = array_merge($picture_names, $exists_picture_names);
						}
					}

					$listing[$field_name] = implode(',', $picture_names);
				}
			}
			else
			{
				$listing[$field_name] = $field_value;
			}
		}
	}

	/*if (!$listing['description'])
	{
		$error = true;
		$msg[] = $esynI18N['error_description'];
	}*/

	if(isset($listing['url']) && !empty($listing['url']))
	{
		$listing['url'] = trim($listing['url']);
		
		if(FALSE === strstr($listing['url'], 'http'))
		{
			$listing['url']  = "http://".$listing['url'];
		}
	}

	$valid_url = esynValidator::isUrl($_POST['url']);

	if ($url_required && !$valid_url)
	{
		$error = true;
		$msg[] = $esynI18N['error_url'];
	}
	else
	{
		$listing['domain'] = '';
		$listing['listing_header'] = '200';
		$listing['recip_valid'] = 0;
		
		$listing['pagerank'] = $esynConfig->getConfig('pagerank') ? esynUtil::getPageRank($listing['url']) : -1;
		
		/** get domain name **/
		$listing['domain'] = esynUtil::getDomain($listing['url']);

		/** check broken url **/
		$listing['listing_header'] = 200;
		if ($esynConfig->getConfig('listing_check') && !$esynConfig->getConfig('broken_visitors'))
		{
			$listing['listing_header'] = esynUtil::getListingHeader($listing['url']);
		
			$correct_headers = explode(',', $esynConfig->getConfig('http_headers'));
			if (!in_array($listing['listing_header'], $correct_headers))
			{
				$error = true;
				$msg[] = $esynI18N['error_broken_listing'];
			}		
		}
	}
	
	if (!$listing['title'])
	{
		$error = true;
		$msg[] = $esynI18N['title_incorrect'];
	}

	/** check reciprocal link **/
	if ($valid_url && esynValidator::isUrl($_POST['reciprocal']) && $esynConfig->getConfig('reciprocal_check') && $esynConfig->getConfig('reciprocal_visitors'))
	{
		if ($esynConfig->getConfig('reciprocal_domain'))
		{
			if (esynUtil::getDomain($_POST['reciprocal']) != esynUtil::getDomain($_POST['url']))
			{
				$error = true;
				$msg[] = 'Reciprocal link seems to be placed on different domain.';
			}
		}

		$listing['recip_valid'] = esynValidator::hasUrl($_POST['reciprocal'], $esynConfig->getConfig('reciprocal_text'));
		
		if (!$listing['recip_valid'])
		{
			$error = true;
			$msg[] = $esynI18N['no_backlink'];
		}
	}

	/** check duplicate link **/
	if ($esynConfig->getConfig('duplicate_checking') && !$esynConfig->getConfig('duplicate_visitors'))
	{
		if(!isset($_GET['do']) || (isset($_GET['do']) && 'edit' == $_GET['do'] && $old_listing['url'] != $_POST['url']))
		{
			$x = $esynConfig->getConfig('duplicate_domain');
			$forcheck = '';
			if($x)
			{
				$forcheck = $listing['domain'];
				$field = 'domain';
			}
			elseif($valid_url)
			{
				$forcheck = $_POST['url'];	
				$field = 'url';
			}

			if (!empty($forcheck) && $esynListing->exists("`".$field."`='".$forcheck."'"))
			{
				$error = true;
				$msg[] = $esynI18N['error_listing_present'];
			}
		}
	}

	if (!$error)
	{
		$listing['_notify'] = isset($_POST['send_email']) ? (bool)$_POST['send_email'] : false;

		$additional = array();
		
		$listing['featured'] = (int)$_POST['featured'];
		if('1' == $_POST['featured'])
		{
			$additional['featured_start'] = 'NOW()';
		}
		else
		{
			$listing['featured_start'] = '0000-00-00 00:00:00';
		}
		
		$listing['partner'] = (int)$_POST['partner'];
		if('1' == $_POST['partner'])
		{
			$additional['partner_start'] = 'NOW()';
		}
		else
		{
			$listing['partner_start'] = '0000-00-00 00:00:00';
		}

		if(isset($_POST['do']) && 'edit' == $_POST['do'])
		{
			$esynAdmin->startHook('phpAdminSuggestListingBeforeListingUpdate');
				
			$listing_new_id = $esynListing->update($listing, "`id` = '{$id}'", $additional);

			$listing_new_id = $id;

			$msg[] = $esynI18N['changes_saved'];
		}
		else
		{
			$esynAdmin->startHook('phpAdminSuggestListingBeforeListingInsert');
			
			$listing_new_id = $esynListing->insert($listing, $additional);
			
			$esynAdmin->startHook('phpAdminSuggestListingAfterListingInsert');
			
			$msg[] = $esynI18N['listing_added'];
		}

		if (!empty($account))
		{
			if (isset($account['account_id']))
			{
				$listing['account_id'] = $account['account_id'];
			}
			else
			{
				$listing['path'] = $esynCategory->one("`path`", "`id` = '{$listing['category_id']}'");
				$listing['id'] = $listing_new_id;
				$listing['account_id'] = $esynAccount->registerAccount($account, $listing);
			}

			$esynAdmin->setTable("listings");
			$esynAdmin->update(array('account_id' => $listing['account_id']), "`id` = '{$listing_new_id}'");
			$esynAdmin->resetTable();
		}

		$esynCategory->adjustNumListings($listing['category_id']);
		$esynCategory->adjustNumListings($category['id']);

		$parent = $esynCategory->one("parent_id", "`id`='".$listing['category_id']."'");		

		esynMessages::setMessage($msg, $error);

		if(isset($_POST['goto']))
		{
			if('add' == $_POST['goto'])
			{
				esynUtil::reload();
			}
			elseif('browse' == $_POST['goto'])
			{
				esynUtil::go2("controller.php?file=browse&id={$listing['category_id']}");
			}
			elseif('addtosame' == $_POST['goto'])
			{
				esynUtil::go2("controller.php?file=suggest-listing&id={$listing['category_id']}");
			}
			elseif('list' == $_POST['goto'])
			{
				$status = isset($_GET['status']) && in_array($_GET['status'], array('active', 'approval', 'banned')) ? '&status=' . $_GET['status'] : '';

				esynUtil::go2("controller.php?file=listings" . $status);
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
	if('clear' == $_GET['action'])
	{
		$esynAdmin->loadClass("JSON");

		$json = new Services_JSON();

		$esynAdmin->setTable('listings');

		$imageName = $esynAdmin->one($_GET['field'], "`id` = :id", array('id' => $_GET['id']));

		$imageName = explode(',', $imageName);

		foreach($imageName as $key => $image)
		{
			if($image == $_GET['image'])
			{
				if(file_exists(ESYN_HOME.'uploads'.ESYN_DS.$image))
				{
					unlink(ESYN_HOME.'uploads'.ESYN_DS.$image);
				}

				if(file_exists(ESYN_HOME.'uploads'.ESYN_DS.'small_'.$image))
				{
					unlink(ESYN_HOME.'uploads'.ESYN_DS.'small_'.$image);
				}

				unset($imageName[$key]);
			}
		}

		$imageName = implode(',', $imageName);

		$esynAdmin->update(array($_GET['field'] => $imageName), "`id` = :id", array('id' => $_GET['id']));
		
		$esynAdmin->resetTable();

		$out['error'] = false;
		$out['msg'] = $esynI18N['image_deleted'];

		echo $json->encode($out);
		exit;
	}

	if('getaccounts' == $_GET['action'])
	{
		$esynAdmin->loadClass("JSON");

		$json = new Services_JSON();

		$query = isset($_GET['query']) ? esynSanitize::sql(trim($_GET['query'])) : '';

		$esynAdmin->setTable("accounts");
		$out['data'] = $esynAdmin->all("`id`, `username`", "`username` LIKE '{$query}%'");
		$out['total'] = $esynAdmin->one("COUNT(*)", "`username` LIKE '{$query}%'");
		$esynAdmin->resetTable();

		if(empty($out['data']))
		{
			$out['data'] = "";
		}

		echo $json->encode($out);
		exit;
	}
}

$gNoBc = false;

$gBc = array();
$gBc[0]['title'] = $esynI18N['browse'];
$gBc[0]['url'] = 'controller.php?file=browse';

$gBc[1]['title'] = $esynI18N['create_listing'];
$gBc[1]['url'] = 'controller.php?file=suggest-listing&amp;id='.$id;

$gTitle = $esynI18N['create_listing'];

if(isset($_GET['do']))
{
	if('edit' == $_GET['do'])
	{
		$gBc[1]['title'] = $esynI18N['edit_listing'];
		$gBc[1]['url'] = 'controller.php?file=suggest-listing&amp;do=edit&amp;id='.$id;

		$gTitle = $esynI18N['edit_listing'];
	}
}

require_once(ESYN_ADMIN_HOME.'view.php');

$esynSmarty->assign('listing', $listing);
$esynSmarty->assign('category', $category);
$esynSmarty->assign('fields', $fields);
$esynSmarty->assign('parent', $parent);

if(isset($account) && !empty($account))
{
	$esynSmarty->assign('account', $account);
}

$esynSmarty->display('suggest-listing.tpl');

?>
