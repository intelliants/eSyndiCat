<?php
//##copyright##

require_once(ESYN_INCLUDES."phpmailer".ESYN_DS."class.phpmailer.php");

/**
 * esynMailer
 *
 * @uses PHPMailer
 * @package
 * @version $id$
 */
class esynMailer extends PHPMailer
{
	/**
	 * From
	 *
	 * @var string
	 * @access public
	 */
	var $From = "info@example.com";

	/**
	 * FromName
	 *
	 * @var string
	 * @access public
	 */
	var $FromName = "eSynDicat mail notifier";

	/**
	 * Host
	 *
	 * @var string
	 * @access public
	 */
	var $Host = "localhost";

	/**
	 * Mailer
	 *
	 * @var string
	 * @access public
	 */
	var $Mailer = "mail";

	/**
	 * AltBody
	 *
	 * @var mixed
	 * @access public
	 */
	var $AltBody;

	/**
	 * Body
	 *
	 * @var mixed
	 * @access public
	 */
	var $Body;

	/**
	 * mConfig
	 *
	 * @var mixed
	 * @access public
	 */
	var $mConfig;

	/**
	 * admins
	 *
	 * @var mixed
	 * @access public
	 */
	var $admins = array();

	/**
	 * actions
	 *
	 * @var array
	 * @access public
	 */
	var $actions = array(
		"listing_approval",
		"listing_submit",
		"listing_modify",
		"listing_move",
		"listing_delete",
		"listing_admin_add",
		"broken_listing_report",
		"admin_password_restoration",
		"admin_new_password_send",
		"account_approved",
		"account_disapproved",
		"account_deleted",
		"register_account",
		"account_admin_register",
		"account_change_password",
		"payment"
	);

	/**
	 * esynMailer
	 *
	 * @access public
	 * @return void
	 */
	function esynMailer()
	{
	}

	function init()
	{
		$this->From = $this->mConfig['site_email'];
		$this->Sender = $this->mConfig['site_email'];
		$this->FromName = $this->mConfig['site'];
		$this->CharSet = "utf-8";

		if('smtp' == $this->mConfig['mail_function'])
		{
			$this->Mailer = "smtp";
			$this->Host = $this->mConfig['smtp_server'];

			$user = $this->mConfig['smtp_user'];
			$pwd = $this->mConfig['smtp_password'];

			$this->Port = $this->mConfig['smtp_port'];
			$this->SMTPSecure = strtolower($this->mConfig['smtp_secure_connection']);
			$this->SMTPDebug = false;

			if(!empty($user))
			{
				$this->SMTPAuth = true;
				$this->Username = $user;
				$this->Password = $pwd;
			}
		}
		elseif('sendmail' == $this->mConfig['mail_function'])
		{
			$this->Mailer = 'sendmail';
		}
		else
		{
			$this->Mailer = 'mail';
		}
	}

