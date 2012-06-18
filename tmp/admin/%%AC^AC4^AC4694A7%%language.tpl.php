<?php /* Smarty version 2.6.26, created on 2011-12-13 04:29:18
         compiled from /home/vbezruchkin/www/v1700/admin/templates/default/language.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'preventCsrf', '/home/vbezruchkin/www/v1700/admin/templates/default/language.tpl', 4, false),array('function', 'include_file', '/home/vbezruchkin/www/v1700/admin/templates/default/language.tpl', 150, false),array('modifier', 'replace', '/home/vbezruchkin/www/v1700/admin/templates/default/language.tpl', 23, false),array('modifier', 'count', '/home/vbezruchkin/www/v1700/admin/templates/default/language.tpl', 25, false),array('modifier', 'escape', '/home/vbezruchkin/www/v1700/admin/templates/default/language.tpl', 74, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "header.tpl", 'smarty_include_vars' => array('css' => "js/ext/plugins/panelresizer/css/PanelResizer")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div id="box_add_phrase" style="margin-top: 15px;">
	<?php echo esynUtil::preventCsrf(array(), $this);?>

</div>

<?php if ($_GET['view'] == 'language'): ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-header.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['gTitle'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	
	<form action="controller.php?file=language&amp;view=language" method="post">
	<?php echo esynUtil::preventCsrf(array(), $this);?>

	<table cellspacing="0" cellpadding="10" width="100%" class="common">
	<tr>
		<th class="first"><?php echo $this->_tpl_vars['esynI18N']['language']; ?>
</th>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
		<th><?php echo $this->_tpl_vars['esynI18N']['default']; ?>
</th>
	</tr>
		
	<?php $_from = $this->_tpl_vars['langs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['code'] => $this->_tpl_vars['language']):
?>
	<tr>
		<td class="first"><?php echo $this->_tpl_vars['language']; ?>
</td>
		<td><a href="controller.php?file=language&amp;view=phrase&amp;language=<?php echo $this->_tpl_vars['code']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['esynI18N']['edit_translate'])) ? $this->_run_mod_handler('replace', true, $_tmp, 'language', $this->_tpl_vars['language']) : smarty_modifier_replace($_tmp, 'language', $this->_tpl_vars['language'])); ?>
</a></td>
		<td>
		<?php if (((is_array($_tmp=$this->_tpl_vars['langs'])) ? $this->_run_mod_handler('count', true, $_tmp) : count($_tmp)) != 1 && $this->_tpl_vars['code'] != $this->_tpl_vars['config']['lang']): ?>
			<a class="delete_language" href="controller.php?file=language&amp;view=language&amp;do=delete&amp;language=<?php echo $this->_tpl_vars['code']; ?>
"><?php echo $this->_tpl_vars['esynI18N']['delete']; ?>
</a>&nbsp;|&nbsp;
		<?php endif; ?>
		<a href="controller.php?file=language&amp;view=language&amp;do=download&amp;language=<?php echo $this->_tpl_vars['code']; ?>
"><?php echo $this->_tpl_vars['esynI18N']['download']; ?>
</a></td>
		<td width="100">
			<?php if ($this->_tpl_vars['code'] != $this->_tpl_vars['config']['lang']): ?>
				<a href="controller.php?file=language&amp;view=language&amp;do=default&amp;language=<?php echo $this->_tpl_vars['code']; ?>
"><?php echo $this->_tpl_vars['esynI18N']['set_default']; ?>
</a>
			<?php else: ?>
				&nbsp;
			<?php endif; ?>
		</td>
	</tr>
	<?php endforeach; endif; unset($_from); ?>
	</table>
	</form>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php elseif ($_GET['view'] == 'phrase'): ?>
	<div id="box_phrases" style="margin-top: 15px;"></div>
<?php elseif ($_GET['view'] == 'download'): ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-header.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['gTitle'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	
	<form action="controller.php?file=language&amp;view=download" method="post">
	<?php echo esynUtil::preventCsrf(array(), $this);?>

	<input type="hidden" name="do" value="download" />
	<table cellspacing="0" cellpadding="10" width="100%" class="common">
	<tr>
		<th colspan="2" class="first caption"><?php echo $this->_tpl_vars['esynI18N']['download']; ?>
</td>
	</tr>
	<tr>
		<td class="first" width="200"><?php echo $this->_tpl_vars['esynI18N']['language']; ?>
</td>
		<td>
			<select name="lang" <?php if (((is_array($_tmp=$this->_tpl_vars['langs'])) ? $this->_run_mod_handler('count', true, $_tmp) : count($_tmp)) == 1): ?>disabled="disabled"<?php endif; ?>>
				<?php $_from = $this->_tpl_vars['langs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['code'] => $this->_tpl_vars['lang']):
?>
					<option value="<?php echo $this->_tpl_vars['code']; ?>
"><?php echo $this->_tpl_vars['lang']; ?>
</option>
				<?php endforeach; endif; unset($_from); ?>
			</select>
		</td>
	</tr>
	<tr>
		<td class="first"><?php echo $this->_tpl_vars['esynI18N']['file_format']; ?>
</td>
		<td>
			<select name="file_format">
				<option value="csv" <?php if (isset ( $_POST['file_format'] ) && $_POST['file_format'] == 'csv'): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['esynI18N']['csv_format']; ?>
</option>
				<option value="sql" <?php if (isset ( $_POST['file_format'] ) && $_POST['file_format'] == 'sql'): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['esynI18N']['sql_format']; ?>
</option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="first"><?php echo $this->_tpl_vars['esynI18N']['filename']; ?>
</td>
		<td><input type="text" size="40" name="filename" class="common" value="<?php if (isset ( $_POST['filename'] ) && ! empty ( $_POST['filename'] )): ?><?php echo ((is_array($_tmp=$_POST['filename'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php else: ?>esc_language<?php endif; ?>" /></td>
	</tr>
	<tr>
		<td colspan="2" align="center" class="first">
			<input type="submit" class="common" value="<?php echo $this->_tpl_vars['esynI18N']['download']; ?>
" />
		</td>
	</tr>
	</table>
	</form>
	
	<form action="controller.php?file=language&amp;view=download" method="post" enctype="multipart/form-data">
	<?php echo esynUtil::preventCsrf(array(), $this);?>

	<input type="hidden" name="do" value="import" />
	<table cellspacing="0" cellpadding="10" width="100%" class="common">
	<tr>
		<th colspan="2" class="first caption"><?php echo $this->_tpl_vars['esynI18N']['import']; ?>
</td>
	</tr>
	<tr>
		<td class="first"><?php echo $this->_tpl_vars['esynI18N']['file_format']; ?>
</td>
		<td>
			<select name="file_format">
				<option value="csv" <?php if (isset ( $_POST['file_format'] ) && $_POST['file_format'] == 'csv'): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['esynI18N']['csv_format']; ?>
</option>
				<option value="sql" <?php if (isset ( $_POST['file_format'] ) && $_POST['file_format'] == 'sql'): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['esynI18N']['sql_format']; ?>
</option>
			</select>
		</td>
	</tr>
	<tr>
		<td class="first" width="200"><?php echo $this->_tpl_vars['esynI18N']['import_from_pc']; ?>
</td>
		<td><input type="file" name="language_file" size="40" /></td>
	</tr>
	<tr>
		<td class="first"><?php echo $this->_tpl_vars['esynI18N']['import_from_server']; ?>
</td>
		<td><input type="text" size="40" name="language_file2" class="common" value="../updates/" /></td>
	</tr>
	<tr>
		<td colspan="2" align="center" class="first">
			<input type="submit" class="common" value="<?php echo $this->_tpl_vars['esynI18N']['import']; ?>
" />
		</td>
	</tr>
	</table>
	</form>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php elseif ($_GET['view'] == 'add_lang'): ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-header.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['esynI18N']['copy_language'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	
	<form action="controller.php?file=language&amp;view=add_lang" method="post">
	<?php echo esynUtil::preventCsrf(array(), $this);?>

	<input type="hidden" name="do" value="add_lang" />
	<table cellspacing="0" cellpadding="0" width="100%" class="striped">
	<tr>
		<td width="250"><?php echo ((is_array($_tmp=$this->_tpl_vars['esynI18N']['copy_default_language_to'])) ? $this->_run_mod_handler('replace', true, $_tmp, "[lang]", $this->_tpl_vars['langs'][$this->_tpl_vars['config']['lang']]) : smarty_modifier_replace($_tmp, "[lang]", $this->_tpl_vars['langs'][$this->_tpl_vars['config']['lang']])); ?>
</td>
		<td>
			<label for="new_code"><?php echo $this->_tpl_vars['esynI18N']['iso_code']; ?>
</label>
			<input id="new_code" size="2" maxlength="2" type="text" name="new_code" class="common" value="<?php if (isset ( $_POST['new_code'] )): ?><?php echo ((is_array($_tmp=$_POST['new_code'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php endif; ?>" />
			<label for="new_lang"><?php echo $this->_tpl_vars['esynI18N']['title']; ?>
</label>
			<input id="new_lang" size="10" maxlength="40" type="text" name="new_lang" class="common" value="<?php if (isset ( $_POST['new_lang'] )): ?><?php echo ((is_array($_tmp=$_POST['new_lang'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php endif; ?>" />
			<input type="submit" class="common" value="<?php echo $this->_tpl_vars['esynI18N']['copy_language']; ?>
" />
		</td>
	</tr>
	
	<tr>
		<td width="200"><?php echo $this->_tpl_vars['esynI18N']['all_languages']; ?>
</td>
		<td>
		<?php $_from = $this->_tpl_vars['langs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['code'] => $this->_tpl_vars['language']):
?>
			<b><?php echo $this->_tpl_vars['language']; ?>
</b>&nbsp;<?php if ($this->_tpl_vars['code'] == $this->_tpl_vars['config']['lang']): ?>[ <?php echo $this->_tpl_vars['esynI18N']['default']; ?>
 ]<?php endif; ?><br />
		<?php endforeach; endif; unset($_from); ?>
		</td>
	</tr>

	</table>
	</form>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php elseif ($_GET['view'] == 'compare'): ?>
	<div id="box_compare" style="margin-top: 15px;"></div>
<?php endif; ?>

<?php echo smarty_function_include_file(array('js' => "js/intelli/intelli.grid, js/intelli/intelli.gmodel, js/ext/plugins/bettercombobox/betterComboBox, js/ext/plugins/panelresizer/PanelResizer, js/ext/plugins/progressbarpager/ProgressBarPager, js/admin/language"), $this);?>


<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>