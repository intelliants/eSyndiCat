<?php /* Smarty version 2.6.26, created on 2011-12-15 11:06:35
         compiled from /home/vbezruchkin/www/v1700/templates/common/search.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', '/home/vbezruchkin/www/v1700/templates/common/search.tpl', 11, false),array('modifier', 'cat', '/home/vbezruchkin/www/v1700/templates/common/search.tpl', 29, false),array('modifier', 'replace', '/home/vbezruchkin/www/v1700/templates/common/search.tpl', 56, false),array('function', 'print_categories', '/home/vbezruchkin/www/v1700/templates/common/search.tpl', 30, false),array('function', 'esynHooker', '/home/vbezruchkin/www/v1700/templates/common/search.tpl', 52, false),array('function', 'include_file', '/home/vbezruchkin/www/v1700/templates/common/search.tpl', 59, false),array('function', 'navigation', '/home/vbezruchkin/www/v1700/templates/common/search.tpl', 65, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<h1><?php echo $this->_tpl_vars['lang']['search']; ?>
</h1>

<div class="box">
	<form action="<?php echo @ESYN_URL; ?>
search.php" method="get">
		<table style="width:auto;">
			<tr>
				<td><?php echo $this->_tpl_vars['lang']['search']; ?>
:</td>
				<td>
					<input type="text" class="text" name="what" id="what" size="22" value="<?php if (isset ( $_GET['what'] )): ?><?php echo ((is_array($_tmp=$_GET['what'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php endif; ?>" />
				</td>
				<td>
					<input type="submit" value="<?php echo $this->_tpl_vars['lang']['search']; ?>
" class="button" />
				</td>
			</tr>
			<tr>
				<td colspan="3" style="text-align: center;">
					<input type="radio" name="type" value="1" id="any" <?php if (isset ( $_GET['type'] ) && $_GET['type'] == '1'): ?>checked="checked"<?php elseif (! isset ( $_GET['type'] )): ?>checked="checked"<?php endif; ?>/><label for="any"><?php echo $this->_tpl_vars['lang']['any_word']; ?>
</label> |
					<input type="radio" name="type" value="2" id="all" <?php if (isset ( $_GET['type'] ) && $_GET['type'] == '2'): ?>checked="checked"<?php endif; ?>/><label for="all"><?php echo $this->_tpl_vars['lang']['all_words']; ?>
</label> |
					<input type="radio" name="type" value="3" id="exact" <?php if (isset ( $_GET['type'] ) && $_GET['type'] == '3'): ?>checked="checked"<?php endif; ?>/><label for="exact"><?php echo $this->_tpl_vars['lang']['exact_match']; ?>
</label>
				</td>
			</tr>
		</table>
	</form>
</div>

<?php if (isset ( $this->_tpl_vars['categories'] ) && ! empty ( $this->_tpl_vars['categories'] )): ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-header.tpl", 'smarty_include_vars' => array('caption' => ((is_array($_tmp=$this->_tpl_vars['lang']['categories_found'])) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['total_categories']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['total_categories'])),'style' => 'fixed')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php echo smarty_function_print_categories(array('aCategories' => $this->_tpl_vars['categories'],'aCols' => 1,'aSubcategories' => false,'display_type' => 'vertical','path_title' => true,'truncate_path_title' => 100), $this);?>

		<?php if ($this->_tpl_vars['total_categories'] > $this->_tpl_vars['config']['num_cats_for_search'] && ! isset ( $_GET['cats'] ) && ! isset ( $_POST['cats_only'] )): ?>
			<?php if (isset ( $_GET['adv'] )): ?>
				<form action="<?php echo @ESYN_URL; ?>
search.php?adv" method="post" id="adv_cat_search_form">
					<?php if (isset ( $_POST['queryFilterCat'] )): ?>
						<?php $_from = $_POST['queryFilterCat']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['filter']):
?>
							<input type="hidden" name="queryFilterCat[]" value="<?php echo $this->_tpl_vars['filter']; ?>
" />
						<?php endforeach; endif; unset($_from); ?>
						<input type="hidden" name="cats_only" value="1" />
						<input type="hidden" name="searchquery" value="<?php echo $_POST['searchquery']; ?>
"/>
						<input type="hidden" name="match" value="<?php echo $_POST['match']; ?>
" />
						<input type="hidden" name="_settings[sort]" value="<?php echo $_POST['_settings']['sort']; ?>
" />
					<?php endif; ?>
				</form> 
				<div><a href="#" id="adv_cat_search_submit"><?php echo $this->_tpl_vars['lang']['more']; ?>
</a></div>
			<?php else: ?>
				<div><a href="<?php echo @ESYN_URL; ?>
search.php?what=<?php echo $_GET['what']; ?>
&cats=true"><?php echo $this->_tpl_vars['lang']['more']; ?>
</a></div>
			<?php endif; ?>
		<?php endif; ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>

<?php echo smarty_function_esynHooker(array('name' => 'tplFrontSearchBeforeListings'), $this);?>


<?php if (( isset ( $this->_tpl_vars['listings'] ) && ! empty ( $this->_tpl_vars['listings'] ) || isset ( $this->_tpl_vars['categories'] ) && ! empty ( $this->_tpl_vars['categories'] ) ) && ( isset ( $_GET['what'] ) || isset ( $_POST['searchquery'] ) )): ?>
	<script type="text/javascript">
		var pWhat = '<?php if (isset ( $_POST['searchquery'] )): ?><?php echo ((is_array($_tmp=$_POST['searchquery'])) ? $this->_run_mod_handler('replace', true, $_tmp, "'", "") : smarty_modifier_replace($_tmp, "'", "")); ?>
<?php else: ?><?php echo ((is_array($_tmp=$_GET['what'])) ? $this->_run_mod_handler('replace', true, $_tmp, "'", "") : smarty_modifier_replace($_tmp, "'", "")); ?>
<?php endif; ?>';
	</script>

	<?php echo smarty_function_include_file(array('js' => "js/frontend/search_highlight"), $this);?>

<?php endif; ?>

<?php if (isset ( $this->_tpl_vars['listings'] ) && ! empty ( $this->_tpl_vars['listings'] )): ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-header.tpl", 'smarty_include_vars' => array('caption' => ((is_array($_tmp=$this->_tpl_vars['lang']['listings_found'])) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['total_listings']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['total_listings'])),'style' => 'fixed')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<div class="listings">
			<?php echo smarty_function_navigation(array('aTotal' => $this->_tpl_vars['total_listings'],'aTemplate' => $this->_tpl_vars['url'],'aItemsPerPage' => $this->_tpl_vars['config']['num_index_listings'],'aNumPageItems' => 5,'aTruncateParam' => 1), $this);?>


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

			<?php echo smarty_function_navigation(array('aTotal' => $this->_tpl_vars['total_listings'],'aTemplate' => $this->_tpl_vars['url'],'aItemsPerPage' => $this->_tpl_vars['config']['num_index_listings'],'aNumPageItems' => 5,'aTruncateParam' => 1), $this);?>

		</div>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	
	<?php echo smarty_function_include_file(array('js' => "js/frontend/listing-display"), $this);?>

<?php elseif (empty ( $this->_tpl_vars['listings'] ) && ( $this->_tpl_vars['adv'] && ! $this->_tpl_vars['showForm'] ) || isset ( $_GET['what'] ) && ! isset ( $_GET['cats'] )): ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-header.tpl", 'smarty_include_vars' => array('caption' => ((is_array($_tmp=$this->_tpl_vars['lang']['listings_found'])) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['total_listings']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['total_listings'])),'style' => 'fixed')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php echo $this->_tpl_vars['lang']['not_found_listings']; ?>

	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>

<?php echo smarty_function_esynHooker(array('name' => 'searchBeforeFooter'), $this);?>


<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>