	/**
	 * dispatcher
	 *
	 * Sends email by the given action
	 *
	 * @param arr $event event info (listing, category etc)
	 * @access public
	 * @return void
	 */
	function dispatcher(&$event)
	{
		if (!empty($event['params']['from']))
		{
			$this->From = $event['params']['from'];
		}

		if (!empty($event['params']['fromname']))
		{
			$this->FromName	= $event['params']['fromname'];
		}

		switch($event['action'])
		{
			/* listings actions */
			case "listing_approve":
			case "listing_disapprove":
			case "listing_submit":
			case "listing_modify":
			case "listing_reject":
			case "listing_delete":
			case "listing_move":
			case "listing_admin_add":
				$this->setListingOptions($event);
				break;

			/* accounts actions */
			case "account_register":
				$this->setAccountRegistrationOptions($event);
				break;

			case "account_admin_register":
				$this->setAccountAdminRegistrationOptions($event);
				break;

			case "account_change_password":
				$this->setChangePasswordOptions($event);
				break;

			case "account_approved":
			case "account_disapproved":
			case "account_deleted":
				$this->setAccountChangesOptions($event);
				break;
			case "account_confirm_email":
			case "account_change_email":
				$this->setAccountConfirmEmailOptions($event);
				break;

			/* other actions */
			case "broken_listing_report":
				$this->setBrokenListingReportOptions($event);
				break;

			case "admin_password_restoration":
				$this->setAdminPasswordRestorationOptions($event);
				break;

			case "admin_new_password_send":
				$this->setAdminNewPasswordOptions($event);
				break;

			case "payment":
				if(!empty($this->admins))
				{
					foreach ($this->admins as $key => $value)
					{
						if ($value['submit_notif'])
						{
							$this->sendPaymentNotif($event, $value);
						}
					}
				}
				break;

			case "item_payment":
				if(!empty($this->admins))
				{
					foreach ($this->admins as $key => $value)
					{
						if ($value['submit_notif'])
						{
							$this->sendPaymentNotifUni($event, $value);
						}
					}
				}
				break;

			case "site_error":
				$this->setErrorNotificationOptions($event);
				break;

			default:
				break;
		}

		// set recipients
		if (!empty($event['params']['rcpts']) && is_array($event['params']['rcpts']))
		{
			foreach($event['params']['rcpts'] as $addr)
			{
				$this->AddAddress($addr);
			}
		}
		elseif (isset($event['params']['item']) && !empty($event['params']['item']['email']))
		{
			$this->AddAddress($event['params']['item']['email']);
		}
		elseif(empty($event['params']['bccs']) && empty($event['params']['ccs']))
		//{
			//trigger_error("No recipient specified", E_USER_WARNING);
		//}

		if(!empty($event['params']['bccs']))
		{
			foreach($event['params']['bccs'] as $b)
			{
				$this->AddBCC($b);
			}
		}

		if(!empty($event['params']['ccs']))
		{
			foreach($event['params']['ccs'] as $b)
			{
				$this->AddCC($b);
			}
		}

		if((count($this->to) + count($this->cc) + count($this->bcc)) > 0) 
		{
			$r = $this->Send();
			
			if (!$r)
			{
				trigger_error("Error occured when sending email with subject \n Subject: '" . $this->Subject."'", E_USER_WARNING);
				if($this->IsError())
				{
					trigger_error("PHPMAILER Error '" . $this->ErrorInfo."'", E_USER_WARNING);
				}
			}
	
			$this->ClearAllRecipients();
	    }

		// Administrator notifying section
		switch($event['action'])
		{
			case "listing_submit":
			case "account_register":
			case "suggest_category":
			case "account_confirmed":
				if (!empty($this->admins))
				{
					foreach ($this->admins as $key => $value)
					{
						if ($value['submit_notif'])
						{
							$r = $this->notifyAdministrator($event, $value['email'], $value['fullname']);
						}
					}
				}
				break;
		}

		if (isset($r))
		{
			return $r;
		}
	}

	/**
	 * setErrorNotificationOptions
	 *
	 * @param mixed $event
	 * @access public
	 * @return void
	 */
	function setErrorNotificationOptions(&$event)
	{
		$this->IsHTML(false);
		$this->Subject = "Fatal error occured in your (site: ".$this->mConfig['site'].") ".$event['params']['subject'];
		$this->Body = $event['params']['body'];
	}

	/**
	 * setListingOptions
	 *
	 * @param mixed $event
	 * @access public
	 * @return void
	 */
	function setListingOptions(&$event)
	{
		$mime = $this->mConfig['mimetype'];

		$search = array(
			"{your_site_url}",
			"{your_site_title}",
			"{your_site_desc}",
			"{your_site_status}",
			"{your_site_email}",
			"{own_site}",
			"{own_url}",
			"{own_email}",
			"{own_dir_url}",
			"{dir_listing}"
		);

		$subject = $this->mConfig[$event['action']."_subject"];

		$site = $this->mConfig['site'];
		$base = ESYN_BASE_URL;
		$email = $this->mConfig['site_email'];
		$dirurl = ESYN_URL;

		if ($this->mConfig['mod_rewrite'])
		{
			$dirlink = ESYN_URL . $event['params']['path'];
			$dirlink .= !empty($event['params']['path']) ? '/' : '';
			
			$dirlink .= esynUtil::convertStr($event['params']['listing']['title']);
			$dirlink .= '-l'.$event['params']['listing']['id'].'.html';
		}
		else
		{
			$dirlink = ESYN_URL.'view-listing.php?id='.$event['params']['listing']['id'];
		}

		$listing = &$event['params']['listing'];
		$replace = array(
			$listing['url'],
			$listing['title'],
			$listing['description'],
			$listing['status'],
			$listing['email'],
			$site,
			$base,
			$email,
			$dirurl,
			$dirlink
		);

		if(isset($listing['rank']))
		{
			$search[] = "{your_site_rank}";
			$replace[] = $listing['rank'];
		}

		if($mime != 'both')
		{
			$body = $this->mConfig[$event['action']."_body_".$mime];

			$typeHtml = ($mime == 'html');
			$this->IsHTML($typeHtml);
			if ($typeHtml)
			{
				$body = <<<BKTN
				<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
				<html>
				<head>
					<title>$subject</title>
					<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
				</head>
				<body>
					$body
				</body>
				</html>
BKTN;
			}
		}
		else // if both
		{
			$altBody = $this->mConfig[$event['action']."_body_plaintext"];
			$altBody = str_replace($search, $replace, $altBody);
			$this->AltBody = $altBody;
			$body = $this->mConfig[$event['action']."_body_html"];
		}

		$subject = str_replace('{own_site}', $this->mConfig['site'], $subject);
		$this->Subject = $subject;

		$body = str_replace($search, $replace, $body);

		if($event['action'] == 'listing_delete')
		{
			$reason = isset($event['params']['reason']) && !empty($event['params']['reason']) ? $event['params']['reason'] : '';

			$body = str_replace("{reason}", $reason, $body);
		}

		$this->Body = $body;
	}

