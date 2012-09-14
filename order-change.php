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


define("ESYN_REALM", "order_change");

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");

require_once('.'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'header.php');

if(!$_SESSION['frontendManageMode'])
{
	esynUtil::accessDenied();
}

if(empty($_GET['type']))
{
	die("Type required");
}

$positions = $esynConfig->getConfig('esyndicat_block_positions');

if (!empty($positions))
{
	$positions = explode(',', $positions);
}
else
{
	$positions = array(
		'left',
		'right',
		'center',
		'user1',
		'user2',
		'top',
		'bottom',
		'verybottom',
		'verytop',
	);
}

switch($_GET['type'])
{
	case "blocks":
		foreach($positions as $p)
		{
			if(isset($_GET[$p.'Blocks']) && is_array($_GET[$p.'Blocks']) && !empty($_GET[$p.'Blocks']))
			{
				foreach($_GET[$p.'Blocks'] as $k => $v)
				{
					$v = explode("_", $v);

					if(ctype_digit($v[1]))
					{
						$eSyndiCat->setTable('blocks');
						$eSyndiCat->update(array(
							"id"		=> $v[1],
							"position"	=> $p,
							"order"		=> $k+1
						));
						$eSyndiCat->resetTable();
					}
				}
			}
		}
}

echo "Ok";
$eSyndiCat->resetTable();
die();