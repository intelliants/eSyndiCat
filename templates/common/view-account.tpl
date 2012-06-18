{include file="header.tpl"}

<h1>{$title}</h1>

{esynHooker name="tplFrontviewAccountsAfterHeader"}

{if isset($listings) && !empty($listings)}
	<!-- listings box start -->
	<div class="listings">
		{if $config.mod_rewrite}{assign var="type" value=2}{else}{assign var="type" value=1}{/if}
		{navigation aTotal=$total_listings aTemplate=$url aItemsPerPage=$config.num_index_listings aNumPageItems=5 aTruncateParam=1}

		<table cellspacing="0" cellpadding="0" width="100%">
		{foreach from=$listings item=listing}
			{include file="listing-display.tpl"}
		{/foreach}
		</table>
	</div>
	<!-- listings box end -->

	{navigation aTotal=$total_listings aTemplate=$url aItemsPerPage=$config.num_index_listings aNumPageItems=5 aTruncateParam=1}

	{include_file js="js/frontend/listing-display"}
{else}
	<div class="box">
		{$lang.no_account_listings}
	</div>
{/if}

{esynHooker name="tplFrontviewAccountsBeforeFooter"}

{include file="footer.tpl"}
