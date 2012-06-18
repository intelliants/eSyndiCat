<?php /* Smarty version 2.6.26, created on 2011-12-13 10:57:10
         compiled from /home/vbezruchkin/www/v1700/admin/templates/default/blocks.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'preventCsrf', '/home/vbezruchkin/www/v1700/admin/templates/default/blocks.tpl', 6, false),array('function', 'include_file', '/home/vbezruchkin/www/v1700/admin/templates/default/blocks.tpl', 158, false),array('modifier', 'cat', '/home/vbezruchkin/www/v1700/admin/templates/default/blocks.tpl', 89, false),array('modifier', 'escape', '/home/vbezruchkin/www/v1700/admin/templates/default/blocks.tpl', 119, false),)), $this); ?>
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
	<form action="controller.php?file=blocks&amp;do=<?php echo $_GET['do']; ?>
<?php if ($_GET['do'] == 'edit'): ?>&amp;id=<?php echo $_GET['id']; ?>
<?php endif; ?>" method="post">
	<?php echo esynUtil::preventCsrf(array(), $this);?>

	<table class="striped" cellspacing="0" width="100%">
	<tr>
		<td class="caption" colspan="2"><strong><?php echo $this->_tpl_vars['esynI18N']['block_options']; ?>
</strong></td>
	</tr>
	<tr>
		<td width="120"><strong><?php echo $this->_tpl_vars['esynI18N']['type']; ?>
:</strong></td>
		<td>
			<select name="type" id="block_type">
				<?php $_from = $this->_tpl_vars['types']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['type']):
?>
					<option value="<?php echo $this->_tpl_vars['type']; ?>
" <?php if (isset ( $this->_tpl_vars['block']['type'] ) && $this->_tpl_vars['block']['type'] == $this->_tpl_vars['type']): ?>selected="selected"<?php elseif (isset ( $_POST['type'] ) && $_POST['type'] == $this->_tpl_vars['type']): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['type']; ?>
</option>
				<?php endforeach; endif; unset($_from); ?>
			</select>
			<br />
			<div class="option_tip" id="type_tip_plain" style="display: none;"><i><?php echo $this->_tpl_vars['esynI18N']['block_type_tip_plain']; ?>
</i></div>
			<div class="option_tip" id="type_tip_html" style="display: none;"><i><?php echo $this->_tpl_vars['esynI18N']['block_type_tip_html']; ?>
</i></div>
			<div class="option_tip" id="type_tip_smarty" style="display: none;"><i><?php echo $this->_tpl_vars['esynI18N']['block_type_tip_smarty']; ?>
</i></div>
			<div class="option_tip" id="type_tip_php" style="display: none;"><i><?php echo $this->_tpl_vars['esynI18N']['block_type_tip_php']; ?>
</i></div>
		</td>
	</tr>
	<tr>
		<td><strong><?php echo $this->_tpl_vars['esynI18N']['position']; ?>
:</strong></td>
		<td>
			<select name="position">
				<?php $_from = $this->_tpl_vars['positions']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['position']):
?>
					<option value="<?php echo $this->_tpl_vars['position']; ?>
" <?php if (isset ( $this->_tpl_vars['block']['position'] ) && $this->_tpl_vars['block']['position'] == $this->_tpl_vars['position']): ?>selected="selected"<?php elseif (isset ( $_POST['position'] ) && $_POST['position'] == $this->_tpl_vars['position']): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['position']; ?>
</option>
				<?php endforeach; endif; unset($_from); ?>
			</select>
		</td>
	</tr>
	<tr>
		<td><strong><?php echo $this->_tpl_vars['esynI18N']['show_header']; ?>
:</strong></td>
		<td>
			<input type="checkbox" name="show_header" value="1" <?php if (isset ( $this->_tpl_vars['block']['show_header'] ) && $this->_tpl_vars['block']['show_header'] == '1'): ?>checked="checked"<?php elseif (isset ( $_POST['show_header'] ) && $_POST['show_header'] == '1'): ?>checked="checked"<?php elseif (! isset ( $this->_tpl_vars['block'] ) && ! $_POST): ?>checked="checked"<?php endif; ?> />
		</td>
	</tr>
	<tr>
		<td><strong><?php echo $this->_tpl_vars['esynI18N']['collapsible']; ?>
:</strong></td>
		<td>
			<input type="checkbox" name="collapsible" value="1" disabled="disabled" <?php if (isset ( $this->_tpl_vars['block']['collapsible'] ) && $this->_tpl_vars['block']['collapsible'] == '1'): ?>checked="checked"<?php elseif (isset ( $_POST['collapsible'] ) && $_POST['collapsible'] == '1'): ?>checked="checked"<?php endif; ?> />
		</td>
	</tr>
	<tr>
		<td><strong><?php echo $this->_tpl_vars['esynI18N']['collapsed']; ?>
:</strong></td>
		<td>
			<input type="checkbox" name="collapsed" value="1" disabled="disabled" <?php if (isset ( $this->_tpl_vars['block']['collapsed'] ) && $this->_tpl_vars['block']['collapsed'] == '1'): ?>checked="checked"<?php elseif (isset ( $_POST['collapsed'] ) && $_POST['collapsed'] == '1'): ?>checked="checked"<?php endif; ?> />
		</td>
	</tr>
	<tr>
		<td><strong><?php echo $this->_tpl_vars['esynI18N']['multi_language']; ?>
:</strong></td>
		<td>
			<input type="checkbox" id="multi_language" name="multi_language" value="1" <?php if (isset ( $this->_tpl_vars['block']['multi_language'] ) && $this->_tpl_vars['block']['multi_language'] == '1'): ?>checked="checked"<?php elseif (isset ( $_POST['multi_language'] ) && $_POST['multi_language'] == '1'): ?>checked="checked"<?php elseif (! isset ( $this->_tpl_vars['block'] ) && ! $_POST): ?>checked="checked"<?php endif; ?> />
		</td>
	</tr>
	<tr id="languages" style="display: none;">
		<td><strong><?php echo $this->_tpl_vars['esynI18N']['language']; ?>
:</strong></td>
		<td>
			<label><input type="checkbox" id="select_all_languages" name="select_all_languages" value="1" <?php if (isset ( $_POST['select_all'] ) && $_POST['select_all'] == '1'): ?>checked="checked"<?php endif; ?> />&nbsp;<?php echo $this->_tpl_vars['esynI18N']['select_all']; ?>
</label>
			
			<?php $_from = $this->_tpl_vars['langs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['code'] => $this->_tpl_vars['lang']):
?>
				<br /><label><input type="checkbox" class="block_languages" name="block_languages[]" value="<?php echo $this->_tpl_vars['code']; ?>
" <?php if (isset ( $this->_tpl_vars['block']['block_languages'] ) && ! empty ( $this->_tpl_vars['block']['block_languages'] ) && in_array ( $this->_tpl_vars['code'] , $this->_tpl_vars['block']['block_languages'] )): ?>checked="checked"<?php elseif (isset ( $_POST['block_languages'] ) && in_array ( $this->_tpl_vars['code'] , $_POST['block_languges'] )): ?>checked="checked"<?php endif; ?> />&nbsp;<?php echo $this->_tpl_vars['lang']; ?>
</label>
			<?php endforeach; endif; unset($_from); ?>
		</td>
	</tr>
	<tr>
		<td><strong><?php echo $this->_tpl_vars['esynI18N']['sticky']; ?>
:</strong></td>
		<td>
			<input type="checkbox" id="sticky" name="sticky" value="1" <?php if (isset ( $this->_tpl_vars['block']['sticky'] ) && $this->_tpl_vars['block']['sticky'] == '1'): ?>checked="checked"<?php elseif (isset ( $_POST['sticky'] ) && $_POST['sticky'] == '1'): ?>checked="checked"<?php elseif (! isset ( $this->_tpl_vars['block'] ) && ! $_POST): ?>checked="checked"<?php endif; ?> />
		</td>
	</tr>
	</table>
	
	<div id="acos" style="display: none;">
	<table class="striped">
	<tr>
		<td width="120"><strong><?php echo $this->_tpl_vars['esynI18N']['visible_on_pages']; ?>
:</strong></td>
		<td>
			<?php if (isset ( $this->_tpl_vars['pages_group'] ) && ! empty ( $this->_tpl_vars['pages_group'] )): ?>
				<?php if (isset ( $this->_tpl_vars['pages'] ) && ! empty ( $this->_tpl_vars['pages'] )): ?>
					<input type="checkbox" value="1" name="select_all" id="select_all" <?php if (isset ( $_POST['select_all'] ) && $_POST['select_all'] == '1'): ?>checked="checked"<?php endif; ?> /><label for="select_all">&nbsp;<?php echo $this->_tpl_vars['esynI18N']['select_all']; ?>
</label>
						<div style="clear:both;"></div>
					<?php $_from = $this->_tpl_vars['pages_group']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['group']):
?>
						<fieldset class="list" style="float:left;">
							<?php $this->assign('post_key', ((is_array($_tmp='select_all_')) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['group']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['group']))); ?>
							<legend><input type="checkbox" value="1" class="<?php echo $this->_tpl_vars['group']; ?>
" name="select_all_<?php echo $this->_tpl_vars['group']; ?>
" id="select_all_<?php echo $this->_tpl_vars['group']; ?>
" <?php if (isset ( $_POST[$this->_tpl_vars['post_key']] ) && $_POST[$this->_tpl_vars['post_key']] == '1'): ?>checked="checked"<?php endif; ?> /><label for="select_all_<?php echo $this->_tpl_vars['group']; ?>
">&nbsp;<strong><?php echo $this->_tpl_vars['esynI18N'][$this->_tpl_vars['group']]; ?>
</strong></label></legend>
							<?php $_from = $this->_tpl_vars['pages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['page']):
?>
								<?php if ($this->_tpl_vars['page']['group'] == $this->_tpl_vars['group']): ?>
									<ul style="list-style-type: none; width:200px;">
										<li style="margin: 0 0 0 15px; padding-bottom: 3px; float: left; width: 200px;" >
											<input type="checkbox" name="visible_on_pages[]" class="<?php echo $this->_tpl_vars['group']; ?>
" value="<?php echo $this->_tpl_vars['page']['name']; ?>
" id="page_<?php echo $this->_tpl_vars['key']; ?>
" <?php if (in_array ( $this->_tpl_vars['page']['name'] , $this->_tpl_vars['visibleOn'] , true )): ?>checked="checked"<?php endif; ?> /><label for="page_<?php echo $this->_tpl_vars['key']; ?>
"> <?php if (empty ( $this->_tpl_vars['page']['title'] )): ?><?php echo $this->_tpl_vars['page']['name']; ?>
<?php else: ?><?php echo $this->_tpl_vars['page']['title']; ?>
<?php endif; ?></label>
										</li>
									</ul>
								<?php endif; ?>
							<?php endforeach; endif; unset($_from); ?>
						</fieldset>
					<?php endforeach; endif; unset($_from); ?>
				<?php endif; ?>
			<?php endif; ?>
		</td>
	</tr>
	</table>
	</div>

	<table class="striped" cellspacing="0" width="100%">
		<tr>
			<td class="caption" colspan="2"><strong><?php echo $this->_tpl_vars['esynI18N']['block_contents']; ?>
</strong></td>
		</tr>
	</table>

	<div id="blocks_contents" style="display: none;">
		<table class="striped" cellspacing="0" width="100%">
		<tr>
			<td width="150"><strong><?php echo $this->_tpl_vars['esynI18N']['title']; ?>
:</strong></td>
			<td><input type="text" name="multi_title" size="30" class="common" value="<?php if (isset ( $this->_tpl_vars['block']['title'] ) && ! is_array ( $this->_tpl_vars['block']['title'] )): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['block']['title'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php elseif (isset ( $_POST['multi_title'] )): ?><?php echo ((is_array($_tmp=$_POST['multi_title'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php endif; ?>" /></td>
		</tr>
		<tr>
			<td><strong><?php echo $this->_tpl_vars['esynI18N']['contents']; ?>
:</strong></td>
			<td><textarea name="multi_contents" id="multi_contents" cols="50" rows="8" class="cked common"><?php if (isset ( $this->_tpl_vars['block']['contents'] ) && ! is_array ( $this->_tpl_vars['block']['contents'] )): ?><?php echo $this->_tpl_vars['block']['contents']; ?>
<?php elseif (isset ( $_POST['multi_contents'] )): ?><?php echo $_POST['multi_contents']; ?>
<?php endif; ?></textarea></td>
		</tr>
		</table>
	</div>
	
	<?php $_from = $this->_tpl_vars['langs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['code'] => $this->_tpl_vars['lang']):
?>
		<div id="blocks_contents_<?php echo $this->_tpl_vars['code']; ?>
" style="display: none;">
			<table class="striped" cellspacing="0" width="100%">
			<tr>
				<td width="150"><strong><?php echo $this->_tpl_vars['esynI18N']['title']; ?>
&nbsp;[<?php echo $this->_tpl_vars['lang']; ?>
]:</strong></td>
				<td><input type="text" name="title[<?php echo $this->_tpl_vars['code']; ?>
]" size="30" class="common" value="<?php if (is_array ( $this->_tpl_vars['block']['title'] ) && isset ( $this->_tpl_vars['block']['title'][$this->_tpl_vars['code']] )): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['block']['title'][$this->_tpl_vars['code']])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php elseif (isset ( $_POST['title'] ) && is_array ( $_POST['title'] ) && isset ( $this->_tpl_vars['block']['post']['title'][$this->_tpl_vars['code']] )): ?><?php echo ((is_array($_tmp=$_POST['title'][$this->_tpl_vars['code']])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php elseif (isset ( $this->_tpl_vars['block']['title'] ) && ! empty ( $this->_tpl_vars['block']['title'] )): ?><?php echo $this->_tpl_vars['block']['title']; ?>
<?php endif; ?>" /></td>
			</tr>
			<tr>
				<td><strong><?php echo $this->_tpl_vars['esynI18N']['contents']; ?>
&nbsp;[<?php echo $this->_tpl_vars['lang']; ?>
]:</strong></td>
				<td><textarea name="contents[<?php echo $this->_tpl_vars['code']; ?>
]" id="contents_<?php echo $this->_tpl_vars['code']; ?>
" cols="50" rows="8" class="cked common"><?php if (is_array ( $this->_tpl_vars['block']['contents'] ) && isset ( $this->_tpl_vars['block']['contents'][$this->_tpl_vars['code']] )): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['block']['contents'][$this->_tpl_vars['code']])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php elseif (isset ( $_POST['contents'] ) && is_array ( $_POST['contents'] ) && isset ( $_POST['contents'][$this->_tpl_vars['code']] )): ?><?php echo ((is_array($_tmp=$_POST['contents'][$this->_tpl_vars['code']])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php elseif (isset ( $this->_tpl_vars['block']['contents'] ) && ! empty ( $this->_tpl_vars['block']['contents'] )): ?><?php echo $this->_tpl_vars['block']['contents']; ?>
<?php endif; ?></textarea></td>
			</tr>
			</table>
		</div>
	<?php endforeach; endif; unset($_from); ?>
	
	<table class="striped">
	<tr class="all">
		<td colspan="2">
			<input type="submit" name="save" class="common" value="<?php if ($_GET['do'] == 'edit'): ?><?php echo $this->_tpl_vars['esynI18N']['save_changes']; ?>
<?php else: ?><?php echo $this->_tpl_vars['esynI18N']['add']; ?>
<?php endif; ?>" />
		</td>
	</tr>
	</table>
	<input type="hidden" name="do" value="<?php if (isset ( $_GET['do'] )): ?><?php echo $_GET['do']; ?>
<?php endif; ?>" />
	<input type="hidden" name="id" value="<?php if (isset ( $this->_tpl_vars['block']['id'] )): ?><?php echo $this->_tpl_vars['block']['id']; ?>
<?php endif; ?>" />
	</form>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php else: ?>
	<div id="box_blocks" style="margin-top: 15px;"></div>
<?php endif; ?>

<?php echo smarty_function_include_file(array('js' => "js/intelli/intelli.grid, js/intelli/intelli.gmodel, js/ext/plugins/bettercombobox/betterComboBox, js/ext/plugins/panelresizer/PanelResizer, js/ext/plugins/progressbarpager/ProgressBarPager, js/ckeditor/ckeditor, js/admin/blocks"), $this);?>


<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>