<?php /* Smarty version 2.6.26, created on 2011-12-13 04:26:25
         compiled from /home/vbezruchkin/www/v1700/templates/common/listings.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'cat', '/home/vbezruchkin/www/v1700/templates/common/listings.tpl', 2, false),array('function', 'esynHooker', '/home/vbezruchkin/www/v1700/templates/common/listings.tpl', 6, false),array('function', 'navigation', '/home/vbezruchkin/www/v1700/templates/common/listings.tpl', 11, false),array('function', 'include_file', '/home/vbezruchkin/www/v1700/templates/common/listings.tpl', 29, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php $this->assign('type', ((is_array($_tmp=$this->_tpl_vars['view'])) ? $this->_run_mod_handler('cat', true, $_tmp, '_listings') : smarty_modifier_cat($_tmp, '_listings'))); ?>

<h1><?php echo $this->_tpl_vars['lang'][$this->_tpl_vars['type']]; ?>
</h1>

<?php echo smarty_function_esynHooker(array('name' => 'tplFrontListingsAfterHeader'), $this);?>


<?php if ($this->_tpl_vars['listings']): ?>
	<div class="listings">
		<?php if (isset ( $this->_tpl_vars['total_listings'] )): ?>
			<?php echo smarty_function_navigation(array('aTotal' => $this->_tpl_vars['total_listings'],'aTemplate' => $this->_tpl_vars['url'],'aItemsPerPage' => $this->_tpl_vars['config']['num_index_listings'],'aNumPageItems' => 5,'aTruncateParam' => 1), $this);?>

		<?php endif; ?>
			
		<table cellspacing="0" cellpadding="0" width="100%">
		<?php $_from = $this->_tpl_vars['listings']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['listings'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['listings']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['listing']):
        $this->_foreach['listings']['iteration']++;
?>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "listing-display.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php endforeach; endif; unset($_from); ?>
		</table>
			
		<?php if (isset ( $this->_tpl_vars['total_listings'] )): ?>
			<?php echo smarty_function_navigation(array('aTotal' => $this->_tpl_vars['total_listings'],'aTemplate' => $this->_tpl_vars['url'],'aItemsPerPage' => $this->_tpl_vars['config']['num_index_listings'],'aNumPageItems' => 5,'aTruncateParam' => 1), $this);?>

		<?php endif; ?>
	</div>

	<?php if ($this->_tpl_vars['esynAccountInfo']): ?>
		<hr /><div class="waiting">&nbsp;</div><div class="admin-approve"> - <?php echo $this->_tpl_vars['lang']['listings_legend']; ?>
</div>
	<?php endif; ?>

	<?php echo smarty_function_include_file(array('js' => "js/frontend/listing-display"), $this);?>

<?php else: ?>
	<p><?php echo $this->_tpl_vars['lang']['no_listings']; ?>
</p>
<?php endif; ?>

<?php echo smarty_function_esynHooker(array('name' => 'listingsBeforeFooter'), $this);?>


<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>