<?php /* Smarty version 2.6.26, created on 2011-12-13 05:12:08
         compiled from /home/vbezruchkin/www/v1700/admin/templates/default/browse.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'count', '/home/vbezruchkin/www/v1700/admin/templates/default/browse.tpl', 12, false),array('modifier', 'escape', '/home/vbezruchkin/www/v1700/admin/templates/default/browse.tpl', 14, false),array('modifier', 'replace', '/home/vbezruchkin/www/v1700/admin/templates/default/browse.tpl', 70, false),array('function', 'print_img', '/home/vbezruchkin/www/v1700/admin/templates/default/browse.tpl', 14, false),array('function', 'include_file', '/home/vbezruchkin/www/v1700/admin/templates/default/browse.tpl', 147, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "header.tpl", 'smarty_include_vars' => array('css' => "js/ext/plugins/panelresizer/css/PanelResizer")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-header.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['esynI18N']['browse_categories'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php if ($this->_tpl_vars['categories']): ?>
	<div class="categories">

	<?php $this->assign('cnt', '0'); ?>
	<?php $this->assign('row', '1'); ?>
	
	<?php $_from = $this->_tpl_vars['categories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['value']):
?>
		<?php $this->assign('cnt', $this->_tpl_vars['cnt']+1); ?>
		<?php if (! ( $this->_tpl_vars['cnt'] % 3 ) || $this->_tpl_vars['cnt'] == count($this->_tpl_vars['categories'])): ?>
			<div class="last"><div class="category">
				<?php if ($this->_tpl_vars['value']['crossed']): ?>@&nbsp;<?php endif; ?><a href="controller.php?file=browse&amp;id=<?php echo $this->_tpl_vars['value']['id']; ?>
" class="<?php echo $this->_tpl_vars['value']['status']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['value']['title'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</a>&nbsp;<?php if ($this->_tpl_vars['config']['num_listings_display']): ?>(<?php echo $this->_tpl_vars['value']['num_all_listings']; ?>
)<?php endif; ?><?php if ($this->_tpl_vars['value']['crossed']): ?>&nbsp;<a href="#" class="actions_edt-crossed_<?php echo $this->_tpl_vars['value']['id']; ?>
"><?php echo smarty_function_print_img(array('full' => true,'fl' => "icons/edit-grid-ico.png",'admin' => true,'style' => "vertical-align: middle;"), $this);?>
</a>&nbsp;<a href="#" class="actions_rmv-crossed_<?php echo $this->_tpl_vars['value']['id']; ?>
"><img style="vertical-align: middle;" src="<?php echo smarty_function_print_img(array('fl' => "remove-grid-ico.png",'folder' => "icons/",'admin' => 'true'), $this);?>
" alt="<?php echo $this->_tpl_vars['esynI18N']['remove']; ?>
 <?php echo ((is_array($_tmp=$this->_tpl_vars['value']['title'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
"></a><?php endif; ?>
				<?php if ($this->_tpl_vars['config']['subcats_display']): ?>
					<?php if (isset ( $this->_tpl_vars['value']['subcategories'] ) && ! empty ( $this->_tpl_vars['value']['subcategories'] )): ?>
						<div class="subcategories">
						<?php $this->assign('cnt2', '1'); ?>
						<?php $_from = $this->_tpl_vars['value']['subcategories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key2'] => $this->_tpl_vars['value2']):
?>
							<?php if (count($this->_tpl_vars['value']['subcategories']) < $this->_tpl_vars['config']['subcats_display']): ?>
								<?php $this->assign('min', count($this->_tpl_vars['value']['subcategories'])); ?>
							<?php else: ?>
								<?php $this->assign('min', $this->_tpl_vars['config']['subcats_display']); ?>
							<?php endif; ?>
							
							<a href="controller.php?file=browse&amp;id=<?php echo $this->_tpl_vars['value2']['id']; ?>
" class="<?php echo $this->_tpl_vars['value2']['status']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['value2']['title'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</a><?php if ($this->_tpl_vars['cnt2'] < $this->_tpl_vars['min']): ?>,<?php endif; ?>
							<?php $this->assign('cnt2', $this->_tpl_vars['cnt2']+1); ?>
						<?php endforeach; endif; unset($_from); ?>
						</div>
					<?php endif; ?>
				<?php endif; ?>	
			</div></div>
			<?php if ($this->_tpl_vars['row'] < count($this->_tpl_vars['categories']) / 3): ?>
				<div class="divider clearfix" style="clear: left;"></div>
			<?php endif; ?>
			<?php $this->assign('row', $this->_tpl_vars['row']+1); ?>
		<?php else: ?>
			<div class="col"><div class="category">
				<?php if ($this->_tpl_vars['value']['crossed']): ?>@&nbsp;<?php endif; ?><a href="controller.php?file=browse&amp;id=<?php echo $this->_tpl_vars['value']['id']; ?>
" class="<?php echo $this->_tpl_vars['value']['status']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['value']['title'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</a>&nbsp;<?php if ($this->_tpl_vars['config']['num_listings_display']): ?>(<?php echo $this->_tpl_vars['value']['num_all_listings']; ?>
)<?php endif; ?><?php if ($this->_tpl_vars['value']['crossed']): ?>&nbsp;<a href="#" class="actions_edt-crossed_<?php echo $this->_tpl_vars['value']['id']; ?>
"><?php echo smarty_function_print_img(array('full' => true,'fl' => "icons/edit-grid-ico.png",'admin' => true,'style' => "vertical-align: middle;"), $this);?>
</a>&nbsp;<a href="#" class="actions_rmv-crossed_<?php echo $this->_tpl_vars['value']['id']; ?>
"><img style="vertical-align: middle;" src="<?php echo smarty_function_print_img(array('fl' => "remove-grid-ico.png",'folder' => "icons/",'admin' => 'true'), $this);?>
" alt="<?php echo $this->_tpl_vars['esynI18N']['remove']; ?>
 <?php echo ((is_array($_tmp=$this->_tpl_vars['value']['title'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
"></a><?php endif; ?>
				<?php if ($this->_tpl_vars['config']['subcats_display']): ?>
					<?php if (isset ( $this->_tpl_vars['value']['subcategories'] ) && ! empty ( $this->_tpl_vars['value']['subcategories'] )): ?>
						<div class="subcategories">
						<?php $this->assign('cnt2', '1'); ?>
						<?php $_from = $this->_tpl_vars['value']['subcategories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key2'] => $this->_tpl_vars['value2']):
?>
							<?php if (count($this->_tpl_vars['value']['subcategories']) < $this->_tpl_vars['config']['subcats_display']): ?>
								<?php $this->assign('min', count($this->_tpl_vars['value']['subcategories'])); ?>
							<?php else: ?>
								<?php $this->assign('min', $this->_tpl_vars['config']['subcats_display']); ?>
							<?php endif; ?>
							
							<a href="controller.php?file=browse&amp;id=<?php echo $this->_tpl_vars['value2']['id']; ?>
" class="<?php echo $this->_tpl_vars['value2']['status']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['value2']['title'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</a><?php if ($this->_tpl_vars['cnt2'] < $this->_tpl_vars['min']): ?>,<?php endif; ?>
							<?php $this->assign('cnt2', $this->_tpl_vars['cnt2']+1); ?>
						<?php endforeach; endif; unset($_from); ?>
						</div>
					<?php endif; ?>
				<?php endif; ?>
			</div></div>
		<?php endif; ?>
	<?php endforeach; endif; unset($_from); ?>
	</div>

	<div style="clear:both;">&nbsp;</div>
<?php else: ?>
	<?php if (isset ( $_GET['id'] )): ?>
		<?php $this->assign('category_id', $_GET['id']); ?>
	<?php else: ?>
		<?php $this->assign('category_id', 0); ?>
	<?php endif; ?>

	<?php echo ((is_array($_tmp=$this->_tpl_vars['esynI18N']['no_categories'])) ? $this->_run_mod_handler('replace', true, $_tmp, "[category_id]", $this->_tpl_vars['category_id']) : smarty_modifier_replace($_tmp, "[category_id]", $this->_tpl_vars['category_id'])); ?>

<?php endif; ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>


<?php if (isset ( $this->_tpl_vars['related_categories'] ) && ! empty ( $this->_tpl_vars['related_categories'] )): ?>

	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-header.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['esynI18N']['related_categories'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

	<div class="categories">

		<?php $this->assign('cnt', '0'); ?>
		<?php $this->assign('row', '1'); ?>
		
		<?php $_from = $this->_tpl_vars['related_categories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['value']):
?>
			<?php $this->assign('cnt', $this->_tpl_vars['cnt']+1); ?>
			<?php if (! ( $this->_tpl_vars['cnt'] % 3 ) || $this->_tpl_vars['cnt'] == count($this->_tpl_vars['related_categories'])): ?>
				<div class="last"><div class="category">
					<a href="controller.php?file=browse&amp;id=<?php echo $this->_tpl_vars['value']['id']; ?>
" class="<?php echo $this->_tpl_vars['value']['status']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['value']['title'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</a>&nbsp;<?php if ($this->_tpl_vars['config']['num_listings_display']): ?>(<?php echo $this->_tpl_vars['value']['num_all_listings']; ?>
)<?php endif; ?>&nbsp;<a href="#" class="actions_rmv-related_<?php echo $this->_tpl_vars['value']['id']; ?>
"><img style="vertical-align: middle;" src="<?php echo smarty_function_print_img(array('fl' => "remove-grid-ico.png",'folder' => "icons/",'admin' => 'true'), $this);?>
" alt="<?php echo $this->_tpl_vars['esynI18N']['remove']; ?>
 <?php echo ((is_array($_tmp=$this->_tpl_vars['value']['title'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
"></a>
					<?php if ($this->_tpl_vars['config']['subcats_display']): ?>
						<?php if (isset ( $this->_tpl_vars['value']['subcategories'] ) && ! empty ( $this->_tpl_vars['value']['subcategories'] )): ?>
							<div class="subcategories">
							<?php $this->assign('cnt2', '1'); ?>
							<?php $_from = $this->_tpl_vars['value']['subcategories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key2'] => $this->_tpl_vars['value2']):
?>
								<?php if (count($this->_tpl_vars['value']['subcategories']) < $this->_tpl_vars['config']['subcats_display']): ?>
									<?php $this->assign('min', count($this->_tpl_vars['value']['subcategories'])); ?>
								<?php else: ?>
									<?php $this->assign('min', $this->_tpl_vars['config']['subcats_display']); ?>
								<?php endif; ?>
								
								<a href="controller.php?file=browse&amp;id=<?php echo $this->_tpl_vars['value2']['id']; ?>
" class="<?php echo $this->_tpl_vars['value2']['status']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['value2']['title'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</a><?php if ($this->_tpl_vars['cnt2'] < $this->_tpl_vars['min']): ?>,<?php endif; ?>
								<?php $this->assign('cnt2', $this->_tpl_vars['cnt2']+1); ?>
							<?php endforeach; endif; unset($_from); ?>
							</div>
						<?php endif; ?>
					<?php endif; ?>	
				</div></div>
				<?php if ($this->_tpl_vars['row'] < count($this->_tpl_vars['related_categories']) / 3): ?>
					<div class="divider clearfix" style="clear: left;"></div>
				<?php endif; ?>
				<?php $this->assign('row', $this->_tpl_vars['row']+1); ?>
			<?php else: ?>
				<div class="col"><div class="category">
					<a href="controller.php?file=browse&amp;id=<?php echo $this->_tpl_vars['value']['id']; ?>
" class="<?php echo $this->_tpl_vars['value']['status']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['value']['title'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</a>&nbsp;<?php if ($this->_tpl_vars['config']['num_listings_display']): ?>(<?php echo $this->_tpl_vars['value']['num_all_listings']; ?>
)<?php endif; ?>&nbsp;<a href="#" class="actions_rmv-related_<?php echo $this->_tpl_vars['value']['id']; ?>
"><img style="vertical-align: middle;" src="<?php echo smarty_function_print_img(array('fl' => "remove-grid-ico.png",'folder' => "icons/",'admin' => 'true'), $this);?>
" alt="<?php echo $this->_tpl_vars['esynI18N']['remove']; ?>
 <?php echo ((is_array($_tmp=$this->_tpl_vars['value']['title'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
"></a>
					<?php if ($this->_tpl_vars['config']['subcats_display']): ?>
						<?php if (isset ( $this->_tpl_vars['value']['subcategories'] ) && ! empty ( $this->_tpl_vars['value']['subcategories'] )): ?>
							<div class="subcategories">
							<?php $this->assign('cnt2', '1'); ?>
							<?php $_from = $this->_tpl_vars['value']['subcategories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key2'] => $this->_tpl_vars['value2']):
?>
								<?php if (count($this->_tpl_vars['value']['subcategories']) < $this->_tpl_vars['config']['subcats_display']): ?>
									<?php $this->assign('min', count($this->_tpl_vars['value']['subcategories'])); ?>
								<?php else: ?>
									<?php $this->assign('min', $this->_tpl_vars['config']['subcats_display']); ?>
								<?php endif; ?>
								
								<a href="controller.php?file=browse&amp;id=<?php echo $this->_tpl_vars['value2']['id']; ?>
" class="<?php echo $this->_tpl_vars['value2']['status']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['value2']['title'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</a><?php if ($this->_tpl_vars['cnt2'] < $this->_tpl_vars['min']): ?>,<?php endif; ?>
								<?php $this->assign('cnt2', $this->_tpl_vars['cnt2']+1); ?>
							<?php endforeach; endif; unset($_from); ?>
							</div>
						<?php endif; ?>
					<?php endif; ?>
				</div></div>
			<?php endif; ?>
		<?php endforeach; endif; unset($_from); ?>
	</div>

	<div style="clear:both;">&nbsp;</div>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>

<div id="box_listings" style="margin-top: 15px;"></div>

<div id="remove_reason" style="display: none;">
	<?php echo $this->_tpl_vars['esynI18N']['listing_remove_reason']; ?>
<br />
	<textarea cols="40" rows="5" name="body" id="remove_reason_text" class="common" style="width: 99%;"></textarea>
</div>

<?php echo smarty_function_include_file(array('js' => "js/intelli/intelli.grid, js/intelli/intelli.gmodel, js/ext/plugins/bettercombobox/betterComboBox, js/ext/plugins/rowexpander/rowExpander, js/ext/plugins/panelresizer/PanelResizer, js/ext/plugins/progressbarpager/ProgressBarPager, js/admin/browse, js/utils/dutil"), $this);?>


<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>