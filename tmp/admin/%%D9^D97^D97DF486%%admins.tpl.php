<?php /* Smarty version 2.6.26, created on 2011-12-13 04:28:25
         compiled from /home/vbezruchkin/www/v1700/admin/templates/default/admins.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'preventCsrf', '/home/vbezruchkin/www/v1700/admin/templates/default/admins.tpl', 6, false),array('function', 'html_radio_switcher', '/home/vbezruchkin/www/v1700/admin/templates/default/admins.tpl', 45, false),array('function', 'include_file', '/home/vbezruchkin/www/v1700/admin/templates/default/admins.tpl', 111, false),array('modifier', 'escape', '/home/vbezruchkin/www/v1700/admin/templates/default/admins.tpl', 10, false),array('modifier', 'default', '/home/vbezruchkin/www/v1700/admin/templates/default/admins.tpl', 45, false),)), $this); ?>
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
	<form action="controller.php?file=admins<?php if ($_GET['do'] == 'add'): ?>&amp;do=add<?php else: ?>&amp;do=edit&amp;id=<?php echo $_GET['id']; ?>
<?php endif; ?>" method="post">
	<?php echo esynUtil::preventCsrf(array(), $this);?>

	<table cellspacing="0" cellpadding="0" width="100%" class="striped">
	<tr>
		<td width="200"><strong><?php echo $this->_tpl_vars['esynI18N']['username']; ?>
:</strong></td>
		<td><input type="text" name="username" class="common" size="22" value="<?php if (isset ( $this->_tpl_vars['admin']['username'] )): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['admin']['username'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php elseif (isset ( $_POST['username'] )): ?><?php echo ((is_array($_tmp=$_POST['username'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php endif; ?>" /></td>
	</tr>
	
	<tr>
		<td><strong><?php echo $this->_tpl_vars['esynI18N']['fullname']; ?>
:</strong></td>
		<td><input type="text" name="fullname" class="common" size="22" value="<?php if (isset ( $this->_tpl_vars['admin']['fullname'] )): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['admin']['fullname'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php elseif (isset ( $_POST['fullname'] )): ?><?php echo ((is_array($_tmp=$_POST['fullname'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php endif; ?>" /></td>
	</tr>
	
	<tr>
		<td><strong><?php echo $this->_tpl_vars['esynI18N']['email']; ?>
:</strong></td>
		<td><input type="text" name="email" class="common" size="22" value="<?php if (isset ( $this->_tpl_vars['admin']['email'] )): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['admin']['email'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php elseif (isset ( $_POST['email'] )): ?><?php echo ((is_array($_tmp=$_POST['email'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php endif; ?>" /></td>
	</tr>
		
	<tr>
		<td><strong><?php echo $this->_tpl_vars['esynI18N']['password']; ?>
:</strong></td>
		<td><input type="password" name="new_pass" class="common" size="22" /></td>
	</tr>

	<tr>
		<td><strong><?php echo $this->_tpl_vars['esynI18N']['password_confirm']; ?>
:</strong></td>
		<td><input type="password" name="new_pass2" class="common" size="22" /></td>
	</tr>

	<tr>
		<td><strong><?php echo $this->_tpl_vars['esynI18N']['status']; ?>
:</strong></td>
		<td>
			<select name="status">
				<option value="active" <?php if (isset ( $this->_tpl_vars['admin']['status'] ) && $this->_tpl_vars['admin']['status'] == 'active'): ?>selected="selected"<?php elseif (isset ( $_POST['status'] ) && $_POST['status'] == 'active'): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['esynI18N']['active']; ?>
</option>
				<option value="inactive" <?php if (isset ( $this->_tpl_vars['admin']['status'] ) && $this->_tpl_vars['admin']['status'] == 'inactive'): ?>selected="selected"<?php elseif (isset ( $_POST['status'] ) && $_POST['status'] == 'inactive'): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['esynI18N']['inactive']; ?>
</option>
			</select>
		</td>
	</tr>

	<tr>
		<td><strong><?php echo $this->_tpl_vars['esynI18N']['submission_notif']; ?>
:</strong></td>
		<td><?php echo smarty_function_html_radio_switcher(array('value' => ((is_array($_tmp=@$this->_tpl_vars['admin']['submit_notif'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)),'name' => 'submit_notif'), $this);?>
</td>
	</tr>
	
<?php if ($this->_tpl_vars['config']['sponsored_listings']): ?>
	<tr>
		<td><strong><?php echo $this->_tpl_vars['esynI18N']['payment_notif']; ?>
:</strong></td>
		<td><?php echo smarty_function_html_radio_switcher(array('value' => ((is_array($_tmp=@$this->_tpl_vars['admin']['payment_notif'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)),'name' => 'payment_notif'), $this);?>
</td>
	</tr>
<?php endif; ?>

	<tr>
		<td class="caption" colspan="2"><strong><?php echo $this->_tpl_vars['esynI18N']['admin_permissions']; ?>
</strong></td>
	</tr>

	<tr>
		<td><strong><?php echo $this->_tpl_vars['esynI18N']['super_admin']; ?>
:&nbsp;</strong></td>
		<td>
			<input type="radio" name="super" value="1" id="type1" <?php if (isset ( $this->_tpl_vars['admin']['super'] ) && $this->_tpl_vars['admin']['super'] == '1'): ?>checked="checked"<?php elseif (isset ( $_POST['super'] ) && $_POST['super'] == '1'): ?>checked="checked"<?php endif; ?> /><label for="type1">&nbsp;<?php echo $this->_tpl_vars['esynI18N']['enabled']; ?>
</label>
			<input type="radio" name="super" value="0" id="type0" <?php if (isset ( $this->_tpl_vars['admin']['super'] ) && $this->_tpl_vars['admin']['super'] == '0'): ?>checked="checked"<?php elseif (isset ( $_POST['super'] ) && $_POST['super'] == '0'): ?>checked="checked"<?php elseif (! $_POST && ! isset ( $this->_tpl_vars['admin'] )): ?>checked="checked"<?php endif; ?> /><label for="type0">&nbsp;<?php echo $this->_tpl_vars['esynI18N']['disabled']; ?>
</label>
		</td>
	</tr>
	</table>

	<div id="permissions" style="display: none;">
		<table cellspacing="0" width="100%" class="striped">
		<tr>
			<td>
				<ul style="list-style-type: none;">
					<li class="caption" style="padding-bottom: 3px;">
						<input type="checkbox" value="1" name="select_all_permis" id="select_all_permis" <?php if (isset ( $_POST['select_all_permis'] ) && $_POST['select_all_permis'] == '1'): ?>checked="checked"<?php endif; ?> /><label for="select_all_permis">&nbsp;<?php echo $this->_tpl_vars['esynI18N']['select_all']; ?>
</label>
					</li>
					<?php $_from = $this->_tpl_vars['esynAcos']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['aco']):
?>
						<li style="margin: 0 0 0 15px; padding-bottom: 3px; float: left; width: 150px;" >
							<input type="checkbox" name="permissions[]" value="<?php echo $this->_tpl_vars['key']; ?>
" id="ts<?php echo $this->_tpl_vars['key']; ?>
" <?php if (( isset ( $this->_tpl_vars['admin']['permissions'] ) && in_array ( $this->_tpl_vars['key'] , $this->_tpl_vars['admin']['permissions'] ) ) || ( isset ( $_POST['permissions'] ) && in_array ( $this->_tpl_vars['key'] , $_POST['permissions'] ) )): ?>checked="checked"<?php endif; ?> /><label for="ts<?php echo $this->_tpl_vars['key']; ?>
">&nbsp;<?php echo $this->_tpl_vars['aco']; ?>
</label>
						</li>
					<?php endforeach; endif; unset($_from); ?>
				</ul>
			</td>
		</tr>
		</table>
	</div>
	
	<table cellspacing="0" width="100%" class="striped">
	<tr>
		<td style="padding: 0 0 0 11px; width: 0;">
			<input type="submit" name="save" class="common" value="<?php if (isset ( $_GET['do'] ) && $_GET['do'] == 'edit'): ?><?php echo $this->_tpl_vars['esynI18N']['save_changes']; ?>
<?php else: ?><?php echo $this->_tpl_vars['esynI18N']['add']; ?>
<?php endif; ?>" />
		</td>
		<td style="padding: 0;">
			<?php if ($_GET['do'] == 'add'): ?>
				<strong>&nbsp;<?php echo $this->_tpl_vars['esynI18N']['and_then']; ?>
&nbsp;</strong>
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
	<input type="hidden" name="id" value="<?php if (isset ( $this->_tpl_vars['admin']['id'] )): ?><?php echo $this->_tpl_vars['admin']['id']; ?>
<?php endif; ?>" />
	<input type="hidden" name="do" value="<?php if (isset ( $_GET['do'] )): ?><?php echo $_GET['do']; ?>
<?php endif; ?>" />
	</form>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php else: ?>
	<div id="box_admins" style="margin-top: 15px;"></div>
<?php endif; ?>

<?php echo smarty_function_include_file(array('js' => "js/jquery/plugins/iphoneswitch/jquery.iphone-switch, js/intelli/intelli.grid, js/intelli/intelli.gmodel, js/ext/plugins/bettercombobox/betterComboBox, js/ext/plugins/panelresizer/PanelResizer, js/ext/plugins/progressbarpager/ProgressBarPager, js/admin/admins"), $this);?>


<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>