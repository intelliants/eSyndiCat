<?php /* Smarty version 2.6.26, created on 2011-12-13 04:44:07
         compiled from /home/vbezruchkin/www/v1700/templates/common/forgot.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', '/home/vbezruchkin/www/v1700/templates/common/forgot.tpl', 11, false),array('function', 'esynHooker', '/home/vbezruchkin/www/v1700/templates/common/forgot.tpl', 17, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<h1><?php echo $this->_tpl_vars['lang']['restore_password']; ?>
</h1>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "notification.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php if ($this->_tpl_vars['form']): ?>
	<div class="box">
		<form action="<?php echo @ESYN_URL; ?>
forgot.php" method="post">
			<p class="field"><strong><?php echo $this->_tpl_vars['lang']['email']; ?>
:</strong><br />
			<input type="text" class="text" name="email" value="<?php if (isset ( $_POST['email'] )): ?><?php echo ((is_array($_tmp=$_POST['email'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php endif; ?>" size="35" /></p>
			<input type="submit" name="restore" value="<?php echo $this->_tpl_vars['lang']['submit']; ?>
" class="button" />
		</form>
	</div>
<?php endif; ?>

<?php echo smarty_function_esynHooker(array('name' => 'forgotBeforeFooter'), $this);?>


<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>