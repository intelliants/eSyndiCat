{include file="header.tpl"}

<h1>{$lang.resend_email}</h1>

{include file="notification.tpl"}

<div class="box">
		<form action="{$smarty.const.ESYN_URL}resend-email.php" method="post">
			<p class="field"><strong>{$lang.username}:</strong><br />
			<input type="text" class="text" name="username" size="35" /></p>

			<input type="submit" name="resend" value="{$lang.submit}" class="button" />
		</form>
</div>

{esynHooker name="resendEmailBeforeFooter"}

{include file="footer.tpl"}