	/**
	 * notifyAdministrator
	 *
	 * Notifies administrator
	 *
	 * @param mixed $event
	 * @param mixed $addr
	 * @param mixed $adminName
	 * @access public
	 * @return void
	 */
	function notifyAdministrator(&$event, $addr, $adminName)
	{
		if ('listing_submit' == $event['action'])
		{
			$action = 'admin_notif';
		}
		elseif ('account_register' == $event['action'])
		{
			$action = 'account_register';
		}
		elseif ('suggest_category' == $event['action'])
		{
			$action = 'suggest_category';
		}
		elseif ('account_confirmed' == $event['action'])
		{
			$action = 'account_confirmed';
		}

		$mime = $this->mConfig['mimetype'];

		$subject = $this->mConfig[$action . '_subject'];

		if($mime != 'both')
		{
			$this->IsHTML($mime == 'html');
			$body = $this->mConfig[$action . '_body_' . $mime];
		}
		else
		{
			$body = $this->mConfig[$action . '_body_html'];
			$altBody = $this->mConfig[$action . '_body_plaintext'];
		}

		$subject = str_replace('{own_site}', $this->mConfig['site'], $subject);

		$search = array(
			"{admin_name}",
			"{your_site_url}",
			"{your_site_title}",
			"{your_site_desc}",
			"{your_site_email}",
			"{own_site}",
			"{own_url}",
			"{own_email}",
			"{own_dir_url}",
			"{dir_listing}",
			"{cat_title}",
			"{cat_path}",
			"{account_username}",
			"{account_email}",
			"{account_status}"
		);

		$site = $this->mConfig['site'];
		$base = ESYN_BASE_URL;
		$email = $this->mConfig['site_email'];
		$dirurl = ESYN_URL;

		if ($this->mConfig['mod_rewrite'])
		{
			$dirlink = ESYN_URL . (isset($event['params']['path']) ? $event['params']['path'] : '');

			if ($this->mConfig['use_html_path'] && isset($event['params']['path']) && ($event['params']['path'] != ""))
            {
                $dirlink = ESYN_URL.$event['params']['path'].'.html';
            }
		}
		else
		{
			$dirlink = ESYN_URL.'index.php?category=' . isset($event['params']['category_id']) ? $event['params']['category_id'] : 0 ;
		}
		$listing 	= &$event['params']['listing'];
		$category 	= &$event['params']['category'];

		$replace = array(
			$adminName,
			$listing['url'],
			$listing['title'],
			$listing['description'],
			$listing['email'],
			$site,
			$base,
			$email,
			$dirurl,
			$dirlink,
			$category['title'],
			$dirurl.$category['path'],
			@$event['params']['editor'],
			@$event['params']['email'],
			@$event['params']['status']
		);
		$this->Body = str_replace($search, $replace, $body);
		if(isset($altBody))
		{
			$this->AltBody = str_replace($search, $replace, $altBody);
		}

		unset($body, $altBody);

		$this->Subject = $subject;

		$this->AddAddress($addr);

		$r = $this->Send();
		if (!$r)
		{
			trigger_error("Admin notifier: There has been a mail error sending Subject: " . $this->Subject, E_USER_WARNING);
		}

		$this->ClearAddresses();

		return $r;
	}

