{include file="header.tpl"}

<h1>{$title}</h1>

{esynHooker name="tplFrontAccountsAfterHeader"}

{if $search_alphas}
	<div class="alpha-navigation">
		{foreach from=$search_alphas item=onealpha}
			{if $onealpha eq $alpha}
				<span class="active">{$onealpha}</span>
			{else}
				<a href="{$smarty.const.ESYN_URL}accounts{if $config.mod_rewrite}/{$onealpha}/{else}.php?alpha={$onealpha}{/if}">{$onealpha}</a>
			{/if}
		{/foreach}
	</div>
{/if}

{esynHooker name="tplFrontAccountsAfterAlphas"}

{if isset($accounts) && !empty($accounts)}
	<table border="0" width="100%" cellpadding="0" cellspacing="0" class="common">
	<tr>
		<th>{$lang.username}</th>
		<th>{$lang.date_registration}</th>
		<th>&nbsp;</th>
	</tr>
	{foreach from=$accounts item=account}
	<tr>
		<td><em>{$account.username}</em></td>
		<td>{$account.date_reg|date_format:$config.date_format}</td>
		<td><a href="{print_account_url account=$account}">{print_img fl="info_16.png" full=true title=$lang.view_account_details alt=$lang.view_account_details}</a></td>
	</tr>
	{/foreach}
	</table>
{else}
	<p>{$lang.no_accounts}</p>
{/if}

{esynHooker name="tplFrontAccountsBeforeFooter"}

{include file="footer.tpl"}