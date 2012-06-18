<?php /* Smarty version 2.6.26, created on 2011-12-13 04:36:38
         compiled from /home/vbezruchkin/www/v1700/templates/common/suggest-category.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', '/home/vbezruchkin/www/v1700/templates/common/suggest-category.tpl', 31, false),array('function', 'esynHooker', '/home/vbezruchkin/www/v1700/templates/common/suggest-category.tpl', 40, false),array('function', 'include_file', '/home/vbezruchkin/www/v1700/templates/common/suggest-category.tpl', 42, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "header.tpl", 'smarty_include_vars' => array('css' => "js/jquery/plugins/mcdropdown/jquery.mcdropdown")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<h1><?php echo $this->_tpl_vars['lang']['suggest_category']; ?>
</h1>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "notification.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div class="box">
	<p><?php echo $this->_tpl_vars['lang']['suggest_category_top1']; ?>
</p>

<form method="post" action="<?php echo @ESYN_URL; ?>
suggest-category.php?id=<?php echo $this->_tpl_vars['category']['id']; ?>
" style="margin-top: 8px;">

<fieldset style="collapsible">
	<legend>
		<span id="categoryTitle">
			<strong><?php echo $this->_tpl_vars['category']['title']; ?>
</strong>
		</span> 
		(<a href="#" onclick="return false;"><span id="changeLabel"><?php echo $this->_tpl_vars['lang']['change']; ?>
</span></a>)
	</legend>

	<div id="treeContainer" style="display:none;">
		<div id="tree" class="tree"></div>
	</div>
</fieldset>	

<input type="hidden" id="category_id" name="category_id" value="<?php echo $this->_tpl_vars['category']['id']; ?>
" />
<input type="hidden" id="category_title" name="category_title" value="<?php echo $this->_tpl_vars['category']['title']; ?>
" />

<br />

<strong><?php echo $this->_tpl_vars['lang']['category_title']; ?>
:</strong><br />
<input type="text" class="text" name="title" id="title" size="30" value="<?php if (isset ( $this->_tpl_vars['cat_title'] )): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['cat_title'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php endif; ?>" /><br />
	
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "captcha.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<input type="submit" name="add_category" value="<?php echo $this->_tpl_vars['lang']['suggest_category']; ?>
" style="margin-top: 10px;" class="button" />
</form>

</div>

<?php echo smarty_function_esynHooker(array('name' => 'suggestCategoryBeforeIncludeJs'), $this);?>


<?php echo smarty_function_include_file(array('js' => "js/intelli/intelli.tree, js/frontend/suggest-category, js/jquery/plugins/jquery.dimensions, js/jquery/plugins/jquery.bgiframe, js/jquery/plugins/mcdropdown/jquery.mcdropdown"), $this);?>


<?php echo smarty_function_esynHooker(array('name' => 'suggestCategoryBeforeFooter'), $this);?>


<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>