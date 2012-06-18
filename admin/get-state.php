<?php
//##copyright##

require_once('.' . DIRECTORY_SEPARATOR . 'header.php');

require_once(ESYN_CLASSES . 'esynJSON.php');

$json = new Services_JSON();

$state = array();

if (!empty($currentAdmin['state']))
{
	$state = unserialize($currentAdmin['state']);
}

echo 'Ext.appState = ';

if (isset($state['index_blocks']) && !empty($state['index_blocks']))
{
	echo $state['index_blocks'];
}
else
{
	echo '[]';
}

echo ';';

