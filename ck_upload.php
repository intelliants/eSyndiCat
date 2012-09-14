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


require_once('.'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'header.php');

$err = 0;
$oFile = $_FILES['upload'] ;

if (isset($_GET['Type']) && 'Image' == $_GET['Type'])
{
	$sErrorNumber = '0' ;
	
	$imgtypes = array("image/gif"=>"gif", "image/jpeg"=>"jpg", "image/pjpeg"=>"jpg", "image/png"=>"png");
	
	$sFileUrl = 'uploads/';
	
	$ext = array_key_exists($oFile['type'], $imgtypes) ? $imgtypes[$oFile['type']] : false;
	
	if (!$ext) 
	{
		$err = true;
		SendResults( '202' );
	}
	
	$tok = esynUtil::getNewToken();
	$fname = "{$tok}.{$ext}";
	
	if (!$err)
	{
		list($width, $height, $type, $attr) = getimagesize($oFile['tmp_name']);

		if($width > 0 && $height > 0)
		{
			$eSyndiCat->loadClass("Image");

			$image = new esynImage();

			$image->processImage($oFile, ESYN_HOME . $sFileUrl . $fname, $width, $height, 1001);
		}
		else
		{
			move_uploaded_file($oFile['tmp_name'], ESYN_HOME . $sFileUrl . $fname);
		}
	}

	SendResults( $err, $sFileUrl . $fname, $fname ) ;
}

// This is the function that sends the results of the uploading process.
function SendResults( $errorNumber, $fileUrl = '', $fileName = '', $customMsg = '' )
{
	$callback = (int)$_GET['CKEditorFuncNum'];
	$output = '<html><body><script type="text/javascript">';
	$output .= "window.parent.CKEDITOR.tools.callFunction('$callback', '$fileUrl', '$customMsg');";
	$output .= '</script></body></html>';
	die($output);
}
