<?php /* Smarty version 2.6.26, created on 2011-12-13 04:26:29
         compiled from /home/vbezruchkin/www/v1700/templates/common/notification.tpl */ ?>
<?php if (isset ( $this->_tpl_vars['msg'] ) && ! empty ( $this->_tpl_vars['msg'] )): ?>
	<div id="notification">
		<div class="<?php if ($this->_tpl_vars['error']): ?>error<?php else: ?>notification<?php endif; ?>">
			<ul class="common">
				<?php $_from = $this->_tpl_vars['msg']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['message']):
?>
					<li><?php echo $this->_tpl_vars['message']; ?>
</li>
				<?php endforeach; endif; unset($_from); ?>
			</ul>
		</div>
	</div>
<?php endif; ?>