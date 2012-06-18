<?php /* Smarty version 2.6.26, created on 2011-12-13 11:00:01
         compiled from /home/vbezruchkin/www/v1700/admin/templates/default/error.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div class="message error">
	<div class="inner">
		<div class="icon"></div>
		<ul>
			<?php echo $this->_tpl_vars['error']; ?>

		</ul>
	</div>
</div>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>