	/**
	 * sendPaymentNotif
	 *
	 * Sends email when admin payment notification enabled
	 *
	 * @param mixed $event
	 * @param array $aAdmin
	 * @access public
	 * @return void
	 */
	function sendPaymentNotif ($event, $aAdmin = array())
	{
		$mime = $this->mConfig['mimetype'];

		$listing = &$event['params']['listing'];

		$subject = $this->mConfig['admin_payment_subject'];

		if($mime != 'both')
		{
			$this->IsHTML($mime == 'html');
			$body = $this->mConfig['admin_payment_body_'.$mime];
		}
		else
		{
			$body = $this->mConfig['admin_payment_body_html'];
			$altBody = $this->mConfig['admin_payment_body_plaintext'];
		}

		$subject = str_replace('{own_site}', $this->mConfig['site'], $subject);

		$body = str_replace('{admin_name}', $aAdmin['fullname'], $body);
		$body = str_replace('{your_site_url}', $listing['url'], $body);
		$body = str_replace('{your_site_title}', $listing['title'], $body);
		$body = str_replace('{your_site_desc}', $listing['description'], $body);
		$body = str_replace('{your_site_email}', $listing['email'], $body);
		$body = str_replace('{your_site_plan}', $listing['plan'], $body);
		$body = str_replace('{own_site}', $this->mConfig['site'], $body);
		$body = str_replace('{own_url}', ESYN_BASE_URL, $body);
		$body = str_replace('{own_email}', $this->mConfig['site_email'], $body);
		$body = str_replace('{own_dir_url}', ESYN_URL, $body);

		$this->Body = $body;
		unset($body);

		if(isset($altBody))
		{
			$altBody = str_replace('{admin_name}', $aAdmin['fullname'], $altBody);
			$altBody = str_replace('{your_site_url}', $listing['url'], $altBody);
			$altBody = str_replace('{your_site_title}', $listing['title'], $altBody);
			$altBody = str_replace('{your_site_desc}', $listing['description'], $altBody);
			$altBody = str_replace('{your_site_email}', $listing['email'], $altBody);
			$altBody = str_replace('{your_site_plan}', $listing['plan'], $altBody);
			$altBody = str_replace('{own_site}', $this->mConfig['site'], $altBody);
			$altBody = str_replace('{own_url}', ESYN_BASE_URL, $altBody);
			$altBody = str_replace('{own_email}', $this->mConfig['site_email'], $altBody);
			$altBody = str_replace('{own_dir_url}', ESYN_URL, $altBody);

			$this->AltBody = $altBody;
		}

		$this->Subject = $subject;

		$this->AddAddress($aAdmin['email']);

		$r = $this->Send();
		if (!$r)
		{
			trigger_error("Admin notifier: There has been a mail error sending Subject: " . $this->Subject, E_USER_WARNING);
		}
		$this->ClearAddresses();

		return $r;
	}

	/**
	 * sendPaymentNotifUni
	 *
	 * Sends email when admin payment notification enabled
	 *
	 * @param mixed $event
	 * @param array $aAdmin
	 * @access public
	 * @return void
	 */
	function sendPaymentNotifUni ($event, $aAdmin = array())
	{
		$mime = $this->mConfig['mimetype'];

		$item = &$event['params']['item'];

		$subject = $this->mConfig[$event['item_type'].'_payment_subject'];

		if($mime != 'both')
		{
			$this->IsHTML($mime == 'html');
			$body = $this->mConfig[$event['item_type'].'_payment_body_'.$mime];
		}
		else
		{
			$body = $this->mConfig[$event['item_type'].'_payment_body_html'];
			$altBody = $this->mConfig[$event['item_type'].'_payment_body_plaintext'];
		}

		$subject = str_replace('{own_site}', $this->mConfig['site'], $subject);

		$body = isset($aAdmin['fullname'])	&& !empty($aAdmin['fullname'])	? str_replace('{admin_name}', $aAdmin['fullname'], $body)	: $body;
		$body = isset($item['url'])			&& !empty($item['url'])			? str_replace('{item_url}', $item['url'], $body)			: $body;
		$body = isset($item['title'])		&& !empty($item['title'])		? str_replace('{item_title}', $item['title'], $body)		: $body;
		$body = isset($item['description'])	&& !empty($item['description'])	? str_replace('{item_desc}', $item['description'], $body)	: $body;
		$body = isset($item['email'])		&& !empty($item['email'])		? str_replace('{item_email}', $item['email'], $body)		: $body;
		$body = isset($item['plan'])		&& !empty($item['plan'])		? str_replace('{item_plan}', $item['plan'], $body)			: $body;
		$body = str_replace('{own_site}', $this->mConfig['site'], $body);
		$body = str_replace('{own_url}', ESYN_BASE_URL, $body);
		$body = str_replace('{own_email}', $this->mConfig['site_email'], $body);
		$body = str_replace('{own_dir_url}', ESYN_URL, $body);

		$this->Body = $body;
		unset($body);

		if(isset($altBody))
		{
			$altBody = isset($aAdmin['fullname'])	&& !empty($aAdmin['fullname'])	? str_replace('{admin_name}', $aAdmin['fullname'], $body)	: $body;
			$altBody = isset($item['url'])			&& !empty($item['url'])			? str_replace('{item_url}', $item['url'], $body)			: $body;
			$altBody = isset($item['title'])		&& !empty($item['title'])		? str_replace('{item_title}', $item['title'], $body)		: $body;
			$altBody = isset($item['description'])	&& !empty($item['description'])	? str_replace('{item_desc}', $item['description'], $body)	: $body;
			$altBody = isset($item['email'])		&& !empty($item['email'])		? str_replace('{item_email}', $item['email'], $body)		: $body;
			$altBody = isset($item['plan'])			&& !empty($item['plan'])		? str_replace('{item_plan}', $item['plan'], $body)			: $body;
			$altBody = str_replace('{own_site}', $this->mConfig['site'], $body);
			$altBody = str_replace('{own_url}', ESYN_BASE_URL, $body);
			$altBody = str_replace('{own_email}', $this->mConfig['site_email'], $body);
			$altBody = str_replace('{own_dir_url}', ESYN_URL, $body);

			$this->AltBody = $altBody;
		}

		$this->Subject = $subject;

		$this->AddAddress($aAdmin['email']);

		$r = $this->Send();
		if (!$r)
		{
			trigger_error("Admin notifier: There has been a mail error sending Subject: " . $this->Subject, E_USER_WARNING);
		}
		$this->ClearAddresses();

		return $r;
	}


