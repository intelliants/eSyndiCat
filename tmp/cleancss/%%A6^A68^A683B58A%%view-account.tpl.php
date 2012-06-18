<?php /* Smarty version 2.6.26, created on 2011-12-15 09:35:21
         compiled from /home/vbezruchkin/www/v1700/templates/common/view-account.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'esynHooker', '/home/vbezruchkin/www/v1700/templates/common/view-account.tpl', 5, false),array('function', 'navigation', '/home/vbezruchkin/www/v1700/templates/common/view-account.tpl', 11, false),array('function', 'include_file', '/home/vbezruchkin/www/v1700/templates/common/view-account.tpl', 23, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<h1><?php echo $this->_tpl_vars['title']; ?>
</h1>

<?php echo smarty_function_esynHooker(array('name' => 'tplFrontviewAccountsAfterHeader'), $this);?>


<?php if (isset ( $this->_tpl_vars['listings'] ) && ! empty ( $this->_tpl_vars['listings'] )): ?>
	<!-- listings box start -->
	<div class="listings">
		<?php if ($this->_tpl_vars['config']['mod_rewrite']): ?><?php $this->assign('type', 2); ?><?php else: ?><?php $this->assign('type', 1); ?><?php endif; ?>
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
	</div>
	<!-- listings box end -->

	<?php echo smarty_function_navigation(array('aTotal' => $this->_tpl_vars['total_listings'],'aTemplate' => $this->_tpl_vars['url'],'aItemsPerPage' => $this->_tpl_vars['config']['num_index_listings'],'aNumPageItems' => 5,'aTruncateParam' => 1), $this);?>


	<?php echo smarty_function_include_file(array('js' => "js/frontend/listing-display"), $this);?>

<?php else: ?>
	<div class="box">
		<?php echo $this->_tpl_vars['lang']['no_account_listings']; ?>

	</div>
<?php endif; ?>

<?php echo smarty_function_esynHooker(array('name' => 'tplFrontviewAccountsBeforeFooter'), $this);?>


<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>