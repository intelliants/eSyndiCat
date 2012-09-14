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


class esynAdmins extends eSyndiCat
{
	/**
	 * Description of the Variable
	 * @var		mixed
	 * @access	public
	 */
	var $mTable = 'admins';

	function delete($ids)
	{
		$this->startHook("beforeAdminDelete");
		
		if(empty($ids))
		{
			$this->message = 'The ID parameter is empty.';

			return false;
		}
		
		$where = $this->convertIds('id', $ids);

		parent::delete($where);

		$where = $this->convertIds('admin_id', $ids);

		parent::setTable("admin_permissions");
		parent::delete($where);
		parent::resetTable();

		$this->startHook("afterAdminDelete");

		return true;
	}

	function insert($admin)
	{
		$this->startHook("beforeAdminInsert");

		if(isset($admin['permissions']))
		{
			$permissions = $admin['permissions'];

			unset($admin['permissions']);
		}

		$admin_id = parent::insert($admin, array('date_reg' => 'NOW()'));

		if(!empty($permissions))
		{
			$acos = $this->getAcos();

			foreach($permissions as $key => $permission)
			{
				if(array_key_exists($permission, $acos))
				{
					$admin_permissions[] = array(
						"admin_id"	=> $admin_id,
						"allow"		=> 1,
						"aco"		=> $permission
					);
				}
			}

			if(!empty($admin_permissions))
			{
				parent::setTable("admin_permissions");
				parent::insert($admin_permissions);
				parent::resetTable();
			}
		}

		$this->startHook("afterAdminInsert");

		return true;
	}

	function update($admin, $ids)
	{
		$this->startHook("beforeAdminUpdate");
		
		if(empty($admin))
		{
			$this->message = 'The Admin parameter is empty.';

			return false;
		}

		if(empty($ids))
		{
			$this->message = 'The ID parameter is empty.';

			return false;
		}

		if(isset($admin['permissions']))
		{
			$acos = $this->getAcos();

			if($acos)
			{
				foreach($acos as $key => $aco)
				{
					if(in_array($key, $admin['permissions'], true))
					{
						if(is_array($ids))
						{
							foreach($ids as $id)
							{
								$temp = array(
									"admin_id"	=> $id,
									"allow"		=> 1,
									"aco"		=> $key
								);
								
								$permissions[] = $temp;
							}
						}
						else
						{
							$temp = array(
								"admin_id"	=> $ids,
								"allow"		=> 1,
								"aco"		=> $key
							);
								
							$permissions[] = $temp;
						}
					}
				}

				if(!empty($permissions))
				{
					parent::setTable("admin_permissions");
					parent::delete("`admin_id` = :id", array('id' => $ids));
					parent::insert($permissions);
					parent::resetTable();
				}
			}

			unset($admin['permissions']);
		}

		if(isset($admin['id']) && !empty($admin['id']))
		{
			$values = array('id' => $admin['id']);
			$where = "`id` = :id";
		}

		if(isset($ids) && !empty($ids))
		{
			$values = array();
			$where = $this->convertIds('id', $ids);
		}

		parent::update($admin, $where, $values);

		$this->startHook("afterAdminUpdate");

		return true;
	}

	function getAcos()
	{
		$acos = array();

		$this->setTable("admin_pages");
		$acos = $this->keyvalue("`aco`, `title`", "1=1 GROUP BY `aco`");
		$this->resetTable();

		return $acos;
	}
}

?>
