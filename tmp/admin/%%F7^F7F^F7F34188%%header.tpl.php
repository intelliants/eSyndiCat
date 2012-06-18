<?php /* Smarty version 2.6.26, created on 2011-12-15 11:07:30
         compiled from header.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'include_file', 'header.tpl', 11, false),array('function', 'esynHooker', 'header.tpl', 30, false),array('function', 'print_icon_url', 'header.tpl', 115, false),array('modifier', 'cat', 'header.tpl', 13, false),)), $this); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>

<head>
	<title><?php if (isset ( $this->_tpl_vars['gTitle'] )): ?><?php echo $this->_tpl_vars['gTitle']; ?>
<?php endif; ?>&nbsp;<?php echo $this->_tpl_vars['config']['suffix']; ?>
</title>
	<meta http-equiv="Content-Type" content="text/html;charset=<?php echo $this->_tpl_vars['config']['charset']; ?>
" />
	<base href="<?php echo @ESYN_URL; ?>
<?php echo @ESYN_ADMIN_FOLDER; ?>
/" />

	<link rel="shortcut icon" href="<?php echo @ESYN_URL; ?>
favicon.ico" />

	<?php echo smarty_function_include_file(array('css' => "js/ext/resources/css/ext-all"), $this);?>


	<?php $this->assign('style_file', ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=@ESYN_ADMIN_FOLDER)) ? $this->_run_mod_handler('cat', true, $_tmp, '/templates/') : smarty_modifier_cat($_tmp, '/templates/')))) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['config']['admin_tmpl']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['config']['admin_tmpl'])))) ? $this->_run_mod_handler('cat', true, $_tmp, '/css/style') : smarty_modifier_cat($_tmp, '/css/style'))); ?>

	<?php echo smarty_function_include_file(array('css' => $this->_tpl_vars['style_file']), $this);?>


	<?php if (isset ( $this->_tpl_vars['css'] )): ?>
		<?php echo smarty_function_include_file(array('css' => $this->_tpl_vars['css']), $this);?>

	<?php endif; ?>

	<?php echo smarty_function_include_file(array('js' => "js/ext/ext-base, js/ext/ext-all"), $this);?>

	<?php echo smarty_function_include_file(array('js' => "js/jquery/jquery, js/jquery/plugins/jquery.interface, js/jquery/plugins/jquery.corner"), $this);?>

	<?php echo smarty_function_include_file(array('js' => "js/intelli/intelli, js/intelli/intelli.admin"), $this);?>


	<?php $this->assign('js_admin_lang_file', ((is_array($_tmp="tmp/cache/intelli.admin.lang.")) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['config']['lang']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['config']['lang']))); ?>

	<?php echo smarty_function_include_file(array('js' => "tmp/cache/intelli.config"), $this);?>

	<?php echo smarty_function_include_file(array('js' => $this->_tpl_vars['js_admin_lang_file']), $this);?>


	<?php echo smarty_function_esynHooker(array('name' => 'adminHeadSection'), $this);?>

</head>

<body>

<noscript>
	<div class="js_notification"><?php echo $this->_tpl_vars['esynI18N']['error_javascript']; ?>
</div>
</noscript>

<script type="text/javascript">
intelli.admin.lang = intelli.admin.lang['<?php echo $this->_tpl_vars['config']['lang']; ?>
'];
</script>

<!-- header start -->
<div class="header">

	<!-- logo start -->
	<div class="logo"><a href="http://www.esyndicat.com/"><img src="templates/<?php echo $this->_tpl_vars['config']['admin_tmpl']; ?>
/img/logos/logo.png" alt="eSyndiCat Directory Software" /></a></div>
	<!-- logo end -->

	<!-- header buttons start -->
	<div class="header-buttons">
		<ul>
			<li><a class="inner" href="<?php echo @ESYN_BASE_URL; ?>
" target="_blank"><?php echo $this->_tpl_vars['esynI18N']['site_home']; ?>
</a></li>
			<li><a class="inner" href="<?php echo @ESYN_URL; ?>
" target="_blank"><?php echo $this->_tpl_vars['esynI18N']['directory_home']; ?>
</a></li>
			<li style="padding: 0;"><a class="inner" style="background: none;" href="http://www.esyndicat.com/order.html" target="_blank"><img src="http://tools.esyndicat.com/img/purchase.png" alt="Purchase eSyndiCat Directory Script" /></a></li>
		</ul>
	</div>
	<!-- header buttons end -->

	<!-- login info start -->
	<div class="login-info">
		<ul>
			<li><?php echo $this->_tpl_vars['esynI18N']['howdy']; ?>
, <a href="controller.php?file=admins&amp;do=edit&amp;id=<?php echo $this->_tpl_vars['currentAdmin']['id']; ?>
"><?php echo $this->_tpl_vars['currentAdmin']['username']; ?>
</a></li>
			<li><a class="logout" href="logout.php" id="admin_logout"><?php echo $this->_tpl_vars['esynI18N']['logout']; ?>
</a></li>
		</ul>
	</div>
	<!-- login info end -->

	<?php if (isset ( $this->_tpl_vars['adminHeaderMenu'] ) && ! empty ( $this->_tpl_vars['adminHeaderMenu'] )): ?>
		<!-- header menu start -->

		<div class="header-menus">
			<div class="h-menu">
				<div class="h-menu-inner">
					<div class="jump-to">
						<span style="float:left;"><a><?php echo $this->_tpl_vars['esynI18N']['quick_jump_to']; ?>
</a></span>
						<span class="h-arrow">&nbsp;</span>
						<div style="clear:both;"></div>
					</div>
					<div class="h-submenu">
					<?php $_from = $this->_tpl_vars['adminHeaderMenu']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
						<?php if ($this->_tpl_vars['item']['text'] == 'divider'): ?>
							<div class="h-divider"></div>
						<?php else: ?>
							<a href="<?php echo $this->_tpl_vars['item']['href']; ?>
" <?php if (isset ( $this->_tpl_vars['item']['attr'] ) && ! empty ( $this->_tpl_vars['item']['attr'] )): ?><?php echo $this->_tpl_vars['item']['attr']; ?>
<?php endif; ?>><?php echo $this->_tpl_vars['item']['text']; ?>
</a>
						<?php endif; ?>
					<?php endforeach; endif; unset($_from); ?>
					</div>
				</div>
			</div>
		</div>
		<!-- header menu end -->
	<?php endif; ?>
</div>
<!-- header end -->

<div class="top-menu" id="top_menu"><?php if (isset ( $this->_tpl_vars['update_msg'] ) && ! empty ( $this->_tpl_vars['update_msg'] )): ?><?php echo $this->_tpl_vars['update_msg']; ?>
<?php endif; ?></div>

<!-- content start -->
<div class="content" id="mainCon">

	<!-- left column start -->
	<div class="left-column" id="left-column">
		<?php if (isset ( $this->_tpl_vars['adminMenu'] ) && ! empty ( $this->_tpl_vars['adminMenu'] )): ?>
			<?php $_from = $this->_tpl_vars['adminMenu']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['menu']):
?>
				<!-- menu start -->
				<div class="menu dragGroup" id="menu_box_<?php echo $this->_tpl_vars['menu']['name']; ?>
" <?php if (! isset ( $this->_tpl_vars['menu']['items'] )): ?>style="display:none;"<?php endif; ?>>
					<div class="inner">
						<div class="menu-caption"><?php echo $this->_tpl_vars['menu']['text']; ?>
</div>
						<div class="minmax white-<?php if (isset ( $this->_tpl_vars['menu']['open'] )): ?><?php echo $this->_tpl_vars['menu']['open']; ?>
<?php else: ?>open<?php endif; ?>" id="amenu_<?php echo $this->_tpl_vars['menu']['name']; ?>
"></div>
						<div class="box-content" style="padding: 0; <?php if (isset ( $this->_tpl_vars['menu']['open'] ) && $this->_tpl_vars['menu']['open'] == 'close'): ?>display:none;<?php endif; ?>" >
							<ul class="menu" id="menu_<?php echo $this->_tpl_vars['menu']['name']; ?>
">
							<?php if (isset ( $this->_tpl_vars['menu']['items'] ) && ! empty ( $this->_tpl_vars['menu']['items'] )): ?>
								<?php $_from = $this->_tpl_vars['menu']['items']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
									<li<?php if (@ESYN_REALM == $this->_tpl_vars['item']['aco']): ?> class="active"<?php endif; ?>><a <?php echo smarty_function_print_icon_url(array('realm' => $this->_tpl_vars['item']['aco'],'path' => 'menu'), $this);?>
 href="<?php echo $this->_tpl_vars['item']['href']; ?>
" <?php if (isset ( $this->_tpl_vars['item']['attr'] ) && ! empty ( $this->_tpl_vars['item']['attr'] )): ?><?php echo $this->_tpl_vars['item']['attr']; ?>
<?php endif; ?> class="submenu"><?php echo $this->_tpl_vars['item']['text']; ?>
</a></li>
								<?php endforeach; endif; unset($_from); ?>
							<?php endif; ?>
							</ul>
						</div>
					</div>
				</div>
				<!-- menu end -->
			<?php endforeach; endif; unset($_from); ?>
		<?php endif; ?>
	</div>
	<!-- left column end -->

	<!-- right column start -->
	<div class="right-column">

		<?php if (isset ( $this->_tpl_vars['esyn_tabs'] ) && ! empty ( $this->_tpl_vars['esyn_tabs'] )): ?>
			<div class="empty-div"></div>
			<?php $_from = $this->_tpl_vars['esyn_tabs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['esyn_tab']):
?>
				<div class="tab-content <?php echo $this->_tpl_vars['esyn_tab']['name']; ?>
" id="esyntab-content-<?php echo $this->_tpl_vars['esyn_tab']['name']; ?>
">
					<div class="tab-content-inner">
						<?php $this->assign('esyn_tab_content_key', ((is_array($_tmp='tab_content_')) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['esyn_tab']['name']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['esyn_tab']['name']))); ?>
						<?php echo $this->_tpl_vars['esynI18N'][$this->_tpl_vars['esyn_tab_content_key']]; ?>

					</div>
				</div>
			<?php endforeach; endif; unset($_from); ?>

			<?php $_from = $this->_tpl_vars['esyn_tabs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['esyn_tab']):
?>
				<div class="tab-shortcut" id="esyntab-shortcut-<?php echo $this->_tpl_vars['esyn_tab']['name']; ?>
">
					<div class="tab-shortcut-inner">
						<?php $this->assign('esyn_tab_title_key', ((is_array($_tmp='tab_title_')) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['esyn_tab']['name']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['esyn_tab']['name']))); ?>
						<div class="tab-icon tab-icon-<?php echo $this->_tpl_vars['esyn_tab']['name']; ?>
"><?php echo $this->_tpl_vars['esynI18N'][$this->_tpl_vars['esyn_tab_title_key']]; ?>
</div>
					</div>
				</div>
			<?php endforeach; endif; unset($_from); ?>
		<?php endif; ?>

<?php if (isset ( $this->_tpl_vars['breadcrumb'] )): ?>
	<?php echo $this->_tpl_vars['breadcrumb']; ?>

<?php endif; ?>

<?php if (isset ( $this->_tpl_vars['gTitle'] )): ?><h1 class="common"<?php echo smarty_function_print_icon_url(array('realm' => @ESYN_REALM,'header' => true), $this);?>
><?php echo $this->_tpl_vars['gTitle']; ?>
</h1><?php endif; ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "buttons.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php if (isset ( $this->_tpl_vars['notifications'] ) && ! empty ( $this->_tpl_vars['notifications'] )): ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "notification.tpl", 'smarty_include_vars' => array('msg' => $this->_tpl_vars['notifications'],'id' => 'notif')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>

<?php if (isset ( $this->_tpl_vars['esyndicat_messages'] ) && ! empty ( $this->_tpl_vars['esyndicat_messages'] )): ?>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "messages.tpl", 'smarty_include_vars' => array('esyndicat_messages' => $this->_tpl_vars['esyndicat_messages'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "notification.tpl", 'smarty_include_vars' => array('msg' => $this->_tpl_vars['messages'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>