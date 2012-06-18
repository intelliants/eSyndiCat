<?php /* Smarty version 2.6.26, created on 2011-12-13 04:35:51
         compiled from /home/vbezruchkin/www/v1700/templates/common/view-listing.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', '/home/vbezruchkin/www/v1700/templates/common/view-listing.tpl', 3, false),array('modifier', 'lower', '/home/vbezruchkin/www/v1700/templates/common/view-listing.tpl', 25, false),array('modifier', 'date_format', '/home/vbezruchkin/www/v1700/templates/common/view-listing.tpl', 60, false),array('modifier', 'cat', '/home/vbezruchkin/www/v1700/templates/common/view-listing.tpl', 89, false),array('modifier', 'explode', '/home/vbezruchkin/www/v1700/templates/common/view-listing.tpl', 97, false),array('modifier', 'file_exists', '/home/vbezruchkin/www/v1700/templates/common/view-listing.tpl', 110, false),array('function', 'print_category_url', '/home/vbezruchkin/www/v1700/templates/common/view-listing.tpl', 32, false),array('function', 'print_pagerank', '/home/vbezruchkin/www/v1700/templates/common/view-listing.tpl', 67, false),array('function', 'esynHooker', '/home/vbezruchkin/www/v1700/templates/common/view-listing.tpl', 72, false),array('function', 'print_img', '/home/vbezruchkin/www/v1700/templates/common/view-listing.tpl', 111, false),array('function', 'include_file', '/home/vbezruchkin/www/v1700/templates/common/view-listing.tpl', 148, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "header.tpl", 'smarty_include_vars' => array('css' => "js/jquery/plugins/prettyphoto/css/prettyPhoto")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<h1><?php echo ((is_array($_tmp=$this->_tpl_vars['listing']['title'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</h1>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "notification.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div class="box">
	<table cellpadding="2" cellspacing="0" width="100%">
	<tr>
		<?php if ($this->_tpl_vars['config']['thumbshot']): ?>
			<td valign="top" style="padding-right: 5px; width: 125px;">
				<div class="preview">
				<?php if (isset ( $this->_tpl_vars['listing'][$this->_tpl_vars['instead_thumbnail']] ) && $this->_tpl_vars['listing'][$this->_tpl_vars['instead_thumbnail']] != ''): ?>
					<img src="<?php echo @ESYN_URL; ?>
uploads/<?php echo $this->_tpl_vars['listing'][$this->_tpl_vars['instead_thumbnail']]; ?>
" />
				<?php else: ?>
					<img src="http://open.thumbshots.org/image.pxf?url=<?php echo $this->_tpl_vars['listing']['url']; ?>
" alt="<?php echo $this->_tpl_vars['listing']['url']; ?>
" />
				<?php endif; ?>
			</div>
			</td>
		<?php endif; ?>
		<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td style="width: 7.8em;"><strong><?php echo $this->_tpl_vars['lang']['title']; ?>
:</strong></td>
				<td><a href="<?php echo ((is_array($_tmp=$this->_tpl_vars['listing']['url'])) ? $this->_run_mod_handler('lower', true, $_tmp) : smarty_modifier_lower($_tmp)); ?>
" id="l<?php echo $this->_tpl_vars['listing']['id']; ?>
" <?php if ($this->_tpl_vars['config']['new_window']): ?>target="_blank"<?php endif; ?>><?php echo $this->_tpl_vars['listing']['title']; ?>
</a></td>
			</tr>
			<tr>
				<td><strong><?php echo $this->_tpl_vars['lang']['category']; ?>
:</strong></td>
				<td>
					<?php if (is_array ( $this->_tpl_vars['category']['path'] )): ?>
						<?php $_from = $this->_tpl_vars['category']['path']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['categpath'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['categpath']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['cat']):
        $this->_foreach['categpath']['iteration']++;
?>
							<a href="<?php echo esynLayout::printCategoryUrl(array('cat' => $this->_tpl_vars['cat']), $this);?>
"><?php echo $this->_tpl_vars['cat']['title']; ?>
</a>
							<?php if (! ($this->_foreach['categpath']['iteration'] == $this->_foreach['categpath']['total'])): ?> / <?php endif; ?>
						<?php endforeach; endif; unset($_from); ?>
					<?php else: ?>
						<a href="<?php echo esynLayout::printCategoryUrl(array('cat' => $this->_tpl_vars['category']), $this);?>
"><?php echo $this->_tpl_vars['category']['title']; ?>
</a>
					<?php endif; ?>
				</td>
			</tr>

			<!-- Display crossed categories modification -->
			<?php if (isset ( $this->_tpl_vars['crossed_categories'] ) && ! empty ( $this->_tpl_vars['crossed_categories'] )): ?>
				<tr>
					<td><strong><?php echo $this->_tpl_vars['lang']['crossed_to']; ?>
:</strong></td>
					<td>
						<?php $_from = $this->_tpl_vars['crossed_categories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['crossed_category'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['crossed_category']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['crossed_category']):
        $this->_foreach['crossed_category']['iteration']++;
?>
							<a href="<?php echo esynLayout::printCategoryUrl(array('cat' => $this->_tpl_vars['crossed_category']), $this);?>
"><?php echo $this->_tpl_vars['crossed_category']['title']; ?>
</a>
							<?php if (! ($this->_foreach['crossed_category']['iteration'] == $this->_foreach['crossed_category']['total'])): ?>,<?php endif; ?>
						<?php endforeach; endif; unset($_from); ?>
					</td>
				</tr>
			<?php endif; ?>

			<tr>
				<td><strong><?php echo $this->_tpl_vars['lang']['clicks']; ?>
:</strong></td>
				<td><?php echo $this->_tpl_vars['listing']['clicks']; ?>
</td>
			</tr>
			<tr>
				<td><strong><?php echo $this->_tpl_vars['lang']['listing_added']; ?>
:</strong></td>
				<td><?php echo ((is_array($_tmp=$this->_tpl_vars['listing']['date'])) ? $this->_run_mod_handler('date_format', true, $_tmp, $this->_tpl_vars['config']['date_format']) : smarty_modifier_date_format($_tmp, $this->_tpl_vars['config']['date_format'])); ?>
</td>
			</tr>
			
			<?php if ($this->_tpl_vars['config']['pagerank']): ?>
			<tr>
				<td><strong><?php echo $this->_tpl_vars['lang']['pagerank']; ?>
:</strong></td>
				<td>
					<?php echo smarty_function_print_pagerank(array('pagerank' => $this->_tpl_vars['listing']['pagerank']), $this);?>

				</td>
			</tr>
			<?php endif; ?>

			<?php echo smarty_function_esynHooker(array('name' => 'viewListingAfterMainFieldsDisplay'), $this);?>


			</table>
		</td>
	</tr>
	</table>

	<?php echo $this->_tpl_vars['listing']['description']; ?>


	<?php if ($this->_tpl_vars['fields']): ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-header.tpl", 'smarty_include_vars' => array('caption' => $this->_tpl_vars['lang']['fields'],'style' => 'fixed')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

		<?php echo smarty_function_esynHooker(array('name' => 'viewListingBeforeFieldsDisplay'), $this);?>


		<table cellpadding="2" cellspacing="0" width="100%">
		<?php $_from = $this->_tpl_vars['fields']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['field']):
?>
			<?php $this->assign('key', $this->_tpl_vars['field']['name']); ?>
			<?php $this->assign('field_name', ((is_array($_tmp='field_')) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['field']['name']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['field']['name']))); ?>
			<?php if ($this->_tpl_vars['listing'][$this->_tpl_vars['key']] || ( $this->_tpl_vars['listing'][$this->_tpl_vars['key']] == '0' )): ?>
			<tr>
				<td style="width: 20%;"><strong><?php echo $this->_tpl_vars['lang'][$this->_tpl_vars['field_name']]; ?>
:</strong></td>
				<td>
					<?php if (( $this->_tpl_vars['field']['type'] == 'text' ) || ( $this->_tpl_vars['field']['type'] == 'textarea' ) || ( $this->_tpl_vars['field']['type'] == 'number' )): ?>
						<?php echo $this->_tpl_vars['listing'][$this->_tpl_vars['key']]; ?>

					<?php elseif ($this->_tpl_vars['field']['type'] == 'checkbox'): ?>
						<?php $this->assign('values', ((is_array($_tmp=',')) ? $this->_run_mod_handler('explode', true, $_tmp, $this->_tpl_vars['listing'][$this->_tpl_vars['key']]) : explode($_tmp, $this->_tpl_vars['listing'][$this->_tpl_vars['key']]))); ?> 
						<?php if ($this->_tpl_vars['values']): ?>
							<?php $_from = $this->_tpl_vars['values']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['checkbox_iter'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['checkbox_iter']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['field_val']):
        $this->_foreach['checkbox_iter']['iteration']++;
?>
								<?php $this->assign('lang_key', ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp='field_')) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['field']['name']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['field']['name'])))) ? $this->_run_mod_handler('cat', true, $_tmp, '_') : smarty_modifier_cat($_tmp, '_')))) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['field_val']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['field_val']))); ?>
								<?php echo $this->_tpl_vars['lang'][$this->_tpl_vars['lang_key']]; ?>
<?php if (! ($this->_foreach['checkbox_iter']['iteration'] == $this->_foreach['checkbox_iter']['total'])): ?>,&nbsp;<?php endif; ?>
							<?php endforeach; endif; unset($_from); ?>
						<?php endif; ?>
					<?php elseif ($this->_tpl_vars['field']['type'] == 'storage'): ?>
						<a href="<?php echo @ESYN_URL; ?>
uploads/<?php echo $this->_tpl_vars['listing'][$this->_tpl_vars['key']]; ?>
"><?php echo $this->_tpl_vars['lang']['download']; ?>
</a>
					<?php elseif ($this->_tpl_vars['field']['type'] == 'image'): ?>
						<?php $this->assign('image_name', ((is_array($_tmp='small_')) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['listing'][$this->_tpl_vars['key']]) : smarty_modifier_cat($_tmp, $this->_tpl_vars['listing'][$this->_tpl_vars['key']]))); ?>
						<?php $this->assign('image_path', ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=@ESYN_HOME)) ? $this->_run_mod_handler('cat', true, $_tmp, 'uploads') : smarty_modifier_cat($_tmp, 'uploads')))) ? $this->_run_mod_handler('cat', true, $_tmp, @ESYN_DS) : smarty_modifier_cat($_tmp, @ESYN_DS)))) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['image_name']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['image_name']))); ?>

						<?php if (((is_array($_tmp=$this->_tpl_vars['image_path'])) ? $this->_run_mod_handler('file_exists', true, $_tmp) : file_exists($_tmp))): ?>
							<a href="<?php echo @ESYN_URL; ?>
uploads/<?php echo $this->_tpl_vars['listing'][$this->_tpl_vars['key']]; ?>
" target="_blank" rel="prettyPhoto"><?php echo smarty_function_print_img(array('ups' => true,'full' => true,'fl' => $this->_tpl_vars['image_name'],'alt' => $this->_tpl_vars['listing'][$this->_tpl_vars['key']]), $this);?>
</a>
						<?php else: ?>
							<a href="<?php echo @ESYN_URL; ?>
uploads/<?php echo $this->_tpl_vars['listing'][$this->_tpl_vars['key']]; ?>
" target="_blank" rel="prettyPhoto"><?php echo smarty_function_print_img(array('ups' => true,'full' => true,'fl' => $this->_tpl_vars['listing'][$this->_tpl_vars['key']],'alt' => $this->_tpl_vars['listing'][$this->_tpl_vars['key']]), $this);?>
</a>
						<?php endif; ?>
					<?php elseif ($this->_tpl_vars['field']['type'] == 'pictures'): ?>
						<?php $this->assign('images', ((is_array($_tmp=",")) ? $this->_run_mod_handler('explode', true, $_tmp, $this->_tpl_vars['listing'][$this->_tpl_vars['key']]) : explode($_tmp, $this->_tpl_vars['listing'][$this->_tpl_vars['key']]))); ?> 

						<?php $_from = $this->_tpl_vars['images']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['image']):
?>
							<?php $this->assign('image_name', ((is_array($_tmp='small_')) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['image']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['image']))); ?>
							<?php $this->assign('image_path', ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=@ESYN_HOME)) ? $this->_run_mod_handler('cat', true, $_tmp, 'uploads') : smarty_modifier_cat($_tmp, 'uploads')))) ? $this->_run_mod_handler('cat', true, $_tmp, @ESYN_DS) : smarty_modifier_cat($_tmp, @ESYN_DS)))) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['image_name']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['image_name']))); ?>

							<?php if (((is_array($_tmp=$this->_tpl_vars['image_path'])) ? $this->_run_mod_handler('file_exists', true, $_tmp) : file_exists($_tmp))): ?>
								<a href="<?php echo @ESYN_URL; ?>
uploads/<?php echo $this->_tpl_vars['image']; ?>
" rel="prettyPhoto[gal]"><?php echo smarty_function_print_img(array('ups' => true,'full' => true,'fl' => $this->_tpl_vars['image_name'],'alt' => $this->_tpl_vars['image']), $this);?>
</a>
							<?php else: ?>
								<a href="<?php echo @ESYN_URL; ?>
uploads/<?php echo $this->_tpl_vars['image']; ?>
" rel="prettyPhoto[gal]"><?php echo smarty_function_print_img(array('ups' => true,'full' => true,'fl' => $this->_tpl_vars['image'],'alt' => $this->_tpl_vars['image']), $this);?>
</a>
							<?php endif; ?>
						<?php endforeach; endif; unset($_from); ?>
					<?php elseif ($this->_tpl_vars['field']['type'] == 'combo'): ?>
						<?php $this->assign('field_combo', ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp='field_')) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['field']['name']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['field']['name'])))) ? $this->_run_mod_handler('cat', true, $_tmp, '_') : smarty_modifier_cat($_tmp, '_')))) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['listing'][$this->_tpl_vars['key']]) : smarty_modifier_cat($_tmp, $this->_tpl_vars['listing'][$this->_tpl_vars['key']]))); ?>
						<?php echo $this->_tpl_vars['lang'][$this->_tpl_vars['field_combo']]; ?>

					<?php elseif ($this->_tpl_vars['field']['type'] == 'radio'): ?>
						<?php $this->assign('field_radio', ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp='field_')) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['field']['name']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['field']['name'])))) ? $this->_run_mod_handler('cat', true, $_tmp, '_') : smarty_modifier_cat($_tmp, '_')))) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['listing'][$this->_tpl_vars['key']]) : smarty_modifier_cat($_tmp, $this->_tpl_vars['listing'][$this->_tpl_vars['key']]))); ?>
						<?php echo $this->_tpl_vars['lang'][$this->_tpl_vars['field_radio']]; ?>

					<?php endif; ?>
				</td>
			</tr>
			<?php endif; ?>
		<?php endforeach; endif; unset($_from); ?>
		</table>

		<?php echo smarty_function_esynHooker(array('name' => 'viewListingAfterFieldsDisplay'), $this);?>


		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php endif; ?>

	<?php echo smarty_function_esynHooker(array('name' => 'tplFrontViewListingBeforeDeepLinks'), $this);?>

</div>
<?php echo smarty_function_include_file(array('js' => "js/jquery/plugins/prettyphoto/jquery.prettyPhoto, js/frontend/view-listing"), $this);?>


<?php echo smarty_function_esynHooker(array('name' => 'viewListingBeforeFooter'), $this);?>


<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>