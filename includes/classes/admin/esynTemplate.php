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
 * esynTemplate 
 * 
 * @uses esynAdmin
 * @package 
 */
class esynTemplate extends esynAdmin
{
	/**
	 * inTag 
	 * 
	 * @var mixed
	 * @access public
	 */
	var $inTag;
	
	/**
	 * level 
	 * 
	 * @var float
	 * @access public
	 */
	var $level = 0;

	/**
	 * path 
	 * 
	 * @var mixed
	 * @access public
	 */
	var $path;

	/**
	 * attributes 
	 * 
	 * @var mixed
	 * @access public
	 */
	var $attributes;

	/**
	 * xml 
	 * 
	 * @var mixed
	 * @access public
	 */
	var $xml;

	/**
	 * upgrade 
	 *
	 * If version of new plugin greater than installed then will run upgrade mode
	 * 
	 * @var bool
	 * @access public
	 */
	var $upgrade = false;

	/**
	 * upgradeCode 
	 *
	 * The PHP code which will run when plugin upgrades
	 * 
	 * @var string
	 * @access public
	 */
	var $upgradeCode;

	/**
	 * name 
	 *
	 * The name of template
	 * 
	 * @var string
	 * @access public
	 */
	var $name;

	/**
	 * title 
	 *
	 * The title of template
	 * 
	 * @var string
	 * @access public
	 */
	var $title;
	
	/**
	 * status
	 *
	 * The status of plugin. Must be 'active' or 'inactive'. Other statuses will be ignored and set 'inactive' status
	 *
	 * @var string
	 * @access public
	 */
	var $status;
	
	/**
	 * summary 
	 *
	 * The description of plugin
	 *
	 * @var string
	 * @access public
	 */
	var $summary;
	
	/**
	 * version 
	 *
	 * The version of plugin
	 *
	 * @var string
	 * @access public
	 */
	var $version;
	
	/**
	 * message 
	 *
	 * The message 
	 *
	 * @var string
	 * @access public
	 */
	var $message;
	
	/**
	 * author 
	 *
	 * The author of plugin
	 * 
	 * @var string
	 *
	 */
	var $author;

	/**
	 * contributor 
	 *
	 * The contributor of plugin
	 *
	 * @var string
	 * @access public
	 */
	var $contributor;

	/**
	 * notes 
	 *
	 * The notes of plugin. Will show in the notification box when plugin installed
	 *
	 * @var string
	 * @access public
	 */
	var $notes;

	/**
	 * error
	 *
	 * True if there is any error
	 *
	 * @var	bool
	 * @access public
	 */
	var $error = false;
	
	/**
	 * phrases 
	 *
	 * New phrases array
	 * 
	 * @var arr
	 * @access public
	 */
	var $phrases;

	/**
	 * config
	 *
	 * Config setting array
	 *
	 * @var arr
	 * @access public
	 */
	var $config;

	/**
	 * date
	 *
	 * date release 
	 *
	 * @var	string
	 * @access public
	 */
	var $date;

	/**
	 * compatibility 
	 *
	 * The number of script version with which plugin will work
	 * If it equal FALSE it means the plugin is not compatibility with current version
	 * 
	 * @var mixed
	 * @access public
	 */
	var $compatibility = false;

	/**
	 * parse 
	 * 
	 * Parsing XML document
	 *
	 * @access public
	 * @return void
	 */
	function parse()
	{
		require_once(ESYN_INCLUDES . 'xml' . ESYN_DS . '/xml_saxy_parser.php');
		
		$xmlParser = new SAXY_Parser();

		$xmlParser->xml_set_element_handler(array(&$this, "startElement"), array(&$this, "endElement"));
		$xmlParser->xml_set_character_data_handler(array(&$this, "charData"));
		$xmlParser->xml_set_comment_handler(array(&$this, "commentElement"));

		$xmlParser->parse($this->xml);
	}

	/**
	 * checkFields 
	 *
	 * Checking mandatory fields. If there is any error the 'error' flag will set to true.
	 * 
	 * @access public
	 * @return void
	 */
	function checkFields()
	{
		$mandatoryFields = array("name", "title", "version", "summary", "author", "contributor");

		$notExist = array();

		$vars = get_object_vars($this);
		
		foreach($mandatoryFields as $field)
		{
			if(!array_key_exists($field, $vars) || empty($vars[$field]))
			{
				$this->error = true;
				$notExist[] = $field;
			}
		}

		if($this->error)
		{
			if(empty($notExist))
			{
				$this->message = "Fatal error: Probably specified file is not XML file or is not acceptable";
			}
			else
			{
				$this->message = "Fatal error: The following fields are required: ";
				$this->message .= join(", ", $notExist);
			}
		}
	}

