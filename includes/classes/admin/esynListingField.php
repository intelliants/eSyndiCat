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


/**
 * esynListingField 
 * 
 * @uses esynAdmin
 * @package 
 * @version $id$
 */
class esynListingField extends eSyndiCat
{
	/**
	 * mTable 
	 * 
	 * @var string
	 * @access public
	 */
	var $mTable = 'listing_fields';

	/**
	 * readOnlyFields 
	 * 
	 * @var string
	 * @access public
	 */
	var $readOnlyFields = array('title', 'url', 'description', 'reciprocal', 'email');

	/**
	 * insert 
	 *
	 * Adds new field for listings table
	 * 
	 * @param arr $field field information
	 * @access public
	 * @return bool
	 */
	function insert($field)
	{
		$this->startHook("beforeFieldInsert");

		if(empty($field))
		{
			$this->message = 'Field parameter is empty.';

			return false;
		}

		if($this->exists("`name` = :name", array('name' => $field['name'])))
		{
			$this->message = $this->mI18N['field_exists'];

			return false;
		}

		$order = $this->one("MAX(`order`) + 1");
		$field['order'] = (NULL == $order) ? 1 : $order;

		$categories = $plans = false;
		
		if(isset($field['categories']))
		{
			if(is_array($field['categories']) && !empty($field['categories']))
			{
				$categories = $field['categories'];
			}
			
			unset($field['categories']);
		}
		
		if(isset($field['_plans']))
		{
			if(is_array($field['_plans']) && !empty($field['_plans']))
			{
				$plans = $field['_plans'];
			}
			
			unset($field['_plans']);
		}
		
		parent::setTable("language");
		
		foreach($this->mLanguages as $code => $lang)
		{
			$phrase = array(
				"key"		=> 'field_'.$field['name'],
				"value"		=> $field['title'][$code],
				"code"		=> $code,
				"lang"		=> $lang,
				"category"	=> "common"
			);
			
			parent::insert($phrase);
		}
		
		parent::resetTable();

		if(!empty($field['any_meta']) && is_array($field['any_meta']))
		{
			parent::setTable("language");
			
			foreach($this->mLanguages as $code => $lang)
			{
				$phrase = array(
					"key"		=> 'field_'.$field['name']."_any_meta",
					"value"		=> $field['any_meta'][$code],
					"lang"		=> $lang,
					"code"		=> $code,
					"category"	=> "common"
				);

				parent::insert($phrase);
			}
			
			unset($field['any_meta']);

			parent::resetTable();
		}

		if(isset($field['_numberRangeForSearch']) && is_array($field['_numberRangeForSearch']) && !empty($field['_numberRangeForSearch']))
		{
			parent::setTable("language");
			
			foreach($field['_numberRangeForSearch'] as $value)
			{
				$phrase = array(
					"key"		=> 'field_'.$field['name']."_range_".$value[1],
					"value"		=> $value[0],
					"code"		=> $this->mConfig['lang'],
					"lang"		=> $this->mLanguages[$this->mConfig['lang']],
					"category"	=> "common"
				);

				parent::insert($phrase);
			}

			unset($field['_numberRangeForSearch']);
			
			parent::resetTable();
		}

		if (isset($field['lang_values']) && is_array($field['lang_values']))
		{
			parent::setTable("language");
			
			foreach($field['lang_values'] as $lng_code => $lng_phrases)
			{
				foreach($lng_phrases as $ph_key => $ph_value)
				{
					$phrase = array(
						"key"		=> 'field_'.$field['name']."_".$ph_key,
						"value"		=> $ph_value,
						"lang"		=> $this->mLanguages[$lng_code],
						"code"		=> $lng_code,
						"category"	=>"common"
					);

					parent::insert($phrase);					
				}
			}
			
			unset($field['lang_values']);			
			
			parent::resetTable();			
		}
		
		if(isset($field['search_key']))
		{
			parent::setTable("search_sections");
			
			if(!parent::exists("`key` = :section", array('section' => $field['section_key'])))
			{
				$field['section_key'] = '';
			}

			parent::resetTable();
		}

		unset($field['title']);

		$sql = "INSERT INTO `".$this->mTable."` (";
		$sql2 = '';

		$total = count($field);
		$cnt = 1;
		foreach($field as $key=>$value)
		{
			if($key == 'values' && $field['values'])
			{
				$value = join(",", array_keys($field['values']));
			}
			$sql .= ($cnt == $total) ? "`{$key}`) VALUES (" : "`{$key}`, ";
			$sql2 .= ($cnt == $total) ? "'{$value}') " : "'{$value}', ";
			$cnt++;
		}
		$sql .= $sql2;
		$this->query($sql);

		$field_id = $this->getInsertId();

		$fields = $this->describe($this->mPrefix.'listings');
		$exist = false;
		
		foreach($fields as $f)
		{
			if($f['Field'] == $field['name'])
			{
				$exist = true;
				break;
			}
		}

		if(!$exist)
		{
			$this->alterAdd($field);
		}

		if($categories)
		{
			$data = array();

			foreach($categories as $cat_id)
			{
				$data[] = array(
					"field_id"		=> $field_id,					
					"category_id"	=> $cat_id
				);
			}
			
			parent::setTable("field_categories");			
			parent::insert($data);
			parent::resetTable();			
		}
		
		if($plans)
		{
			$data = array();

			foreach($plans as $plan_id)
			{
				$data[] = array(
					"field_id"		=> $field_id,
					"plan_id"		=> $plan_id
				);
			}
			
			parent::setTable("field_plans");
			parent::insert($data);
			parent::resetTable();		
		}		

		if($field['searchable'] == '2')
		{
			$sql = "SHOW INDEX FROM `".$this->mPrefix."listings`";
			$indexes = $this->getAll($sql);
			$keyExists = false;
			
			foreach($indexes as $i)
			{
				if($i['Key_name'] == $field['name'] && $i['Index_type'] == 'FULLTEXT')
				{
					$keyExists = true;
					break;
				}
			}

			if(!$keyExists && in_array($field['type'], array('text', 'textarea')))
			{
				$sql = "ALTER TABLE `".$this->mPrefix."listings` ADD FULLTEXT (`".$field['name']."`)";
				$this->query($sql);
			}
		}
		
		$this->startHook("afterFieldInsert");

		return true;
	}

