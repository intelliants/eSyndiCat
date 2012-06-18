<?php /* Smarty version 2.6.26, created on 2011-12-13 04:25:54
         compiled from /home/vbezruchkin/www/v1700/admin/templates/default/login.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'include_file', '/home/vbezruchkin/www/v1700/admin/templates/default/login.tpl', 9, false),array('function', 'preventCsrf', '/home/vbezruchkin/www/v1700/admin/templates/default/login.tpl', 38, false),array('modifier', 'escape', '/home/vbezruchkin/www/v1700/admin/templates/default/login.tpl', 43, false),array('modifier', 'date_format', '/home/vbezruchkin/www/v1700/admin/templates/default/login.tpl', 78, false),)), $this); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title><?php echo $this->_tpl_vars['esynI18N']['login_to']; ?>
</title>
	<meta http-equiv="Content-Type" content="text/html;charset=<?php echo $this->_tpl_vars['config']['charset']; ?>
" />
	<base href="<?php echo @ESYN_URL; ?>
<?php echo @ESYN_ADMIN_FOLDER; ?>
/" />
	
	<?php echo smarty_function_include_file(array('js' => "js/ext/ext-base, js/ext/ext-all"), $this);?>

	<?php echo smarty_function_include_file(array('js' => "js/jquery/jquery, js/jquery/plugins/jquery.corner, js/utils/md5"), $this);?>

	<?php echo smarty_function_include_file(array('js' => "js/intelli/intelli, js/intelli/intelli.admin"), $this);?>


	<?php echo smarty_function_include_file(array('js' => "tmp/cache/intelli.config"), $this);?>

	<?php echo smarty_function_include_file(array('js' => "tmp/cache/intelli.admin.lang.en"), $this);?>


	<?php echo smarty_function_include_file(array('css' => "js/ext/resources/css/ext-all"), $this);?>

	<link rel="stylesheet" type="text/css" href="<?php echo @ESYN_URL; ?>
<?php echo @ESYN_ADMIN_FOLDER; ?>
/templates/<?php echo $this->_tpl_vars['config']['admin_tmpl']; ?>
/css/login.css" />
</head>

<body>

	<!-- login start -->
	<div class="login">

		<!-- logo start -->
		<div class="logo"><a href="http://www.esyndicat.com/"><img src="templates/<?php echo $this->_tpl_vars['config']['admin_tmpl']; ?>
/img/logos/logo.png" alt="eSyndiCat" /></a></div>
		<!-- logo end -->

		<!-- text start -->
		<div class="text">
			<?php echo $this->_tpl_vars['esynI18N']['login_to_text']; ?>

		</div>
		<!-- text end -->

		<!-- login form start -->
		<div class="form">
			<form action="login.php" method="post" name="login_form" id="login_form" onsubmit="formSubmit();">
			<?php echo esynUtil::preventCsrf(array(), $this);?>

			<ul>
				<li><label for="username"><strong><?php echo $this->_tpl_vars['esynI18N']['login']; ?>
</strong></label></li>

				<li style="width:200px;">
					<input type="text" id="username" name="username" tabindex="1" value="<?php if (isset ( $_POST['username'] )): ?><?php echo ((is_array($_tmp=$_POST['username'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
<?php endif; ?>" />
				</li>

				<li style="clear:both;"><label for="dummy_password"><strong><?php echo $this->_tpl_vars['esynI18N']['password']; ?>
</strong></label></li>

				<li>
					<input type="password" id="dummy_password" name="dummy_password" value="" tabindex="2" />
					<input type="hidden" id="password" name="password" size="25" tabindex="2" />
				</li>

				<li style="clear:both;padding-left:80px;">
					<input type="submit" id="login" value="<?php echo $this->_tpl_vars['esynI18N']['login']; ?>
" tabindex="3" />
				</li>
			</ul>
			<input type="hidden" name="action" id="action" value="" />
			<input type="hidden" name="md5Salt" id="md5Salt" value="<?php echo $_SESSION['md5Salt']; ?>
" />
			</form>

			<div style="clear:both;"></div>

			<?php if ($this->_tpl_vars['error']): ?>
				<div class="tip">
					<div class="inner">
						<div class="tip-arrow"></div>
						<?php echo $this->_tpl_vars['esynI18N']['error_login']; ?>

					</div>
				</div>
			<?php endif; ?>

		</div>
		<!-- login form end -->

		<!-- copyrights start -->
		<div class="copy">
			Powered by <a href="http://www.esyndicat.com/">eSyndiCat Free v<?php echo $this->_tpl_vars['config']['version']; ?>
</a><br />
			Copyright &copy; 2005-<?php echo ((is_array($_tmp=time())) ? $this->_run_mod_handler('date_format', true, $_tmp, "%Y") : smarty_modifier_date_format($_tmp, "%Y")); ?>
 <a href="http://www.intelliants.com/">Intelliants LLC</a>
		</div>
		<!-- copyrights end -->

		<div class="forgot">
			<br /><a href="#" id="forgot_password"><?php echo $this->_tpl_vars['esynI18N']['forgot_password']; ?>
</a>
		</div>

	</div>
	<!-- login end -->

<?php echo smarty_function_include_file(array('js' => "js/admin/login"), $this);?>


</body>
</html>