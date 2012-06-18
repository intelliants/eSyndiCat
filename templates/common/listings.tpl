{include file="header.tpl"}
{assign var="type" value=$view|cat:"_listings"}

<h1>{$lang.$type}</h1>

{esynHooker name="tplFrontListingsAfterHeader"}

{if $listings}
	<div class="listings">
		{if isset($total_listings)}
			{navigation aTotal=$total_listings aTemplate=$url aItemsPerPage=$config.num_index_listings aNumPageItems=5 aTruncateParam=1}
		{/if}
			
		<table cellspacing="0" cellpadding="0" width="100%">
		{foreach from=$listings item=listing name=listings}
			{include file="listing-display.tpl"}
		{/foreach}
		</table>
			
		{if isset($total_listings)}
			{navigation aTotal=$total_listings aTemplate=$url aItemsPerPage=$config.num_index_listings aNumPageItems=5 aTruncateParam=1}
		{/if}
	</div>

	{if $esynAccountInfo}
		<hr /><div class="waiting">&nbsp;</div><div class="admin-approve"> - {$lang.listings_legend}</div>
	{/if}

	{include_file js="js/frontend/listing-display"}
{else}
	<p>{$lang.no_listings}</p>
{/if}

{esynHooker name="listingsBeforeFooter"}

{include file="footer.tpl"}
