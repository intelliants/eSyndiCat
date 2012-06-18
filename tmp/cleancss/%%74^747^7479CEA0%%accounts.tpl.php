<?php /* Smarty version 2.6.26, created on 2011-12-15 10:39:47
         compiled from /home/vbezruchkin/www/v1700/templates/common/accounts.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'esynHooker', '/home/vbezruchkin/www/v1700/templates/common/accounts.tpl', 5, false),array('function', 'print_account_url', '/home/vbezruchkin/www/v1700/templates/common/accounts.tpl', 32, false),array('function', 'print_img', '/home/vbezruchkin/www/v1700/templates/common/accounts.tpl', 32, false),array('modifier', 'date_format', '/home/vbezruchkin/www/v1700/templates/common/accounts.tpl', 31, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<h1><?php echo $this->_tpl_vars['title']; ?>
</h1>

<?php echo smarty_function_esynHooker(array('name' => 'tplFrontAccountsAfterHeader'), $this);?>


<?php if ($this->_tpl_vars['search_alphas']): ?>
	<div class="alpha-navigation">
		<?php $_from = $this->_tpl_vars['search_alphas']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['onealpha']):
?>
			<?php if ($this->_tpl_vars['onealpha'] == $this->_tpl_vars['alpha']): ?>
				<span class="active"><?php echo $this->_tpl_vars['onealpha']; ?>
</span>
			<?php else: ?>
				<a href="<?php echo @ESYN_URL; ?>
accounts<?php if ($this->_tpl_vars['config']['mod_rewrite']): ?>/<?php echo $this->_tpl_vars['onealpha']; ?>
/<?php else: ?>.php?alpha=<?php echo $this->_tpl_vars['onealpha']; ?>
<?php endif; ?>"><?php echo $this->_tpl_vars['onealpha']; ?>
</a>
			<?php endif; ?>
		<?php endforeach; endif; unset($_from); ?>
	</div>
<?php endif; ?>

<?php echo smarty_function_esynHooker(array('name' => 'tplFrontAccountsAfterAlphas'), $this);?>


<?php if (isset ( $this->_tpl_vars['accounts'] ) && ! empty ( $this->_tpl_vars['accounts'] )): ?>
	<table border="0" width="100%" cellpadding="0" cellspacing="0" class="common">
	<tr>
		<th><?php echo $this->_tpl_vars['lang']['username']; ?>
</th>
		<th><?php echo $this->_tpl_vars['lang']['date_registration']; ?>
</th>
		<th>&nbsp;</th>
	</tr>
	<?php $_from = $this->_tpl_vars['accounts']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['account']):
?>
	<tr>
		<td><em><?php echo $this->_tpl_vars['account']['username']; ?>
</em></td>
		<td><?php echo ((is_array($_tmp=$this->_tpl_vars['account']['date_reg'])) ? $this->_run_mod_handler('date_format', true, $_tmp, $this->_tpl_vars['config']['date_format']) : smarty_modifier_date_format($_tmp, $this->_tpl_vars['config']['date_format'])); ?>
</td>
		<td><a href="<?php echo esynLayout::printAccUrl(array('account' => $this->_tpl_vars['account']), $this);?>
"><?php echo smarty_function_print_img(array('fl' => "info_16.png",'full' => true,'title' => $this->_tpl_vars['lang']['view_account_details'],'alt' => $this->_tpl_vars['lang']['view_account_details']), $this);?>
</a></td>
	</tr>
	<?php endforeach; endif; unset($_from); ?>
	</table>
<?php else: ?>
	<p><?php echo $this->_tpl_vars['lang']['no_accounts']; ?>
</p>
<?php endif; ?>

<?php echo smarty_function_esynHooker(array('name' => 'tplFrontAccountsBeforeFooter'), $this);?>


<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>