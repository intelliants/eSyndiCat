<?php /* Smarty version 2.6.26, created on 2011-12-15 08:48:15
         compiled from /home/vbezruchkin/www/v1700/admin/templates/default/suggest-listing.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'cat', '/home/vbezruchkin/www/v1700/admin/templates/default/suggest-listing.tpl', 1, false),array('modifier', 'escape', '/home/vbezruchkin/www/v1700/admin/templates/default/suggest-listing.tpl', 31, false),array('modifier', 'explode', '/home/vbezruchkin/www/v1700/admin/templates/default/suggest-listing.tpl', 47, false),array('modifier', 'is_file', '/home/vbezruchkin/www/v1700/admin/templates/default/suggest-listing.tpl', 101, false),array('modifier', 'file_exists', '/home/vbezruchkin/www/v1700/admin/templates/default/suggest-listing.tpl', 101, false),array('modifier', 'default', '/home/vbezruchkin/www/v1700/admin/templates/default/suggest-listing.tpl', 150, false),array('function', 'preventCsrf', '/home/vbezruchkin/www/v1700/admin/templates/default/suggest-listing.tpl', 6, false),array('function', 'esynHooker', '/home/vbezruchkin/www/v1700/admin/templates/default/suggest-listing.tpl', 21, false),array('function', 'html_radio_switcher', '/home/vbezruchkin/www/v1700/admin/templates/default/suggest-listing.tpl', 150, false),array('function', 'include_file', '/home/vbezruchkin/www/v1700/admin/templates/default/suggest-listing.tpl', 267, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "header.tpl", 'smarty_include_vars' => array('css' => ((is_array($_tmp=@ESYN_URL)) ? $this->_run_mod_handler('cat', true, $_tmp, "js/jquery/plugins/lightbox/css/jquery.lightbox") : smarty_modifier_cat($_tmp, "js/jquery/plugins/lightbox/css/jquery.lightbox")))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-header.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['gTitle'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<form name="suggest_listing" action="controller.php?file=suggest-listing<?php if (isset ( $_GET['do'] )): ?>&amp;do=<?php echo $_GET['do']; ?>
<?php endif; ?><?php if (isset ( $_GET['status'] )): ?>&amp;status=<?php echo $_GET['status']; ?>
<?php endif; ?><?php if (isset ( $_GET['id'] )): ?>&amp;id=<?php echo $_GET['id']; ?>
<?php endif; ?>" method="post" enctype="multipart/form-data">
<?php echo esynUtil::preventCsrf(array(), $this);?>

<table cellspacing="0" cellpadding="0" width="100%" class="striped">
<tr>
	<td width="200"><strong><?php echo $this->_tpl_vars['esynI18N']['listing_category']; ?>
:</strong></td>
	<td>
		<span id="parent_category_title_container">
			<strong><?php if (isset ( $this->_tpl_vars['category']['title'] )): ?><a href="controller.php?file=browse&amp;id=<?php echo $this->_tpl_vars['parent']['id']; ?>
"><?php echo $this->_tpl_vars['category']['title']; ?>
</a><?php else: ?>ROOT<?php endif; ?></strong>
		</span>&nbsp;|&nbsp;
		<a href="#" id="change_category"><?php echo $this->_tpl_vars['esynI18N']['change']; ?>
...</a>

		<input type="hidden" id="category_id" name="category_id" value="<?php echo $this->_tpl_vars['category']['id']; ?>
" />
		<input type="hidden" id="category_parents" name="category_parents" value="<?php if (isset ( $this->_tpl_vars['category']['parents'] )): ?><?php echo $this->_tpl_vars['category']['parents']; ?>
<?php endif; ?>" />
	</td>
</tr>

<?php echo smarty_function_esynHooker(array('name' => 'tplAdminSuggestListingForm'), $this);?>


<?php if (isset ( $this->_tpl_vars['fields'] )): ?>
	<?php $_from = $this->_tpl_vars['fields']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['value']):
?>
		<tr>
			<?php $this->assign('lang_key', ((is_array($_tmp='field_')) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['value']['name']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['value']['name']))); ?>
			<?php $this->assign('value_name', $this->_tpl_vars['value']['name']); ?>
			<td><strong><?php echo $this->_tpl_vars['esynI18N'][$this->_tpl_vars['lang_key']]; ?>
:</strong></td>
			<td>
			<?php if ($this->_tpl_vars['value']['type'] == 'text' || $this->_tpl_vars['value']['type'] == 'number'): ?>
				<input <?php if ($this->_tpl_vars['value']['length'] != ''): ?>maxlength="<?php echo $this->_tpl_vars['value']['length']; ?>
"<?php endif; ?> type="text" name="<?php echo $this->_tpl_vars['value']['name']; ?>
" value="<?php if (isset ( $this->_tpl_vars['listing'][$this->_tpl_vars['value_name']] )): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['listing'][$this->_tpl_vars['value_name']])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php elseif (isset ( $_POST[$this->_tpl_vars['value_name']] )): ?><?php echo $_POST[$this->_tpl_vars['value_name']]; ?>
<?php else: ?><?php echo $this->_tpl_vars['value']['default']; ?>
<?php endif; ?>" class="common<?php if ($this->_tpl_vars['value']['type'] == 'number'): ?> numeric<?php endif; ?>" size="45" />
			<?php elseif ($this->_tpl_vars['value']['type'] == 'textarea'): ?>
				<?php if ($this->_tpl_vars['value']['editor'] == '1'): ?>
					<textarea class="ckeditor_textarea" id="<?php echo $this->_tpl_vars['value']['name']; ?>
" name="<?php echo $this->_tpl_vars['value']['name']; ?>
" cols="53" rows="8"><?php if (isset ( $this->_tpl_vars['listing'][$this->_tpl_vars['value_name']] )): ?><?php echo $this->_tpl_vars['listing'][$this->_tpl_vars['value_name']]; ?>
<?php elseif (isset ( $_POST[$this->_tpl_vars['value_name']] )): ?><?php echo $_POST[$this->_tpl_vars['value_name']]; ?>
<?php else: ?><?php echo $this->_tpl_vars['value']['default']; ?>
<?php endif; ?></textarea>
				<?php else: ?>
					<textarea name="<?php echo $this->_tpl_vars['value']['name']; ?>
" cols="53" rows="8" class="common"><?php if (isset ( $this->_tpl_vars['listing'][$this->_tpl_vars['value_name']] )): ?><?php echo $this->_tpl_vars['listing'][$this->_tpl_vars['value_name']]; ?>
<?php elseif (isset ( $_POST[$this->_tpl_vars['value_name']] )): ?><?php echo $_POST[$this->_tpl_vars['value_name']]; ?>
<?php else: ?><?php echo $this->_tpl_vars['value']['default']; ?>
<?php endif; ?></textarea><br />
				<?php endif; ?>
			<?php elseif ($this->_tpl_vars['value']['type'] == 'combo'): ?>
				<?php if (isset ( $this->_tpl_vars['listing'][$this->_tpl_vars['value_name']] )): ?>
					<?php $this->assign('temp', $this->_tpl_vars['listing'][$this->_tpl_vars['value_name']]); ?>
				<?php elseif (isset ( $_POST[$this->_tpl_vars['value_name']] )): ?>
					<?php $this->assign('temp', $_POST[$this->_tpl_vars['value_name']]); ?>
				<?php else: ?>
					<?php $this->assign('temp', $this->_tpl_vars['value']['default']); ?>
				<?php endif; ?>
				
				<?php $this->assign('values', ((is_array($_tmp=',')) ? $this->_run_mod_handler('explode', true, $_tmp, $this->_tpl_vars['value']['values']) : explode($_tmp, $this->_tpl_vars['value']['values']))); ?> 
				
				<?php if ($this->_tpl_vars['values']): ?>
					<select name="<?php echo $this->_tpl_vars['value']['name']; ?>
">
					<?php $_from = $this->_tpl_vars['values']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
						<?php $this->assign('key', ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp='field_')) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['value']['name']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['value']['name'])))) ? $this->_run_mod_handler('cat', true, $_tmp, '_') : smarty_modifier_cat($_tmp, '_')))) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['item']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['item']))); ?>
						<option value="<?php echo $this->_tpl_vars['item']; ?>
" <?php if ($this->_tpl_vars['item'] == $this->_tpl_vars['temp']): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['esynI18N'][$this->_tpl_vars['key']]; ?>
</option>
					<?php endforeach; endif; unset($_from); ?>
					</select>
				<?php endif; ?>
			<?php elseif ($this->_tpl_vars['value']['type'] == 'radio'): ?>
				<?php if (isset ( $this->_tpl_vars['listing'][$this->_tpl_vars['value_name']] )): ?>
					<?php $this->assign('temp', $this->_tpl_vars['listing'][$this->_tpl_vars['value_name']]); ?>
				<?php elseif (isset ( $_POST[$this->_tpl_vars['value_name']] )): ?>
					<?php $this->assign('temp', $_POST[$this->_tpl_vars['value_name']]); ?>
				<?php else: ?>
					<?php $this->assign('temp', $this->_tpl_vars['value']['default']); ?>
				<?php endif; ?>
				
				<?php $this->assign('values', ((is_array($_tmp=',')) ? $this->_run_mod_handler('explode', true, $_tmp, $this->_tpl_vars['value']['values']) : explode($_tmp, $this->_tpl_vars['value']['values']))); ?> 
				
				<?php if ($this->_tpl_vars['values']): ?>
					<?php $_from = $this->_tpl_vars['values']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
						<?php $this->assign('key', ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp='field_')) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['value']['name']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['value']['name'])))) ? $this->_run_mod_handler('cat', true, $_tmp, '_') : smarty_modifier_cat($_tmp, '_')))) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['item']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['item']))); ?>
						<input type="radio" name="<?php echo $this->_tpl_vars['value']['name']; ?>
" id="<?php echo $this->_tpl_vars['value']['name']; ?>
_<?php echo $this->_tpl_vars['item']; ?>
" value="<?php echo $this->_tpl_vars['item']; ?>
" <?php if ($this->_tpl_vars['item'] == $this->_tpl_vars['temp']): ?>checked="checked"<?php endif; ?> />
						<label for="<?php echo $this->_tpl_vars['value']['name']; ?>
_<?php echo $this->_tpl_vars['item']; ?>
"><?php echo $this->_tpl_vars['esynI18N'][$this->_tpl_vars['key']]; ?>
</label>
					<?php endforeach; endif; unset($_from); ?>
				<?php endif; ?>
			<?php elseif ($this->_tpl_vars['value']['type'] == 'checkbox'): ?>
				<?php if (isset ( $this->_tpl_vars['listing'][$this->_tpl_vars['value_name']] )): ?>
					<?php $this->assign('default', ((is_array($_tmp=',')) ? $this->_run_mod_handler('explode', true, $_tmp, $this->_tpl_vars['listing'][$this->_tpl_vars['value_name']]) : explode($_tmp, $this->_tpl_vars['listing'][$this->_tpl_vars['value_name']]))); ?>
				<?php elseif (isset ( $_POST[$this->_tpl_vars['value_name']] )): ?>
					<?php $this->assign('default', $_POST[$this->_tpl_vars['value_name']]); ?>
				<?php else: ?>
					<?php $this->assign('default', ((is_array($_tmp=',')) ? $this->_run_mod_handler('explode', true, $_tmp, $this->_tpl_vars['value']['default']) : explode($_tmp, $this->_tpl_vars['value']['default']))); ?> 
				<?php endif; ?>
				
				<?php $this->assign('checkboxes', ((is_array($_tmp=',')) ? $this->_run_mod_handler('explode', true, $_tmp, $this->_tpl_vars['value']['values']) : explode($_tmp, $this->_tpl_vars['value']['values']))); ?>			
				
				<?php if ($this->_tpl_vars['checkboxes']): ?>
					<?php $_from = $this->_tpl_vars['checkboxes']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['index'] => $this->_tpl_vars['item']):
?>
						<?php $this->assign('key', ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp='field_')) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['value']['name']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['value']['name'])))) ? $this->_run_mod_handler('cat', true, $_tmp, '_') : smarty_modifier_cat($_tmp, '_')))) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['index']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['index']))); ?>
						<input type="checkbox" name="<?php echo $this->_tpl_vars['value']['name']; ?>
[]" id="<?php echo $this->_tpl_vars['value']['name']; ?>
_<?php echo $this->_tpl_vars['item']; ?>
" value="<?php echo $this->_tpl_vars['item']; ?>
" <?php if (in_array ( $this->_tpl_vars['item'] , $this->_tpl_vars['default'] )): ?>checked="checked"<?php endif; ?> />
						<label for="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['value']['name'])) ? $this->_run_mod_handler('cat', true, $_tmp, '_') : smarty_modifier_cat($_tmp, '_')))) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['item']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['item'])); ?>
"><?php echo $this->_tpl_vars['esynI18N'][$this->_tpl_vars['key']]; ?>
</label>
					<?php endforeach; endif; unset($_from); ?>
				<?php endif; ?>
			<?php elseif ($this->_tpl_vars['value']['type'] == 'image' || $this->_tpl_vars['value']['type'] == 'storage'): ?>
				<?php if (! is_writeable ( ((is_array($_tmp=((is_array($_tmp=@ESYN_HOME)) ? $this->_run_mod_handler('cat', true, $_tmp, @ESYN_DS) : smarty_modifier_cat($_tmp, @ESYN_DS)))) ? $this->_run_mod_handler('cat', true, $_tmp, 'uploads') : smarty_modifier_cat($_tmp, 'uploads')) )): ?>
					<div style="width: 430px; padding: 3px; margin: 0; background: #FFE269 none repeat scroll 0 0;"><i><?php echo $this->_tpl_vars['esynI18N']['upload_writable_permission']; ?>
</i></div>
				<?php else: ?>
					<input type="file" name="<?php echo $this->_tpl_vars['value']['name']; ?>
" id="<?php echo $this->_tpl_vars['value']['name']; ?>
" size="40" style="float:left;" />
					<?php if (isset ( $_GET['do'] ) && $_GET['do'] == 'edit'): ?>
						<?php $this->assign('file_path', ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=@ESYN_HOME)) ? $this->_run_mod_handler('cat', true, $_tmp, 'uploads') : smarty_modifier_cat($_tmp, 'uploads')))) ? $this->_run_mod_handler('cat', true, $_tmp, @ESYN_DS) : smarty_modifier_cat($_tmp, @ESYN_DS)))) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['listing'][$this->_tpl_vars['value_name']]) : smarty_modifier_cat($_tmp, $this->_tpl_vars['listing'][$this->_tpl_vars['value_name']]))); ?>
						
						<?php if (((is_array($_tmp=$this->_tpl_vars['file_path'])) ? $this->_run_mod_handler('is_file', true, $_tmp) : is_file($_tmp)) && ((is_array($_tmp=$this->_tpl_vars['file_path'])) ? $this->_run_mod_handler('file_exists', true, $_tmp) : file_exists($_tmp))): ?>
							<div id="file_manage" style="float:left;padding-left:10px;">
								<a href="../uploads/<?php echo $this->_tpl_vars['listing'][$this->_tpl_vars['value_name']]; ?>
" target="_blank"><?php echo $this->_tpl_vars['esynI18N']['view']; ?>
</a>&nbsp;|&nbsp;
								<a href="<?php echo $this->_tpl_vars['value_name']; ?>
/<?php echo $_GET['id']; ?>
/<?php echo $this->_tpl_vars['listing'][$this->_tpl_vars['value_name']]; ?>
/" class="clear"><?php echo $this->_tpl_vars['esynI18N']['delete']; ?>
</a>
							</div>
						<?php endif; ?>
					<?php endif; ?>
				<?php endif; ?>
			<?php elseif ($this->_tpl_vars['value']['type'] == 'pictures'): ?>
				<?php if (! is_writeable ( ((is_array($_tmp=((is_array($_tmp=@ESYN_HOME)) ? $this->_run_mod_handler('cat', true, $_tmp, @ESYN_DS) : smarty_modifier_cat($_tmp, @ESYN_DS)))) ? $this->_run_mod_handler('cat', true, $_tmp, 'uploads') : smarty_modifier_cat($_tmp, 'uploads')) )): ?>
					<div style="width: 430px; padding: 3px; margin: 0; background: #FFE269 none repeat scroll 0 0;"><i><?php echo $this->_tpl_vars['esynI18N']['upload_writable_permission']; ?>
</i></div>
				<?php else: ?>
					<div class="pictures">
						<input type="file" name="<?php echo $this->_tpl_vars['value']['name']; ?>
[]" size="35" />
						<input type="button" value="+" class="add_img" />
						<input type="button" value="-" class="remove_img" />
					</div>
					<input type="hidden" value="<?php echo $this->_tpl_vars['value']['length']; ?>
" name="num_images" id="<?php echo $this->_tpl_vars['value']['name']; ?>
_num_img" />
					<?php if (isset ( $_GET['do'] ) && $_GET['do'] == 'edit'): ?>
						<?php if (! empty ( $this->_tpl_vars['listing'][$this->_tpl_vars['value_name']] )): ?>
							<?php $this->assign('images', ((is_array($_tmp=',')) ? $this->_run_mod_handler('explode', true, $_tmp, $this->_tpl_vars['listing'][$this->_tpl_vars['value_name']]) : explode($_tmp, $this->_tpl_vars['listing'][$this->_tpl_vars['value_name']]))); ?>

							<?php $_from = $this->_tpl_vars['images']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['image']):
?>
								<?php $this->assign('file_path', ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=@ESYN_HOME)) ? $this->_run_mod_handler('cat', true, $_tmp, 'uploads') : smarty_modifier_cat($_tmp, 'uploads')))) ? $this->_run_mod_handler('cat', true, $_tmp, @ESYN_DS) : smarty_modifier_cat($_tmp, @ESYN_DS)))) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['image']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['image']))); ?>

								<?php if (((is_array($_tmp=$this->_tpl_vars['file_path'])) ? $this->_run_mod_handler('is_file', true, $_tmp) : is_file($_tmp)) && ((is_array($_tmp=$this->_tpl_vars['file_path'])) ? $this->_run_mod_handler('file_exists', true, $_tmp) : file_exists($_tmp))): ?>
									<div class="image_box">
										<a href="../uploads/<?php echo $this->_tpl_vars['image']; ?>
" target="_blank" class="lightbox"><img src="../uploads/small_<?php echo $this->_tpl_vars['image']; ?>
" /></a>
										<a href="<?php echo $this->_tpl_vars['value_name']; ?>
/<?php echo $_GET['id']; ?>
/<?php echo $this->_tpl_vars['image']; ?>
" class="clear"><?php echo $this->_tpl_vars['esynI18N']['delete']; ?>
</a><br />
									</div>
								<?php endif; ?>
							<?php endforeach; endif; unset($_from); ?>
						<?php endif; ?>
					<?php endif; ?>
				<?php endif; ?>
			<?php endif; ?>
		</td>
		</tr>
	<?php endforeach; endif; unset($_from); ?>

	<?php if (isset ( $_GET['do'] ) && $_GET['do'] == 'edit'): ?>
		<tr>
			<td><strong><?php echo $this->_tpl_vars['esynI18N']['date']; ?>
</strong></td>
			<td><input type="text" name="date" id="date" class="common" value="<?php if (isset ( $this->_tpl_vars['listing']['date'] )): ?><?php echo $this->_tpl_vars['listing']['date']; ?>
<?php elseif (isset ( $_POST['date'] )): ?><?php echo $_POST['date']; ?>
<?php endif; ?>" /></td>
		</tr>
	<?php endif; ?>

	<tr>
		<td><strong><?php echo $this->_tpl_vars['esynI18N']['featured']; ?>
</strong></td>
		<td><?php echo smarty_function_html_radio_switcher(array('value' => ((is_array($_tmp=@$this->_tpl_vars['listing']['featured'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)),'name' => 'featured'), $this);?>
</td>
	</tr>

	<tr>
		<td><strong><?php echo $this->_tpl_vars['esynI18N']['partner']; ?>
</strong></td>
		<td><?php echo smarty_function_html_radio_switcher(array('value' => ((is_array($_tmp=@$this->_tpl_vars['listing']['partner'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)),'name' => 'partner'), $this);?>
</td>
	</tr>

	<tr>
		<td><strong><?php echo $this->_tpl_vars['esynI18N']['assign_account']; ?>
</strong></td>
		<td>
			<input type="radio" name="assign_account" value="1" id="a1" <?php if (isset ( $_POST['assign_account'] ) && $_POST['assign_account'] == '1'): ?>checked="checked"<?php endif; ?> /><label for="a1">&nbsp;<?php echo $this->_tpl_vars['esynI18N']['new_account']; ?>
</label>
			<input type="radio" name="assign_account" value="2" id="a2" <?php if (isset ( $_POST['assign_account'] ) && $_POST['assign_account'] == '2'): ?>checked="checked"<?php elseif (isset ( $_GET['do'] ) && $_GET['do'] == 'edit' && isset ( $this->_tpl_vars['account'] ) && ! empty ( $this->_tpl_vars['account'] )): ?>checked="checked"<?php endif; ?> /><label for="a2">&nbsp;<?php echo $this->_tpl_vars['esynI18N']['existing_account']; ?>
</label>
			<input type="radio" name="assign_account" value="0" id="a0" <?php if (isset ( $_POST['assign_account'] ) && $_POST['assign_account'] == '0'): ?>checked="checked"<?php elseif (! $_POST && ! isset ( $this->_tpl_vars['account'] )): ?>checked="checked"<?php endif; ?> /><label for="a0">&nbsp;<?php echo $this->_tpl_vars['esynI18N']['dont_assign']; ?>
</label>
		
			<div id="exist_account" style="display:none;">
				<div id="accounts_list"><?php if (isset ( $this->_tpl_vars['account'] ) && ! empty ( $this->_tpl_vars['account'] )): ?><?php echo $this->_tpl_vars['account']['id']; ?>
|<?php echo $this->_tpl_vars['account']['username']; ?>
<?php endif; ?></div>
			</div>			
			<div id="new_account" style="display:none;">
				<table border="0">
				<tr>
					<td><?php echo $this->_tpl_vars['esynI18N']['username']; ?>
:</td>
					<td><input type="text" name="new_account" size="45" class="common" value="<?php if (isset ( $_POST['new_account'] )): ?><?php echo $_POST['new_account']; ?>
<?php endif; ?>" /></td>
				</tr>
				<tr>
					<td><?php echo $this->_tpl_vars['esynI18N']['email']; ?>
:</td>
					<td><input type="text" name="new_account_email" size="45" class="common" value="<?php if (isset ( $_POST['new_account_email'] )): ?><?php echo $_POST['new_account_email']; ?>
<?php endif; ?>" /></td>
				</tr>
				</table>
			</div>
		</td>
	</tr>

	<tr>
		<td class="caption" colspan="2"><strong><?php echo $this->_tpl_vars['esynI18N']['additional_fields']; ?>
</strong></td>
	</tr>
	
	<tr>
		<td><strong><?php echo $this->_tpl_vars['esynI18N']['listing_status']; ?>
:</strong></td>
		<td> 
			<select name="status">
				<option value="active" <?php if (isset ( $this->_tpl_vars['listing']['status'] ) && $this->_tpl_vars['listing']['status'] == 'active'): ?>selected="selected"<?php elseif (isset ( $_POST['status'] ) && $_POST['status'] == 'active'): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['esynI18N']['active']; ?>
</option>
				<option value="approval" <?php if (isset ( $this->_tpl_vars['listing']['status'] ) && $this->_tpl_vars['listing']['status'] == 'approval'): ?>selected="selected"<?php elseif (isset ( $_POST['status'] ) && $_POST['status'] == 'approval'): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['esynI18N']['approval']; ?>
</option>
				<option value="banned" <?php if (isset ( $this->_tpl_vars['listing']['status'] ) && $this->_tpl_vars['listing']['status'] == 'banned'): ?>selected="selected"<?php elseif (isset ( $_POST['status'] ) && $_POST['status'] == 'banned'): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['esynI18N']['banned']; ?>
</option>
				<option value="banned" <?php if (isset ( $this->_tpl_vars['listing']['status'] ) && $this->_tpl_vars['listing']['status'] == 'suspended'): ?>selected="selected"<?php elseif (isset ( $_POST['status'] ) && $_POST['status'] == 'suspended'): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['esynI18N']['suspended']; ?>
</option>
			</select>
		</td>
	</tr>

	<tr>
		<td><strong><?php echo $this->_tpl_vars['esynI18N']['rank']; ?>
:</strong></td>
		<td> 
			<select name="rank">
				<?php unset($this->_sections['listing_rank']);
$this->_sections['listing_rank']['name'] = 'listing_rank';
$this->_sections['listing_rank']['loop'] = is_array($_loop='11') ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['listing_rank']['show'] = true;
$this->_sections['listing_rank']['max'] = $this->_sections['listing_rank']['loop'];
$this->_sections['listing_rank']['step'] = 1;
$this->_sections['listing_rank']['start'] = $this->_sections['listing_rank']['step'] > 0 ? 0 : $this->_sections['listing_rank']['loop']-1;
if ($this->_sections['listing_rank']['show']) {
    $this->_sections['listing_rank']['total'] = $this->_sections['listing_rank']['loop'];
    if ($this->_sections['listing_rank']['total'] == 0)
        $this->_sections['listing_rank']['show'] = false;
} else
    $this->_sections['listing_rank']['total'] = 0;
if ($this->_sections['listing_rank']['show']):

            for ($this->_sections['listing_rank']['index'] = $this->_sections['listing_rank']['start'], $this->_sections['listing_rank']['iteration'] = 1;
                 $this->_sections['listing_rank']['iteration'] <= $this->_sections['listing_rank']['total'];
                 $this->_sections['listing_rank']['index'] += $this->_sections['listing_rank']['step'], $this->_sections['listing_rank']['iteration']++):
$this->_sections['listing_rank']['rownum'] = $this->_sections['listing_rank']['iteration'];
$this->_sections['listing_rank']['index_prev'] = $this->_sections['listing_rank']['index'] - $this->_sections['listing_rank']['step'];
$this->_sections['listing_rank']['index_next'] = $this->_sections['listing_rank']['index'] + $this->_sections['listing_rank']['step'];
$this->_sections['listing_rank']['first']      = ($this->_sections['listing_rank']['iteration'] == 1);
$this->_sections['listing_rank']['last']       = ($this->_sections['listing_rank']['iteration'] == $this->_sections['listing_rank']['total']);
?>
					<option value="<?php echo $this->_sections['listing_rank']['index']; ?>
" <?php if (isset ( $this->_tpl_vars['listing']['rank'] ) && $this->_tpl_vars['listing']['rank'] == $this->_sections['listing_rank']['index']): ?>selected="selected"<?php endif; ?>><?php echo $this->_sections['listing_rank']['index']; ?>
</option>
				<?php endfor; endif; ?>
			</select>
		</td>
	</tr>

	<?php if ($this->_tpl_vars['config']['expiration_period'] > 0): ?>
		<tr>
			<td><strong><?php echo $this->_tpl_vars['esynI18N']['expiration_period']; ?>
:</strong></td>
			<td><input type="text" name="expire" class="common" value="<?php if (isset ( $this->_tpl_vars['listing']['expire'] ) && $this->_tpl_vars['listing']['expire'] > 0): ?><?php echo $this->_tpl_vars['listing']['expire']; ?>
<?php elseif (isset ( $_POST['expire'] )): ?><?php echo $_POST['expire']; ?>
<?php else: ?><?php echo $this->_tpl_vars['config']['expiration_period']; ?>
<?php endif; ?>" /></td>
		</tr>
		<tr>
			<td><strong><?php echo $this->_tpl_vars['esynI18N']['cron_for_expiration']; ?>
:</strong></td>
			<td>
				<select name="action_expire">
					<option value="" <?php if (isset ( $this->_tpl_vars['listing']['action_expire'] ) && $this->_tpl_vars['listing']['action_expire'] == ''): ?>selected="selected"<?php elseif (isset ( $_POST['action_expire'] ) && $_POST['action_expire'] == ''): ?>selected="selected"<?php elseif ($this->_tpl_vars['config']['expiration_action'] == ''): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['esynI18N']['nothing']; ?>
</option>
					<option value="remove" <?php if (isset ( $this->_tpl_vars['listing']['action_expire'] ) && $this->_tpl_vars['listing']['action_expire'] == 'remove'): ?>selected="selected"<?php elseif (isset ( $_POST['action_expire'] ) && $_POST['action_expire'] == 'remove'): ?>selected="selected"<?php elseif ($this->_tpl_vars['config']['expiration_action'] == 'remove'): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['esynI18N']['remove']; ?>
</option>
					<optgroup label="Status">
						<option value="approval" <?php if (isset ( $this->_tpl_vars['listing']['action_expire'] ) && $this->_tpl_vars['listing']['action_expire'] == 'approval'): ?>selected="selected"<?php elseif (isset ( $_POST['action_expire'] ) && $_POST['action_expire'] == 'approval'): ?>selected="selected"<?php elseif ($this->_tpl_vars['config']['expiration_action'] == 'approval'): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['esynI18N']['approval']; ?>
</option>
						<option value="banned" <?php if (isset ( $this->_tpl_vars['listing']['action_expire'] ) && $this->_tpl_vars['listing']['action_expire'] == 'banned'): ?>selected="selected"<?php elseif (isset ( $_POST['action_expire'] ) && $_POST['action_expire'] == 'banned'): ?>selected="selected"<?php elseif ($this->_tpl_vars['config']['expiration_action'] == 'banned'): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['esynI18N']['banned']; ?>
</option>
						<option value="suspended" <?php if (isset ( $this->_tpl_vars['listing']['action_expire'] ) && $this->_tpl_vars['listing']['action_expire'] == 'suspended'): ?>selected="selected"<?php elseif (isset ( $_POST['action_expire'] ) && $_POST['action_expire'] == 'suspended'): ?>selected="selected"<?php elseif ($this->_tpl_vars['config']['expiration_action'] == 'suspended'): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['esynI18N']['suspended']; ?>
</option>
					</optgroup>
					<optgroup label="Type">
						<option value="regular" <?php if (isset ( $this->_tpl_vars['listing']['action_expire'] ) && $this->_tpl_vars['listing']['action_expire'] == 'regular'): ?>selected="selected"<?php elseif (isset ( $_POST['action_expire'] ) && $_POST['action_expire'] == 'regular'): ?>selected="selected"<?php elseif ($this->_tpl_vars['config']['expiration_action'] == 'regular'): ?><?php endif; ?>><?php echo $this->_tpl_vars['esynI18N']['regular']; ?>
</option>
						<option value="featured" <?php if (isset ( $this->_tpl_vars['listing']['action_expire'] ) && $this->_tpl_vars['listing']['action_expire'] == 'featured'): ?>selected="selected"<?php elseif (isset ( $_POST['action_expire'] ) && $_POST['action_expire'] == 'featured'): ?>selected="selected"<?php elseif ($this->_tpl_vars['config']['expiration_action'] == 'featured'): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['esynI18N']['featured']; ?>
</option>
						<option value="partner" <?php if (isset ( $this->_tpl_vars['listing']['action_expire'] ) && $this->_tpl_vars['listing']['action_expire'] == 'partner'): ?>selected="selected"<?php elseif (isset ( $_POST['action_expire'] ) && $_POST['action_expire'] == 'partner'): ?>selected="selected"<?php elseif ($this->_tpl_vars['config']['expiration_action'] == 'partner'): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['esynI18N']['partner']; ?>
</option>
					</optgroup>
				</select>
			</td>
		</tr>
	<?php endif; ?>
	</table>
	
	<table cellspacing="0" width="100%" class="striped">
	<tr>
		<td style="padding: 0 0 0 11px; width: 1%">
			<input type="checkbox" name="send_email" id="send_email" <?php if ($this->_tpl_vars['config']['listing_admin_add']): ?>checked="checked"<?php endif; ?> />&nbsp;<label for="send_email"><?php echo $this->_tpl_vars['esynI18N']['email_notif']; ?>
?</label>&nbsp;|&nbsp;
			<input type="submit" name="save" class="common" value="<?php if (isset ( $_GET['do'] ) && $_GET['do'] == 'edit'): ?><?php echo $this->_tpl_vars['esynI18N']['save']; ?>
<?php else: ?><?php echo $this->_tpl_vars['esynI18N']['create_listing']; ?>
<?php endif; ?>" />

			<?php if (isset ( $_GET['do'] ) && $_GET['do'] == 'edit'): ?>
				<?php if (stristr ( $_SERVER['HTTP_REFERER'] , 'browse' )): ?>
					<input type="hidden" name="goto" value="browse" />
				<?php else: ?>
					<input type="hidden" name="goto" value="list" />
				<?php endif; ?>
			<?php else: ?>
				<span><strong><?php echo $this->_tpl_vars['esynI18N']['and_then']; ?>
</strong></span>
				<select name="goto">
					<option value="list" <?php if (isset ( $_POST['goto'] ) && $_POST['goto'] == 'list'): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['esynI18N']['go_to_list']; ?>
</option>
					<option value="add" <?php if (isset ( $_POST['goto'] ) && $_POST['goto'] == 'add'): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['esynI18N']['add_another_one']; ?>
</option>
					<option value="addtosame" <?php if (isset ( $_POST['goto'] ) && $_POST['goto'] == 'addtosame'): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['esynI18N']['add_another_one_to_same']; ?>
</option>
				</select>
			<?php endif; ?>
		</td>
	</tr>

	</table>
	<input type="hidden" name="do" value="<?php if (isset ( $_GET['do'] ) && $_GET['do'] == 'edit'): ?><?php echo $_GET['do']; ?>
<?php endif; ?>" />
	</form>
<?php endif; ?>

<?php echo smarty_function_esynHooker(array('name' => 'tplAdminSuggestListingBeforeIncludeJs'), $this);?>


<?php echo smarty_function_include_file(array('js' => "js/jquery/plugins/iphoneswitch/jquery.iphone-switch, js/jquery/plugins/lightbox/jquery.lightbox, js/ckeditor/ckeditor, js/admin/suggest-listing"), $this);?>


<?php echo smarty_function_esynHooker(array('name' => 'tplAdminSuggestListingAfterIncludeJs'), $this);?>


<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>