	/**
	 * setChangePasswordOptions
	 *
	 * Sends email when password change request exists
	 *
	 * @param mixed $event
	 * @access public
	 * @return void
	 */
	function setChangePasswordOptions(&$event)
	{
		$mime = $this->mConfig['mimetype'];

		$subject = $this->mConfig['password_change_subject'];
		if($mime != 'both')
		{
			$body = $this->mConfig['password_change_body_'.$mime];
			$this->IsHTML($mime == 'html');
		}
		else
		{
			$body = $this->mConfig['password_change_body_html'];
			$altBody = $this->mConfig['password_change_body_plaintext'];
		}

		$subject = str_replace('{own_site}', $this->mConfig['site'], $subject);

		$body = str_replace('{username}', $event['params']['account']['username'], $body);
		$body = str_replace('{password}', $event['params']['newpassword'], $body);
		$body = str_replace('{own_site}', $this->mConfig['site'], $body);
		$body = str_replace('{own_url}', ESYN_BASE_URL, $body);
		$body = str_replace('{own_email}', $this->mConfig['site_email'], $body);
		$body = str_replace('{own_dir_url}', ESYN_URL, $body);

		if(!empty($altBody))
		{
			$altBody = str_replace('{username}', $event['params']['account']['username'], $altBody);
			$altBody = str_replace('{password}', $event['params']['newpassword'], $altBody);
			$altBody = str_replace('{own_site}', $this->mConfig['site'], $altBody);
			$altBody = str_replace('{own_url}', ESYN_BASE_URL, $altBody);
			$altBody = str_replace('{own_email}', $this->mConfig['site_email'], $altBody);
			$altBody = str_replace('{own_dir_url}',	ESYN_URL, $altBody);
			$altBody = str_replace($search, $replace, $altBody);

			$this->AltBody = $altBody;
		}

		$this->Subject = $subject;
		$this->Body = $body;
	}

	function setAccountConfirmEmailOptions(&$event)
	{
		$mime = $this->mConfig['mimetype'];

		$subject = $this->mConfig["{$event['action']}_subject"];

		if($mime != 'both')
		{
			$body = $this->mConfig["{$event['action']}_body_{$mime}"];

			$this->IsHTML($mime == 'html');
		}
		else
		{
			$body = $this->mConfig["{$event['action']}_body_html"];
			$altBody = $this->mConfig["{$event['action']}_body_plaintext"];
		}

		$subject = str_replace('{own_site}', $this->mConfig['site'], $subject);

		$body = str_replace('{username}', $event['params']['account']['username'], $body);
		$body = str_replace('{account_id}', $event['params']['account']['id'], $body);
		$body = str_replace('{sec_key}', $event['params']['account']['sec_key'], $body);
		$body = str_replace('{own_site}', $this->mConfig['site'], $body);
		$body = str_replace('{own_url}', ESYN_BASE_URL, $body);
		$body = str_replace('{own_email}', $this->mConfig['site_email'], $body);
		$body = str_replace('{own_dir_url}', ESYN_URL, $body);

		if(!empty($altBody))
		{
			$altBody = str_replace('{username}', $event['params']['account']['username'], $altBody);
			$altBody = str_replace('{account_id}', $event['params']['account']['id'], $altBody);
			$altBody = str_replace('{sec_key}', $event['params']['account']['sec_key'], $altBody);
			$altBody = str_replace('{own_site}', $this->mConfig['site'], $altBody);
			$altBody = str_replace('{own_url}', ESYN_BASE_URL, $altBody);
			$altBody = str_replace('{own_email}', $this->mConfig['site_email'], $altBody);
			$altBody = str_replace('{own_dir_url}',	ESYN_URL, $altBody);

			$this->AltBody = $altBody;
		}

		$this->Subject = $subject;
		$this->Body = $body;
	}

