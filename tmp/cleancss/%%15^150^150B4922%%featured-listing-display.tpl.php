<?php /* Smarty version 2.6.26, created on 2011-12-15 09:36:39
         compiled from /home/vbezruchkin/www/v1700/templates/common/featured-listing-display.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'print_listing_url', '/home/vbezruchkin/www/v1700/templates/common/featured-listing-display.tpl', 12, false),array('function', 'print_img', '/home/vbezruchkin/www/v1700/templates/common/featured-listing-display.tpl', 15, false),array('function', 'print_pagerank', '/home/vbezruchkin/www/v1700/templates/common/featured-listing-display.tpl', 29, false),array('function', 'esynHooker', '/home/vbezruchkin/www/v1700/templates/common/featured-listing-display.tpl', 32, false),array('modifier', 'truncate', '/home/vbezruchkin/www/v1700/templates/common/featured-listing-display.tpl', 23, false),array('modifier', 'date_format', '/home/vbezruchkin/www/v1700/templates/common/featured-listing-display.tpl', 37, false),array('modifier', 'cat', '/home/vbezruchkin/www/v1700/templates/common/featured-listing-display.tpl', 63, false),array('modifier', 'escape', '/home/vbezruchkin/www/v1700/templates/common/featured-listing-display.tpl', 63, false),)), $this); ?>
<tr>
	<td id="tdlisting<?php echo $this->_tpl_vars['listing']['id']; ?>
">

	<div class="listing featured <?php echo $this->_tpl_vars['listing']['status']; ?>
">

	<?php if ($this->_tpl_vars['config']['thumbshot']): ?>
		<div class="preview"><img src="http://open.thumbshots.org/image.pxf?url=<?php echo $this->_tpl_vars['listing']['url']; ?>
" alt="<?php echo $this->_tpl_vars['listing']['url']; ?>
" /></div>
	<?php endif; ?>
	<div class="badge"><div class="type featured"><?php echo $this->_tpl_vars['lang']['featured']; ?>
</div></div>

	<div class="title">
		<a href="<?php echo esynLayout::printListingUrl(array('listing' => $this->_tpl_vars['listing']), $this);?>
" <?php if ($this->_tpl_vars['config']['new_window']): ?>target="_blank"<?php endif; ?> class="countable listings title" id="lnk_<?php echo $this->_tpl_vars['listing']['id']; ?>
"><?php echo $this->_tpl_vars['listing']['title']; ?>
</a>
		
		<?php if (isset ( $this->_tpl_vars['listing']['interval'] ) && ( 1 == $this->_tpl_vars['listing']['interval'] )): ?>
			<?php echo smarty_function_print_img(array('fl' => "new.gif",'alt' => $this->_tpl_vars['lang']['new'],'full' => true), $this);?>

		<?php endif; ?>

		<?php if (isset ( $this->_tpl_vars['listing']['rank'] )): ?>
			<?php unset($this->_sections['star']);
$this->_sections['star']['name'] = 'star';
$this->_sections['star']['loop'] = is_array($_loop=$this->_tpl_vars['listing']['rank']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['star']['show'] = true;
$this->_sections['star']['max'] = $this->_sections['star']['loop'];
$this->_sections['star']['step'] = 1;
$this->_sections['star']['start'] = $this->_sections['star']['step'] > 0 ? 0 : $this->_sections['star']['loop']-1;
if ($this->_sections['star']['show']) {
    $this->_sections['star']['total'] = $this->_sections['star']['loop'];
    if ($this->_sections['star']['total'] == 0)
        $this->_sections['star']['show'] = false;
} else
    $this->_sections['star']['total'] = 0;
if ($this->_sections['star']['show']):

            for ($this->_sections['star']['index'] = $this->_sections['star']['start'], $this->_sections['star']['iteration'] = 1;
                 $this->_sections['star']['iteration'] <= $this->_sections['star']['total'];
                 $this->_sections['star']['index'] += $this->_sections['star']['step'], $this->_sections['star']['iteration']++):
$this->_sections['star']['rownum'] = $this->_sections['star']['iteration'];
$this->_sections['star']['index_prev'] = $this->_sections['star']['index'] - $this->_sections['star']['step'];
$this->_sections['star']['index_next'] = $this->_sections['star']['index'] + $this->_sections['star']['step'];
$this->_sections['star']['first']      = ($this->_sections['star']['iteration'] == 1);
$this->_sections['star']['last']       = ($this->_sections['star']['iteration'] == $this->_sections['star']['total']);
?><?php echo smarty_function_print_img(array('fl' => "star.png",'full' => true), $this);?>
<?php endfor; endif; ?>
		<?php endif; ?>
	</div>

	<div class="description"><?php echo ((is_array($_tmp=$this->_tpl_vars['listing']['description'])) ? $this->_run_mod_handler('truncate', true, $_tmp, '300') : smarty_modifier_truncate($_tmp, '300')); ?>
</div>

	<div class="url"><?php echo $this->_tpl_vars['listing']['url']; ?>
</div>
	<input type="hidden" value="<?php if (isset ( $this->_tpl_vars['instead_thumbnail'] ) && ( $this->_tpl_vars['listing'][$this->_tpl_vars['instead_thumbnail']] != '' )): ?><?php echo @ESYN_URL; ?>
uploads/<?php echo $this->_tpl_vars['listing'][$this->_tpl_vars['instead_thumbnail']]; ?>
<?php endif; ?>" />

	<?php if ($this->_tpl_vars['config']['pagerank']): ?>
		<?php echo smarty_function_print_pagerank(array('pagerank' => $this->_tpl_vars['listing']['pagerank'],'label' => true), $this);?>

	<?php endif; ?>

	<?php echo smarty_function_esynHooker(array('name' => 'listingDisplayBeforeStats'), $this);?>


	<div class="stat">(<?php echo $this->_tpl_vars['lang']['clicks']; ?>
: <?php echo $this->_tpl_vars['listing']['clicks']; ?>
;
		<?php echo smarty_function_esynHooker(array('name' => 'listingDisplayFieldsArea'), $this);?>


		<?php echo $this->_tpl_vars['lang']['listing_added']; ?>
: <?php echo ((is_array($_tmp=$this->_tpl_vars['listing']['date'])) ? $this->_run_mod_handler('date_format', true, $_tmp, $this->_tpl_vars['config']['date_format']) : smarty_modifier_date_format($_tmp, $this->_tpl_vars['config']['date_format'])); ?>
)

		<a href="<?php echo esynLayout::printListingUrl(array('listing' => $this->_tpl_vars['listing'],'details' => true), $this);?>
"><span class="info16" title="<?php echo $this->_tpl_vars['lang']['listing_details']; ?>
">&nbsp;</span></a>

		<?php if ($this->_tpl_vars['esynAccountInfo']['id'] == $this->_tpl_vars['listing']['account_id']): ?>
			<a href="<?php echo @ESYN_URL; ?>
suggest-listing.php?edit=<?php echo $this->_tpl_vars['listing']['id']; ?>
"><?php echo smarty_function_print_img(array('fl' => "edit_16.png",'full' => true,'alt' => $this->_tpl_vars['lang']['edit_listing'],'title' => $this->_tpl_vars['lang']['edit_listing']), $this);?>
</a>
		<?php endif; ?>

		<?php if ($this->_tpl_vars['config']['broken_listings_report'] && ! ( $this->_tpl_vars['esynAccountInfo']['id'] == $this->_tpl_vars['listing']['account_id'] )): ?>
			<a href="#" class="actions_broken_<?php echo $this->_tpl_vars['listing']['id']; ?>
" rel="nofollow"><span class="report16" title="<?php echo $this->_tpl_vars['lang']['report_broken_listing']; ?>
">&nbsp;</span></a>
		<?php endif; ?>

		<?php if ($this->_tpl_vars['esynAccountInfo']): ?>
			<?php if ($this->_tpl_vars['esynAccountInfo']['id'] != $this->_tpl_vars['listing']['account_id']): ?>
				<span id="af_<?php echo $this->_tpl_vars['listing']['id']; ?>
">
				<?php if (isset ( $this->_tpl_vars['listing']['favorite'] ) && ! $this->_tpl_vars['listing']['favorite']): ?>
					<a href="#" class="actions_add-favorite_<?php echo $this->_tpl_vars['listing']['id']; ?>
_<?php echo $this->_tpl_vars['esynAccountInfo']['id']; ?>
" rel="nofollow"><?php echo smarty_function_print_img(array('fl' => "favorites-add_16.png",'full' => true,'alt' => $this->_tpl_vars['lang']['add_to_favorites'],'title' => $this->_tpl_vars['lang']['add_to_favorites']), $this);?>
</a>
				<?php else: ?>
					<a href="#" class="actions_remove-favorite_<?php echo $this->_tpl_vars['listing']['id']; ?>
_<?php echo $this->_tpl_vars['esynAccountInfo']['id']; ?>
" rel="nofollow"><?php echo smarty_function_print_img(array('fl' => "favorites-remove_16.png",'full' => true,'alt' => $this->_tpl_vars['lang']['remove_from_favorites'],'title' => $this->_tpl_vars['lang']['remove_from_favorites']), $this);?>
</a>
				<?php endif; ?>
				</span>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ($this->_tpl_vars['esynAccountInfo']['id'] == $this->_tpl_vars['listing']['account_id']): ?>
			<a href="#" class="actions_move_<?php echo $this->_tpl_vars['listing']['id']; ?>
_<?php echo $this->_tpl_vars['listing']['category_id']; ?>
"><?php echo smarty_function_print_img(array('fl' => "move_16.png",'full' => true,'alt' => $this->_tpl_vars['lang']['move_listing'],'title' => $this->_tpl_vars['lang']['move_listing']), $this);?>
</a><br />
			<?php if (@ESYN_REALM == 'account_listings'): ?><?php echo $this->_tpl_vars['lang']['category']; ?>
: <a href="<?php echo @ESYN_URL; ?>
<?php if ($this->_tpl_vars['config']['mod_rewrite']): ?><?php if ($this->_tpl_vars['config']['use_html_path']): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['listing']['path'])) ? $this->_run_mod_handler('cat', true, $_tmp, ".html") : smarty_modifier_cat($_tmp, ".html")); ?>
<?php else: ?><?php echo $this->_tpl_vars['listing']['path']; ?>
<?php endif; ?><?php else: ?>index.php?category=<?php echo $this->_tpl_vars['listing']['category_id']; ?>
<?php endif; ?>"><?php echo ((is_array($_tmp=$this->_tpl_vars['listing']['category_title'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</a><br /><?php endif; ?>
		<?php endif; ?>

		<?php echo smarty_function_esynHooker(array('name' => 'listingDisplayLinksArea'), $this);?>


	</div>

	</td>
</tr>