<?php
//##copyright##

/**
 * esynI18N
 *
 * Implements main class for eSyndiCat administration board 
 * 
 * @uses esynAdmin
 * @package 
 * @version $id$
 */
class esynLanguage extends eSyndiCat
{

	/**
	 * mTable 
	 * 
	 * @var string
	 * @access public
	 */
	var $mTable = "language";

	/**
	 * getLang 
	 *
	 * Returns language strings in convenient format
	 * 
	 * @param string $aLang language title 
	 * @param string $admin 
	 * @access public
	 * @return arr
	 */
	function getLang($lang, $admin = false)
	{
		$sql = "SELECT `key`, `value` FROM `{$this->mTable}` ";
		$sql .= "WHERE `code` = :code";
		
		if($admin)
		{
			$sql .= " AND (`category` <> 'frontend')";
		}

		return $this->getKeyValue($sql, array('code' => $lang));
	}

	/**
	 * getNumPhrases 
	 *
	 * Returns total phrases by language
	 * 
	 * @param string $aLang language
	 * @param string $aCategory phrase group (admin, common etc)
	 * @access public
	 * @return int
	 */
	function getNumPhrases($aLang = '',$aCategory = '')
	{
		$sql = "SELECT count(*) FROM `".$this->mTable."` WHERE lang='{$aLang}'";
		if(!empty($aCategory)) {
			$sql.=" and `category`='{$aCategory}'";
		}
		return $this->getOne($sql);
	}	

	/**
	 * getPhrases 
	 *
	 * Returns a list of language phrases by some given values
	 * 
	 * @param string $aText key/value text
	 * @param string $aType search type (1 - values text, 2 - keys text, 3 - both values and keys texts)
	 * @param string $aLanguage 
	 * @param string $aCategory 
	 * @param int $aStart starting position
	 * @param int $aLimit number of phrases to be returned
	 * @access public
	 * @return arr
	 */
	function getPhrases($text = '', $type = '', $language = '', $category = '', $start = 0, $limit = 0)
	{
		$text = $this->escape_sql($text);
		$type = $this->escape_sql($type);
		
		$language = $this->escape_sql($language);
		$category = $this->escape_sql($category);

		$start = (int)$start;
		$limit = (int)$limit;

		$sql = "SELECT * FROM `{$this->mTable}` ";
		$sql .= "WHERE 1 ";
		
		$sql .= $language ? "AND `code` = '{$language}' " : ' ';
		
		if ('1' == $type)
		{
			$sql .= "AND `value` LIKE '%{$text}%' ";
		}
		elseif ('2' == $type)
		{
			$sql .= "AND `key` LIKE '%{$text}%' ";
		}
		elseif ('3' == $type)
		{
			$sql .= "AND `key` LIKE '%{$text}%' OR `value` LIKE '%{$text}%' ";
		}
		
		$sql .= $category ? "AND `category` = '{$category}' " : ' ';
		$sql .= $limit ? "LIMIT {$start}, {$limit}" : '';

		return $this->getAll($sql);
	}

	/**
	 * updateLang 
	 *
	 * Updates phrase record in language file
	 * 
	 * @param string $aKey key value for a phrase
	 * @param string $aValue new value
	 * @param string $aLang language
	 * @param string $aCategory phrase category
	 * @access public
	 * @return bool
	 */
	function updateLang($aKey, $aValue = '', $aLang = '', $aCategory = 'common')
	{
		$sql = "UPDATE `".$this->mPrefix."language` ";
		$sql .= "SET `value` = '{$aValue}' ";
		$sql .= "WHERE `key` = '{$aKey}' ";
		$sql .= $aLang ? "AND `code` = '{$aLang}' " : '';
		$sql .= $aCategory ? "AND `category` = '{$aCategory}'" : '';

		return $this->query($sql);
	}

	/**
	 * deletePhrase 
	 *
	 * Removes phrase by key
	 * 
	 * @param mixed $aKey phrase key
	 * @param string $aLang 
	 * @access public
	 * @return bool
	 */
	function deletePhrase($aKey, $aLang = '')
	{
		$sql = "DELETE FROM `".$this->mTable."`";
		$sql .= "WHERE `key` = '{$aKey}' ";
		$sql .= $aLang ? "AND `code = '{$aLang}' " : '';

		return $this->query($sql);		
	}
	
	/**
	 * getLangAbsentFields 
	 * 
	 * @param mixed $aLangParent 
	 * @param mixed $aLangChild 
	 * @param mixed $aCategory 
	 * @access public
	 * @return void
	 */
	function getLangAbsentFields($aLangParent, $aLangChild, $aCategory)
	{
		$cause = ($aCategory == 'allfields') ? "" : "AND `category`='{$aCategory}'";

		$sql = "SELECT `key`, `value`, `category` ";
		$sql .= "FROM `{$this->mPrefix}language` ";
		$sql .= "WHERE `code='{$aLangParent}' {$cause}";		
		$arrayLangParent = $this->getAllByFirstFieldAsKey($sql);

		$sql = "SELECT `key`, `value`, `category` ";
		$sql .= "FROM `{$this->mPrefix}language` ";
		$sql .= "WHERE `code='{$aLangChild}' {$cause}";		
		$arrayLangChild = $this->mDb->getAllByFirstAsKey($sql);

		foreach ($arrayLangParent as $key => $value)
		{
			if (!array_key_exists($key, $arrayLangChild))
			{
				$differ[$key]=$value;
			}
		}

		return $differ;
	}
}