	/**
	 * setAccountRegistrationOptions
	 *
	 * Sends email when editor registration action happens
	 *
	 * @param mixed $event
	 * @access public
	 * @return void
	 */
	function setAccountRegistrationOptions(&$event)
	{
		$mime = $this->mConfig['mimetype'];

		$subject = $this->mConfig['register_account_subject'];

		if($mime != 'both')
		{
			$body = $this->mConfig['register_account_body_'.$mime];
			$this->IsHTML($mime == 'html');
		}
		else
		{
			$body = $this->mConfig['register_account_body_html'];
			$altBody = $this->mConfig['register_account_body_plaintext'];
		}

		$subject = str_replace('{own_site}', $this->mConfig['site'], $subject);

		$body = str_replace('{account_username}', $event['params']['editor'], $body);
		$body = str_replace('{account_username_url}', urlencode($event['params']['editor']), $body);
		$body = str_replace('{account_pwd}', $event['params']['newpassword'], $body);
		$body = str_replace('{key}', $event['params']['sec_key'], $body);
		$body = str_replace('{own_site}', $this->mConfig['site'], $body);
		$body = str_replace('{own_dir_url}', ESYN_URL, $body);
		$body = str_replace('{own_url}', ESYN_BASE_URL, $body);
		$body = str_replace('{own_email}', $this->mConfig['site_email'], $body);

		$this->Subject = $subject;

		$html = $this->mConfig['mimetype'];
		$mailType = $this->mConfig['mail_function'];

		$this->IsHTML($html!='plaintext');
		$this->Body = $body;
		unset($body);
	}

	/**
	 * setAccountAdminRegistrationOptions
	 *
	 * Sends email when editor registration action happens
	 *
	 * @param mixed $event
	 * @access public
	 * @return void
	 */
	function setAccountAdminRegistrationOptions(&$event)
	{
		$mime = $this->mConfig['mimetype'];

		$subject = $this->mConfig['register_account_admin_subject'];

		if($mime != 'both')
		{
			$body = $this->mConfig['register_account_admin_body_'.$mime];
			$this->IsHTML($mime == 'html');
		}
		else
		{
			$body = $this->mConfig['register_account_admin_body_html'];
			$altBody = $this->mConfig['register_account_admin_body_plaintext'];
		}

		if ($this->mConfig['mod_rewrite'])
		{
			$dirlink = ESYN_URL . $event['params']['listing']['path'];
			$dirlink .= !empty($event['params']['listing']['path']) ? '/' : '';
			
			$dirlink .= esynUtil::convertStr($event['params']['listing']['title']);
			$dirlink .= '-l'.$event['params']['listing']['id'].'.html';
		}
		else
		{
			$dirlink = ESYN_URL.'view-listing.php?id='.$event['params']['listing']['id'];
		}

		$subject = str_replace('{own_site}', $this->mConfig['site'], $subject);

		$body = str_replace('{account_username}', $event['params']['editor'], $body);
		$body = str_replace('{account_username_url}', urlencode($event['params']['editor']), $body);
		$body = str_replace('{account_pwd}', $event['params']['newpassword'], $body);
		$body = str_replace('{key}', $event['params']['sec_key'], $body);
		$body = str_replace('{own_site}', ESYN_URL, $body);
		$body = str_replace('{own_url}', ESYN_BASE_URL, $body);
		$body = str_replace('{own_email}', $this->mConfig['site_email'], $body);

		$body = str_replace('{your_site_title}', $event['params']['listing']['title'], $body);
		$body = str_replace('{your_site_desc}', $event['params']['listing']['description'], $body);
		$body = str_replace('{your_site_url}', $event['params']['listing']['url'], $body);
		$body = str_replace('{your_site_email}', $event['params']['listing']['email'], $body);

		$body = str_replace('{dir_listing}', $dirlink, $body);

		$this->Subject = $subject;

		$html = $this->mConfig['mimetype'];
		$mailType = $this->mConfig['mail_function'];

		$this->IsHTML($html!='plaintext');
		$this->Body = $body;
		unset($body);
	}

