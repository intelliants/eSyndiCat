<?php /* Smarty version 2.6.26, created on 2011-12-13 04:29:48
         compiled from /home/vbezruchkin/www/v1700/plugins/comments/admin/templates/index.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'preventCsrf', '/home/vbezruchkin/www/v1700/plugins/comments/admin/templates/index.tpl', 8, false),array('function', 'include_file', '/home/vbezruchkin/www/v1700/plugins/comments/admin/templates/index.tpl', 50, false),array('modifier', 'escape', '/home/vbezruchkin/www/v1700/plugins/comments/admin/templates/index.tpl', 12, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "header.tpl", 'smarty_include_vars' => array('css' => "js/ext/plugins/panelresizer/css/PanelResizer")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php if (isset ( $_GET['do'] ) && $_GET['do'] == 'edit'): ?>
	
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-header.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['gTitle'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	
	<form action="controller.php?plugin=comments&amp;do=<?php echo $_GET['do']; ?>
<?php if ($_GET['do'] == 'edit'): ?>&amp;id=<?php echo $_GET['id']; ?>
<?php endif; ?>" method="post">
	<?php echo esynUtil::preventCsrf(array(), $this);?>

	<table cellspacing="0" cellpadding="0" width="100%" class="striped">
	<tr>
		<td width="200"><strong><?php echo $this->_tpl_vars['esynI18N']['author']; ?>
:</strong></td>
		<td><input type="text" size="40" name="author" class="common" value="<?php if (isset ( $this->_tpl_vars['comment']['author'] )): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['comment']['author'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php endif; ?>" /></td>
	</tr>
	<tr>
		<td><strong><?php echo $this->_tpl_vars['esynI18N']['email']; ?>
:</strong></td>
		<td><input type="text" size="40" name="email" class="common" value="<?php if (isset ( $this->_tpl_vars['comment']['email'] )): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['comment']['email'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php endif; ?>" /></td>
	</tr>
	
	<tr>
		<td><strong><?php echo $this->_tpl_vars['esynI18N']['url']; ?>
:</strong></td>
		<td><input type="text" size="40" name="url" class="common" value="<?php if (isset ( $this->_tpl_vars['comment']['url'] )): ?><?php echo $this->_tpl_vars['comment']['url']; ?>
<?php endif; ?>" /></td>
	</tr>
	
	<tr>
		<td><strong><?php echo $this->_tpl_vars['esynI18N']['body']; ?>
:</strong></td>
		<td><textarea name="body" cols="53" rows="8" class="common" id="commentbody"><?php if (isset ( $this->_tpl_vars['comment']['body'] )): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['comment']['body'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php endif; ?></textarea></td>
	</tr>
	<tr>
		<td><strong><?php echo $this->_tpl_vars['esynI18N']['status']; ?>
:</strong></td>
		<td>
			<select name="status">
				<option value="inactive" <?php if (isset ( $this->_tpl_vars['comment']['status'] ) && $this->_tpl_vars['comment']['status'] == 'inactive'): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['esynI18N']['inactive']; ?>
</option>
				<option value="active" <?php if (isset ( $this->_tpl_vars['comment']['status'] ) && $this->_tpl_vars['comment']['status'] == 'active'): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['esynI18N']['active']; ?>
</option>
			</select>
		</td>
	</tr>
	<tr class="all">
		<td colspan="2">
			<input type="submit" name="edit_comments" value="<?php echo $this->_tpl_vars['esynI18N']['save_changes']; ?>
" class="common" />
			<input type="hidden" name="id" value="<?php echo $this->_tpl_vars['comment']['id']; ?>
" />
		</td>
	</tr>
	</table>
	</form>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-footer.tpl", 'smarty_include_vars' => array('class' => 'box')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php else: ?>
	<div id="box_comments" style="margin-top: 15px;"></div>
<?php endif; ?>

<?php echo smarty_function_include_file(array('js' => "js/intelli/intelli.grid, js/intelli/intelli.gmodel, js/ckeditor/ckeditor, js/ext/plugins/bettercombobox/betterComboBox, js/ext/plugins/panelresizer/PanelResizer, js/ext/plugins/progressbarpager/ProgressBarPager, plugins/comments/js/admin/comments"), $this);?>


<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>