	/**
	 * install 
	 *
	 * Install plugin
	 * 
	 * @access public
	 * @return void
	 */
	function install()
	{
		$this->setTable("config");
		$this->update(array('value' => $this->name), "`name` = 'tmpl'");
		$this->resetTable();

		/**
		 * Add new phrases
		 */
		if(!empty($this->phrases))
		{
			if(!array_key_exists('en', $this->mLanguages))
			{
				foreach($this->mLanguages as $code => $language)
				{
					foreach($this->phrases as $key => $phrase)
					{
						$this->phrases[$key]['lang'] = $language;
						$this->phrases[$key]['code'] = $code;
					}
				}
			}
			else
			{
				foreach($this->mLanguages as $code => $language)
				{
					if('en' != $code)
					{
						foreach($this->phrases as $key => $phrase)
						{
							$new_phrases[] = array(
								"key"		=> $phrase['key'],
								"value"		=> $phrase['value'],
								"lang"		=> $language,
								"category"	=> $phrase['category'],
								"code"		=> $code,
								"plugin"	=> $this->name
							);
						}
					}
				}
			}

			$this->setTable("language");

			foreach($this->phrases as $phrase)
			{
				if($this->exists("`key` = '{$phrase['key']}' AND `code` = '{$phrase['code']}'"))
				{
					$this->update($phrase, "`key` = '{$phrase['key']}' AND `code` = '{$phrase['code']}'");
				}
				else
				{
					$this->insert($phrase);
				}
			}
			
			if(!empty($new_phrases))
			{
				foreach($new_phrases as $phrase)
				{
					if($this->exists("`key` = '{$phrase['key']}' AND `code` = '{$phrase['code']}'"))
					{
						$this->update($phrase, "`key` = '{$phrase['key']}' AND `code` = '{$phrase['code']}'");
					}
					else
					{
						$this->insert($phrase);
					}
				}
			}
			
			$this->resetTable();
		}

		/**
		 * Add new config settings
		 */
		if(!empty($this->config))
		{
			$this->setTable("config");
			
			$maxorder = $this->one("MAX(`order`) + 1");
			$maxorder = (NULL == $maxorder) ? 1 : $maxorder;

			foreach($this->config as $config)
			{
				if($this->exists("`name` = '{$config['name']}'"))
				{
					$this->update($config, "`name` = '{$config['name']}'");
				}
				else
				{
					$this->insert($config, array("order" => $maxorder));
					$maxorder++;
				}
			}

			$this->resetTable();
		}

		$this->mCacher->clearAll('', true);

		return true;
	}

	/**
	 * getScreenshots 
	 *
	 * Return the list of screenshots of template
	 * 
	 * @access public
	 * @return void
	 */
	function getScreenshots()
	{
		$screenshots = array();

		if(!empty($this->name))
		{
			$template_path = ESYN_TEMPLATES . $this->name . ESYN_DS . 'info' . ESYN_DS . 'screenshots' . ESYN_DS;
			$directory = opendir($template_path);

			while (false !== ($file = readdir($directory)))
			{
				if (substr($file, 0, 1) != ".")
				{
					$ext = substr($file, strrpos($file, '.') + 1);

					if(is_file($template_path . $file) && 'jpg' == $ext)
					{
						if('index.jpg' != $file)
						{
							$screenshots[] = $file;
						}
					}
				}
			}

			closedir($directory);
		}

		$screenshots[] = 'index.jpg';

		return array_reverse($screenshots);
	}

	/**
	 * setXml 
	 *
	 * Set XML content
	 * 
	 * @param mixed $str the XML content
	 * @access public
	 * @return void
	 */
	function setXml($str)
	{
		$this->xml = $str;
	}

	/**
	 * getFromPath 
	 *
	 * Set XML file by path
	 * 
	 * @param mixed $filePath the path to XML file
	 * @access public
	 * @return void
	 */
	function getFromPath($filePath)
	{
		if(empty($filePath))
		{
			trigger_error("Install XML path wasn't specified", E_USER_ERROR);

			return false;
		}
		$this->xml = file_get_contents($filePath);
	}

	/**
	 * startElement 
	 * 
	 * @param mixed $parser 
	 * @param mixed $name 
	 * @param mixed $attributes 
	 * @access public
	 * @return void
	 */
	function startElement($parser, $name, $attributes)
	{
		$this->level++;

		$this->inTag = $name;

		$this->attributes = $attributes;

		if($this->inTag == 'template' && isset($attributes['name']))
		{
			$this->name = $attributes['name'];
		}

		$this->path[] = $name;
	}

	/**
	 * endElement 
	 * 
	 * @param mixed $parser 
	 * @param mixed $name 
	 * @access public
	 * @return void
	 */
	function endElement($parser, $name)
	{
		$this->level--;
		array_pop($this->path);
	}

	/**
	 * charData 
	 * 
	 * @param mixed $parser 
	 * @param mixed $text 
	 * @access public
	 * @return void
	 */
	function charData($parser, $text)
	{
		$text = trim($text);

		/* Language phrases */
		if(in_array('phrases', $this->path) && 'phrase' == $this->inTag)
		{
			$this->phrases[] = array(
				"key"		=> $this->attributes['key'],
				"value"		=> $text,
				"lang"		=> $this->attributes['lang'],
				"category"	=> $this->attributes['category'],
				"code"		=> $this->attributes['code'],
				"plugin"	=> $this->name
			);
		}

		/* Config */
		if('config' == $this->inTag)
		{
			$this->config[] = array(
				"group_name"		=> $this->attributes['configgroup'],
				"name"				=> $this->attributes['name'],
				"value"				=> $text,
				"multiple_values"	=> isset($this->attributes['multiplevalues']) ? $this->attributes['multiplevalues'] : '',
				"type"				=> $this->attributes['type'],
				"description"		=> isset($this->attributes['description']) ? $this->attributes['description'] : '',
				"plugin"			=> $this->name
			);
		}

		/* Other fields */
		if(in_array($this->inTag, array('version', 'summary', 'title', 'author', 'contributor', 'notes', 'status', 'date', 'compatibility')))
		{
			$this->{$this->inTag} = $text;
		}
	}

	/**
	 * getMessage 
	 * 
	 * Return message string
	 *
	 * @access public
	 * @return string
	 */
	function getMessage()
	{
		return $this->message;
	}

	/**
	 * getNotes 
	 *
	 * Return notes string
	 * 
	 * @access public
	 * @return string
	 */
	function getNotes()
	{
		return $this->notes;
	}

	/**
	 * commentElement 
	 * 
	 * @param mixed $parser 
	 * @param mixed $name 
	 * @access public
	 * @return void
	 */
	function commentElement($parser, $name)
	{
	}
}

?>