	/**
	 * setAccountChangesOptions
	 *
	 * Sends email when account's status changes and account deleted
	 *
	 * @param mixed $event
	 * @access public
	 * @return void
	 */
	function setAccountChangesOptions(&$event)
	{
		$mime = $this->mConfig['mimetype'];

		$subject = $this->mConfig[$event['action'].'_subject'];
		if($mime != 'both')
		{
			$body = $this->mConfig[$event['action'].'_body_'.$mime];
			$this->IsHTML($mime == 'html');
		}
		else
		{
			$body = $this->mConfig[$event['action'].'_body_html'];
			$altBody = $this->mConfig[$event['action'].'_body_plaintext'];
		}

		$subject = str_replace('{own_site}', $this->mConfig['site'], $subject);

		$body = str_replace('{username}', $event['params']['account']['username'], $body);
		$body = str_replace('{own_site}', $this->mConfig['site'], $body);
		$body = str_replace('{own_url}', ESYN_BASE_URL, $body);
		$body = str_replace('{own_email}',$this->mConfig['site_email'], $body);
		$body = str_replace('{own_dir_url}', ESYN_URL, $body);

		if(!empty($altBody))
		{
			$altBody = str_replace('{username}',	$event['params']['account']['username'], $altBody);
			$altBody = str_replace('{own_site}',	$this->mConfig['site'], $altBody);
			$altBody = str_replace('{own_url}',		ESYN_BASE_URL, $altBody);
			$altBody = str_replace('{own_email}',	$this->mConfig['site_email'], $altBody);
			$altBody = str_replace('{own_dir_url}',	ESYN_URL, $altBody);

			$this->AltBody = $altBody;
		}

		$this->Subject = $subject;
		$this->Body = $body;
	}

	/**
	 * setBrokenListingReportOptions
	 *
	 * Set broken listing report options
	 *
	 * @param mixed $event
	 * @access public
	 * @return void
	 */
	function setBrokenListingReportOptions(&$event)
	{
		$subject = $this->mConfig[$event['action']."_subject"];

		$mime = $this->mConfig["mimetype"];

		if($mime != 'both')
		{
			$body = $this->mConfig[$event['action']."_body_".$mime];
		}
		else
		{
			$body = $this->mConfig[$event['action']."_body_html"];
			$altBody = $this->mConfig[$event['action']."_body_plaintext"];
		}

		$this->Subject = $subject;

		$this->IsHTML($mime == 'html');


		$listing = &$event['params']['listing'];

		$body = str_replace(
			array("{id}", "{url}", "{title}"),
			array($listing['id'], $listing['url'], $listing['title']), $body);

		if(isset($altBody))
		{
			$altBody = str_replace(array("{id}", "{url}", "{title}"), array($listing['id'], $listing['url'], $listing['title']), $altBody);
			$this->AltBody = $altBody;
		}

		$this->Body = $body;
	}

