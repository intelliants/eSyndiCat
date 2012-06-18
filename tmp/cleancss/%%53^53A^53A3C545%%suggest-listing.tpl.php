<?php /* Smarty version 2.6.26, created on 2011-12-15 08:42:17
         compiled from /home/vbezruchkin/www/v1700/templates/common/suggest-listing.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'esynHooker', '/home/vbezruchkin/www/v1700/templates/common/suggest-listing.tpl', 22, false),array('function', 'include_file', '/home/vbezruchkin/www/v1700/templates/common/suggest-listing.tpl', 58, false),array('modifier', 'escape', '/home/vbezruchkin/www/v1700/templates/common/suggest-listing.tpl', 34, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "header.tpl", 'smarty_include_vars' => array('css' => "js/jquery/plugins/lightbox/css/jquery.lightbox, js/jquery/plugins/mcdropdown/jquery.mcdropdown")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<h1><?php echo $this->_tpl_vars['title']; ?>
</h1>
<div id="msg"></div>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "notification.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div class="box">
	<form action="<?php echo @ESYN_URL; ?>
suggest-listing.php" method="post" id="form_listing" enctype="multipart/form-data">
	<fieldset style="collapsible">
		<legend>
			<span id="categoryTitle">
				<strong><?php echo $this->_tpl_vars['category']['title']; ?>
</strong>
			</span> 
		</legend>

		<div id="treeContainer">
			<div id="tree" class="tree"></div>
		</div>
	</fieldset>

	<?php echo smarty_function_esynHooker(array('name' => 'editListingForm'), $this);?>


	<fieldset class="collapsible">
		<legend><strong><?php echo $this->_tpl_vars['lang']['fields']; ?>
</strong></legend>
		<div id="fields" class="fields"></div>
	</fieldset>

	<?php if ($this->_tpl_vars['config']['reciprocal_check']): ?>
		<div id="reciprocal">
			<fieldset class="collapsible">
				<legend><strong><?php echo $this->_tpl_vars['lang']['reciprocal']; ?>
</strong></legend>
				<?php echo $this->_tpl_vars['config']['reciprocal_label']; ?>
<br />
				<textarea cols="50" rows="2" readonly="readonly"><?php echo ((is_array($_tmp=$this->_tpl_vars['config']['reciprocal_code'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</textarea>
			</fieldset>
		</div>
	<?php endif; ?>

	<div id="gateways" style="display: none;">
		<fieldset class="collapsible">
			<legend><strong><?php echo $this->_tpl_vars['lang']['payment_gateway']; ?>
</strong></legend>
			<?php echo smarty_function_esynHooker(array('name' => 'paymentButtons'), $this);?>

		</fieldset>
	</div>

	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "captcha.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

	<div class="categories-tree">
		<input type="hidden" id="category_id" name="category_id" value="<?php echo $this->_tpl_vars['listing']['category_id']; ?>
" />
		<input type="hidden" name="listing_id" value="<?php echo $this->_tpl_vars['listing']['id']; ?>
" />
		<input type="submit" name="save_changes" value="<?php echo $this->_tpl_vars['lang']['submit']; ?>
" id="submit_btn" class="button" />
	</div>
	</form>
</div>

<?php echo smarty_function_esynHooker(array('name' => 'editListingBeforeIncludeJs'), $this);?>


<?php echo smarty_function_include_file(array('js' => "js/jquery/plugins/lightbox/jquery.lightbox, js/intelli/intelli.tree"), $this);?>

<?php echo smarty_function_include_file(array('js' => "js/intelli/intelli.deeplinks, js/intelli/intelli.fields, js/intelli/intelli.textcounter"), $this);?>

<?php echo smarty_function_include_file(array('js' => "js/jquery/plugins/mcdropdown/jquery.mcdropdown, js/jquery/plugins/jquery.tooltip, js/frontend/suggest-listing"), $this);?>

<?php echo smarty_function_include_file(array('js' => "js/jquery/plugins/jquery.form.ajaxLoader"), $this);?>


<?php echo smarty_function_esynHooker(array('name' => 'editListingBeforeFooter'), $this);?>


<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>