<?php /* Smarty version 2.6.26, created on 2011-12-13 04:27:19
         compiled from buttons.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'cat', 'buttons.tpl', 4, false),array('modifier', 'file_exists', 'buttons.tpl', 4, false),)), $this); ?>
<?php if (isset ( $this->_tpl_vars['actions'] )): ?>
	<div class="buttons">
	<?php $_from = $this->_tpl_vars['actions']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['action']):
?>
		<a href="<?php if (isset ( $this->_tpl_vars['action']['url'] ) && $this->_tpl_vars['action']['url'] != ''): ?><?php echo $this->_tpl_vars['action']['url']; ?>
<?php else: ?>#<?php endif; ?>" <?php if (isset ( $this->_tpl_vars['action']['attributes'] ) && $this->_tpl_vars['action']['attributes'] != ''): ?><?php echo $this->_tpl_vars['action']['attributes']; ?>
<?php endif; ?>><img src="<?php if (@ESYN_CURRENT_PLUGIN && ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=@ESYN_PLUGIN_TEMPLATE)) ? $this->_run_mod_handler('cat', true, $_tmp, '/img/') : smarty_modifier_cat($_tmp, '/img/')))) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['action']['icon']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['action']['icon'])))) ? $this->_run_mod_handler('file_exists', true, $_tmp) : file_exists($_tmp))): ?><?php echo @ESYN_URL; ?>
plugins/<?php echo @ESYN_CURRENT_PLUGIN; ?>
/admin/templates/img/<?php else: ?>templates/<?php echo $this->_tpl_vars['config']['admin_tmpl']; ?>
/img/icons/<?php endif; ?><?php if (isset ( $this->_tpl_vars['action']['icon'] ) && $this->_tpl_vars['action']['icon'] != ''): ?><?php echo $this->_tpl_vars['action']['icon']; ?>
<?php else: ?>default-ico.png<?php endif; ?>" title="<?php if (isset ( $this->_tpl_vars['action']['label'] ) && $this->_tpl_vars['action']['label'] != ''): ?><?php echo $this->_tpl_vars['action']['label']; ?>
<?php endif; ?>" alt="<?php if (isset ( $this->_tpl_vars['action']['label'] ) && $this->_tpl_vars['action']['label'] != ''): ?><?php echo $this->_tpl_vars['action']['label']; ?>
<?php endif; ?>" /></a>
	<?php endforeach; endif; unset($_from); ?>
	</div>

	<div style="clear:right; overflow:hidden;"></div>
<?php endif; ?>