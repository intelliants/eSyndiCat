<?php
//##copyright##

define("ESYN_REALM", "templates");

define("ESYN_NUM_TEMPLATES", 10);

esynUtil::checkAccess();

$esynAdmin->factory("Template");

/** gets current page and defines start position **/
$page = empty($_GET['page']) ? 0 : (int)$_GET['page'];
$page = ($page < 1) ? 1 : $page;
$start = ($page - 1) * ESYN_NUM_TEMPLATES;

$msg = array();
$error = false;

/**
 * ACTIONS
 */
if(isset($_POST['set_template']))
{
	$template = $_POST['template'];

	if(empty($template))
	{
		$error = true;
		$msg[] = $esynI18N['template_empty'];
	}

	if(!is_dir(ESYN_TEMPLATES . $template) && !$error)
	{
		$error = true;
		$msg[] = $esynI18N['template_folder_error'];
	}

	$infoXmlFile = ESYN_TEMPLATES . $template . ESYN_DS . 'info' . ESYN_DS .'install.xml';
		
	if(!file_exists($infoXmlFile) && !$error)
	{
		$error = true;

		$esynI18N['template_xmlfile_error'] = str_replace('{template}', $template, $esynI18N['template_xmlfile_error']);
		
		$msg[] = $esynI18N['template_xmlfile_error'];
	}

	if(!$error)
	{
		$esynTemplate = new esynTemplate();

		$esynTemplate->getFromPath($infoXmlFile);
		$esynTemplate->parse();
		$esynTemplate->checkFields();
		$esynTemplate->install();

		if($esynTemplate->error)
		{
			$error = true;
			$msg[] = $esynTemplate->getMessage();
		}
		else
		{
			$esynI18N['template_installed'] = str_replace('{template}', $esynTemplate->title, $esynI18N['template_installed']);

			$msg[] = $esynI18N['template_installed'];
			$msg[] = $esynTemplate->getNotes();
		}
	}

	esynMessages::setMessage($msg, $error);
}

/**
 * ACTIONS
 */

$gNoBc = false;

$gBc[0]['title'] = $esynI18N['manage_templates'];
$gBc[0]['url'] = 'controller.php?file=templates';

$gTitle = $esynI18N['manage_templates']; 

require_once(ESYN_ADMIN_HOME.'view.php');

$tmpl = $esynConfig->getConfig('tmpl', true);

/** get templates list **/
$templates = array();
$xml_files = array();

$directory = opendir(ESYN_TEMPLATES);

while (false !== ($file = readdir($directory)))
{
	if (substr($file, 0, 1) != ".")
	{
		if (is_dir(ESYN_TEMPLATES . $file))
		{
			$infoXmlFile = ESYN_TEMPLATES . $file . ESYN_DS . 'info' . ESYN_DS . 'install.xml';

			if(file_exists($infoXmlFile))
			{
				$xml_files[] = $infoXmlFile;
			}
		}
	}
}

closedir($directory);

if (!empty($xml_files))
{
	$slice_xml_files = array_slice($xml_files, $start, ESYN_NUM_TEMPLATES);

	foreach ($slice_xml_files as $infoXmlFile)
	{
		$esynTemplate = new esynTemplate();

		$esynTemplate->getFromPath($infoXmlFile);
		$esynTemplate->parse();
		$esynTemplate->checkFields();

		if (!$esynTemplate->error)
		{
			$templates[] = array(
				'name'			=> $esynTemplate->name,
				'title'			=> $esynTemplate->title,
				'author'		=> $esynTemplate->author,
				'contributor'	=> $esynTemplate->contributor,
				'date'			=> $esynTemplate->date,
				'description'	=> $esynTemplate->summary,
				'version'		=> $esynTemplate->version,
				'compatibility'	=> $esynTemplate->compatibility,
				'screenshots'	=> $esynTemplate->getScreenshots()
			);
		}

		unset($esynTemplate);
	}
}

$url = ESYN_ADMIN_URL . 'controller.php?file=templates&page={page}';

$esynSmarty->assign('total_templates', count($xml_files));
$esynSmarty->assign('templates', $templates);
$esynSmarty->assign('tmpl', $tmpl);
$esynSmarty->assign('url', $url);

$esynSmarty->display('templates.tpl');
?>
