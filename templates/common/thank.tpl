{include file="header.tpl"}

<h1>{$lang.thanks}</h1>

{$lang.thankyou_head}
<h2 style="padding-top: 10px;">{$email}</h2>
{$lang.thankyou_tail}

{if $config.accounts_autoapproval}
	<div align="center" style="padding-top:20px;">
		<input type="button" value=" {$lang.next} " onclick="javascript:document.location.href='login.php';" class="button"/>
	</div>
{/if}

{esynHooker name="thankBeforeFooter"}

{include file="footer.tpl"}
