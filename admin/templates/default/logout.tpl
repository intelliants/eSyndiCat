<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>{$esynI18N.logout}</title>
	<meta http-equiv="Content-Type" content="text/html;charset={$config.charset}" />
	<link rel="stylesheet" type="text/css" href="{$smarty.const.ESYN_URL}admin/templates/{$config.admin_tmpl}/css/login.css" />
</head>

<body>

	<!-- logout start -->
	<div class="login">

		<!-- logo start -->
		<div class="logo"><a href="http://www.esyndicat.com/"><img src="templates/{$config.admin_tmpl}/img/logos/logo.png" alt="eSyndiCat" /></a></div>
		<!-- logo end -->

		<!-- text start -->
		<div class="logout">

			{$esynI18N.logout_content}

		</div>
		<!-- text end -->

		<!-- copyrights start -->
		<div class="copy">
			Powered by <a href="http://www.esyndicat.com/">eSyndiCat Free v{$config.version}</a><br />
			Copyright &copy; 2005-{$smarty.now|date_format:"%Y"} <a href="http://www.intelliants.com/">Intelliants LLC</a>
		</div>
		<!-- copyrights end -->

	</div>
	<!-- logout end -->

</body>
</html>