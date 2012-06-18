<?php /* Smarty version 2.6.26, created on 2011-12-13 04:26:29
         compiled from /home/vbezruchkin/www/v1700/templates/common/captcha.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'include_captcha', '/home/vbezruchkin/www/v1700/templates/common/captcha.tpl', 5, false),)), $this); ?>
<?php if ($this->_tpl_vars['config']['captcha'] && $this->_tpl_vars['config']['captcha_name'] != ''): ?>
	<div class="captcha" id="captcha">
		<fieldset class="collapsible">
			<legend><?php echo $this->_tpl_vars['lang']['captcha']; ?>
</legend>
			<?php echo smarty_function_include_captcha(array('name' => $this->_tpl_vars['config']['captcha_name']), $this);?>

		</fieldset>
	</div>
<?php endif; ?>