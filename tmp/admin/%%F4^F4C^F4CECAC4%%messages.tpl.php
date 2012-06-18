<?php /* Smarty version 2.6.26, created on 2011-12-13 04:27:19
         compiled from messages.tpl */ ?>
<?php if (isset ( $this->_tpl_vars['esyndicat_messages'] )): ?>
	<?php $_from = $this->_tpl_vars['esyndicat_messages']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['esyn_message']):
?>
		<?php if (! empty ( $this->_tpl_vars['esyn_message']['msg'] )): ?>
			<div class="message <?php echo $this->_tpl_vars['esyn_message']['type']; ?>
">
				<div class="inner">
					<div class="icon"></div>
					<ul>
						<?php $_from = $this->_tpl_vars['esyn_message']['msg']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['m']):
?>
							<li><?php echo $this->_tpl_vars['m']; ?>
</li>
						<?php endforeach; endif; unset($_from); ?>
					</ul>
				</div>
			</div>
		<?php endif; ?>
	<?php endforeach; endif; unset($_from); ?>
<?php endif; ?>