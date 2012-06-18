<?php /* Smarty version 2.6.26, created on 2011-12-13 04:25:57
         compiled from /home/vbezruchkin/www/v1700/templates/common/index.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'cat', '/home/vbezruchkin/www/v1700/templates/common/index.tpl', 5, false),array('modifier', 'escape', '/home/vbezruchkin/www/v1700/templates/common/index.tpl', 8, false),array('modifier', 'default', '/home/vbezruchkin/www/v1700/templates/common/index.tpl', 30, false),array('modifier', 'add_url_param', '/home/vbezruchkin/www/v1700/templates/common/index.tpl', 56, false),array('function', 'esynHooker', '/home/vbezruchkin/www/v1700/templates/common/index.tpl', 21, false),array('function', 'print_categories', '/home/vbezruchkin/www/v1700/templates/common/index.tpl', 25, false),array('function', 'navigation', '/home/vbezruchkin/www/v1700/templates/common/index.tpl', 39, false),array('function', 'include_file', '/home/vbezruchkin/www/v1700/templates/common/index.tpl', 75, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<h1><?php echo $this->_tpl_vars['header']; ?>
</h1>

<?php $this->assign('confirm_key', ((is_array($_tmp='confirm_')) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['category']['id']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['category']['id']))); ?>

<?php if ($this->_tpl_vars['category']['confirmation'] && ! isset ( $_COOKIE[$this->_tpl_vars['confirm_key']] )): ?>
	<?php echo ((is_array($_tmp=$this->_tpl_vars['category']['confirmation_text'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
&nbsp;
	<div style="text-align: center; margin-top: 20px;">
		<input type="button" class="button" name="confirm_answer" id="continue" value="<?php echo $this->_tpl_vars['lang']['yes']; ?>
" />
		<input type="button" class="button" name="confirm_answer" id="back" value="<?php echo $this->_tpl_vars['lang']['no']; ?>
" />
		<input type="hidden" name="category_id" id="category_id" value="<?php echo $this->_tpl_vars['category']['id']; ?>
" />
	</div>
<?php else: ?>
	<?php if ($this->_tpl_vars['category']['description']): ?>
		<div class="box">
			<?php echo $this->_tpl_vars['category']['description']; ?>

		</div>
	<?php endif; ?>
	
	<?php echo smarty_function_esynHooker(array('name' => 'tplFrontIndexBeforeCategories'), $this);?>


	<?php if ($this->_tpl_vars['categories']): ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-header.tpl", 'smarty_include_vars' => array('caption' => $this->_tpl_vars['lang']['categories'],'style' => 'fixed')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			<?php echo smarty_function_print_categories(array('aCategories' => $this->_tpl_vars['categories'],'aCols' => $this->_tpl_vars['category']['num_cols'],'aSubcategories' => $this->_tpl_vars['config']['subcats_display'],'display_type' => $this->_tpl_vars['config']['categories_display_type']), $this);?>

		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php endif; ?>

	<div id="centerBlocks" class="groupWrapper">
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "parse-blocks.tpl", 'smarty_include_vars' => array('pos' => ((is_array($_tmp=@$this->_tpl_vars['centerBlocks'])) ? $this->_run_mod_handler('default', true, $_tmp, null) : smarty_modifier_default($_tmp, null)))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</div>
	
	<?php echo smarty_function_esynHooker(array('name' => 'indexBeforeListings'), $this);?>


	<?php if ($this->_tpl_vars['listings']): ?>
		<!-- listings box start -->
		<div class="listings">
			<?php if ($this->_tpl_vars['config']['mod_rewrite']): ?><?php $this->assign('type', 2); ?><?php else: ?><?php $this->assign('type', 1); ?><?php endif; ?>
			<?php echo smarty_function_navigation(array('aTotal' => $this->_tpl_vars['total_listings'],'aTemplate' => $this->_tpl_vars['url'],'aItemsPerPage' => $this->_tpl_vars['config']['num_index_listings'],'aNumPageItems' => 5,'aTruncateParam' => $this->_tpl_vars['config']['use_html_path']), $this);?>


			<table cellspacing="0" cellpadding="0" width="100%">
			<?php $_from = $this->_tpl_vars['listings']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['listing']):
?>
				<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "listing-display.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			<?php endforeach; endif; unset($_from); ?>
			</table>
		</div>
		<!-- listings box end -->

		<!-- visitor sorting start -->
		<?php if ($this->_tpl_vars['config']['visitor_sorting']): ?>
			<div class="listing-sorting"><?php echo $this->_tpl_vars['lang']['sort_listings_by']; ?>

				<?php $_from = $this->_tpl_vars['sortings']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['order']):
?>
					<?php if ($this->_tpl_vars['order'] == $this->_tpl_vars['config']['listings_sorting']): ?>
						<?php echo $this->_tpl_vars['lang'][$this->_tpl_vars['order']]; ?>

					<?php else: ?>
						<a href="<?php echo ((is_array($_tmp=$_SERVER['REQUEST_URI'])) ? $this->_run_mod_handler('add_url_param', true, $_tmp, 'order', $this->_tpl_vars['order']) : smarty_modifier_add_url_param($_tmp, 'order', $this->_tpl_vars['order'])); ?>
" rel="nofollow"><?php echo $this->_tpl_vars['lang'][$this->_tpl_vars['order']]; ?>
</a>
					<?php endif; ?>
				<?php endforeach; endif; unset($_from); ?>&nbsp;&nbsp;

				<?php if ($this->_tpl_vars['config']['listings_sorting_type'] == 'ascending'): ?>
					<?php echo $this->_tpl_vars['lang']['ascending']; ?>
 | 
					<a href="<?php echo ((is_array($_tmp=$_SERVER['REQUEST_URI'])) ? $this->_run_mod_handler('add_url_param', true, $_tmp, 'order_type', 'descending') : smarty_modifier_add_url_param($_tmp, 'order_type', 'descending')); ?>
" rel="nofollow"><?php echo $this->_tpl_vars['lang']['descending']; ?>
</a>
				<?php else: ?>
					<a href="<?php echo ((is_array($_tmp=$_SERVER['REQUEST_URI'])) ? $this->_run_mod_handler('add_url_param', true, $_tmp, 'order_type', 'ascending') : smarty_modifier_add_url_param($_tmp, 'order_type', 'ascending')); ?>
" rel="nofollow"><?php echo $this->_tpl_vars['lang']['ascending']; ?>
</a> | 
					<?php echo $this->_tpl_vars['lang']['descending']; ?>

				<?php endif; ?>
			</div>
		<?php endif; ?>
		<!-- visitor sorting end -->
			
		<?php echo smarty_function_navigation(array('aTotal' => $this->_tpl_vars['total_listings'],'aTemplate' => $this->_tpl_vars['url'],'aItemsPerPage' => $this->_tpl_vars['config']['num_index_listings'],'aNumPageItems' => 5,'aTruncateParam' => $this->_tpl_vars['config']['use_html_path']), $this);?>


		<?php if ($this->_tpl_vars['esynAccountInfo']['id']): ?>
			<hr /><div class="waiting">&nbsp;</div><div class="admin-approve"> - <?php echo $this->_tpl_vars['lang']['listings_legend']; ?>
</div>
			<?php echo smarty_function_include_file(array('js' => "js/intelli/intelli.tree"), $this);?>

		<?php endif; ?>

		<?php echo smarty_function_include_file(array('js' => "js/frontend/listing-display"), $this);?>

	<?php endif; ?>

	<?php if (isset ( $this->_tpl_vars['related_categories'] ) && ! empty ( $this->_tpl_vars['related_categories'] )): ?>
		<!-- related categories box start -->
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-header.tpl", 'smarty_include_vars' => array('caption' => $this->_tpl_vars['lang']['related_categories'],'style' => 'fixed')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			<?php echo smarty_function_print_categories(array('aCategories' => $this->_tpl_vars['related_categories']), $this);?>

		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<!-- related categories box end -->
	<?php endif; ?>

	<?php if (isset ( $this->_tpl_vars['neighbour_categories'] ) && ! empty ( $this->_tpl_vars['neighbour_categories'] )): ?>
		<!-- neighbour categories box start -->
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-header.tpl", 'smarty_include_vars' => array('caption' => $this->_tpl_vars['lang']['neighbour_categories'],'style' => 'fixed')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
			<?php echo smarty_function_print_categories(array('aCategories' => $this->_tpl_vars['neighbour_categories']), $this);?>

		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<!-- neighbour categories box end -->
	<?php endif; ?>
<?php endif; ?>

<?php echo smarty_function_include_file(array('js' => "js/frontend/index"), $this);?>


<?php echo smarty_function_esynHooker(array('name' => 'indexBeforeFooter'), $this);?>


<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>