	/**
	 * alterAdd 
	 *
	 * Creates checkboxes array for link field
	 * 
	 * @param int $field field info array
	 * @access public
	 * @return void
	 */
	function alterAdd($field)
	{
		$sql = "ALTER TABLE `".$this->mPrefix."listings` ";
		$sql .= "ADD `".$field['name']."` ";
		
		switch ($field['type'])
		{
			case 'date':
				$sql .= "date ";
				break;
			case 'number':
				$sql .= "DOUBLE ";
				break;
			case 'text':
				$sql .= "VARCHAR (".$field['length'].") ";
				$sql .= $field['default'] ? "DEFAULT '{$field['default']}' " : '';
				break;
			case 'textarea':
				$sql .= "TEXT ";
				break;
			case 'storage':
				$sql .= "TEXT ";
				break;
			case 'image':
				$sql .= "TEXT ";
				break;
			case 'pictures':
				$sql .= "TEXT ";
				break;
			default:
				if ($field['values'])
				{
					$sql .= ($field['type'] == 'checkbox') ? "SET(" : "ENUM(";
					$values = &$field['values'];
					$cnt = count($values);
					$i = 0;
					foreach($values as $key=>$value)
					{
						$i++;
						$comma = ($i == $cnt) ? '' : ', ';
						$sql .= "'".$key."' ".$comma;
					}
					$sql .= ")";
					if(!empty($field['default']))
					{
						$sql .= " DEFAULT '{$field['default']}' ";
					}					
				}
				break;
		}
		
		$sql .= "NOT NULL";
		
		$this->query($sql);
		
		return true;
	}

