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
 * esynCacher 
 * 
 * Class for caching (mostly used to store database results and other serialized arrays or objects) also can be used to store static strings
 * Note: cache file names should not contain '.'(point) character
 *
 * @package 
 * @version $id$
 */
class esynCacher
{
	/**
	 * caching 
	 * 
	 * This property means whether enable cache or not.
	 * It is needed because even if admin turns off caching we get $config first even before we determine whether caching is enabled.
	 *
	 * @var string
	 * @access public
	 */
	var $caching;
	
	/**
	 * savePath 
	 *
	 * Path to the cache Directory
	 * 
	 * @var mixed
	 * @access public
	 */
	var $savePath = ESYN_CACHEDIR;

	/**
	 * filePath 
	 *
	 * Path to the Cache File
	 * 
	 * @var string
	 * @access public
	 */
	var $filePath = '';

	/**
	 * pid 
	 * 
	 * Proccess id (for the unique swap file name purposes only)
	 *
	 * @var mixed
	 * @access public
	 */
	var $pid;
	
	/**
	 * esynCacher 
	 *
	 * Constructor for CacheEngine instances
	 * 
	 * @access public
	 * @return void
	 */
	function esynCacher()
	{
		$this->caching = true;
		$this->pid = getmypid();
		$parent_dir = dirname($this->savePath);

		if(!file_exists($this->savePath))
		{
			esynUtil::mkdir($this->savePath);
		}
		if(!is_writeable($this->savePath))
		{
			$e  = 'Directory Creation Error | tmp_dir_permissions | ';
			$e .='Can not create the '.$this->savePath.' directory. ';
			$e .= 'Please set writable permission for '.$parent_dir.' directory.';
			trigger_error($e, E_USER_ERROR);
		}
	}
	
	/**
	 * get 
	 * 
	 * Retrieves a Cache file and returns either an object or a string
	 *
	 * @param string $FileName Name of File in Cache
	 * @param int $Seconds Number of Seconds until File is considered old
	 * @param mixed $IsObject Return an object from Cache?
	 * @access public
	 * @return void
	 */
	function get( $FileName , $Seconds = 0, $IsObject = false)
	{
		$FileName = str_replace(".","",$FileName);
		$this->filePath = $this->savePath.$FileName;
		
		if (!$this->caching || !is_file($this->filePath))
		{
			return false;
		}

		$return = false;

		if ($Seconds == 0)
		{
			$return = $this->_read();
		} 
		else
		{
			if (filemtime($this->filePath) > ($_SERVER['REQUEST_TIME']-$Seconds))
			{
				$return = $this->_read();
			}
			else
			{
				$this->remove($FileName);
				return false;
			}
		}
		if ($IsObject && !empty($return))
		{
		    $return = unserialize($return);
		}

		return $return;
	}
	
	/**
	 * write 
	 *
	 * Writes data to the cache
	 * 
	 * @param string $file File Name (may be encoded)
	 * @param mixed $Data Data to write
	 * @access public
	 * @return void
	 */
	function write($file, $Data)
	{
		if (!$this->caching)
		{
			return true;
		}	

		$file = str_replace(".","",$file);

		if (is_array($Data) || is_object($Data))
	    {
			$Data = serialize($Data);
	    }

		$this->filePath = $this->savePath . $file;

		$file = fopen($this->filePath, "wb");
	    
		if (!$file)
	    {
		    trigger_error('Cacher::write(): Could not open file for writing.', E_USER_WARNING);				
		    return false;
	    }    

		if (flock($file, LOCK_EX))
		{
			$len_data = strlen($Data);
			fwrite($file, $Data, $len_data);	
			flock($file, LOCK_UN);
		}
		else
		{
			trigger_error("Cacher::write(): Could not LOCK the file".$this->filePath." for writing.", E_USER_WARNING);
		    return false;
		}

		fclose($file);

	    return true;
	}

	/**
	 * remove
	 *
	 * Removes a cache file from the cache directory 
	 * 
	 * @param string $file_name File Name to remove (will be encoded)
	 * @access public
	 * @return void
	 */
	function remove( $file_name )
	{
		$this->filePath = $this->savePath.$file_name;
		
		if (file_exists($this->filePath))
	    {
			if (unlink($this->filePath))
	        {
				return true;
			}
			else
			{
				trigger_error(__CLASS__.'::remove(): Unable to remove from cache file' , E_USER_NOTICE);
			    
				return false;
			}
        }
	    else
	    {
			return false;
        }
	}
	
	/**
	 * _read 
	 * 
	 * Reads the local file from the cache directory
	 *
	 * @access protected
	 * @return mixed
	 */
	function _read()
	{
		$return_data = file_get_contents($this->filePath);

	    if (false === $return_data)
	    {
		    trigger_error(__CLASS__. '::_read(): Unable to read file('.$this->filePath.' contents', E_USER_WARNING);
	    	return false;
	    }

	    return $return_data;
	}

	/**
	 * clearAll 
	 * 
	 * Clears all cache
	 *
	 * @param string $mask The mask of removed cache file
	 * @access public
	 * @return bool
	 */
	function clearAll($mask = '', $reloadCache = false)
	{
		$files = scandir($this->savePath);
		$files = array_slice($files, 2);

		foreach($files as $f)
		{
			$file = $this->savePath.$f;

			if(!empty($mask))
			{
				$filter = (bool)stristr($file, $mask);
			}
			else
			{
				$filter = true;
			}
			
			if(!is_dir($file) && $filter)
			{
				unlink($this->savePath.$f);
			}
		}

		if($reloadCache)
		{
			$_SESSION['reloadCache'] = true;
		}

		return true;
	}
}
