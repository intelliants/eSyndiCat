<?php /* Smarty version 2.6.26, created on 2011-12-15 10:18:39
         compiled from footer.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', 'footer.tpl', 5, false),array('modifier', 'date_format', 'footer.tpl', 47, false),array('function', 'esynHooker', 'footer.tpl', 40, false),array('function', 'include_file', 'footer.tpl', 70, false),)), $this); ?>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td valign="top" style="width: 50%;">
						<div id="user1Blocks" class="groupWrapper">
							<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "parse-blocks.tpl", 'smarty_include_vars' => array('pos' => ((is_array($_tmp=@$this->_tpl_vars['user1Blocks'])) ? $this->_run_mod_handler('default', true, $_tmp, null) : smarty_modifier_default($_tmp, null)))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						</div>
					</td>
					<td valign="top" style="width: 50%; padding-left: 10px;">
						<div id="user2Blocks" class="groupWrapper">
							<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "parse-blocks.tpl", 'smarty_include_vars' => array('pos' => ((is_array($_tmp=@$this->_tpl_vars['user2Blocks'])) ? $this->_run_mod_handler('default', true, $_tmp, null) : smarty_modifier_default($_tmp, null)))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						</div>
					</td>
				</tr>
				</table>
				<div id="bottomBlocks" class="groupWrapper">
					<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "parse-blocks.tpl", 'smarty_include_vars' => array('pos' => ((is_array($_tmp=@$this->_tpl_vars['bottomBlocks'])) ? $this->_run_mod_handler('default', true, $_tmp, null) : smarty_modifier_default($_tmp, null)))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
				</div>
			</td>
			<?php if (( isset ( $this->_tpl_vars['rightBlocks'] ) && ! empty ( $this->_tpl_vars['rightBlocks'] ) ) || $this->_tpl_vars['manageMode']): ?>
				<td class="right-column" valign="top">
					<div id="rightBlocks" class="groupWrapper">
						<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "parse-blocks.tpl", 'smarty_include_vars' => array('pos' => ((is_array($_tmp=@$this->_tpl_vars['rightBlocks'])) ? $this->_run_mod_handler('default', true, $_tmp, null) : smarty_modifier_default($_tmp, null)))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
					</div>
				</td>
			<?php endif; ?>
		</tr>
		</table>

		<!-- verybottom block -->
		<div id="verybottomBlocks" class="groupWrapper">
			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "parse-blocks.tpl", 'smarty_include_vars' => array('pos' => ((is_array($_tmp=@$this->_tpl_vars['verybottomBlocks'])) ? $this->_run_mod_handler('default', true, $_tmp, null) : smarty_modifier_default($_tmp, null)))));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		</div>
		<!-- verybottom block -->

	</div>
	<!-- content end -->
	
	<!-- footer start -->
	<div class="footer">
		<?php echo smarty_function_esynHooker(array('name' => 'beforeFooterLinks'), $this);?>

		<a href="#">About Us</a> |
		<a href="#">Privacy Policy</a> |
		<a href="#">Terms of Use</a> |
		<a href="#">Help</a> |
		<a href="#">Advertise Us</a>
		<?php echo smarty_function_esynHooker(array('name' => 'afterFooterLinks'), $this);?>

		<div class="copyright">&copy; <?php echo ((is_array($_tmp=$_SERVER['REQUEST_TIME'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%Y") : smarty_modifier_date_format($_tmp, "%Y")); ?>
 Powered by <a href="http://www.esyndicat.com/">eSyndiCat Directory Software</a></div>
	</div>
	<!-- footer end -->

</div>
<!-- main page end -->

<noscript>
	<div class="js_notification"><?php echo $this->_tpl_vars['lang']['error_javascript']; ?>
</div>
</noscript>

<!-- thumbs preview start -->
<div class="thumb">
	<div class="loading" style="display: none;"></div>
</div>
<!-- thumbs preview end -->

<?php echo smarty_function_esynHooker(array('name' => 'footerBeforeIncludeJs'), $this);?>


<?php if ($this->_tpl_vars['manageMode']): ?>
	<div id="mod_box" class="mode">
		<?php echo $this->_tpl_vars['lang']['youre_in_manage_mode']; ?>
. <a href="?switchToNormalMode=y" style="font-weight: bold; color: #FFF;"><?php echo $this->_tpl_vars['lang']['exit']; ?>
</a>
	</div>
	<?php echo smarty_function_include_file(array('js' => "js/frontend/visual-mode"), $this);?>

<?php endif; ?>

<?php if (isset ( $_GET['preview'] ) || isset ( $_SESSION['preview'] )): ?>
	<div id="mod_box" class="mode">
		<?php echo $this->_tpl_vars['lang']['youre_in_preview_mode']; ?>
 <a href="?switchToNormalMode=y" style="font-weight: bold; color: #FFF;"><?php echo $this->_tpl_vars['lang']['exit']; ?>
</a>
	</div>
<?php endif; ?>
<?php if ($this->_tpl_vars['config']['cron']): ?><div style="display:none"><img src="cron.php" width="1" height="1" alt="" /></div><?php endif; ?>

<?php echo smarty_function_esynHooker(array('name' => 'beforeCloseTag'), $this);?>


</body>
</html>