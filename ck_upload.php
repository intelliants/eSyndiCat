<?php
//##copyright##

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
