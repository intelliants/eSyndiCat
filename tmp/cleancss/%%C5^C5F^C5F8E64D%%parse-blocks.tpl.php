<?php /* Smarty version 2.6.26, created on 2011-12-13 04:25:57
         compiled from /home/vbezruchkin/www/v1700/templates/common/parse-blocks.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('insert', 'dynamic', '/home/vbezruchkin/www/v1700/templates/common/parse-blocks.tpl', 12, false),array('modifier', 'escape', '/home/vbezruchkin/www/v1700/templates/common/parse-blocks.tpl', 14, false),)), $this); ?>
<!-- dynamic bocks -->
<?php if (isset ( $this->_tpl_vars['pos'] ) && ! empty ( $this->_tpl_vars['pos'] )): ?>
	<?php $_from = $this->_tpl_vars['pos']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['block']):
?>
		<!--__b_<?php echo $this->_tpl_vars['block']['id']; ?>
-->
		<?php if ($this->_tpl_vars['block']['show_header'] || $this->_tpl_vars['manageMode']): ?>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-header.tpl", 'smarty_include_vars' => array('caption' => $this->_tpl_vars['block']['title'],'style' => 'movable','id' => $this->_tpl_vars['block']['id'],'collapsible' => $this->_tpl_vars['block']['collapsible'],'collapsed' => $this->_tpl_vars['block']['collapsed'],'rss' => $this->_tpl_vars['block']['rss'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php else: ?>
			<div class="box" id="block_<?php echo $this->_tpl_vars['block']['id']; ?>
">
		<?php endif; ?>
		<!--__b_c_<?php echo $this->_tpl_vars['block']['id']; ?>
-->
			<?php if ($this->_tpl_vars['block']['type'] == 'smarty'): ?>
				<?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'dynamic', 'content' => $this->_tpl_vars['block']['contents'])), $this); ?>

			<?php elseif ($this->_tpl_vars['block']['type'] == 'plain'): ?>
				<?php echo ((is_array($_tmp=$this->_tpl_vars['block']['contents'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>

			<?php elseif ($this->_tpl_vars['block']['type'] == 'php'): ?>
				<?php 
					eval($this->_tpl_vars['block']['contents']);
				 ?>
			<?php else: ?>
				<?php echo $this->_tpl_vars['block']['contents']; ?>

			<?php endif; ?>
		<!--__e_c_<?php echo $this->_tpl_vars['block']['id']; ?>
-->
		<?php if ($this->_tpl_vars['block']['show_header'] || $this->_tpl_vars['manageMode']): ?>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php else: ?>
			</div>
		<?php endif; ?>
		<!--__e_<?php echo $this->_tpl_vars['block']['id']; ?>
-->
	<?php endforeach; endif; unset($_from); ?>
<?php endif; ?>
<!-- end dynamic bocks -->