	/**
	 * sendExpirationNotif
	 *
	 * Sends payment expiration notification to listing owner
	 *
	 * @param mixed $aLink
	 * @access public
	 * @return void
	 */
	function sendExpirationNotif ($aLink)
	{
		$subject = $this->mConfig['payment_expiration_subject'];
		$mime = $this->mConfig['mimetype'];

		if($mime != 'both')
		{
			$body = $this->mConfig['payment_expiration_body_'.$mime];
		}
		else
		{
			$body = $this->mConfig['payment_expiration_body_html'];
			$altBody = $this->mConfig['payment_expiration_body_plaintext'];
		}

		// Prepare body
		$body = str_replace('{your_site_url}', $aLink['url'], $body);
		$body = str_replace('{your_site_title}', $aLink['title'], $body);
		$body = str_replace('{your_site_desc}', $aLink['description'], $body);
		$body = str_replace('{your_site_email}', $aLink['email'], $body);
		$body = str_replace('{own_site}', $this->mConfig['site'], $body);
		$body = str_replace('{own_url}', ESYN_BASE_URL, $body);
		$body = str_replace('{own_email}', $this->mConfig['site_email'], $body);
		$body = str_replace('{own_dir_url}', ESYN_URL, $body);
		if ($this->mConfig['mod_rewrite'])
		{
			$body = str_replace('{dir_listing}', ESYN_URL.$aLink['path'], $body);
		}
		else
		{
			$body = str_replace('{dir_listing}', ESYN_URL.'index.php?category='.$aLink['category_id'], $body);
		}
		$body = str_replace('{upgrade_url}', ESYN_URL.'edit-listing.php?id='.$aLink['id'], $body);
		$body = str_replace('{plan_cost}', number_format($aLink['plan_cost'], 2), $body);
		$body = str_replace('{start_date}', date('j M, Y', strtotime($aLink['start_date'])), $body);
		$body = str_replace('{end_date}', date('j M, Y', strtotime($aLink['end_date'])), $body);
		$body = str_replace('{plan_name}', $aLink['plan_name'], $body);

		$postfix = $aLink['days'] > 1 ? 'days' : 'day';
		$body = str_replace('{days}', $aLink['days'].' '.$postfix, $body);

		if(!empty($aLink['action_expire']))
		{
			if(in_array($aLink['action_expire'], array('approval', 'banned', 'suspended')))
			{
				$template = "Once it is expired its status will be changed to {$aLink['action_expire']}.";

				$body = str_replace('{action_expire}', $template, $body);
			}
			elseif(in_array($aLink['action_expire'], array('regular', 'featured', 'partner')))
			{
				$template = "Once it is expired its type will be changed to {$aLink['action_expire']}.";

				$body = str_replace('{action_expire}', $template, $body);
			}
			elseif('remove' == $aLink['action_expire'])
			{
				$template = "Once it is expired listing will be removed";

				$body = str_replace('{action_expire}', $template, $body);
			}
			else
			{
				$body = str_replace('{action_expire}', '', $body);
			}
		}
		else
		{
			$body = str_replace('{action_expire}', '', $body);
		}

		if(isset($altBody))
		{
			// Prepare body
			$altBody = str_replace('{your_site_url}', $aLink['url'], $altBody);
			$altBody = str_replace('{your_site_title}', $aLink['title'], $altBody);
			$altBody = str_replace('{your_site_desc}', $aLink['description'], $altBody);
			$altBody = str_replace('{your_site_email}', $aLink['email'], $altBody);
			$altBody = str_replace('{own_site}', $this->mConfig['site'], $altBody);
			$altBody = str_replace('{own_url}', ESYN_BASE_URL, $altBody);
			$altBody = str_replace('{own_email}', $this->mConfig['site_email'], $altBody);
			$altBody = str_replace('{own_dir_url}', ESYN_URL, $altBody);
			if ($this->mConfig['mod_rewrite'])
			{
				$altBody = str_replace('{dir_listing}', ESYN_URL.$aLink['path'], $altBody);
			}
			else
			{
				$altBody = str_replace('{dir_listing}', ESYN_URL.'index.php?category='.$aLink['category_id'], $altBody);
			}
			$altBody = str_replace('{upgrade_url}', ESYN_URL.'edit-listing.php?id='.$aLink['id'], $altBody);
			$altBody = str_replace('{plan_cost}', number_format($aLink['plan_cost'], 2), $altBody);
			$altBody = str_replace('{start_date}', date('j M, Y', strtotime($aLink['start_date'])), $altBody);
			$altBody = str_replace('{end_date}', date('j M, Y', strtotime($aLink['end_date'])), $altBody);
			$altBody = str_replace('{plan_name}', $aLink['plan_name'], $altBody);
			$postfix = $aLink['days'] > 1 ? 'days' : 'day';
			$altBody = str_replace('{days}', $aLink['days'].' '.$postfix, $altBody);

			if(!empty($aLink['action_expire']))
			{
				if(in_array($aLink['action_expire'], array('approval', 'banned', 'suspended')))
				{
					$template = "Once it is expired its status will be changed to {$aLink['action_expire']}.";

					$altBody = str_replace('{action_expire}', $template, $altBody);
				}
				elseif(in_array($aLink['action_expire'], array('regular', 'featured', 'partner')))
				{
					$template = "Once it is expired its type will be changed to {$aLink['action_expire']}.";

					$altBody = str_replace('{action_expire}', $template, $altBody);
				}
				elseif('remove' == $aLink['action_expire'])
				{
					$template = "Once it is expired listing will be removed.";

					$altBody = str_replace('{action_expire}', $template, $altBody);
				}
				else
				{
					$altBody = str_replace('{action_expire}', '', $altBody);
				}
			}
			else
			{
				$altBody = str_replace('{action_expire}', '', $altBody);
			}

			$this->AltBody = $altBody;
		}

		$this->AddAddress($aLink['email']);
		$this->Subject = $subject;
		$this->Body = $body;
		$this->From = $this->mConfig['site_email'];

		$r = $this->Send();
		$this->ClearAddresses();

		return $r;
	}

	/**
	 * setAdminPasswordRestorationOptions
	 *
	 * Sends email when user requests admin password
	 *
	 * @param mixed $event
	 * @access public
	 * @return bool
	 */
	function setAdminPasswordRestorationOptions(&$event)
	{
		$subject = "Admin password restoration";
		$body = "Please follow this URL: {url} in order to reset your password.";

		$url = ESYN_URL.ESYN_ADMIN_DIR."/login.php?action=success&code=".urlencode($event['params']['code']);

		$body = str_replace('{url}', $url, $body);

		$this->Subject = $subject;
		$this->IsHtml(false);
		$this->Body = $body;
	}

	function setAdminNewPasswordOptions(&$event)
	{
		$subject = "Admin password restoration";
		$body = "Your new password: {password}";

		$body = str_replace('{password}', $event['params']['password'], $body);

		$this->Subject = $subject;
		$this->IsHtml(false);
		$this->Body = $body;
	}

    /**
     * error_handler
	 *
	 * Replace the default error_handler
	 *
     * @param mixed $msg
     * @access public
     * @return void
     */
    function error_handler($msg)
    {
        trigger_error("There has been a mail error: ".$msg, E_USER_WARNING);
    }
}
