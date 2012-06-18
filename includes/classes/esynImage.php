<?php
//##copyright##

require_once(ESYN_INCLUDES.'asido'.ESYN_DS.'class.asido.php');

class esynImage extends Asido
{
	function esynImage()
	{
		if (extension_loaded('imagick'))
		{
			/**
			 * Enable the Imagick Shell driver
			 */
			parent::driver('imagick_shell');
		}
		elseif (extension_loaded('magickwand'))
		{
			parent::driver('magick_wand');
		}
		elseif (extension_loaded('gd'))
		{
			/**
			 * Enable the GD driver
			 */
			parent::driver('gd');
		}
	}

	/**
	 * Return image file extension
	 *
	 * @param string $aFile file mime type
	 *
	 * @return string
	 */
	function getImageExt($aFile)
	{
		$imgtypes = array("image/gif"=>"gif", "image/jpeg"=>"jpg", "image/pjpeg"=>"jpg", "image/png"=>"png");

		return array_key_exists($aFile, $imgtypes) ? '.'.$imgtypes[$aFile] : '';
	}

	/**
	 * Process image types here and returns filename to write
	 *
	 * @param array $aFile uploaded file information
	 * @param string $aName the file name including path
	 * @param integer $aWidth image width
	 * @param integer $aHeight image height
	 * @param integer $aResizeMode image resize mode
	 *
	 * @return string
	 */
	function processImage($aFile, $aName, $aWidth, $aHeight, $aResizeMode = 1001)
	{
		global $esynConfig;
		
		$watermark_positions = array(
			'top_left'		=> 3001,
			'top_center'	=> 3002,
			'top_right'		=> 3003,
			'middle_left'	=> 3004,
			'middle_center'	=> 3005,
			'middle_right'	=> 3006,
			'bottom_left'	=> 3007,
			'bottom_center'	=> 3008,
			'bottom_right'	=> 3009
		);
		
		$position = $esynConfig->getConfig('site_watermark_position');
		
		$watermark_position = !empty($position) && array_key_exists($position, $watermark_positions) ? $watermark_positions[$position] : $watermark_positions['bottom_right'];
		
		/**
		 * Create an Asido_Image object
		 */
		$image = $this->image($aFile['tmp_name'], $aName);

		/**
		 * Resize them proportionally to make them fit configured dimensions
		 */

		/*
		 * ASIDO_RESIZE_PROPORTIONAL
		 * The mod will attempt to fit the image inside the "frame" create by the $width and $height arguments
		 * 1001
		 */

		/*
		 * ASIDO_RESIZE_STRETCH
		 * The mod will stretch the image if necessary to fit into that "frame"
		 * 1002
		 */

		/*
		 * ASIDO_RESIZE_FIT
		 * The will attempt to resize the image proportionally only if it does not fit inside the "frame" set by the provided width and height:
		 * if it does fit, the image will not be resized at all
		 * 1003
		 */

		$this->resize($image, $aWidth, $aHeight, $aResizeMode);

		/**
		 * Add watermark to images
		 */
		$site_watermark = $esynConfig->getConfig('site_watermark');
		
		if ($site_watermark)
		{
			$watermark = ESYN_HOME . 'uploads' . ESYN_DS . $site_watermark;
			
			if(file_exists($watermark))
			{
				$this->watermark($image, $watermark, $watermark_position);
			}
		}

		/**
		 * Save it and overwrite the file if it exists
		 */
		$image->save(ASIDO_OVERWRITE_ENABLED);
	}
}

?>
