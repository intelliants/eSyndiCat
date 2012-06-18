<?php /* Smarty version 2.6.26, created on 2011-12-13 04:27:46
         compiled from box-header.tpl */ ?>
<!-- simple box start -->
<div class="box" <?php if (isset ( $this->_tpl_vars['id'] )): ?>id="<?php echo $this->_tpl_vars['id']; ?>
"<?php endif; ?> <?php if (isset ( $this->_tpl_vars['hidden'] )): ?>style="display: none;"<?php endif; ?>>
	<div class="inner">
		<div class="box-caption"><?php echo $this->_tpl_vars['title']; ?>
</div>
		<div class="minmax <?php if (isset ( $this->_tpl_vars['collapsed'] )): ?>white-close<?php else: ?>white-open<?php endif; ?>"></div>
		<div class="box-content" <?php if (isset ( $this->_tpl_vars['collapsed'] )): ?>style="display: none;"<?php endif; ?><?php if (isset ( $this->_tpl_vars['style'] ) && ! empty ( $this->_tpl_vars['style'] )): ?>style="<?php echo $this->_tpl_vars['style']; ?>
"<?php endif; ?>>