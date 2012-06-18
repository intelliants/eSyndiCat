<?php /* Smarty version 2.6.26, created on 2011-12-13 05:11:44
         compiled from /home/vbezruchkin/www/v1700/admin/templates/default/search-sections.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'preventCsrf', '/home/vbezruchkin/www/v1700/admin/templates/default/search-sections.tpl', 6, false),array('function', 'include_file', '/home/vbezruchkin/www/v1700/admin/templates/default/search-sections.tpl', 39, false),array('modifier', 'escape', '/home/vbezruchkin/www/v1700/admin/templates/default/search-sections.tpl', 12, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "header.tpl", 'smarty_include_vars' => array('css' => "js/ext/plugins/panelresizer/css/PanelResizer")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php if (isset ( $_GET['do'] ) && ( $_GET['do'] == 'add' || $_GET['do'] == 'edit' )): ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-header.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['gTitle'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<form action="controller.php?file=search-sections&amp;do=<?php echo $_GET['do']; ?>
<?php if ($_GET['do'] == 'edit'): ?>&amp;id=<?php echo $_GET['id']; ?>
<?php endif; ?>" method="post">
	<?php echo esynUtil::preventCsrf(array(), $this);?>

	<table cellspacing="0" cellpadding="0" width="100%" class="striped">
	<tr>
		<td width="150"><strong><?php echo $this->_tpl_vars['esynI18N']['key']; ?>
:</strong></td>
		<td>
			<?php $this->assign('section_key', ($this->_tpl_vars['config']).".lang|"); ?>
			<input type="text" name="key" size="24" class="common" value="<?php if (isset ( $_GET['key'] )): ?><?php echo ((is_array($_tmp=$_GET['key'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php elseif (isset ( $_POST['key'] )): ?><?php echo ((is_array($_tmp=$_POST['key'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php endif; ?>" <?php if (isset ( $_GET['key'] )): ?>readonly="readonly"<?php endif; ?> />
		</td>
	</tr>
	<?php $_from = $this->_tpl_vars['langs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['code'] => $this->_tpl_vars['lang']):
?>
	<tr>
		<td><strong><?php echo $this->_tpl_vars['lang']; ?>
&nbsp;<?php echo $this->_tpl_vars['esynI18N']['title']; ?>
:</strong></td>
		<td>
			<input type="text" name="title[<?php echo $this->_tpl_vars['code']; ?>
]" size="24" class="common" value="<?php if (isset ( $this->_tpl_vars['section'][$this->_tpl_vars['code']]['title'] )): ?><?php echo $this->_tpl_vars['section'][$this->_tpl_vars['code']]['title']; ?>
<?php elseif (isset ( $_POST['title'][$this->_tpl_vars['code']] )): ?><?php echo $_POST['title'][$this->_tpl_vars['code']]; ?>
<?php endif; ?>" />
		</td>
	</tr>
	<?php endforeach; endif; unset($_from); ?>
	
	<tr class="all">
		<td colspan="2">
			<input type="submit" name="save" class="common" value="<?php if ($_GET['do'] == 'edit'): ?><?php echo $this->_tpl_vars['esynI18N']['save_changes']; ?>
<?php else: ?><?php echo $this->_tpl_vars['esynI18N']['add']; ?>
<?php endif; ?>" />
		</td>
	</tr>
	</table>
	<input type="hidden" name="old_name" value="<?php if (isset ( $this->_tpl_vars['section']['field']['name'] )): ?><?php echo $this->_tpl_vars['section']['field']['name']; ?>
<?php endif; ?>" />
	<input type="hidden" name="do" value="<?php if (isset ( $_GET['do'] )): ?><?php echo $_GET['do']; ?>
<?php endif; ?>" />
	<input type="hidden" name="type" value="<?php if (isset ( $this->_tpl_vars['field']['type'] )): ?><?php echo $this->_tpl_vars['field']['type']; ?>
<?php endif; ?>" />
	</form>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "box-footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php else: ?>
	<div id="box_sections" style="margin-top: 15px;"></div>
<?php endif; ?>

<?php echo smarty_function_include_file(array('js' => "js/intelli/intelli.grid, js/intelli/intelli.gmodel, js/ext/plugins/bettercombobox/betterComboBox, js/ext/plugins/panelresizer/PanelResizer, js/ext/plugins/progressbarpager/ProgressBarPager, js/admin/search-sections"), $this);?>


<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>