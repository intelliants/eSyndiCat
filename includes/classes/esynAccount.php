<?php
//##copyright##


/**
 * esynAccount 
 * 
 * @uses eSyndiCat
 * @package 
 * @version $id$
 */
class esynAccount extends eSyndiCat
{	
	/**
	 * mTable 
	 * 
	 * @var string
	 * @access public
	 */
	var $mTable = "accounts";
	
	/**
	 * User Profile
	 * 
	 * @var array
	 * @access public
	 */
	var $accountProfile = array();
	/**
	 * getInfo 
	 * 
	 * Returns account information by id
	 *
	 * @param int $aAccount id account
	 * @access public
	 * @return array
	 */
	function getInfo($aAccount)
	{
		$sql = "SELECT t1.*";
		$sql .= "FROM `".$this->mTable."` t1 ";
		$sql .= "WHERE t1.`id` = '".$aAccount."' ";
		$sql .= "AND t1.`status` = 'active' ";
		$sql .= "GROUP BY t1.`id` ";
		$sql .= "LIMIT 0,1";

		return $this->getRow($sql);
	}

	/**
	 * changePassword 
	 * 
	 * Changes account password
	 *
	 * @param int $aId 
	 * @param string $aPassword 
	 * @access public
	 * @return bool
	 */
	function changePassword($aId, $aPassword)
	{
		$x = $this->update(array("password" => md5($aPassword)), "`id` = '".$aId."'");
		$aAccount = $this->row("*","`id`='".$aId."'");
		
		$event 	= array(
			"action" => "account_change_password",
			"params" => array(
				"rcpts"			=> array($aAccount['email']),
				"account"		=> $aAccount,
				"newpassword"	=> $aPassword
			)
		);
		
		$this->mMailer->dispatcher($event);

		return $x;
	}

	/**
	 * setNewPassword 
	 * 
	 * Change account password and sends to account
	 *
	 * @param mixed $account account information
	 * @access public
	 * @return bool
	 */
	function setNewPassword($account)
	{
		$pass = rand(1000, 1000000);

		$x = $this->update(array("password" => md5($pass), "sec_key" => ''), "`id` = :id", array('id' => $account['id']));

		/** sends email to account **/
		$action	= "account_change_password";
		
		$event = array(
			"action"	=> $action,
			"params"	=> array(
				"account"		=> $account,
				"newpassword"	=> $pass,
				"rcpts"			=> array($account['email'])
			)
		);

		$this->mMailer->dispatcher($event);

		return $x;
	}

	/**
	 * registerAccount 
	 * 
	 * The function creates new record in the database and sends confirmation email
	 *
	 * @param mixed $aAccount 
	 * @access public
	 * @return int newly created account id
	 */
	function registerAccount($aAccount)
	{
		$account = array();

		if ($aAccount['auto_generate'])
		{
			$password = $this->createPassword();
		}
		else
		{
			$password = $aAccount['password'];
		}
			
		$sec_key = md5($this->createPassword());
		$md5_password = md5($password);

		$account['username']	= $aAccount['username'];
		$account['email']		= $aAccount['email'];
		$account['password']	= $md5_password;
		$account['sec_key']		= $sec_key;
		$account['status']		= isset($aAccount['status']) ? $aAccount['status'] : 'unconfirmed';

		parent::insert($account, array('date_reg' => 'NOW()'));
		$id = mysql_insert_id();
		
		$account['password'] = $password;
		$this->accountProfile = $account;

		$event 	= array(
			"action" => "account_register",
			"params" => array(
				"rcpts"			=> array($aAccount['email']),
				"editor"		=> $aAccount['username'],
				"email"			=> $aAccount['email'],
				"newpassword"	=> $password,
				"sec_key"		=> $sec_key
			)
		);

		$this->mMailer->dispatcher($event);

		return $id;
	}

	/**
	 * createPassword 
	 * 
	 * Generate random password
	 *
	 * @param int $length Optional, length of password 
	 * @access public
	 * @return string
	 */
	function createPassword( $length = 7)
	{
		$chars = "abcdefghijkmnopqrstuvwxyz023456789";
		$pass = '';
		srand((double)microtime()*1000000);
		
		for ($i = 0; $i < $length; $i++)
		{
			$num = rand() % 33;
			$pass .= $chars[$num];
		}
		return $pass;
	}

	/**
	 * resendEmail 
	 * 
	 * Resend email to account
	 *
	 * @param mixed $aAccount account information
	 * @access public
	 * @return void
	 */
	function resendEmail($aAccount)
	{
		$password = $aAccount['password'];
		$sec_key = $aAccount['sec_key'];
		
		$event 	= array(
			"action" => "account_register",
			"params" => array(
				"rcpts"			=> array($aAccount['email']),
				"editor"		=> $aAccount['username'],
				"newpassword"	=> $password,
				"sec_key"		=> $sec_key
			)
		);

		$this->mMailer->dispatcher($event);
	}

	function confirmEmail($account, $action)
	{
		$update = array();

		$account['sec_key'] = md5($this->createPassword());

		$update['sec_key'] = $account['sec_key'];

		if ('change_email' == $action)
		{
			$update['nemail'] = $account['nemail'];
		}

		$this->update($update, "`id` = :id", array('id' => (int)$account['id']));

		$event = array(
			"action" => "account_{$action}",
			"params" => array(
				"rcpts"		=> array($account['email']),
				"account"	=> $account,
			)
		);

		$this->mMailer->dispatcher($event);
	}

	function setNewAccountEmail($account)
	{
		$account['email'] = $account['nemail'];

		$this->update(array('sec_key' => '', 'nemail' => '', 'email' => $account['email']), '`id` = :id', array('id' => (int)$account['id']));

		// TODO: implement notification for admin
		/*$event = array(
			"action" => "account_new_email_set",
			"params" => array(
				"rcpts"		=> array($account['email']),
				"account"	=> $account,
			)
		);

		$this->mMailer->dispatcher($event);*/
	}
}
