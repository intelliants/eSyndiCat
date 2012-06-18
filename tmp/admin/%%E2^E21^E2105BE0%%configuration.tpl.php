<?php /* Smarty version 2.6.26, created on 2011-12-13 04:27:46
         compiled from /home/vbezruchkin/www/v1700/admin/templates/default/configuration.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'preventCsrf', '/home/vbezruchkin/www/v1700/admin/templates/default/configuration.tpl', 32, false),array('function', 'html_radio_switcher', '/home/vbezruchkin/www/v1700/admin/templates/default/configuration.tpl', 119, false),array('function', 'include_file', '/home/vbezruchkin/www/v1700/admin/templates/default/configuration.tpl', 171, false),array('modifier', 'escape', '/home/vbezruchkin/www/v1700/admin/templates/default/configuration.tpl', 56, false),array('modifier', 'cat', '/home/vbezruchkin/www/v1700/admin/templates/default/configuration.tpl', 98, false),array('modifier', 'explode', '/home/vbezruchkin/www/v1700/admin/templates/default/configuration.tpl', 132, false),array('modifier', 'count', '/home/vbezruchkin/www/v1700/admin/templates/default/configuration.tpl', 136, false),array('modifier', 'trim', '/home/vbezruchkin/www/v1700/admin/templates/default/configuration.tpl', 138, false),array('modifier', 'replace', '/home/vbezruchkin/www/v1700/admin/templates/default/configuration.tpl', 147, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "header.tpl", 'smarty_include_vars' => array('css' => "js/ext/plugins/fileuploadfield/css/file-upload")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<a name="top"></a>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-header.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['esynI18N']['htaccess_file'],'id' => 'htaccess','hidden' => 'true')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php if (isset ( $this->_tpl_vars['htaccess_code'] ) && ! empty ( $this->_tpl_vars['htaccess_code'] )): ?>
	<br />
	<a class="button" id="close" href="#"><?php echo $this->_tpl_vars['esynI18N']['close']; ?>
</a>&nbsp;
	<a class="button" id="rebuild" href="#"><?php echo $this->_tpl_vars['esynI18N']['rebuild_htaccess']; ?>
</a>&nbsp;
	<a class="button copybutton" id="copybutton" href="#"><?php echo $this->_tpl_vars['esynI18N']['copy_to_clipboard']; ?>
</a>
	<?php echo $this->_tpl_vars['htaccess_code']; ?>

<?php endif; ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-header.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['esynI18N']['config_groups'],'id' => 'options')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div class="config-col-left">
	<ul class="groups">
	<?php $_from = $this->_tpl_vars['groups']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['groups'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['groups']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['group_item']):
        $this->_foreach['groups']['iteration']++;
?>
		<?php if (isset ( $this->_tpl_vars['group'] ) && $this->_tpl_vars['group'] == $this->_tpl_vars['key']): ?>
			<li><div><?php echo $this->_tpl_vars['group_item']; ?>
</div></li>
		<?php else: ?>
			<li><a href="controller.php?file=configuration&amp;group=<?php echo $this->_tpl_vars['key']; ?>
"><?php echo $this->_tpl_vars['group_item']; ?>
</a></li>
		<?php endif; ?>
	<?php endforeach; endif; unset($_from); ?>
	</ul>
</div>

<div class="config-col-right">
<?php if (isset ( $this->_tpl_vars['params'] )): ?>
		<form action="controller.php?file=configuration&amp;group=<?php echo $this->_tpl_vars['group']; ?>
" enctype="multipart/form-data" method="post">
		<?php echo esynUtil::preventCsrf(array(), $this);?>

		<table cellspacing="0" class="striped" width="100%">
		
		<?php if (isset ( $this->_tpl_vars['group'] ) && $this->_tpl_vars['group'] == 'email_templates'): ?>
		<tr>
			<td colspan="2" style="padding:0;">
				<ul class="config-tabs">
				<?php if (isset ( $_GET['show'] ) && $_GET['show'] == 'plaintext'): ?>
					<li><div><?php echo $this->_tpl_vars['esynI18N']['plain_text_templates']; ?>
</div></li>
				<?php else: ?>
					<li><a id="plaintext" href="controller.php?file=configuration&amp;group=email_templates&amp;show=plaintext"><?php echo $this->_tpl_vars['esynI18N']['plain_text_templates']; ?>
</a></li>
				<?php endif; ?>
				<?php if (! ( isset ( $_GET['show'] ) && $_GET['show'] == 'html' )): ?>
					<li><a href="controller.php?file=configuration&amp;group=email_templates&amp;show=html"><?php echo $this->_tpl_vars['esynI18N']['html_templates']; ?>
</a></li>
				<?php else: ?>
					<li><div><?php echo $this->_tpl_vars['esynI18N']['html_templates']; ?>
</div></li>
				<?php endif; ?>
				</ul>
		<?php endif; ?>
		
		<?php if ($this->_tpl_vars['group'] != 'email_templates' || ( isset ( $_GET['show'] ) && in_array ( $_GET['show'] , array ( 'plaintext' , 'html' ) ) )): ?>
			<?php $_from = $this->_tpl_vars['params']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['value']):
?>
				<?php if ($this->_tpl_vars['value']['type'] == 'password'): ?>
					<tr>
						<td class="tip-header" id="tip-header-<?php echo $this->_tpl_vars['value']['name']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['value']['description'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</td>
						<td><input type="password" class="common" size="45" name="param[<?php echo $this->_tpl_vars['value']['name']; ?>
]" id="<?php echo $this->_tpl_vars['value']['name']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['value']['value'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" /></td>
					</tr>
				<?php elseif ($this->_tpl_vars['value']['type'] == 'text'): ?>
					<tr>
						<td class="tip-header" id="tip-header-<?php echo $this->_tpl_vars['value']['name']; ?>
" width="25%"><?php echo ((is_array($_tmp=$this->_tpl_vars['value']['description'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</td>
						<?php if ($this->_tpl_vars['value']['name'] == 'expiration_action'): ?>
							<td>
								<select name="param[expiration_action]" class="common">
									<option value="" <?php if ($this->_tpl_vars['value']['value'] == ''): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['esynI18N']['nothing']; ?>
</option>
									<option value="remove" <?php if ($this->_tpl_vars['value']['value'] == 'remove'): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['esynI18N']['remove']; ?>
</option>
									<optgroup label="Status">
										<option value="approval" <?php if ($this->_tpl_vars['value']['value'] == 'approval'): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['esynI18N']['approval']; ?>
</option>
										<option value="banned" <?php if ($this->_tpl_vars['value']['value'] == 'banned'): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['esynI18N']['banned']; ?>
</option>
										<option value="suspended" <?php if ($this->_tpl_vars['value']['value'] == 'suspended'): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['esynI18N']['suspended']; ?>
</option>
									</optgroup>
									<optgroup label="Type">
										<option value="regular" <?php if ($this->_tpl_vars['value']['value'] == 'regular'): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['esynI18N']['regular']; ?>
</option>
										<option value="featured" <?php if ($this->_tpl_vars['value']['value'] == 'featured'): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['esynI18N']['featured']; ?>
</option>
										<option value="partner" <?php if ($this->_tpl_vars['value']['value'] == 'partner'): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['esynI18N']['partner']; ?>
</option>
									</optgroup>
								</select>
							</td>
						<?php elseif ($this->_tpl_vars['value']['name'] == 'captcha_preview'): ?>
							<?php if (isset ( $this->_tpl_vars['captcha_preview'] ) && ! empty ( $this->_tpl_vars['captcha_preview'] )): ?>
								<td><?php echo $this->_tpl_vars['captcha_preview']; ?>
</td>
							<?php else: ?>
								<td><?php echo $this->_tpl_vars['esynI18N']['no_captcha_preview']; ?>
</td>
							<?php endif; ?>
						<?php else: ?>
							<td><input type="text" size="45" name="param[<?php echo $this->_tpl_vars['value']['name']; ?>
]" class="common" id="<?php echo $this->_tpl_vars['value']['name']; ?>
" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['value']['value'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
" /></td>
						<?php endif; ?>
					</tr>
				<?php elseif ($this->_tpl_vars['value']['type'] == 'textarea'): ?>
					<tr>
						<td class="tip-header" id="tip-header-<?php echo $this->_tpl_vars['value']['name']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['value']['description'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</td>
						<td><textarea name="param[<?php echo $this->_tpl_vars['value']['name']; ?>
]" id="<?php echo $this->_tpl_vars['value']['name']; ?>
" class="<?php if ($this->_tpl_vars['value']['editor'] == '1'): ?>cked <?php endif; ?>common" cols="45" rows="7"><?php echo ((is_array($_tmp=$this->_tpl_vars['value']['value'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</textarea></td>
					</tr>
				<?php elseif ($this->_tpl_vars['value']['type'] == 'image'): ?>
					<tr>
						<td class="tip-header" id="tip-header-<?php echo $this->_tpl_vars['value']['name']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['value']['description'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</td>
						<td>
							<?php if (! is_writeable ( ((is_array($_tmp=((is_array($_tmp=@ESYN_HOME)) ? $this->_run_mod_handler('cat', true, $_tmp, @ESYN_DS) : smarty_modifier_cat($_tmp, @ESYN_DS)))) ? $this->_run_mod_handler('cat', true, $_tmp, 'uploads') : smarty_modifier_cat($_tmp, 'uploads')) )): ?>
								<div style="width: 430px; padding: 3px; margin: 0; background: #FFE269 none repeat scroll 0 0;"><i><?php echo $this->_tpl_vars['esynI18N']['upload_writable_permission']; ?>
</i></div>							
							<?php else: ?>
								<input type="hidden" name="param[<?php echo $this->_tpl_vars['value']['name']; ?>
]" />
								<input type="file" name="<?php echo $this->_tpl_vars['value']['name']; ?>
" id="conf_<?php echo $this->_tpl_vars['value']['name']; ?>
" class="common" size="42" />
							<?php endif; ?>

							<?php if ($this->_tpl_vars['value']['value'] != ''): ?>
								<a href="#" class="view_image"><?php echo $this->_tpl_vars['esynI18N']['view_image']; ?>
</a>&nbsp;
								<a href="#" class="remove_image"><?php echo $this->_tpl_vars['esynI18N']['remove']; ?>
 <?php echo $this->_tpl_vars['esynI18N']['image']; ?>
</a>
							<?php endif; ?>
						</td>
					</tr>
				<?php elseif ($this->_tpl_vars['value']['type'] == 'checkbox'): ?>
					<tr>
						<td class="tip-header" id="tip-header-<?php echo $this->_tpl_vars['value']['name']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['value']['description'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</td>
						<td><input type="checkbox" name="param[<?php echo $this->_tpl_vars['value']['name']; ?>
]" id="<?php echo $this->_tpl_vars['value']['name']; ?>
" /></td>
					</tr>
				<?php elseif ($this->_tpl_vars['value']['type'] == 'radio'): ?>
					<tr>
						<td class="tip-header" id="tip-header-<?php echo $this->_tpl_vars['value']['name']; ?>
" width="250"><?php echo ((is_array($_tmp=$this->_tpl_vars['value']['description'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</td>
						<td><?php echo smarty_function_html_radio_switcher(array('value' => $this->_tpl_vars['value']['value'],'name' => $this->_tpl_vars['value']['name'],'conf' => true), $this);?>
</td>
					</tr>
				<?php elseif ($this->_tpl_vars['value']['type'] == 'select'): ?>
					<tr>
						<td class="tip-header" id="tip-header-<?php echo $this->_tpl_vars['value']['name']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['value']['description'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</td>
					
						<?php if ($this->_tpl_vars['value']['name'] == 'tmpl'): ?>
							<?php $this->assign('array_res', $this->_tpl_vars['templates']); ?>
						<?php elseif ($this->_tpl_vars['value']['name'] == 'admin_tmpl'): ?>
							<?php $this->assign('array_res', $this->_tpl_vars['admin_templates']); ?>
						<?php elseif ($this->_tpl_vars['value']['name'] == 'lang'): ?>
							<?php $this->assign('array_res', $this->_tpl_vars['langs']); ?>
						<?php else: ?>
							<?php $this->assign('array_res', ((is_array($_tmp=",")) ? $this->_run_mod_handler('explode', true, $_tmp, $this->_tpl_vars['value']['multiple_values']) : explode($_tmp, $this->_tpl_vars['value']['multiple_values']))); ?>
						<?php endif; ?>

						<td>
							<select name="param[<?php echo $this->_tpl_vars['value']['name']; ?>
]" class="common" <?php if (count($this->_tpl_vars['array_res']) == 1): ?>disabled="disabled"<?php endif; ?>>
								<?php $_from = $this->_tpl_vars['array_res']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['value2']):
?>
									<option value="<?php if ($this->_tpl_vars['value']['name'] == 'lang'): ?><?php echo $this->_tpl_vars['key']; ?>
<?php else: ?><?php echo ((is_array($_tmp=$this->_tpl_vars['value2'])) ? $this->_run_mod_handler('trim', true, $_tmp, "'") : trim($_tmp, "'")); ?>
<?php endif; ?>" <?php if (( $this->_tpl_vars['value']['name'] == 'lang' && $this->_tpl_vars['key'] == $this->_tpl_vars['value']['value'] ) || ((is_array($_tmp=$this->_tpl_vars['value2'])) ? $this->_run_mod_handler('trim', true, $_tmp, "'") : trim($_tmp, "'")) == $this->_tpl_vars['value']['value']): ?>selected="selected"<?php endif; ?>><?php echo ((is_array($_tmp=$this->_tpl_vars['value2'])) ? $this->_run_mod_handler('trim', true, $_tmp, "'") : trim($_tmp, "'")); ?>
</option>
								<?php endforeach; endif; unset($_from); ?>
							</select>
						</td>
					</tr>
				<?php elseif ($this->_tpl_vars['value']['type'] == 'divider'): ?>
					<tr>
						<td colspan="2" class="caption"><strong><?php echo ((is_array($_tmp=$this->_tpl_vars['value']['value'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</strong><?php if (! empty ( $this->_tpl_vars['value']['name'] )): ?><a name="<?php echo $this->_tpl_vars['value']['name']; ?>
"></a><?php endif; ?>
							<?php if ($this->_tpl_vars['group'] == 'email_templates'): ?>
								&nbsp;<a href="<?php echo ((is_array($_tmp=$_SERVER['REQUEST_URI'])) ? $this->_run_mod_handler('replace', true, $_tmp, "&", "&amp;") : smarty_modifier_replace($_tmp, "&", "&amp;")); ?>
#top" style="vertical-align:middle;"><img src="templates/default/img/icons/arrow_up.png" alt="" /></a>
								&nbsp;<a href="<?php echo ((is_array($_tmp=$_SERVER['REQUEST_URI'])) ? $this->_run_mod_handler('replace', true, $_tmp, "&", "&amp;") : smarty_modifier_replace($_tmp, "&", "&amp;")); ?>
#bottom" style="vertical-align:middle;"><img src="templates/default/img/icons/arrow_down.png" alt="" /></a>
							<?php endif; ?>
						</td>
					</tr>
				<?php endif; ?>
			<?php endforeach; endif; unset($_from); ?>
		<?php endif; ?>
		
		<tr class="all">
			<?php if ($this->_tpl_vars['group'] == 'email_templates' && ! isset ( $_GET['show'] )): ?>
			<?php else: ?>
				<td colspan="2"><input type="submit" name="save" id="save" class="common" value="<?php echo $this->_tpl_vars['esynI18N']['save_changes']; ?>
" /></td>
			<?php endif; ?>
		</tr>
		</table>
		</form>
<?php endif; ?>
</div>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<a name="bottom"></a>

<?php echo smarty_function_include_file(array('js' => "js/jquery/plugins/iphoneswitch/jquery.iphone-switch, js/ext/plugins/fileuploadfield/FileUploadField, js/ckeditor/ckeditor, js/utils/zeroclipboard/ZeroClipboard, js/admin/configuration"), $this);?>


<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>