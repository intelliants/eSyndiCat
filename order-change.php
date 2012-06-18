<?php
//##copyright##

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