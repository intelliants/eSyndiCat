<?php /* Smarty version 2.6.26, created on 2011-12-13 04:27:19
         compiled from notification.tpl */ ?>
<div <?php if (isset ( $this->_tpl_vars['id'] ) && ! empty ( $this->_tpl_vars['id'] )): ?>id="<?php echo $this->_tpl_vars['id']; ?>
"<?php else: ?>id="notification"<?php endif; ?>>
	<?php if ($this->_tpl_vars['msg']): ?>
		<div class="message <?php echo $this->_tpl_vars['msg']['type']; ?>
">
			<div class="inner">
				<div class="icon"></div>
				<ul>
					<?php $_from = $this->_tpl_vars['msg']['msg']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['message']):
?>
						<li><?php echo $this->_tpl_vars['message']; ?>
</li>
					<?php endforeach; endif; unset($_from); ?>
				</ul>
			</div>
		</div>
	<?php endif; ?>
</div>