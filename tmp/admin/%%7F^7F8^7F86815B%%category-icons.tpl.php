<?php /* Smarty version 2.6.26, created on 2011-12-13 05:12:10
         compiled from /home/vbezruchkin/www/v1700/admin/templates/default/category-icons.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'include_file', '/home/vbezruchkin/www/v1700/admin/templates/default/category-icons.tpl', 11, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "header.tpl", 'smarty_include_vars' => array('css' => "js/ext/plugins/dataview/css/data-view, js/ext/plugins/fileuploadfield/css/file-upload")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div id="box_upload_icon" style="margin-top: 15px;"></div>

<div id="box_category_icons" style="margin-top: 15px;"></div>

<div id="box_default_button" style="margin: 10px 5px 0 0; float: left;"></div>

<div id="box_remove_button" style="margin: 10px 0 0 0; float: left;"></div>

<?php echo smarty_function_include_file(array('js' => "js/ext/plugins/dataview/data-view-plugins, js/ext/plugins/fileuploadfield/FileUploadField, js/admin/category-icons"), $this);?>


<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>