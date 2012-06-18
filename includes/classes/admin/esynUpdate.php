<?php
//##copyright##

/**
 * esynUpdate 
 * Class for automatic update script core
 * 
 * @uses eSyndiCat
 * @package 
 * @version $id$
 */
class esynUpdate extends eSyndiCat
{
	/**
	 * Update URL
	 *
	 * The URL for getting information about available updates
	 *
	 * @var		string
	 * @access	public
	 */
	var $updateURL = 'http://tools.esyndicat.com/update.php';

	/**
	 * tmpCoreDir 
	 *
	 * The folder where script upload and unpacked files of new version
	 * 
	 * @var string
	 * @access public
	 */
	var $tmpDir = 'update_core';

	/**
	 * tmpDirPath 
	 *
	 * The full path to temporary directory for storing downloaded package
	 * 
	 * @var string
	 * @access public
	 */
	var $tmpDirPath = '';

	/**
	 * tmpPackageName 
	 *
	 * The downloaded package temporary name
	 * 
	 * @var string
	 * @access public
	 */
	var $tmpPackageName = 'upgrade_version.zip';

	/**
	 * updates 
	 *
	 * The array with updates information
	 * 
	 * @var mixed
	 * @access public
	 */
	var $updates;

	/**
	 * packageFiles 
	 *
	 * The list of unpacked files
	 * 
	 * @var mixed
	 * @access public
	 */
	var $packageFiles;

	/**
	 * success 
	 *
	 * The success update flag. It will be true if update process succesfully completed
	 * 
	 * @var mixed
	 * @access public
	 */
	var $success = false;

	/**
	 * msg
	 *
	 * The messages array
	 * 
	 * @var array
	 * @access public
	 */
	var $msg = array();

	/**
	 * method 
	 *
	 * The method of coping files
	 * 
	 * @var mixed
	 * @access public
	 */
	var $method = false;

	/**
	 * updateInfo 
	 *
	 * Update information
	 * 
	 * @var string
	 * @access public
	 */
	var $updateInfo = '';

	/**
	 * serviceFiles 
	 *
	 * The service files which will not copy
	 * 
	 * @var string
	 * @access public
	 */
	var $serviceFiles = array('update.xml');

	/**
	 * getUpdates 
	 *
	 * Return array with information about update
	 * if there is no any new available update function will be return empty array
	 *
	 * response	=> can be update or something else, for future.
	 * version	=> the number of new available version
	 * url		=> the URL of download package
	 * msg		=> message
	 * 
	 * @access public
	 * @return array
	 */
	function getUpdates()
	{
		global $_lc;
		
		$this->updateURL .= '?current=' . urlencode(ESYN_VERSION);
		//$this->updateURL .= '&license=' . $this->mConfig['license'];
		$this->updateURL .= '&domain=' . esynUtil::getDomain(ESYN_URL);

		$result = esynUtil::getPageContent($this->updateURL);
		
		if ($result)
		{
			$result = unserialize($result);
			$result = is_array($result) ? $result : array();
		}

		return $result;
	}

	/**
	 * doUpdateCore 
	 *
	 * Update the files of version
	 * 
	 * @access public
	 * @return void
	 */
	function doUpdateCore()
	{
		$this->updates = $this->getUpdates();

		if(!empty($this->updates))
		{
			if('license_not_valid' == $this->updates['response'])
			{
				$this->msg = $this->mI18N['license_not_valid'];

				return false;
			}

			if('domain_not_valid' == $this->updates['response'])
			{
				$this->msg = $this->mI18N['domain_not_valid'];

				return false;
			}
		}
		else
		{
			return false;
		}

		if(!version_compare($this->updates['version'], ESYN_VERSION, ">"))
		{
			$this->msg = $this->mI18N['no_updates'];

			return false;
		}

		$this->tmpDirPath = ESYN_TMP . $this->tmpDir . ESYN_DS;

		$this->clearDir($this->tmpDirPath);

		if(!file_exists($this->tmpDirPath))
		{
			mkdir($this->tmpDirPath, 0777);
		}

		$result = $this->downloadPackage();

		if(!$result)
		{
			$this->clearDir($this->tmpDirPath);
			
			return false;
		}

		$result = $this->unpackPackage();

		if(!$result)
		{
			$this->clearDir($this->tmpDirPath);
			
			return false;
		}

		$this->getMethod();

		if(!$this->method)
		{
			$this->clearDir($this->tmpDirPath);

			$this->msg = $this->mI18N['can_not_get_method'];

			return false;
		}

		$methodName = 'copy'.$this->method;

		$result = $this->$methodName();

		if($result)
		{
			$this->success = true;

			$this->msg = str_replace('{version}', $this->updates['version'], $this->mI18N['script_success_updated']);

			$this->runXmlUpdate();

			parent::setTable("config");
			parent::update(array('value' => $this->updates['version']), "`name` = 'version'");
			parent::resetTable();
		}

		$this->clearDir($this->tmpDirPath);
		$this->mCacher->clearAll();

		return $result;
	}

