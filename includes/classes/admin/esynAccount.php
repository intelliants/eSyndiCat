<?php
//##copyright##

/**
 * esynAccount 
 * 
 * @uses esynAdmin
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
	 * insert
	 *
	 * Adds new account to database
	 * 
	 * @param array $account account information
	 * @access public
	 * @return int the id of the newly created account
	 */
	function insert($account)
	{
		$this->startHook("beforeAccountInsert");

		if(empty($account))
		{
			$this->message = 'Account parameter is empty';

			return false;
		}

		if(isset($account['old_name']))
		{
			unset($account['old_name']);
		}

		if(isset($account['password2']))
		{
			unset($account['password2']);
		}

		$account['password'] = md5($account['password']);

		$id = parent::insert($account, array('date_reg' => "NOW()"));

		$this->startHook("afterAccountInsert");
		
		return true;
	}

	/**
	 * update 
	 * 
	 * @param mixed $fields 
	 * @param string $where 
	 * @param array $addit 
	 * @access public
	 * @return void
	 */
	function update($fields, $ids)
	{
		$this->startHook("beforeAccountUpdate");

		if(empty($fields))
		{
			$this->message = 'The Fields parameter is empty.';

			return false;
		}

		if(empty($ids))
		{
			$this->message = 'The ID parameter is empty.';

			return false;
		}

		if(isset($fields['status']))
		{
			switch($fields['status'])
			{
			case "active":
				/** sends email to account **/
				$action = "account_approved";
				
				if($this->mConfig[$action])
				{
					$where = $this->convertIds('id', $ids);

					if(!empty($where))
					{
						$accounts = $this->all("*", $where);
					
						if($accounts)
						{
							foreach($accounts as $account)
							{
								if('unconfirmed' == $account['status'])
								{
									parent::update(array("sec_key" => ''), "`id` = '{$account['id']}'");
								}

								if($account['email'])
								{
									$event = array(
										"action" => $action,
										"params" => array (
											"account" =>	$account,
											"rcpts" =>	array($account['email'])
										)
									);
									
									$this->mMailer->dispatcher($event);
								}
							}
						}
					}
				}
				break;
			case "approval":
				/** sends email to account **/
				$action = "account_disapproved";
				
				if($this->mConfig[$action])
				{
					$where = $this->convertIds('id', $ids);

					if(!empty($where))
					{
						$accounts = $this->all("*", $where);
					
						if($accounts)
						{
							foreach($accounts as $account)
							{
								if($account['email'])
								{
									$event 	= array(
										"action" => $action,
										"params" => array(
											"account" 	=>	$account,
											"rcpts" =>	array($account['email'])
										)
									);
									
									$this->mMailer->dispatcher($event);
								}
							}
						}
					}
				}
				break;
			}
		}

		if(isset($fields['password']))
		{
			$fields['password'] = md5($fields['password']);
		}

		if(empty($where))
		{
			$where = $this->convertIds('id', $ids);
		}

		$id = parent::update($fields, $where, array('date_reg' => "NOW()"));

		$this->startHook("afterAccountUpdate");

		return true;
	}
	
	/**
	 * delete
	 *
	 * Remove account
	 * 
	 * @param string $where 
	 * @access public
	 * @return void
	 */
	function delete($ids)
	{
		$this->startHook("beforeAccountDelete");

		if(empty($ids))
		{
			$this->message = 'The ID parameter is empty.';

			return false;
		}

		$action = "account_deleted";
		
		if($this->mConfig[$action])
		{
			$where = $this->convertIds('id', $ids);

			if(!empty($where))
			{
				$accounts = $this->all("*", $where);
			
				if($accounts)
				{
					foreach($accounts as $account)
					{
						if($account['email'])
						{
							$event 	= array(
								"action" => $action,
								"params" => array(
									"account"	=> $account,
									"rcpts"		=> array($account['email'])
								)
							);
							
							$this->mMailer->dispatcher($event);
						}
					}
				}
			}
		}

		if(empty($where))
		{
			$where = $this->convertIds('id', $ids);
		}
		
		parent::delete($where);

		$this->startHook("afterAccountDelete");

		return true;
	}

	/**
	 * getInfo
	 *
	 * Returns account information by id
	 * 
	 * @param int $aAccount account id
	 * @access public
	 * @return array
	 */
	function getInfo($aAccount)
	{
		$sql = "SELECT t1.*, COUNT(t2.`id`) listings ";
		$sql .= "FROM `{$this->mTable}` t1 ";
		$sql .= "LEFT JOIN `{$this->mPrefix}listings` t2 ";
		$sql .= "ON t1.`id` = t2.`account_id` ";
		$sql .= "WHERE t1.`id` = '{$aAccount}' ";
		$sql .= "AND t1.`status` = 'active' ";
		$sql .= "GROUP BY t1.`id` ";
		$sql .= "LIMIT 0,1";

		return $this->getRow($sql);
	}

	/**
	 * setNewPassword
	 *
	 * Change account password and sends to account
	 * 
	 * @param mixed $aAccount account information
	 * @access public
	 * @return bool
	 */
	function setNewPassword($aAccount)
	{
		$pass = rand(1000, 1000000);

		$x = parent::update(array("password" => md5($pass)), "`id` = :id", array('id' => $aAccount['id']));

		/** sends email to account **/
		$action	= "account_change_password";
		
		$event = array(
			"action" => $action,
			"params" => array(
				"account"		=> $aAccount,
				"newpassword"	=> $pass,
				"rcpts"			=> array($aAccount['email'])
			)
		);

		$this->mMailer->dispatcher($event);

		return $x;
	}
	
	/**
	 * getAccountsByStatus
	 *
	 * Returns accounts by status
	 * 
	 * @param string $aStatus account status
	 * @param int $aStart starting position
	 * @param int $aLimit number of accounts to be returned
	 * @access public
	 * @return void array
	 */
	function getAccountsByStatus($aStatus = '', $aStart = 0, $aLimit = 0)
	{
		$sql = "SELECT *";
		$sql .= "FROM `".$this->mTable."` ";
		$sql .= $aStatus ? "WHERE `status` = '{$aStatus}'" : '';
		$sql .= "ORDER BY `date_reg` DESC ";
		$sql .= $aLimit ? "LIMIT {$aStart}, {$aLimit} " : ' ';

		return $this->getAll($sql);
	}

	/**
	 * getNumSearch
	 *
	 * Returns number of accounts
	 * 
	 * @param string $aUsername account username
	 * @param string $aEmail account email
	 * @access public
	 * @return int
	 */
	function getNumSearch($aUsername = '', $aEmail = '')
	{
		$sql = "SELECT COUNT(`id`) ";
		$sql .= "FROM `".$this->mTable."` ";
		$sql .= $aUsername ? "WHERE `username` = '{$aUsername}'" : '';
		if ($aEmail)
		{
			$sql .= $aUsername ? " AND  " : " WHERE ";
			$sql .= " `email` LIKE '%{$aEmail}%' ";
		}

		return $this->getOne($sql);
	}

	/**
	 * getSearch
	 *
	 * Returns accounts by username or email
	 * 
	 * @param string $aUsername account username
	 * @param string $aEmail account email
	 * @param int $aStart starting position
	 * @param int $aLimit number of accounts to be returned
	 * @access public
	 * @return array
	 */
	function getSearch($aUsername = '', $aEmail = '', $aStart = 0, $aLimit = 0)
	{
		$sql = "SELECT * ";
		$sql .= "FROM `".$this->mTable."` ";
		$sql .= $aUsername ? "WHERE `username` = '{$aUsername}'" : '';
		if ($aEmail)
		{
			$sql .= $aUsername ? " AND " : " WHERE ";
			$sql .= " `email` LIKE '%{$aEmail}%' ";
		}
		$sql .= $aStart ? "LIMIT {$aStart}, {$aLimit}" : '';

		return $this->getAll($sql);
	}

	/**
	 * createPassword
	 *
	 * Generate random password
	 * 
	 * @param int $length (optional) length of password
	 * @access public
	 * @return void
	 */
	function createPassword($length = 7)
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
	 * registerAccount 
	 *
	 * The function creates new record in the database
	 * and sends confirmation email
	 *
	 * @param arr $aAccount user's data
	 * @access public
	 * @return int newly created account id
	 */
	function registerAccount($aAccount, $aListing)
	{
		$account = array();

		$password		= $this->createPassword();
		$sec_key		= md5($this->createPassword());
		$md5_password	= md5($password);

		$account['username']	= $aAccount['username'];
		$account['email']		= $aAccount['email'];
		$account['password']	= $md5_password;
		$account['sec_key']		= $sec_key;
		$account['status']		= isset($aAccount['status']) ? $aAccount['status'] : 'unconfirmed';
		
		parent::insert($account, array('date_reg' => 'NOW()'));
		
		$id = mysql_insert_id();

		$event 	= array(
			"action" => "account_admin_register",
			"params" => array(
				"rcpts"			=> array($aAccount['email']),
				"editor"		=> $aAccount['username'],
				"newpassword"	=> $password,
				"sec_key"		=> $sec_key,
				"listing"		=> $aListing
			)
		);

		$this->mMailer->dispatcher($event);

		return $id;
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
}
