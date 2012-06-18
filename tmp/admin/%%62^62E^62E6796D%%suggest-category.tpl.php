<?php /* Smarty version 2.6.26, created on 2011-12-15 08:54:34
         compiled from /home/vbezruchkin/www/v1700/admin/templates/default/suggest-category.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'preventCsrf', '/home/vbezruchkin/www/v1700/admin/templates/default/suggest-category.tpl', 5, false),array('function', 'html_radio_switcher', '/home/vbezruchkin/www/v1700/admin/templates/default/suggest-category.tpl', 59, false),array('function', 'include_file', '/home/vbezruchkin/www/v1700/admin/templates/default/suggest-category.tpl', 190, false),array('modifier', 'escape', '/home/vbezruchkin/www/v1700/admin/templates/default/suggest-category.tpl', 20, false),array('modifier', 'default', '/home/vbezruchkin/www/v1700/admin/templates/default/suggest-category.tpl', 59, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "header.tpl", 'smarty_include_vars' => array('css' => "js/ext/plugins/chooser/css/chooser")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-header.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['gTitle'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<form action="controller.php?file=suggest-category<?php if (isset ( $_GET['id'] )): ?>&amp;id=<?php echo $_GET['id']; ?>
<?php endif; ?><?php if (isset ( $_GET['do'] )): ?>&amp;do=<?php echo $_GET['do']; ?>
<?php endif; ?>" method="post">
<?php echo esynUtil::preventCsrf(array(), $this);?>

<table cellspacing="0" cellpadding="0" width="100%" class="striped">

<?php if (isset ( $this->_tpl_vars['parent'] ) && ! empty ( $this->_tpl_vars['parent'] )): ?>
	<tr>
		<td width="200"><strong><?php echo $this->_tpl_vars['esynI18N']['parent_category']; ?>
:</strong></td>
		<td>
			<span id="parent_category_title_container"><strong><?php if (isset ( $this->_tpl_vars['parent'] ) && ! empty ( $this->_tpl_vars['parent'] )): ?><a href="controller.php?file=browse&amp;id=<?php echo $this->_tpl_vars['parent']['id']; ?>
"><?php echo $this->_tpl_vars['parent']['title']; ?>
</a><?php else: ?><a href="controller.php?file=browse"><?php echo $this->_tpl_vars['category']['title']; ?>
</a><?php endif; ?></strong>&nbsp;|&nbsp;<a href="#" id="change_category"><?php echo $this->_tpl_vars['esynI18N']['change']; ?>
...</a></span>
			<input type="hidden" id="parent_id" name="parent_id" value="<?php if (isset ( $this->_tpl_vars['parent'] ) && ! empty ( $this->_tpl_vars['parent'] )): ?><?php echo $this->_tpl_vars['parent']['id']; ?>
<?php endif; ?>" />
		</td>
	</tr>
<?php endif; ?>

<tr>
	<td width="200"><strong><?php echo $this->_tpl_vars['esynI18N']['title']; ?>
:</strong></td>
	<td><input type="text" name="title" size="30" maxlength="150" class="common" value="<?php if (isset ( $this->_tpl_vars['category']['title'] ) && isset ( $_GET['do'] ) && $_GET['do'] == 'edit'): ?><?php echo $this->_tpl_vars['category']['title']; ?>
<?php elseif (isset ( $_POST['title'] )): ?><?php echo ((is_array($_tmp=$_POST['title'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php endif; ?>" /></td>
</tr>

<tr>
	<td><strong><?php echo $this->_tpl_vars['esynI18N']['page_title']; ?>
:</strong></td>
	<td><input type="text" name="page_title" size="30" maxlength="150" class="common" value="<?php if (isset ( $this->_tpl_vars['category']['page_title'] ) && isset ( $_GET['do'] ) && $_GET['do'] == 'edit'): ?><?php echo $this->_tpl_vars['category']['page_title']; ?>
<?php elseif (isset ( $_POST['page_title'] )): ?><?php echo ((is_array($_tmp=$_POST['page_title'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php endif; ?>" /></td>
</tr>

<?php if (isset ( $this->_tpl_vars['parent'] ) && ! empty ( $this->_tpl_vars['parent'] )): ?>
	<tr>
		<td><strong><?php echo $this->_tpl_vars['esynI18N']['path']; ?>
:</strong></td>
		<td>
			<input type="text" name="path" size="30" maxlength="150" class="common" style="float: left;" value="<?php if (isset ( $this->_tpl_vars['category']['path'] ) && isset ( $_GET['do'] ) && $_GET['do'] == 'edit'): ?><?php echo $this->_tpl_vars['category']['path']; ?>
<?php elseif (isset ( $_POST['path'] )): ?><?php echo ((is_array($_tmp=$_POST['path'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php endif; ?>" />&nbsp;
			<div style="float: left; display: none; margin-left: 3px; padding: 4px;" id="category_url_box"><span><?php echo $this->_tpl_vars['esynI18N']['category_url_will_be']; ?>
:&nbsp;</span><span id="category_url" style="padding: 3px; margin: 0; background: #FFE269;"></span></div>
		</td>
	</tr>
<?php endif; ?>

<tr>
	<td><strong><?php echo $this->_tpl_vars['esynI18N']['description']; ?>
:</strong></td>
	<td>
		<textarea name="description" id="description" cols="43" rows="8"><?php if (isset ( $this->_tpl_vars['category']['description'] ) && isset ( $_GET['do'] ) && $_GET['do'] == 'edit'): ?><?php echo $this->_tpl_vars['category']['description']; ?>
<?php elseif (isset ( $_POST['description'] )): ?><?php echo $_POST['description']; ?>
<?php endif; ?></textarea>
	</td>
</tr>

<tr>
	<td><strong><?php echo $this->_tpl_vars['esynI18N']['meta_description']; ?>
:</strong></td>
	<td>
		<textarea name="meta_description" cols="43" rows="8" class="common"><?php if (isset ( $this->_tpl_vars['category']['meta_description'] ) && isset ( $_GET['do'] ) && $_GET['do'] == 'edit'): ?><?php echo $this->_tpl_vars['category']['meta_description']; ?>
<?php elseif (isset ( $_POST['meta_description'] )): ?><?php echo ((is_array($_tmp=$_POST['meta_description'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php endif; ?></textarea>
	</td>
</tr>

<tr>
	<td><strong><?php echo $this->_tpl_vars['esynI18N']['meta_keywords']; ?>
:</strong></td>
	<td><input type="text" name="meta_keywords" size="60" maxlength="150" class="common" value="<?php if (isset ( $this->_tpl_vars['category']['meta_keywords'] ) && isset ( $_GET['do'] ) && $_GET['do'] == 'edit'): ?><?php echo $this->_tpl_vars['category']['meta_keywords']; ?>
<?php elseif (isset ( $_POST['meta_keywords'] )): ?><?php echo ((is_array($_tmp=$_POST['meta_keywords'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php endif; ?>" /></td>
</tr>

<tr>
	<td class="first"><strong><?php echo $this->_tpl_vars['esynI18N']['enable_no_follow']; ?>
:</strong></td>
	<td><?php echo smarty_function_html_radio_switcher(array('value' => ((is_array($_tmp=@$this->_tpl_vars['category']['no_follow'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)),'name' => 'no_follow'), $this);?>
</td>
</tr>

<tr>
	<td class="first"><strong><?php echo $this->_tpl_vars['esynI18N']['lock_category']; ?>
:</strong></td>
	<td>
		<?php echo smarty_function_html_radio_switcher(array('value' => ((is_array($_tmp=@$this->_tpl_vars['category']['locked'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)),'name' => 'locked'), $this);?>

		<div style="padding: 5px 0 0 100px;"><label><input type="checkbox" name="subcategories" />&nbsp;<?php echo $this->_tpl_vars['esynI18N']['include_subcats']; ?>
</label></div>
	</td>
</tr>

<tr>
	<td class="tip-header first" id="tip-header-hide_category"><strong><?php echo $this->_tpl_vars['esynI18N']['hide_category']; ?>
:</strong></td>
	<td><?php echo smarty_function_html_radio_switcher(array('value' => ((is_array($_tmp=@$this->_tpl_vars['category']['hidden'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)),'name' => 'hidden'), $this);?>
</td>
</tr>

<tr>
	<td class="first"><strong><?php echo $this->_tpl_vars['esynI18N']['unique_category_template']; ?>
:</strong></td>
	<td><?php echo smarty_function_html_radio_switcher(array('value' => ((is_array($_tmp=@$this->_tpl_vars['category']['unique_tpl'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)),'name' => 'unique_tpl'), $this);?>
</td>
</tr>

<tr>
	<td class="first"><strong><?php echo $this->_tpl_vars['esynI18N']['number_of_columns']; ?>
:</strong></td>
	<td>
		<span style="float: left;">
			<input type="radio" name="num_cols_type" value="1" <?php if (isset ( $this->_tpl_vars['category']['num_cols'] ) && isset ( $_GET['do'] ) && $_GET['do'] == 'edit' && $this->_tpl_vars['category']['num_cols'] == '0'): ?>checked="checked"<?php elseif (isset ( $_POST['num_cols_type'] ) && $_POST['num_cols_type'] == '1'): ?>checked="checked"<?php elseif (! $_POST): ?>checked="checked"<?php endif; ?> id="nc1" /><label for="nc1">&nbsp;<?php echo $this->_tpl_vars['esynI18N']['default']; ?>
 ( <?php echo $this->_tpl_vars['config']['num_categories_cols']; ?>
 )</label>
			<input type="radio" name="num_cols_type" value="0" <?php if (isset ( $this->_tpl_vars['category']['num_cols'] ) && isset ( $_GET['do'] ) && $_GET['do'] == 'edit' && $this->_tpl_vars['category']['num_cols'] != '0'): ?>checked="checked"<?php elseif (isset ( $_POST['num_cols_type'] ) && $_POST['num_cols_type'] == '0'): ?>checked="checked"<?php endif; ?> id="nc2" /><label for="nc2">&nbsp;<?php echo $this->_tpl_vars['esynI18N']['custom']; ?>
</label>&nbsp;&nbsp;&nbsp;
		</span>
		<span id="nc" style="display: none;"><input class="common numeric" type="text" name="num_cols" size="5" value="<?php if (isset ( $this->_tpl_vars['category']['num_cols'] )): ?><?php echo $this->_tpl_vars['category']['num_cols']; ?>
<?php elseif (isset ( $_POST['num_cols'] )): ?><?php echo $_POST['num_cols']; ?>
<?php elseif (empty ( $this->_tpl_vars['category'] )): ?><?php echo $this->_tpl_vars['config']['num_categories_cols']; ?>
<?php endif; ?>" style="text-align: right;" />&nbsp;<?php echo $this->_tpl_vars['esynI18N']['number_of_cols_tip']; ?>
</span>
	</td>
</tr>

<?php if ($this->_tpl_vars['config']['neighbour']): ?>
<tr>
	<td class="first"><strong><?php echo $this->_tpl_vars['esynI18N']['number_of_neighbours']; ?>
:</strong></td>
	<td>
		<span style="float: left;">
			<input type="radio" name="num_neighbours_type" value="-1" <?php if (isset ( $this->_tpl_vars['category']['num_neighbours'] ) && isset ( $_GET['do'] ) && $_GET['do'] == 'edit' && $this->_tpl_vars['category']['num_neighbours'] == '0'): ?>checked="checked"<?php elseif (isset ( $_POST['num_neighbours_type'] ) && $_POST['num_neighbours_type'] == '-1'): ?>checked="checked"<?php elseif (! $_POST): ?>checked="checked"<?php endif; ?> id="nnc0" /><label for="nnc0">&nbsp;<?php echo $this->_tpl_vars['esynI18N']['do_not_display_neighbours']; ?>
</label>
			<input type="radio" name="num_neighbours_type" value="0" <?php if (isset ( $this->_tpl_vars['category']['num_neighbours'] ) && isset ( $_GET['do'] ) && $_GET['do'] == 'edit' && $this->_tpl_vars['category']['num_neighbours'] == '-1'): ?>checked="checked"<?php elseif (isset ( $_POST['num_neighbours_type'] ) && $_POST['num_neighbours_type'] == '0'): ?>checked="checked"<?php endif; ?> id="nnc1" /><label for="nnc1">&nbsp;<?php echo $this->_tpl_vars['esynI18N']['all_neighbours']; ?>
</label>
			<input type="radio" name="num_neighbours_type" value="1" <?php if (isset ( $this->_tpl_vars['category']['num_neighbours'] ) && isset ( $_GET['do'] ) && $_GET['do'] == 'edit' && $this->_tpl_vars['category']['num_neighbours'] > 0): ?>checked="checked"<?php elseif (isset ( $_POST['num_neighbours_type'] ) && $_POST['num_neighbours_type'] == '1'): ?>checked="checked"<?php endif; ?> id="nnc2"/><label for="nnc2">&nbsp;<?php echo $this->_tpl_vars['esynI18N']['custom']; ?>
</label>&nbsp;&nbsp;&nbsp;
		</span>
		<span id="nnc" style="display: none;">
			<input class="common numeric" type="text" name="num_neighbours" size="5" value="<?php if (isset ( $this->_tpl_vars['category']['num_neighbours'] )): ?><?php echo $this->_tpl_vars['category']['num_neighbours']; ?>
<?php elseif (isset ( $_POST['num_neighbours'] )): ?><?php echo $_POST['num_neighbours']; ?>
<?php endif; ?>" style="text-align: right;" />&nbsp;<?php echo $this->_tpl_vars['esynI18N']['number_of_neigh_tip']; ?>
</span>
	</td>
</tr>
<?php endif; ?>

<tr>
	<td><strong><?php echo $this->_tpl_vars['esynI18N']['confirmation']; ?>
:</strong></td>
	<td>
		<input type="radio" name="confirmation" value="1" <?php if (isset ( $this->_tpl_vars['category']['confirmation'] ) && $this->_tpl_vars['category']['confirmation'] == '1'): ?>checked="checked"<?php elseif (isset ( $_POST['confirmation'] ) && $_POST['confirmation'] == '1'): ?>checked="checked"<?php endif; ?> id="confirmation1" /><label for="confirmation1">&nbsp;<?php echo $this->_tpl_vars['esynI18N']['yes']; ?>
</label>
		<input type="radio" name="confirmation" value="0" <?php if (isset ( $this->_tpl_vars['category']['confirmation'] ) && $this->_tpl_vars['category']['confirmation'] == '0'): ?>checked="checked"<?php elseif (isset ( $_POST['confirmation'] ) && $_POST['confirmation'] == '0'): ?>checked="checked"<?php elseif (empty ( $this->_tpl_vars['category'] )): ?>checked="checked"<?php endif; ?> id="confirmation2"/><label for="confirmation2">&nbsp;<?php echo $this->_tpl_vars['esynI18N']['no']; ?>
</label>
		<div id="confirmation_text" style="display: none;">
			<textarea name="confirmation_text" cols="43" rows="8" class="common"><?php if (isset ( $this->_tpl_vars['category']['confirmation_text'] )): ?><?php echo $this->_tpl_vars['category']['confirmation_text']; ?>
<?php elseif (isset ( $_POST['confirmation_text'] )): ?><?php echo $_POST['confirmation_text']; ?>
<?php endif; ?></textarea>
		</div>
	</td>
</tr>

<tr>
	<td><strong><?php echo $this->_tpl_vars['esynI18N']['status']; ?>
:</strong></td>
	<td>
		<select name="status">
			<option value="active" <?php if (isset ( $this->_tpl_vars['category']['status'] ) && $this->_tpl_vars['category']['status'] == 'active'): ?>selected="selected"<?php elseif (isset ( $_POST['status'] ) && $_POST['status'] == 'active'): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['esynI18N']['active']; ?>
</option>
			<option value="approval" <?php if (isset ( $this->_tpl_vars['category']['status'] ) && $this->_tpl_vars['category']['status'] == 'approval'): ?>selected="selected"<?php elseif (isset ( $_POST['status'] ) && $_POST['status'] == 'approval'): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['esynI18N']['approval']; ?>
</option>
		</select>
	</td>
</tr>

<tr>
	<td><strong><?php echo $this->_tpl_vars['esynI18N']['icon']; ?>
:</strong></td>
	<td>
		<div id="icons">
			<?php if (isset ( $this->_tpl_vars['category']['icon'] ) && ! empty ( $this->_tpl_vars['category']['icon'] )): ?>
				<img style="margin: 10px; visibility: visible; opacity: 1;" src="<?php echo $this->_tpl_vars['category']['icon']; ?>
" />
			<?php elseif (isset ( $_POST['icon'] ) && ! empty ( $_POST['icon'] )): ?>
				<img style="margin: 10px; visibility: visible; opacity: 1;" src="<?php echo $_POST['icon']; ?>
" />
			<?php endif; ?>
		</div>

		<input type="button" id="choose_icon" name="choose" class="common" value="<?php echo $this->_tpl_vars['esynI18N']['choose_icon']; ?>
" />
		<input type="button" id="remove_icon" name="remove" class="common" value="<?php echo $this->_tpl_vars['esynI18N']['remove_icon']; ?>
" />
		<input type="hidden" id="icon_name" name="icon" value="<?php if (isset ( $this->_tpl_vars['category']['icon'] )): ?><?php echo $this->_tpl_vars['category']['icon']; ?>
<?php elseif (isset ( $_POST['icon'] )): ?><?php echo $_POST['icon']; ?>
<?php endif; ?>" />
	</td>
</tr>
<?php if (! file_exists ( @ESYN_CATEGORY_ICONS_DIR )): ?>
<tr>
	<td>&nbsp;</td>
	<td>
		<span class="option_tip">
			<?php echo $this->_tpl_vars['esynI18N']['categories_icon_notif']; ?>

		</span>
	</td>
</tr>
<?php endif; ?>
</table>

<table cellspacing="0" width="100%" class="striped">
<tr>
	<td style="padding: 0 0 0 11px; width: 0;">
		<input type="submit" name="save" class="common" value="<?php if (isset ( $_GET['do'] ) && $_GET['do'] == 'edit'): ?><?php echo $this->_tpl_vars['esynI18N']['save_changes']; ?>
<?php else: ?><?php echo $this->_tpl_vars['esynI18N']['add']; ?>
<?php endif; ?>" />
	</td>
	<td style="padding: 0; width:99%;">
		<?php if (isset ( $_GET['do'] ) && $_GET['do'] == 'edit'): ?>
			<?php if (stristr ( $_SERVER['HTTP_REFERER'] , 'browse' )): ?>
				<input type="hidden" name="goto" value="browse_new" />
			<?php else: ?>
				<input type="hidden" name="goto" value="list" />
			<?php endif; ?>
		<?php else: ?>
			<span><strong>&nbsp;<?php echo $this->_tpl_vars['esynI18N']['and_then']; ?>
&nbsp;</strong></span>
			<select name="goto">
				<option value="list" <?php if (isset ( $_POST['goto'] ) && $_POST['goto'] == 'list'): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['esynI18N']['go_to_list']; ?>
</option>
				<option value="browse_add" <?php if (isset ( $_POST['goto'] ) && $_POST['goto'] == 'browse_add'): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['esynI18N']['go_to_browse']; ?>
 <?php echo $this->_tpl_vars['parent']['title']; ?>
</option>
				<option value="browse_new" <?php if (isset ( $_POST['goto'] ) && $_POST['goto'] == 'browse_new'): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['esynI18N']['go_to_browse_new_category']; ?>
</option>
				<option value="add" <?php if (isset ( $_POST['goto'] ) && $_POST['goto'] == 'add'): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['esynI18N']['add_another_one']; ?>
</option>
			</select>
		<?php endif; ?>
	</td>
</tr>

</table>
<input type="hidden" name="id" value="<?php if (isset ( $this->_tpl_vars['category']['id'] )): ?><?php echo $this->_tpl_vars['category']['id']; ?>
<?php endif; ?>" />
<input type="hidden" name="old_path" value="<?php if (isset ( $this->_tpl_vars['category']['old_path'] )): ?><?php echo $this->_tpl_vars['category']['old_path']; ?>
<?php endif; ?>" />
</form>

<div style="display: none;">
	<div id="tip-content-hide_category" ><?php echo $this->_tpl_vars['esynI18N']['hide_category_option']; ?>
</div>
</div>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php echo smarty_function_include_file(array('js' => "js/jquery/plugins/iphoneswitch/jquery.iphone-switch, js/ckeditor/ckeditor, js/ext/plugins/chooser/chooser, js/admin/suggest-category"), $this);?>


<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>