	function runXmlUpdate()
	{
		$xmlFile = $this->tmpDirPath . 'update.xml';

		if(!file_exists($xmlFile))
		{
			return false;
		}

		parent::factory("Plugin");

		global $esynPlugin;

		$esynPlugin->setXml(file_get_contents($xmlFile));

		$esynPlugin->doAction('upgrade');

		$this->updateInfo = $esynPlugin->getNotes();

		return true;
	}

	/**
	 * getMethod 
	 *
	 * Return the method of coping files
	 * 
	 * @access public
	 * @return void
	 */
	function getMethod()
	{
		if(function_exists('getmyuid') && function_exists('fileowner'))
		{
			$temp_file = ESYN_TMP.'tempfile';

			if(!file_exists($temp_file))
			{
				$fp = fopen($temp_file, 'w');
				fclose($fp);
			}

			if(getmyuid() == fileowner($temp_file))
			{
				$this->method = 'Direct';
			}
			
			unlink($temp_file);
		}

		if(!$this->method && extension_loaded('ftp'))
		{
			$this->method = 'FTPExt';
		}
		
		if(!$this->method && (extension_loaded('sockets') || function_exists('fsockopen')))
		{
			$this->method = 'FTPSock';
		}
	}

	/**
	 * downloadPackage 
	 *
	 * Download and create package in the temporary directory
	 * 
	 * @access public
	 * @return void
	 */
	function downloadPackage()
	{
		if(empty($this->updates['url']))
		{
			$this->msg = $this->mI18N['url_package_empty'];

			return false;
		}

		$content = @file_get_contents($this->updates['url']);
		
		if(!$content)
		{
			$this->msg = $this->mI18N['get_package_content_error'];

			return false;
		}

		$handle = fopen($this->tmpDirPath . $this->tmpPackageName, 'w');
		fwrite($handle, $content);
		fclose($handle);

		return true;
	}
	
	/**
	 * unpackPackage 
	 *
	 * Unpack the downloaded package
	 * 
	 * @access public
	 * @return void
	 */
	function unpackPackage()
	{
		require_once(ESYN_INCLUDES.'pclzip'.ESYN_DS.'pclzip.lib.php');

		$archive = new PclZip($this->tmpDirPath . $this->tmpPackageName);

		$this->packageFiles = $archive->extract(PCLZIP_OPT_PATH, $this->tmpDirPath);

		unlink($this->tmpDirPath . $this->tmpPackageName);

		if(0 == $this->packageFiles)
		{
			$this->msg = $this->mI18N['unpack_error'];

			return false;
		}

		return true;
	}

	/**
	 * copyDirect 
	 *
	 * If PHP has permissions to overwrite files
	 * 
	 * @access public
	 * @return void
	 */
	function copyDirect($source = null, $target = null)
	{
		if(is_null($source) && is_null($target))
		{
			$source = $this->tmpDirPath;
			$target = ESYN_HOME;
		}

		if(is_dir($source))
		{
			@mkdir($target, 0777);

			$dir = @opendir($source);

			while(false !== ($file = readdir($dir)))
			{
				if(( $file != '.' ) && ( $file != '..' ) && !in_array($entry, $this->serviceFiles))
				{
					if(is_dir($source . $file . ESYN_DS))
					{
						$this->copyDirect($source . $file . ESYN_DS, $target . $file . ESYN_DS);
					}
					else
					{
						copy($source . $file, $target . $file);
					}
				}
			}
			
			closedir($dir);
		}
		else
		{
			copy($source, $target);
		}

		return true;
	}

	/**
	 * copyFTPExt 
	 *
	 * If FTP extension is installed
	 * 
	 * @access public
	 * @return void
	 */
	function copyFTPExt()
	{
		$details = $this->getFTPDetails();

		if(!$details)
		{
			$this->msg = $this->mI18N['ftp_details_error'];

			return false;
		}

		$ftp_link = @ftp_connect($details['hostname'], 21, 5);

		if(!$ftp_link)
		{
			$this->msg = $this->mI18N['ftp_connect_error'];

			return false;
		}

		$ftp_login = @ftp_login($ftp_link, $details['username'], $details['password']);

		if(!$ftp_login)
		{
			$this->msg = str_replace('{username}', $details['username'], $this->mI18N['ftp_login_error']);

			return false;
		}
		
		ftp_pasv($ftp_link, true);

		$list = ftp_nlist($ftp_link, '.');
		
		$ftpBaseDir = $this->getFTPBaseDir($list);

		if(!empty($this->packageFiles))
		{
			foreach($this->packageFiles as $file)
			{
				if(in_array($file['stored_filename'], $this->serviceFiles))
				{
					continue;
				}
			
				if($file['folder'])
				{
					if(!file_exists($ftpBaseDir.$file['stored_filename']))
					{
						$result = @ftp_mkdir($ftp_link, $ftpBaseDir.$file['stored_filename']);
					}
				}
				else
				{
					$fp_handle = fopen($file['filename'], 'r');

					$type = $this->is_binary($file['filename']) ? FTP_BINARY : FTP_ASCII;

					$result = ftp_fput($ftp_link, $ftpBaseDir.$file['stored_filename'], $fp_handle, $type);

					fclose($fp_handle);
				}
			}
		}

		return true;
	}

