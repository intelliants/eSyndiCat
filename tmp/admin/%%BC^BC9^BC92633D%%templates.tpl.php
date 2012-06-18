<?php /* Smarty version 2.6.26, created on 2011-12-15 08:46:37
         compiled from /home/vbezruchkin/www/v1700/admin/templates/default/templates.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'cat', '/home/vbezruchkin/www/v1700/admin/templates/default/templates.tpl', 1, false),array('function', 'navigation', '/home/vbezruchkin/www/v1700/admin/templates/default/templates.tpl', 8, false),array('function', 'preventCsrf', '/home/vbezruchkin/www/v1700/admin/templates/default/templates.tpl', 42, false),array('function', 'include_file', '/home/vbezruchkin/www/v1700/admin/templates/default/templates.tpl', 61, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "header.tpl", 'smarty_include_vars' => array('css' => ((is_array($_tmp=@ESYN_URL)) ? $this->_run_mod_handler('cat', true, $_tmp, "js/jquery/plugins/lightbox/css/jquery.lightbox") : smarty_modifier_cat($_tmp, "js/jquery/plugins/lightbox/css/jquery.lightbox")))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-header.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['gTitle'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php if (isset ( $this->_tpl_vars['templates'] ) && ! empty ( $this->_tpl_vars['templates'] )): ?>
		<table cellspacing="0" class="striped common">
		<tr>
			<td colspan="3" style="border-left: 0px; text-align: center;">
				<?php echo smarty_function_navigation(array('aTotal' => $this->_tpl_vars['total_templates'],'aTemplate' => $this->_tpl_vars['url'],'aItemsPerPage' => @ESYN_NUM_TEMPLATES,'aNumPageItems' => 5), $this);?>

			</td>
		</tr>
		<tr>
			<th width="10%" class="first"><?php echo $this->_tpl_vars['esynI18N']['screenshot']; ?>
</th>
			<th width="79%"><?php echo $this->_tpl_vars['esynI18N']['details']; ?>
</th>
			<th width="10%"><?php echo $this->_tpl_vars['esynI18N']['operation']; ?>
</th>
		</tr>
		<?php $_from = $this->_tpl_vars['templates']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['template']):
?>
			<tr>
				<td class="first">
					<?php if (file_exists ( ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=@ESYN_TEMPLATES)) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['template']['name']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['template']['name'])))) ? $this->_run_mod_handler('cat', true, $_tmp, @ESYN_DS) : smarty_modifier_cat($_tmp, @ESYN_DS)))) ? $this->_run_mod_handler('cat', true, $_tmp, 'info') : smarty_modifier_cat($_tmp, 'info')))) ? $this->_run_mod_handler('cat', true, $_tmp, @ESYN_DS) : smarty_modifier_cat($_tmp, @ESYN_DS)))) ? $this->_run_mod_handler('cat', true, $_tmp, "preview.jpg") : smarty_modifier_cat($_tmp, "preview.jpg")) )): ?>
						<?php $this->assign('template_img', ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=@ESYN_URL)) ? $this->_run_mod_handler('cat', true, $_tmp, "templates/") : smarty_modifier_cat($_tmp, "templates/")))) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['template']['name']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['template']['name'])))) ? $this->_run_mod_handler('cat', true, $_tmp, "/info/") : smarty_modifier_cat($_tmp, "/info/")))) ? $this->_run_mod_handler('cat', true, $_tmp, "preview.jpg") : smarty_modifier_cat($_tmp, "preview.jpg"))); ?>
					<?php else: ?>
						<?php $this->assign('template_img', ((is_array($_tmp=@ESYN_URL)) ? $this->_run_mod_handler('cat', true, $_tmp, "admin/templates/default/img/not_available.gif") : smarty_modifier_cat($_tmp, "admin/templates/default/img/not_available.gif"))); ?>
					<?php endif; ?>
					<a href="#" class="screenshots"><img src="<?php echo $this->_tpl_vars['template_img']; ?>
" title="<?php echo $this->_tpl_vars['template']['title']; ?>
" alt="<?php echo $this->_tpl_vars['template']['title']; ?>
" /></a>
						<?php if (isset ( $this->_tpl_vars['template']['screenshots'] ) && ! empty ( $this->_tpl_vars['template']['screenshots'] )): ?>
							<?php $_from = $this->_tpl_vars['template']['screenshots']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['screenshot']):
?>
								<a class="lb" href="<?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=@ESYN_URL)) ? $this->_run_mod_handler('cat', true, $_tmp, "templates/") : smarty_modifier_cat($_tmp, "templates/")))) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['template']['name']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['template']['name'])))) ? $this->_run_mod_handler('cat', true, $_tmp, "/info/screenshots/") : smarty_modifier_cat($_tmp, "/info/screenshots/")))) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['screenshot']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['screenshot'])); ?>
" style="display: none;"><img src="<?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=@ESYN_URL)) ? $this->_run_mod_handler('cat', true, $_tmp, "templates/") : smarty_modifier_cat($_tmp, "templates/")))) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['template']['name']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['template']['name'])))) ? $this->_run_mod_handler('cat', true, $_tmp, "/info/screenshots/") : smarty_modifier_cat($_tmp, "/info/screenshots/")))) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['screenshot']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['screenshot'])); ?>
" alt="<?php echo $this->_tpl_vars['template']['title']; ?>
" /></a>
							<?php endforeach; endif; unset($_from); ?>
						<?php endif; ?>
				</td>
				
				<td style="vertical-align:top;">
					<?php echo $this->_tpl_vars['esynI18N']['name']; ?>
:&nbsp;<strong><?php echo $this->_tpl_vars['template']['title']; ?>
</strong><br />
					<?php echo $this->_tpl_vars['esynI18N']['author']; ?>
:&nbsp;<strong><?php echo $this->_tpl_vars['template']['author']; ?>
</strong><br />
					<?php echo $this->_tpl_vars['esynI18N']['contributor']; ?>
:&nbsp;<strong><?php echo $this->_tpl_vars['template']['contributor']; ?>
</strong><br />
					<?php echo $this->_tpl_vars['esynI18N']['release_date']; ?>
:&nbsp;<strong><?php echo $this->_tpl_vars['template']['date']; ?>
</strong><br />
					<?php echo $this->_tpl_vars['esynI18N']['esyndicat_version']; ?>
:&nbsp;<strong><?php echo $this->_tpl_vars['template']['compatibility']; ?>
</strong><br />
				</td>
				
				<td>&nbsp;
					<form method="post" action="">
					<?php echo esynUtil::preventCsrf(array(), $this);?>

					<input type="hidden" name="template" value="<?php echo $this->_tpl_vars['template']['name']; ?>
" />
					<?php if ($this->_tpl_vars['template']['name'] != $this->_tpl_vars['tmpl']): ?>
						<input type="submit" name="set_template" value="<?php echo $this->_tpl_vars['esynI18N']['set_default']; ?>
" class="common" /><br /><br />
					<?php endif; ?>
					<a href="<?php echo @ESYN_URL; ?>
?preview=<?php echo $this->_tpl_vars['template']['name']; ?>
" target="_blank"><?php echo $this->_tpl_vars['esynI18N']['preview']; ?>
</a>
					</form>
				</td>
			</tr>
		<?php endforeach; endif; unset($_from); ?>
		<tr>
			<td colspan="3" style="border-left: 0px; text-align: center;">
				<?php echo smarty_function_navigation(array('aTotal' => $this->_tpl_vars['total_templates'],'aTemplate' => $this->_tpl_vars['url'],'aItemsPerPage' => @ESYN_NUM_TEMPLATES,'aNumPageItems' => 5), $this);?>

			</td>
		</tr>
		</table>
	<?php endif; ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php echo smarty_function_include_file(array('js' => "js/jquery/plugins/lightbox/jquery.lightbox, js/admin/templates"), $this);?>


<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>