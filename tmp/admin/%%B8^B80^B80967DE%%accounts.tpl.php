<?php /* Smarty version 2.6.26, created on 2011-12-13 04:29:03
         compiled from /home/vbezruchkin/www/v1700/admin/templates/default/accounts.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'preventCsrf', '/home/vbezruchkin/www/v1700/admin/templates/default/accounts.tpl', 6, false),array('function', 'include_file', '/home/vbezruchkin/www/v1700/admin/templates/default/accounts.tpl', 61, false),array('function', 'esynHooker', '/home/vbezruchkin/www/v1700/admin/templates/default/accounts.tpl', 63, false),array('modifier', 'escape', '/home/vbezruchkin/www/v1700/admin/templates/default/accounts.tpl', 10, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "header.tpl", 'smarty_include_vars' => array('css' => "js/ext/plugins/panelresizer/css/PanelResizer")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php if (isset ( $_GET['do'] ) && ( $_GET['do'] == 'add' || $_GET['do'] == 'edit' )): ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-header.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['gTitle'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<form action="controller.php?file=accounts&amp;do=<?php echo $_GET['do']; ?>
<?php if ($_GET['do'] == 'edit'): ?>&amp;id=<?php echo $_GET['id']; ?>
<?php endif; ?>" method="post">
	<?php echo esynUtil::preventCsrf(array(), $this);?>

	<table cellspacing="0" width="100%" class="striped">
	<tr>
		<td width="200"><strong><?php echo $this->_tpl_vars['esynI18N']['username']; ?>
:</strong></td>
		<td><input type="text" name="username" size="26" class="common" value="<?php if (isset ( $this->_tpl_vars['account']['username'] )): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['account']['username'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php elseif (isset ( $_POST['username'] )): ?><?php echo ((is_array($_tmp=$_POST['username'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php endif; ?>" /></td>
	</tr>
	<tr>
		<td><strong><?php echo $this->_tpl_vars['esynI18N']['password']; ?>
:</strong></td>
		<td><input type="password" name="password" size="26" class="common" value="<?php if (isset ( $_POST['password'] )): ?><?php echo ((is_array($_tmp=$_POST['password'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php endif; ?>"/></td>
	</tr>
	<tr>
		<td><strong><?php echo $this->_tpl_vars['esynI18N']['password_confirm']; ?>
:</strong></td>
		<td><input type="password" name="password2" size="26" class="common" value="<?php if (isset ( $_POST['password2'] )): ?><?php echo ((is_array($_tmp=$_POST['password2'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php endif; ?>" /></td>
	</tr>
	<tr>
		<td><strong><?php echo $this->_tpl_vars['esynI18N']['email']; ?>
:</strong></td>
		<td><input type="text" name="email" size="26" class="common" value="<?php if (isset ( $this->_tpl_vars['account']['email'] )): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['account']['email'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php elseif (isset ( $_POST['email'] )): ?><?php echo ((is_array($_tmp=$_POST['email'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php endif; ?>" /></td>
	</tr>
	<tr>
		<td><strong><?php echo $this->_tpl_vars['esynI18N']['status']; ?>
:</strong></td>
		<td>
			<select name="status">
				<option value="active" <?php if (isset ( $this->_tpl_vars['account']['status'] ) && $this->_tpl_vars['account']['status'] == 'active'): ?>selected="selected"<?php elseif (isset ( $_POST['status'] ) && $_POST['status'] == 'active'): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['esynI18N']['active']; ?>
</option>
				<option value="approval" <?php if (isset ( $this->_tpl_vars['account']['status'] ) && $this->_tpl_vars['account']['status'] == 'approval'): ?>selected="selected"<?php elseif (isset ( $_POST['status'] ) && $_POST['status'] == 'approval'): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['esynI18N']['approval']; ?>
</option>
				<option value="banned" <?php if (isset ( $this->_tpl_vars['account']['status'] ) && $this->_tpl_vars['account']['status'] == 'banned'): ?>selected="selected"<?php elseif (isset ( $_POST['status'] ) && $_POST['status'] == 'banned'): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['esynI18N']['banned']; ?>
</option>
			</select>
		</td>
	</tr>
	</table>

	<table>
	<tr class="all">
		<td style="padding: 0 0 0 11px; width: 0;">
			<input type="submit" name="save" class="common" value="<?php if ($_GET['do'] == 'add'): ?><?php echo $this->_tpl_vars['esynI18N']['add']; ?>
<?php else: ?><?php echo $this->_tpl_vars['esynI18N']['save_changes']; ?>
<?php endif; ?>" />
		</td>
		<td style="padding: 0;">
		<?php if (isset ( $_GET['do'] ) && $_GET['do'] == 'add'): ?>
			<span><strong>&nbsp;<?php echo $this->_tpl_vars['esynI18N']['and_then']; ?>
&nbsp;</strong></span>
			<select name="goto">
				<option value="list" <?php if (isset ( $_POST['goto'] ) && $_POST['goto'] == 'list'): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['esynI18N']['go_to_list']; ?>
</option>
				<option value="add" <?php if (isset ( $_POST['goto'] ) && $_POST['goto'] == 'add'): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['esynI18N']['add_another_one']; ?>
</option>
			</select>
		<?php endif; ?>
		</td>
	</tr>
	</table>
	<input type="hidden" name="do" value="<?php if (isset ( $_GET['do'] )): ?><?php echo $_GET['do']; ?>
<?php endif; ?>" />
	<input type="hidden" name="old_name" value="<?php if (isset ( $this->_tpl_vars['account']['username'] )): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['account']['username'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php endif; ?>" />
	<input type="hidden" name="id" value="<?php if (isset ( $_GET['id'] )): ?><?php echo $_GET['id']; ?>
<?php endif; ?>" />
	</form>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php else: ?>
	<div id="box_accounts" style="margin-top: 15px;"></div>
<?php endif; ?>

<?php echo smarty_function_include_file(array('js' => "js/intelli/intelli.grid, js/intelli/intelli.gmodel, js/ext/plugins/bettercombobox/betterComboBox, js/ext/plugins/panelresizer/PanelResizer, js/ext/plugins/progressbarpager/ProgressBarPager, js/admin/accounts"), $this);?>


<?php echo smarty_function_esynHooker(array('name' => 'smartyAdminAccountsAfterJSInclude'), $this);?>


<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>