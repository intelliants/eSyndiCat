<?php

function smarty_outputfilter_compress($source, &$smarty)
{
	require_once(ESYN_INCLUDES.'php_speedy'.ESYN_DS.'php_speedy.php');
	
	return $compressor->finish($source);
}

?>
