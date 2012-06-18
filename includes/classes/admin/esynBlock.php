<?php
//##copyright##

/**
 * esynBlock 
 * 
 * @uses esynAdmin
 * @package 
 * @version $id$
 */
class esynBlock extends esynAdmin
{
	/**
	 * mTable 
	 * 
	 * @var string
	 * @access public
	 */
	var $mTable = "blocks";

	var $types = array("plain", "html", "smarty", "php");
	var $positions = array();

	function esynBlock()
	{
		parent::eSyndiCat();

		$this->positions = explode(",", $this->mConfig['esyndicat_block_positions']);
	}


	/**
	 * insert 
	 * 
	 * @param mixed $aBlock 
	 * @access public
	 * @return void
	 */
	function insert($block)
	{
		$this->startHook("beforeBlockInsert");

		if(empty($block))
		{
			$this->message = 'The Block parameter is empty.';

			return false;
		}

		if(empty($block['lang']) || !array_key_exists($block['lang'], $this->mLanguages))
		{
			$block['lang'] = ESYN_LANGUAGE;
		}

		if(!isset($block['type']) || !in_array($block['type'], $this->types, true))
		{
			$block['type'] = 'plain';
		}
		
		$order = $this->one("MAX(`order`) + 1");
		$block['order'] = (NULL == $order) ? 1 : $order;

		if(isset($block['visible_on_pages']))
		{
			if(!empty($block['visible_on_pages']))
			{
				$visible_on_pages = $block['visible_on_pages'];
			}

			unset($block['visible_on_pages']);
		}

		if('1' != $block['multi_language'])
		{
			if(isset($block['block_languages']))
			{
				$block_languages = $block['block_languages'];
				$title = $block['title'];
				$contents = $block['contents'];

				unset($block['block_languages'], $block['title'], $block['contents']);
			}
		}

		$id = parent::insert($block);

		if('1' != $block['multi_language'])
		{
			if(isset($block_languages))
			{
				$language_content = array();

				foreach($block_languages as $block_language)
				{
					$language_content[] = array(
						'key'		=> 'block_title_blc' . $id,
						'value'		=> $title[$block_language],
						'lang'		=> $this->mLanguages[$block_language],
						'category'	=> 'common',
						'code'		=> $block_language
					);

					$language_content[] = array(
						'key'		=> 'block_content_blc' . $id,
						'value'		=> $contents[$block_language],
						'lang'		=> $this->mLanguages[$block_language],
						'category'	=> 'common',
						'code'		=> $block_language
					);
				}

				parent::setTable("language");
				parent::insert($language_content);
				parent::resetTable();
			}
		}

		if(isset($visible_on_pages) && !empty($visible_on_pages))
		{
			$data = array();

			foreach($visible_on_pages as $key => $page)
			{
				$data[] = array(
					'block_id' => $id,
					'page' => $page
				);
			}

			parent::setTable("block_show");
			parent::insert($data);
			parent::resetTable();
		}

		$this->startHook("afterBlockInsert");

		return true;
	}

	function delete($ids)
	{
		$this->startHook("beforeBlockDelete");

		if(empty($ids))
		{
			$this->message = 'The ID parameter is empty.';

			return false;
		}

		$where = $this->convertIds('id', $ids);
		parent::delete($where);

		$where = $this->convertIds('block_id', $ids);
		parent::setTable("block_show");
		parent::delete($where);
		parent::resetTable();

		if(is_array($ids))
		{
			parent::setTable("language");

			foreach($ids as $id)
			{
				$where = "`key` = 'block_title_blc{$id}'";
				parent::delete($where);

				$where = "`key` = 'block_content_blc{$id}'";
				parent::delete($where);
			}

			parent::resetTable();
		}
		else
		{
			parent::setTable("language");
			
			$where = "`key` = 'block_title_blc{$ids}'";
			parent::delete($where);

			$where = "`key` = 'block_content_blc{$ids}'";
			parent::delete($where);

			parent::resetTable();
		}

		$this->startHook("afterBlockDelete");

		return true;
	}

	function update($fields, $ids)
	{
		$this->startHook("beforeBlockUpdate");

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

		if(isset($fields['visible_on_pages']))
		{
			if(!empty($fields['visible_on_pages']))
			{
				if(is_array($ids))
				{
					$page_ids = $ids;
				}
				else
				{
					$page_ids[] = $ids;
				}

				$visible_on_pages = $fields['visible_on_pages'];

				$data = array();

				foreach($visible_on_pages as $key => $page)
				{
					foreach($page_ids as $id)
					{
						$data[] = array(
							'block_id' => $id,
							'page' => $page
						);
					}
				}

				$where = $this->convertIds('block_id', $ids);

				parent::setTable("block_show");
				parent::delete($where);
				parent::insert($data);
				parent::resetTable();
			}

			unset($fields['visible_on_pages']);
		}

		
		if(isset($fields['multi_language']) && '1' != $fields['multi_language'])
		{
			if(isset($fields['block_languages']))
			{
				$block_languages = $fields['block_languages'];
				$title = $fields['title'];
				$contents = $fields['contents'];

				unset($fields['block_languages'], $fields['title'], $fields['contents']);
			}
		}

		$where = $this->convertIds('id', $ids);

		parent::update($fields, $where);

		if(isset($fields['multi_language']) && '1' != $fields['multi_language'])
		{
			if(isset($block_languages))
			{
				$language_content_where = array();
				$language_content = array();

				foreach($block_languages as $block_language)
				{
					if(is_array($ids))
					{
						foreach($ids as $id)
						{
							$language_content[] = array(
								'key'		=> 'block_title_blc' . $id,
								'value'		=> $title[$block_language],
								'lang'		=> $this->mLanguages[$block_language],
								'category'	=> 'common',
								'code'		=> $block_language
							);

							$language_content[] = array(
								'key'		=> 'block_content_blc' . $id,
								'value'		=> $contents[$block_language],
								'lang'		=> $this->mLanguages[$block_language],
								'category'	=> 'common',
								'code'		=> $block_language
							);

							$language_content_where[] = 'block_title_blc' . $id;
							$language_content_where[] = 'block_content_blc' . $id;
						}
					}
					else
					{
						$id = $ids;

						$language_content[] = array(
							'key'		=> 'block_title_blc' . $id,
							'value'		=> $title[$block_language],
							'lang'		=> $this->mLanguages[$block_language],
							'category'	=> 'common',
							'code'		=> $block_language
						);

						$language_content[] = array(
							'key'		=> 'block_content_blc' . $id,
							'value'		=> $contents[$block_language],
							'lang'		=> $this->mLanguages[$block_language],
							'category'	=> 'common',
							'code'		=> $block_language
						);

						$language_content_where[] = 'block_title_blc' . $id;
						$language_content_where[] = 'block_content_blc' . $id;
					}
				}

				parent::setTable("language");
				parent::delete("`key` IN ('" . join("','", $language_content_where) . "')");
				parent::insert($language_content);
				parent::resetTable();
			}
		}
		else
		{
			$language_content_where = array();

			if(is_array($ids))
			{
				foreach($ids as $id)
				{
					$language_content_where[] = 'block_title_blc' . $id;
					$language_content_where[] = 'block_content_blc' . $id;
				}
			}
			else
			{
				$language_content_where[] = 'block_title_blc' . $ids;
				$language_content_where[] = 'block_content_blc' . $ids;
			}

			parent::setTable("language");
			parent::delete("`key` IN ('" . join("','", $language_content_where) . "')");
			parent::resetTable();
		}

		$this->startHook("afterBlockUpdate");

		return true;
	}
}