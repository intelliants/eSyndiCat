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


if(isset($_GET['idcategory']) && preg_match("/\D/", $_GET['idcategory']))
{
	header("HTTP/1.1 404 Not found");
	print("Powered By eSyndicat ");
	die();
}

if(isset($_GET['idplan']) && preg_match("/\D/", $_GET['idplan']))
{
	header("HTTP/1.1 404 Not found");
	print("Powered By eSyndicat ");
	die();
}

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'header.php');

$eSyndiCat->factory("Listing");

if(isset($_GET['action']))
{
	$eSyndiCat->loadClass("JSON");

	$json = new Services_JSON();

	if('getfields' == $_GET['action'])
	{
		$id_category = (int)$_GET['idcategory'];
		$id_plan = isset($_GET['idplan']) ? (int)$_GET['idplan'] : null;
		$id_listing = isset($_GET['idlisting']) ? (int)$_GET['idlisting'] : null;
		$labelsType = array('combo', 'radio', 'checkbox');

		$eSyndiCat->setTable("categories");
		$category = $eSyndiCat->row("*", "`id` = '{$id_category}'");
		$eSyndiCat->resetTable();

		$part = isset($_GET['part']) && !empty($_GET['part']) && in_array($_GET['part'], array('suggest', 'edit')) ? $_GET['part'] : 'suggest';

		$fields = $esynListing->getFieldsByPage($part, $category);

		if($id_listing > 0)
		{
			$listing = $esynListing->getListingById($id_listing, (int)$esynAccountInfo['id']);
		}

		if(!empty($fields))
		{
			foreach($fields as $key => $field)
			{
				$fields[$key]['title'] = isset($esynI18N['field_'. $field['name']]) ? $esynI18N['field_'. $field['name']] : 'Unknow';

				if('email' == $fields[$key]['name'])
				{
					$fields[$key]['default'] = (isset($esynAccountInfo)) ? $esynAccountInfo['email'] : '';
				}

				if(in_array($field['type'], $labelsType))
				{
					$values = explode(',', $field['values']);

					foreach($values as $value)
					{
						$fields[$key]['labels'][$value] = $esynI18N['field_'. $field['name'].'_'.$value];
					}
				}

				if(isset($listing))
				{
					$fields[$key]['default'] = $listing[$field['name']];
				}
			}
		}
		else
		{
			$fields = "";
		}

		echo $json->encode($fields);
	}

	exit;
}
?>
