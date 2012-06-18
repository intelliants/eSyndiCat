{include file="header.tpl"}

<h1>{$lang.edit_account}</h1>

{include file="notification.tpl"}

{esynHooker name="tplFrontEditAccountBeforeEdit"}

{include file="box-header.tpl" caption=$lang.edit_account style="fixed"}
<form action="{$smarty.const.ESYN_URL}edit-account.php" method="post">
	<p class="field"><strong>{$lang.email}:</strong><br />
		<input type="text" class="text" name="email" id="email" size="30" value="{$smarty.post.email|default:$esynAccountInfo.email}" />
	</p>

	<p class="field">
		<input type="hidden" name="old_email" value="{$esynAccountInfo.email}" />
		<input type="submit" name="change_email" value="{$lang.change_email}" class="button" />
	</p>
</form>
{include file="box-footer.tpl"}

{esynHooker name="tplFrontEditAccountBeforePasswordChange"}

{include file="box-header.tpl" caption=$lang.change_password style="fixed"}
<form action="{$smarty.const.ESYN_URL}edit-account.php" method="post">

	<p class="field"><strong>{$lang.current_password}:</strong><br />
		<input type="password" class="text" name="current" id="title" size="30" />
	</p>

	<p class="field"><strong>{$lang.new_password}:</strong><br />
		<input type="password" class="text" name="new" id="title" size="30" />
	</p>

	<p class="field"><strong>{$lang.new_password2}:</strong><br />
		<input type="password" class="text" name="confirm" id="title" size="30" />
	</p>

	<p class="field">
		<input type="submit" name="change_pass" value="{$lang.change_password}" class="button" />
	</p>
	
</form>
{include file="box-footer.tpl"}

{esynHooker name="tplFrontEditAccountBeforeFooter"}

{include file="footer.tpl"}
