{include file="header.tpl"}

<h1>{$lang.register}</h1>

{include file="notification.tpl"}

{esynHooker name="tplFrontRegisterBeforeRegister"}

<form method="post" action="{$smarty.const.ESYN_URL}register.php">
	<p class="field">
		<strong>{$lang.your_username}:</strong><br />
		<input type="text" class="text" name="username" size="25" id="username" value="{if isset($account.username)}{$account.username|escape:"html"}{elseif isset($smarty.post.username)}{$smarty.post.username|escape:"html"}{/if}" />
	</p>
	
	<p class="field">
		<strong>{$lang.your_email}:</strong><br />
		<input type="text" class="text" name="email" size="25" id="email" value="{if isset($account.email)}{$account.email|escape:"html"}{elseif isset($smarty.post.email)}{$smarty.post.email|escape:"html"}{/if}" />
	</p>

	<p class="field">
		<input type="checkbox" id="auto_generate" name="auto_generate" value="1" {if isset($smarty.post.auto_generate) && $smarty.post.auto_generate eq '1'}checked="checked"{elseif !isset($account) && !$smarty.post}checked="checked"{/if} /><label for="auto_generate">{$lang.auto_generate_password}</label>
	</p>

	<div id="passwords" style="display: none;">
		<p class="field">
			<strong>{$lang.your_password}:</strong><br />
			<input type="password" name="password" class="text" size="25" id="pass1" value="{if isset($account.password)}{$account.password|escape:"html"}{elseif isset($smarty.post.password)}{$smarty.post.password|escape:"html"}{/if}" />
		</p>
		<p class="field">
			<strong>{$lang.your_password_confirm}:</strong><br />
			<input type="password" name="password2" class="text" size="25" id="pass2" value="{if isset($account.password2)}{$account.password2|escape:"html"}{elseif isset($smarty.post.password2)}{$smarty.post.password2|escape:"html"}{/if}" />
		</p>
	</div>

	{include file="captcha.tpl"}
	
	<p class="field"><input type="submit" name="register" value="{$lang.register}" class="button" /></p>
</form>

{esynHooker name="registerBeforeIncludeJs"}

{include_file js="js/frontend/register"}

{esynHooker name="registerBeforeFooter"}

{include file="footer.tpl"}