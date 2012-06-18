<?php /* Smarty version 2.6.26, created on 2011-12-13 04:44:31
         compiled from /home/vbezruchkin/www/v1700/templates/common/register.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'esynHooker', '/home/vbezruchkin/www/v1700/templates/common/register.tpl', 7, false),array('function', 'include_file', '/home/vbezruchkin/www/v1700/templates/common/register.tpl', 42, false),array('modifier', 'escape', '/home/vbezruchkin/www/v1700/templates/common/register.tpl', 12, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<h1><?php echo $this->_tpl_vars['lang']['register']; ?>
</h1>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "notification.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php echo smarty_function_esynHooker(array('name' => 'tplFrontRegisterBeforeRegister'), $this);?>


<form method="post" action="<?php echo @ESYN_URL; ?>
register.php">
	<p class="field">
		<strong><?php echo $this->_tpl_vars['lang']['your_username']; ?>
:</strong><br />
		<input type="text" class="text" name="username" size="25" id="username" value="<?php if (isset ( $this->_tpl_vars['account']['username'] )): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['account']['username'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php elseif (isset ( $_POST['username'] )): ?><?php echo ((is_array($_tmp=$_POST['username'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php endif; ?>" />
	</p>
	
	<p class="field">
		<strong><?php echo $this->_tpl_vars['lang']['your_email']; ?>
:</strong><br />
		<input type="text" class="text" name="email" size="25" id="email" value="<?php if (isset ( $this->_tpl_vars['account']['email'] )): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['account']['email'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php elseif (isset ( $_POST['email'] )): ?><?php echo ((is_array($_tmp=$_POST['email'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php endif; ?>" />
	</p>

	<p class="field">
		<input type="checkbox" id="auto_generate" name="auto_generate" value="1" <?php if (isset ( $_POST['auto_generate'] ) && $_POST['auto_generate'] == '1'): ?>checked="checked"<?php elseif (! isset ( $this->_tpl_vars['account'] ) && ! $_POST): ?>checked="checked"<?php endif; ?> /><label for="auto_generate"><?php echo $this->_tpl_vars['lang']['auto_generate_password']; ?>
</label>
	</p>

	<div id="passwords" style="display: none;">
		<p class="field">
			<strong><?php echo $this->_tpl_vars['lang']['your_password']; ?>
:</strong><br />
			<input type="password" name="password" class="text" size="25" id="pass1" value="<?php if (isset ( $this->_tpl_vars['account']['password'] )): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['account']['password'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php elseif (isset ( $_POST['password'] )): ?><?php echo ((is_array($_tmp=$_POST['password'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php endif; ?>" />
		</p>
		<p class="field">
			<strong><?php echo $this->_tpl_vars['lang']['your_password_confirm']; ?>
:</strong><br />
			<input type="password" name="password2" class="text" size="25" id="pass2" value="<?php if (isset ( $this->_tpl_vars['account']['password2'] )): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['account']['password2'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php elseif (isset ( $_POST['password2'] )): ?><?php echo ((is_array($_tmp=$_POST['password2'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php endif; ?>" />
		</p>
	</div>

	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "captcha.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	
	<p class="field"><input type="submit" name="register" value="<?php echo $this->_tpl_vars['lang']['register']; ?>
" class="button" /></p>
</form>

<?php echo smarty_function_esynHooker(array('name' => 'registerBeforeIncludeJs'), $this);?>


<?php echo smarty_function_include_file(array('js' => "js/frontend/register"), $this);?>


<?php echo smarty_function_esynHooker(array('name' => 'registerBeforeFooter'), $this);?>


<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>