<?
// ======================================================================================
// This application is free software; you can redistribute it and/or
// modify it under the terms of the GNU Lesser General Public
// License as published by the Free Software Foundation; either
// version 2.1 of the License, or (at your option) any later version.
// 
// This class is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
// Lesser General Public License for more details.
// ======================================================================================
// @author     Leon Chevalier (http://www.aciddrop.com)
// @version    0.5
// @copyright  Copyright &copy; 2008 Leon Chevalier, All Rights Reserved
// ======================================================================================
// 0.1  - Released first version
// 0.2  - Added support for different CSS media types
//		- Fixed bug in relative file paths. Thanks to Jeff Badger
//		- Improved old file cleanup function
//		- Changed file versioning to by date instead of by file size
//		- Added check for trailing slash on cachedir
// 0.3  - Added check for gzip compression compatibilty. Thanks to aNTRaX.
//		- Added check for external javascript files. Thanks to aNTRaX.
//		- Fixed bug in head grab function
// 0.3.1 - Added test page directory
// 0.4  - Added installer
//		- Allowed for separation of gzip and far future expires
//		- Changed the way the config works
//		- Added check for relative image paths in CSS
//		- Added support for different rel types for stylesheets
//		- Added compatibility for conditional comments in HEAD
//		- Lots of little tweaks here and there
// 0.4.1 - Fixed bug in CSS image path function. Thanks to RUDE.
//`0.4.2 - Added variable initialisation to prevent notice errors
//		 - Changed CSS and JS compression to gzcompress
//		 - Added some more checks for correct path
//       - Changed to full PHP tags
// 0.4.3 - Added in compress method for direct content compression
//		 - Made the gzipping functions more robust
//		 - Added in check for query string in file paths
//		 - Changed name of view class to avoid conflicts
// 0.4.4 - Added in check for JS and CSS files with PHP extension
//		 - More refinements to the GZIP functions
// 0.4.5 - Changed permissions on written files to 755
//		 - Added ability to ignore certain files
//		 - Added ignore of dynamically generated JS and CSS files
// 0.4.6 - Fixed paths problem
// 0.5 	 - Lots of bug fixes
//		 - Added Data URIs
//		 - Updated install
// ======================================================================================


require("controller/compressor.php");
require("libs/php/view.php"); //Include this for path getting help
require("libs/php/user_agent.php"); //Include this for getting user agent

//We need to know the config
require("config.php");

//Con. the view library
$view = new compressor_view();

//Con. the user agent library
$user_agent = new _speedy_User_agent();

//Con. the js min library
if(substr(phpversion(),0,1) == 5) {
require_once('libs/php/jsmin.php');
$jsmin = new JSMin($contents);
}

//Con. the compression controller
$compressor = new compressor(array('view'=>$view,
								   'options'=>$compress_options,
								   'jsmin'=>$jsmin,
								   'user_agent'=>$user_agent)
							 );
?>