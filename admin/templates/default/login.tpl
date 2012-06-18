<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>{$esynI18N.login_to}</title>
	<meta http-equiv="Content-Type" content="text/html;charset={$config.charset}" />
	<base href="{$smarty.const.ESYN_URL}{$smarty.const.ESYN_ADMIN_FOLDER}/" />
	
	{include_file js="js/ext/ext-base, js/ext/ext-all"}
	{include_file js="js/jquery/jquery, js/jquery/plugins/jquery.corner, js/utils/md5"}
	{include_file js="js/intelli/intelli, js/intelli/intelli.admin"}

	{include_file js="tmp/cache/intelli.config"}
	{include_file js="tmp/cache/intelli.admin.lang.en"}

	{include_file css="js/ext/resources/css/ext-all"}
	<link rel="stylesheet" type="text/css" href="{$smarty.const.ESYN_URL}{$smarty.const.ESYN_ADMIN_FOLDER}/templates/{$config.admin_tmpl}/css/login.css" />
</head>

<body>

	<!-- login start -->
	<div class="login">

		<!-- logo start -->
		<div class="logo"><a href="http://www.esyndicat.com/"><img src="templates/{$config.admin_tmpl}/img/logos/logo.png" alt="eSyndiCat" /></a></div>
		<!-- logo end -->

		<!-- text start -->
		<div class="text">
			{$esynI18N.login_to_text}
		</div>
		<!-- text end -->

		<!-- login form start -->
		<div class="form">
			<form action="login.php" method="post" name="login_form" id="login_form" onsubmit="formSubmit();">
			{preventCsrf}
			<ul>
				<li><label for="username"><strong>{$esynI18N.login}</strong></label></li>

				<li style="width:200px;">
					<input type="text" id="username" name="username" tabindex="1" value="{if isset($smarty.post.username)}{$smarty.post.username|escape:"html"}{/if}" />
				</li>

				<li style="clear:both;"><label for="dummy_password"><strong>{$esynI18N.password}</strong></label></li>

				<li>
					<input type="password" id="dummy_password" name="dummy_password" value="" tabindex="2" />
					<input type="hidden" id="password" name="password" size="25" tabindex="2" />
				</li>

				<li style="clear:both;padding-left:80px;">
					<input type="submit" id="login" value="{$esynI18N.login}" tabindex="3" />
				</li>
			</ul>
			<input type="hidden" name="action" id="action" value="" />
			<input type="hidden" name="md5Salt" id="md5Salt" value="{$smarty.session.md5Salt}" />
			</form>

			<div style="clear:both;"></div>

			{if $error}
				<div class="tip">
					<div class="inner">
						<div class="tip-arrow"></div>
						{$esynI18N.error_login}
					</div>
				</div>
			{/if}

		</div>
		<!-- login form end -->

		<!-- copyrights start -->
		<div class="copy">
			Powered by <a href="http://www.esyndicat.com/">eSyndiCat Free v{$config.version}</a><br />
			Copyright &copy; 2005-{$smarty.now|date_format:"%Y"} <a href="http://www.intelliants.com/">Intelliants LLC</a>
		</div>
		<!-- copyrights end -->

		<div class="forgot">
			<br /><a href="#" id="forgot_password">{$esynI18N.forgot_password}</a>
		</div>

	</div>
	<!-- login end -->

{include_file js="js/admin/login"}

</body>
</html>
