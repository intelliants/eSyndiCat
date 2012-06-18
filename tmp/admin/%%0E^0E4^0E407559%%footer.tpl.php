<?php /* Smarty version 2.6.26, created on 2011-12-13 04:27:19
         compiled from footer.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', 'footer.tpl', 13, false),array('function', 'include_file', 'footer.tpl', 26, false),)), $this); ?>
</div>
<!-- right column end -->

<div style="clear:both;"></div>

</div>
<!-- content end -->

<!-- footer start -->
<div class="footer">
	<div>
		Powered by <a href="http://www.esyndicat.com/" target="_blank">eSyndiCat Free v<?php echo $this->_tpl_vars['config']['version']; ?>
</a><br />
		Copyright &copy; 2005-<?php echo ((is_array($_tmp=time())) ? $this->_run_mod_handler('date_format', true, $_tmp, "%Y") : smarty_modifier_date_format($_tmp, "%Y")); ?>
 <a href="http://www.intelliants.com/" target="_blank">Intelliants LLC</a>
	</div>
</div>
<!-- footer end -->

<?php if (isset ( $this->_tpl_vars['esyn_tips'] ) && ! empty ( $this->_tpl_vars['esyn_tips'] )): ?>
	<?php $_from = $this->_tpl_vars['esyn_tips']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['tip']):
?>
		<div style="display: none;"><div id="tip-content-<?php echo $this->_tpl_vars['tip']['key']; ?>
"><?php echo $this->_tpl_vars['tip']['value']; ?>
</div></div>
	<?php endforeach; endif; unset($_from); ?>
<?php endif; ?>

<div id="ajax-loader"><?php echo $this->_tpl_vars['esynI18N']['loading']; ?>
</div>

<?php echo smarty_function_include_file(array('js' => "js/admin/footer"), $this);?>


</body>
</html>