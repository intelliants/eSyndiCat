{include file="header.tpl"}

<h1>{$lang.login}</h1>

{include file="notification.tpl"}

{esynHooker name="tplFrontLoginAfterHeader"}

<form action="{$smarty.const.ESYN_URL}login.php" method="post">
	<p class="field">
		<strong>{$lang.username}:</strong><br />
		<input type="text" class="text" tabindex="4" name="username" size="26" value="{if isset($smarty.post.username) && !empty($smarty.post.username)}{$smarty.post.username|escape:"html"}{/if}" />
	</p>

	<p class="field">
		<strong>{$lang.password}:</strong><br />
		<input type="password" class="text" tabindex="5" name="password" size="26" value="" />
	</p>

	<p class="field">
		<input type="checkbox" tabindex="3" name="rememberme" value="1" id="rememberme" {if isset($smarty.post.rememberme) && $smarty.post.rememberme eq '1'}checked="checked"{/if} />&nbsp;<label for="rememberme">{$lang.rememberme}</label>
	</p>

	<p class="field">
		<input type="submit" tabindex="6" name="login" value="{$lang.login}" class="button" />
		<a href="{$smarty.const.ESYN_URL}forgot.php">{$lang.forgot}</a>
	</p>
</form>

<p>{$lang.register_account} <a href="{$smarty.const.ESYN_URL}register.php" rel="nofollow">{$lang.register}</a></p>

{esynHooker name="loginBeforeFooter"}

{include file="footer.tpl"}