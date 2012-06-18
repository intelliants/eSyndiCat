<?php /* Smarty version 2.6.26, created on 2011-12-13 05:09:17
         compiled from /home/vbezruchkin/www/v1700/templates/common/error.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'esynHooker', '/home/vbezruchkin/www/v1700/templates/common/error.tpl', 5, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<h1><?php echo $this->_tpl_vars['lang']['error']; ?>
: <span class="error"><?php echo $this->_tpl_vars['error']; ?>
</span></h1>

<?php echo smarty_function_esynHooker(array('name' => 'tplFrontErrorBeforeFooter'), $this);?>


<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>