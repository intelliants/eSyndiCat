<?php /* Smarty version 2.6.26, created on 2011-12-13 04:27:09
         compiled from /home/vbezruchkin/www/v1700/templates/common/login.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'esynHooker', '/home/vbezruchkin/www/v1700/templates/common/login.tpl', 7, false),array('modifier', 'escape', '/home/vbezruchkin/www/v1700/templates/common/login.tpl', 12, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<h1><?php echo $this->_tpl_vars['lang']['login']; ?>
</h1>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "notification.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php echo smarty_function_esynHooker(array('name' => 'tplFrontLoginAfterHeader'), $this);?>


<form action="<?php echo @ESYN_URL; ?>
login.php" method="post">
	<p class="field">
		<strong><?php echo $this->_tpl_vars['lang']['username']; ?>
:</strong><br />
		<input type="text" class="text" tabindex="4" name="username" size="26" value="<?php if (isset ( $_POST['username'] ) && ! empty ( $_POST['username'] )): ?><?php echo ((is_array($_tmp=$_POST['username'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php endif; ?>" />
	</p>

	<p class="field">
		<strong><?php echo $this->_tpl_vars['lang']['password']; ?>
:</strong><br />
		<input type="password" class="text" tabindex="5" name="password" size="26" value="" />
	</p>

	<p class="field">
		<input type="checkbox" tabindex="3" name="rememberme" value="1" id="rememberme" <?php if (isset ( $_POST['rememberme'] ) && $_POST['rememberme'] == '1'): ?>checked="checked"<?php endif; ?> />&nbsp;<label for="rememberme"><?php echo $this->_tpl_vars['lang']['rememberme']; ?>
</label>
	</p>

	<p class="field">
		<input type="submit" tabindex="6" name="login" value="<?php echo $this->_tpl_vars['lang']['login']; ?>
" class="button" />
		<a href="<?php echo @ESYN_URL; ?>
forgot.php"><?php echo $this->_tpl_vars['lang']['forgot']; ?>
</a>
	</p>
</form>

<p><?php echo $this->_tpl_vars['lang']['register_account']; ?>
 <a href="<?php echo @ESYN_URL; ?>
register.php" rel="nofollow"><?php echo $this->_tpl_vars['lang']['register']; ?>
</a></p>

<?php echo smarty_function_esynHooker(array('name' => 'loginBeforeFooter'), $this);?>


<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>