	/**
	 * delete 
	 *
	 * Deletes listing field from database
	 * 
	 * @param string $aName field name
	 * @access public
	 * @return bool
	 */
	function delete($ids)
	{
		$this->startHook("beforeFieldDelete");

		if(empty($ids))
		{
			$this->message = 'ID parameter is empty.';

			return false;
		}

		$where = $this->convertIds('id', $ids);

		$fields = $this->keyvalue("`id`, `name`", $where);

		if($fields)
		{
			foreach($fields as $field_id => $field)
			{
				if(in_array($field, $this->readOnlyFields))
				{
					$this->message = "The {$field} field is read only.";

					return false;
				}
				else
				{
					parent::delete("`name` = :name", array('name' => $field));
			
					parent::setTable("language");
					parent::delete("`key` LIKE 'field_".$field."%'");
					parent::resetTable();
			
					parent::setTable("field_plans");
					parent::delete("`field_id` = :id", array('id' => $field_id));
					parent::resetTable();
			
					parent::setTable("field_categories");
					parent::delete("`field_id` = :id", array('id' => $field_id));
					parent::resetTable();
			
					// just additional checking
					parent::setTable("listings");
					$listing_fields = parent::describe();
					parent::resetTable();

					if($listing_fields)
					{
						foreach($listing_fields as $listing_field)
						{
							if($listing_field['Field'] == $field)
							{
								$this->query("ALTER TABLE `{$this->mPrefix}listings` DROP `{$field}`");
								break;
							}
						}
					}
				}
			}
		}

		$this->startHook("afterFieldDelete");

		return true;
	}

	/**
	 * update 
	 *
	 * Updates listing field information
	 * 
	 * @param arr $field listing field array
	 * @access public
	 * @return bool
	 */
	function update($field)
	{
		$this->startHook("beforeFieldUpdate");

		if(empty($field))
		{
			$this->message = 'Field parameter is empty.';

			return false;
		}

		$field_id = $this->one('`id`', "`name` = :name", array('name' => $field['name']));

		$categories = false;
		
		if(isset($field['categories']))
		{
			if(is_array($field['categories']) && !empty($field['categories']))
			{
				$data = array();
				
				foreach($field['categories'] as $id)
				{
					$data[] = array(
						"category_id"	=> $id,
						"field_id"		=> $field_id
					);
				}
				
				parent::setTable("field_categories");
				parent::delete("`field_id` = :id", array('id' => $field_id));
				parent::insert($data);
				parent::resetTable();
			}

			unset($field['categories']);
		}

		if(isset($field['_plans']))
		{
			if(is_array($field['_plans']) && !empty($field['_plans']))
			{
				$data = array();
				
				foreach($field['_plans'] as $plan_id)
				{
					$data[] = array(
						"field_id"		=> $field_id,
						"plan_id"		=> $plan_id
					);
				}

				parent::setTable("field_plans");
				parent::delete("`field_id` = :id", array('id' => $field_id));
				parent::insert($data);
				parent::resetTable();
			}

			unset($field['_plans']);
		}

		parent::setTable("language");
		
		foreach($this->mLanguages as $code => $lang)
    	{
			if(parent::exists("`key` = 'field_{$field['name']}'"))
			{
				parent::update(array("value" => $field['title'][$code]), "`key` = 'field_{$field['name']}' AND `code` = :code", array('code' => $code));
			}
			else
			{
				parent::insert(array(
					"value"		=> $field['title'][$code],
					"key"		=> 'field_' . $field['name'],
					"code"		=> $code,
					"lang"		=> $lang,
					"category"	=> "common"
				));
			}
		}

		if (isset($field['values']) && is_array($field['values']))
		{
			foreach($field['values'] as $key => $value)
      		{
				if(parent::one("id", "`key` = 'field_{$field['name']}_{$key}' AND `code` = '".ESYN_LANGUAGE."'"))
				{
					parent::update(array("value" => $value), "`key` = 'field_{$field['name']}_{$key}' AND `code` = '".ESYN_LANGUAGE."'");
				}
        		else
				{
					$phrase = array(
						"key"		=> "field_{$field['name']}_{$key}",
						"value"		=> $value,
						"lang"		=> $this->mLanguages[ESYN_LANGUAGE],
						"code"		=> ESYN_LANGUAGE,
						"category"	=> 'common'
					);

					parent::insert($phrase);
				}

				$new_keys[] = $key;
				$field['values'] = join(",", $new_keys);
      		}
		}

		if (isset($field['lang_values']) && is_array($field['lang_values']))
		{
			foreach($field['lang_values'] as $lng_code => $lng_phrases)
			{
				foreach($lng_phrases as $ph_key=>$ph_value)
				{
					if (parent::one("id", "`key` = 'field_{$field['name']}_{$ph_key}' AND `lang` = '{$this->mLanguages[$lng_code]}'"))
					{
						parent::update(array("value" => $ph_value), "`key` = 'field_{$field['name']}_{$ph_key}' AND `lang` = '{$this->mLanguages[$lng_code]}'");
					}
					else
					{
						$phrase = array(
							"key"		=> "field_{$field['name']}_{$ph_key}",
							"value"		=> $ph_value,
							"lang"		=> $this->mLanguages[$lng_code],
							"code"		=> $lng_code,
							"category"	=> 'common'
						);

						parent::insert($phrase);
					}
				}
			}

			unset($field['lang_values']);
		}

		parent::resetTable();

		unset($field['title']);

		if(isset($field['_numberRangeForSearch']) && is_array($field['_numberRangeForSearch']) && !empty($field['_numberRangeForSearch']))
		{
			parent::setTable("language");

			parent::delete("`key` LIKE 'field_{$field['name']}_range_%'");
				
			foreach($field['_numberRangeForSearch'] as $value)
			{
				$phrase = array(
					"key"		=> "field_{$field['name']}_range_{$value[0]}",
					"value"		=> $value[1],
					"code"		=> $this->mConfig['lang'],
					"lang"		=> $this->mLanguages[ESYN_LANGUAGE],
					"category"	=> "common"
				);

				parent::insert($phrase);
			}
			
			parent::resetTable();
			
			unset($field['_numberRangeForSearch']);
		}
		else // if empty then delete all possible range entries for this field as user can deleted
		{
			parent::setTable("language");
			parent::delete("`key` LIKE 'field_{$field['name']}_range_%'");
			parent::resetTable();
		}

		// avoid making fulltext second time
		if($field['searchable'] == 2)
		{
			$sql = "ALTER TABLE `{$this->mPrefix}listings` ADD FULLTEXT (`{$field['name']}`)";
			
			$this->query($sql);
		}
		else
		{
			if($field['searchable'] > 0)
			{
				$field['searchable'] = 1;
			}
				
			$sql = "SHOW INDEX FROM `{$this->mPrefix}listings`";
			
			$indexes = $this->getAll($sql);
			$keyExists = false;
			
			foreach($indexes as $i)
			{
				if($i['Key_name'] == $field['name'])
				{
					$keyExists = true;
					break;
				}
			}
				
			if($keyExists)
			{
				$sql = "ALTER TABLE `{$this->mPrefix}listings` DROP INDEX `{$field['name']}`";
				
				$this->query($sql);
			}
		}

		$sql = "UPDATE `{$this->mTable}` SET ";

		$total = count($field) - 1;
		foreach($field as $key => $value)
		{
			if ($key != 'old_name')
			{
				$sql .= "`{$key}` = '{$value}',";
			}
		}

		$sql = rtrim($sql, ",");
		$sql .= " WHERE `name` = '{$field['old_name']}'";

		$this->alterEdit($field);
		$this->query($sql);

		$this->startHook("afterFieldUpdate");

		return true;
	}

