{include file="header.tpl"}

<h1>{$lang.restore_password}</h1>

{include file="notification.tpl"}

{if $form}
	<div class="box">
		<form action="{$smarty.const.ESYN_URL}forgot.php" method="post">
			<p class="field"><strong>{$lang.email}:</strong><br />
			<input type="text" class="text" name="email" value="{if isset($smarty.post.email)}{$smarty.post.email|escape:"html"}{/if}" size="35" /></p>
			<input type="submit" name="restore" value="{$lang.submit}" class="button" />
		</form>
	</div>
{/if}

{esynHooker name="forgotBeforeFooter"}

{include file="footer.tpl"}
