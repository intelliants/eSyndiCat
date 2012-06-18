<?php /* Smarty version 2.6.26, created on 2011-12-13 04:28:04
         compiled from /home/vbezruchkin/www/v1700/admin/templates/default/plugins.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'preventCsrf', '/home/vbezruchkin/www/v1700/admin/templates/default/plugins.tpl', 4, false),array('function', 'include_file', '/home/vbezruchkin/www/v1700/admin/templates/default/plugins.tpl', 7, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "header.tpl", 'smarty_include_vars' => array('css' => "js/ext/plugins/panelresizer/css/PanelResizer")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div id="box_plugins" style="margin-top: 15px;">
	<?php echo esynUtil::preventCsrf(array(), $this);?>

</div>

<?php echo smarty_function_include_file(array('js' => "js/intelli/intelli.grid, js/intelli/intelli.gmodel, js/ext/plugins/bettercombobox/betterComboBox, js/ext/plugins/panelresizer/PanelResizer, js/ext/plugins/progressbarpager/ProgressBarPager, js/admin/plugins"), $this);?>


<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>