	/**
	 * alterEdit 
	 *
	 * Edits listing field
	 * 
	 * @param arr $field field info array
	 * @access public
	 * @return bool
	 */
	function alterEdit($field)
	{
		$sql = "ALTER TABLE `{$this->mPrefix}listings` ";
		$sql .= "CHANGE `{$field['old_name']}` `{$field['name']}` ";

		switch ($field['type'])
		{
			case 'date':
				$sql .= "date ";
				break;
			case 'number':
				$sql .= "DOUBLE ";
				break;
			case 'storage':
				$sql .= "TEXT ";
				break;
			case 'text':
				$sql .= "VARCHAR ({$field['length']}) ";
				$sql .= $field['default'] ? "DEFAULT '{$field['default']}' " : '';
				break;
			case 'textarea':
				$sql .= "TEXT ";
				break;
			case 'image':
				$sql .= "TEXT ";
				break;
			case 'pictures':
				$sql .= "TEXT ";
				break;
			default:
				if ($field['values'])
				{
					$sql .= ($field['type'] == 'checkbox') ? "SET(" : "ENUM(";
					$values = explode(',', $field['values']);
					$cnt = count($values);
					$i = 0;
					foreach($values as $value)
					{
						$i++;
						$comma = ($i == $cnt) ? '' : ', ';
						$sql .= "'{$value}' {$comma}";
					}
					$sql .= ")";
					if(!empty($field['default']))
					{
						$sql .= " DEFAULT '{$field['default']}' ";
					}
				}	
				break;
		}
		$sql .= "NOT NULL";

		return $this->query($sql);
	}
}