	/**
	 * copyFTPSock 
	 *
	 * Using PemFTP class
	 * 
	 * @access public
	 * @return void
	 */
	function copyFTPSock()
	{
		$details = $this->getFTPDetails();

		if(!$details)
		{
			$this->msg = $this->mI18N['ftp_details_error'];

			return false;
		}

		require_once(ESYN_INCLUDES.'pemftp'.ESYN_DS.'ftp_class.php');

		$ftp = new ftp();

		$ftp->SetServer($details['hostname']);

		$ftp_connect = $ftp->connect();

		if(!$ftp_connect)
		{
			$this->msg = $this->mI18N['ftp_connect_error'];

			return false;
		}

		$ftp_login = $ftp->login($detais['username'], $detais['password']);

		if(!$$ftp_login)
		{
			$this->msg = str_replace('{username}', $details['username'], $this->mI18N['ftp_login_error']);

			return false;
		}

		$ftp->SetType(FTP_ASCII);
		$ftp->Passive(true);

		$list = $ftp->nlist(".");

		$ftpBaseDir = $this->getFTPBaseDir($list);

		if(!empty($this->packageFiles))
		{
			foreach($this->packageFiles as $file)
			{
				if(in_array($file['stored_filename'], $this->serviceFiles))
				{
					continue;
				}
			
				if($file['folder'])
				{
					if(!file_exists($ftpBaseDir.$file['stored_filename']))
					{
						$result = $ftp->mkdir($ftpBaseDir.$file['stored_filename']);
					}
				}
				else
				{
					$result = $ftp->put($file['filename'], $ftpBaseDir . $file['stored_filename']);
				}
			}
		}

		$ftp->quit();

		return true;
	}

	/**
	 * getFTPDetails 
	 *
	 * Return FTP details
	 * 
	 * @access public
	 * @return void
	 */
	function getFTPDetails()
	{
		$details = array();

		if(!isset($_POST))
		{
			return false;
		}

		$details['hostname'] = isset($_POST['hostname']) && !empty($_POST['hostname']) ? $_POST['hostname'] : '';
		$details['username'] = isset($_POST['username']) && !empty($_POST['username']) ? $_POST['username'] : '';
		$details['password'] = isset($_POST['password']) && !empty($_POST['password']) ? $_POST['password'] : '';

		return $details;
	}

	/**
	 * clearDir 
	 *
	 * Remove all files and directories
	 * 
	 * @param mixed $directory 
	 * @param mixed $empty 
	 * @access public
	 * @return void
	 */
	function clearDir($directory, $empty = false)
	{
		if(substr($directory, -1) == DIRECTORY_SEPARATOR)
		{
			$directory = substr($directory, 0, -1);
		}

		if(!file_exists($directory) || !is_dir($directory))
		{
			return false;
		}
		elseif(is_readable($directory))
		{
			$handle = opendir($directory);
			
			while (false !== ($item = readdir($handle)))
			{
				if($item != '.' && $item != '..')
				{
					$path = $directory.'/'.$item;
					
					if(is_dir($path)) 
					{
						$this->clearDir($path);
					}
					else
					{
						unlink($path);
					}
				}
			}
			closedir($handle);

			if($empty == false)
			{
				if(!rmdir($directory))
				{
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * getFTPBaseDir 
	 *
	 * Return path for coping files
	 * 
	 * @param mixed $list 
	 * @access public
	 * @return void
	 */
	function getFTPBaseDir($list)
	{
		$ftp_basedir = '';

		if(empty($list))
		{
			return false;
		}

		foreach($list as $entry)
		{
			if (stristr(ESYN_HOME, '/'.$entry))
			{
				$split = explode($entry, ESYN_HOME);
				
				$ftp_basedir = str_replace($split[0], '', ESYN_HOME);
				
				break;
			}
		}

		return $ftp_basedir;
	}

	/**
	 * getMsg 
	 *
	 * Return msg array
	 * 
	 * @access public
	 * @return void
	 */
	function getMsg()
	{
		return $this->msg;
	}

	/**
	 * getUpdateInfo 
	 *
	 * Return update info
	 * 
	 * @access public
	 * @return void
	 */
	function getUpdateInfo()
	{
		return $this->updateInfo;
	}

	/**
	 * is_binary 
	 * 
	 * @param mixed $file 
	 * @access public
	 * @return void
	 */
	function is_binary($file)
	{
		$content = file_get_contents($file);

		return (bool) preg_match('|[^\x20-\x7E]|', $content);
	}
}

?>
